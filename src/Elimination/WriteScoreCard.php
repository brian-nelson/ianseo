<?php
//define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/ScoreEditor/Score.class.php');
CheckTourSession(true);
checkACL(AclEliminations, AclReadWrite);
if (BlockExperimental) printcrackerror(false,'Blocked');

require_once('Common/Lib/CommonLib.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Various.inc.php');
require_once('Common/Fun_Sessions.inc.php');

$JS_SCRIPT=array(
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Elimination/WriteArrows.js"></script>',
	phpVars2js(array(
		'CmdPostUpdate'=>get_text('CmdPostUpdate'),
		'PostUpdating'=>get_text('PostUpdating'),
		'PostUpdateEnd'=>get_text('PostUpdateEnd'),
		'RootDir'=>$CFG->ROOT_DIR.'Elimination/',
		'MsgAreYouSure' => get_text('MsgAreYouSure'),
	)),
);

// creates the 2 stripes of checkboxes for the various phases/events
$Events=isset($_REQUEST['Events']) ? $_REQUEST['Events'] : array();

$Select = "SELECT EvCode,EvTournament,	EvEventName, EvElim1, EvElim2
FROM Events
WHERE EvTournament={$_SESSION['TourId']}
AND EvTeamEvent=0
AND (EvElim1>0 OR EvElim2>0)
ORDER BY EvProgr ASC ";
$Rs=safe_r_sql($Select);

$CheckEvent1='';
$CheckEvent2='';

while($MyRow=safe_fetch($Rs)) {
	if ($MyRow->EvElim1>0) {
		$CheckEvent1.='<input type="checkbox" name="Events[]" value="' . $MyRow->EvCode .'-0"' . (in_array($MyRow->EvCode . "-0",$Events) ? ' checked' : '') . '>' . $MyRow->EvCode.'&nbsp;&nbsp;';
	}
	if ($MyRow->EvElim2>0) {
		$CheckEvent2.='<input type="checkbox" name="Events[]" value="' . $MyRow->EvCode .'-1"' . (in_array($MyRow->EvCode . "-1",$Events) ? ' checked' : '') . '>' . $MyRow->EvCode.'&nbsp;&nbsp;';
	}
}

