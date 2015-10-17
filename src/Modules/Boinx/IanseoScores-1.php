<?php
/*****************
 *
 * LEGGERE NOTA PIU' SOTTO RIGUARDO ALLA VISIBILITA' DELLE FRECCE (riga 163)
 *
 * */

// Directory dove saranno inserite le varie foto
$fotodir='http://' . $_SERVER['HTTP_HOST'] . $CFG->ROOT_DIR . 'TV/Photos/' . $TourCodeSafe . '-%s-%s.jpg';

$rank=Obj_RankFactory::create('GridTeam', $opts);
$rank->read();
$rankData=$rank->getData();

//debug_svela($rankData);

if(empty($EXCLUDE_HEADER)) {
	$XmlDoc = new DOMDocument('1.0', 'UTF-8');
	$XmlRoot = $XmlDoc->createElement('archeryscores');
	$XmlDoc->appendChild($XmlRoot);

	$Header = $XmlDoc->createElement('header');
	$Header->appendChild($XmlDoc->createCDATASection(''));
	$XmlRoot->appendChild($Header);

	$Games = $XmlDoc->createElement('games');
	$XmlRoot->appendChild($Games);
}


foreach($rankData['sections'] as $tmp) {
	if(empty($tmp['phases']))
		continue;
	
	$EvEventName=$tmp['meta']['eventName'];
	$SetSystem=intval($tmp['meta']['matchMode']);
	$Target=GetTarget($TourId, $tmp['meta']['targetType']);
	$MaxTargetPoints=$Target[0];
	$NumArrows=$tmp['meta']['finArrows'];				// Numero di frecce
	$NumEnds=$tmp['meta']['finEnds'];
	foreach($tmp['phases'] as $phase) {
		$PhaseName=$phase['meta']['phaseName'];
		
		foreach($phase['items'] as $r) {
			$Game = $XmlDoc->createElement('game');
			$Games->appendChild($Game);
			//$Games->appendChild($XmlDoc->createElement('game')); // fake element so that Boinx Works
		
			// Insert Event Name
			$Event = $XmlDoc->createElement('event');
			$Event->appendChild($XmlDoc->createCDATASection($EvEventName));
			$Game->appendChild($Event);
		
			// Insert Phase Name
			$Phase = $XmlDoc->createElement('phase');
			$Phase->appendChild($XmlDoc->createCDATASection($PhaseName));
			$Game->appendChild($Phase);
		
			// create opponent 1
			$Opp1 = $XmlDoc->createElement('opponent1');
			$Game->appendChild($Opp1);
		
			// create opponent 2
			$Opp2 = $XmlDoc->createElement('opponent2');
			$Game->appendChild($Opp2);
		
			// targetno
			$Tg = $XmlDoc->createElement('targetno', $r['target'].($r['target']==$r['oppTarget'] ? ' A':''));
			$Opp1->appendChild($Tg);
			$Tg = $XmlDoc->createElement('targetno', $r['oppTarget'].($r['target']==$r['oppTarget'] ? ' B':''));
			$Opp2->appendChild($Tg);
		
			// name: Athlete name?
			$Tg = $XmlDoc->createElement('name', $r['countryName']);
			$Opp1->appendChild($Tg);
			$Tg = $XmlDoc->createElement('name', $r['oppCountryName']);
			$Opp2->appendChild($Tg);
		
			// shortname
			$Tg = $XmlDoc->createElement('shortname', $r['countryCode']);
			$Opp1->appendChild($Tg);
			$Tg = $XmlDoc->createElement('shortname', $r['oppCountryCode']);
			$Opp2->appendChild($Tg);
		
			// First Team
			$n=0;
			foreach ($tmp['athletes'][$r['teamId']][0] as $ath) {
				$Tg = $XmlDoc->createElement('component'.$n, $ath['athlete']);
				$Opp1->appendChild($Tg);
				
				$Tg = $XmlDoc->createElement('photo'.$n, file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-En-'.$ath['id'].'.jpg')?sprintf($fotodir, 'En', $ath['id']):'');
				$Opp1->appendChild($Tg);
				$n++;
			}
			// Second Team
			$n=0;
			foreach ($tmp['athletes'][$r['oppTeamId']][0] as $ath) {
				$Tg = $XmlDoc->createElement('component'.$n, $ath['athlete']);
				$Opp2->appendChild($Tg);
			
				$Tg = $XmlDoc->createElement('photo'.$n, file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-En-'.$ath['id'].'.jpg')?sprintf($fotodir, 'En', $ath['id']):'');
				$Opp2->appendChild($Tg);
				$n++;
			}
		
			// get the single ends => set
			$Arrows='';
			$OppArrows='';
			for($n=0; $n<5; $n++) {
				$End=substr($r['arrowstring'], $n*$NumArrows, $NumArrows);
				if(trim($End)) {
					$Arrows=$End;
					$OppArrows='';
				}
				$tot=ValutaArrowString($End) . ($End!=strtoupper($End) ? '*' : '');
				$Tg = $XmlDoc->createElement('set'.($n+1), $tot ? $tot : '');
				$Opp1->appendChild($Tg);
		
				$OppEnd=substr($r['oppArrowstring'], $n*$NumArrows, $NumArrows);
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
			if(trim($r['tiebreak']) or trim($r['oppTiebreak'])) {
				// le singole frecce del tiebrak vanno in arrow
				$Arrows=$r['tiebreak'];
				$OppArrows=$r['oppTiebreak'];
				$tie=max(strlen(rtrim($r['tiebreak'])), strlen(rtrim($r['oppTiebreak'])));
		
				for($n=0; $n<$tie; $n+=$tmp['meta']['maxTeamPerson']) {
					$End=trim(substr($r['tiebreak'], $n, $tmp['meta']['maxTeamPerson']));
					$OppEnd=trim(substr($r['oppTiebreak'], $n, $tmp['meta']['maxTeamPerson']));
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
		
			
			// adding SO winner
			$Tg = $XmlDoc->createElement('sw');
			$Tg->appendChild($XmlDoc->createCDATASection($r['tie']));
			$Opp1->appendChild($Tg);
			
			$Tg = $XmlDoc->createElement('sw');
			$Tg->appendChild($XmlDoc->createCDATASection($r['oppTie']));
			$Opp2->appendChild($Tg);
			
			// IN QUESTO PUNTO VA INSERITO L'EVENTUALE AZZERAMENTO DELLE FRECCE DI VOLEE
			// BASATO SULLA DISTANZA NEL TEMPO DI FinDateTime
//			if(false and $r->TooOld) {
//				$Arrows='';
//				$OppArrows='';
//			}
		
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
		// 		$End=substr($Arrows, $n, 1);
		// 		$tot=ValutaArrowString($End);
		// 		$Tg = $XmlDoc->createElement('arrow'.($n+1), $tot);
		// 		$Opp1->appendChild($Tg);
		
		// 		$End=substr($OppArrows, $n, 1);
		// 		$tot=ValutaArrowString($End);
		// 		$Tg = $XmlDoc->createElement('arrow'.($n+1), $tot);
		// 		$Opp2->appendChild($Tg);
				$End1=substr($Arrows, $n, 1);
				$tot1=ValutaArrowString($End1);
				$End2=substr($OppArrows, $n, 1);
				$tot2=ValutaArrowString($End2);
		
				$len=max(2, strlen($tot1), strlen($tot2));
				$pad1=GetPaddedNames($tot1, $len);
				$pad2=GetPaddedNames($tot2, $len);
				if($End1!='A' and !$tot1) $tot1='';
				if($End2!='A' and !$tot2) $tot2='';
		
				$Tg = $XmlDoc->createElement('arrow'.($n+1));
				$Tg->appendChild($XmlDoc->createCDATASection(str_pad($tot1, $pad1, ' ', STR_PAD_LEFT)));
				$Opp1->appendChild($Tg);
		
				$Tg = $XmlDoc->createElement('arrow'.($n+1));
				$Tg->appendChild($XmlDoc->createCDATASection(str_pad($tot2, $pad2, ' ', STR_PAD_LEFT)));
				$Opp2->appendChild($Tg);
			}
		
			// total
			$Tg = $XmlDoc->createElement('total', $r['score']);
			$Opp1->appendChild($Tg);
			$Tg = $XmlDoc->createElement('total', $r['oppScore']);
			$Opp2->appendChild($Tg);
		
			// setscore
			$Tg = $XmlDoc->createElement('setscore', $r['setScore']);
			$Opp1->appendChild($Tg);
			$Tg = $XmlDoc->createElement('setscore', $r['oppSetScore']);
			$Opp2->appendChild($Tg);
		
			// flag
			$Tg = $XmlDoc->createElement('flag', file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$r['countryCode'].'.jpg')?sprintf($fotodir, 'Fl', $r['countryCode']):'');
			$Opp1->appendChild($Tg);
			$Tg = $XmlDoc->createElement('flag', file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$r['oppCountryCode'].'.jpg')?sprintf($fotodir, 'Fl', $r['oppCountryCode']):'');
			$Opp2->appendChild($Tg);
		}
	}
}

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);
echo $XmlDoc->SaveXML();

die();

?>