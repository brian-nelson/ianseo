<?php
/*
 * Utilizzao Events.EvE1ShootOf come flag x lo spareggio della prima fase
 */
	define('debug',false);	// settare a true per l'output di debug
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_Number.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Fun_CONI.local.inc.php');


	CheckTourSession(true);

	$destroy=(isset($_REQUEST['Destroy']) ? $_REQUEST['Destroy'] : 0);

	$error=false;

	$QueryFilter = '';
	if (isset($_REQUEST['EventCode']) && strlen($_REQUEST['EventCode'])>0)
	{
		$Events = explode('|',$_REQUEST['EventCode']);
		$ee='';
		foreach ($Events as $Value)
			$ee.=StrSafe_DB($Value) . ",";
		$QueryFilter = "AND EvCode IN (" . substr($ee,0,-1) . ") ";

	}

	$IdAffected = array();

	if (isset($_REQUEST['Ok']) && $_REQUEST['Ok']=='OK')
	{
		$Ties=array();
		foreach ($_REQUEST as $Key => $Value)
		{
			if (strpos($Key,'R_')===0)
			{
				if (!(is_numeric($Value) && $Value>=0))
				{
					$Value="0";
				}
				$ee=''; $cc=''; $ss='';
				list(,$ee,$cc,$ss)=explode('_',$Key);

				$MyUpQuery = "UPDATE Teams SET ";
				$MyUpQuery.= "TeRank=" . StrSafe_DB($Value) . " ";
				$MyUpQuery.= "WHERE TeCoId=" . StrSafe_DB($cc) . " AND TeSubTeam=" . StrSafe_DB($ss) . " AND TeEvent=" . StrSafe_DB($ee) . " ";
				$MyUpQuery.= "AND TeTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TeFinEvent=1 ";
				$RsUp=safe_w_sql($MyUpQuery);
				//print $MyUpQuery . "<br><br>";
				if (!$RsUp)
					$error=true;
	//echo safe_w_affected_rows();exit;
				if (safe_w_affected_rows()==1)
				{
					$IdAffected[$ee]= StrSafe_DB($ee);
				}
			}

			if (strpos($Key,'T_')===0)
			{
			/*
			 * In $tmp ci sarà la stringa T_[index] e in $kk la stringa [ee]_[tt]_[ss]
			 */
				list ($tmp,$kk)=explode('-',$Key);

			/*
			 * Ora tiro fuori l'index del tie
			 */
				list (,$index)=explode('_',$tmp);

				//print $Key . '-> ' . $index . ' ' . $kk . '<br>';
				if (!array_key_exists($kk,$Ties))
					$Ties[$kk]=str_pad('',15,' ');

				$v=GetLetterFromPrint($Value);

				$Ties[$kk]=substr_replace($Ties[$kk],$v,$index,1);
			}
		}
	//	exit;
		/*print '<pre>';
		print_r($IdAffected);
		print '</pre>';exit;*/

		if (count($Ties)>0)
		{
			foreach ($Ties as $Key=>$Value)
			{
				list($ee,$cc,$ss)=explode('_',$Key);
				// by default the QUalification SO arrows are the same than the ones in Elimination!
                $obj=getEventArrowsParams($ee,64, 1);
                $Decoded=array();
                $idx=0;
                while($TbString=substr($Value, $idx, $obj->so)) {
                    if($obj->so==1) {
	                    $Decoded[]=DecodeFromLetter($TbString);
                    } else {
	                    $Decoded[]=ValutaArrowString($TbString);
                    }
                    $idx+=$obj->so;
                }
				$Update
					= "UPDATE "
						. "Teams "
					. "SET "
						. "TeTieBreak=" . StrSafe_DB($Value) . " "
						. ", TeTieDecoded=" . StrSafe_DB(implode(',', $Decoded)) . " "
					. "WHERE "
						. "TeCoId=" . StrSafe_DB($cc) . " AND TeSubTeam=" . StrSafe_DB($ss) . " AND TeEvent=" . StrSafe_DB($ee) . " AND TeTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";

				$RsUp=safe_w_sql($Update);
			}
		}

		if (!$error)
		{
			if (count($IdAffected)>0)
			{

			// tiro fuori per ogni evento la fase da cui parte per stabilire quante sono le squadre che entrano in CasTeam
				$startPhases=array();

				$Query
					= "SELECT "
						. "EvCode,8 AS EvFinalFirstPhase "
					. "FROM "
						. "Events "
					. "WHERE "
						. "EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 "
						. "AND EvCode IN (" . implode(',',$IdAffected) . ") ";
				//print $Query . '<br>';
				$Rs=safe_r_sql($Query);
				if ($Rs && safe_num_rows($Rs)>0)
				{
					while ($myRow=safe_fetch($Rs))
					{
						$startPhases[$myRow->EvCode]=$myRow->EvFinalFirstPhase;
					}
				}

			// Distruggo la tabella CasTeam
				$Query
					= "DELETE FROM CasTeam "
					. "WHERE "
						. "(CaPhase=1 OR CaPhase=2) AND CaEventCode IN (" .  implode(',',$IdAffected) . ") AND CaTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
				//print $Query.'<br>';
				$Rs=safe_w_sql($Query);

			// Distruggo la tabella CasScore se ho scelto di distruggere gli score
				if ($destroy==1)
				{
					$Query
						= "DELETE FROM CasScore "
						. "WHERE "
							. "(CaSPhase=1 OR CaSPhase=2) AND CaSEventCode IN (" .  implode(',',$IdAffected) . ") AND CaSTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
					$Rs=safe_w_sql($Query);
				//print $Query.'<br>';exit;
				}

				$ScoreQuery='';
				$tuple=array();
				foreach ($IdAffected as $key => $ev)
				{

				// Ricreo la tabella CasTeam
					$Query
						= "INSERT INTO CasTeam (CaTournament,CaPhase,CaMatchNo,CaEventCode,CaTeam,CaSubTeam,CaRank,CaTiebreak) "
						. "SELECT "
							. "TeTournament,1,CRMMatchNo,TeEvent,TeCoId,TeSubTeam,0,'' "
						. "FROM "
							. "Teams "
							. "INNER JOIN CasRankMatch ON TeRank=CRMRank AND CRMEventPhase='" . (2*$startPhases[$key]) . "' "
						. "WHERE TeRank<=" . (2*$startPhases[$key]) . " AND TeEvent= " . $ev . " AND TeTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TeFinEvent=1 ";
					//print $Query.'<br/>';
					$Rs=safe_w_sql($Query);

					if (!$Rs)
					{
						$error=true;
					}
					else
					{
					// in base a quante ClubTeam ho scritto, aggiungo quelle che mi servono per arrivare a 2*$startPhases[$key]
						$written=safe_w_affected_rows();

					/*	$Query
							= "INSERT INTO ClubTeam (CTTournament,CTPhase,CTMatchNo,CTEventCode,CTPrimary,CTTeam,CTSubTeam,CTBonus,CTRank,CTTiebreak) ";

						$xuple=array();
						for ($i=$written+1;$i<=(2*$startPhases[$key]);++$i)
						{
							$xuple[]="(" . StrSafe_DB($_SESSION['TourId']) . ",1," . $i . "," . $ev . "," . $primary . ",0,0,0,0,'')";
						}
						$Query.="VALUES " . join(",",$xuple);
						$Rs=safe_w_sql($Query);*/

					/********** questa roba fa schifo al cazzo e andrebbe rifatta usando una tab temporanea!!!!!!!!  ****************/
						for ($i=$written+1;$i<=(2*$startPhases[$key]);++$i)
						{
							$Query
								= "INSERT INTO CasTeam (CaTournament,CaPhase,CaMatchNo,CaEventCode,CaTeam,CaSubTeam,CaRank,CaTiebreak) "
								. "SELECT "
									. StrSafe_DB($_SESSION['TourId']) . ",1,CRMMatchNo," . $ev . ",1,0,0,'' "
								. "FROM "
									. "CasRankMatch "
								. "WHERE "
									. "CRMEventPhase=" . (2*$startPhases[$key]) . " AND CRMRank=" . $i . " ";
							$Rs=safe_w_sql($Query);
							//print $Query.'<br/>';

						}
					/********************************************************************************************************************/
					// query per creare le righe degli score
						$ScoreQuery
							= "INSERT INTO CasScore "
							. "("
								. "CaSTournament,"
								. "CaSPhase,"
								. "CaSRound,"
								. "CaSMatchNo,"
								. "CaSEventCode,"
								. "CaSTarget,"
								. "CaSSetPoints,"
								. "CaSSetScore,"
								. "CaSScore,"
								. "CaSTie,"
								. "CaSArrowString,"
								. "CaSArrowPosition,"
								. "CaSTiebreak,"
								. "CaSTiePosition,"
								. "CaSPoints "
							. ") VALUES ";



						for ($r=1;$r<=MaxRound;++$r)
						{
							for ($m=1;$m<=16;++$m)
							{
								$tuple[]
									= "("
										. StrSafe_DB($_SESSION['TourId']) . ","
										. "1,"
										. $r . ","
										. $m . ","
										. $ev . ","
										. "'',"
										. "'',"
										. "0,"
										. "0,"
										. "0,"
										. "'',"
										. "'',"
										. "'',"
										. "'',"
										. "0 "
									. ")";


							// fase 2
								$tuple[]
									= "("
										. StrSafe_DB($_SESSION['TourId']) . ","
										. "2,"
										. $r . ","
										. $m . ","
										. $ev . ","
										. "'',"
										. "'',"
										. "0,"
										. "0,"
										. "0,"
										. "'',"
										. "'',"
										. "'',"
										. "'',"
										. "0 "
									. ")";

							}
						}
					}
				}

				$ScoreQuery
					.=join(',',$tuple)
					. "ON DUPLICATE KEY UPDATE "
						. "CaSScore=0,CaSTie=0,CaSArrowString='',CaSArrowPosition='',CaSTiebreak='',CaSTiePosition='',CaSSetPoints='',CaSSetScore=0,CaSPoints=0 ";
				//print $ScoreQuery.'<br>';//exit;
				$Rs=safe_w_sql($ScoreQuery);

				if (!$Rs)
				{
					$error=true;
				}
				else
				{
					$Update
						= "UPDATE Events SET "
						. "EvE1ShootOff='1' "
						. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " " . $QueryFilter . " AND EvTeamEvent='1' ";
					$RsUp=safe_w_sql($Update);

				}
			}
		}
		//exit;
	}

	include('Common/Templates/head.php');
