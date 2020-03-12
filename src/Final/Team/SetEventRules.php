<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
if (!CheckTourSession() || !isset($_REQUEST['EvCode'])) printCrackError();
checkACL(AclCompetition, AclReadWrite);
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Qualification/Fun_Qualification.local.inc.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Common/Fun_Various.inc.php');

if (isset($_REQUEST['DelRow']) && !IsBlocked(BIT_BLOCK_TOURDATA)) {
    list($EcClass,$EcDivision,$EcSubClass) = explode('~',$_REQUEST['DelRow']);
    //print $EcClass . ' - ' . $EcDivision . '<br>';exit;
    $Delete
        = "DELETE FROM EventClass "
        . "WHERE EcCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EcClass=" . StrSafe_DB($EcClass) . " AND EcSubClass=" . StrSafe_DB($EcSubClass) . " AND EcDivision=" . StrSafe_DB($EcDivision) . " "
        . "AND EcTeamEvent!='0' AND EcTournament=" . StrSafe_DB($_SESSION['TourId']) .  " ";
        //print $Delete;exit;
    $Rs=safe_w_sql($Delete);

	// calcolo il numero massimo di persone nel team
    calcMaxTeamPerson(array($_REQUEST['EvCode']));

	// cancello le righe di Team per l'evento passato
    $queries[] = "DELETE FROM Teams 
        WHERE TeTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TeFinEvent=1 AND TeEvent=" . StrSafe_DB($_REQUEST['EvCode']) . " ";

	// cancello i nomi
    $queries[] = "DELETE FROM TeamComponent 
        WHERE TcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TcFinEvent=1 AND TcEvent=". StrSafe_DB($_REQUEST['EvCode']) . " ";

	// cancello i nomi fin
    $queries[] = "DELETE FROM TeamFinComponent 
        WHERE TfcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfcEvent=" .  StrSafe_DB($_REQUEST['EvCode']) . " ";

	// elimino le griglie
    $queries[] = "UPDATE TeamFinals SET 
          TfTeam=0, TfSubTeam=0, TfScore=0, TfSetScore=0, TfSetPoints='', TfSetPointsByEnd='', TfWinnerSet=0, TfTie=0, 
          TfArrowstring='', TfTiebreak='', TfArrowPosition='', TfTiePosition='', TfWinLose=0, 
          TfDateTime=NOW(), TfLive=0, TfStatus=0, TfShootFirst=0, TfShootingArchers='', TfConfirmed=0, TfNotes='' 
          WHERE TfEvent=" . StrSafe_DB($_REQUEST['EvCode']) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";

    foreach ($queries as $q) {
        safe_w_sql($q);
    }

    safe_w_sql("UPDATE Events SET EvTourRules='' where EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EvTeamEvent='1' AND EvTournament = " . StrSafe_DB($_SESSION['TourId']));

	// reset shootoff
    ResetShootoff($_REQUEST['EvCode'],1,0);

	// teamabs
    MakeTeamsAbs(null,null,null);

    header('Location: SetEventRules.php?EvCode=' . $_REQUEST['EvCode']);
    exit;
}

$JS_SCRIPT=array(
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/jQuery/jquery-3.2.1.min.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Team/Fun_AJAX_SetEventRules.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Team/Fun_JS.js"></script>',
);

$PAGE_TITLE=get_text('TeamDefinition');

include('Common/Templates/head.php');
?>
<div align="center">
<div class="medium">
<table class="Tabella" id="MyTable">
<tbody id="tbody">
<tr><th class="Title" colspan="6"><?php print get_text('TeamDefinition');?></th></tr>
<tr class="Divider"><td colspan="6"></td></tr>
<?php

