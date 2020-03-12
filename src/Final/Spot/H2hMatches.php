<?php
//define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
if (!CheckTourSession() or !isset($_REQUEST['Id']) or !isset($_REQUEST['OppId'])) printCrackerror('popup');

	include('Common/Templates/head-min.php');

$rawData = file_get_contents("http://wa_appwrapper/?content=ATHMAT&ID=".$_REQUEST["Id"]."&OppId=".$_REQUEST["OppId"]);
if(($H2HStatus=json_decode($rawData))!=null) {
	debug_svela($H2HStatus);	
}


include('Common/Templates/tail-min.php');