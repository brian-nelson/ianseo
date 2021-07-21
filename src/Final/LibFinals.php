<?php

function SetTargetDb($Key, $Value, $Type='AB', $Letter='') {
	$ret=array();

	list($Event, $Team, $Matchno)=explode('_',$Key);

// cerco la fase del matchno
	$Select = "SELECT GrPhase, EvFinalFirstPhase, EvFinalAthTarget & greatest(1, GrPhase*2) > 0 as Ath4Tgt, EvMatchMultipleMatches & greatest(1, GrPhase*2) > 0 as Match4Tgt
		FROM Events
		inner join Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2, EvTeamEvent))>0
		inner join Grids on GrPhase<=greatest(PhId, PhLevel) 
		WHERE GrMatchNo=$Matchno and EvCode='$Event' and EvTeamEvent=$Team and EvTournament={$_SESSION['TourId']}";
	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)!=1) {
		return;
	}

	$MyRow=safe_fetch($Rs);
	$Phase=$MyRow->GrPhase;
	$realPhase=namePhase($MyRow->EvFinalFirstPhase, $MyRow->GrPhase);
	$ABCD='';

	switch(substr($Value,-1)) {
		case '*':
		case '+':
			// target is followed by a "+" sign fills up the phase from this point up to the last with increments of 1
			// target is followed by a "*" sign fills up the phase from this point up to the last with increments of 1, leaving a gap of 1 target after each match
			// target is followed by a "**" sign fills up the phase from this point up to the last with increments of 1, leaving a gap of 2 targets after each match
			$Gap=substr_count($Value,'*');
			$Value=intval($Value);
			$val=$Value;
			foreach(range($Matchno, max($Phase*4 - 1, 1)) as $k => $n) {
				switch($MyRow->Ath4Tgt.'-'.$MyRow->Match4Tgt) {
					case '0-0':
						// one archer per butt, single wave, no letter
						$ret[]=SetTargetDbAssign($Event, $Team, $n, $val++);
						if($k%2==1) {
							// every 2 matchnos
							$val+=$Gap;
						}
						break;
					case '1-0':
						// two archers per butt, single wave, always A+B
						$ABCD=($ABCD=='A' ? 'B' : 'A');
						$ret[]=SetTargetDbAssign($Event, $Team, $n, $val, $ABCD);
						if($ABCD=='B') {
							$val++;
							if($k%4==3) {
								// every 4 matchnos, so 2 buts
								$val+=$Gap;
							}
						}
						break;
					case '0-1':
						// one archer per butt, double wave, based on $Type it can be always A, always C or A+A followed by C+C
						switch($Type) {
							case 'AB':
								$ret[]=SetTargetDbAssign($Event, $Team, $n, $val++, 'A');
								if($k%2==1) {
									// every 2 matchnos
									$val+=$Gap;
								}
								break;
							case 'CD':
								$ret[]=SetTargetDbAssign($Event, $Team, $n, $val++, 'C');
								if($k%2==1) {
									// every 2 matchnos
									$val+=$Gap;
								}
								break;
							case 'ABCD':
								// means 1A vs 2A and 1C vs 2C
								$ABCD=(($ABCD=='A' and $n%2) ? 'C' : 'A');
								$ret[]=SetTargetDbAssign($Event, $Team, $n, $val, $ABCD);
								$val++;
								if($n%2 and $ABCD=='A') {
									// 2nd matchno of the couple, if 'AB' needs to go back 2 targets
									$val-=2;
								}
								if($k%4==3) {
									// every 4 matchnos
									$val+=$Gap;
								}
								break;
						}
					case '1-1':
						// two archers per butt, double wave, based on $Type it can be always A+B, always C+B or A+B followed by C+D
						switch($Type) {
							case 'AB': // always AB
								$ABCD=($ABCD=='A' ? 'B' : 'A');
								$ret[]=SetTargetDbAssign($Event, $Team, $n, $val, $ABCD);
								if($ABCD=='B') {
									$val++;
								}
								if($k%4==3) {
									// every 4 matchnos (2 butts) jumps
									$val+=$Gap;
								}
								break;
							case 'CD': // always CD
								$ABCD=($ABCD=='C' ? 'D' : 'C');
								$ret[]=SetTargetDbAssign($Event, $Team, $n, $val, $ABCD);
								if($ABCD=='D') {
									$val++;
								}
								if($k%4==3) {
									// every 4 matchnos (2 butts) jumps
									$val+=$Gap;
								}
								break;
							case 'ABCD':
								$ABCD=($ABCD=='A' ? 'B' : ($ABCD=='B' ? 'C' : ($ABCD=='C' ? 'D' : 'A')));
								$ret[]=SetTargetDbAssign($Event, $Team, $n, $val, $ABCD);
								if($ABCD=='D') {
									// after 4 matchnos, moves 1 target
									$val++;
								}
								if($k%8==7) {
									// every 4 matchnos (2 butts) jumps
									$val+=$Gap;
								}
								break;
						}
				}
			}
			break;
		case '-':
			// recupera gli scontri senza bye...
			// lo scontro parte da un numero pari
			$Matchno=2*floor($Matchno/2);
			$Value=intval($Value);
			$val=$Value;

			// cerca i byes, quindi va a prendere la differenza tra il numero di atleti della fase e gli atleti presenti in quell'evento
			if($Team) {
				$Sql = "SELECT DISTINCT EcCode, EcTeamEvent, EcNumber FROM EventClass WHERE EcCode='$Event' AND EcTeamEvent!=0 AND EcTournament={$_SESSION['TourId']}";
				$RsEc=safe_r_sql($Sql);
				$RuleCnt=0;
				$MyQuery = "Select COUNT(*) as Quanti, EvFinalFirstPhase as FirstPhase, EvNumQualified ";
				while($MyRowEc=safe_fetch($RsEc)) {
					$MyQuery .= (++$RuleCnt == 1 ? "FROM ": "INNER JOIN ");
					$MyQuery .= "(SELECT EnCountry as C" . $RuleCnt . "
                  FROM Entries
                  INNER JOIN EventClass ON EnClass=EcClass AND EnDivision=EcDivision AND EnTournament=EcTournament AND EcTeamEvent=" . $MyRowEc->EcTeamEvent . " AND EcCode='$Event'
                  WHERE EnTournament={$_SESSION['TourId']} AND EnTeamFEvent=1
                  group by EnCountry
                  HAVING COUNT(EnId)>=" . $MyRowEc->EcNumber . ") as sqy";
					$MyQuery .= ($RuleCnt == 1 ? " ": $RuleCnt . " ON C1=C". $RuleCnt . " ");
				}
				$MyQuery.=" inner join Events on EvCode='$Event' and EvTournament={$_SESSION['TourId']} and EvTeamEvent=1 ";

				//$Rs=safe_r_sql($Sql);
				//$tmpQuanti=safe_num_rows($Rs);
				//$tmpSaved=($Phase>=$FirstPhase ? SavedInPhase($FirstPhase) : SavedInPhase($realPhase));
				//$tmpQuantiIn = min($NumQualified, maxPhaseRank($realPhase));
				//$tmpQuantiOut = $tmpQuanti-$tmpQuantiIn;
				//$tmpBye = ($tmpQuantiOut<0 ? abs($tmpQuantiOut) : 0) + $tmpSaved;
			} else {
				$MyQuery = "SELECT COUNT(EnId) as Quanti, EvFinalFirstPhase as FirstPhase, EvNumQualified
					FROM Events
					INNER JOIN Individuals ON EvCode=IndEvent AND EvTournament=IndTournament
					INNER JOIN Entries ON EnId=IndId AND EnTournament=IndTournament
					WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode='$Event' AND EvTeamEvent=0 AND ((EnIndFEvent=1 AND EnStatus<=1) OR EnId IS NULL)";
			}
			$q=safe_r_sql($MyQuery);
			$r=safe_fetch($q);
			$tmpQuanti=$r->Quanti;
			$tmpSaved=($Phase>=$r->FirstPhase ? SavedInPhase($r->FirstPhase) : SavedInPhase($realPhase));
			$tmpQuantiIn = min($r->EvNumQualified, maxPhaseRank($realPhase));
			$tmpQuantiOut = $tmpQuanti-$tmpQuantiIn;
			$tmpBye = ($tmpQuantiOut<0 ? abs($tmpQuantiOut) : 0) + $tmpSaved;

			// ci sono byes, quindi va a riempire solo i matchno dei match pieni cioè con una rank superiore all'ultimo bye!
			// esempio: 1/8, 13 presenti, sono 3 byes, quindi si parte dal 4° in ranking...
			//
			$PosToTake=($realPhase==24 or $realPhase==48) ? 'GrPosition2' : 'GrPosition';
			$MyQuery = "SELECT distinct GrMatchNo, if($PosToTake > EvNumQualified, 0, $PosToTake) as Position
				FROM Events
				inner join Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2,EvTeamEvent))>0
				inner join Grids on GrPhase<=greatest(PhId, PhLevel)
				WHERE GrPhase = '$Phase' AND GrMatchNo>= $Matchno and EvCode='$Event' and EvTeamEvent=$Team and EvTournament={$_SESSION['TourId']} 
				ORDER BY Position < $tmpBye or Position > $tmpQuanti, GrMatchNo ASC";
			$q=safe_r_sql($MyQuery);

			while($r=safe_fetch($q)) {
				$butt= ($r->Position < $tmpBye or $r->Position > $tmpQuanti) ? '' : $val;

				if($butt and $r->Position > $tmpBye) {
					switch($MyRow->Ath4Tgt.'-'.$MyRow->Match4Tgt) {
						case '0-0':
							// one archer per butt, single wave, no letter
							$ret[]=SetTargetDbAssign($Event, $Team, $r->GrMatchNo, $val++);
							break;
						case '1-0':
							// two archers per butt, single wave, always A+B
							$ABCD=($ABCD=='A' ? 'B' : 'A');
							$ret[]=SetTargetDbAssign($Event, $Team, $r->GrMatchNo, $val, $ABCD);
							if($ABCD=='B') {
								$val++;
							}
							break;
						case '0-1':
							// one archer per butt, double wave, based on $Type it can be always A, always C or A+A followed by C+C
							switch($Type) {
								case 'AB':
									$ret[]=SetTargetDbAssign($Event, $Team, $r->GrMatchNo, $val++, 'A');
									break;
								case 'CD':
									$ret[]=SetTargetDbAssign($Event, $Team, $r->GrMatchNo, $val++, 'C');
									break;
								case 'ABCD':
									// means 1A vs 2A and 1C vs 2C
									$ABCD=(($ABCD=='A' and $r->GrMatchNo%2) ? 'C' : 'A');
									$ret[]=SetTargetDbAssign($Event, $Team, $r->GrMatchNo, $val, $ABCD);
									$val++;
									if($r->GrMatchNo%2 and $ABCD=='A') {
										// 2nd matchno of the couple, if 'AB' needs to go back 2 targets
										$val-=2;
									}
									break;
							}
						case '1-1':
							// two archers per butt, double wave, based on $Type it can be always A+B, always C+B or A+B followed by C+D
							switch($Type) {
								case 'AB': // always AB
									$ABCD=($ABCD=='A' ? 'B' : 'A');
									$ret[]=SetTargetDbAssign($Event, $Team, $r->GrMatchNo, $val, $ABCD);
									if($ABCD=='B') {
										$val++;
									}
									break;
								case 'CD': // always CD
									$ABCD=($ABCD=='C' ? 'D' : 'C');
									$ret[]=SetTargetDbAssign($Event, $Team, $r->GrMatchNo, $val, $ABCD);
									if($ABCD=='D') {
										$val++;
									}
									break;
								case 'ABCD':
									$ABCD=($ABCD=='A' ? 'B' : ($ABCD=='B' ? 'C' : ($ABCD=='C' ? 'D' : 'A')));
									$ret[]=SetTargetDbAssign($Event, $Team, $r->GrMatchNo, $val, $ABCD);
									if($ABCD=='D') {
										// after 4 matchnos, moves 1 target
										$val++;
									}
									break;
							}
					}
				} else {
					$ret[]=SetTargetDbAssign($Event, $Team, $r->GrMatchNo, '');
				}
			}
			break;
		default:
			// single value... need to check if single wave or not, 1 or 2 archers per butt
			$Value=intval($Value);
			switch($MyRow->Ath4Tgt.'-'.$MyRow->Match4Tgt) {
				case '0-0':
					// straight!
					$ret[] = SetTargetDbAssign($Event, $Team, $Matchno, $Value);
					break;
				case '1-0':
					// A/B always
					$ret[] = SetTargetDbAssign($Event, $Team, $Matchno, $Value, 'A');
					$ret[] = SetTargetDbAssign($Event, $Team, $Matchno + 1, $Value, 'B');
					break;
				case '0-1':
					// 1 per butt, 2 waves, depends on type
					switch ($Type) {
						case 'AB':
							$ret[] = SetTargetDbAssign($Event, $Team, $Matchno, $Value, 'A');
							$ret[] = SetTargetDbAssign($Event, $Team, $Matchno + 1, $Value + 1, 'A');
							break;
						case 'CD':
							$ret[] = SetTargetDbAssign($Event, $Team, $Matchno, $Value, 'C');
							$ret[] = SetTargetDbAssign($Event, $Team, $Matchno + 1, $Value + 1, 'C');
							break;
						case 'ABCD':
							// first 2 are AB
							$ret[] = SetTargetDbAssign($Event, $Team, $Matchno, $Value, 'A');
							$ret[] = SetTargetDbAssign($Event, $Team, $Matchno + 1, $Value + 1, 'A');
							$ret[] = SetTargetDbAssign($Event, $Team, $Matchno + 2, $Value, 'C');
							$ret[] = SetTargetDbAssign($Event, $Team, $Matchno + 3, $Value + 1, 'C');
							break;
					}
					break;
				case '1-1':
					// 2 per butt, 2 waves, depends on type
					switch ($Type) {
						case 'AB':
							$ret[] = SetTargetDbAssign($Event, $Team, $Matchno, $Value, 'A');
							$ret[] = SetTargetDbAssign($Event, $Team, $Matchno + 1, $Value, 'B');
							break;
						case 'CD':
							$ret[] = SetTargetDbAssign($Event, $Team, $Matchno, $Value, 'C');
							$ret[] = SetTargetDbAssign($Event, $Team, $Matchno + 1, $Value, 'D');
							break;
						case 'ABCD':
							// first 2 are AB
							$ret[] = SetTargetDbAssign($Event, $Team, $Matchno, $Value, 'A');
							$ret[] = SetTargetDbAssign($Event, $Team, $Matchno + 1, $Value, 'B');
							$ret[] = SetTargetDbAssign($Event, $Team, $Matchno + 2, $Value, 'C');
							$ret[] = SetTargetDbAssign($Event, $Team, $Matchno + 3, $Value, 'D');
							break;
					}
					break;
			}
			break;
	}
	return $ret;
}

