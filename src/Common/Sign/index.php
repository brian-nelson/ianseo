<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	CheckTourSession(true);

	$PAGE_TITLE=get_text('Sign/guide-board','Tournament');
	include('Common/Templates/head.php');
?>
<form name="frm" method="post" action="PDFSign.php" target="PrintOut">
	<table class="Tabella">
		<tr><th colspan="2" class="Title"><?php print get_text('Sign/guide-board','Tournament'); ?></th></tr>
		<tr>
			<th class="TitleLeft" style="width:15%;"><?php print get_text('Row-1','Tournament'); ?></th>
			<td><input type="text" name="First" value="" style="width:100%"></td>
		</tr>
		<tr>
			<th class="TitleLeft" style="width:15%;"><?php print get_text('Row-2','Tournament'); ?></th>
			<td><input type="text" name="Second" value="" style="width:100%"></td>
		</tr>
		<tr><td class="Center" colspan="2"><input type="submit" name="Command" value="<?php print get_text('Print', 'Tournament'); ?>"></td></tr>
	</table>
</form>
<?php
	include('Common/Templates/tail.php');
?>