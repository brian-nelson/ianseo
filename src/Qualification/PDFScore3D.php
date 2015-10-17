<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ScorePDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');

$pdf = new ScorePDF(true);

$defScoreX = $pdf->getSideMargin();
$defScoreY = $pdf->getSideMargin();
$defScoreY2 = ($pdf->GetPageHeight()+$pdf->getSideMargin())/2;

$defScoreW = $pdf->GetPageWidth()-$pdf->getSideMargin()*2;
$defScoreH = ($pdf->GetPageHeight()-$pdf->getSideMargin()*3)/2;

$NumEnd = 10;
$subRule = '';
//$MyQuery = "SELECT TtNumEnds FROM Tournament INNER JOIN Tournament*Type AS tt ON ToType=TtId WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
$MyQuery = "SELECT ToNumEnds AS TtNumEnds, ToTypeSubRule as subRule FROM Tournament  WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
$Rs=safe_r_sql($MyQuery);
if(safe_num_rows($Rs)==1) {
	$r=safe_fetch($Rs);
	$NumEnd = $r->TtNumEnds;
	$subRule = ($r->subRule == 'Set1Dist1Arrow');
}

if(!empty($_REQUEST["ScoreBarcode"])) $pdf->PrintBarcode=true;

// gets the default target face for this tournament
$Target=getTarget($_SESSION['TourId']);

if(!(isset($_REQUEST["ScoreHeader"]) && $_REQUEST["ScoreHeader"]==1))
	$pdf->HideHeader();

if(!(isset($_REQUEST["ScoreLogos"]) && $_REQUEST["ScoreLogos"]==1))
	$pdf->HideLogo();

if(isset($_REQUEST["ScoreDraw"]) && $_REQUEST["ScoreDraw"]=="Data")
	$pdf->NoDrawing();

if(!empty($_REQUEST['QRCode'])) {
	$QRCodeX=0;
	$QRCodeY=0;
	$defScoreH-=11;
	$defScoreY2+=11;
	$quanti=count($_REQUEST['QRCode']);
	$QRCodeX=($pdf->GetPageWidth() + 5 - (30*$quanti))/2;
}

if(isset($_REQUEST["ScoreDraw"]) && $_REQUEST['ScoreDraw']=="Draw")
{
	$pdf->AddPage();
	if($subRule)
	{
		$pdf->DrawScore3D($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd/2, array(), false, $Target);
		$pdf->DrawScore3D($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $NumEnd/2, array(), false, $Target);
	}
	else
	{
		$pdf->DrawScoreField($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd/2, 2, array(), false);
		$pdf->DrawScoreField($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $NumEnd/2, 2, array(), false);
	}
	if(!empty($_REQUEST['QRCode'])) {
		foreach($_REQUEST['QRCode'] as $k => $Api) {
			require_once('Api/'.$Api.'/DrawQRCode.php');
			$Function='DrawQRCode_'.preg_replace('/[^a-z0-9]/sim', '_', $Api);
			$Function($pdf, $QRCodeX + 30*$k, $QRCodeY);
		}
	}
}
else
{
	$MyQuery = 'SELECT EnCode, EnDivision, EnClass, SUBSTRING(at.AtTargetNo,2) as tNo, CONCAT(EnFirstName,\' \', EnName) AS Ath, CONCAT(CoCode, \' - \', CoName) as Noc, CONCAT(EnDivision, \' \', EnClass) AS Cat, TfName '
		. ', CoCode, CoName FROM AvailableTarget as at ';
		if((isset($_REQUEST["noEmpty"]) && $_REQUEST["noEmpty"]==1))
		{
			$MyQuery .= "INNER JOIN
				(SELECT DISTINCT EnTournament, SUBSTRING(QuTargetNo,1,4) as TgtNo
				FROM Qualifications
				INNER JOIN Entries On QuId=EnId
				WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1 AND QuTargetNo>='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_From'],TargetNoPadding,"0",STR_PAD_LEFT) . "A' AND QuTargetNo<='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_To'],TargetNoPadding,"0",STR_PAD_LEFT) . "Z'
				) as Tgt ON at.AtTournament=Tgt.EnTournament AND SUBSTRING(at.AtTargetNo,1,4)=Tgt.TgtNo	";
		}
		$MyQuery .= " LEFT JOIN "
		. " (SELECT QuTargetNo, EnCode, EnName, EnFirstName, CoCode, CoName, EnClass, EnDivision, TfName "
		. " FROM Qualifications AS q  "
		. " INNER JOIN Entries AS e ON q.QuId=e.EnId AND e.EnTournament= " . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1 "
		. " INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament "
		. ' INNER JOIN Tournament AS t ON e.EnTournament=t.ToId '
		. ' LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId) as Sq ON at.AtTargetNo=Sq.QuTargetNo'
		. " WHERE AtTournament =  " . StrSafe_DB($_SESSION['TourId']) . ' '
		. " AND at.AtTargetNo>='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_From'],TargetNoPadding,"0",STR_PAD_LEFT) . "A' AND at.AtTargetNo<='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_To'],TargetNoPadding,"0",STR_PAD_LEFT) . "Z' "
		. ' ORDER BY at.AtTargetNo ASC, EnFirstName, EnName, CoCode';

	$Rs=safe_r_sql($MyQuery);
	while($MyRow=safe_fetch($Rs)) {
		if($_REQUEST['ScoreDraw']!="TargetNo") {
			$Value = array(
				"EnCode"=>$MyRow->EnCode,
				"Div"=>$MyRow->EnDivision,
				"Cls"=>$MyRow->EnClass,
				"tNo"=>$MyRow->tNo,
				"startTarget"=>(substr($MyRow->tNo,0,-1)*1),
				"Cat"=>$MyRow->Cat,
				"Dist"=>$MyRow->TfName,
				"Ath"=>$MyRow->Ath,
				"Noc"=>$MyRow->Noc,
				"CoCode"=>$MyRow->CoCode,
				"CoName"=>$MyRow->CoName,
					);
		} else {
			$Value = array(
				"EnCode"=>$MyRow->EnCode,
				"Div"=>$MyRow->EnDivision,
				"Cls"=>$MyRow->EnClass,
				"tNo"=>$MyRow->tNo,
				"startTarget"=>(substr($MyRow->tNo,0,-1)*1),
				"Cat"=>'',
				"Dist"=>'',
				"Ath"=>'',
				"Noc"=>'',
				"CoCode"=>'',
				"CoName"=>'',);
		}

		$Yscore = $defScoreY2;
		switch(substr($Value["tNo"],-1,1)) {
			case 'A':
			case 'C':
			case 'E':
				$pdf->AddPage();
				$Yscore = $defScoreY;
		}
		if($subRule) {
			$pdf->DrawScore3D($defScoreX, $Yscore, $defScoreW, $defScoreH,$NumEnd/2,$Value,false, $Target);
		} else {
			$pdf->DrawScoreField($defScoreX, $Yscore, $defScoreW, $defScoreH, $NumEnd/2, 2, $Value, false);
		}
		if($Yscore == $defScoreY2 and !empty($_REQUEST['QRCode'])) {
			foreach($_REQUEST['QRCode'] as $k => $Api) {
				require_once('Api/'.$Api.'/DrawQRCode.php');
				$Function='DrawQRCode_'.preg_replace('/[^a-z0-9]/sim', '_', $Api);
				$Function($pdf, $QRCodeX + 30*$k, $QRCodeY, $_REQUEST['x_Session'], 0, substr($MyRow->tNo,0,-1));
			}
		}
	}
}
$pdf->Output();
?>