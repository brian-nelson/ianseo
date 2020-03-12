<?php
/*
													- CheckSession_Par.php -
	La pagina aggiorna controlla se è possibile settare la sessione scelta all'arciere
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_Sessions.inc.php');

	if (!CheckTourSession() || !isset($_REQUEST['Session'])) {
		print get_text('CrackError');
		exit;
	}
	checkACL(AclParticipants, AclReadOnly, false);

	$Errore=0;
	$Troppi=0;
	$Msg = get_text('CmdOk');
	$SESSION=intval($_REQUEST['Session']);
	$Rs=null;
// Bersagli e arcieri per bersaglio della sessione per ottenere il num max di arcieri per la sessione
	if ($SESSION!=0)
	{
		$ses=GetSessions('Q',array($SESSION.'_Q'));
		$Num4Session=$ses[0]->SesTar4Session*$ses[0]->SesAth4Target;

		if ($SESSION!=0)
		{
		// se non ho una riga nuova e la sessione nuova è diversa dalla vecchia
			$Select
				= "SELECT QuSession FROM Qualifications "
				. "WHERE QuId=" . StrSafe_DB($_REQUEST['Id']) . " && QuSession<>" . StrSafe_DB($SESSION) . " ";
			$RsOld = safe_r_sql($Select);

			// se ho una riga conto altrimenti la sessione è quella vecchia e non faccio nulla
			if ($RsOld)
			{
				if (safe_num_rows($RsOld)==1)
				{
				// conto il numero di arcieri inseriti per quella quella sessione
					$Select
						= "SELECT COUNT(QuId) AS Quanti "
						. "FROM Qualifications INNER JOIN Entries ON QuId=EnId AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
						. "WHERE QuSession=" . StrSafe_DB($SESSION) . " ";
					if (debug)
						print $Select . '<br>';
					$Rs=safe_r_sql($Select);

					if ($Rs)
					{
						if (safe_num_rows($Rs)==1)
						{
							$MyRow=safe_fetch($Rs);

							if ($Num4Session<$MyRow->Quanti+1)
							{
								$Troppi=1;
								$Msg = get_text('NoMoreAth4Session','Tournament');
							}
						}
					}
				}
				elseif (safe_num_rows($RsOld)>1)
					$Errore=1;
			}
			else
				$Errore=1;
		}

		/*$Select
			= "SELECT ToTar4Session" . $SESSION . ", ToAth4Target" . $SESSION . " FROM Tournament WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
		if (debug)
			print $Select . '<br>';
		$Rs=safe_r_sql($Select);
		if ($Rs)
		{
			if (safe_num_rows($Rs)==1)
			{
				$rr=safe_fetch($Rs);
				$Num4Session=$rr->{'ToTar4Session'. $SESSION} * $rr->{'ToAth4Target'. $SESSION};
				if (debug)
					print 'Num4Session ' . $Num4Session .'<br>';

				if ($SESSION!=0)
				{
				// se non ho una riga nuova e la sessione nuova è diversa dalla vecchia
					$Select
						= "SELECT QuSession FROM Qualifications "
						. "WHERE QuId=" . StrSafe_DB($_REQUEST['Id']) . " && QuSession<>" . StrSafe_DB($SESSION) . " ";
					$RsOld = safe_r_sql($Select);

					// se ho una riga conto altrimenti la sessione è quella vecchia e non faccio nulla
					if ($RsOld)
					{
						if (safe_num_rows($RsOld)==1)
						{
						// conto il numero di arcieri inseriti per quella quella sessione
							$Select
								= "SELECT COUNT(QuId) AS Quanti "
								. "FROM Qualifications INNER JOIN Entries ON QuId=EnId AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
								. "WHERE QuSession=" . StrSafe_DB($SESSION) . " ";
							if (debug)
								print $Select . '<br>';
							$Rs=safe_r_sql($Select);

							if ($Rs)
							{
								if (safe_num_rows($Rs)==1)
								{
									$MyRow=safe_fetch($Rs);

									if ($Num4Session<$MyRow->Quanti+1)
									{
										$Troppi=1;
										$Msg = get_text('NoMoreAth4Session','Tournament');
									}
								}
							}
						}
						elseif (safe_num_rows($RsOld)>1)
							$Errore=1;
					}
					else
						$Errore=1;
				}
			}
			else
				$Errore=1;
		}*/

	}

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>';
	print '<error>' . $Errore . '</error>';
	print '<troppi>' . $Troppi . '</troppi>';
	print '<msg>' . $Msg . '</msg>';
	print '</response>';
?>
