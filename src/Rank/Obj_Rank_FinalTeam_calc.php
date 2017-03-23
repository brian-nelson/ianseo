<?php
/**
 * Obj_Rank_FinalTeam
 *
 * Implementa l'algoritmo di default per il calcolo della rank finale a squadre.
 *
 * La tabella in cui scrive è Teams e popola la RankFinal "a pezzi". Solo alla fine della gara
 * avremo tutta la colonna valorizzata.
 *
 * A seconda della fase che sto trattando avrò porzioni di colonna da gestire differenti e calcoli differenti.
 *
 * Per questa classe $opts ha la seguente forma:
 *
 * array(
 * 		eventsC => array(<ev_1>@<calcPhase_1>,<ev_2>@<calcPhase_2>,...,<ev_n>@<calcPhase_n>)			[calculate,non influisce su read]
 * 		eventsR => array(<ev_1>,...,<ev_n>)																[read,non influisce su calculate]
 * 		tournament => #																					[calculate/read]
 * )
 */
	class Obj_Rank_FinalTeam_calc extends Obj_Rank_FinalTeam
	{
	/**
	 * writeRow()
	 * Fa l'update in Teams
	 * @param int $id: id della persona
	 * @param string $event: evento
	 * @param int $rank: rank da scrivere
	 * @return boolean: true ok false altrimenti
	 */
		protected function writeRow($id,$subteam,$event,$rank)
		{
			$date=date('Y-m-d H:i:s');
			$q="
				UPDATE
					Teams
				SET
					TeRankFinal={$rank},
					TeTimeStampFinal='{$date}'
				WHERE
					TeTournament={$this->tournament} AND TeEvent='{$event}' AND TeCoId={$id} AND TeSubTeam='{$subteam}'
			";
			//print $q.'<br><br>';
			$r=safe_w_sql($q);

			return ($r!==false);
		}


	/*
	 * **************************************************************
	 *
	 * Micro algoritmi da chiamare a seconda del punto di inizio
	 *
	 * **************************************************************
	 */

	/**
	 * calcFromAbs()
	 * Calcola la RankFinal di chi si è fermato agli assoluti.
	 *
	 * @param string $event: evento su cui lavorare
	 * @return bool: true ok false altrimenti
	 */
		protected function calcFromAbs($event)
		{
			$date=date('Y-m-d H:i:s');

			$q="
				UPDATE
					Teams
					INNER JOIN
						Events
					ON TeEvent=EvCode AND TeTournament=EvTournament AND TeFinEvent=1
				SET
					TeRankFinal=IF(TeRank>IF(EvFinalFirstPhase=48, 104, IF(EvFinalFirstPhase=24, 56,(EvFinalFirstPhase*2))),TeRank,0),
					TeTimeStampFinal='{$date}'
				WHERE
					TeTournament={$this->tournament} AND EvCode='{$event}' AND EvTeamEvent=1

			";
			//print $q.'<br><br>';
			$r=safe_w_sql($q);

			return ($r!==false);
		}

	/**
	 * calcFromPhase()
	 * Calcola la FinalRank per un evento in una certa fase
	 * @param string $event: evento
	 * @param int $phase: fase
	 * @return boolean: true ok false altrimenti. In un ciclo il primo errore fa terminare il metodo con false!
	 */
		protected function calcFromPhase($event, $phase, $FirstCycle=true) {
			// trasformo la fase
			$phase=valueFirstPhase($phase);

			$date=date('Y-m-d H:i:s');

		// reset delle RankFinal della fase x le persone di quell'evento e quella fase
			$q="
				UPDATE
					Teams
					INNER JOIN
						TeamFinals
					ON TeCoId=TfTeam AND TeSubTeam=TfSubTeam AND TeTournament=TfTournament AND TeEvent=TfEvent AND TeFinEvent=1
					INNER JOIN
						Grids
					ON TfMatchNo=GrMatchNo AND GrPhase={$phase}
				SET
					TeRankFinal=0,
					TeTimeStampFinal='{$date}'
				WHERE
					GrPhase={$phase} AND TeTournament={$this->tournament} AND TeEvent='{$event}' AND TeFinEvent=1
			";
			//print $q.'<br><br>';
			$r=safe_w_sql($q);
			if (!$r)
				return false;

		/*
		 *  Tiro fuori gli scontri con i perdenti nei non Opp
		 */
			$q="
				SELECT
					tf.TfTeam AS TeamId,
					tf.TfSubTeam AS SubTeam,
					tf2.TfTeam AS OppTeamId,
					tf2.TfSubTeam AS OppSubTeam,
					IF(EvMatchMode=0,tf.TfScore,tf.TfSetScore) AS Score, tf.TfScore AS CumScore,tf.TfTie AS Tie,
					IF(EvMatchMode=0,tf2.TfScore,tf2.TfSetScore) as OppScore, tf2.TfScore AS OppCumScore,tf2.TfTie as OppTie

				FROM
					TeamFinals AS tf

					INNER JOIN
						TeamFinals AS tf2
					ON tf.TfEvent=tf2.TfEvent AND tf.TfMatchNo=IF((tf.TfMatchNo % 2)=0,tf2.TfMatchNo-1,tf2.TfMatchNo+1) AND tf.TfTournament=tf2.TfTournament

					INNER JOIN
						Grids
					ON tf.TfMatchNo=GrMatchNo

					INNER JOIN
						Events
					ON tf.TfEvent=EvCode AND tf.TfTournament=EvTournament AND EvTeamEvent=1

				WHERE
					tf.TfTournament={$this->tournament} AND tf.TfEvent='{$event}' AND GrPhase={$phase}
					AND (tf.TfWinLose=1 OR tf2.TfWinLose=1)
					AND (IF(EvMatchMode=0,tf.TfScore,tf.TfSetScore) < IF(EvMatchMode=0,tf2.TfScore,tf2.TfSetScore) OR (IF(EvMatchMode=0,tf.TfScore,tf.TfSetScore)=IF(EvMatchMode=0,tf2.TfScore,tf2.TfSetScore) AND tf.TfTie < tf2.TfTie))
				ORDER BY
					IF(EvMatchMode=0,tf.TfScore,tf.TfSetScore) DESC,tf.TfScore DESC
			";

			$rs=safe_r_sql($q);

			if ($rs) {
				if (safe_num_rows($rs)>0) {
				/*
				 * Se fase 0 (oro) il perdente ha la rank=2 e il vincente piglia 1,
				 * se fase 1 (bronzo) il perdente ha la rank=4 e il vincete piglia 3
				 * e in entrambi i casi avrò sempre e solo una riga.
				 *
				 * Se fase 2 (semi) non succede nulla.
				 *
				 * Per le altre fasi si cicla nel recordset che ha il numero di righe >=0
				 */
					if ($phase==0 || $phase==1)
					{
						$myRow=safe_fetch($rs);

						$toWrite=array();

						if ($phase==0)
						{
						// vincente
							$toWrite[]=array('event'=>$event,'id'=>$myRow->OppTeamId,'subteam'=>$myRow->OppSubTeam, 'rank'=>1);
						// perdente
							$toWrite[]=array('event'=>$event,'id'=>$myRow->TeamId,'subteam'=>$myRow->SubTeam, 'rank'=>2);
						}
						elseif ($phase==1)
						{
						// vincente
							$toWrite[]=array('event'=>$event,'id'=>$myRow->OppTeamId,'subteam'=>$myRow->OppSubTeam, 'rank'=>3);
						// perdente
							$toWrite[]=array('event'=>$event,'id'=>$myRow->TeamId,'subteam'=>$myRow->SubTeam, 'rank'=>4);
						}

						foreach ($toWrite as $values)
						{
							$x=$this->writeRow($values['id'],$values['subteam'], $values['event'],$values['rank']);
							if ($x===false)
								return false;
						}
					}
					elseif ($phase==2)
					{
					// non faccio nulla!
					}
					else
					{
					// qui posso avere tante righe
						$pos=0;

					/*
					 *  per la fase 4 pos viene inizializzato al valore iniziale -1
					 *  perchè poi nel ciclo come prima cosa ho un suo incremento dato che la if
					 *  che decide se incrementare o no sarà vera. Per gli altri non ci sarà
					 *  l'incremento così avrò sempre il valore iniziale (senza il -1)
					 */
						switch ($phase)
						{
							case 4:
								$pos=8-safe_num_rows($rs);		// dovendo partire dal fondo, recupero l'ultimo posto disponibile
								break;
							case 8:
								$pos=9;
								break;
							case 16:
								$pos=17;
								break;
							case 32: // (e 24)
								$pos=33;
								break;
							case 48:
								$pos=49;
								break;
							default:
								return false;
						}

						if ($phase==4)
						{
							$rank=$pos+1;
						}
						else
						{
							$rank=$pos;
						}

						$scoreOld=0;
						$cumOld=0;

						while ($myRow=safe_fetch($rs))
						{
							if ($phase==4)
							{
								++$pos;
								if (!($myRow->Score==$scoreOld && $myRow->CumScore==$cumOld))
								{
									$rank=$pos;
								}
							}

							$scoreOld=$myRow->Score;
							$cumOld=$myRow->CumScore;

						// devo scrivere solo il perdente
							$x=$this->writeRow($myRow->TeamId,$myRow->SubTeam,$event,$rank);

							if ($x===false)
								return false;
						}
					}
				}

				if($FirstCycle) {
					// get all ranked 0 with next matches already won...
					$q="
					SELECT distinct GrPhase

					FROM TeamFinals AS tf
					INNER JOIN Teams on TeCoId=tf.TfTeam and TeSubTeam=tf.TfSubTeam and TeTournament=tf.TfTournament and TeEvent=tf.TfEvent and TeRankFinal=0
					INNER JOIN Grids
						ON tf.TfMatchNo=GrMatchNo
					INNER JOIN TeamFinals AS tf2
						ON tf.TfEvent=tf2.TfEvent AND tf.TfMatchNo=IF((tf.TfMatchNo % 2)=0,tf2.TfMatchNo-1,tf2.TfMatchNo+1) AND tf.TfTournament=tf2.TfTournament
					INNER JOIN Events
						ON tf.TfEvent=EvCode AND tf.TfTournament=EvTournament AND EvTeamEvent=1
					LEFT JOIN
						(select nm1.TfWinLose+nm2.TfWinLose Winner, nm1.TfMatchNo, nm1.TfEvent
							from TeamFinals nm1
							inner join TeamFinals nm2 on nm1.TfTournament=nm2.TfTournament and nm1.TfEvent=nm2.TfEvent and nm1.TfMatchNo=IF((nm1.TfMatchNo % 2)=0,nm2.TfMatchNo-1,nm2.TfMatchNo+1)
							where nm1.TfTournament={$this->tournament} AND nm1.TfEvent='{$event}') NextMatch
						on NextMatch.TfMatchNo=floor(tf.TfMatchNo/2) and NextMatch.TfEvent=tf.TfEvent

					WHERE
						tf.TfTournament={$this->tournament} AND tf.TfEvent='{$event}'
					AND (
						IF(EvMatchMode=0,tf.TfScore,tf.TfSetScore) < IF(EvMatchMode=0,tf2.TfScore,tf2.TfSetScore)
						OR (IF(EvMatchMode=0,tf.TfScore,tf.TfSetScore)=IF(EvMatchMode=0,tf2.TfScore,tf2.TfSetScore) AND tf.TfTie < tf2.TfTie)
						OR (tf.TfWinLose+tf2.TfWinLose=0 and NextMatch.Winner>0)
						)
					ORDER BY
					IF(EvMatchMode=0,tf.TfScore,tf.TfSetScore) DESC,tf.TfScore DESC
					";
					$t=safe_r_sql($q);
					while($u=safe_fetch($t)) {
						$this->calcFromPhase($event, $u->GrPhase, false);
					}
				}
			} else {
				return false;
			}

			return true;
		}

	/*
	 * **************************************************************
	 *
	 * FINE Micro algoritmi da chiamare a seconda del punto di inizio
	 *
	 * **************************************************************
	 */

	/**
	 * calculate()
	 *
	 * Al primo errore termina con false!
	 *
	 * @Override
	 *
	 * (non-PHPdoc)
	 * @see ianseo/Common/Rank/Obj_Rank#calculate()
	 */
		public function calculate()
		{
			if (count($this->opts['eventsC'])>0)
			{
				foreach ($this->opts['eventsC'] as $c)
				{
					list($event,$phase)=explode('@',$c);

					$x=true;
					switch ($phase)
					{
						case -3:
							$x=$this->calcFromAbs($event);
							break;
						case -2:
							break;
						case -1:
							break;
						default:
						/*
						 * Qui devo ciclare a partire dalla fase passata fino agli ori.
						 * Il primo errore mi fa terminare il metodo con false
						 */
							foreach (getPhasesId() as $p)
							{
							// se sono in una fase > di quella passata ignoro
								if ($p>$phase)
								{
									continue;
								}
								$x=$this->calcFromPhase($event,$p);

								if ($x===false)
								{
									return false;
								}
							}
							break;
					}

					if ($x===false)
						return false;
				}
			}
		}
	}