<?php


	require_once('./config.php');
	require_once('Common/Lib/CommonLib.php');

	$TourId=getIdFromCode($TourCode);
    checkACL(AclOutput,AclReadWrite, true, $TourId);

	$JS_SCRIPT[]='<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>';
	$JS_SCRIPT[]='<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>';
	$JS_SCRIPT[]='<script type="text/javascript" src="Fun_AJAX_ShowFlags.js"></script>';
	$JS_SCRIPT[]='<script type="text/javascript">var TourCode="'.$TourCode.'";</script>';
// 	$JS_SCRIPT[]='<script type="text/javascript">var minHeight="'.$_SESSION['WINHEIGHT'] .'";</script>';
// 	$JS_SCRIPT[]='<script type="text/javascript">var minWidth="'.$_SESSION['WINWIDTH'] .'";</script>';
	$ONLOAD=' onload="GetFlags()"';

	$BackColor=Get_Tournament_Option('AwardBackColor','#d0d0d0');
	$BackColor='none';
	$BackPhoto= $CFG->ROOT_DIR . 'TV/Photos/' . $TourCodeSafe . '--Award--.jpg';
	if(!file_exists($CFG->DOCUMENT_PATH . 'TV/Photos/' . $TourCodeSafe . '--Award--.jpg')) {
		$BackPhoto='';
	}


	$JS_SCRIPT[]='<style>
		body {position: absolute;margin:0; padding:0;top:0;bottom:0;left:0;right:0;}
		#PopupContent {height:100%; width:100%;margin:0; padding:0;background-color:'.$BackColor.'; }
		#FlagContent {position: relative;height:100%; width:100%;background-color:'.$BackColor.';
		'.($BackPhoto ? 'background-image:url("'.$BackPhoto.'");' : '').'
			background-position:"center center";
			background-repeat:"no-repeat";
			background-size:"cover";
		}
		.p-pole {position:absolute; width:3%; text-align:right; overflow:hidden;}
		.f-flag {position:absolute; text-align:left; width: 22%; vertical-align:top; overflow:hidden;}
		.p-silver {left:6%; top:20%; height:80%;}
		.f-silver {left:8.9%; top:20%; height:80%;}
		.p-gold {left:36%; top:10%; height:90%;}
		.f-gold {left:38.9%; top:10%; height:90%;}
		.p-bronze {left:66%; top:30%; height:70%;}
		.f-bronze {left:68.9%; top:30%; height:70%;}
		canvas {width:90%; position:relative; top:100%}
		#flagG {transition: top 20s linear;}
		#flagS {transition: top 17s linear 3s;}
		#flagB {transition: top 14s linear 6s;}
		.p-pole img {width:25%}
		</style>';
	$NOSTYLE=true;
	include('Common/Templates/head-min.php');

	echo '<div id="FlagContent" onclick="raiseFlag()">';

	echo '<div class="p-pole p-silver"><img src="'.($CFG->ROOT_DIR.'Common/Images/pole.svg').'"></div>';
	echo '<div class="f-flag f-silver"><canvas id="flagS"></canvas></div>';

	echo '<div class="p-pole p-gold"><img src="'.($CFG->ROOT_DIR.'Common/Images/pole.svg').'"></div>';
	echo '<div class="f-flag f-gold"><canvas id="flagG"></canvas></div>';

	echo '<div class="p-pole p-bronze"><img src="'.($CFG->ROOT_DIR.'Common/Images/pole.svg').'"></div>';
	echo '<div class="f-flag f-bronze"><canvas id="flagB"></canvas></div>';

	echo '</div>';

	include('Common/Templates/tail-min.php');
?>