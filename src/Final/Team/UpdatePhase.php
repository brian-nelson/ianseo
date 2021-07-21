<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Fun_Sessions.inc.php');
if (!CheckTourSession() || !isset($_REQUEST['EvCode']) || !isset($_REQUEST['NewPhase'])) {
    print get_text('CrackError');
    exit;
}
checkACL(AclCompetition, AclReadWrite, false);

$JSON=array('error' => 1, 'events' => array());

if (IsBlocked(BIT_BLOCK_TOURDATA)) {
    JsonOut($JSON);
}

$NewPhase=intval($_REQUEST['NewPhase']);
$NumQualified=numQualifiedByPhase($NewPhase);
$GridPhase=valueFirstPhase($NewPhase);

// aggiorno la fase
$Update = "UPDATE Events SET 
	EvFinalFirstPhase=$NewPhase, EvNumQualified=$NumQualified
	WHERE EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
$Rs=safe_w_sql($Update);


$JSON['error']=0;


if (safe_w_affected_rows()) {
    // Distruggo la griglia
    $Delete = "DELETE FROM TeamFinals WHERE TfEvent=" . StrSafe_DB($_REQUEST['EvCode']) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
    $Rs=safe_w_sql($Delete);

    if($GridPhase) {
	    // Deletes unused warmups
	    $delSchedule = "DELETE FROM FinWarmup USING
	        Events
	        INNER JOIN FinSchedule ON EvCode = FsEvent AND EvTeamEvent = FsTeamEvent AND EvTournament = FsTournament
	        INNER JOIN Grids ON GrMatchNo = FsMatchNo
	        INNER JOIN FinWarmup on FsEvent=FwEvent and FsTeamEvent=FwTeamEvent and FsTournament=FwTournament and FsScheduledDate=FwDay and FsScheduledTime=FwMatchTime
	        WHERE GrPhase > $GridPhase
	        AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='1' AND EvCode=" . StrSafe_DB($_REQUEST['EvCode']);
	    $RsDel=safe_w_sql($delSchedule);

	    //Cancello lo schedule non in uso
	    $delSchedule = "DELETE FROM FinSchedule USING
	        Events
	        INNER JOIN FinSchedule ON EvCode = FsEvent AND EvTeamEvent = FsTeamEvent AND EvTournament = FsTournament
	        INNER JOIN Grids ON GrMatchNo = FsMatchNo
	        WHERE GrPhase > $GridPhase
	        AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='1' AND EvCode=" . StrSafe_DB($_REQUEST['EvCode']);
	    $RsDel=safe_w_sql($delSchedule);

	    // Creo la griglia
	    $Insert = "INSERT INTO TeamFinals (TfEvent,TfMatchNo,TfTournament,TfDateTime) 
	        SELECT EvCode,GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . "," . StrSafe_DB(date('Y-m-d H:i:s')) . " 
	        FROM Events 
	        INNER JOIN Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2,EvTeamEvent))>0
	        INNER JOIN Grids ON GrPhase<=greatest(PhId, PhLevel) AND EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " 
	        WHERE EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " ";
        $RsIns = safe_w_sql($Insert);
    } else {
	    // deletes warmups
	    $delSchedule = "DELETE FROM FinWarmup WHERE FwTournament={$_SESSION['TourId']} AND FwTeamEvent=1 AND FwEvent=" . StrSafe_DB($_REQUEST['EvCode']);
	    $RsDel=safe_w_sql($delSchedule);

	    // deletes schedule
	    $delSchedule = "DELETE FROM FinSchedule WHERE FsTournament={$_SESSION['TourId']} AND FsTeamEvent=1 AND FsEvent=" . StrSafe_DB($_REQUEST['EvCode']);
	    $RsDel=safe_w_sql($delSchedule);
    }

    // Azzero il flag di spareggio
    ResetShootoff($_REQUEST['EvCode'],1,0);


    // TODO: needs to check the descendent events!
    $q=safe_r_sql("select * from Events where EvFinalFirstPhase>" . StrSafe_DB($_REQUEST['NewPhase']/2) . " and EvTeamEvent='1' AND EvCodeParent=" . StrSafe_DB($_REQUEST['EvCode']) . " and EvTournament=" . StrSafe_DB($_SESSION['TourId']));
    while($r=safe_fetch($q)) {
        $JSON['events']=array_merge(deleteEvent($r->EvCode,1), $JSON['events']);
    }

}

JsonOut($JSON);
