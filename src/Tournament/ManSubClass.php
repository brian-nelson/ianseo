<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Tournament/Fun_Tournament.local.inc.php');
require_once('Common/Lib/CommonLib.php');
checkACL(AclCompetition, AclReadWrite);

if (!CheckTourSession()) {
    print get_text('CrackError');
    exit;
}

$JS_SCRIPT = array(
	phpVars2js(array(
		'MsgAreYouSure' => get_text('MsgAreYouSure'),
		'MsgRowMustBeComplete' => str_replace('<br>','\n',get_text('MsgRowMustBeComplete')),
	)),
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
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
<tr><th class="Title" colspan="4"><?php print get_text('SubClasses','Tournament');?></th></tr>
<?php
	$Rs=safe_r_sql("SELECT * FROM `SubClass` WHERE ScTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY ScViewOrder ASC ");

	echo '<tr><th width="5%">' . get_text('SubClass','Tournament') . '</th><th width="30%">'. get_text('Descr','Tournament') . '</th><th width="10%">' . get_text('Progr') . '</th><th width="15%">&nbsp;</th></tr>';
    echo '<tbody id="tbody_subclass">';
    while ($MyRow=safe_fetch($Rs)) {
        echo '<tr id="SubClass_' . $MyRow->ScId .'">'.
                '<td class="Bold Center">'. $MyRow->ScId . '</td>'.
                '<td><input type="text" name="d_ScDescription_' . $MyRow->ScId .'" id="d_ScDescription_' . $MyRow->ScId . '" size="56" maxlength="32" value="'.$MyRow->ScDescription .'" onBlur="UpdateField(\'SC\',\'d_ScDescription_'. $MyRow->ScId . '\')"></td>'.
                '<td class="Center"><input type="text" name="d_ScViewOrder_'. $MyRow->ScId . '" id="d_ScViewOrder_' .$MyRow->ScId . '" size="3" maxlength="3" value="'. $MyRow->ScViewOrder . '" onBlur="UpdateField(\'SC\',\'d_ScViewOrder_'. $MyRow->ScId.'\')"></td>'.
                '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" border="0" alt="#" title="#" onclick="DeleteRow(\'SC\',\''.$MyRow->ScId.'\')"></td>'.
            '</tr>';
    }
    echo '</tbody>';
?>
<tr id="NewSubCl" class="Spacer"><td colspan="4"></td></tr>
<tr>
<td class="Center"><input type="text" name="New_ScId" id="New_ScId" size="3" maxlength="2"></td>
<td><input type="text" name="New_ScDescription" id="New_ScDescription" size="56" maxlength="32"></td>
<td class="Center"><input type="text" name="New_ScViewOrder" id="New_ScViewOrder" size="3" maxlength="3"></td>
<td class="Center"><input type="button" name="CommandSc" value="<?php print get_text('CmdSave');?>" onClick="AddSubClass()"></td>
</tr>
</table>

</div>
</div>
<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');
?>
