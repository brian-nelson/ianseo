<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Tournament/Fun_Tournament.local.inc.php');
checkACL(AclCompetition, AclReadWrite, false);

$JSON=array('error' => 1);

if (!CheckTourSession() ||
		empty($_REQUEST['New_ScId']) ||
		empty($_REQUEST['New_ScDescription']) ||
		!isset($_REQUEST['New_ScViewOrder'])
		or IsBlocked(BIT_BLOCK_TOURDATA)
		) {
	JsonOut($JSON);
}

$JSON['error']=0;

$Insert
	= "INSERT ignore INTO SubClass (ScId,ScTournament,ScDescription,ScViewOrder) "
	. "VALUES("
	. StrSafe_DB($_REQUEST['New_ScId']) . ","
	. intval($_SESSION['TourId']) . ","
	. StrSafe_DB($_REQUEST['New_ScDescription']) . ","
	. intval($_REQUEST['New_ScViewOrder']) . " "
	. ") ";
safe_w_sql($Insert);

if (!safe_w_affected_rows()) {
	$JSON['errormsg']=get_text('DuplicateEntry','Tournament');
	$JSON['error']=2;
}

$JSON['scid'] =  $_REQUEST['New_ScId'];
$JSON['scdescr'] = $_REQUEST['New_ScDescription'];
$JSON['scprogr'] =  intval($_REQUEST['New_ScViewOrder']) ;

JsonOut($JSON);
