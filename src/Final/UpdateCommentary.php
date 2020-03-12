<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);

	$event = isset($_REQUEST['d_Event']) ? $_REQUEST['d_Event'] : null;
	$TeamEvent = isset($_REQUEST['d_Team']) ? $_REQUEST['d_Team'] : null;
	$match = isset($_REQUEST['d_Match']) ? $_REQUEST['d_Match'] : null;
	$Rev1 = isset($_REQUEST['Review1']) ? $_REQUEST['Review1'] : null;
	$Rev2 = isset($_REQUEST['Review2']) ? $_REQUEST['Review2'] : null;

    checkACL(($TeamEvent ? AclTeams : AclIndividuals), AclReadWrite, false);
	
	if($match%2!=0)
		$match--;
	
	$Errore = 0;
	$msg = '';
	
	$isBlocked=($TeamEvent==0 ? IsBlocked(BIT_BLOCK_IND) : IsBlocked(BIT_BLOCK_TEAM));
	if (is_null($event) || is_null($TeamEvent) || is_null($match) || $isBlocked)
	{
		$Errore=1;
	}
	else
	{
		if(strlen($Rev1)==0 && strlen($Rev2)==0)
		{
			$sql = "DELETE FROM Reviews "
				. "WHERE RevEvent=" . StrSafe_DB($event) . " AND "
				. "RevMatchNo =" . $match . " AND "
				. "RevTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND "
				. "RevTeamEvent=" . StrSafe_DB($TeamEvent);
			$Rs=safe_w_sql($sql);
			
		}
		else
		{
			$sql = "INSERT INTO Reviews (RevEvent, RevMatchNo, RevTournament, RevTeamEvent, RevLanguage1, RevLanguage2, RevDateTime) "
				. "VALUES(" . StrSafe_DB($event) . ", " . StrSafe_DB($match) . ", " . StrSafe_DB($_SESSION['TourId']) . ", " . StrSafe_DB($TeamEvent) . ", "
				. StrSafe_DB(urldecode($Rev1)) . ", " . StrSafe_DB(urldecode($Rev2)) . ", " . StrSafe_DB(date('Y-m-d H:i:s')) . ") "
				. "ON DUPLICATE KEY UPDATE RevLanguage1=" . StrSafe_DB(urldecode($Rev1)) . ", RevLanguage2=" . StrSafe_DB(urldecode($Rev2)) . ", RevDateTime=" . StrSafe_DB(date('Y-m-d H:i:s'));
			$Rs=safe_w_sql($sql);
			if(!safe_w_affected_rows())
				$Errore=1;
		}
	}
	
		if ($Errore==0)
	{
		
		$msg=get_text('CmdOk');
	}
	else
	{
		$msg=get_text('Error');
	}

	header('Content-Type: text/xml');

	print '<response>' . "\n";
		print '<error>' . $Errore . '</error>' . "\n";
		print '<msg>' . $msg . '</msg>' . "\n";
	print '</response>' . "\n";
?>