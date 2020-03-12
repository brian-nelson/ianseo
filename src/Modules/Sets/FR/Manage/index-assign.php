<?php
/**
 * Created by PhpStorm.
 * User: deligant
 * Date: 13/03/19
 * Time: 22.19
 */

require_once(dirname(__FILE__) . '/config.php');

$Event=$_REQUEST['event'];

$JSON=array(
	'msg'=>get_text('NotAllShootoffResolved', 'Tournament', $Event),
	);

// updates the team, so we need to update all the entries in the sub-events and the team itself
// ATTENTION: TfEvent=IndEvent IS ON PURPOSE!!!
// first get all the matchnos where teamevents are shot
$TeamMatchnos=array();
$SQL="select TfTeam,TfMatchNo 
	from TeamFinals
    where TfEvent=".StrSafe_DB($_REQUEST['event'])." and TfTournament={$_SESSION['TourId']}";
$q=safe_r_sql($SQL);
while($r=safe_fetch($q)) {
	$TeamMatchnos[$r->TfTeam][]=$r->TfMatchNo;
}

//debug_svela($TeamMatchnos);

safe_w_sql("update Finals set FinAthlete=0,FinTie=0,FinWinLose=0,FinWinnerSet=0 where FinEvent like ".StrSafe_DB($Event.'%')." and FinTournament={$_SESSION['TourId']}");
$SQL="select EnId, EnCountry, IndRank
	from Individuals
    inner join Entries on EnId=IndId and EnTournament=IndTournament and EnTeamFEvent=1
    where IndEvent=".StrSafe_DB($_REQUEST['event'])." and IndTournament={$_SESSION['TourId']}
    order by EnCountry, IndRank";
$OldCountry='';
$q=safe_r_sql($SQL);
while($r=safe_fetch($q)) {
	if($OldCountry!=$r->EnCountry) {
		$i=1;
		$OldCountry=$r->EnCountry;
		$OldRank=$r->IndRank;
	}
	foreach($TeamMatchnos[$r->EnCountry] as $MatchNo) {
		safe_w_sql("update Finals set FinAthlete=$r->EnId where FinEvent=".StrSafe_DB($Event.$i)." and FinMatchNo=$MatchNo and FinTournament={$_SESSION['TourId']}");
	}
	$i++;
}

// check the SO of all the involved events
$q=safe_r_sql("select IndEvent 
	from Individuals 
	where IndEvent like '{$Event}_' and IndTournament={$_SESSION['TourId']}
	group by IndEvent, IndRank
	having count(*)>1");
$SOEvent=array();
while($r=safe_fetch($q)) {
	$SOEvent[]=$r->IndEvent;
}

if($SOEvent) {
	$JSON['msg']=get_text('NotAllShootoffResolved', 'Tournament', implode(', ', $SOEvent));
} else {
	safe_w_sql("update Events set EvShootOff=1 where EvCode like '$Event%' and EvTournament={$_SESSION['TourId']}");
	$JSON['msg']='OK';
}

set_qual_session_flags();

JsonOut($JSON);
