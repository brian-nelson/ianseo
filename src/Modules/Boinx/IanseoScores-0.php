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

require_once('Common/Lib/Obj_RankFactory.php');

$rank=Obj_RankFactory::create('GridInd', $opts);
$rank->read();
$rankData=$rank->getData();


// $SQL="SELECT "
// 	. " f.FinEvent Event, "
// 	. " EvMatchArrowsNo, EvMixedTeam, EvTeamEvent, EvEventName, "
// 	. " GrPhase Phase, "
// 	. " f.FinVxF,"
// 	. " IF(f.FinDateTime>=f2.FinDateTime, f.FinDateTime, f2.FinDateTime) AS DateTime,"
// 	. " TIMESTAMPDIFF(SECOND, IF(f.FinDateTime>=f2.FinDateTime, f.FinDateTime, f2.FinDateTime), now())>90  AS TooOld,"
// 	. " f.FinMatchNo as MatchNo,"
// 	. " fs1.FsTarget Target, "
// 	. " fs2.FsTarget OppTarget, "
// 	. " f2.FinMatchNo as OppMatchNo,"
// 	. " EvMatchMode!=0 as SetMode, "
// 	// left side athlete
// 	. " e1.EnId,"
// 	. " e1.EnFirstName AS AthleteFirstName,"
// 	. " e1.EnName as AthleteName,"
// 	. " e1.EnCode as AthleteBib,"
// 	. " c1.CoCode as CoCode, "
// 	. " c1.CoName as CoName, "
// 	. " IF(EvMatchMode=0,f.FinScore,f.FinSetScore) AS Score,"
// 	. " f.FinSetPoints SetPoints,"
// 	. " f.FinTie as Tie,"
// 	. " f.FinTieBreak as TieBreak,"
// 	. " f.FinArrowString as ArrowString, "
// 	// right side athelete
// 	. " e2.EnId OppEnId,"
// 	. " e2.EnFirstName AS OpponentFirstName,"
// 	. " e2.EnName as OpponentName,"
// 	. " e2.EnCode as OpponentBib,"
// 	. " c2.CoCode as OppCoCode, "
// 	. " c2.CoName as OppCoName, "
// 	. " IF(EvMatchMode=0,f2.FinScore,f2.FinSetScore) as OppScore,"
// 	. " f2.FinSetPoints OppSetPoints,"
// 	. " f2.FinTie as OppTie,"
// 	. " f2.FinTieBreak as OppTieBreak,"
// 	. " f2.FinArrowString as OppArrowString "
// 	. "FROM Finals AS f "
// 	. "INNER JOIN Finals AS f2 ON f.FinEvent=f2.FinEvent AND f.FinMatchNo=f2.FinMatchNo-1 AND f.FinTournament=f2.FinTournament "
// 	. "LEFT JOIN Entries e1 on f.FinAthlete=e1.EnId "
// 	. "LEFT JOIN Entries e2 on f2.FinAthlete=e2.EnId "
// 	. "LEFT JOIN Countries c1 on e1.EnCountry=c1.CoId "
// 	. "LEFT JOIN Countries c2 on e2.EnCountry=c2.CoId "
// 	. "LEFT JOIN FinSchedule fs1 on fs1.FSEvent=f.FinEvent and fs1.FSTeamEvent='0' and fs1.FSMatchNo=f.FinMatchNo and f.FinTournament=fs1.FSTournament "
// 	. "LEFT JOIN FinSchedule fs2 on fs2.FSEvent=f2.FinEvent and fs2.FSTeamEvent='0' and fs2.FSMatchNo=f2.FinMatchNo and f2.FinTournament=fs2.FSTournament "
// 	. "INNER JOIN Events ON f.FinEvent=EvCode AND f.FinTournament=EvTournament AND EvTeamEvent=0 "
// 	. "INNER JOIN Grids ON f.FinMatchNo=GrMatchNo "
// 	. "WHERE f.FinTournament=$TourId AND (f.FinMatchNo % 2)=0"
// 	. " AND {$FILTER} ";

