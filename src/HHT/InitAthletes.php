<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('serial.php');
	require_once('Fun_HHT.local.inc.php');
    require_once('Common/Lib/CommonLib.php');
    require_once('Common/Lib/Fun_Phases.inc.php');

	$RowTour=RowTour();

	$ComboHHT=ComboHHT();
	$ComboSes=ComboSession();

	$LettersCode=array
	(
		'A'=> chr(217),
		'B'=> chr(218),
		'C'=> chr(219),
		'D'=> chr(220)
	);

	$HTTOK=array();
	$Disable=array();
	$ResponseFromHHT=true;

	$Command=(isset($_REQUEST['Command']) ? $_REQUEST['Command'] : null);

	$HTTs=(isset($_REQUEST['HTT']) ? $_REQUEST['HTT'] : null);
	$Frames = array();

	if (!is_null($Command))
	{
		if ($Command=='OK')
		{

			if (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']!=-1 &&
				!is_null($HTTs) && is_array($HTTs) && !is_null($RowTour))
			{

//Carico i vuoti (se non è una finale)
				if (is_numeric($_REQUEST['x_Session']))
				{
					$Sql  = "SELECT SUBSTRING(AtTargetNo,2," . (TargetNoPadding) . ") as ChiTarget, SUBSTRING(AtTargetNo," . (TargetNoPadding+2) . ",1) as ChiLetter ";
					$Sql .= "FROM AvailableTarget at ";
					$Sql .= "LEFT JOIN ";
					$Sql .= "(SELECT QuTargetNo FROM Qualifications AS q  ";
					$Sql .= "INNER JOIN Entries AS e ON q.QuId=e.EnId AND e.EnTournament= " . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1) as Sq ON at.AtTargetNo=Sq.QuTargetNo ";
					$Sql .= "WHERE AtTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND LEFT(AtTargetNo,1)='" . $_REQUEST['x_Session'] . "' AND Sq.QuTargetNo is NULL";
					$Rs = safe_r_sql($Sql);
					//print $Sql;exit;
					if(safe_num_rows($Rs)>0)
					{
						while($myRow = safe_fetch($Rs))
							$Disable[] = intval($myRow->ChiTarget) . $myRow->ChiLetter;
						safe_free_result($Rs);
					}
//					print_r($Disable);exit();
				}

			// preparo i destinatari
				$Dests=array_values($HTTs);
				sort($Dests);	// per essere sicuro che se c'è lo zero allora sarà all'inizio

			// la if mi elimina la check "tutti"
				if (array_search(0,$HTTs)!==false)
					array_shift($Dests);

				/*print '<pre>';
				print_r($Dests);
				print '</pre>';
				exit;*/

			// paddo tutti i target
				$Targets=array();

				for ($i=0;$i<count($Dests);++$i)
					$Targets[$i]= StrSafe_DB((is_numeric($_REQUEST['x_Session']) ? $_REQUEST['x_Session'] : ''). str_pad($Dests[$i],TargetNoPadding,'0',STR_PAD_LEFT));

			// nomi
				$Select="";

				if (is_numeric($_REQUEST['x_Session'])) {
					// qualifications

					$Select = "SELECT "
							. "LEFT(CONCAT(EnFirstName,' ',LEFT(EnName,1),'.',RPAD('',13,' ')),13) AS Ath, "
							. "RPAD(SUBSTRING(CoCode,1,3),3,' ') AS CountryCode,";
					if(!empty($_SESSION['TargetsToHHt'])) {
						$Select = "SELECT "
								. "left(concat(trim(leading '0' from SUBSTRING(QuTargetNo,2)), '             '),13) AS Ath, "
								. "'   ' AS CountryCode,";
					}
					$Select.= "SUBSTRING(QuTargetNo,2," . TargetNoPadding . ") AS TargetNo,"
							. "RIGHT(QuTargetNo,1) AS TargetLetter "
						. "FROM "
							. "Entries "
							. "INNER JOIN "
								. "Qualifications "
							. "ON EnId=QuId "
							. "INNER JOIN "
								. "Countries "
							. "ON EnCountry=CoId "
						. "WHERE "
							. "EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND "
							. "SUBSTRING(QuTargetNo,1," . (TargetNoPadding+1). ") IN(" . implode(',',$Targets) . ") "
							. "AND EnStatus<=1 "	//???
						. "ORDER BY "
							. "QuTargetNo ASC ";

					//		print $Select;exit;

				} else {
					// finali
					$team=substr($_REQUEST['x_Session'],0,1);
					$when=substr($_REQUEST['x_Session'],1);

					$Select="";

					if ($team==0) {
						$Select = "SELECT "
								. "IFNULL(LEFT(CONCAT(EnFirstName,' ',LEFT(EnName,1),'.',RPAD('',13,' ')),13),'//') AS Ath, "
								. "IFNULL( RPAD(SUBSTRING(CoCode,1,3),3,' '),''/*RPAD('',3,'x')*/) AS CountryCode, ";
						if(!empty($_SESSION['TargetsToHHt'])) {
							$Select = "SELECT "
									. "left(concat(trim(leading '0' from FSTarget),'             '),13) AS Ath, "
									. "'   ' AS CountryCode,";
						}
						$Select.= "EvFinalAthTarget AS BitMask,"
								. "FSTarget AS TargetNo,"
								. "GrPhase,"
								. "GrMatchNo,"
								. "CONCAT(FSScheduledDate,' ',FSScheduledTime),FinEvent, "
								. "IF(IF((IF(GrPhase>0,GrPhase*2,1) & EvFinalAthTarget)=IF(GrPhase>0,GrPhase*2,1),1,0)=1 && MOD(GrMatchNo,2)=1,'B','A') AS TargetLetter "
							. "FROM "
								. "Events "

								. "INNER JOIN "
									. "Finals "
								. "ON EvCode=FinEvent AND EvTournament=FinTournament AND EvTeamEvent='" . $team . "' "

								. "INNER JOIN "
									. "FinSchedule "
								. "ON FSTeamEvent='" . $team . "' AND FinMatchNo=FSMatchNo AND FinEvent=FSEvent AND FinTournament=FSTournament "

								. "INNER JOIN "
									. "Grids "
								. "ON FinMatchNo=GrMatchNo "

								. "LEFT JOIN "
									. "Entries "
								. "ON FinAthlete=EnId AND FinTournament=EnTournament "

								. "LEFT JOIN "
									. "Countries "
								. "ON EnCountry=CoId "

							. "WHERE "
								. "EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
								. "AND FSTarget IN(" . implode(',',$Targets) . ") "
								. "AND CONCAT(FSScheduledDate,' ',FSScheduledTime)=" . StrSafe_DB($when) .  " "
							. "ORDER BY "
								. "FSTarget ASC";

					} else {
						$Select = "SELECT "
								. "IFNULL(LEFT(CONCAT(CoName,IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),''),RPAD('',13,' ')),13),'//') AS Ath, "
								. "IFNULL(RPAD(SUBSTRING(CoCode,1,3),3,' '),''/*RPAD('',3,' ')*/) AS CountryCode, ";
						if(!empty($_SESSION['TargetsToHHt'])) {
							$Select = "SELECT "
									. "left(concat(trim(leading '0' from FSTarget),'             '),13) AS Ath, "
									. "'   ' AS CountryCode,";
						}
						$Select.= "EvFinalAthTarget AS BitMask,"
								. "FSTarget AS TargetNo,"
								. "GrPhase,"
								. "GrMatchNo,"
								. "CONCAT(FSScheduledDate,' ',FSScheduledTime),TfEvent, "
								. "IF(IF((IF(GrPhase>0,GrPhase*2,1) & EvFinalAthTarget)=IF(GrPhase>0,GrPhase*2,1),1,0)=1 && MOD(GrMatchNo,2)=1,'B','A') AS TargetLetter "
							. "FROM "
								. "Events "

								. "INNER JOIN "
									. "TeamFinals "
								. "ON EvCode=TfEvent AND EvTournament=TfTournament AND EvTeamEvent='" . $team . "' "

								. "INNER JOIN "
									. "FinSchedule "
								. "ON FSTeamEvent='" . $team . "' AND TfMatchNo=FSMatchNo AND TfEvent=FSEvent AND TfTournament=FSTournament "

								. "INNER JOIN "
									. "Grids "
								. "ON TfMatchNo=GrMatchNo "

								. "LEFT JOIN "
									. "Countries "
								. "ON TfTeam=CoId "

							. "WHERE "
								. "EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
								. "AND FSTarget IN(" . implode(',',$Targets) . ") "
								. "AND CONCAT(FSScheduledDate,' ',FSScheduledTime)=" . StrSafe_DB($when) .  " "
							. "ORDER BY "
								. "FSTarget ASC";
					}
				}
//echo $Select; 		exit;

				$Rs=safe_r_sql($Select);

				if (safe_num_rows($Rs)>0)
				{
					$Data='';
					$TargetNo='xx';

					while ($MyRow=safe_fetch($Rs))
					{
						if ($TargetNo!=$MyRow->TargetNo)
						{
							if ($TargetNo!='xx')
							{
								//print 'finito<br>';
								$Frames = array_merge($Frames, PrepareTxFrame(intval($TargetNo),$Data));
							}

							$Data='';
						}

						$Data
							.=Alpha
							. $LettersCode[$MyRow->TargetLetter]
							. substr(iconv('UTF-8','ASCII//TRANSLIT',$MyRow->Ath),0,13)
							. substr(iconv('UTF-8','ASCII//TRANSLIT',$MyRow->CountryCode),0,3);
						//print intval($MyRow->TargetNo) . ' - ' . $Data . '<br/>';
						//print $Data.'<br>';
						$TargetNo=$MyRow->TargetNo;
					}
				}

			// ultimo ciclo
				$Frames = array_merge($Frames, PrepareTxFrame(intval($TargetNo),$Data));
				//print 'finito<br>';

				/*print '<pre>';
				print_r($Frames);
				print '</pre>';exit;*/

/*foreach($Frames as $value)
	echo OutText($value);
//exit();*/
				if(count($Frames)>0)
				{
					$ResponseFromHHT=false;
					$Results=SendHTT(HhtParam($_REQUEST['x_Hht']),$Frames,false,0.5);
					if(!is_null($Results))
						$ResponseFromHHT=true;
					if (count($Results)!=0)
					{
						foreach($Results as $v)
						{
							if ($v!=-1)
								$HTTOK[]=$v;
						}

						//print_r($HTTOK);
					}
				}
//exit();

			}
		}
	}

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="../Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="Fun_JS.js"></script>',
		);

	$PAGE_TITLE=get_text('AthletesSetup','HTT');

	include('Common/Templates/head.php');

