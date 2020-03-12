<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_Number.inc.php');
	require_once('Common/Fun_FormatText.inc.php');

	CheckTourSession(true);
    checkACL(AclTeams, AclReadWrite);

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
<?php

$Select
    = "SELECT EvCode,EvTournament,	EvEventName, (EvCodeParent!='') as hasParent  "
    . "FROM Events "
    . "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='1' AND EvFinalFirstPhase!=0 "
    . "ORDER BY EvProgr ASC ";
$Rs=safe_r_sql($Select);

$rows=min(10, max(5, safe_num_rows($Rs)));

echo '<select name="EventCodeMult[]" multiple="multiple" size="'.$rows.'">';
while($MyRow=safe_fetch($Rs)) {
    echo '<option value="' . $MyRow->EvCode . '"'.(in_array($MyRow->EvCode, $_SESSION['MenuFinT'])?' style="color:red"':''). ($MyRow->hasParent ? ' disabled':'') .'>' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true) . '</option>' . "\n";
}
echo '</select>';

?>
</td>
</tr>
<tr><td class="Center" colspan="2"><input type="submit" value="<?php print get_text('CmdNext');?>"></td></tr>
</table>
</form>
<?php
	include('Common/Templates/tail.php');
?>