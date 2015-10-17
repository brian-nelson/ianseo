<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	
	if (!CheckTourSession() || !isset($_REQUEST['Id']))
	{
		print get_text('CrackError');
		exit;
	}
	
	$Errore=0;
	
	if (!(IsBlocked(BIT_BLOCK_QUAL) && IsBlocked(BIT_BLOCK_IND) && IsBlocked(BIT_BLOCK_TEAM)))
	{
		$delete
			= "DELETE FROM HhtEvents "
			. "WHERE HeHhtId=" . StrSafe_DB($_REQUEST['Id']) . " AND HeTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";	
		$rs=safe_w_sql($delete);
		
		$delete
			= "DELETE FROM HhtSetup "
			. "WHERE HsId=" . StrSafe_DB($_REQUEST['Id']) . " AND HsTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";	
		$rs=safe_w_sql($delete);
		
		if (!$rs)
			$Errore=1;
	}
	else
		$Errore=1;
	
	header('Content-Type: text/xml');
	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<id>' . $_REQUEST['Id'] . '</id>' . "\n";		
	print '</response>' . "\n";

?>