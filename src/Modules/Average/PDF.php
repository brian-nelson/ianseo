<?php

/*
 *
 * Average score throughout the whole competition
 * // used by FITARCO 2017
 *
 * */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/pdf/IanseoPdf.php');

if(!CheckTourSession()) {
	print get_text('CrackError');
	exit;
}
checkACL(AclModules, AclReadOnly);

// Get Archers and Bonusses
list($Archers, $Bonus, $Coeffs)=json_decode(file_get_contents('./conf.php'), true);

// get qualification Rank for all partecipants
$Rank1=array();
$Rank2=array();
$Rank3=array();
$ArRank=array();

$Data=array();
$FinalRank=array();
$EventNames=array();
$ToGolds='';
$ToXNine='';
$Sql="select ToGolds, ToXNine, '' Average, '' AverageRank, EvEventName, EnId, Encode, EnFirstName, EnName, IndEvent, CoCode, CoName, QuScore, QuGold, QuXnine, IndRank, IndRankFinal, 
		group_concat(concat(trim(FinArrowstring),trim(FinTiebreak)) SEPARATOR '') as FinArrows, 0 as Average
	from Entries 
	inner join Qualifications on EnId=QuId 
	inner join Countries on CoId=EnCountry and CoTournament=EnTournament
	inner join Individuals on EnId=IndId and IndTournament=EnTournament
	inner join Tournament on ToId=EnTournament
	left join Events on EvCode=IndEvent
	left join Finals on FinAthlete=EnId and FinTournament=EnTournament
	where EnTournament={$_SESSION['TourId']} and EnCode in ('".implode("','", $Archers)."')
	group by EnCode
	order by IndEvent
	";

// Calculates first Ranking list
$q=safe_r_SQL($Sql.', IndRank');

$cnt=0;
$pos=0;
$OldPos=0;
$OldEvent='';
while($r=safe_fetch($q)) {
	$ToGolds=$r->ToGolds;
	$ToXNine=$r->ToXNine;
	$EventNames[$r->IndEvent]=$r->EvEventName;
	if($OldEvent!=$r->IndEvent) {
		$cnt=0;
		$pos=0;
		$OldScore=0;
		$OldGolds=0;
		$OldXNine=0;
	}
	$cnt++;
	if($OldPos!=$r->IndRank) {
		$pos=$cnt;
	}

	$Rank1[$r->IndEvent][$r->EnId]=$pos;

	$FinalRank[$r->IndEvent][$r->EnId] =  (empty($Bonus[$r->Encode]) ? 0 : $Bonus[$r->Encode]);

	if($arrows=strlen(trim($r->FinArrows))) {
		// has matches
		$value=ValutaArrowString($r->FinArrows);
		$r->Average=$value/$arrows;
		$ArRank[$r->IndEvent][$r->EnId]=$r->Average;
	//} else {
		// has no matches... adds qualification position position
		//$FinalRank[$r->IndEvent][$r->EnId] += ($Coeffs['arrow']*$r->IndRank);
	}
	$Data[$r->EnId]=$r;

	$OldPos=$r->IndRank;
	$OldEvent=$r->IndEvent;
}


// calculate the arrow average for matches
foreach($ArRank as $Event => $Entries) {
	$pos=0;
	$cnt=0;
	$oldAvg=0;
	arsort($Entries);
	foreach($Entries as $EnId => $avg) {
		$cnt++;
		if($oldAvg!=$avg) {
			$pos=$cnt;
		}
		$Rank2[$Event][$EnId]=$pos;
		$oldAvg=$avg;
	}
}

// Calculates Final Ranking list
$OldEvent='';
$q=safe_r_SQL($Sql.', IndRankFinal');
while($r=safe_fetch($q)) {
	if($OldEvent!=$r->IndEvent) {
		$cnt=0;
		$pos=0;
		$OldPos=0;
	}
	$cnt++;
	if($OldPos!=$r->IndRankFinal) {
		$pos=$cnt;
	}

	$Rank3[$r->IndEvent][$r->EnId]=$pos;
	$OldPos=$r->IndRankFinal;
	$OldEvent=$r->IndEvent;
}

// Calculates the points
foreach ($Rank1 as $Event => $Entries) {
	foreach($Entries as $EnId => $rank) {
		$FinalRank[$Event][$EnId] += $Coeffs['qual']*$rank;
		if(!empty($Rank2[$Event][$EnId])) {
			$FinalRank[$Event][$EnId] += ($Coeffs['arrow']*$Rank2[$Event][$EnId]);
		}
		$FinalRank[$Event][$EnId] += ($Coeffs['rank']*$Rank3[$Event][$EnId]);
	}
}

