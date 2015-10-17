<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ScorePDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Sessions.inc.php');

if(isset($_REQUEST["PersonalScore"]) && $_REQUEST["PersonalScore"]==1)
{
	$Tmp="";
	foreach($_REQUEST as $key=>$value)
	{
		$Tmp .= '&' . $key . '=' . $value;
	}
	header('location: PDFScorePersonal.php' . '?' . substr($Tmp,1));
	exit();
}

$pdf = new ScorePDF(true);

$NumEnd = 12;
$session=0;

//$MyQuery = "SELECT TtNumEnds FROM Tournament INNER JOIN Tournament*Type AS tt ON ToType=TtId WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);

$MyQuery = "SELECT ToNumEnds AS TtNumEnds FROM Tournament  WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
$Rs=safe_r_sql($MyQuery);
if($r=safe_fetch($Rs)) {
	$NumEnd=$r->TtNumEnds;
}

$FillWithArrows = ((isset($_REQUEST["ScoreFilled"]) && $_REQUEST["ScoreFilled"]==1) or !empty($_REQUEST["GetScorecardAsString"]));
$pdf->FillWithArrows=$FillWithArrows;

if(empty($_REQUEST["GetScorecardAsString"])) {
	if(empty($_REQUEST["ScoreHeader"])) $pdf->HideHeader();
	if(empty($_REQUEST["ScoreLogos"])) $pdf->HideLogo();
	if(empty($_REQUEST["ScoreFlags"])) $pdf->HideFlags();
	if(!empty($_REQUEST["ScoreBarcode"])) $pdf->PrintBarcode=true;
	if(isset($_REQUEST["ScoreDraw"]) && $_REQUEST["ScoreDraw"]=="Data") $pdf->NoDrawing();
	if(isset($_REQUEST["ScoreDraw"]) && $_REQUEST["ScoreDraw"]=="CompleteTotals") $pdf->PrintTotalColumns();

	$Ath4Target = 4;
	$session=intval($_REQUEST['x_Session']);
}

if($session>0) {
	$ses=GetSessions(null,false,array($session.'_Q'));
	$Ath4Target = $ses[0]->SesAth4Target;
}

$defScoreX = $pdf->getSideMargin();
$defScoreX2 = ($pdf->GetPageWidth()+$pdf->getSideMargin())/2;
$defScoreY = $pdf->getSideMargin();
$defScoreY2 = ($pdf->GetPageHeight()+$pdf->getSideMargin())/2;

$defScoreW = ($pdf->GetPageWidth()-$pdf->getSideMargin()*3)/2;
$defScoreH = ($pdf->GetPageHeight()-$pdf->getSideMargin()*3)/2;

if($Ath4Target==2) {
	$defScoreX = $pdf->getSideMargin()*3;
	$defScoreH = ($pdf->GetPageWidth()-$pdf->getSideMargin()*2);
	$defScoreW = ($pdf->GetPageHeight()-$defScoreX*3)/2;
} elseif($Ath4Target==3) {
	$defScoreH = ($pdf->GetPageWidth()-$pdf->getSideMargin()*2);
	$defScoreW = ($pdf->GetPageHeight()-$pdf->getSideMargin()*4)/3;
}

if(!empty($_REQUEST['QRCode'])) {
	$QRCodeX=0;
	$QRCodeY=0;
	switch($Ath4Target) {
		case 2:
			$defScoreH-=25;
			$quanti=count($_REQUEST['QRCode']);
			$QRCodeY=$pdf->GetPageWidth() - $pdf->getSideMargin() - 25;
			$QRCodeX=($pdf->GetPageHeight() + 5 - (25*$quanti))/2;
			break;
		case 3:
			$defScoreH-=25;
			$quanti=count($_REQUEST['QRCode']);
			$QRCodeY=$pdf->GetPageWidth() - $pdf->getSideMargin() - 25;
			$QRCodeX=($pdf->GetPageHeight() + 5 - (25*$quanti))/2;
			break;
		case 4:
			$defScoreH-=6;
			$defScoreY2+=6;
			if(count($_REQUEST['QRCode'])>1) {
				$quanti=count($_REQUEST['QRCode']);
				$QRCodeX=($pdf->GetPageWidth() + 5 - (25*$quanti))/2;
			}
			break;
	}
}

