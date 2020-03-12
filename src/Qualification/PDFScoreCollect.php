<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ScorePDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Sessions.inc.php');
checkACL(AclQualification, AclReadOnly);

// Works for collecting up to 12 ends of 3 arrows
// changing the define here to have 6 ends of 6 arrows

define('IANSEO_ARROWS', empty($_GET['arr']) ? 3 : intval($_GET['arr']));

// get how many athletes per target
$Ath4Target = 4;
$session=intval($_REQUEST['x_Session']);
if($session>0) {
	$ses=GetSessions(null,false,array($session.'_Q'));
	$Ath4Target = $ses[0]->SesAth4Target;
}

// Defines which kind of layout
$Portrait = ($Ath4Target == 4);

// creates the PDF
$pdf = new ScorePDF($Portrait);

// get how many ends
$NumEnd = 12;
$MyQuery = "SELECT ToNumEnds AS TtNumEnds FROM Tournament  WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
$Rs=safe_r_sql($MyQuery);
if($r=safe_fetch($Rs)) {
	$NumEnd=$r->TtNumEnds;
}

if(empty($_REQUEST["ScoreHeader"])) $pdf->HideHeader();
if(empty($_REQUEST["ScoreLogos"])) $pdf->HideLogo();
if(empty($_REQUEST["ScoreFlags"])) $pdf->HideFlags();
if(isset($_REQUEST["ScoreDraw"]) && $_REQUEST["ScoreDraw"]=="Data") $pdf->NoDrawing();

// lets start with 12 collecting tickets of 3 arrows
// Landscape mode (2 or 3 athletes)
// 10  07  04  01
// 11  08  05  02
// 12  09  06  03
// Portrait mode (4 athletes)
// 12  11  10
// 09  08  07
// 06  05  04
// 03  02  01

$Margin=$pdf->getSideMargin();
$XScorePos=array();
$YScorePos=array();

if($Portrait) {
	// portrait mode!
	$NumW = 3;
	$NumH = ceil(($NumEnd*3)/(IANSEO_ARROWS*$NumW));
	if($NumW > $NumH) {
		$NumW = $NumH;
		$NumH = 3;
	}
} else {
	// Landscape mode!
	$NumH = 3;
	$NumW = ceil(($NumEnd*3)/(IANSEO_ARROWS*$NumH));
}

$ScoreW=($pdf->GetPageWidth()-$Margin*($NumW+1))/$NumW;
$ScoreH=($pdf->GetPageHeight()-$Margin*($NumH+1))/$NumH;

if($Portrait) {
	foreach(range($NumH-1,0) as $r) foreach(range($NumW-1,0) as $c) {
		$XScorePos[]=$Margin+($ScoreW+$Margin)*$c;
		$YScorePos[]=$Margin+($ScoreH+$Margin)*$r;
	}
} else {
	foreach(range($NumW-1,0) as $c) foreach(range(0,$NumH-1) as $r) {
		$XScorePos[]=$Margin+($ScoreW+$Margin)*$c;
		$YScorePos[]=$Margin+($ScoreH+$Margin)*$r;
	}
}

