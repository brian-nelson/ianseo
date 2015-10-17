<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Sessions.inc.php');

	if (isset($_REQUEST['Exec']) && $_REQUEST['Exec']=='OK')
	{
		if (isset($_SESSION['chk_Turni']))
			unset($_SESSION['chk_Turni']);

		if (!empty($_REQUEST['chk_Turni']) &&  is_array($_REQUEST['chk_Turni']))
		{
			foreach ($_REQUEST['chk_Turni'] as $Key => $Value)
			{
				$_SESSION['chk_Turni'][$Key]=$Value;
			}

			$_SESSION['AccOp']=$_REQUEST['AccOp'];
			cd_redirect('Accreditation.php');
			exit;
		}
	}

	include('Common/Templates/head.php');
?>
<form name="FrmSelect" method="get" action="">
<input type="hidden" name="Exec" value="OK">
<table class="Tabella">
<tr><th class="Title" colspan="2"><?php echo get_text('Accreditation','Tournament'); ?></th></tr>
<tr>
<th class="Title" width="50%"><?php echo get_text('Session'); ?></th>
<th class="Title" width="50%"><?php echo get_text('Descr','Tournament'); ?></th>
</tr>
<?php
	$sessions=GetSessions('Q',true);

	print '<tr>';
	print '<td class="Center">';
	print '<input type="checkbox" name="chk_Turni[]" value="0">0<br>' . "\n";
	foreach ($sessions as $s)
	{
		print '<input type="checkbox" name="chk_Turni[]" value="' . $s->SesOrder . '">' . $s->Descr . '<br>' . "\n";
	}
	print '</td>';

	$SqlOp = "SELECT * FROM AccOperationType ORDER BY AOTId ";
	$RsOp=safe_r_sql($SqlOp);
	print '<td class="Center">';
	print '<select name="AccOp">' . "\n";
	print '<option value="">&nbsp;</option>' . "\n";
	if (safe_num_rows($RsOp)>0)
	{
		print '<option value="-1">' . get_text('TakePicture', 'Tournament') . '</option>' . "\n";
		while ($RowO=safe_fetch($RsOp))
		{
			print '<option value="' . $RowO->AOTId . '">' . get_text($RowO->AOTDescr, 'Tournament') . '</option>' . "\n";
		}
	}
	print '</select>' . "\n";
	print '</td>';
	print '</tr>' . "\n";
?>
<tr><td colspan="2" class="Center"><input type="submit" value="<?php print get_text('CmdOk');?>"></td></tr>
</table>
</form>
<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');
?>