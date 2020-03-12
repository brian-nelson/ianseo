<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('serial.php');
	require_once('Fun_HHT.local.inc.php');

	CheckTourSession(true);
	$ComboHHT=ComboHHT();

	$Msg='';
	$xSession=0;
	$FirstArr=0;
	$LastArr=0;
	$Volee=0;
	$Dist=0;


	if(isset($_REQUEST["x_Hht"]) && $_REQUEST["x_Hht"]!=-1)
	{
		$Select = "Select HsPhase, HsSequence, HsDistance FROM HhtSetup WHERE HsTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND HsId=" . StrSafe_DB($_REQUEST['x_Hht']);
		$rs = safe_w_sql($Select);
		$MyRow = safe_fetch($rs);
		$DbSeqParam = str_pad($MyRow->HsSequence,12,' ');
		$Dist=$MyRow->HsDistance;
		$xSession=$MyRow->HsPhase;

		$FirstArr=intval(substr($DbSeqParam,0,2));
		$LastArr=intval(substr($DbSeqParam,2,2));
		$Volee=intval(substr($DbSeqParam,4,2));
	}

	$_REQUEST['x_Session']=$xSession;	// per SelectTableHTT

	$Num2Download=0;
	$ResponseFromHHT=true;

	$HTTOK=array();
	$FromDB=array(); // will be the red ones
	$Status=array(); // will be orange (wrong arrow range)
	$Disable=array();	// no se final

	if (is_numeric($xSession))	//qual
	{
		$Sql  = "SELECT SUBSTRING(AtTargetNo,2," . (TargetNoPadding) . ") as ChiTarget, SUBSTRING(AtTargetNo," . (TargetNoPadding+2) . ",1) as ChiLetter ";
		$Sql .= "FROM AvailableTarget at ";
		$Sql .= "LEFT JOIN ";
		$Sql .= "(SELECT QuTargetNo FROM Qualifications AS q  ";
		$Sql .= "INNER JOIN Entries AS e ON q.QuId=e.EnId AND e.EnTournament= " . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1 AND EnStatus<6) as Sq ON at.AtTargetNo=Sq.QuTargetNo ";
		$Sql .= "WHERE AtTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND LEFT(AtTargetNo,1)='" . $xSession . "' AND Sq.QuTargetNo is NULL ";

		$Rs = safe_r_sql($Sql);
		if(safe_num_rows($Rs)>0)
		{
			while($myRow = safe_fetch($Rs)) {
				$Disable[] = intval($myRow->ChiTarget) . $myRow->ChiLetter;
				$Status[intval($myRow->ChiTarget) . $myRow->ChiLetter]='Disabled';
			}
		}

		$Sql = "SELECT QuTargetNo FROM Qualifications AS q  ";
		$Sql .= "INNER JOIN Entries AS e ON q.QuId=e.EnId AND e.EnTournament= " . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1 AND EnStatus<6 AND LEFT(QuTargetNo,1)='" . $xSession . "'";
		$Rs = safe_r_sql($Sql);
		$Num2Download = safe_num_rows($Rs);
	}
	else
	{
	// if matchmode==0 the code manages the thing directly so it doesn't matter here
		$Team=substr($xSession,0,1);
		$prefix=($Team?'Tf':'Fin');
		$table=($Team?'TeamFinals':'Finals');
		$Select
			= "SELECT DISTINCT"
				. " EvMatchMode,"
				. " CONCAT(FSScheduledDate,' ',FSScheduledTime),"
				. " FSTarget AS TargetNo,"
				. " GrPhase,"
				. " EvFinalAthTarget AS BitMask,"
				// The Match is ended if one of the 2 ties is set
				// or if the greatest score of the opponents is 1 more than the max number of ends to shoot
				// eg: 3 ends => 4 setpoints; 5 ends => 6 setpoints
				. " @arrows:=pow(2,log2(if(GrPhase=48,64,GrPhase))+1) & EvMatchArrowsNo as Set6Arrows,"
				. " (greatest(a.$prefix"."SetScore, b.$prefix"."SetScore) > if(@arrows>0, EvElimEnds, EvFinEnds) ) or a.$prefix"."Tie or b.$prefix"."Tie MatchEnded "
			. "FROM "
				. "FinSchedule "
				. "INNER JOIN Grids ON FSMatchNo=GrMatchNo "
				. "INNER JOIN Events ON FSEvent=EvCode AND FSTeamEvent=EvTeamEvent AND FSTournament=EvTournament "
				. "left join $table a on a.$prefix"."Tournament=FSTournament and a.$prefix"."Event=FSEvent and a.$prefix"."MatchNo=FSMatchNo "
				. "left join $table b on b.$prefix"."Tournament=FSTournament and b.$prefix"."Event=FSEvent and b.$prefix"."MatchNo=if(floor(a.$prefix"."MatchNo/2)=a.$prefix"."MatchNo/2,a.$prefix"."MatchNo+1,a.$prefix"."MatchNo-1) "
			. "WHERE "
				. "FSTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
				. "AND CONCAT(FSScheduledDate,' ',FSScheduledTime)=" . StrSafe_DB(substr($xSession,1)) . " "
				. "AND FSTeamEvent=" . StrSafe_DB(substr($xSession,0,1)) . " "
				. "AND FSTarget<>'' "
			. "ORDER BY "
				. "FSTarget ASC ";

		//print $Select;
		$Rs=safe_r_sql($Select);

		// Start with the complete set...
		$Num2Download = safe_num_rows($Rs);
		while($r=safe_fetch($Rs)) {
			$bit=($r->GrPhase>0 ? $r->GrPhase*2 : 1);
			$value=(($bit & $r->BitMask)==$bit ? 1 : 0);
			if($value) $Num2Download++;
			// If the match is finished, no need to get data from it
			if($r->MatchEnded and $r->EvMatchMode!='0') {
				$Num2Download--;
				if($value) $Num2Download--;

				for ($i='A';$i<=($value==0 ? 'A' : 'B');++$i) {
					$Disable[] = intval($r->TargetNo) . $i;
					$Status[intval($r->TargetNo) . $i]='Disabled';
				}
			}
		}
	}

	$Command=(isset($_REQUEST['Command']) ? $_REQUEST['Command'] : null);

	$HTTs=(isset($_REQUEST['HTT']) ? $_REQUEST['HTT'] : null);

	if (!is_null($Command))
	{
		if ($Command=='OK' || $Command=='STORE')
		{
			if (!is_null($HTTs) && is_array($HTTs))
			{
				// preparo i destinatari
					$Dests=array_values($HTTs);
					sort($Dests);	// per essere sicuro che se c'è lo zero allora sarà all'inizio
					if (array_search(0,$HTTs)!==false)
						array_shift($Dests);
			/*print '<pre>';
				print_r($Dests);
				print '</pre>';
				exit;*/
					$Frames=array();
					if($Command=='OK')
						$Frames=PrepareTxFrame($Dests,"");
					else
						$Frames=PrepareTxFrame($Dests,"sTORE");

					// Risposte
					$Results = array();
					if(count($Frames)>0)
					{
						$ResponseFromHHT=false;
						$Results=SendHTT(HhtParam($_REQUEST['x_Hht']),$Frames,true);
						if(!is_null($Results))
							$ResponseFromHHT=true;
						//print '<pre>';print_r($Results);print'</pre>';exit();
						if (count($Results)!=0)
						{
							foreach($Results as $v)
							{
							//Carico il vettore HTTOK
								if ($v["TargetNo"]!=-1) {
									$HTTOK[]=$v["TargetNo"];
									$Status[$v["TargetNo"]]='Green';
								}
							//Inizializzo le altre variabili
								$team = '0';
								$scheduling = '0000-00-00 00:00:00';
								$Dist = $v["Dist"];

							//Verifico il tipo di dato che torna
								$EnId=0;
								if ($v["FlagWhat"]==-1)	{//Qualifica
									$TargetNo = $v["Session"] . str_pad($v["TargetNo"],(TargetNoPadding+1),'0',STR_PAD_LEFT);
									$t=safe_r_sql("select EnId from Entries
											inner join Qualifications on QuId=EnId and QuTargetNo='{$TargetNo}'
											where EnTournament={$_SESSION['TourId']}
											");
									if($u=safe_fetch($t)) $EnId=$u->EnId;
								} else	// finali
								{
									$TargetType='';
									$TargetNo=str_pad($v["TargetNo"],(TargetNoPadding+1),'0',STR_PAD_LEFT);
									$team = $v["FlagWhat"];
									$scheduling=$v["Session"];
									$TargetType='';
									$query = "SELECT DISTINCT FSEvent, EvFinalTargetType FROM FinSchedule "
										. "inner join Events on EvTournament={$_SESSION['TourId']} and EvTeamEvent={$team} and EvCode=FsEvent "
										. "WHERE "
										. "CONCAT(FSScheduledDate,' ',FSScheduledTime)=" . StrSafe_DB($scheduling) . " AND "
										. "FSTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND "
										. "FSTeamEvent=" . $team . " AND "
										. "FSTarget=" .StrSafe_DB(substr($TargetNo,0,-1));
									$rs=safe_r_sql($query);

									if (safe_num_rows($rs)==1)
									{
										$myRow = safe_fetch($rs);
										$TargetType=$myRow->EvFinalTargetType;
									}
								}

								$ArrowString='';
								$str=$v["ArrowString"];
								for ($k=0;$k<strlen($str);++$k)
								{
									$value=$str[$k];
									if ($value=='0')
										$value='M';
									elseif ($value==chr(158))
										$value=10;

									$ChekedValue1=GetLetterFromPrint($value);
									if($v["FlagWhat"]==-1) { // Qualifications
										$ChekedValue2 = GetLetterFromPrint($value, $EnId, $Dist);
									} else {
										if($TargetType) {
											$ChekedValue2 = GetLetterFromPrint($value, 'T', $TargetType);
										} else {
											$ChekedValue2 = GetLetterFromPrint($value);
										}
									}
									$ArrowString .= $ChekedValue2;
									if($ChekedValue1!=$ChekedValue2) {
										// value is out of range so orange!
										$Status[$v["TargetNo"]]='Orange';
									}

									if ($value=='E' || ($value=='-' && $k<($v["LastArr"]-$v["FirstArr"]+1))) {
										$ArrowString .= ' ';
									}
								}


								$Sql = "INSERT INTO HhtData (HdEnId, HdTournament, HdTargetNo, HdRealTargetNo, HdLetter, HdDistance, HdFinScheduling, HdTeamEvent, HdArrowStart, HdArrowEnd, HdArrowString, HdHhtId, HdTimeStamp) "
									. "VALUES ($EnId, " . StrSafe_DB($_SESSION['TourId']) . ", '" . $TargetNo. "', '" . substr($TargetNo, 0, -1). "', '" . substr($TargetNo,-1). "', '" . $Dist . "', '" . $scheduling . "', '" .  $team ."', '" . $v["FirstArr"] . "', '" . $v["LastArr"] . "', '" . $ArrowString . "', " . StrSafe_DB($_REQUEST['x_Hht']) . ", '".date('Y-m-d H:i:s')."') "
									. "ON DUPLICATE KEY UPDATE HdTimeStamp='".date('Y-m-d H:i:s')."', HdEnId=$EnId, HdArrowEnd='" . $v["LastArr"] . "', HdArrowString='" . $ArrowString . "', HdHhtId=" . StrSafe_DB($_REQUEST['x_Hht']);
								safe_w_sql($Sql);

								//print $Sql . '<br><br>';
							}

							$fileName=	dirname(__FILE__)."/Files/{$_SESSION['TourCode']}-$xSession-$Dist-$Volee-$FirstArr-$LastArr.txt";
							@file_put_contents($fileName, serialize($Results));

							//exit();
						// imposto gli id
							$Sql="";

							if (is_numeric($xSession))
							{
								$Sql = "UPDATE HhtData "
									. "INNER JOIN Qualifications ON HdTargetNo=QuTargetNo "
									. "INNER JOIN Entries ON QuId=EnId AND HdTournament=EnTournament "
									. "SET HdEnId=EnId "
									. "WHERE HdTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND HdHhtId=". StrSafe_DB($_REQUEST['x_Hht']);
							}
							else
							{
								$Sql
									= "UPDATE "
										. "Grids "
									. "INNER JOIN "
										. "FinSchedule "
										. "ON GrMatchNo=FSMatchNo AND FSTeamEvent=" . $team . " AND FSTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
									. "INNER JOIN "
										. "Events "
										. "ON FSEvent=EvCode AND FSTeamEvent=" . $team . " AND FSTournament=EvTournament AND FSTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
									. "INNER JOIN "
										. "HhtData "
										. "ON FSTarget=HdRealTargetNo AND "
										. "IF(IF((IF(GrPhase>0, GrPhase*2, 1) & EvFinalAthTarget)=IF(GrPhase>0,GrPhase*2,1),1,0)=1 && MOD(GrMatchNo,2)=1,'B','A') = HdLetter AND "
										. "HdFinScheduling=CONCAT(FSScheduledDate,' ',FSScheduledTime) AND HdTeamEvent=FSTeamEvent AND HdTeamEvent=" . $team . " AND HdTournament=FSTournament AND HdHhtId=". StrSafe_DB($_REQUEST['x_Hht']) . " "
									. "SET "
										. "HdMatchNo=FSMatchNo,"
										. "HdEvent=FSEvent "
									. "WHERE "
										. "FSTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
/*

update HhtData
inner join FinSchedule on
	FSLetter=HdTargetNo
	AND HdFinScheduling=CONCAT(FSScheduledDate,' ',FSScheduledTime)
	AND HdTeamEvent=FSTeamEvent
	AND HdTournament=FSTournament

set HdMatchNo=FSMatchNo, HdEvent=FSEvent
where
	HdTournament=296
	AND HdTeamEvent=1
	AND HdHhtId=1

Original
UPDATE
 Grids
 INNER JOIN FinSchedule ON GrMatchNo=FSMatchNo AND FSTeamEvent=1 AND FSTournament='147'
 INNER JOIN Events ON FSEvent=EvCode AND FSTeamEvent=1 AND FSTournament=EvTournament AND FSTournament='147'
 INNER JOIN HhtData ON FSTarget=LEFT(HdTargetNo,CHAR_LENGTH(HdTargetNo)-1) AND IF(IF((IF(GrPhase>0,GrPhase*2,1) & EvFinalAthTarget)=IF(GrPhase>0,GrPhase*2,1),1,0)=1 && MOD(GrMatchNo,2)=1,'B','A') = RIGHT(HdTargetNo,1) AND HdFinScheduling=CONCAT(FSScheduledDate,' ',FSScheduledTime) AND HdTeamEvent=FSTeamEvent AND HdTeamEvent=1 AND HdTournament=FSTournament AND HdHhtId='1' SET HdMatchNo=FSMatchNo,HdEvent=FSEvent WHERE FSTournament='147'

Maybe this one is better!


UPDATE Grids INNER JOIN FinSchedule ON GrMatchNo = FSMatchNo AND FSTeamEvent =1 INNER JOIN EVENTS ON FSEvent = EvCode AND FSTeamEvent =1 AND FSTournament = EvTournament INNER JOIN HhtData ON FSTarget = HdRealTargetNo AND if( IF( GrPhase >0, GrPhase *2, 1 ) & EvFinalAthTarget >0 AND MOD( GrMatchNo, 2 ) =1, 'B', 'A' ) = HdRealLetter AND HdFinScheduling = CONCAT( FSScheduledDate, ' ', FSScheduledTime ) AND HdTeamEvent = FSTeamEvent AND HdTournament = FSTournament SET HdMatchNo = FSMatchNo,
HdEvent = FSEvent WHERE FSTournament = '296' AND HdTeamEvent =1 AND HdHhtId = '1'


*/
							}
							//print $Sql;exit();
							safe_w_sql($Sql);

						}
					}
				/*}
				else
					$Msg=get_text('SetDistanceError','HTT');*/
			}
		}
	}

	if(isset($_REQUEST['x_Hht']) && $_REQUEST['x_Hht']!=-1)
	{
		$Sql="";
		if (is_numeric($xSession))	// qual
		{
			$Sql = "SELECT SUBSTRING(HdTargetNo,2," . (TargetNoPadding) . ") as ChiTarget, SUBSTRING(HdTargetNo," . (TargetNoPadding+2) . ",1) as ChiLetter "
				. "FROM `HhtData` "
				. "WHERE LEFT(HdTargetNo,1)='" . $xSession . "' AND HdDistance='" . $Dist . "' "
				. "AND HdArrowStart='". $FirstArr . "' AND HdArrowEnd='". $LastArr . "' AND INSTR(HdArrowString, ' ')!= 0 "
				. "AND HdTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND HdHhtId=". StrSafe_DB($_REQUEST['x_Hht']);
		}
		else	// final
		{

			$Sql
				= "SELECT LEFT(HdTargetNo,CHAR_LENGTH(HdTargetNo)-1) AS ChiTarget, RIGHT(HdTargetNo,1) AS ChiLetter "
				. "FROM HhtData "
				. "WHERE HdFinScheduling=" . StrSafe_DB(substr($xSession,1)) . " AND HdTeamEvent=" . StrSafe_DB(substr($xSession,0,1)) . " "
				. "AND HdArrowStart='". $FirstArr . "' AND HdArrowEnd='". $LastArr . "' AND INSTR(HdArrowString, ' ')!= 0 "
				. "AND HdTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND HdHhtId=". StrSafe_DB($_REQUEST['x_Hht']);

		}
	//print $Sql;Exit;
// ora gestisce le arrowstring con errore per colorare di rosso
		$Rs = safe_r_sql($Sql);
		if(safe_num_rows($Rs)>0)
		{
			while($myRow = safe_fetch($Rs)) {
				$FromDB[] = intval($myRow->ChiTarget) . $myRow->ChiLetter;
				$Status[intval($myRow->ChiTarget) . $myRow->ChiLetter]='Red';
			}
			//safe_free_result($Rs);
		}
	/*print '<pre>';
	print_r($FromDB);
	print '</pre>';exit;*/
	}

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="Fun_JS.js"></script>',
		);

	$PAGE_TITLE=get_text('Download','HTT');

	include('Common/Templates/head.php');
