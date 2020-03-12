<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');

	CheckTourSession(true, 'popup');

// Al richiamo del popup pulisco la dir temporanea
	foreach(glob($CFG->DOCUMENT_PATH . 'Tournament/TmpDownload/*.*') as $ff)
		@unlink($ff);

	$JS_SCRIPT = array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Tournament/Fun_AJAX_ExportAndSend.js.php"></script>',
		);
	include('Common/Templates/head-popup.php');

?>
<table class="Tabella">
<tr><th class="Title" colspan="2"><?php print get_text('Export2Fitarco','Tournament');?></th></tr>
<tr><td class="FontMedium Bold Center" colspan="2"><?php print get_text('ExportInstructions','Tournament'); ?></td></tr>
<tr><th class="TitleLeft" width="25%"><?php print get_text('ExportRefEMail','Tournament'); ?></th><td><input type="text" id="d_RefEmail" size="50"></td></tr>
<tr class="Spacer"><td colspan="2"></td></tr>
<tr><td class="Title" colspan="2"><?php print get_text('ExportNotes','Tournament'); ?></td></tr>
<tr><td colspan="2" class="Center"><textarea id="d_Notes" rows="6" cols="60"></textarea></td></tr>
<tr><td colspan="2" class="Center"><input type="button" id="Command" value="<?php print get_text('CmdSend','Tournament'); ?>" onClick="javascript:ExportAndSend('<?php print $_REQUEST['Code']?>','<?php print addslashes($CFG->DOCUMENT_PATH) ;?>',<?php print $_REQUEST['Ind']; ?>,<?php print $_REQUEST['Team']; ?>);"></td></tr>
<tr><td colspan="2" class="Center"><a class="Link" href="javascript:window.close();"><?php print get_text('Close'); ?></a></td></tr>
</table>
<br>
<table class="Tabella">
<tr><th class="SubTitle"><?php print get_text('Report','Tournament'); ?></th></tr>
<tr><td id="Report"></td></tr>
</table>
<div id="idOutput">	</div>
<?php include('Common/Templates/tail-popup.php'); ?>