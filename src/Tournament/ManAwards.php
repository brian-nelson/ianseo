<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Modules.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$evArray= array(
		"00"=>get_text('IndClEvent', 'Tournament'),
		"10"=>get_text('IndFinEvent', 'Tournament'),
		"01"=>get_text('TeamClEvent', 'Tournament'),
		"11"=>get_text('TeamFinEvent', 'Tournament')
	);
	$editRow = null;

	if (isset($_REQUEST['Command'])) {
		if ($_REQUEST['Command']=='ADD'){
			foreach($_REQUEST["addField"] as $v) {
				if(preg_match('/^[A-Z0-9]{1,4}\|[0-1]{1}\|[0-1]{1}$/',$v)) {
					list($Event,$isFinal,$isTeam) = explode('|',$v);
					$Insert	= "INSERT IGNORE INTO Awards (AwTournament, AwEvent, AwFinEvent, AwTeam, AwUnrewarded, AwPositions) "
						. "VALUES("
						. StrSafe_DB($_SESSION['TourId']) . ","
						. StrSafe_DB($Event) . ","
						. StrSafe_DB($isFinal) . ","
						. StrSafe_DB($isTeam) . ","
						. "0,"
						. StrSafe_DB('1,2,3') . ")";
					$RsIns=safe_w_sql($Insert);

				}
			}
		} elseif ($_REQUEST['Command']=='SWITCH') {
			if (isset($_REQUEST['EvSwitch']) && isset($_REQUEST['FinEv']) && isset($_REQUEST['TeamEv'])) {
				$Switch
					= "UPDATE Awards SET AwGroup = (NOT AwGroup) WHERE AwTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AwEvent=" . StrSafe_DB($_REQUEST['EvSwitch']) . " AND AwFinEvent=". StrSafe_DB($_REQUEST['FinEv']) . " AND AwTeam=". StrSafe_DB($_REQUEST['TeamEv']);
				$RsSwitch = safe_w_sql($Switch);
			}
		} elseif ($_REQUEST['Command']=='OPTION') {
			if(isset($_REQUEST['OptSwitch']) && in_array($_REQUEST["OptSwitch"],array('RepresentCountry','PlayAnthem'))) {
				$tmp = getModuleParameter('Awards',$_REQUEST["OptSwitch"],1);
				setModuleParameter('Awards',$_REQUEST["OptSwitch"],($tmp ? 0:1));
			}
		} elseif ($_REQUEST['Command']=='DELETE') {
			if (isset($_REQUEST['EvDel']) && isset($_REQUEST['FinEv']) && isset($_REQUEST['TeamEv'])) {
				$Delete
					= "DELETE FROM Awards WHERE AwTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AwEvent=" . StrSafe_DB($_REQUEST['EvDel']) . " AND AwFinEvent=". StrSafe_DB($_REQUEST['FinEv']) . " AND AwTeam=". StrSafe_DB($_REQUEST['TeamEv']);
				$RsDel = safe_w_sql($Delete);
			}
		}
		header('Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
	$JS_SCRIPT = array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Tournament/Fun_AJAX_ManAwards.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
		);

	$PAGE_TITLE=get_text('MenuLM_ManAwards');

	include('Common/Templates/head.php');
?>
<table class="Tabella">
<tr><th class="Title" colspan="8"><?php print get_text('MenuLM_ManAwards'); ?></th></tr>
<tr class="Divider"><td colspan="8"></td></tr>
<tr>
<th width="5%"><?php print get_text('Print', 'Tournament'); ?></th>
<th width="5%"><?php print get_text('Order', 'Tournament'); ?></th>
<th width="5%"><?php print get_text('EvCode'); ?></th>
<th width="5%"><?php print get_text('Event'); ?></th>
<th width="10%"><?php print get_text('RankFinals', 'Tournament'); ?></th>
<th width="20%"><?php print get_text('AwardName', 'Tournament'); ?></th>
<th width="50%"><?php print get_text('Awarders', 'Tournament'); ?></th>
<th>&nbsp;</th>
</tr>
<?php
	$Select
		= "SELECT AwEvent, AwFinEvent, AwTeam, AwPositions, AwDescription, AwAwarders, AwGroup, AwOrder "
		. "FROM Awards "
		. "WHERE AwTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "ORDER BY AwGroup DESC, AwOrder, AwFinEvent DESC, AwTeam ASC, AwEvent";

		//print $Select;  exit;

	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)>0)
	{
		while ($MyRow=safe_fetch($Rs))
		{
			print '<tr id="'.$MyRow->AwEvent.'|'.$MyRow->AwFinEvent.'|'.$MyRow->AwTeam.'">';

			print '<td class="Center"  onclick="switchEnabled(\'' . $MyRow->AwEvent . "'," . $MyRow->AwFinEvent . "," . $MyRow->AwTeam . ')">';
			print '<img src="' . $CFG->ROOT_DIR . 'Common/Images/Enabled' . $MyRow->AwGroup . '.png" width="20" alt="' .  get_text($MyRow->AwGroup ? 'Yes' : 'No'). '">';
			print '</td>';

			print '<td class="Center" onclick="insertInput(this,\'AwOrder\')">';
			print $MyRow->AwOrder;
			print '</td>';

			print '<td class="Center">';
			print $MyRow->AwEvent;
			print '</td>';

			print '<td>';
			print $evArray[$MyRow->AwFinEvent. $MyRow->AwTeam];
			print '</td>';

			print '<td onclick="insertInput(this,\'AwPositions\')">';
			print $MyRow->AwPositions;
			print '</td>';

			print '<td onclick="insertInput(this,\'AwDescription\')">';
			print $MyRow->AwDescription;
			print '</td>';

			print '<td onclick="insertInput(this,\'AwAwarders\')">';
			print ManageHTML($MyRow->AwAwarders);
			print '</td>';

			print '<td class="Center">';
			print '<input type="button" value="' . get_text('CmdDelete','Tournament') . '" onClick="javascript:DeleteAwards(\'' . $MyRow->AwEvent . "'," . $MyRow->AwFinEvent . "," . $MyRow->AwTeam . ',\'' . get_text('MsgAreYouSure') . '\');">';
			print '</td>';

			print '</tr>' . "\n";
		}
	}
	echo '<tr class="Divider"><td colspan="8"></td></tr>';
	echo '<tr><th class="Title" colspan="8">' . get_text('Options','Tournament') . '</th></tr>';
	$tmp = getModuleParameter('Awards','PlayAnthem',1);
	echo '<tr><td class="Center" onclick="switchOption(\'PlayAnthem\')"><img src="' . $CFG->ROOT_DIR . 'Common/Images/Enabled' . $tmp. '.png" width="20" alt="' .  get_text($tmp ? 'Yes' : 'No'). '"></td>';
	echo '<td colspan="7">'. get_text('AwardPlayAnthem','Tournament') . '</td></tr>';
	$tmp = getModuleParameter('Awards','RepresentCountry',1);
	echo '<tr><td class="Center" onclick="switchOption(\'RepresentCountry\')"><img src="' . $CFG->ROOT_DIR . 'Common/Images/Enabled' . $tmp. '.png" width="20" alt="' .  get_text($tmp ? 'Yes' : 'No'). '"></td>';
	echo '<td colspan="7">'. get_text('AwardRepresentCountry','Tournament') . '</td></tr>';

	echo '<tr class="Divider"><td colspan="8"></td></tr>';
	echo '<tr><th class="Title" colspan="8">' . get_text('AwardAvailableEvents','Tournament') . '</th></tr>';
	echo '<tr><td colspan="8"><form name="frmAdd" action="" method="get"><table class="Tabella">';
	$needSubmit = false;
	//Individual Events
	$Sql = "SELECT EvCode as Event
		FROM Events
		LEFT JOIN Awards ON EvTournament=AwTournament AND EvCode=AwEvent AND AwFinEvent=1 AND EvTeamEvent=AwTeam
		WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0 AND AwEvent IS NULL";
	$Rs=safe_r_SQL($Sql);
	if(safe_num_rows($Rs)) {
		$needSubmit = true;
		echo '<tr><th style="width: 15%">' . get_text('IndEventList') . '</th><td>';
		while($row=safe_fetch($Rs))
			echo '<input type="checkbox" name="addField[]" value="' . $row->Event . '|1|0">'. $row->Event . "&nbsp;&nbsp;&nbsp;";
		echo '</td></tr>';
	}
	//Team Events
	$Sql = "SELECT EvCode as Event
		FROM Events
		LEFT JOIN Awards ON EvTournament=AwTournament AND EvCode=AwEvent AND AwFinEvent=1 AND EvTeamEvent=AwTeam
		WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 AND AwEvent IS NULL";
	$Rs=safe_r_SQL($Sql);
	if(safe_num_rows($Rs)) {
		$needSubmit = true;
		echo '<tr><th style="width: 15%">' . get_text('TeamEventList') . '</th><td>';
		while($row=safe_fetch($Rs))
			echo '<input type="checkbox" name="addField[]" value="' . $row->Event . '|1|1">'. $row->Event . "&nbsp;&nbsp;&nbsp;";
		echo '</td></tr>';
	}
	//Individual Cl/div
	$Sql = "SELECT CONCAT(DivId,ClId) as Event
		FROM Divisions INNER JOIN Classes ON DivTournament=ClTournament
		LEFT JOIN Awards ON DivTournament=AwTournament AND CONCAT(DivId,ClId)=AwEvent AND AwFinEvent=0 AND AwTeam=0
		WHERE DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND ClAthlete=1 AND DivAthlete=1 AND AwEvent IS NULL";
	$Rs=safe_r_SQL($Sql);
	if(safe_num_rows($Rs)) {
		$needSubmit = true;
		echo '<tr><th style="width: 15%">' . get_text('ResultIndClass', 'Tournament') . '</th><td>';
		while($row=safe_fetch($Rs))
			echo '<input type="checkbox" name="addField[]" value="' . $row->Event . '|0|0">'. $row->Event . "&nbsp;&nbsp;&nbsp;";
		echo '</td></tr>';
	}
	//Team Cl/div
	$Sql = "SELECT CONCAT(DivId,ClId) as Event
		FROM Divisions INNER JOIN Classes ON DivTournament=ClTournament
		LEFT JOIN Awards ON DivTournament=AwTournament AND CONCAT(DivId,ClId)=AwEvent AND AwFinEvent=0 AND AwTeam=1
		WHERE DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND ClAthlete=1 AND DivAthlete=1 AND AwEvent IS NULL";
	$Rs=safe_r_SQL($Sql);
	if(safe_num_rows($Rs)) {
		$needSubmit = true;
		echo '<tr><th style="width: 15%">' . get_text('ResultSqClass', 'Tournament') . '</th><td>';
		while($row=safe_fetch($Rs))
			echo '<input type="checkbox" name="addField[]" value="' . $row->Event. '|0|1">'. $row->Event . "&nbsp;&nbsp;&nbsp;";
		echo '</td></tr>';
	}
	if($needSubmit)
		echo '<tr><td colspan="2" class="Center"><input type="hidden" name="Command" value="ADD"><input type="submit" name="' . get_text('CmdAdd', 'Tournament') . '"></td></th>';

	echo '</table></td></tr>';
?>
</table>
<?php
	include('Common/Templates/tail.php');
?>