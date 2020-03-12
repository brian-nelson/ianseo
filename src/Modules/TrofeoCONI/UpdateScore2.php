<?php
	define('debug',false);

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Fun_CONI.local.inc.php');

	CheckTourSession(true);

	$error=0;
	$which=(isset($_REQUEST['which']) ? $_REQUEST['which'] : null);
	$value=(isset($_REQUEST['value']) ? $_REQUEST['value'] : null);


	if (is_null($which) || is_null($value))
		exit;

	if (!IsBlocked(BIT_BLOCK_TEAM))
	{
	/*
	 * Contiene le parti di $which.
	 * Avrò 4 elementi: [STt] Event Round Match ;
	 */
		$parts=explode('_',$which);

		// get the match maximum values
		$MaxScores=GetMaxScores($parts[1], $parts[3], '1');

		$field='';			// nome del campo da aggiornare
		$val2write='';		// valore del campo da aggiornare

	// A seconda di cosa devo scrivere faccio le verifiche opportune
		if ($parts[0]=='S')		// score
		{

			if (!is_numeric($value) || $value > $MaxScores['MaxMatch']) {
				$error=1;
			} else {
				$field='CaSScore';
				$val2write=$value;
			}
		}
		elseif ($parts[0]=='P')	// SetPoints
		{
			if (!is_numeric($value) || $value > $MaxScores['MaxSetPoints']) {
				$error=1;
			} else 	{
				$field='CaSSetScore';
				$val2write=$value;
			}
		}
		elseif ($parts[0]=='T')	// tie
		{
			if (is_numeric($value) && $value>=0 && $value<=2)
			{
				$field='CaSTie';
				$val2write=$value;
			}
			else
			{
				$error=1;
			}
		}
		elseif ($parts[0]=='t')		// tiebreak
		{
			$tiebreak='';
			$tiepoints = explode('|',$value);
			/*print '<pre>';
			print_r($tiepoints);
			print '</pre>';*/
			if (count($tiepoints)==TieBreakArrows_Team)
			{

				$tiebreak=str_pad($tiebreak,TieBreakArrows_Team,GetLetterFromPrint('M'),STR_PAD_RIGHT);
				for ($i=0;$i<TieBreakArrows_Team;++$i)
				{
					$vv = strtoupper($tiepoints[$i]);

					$tiebreak[$i]=GetLetterFromPrint($vv);

				}
				$field='CaSTiebreak';
				$val2write=$tiebreak;
			}
			else
			{
				$error=1;
			}
		}

		if ($field!='')
		{
			$Query
				= "UPDATE "
					. "CasScore "
				. "SET "
					. $field . "=" . StrSafe_DB($val2write) . " "
				. "WHERE "
					. "CaSTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND  "
					. "CaSPhase=2 AND "
					. "CaSRound=" . StrSafe_DB($parts[2]) . " AND "
					. "CaSMatchNo=" . StrSafe_DB($parts[3]) . " AND "
					. "CaSEventCode=" . StrSafe_DB($parts[1]) . " ";
			//print '...'.$Query .'<br>';
			$Rs=safe_w_sql($Query);


			if (!$Rs)
			{
				$error=1;
			}
			else
			{
				//print 'qui<br>';
				$filter
					= " AND (CG.CGMatchNo1=" . StrSafe_DB($parts[3]) . " OR CG.CGMatchNo2=" . StrSafe_DB($parts[3]) . ") ";
				$Rs=getMatchesPhase1($parts[1],$parts[2],2,$filter);

				if ($Rs && safe_num_rows($Rs)==1)
				{
					$myRow=safe_fetch($Rs);

				// azzero i Points di entrambi i team
					$Query
						= "UPDATE "
							. "CasScore "
						. "SET "
							. "CaSPoints=0 "
						. "WHERE "
							. "CaSTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CaSPhase=2 AND CaSRound=" . StrSafe_DB($parts[2]) . " "
								. "AND CaSEventCode=" . StrSafe_DB($parts[1]) . "  AND CaSMatchNo IN(" . join(",",array($myRow->Match1,$myRow->Match2)) . ") ";
					$Rs=safe_w_sql($Query);
					//print $Query . '<br>';
					if (!$Rs)
					{
						$error=1;
					}
					else
					{
					/*
					 * Decido chi vince.
					 *
					 * Vince chi ha lo score più alto; a parità di score vince il tie più alto; a parità di tie non vince nessuno
					 */
						$match2up=$myRow->Match1 . "," . $myRow->Match2;	// in caso update ad entrambi con 1 punto
						$points=1;

					//	print $myRow->Score1 . ' ' . $myRow->Score2 . '<br>';
						//print $myRow->Tie1 . ' ' . $myRow->Tie2 . '<br>';

						if ($myRow->SetScore1>$myRow->SetScore2)		// vince 1
						{
							$points=2;
							$match2up=$myRow->Match1;
						}
						elseif ($myRow->SetScore1<$myRow->SetScore2)	// vince 2
						{
							$points=2;
							$match2up=$myRow->Match2;
						}
						else	// pari
						{
							//print 'qui';exit;
							if ($myRow->Tie1>$myRow->Tie2)	// vince 1
							{
								$points=2;
								$match2up=$myRow->Match1;
							}
							elseif ($myRow->Tie1<$myRow->Tie2)	// vince 2
							{
								$points=2;
								$match2up=$myRow->Match2;
							}
						}



						$Query
							= "UPDATE "
								. "CasScore "
							. "SET "
								. "CaSPoints="  .$points . " "
							. "WHERE "
								. "CaSTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CaSPhase=2 AND CaSRound=" . StrSafe_DB($parts[2]) . " "
									. "AND CaSEventCode=" . StrSafe_DB($parts[1]) . "  AND CaSMatchNo IN(" . $match2up. ") ";
						$Rs=safe_w_sql($Query);
				//print $Query . '<br>';
						if (!$Rs)
						{
							$error=1;
						}
						else	//azzero lo spareggio
						{
							$Query
								= "UPDATE "
									. "Events "
								. "SET "
									. "EvShootOff=0 "
								. "WHERE "
									. "EvCode=" . StrSafe_DB($parts[1]) . " AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 ";
							$Rs=safe_w_sql($Query);
							//print $Query;exit;
						}

					}
				}
				else
				{
					$error=1;
				}
			}
		}
	}

	header('Content-Type: text/xml');

	print '<response>' . "\n";
		print '<error>' . $error . '</error>' . "\n";
		print '<which>' . $which . '</which>' . "\n";
	print '</response>' . "\n";
