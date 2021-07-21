<?php
	define('debug',false);	// settare a true per l'output di debug
	require_once(dirname(dirname(__FILE__)) . '/config.php');
    checkACL(AclCompetition, AclReadWrite);

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}

	$NumErr=0;

	if (isset($_REQUEST['Command']))
	{
		if ($_REQUEST['Command']=='UPDATE')
		{
			require_once('Common/CheckPictures.php');
//Immagine di Sinistra
			if(isset($_FILES) && array_key_exists('UploadedFileL',$_FILES) &&  $_FILES['UploadedFileL']['error']==UPLOAD_ERR_OK && $_FILES['UploadedFileL']['size']>0 && $_FILES['UploadedFileL']['size']<=262143)
			{
			    $TmpData = file_get_contents($_FILES['UploadedFileL']['tmp_name']);
			    $TmpData = StrSafe_DB($TmpData);
			    $Rs=safe_w_sql("UPDATE Tournament SET ToImgL=" . $TmpData . " WHERE ToId=" . StrSafe_DB($_SESSION['TourId']));

				unlink($_FILES['UploadedFileL']['tmp_name']);
				CheckPictures();
			}
			elseif(isset($_REQUEST["DeleteL"]) && $_REQUEST["DeleteL"]==1)
			{
			    $Rs=safe_w_sql("UPDATE Tournament SET ToImgL='' WHERE ToId=" . StrSafe_DB($_SESSION['TourId']));
			    @unlink('../TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToLeft.jpg');
			}
//Immagine di Destra
			if(isset($_FILES) && array_key_exists('UploadedFileR',$_FILES) &&  $_FILES['UploadedFileR']['error']==UPLOAD_ERR_OK && $_FILES['UploadedFileR']['size']>0 && $_FILES['UploadedFileR']['size']<=262143)
			{
			    $TmpData = addslashes(fread(fopen($_FILES['UploadedFileR']['tmp_name'], "r"), filesize($_FILES['UploadedFileR']['tmp_name'])));
			    $Rs=safe_w_sql("UPDATE Tournament SET ToImgR='" . $TmpData . "' WHERE ToId=" . StrSafe_DB($_SESSION['TourId']));
				unlink($_FILES['UploadedFileR']['tmp_name']);
				CheckPictures();
			}
			elseif(isset($_REQUEST["DeleteR"]) && $_REQUEST["DeleteR"]==1)
			{
			    $Rs=safe_w_sql("UPDATE Tournament SET ToImgR='' WHERE ToId=" . StrSafe_DB($_SESSION['TourId']));
			    @unlink('../TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToRight.jpg');
			}
//Immagine in basso
			if(isset($_FILES) && array_key_exists('UploadedFileB',$_FILES) &&  $_FILES['UploadedFileB']['error']==UPLOAD_ERR_OK && $_FILES['UploadedFileB']['size']>0 && $_FILES['UploadedFileB']['size']<=262143)
			{
			    $TmpData = addslashes(fread(fopen($_FILES['UploadedFileB']['tmp_name'], "r"), filesize($_FILES['UploadedFileB']['tmp_name'])));
			    $Rs=safe_w_sql("UPDATE Tournament SET ToImgB='" . $TmpData . "' WHERE ToId=" . StrSafe_DB($_SESSION['TourId']));
				unlink($_FILES['UploadedFileB']['tmp_name']);
				CheckPictures();
			}
			elseif(isset($_REQUEST["DeleteB"]) && $_REQUEST["DeleteB"]==1)
			{
			    $Rs=safe_w_sql("UPDATE Tournament SET ToImgB='' WHERE ToId=" . StrSafe_DB($_SESSION['TourId']));
			    @unlink('../TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToBottom.jpg');
			}
		}
	}


	$JS_SCRIPT = array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Tournament/Fun_JS.js"></script>',
		);
	$PAGE_TITLE=get_text('LogoManagement','Tournament');

	include('Common/Templates/head.php');

	/* DEBUG */
	if (debug==true)
	{
		if (isset($_REQUEST['Command']))
		{
			if ($_REQUEST['Command']=='SAVE')
			{
				print '<pre>';
				print_r($Arr_Values2Check_ManSessions);
				print '</pre>';
			}
		}
	}
	/* FINE DEBUG */

	$Rs=NULL;
	$MyRow=NULL;
	$Select
		= "SELECT LENGTH(ToImgL) as L, LENGTH(ToImgR) as R, LENGTH(ToImgB) as B, LENGTH(ToImgB2) as B2 "
		. "FROM Tournament "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)==1)
	{
		$MyRow=safe_fetch($Rs);
	}
	else
	{
		print get_text('CrackError');
		include('Common/Templates/tail.php');
		exit;
	}