?>
<form name="FrmParam" id="FrmParam" method="POST" action="">
	<input type="hidden" name="Command" value="">
	<table class="Tabella" >
<?php
if(!$ResponseFromHHT)
{
	echo '<tr class="error" style="height:35px;"><td colspan="5" class="Center LetteraGrande">' . get_text('HTTNotConnected','HTT') . '</td></tr>';
}
?>
		<tr><th class="Title" colspan="5"><?php print get_text('Download','HTT'); ?></th></tr>
		<tr class="Divider"><td colspan="5"></td></tr>
		<tr>
			<th width="20%"><?php print get_text('Terminal','HTT');?></th>
			<td ><?php print $ComboHHT;?>&nbsp;&nbsp;&nbsp;<input type="submit" value="<?php print get_text('CmdOk');?>"></td>
			<td colspan="3"><?php print get_text('KeepSelectedHHT','HTT');?>: <input type="checkbox" name="propagate"<?php echo (!empty($_REQUEST['propagate']) || empty($_REQUEST['x_Session'])?' checked="checked"':'') ?> onclick="UpdateLinks(this.checked,'FrmParam')" id="d_UpdateLinks"></td>
		</tr>
		<?php
		if(isset($_REQUEST["x_Hht"]) && $_REQUEST["x_Hht"]!=-1)
		{
		?>
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
			<td class="Center Bold"><?php print $Dist; ?></td>
		</tr>
		<?php
		}
		?>
	</table>
	<br/>
	<div id="HhtSearchResult">
	<?php
		if(isset($_REQUEST["x_Hht"]) && $_REQUEST["x_Hht"]!=-1)
		{
			if ($xSession!='0' &&
				($FirstArr<=$LastArr) &&
				$Volee>0)
			{
				print SelectTableHTT(10, 'FrmParam', false, $HTTOK, $FromDB, $Disable, false, $Status);

				print '<br /><table class="Tabella"><tr><td class="Center LetteraGrande">' . count($HTTOK) . ' / ' . $Num2Download . "</td></tr></table>\n";
				print '<br/><table class="Tabella">' . "\n";
					print '<tr><td class="Center"><input type="submit" value="' . get_text('CmdOk') . '" onclick="document.FrmParam.Command.value=\'OK\'"/></td></tr>' . "\n";
					print '<tr><td class="Center"><br/><a target="import" class="Link" href="Import.php?x_Hht=' . $_REQUEST["x_Hht"] . '&x_Session=' . $xSession . '&amp;FirstArr=' . $FirstArr . '&amp;LastArr=' . $LastArr . '&amp;Volee=' . $Volee . '&amp;Dist=' . $Dist . '&amp;Command=OK">' . get_text('CmdImport','HTT') . '</a></td></tr>' . "\n";
					if ($Msg!='')
					{
						print '<tr><td class="Center Bold">' . $Msg . '</td></tr>' . "\n";
					}
				print '</table>' . "\n";

				print '<br/><div>';
				$outhht='';
				if(!empty($_REQUEST['HTT'])) {
					foreach($_REQUEST['HTT'] as $k => $v) $outhht .= '&HTT['.$k.']='.$v;
				}
				print '<div align="left" style="position: relative; float: left; width: 45%;"><a href="Sequence.php?propagate='.(!empty($_REQUEST['propagate'])).'&x_Hht=' . $_REQUEST['x_Hht'] . '&x_Session=' . $_REQUEST['x_Session'] . $outhht . '" id="HhtPrevPage">' . get_text('HTTSequence', 'HTT') . '</a></div>';
				print '</div><br/>';

				print '<br/><table class="Tabella">' . "\n";
					print '<tr><td class="Center"><input type="submit" value="' . get_text('Store','HTT') . '" onclick="document.FrmParam.Command.value=\'STORE\'"/></td></tr>' . "\n";
				print '</table>' . "\n";

			}
			else
			{
				print '<br/>';
				print '<div align="center">' . "\n";
					print '<table class="Tabella">' . "\n";
						print '<tr><th>' . get_text('Error') . '</th></tr>' . "\n";
					print '</table>' . "\n";
				print '</div>' . "\n";
			}
		}
	?>
	</div>
</form>
<?php
	include('Common/Templates/tail.php');
?>