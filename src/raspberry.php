<?php
require_once('./config.php');

$Device='default';
if(!empty($_REQUEST['device'])) $Device=preg_replace('/[^a-z0-9_.-]/sim', '', $_REQUEST['device']);
$q=safe_r_sql("select * from Raspberries where RasDevice='$Device' and RasActive=1");

if($r=safe_fetch($q)) {
	if(safe_num_rows($q)==1) safe_w_sql("update Raspberries set RasLastseen='".date('Y-m-d H:i:s')."', RasIp='{$_SERVER['REMOTE_ADDR']}' where RasDevice='$Device'");
	if($r->RasUrl) {
		header("Location: $r->RasUrl");
		die();
	}
	if($r->RasTourCode and $r->RasRot) {
		header("Location: ".getMyScheme()."://{$_SERVER['SERVER_NAME']}{$CFG->ROOT_DIR}TV/".($r->RasType ? 'LightRot.php' : 'Rot/index.php')."?Rule={$r->RasRot}&Tour={$r->RasTourCode}");
		die();
	}
} else {
	safe_w_sql("insert into Raspberries set RasDevice='$Device', RasIp='{$_SERVER['REMOTE_ADDR']}' on duplicate key update RasIp='{$_SERVER['REMOTE_ADDR']}', RasLastseen='".date('Y-m-d H:i:s')."'");
}

?><html><head><meta HTTP-EQUIV="refresh" CONTENT="10" /></head>
<body>
<h1><?php
echo $_REQUEST['device'] . ' ' . rand(1,100) ;
?></h1></body></html>