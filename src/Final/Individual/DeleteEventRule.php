<?php
/*
													- DeleteEventRule.php -
	Elimina una coppia DivClass da EventClass
*/

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

$JSON=array('error' => 1, 'msg' => 'Error');

if(checkACL(AclCompetition, AclNoAccess) != AclReadWrite
		or !CheckTourSession()
		or empty($_REQUEST['EvCode'])
		or empty($_REQUEST['DelDiv'])
		or empty($_REQUEST['DelCl'])
		) {
	JsonOut($JSON);
}

if(IsBlocked(BIT_BLOCK_TOURDATA)) {
	$JSN['msg']=get_text('LockedProcedure', 'Errors');
	JsonOut($JSON);
}

require_once('Common/Fun_Sessions.inc.php');
require_once('Qualification/Fun_Qualification.local.inc.php');

$Delete = "DELETE FROM EventClass 
	WHERE EcCode=" . StrSafe_DB($_REQUEST['EvCode']) . " 
		AND EcTeamEvent='0' 
		AND EcTournament=" . StrSafe_DB($_SESSION['TourId']) . " 
		AND EcClass=" . StrSafe_DB($_REQUEST['DelCl']) . " 
		AND EcDivision=" . StrSafe_DB($_REQUEST['DelDiv']) . " 
		AND EcSubClass=" . StrSafe_DB($_REQUEST['DelSubCl']) ;
$Rs=safe_w_sql($Delete);

if(safe_w_affected_rows()) {
	safe_w_sql("UPDATE Events SET EvTourRules='' where EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EvTeamEvent='0' AND EvTournament = " . StrSafe_DB($_SESSION['TourId']));

	// SO Reset
	ResetShootoff($_REQUEST['EvCode'],0,0);
	MakeIndAbs();
}

$JSON['error']=0;

JsonOut($JSON);

