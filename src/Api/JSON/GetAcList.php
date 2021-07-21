<?php
/**
 * Called through GetAcPerson.php?id=XXX[&pic=1]
 *
 * JSON returned:
 * - error
 *      1 if not configured,
 *      0 otherwise
 * - status
 *      -1: not present in competitions
 *      0: present but not accredited
 *      1: Accredited
 *      2: Accredited but wrong session
 */

require_once(dirname(dirname(__FILE__)).'/config.php');
require_once('Common/Lib/Fun_Modules.php');
require_once('Accreditation/Lib.php');
/*
API 1 : GetAcPerson.php
nome
cognome
cocode
coname
foto base64
flag base64
encode (pulito)
zone array con zone attive (stringa)
status 1=accreditato, 0=presente NON accreditato, -1 assente, 2=wrong session

id=XXX&pic=1
qr=xxx&pic=1
*/

$JSON=array(
	'error' => 1,
	'regexp' => array(),
	'entries' => array(),
	'flags' => array(),
);
$tmpList=array();

$Options=GetParameter('AccessApp', false, array(), true);
if(empty($Options)) {
	JsonOut($JSON);
}

$JSON['error']=0;

// get all the accreditation QRcodes for this competition...
$q=safe_r_sql("select IceContent, ToCode from IdCardElements inner join Tournament on ToId=IceTournament where IceType IN ('AthQrCode','AthBarCode') and IceTournament in (".implode(',', array_keys($Options)).")");
while ($r = safe_fetch($q)) {
    $replacements = array(
        '\\{ENCODE\\}' => '(.+?)',
        '\\{COUNTRY\\}' => '(.+?)',
        '\\{DIVISION\\}' => '(.+?)',
        '\\{CLASS\\}' => '(.+?)',
        '\\{TOURNAMENT\\}' => '(.+?)',
    );
    $RegExp = preg_quote('{ENCODE}-{DIVISION}-{CLASS}', '/');
    if ($r->IceContent != '') {
        $RegExp = preg_quote($r->IceContent, '/');
    }
    $RegExp = '^' . str_replace(array_keys($replacements), array_values($replacements), $RegExp) . '$';
    $RegArray = getIceRegExpMatches($r->IceContent);
    $RegArray['formula'] = $RegExp;
    $RegArray['competition'] = $r->ToCode;
    if (!in_array($RegArray, $JSON['regexp'])) {
        $JSON['regexp'][$r->ToCode] = $RegArray;
    }
}

// get all entries allowed by the setup
$qEntry = "select EnId, EnCode, QuSession, '' as ScheduledSession, ToId, ToCode, EnName, EnFirstName, EnCountry, CoCode, CoName, AeId is not null as Accredited, 
		AcArea0, AcArea1, AcArea2, AcArea3, AcArea4, AcArea5, AcArea6, AcArea7, AcAreaStar, EdExtra as EnCaption, 
		(ClAthlete*DivAthlete) AS  AcIsAthlete, DivDescription, ClDescription, DivId, ClId
		from Entries
		inner join Qualifications on QuId=EnId
		inner join Tournament on ToId=EnTournament
		inner join Countries on CoTournament=EnTournament and CoId=EnCountry
		left join Divisions on DivTournament=EnTournament and DivId=EnDivision
		left join Classes on ClTournament=EnTournament and ClId=EnClass
		left join Eliminations on ElId=EnId
		LEFT JOIN AccEntries ON AeId=EnId AND AEOperation=1
		LEFT JOIN AccColors ON AcTournament=EnTournament AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE AcDivClass
		LEFT JOIN ExtraData ON EdId=EnId and EdType='C'
		WHERE EnTournament in (".implode(',', array_keys($Options)).")";