function SetTargetDbAssign($Event, $Team, $Matchno, $Value, $Letter='') {
	$Insert = "INSERT INTO FinSchedule (FSEvent,FSTeamEvent,FSMatchNo,FSTournament,FSTarget, FSLetter)
					VALUES(" . StrSafe_DB($Event) . ", $Team, %3\$s, {$_SESSION['TourId']}, '%1\$s', '%2\$s')
					ON DUPLICATE KEY UPDATE FSTarget='%1\$s', FSLetter='%2\$s'";
	if ($Value) {
		$Target = str_pad($Value,TargetNoPadding,'0',STR_PAD_LEFT);
	} else {
		$Target='';
	}

	$let=$Target ? $Target.$Letter : '';
	safe_w_sql(sprintf($Insert, $Target, $let, $Matchno));

	return array('id'=>$Event.'_'.$Team.'_'.$Matchno, 'tgt' => $Target, 'let' => $Letter, 'matchno'=>$Matchno);
}

function getRedTargets($Event, $Team=0) {
	$ret = array();

	// we need to check if there are more opponents and matches on the same targets at a certain date/time
	$Select = "select  group_concat(FSMatchNo separator '|') as MatchNos, EvFinalAthTarget & greatest(1, GrPhase*2) > 0 as Ath4Tgt, EvMatchMultipleMatches & greatest(1, GrPhase*2) > 0 as Match4Tgt, FsEvent, GrPhase, GrMatchNo, FSTarget+0 as FsTarget, count(*) as Quanti, SecQuanti, SecAth4Tgt, SecMatch4Tgt
		from FinSchedule
		inner join Events on EvCode=FSEvent and EvTeamEvent=FSTeamEvent and EvTournament=FSTournament
		inner join Grids on GrMatchNo=FSMatchNo
		left join (
		    select count(*) as SecQuanti, FSScheduledDate as SecDate, FSScheduledTime as SecTime, FSTarget+0 as SecTarget, min(EvFinalAthTarget & greatest(1, GrPhase*2) > 0) as SecAth4Tgt, min(EvMatchMultipleMatches & greatest(1, GrPhase*2) > 0) as SecMatch4Tgt
		    from FinSchedule
			inner join Events on EvCode=FSEvent and EvTeamEvent=FSTeamEvent and EvTournament=FSTournament
			inner join Grids on GrMatchNo=FSMatchNo
			where FSEvent!='$Event' and FSTeamEvent=$Team and FSTournament={$_SESSION['TourId']}
			group by FSScheduledDate, FSScheduledTime, FSTarget+0
		) Secondary on SecDate=FSScheduledDate and SecTime=FSScheduledTime and SecTarget=FSTarget+0
		where FSEvent='$Event' and FSTeamEvent=$Team and FSTournament={$_SESSION['TourId']} and FsTarget>0
		group by FSScheduledDate, FSScheduledTime, FSTarget+0
	";

	$Rs=safe_r_sql($Select);
	while ($MyRow=safe_fetch($Rs)) {
		$Error=false;
		if($MyRow->SecQuanti) {
			// we have a second event at same date, time and target
			$Error=true;
			if($MyRow->Match4Tgt>0 and $MyRow->Match4Tgt == $MyRow->SecMatch4Tgt) {
				// we have allowed the multiple match system on both ends
				$Max= $MyRow->Ath4Tgt ? 2 : 1;
				$SecMax= $MyRow->SecAth4Tgt ? 2 : 1;
				$Error=($MyRow->Quanti>$Max or $MyRow->SecQuanti>$SecMax);
			}
		} else {
			$Error= ($MyRow->Quanti> (($MyRow->Ath4Tgt+1)*($MyRow->Match4Tgt+1)));
		}
		foreach(explode('|', $MyRow->MatchNos) as $MatchNo) {
			$ret[]=array(
				'id' => $MyRow->FsEvent . '_' . $Team . '_' . $MatchNo,
				'error' => $Error,
				);
		}
	}

	return $ret;
}
