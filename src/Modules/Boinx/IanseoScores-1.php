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

$XmlDoc = new DOMDocument('1.0', 'UTF-8');
$XmlRoot = $XmlDoc->createElement('archeryscores');
$XmlDoc->appendChild($XmlRoot);

//Header
$Header = $XmlDoc->createElement('header');
$XmlRoot->appendChild($Header);

$SQL = "SELECT ToName FROM Tournament WHERE ToId=" . StrSafe_DB($TourId);
$q=safe_r_sql($SQL);
$r=safe_fetch($q);
$CompDet = $XmlDoc->createElement('competition');
$CompDet->appendChild($XmlDoc->createCDATASection($r->ToName));
$Header->appendChild($CompDet);
$CompDet = $XmlDoc->createElement('imgL');
$CompDet->appendChild($XmlDoc->createCDATASection(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-ToLeft.jpg') ? 'http://' . $_SERVER['HTTP_HOST'] . $CFG->ROOT_DIR . 'TV/Photos/' . $TourCodeSafe . '-ToLeft.jpg':''));
$Header->appendChild($CompDet);
$CompDet = $XmlDoc->createElement('imgR');
$CompDet->appendChild($XmlDoc->createCDATASection(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-ToRight.jpg') ? 'http://' . $_SERVER['HTTP_HOST'] . $CFG->ROOT_DIR . 'TV/Photos/' . $TourCodeSafe . '-ToRight.jpg':''));
$Header->appendChild($CompDet);
$CompDet = $XmlDoc->createElement('imgB');
$CompDet->appendChild($XmlDoc->createCDATASection(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-ToBottom.jpg') ? 'http://' . $_SERVER['HTTP_HOST'] . $CFG->ROOT_DIR . 'TV/Photos/' . $TourCodeSafe . '-ToBottom.jpg':''));
$Header->appendChild($CompDet);


$Games = $XmlDoc->createElement('games');
$XmlRoot->appendChild($Games);


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
			if($SetSystem) {
				$SetDetails=explode('|', $r['setPointsByEnd']);
				$OppSetDetails=explode('|', $r['oppSetPointsByEnd']);
			}

			$Game = $XmlDoc->createElement('game');
			$Games->appendChild($Game);

			// Insert Event Name
			$Event = $XmlDoc->createElement('event');
			$Event->appendChild($XmlDoc->createCDATASection($EvEventName));
			$Game->appendChild($Event);

			// SetSystem
			$Event = $XmlDoc->createElement('ss');
			$Event->appendChild($XmlDoc->createCDATASection($SetSystem));
			$Game->appendChild($Event);

			// Arrows4End
			$Event = $XmlDoc->createElement('a4e');
			$Event->appendChild($XmlDoc->createCDATASection($NumArrows));
			$Game->appendChild($Event);

			// Insert Phase Name
			$Phase = $XmlDoc->createElement('phase');
			$Phase->appendChild($XmlDoc->createCDATASection($PhaseName));
			$Game->appendChild($Phase);

			//Shootfirst
			for($n=0; $n<=$NumEnds; $n++) {
				$Tg = $XmlDoc->createElement('sf'. ($n!=$NumEnds ? ($n+1) : 'tie'));
				$Tg->appendChild($XmlDoc->createCDATASection((($r['shootFirst'] & pow(2,$n))!=0 ? 1 : (($r['oppShootFirst'] & pow(2,$n))!=0 ? 2 : 0))));
				$Game->appendChild($Tg);
			}

			//Confirmed Status
			$Tg = $XmlDoc->createElement('ec1');
			$Tg->appendChild($XmlDoc->createCDATASection(($r['status']==3 || $r['status']==1 ? 1 : 0)));
			$Game->appendChild($Tg);
			$Tg = $XmlDoc->createElement('ec2');
			$Tg->appendChild($XmlDoc->createCDATASection(($r['oppStatus']==3 || $r['oppStatus']==1 ? 1 : 0)));
			$Game->appendChild($Tg);
			$Tg = $XmlDoc->createElement('mc');
			$Tg->appendChild($XmlDoc->createCDATASection(($r['status']==1 && $r['oppStatus']==1 ? 1 : 0)));
			$Game->appendChild($Tg);

			//Winner
			$Tg = $XmlDoc->createElement('winner');
			$Tg->appendChild($XmlDoc->createCDATASection(($r['winner']==1 ? 1 : ($r['oppWinner']==1 ? 2 : 0))));
			$Game->appendChild($Tg);

			// create opponent 1
			$Opp1 = $XmlDoc->createElement('opponent1');
			$Game->appendChild($Opp1);

			// create opponent 2
			$Opp2 = $XmlDoc->createElement('opponent2');
			$Game->appendChild($Opp2);

			// targetno
			$Tg = $XmlDoc->createElement('targetno');
			$Tg->appendChild($XmlDoc->createCDATASection(ltrim($r['target'],'0').($r['target']==$r['oppTarget'] ? ' A':'')));
			$Opp1->appendChild($Tg);

			$Tg = $XmlDoc->createElement('targetno');
			$Tg->appendChild($XmlDoc->createCDATASection( ltrim($r['oppTarget'],'0').($r['target']==$r['oppTarget'] ? ' B':'')));
			$Opp2->appendChild($Tg);

			// name: Athlete name?
			$Ath1=$r['countryName'];
			$Ath2=$r['oppCountryName'];
			$len=max(strlen($Ath1), strlen($Ath2));

			$pad1=GetPaddedNames($Ath1, $len);
			$pad2=GetPaddedNames($Ath2, $len);

			$Tg = $XmlDoc->createElement('name');
			$Tg->appendChild($XmlDoc->createCDATASection(str_pad($Ath1, $pad1, ' ')));
			$Opp1->appendChild($Tg);
			$Tg = $XmlDoc->createElement('name');
			$Tg->appendChild($XmlDoc->createCDATASection(str_pad($Ath2, $pad2, ' ')));
			$Opp2->appendChild($Tg);

			// shortname
			$Tg = $XmlDoc->createElement('shortname');
			$Tg->appendChild($XmlDoc->createCDATASection($r['countryCode']));
			$Opp1->appendChild($Tg);
			$Tg = $XmlDoc->createElement('shortname');
			$Tg->appendChild($XmlDoc->createCDATASection($r['oppCountryCode']));
			$Opp2->appendChild($Tg);

			// First Team
			$n=0;
			foreach ($tmp['athletes'][$r['teamId']][0] as $ath) {
				$Tg = $XmlDoc->createElement('component' . $n);
				$Tg->appendChild($XmlDoc->createCDATASection($ath['athlete']));
				$Opp1->appendChild($Tg);

				$Tg = $XmlDoc->createElement('photo' . $n);
				$Tg->appendChild($XmlDoc->createCDATASection(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-En-'.$ath['id'].'.jpg')?sprintf($fotodir, 'En', $ath['id']):''));
				$Opp1->appendChild($Tg);
				$n++;
			}
			// Second Team
			$n=0;
			foreach ($tmp['athletes'][$r['oppTeamId']][0] as $ath) {
				$Tg = $XmlDoc->createElement('component' . $n);
				$Tg->appendChild($XmlDoc->createCDATASection($ath['athlete']));
				$Opp2->appendChild($Tg);

				$Tg = $XmlDoc->createElement('photo' . $n);
				$Tg->appendChild($XmlDoc->createCDATASection(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-En-'.$ath['id'].'.jpg')?sprintf($fotodir, 'En', $ath['id']):''));
				$Opp2->appendChild($Tg);
				$n++;
			}

			// get the single ends => set
			$Arrows='';
			$OppArrows='';
			$EndNumber=0;
			$CurEnd=0;
			$opp1S=0;
			$opp2S=0;

			for($n=0; $n<5; $n++) {
				$End=substr($r['arrowstring'], $n*$NumArrows, $NumArrows);
				if(trim($End)) {
					$Arrows=$End;
					$OppArrows='';
				}
				$tot1=ValutaArrowString($End);// . ($End!=strtoupper($End) ? '*' : '');

				$OppEnd=substr($r['oppArrowstring'], $n*$NumArrows, $NumArrows);
				if(trim($OppEnd)) {
					$Arrows=$End;
					$OppArrows=$OppEnd;
				}
				$tot2=ValutaArrowString($OppEnd);// . ($OppEnd!=strtoupper($OppEnd) ? '*' : '');

				$len=max(strlen($tot1), strlen($tot2));
				$pad1=GetPaddedNames($tot1, $len);
				$pad2=GetPaddedNames($tot2, $len);

				$Tg = $XmlDoc->createElement('set'.($n+1));
				$Tg->appendChild($XmlDoc->createCDATASection(str_pad($Arrows ? $tot1 : '', $pad1, ' ', STR_PAD_LEFT)));
				$Opp1->appendChild($Tg);

				$Tg = $XmlDoc->createElement('set'.($n+1));
				$Tg->appendChild($XmlDoc->createCDATASection(str_pad($OppArrows ? $tot2 : '', $pad2, ' ', STR_PAD_LEFT)));
				$Opp2->appendChild($Tg);

				if($SetSystem) {
					// winner of set
					if(strlen(trim($End))==$NumArrows and strlen(trim($OppEnd))==$NumArrows and isset($SetDetails[$n])) {
						$opp1S += $SetDetails[$n];
						$opp2S += $OppSetDetails[$n];

						$Tg = $XmlDoc->createElement('w'.($n+1));
						$Tg->appendChild($XmlDoc->createCDATASection($SetDetails[$n]));
						$Opp1->appendChild($Tg);

						$Tg = $XmlDoc->createElement('w'.($n+1));
						$Tg->appendChild($XmlDoc->createCDATASection($OppSetDetails[$n]));
						$Opp2->appendChild($Tg);

					} else {
						// set in progress
						$Tg = $XmlDoc->createElement('w'.($n+1));
						$Tg->appendChild($XmlDoc->createCDATASection(''));
						$Opp1->appendChild($Tg);

						$Tg = $XmlDoc->createElement('w'.($n+1));
						$Tg->appendChild($XmlDoc->createCDATASection(''));
						$Opp2->appendChild($Tg);
					}

					// set points
					$Tg = $XmlDoc->createElement('s'.($n+1));
					$Tg->appendChild($XmlDoc->createCDATASection($opp1S));
					$Opp1->appendChild($Tg);

					$Tg = $XmlDoc->createElement('s'.($n+1));
					$Tg->appendChild($XmlDoc->createCDATASection($opp2S));
					$Opp2->appendChild($Tg);
				}

				if(strlen(trim($End))==$NumArrows and strlen(trim($OppEnd))==$NumArrows) $EndNumber=$n+1;
				if(trim($End) or trim($OppEnd)) $CurEnd=$n+1;
			}

			$Tg = $XmlDoc->createElement('endnumber');
			$Tg->appendChild($XmlDoc->createCDATASection($EndNumber));
			$Game->appendChild($Tg);

			$Tg = $XmlDoc->createElement('currentend');
			$Tg->appendChild($XmlDoc->createCDATASection($CurEnd));
			$Game->appendChild($Tg);

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
				$T1=(!empty($T1) ? ValutaArrowString($T1).($T1==strtoupper($T1) ? '' : '*') : '');
				$T2=(!empty($T2) ? ValutaArrowString($T2).($T2==strtoupper($T2) ? '' : '*') : '');
			}

			$len=max(strlen($T1), strlen($T2));
			$pad1=GetPaddedNames($T1, $len);
			$pad2=GetPaddedNames($T2, $len);

			$Tg = $XmlDoc->createElement('tie');
			$Tg->appendChild($XmlDoc->createCDATASection(str_pad($T1, $pad1, ' ', STR_PAD_LEFT)));
			$Opp1->appendChild($Tg);

			$Tg = $XmlDoc->createElement('tie');
			$Tg->appendChild($XmlDoc->createCDATASection(str_pad($T2, $pad2, ' ', STR_PAD_LEFT)));
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
				$tot1=ValutaArrowString($Arrows);// . ($Arrows!=strtoupper($Arrows) ? '*' : '');
				$tot2=ValutaArrowString($OppArrows);// . ($OppArrows!=strtoupper($OppArrows) ? '*' : '');
			}

			$len=max(2, strlen($tot1), strlen($tot2));
			$pad1=GetPaddedNames($tot1, $len);
			$pad2=GetPaddedNames($tot2, $len);

			$Tg = $XmlDoc->createElement('setpoints');
			$Tg->appendChild($XmlDoc->createCDATASection(str_pad($tot1, $pad1, ' ', STR_PAD_LEFT)));
			$Opp1->appendChild($Tg);
			$Tg = $XmlDoc->createElement('setpoints');
			$Tg->appendChild($XmlDoc->createCDATASection(str_pad($tot2, $pad2, ' ', STR_PAD_LEFT)));
			$Opp2->appendChild($Tg);

			// arrows
			for($n=0; $n<6; $n++) {
				$End1=substr($Arrows, $n, 1);
				$tot1=ValutaArrowString($End1). ($End1!=strtoupper($End1) ? '*' : '');
				$End2=substr($OppArrows, $n, 1);
				$tot2=ValutaArrowString($End2). ($End2!=strtoupper($End2) ? '*' : '');

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
			$Tg = $XmlDoc->createElement('total');
			$Tg->appendChild($XmlDoc->createCDATASection($r['score']));
			$Opp1->appendChild($Tg);
			$Tg = $XmlDoc->createElement('total');
			$Tg->appendChild($XmlDoc->createCDATASection($r['oppScore']));
			$Opp2->appendChild($Tg);

			// setscore
			$Tg = $XmlDoc->createElement('setscore');
			$Tg->appendChild($XmlDoc->createCDATASection($r['setScore']));
			$Opp1->appendChild($Tg);
			$Tg = $XmlDoc->createElement('setscore');
			$Tg->appendChild($XmlDoc->createCDATASection($r['oppSetScore']));
			$Opp2->appendChild($Tg);

			// flag
			$Tg = $XmlDoc->createElement('flag');
			$Tg->appendChild($XmlDoc->createCDATASection(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$r['countryCode'].'.jpg')?sprintf($fotodir, 'Fl', $r['countryCode']):''));
			$Opp1->appendChild($Tg);
			$Tg = $XmlDoc->createElement('flag');
			$Tg->appendChild($XmlDoc->createCDATASection(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$r['oppCountryCode'].'.jpg')?sprintf($fotodir, 'Fl', $r['oppCountryCode']):''));
			$Opp2->appendChild($Tg);

			// all arrows
			for($n=0; $n<24; $n++) {
				$a=DecodeFromLetter(substr($r['arrowstring'], $n, 1));
				$b=DecodeFromLetter(substr($r['oppArrowstring'], $n, 1));
				$Tg = $XmlDoc->createElement('a'.($n+1));
				$Tg->appendChild($XmlDoc->createCDATASection($a));
				$Opp1->appendChild($Tg);
				$Tg = $XmlDoc->createElement('a'.($n+1));
				$Tg->appendChild($XmlDoc->createCDATASection($b));
				$Opp2->appendChild($Tg);
			}
		}
	}
}

if(empty($EXCLUDE_HEADER)) {
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);
	echo $XmlDoc->SaveXML();

	die();
}


?>