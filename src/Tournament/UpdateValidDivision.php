<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Tournament/Fun_Tournament.local.inc.php');
checkACL(AclCompetition, AclReadWrite, false);

$JSON=array('error'=>1, 'clid' => '', 'valid' => '');
if (!CheckTourSession()
		or !isset($_REQUEST['ClId'])
		or !isset($_REQUEST['ClList'])
		or IsBlocked(BIT_BLOCK_TOURDATA)
		or defined('dontEditClassDiv')
		) {
	JsonOut($JSON);
}

$ClId = $_REQUEST['ClId'];
$StrList=CreateValidDivision($_REQUEST['ClList']);

$Update
	= "UPDATE Classes SET "
	. "ClDivisionsAllowed=" . StrSafe_DB($StrList) . " "
	. "WHERE ClId=" . StrSafe_DB($ClId) . " AND ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
$Rs=safe_w_sql($Update);
if(safe_w_affected_rows()) {
	safe_w_sql("UPDATE Classes SET ClTourRules='' WHERE ClId=" . StrSafe_DB($ClId) . " AND ClTournament=" . StrSafe_DB($_SESSION['TourId']));
}

$JSON['error']=0;
$JSON['clid']=$_REQUEST['ClId'];
$JSON['valid']=$StrList;

JsonOut($JSON);