$Select = "SELECT * FROM Events WHERE EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$RsEv = safe_r_sql($Select);

	$RowEv=null;

	if (safe_num_rows($RsEv)==1) {
		$RowEv=safe_fetch($RsEv);
?>
<tr><td class="Title" colspan="6"><?php print get_text($RowEv->EvEventName,'','',true);?></td></tr>
<tr>
<th width="20%"><?php print get_text('Number');?></th>
<th width="20%"><?php print get_text('Division');?></th>
<th width="20%"><?php print get_text('Class');?></th>
<th width="20%"><?php print get_text('SubClass','Tournament');?></th>
<th width="10%">&nbsp;</th>
<th width="10%">&nbsp;</th>
</tr>
<?php
		$Select
			= "SELECT ec.*,Quanti "
			. "FROM EventClass AS ec INNER JOIN("
			. "SELECT COUNT(*) AS Quanti,EcCode,EcTeamEvent,EcTournament "
			. "FROM EventClass "
			. "WHERE EcCode=" . StrSafe_DB($RowEv->EvCode) . " AND EcTeamEvent!='0' AND EcTournament= " . StrSafe_DB($_SESSION['TourId']) . " "
			. "GROUP BY EcCode,EcTeamEvent,EcTournament"
			. ") AS sq ON ec.EcCode=sq.EcCode AND ec.EcTeamEvent=sq.EcTeamEvent AND ec.EcTournament=sq.EcTournament "
			. "WHERE ec.EcCode=" . StrSafe_DB($RowEv->EvCode) . " AND ec.EcTeamEvent<>'0' AND ec.EcTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "ORDER BY EcTeamEvent ASC,EcDivision,EcClass ";
		$Rs=safe_r_sql($Select);
 		//print $Select;exit;
		if (safe_num_rows($Rs)>0) {
			$MyGroup = -1;

			while ($MyRow=safe_fetch($Rs)) {
				if ($MyGroup!=$MyRow->EcTeamEvent && $MyGroup!=-1)
					print '<tr id="Div_' . $MyRow->EcCode . '_' . $MyRow->EcTeamEvent . '" class="Divider"><td colspan="6"></td></tr>' . "\n";

				print '<tr id="Row_' . $MyRow->EcCode . '_' . $MyRow->EcTeamEvent . '_' . $MyRow->EcDivision . '_' . $MyRow->EcClass . '_' . $MyRow->EcSubClass . '">';
				if ($MyGroup!=$MyRow->EcTeamEvent)
					print '<td rowspan="' . $MyRow->Quanti . '" class="Center">' . $MyRow->EcNumber . '</td>';
				print '<td class="Center">' . $MyRow->EcDivision . '</td>';
				print '<td class="Center">' . $MyRow->EcClass . '</td>';
                print '<td class="Center">' . $MyRow->EcSubClass . '</td>';
				$Row2Del = $MyRow->EcClass . '~' . $MyRow->EcDivision . '~' . $MyRow->EcSubClass; // l'altro pezzo di chiave lo ricavo con gli altri parametri e la sessione
				print '<td class="Center"><a href="' . $_SERVER['PHP_SELF'] . '?EvCode=' . $MyRow->EcCode . '&amp;DelRow=' . $Row2Del . '"><img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" border="0" alt="#" title="#"></a></td>';
				if ($MyGroup!=$MyRow->EcTeamEvent) {
					print '<td rowspan="' . $MyRow->Quanti . '" class="Center">';
					print '<a href="javascript:DeleteEventRule(\'' . $MyRow->EcCode .'\',\'' . $MyRow->EcTeamEvent . '\');"><img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" border="0" alt="#" title="#"></a>';
					print '</td>';
				}
				print '</tr>';
				$MyGroup=$MyRow->EcTeamEvent;
			}
		}
	}
?>
</tbody>
</table>
<br>

<table class="Tabella">
<tr><td class="Center">
<?php echo get_text('MixedTeamEvent') ?>&nbsp;
<select name="d_EvMixedTeam" id="d_EvMixedTeam" onChange="SetMixedTeam('<?php print $RowEv->EvCode; ?>');">
	<option value="0"<?php print ($RowEv!=null && $RowEv->EvMixedTeam==0 ? ' selected' : ''); ?>><?php print get_text('No'); ?></option>
	<option value="1"<?php print ($RowEv!=null && $RowEv->EvMixedTeam==1 ? ' selected' : ''); ?>><?php print get_text('Yes'); ?></option>
</select>
</td></tr>
<tr><td class="Center">
<?php echo get_text('AllowPartialTeams') ?>&nbsp;
<select name="d_EvPartialTeam" id="d_EvPartialTeam" onChange="SetPartialTeam('<?php print $RowEv->EvCode; ?>');">
	<option value="0"<?php print ($RowEv!=null && $RowEv->EvPartialTeam==0 ? ' selected' : ''); ?>><?php print get_text('No'); ?></option>
	<option value="1"<?php print ($RowEv!=null && $RowEv->EvPartialTeam==1 ? ' selected' : ''); ?>><?php print get_text('Yes'); ?></option>
