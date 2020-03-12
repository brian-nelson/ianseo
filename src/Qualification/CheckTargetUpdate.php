<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_Sessions.inc.php');

	CheckTourSession(true);
    checkACL(AclQualification, AclReadOnly);

	if (isset($_REQUEST['Command']) && $_REQUEST['Command']=='OK' && $_REQUEST['x_Session']!=-1
		&& isset($_REQUEST['x_Hour']) && preg_match('/[0-9]{1,2}:[0-9]{1,2}/i',$_REQUEST['x_Hour']))
	{
	// riformatto l'ora
		list($hh,$mm)=explode(':',$_REQUEST['x_Hour']);
		$hh=str_pad($hh,2,'0',STR_PAD_LEFT);
		$mm=str_pad($mm,2,'0',STR_PAD_LEFT);

		$_REQUEST['x_Hour']= $hh . ':' . $mm;
	}

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Qualification/Fun_AJAX_CheckTargetUpdate.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Qualification/Fun_JS.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
		);

	$PAGE_TITLE=get_text('CheckTargetUpdate','Tournament');

	include('Common/Templates/head.php');
?>
<div id="idOutput"></div>
<?php
	$Select
		= "SELECT ToId,ToNumDist AS TtNumDist "
		. "FROM Tournament "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$RsTour=safe_r_sql($Select);

	$RowTour=NULL;
	$ComboSes='';
	if (safe_num_rows($RsTour)==1)
	{
		$RowTour=safe_fetch($RsTour);

		$sessions=GetSessions('Q');

		$ComboSes = '<select name="x_Session" id="x_Session">' . "\n";
		$ComboSes.= '<option value="-1">---</option>' . "\n";

		foreach ($sessions as $s)
		{
			$ComboSes.= '<option value="' . $s->SesOrder . '"' . (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$s->SesOrder ? ' selected' : '') . '>' . $s->Descr . '</option>' . "\n";
		}


		$ComboSes.= '</select>' . "\n";
?>
<form name="FrmParam" method="POST" action="">
<input type="hidden" name="Command" value="OK">
<input type="hidden" name="xxx" id="Command">
<table class="Tabella">
<TR><TH class="Title" colspan="3"><?php print get_text('CheckTargetUpdate','Tournament');?></TH></TR>
<tr class="Divider"><TD colspan="3"></TD></tr>
<tr>
<th width="5%"><?php print get_text('Session');?></th>
<th width="5%"><?php print get_text('Hour','Tournament');?></th>
<th width="5%">&nbsp;</th>
</tr>
<tr>
<td class="Center"><?php print $ComboSes; ?></td>
<td class="Center">
<input type="text" name="x_Hour" id="x_Hour" size="5" maxlength="5" value="<?php print (isset($_REQUEST['x_Hour']) ? $_REQUEST['x_Hour'] : '');?>"><br>
<a class="Link" href="javascript:ReqServerTime(0);"><?php echo get_text('Now','Tournament') ?></a>&nbsp;&nbsp;
<a class="Link" href="javascript:ReqServerTime(5);"><?php echo get_text('5Before','Tournament') ?></a>&nbsp;&nbsp;
<a class="Link" href="javascript:ReqServerTime(15);"><?php echo get_text('15Before','Tournament') ?></a>
</td>
<td><input type="submit" value="<?php print get_text('CmdOk');?>"></td>
</tr>
</table>
</form>
<?php
		if (isset($_REQUEST['Command']) && $_REQUEST['Command']=='OK' && $_REQUEST['x_Session']!=-1)
		{

?>
<table class="Tabella">
<tbody id="tbody">
</tbody>
</table>
<script type="text/javascript">CheckTarget();</script>
<?php
		}
	}

	include('Common/Templates/tail.php');
	$mid->printFooter();
?>