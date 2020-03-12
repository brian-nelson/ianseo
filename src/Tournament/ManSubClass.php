<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Tournament/Fun_Tournament.local.inc.php');
    checkACL(AclCompetition, AclReadWrite);

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}

	$JS_SCRIPT = array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Tournament/Fun_AJAX_ManDivClass.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
		);

	$PAGE_TITLE=get_text('ManSubClasses','Tournament');

	include('Common/Templates/head.php');

?>
<div align="center">
<div class="medium">
<table class="Tabella">
<tr><th class="Title" colspan="4"><?php print get_text('ManSubClasses','Tournament');?></th></tr>
<tbody id="tbody_subclass">
<tr><th class="Title" colspan="4"><?php print get_text('SubClasses','Tournament');?></th></tr>
<?php
	$Select
		= "SELECT * "
		. "FROM `SubClass` "
		. "WHERE ScTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "ORDER BY ScViewOrder ASC ";
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)>0)
	{
?>
<tr><th width="5%"><?php print get_text('SubClass','Tournament');?></th><th width="30%"><?php print get_text('Descr','Tournament');?></th><th width="10%"><?php print get_text('Progr');?></th><th width="15%">&nbsp;</th></tr>
<?php
		while ($MyRow=safe_fetch($Rs))
		{
?>
<tr id="SubClass_<?php print $MyRow->ScId;?>">
<td class="Bold Center"><?php print $MyRow->ScId;?></td>
<td><input type="text" name="d_ScDescription_<?php print $MyRow->ScId;?>" id="d_ScDescription_<?php print $MyRow->ScId;?>" size="56" maxlength="32" value="<?php print ManageHTML($MyRow->ScDescription);?>" onBlur="javascript:UpdateField('SC','d_ScDescription_<?php print $MyRow->ScId;?>');"></td>
<td class="Center"><input type="text" name="d_ScViewOrder_<?php print $MyRow->ScId;?>" id="d_ScViewOrder_<?php print $MyRow->ScId;?>" size="3" maxlength="3" value="<?php print ManageHTML($MyRow->ScViewOrder);?>" onBlur="javascript:UpdateField('SC','d_ScViewOrder_<?php print $MyRow->ScId;?>');"></td>
<td class="Center"><a href="javascript:DeleteRow('SC','<?php print $MyRow->ScId;?>','<?php print urlencode(get_text('MsgAreYouSure'))	;	?>');"><img src="<?php echo $CFG->ROOT_DIR ?>Common/Images/drop.png" border="0" alt="#" title="#"></a></td>
</tr>
<?php
		}
	}
?>
<tr id="NewSubCl" class="Spacer"><td colspan="4"></td></tr>
<tr>
<td class="Center"><input type="text" name="New_ScId" id="New_ScId" size="3" maxlength="2"></td>
<td><input type="text" name="New_ScDescription" id="New_ScDescription" size="56" maxlength="32"></td>
<td class="Center"><input type="text" name="New_ScViewOrder" id="New_ScViewOrder" size="3" maxlength="3"></td>
<td class="Center"><input type="button" name="CommandSc" value="<?php print get_text('CmdSave');?>" onClick="javascript:AddSubClass('<?php print str_replace('<br>','\n',get_text('MsgRowMustBeComplete'));?>');"></td>
</tr>
</tbody>
</table>

</div>
</div>
<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');
?>