<?php

require_once(dirname(__FILE__) . '/config.php');
require_once('Common/GlobalsLanguage.inc.php');
require_once('Common/Lib/CommonLib.php');
require_once('Common/Lib/Fun_Modules.php');

$json_array=array();

if(empty($_REQUEST['lang'])) {
	$ServerUrl=getModuleParameter('ISK', 'ServerUrl', '', $CompId);
	foreach($Lingue as $lang => $text) {
		if(!file_exists($CFG->LANGUAGE_PATH . $lang . '/ISK.php')) continue;
		$json_array[]=array(
			'id' => $lang,
			'name'=>$text,
			'md5'=> md5_file($CFG->LANGUAGE_PATH . $lang . '/ISK.php'),
			'urlFlag' => $ServerUrl.$CFG->ROOT_DIR.'Common/Languages/'.$lang.'/'.$lang.'.png',
			'urlFile' => $ServerUrl.$CFG->ROOT_DIR.'Api/ISK/'.basename(__FILE__).'?lang='.$lang,
		);
	}

} elseif(preg_match('/^[a-z_0-9-]+$/sim', $_REQUEST['lang'])) {
	$json_array=getArrayLang($CFG->LANGUAGE_PATH . $_REQUEST['lang'] . '/ISK.php');
	$json_array['MD5']=md5_file($CFG->LANGUAGE_PATH . $_REQUEST['lang'] . '/ISK.php');
	$json_array['LanguageName']=$Lingue[ $_REQUEST['lang'] ];
}

// Return the json structure with the callback function that is needed by the app
SendResult($json_array);

function getArrayLang($file) {
	$lang=array();
	include($file);
	return $lang;
}
