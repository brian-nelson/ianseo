<?php

/*
 * Since 2020 ORIS book hase the following sections
 *
 * - C24 Records
 * - C30 Number of Entries by NOC
 * - C32E Entries by NOC
 * - C35A Competition Officials (TODO:)
 * - C65B/C/D Scorecards
 * - C67 Official Communication (TODO:)
 * - C68 Sport Communication (TODO:)
 * - C73A Results RR
 * - C73B Results RR Team
 * - C73C Results RR Mixed Teams
 * - C75A/B Brackets Individual
 * - C75C Brackets (Team)
 * - C76A Final Rank Individual
 * - C76B Final Ranking Team
 * - C92A/B-C93 Medallists
 * - C95 Medal Standing
 *
 * */

require_once(dirname(dirname(__FILE__)) . '/config.php');
include_once('Common/pdf/OrisPDF.inc.php');
include_once('Common/pdf/OrisBracketPDF.inc.php');
include_once('Common/Fun_FormatText.inc.php');
include_once('Common/pdf/PdfChunkLoader.php');
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
$pdf->SetAutoPageBreak(true,OrisPDF::bottomMargin+$pdf->extraBottomMargin);

//Medaglieri
if($cbIndFinal || $cbTeamFinal) {
	include 'OrisMedalStanding.php';
	$pdf->SetAutoPageBreak(true,OrisPDF::bottomMargin+$pdf->extraBottomMargin);
	include 'OrisMedalList.php';
}

include 'Partecipants/OrisCountry.php';

if($cbIndFinal || $cbIndElim) {
    include 'Final/Individual/OrisRanking.php';
}

if($cbIndFinal) {
	include 'Final/Individual/OrisBracket.php';
	$BracketsInd = clone $PdfData;
	$pdf->SetAutoPageBreak(true,OrisPDF::bottomMargin+$pdf->extraBottomMargin);
}

if($cbIndElim) {
    include 'Elimination/OrisIndividual.php';
}

if($cbIndFinal) {
    include 'Qualification/OrisIndividual.php';
}

if($cbTeamFinal) {
	include 'Final/Team/OrisRanking.php';
	include 'Final/Team/OrisBracket.php';
	$BracketsTeam = clone $PdfData;
	$pdf->SetAutoPageBreak(true,OrisPDF::bottomMargin+$pdf->extraBottomMargin);
	include 'Qualification/OrisTeam.php';
	$pdf->endPage();
}

if($cbIndFinal) {
	if(empty($BracketsInd)) {
		$PdfData = getBracketsIndividual('',
			true,
			isset($_REQUEST["ShowTargetNo"]),
			isset($_REQUEST["ShowSchedule"]),
			true,
			true);

	} else {
		$PdfData = clone $BracketsInd;
	}

	//$pdf->setOrisCode('', '', true);
	$pdf->SetAutoPageBreak(true,(OrisPDF::bottomMargin+$pdf->extraBottomMargin));

	include(PdfChunkLoader('OrisScoreIndividual.inc.php'));
}
if($cbTeamFinal) {
	if(empty($BracketsTeam)) {
		$PdfData = getBracketsTeams('',
			true,
			isset($_REQUEST["ShowTargetNo"]),
			isset($_REQUEST["ShowSchedule"]),
			true,
			true);

	} else {
		$PdfData = clone $BracketsTeam;
	}

	//$pdf->setOrisCode('', '', true);
	$pdf->SetAutoPageBreak(true,(OrisPDF::bottomMargin+$pdf->extraBottomMargin));

	include(PdfChunkLoader('OrisScoreTeam.inc.php'));
}

$pdf->endPage();
$pdf->Records=array();

// add a new page for TOC
$pdf->setPrintPageNo(false);
$pdf->SetDataHeader(array(), array());
$pdf->setEvent('Complete Results Booklet');
$pdf->setComment('');
$pdf->setOrisCode('SUMMARY', 'Complete Results Booklet');
$pdf->setPhase('');

$pdf->addTOCPage();

// write the TOC title
$pdf->SetFont('times', 'B', 16);

// disable existing columns
$pdf->resetColumns();
// set columns
$pdf->setEqualColumns(2, ($pdf->getPageWidth()-25)/2);

$pdf->SetFont('freesans', '', 9.5);

// add a simple Table Of Content at first page
// (check the example n. 59 for the HTML version)
$pdf->addTOC(1, 'courier', '. ', 'INDEX', 'B');

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
