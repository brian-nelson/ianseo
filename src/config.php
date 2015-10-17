<?php

if(!defined('INSTALL')) define('INSTALL', false);
$newversion='2014-08-10 17:00:00';

// definition of the CONFIG object
$CFG = new StdClass();

// debug utility, set to false in production
$CFG->TRACE_QUERRIES = false;

// set the include path to the "root" directory of the IANSEO Installation
$CFG->INCLUDE_PATH = dirname(__FILE__);
ini_set("include_path", get_include_path() . PATH_SEPARATOR . $CFG->INCLUDE_PATH );

$CFG->DOCUMENT_PATH = $CFG->INCLUDE_PATH . DIRECTORY_SEPARATOR;

// Input the distro
require_once('Common/distro.inc.php');

// ALWAYS SET THE TIMEZONE TO UTC!!!!
date_default_timezone_set('UTC');

$CFG->LANGUAGE_PATH = $CFG->DOCUMENT_PATH . 'Common/Languages/';

require_once($CFG->DOCUMENT_PATH . 'Common/Fun_DB.inc.php');
require_once($CFG->DOCUMENT_PATH . 'Common/Globals.inc.php');
@include_once($CFG->DOCUMENT_PATH . 'Common/config.inc.php');
@include_once('Common/DebugOverrides.php');

// Check if the DB is up to date
if(in_array($CFG->DOCUMENT_PATH . 'Common'.DIRECTORY_SEPARATOR.'config.inc.php', get_included_files())) {
	$version = GetParameter('DBUpdate');
	if($version < $newversion) {
		require_once('Common/UpdateDb.inc.php');
	}
}

define_session_flags(); // restores some of the Session flags

if(!INSTALL and (!file_exists($CFG->DOCUMENT_PATH . 'Common/config.inc.php')
		|| empty($CFG->R_HOST)
		|| empty($CFG->W_HOST)
		|| empty($CFG->R_USER)
		|| empty($CFG->W_USER)
		|| empty($CFG->R_PASS)
		|| empty($CFG->W_PASS)
		|| empty($CFG->DB_NAME)
	)) {

	$CFG->ROOT_DIR = substr($_SERVER['SCRIPT_NAME'], 0, strlen(dirname(__FILE__)) + strlen($_SERVER['SCRIPT_NAME']) - strlen(realpath($_SERVER['SCRIPT_FILENAME']))) . '/';

	cd_redirect($CFG->ROOT_DIR . 'Install/');
}

/*

Controllo del debug va in sessione

*/

if(!isset($_SESSION['debug'])) {
	if(file_exists($CFG->DOCUMENT_PATH . 'Common/config.inc.php')) {
		$_SESSION['debug'] = getparameter('DEBUG'); // modificare a true per partire con il debug !!
	} else {
		$_SESSION['debug'] = false;
	}
	if(!isset($_SESSION['debug-mode'])) $_SESSION['debug-mode']='';
}
if(isset($_GET['ianseo-debug-session'])) {
	$_SESSION['debug'] = (!$_SESSION['debug']);
	cd_redirect();
}
if(isset($_GET['Ianseo-debug-mode'])) {
	$_SESSION['debug-mode'] = '-'.$_GET['Ianseo-debug-mode'];
	if($_SESSION['debug-mode']=='-Main') $_SESSION['debug-mode']='';
	cd_redirect();
}

// trucchetto per permettere o negare il debug nonostante il get di cui sopra
$ERROR_REPORT = ($ERROR_REPORT and $_SESSION['debug']);

if($ERROR_REPORT) {
	error_reporting(E_ALL);
	ini_set('display_errors','On');
} else {
	error_reporting(0);
	ini_set('display_errors','off');
}


$_SESSION['debug'] = $ERROR_REPORT;
$CFG->TRACE_QUERRIES = ($CFG->TRACE_QUERRIES and $_SESSION['debug']);

// Autocheckin
if(!empty($CFG->ROOT_DIR) and dirname($_SERVER['PHP_SELF'])!=$CFG->ROOT_DIR.'Modules/AutoCheckin' and $Code=GetParameter('AutoCHK-Code') and !empty($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], explode(',', GetParameter('AutoCHK-IP')))) {
	CreateTourSession(getIdFromCode($Code));
	CD_redirect($CFG->ROOT_DIR.'Modules/AutoCheckin/AutoCheckin.php');
	die();
}


// RICONTROLLA TUTTE LE VOLTE GLI SPAREGGI!
//set_qual_session_flags();

/*

	deposito temporaneo per Chris nelle modifiche degli header :D

	$JS_SCRIPT=array(
		'',
		'',
		'',
		'',
		);

	include('Common/Templates/head.php');
	include('Common/Templates/tail.php');

*/
?>