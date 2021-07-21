<?php
define('debug', false);

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Tournament/Fun_Tournament.local.inc.php');
checkACL(AclCompetition, AclReadWrite, false);

$JSON=array('error'=>1, 'errormsg'=>'');
if (!CheckTourSession() ||
		empty($_REQUEST['New_ClId']) ||
		!isset($_REQUEST['New_ClSex']) ||
		empty($_REQUEST['New_ClDescription']) ||
		!isset($_REQUEST['New_ClAthlete']) ||
		!isset($_REQUEST['New_ClViewOrder']) ||
		!isset($_REQUEST['New_ClAgeFrom']) ||
		!isset($_REQUEST['New_ClAgeTo']) ||
		!isset($_REQUEST['New_ClValidClass']) ||
		!isset($_REQUEST['New_ClValidDivision'])
		or IsBlocked(BIT_BLOCK_TOURDATA)
		or defined('dontEditClassDiv')) {
	JsonOut($JSON);
}

if (!is_numeric($_REQUEST['New_ClAgeFrom']) || !is_numeric($_REQUEST['New_ClAgeTo']) ||
	$_REQUEST['New_ClAgeFrom']<=0 || $_REQUEST['New_ClAgeTo']<=0 || $_REQUEST['New_ClAgeFrom']>$_REQUEST['New_ClAgeTo']) {
	$JSON['errormsg']=get_text('ClassFromToError', 'Errors');
	JsonOut($JSON);
}

$JSON['error']=0;

$ValidClass=CreateValidClass($_REQUEST['New_ClId'],$_REQUEST['New_ClValidClass']);

$Insert
	= "INSERT IGNORE INTO Classes (ClId,ClTournament,ClDescription,ClAthlete,ClIsPara,ClViewOrder,ClAgeFrom,ClAgeTo,ClValidClass,ClDivisionsAllowed,ClSex) "
	. "VALUES("
	. StrSafe_DB($_REQUEST['New_ClId']) . ","
	. intval($_SESSION['TourId']) . ","
	. StrSafe_DB($_REQUEST['New_ClDescription']) . ","
	. intval($_REQUEST['New_ClAthlete']) . ","
	. intval($_REQUEST['New_ClIsPara']) . ","
	. intval($_REQUEST['New_ClViewOrder']) . ", "
	. intval($_REQUEST['New_ClAgeFrom']) . ", "
	. intval($_REQUEST['New_ClAgeTo']) . ", "
	. StrSafe_DB($ValidClass) . ", "
	. StrSafe_DB($_REQUEST['New_ClValidDivision']) . ", "
	. intval($_REQUEST['New_ClSex']) . " "
	. ") ";
safe_w_sql($Insert); //duplicate entries is OK

if (!safe_w_affected_rows()) {
	$JSON['errormsg']=get_text('DuplicateEntry','Tournament');
	$JSON['error']=2;
}

$JSON['clid']= $_REQUEST['New_ClId'];
$JSON['cldescr']= $_REQUEST['New_ClDescription'];
$JSON['clathlete']= $_REQUEST['New_ClAthlete'];
$JSON['clpara']= $_REQUEST['New_ClIsPara'];
$JSON['clprogr']= intval($_REQUEST['New_ClViewOrder']);
$JSON['clagefrom']= $_REQUEST['New_ClAgeFrom'];
$JSON['clageto']= $_REQUEST['New_ClAgeTo'];
$JSON['clvalid']= $ValidClass;
$JSON['clvaliddiv']= $_REQUEST['New_ClValidDivision'];
$JSON['clsex']= $_REQUEST['New_ClSex'];
$JSON['male']= get_text('ShortMale','Tournament');
$JSON['female']= get_text('ShortFemale','Tournament');
$JSON['unisex']= get_text('ShortUnisex','Tournament');
$JSON['yes']= get_text('Yes');
$JSON['no']= get_text('No');

JsonOut($JSON);
