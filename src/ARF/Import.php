<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
	require_once('Common/ARF/ARFInput.class.php');

	/*$xml=file_get_contents('input-test.xml');

	$arf=new ARFInput($xml);

	$arf->checkExist();
	list($tourCode,$importedEntries,$badEntries,$postError)=$arf->import();

	print 'Imported: ' . $importedEntries . '<br>';

	print '<pre>';
	print 'bad: ';
	print_r($badEntries);
	print '</pre>';

	print 'Post Error: ' . $postError;*/

	$command=(isset($_REQUEST['Command']) ? $_REQUEST['Command'] : null);
	$msg='';

	if (!is_null($command))
	{
		if ($command=='NEXT')
		{
			$fileName='';

			switch ($_FILES["fileup"]["error"])
			{
				case UPLOAD_ERR_OK:
					$fileName=$_FILES["fileup"]['tmp_name'];
					break;
				case UPLOAD_ERR_NO_FILE:
					$msg=get_text('FileNotUploaded','HTT');
					break;
				default:
					$msg=get_text('UnexpectedError');
			}

			if ($fileName!='')
			{
				$xml=file_get_contents($fileName);

				$arf=new ARFInput($xml);

				if ($arf->getError()!=999)
				{
					list($tourCode,$importedEntries,$badEntries,$postError)=$arf->import();

					$msg
						.=get_text('Report','Tournament') . '<br/>'
						. get_text('ImportedTour','Tournament') . ': ' . $tourCode . '<br/>'
						. get_text('ImportedEntries','Tournament') . ': ' . $importedEntries . '<br/>'
						. get_text('BadEntries','Tournament') . ': ' . join(',',$badEntries) . '<br/>'
						. get_text('PostProcError','Tournament') . ': ' . ($postError ? get_text('Yes') : get_text('No'));
				}
				else
				{
					$msg=get_text('BlockedPhase','Tournament');
				}

			}
		}
	}

	include('Common/Templates/head.php');
?>
<div align="center">
	<div class="half">
		<form method="post" enctype="multipart/form-data" action="">
			<input type="hidden" name="Command" value="NEXT">
			<table class="Tabella">
				<tr><th colspan="2" class="Title"><?php print get_text('ImportARFFile','Tournament'); ?></th></tr>
				<tr>
					<th class="TitleLeft"><?php print get_text('File','HTT'); ?></th>
					<td>
						<input type="file" name="fileup">&nbsp;
						<input type="submit" name="Command" value="<?php print get_text('CmdNext') ?>">
					</td>
				</tr>
				<?php if ($msg!='') { ?>
					<tr><td class="Bold" colspan="2"><?php print $msg; ?></td>
				<?php } ?>
			</table>
		</form>
	</div>
</div>
<?php
	include('Common/Templates/tail.php');
?>