// make the final rank!
$pdf=new IanseoPdf('Average Rank');
$pdf->startPageGroup();
foreach($FinalRank as $Event => $Entries) {
	$pdf->AddPage();
	$pdf->ln(10);
	$pdf->SetFont('', 'b', 16);
	$pdf->cell(0, 0, $EventNames[$Event], '', 0, 'C');
	$pdf->ln(10);

	$pdf->SetFont('', 'b', 10);
	$pdf->cell(82, 0, '');
	$pdf->cell(27, 0, get_text('MenuLM_Qualification'), '', 0, 'C');
	$pdf->Cell(5, 0, '');
	$pdf->cell(32, 0, get_text('Arrows', 'Tournament'), '', 0, 'C');
	$pdf->Cell(5, 0, '');
	$pdf->cell(17, 0, get_text('Finals', 'Tournament'), '', 0, 'C');
	$pdf->Cell(5, 0, '');
	$pdf->cell(10, 0, get_text('Bonus', 'Tournament'));
	$pdf->ln();

	$pdf->cell(7, 0, get_text('PositionShort'));
	$pdf->cell(10, 0, get_text('Points', 'Tournament'));
	$pdf->cell(30, 0, get_text('FamilyName', 'Tournament'));
	$pdf->cell(30, 0, get_text('Name', 'Tournament'));
	$pdf->Cell(5, 0, '');
	$pdf->cell(10, 0, get_text('Total'), '', 0, 'C');
	$pdf->cell(7, 0, get_text('PositionShort'), '', 0, 'R');
	$pdf->cell(10, 0, get_text('Points', 'Tournament'));
	$pdf->Cell(5, 0, '');
	$pdf->cell(15, 0, get_text('ArrowAverage'), '', 0, 'C');
	$pdf->cell(7, 0, get_text('PositionShort'), '', 0, 'R');
	$pdf->cell(10, 0, get_text('Points', 'Tournament'));
	$pdf->Cell(5, 0, '');
	$pdf->cell(7, 0, get_text('PositionShort'), '', 0, 'R');
	$pdf->cell(10, 0, get_text('Points', 'Tournament'));
	$pdf->ln();

	$pdf->SetFont('', '', 10);
	$pos=0;
	$cnt=0;
	$oldAvg=0;
	asort($Entries);
	foreach($Entries as $EnId => $avg) {
		$cnt++;
		if($oldAvg!=$avg) {
			$pos=$cnt;
		}
		$oldAvg=$avg;

		$pdf->Cell(7, 0, $pos, '', 0, 'R');
		$pdf->Cell(10, 0, number_format($avg, 1), '', 0, 'R');
		$pdf->Cell(30, 0, $Data[$EnId]->EnFirstName);
		$pdf->Cell(30, 0, $Data[$EnId]->EnName);
		$pdf->Cell(5, 0, '');
		$pdf->Cell(10, 0, $Data[$EnId]->QuScore, '', 0, 'R');
		$pdf->Cell(7, 0, $Rank1[$Event][$EnId], '', 0, 'R');
		$pdf->Cell(10, 0, number_format($Rank1[$Event][$EnId]*$Coeffs['qual'], 1), '', 0, 'R');
		$pdf->Cell(5, 0, '');
		$pdf->Cell(15, 0, empty($Data[$EnId]->Average) ? '' : number_format($Data[$EnId]->Average, 5), '', 0, 'R');
		$pdf->Cell(7, 0, empty($Rank2[$Event][$EnId]) ? '' : $Rank2[$Event][$EnId], '', 0, 'R');
		$pdf->Cell(10, 0, empty($Rank2[$Event][$EnId]) ? '' : number_format($Rank2[$Event][$EnId]*$Coeffs['arrow'], 1), '', 0, 'R');
		$pdf->Cell(5, 0, '');
		$pdf->Cell(7, 0, $Rank3[$Event][$EnId], '', 0, 'R');
		$pdf->Cell(10, 0, number_format($Rank3[$Event][$EnId]*$Coeffs['rank'], 1), '', 0, 'R');
		$pdf->Cell(5, 0, '');
		$pdf->Cell(10, 0, empty($Bonus[$Data[$EnId]->Encode]) ? '' : number_format($Bonus[$r->Encode], 2), '', 0, 'R');

		$pdf->ln();
	}
}
$pdf->Output();