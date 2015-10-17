<?php
/*
													- UpdateTargetNo.php -
	La pagina aggiorna il TargetNo del tizio in Qualifications se la sessione è settata
*/

define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;
	$Id='#';
	$Msg = get_text('CmdOk');
	$PadValue='#';
	$Doppi=0;

	if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
	{
		foreach ($_REQUEST as $Key => $Value)
		{
			if (substr($Key,0,2)=='d_')
			{

				$Campo = '';
				$Chiave = '';

				list(,,$Campo,$Chiave) = explode('_',$Key);
				$Id=$Chiave;
				if (str_replace(' ','',$Value)!='')
				{
					if (preg_match('/^[0-9]{1,' . TargetNoPadding . '}[a-z]{1}$/i',$Value))
					{
					// verifico che in db ci sia settata la sessione != 0
						$SelectSes
							= "SELECT QuSession "
							. "FROM Qualifications "
							. "WHERE QuId=" . StrSafe_DB($Chiave) . " ";
						$RsS=safe_r_sql($SelectSes);
						if (safe_num_rows($RsS)==1)
						{
						// Al padding aggiungo +1 per considerare la lettera
							$PadValue=str_pad(strtoupper($Value),(TargetNoPadding+1),'0',STR_PAD_LEFT);

							$RowSes=safe_fetch($RsS);

							$Target = $RowSes->QuSession . $PadValue;
							// se il target è nei bersagli disponibili aggiorno
							$Sel = "SELECT AtTargetNo FROM AvailableTarget WHERE AtTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AtTargetNo=" . StrSafe_DB($Target) . " ";
							//print $Sel;exit;
							$RsSel=safe_r_sql($Sel);
							if (debug)
								print $Sel . '<br>';
							if (safe_num_rows($RsSel)==1)
							{
								$Update
									= "UPDATE Qualifications SET "
									. "QuTargetNo=" . StrSafe_DB($Target) . " "
									. "WHERE QuId=" . StrSafe_DB($Chiave) . " ";
								$RsUp=safe_w_sql($Update);
								if(safe_w_affected_rows()) safe_w_sql("UPDATE Qualifications SET QuBacknoPrinted=0 WHERE QuId='{$Chiave}'");

								if (debug)
									print $Update . '<br>';


							}
							else
								$Errore=1;
						}
						else
							$Errore=1;
					}
					elseif($Value==0)
					{
						$Update = "UPDATE Qualifications SET QuTargetNo='', QuBacknoPrinted=0 WHERE QuId=" . StrSafe_DB($Chiave);
						$RsUp=safe_w_sql($Update);
					}
					else
						$Errore=1;
				}
			}
		}
	}
	else
		$Errore=1;

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<pad_value>' . $PadValue . '</pad_value>' . "\n";
	print '<id>' . $Id . '</id>' . "\n";
	print '<ses>' . $_REQUEST['Ses'] . '</ses>' . "\n";
	print '</response>' . "\n";
?>