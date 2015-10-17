<?php
/*
													- UpdateFieldEventList.php -
	Aggiorna il campo di Events passato in querystring.
*/

	define('debug',false);

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');

	if (!CheckTourSession() ||
		!isset($_REQUEST['EvCode']))
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;

	$Tuple = array();
	$Rules = array();

	$Tuple_Index=0;
	$xml = '';

	if (!IsBlocked(BIT_BLOCK_TOURDATA))
	{
		foreach ($_REQUEST['New_EcDivision'] as $DivKey => $DivValue)
		{
			foreach ($_REQUEST['New_EcClass'] as $ClKey => $ClValue)
			{
				$Tuple[$Tuple_Index]
					= "("
					. StrSafe_DB($_REQUEST['EvCode']) . ", "
					. StrSafe_DB(0) . ", "
					. StrSafe_DB($_SESSION['TourId']) . ", "
					. StrSafe_DB($ClValue) . ", "
					. StrSafe_DB($DivValue) . ""
					. ")";
				$Rules[$Tuple_Index] = $DivValue ."|". $ClValue;

				++$Tuple_Index;
			}
		}

		/*print '<pre>';
		print_r($Tuple);
		print '</pre>';*/

		foreach ($Tuple as $Key => $Value)
		{
			$Insert
				= "INSERT INTO EventClass (EcCode,EcTeamEvent,EcTournament,EcClass,EcDivision) "
				. "VALUES" . $Value;
			$RsIns=safe_w_sql($Insert,false,array(1062));

			if (safe_w_affected_rows()==1)
			{
				$xml.= '<new_rule>' . $Rules[$Key] . '</new_rule>' . "\n";
			}
		}

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

		if ($xml=='')
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
	print '<confirm_msg>' . get_text('MsgAreYouSure') . '</confirm_msg>' . "\n";
	print '<evcode>' . $_REQUEST['EvCode'] . '</evcode>' . "\n";
	print $xml;
	/*print '<new_ecdivision>' . $_REQUEST['New_EcDivision'] . '</new_ecdivision>' . "\n";
	print '<new_ecclass>' . $_REQUEST['New_EcClass'] . '</new_ecclass>' . "\n";*/
	print '</response>' . "\n";
?>