<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_Number.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Fun_CONI.local.inc.php');

	CheckTourSession(true);

	$comboEvent=makeComboEvent();

	$comboRound=makeComboRound();

	include('Common/Templates/head.php');
?>
<form name="frm" method="post" action="ManTarget2_1.php">

	<table class="Tabella">
		<tr><th class="Title" colspan="2"><?php print get_text('MenuLM_TargetAssignmentFirst'); ?></th></tr>
		<tr class="Divider"><td colspan="2"></td></tr>

		<tr>
			<th class="TitleLeft" width="15%"><?php print get_text('Event');?></th>
			<td><?php print $comboEvent; ?></td>
		</tr>

		<tr>
			<th class="TitleLeft" width="15%"><?php print get_text('Round','Tournament');?></th>
			<td><?php print $comboRound; ?></td>
		</tr>



		<tr><td class="Center" colspan="2"><input type="submit" value="<?php print get_text('CmdNext');?>"></td></tr>
	</table>

</form>

<?php include('Common/Templates/tail.php'); ?>