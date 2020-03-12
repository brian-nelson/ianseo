<?php
require_once(dirname(dirname(__FILE__)).'/config.php');

$JSON=array('error'=>true, 'data'=>array());
$TourId=0;
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
    $TourId = getIdFromCode($_REQUEST['CompCode']);
}
$Code='';
if(isset($_REQUEST['Code']) && preg_match("/^[a-z0-9]+$/i", $_REQUEST['Code'])) {
    $Code=$_REQUEST['Code'];
}
$Division='';
if(isset($_REQUEST['D']) && preg_match("/^[a-z0-9]{1,2}$/i", $_REQUEST['D'])) {
    $Division=$_REQUEST['D'];
}

if($TourId == 0 OR $Code == '' OR $Division == '' ) {
    JsonOut($JSON);
} else {
    $JSON['error']=false;
}

$Sql = "SELECT EnId 
  FROM Entries 
  WHERE EnCode='{$Code}' AND EnDivision='{$Division}' AND EnTournament='{$TourId}'";
$q=safe_r_SQL($Sql);
if($r=safe_fetch($q) AND (file_exists($im=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_REQUEST['CompCode'].'-En-'.$r->EnId.'.jpg'))) {
    $JSON['data']='data:image/jpeg;base64,'.base64_encode(file_get_contents($im));
}

JsonOut($JSON);