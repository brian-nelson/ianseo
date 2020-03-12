<?php
/*
													- TourOff.php -
	Distrugge la sessione in corso e (di default) rimanda all'index
*/


	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');

	$BackTo = $CFG->ROOT_DIR . 'index.php';	// pagina a cui ritornare in caso di successo

	if (isset($_REQUEST['BackTo']))
		$BackTo=$_REQUEST['BackTo'];

	EraseTourSession();
	header('Location: ' . $BackTo);
	exit;
?>