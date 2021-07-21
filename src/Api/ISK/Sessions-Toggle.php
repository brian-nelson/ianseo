<?php
require_once(__DIR__.'/config-ianseo.php');

$JSON=array('error'=>1, 'msg'=>get_text('Error'), 'status'=>array());

if(!CheckTourSession() or checkACL(AclISKServer, AclReadWrite, false)!=AclReadWrite or empty($_REQUEST['key'])) {
	JsonOut($JSON);
}

require_once(__DIR__.'/Lib.php');
$LockSessions=getModuleParameter('ISK', 'LockedSessions', array());

if($_REQUEST['key']=='lockall') {
	$q=safe_r_sql(GetLockableSessions());
	$LockSessions=array();
	while($r=safe_fetch($q)) {
		$LockSessions[]=$r->LockKey;
		$JSON['status'][$r->LockKey]=1;
	}
} elseif($_REQUEST['key']=='unlockall') {
	$q=safe_r_sql(GetLockableSessions());
	$LockSessions=array();
	while($r=safe_fetch($q)) {
		$JSON['status'][$r->LockKey]=0;
	}
} else {
	if(in_array($_REQUEST['key'], $LockSessions)) {
		unset($LockSessions[array_search($_REQUEST['key'], $LockSessions)]);
		$JSON['status'][$_REQUEST['key']]=0;
	} else {
		$LockSessions[]=$_REQUEST['key'];
		$JSON['status'][$_REQUEST['key']]=1;
	}
}
$JSON['error']=0;

setModuleParameter('ISK', 'LockedSessions', $LockSessions);

JsonOut($JSON);
