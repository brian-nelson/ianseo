<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');

	if(isset($_REQUEST['Event']) && preg_match("/^[A-Z0-9]{1,4}_[0,1]_[1,2,3]$/i", $_REQUEST['Event']))
	{
		list($tmpEvent,$tmpTeam,$tmpEventType) = explode('_', $_REQUEST['Event']);
		$MyQuery = "UPDATE Events "
			. "SET EvRunning=IF(EvRunning!=" . $tmpEventType . "," . $tmpEventType . ",0) "
			. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode=" . StrSafe_DB($tmpEvent) . " AND EvTeamEvent=" . StrSafe_DB($tmpTeam);
		safe_w_sql($MyQuery);
		if($tmpTeam)
			MakeTeamsAbs(null,null,null);
	}

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/Fun_JS.inc.js"></script>',
		);

	$PAGE_TITLE=get_text('RunningEvents','Tournament');

	include('Common/Templates/head.php');
?>
<table class="Tabella" style="width: 100%; align: center;">
<tr><th class="Title" colspan="8"><?php print get_text('RunningEvents','Tournament'); ?></th></tr>
<?php
$MyQuery ="(select IndEvent as Event, '0_1' as What, MIN(QuHits) as MinHits, MAX(QuHits) as MaxHits 
	FROM Entries
	INNER JOIN Qualifications ON EnId=QuId
	INNER JOIN Individuals ON EnId=IndId
	WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND IndRank!=0
	GROUP BY Event, What)
	UNION ALL
	(select TeEvent as Event, '1_1' as What, MIN(TeHits) as MinHits, MAX(TeHits) as MaxHits 
	FROM Teams
	WHERE TeTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TeFinEvent=1 AND TeRank!=0
	GROUP BY Event, What)
	UNION ALL
	(select ElEventCode as Event, CONCAT('0_',ElElimPhase+2) as What, MIN(ElHits) as MinHits, MAX(ElHits) as MaxHits 
	FROM Eliminations
	WHERE ElTournament=" . StrSafe_DB($_SESSION['TourId']) . "  AND ElRank!=0
	GROUP BY Event, What)
	ORDER BY Event, What";
$ResultRs = safe_r_sql($MyQuery);
$ArrowNoArray=array();
while($arrRow=safe_fetch($ResultRs))
	$ArrowNoArray[$arrRow->Event . "_" . $arrRow->What] = array($arrRow->MinHits,$arrRow->MaxHits);
//debug_svela($ArrowNoArray,true);

$MyQuery = "SELECT DISTINCT EvCode, EvTeamEvent, EvEventName, EvRunning, EvElim1, EvElim2, EvFinalPrintHead as PrintHeader "
	. "FROM Events "
	. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
	. "ORDER BY EvTeamEvent, EvProgr";
	//echo $MyQuery;exit();
