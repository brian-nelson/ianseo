<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Fun_Final.local.inc.php');

	if(empty($_REQUEST['TourCode']) and empty($_SESSION['TourCode'])) die('No Tournament Selected');

	$TourCode=(empty($_REQUEST['TourCode']) ? $_SESSION['TourCode'] : $_REQUEST['TourCode']);

//
//	$ColWidth=100/($cols*2);
////	$JS_SCRIPT[]='<meta http-equiv="refresh" content="0.1">';
	$Col1='#4040ff';
	$Col2='#4040ff'; //'#ffff80';
	$BgCol1='#D0D0D0';
	$BgCol2='#D0D0D0';
	$JS_SCRIPT[]='<style>';
	$JS_SCRIPT[]='body, table, #PopupContent {margin:0;padding:0;width:100%;height:100%;}';
//	$JS_SCRIPT[]='td {border:1px solid black;}';
	$JS_SCRIPT[]='#Tabella {width:100%; height:100%;}';
	$JS_SCRIPT[]='#Score1, #Score2, #MiniScore1, #MiniScore2, #name1, #name2,  .ScoreArrows1, .ScoreArrows2, #error {color:'.$Col1.'; background-color:'.$BgCol1.';font-weight:bold;width:50%;text-align:center;}';
	$JS_SCRIPT[]='#Score2, #MiniScore2, #name2, .ScoreArrows2 {color:'.$Col2.'; background-color:'.$BgCol2.';}';

//	$JS_SCRIPT[]='.ScoreArrows1, .ScoreArrows2 {width:'.$ColWidth.'%; font-size:'.($_SESSION['WINWIDTH']/$OutString).'px}';
	$JS_SCRIPT[]='#MiniScore1, #MiniScore2 {text-align:right;}';
	$JS_SCRIPT[]='#MiniScore2 {text-align:left}';
	$JS_SCRIPT[]='#error {width:100%; height:100%;font-size: 100px; }';
	$JS_SCRIPT[]='</style>';
	$JS_SCRIPT[]='<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/Fun_JS.inc.js"></script>';
	$JS_SCRIPT[]='<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>';
	$JS_SCRIPT[]='<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Fun_AJAX_ShowScore.js"></script>';
	$JS_SCRIPT[]='<script type="text/javascript">var TourCode="'.$TourCode.'";</script>';
	$ONLOAD=' onload="GetMatches()"';

	include('Common/Templates/head-popup.php');
	include('Common/Templates/tail-popup.php');
?>