$q=safe_r_sql($qEntry);
while($r=safe_fetch($q)) {

	$Template=array(
		'key' => '',
		'enCode' => '',
		'givName' => '',
		'famName' => '',
		'coCode' => '',
		'coName' => '',
		'caption' => '',
		'status' => '',
		'zones' => array(),
		'photo' => '',
        'hash' => ''
	);

    $zones=array();
    if($r->AcArea0) $zones[]='0'.($r->AcAreaStar ? '*' : '');
    if($r->AcArea1) $zones[]='1'.($r->AcAreaStar ? '*' : '');
    if($r->AcArea2) $zones[]='2';
    if($r->AcArea3) $zones[]='3';
    if($r->AcArea4) $zones[]='4';
    if($r->AcArea5) $zones[]='5';
    if($r->AcArea6) $zones[]='6';
    if($r->AcArea7) $zones[]='7';
    $status=0; // present, not accredited
    if($r->Accredited) {
        $status = 1;
        if (!empty($Options[$r->ToId])) {
            $status = 2;
        }
    }

    $Caption=$r->EnCaption;
    if(empty($Caption)) {
        if($r->AcIsAthlete) {
            $Caption=$r->DivDescription . ' ' . $r->ClDescription;
        } else {
            $Caption=$r->ClDescription;
        }
    }
    $Template['key'] = $r->ToCode . '|' . $r->EnCode  . '|' . $r->CoCode . '|' . $r->DivId;
    if(array_key_exists($r->ToCode,$JSON['regexp'])) {
        $Template['key'] = $r->ToCode . '|' . $r->EnCode . ($JSON['regexp'][$r->ToCode]["country"] != -1 ? '|' . $r->CoCode : '') . ($JSON['regexp'][$r->ToCode]["division"] != -1 ? '|' . $r->DivId : '');
    }
    $Template['enCode']=$r->EnCode;
    $Template['famName']=$r->EnFirstName;
    $Template['givName']=$r->EnName;
    $Template['coCode']=$r->CoCode;
    $Template['coName']=$r->CoName;
    $Template['caption']=$Caption;
    $Template['status']=$status;
    $Template['zones']=$zones;

    if(!empty($_REQUEST['pic'])) {
        if(file_exists($im=$CFG->DOCUMENT_PATH.'TV/Photos/'.$r->ToCode.'-En-'.$r->EnId.'.jpg')) {
            $Template['photo']='data:image/jpeg;base64,'.base64_encode(file_get_contents($im));
        }
        if(empty($JSON['flags'][$r->CoCode]) and file_exists($im=$CFG->DOCUMENT_PATH.'TV/Photos/'.$r->ToCode.'-Fl-'.$r->CoCode.'.jpg')) {
            $JSON['flags'][$r->CoCode] = 'data:image/jpeg;base64,'.base64_encode(file_get_contents($im));
        }
    }

    $Template['hash'] = md5($Template['enCode'].$Template['famName'].$Template['givName'].$Template['coCode'].$Template['coName'].$Template['caption'].implode(',',$Template['zones']).strlen($Template['photo']));
    $tmpList[$Template['key']]=$Template;
}
//Search for specific option competitions
foreach ($Options as $ToId=>$Sessions) {
    if(!empty($Sessions)) {
        $ToCode=getCodeFromId($ToId);
        $Template['key'] = "ToCode,'|',EnCode,'|',CoCode,'|',EnDivision";
        if(array_key_exists($ToCode,$JSON['regexp'])) {
            $tmpKeyStr = "ToCode,'|',EnCode" . ($JSON['regexp'][$ToCode]["country"] != -1 ? ",'|',CoCode" : "") . ($JSON['regexp'][$ToCode]["division"] != -1 ? ",'|',EnDivision" : "");
        }
        $SQL = "
            (
                SELECT DISTINCT CONCAT({$tmpKeyStr}) as keyValue, EnCountry as cntOff
                FROM Entries 
                inner join Countries on CoTournament=EnTournament and CoId=EnCountry
                inner join Qualifications on QuId=EnId 
                INNER JOIN Tournament ON ToId=EnTournament
                WHERE EnTournament=$ToId AND EnAthlete=1 AND CONCAT('Q',ToNumDist,QuSession) IN ('" . implode("','", $Sessions) . "')
            ) UNION ALL (
                SELECT DISTINCT CONCAT({$tmpKeyStr}) as keyValue, EnCountry as cntOff
                FROM Eliminations 
                INNER JOIN Entries ON ElId=EnId
                inner join Countries on CoTournament=EnTournament and CoId=EnCountry
                INNER JOIN Tournament ON ToId=EnTournament
                INNER JOIN Events ON EvTournament=ElTournament and EvCode=ElEventCode and EvTeamEvent=0 and EvElim1>0
            WHERE ElTournament=$ToId AND EnAthlete=1 AND CONCAT('E1',ElSession) IN ('" . implode("','", $Sessions) . "') and ElElimPhase=0
            ) UNION ALL (
                SELECT DISTINCT CONCAT({$tmpKeyStr}) as keyValue, EnCountry as cntOff
                FROM Eliminations 
                INNER JOIN Entries ON ElId=EnId
                inner join Countries on CoTournament=EnTournament and CoId=EnCountry
                INNER JOIN Tournament ON ToId=EnTournament
                INNER JOIN Events ON EvTournament=ElTournament and EvCode=ElEventCode and EvTeamEvent=0 and EvElim1>0
                WHERE ElTournament=$ToId AND EnAthlete=1 AND CONCAT('E2',ElSession) IN ('" . implode("','", $Sessions) . "') and ElElimPhase=1
            ) UNION ALL (
                SELECT DISTINCT CONCAT({$tmpKeyStr}) AS keyValue, EnCountry as cntOff
                FROM Finals
                INNER JOIN Entries ON FinAthlete=EnId
                inner join Countries on CoTournament=EnTournament and CoId=EnCountry
                INNER JOIN Tournament ON ToId=EnTournament
                inner join FinSchedule on FSEvent=FinEvent and FSTeamEvent=0 and FsTournament=FinTournament and FsMatchNo=FinMatchNo
                WHERE FinTournament=$ToId AND EnAthlete=1 AND CONCAT('I', FSScheduledDate, FSScheduledTime) IN ('" . implode("','", $Sessions) . "')
            ) UNION ALL (
                SELECT DISTINCT CONCAT({$tmpKeyStr}) AS keyValue, EnCountry as cntOff
                FROM TeamFinComponent
                INNER JOIN Entries ON TfcId=EnId
                inner join Countries on CoTournament=EnTournament and CoId=EnCountry
                INNER JOIN Tournament ON ToId=EnTournament
                inner join TeamFinals on TfTeam=TfcCoId and TfSubTeam=TfcSubTeam and TfTournament=TfcTournament and TfEvent=TfcEvent
                inner join FinSchedule on FSEvent=TfEvent and FSTeamEvent=1 and FsTournament=TfTournament and FsMatchNo=TfMatchNo
                WHERE TfTournament=$ToId AND EnAthlete=1 AND CONCAT('T', FSScheduledDate, FSScheduledTime) IN ('" . implode("','", $Sessions) . "')
            )";
        $cntList = Array();
        $q = safe_r_sql($SQL);
        while ($r = safe_fetch($q)) {
            if(array_key_exists($r->keyValue, $tmpList)) {
                $tmpList[$r->keyValue]['status'] = 1;
            }
            if (!in_array($r->cntOff, $cntList)) {
                $cntList[] = $r->cntOff;
            }
        }
        $SQL = "SELECT DISTINCT CONCAT({$tmpKeyStr}) as keyValue
            FROM Entries 
            inner join Qualifications on QuId=EnId 
            inner join Countries on CoTournament=EnTournament and CoId=EnCountry
            INNER JOIN Tournament ON ToId=EnTournament
            WHERE EnTournament=$ToId AND EnAthlete=0 AND QuSession=0 ";
        if(count($cntList)) {
            $SQL .= "AND EnCountry IN (" . implode(",", $cntList) . ")";
        }
        $q = safe_r_sql($SQL);
        while ($r = safe_fetch($q)) {
            if(array_key_exists($r->keyValue, $tmpList)) {
                $tmpList[$r->keyValue]['status'] = 1;
            }
        }
    }
}

$JSON['regexp'] = array_values($JSON['regexp']);
$JSON['entries'] = array_values($tmpList);


JsonOut($JSON);
