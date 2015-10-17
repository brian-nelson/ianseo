<?php
/*
													- DeleteEventRule.php -
	Elimina una coppia DivClass da EventClass
*/

	define('debug',false);

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');
	if (!CheckTourSession() || !isset($_REQUEST['EvCode']) || !isset($_REQUEST['DelDiv']) || !isset($_REQUEST['DelCl']))
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;

	if (!IsBlocked(BIT_BLOCK_TOURDATA))
	{
		$Delete
			= "DELETE FROM EventClass "
			. "WHERE EcCode=" . StrSafe_DB($_REQUEST['EvCode']) . " "
			. "AND EcTeamEvent='0' "
			. "AND EcTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "AND EcClass=" . StrSafe_DB($_REQUEST['DelCl']) . " "
			. "AND EcDivision=" . StrSafe_DB($_REQUEST['DelDiv']) . " ";
		$Rs=safe_w_sql($Delete);
		if (debug) print $Delete;

		if (safe_w_affected_rows() != 1)
		{
			$Errore=1;
		}
		else
		{
		// resetto gli shootoff per l'evento
			/*$q="
				UPDATE Events SET
					EvShootOff='0',EvE1ShootOff='0',EvE2ShootOff='0'
				WHERE
					EvCode='{$_REQUEST['EvCode']}' AND EvTournament={$_SESSION['TourId']} AND EvTeamEvent=0
			";
			$r=safe_w_sql($q);
			set_qual_session_flags();*/
			ResetShootoff($_REQUEST['EvCode'],0,0);
		// e faccio gli individuali abs
			MakeIndAbs();
		}
	}
	else
		$Errore=1;

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<event>' . $_REQUEST['EvCode'] . '</event>' . "\n";
	print '<div>' . $_REQUEST['DelDiv'] . '</div>' . "\n";
	print '<cl>' . $_REQUEST['DelCl'] . '</cl>' . "\n";
	print '</response>' . "\n";
?>