<?php

require_once(dirname(dirname(__FILE__)).'/config.php');

$JSON=array('error' => 1, 'status' => 0);

if(!CheckTourSession()) {
	JsonOut($JSON);
	die();
}
checkACL(AclQualification, AclReadWrite, false);

// toggle the snapshot feature
require_once('Common/Lib/Fun_Modules.php');
$status=intval(getModuleParameter('ISK', 'Snapshot'));

setModuleParameter('ISK', 'Snapshot', 1-$status);

$JSON['error']=0;
$JSON['status']=1-$status;

JsonOut($JSON);
