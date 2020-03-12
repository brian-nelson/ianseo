<?php
/*****************
 *
 * LEGGERE NOTA PIU' SOTTO RIGUARDO ALLA VISIBILITA' DELLE FRECCE (riga 163)
 *
 * */

// Directory dove saranno inserite le varie foto
$fotodir='http://' . $_SERVER['HTTP_HOST'] . $CFG->ROOT_DIR . '/TV/Photos/' . $TourCodeSafe . '-%s-%s.jpg';

//Genero la Query dei Nomi
$MyQueryNames  = "SELECT EnFirstName, EnName, EnId "
	. "FROM TeamFinComponent "
	. "INNER JOIN Entries ON TfcId=EnId AND TfcTournament=EnTournament "
	. "INNER JOIN Teams ON TfcCoId=TeCoId AND TfcSubTeam=TeSubTeam AND TfcEvent=TeEvent AND TfcTournament=TeTournament AND TeFinEvent=1 "
	. "WHERE TfcTournament = $TourId "
	. " AND TfcEvent='%s' AND TfcCoId='%s' AND TfcSubTeam='%s' "
	. "ORDER BY TfcOrder";

//Genero la query Degli eventi
$MyQuery = "SELECT "
	// squadra sx
	//. " f.TfTeam,"
	//. " f.TfSubTeam,"
	. " t1.TeRank,"
	. " t1.TeScore, "
	. " c1.CoId, "
	. " CONCAT(c1.CoName, IF(f.TfSubTeam>'1',CONCAT(' (',f.TfSubTeam,')'),'')) as Team,"
	. " c1.CoCode ,"
	. " f.TfSubTeam SubTeam, "
	. " f.TfScore Score,"
	. " f.TfTie Tie,"
	. " fs1.FsTarget Target, "
	. " f.TfTieBreak TieBreak,"
	. " f.TfArrowstring ArrowString, "

	// squadra dx
	. " t2.TeRank OppTeRank,"
	. " t2.TeScore OppTeScore, "
	. " c2.CoId OppCoId, "
	. " CONCAT(c2.CoName, IF(f2.TfSubTeam>'1',CONCAT(' (',f2.TfSubTeam,')'),'')) as OppTeam,"
	. " c2.CoCode as OppCoCode,"
	. " f2.TfSubTeam OppSubTeam, "
	. " f2.TfScore as OppScore,"
	. " f2.TfTie as OppTie, "
	. " fs2.FsTarget OppTarget, "
	. " f2.TfTieBreak OppTieBreak,"
	. " f2.TfArrowstring OppArrowString, "

	// generico
	. " GrPhase as Phase,"
	. " EvMixedTeam, "
//	. " EvMaxTeamPerson, "
	. " EvEventName AS EventDescr,"
	. " EvFinalFirstPhase,"
	. " f.TfEvent AS Event,"
	. " f.TfMatchNo, "
	. " EvFinalPrintHead, "
	. " IFNULL(NComponenti,0) AS NumComponenti "

	. "FROM TeamFinals as f "
	. " INNER JOIN TeamFinals AS f2 ON f.TfEvent=f2.TfEvent AND f.TfMatchNo=IF((f.TfMatchNo % 2)=0,f2.TfMatchNo-1,f2.TfMatchNo+1) AND f.TfTournament=f2.TfTournament "
	. " INNER JOIN Events ON f.TfEvent=EvCode AND f.TfTournament=EvTournament AND EvTeamEvent=1 "
	. " INNER JOIN Grids ON f.TfMatchNo=GrMatchNo "
	. " LEFT JOIN Teams t1 ON f.TfTeam=t1.TeCoId AND f.TfSubTeam=t1.TeSubTeam AND f.TfEvent=t1.TeEvent AND f.TfTournament=t1.TeTournament AND t1.TeFinEvent=1 "
	. " LEFT JOIN Teams t2 ON f2.TfTeam=t2.TeCoId AND f2.TfSubTeam=t2.TeSubTeam AND f2.TfEvent=t2.TeEvent AND f2.TfTournament=t2.TeTournament AND t2.TeFinEvent=1 "
	. " LEFT JOIN Countries c1 ON f.TfTeam=c1.CoId AND f.TfTournament=c1.CoTournament "
	. " LEFT JOIN Countries c2 ON f2.TfTeam=c2.CoId AND f2.TfTournament=c2.CoTournament "
	. " LEFT JOIN FinSchedule fs1 on fs1.FSEvent=f.TfEvent and fs1.FSTeamEvent='1' and fs1.FSMatchNo=f.TfMatchNo and f.TfTournament=fs1.FSTournament "
	. " LEFT JOIN FinSchedule fs2 on fs2.FSEvent=f2.TfEvent and fs2.FSTeamEvent='1' and fs2.FSMatchNo=f2.TfMatchNo and f2.TfTournament=fs2.FSTournament "
	. " LEFT JOIN (SELECT TfcEvent AS Evento, Max(Quanti) AS NComponenti FROM ( "
	. "  SELECT TfcEvent, Count( * ) AS Quanti FROM TeamFinComponent WHERE TfcTournament = $TourId "
	. "  GROUP BY TfcEvent, TfcCoId, TfcSubTeam) AS Ssqy  GROUP BY TfcEvent) as Sqy ON f.TfEvent=Evento "
	. "WHERE f.TfTournament = $TourId  AND (f.TfMatchNo % 2)=0"
	. " AND f.TfLive='1' ";

