<?php
	define('debug',false);	// settare a true per l'output di debug

	$ErrMsg=array();

	// switch to deal with individuals or teams...
	$Teams='0';
	if(!empty($_REQUEST['Teams'])) $Teams='1';

	require_once(dirname(dirname(__FILE__)) . '/config.php');
    CheckTourSession(true);
    checkACL(AclCompetition, AclReadWrite);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Phases.inc.php');

	// checks the max length of ArrowString and TieArrowString
	if($Teams) {
		$q=safe_r_sql("show columns in TeamFinals where field= 'TfArrowString' or Field='TfTieBreak'");
	} else {
		$q=safe_r_sql("show columns in Finals where field= 'FinArrowString' or Field='FinTieBreak'");
	}
	while($r=safe_fetch($q)) {
		$val=array();
		if(preg_match("/\(([0-9.]+)\)/", $r->Type, $val)) {
			$val=$val[1];
		}
		switch($r->Field) {
			case 'FinArrowstring':
			case 'TfArrowstring':
				$MaxArrowString=$val;
				break;
			case 'FinTiebreak':
			case 'TfTiebreak':
				$MaxSOString=$val;
				break;
			default:
		}
	}

	if (isset($_REQUEST['Command'])) {
		if ($_REQUEST['Command']=='SAVE') {
			// If bit is set than it is an elimiatory phase!!!
			$BitMask=255;
			for($n=0; $n<8; $n++) {
				//print 'n: '.$n.'<br>';
				if(isset($_REQUEST['d_EvMatchArrowsNo_'.$n])) {
					if(intval($_REQUEST['d_EvMatchArrowsNo_'.$n])) {
						// bit is set!
						$BitMask= ($BitMask | pow(2,$n));
					} else {
						$BitMask= ($BitMask & ~pow(2,$n));
					}
				}
			}
			//print $BitMask;Exit;
			$query=array();
			$query[] = "EvMatchArrowsNo=" . StrSafe_DB($BitMask);
			if(($tmp=abs(intval($_REQUEST['ElimSO']))) <= $MaxSOString) {
				$query[] = "EvElimSO=$tmp";
			} else {
				$ErrMsg['Elim']['SO']=get_text('TooManyElimSO', 'Tournament');
			}
			if(($tmp=abs(intval($_REQUEST['FinSO']))) <= $MaxSOString) {
				$query[] = "EvFinSO=$tmp";
			} else {
				$ErrMsg['Fin']['SO']=get_text('TooManyElimSO', 'Tournament');
			}
			if(($tmpEnds=abs(intval($_REQUEST['ElimEnds']))) * ($tmpArrows=abs(intval($_REQUEST['ElimArrows']))) <= $MaxArrowString) {
				$query[] = "EvElimEnds=$tmpEnds";
				$query[] = "EvElimArrows=$tmpArrows";
			} else {
				$ErrMsg['Elim']['Arrows']=get_text('TooManyElimArrows', 'Tournament');
			}
			if(($tmpEnds=abs(intval($_REQUEST['FinEnds']))) * ($tmpArrows=abs(intval($_REQUEST['FinArrows']))) <= $MaxArrowString) {
				$query[] = "EvFinEnds=$tmpEnds";
				$query[] = "EvFinArrows=$tmpArrows";
			} else {
				$ErrMsg['Fin']['Arrows']=get_text('TooManyElimArrows', 'Tournament');
			}
			$Update = "UPDATE Events SET " . implode(', ', $query)
				. " WHERE EvCode=" . StrSafe_DB($_REQUEST['d_Event2Set']) . " AND EvTeamEvent='$Teams' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']);
			$Rs=safe_w_sql($Update);
		}
	}

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
		);

	include('Common/Templates/head.php');
?>
<div align="center">
<div class="medium">
<form name="Frm" method="post" action="">
<input type="hidden" name="Command" value=""/>
<table class="Tabella">
<tr><th class="Title" colspan="2"><?php print get_text('ManMatchArr4Phase','Tournament'); ?> (<?php echo get_text($Teams?'TeamEventList':'IndEventList'); ?>)</th></tr>
<tr><th colspan="2"><?php print get_text('FilterRules'); ?></th></tr>
<tr class="Spacer"><td colspan="2"></td></tr>
<tr>
<th class="TitleLeft" width="15%"><?php print get_text('Event');?></th>
<td>
<?php
	$Select = "SELECT EvCode,EvEventName 
		FROM Events 
		WHERE EvTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='$Teams' AND EvFinalFirstPhase!=0 
		ORDER BY EvProgr ASC ";
	$Rs=safe_r_sql($Select);

	print '<select name="d_Event" id="d_Event">';
	if (safe_num_rows($Rs)>0) {
		while ($Row=safe_fetch($Rs)) {
			print '<option value="' . $Row->EvCode . '"' . (isset($_REQUEST['d_Event']) && $_REQUEST['d_Event']==$Row->EvCode ? ' selected' : '') . '>' . $Row->EvCode . ' - ' . get_text($Row->EvEventName,'','',true) . '</option>';
		}
	}
	print '</select>';
