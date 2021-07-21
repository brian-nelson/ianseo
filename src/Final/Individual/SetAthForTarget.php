<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	CheckTourSession(true);
    checkACL(AclCompetition, AclReadWrite);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Phases.inc.php');

	if (isset($_REQUEST['Command'])) {
		if (!IsBlocked(BIT_BLOCK_TOURDATA) && $_REQUEST['Command']=='SAVE') {
			$BitMask=0;
			$MatchMask=0;
			foreach($_REQUEST as $Key => $Value) {
				if (substr($Key,0,19)=='d_EvFinalAthTarget_') {
					list(,,$e)=explode('_',$Key);
					$BitMask+=($Value*pow(2,$e));
                    /*
                     * Questa parte potrebbe essere risolta tramite una sola query.
                     * Occorre usare gli operatori bit a bit di mysql per trovare
                     * le fasi con il Bit=1
                     */
					if ($Value==1) {
						$Phase = floor(pow(2,$e)/2);
						$Update = "UPDATE FinSchedule AS fs1 
						    LEFT JOIN FinSchedule AS fs2 ON fs1.FSMatchNo=(fs2.FSMatchNo-1) AND fs1.FSEvent=fs2.FSEvent AND fs1.FSTeamEvent=fs2.FSTeamEvent AND fs1.FSTournament=fs2.FSTournament 
							SET fs2.FSTarget = fs1.FSTarget 
							WHERE fs1.FSEvent=" . StrSafe_DB($_REQUEST['d_Event2Set']) . " AND fs1.FSTeamEvent='0' AND 
							fs1.FSTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (fs1.FSMatchNo% 2)=0 
							AND fs1.FSMatchNO IN(SELECT GrMatchNo FROM Grids WHERE GrPhase=" . StrSafe_DB($Phase) . ")";
						$Rs=safe_w_sql($Update);
					}
				} elseif (substr($Key,0,25)=='d_EvMatchMultipleMatches_') {
					list(,,$e)=explode('_',$Key);
					$MatchMask+=($Value*pow(2,$e));
				}
			}
			$Update = "UPDATE Events SET EvFinalAthTarget=" . StrSafe_DB($BitMask) . ", EvMatchMultipleMatches=" . StrSafe_DB($MatchMask) . " 
				WHERE EvCode=" . StrSafe_DB($_REQUEST['d_Event2Set']) . " AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']);
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
<tr><th class="Title" colspan="2"><?php print get_text('ManFinAthTargetInd'); ?></th></tr>
<tr><th colspan="2"><?php print get_text('FilterRules'); ?></th></tr>
<tr class="Spacer"><td colspan="2"></td></tr>
<tr>
<th class="TitleLeft" width="15%"><?php print get_text('Event');?></th>
<td>
<?php
	$Select = "SELECT EvCode,EvEventName
		FROM Events
		WHERE EvTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='0' AND EvFinalFirstPhase!=0
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
&nbsp;<input type="submit" value="<?php print get_text('CmdOk');?>" onclick="document.Frm.Command.value='OK'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo $CFG->ROOT_DIR ?>Final/FopSetup.php" target="PrintOut" clasS="Link"><?php echo get_text('FopSetup'); ?></a><div id="idOutput"></div>
</td>
</tr>
<tr class="Spacer"><td colspan="2"></td></tr>
</table>

<?php
	if (isset($_REQUEST['Command']) && ($_REQUEST['Command']=='OK' || $_REQUEST['Command']=='SAVE')) {
		$Select	= "SELECT EvFinalFirstPhase AS StartPhase, EvFinalAthTarget AS BitMask, EvMatchMultipleMatches
			FROM Events 
			WHERE EvTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='0' 
			AND EvCode=" . StrSafe_DB($_REQUEST['d_Event']);
		$Rs = safe_r_sql($Select);

		if (safe_num_rows($Rs)==1) {
			$MyRow=safe_fetch($Rs);
			print '<input type="hidden" name="d_Event2Set" value="' . $_REQUEST['d_Event'] .'">';
			print '<table class="Tabella">';
			print '<tr><th width="33%">' . get_text('Phase') . '</th><th width="33%">' . get_text('Ath4Target', 'Tournament') . '</th><th width="33%">' . get_text('Match4Target', 'Tournament') . '</th></tr>';

			for ($CurPhase=$MyRow->StartPhase;$CurPhase>=0;($CurPhase>1 ? $CurPhase = valueFirstPhase($CurPhase)/2 : --$CurPhase)) {
				$Bit = ($CurPhase>0 ? 2*bitwisePhaseId($CurPhase) : 1);
				$e=log($Bit,2);  // esponente di 2 per ottenere la fase
				print '<tr>';
				print '<td class="Center">' . get_text(namePhase($MyRow->StartPhase, $CurPhase) . '_Phase') . '</td>';
				print '<td class="Center">';
			// Estraggo il bit corrispondete alla fase
				$Value = (($Bit & $MyRow->BitMask)==$Bit ? 1 : 0);
				print '<input type="radio" name="d_EvFinalAthTarget_' . $e . '"' . ($Value==0 ? ' checked="checked"' : '') . ' value="0">1';
				print '&nbsp;&nbsp;&nbsp;';
				print '<input type="radio" name="d_EvFinalAthTarget_' . $e . '"' . ($Value==1 ? ' checked="checked"' : '') . ' value="1">2';
				print '</td>';
				print '<td class="Center">';
			// Estraggo il bit corrispondete alla fase
				$Value = (($Bit & $MyRow->EvMatchMultipleMatches)==$Bit ? 1 : 0);
				print '<input type="radio" name="d_EvMatchMultipleMatches_' . $e . '"' . ($Value==0 ? ' checked="checked"' : '') . ' value="0">1';
				print '&nbsp;&nbsp;&nbsp;';
				print '<input type="radio" name="d_EvMatchMultipleMatches_' . $e . '"' . ($Value==1 ? ' checked="checked"' : '') . ' value="1">2';
				print '</td>';
				print '</tr>';
			}

			print '<tr><td colspan="3" class="Center"><input type="submit" value="' . get_text('CmdSave') . '" onclick="document.Frm.Command.value=\'SAVE\'">&nbsp;<input type="reset" value="' . get_text('CmdCancel') . '">';
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