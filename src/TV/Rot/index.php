<?php

require_once(dirname(__FILE__).'/config.php');
checkACL(AclOutput,AclReadOnly,true, $TourId);

$NOSTYLE=true;

if(empty($JS_SCRIPT)) {
	$JS_SCRIPT=array();
}

$JS_SCRIPT[]=phpVars2js(array('Rule' => $Rule, 'TourCode' => $IsCode, 'DirRoot'=>$CFG->ROOT_DIR));
$JS_SCRIPT[]='<script type="text/javascript" src="'.$CFG->ROOT_DIR.'TV/Rot/rot.js"></script>';
$JS_SCRIPT[]='<link href="'.$CFG->ROOT_DIR.'TV/Rot/rot.css" media="screen" rel="stylesheet" type="text/css">';

// gets the main CSS ruleset
$JS_SCRIPT[]=getCss($TourId, $Rule);

$ONLOAD=' onload="GetContent('.(empty($Hidden) ? '' : "'hide'").')"';

// uses the same headings as Caspar
require_once('Common/Templates/head-caspar.php');
//echo '<div id="body"></div>';
require_once('Common/Templates/tail-min.php');