?>
&nbsp;<input type="submit" value="<?php print get_text('CmdOk');?>" onclick="document.Frm.Command.value='OK'"><div id="idOutput"></div>
</td>
</tr>
<tr class="Spacer"><td colspan="2"></td></tr>
</table>

<?php
	if (isset($_REQUEST['Command']) && ($_REQUEST['Command']=='OK' || $_REQUEST['Command']=='SAVE')) {
		$Select = "SELECT Events.*, EvFinalFirstPhase AS StartPhase, EvMatchArrowsNo AS BitMask 
			FROM Events 
			WHERE EvTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='$Teams' 
			AND EvCode=" . StrSafe_DB($_REQUEST['d_Event']);
		$Rs = safe_r_sql($Select);

		if (safe_num_rows($Rs)==1) {
			$MyRow=safe_fetch($Rs);
			print '<input type="hidden" name="d_Event2Set" value="' . $_REQUEST['d_Event'] .'">';
			print '<table class="Tabella">';
			print '<tr><th rowspan="3">' . get_text('RoundDefinition') . '</th>'
				. '<th colspan="3">' . get_text('Elimination') . (!empty($ErrMsg['Elim'])?'<div class="red">'.implode('<br/>', $ErrMsg['Elim']).'</div>':'') . '</th>'
				. '<th colspan="3">' . get_text('MenuLM_Final Rounds') . (!empty($ErrMsg['Fin'])?'<div class="red">'.implode('<br/>', $ErrMsg['Fin']).'</div>':'') . '</th></tr>';
			print '<tr>'
				. '<th>' . get_text('Ends', 'Tournament') . '</th>'
				. '<th>' . get_text('Arrows', 'Tournament') . '</th>'
				. '<th>' . get_text('ShotOff', 'Tournament') . '</th>'
				. '<th>' . get_text('Ends', 'Tournament') . '</th>'
				. '<th>' . get_text('Arrows', 'Tournament') . '</th>'
				. '<th>' . get_text('ShotOff', 'Tournament') . '</th></tr>';
			print '<tr>'
				. '<td align="center" width="10%"><input size="3" name="ElimEnds" value="'.$MyRow->EvElimEnds.'"/></td>'
				. '<td align="center" width="10%"><input size="3" name="ElimArrows" value="'.$MyRow->EvElimArrows.'"/></td>'
				. '<td align="center" width="10%"><input size="3" name="ElimSO" value="'.$MyRow->EvElimSO.'"/></td>'
				. '<td align="center" width="10%"><input size="3" name="FinEnds" value="'.$MyRow->EvFinEnds.'"/></td>'
				. '<td align="center" width="10%"><input size="3" name="FinArrows" value="'.$MyRow->EvFinArrows.'"/></td>'
				. '<td align="center" width="10%"><input size="3" name="FinSO" value="'.$MyRow->EvFinSO.'"/></td>'
				. '</tr>';
			print '<tr><th>' . get_text('Phase') . '</th><th colspan="3">' . get_text('Arr4Set', 'Tournament') . '</th><th colspan="3">' . get_text('Arr4Set', 'Tournament') . '</th></tr>';

			for ($CurPhase=$MyRow->StartPhase;$CurPhase>=0;($CurPhase>1 ? $CurPhase = valueFirstPhase($CurPhase)/2 : --$CurPhase))
			{
				print '<tr>';
				print '<td class="Center">' . get_text(namePhase($MyRow->StartPhase, $CurPhase) . '_Phase') . '</td>';
				print '<td class="Center" colspan="3">';
			// Estraggo il bit corrispondete alla fase
				$Bit = ($CurPhase>0 ? 2*bitwisePhaseId($CurPhase) : 1);
				$Value = (($Bit & $MyRow->BitMask)==$Bit ? 1 : 0);
				$e=log($Bit,2);  // esponente di 2 per ottenere la fase
				print '<input type="radio" name="d_EvMatchArrowsNo_' . $e . '"' . ($Value==1 ? ' checked="checked"' : '') . ' value="1">';
				print '</td>';
				print '<td class="Center" colspan="3">';
				print '<input type="radio" name="d_EvMatchArrowsNo_' . $e . '"' . ($Value==0 ? ' checked="checked"' : '') . ' value="0">';
				print '</td>';
				print '</tr>';
			}

			print '<tr><td colspan="7" class="Center"><input type="submit" value="' . get_text('CmdSave') . '" onclick="document.Frm.Command.value=\'SAVE\'">&nbsp;<input type="reset" value="' . get_text('CmdCancel') . '">';
			print '</table>';
		}
	}
?>
</form>
</div>
</div>
<?php
	include('Common/Templates/tail.php');
?>