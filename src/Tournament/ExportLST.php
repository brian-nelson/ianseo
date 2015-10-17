<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_Various.inc.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}
	
	$ToCode = '';
	
	$Select
		= "SELECT ToCode "
		. "FROM Tournament "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$Rs=safe_r_sql($Select);
	
	if (safe_num_rows($Rs)==1)
	{
		$row=safe_fetch($Rs);
		$ToCode=$row->ToCode;
	}
	
	if ($ToCode=='')
		exit;
	
	$StrData='';
	
	/*$urls = array
	(
		'http://' . $_SERVER['HTTP_HOST'] . $CFG->ROOT_DIR . 'Qualification/LST_Individual.php?TourId=' . $_SESSION['TourId'],
		'http://' . $_SERVER['HTTP_HOST'] . $CFG->ROOT_DIR . 'Qualification/LST_Team.php?TourId=' . $_SESSION['TourId'],
		'http://' . $_SERVER['HTTP_HOST'] . $CFG->ROOT_DIR . 'Final/Individual/LST_Individual.php?TourId=' . $_SESSION['TourId'],
		'http://' . $_SERVER['HTTP_HOST'] . $CFG->ROOT_DIR . 'Final/Team/LST_Team.php?TourId=' . $_SESSION['TourId'],
	);*/
	
	$urls = array
	(
		$CFG->ROOT_DIR . 'Qualification/LST_Individual.php?TourId=' . $_SESSION['TourId'],
		$CFG->ROOT_DIR . 'Qualification/LST_Team.php?TourId=' . $_SESSION['TourId'],
		$CFG->ROOT_DIR . 'Final/Individual/LST_Individual.php?TourId=' . $_SESSION['TourId'],
		$CFG->ROOT_DIR . 'Final/Team/LST_Team.php?TourId=' . $_SESSION['TourId'],
	);

// wrappo tutte le pagine
	foreach ($urls as $v)
	{
		if (($x=URLWrapper($v))!==false)
			$StrData.=$x . "\n\n\n";	
	}
	
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-Disposition: attachment; filename=' . ($ToCode!='' ? $ToCode : 'exportlst') . '.lst');
	header('Content-type: text/tab-separated-values');
	
	print $StrData;
?>