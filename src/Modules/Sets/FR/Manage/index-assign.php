<?php

require_once(dirname(__FILE__) . '/config.php');

$AllInOne=getModuleParameter('FFTA', 'D1AllInOne', 0);

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

// reset the archers from TeamComponents to TeamFinComponents
safe_w_sql("delete from TeamFinComponent where TfcEvent='$Event' and TfcTournament={$_SESSION['TourId']}");
safe_w_sql("insert into TeamFinComponent (TfcCoId, TfcSubTeam, TfcTournament, TfcEvent, TfcId, TfcOrder) 
	select TcCoId, TcSubTeam, TcTournament, TcEvent, TcId, TcOrder from TeamComponent 
	where TcEvent='$Event' and TcTournament={$_SESSION['TourId']} and TcFinEvent=1
");

if($AllInOne) {
	safe_w_sql("update Events set EvShootOff=1 where EvCode like '$Event%' and EvTournament={$_SESSION['TourId']}");
	$JSON['msg']='OK';

} else {
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
			safe_w_sql("update Finals set FinAthlete=$r->EnId, FinTie=0, FinWinLose=0 where FinEvent=".StrSafe_DB($Event.$i)." and FinMatchNo=$MatchNo and FinTournament={$_SESSION['TourId']}");
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

		// check all the byes
		$SQL = "select f1.FinAthlete as Opp1, f2.FinAthlete as Opp2, f1.FinMatchNo, f1.FinEvent
			from Finals f1
			inner join Finals f2 on f2.FinEvent=f1.FinEvent and f2.FinTournament=f1.FinTournament and f2.FinMatchNo=f1.FinMatchNo+1
			where f1.FinMatchNo % 2 = 0 and (f1.FinAthlete=0 or f2.FinAthlete=0) and (f1.FinAthlete!=0 or f2.FinAthlete!=0) and f1.FinTournament={$_SESSION['TourId']}";

		$q=safe_r_sql($SQL);
		while($r=safe_fetch($q)) {
			safe_w_sql("update Finals set FinTie=2, FinWinLose=1 where FinEvent='$r->FinEvent' and FinMatchNo=".($r->Opp1 ? $r->FinMatchNo : ($r->Opp2 ? $r->FinMatchNo+1 : 999))." and FinTournament={$_SESSION['TourId']}");
		}

		$JSON['msg']='OK';
	}
}

set_qual_session_flags();

JsonOut($JSON);
