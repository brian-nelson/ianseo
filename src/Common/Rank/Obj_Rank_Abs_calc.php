<?php
/**
 * Obj_Rank_Abs
 * Implementa l'algoritmo di default per il calcolo della rank di qualificazione assoluta individuale
 *
 * La tabella in cui vengono scritti i valori è la Individuals.
 *
 * Per questa classe $opts ha la seguente forma:
 *
 * array(
 * 		events	=> array(<ev_1>,<ev_2>...<ev_n>) || string,			[calculate/read]
 * 		dist	=> #												[calculate/read]
 * 		runningDist	=> #											[read]
 * 		tournament => #												[calculate/read]
 * 		cutRank => #												[read]
 * 		session => #												[read,non influisce su calculate]
 * 		skipExisting => #											[calculate]
 * )
 *
 * con:
 * 	 events: l'array degli eventi assoluti oppure se scalare, una stringa usata in LIKE
 * 	 dist: la distanza con 0 per indicare la rank assoluta totale totale.
 * 	 runningDist: Restituisce la classifica dopo "X" distanze a non della distanza "x" (e rimuove le impostazioni di "dist" se presenti)
 *	 tournament: Se impostato è l'id del torneo su cui operare altrimenti prende quello in sessione.
 *	 session: Se impostato ritorna la classifica di quella sessione, con la rank globale. Chi chiama se vuole ricalcolerà la rank in quella sessione
 *	 skipExisting: Se 1 non sovrascrive posizione e frecce di SO dove sono già valorizzati - Solo per Distanza = 0
 *
 * $data ha la seguente forma
 *
 * array(
 * 		meta 		=> array(
 * 			title 	=> <titolo della classifica localizzato>
 * 			numDist	=> <numero distanze>, inizializzato solo se c'è almeno una sezione
 * 			double	=> <1 se gara doppia 0 altrimenti>, inizializzato solo se c'è almeno una sezione
 * 			lastUpdate => timestamp dell'ultima modifica (il max tra tutte le righe)
 *		),
 * 		sections 	=> array(
 * 			event_1 => array(
 * 				meta => array(
 * 					event => <event_1>, valore uguale alla chiave
 * 					descr => <descrizione evento localizzata>
 * 					qualifiedNo => <numero di persone qualificate per l'evento>
 * 					printHeader => <testa stampa>
 * 					fields(*1) => array(
 *						id 				=> <id della persona>
 *                      bib 			=> <codice della persona>
 *                      session 		=> <sessione>
 *                      target 			=> <piazzola>
 *                      athlete 		=> <cognome e nome>
 *                      familyname 		=> <cognome>
 *						givenname 		=> <nome>
 *                      div				=> <codice divisione>
 *                      cl				=> <codice classe>
 *                      subclass 		=> <categoria>
 *                      countryCode 	=> <codice nazione>
 *                      countryName 	=> <nazione>
 *                      rank 			=> <rank in base alla distanza>
 *                      score 			=> <punti in base alla distanza>
 *                      gold 			=> <ori in base alla distanza>
 *                      xnine 			=> <xnine in base alla distanza>
 *                      tiebreak		=> <frecce di tie>					(distanza 0)
 *                      ct				=> <numero di cointoss (gialli)>	(distanza 0)
 *                      so				=> <1 se shootoff (rosso)>			(distanza 0)
 *                      dist_1 			=> <rank|punti|ori|xnine della distanza 1>
 *                      dist_2 			=> <rank|punti|ori|xnine della distanza 2>
 *                      dist_3 			=> <rank|punti|ori|xnine della distanza 3>
 *                      dist_4 			=> <rank|punti|ori|xnine della distanza 4>
 *                      dist_5 			=> <rank|punti|ori|xnine della distanza 5>
 *                      dist_6 			=> <rank|punti|ori|xnine della distanza 6>
 *                      dist_7	 		=> <rank|punti|ori|xnine della distanza 7>
 *                      dist_8 			=> <rank|punti|ori|xnine della distanza 8>
 *                      hits			=> <frecce tirate (tutte se la distanza è zero oppure solo quelle della distanza passata)>
 * 					)
 *				)
 * 				items => array(
 * 					array(id=><valore>,bib=><valore>,...,dist_8=><valore>),
 * 					...
 * 				)
 * 			)
 * 			...
 * 			event_n = ...
 * 		)
 * )
 *
 * Estende Obj_Rank
 */
	class Obj_Rank_Abs_calc extends Obj_Rank_Abs
	{
	/**
	 * calculate().
	 * La classifica abs viene calcolata quando si calcola quella di classe e l'evento
	 * prevede la div/cl della persona coinvolta
	 * e quando si fanno gli spareggi per passare alle eliminatorie o alle finali.
	 * Nel primo caso questo è il metodo da chiamare perchè calcolerà l'IndRank o l'IndD[1-8]Rank lavorando su tutto l'evento
	 * (utilizza setRow()) altrimenti occorre usare setRow() direttamente.
	 *
	 * @override
	 *
	 * (non-PHPdoc)
	 * @see ianseo/Common/Rank/Obj_Rank#calculate()
	 */
		public function calculate() {
			$dd = ($this->opts['dist']>0 ? 'D' . $this->opts['dist'] : '');

			$f=$this->safeFilter();
			$filter=($f!==false ? $f : "");

			// assign rank=0 if distance or total points=0, to prevent ranking to be retained when making tests
			$sql='';
			for($n=1; $n<=8; $n++) {
				$sql.="IndD{$n}Rank=if(QuD{$n}Score=0, 0, IndD{$n}Rank), ";
			}
			$sql.="IndRank=if(QuScore=0 and QuHits=0, 0, IndRank), IndRankFinal=if(QuScore=0 and QuHits=0, 0, IndRankFinal), IndSO=if((QuScore=0 and QuHits=0) or IrmShowRank=0, 0, IndSO) ";
			safe_w_sql("update Individuals
					inner join Qualifications ON IndId=QuId
					inner join IrmTypes on IrmId=IndIrmType
					set $sql
					where IndTournament={$this->tournament}");

			$q="SELECT
					IndId AS `athId`,IndEvent AS `EventCode`,
					Qu{$dd}Score AS Score,Qu{$dd}Gold AS Gold,Qu{$dd}Xnine AS XNine, Qu{$dd}Hits AS Hits, IndRank as actualRank,
					EvFinalFirstPhase, EvElim1, EvElim2, EvElimType,
					IF(EvFinalFirstPhase=0,999999,IF(EvElimType=0, EvNumQualified ,IF(EvElim1=0,EvElim2,EvElim1))) as QualifiedNo, EvFirstQualified
				FROM Events
				INNER JOIN Individuals ON EvCode=IndEvent AND EvTournament=IndTournament AND EvTeamEvent=0
			    inner join IrmTypes on IrmId=IndIrmType and IrmShowRank=1
				INNER JOIN Qualifications ON IndId=QuId
				WHERE
					IndTournament={$this->tournament}
			        AND (QuScore != 0 OR QuHits !=0) 
					{$filter}
				ORDER BY
					IndEvent,Qu{$dd}Score DESC,Qu{$dd}Gold DESC,Qu{$dd}Xnine DESC
			";
				//print $q.'<br><br>';
			$r=safe_r_sql($q);

			if (safe_num_rows($r)>0) {
				$curGroup = "";
				$myRank = 1;
				$myPos = 0;

				$myScoreOld = 0;
				$myGoldOld = 0;
				$myXNineOld = 0;
				$mySoScore=array();
				$myGroupStartPos=0;
				$currentRow=-1;

				while($myRow=safe_fetch($r)) {
					$currentRow++;

					if ($curGroup != $myRow->EventCode) {
						$curGroup = $myRow->EventCode;

						$myRank = 1;
						$myPos = 0;
						$myScoreOld = 0;
						$myGoldOld = 0;
						$myXNineOld = 0;
						$mySoScore=array();
						$myGroupStartPos = $currentRow;


					/*
					 * If starting phase is 1/48 or 1/24, I check the 8th position for shootoff,
					 */
						if(($NumSaved=SavedInPhase($myRow->EvFinalFirstPhase ) and $myRow->EvElimType==0) or ($NumSaved=2 and ($myRow->EvElimType==3 or $myRow->EvElimType==4))) {
							if(safe_num_rows($r) > ($myGroupStartPos + $NumSaved + $myRow->EvFirstQualified-1)) {
								safe_data_seek($r,$myGroupStartPos + $NumSaved + $myRow->EvFirstQualified - 2);
								$tmpMyRow = safe_fetch($r);
								if($curGroup == $tmpMyRow->EventCode) {
									$tmpScore = $tmpMyRow->Score;
									$tmpMyRow = safe_fetch($r);
									//Controllo se c'è parimerito per entrare
									if ($tmpScore == $tmpMyRow->Score AND $curGroup == $tmpMyRow->EventCode) {
                                        $mySoScore[] = $tmpScore;
                                    }
								}
								$tmpMyRow = NULL;
							}
							safe_data_seek($r,$myGroupStartPos+1);
						}

					/*
					 * Carico l'ultimo punteggio per entrare.
					 * Vado a prendere la riga con l'ultimo Score buono
					 */
						if(safe_num_rows($r) > ($myGroupStartPos + $myRow->QualifiedNo + $myRow->EvFirstQualified - 1)) {
							safe_data_seek($r,$myGroupStartPos + $myRow->QualifiedNo + $myRow->EvFirstQualified - 2);
							$tmpMyRow = safe_fetch($r);
							if($curGroup == $tmpMyRow->EventCode) {
								$tmpScore = $tmpMyRow->Score;
								$tmpMyRow = safe_fetch($r);
								//Controllo se c'è parimerito per entrare
								if ($tmpScore == $tmpMyRow->Score && $curGroup == $tmpMyRow->EventCode) {
                                    $mySoScore[] = $tmpScore;
                                }
							}
							$tmpMyRow = NULL;
						}
						safe_data_seek($r,$myGroupStartPos+1);
					}
					$myPos++;
					$so=-1;

                    // As for $mySoScore loading, in case of same score there is a SO.
                    if(in_array($myRow->Score,$mySoScore)) {
						if ($myRow->Score!=$myScoreOld) {
                            $myRank = $myPos;
                        }
    					$so=1;
					} else {
                        // all the other are tie only in case of Full Tie
						if (!($myRow->Score==$myScoreOld AND $myRow->Gold==$myGoldOld AND $myRow->XNine==$myXNineOld)) {
                            $myRank = $myPos;
                        }
					}
                    //Before the first qualified we remove CT
                    if ($myRow->EvFirstQualified != 1 AND $myPos == $myRow->EvFirstQualified) {
                        safe_w_SQL("UPDATE Individuals SET IndSO=0 WHERE IndSO < 0 AND IndSO > " . (-1*$myRank) . " AND IndEvent='{$myRow->EventCode}' AND IndTournament={$this->tournament}");
                    }
                    //After the last qualified we do not check SO/CT any more
					if($myRow->EvFinalFirstPhase==0 OR ($myRank>($myRow->QualifiedNo + $myRow->EvFirstQualified -1))) {
                        $so = 0;
                    }

					$myScoreOld = $myRow->Score;
					$myGoldOld = $myRow->Gold;
					$myXNineOld = $myRow->XNine;

					$x = false;
					if($this->opts['dist']==0 AND $myRow->actualRank!=0 AND array_key_exists('skipExisting',$this->opts) AND $this->opts['skipExisting']==1) {
						$x=$this->setRow(array(
							array(	// passo 1 item alla volta
								'ath' 		=> $myRow->athId,
								'event'		=> $myRow->EventCode,
								'dist'		=> $this->opts['dist'],
								'hits'		=> $myRow->Hits,
								'so'		=> ($so * $myRank)
							)
						));
					} else {
						$x=$this->setRow(array(
							array(	// passo 1 item alla volta
								'ath' 		=> $myRow->athId,
								'event'		=> $myRow->EventCode,
								'dist'		=> $this->opts['dist'],
								'hits'		=> $myRow->Hits,
								'rank'		=> $myRank,
								'finalrank' => ($myRow->EvFinalFirstPhase ? -1 : $myRank),
								'tiebreak'	=> '',
								'decoded'	=> '',
                                'closest'	=> 0,
								'so'		=> ($so * $myRank)
							)
						));
					}

					//print '..'.$x.'<br>';
					if ($x===false)
						return false;
				}
			}

			return true;

		}

	/**
	 * setRow().
	 * Imposta le IndRank degli elementi passati.
	 *
	 * Questo è il metodo da chiamare quando si risolvono gli spareggi perchè chi non passa ha la rank a posto
	 * grazie a calculate() e gli altri (sia quelli a cavallo che i buoni di sicuro) vanno impostati a mano.
	 *
	 *
	 * @param mixed $items: array degli elementi da scrivere.
	 * 		La struttra è la seguente:
	 * 			array(
	 * 				array(
	 * 					ath 		=> <id>		(chiave)
	 * 					event 		=> <ev>		(chiave)
	 *					dist		=> <dist> 	(chiave)
	 * 					rank 		=> <rank>
 	 * 					tiebreak 	=> <arrowstring>
                        closest 	=> <tinyint>
	 * 					so 			=> <so>
	 * 				)
	 * 			)
	 *		con <id> l'id della persona <ev> l'evento, <arrowstring> l'arrowstring delle frecce di tie (opzionale), <dist> la distanza (0 vuol dire IndRank),
	 *		<rank> la rank da impostare (opzionale), e e <so> prima degli spareggi vale come la rank se non ci sono spareggi; 0 per chi non passa e negativo come la rank in caso di gialli (opzionale).
	 *		L'arrowstring, e l'so comunque sono considerati solo se <dist>==0
	 *
	 *		Deve essere presente almeno un campo opzionale se no il metodo ritorna errore.
	 *
	 *
	 * @return mixed: ritorna le affected_rows oppure false se c'è qualche errore
	 * 		(non salva gli eventuali elementi successivi a quello che ha generato l'errore)
	 */
		public function setRow($items=array()) {
		// campi mandatory per $item
			$params=array('ath','event','dist');

			$affected=0;

			foreach ($items as $item) {
				/*print '<pre>';
				print_r($item);
				print '</pre>';*/

				$paramsOk=true;

				$canUp=false;

		/*
		 *  controllo che ci siano i campi mandatory
		 */
				foreach ($params as $p) {
					if (!array_key_exists($p,$item)) {
						$paramsOk=false;
						$ret=false;
						break;
					}
				}

				if (!$paramsOk) continue;

				$dd = ($item['dist'] ? 'D' . $item['dist'] : '');

				$date=date('Y-m-d H:i:s');

				$q = "UPDATE Individuals SET IndTimestamp='{$date}' ";

			/* campi opzionali e basta */
				if (array_key_exists('rank',$item))	{
					$canUp=true;
					$q.=",Ind{$dd}Rank={$item['rank']}";
                    if (array_key_exists('finalrank',$item) AND $item['finalrank']!=-1) {
                        $q.=",IndRankFinal={$item['finalrank']}";
                    }
				}

			/*
			 *  campi opzionali (se dist==0).
			 *  In ogni caso i valori vengono scritti se e solo se la rank nuova è diversa dalla vecchia!
			 */
				if ($item['dist']==0) {
					if (array_key_exists('tiebreak',$item)) {
						$canUp=true;
						$q.=",IndTiebreak='{$item['tiebreak']}'";
					}

                    if (array_key_exists('decoded',$item)) {
                        $canUp=true;
                        $q.=",IndTbDecoded='{$item['decoded']}'";
                    }

                    if (array_key_exists('closest',$item)) {
                        $canUp=true;
                        $q.=",IndTbClosest='{$item['closest']}'";
                    }

                    if (array_key_exists('so',$item)) {
						$canUp=true;
						$q.=",IndSO={$item['so']}";
					}
				}

				$q .= " WHERE IndId=" . $item['ath'] . " AND IndEvent='" . $item['event'] . "' AND IndTournament=" . $this->tournament;
				//print $q.'<br><br>';

				if (!$canUp) {
					return false;
				}
				$r=safe_w_sql($q);

				$affected+=safe_w_affected_rows();

				if(empty($item['dist']) and array_key_exists('rank',$item) and array_key_exists('hits',$item) and $item['hits']%3 == 0 ) {
					$q = "INSERT INTO IndOldPositions (IopId, IopEvent, IopTournament, IopHits, IopRank) "
						. "VALUES(" . $item['ath'] . ",'" . $item['event'] . "'," . $this->tournament . "," . $item['hits'] . "," . $item['rank'] . ") "
						. "ON DUPLICATE KEY UPDATE IopRank=" . $item['rank'];
					safe_w_sql($q);
				}

			}

			return $affected;
		}

	}
