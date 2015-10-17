<?php
/*
													- WriteScore_Bra.php -
	Scrive in Finals
*/

	define('debug',false);

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Final/Fun_ChangePhase.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;
	$FieldType = '#';
	$FieldError=0;
	$Which = '#';

	$OldId = '';
	$Propagato=0;

	$AthProp=0;

	$xml='';

	if (!IsBlocked(BIT_BLOCK_IND))
	{
		foreach ($_REQUEST as $Key => $Value)
		{
		// ho qualcosa da scrivere (considero l'attuale matchno)
			if (substr($Key,0,4)=='d_S_' || substr($Key,0,4)=='d_T_' || substr($Key,0,4)=='d_t_')
			{

				$Which=$Key;
				$ee=''; $mm='';
				list(,,$ee,$mm)=explode('_',$Key);

				// Max Score is dependent on Event Ends, Arrows by end and Target type
				$MaxScores=GetMaxScores($ee, $mm);

				$Which = $Key;

				if (debug) print $ee . ' - ' . $mm . '<br>';

				if (substr($Key,0,4)=='d_S_')
				{

					$FieldType = 'Score';
					if (debug) print $FieldType . '<br>';

					if (!is_numeric($Value) || $Value > ($MaxScores['MaxSetPoints'] ? $MaxScores['MaxSetPoints'] : $MaxScores['MaxMatch']))
					{
						$FieldError=1;
					}
					else
					{


						$Update
							= "UPDATE Finals "
							. "INNER JOIN Events ON FinEvent=EvCode AND EvTeamEvent='0' AND EvTournament=FinTournament "
							. "SET "
							. "FinScore=IF(EvMatchMode=0," . StrSafe_DB($Value) . ",FinScore), "
							. "FinSetScore=IF(EvMatchMode=0,FinSetScore," . StrSafe_DB($Value) . "), "
							. "FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " "
							. "WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent=" . StrSafe_DB($ee) . " AND FinMatchNo=" . StrSafe_DB($mm) . " ";
						$Rs=safe_w_sql($Update);

						if (debug)
							print $Update . '<br>';
						if (!$Rs)
							$Errore=1;
					}
				}
				elseif (substr($Key,0,4)=='d_T_')
				{
					$FieldType = 'Tie';
					if (debug) print $FieldType . '<br>';

					if (!is_numeric($Value) && !($Value>=0 && $Value<=2))
					{
						$FieldError=1;
					}
					else
					{
						$Update
							= "UPDATE Finals SET "
							. "FinTie=" . StrSafe_DB($Value) . ", "
							. "FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " "
							. "WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent=" . StrSafe_DB($ee) . " AND FinMatchNo=" . StrSafe_DB($mm) . " ";
						$Rs=safe_w_sql($Update);
						if (debug)
							print $Update . '<br>';

						if (!$Rs)
							$Errore=1;
					}
				}
				elseif (substr($Key,0,4)=='d_t_')
				{
					$tiebreak='';
					$tiepoints = explode('|',$_REQUEST['d_t_' . $ee . '_' . $mm]);
					for ($i=0;$i<count($tiepoints);++$i)
						$tiebreak .= GetLetterFromPrint($tiepoints[$i]);
					$Update
						= "UPDATE Finals SET "
						. "FinTiebreak=" . StrSafe_DB($tiebreak) . ", "
						. "FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " "
						. "WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent=" . StrSafe_DB($ee) . " AND FinMatchNo=" . StrSafe_DB($mm) . " ";
					$Rs=safe_w_sql($Update);
				}

				$xml.= '<which>' . $Which . '</which>' . "\n";
				$xml.= '<field_error>' . $FieldError . '</field_error>' . "\n";

			// faccio il passaggio di fase di quel matchno e di quello accoppiato
				if ($Errore==0)
				{
					//Faccio i passaggi di fase
					$updateTS = move2NextPhase(NULL, $ee, $mm);

					if (!is_null($updateTS))
					{

						$Select
							= "SELECT "
							. "FinMatchNo, FinEvent,  FinAthlete, IF(EvMatchMode=0,FinScore,FinSetScore) AS Score, FinTie, "
							. "IFNULL(CONCAT(EnFirstName,' ',SUBSTRING(EnName,1,1),'.'),'#') AS Atleta, "
							. "IFNULL(CoCode,'#') AS Country "
							. "FROM Finals "
							. "INNER JOIN Events ON FinEvent=EvCode AND EvTeamEvent='0' AND EvTournament=FinTournament "
							. "LEFT JOIN Entries ON FinAthlete=EnId "
							. "LEFT JOIN Countries ON EnCountry=CoId "
							. "WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent=" . StrSafe_DB($ee) . " AND FinDateTime=" . StrSafe_DB($updateTS) . " "
							. "ORDER BY FinEvent, FinMatchNo";
						$Rs=safe_w_sql($Select);
						if(safe_num_rows($Rs)>0)
						{
							while ($MyRow=safe_fetch($Rs))
							{
								$xml.= '<ath>';
								$xml.= '<name><![CDATA[' . $MyRow->Atleta . ']]></name>' . "\n";
								$xml.= '<cty><![CDATA[' . $MyRow->Country . ']]></cty>' . "\n";
								$xml.= '<event><![CDATA[' . $MyRow->FinEvent . ']]></event>' . "\n";
								$xml.= '<matchno>' . $MyRow->FinMatchNo . '</matchno>' . "\n";
								$xml.= '<tie>' . $MyRow->FinTie . '</tie>' . "\n";
								$xml.= '</ath>' . "\n";
							}
						}
					}
				}
			}
		}
	}
	else
		$Errore=1;

// produco l'xml di ritorno
	header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print $xml;
	print '</response>' . "\n";
?>
