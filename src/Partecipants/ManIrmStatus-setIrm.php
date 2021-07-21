<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);
checkACL(AclParticipants, AclReadWrite);

$JSON=array('error' => 1, 'msg' => '');

if(empty($_REQUEST['item']) or (!isset($_REQUEST['bye']) and !isset($_REQUEST['note']) and (!isset($_REQUEST['irm']) or !isset($_REQUEST['qual']) or !isset($_REQUEST['fin'])))) {
	JsonOut($JSON);
}

// FOR THE MOMENT WE KEEP LOW PROFILE!!!!!
// NO AUTOMATIC RECALCULATION OF RANKS AND TEAMS

$Items=explode('-', $_REQUEST['item']);

if(isset($_REQUEST['note'])) {
	// records can be set:
	// - Individuals for the ranking round of that event
	// - Qualification should be set if there are no events
	// - Matches if the evnt is not in sets
	// - Teams: suspended at the moment, in case only AFTER the qualification is done
	switch($Items[0].$Items[1]) {
		case '0Q':
			// qualification (div-class)
			$EnId=intval($Items[2]);
			safe_w_sql("update Qualifications inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']} set QuNotes=".StrSafe_DB($_REQUEST['note'])."  where QuId=$EnId");
			$JSON['error']=0;
			break;
		case '0M':
			// Individual Events
			$Phase=$Items[2];
			$Event=$Items[3];
			$EnId=intval($Items[4]);

			if($Phase=='Q') {
				safe_w_sql("update Individuals set IndNotes=".StrSafe_DB($_REQUEST['note'])."  where IndTournament={$_SESSION['TourId']} and IndId=$EnId");
				$JSON['error']=0;
			} elseif(is_numeric($Phase)) {
                $JSON['error']=0;
                safe_w_sql("update Finals 
                    inner join Grids on GrMatchNo=FinMatchNo and GrPhase=$Phase
                    set FinNotes=".StrSafe_DB($_REQUEST['note'])." 
                    where FinTournament={$_SESSION['TourId']} and FinEvent=".StrSafe_DB($Event)." and FinAthlete=$EnId");
			}
			break;
	}
	JsonOut($JSON);
}

if(isset($_REQUEST['bye'])) {
	if($Items[1]=='M' and is_numeric($Items[2])) {
		require_once('Final/Fun_ChangePhase.inc.php');
		$Bye=intval($_REQUEST['bye']);
		$Phase=$Items[2];
		$Event=$Items[3];
		if($Items[0]==0) {
			$EnId=intval($Items[4]);

			safe_w_sql("update Finals 
				inner join Grids on GrMatchNo=FinMatchNo and GrPhase=$Phase
				set FinTie=$Bye 
				where FinTournament={$_SESSION['TourId']} and FinEvent=".StrSafe_DB($Event)." and FinAthlete=$EnId");

			// recalculate winners
			move2NextPhase($Phase, $Event);
		} else {
			$CoId=intval($Items[4]);
			$SubTeam=intval($Items[5]);

			safe_w_sql("update TeamFinals 
				inner join Grids on GrMatchNo=TfMatchNo and GrPhase=$Phase
				set TfTie=$Bye 
				where TfTeam=$CoId and TfSubTeam=$SubTeam and TfTournament={$_SESSION['TourId']} and TfEvent=".StrSafe_DB($Event));

			// recalculate winners
			move2NextPhaseTeam($Phase, $Event);
		}
		$JSON['error']=0;
		$JSON['msg']=get_text('ByeMovedToPhase', 'Tournament');
	}
	JsonOut($JSON);
}

$IRM=intval($_REQUEST['irm']);
$QUAL=intval($_REQUEST['qual']);
$FIN=intval($_REQUEST['fin']);
$SUB=intval($_REQUEST['sub']);

$q=safe_r_sql("select * from IrmTypes where IrmId=$IRM");
if(!($IrmRecord=safe_fetch($q))) {
	JsonOut($JSON);
}

$Propagate=!$IrmRecord->IrmShowRank; // affects the ranking so propagates the status to teams...

$JSON['error']=0;
$JSON['msg']=get_text('CmdOk');
$JSON['qual']=$QUAL;
$JSON['fin']=$FIN;
$JSON['sub']=$SUB;

switch($Items[0].$Items[1]) {
	case '0Q':
		// Individual Qualification
		$EnId=intval($Items[2]);
		safe_w_sql("update Qualifications inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']} set QuIrmType=$IRM, QuClRank=$QUAL, QuSubClassRank=$SUB where QuId=$EnId");
		$JSON['msg']=get_text('IrmUpdateIndividual', 'Tournament');

		//  if IRM is DSQ or DQB the entry is part of a team needs to recalculate the teams
		if($Propagate) {
			switch($IRM) {
				case 10: $NewRank=$CFG->DIDNOTSTART; break;
				case 15: $NewRank=$CFG->DISQUALIFIED; break;
				case 20: $NewRank=$CFG->DERANKING; break;
			}
			safe_w_sql("update TeamComponent set TcIrmType=$IRM where TcTournament={$_SESSION['TourId']} and TcId=$EnId");
			if(safe_w_affected_rows()) {
				safe_w_sql("update Teams inner join TeamComponent on TcTournament=TeTournament and TcEvent=TeEvent and TcCoId=TeCoId and TcSubTeam=TeSubTeam and TcFinEvent=TeFinEvent and TcId=$EnId set TeIrmType=$IRM, TeRank=$NewRank where TeTournament={$_SESSION['TourId']}");
				$JSON['msg']=get_text('IrmUpdateIndAndTeams', 'Tournament');
			}

			safe_w_sql("update TeamFinComponent set TfcIrmType=$IRM where TfcTournament={$_SESSION['TourId']} and TfcId=$EnId");
			if(safe_w_affected_rows()) {
				safe_w_sql("update Teams inner join TeamFinComponent on TfcTournament=TeTournament and TfcEvent=TeEvent and TfcCoId=TeCoId and TfcSubTeam=TeSubTeam and TeFinEvent=1 and TfcId=$EnId set TeIrmTypeFinal=$IRM, TeRankFinal=$NewRank where TeTournament={$_SESSION['TourId']}");
				$JSON['msg']=get_text('IrmUpdateIndAndTeams', 'Tournament');
			}
		}
		break;
	case '1Q':
		// this should never been triggered
		$JSON['error']=1;
		$JSON['msg']='Generic Error';
		break;
	case '0M':
		// Individual Events
		$Phase=$Items[2];
		$Event=$Items[3];
		$EnId=intval($Items[4]);

		if($Phase=='Q' or $Phase=='E0' or $Phase=='E1') {
			if($Phase=='Q') {
				// qualifications
				safe_w_sql("update Individuals set IndIrmType=$IRM, IndRank=$QUAL, IndRankFinal=$FIN where IndTournament={$_SESSION['TourId']} and IndEvent=".StrSafe_DB($Event)." and IndId=$EnId");

				// check if the athlete has only 1 event, so also Qualification is affected
				$q=safe_r_sql("select IndId from Individuals where IndTournament={$_SESSION['TourId']} and IndId=$EnId");
				if($QualAffected = (safe_num_rows($q)==1)) {
					safe_w_sql("update Qualifications inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']} set QuIrmType=$IRM, QuClRank=$QUAL, QuSubClassRank=$SUB where QuId=$EnId");
				}
			} else {
				$ElimPhase=substr($Phase,1);
				safe_w_sql("update Eliminations set ElIrmType=$IRM, ElRank=$SUB where ElTournament={$_SESSION['TourId']} and ElEventCode=".StrSafe_DB($Event)." and ElId=$EnId and ElElimPhase=$ElimPhase");
				safe_w_sql("update Individuals set IndRankFinal=$FIN".($IRM>=15 ? ", IndIrmTypeFinal=$IRM" : "")." where IndTournament={$_SESSION['TourId']} and IndEvent=".StrSafe_DB($Event)." and IndId=$EnId");
			}

			$JSON['msg']=get_text('IrmUpdateIndividual', 'Tournament');

			//  if IRM is DSQ or DQB the entry is part of a team needs to recalculate the teams
			if($Propagate) {
				switch($IRM) {
					case 7: $NewRank=$CFG->DIDNOTFINISH; break;
					case 10: $NewRank=$CFG->DIDNOTSTART; break;
					case 15: $NewRank=$CFG->DISQUALIFIED; break;
					case 20: $NewRank=$CFG->DERANKING; break;
				}
				safe_w_sql("update Teams 
    				inner join TeamComponent on TcTournament=TeTournament and TcEvent=TeEvent and TcCoId=TeCoId and TcSubTeam=TeSubTeam and TcFinEvent=TeFinEvent and TcId=$EnId 
					set TeIrmType=$IRM, TeRank=$NewRank, TcIrmType=$IRM
					where TeTournament={$_SESSION['TourId']}");
				if(safe_w_affected_rows()) {
					$JSON['msg']=get_text('IrmUpdateIndAndTeams', 'Tournament');
				}

				safe_w_sql("update Teams 
                    inner join TeamFinComponent on TfcTournament=TeTournament and TfcEvent=TeEvent and TfcCoId=TeCoId and TfcSubTeam=TeSubTeam and TeFinEvent=1 and TfcId=$EnId 
					set TeIrmTypeFinal=$IRM, TeRankFinal=$NewRank, TfcIrmType=$IRM 
					where TeTournament={$_SESSION['TourId']}");
				if(safe_w_affected_rows()) {
					$JSON['msg']=get_text('IrmUpdateIndAndTeams', 'Tournament');
				}
			}
		} elseif(is_numeric($Phase)) {
			// Matches
			safe_w_sql("update Finals 
				inner join Grids on GrMatchNo=FinMatchNo and GrPhase=$Phase
				set FinIrmType=$IRM 
				where FinTournament={$_SESSION['TourId']} and FinEvent=".StrSafe_DB($Event)." and FinAthlete=$EnId");

			// update qualification and final rank
			$SQL="update Individuals set IndRank=$QUAL, IndRankFinal=$FIN";
			if($Propagate) {
				$SQL.=", IndIrmTypeFinal=$IRM";
			}
			$SQL.=" where IndTournament={$_SESSION['TourId']} and IndId=$EnId and IndEvent=".StrSafe_DB($Event);
			safe_w_sql($SQL);

			// check if there is still a locking IRM in the matches
			$q=safe_r_sql("select FinIrmType from Finals inner join IrmTypes on IrmId=FinIrmType where IrmShowRank=0 and FinIrmType>0 and FinTournament={$_SESSION['TourId']} and FinEvent=".StrSafe_DB($Event)." and FinAthlete=$EnId");
			if(!safe_num_rows($q)) {
				// no more IRM so resets the final IRM status
				safe_w_sql("update Individuals set IndIrmTypeFinal=0 where IndTournament={$_SESSION['TourId']} and IndEvent=".StrSafe_DB($Event)." and IndId=$EnId");
			}

			// If DNF/DNS, the opponent if any wins the match and gets a bye so get the matchno
			if($IRM==5 or $IRM==10) {
				$q=safe_r_sql("select FinMatchNo, FinWinLose 
					from Finals 
				    inner join Grids on GrMatchNo=FinMatchNo and GrPhase=$Phase 
					where FinTournament={$_SESSION['TourId']} and FinEvent=".StrSafe_DB($Event)." and FinAthlete=$EnId");
				if($r=safe_fetch($q)) {
					// assigns the winlose status to the opponent only if there are no winners already
					$OppMatchno=$r->FinMatchNo%2 ? $r->FinMatchNo-1 : $r->FinMatchNo+1;
					$Match=$r->FinMatchNo.','.$OppMatchno;
					$t=safe_r_sql("select max(FinWinLose) as HasWinner, group_concat(FinAthlete separator '|') as Opponents from Finals where FinTournament={$_SESSION['TourId']} and FinEvent=".StrSafe_DB($Event)." and FinMatchNo in ($Match)");
					if($u=safe_fetch($t) and !$u->HasWinner) {
						safe_w_sql("update Finals set FinWinLose=1, FinTie=2 where FinAthlete>0 and FinTournament={$_SESSION['TourId']} and FinEvent=".StrSafe_DB($Event)." and FinMatchNo=$OppMatchno");

						// recalculates the rank
						require_once('Final/Fun_ChangePhase.inc.php');
						move2NextPhase($Phase, $Event);

						$Opponents=explode('|', $u->Opponents);
						$Items[4] = $EnId==$Opponents[0] ? $Opponents[1] : $Opponents[0];
						$JSON['OpStatusChange']=implode('-',$Items);

						// gets the new rank of the IRM
						$t=safe_r_sql("select IndRankFinal from Individuals where IndId=$EnId and IndTournament={$_SESSION['TourId']}");
						$u=safe_fetch($t);
						$JSON['FinRank']=$u->IndRankFinal;
					}
				}
			}

			$JSON['msg']=get_text('IrmUpdateIndividual', 'Tournament');
		} else {
			/// somebody faking the system?
			$JSON['error']=1;
			$JSON['msg']='Generic Error';
		}
		break;
	case '1M':
		$Phase=$Items[2];
		$Event=$Items[3];
		$CoId=intval($Items[4]);
		$SubTeam=intval($Items[5]);

		// Team
		if($Phase=='Q') {
			// qualifications
			safe_w_sql("update Teams set TeIrmType=$IRM, TeRank=$QUAL, TeRankFinal=$FIN where TeTournament={$_SESSION['TourId']} and TeEvent=".StrSafe_DB($Event)." and TeCoId=$CoId and TeSubTeam=$SubTeam");
			$JSON['msg']=get_text('IrmUpdateTeams', 'Tournament');
		} elseif(is_numeric($Phase)) {
			// Matches
			safe_w_sql("update TeamFinals 
				inner join Grids on GrMatchNo=TfMatchNo and GrPhase=$Phase
				set TfIrmType=$IRM 
				where TfTeam=$CoId and TfSubTeam=$SubTeam and TfTournament={$_SESSION['TourId']} and TfEvent=".StrSafe_DB($Event));

			$SQL="update Teams set TeRank=$QUAL, TeRankFinal=$FIN";
			if($Propagate) {
				$SQL.=", TeIrmTypeFinal=$IRM";
			}
			$SQL.=" where TeTournament={$_SESSION['TourId']} and TeEvent=".StrSafe_DB($Event)." and TeCoId=$CoId and TeSubTeam=$SubTeam";
			safe_w_sql($SQL);

			// check if there is still a locking IRM in the matches
			$q=safe_r_sql("select TfIrmType as TfIrmType from TeamFinals inner join IrmTypes on IrmId=TfIrmType where IrmShowRank=0 and TfIrmType>0 and TfTournament={$_SESSION['TourId']} and TfEvent=".StrSafe_DB($Event)." and TfTeam=$CoId and TfSubTeam=$SubTeam");
			if(!safe_num_rows($q)) {
				// no more IRM so resets the final IRM status
				safe_w_sql("update Teams set TeIrmTypeFinal=0 where TeTournament={$_SESSION['TourId']} and TeEvent=".StrSafe_DB($Event)." and TeCoId=$CoId and TeSubTeam=$SubTeam");
			}

			// If DNF/DNS, the opponent if any wins the match and gets a bye so get the matchno
			if($IRM==5 or $IRM==10) {
				$q=safe_r_sql("select TfMatchNo, TfWinLose 
					from TeamFinals 
				    inner join Grids on GrMatchNo=TfMatchNo and GrPhase=$Phase 
					where TfTournament={$_SESSION['TourId']} and TfEvent=".StrSafe_DB($Event)." and TfTeam=$CoId and TfSubTeam=$SubTeam");
				if($r=safe_fetch($q)) {
					// assigns the winlose status to the opponent only if there are no winners already
					$OppMatchno=$r->TfMatchNo%2 ? $r->TfMatchNo-1 : $r->TfMatchNo+1;
					$Match=$r->TfMatchNo.','.$OppMatchno;
					$t=safe_r_sql("select max(TfWinLose) as HasWinner, group_concat(concat(TfTeam, '-', TfSubTeam) separator '|') as Opponents from TeamFinals where TfTournament={$_SESSION['TourId']} and TfEvent=".StrSafe_DB($Event)." and TfMatchNo in ($Match)");
					if($u=safe_fetch($t) and !$u->HasWinner) {
						safe_w_sql("update TeamFinals set TfWinLose=1, TfTie=2 where TfTeam>0 and TfTournament={$_SESSION['TourId']} and TfEvent=".StrSafe_DB($Event)." and TfMatchNo=$OppMatchno");

						// recalculates the rank
						require_once('Final/Fun_ChangePhase.inc.php');
						move2NextPhaseTeam($Phase, $Event);

						$Opponents=explode('|', $u->Opponents);
						$Items[4] = $CoId.'-'.$SubTeam==$Opponents[0] ? $Opponents[1] : $Opponents[0];
						unset($Items[5]);
						$JSON['OpStatusChange']=implode('-',$Items);

						// gets the new rank of the IRM
						$t=safe_r_sql("select TeRankFinal from Teams where TeCoId=$CoId and TeSubTeam=$SubTeam and TeTournament={$_SESSION['TourId']}");
						$u=safe_fetch($t);
						$JSON['FinRank']=$u->TeRankFinal;
					}
				}
			}

		} else {
			/// somebody faking the system?
			$JSON['error']=1;
			$JSON['msg']='Generic Error';
		}
		break;
	default:
		$JSON['error']=1;
		$JSON['msg']='Generic Error';
		JsonOut($JSON);
}

// if a DQB then affects EVERYWHERE the archer is, including teams
if($IRM==20 and $Items[0]==0) {
	safe_w_sql("update Qualifications set QuIrmType=$IRM, QuClRank=$QUAL where QuId=$EnId");
	safe_w_sql("update Individuals set IndIrmType=$IRM, IndIrmTypeFinal=$IRM, IndRank=$QUAL, IndRankFinal=$FIN where IndTournament={$_SESSION['TourId']} and IndId=$EnId");
	safe_w_sql("update Finals set FinIrmType=$IRM where FinTournament={$_SESSION['TourId']} and FinAthlete=$EnId");
	// check the team components as if part of the qualification team or the finals team the DQB affects both!!!
	$q=safe_r_sql("Select TcCoId Team, TcSubTeam SubTeam, TcEvent Event from TeamComponent where TcId=$EnId and TcTournament={$_SESSION['TourId']}
		union
		Select TfcCoId Team, TfcSubTeam SubTeam, TfcEvent Event from TeamFinComponent where TfcId=$EnId and TfcTournament={$_SESSION['TourId']}");
	while($r=safe_fetch($q)) {
		// updates all teams for each event involved by the DQB
		safe_w_sql("update Teams 
			set TeIrmTypeFinal=$IRM, TeRankFinal=$CFG->DERANKING, TeIrmType=$IRM, TeRank=$CFG->DERANKING 
			where TeCoId=$r->Team and TeSubTeam=$r->SubTeam and TeEvent='$r->Event' and TeTournament={$_SESSION['TourId']}");

		// updates all matches
		safe_w_sql("update TeamFinals
			set TfIrmType=$IRM 
			where TfTeam=$r->Team and TfSubTeam=$r->SubTeam and TfEvent='$r->Event' and TfTournament={$_SESSION['TourId']}");

		// Sets the DQB to the TeamComponent and TeamFinComponents
		safe_w_sql("update TeamComponent
			set TcIrmType=$IRM 
			where TcCoId=$r->Team and TcSubTeam=$r->SubTeam and TcEvent='$r->Event' and TcTournament={$_SESSION['TourId']}");
		safe_w_sql("update TeamFinComponent
			set TfcIrmType=$IRM 
			where TfcCoId=$r->Team and TfcSubTeam=$r->SubTeam and TfcEvent='$r->Event' and TfcTournament={$_SESSION['TourId']}");
	}
	$JSON['qual']=$CFG->DERANKING;
	$JSON['fin']=$CFG->DERANKING;
	$JSON['msg']=get_text('IrmUpdateIndAndTeams', 'Tournament');
}

JsonOut($JSON);
