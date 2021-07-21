<?php
/*
 * Utilizzao Events.EvE2ShootOf come flag x lo spareggio della seconda fase
 */
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_Number.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Fun_CONI.local.inc.php');

	CheckTourSession(true);

	$destroy=(isset($_REQUEST['Destroy']) ? $_REQUEST['Destroy'] : 0);

	$event=(isset($_REQUEST['EventCode']) ? $_REQUEST['EventCode'] : null);
	if (is_null($event))
		exit;

	$destroy=(isset($_REQUEST['Destroy']) ? $_REQUEST['Destroy'] : 0);

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
		= "SELECT EvE1ShootOff  "
		. "FROM Events "
		. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (EvE1ShootOff='1') AND EvTeamEvent='1' AND EvCode=" . StrSafe_DB($event) . " ";
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
							. "CaTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CaPhase=1 AND CaMatchNo=" . StrSafe_DB($mm) . " "
								. "AND CaEventCode=" . StrSafe_DB($ee) . " ";
					$Rs=safe_w_sql($Query);

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
							. "CaTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CaPhase=1 AND CaMatchNo=" . StrSafe_DB($mm) . " "
								. "AND CaEventCode=" . StrSafe_DB($ee) . "  ";
					//print $Update . '<br><br>';
					$RsUp=safe_w_sql($Update);
				}
			}

			if (!$error)
			{
				/*print '<pre>';
				print_r($IdAffected);
				print '</pre>';exit;*/
				if (count($IdAffected)>0)
				{

					// tiro fuori per ogni evento la fase da cui parte per stabilire quante sono le squadre che entrano in ClubTeam
					$startPhases=array();

					$Query
						= "SELECT "
							. "EvCode,EvFinalFirstPhase "
						. "FROM "
							. "Events "
						. "WHERE "
							. "EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 "
							. "AND EvCode IN (" . implode(',',$IdAffected) . ") ";
					$Rs=safe_r_sql($Query);
					if ($Rs && safe_num_rows($Rs)>0)
					{
						while ($myRow=safe_fetch($Rs))
						{
							$startPhases[$myRow->EvCode]=$myRow->EvFinalFirstPhase;
						}
					}

				// Distruggo la tabella ClubTeam
					$Query
						= "DELETE FROM CasTeam "
						. "WHERE "
							. "(CaPhase=2 ) AND CaEventCode IN (" .  implode(',',$IdAffected) . ") AND CaTournament=" . StrSafe_DB($_SESSION['TourId']) . "  ";
					//print $Query.'<br>';
					$Rs=safe_w_sql($Query);

					foreach ($IdAffected as $key => $ev)
					{
					// fase 2
					/*
					 * La join su CasGrid mi serve per ottenere il gruppo che il tizio aveva nella fase 1.
					 * dato che indipendentemente dal round il matchno di una squadra non cambia,
					 * se prendo il gruppo dal round 1 sono ok. Una squadra è in un gruppo se il suo matchno è
					 * in CGMatchNo1 oppure in CGMatchNo2
					 */
						$Query
							= "INSERT INTO CasTeam (CaTournament,CaPhase,CaMatchNo,CaEventCode,CaTeam,CaSubTeam,CaRank,CaTiebreak ) "
							. "SELECT "
								. "CaTournament as Tournament, '2' as Phase, CaGMMatchNo as MatchNo, "
								. "CaEventCode as EventCode,  "
								. "CaTeam AS Team,	CaSubTeam AS SubTeam, "
								. " 0 AS `Rank`, '' AS Tiebreak "
							. "FROM "
								. "CasTeam "
								. "INNER JOIN "
									. "CasGrid "
								. "ON (CaMatchNo=CGMatchNo1 OR CaMatchNo=CGMatchNo2) AND CaPhase=CGPhase AND CGPhase=1 AND CGRound=1 "
								. "INNER JOIN "
									. "CasGroupMatch ON CaGMGroup=CGGroup AND CaRank=CaGRank "
							. "WHERE "
								. "CaTournament =" . StrSafe_DB($_SESSION['TourId']) . " AND CaEventCode=" . $ev . " AND  CaPhase='1'  ";
						//print $Query . '<br>';
						$Rs=safe_r_sql($Query);

						if ($Rs)
						{
							$Query
								= "UPDATE "
									. "CasScore "
								. "SET "
									. "CaSScore=0,CaSTie=0,CaSArrowString='',CaSArrowPosition='',CaSTiebreak='',CaSTiePosition='',CaSSetPoints='',	CaSSetScore=0,CaSPoints=0 "
								. "WHERE "
									. "CaSTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CaSPhase=2 AND CaSRound IN(1,2,3) AND CaSEventCode=" . $ev . "  ";
							$Rs=safe_r_sql($Query);

							if ($Rs)
							{
								$Update
									= "UPDATE Events SET "
									. "EvE2ShootOff='1' "
									. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode =" . $ev . " AND EvTeamEvent='1' ";
								$RsUp=safe_w_sql($Update);
							}
						}

					}
				//	exit;
				}
			}
		}
	}

	include('Common/Templates/head.php');
