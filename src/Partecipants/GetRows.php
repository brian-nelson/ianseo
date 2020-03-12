<?php
/*
															- GetRows.php
	Ritorna tutte le righe di Entries
*/

	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Partecipants/Fun_Partecipants.local.inc.php');

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadOnly, false);

	$Errore = 0;

	$EnId = (isset($_REQUEST['Id']) ? $_REQUEST['Id'] : '');
	$OrderBy = (isset($_REQUEST['OrderBy']) ? $_REQUEST['OrderBy'] : '');

	$Sessions = '';
	$Divisions = '';
	$SubClasses = '';
	$AllClasses = '';

// Ritorno l'elenco delle sessioni
	$Select
		= "SELECT ToNumSession FROM Tournament WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)==1)
	{
		$Row=safe_fetch($Rs);

		for ($i=1; $i<=$Row->ToNumSession;++$i)
			$Sessions.= '<session_num>' . $i . '</session_num>';
	}
	else
		$Errore=1;

	if ($Errore==0)
	{
	// Ritorno l'elenco delle divisioni
		$Select
			= "SELECT * FROM Divisions WHERE DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY DivViewOrder ASC ";
		$Rs=safe_r_sql($Select);
		if (safe_num_rows($Rs)>0)
		{
			while ($Row=safe_fetch($Rs))
			{
				$Divisions.= '<div_id><![CDATA[' . $Row->DivId . ']]></div_id>';
			}
		}

	// Ritorno l'elenco delle subclasses
		$Select
			= "SELECT * FROM SubClass WHERE ScTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY ScViewOrder ASC ";
		$Rs=safe_r_sql($Select);
		if (safe_num_rows($Rs)>0)
		{
			while ($Row=safe_fetch($Rs))
			{
				$SubClasses.= '<subcl_id><![CDATA[' . $Row->ScId . ']]></subcl_id>';
			}
		}

	// Ritorno l'elenco di tutte le classi
		$Select
			= "SELECT ClId FROM Classes WHERE ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY ClViewOrder ASC ";
		$Rs=safe_r_sql($Select);
		if (safe_num_rows($Rs)>0)
		{
			while ($Row=safe_fetch($Rs))
			{
				$AllClasses.= '<cl_id><![CDATA[' . $Row->ClId . ']]></cl_id>';
			}
		}
	}

// Produco l'xml di ritorno
	if (!debug)
		header('Content-Type: text/xml; charset=UTF-8');

	print '<response>';
	print '<error>' . $Errore . '</error>';
	if ($Errore==0)
	{
		print '<PARAM>';
		print $Sessions;
		print $Divisions;
		print $AllClasses;
		print $SubClasses;
		print '<confirm_msg1><![CDATA[' . get_text('Archer') . ']]></confirm_msg1>';
		print '<confirm_msg2><![CDATA[' . get_text('Country') . ']]></confirm_msg2>';
		print '<confirm_msg3><![CDATA[' . get_text('OpDelete','Tournament') . ']]></confirm_msg3>';
		print '<confirm_msg4><![CDATA[' . get_text('MsgAreYouSure') . ']]></confirm_msg4>';
		print '</PARAM>';

		if ($OrderBy!='')
			print GetRows($EnId,$OrderBy);
		else
			print GetRows($EnId);
	}
	print '</response>';
?>