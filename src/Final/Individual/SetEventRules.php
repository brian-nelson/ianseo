<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
if (!CheckTourSession() || !isset($_REQUEST['EvCode'])) printCrackError();
checkACL(AclCompetition, AclReadWrite);
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');

$JS_SCRIPT=array(
    //'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Individual/Fun_AJAX_SetEventRules.js"></script>',
    //'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
    //'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Individual/Fun_JS.js"></script>',
    );

include('Common/Templates/head.php');

echo '<div align="center">';
echo '<div class="medium">';
echo '<table class="Tabella" id="MyTable">';
echo '<tr><th class="Title" colspan="4">'. get_text('EventClass') . '</th></tr>';
echo '<tr class="Divider"><td colspan="4"></td></tr>';

$Select = "SELECT * FROM Events WHERE EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
$RsEv = safe_r_sql($Select);

if (safe_num_rows($RsEv)==1 and $RowEv=safe_fetch($RsEv)) {

    $Select
        = "SELECT * "
        . "FROM Divisions "
        . "WHERE DivTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND DivAthlete=1 "
        . "ORDER BY DivViewOrder ASC ";
    $RsSel = safe_r_sql($Select);
    $ComboDiv = '<select name="New_EcDivision" id="New_EcDivision" multiple="multiple" size="'.min(15,safe_num_rows($RsSel)+2).'">';
    if (safe_num_rows($RsSel)>0) {
        while ($Row=safe_fetch($RsSel))
            $ComboDiv.= '<option value="' . $Row->DivId . '">' . $Row->DivId . ' - ' . $Row->DivDescription . '</option>';
    }
    $ComboDiv.= '</select>';

    $Select
        = "SELECT * "
        . "FROM Classes "
        . "WHERE ClTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND ClAthlete=1 "
        . "ORDER BY ClViewOrder ASC ";
    $RsSel = safe_r_sql($Select);
    $ComboCl = '<select name="New_EcClass" id="New_EcClass" multiple="multiple" size="'.min(15,safe_num_rows($RsSel)+2).'">';
    if (safe_num_rows($RsSel)>0) {
        while ($Row=safe_fetch($RsSel))
            $ComboCl.= '<option value="' . $Row->ClId . '">' . $Row->ClId . ' - ' . $Row->ClDescription . '</option>';
    }
    $ComboCl.= '</select>';


    $Select
        = "SELECT ScId, ScDescription, ScViewOrder  "
        . "FROM SubClass "
        . "WHERE ScTournament = " . StrSafe_DB($_SESSION['TourId'])
        . "ORDER BY ScViewOrder ASC ";
    $RsSel = safe_r_sql($Select);
    $ComboSubCl = '<select name="New_EcSubClass" id="New_EcSubClass" multiple="multiple" disabled="disabled" size="'.min(15,safe_num_rows($RsSel)+2).'">';
    if (safe_num_rows($RsSel)>0) {
        while ($Row=safe_fetch($RsSel))
            $ComboSubCl.= '<option value="' . $Row->ScId . '">' . $Row->ScId  . ' - ' . $Row->ScDescription . '</option>';
    }
    $ComboSubCl.= '</select>';

    echo '<tr><td class="Title" colspan="4">'.get_text($RowEv->EvEventName,'','',true).'</td></tr>';
    echo '<tr>';
    echo '<th width="30%">'.get_text('Division').'</th>';
    echo '<th width="30%">'.get_text('Class').'</th>';
    echo '<th width="30%">'.get_text('SubClass','Tournament').'</th>';
    echo '<th width="10%">&nbsp;</th>';
    echo '</tr>';

    $Select = "SELECT * FROM EventClass 
        WHERE EcCode=" . StrSafe_DB($RowEv->EvCode) . " AND EcTeamEvent='0' AND EcTournament=" . StrSafe_DB($_SESSION['TourId']) . " 
        ORDER BY EcDivision,EcClass,EcSubClass ";
    $Rs=safe_r_sql($Select);

	echo '<tbody id="tbody">';
    if (safe_num_rows($Rs)>0) {
        while ($MyRow=safe_fetch($Rs)) {
            print '<tr id="Row_' . $RowEv->EvCode . '_' . $MyRow->EcDivision . $MyRow->EcClass . $MyRow->EcSubClass . '">';
            print '<td class="Center">' . $MyRow->EcDivision . '</td>';
            print '<td class="Center">' . $MyRow->EcClass . '</td>';
            print '<td class="Center">' . $MyRow->EcSubClass . '</td>';
            print '<td class="Center">';
            print '<img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" border="0" alt="Delete" title="Delete" onclick="DeleteEventRule(\'' . $RowEv->EvCode . '\',\'' . $MyRow->EcDivision . '\',\'' . $MyRow->EcClass . '\',\'' . $MyRow->EcSubClass . '\')">';
            print '</td>';
            print '</tr>';
        }
    }
    echo '</tbody>';
    print '<tr id="RowDiv" class="Divider"><td colspan="4"></td></tr>';

    echo '</table>';

    echo '<br/>';
    echo '<table class="Tabella">';
    echo '<tr><td colspan="4" class="Center">'.get_text('PressCtrl2SelectAll').'</td></tr>';
    echo '<tr id="NewRow">';
    echo '<td style="width:30%;" class="Center" valign="top">'.$ComboDiv.'<br/><br/>
        <a class="Link" href="javascript:SelectAllOpt(\'New_EcDivision\');">'.get_text('SelectAll').'</a></td>';
    echo '<td style="width:30%;" class="Center" valign="top">'.$ComboCl.'<br/><br/>
        <a class="Link" href="javascript:SelectAllOpt(\'New_EcClass\');">'.get_text('SelectAll').'</a></td>';
    echo '<td style="width:30%;" class="Center" valign="top">'.$ComboSubCl.'<br/><br/>
        <input type="checkbox" id="enableSubClass" onclick="enableSubclass(this)">'.get_text('UseSubClasses','Tournament').'</td>';
    echo '<td style="width:10%;" class="Center" valign="top">
        <input type="button" name="Command" id="Command" value="'.get_text('CmdSave').'" onclick="AddEventRule(\''.$RowEv->EvCode.'\');"></td>';
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
	echo '<th>'.get_text('WaCategory', 'Tournament').'</th>';
	echo '<th>'.get_text('RecordCategory', 'Tournament').'</th>';
	echo '<th>'.get_text('OdfEventCode', 'ODF').'</th>';
    echo '</tr>';

    echo '<tr>';
    echo '<td class="Center"><input type="number" value="'.$RowEv->EvNumQualified.'" id="fld=num&team=0&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
    echo '<td class="Center"><input type="number" value="'.$RowEv->EvFirstQualified.'" id="fld=first&team=0&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
    echo '<td class="Center"><select onchange="UpdateData(this)" id="fld=medal&team=0&event='.$_REQUEST['EvCode'].'">
            <option value="1" '.($RowEv->EvMedals ? ' selected="selected"' : '').'>'.get_text('Yes').'</option>
            <option value="0" '.($RowEv->EvMedals ? '' : ' selected="selected"').'>'.get_text('No').'</option>
        </select></td>';
    echo '<td class="Center"><select name="ParentRule" onchange="UpdateData(this)" id="fld=parent&team=0&event='.$_REQUEST['EvCode'].'">';
    echo '<option value="">'.get_text('Select', 'Tournament').'</option>';
    $q=safe_r_sql("select EvCode, EvEventName from Events where EvTeamEvent=0 and EvFinalFirstPhase>$RowEv->EvFinalFirstPhase and EvCode!='$RowEv->EvCode' and EvTournament={$_SESSION['TourId']}");
    while($r=safe_fetch($q)) {
        echo '<option value="'.$r->EvCode.'" '.($RowEv->EvCodeParent==$r->EvCode ? ' selected="selected"' : '').'>'.$r->EvCode.' - '.$r->EvEventName.'</option>';
    }
    echo '</select></td>';
	echo '<td class="Center"><input type="number" value="'.$RowEv->EvWinnerFinalRank.'" id="fld=final&team=0&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
	echo '<td class="Center"><input type="text" value="'.$RowEv->EvWaCategory.'" id="fld=wacat&team=0&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
	echo '<td class="Center"><input type="text" value="'.$RowEv->EvRecCategory.'" id="fld=reccat&team=0&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
	echo '<td class="Center"><input type="text" value="'.$RowEv->EvOdfCode.'" id="fld=odfcode&team=0&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
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
}

include('Common/Templates/tail.php');
