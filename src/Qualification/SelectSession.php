<?php
/*
													- SelectSession.php -
	Estrae il primo e l'ultimo target della sessione
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;
	$First='';
	$Last='';

	if (isset($_REQUEST['Ses']))
	{
		$Select
			= "SELECT SUBSTRING(MIN(AtTargetNo),2," . TargetNoPadding . ") AS Minimo, SUBSTRING(MAX(AtTargetNo),2," . TargetNoPadding . ") AS Massimo "
			. "FROM AvailableTarget "
			. "WHERE AtTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AtTargetNo LIKE " . StrSafe_DB($_REQUEST['Ses'] . "%") . " ";
		$Rs=safe_r_sql($Select);
		if (debug)
			print $Select . '<br>';
		if (safe_num_rows($Rs)==1)
		{
			$MyRow=safe_fetch($Rs);
			$First=(!is_null($MyRow->Minimo) ? $MyRow->Minimo : '#');
			$Last=(!is_null($MyRow->Massimo) ? $MyRow->Massimo : '#');
		}
		else
			$Errore=1;
	}

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<minimo>' . $First . '</minimo>' . "\n";
	print '<massimo>' . $Last . '</massimo>' . "\n";
	print '</response>' . "\n";
?>