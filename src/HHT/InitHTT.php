<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('serial.php');
	require_once('Fun_HHT.local.inc.php');
    require_once('Common/Lib/CommonLib.php');
    require_once('Common/Lib/Fun_Phases.inc.php');

	$Modes=array
	(
//		0=>get_text('TerminalMode', 'HTT'),
//		1=>get_text('OfficeMode', 'HTT'),
//		2=>get_text('TeamMode', 'HTT'),
		3=>get_text('IndividualMode', 'HTT')
	);

	$ModeMapping=array
	(
		0=>chr(208),
		1=>chr(209),
		2=>chr(210),
		3=>chr(211)
	);

	$HTTOK=array();
	$ResponseFromHHT=true;

	$Command=(isset($_REQUEST['Command']) ? $_REQUEST['Command'] : null);

	$HTTs=(isset($_REQUEST['HTT']) ? $_REQUEST['HTT'] : null);

	$Mode = 0;
	$DbFlags='';
	if(isset($_REQUEST['Mode']))
	{
		$Mode = $_REQUEST['Mode'];
		if(isset($_REQUEST['x_Hht']) && $_REQUEST['x_Hht']!=-1)
		{
			$Select = "Select HsFlags FROM HhtSetup WHERE HsTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND HsId=" . StrSafe_DB($_REQUEST['x_Hht']);
			$rs = safe_w_sql($Select);
			$MyRow = safe_fetch($rs);
			$DbFlags = str_pad(trim($MyRow->HsFlags),16,"N");
		}
	}
	else if(isset($_REQUEST['x_Hht']) && $_REQUEST['x_Hht']!=-1)
	{
		$Select = "Select HsMode, HsFlags FROM HhtSetup WHERE HsTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND HsId=" . StrSafe_DB($_REQUEST['x_Hht']);
		$rs = safe_w_sql($Select);
		$MyRow = safe_fetch($rs);
		$Mode = $MyRow->HsMode;
		$DbFlags = str_pad(trim($MyRow->HsFlags),16,"N");
	}


	//item 3 was 'HTTFlags_3Arrows',
	//item 4 was 'HTTFlags_LasVegasMode',
	$Flags=array();
	$TrueFlags=0;
	$HttFlags=array(
		'HTTFlags_SelectX',
		'HTTFlags_SelectM',
		'HTTFlags_SelectE',
		'',
		'',
		'HTTFlags_DistanceTotal',
		'HTTFlags_Min6_A',
		'HTTFlags_Min6_B',
		'HTTFlags_Min6_C',
		'HTTFlags_Min6_D',
		'HTTFlags_TargetLetter',
		'HTTFlags_GameInfo',
		'',
		'',
		'HTTFlags_ResetInfo',
		'HTTFlags_ResetID',
		);

	foreach($HttFlags as $key=>$val) {
		if($val and $val!='HTTFlags_ResetID') {
			$_REQUEST[$val]=isset($_REQUEST[$val]) ? $_REQUEST[$val] : substr($DbFlags,$key,1);
			$Flags[$key]=$_REQUEST[$val];
			$TrueFlags++;
		} else {
			$Flags[$key]='N';
		}
	}

	if(isset($_REQUEST['truncate'])) {
		$query = "truncate HhtData";
		$rs=safe_w_sql($query);
		CD_redirect(basename(__FILE__));
	}

	if(isset($_REQUEST['truncatemy']) and isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']!=-1) {
		$Disable=array();
		if (is_numeric($_REQUEST['x_Session']))	 {
			//qual
            safe_w_sql("delete from HhtData where HdTournament={$_SESSION['TourId']} and HdTargetNo like '{$_REQUEST['x_Session']}%' and HdFinScheduling=0");
		} else {
			// matches
			$team=substr($_REQUEST['x_Session'],0,1);
			$when=substr($_REQUEST['x_Session'],1);

			$Sql = "SELECT DISTINCT FSTarget AS TargetNo
				FROM FinSchedule
				WHERE FSTournament={$_SESSION['TourId']}
					AND CONCAT(FSScheduledDate,' ',FSScheduledTime)=" . StrSafe_DB($when) . "
					AND FSTeamEvent=" . StrSafe_DB($team) . "
					AND FSTarget<>''";
            $q=safe_r_SQL($Sql);
            while($r=safe_fetch($q)) {
                safe_w_sql("delete from HhtData where HdTournament={$_SESSION['TourId']} and HdRealTargetNo = ".intval($r->TargetNo));
            }
		}

		CD_redirect(basename(__FILE__));
	}

	if (!is_null($Command)) {
		if ($Command=='OK')
		{
			if (!is_null($Mode) && array_key_exists($Mode,$Modes))
			{
				if (!is_null($HTTs) && is_array($HTTs))
				{
				// scrivo i parametri nel db
					$Query
						= "UPDATE "
							. "HhtSetup "
						. "SET "
							. "HsMode=" . StrSafe_DB($Mode) . ", "
							. "HsFlags=" . StrSafe_DB(implode($Flags)) . " "
						. "WHERE "
							. "HsTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND "
							. "HsId=" . StrSafe_DB($_REQUEST['x_Hht']);
					$Rs=safe_w_sql($Query);


					// preparo i destinatari
					$Dests=array_values($HTTs);
					sort($Dests);	// per essere sicuro che se c'è lo zero allora sarà all'inizio

					// la if mi elimina la check "tutti"
					if (array_search(0,$HTTs)!==false)
						array_shift($Dests);

				// preparo la parte data del datagram
					$Data=Alpha . $ModeMapping[$Mode];

				// a seconda del $Mode aggiungo o no i flags
					switch ($Mode)
					{
						case 2:
						case 3:
							for($numFlag=0; $numFlag<count($Flags); $numFlag++ )
								$Data.=$Flags[$numFlag];
							break;
					}

					/*print '<pre>';
					print_r($Dests);
					print($Data);
					print '</pre>';exit;*/

				// preparo i pacchetti
					$Frames=PrepareTxFrame($Dests,$Data);
				// Risposte
/*foreach($Frames as $value)
	echo OutText($value);
//exit();*/
					$ResponseFromHHT=false;
					$Updated=SendHTT(HhtParam($_REQUEST['x_Hht']),$Frames,false,3);
//print_r($Updated);
				// se non era un broadcast verifico chi ha risposto ok
					if ($Dests!=0)
					{
						/*print '<pre>';
						print_r($Updated);
						print '</pre>';*/
						if(!is_null($Updated))
							$ResponseFromHHT=true;
						if (count($Updated)!=0)
						{
							foreach($Updated as $v)
							{
								if ($v!=-1)
									$HTTOK[]=$v;
							}
						}
					}

//exit();
				}
			}
		}
	}

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="../Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="Fun_JS.js"></script>',
		);

	$PAGE_TITLE=get_text('SetupTerminals','HTT');

	include('Common/Templates/head.php');

	$ComboHHT=ComboHHT();
	$ComboSes=ComboSession();
