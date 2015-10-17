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

	if (!IsBlocked(BIT_BLOCK_TEAM))
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
				$MaxScores=GetMaxScores($ee, $mm, '1'); // team

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
							= "UPDATE TeamFinals "
							. "INNER JOIN Events ON TfEvent=EvCode AND EvTeamEvent='1' AND EvTournament=TfTournament "
							. "SET "
							. "TfScore=IF(EvMatchMode=0," . StrSafe_DB($Value) . ",TfScore), "
							. "TfSetScore=IF(EvMatchMode=0,TfSetScore," . StrSafe_DB($Value) . "), "
							. "TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " "
							. "WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfEvent=" . StrSafe_DB($ee) . " AND TfMatchNo=" . StrSafe_DB($mm) . " ";
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
							= "UPDATE TeamFinals SET "
							. "TfTie=" . StrSafe_DB($Value) . ", "
							. "TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " "
							. "WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfEvent=" . StrSafe_DB($ee) . " AND TfMatchNo=" . StrSafe_DB($mm) . " ";
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
						= "UPDATE TeamFinals SET "
						. "TfTiebreak=" . StrSafe_DB($tiebreak) . ", "
						. "TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " "
						. "WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfEvent=" . StrSafe_DB($ee) . " AND TfMatchNo=" . StrSafe_DB($mm) . " ";
					$Rs=safe_w_sql($Update);
				}

				$xml.= '<which>' . $Which . '</which>' . "\n";
				$xml.= '<field_error>' . $FieldError . '</field_error>' . "\n";

			// faccio il passaggio di fase di quel matchno e di quello accoppiato
				if ($Errore==0)
				{
					//Faccio i passaggi di fase
					$updateTS = move2NextPhaseTeam(NULL, $ee, $mm);

					if (!is_null($updateTS))
					{

						$Select
							= "SELECT "
							. "TfMatchNo, TfEvent,  TfTeam, TfSubTeam, IF(EvMatchMode=0,TfScore,TfSetScore) AS Score, TfTie, "
							. "CoCode, CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) AS TeamName "
							. "FROM TeamFinals "
							. "INNER JOIN Events ON TfEvent=EvCode AND EvTeamEvent='1' AND EvTournament=TfTournament "
							. "LEFT JOIN Countries ON TfTeam=CoId "
							. "WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfEvent=" . StrSafe_DB($ee) . " AND TfDateTime=" . StrSafe_DB($updateTS) . " "
							. " and TfMatchNo in (".($mm%2 ? ($mm-1).','.$mm : $mm.','.($mm+1)).")"
							. "ORDER BY TfEvent, TfMatchNo";
						$Rs=safe_w_sql($Select);
						if(safe_num_rows($Rs)>0)
						{
							while ($MyRow=safe_fetch($Rs))
							{
							$xml.= '<ath>';
							$xml.= '<name><![CDATA[' . $MyRow->TeamName . ']]></name>' . "\n";
							$xml.= '<cty><![CDATA[' . $MyRow->CoCode . ']]></cty>' . "\n";
							$xml.= '<event><![CDATA[' . $MyRow->TfEvent . ']]></event>' . "\n";
							$xml.= '<matchno>' . $MyRow->TfMatchNo . '</matchno>' . "\n";
							$xml.= '<score>' . $MyRow->Score . '</score>' . "\n";
							$xml.= '<tie>' . $MyRow->TfTie . '</tie>' . "\n";
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
