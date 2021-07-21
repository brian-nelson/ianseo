<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
CheckTourSession(true);

checkACL(AclTeams, AclReadWrite, false);

$JSON=array('error'=>1);

$EvCode = (!empty($_REQUEST["EvCode"]) ? filter_var($_REQUEST["EvCode"], FILTER_SANITIZE_STRING) : '' );
$CoId = (!empty($_REQUEST["CoId"]) ? intval($_REQUEST["CoId"]) : 0 );
$TeamId = (!empty($_REQUEST["TeamId"]) ? intval($_REQUEST["TeamId"]) : 0 );
$TeamSubId = (!empty($_REQUEST["TeamSubId"]) ? intval($_REQUEST["TeamSubId"]) : 0 );

if(!empty($EvCode) AND !empty($TeamId)) {
    if(!empty($_REQUEST["data"]) AND is_array($_REQUEST["data"])) {
        $newIds = array();
        foreach ($_REQUEST["data"] as $v) {
            $newIds[$v["Id"]] = $v["Grp"];
        }
        $toRemove = array();
        $toAdd = array();
        $grpPositions = array();
        $Sql = "SELECT EnId as `Id`, EcTeamEvent as `Group`, IFNULL(TfcId,0) as `Existing`, IFNULL(TfcOrder,0) as `Order` " .
            "FROM Entries " .
            "INNER JOIN EventClass ON EcCode='{$EvCode}' AND EcTeamEvent!=0 AND EcTournament={$_SESSION['TourId']} AND EcClass=EnClass AND EcDivision=EnDivision " .
            "INNER JOIN Events ON  EvCode=EcCode and EvTeamEvent=1 and EvTournament=EnTournament " .
            "LEFT JOIN TeamFinComponent ON TfcCoId={$TeamId} AND TfcSubTeam={$TeamSubId} AND TfcTournament=EvTournament AND TfcEvent=EvCode AND TfcId=EnId " .
            "LEFT JOIN TeamComponent ON TcCoId={$TeamId}  AND TcSubTeam={$TeamSubId} AND TcTournament=EvTournament AND TcEvent=EvCode AND TcFinEvent=1 AND TcId=EnId " .
            "WHERE EnTournament={$_SESSION['TourId']} AND EnAthlete=1 AND EnStatus<=1 " .
            "AND IF(EvTeamCreationMode=3, EnCountry3, if(EvTeamCreationMode=2, EnCountry2, if((EvTeamCreationMode=0 and EnCountry2=0) or EvTeamCreationMode=1, EnCountry, EnCountry2)))={$TeamId} " .
            "AND IF(EvMixedTeam=0, EnTeamFEvent, EnTeamMixEvent) = 1 ".
            "AND EnId NOT IN (SELECT TfcId FROM TeamFinComponent WHERE TfcCoId={$TeamId} AND TfcSubTeam!={$TeamSubId} AND TfcTournament={$_SESSION['TourId']} AND TfcEvent='{$EvCode}') ".
            "ORDER BY TfcId IS NOT NULL DESC, EnId";
        $q=safe_r_SQL($Sql);
        while($r = safe_fetch($q)) {
            if(array_key_exists($r->Id,$newIds) AND $r->Existing == 0) {
                if($newIds[$r->Id]==$r->Group) {
                    $toAdd[$r->Id] = $r;
                }
            } else if(!array_key_exists($r->Id,$newIds) AND $r->Existing != 0) {
                $toRemove[] = $r->Id;
                if(!array_key_exists($r->Group, $grpPositions)) {
                    $grpPositions[$r->Group]=array();
                }
                $grpPositions[$r->Group][] = $r->Order;
            }
        }
        if(count($toAdd)==count($toRemove)) {
            $now = date('Y-m-d H:i:s');
            foreach ($toAdd as $k=>$v) {
                $v->Order = array_shift($grpPositions[$v->Group]);
                $Sql = "INSERT INTO `TeamFinComponent` (`TfcCoId`, `TfcSubTeam`, `TfcTournament`, `TfcEvent`, `TfcId`, `TfcOrder`, `TfcTimeStamp`) VALUES".
                    "({$TeamId}, {$TeamSubId}, {$_SESSION['TourId']}, '{$EvCode}', {$v->Id}, {$v->Order}, '$now')";
                safe_w_SQL($Sql);
            }
            $Sql = "DELETE FROM TeamFinComponent WHERE TfcCoId={$TeamId} AND TfcSubTeam={$TeamSubId} AND TfcTournament={$_SESSION['TourId']} AND TfcEvent='{$EvCode}' AND TfcId IN (".implode(',',$toRemove).")";
            safe_w_SQL($Sql);
            $JSON['error'] = 0;
        }
    } else {
        $Sql = "SELECT EnId, EnDivision, EnClass, EnCode, EnSubClass, CONCAT(UPPER(EnFirstName), ' ', EnName) as Ath, EcTeamEvent, EcNumber, EcSubClass, TfcId, TcId " .
            "FROM Entries " .
            "INNER JOIN EventClass ON EcCode='{$EvCode}' AND EcTeamEvent!=0 AND EcTournament={$_SESSION['TourId']} AND EcClass=EnClass AND EcDivision=EnDivision " .
            "INNER JOIN Events ON  EvCode=EcCode and EvTeamEvent=1 and EvTournament=EnTournament " .
            "LEFT JOIN TeamFinComponent ON TfcCoId={$TeamId} AND TfcSubTeam={$TeamSubId} AND TfcTournament=EvTournament AND TfcEvent=EvCode AND TfcId=EnId " .
            "LEFT JOIN TeamComponent ON TcCoId={$TeamId}  AND TcSubTeam={$TeamSubId} AND TcTournament=EvTournament AND TcEvent=EvCode AND TcFinEvent=1 AND TcId=EnId " .
            "WHERE EnTournament={$_SESSION['TourId']} AND EnAthlete=1 AND EnStatus<=1 " .
            "AND IF(EvTeamCreationMode=3, EnCountry3, if(EvTeamCreationMode=2, EnCountry2, if((EvTeamCreationMode=0 and EnCountry2=0) or EvTeamCreationMode=1, EnCountry, EnCountry2)))={$TeamId} " .
            "AND IF(EvMixedTeam=0, EnTeamFEvent, EnTeamMixEvent) = 1 ".
            "AND EnId NOT IN (SELECT TfcId FROM TeamFinComponent WHERE TfcCoId={$TeamId} AND TfcSubTeam!={$TeamSubId} AND TfcTournament={$_SESSION['TourId']} AND TfcEvent='{$EvCode}') ".
            "ORDER BY EcTeamEvent, TfcId IS NOT NULL DESC, TcId IS NOT NULL DESC, Ath";

        $JSON['data'] = array();
        $q = safe_r_SQL($Sql);
        while ($r = safe_fetch($q)) {
            if (!array_key_exists($r->EcTeamEvent, $JSON['data'])) {
                $JSON['data'][$r->EcTeamEvent] = array('Group' => intval($r->EcTeamEvent), 'Qty' => intval($r->EcNumber), 'Athletes' => array());
            }
            if(empty($r->EcSubClass) OR $r->EcSubClass==$r->EnSubClass) {
                $JSON['data'][$r->EcTeamEvent]['Athletes'][] = array('Id' => intval($r->EnId), 'Bib' => $r->EnCode, 'Athlete' => $r->Ath, 'Div' => $r->EnDivision, 'Cl' => $r->EnClass, 'isF' => !is_null($r->TfcId), 'isQ' => !is_null($r->TcId));
            }
            $JSON['error'] = 0;
        }
    }
    JsonOut($JSON);
    die();
}

