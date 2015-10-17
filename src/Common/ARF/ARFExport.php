<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/ARF/ARFOutput.class.php');
	
	$ToId=(!isset($_REQUEST['Code']) ? $_SESSION['TourId'] : getIdFromCode($_REQUEST['Code']));
	
	$phase=$_REQUEST['phase'];
	$params=array();
	
	foreach ($_REQUEST['params'] as $v)
		$params[]=$v;
	
	$arf=new ARFOutput($ToId,$phase,$params);

	if (isset($_REQUEST['download']))
		$arf->render2download();
	else
		$arf->render2browser();
	
?>
