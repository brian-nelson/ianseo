<?php
/*
 * BIT_BLOCK_TOURDATA
 * - elimina torneo
 * - modifica dati gara
 * - modifica impostazioni finali (tranne running)
 * - idem per squadre
 *
 * BIT_BLOCK_PARTICIPANT
 * - elenchi partecipanti
 *
 * BIT_BLOCK_ACCREDITATION
 * - procedure di accreditamento
 *
 *
 * La chiave rappresenta il bit di cui si è chiesto il set.
 * Il vecchio valore nel db viene posto in OR con il valore corrispondente alla chiave
 */
//define ("BIT_BLOCK_TOUR",   0x1);	// Blocco info gara
define ("BIT_BLOCK_PARTICIPANT",   0x1);	// Blocco Elenco Partecipanti e modifiche alle persone
define ("BIT_BLOCK_QUAL",   0x2); // Blocco qualificazioni
define ("BIT_BLOCK_ELIM",   0x4); // Blocco eliminatorie (ind)
define ("BIT_BLOCK_IND",    0x8); // Blocco finali ind
define ("BIT_BLOCK_TEAM",  0x10); // Blocco finali team
define ("BIT_BLOCK_REPORT",0x20); // Blocco verbale arbitri
define ("BIT_BLOCK_TOURDATA",0x40); // Blocco modifiche Torneo
define ("BIT_BLOCK_MEDIA",0x80); // Blocco delle modalità Media (rot, etc)
define ("BIT_BLOCK_ACCREDITATION",0x100); // Blocco accreditamento
define ("BIT_BLOCK_PUBBLICATION",0x200); // Blocco pubblicazioni online
define ("BIT_BLOCK_FLIGHTS",0x400); // Blocco gestione Flights
define ('BIT_BLOCK_ALL', 0xFFFF);

define ("AclNoAccess",0);
define ("AclReadOnly",1);
define ("AclReadWrite",2);

define ('AclRoot', 0);
define ('AclCompetition', 1);
define ('AclParticipants', 2);
define ('AclQualification', 3);
define ('AclEliminations', 4);
define ('AclIndividuals', 5);
define ('AclTeams', 6);
define ('AclAccreditation', 7);
define ('AclModules', 8);
define ('AclInternetPublish', 9);
define ('AclAPI', 10);
define ('AclISKClient', 11);
define ('AclISKServer', 12);
define ('AclSpeaker', 13);
define ('AclOutput', 14);
define ('AclOdf', 15);


$limitedACL = array(
    AclRoot => AclReadWrite,
    AclInternetPublish => AclReadWrite,
    AclAPI => AclReadWrite,
    AclISKClient => AclReadWrite,
    AclSpeaker => AclReadOnly,
    AclOdf => AclReadWrite
);

$listACL = array(
    AclRoot => 'AclRoot',
    AclCompetition => 'AclCompetition',
    AclParticipants => 'AclParticipants',
    AclQualification => 'AclQualification',
    AclEliminations => 'AclEliminations',
    AclIndividuals => 'AclIndividuals',
    AclTeams => 'AclTeams',
    AclAccreditation => 'AclAccreditation',
    AclModules => 'AclModules',
    AclInternetPublish => 'AclInternet',
    AclAPI => 'AclAPI',
    AclISKClient => 'AclISKClient',
    AclISKServer => 'AclISKServer',
    AclSpeaker => 'AclSpeaker',
    AclOutput => 'AclOutput',
    AclOdf => 'AclOdf',
);

/*
 * La chiave rappresenta il bit di cui si è chiesto l'unset
 * Il vecchio valore nel db viene posto in AND con il valore corrispondente alla chiave
 */
