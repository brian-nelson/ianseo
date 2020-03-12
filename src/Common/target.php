<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once("Obj_Target.php");

$target = new Obj_Target();

if(!CheckTourSession()) {
	$target->drawSvgError();
}

// we can call this page in 2 ways...
// OLD WAY with an array of arrows (PNG)
if(isset($_REQUEST['Match'])) {
	$Event=isset($_REQUEST['Event']) ? $_REQUEST['Event'] : null;
	$Match=isset($_REQUEST['Match']) ? $_REQUEST['Match'] : null;
	$Team=isset($_REQUEST['Team']) ? $_REQUEST['Team'] : 0;
	$Size=isset($_REQUEST['Size']) ? $_REQUEST['Size'] : 100;

	$validData=GetMaxScores($Event, $Match, $Team);
	$target = new Obj_Target($validData["Arrows"],$validData["Size"]);
	if(!empty($_REQUEST['Arrows'])) {
		$target->setArrowPos($_REQUEST['Arrows']);
	}

	$target->draw($Size);
	die();
}

if(!isset($_REQUEST['Event']) or !isset($_REQUEST['Matchno']) or !isset($_REQUEST['Team'])) {
	$target->drawSvgError();
}

// NEW WAY calling match, event, etc... (SVG)
// End==-1 means SO request
$Event = preg_replace('/[^0-9a-z_-]/sim', '', $_REQUEST['Event']);
$Team = empty($_REQUEST['Team']) ? 0 : 1;
$Match = intval($_REQUEST['Matchno']);
$End = isset($_REQUEST['End']) ? intval($_REQUEST['End']) : 0;
$Both = isset($_REQUEST['Both']) ? 1 : 0;

$target->OutputSVG($Event, $Team, $Match, $End, $Both);
