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

	$xSession=0;
	$DbSeqParam='';
	$DbDistParam=0;
	if(isset($_REQUEST['x_Session']))
	{
		$xSession = $_REQUEST['x_Session'];
		if(isset($_REQUEST['x_Hht']) && $_REQUEST['x_Hht']!=-1)
		{
			$Select = "Select HsSequence, HsDistance FROM HhtSetup WHERE HsTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND HsId=" . StrSafe_DB($_REQUEST['x_Hht']);
			$rs = safe_w_sql($Select);
			$MyRow = safe_fetch($rs);
			$DbSeqParam = str_pad($MyRow->HsSequence,12,' ');
			$DbDistParam = $MyRow->HsDistance;
			$Select = "UPDATE HhtSetup SET HsPhase=" . StrSafe_DB($_REQUEST['x_Session']) . " WHERE HsTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND HsId=" . StrSafe_DB($_REQUEST['x_Hht']);
			$rs = safe_w_sql($Select);
		}
	}
	else if(isset($_REQUEST['x_Hht']) && $_REQUEST['x_Hht']!=-1)
	{
		$Select = "Select HsPhase, HsSequence, HsDistance FROM HhtSetup WHERE HsTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND HsId=" . StrSafe_DB($_REQUEST['x_Hht']);
		$rs = safe_w_sql($Select);
		$MyRow = safe_fetch($rs);
		$xSession = $MyRow->HsPhase;
		$DbSeqParam = str_pad($MyRow->HsSequence,12,' ');
		$DbDistParam = $MyRow->HsDistance;
	}




	$ScoreStartMapping=array
	(
		0=>'0',
		1=>'1',
		2=>'2',
		3=>'3',
		4=>'4',
		5=>'5',
		6=>'6',
		7=>'7',
		8=>'8',
		9=>'9',
		10=>chr(158),
		11=>'X'
	);

	$InfosMapping=array
	(
		'GameInfo'=>chr(221),
		'Sponsor'=>chr(222),
		'Sequence'=>chr(223)
	);

	$HTTOK=array();
	$Disable=array();
	$ResponseFromHHT=true;

	$Command=(isset($_REQUEST['Command']) ? $_REQUEST['Command'] : null);

	$HTTs=(isset($_REQUEST['HTT']) ? $_REQUEST['HTT'] : null);

	$FirstArr     =(isset($_REQUEST['txtFirstArr']) ? str_pad($_REQUEST['txtFirstArr'],2,'0',STR_PAD_LEFT) : substr($DbSeqParam,0,2));
	$LastArr      =(isset($_REQUEST['txtLastArr']) ? str_pad($_REQUEST['txtLastArr'],2,'0',STR_PAD_LEFT) : substr($DbSeqParam,2,2));
	$Volee        =(isset($_REQUEST['txtVolee']) ? str_pad($_REQUEST['txtVolee'],2,"0",STR_PAD_LEFT) : substr($DbSeqParam,4,2));
	$ScoreStart   =(isset($_REQUEST['ScoreStart']) ? $_REQUEST['ScoreStart'] : intval(substr($DbSeqParam,6,2)));
	$StoreTimeout =(isset($_REQUEST['txtStoreTo']) ? str_pad($_REQUEST['txtStoreTo'],2,"0",STR_PAD_LEFT) : substr($DbSeqParam,8,2));

	$What=-1;
	$Ses=$xSession;
	if (!is_numeric($xSession))
	{
		$What=(substr($xSession,0,1));
		$Ses=substr($xSession,1);
	}
	$Phase =(isset($_REQUEST['phase']) ? $_REQUEST['phase'] : phaseEncode($What,$Ses,$DbDistParam));

	//print $Phase;exit;
	/*print $ActualArr . '<br>';
	print $TotalArrs . '<br>';
	print $Dist . '<br>';
	exit;*/
	$Dist='';
	if (!is_null($Command))
	{
		if ($Command=='OK')
		{
			if (!is_null($HTTs) && is_array($HTTs))
			{
				list(,,$Dist)=phaseDecode($Phase);
				// preparo i destinatari
				$Dests=null;
				$Dests=array_values($HTTs);
				sort($Dests);	// per essere sicuro che se c'è lo zero allora sarà all'inizio

				// la if mi elimina la check "tutti"
				if (array_search(0,$HTTs)!==false)
					array_shift($Dests);
				$Frames=array();

			// preparo la parte data dei datagram
				if (isset($_REQUEST['chkGameInfo']) && $_REQUEST['chkGameInfo']==1)
				{
					if (isset($_REQUEST['txtGameInfo']))
					{
						$GameInfo=str_pad($_REQUEST['txtGameInfo'],21,' ',STR_PAD_RIGHT);
						$Data=Alpha . $InfosMapping['GameInfo'] . $GameInfo;
						$Frames=array_merge($Frames, PrepareTxFrame($Dests,$Data));
					}
				}

				if (isset($_REQUEST['chkSponsor']) && $_REQUEST['chkSponsor']==1)
				{
					if (isset($_REQUEST['txtSponsor1']) && isset($_REQUEST['txtSponsor2']))
					{
						$Sponsor1=str_pad($_REQUEST['txtSponsor1'],21,' ',STR_PAD_RIGHT);
						$Sponsor2=str_pad($_REQUEST['txtSponsor2'],21,' ',STR_PAD_RIGHT);
						$Data=Alpha . $InfosMapping['Sponsor'] . $Sponsor1 . $Sponsor2;
						$Frames=array_merge($Frames, PrepareTxFrame($Dests,$Data));
					}
				}
				if (isset($_REQUEST['chkSendSequence']) && $_REQUEST['chkSendSequence']==1)
				{
					if (intval($FirstArr)>0 &&
						intval($LastArr)>0 &&
						intval($Volee)>0 &&
						array_key_exists($ScoreStart,$ScoreStartMapping) &&
						intval($StoreTimeout)>0)
					{
						// scrivo i parametri nel DB
						$Query
						= "UPDATE "
							. "HhtSetup "
						. "SET "
							. "HsDistance=" . StrSafe_DB($Dist) . ", "
							. "HsSequence=" . StrSafe_DB($FirstArr . $LastArr . $Volee . $ScoreStart . $StoreTimeout) . " "
						. "WHERE "
							. "HsTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND "
							. "HsId=" . StrSafe_DB($_REQUEST['x_Hht']);
						$Rs=safe_w_sql($Query);

						$Data
							= Alpha
							. $InfosMapping['Sequence']
							. $FirstArr	// già paddato
							. $LastArr	// già paddato
							. $Volee //già paddato
							. $Phase
							. $ScoreStartMapping[$ScoreStart]
							. $StoreTimeout; //già paddato

						$Frames=array_merge($Frames, PrepareTxFrame($Dests,$Data));
					}

				}
/*foreach($Frames as $value)
	echo count($Frames) . OutText($value);
exit();*/
			// Risposte
				$Results = array();
				if(count($Frames)>0)
				{
					$ResponseFromHHT=false;
					$Results=SendHTT(HhtParam($_REQUEST['x_Hht']),$Frames);
					if(!is_null($Results))
						$ResponseFromHHT=true;
					//print '<pre>';print_r($Results);print'</pre>';exit();
					if (count($Results)!=0)
					{
						foreach($Results as $v)
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

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="../Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="Fun_JS.js"></script>',
		);

	$PAGE_TITLE=get_text('HTTSequence','HTT');

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
	<tr><th class="Title" colspan="4"><?php print get_text('HTTSequence','HTT'); ?></th></tr>
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
	<td class="Center"><input type="submit" value="<?php print get_text('CmdOk');?>"></td>
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
		$out.='<div align="left" style="position: relative; float: left; width: 45%;"><a href="InitScores.php?propagate='.(!empty($_REQUEST['propagate'])).'&x_Hht=' . $_REQUEST['x_Hht'] . '&x_Session=' . $_REQUEST['x_Session'] . $outhht . '" id="HhtPrevPage">' . get_text('ScoreSetup', 'HTT') . '</a></div>';
		$out.='<div align="right" style="position: relative; float: right; width: 45%;"><a href="Collect.php?propagate='.(!empty($_REQUEST['propagate'])).'&x_Hht=' . $_REQUEST['x_Hht'] . '&x_Session=' . $_REQUEST['x_Session'] . $outhht . '" id="HhtNextPage">' . get_text('Download', 'HTT') . '</a></div>';
		$out.='</div><br/><br/>';

		$out
			.='<form name="FrmSetup" id="FrmSetup" method="post" action="'.basename($_SERVER['SCRIPT_NAME']).'?x_Hht=' . $_REQUEST['x_Hht'] . '&x_Session=' . $_REQUEST['x_Session'] . '">' . "\n"
			. '<input type="hidden" name="x_Hht" value="' . $_REQUEST['x_Hht'] . '"/>'
			. '<input type="hidden" name="propagate" value="'.(!empty($_REQUEST['propagate'])).'"/>'
			. '<input type="hidden" name="x_Session" value="' . $_REQUEST['x_Session'] . '"/>';

		$out
				.='<table class="Tabella">' . "\n";
				$out .= '<tr>'
						. '<td><input type="checkbox" name="chkGameInfo" value="1"' . (isset($_REQUEST['chkGameInfo']) ? ' checked="true"' : ''). '>' . get_text('SeqGameInfo','HTT')
						. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="txtGameInfo" maxlength="21" size="35" value="' . (isset($_REQUEST['txtGameInfo']) ? $_REQUEST['txtGameInfo'] : ''). '"></td>'
					. '</tr>' . "\n";
				$out .= '<tr>'
						. '<td><input type="checkbox" name="chkSponsor" value="1"' . (isset($_REQUEST['chkSponsor']) ? ' checked="true"' : ''). '>' . get_text('SeqSponsor','HTT')
						. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="txtSponsor1" maxlength="21" size="35" value="' . (isset($_REQUEST['txtSponsor1']) ? $_REQUEST['txtSponsor1'] : ''). '">'
						. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="txtSponsor2" maxlength="21" size="35" value="' . (isset($_REQUEST['txtSponsor2']) ? $_REQUEST['txtSponsor2'] : ''). '"></td>'
					. '</tr>' . "\n";
				$out.='</table>' . "\n";

			$out.='<br/><br/>';

			$ComboScoreStart= '<select name="ScoreStart">' . "\n";
			for ($i=0;$i<=11;++$i)
			{
				$ComboScoreStart.='<option value="' . $i . '"' . ($ScoreStart==$i ? ' selected' : '') . '>' . ($i==11 ? 'X' : $i) . '</option>' . "\n";
			}
			$ComboScoreStart.='</select>' . "\n";

			$InputPhase='';
			if (is_numeric($xSession))	// qual
			{
				$InputPhase='<select name="phase">' . "\n";
					$InputPhase.='<option value="000">---</option>' . "\n";
					for ($i=1;$i<=$RowTour->TtNumDist;++$i)
					{
						//$v=str_pad($i,3,'0',STR_PAD_LEFT);
						$v='0' . $Ses . $i;
						$InputPhase.='<option value="' . $v . '"' . ($v==$Phase ? ' selected' : '') . '>' . $i . '</option>' . "\n";
					}
				$InputPhase.='</select>' . "\n";
			}
			else
			{
				$encode=phaseEncode($What,$Ses,$Dist);
				$InputPhase='<input type="hidden" name="phase" value="' . $encode . '">' . $encode;
			}

			$out
				.='<table class="Tabella">' . "\n"
					. '<tr>'
						. '<td class="Title">&nbsp;</td>'
						. '<td class="Title">&nbsp;</td>'
						. '<td class="Title">' . get_text('FirstArrow','HTT') . '</td>'
						. '<td class="Title">' . get_text('LastArrow','HTT') . '</td>'
						. '<td class="Title">' . get_text('End (volee)') . '</td>'
						. '<td class="Title">' . get_text('Phase') . '<br/>(' . get_text('Distance','HTT') . ')</td>'
						. '<td class="Title">' . get_text('StartScore','HTT') . '</td>'
						. '<td class="Title">' . get_text('StoreTimeout','HTT') . '</td>'
					. '</tr>' . "\n"
					. '<tr>'
						. '<td class="Center"><input type="checkbox" name="chkSendSequence" value="1" ' . (isset($_REQUEST['chkSendSequence']) ? ' checked ' : ''). '/>' . get_text('SendInitSequence','HTT') . '</td>'
						. '<td class="Center">'
							. '<input type="button" id="btnPlus" value="+" onclick="incSeq();">'
							. '&nbsp;&nbsp;'
							. '<input type="button" id="btnMinus" value="-" onclick="decSeq();">'
						. '</td>'
						. '<td class="Center"><input type="text" id="firstArr" name="txtFirstArr" maxlength="2" size="3" value="' . $FirstArr . '" /></td>'
						. '<td class="Center"><input type="text" id="lastArr" name="txtLastArr" maxlength="2" size="3" value="' . $LastArr. '" /></td>'
						. '<td class="Center"><input type="text" id="volee" name="txtVolee" maxlength="2" size="3" value="' . $Volee . '" /></td>'
						. '<td class="Center">' . $InputPhase . '</td>'
						. '<td class="Center">' . $ComboScoreStart . '</td>'
						. '<td class="Center"><input type="text" id="txtStoreTo" name="txtStoreTo" maxlength="2" size="3" value="' . $StoreTimeout . '" /></td>'
					. '</tr>'
				. '</table>' . "\n";

			$out.='<br/><br/>';

			//$out.=TableHTT(10,'FrmSetup',false,$HTTOK,array(),$Disable);
			$out.=SelectTableHTT(10,'FrmSetup',false,$HTTOK,array(),$Disable,true);

			$out.='<br/><div align="center">';
				$out.='<input type="hidden" name="Command" value="OK"><input type="submit" value="' . get_text('CmdOk') . '"/>' . "\n";
			$out.='</div>';

		$out.='</form></div>' . "\n";

		print $out;
	}

	include('Common/Templates/tail.php');
?>
