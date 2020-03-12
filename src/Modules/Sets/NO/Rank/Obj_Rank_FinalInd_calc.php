<?php
/**
 * Obj_Rank_FinalInd
 *
 * Implementa l'algoritmo di default per il calcolo della rank finale individuale.
 *
 * La tabella in cui scrive è Individuals e popola la RankFinal "a pezzi". Solo alla fine della gara
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
 *
 * con:
 * 	 eventsC: l'array con le coppie evento@fase di cui voglio il calcolo.
 * 	 	I valori calcPhase_n servono a calculate() per scegliere da dove prelevare i dati e come gestirli per l'evento accoppiato perchè
 * 			a seconda che ci siano o no le eliminatorie, che si stia passando dagli assoluti alle finali o da un girone
 * 			eliminatorio all'altro avrò comportamenti diversi.
 * 			I valori sono:
 * 				-3: sto arrivando dalle qualifiche
 * 				-2: sto arrivando dall'ultimo girone eliminatorio
 * 				-1: sto arrivando dal primo girone eliminatorio
 * 			 	0: oro 				--> assegna le rank 1,2
 * 			 	1: bronzo				--> assegna le rank 3,4
 * 			 	2: semifinali			--> non assegna nulla
 * 			 	4: quarti				--> assegna le rank dalla 5 all'8 con la regola setpoints=>cumulativo=>tie
 * 			 	8: ottavi				--> assegna le rank dalla 9 alla 16 mettendo tutti a 9
 * 			 	16: sedicesimi			--> assegna le rank dalla 17 alla 32 mettendo tutti a 17
 * 			 	24: ventiquattresimi 	--> assegna le rank dalla 17 alla 32 mettendo tutti a 33
 * 			 	32: trentaduesimi 		--> assegna le rank dalla 17 alla 32 mettendo tutti a 33
 * 			 	48: quarantottesimi 	--> assegna le rank dalla 34 in su mettendo tutti a 49
 * 			 	64: sessantaquattresimi --> assegna le rank dalla 65 al 128 con la regola setpoints=>cumulativo=>tie
 * 			IMPORTANTE!
 * 				il valore di calcPhase rappresenta la fase che è appena terminata e per la quale vogliamo calcolare
 * 				la rank. Ad esempio se abbiamo risolto gli spareggi delle quelifiche verso le finali dobbiamo usare -3
 * 				per calcolare la RankFinal di quelli che non sono passati.
 *	eventsR: l'array con gli eventi per cui si vuole la classifica.
 *  tournament: Se impostato è l'id del torneo su cui operare altrimenti prende quello in sessione.
 *
 * Estende Obj_Rank
 */
	class Obj_Rank_FinalInd_calc extends Obj_Rank_FinalInd
	{
	/**
	 * writeRow()
	 * Fa l'update in Individuals
	 * @param int $id: id della persona
	 * @param string $event: evento
	 * @param int $rank: rank da scrivere
	 * @return boolean: true ok false altrimenti
	 */
		protected function writeRow($id,$event,$rank)
		{
			$date=date('Y-m-d H:i:s');
			$q="
				UPDATE
					Individuals
				SET
					IndRankFinal={$rank},
					IndTimestampFinal='{$date}'
				WHERE
					IndTournament={$this->tournament} AND IndEvent='{$event}' AND IndId={$id}
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
					Individuals
					INNER JOIN
						Events
					ON IndEvent=EvCode AND IndTournament=EvTournament
				SET
					IndRankFinal=IF(IndRank>IF(EvElim1=0 && EvElim2=0, EvNumQualified ,IF(EvElim1=0,EvElim2,EvElim1)),IndRank,0),
					IndTimestampFinal='{$date}'
				WHERE
					IndTournament={$this->tournament} AND EvCode='{$event}' AND EvTeamEvent=0

			";
			//print $q;exit;
			return (safe_w_sql($q)!==false);
		}

	/**
	 * calcFromElim1()
	 * Calcola la RankFinal di chi si è fermato al primo girone
	 * (inteso come fase 0)
	 *
	 * @param string $event: evento su cui lavorare
	 * @return bool: true ok false altrimenti
	 */
		protected function calcFromElim1($event)
		{
		/*
		 * Se passo di qui devo finalizzare la colonna IndRankFinal in Individuals
		 *
		 * Prendo l'ElRank di chi si è fermato (quelli con la ElRank># dei passati alla fase 1)
		 * e copio dentro a IndRankFinal
		 */
			$num=0;
			$q="SELECT EvElim2 AS `Num` FROM Events WHERE EvCode='{$event}' AND EvTournament={$this->tournament} AND EvTeamEvent=0 ";
			$r=safe_r_sql($q);
			if ($r && safe_num_rows($r)==1)
				$num=safe_fetch($r)->Num;

			$date=date('Y-m-d H:i:s');

			$q="
				UPDATE
					Individuals
					INNER JOIN
						Eliminations
					ON IndId=ElId AND IndTournament=ElTournament AND IndEvent=ElEventCode AND ElElimPhase=0
				SET
					IndRankFinal=ElRank,
					IndTimestampFinal='{$date}'
				WHERE
					ElTournament={$this->tournament} AND ElEventCode='{$event}' AND ElElimPhase=0 AND ElRank>{$num}
			";
		//print $q	;exit;
			return (safe_w_sql($q)!==false);
		}

	/**
	 * calcFromElim2()
	 * Calcola la RankFinal di chi si è fermato al secondo girone
	 * (inteso come fase 1)
	 *
	 * @param string $event: evento su cui lavorare
	 * @return bool: true ok false altrimenti
	 */
		protected function calcFromElim2($event)
		{
		/*
		 * Se passo di qui devo finalizzare la colonna IndRankFinal in Individuals
		 *
		 * Prendo l'ElRank di chi si è fermato (quelli con la ElRank># di passati)
		 * e copio dentro a IndRankFinal
		 */
			$num=0;
			$q="SELECT EvNumQualified AS `Num` FROM Events WHERE EvCode='{$event}' AND EvTournament={$this->tournament} AND EvTeamEvent=0 ";
			$r=safe_r_sql($q);
			if ($r && safe_num_rows($r)==1)
				$num=safe_fetch($r)->Num;

			$date=date('Y-m-d H:i:s');

			$q="
				UPDATE
					Individuals
					INNER JOIN
						Eliminations
					ON IndId=ElId AND IndTournament=ElTournament AND IndEvent=ElEventCode AND ElElimPhase=1
				SET
					IndRankFinal=ElRank,
					IndTimestampFinal='{$date}'
				WHERE
					ElTournament={$this->tournament} AND ElEventCode='{$event}' AND ElElimPhase=1 AND ElRank>{$num}
			";
	//print $q;exit;
			return (safe_w_sql($q)!==false);
		}

	/**
	 * calcFromPhase()
	 * Calcola la FinalRank per un evento in una certa fase
	 * @param string $event: evento
	 * @param int $phase: fase
	 * @return boolean: true ok false altrimenti. In un ciclo il primo errore fa terminare il metodo con false!
	 */
		protected function calcFromPhase($event,$phase)
		{
		// trasformo la fase
			$phase=valueFirstPhase($phase);

			$date=date('Y-m-d H:i:s');

		// reset delle RankFinal della fase x le persone di quell'evento e quella fase
			$q="
				UPDATE
					Individuals
					INNER JOIN
						Finals
					ON IndId=FinAthlete AND IndTournament=FinTournament AND IndEvent=FinEvent
					INNER JOIN
						Grids
					ON FinMatchNo=GrMatchNo AND GrPhase={$phase}
				SET
					IndRankFinal=0,
					IndTimestampFinal='{$date}'
				WHERE
					GrPhase={$phase} AND IndTournament={$this->tournament} AND IndEvent='{$event}'
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
					f.FinAthlete AS AthId,
					f2.FinAthlete AS OppAthId,
					IF(EvMatchMode=0,f.FinScore,f.FinSetScore) AS Score, f.FinScore AS CumScore,f.FinTie AS Tie,
					IF(EvMatchMode=0,f2.FinScore,f2.FinSetScore) as OppScore, f2.FinScore AS OppCumScore,f2.FinTie as OppTie
				FROM
					Finals AS f

					INNER JOIN
						Finals AS f2
					ON f.FinEvent=f2.FinEvent AND f.FinMatchNo=IF((f.FinMatchNo % 2)=0,f2.FinMatchNo-1,f2.FinMatchNo+1) AND f.FinTournament=f2.FinTournament

					INNER JOIN
						Grids
					ON f.FinMatchNo=GrMatchNo

					INNER JOIN
						Events
					ON f.FinEvent=EvCode AND f.FinTournament=EvTournament AND EvTeamEvent=0

				WHERE
					f.FinTournament={$this->tournament} AND f.FinEvent='{$event}' AND GrPhase={$phase}
					AND (IF(EvMatchMode=0,f.FinScore,f.FinSetScore) < IF(EvMatchMode=0,f2.FinScore,f2.FinSetScore) OR (IF(EvMatchMode=0,f.FinScore,f.FinSetScore)=IF(EvMatchMode=0,f2.FinScore,f2.FinSetScore) AND f.FinTie < f2.FinTie))
				ORDER BY
					IF(EvMatchMode=0,f.FinScore,f.FinSetScore) DESC,f.FinScore DESC
			";
			//print $q.'<br><br>';
			$rs=safe_r_sql($q);

			if ($rs)
			{
				if (safe_num_rows($rs)>0)
				{
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
							$toWrite[]=array('event'=>$event,'id'=>$myRow->OppAthId,'rank'=>1);
						// perdente
							$toWrite[]=array('event'=>$event,'id'=>$myRow->AthId,'rank'=>2);
						}
						elseif ($phase==1)
						{
						// vincente
							$toWrite[]=array('event'=>$event,'id'=>$myRow->OppAthId,'rank'=>3);
						// perdente
							$toWrite[]=array('event'=>$event,'id'=>$myRow->AthId,'rank'=>4);
						}

						foreach ($toWrite as $values)
						{
							$x=$this->writeRow($values['id'],$values['event'],$values['rank']);
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
					 * Le posizioni partono dal numero della fase perchè poi l'incremento avverrà sempre.
					 * (@see Obj_Rank_FinalInd_calc base)
					 * I 1/48 quindi andranno inizializzati a 56 perchè la prima rank buona è 57.
					 * Con tutte le fase avverrno gli incrementi
					 */
						switch ($phase)
						{
							case 4:
								$pos=4;		// si parte da 5 quindi qui metto 4 dato che la fase è 4 (vedi commento qui sopra)
								break;
							case 8:
								$pos=8;
								break;
							case 16:
								$pos=16;
								break;
							case 32: // (e 24)
								$pos=32;
								break;
							case 48:
								$pos=56;
								break;
							case 64:
								$pos=64;
								break;
							default:
								return false;
						}

						$rank=$pos+1;

						$scoreOld=0;
						$cumOld=0;

						while ($myRow=safe_fetch($rs))
						{

							++$pos;
							//print $pos . ' - ';
							if (!($myRow->Score==$scoreOld && $myRow->CumScore==$cumOld))
							{
								$rank=$pos;
							}
							//print $rank.'<br>';

							$scoreOld=$myRow->Score;
							$cumOld=$myRow->CumScore;

						// devo scrivere solo il perdente
							$x=$this->writeRow($myRow->AthId,$event,$rank);

							if ($x===false)
								return false;
						}
					}
				}
			}
			else
			{
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
							$x=$this->calcFromElim2($event);
							break;
						case -1:
							$x=$this->calcFromElim1($event);
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

			return true;
		}

	}
