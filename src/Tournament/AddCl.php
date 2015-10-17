<?php
	define('debug', false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Tournament/Fun_Tournament.local.inc.php');

	if (!CheckTourSession() ||
		!isset($_REQUEST['New_ClId']) ||
		!isset($_REQUEST['New_ClSex']) ||
		!isset($_REQUEST['New_ClDescription']) ||
		!isset($_REQUEST['New_ClAthlete']) ||
		!isset($_REQUEST['New_ClViewOrder']) ||
		!isset($_REQUEST['New_ClAgeFrom']) ||
		!isset($_REQUEST['New_ClAgeTo']) ||
		!isset($_REQUEST['New_ClValidClass']) ||
		!isset($_REQUEST['New_ClValidDivision']))
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=intval(IsBlocked(BIT_BLOCK_TOURDATA) && !defined('dontEditClassDiv'));
	$MsgErrore='';

	if (!$Errore) {
		if (!is_numeric($_REQUEST['New_ClAgeFrom']) || !is_numeric($_REQUEST['New_ClAgeTo']) ||
			$_REQUEST['New_ClAgeFrom']<=0 || $_REQUEST['New_ClAgeTo']<=0 || $_REQUEST['New_ClAgeFrom']>$_REQUEST['New_ClAgeTo']) {
			$Errore=1;
		}
	}

	if (!$Errore) {
		// Aggiungo la nuova riga
		$Insert
			= "INSERT IGNORE INTO Classes (ClId,ClTournament,ClDescription,ClAthlete,ClViewOrder,ClAgeFrom,ClAgeTo,ClValidClass,ClDivisionsAllowed,ClSex) "
			. "VALUES("
			. StrSafe_DB($_REQUEST['New_ClId']) . ","
			. StrSafe_DB($_SESSION['TourId']) . ","
			. StrSafe_DB($_REQUEST['New_ClDescription']) . ","
			. StrSafe_DB(intval($_REQUEST['New_ClAthlete'])) . ","
			. StrSafe_DB($_REQUEST['New_ClViewOrder']) . ", "
			. StrSafe_DB($_REQUEST['New_ClAgeFrom']) . ", "
			. StrSafe_DB($_REQUEST['New_ClAgeTo']) . ", "
			. StrSafe_DB(CreateValidClass($_REQUEST['New_ClId'],$_REQUEST['New_ClValidClass'])) . ", "
			. StrSafe_DB($_REQUEST['New_ClValidDivision']) . ", "
			. StrSafe_DB($_REQUEST['New_ClSex']) . " "
			. ") ";
		$RsIns=safe_w_sql($Insert); //duplicate entries is OK

		if (!safe_w_affected_rows()) {
			$Errore=2;
			$MsgErrore=get_text('DuplicateEntry','Tournament');
		}
	}

	header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<errormsg><![CDATA[' . $MsgErrore . ']]></errormsg>' . "\n";
	print '<new_clid><![CDATA[' . $_REQUEST['New_ClId'] . ']]></new_clid>' . "\n";
	print '<new_cldescr><![CDATA[' . ManageHTML($_REQUEST['New_ClDescription']) . ']]></new_cldescr>' . "\n";
	print '<new_clathleteyes><![CDATA[' . ManageHTML(get_text('Yes')) . ']]></new_clathleteyes>' . "\n";
	print '<new_clathleteno><![CDATA[' . ManageHTML(get_text('No')) . ']]></new_clathleteno>' . "\n";
	print '<new_clathlete><![CDATA[' . $_REQUEST['New_ClAthlete'] . ']]></new_clathlete>' . "\n";
	print '<new_clprogr>' . $_REQUEST['New_ClViewOrder'] . '</new_clprogr>' . "\n";
	print '<new_clagefrom>' . $_REQUEST['New_ClAgeFrom'] . '</new_clagefrom>' . "\n";
	print '<new_clageto>' . $_REQUEST['New_ClAgeTo'] . '</new_clageto>' . "\n";
	print '<new_clvalid><![CDATA[' . $_REQUEST['New_ClValidClass'] . ']]></new_clvalid>' . "\n";
	print '<new_clvaliddiv><![CDATA[' . $_REQUEST['New_ClValidDivision'] . ']]></new_clvaliddiv>' . "\n";
	print '<new_clsex>' . $_REQUEST['New_ClSex'] . '</new_clsex>' . "\n";
	print '<confirm_msg><![CDATA[' . get_text('MsgAreYouSure') . ']]></confirm_msg>' . "\n";
// Stringhe maschio e femmina
	print '<male>' . get_text('ShortMale','Tournament') . '</male>' . "\n";
	print '<female>' . get_text('ShortFemale','Tournament') . '</female>' . "\n";
	print '<unisex>' . get_text('ShortUnisex','Tournament') . '</unisex>' . "\n";
	print '</response>' . "\n";
?>