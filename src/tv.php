<?php
require_once(dirname(__FILE__) . '/config.php');
require_once(dirname(__FILE__) . '/Common/Lib/CommonLib.php');

if(isset($_REQUEST['file']) and $Path=getParameter('TVOUT-Path', false, '')) {
	$File=$_REQUEST['file'];
	if($File[0]==DIRECTORY_SEPARATOR or $File[0]=='.') {
		die();
	}

	if(is_file($Path.$File)) {
		header('Content-Type: '.mime_content_type($Path.$File));
		readfile($Path.$File);
	}

	die();
}

$Channel=1;
$Page=getMyScheme().'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'TV/ChannelNoContent.php';

if(!empty($_GET)) {
	foreach($_GET as $k => $v) {
		if(strtolower($k)=='id')
			$Channel=intval($v);
	}
}
$Frames='';
$q=safe_r_sql("SELECT TVOId , TVOSide, TVOHeight, TVOName, TVOUrl, TVOMessage, TVORuleId, TVOTourCode, TVORuleType, TVOFile
	FROM TVOut
	where TVORuleType>0 and not (TVOHeight='' or left(TVOHeight,1)='0')
	order by TVOId=$Channel desc, TVOId, TVOSide");

$First=true;
$RealChannel=0;
$Pages=array();
while($r=safe_fetch($q)) {
	if($First==true) {
		$RealChannel=$r->TVOId;
	}
	$First=false;
	if($RealChannel!=$r->TVOId) {
		// steps out of the loop
		break;
	}
	switch($r->TVORuleType) {
		case 1:
			// HTML text...
			$Page=getMyScheme().'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'TV/ChannelHtmlContent.php?id='.$r->TVOId;
			break;
		case 2:
			// URL...
			$Page=$r->TVOUrl;
			break;
		case 3:
			// Rot standard rules...
			$Page=getMyScheme().'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'TV/Rotation.php?Rule='.$r->TVORuleId.'&Tour='.$r->TVOTourCode;
			break;
		case 4:
			// Rot light rules...
			$Page=getMyScheme().'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'TV/LightRot.php?Rule='.$r->TVORuleId.'&Tour='.$r->TVOTourCode;
			break;
		case 5:
			// CSS3 rules...
			$Page=getMyScheme().'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'TV/Rot/?Rule='.$r->TVORuleId.'&Tour='.$r->TVOTourCode;
			break;
		case 6:
			// File...
			$Page=getMyScheme().'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'tv.php?file='.$r->TVOFile;
			break;
		default:
			$Page=getMyScheme().'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'TV/ChannelNoContent.php';
	}
	$Pages[$r->TVOSide]=$Page;
	$Frames.='<iframe class="TvoChannel" id="channel-'.$r->TVOSide.'" src="'.$Page.'" style="height:'.$r->TVOHeight.'"></iframe>';
}


$JS_SCRIPT=array(
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'TV/Channel.js"></script>',
	phpVars2js(array('Pages' => $Pages, 'Channel' => $RealChannel)),
		'<style>
		body {position:absolute; margin:0; padding:0; top:0; bottom:0; width:100%; height:100%;}
		.TvoChannel {display:block;border:none;padding:0;width:100%;}</style>'
);

include('Common/Templates/head-caspar.php');

echo $Frames;

include('Common/Templates/tail-min.php');
?>
