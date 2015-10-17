<?php
/*
													- UpdatePhase.php -
	Aggiorna la fase di inizio di un evento, distrugge la griglia di quell'evento e azzera i flag di Shotoff fatto
*/

	define('debug',false);

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_Sessions.inc.php');
	if (!CheckTourSession() || !isset($_REQUEST['EvCode']) || !isset($_REQUEST['NewPhase']))
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;

	if (!IsBlocked(BIT_BLOCK_TOURDATA))
	{
	// aggiorno la fase
		$Update
			= "UPDATE Events SET "
			. "EvFinalFirstPhase=" . StrSafe_DB($_REQUEST['NewPhase']) . " "
			. "WHERE EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
		$Rs=safe_w_sql($Update);

		if ($Rs)
		{
		// Distruggo la griglia
			$Delete
				= "DELETE FROM Finals "
				. "WHERE FinEvent=" . StrSafe_DB($_REQUEST['EvCode']) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
			$Rs=safe_w_sql($Delete);

			if ($Rs)
			{
				// Deletes unused warmups
				$delSchedule = "DELETE FROM FinWarmup USING
					Events
					INNER JOIN FinSchedule ON EvCode = FsEvent AND EvTeamEvent = FsTeamEvent AND EvTournament = FsTournament
					INNER JOIN Grids ON GrMatchNo = FsMatchNo
					INNER JOIN FinWarmup on FsEvent=FwEvent and FsTeamEvent=FwTeamEvent and FsTournament=FwTournament and FsScheduledDate=FwDay and FsScheduledTime=FwMatchTime
					WHERE EvFinalFirstPhase < GrPhase
					AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='0' AND EvCode=" . StrSafe_DB($_REQUEST['EvCode']);
				$RsDel=safe_w_sql($delSchedule);

				//Cancello lo schedule non in uso
				$delSchedule = "DELETE FROM FinSchedule USING
					Events
					INNER JOIN FinSchedule ON EvCode = FsEvent AND EvTeamEvent = FsTeamEvent AND EvTournament = FsTournament
					INNER JOIN Grids ON GrMatchNo = FsMatchNo
					WHERE EvFinalFirstPhase < GrPhase
					AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='0' AND EvCode=" . StrSafe_DB($_REQUEST['EvCode']);
				$RsDel=safe_w_sql($delSchedule);

				// Creo la griglia
				$Insert
					= "INSERT INTO Finals (FinEvent,FinMatchNo,FinTournament,FinDateTime) "
					. "SELECT EvCode,GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . "," . StrSafe_DB(date('Y-m-d H:i:s')) . " "
					. "FROM Events INNER JOIN Grids ON GrPhase<=if(EvFinalFirstPhase=24, 32, IF(EvFinalFirstPhase=48,64, EvFinalFirstPhase)) AND EvTeamEvent='0' "
					. "AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
					. "WHERE EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " ";
				if($_REQUEST['NewPhase']!=0)
					$RsIns=safe_w_sql($Insert);

			// Azzero il flag di spareggio
				ResetShootoff($_REQUEST['EvCode'],0,3);
			}
			else
				$Errore=1;
		}
		else
			$Errore=1;
	}
	else
		$Errore=1;

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<event>' . $_REQUEST['EvCode'] . '</event>' . "\n";
	print '</response>' . "\n";
?>