function getBlocksToUnset() {
	$ToUnset = array();
	$ToUnset['6'] = (BIT_BLOCK_PARTICIPANT | BIT_BLOCK_ACCREDITATION);
	$ToUnset['0'] = (BIT_BLOCK_TOURDATA | BIT_BLOCK_ACCREDITATION);
	$ToUnset['8'] = (BIT_BLOCK_PARTICIPANT | BIT_BLOCK_TOURDATA);
	$ToUnset['1'] = (BIT_BLOCK_PARTICIPANT | BIT_BLOCK_TOURDATA | BIT_BLOCK_ACCREDITATION);
	$ToUnset['10'] = (BIT_BLOCK_ALL & ~ BIT_BLOCK_FLIGHTS);
	$ToUnset['2'] = ($ToUnset['1'] | BIT_BLOCK_QUAL | BIT_BLOCK_TEAM);
	$ToUnset['3'] = ($ToUnset['2'] | BIT_BLOCK_ELIM);
	$ToUnset['4'] = ($ToUnset['1'] | BIT_BLOCK_QUAL | BIT_BLOCK_ELIM | BIT_BLOCK_IND);
	$ToUnset['7'] = (BIT_BLOCK_ALL & ~ (BIT_BLOCK_MEDIA | BIT_BLOCK_REPORT) );
	$ToUnset['9'] = (BIT_BLOCK_ALL & ~ BIT_BLOCK_PUBBLICATION );
	$ToUnset['5'] = (BIT_BLOCK_ALL & ~ BIT_BLOCK_REPORT);

	return $ToUnset;

}

function getBlocksToSet() {
	$ToSet = array ();
	$ToSet['6'] = BIT_BLOCK_TOURDATA;
	$ToSet['0'] = ($ToSet['6'] | BIT_BLOCK_PARTICIPANT);
	$ToSet['8'] = ($ToSet['0'] | BIT_BLOCK_ACCREDITATION);
	$ToSet['1'] = ($ToSet['8'] | BIT_BLOCK_QUAL);
	$ToSet['10'] = BIT_BLOCK_FLIGHTS;
	$ToSet['2'] = ($ToSet['1'] | BIT_BLOCK_ELIM);
	$ToSet['3'] = ($ToSet['2'] | BIT_BLOCK_IND);
	$ToSet['4'] = ($ToSet['2'] | BIT_BLOCK_TEAM);
	$ToSet['7'] = ($ToSet['2'] | BIT_BLOCK_IND | BIT_BLOCK_TEAM | BIT_BLOCK_MEDIA);
	$ToSet['9'] = ($ToSet['7'] | BIT_BLOCK_PUBBLICATION);
	$ToSet['5'] = (BIT_BLOCK_ALL & ~ BIT_BLOCK_PUBBLICATION);
	return $ToSet;
}

function actualACL() {
    global $listACL;
    $lockEnabled = getModuleParameter("ACL", "AclEnable", "00");
    $ip = $_SERVER["REMOTE_ADDR"];
    if($ip == '127.0.0.1' OR $ip == '::1' OR $lockEnabled[0] == "0") {
        $acl = array_fill(0, count($listACL), AclReadWrite);
    } else {
        $acl = array_fill(0, count($listACL), AclNoAccess);
        $Sql = "SELECT AclDtFeature, AclDtLevel FROM AclDetails WHERE AclDtTournament=" . intval($_SESSION['TourId']) . " AND AclDtIP='{$ip}'";
        $q = safe_r_SQL($Sql);
        while($r=safe_fetch($q)) {
            $acl[$r->AclDtFeature] = $r->AclDtLevel;
        }
    }
    return $acl;
}

function panicACL() {
    $ipC = $_SERVER["REMOTE_ADDR"];
    $ipS = $_SERVER["SERVER_ADDR"];

    if(($ipC == '127.0.0.1' OR $ipC == '::1') AND ($ipS == '127.0.0.1' OR $ipS == '::1') AND isset($_REQUEST['ACLReset']) AND preg_match("/^[0-9a-z.,:;_-]*$/i",$_REQUEST['ACLReset'])) {
        $TourId = getIdFromCode($_REQUEST['ACLReset']);
        if($TourId) {
            setModuleParameter("ACL","AclEnable","00",$TourId);
            die();
        }

    }
}

