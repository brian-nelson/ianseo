<?php

require_once(dirname(__FILE__) . '/cfg.php');

// Check the coherence of Qualifications and Entries tables
safe_w_sql("insert ignore into Qualifications (QuId) select EnId from Entries left join Qualifications on EnId=QuId where QuId is null and EnTournament in ($TourId)");

$errMsg='';
if($_POST) {

	//$errMsg=get_text('PhotoUpError','Tournament');

	if (!IsBlocked(BIT_BLOCK_PARTICIPANT)) {
		require_once('Common/PhotoResize.php');

		$id=(empty($_REQUEST['id']) ? '0' : intval($_REQUEST['id']));

		if(!empty($_REQUEST['remove'])) {
			// ask for the removal of the picture
			$query =  "DELETE FROM Photos WHERE PhEnId=" . StrSafe_DB($id) . " ";
			$rs=safe_w_sql($query);
			@unlink($CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-En-'.$id.'.jpg');
			if($EnSelect=GetAccBoothEnWhere($id, true, true)) {
				LogAccBoothQuerry("DELETE FROM Photos WHERE PhEnId=(select EnId from Entries where $EnSelect)");
			}

			$errMsg='';
		} elseif(!empty($_FILES['file']['size'])) {
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
			//$errMsg=get_text('PhotoUpError','Tournament');
		}
	}
}


// $=(isset($_REQUEST['']) ? intval($_REQUEST['']) : 0);
// $=(isset($_REQUEST['']) ? intval($_REQUEST['']) : 0);

$PAGE_TITLE=get_text('TourPartecipants','Tournament');

$JS_SCRIPT=array(
	phpVars2js(array(
		'StrAreYouSure'=>get_text('MsgAreYouSure'),
        'rootDir' => $CFG->ROOT_DIR,
        'strStatus_0' => get_text('CmdOk'),
        'strStatus_1' => get_text('Status_1'),
        'strStatus_5' => get_text('Status_5'),
        'strStatus_6' => get_text('Status_6'),
        'strStatus_7' => get_text('Status_7'),
        'strStatus_8' => get_text('Status_8'),
        'strStatus_9' => get_text('Status_9'),
	)),
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
	'<script type="text/javascript" src="index.js"></script>',
	'<link href="./index.css" rel="stylesheet" type="text/css">',
);

$ONLOAD=' onload="getRows()"';

include('Common/Templates/head.php');
?>
<div id="Accreditation" sort="<?php echo $Sort; ?>" sortorder="<?php echo $SortOrder ;?>">
<div class="Title"><?php echo get_text('TourPartecipants','Tournament') ?></div>
<div class="GenFlex">
	<div><input type="button" onclick="addRow()" value="<?php print get_text('CmdAdd','Tournament'); ?>"></div>
	<div><input onclick="getRows()" type="checkbox" id="AllTargets"   <?php echo ($AllTargets   ? ' checked="checked"' : ''); ?>><?php print get_text('AllTargets','Tournament');?></div>
	<div><input onclick="getRows()" type="checkbox" id="ShowTourCode" <?php echo ($ShowTourCode ? ' checked="checked"' : ''); ?>><?php print get_text('ShowTourCode','Tournament');?></div>
	<div><input onclick="getRows()" type="checkbox" id="ShowPicture" <?php echo ($ShowPicture ? ' checked="checked"' : ''); ?>><?php print get_text('ShowPicture','Tournament');?></div>
	<div><input onclick="getRows()" type="checkbox" id="ShowLocalBib" <?php echo ($ShowLocalBib ? ' checked="checked"' : ''); ?>><?php print get_text('ShowLocalCode','Tournament');?></div>
	<div><input onclick="getRows()" type="checkbox" id="ShowEmail"    <?php echo ($ShowEmail    ? ' checked="checked"' : ''); ?>><?php print get_text('ShowEmail','Tournament');?></div>
	<div><input onclick="getRows()" type="checkbox" id="ShowCaption"  <?php echo ($ShowCaption  ? ' checked="checked"' : ''); ?>><?php print get_text('ShowCaption','Tournament');?></div>
	<div><input onclick="getRows()" type="checkbox" id="ShowCountry2" <?php echo ($ShowCountry2 ? ' checked="checked"' : ''); ?>><?php print get_text('ShowCountry2','Tournament');?></div>
	<div><input onclick="getRows()" type="checkbox" id="ShowCountry3" <?php echo ($ShowCountry3 ? ' checked="checked"' : ''); ?>><?php print get_text('ShowCountry3','Tournament');?></div>
	<div><input onclick="getRows()" type="checkbox" id="ShowDisable"  <?php echo ($ShowDisable  ? ' checked="checked"' : ''); ?>><?php print get_text('ShowDisable','Tournament');?></div>
	<div><input onclick="getRows()" type="checkbox" id="ShowAgeClass"  <?php echo ($ShowAgeClass  ? ' checked="checked"' : ''); ?>><?php print get_text('ShowAgeClass','Tournament');?></div>
	<div><input onclick="getRows()" type="checkbox" id="ShowSubClass"  <?php echo ($ShowSubClass  ? ' checked="checked"' : ''); ?>><?php print get_text('ShowSubClass','Tournament');?></div>
</div>
<div id="Rows"></div>
</div>

<?php
$acl = actualACL();
if($acl[AclParticipants] == AclReadWrite) {
?>
<div id="PhotoFrame">
    <div id="PhotoFrameTitle" align="center">Foto<img src="<?php echo $CFG->ROOT_DIR.'Common/Images/status-noshoot.gif'; ?>" align="right" onclick="closePhoto()"></div>

    <div id="photo-manager">
        <form name="frm" id="frm" method="post" action=""  enctype="multipart/form-data">
            <input type="hidden" name="id" id="PhotoId" value="" />

            <p align="center">
                <img src="photo.php?mode=y&val=130&id=0" id="PhotoPhoto"/>
            </p>

            <p>
                <input type="checkbox" name="remove" id="remove" value="1" />&nbsp;<?php print get_text('PhotoDelete','Tournament'); ?>
                <br/><br/>
                <input type="file" name="file" size="8" />
            </p>

            <input type="submit" value="<?php print get_text('CmdOk') ?>"/>
        </form>

        <div id="error-msg" style="color: #ff0000; font-size: 10px;">
<?php
echo $errMsg;
?>
        </div>
        <div><button type="button" onclick="closePhoto()">Chiudi</button></div>
    </div>
</div>
<?php
}

include('Common/Templates/tail.php');
