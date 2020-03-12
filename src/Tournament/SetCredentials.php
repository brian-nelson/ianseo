<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACL(AclInternetPublish, AclReadWrite);

CheckTourSession(true);

if (isset($_REQUEST['Command'])) {
    if ($_REQUEST['Command']=='OK' && !IsBlocked(BIT_BLOCK_PUBBLICATION)) {
		// Query per estrarre l'eventcode del torneo
        $Select
            = "SELECT ToCode "
            . "FROM Tournament "
            . "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
        $Rs=safe_r_sql($Select);

        $code=0;
        if (safe_num_rows($Rs)==1) {
            $r=safe_fetch($Rs);
            $code=$r->ToCode;
        }

	    $postdata = http_build_query( array(
		    "ToId" => intval($_REQUEST['OnlineId']),
		    "Auth1" => stripslashes($_REQUEST['OnlineAuth']),
		    "Version" => '2',
		    "ToCode" => $code,
		    "CheckImgs" => '1',
            'Voucher' => (!empty($_REQUEST['LicenseVoucher']) ? $_REQUEST['LicenseVoucher'] : ''),
            ), '', '&' );

	    $opts = array('http' =>
		    array(
			    'method'  => 'POST',
			    'header'  => 'Content-type: application/x-www-form-urlencoded',
			    'content' => $postdata
		    )
	    );

        $URL=$CFG->IanseoServer."TourCheckCodes.php";
	    $context = stream_context_create($opts);
	    $stream = fopen($URL, 'r', false, $context);
	    $tmp = null;

	    if($stream===false) {
		    $tmpErr = error_get_last();
		    die($tmpErr["message"]);
	    }

        // retrieving data from ianseo
        $varResponse=explode('|', stream_get_contents($stream));

        if(substr($varResponse[0],0,2) == 'OK') {
	        $ErrorMessage='';
            list(, $_SESSION['OnlineServices'])=explode('-', $varResponse[0]);
            //$_SESSION['OnlineAuthA2A']='';

            $_SESSION['OnlineAuth']=stripslashes($_REQUEST['OnlineAuth']);
            //if($varResponse[0]=='OK' or $varResponse[0]=='OK-1') $_SESSION['OnlineAuth']=stripslashes($_REQUEST['OnlineAuth']);
            //if($varResponse[0]=='OK' or $varResponse[0]=='OK-2') $_SESSION['OnlineAuthA2A']=stripslashes($_REQUEST['OnlineAuthA2A']);
            $_SESSION['OnlineId']=intval($_REQUEST['OnlineId']);
            $_SESSION['OnlineEventCode']=$code;
            $return='Tournament/UploadResults.php';
            if(!empty($_REQUEST['return'])) $return=$_REQUEST['return'];

            // No header images for PDF...
            $_SESSION['SendOnlinePDFImages']=(trim($varResponse[1]) ? false : true);

            // sets the online code inside the tournament...
            safe_w_SQL("update Tournament set ToOnlineId=".intval($_REQUEST['OnlineId'])." where ToId={$_SESSION['TourId']}");

            // check if the service is available
            switch($return) {
                case 'Modules/UpdateWeb/UpdateWeb.php':
                    // old arrow2arrow permission
                    if(!($_SESSION['OnlineServices']&2)) {
                        $ErrorMessage=get_text('ServiceNotAvailable', 'Tournament');
                    }
                    break;
                case 'Modules/SyncroWeb/index.php':
                    // InfoSystem permission
                    if(!($_SESSION['OnlineServices']&4)) {
                        $ErrorMessage=get_text('ServiceNotAvailable', 'Tournament');
                    }
                    break;
                case 'Tournament/UploadResults.php':
                    // InfoSystem permission
                    if(!($_SESSION['OnlineServices']&1)) {
                        $ErrorMessage=get_text('ServiceNotAvailable', 'Tournament');
                    }
                    break;
                default:
            }
            if(!$ErrorMessage) {
                cd_redirect($CFG->ROOT_DIR . $return);
            }
        } else {
            $ErrorMessage=get_text($varResponse[0], 'Tournament');
        }
    }
}

$onlineId=(empty($_SESSION['OnlineId']) ? '' : $_SESSION['OnlineId']);
$onlineAuth=(empty($_SESSION['OnlineAuth']) ? '' : $_SESSION['OnlineAuth']);
//$onlineAuthA2A=(empty($_SESSION['OnlineAuthA2A']) ? '' : $_SESSION['OnlineAuthA2A']);
$onlineEventCode=(empty($_SESSION['OnlineEventCode']) ? 0 : $_SESSION['OnlineEventCode']);

$PAGE_TITLE=get_text('SetCredentials','Tournament');

include('Common/Templates/head.php');

?>
<div align="center">
	<form name="Frm" method="POST" action="">
	<input type="hidden" name="Command" value="OK">
		<table class="Tabella" style="width:50%;">
			<tr><th colspan="2"><?php print get_text('SetCredentials','Tournament'); ?></th></tr>
<?php
if(!empty($ErrorMessage)) {
	echo '<tr><td colspan="2" class="ServerError">'.$ErrorMessage.'</td></tr>' ;
}

echo '<tr>
        <td style="width:30%;" class="Bold Right">'.get_text('OnlineId','Tournament').'</td>
        <td class="Left"><input type="text" style="width:100%" name="OnlineId" value="'.$onlineId.'"></td>
    </tr>';
echo '<tr>
        <td style="width:30%;" class="Bold Right">'.get_text('AuthCode','Tournament').'</td>
        <td class="Left"><input type="password" style="width:100%" name="OnlineAuth" value="'.$onlineAuth.'"></td>
    </tr>';
//echo '<tr>
//        <td style="width:30%;" class="Bold Right">'.get_text('AuthCodeA2A','Tournament').'</td>
//        <td class="Left"><input type="password" style="width:100%" name="OnlineAuthA2A" value="'.$onlineAuthA2A.'"></td>
//    </tr>';

if(getModuleParameter('ISK', 'Mode', '')=='pro' and $CFG->ISK_PRO_DEBUG) {
    if($LicenseNumber=getModuleParameter('ISK', 'LicenseNumber', '')) {
        echo '<tr>
            <td style="width:30%;" class="Bold Right">'.get_text('ISK-LicenseNumber','Api').'</td>
            <td class="Left">'.$LicenseNumber.'</td>
            </tr>';
    } else {
        $ISKLicenseVoucher=getModuleParameter('ISK', 'LicenseVoucher', '');
        echo '<tr>
            <td style="width:30%;" class="Bold Right">'.get_text('ISK-LicenseVoucher','Api').'</td>
            <td class="Left">
                <input type="text" style="width:100%" name="LicenseVoucher" value="'.$ISKLicenseVoucher.'">
            </td>
            </tr>';
    }
}

echo '<tr>
        <td class="Center" colspan="2">
            <input type="submit" value="'.get_text('CmdOk').'">&nbsp;&nbsp;
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