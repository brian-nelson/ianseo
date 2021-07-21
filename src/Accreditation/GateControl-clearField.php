<?php

require_once(dirname(dirname(__FILE__)).'/config.php');
checkACL(AclRoot, AclReadWrite);

require_once('Common/Lib/Fun_Modules.php');
require_once('Common/Lib/CommonLib.php');
require_once('Accreditation/Lib.php');

$JSON=array(
	'error' => 1,
	'msg' => '',
	);

if(empty($_REQUEST['toid']) or !($ToId=intval($_REQUEST['toid']))) {
	JsonOut($JSON);
}

$q=safe_r_sql("select sum(GLDirection) as HowMany, GLEntry from GateLog where GLTournament=$ToId group by GlEntry having HowMany!=0");

while($r=safe_fetch($q)) {
	GateLog($r->GLEntry, 3, $ToId, -1*$r->HowMany);
}

$JSON['error']=0;
$JSON['msg']=get_text('FieldCleaned', 'Tournament');

JsonOut($JSON);