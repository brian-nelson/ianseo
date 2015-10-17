<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	
	if (!CheckTourSession() || !isset($_REQUEST['row']) || !isset($_REQUEST['dbdate']) || !isset($_REQUEST['dbtime']) || !isset($_REQUEST['dbfrom']))
	{
		print get_text('CrackError');
		exit;
	}
	
	$Errore=0;
	
	if (!IsBlocked(BIT_BLOCK_TOURDATA))
	{
		$delete
			= "DELETE FROM FinTraining "
			. "WHERE FtTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FtScheduledDate=" . StrSafe_DB($_REQUEST['dbdate']) . " AND FtScheduledTime=" . StrSafe_DB($_REQUEST['dbtime']) . " AND FtTargetFrom=" . StrSafe_DB($_REQUEST['dbfrom']);	
		$rs=safe_w_sql($delete);
		if (!$rs)
			$Errore=1;
		
		$delete
			= "DELETE FROM FinTrainingEvent "
			. "WHERE FteTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FteScheduledDate=" . StrSafe_DB($_REQUEST['dbdate']) . " AND FteScheduledTime=" . StrSafe_DB($_REQUEST['dbtime']) . " AND FteTargetFrom=" . StrSafe_DB($_REQUEST['dbfrom']);	
		$rs=safe_w_sql($delete);
		if (!$rs)
			$Errore=1;
	}
	else
		$Errore=1;
	
	
	header('Content-Type: text/xml');
		
	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<row>' . $_REQUEST['row'] . '</row>' . "\n";		
	print '</response>' . "\n";
?>