</select>
</td></tr>
<tr><td class="Center">
<?php echo get_text('AllowMultiTeam') ?>&nbsp;
<select name="d_EvMultiTeam" id="d_EvMultiTeam" onChange="SetMultiTeam('<?php print $RowEv->EvCode; ?>');">
	<option value="0"<?php print ($RowEv!=null && $RowEv->EvMultiTeam==0 ? ' selected' : ''); ?>><?php print get_text('No'); ?></option>
	<option value="1"<?php print ($RowEv!=null && $RowEv->EvMultiTeam==1 ? ' selected' : ''); ?>><?php print get_text('Yes'); ?></option>
</select>
</td></tr>

<tr>
	<td class="Center">
		<?php print get_text('TeamCreationMode','Tournament');?>&nbsp;
		<?php
			$comboTeamCreationMode=ComboFromRs(
				array(
					array('id'=>0,'descr'=>get_text('TeamCreationMode_0','Tournament')),
					array('id'=>1,'descr'=>get_text('TeamCreationMode_1','Tournament')),
					array('id'=>2,'descr'=>get_text('TeamCreationMode_2','Tournament')),
					array('id'=>3,'descr'=>get_text('TeamCreationMode_3','Tournament')),
				),
				'id',
				'descr',
				1,
				$RowEv->EvTeamCreationMode,
				null,
				'd_EvTeamCreationMode',
				'd_EvTeamCreationMode',
				array(
					'onChange'=>'SetTeamCreationMode(\'' . $RowEv->EvCode. '\');'
				)
			);

			print $comboTeamCreationMode;
		?>
	</td>
</tr>

</table>
<br/>

<table class="Tabella">
<tr><td colspan="6" class="Center"><?php print get_text('PressCtrl2SelectAll'); ?></td></tr>
<tr>
<td width="20%" class="Center" valign="top">
<input type="text" name="New_EcNumber" id="New_EcNumber" size="3" maxlength="3" value="">
</td>
<td width="20%" class="Center" valign="top">
<?php

	$Select = "SELECT * "
		. "FROM Divisions "
		. "WHERE DivTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND DivAthlete=1 "
		. "ORDER BY DivViewOrder ASC ";
	$RsSel = safe_r_sql($Select);
    $ComboDiv = '<select name="New_EcDivision" id="New_EcDivision" multiple="multiple" size="'.min(15,safe_num_rows($RsSel)+2).'">';
	if (safe_num_rows($RsSel)>0) {
		while ($Row=safe_fetch($RsSel)) {
			$ComboDiv.= '<option value="' . $Row->DivId . '">' . $Row->DivId . ' - ' . $Row->DivDescription . '</option>';
		}
	}

	$ComboDiv.= '</select>';
	print $ComboDiv;
	print '<br><br><a class="Link" href="javascript:SelectAllOpt(\'New_EcDivision\');">' . get_text('SelectAll') . '</a>';
?>
</td>
<td width="20%" class="Center" valign="top">
<?php
	$Select = "SELECT * "
		. "FROM Classes "
		. "WHERE ClTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND ClAthlete=1 "
		. "ORDER BY ClViewOrder ASC ";
	$RsSel = safe_r_sql($Select);
    $ComboCl = '<select name="New_EcClass" id="New_EcClass" multiple="multiple" size="'.min(15,safe_num_rows($RsSel)+2).'">';
	if (safe_num_rows($RsSel)>0) {
		while ($Row=safe_fetch($RsSel)) {
			$ComboCl.= '<option value="' . $Row->ClId . '">' . $Row->ClId . ' - ' . $Row->ClDescription . '</option>';
		}
	}

	$ComboCl.= '</select>';
	print $ComboCl;
	print '<br><br><a class="Link" href="javascript:SelectAllOpt(\'New_EcClass\');">' . get_text('SelectAll') . '</a>';
?>
</td>
<td width="20%" class="Center" valign="top">
    <?php

    $Select
        = "SELECT  ScId, ScDescription, ScViewOrder "
        . "FROM SubClass "
        . "WHERE ScTournament = " . StrSafe_DB($_SESSION['TourId'])
        . "ORDER BY ScViewOrder ASC ";
    $RsSel = safe_r_sql($Select);
    $ComboSubCl = '<select name="New_EcSubClass" id="New_EcSubClass" multiple="multiple" disabled="disabled" size="'.min(15,safe_num_rows($RsSel)+2).'">';
    if (safe_num_rows($RsSel)>0) {
        while ($Row=safe_fetch($RsSel)) {
            $ComboSubCl.= '<option value="' . $Row->ScId . '">' . $Row->ScId . ' - ' . $Row->ScDescription . '</option>';
        }
    }

    $ComboSubCl.= '</select>';
    print $ComboSubCl;
    print '<br><br><input type="checkbox" id="enableSubClass" onclick="enableSubclass(this)">'. get_text('UseSubClasses','Tournament');

