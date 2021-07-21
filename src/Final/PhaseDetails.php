<?php

require_once(dirname(__DIR__) . '/config.php');
CheckTourSession(true);
checkACL(AclCompetition, AclReadWrite);
require_once('Common/Lib/CommonLib.php');

//require_once('Common/Fun_FormatText.inc.php');
//require_once('Common/Fun_Phases.inc.php');
$IsBlocked = IsBlocked(BIT_BLOCK_TOURDATA);

if (false and isset($_REQUEST['Command'])) {
	if (!IsBlocked(BIT_BLOCK_TOURDATA) && $_REQUEST['Command']=='SAVE') {
		$BitMask=0;
		foreach($_REQUEST as $Key => $Value) {
			if (substr($Key,0,19)=='d_EvFinalAthTarget_') {
				list(,,$e)=explode('_',$Key);
				$BitMask+=($Value*pow(2,$e));
				/*
				 * Questa parte potrebbe essere risolta tramite una sola query.
				 * Occorre usare gli operatori bit a bit di mysql per trovare
				 * le fasi con il Bit=1
				 */
				if ($Value==1) {
					$Phase = floor(pow(2,$e)/2);
					$Update = "UPDATE FinSchedule AS fs1 
						    LEFT JOIN FinSchedule AS fs2 ON fs1.FSMatchNo=(fs2.FSMatchNo-1) AND fs1.FSEvent=fs2.FSEvent AND fs1.FSTeamEvent=fs2.FSTeamEvent AND fs1.FSTournament=fs2.FSTournament 
							SET fs2.FSTarget = fs1.FSTarget 
							WHERE fs1.FSEvent=" . StrSafe_DB($_REQUEST['d_Event2Set']) . " AND fs1.FSTeamEvent='0' AND 
							fs1.FSTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (fs1.FSMatchNo% 2)=0 
							AND fs1.FSMatchNO IN(SELECT GrMatchNo FROM Grids WHERE GrPhase=" . StrSafe_DB($Phase) . ")";
					$Rs=safe_w_sql($Update);
				}
			}
		}
		$Update = "UPDATE Events SET EvFinalAthTarget=" . StrSafe_DB($BitMask) . " 
				WHERE EvCode=" . StrSafe_DB($_REQUEST['d_Event2Set']) . " AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']);
		$Rs=safe_w_sql($Update);
	}
}

$Team=empty($_REQUEST['team']) ? 0 : 1;
$Option='AthButt';
if(!empty($_REQUEST['option'])) {
	switch($_REQUEST['option']) {
		case 'ArrowPhase':
			$Option=$_REQUEST['option'];
	}
}

$JS_SCRIPT[] = phpVars2js(array(
    'TeamType' => $Team,
    'OptionType' => $Option,
));
$JS_SCRIPT[] = '<script type="text/javascript" src="../Common/js/jquery-3.2.1.min.js"></script>';
$JS_SCRIPT[] = '<script type="text/javascript" src="./PhaseDetails.js"></script>';
$JS_SCRIPT[] = '<link href="./PhaseDetails.css" rel="stylesheet" type="text/css">';
$JS_SCRIPT[] = '<link href="../Common/css/font-awesome.css" rel="stylesheet" type="text/css">';

$PAGE_TITLE=get_text('PhasesDetails', 'Tournament');

include('Common/Templates/head.php');

echo '<table class="Tabella" style="width:unset;min-width:50%;margin:auto">
	<tr><th colspan="13" class="Main varCol">'.$PAGE_TITLE.'</th></tr>
	<tr><th colspan="13" class="Title varCol">
		<select class="Button" onchange="changeType(this)">
		<option value="0" '.($Team ? '' : ' selected="selected"').'>'.get_text('Individual').'</option>
		<option value="1" '.($Team ? ' selected="selected"' : '').'>'.get_text('Team').'</option>
		</select>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<select class="Button" onchange="changeOption(this)">
		<option value="AthButt" '.($Option=='AthButt' ? ' selected="selected"' : '').'>'.get_text('OpponentsPerTarget').'</option>
		<option value="ArrowPhase" '.($Option=='ArrowPhase' ? ' selected="selected"' : '').'>'.get_text('ManMatchArr4Phase', 'Tournament').'</option>
		</select>
	</th></tr>
	<tr><th colspan="13" class="Title varCol PhaseLegend"></th></tr>
	<tr>
		<th rowspan="2" class="Title">'.get_text('Event').'</th>
		<th colspan="4" class="Title phhide">'.get_text('Options','Tournament').'</th>
		<th colspan="8" class="Title">'.get_text('Phase').'</th>
	</tr>
	<tr>
		<th class="Title phhide"></th>
		<th class="Title phhide">'.get_text('Ends', 'Tournament').'</th>
		<th class="Title phhide">'.get_text('Arrows', 'Tournament').'</th>
		<th class="Title phhide">'.get_text('ShotOffShort', 'Tournament').'</th>
		<th class="Title ph64">'.get_text('64_Phase').' | '.get_text('48_Phase').'</th>
		<th class="Title ph32">'.get_text('32_Phase').' | '.get_text('24_Phase').'</th>
		<th class="Title ph16">'.get_text('16_Phase').'</th>
		<th class="Title ph8">'.get_text('8_Phase').'</th>
		<th class="Title ph4">'.get_text('4_Phase').'</th>
		<th class="Title ph2">'.get_text('2_Phase').'</th>
		<th class="Title ph1">'.get_text('1_Phase').'</th>
		<th class="Title ph0">'.get_text('0_Phase').'</th>
	</tr>
	<tbody id="TableBody"></tbody>
	</table>';
include('Common/Templates/tail.php');
