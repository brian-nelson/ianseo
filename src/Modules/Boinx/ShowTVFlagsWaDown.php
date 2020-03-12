<?php
	define('debug',false);	// settare a true per l'output di debug


	require_once('./config.php');
	require_once('Common/Lib/CommonLib.php');

	$TourId=getIdFromCode($TourCode);

	$JS_SCRIPT[]='<style>';

	$JS_SCRIPT[]='body {color:#0F0F0F; background-color:#D0D0D0; font-size:30px;}
			@media screen {
				#PopupContent {background-color:#D0D0D0; }
			}';
	$JS_SCRIPT[]='</style>';
	$JS_SCRIPT[]='<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>';
	$JS_SCRIPT[]='<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>';
	$JS_SCRIPT[]='<script type="text/javascript" src="Fun_AJAX_ShowFlags.js"></script>';
	$JS_SCRIPT[]='<script type="text/javascript">var TourCode="'.$TourCode.'";</script>';
// 	$JS_SCRIPT[]='<script type="text/javascript">var minHeight="'.$_SESSION['WINHEIGHT'] .'";</script>';
// 	$JS_SCRIPT[]='<script type="text/javascript">var minWidth="'.$_SESSION['WINWIDTH'] .'";</script>';
	$ONLOAD=' onload="GetFlagsWaDown()"';

	$BackColor=Get_Tournament_Option('AwardBackColor','#d0d0d0');
	$BackPhoto= $CFG->ROOT_DIR . 'TV/Photos/' . $TourCodeSafe . '--Award--.jpg';
	if(!file_exists($CFG->DOCUMENT_PATH . 'TV/Photos/' . $TourCodeSafe . '--Award--.jpg')) {
		$BackPhoto='';
	}


	$JS_SCRIPT[]='<style>
		body {margin:0; padding:0;}
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
		canvas {width:90%; position:relative; top:0%}
		#flagG {transition: top 20s linear 2s;}
		#flagS {transition: top 20s linear 1s;}
		#flagB {transition: top 20s linear;}
		.p-pole img {width:25%}
		</style>';
	include('Common/Templates/head-popup.php');

	echo '<div id="FlagContent" onclick="lowerFlag()">';

	echo '<div class="p-pole p-silver"><img src="'.($CFG->ROOT_DIR.'Common/Images/pole.svg').'"></div>';
	echo '<div class="f-flag f-silver"><canvas id="flagS"></canvas></div>';

	echo '<div class="p-pole p-gold"><img src="'.($CFG->ROOT_DIR.'Common/Images/pole.svg').'"></div>';
	echo '<div class="f-flag f-gold"><canvas id="flagG"></canvas></div>';

	echo '<div class="p-pole p-bronze"><img src="'.($CFG->ROOT_DIR.'Common/Images/pole.svg').'"></div>';
	echo '<div class="f-flag f-bronze"><canvas id="flagB"></canvas></div>';

	echo '</div>';

	include('Common/Templates/tail-popup.php');
?>