?>
<form name="FrmParam" method="POST" action="<?php print $_SERVER["PHP_SELF"];?>">
	<table class="Tabella">
<?php
if(!$ResponseFromHHT) {
	echo '<tr class="error" style="height:35px;"><td colspan="5" class="Center LetteraGrande">' . get_text('HTTNotConnected','HTT') . '</td></tr>';
}
?>
	<tr><th class="Title" colspan="5"><?php print get_text('SetupTerminals','HTT'); ?></th></tr>
	<tr class="Divider"><td colspan="5"></td></tr>
	<tr>
	<th width="5%"><?php print get_text('Terminal','HTT');?></th>
	<th width="5%"><?php print get_text('Session');?></th>
	<th width="5%"><?php print get_text('KeepSelectedHHT','HTT');?></th>
	<th width="5%">&nbsp;</th>
	<th width="5%">&nbsp;</th>
	</tr>
	<tr>
	<td class="Center"><?php print $ComboHHT; ?></td>
	<td class="Center" id="HhtSearchSession"><?php print $ComboSes; ?></td>
	<td class="Center"><input type="checkbox" name="propagate"<?php echo (!empty($_REQUEST['propagate']) || empty($_REQUEST['x_Session'])?' checked="checked"':'') ?> onclick="UpdateLinks(this.checked)" id="d_UpdateLinks"></td>
	<td class="Center"><input type="submit" name="submit" value="<?php print get_text('CmdOk');?>"></td>
	<td class="Center">
		<input type="submit" name="truncate" value="<?php print get_text('CmdTruncateHTTData','HTT');?>" onClick="return(confirm('<?php print addslashes(get_text('MsgAreYouSure')); ?>'))">
		<?php
		if(isset($_REQUEST["x_Hht"]) and $_REQUEST["x_Hht"]!=-1 and isset($_REQUEST['x_Session']) and $_REQUEST['x_Session']!=-1) {
			echo '<input type="submit" name="truncatemy" value="'.get_text('CmdTruncateMyHTTData','HTT').'" onClick="return(confirm(\''.addslashes(get_text('MsgAreYouSure')).'\'))">';
		}

		?>
	</td>
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
		$out.='<br/><div align="right">';
		$outhht='';
		if(!empty($_REQUEST['HTT'])) {
			foreach($_REQUEST['HTT'] as $k => $v) $outhht .= '&HTT['.$k.']='.$v;
		}
		$out.='<a href="InitAthletes.php?propagate='.(!empty($_REQUEST['propagate'])).'&x_Hht=' . $_REQUEST['x_Hht'] . '&x_Session=' . $_REQUEST['x_Session'] . $outhht . '" id="HhtNextPage">' . get_text('AthletesSetup', 'HTT') . '</a>' . "\n";
		$out.='</div>';
		$out
			.='<form name="FrmSetup" id="FrmSetup" method="post" action="'.basename($_SERVER['SCRIPT_NAME']).'?x_Hht=' . $_REQUEST['x_Hht'] . '&x_Session=' . $_REQUEST['x_Session'] . '">' . "\n"
			. '<input type="hidden" name="x_Hht" value="' . $_REQUEST['x_Hht'] . '"/>'
			. '<input type="hidden" name="x_Session" value="' . $_REQUEST['x_Session'] . '"/>'
			. '<input type="hidden" name="Command" value="OK"/>'
			. '<input type="hidden" name="propagate" value="'.(!empty($_REQUEST['propagate'])).'"/>'
			. '<br/>';

			$ComboMode= '<select name="Mode">' . "\n";
				foreach ($Modes as $k=>$v)
				{
					$ComboMode.='<option value="' . $k .'"' . (!is_null($Mode) && $Mode==$k ? ' selected' : '') . '>' . $v . '</option>' . "\n";
				}
			$ComboMode.='</select>' . "\n";

			$out
				.='<table class="Tabella">' . "\n"
					. '<tr>'
					. '<td class="Center" colspan="' . $TrueFlags . '">' . get_text('Mode','HTT') . '<br>' . $ComboMode . '</td>'
					. '</tr>' . "\n";
					$out .= '<tr>';
					foreach ($HttFlags as $Flg)
					{
						if($Flg) {
							$Selected='N';
							if($Flg=='HTTFlags_ResetInfo') $Selected='Y';
							if(!empty($_REQUEST[$Flg]) && $_REQUEST[$Flg]=='Y') $Selected='Y';
							$out
								.='<td class="Center">' . get_text($Flg,'HTT') . '<br>'
									. '<select name="' . $Flg . '">' . "\n"
										. '<option value="N"' . (isset($_REQUEST[$Flg]) && $_REQUEST[$Flg]=='N' ? ' selected' : '') . '>' . get_text('No') . '</option>' . "\n"
										. '<option value="Y"' . (isset($_REQUEST[$Flg]) && $_REQUEST[$Flg]=='Y' ? ' selected' : '') . '>' . get_text('Yes') . '</option>' . "\n"
									. '</select>' . "\n"
								. '</td>';
						}
					}
					$out .= '</tr>';
				$out.='</table>' . "\n";

			$out.='<br/><br/>';

			/*if (is_numeric($_REQUEST['x_Session']))
				$out.=TableHTT(10,'FrmSetup',false,$HTTOK,array(),array());
			else
				$out.=TableFinalHTT(10,'FrmSetup',false,$HTTOK,array(),array());*/
			$out.=SelectTableHTT(10,'FrmSetup',false,$HTTOK,array(),$Disable);

			$out.='<br/><div align="center">';
				$out.='<input type="submit" value="' . get_text('CmdOk') . '" onsubmit="UpdateLinks(document.getElementById(\'d_UpdateLinks\').checked)" />' . "\n";
			$out.='</div></div>';

		$out.='</form>' . "\n";

		print $out;
	}

	include('Common/Templates/tail.php');
?>