// $q=safe_r_sql($SQL);

// debug_svela($rankData);


list(,$tmp)=each($rankData['sections']);

list(,$tmp)=each($tmp['phases']);


$XmlDoc = new DOMDocument('1.0', 'UTF-8');
$XmlRoot = $XmlDoc->createElement('archeryscores');
$XmlDoc->appendChild($XmlRoot);

$Header = $XmlDoc->createElement('header');
$Header->appendChild($XmlDoc->createCDATASection(''));
$XmlRoot->appendChild($Header);

$Games = $XmlDoc->createElement('games');
$XmlRoot->appendChild($Games);

foreach($rankData['sections'] as $tmp) {
	$EvEventName=$tmp['meta']['eventName'];
	$SetSystem=intval($tmp['meta']['matchMode']);
	$Target=GetTarget($TourId, $tmp['meta']['targetType']);
	$MaxTargetPoints=$Target[0];
	$NumArrows=$tmp['meta']['finArrows'];				// Numero di frecce
	$NumEnds=$tmp['meta']['finEnds'];				// Numero di frecce
	foreach($tmp['phases'] as $phase) {
		$PhaseName=$phase['meta']['phaseName'];

		foreach($phase['items'] as $r) {

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
			$Tg = $XmlDoc->createElement('targetno');
			$Tg->appendChild($XmlDoc->createCDATASection($r['target'].($r['target']==$r['oppTarget'] ? ' A':'')));
			$Opp1->appendChild($Tg);

			$Tg = $XmlDoc->createElement('targetno');
			$Tg->appendChild($XmlDoc->createCDATASection( $r['oppTarget'].($r['target']==$r['oppTarget'] ? ' B':'')));
			$Opp2->appendChild($Tg);

			// name: Athlete name?
			$Ath1=$r['athlete'];
			$Ath2=$r['oppAthlete'];
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
			$EndNumber=0;
			$CurEnd=0;
			$opp1S=0;
			$opp2S=0;


			for($n=0; $n<5; $n++) {
// 				$Arrows='';
// 				$OppArrows='';
				$End=substr($r['arrowstring'], $n*$NumArrows, $NumArrows);
				if(trim($End)) {
					$Arrows=$End;
					$OppArrows='';
				}
				$tot1=ValutaArrowString($End) . ($End!=strtoupper($End) ? '*' : '');

				$OppEnd=substr($r['oppArrowstring'], $n*$NumArrows, $NumArrows);
				if(trim($OppEnd)) {
					$Arrows=$End;
					$OppArrows=$OppEnd;
				}
				$tot2=ValutaArrowString($OppEnd) . ($OppEnd!=strtoupper($OppEnd) ? '*' : '');

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
					if(strlen(trim($End))==$NumArrows and strlen(trim($OppEnd))==$NumArrows) {
						$a=intval($tot1);
						$b=intval($tot2);
						if($a==$b) {
							$opp1S+=1;
							$opp2S+=1;
						} elseif($a>$b) {
							$opp1S+=2;
						} else {
							$opp2S+=2;
						}
						$Tg = $XmlDoc->createElement('w'.($n+1));
						$Tg->appendChild($XmlDoc->createCDATASection($a>$b ? 2 : ($a==$b ? 1 : 0)));
						$Opp1->appendChild($Tg);

						$Tg = $XmlDoc->createElement('w'.($n+1));
						$Tg->appendChild($XmlDoc->createCDATASection($a>$b ? 0 : ($a==$b ? 1 : 2)));
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

				for($n=0; $n<$tie; $n++) {
					$End=trim(substr($r['tiebreak'], $n, 1));
					$OppEnd=trim(substr($r['oppTiebreak'], $n, 1));
					if($End or $OppEnd) {
						$T1=$End;
						$T2=$OppEnd;
					}
				}
				$T1=ValutaArrowString($T1).($T1==strtoupper($T1) ? '' : '*');
				$T2=ValutaArrowString($T2).($T2==strtoupper($T2) ? '' : '*');
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
				$tot1=ValutaArrowString($Arrows) . ($Arrows!=strtoupper($Arrows) ? '*' : '');
				$tot2=ValutaArrowString($OppArrows) . ($OppArrows!=strtoupper($OppArrows) ? '*' : '');
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

			// controllo della freccia mancante "X to win/tie/lead"
			$X1towin='';
			$X1todescr='';
			$X2towin='';
			$X2todescr='';
			if(abs( $dif = strlen(trim($Arrows))-strlen(trim($OppArrows)) )==1
					and (($r['oppSetScore']==$NumEnds and $r['setScore']==$NumEnds) or strlen(trim($Arrows))==$NumArrows or strlen(trim($OppArrows))==$NumArrows)) {
				// missing 1 arrow at the end of the current end/set

				if($SetSystem) {
					$v1=ValutaArrowString($Arrows);
					$v2=ValutaArrowString($OppArrows);
					$v=max(abs($v1-$v2), end($Target));

					// check if it wins or ties the match
					if($v<=$MaxTargetPoints) { // only if it is a coherent point
						if($dif==1) { // archer 2 is shooting
							if($r['oppSetScore']>=$NumEnds-1) {
								// ready to win or tie match/SO
								if($v==$MaxTargetPoints) {
									// can only tie
									// check if it ties the match (SO)
									if($r['oppSetScore']==$NumEnds and $r['setScore']==$NumEnds) {
// 										$X2todescr='tie SO';
									} elseif($r['oppSetScore']==$NumEnds) {
										$X2todescr='win match';
										$X2towin=$v;
									} else {
// 										$X2todescr='tie match';
									}
								} else {
									$X2towin=$v+($r['oppSetScore']==$NumEnds ? 0 : 1);
									$X2todescr='win match';
								}
							} elseif($r['setScore']<$NumEnds or $v<$MaxTargetPoints) {
								// makes sense if a tie is not leading to archer 1 victory!
// 								if($v==$MaxTargetPoints) {
// 									// can only tie the set
// 									$X2towin=$v;
// 									$X2todescr='tie set';
// 								} else {
// 									$X2towin=$v+1;
// 									$X2todescr='win set';
// 								}
							}
						} elseif($dif==-1) { // archer 1 is shooting
							if($r['setScore']>=$NumEnds-1) {
								// ready to win or tie match/SO
								if($v==$MaxTargetPoints) {
									// can only tie
									// check if it ties the match (SO)
									if($r['oppSetScore']==$NumEnds and $r['setScore']==$NumEnds) {
// 										$X1todescr='tie SO';
									} elseif($r['setScore']==$NumEnds) {
										$X1todescr='win match';
										$X1towin=$v;
									} else {
// 										$X1todescr='tie match';
									}
								} else {
									$X1towin=$v+($r['setScore']==$NumEnds ? 0 : 1);
									$X1todescr='win match';
								}
							} elseif($r['oppSetScore']<$NumEnds or $v<$MaxTargetPoints) {
								// makes sense if a tie is not leading to archer 1 victory!
// 								if($v==$MaxTargetPoints) {
// 									// can only tie the set
// 									$X1towin=$v;
// 									$X1todescr='tie set';
// 								} else {
// 									$X1towin=$v+1;
// 									$X1todescr='win set';
// 								}
							}
						}
					}
				} else {
					// cumulative points...
					$v1=ValutaArrowString($r['score']);
					$v2=ValutaArrowString($r['oppScore']);
					$v=abs($v1-$v2);

					// check if it wins or ties the match
					$CumArrows=$NumEnds*$NumArrows;
					if($v<=$MaxTargetPoints) { // only if it is a coherent point
						if($dif==1) { // archer 2 is shooting
							$ArrowString=strlen(trim($r['arrowstring']));
							if($ArrowString == $CumArrows) {
								// ready to win or tie SO
								if($v==$MaxTargetPoints) {
									// can only tie
									$X2towin=$v;
									$X2todescr='tie SO';
								} else {
									$X2towin=$v+1;
									$X2todescr='win match';
								}
							} elseif($ArrowString == $CumArrows-1) {
								// ready to win or tie SO
								if($v==$MaxTargetPoints) {
									// can only tie
									$X2towin=$v;
									$X2todescr='tie match';
								} else {
									$X2towin=$v+1;
									$X2todescr='win match';
								}
							} else {
								if($v==$MaxTargetPoints) {
									// can only tie the set
									$X2towin=$v;
									$X2todescr='tie';
								} else {
									$X2towin=$v+1;
									$X2todescr='take lead';
								}
							}
						} elseif($dif==-1) { // archer 1 is shooting
							$ArrowString=strlen(trim($r['oppArrowstring']));
							if($ArrowString == $CumArrows) {
								// ready to win or tie SO
								if($v==$MaxTargetPoints) {
									// can only tie
									$X1towin=$v;
									$X1todescr='tie SO';
								} else {
									$X1towin=$v+1;
									$X1todescr='win match';
								}
							} elseif($ArrowString == $CumArrows-1) {
								// ready to win or tie SO
								if($v==$MaxTargetPoints) {
									// can only tie
									$X1towin=$v;
									$X1todescr='tie match';
								} else {
									$X1towin=$v+1;
									$X1todescr='win match';
								}
							} else {
								if($v==$MaxTargetPoints) {
									// can only tie the set
									$X1towin=$v;
									$X1todescr='tie';
								} else {
									$X1towin=$v+1;
									$X1todescr='take lead';
								}
							}
						}
					}
				}
			}

			$Tg = $XmlDoc->createElement('xtowin', $X1towin);
			$Opp1->appendChild($Tg);
			$Tg = $XmlDoc->createElement('xtowin', $X2towin);
			$Opp2->appendChild($Tg);

			$Tg = $XmlDoc->createElement('xtodescr', $X1todescr);
			$Opp1->appendChild($Tg);
			$Tg = $XmlDoc->createElement('xtodescr', $X2todescr);
			$Opp2->appendChild($Tg);


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

			$fotodir='http://' . $_SERVER['HTTP_HOST'] . $CFG->ROOT_DIR . 'TV/Photos/' . $TourCodeSafe . '-%s-%s.jpg';

			// flag
			$Tg = $XmlDoc->createElement('flag');
			$Tg->appendChild($XmlDoc->createCDATASection(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$r['countryCode'].'.jpg')?sprintf($fotodir, 'Fl', $r['countryCode']):''));
			$Opp1->appendChild($Tg);
			$Tg = $XmlDoc->createElement('flag');
			$Tg->appendChild($XmlDoc->createCDATASection(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$r['oppCountryCode'].'.jpg')?sprintf($fotodir, 'Fl', $r['oppCountryCode']):''));
			$Opp2->appendChild($Tg);


			// photo1
			$Tg = $XmlDoc->createElement('photo1');
			$Tg->appendChild($XmlDoc->createCDATASection(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-En-'.$r['id'].'.jpg')?sprintf($fotodir, 'En', $r['id']):''));
			$Opp1->appendChild($Tg);
			$Tg = $XmlDoc->createElement('photo1');
			$Tg->appendChild($XmlDoc->createCDATASection(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-En-'.$r['oppId'].'.jpg')?sprintf($fotodir, 'En', $r['oppId']):''));
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

			// all arrows
			for($n=0; $n<15; $n++) {
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
// debug_svela($r);



if(empty($EXCLUDE_HEADER)) {
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);
	echo $XmlDoc->SaveXML();

	die();
}
