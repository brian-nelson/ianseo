<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/Fun_DateTime.inc.php');
require_once('Scheduler/LibScheduler.php');
checkACL(AclCompetition, AclReadWrite, false);

$Errore=1;

if(!empty($_REQUEST['end'])) {
	foreach($_REQUEST['end'] as $Session => $Distances) {
		foreach($Distances as $Dist => $Value) {
			safe_w_sql("insert into DistanceInformation set
				DiTournament={$_SESSION['TourId']},
				DiDistance=$Dist,
				DiSession=$Session,
				DiType='Q',
				DiEnds=$Value
				on duplicate key update
				DiEnds=$Value,
				DiTourRules=''
				");
			$Errore=0;
		}
	}
} elseif(!empty($_REQUEST['arr'])) {
	foreach($_REQUEST['arr'] as $Session => $Distances) {
		foreach($Distances as $Dist => $Value) {
			safe_w_sql("insert into DistanceInformation set
				DiTournament={$_SESSION['TourId']},
				DiDistance=$Dist,
				DiSession=$Session,
				DiType='Q',
				DiArrows=$Value
				on duplicate key update
				DiArrows=$Value,
				DiTourRules=''
				");
			$Errore=0;
		}
	}
} elseif(!empty($_REQUEST['startday'])) {
	$ret=InsertSchedDate($_REQUEST['startday']);
	$Errore=$ret['error'];
	$Value=$ret['day'];
} elseif(!empty($_REQUEST['starttime'])) {
	$ret=InsertSchedTime($_REQUEST['starttime']);
	$Errore=$ret['error'];
	$Value=$ret['start'];
} elseif(!empty($_REQUEST['warmtime'])) {
	$ret=InsertSchedTime($_REQUEST['warmtime'], 'Warm');
	$Errore=$ret['error'];
	$Value=$ret['warmtime'];
} elseif(!empty($_REQUEST['duration'])) {
	$ret=InsertSchedDuration($_REQUEST['duration']);
	$Errore=$ret['error'];
	$Value=$ret['duration'];
} elseif(!empty($_REQUEST['warmduration'])) {
	$ret=InsertSchedDuration($_REQUEST['warmduration'], 'Warm');
	$Errore=$ret['error'];
	$Value=$ret['warmduration'];
} elseif(!empty($_REQUEST['comment'])) {
	$ret=InsertSchedComment($_REQUEST['comment']);
	$Errore=$ret['error'];
	$Value=$ret['options'];
}

header('Content-Type: text/xml');

echo '<response>';
echo '<error>' . $Errore . '</error>';
echo '<fld><![CDATA[' . $Value . ']]></fld>';
echo '</response>';