$q=safe_r_sql($MyQuery);
$r=safe_fetch($q);

$NumArrows=6;	// Numero di frecce
if($r->EvMixedTeam) $NumArrows=4;

$XmlDoc = new DOMDocument('1.0', 'UTF-8');
$XmlRoot = $XmlDoc->createElement('archeryscores');
$XmlDoc->appendChild($XmlRoot);

//$Header = $XmlDoc->createElement('header', get_text($r->Event, null, null, true) . ' - ' . get_text('TeamFinEvent', 'Tournament') . ' - ' . get_text($r->Phase.'_Phase'));
$Header = $XmlDoc->createElement('header', get_text($r->EventDescr, null, null, true));
$XmlRoot->appendChild($Header);

if($ERROR_REPORT) {
	$Games = $XmlDoc->createElement('query', $MyQuery);
	$XmlRoot->appendChild($Games);
}

$Games = $XmlDoc->createElement('games');
$XmlRoot->appendChild($Games);

$Game = $XmlDoc->createElement('game');
$Games->appendChild($Game);

// create opponent 1
$Opp1 = $XmlDoc->createElement('opponent1');
$Game->appendChild($Opp1);

// create opponent 2
$Opp2 = $XmlDoc->createElement('opponent2');
$Game->appendChild($Opp2);

// targetno
$Tg = $XmlDoc->createElement('targetno', ltrim($r->Target,'0'));
$Opp1->appendChild($Tg);
$Tg = $XmlDoc->createElement('targetno', ltrim($r->OppTarget,'0'));
$Opp2->appendChild($Tg);

// name: Athlete name?
$Tg = $XmlDoc->createElement('name', $r->Team);
$Opp1->appendChild($Tg);
$Tg = $XmlDoc->createElement('name', $r->OppTeam);
$Opp2->appendChild($Tg);

// shortname
$Tg = $XmlDoc->createElement('shortname', $r->CoCode);
$Opp1->appendChild($Tg);
$Tg = $XmlDoc->createElement('shortname', $r->OppCoCode);
$Opp2->appendChild($Tg);

