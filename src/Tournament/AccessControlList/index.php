<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Lib/CommonLib.php');
checkACL(AclRoot, AclReadWrite);
CheckTourSession(true);

$lockEnabled =  getModuleParameter("ACL","AclEnable","00");
$JS_SCRIPT = array(
    phpVars2js(array(
        'optNo' => count($listACL),
        'RootDir' => $CFG->ROOT_DIR,
        'CmdDelete' => get_text('CmdDelete', 'Tournament'),
        'AreYouSure' => get_text('MsgAreYouSure')
        )),
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/jQuery/jquery-2.1.4.min.js"></script>',
    '<script type="text/javascript" src="Fun_AJAX_index.js"></script>',
);

$PAGE_TITLE=get_text('Block_Manage', 'Tournament');

include('Common/Templates/head.php');

echo '<div align="center">
    <table class="Tabella">';
echo '<tr><th class="Title" colspan="'.(3+count($listACL)).'">'.get_text('Block_Manage','Tournament').'</th></tr>';
echo '<tr><th colspan="3">'.get_text('EnableAccess','Tournament').'</th><td colspan="'.count($listACL).'">
    <select onchange="ActivateACL()" id="AclEnable">
		<option value="0" '.(substr($lockEnabled,0,1)=="0" ? 'selected="selected"' : '').'>'.get_text('No').'</option>
		<option value="1" '.(substr($lockEnabled,0,1)=="1" ? 'selected="selected"' : '').'>'.get_text('Yes').'</option>
	</select></td></tr>';
echo '<tr><th colspan="3">'.get_text('RecordAccess','Tournament').'</th><td colspan="'.count($listACL).'">
    <select onchange="ActivateACL()" id="AclRecord">
		<option value="0" '.(substr($lockEnabled,1,1)=="0" ? 'selected="selected"' : '').'>'.get_text('No').'</option>
		<option value="1" '.(substr($lockEnabled,1,1)=="1" ? 'selected="selected"' : '').'>'.get_text('Yes').'</option>
	</select>
	<input type="button" value="'.get_text('CmdUpdate').'" onclick="updateList()">
	</td></tr>';
echo '<tr><td class="divider" colspan="'.(3+count($listACL)).'"></td></tr>';


echo '<tr>
    <th class="Title">&nbsp;</th>
    <th class="Title">'.get_text('Block_IP','Tournament').'</th>
    <th class="Title">'.get_text('Block_Nick','Tournament').'</th>';
foreach($listACL as $i => $n) {
    echo '<th class="Title">'.get_text($n, 'Tournament').'</th>';
}
echo '</tr>';

echo '<tr>';
echo '<td class="Center"><input type="button" onclick="saveIp();" value="' . get_text('CmdSave') . '"></td>';
echo '<td><input type="text" id="newIP" value=""></td>';
echo '<td><input type="text" id="newNick" value=""></td>';
echo '<td colspan="'.count($listACL).'">&nbsp;</td>';
echo '</tr>';
echo '<tr><td class="divider" colspan="'.(3+count($listACL)).'"></td></tr>';

echo '<tbody id="ipList"></tbody>';
echo '<tr><td class="divider" colspan="'.(3+count($listACL)).'"></td></tr>';
echo '<tr><td colspan="'.(3+count($listACL)).'">
    <img src="'.$CFG->ROOT_DIR.'Common/Images/ACL0.png" style="vertical-align: middle; margin: 5px;">'. get_text('ACLNoAccess','Tournament') . '<br>
    <img src="'.$CFG->ROOT_DIR.'Common/Images/ACL1.png" style="vertical-align: middle; margin: 5px;">'. get_text('ACLReadOnly','Tournament') . '<br>
    <img src="'.$CFG->ROOT_DIR.'Common/Images/ACL2.png" style="vertical-align: middle; margin: 5px;">'. get_text('ACLReadWrite','Tournament') . '
    </td></tr>';
echo '<tr><td colspan="'.(3+count($listACL)).'">'.get_text('AclNotes','Tournament', getMyScheme() . '://localhost' . ($_SERVER['SERVER_PORT']!=80 ? $port=':'.$_SERVER['SERVER_PORT'] : '') . $CFG->ROOT_DIR . '?ACLReset=' . $_SESSION["TourCode"]) .'</td></tr>';
echo '</table>
    </div>';
echo '<script>updateList();</script>';

include('Common/Templates/tail.php');