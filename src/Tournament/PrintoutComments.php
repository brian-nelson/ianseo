<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Sessions.inc.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	/*$Select
		= "SELECT ToId,ToNumSession,TtNumDist, "
		. "ToTar4Session1, ToTar4Session2, ToTar4Session3, ToTar4Session4, ToTar4Session5, ToTar4Session6, ToTar4Session7, ToTar4Session8, ToTar4Session9 "
		. "FROM Tournament INNER JOIN Tournament*Type ON ToType=TtId "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";*/
	$Select
		= "SELECT ToId,ToNumSession,ToNumDist AS TtNumDist "
		. "FROM Tournament  "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$RsTour=safe_r_sql($Select);
	$RowTour=null;
	if (safe_num_rows($RsTour)==1)
		$RowTour=safe_fetch($RsTour);

	//Tutte le fasi di qualifica
	$sessions=GetSessions('Q',true);

	$ComboSes = '<select name="x_Session" id="x_Session">' . "\n";
	$ComboSes.= '<option value="-1">---</option>' . "\n";

	foreach ($sessions as $s)
	{
		$ComboSes.= '<option value="' . $s->SesOrder . '"' . (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$s->SesOrder ? ' selected' : '') . '>' . $s->Descr . '</option>' . "\n";
	}
	//Tutte le finali
	$Select
		= "SELECT DISTINCT CONCAT(FSScheduledDate,' ',FSScheduledTime) AS MyDate,FSTeamEvent "
		. "FROM Tournament INNER JOIN FinSchedule ON ToId=FSTournament "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "ORDER BY FSTeamEvent ASC,CONCAT(FSScheduledDate,FSScheduledTime) ASC ";
	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)>0)
	{
		while ($MyRow=safe_fetch($Rs))
		{
			$val=$MyRow->FSTeamEvent . $MyRow->MyDate;
			$text=($MyRow->FSTeamEvent==0 ? get_text('FinInd','HTT') . ': ' . $MyRow->MyDate : get_text('FinTeam','HTT') . ': ' . $MyRow->MyDate);
			$ComboSes.='<option value="' . $val . '" ' . (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$val ? ' selected' : '') . '>' . $text . '</option>' . "\n";
		}
	}
	$ComboSes.= '</select>' . "\n";



	$Command=(isset($_REQUEST['Command']) ? $_REQUEST['Command'] : null);
	$ResultRs = null;

	if (!is_null($Command))
	{
		if ($Command=='SAVE')
		{
			if (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']!=-1)
			{
				//Non è una finale
				if (is_numeric($_REQUEST['x_Session']))
				{
					//Update Individuals
					$MyQuery = "UPDATE Qualifications "
						. "INNER JOIN Entries ON QuId=EnId "
						. "INNER JOIN EventClass ON EcClass=EnClass AND EcDivision=EnDivision AND EcTournament=EnTournament "
						. "INNER JOIN Events ON EvCode=EcCode AND EvTeamEvent=IF(EcTeamEvent!=0, 1,0) AND EvTournament=EcTournament "
						. "SET EvQualPrintHead=" . StrSafe_DB((substr($_REQUEST['txtComment'],0,2)!='||' ? stripslashes($_REQUEST['txtComment']) : (substr($_REQUEST['txtComment'],2,1)=='!' ? 'OFFICIAL ' : 'Unofficial ') . str_replace("##",intval(substr($_REQUEST['txtComment'],(substr($_REQUEST['txtComment'],2,1)=='!' ? 3:2))),"After ## Arrows"))) . " "
						. "WHERE QuSession=" . StrSafe_DB($_REQUEST['x_Session']) . " AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0";
					$Rs=safe_w_sql($MyQuery);

					//Update Teams
					// normal teams, 3 components
					$MyQuery = "UPDATE Qualifications "
						. "INNER JOIN Entries ON QuId=EnId "
						. "INNER JOIN EventClass ON EcClass=EnClass AND EcDivision=EnDivision AND EcTournament=EnTournament "
						. "INNER JOIN Events ON EvCode=EcCode AND EvTeamEvent=IF(EcTeamEvent!=0, 1,0) AND EvTournament=EcTournament "
						. "SET EvQualPrintHead=" . StrSafe_DB((substr($_REQUEST['txtComment'],0,2)!='||' ? stripslashes($_REQUEST['txtComment']) : (substr($_REQUEST['txtComment'],2,1)=='!' ? 'OFFICIAL ' : 'Unofficial ') . str_replace("##",intval(substr($_REQUEST['txtComment'],(substr($_REQUEST['txtComment'],2,1)=='!' ? 3:2)))*3,"After ## Arrows"))) . " "
						. "WHERE EvMixedTeam=0 AND QuSession=" . StrSafe_DB($_REQUEST['x_Session']) . " AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent!=0";
					$Rs=safe_w_sql($MyQuery);
					// Mixed Teams
					$MyQuery = "UPDATE Qualifications "
						. "INNER JOIN Entries ON QuId=EnId "
						. "INNER JOIN EventClass ON EcClass=EnClass AND EcDivision=EnDivision AND EcTournament=EnTournament "
						. "INNER JOIN Events ON EvCode=EcCode AND EvTeamEvent=IF(EcTeamEvent!=0, 1,0) AND EvTournament=EcTournament "
						. "SET EvQualPrintHead=" . StrSafe_DB((substr($_REQUEST['txtComment'],0,2)!='||' ? stripslashes($_REQUEST['txtComment']) : (substr($_REQUEST['txtComment'],2,1)=='!' ? 'OFFICIAL ' : 'Unofficial ') . str_replace("##",intval(substr($_REQUEST['txtComment'],(substr($_REQUEST['txtComment'],2,1)=='!' ? 3:2)))*2,"After ## Arrows"))) . " "
						. "WHERE EvMixedTeam=1 AND QuSession=" . StrSafe_DB($_REQUEST['x_Session']) . " AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent!=0";
					$Rs=safe_w_sql($MyQuery);

					$MyQuery = "SELECT DISTINCT EvCode, EvTeamEvent, EvEventName, EvQualPrintHead as PrintHeader "
						. "FROM Qualifications "
						. "INNER JOIN Entries ON QuId=EnId "
						. "INNER JOIN EventClass ON EcClass=EnClass AND EcDivision=EnDivision AND EcTournament=EnTournament "
						. "INNER JOIN Events ON EvCode=EcCode AND EvTeamEvent=IF(EcTeamEvent!=0, 1,0) AND EvTournament=EcTournament "
						. "WHERE QuSession=" . StrSafe_DB($_REQUEST['x_Session']) . " AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
						. "ORDER BY EvTeamEvent, EvProgr";
						//echo $MyQuery;exit();
					$ResultRs = safe_r_sql($MyQuery);
				}
				else	// finali
				{
					$team=substr($_REQUEST['x_Session'],0,1);
					$when=substr($_REQUEST['x_Session'],1);

					$MyQuery = "UPDATE FinSchedule "
						. "INNER JOIN Events ON EvCode=FSEvent AND EvTeamEvent=FSTeamEvent AND EvTournament=FSTournament "
						. "SET EvFinalPrintHead=" . StrSafe_DB(stripslashes($_REQUEST['txtComment'])) . " "
						. "WHERE CONCAT(FSScheduledDate,' ',FSScheduledTime)=" . StrSafe_DB($when) . " AND "
						. "FSTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND "
						. "FSTeamEvent=" . StrSafe_DB($team);
					$Rs=safe_w_sql($MyQuery);

					$MyQuery = "SELECT DISTINCT EvCode, EvTeamEvent, EvEventName, EvFinalPrintHead as PrintHeader "
						. "FROM FinSchedule "
						. "INNER JOIN Events ON EvCode=FSEvent AND EvTeamEvent=FSTeamEvent AND EvTournament=FSTournament "
						. "WHERE CONCAT(FSScheduledDate,' ',FSScheduledTime)=" . StrSafe_DB($when) . " AND "
						. "FSTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND "
						. "FSTeamEvent=" . StrSafe_DB($team) . " "
						. "ORDER BY EvTeamEvent, EvProgr";
						//echo $MyQuery;exit();
					$ResultRs = safe_r_sql($MyQuery);

				}
			}
		}
	}

	$JS_SCRIPT = array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/Fun_JS.inc.js"></script>'
		);
	$PAGE_TITLE=get_text('PrintTextTitle','Tournament');

	include('Common/Templates/head.php');
