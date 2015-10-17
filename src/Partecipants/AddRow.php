<?php
/*
													- AddRow.php -
	Inserisce una riga nella tabella delle entries e aggiunge una riga bianca nella tabella HTML
	prepando i campi con gli id giusti
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Errore = 0;

	if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
	{
	// aggiungo la riga in entries
		$Insert
			= "INSERT INTO Entries (EnTournament,EnDivision,EnClass,EnSubClass,EnAgeClass,EnCountry,EnCtrlCode,EnCode,EnName,EnFirstName,EnAthlete,EnSex,EnWChair,EnSitting,EnIndClEvent,EnTeamClEvent,EnIndFEvent,EnTeamFEvent,EnStatus,EnDob) "
			. "VALUES("
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
			. "'0',"
			. "'0000-00-00'"
			. ") ";
		$Rs=safe_w_sql($Insert);

		if (debug)
			print ($Insert) . '<br>';

	// recupero l'ultimo id della tabella
		$NewId=0;
		$Select
			= "SELECT MAX(EnId) AS Id FROM Entries ";
		$Rs=safe_r_sql($Select);
		if (debug)
			print $Select . '<br>';

		if (safe_num_rows($Rs)==1)
		{
			$rr=safe_fetch($Rs);
			$NewId=$rr->Id;
		}

	// aggiungo la riga in Qualifications
		$Insert
			= "INSERT INTO Qualifications (QuId,QuSession) "
			. "VALUES("
			. StrSafe_DB($NewId) . ","
			. "0"
			. ") ";
		$Rs=safe_w_sql($Insert);

	// le query che seguono mi servono per generare la tendine dinamiche
		$Arr_Ses = array();
		$Select = "SELECT ToNumSession FROM Tournament WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
		$Rs=safe_r_sql($Select);
		if (safe_num_rows($Rs)==1)
		{
			$Row=safe_fetch($Rs);
			for ($i=1;$i<=$Row->ToNumSession;++$i)
			{
				$Arr_Ses[$i]=$i;
			}
		}

		$Arr_Div = array();
		$Select = "SELECT DivId FROM Divisions WHERE DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY DivViewOrder ASC ";
		$Rs=safe_r_sql($Select);
		if (safe_num_rows($Rs)>0)
		{
			while ($Row=safe_fetch($Rs))
			{
				$Arr_Div[$Row->DivId]=$Row->DivId;
			}
		}

		$Arr_Cl = array();
		$Select = "SELECT ClId FROM Classes WHERE ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY ClViewOrder ASC ";
		$Rs=safe_r_sql($Select);
		if (safe_num_rows($Rs)>0)
		{
			while ($Row=safe_fetch($Rs))
			{
				$Arr_Cl[$Row->ClId]=$Row->ClId;
			}
		}

		$Arr_SubCl = array();
		$Select = "SELECT ScId FROM SubClass WHERE ScTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY ScViewOrder ASC ";
		$Rs=safe_r_sql($Select);
		if (safe_num_rows($Rs)>0)
		{
			while ($Row=safe_fetch($Rs))
			{
				$Arr_SubCl[$Row->ScId]=$Row->ScId;
			}
		}

		$xml = '';

		foreach ($Arr_Ses as $Key => $Value)
		{
			$xml .= '<sessions>' . $Key . '</sessions>' . "\n";
		}

		foreach ($Arr_Div as $Key => $Value)
		{
			$xml .= '<divisions>' . $Key . '</divisions>' . "\n";
		}

		foreach ($Arr_Cl as $Key => $Value)
		{
			$xml .= '<classes>' . $Key . '</classes>' . "\n";
		}

		foreach ($Arr_SubCl as $Key => $Value)
		{
			$xml .= '<sub_classes>' . $Key . '</sub_classes>' . "\n";
		}
	}
	else
		$Errore=1;

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<new_id>' . $NewId . '</new_id>' . "\n";
	print $xml;
	print '<confirm_msg1><![CDATA[' . get_text('Archer') . ']]></confirm_msg1>';
	print '<confirm_msg2><![CDATA['	. get_text('Country') . ']]></confirm_msg2>';
	print '<confirm_msg3><![CDATA[' . get_text('OpDelete','Tournament') . ']]></confirm_msg3>';
	print '<confirm_msg4><![CDATA[' . get_text('MsgAreYouSure') . ']]></confirm_msg4>';
	print '</response>' . "\n";

?>