if(isset($_REQUEST["ScoreDraw"]) && $_REQUEST['ScoreDraw']=="Draw") {
	$pdf->AddPage();
	$Data=array();
	foreach(range(1, $Ath4Target) as $k)  $Data[]='';
	foreach($XScorePos as $k => $Xpos) {
		$pdf->DrawCollector($Xpos, $YScorePos[$k], $ScoreW, $ScoreH, $k+1, IANSEO_ARROWS, $Data);
	}
	$oldline=$pdf->GetLineStyle();
	$pdf->SetLineStyle(array('width'=>0.25,'dash'=>'2,5'));
	foreach(range(1, $NumW-1) as $X) $pdf->Line($x = $Margin*0.5 + $X*($ScoreW+$Margin), 0, $x, $pdf->getPageHeight());
	foreach(range(1, $NumH-1) as $Y) $pdf->Line(0, $y = $Margin*0.5 + $Y*($ScoreH+$Margin), $pdf->getPageWidth(), $y);
	$pdf->SetLineStyle($oldline);
} else {
	$MyQuery = 'SELECT SUBSTRING(at.AtTargetNo,2) as tNo, CoCode, CoName, Ath, Noc, Cat, Td1, Td2, Td3, Td4, Td5, Td6, Td7, Td8, '
		. 'QuD1Arrowstring, QuD2Arrowstring, QuD3Arrowstring, QuD4Arrowstring, QuD5Arrowstring, QuD6Arrowstring, QuD7Arrowstring, QuD8Arrowstring, '
		. 'QuD1Score, QuD2Score, QuD3Score, QuD4Score, QuD5Score, QuD6Score, QuD7Score, QuD8Score, '
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
		. ' (SELECT CoCode, CoName, QuTargetNo, CONCAT(EnFirstName,\' \', EnName) AS Ath, CONCAT(CoCode, \' - \', CoName) as Noc, CONCAT(EnDivision, \' \', EnClass) AS Cat, '
		. ' Td1, Td2, Td3, Td4, Td5, Td6, Td7, Td8, '
		. ' QuD1Arrowstring, QuD2Arrowstring, QuD3Arrowstring, QuD4Arrowstring, QuD5Arrowstring, QuD6Arrowstring, QuD7Arrowstring, QuD8Arrowstring, '
		. ' QuD1Score, QuD2Score, QuD3Score, QuD4Score, QuD5Score, QuD6Score, QuD7Score, QuD8Score, '
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
		//print $MyQuery;Exit;
	$Rs=safe_r_sql($MyQuery);
	if(safe_num_rows($Rs)>0)
	{
		$TmpTarget='-----';
		$Tmp=array();
		$DistArray=array();

		$tmpRow=safe_fetch($Rs);
		safe_data_seek($Rs,0);

		if(is_array($_REQUEST["ScoreDist"])) {
			foreach($_REQUEST["ScoreDist"] as $Value) {
				if(is_numeric($Value)) {
					$DistArray[$Value]=($Value ? $tmpRow->{'Td'.$Value} : '');
				}
			}
		} else {
			$DistArray[]='';
		}

		$Butt=array();
		$OldButt='';
		while($MyRow=safe_fetch($Rs)) {
			if($OldButt != substr($MyRow->tNo,0,-1)) {
				if($Butt) {
					// Athletes are there!
					foreach($DistArray as $CurDist) {
						$pdf->AddPage();
						foreach($XScorePos as $k => $Xpos) {
							$pdf->DrawCollector($Xpos, $YScorePos[$k], $ScoreW, $ScoreH, $k+1, IANSEO_ARROWS, $Butt, ltrim($OldButt,'0'), $CurDist);
						}
						$oldline=$pdf->GetLineStyle();
						$pdf->SetLineStyle(array('width'=>0.25,'dash'=>'2,5'));
						foreach(range(1, $NumW-1) as $X) $pdf->Line($x = $Margin*0.5 + $X*($ScoreW+$Margin), 0, $x, $pdf->getPageHeight());
						foreach(range(1, $NumH-1) as $Y) $pdf->Line(0, $y = $Margin*0.5 + $Y*($ScoreH+$Margin), $pdf->getPageWidth(), $y);
						$pdf->SetLineStyle($oldline);
					}
				}
				$OldButt = substr($MyRow->tNo,0,-1);
				$Butt=array();
			}
			$Butt[]=$MyRow->Ath;
		}

		if($Butt) {
			// Athletes are there!
			foreach($DistArray as $CurDist) {
				$pdf->AddPage();
				foreach($XScorePos as $k => $Xpos) {
					$pdf->DrawCollector($Xpos, $YScorePos[$k], $ScoreW, $ScoreH, $k+1, IANSEO_ARROWS, $Butt, ltrim($OldButt, '0'), $CurDist);
				}

				$oldline=$pdf->GetLineStyle();
				$pdf->SetLineStyle(array('width'=>0.25,'dash'=>'2,5'));
				foreach(range(1, $NumW-1) as $X) $pdf->Line($x = $Margin*0.5 + $X*($ScoreW+$Margin), 0, $x, $pdf->getPageHeight());
				foreach(range(1, $NumH-1) as $Y) $pdf->Line(0, $y = $Margin*0.5 + $Y*($ScoreH+$Margin), $pdf->getPageWidth(), $y);
				$pdf->SetLineStyle($oldline);
			}
		}
		safe_free_result($Rs);
	}
}
$pdf->Output();
?>