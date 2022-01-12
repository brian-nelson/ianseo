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

// sessioni
	$sessions=GetSessions('Q');

	$comboStartSession = '<select name="startSession" id="startSession">'
		. '<option value="0">' . get_text('AllSessions','Tournament') . '</option>';
	$comboEndSession = '<select name="endSession" id="endSession">'
		. '<option value="0">' . get_text('DeleteSession') . '</option>';
	foreach($sessions as $s) {
		$comboStartSession .= '<option value="' . $s->SesOrder. '"' . (!is_null($startSession) && $s->SesOrder==$startSession ? ' selected' : '') . '>' . $s->SesOrder .': ' . $s->SesName . '</option>';
		$comboEndSession .= '<option value="' . $s->SesOrder . '"' . (!is_null($endSession) && $s->SesOrder==$endSession ? ' selected' : '') . '>' . $s->SesOrder .': ' . $s->SesName . '</option>';
	}
	$comboStartSession.='</select>';
	$comboEndSession.='</select>';

	$msg='';
	$msg=get_text('Error');

	if (isset($_REQUEST['command']) and $_REQUEST['command']=='OK' and !IsBlocked(BIT_BLOCK_PARTICIPANT) and $filter!='') {
		$Where="EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND CONCAT(EnDivision, EnClass) LIKE " . StrSafe_DB($filter);
		if ($startSession!=0) {
			$Where .=" AND QuSession=" . StrSafe_DB($startSession) . " ";
		}

		$query="";

		// query per cancellare i bersagli considerando il filtro un NON evento
		safe_w_sql("update Entries inner join Qualifications on EnId=QuId
				set EnTimestamp='".date('Y-m-d H:i:s')."'
				Where (QuSession=" . StrSafe_DB($endSession) . " or QuTargetNo=CONCAT(" . StrSafe_DB($endSession) . "," . ($startSession==0 || $endSession==0  ?  "''" : "SUBSTRING(QuTargetNo,2) ") . ")) and $Where");
		$query = "UPDATE Entries INNER JOIN Qualifications ON EnId=QuId
			SET QuSession=" . StrSafe_DB($endSession) . ",
				QuTargetNo=".($endSession ? "CONCAT(" . StrSafe_DB($endSession) . ", if(QuTarget>0, concat(lpad(QuTarget, 3, '0'), QuLetter), ''))" : "0").",
				QuTarget=".($endSession ? "QuTarget" : "0").",
				QuLetter=".($endSession ? "QuLetter" : "''").",
				QuBacknoPrinted=0,
				QuTimestamp=QuTimestamp
			WHERE $Where";
		$rs=safe_w_SQL($query);
		if ($rs) {
			if (safe_w_affected_rows()>0) {
				$msg=get_text('TargetMoved');
			} else{
				$msg=get_text('NoTargetFound');
			}
		}

	}

	$JS_SCRIPT=array(
		phpVars2js(array(
			'StrAreYouSure' => get_text('MsgAreYouSure')
		)),
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Partecipants/Fun_DeleteTarget.js"></script>'
	);

	include('Common/Templates/head.php');
?>

<form id="frm" method="post" action="<?php print $_SERVER['PHP_SELF'];?>">
	<table class="Tabella">
		<tr><th class="Title"><?php print get_text('MenuLM_MoveSession');?></th></tr>
		<tr class="Divider"><td></td></tr>
		<tr><th><?php print get_text('SourceSession');?></th></tr>
		<tr>
			<td class="Center">
				<?php print get_text('Session');?>: <?php print $comboStartSession;?>
				&nbsp;&nbsp;
				<?php print get_text('FilterOnDivCl','Tournament'); ?>: <input type="text" name="filter" id="filter" size="12" maxlength="10" value="<?php print (!is_null($filter) ? $filter : '');?>" />
			</td>
		</tr>
		<tr class="Divider"><td></td></tr>
		<tr><th><?php print get_text('DestinationSession');?></th></tr>
				<tr>
			<td class="Center">
				<?php print get_text('Session');?>: <?php print $comboEndSession;?>
			</td>
		</tr>
		<tr class="Divider"><td></td></tr>
		<tr>
			<td class="Center">
				<input type="hidden" name="command" value="OK"/>
				<input type="button" id="btnOk" value="<?php print get_text('CmdOk');?>" onClick="doConfirm();" />
			</td>
		</tr>
		<?php if ($msg!='') { ?>
			<tr class="Divider"><td></td></tr>
			<tr><td><?php print $msg;?></td></tr>
		<?php }?>
	</table>
</form>

<?php include('Common/Templates/tail.php');?>
