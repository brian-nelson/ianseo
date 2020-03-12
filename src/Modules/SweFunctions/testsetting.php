<?php
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

CheckTourSession(true);

require_once(dirname(__FILE__).'/config.php');
$url = $swe_url_test;
// Read post_body for json data
$request_body = file_get_contents('php://input');
$data = json_decode($request_body,true);

echo sendInformation($url, json_encode($data));

function sendInformation($url, $data) {
    $hand = curl_init($url);
    curl_setopt($hand, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($hand, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($hand, CURLOPT_POSTFIELDS, $data);
    curl_setopt($hand, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($hand, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($hand, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
    $result = curl_exec($hand);
    curl_close($hand);
    return $result;
}
?>