$ComboSes = '';
$q=safe_r_sql("select distinct group_concat(distinct concat(ElEventCode, '-', ElElimPhase) order by EvProgr) as SessionId, concat('".get_text('Session')." ', SesOrder, if(SesName!='', concat(': ', SesName), '')) SessionName
		from Eliminations
		inner join Events on EvTournament=ElTournament and EvTeamEvent=0
		inner join Session on SesTournament=ElTournament and ElSession=SesOrder and SesType='E'
		where ElTournament={$_SESSION['TourId']} and ElId>0
		group by SesOrder");
if(safe_num_rows($q)) {
	$ComboSes = '<select name="x_Session" id="x_Session" onChange="SelectSession(this);">' . "\n";
	$ComboSes.= '<option value="">---</option>' . "\n";

	while($r=safe_fetch($q)) {
		$ComboSes.= '<option value="' . $r->SessionId . '"' . (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$r->SessionId ? ' selected' : '') . '>' . $r->SessionName . '</option>' . "\n";
	}
	$ComboSes.= '</select>' . "\n";
}

$TxtTarget = '<input type="text" name="x_Target" id="x_Target" size="5" maxlength="' . (TargetNoPadding +1) . '" value="' . (isset($_REQUEST['x_Target']) ? $_REQUEST['x_Target'] : '') . '">';

$PAGE_TITLE=get_text('Elimination');

include('Common/Templates/head.php');

?>
<form name="FrmParam" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="Tabella">
<tr><th class="Title" colspan="5"><?php print get_text('Elimination');?></th></tr>
<tr class="Divider"><td colspan="5"></td></tr>
<tr>
	<th width="100"><?php print get_text('Session');?></th>
	<th width="100"><?php print get_text('Events', 'Tournament');?></th>
	<th width="5%"><?php echo get_text('Target') ?></th>
	<th></th>
</tr>
<tr>
	<td class="Center"><?php print $ComboSes; ?></td>
	<td nowrap="nowrap" id="EventSelector">
		<?php
			if ($CheckEvent1!='') {
				print '<div style="white-space:nobreak;" class="Right">' . get_text('Eliminations_1'). ': ' . $CheckEvent1.'</div>';
			}

			if ($CheckEvent2!='') {
				print '<div style="white-space:nobreak;" class="Right">' . get_text('Eliminations_2'). ': ' . $CheckEvent2.'</div>';
			}
			?>
	</td>
	<td class="Center"><?php print $TxtTarget; ?></td>
	<td><input type="submit" value="<?php echo get_text('CmdOk')?>"/></td>
</tr>
</table>
</form>

<br>

<?php
if($Events and !empty($_REQUEST['x_Target'])) {
	$target=(is_numeric(substr($_REQUEST['x_Target'], -1)) ? str_pad($_REQUEST['x_Target'], TargetNoPadding, '0', STR_PAD_LEFT).'_': str_pad($_REQUEST['x_Target'], TargetNoPadding+1, '0', STR_PAD_LEFT));
	$Select = "SELECT EnId,EnCode,EnName,EnFirstName,EnTournament,EnDivision,EnClass,EnCountry,CoCode, CoName, (EnStatus <=1) AS EnValid,EnStatus,
			ElTargetNo,(ElTargetNo-1) as TgtOffset, EvCode,
			ElScore, ElHits, ElGold, ElXnine, ElArrowString, ElElimPhase,
			if(ElElimPhase=0, EvE1Ends, EvE2Ends) Ends, if(ElElimPhase=0, EvE1Arrows, EvE2Arrows) Arrows, ToGolds, ToXNine, ToCategory
		FROM Eliminations
		inner join Entries on ElId=EnId and EnAthlete=1
		INNER JOIN Countries ON EnCountry=CoId
		inner join Events on EvTournament=ElTournament and EvTeamEvent=0 and EvCode=ElEventCode
		INNER JOIN Tournament ON ToId=ElTournament
		WHERE concat(ElEventCode, '-', ElElimPhase) in (".implode(',', StrSafe_DB($Events)).")
			AND ElTournament={$_SESSION['TourId']}
			AND ElTargetNo like '{$target}'
			order by ElElimPhase, ElTargetNo";

	$Rs=safe_r_sql($Select);
	if(safe_num_rows($Rs)>1) {
		$Head=false;
		echo '<table class="Tabella">';
		while ($MyRow=safe_fetch($Rs)) {
			if(!$Head) {
				echo '<tr>';
				echo '<th>' . get_text('Target') . '</th>';
				echo '<th>' . get_text('Code','Tournament') . '</th>';
				echo '<th>' . get_text('Archer') . '</th>';
				echo '<th>' . get_text('Country') . '</th>';
				echo '<th>' . get_text('Event') . '</th>';
				echo '<th>' . get_text('Total') . '</th>';
				echo '<th>' . $MyRow->ToGolds . '</th>';
				echo '<th>' . $MyRow->ToXNine . '</th>';
				echo '</tr>';
				$Head=true;
			}
			echo '<tr onClick="document.getElementById(\'x_Target\').value=\'' .$MyRow->ElTargetNo . '\';document.FrmParam.submit();">';
			echo '<td>' . $MyRow->ElTargetNo . '</td>';
			echo '<td>' . $MyRow->EnCode . '</td>';
			echo '<td>' . $MyRow->EnFirstName . ' ' . $MyRow->EnName . '</td>';
			echo '<td>' . $MyRow->CoCode . '-' . $MyRow->CoName . '</td>';
			echo '<td>' . $MyRow->EvCode . '</td>';
			echo '<td>' . $MyRow->ElScore . '</td>';
			echo '<td>' . $MyRow->ElGold . '</td>';
			echo '<td>' . $MyRow->ElXnine . '</td>';
			echo '</tr>';
		}
		echo '</table>';
	} elseif(safe_num_rows($Rs)==1) {
		// show the scorecard!
		$MyRow=safe_fetch($Rs);

		$FieldId = $MyRow->EvCode . '_' . $MyRow->ElElimPhase . '_' . $MyRow->EnId;

		$MaxArrows=$MyRow->Ends*$MyRow->Arrows;
		echo '<form name="Frm" method="POST" action="">';
		echo '<input type="hidden" name="ScoreCard" id="ScoreCard">';
		echo '<table class="Tabella">';
		echo '<tr>';
		echo '<td valign="top">';

		//Dettaglio Arciere
		echo '<table class="Tabella">';
		echo '<tr>
			<th>' . get_text('Target') . '</th><td>' . $MyRow->ElTargetNo . '</td>
			<th>' . get_text('Code','Tournament') . '</th><td>' . $MyRow->EnCode . '</td></tr>';
		echo '<tr>
			<th>' . get_text('Archer') . '</th><td>' . $MyRow->EnFirstName . ' ' . $MyRow->EnName . '</td>
			<th>' . get_text('Event') . '</th><td>' . $MyRow->EvCode . '</td>
			</tr>';
		echo '<tr><th>' . get_text('Country') . '</th><td>' . $MyRow->CoCode . '</td><td colspan="2">' . $MyRow->CoName . '</td></tr>';
		echo '<tr><td colspan="4"></td></tr>';
		echo '<tr><th>' . get_text('TVCss3Arrows', 'Tournament') . '</th><th>' . get_text('Total') . '</th><th>' . $MyRow->ToGolds . '</th><th>' . $MyRow->ToXNine . '</th></tr>';
		echo '<tr>
			<td class="Bold Right" id="Hits">' . $MyRow->ElHits . '</td>
			<td class="Bold Right" id="Score">' . $MyRow->ElScore . '</td>
			<td class="Right" id="Gold">' . $MyRow->ElGold . '</td>
			<td class="Right" id="XNine">' . $MyRow->ElXnine . '</td></tr>';
		echo '</table>';

		echo '</td>';
		echo '<td valign="top">';

		// Dettaglio Score
		$ScoreNumEnds=$MyRow->Ends;
		$NumEnds=$MyRow->Ends;
		$OffSet = ($MyRow->ToCategory & 12) ? $MyRow->TgtOffset : 0;

		$NumArrows=$MyRow->Arrows;
		$ScoreNumArrows=$MyRow->Arrows;

		$MultiLine=false;

		if($NumArrows>3 and !($NumArrows%3)) {
			$NumEnds=$NumEnds*($NumArrows/3);
			$NumArrows=3;
			$MultiLine=true;
		}

		echo '<table class="Tabella">';
		echo '<tr>';
		echo '<td>&nbsp;<input type="hidden" name="MaxArrows" id="MaxArrows" value="' . $MaxArrows . '"><input type="hidden" name="NumEnds" id="NumEnds" value="' . $NumEnds . '"></td>';
		for($i=1; $i<=$NumArrows; $i++)
			echo '<th>' . $i . '</th>';
		echo '<th>'. get_text('TotalProg','Tournament') . '</th><th' . ($MultiLine ? ' colspan="2"' : ''). '>'. get_text('TotalShort','Tournament') . '</th></tr>';
		$ArrowString = str_pad($MyRow->ElArrowString, $MaxArrows, ' ', STR_PAD_RIGHT);
		$TotRunning=0;
		$TotEndRun=0;

		for($i=0; $i<$NumEnds; $i++) {
			$RealI=(($i+$OffSet)%$NumEnds)+1;
			echo '<tr id="Row_'.$FieldId.'_'.$RealI.'">';
			echo '<th>' . $RealI . '</th>';
			$ArrNo = (($i+$OffSet)%$NumEnds) * $NumArrows;
			$TotEnd=0;
			for($j=0; $j<$NumArrows; $j++) {
				echo '<td class="Center">'
					. '<input type="text" id="arr_' . ($ArrNo+$j) . '_' . $FieldId . '" '
					. 'size="2" maxlength="2" value="' . DecodeFromLetter($ArrowString[$ArrNo+$j]) . '" '
					. 'onChange="UpdateArrow(this, \'score\');">'
					. '</td>';
				$TotEnd += ValutaArrowString($ArrowString[$ArrNo+$j]);
				$TotEndRun += ValutaArrowString($ArrowString[$ArrNo+$j]);
				$TotRunning += ValutaArrowString($ArrowString[$ArrNo+$j]);
			}
			echo '<td class="Right" id="End_' . $FieldId.'_'.$RealI.'">' . $TotEnd . '</td>';
			if($MultiLine && !(($ArrNo+3) % $ScoreNumArrows)) {
				echo '<td class="Right" id="EndRun_' . $FieldId.'_'.$RealI . '">' . $TotEndRun . '</td>';
				echo '<td class="Bold Right" id="Score_' . $FieldId.'_'.$RealI . '">' . $TotRunning . '</td>';
				$TotEndRun=0;
			} elseif($MultiLine) {
				echo '<td colspan="2">&nbsp;</td>';
			} else {
				echo '<td class="Bold Right" id="Score_' . $FieldId.'_'.$RealI . '">' . $TotRunning . '</td>';
			}
			echo '</tr>';
		}
		echo '</tr>';
		echo '<tr>';
		echo '<td colspan="' . ($NumArrows+1) . '">&nbsp;</td>';
		echo '<th>'. get_text('Total') . '</th>';
		echo '<td class="Bold Right"><div id="TotScore">' . $MyRow->ElScore . '</div></td>';
		echo '</tr>';
		echo '</table>';
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		echo '</form>';
	}
}

if(!empty($GoBack)) {
	echo '<table class="Tabella2" width="50%"><tr><th style="background-color:red"><a href="'.$GoBack.'" style="color:white">'.get_text('BackBarCodeCheck','Tournament').'</a></th></tr></table>';
}

echo '<div id="idOutput"></div>';

include('Common/Templates/tail.php');
