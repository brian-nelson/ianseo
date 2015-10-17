<?php
/*
													- UpdateFieldEventList.php -
	Aggiorna il campo di Events passato in querystring.
*/

	define('debug',false);

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');

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
			. "WHERE EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
		$Rs=safe_w_sql($Delete);
		set_qual_session_flags();
		if (debug) print $Delete . '<br>';

		if (safe_w_affected_rows()==1) {
			// deletes schedule
			safe_w_sql("delete from FinSchedule where FsTournament={$_SESSION['TourId']} and FsTeamEvent=0 and FsEvent=".StrSafe_DB($_REQUEST['EvCode']));
			// deletes warmup
			safe_w_sql("delete from FinWarmup where FwTournament={$_SESSION['TourId']} and FwTeamEvent=0 and FwEvent=".StrSafe_DB($_REQUEST['EvCode']));
			//	elimino le righe da EventClass
			$Delete
				= "DELETE FROM EventClass "
				. "WHERE EcCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EcTeamEvent='0' AND EcTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
			$Rs=safe_w_sql($Delete);
			if (debug) print $Delete . '<br>';

			if ($Rs)
			{
			// elimino le righe da Individuals
				$q="DELETE FROM Individuals WHERE IndTournament={$_SESSION['TourId']} AND IndEvent='{$_REQUEST['EvCode']}'";
				$r=safe_w_sql($q);

			// elimino le griglie
				$Delete
					= "DELETE FROM Finals "
					. "WHERE FinEvent=" . StrSafe_DB($_REQUEST['EvCode']) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
				$Rs=safe_w_sql($Delete);
				if (debug) print $Delete . '<br>';

			// elimino le griglie eliminatorie
				if ($Rs)
				{
					$Delete
						="DELETE FROM Eliminations "
						."WHERE ElEventCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND ElTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
					$Rs=safe_w_sql($Delete);

					if (!$Rs)
						$Errore=1;
				}
				else
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