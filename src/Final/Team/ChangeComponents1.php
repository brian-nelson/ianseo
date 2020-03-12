<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_Number.inc.php');
	require_once('Common/Fun_FormatText.inc.php');

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclTeams, AclReadWrite);

	$combo
		= '<select name="ev">'
		. '<option value="">--</option>';

	$query
		= "SELECT "
			. "EvCode,EvEventName "
		. "FROM "
			. "Events "
		. "WHERE "
			. "EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 AND EvFinalFirstPhase!=0 "
		. "ORDER BY "
			. "EvCode ASC ";
	$rs=safe_r_sql($query);

	if (safe_num_rows($rs)>0)
	{
		while ($MyRow=safe_fetch($rs))
		{
			$combo.='<option value="' . $MyRow->EvCode . '">' . get_text($MyRow->EvEventName,'','',true) . '</option>';
		}
	}

	$combo.='</select>';


	$PAGE_TITLE=get_text('ChangeComponents');

	include('Common/Templates/head.php');
?>
<div align="center">
	<div class="half">
		<form name="frm" method="post" action="ChangeComponents2.php">
		<input type="hidden" name="Command" value="OK"/>
			<table class="Tabella">
				<tr><th class="Title" colspan="2"><?php print get_text('ChangeComponents'); ?></th></tr>
				<tr>
					<td><?php print get_text('Event'); ?></td>
					<td><?php print $combo; ?></td>
				</tr>
				<tr><td colspan="2" class="Center"><input type="submit" value="<?php print get_text('CmdOk');?>"/></td></tr>
			</table>
		</form>
	</div>
</div>
<?php
	include('Common/Templates/tail.php');
?>