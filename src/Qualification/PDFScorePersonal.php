<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ScorePDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
checkACL(AclQualification, AclReadOnly);

$pdf = new ScorePDF(true);

$NumEnd = 12;
$NumDistances = 1;

$defScoreX = $pdf->getSideMargin();
$defScoreX2 = ($pdf->GetPageWidth()+$pdf->getSideMargin())/2;
$defScoreY = $pdf->getSideMargin();
$defScoreY2 = ($pdf->GetPageHeight()+$pdf->getSideMargin())/2;

$defScoreW = ($pdf->GetPageWidth()-$pdf->getSideMargin()*3)/2;
$defScoreH = ($pdf->GetPageHeight()-$pdf->getSideMargin()*3)/2;

$MisArray = array(1=>array($defScoreX,$defScoreY), 2=>array($defScoreX2,$defScoreY), 3=>array($defScoreX,$defScoreY2), 4=>array($defScoreX2,$defScoreY2), 5=>array($defScoreX,$defScoreY), 6=>array($defScoreX2,$defScoreY), 7=>array($defScoreX,$defScoreY2), 8=>array($defScoreX2,$defScoreY2));

//$MyQuery = "SELECT TtNumEnds,TtNumDist FROM Tournament INNER JOIN Tournament*Type AS tt ON ToType=TtId WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);

$MyQuery = "SELECT ToNumEnds AS TtNumEnds,ToNumDist AS TtNumDist FROM Tournament WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
$Rs=safe_r_sql($MyQuery);
if(safe_num_rows($Rs)==1) {
	$r=safe_fetch($Rs);
	$NumEnd	= $r->TtNumEnds;
	$NumDistances= $r->TtNumDist;
}

$FillWithArrows = ((isset($_REQUEST["ScoreFilled"]) && $_REQUEST["ScoreFilled"]==1));

$pdf->FillWithArrows = $FillWithArrows;

if(!(isset($_REQUEST["ScoreHeader"]) && $_REQUEST["ScoreHeader"]==1))
	$pdf->HideHeader();

if(!(isset($_REQUEST["ScoreLogos"]) && $_REQUEST["ScoreLogos"]==1))
	$pdf->HideLogo();

if(isset($_REQUEST["ScoreDraw"]) && $_REQUEST["ScoreDraw"]=="Data")
	$pdf->NoDrawing();

if(isset($_REQUEST["ScoreDraw"]) && $_REQUEST["ScoreDraw"]=="CompleteTotals")
	$pdf->PrintTotalColumns();

if(!empty($_REQUEST["ScoreBarcode"])) $pdf->PrintBarcode=true;

