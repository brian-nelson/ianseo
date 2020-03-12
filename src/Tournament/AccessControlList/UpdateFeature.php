<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
checkACL(AclRoot, AclReadWrite);
if (!CheckTourSession())
    exit;

$Json = array();
if(!empty($_REQUEST["IP"]) AND $ip =checkValidIP($_REQUEST["IP"]) AND $ip!='127.0.0.1') {
    $name = empty($_REQUEST["Name"]) ? "" : filter_var($_REQUEST["Name"], FILTER_SANITIZE_STRING);
    $Sql = "INSERT INTO ACL (AclTournament, AclIP, AclNick, AclEnabled) VALUES (".StrSafe_DB($_SESSION['TourId']).",'{$ip}','{$name}',1) 
      ON DUPLICATE KEY UPDATE AclNick='{$name}'";
    $q = safe_w_SQL($Sql);
}
if(!empty($_REQUEST["deleteIP"]) AND $ip = checkValidIP($_REQUEST["deleteIP"])) {
    $Sql = "DELETE FROM AclDetails WHERE AclDtTournament=".StrSafe_DB($_SESSION['TourId'])." AND AclDtIP='{$ip}'";
    $q = safe_w_SQL($Sql);
    $Sql = "DELETE FROM ACL WHERE AclTournament=".StrSafe_DB($_SESSION['TourId'])." AND AclIP='{$ip}'";
    $q = safe_w_SQL($Sql);
}

if(isset($_REQUEST["featureIP"]) AND $ip = checkValidIP($_REQUEST["featureIP"]) AND isset($_REQUEST["levelID"]) AND preg_match("/^[0-2]{1}$/",$_REQUEST["levelID"])) {
    $level = intval($_REQUEST["levelID"]);
    if($level == AclNoAccess OR (isStarIP($ip) AND $level>AclReadOnly)) {
        $Sql = "DELETE FROM AclDetails WHERE AclDtTournament=".StrSafe_DB($_SESSION['TourId'])." AND AclDtIP='{$ip}'";
        safe_w_SQL($Sql);
    } else {
        foreach ($listACL as $k=>$v) {
            $lvl = (array_key_exists($k, $limitedACL) ?  ($limitedACL[$k]<=$level ? $limitedACL[$k] : 0) : $level);
            $Sql = "INSERT INTO AclDetails (AclDtTournament, AclDtIP, AclDtFeature, AclDtLevel) 
              VALUES (".StrSafe_DB($_SESSION['TourId']).", '{$ip}', {$k}, {$lvl}) 
              ON DUPLICATE KEY UPDATE AclDtLevel={$lvl}";
            safe_w_SQL($Sql);
        }
    }
}

if(isset($_REQUEST["featureIP"]) AND $ip = checkValidIP($_REQUEST["featureIP"]) AND isset($_REQUEST["featureID"]) AND preg_match("/^[0-9]+$/",$_REQUEST["featureID"]) AND ($feature = intval($_REQUEST["featureID"]))<count($listACL)) {
    $lvl = 0;
    $Sql = "SELECT AclDtLevel FROM AclDetails WHERE AclDtTournament=".StrSafe_DB($_SESSION['TourId'])." AND AclDtIP='{$ip}' && AclDtFeature={$feature}";
    $q=safe_r_SQL($Sql);
    if($r=safe_fetch($q)) {
        $lvl = $r->AclDtLevel;
    }
    if($lvl++ == 2 OR (array_key_exists($feature, $limitedACL) AND $lvl > $limitedACL[$feature]) OR (isStarIP($ip) AND $lvl!=AclReadOnly)) {
        $Sql = "DELETE FROM AclDetails WHERE AclDtTournament=".StrSafe_DB($_SESSION['TourId'])." AND AclDtIP='{$ip}' && AclDtFeature={$feature}";
    } else {
        if(array_key_exists($feature, $limitedACL)) {
            $lvl = $limitedACL[$feature];
        }
        $Sql = "INSERT INTO AclDetails (AclDtTournament, AclDtIP, AclDtFeature, AclDtLevel) 
          VALUES (".StrSafe_DB($_SESSION['TourId']).", '{$ip}', {$feature}, {$lvl}) 
          ON DUPLICATE KEY UPDATE AclDtLevel={$lvl}";
    }
    $q = safe_w_SQL($Sql);
}


if(isset($_REQUEST["AclOnOff"]) AND preg_match("/^[0|1]$/",$_REQUEST["AclOnOff"]) AND isset($_REQUEST["AclRecord"]) AND preg_match("/^[0|1]$/",$_REQUEST["AclRecord"])) {
    if($_REQUEST["AclOnOff"]=="0") {
        $_REQUEST["AclRecord"]="0";
    }
    setModuleParameter("ACL","AclEnable",$_REQUEST["AclOnOff"] . $_REQUEST["AclRecord"]);
    $lockEnabled = getModuleParameter("ACL","AclEnable","00",0,true);
    $Json['AclEnable'] = substr($lockEnabled,0,1);
    $Json['AclRecord'] = substr($lockEnabled,1,1);
} else {
    $Sql = "SELECT AclIP, AclNick, GROUP_CONCAT(CONCAT_WS('|',AclDtFeature,AclDtLevel) separator '#') as Features
      FROM ACL
      LEFT JOIN  AclDetails ON AclTournament=AclDtTournament AND AclIP=AclDtIP
      WHERE AclTournament=" . StrSafe_DB($_SESSION['TourId']) . "
      GROUP BY AclTournament, AclIP
      ORDER BY AclIP";
    $q = safe_r_SQL($Sql);
    $tmpIP = array();
    while ($r = safe_fetch($q)) {
        $tmpFeatures = array();
        if ($r->Features) {
            foreach (explode('#', $r->Features) as $ft) {
                $tmp = explode("|", $ft);
                $tmpFeatures[$tmp[0]] = intval($tmp[1]);
            }
        }
        $tmpIP[$r->AclIP] = array("Ip" => $r->AclIP, "Name" => $r->AclNick, "Opt" => $tmpFeatures);
    }
    $tmpSort=array_keys($tmpIP);
    natsort($tmpSort);
    foreach ($tmpSort as $vSort) {
        $Json[] = $tmpIP[$vSort];
    }

}

JsonOut($Json, 'callback');

function checkValidIP($ip) {
    /*
     /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/
     */
    if(preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/",$ip)) {
        return filter_var($ip, FILTER_VALIDATE_IP);
    } else if(preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\*$/",$ip)) {
        return($ip);
    } else {
        return false;
    }
}

function isStarIP($ip) {
    return preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\*$/",$ip);
}