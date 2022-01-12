<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/CommonLib.php');
require_once('Common/Lib/Fun_Modules.php');

if (defined('hideSpeaker')) {
	header('location: /index.php');
	exit;
}

if(!CheckTourSession()) {
	// opens a session based on the competition saved in InfoSystem Setup
	if($IsCode=GetIsParameter('IsCode') and $TourId=getIdFromCode($IsCode)) {
		CreateTourSession($TourId);
	}
}
checkACL(AclSpeaker, AclReadOnly);

$PAGE_TITLE=get_text('MenuLM_Speaker');
$JS_SCRIPT=array(
		phpVars2js(array(
		'UpdateTimeout'=>(empty($CFG->ONLINE) ? 2500 : 30000),
		)),
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Modules/Speaker/Fun_AJAX.js"></script>',
		'<link href="speaker.css" media="screen" rel="stylesheet" type="text/css">',
// 		'<script>var CurDate=new Date(); alert(CurDate.getDate());</script>'
);


$ONLOAD=(' onload="GetSchedule()"');

$enableHHT = $_SESSION["MenuHHT"];
if($enableHHT)
{
	$Rs=safe_r_sql("SELECT HeTournament FROM HhtEvents WHERE HeTournament=" . StrSafe_DB($_SESSION['TourId']));
	$enableHHT = (safe_num_rows($Rs)!=0);
}

if(empty($CFG->IS)) {
	$JS_SCRIPT[]='<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>';
	include('Common/Templates/head' . (isset($_REQUEST["showMenu"]) ? '': '-min') . '.php');
} else {
	include_once ($CFG->DOCUMENT_PATH . "Common/Styles/head.php");
}


?>

<table class="Tabella Speaker">
<tr onClick="showOptions();"><th class=Title colspan="4"><?php print get_text('MenuLM_Speaker');?></th></tr>
<tr class="Divider"><td colspan="4"></td></tr>
<tbody id="options">
<tr>
<th class="Title" width="30%"><?php print get_text('Schedule', 'Tournament');?></th>
<th class="Title" width="30%"><?php print get_text('Events', 'Tournament');?></th>
<th class="Title" width="30%"><?php print get_text('Options', 'Tournament');?></th>
<th class="Title" width="10%">&nbsp;</th>
</tr>
<tr>
<td class="Center">
<select name="x_Schedule" id="x_Schedule" onChange="GetEvents();"></select><br>
<?php
if($enableHHT) {
	echo '<input type="checkbox" id="useHHT" checked="checked" onClick="GetSchedule();">'.get_text('FollowHHT','Tournament').'<br>';
} else {
	if($IskSequence=getModuleParameter('ISK', 'Sequence')) {
		echo '<input type="button" id="currentSession" onClick="GetSchedule(true);" value="'.get_text('GoToRunning','Tournament').'"><br>';
	}
}
?>

<input type="checkbox" id="onlyToday" checked onClick="GetSchedule();"><?php print get_text('OnlyToday','Tournament');?><br>
</td>
<td class="Center"><select name="x_Events" id="x_Events" multiple="multiple" onChange="document.getElementById('lu').value=0;GetMatches()"></select><br><a class="Link" href="javascript:SelectAllOpt('x_Events');"><?php print get_text('SelectAll');?></a></td>
<td class="Center">
<?php
if(empty($CFG->IS)) {
	echo '<input type="checkbox" id="showMenu" ' . (isset($_REQUEST["showMenu"]) ? 'checked' : '') .
		' onClick="document.location=\'' . $_SERVER["PHP_SELF"]. (isset($_REQUEST["showMenu"]) ? '' : '?showMenu') . '\';"' .
		'>&nbsp;';
	echo get_text('ShowIanseoMenu', 'Tournament');
}
?>
<br>&nbsp;<br>
<input type="checkbox" id="pauseUpdate" onClick="pauseRefresh();"><?php  print get_text('StopRefresh','Tournament');?>
</td>
<td class="Center"><input type="button" value="<?php  print get_text('CmdOk');?>" onClick="GetMatches();"></td>
</tr>
<tr>
</tr>
</tbody>
</table>
<table class="Tabella Speaker">
<tr>
<th class="Title" width="5%"><?php print get_text('Status', 'Tournament');?></th>
<th class="Title" width="10%"><?php print get_text('Event');?></th>
<th class="Title" width="5%"><?php print get_text('Target');?></th>
<th class="Title" width="25%">&nbsp;</th>
<th class="Title" width="10%"><?php print get_text('TotalShort', 'Tournament');?></th>
<th class="Title" width="15%"><?php print get_text('SetPoints', 'Tournament');?></th>
<th class="Title" width="5%"><?php print get_text('Target');?></th>
<th class="Title" width="25%">&nbsp;</th>
</tr>
<tbody id="tbody">
<tr id="RowDiv" class="Divider"><td colspan="8"><input type="hidden" id="lu" value="0"></tr>
</tbody>
</table>

<?php

if(empty($CFG->IS)) {
	include('Common/Templates/tail' . (isset($_REQUEST["showMenu"]) ? '': '-min') . '.php');
} else {
	include_once ($CFG->DOCUMENT_PATH . "Common/Styles/tail.php");
}
