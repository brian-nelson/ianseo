<?php
/*
THIS FILE IS ESSENTIAL TO MAKE THE APIS TO GET RECOGNIZED BY IANSEO

* HOW IT WORKS
the "codename" of the API will be used in ianseo. The codename is the name of the directory containing the Api.
The essentials are:

* ApiConfig.php
this file gets included in the Competition Setup (Tournament/index.php)
* DrawQRCode.php
this file is used by the ScoreCard printout routines.
 */

require_once(dirname(dirname(__FILE__)).'/config.php');

CheckTourSession(true);
checkACL(AclISKServer, AclReadWrite);


if(!empty($_REQUEST['remove'])) {
	safe_w_sql("delete from IskData where IskDtDevice=".StrSafe_DB($_REQUEST['remove']));
	safe_w_sql("delete from IskDevices where IskDvDevice=".StrSafe_DB($_REQUEST['remove']));
	cd_redirect('./');
}

require_once('Common/Lib/CommonLib.php');

$PAGE_TITLE=get_text('ISK-Configuration');
$JS_SCRIPT=array(
	phpVars2js(array(
		'TourId'=>$_SESSION["TourId"],
		'MsgConfirm'=>htmlspecialchars(get_text('MsgAreYouSure')),
		'MsgOk'=>get_text('CmdOk'),
		'msgRemove'=>htmlspecialchars(get_text('ISK-Remove', 'Api')),
		'Lite'=>get_text('ISK-Lite-Name', 'Api'),
		'Pro'=>get_text('ISK-Pro-Name', 'Api'),
		'msgIskRequiresApproval'=>htmlspecialchars(get_text('ISK-RequiresApproval', 'Api')),
		'msgIskDenyAccess'=>htmlspecialchars(get_text('ISK-DenyAccess', 'Api')),
		'msgIskReloadConfig'=>htmlspecialchars(get_text('ISK-ReloadConfig', 'Api')),
		'msgIskForceConfirm'=>htmlspecialchars(get_text('ISK-ForceConfirm', 'Api')),
		'msgIskApproveConfig'=>htmlspecialchars(get_text('ISK-ApproveConfig', 'Api')),
		'msgIskStatusNoShoot'=>htmlspecialchars(get_text('ISK-StatusNoShoot', 'Api')),
		'msgIskStatusReloading'=>htmlspecialchars(get_text('ISK-StatusReloading', 'Api')),
		'msgIskStatusWaitConfirm'=>htmlspecialchars(get_text('ISK-StatusWaitConfirm', 'Api')),
		'msgIskStatusOK'=>htmlspecialchars(get_text('ISK-StatusOK', 'Api')),
		'imgPath'=>$CFG->ROOT_DIR.'Common/Images/',
		)),
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/jQuery/jquery-2.1.4.min.js"></script>',
	'<script type="text/javascript" src="./index.js"></script>',
	'<link href="ISK.css" rel="stylesheet" type="text/css">',
);

$ONLOAD=' onload="loadDevices()"';

include('Common/Templates/head.php');

echo '<table class="Tabella">';
echo '<tr><th class="Title" colspan="14">' . get_text('ISK-Configuration') . '</th></tr>';
echo '<tr>';
echo '<th colspan="11">' . get_text('Session') . '</th>';
// echo '<th colspan="3" width="5%">' . get_text('ISK-CheckAlive', 'Api') . '</th>';
echo '<th>' . get_text('Distance', 'Tournament') . '</th>';
echo '<th>' . get_text('Volee', 'HTT') . '</th>';
echo '<th>&nbsp;</th>';
echo '</tr>';
echo '<tr>';
echo '<td colspan="11">
	<select name="x_Session" id="x_Session" onChange="loadComboDistanceEnd();"></select><br>
	<input type="checkbox" value="1" id="x_onlyToday" onClick="loadComboSchedule();">' . get_text('OnlyToday', 'Tournament') . '
	<script>loadComboSchedule();</script>
	</td>';
// echo '<td colspan="3" align="center"><input type="button" value="' . get_text('ISK-CheckAlive', 'Api') . '" onClick="CheckTablets();"></td>';
echo '<td><select name="x_Distance" id="x_Distance" onChange="adjustMaxEnd();"></select></td>';
echo '<td><input type="number" name="x_End" id="x_End" min="0" max="12" value="0"></select></td>';
echo '<td>
	<input type="button" id="setCurrent" value="' . get_text('CmdSave') . '" onClick="saveSequence(true);">
	<input type="button" id="refreshCurrent" value="' . get_text('CmdCancel') . '" onClick="saveSequence(false);">
	</td>';
echo '</tr>';

echo '<tr class="divider"><td colspan="12"></td></tr>';

echo '<tr>';
echo '<th width="7%" id="tgtOrder" onclick="loadDevicesOrdered(this)" ordertype="ordasc">' . get_text('Target') . '</th>';
echo '<th width="7%">' . get_text('ISK-AuthRequest', 'Api') . '</th>';
echo '<th width="8%">' . get_text('Tournament', 'Tournament') . '</th>';
echo '<th colspan="3" widht="10%" id="codeOrder" onclick="loadDevicesOrdered(this)">' . get_text('ISK-DeviceCode', 'Api') . '</th>';
echo '<th id="idOrder" onclick="loadDevicesOrdered(this)">' . get_text('ISK-DeviceId', 'Api') . '</th>';
echo '<th>' . get_text('ISK-DeviceAlive', 'Api') . '</th>';
echo '<th>' . get_text('ISK-DeviceStatus', 'Api') . '</th>';
echo '<th>' . get_text('ISK-DeviceStatusWanted', 'Api') . '</th>';
echo '<th id="batteryOrder" onclick="loadDevicesOrdered(this)">' . get_text('ISK-DeviceBattery', 'Api') . '</th>';
echo '<th>' . get_text('ISK-DeviceIpAddress', 'Api') . '</th>';
echo '<th>' . get_text('ISK-DeviceLastSeen', 'Api') . '</th>';
echo '<th>' . get_text('ISK-Remove', 'Api') . '</th>';
echo '</tr>';
echo '<tbody id="tablets">';
echo '</tbody>';
echo '</table>';

$tmp='';
foreach(range('A','Z') as $k) {
	$tmp.='<option value="'.(ord($k)-65).'">'.$k.'</option>';
}
echo '<div id="PopUp" class="PopUp">
	<div class="TargetTitle"><img align="right" src="'.$CFG->ROOT_DIR.'Common/Images/status-noshoot.gif" onClick="closePopup()">'.get_text('ManageGroupTarget', 'Api').'</div>
	<table align="center">
		<tr>
			<td>Device</td>
			<td id="PopDevice"></td>
		</tr>
		<tr>
			<td>Group</td>
			<td id="PopGroup"></td>
		</tr>
		<tr>
			<td>Target</td>
			<td id="PopTarget"></td>
		</tr>';
//echo '<tr>
//			<th>New Group</th>
//			<td><select id="NewGroup">'.$tmp.'</select></td>
//		</tr>';
echo '<tr>
			<th>New Target</th>
			<td><input type="number" id="NewTarget" min="1" max="255"></td>
		</tr>
	</table>
	<div id="PopUpCmd"><input type="button" id="PopupImport" value="'.get_text('CmdOk').'" onclick="AssignGroupTarget()">
			<input type="button" value="'.get_text('Close').'" onclick="closePopup()">
					</div>
	</div>';

include('Common/Templates/tail.php');