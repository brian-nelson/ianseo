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
	'key' => '',
	'enCode' => '',
	'givName' => '',
	'famName' => '',
	'coCode' => '',
	'coName' => '',
	'caption' => '',
	'status' => -1,
	'zones' => array(),
	'photo' => '',
	'flag' => '',
    'hash' => '',
	'direction' => 1,
);

$Options=GetParameter('AccessApp', false, array(), true);
if(empty($Options)) {
	JsonOut($JSON);
}

$JSON['error']=0;

$q=safe_r_sql("select IceContent, ToCode from IdCardElements inner join Tournament on ToId=IceTournament where IceType IN ('AthQrCode','AthBarCode') and IceTournament in (".implode(',', array_keys($Options)).")");
$RegArray = array();
while ($r = safe_fetch($q)) {
    $RegExp = preg_quote('{ENCODE}-{DIVISION}-{CLASS}', '/');
    if ($r->IceContent != '') {
        $RegExp = preg_quote($r->IceContent, '/');
    }
    $RegArray[$r->ToCode] = getIceRegExpMatches($r->IceContent);
}

$EnId=0;
if(!empty($_REQUEST['id'])) {
	$EnId=CheckAccreditationCode($_REQUEST['id'], $Options, true);
}
$checkOnly = (!empty($_REQUEST['checkOnly']));

if(empty($EnId)) {
	JsonOut($JSON);
}

$SQL = "select EnId, EnCode, QuSession, '' as ScheduledSession, ToId, ToCode, EnName, EnFirstName, EnCountry, CoCode, CoName, AeId is not null as Accredited, 
	AcArea0, AcArea1, AcArea2, AcArea3, AcArea4, AcArea5, AcArea6, AcArea7, AcAreaStar, EdExtra as EnCaption, 
	(ClAthlete*DivAthlete) AS  AcIsAthlete, DivDescription, ClDescription, DivId
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
	Where EnId=$EnId";

