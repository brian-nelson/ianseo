<?php

require_once(dirname(__FILE__).'/config.php');

require_once('Common/OrisFunctions.php');

$Sessions=array();
if(!empty($_REQUEST['sessions'])) {
	$Sessions=explode('|', $_REQUEST['sessions']);
}

$_SESSION['CIS']['PART']['Sessions']=$Sessions;

$Events=array();
if(!empty($_REQUEST['events'])) {
	$Events=explode('|', $_REQUEST['events']);
}
$_SESSION['CIS']['PART']['Events']=$Events;

if(empty($_REQUEST['type'])) {
	$_REQUEST['type']='TGT';
}

switch($_REQUEST['type']) {
	case 'TGT':
		$Type='TGT';
		$List=getStartList(true, $Events, '', true);
		break;
	default:
		$Type='NOC';
		$List=getStartListByCountries(true, 1, '', $Events, $Sessions);
}

$_SESSION['CIS']['PART']['NocTgt']=$Type;

$Data=array(
	'Timestamp' => '',
	'Events' => $Events,
	'Sessions' => $Sessions,
	'Type' => $Type,
	'Header' => array(),
	'Data' => array(),
);

$Data['Header'] = array(
	'Bib' => $List->Data['Fields']['Bib'],
	'TargetNo' => $List->Data['Fields']['TargetNo'],
    'Athlete' => $List->Data['Fields']['Athlete'],
	'NationCode' => $List->Data['Fields']['NationCode'],
	'Nation' => $List->Data['Fields']['Nation'],
	'DivCode' => $List->Data['Fields']['DivCode'],
	'ClassCode' => $List->Data['Fields']['ClassCode'],
	'DivDescription' => $List->Data['Fields']['DivDescription'],
	'ClDescription' => $List->Data['Fields']['ClDescription'],
	'Category' => $List->Data['Fields']['Category'],
	'RealEventCode' => $List->Data['Fields']['EventCode'],
	'RealEventName' => $List->Data['Fields']['EventName'],
	'Ranking' => $List->HeaderPool[5],
	);
foreach($List->Data['Items'] as $k => $items) {
	$data=array();
	foreach($items as $item) {
		$data[]=array(
			'Bib' => $item->Bib,
			'TargetNo' => ltrim($item->TargetNo,'0'),
            'Athlete' => $item->Athlete,
			'NationCode' => $item->NationCode,
			'Nation' => $item->Nation,
			'DivCode' => $item->DivCode,
			'ClassCode' => $item->ClassCode,
			'DivDescription' => $item->DivDescription,
			'ClDescription' => $item->ClDescription,
			'RealEventCode' => isset($item->RealEventCode) ? $item->RealEventCode : $item->EventCode,
			'RealEventName' => isset($item->RealEventName) ? $item->RealEventName : $item->EventName,
			'Ranking' => $item->Ranking ,
		);
	}
	$Data['Data'][]=$data;
}


JsonOut($Data);


//Session
//
