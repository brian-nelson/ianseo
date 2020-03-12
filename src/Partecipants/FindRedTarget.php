<?php
/*
													- FindRedTarget.php -
	Cerca i targetno doppi e ritorna l'elenco.
	La funzione ajax si preoccuperà di colorare i doppioni
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadOnly, false);

	$xml = '';
	$Errore=0;

	$MaxSession = 0;
	/*$Select
		= "SELECT ToNumSession,	ToTar4Session1,	ToTar4Session2,	ToTar4Session3,	ToTar4Session4,	ToTar4Session5,	ToTar4Session6,	ToTar4Session7,	ToTar4Session8,	ToTar4Session9,"
		. "ToAth4Target1,ToAth4Target2,	ToAth4Target3,	ToAth4Target4,	ToAth4Target5,	ToAth4Target6,	ToAth4Target7,	ToAth4Target8,	ToAth4Target9 "
		. "FROM Tournament "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$Rs=safe_r_sql($Select);*/

	$Select
		= "SELECT ToNumSession "
		. "FROM Tournament "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)==1)
	{
		$MyRow=safe_fetch($Rs);
		$MaxSession=$MyRow->ToNumSession;
	}

	if (isset($_REQUEST['Ses']) && ((is_numeric($_REQUEST['Ses']) && $_REQUEST['Ses']>0 && $_REQUEST['Ses']<=$MaxSession) || (!is_numeric($_REQUEST['Ses']) && $_REQUEST['Ses']=='*')))
	{
		/*$Select
			= "SELECT sq.EnId,sq.Quanti,sq.EnTournament "
			. "FROM Qualifications AS q "
			. "INNER JOIN ("
			. "SELECT EnId,EnTournament,QuTargetNo,COUNT(QuTargetNo) AS Quanti FROM Entries INNER JOIN Qualifications ON EnId=QuId AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "WHERE QuTargetNo<>''  GROUP BY QuTargetNo) AS sq ON q.QuTargetNo=sq.QuTargetNo AND QuId=sq.EnId "
			. ($_REQUEST['Ses']!='*' ? "AND ((QuSession=0 AND QuTargetNo='') OR QuSession=" . StrSafe_DB($_REQUEST['Ses']) . ") " : '') . " "
			. "ORDER BY QuSession ASC,sq.QuTargetNo ASC ";
			*/

		$Select
			= "SELECT QuId, sq.Quanti, (AtTargetNo IS NOT NULL) AS ValidTarget "
			. "FROM Qualifications AS q "
			. "INNER JOIN ("
				. "SELECT QuTargetNo, COUNT( QuTargetNo ) AS Quanti "
				. "FROM Entries INNER JOIN Qualifications ON EnId = QuId AND EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " "
				. "WHERE QuTargetNo <>'' GROUP BY QuTargetNo"
			. ") AS sq ON q.QuTargetNo = sq.QuTargetNo "
			. "INNER JOIN Entries ON QuId=EnId AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "LEFT JOIN AvailableTarget ON EnTournament=AtTournament AND q.QuTargetNo=AtTargetNo "
			. "ORDER BY QuSession ASC , sq.QuTargetNo ASC ";

		$Rs=safe_r_sql($Select);

		if (debug)
			print $Select . '<br>';

		if (safe_num_rows($Rs)>0)
		{
			while ($MyRow=safe_fetch($Rs))
			{
				$xml
					.= '<target>'
					 . '<id>' . $MyRow->QuId . '</id>'
					 . '<num>' . (!$MyRow->ValidTarget ? '0' : $MyRow->Quanti) . '</num>'
					 . '</target>';
			}
		}
	}

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>';
	print '<error>' . $Errore . '</error>';
	print $xml;
	print '</response>';
?>