<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
CheckTourSession(true);
checkACL(AclCompetition, AclReadWrite);

require_once('Common/Lib/CommonLib.php');
//require_once('Common/Lib/ArrTargets.inc.php');
//require_once('Common/Fun_Various.inc.php');

if(empty($_REQUEST['Event'])) {
    cd_redirect('ListEvent.php');
}

$q=safe_r_sql("SELECT * FROM Events 
  INNER JOIN Targets ON EvFinalTargetType=TarId 
  WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='0' and EvCode=".StrSafe_DB($_REQUEST['Event'])."
  ORDER BY EvProgr ASC,EvCode ASC, EvTeamEvent ASC ");

$EVENT=safe_fetch($q);

if(!$EVENT) {
    // Wrong Event, redirect to normal page
    cd_redirect('ListEvent.php');
}

$JS_SCRIPT=array(
    phpVars2js(array(
        'StrResetElimError' => get_text('ResetElimError', 'Tournament'),
        'StrResetElim' => get_text('ChangeElimWarning'),
        'EVENT' => $_REQUEST['Event'],
        )),
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/jQuery/jquery-2.1.4.min.js"></script>',
    '<script type="text/javascript" src="ListEvents-Eliminations.js"></script>',
    );

include('Common/Templates/head.php');

echo '<table class="Tabella" style="margin:auto;width:auto;">';
echo '<tr><th class="Title" colspan="2">'.get_text('EliminationType', 'Tournament').'</th></tr>';
echo '<tr class="Divider"><td colspan="2"></td></tr>';

echo '<tr>';
echo '<th>'.get_text('Progr').'</th>';
echo '<td>'.$EVENT->EvProgr.'</td>';
echo '</tr>';

echo '<tr>';
echo '<th>'.get_text('EvCode').'</th>';
echo '<td>'.$EVENT->EvCode.'</td>';
echo '</tr>';

echo '<tr>';
echo '<th>'.get_text('EvName').'</th>';
echo '<td>'.$EVENT->EvEventName.'</td>';
echo '</tr>';

echo '<tr>';
echo '<th>'.get_text('EliminationType', 'Tournament').'</th>';
echo '<td><input type="hidden" id="oldGetEliminationSelect" value="'.($EVENT->EvElimType ? '999':$EVENT->EvElimType).'"><select onchange="GetFields(this.value)" id="GetEliminationSelect">'.$EVENT->EvElimType.'';
//echo '<option value="0">'.get_text('Select', 'Tournament').'</option>';
echo '<option value="999"'.($EVENT->EvElimType==0 OR ($EVENT->EvElimType==0 AND $EVENT->EvElim2==0) ? ' selected="selected"' : '').'>'.get_text('Eliminations_0').'</option>';
echo '<option value="1"'.($EVENT->EvElimType==1 ? ' selected="selected"' : '').'>'.get_text('Eliminations_1').'</option>';
echo '<option value="2"'.($EVENT->EvElimType==2 ? ' selected="selected"' : '').'>'.get_text('Eliminations_2').'</option>';
echo '<option value="3"'.($EVENT->EvElimType==3 ? ' selected="selected"' : '').'>'.get_text('WG_Pool2').'</option>';
echo '<option value="4"'.($EVENT->EvElimType==4 ? ' selected="selected"' : '').'>'.get_text('WA_Pool4').'</option>';
echo '';
echo '</select>';
echo '</td>';
echo '</tr>';

echo '<tbody id="ElimType"></tbody>';
echo '<tr class="Divider"><td colspan="2"></td></tr>';
echo '<tr><td class="Center" colspan="2"><a class="Link" href="ListEvents.php">' .get_text('Back') . '</a></td></tr>';
echo '</table>';

include('Common/Templates/tail.php');

