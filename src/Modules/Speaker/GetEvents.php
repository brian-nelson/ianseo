<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');

	$schedule=(isset($_REQUEST['schedule']) && preg_match('/^[0-1]{1}[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/',$_REQUEST['schedule']) ? $_REQUEST['schedule'] : null);

	if (empty($_SESSION['TourId']) && (!CheckTourSession() || is_null($schedule)))
		exit;

	$xml='';
	$error=0;

	$team=substr($schedule,0,1);

	$xml.='<team>' . $team . '</team>' . "\n";

	$tmp=explode(' ',substr($schedule,1));

	$date=$tmp[0];
	$time=$tmp[1];

	$query
		= "SELECT DISTINCT EvCode AS code, EvEventName as name "
		. "FROM "
			. "FinSchedule "
		. "INNER JOIN "
			. "Events ON FSEvent=EvCode AND FSTeamEvent=EvTeamEvent AND FSTournament=EvTournament "
		. "WHERE "
			. "FSTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FSTeamEvent=" . $team . " "
			. "AND FSScheduledDate=" . StrSafe_DB($date) . " AND FSScheduledTime=" . StrSafe_DB($time) . " "
		. "ORDER BY "
			. "EvProgr, CONCAT(FSScheduledDate, ' ',FSScheduledTime) ASC ";

	$rs=safe_r_sql($query);

	if ($rs && safe_num_rows($rs)>0)
	{
		while ($myRow=safe_fetch($rs))
		{
			$xml.='<event><code>' . $myRow->code . '</code><name>' . $myRow->name . '</name></event>' . "\n";
		}
	}

	header('Content-Type: text/xml');

	print '<response>' . "\n";

		print '<error>' . $error . '</error>' . "\n";

		print $xml;

	print '</response>' . "\n";
?>