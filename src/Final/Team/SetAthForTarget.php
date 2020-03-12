<?php
    require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    CheckTourSession(true);
    checkACL(AclCompetition, AclReadWrite);
	require_once('Common/Fun_FormatText.inc.php');
    require_once('Common/Fun_Phases.inc.php');

	if (isset($_REQUEST['Command'])) {
		if ($_REQUEST['Command']=='SAVE') {
			$BitMask=0;
			foreach($_REQUEST as $Key => $Value) {
				if (substr($Key,0,19)=='d_EvFinalAthTarget_') {
					list(,,$e)=explode('_',$Key);
					$BitMask+=($Value*pow(2,$e));
					//print $e . ' - ' . $Value . ' - ' . ($Value*pow(2,$e)) . '<br>';

				/*
				 * Questa parte potrebbe essere risolta tramite una sola query.
				 * Occorre usare gli operatori bit a bit di mysql per trovare
				 * le fasi con il Bit=1
				 */
					if ($Value==1) 	{
						$Phase = floor(pow(2,$e)/2);
						//print '--> '. $e . ' - ' . $Phase . '<br>';

						$Update = "UPDATE 
							FinSchedule AS fs1 LEFT JOIN FinSchedule AS fs2 ON fs1.FSMatchNo=(fs2.FSMatchNo-1) AND fs1.FSEvent=fs2.FSEvent AND fs1.FSTeamEvent=fs2.FSTeamEvent AND fs1.FSTournament=fs2.FSTournament 
							SET fs2.FSTarget = fs1.FSTarget 
							WHERE fs1.FSEvent=" . StrSafe_DB($_REQUEST['d_Event2Set']) . " AND fs1.FSTeamEvent='1' AND 
							fs1.FSTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (fs1.FSMatchNo% 2)=0 
							AND fs1.FSMatchNO IN(SELECT GrMatchNo FROM Grids WHERE GrPhase=" . StrSafe_DB($Phase) . ") ";
						$Rs=safe_w_sql($Update);
					}
				}
			}
			$Update	= "UPDATE Events SET EvFinalAthTarget=" . StrSafe_DB($BitMask) . " 
				WHERE EvCode=" . StrSafe_DB($_REQUEST['d_Event2Set']) . " AND EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']);
			$Rs=safe_w_sql($Update);
		}
	}

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
		);

	$PAGE_TITLE=get_text('ManFinAthTargetTeam');

	include('Common/Templates/head.php');
?>
<div align="center">
<div class="medium">
<form name="Frm" method="post" action="">
<input type="hidden" name="Command" value=""/>
<table class="Tabella">
<tr><th class="Title" colspan="2"><?php print get_text('ManFinAthTargetTeam'); ?></th></tr>
<tr><th colspan="2"><?php print get_text('FilterRules'); ?></th></tr>
<tr class="Spacer"><td colspan="2"></td></tr>
<tr>
<th class="TitleLeft" width="15%"><?php print get_text('Event');?></th>
<td>
<?php
	$Select = "SELECT EvCode,EvEventName 
		FROM Events 
		WHERE EvTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='1' 
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
		$Select = "SELECT EvFinalFirstPhase AS StartPhase, EvFinalAthTarget AS BitMask 
			FROM Events 
			WHERE EvTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='1' 
			AND EvCode=" . StrSafe_DB($_REQUEST['d_Event']);
		$Rs = safe_r_sql($Select);

		if (safe_num_rows($Rs)==1) {
			$MyRow=safe_fetch($Rs);
			print '<input type="hidden" name="d_Event2Set" value="' . $_REQUEST['d_Event'] .'">';
			print '<table class="Tabella">';
			print '<tr><th width="50%">' . get_text('Phase') . '</th><th width="50%">' . get_text('Ath4Target', 'Tournament') . '</th></tr>';

			for ($CurPhase=$MyRow->StartPhase;$CurPhase>=0;($CurPhase>1 ? $CurPhase = valueFirstPhase($CurPhase)/2 : --$CurPhase)) {
				print '<tr>';
				print '<td class="Center">' . get_text(namePhase($MyRow->StartPhase, $CurPhase) . '_Phase') . '</td>';
				print '<td class="Center">';
			// Estraggo il bit corrispondete alla fase
                $Bit = ($CurPhase>0 ? 2*bitwisePhaseId($CurPhase) : 1);
                $Value = (($Bit & $MyRow->BitMask)==$Bit ? 1 : 0);
                $e=log($Bit,2);  // esponente di 2 per ottenere la fase
				print '<input type="radio" name="d_EvFinalAthTarget_' . $e . '"' . ($Value==0 ? ' checked="checked"' : '') . ' value="0">1';
				print '&nbsp;&nbsp;&nbsp;';
				print '<input type="radio" name="d_EvFinalAthTarget_' . $e . '"' . ($Value==1 ? ' checked="checked"' : '') . ' value="1">2';
				print '</td>';
				print '</tr>';
			}

			print '<tr><td colspan="2" class="Center"><input type="submit" value="' . get_text('CmdSave') . '" onclick="document.Frm.Command.value=\'SAVE\'">&nbsp;<input type="reset" value="' . get_text('CmdCancel') . '">';
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