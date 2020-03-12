<?php
/**
 * Created by PhpStorm.
 * User: deligant
 * Date: 09/05/17
 * Time: 18.51
 */

require_once(dirname(dirname(__FILE__)).'/config.php');
checkACL(AclRoot, AclReadWrite);

require_once('Common/Lib/Fun_Modules.php');
require_once('Common/Lib/CommonLib.php');

$JSON=array(
	'error' => 1,
	);

if(empty($_REQUEST['toid']) or !($ToId=intval($_REQUEST['toid'])) or empty($_REQUEST['session'])) {
	JsonOut($JSON);
}

$Options=GetParameter('AccessApp', '', array(), true);

if(in_array($_REQUEST['session'], $Options[$ToId])) {
	unset($Options[$ToId][array_search($_REQUEST['session'], $Options[$ToId])]);
} else {
	$Options[$ToId][]=$_REQUEST['session'];
}

SetParameter('AccessApp', $Options, true);

$JSON['error']=0;

JsonOut($JSON);