<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	include_once('Common/Fun_FormatText.inc.php');
	require_once 'Fun_Qualification.local.inc.php';

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

	$StrData=ExportLSTTeam();

	if (!isset($_REQUEST['ToFitarco']))
	{
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Content-Disposition: attachment; filename=' . ($ToCode!='' ? $ToCode .'_team': 'exportteamlst') . '.lst');
		header('Content-type: text/tab-separated-values');

		print $StrData;
	}
	else
	{
		$fp = fopen($_REQUEST['ToFitarco'],'w');
		fputs($fp,$StrData);
		fclose($fp);
	}
?>