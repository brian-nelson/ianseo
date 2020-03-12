<?php
require_once(dirname(dirname(__FILE__)).'/config.php');
require_once('Common/Lib/Fun_Modules.php');
require_once('Common/Fun_Phases.inc.php');

$JSON=array('error'=>true, 'data'=>array());
$SelectedEvent='';
$TourId=0;
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
    $TourId = getIdFromCode($_REQUEST['CompCode']);
}
if($TourId == 0) {
    JsonOut($JSON);
} else {
    $JSON['error']=false;
}

if($IskSequence=getModuleParameter('ISK', 'Sequence','',$TourId)) {
    if(!isset($IskSequence['session'])) {
        $IskSequence=current($IskSequence);
    }
    // get the running sequence
    $SelectedEvent="concat(FSScheduledDate,FSScheduledTime) = '{$IskSequence['session']}'";
}

$Sql="SELECT DISTINCT FsTeamEvent, FsScheduledDate, concat(FsScheduledDate, ' ',  date_format(FsScheduledTime, '%H:%i')) MyDate, 
        date_format(FsScheduledDate,'%d %M') as MyViewDate,date_format(FsScheduledTime, '%H:%i') MyViewTime,
		EvFinalFirstPhase, GrPhase, group_concat(DISTINCT FsEvent ORDER BY FsEvent SEPARATOR ',') Events, {$SelectedEvent} as SelectedEvent
	FROM FinSchedule
	INNER JOIN Events ON FsEvent=EvCode AND FsTeamEvent=EvTeamEvent and FsTournament=EvTournament
	INNER JOIN Grids ON FsMatchNo=GrMatchNo
	WHERE FsTournament={$TourId} AND FsScheduledDate>0 AND FsScheduledTime>0
	GROUP BY MyDate, FsTeamEvent, GrPhase
	ORDER BY MyDate, FsTeamEvent, GrPhase DESC";

$Schedule=array();

$Rs=safe_r_sql($Sql);
while ($myRow=safe_fetch($Rs)) {
    $k="{$myRow->FsTeamEvent}{$myRow->MyDate}";
    if(empty($Schedule[$k])) {
        $Schedule[$k]=array(
            'team'=>'',
            'sel'=>'',
            'time'=>'',
            'day'=>'',
            'txt'=>array(),
        );
    }
    $Schedule[$k]['team']=($myRow->FsTeamEvent ? true : false);
    $Schedule[$k]['sel']=($myRow->SelectedEvent ? '1' : '0');
    $Schedule[$k]['day']=$myRow->MyViewDate;
    $Schedule[$k]['date']=$myRow->FsScheduledDate;
    $Schedule[$k]['time']=$myRow->MyViewTime;
    $Schedule[$k]['txt'][]=get_text(namePhase($myRow->EvFinalFirstPhase,$myRow->GrPhase).'_Phase') . ' '. $myRow->Events;
}

$tmp = Array();
foreach($Schedule as $MyDate => $Items) {
    if(!array_key_exists($Items['day'],$tmp)) {
        $tmp[$Items['day']]=array('day'=>$Items['day'],'date'=>$Items['date'],'schedule'=>array());
    }

    $tmp[$Items['day']]['schedule'][]=array(
        'key' => $MyDate,
        'time' => $Items['time'],
        'event' => $Items['time'] . ' - '.  implode(' + ', $Items['txt']),
        'team' => $Items['team'],
        'selected' => $Items['sel'] ? true : false,
    );
}
$JSON['data']=array_values($tmp);


JsonOut($JSON);