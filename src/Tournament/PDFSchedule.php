<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
include_once('Common/pdf/OrisPDF.inc.php');

$pdf = new OrisPDF(get_text('DailySchedule', 'Tournament'),false);

//Genero la query
$MyQuery = "SELECT f.FinEvent AS Event, "
	. " EvEventName AS EventDescr,"
	. " f.FinMatchNo,"
	. " EvFinalFirstPhase, "
	. " IF(GrPhase!=0,GrPhase,1) as Phase, "
	. " (GrPhase=1) as Finalina, "
	. " I1.IndRank as Rank, "
	. " Q1.QuScore, "
	. " E1.EnFirstName as FirstName, "
	. " E1.EnName as Name, "
	. " C1.CoCode as Country, "
	. " I2.IndRank OppRank, "
	. " Q2.QuScore OppQuScore, "
	. " E2.EnFirstName as OppFirstName, "
	. " E2.EnName as OppName, "
	. " C2.CoCode as OppCountry, "
	. " IF(EvMatchMode=0,f.FinScore,f.FinSetScore) AS Score, "
	. " f.FinTie,"
	. " f.FinTiebreak,"
	. " IF(EvMatchMode=0,f2.FinScore,f2.FinSetScore) as OppScore,"
	. " f2.FinTie as OppTie,"
	. " f.FinSetPoints as SetPoints, "
	. " GrPosition,"
	. " EvFinalPrintHead,"
	. " fs1.FSTarget,"
	. " fs2.FSTarget OppFSTarget,"
	. " DATE_FORMAT(fs1.FSScheduledDate,'" . get_text('DateFmtDB') . "') as ScheduledDate,"
	. " DATE_FORMAT(fs1.FSScheduledTime,'" . get_text('TimeFmt') . "') AS ScheduledTime "
	. "FROM Finals AS f "
	. "INNER JOIN Finals AS f2 ON f.FinEvent=f2.FinEvent AND f.FinMatchNo=f2.FinMatchNo-1 AND f.FinTournament=f2.FinTournament "
	. "INNER JOIN Events ON f.FinEvent=EvCode AND f.FinTournament=EvTournament AND EvTeamEvent=0 "
	. "INNER JOIN Grids ON f.FinMatchNo=GrMatchNo "
	. "INNER JOIN Entries E1 ON f.FinAthlete=E1.EnId AND f.FinTournament=E1.EnTournament "
	. "INNER JOIN Qualifications Q1 ON E1.EnId=Q1.QuId  "
	. "INNER JOIN Individuals I1 ON I1.IndId=f.FinAthlete AND I1.IndEvent=f.FinEvent AND I1.IndTournament=f.FinTournament  "
	. "INNER JOIN Countries C1 ON E1.EnCountry=C1.CoId AND E1.EnTournament=C1.CoTournament "
	. "INNER JOIN Entries E2 ON f2.FinAthlete=E2.EnId AND f2.FinTournament=E2.EnTournament "
	. "INNER JOIN Qualifications Q2 ON E2.EnId=Q2.QuId  "
	. "INNER JOIN Individuals I2 ON I2.IndId=f2.FinAthlete AND I2.IndEvent=f2.FinEvent AND I2.IndTournament=f2.FinTournament  "
	. "INNER JOIN Countries C2  ON E2.EnCountry=C2.CoId AND E2.EnTournament=C2.CoTournament "
	. "INNER JOIN FinSchedule fs1 ON f.FinEvent=fs1.FSEvent AND f.FinMatchNo=fs1.FSMatchNo AND f.FinTournament=fs1.FSTournament AND fs1.FSTeamEvent='0' "
	. "INNER JOIN FinSchedule fs2 ON f.FinEvent=fs2.FSEvent AND f.FinMatchNo=fs2.FSMatchNo AND f.FinTournament=fs2.FSTournament AND fs2.FSTeamEvent='0' "
	. "WHERE f.FinTournament = " . StrSafe_DB($_SESSION['TourId']) . " "
	. " AND f.FinMatchNo%2 = 0 ";
if(!empty($_REQUEST['Event'])) $MyQuery .= CleanEvents($_REQUEST['Event'], 'f.FinEvent');
$MyQuery .= "ORDER BY fs1.FSScheduledDate, fs1.FSScheduledTime, EvProgr ASC, EvCode, Phase DESC, f.FinMatchNo ASC ";

//$MyQuery = "($MyQuery) union (";


$Rs = safe_r_sql($MyQuery);

$pdf->SetMargins(10, 35, 10);

$pdf->AddPage();

$W=$pdf->getPageWidth();

$W=($W-100-20)/2;

$pdf->SetLineWidth(0.1);
$pdf->SetFontSize(15);
$pdf->SetFont('','b');

