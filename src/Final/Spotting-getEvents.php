<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_Phases.inc.php');

checkACL(array(AclIndividuals,AclTeams, AclOutput), AclReadOnly, false);

$JSON=array('error' => 1, 'data'=>array('Team'=>array(),'Ind'=>array()));

$Sql = "SELECT EvCode, EvEventName as EvName, EvTeamEvent as isTeam, EvFinalFirstPhase AS StartPhase, EvElimType 
  FROM Events 
  WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvFinalFirstPhase != 0
  order by EvProgr";

$q=safe_r_sql($Sql);
while($r=safe_fetch($q)) {
    $isTeam = ($r->isTeam ? 'Team':'Ind');
    if(!array_key_exists($r->EvCode,$JSON['data'][$isTeam])) {
        $JSON['data'][$isTeam][$r->EvCode] = array('name'=>$r->EvName, 'phases'=>array());
    }
    if($r->EvElimType==3) {
		// WG Format 2 pools
        $JSON['data'][$isTeam][$r->EvCode]['phases'][] = array('id'=>'A', 'name'=>get_text('PoolName', 'Tournament', 'A'));
        $JSON['data'][$isTeam][$r->EvCode]['phases'][] = array('id'=>'B', 'name'=>get_text('PoolName', 'Tournament', 'B'));
        $JSON['data'][$isTeam][$r->EvCode]['phases'][] = array('id'=>'C', 'name'=>'Show Match 1st vs 2nd');
    } elseif($r->EvElimType==4) {
		// WA Format, 4 pools
        $JSON['data'][$isTeam][$r->EvCode]['phases'][] = array('id'=>'A', 'name'=>get_text('PoolName', 'Tournament', 'A'));
        $JSON['data'][$isTeam][$r->EvCode]['phases'][] = array('id'=>'B', 'name'=>get_text('PoolName', 'Tournament', 'B'));
        $JSON['data'][$isTeam][$r->EvCode]['phases'][] = array('id'=>'C', 'name'=>get_text('PoolName', 'Tournament', 'C'));
        $JSON['data'][$isTeam][$r->EvCode]['phases'][] = array('id'=>'D', 'name'=>get_text('PoolName', 'Tournament', 'D'));
    }
    foreach(getPhasesId($r->StartPhase) as $ph) {
        $JSON['data'][$isTeam][$r->EvCode]['phases'][] = array('id'=>$ph, 'name'=>get_text(namePhase($r->StartPhase,$ph).'_Phase'));
    }
    $JSON['error']=0;
}

JsonOut($JSON);