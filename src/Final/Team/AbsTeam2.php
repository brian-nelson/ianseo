<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_Number.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Lib/Obj_RankFactory.php');
	require_once('Final/Fun_ChangePhase.inc.php');

	CheckTourSession(true);

	$Error=false;

	$EventList=array();

	$Events=array();
	if (!empty($_REQUEST['EventCode']))
	{
		$Events = explode('|',$_REQUEST['EventCode']);
	}
	//print_r($Events);exit;
	$rank=Obj_RankFactory::create('AbsTeam',array('events'=>$Events,'components'=>false));

	$IdAffected = array();
	$NotResolvedMsg=array();

	// scrivo
	if (isset($_REQUEST['Ok']) && $_REQUEST['Ok']=='OK' && !IsBlocked(BIT_BLOCK_TEAM))
	{
		$Ties=array();
		$NotResolved=array();

	// rank
		foreach($_REQUEST['R'] as $Event => $CoIds)
		{
			$q=safe_r_sql("select EvFinalFirstPhase, EvMatchMode from Events where EvCode='$Event' and EvTeamEvent='1' and EvTournament='{$_SESSION['TourId']}'");
			$r=safe_fetch($q);
			$MaxRank=$r->EvFinalFirstPhase*2;
			if($r->EvFinalFirstPhase==24 or $r->EvFinalFirstPhase==48) $MaxRank+=8; // salva i primi 8
			$NotResolved[$Event]=false;

			asort($CoIds);

		// controlla che tutti gli spareggi siano stati fatti
			$TrueRank=1;
			foreach($CoIds as $CoId => $AssignedRank)
			{
				if($AssignedRank!=$TrueRank && $AssignedRank<=$MaxRank)
				{

					$NotResolved[$Event]=true;
				}
				$TrueRank++;
			}

		// assegna le rank SOLO se tutto è a posto
			if(!$NotResolved[$Event])
			{
				foreach($CoIds as $CoId => $AssignedRank)
				{
					list($id,$subteam)=explode('_',$CoId);
					$x=$rank->setRow(array(
						array(
							'team' => $id,
							'subteam' => $subteam,
							'event' => $Event,
							'rank' => $AssignedRank
						)
					));

					if ($x==1)
					{
						$IdAffected[]= strsafe_db($CoId);
					}

				}
			}
			else
			{
				$NotResolvedMsg[]=$Event;
			}
		}

	// tie
		foreach ($_REQUEST['T'] as $EventKey => $Event)
		{
			foreach ($Event as $id => $TieArrows)
			{
				foreach($TieArrows as $index => $Value)
				{
					if (!array_key_exists($EventKey.'_'.$id,$Ties))
						$Ties[$EventKey.'_'.$id]=str_pad('',3,' ');

					$v=GetLetterFromPrint($Value);

					$Ties[$EventKey.'_'.$id]=substr_replace($Ties[$EventKey.'_'.$id],$v,$index,1);
				}
			}
		}

		if (count($Ties)>0)
		{
			foreach ($Ties as $Key=>$Value)
			{
				list($ev,$id,$subteam)=explode('_',$Key);

				$x=$rank->setRow(array(
					array(
						'team' => $id,
						'subteam'=>$subteam,
						'event' => $ev,
						'tiebreak' => $Value
					)
				));
			}
		}

		if (count($IdAffected)>0)
		{


//			print '<pre>';
//			print_r($IdAffected);
//			print '</pre>';

		// distruggo le griglie
			$Select
				= "SELECT DISTINCT EvCode "
				. "FROM "
					. "Events INNER JOIN Teams ON EvCode=TeEvent AND EvTournament=TeTournament AND EvTeamEvent=1 "
				. "WHERE "
					. "CONCAT(TeCoId,'_',TeSubTeam) IN(" . implode(',',$IdAffected). ") AND TeTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TeFinEvent=1 "
					. (count($Events)>0 ? " AND EvCode IN('" . implode("','",$Events). "')" : ""). " "
//					. ($VetoEvents?" AND EvCode not in ('".implode("','", $VetoEvents)."')":'')
			;
				//print $Select;Exit;
			$Rs=safe_r_sql($Select);

			if (safe_num_rows($Rs)>0)
			{
				$Ev2Delete = array();
				while ($Row=safe_fetch($Rs))
					$Ev2Delete[]=StrSafe_DB($Row->EvCode);

				$Delete
					= "DELETE FROM TeamFinals "
					. "WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfEvent IN (" . implode(',',$Ev2Delete) . ") ";
					//print $Delete;exit;
				$Rs=safe_w_sql($Delete);


				/*print $Delete . '<br>';
				exit;*/

				// Distruggo le righe di TeamFinComponent basandomi su $IdAffected
				$Delete
					= "DELETE FROM TeamFinComponent "
					. "WHERE CONCAT(TfcCoId,'_',TfcSubTeam) IN(" . implode(',',$IdAffected) . ") AND TfcEvent IN(" . implode(',',$Ev2Delete). ") AND TfcTournament=" . StrSafe_DB($_SESSION['TourId']);
					//print $Delete;exit;
				$Rs=safe_w_sql($Delete);

			// ricreo la grid
				$Insert
					= "INSERT INTO TeamFinals (TfEvent,TfMatchNo,TfTournament,TfDateTime) "
					. "SELECT EvCode,GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . "," . StrSafe_DB(date('Y-m-d H:i:s')) . " "
					. "FROM Events "
					. "INNER JOIN Grids ON GrPhase<=EvFinalFirstPhase AND EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
					. "WHERE EvCode IN (" . implode(',',$Ev2Delete) . ")";
				$RsIns=safe_w_sql($Insert);
			}

			// importo i nomi nelle griglie
			$VetoEvents=array();
//			print'<pre>';
//			print_r($NotResolved);
//			print'</pre>';exit;
			foreach($NotResolved as $Event => $veto)
			{
				if($veto) $VetoEvents[]=$Event;
			}
			sort($VetoEvents);

			$Select
				= "SELECT "
					. "TeCoId,TeSubTeam,TeRank, TeEvent,GrMatchNo,EvFinalFirstPhase "
				. "FROM "
					. "Teams INNER JOIN Events ON TeTournament=EvTournament AND TeEvent=EvCode AND EvTeamEvent=1 "
					. "INNER JOIN Grids ON IF(EvFinalFirstPhase=24,32, IF(EvFinalFirstPhase=48,64,EvFinalFirstPhase))=GrPhase AND TeRank=GrPosition "
				. "WHERE "
					. "TeTournament=" . StrSafe_DB($_SESSION['TourId']) . " " . (count($Events)>0 ? " AND TeEvent IN('" . implode("','",$Events). "')" : ""). " AND TeFinEvent=1 "
					. ($VetoEvents?" AND EvCode not in ('".implode("','", $VetoEvents)."')":'')
				. "ORDER BY EvCode,TeRank ASC,GrMatchNo ASC ";
			;
			//print $Select;exit;
			$RsSel=safe_r_sql($Select);

			while ($MyRow=safe_fetch($RsSel))
			{
				if(!array_key_exists($MyRow->TeEvent, $EventList))
					$EventList[$MyRow->TeEvent]=($MyRow->EvFinalFirstPhase == 24 ? 32 : $MyRow->EvFinalFirstPhase);

				$Update
					= "UPDATE TeamFinals SET "
					. "TfTeam='" . $MyRow->TeCoId . "', "
					. "TfSubTeam='" . $MyRow->TeSubTeam . "', "
					. "TfDateTime='" . date('Y-m-d H:i:s') . "' "
					. "WHERE TfEvent='" . $MyRow->TeEvent . "' AND "
					. "TfMatchNo='" . $MyRow->GrMatchNo . "' AND "
					. "TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
				$RsUp=safe_w_sql($Update);
				//print $Update . '<br>';
			}

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
					. "TcFinEvent=1 AND TcTournament=" . StrSafe_DB($_SESSION['TourId']) . "  " . (count($Events)>0 ? " AND TcEvent IN('" . implode("','",$Events). "')" : ""). " "
					. ($VetoEvents?" AND TcEvent not in ('".implode("','", $VetoEvents)."')":'')
			;
			//print $Insert;exit;
			$RsIns=safe_w_sql($Insert);

		// setto a 1 i flags che dicono che ho fatto gli spareggi per gli eventi
			$Update
				= "UPDATE Events SET "
				. "EvShootOff='1' "
				. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='1' " . (count($Events)>0 ? " AND EvCode IN('" . implode("','",$Events). "')" : ""). " "
				. ($VetoEvents?" AND EvCode not in ('".implode("','", $VetoEvents)."')":'')
			;
			$RsUp=safe_w_sql($Update);
			set_qual_session_flags();

		// qui la rank finale
		// calcolo la finalrank di quelli che si son fermati alle qualifiche
			$coppie=array();
			$q="SELECT EvCode FROM Events WHERE EvTournament={$_SESSION['TourId']} AND EvTeamEvent=1 AND EvCode NOT IN ('" . implode(',',$VetoEvents). "')" . (count($Events)>0 ? " AND EvCode IN('" . implode("','",$Events). "') " : '');
			$r=safe_r_sql($q);
			while ($rr=safe_fetch($r))
			{
				$coppie[$rr->EvCode]= $rr->EvCode . "@-3";
			}
			/*foreach ($Events as $e)
			{
				$coppie[$e]= $e . "@-3";
			}*/
			//print_r($coppie);exit;
			Obj_RankFactory::create('FinalTeam',array('eventsC'=>$coppie))->calculate();

			foreach($EventList as $key=>$value)
			{
				move2NextPhaseTeam($value,$key,null);
			}
		}
		//exit;
	}

	$PAGE_TITLE=get_text('ShootOff4Final') . ' - ' . get_text('Team');

	include('Common/Templates/head.php');
