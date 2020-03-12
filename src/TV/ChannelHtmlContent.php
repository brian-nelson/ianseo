<?php

if(empty($_REQUEST['id'])) die();

$Channel=intval($_REQUEST['id']);

require_once(dirname(dirname(__FILE__)) . '/config.php');

$NOSTYLE=true;

include('Common/Templates/head-caspar.php');

$q=safe_r_sql("SELECT TVOId , TVOName, TVOUrl, TVOMessage, TVORuleId, TVOTourCode, TVORuleType
		FROM TVOut
		where TVORuleType>0 and TVOId=$Channel");
$r=safe_fetch($q);

echo $r->TVOMessage;

include('Common/Templates/tail-min.php');
