<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_Number.inc.php');
	require_once('Common/Fun_FormatText.inc.php');

	CheckTourSession(true);

	include('Common/Templates/head.php');

?>
<form name="Frm" method="get" action="AbsIndividual2.php">
<table class="Tabella">
<TR><TH class="Title" colspan="2"><?php print get_text('ShootOff4Final') . ' - ' . get_text('Individual');?></TH></TR>
<tr class="Divider"><td colspan="2"></td></tr>
<tr>
<th class="TitleLeft" width="15%"><?php print get_text('Event');?></th>
<td>
<select name="EventCode">
<option value=""><?php print get_text('SelectSession','Tournament');?></option>
<?php
	$Select
		= "SELECT EvCode,EvTournament,	EvEventName, EvElim1, EvElim2 "
		. "FROM Events "
		. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='0' "
		. "AND (EvElim1>0 OR EvElim2>0) "
		. "ORDER BY EvProgr ASC ";
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)>0)
	{
		$opts1=array();
		$opts2=array();
		while($MyRow=safe_fetch($Rs)) {
			if($MyRow->EvElim1>0) {
				$opts = '<option value="' . $MyRow->EvCode . '#0"';
				if(in_array($MyRow->EvCode, $_SESSION['MenuElim1'])) $opts.= ' style="color:red"';
				$opts.= '>' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true) . ' (' . get_text('Eliminations_1') . ')</option>' . "\n";
				$opts1[]= $opts;
			}

			if($MyRow->EvElim2>0) {
				$opts = '<option value="' . $MyRow->EvCode . '#1"' ;
				if(in_array($MyRow->EvCode, $_SESSION['MenuElim2'])) $opts.= ' style="color:red"';
				$opts.='>' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true) . ' (' . get_text('Eliminations_2') . ')</option>'. "\n";
				$opts2[]= $opts;
			}
		}
		echo implode('',$opts1);
		if($opts1 and $opts2) echo '<option value="">=========</option>';
		echo implode('',$opts2);
	}
?>
</select>
</td>
</tr>
<tr><td class="Center" colspan="2"><input type="submit" name="Command" value="<?php print get_text('CmdNext');?>"></td></tr>
</table>
</form>
<?php
	include('Common/Templates/tail.php');
?>