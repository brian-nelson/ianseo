<?php
/*
													- UpdateFieldEventList.php -
	Aggiorna il campo di Events passato in querystring.
*/

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

$JSON=array('error' => 1, 'msg' => 'Error', 'rules' => array());

if(checkACL(AclCompetition, AclNoAccess) != AclReadWrite
	or !CheckTourSession()
	or empty($_REQUEST['New_EcDivision'])
	or empty($_REQUEST['New_EcClass'])
	or !isset($_REQUEST['New_EcSubClass'])
) {
	JsonOut($JSON);
}

if(IsBlocked(BIT_BLOCK_TOURDATA)) {
	$JSON['msg']=get_text('LockedProcedure', 'Errors');
	JsonOut($JSON);
}

require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Qualification/Fun_Qualification.local.inc.php');

$Tuple = array();
$Rules = array();

foreach ($_REQUEST['New_EcDivision'] as $DivKey => $DivValue) {
	foreach ($_REQUEST['New_EcClass'] as $ClKey => $ClValue) {
        foreach ($_REQUEST['New_EcSubClass'] as $SubClKey => $SubClValue) {
            $Tuple[] = "("
                . StrSafe_DB($_REQUEST['EvCode']) . ", "
                . StrSafe_DB(0) . ", "
                . StrSafe_DB($_SESSION['TourId']) . ", "
                . StrSafe_DB($ClValue) . ", "
                . StrSafe_DB($DivValue) . ", "
                . StrSafe_DB($SubClValue)
                . ")";
            $Rules[] = array($DivValue, $ClValue, $SubClValue);
        }
	}
}

foreach ($Tuple as $Key => $Value) {
	$Insert = "INSERT ignore INTO EventClass (EcCode,EcTeamEvent,EcTournament,EcClass,EcDivision,EcSubClass) VALUES " . $Value;
	$RsIns=safe_w_sql($Insert);

	if (safe_w_affected_rows()) {
		safe_w_sql("UPDATE Events SET EvTourRules='' where EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EvTeamEvent='0' AND EvTournament = " . StrSafe_DB($_SESSION['TourId']));
		$JSON['rules'][] = $Rules[$Key];
	}
}

// reset of the Event's SO
ResetShootoff($_REQUEST['EvCode'],0,0);

// rebuild Individuals
MakeIndAbs();

if($JSON['rules']) {
	$JSON['error']=0;
}

JsonOut($JSON);