// First Team
$n=0;
$t=safe_r_sql(sprintf($MyQueryNames, $r->Event, $r->CoId, $r->SubTeam));
while($u=safe_fetch($t)) {
	$n++;
	$Tg = $XmlDoc->createElement('component'.$n, strtoupper($u->EnFirstName) . ' ' . $u->EnName);
	$Opp1->appendChild($Tg);

	$Tg = $XmlDoc->createElement('photo'.$n, file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-En-'.$u->EnId.'.jpg')?sprintf($fotodir, 'En', $u->EnId):'');
	$Opp1->appendChild($Tg);
}
while($n++ < 3) {
	$Tg = $XmlDoc->createElement('component'.$n, '');
	$Opp1->appendChild($Tg);

	$Tg = $XmlDoc->createElement('photo'.$n, '');
	$Opp1->appendChild($Tg);
}

// Second Team
$n=0;
$t=safe_r_sql(sprintf($MyQueryNames, $r->Event, $r->OppCoId, $r->OppSubTeam));
while($u=safe_fetch($t)) {
	$n++;
	$Tg = $XmlDoc->createElement('component'.$n, strtoupper($u->EnFirstName) . ' ' . $u->EnName);
	$Opp2->appendChild($Tg);

	$Tg = $XmlDoc->createElement('photo'.$n, file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-En-'.$u->EnId.'.jpg')?sprintf($fotodir, 'En', $u->EnId):'');
	$Opp2->appendChild($Tg);
}
while($n++ < 3) {
	$Tg = $XmlDoc->createElement('component'.$n, '');
	$Opp2->appendChild($Tg);

	$Tg = $XmlDoc->createElement('photo'.$n, '');
	$Opp2->appendChild($Tg);
}

// get the single ends => set
$Arrows='';
$OppArrows='';
for($n=0; $n<5; $n++) {
	$End=substr($r->ArrowString, $n*$NumArrows, $NumArrows);
	if(trim($End)) {
		$Arrows=$End;
		$OppArrows='';
	}
	$tot=ValutaArrowString($End) . ($End!=strtoupper($End) ? '*' : '');
	$Tg = $XmlDoc->createElement('set'.($n+1), $tot ? $tot : '');
	$Opp1->appendChild($Tg);

	$OppEnd=substr($r->OppArrowString, $n*$NumArrows, $NumArrows);
	if(trim($OppEnd)) {
		$Arrows=$End;
		$OppArrows=$OppEnd;
	}
	$tot=ValutaArrowString($OppEnd) . ($OppEnd!=strtoupper($OppEnd) ? '*' : '');
	$Tg = $XmlDoc->createElement('set'.($n+1), $tot ? $tot : '');
	$Opp2->appendChild($Tg);
}

// tie
$T1='';
$T2='';
if(trim($r->TieBreak) or trim($r->OppTieBreak)) {
	// le singole frecce del tiebrak vanno in arrow
	$Arrows=$r->TieBreak;
	$OppArrows=$r->OppTieBreak;
	$tie=max(strlen(rtrim($r->TieBreak)), strlen(rtrim($r->OppTieBreak)));

	for($n=0; $n<$tie; $n+=$r->NumComponenti) {
		$End=trim(substr($r->TieBreak, $n, $r->NumComponenti));
		$OppEnd=trim(substr($r->OppTieBreak, $n, $r->NumComponenti));
		if($End or $OppEnd) {
			$T1=$End;
			$T2=$OppEnd;
		}
	}
	$T1=(!empty($T1)?ValutaArrowString($T1):'');
	$T2=(!empty($T2)?ValutaArrowString($T2):'');
}

$Tg = $XmlDoc->createElement('tie', $T1);
$Opp1->appendChild($Tg);

$Tg = $XmlDoc->createElement('tie', $T2);
$Opp2->appendChild($Tg);

// IN QUESTO PUNTO VA INSERITO L'EVENTUALE AZZERAMENTO DELLE FRECCE DI VOLEE
// BASATO SULLA DISTANZA NEL TEMPO DI FinDateTime
if(false and $r->TooOld) {
	$Arrows='';
	$OppArrows='';
}

// setpoints
// setpoints/setscore: se setscore è il punteggio totale degli scontri (es. 4 - 0),
//     setpoints è il totale freccie del set in corso ( 54 - 51)
//		se invece siamo a far vedere i ties, allora fa vedere l'ultima freccia di spareggio
if($T1 or $T2) {
	$tot1=$T1;
	$tot2=$T2;
} else {
	$tot1=ValutaArrowString($Arrows) . ($Arrows!=strtoupper($Arrows) ? '*' : '');
	$tot2=ValutaArrowString($OppArrows) . ($OppArrows!=strtoupper($OppArrows) ? '*' : '');
}
$Tg = $XmlDoc->createElement('setpoints', $tot1);
$Opp1->appendChild($Tg);
$Tg = $XmlDoc->createElement('setpoints', $tot2);
$Opp2->appendChild($Tg);

// arrows
for($n=0; $n<6; $n++) {
	$End=substr($Arrows, $n, 1);
	$tot=(!empty($Target[$End]['R'])?$Target[$End]['R']:'');
	$Tg = $XmlDoc->createElement('arrow'.($n+1), $tot);
	$Opp1->appendChild($Tg);

	$End=substr($OppArrows, $n, 1);
	$tot=(!empty($Target[$End]['R'])?$Target[$End]['R']:'');
	$Tg = $XmlDoc->createElement('arrow'.($n+1), $tot);
	$Opp2->appendChild($Tg);
}

// total
$Tg = $XmlDoc->createElement('total', $r->Score);
$Opp1->appendChild($Tg);
$Tg = $XmlDoc->createElement('total', $r->OppScore);
$Opp2->appendChild($Tg);

// setscore
$Tg = $XmlDoc->createElement('setscore', $r->Score);
$Opp1->appendChild($Tg);
$Tg = $XmlDoc->createElement('setscore', $r->OppScore);
$Opp2->appendChild($Tg);

// flag
$Tg = $XmlDoc->createElement('flag', file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$r->CoCode.'.jpg')?sprintf($fotodir, 'Fl', $r->CoCode):'');
$Opp1->appendChild($Tg);
$Tg = $XmlDoc->createElement('flag', file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$r->OppCoCode.'.jpg')?sprintf($fotodir, 'Fl', $r->OppCoCode):'');
$Opp2->appendChild($Tg);

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);
echo $XmlDoc->SaveXML();

die();

?>