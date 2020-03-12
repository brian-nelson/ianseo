<?php
// creates a dump of the DB structure

require_once('../config.php');

$Tables=array();
$q=safe_r_sql("show tables");
$f=safe_fetch_field($q);
while($r=safe_fetch_assoc($q)) {
	$t=safe_r_SQL("show create table {$r[$f->name]}");
	$h1=safe_fetch_field($t);
	$h2=safe_fetch_field($t);
	$u=safe_fetch_assoc($t);
	$Tables[$u[$h1->name]]=$u[$h2->name];
}

