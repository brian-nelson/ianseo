<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
include_once('Common/pdf/OrisPDF.inc.php');
include_once('Common/pdf/OrisBracketPDF.inc.php');
include_once('Common/Fun_FormatText.inc.php');
$cbIndFinal = false;
$cbIndElim = false;
$cbTeamFinal = false;

$Rs=safe_r_sql('SELECT EvCode FROM Events WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=0 AND EvShootOff=1');
$cbIndFinal = (safe_num_rows($Rs)>0);
$Rs=safe_r_sql('SELECT EvCode FROM Events WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=0 AND (EvE1ShootOff=1 OR EvE2ShootOff=1)');
$cbIndElim = (safe_num_rows($Rs)>0);
$Rs=safe_r_sql('SELECT EvCode FROM Events WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=1 AND EvShootOff=1');
$cbTeamFinal = (safe_num_rows($Rs)>0);

$isCompleteResultBook = true;

$pdf = new OrisBracketPDF('', 'Complete Result Book');
$pdf->SetAutoPageBreak(true,OrisPDF::bottomMargin);

error_reporting(E_ALL);

//Medaglieri
if($cbIndFinal || $cbTeamFinal)
{
	include 'OrisMedalStanding.php';
	$pdf->SetAutoPageBreak(true,OrisPDF::bottomMargin);
	include 'OrisMedalList.php';
}

include 'Partecipants/OrisCountry.php';

if($cbIndFinal || $cbIndElim)
	include 'Final/Individual/OrisRanking.php';

if($cbIndFinal)
{
	include 'Final/Individual/OrisBracket.php';
	$pdf->SetAutoPageBreak(true,OrisPDF::bottomMargin);
}

if($cbIndElim)
	include 'Elimination/OrisIndividual.php';

if($cbIndFinal)
	include 'Qualification/OrisIndividual.php';

if($cbTeamFinal)
{
	include 'Final/Team/OrisRanking.php';
	include 'Final/Team/OrisBracket.php';
	$pdf->SetAutoPageBreak(true,OrisPDF::bottomMargin);
	include 'Qualification/OrisTeam.php';
}

if(isset($_REQUEST['ToFitarco']))
{
	$Dest='D';
	if (isset($_REQUEST['Dest']))
		$Dest=$_REQUEST['Dest'];
	$pdf->Output($_REQUEST['ToFitarco'],$Dest);
}
else
	$pdf->Output();
?>