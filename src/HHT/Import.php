<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');
	require_once('Final/Fun_MatchTotal.inc.php');
	require_once('Fun_HHT.local.inc.php');
	require_once('Common/Lib/Obj_RankFactory.php');
	if(!empty($_SESSION['OvrHost'])) require_once('Modules/OvrExport/OvrExportFunctions.php');

	$ComboHHT=ComboHHT();
	$DBSesParam=0;
	$DbSeqParam=0;
	$DbDistParam=0;

	if(isset($_REQUEST["x_Hht"]) && $_REQUEST["x_Hht"]!=-1)
	{
		$Select = "Select HsPhase, HsSequence, HsDistance FROM HhtSetup WHERE HsTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND HsId=" . StrSafe_DB($_REQUEST['x_Hht']);
		$rs = safe_w_sql($Select);
		$MyRow = safe_fetch($rs);
		$DBSesParam=$MyRow->HsPhase;
		$DbSeqParam = str_pad($MyRow->HsSequence,12,' ');
		$DbDistParam=$MyRow->HsDistance;
	}

	$xSession= (isset($_REQUEST['x_Session']) ? $_REQUEST['x_Session'] : $DBSesParam);
	$FirstArr = (isset($_REQUEST['FirstArr']) ? intval($_REQUEST['FirstArr']) : intval(substr($DbSeqParam,0,2)));
	$LastArr= (isset($_REQUEST['LastArr']) ? intval($_REQUEST['LastArr']) : intval(substr($DbSeqParam,2,2)));
	$Volee= (isset($_REQUEST['Volee']) ? intval($_REQUEST['Volee']) : intval(substr($DbSeqParam,4,2)));
	$Dist=(isset($_REQUEST['Dist']) ? $_REQUEST['Dist'] : $DbDistParam);

	$Command=(isset($_REQUEST['Command']) ? $_REQUEST['Command'] : null);
	$Msg='';

	if (!is_null($Command))
	{
		if ($Command=='OK')
		{
			if (($Dist>0 && is_numeric($xSession) ||
				($Dist==0 && !is_numeric($xSession))))
			{
			// query per l'import
			// come vengono calcolati ori e x
				$G='';
				$X='';

				$team=(!is_numeric($xSession) ? substr($xSession,0,1) : '0');
				$when=(!is_numeric($xSession) ? substr($xSession,1) : '0000-00-00 00:00:00');

				if (is_numeric($xSession))	// qualificazioni
				{
					$Sql
						= "SELECT ToGoldsChars AS TtGolds,ToXNineChars AS TtXNine
						FROM Tournament
						WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
					$Rs=safe_r_sql($Sql);

					if (safe_num_rows($Rs)==1)
					{
						$myRow=safe_fetch($Rs);
						$G=$myRow->TtGolds;
						$X=$myRow->TtXNine;
					}

					$Sql
						= "SELECT HhtData.*, QuD" . $Dist . "Arrowstring AS ArrowString "
						. "FROM HhtData "
						. "INNER JOIN Entries ON HdEnId=EnId AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
						. "INNER JOIN Qualifications ON EnId=QuId "
						. "WHERE LEFT(HdTargetNo,1)=" . StrSafe_DB($xSession) . " AND HdDistance=" . StrSafe_DB($Dist) . " AND HdArrowStart=" . StrSafe_DB($FirstArr). " AND HdTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND HdHhtId=". StrSafe_DB($_REQUEST['x_Hht']) . " "
						. "ORDER BY HdTargetNo ASC ";
					$Rs=safe_r_sql($Sql);
					if (safe_num_rows($Rs)>0 && !IsBlocked(BIT_BLOCK_QUAL))
					{
						while ($myRow=safe_fetch($Rs))
						{
							$Msg.=get_text('Importing','HTT',substr($myRow->HdTargetNo,1)) . '...';
							$Start=$myRow->HdArrowStart-1;
							$Len=($myRow->HdArrowEnd-$myRow->HdArrowStart)+1;
							$ArrowString=substr_replace(str_pad($myRow->ArrowString,90," ",STR_PAD_RIGHT),substr($myRow->HdArrowString,0,$Len),$Start,$Len);
							//print $myRow->HdTargetNo . ' ...' . $ArrowString . '...<br><br>';

						// Ora posso calcolare i punti della distanza
							$Score=0;
							$Gold=0;
							$XNine=0;
							$Update="";
							list($Score,$Gold,$XNine)=ValutaArrowStringGX($ArrowString,$G,$X);
							// Ora posso aggiornare la riga di Qualifications
							$Update
								= "UPDATE Qualifications SET "
								. "QuD" . $Dist . "Score=" . StrSafe_DB($Score) . ","
								. "QuD" . $Dist . "Gold=" . StrSafe_DB($Gold) . ","
								. "QuD" . $Dist . "Xnine=" . StrSafe_DB($XNine) . ","
								. "QuD" . $Dist . "ArrowString=" . StrSafe_DB($ArrowString) . ","
								. "QuD" . $Dist . "Hits=GREATEST(LENGTH(RTRIM(QuD" . $Dist . "ArrowString))," . StrSafe_DB($LastArr) . "),"
								. "QuScore=QuD1Score+QuD2Score+QuD3Score+QuD4Score+QuD5Score+QuD6Score+QuD7Score+QuD8Score,"
								. "QuGold=QuD1Gold+QuD2Gold+QuD3Gold+QuD4Gold+QuD5Gold+QuD6Gold+QuD7Gold+QuD8Gold,"
								. "QuXnine=QuD1Xnine+QuD2Xnine+QuD3Xnine+QuD4Xnine+QuD5Xnine+QuD6Xnine+QuD7Xnine+QuD8Xnine, "
								. "QuHits=QuD1Hits+QuD2Hits+QuD3Hits+QuD4Hits+QuD5Hits+QuD6Hits+QuD7Hits+QuD8Hits, "
								. "QuTimestamp=" . StrSafe_DB(date('Y-m-d H:i:s')) . " "
								. "WHERE QuId=" . StrSafe_DB($myRow->HdEnId) . " ";
							$RsUp = safe_w_sql($Update);

							useArrowsSnapshot($xSession, $Dist, substr($myRow->HdTargetNo,1,-1), substr($myRow->HdTargetNo,1,-1), $LastArr);
							recalSnapshot($xSession, $Dist, substr($myRow->HdTargetNo,1,-1), substr($myRow->HdTargetNo,1,-1));
							//print $Update . '<br><br>';
//							if()

							if ($RsUp)
								$Msg.=get_text('CmdOk') . '<br>';
							else
								$Msg.='<br>';
						}
						// rank distanza
						CalcQualRank($Dist,'%');
						// rank totale
						CalcQualRank(0,'%');
						// rank abs sulla distanza
						CalcRank($Dist);
						// rank abs totale
						CalcRank(0);
						// squadre
						MakeTeams(NULL, NULL);
						MakeTeamsAbs(NULL,null,null);
						$Msg .= get_text('CmdImport','HTT') . ": " . get_text('CmdOk');
					}
					if(!empty($_SESSION['OvrHost'])) OvrExport($xSession, $Dist, $LastArr);
				}
				else	//Finali
				{
					$Sql
						= "SELECT HhtData.* "
						. "FROM HhtData "
						. ($team==0 ?
							"INNER JOIN Finals ON HdEvent=FinEvent AND HdMatchNo=FinMatchNo AND HdTournament=FinTournament AND HdTeamEvent=0 " :
							"INNER JOIN TeamFinals ON HdEvent=TfEvent AND HdMatchNo=TfMatchNo AND HdTournament=TfTournament AND HdTeamEvent=1 " )
						. "WHERE HdFinScheduling=" . StrSafe_DB($when) . " AND HdTeamEvent=" . StrSafe_DB($team) . " AND HdArrowStart=" .  StrSafe_DB($FirstArr). " AND HdTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND HdHhtId=". StrSafe_DB($_REQUEST['x_Hht']) . " "
						. "ORDER BY HdTargetNo ASC ";
					$Rs=safe_r_sql($Sql);
					if (safe_num_rows($Rs)>0 && !IsBlocked(($team==0 ? BIT_BLOCK_IND : BIT_BLOCK_TEAM)))
					{
						while ($myRow=safe_fetch($Rs))
						{
							UpdateArrowString($myRow->HdMatchNo, $myRow->HdEvent, $myRow->HdTeamEvent, $myRow->HdArrowString, $myRow->HdArrowStart, $myRow->HdArrowEnd);
							$Msg.=get_text('Importing','HTT',substr($myRow->HdTargetNo,0)) . '...' . get_text('CmdOk') . '<br>';
						}
					}
				}
			}
			else
				$Msg='<strong>' . get_text('SetDistanceError','HTT') . '</strong>';
		}

		//exit;
	}

	$PAGE_TITLE=get_text('CmdImport','HTT');

	include('Common/Templates/head.php');