?>

<table class="Tabella">
	<tr><th class="Title"><?php print get_text('MenuLM_ShootOf4Cas1'); ?></th></tr>
</table>

<?php $error=false;?>

<?php
	if (!$error)
	{
		$Select
			= "SELECT EvCode,EvTeamEvent,EvTournament,EvEventName,8 AS EvFinalFirstPhase,EvE1ShootOff "
			. "FROM Events "
			. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " " . $QueryFilter . " AND EvTeamEvent='1' "
			. "ORDER BY EvProgr ASC ";
		$RsEv=safe_r_sql($Select);
		//print $Select;
		// Variabile per gestire il cambio di Evento
		$MyEvent = "xx";

	// Variabili per la gestione del ranking
		$MyRank = 1;
		$MyPos = 1;

	// Variabili che contengono i punti del precedente atleta per la gestione del rank
		$MyScoreOld = 0;
		$MyTieOld = 0;
		$MyGoldOld = 0;
		$MyXNineOld = 0;

	// Riga da stampare
		$MyPrintRow=NULL;
		$MyStile="";
		$MyOldStile="";

		$MyEndScore=-1; // Punteggio di riferimento in base a finalistart
		$MyEndPos=-1;  // posizione di riferimento in base a finalistart

		if (safe_num_rows($RsEv)>0)
		{
			print '<form name="frm" method="post" action="' . $_SERVER['PHP_SELF'] . '">' . "\n";
				print '<input type="hidden" name="Destroy" value="' . $destroy . '" />';

				if (isset($_REQUEST['EventCode']))
					print '<input type="hidden" name="EventCode" value="' . $_REQUEST['EventCode'] . '">' . "\n";

				while ($MyRowEv=safe_fetch($RsEv))
				{
				// se il flag di shootoff (quello della prima fase elim)è a 0 devo azzerare la colonna TeRank dell'evento
				// Uso Il FinEvent a 1 per decidere gli eventi della finale
					if ($MyRowEv->EvE1ShootOff==0)
					{
						$Update
							= "UPDATE Teams INNER JOIN Events ON TeEvent=EvCode AND TeFinEvent='1'  AND TeTournament= " . StrSafe_DB($_SESSION['TourId']) ." "
							. "SET TeRank='0' "
							. "WHERE TeEvent=". StrSafe_DB($MyRowEv->EvCode) ."  AND TeTournament=" . StrSafe_DB($_SESSION['TourId']) ." ";
						$RsUp=safe_w_sql($Update);
					}

					$Select
						= "SELECT DISTINCT TeCoId,TeSubTeam,TeEvent,TeTournament,TeFinEvent,TeScore AS Score,TeGold AS Gold,TeXnine AS XNine,TeTie,TeTieBreak,TeRank AS `Rank`,TeTieBreak,	/* Teams */ "
						. "CoCode,CoName, /* Countries */ "
						. "sqY.QuantiPoss as NumGialli,sqR.QuantiPoss as NumRossi "
						. "FROM Teams INNER JOIN Countries ON TeCoId=CoId AND TeFinEvent='1' AND TeTournament=CoTournament "
						. "INNER JOIN("
						. "SELECT Count( * ) AS QuantiPoss, EvCode AS SubCode, TeScore AS Score, TeGold AS Gold, TeXnine AS XNine "
						. "FROM Teams INNER JOIN Events ON TeEvent = EvCode AND TeTournament = EvTournament AND EvTeamEvent = '1' "
						. "WHERE TeTournament = " . StrSafe_DB($_SESSION['TourId']) ." "
						. "GROUP BY TeScore, EvCode, TeGold, TeXnine "
						. ") AS sqY ON sqY.Score=TeScore AND sqY.Gold=TeGold AND sqY.XNine=TeXnine AND sqY.SubCode=TeEvent "
						. "INNER JOIN("
						. "SELECT Count( * ) AS QuantiPoss, EvCode AS SubCode, TeScore AS Score "
						. "FROM Teams "
						. "INNER JOIN Events ON TeEvent = EvCode AND TeTournament = EvTournament AND EvTeamEvent ='1' "
						. "WHERE TeTournament = " . StrSafe_DB($_SESSION['TourId']) ." "
						. "GROUP BY TeScore, EvCode "
						. ") AS sqR ON sqR.Score = TeScore AND sqR.SubCode=TeEvent "
						. "WHERE TeEvent=" . StrSafe_DB($MyRowEv->EvCode) . " AND TeScore!=0 AND TeTournament=" . StrSafe_DB($_SESSION['TourId']) ." "
						. "ORDER BY TeScore DESC,TeRank ASC,TeGold DESC,TeXnine DESC,CoName ASC ";

					$Rs=safe_r_sql($Select);
					//print $Select . '<br>';exit();

					if (safe_num_rows($Rs)>0)
					{
						$MyRank = 1;
						$MyPos = 1;

						$MyPrintRow=NULL;
						$MyStile="";
						$MyOldStile="";

						$MyEndScore=-1;
						$MyEndPos=-1;  	// ultima posizione buona

						$MyRow=safe_fetch($Rs);
						$MyPrintRow=$MyRow;	// memorizzo qui la prima riga del rs
						$MyEndPos=2*$MyRowEv->EvFinalFirstPhase;

						if(safe_num_rows($Rs)>($MyEndPos))
						{
							//safe_data_seek($Rs,2*$MyEndPos-1);
							safe_data_seek($Rs,$MyEndPos-1);
							$MyRow=safe_fetch($Rs);
							$MyEndScore=$MyRow->Score;
							$MyRow=safe_fetch($Rs);
						//Controllo se c'� parimerito per entrare
							if ($MyEndScore!=$MyRow->Score) {
								$MyEndScore *= -1;
							}
						}
						else
						{
							safe_data_seek($Rs,safe_num_rows($Rs)-1);
							$MyRow = safe_fetch($Rs);
							$MyEndScore = (-1 * $MyRow->Score);
						}

						$Colonne = 7;

						print '<table class="Tabella">' . "\n";
							print '<tr class="Divider"><td colspan="' . $Colonne . '"></td></tr>' . "\n";
							print '<tr>';
							print '<th class="Title" colspan="' . $Colonne . '">' . get_text($MyRowEv->EvEventName,'','',true) . ' (' . $MyRowEv->EvCode . ')</th>';
							print '</tr>';
							print '<tr>';
							print '<th width="5%">' . get_text('Rank') . '</th>';
							print '<th width="40%" colspan="2">' . get_text('Country') . '</th>';
							print '<th width="10%">' . get_text('Total') . '</th>';
							print '<th width="10%">G</th>';
							print '<th width="10%">X</th>';
							print '<th>' . get_text('TieArrows') . '</th>';
							print '</tr>' . "\n";

							safe_data_seek($Rs,1);
							$MyRow=safe_fetch($Rs);

							while ($MyPrintRow && $MyPrintRow->Score >= abs($MyEndScore))
							{
								if ($MyRow && $MyRow->Score != $MyPrintRow->Score)	// stile normale
								{
									$MyStile='';
								}
								else	// i punti sono uguali
								{
									if ($MyRow && $MyRow->Score!=$MyEndScore) // non sono a cavallo dell'ultimo posto
									{
									// ho parit� assoluta
										if ($MyRow && $MyRow->Gold==$MyPrintRow->Gold && $MyRow->XNine==$MyPrintRow->XNine /*&& $MyRow->Rank==$MyPrintRow->Rank*/)
										{
											$MyStile='warning';
											$MyOldStile=$MyStile;
										}
										else	// non ho parit� assoluta
										{
											$MyStile='';
										}
									}
									else if($MyRow && $MyRow->Rank==$MyPrintRow->Rank)	// sono a cavallo dell'ultimo posto e non ho gestito i "tie"
									{
										$MyStile='error';
										$MyOldStile=$MyStile;
									}
									else
									{
										$MyStile='';
									}
								}

								print '<tr class="' . $MyOldStile . '">';
									print '<th class="Title">' . $MyRank . '&nbsp;';

										$EndRank = $MyRank;
										if ($MyOldStile=='warning')
											$EndRank = $EndRank+$MyPrintRow->NumGialli-1;
										elseif ($MyOldStile=='error')
											$EndRank = $EndRank+$MyPrintRow->NumRossi-1;

										print '<select name="R_' . $MyRowEv->EvCode . '_' . $MyPrintRow->TeCoId . '_' . $MyPrintRow->TeSubTeam . '">' . "\n";
										for ($i=$MyRank;$i<=$EndRank;++$i)
											print '<option value="' . $i . '"' . ($i==$MyPrintRow->Rank ? ' selected' : '') . '>' . $i . '</option>' . "\n";
										print '</select>' . "\n";

									print '</th>';

									print '<td width="10%" class="Center">' . $MyPrintRow->CoCode . '</td>';
									print '<td width="30%">' . ($MyPrintRow->CoName!='' ? $MyPrintRow->CoName . (intval($MyPrintRow->TeSubTeam)<=1 ? '' : ' (' . $MyPrintRow->TeSubTeam .')') : '&nbsp') . '</td>';
									print '<td class="Center">' . $MyPrintRow->Score . '</td>';
									print '<td class="Center">' . $MyPrintRow->Gold  . '</td>';
									print '<td class="Center">' . $MyPrintRow->XNine  . '</td>';

									print '<td>';
									/*
									 * Separo l'indice del tie con un - perchè poi in scrittura dovrò usare il blocco dopo l'indice come chiave e per
									 * non spezzare e ricostruire più volte uso un separatore diverso.
									 */
										for ($i=0;$i<9;++$i)
										{
											print '<input type="text" maxlength="2" size="1" name="T_' . $i . '-' . $MyRowEv->EvCode . '_' . $MyPrintRow->TeCoId . '_' . $MyPrintRow->TeSubTeam . '" value="' . (strlen($MyPrintRow->TeTieBreak)>=($i+1) ? DecodeFromLetter($MyPrintRow->TeTieBreak[$i]) : ''). '">&nbsp;';
											/*if (($i+1)%3==0)
												print '<br/>';*/
										}
									print '</td>';
								print '</tr>' . "\n";

								$MyPrintRow=$MyRow;
								$MyOldStile=$MyStile;
								$MyPos++;
								if($MyStile=='') {
									$MyRank=$MyPos;
								}
								$MyRow=safe_fetch($Rs);
							}
						print '<tr><td class="Center" colspan="' . $Colonne . '"><input type="hidden" name="Ok" value="OK"><input type="submit" value="' . get_text('CmdOk') . '"></td></tr>' . "\n";
						print '</table>' . "\n";
						print '<br>';

					}
				}

			print '</form>' . "\n";
		}
	}
?>

<?php include('Common/Templates/tail.php'); ?>
