<?php
/*
													- UpdateCountryName.php -
	Aggiorna il nome di una determinata nazione se il codice non è vuoto.
	Ritorna l'id della nazione da cambiare, il nome e se bisogna cambiare il nome oppure no
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!isset($_REQUEST['Code']) ||  !isset($_REQUEST['Name']) || !CheckTourSession())	{
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadWrite, false);

	$Errore=0;
	$NuovoNome=0;

	if (strlen($_REQUEST['Name'])>0 && strlen($_REQUEST['Code'])>0)
	{
		if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
		{
			$Select
				= "SELECT CoId FROM Countries WHERE CoCode=" . StrSafe_DB($_REQUEST['Code']) . " AND CoTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
			$Rs=safe_r_sql($Select);

			if ($Rs)
			{
				if (safe_num_rows($Rs)==1)
				{
					$MyRow=safe_fetch($Rs);

					$Update
						= "UPDATE Countries SET "
						. "CoName=" . StrSafe_DB(stripslashes($_REQUEST['Name'])) . " "
						. "WHERE CoCode=" . StrSafe_DB($_REQUEST['Code']) . " AND CoTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
					$RsUp=safe_w_sql($Update);
					if (safe_w_affected_rows()==1)
						$NuovoNome=1;

					if (debug)
						print $Update . '<br><br>';

				}
				else
					$Errore=1;
			}
			else
				$Errore=1;
		}
		else
			$Errore=1;
	}

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>';
	print '<error>' . $Errore . '</error>';
	print '<new_name><![CDATA[' . stripslashes($NuovoNome) . ']]></new_name>';
	print '<name><![CDATA[' . stripslashes((trim($_REQUEST['Name'])!='' ? $_REQUEST['Name'] : '#')) . ']]></name>';
	print '<code>' . $_REQUEST['Code'] . '</code>';
	print '</response>';
?>