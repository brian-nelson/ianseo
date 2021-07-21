<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
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

$TxtArrows = '<input type="text" name="x_Arrows" id="x_Arrows" size="3" maxlength="3" value="' . (isset($_REQUEST['x_Arrows']) ? $_REQUEST['x_Arrows'] : '') . '">';
$TxtVolee = '<input type="text" name="x_Volee" id="x_Volee" size="3" maxlength="3" value="' . (isset($_REQUEST['x_Volee']) ? $_REQUEST['x_Volee'] : '') . '">';


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
	<th width="100"><?php echo get_text('End (volee)') ?></th>
	<th width="100"><?php echo get_text('Arrows','Tournament') ?></th>
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
	<td class="Center"><?php print $TxtVolee; ?></td>
	<td class="Center"><?php print $TxtArrows; ?></td>
	<td><input type="submit" value="<?php echo get_text('CmdOk')?>"/></td>
</tr>
</table>
</form>

<br>
<?php

if($Events and
		is_numeric($_REQUEST['x_Arrows']) && $_REQUEST['x_Arrows']>0 &&
		is_numeric($_REQUEST['x_Volee']) && $_REQUEST['x_Volee']>0 ) {

	// check if index of first arrow is in range for that elimination, phase and event
	$q=safe_r_sql("select distinct EvCode, EvEventName, ElElimPhase, if(ElElimPhase=0, EvE1Ends, EvE2Ends) Ends, if(ElElimPhase=0, EvE1Arrows, EvE2Arrows) Arrows, ToGolds, ToXNine
		from Eliminations
		inner join Events on EvCode=ElEventCode and EvTeamEvent=0 and EvTournament=ElTournament
		INNER JOIN Tournament on ToId=ElTournament
		where ElTournament={$_SESSION['TourId']} and concat(ElEventCode, '-', ElElimPhase) in (".implode(',', StrSafe_DB($Events)).")
		order by EvProgr, ElElimPhase");
	while($TOUR=safe_fetch($q)) {
		$Offset=$_REQUEST['x_Arrows']*($_REQUEST['x_Volee']-1);
		$MaxArrows=$TOUR->Ends*$TOUR->Arrows;
		$NumArrows=min($_REQUEST['x_Arrows'], $MaxArrows-$Offset);

		if($Offset<$MaxArrows and $NumArrows>0) {
			$Select = "SELECT EnId,EnCode,EnName,EnFirstName,EnTournament,EvCode,EnCountry,CoCode, (EnStatus <=1) AS EnValid, EnStatus,
					ElTargetNo AS Target,
					ElScore AS SelScore, ElHits AS SelHits, ElGold AS SelGold, ElXnine AS SelXNine, ElElimPhase,
					ElArrowString AS ArrowString
				FROM Entries INNER JOIN Countries ON EnCountry=CoId
				INNER JOIN Eliminations ON ElId=EnId and ElTournament=EnTournament and ElEventCode='{$TOUR->EvCode}' and ElElimPhase=$TOUR->ElElimPhase
				INNER JOIN Events on EvCode=ElEventCode and EvTeamEvent=0 and EvTournament=ElTournament
				WHERE EnAthlete=1 AND EnTournament={$_SESSION['TourId']}
				ORDER BY ElTargetNo ASC ";

			$Rs=safe_r_sql($Select);
			if (safe_num_rows($Rs)>0) {
				echo '<form name="Frm" method="POST" action="">';
				echo '<table class="Tabella" style="margin:auto;width:auto;">';
				echo '<tr><th class="Title" colspan="'.($NumArrows+7).'">'.$TOUR->EvCode.' - '.$TOUR->EvEventName.' (Phase '.($TOUR->ElElimPhase+1).')</th></tr>';
				echo '<tr>';
				echo '<td class="Title">'.get_text('Target').'</td>';
				echo '<td class="Title">'.get_text('Code','Tournament').'</td>';
				echo '<td class="Title">'.get_text('Archer').'</td>';
				echo '<td class="Title">'.get_text('Country').'</td>';
				foreach(range($Offset+1, $Offset+$NumArrows) as $i) {
					echo '<td class="Title">(' . ($i) . ')</td>';
				}
				echo '<td class="Title" width="5%">'.get_text('Total').'</td>';
				echo '<td class="Title" width="5%">'.$TOUR->ToGolds . '</td>';
				echo '<td class="Title" width="5%">'.$TOUR->ToXNine . '</td>';
				echo '</tr>';

				$CurTarget = 'xx';
				$RowStyle='';	// NoShoot oppure niente
				$TarStyle='';	// niene oppure warning se $RowStyle==''
				while ($MyRow=safe_fetch($Rs)) {
					$RowStyle=($MyRow->EnValid ? '' : 'NoShoot');
					if ($CurTarget!='xx') {
						if ($CurTarget!=substr($MyRow->Target,0,-1) ) {
							if ($TarStyle=='') {
								$TarStyle='warning';
							} elseif($TarStyle=='warning') {
								$TarStyle='';
							}
						}
					}
					$FieldId = $MyRow->EvCode . '_' . $MyRow->ElElimPhase . '_' . $MyRow->EnId;
					echo '<tr id="Row_'.$FieldId.'" class="' . ($RowStyle!='' ? $RowStyle : $TarStyle) . '">';
					echo '<td>'.$MyRow->Target.'</td>';
					echo '<td>'.$MyRow->EnCode.'</td>';
					echo '<td>'.$MyRow->EnFirstName . ' ' . $MyRow->EnName.'</td>';
					echo '<td>'.$MyRow->CoCode.'</td>';

					$CurArrowString = str_pad($MyRow->ArrowString,$MaxArrows,' ',STR_PAD_RIGHT);
					foreach(range($Offset, $Offset+$NumArrows-1) as $i) {
						$vv = DecodeFromLetter($CurArrowString[$i]);
						echo '<td class="Center">'
							. '<input type="text" id="arr_' . ($i) . '_' . $FieldId . '" '
							. 'size="2" maxlength="2" value="' . $vv . '" '
							. 'onChange="UpdateArrow(this, \'table\');">'
							. '</td>';
					}

					echo '<td id="Score_'.$FieldId.'" onDblClick="window.open(\'WriteScoreCard.php?Events[]='.$MyRow->EvCode.'-'.$MyRow->ElElimPhase.'&x_Target='.$MyRow->Target.'\', '.$MyRow->EnId.');" class="Center Bold">'.$MyRow->SelScore.'</td>';
					echo '<td id="Gold_'.$FieldId.'" class="Center Bold">'.$MyRow->SelGold.'</td>';
					echo '<td id="XNine_'.$FieldId.'" class="Center Bold">'.$MyRow->SelXNine.'</td>';
					$CurTarget=	substr($MyRow->Target,0,-1);
				}
				echo '</tr>';
				echo '</table>';
				echo '</form>';
			}
		}
	}

}
echo '<div id="idOutput"></div>';

include('Common/Templates/tail.php');