?>
<table class="Tabella">
<TR><TH class="Title"><?php print get_text('ShootOff4Final') . ' - ' . get_text('Team');?></TH></TR>
<?php if (count($NotResolvedMsg)>0) { ?>
	<tr class="warning"><td><?php print get_text('NotAllShootoffResolved','Tournament',implode(', ',$NotResolvedMsg));?></td></tr>
<?php } ?>
</table>
<?php
	if (!$Error)
	{
		$rank->read();
		$data=$rank->getData();

		if(count($data['sections'])>0)
		{
			print '<form name="Frm" method="post" action="" onsubmit="return validShotoff()">' . "\n";
				if (isset($_REQUEST['EventCode']))
					print '<input type="hidden" name="EventCode" value="' . $_REQUEST['EventCode'] . '">' . "\n";

				$Colonne = 7;

				foreach ($data['sections'] as $section)
				{
					print '<table class="Tabella">' . "\n";
						print '<tr class="Divider"><td colspan="' . $Colonne . '"></td></tr>' . "\n";
						print '<tr><th class="Title" colspan="' . $Colonne . '">' . $section['meta']['descr']. ' (' . $section['meta']['event'] . ')</th></tr>';
						print '<tr>';
							print '<th width="5%">' . get_text('Rank') . '</th>';
							print '<th width="40%" colspan="2">' . get_text('Country') . '</th>';
							print '<th width="10%">' . get_text('Total') . '</th>';
							print '<th width="10%">G</th>';
							print '<th width="10%">X</th>';
							print '<th>' . get_text('TieArrows') . '</th>';
						print '</tr>' . "\n";

						foreach ($section['items'] as $item)
						{
						// fermo appena trovo una rank > di quelle che passano e una riga con so=0
							if ($item['rank']>$section['meta']['qualifiedNo'] && $item['so']==0)
								break;

							$style="";
							/*if ($item['ct']>1)		// ho qualche rank pari
							{
								if ($item['so']==0)		// ho un giallo
								{
									$style="warning";
								}
								else					// ho un rosso
								{
									$style="error";
								}
							}*/

							if ($item['so']==0)	// potrei avere un giallo
							{
								if ($item['ct']>1)		// ho un giallo
								{
									$style="warning";
								}
								else	// no pari
								{
									$style="";
								}
							}
							else	// rossi
							{
								$style="error";
							}

							print '<tr class="' . $style . '">';
								print '<th class="Title">';
									print $item['rank'] . '&nbsp;';

									$endRank = $item['rankBeforeSO']+$item['ct']-1;

									print '<select name="R['.$section['meta']['event'].'][' . $item['id'] . '_' . $item['subteam']. ']">' . "\n";
										for ($i=$item['rankBeforeSO'];$i<=$endRank;++$i)
											print '<option value="' . $i . '"' . ($i==$item['rank'] ? ' selected' : '') . '>' . $i . '</option>' . "\n";
									print '</select>' . "\n";
								print '</th>';

								print '<td width="10%" class="Center">' . $item['countryCode'] . '</td>';
								print '<td width="30%">' . ($item['countryName']!='' ? $item['countryName'] . (intval($item['subteam'])<=1 ? '' : ' (' . $item['subteam'] .')') : '&nbsp') . '</td>';
								print '<td class="Center">' . $item['score'] . '</td>';
								print '<td class="Center">' . $item['gold']  . '</td>';
								print '<td class="Center">' . $item['xnine']  . '</td>';
								print '<td>';
									for ($i=0;$i<9;++$i)
									{
										print '<input type="text" maxlength="2" size="1" name="T[' . $section['meta']['event']. '][' . $item['id'] .'_'.$item['subteam'] . '][' . $i . ']" value="' . (strlen($item['tiebreak'])>$i ? DecodeFromLetter($item['tiebreak'][$i]) : ''). '">&nbsp;';
									}
								print '</td>';
							print '</tr>';
						}
						print '<tr><td class="Center" colspan="' . $Colonne . '"><input type="hidden" name="Ok" value="OK"><input type="submit" value="' . get_text('CmdOk') . '"></td></tr>' . "\n";
					print '</table>' . "\n";
					print '<br>';


				}
			print '</form>';
		}
	}

	include('Common/Templates/tail.php');
?>