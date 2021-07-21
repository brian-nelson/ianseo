<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Tournament/Fun_Tournament.local.inc.php');

checkACL(AclCompetition, AclReadWrite, false);

$JSON=array('error'=>1, 'clid'=>'', 'fromto' => '');

if (!CheckTourSession()
		or !isset($_REQUEST['ClId'])
		or !isset($_REQUEST['Age'])
		or !isset($_REQUEST['FromTo'])
		or ($_REQUEST['FromTo']!='From' and $_REQUEST['FromTo']!='To')
		or IsBlocked(BIT_BLOCK_TOURDATA)
		or defined('dontEditClassDiv')
		) {
	JsonOut($JSON);
}

$Age = intval($_REQUEST['Age']);
$ClId = $_REQUEST['ClId'];

$JSON['clid']=$ClId;
$JSON['fromto']=$_REQUEST['FromTo'];

$ClDivAllowed=(empty($_REQUEST['AlDivs']) ? '' : $_REQUEST['AlDivs']);
if (!CheckClassAge($ClId, $Age, $_REQUEST['FromTo'], $ClDivAllowed)) {
	JsonOut($JSON);
}

$Update = "UPDATE Classes SET "
	. "ClAge" . $_REQUEST['FromTo'] . "=" . StrSafe_DB($Age) . " "
	. ", ClDivisionsAllowed=" . StrSafe_DB($ClDivAllowed) . " "
	. "WHERE ClId=" . StrSafe_DB($ClId) . " AND ClTournament=" . StrSafe_DB($_SESSION['TourId']) . "";
safe_w_sql($Update);

$err=safe_w_error();
if($err->errno!=0) {
	JsonOut($JSON);
} else if(safe_w_affected_rows()) {
	safe_w_sql("UPDATE Classes SET ClTourRules='' WHERE ClId=" . StrSafe_DB($ClId) . " AND ClTournament=" . StrSafe_DB($_SESSION['TourId']));
}

$JSON['error']=0;
JsonOut($JSON);
