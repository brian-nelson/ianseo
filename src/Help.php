<?php

	require_once(dirname(__FILE__) . '/config.php');

	if(empty($_GET['help'])) PrintCrackError('popup');

	$helpfile=$_GET['help'];

	include('Common/Templates/head-popup.php');
	if(file_exists($CFG->DOCUMENT_PATH . 'Common/Help/' . $helpfile)) {
		include($CFG->DOCUMENT_PATH . 'Common/Help/' . $helpfile);
	} else {
		echo get_text('CrackError');
	}

	include('Common/Templates/tail-popup.php');

