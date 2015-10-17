<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	if (isset($_REQUEST['Command']))
	{
		if ($_REQUEST['Command']=='OK' && !IsBlocked(BIT_BLOCK_PUBBLICATION))
		{
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

			$URL="http://www.ianseo.net/TourCheckCodes.php";
			if(!empty($CFG->OVERRIDES) and !empty($CFG->CheckCodes)) $URL=$CFG->CheckCodes;
			$ch=curl_init($URL);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, array(
				"ToId" => intval($_REQUEST['OnlineId']),
				"Auth1" => stripslashes($_REQUEST['OnlineAuth']),
				"Auth2" => stripslashes($_REQUEST['OnlineAuthA2A']),
				"ToCode" => $code,
				"CheckImgs" => '1',
			));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$varResponse=explode('|', curl_exec($ch));
			curl_close($ch);

			if(substr($varResponse[0],0,2) == 'OK') {
				$_SESSION['OnlineAuth']='';
				$_SESSION['OnlineAuthA2A']='';

				if($varResponse[0]=='OK' or $varResponse[0]=='OK-1') $_SESSION['OnlineAuth']=stripslashes($_REQUEST['OnlineAuth']);
				if($varResponse[0]=='OK' or $varResponse[0]=='OK-2') $_SESSION['OnlineAuthA2A']=stripslashes($_REQUEST['OnlineAuthA2A']);
				$_SESSION['OnlineId']=intval($_REQUEST['OnlineId']);
				$_SESSION['OnlineEventCode']=$code;
				$return='Tournament/UploadResults.php';
				if(!empty($_REQUEST['return'])) $return=$_REQUEST['return'];

				// No header images for PDF...
				$_SESSION['SendOnlinePDFImages']=(trim($varResponse[1]) ? false : true);

				// sets the online code inside the tournament...
				safe_w_SQL("update Tournament set ToOnlineId=".intval($_REQUEST['OnlineId'])." where ToId={$_SESSION['TourId']}");

				cd_redirect($CFG->ROOT_DIR . $return);
			} else {
				$ErrorMessage=get_text($varResponse[0], 'Tournament');
			}
		}
	}

	$onlineId=(empty($_SESSION['OnlineId']) ? '' : $_SESSION['OnlineId']);
	$onlineAuth=(empty($_SESSION['OnlineAuth']) ? '' : $_SESSION['OnlineAuth']);
	$onlineAuthA2A=(empty($_SESSION['OnlineAuthA2A']) ? '' : $_SESSION['OnlineAuthA2A']);
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
	echo '<tr class="error"><td colspan="2" align="center" style="padding:5px; font-size:200%; color:red; ">'.$ErrorMessage.'</td></tr>' ;
}
?>
			<tr>
				<td style="width:30%;" class="Bold Right"><?php print get_text('OnlineId','Tournament'); ?></td>
				<td class="Left"><input type="text" name="OnlineId" value="<?php print $onlineId; ?>"></td>
			</tr>
			<tr>
				<td style="width:30%;" class="Bold Right"><?php print get_text('AuthCode','Tournament'); ?></td>
				<td class="Left"><input type="password" name="OnlineAuth" value="<?php print $onlineAuth; ?>"></td>
			</tr>
			<tr>
				<td style="width:30%;" class="Bold Right"><?php print get_text('AuthCodeA2A','Tournament'); ?></td>
				<td class="Left"><input type="password" name="OnlineAuthA2A" value="<?php print $onlineAuthA2A; ?>"></td>
			</tr>
			<tr>
				<td class="Center" colspan="2">
					<input type="submit" value="<?php print get_text('CmdOk') ?>">&nbsp;&nbsp;
					<input type="reset" value="<?php print get_text('CmdCancel'); ?>">&nbsp;&nbsp;
				</td>
			</tr>
<?php
	$Select
		= "SELECT ToCode,ToName,ToCommitee,ToComDescr,ToWhere,ToWhenFrom,ToWhenTo "
		. "FROM Tournament  "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";

	$Rs=safe_r_sql($Select);
	$MyRow=safe_fetch($Rs);

	$mail=array(get_text('RequestHeader','Tournament'));
	$mail[]='--------------------';
	$mail[]="Code: " . $MyRow->ToCode;
	$mail[]='Name: ' . $MyRow->ToName;
	$mail[]='ComCode: ' . $MyRow->ToCommitee;
	$mail[]='ComName: ' . $MyRow->ToComDescr;
	$mail[]='Where: ' . $MyRow->ToWhere;
	$mail[]='From: ' . $MyRow->ToWhenFrom ;
	$mail[]='To: ' . $MyRow->ToWhenTo;
	$mail[]='Password: ' ;
	$mail[]='--------------------';
	$mail[]=get_text('RequestDisclaimer','Tournament');

	$mailBody=rawurlencode(implode("\n", $mail));
?>
			<tr><th colspan="2"><?php print get_text('GetCredentials','Tournament'); ?></th></tr>
			<tr><td colspan="2"><?php print get_text('GetCredentialsExplained','Tournament') . '<br/>' . nl2br(get_text('RequestDisclaimer','Tournament')); ?></td></tr>

			<tr><td colspan="2" nowrap="nowrap">Code: <?php print $MyRow->ToCode; ?></td></tr>
			<tr><td colspan="2" nowrap="nowrap">Name: <?php print $MyRow->ToName; ?></td></tr>
			<tr><td colspan="2" nowrap="nowrap">ComCode: <?php print $MyRow->ToCommitee ; ?></td></tr>
			<tr><td colspan="2" nowrap="nowrap">ComName: <?php print $MyRow->ToComDescr; ?></td></tr>
			<tr><td colspan="2" nowrap="nowrap">Where: <?php print $MyRow->ToWhere;?></td></tr>
			<tr><td colspan="2" nowrap="nowrap">From: <?php print $MyRow->ToWhenFrom ; ?></td></tr>
			<tr><td colspan="2" nowrap="nowrap">To: <?php print $MyRow->ToWhenTo; ?></td></tr>
			<tr><td colspan="2" nowrap="nowrap">Password: </td></tr>

			<tr><td colspan="2" class="Center"><a href="mailto:codes@ianseo.net?subject=Online%20Code%20Request&body=<?php echo $mailBody; ?>"><?php print get_text('ClickToMailRequest','Tournament'); ?></a></td></tr>

		</table>
	</form>
</div>
<?php
	include('Common/Templates/tail.php');
?>