$Sql = "SELECT DISTINCT EvCode, EvEventName ".
    "FROM Events ".
    "WHERE EvTournament={$_SESSION['TourId']} AND EvTeamEvent=1 AND EvFinalFirstPhase!=0 AND EvShootOff!=0 ";
if(!empty($CoId)) {
    $Sql .= "AND EvCode IN (SELECT TeEvent FROM Teams WHERE TeTournament={$_SESSION['TourId']} AND TeCoId={$CoId} AND TeFinEvent=1 AND TeSO!=0) ";
}
$Sql .= "ORDER BY EvProgr";

$JSON['eventList'] = array();
$q = safe_r_SQL($Sql);
while($r = safe_fetch($q)) {
    $JSON['eventList'][] = array('EvCode'=>$r->EvCode, 'EvName'=>$r->EvEventName);
    $JSON['error'] = 0;
}

$Sql = "SELECT DISTINCT TeCoId, CoCode, IF(CoNameComplete='',CoName,CoNameComplete) as Name ".
    "FROM Events ".
    "INNER JOIN Teams ON EvTournament=TeTournament AND TeEvent=EvCode AND TeFinEvent=1 AND TeSO!=0 " .
    "INNER JOIN Countries on CoId=TeCoId AND CoTournament=TeTournament " .
    "WHERE EvTournament={$_SESSION['TourId']} AND EvTeamEvent=1 AND EvFinalFirstPhase!=0 AND EvShootOff!=0 ";
