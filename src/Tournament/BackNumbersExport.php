<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');

	$CFG->TRACE_QUERRIES=false;

	$TourId=$_SESSION['TourId'];
	$BackNo=0;
	if(!empty($_GET['BackNo'])) $Backno=intval($_GET['BackNo']);

	if (!$TourId)
	{
		print get_text('CrackError');
		exit;
	}

	include('Common/Fun_Export.php');

	$Gara=array();
	$q=safe_r_sql("select * from BackNumber where BnTournament=$TourId order by BnFinal=$BackNo desc limit 1");

	$Gara[]=safe_fetch($q);
	unset($Gara[0]->BnFinal);

// We'll be outputting a gzipped TExt File in UTF-8 pretending it's binary
header('Content-type: application/octet-stream');

// It will be called ToCode.ianseo
header("Content-Disposition: attachment; filename=\"{$_SESSION['TourCode']}-BackNumbers.ianseo\"");

ini_set('memory_limit',sprintf('%sM',512));

// The PDF source is in original.pdf
echo gzcompress(serialize($Gara),9);

exit();
?>