<?php
/*****************
 *
 * LEGGERE NOTA PIU' SOTTO RIGUARDO ALLA VISIBILITA' DELLE FRECCE (riga 163)
 *
 *
 *
 * Aggiunta per il calcolo della freccia X to win
 *
 * aggiunti 4 campi XML:
 * - xtowin: valore della freccia (e quindi della zona da non scurire). Se 1000 ignorare
 * - totieset: indica se il valore indicato è per vincere o pareggiare il set
 * - towinmatch: indica se il valore indicato è per vincere il match
 * - totiematch: indica se il valore indicato è per pareggiare il match
 *
 * */

$SQL="SELECT "
	. " f.FinEvent Event, "
	. " EvMatchArrowsNo, EvMixedTeam, EvTeamEvent, EvEventName, "
	. " GrPhase Phase, "
	. " f.FinVxF,"
	. " IF(f.FinDateTime>=f2.FinDateTime, f.FinDateTime, f2.FinDateTime) AS DateTime,"
	. " TIMESTAMPDIFF(SECOND, IF(f.FinDateTime>=f2.FinDateTime, f.FinDateTime, f2.FinDateTime), '".date('Y-m-d H:i:s')."')>90  AS TooOld,"
	. " f.FinMatchNo as MatchNo,"
	. " fs1.FsTarget Target, "
	. " fs2.FsTarget OppTarget, "
	. " f2.FinMatchNo as OppMatchNo,"
	. " EvMatchMode!=0 as SetMode, "
	// left side athlete
	. " e1.EnId,"
	. " e1.EnFirstName AS AthleteFirstName,"
	. " e1.EnName as AthleteName,"
	. " e1.EnCode as AthleteBib,"
	. " c1.CoCode as CoCode, "
	. " c1.CoName as CoName, "
	. " IF(EvMatchMode=0,f.FinScore,f.FinSetScore) AS Score,"
	. " f.FinSetPoints SetPoints,"
	. " f.FinTie as Tie,"
	. " f.FinTieBreak as TieBreak,"
	. " f.FinArrowString as ArrowString, "
	// right side athelete
	. " e2.EnId OppEnId,"
	. " e2.EnFirstName AS OpponentFirstName,"
	. " e2.EnName as OpponentName,"
	. " e2.EnCode as OpponentBib,"
	. " c2.CoCode as OppCoCode, "
	. " c2.CoName as OppCoName, "
	. " IF(EvMatchMode=0,f2.FinScore,f2.FinSetScore) as OppScore,"
	. " f2.FinSetPoints OppSetPoints,"
	. " f2.FinTie as OppTie,"
	. " f2.FinTieBreak as OppTieBreak,"
	. " f2.FinArrowString as OppArrowString "
	. "FROM Finals AS f "
	. "INNER JOIN Finals AS f2 ON f.FinEvent=f2.FinEvent AND f.FinMatchNo=f2.FinMatchNo-1 AND f.FinTournament=f2.FinTournament "
	. "LEFT JOIN Entries e1 on f.FinAthlete=e1.EnId "
	. "LEFT JOIN Entries e2 on f2.FinAthlete=e2.EnId "
	. "LEFT JOIN Countries c1 on e1.EnCountry=c1.CoId "
	. "LEFT JOIN Countries c2 on e2.EnCountry=c2.CoId "
	. "LEFT JOIN FinSchedule fs1 on fs1.FSEvent=f.FinEvent and fs1.FSTeamEvent='0' and fs1.FSMatchNo=f.FinMatchNo and f.FinTournament=fs1.FSTournament "
	. "LEFT JOIN FinSchedule fs2 on fs2.FSEvent=f2.FinEvent and fs2.FSTeamEvent='0' and fs2.FSMatchNo=f2.FinMatchNo and f2.FinTournament=fs2.FSTournament "
	. "INNER JOIN Events ON f.FinEvent=EvCode AND f.FinTournament=EvTournament AND EvTeamEvent=0 "
	. "INNER JOIN Grids ON f.FinMatchNo=GrMatchNo "
	. "WHERE f.FinTournament=$TourId AND (f.FinMatchNo % 2)=0"
	. " AND f.FinLive='1' ";

$q=safe_r_sql($SQL);
$r=safe_fetch($q);

