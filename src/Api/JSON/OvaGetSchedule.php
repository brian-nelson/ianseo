<?php
require_once(dirname(dirname(__FILE__)).'/config.php');
require_once('Common/Lib/Fun_Scheduler.php');

$JSON=array('error'=>true, 'data'=>array());
$TourId=0;
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
    $TourId = getIdFromCode($_REQUEST['CompCode']);
}
if($TourId == 0) {
    JsonOut($JSON);
} else {
    $JSON['error']=false;
}

$Schedule=new Scheduler($TourId);
$Schedule->Finalists=true;
$JSON['data'] = $Schedule->getScheduleByDay();

JsonOut($JSON);