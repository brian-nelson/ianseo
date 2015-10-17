<?php
/*
													- GetStatus.php -
	Ritorna lo status degli atleti.
	Se il parameto Id non è vuoto allora la query avverrà solo su quel codice
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Id = (isset($_REQUEST['Id']) ? $_REQUEST['Id'] : '');

	$Errore = 0;
	$xml = '';

	$Select
		= "SELECT EnId,EnStatus "
		. "FROM Entries "
		. "WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. ($Id!='' ? " AND EnId=" . StrSafe_DB($Id) . " " : "" )
		. "ORDER BY EnId ASC ";
	$Rs=safe_r_sql($Select);

	if (debug)
		print $Select . '<br><br>';

	if (safe_num_rows($Rs)>0)
	{
		while ($MyRow=safe_fetch($Rs))
		{
			$xml
				.= '<ath>' . "\n"
				 . '<id>' . $MyRow->EnId . '</id>' . "\n"
				 . '<status>' . $MyRow->EnStatus . '</status>' . "\n"
				 . '</ath>' . "\n";
		}
	}
	else
		$Errore=1;

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print $xml;
	print '</response>' . "\n";
?>