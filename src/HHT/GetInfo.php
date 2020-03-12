<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('serial.php');
	require_once('Fun_HHT.local.inc.php');

	//$htt=isset($_REQUEST['htt']) ? str_pad($_REQUEST['htt'],3,'0',STR_PAD_LEFT) : '';
	$Command=isset($_REQUEST['Command']) ? $_REQUEST['Command'] : null;
	$Results=null;
	$ResponseFromHHT=true;
	$HhtRequested=array();
	$HhtAnswered=array();

	if (!is_null($Command))
	{
		if ($Command=='OK')
		{
			$Frames=array();
			if(isset($_REQUEST["htt"]) && strlen(str_replace(" ","",$_REQUEST["htt"]))>0 && preg_match("/^[-,0-9A-Z]*$/i",str_replace(" ","",$_REQUEST["htt"])))
			{
				foreach(explode(",",$_REQUEST["htt"]) as $Value)
				{
					if(preg_match("/^([0-9A-Z]*)\-([0-9A-Z]*)$/i",str_replace(" ","",$Value),$Tmp))
					{
						for($i=intval($Tmp[1]); $i<=intval($Tmp[2]); $i++)
						{
							$Frames=array_merge($Frames,PrepareTxFrame($i,'SW?'));		// versione fw
							$Frames=array_merge($Frames,PrepareTxFrame($i,'READAB'));		// AB
							$Frames=array_merge($Frames,PrepareTxFrame($i,'READCD'));		// CD
							$HhtRequested[]=$i;
						}
					}
					else
					{
						$Frames=array_merge($Frames,PrepareTxFrame(intval($Value),'SW?'));		// versione fw
						$Frames=array_merge($Frames,PrepareTxFrame(intval($Value),'READAB'));		// AB
						$Frames=array_merge($Frames,PrepareTxFrame(intval($Value),'READCD'));		// CD
						$HhtRequested[]=$Value;
					}
				}
			}
/*foreach($Frames as $value)
	echo OutText($value);
exit();*/

			if (count($Frames)>0)
			{
				$ResponseFromHHT=false;
				$Results=SendHTT(HhtParam($_REQUEST['x_Hht']),$Frames);
				if(count($Results)>0) {
					$ResponseFromHHT=true;
					foreach($Results as $frame) {
						$HhtAnswered[$frame['Target']][]=$frame;
					}
				}
/*print '<pre>';
print_r($Results);
print '</pre>';
exit();*/
			}
		}
	}

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
		);

	$PAGE_TITLE=get_text('HTTInfo','HTT');

	include('Common/Templates/head.php');

?>
<div align="center">
	<div class="half">
		<form name="frm" method="post" action="<?php print $_SERVER['PHP_SELF']; ?>">
			<input type="hidden" name="Command" value="OK">
			<table class="Tabella">
<?php
if(!$ResponseFromHHT)
{
	echo '<tr class="error" style="height:35px;"><td colspan="2" class="Center LetteraGrande">' . get_text('HTTNotConnected','HTT') . '</td></tr>';
}
?>
				<tr><th colspan="2"><?php print get_text('HTTInfo','HTT'); ?></th></tr>
				<tr>
					<th class="TitleLeft" width="10%" colspan="2"><?php print get_text('Terminal', 'HTT'); ?></th>
					<td width="90%">
						<input type="text" name="htt" maxlength="50" value="<?php print (isset($_REQUEST["htt"]) ? $_REQUEST["htt"] : ''); ?>">
						&nbsp;&nbsp;&nbsp;
						<?php print ComboHHT(); ?>
						&nbsp;&nbsp;&nbsp;
						<input type="submit" value="<?php print get_text('CmdOk'); ?>">
					</td>
				</tr>
<?php
	if($Results != null)
	{
		$TmpTarget=0;
		$answer='';
		sort($HhtRequested);


		foreach($HhtRequested as $Target) {
			if(empty($HhtAnswered[$Target])) {
				if($TmpTarget!=$Target)
					echo '<tr class="Divider"><td colspan="3"></td></tr>' . "\n";
				echo '<tr><th class="warning" nowrap="nowrap">';
				echo get_text('Target','HTT') . ": " . $Target. "<br>\n";
				echo '</th>';
				echo '<td colspan="2" style="color:white; font-weight:bold; background-color:red">' . get_text('HTTNotConnected','HTT') . '</td>';
				echo '</tr>';
			} else {
				if($TmpTarget!=$Target)
					echo '<tr class="Divider"><td colspan="3"></td></tr>' . "\n";
				echo '<tr><th nowrap="nowrap">';
				echo get_text('Target','HTT') . ": " . $Target. "<br>\n";
				echo '</th>';
				echo '<th nowrap="nowrap">';
				echo get_text('CmdOk') . "<br>\n";
				echo '</th>';
				echo '<td>';
				foreach($HhtAnswered[$Target] as $key1 => $value1) {
					foreach($value1 as $key => $value) {
						if($key!="Target")
						{
							if($key!="Arrows")
							{
								echo get_text($key,'HTT') . ": <b>" . $value . "</b><br>\n";
							}
							else
							{
								echo get_text($key,'HTT') . ": ";
								for ($k=0;$k<strlen($value);++$k)
								{
									switch($value[$k])
									{
										case '0':
											echo "<b>M</b> . ";
											break;
										case chr(158):
											echo "<b>10</b> . ";
											break;
										default:
											echo "<b>" . $value[$k] . "</b> . ";
											break;
									}
								}
								echo "</b><br>\n";
							}

						}
					}
				}
				echo '</td></tr>';
			}
			$TmpTarget=$Target;
		}
	}
?>
			</table>
		</form>
	</div>
</div>
<?php
	include('Common/Templates/tail.php');
?>