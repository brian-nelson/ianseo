<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(__FILE__) . '/config.php');

CheckTourSession(true);
checkACL(AclAccreditation, AclReadWrite);
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Sessions.inc.php');

$sessions=GetSessions('Q',true);

if (isset($_REQUEST['Exec'])) {
	$_SESSION['chk_Turni']=array();
	$_SESSION['chk_Photo']='';
	$_SESSION['chk_Paid']='';
	$_SESSION['chk_Accredited']='';

	$Lines=(!empty($_REQUEST['chk_Turni']) &&  is_array($_REQUEST['chk_Turni']));
	if(!$Lines or in_array('0', $_REQUEST['chk_Turni'])) {
		$_SESSION['chk_Turni'][]=0;
	}
	foreach($sessions as $s) {
		if(!$Lines or in_array($s->SesOrder, $_REQUEST['chk_Turni'])) {
			$_SESSION['chk_Turni'][]=$s->SesOrder;
		}
	}

	if(!empty($_REQUEST['photo'])) $_SESSION['chk_Photo']=$_REQUEST['photo'];
	if(!empty($_REQUEST['payment'])) $_SESSION['chk_Paid']=$_REQUEST['payment'];
	if(!empty($_REQUEST['accreditation'])) $_SESSION['chk_Accredited']=$_REQUEST['accreditation'];

	$_SESSION['AccOp']=$_REQUEST['AccOp'];
	cd_redirect('Accreditation.php');
	exit;
}

$JS_SCRIPT[]='<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>';
$JS_SCRIPT[]='<script type="text/javascript" src="index.js"></script>';
include('Common/Templates/head.php');

?>
<form name="FrmSelect" method="get" action="">
<table class="Tabella">
<tr><th class="Title" colspan="3"><?php echo get_text('Accreditation','Tournament'); ?></th></tr>
<tr>
<th class="Title" width="33%"><?php echo get_text('Session'); ?></th>
<th class="Title" width="33%"><?php echo get_text('Descr','Tournament'); ?></th>
<th class="Title" width="33%"><?php echo get_text('SetFilter','Tournament'); ?></th>
</tr>
<?php

	print '<tr>';
	print '<td class="Center">';
	print '<input type="checkbox" name="chk_Turni[]" value="0">0<br>' . "\n";
	foreach ($sessions as $s)
	{
		print '<input type="checkbox" name="chk_Turni[]" value="' . $s->SesOrder . '">' . $s->Descr . '<br>' . "\n";
	}
	print '</td>';

	$SqlOp = "SELECT * FROM AccOperationType ORDER BY AOTOrder ";
	$RsOp=safe_r_sql($SqlOp);
	print '<td class="Center">';
	print '<select name="AccOp" onchange="ChangeAccOp(this)">' . "\n";
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
	echo '<td id="ExtraContent"></td>';
	print '</tr>' . "\n";
?>
<tr><td colspan="3" class="Center"><input name="Exec" type="submit" value="<?php print get_text('CmdOk');?>"></td></tr>
</table>
</form>
<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');
?>