echo '</td>';
echo '<td width="20%" colspan="2" class="Center" valign="top">';
echo '<input type="button" name="Command" id="Command" value="'.get_text('CmdSave').'" onclick="javascript:AddEventRule(\''.$RowEv->EvCode.'\');">';
echo '</td>';
echo '</tr>';
echo '</table>';

echo '<br/>';
echo '<table class="Tabella">';
    echo '<tr id="AdvancedButton"><th colspan="4"><input type="button" onclick="showAdvanced()" value="'.get_text('Advanced').'"></th></tr>';
    echo '<tbody id="Advanced" style="display: none;">';
    echo '<tr>';
        echo '<th>'.get_text('EventNumQualified', 'Tournament').'</th>';
        echo '<th>'.get_text('EventStartPosition', 'Tournament').'</th>';
        echo '<th>'.get_text('EventHasMedal', 'Tournament').'</th>';
        echo '<th>'.get_text('EventParentCode', 'Tournament').'</th>';
        echo '<th>'.get_text('EventWinnerFinalRank', 'Tournament').'</th>';
        echo '<th>'.get_text('MaxTeamPersons', 'Tournament').'</th>';
        echo '<th>'.get_text('WaCategory', 'Tournament').'</th>';
        echo '<th>'.get_text('RecordCategory', 'Tournament').'</th>';
        echo '</tr>';

    echo '<tr>';
        echo '<td class="Center"><input type="number" value="'.$RowEv->EvNumQualified.'" id="fld=num&team=1&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
        echo '<td class="Center"><input type="number" value="'.$RowEv->EvFirstQualified.'" id="fld=first&team=1&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
        echo '<td class="Center"><select onchange="UpdateData(this)" id="fld=medal&team=1&event='.$_REQUEST['EvCode'].'">
                <option value="1" '.($RowEv->EvMedals ? ' selected="selected"' : '').'>'.get_text('Yes').'</option>
                <option value="0" '.($RowEv->EvMedals ? '' : ' selected="selected"').'>'.get_text('No').'</option>
            </select></td>';
        echo '<td class="Center"><select name="ParentRule" onchange="UpdateData(this)" id="fld=parent&team=1&event='.$_REQUEST['EvCode'].'">';
                echo '<option value="">'.get_text('Select', 'Tournament').'</option>';
                $q=safe_r_sql("select EvCode, EvEventName from Events where EvTeamEvent=1 and EvFinalFirstPhase>$RowEv->EvFinalFirstPhase and EvCode!='$RowEv->EvCode' and EvTournament={$_SESSION['TourId']}");
                while($r=safe_fetch($q)) {
                    echo '<option value="'.$r->EvCode.'" '.($RowEv->EvCodeParent==$r->EvCode ? ' selected="selected"' : '').'>'.$r->EvCode.' - '.$r->EvEventName.'</option>';
                }
                echo '</select></td>';
        echo '<td class="Center"><input type="number" value="'.$RowEv->EvWinnerFinalRank.'" id="fld=final&team=1&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
        echo '<td class="Center"><input type="number" value="'.$RowEv->EvMaxTeamPerson.'" id="fld=persons&team=1&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
        echo '<td class="Center"><input type="text" value="'.$RowEv->EvWaCategory.'" id="fld=wacat&team=1&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
        echo '<td class="Center"><input type="text" value="'.$RowEv->EvRecCategory.'" id="fld=reccat&team=1&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
        echo '<td class="Center"><input type="text" value="'.$RowEv->EvOdfCode.'" id="fld=odfcode&team=1&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
        echo '</tr>';
    echo '</tbody>';
    echo '</table>';

echo '<br/>';

echo '<table class="Tabella">';
echo '<tr><td class="Center"><a class="Link" href="ListEvents.php">'.get_text('Back').'</a></td></tr>';
echo '</table>';
echo '<div id="idOutput"></div>';
echo '</div>';
echo '</div>';

include('Common/Templates/tail.php');
