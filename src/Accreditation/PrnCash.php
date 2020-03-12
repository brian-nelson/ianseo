<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACL(AclParticipants, AclReadOnly);
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_Number.inc.php');
require_once('Common/Fun_FormatText.inc.php');

$OpDetails = "Accreditation";
if(isset($_REQUEST["OperationType"]))
	$OpDetails = $_REQUEST["OperationType"];

if(!isset($isCompleteResultBook))
	$pdf = new ResultPDF((get_text($OpDetails,'Tournament')));
/*
$MyQuery = 'SELECT DATE_FORMAT(AEWhen,\''.get_text('DateFmtDB').'\') as Data, QuSession as Turno, COUNT(EnId) as Accreditati, SUM(APPrice) as Totale'
        . ' FROM AccEntries'
        . ' INNER JOIN Entries ON AEId=EnId AND EnPays=1 AND AETournament = EnTournament'
        . ' INNER JOIN Qualifications ON EnId=QuId'
        . ' INNER JOIN AccPrice ON EnTournament=APTournament AND CONCAT(EnDivision,EnClass) LIKE APDivClass'
        . ' WHERE AETournament = ' . StrSafe_DB($_SESSION['TourId'])
		. ' AND AEOperation = (SELECT AOTId FROM AccOperationType WHERE AOTDescr=' . StrSafe_DB($OpDetails) . ')'
        . ' GROUP BY QuSession, DATE_FORMAT(AEWhen,\''.get_text('DateFmtDB').'\')'
        . ' ORDER BY Data ASC, Turno ASC';
*/
$MyQuery = 'SELECT DATE_FORMAT(AEWhen,\''.get_text('DateFmtDB').'\') as Data, QuSession as Turno, COUNT(EnId) as Accreditati, IF(EnPays=0,"NoPay",ifnull(APPrice,0)) as Prezzo, SUM(IF(EnPays=1,APPrice,0)) as Totale'
        . ' FROM AccEntries'
        . ' INNER JOIN Entries ON AEId=EnId /*AND EnPays=1*/ AND AETournament = EnTournament'
        . ' INNER JOIN Qualifications ON EnId=QuId'
        . ' LEFT JOIN AccPrice ON EnTournament=APTournament AND CONCAT(EnDivision,EnClass) LIKE APDivClass'
        . ' WHERE AETournament = ' . StrSafe_DB($_SESSION['TourId'])
	. ' AND AEOperation = (SELECT AOTId FROM AccOperationType WHERE AOTDescr=' . StrSafe_DB($OpDetails) . ')'
        . ' GROUP BY QuSession, EnPays, IF(EnPays=0,"NoPay",ifnull(APPrice,0)), DATE_FORMAT(AEWhen,\''.get_text('DateFmtDB').'\')'
        . ' ORDER BY Data ASC, Turno ASC, EnPays DESC, APPrice DESC';
//echo $MyQuery;exit;
$Rs=safe_r_sql($MyQuery);
if($Rs)
{
	$TotalDay=0;
	$Total=0;
	$OldDate='';
	$isFirstTime=true;
	while($MyRow=safe_fetch($Rs))
	{
		if($OldDate != $MyRow->Data)
		{
			if($OldDate != '')
			{
				$pdf->SetFont($pdf->FontStd,'',8);
				$pdf->Cell(30, 6, '', 0, 0, 'L', 0);
				$pdf->Cell(115, 6, get_text('Total') . " " . ($OldDate), 0, 0, 'R', 0);
				$pdf->SetFont($pdf->FontStd,'B',8);
				$pdf->Cell(45, 6, NumFormat($TotalDay,2) . " " . $pdf->writeCurrency(), 1, 1, 'R', 0);
			}

			$isFirstTime=true;
			$pdf->SetY($pdf->GetY()+5);
		   	$pdf->SetFont($pdf->FontStd,'B',8);
			$pdf->Cell(30, 7,  '', 0, 0, 'C', 0);
			$pdf->Cell(30, 7,  (get_text('TourWhen','Tournament')), 1, 0, 'C', 1);
			$pdf->Cell(25, 7,  (get_text('SessionShort','Tournament')), 1, 0, 'C', 1);
			$pdf->Cell(30, 7,  (get_text('Number')), 1, 0, 'C', 1);
			$pdf->Cell(30, 7,  (get_text('Price','Tournament')), 1, 0, 'C', 1);
			$pdf->Cell(45, 7,  (get_text('Total')), 1, 1, 'C', 1);
			$pdf->SetFont($pdf->FontStd,'',1);
			$pdf->Cell(190, 0.5,  '', 0, 1, 'C', 0);
			$OldDate = $MyRow->Data;
			$TotalDay=0;
		}
	   	$pdf->SetFont($pdf->FontStd,'',8);
		$pdf->Cell(30, 5,  '', 0, 0, 'C', 0);
		if($isFirstTime)
			$pdf->Cell(30, 5, ($MyRow->Data), 1, 0, 'R', 0);
		else
			$pdf->Cell(30, 5, '', 0, 0, 'R', 0);
		$pdf->Cell(25, 5,  ($MyRow->Turno), 1, 0, 'R', 0);
		$pdf->Cell(30, 5,  ($MyRow->Accreditati), 1, 0, 'R', 0);
		$pdf->Cell(30, 5,  $MyRow->Prezzo=='NoPay' ? get_text('NoPay','Tournament') : $MyRow->Prezzo . " " . $pdf->writeCurrency(), 1, 0, 'R', 0);
		$pdf->Cell(45, 5,  NumFormat($MyRow->Totale,2) . " " . $pdf->writeCurrency(), 1, 1, 'R', 0);
		$TotalDay += $MyRow->Totale;
		$Total += $MyRow->Totale;
		$isFirstTime=false;
	}
	$pdf->SetFont($pdf->FontStd,'',8);
	$pdf->Cell(30, 6, '', 0, 0, 'L', 0);
	$pdf->Cell(115, 6, (get_text('Total')) . " " . ($OldDate), 0, 0, 'R', 0);
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell(45, 6, NumFormat($TotalDay,2) . " " . $pdf->writeCurrency(), 1, 1, 'R', 0);

//Totale Globale
	$pdf->SetXY(10,$pdf->GetY()+5);
	$pdf->SetFont($pdf->FontStd,'',8);
	$pdf->Cell(30, 6, '', 0, 0, 'L', 0);
	$pdf->Cell(115, 6, (get_text('Total')), 1, 0, 'R', 1);
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell(45, 6, NumFormat($Total,2) . " " . $pdf->writeCurrency(), 1, 1, 'R', 0);

	safe_free_result($Rs);
}
if(!isset($isCompleteResultBook))
	$pdf->Output();
?>
