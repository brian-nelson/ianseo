<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

	$id=isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
	if (!CheckTourSession() || is_null($id))
	{
		print get_text('CrackError');
		exit;
	}
?>
<iframe id="iframe_eventsAccess" src="<?php echo $CFG->ROOT_DIR; ?>Partecipants-exp/iframes/eventsAccess_iframe.php?id=<?php print $id; ?>" frameborder="0" style="width:100%; height:100%; overflow:hidden;"></iframe>