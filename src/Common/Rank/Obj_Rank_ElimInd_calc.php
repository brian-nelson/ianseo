<?php
/**
 * Obj_Rank_ElimInd
 * Implementa l'algoritmo di default per il calcolo della rank dei gironi delle eliminatorie.
 *
 * La tabella in cui vengono scritti i valori è la Eliminations.
 *
 * Per questa classe $opts ha la seguente forma:
 *
 * array(
 * 		eventsC => array(<ev_1>@<calcPhase_1>,<ev_2>@<calcPhase_2>,...,<ev_n>@<calcPhase_n>)	[calculate,non influisce su read]
 * 		eventsR => array(<ev_1>@<phase_1>,<ev_2>@<phase_2>,...,<ev_n>@<phase_n>)				[read,non influisce su calculate], se presente events verrà ignorato
 * 		events =>  array(<ev_1>,<ev_2>,...,<ev_n>)												[read,non influisce su calculate]
 * 		skipExisting => #																		[calculate]
 * 		tournament => #																			[calculate/read]
 * )
 *
 * con:
 * 	 eventsC: l'array con le coppie evento@fase di cui voglio il calcolo.
 * 	 	I valori calcPhase_n servono a calculate() per scegliere se stiamo parlando del primo girone oppure del secondo.
 * 			I valori sono:
 * 				2: voglio calcolare il secondo girone
 * 				1: voglio calcolare il primo
 *	eventsR: come eventsC ma per la lettura
 *  events: array di eventi.
 *	tournament: Se impostato è l'id del torneo su cui operare altrimenti prende quello in sessione.
 *  skipExisting: Se 1 non sovrascrive posizione e frecce di SO dove sono già valorizzati
 *
 *  NOTA BENE:
 *  	In questa classe ci sono 3 elementi per i filtri: uno per la scrittura e gli altri per la lettura.
 *  	eventsC serve perchè per scrivere occorre sapere di che evento e di che girone si sta parlando; eventsR e events vanno usato
 *  	in mutua esclusione (se presente eventsR events verrà ignorato).
 *  	events estrae per gli eventi passati tutti i gironi ed è comodo per stampare le classifiche.
 *  	eventsR invece ha lo scopo di eventsC e torna utile per estrarre le classifiche per gli spareggi.
 *
 * $data ha la seguente forma
 *
 * array(
 * 		meta 		=> array(
 * 			title 	=> <titolo della classifica localizzato>
 *		),
 * 		sections 	=> array(
 * 			event_1 => array(
 * 				meta => array(
 * 					event => <event_1>, valore uguale alla chiave
 * 					descr => <descrizione evento localizzata>
 * 					qualifiedNo => <numero di persone qualificate per l'evento>
 * 					fields(*1) => array(
 *						id 				=> <id della persona>
 *                      bib 			=> <codice della persona>
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
 *                      ct				=> <numero di cointoss (gialli)>	(distanza 0)
 *                      so				=> <1 se shootoff (rosso)>			(distanza 0)
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
 * Estende Obj_Rank
 */
	class Obj_Rank_ElimInd_calc extends Obj_Rank_ElimInd
	{
	/**
	 * calculate()
	 * La classifica viene calcolata quando si cambia un punteggio oppure quando si risolvono gli
	 * spareggi per passare al girone dopo oppure alle finali.
	 * Nel primo caso si chiama direttamente questo metodo; nel secondo si userà setRow() utilizzata pure
	 * da questo metodo.
	 *
	 * @override
	 *
	 * (non-PHPdoc)
	 * @see ianseo/Common/Rank/Obj_Rank#calculate()
	 */
		public function calculate()
		{
			$f=$this->safeFilterC();
			$filter=($f!==false ? $f : "");

			$q="
				SELECT
					ElId,EnFirstName,ElEventCode,ElElimPhase,
					ElScore,ElGold, ElXnine, ElRank as actualRank,
					IF(ElElimPhase=0,EvElim2,EvNumQualified) AS QualifiedNo
				FROM
					Eliminations
					INNER JOIN
						Entries ON ElId=EnId
					INNER JOIN
						Events
					ON ElEventCode=EvCode AND ElTournament=EvTournament AND EvTeamEvent=0
				WHERE
					ElTournament={$this->tournament} {$filter}
				ORDER BY
					EvProgr, ElEventCode, ElElimPhase ASC, ElScore DESC, ElGold DESC, ElXnine DESC, ElTargetNo
			";
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

				$myEndScore=-1;
				$myGroupStartPos=0;
				$currentRow=-1;

				while($myRow=safe_fetch($r))
				{
					++$currentRow;
					if ($curGroup != $myRow->ElEventCode.$myRow->ElElimPhase)
					{
						$curGroup = $myRow->ElEventCode.$myRow->ElElimPhase;
						//print $curGroup.'<br>';
						$myRank = 1;
						$myPos = 0;
						$myScoreOld = 0;
						$myGoldOld = 0;
						$myXNineOld = 0;
						$endQualified = false;
						$myGroupStartPos = $currentRow;

						if(safe_num_rows($r) > ($myGroupStartPos + $myRow->QualifiedNo))
						{
							safe_data_seek($r,$myGroupStartPos + $myRow->QualifiedNo -1);
							$tmpMyRow = safe_fetch($r);
							//print_r($tmpMyRow);
							if($curGroup == $tmpMyRow->ElEventCode.$tmpMyRow->ElElimPhase)
							{
								$myEndScore = $tmpMyRow->ElScore;
								$tmpMyRow = safe_fetch($r);
								//print_r($tmpMyRow);
								//Controllo se c'è parimerito per entrare
								if ($myEndScore != $tmpMyRow->ElScore || $curGroup != $tmpMyRow->ElEventCode.$tmpMyRow->ElElimPhase)
								{
									$myEndScore *= -1;
								}
							}
							else
								$myEndScore = -1;
							$tmpMyRow = NULL;
						}
						else
						{
							safe_data_seek($r,safe_num_rows($r)-1);
							$tmpMyRow = safe_fetch($r);
							$myEndScore = -1;
						}
						safe_data_seek($r,$myGroupStartPos+1);
								//print $myEndScore;
					}

					++$myPos;
					$so=-1;

					if($myEndScore == $myRow->ElScore)  //Spareggio
					{
						if ($myRow->ElScore!=$myScoreOld)
							$myRank = $myPos;

						$so=1;	// rosso
					}
					else
					{
						if (!($myRow->ElScore==$myScoreOld && $myRow->ElGold==$myGoldOld && $myRow->ElXnine==$myXNineOld))
							$myRank = $myPos;
					}
					if($myRank>$myRow->QualifiedNo)
						$so=0;

					$myScoreOld = $myRow->ElScore;
					$myGoldOld = $myRow->ElGold;
					$myXNineOld = $myRow->ElXnine;
					$x = false;
					if($myRow->actualRank!=0 && array_key_exists('skipExisting',$this->opts) && $this->opts['skipExisting']==1)
					{
						$x=$this->setRow(array(
							array(	// passo 1 item alla volta
								'ath' 		=> $myRow->ElId,
								'event'		=> $myRow->ElEventCode,
								'phase'		=> $myRow->ElElimPhase,
								'so'		=> ($so * $myRank)
							)
						));
					}
					else
					{
						$x=$this->setRow(array(
							array(	// passo 1 item alla volta
								'ath' 		=> $myRow->ElId,
								'event'		=> $myRow->ElEventCode,
								'phase'		=> $myRow->ElElimPhase,
								'rank'		=> $myRank,
								'tiebreak'	=> '',
								'decoded'	=> '',
								'closest'	=> 0,
								'so'		=> ($so * $myRank)
							)
						));
					}
					/*print '<pre>';
					print_r($x);
					print '</pre>';*/
					if ($x===false)
						return false;
				}
			}

			return true;
		}

	/**
	 * setRow().
	 * Imposta le ElRank degli elementi passati.
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
	 *					phase		=> <phase> 	(chiave)
	 * 					rank 		=> <rank>
	 * 					so 			=> <so>
	 * 				)
	 * 			)
	 *		con <id> l'id della persona <ev> l'evento, <phase> il girone eliminatorio (0 vuol dire primo girone, 1 il secondo),
	 *		<rank> la rank da impostare (opzionale), e <so> un flag che vale 1 se ci sono spareggi (gialli o rossi, opzionale).
	 *
	 *		Deve essere presente almeno un campo opzionale se no il metodo ritorna errore.
	 *
	 *		NOTA BENE
	 *			Qui phase ha i valori del db quindi 0->I girone, 1->II girone!!!!
	 *
	 * @return mixed: ritorna le affected_rows oppure false se c'è qualche errore
	 * 		(non salva gli eventuali elementi successivi a quello che ha generato l'errore)
	 */
		public function setRow($items=array())
		{
		// campi mandatory per $item
			$params=array('ath','event','phase');

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

				$date=date('Y-m-d H:i:s');

				$q
					= "UPDATE "
						. "Eliminations "
					. "SET "
						. "ElDateTime='{$date}' "
				;

			/* campi opzionali e basta */
				if (array_key_exists('rank',$item)) {
					$canUp=true;
					$q.=",ElRank={$item['rank']}";
				}

				if (array_key_exists('so',$item)) {
					$canUp=true;
					$q.=",ElSO={$item['so']}";
				}

				if (array_key_exists('tiebreak',$item)) {
					$canUp=true;
					$q.=",ElTiebreak='{$item['tiebreak']}' ";
				}

				if (array_key_exists('decoded',$item)) {
					$canUp=true;
					$q.=",ElTbDecoded='{$item['decoded']}' ";
				}

                if (array_key_exists('closest',$item)) {
                    $canUp=true;
                    $q.=",ElTbClosest='{$item['closest']}' ";
                }

                $phase=$item['phase'];

				$q
					.=" WHERE "
						. "ElId=" . $item['ath'] . " AND ElTournament=" . $this->tournament . " AND ElElimPhase={$phase} AND ElEventCode='{$item['event']}'";
				;
				//print $q.'<br><br>';
				if (!$canUp)
					return false;

				$r=safe_w_sql($q);

				if (!$r)
				{
					$affected=false;
				}
				else
				{
					$affected+=safe_w_affected_rows();
				}
			}

			return $affected;
		}
	}
