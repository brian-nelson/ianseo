<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once("Obj_Target.php");

	CheckTourSession(true);

	$Event=isset($_REQUEST['Event']) ? $_REQUEST['Event'] : null;
	$Match=isset($_REQUEST['Match']) ? $_REQUEST['Match'] : null;
	$Team=isset($_REQUEST['Team']) ? $_REQUEST['Team'] : 0;
	$Size=isset($_REQUEST['Size']) ? $_REQUEST['Size'] : 100;

	$validData=GetMaxScores($Event, $Match, $Team);
	$target = new Obj_Target($validData["Arrows"]);
	if(!empty($_REQUEST['Arrows'])) 
		$target->setArrowPos($_REQUEST['Arrows']);

	$target->draw($Size);
?>