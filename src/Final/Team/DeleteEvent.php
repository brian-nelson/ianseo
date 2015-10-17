<?php
/*
													- UpdateFieldEventList.php -
	Aggiorna il campo di Events passato in querystring.
*/

	define('debug',false);

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

	if (!CheckTourSession() || !isset($_REQUEST['EvCode']))
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;

	if (!IsBlocked(BIT_BLOCK_TOURDATA))
	{
	// elimino la riga dalla tabella Events
		$Delete
			= "DELETE FROM Events "
			. "WHERE EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
		$Rs=safe_w_sql($Delete);
		set_qual_session_flags();
		if (debug) print $Delete . '<br>';

		if (safe_w_affected_rows()==1) {
			// deletes schedule
			safe_w_sql("delete from FinSchedule where FsTournament={$_SESSION['TourId']} and FsTeamEvent=1 and FsEvent=".StrSafe_DB($_REQUEST['EvCode']));
			// deletes warmup
			safe_w_sql("delete from FinWarmup where FwTournament={$_SESSION['TourId']} and FwTeamEvent=1 and FwEvent=".StrSafe_DB($_REQUEST['EvCode']));

			//	elimino le righe da EventClass
			$Delete
				= "DELETE FROM EventClass "
				. "WHERE EcCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EcTeamEvent!='0' AND EcTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
			$Rs=safe_w_sql($Delete);
			if (debug) print $Delete . '<br>';

			if ($Rs)
			{
			// elimino le righe da Teams
				$q="DELETE FROM Teams WHERE TeTournament={$_SESSION['TourId']} AND TeEvent='{$_REQUEST['EvCode']}' AND TeFinEvent=1 ";
				$r=safe_w_sql($q);

			// cancello i nomi
				$q="DELETE FROM TeamComponent WHERE TcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TcFinEvent=1 AND TcEvent=". StrSafe_DB($_REQUEST['EvCode']) . " ";
				$r=safe_w_sql($q);

		// cancello i nomi fin
				$q="DELETE FROM TeamFinComponent WHERE TfcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfcEvent=" .  StrSafe_DB($_REQUEST['EvCode']) . " ";
				$r=safe_w_sql($q);

			// elimino le griglie
				$Delete
					= "DELETE FROM TeamFinals "
					. "WHERE TfEvent=" . StrSafe_DB($_REQUEST['EvCode']) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
				$Rs=safe_w_sql($Delete);
				if (debug) print $Delete . '<br>';

				if (!$Rs)
					$Errore=1;
			}
			else
			{
				$Errore=1;
			}
		}
		else
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
	print '<event>' . $_REQUEST['EvCode'] . '</event>' . "\n";
	print '</response>' . "\n";
?>