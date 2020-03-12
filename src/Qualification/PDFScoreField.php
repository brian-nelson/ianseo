<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ScorePDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
checkACL(AclQualification, AclReadOnly);

$pdf = new ScorePDF(true);
$pdf->BottomImage=empty($_REQUEST['QRCode']);

$defScoreX = $pdf->getSideMargin();
$defScoreY = $pdf->getSideMargin();
$defScoreY2 = ($pdf->GetPageHeight()+$pdf->getSideMargin())/2;

$defScoreW = ($pdf->GetPageWidth()-$pdf->getSideMargin()*2);
$defScoreH = ($pdf->GetPageHeight()-$pdf->getSideMargin()*3)/2;


$NumEnd = 12;
//$MyQuery = "SELECT TtNumEnds FROM Tournament INNER JOIN Tournament*Type AS tt ON ToType=TtId WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
$MyQuery = "SELECT ToNumEnds AS TtNumEnds FROM Tournament  WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
$Rs=safe_r_sql($MyQuery);
if(safe_num_rows($Rs)==1) {
	$r=safe_fetch($Rs);
	$NumEnd=$r->TtNumEnds;
}

$FillWithArrows = (isset($_REQUEST["ScoreFilled"]) && $_REQUEST["ScoreFilled"]==1 && !empty($_REQUEST["ScoreDist"]));
$pdf->FillWithArrows=$FillWithArrows;
$reqDist = (empty($_REQUEST["ScoreDist"]) ? 1 : $_REQUEST["ScoreDist"]);


if(!empty($_REQUEST["ScoreBarcode"])) $pdf->PrintBarcode=true;

if(!(isset($_REQUEST["ScoreHeader"]) && $_REQUEST["ScoreHeader"]==1))
	$pdf->HideHeader();

if(!(isset($_REQUEST["ScoreLogos"]) && $_REQUEST["ScoreLogos"]==1))
	$pdf->HideLogo();

if(!(isset($_REQUEST["ScoreFlags"]) && $_REQUEST["ScoreFlags"]==1))
	$pdf->HideFlags();

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


if(isset($_REQUEST["ScoreDraw"]) && $_REQUEST['ScoreDraw']=="Draw") {
	$session = (isset($_REQUEST['x_Session']) AND $_REQUEST['x_Session']>0) ? $_REQUEST['x_Session'] : 1;
    $pdf->NoLineNumbers();
    $pdf->AddPage();
	$pdf->DrawScoreField($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd/2,3,array('Dist'=>'', 'CurDist'=>$reqDist, 'Session'=>$session));
	$pdf->DrawScoreField($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $NumEnd/2,3,array('Dist'=>'', 'CurDist'=>$reqDist, 'Session'=>$session));
	if(!empty($_REQUEST['QRCode'])) {
		foreach($_REQUEST['QRCode'] as $k => $Api) {
			require_once('Api/'.$Api.'/DrawQRCode.php');
			$Function='DrawQRCode_'.preg_replace('/[^a-z0-9]/sim', '_', $Api);
			$Function($pdf, $QRCodeX + 30*$k, $QRCodeY);
		}
	}
} else {
	$MyQuery = 'SELECT CoCode, CoName, EnCode, EnDivision, EnClass, SUBSTRING(at.AtTargetNo,2) as tNo, CONCAT(EnFirstName,\' \', EnName) AS Ath, CONCAT(CoCode, \' - \', CoName) as Noc, CONCAT(EnDivision, \' \', EnClass) AS Cat, TfName, '
		. " TdX, ArrowstringX, ScoreX, GoldX, XNineX, printDXgx "
		. ' FROM AvailableTarget as at ';
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
		. " (SELECT QuTargetNo, EnCode, EnName, EnFirstName, CoCode, CoName, EnClass, EnDivision, TfName, "
		. " Td{$reqDist} as TdX, QuD{$reqDist}Arrowstring as ArrowstringX, QuD{$reqDist}Score as ScoreX, QuD{$reqDist}Gold as GoldX, QuD{$reqDist}XNine as XNineX, (QuD{$reqDist}Gold+QuD{$reqDist}XNine)  as printDXgx "
		. " FROM Qualifications AS q  "
		. " INNER JOIN Entries AS e ON q.QuId=e.EnId AND e.EnTournament= " . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1 "
		. " INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament "
		. ' INNER JOIN Tournament AS t ON e.EnTournament=t.ToId '
		. ' LEFT JOIN TournamentDistances ON ToType=TdType and TdTournament=ToId AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE TdClasses '
		. ' LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId) as Sq ON at.AtTargetNo=Sq.QuTargetNo '
		. " WHERE AtTournament =  " . StrSafe_DB($_SESSION['TourId']) . ' '
		. " AND at.AtTargetNo>='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_From'],TargetNoPadding,"0",STR_PAD_LEFT) . "A' AND at.AtTargetNo<='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_To'],TargetNoPadding,"0",STR_PAD_LEFT) . "Z' "
		. ' ORDER BY at.AtTargetNo ASC, EnFirstName, EnName, CoCode';
	//echo $MyQuery;
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
                "CurDist"=>$reqDist,
				"Ath"=>$MyRow->Ath,
				"Noc"=>$MyRow->Noc,
				"CoCode"=>$MyRow->CoCode,
				"CoName"=>$MyRow->CoName,
				"D"=>$MyRow->TdX,
				"gxD"=>$MyRow->printDXgx,
				"Arr"=>(($FillWithArrows && $_REQUEST['ScoreDraw']!="TargetNo") ? $MyRow->ArrowstringX : ''),
				"QuD"=>$MyRow->ScoreX,
				"QuGD"=>$MyRow->GoldX,
				"QuXD"=>$MyRow->XNineX,
                "Session"=>$_REQUEST['x_Session']
			);
		} else {
			$Value = array(
				"EnCode"=>$MyRow->EnCode,
				"Div"=>$MyRow->EnDivision,
				"Cls"=>$MyRow->EnClass,
				"tNo"=>$MyRow->tNo,
				"startTarget"=>(substr($MyRow->tNo,0,-1)*1),
                "Cat"=>$MyRow->Cat,
                "Dist"=>$MyRow->TfName,
                "CurDist"=>$reqDist,
				"Ath"=>'',
				"Noc"=>'',
				"CoCode"=>'',
				"CoName"=>'',
				"D"=>'',
				"gxD"=>'',
				"Arr"=>'',
				"QuD"=>'',
				"QuGD"=>'',
				"QuXD"=>'',
                "Session"=>$_REQUEST['x_Session']
			);
		}

		$Yscore = $defScoreY2;
		switch(substr($Value["tNo"],-1,1)) {
			case 'A':
			case 'C':
			case 'E':
				$pdf->AddPage();
				$Yscore = $defScoreY;
		}

		$pdf->DrawScoreField($defScoreX, $Yscore, $defScoreW, $defScoreH,$NumEnd/2,3,$Value);
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