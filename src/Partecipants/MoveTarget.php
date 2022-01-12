<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);
checkACL(AclParticipants, AclReadWrite);
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Various.inc.php');
require_once('Common/Fun_Sessions.inc.php');

$startSession=(isset($_REQUEST['startSession']) ? $_REQUEST['startSession'] : null);
$endSession=(isset($_REQUEST['endSession']) ? $_REQUEST['endSession'] : null);
$filter=((isset($_REQUEST['filter']) AND preg_match("/^[0-9A-Z%_]+$/i",$_REQUEST["filter"])) ? $_REQUEST['filter'] : null);
$sourceFrom=(isset($_REQUEST['sourceFrom']) ? $_REQUEST['sourceFrom'] : null);
$sourceTo=(isset($_REQUEST['sourceTo']) ? $_REQUEST['sourceTo'] : null);
$destFrom=(isset($_REQUEST['destFrom']) ? $_REQUEST['destFrom'] : null);

$errors=array();

$msg='';
$msg=get_text('Error');
if (isset($_REQUEST['command'])) {
	if (!IsBlocked(BIT_BLOCK_PARTICIPANT)) {
		if (!(is_numeric($startSession) && $startSession>0)) $errors[]='startSession';
		if (!is_numeric($endSession)) $errors[]='endSession';
		if (empty($filter)) $errors[]='filter';
		if (!(is_numeric($sourceFrom) && $sourceFrom>0)) $errors[]='sourceFrom';
		if (!(is_numeric($sourceTo) && $sourceTo>0)) $errors[]='sourceTo';
		if (!is_numeric($destFrom)) $errors[]='destFrom';

		$query="";
		if (count($errors)==0) {
			$Where="EnTournament=" . StrSafe_DB($_SESSION['TourId']) . "
				AND CONCAT(Entries.EnDivision,Entries.EnClass) LIKE " . StrSafe_DB($filter) . "
				AND Qualifications.QuSession=" . StrSafe_DB($startSession) . "
				AND QuTargetNo>=CONCAT(" . StrSafe_DB($startSession) . ", RIGHT(CONCAT('000'," . StrSafe_DB($sourceFrom) . "),LENGTH(QuTargetNo)-2),'A')
				AND QuTargetNo<=CONCAT(" . StrSafe_DB($startSession) . ", RIGHT(CONCAT('000'," . StrSafe_DB($sourceTo) . "),LENGTH(QuTargetNo)-2),'Z') ";
			safe_w_sql("Update Entries inner join Qualifications on EnId=QuId
				set EnTimestamp='".date('Y-m-d H:i:s')."'
				where (QuSession!=" . StrSafe_DB($endSession) . " or QuTargetNo!=CONCAT(" . StrSafe_DB($endSession) . ",RIGHT(CONCAT('000',SUBSTRING(QuTargetNo,2,".TargetNoPadding.")+(" . (intval($destFrom)-intval($sourceFrom)) . ")), ".TargetNoPadding."),RIGHT(QuTargetNo,1))) and $Where");
			$query = "UPDATE Entries INNER JOIN Qualifications ON EnId=QuId
				SET QuTimestamp=Qutimestamp,
					QuBacknoPrinted=0,
					QuSession=" . StrSafe_DB($endSession) . ",
					QuTargetNo=CONCAT(" . StrSafe_DB($endSession) . ",RIGHT(CONCAT('000',SUBSTRING(QuTargetNo,2,".TargetNoPadding.")+(" . (intval($destFrom)-intval($sourceFrom)) . ")), ".TargetNoPadding."),RIGHT(QuTargetNo,1)),
					QuTarget=QuTarget+(" . (intval($destFrom)-intval($sourceFrom)) . ")
					WHERE $Where";

			safe_w_SQL($query);

			if (safe_w_affected_rows()>0) {
				$msg=get_text('TargetMoved');
			} else {
				$msg=get_text('NoTargetFound');
			}
		}
	}
}

	//print $sourceFrom . ' ' . $sourceTo;exit;
// sessioni
$sessions=GetSessions('Q');

$comboStartSession
	= '<select name="startSession" id="startSession"' . (in_array('startSession',$errors) ? ' class="error"' : '') .'>'
	. '<option value="0">--</option>';
	//. '<option value="0">' . get_text('AllSessions','Tournament') . '</option>';

$comboEndSession
	= '<select name="endSession" id="endSession"' . (in_array('endSession',$errors) ? ' class="error"' : '') . '>'
	. '<option value="0">--</option>';


foreach ($sessions as $s)
{
	$comboStartSession.='<option value="' . $s->SesOrder. '"' . (!is_null($startSession) && $s->SesOrder==$startSession ? ' selected' : '') . '>' . $s->SesOrder .': ' . $s->SesName . '</option>';
	$comboEndSession.='<option value="' . $s->SesOrder . '"' . (!is_null($endSession) && $s->SesOrder==$endSession ? ' selected' : '') . '>' . $s->SesOrder .': ' . $s->SesName . '</option>';
}


$comboStartSession.='</select>';
$comboEndSession.='</select>';


$JS_SCRIPT=array(
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ext-2.2/adapter/ext/ext-base.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ext-2.2/ext-all-debug.js"></script>',
	phpVars2js(array(
		'StrError' => get_text('Error'),
	)),
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Partecipants/Fun_MoveTarget.js"></script>'
);

include('Common/Templates/head.php');
?>

<form id="frm" method="post" action="<?php print $_SERVER['PHP_SELF'];?>">
	<table class="Tabella">
		<tr><th class="Title"><?php print get_text('MenuLM_MoveTarget');?></th></tr>
		<tr class="Divider"><td></td></tr>
		<tr><th><?php print get_text('Source');?></th></tr>
		<tr>
			<td class="Center">
				<?php print get_text('Session');?>: <?php print $comboStartSession;?>
				&nbsp;&nbsp;
				<?php print get_text('FilterOnDivCl','Tournament'); ?>: <input <?php print (in_array('filter',$errors) ? ' class="error"' : '');?>type="text" name="filter" id="filter" size="12" maxlength="10" value="<?php print (!is_null($filter) ? $filter : '');?>" />
				&nbsp;
				<?php print get_text('From','Tournament'); ?>: <input <?php print (in_array('sourceFrom',$errors) ? ' class="error"' : '');?> type="text" name="sourceFrom" id="sourceFrom" size="5" maxlength="4" value="<?php print (!is_null($sourceFrom) ? $sourceFrom : '');?>" />
				&nbsp;
				<?php print get_text('To','Tournament'); ?>: <input <?php print (in_array('sourceTo',$errors) ? ' class="error"' : '');?> type="text" name="sourceTo" id="sourceTo" size="5" maxlength="4" value="<?php print (!is_null($sourceTo) ? $sourceTo : '');?>" />
			</td>
		</tr>

		<tr class="Divider"><td></td></tr>

		<tr><th><?php print get_text('Destination');?></th></tr>

		<tr>
			<td class="Center">
				<?php print get_text('Session');?>: <?php print $comboEndSession;?>
				&nbsp;&nbsp;
				<?php print get_text('From','Tournament'); ?>: <input <?php print (in_array('destFrom',$errors) ? ' class="error"' : '');?> type="text" name="destFrom" id="destFrom" size="5" maxlength="4" value="<?php print (!is_null($destFrom) ? $destFrom : '');?>" />
			</td>
		</tr>
		<tr>
			<td class="Center">
				<input type="hidden" name="command" value="OK"/>
				<input type="button" id="btnOk" value="<?php print get_text('CmdOk');?>" />
			</td>
		</tr>
		<?php if ($msg!='') { ?>
			<tr class="Divider"><td></td></tr>
			<tr><td><?php print $msg;?></td></tr>
		<?php }?>
	</table>
</form>

<?php include('Common/Templates/tail.php');?>