$pdf->Cell($pdf->getPageWidth()-20, 5, get_text('DailySchedule', 'Tournament'), 0, 1, 'C');

$pdf->sety($pdf->gety()+5);
$pdf->SetFontSize(10);

$oldDate='';
$oldTime='';
$oldEvent='';
$oldPhase='';
$Date='';
$Time='';
$Event='';
$Phase='';
$header=true;

while($r=safe_fetch($Rs)) {
	if($r->ScheduledDate!=$oldDate){
		$oldDate=$r->ScheduledDate;
		$DateBorder='LTR';
		$oldTime='';
		$oldEvent='';
		$oldPhase='';
		$Date=$r->ScheduledDate;
		$Time='';
		$Event='';
		$Phase='';
	}
	if($r->ScheduledTime!=$oldTime){
		$oldTime=$r->ScheduledTime;
		$TimeBorder='LTR';
		$oldEvent='';
		$oldPhase='';
		$Time=$r->ScheduledTime;
		$Event='';
		$Phase='';
	}
	if($r->EventDescr!=$oldEvent){
		$oldEvent=$r->EventDescr;
		$EventBorder='LTR';
		$oldPhase='';
		$Event=$r->EventDescr;
		$Phase='';
	}
	if($r->Phase!=$oldPhase){
		$oldPhase=$r->Phase;
		$PhaseBorder='LTR';
		$Phase=get_text((($oldPhase==1 && !$r->Finalina) ? 'MedalGold' : $oldPhase . '_Phase'));
	}
	if($header) {
		$pdf->SetFont('','b');
		$pdf->Cell(13, 6, get_text('Date', 'Tournament'), 1, 0, 'L');
		$pdf->Cell(8, 6, get_text('Time', 'Tournament'), 1, 0, 'L');
		$pdf->Cell(25, 6, get_text('Event'), 1, 0, 'L');
		$pdf->Cell(6, 6, get_text('Phase'), 1, 0, 'L');
		$pdf->Cell(6, 6, get_text('MatchNo'), 1, 0, 'L');
		//$pdf->Cell(3, 4, get_text('Court', 'Tournament'), 1, 0, 'L');
		$pdf->Cell(5, 6, get_text('Rank'), 1, 0, 'L');
		$pdf->Cell($W+10, 6, get_text('ParticipantSchedule', 'Tournament', 1), 1, 0, 'L');
		$pdf->Cell(6, 6, get_text('TargetShort', 'Tournament'), 1, 0, 'L');
		$pdf->Cell(5, 6, get_text('Rank'), 1, 0, 'L');
		$pdf->Cell($W+10, 6, get_text('ParticipantSchedule', 'Tournament', 2), 1, 0, 'L');
		$pdf->Cell(6, 6, get_text('TargetShort', 'Tournament'), 1, 1, 'L');
		$pdf->SetFont('','');
		$header=false;
	}

	if(!$pdf->SamePage(8)) {
		$DateBorder.='B';
		$TimeBorder.='B';
		$EventBorder.='B';
		$PhaseBorder.='B';
		$header=true;
	}
	$pdf->Cell(13, 5, $Date, $DateBorder, 0, 'L');
	$pdf->Cell(8,  5, $Time, $TimeBorder, 0, 'L');
	$pdf->Cell(25, 5, $Event, $EventBorder, 0, 'L');
	$pdf->Cell(6,  5, $Phase, $PhaseBorder, 0, 'C');
	$pdf->Cell(6,  5, $r->FinMatchNo/2 + 1, 1, 0, 'R');
//	$pdf->Cell(3,  4, '', 1, 0, 'L');
	$pdf->Cell(5,  5, $r->Rank, 1, 0, 'L');
	$pdf->Cell($W, 5, strtoupper($r->FirstName) . ' '.$r->Name, 1, 0, 'L');
	$pdf->Cell(10, 5, $r->Country, 1, 0, 'L');
	$pdf->Cell(6,  5, $r->FSTarget*1, 1, 0, 'L');
	$pdf->Cell(5,  5, $r->OppRank, 1, 0, 'L');
	$pdf->Cell($W, 5, strtoupper($r->OppFirstName) . ' '.$r->OppName, 1, 0, 'L');
	$pdf->Cell(10, 5, $r->OppCountry, 1, 0, 'L');
	$pdf->Cell(6,  5, $r->OppFSTarget+1, 1, 1, 'L');
	$DateBorder='LR';
	$TimeBorder='LR';
	$EventBorder='LR';
	$PhaseBorder='LR';
	$Date='';
	$Time='';
	$Event='';
	$Phase='';
}


$pdf->Output();

?>