?>
<form name="FrmParam" method="POST" action="<?php print $_SERVER["PHP_SELF"];?>">
	<table class="Tabella">
<?php
if(!$ResponseFromHHT)
{
	echo '<tr class="error" style="height:35px;"><td colspan="5" class="Center LetteraGrande">' . get_text('HTTNotConnected','HTT') . '</td></tr>';
}
?>
	<tr><th class="Title" colspan="4"><?php print get_text('AthletesSetup','HTT'); ?></th></tr>
	<tr class="Divider"><td colspan="4"></td></tr>
	<tr>
	<th width="5%"><?php print get_text('Terminal','HTT');?></th>
	<th width="5%"><?php print get_text('Session');?></th>
	<th width="5%"><?php print get_text('KeepSelectedHHT','HTT');?></th>
	<th width="5%">&nbsp;</th>
	</tr>
	<tr>
	<td class="Center"><?php print $ComboHHT; ?></td>
	<td class="Center" id="HhtSearchSession"><?php print $ComboSes; ?></td>
	<td class="Center"><input type="checkbox" name="propagate"<?php echo (!empty($_REQUEST['propagate']) || empty($_REQUEST['x_Session'])?' checked="checked"':'') ?> onclick="UpdateLinks(this.checked)" id="d_UpdateLinks"></td>
	<td class="Center"><input type="submit" name="submit" value="<?php print get_text('CmdOk');?>"></td>
	</tr>
	</table>
