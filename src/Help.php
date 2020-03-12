<?php

require_once(dirname(__FILE__) . '/config.php');

$JS_SCRIPT[]='<style>
code {display:block; margin:0.5em; border:1px solid grey; background-color: lightgrey;padding:0.5em;}
body {font-size:small;}
</style>';

if(empty($_GET['help'])) PrintCrackError('popup');

require_once('Common/Lib/wiki.php');

$helpfile=$_GET['help'];

include('Common/Templates/head-popup.php');
if(file_exists($CFG->DOCUMENT_PATH . 'Common/Help/' . $helpfile)) {
	include($CFG->DOCUMENT_PATH . 'Common/Help/' . $helpfile);
} else {
	echo get_text('CrackError');
}

include('Common/Templates/tail-popup.php');

