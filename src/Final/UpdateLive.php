<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);

	$event = isset($_REQUEST['d_Event']) ? $_REQUEST['d_Event'] : null;
	$TeamEvent = isset($_REQUEST['d_Team']) ? $_REQUEST['d_Team'] : null;
	$match = isset($_REQUEST['d_Match']) ? $_REQUEST['d_Match'] : null;

	if($match%2!=0)
		$match--;

	$Errore = 0;
	$msg = '';
	$xml = '';

	$isBlocked=($TeamEvent==0 ? IsBlocked(BIT_BLOCK_IND) : IsBlocked(BIT_BLOCK_TEAM));
	if (is_null($event) || is_null($TeamEvent) || is_null($match) || $isBlocked)
	{
		$Errore=1;
		$msg = 'Blocked!';
	}
	else
	{
		$prefix = ($TeamEvent ? 'Tf' : 'Fin');
		$sql = "UPDATE " . ($TeamEvent ? 'Team' : '') . "Finals SET "
			. $prefix . "Live = (NOT " . $prefix . "Live), "
			. $prefix . "DateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " "
			. "WHERE "
			. $prefix . "Tournament=" . StrSafe_DB($_SESSION['TourId']) . " AND "
			. $prefix . "MatchNo IN(" . StrSafe_DB($match) . "," . StrSafe_DB($match+1) . ") AND "
			. $prefix . "Event=" . StrSafe_DB($event);
		safe_w_sql($sql);

		$sql = "UPDATE Finals SET FinLive='0', "
			. "FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " "
			. "WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId'])
			. ($TeamEvent ? '' : " AND FinLive!='0' AND NOT(FinMatchNo IN(" . StrSafe_DB($match) . "," . StrSafe_DB($match+1) . ") AND FinEvent=" . StrSafe_DB($event) . ")");
		safe_w_sql($sql);
		$sql = "UPDATE TeamFinals SET TfLive='0', "
			. "TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " "
			. "WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId'])
			. ($TeamEvent ? " AND TfLive!='0' AND NOT (TfMatchNo IN(" . StrSafe_DB($match) . "," . StrSafe_DB($match+1) . ") AND TfEvent=" . StrSafe_DB($event) . ")" : '');
		safe_w_sql($sql);

		$sql = "SELECT " . $prefix . "Live as Live "
			. "FROM " . ($TeamEvent ? 'Team' : '') . "Finals "
			. "WHERE "
			. $prefix . "Tournament=" . StrSafe_DB($_SESSION['TourId']) . " AND "
			. $prefix . "MatchNo =" . StrSafe_DB($match) . " AND "
			. $prefix . "Event=" . StrSafe_DB($event);

		$Rs = safe_r_sql($sql);

		if (safe_num_rows($Rs)==1)
		{
			$myRow=safe_fetch($Rs);
			$xml = '<live>' . $myRow->Live . '</live>' ."\n";
			$xml .= '<livemsg>' . get_text(($myRow->Live ? 'LiveOff':'LiveOn')) . '</livemsg>' ."\n";
		}
		else {
			$Errore = 1;
			$msg=get_text('Error');
		}
	}

	if ($Errore==0)
	{

		$msg=get_text('CmdOk');
	}
	else
	{
		//$msg=get_text('Error');
	}

	header('Content-Type: text/xml');

	print '<response>' . "\n";
		print '<error>' . $Errore . '</error>' . "\n";
		print '<msg>' . $msg . '</msg>' . "\n";
		print $xml;
	print '</response>' . "\n";

?>