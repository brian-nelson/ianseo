<?php
/*
 * Utilizzao Events.EvShootOf come flag x lo spareggio della seconda fase
 */
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_Number.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Fun_CONI.local.inc.php');

	CheckTourSession(true);

	$event=(isset($_REQUEST['EventCode']) ? $_REQUEST['EventCode'] : null);
	if (is_null($event))
		exit;

	$error=false;

	$QueryFilter = '';
	$IdAffected = array();
	$Ties=array();

	if (isset($_REQUEST['EventCode']) && strlen($_REQUEST['EventCode'])>0)
	{
		$Events = explode('|',$_REQUEST['EventCode']);
		$ee='';
		foreach ($Events as $Value)
			$ee.=StrSafe_DB($Value) . ",";
		$QueryFilter = "AND EvCode IN ('" . $event . "') ";

	}

	// verifico se lo spareggio per l'evento è stato fatto
	$Select
		= "SELECT EvE2ShootOff  "
		. "FROM Events "
		. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (EvE2ShootOff='1') AND EvTeamEvent='1' AND EvCode=" . StrSafe_DB($event) . " ";
	$Rs = safe_r_sql($Select);
	//print $Select;exit;
	if (!$Rs || safe_num_rows($Rs)!=1)
	{
		header('Location: AbsTeam2_1.php?EventCode=' . $event);
		exit;
	}

	$Command=isset($_REQUEST['Command']) ? $_REQUEST['Command'] : null;

	if (!is_null($Command))
	{
		if ($Command=='OK')
		{
			foreach ($_REQUEST as $Key=>$Value)
			{
				if (substr($Key,0,2)=='R_')
				{
					if (!(is_numeric($Value) && $Value>=0))
					{
						$Value="0";
					}

					list(,$ee,$mm)=explode('_',$Key);

					$Query
						= "UPDATE "
							. "CasTeam "
						. "SET "
							. "CaRank=" . StrSafe_DB($Value) . " "
						. "WHERE "
							. "CaTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CaPhase=2 AND CaMatchNo=" . StrSafe_DB($mm) . " "
								. "AND CaEventCode=" . StrSafe_DB($ee) . " ";
					$Rs=safe_w_sql($Query);
					//print $Query . '<br>';
					if (!$Rs)
						$error=true;

					if (safe_w_affected_rows()==1)
					{
						$IdAffected[$ee]= StrSafe_DB($ee);
					}
				}

				if (substr($Key,0,2)=='T_')
				{
				/*
				 * In $tmp ci sarà la stringa T_[index] e in $kk la stringa [ee]_[mm]_[pp]
				 */
					list ($tmp,$kk)=explode('-',$Key);

				/*
				 * Ora tiro fuori l'index del tie
				 */
					list (,$index)=explode('_',$tmp);

					if (!array_key_exists($kk,$Ties))
						$Ties[$kk]=str_pad('',9,' ');

					$v=GetLetterFromPrint($Value);

					$Ties[$kk]=substr_replace($Ties[$kk],$v,$index,1);
				}
			}
//exit;
			if (count($Ties)>0)
			{
				foreach ($Ties as $Key=>$Value)
				{
					list($ee,$mm)=explode('_',$Key);
					$Update
						= "UPDATE "
							. "CasTeam "
						. "SET "
							. "CaTieBreak=" . StrSafe_DB($Value) . " "
						. "WHERE "
							. "CaTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CaPhase=2 AND CaMatchNo=" . StrSafe_DB($mm) . " "
								. "AND CaEventCode=" . StrSafe_DB($ee) . "  ";
					//print $Update . '<br><br>';
					$RsUp=safe_w_sql($Update);
				}
			}
			//exit;
			if (!$error)
			{
				//if (count($IdAffected)>0)
				{
				/*
				 * Tiro fuori i nomi da mandare nelle semifinali.
				 */
					foreach ($IdAffected as $ev)
					{
					// prima ranco via le griglia a partire dalle semifinali
						$Delete
							= "DELETE FROM TeamFinals "
							. "USING "
								. "TeamFinals "
								. "INNER JOIN "
									. "Grids "
								. "ON TfMatchNo=GrMatchNo AND GrPhase<=2 AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfEvent=" . $ev . " ";
						//print $Delete . '<br>';
						$Rs=safe_w_SQL($Delete);
						
						// Distruggo le righe di TeamFinComponent basandomi su $IdAffected
						$Delete
						= "DELETE FROM TeamFinComponent "
						. "WHERE TfcTournament=" . StrSafe_DB($_SESSION['TourId']) . "AND TfcEvent=" . $ev . " ";
						//print $Delete;exit;
						$Rs=safe_w_sql($Delete);

						// ricreo la griglia distrutta
						$Insert
							= "INSERT INTO TeamFinals (TfEvent,TfMatchNo,TfTournament,TfDateTime) "
							. "SELECT EvCode,GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . "," . StrSafe_DB(date('Y-m-d H:i:s')) . " "
							. "FROM Events "
							. "INNER JOIN Grids ON GrPhase<=2 AND EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
							. "WHERE EvCode IN (" . implode(',',$IdAffected) . ")";
				//	print $Insert . '<br><br>';
						$RsIns=safe_w_sql($Insert);

					/*
					 * Ora tiro fuori 1-E e 2-F e rigenerando le due righe della griglia li
					 * sbatto in semifinale
					 */
						$Query
							= "SELECT "
								. "CaTeam,CaSubTeam,CaRank,CGGroup "
							. "FROM "
								. "CasTeam "
								. "INNER JOIN "
									. "CasGrid "
								. "ON (CaMatchNo=CGMatchNo1 OR CaMatchNo=CGMatchNo2) AND CaPhase=CGPhase AND CGPhase=2 AND CGRound=1 AND CaTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CaEventCode=" . $ev . " "
							. "WHERE "
								. "CaTournament=". StrSafe_DB($_SESSION['TourId']) . " AND CaPhase=2  AND "
									. "(CaRank=1 AND CGGroup=1) OR (CaRank=2 AND CGGroup=2) ";
						//print $Query . '<br><br>';exit;
						$Rs=safe_w_sql($Query);
						if ($Rs && safe_num_rows($Rs)==2)
						{
							while ($myRow=safe_fetch($Rs))
							{
								$mm=$myRow->CaRank==1 ? 4 : 5;

								$Query
									= "UPDATE "
										. "TeamFinals "
									. "SET "
										. "TfTeam=" . $myRow->CaTeam . ","
										. "TfSubTeam=" . $myRow->CaSubTeam . ","
										. "TfDateTime='" . date('Y-m-d H:i:s') . "' "
									. "WHERE "
										. "TfEvent=" . $ev . " AND "
										. "TfMatchNo=" . $mm . " AND "
										. "TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
										//print $Query . '<br><br>';
								$RsUp=safe_w_sql($Query);
								// componenti
								$Insert
									= "REPLACE INTO TeamFinComponent (TfcCoId,TfcSubTeam,TfcTournament,TfcEvent,TfcId,TfcOrder) "
									. "SELECT "
									. "TcCoId,TcSubTeam,TcTournament,TcEvent,TcId,TcOrder "
									. "FROM "
									. "TeamComponent "
									. "INNER JOIN "
									. "(SELECT DISTINCT TfTeam, TfSubTeam, TfEvent FROM TeamFinals WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . ") "
									. "as Sqy ON TcCoId=TfTeam AND TcSubTeam=TfSubTeam AND TcEvent=TfEvent "
									. "WHERE "
									. "TcFinEvent=1 AND TcTournament=" . StrSafe_DB($_SESSION['TourId']) . "  AND TcEvent=" . $ev;
								;
								$RsIns=safe_w_sql($Insert);
							}
						}

					/*
					 * Ora tiro fuori 2-E e 1-F e rigenerando le due righe della griglia li
					 * sbatto in semifinale
					 */
						$Query
							= "SELECT "
								. "CaTeam,CaSubTeam,CaRank,CGGroup "
							. "FROM "
								. "CasTeam "
								. "INNER JOIN "
									. "CasGrid "
								. "ON (CaMatchNo=CGMatchNo1 OR CaMatchNo=CGMatchNo2) AND CaPhase=CGPhase AND CGPhase=2 AND CGRound=1 AND CaTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CaEventCode=" . $ev . " "
							. "WHERE "
								. "CaTournament=". StrSafe_DB($_SESSION['TourId']) . " AND CaPhase=2 AND "
									. "(CaRank=2 AND CGGroup=1) OR (CaRank=1 AND CGGroup=2) ";
						//print $Query.'<br><br>';
						$Rs=safe_w_sql($Query);
						if ($Rs && safe_num_rows($Rs)==2)
						{
							while ($myRow=safe_fetch($Rs))
							{
								$mm=$myRow->CaRank==1 ? 7 : 6;

								$Query
									= "UPDATE "
										. "TeamFinals "
									. "SET "
										. "TfTeam=" . $myRow->CaTeam . ","
										. "TfSubTeam=" . $myRow->CaSubTeam . ","
										. "TfDateTime='" . date('Y-m-d H:i:s') . "' "
									. "WHERE "
										. "TfEvent=" . $ev . " AND "
										. "TfMatchNo=" . $mm . " AND "
										. "TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
									//	print $Query . '<br><br>';
								$RsUp=safe_w_sql($Query);
								// componenti
								$Insert
									= "REPLACE INTO TeamFinComponent (TfcCoId,TfcSubTeam,TfcTournament,TfcEvent,TfcId,TfcOrder) "
									. "SELECT "
									. "TcCoId,TcSubTeam,TcTournament,TcEvent,TcId,TcOrder "
									. "FROM "
									. "TeamComponent "
									. "INNER JOIN "
									. "(SELECT DISTINCT TfTeam, TfSubTeam, TfEvent FROM TeamFinals WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . ") "
									. "as Sqy ON TcCoId=TfTeam AND TcSubTeam=TfSubTeam AND TcEvent=TfEvent "
									. "WHERE "
									. "TcFinEvent=1 AND TcTournament=" . StrSafe_DB($_SESSION['TourId']) . "  AND TcEvent=" . $ev;
								;
								$RsIns=safe_w_sql($Insert);
							}
						}

						//if ($Rs)
						{
							$Update
								= "UPDATE Events SET "
								. "EvShootOff='1' "
								. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode =" . $ev . " AND EvTeamEvent='1' ";
							$RsUp=safe_w_sql($Update);
						}

						set_qual_session_flags();
						//exit;
					}
				}
			}
		}
	}

	include('Common/Templates/head.php');
