<?php
require_once(dirname(__FILE__) . '/config.php');


$TourId = 0;
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
	$TourId = getIdFromCode($_REQUEST['CompCode']);
}

$json_array=array();


$Sql = "SELECT SesOrder, SesName, SesDtStart, SesDtEnd FROM Session WHERE SesTournament={$TourId} AND SesType='F' ORDER BY SesDtStart, SesDtEnd";
$q=safe_r_SQL($Sql);
while($r=safe_fetch($q)) {
	$json_array[] = array("Id"=>$r->SesOrder, "Name"=>$r->SesName, "DateTimeFrom"=>substr($r->SesDtStart,0,-3), "DateTimeTo"=>substr($r->SesDtEnd,0,-3));
}




// Return the json structure with the callback function that is needed by the app
SendResult($json_array);
