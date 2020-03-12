<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

if (!CheckTourSession()) {
    exit;
}
checkACL(AclISKServer, AclReadWrite,false);

$devId = (isset($_REQUEST["device"]) ? $_REQUEST["device"] : '');
$tourid = (!empty($_REQUEST["setCompetition"]) && $_REQUEST["setCompetition"]=='true' ? $_SESSION["TourId"] : 0);
$tgtno = (!empty($_REQUEST["setTarget"]) && is_numeric($_REQUEST["setTarget"]) ? $_REQUEST["setTarget"] : 0);
$status = (isset($_REQUEST["setStatus"]) ? intval($_REQUEST["setStatus"]) : 0);

$error = 1;
$Sql = "";
if(isset($_REQUEST["setCompetition"])) {
    $Sql = "UPDATE IskDevices SET `IskDvTournament`='{$tourid}', IskDvState=0 WHERE IskDvDevice=" . StrSafe_DB($devId);
} elseif(!empty($tgtno)) {
    $Sql = "UPDATE IskDevices SET `IskDvTarget`='{$tgtno}' WHERE IskDvDevice=" . StrSafe_DB($devId);
} elseif(isset($_REQUEST["setStatus"])) {
    $Sql = "UPDATE IskDevices SET `IskDvState`='{$status}' WHERE IskDvDevice=" . StrSafe_DB($devId);
}


if(strlen($Sql)) {
    $Rs=safe_w_SQL($Sql);
    if(safe_w_affected_rows()!=0)
        $error=0;
}
header('Content-Type: text/xml');
print '<response error="' . $error . '" />';