<?php
/*
													- UpdateCountryCode.php -
	Aggiorna il codice della nazione alla persona.
	Riceve IdEntry=<id persona> e Code=<codice nazione>
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!isset($_REQUEST['IdEntry']) || !is_numeric($_REQUEST['IdEntry']) || !isset($_REQUEST['Code'])||  !CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadWrite, false);

	$Errore = 0;
	$CoName='';
	$CoId=0;

	if (strlen($_REQUEST['Code'])>0)
	{
		if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
		{
		// cerco il codice nazione nel db
			$Select
				= "SELECT CoId,CoName "
				. "FROM Countries "
				. "WHERE CoCode=" . StrSafe_DB($_REQUEST['Code']) . " AND CoTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
			$Rs=safe_r_sql($Select);

		// se non c'è lo aggiungo
			if ($Rs)
			{
				if (safe_num_rows($Rs)==0)
				{
					$Insert
						= "INSERT INTO Countries (CoTournament,CoCode,CoName) "
						. "VALUES("
						. StrSafe_DB($_SESSION['TourId']) . ","
						. StrSafe_DB($_REQUEST['Code']) . ","
						. "''"
						. ")";
						//print $Insert;exit;
					$RsIns=safe_w_sql($Insert);

				// estraggo l'ultimo id
					$SelId
						= "SELECT MAX(CoId) AS Id FROM Countries ";
					$RsId=safe_r_sql($SelId);
					if (safe_num_rows($RsId)==1)
					{
						$rr=safe_fetch($RsId);
						$CoId=$rr->Id;
					}
					else
						$Errore=1;
				}
				elseif (safe_num_rows($Rs)==1)
				{
					$MyRow=safe_fetch($Rs);
					$CoName=$MyRow->CoName;
					$CoId=$MyRow->CoId;
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

	if ($Errore==0)
	{
		$Update
			= "UPDATE Entries SET "
			. "EnCountry=" . StrSafe_DB($CoId) . " "
			. "WHERE EnId=" . StrSafe_DB($_REQUEST['IdEntry']) . " ";
		$RsUp=safe_w_sql($Update);
		if (!$RsUp)
			$Errore=1;
	}

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>';
	print '<error>' . $Errore . '</error>';
	print '<name>' . ($CoName!='' ? $CoName : '#') . '</name>';
	print '<id>' . $CoId . '</id>';
	print '<id_ret>' . $_REQUEST['IdEntry'] . '</id_ret>';
	print '</response>';
?>