<?php
/*
													- SelectCountryCode.php -
	Compila il nome della nazione partendo dal codice della stessa
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!isset($_REQUEST['which']) && !isset($_REQUEST['Code'])||  !CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;
	$Id = 0;
	$Name='';

	if (strlen($_REQUEST['Code'])>0)
	{
	// cerco il codice nazione nel db
		$Select
			= "SELECT CoId,CoName "
			. "FROM Countries "
			. "WHERE CoCode=" . StrSafe_DB($_REQUEST['Code']) . " AND CoTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
		$Rs=safe_r_sql($Select);

		if ($Rs)
		{
			if (safe_num_rows($Rs)==1)
			{
				$MyRow=safe_fetch($Rs);
				$Id=$MyRow->CoId;
				$Name=$MyRow->CoName;
			}
		}
		else
			$Errore=1;
	}

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<which><![CDATA[' . $_REQUEST['which'] . ']]></which>' . "\n";
	print '<id><![CDATA[' . $Id . ']]></id>' . "\n";
	print '<name><![CDATA[' . $Name . ']]></name>' . "\n";
	print '</response>' . "\n";
?>