<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

CheckTourSession(true);

$EnId=(empty($_GET['EnId']) ? intval($_GET['EnId']) : 0);
$EnCo1=(empty($_GET['EnCo1']) ? intval($_GET['EnCo1']) : 0);
$EnCo2=(empty($_GET['EnCo2']) ? intval($_GET['EnCo2']) : 0);
$EnCo3=(empty($_GET['EnCo3']) ? intval($_GET['EnCo3']) : 0);
if(!$EnId and !($EnCo1 or $EnCo2 or $EnCo3)) die('<html><head><script>window.close()</script></head></html>');

safe_w_sql("update Entries set EnCountry=$EnCo1, EnCountry2=$EnCo2, EnCountry3=$EnCo3 where EnId=$EnId");

die('<html><head><script>window.close()</script></head></html>');

