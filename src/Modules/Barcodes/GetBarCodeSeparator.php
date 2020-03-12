<?php

if(!empty($_GET['SEP'])
        and (mb_strlen(($_GET['SEP']))==1
            or (mb_strlen(($_GET['SEP']))==3 and mb_substr($_GET['SEP'],0,1)==mb_substr($_GET['SEP'], 1, 1) and mb_substr($_GET['SEP'],0,1)==mb_substr($_GET['SEP'],2,1)))) {

    $_SESSION['BarCodeSeparator']=mb_substr($_GET['SEP'], 0, 1);
	CD_redirect($_SERVER['PHP_SELF']);
}

$ONLOAD=' onLoad="javascript:document.Frm.sep.focus()"';
$JS_SCRIPT=array();

include('Common/Templates/head.php');

?>
<form name="Frm" method="get" action="">
<table class="Tabella2">
	<tr>
		<th class="Title" colspan="3"><?php print get_text('CheckBarcodeSeparator','BackNumbers');?></th>
	</tr>
	<tr>
		<th><?php print get_text('ReadSeparator', 'BackNumbers');?></th>
	</tr>
	<?php
	echo '<tr><td class="Center">';
	echo '<input type="text" size="2" name="SEP" id="sep" tabindex="1" value="">';
	echo '</td></tr>';
	if(!empty($_GET['SEP'])) {
		echo '<tr><td class="Center">' . $_GET['SEP'] . '</td></tr>';
	}
	echo '<tr>
		<td class="Center" colspan="2"><input type="submit" value="' . get_text('CmdGo','Tournament') . '" id="Vai" onClick="javascript:SendBib();"></td>
		</tr>';
	echo '<tr class="divider"><td colspan="2"></td></tr>
		<tr><th colspan="2"><img src="beiter.png" width="80" hspace="10" alt="Beiter Logo" border="0"/><br>' . get_text('Credits-BeiterCredits', 'Install') . '</th></tr>';
	?>
</table>
</form>
<?php
include('Common/Templates/tail.php');

