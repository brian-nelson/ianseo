<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACL(AclParticipants, AclReadOnly);
require_once('Common/pdf/ResultPDF.inc.php');

if(!isset($isCompleteResultBook))
	$pdf = new ResultPDF((get_text('StatClasses','Tournament')),false);

$SesArray=array();
$DivArray=array();
$ClsArray=array();
$TotArray=array();
$SesFields=array();
$Categories=array();
$MyQuery = "SELECT DISTINCT QuSession " .
	"FROM Qualifications INNER JOIN Entries ON QuId = EnId AND EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " " .
	"ORDER BY QuSession";
$Rs = safe_r_sql($MyQuery);
while($MyRow=safe_fetch($Rs)) $SesArray[] = $MyRow->QuSession;
safe_free_result($Rs);

$Sql = "SELECT ";
$MyQuery
	= "SELECT DISTINCT EnDivision, DivDescription FROM Entries LEFT JOIN Divisions ON EnDivision=DivId AND DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
	. "WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " ORDER BY DivViewOrder ";
$Rs = safe_r_sql($MyQuery);
while($MyRow=safe_fetch($Rs)) {
	$DivArray[(trim($MyRow->EnDivision)!='' ? $MyRow->EnDivision : '--')] = $MyRow->DivDescription;
	foreach($SesArray as $Value) {
		$Sql .= "SUM(IF(TRIM(EnDivision)='" . trim($MyRow->EnDivision) . "' AND QuSession='" . $Value . "',1,0)) as `" . (trim($MyRow->EnDivision)!='' ? $MyRow->EnDivision : '--') . $Value . "`, ";
		$SesFields[]= (trim($MyRow->EnDivision)!='' ? $MyRow->EnDivision : '--') . $Value;
	}
}
safe_free_result($Rs);

$DivKeys=array_keys($DivArray);

$SqlEmpty=$Sql;
$Sql .= "ClId, ClDescription "
	. "FROM Classes "
	. "LEFT JOIN Entries ON TRIM(ClId) = TRIM(EnClass) AND ClTournament=EnTournament "
	. "LEFT JOIN Qualifications ON EnId = QuId "
	. "WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " "
	. "GROUP BY ClId "
	. "ORDER BY ClViewOrder";
$SqlEmpty .= "'--' AS ClId, '' as ClDescription  "
	. "FROM Entries "
	. "LEFT JOIN Qualifications ON EnId = QuId "
	. "WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EnClass='' "
	. "GROUP BY EnClass ";
$Rs=safe_r_sql($Sql);
$RsEmpty=safe_r_sql($SqlEmpty);

