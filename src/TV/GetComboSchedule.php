<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession())
		exit;
    checkACL(AclOutput,AclReadOnly);

	$teamEvent = (!empty($_REQUEST["teamEvent"]) && $_REQUEST["teamEvent"]==1 ? 1 : 0);

	$xml='';
	$error=0;

	$Select
		= "(SELECT DISTINCT CONCAT(FSScheduledDate,' ',FSScheduledTime) AS MyDate,FSTeamEvent "
		. "FROM Tournament INNER JOIN FinSchedule ON ToId=FSTournament "
		. (isset($_REQUEST["useHHT"]) && $_REQUEST["useHHT"] ? "INNER JOIN HhtEvents on HeTournament=ToId and HeFinSchedule=concat(FSScheduledDate, ' ', FSScheduledTime) and HeTeamEvent=FsTeamEvent " : "")
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " and FSScheduledDate>0 and FSTeamEvent=0 "
		. (isset($_REQUEST["onlyToday"]) && $_REQUEST["onlyToday"] ? "AND FSScheduledDate=UTC_DATE()" : "")
		.") UNION ALL "
		. "(SELECT DISTINCT CONCAT(FSScheduledDate,' ',FSScheduledTime) AS MyDate,FSTeamEvent "
		. "FROM Tournament INNER JOIN FinSchedule ON ToId=FSTournament "
		. (isset($_REQUEST["useHHT"]) && $_REQUEST["useHHT"] ? "inner join HhtEvents on HeTournament=ToId and HeFinSchedule=concat(FSScheduledDate, ' ', FSScheduledTime) and HeTeamEvent=FsTeamEvent  " : "")
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " and FSScheduledDate>0 and FSTeamEvent!=0 "
		. (isset($_REQUEST["onlyToday"]) && $_REQUEST["onlyToday"] ? "AND FSScheduledDate=UTC_DATE()" : "")
		. ") ORDER BY MyDate ASC ";

	$Rs=safe_r_sql($Select);
	if ($Rs && safe_num_rows($Rs)>0)
	{
		while ($myRow=safe_fetch($Rs))
		{
			if($teamEvent != $myRow->FSTeamEvent)
				continue;
			//
			$xml.='
				<schedule>
					<val>' . ($myRow->FSTeamEvent . $myRow->MyDate) . '</val>
					<display>' . (($myRow->FSTeamEvent ? get_text('Team'):get_text('Individual')) . ' ' . $myRow->MyDate) . '</display>
				</schedule>
			';
		}
	}

	header('Content-Type: text/xml');

	print '<response>';

		print '<error>' . $error . '</error>';

		print $xml;

	print '</response>';
?>