<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Tournament/Fun_Tournament.local.inc.php');
    checkACL(AclCompetition, AclReadWrite, false);

	if (!CheckTourSession() ||
		!isset($_REQUEST['New_ScId']) ||
		!isset($_REQUEST['New_ScDescription']) ||
		!isset($_REQUEST['New_ScViewOrder']))
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;

	if (!IsBlocked(BIT_BLOCK_TOURDATA))
	{
		// Aggiungo la nuova riga
		$Insert
			= "INSERT INTO SubClass (ScId,ScTournament,ScDescription,ScViewOrder) "
			. "VALUES("
			. StrSafe_DB($_REQUEST['New_ScId']) . ","
			. StrSafe_DB($_SESSION['TourId']) . ","
			. StrSafe_DB($_REQUEST['New_ScDescription']) . ","
			. StrSafe_DB($_REQUEST['New_ScViewOrder']) . " "
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
	print '<new_scid>' . str_pad($_REQUEST['New_ScId'],2,' ',STR_PAD_LEFT) . '</new_scid>' . "\n";
	print '<new_scdescr><![CDATA[' . ManageHTML($_REQUEST['New_ScDescription']) . ']]></new_scdescr>' . "\n";
	print '<new_scprogr>' . $_REQUEST['New_ScViewOrder'] . '</new_scprogr>' . "\n";
	print '<confirm_msg>' . get_text('MsgAreYouSure') . '</confirm_msg>' . "\n";
	print '</response>' . "\n";
?>