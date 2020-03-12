<?php

require_once(dirname(dirname(__FILE__)).'/config.php');

CheckTourSession(true);
checkACL(AclISKServer, AclReadOnly);

require_once('Common/Lib/CommonLib.php');
require_once('Common/Lib/Fun_Modules.php');

$PAGE_TITLE=get_text('ISK-Anomalies', 'Api');
$JS_SCRIPT=array(
	phpVars2js(array('msgAreYouSure' => get_text('MsgAreYouSure'), 'Anomalies' => 1)),
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/jQuery/jquery-2.1.4.min.js"></script>',
	'<script type="text/javascript" src="./index.js"></script>',
	'<script type="text/javascript" src="./Results.js"></script>',
	'<link href="ISK.css" rel="stylesheet" type="text/css">',
);

$ONLOAD=' onload="ResultsInit()"'; // onunload="SetAutoImport(true)"';

include('Common/Templates/head.php');

echo '<table class="Tabella">';
echo '<tr><th class="Title" colspan="6">' . get_text('ISK-Anomalies', 'Api') . '</th></tr>';
echo '<tr>';
	echo '<th nowrap="nowrap">' . get_text('Session') . '</th>';
	echo '<th nowrap="nowrap">' . get_text('Distance', 'Tournament') . '</th>';
	echo '<th nowrap="nowrap">' . get_text('Volee', 'HTT') . '</th>';
	echo '<th >&nbsp;</th>';
	echo '<th nowrap="nowrap">' . get_text('AutoImport', 'Api') . '</th>';
	echo '<th nowrap="nowrap">' . get_text('LockToEnds', 'Api') . '</th>';
echo '</tr>';
echo '<tr>';
	echo '<td nowrap="nowrap">
		<select name="x_Session" id="x_Session" onChange="loadComboDistanceEnd(); LoadTablets();"></select><br>
		<input type="checkbox" value="1" id="x_onlyToday" onClick="loadComboSchedule();">' . get_text('OnlyToday', 'Tournament') . '
		</td>';
	echo '<td nowrap="nowrap"><select name="x_Distance" id="x_Distance" onchange="adjustMaxEnd(); UpdateTablets()"></select></td>';
	echo '<td nowrap="nowrap"><input type="number" name="x_End" id="x_End" min="0" max="12" value="0" onchange="UpdateTablets()"></td>';
	echo '<td nowrap="nowrap" align="center" style="padding:0 2em;"><input type="button" id="setCurrent" value="' . get_text('CmdOk') . '" onClick="LoadTablets();"></td>';
	echo '<td nowrap="nowrap" align="center" style="padding:0 2em;">
		<input type="checkbox" id="AutoImport" onClick="SetAutoImport(this.checked)"'.(getModuleParameter('ISK', 'StopAutoImport') ? '' : ' checked="checked"').'>
		<input type="button" id="cmdImport" class="' . (getModuleParameter('ISK', 'StopAutoImport') ? '':'hidden'). '" value="' . get_text('CmdImport','Api') . '" onClick="dataImport();">
		</td>';
	echo '<td nowrap="nowrap" align="center" style="padding:0 2em;" id="StickyEnds"></td>';
echo '</tr>';
echo '<tr class="divider"><td colspan="6"></td></tr>';
echo '<tr>';
	echo '<td id="ISK-ses"></td>';
	echo '<td id="ISK-dis"></td>';
	echo '<td id="ISK-end"></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td id="ISK-sticky"></td>';
echo '</tr>';
echo '<tr>';
	echo '<td id="Errors" colspan="6"></td>';
echo '</tr>';

echo '</table>';

// gets all the

echo '<div id="TabletInfo"></div>';

echo '<div class="Legend">';
foreach(array('G', 'R', 'O', 'B', 'C', 'Z', 'Y') as $Let) {
	echo '<div><span class="Let-'.$Let.'">&nbsp;&nbsp;&nbsp;&nbsp;</span> '.get_text('Desc-Let-'.$Let, 'Api').'</div>';
}
echo '</div>';
echo '<div id="PopUp" class="PopUp">
	<div class="TargetTitle"><img class="PopUpCloseImg ClickableDiv" onClick="closeTarget()"><span id="PopUpTitle"></span></div>
	<div id="PopUpContent"></div>
	<div id="PopUpCmd">
		<input type="button" class="ClickableDiv" id="PopupRemove" value="'.get_text('CmdDelete', 'Api').'" onclick="popupRemove(this)">
		<input type="button" class="ClickableDiv" id="PopupImport" value="'.get_text('CmdImport', 'Api').'" onclick="popupImport(this)">
		<input type="button" class="ClickableDiv" value="'.get_text('Close').'" onclick="closeTarget()">
	</div>
	</div>';
include('Common/Templates/tail.php');