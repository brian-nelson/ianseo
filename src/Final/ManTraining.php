<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);
checkACL(AclCompetition, AclReadWrite);

if(!empty($_REQUEST['Delete'])) {
	safe_w_sql("delete from FinWarmup where
		FwTournament={$_SESSION['TourId']}
		and FwDay=".StrSafe_DB($_REQUEST['Day']) . "
		and FwTime=".StrSafe_DB($_REQUEST['Time']) . "
		and FwTeamEvent=".intval($_REQUEST['TeamEvent']) . "
		and FwEvent=".StrSafe_DB($_REQUEST['Event']) . "
		and FwMatchTime=".StrSafe_DB($_REQUEST['MatchTime']) . "
		");
	CD_redirect(basename(__FILE__));
}

$k=0;

$JS_SCRIPT=array(
	'<script type="text/javascript" src="../Common/ajax/ObjXMLHttpRequest.js"></script>',
	'<script type="text/javascript" src="Fun_AJAX_ManTraining.js"></script>',
	'<script type="text/javascript" >var StrConfirm="' . get_text('MsgAreYouSure') . '";</script>'
	);

include('Common/Templates/head.php');

echo '<div align="center">';

$SQL="Select fw.*, group_concat(distinct FsTarget order by FsTarget) Targets from FinWarmup fw
	left join FinSchedule on FsTournament={$_SESSION['TourId']} and FwDay=FsScheduledDate and FwMatchTime=FsScheduledTime and FsEvent=FwEvent and FsTeamEvent=FwTeamEvent and FsTarget>''
	where FwTournament={$_SESSION['TourId']}
	group by FwDay, FwMatchTime, FwTime, FwTeamEvent, FwEvent
	order by FwDay, FwTime, FwMatchTime, Targets, FwTeamEvent, FwEvent";

echo '<table class="Tabella">';
echo '<tbody id="tbody">';
echo '<tr><th class="Title" colspan="9">'.get_text('ManTraining', 'Tournament').'</th></tr>';
echo '<tr>';
echo '<th colspan="2">'.get_text('DateTimeViewFmt').'</th>';
echo '<th>'.get_text('WarmUpMins', 'Tournament').'</th>';
echo '<th>'.get_text('IndEventList').'</th>';
echo '<th>'.get_text('TeamEventList').'</th>';
echo '<th>'.get_text('WarmupTargets', 'Tournament').'</th>';
echo '<th width="1%"></th>';
echo '<th>'.get_text('MatchTargets', 'Tournament').'/'.get_text('ScheduleNotes', 'Tournament').'</th>';
echo '<th>'.get_text('RelatedMatchTime', 'Tournament').'</th>';
echo '</tr>';

$q=safe_r_SQL($SQL);
while($myRow=safe_fetch($q)) {
	echo '<tr id="row_' . ($k++) . '">';
	echo '<td class="Center">'.$myRow->FwDay . '</a></td>';
	echo '<td class="Center">' . substr($myRow->FwTime,0,5) . '</td>';
	echo '<td class="Center">' . $myRow->FwDuration . '</td>';
	echo '<td class="Center">' . ($myRow->FwTeamEvent ? '' : $myRow->FwEvent) . '</td>';
	echo '<td class="Center">' . ($myRow->FwTeamEvent ? $myRow->FwEvent : '') . '</td>';
	echo '<td class="Center"><input type="text" size="50" name="Targets['.$myRow->FwDay . '][' . $myRow->FwTime . '][' . $myRow->FwTeamEvent . '][' . $myRow->FwEvent . ']" value="'.$myRow->FwTargets.'" onchange="UpdateTargets(this)"></td>';
	if($myRow->FwMatchTime=='00:00:00') {
		echo '<td width="1%"></td>';
		echo '<td class="Center"><input type="text" size="50" name="Comments[]" value="'.$myRow->FwOptions.'" onchange="UpdateTargets(this)"></td>';
	} else {
		echo '<td class="Center"><input type="button" value="<=" onclick="DefaultTarget(this)"></td>';
		echo '<td class="Left">' . decodeRange($myRow->Targets) . '</td>';
	}
	echo '<td class="Center">'
		. substr($myRow->FwMatchTime,0,5) . '<br><a href="?Delete=1&Day='.$myRow->FwDay . '&Time=' . $myRow->FwTime . '&TeamEvent=' . $myRow->FwTeamEvent . '&Event=' . $myRow->FwEvent . '&MatchTime=' . $myRow->FwMatchTime . '" class="Button">'.get_text('CmdDelete', 'Tournament').'</a>'
		. '</td>';
	echo '</tr>';
}

echo '</tbody>';
echo '</table>';

echo '</div>';

include('Common/Templates/tail.php');



function decodeRange($string='') {
	if(empty($string)) return $string;
	if(!is_array($string)) $string=explode(',', $string);
	sort($string);
	$ret=array();
	$oldNum='';
	$Start='';
	$End='';
	foreach($string as $k => $num) {
		$num=intval($num);
		if(!$k) {
			$Start=$num;
			$oldNum=$num;
			continue;
		}
		if($num==$oldNum+1) {
			$oldNum=$num;
			continue;
		} else {
			$ret[]="$Start-$oldNum";
			$Start=$num;
			$oldNum=$num;
		}
	}
	if($Start and $oldNum) {
		$ret[]="$Start-$oldNum";
	}
	return implode(', ', $ret);
}