<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/Fun_DateTime.inc.php');

	if (!CheckTourSession() ||
		!isset($_REQUEST['rowid']) ||
		!isset($_REQUEST['date']) ||
		!isset($_REQUEST['time']) ||
		!isset($_REQUEST['len']) ||
		!isset($_REQUEST['from']) ||
		!isset($_REQUEST['to']))
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;

	$date=ConvertDate($_REQUEST['date']);
	$time=Convert24Time($_REQUEST['time']);
	$badDate=(false and !($date>=date('Y-m-d',$_SESSION['ToWhenFromUTS']) && $date<=date('Y-m-d',$_SESSION['ToWhenToUTS'])));
	$len=intval($_REQUEST['len']);
	$from=intval($_REQUEST['from']);
	$to=intval($_REQUEST['to']);

	$tmpEvent='';
	$tmpDbEvent='';

	if($date && $time && !$badDate && $from && $to && $to>=$from)
	{
		if($_REQUEST['rowid']==-1)
		{
			$query = "INSERT INTO FinTraining (FtTournament, FtScheduledDate, FtScheduledTime, FtScheduledLen, FtTargetFrom, FtTargetTo) "
				. "VALUES ("
				. StrSafe_DB($_SESSION['TourId']) . ","
				. StrSafe_DB($date) . ","
				. StrSafe_DB($time) . ","
				. StrSafe_DB($len) . ","
				. StrSafe_DB($from) . ","
				. StrSafe_DB($to)
				. ")";
		}
		else
		{
			$query = "UPDATE FinTraining SET "
				. "FtScheduledDate = " . StrSafe_DB($date) . ","
				. "FtScheduledTime = " . StrSafe_DB($time) . ","
				. "FtScheduledLen = " . StrSafe_DB($len) . ","
				. "FtTargetFrom = " . StrSafe_DB($from) . ","
				. "FtTargetTo = " . StrSafe_DB($to)
				. "WHERE FtTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND  FtScheduledDate=" . StrSafe_DB($_REQUEST['olddate']) . " AND FtScheduledTime= " . StrSafe_DB($_REQUEST['oldtime']) . " AND FtTargetFrom = " . StrSafe_DB($_REQUEST['oldfrom']) . " ";
		}
		$rs=safe_w_sql($query);
		if (!$rs)
			$Errore=1;

		//salvo gli eventi se presenti
		if(isset($_REQUEST["event"]) && is_array($_REQUEST["event"]))
		{
			$query = "DELETE FROM FinTrainingEvent "
				. "WHERE FteTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND  FteScheduledDate=" . StrSafe_DB($_REQUEST['olddate']) . " AND FteScheduledTime= " . StrSafe_DB($_REQUEST['oldtime']) . " AND FteTargetFrom = " . StrSafe_DB($_REQUEST['oldfrom']) . " ";
			$rs=safe_w_sql($query);
			$query = "INSERT INTO FinTrainingEvent (FteTournament, FteScheduledDate, FteScheduledTime, FteTargetFrom, FteEvent, FteTeamEvent) VALUES ";
			foreach($_REQUEST["event"] as $singleEvent)
			{
				list($event,$teamevent) = explode("|", $singleEvent);
				$tmpEvent .= $event . ", ";
				$tmpDbEvent .= $singleEvent . ", ";
				$query .= "(" . StrSafe_DB($_SESSION['TourId']) . ", " . StrSafe_DB($date) . ", " . StrSafe_DB($time) . ", " . StrSafe_DB($from) . ", " . StrSafe_DB($event) . ", " . StrSafe_DB($teamevent) . "),";
			}
			$rs=safe_w_sql(substr($query,0,-1));
		}
	}
	else
	{
		$Errore=1;
	}

	header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<rowid>' . $_REQUEST['rowid'] . '</rowid>';
	print '<dbdate>' . $date . '</dbdate>';
	print '<dbtime>' . $time . '</dbtime>';
	print '<date>' . $_REQUEST['date'] . '</date>';
	print '<time>' . $_REQUEST['time'] . '</time>';
	print '<len>' . $_REQUEST['len'] . '</len>';
	print '<from>' . $_REQUEST['from'] . '</from>';
	print '<to>' . $_REQUEST['to'] . '</to>';
	print '<event>' . substr($tmpEvent,0,-2) . '#</event>';
	print '<dbevent>' . substr($tmpDbEvent,0,-2) . '#</dbevent>';
	print '</response>' . "\n";

?>