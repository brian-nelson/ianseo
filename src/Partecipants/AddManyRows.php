<?php
/*
													- AddManyRows.php -
	Inserisce N righe vuote preparando i campi con i valori di defaults
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession() || !isset($_REQUEST['Num']))
	{
		print get_text('CrackError');
		exit;
	}

	$Errore = 0;

	if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
	{
		$NextId=0;
	// Devo Scoprire il prox autoid di Entries
		$Select
			= "SELECT (MAX(EnId)+1) AS NextId FROM Entries ";

		if (debug)
			print $Select . '<br>';
		$Rs = safe_r_sql($Select);

		if (safe_num_rows($Rs)==1)
		{
			$Row=safe_fetch($Rs);
			$NextId=$Row->NextId;
		}

		$InsertE
			= "INSERT INTO Entries (EnId,EnTournament,EnDivision,EnClass,EnSubClass,EnAgeClass,EnCountry,EnCtrlCode,EnCode,EnName,EnFirstName,EnAthlete,EnSex,EnWChair,EnSitting,EnIndClEvent,EnTeamClEvent,EnIndFEvent,EnTeamFEvent,EnStatus) "
			. "VALUES ";

		$ValuesE = '';

		$InsertQ
			= "INSERT INTO Qualifications (QuId,QuSession) "
			. "VALUES ";
		$ValuesQ = '';

		for ($i=$NextId; $i<($NextId+$_REQUEST['Num']);++$i)
		{
			$ValuesE
				.= "(" . StrSafe_DB($i) . ","
				. StrSafe_DB($_SESSION['TourId']) . ","
				. "'',"
				. "'',"
				. "'',"
				. "'',"
				. "'0',"
				. "'',"
				. "'',"
				. "'',"
				. "'',"
				. "'1',"
				. "'0',"
				. "'0',"
				. "'0',"
				. "'1',"
				. "'1',"
				. "'1',"
				. "'1',"
				. "'0'"
				. "),";

			$ValuesQ
				.="(" . StrSafe_DB($i) . ","
				. "0"
				. "),";
		}
		$ValuesE = substr($ValuesE,0,-1);
		$ValuesQ = substr($ValuesQ,0,-1);

		$RsE=safe_w_sql($InsertE . $ValuesE);
		$RsQ=safe_w_sql($InsertQ . $ValuesQ);

		if (debug)
		{
			print $InsertE . $ValuesE . '<br>' . $InsertQ . $ValuesQ . '<br>';
		}

		if (!$RsE || !$RsQ)
			$Errore=1;
	}
	else
		$Errore=1;

	if (!debug)
		header('Content-Type: text/xml');


	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";

	print '</response>' . "\n";
?>