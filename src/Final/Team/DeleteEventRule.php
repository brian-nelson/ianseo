<?php
/*
													- DeleteEventRule.php -
	Elimina una coppia DivClass da EventClass
*/

	define('debug',false);

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');
	require_once('Common/Fun_Various.inc.php');

	if (!CheckTourSession() || !isset($_REQUEST['EvCode']) || !isset($_REQUEST['DelGroup']))
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
			. "AND EcTeamEvent=" . StrSafe_DB($_REQUEST['DelGroup']) . " "
			. "AND EcTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
		$Rs=safe_w_sql($Delete);
		if (debug) print $Delete;

		if (!safe_w_affected_rows())
		{
			$Errore=1;
		}
		else
		{
		// calcolo il numero massimo di persone nel team
			calcMaxTeamPerson(array($_REQUEST['EvCode']));

		// cancello le righe di Team per l'evento passato
			$queries[]
				= "DELETE FROM Teams WHERE TeTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TeFinEvent=1 AND TeEvent=" . StrSafe_DB($_REQUEST['EvCode']) . " ";

		// cancello i nomi
			$queries[]
				= "DELETE FROM TeamComponent WHERE TcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TcFinEvent=1 AND TcEvent=". StrSafe_DB($_REQUEST['EvCode']) . " ";

		// cancello i nomi fin
			$queries[]
				= "DELETE FROM TeamFinComponent WHERE TfcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfcEvent=" .  StrSafe_DB($_REQUEST['EvCode']) . " ";

		// elimino le griglie
			$queries[]
				= "DELETE FROM TeamFinals "
				. "WHERE TfEvent=" . StrSafe_DB($_REQUEST['EvCode']) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
			$Rs=safe_w_sql($Delete);

			foreach ($queries as $q)
			{
				safe_w_sql($q);
			}

		// reset shootoff
			ResetShootoff($_REQUEST['EvCode'],1,0);

		// teamabs
			MakeTeamsAbs(null,null,null);
		}

	}
	else
		$Errore=1;

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<event>' . $_REQUEST['EvCode'] . '</event>' . "\n";
	print '<group>' . $_REQUEST['DelGroup'] . '</group>' . "\n";
	print '</response>' . "\n";
?>