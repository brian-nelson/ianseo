<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

$PAGE_TITLE=get_text('MenuLM_TV Channels');
$JS_SCRIPT=array(
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
	'<script type="text/javascript" src="./ChannelSetup.js"></script>'
);

// get all the TV rules
$Rules=array();
$CompSelect='';
$q=safe_r_sql("select ToCode, ToName, ToWhere, TVRId, TVRName from TVRules inner join Tournament on TVRTournament=ToId order by ToWhenTo desc");
while($r=safe_fetch($q)) {
	if(empty($Rules[$r->ToCode])) {
		$Rules[$r->ToCode]='';
		$CompSelect.='<option value="'.$r->ToCode.'">'.$r->ToCode.' - '.$r->ToName.' ('.$r->ToWhere.')</option>';
	}
	$Rules[$r->ToCode].='<option value="'.$r->TVRId.'">'.$r->TVRName.'</option>';
}

$Status='<option value="0">'.get_text('CmdOff').'</option>
	<option value="1">'.get_text('Freetext', 'Tournament').'</option>
	<option value="2">'.get_text('URL', 'Tournament').'</option>
	<option value="3">'.get_text('MenuLM_TV Output').'</option>
	<option value="4">'.get_text('TVOutputLight', 'Tournament').'</option>
	<option value="5">'.get_text('TVOutputCss3', 'Tournament').'</option>';


include('Common/Templates/head.php');

echo '<table class="Tabella">';
echo '<tr><th class="Title" colspan="10">' . get_text('MenuLM_TV Channels') . '</th></tr>';
echo '<tr class="Divider"><td colspan="10"></td></tr>';
echo '<tr>
	<th colspan="2">'.get_text('Channel', 'Tournament').'</th>
	<th>'.get_text('Description').'</th>
	<th>'.get_text('Freetext', 'Tournament').'</th>
	<th>'.get_text('URL', 'Tournament').'</th>
	<th>'.get_text('TourCode', 'Tournament').'</th>
	<th>'.get_text('TVOutRules', 'Tournament').'</th>
	<th colspan="2">'.get_text('Status', 'Tournament').'</th>
	</tr>';

$q=safe_r_sql("SELECT TVOId , TVOName, TVOUrl, TVOMessage, TVORuleId, TVOTourCode, TVORuleType
	FROM TVOut
	ORDER BY TVOId ASC");
$maxId=0;
$SCHEME=getMyScheme();
while($r=safe_fetch($q)) {
	echo '<tr>
		<td class="Center"><a href="'. $SCHEME.'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'tv.php?id='.$r->TVOId.'" target="_blank">'.get_text('Open').'</a></td>
		<td class="Center Bold">'.$r->TVOId.'</td>
		<td><input type="text" id="Name['.$r->TVOId.']" onchange="update(this)" size="40" maxlength="50" value="'.htmlspecialchars($r->TVOName).'"></td>
		<td><textarea id="Message['.$r->TVOId.']" onchange="update(this)" rows="5" cols="40">'.nl2br($r->TVOMessage).'</textarea></td>
		<td><input id="Url['.$r->TVOId.']" type="url" onchange="update(this)" size="40" maxlength="255" value="'.htmlspecialchars($r->TVOUrl).'"></td>
		<td><select id="Tournament['.$r->TVOId.']" onchange="update(this)"><option value="">'.get_text('TitleTourMenu', 'Tournament').'</option>'.str_replace('value="'.$r->TVOTourCode.'"', 'value="'.$r->TVOTourCode.'" selected="selected"', $CompSelect).'</select></td>
		<td><select id="Rule['.$r->TVOId.']" onchange="update(this)">'.($r->TVOTourCode ? '<option value="">'.get_text('TVSelectPage', 'Tournament').'</option>'.str_replace('value="'.$r->TVORuleId.'"', 'value="'.$r->TVORuleId.'" selected="selected"', $Rules[$r->TVOTourCode]) : '').'</select></td>
		<td><select id="Status['.$r->TVOId.']" onchange="update(this)">'.str_replace('value="'.$r->TVORuleType.'"', 'value="'.$r->TVORuleType.'" selected="selected"', $Status).'</select>
				<input id="Reload['.$r->TVOId.']" type="button" value="'.get_text('Reload', 'Tournament').'" onclick="update(this)"></td>
		</tr>';
	$maxId = $r->TVOId;
}
$maxId++;
echo '<tr id="newRow">
		<td><input type="button" onClick="saveChannel();" value="'.get_text("CmdSave").'"></td>
		<td class="Center Bold" id="newID">'.$maxId.'</td>
		<td><input type="text" id="Name[0]" size="40" maxlength="50"></td>
		<td><textarea id="Message[0]" rows="5" cols="40"></textarea></td>
		<td><input id="Url[0]" type="url" size="40" maxlength="255"></td>
		<td><select id="Tournament[0]" onchange="update(this)"><option value="">'.get_text('TitleTourMenu', 'Tournament').'</option>'.$CompSelect.'</select></td>
		<td><select id="Rule[0]"></select></td>
		<td><select id="Status[0]">'.$Status.'</select></td>
		</tr>';


echo '</table>';

include('Common/Templates/tail.php');
?>
