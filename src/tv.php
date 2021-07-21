<?php
require_once(dirname(__FILE__) . '/config.php');
require_once(dirname(__FILE__) . '/Common/Lib/CommonLib.php');

$Channel=1;
$Page=getMyScheme().'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'TV/ChannelNoContent.php';

if(!empty($_GET)) {
	foreach($_GET as $k => $v) {
		if(strtolower($k)=='id')
			$Channel=intval($v);
	}
}

$q=safe_r_sql("SELECT TVOId , TVOName, TVOUrl, TVOMessage, TVORuleId, TVOTourCode, TVORuleType
	FROM TVOut
	where TVORuleType>0 and TVOId=$Channel");

// 	<option value="1">'.get_text('Freetext', 'Tournament').'</option>
// 	<option value="2">'.get_text('URL', 'Tournament').'</option>

if($r=safe_fetch($q)) {
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
		default:
			$Page=getMyScheme().'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'TV/ChannelNoContent.php';
	}
}


$NOSTYLE=true;

$JS_SCRIPT=array(
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'TV/Channel.js"></script>',
	phpVars2js(array('Page' => $Page, 'Channel' => $Channel)),
		'<style>
		body {position:absolute; margin:0; padding:0; top:0; bottom:0; width:100%; height:100%;}
		#channel {border:none;padding:none;position:relative;width:100%;height:100%;top:0;bottom:0;left:0;right:0;}</style>'
);

include('Common/Templates/head-caspar.php');

echo '<iframe id="channel" src="'.$Page.'" />';

include('Common/Templates/tail-min.php');
?>
