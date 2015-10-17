<?php
/*
													- UpdateSession.php -
	La pagina aggiorna la sessione del tizio in Qualifications
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;
	$Troppi=0;
	$Id='';
	$NewTargetNo = '';
	$Session=0;
	$Msg = get_text('CmdOk');


	if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
	{
		foreach ($_REQUEST as $Key => $Value)
		{
			if (substr($Key,0,2)=='d_')
			{
				$Session=$Value;
				$Campo = '';
				$Chiave = '';
				$Num4Session = 999;

			// Bersagli e arcieri per bersaglio della sessione per ottenere il num max di arcieri per la sessione
				if($Value>0)
				{
					$Select = "SELECT SesTar4Session,SesAth4Target FROM Session WHERE SesTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND SesType=1 AND SesOrder=" . StrSafe_DB($Value) . " ";
					//print $Select;
					if (debug)
						print $Select . '<br>';
					$Rs=safe_r_sql($Select);
					if ($Rs)
					{
						if (safe_num_rows($Rs)==1)
						{
							$rr=safe_fetch($Rs);
							$Num4Session=$rr->SesTar4Session * $rr->SesAth4Target;
							if (debug)
								print 'Num4Session ' . $Num4Session .'<br>';
						}
					}
				}
				list(,,$Campo,$Chiave) = explode('_',$Key);
				//print '..'.$Chiave.'..';exit;
			// estraggo la sessione attuale del tipo
				$Sql
					= "SELECT QuSession FROM Qualifications WHERE QuId=" . StrSafe_DB($Chiave) . " ";
				$Rs = safe_r_sql($Sql);

				if (safe_num_rows($Rs)==1)
				{
					$RowOldSes = safe_fetch($Rs);


					if ($Value!=0 && $Value!=$RowOldSes->QuSession)
					{
					// conto il numero di arcieri inseriti per quella sessione
						$Select
							= "SELECT COUNT(QuId) AS Quanti "
							. "FROM Qualifications INNER JOIN Entries ON QuId=EnId AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
							. "WHERE QuSession=" . StrSafe_DB($Value) . " ";
						if (debug)
							print $Select . '<br>';
						$Rs=safe_r_sql($Select);

						if (safe_num_rows($Rs)==1)
						{
							$MyRow=safe_fetch($Rs);

							if ($Num4Session<$MyRow->Quanti+1)
							{
								$Troppi=1;
								$Msg = get_text('NoMoreAth4Session','Tournament');
								$Id=$Chiave;
							}
						}
					}

					if ($Troppi==0)
					{
						$Id=$Chiave;
						$Update
							= "UPDATE Qualifications SET "
							. "QuSession=" . StrSafe_DB($Value) . " "
							. "WHERE QuId=" . StrSafe_DB($Chiave) . " ";

						$RsUp=safe_w_sql($Update);

					// se la sessione è cambiata, annullo il targetno
						if (safe_w_affected_rows()==1)
						{
							$Update
								= "UPDATE Qualifications SET "
								. "QuTargetNo='', QuBacknoPrinted=0 "
								. "WHERE QuId=" . StrSafe_DB($Chiave) . " ";
							$RsUp=safe_w_sql($Update);
							$NewTargetNo='#';
						}
						if (debug)
							print $Update .'<br>';

					}
				}
				else
					$Errore=1;
			}
		}
	}
	else
		$Errore=1;

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<troppi>' . $Troppi . '</troppi>' . "\n";
	print '<msg>' . $Msg . '</msg>' . "\n";
	print '<id>' . $Id . '</id>' . "\n";
	print '<session>' . $Session . '</session>' . "\n";
	print '<new_targetno>' . $NewTargetNo . '</new_targetno>' . "\n";
	print '</response>' . "\n";
?>