$NumArrows=3;				// Numero di frecce
if(($r->EvMatchArrowsNo & ($r->Phase>0 ? 2*bitwisePhaseId($r->Phase):1)) !== 0) {
	$NumArrows=6;	// Numero di frecce
}
//			if($r->EvMixedTeam) $nARR='2x2';
//			elseif($r->EvTeamEvent) $nARR='3x2';

//$Target=${GetTargetType($r->Event, 0, $TourId)};

$XmlDoc = new DOMDocument('1.0', 'UTF-8');
$XmlRoot = $XmlDoc->createElement('archeryscores');
$XmlDoc->appendChild($XmlRoot);

//$Header = $XmlDoc->createElement('header', get_text($r->Event, null, null, true) . ' - ' . get_text('IndFinEvent', 'Tournament') . ' - ' . get_text($r->Phase.'_Phase'));
$Header = $XmlDoc->createElement('header');
$Header->appendChild($XmlDoc->createCDATASection(get_text($r->EvEventName, null, null, true)));
$XmlRoot->appendChild($Header);

//if($ERROR_REPORT) {
//	$Games = $XmlDoc->createElement('query', $SQL);
//	$XmlRoot->appendChild($Games);
//}

//$Games = $XmlDoc->createElement('games');
//$XmlRoot->appendChild($Games);

$Game = $XmlDoc->createElement('game');
$Games->appendChild($Game);
$XmlRoot->appendChild($Game);
//$Games->appendChild($XmlDoc->createElement('game'));

// create opponent 1
$Opp1 = $XmlDoc->createElement('opponent1');
$Game->appendChild($Opp1);

// create opponent 2
$Opp2 = $XmlDoc->createElement('opponent2');
$Game->appendChild($Opp2);

// targetno
$Tg = $XmlDoc->createElement('targetno');
$Tg->appendChild($XmlDoc->createCDATASection(ltrim($r->Target,'0').($r->Target==$r->OppTarget ? ' A':'')));
$Opp1->appendChild($Tg);

$Tg = $XmlDoc->createElement('targetno');
$Tg->appendChild($XmlDoc->createCDATASection( ltrim($r->OppTarget,'0').($r->Target==$r->OppTarget ? ' B':'')));
$Opp2->appendChild($Tg);

// name: Athlete name?
$Tg = $XmlDoc->createElement('name');
$Tg->appendChild($XmlDoc->createCDATASection(strtoupper($r->AthleteFirstName) . ' ' . $r->AthleteName));
$Opp1->appendChild($Tg);
$Tg = $XmlDoc->createElement('name');
$Tg->appendChild($XmlDoc->createCDATASection(strtoupper($r->OpponentFirstName) . ' ' . $r->OpponentName));
$Opp2->appendChild($Tg);

// shortname
$Tg = $XmlDoc->createElement('shortname');
$Tg->appendChild($XmlDoc->createCDATASection($r->CoCode));
$Opp1->appendChild($Tg);
$Tg = $XmlDoc->createElement('shortname');
$Tg->appendChild($XmlDoc->createCDATASection($r->OppCoCode));
$Opp2->appendChild($Tg);

// component1
$Tg = $XmlDoc->createElement('component1');
$Tg->appendChild($XmlDoc->createCDATASection(''));
$Opp1->appendChild($Tg);
$Tg = $XmlDoc->createElement('component1');
$Tg->appendChild($XmlDoc->createCDATASection(''));
$Opp2->appendChild($Tg);

// component2
$Tg = $XmlDoc->createElement('component2');
$Tg->appendChild($XmlDoc->createCDATASection(''));
$Opp1->appendChild($Tg);
$Tg = $XmlDoc->createElement('component2');
$Tg->appendChild($XmlDoc->createCDATASection(''));
$Opp2->appendChild($Tg);

