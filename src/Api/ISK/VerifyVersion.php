<?php

$SkipCompCode=true;
require_once(dirname(__FILE__) . '/config.php');

$JsonResponse=array(
	'compatible' => true,
	'ianseoversion' => ProgramVersion,
	'minappversion' => $AppMinVersion,
	'maxappversion' => $AppMaxVersion
	);

if(empty($_REQUEST['version'])) {
	$JsonResponse['compatible']=false;
} else {
	$AppMinVersion=explode('.', $AppMinVersion);
	$AppMinVersion=sprintf('%03s-%03s-%04s-', $AppMinVersion[0], $AppMinVersion[1], $AppMinVersion[2]);
	$AppMaxVersion=explode('.', $AppMaxVersion);
	$AppMaxVersion=sprintf('%03s-%03s-%04s-', $AppMaxVersion[0], $AppMaxVersion[1], $AppMaxVersion[2]);
	$AppVersion=explode('.', $_REQUEST['version']);
	while(count($AppVersion)<3) $AppVersion[]=0;
	$AppVersion=sprintf('%03s-%03s-%04s-', $AppVersion[0], $AppVersion[1], $AppVersion[2]);

	if($AppVersion<$AppMinVersion or $AppVersion>$AppMaxVersion) {
		$JsonResponse['compatible']=false;
	}
}

SendResult($JsonResponse);