function hasACL($feature, $level, $TourId=0) {
    global $INFO, $CFG;
    if(!is_array($feature)) {
        $feature = array($feature);
    }
    $INFO->ACLReqfeatures = $feature;
    $INFO->ACLReqlevel = $level;
    $INFO->ACLEnabled = false;

    if ($TourId == 0 AND !empty($_SESSION['TourId'])) {
        $TourId = intval($_SESSION['TourId']);
    }
    $lockEnabled = getModuleParameter("ACL", "AclEnable", "00", $TourId, true);
    if($lockEnabled[0] == "1") {
        $INFO->ACLEnabled = true;
    }
    $ip = $_SERVER["REMOTE_ADDR"];
    if($ip == '127.0.0.1' OR $ip == '::1') {
        return true;
    } else {
        if ($lockEnabled[0] == "1") {
            $Sql = "SELECT AclDtLevel FROM AclDetails WHERE AclDtTournament={$TourId} AND AclDtIP='{$ip}' && AclDtFeature IN (" . implode(',', $feature) . ") ORDER BY AclDtLevel ASC";
            $q = safe_r_SQL($Sql);
            if ($r = safe_fetch($q) and $level <= $r->AclDtLevel) {
                return true;
            } else if($level==AclNoAccess) {
                return false;
            }
        } else {
            return true;
        }
    }
}

function checkACL($feature, $level, $redirect=true, $TourId=0) {
    global $INFO, $CFG;
    if(!is_array($feature)) {
        $feature = array($feature);
    }
    $INFO->ACLReqfeatures = $feature;
    $INFO->ACLReqlevel = $level;
    $INFO->ACLEnabled = false;

    if ($TourId == 0 AND !empty($_SESSION['TourId'])) {
        $TourId = intval($_SESSION['TourId']);
    }
    $lockEnabled = getModuleParameter("ACL", "AclEnable", "00", $TourId, true);
    if($lockEnabled[0] == "1") {
        $INFO->ACLEnabled = true;
    }

    $ip = $_SERVER["REMOTE_ADDR"];
    if($ip == '127.0.0.1' OR $ip == '::1') {
        return AclReadWrite;
    } else {
        if ($lockEnabled[0] == "1") {
            if($lockEnabled[1] == "1") {
                $Sql="SELECT AclIP FROM ACL WHERE AclTournament={$TourId} AND AclIP='{$ip}'";
                $q = safe_r_SQL($Sql);
                if(safe_num_rows($q)==0) {
                    $Sql = "SELECT AclIP FROM ACL WHERE AclTournament={$TourId} AND '{$ip}' LIKE REPLACE(AclIp,'*','%')";
                    $q = safe_r_SQL($Sql);
                    if(safe_num_rows($q)!=0) {
                        $ruleRs=safe_fetch($q);
                        $Sql = "SELECT * FROM `AclDetails` WHERE `AclDtTournament` = {$TourId} and AclDtIP='{$ruleRs->AclIP}'";
                        $q = safe_r_SQL($Sql);
                        while($r = safe_fetch($q)) {
                            safe_w_SQL("INSERT INTO `AclDetails` (`AclDtTournament`, `AclDtIP`, `AclDtFeature`, `AclDtLevel`) VALUES ({$TourId}, '{$ip}', {$r->AclDtFeature}, {$r->AclDtLevel});");
                        }
                    }
                    safe_w_SQL("INSERT INTO ACL (AclTournament, AclIP, AclNick, AclEnabled) VALUES ({$TourId},'{$ip}',NOW(),1)");
                }
            }
            $Sql = "SELECT AclDtLevel FROM AclDetails WHERE AclDtTournament={$TourId} AND AclDtIP='{$ip}' && AclDtFeature IN (" . implode(',', $feature) . ") ORDER BY AclDtLevel ASC";
            $q = safe_r_SQL($Sql);
            if ($r = safe_fetch($q) and $level <= $r->AclDtLevel) {
                return intval($r->AclDtLevel);
            } else if($level==AclNoAccess) {
                return AclNoAccess;
            } else {
                if ($redirect) {
                    CD_redirect($CFG->ROOT_DIR.'noAccess.php');
                } else {
                    http_response_code(404);
                }
                die();
            }
        } else {
            return AclReadWrite;
        }
    }
}