?>

<table class="Tabella">
	<tr><th class="Title"><?php print get_text('MenuLM_LastShootoff'); ?></th></tr>
</table>

<?php $error=false;?>

<?php
	if (!$error)
	{
		$Select
			= "SELECT EvCode,EvTeamEvent,EvTournament,EvEventName,EvShootOff "
			. "FROM Events "
			. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " " . $QueryFilter . " AND EvTeamEvent='1' "
			. "ORDER BY EvProgr ASC ";
		$RsEv=safe_r_sql($Select);

		if (safe_num_rows($RsEv)>0)
		{
			print '<form name="frm" method="post" action="' . $_SERVER['PHP_SELF'] . '">' . "\n";

				if (isset($_REQUEST['EventCode']))
					print '<input type="hidden" name="EventCode" value="' . $_REQUEST['EventCode'] . '">' . "\n";

				while ($MyRowEv=safe_fetch($RsEv))
				{
				// se il flag di shootoff (quello della seconda fase elim)è a 0 devo azzerare la colonna CTRank dell'evento
				// Uso Il FinEvent a 1 per decidere gli eventi della finale
					if ($MyRowEv->EvShootOff==0)
					{
						$Update
							= "UPDATE "
								. "CasTeam "
							. "SET "
								. "CaRank=0 "
							. "WHERE "
								. "CaTournament=" . Strsafe_DB($_SESSION['TourId']) . " AND CaPhase=2 AND CaEventCode=" . StrSafe_DB($MyRowEv->EvCode) . " ";

						$RsUp=safe_w_sql($Update);
					}

				/*
				 * Faccio i gironi G (gruppo 3) e F (gruppo 4).
				 * Il vettore qui sotto contiene per ogni gruppo la rank da cui si parte
				 */
					$groups=array
					(
						1=>1,
						2=>1,
						3=>9,
						4=>13
					);

					foreach ($groups as $g => $startRank)
					{
						$Query
							= "SELECT "
								. "CaTeam,CaSubTeam,CaEventCode,CaMatchNo,CaRank,CaTiebreak, /*ClubTeam*/ "
								. "CoCode,CoName, /*Countries*/ "
								. "CGGroup, /*ClubTeamGrid*/ "
								. "SUM(CaSPoints) AS Points, SUM(CaSSetScore) AS SetScore,SUM(CaSScore) AS Score /*ClubTeamScore*/ "
							. "FROM "
								. "CasTeam "
							. "LEFT JOIN "
								. "Countries "
							. "ON CaTeam=CoId "
							. "LEFT JOIN "
								. "CasGrid "
							. "ON CaPhase=CGPhase AND (CaMatchNo=CGMatchNo1 OR CaMatchNo=CGMatchNo2) "
							. "LEFT JOIN "
								. "CasScore "
							. "ON CaTournament=CaSTournament AND CaPhase=CaSPhase AND CaMatchNo=CaSMatchNo AND CaEventCode=CaSEventCode AND CGRound=CaSRound "
							. "WHERE "
								. "CaTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CGGroup=" . $g . " AND CaPhase=2 AND CaEventCode=" .StrSafe_DB($MyRowEv->EvCode) . " "
							. "GROUP BY "
								. "CaPhase,CaEventCode,CGGroup,CaTeam,CaSubTeam "
							. "ORDER BY "
								. "CaEventCode ASC,CGGroup ASC,CaRank ASC,Points DESC, SUM(CaSScore) DESC ";


						//print $Query . '<br>';
						$Rs=safe_r_sql($Query);

					// Variabili per la gestione del ranking
						$MyRank = $startRank;
						$MyPos = $startRank-1;

					// Variabili che contengono i punti del precedente atleta per la gestione del rank
						$MyPointsOld = 0;
						$MyScoreOld = 0;
						$MySetScoreOld=0;

						print '<table class="Tabella">' . "\n";
							print '<tr><th class="Title" colspan="5">' . $MyRowEv->EvCode . ' - ' . $MyRowEv->EvEventName . '</th></tr>' . "\n";

							print '<tr><th colspan="5">' . get_text('Group#','Tournament',$g) . '</th></tr>' . "\n";
							print '<tr>';
								print '<th style="width: 10%;">' . get_text('Rank') . '</th>';
								print '<th style="width: 30%;">' . get_text('Country') . '</th>';
								print '<th>' . get_text('Points','Tournament') . '</th>';
								print '<th>' . get_text('Total') . '</th>';
								print '<th>' . get_text('TieArrows') . '</th>';
							print '</tr>' . "\n";

							while ($MyRow=safe_fetch($Rs))
							{
								++$MyPos;
								if (!($MyPointsOld==$MyRow->Points && $MyScoreOld==$MyRow->Score && $MySetScoreOld==$MyRow->SetScore))
									$MyRank=$MyPos;

								$code=$MyRowEv->EvCode . '_' . $MyRow->CaMatchNo;

								print '<tr>';
									print '<td>';
										print $MyRank  . '&nbsp;&nbsp;';
										print '<input type="text" maxlength="2" size="2" name="R_' . $code . '" value="' . ($MyRow->CaRank!=0 ? $MyRow->CaRank : $MyRank) . '" />';
									print '</td>';

									print '<td>' . $MyRow->CoCode . ' - ' . $MyRow->CoName . ($MyRow->CaSubTeam>1 ? ' (' . $MyRow->CaSubTeam . ')' : '') . '</td>';
									print '<td>' . $MyRow->Points . '</td>';
									print '<td>' . $MyRow->Score . '</td>';
									print '<td>';
										for ($i=0;$i<=9;++$i)
										{
											print '<input maxlength="3" size="2" name="T_' . $i  . '-' . $code . '" value="'. (strlen($MyRow->CaTiebreak)>=($i+1) ? DecodeFromLetter($MyRow->CaTiebreak[$i]) : '').'" />';
										}
									print '</td>';

								print '</tr>' . "\n";

								$MyPointsOld=$MyRow->Points;
								$MyScoreOld=$MyRow->Score;
								$MySetScoreOld=$MyRow->SetScore;
							}

						print '</table>' . "\n";
					}
				}

				print '<table class="Tabella">' . "\n";
					print '<tr><td class="Center" colspan="5"><input type="submit" name="submit" value="' . get_text('CmdOk') . '" /><input type="hidden" name="Command" value="OK" /></td></tr>' . "\n";
				print '</table>';

			print '</form>' . "\n";
		}
	}
?>

<?php include('Common/Templates/tail.php'); ?>