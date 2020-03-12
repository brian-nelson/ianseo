<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Fun_Modules.php');

CheckTourSession(true);

$endNo=0;
if(isset($_REQUEST["Submit"]) && is_numeric($_REQUEST["EndNo"])) {
	setModuleParameter("Vegas", "EndNo", $_REQUEST["EndNo"]-1);
}
$endNo = getModuleParameter("Vegas", "EndNo", 0)+1;

$PAGE_TITLE=get_text('SetVegas-WAF', 'Install');

include('Common/Templates/head.php');

echo '<form name="Frm" method="post" action="">';
echo '<table class="Tabella">';
echo '<tr><th class="Title" colspan="2">' . get_text('SetVegas-WAF', 'Install'). '</th></tr>';

echo '<tr class="Divider"><td colspan="2"></td></tr>';
echo '<tr><th width="25%">' . get_text('End (volee)') . '</th><td width="75%">';
echo '<input type="text" size="8" maxlength="3" name="EndNo" value="' . $endNo . '">';
echo '</td></tr>';

echo '<tr><td colspan="2" class="Center"><input type="submit" name="Submit" value="' . get_text('CmdSave') . '"></td></tr>';
echo '<tr class="Divider"><td colspan="2"></td></tr>';
echo '<tr><td colspan="2" class="Center"><a href="index.php">' . get_text('Back') . '</a></td></tr>';
echo '</table>';
echo '</form>';


include('Common/Templates/tail.php');
?>