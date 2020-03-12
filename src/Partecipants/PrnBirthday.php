<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACL(AclParticipants, AclReadOnly);
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');


if(!isset($isCompleteResultBook))
	$pdf = new ResultPDF(get_text('Birthdays','Tournament'));

$Select = "SELECT
	CONCAT(upper(EnFirstName), ' ', EnName) as Athlete, DATE_FORMAT(EnDob,'" . get_text('DateFmtDB') . "') as DoB, CoCode, CoName, SesName, DATE_FORMAT(EnDob,'%d') as Day, DATE_FORMAT(EnDob,'%m') as Month, 
	SUBSTRING(QuTargetNo,1,1) AS Session, SUBSTRING(QuTargetNo,2) AS TargetNo
	FROM Entries
	INNER JOIN Qualifications ON EnId=QuId
	INNER JOIN Countries on EnCountry=CoId
	INNER JOIN Session ON SesTournament=EnTournament AND QuSession=SesOrder AND SesType='Q'
	WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . "
	AND DATE_FORMAT(EnDob,'%m%d') BETWEEN DATE_FORMAT(" . StrSafe_DB($_SESSION['TourRealWhenFrom']) . ",'%m%d') AND DATE_FORMAT(" . StrSafe_DB($_SESSION['TourRealWhenTo']) . ",'%m%d')
	ORDER BY  DATE_FORMAT(EnDob,'%m%d') ASC, EnName, EnFirstName";

$Rs=safe_r_sql($Select);

$CurDate='';

if ($Rs && safe_num_rows($Rs)>0)
{
	$pdf->SetFont($pdf->FontStd,'B',16);
	$pdf->Cell(190, 15, get_text('Birthdays','Tournament'), 0, 1, 'C');
	
	while ($MyRow=safe_fetch($Rs))
	{
		if ($CurDate!=$MyRow->Month.$MyRow->Day)
		{			
			if ($CurDate!='')
				$pdf->Cell(190, 3,  '', 0, 1);
				
			$pdf->SetFont($pdf->FontStd,'B',16);
			$pdf->Cell(190, 8, get_text($MyRow->Month.'_Short', 'DateTime') . " " . $MyRow->Day, 0, 1, 'L');
		}
		
		$pdf->SetFont($pdf->FontStd,'',10);
		
		$pdf->Cell(10, 8,  '', 0, 0);
		$pdf->Cell(70, 6,  $MyRow->Athlete . ' ('. $MyRow->DoB . ')', 0, 0);
		$pdf->Cell(50, 6,  $MyRow->CoCode . ' - '. $MyRow->CoName, 0, 0);
		$pdf->Cell(60, 6,  $MyRow->Session . ' - '. $MyRow->TargetNo . ($MyRow->SesName ?  ' (' . $MyRow->SesName . ')' : ''), 0, 1);
		$CurDate=$MyRow->Month.$MyRow->Day;
		
	}	
}

if(!isset($isCompleteResultBook))
{
	if(isset($_REQUEST['ToFitarco']))
	{
		$Dest='D';
		if (isset($_REQUEST['Dest']))
			$Dest=$_REQUEST['Dest'];
		$pdf->Output($_REQUEST['ToFitarco'],$Dest);
	}
	else
		$pdf->Output();
}
