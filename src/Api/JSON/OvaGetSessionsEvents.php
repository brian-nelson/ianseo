<?php
require_once(dirname(dirname(__FILE__)).'/config.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Qualification/Fun_Qualification.local.inc.php');

$JSON=array('error'=>true, 'data'=>array('Sessions'=>Array(), 'Events'=>Array()));
$TourId=0;
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
    $TourId = getIdFromCode($_REQUEST['CompCode']);
}
if($TourId == 0) {
    JsonOut($JSON);
} else {
    $JSON['error']=false;
}

foreach(GetSessions('Q',false, null, $TourId) as $Session) {
    $JSON['data']['Sessions'][] = array('key'=> $Session->SesOrder, 'value'=>$Session->SesName);
}

foreach(GetEvents('I',$TourId) as $Event) {
    $JSON['data']['Events'][] = array('key'=> $Event->EvCode, 'value'=>$Event->EvEventName, 'type'=>($Event->EvTeamEvent ? 'T' : 'I'));
}
foreach(GetEvents('T',$TourId) as $Event) {
    $JSON['data']['Events'][] = array('key'=> $Event->EvCode, 'value'=>$Event->EvEventName, 'type'=>($Event->EvTeamEvent ? 'T' : 'I'));
}


JsonOut($JSON);