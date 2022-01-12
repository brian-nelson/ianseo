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
	class Obj_Rank_FinalTeam_3_SetFRChampsD1DNAP_calc extends Obj_Rank_FinalTeam_3_SetFRChampsD1DNAP
	{
		protected $FromIndividual=false;

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
					TeRankFinal=IF(TeRank > EvNumQualified, TeRank, 0),
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
		protected function calcFromPhase($event, $realphase, $FirstCycle=true) {
			return;
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
					ON TfMatchNo=GrMatchNo AND GrPhase={$realphase}
				SET
					TeRankFinal=0,
					TeTimeStampFinal='{$date}'
				WHERE
					GrPhase={$realphase} AND TeTournament={$this->tournament} AND TeEvent='{$event}' AND TeFinEvent=1
			";
			//print $q.'<br><br>';
			$r=safe_w_sql($q);
			if (!$r)
				return false;

		/*
		 *  Tiro fuori gli scontri con i perdenti nei non Opp
		 */
			$q="
				SELECT EvWinnerFinalRank, EvCodeParent, SubCodes, EvFinalFirstPhase,
					tf.TfTeam AS TeamId,
					tf.TfSubTeam AS SubTeam,
					tf2.TfTeam AS OppTeamId,
					tf2.TfSubTeam AS OppSubTeam,
					IF(EvMatchMode=0,tf.TfScore,tf.TfSetScore) AS Score, tf.TfScore AS CumScore,tf.TfTie AS Tie,
					IF(EvMatchMode=0,tf2.TfScore,tf2.TfSetScore) as OppScore, tf2.TfScore AS OppCumScore,tf2.TfTie as OppTie

				FROM
					TeamFinals AS tf
					INNER JOIN TeamFinals AS tf2 ON tf.TfEvent=tf2.TfEvent AND tf.TfMatchNo=IF((tf.TfMatchNo % 2)=0,tf2.TfMatchNo-1,tf2.TfMatchNo+1) AND tf.TfTournament=tf2.TfTournament
					INNER JOIN Grids ON tf.TfMatchNo=GrMatchNo
					INNER JOIN Events ON tf.TfEvent=EvCode AND tf.TfTournament=EvTournament AND EvTeamEvent=1
					left join (select group_concat(DISTINCT concat(EvCode, '@', EvFinalFirstPhase)) SubCodes, EvCodeParent SubMainCode, EvFinalFirstPhase SubFirstPhase from Events where EvCodeParent!='' and EvTeamEvent=1 and EvTournament={$this->tournament} group by EvCodeParent, EvFinalFirstPhase) Secondary on SubMainCode=EvCode and SubFirstPhase=GrPhase/2
				WHERE
					tf.TfTournament={$this->tournament} AND tf.TfEvent='{$event}' AND GrPhase={$realphase}
					AND (tf.TfNotes='DNS' or ((tf.TfWinLose=1 OR tf2.TfWinLose=1)
					AND (IF(EvMatchMode=0,tf.TfScore,tf.TfSetScore) < IF(EvMatchMode=0,tf2.TfScore,tf2.TfSetScore) OR (IF(EvMatchMode=0,tf.TfScore,tf.TfSetScore)=IF(EvMatchMode=0,tf2.TfScore,tf2.TfSetScore) AND tf.TfTie < tf2.TfTie))))
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

					$myRow=safe_fetch($rs);

					// trasformo la fase
					$phase=namePhase($myRow->EvFinalFirstPhase, $realphase);

					// get the parent chain for this event if any
					$EventToUse=$event;
					$ParentCode=$myRow->EvCodeParent;
					while($ParentCode) {
						$EventToUse=$ParentCode;
						$t=safe_r_sql("select EvCodeParent from Events where EvCode=".StrSafe_DB($ParentCode));
						if($u=safe_fetch($t)) {
							$ParentCode=$u->EvCodeParent;
						} else {
							$ParentCode='';
						}
					}

					if ($phase==0 || $phase==1) {

						$toWrite=array();

						if ($phase==0)
						{
						// vincente
							$toWrite[]=array('event'=>$EventToUse,'id'=>$myRow->OppTeamId,'subteam'=>$myRow->OppSubTeam, 'rank'=>$myRow->EvWinnerFinalRank);
						// perdente
							$toWrite[]=array('event'=>$EventToUse,'id'=>$myRow->TeamId,'subteam'=>$myRow->SubTeam, 'rank'=>$myRow->EvWinnerFinalRank+1);
						}
						elseif ($phase==1)
						{
						// vincente
							$toWrite[]=array('event'=>$EventToUse,'id'=>$myRow->OppTeamId,'subteam'=>$myRow->OppSubTeam, 'rank'=>$myRow->EvWinnerFinalRank+2);
						// perdente
							$toWrite[]=array('event'=>$EventToUse,'id'=>$myRow->TeamId,'subteam'=>$myRow->SubTeam, 'rank'=>$myRow->EvWinnerFinalRank+3);
						}
						foreach ($toWrite as $values)
						{
							$x=$this->writeRow($values['id'],$values['subteam'], $values['event'],$values['rank']);
							if ($x===false)
								return false;
						}
					}
					elseif ($phase==2 or $myRow->SubCodes)
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
						if($realphase==4) {
							// dovendo partire dal fondo, recupero l'ultimo posto disponibile
							$pos=max(4, 8-safe_num_rows($rs));
						} elseif($realphase>4) {
							$pos=numMatchesByPhase($phase)+SavedInPhase($phase)+1;
						} else {
							// no need to rerank
							return false;
						}
						//switch ($phase)
						//{
						//	case 4:
						//		break;
						//	case 8:
						//		$pos=9;
						//		break;
						//	case 16:
						//		$pos=17;
						//		break;
						//	case 32: // (e 24)
						//		$pos=33;
						//		break;
						//	case 48:
						//		$pos=49;
						//		break;
						//	default:
						//		return false;
						//}

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

						while ($myRow) {
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
							$x=$this->writeRow($myRow->TeamId,$myRow->SubTeam,$event,$rank+$myRow->EvWinnerFinalRank-1);

							if ($x===false) {
								return false;
							}

							$myRow=safe_fetch($rs);
						}
					}
				}


				if($FirstCycle) {
					// get all ranked 0 with next matches already won...
					$q="
					SELECT distinct GrPhase

					FROM TeamFinals AS tf
					INNER JOIN Teams on TeCoId=tf.TfTeam and TeSubTeam=tf.TfSubTeam and TeTournament=tf.TfTournament and TeEvent=tf.TfEvent and TeRankFinal=0 and TeFinEvent=1
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
						//echo "<div>$event, $u->GrPhase</div>";
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
	public function calculate(){
		if (count($this->opts['eventsC'])>0) {
			foreach ($this->opts['eventsC'] as $c) {
				list($event,$phase)=explode('@',$c);
				if(getModuleParameter('FFTA', 'D1AllInOne', 0) and strlen($event)==4) {
					require_once('Common/Rank/Obj_Rank_FinalTeam.php');
					require_once('Common/Rank/Obj_Rank_FinalTeam_calc.php');
					$tmp=new Obj_Rank_FinalTeam_calc($this->opts);
					return $tmp->calculate();
				}

				$x=true;
				switch ($phase) {
					case -3:
						$x=$this->calcFromAbs($event);
						break;
					case -2:
						break;
					case -1:
						break;
					default:
						/*
						 * D1 has only one phase but team ranks are caluclated like this
						 * EACH match assigns 1 point to the winner in individual and 2 in team
						 * EACH group of matches (game) assigns 2 points to the winner, tie is resolved through the winner of the team match
						 *
						 * in 2020 change of points: After 5 matches the winning team gets 3 poiints, in case of tie 1 point each
						 * in 2021 another change: $AllInOne is set then onl team matches are done
						 */
						$TeamDavis=array();
						$Bonus=getModuleParameter('FFTA', 'D1Bonus');
						$YEAR = substr($_SESSION['TourRealWhenFrom'],0,4);
						if($YEAR>=2020 and !$this->AllInOne) {
							$MatchWinner=3;
						} else {
							$MatchWinner=2;
						}

						// calculates the starting situation
						if($this->FromIndividual) {
							$event=substr($event,0,-1);
						}
						if($this->AllInOne) {
							$SQL="select 
	                                c1.CoCode as Team1,
	                                c2.CoCode as Team2,
	                                tf1.TfSubTeam as SubTeam1,
	                                tf2.TfSubTeam as SubTeam2,
	                                if(EvMatchMode, tf1.TfSetScore, tf1.TfScore) as WinPoints1,
	                                if(EvMatchMode, tf2.TfSetScore, tf2.TfScore) as WinPoints2,
	                                tf1.TfWinLose as Winner1,
	                                tf2.TfWinLose as Winner2,
	                                t1.TeRank as Rank1,
	                                t2.TeRank as Rank2,
       								EvMatchMode
								from TeamFinals tf1
								inner join Teams t1 on t1.TeTournament=tf1.TfTournament and t1.TeEvent=tf1.TfEvent and t1.TeCoId=tf1.TfTeam and t1.TeSubTeam=tf1.TfSubTeam and t1.TeFinEvent=1
								inner join Countries c1 on c1.CoId=tf1.TfTeam and c1.CoTournament=tf1.TfTournament
								inner join TeamFinals tf2 on tf2.TfEvent=tf1.TfEvent and tf2.TfTournament=tf1.TfTournament and tf2.TfMatchNo=tf1.TfMatchNo+1
								inner join Teams t2 on t2.TeTournament=tf2.TfTournament and t2.TeEvent=tf2.TfEvent and t2.TeCoId=tf2.TfTeam and t2.TeSubTeam=tf2.TfSubTeam and t2.TeFinEvent=1
								inner join Countries c2 on c2.CoId=tf2.TfTeam and c2.CoTournament=tf2.TfTournament
							    inner join Events on EvTournament=tf1.TfTournament and EvTeamEvent=1 and EvCode=tf1.TfEvent
								where tf1.TfTournament={$this->tournament} and tf1.TfEvent='$event' and tf1.TfMatchNo%2=0
								group by tf1.TfMatchNo, tf2.TfMatchNo";

							$q=safe_r_sql($SQL);
							while($r=safe_fetch($q)) {
								if(empty($TeamDavis["$r->Team1"][$r->SubTeam1])) {
									$TeamDavis["$r->Team1"][$r->SubTeam1]=array('b'=>(isset($Bonus[$event][$r->Rank1]) ? intval($Bonus[$event][$r->Rank1]) : 0),'mp'=>0,'wp'=>0,'lp'=>0);
								}
								if(empty($TeamDavis["$r->Team2"][$r->SubTeam2])) {
									$TeamDavis["$r->Team2"][$r->SubTeam2]=array('b'=>(isset($Bonus[$event][$r->Rank2]) ? intval($Bonus[$event][$r->Rank2]) : 0),'mp'=>0,'wp'=>0,'lp'=>0);
								}
								$TeamDavis["$r->Team1"][$r->SubTeam1]['mp']+=0;
								$TeamDavis["$r->Team1"][$r->SubTeam1]['wp']+=$r->WinPoints1;
								if($r->EvMatchMode) {
									$TeamDavis["$r->Team1"][$r->SubTeam1]['lp']+=$r->WinPoints2;
								}
								$TeamDavis["$r->Team2"][$r->SubTeam2]['mp']+=0;
								$TeamDavis["$r->Team2"][$r->SubTeam2]['wp']+=$r->WinPoints2;
								if($r->EvMatchMode) {
									$TeamDavis["$r->Team2"][$r->SubTeam2]['lp']+=$r->WinPoints1;
								}
								if($r->Winner1) {
									$TeamDavis["$r->Team1"][$r->SubTeam1]['mp'] += $MatchWinner;
								} elseif($r->Winner2) {
									$TeamDavis["$r->Team2"][$r->SubTeam2]['mp'] += $MatchWinner;
								}
							}
						} else {
							$SQL="select 
	                                c1.CoCode as Team1,
	                                c2.CoCode as Team2,
	                                tf1.TfSubTeam as SubTeam1,
	                                tf2.TfSubTeam as SubTeam2,
	                                tf1.TfWinLose*2 + sum(f1.FinWinLose) as WinPoints1,
	                                tf2.TfWinLose*2 + sum(f2.FinWinLose) as WinPoints2,
	                                tf1.TfWinLose as Winner1,
	                                tf2.TfWinLose as Winner2,
	                                t1.TeRank as Rank1,
	                                t2.TeRank as Rank2
								from TeamFinals tf1
								inner join Teams t1 on t1.TeTournament=tf1.TfTournament and t1.TeEvent=tf1.TfEvent and t1.TeCoId=tf1.TfTeam and t1.TeSubTeam=tf1.TfSubTeam and t1.TeFinEvent=1
								inner join Countries c1 on c1.CoId=tf1.TfTeam and c1.CoTournament=tf1.TfTournament
								inner join TeamFinals tf2 on tf2.TfEvent=tf1.TfEvent and tf2.TfTournament=tf1.TfTournament and tf2.TfMatchNo=tf1.TfMatchNo+1
								inner join Teams t2 on t2.TeTournament=tf2.TfTournament and t2.TeEvent=tf2.TfEvent and t2.TeCoId=tf2.TfTeam and t2.TeSubTeam=tf2.TfSubTeam and t2.TeFinEvent=1
								inner join Countries c2 on c2.CoId=tf2.TfTeam and c2.CoTournament=tf2.TfTournament
								inner join Finals f1 on f1.FinEvent like concat(tf1.TfEvent,'%') and f1.FinTournament=tf1.TfTournament and f1.FinMatchNo=tf1.TfMatchNo
								inner join Finals f2 on f2.FinEvent = f1.FinEvent and f2.FinTournament=tf2.TfTournament and f2.FinMatchNo=tf2.TfMatchNo
								where tf1.TfTournament={$this->tournament} and tf1.TfEvent='$event' and tf1.TfMatchNo%2=0
								group by tf1.TfMatchNo, tf2.TfMatchNo";

							$q=safe_r_sql($SQL);
							while($r=safe_fetch($q)) {
								if(empty($TeamDavis["$r->Team1"][$r->SubTeam1])) {
									$TeamDavis["$r->Team1"][$r->SubTeam1]=array('b'=>(isset($Bonus[$event][$r->Rank1]) ? intval($Bonus[$event][$r->Rank1]) : 0),'mp'=>0,'wp'=>0,'lp'=>0);
								}
								if(empty($TeamDavis["$r->Team2"][$r->SubTeam2])) {
									$TeamDavis["$r->Team2"][$r->SubTeam2]=array('b'=>(isset($Bonus[$event][$r->Rank2]) ? intval($Bonus[$event][$r->Rank2]) : 0),'mp'=>0,'wp'=>0,'lp'=>0);
								}
								$TeamDavis["$r->Team1"][$r->SubTeam1]['mp']+=0;
								$TeamDavis["$r->Team1"][$r->SubTeam1]['wp']+=$r->WinPoints1;
								$TeamDavis["$r->Team1"][$r->SubTeam1]['lp']+=$r->WinPoints2;
								$TeamDavis["$r->Team2"][$r->SubTeam2]['mp']+=0;
								$TeamDavis["$r->Team2"][$r->SubTeam2]['wp']+=$r->WinPoints2;
								$TeamDavis["$r->Team2"][$r->SubTeam2]['lp']+=$r->WinPoints1;
								if($r->WinPoints1 + $r->WinPoints2 >= 5) {
									if($r->WinPoints1 > $r->WinPoints2) {
										$TeamDavis["$r->Team1"][$r->SubTeam1]['mp'] += $MatchWinner;
									} elseif($r->WinPoints1 < $r->WinPoints2) {
										$TeamDavis["$r->Team2"][$r->SubTeam2]['mp'] += $MatchWinner;
									} else {
										if($YEAR>=2020) {
											$TeamDavis["$r->Team1"][$r->SubTeam1]['mp']+=1;
											$TeamDavis["$r->Team2"][$r->SubTeam2]['mp']+=1;
										} else {
											if($r->Winner1) {
												$TeamDavis["$r->Team1"][$r->SubTeam1]['mp']+=2;
												$TeamDavis["$r->Team2"][$r->SubTeam2]['mp']+=0;
											} elseif($r->Winner2) {
												$TeamDavis["$r->Team1"][$r->SubTeam1]['mp']+=0;
												$TeamDavis["$r->Team2"][$r->SubTeam2]['mp']+=2;
											}
										}
									}
								}
							}
						}


						$DateTime=date('Y-m-d H:i:s');

						foreach($TeamDavis as $Team => $Subteams) {
							foreach($Subteams as $SubTeam => $Points) {
								safe_w_sql("insert into TeamDavis set TeDaDateTime='$DateTime', TeDaTournament={$this->tournament}, TeDaEvent='$event', TeDaTeam='$Team', TeDaSubTeam=$SubTeam, TeDaBonusPoints={$Points['b']}, TeDaMainPoints={$Points['mp']},TeDaWinPoints={$Points['wp']}, TeDaLoosePoints={$Points['lp']}
									on duplicate key update TeDaDateTime='$DateTime', TeDaBonusPoints={$Points['b']}, TeDaMainPoints={$Points['mp']},TeDaWinPoints={$Points['wp']}, TeDaLoosePoints={$Points['lp']}");
							}
						}

						break;
				}

				if($x===false) {
					return false;
				}
			}
		}
	}
}
