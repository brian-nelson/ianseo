<?php
/*
													- UpdateFieldEventList.php -
	Aggiorna il campo di Events passato in querystring.
*/

	define('debug',false);

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');
	require_once('Common/Fun_Various.inc.php');

	if (!CheckTourSession() ||
		!isset($_REQUEST['EvCode']) || !isset($_REQUEST['EcNumber']))
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;

	$Tuple = array();
	$Rules = array();

	$Tuple_Index=0;
	$xml = '';

	$NewGroup = 1;

	if (!IsBlocked(BIT_BLOCK_TOURDATA))
	{
		$Select
			= "SELECT (IF(MAX(EcTeamEvent) IS NULL,1,MAX(EcTeamEvent)+1))	 AS NewGroup "
			. "FROM EventClass "
			. "WHERE EcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EcCode=" . StrSafe_DB($_REQUEST['EvCode']) . " ";
		$Rs=safe_r_sql($Select);

		if (debug)
			print $Select . '<br>';

		if (safe_num_rows($Rs)==1)
		{
			$Row=safe_fetch($Rs);
			$NewGroup=$Row->NewGroup;
		}

		foreach ($_REQUEST['New_EcDivision'] as $DivKey => $DivValue)
		{
			foreach ($_REQUEST['New_EcClass'] as $ClKey => $ClValue)
			{
				$Tuple[$Tuple_Index]
					= "("
					. StrSafe_DB($_REQUEST['EvCode']) . ", "
					. StrSafe_DB($NewGroup) . ", "
					. StrSafe_DB($_SESSION['TourId']) . ", "
					. StrSafe_DB($ClValue) . ", "
					. StrSafe_DB($DivValue) . ","
					. StrSafe_DB($_REQUEST['EcNumber']) . ""
					. ")";
				$Rules[$Tuple_Index] = $DivValue . "|" . $ClValue;

				++$Tuple_Index;
			}
		}

		/*print '<pre>';
		print_r($Tuple);
		print '</pre>';*/

		foreach ($Tuple as $Key => $Value)
		{
			$Insert
				= "INSERT INTO EventClass (EcCode,EcTeamEvent,EcTournament,EcClass,EcDivision,EcNumber) "
				. "VALUES" . $Value;
			$RsIns=safe_w_sql($Insert);

			if (debug)
				print $Insert . '<br>';

			if (safe_w_affected_rows()==1)
			{
				$xml.= '<new_rule>' . $Rules[$Key] . '</new_rule>' . "\n";
			}
		}

	// calcolo il numero massimo di persone nel team
		calcMaxTeamPerson(array($_REQUEST['EvCode']));

	// reset shootoff dell'evento
		ResetShootoff($_REQUEST['EvCode'],1,0);

	// teamabs
		MakeTeamsAbs(null,null,null);
	}
	else
		$Errore=1;

	if ($xml=='')
		$Errore=1;

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<confirm_msg>' . get_text('MsgAreYouSure') . '</confirm_msg>' . "\n";
	print '<evcode>' . $_REQUEST['EvCode'] . '</evcode>' . "\n";
	print '<new_number>' . $_REQUEST['EcNumber'] . '</new_number>' . "\n";
	print '<new_group>' . $NewGroup . '</new_group>' . "\n";
	print $xml;
	/*print '<new_ecdivision>' . $_REQUEST['New_EcDivision'] . '</new_ecdivision>' . "\n";
	print '<new_ecclass>' . $_REQUEST['New_EcClass'] . '</new_ecclass>' . "\n";*/
	print '</response>' . "\n";
?>