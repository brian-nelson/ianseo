<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	
	if (!CheckTourSession() || !isset($_REQUEST['row']) || !isset($_REQUEST['cl']))	{
		print get_text('CrackError');
		exit;
	}
    checkACL(AclCompetition, AclReadWrite, false);
	
	$Errore=0;
	
	if (!IsBlocked(BIT_BLOCK_ACCREDITATION))
	{
		$delete
			= "DELETE FROM AccColors "
			. "WHERE AcDivClass=" . StrSafe_DB($_REQUEST['cl']) . " AND AcTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";	
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