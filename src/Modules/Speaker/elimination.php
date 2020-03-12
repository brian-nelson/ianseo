<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/CommonLib.php');

if (defined('hideSpeaker')) {
	header('location: /index.php');
	exit;
}
checkACL(AclSpeaker, AclReadOnly);

$PAGE_TITLE=get_text('MenuLM_Speaker');
$JS_SCRIPT=array(
		phpVars2js(array(
		'UpdateTimeout'=>(empty($CFG->ONLINE) ? 2500 : 30000),
		'RootDir'=>$CFG->ROOT_DIR
		)),
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Modules/Speaker/Fun_AJAX.js"></script>',
		'<link href="speaker.css" media="screen" rel="stylesheet" type="text/css">',
);
$ONLOAD=(' onLoad="javascript:GetElimEvents()"');


if(empty($CFG->IS)) {
	$JS_SCRIPT[]='<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>';
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
<input type="radio" name="isEvent" checked id="isEvent0" onClick="GetElimEvents();"><?php print get_text('StageE1', 'ISK-Lite');?>&nbsp;&nbsp;&nbsp;
<input type="radio" name="isEvent" id="isEvent1" onClick="GetElimEvents();"><?php print get_text('StageE2', 'ISK-Lite');?><br>
<input type="checkbox" name="viewInd" checked id="viewInd" onClick="GetElimEvents();"><?php print get_text('IndEventList');?>&nbsp;-&nbsp;<input type="checkbox" name="viewInd" id="viewIndSnap" onClick="GetQualEvents();"><?php print get_text('Snapshot', 'Tournament');?><br>
<?php print get_text('ComparedTo', 'Tournament');?><input name="comparedTo" id="comparedTo" type="text" size="5" maxlength="3" onChange="document.getElementById('lu').value=0;GetResults();"><br>
<?php print get_text('NumResult', 'Tournament');?><input name="numPlaces" id="numPlaces" type="text" size="10" maxlength="3" onChange="document.getElementById('lu').value=0;GetResults();"><br>
</td>
<td class="Center"><select name="x_Events" id="x_Events" size="8" multiple="multiple" onChange="document.getElementById('lu').value=0;GetElimResults();"></select><br><a class="Link" href="javascript:SelectAllOpt('x_Events');GetElimResults();"><?php print get_text('SelectAll');?></a></td>
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
<td class="Center"><input type="button" value="<?php  print get_text('CmdOk');?>" onClick="GetElimResults();"></td>
</tr>
<tr>
</tr>
</tbody>
</table>
<table class="Tabella Speaker">
<tr>
<th class="Title" width="5%"><?php print get_text('Status', 'Tournament');?></th>
<th class="Title" width="15%"><?php print get_text('Event');?></th>
<th class="Title" width="5%"><?php print get_text('Target');?></th>
<th class="Title" width="5%" colspan="2"><?php print get_text('Rank');?></th>
<th class="Title" width="50%" colspan="2">&nbsp;</th>
<th class="Title" width="15%" colspan="4"><?php print get_text('TotalShort', 'Tournament');?></th>
<th class="Title" width="5%">&nbsp;</th>
</tr>
<tbody id="tbody">
<tr id="RowDiv" class="Divider"><td colspan="12"><input type="hidden" id="lu" value="0"></td></tr>
</tbody>
</table>

<?php

include('Common/Templates/tail' . (isset($_REQUEST["showMenu"]) ? '': '-min') . '.php');

?>