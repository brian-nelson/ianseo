<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Tournament/Fun_Tournament.local.inc.php');
    checkACL(AclCompetition, AclReadWrite, false);

	if (!CheckTourSession() ||
		!isset($_REQUEST['New_DivId']) ||
		!isset($_REQUEST['New_DivDescription']) ||
		!isset($_REQUEST['New_DivAthlete']) ||
		!isset($_REQUEST['New_DivViewOrder']))
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;

	if (!IsBlocked(BIT_BLOCK_TOURDATA) && !defined('dontEditClassDiv'))
	{
		// Aggiungo la nuova riga
		$Insert
			= "INSERT INTO Divisions (DivId,DivTournament,DivDescription,DivAthlete,DivViewOrder) "
			. "VALUES("
			. StrSafe_DB($_REQUEST['New_DivId']) . ","
			. StrSafe_DB($_SESSION['TourId']) . ","
			. StrSafe_DB($_REQUEST['New_DivDescription']) . ","
			. StrSafe_DB(intval($_REQUEST['New_DivAthlete'])) . ","
			. StrSafe_DB($_REQUEST['New_DivViewOrder']) . " "
			. ") ";
		$RsIns=safe_w_sql($Insert);

		if (debug) print $Insert . '<br>';

		if (!$RsIns)
		{
			$Errore=1;
		}
	}
	else
		$Errore=1;

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<new_divid>' . $_REQUEST['New_DivId'] . '</new_divid>' . "\n";
	print '<new_divdescr><![CDATA[' . ManageHTML($_REQUEST['New_DivDescription']) . ']]></new_divdescr>' . "\n";
	print '<new_divathlete><![CDATA[' . ManageHTML($_REQUEST['New_DivAthlete']) . ']]></new_divathlete>' . "\n";
	print '<new_divathleteyes><![CDATA[' . ManageHTML(get_text('Yes')) . ']]></new_divathleteyes>' . "\n";
	print '<new_divathleteno><![CDATA[' . ManageHTML(get_text('No')) . ']]></new_divathleteno>' . "\n";
	print '<new_divprogr>' . $_REQUEST['New_DivViewOrder'] . '</new_divprogr>' . "\n";
	print '<confirm_msg>' . get_text('MsgAreYouSure') . '</confirm_msg>' . "\n";
	print '</response>' . "\n";
?>