// component3
$Tg = $XmlDoc->createElement('component3');
$Tg->appendChild($XmlDoc->createCDATASection(''));
$Opp1->appendChild($Tg);
$Tg = $XmlDoc->createElement('component3');
$Tg->appendChild($XmlDoc->createCDATASection(''));
$Opp2->appendChild($Tg);

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
	$Tg = $XmlDoc->createElement('set'.($n+1));
	$Tg->appendChild($XmlDoc->createCDATASection($tot ? $tot : ''));
	$Opp1->appendChild($Tg);

	$OppEnd=substr($r->OppArrowString, $n*$NumArrows, $NumArrows);
	if(trim($OppEnd)) {
		$Arrows=$End;
		$OppArrows=$OppEnd;
	}
	$tot=ValutaArrowString($OppEnd) . ($OppEnd!=strtoupper($OppEnd) ? '*' : '');
	$Tg = $XmlDoc->createElement('set'.($n+1));
	$Tg->appendChild($XmlDoc->createCDATASection($tot ? $tot : ''));
	$Opp2->appendChild($Tg);
}

// tie
$T1='';
$T2='';
if(trim($r->TieBreak) or trim($r->OppTieBreak)) {
	// le singole frecce del tiebrak vanno in arrow
	$Arrows=$r->TieBreak;
	$OppArrows=$r->OppTieBreak;
	$tie=max(strlen(rtrim($r->TieBreak)), strlen(rtrim($r->TieBreak)));

	for($n=0; $n<$tie; $n++) {
		$End=trim(substr($r->TieBreak, $n, 1));
		$OppEnd=trim(substr($r->OppTieBreak, $n, 1));
		if($End or $OppEnd) {
			$T1=$End;
			$T2=$OppEnd;
		}
	}
	$T1=ValutaArrowString($T1);
	$T2=ValutaArrowString($T2);
}

$Tg = $XmlDoc->createElement('tie');
$Tg->appendChild($XmlDoc->createCDATASection($T1));
$Opp1->appendChild($Tg);

$Tg = $XmlDoc->createElement('tie');
$Tg->appendChild($XmlDoc->createCDATASection($T2));
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
$Tg = $XmlDoc->createElement('setpoints');
$Tg->appendChild($XmlDoc->createCDATASection($tot1));
$Opp1->appendChild($Tg);
$Tg = $XmlDoc->createElement('setpoints');
$Tg->appendChild($XmlDoc->createCDATASection($tot2));
$Opp2->appendChild($Tg);

// arrows
for($n=0; $n<6; $n++) {
	$End=substr($Arrows, $n, 1);
	$tot=ValutaArrowString($End);
	$Tg = $XmlDoc->createElement('arrow'.($n+1));
	$Tg->appendChild($XmlDoc->createCDATASection($tot));
	$Opp1->appendChild($Tg);

	$End=substr($OppArrows, $n, 1);
	$tot=ValutaArrowString($End);
	$Tg = $XmlDoc->createElement('arrow'.($n+1));
	$Tg->appendChild($XmlDoc->createCDATASection($tot));
	$Opp2->appendChild($Tg);
}

// controllo della freccia mancante "X to win/tie/lead"
if(false and abs( $dif = strlen(trim($Arrows))-strlen(trim($OppArrows)) )==1) {
	$v1=ValutaArrowString($Arrows);
	$v2=ValutaArrowString($OppArrows);

	$v=($dif==1?$v1-$v2:$v2-$v1);

	// se la freccia da fare è il valore massimo di quel bersaglio allora settiamo il "totie"
	if($v==GetHigherTargetValue($Target)) {
		$Tg = $XmlDoc->createElement('xtotieset', $dif==1?0:1);
		$Opp1->appendChild($Tg);
		$Tg = $XmlDoc->createElement('xtotieset', $dif==1?1:0);
		$Opp2->appendChild($Tg);
	} else {
		$Tg = $XmlDoc->createElement('xtotieset', 0);
		$Opp1->appendChild($Tg);
		$Tg = $XmlDoc->createElement('xtotieset', 0);
		$Opp2->appendChild($Tg);
	}

	$Tg = $XmlDoc->createElement('xtowin', $dif==1?$v:1000);
	$Opp1->appendChild($Tg);
	$Tg = $XmlDoc->createElement('xtowin', $dif==1?1000:$v);
	$Opp2->appendChild($Tg);

	// vediamo adesso a quanto stiamo per i set e calcolare se vince il match o meno
	if($r->SetMode) {
		// si vince a 6 se si tirano 3 frecce, a 4 se si tirano 6 frecce...
		if($dif==1) {
			// arciere 2 ha 1 freccia in meno
			if(($r->OppScore>=4 and $NumArrows==3) or ($r->OppScore>=2 and $NumArrows==6)) {
			}
		} else {
			// arciere 1 ha 1 freccia in meno
		}
	} else {
		// modo vecchio, bisogna vedere il totale complessivo
		// il match viene vinto dopo 12 frecce (dai 1/4 in su)
		if($dif==1) {
			if(strlen(trim($r->ArrowString))==12) {
				// siamo all'ultima freccia, X to win match
			} else {
				// siamo al "X to lead"
			}
		}
	}
} else {
	$Tg = $XmlDoc->createElement('xtowin', 1000);
	$Opp1->appendChild($Tg);
	$Tg = $XmlDoc->createElement('xtowin', 1000);
	$Opp2->appendChild($Tg);

	$Tg = $XmlDoc->createElement('xtotieset', 0);
	$Opp1->appendChild($Tg);
	$Tg = $XmlDoc->createElement('xtotieset', 0);
	$Opp2->appendChild($Tg);
}

