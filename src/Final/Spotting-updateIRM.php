<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Fun_ChangePhase.inc.php');
require_once('Common/Lib/CommonLib.php');
//require_once('Common/Fun_FormatText.inc.php');
//require_once('Fun_Final.local.inc.php');

$JSON=array('error'=>1, 'msg'=>get_text('Error'));

if(empty($_REQUEST['event']) or !isset($_REQUEST['team']) or !isset($_REQUEST['matchno']) or !isset($_REQUEST['value']) or !CheckTourSession()) {
	JsonOut($JSON);
}

$event=$_REQUEST['event'];
$team=intval($_REQUEST['team']);
$match=intval($_REQUEST['matchno']);
$irm=intval($_REQUEST['value']);

if(($team==0 ? IsBlocked(BIT_BLOCK_IND) : IsBlocked(BIT_BLOCK_TEAM)) or checkACL(($team ? AclTeams : AclIndividuals), AclReadWrite, false)!=AclReadWrite) {
	JsonOut($JSON);
}

$ok=true;
$JSON['msg']=get_text('CmdOk');
if ($team) {
	safe_w_sql("update TeamFinals set TfIrmType=$irm where TfMatchNo=$match and TfEvent='$event' and TfTournament={$_SESSION['TourId']}");
	switch($irm) {
		case 5: // DNF: rank is OK, lost the match, move opponent to next phase
		case 10: // DNS: rank is OK, lost the match, move opponent to next phase
			break;
		case 15: // DSQ: rank is OK but not shown, lost the match, goes last of his phase
			$q=safe_r_sql("select TfTeam, TfSubTeam
				from TeamFinals
				where TfEvent='$event' and TfMatchNo=$match and TfTournament={$_SESSION['TourId']}");
			if($r=safe_fetch($q)) {
				// updates all the team result with DSQ!
				safe_w_sql("update Teams set TeIrmTypeFinal=15 where (TeCoId, TeSubTeam, TeEvent, TeTournament) = ($r->TfTeam, $r->TfSubTeam, '$event', {$_SESSION['TourId']})");
			}
			break;
		case 20: // DQB: Disqualified by behaviour, Virtually removed from any rank, TODO: other stay where they are? If Silver gets DQB, what happens to Bronze and 4th?
			// gets the Team ID
			$q=safe_r_sql("select TfTeam, TfSubTeam
				from TeamFinals
				where TfEvent='$event' and TfMatchNo=$match and TfTournament={$_SESSION['TourId']}");
			if($r=safe_fetch($q)) {
				// updates all the team result with DSQ!
				safe_w_sql("update TeamFinals set TfIrmType=20 where (TfTeam, TfSubTeam, TfEvent, TfTournament) = ($r->TfTeam, $r->TfSubTeam, '$event', {$_SESSION['TourId']})");
				safe_w_sql("update Teams set TeRank=$CFG->DERANKING, TeRankFinal=$CFG->DERANKING, TeIrmType=20, TeIrmTypeFinal=20 where (TeCoId, TeSubTeam, TeEvent, TeTournament) = ($r->TfTeam, $r->TfSubTeam, '$event', {$_SESSION['TourId']})");
				$JSON['msg']='Please Check teams and individual rankings';
			}
			break;
		default:
			$JSON['msg']=get_text('Error');
			$ok=false;
	}
} else {
	safe_w_sql("update Finals set FinIrmType=$irm where FinMatchNo=$match and FinEvent='$event' and FinTournament={$_SESSION['TourId']}");
	switch($irm) {
		case 5: // DNF: rank is OK, lost the match, move opponent to next phase
		case 10: // DNS: rank is OK, lost the match, move opponent to next phase
			break;
		case 15: // DSQ: rank is OK but not shown, lost the match, goes last of his phase
			$q=safe_r_sql("select FinAthlete
				from Finals
				where FinEvent='$event' and FinMatchNo=$match and FinTournament={$_SESSION['TourId']}");
			if($r=safe_fetch($q)) {
				// updates the Individual Final IRM with DSQ!
				safe_w_sql("update Individuals set IndIrmTypeFinal=15 where (IndId, IndEvent, IndTournament) = ($r->FinAthlete, '$event', {$_SESSION['TourId']})");
			}
			break;
		case 20: // DQB: Disqualified by behaviour, Virtually removed from any rank, TODO: other stay where they are? If Silver gets DQB, what happens to Bronze and 4th?
			// gets the Team ID
			$q=safe_r_sql("select FinAthlete
				from Finals
				where FinEvent='$event' and FinMatchNo=$match and FinTournament={$_SESSION['TourId']}");
			if($r=safe_fetch($q)) {
				// updates all the Individual result with DQB!
				safe_w_sql("update Finals set FinIrmType=20 where (FinAthlete, FinEvent, FinTournament) = ($r->FinAthlete, '$event', {$_SESSION['TourId']})");
				safe_w_sql("update Individuals set IndRank=$CFG->DERANKING, IndRankFinal=$CFG->DERANKING, IndIrmType=20, IndIrmTypeFinal=20 where (IndId, IndEvent, IndTournament) = ($r->FinAthlete, '$event', {$_SESSION['TourId']})");
				safe_w_sql("update Qualifications set QuIrmType=20 where QuId=$r->FinAthlete");

				// check if he is part of a team in qualifications
				$t=safe_r_sql("select * from TeamComponent where TcId=$r->FinAthlete");
				while($u=safe_fetch($t)) {
					// The team gets a DQB too!!!
					safe_w_sql("update Teams set TeRank=$CFG->DERANKING, TeIrmType=20 where (TeCoId, TeSubTeam, TeEvent, TeTournament) = ($u->TcCoId, $u->TcSubTeam, '$u->TcEvent', {$_SESSION['TourId']})");
				}

				// check if he is part of a team in Matches
				$t=safe_r_sql("select * from TeamFinComponent where TfcId=$r->FinAthlete");
				while($u=safe_fetch($t)) {
					// The team gets a DQB too!!!
					safe_w_sql("update TeamFinals set TfIrmType=20 where (TfTeam, TfSubTeam, TfEvent, TfTournament) = ($u->TfcCoId, $u->TfcSubTeam, '$u->TfcEvent', {$_SESSION['TourId']})");
					safe_w_sql("update Teams set TeRankFinal=$CFG->DERANKING, TeIrmTypeFinal=20 where (TeCoId, TeSubTeam, TeEvent, TeTournament) = ($u->TfcCoId, $u->TfcSubTeam, '$u->TfcEvent', {$_SESSION['TourId']})");
				}
				$JSON['msg']='Please Check teams and individual rankings';
			}
			break;
		default:
			$JSON['msg']=get_text('Error');
			$ok=false;
	}
}

if ($ok) {
	$JSON['error']=0;
}

JsonOut($JSON);

