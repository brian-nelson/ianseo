<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Fun_Modules.php');


$TourId = 0;
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
	$TourId = getIdFromCode($_REQUEST['CompCode']);
} else {
	$IsCode=GetParameter('CasparCode');
	$TourId=getIdFromCode($IsCode);
}

$json_array=array("Error"=>1, "Info"=>"");

$speed = '0.0';
$unit = '';
$direction = 0;


if(isset($_REQUEST['Speed']) AND preg_match("/^[0-9.]+$/", $_REQUEST['Speed'])) {
    $speed = number_format($_REQUEST['Speed'],1);
    if(isset($_REQUEST['Unit']) AND preg_match("/^(m\/s|km\/h|fps|mph)$/i", $_REQUEST['Unit'])) {
        $unit = mb_convert_case($_REQUEST['Unit'],MB_CASE_LOWER);
        if(isset($_REQUEST['Direction']) AND preg_match("/^[0-9]+$/", $_REQUEST['Direction'])) {
            $direction = (intval($_REQUEST['Direction']) % 360);
            $json_array["Error"]=0;

        }
    }
}

if( $json_array["Error"] != 1) {
    $json_array["Info"] = "Wind {$speed} {$unit}, direction {$direction}";
    runJack("Wind", $TourId, array("WindSpeed" => $speed, "WindDirection" => $direction, "WindUM" => $unit, "TourId" => $TourId));
}

SendResult($json_array);

