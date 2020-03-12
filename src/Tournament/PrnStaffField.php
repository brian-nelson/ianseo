<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
checkACL(AclCompetition, AclReadOnly);
define("HideCols", GetParameter("IntEvent"));

$CatJudge=isset($_REQUEST['judge']);
$CatDos=isset($_REQUEST['dos']);
$CatJury=isset($_REQUEST['jury']);
$CatOC=isset($_REQUEST['oc']);

if(!isset($isCompleteResultBook)) 
	$pdf = new ResultPDF((get_text('StaffOnField','Tournament')),true,'',false);


$Ses=StrSafe_DB($_SESSION['TourId']);

$Filter=array();

if ($CatJudge)
	$Filter[]=" ItJudge<>0 ";
	
if ($CatDos)
	$Filter[]=" ItDoS<>0 ";
	
if ($CatJury)
	$Filter[]=" ItJury<>0 ";
	
if ($CatOC)
	$Filter[]=" ItOC<>0 ";

if (count($Filter)>0)
	$Filter="AND (" . implode(" OR ",$Filter) . ") ";
else
	$Filter="";	
	
$Select="
	SELECT ti.*, it.*,IF(ItJudge!=0,'CatJudge',IF(ItDoS!=0,'CatDos',IF(ItJury!=0,'CatJury','CatOC'))) AS `Category` 
	FROM TournamentInvolved AS ti LEFT JOIN InvolvedType AS it ON ti.TiType=it.ItId 
	WHERE ti.TiTournament={$Ses} AND it.ItId IS NOT NULL {$Filter}
	ORDER BY IF(ItJudge!=0,1,IF(ItDoS!=0,2,IF(ItJury!=0,3,4))) ASC, IF(ItJudge!=0,ItJudge,IF(ItDoS!=0,ItDoS,IF(ItJury!=0,ItJury,ItOC))) ASC,ti.TiName ASC
";	 
//print $Select;Exit;
$Rs=safe_r_sql($Select);

$CurCategory='';

if ($Rs && safe_num_rows($Rs)>0) {
	while ($MyRow=safe_fetch($Rs)) {
		if ($CurCategory!=$MyRow->Category) {	
			$pdf->Ln(10);
			$pdf->SetFont($pdf->FontStd,'B',14);
			$pdf->Cell(190, 8,  (get_text($MyRow->Category,'Tournament')), 0, 1, 'L');
		}
		
		$pdf->SetFont($pdf->FontStd,'',10);
		
		$pdf->Cell(10, 8,  '', 0, 0);
		$pdf->Cell(180, 6,  $MyRow->TiName . ' - ' . get_text($MyRow->ItDescription,'Tournament'), 0, 1);
		$CurCategory=$MyRow->Category;
		
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
