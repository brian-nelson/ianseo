<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Tournament/Fun_Tournament.local.inc.php');

$JSON=array('error' => 1);

if(checkACL(AclCompetition, AclReadWrite, false)!=AclReadWrite
		or !CheckTourSession()
		or empty($_REQUEST['New_DivId'])
		or empty($_REQUEST['New_DivDescription'])
		or empty($_REQUEST['New_DivViewOrder'])
		or IsBlocked(BIT_BLOCK_TOURDATA)
		or defined('dontEditClassDiv')
		) {
	JsonOut($JSON);
}

$IsAthlete=empty($_REQUEST['New_DivAthlete']) ? 0 : 1;
$IsPara=empty($_REQUEST['New_DivIsPara']) ? 0 : 1;

// Aggiungo la nuova riga
$Insert
	= "INSERT INTO Divisions (DivId,DivTournament,DivDescription,DivIsPara,DivAthlete,DivViewOrder) "
	. "VALUES("
	. StrSafe_DB($_REQUEST['New_DivId']) . ","
	. StrSafe_DB($_SESSION['TourId']) . ","
	. StrSafe_DB($_REQUEST['New_DivDescription']) . ","
	. $IsPara . ","
	. $IsAthlete . ","
	. intval($_REQUEST['New_DivViewOrder']) . " "
	. ") ";

safe_w_sql($Insert);

$JSON['error']=0;
$JSON['divid']= $_REQUEST['New_DivId'];
$JSON['divdescr']=$_REQUEST['New_DivDescription'];
$JSON['divpara']=$IsPara;
$JSON['divathlete']=$IsAthlete;
$JSON['divprogr']=intval($_REQUEST['New_DivViewOrder']);
$JSON['yes']=get_text('Yes');
$JSON['no']=get_text('No');
$JSON['confirm_msg']=get_text('MsgAreYouSure');

JsonOut($JSON);
