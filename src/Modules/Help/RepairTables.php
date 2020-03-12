<?php
// creates a dump of the DB structure
apache_setenv('no-gzip', '1');
if (ob_get_level() == 0) ob_start();

require_once('../config.php');

include('Common/Templates/head.php');

$Tables=array();
$q=safe_r_sql("show tables");
$f=safe_fetch_field($q);
while($r=safe_fetch_assoc($q)) {
	echo '<div>Repairing table '.$r[$f->name].'... ';
	ob_flush();
	flush();
	$t=safe_r_SQL("repair table {$r[$f->name]}");
	echo 'done; optimizing... ';
	ob_flush();
	flush();
	$t=safe_r_SQL("optimize table {$r[$f->name]}");
	echo 'done!</div>';
	ob_flush();
	flush();
}

include('Common/Templates/tail.php');
ob_end_flush();
