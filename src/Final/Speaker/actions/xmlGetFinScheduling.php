<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');

	if (!CheckTourSession())
		exit;

	$xml='';
	$error=0;

// ind
	$Select
		= "SELECT DISTINCT CONCAT(FSScheduledDate,' ',FSScheduledTime) AS MyDate,FSTeamEvent "
		. "FROM Tournament INNER JOIN FinSchedule ON ToId=FSTournament inner join HhtEvents on HeTournament=ToId and HeFinSchedule=concat(FSScheduledDate, ' ', FSScheduledTime) and HeTeamEvent=FsTeamEvent "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " and FSScheduledDate>0 and FSTeamEvent=0 "
		. "ORDER BY CONCAT(FSScheduledDate,FSScheduledTime) ASC ";
	$Rs=safe_r_sql($Select);

	if ($Rs && safe_num_rows($Rs)>0)
	{
		while ($myRow=safe_fetch($Rs))
		{
			//
			$xml.='
				<schedule>
					<val>' . ($myRow->FSTeamEvent . $myRow->MyDate) . '</val>
					<display>' . (get_text('Individual') . ' ' . $myRow->MyDate) . '</display>
				</schedule>
			' . "\n";
		}
	}

// team
	$Select
		= "SELECT DISTINCT CONCAT(FSScheduledDate,' ',FSScheduledTime) AS MyDate,FSTeamEvent "
		. "FROM Tournament INNER JOIN FinSchedule ON ToId=FSTournament inner join HhtEvents on HeTournament=ToId and HeFinSchedule=concat(FSScheduledDate, ' ', FSScheduledTime) and HeTeamEvent=FsTeamEvent  "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " and FSScheduledDate>0 and FSTeamEvent!=0 "
		. "ORDER BY CONCAT(FSScheduledDate,FSScheduledTime) ASC ";
	$Rs=safe_r_sql($Select);

	if ($Rs && safe_num_rows($Rs)>0)
	{
		while ($myRow=safe_fetch($Rs))
		{
			$xml.='
				<schedule>
					<val>' . ($myRow->FSTeamEvent . $myRow->MyDate) . '</val>
					<display>' . (get_text('Team') . ' ' . $myRow->MyDate) . '</display>
				</schedule>
			' . "\n";
		}
	}

	header('Content-Type: text/xml');

	print '<response>' . "\n";

		print '<error>' . $error . '</error>' . "\n";

		print $xml;

	print '</response>' . "\n";