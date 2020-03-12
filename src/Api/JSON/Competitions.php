<?php
require_once(dirname(__FILE__) . '/config.php');

$JsonResponse = array();
$sql = "SELECT ToCode, ToName, ToNameShort, ToWhere, ToWhenFrom, ToWhenTo, CURDATE() as Today, ToCategory
	FROM Tournament
	ORDER BY ToWhenFrom DESC, ToCode";
$rs = safe_r_sql($sql);
while($row = safe_fetch($rs)) {
	$JsonResponse[] = array(
			"CompCode"=>$row->ToCode,
			"Name"=>$row->ToName,
			"ShortName"=>$row->ToNameShort,
			"Place"=>$row->ToWhere,
			"DateFrom"=>$row->ToWhenFrom,
			"DateTo"=>$row->ToWhenTo
	);
}

SendResult($JsonResponse);