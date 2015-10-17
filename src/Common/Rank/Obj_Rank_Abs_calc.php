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
		public function calculate()
		{
			$dd = ($this->opts['dist']>0 ? 'D' . $this->opts['dist'] : '');

			$f=$this->safeFilter();
			$filter=($f!==false ? $f : "");
			
			$q="
				SELECT
					IndId AS `athId`,IndEvent AS `EventCode`,
					Qu{$dd}Score AS Score,Qu{$dd}Gold AS Gold,Qu{$dd}Xnine AS XNine, Qu{$dd}Hits AS Hits, IndRank as actualRank,
					EvFinalFirstPhase, EvElim1, EvElim2,
					IF(EvFinalFirstPhase=0,9999,IF(EvElim1=0 && EvElim2=0, IF(EvFinalFirstPhase=48, 104, IF(EvFinalFirstPhase=24, 56, (EvFinalFirstPhase*2))) ,IF(EvElim1=0,EvElim2,EvElim1))) as QualifiedNo
				FROM
					Events

					INNER JOIN
						Individuals
					ON EvCode=IndEvent AND EvTournament=IndTournament AND EvTeamEvent=0

					INNER JOIN
						Qualifications
					ON IndId=QuId
				WHERE
					IndTournament={$this->tournament}
			".(empty($this->opts['includeNullPoints'])? " AND QuScore != 0 " : "")."
					{$filter}
				ORDER BY
					IndEvent,Qu{$dd}Score DESC,Qu{$dd}Gold DESC,Qu{$dd}Xnine DESC
			";
				//print $q.'<br><br>';
			$r=safe_r_sql($q);


			if (!$r)
				return false;

			if (safe_num_rows($r)>0)
			{
				$curGroup = "";
				$myRank = 1;
				$myPos = 0;
				$endQualified = false;

				$myScoreOld = 0;
				$myGoldOld = 0;
				$myXNineOld = 0;
				$mySoScore=array();
				$myGroupStartPos=0;
				$currentRow=-1;

				while($myRow=safe_fetch($r))
				{
					++$currentRow;

					if ($curGroup != $myRow->EventCode)
					{
						$curGroup = $myRow->EventCode;

						$myRank = 1;
						$myPos = 0;
						$myScoreOld = 0;
						$myGoldOld = 0;
						$myXNineOld = 0;
						$mySoScore=array();
						$endQualified = false;
						$myGroupStartPos = $currentRow;


					/*
					 * If starting phase is 1/48 or 1/24, I check the 8th position for shootoff,
					 */
						if(($myRow->EvFinalFirstPhase == 48 || $myRow->EvFinalFirstPhase == 24) && $myRow->EvElim1==0 && $myRow->EvElim2 ==0)
						{
							if(safe_num_rows($r) > ($myGroupStartPos + 8))
							{
								safe_data_seek($r,$myGroupStartPos + 8 - 1);
								$tmpMyRow = safe_fetch($r);
								if($curGroup == $tmpMyRow->EventCode)
								{
									$tmpScore = $tmpMyRow->Score;
									$tmpMyRow = safe_fetch($r);
									//Controllo se c'è parimerito per entrare
									if ($tmpScore == $tmpMyRow->Score && $curGroup == $tmpMyRow->EventCode)
										$mySoScore[] = $tmpScore;
								}
								$tmpMyRow = NULL;
							}
							safe_data_seek($r,$myGroupStartPos+1);
						}

					/*
					 * Carico l'ultimo punteggio per entrare.
					 * Vado a prendere la riga con l'ultimo Score buono
					 */
						if(safe_num_rows($r) > ($myGroupStartPos + $myRow->QualifiedNo))
						{
							safe_data_seek($r,$myGroupStartPos + $myRow->QualifiedNo -1);
							$tmpMyRow = safe_fetch($r);
							if($curGroup == $tmpMyRow->EventCode)
							{
								$tmpScore = $tmpMyRow->Score;
								$tmpMyRow = safe_fetch($r);
								//Controllo se c'è parimerito per entrare
								if ($tmpScore == $tmpMyRow->Score && $curGroup == $tmpMyRow->EventCode)
									$mySoScore[] = $tmpScore;
							}
							$tmpMyRow = NULL;
						}
						safe_data_seek($r,$myGroupStartPos+1);
					}
					++$myPos;

					$so=-1;

				// Se non ho parimerito il ranking è uguale alla posizione
					if(in_array($myRow->Score,$mySoScore))  //so che c'è uno spareggio per come ho caricato $mySoScore
					{
						if ($myRow->Score!=$myScoreOld)
							$myRank = $myPos;

						$so=1;	// rosso

					}
					else	//tutti gli altri pareggi...
					{
						if (!($myRow->Score==$myScoreOld && $myRow->Gold==$myGoldOld && $myRow->XNine==$myXNineOld))
							$myRank = $myPos;
					}
					if($myRank>$myRow->QualifiedNo)
						$so=0;

					$myScoreOld = $myRow->Score;
					$myGoldOld = $myRow->Gold;
					$myXNineOld = $myRow->XNine;

					$x = false;
					if($this->opts['dist']==0 && $myRow->actualRank!=0 && array_key_exists('skipExisting',$this->opts) && $this->opts['skipExisting']==1)
					{
						$x=$this->setRow(array(
							array(	// passo 1 item alla volta
								'ath' 		=> $myRow->athId,
								'event'		=> $myRow->EventCode,
								'dist'		=> $this->opts['dist'],
								'hits'		=> $myRow->Hits,
								'so'		=> ($so * $myRank)
							)
						));
					}
					else
					{
						$x=$this->setRow(array(
							array(	// passo 1 item alla volta
								'ath' 		=> $myRow->athId,
								'event'		=> $myRow->EventCode,
								'dist'		=> $this->opts['dist'],
								'hits'		=> $myRow->Hits,
								'rank'		=> $myRank,
								'tiebreak'	=> '',
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
		public function setRow($items=array())
		{
		// campi mandatory per $item
			$params=array('ath','event','dist');

			$affected=0;

			foreach ($items as $item)
			{
				/*print '<pre>';
				print_r($item);
				print '</pre>';*/

				$paramsOk=true;

				$canUp=false;

		/*
		 *  controllo che ci siano i campi mandatory
		 */
				foreach ($params as $p)
				{
					if (!array_key_exists($p,$item))
					{
						$paramsOk=false;
						$ret=false;
						break;
					}
				}

				if (!$paramsOk) continue;

				$dd = ($item['dist'] ? 'D' . $item['dist'] : '');

				$date=date('Y-m-d H:i:s');

				$q
					= "UPDATE "
						. "Individuals "
					. "SET "
						. "IndTimestamp='{$date}' "
				;

			/* campi opzionali e basta */
				if (array_key_exists('rank',$item))
				{
					$canUp=true;
					$q.=",Ind{$dd}Rank={$item['rank']}";
				}

			/*
			 *  campi opzionali (se dist==0).
			 *  In ogni caso i valori vengono scritti se e solo se la rank nuova è diversa dalla vecchia!
			 */
				if ($item['dist']==0)
				{
					if (array_key_exists('tiebreak',$item))
					{
						$canUp=true;
						$q.=",IndTiebreak='{$item['tiebreak']}'";
					}

					if (array_key_exists('so',$item))
					{
						$canUp=true;
						$q.=",IndSO={$item['so']}";
					}
				}

				$q
					.=" WHERE "
						. "IndId=" . $item['ath'] . " AND IndEvent='" . $item['event'] . "' AND IndTournament=" . $this->tournament . " ";
				;
				//print $q.'<br><br>';

				if (!$canUp) {
					return false;
				}
				$r=safe_w_sql($q);

				if (!$r) {
					$affected=false;
				} else {
					$affected+=safe_w_affected_rows();
				}
				
				if(empty($item['fist']) && array_key_exists('rank',$item) && array_key_exists('hits',$item) && $item['hits']%3 == 0 ) {
					$q = "INSERT INTO IndOldPositions (IopId, IopEvent, IopTournament, IopHits, IopRank) "
						. "VALUES(" . $item['ath'] . ",'" . $item['event'] . "'," . $this->tournament . "," . $item['hits'] . "," . $item['rank'] . ") "
						. "ON DUPLICATE KEY UPDATE IopRank=" . $item['rank'];
					safe_w_sql($q);
				}

			}

			return $affected;
		}

	}
