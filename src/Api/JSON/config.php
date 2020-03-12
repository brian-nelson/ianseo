<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
define("LastSeenTO",300);

function SendResult($Result) {
	parse_str($_SERVER["QUERY_STRING"], $headerArray);
	$headerArray = array("Request"=>basename($_SERVER["SCRIPT_NAME"],".php"), "Timestamp"=>$_SERVER['REQUEST_TIME']) + $headerArray;

	JsonOut(array("header"=>$headerArray, "data"=>$Result));
}
