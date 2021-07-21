<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Lib/CommonLib.php');

CheckTourSession(true);
checkACL(AclTeams, AclReadWrite);

$PAGE_TITLE=get_text('ChangeComponents');

$JS_SCRIPT = array(phpVars2js(array(
        'ROOT_DIR'=>$CFG->ROOT_DIR,
        'cmdEdit' => get_text('Edit', 'Tournament'),
        'cmdBack' => get_text('Back'),
        'cmdSave' => get_text('CmdSave'),
        'Number' => get_text('Number')
    )),
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-confirm.min.js"></script>',
    '<script type="text/javascript" src="./ChangeComponents.js"></script>',
    '<link href="./ChangeComponents.css" rel="stylesheet" type="text/css">',
    '<link href="'.$CFG->ROOT_DIR.'Common/css/jquery-confirm.min.css" media="screen" rel="stylesheet" type="text/css">',
);
include('Common/Templates/head.php');

echo '<table class="Tabella">';
echo '<thead>' .
    '<tr><th colspan="8" class="Title">'.get_text('ChangeComponents').'</th></tr>'.
    '<tr class="srcControls"><th>' . get_text('Event') . '</th>'.
        '<td colspan="6"><select id="cmbEvent" onchange="populateData()"><option value="0">---</option></select>'.
        '&nbsp;&nbsp;&nbsp;<input type="button" value="'.get_text('CmdCancel').'" onclick="clearCombo(\'cmbEvent\')">'.
        '</td>'.
        '<td rowspan="2" class="evCodeContainer"><a href="" onclick="printTeamComponentForm()"><img src="' . $CFG->ROOT_DIR . 'Common/Images/pdf.gif" alt="' . get_text('TeamComponentForm', 'Tournament') . '" border="0"></a></td>'.
    '</tr>'.
    '<tr class="srcControls"><th>' . get_text('Team') . '</th>'.
        '<td colspan="6"><select id="cmbTeam" onchange="populateData()"><option value="0">---</option></select>'.
        '&nbsp;&nbsp;&nbsp;<input type="button" value="'.get_text('CmdCancel').'" onclick="clearCombo(\'cmbTeam\')">'.
        '</td>'.
    '</tr>'.
    '<tr class="divider"><td colspan="8"></td></tr>'.
    '<tr>'.
        '<th colspan="2">' . get_text('Event') . '</th>'.
        '<th colspan="2">' . get_text('Team') . '</th>'.
        '<th colspan="2">'. get_text('TeamComponents') . '</th>'.
        '<th>'. get_text('VersionTimestamp', 'Tournament'). '</th>'.
        '<th></th>'.
    '</tr></thead>'.
    '<tbody id="lstBody"></tbody>'.
    '<tfoot class="hidden"><tr><td colspan="8">'.get_text('ChangeComponents','Help').'</td></tr></tfoot>'.
    '</table>';

include('Common/Templates/tail.php');