<?php
/*

// TO ADD
get_text('Freetext', 'Tournament')

NEW VERSION

Each schedule line can be of type:
- Free text
--- has a date & time attribute for start
--- has a duration attribute
--- has a title and a text, at least one should be filled
--- has a "show time" flag
- Tournament Object
--- same as above
--- Q: Qualification
----- Session (name)
----- Distance and Category
----- group flag
--- E: Eliminiation
----- Session (name)
----- Categories
----- group flag
--- M: Matches
----- Events
----- Phase
----- Group flag

*/
require_once(dirname(dirname(__FILE__)) . '/config.php');

CheckTourSession(true);

if(!empty($_REQUEST['Activate'])) {
	if(empty($_SESSION['ActiveSession'])) {
		$_SESSION['ActiveSession']=array();
	}
	if(in_array($_REQUEST['Activate'], $_SESSION['ActiveSession'])) {
		unset($_SESSION['ActiveSession'][array_search($_REQUEST['Activate'], $_SESSION['ActiveSession'])]);
	} else {
		$_SESSION['ActiveSession'][]=$_REQUEST['Activate'];
	}
	Set_Tournament_Option('ActiveSession', $_SESSION['ActiveSession']);
	CD_redirect(basename(__FILE__));
}

require_once('Common/Lib/Fun_Scheduler.php');
require_once('./LibScheduler.php');

$edit=(empty($_REQUEST['key']) ? '' : preg_replace('#[^0-9:| -]#sim', '', $_REQUEST['key']));

$JS_SCRIPT=array(
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Scheduler/Fun_AJAX_Scheduler.js"></script>',
	'<link href="'.$CFG->ROOT_DIR.'Scheduler/Scheduler.css" media="screen" rel="stylesheet" type="text/css">',
);
$JS_SCRIPT[]='<style>
</style>';

include('Common/Templates/head.php');

echo '<table class="Tabella">
	<tr><th class="Main" colspan="10">'.get_text('Scheduler').'</th></tr>
	<tr class="Divider"><td colspan="10"></td></tr>
	<tr class="Divider"><td colspan="10"><form action="./PrnScheduler.php" target="PDF">
			<b>'.get_text('MenuLM_PrintScheduling').':</b>
			<input type="checkbox" name="Finalists">'.get_text('SchIncFinalists','Tournament').'&nbsp;&nbsp;
			<input type="checkbox" name="Daily">'.get_text('DailySchedule', 'Tournament').'&nbsp;&nbsp;
			<input type="submit" name="Complete" value="'.get_text('CompleteSchedule', 'Tournament').'">&nbsp;&nbsp;
			<input type="submit" name="Today" value="'.get_text('ScheduleToday', 'Tournament').'">&nbsp;&nbsp;
			<input type="submit" name="FromDay" value="'.get_text('ScheduleFromDay', 'Tournament').'">&nbsp;&nbsp;
			<input type="text" name="FromDayDay">
			</form></td></tr>
	<tr class="Divider"><td colspan="10"></td></tr>
	<tr><td id="Manager">';

// management
echo '<table id="ScheduleTexts">';

// Get all the texts from the scheduler
echo getScheduleTexts();

echo '</table>';


echo '<table>';

