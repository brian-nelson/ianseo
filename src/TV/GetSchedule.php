<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');

	$schedule=(isset($_REQUEST['schedule']) && preg_match('/^[0-1]{1}[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/',$_REQUEST['schedule']) ? $_REQUEST['schedule'] : null);

	if (!CheckTourSession() || is_null($schedule))
		exit;
    checkACL(AclOutput,AclReadWrite,false);

	$xml='';
	$error=0;

	$team=substr($schedule,0,1);

	$xml.='<team>' . $team . '</team>';

	$tmp=explode(' ',substr($schedule,1));

	$date=$tmp[0];
	$time=$tmp[1];

	$query
		= "SELECT DISTINCT EvCode AS code, EvEventName as name, GrPhase "
		. "FROM "
			. "FinSchedule "
		. "INNER JOIN "
			. "Events ON FSEvent=EvCode AND FSTeamEvent=EvTeamEvent AND FSTournament=EvTournament "
		. "INNER JOIN "
			. "Grids ON FSMatchNo=GrMatchNo "
		. "WHERE "
			. "FSTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FSTeamEvent=" . $team . " "
			. "AND FSScheduledDate=" . StrSafe_DB($date) . " AND FSScheduledTime=" . StrSafe_DB($time) . " "
		. "ORDER BY "
			. "CONCAT(FSScheduledDate, ' ',FSScheduledTime) ASC ";

	$rs=safe_r_sql($query);

	if ($rs && safe_num_rows($rs)>0)
	{
		while ($myRow=safe_fetch($rs))
		{
			$xml.='<event><![CDATA[id_' . $myRow->code .'_' . $myRow->GrPhase . ']]></event>' ;
		}
	}

	header('Content-Type: text/xml');

	print '<response>';

		print '<error>' . $error . '</error>';

		print $xml;

	print '</response>';
?>