if(isset($_REQUEST["ScoreDraw"]) && $_REQUEST['ScoreDraw']=="Draw")
{
	$pdf->AddPage();
	for($i=1; $i<=$NumDistances; $i++)
	{
		if($i==5)
			$pdf->AddPage();
		$pdf->DrawScore($MisArray[$i][0],$MisArray[$i][1],$defScoreW, $defScoreH, $NumEnd,3,array("tNo"=>''));
	}
}
else
{
	$MyQuery = 'SELECT SUBSTRING(at.AtTargetNo,2) as tNo, EnCode, EnDivision, EnClass, CoCode, CoName, Ath, Noc, Cat, Td1, Td2, Td3, Td4, Td5, Td6, Td7, Td8, '
		. 'QuD1Arrowstring, QuD2Arrowstring, QuD3Arrowstring, QuD4Arrowstring, QuD5Arrowstring, QuD6Arrowstring, QuD7Arrowstring, QuD8Arrowstring, '
		. 'QuD1Score, QuD2Score, QuD3Score, QuD4Score, QuD5Score, QuD6Score, QuD7Score, QuD8Score, '
		. ' QuD1Gold, QuD1XNine, QuD2Gold, QuD2XNine, QuD3Gold, QuD3XNine, QuD4Gold, QuD4XNine, '
		. ' QuD5Gold, QuD5XNine, QuD6Gold, QuD6XNine, QuD7Gold, QuD7XNine, QuD8Gold, QuD8XNine, '
		. 'printD1gx, printD2gx, printD3gx, printD4gx, printD5gx, printD6gx, printD7gx, printD8gx '
		. ' FROM AvailableTarget as at '
		. ' ' . ($FillWithArrows ? 'INNER' : 'LEFT') . ' JOIN ('
		. ' SELECT EnCode, EnDivision, EnClass, CoCode, CoName, QuTargetNo, CONCAT(EnFirstName,\' \', EnName) AS Ath, CONCAT(CoCode, \' - \', CoName) as Noc, CONCAT(EnDivision, \' \', EnClass) AS Cat, '
		. ' Td1, Td2, Td3, Td4, Td5, Td6, Td7, Td8,'
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
		. ' INNER JOIN TournamentDistances ON ToType=TdType and TdTournament=ToId AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE TdClasses '
		. ' WHERE EnTournament = ' . StrSafe_DB($_SESSION['TourId'])
        . (($_REQUEST['x_Session']==-1 and $FillWithArrows) ? "" : " AND QuTargetNo>='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_From'],TargetNoPadding,"0",STR_PAD_LEFT) . "A' AND QuTargetNo<='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_To'],TargetNoPadding,"0",STR_PAD_LEFT) . "Z' ")
		. ') as Sqy ON at.AtTargetNo = Sqy.QuTargetNo '
		. " WHERE at.AtTournament =  " . StrSafe_DB($_SESSION['TourId']) . ' '
		. (($_REQUEST['x_Session']==-1 and $FillWithArrows) ? "" : " AND at.AtTargetNo>='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_From'],TargetNoPadding,"0",STR_PAD_LEFT) . "A' AND at.AtTargetNo<='" . $_REQUEST['x_Session'] . str_pad($_REQUEST['x_To'],TargetNoPadding,"0",STR_PAD_LEFT) . "Z' ")
		. ' ORDER BY at.AtTargetNo ASC, Ath, Noc ';
//	print $MyQuery;Exit;
	$Rs=safe_r_sql($MyQuery);
	if(safe_num_rows($Rs)>0)
	{
		$TmpTarget='-----';
		$Tmp=array();
		$DistArray=array();

		while($MyRow=safe_fetch($Rs))
		{
			set_time_limit(0);
			$pdf->AddPage(($NumDistances<4 ? "L":"P"));
			$Value =  array(
				"tNo"=>$MyRow->tNo,
				"EnCode"=>$MyRow->EnCode,
				"Div"=>$MyRow->EnDivision,
				"Cls"=>$MyRow->EnClass,
				"Cat"=>($_REQUEST['ScoreDraw']!="TargetNo" ? $MyRow->Cat : ''),
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
				"CoName"=>($_REQUEST['ScoreDraw']!="TargetNo" ? $MyRow->CoName : ''));

			if($NumDistances<=2) {
				$defScoreX = $pdf->getSideMargin()*3;
				$defScoreH = ($pdf->GetPageHeight()-$pdf->getTopMargin()*2);
				$defScoreW = ($pdf->GetPageWidth()-$defScoreX*3)/2;
			} elseif($NumDistances==3) {
				$defScoreX = $pdf->getSideMargin();
				$defScoreH = ($pdf->GetPageHeight()-$pdf->getTopMargin()*2);
				$defScoreW = ($pdf->GetPageWidth()-$pdf->getSideMargin()*4)/3;
			} else {
				$defScoreX = $pdf->getSideMargin();
				$defScoreW = ($pdf->GetPageWidth()-$pdf->getSideMargin()*3)/2;
				$defScoreH = ($pdf->GetPageHeight()-$pdf->getSideMargin()*3)/2;
			}
			$defScoreX2 = ($pdf->GetPageWidth()+$pdf->getSideMargin())/2;
			$defScoreY = $pdf->getSideMargin();
			$defScoreY2 = ($pdf->GetPageHeight()+$pdf->getSideMargin())/2;

			$MisArray = array(1=>array($defScoreX,$defScoreY), 2=>array($defScoreX2,$defScoreY), 3=>array($defScoreX,$defScoreY2), 4=>array($defScoreX2,$defScoreY2), 5=>array($defScoreX,$defScoreY), 6=>array($defScoreX2,$defScoreY), 7=>array($defScoreX,$defScoreY2), 8=>array($defScoreX2,$defScoreY2));

			for($i=1; $i<=$NumDistances; $i++)
			{
				if($i==5)
					$pdf->AddPage(($NumDistances<4 ? "L":"P"));
				$Value["Dist"]=$Value["D" . $i];
				$Value["CurDist"]=$i;
				if($Value["Dist"]!= '-')
					$pdf->DrawScore($MisArray[$i][0],$MisArray[$i][1],$defScoreW,$defScoreH,$NumEnd,3,$Value,$Value["Arr" . $i],$Value["Tot" . $i], $Value["gxD" . $i]);
			}
		}
		safe_free_result($Rs);
	}
}
$pdf->Output();
?>