</form>

<?php
	if (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']!=-1)
	{
		$Disable=array();
		if (is_numeric($_REQUEST['x_Session']))	//qual
		{
			$Sql  = "SELECT SUBSTRING(AtTargetNo,2," . (TargetNoPadding) . ") as ChiTarget, SUBSTRING(AtTargetNo," . (TargetNoPadding+2) . ",1) as ChiLetter ";
			$Sql .= "FROM AvailableTarget at ";
			$Sql .= "LEFT JOIN ";
			$Sql .= "(SELECT QuTargetNo FROM Qualifications AS q  ";
			$Sql .= "INNER JOIN Entries AS e ON q.QuId=e.EnId AND e.EnTournament= " . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1 AND EnStatus<6) as Sq ON at.AtTargetNo=Sq.QuTargetNo ";
			$Sql .= "WHERE AtTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND LEFT(AtTargetNo,1)='" . $_REQUEST['x_Session'] . "' AND Sq.QuTargetNo is NULL ";

			$Rs = safe_r_sql($Sql);
			if(safe_num_rows($Rs)>0)
			{
				while($myRow = safe_fetch($Rs))
					$Disable[] = intval($myRow->ChiTarget) . $myRow->ChiLetter;
			}

			$Sql = "SELECT QuTargetNo FROM Qualifications AS q  ";
			$Sql .= "INNER JOIN Entries AS e ON q.QuId=e.EnId AND e.EnTournament= " . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1 AND EnStatus<6 AND LEFT(QuTargetNo,1)='" . $_REQUEST['x_Session'] . "'";
			$Rs = safe_r_sql($Sql);
			$Num2Download = safe_num_rows($Rs);
		}

		$out='<div id="HhtSearchResult">';
		$out.='<br/><div>';
		$outhht='';
		if(!empty($_REQUEST['HTT'])) {
			foreach($_REQUEST['HTT'] as $k => $v) $outhht .= '&HTT['.$k.']='.$v;
		}
		$out.='<div align="left" style="position: relative; float: left; width: 45%;"><a href="InitHTT.php?propagate='.(!empty($_REQUEST['propagate'])).'&x_Hht=' . $_REQUEST['x_Hht'] . '&x_Session=' . $_REQUEST['x_Session'] . $outhht . '" id="HhtPrevPage">' . get_text('SetupTerminals', 'HTT') . '</a></div>';
		$out.='<div align="right" style="position: relative; float: right; width: 45%;"><a href="InitScores.php?propagate='.(!empty($_REQUEST['propagate'])).'&x_Hht=' . $_REQUEST['x_Hht'] . '&x_Session=' . $_REQUEST['x_Session'] . $outhht . '" id="HhtNextPage">' . get_text('ScoreSetup', 'HTT') . '</a></div>';
		$out.='</div><br/><br/>';

		$out
			.='<form id="FrmSetup" name="FrmSetup" method="post" action="'.basename($_SERVER['SCRIPT_NAME']).'?x_Hht=' . $_REQUEST['x_Hht'] . '&x_Session=' . $_REQUEST['x_Session'] . '">' . "\n"
				. '<input type="hidden" name="x_Hht" value="' . $_REQUEST['x_Hht'] . '"/>'
				. '<input type="hidden" name="x_Session" value="' . $_REQUEST['x_Session'] . '"/>'
				. '<input type="hidden" name="propagate" value="'.(!empty($_REQUEST['propagate'])).'"/>'
				. '<input type="hidden" name="Command" value="OK"/>';

			//$out.=TableHTT(10,'FrmSetup',false,$HTTOK,array(),$Disable);

			$out.=SelectTableHTT(10,'FrmSetup',false,$HTTOK,array(),$Disable);

			$out.='<br/><div align="center">';
			$out.='<input type="submit" value="' . get_text('CmdOk') . '"/>' . "\n";
			$out.='</div>';

		$out.='</form></div>' . "\n";

		print $out;
	}

	include('Common/Templates/tail.php');
?>
