<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (empty($_SESSION['TourId']) && !CheckTourSession())
		exit;

	$xml='';
	$error=0;

// ind
	$Select
		= "(SELECT DISTINCT CONCAT(FSScheduledDate,' ',FSScheduledTime) AS MyDate,FSTeamEvent "
		. "FROM Tournament INNER JOIN FinSchedule ON ToId=FSTournament "
		. (isset($_REQUEST["useHHT"]) && $_REQUEST["useHHT"] ? "INNER JOIN HhtEvents on HeTournament=ToId and HeFinSchedule=concat(FSScheduledDate, ' ', FSScheduledTime) and HeTeamEvent=FsTeamEvent " : "")
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " and FSScheduledDate>0 and FSTeamEvent=0 "
		. (isset($_REQUEST["onlyToday"]) && $_REQUEST["onlyToday"] ? "AND FSScheduledDate=date_format(sysdate(),'%Y-%m-%d')" : "")
		.") UNION ALL "
		. "(SELECT DISTINCT CONCAT(FSScheduledDate,' ',FSScheduledTime) AS MyDate,FSTeamEvent "
		. "FROM Tournament INNER JOIN FinSchedule ON ToId=FSTournament "
		. (isset($_REQUEST["useHHT"]) && $_REQUEST["useHHT"] ? "INNER join HhtEvents on HeTournament=ToId and HeFinSchedule=concat(FSScheduledDate, ' ', FSScheduledTime) and HeTeamEvent=FsTeamEvent " : "")
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " and FSScheduledDate>0 and FSTeamEvent!=0 "
		. (isset($_REQUEST["onlyToday"]) && $_REQUEST["onlyToday"] ? "AND FSScheduledDate=date_format(sysdate(),'%Y-%m-%d')" : "")
		. ") ORDER BY MyDate ASC ";

	$Rs=safe_r_sql($Select);
	if ($Rs && safe_num_rows($Rs)>0)
	{
		while ($myRow=safe_fetch($Rs))
		{
			//
			$xml.='
				<schedule>
					<val>' . ($myRow->FSTeamEvent . $myRow->MyDate) . '</val>
					<display>' . (($myRow->FSTeamEvent ? get_text('Team'):get_text('Individual')) . ' ' . $myRow->MyDate) . '</display>
				</schedule>
			' . "\n";
		}
	}

	header('Content-Type: text/xml');

	print '<response>' . "\n";

		print '<error>' . $error . '</error>' . "\n";

		print $xml;

	print '</response>' . "\n";
?>