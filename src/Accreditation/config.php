<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');


if(empty($_SESSION['TourId']) and $CompCode=GetParameter('AutoCHK-Code') and ($_SERVER['REMOTE_ADDR']=='127.0.0.1' or in_array($_SERVER['REMOTE_ADDR'], explode(',', GetParameter('AutoCHK-IP'))))) {
	include_once('Common/UpdatePreOpen.inc.php');
	include_once('Common/CheckPictures.php');
	$TourId=getIdFromCode($CompCode);
	UpdatePreOpen($TourId);
	if (!CreateTourSession($TourId)) {
		CheckTourSession(true);
		die();
	}
	if(empty($_SESSION['ShortMenu'])) $_SESSION['ShortMenu']=array();
	$_SESSION['ShortMenu']['ACCR'][] = get_text('MenuLM_Accreditation') .'';
	$_SESSION['ShortMenu']['ACCR'][] = get_text('MenuLM_Accreditation') .'|'.$CFG->ROOT_DIR.'Accreditation/index.php';
	$_SESSION['ShortMenu']['ACCR'][] = get_text('TakePicture', 'Tournament') .'|'.$CFG->ROOT_DIR.'Accreditation/AccreditationPicture.php';
	$_SESSION['ShortMenu']['ACCR'][] = '---';
	$_SESSION['ShortMenu']['ACCR'][] = get_text('MenuLM_PrintBadges') .'-new|'.$CFG->ROOT_DIR.'Accreditation/IdCards.php';
	$_SESSION['ShortMenu']['ACCR'][] = get_text('MenuLM_Printout') .'|'.$CFG->ROOT_DIR.'Accreditation/PrintOut.php';

}
