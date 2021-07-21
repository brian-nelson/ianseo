<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACL(AclInternetPublish, AclReadWrite);

CheckTourSession(true);

if(!empty($_REQUEST['ForgetPwd'])) {
    SetModuleParameter('SendToIanseo', 'Credentials', (object) array('OnlineId' => '', 'OnlineAuth' => ''));
    if(isset($_SESSION['OnlineId'])) {
        unset($_SESSION['OnlineId']);
    }
    if(isset($_SESSION['OnlineAuth'])) {
        unset($_SESSION['OnlineAuth']);
    }
    CD_redirect(basename(__FILE__));
} else if (!empty($_REQUEST['Command'])) {
    if (!IsBlocked(BIT_BLOCK_PUBBLICATION)) {
        $return='Tournament/UploadResults.php';
        if(!empty($_REQUEST['return'])) {
            $return=$_REQUEST['return'];
        }
        require_once('Common/Lib/CommonLib.php');
	    $ErrorMessage=CheckCredentials($_REQUEST['OnlineId'], $_REQUEST['OnlineAuth'], $return,!empty($_REQUEST['LicenseVoucher']) ? $_REQUEST['LicenseVoucher'] : '');
	    if(!$ErrorMessage) {
            // save the credentials into ModuleParameters
            if(empty($_REQUEST["RememberPwd"])) {
                setModuleParameter('SendToIanseo', 'Credentials', (object)array('OnlineId' => '', 'OnlineAuth' => ''));
            } else {
                setModuleParameter('SendToIanseo', 'Credentials', (object)array('OnlineId' => $_REQUEST['OnlineId'], 'OnlineAuth' => $_REQUEST['OnlineAuth']));
            }
            cd_redirect($CFG->ROOT_DIR . $return);
	    } else {
            setModuleParameter('SendToIanseo', 'Credentials', (object)array('OnlineId' => '', 'OnlineAuth' => ''));
        }
    } else {
	    $ErrorMessage=get_text('LockedProcedure', 'Errors');
    }
}


$Credentials=getModuleParameter('SendToIanseo', 'Credentials', (object) array('OnlineId' => '', 'OnlineAuth' => ''));

$onlineId=(empty($_SESSION['OnlineId']) ? $Credentials->OnlineId : $_SESSION['OnlineId']);
$onlineAuth=(empty($_SESSION['OnlineAuth']) ? $Credentials->OnlineAuth : $_SESSION['OnlineAuth']);
//$onlineAuthA2A=(empty($_SESSION['OnlineAuthA2A']) ? '' : $_SESSION['OnlineAuthA2A']);
$onlineEventCode=(empty($_SESSION['OnlineEventCode']) ? 0 : $_SESSION['OnlineEventCode']);

$PAGE_TITLE=get_text('SetCredentials','Tournament');

include('Common/Templates/head.php');

?>
<div align="center">
	<form name="Frm" method="POST" action="">
	<input type="hidden" name="Command" value="1">
		<table class="Tabella" style="width:50%;">
			<tr><th colspan="2"><?php print get_text('SetCredentials','Tournament'); ?></th></tr>
<?php
if(!empty($ErrorMessage)) {
	echo '<tr><td colspan="2" class="ServerError">'.$ErrorMessage.'</td></tr>' ;
}

echo '<tr>
        <td style="width:30%;" class="Bold Right">'.get_text('OnlineId','Tournament').'</td>
        <td class="Left"><input type="text" style="width:90%" name="OnlineId" value="'.$onlineId.'"></td>
    </tr>';
echo '<tr>
        <td style="width:30%;" class="Bold Right">'.get_text('AuthCode','Tournament').'</td>
        <td class="Left"><input type="password" style="width:90%" name="OnlineAuth" value="'.$onlineAuth.'"></td>
    </tr>';
echo '<tr>
        <td style="width:30%;" class="Bold Right">'.get_text('RememberPwd','Tournament').'</td>
        <td class="Left"><input type="checkbox" name="RememberPwd" value="1" '.(empty($Credentials->OnlineId) ? '':'checked="checked"').'"></td>
    </tr>';
//echo '<tr>
//        <td style="width:30%;" class="Bold Right">'.get_text('AuthCodeA2A','Tournament').'</td>
//        <td class="Left"><input type="password" style="width:100%" name="OnlineAuthA2A" value="'.$onlineAuthA2A.'"></td>
//    </tr>';

if(getModuleParameter('ISK', 'Mode', '')=='pro') {
    if($LicenseNumber=getModuleParameter('ISK', 'LicenseNumber', '')) {
        echo '<tr>
            <td style="width:30%;" class="Bold Right">'.get_text('ISK-LicenseNumber','Api').'</td>
            <td class="Left">'.$LicenseNumber.'</td>
            </tr>';
    //} else {
    //    $ISKLicenseVoucher=getModuleParameter('ISK', 'LicenseVoucher', '');
    //    echo '<tr>
    //        <td style="width:30%;" class="Bold Right">'.get_text('ISK-LicenseVoucher','Api').'</td>
    //        <td class="Left">
    //            <input type="text" style="width:100%" name="LicenseVoucher" value="'.$ISKLicenseVoucher.'">
    //        </td>
    //        </tr>';
    }
}

echo '<tr>
        <td class="Center" colspan="2">
            <input type="submit" value="'.get_text('CmdOk').'">&nbsp;&nbsp;
            <input type="button" value="'.get_text('CmdForgetPwd','Tournament').'" onClick="location.href=\''.go_get('ForgetPwd','1').'\'">
            <input type="reset" value="'.get_text('CmdCancel').'">
        </td>
    </tr>';


// CHECK IF ALREADY AN ONLINE CODE
// CHECK IF DATE OF COMPETITION IS IN AT LEAST 1 DAY
$Select = "SELECT ToOnlineId FROM Tournament WHERE date_sub(ToWhenFrom, interval 1 day)>=current_date() and ToId=" . StrSafe_DB($_SESSION['TourId']) ;
$Rs=safe_r_sql($Select);
if($MyRow=safe_fetch($Rs) and !$MyRow->ToOnlineId) {

	echo '<tr><th colspan="2">'.get_text('GetCredentials','Tournament').'</th></tr>';
	echo '<tr><td colspan="2">'.get_text('GetCredentialsExplained','Tournament') . '<br/>' . nl2br(get_text('RequestDisclaimer','Tournament')).'</td></tr>';
	echo '<tr><td colspan="2"><b>'.get_text('SupportIanseo','Tournament').'</b></td></tr>';
	echo '<tr><th colspan="2" class="Center ServerError"><a href="CodeRequest.php">'.get_text('ClickToRequestCode','Tournament').'</a></th></tr>';
}

?>
		</table>
	</form>
</div>
<?php
	include('Common/Templates/tail.php');
?>
