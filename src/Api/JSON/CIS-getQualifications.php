<?php

require_once(dirname(__FILE__).'/config.php');

require_once('Common/OrisFunctions.php');

$Sessions=array();
$IndEvents=array();
$TeamEvents=array();

if(!empty($_REQUEST['sessions'])) {
	$Sessions=$_REQUEST['Session']=explode('|', $_REQUEST['sessions']);
}
$_SESSION['CIS']['QUAL']['Sessions']=$Sessions;

if(!empty($_REQUEST['ievents'])) {
	$IndEvents=explode('|', $_REQUEST['ievents']);
}
$_SESSION['CIS']['QUAL']['IndEvents']=$IndEvents;

if(!empty($_REQUEST['tevents'])) {
	$TeamEvents=explode('|', $_REQUEST['tevents']);
}
$_SESSION['CIS']['QUAL']['TeamEvents']=$TeamEvents;

if(empty($_REQUEST['type'])) {
	$_REQUEST['type']='0';
}

if($_REQUEST['type']=='cut') {
	$Number='cut';
} else {
	$Number=intval($_REQUEST['type']);
}
$_SESSION['CIS']['QUAL']['Number']=$Number;

// Initialize
$rankDataI=array('sections' => array());
$rankDataT=array('sections' => array());

$options=array('dist'=>0);
if($Sessions) {
	$options['session']=$Sessions;
}
if($Number) {
	$options['cutRank']=$Number;
}

if(!$TeamEvents or $IndEvents) {
	// get the individual events
	if($IndEvents) {
		$options['events']=$IndEvents;
	}
	$rank=Obj_RankFactory::create('Abs', $options);
	$rank->read();
	$rankDataI=$rank->getData();
	$lastUpdate=strtotime($rankDataI['meta']['lastUpdate']);
}

if(!$IndEvents or $TeamEvents) {
	// get the individual events
	if($TeamEvents) {
		$options['events']=$TeamEvents;
	}
	$rank=Obj_RankFactory::create('AbsTeam', $options);
	$rank->read();
	$rankDataT=$rank->getData();
	$lastUpdate=strtotime($rankDataT['meta']['lastUpdate']);
}

$JSON=array('error' => 0, 'data' => array());

// show Individuals
foreach($rankDataI['sections'] as $EventCode => $Section) {
	$Data=array(
		'Timestamp' => $Section['meta']['lastUpdate'],
		'Event' => $Section['meta']['descr'],
		'Session' => $rankDataI['meta']['title'],
		'Cut'=>intval($Section['meta']['qualifiedNo']),
		'Header'=>array(),
		'Items' => array(),
	);

	$Data['Header'] = array(
		'Tgt' => $Section['meta']['fields']['target'],
		'Ath' => $Section['meta']['fields']['athlete'],
		'Noc' => $Section['meta']['fields']['countryCode'],
		'Nat' => $Section['meta']['fields']['countryName'],
		'Rnk' => $Section['meta']['fields']['rank'],
		'Pts' => $Section['meta']['fields']['score'],
		);

	foreach($Section['items'] as $Item) {
		$archer=array();
		$archer['Id']=$Item['id'];
		$archer['Tgt']=ltrim($Item['target'], '0');
		$archer['Ath']=$Item['athlete'];
		$archer['Noc']=$Item['countryCode'];
		$archer['Nat']=$Item['countryName'];
		$archer['Rnk']=intval($Item['rank']);
		$archer['Pts']=$Item['score'];
		$Data['Items'][]=$archer;
	}

	$JSON['data'][]=$Data;
}

// show Teams
foreach($rankDataT['sections'] as $EventCode => $Section) {
	$Data=array(
		'Timestamp' => $Section['meta']['lastUpdate'],
		'Event' => $Section['meta']['descr'],
		'Session' => $rankDataT['meta']['title'],
		'Cut'=>intval($Section['meta']['qualifiedNo']),
		'Header'=>array(),
		'Items' => array(),
	);

	$Data['Header'] = array(
		'Tgt' => $Section['meta']['fields']['athletes']['fields']['target'],
		'Ath' => $Section['meta']['fields']['athletes']['name'],
		'Noc' => $Section['meta']['fields']['countryCode'],
		'Nat' => $Section['meta']['fields']['countryName'],
		'Rnk' => $Section['meta']['fields']['rank'],
		'Pts' => $Section['meta']['fields']['score'],
		);

	foreach($Section['items'] as $Item) {
		$team=array(
			'names' => array(),
			'target' => array(),
		);
		foreach($Item['athletes'] as $v) {
			$team['names'][]=$v['athlete'];
			$team['target'][]=ltrim($v['target'], '0');
		}
		$archer=array();
		$archer['Id']=$Item['id'];
		$archer['Tgt']=implode('<br/>', $team['target']);
		$archer['Ath']=implode('<br/>', $team['names']);
		$archer['Noc']=$Item['countryCode'];
		$archer['Nat']=$Item['countryName'];
		$archer['Rnk']=intval($Item['rank']);
		$archer['Pts']=$Item['score'];
		$Data['Items'][]=$archer;
	}

	$JSON['data'][]=$Data;
}

JsonOut($JSON);

