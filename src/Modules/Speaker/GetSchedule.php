<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/Fun_Modules.php');
require_once('Common/Fun_Phases.inc.php');

$JSON=array('error' => 1, 'rows' => array(), 'running'=>'', 'onlytoday'=>'1');

if (empty($_SESSION['TourId'])) {
	JsonOut($JSON);
}
checkACL(array(AclSpeaker, AclQualification, AclEliminations, AclIndividuals, AclTeams), AclReadOnly);

$Today=date('Y-m-d');
$UseHHT='';
$OnlyToday='';
$SelectedEvent="''";

if(isset($_REQUEST["useHHT"]) && $_REQUEST["useHHT"]) {
	$UseHHT="INNER JOIN HhtEvents on HeTournament=FsTournament and HeFinSchedule=concat(FSScheduledDate, ' ', FSScheduledTime) and HeTeamEvent=FsTeamEvent";
}

if(isset($_REQUEST["onlyToday"]) && $_REQUEST["onlyToday"]) {
	$OnlyToday="AND FSScheduledDate='$Today'";
}

if($IskSequence=getModuleParameter('ISK', 'Sequence')) {
	if(!isset($IskSequence['session'])) {
		$IskSequence=current($IskSequence);
	}
	// get the running sequence
	$SelectedEvent="concat(FSScheduledDate,FSScheduledTime) = '{$IskSequence['session']}'";
	$JSON['running']=$IskSequence['session'];
	if(!empty($_REQUEST['reset']) and $OnlyToday and !strstr($IskSequence['session'], $Today)) {
		$JSON['onlytoday']=0;
		$OnlyToday='';
	}
}

$Select="select distinct
		FsTeamEvent,
		concat(FsScheduledDate, ' ',  date_format(FsScheduledTime, '%H:%i')) MyDate,
		EvFinalFirstPhase, GrPhase,
		group_concat(distinct FsEvent order by FsEvent separator ', ') Events,
		$SelectedEvent as SelectedEvent
	from FinSchedule
	inner join Events on FsEvent=EvCode and FsTeamEvent=EvTeamEvent and FsTournament=EvTournament
	inner join Grids on FsMatchNo=GrMatchNo $UseHHT
	where FsTournament={$_SESSION['TourId']}
		and FsScheduledDate>0 and FsScheduledTime>0
		$OnlyToday
	group by MyDate, FsTeamEvent, GrPhase
	order by MyDate, FsTeamEvent, GrPhase desc";



// $Select = "(SELECT DISTINCT CONCAT(FSScheduledDate,' ',date_format(FSScheduledTime, '%H:%i')) AS MyDate, FSTeamEvent, $SelectedEvent as SelectedEvent
// 		FROM Tournament INNER JOIN FinSchedule ON ToId=FSTournament $UseHHT
// 		WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " and FSScheduledDate>0 and FSTeamEvent=0 $OnlyToday)
// 	UNION ALL
// 		(SELECT DISTINCT CONCAT(FSScheduledDate,' ',date_format(FSScheduledTime, '%H:%i')) AS MyDate, FSTeamEvent, $SelectedEvent as SelectedEvent
// 		FROM Tournament INNER JOIN FinSchedule ON ToId=FSTournament $UseHHT
// 		WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " and FSScheduledDate>0 and FSTeamEvent!=0 $OnlyToday)
// 	ORDER BY MyDate ASC ";

$JSON['error']=0;

$Schedule=array();

$Rs=safe_r_sql($Select);
while ($myRow=safe_fetch($Rs)) {
	$k="{$myRow->FsTeamEvent}{$myRow->MyDate}";
	if(empty($Schedule[$k])) {
		$Schedule[$k]=array(
			'team'=>'',
			'sel'=>'',
			'txt'=>array(),
		);
	}
	$Schedule[$k]['team']=($myRow->FsTeamEvent ? get_text('Team'):get_text('Individual'));
	$Schedule[$k]['sel']=($myRow->SelectedEvent ? '1' : '0');
	$Schedule[$k]['txt'][]=get_text(namePhase($myRow->EvFinalFirstPhase,$myRow->GrPhase).'_Phase') . ' '. $myRow->Events;
}

foreach($Schedule as $MyDate => $Items) {
	$JSON['rows'][]=array(
		'val' => $MyDate,
		'txt' => $Items['team'] . ' ' . substr($MyDate,1) . ' - '. implode(' + ', $Items['txt']),
		'sel' => $Items['sel'] ? '1' : '0',
	);
}

JsonOut($JSON);