$Tg = $XmlDoc->createElement('towinmatch', 0);
$Opp1->appendChild($Tg);
$Tg = $XmlDoc->createElement('towinmatch', 0);
$Opp2->appendChild($Tg);

$Tg = $XmlDoc->createElement('totiematch', 0);
$Opp1->appendChild($Tg);
$Tg = $XmlDoc->createElement('totiematch', 0);
$Opp2->appendChild($Tg);


// total
$Tg = $XmlDoc->createElement('total');
$Tg->appendChild($XmlDoc->createCDATASection($r->Score));
$Opp1->appendChild($Tg);
$Tg = $XmlDoc->createElement('total');
$Tg->appendChild($XmlDoc->createCDATASection($r->OppScore));
$Opp2->appendChild($Tg);

// setscore
$Tg = $XmlDoc->createElement('setscore');
$Tg->appendChild($XmlDoc->createCDATASection($r->Score));
$Opp1->appendChild($Tg);
$Tg = $XmlDoc->createElement('setscore');
$Tg->appendChild($XmlDoc->createCDATASection($r->OppScore));
$Opp2->appendChild($Tg);

$fotodir='http://' . $_SERVER['HTTP_HOST'] . $CFG->ROOT_DIR . 'TV/Photos/' . $TourCodeSafe . '-%s-%s.jpg';

// flag
$Tg = $XmlDoc->createElement('flag');
$Tg->appendChild($XmlDoc->createCDATASection(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$r->CoCode.'.jpg')?sprintf($fotodir, 'Fl', $r->CoCode):''));
$Opp1->appendChild($Tg);
$Tg = $XmlDoc->createElement('flag');
$Tg->appendChild($XmlDoc->createCDATASection(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$r->OppCoCode.'.jpg')?sprintf($fotodir, 'Fl', $r->OppCoCode):''));
$Opp2->appendChild($Tg);


// photo1
$Tg = $XmlDoc->createElement('photo1');
$Tg->appendChild($XmlDoc->createCDATASection(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-En-'.$r->EnId.'.jpg')?sprintf($fotodir, 'En', $r->EnId):''));
$Opp1->appendChild($Tg);
$Tg = $XmlDoc->createElement('photo1');
$Tg->appendChild($XmlDoc->createCDATASection(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-En-'.$r->OppEnId.'.jpg')?sprintf($fotodir, 'En', $r->OppEnId):''));
$Opp2->appendChild($Tg);

// photo2
$Tg = $XmlDoc->createElement('photo2');
$Tg->appendChild($XmlDoc->createCDATASection(''));
$Opp1->appendChild($Tg);
$Tg = $XmlDoc->createElement('photo2');
$Tg->appendChild($XmlDoc->createCDATASection(''));
$Opp2->appendChild($Tg);

// photo3
$Tg = $XmlDoc->createElement('photo3');
$Tg->appendChild($XmlDoc->createCDATASection(''));
$Opp1->appendChild($Tg);
$Tg = $XmlDoc->createElement('photo3');
$Tg->appendChild($XmlDoc->createCDATASection(''));
$Opp2->appendChild($Tg);

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);
echo $XmlDoc->SaveXML();

die();

?>