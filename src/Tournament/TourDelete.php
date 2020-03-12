<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACL(AclRoot, AclReadWrite);

if (!CheckTourSession()) {
    print get_text('CrackError');
    exit;
}

if(isset($_REQUEST["CtrlCode"]) && preg_match("/^[0-9A-F]{8}$/i",$_REQUEST["CtrlCode"]) &&
	isset($_REQUEST["InputCode"]) && preg_match("/^[0-9A-F]{8}$/i",$_REQUEST["InputCode"]) &&
	strcmp($_REQUEST["InputCode"], $_REQUEST["CtrlCode"])==0)
{
	include('Common/Fun_TourDelete.php');
	tour_delete($_SESSION['TourId']);
	header("location: " . $CFG->ROOT_DIR . "Common/TourOff.php");
	exit();
}

$PAGE_TITLE=get_text('DeleteTournament','Tournament');

include('Common/Templates/head.php');

$CtrlCode = substr(md5(date("r")),0,8);
?>
<form action="TourDelete.php" method="get" name="frmConfirmDelete">
<table class="Tabella">
<tr><th class="Title"><?php print get_text('DeleteTournament','Tournament');?></th></tr>
<tr>
<th class="SubTitle"><?php print get_text('SelTour') . ': ' . $_SESSION['TourName'] . ' (' . $_SESSION['TourWhere'] . ' ' . get_text('From','Tournament') . ' ' . $_SESSION['TourWhenFrom'] . ' ' . get_text('To','Tournament') . ' ' . $_SESSION['TourWhenTo'] . ')'; ?></th>
</tr>
<tr><td>
<?php print get_text('DeleteTourConfirm','Tournament') . " <strong>" . $CtrlCode . "</strong>"; ?>
<input type="hidden" name="CtrlCode" value="<?php print $CtrlCode; ?>" />
</td></tr>
<tr><td class="Center">
<input type="text" name="InputCode" maxlength="8" size="10"/>
<br />&nbsp;<br /><input type="submit" value="<?php print get_text('DeleteTournament','Tournament');?>" />
</td></tr>
</table>
</form>
<?php
	include('Common/Templates/tail.php');
?>