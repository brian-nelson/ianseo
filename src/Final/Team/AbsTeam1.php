<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_Number.inc.php');
	require_once('Common/Fun_FormatText.inc.php');

	CheckTourSession(true);


	$PAGE_TITLE=get_text('ShootOff4Final') . ' - ' . get_text('Team');

	include('Common/Templates/head.php');
?>
<form name="Frm" method="post" action="AbsTeam2.php">
<table class="Tabella">
<TR><TH class="Title" colspan="2"><?php print get_text('ShootOff4Final') . ' - ' . get_text('Team');?></TH></TR>
<tr class="Divider"><td colspan="2"></td></tr>
<tr>
<th class="TitleLeft" width="15%"><?php print get_text('Event');?></th>
<td>
<select name="EventCode">
<option value=""><?php echo get_text('AllEvents') ?></option>
<?php
	$Select
		= "SELECT EvCode,EvTournament,	EvEventName "
		. "FROM Events "
		. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='1' AND EvFinalFirstPhase!=0 "
		. "ORDER BY EvProgr ASC ";
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)>0)
	{
		while($MyRow=safe_fetch($Rs))
		{
			print '<option value="' . $MyRow->EvCode . '"'.(in_array($MyRow->EvCode, $_SESSION['MenuFinT'])?' style="color:red"':'').'>' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true) . '</option>' . "\n";
		}
	}
?>
</select>
</td>
</tr>
<tr><td class="Center" colspan="2"><input type="submit" value="<?php print get_text('CmdNext');?>"></td></tr>
</table>
</form>
<?php
	include('Common/Templates/tail.php');
?>