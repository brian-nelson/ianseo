<?php

require_once(dirname(dirname(__FILE__)).'/config.php');

CheckTourSession(true);
checkACL(AclISKServer, AclReadWrite);

require_once('Common/Lib/CommonLib.php');
require_once('Common/Lib/Fun_Modules.php');

if(!empty($_REQUEST['truncate'])) {
	switch($_REQUEST['truncate']) {
		case 1:
			$IskSequence=getModuleParameter('ISK', 'Sequence', array('type' => '', 'session'=>'', 'distance'=>0, 'maxdist'=>0, 'end'=>0));
			safe_w_sql("delete from IskData where IskDtTournament='{$_SESSION['TourId']}' and IskDtType!='{$IskSequence['type']}'");
			break;
		case 2:
			$IskSequence=getModuleParameter('ISK', 'Sequence', array('type' => '', 'session'=>'', 'distance'=>0, 'maxdist'=>0, 'end'=>0));
			safe_w_sql("delete from IskData where IskDtTournament='{$_SESSION['TourId']}' and IskDtType='{$IskSequence['type']}'");
			break;
		case 3:
			safe_w_sql("delete from IskData where IskDtTournament='{$_SESSION['TourId']}'");
			break;
	}
	CD_redirect('Results.php');
}

$PAGE_TITLE=get_text('ISK-Results');
$JS_SCRIPT=array(
	phpVars2js(array('msgAreYouSure' => get_text('MsgAreYouSure'))),
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/jQuery/jquery-2.1.4.min.js"></script>',
	'<script type="text/javascript" src="./index.js"></script>',
	'<script type="text/javascript" src="./Results.js"></script>',
	'<link href="ISK.css" rel="stylesheet" type="text/css">',
);

$ONLOAD=' onload="ResultsInit()"'; // onunload="SetAutoImport(true)"';

include('Common/Templates/head.php');

echo '<table class="Tabella">';
echo '<tr><th class="Title" colspan="7">' . get_text('ISK-Results') . '</th></tr>';
echo '<tr>';
	echo '<th nowrap="nowrap">' . get_text('Session') . '</th>';
	echo '<th nowrap="nowrap">' . get_text('Distance', 'Tournament') . '</th>';
	echo '<th nowrap="nowrap">' . get_text('Volee', 'HTT') . '</th>';
	echo '<th >&nbsp;</th>';
	echo '<th nowrap="nowrap">' . get_text('AutoImport', 'Api') . '</th>';
	echo '<th nowrap="nowrap">' . get_text('LockToEnds', 'Api') . '</th>';
	echo '<th >&nbsp;</th>';
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
	echo '<td><input type="button" value="'.htmlspecialchars(get_text('CmdTruncateMyHTTData', 'HTT')).'" onclick="window.location.href=\'?truncate=2\'">
		<input type="button" value="'.htmlspecialchars(get_text('ISK-TruncateTable', 'Api')).'" onclick="window.location.href=\'?truncate=1\'">
		<input type="button" value="'.htmlspecialchars(get_text('ISK-TruncateTableAll', 'Api')).'" onclick="window.location.href=\'?truncate=3\'"></td>';
echo '</tr>';
echo '<tr class="divider"><td colspan="7"></td></tr>';
echo '<tr>';
	echo '<td id="ISK-ses"></td>';
	echo '<td id="ISK-dis"></td>';
	echo '<td id="ISK-end"></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td id="ISK-sticky"></td>';
	echo '<td></td>';
echo '</tr>';
echo '<tr>';
	echo '<td id="Errors" colspan="7"></td>';
echo '</tr>';

echo '</table>';

// gets all the

echo '<div id="TabletInfo"></div>';

echo '<div class="Legend">';
foreach(array('G', 'Z', 'Y', 'B', 'C', 'R', 'O') as $Let) {
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