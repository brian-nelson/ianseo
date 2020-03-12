<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Fun_Final.local.inc.php');

	if(empty($_REQUEST['TourCode']) and empty($_SESSION['TourCode'])) die('No Tournament Selected');

	$TourCode=(empty($_REQUEST['TourCode']) ? $_SESSION['TourCode'] : $_REQUEST['TourCode']);
    checkACL(AclOutput,AclReadOnly, true, getIdFromCode($TourCode));
//
//	$ColWidth=100/($cols*2);
////	$JS_SCRIPT[]='<meta http-equiv="refresh" content="0.1">';
	$Col1='#4040ff';
	$Col2='#ffff80';
	$BgCol1='#ffff80';
	$BgCol2='#4040ff';
	$JS_SCRIPT[]='<link href="'.$CFG->ROOT_DIR.'Final/TvScore.css" rel="stylesheet" type="text/css">';
	$JS_SCRIPT[]='<style>';
	$JS_SCRIPT[]='#Score1, #Score2, #MiniScore1, #MiniScore2, #name1, #name2,  .ScoreArrows1, .ScoreArrows2, #error {color:'.$Col1.'; background-color:'.$BgCol1.';}';
	$JS_SCRIPT[]='#Score2, #MiniScore2, #name2, .ScoreArrows2 {color:'.$Col2.'; background-color:'.$BgCol2.';}';
	$JS_SCRIPT[]='</style>';
	$JS_SCRIPT[]='<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>';
	$JS_SCRIPT[]='<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>';
	$JS_SCRIPT[]='<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Fun_AJAX_ShowScore.js"></script>';
	$JS_SCRIPT[]='<script type="text/javascript">var TourCode="'.$TourCode.'";</script>';
	$ONLOAD=' onload="GetMatches()"';

	include('Common/Templates/head-popup.php');
	include('Common/Templates/tail-popup.php');
?>