$q=safe_r_sql($SQL);
if($r=safe_fetch($q)) {
	$status=0; // present, not accredited
	$ToCode=$r->ToCode;
	$CoCode=$r->CoCode;
	$CoName=$r->CoName;
	if($r->Accredited) {
		$status=1;

		// check if the entry is in a wrong session
		if(!empty($Options[$r->ToId])) {
			// we have sessions so check if session=0 and is not athlete... it is a coach
			$status=CheckStatus($r, $EnId, $Options);

			if($status==2) {
				//// check eventually an upgrade card?
				//$t=safe_r_sql("select EnId, EnCode, QuSession, '' as ScheduledSession, ToId, ToCode, EnName, EnFirstName, EnCountry, CoCode, CoName, AeId is not null as Accredited,
				//	AcArea0, AcArea1, AcArea2, AcArea3, AcArea4, AcArea5, AcArea6, AcArea7, AcAreaStar, CEdata.EdExtra as EnCaption,
				//	(ClAthlete*DivAthlete) AS  AcIsAthlete, DivDescription, ClDescription, DivId
				//	from ExtraData ZEdata
				//	inner join Entries on EnId=EdId
				//	inner join Qualifications on QuId=EnId
				//	inner join Tournament on ToId=EnTournament
				//	inner join Countries on CoTournament=EnTournament and CoId=EnCountry
				//	left join Divisions on DivTournament=EnTournament and DivId=EnDivision
				//	left join Classes on ClTournament=EnTournament and ClId=EnClass
				//	left join Eliminations on ElId=EnId
				//	LEFT JOIN AccEntries ON AeId=EnId AND AEOperation=1
				//	LEFT JOIN AccColors ON AcTournament=EnTournament AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE AcDivClass
				//	LEFT JOIN ExtraData CEdata ON CEdata.EdId=EnId and CEdata.EdType='C'
				//	where ZEdata.EdExtra='$r->EnCode' and ZEdata.EdType='Z' and EnTournament in (".implode(', ', array_keys($Options)).")");
				//if($u=safe_fetch($t)) {
				//	$status=CheckStatus($u, $u->EnId, $Options);
				//	if($status==1) {
				//		if(!$r->AcAreaStar) $r->AcAreaStar = $u->AcAreaStar;
				//		if(!$r->AcArea0) $r->AcArea0 = $u->AcArea0;
				//		if(!$r->AcArea1) $r->AcArea1 = $u->AcArea1;
				//		if(!$r->AcArea2) $r->AcArea2 = $u->AcArea2;
				//		if(!$r->AcArea3) $r->AcArea3 = $u->AcArea3;
				//		if(!$r->AcArea4) $r->AcArea4 = $u->AcArea4;
				//		if(!$r->AcArea5) $r->AcArea5 = $u->AcArea5;
				//		if(!$r->AcArea6) $r->AcArea6 = $u->AcArea6;
				//		if(!$r->AcArea7) $r->AcArea7 = $u->AcArea7;
				//		if($u->EnCaption) {
				//			$r->EnCaption=$u->EnCaption;
				//		} else {
				//			if($u->AcIsAthlete) {
				//				$r->EnCaption=$u->DivDescription . ' ' . $u->ClDescription;
				//			} else {
				//				$r->EnCaption=$u->ClDescription;
				//			}
				//		}
				//	}
				//}
			} else {
				// check if this upgrade is linked to someone else's bib in another competition?
				// if yes completely swap the accreditatoion
				// select the extradata of the other competition
				$t=safe_r_sql("select EdExtra, CoCode
					from ExtraData 
					inner join Entries on EnId=EdId and EnTournament in (".implode(', ', array_keys($Options)).")
					inner join Countries on CoId=EnCountry
					where EdType='Z' and EdId=$EnId");

				if($u=safe_fetch($t) and $u->EdExtra) {
					$bits=explode('-', $u->EdExtra);
					$TmpEnCode = $bits[0];
					if(count($bits)>1) {
						// this is a coach upgrade
						$IsCoach=1;
						$TmpCoCode = $bits[1];
						$t=safe_r_sql("select EnId, EnCode, 0 QuSession, '' as ScheduledSession, ToId, ToCode, EnName, EnFirstName, CoId as EnCountry, CoCode, CoName, AeId is not null as Accredited, 
							AcArea0, AcArea1, AcArea2, AcArea3, AcArea4, AcArea5, AcArea6, AcArea7, AcAreaStar, EdExtra as EnCaption, 
							0 AS  AcIsAthlete, DivDescription, ClDescription, DivId
							from Entries
							inner join Qualifications on QuId=EnId
							inner join Tournament on ToId=EnTournament
							inner join Countries on CoTournament=EnTournament and CoCode='$TmpCoCode'
							left join Divisions on DivTournament=EnTournament and DivId=EnDivision
							left join Classes on ClTournament=EnTournament and ClId=EnClass
							left join Eliminations on ElId=EnId
							LEFT JOIN AccEntries ON AeId=EnId AND AEOperation=1
							LEFT JOIN AccColors ON AcTournament=EnTournament AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE AcDivClass
							LEFT JOIN ExtraData ON EdId=EnId and EdType='C'
							where EnCode='$TmpEnCode' and EnTournament in (".implode(', ', array_keys($Options)).")");
					} else {
						// normal upgrade linked to somebody
						$IsCoach=0;
						$t=safe_r_sql("select EnId, EnCode, QuSession, '' as ScheduledSession, ToId, ToCode, EnName, EnFirstName, CoId as EnCountry, CoCode, CoName, AeId is not null as Accredited, 
							AcArea0, AcArea1, AcArea2, AcArea3, AcArea4, AcArea5, AcArea6, AcArea7, AcAreaStar, EdExtra as EnCaption, 
							0 AS  AcIsAthlete, DivDescription, ClDescription, DivId
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
							where EnCode='$TmpEnCode' and EnTournament in (".implode(', ', array_keys($Options)).")");
					}

					if($u=safe_fetch($t)) {
						if($IsCoach) {
							$status=CheckStatus($u, $EnId, $Options);
						}
						if($status==1) {
							// gets name and picture of the linked entry
							if(!$r->AcAreaStar) $r->AcAreaStar = $u->AcAreaStar;
							if(!$r->AcArea0) $r->AcArea0 = $u->AcArea0;
							if(!$r->AcArea1) $r->AcArea1 = $u->AcArea1;
							if(!$r->AcArea2) $r->AcArea2 = $u->AcArea2;
							if(!$r->AcArea3) $r->AcArea3 = $u->AcArea3;
							if(!$r->AcArea4) $r->AcArea4 = $u->AcArea4;
							if(!$r->AcArea5) $r->AcArea5 = $u->AcArea5;
							if(!$r->AcArea6) $r->AcArea6 = $u->AcArea6;
							if(!$r->AcArea7) $r->AcArea7 = $u->AcArea7;
							//$r->EnCaption=$u->EnCaption;
							$ToCode=$u->ToCode;
							$CoCode=$u->CoCode;
							$CoName=$u->CoName;
							$r->EnId=$u->EnId;
						}
					}
				}
			}
		}
	}

	$zones=array();
	if($r->AcArea0) $zones[]='0'.($r->AcAreaStar ? '*' : '');
	if($r->AcArea1) $zones[]='1'.($r->AcAreaStar ? '*' : '');
	if($r->AcArea2) $zones[]='2';
	if($r->AcArea3) $zones[]='3';
	if($r->AcArea4) $zones[]='4';
	if($r->AcArea5) $zones[]='5';
	if($r->AcArea6) $zones[]='6';
	if($r->AcArea7) $zones[]='7';

	$Caption=$r->EnCaption;
	if(empty($Caption)) {
		if($r->AcIsAthlete) {
			$Caption=$r->DivDescription . ' ' . $r->ClDescription;
		} else {
			$Caption=$r->ClDescription;
		}
	}
    $JSON['key']=$r->ToCode.'|'.$r->EnCode.'|'.$r->CoCode.'|'.$r->DivId;
    if(array_key_exists($r->ToCode,$RegArray)) {
        $JSON['key'] = $r->ToCode . '|' . $r->EnCode . ($RegArray[$r->ToCode]["country"] != -1 ? '|' . $r->CoCode : '') . ($RegArray[$r->ToCode]["division"] != -1 ? '|' . $r->DivId : '');
    }
	$JSON['enCode']=$r->EnCode;
	$JSON['famName']=$r->EnFirstName;
	$JSON['givName']=$r->EnName;
	$JSON['coCode']=$CoCode;
	$JSON['coName']=$CoName;
	$JSON['caption']=$Caption;
	$JSON['status']=$status;
	$JSON['zones']=$zones;

	if(!empty($_REQUEST['pic'])) {
		if(file_exists($im=$CFG->DOCUMENT_PATH.'TV/Photos/'.$ToCode.'-En-'.$r->EnId.'.jpg')) {
			$JSON['photo']='data:image/jpeg;base64,'.base64_encode(file_get_contents($im));
		}
		if(file_exists($im=$CFG->DOCUMENT_PATH.'TV/Photos/'.$ToCode.'-Fl-'.$CoCode.'.jpg')) {
			$JSON['flag']='data:image/jpeg;base64,'.base64_encode(file_get_contents($im));
		}
	}

	$Direction=0;
	if(!empty($_REQUEST['isgate'])) {
		$Direction=GetGateAccess($EnId);
		$Direction=($Direction ? -1*$Direction : ($status==1 ? 1 : 0));
	}
	$JSON['direction']=$Direction;
    $JSON['hash'] = md5($JSON['enCode'].$JSON['famName'].$JSON['givName'].$JSON['coCode'].$JSON['coName'].$JSON['caption'].implode(',',$JSON['zones']).strlen($JSON['photo']));

    if($checkOnly) {
        unset($JSON['famName']);
        unset($JSON['givName']);
        unset($JSON['coCode']);
        unset($JSON['coName']);
        unset($JSON['caption']);
        unset($JSON['zones']);
        unset($JSON['photo']);
        unset($JSON['flag']);
    }
	GateLog($EnId, $status, $r->ToId, $Direction);
}

JsonOut($JSON);

function CheckStatus($r, $EnId, $Options) {
	if($r->QuSession==0 and !$r->AcIsAthlete) {
		// coach, so check if there are  archers from that NOC in one of the sessions selected
		$SQL = "(SELECT DISTINCT CONCAT('Q',ToNumDist,QuSession) as keyValue, '' as Bye
			FROM Entries 
			inner join Qualifications on QuId=EnId 
			INNER JOIN Tournament ON ToId=EnTournament
			WHERE EnCountry=$r->EnCountry
			) UNION ALL (
			SELECT DISTINCT CONCAT('E1',ElSession) as keyValue, '' as Bye
			FROM Eliminations 
			inner join Entries on EnId=ElId
			INNER JOIN Events ON EvTournament=ElTournament and EvCode=ElEventCode and EvTeamEvent=0 and EvElim1>0
			WHERE EnCountry=$r->EnCountry and ElElimPhase=0
			) UNION ALL (
			SELECT DISTINCT CONCAT('E2',ElSession) as keyValue, '' as Bye
			FROM Eliminations 
			inner join Entries on EnId=ElId
			INNER JOIN Events ON EvTournament=ElTournament and EvCode=ElEventCode and EvTeamEvent=0 and EvElim1>0
			WHERE EnCountry=$r->EnCountry and ElElimPhase=1
			) UNION ALL (
			SELECT DISTINCT CONCAT('I', FSScheduledDate, FSScheduledTime) AS keyValue, '' as Bye
			FROM Finals
			inner join Entries on EnId=FinAthlete
			inner join FinSchedule on FSEvent=FinEvent and FSTeamEvent=0 and FsTournament=FinTournament and FsMatchNo=FinMatchNo
			WHERE EnCountry=$r->EnCountry
			) UNION ALL (
			SELECT DISTINCT CONCAT('T', FSScheduledDate, FSScheduledTime) AS keyValue, '' as Bye
			FROM TeamFinComponent
			inner join Entries on EnId=TfcId
			inner join TeamFinals on TfTeam=TfcCoId and TfSubTeam=TfcSubTeam and TfTournament=TfcTournament and TfEvent=TfcEvent
			inner join FinSchedule on FSEvent=TfEvent and FSTeamEvent=1 and FsTournament=TfTournament and FsMatchNo=TfMatchNo
			WHERE EnCountry=$r->EnCountry
			)";
	} else {
		$SQL = "(SELECT DISTINCT CONCAT('Q',ToNumDist,QuSession) as keyValue, '' as Bye
			FROM Entries 
			inner join Qualifications on QuId=EnId 
			INNER JOIN Tournament ON ToId=EnTournament
			WHERE EnId=$EnId
			) UNION ALL (
			SELECT DISTINCT CONCAT('E1',ElSession) as keyValue, '' as Bye
			FROM Eliminations 
			INNER JOIN Events ON EvTournament=ElTournament and EvCode=ElEventCode and EvTeamEvent=0 and EvElim1>0
			WHERE ElId=$EnId and ElElimPhase=0
			) UNION ALL (
			SELECT DISTINCT CONCAT('E2',ElSession) as keyValue, '' as Bye
			FROM Eliminations 
			INNER JOIN Events ON EvTournament=ElTournament and EvCode=ElEventCode and EvTeamEvent=0 and EvElim1>0
			WHERE ElId=$EnId and ElElimPhase=1
			) UNION ALL (
			SELECT DISTINCT CONCAT('I', FSScheduledDate, FSScheduledTime) AS keyValue, FinTie as Bye
			FROM Finals
			inner join FinSchedule on FSEvent=FinEvent and FSTeamEvent=0 and FsTournament=FinTournament and FsMatchNo=FinMatchNo
			WHERE FinAthlete=$EnId
			) UNION ALL (
			SELECT DISTINCT CONCAT('T', FSScheduledDate, FSScheduledTime) AS keyValue, TfTie as Bye
			FROM TeamFinComponent
			inner join TeamFinals on TfTeam=TfcCoId and TfSubTeam=TfcSubTeam and TfTournament=TfcTournament and TfEvent=TfcEvent
			inner join FinSchedule on FSEvent=TfEvent and FSTeamEvent=1 and FsTournament=TfTournament and FsMatchNo=TfMatchNo
			WHERE TfcId=$EnId
			)";
	}

	$t=safe_r_sql($SQL);
	$status='2';
	while($u=safe_fetch($t)) {
		if(in_array($u->keyValue, $Options[$r->ToId])) {
			$status='1';
			break;
		} elseif(in_array(strtolower($u->keyValue), $Options[$r->ToId]) and $u->Bye!=2) {
			$status='1';
			break;
		}
	}

	return $status;
}