// Get all the qualification items with date & time
$q=safe_r_sql("select DiSession,
		DiDistance,
		if(DiDay=0, '', DiDay) DiDay,
		if(DiStart=0, '', date_format(DiStart, '%H:%i')) DiStart,
		DiDuration,
		if(DiWarmStart=0, '', date_format(DiWarmStart, '%H:%i')) DiWarmStart,
		DiWarmDuration,
		DiOptions,
		if(SesName!='', SesName, DiSession) Session,
		DiShift
	from DistanceInformation
	inner join Session on SesTournament=DiTournament and SesOrder=DiSession and SesType=DiType and SesType='Q'
	where DiTournament={$_SESSION['TourId']}
	order by DiSession, DiDistance");
echo '<tr>
		<th class="Title" colspan="6">'.get_text('Q-Session', 'Tournament').'</th>
		<th class="Title" colspan="4" width="10%">'.get_text('WarmUp', 'Tournament').'</th>
	</tr>
	<tr>
		<th class="Title" width="10%">'.get_text('Session').'</th>
		<th class="Title" width="10%">'.get_text('Distance', 'Tournament').'</th>
		<th class="Title" width="10%"><img src="'.$CFG->ROOT_DIR.'Common/Images/Tip.png" title="'.get_Text('TipDate', 'Tournament').'" align="right">'.get_text('Date', 'Tournament').'</th>
		<th class="Title" width="10%">'.get_text('Time', 'Tournament').'</th>
		<th class="Title" width="10%">'.get_text('Length', 'Tournament').'</th>
		<th class="Title" width="10%">'.get_text('Delayed', 'Tournament').'</th>
		<th class="Title" width="10%">'.get_text('Time', 'Tournament').'</th>
		<th class="Title" width="10%">'.get_text('Length', 'Tournament').'</th>
		<th class="Title" colspan="2" width="10%">'.get_text('ScheduleNotes', 'Tournament').'</th>
	</tr>';
while($r=safe_fetch($q)) {
	echo '<tr>
		<th nowrap="nowrap">'.$r->Session.'</td>
		<th nowrap="nowrap">'.$r->DiDistance.'</td>
		<td><input size="10" type="text" name="Fld[Q][Day]['.$r->DiSession.']['.$r->DiDistance.']" value="'.$r->DiDay.'" onchange="DiUpdate(this)"></td>
		<td><input size="5"  type="text" name="Fld[Q][Start]['.$r->DiSession.']['.$r->DiDistance.']" value="'.$r->DiStart.'" onchange="DiUpdate(this)"></td>
		<td><input size="3"  type="text" name="Fld[Q][Duration]['.$r->DiSession.']['.$r->DiDistance.']" value="'.$r->DiDuration.'" onchange="DiUpdate(this)"></td>
		<td><input size="3"  type="text" name="Fld[Q][Shift]['.$r->DiSession.']['.$r->DiDistance.']" value="'.$r->DiShift.'" onchange="DiUpdate(this)"></td>
		<td><input size="5"  type="text" name="Fld[Q][WarmTime]['.$r->DiSession.']['.$r->DiDistance.']" value="'.$r->DiWarmStart.'" onchange="DiUpdate(this)"></td>
		<td><input size="3"  type="text" name="Fld[Q][WarmDuration]['.$r->DiSession.']['.$r->DiDistance.']" value="'.$r->DiWarmDuration.'" onchange="DiUpdate(this)"></td>
		<td colspan="2"><input size="50" type="text" name="Fld[Q][Options]['.$r->DiSession.']['.$r->DiDistance.']" value="'.$r->DiOptions.'" onchange="DiUpdate(this)"></td>
		</tr>';
}

// Get all the Elimination items with date & time
$q=safe_r_sql("select SesOrder,
		ElElimPhase,
		if(DiDay=0, '', DiDay) DiDay,
		if(DiStart=0, '', date_format(DiStart, '%H:%i')) DiStart,
		DiDuration,
		if(DiWarmStart=0, '', date_format(DiWarmStart, '%H:%i')) DiWarmStart,
		DiWarmDuration,
		DiOptions,
		if(SesName!='', SesName, SesOrder) Session, Events, DiShift
	from Session
	inner join (select distinct ElSession, ElTournament, ElElimPhase, group_concat(distinct ElEventCode order by ElEventCode separator ', ') Events from Eliminations where ElTournament={$_SESSION['TourId']} group by ElTournament, ElSession, ElElimPhase) Phase on ElSession=SesOrder and ElTournament=SesTournament
	left join DistanceInformation on SesTournament=DiTournament and SesOrder=DiSession and ElElimPhase=DiDistance and DiType='E'
	where SesTournament={$_SESSION['TourId']}
	and SesType='E'
	order by SesOrder, ElElimPhase");
if(safe_num_rows($q)) {
	echo '<tr class="Divider"><td colspan="10"></td></tr>
		<tr>
			<th class="Title" colspan="6">'.get_text('E-Session', 'Tournament').'</th>
			<th class="Title" colspan="4" width="10%">'.get_text('WarmUp', 'Tournament').'</th>
		</tr>
		<tr>
			<th class="Title" width="10%">'.get_text('Session').'</th>
			<th class="Title" width="10%">'.get_text('Eliminations').'</th>
			<th class="Title" width="10%">'.get_text('Date', 'Tournament').'</th>
			<th class="Title" width="10%">'.get_text('Time', 'Tournament').'</th>
			<th class="Title" width="10%">'.get_text('Length', 'Tournament').'</th>
			<th class="Title" width="10%">'.get_text('Delayed', 'Tournament').'</th>
			<th class="Title" width="10%">'.get_text('Time', 'Tournament').'</th>
			<th class="Title" width="10%">'.get_text('Length', 'Tournament').'</th>
			<th class="Title" colspan="2" width="10%">'.get_text('ScheduleNotes', 'Tournament').'</th>
		</tr>';
		while($r=safe_fetch($q)) {
		echo '<tr>
			<th nowrap="nowrap">'.$r->Session.'<br/>'.$r->Events.'</td>
			<th nowrap="nowrap">'.get_text('Eliminations_'.($r->ElElimPhase+1)).'</td>
			<td><input size="10" type="text" name="Fld[E][Day]['.$r->SesOrder.']['.$r->ElElimPhase.']" value="'.$r->DiDay.'" onchange="DiUpdate(this)"></td>
			<td><input size="5"  type="text" name="Fld[E][Start]['.$r->SesOrder.']['.$r->ElElimPhase.']" value="'.$r->DiStart.'" onchange="DiUpdate(this)"></td>
			<td><input size="3"  type="text" name="Fld[E][Duration]['.$r->SesOrder.']['.$r->ElElimPhase.']" value="'.$r->DiDuration.'" onchange="DiUpdate(this)"></td>
			<td><input size="3"  type="text" name="Fld[E][Shift]['.$r->SesOrder.']['.$r->ElElimPhase.']" value="'.$r->DiShift.'" onchange="DiUpdate(this)"></td>
			<td><input size="5"  type="text" name="Fld[E][WarmTime]['.$r->SesOrder.']['.$r->ElElimPhase.']" value="'.$r->DiWarmStart.'" onchange="DiUpdate(this)"></td>
			<td><input size="3"  type="text" name="Fld[E][WarmDuration]['.$r->SesOrder.']['.$r->ElElimPhase.']" value="'.$r->DiWarmDuration.'" onchange="DiUpdate(this)"></td>
			<td colspan="2"><input size="50" type="text" name="Fld[E][Options]['.$r->SesOrder.']['.$r->ElElimPhase.']" value="'.$r->DiOptions.'" onchange="DiUpdate(this)"></td>
			</tr>';
	}
}

if(!empty($_SESSION['InfoMenu']->RoundRobin)) {
	// get all the scheduled round robins
	$SQL="SELECT
		g.F2FPhase AS phase,
		g.F2FRound AS round,
		g.F2FMatchNo1 AS matchNo1,
		g.F2FMatchNo2 AS matchNo2,
		g.F2FGroup AS `group`,
		group_concat(f1.F2FEvent order by f1.F2FEvent separator ', ') AS `Events`,
		group_concat(CONCAT(en1.EnFirstName,' ',en1.EnName, ' <-> ', en2.EnFirstName,' ',en2.EnName) separator '<br>') AS name,
		date_format(f1.F2FSchedule, '%Y-%m-%d') as F2FDate,
		date_format(f1.F2FSchedule, '%H:%i:%s') as F2FTime,
		date_format(f1.F2FSchedule, '%d-%m-%Y') as ScheduledDate,
		date_format(f1.F2FSchedule, '%H:%i') as ScheduledTime
	FROM
		F2FGrid AS g
		LEFT JOIN F2FFinal AS f1
			ON f1.F2FTournament={$_SESSION['TourId']} AND g.F2FPhase=f1.F2FPhase AND g.F2FRound=f1.F2FRound AND g.F2FGroup=f1.F2FGroup AND g.F2FMatchNo1=f1.F2FMatchNo
		LEFT JOIN Entries AS en1
			ON f1.F2FEnId=en1.EnId AND en1.EnTournament={$_SESSION['TourId']}

		LEFT JOIN F2FFinal AS f2
			ON f2.F2FTournament={$_SESSION['TourId']} AND g.F2FPhase=f2.F2FPhase AND g.F2FRound=f2.F2FRound AND g.F2FGroup=f2.F2FGroup AND g.F2FMatchNo2=f2.F2FMatchNo AND f1.F2FEvent=f2.F2FEvent
		LEFT JOIN Entries AS en2
			ON f2.F2FEnId=en2.EnId AND en2.EnTournament={$_SESSION['TourId']}

	WHERE
		g.F2FTournament={$_SESSION['TourId']}
		and f1.F2FSchedule>0
	group by f1.F2FSchedule, phase, round, `group`
	ORDER BY
		f1.F2FSchedule";
	$q=safe_r_sql($SQL);
	if(safe_num_rows($q)) {
		echo '<tr class="Divider"><td colspan="10"></td></tr>
			<tr>
				<th class="Title" colspan="6">'.get_text('R-Session', 'Tournament').'</th>
				<th class="Title" colspan="4" width="10%">'.get_text('WarmUp', 'Tournament').'</th>
			</tr>
			<tr>
				<th class="Title" width="10%">'.get_text('Events', 'Tournament').'</th>
				<th class="Title" width="10%">'.get_text('Phase').'</th>
				<th class="Title" width="10%">'.get_text('Date', 'Tournament').'</th>
				<th class="Title" width="10%">'.get_text('Time', 'Tournament').'</th>
				<th class="Title" width="10%">'.get_text('Length', 'Tournament').'</th>
				<th class="Title" width="10%">'.get_text('Delayed', 'Tournament').'</th>
				<th class="Title" width="10%">'.get_text('Time', 'Tournament').'</th>
				<th class="Title" width="10%">'.get_text('Length', 'Tournament').'</th>
				<th class="Title" colspan="2" width="10%">'.get_text('ScheduleNotes', 'Tournament').'</th>
			</tr>';
		while($r=safe_fetch($q)) {
			echo '<tr>
				<th nowrap="nowrap">'.$r->Events.'</td>
				<th nowrap="nowrap">Phase '.$r->phase.' - Round '.$r->round.' - Group '.$r->group.'</td>
				<td><input size="10" type="text" name="Fld[R][Day]['.$r->F2FDate .']['.$r->F2FTime.']" value="'.$r->ScheduledDate.'" onchange="DiUpdate(this)"></td>
				<td><input size="5"  type="text" name="Fld[R][Start]['.$r->F2FDate .']['.$r->F2FTime.']" value="'.$r->ScheduledTime.'" onchange="DiUpdate(this)"></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				</tr>';
		}
	}
}

// Get all the Matches items with date & time
$SQL="select
		FsTeamEvent, GrPhase, FsScheduledDate, FsScheduledTime,
		if(FsScheduledDate=0, '', FsScheduledDate) ScheduledDate,
		if(FsScheduledTime=0, '', date_format(FsScheduledTime, '%H:%i')) ScheduledTime,
		FsScheduledLen,
		EvFinalFirstPhase,
		FwTime,
		FwDuration,
		FwOptions,
		group_concat(distinct FsEvent order by FsEvent separator ', ') Events, FsShift
	from FinSchedule
	inner join Events on FsEvent=EvCode and FsTeamEvent=EvTeamEvent and FsTournament=EvTournament
	inner join Grids on FsMatchNo=GrMatchNo
	left join (
		select
		FwTeamEvent, FwDay, FwMatchTime, FwEvent, FwTournament,
		group_concat( date_format(FwTime, '%H:%i') order by FwTime separator '|') FwTime,
		group_concat( FwDuration separator '|') FwDuration,
		group_concat( FwOptions separator '|') FwOptions
		from FinWarmup
		where FwTournament={$_SESSION['TourId']}
		group by FwTeamEvent, FwDay, FwMatchTime, FwEvent
		) FinWarmup on FsEvent=FwEvent and FsTeamEvent=FwTeamEvent and FsTournament=FwTournament and FsScheduledDate=FwDay and FsScheduledTime=FwMatchTime
	where FsTournament={$_SESSION['TourId']}
	group by FsTeamEvent, GrPhase, FsScheduledDate, FsScheduledTime
	order by FsScheduledDate, FsScheduledTime, FwTime, FsTeamEvent, GrPhase desc";
$q=safe_r_sql($SQL);
if(safe_num_rows($q)) {
	$OldHeader='';
	$TeamEvent='I';
	while($r=safe_fetch($q)) {
		if($OldHeader!=$r->FsTeamEvent) {
			$TeamEvent=($r->FsTeamEvent ? 'T' : 'I');
			echo '<tr class="Divider"><td colspan="10"></td></tr>
				<tr>
					<th class="Title" colspan="6">'.get_text(($r->FsTeamEvent ? 'T' : 'I').'-Session', 'Tournament').'</th>
					<th class="Title" colspan="4" width="10%">'.get_text('WarmUp', 'Tournament').'</th>
				</tr>
				<tr>
					<th class="Title" width="10%">'.get_text('Events', 'Tournament').'</th>
					<th class="Title" width="10%">'.get_text('Phase').'</th>
					<th class="Title" width="10%">'.get_text('Date', 'Tournament').'</th>
					<th class="Title" width="10%">'.get_text('Time', 'Tournament').'</th>
					<th class="Title" width="10%">'.get_text('Length', 'Tournament').'</th>
					<th class="Title" width="10%">'.get_text('Delayed', 'Tournament').'</th>
					<th class="Title" width="10%">'.get_text('Time', 'Tournament').'</th>
					<th class="Title" width="10%">'.get_text('Length', 'Tournament').'</th>
					<th class="Title" colspan="2" width="10%">'.get_text('ScheduleNotes', 'Tournament').'</th>
				</tr>';
			$OldHeader=$r->FsTeamEvent;
		}
		echo '<tr>
			<th nowrap="nowrap">'.$r->Events.'</td>
			<th nowrap="nowrap">'.get_text(((($r->EvFinalFirstPhase==48 or $r->EvFinalFirstPhase==24) and $r->GrPhase>16) ? ($r->GrPhase==64 ? 48 : 24) : $r->GrPhase).'_Phase').'</td>
			<td><input size="10" type="text" name="Fld['.$TeamEvent.'][Day]['.$r->GrPhase.']['.$r->FsScheduledDate .']['.$r->FsScheduledTime.']" value="'.$r->ScheduledDate.'" onchange="DiUpdate(this)"></td>
			<td><input size="5"  type="text" name="Fld['.$TeamEvent.'][Start]['.$r->GrPhase.']['.$r->FsScheduledDate .']['.$r->FsScheduledTime.']" value="'.$r->ScheduledTime.'" onchange="DiUpdate(this)"></td>
			<td><input size="3"  type="text" name="Fld['.$TeamEvent.'][Duration]['.$r->GrPhase.']['.$r->FsScheduledDate .']['.$r->FsScheduledTime.']" value="'.$r->FsScheduledLen.'" onchange="DiUpdate(this)"></td>
			<td><input size="3"  type="text" name="Fld['.$TeamEvent.'][Shift]['.$r->GrPhase.']['.$r->FsScheduledDate .']['.$r->FsScheduledTime.']" value="'.$r->FsShift.'" onchange="DiUpdate(this)"></td>
			<td>';
		$FwTimes=explode('|', $r->FwTime);
		foreach($FwTimes as $k => $FwTime) {
			if($k) echo '<br/>';
			echo '<input size="5"  type="text" name="Fld['.$TeamEvent.'][WarmTime]['.$r->GrPhase.']['.$r->FsScheduledDate .']['.$r->FsScheduledTime.']['.$FwTime.']" value="'.$FwTime.'" onchange="DiUpdate(this)">';
		}
		echo '</td>
			<td>';
		foreach(explode('|', $r->FwDuration) as $k => $FwDuration) {
			if($k) echo '<br/>';
			echo '<input size="3"  type="text" name="Fld['.$TeamEvent.'][WarmDuration]['.$r->GrPhase.']['.$r->FsScheduledDate .']['.$r->FsScheduledTime.']['.$FwTimes[$k].']" value="'.$FwDuration.'" onchange="DiUpdate(this)">';
		}
		echo '</td>
			<td>';
		foreach(explode('|', $r->FwOptions) as $k => $FwOption) {
			if($k) echo '<br/>';
			echo '<input size="50" type="text" name="Fld['.$TeamEvent.'][Options]['.$r->GrPhase.']['.$r->FsScheduledDate .']['.$r->FsScheduledTime.']['.$FwTimes[$k].']" value="'.$FwOption.'" onchange="DiUpdate(this)">';
		}
		echo '</td>
			<td>';
		foreach($FwTimes as $k => $FwTime) {
			if($k) {
				echo '<br/>';
				echo '<input type="button" value="'.get_text('CmdDelete', 'Tournament').'" onclick="DiDelSubRow(this, \''.$TeamEvent.'|'.$r->GrPhase.'|'.$r->FsScheduledDate .'|'.$r->FsScheduledTime.'|'.$FwTime.'\')">';
			} else {
				echo '<input type="button" value="'.get_text('CmdAdd', 'Tournament').'" onclick="DiAddSubRow(this)">';
			}
		}
		echo '</td>
			</tr>';
	}
}
echo '</table>';
echo '</td>';

// Schedule
echo '<td id="TrueScheduler" width="100%">';
$Schedule=new Scheduler();
$Schedule->ROOT_DIR=$CFG->ROOT_DIR;
echo $Schedule->getScheduleHTML('SET');
echo '</td></tr>';



echo '</table>';
include('Common/Templates/tail.php');

