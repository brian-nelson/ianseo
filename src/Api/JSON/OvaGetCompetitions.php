<?php
require_once(dirname(dirname(__FILE__)).'/config.php');

$JSON=array('error'=>true, 'data'=>array());

$sql = "SELECT ToCode, ToName, ToWhenFrom, ToWhenTo
	FROM Tournament
	ORDER BY (CURDATE() BETWEEN ToWhenFrom AND ToWhenTo) DESC, ToWhenFrom DESC, ToCode";
$rs = safe_r_sql($sql);
while($row = safe_fetch($rs)) {
    $JSON['error']=false;
    $JSON['data'][] = array(
        "CompCode"=>$row->ToCode,
        "Name"=>$row->ToName,
        "DateFrom"=>$row->ToWhenFrom,
        "DateTo"=>$row->ToWhenTo
	);
}

JsonOut($JSON);