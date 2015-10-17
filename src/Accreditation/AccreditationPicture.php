<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/CommonLib.php');

$PAGE_TITLE=get_text('TakePicture', 'Tournament');
$JS_SCRIPT = array(
	phpVars2js(array('ROOT_DIR' => $CFG->ROOT_DIR)),
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Accreditation/Fun_AJAX_AccreditationPicture.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Accreditation/TakePicture.js"></script>'
);
$ONLOAD = ' onLoad="javascript:setupVideo();searchAthletes();"';
include('Common/Templates/head' . (isset($_REQUEST["showMenu"]) ? '': '-min') . '.php');

?>
<table class="Tabella Speaker">
<tr onClick="showOptions();"><th class=Title colspan="4"><?php echo get_text('TakePicture', 'Tournament');?></th></tr>
<tr class="Divider"><td colspan="2"></td></tr>
<tbody id="options">

<tr>
<th class="Title" width="50%"><?php echo get_text('Options', 'Tournament');?></th>
<th class="Title" width="50%"><?php echo get_text('FilterRules');?></th>
</tr>

<tr>
<td class="Center">

<?php
echo get_text('Camera', 'Tournament');
echo '<select id="videoSource"></select><br>';
echo '<input type="checkbox" id="showMenu" ' . (isset($_REQUEST["showMenu"]) ? 'checked' : '') .
	' onClick="document.location=\'' . $_SERVER["PHP_SELF"]. (isset($_REQUEST["showMenu"]) ? '' : '?showMenu') . '\';"' .
	'>&nbsp;';
echo get_text('ShowIanseoMenu', 'Tournament');
?>
</td>
<td class="Center">
<input type="text" name="x_Search" id="x_Search" style="width: 80%;" maxlength="50" onBlur="searchAthletes();"><br>
<input type="checkbox" id="x_Country" name="x_Country" value="1" checked onChange="searchAthletes();"><?php echo get_text('Country') ?>&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="x_Athlete" name="x_Athlete" value="1" checked onChange="searchAthletes();"> <?php echo get_text('Athlete') ?><br>
<input type="checkbox" id="x_noPhoto" name="x_noPhoto" value="1" checked onChange="searchAthletes();"><?php echo get_text('OnlyWithoutPhoto','Tournament')?>
</td>
</tr>
</tbody>
</table>

<table class="Tabella Speaker">
	<tr>
		<th class="Title" width="35%"><?php echo get_text('Camera', 'Tournament');?></th>
		<th class="Title" width="35%"><?php echo get_text('Photo', 'Tournament');?></th>
		<th class="Title" width="30%"><?php echo get_text('MenuLM_Partecipant List');?></th>
	</tr>
<tbody id="tbody">
	<tr>
		<td style="vertical-align: top; text-align: center;">
			<input type="button" id="stop-button" value="<?php echo get_text('StopCamera', 'Tournament')?>" onClick="stopVideo();" style="display: none;">
			<input type="button" id="start-button" value="<?php echo get_text('StartCamera', 'Tournament')?>" onClick="startVideo();" style="display: none;">
			<div id="cameraContainer" style="position: relative;">
				<video id="CamVideo" width="100%" autoplay onClick="snapshot();" style="position: absolute; top: 0px; left: 0px;"></video>
				<svg id="face" version="1.1" xmlns="http://www.w3.org/2000/svg" style="position: absolute; display:none; top: 0px; left: 0px; width:300px; height: 400px;" viewBox="0 0 300 400" onClick="snapshot();">
					<ellipse cx="150" cy="200" rx="125" ry="160" style="fill:none;stroke:yellow;stroke-width:2"/>
					<rect width="300" height="400"  style="fill:none;stroke:yellow;stroke-width:2"/>
				</svg>
			</div>
		</td>
		<td style="vertical-align: top; text-align: center;">
			<input type="hidden" id="selId">
			<table class="Tabella">
				<tr><th id="selAth" style="width:40%;"></th><td id="selCat" style="width:20%;"></td><td id="selTeam" style="width:40%;"></td></tr>
			</table><br>
			<img id="athPic" src="" width="150"><br>
			<input type="button" id="delete-button" value="<?php echo get_text('PhotoDelete', 'Tournament')?>" onClick="deletePicture();" style="display: none;">
			<canvas id="screenshot-canvas" style="display: none;"></canvas>
		</td>
		<td style="vertical-align: top;">
			<table class="Tabella" id="List">
			<thead><tr>
				<th colspan="2"><?php echo get_text('Athlete')?></th>
				<th><?php echo get_text('DivisionClass'); ?></th>
				<th><?php echo get_text('Country')?></th></tr></thead>
			<tbody id="ListBody"></tbody>
			</table>
		</td>
	</tr>
</tbody>
</table>

<?php

include('Common/Templates/tail' . (isset($_REQUEST["showMenu"]) ? '': '-min') . '.php');
?>