<?php
/*
													- Went2Home.php -
	Ritira una persona
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession() || !isset($_REQUEST['Id']))
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;
	$Atleta = 0;

	$Select
		= "SELECT EnStatus, IFNULL(LueStatus,0) as NewStatus "
		. "FROM Entries LEFT JOIN LookUpEntries ON EnCode=LueCode AND EnClass=LueClass "
		. "WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnId=" . StrSafe_DB($_REQUEST['Id']) . " ";

	$Rs = safe_r_sql($Select);
	$Update="";
	$Retired=1;
	$NewStatus=0;

	if (!IsBlocked(BIT_BLOCK_QUAL))
	{
		if(safe_num_rows($Rs)==1)
		{
			$Row=safe_fetch($Rs);
			if($Row->EnStatus==6)
			{
				$Update
					= "UPDATE Entries AS e "
					. "LEFT JOIN LookUpEntries AS l ON e.EnCode=l.LueCode AND e.EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
					. "SET "
					. "e.EnStatus=IFNULL(l.LueStatus,0) "
					. "WHERE e.EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnId=" . StrSafe_DB($_REQUEST['Id']) . " ";
				$NewStatus=($Row->NewStatus<=1 ? 1 : 0);
				$Retired=0;
			}
			else
			{
				$Update
					= "UPDATE Entries INNER JOIN Qualifications ON EnId=QuId SET "
					. "EnStatus='6',";
				for ($i=1;$i<=8;++$i)
					$Update
						.="QuD" . $i . "Score='0',"
						. "QuD" . $i . "Gold='0',"
						. "QuD" . $i . "Xnine='0',";
				$Update
					.="QuScore='0',"
					. "QuGold='0',"
					. "QuXnine='0' "
					. "WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND QuId=" . StrSafe_DB($_REQUEST['Id']) . " ";
			}
			if (debug)
				print $Update . '<br>';

			$Rs=safe_w_sql($Update);
			if (!$Rs)
				$Errore=1;
		}
		else
			$Errore=1;
	}
	else
		$Errore=1;

// produco l'xml di ritorno
	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<newstatus>' . $NewStatus . '</newstatus>' . "\n";
	print '<retired>' . $Retired . '</retired>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<ath>' . $_REQUEST['Id'] . '</ath>' . "\n";
	print '</response>' . "\n";
?>