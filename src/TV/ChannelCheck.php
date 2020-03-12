<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

$JSON=array('error' => 1, 'reload'=>false, 'page' => getMyScheme().'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'TV/ChannelNoContent.php');

$Channel=1;

if(!empty($_GET)) {
	foreach($_GET as $k => $v) {
		if(strtolower($k)=='id')
			$Channel=intval($v);
	}
}

$q=safe_r_sql("SELECT TVOId , TVOName, TVOUrl, TVOMessage, TVORuleId, TVOTourCode, TVORuleType
		FROM TVOut
		where TVORuleType>0 and TVOId=$Channel");

require_once('Common/Lib/Fun_Modules.php');


$JSON['error']=0;
if($r=safe_fetch($q)) {
	$JSON['reload']=(getModuleParameter('TVOUT', 'Reload', 0, getIdFromCode($r->TVOTourCode)) ? true : false);
	setModuleParameter('TVOUT', 'Reload', 0, getIdFromCode($r->TVOTourCode));

	switch($r->TVORuleType) {
		case 1:
			// HTML text...
			$JSON['page']=getMyScheme().'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'TV/ChannelHtmlContent.php?id='.$r->TVOId;
			break;
		case 2:
			// URL...
			$JSON['page']=$r->TVOUrl;
			break;
		case 3:
			// Rot standard rules...
			$JSON['page']=getMyScheme().'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'TV/Rotation.php?Rule='.$r->TVORuleId.'&Tour='.$r->TVOTourCode;
			break;
		case 4:
			// Rot light rules...
			$JSON['page']=getMyScheme().'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'TV/LightRot.php?Rule='.$r->TVORuleId.'&Tour='.$r->TVOTourCode;
			break;
		case 5:
			// CSS3 rules...
			$JSON['page']=getMyScheme().'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'TV/Rot/?Rule='.$r->TVORuleId.'&Tour='.$r->TVOTourCode;
			break;
		default:
			$JSON['page']=getMyScheme().'://'.$_SERVER['SERVER_NAME'].$CFG->ROOT_DIR.'TV/ChannelNoContent.php';
	}
}

JsonOut($JSON);