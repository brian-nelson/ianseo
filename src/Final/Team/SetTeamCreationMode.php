<?php
	define('debug',false);

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;

	if (!isset($_REQUEST['EvCode']) || !isset($_REQUEST['EvTeamCreationMode']))
	{
		$Errore=1;
	}
	else
	{
		$Update
			= "UPDATE Events SET "
			. "EvTeamCreationMode=" . StrSafe_DB($_REQUEST['EvTeamCreationMode']) . " "
			. "WHERE EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EvTeamEvent='1' AND "
			. "EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
		$Rs=safe_w_sql($Update);
		if(safe_w_affected_rows())
			MakeTeamsAbs(null,null,null);
	}

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '</response>' . "\n";
?>