$ResultRs = safe_r_sql($MyQuery);
if(safe_num_rows($ResultRs))
{
	echo '<tr>';
	echo '<th width="35%">' . get_text('EvName') . '</th>';
	echo '<th width="20%">' . get_text('PrintText','Tournament') . '</th>';
	echo '<th width="15%" colspan="2">' . get_text('QualRound') . '</th>';
	echo '<th width="15%" colspan="2">' . get_text('Eliminations_1') . '</th>';
	echo '<th width="15%" colspan="2">' . get_text('Eliminations_2') . '</th>';
	echo '</tr>';
	while($MyRow = safe_fetch($ResultRs))
	{
		echo '<tr>';
		echo '<td width="35%"><b>' . $MyRow->EvCode . '</b> - ' . $MyRow->EvEventName .  ' (' . ($MyRow->EvTeamEvent==0 ? get_text('Individual') : get_text('Team'))  . ')</td>';
		echo '<td width="20%">' . $MyRow->PrintHeader . '</td>';
		echo '<td width="5%" class="Center' . ($MyRow->EvRunning == 1 ? ' yellow' : '') . '">';
		if(!empty($ArrowNoArray[$MyRow->EvCode . "_" . $MyRow->EvTeamEvent . "_1"]))
			echo $ArrowNoArray[$MyRow->EvCode . "_" . $MyRow->EvTeamEvent . "_1"][0] . ($ArrowNoArray[$MyRow->EvCode . "_" . $MyRow->EvTeamEvent . "_1"][0]!=$ArrowNoArray[$MyRow->EvCode . "_" . $MyRow->EvTeamEvent . "_1"][1] ? " - ". $ArrowNoArray[$MyRow->EvCode . "_" . $MyRow->EvTeamEvent . "_1"][1]:"");
		else 
			echo '&nbsp;';
		echo '</td>';
		echo '<td width="10%" class="Center' . ($MyRow->EvRunning == 1 ? ' yellow' : '') . '">';
		echo '<a href="' . $_SERVER['PHP_SELF']. '?Event=' . $MyRow->EvCode . '_' . $MyRow->EvTeamEvent . '_1">';
		if($MyRow->EvRunning == 1)
			echo get_text('RunningEv','Tournament');
		else
			echo get_text('StandardEv','Tournament');
 		echo '</a>';
		echo '</td>';
		echo '<td width="5%" class="Center' . ($MyRow->EvRunning == 2 ? ' yellow' : '') . '">';
		if(!empty($ArrowNoArray[$MyRow->EvCode . "_" . $MyRow->EvTeamEvent . "_2"]))
			echo $ArrowNoArray[$MyRow->EvCode . "_" . $MyRow->EvTeamEvent . "_2"][0] . ($ArrowNoArray[$MyRow->EvCode . "_" . $MyRow->EvTeamEvent . "_2"][0]!=$ArrowNoArray[$MyRow->EvCode . "_" . $MyRow->EvTeamEvent . "_2"][1] ? " - ". $ArrowNoArray[$MyRow->EvCode . "_" . $MyRow->EvTeamEvent . "_2"][1]:"");
		else
			echo '&nbsp;';
		echo '</td>';
		echo '<td width="10%" class="Center' . ($MyRow->EvRunning == 2 ? ' yellow' : '') . '">';
		if($MyRow->EvElim1) 
		{
			echo '<a href="' . $_SERVER['PHP_SELF']. '?Event=' . $MyRow->EvCode . '_' . $MyRow->EvTeamEvent . '_2">';
			if($MyRow->EvRunning == 2)
				echo get_text('RunningEv','Tournament');
			else
				echo get_text('StandardEv','Tournament');
			echo '</a>';
		}
		else
			echo '&nbsp;';
		echo '</td>';
		echo '<td width="5%" class="Center' . ($MyRow->EvRunning == 3 ? ' yellow' : '') . '">';
		if(!empty($ArrowNoArray[$MyRow->EvCode . "_" . $MyRow->EvTeamEvent . "_3"]))
			echo $ArrowNoArray[$MyRow->EvCode . "_" . $MyRow->EvTeamEvent . "_3"][0] . ($ArrowNoArray[$MyRow->EvCode . "_" . $MyRow->EvTeamEvent . "_3"][0]!=$ArrowNoArray[$MyRow->EvCode . "_" . $MyRow->EvTeamEvent . "_3"][1] ? " - ". $ArrowNoArray[$MyRow->EvCode . "_" . $MyRow->EvTeamEvent . "_3"][1]:"");
		else
			echo '&nbsp;';
		echo '</td>';
		echo '<td width="10%" class="Center' . ($MyRow->EvRunning == 3 ? ' yellow' : '') . '">';
		if($MyRow->EvElim2)
		{
			echo '<a href="' . $_SERVER['PHP_SELF']. '?Event=' . $MyRow->EvCode . '_' . $MyRow->EvTeamEvent . '_3">';
			if($MyRow->EvRunning == 3)
				echo get_text('RunningEv','Tournament');
			else
				echo get_text('StandardEv','Tournament');
			echo '</a>';
		}
		else
			echo '&nbsp;';
		echo '</td>';
		
		echo '</tr>';
	}
	
	echo '<tr><td colspan="8" class="Center"><input type="button" value="' . get_text('CmdUpdate'). '" onClick="document.location.href=\'' . $_SERVER['PHP_SELF']. '\'"></a></td></tr>';
}
?>

	</table>
<?php
	include('Common/Templates/tail.php');
?>