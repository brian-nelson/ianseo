<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
    checkACL(AclCompetition, AclReadOnly);

	$CFG->TRACE_QUERRIES=false;

	if(!empty($_SESSION['TourId'])) $TourId=$_SESSION['TourId'];

	if (isset($_REQUEST['TourCode']))
		$TourId=getIdFromCode($_REQUEST['TourCode']);

	if (empty($TourId))
	{
		print get_text('CrackError');
		exit;
	}

	include('Common/Fun_Export.php');

	$Gara=export_tournament($TourId, !empty($_REQUEST['Complete']));

	// We'll be outputting a gzipped TExt File in UTF-8 pretending it's binary
	header('Content-type: application/octet-stream');

	// It will be called ToCode.ianseo
	header("Content-Disposition: attachment; filename=\"{$Gara['Tournament']['ToCode']}.ianseo\"");

ini_set('memory_limit',sprintf('%sM',512));


// The PDF source is in original.pdf
echo gzcompress(serialize($Gara),9);

exit();
?>