if($Rs && count($DivArray)>0)
{
	$ShowStatusLegend = false;
	$FirstTime=true;
	$DivSize=(($pdf->getPageWidth()-35)/count($DivArray));
	$SesSize=min(20,($DivSize/(count($SesArray)+1)));
	$SumSize=$SesSize * ($SesSize<5 ? 2:1);
	$DivSize=$SesSize*(count($SesArray)+1);
	$PageWidth=$DivSize*count($DivArray);
	while($MyRow=safe_fetch($Rs))
	{
		if ($FirstTime || !$pdf->SamePage(16))
		{
			$TmpSegue = !$pdf->SamePage(16);
		   	$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->SetXY(25,$pdf->GetY()+5);
			$pdf->Cell(($PageWidth), 6,  (get_text('StatClasses','Tournament')), 1, 1, 'C', 1);
			if($TmpSegue)
			{
				$pdf->SetXY(($pdf->getPageWidth()-40),$pdf->GetY()-6);
			   	$pdf->SetFont($pdf->FontStd,'I',6);
				$pdf->Cell(30, 6,  (get_text('Continue')), 0, 1, 'R', 0);
			}
			$pdf->SetX(25);
		   	$pdf->SetFont($pdf->FontStd,'B',10);
			foreach($DivArray as $Value => $DivDescr)
				$pdf->Cell($DivSize, 6,  $Value, 1, 0, 'C', 1);
			$pdf->Cell(0.1, 6,  '', 0, 1, 'C', 0);

			$pdf->SetX(25);
		   	$pdf->SetFont($pdf->FontStd,'B',8);
			for($i=0; $i < count($DivArray); $i++)
			{
				foreach($SesArray as $Value)
				{
					$TotArray[]=0;
					$pdf->Cell($SesSize, 4,  ($Value==0 ? '--' : $Value), 1, 0, 'C', 1);
				}
				$pdf->Cell($SesSize, 4,   (get_text('TotalShort','Tournament')), 1, 0, 'C', 1);
			}
			$pdf->Cell(0.1, 4,  '', 0, 1, 'C', 0);
			$FirstTime=false;
		}
	   	$pdf->SetFont($pdf->FontStd,'',8);
		$pdf->Cell(15, 5,  trim($MyRow->ClId), 1, 0, 'C', 1);

		for($i=0; $i < count($DivArray); $i++)
		{
			$pdf->SetFont($pdf->FontStd,'',7);
			$TmpCounter=0;
			for($j=0; $j < count($SesArray); $j++)
			{
				$TmpValue = $MyRow->{trim($SesFields[$i*count($SesArray)+$j])};
				$TotArray[$i*count($SesArray)+$j] += $TmpValue;
				$TmpCounter += $TmpValue;
				$pdf->Cell($SesSize, 5, ($TmpValue>0 ? $TmpValue : ''), 1, 0, 'R', 0);
			}
			$pdf->SetFont($pdf->FontStd,'B',7);
			$pdf->Cell($SesSize, 5,  $TmpCounter, 1, 0, 'R', 0);
			$ClsArray[(trim($MyRow->ClId)!='' ? $MyRow->ClId : '--')] = $MyRow->ClDescription;
// 			if($TmpCounter) $Categories[$DivArray[$DivKeys[$i]].' '.$MyRow->ClDescription]=array($DivKeys[$i].$MyRow->ClId, $TmpCounter);
			if($TmpCounter) $Categories[$DivKeys[$i]][(trim($MyRow->ClId)!='' ? $MyRow->ClId : '--')]=$TmpCounter;
		}
		$pdf->Cell(0.1, 5,  '', 0, 1, 'C', 0);
	}
//Righe con classe vuota
	if(safe_num_rows($RsEmpty)>0)
	{
		while($MyRow=safe_fetch($RsEmpty))
		{
		   	$pdf->SetFont($pdf->FontStd,'',8);
			$pdf->Cell(15, 5,  ($MyRow->ClId), 1, 0, 'C', 1);

			for($i=0; $i < count($DivArray); $i++)
			{
				$pdf->SetFont($pdf->FontStd,'',7);
				$TmpCounter=0;
				for($j=0; $j < count($SesArray); $j++)
				{
					$TmpValue = $MyRow->{$SesFields[$i*count($SesArray)+$j]};
					$TotArray[$i*count($SesArray)+$j] += $TmpValue;
					$TmpCounter += $TmpValue;
					$pdf->Cell($SesSize, 5,  ($TmpValue>0 ? $TmpValue : ''), 1, 0, 'R', 0);
				}
				$pdf->SetFont($pdf->FontStd,'B',7);
				$pdf->Cell($SesSize, 5,  $TmpCounter, 1, 0, 'R', 0);
// 				if($TmpCounter) $Categories[$DivArray[$DivKeys[$i]].' '.$MyRow->ClDescription]=array($DivKeys[$i].$MyRow->ClId, $TmpCounter);
				$ClsArray[(trim($MyRow->ClId)!='' ? $MyRow->ClId : '--')] = $MyRow->ClDescription;
				if($TmpCounter) $Categories[$DivKeys[$i]][(trim($MyRow->ClId)!='' ? $MyRow->ClId : '--')]=$TmpCounter;
			}
			$pdf->Cell(0.1, 5,  '', 0, 1, 'C', 0);
		}
	}

//Divider
	$pdf->SetFont($pdf->FontStd,'B',1);
	$pdf->Cell($PageWidth+15, 0.5, '', 1, 1, 'C', 0);
//Totali
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell(15, 5,  (get_text('Total')), 1, 0, 'C', 1);
	for($i=0; $i < count($DivArray); $i++)
	{
		$pdf->SetFont($pdf->FontStd,'B',7);
		$TmpCounter=0;
		for($j=0; $j < count($SesArray); $j++)
		{
			$TmpCounter += $TotArray[$i*count($SesArray)+$j];
			$pdf->Cell($SesSize, 5, $TotArray[$i*count($SesArray)+$j], 1, 0, 'R', 0);
		}
		$pdf->SetFont($pdf->FontStd,'B',8);
		$pdf->Cell($SesSize, 5,  $TmpCounter, 1, 0, 'R', 1);
	}
	$pdf->Cell(0.1, 5,  '', 0, 1, 'C', 0);


//Totali per turni
	$YORG=$pdf->GetY()+5;
	$LEFT=$pdf->GetX();
	if(!$pdf->SamePage(11 + count($SesArray)*5)) {
		$pdf->AddPage();
		$YORG=$pdf->GetY()+5;
	}
   	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->SetXY(25, $YORG);
	foreach($DivArray as $Value => $DivDescr) {
		$pdf->Cell($SumSize, 6,  $Value, 1, 0, 'C', 1);
	}
	$pdf->Cell($SumSize, 6,   (get_text('TotalShort','Tournament')), 1, 0, 'C', 1);
	$pdf->Cell(0.1, 6,  '', 0, 1, 'C', 0);
//Totali
	for($i=0; $i < count($SesArray); $i++)
	{
		$pdf->SetFont($pdf->FontStd,'B',8);
		$pdf->Cell(15, 5,  $SesArray[$i]==0 ? '--' : $SesArray[$i], 1, 0, 'C', 1);
		$pdf->SetFont($pdf->FontStd,'',7);
		$TmpCounter=0;
		for($j=0; $j < count($DivArray); $j++)
		{
			$TmpCounter += $TotArray[$j*count($SesArray)+$i];
			$pdf->Cell($SumSize, 5, $TotArray[$j*count($SesArray)+$i], 1, 0, 'R', 0);
		}
		$pdf->SetFont($pdf->FontStd,'B',8);
		$pdf->Cell($SumSize, 5,  $TmpCounter, 1, 0, 'R', 0);
		$pdf->Cell(0.1, 5,  '', 0, 1, 'C', 0);
	}
	$pdf->SetFont($pdf->FontStd,'B',1);
	$pdf->Cell($SumSize*(count($DivArray)+1)+15, 0.5, '', 1, 1, 'C', 0);
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell(15, 5, (get_text('Total')), 1, 0, 'C', 1);
	$GrandTotal=0;
	for($i=0; $i < count($DivArray); $i++)
	{
		$pdf->SetFont($pdf->FontStd,'B',7);
		$TmpCounter=0;
		for($j=0; $j < count($SesArray); $j++)
			$TmpCounter += $TotArray[$i*count($SesArray)+$j];
		$pdf->Cell($SumSize, 5,  $TmpCounter, 1, 0, 'R', 0);
		$GrandTotal+=$TmpCounter;
	}
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell($SumSize, 5,  $GrandTotal, 1, 0, 'R', 1);
	$pdf->Cell(0.1, 5,  '', 0, 1, 'C', 0);
	safe_free_result($Rs);

	if($Categories) {
// 		ksort($Categories);
		$TotCats=0;
		foreach($Categories as $k=>$v) $TotCats+=count($v);

		if(!$pdf->SamePage(2*$TotCats)) {
			// cannot print all categories in the bottom right square... go to a new page
			// 3 columns to put the data
			$pdf->AddPage();
			$YORG=$pdf->GetY()+5;
			$cols=3;
			$Left=10;
			$cellHead=  (($pdf->getPageWidth()-20-$Left)/2)-$SesSize-15;
			$i=0;
			$ChangeColumn=ceil($TotCats/2);
		} else {
			$cols=2;
			$Left=($SumSize*(count($DivArray)+1))+35;
			$cellHead=  (($pdf->getPageWidth()-20-$Left)/2)-$SesSize-15;
			$i=0;
			$ChangeColumn=ceil($TotCats/2);
		}

	   	$pdf->SetFont($pdf->FontStd,'B',8);
		$SesSize+=6;
		$cellHead-=6;
	   	foreach($DivArray as $DivCode => $DivDescription) {
	   		foreach($ClsArray as $ClsCode => $ClsDescription) {
	   			if(empty($Categories[$DivCode][$ClsCode])) continue;

				if($i==$ChangeColumn) {
					$Left+=$cellHead+$SesSize+25;
					$YORG-=4*$i;
				}
				$pdf->SetXY($Left, $YORG+4*$i++);
				$pdf->Cell(15, 4,  $DivCode.$ClsCode, 1, 0, 'L', 1);
				$pdf->Cell($SesSize, 4,  $Categories[$DivCode][$ClsCode], 1, 0, 'R', 0);
				$pdf->Cell($cellHead, 4,  $DivDescription.' '.$ClsDescription, 1, 0, 'L', 0);
	   		}
	   	}
	}
}
if(!isset($isCompleteResultBook))
	$pdf->Output();
?>