?>
<form name="FrmParam" method="POST" action="">
	<input type="hidden" name="Command" value="SAVE">
	<table class="Tabella" width="50%">
	<tr><th class="Title" colspan="3"><?php print get_text('PrintTextTitle','Tournament'); ?></th></tr>
	<tr class="Divider"><td colspan="3"></td></tr>
	<tr>
	<th width="50%"><?php print get_text('Session');?></th>
	<th width="50%"><?php print get_text('PrintText','Tournament');?></th>
	</tr>
	<tr>
	<td class="Center"><?php print $ComboSes; ?></td>
	<td class="Left"><input type="text" name="txtComment" id="txtComment" size="80" maxlength="64" value="<?php echo (isset($_REQUEST['txtComment']) ? stripslashes($_REQUEST['txtComment']) : '') ?>">
	<br/><?php echo get_text('PrintCommentTip', 'Tournament'); ?></td>
	</tr>
	<tr>
	<td colspan="2" class="Center"><input type="submit" value="<?php print get_text('CmdSave');?>"></td>
	</tr>
<?php
if(!is_null($ResultRs))
{
	while($MyRow = safe_fetch($ResultRs))
	{
		echo '<tr>';
		echo '<td><b>' . $MyRow->EvCode . '</b> - ' . $MyRow->EvEventName .  ' (' . ($MyRow->EvTeamEvent==0 ? get_text('Individual') : get_text('Team'))  . ')</td>';
		echo '<td>' . $MyRow->PrintHeader . '</td>';
		echo '</tr>';
	}
}
?>
	</table>
</form>
<?php
	include('Common/Templates/tail.php');
?>