if(!empty($EvCode)) {
    $Sql .= "AND TeEvent='{$EvCode}' ";
}
$Sql .= "ORDER BY CoCode";

$JSON['teamList']=array();
$q = safe_r_SQL($Sql);
while($r = safe_fetch($q)) {
    $JSON['teamList'][] = array('Id'=>intval($r->TeCoId), 'Code'=>$r->CoCode,  'Name'=>$r->Name);
}

$JSON['teamComposition']=array();
if(!empty($CoId) OR !empty($EvCode)) {
    $Sql = "SELECT EvCode, EvEventName, TeCoId, TeSubTeam, CoCode, EnCode, IF(CoNameComplete='',CoName,CoNameComplete) as Name, TfcId, TcId, CONCAT(UPPER(EnFirstName), ' ', EnName) as Ath, EnDivision, EnClass, TfcTimeStamp " .
        "FROM Events " .
        "INNER JOIN Teams ON EvTournament=TeTournament AND TeEvent=EvCode AND TeFinEvent=1 AND TeSO!=0 " .
        "INNER JOIN Countries on CoId=TeCoId AND CoTournament=TeTournament " .
        "INNER JOIN TeamFinComponent ON TfcCoId=TeCoId AND TfcSubTeam=TeSubTeam AND TfcTournament=TeTournament AND TfcEvent=TeEvent " .
        "INNER JOIN Entries ON TfcId=EnId AND TfcTournament=EnTournament " .
        "LEFT JOIN TeamComponent ON TcCoId=TeCoId AND TcSubTeam=TeSubTeam AND TcTournament=TeTournament AND TcEvent=TeEvent AND TcFinEvent=TeFinEvent AND TcId=TfcId " .
        "WHERE EvTournament={$_SESSION['TourId']} AND EvTeamEvent=1 AND EvFinalFirstPhase!=0 AND EvShootOff!=0 ";
    if(!empty($CoId)) {
        $Sql .= "AND TeCoId='{$CoId}' ";
    }
    if(!empty($EvCode)) {
        $Sql .= "AND TeEvent='{$EvCode}' ";
    }
    $Sql .= "ORDER BY EvProgr, CoCode, TfcOrder";

    $tmpTeam = array();
    $q = safe_r_SQL($Sql);
    while($r = safe_fetch($q)) {
        if(!array_key_exists($r->EvCode.'|'.$r->TeCoId.'|'.$r->TeSubTeam,$tmpTeam)) {
            $tmpTeam[$r->EvCode.'|'.$r->TeCoId.'|'.$r->TeSubTeam] = array('EvCode'=>$r->EvCode, 'EvName'=>$r->EvEventName, 'Id' => intval($r->TeCoId), 'SubId'=>intval($r->TeSubTeam), 'Code' => $r->CoCode, 'Name' => $r->Name . ($r->TeSubTeam<=1 ? '' : ' ('.$r->TeSubTeam.')'), 'Components'=>array());
        }
        $tmpTeam[$r->EvCode.'|'.$r->TeCoId.'|'.$r->TeSubTeam]['Components'][] = array('Id'=>intval($r->TfcId), 'Bib'=>$r->EnCode, 'Athlete'=>$r->Ath, 'Div'=>$r->EnDivision, 'Cl'=>$r->EnClass, 'Ts'=>$r->TfcTimeStamp, 'isQ'=>!is_null($r->TcId));
    }
    $JSON['teamComposition'] = array_values($tmpTeam);
}

JsonOut($JSON);