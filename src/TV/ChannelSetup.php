<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/CommonLib.php');

$PAGE_TITLE=get_text('MenuLM_TV Channels');
$JS_SCRIPT=array(
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-confirm.min.js"></script>',
	'<script type="text/javascript" src="./ChannelSetup.js"></script>'
);
$JS_SCRIPT[]= '<link href="'.$CFG->ROOT_DIR.'Common/css/font-awesome.css" rel="stylesheet" type="text/css">';
$JS_SCRIPT[]= '<link href="'.$CFG->ROOT_DIR.'Common/css/jquery-confirm.min.css" rel="stylesheet" type="text/css">';
$JS_SCRIPT[]= '<link href="./ChannelSetup.css" rel="stylesheet" type="text/css">';
$JS_SCRIPT[]= phpVars2js(array(
	'MsgAddChannel' => get_text('NewChannel','Tournament'),
	'MsgAddSplit' => get_text('NewSplit','Tournament'),
	'MsgDelChannel' => get_text('DeleteChannel','Tournament'),
	'MsgDelSplit' => get_text('DeleteSplit','Tournament'),
	'CmdCancel' => get_text('CmdCancel'),
	'CmdConfirm' => get_text('Confirm', 'Tournament'),
));

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
	<option value="5">'.get_text('TVOutputCss3', 'Tournament').'</option>
	<option value="4">'.get_text('TVOutputLight', 'Tournament').'</option>
	<option value="3">'.get_text('MenuLM_TV Output').'</option>
	<option value="2">'.get_text('URL', 'Tournament').'</option>
	<option value="6">'.get_text('ServerFile', 'Tournament').'</option>
	<option value="1">'.get_text('Freetext', 'Tournament').'</option>
	';


include('Common/Templates/head.php');

echo '<table class="Tabella">';
echo '<tr><th class="Title" colspan="11"><i class="fa fa-lg fa-plus-circle mx-1 float-left" onclick="AddChannel()" title="'.get_text('NewChannel','Tournament').'"></i>' . get_text('MenuLM_TV Channels') . '</th></tr>';
echo '<tr class="Divider"><td colspan="10"></td></tr>';
echo '<tr refid="-1" refside="-1">
	<th class="Title Right" colspan="3">'.get_text('ServerFilesPath', 'Tournament').'</th>
	<th class="Title Left">
		<input type="text" class="TvoPath" onchange="update(this, \'Path\')" value="'.getParameter('TVOUT-Path', false, '').'">
	</th>
	</tr>';
echo '<tr class="Divider"><td colspan="10"></td></tr>';

$SCHEME=getMyScheme();

$OldChannel=0;

$q=safe_r_sql("SELECT TVOId , TVOSide, TVOHeight, TVOName, TVOUrl, TVOMessage, TVORuleId, TVOTourCode, TVORuleType, TVOFile
	FROM TVOut
	ORDER BY TVOId,TVOSide");

while($r=safe_fetch($q)) {
	if($OldChannel!=$r->TVOId) {
		echo '<tr refid="'.$r->TVOId.'" refside="'.$r->TVOSide.'">
			<th class="Title Left" colspan="3">
				<i class="fa fa-lg fa-tv mx-1" onclick="window.open(\''.$SCHEME.'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'tv.php?id='.$r->TVOId.'\')"></i>
				<i class="fa fa-lg fa-refresh mx-1" onclick="update(this,\'Reload\')"></i>
				<i class="fa fa-lg fa-plus-circle mx-1" onclick="AddSplit(this)" title="'.get_text('NewSplit','Tournament').'"></i>
				'.get_text('Channel', 'Tournament').' '.$r->TVOId.'
				<i class="fa fa-lg fa-trash-o float-right mx-1" onclick="deleteChannel(this)" title="'.get_text('DeleteChannel','Tournament').'"></i>
				</th>
			<th class="Title Left">
				<span class="mx-1">'.get_text('Name', 'Tournament').'</span> <input type="text" class="TvoName" onchange="update(this, \'Name\')" value="'.htmlspecialchars($r->TVOName).'">
			</th>
			</tr>';
		echo '<tr>
			<th>'.get_text('Order', 'Tournament').'</th>
			<th>'.get_text('Heigh', 'BackNumbers').'</th>
			<th>'.get_text('Status', 'Tournament').'</th>
			<th>'.get_text('Options', 'Tournament').'</th>
			</tr>';
		$First=true;
	}
	$OldChannel=$r->TVOId;

	echo '<tr refid="'.$r->TVOId.'" refside="'.$r->TVOSide.'">';
	echo '<td class="Center Bold"><input type="number" onchange="update(this, \'Side\')" value="'.$r->TVOSide.'" class="TvoSide"></td>';
	echo '<td class="Center Bold"><input type="text" onchange="update(this, \'Height\')" value="'.$r->TVOHeight.'" class="TvoHeight"></td>';
	echo '<td class="Center Bold">
		<select class="TvoStatus" onchange="update(this, \'Status\')">'.str_replace('value="'.$r->TVORuleType.'"', 'value="'.$r->TVORuleType.'" selected="selected"', $Status).'</select>
		'.($First ? '' : '<i class="fa fa-lg fa-trash-o float-right mx-1" onclick="deleteSplit(this)" title="'.get_text('DeleteSplit','Tournament').'"></i>').'
		</td>';
	echo '<td>
		<div class="TvoRow">
			<div class="TvoRowTitle">'.get_text('TourCode', 'Tournament').'</div>
			<div class="TvoRowField">
				<select class="TvoTournament" onchange="update(this, \'Code\')"><option value="">'.get_text('TitleTourMenu', 'Tournament').'</option>'.str_replace('value="'.$r->TVOTourCode.'"', 'value="'.$r->TVOTourCode.'" selected="selected"', $CompSelect).'</select>	
			</div>
			<div class="TvoRowTitle">'.get_text('TVOutRules', 'Tournament').'</div>
			<div class="TvoRowField">
				<select class="TvoRule" onchange="update(this, \'Rule\')">'.($r->TVOTourCode ? '<option value="">'.get_text('TVSelectPage', 'Tournament').'</option>'.(isset($Rules[$r->TVOTourCode]) ? str_replace('value="'.$r->TVORuleId.'"', 'value="'.$r->TVORuleId.'" selected="selected"', $Rules[$r->TVOTourCode]) : '') : '').'</select>	
			</div>
		</div>

		<div class="TvoRow">
			<div class="TvoRowTitle">'.get_text('URL', 'Tournament').'</div>
			<div class="TvoRowField">
				<input class="TvoUrl" type="text" onchange="update(this, \'Url\')" maxlength="255" value="'.htmlspecialchars($r->TVOUrl).'">	
			</div>
		</div>
		
		<div class="TvoRow">
			<div class="TvoRowTitle">'.get_text('ServerFile', 'Tournament').'</div>
			<div class="TvoRowField">
				<input class="TvoFile" type="text" onchange="update(this, \'File\')" maxlength="255" value="'.htmlspecialchars($r->TVOFile).'">	
			</div>
		</div>
		
		<div class="TvoRow">
			<div class="TvoRowTitle">'.get_text('Freetext', 'Tournament').'</div>
			<div class="TvoRowField">
				<textarea class="TvoMessage" onchange="update(this, \'Message\')">'.nl2br($r->TVOMessage).'</textarea>	
			</div>
		</div>
		</td>';
	echo '</tr>';
	$First=false;
}
echo '</table>';

include('Common/Templates/tail.php');
