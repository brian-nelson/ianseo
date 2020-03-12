<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once 'Final/Fun_Final.local.inc.php';

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Code='';

// Tiro fuori il codice gara
	$Select
		= "SELECT ToCode FROM Tournament WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$Rs=safe_r_sql($Select);
	
	$StrData=ExportLSTFinTeam(isset($_REQUEST['Event']) ? $_REQUEST['Event'] : null);

	if (safe_num_rows($Rs)==1)
	{
		$MyRow=safe_fetch($Rs);
		$Code=$MyRow->ToCode;
	}

	if ($Code)
	{
		if (!isset($_REQUEST['ToFitarco']))
		{
			header('Cache-Control: no-store, no-cache, must-revalidate');
			header('Content-Disposition: attachment; filename=' . $Code . '_rank_team.lst');
			header('Content-type: text/tab-separated-values');

			print $StrData;
		}
		else
		{
			$fp = fopen($_REQUEST['ToFitarco'],'w');
			fclose($fp);
		}
	}
?>