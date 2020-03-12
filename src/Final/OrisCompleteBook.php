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

checkACL(array(AclIndividuals, AclTeams), AclReadOnly);
$isCompleteResultBook = true;

$pdf = new OrisBracketPDF('', 'Complete Result Book');
$pdf->startPageGroup();
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

$pdf->startPageGroup();
// add a new page for TOC

$pdf->setPrintFooter(false);
$pdf->SetDataHeader(array(), array());
$pdf->setEvent('Summary');
$pdf->setComment('');
$pdf->setOrisCode('', 'Summary');
$pdf->setPhase('');

$pdf->addTOCPage();

// write the TOC title
$pdf->SetFont('times', 'B', 16);
// $pdf->MultiCell(0, 0, 'Index', 0, 'C', 0, 1, '', '', true, 0);
// 			$pdf->Ln();

// disable existing columns
$pdf->resetColumns();
// set columns
$pdf->setEqualColumns(2, ($pdf->getPageWidth()-25)/2);

$pdf->SetFont('freesans', '', 9.5);

// add a simple Table Of Content at first page
// (check the example n. 59 for the HTML version)
$pdf->addTOC(1, 'courier', '.', 'INDEX', 'B');

// end of TOC page
$pdf->endTOCPage();


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