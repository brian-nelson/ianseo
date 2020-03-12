<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	if (!CheckTourSession() || empty($_REQUEST['id']) || !($id=$_REQUEST['id'])) printCrackError();
    checkACL(AclParticipants, AclReadWrite, false);

	$errMsg='';

	$command=isset($_REQUEST['command']) ? $_REQUEST['command'] : null;

	if($_POST) {
		$errMsg=get_text('PhotoUpError','Tournament');

		if (!IsBlocked(BIT_BLOCK_PARTICIPANT)) {
			include('Common/PhotoResize.php');
			if(!empty($_REQUEST['remove'])) {
				// ask for the removal of the picture
				$query =  "DELETE FROM Photos WHERE PhEnId=" . StrSafe_DB($id) . " ";
				$rs=safe_w_sql($query);
				@unlink($CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-En-'.$id.'.jpg');
				if($EnSelect=GetAccBoothEnWhere($id, true, true)) {
					LogAccBoothQuerry("DELETE FROM Photos WHERE PhEnId=(select EnId from Entries where $EnSelect)");
				}

				$errMsg='';
			} elseif(!empty($_FILES['file']['name'])) {
				$file=$_FILES['file'];
				$ImName = $CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-En-'.$id.'.jpg';
				if($image=photoresize($file)) {
					require_once('Common/CheckPictures.php');
					$Booth='';
					if($_SESSION['AccBooth']) {
						// pictures will be recorded in a Database!
						$q=safe_r_sql("select EnCode, EnIocCode, ToCode from Entries inner join Tournament on EnTournament=ToId where EnId={$id}");
						$Booth=safe_fetch($q);
					}

					if(InsertPhoto($id, $image, $Booth)) {
						$errMsg=get_text('PhotoUploaded','Tournament');
					}
				} else {
					// otherwise deletes the reference!
					$query =  "DELETE FROM Photos WHERE PhEnId=" . StrSafe_DB($id) . " ";
					if($EnSelect=GetAccBoothEnWhere($id, true, true)) {
						LogAccBoothQuerry("DELETE FROM Photos WHERE PhEnId=(select EnId from Entries where $EnSelect)");
					}
				}
			} else {
				// why did you press OK for? :D
				$errMsg=get_text('PhotoUpError','Tournament');
			}
		}
	}

	$JS_SCRIPT=array(
		'<link href="../../Partecipants-exp/css/partecipants.css" media="screen" rel="stylesheet" type="text/css">',
		);

	include('Common/Templates/head-min.php');
?>
<div id="photo-manager">
	<form name="frm" id="frm" method="post" action=""  enctype="multipart/form-data">
		<input type="hidden" name="id" value="<?php print $id; ?>" />
		<input type="hidden" name="command" value="OK" />


		<p align="center">
			<img src="../common/photo.php?mode=y&val=130&id=<?php print $id; ?>" />
		</p>

		<p>
			<input type="checkbox" name="remove" id="remove" value="1" />&nbsp;<?php print get_text('PhotoDelete','Tournament'); ?>
			<br/><br/>
			<input type="file" name="file" size="8" />
		</p>

		<input type="submit" value="<?php print get_text('CmdOk') ?>"/>
	</form>

	<div id="error-msg" style="color: #ff0000; font-size: 10px;">
		<?php print $errMsg; ?>
	</div>
</div>
<?php
	include('Common/Templates/tail-min.php');
?>