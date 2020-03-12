<?php
require_once(dirname(dirname(__FILE__)).'/config.php');

$overTgtDesc = array(0=>'',1=>' bis',2=>' ter');

$JSON=array('error'=>true, 'data'=>array());
$TourId=0;
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
    $TourId = getIdFromCode($_REQUEST['CompCode']);
}
if($TourId == 0) {
    JsonOut($JSON);
} else if(CreateTourSession($TourId)) {
    require_once('Common/OrisFunctions.php');
    $JSON['error']=false;
}

$Sessions=array();
if(!empty($_REQUEST['Sessions'])) {
    $Sessions=explode(',', $_REQUEST['Sessions']);
}
$Events=array();
if(!empty($_REQUEST['Events'])) {
    $tmp=explode(',', $_REQUEST['Events']);
    foreach ($tmp as $ev) {
        $Events[] = explode('|',$ev)[0];
    }
}

$List=getStartListByCountries(true, false,false, $Events, '');
foreach($List->Data['Items'] as $k => $items) {
    foreach($items as $item) {
        if(count($Sessions)==0 OR in_array($item->Session,$Sessions)) {
            $target = intval((!empty($List->BisTarget) AND (intval($item->TargetNo) > intval($List->NumEnd))) ? intval($item->TargetNo)-intval($List->NumEnd) : $item->TargetNo);
            $overTgt = (!empty($List->BisTarget) AND (intval($item->TargetNo) > intval($List->NumEnd))) ? intval(intval($item->TargetNo) / intval($List->NumEnd)) : 0;

            $JSON['data'][] = array(
                'Code' => $item->Bib,
                'Session' => $item->Session,
                'SessionName' => strval($item->SesName),
                'Target' => $target . substr($item->TargetNo,-1,1) . $overTgtDesc[$overTgt] ,
                'TargetNo' => $target,
                'TargetOrderBy' => ($item->Session*100000)+($target*100)+($overTgt*10)+(ord(substr($item->TargetNo,-1,1))-65),
                'Athlete' => $item->Athlete,
                'Noc' => $item->NationCode,
                'Nation' => $item->Nation,
                'Division' => $item->DivCode,
                'Class' => $item->ClassCode,
                'DivisionName' => $item->DivDescription,
                'ClassName' => $item->ClDescription,
                'Event' => isset($item->RealEventCode) ? $item->RealEventCode : $item->EventCode,
                'EventName' => isset($item->RealEventName) ? $item->RealEventName : $item->EventName,
                'IsAthlete' => ($item->IsAthlete== 1 ? true : false)
            );
        }
    }
}



JsonOut($JSON);