?>

<table class="Tabella">
	<tr><th class="Title"><?php print get_text('MenuLM_ShootOf4Cas2'); ?></th></tr>
</table>

<?php $error=false;?>

<?php
	if (!$error)
	{
		$Select
			= "SELECT EvCode,EvTeamEvent,EvTournament,EvEventName,EvE2ShootOff "
			. "FROM Events "
			. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " " . $QueryFilter . " AND EvTeamEvent='1' "
			. "ORDER BY EvProgr ASC ";
		$RsEv=safe_r_sql($Select);

		//$MyEndScore=-1; // Punteggio di riferimento in base a finalistart
		//$MyEndPos=-1;  // posizione di riferimento in base a finalistart

		if (safe_num_rows($RsEv)>0)
		{
			print '<form name="frm" method="post" action="' . $_SERVER['PHP_SELF'] . '">' . "\n";

				if (isset($_REQUEST['EventCode']))
					print '<input type="hidden" name="EventCode" value="' . $_REQUEST['EventCode'] . '">' . "\n";

				while ($MyRowEv=safe_fetch($RsEv))
				{
				// se il flag di shootoff (quello della seconda fase elim)è a 0 devo azzerare la colonna CTRank dell'evento
				// Uso Il FinEvent a 1 per decidere gli eventi della finale
					if ($MyRowEv->EvE2ShootOff==0)
					{
						$Update
							= "UPDATE "
								. "CasTeam "
							. "SET "
								. "CaRank=0 "
							. "WHERE "
								. "CaTournament=" . Strsafe_DB($_SESSION['TourId']) . " AND CaPhase=1 AND CaEventCode=" . StrSafe_DB($MyRowEv->EvCode) . " ";

						$RsUp=safe_w_sql($Update);
					}

				/*
				 * ATTENZIONE!
				 * E' importante avere le LEFT per tirar fuori le eventuali "code" create da AbsTeam2_1 affinchè anche quelle
				 * righe si ritrovino una rank!=0  così da avere la formula ((CTRank-1)*4 +CTGMGroup) che non si incarti mai.
				 */
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
							. "CaTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CaPhase=1 AND CaEventCode=" .StrSafe_DB($MyRowEv->EvCode) . " "
						. "GROUP BY "
							. "CaPhase,CaEventCode,CGGroup,CaTeam,CaSubTeam "
						. "ORDER BY "
							. "CaEventCode ASC,CGGroup ASC,CaRank ASC,SUM(CaSPoints) DESC, SUM(CaSScore) DESC ";

					$Rs=safe_r_sql($Query);
					//print $Query . '<br>';


					if ($Rs && safe_num_rows($Rs)>0)
					{
						$MyGroup = "xx";

					// Variabili per la gestione del ranking
						$MyRank = 1;
						$MyPos = 0;

					// Variabili che contengono i punti del precedente atleta per la gestione del rank
						$MyPointsOld = 0;
						$MyScoreOld = 0;
						$MySetScoreOld=0;

						print '<table class="Tabella">' . "\n";
							print '<tr><th class="Title" colspan="5">' . $MyRowEv->EvCode . ' - ' . $MyRowEv->EvEventName . '</th></tr>' . "\n";

							while ($MyRow=safe_fetch($Rs))
							{
								if ($MyGroup!=$MyRow->CGGroup)
								{
									// Variabili per la gestione del ranking
									$MyRank = 1;
									$MyPos = 0;

								// Variabili che contengono i punti del precedente atleta per la gestione del rank
									$MyPointsOld = 0;
									$MyScoreOld = 0;

									$MySetScoreOld=0;

									print '<tr><th colspan="5">' . get_text('Group#','Tournament',$MyRow->CGGroup) . '</th></tr>' . "\n";
									print '<tr>';
										print '<th>' . get_text('Rank') . '</th>';
										print '<th>' . get_text('Country') . '</th>';
										print '<th>' . get_text('Points','Tournament') . '</th>';
										print '<th>' . get_text('Total') . '</th>';
										print '<th>' . get_text('TieArrows') . '</th>';
									print '</tr>' . "\n";
								}

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
										for ($i=0;$i<9;++$i)
										{
											print '<input maxlength="3" size="2" name="T_' . $i  . '-' . $code . '" value="'. (strlen($MyRow->CaTiebreak)>=($i+1) ? DecodeFromLetter($MyRow->CaTiebreak[$i]) : '').'" />';
										}
									print '</td>';

								print '</tr>' . "\n";

								$MyGroup=$MyRow->CGGroup;

								$MyPointsOld=$MyRow->Points;
								$MyScoreOld=$MyRow->Score;
								$MySetScoreOld=$MyRow->SetScore;
							}

							print '<tr><td class="Center" colspan="5"><input type="submit" name="submit" value="' . get_text('CmdOk') . '" /><input type="hidden" name="Command" value="OK" /></td></tr>' . "\n";

						print '</table>' . "\n";
					}
				}

			print '</form>' . "\n";
		}
	}
?>

<?php include('Common/Templates/tail.php'); ?>