?>
<form name="FrmParam" method="POST" action="">
	<table class="Tabella" >
		<tr><th class="Title" colspan="5"><?php echo get_text('CmdImport','HTT') ?></th></tr>
		<tr class="Divider"><td colspan="5"></td></tr>

		<?php
		if(isset($_REQUEST["x_Hht"]) && $_REQUEST["x_Hht"]!=-1)
		{
		?>
		<tr>
			<th width="20%"><?php print get_text('Terminal','HTT');?></th>
			<td colspan="4"><?php print $ComboHHT;?></td>
		</tr>
		<tr>
			<th width="20%"><?php print get_text('Session');?></th>
			<th width="20%"><?php print get_text('FirstArrow','HTT');?></th>
			<th width="20%"><?php print get_text('LastArrow','HTT');?></th>
			<th width="20%"><?php print get_text('End (volee)');?></th>
			<th width="20%"><?php print get_text('Distance','HTT');?></th>
		</tr>
		<tr>
			<td class="Center Bold"><input type="hidden" name="x_Session" value="<?php print $xSession; ?>"><?php print $xSession; ?></td>
			<td class="Center Bold"><input type="hidden" name="FirstArr" value="<?php print $FirstArr; ?>"><?php print $FirstArr; ?></td>
			<td class="Center Bold"><input type="hidden" name="LastArr" value="<?php print $LastArr; ?>"><?php print $LastArr; ?></td>
			<td class="Center Bold"><input type="hidden" name="Volee" value="<?php print $Volee; ?>"><?php print $Volee; ?></td>
			<td class="Center Bold"><input type="hidden" name="Dist" value="<?php print $Dist; ?>"><?php print $Dist; ?></td>
		</tr>
		<tr>
			<td colspan="5" class="Center">
				<input type="hidden" name="Command" value=""/>
				<input type="submit" value="<?php print get_text('CmdOk'); ?>" onclick="document.FrmParam.Command.value='OK'"/>
			</td>
		</tr>
		<?php
		}
		else
		{
		?>
		<tr>
			<th width="20%"><?php print get_text('Terminal','HTT');?></th>
			<td colspan="4"><?php print $ComboHHT;?>&nbsp;&nbsp;&nbsp;<input type="submit" value="<?php print get_text('CmdOk');?>"></td>
		</tr>
		<?php
		}
		?>

	</table>
	<br/>
	<?php
		if ($Msg!='')
		{
	?>
			<table class="Tabella">
				<tr><th><?php print get_text('Report','HTT'); ?></th></tr>
				<tr><td><?php print $Msg; ?></td></tr>
			</table>
	<?php
		}
	?>
</form>
<?php
	include('Common/Templates/tail.php');
?>
