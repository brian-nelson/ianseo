<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('HHT/Fun_HHT.local.inc.php');

	CheckTourSession(true);

	$PAGE_TITLE=get_text('MenuLM_Data insert (Table view)');

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Fun_AJAX.js.php"></script>',
		);

	include('Common/Templates/head.php');
?>
<form name="Frm" method="post" action="InsertPoint2.php">
<input type="hidden" name="Command" value="OK">
<table class="Tabella">
<tr><th class="Title" colspan="2"><?php print get_text('IndFinal'); ?></th></tr>
<tr><th colspan="2"><?php print get_text('FilterRules'); ?></th></tr>
<tr class="Divider"><td colspan="2"></td></tr>
<tr>
	<th class="TitleLeft" colspan="2"><?php print get_text('ScheduledMatches', 'Tournament');?></th>
</tr>
<tr>
	<td colspan="2"><?php echo ComboSes(RowTour(), 'Individuals'); ?></td>
</tr>
<tr>
	<th class="TitleLeft" colspan="2"><?php print get_text('Event');?></th>
</tr>
<tr>
<td colspan="2">
<?php
	$Select
		= "SELECT EvCode,EvEventName "
		. "FROM Events "
		. "WHERE EvTournament = " . StrSafe_DB($_SESSION['TourId']) . "	AND EvTeamEvent='0' AND EvFinalFirstPhase!=0 "
		. "ORDER BY EvProgr ASC ";
	$Rs=safe_r_sql($Select);

	print '<select name="d_Event[]" id="d_Event" multiple="multiple" onChange="javascript:ChangeEvent(0);">' . "\n";
	//print '<option value="">' . get_text('AllEvents') . '</option>' . "\n";
	if (safe_num_rows($Rs)>0)
	{
		while ($Row=safe_fetch($Rs))
		{
			print '<option value="' . $Row->EvCode . '">' . $Row->EvCode . ' - ' . get_text($Row->EvEventName,'','',true) . '</option>' . "\n";
		}
	}
	print '</select>' . "\n";
?>
</td>
</tr>
<tr>
<td width="15%" align="right"><b><?php print get_text('Phase');?></b></td>
<td>
<select name="d_Phase" id="d_Phase">
</select>
</td>
</tr>
<tr>
<td width="15%" align="right"><b><?php print get_text('ManTie');?></b></td>
<td>
<select name="d_Tie" id="d_tie">
<option value="1"><?php print get_text('Yes');?></option>
<option value="0" selected><?php print get_text('No');?></option>
</select>
</td>
</tr>
<tr>
<td width="15%" align="right"><b><?php print get_text('ManSetPoint');?></b></td>
<td>
<select name="d_SetPoint" id="d_SetPoint">
<option value="1"><?php print get_text('Yes');?></option>
<option value="0"><?php print get_text('No');?></option>
</select>
</td>
</tr>
<tr><td colspan="2" class="Center"><input type="submit" value="<?php print get_text('CmdOk');?>"></td></tr>
</table>
</form>
<div id="idOutput"></div>
<script type="text/javascript">ChangeEvent(0);</script>
<?php
	include('Common/Templates/tail.php');
?>