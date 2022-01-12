<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

$JSON=array('error' => 1, 'reload'=>false, 'pages' => array());

$Channel=1;

if(!empty($_GET)) {
	foreach($_GET as $k => $v) {
		if(strtolower($k)=='id')
			$Channel=intval($v);
	}
}

$q=safe_r_sql("SELECT TVOId , TVOSide, TVOHeight, TVOName, TVOUrl, TVOMessage, TVORuleId, TVOTourCode, TVORuleType, TVOFile
	FROM TVOut
	where TVORuleType>0 and not (TVOHeight='' or left(TVOHeight,1)='0')
	order by TVOId=$Channel desc, TVOId, TVOSide");

require_once('Common/Lib/Fun_Modules.php');

$JSON['error']=0;
$First=true;
$RealChannel=0;
$Pages=array();
while($r=safe_fetch($q)) {
	if($First==true) {
		$RealChannel=$r->TVOId;
	}
	if($RealChannel!=$r->TVOId) {
		// steps out of the loop
		break;
	}

	// check if there is a request to reload the requested channel
	if(getParameter('TVOUT-Reload-'.$RealChannel, false, 0)) {
		$JSON['reload']=true;
		DelParameter('TVOUT-Reload-'.$RealChannel);

	}

	$First=false;
	switch($r->TVORuleType) {
		case 1:
			// HTML text...
			$JSON['pages'][$r->TVOSide]=getMyScheme().'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'TV/ChannelHtmlContent.php?id='.$r->TVOId;
			break;
		case 2:
			// URL...
			$JSON['pages'][$r->TVOSide]=$r->TVOUrl;
			break;
		case 3:
			// Rot standard rules...
			$JSON['pages'][$r->TVOSide]=getMyScheme().'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'TV/Rotation.php?Rule='.$r->TVORuleId.'&Tour='.$r->TVOTourCode;
			break;
		case 4:
			// Rot light rules...
			$JSON['pages'][$r->TVOSide]=getMyScheme().'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'TV/LightRot.php?Rule='.$r->TVORuleId.'&Tour='.$r->TVOTourCode;
			break;
		case 5:
			// CSS3 rules...
			$JSON['pages'][$r->TVOSide]=getMyScheme().'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'TV/Rot/?Rule='.$r->TVORuleId.'&Tour='.$r->TVOTourCode;
			break;
		case 6:
			// File...
			$JSON['pages'][$r->TVOSide]=$Page=getMyScheme().'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'tv.php?file='.$r->TVOFile;
			break;
		default:
			$JSON['pages'][$r->TVOSide]=getMyScheme().'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'TV/ChannelNoContent.php';
	}
}

JsonOut($JSON);