?>
<form name="FrmFile" method="POST" action="" enctype="multipart/form-data">
<table class="Tabella">
<tr><th class="Title" colspan="3"><?php print get_text('LogoManagement','Tournament'); ?></th></tr>
<tr class="Divider"><td colspan="3"></td></tr>
<tr>
	<th width="30%"><?php echo get_text('LogoL','Tournament'); ?></th>
	<td width="40%" rowspan="3">&nbsp;</td>
	<th width="30%"><?php echo get_text('LogoR','Tournament'); ?></th>
</tr>
<tr>
	<td class="Center Bold"><?php echo get_text('ImgInfo','Tournament'); ?></td>
	<td class="Center Bold"><?php echo get_text('ImgInfo','Tournament'); ?></td>
</tr>

<tr>
	<td class="Center">
	<br/>
<?php
	if($MyRow->L>0)
	{
		echo '<img src="../Common/TourLogo.php?Type=L&amp;W=300" alt="' . get_text('LogoL','Tournament') . '" /><br />';
		echo '<input name="DeleteL" type="checkbox" value="1"/>&nbsp;' . get_text('CmdDelete','Tournament');
	}
	else
	{
		echo '<input type="hidden" name="MAX_FILE_SIZE" value="262143"><input name="UploadedFileL" type="file" size="50" />';
	}
?>
	<br/>
	</td>
	<td class="Center">
	<br/>
<?php
	if($MyRow->R>0)
	{
		echo '<img src="../Common/TourLogo.php?Type=R&amp;W=300" alt="' . get_text('LogoR','Tournament') . '" /><br />';
		echo '<input name="DeleteR" type="checkbox" value="1"/>&nbsp;' . get_text('CmdDelete','Tournament');
	}
	else
	{
		echo '<input type="hidden" name="MAX_FILE_SIZE" value="262143"><input name="UploadedFileR" type="file" size="50" />';
	}
?>
	<br/>
	</td>

</tr>
<tr>
	<th colspan="3"><?php echo get_text('LogoB','Tournament'); ?></th>
</tr>
<tr>
	<td colspan="3" class="Center Bold"><?php echo get_text('ImgInfo','Tournament'); ?></td>
</tr>
<tr>
	<td colspan="3" class="Center">
<?php
	if($MyRow->B>0)
	{
		echo '<img src="../Common/TourLogo.php?Type=B&amp;W=1000" alt="' . get_text('LogoB','Tournament') . '" /><br />';
		echo '<input name="DeleteB" type="checkbox" value="1"/>&nbsp;' . get_text('CmdDelete','Tournament');
	}
	else
	{
		echo '<input type="hidden" name="MAX_FILE_SIZE" value="262143"><input name="UploadedFileB" type="file" size="50" />';
	}
?>		</td>
</tr>
<tr class="Divider"><td colspan="3"></td></tr>
<tr>
	<td colspan="3" class="Center">
	<input type="hidden" name="Command" value="UPDATE">
	<input type="submit" value="<?php print get_text('CmdUpdate');?>">&nbsp;&nbsp;
	<br><br>
	<a class="Link" href="index.php"><?php echo get_text('Back') ?></a>
</td></tr>
</table>
</form>
<?php
	include('Common/Templates/tail.php');
?>