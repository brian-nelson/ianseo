<?php
	define('debug',false);	// settare a true per l'output di debug
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Globals.inc.php');
	require_once('Common/Fun_DB.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Fun_Tournament.local.inc.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Event=isset($_REQUEST['Event']) ? $_REQUEST['Event'] : null;

	list($StrData,$ToCode)=ExportASC($Event);

	if ($ToCode=='')
		exit;

	if (!isset($_REQUEST['ToFitarco']))
	{
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Content-Disposition: attachment; filename=' . $ToCode . '.asc');
		header('Content-type: text/tab-separated-values; charset=' . PageEncode);
//		echo "<pre>";
		print $StrData;
//		echo "</pre>";
	}
	else
	{

		$fp = fopen($_REQUEST['ToFitarco'],'w');
		fputs($fp,$StrData);
		fclose($fp);
	}
?>