if(isset($_REQUEST["ScoreDraw"]) && $_REQUEST['ScoreDraw']=="Draw") {
	switch($Ath4Target) {
		case 2:
			$pdf->AddPage('L');
			$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd, 3,  array("Session"=>$_REQUEST['x_Session']));
			$pdf->DrawScore(2*$defScoreX+$defScoreW, $defScoreY, $defScoreW, $defScoreH, $NumEnd, 3,  array("Session"=>$_REQUEST['x_Session']));
			break;
		case 3:
			$pdf->AddPage('L');
			$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, ($pdf->GetPageHeight()-$pdf->getSideMargin()*2), $NumEnd, 3,  array("Session"=>$_REQUEST['x_Session']));
			$pdf->DrawScore(2*$defScoreX+$defScoreW, $defScoreY, $defScoreW, ($pdf->GetPageHeight()-$pdf->getSideMargin()*2), $NumEnd, 3,  array("Session"=>$_REQUEST['x_Session']));
			$pdf->DrawScore(3*$defScoreX+2*$defScoreW, $defScoreY, $defScoreW, ($pdf->GetPageHeight()-$pdf->getSideMargin()*2), $NumEnd, 3,  array("Session"=>$_REQUEST['x_Session']));
			break;
		default:
			$pdf->AddPage();
			$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd, 3, array("Session"=>$_REQUEST['x_Session']));
			$pdf->DrawScore($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $NumEnd, 3, array("Session"=>$_REQUEST['x_Session']));
			$pdf->DrawScore($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $NumEnd, 3, array("Session"=>$_REQUEST['x_Session']));
			$pdf->DrawScore($defScoreX2, $defScoreY2, $defScoreW, $defScoreH, $NumEnd, 3, array("Session"=>$_REQUEST['x_Session']));

	}
	if(!empty($_REQUEST['QRCode'])) {
		foreach($_REQUEST['QRCode'] as $k => $Api) {
			require_once('Api/'.$Api.'/DrawQRCode.php');
			$Function='DrawQRCode_'.preg_replace('/[^a-z0-9]/sim', '_', $Api);
			$Function($pdf, $QRCodeX + 30*$k, $QRCodeY);
		}
	}
} else {

	$MyQuery = 'SELECT SUBSTRING(at.AtTargetNo,2) as tNo, EnCode, CoCode, CoName, Ath, Noc, EnDivision, EnClass, Td1, Td2, Td3, Td4, Td5, Td6, Td7, Td8, '
		. 'QuD1Arrowstring, QuD2Arrowstring, QuD3Arrowstring, QuD4Arrowstring, QuD5Arrowstring, QuD6Arrowstring, QuD7Arrowstring, QuD8Arrowstring, '
		. 'length(concat(trim(QuD1Arrowstring), trim(QuD2Arrowstring), trim(QuD3Arrowstring), trim(QuD4Arrowstring), trim(QuD5Arrowstring), trim(QuD6Arrowstring), trim(QuD7Arrowstring), trim(QuD8Arrowstring))) as Arrows, '
		. 'QuD1Score, QuD2Score, QuD3Score, QuD4Score, QuD5Score, QuD6Score, QuD7Score, QuD8Score, '
		. ' QuD1Gold, QuD1XNine, QuD2Gold, QuD2XNine, QuD3Gold, QuD3XNine, QuD4Gold, QuD4XNine, '
		. ' QuD5Gold, QuD5XNine, QuD6Gold, QuD6XNine, QuD7Gold, QuD7XNine, QuD8Gold, QuD8XNine, '
		. 'printD1gx, printD2gx, printD3gx, printD4gx, printD5gx, printD6gx, printD7gx, printD8gx '
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
		. ' (SELECT EnCode, CoCode, CoName, QuTargetNo, CONCAT(EnFirstName,\' \', EnName) AS Ath, CONCAT(CoCode, \' - \', CoName) as Noc, EnDivision, EnClass, '
		. ' Td1, Td2, Td3, Td4, Td5, Td6, Td7, Td8, '
		. ' QuD1Arrowstring, QuD2Arrowstring, QuD3Arrowstring, QuD4Arrowstring, QuD5Arrowstring, QuD6Arrowstring, QuD7Arrowstring, QuD8Arrowstring, '
		. ' QuD1Score, QuD2Score, QuD3Score, QuD4Score, QuD5Score, QuD6Score, QuD7Score, QuD8Score, '
		. ' QuD1Gold, QuD1XNine, QuD2Gold, QuD2XNine, QuD3Gold, QuD3XNine, QuD4Gold, QuD4XNine, '
		. ' QuD5Gold, QuD5XNine, QuD6Gold, QuD6XNine, QuD7Gold, QuD7XNine, QuD8Gold, QuD8XNine, '
		. ' QuD1Gold+QuD1XNine as printD1gx, QuD2Gold+QuD2XNine as printD2gx, QuD3Gold+QuD3XNine as printD3gx, QuD4Gold+QuD4XNine as printD4gx, '
		. ' QuD5Gold+QuD5XNine as printD5gx, QuD6Gold+QuD6XNine as printD6gx, QuD7Gold+QuD7XNine as printD7gx ,QuD8Gold+QuD8XNine as printD8gx '
		. ' FROM Entries '
		. ' INNER JOIN Qualifications ON EnId = QuId '
		. ' INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament '
		. ' INNER JOIN Tournament ON EnTournament=ToId '
		. ' LEFT JOIN TournamentDistances ON ToType=TdType and TdTournament=ToId AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE TdClasses '
		. ' WHERE EnTournament = ' . StrSafe_DB($_SESSION['TourId']) . " AND QuTargetNo>='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_From'],TargetNoPadding,"0",STR_PAD_LEFT) . "A' AND QuTargetNo<='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_To'],TargetNoPadding,"0",STR_PAD_LEFT) . "Z' "
		. ') as Sqy ON at.AtTargetNo = Sqy.QuTargetNo '
		. " WHERE at.AtTournament =  " . StrSafe_DB($_SESSION['TourId']) . ' '
		. " AND at.AtTargetNo>='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_From'],TargetNoPadding,"0",STR_PAD_LEFT) . "A' AND at.AtTargetNo<='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_To'],TargetNoPadding,"0",STR_PAD_LEFT) . "Z' "
		. ' ORDER BY at.AtTargetNo ASC, Ath, Noc ';
// 		debug_svela($MyQuery);
	$Rs=safe_r_sql($MyQuery);
	if(safe_num_rows($Rs)>0)
	{
		$TmpTarget='-----';
		$Tmp=array();
		$DistArray=array();

		if(is_array($_REQUEST["ScoreDist"]))
		{
			foreach($_REQUEST["ScoreDist"] as $Value)
			{
				if(is_numeric($Value))
					$DistArray[]=$Value;
			}
		}
		else
			$DistArray[]=0;

		while($MyRow=safe_fetch($Rs))
		{
			if(!empty($_REQUEST["GetScorecardAsString"]) and !$MyRow->Arrows) continue;
			if($TmpTarget != substr($MyRow->tNo,0,-1) && count($Tmp)>0)
			{
				foreach($DistArray as $CurDist)
				{
					if($CurDist and $Tmp[0]["D" . $CurDist]=='-') continue;
					$pdf->AddPage($Ath4Target<=3 ? 'L' : 'P' );
					foreach($Tmp as $Value)
					{
						$Value['FirstDist']=($CurDist==1);
						if($CurDist==0) {
							unset($Value["Dist"]);
							$Value["gxD0"]='';
						} else {
							$Value["Dist"]=$Value["D" . $CurDist];
						}

						$Value["CurDist"]=$CurDist;
						$Value["Session"]=$_REQUEST['x_Session'];

						switch($Ath4Target) {
							case 2:
								if(substr($Value["tNo"],-1,1)=="A")
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								elseif(substr($Value["tNo"],-1,1)=="B")
									$pdf->DrawScore(2*$defScoreX+$defScoreW, $defScoreY, $defScoreW, $defScoreH,$NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								break;
							case 3:
								if(substr($Value["tNo"],-1,1)=="A")
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH,$NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								elseif(substr($Value["tNo"],-1,1)=="B")
									$pdf->DrawScore(2*$defScoreX+$defScoreW, $defScoreY, $defScoreW, $defScoreH,$NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								elseif(substr($Value["tNo"],-1,1)=="C")
									$pdf->DrawScore(3*$defScoreX+2*$defScoreW, $defScoreY, $defScoreW, $defScoreH,$NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								break;
							case 4:
								if(substr($Value["tNo"],-1,1)=="A")
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value, ($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								elseif(substr($Value["tNo"],-1,1)=="B")
									$pdf->DrawScore($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								elseif(substr($Value["tNo"],-1,1)=="C")
									$pdf->DrawScore($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								elseif(substr($Value["tNo"],-1,1)=="D")
									$pdf->DrawScore($defScoreX2, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								break;
							case 5:
								if(substr($Value["tNo"],-1,1)=="A") {
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value, ($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="B") {
									$pdf->DrawScore($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="C") {
									$pdf->DrawScore($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="D") {
									$pdf->AddPage('P');
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value, ($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="E") {
									$pdf->DrawScore($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								}
								break;
							case 6:
								if(substr($Value["tNo"],-1,1)=="A") {
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value, ($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="B") {
									$pdf->DrawScore($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="C") {
									$pdf->DrawScore($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="D") {
									$pdf->DrawScore($defScoreX2, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="E") {
									$pdf->AddPage('P');
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value, ($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="F") {
									$pdf->DrawScore($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								}
								break;
							case 7:
								if(substr($Value["tNo"],-1,1)=="A") {
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value, ($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="B") {
									$pdf->DrawScore($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="C") {
									$pdf->DrawScore($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="D") {
									$pdf->DrawScore($defScoreX2, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="E") {
									$pdf->AddPage('P');
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value, ($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="F") {
									$pdf->DrawScore($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="G") {
									$pdf->DrawScore($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								}
								break;
							case 8:
								if(substr($Value["tNo"],-1,1)=="A") {
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value, ($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="B") {
									$pdf->DrawScore($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="C") {
									$pdf->DrawScore($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="D") {
									$pdf->DrawScore($defScoreX2, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="E") {
									$pdf->AddPage('P');
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value, ($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="F") {
									$pdf->DrawScore($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="G") {
									$pdf->DrawScore($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="H") {
									$pdf->DrawScore($defScoreX2, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								}
								break;
						}
					}
					if(!empty($_REQUEST['QRCode'])) {
						foreach($_REQUEST['QRCode'] as $k => $Api) {
							require_once('Api/'.$Api.'/DrawQRCode.php');
							$Function='DrawQRCode_'.preg_replace('/[^a-z0-9]/sim', '_', $Api);
							$Function($pdf, $QRCodeX + 30*$k, $QRCodeY, $_REQUEST['x_Session'], $CurDist, $TmpTarget);
						}
					}
				}
				$Tmp=array();
			}
			$TmpTarget = substr($MyRow->tNo,0,-1);
			$Tmp[] = array(
				"tNo"=>$MyRow->tNo,
				"EnCode"=>$MyRow->EnCode,
				"Div"=>$MyRow->EnDivision,
				"Cls"=>$MyRow->EnClass,
				"Cat"=>($_REQUEST['ScoreDraw']!="TargetNo" ? $MyRow->EnDivision.' '.$MyRow->EnClass : ''),
				"Dist"=>'',
				"D1"=>$MyRow->Td1,
				"D2"=>$MyRow->Td2,
				"D3"=>$MyRow->Td3,
				"D4"=>$MyRow->Td4,
				"D5"=>$MyRow->Td5,
				"D6"=>$MyRow->Td6,
				"D7"=>$MyRow->Td7,
				"D8"=>$MyRow->Td8,
				"gxD1"=>$MyRow->printD1gx,
				"gxD2"=>$MyRow->printD2gx,
				"gxD3"=>$MyRow->printD3gx,
				"gxD4"=>$MyRow->printD4gx,
				"gxD5"=>$MyRow->printD5gx,
				"gxD6"=>$MyRow->printD6gx,
				"gxD7"=>$MyRow->printD7gx,
				"gxD8"=>$MyRow->printD8gx,
				"Arr1"=>(($FillWithArrows && $_REQUEST['ScoreDraw']!="TargetNo") ? $MyRow->QuD1Arrowstring : ''),
				"Arr2"=>(($FillWithArrows && $_REQUEST['ScoreDraw']!="TargetNo") ? $MyRow->QuD2Arrowstring : ''),
				"Arr3"=>(($FillWithArrows && $_REQUEST['ScoreDraw']!="TargetNo") ? $MyRow->QuD3Arrowstring : ''),
				"Arr4"=>(($FillWithArrows && $_REQUEST['ScoreDraw']!="TargetNo") ? $MyRow->QuD4Arrowstring : ''),
				"Arr5"=>(($FillWithArrows && $_REQUEST['ScoreDraw']!="TargetNo") ? $MyRow->QuD5Arrowstring : ''),
				"Arr6"=>(($FillWithArrows && $_REQUEST['ScoreDraw']!="TargetNo") ? $MyRow->QuD6Arrowstring : ''),
				"Arr7"=>(($FillWithArrows && $_REQUEST['ScoreDraw']!="TargetNo") ? $MyRow->QuD7Arrowstring : ''),
				"Arr8"=>(($FillWithArrows && $_REQUEST['ScoreDraw']!="TargetNo") ? $MyRow->QuD8Arrowstring : ''),
				"Tot1"=>(($FillWithArrows && $_REQUEST['ScoreDraw']!="TargetNo") ? 0 : ''),
				"Tot2"=>(($FillWithArrows && $_REQUEST['ScoreDraw']!="TargetNo") ? $MyRow->QuD1Score : ''),
				"Tot3"=>(($FillWithArrows && $_REQUEST['ScoreDraw']!="TargetNo") ? $MyRow->QuD1Score+$MyRow->QuD2Score : ''),
				"Tot4"=>(($FillWithArrows && $_REQUEST['ScoreDraw']!="TargetNo") ? $MyRow->QuD1Score+$MyRow->QuD2Score+$MyRow->QuD3Score : ''),
				"Tot5"=>(($FillWithArrows && $_REQUEST['ScoreDraw']!="TargetNo") ? $MyRow->QuD1Score+$MyRow->QuD2Score+$MyRow->QuD3Score+$MyRow->QuD4Score : ''),
				"Tot6"=>(($FillWithArrows && $_REQUEST['ScoreDraw']!="TargetNo") ? $MyRow->QuD1Score+$MyRow->QuD2Score+$MyRow->QuD3Score+$MyRow->QuD4Score+$MyRow->QuD5Score : ''),
				"Tot7"=>(($FillWithArrows && $_REQUEST['ScoreDraw']!="TargetNo") ? $MyRow->QuD1Score+$MyRow->QuD2Score+$MyRow->QuD3Score+$MyRow->QuD4Score+$MyRow->QuD5Score+$MyRow->QuD6Score : ''),
				"Tot8"=>(($FillWithArrows && $_REQUEST['ScoreDraw']!="TargetNo") ? $MyRow->QuD1Score+$MyRow->QuD2Score+$MyRow->QuD3Score+$MyRow->QuD4Score+$MyRow->QuD5Score+$MyRow->QuD6Score+$MyRow->QuD7Score : ''),
				"QuD1"=>$MyRow->QuD1Score,
				"QuD2"=>$MyRow->QuD2Score,
				"QuD3"=>$MyRow->QuD3Score,
				"QuD4"=>$MyRow->QuD4Score,
				"QuD5"=>$MyRow->QuD5Score,
				"QuD6"=>$MyRow->QuD6Score,
				"QuD7"=>$MyRow->QuD7Score,
				"QuD8"=>$MyRow->QuD8Score,
				"QuGD1"=>$MyRow->QuD1Gold,
				"QuGD2"=>$MyRow->QuD2Gold,
				"QuGD3"=>$MyRow->QuD3Gold,
				"QuGD4"=>$MyRow->QuD4Gold,
				"QuGD5"=>$MyRow->QuD5Gold,
				"QuGD6"=>$MyRow->QuD6Gold,
				"QuGD7"=>$MyRow->QuD7Gold,
				"QuGD8"=>$MyRow->QuD8Gold,
				"QuXD1"=>$MyRow->QuD1XNine,
				"QuXD2"=>$MyRow->QuD2XNine,
				"QuXD3"=>$MyRow->QuD3XNine,
				"QuXD4"=>$MyRow->QuD4XNine,
				"QuXD5"=>$MyRow->QuD5XNine,
				"QuXD6"=>$MyRow->QuD6XNine,
				"QuXD7"=>$MyRow->QuD7XNine,
				"QuXD8"=>$MyRow->QuD8XNine,
				"Ath"=>($_REQUEST['ScoreDraw']!="TargetNo" ? $MyRow->Ath : ''),
				"Noc"=>($_REQUEST['ScoreDraw']!="TargetNo" ? $MyRow->Noc : ''),
				"CoCode"=>($_REQUEST['ScoreDraw']!="TargetNo" ? $MyRow->CoCode : ''),
				"CoName"=>($_REQUEST['ScoreDraw']!="TargetNo" ? $MyRow->CoName : ''),
			);
		}

		if(count($Tmp)>0)
		{
			foreach($DistArray as $CurDist)
			{
				if($CurDist and $Tmp[0]["D" . $CurDist]=='-') continue;
				$pdf->AddPage($Ath4Target<=3 ? 'L' : 'P' );
				foreach($Tmp as $Value)
				{
					$Value['FirstDist']=($CurDist==1);
					if($CurDist==0) {
						unset($Value["Dist"]);
						$Value["gxD0"]='';
					} else {
						$Value["Dist"]=$Value["D" . $CurDist];
					}

					$Value["CurDist"]=$CurDist;

						switch($Ath4Target) {
							case 2:
								if(substr($Value["tNo"],-1,1)=="A")
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								elseif(substr($Value["tNo"],-1,1)=="B")
									$pdf->DrawScore(2*$defScoreX+$defScoreW, $defScoreY, $defScoreW, $defScoreH,$NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								break;
							case 3:
								if(substr($Value["tNo"],-1,1)=="A")
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH,$NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								elseif(substr($Value["tNo"],-1,1)=="B")
									$pdf->DrawScore(2*$defScoreX+$defScoreW, $defScoreY, $defScoreW, $defScoreH,$NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								elseif(substr($Value["tNo"],-1,1)=="C")
									$pdf->DrawScore(3*$defScoreX+2*$defScoreW, $defScoreY, $defScoreW, $defScoreH,$NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								break;
							case 4:
								if(substr($Value["tNo"],-1,1)=="A")
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value, ($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								elseif(substr($Value["tNo"],-1,1)=="B")
									$pdf->DrawScore($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								elseif(substr($Value["tNo"],-1,1)=="C")
									$pdf->DrawScore($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								elseif(substr($Value["tNo"],-1,1)=="D")
									$pdf->DrawScore($defScoreX2, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								break;
							case 5:
								if(substr($Value["tNo"],-1,1)=="A") {
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value, ($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="B") {
									$pdf->DrawScore($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="C") {
									$pdf->DrawScore($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="D") {
									$pdf->AddPage('P');
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value, ($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="E") {
									$pdf->DrawScore($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								}
								break;
							case 6:
								if(substr($Value["tNo"],-1,1)=="A") {
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value, ($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="B") {
									$pdf->DrawScore($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="C") {
									$pdf->DrawScore($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="D") {
									$pdf->DrawScore($defScoreX2, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="E") {
									$pdf->AddPage('P');
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value, ($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="F") {
									$pdf->DrawScore($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								}
								break;
							case 7:
								if(substr($Value["tNo"],-1,1)=="A") {
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value, ($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="B") {
									$pdf->DrawScore($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="C") {
									$pdf->DrawScore($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="D") {
									$pdf->DrawScore($defScoreX2, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="E") {
									$pdf->AddPage('P');
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value, ($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="F") {
									$pdf->DrawScore($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="G") {
									$pdf->DrawScore($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								}
								break;
							case 8:
								if(substr($Value["tNo"],-1,1)=="A") {
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value, ($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="B") {
									$pdf->DrawScore($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="C") {
									$pdf->DrawScore($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="D") {
									$pdf->DrawScore($defScoreX2, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="E") {
									$pdf->AddPage('P');
									$pdf->DrawScore($defScoreX, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value, ($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="F") {
									$pdf->DrawScore($defScoreX2, $defScoreY, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="G") {
									$pdf->DrawScore($defScoreX, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								} elseif(substr($Value["tNo"],-1,1)=="H") {
									$pdf->DrawScore($defScoreX2, $defScoreY2, $defScoreW, $defScoreH, $NumEnd,3,$Value,($CurDist==0) ?  '' : $Value["Arr" . $CurDist],($CurDist==0) ?  '' : $Value["Tot" . $CurDist], $Value["gxD" . $CurDist]);
								}
								break;
						}
				}
				if(!empty($_REQUEST['QRCode'])) {
					foreach($_REQUEST['QRCode'] as $k => $Api) {
						require_once('Api/'.$Api.'/DrawQRCode.php');
						$Function='DrawQRCode_'.preg_replace('/[^a-z0-9]/sim', '_', $Api);
						$Function($pdf, $QRCodeX + 30*$k, $QRCodeY, $_REQUEST['x_Session'], $CurDist, $TmpTarget);
// 						$pdf->cell
					}
				}
			}
		}
		safe_free_result($Rs);
	}
}
if(empty($_REQUEST["GetScorecardAsString"])) {
	$pdf->Output();
} else {
	return $pdf->Output('', 'S');
}
?>