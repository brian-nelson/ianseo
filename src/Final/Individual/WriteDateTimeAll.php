<?php
/*
													- WriteDateTimeAll.php -
	Scrive lo scheduling a tutta la fase
*/

	define('debug',false);

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/Fun_DateTime.inc.php');

	if (!CheckTourSession() ||
		!isset($_REQUEST['d_Event']) ||
		!isset($_REQUEST['d_Phase']) ||
		!isset($_REQUEST['d_FSScheduledDateAll']) ||
		!isset($_REQUEST['d_FSScheduledTimeAll']))
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;
	$xml = '';

	if(empty($_REQUEST['d_FSScheduledDateAll'])) {
		$Phase=intval($_REQUEST['d_Phase']);
		$Phase2=max(2, $Phase*2);
		$SQL
			= "select"
			. " FSScheduledDate,"
			. " date_format(FSScheduledDate, '".get_text('DateFmtDB')."') DateFormatted,"
			. " UNIX_TIMESTAMP(concat(FSScheduledDate, ' ', FSScheduledTime)) UnixTime "
			. "FROM FinSchedule "
			. " INNER JOIN Grids on FsMatchNo=GrMatchNo "
			. "WHERE"
			. " FSEvent=" . StrSafe_DB($_REQUEST['d_Event']) . " "
			. " AND FSTeamEvent = 0"
			. " AND FSTournament = " . StrSafe_DB($_SESSION['TourId']).  " "
			. " AND GrPhase = $Phase2 "
			. "ORDER BY"
			. " FSScheduledDate desc,"
			. " FSScheduledTime desc "
			. "LIMIT 1";
		$q=safe_r_sql($SQL);
		if($r=safe_fetch($q)) {
			$date=$r->FSScheduledDate;
			$_REQUEST['d_FSScheduledDateAll']=$r->DateFormatted;
		}
		if(substr($_REQUEST['d_FSScheduledTimeAll'],0,1)=='+') {
			$time=date('H:i:s', strtotime($_REQUEST['d_FSScheduledTimeAll'].' minutes', $r->UnixTime));
			$_REQUEST['d_FSScheduledTimeAll']=substr($time,0,5);
		}
	}

	$time=(empty($time)?Convert24Time($_REQUEST['d_FSScheduledTimeAll']):$time);
	$date=(empty($date)?ConvertDate($_REQUEST['d_FSScheduledDateAll']):$date);
	$matchLen=(isset($_REQUEST['d_FSScheduledLenAll']) && strlen(trim($_REQUEST['d_FSScheduledLenAll']))>0 && intval($_REQUEST['d_FSScheduledLenAll'])>=0 ? $_REQUEST['d_FSScheduledLenAll'] : 0);

	if (!(($time || strlen(trim($_REQUEST['d_FSScheduledTimeAll']))==0) &&
		($date || strlen(trim($_REQUEST['d_FSScheduledDateAll']))==0) &&
		$date>=$_SESSION['TourRealWhenFrom'] && $date <= $_SESSION['TourRealWhenTo']))
	{
		$Errore=1;
	}

	if (IsBlocked(BIT_BLOCK_TOURDATA))
		$Errore==1;

	if ($Errore==0)
	{
		$PhaseFilter = '';
		if ($_REQUEST['d_Phase']!=1)
			$PhaseFilter = "GrPhase=" . StrSafe_DB($_REQUEST['d_Phase']) . " ";
		else
			$PhaseFilter = "(GrPhase='0' OR GrPhase='1') ";

		$Insert
			= "INSERT INTO FinSchedule (FSEvent,FSTeamEvent,FSMatchNo,FSTournament,FSScheduledDate,FSScheduledTime,FSScheduledLen) "
			. "(SELECT " . StrSafe_DB($_REQUEST['d_Event']) . ", '0', GrMatchNo,"
			. StrSafe_DB($_SESSION['TourId']) . "," . StrSafe_DB($date) . ","
			. ($time ? StrSafe_DB($time) : 'null') . ", "
			. $matchLen . " "
			. "FROM Grids "
			. "WHERE " . $PhaseFilter . ") "
			. "ON DUPLICATE KEY UPDATE "
			. "FSTarget=FSTarget,FSGroup=FSGroup, "
			. "FSScheduledDate=" . StrSafe_DB($date) . ","
			. "FSScheduledTime=" . ($time ? StrSafe_DB($time) : 'null') . ","
			. "FSScheduledLen=" . $matchLen;
		$Rs=safe_w_sql($Insert);
		if (debug) print $Insert . '<br>';
		if (!$Rs)
			$Errore=1;

		// MatchNo della fase
		$Select
			= "SELECT GrMatchNo FROM Grids WHERE " . $PhaseFilter . " AND (GrMatchNo MOD 2)=0 ";
		$Rs=safe_r_sql($Select);

		if (safe_num_rows($Rs)>0)
		{
			while ($MyRow=safe_fetch($Rs))
			{
				$xml.='<matchno>' . $MyRow->GrMatchNo . '</matchno>' . "\n";
			}
		}
	}

	if (!debug)
		header('Content-Type: text/xml');
	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<event><![CDATA[' . $_REQUEST['d_Event'] . ']]></event>' . "\n";
	print '<phase><![CDATA[' . $_REQUEST['d_Phase'] . ']]></phase>' . "\n";
	print '<date><![CDATA[' . $_REQUEST['d_FSScheduledDateAll'] . ']]></date>' . "\n";
	print '<time><![CDATA[' . $_REQUEST['d_FSScheduledTimeAll'] . ']]></time>' . "\n";
	print '<len><![CDATA[' . $matchLen . ']]></len>' . "\n";
	print $xml;
	print '</response>' . "\n";
?>