<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/CommonLib.php');
require_once('Common/Fun_Sessions.inc.php');

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
		'UpdateTimeout'=>(empty($CFG->ONLINE) ? 10000 : 30000),
		'RootDir'=>$CFG->ROOT_DIR
		)),
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Modules/Speaker/Fun_AJAX.js"></script>',
		'<link href="speaker.css" media="screen" rel="stylesheet" type="text/css">',
);
$ONLOAD=(' onLoad="GetStartlist()"');


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
<th class="Title" width="30%"><?php print get_text('MenuLM_Parameters');?></th>
<th class="Title" width="30%"><?php print get_text('Events', 'Tournament');?></th>
<th class="Title" width="30%"><?php print get_text('Options', 'Tournament');?></th>
<th class="Title" width="10%">&nbsp;</th>
</tr>
<tr>
<td class="Center">
<?php

echo '<input type="radio" name="type" id="StartCountry" onClick="GetStartlist()" checked="checked">'.get_text('StartlistCountryOnlyAthletes', 'Tournament').'&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<input type="radio" name="type" id="StartTarget" onClick="GetStartlist()">'.get_text('StartListbyTarget', 'Tournament').'<br/>';
echo '<div style="display:inline-block;text-align:left">';
foreach(GetSessions('Q') as $Session) {
	echo '<input type="checkbox" name="Session[]" value="'.$Session->SesType.$Session->SesOrder.'" onClick="GetStartlist()">'.$Session->SesName.'<br/>';
}
echo '</div>';

?>
</td>
<td class="Center"><select name="x_Events[]" id="x_Events" size="8" multiple="multiple" onChange="document.getElementById('lu').value=0;GetStartDetails();"></select><br><a class="Link" href="javascript:SelectAllOpt('x_Events');GetResults();"><?php print get_text('SelectAll');?></a></td>
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
<td class="Center"><input type="button" value="<?php  print get_text('CmdOk');?>" onClick="GetStartDetails();"></td>
</tr>
<tr>
</tr>
</tbody>
</table>
<table class="Tabella Speaker">
<tr>
<th class="Title" id="Head1"></th>
<th class="Title" id="Head2"></th>
<th class="Title" id="Head3"></th>
<th class="Title" id="Head4"></th>
<th class="Title" id="Head5"></th>
</tr>
<tbody id="tbody">
<tr id="RowDiv" class="Divider"><td colspan="12"><input type="hidden" id="lu" value="0"></td></tr>
</tbody>
</table>

<?php

include('Common/Templates/tail' . (isset($_REQUEST["showMenu"]) ? '': '-min') . '.php');

?>
