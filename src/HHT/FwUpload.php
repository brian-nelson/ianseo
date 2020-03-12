<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('serial.php');
	require_once('Fun_HHT.local.inc.php');

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="../Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="Fun_JS.js"></script>',
	);

	$PAGE_TITLE='Firmware Upload';

	include('Common/Templates/head.php');
?>
<form name="FrmDownload" method="POST" action="" enctype="multipart/form-data">
<table class="Tabella">
<tr><th class="Title">Firmware Upload</th></tr>
<tr><td>
HTT Line&nbsp;&nbsp;&nbsp;<?php  print ComboHHT();?><br>
</td></tr>
<tr><td>
HTT number&nbsp;&nbsp;&nbsp;<input name="HttNumber" size="5" maxlength="3"><br>
</td></tr>
<tr><td>
File to upload&nbsp;&nbsp;&nbsp;<input name="Fw" type="file" size="50"><br>
</td></tr>
<tr><td class="Center">
<input type="submit" value="<?php echo get_text('CmdOk');?>">
</td></tr>
</table>
</form>
<?php

	$HttNumber = (isset($_REQUEST['HttNumber']) ? intval($_REQUEST['HttNumber']) : null);

	if(is_numeric($HttNumber) &&  isset($_FILES['Fw']['tmp_name'])){
		$DataSource="";
		if(strlen($_FILES["Fw"]["name"])) {
			switch ($error)
			{
				case UPLOAD_ERR_OK:
					$DataSource=$_FILES["Fw"]["tmp_name"];
					break;
				case UPLOAD_ERR_NO_FILE:
					unset($_REQUEST["Fw"]);
					break;
				default:
					unset($_REQUEST["Fw"]);
			}
		}
		if($DataSource!= "")
		{
			$fwp = fopen($DataSource,"r");

			if($fwp)
			{
//"mF69a-7Ji1Z&pjU3"
				$Frames=PrepareTxFrame($HttNumber,"qW4Gl56fR3HJ80+s");
				$Updated=SendHTT(HhtParam($_REQUEST['x_Hht']),$Frames);
				sleep(4);
				$Frames=array();

				while (!feof($fwp)) {
    				$buffer = fgets($fwp, 4096);
    				$buffer = str_replace(chr(10),'',$buffer);
    				$buffer = str_replace(chr(13),'',$buffer);
    				$Frames = array_merge($Frames, PrepareTxFrame($HttNumber,$buffer));
				}
				fclose($fwp);
				echo "<pre>\n";
				$Updated=SendHTT(HhtParam($_REQUEST['x_Hht']),$Frames,false,5000);
				echo "</pre>\n";
			}
		}

	}

	include('Common/Templates/tail.php');
?>