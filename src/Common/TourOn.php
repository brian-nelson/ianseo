<?php
/*
													- TourOn.php -
	Imposta la sessione per il torneo selezionato.
	Se ci sono problemi, distrugge la sessione e (di default) rimanda all'index principale.
*/

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/UpdatePreOpen.inc.php');
require_once('Common/CheckPictures.php');

$BackTo = $CFG->ROOT_DIR . 'Main.php';	// pagina a cui ritornare in caso di successo

if (!(isset($_REQUEST['ToId']) && is_numeric($_REQUEST['ToId'])>0)) {
	print get_text('CrackError');
	exit;
} else {
	if (isset($_REQUEST['BackTo'])) {
		$BackTo=$_REQUEST['BackTo'];
	}
}

UpdatePreOpen($_REQUEST['ToId']);

if ($Tour=CreateTourSession($_REQUEST['ToId'])) {
	header('Location: ' . $BackTo);
	exit;
} else {
	EraseTourSession();
	header('Location: '.$CFG->ROOT_DIR.'index.php');
	exit;
}

