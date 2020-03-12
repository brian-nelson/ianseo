<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');

$JSON=array('error' => 1, 'html' => '');

if(!CheckTourSession() or BlockExperimental) {
	JsonOut($JSON);
}

// require_once('Common/Lib/CommonLib.php');
// require_once('Common/Fun_Sessions.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
// require_once('Common/Fun_FormatText.inc.php');
// require_once('Common/Fun_Various.inc.php');


// if ends or arrows is not > 0 return
$End=(empty($_REQUEST['end']) ? 0 : intval($_REQUEST['end']));
$Arrows=(empty($_REQUEST['arrows']) ? 0 : intval($_REQUEST['arrows']));
$Schedule=(empty($_REQUEST['schedule']) ? '' : $_REQUEST['schedule']);
$Events=(empty($_REQUEST['Event']) ? array() : $_REQUEST['Event']);
$Phases=(empty($_REQUEST['Phase']) ? array() : $_REQUEST['Phase']);

if(!$End or !$Arrows or (!$Schedule and (!$Events or !$Phases))) {
	JsonOut($JSON);
}

if($Schedule) {
	$Date=substr($Schedule, 1, 10);
	$Time=substr($Schedule, -5).':00';
	if($Team=$Schedule[0]) {
		$Tab='TeamFinals';
		$Pre='Tf';
		$Join='inner join Countries on TfTeam=CoId';
		$Fields=", CoCode as Country, CoName as Athlete";
        checkACL(AclTeams, AclReadWrite, false);
	} else {
		$Tab='Finals';
		$Pre='Fin';
		$Join='inner join Entries on FinAthlete=EnId inner join Countries on EnCountry=CoId';
		$Fields=", concat(upper(EnFirstName), ' ', EnName) as Athlete, CoCode as Country";
        checkACL(AclIndividuals, AclReadWrite, false);
	}
	$SQL="Select if(FsLetter, FsLetter, FsTarget) Target, EvMatchMode,
		{$Pre}Score as Score, {$Pre}SetScore as SetScore, {$Pre}Tie as Tie, {$Pre}Arrowstring as Arrowstring $Fields, {$Pre}TieBreak as TieBreak, FsMatchNo as MatchNo,
		EvCode, EvEventName, EvTeamEvent, {$Pre}Notes as Notes, {$Pre}WinLose as WinLose,
		if(GrPhase & EvMatchArrowsNo, EvElimArrows, EvFinArrows) Arrows, if(GrPhase & EvMatchArrowsNo, EvElimEnds, EvFinEnds) Ends, if(GrPhase & EvMatchArrowsNo, EvElimSO, EvFinSO) SO
		from FinSchedule
		inner join $Tab on {$Pre}Tournament=FsTournament and {$Pre}Event=FsEvent and {$Pre}MatchNo=FsMatchNo
		inner join Events on EvCode=FsEvent and EvTeamEvent=FsTeamEvent and EvTournament=FsTournament
		inner join Grids on FsMatchNo=GrMatchno
		$Join
		where FsTeamEvent=$Team and FsScheduledDate='$Date' and FsScheduledTime='$Time' and FsTournament={$_SESSION['TourId']}
		".(empty($Events[$Team]) ? '' : 'and FsEvent in ('.implode(',', StrSafe_DB($Events[$Team])).')')
		. "order by GrPhase ASC, Target='', Target, EvProgr, FsMatchNo";
} else {
	$tmp=array();
	foreach($Events as $Team => $Event) {
		if($Team) {
			$Tab='TeamFinals';
			$Pre='Tf';
			$Join='inner join Countries on TfTeam=CoId';
			$Fields=", CoCode as Country, CoName as Athlete";
            checkACL(AclTeams, AclReadWrite, false);
		} else {
			$Tab='Finals';
			$Pre='Fin';
			$Join='inner join Entries on FinAthlete=EnId inner join Countries on EnCountry=CoId';
			$Fields=", concat(upper(EnFirstName), ' ', EnName) as Athlete, CoCode as Country";
            checkACL(AclIndividuals, AclReadWrite, false);
		}
		$tmp[]="Select if(FsLetter, FsLetter, FsTarget) Target, EvMatchMode,
			{$Pre}Score as Score, {$Pre}SetScore as SetScore, {$Pre}Tie as Tie, {$Pre}Arrowstring as Arrowstring $Fields, {$Pre}TieBreak as TieBreak, {$Pre}MatchNo as MatchNo,
			EvCode, EvEventName, EvTeamEvent, {$Pre}Notes as Notes, {$Pre}WinLose as WinLose,
			if(GrPhase & EvMatchArrowsNo, EvElimArrows, EvFinArrows) Arrows, if(GrPhase & EvMatchArrowsNo, EvElimEnds, EvFinEnds) Ends, if(GrPhase & EvMatchArrowsNo, EvElimSO, EvFinSO) SO
			from $Tab
			inner join Events on EvCode={$Pre}Event and EvTeamEvent=$Team and EvTournament={$Pre}Tournament
			inner join Grids on {$Pre}MatchNo=GrMatchno
			$Join
			left join FinSchedule on FsTeamEvent=$Team and {$Pre}Tournament=FsTournament and {$Pre}Event=FsEvent and {$Pre}MatchNo=FsMatchNo
			where {$Pre}Tournament={$_SESSION['TourId']}
			and {$Pre}Event in (".implode(',', StrSafe_DB($Event)).")
			and GrPhase in (".implode(',', StrSafe_DB($Phases[$Team])).")
			order by GrPhase ASC, Target='', Target, EvProgr, MatchNo";
	}
	$SQL="(".implode(") union (", $tmp).")";
}

$OldEvent='';
$JSON['sql']=$SQL;
$q=safe_r_SQL($SQL);
$Offset=safe_num_rows($q);
$TabIndex=1;
while($r=safe_fetch($q)) {
	// check arrows etc
	if($OldEvent!=$r->EvCode) {
		$StartIdx=$Arrows*($End-1);
		if($StartIdx > $r->Ends*$r->Arrows) {
			// too many arrows :)
			continue;
		}
		$MaxArrows=$r->Ends*$r->Arrows;
		$EndIdx=min($StartIdx+$Arrows, $MaxArrows);
		$OldEvent=$r->EvCode;
		$First=true;
		$Cols=8+$EndIdx+($EndIdx==$MaxArrows ? (3*$r->SO) : 0)-$StartIdx;
		$JSON['html'].='<tr><th colspan="'.$Cols.'" class="Title">'.$r->EvCode.' - '.$r->EvEventName.'</th></tr>';
		$JSON['html'].='<tr>
				<th>'.get_text('Target').'</th>
				<th>'.get_text('Athlete').'</th>
				<th>'.get_text('Country').'</th>';
		for($n=$StartIdx; $n<$EndIdx; $n++) {
			$JSON['html'].='<th>'.($n+1).'</th>';
		}
		$JSON['html'].='<th>'.get_text('Total').'</th>';
		$JSON['html'].='<th>'.get_text('SetPoints', 'Tournament').'</th>';
		if($EndIdx==$MaxArrows) {
			for($n=0; $n<3*$r->SO; $n++) {
				$JSON['html'].='<th>SO '.($n+1).'</th>';
			}
		}
		$JSON['html'].='<th colspan="3"></th>
			</tr>';
	}

	$r->Arrowstring=str_pad($r->Arrowstring, $MaxArrows, ' ', STR_PAD_RIGHT);

	$Class='';
	if($r->Tie==2) {
		$Class= 'Bye';
	}

	if(!$First and $r->MatchNo%2==0) {
		$JSON['html'].='<tr><td class="divider" colspan="'.$Cols.'"></td></tr>';
	}

	$id=$r->EvTeamEvent.'_'.$r->EvCode.'_'.$r->MatchNo;

	$JSON['html'].='<tr class="'.$Class.'">
			<td id="tgt_'.$id.'">'.ltrim($r->Target, '0').'</td>
			<td id="nam_'.$id.'">'.$r->Athlete.'</td>
			<td id="cty_'.$id.'">'.$r->Country.'</td>';
	for($n=$StartIdx; $n<$EndIdx; $n++) {
		$a='';
		if(strlen(trim($r->Arrowstring[$n]))) $a=DecodeFromLetter($r->Arrowstring[$n]);
		$JSON['html'].='<td><input type="text" size="2" id="s_'.$id.'_'.$n.'" value="'.$a.'" onblur="updateScore(this)" onfocus="this.select()" tabindex="'.($TabIndex++).'"></td>';
	}
	$JSON['html'].='<td id="tot_'.$id.'" class="Right">'.$r->Score.'</td>';
	$JSON['html'].='<td id="set_'.$id.'" class="Center">'.($r->EvMatchMode ? $r->SetScore : '').'</td>';
	if($EndIdx==$MaxArrows) {
		$r->TieBreak=str_pad($r->TieBreak, $r->SO, ' ', STR_PAD_RIGHT);

        for($pSo=0; $pSo<3; $pSo++ ) {
            for ($n = 0; $n < $r->SO; $n++) {
                $ArrI = $n+($pSo*$r->SO);
                $JSON['html'] .= '<td><input type="text" size="2" id="tie_' . $id . '_' . $ArrI . '" value="' . (!empty($r->TieBreak[$ArrI]) ? DecodeFromLetter($r->TieBreak[$ArrI]):'') . '" onblur="SendToServer(this, this.value)" onfocus="this.select()" tabindex="' . (($Arrows * $Offset) + $TabIndex++) . '"></td>';
            }
        }
	}
	$JSON['html'].='<td>';
	if($r->MatchNo%2==0 and $r->Tie!=2) {
		$JSON['html'].='<input style="display:none" type="button" value="'.get_text('NextPhase').'" id="next_'.$id.'" onclick="move2next(this)" tabindex="'.(($Arrows+$r->SO)*$Offset+$TabIndex++).'">';
	}
	$JSON['html'].='</td>';
	$JSON['html'].='<td><input type="button" value="'.get_text('Bye').'" id="bye_'.$id.'" onclick="SendToServer(this, 2)" tabindex="'.(($Arrows+$r->SO)*$Offset+$TabIndex++).'"></td>';
	$JSON['html'].='<td><input list="NoteList" value="'.$r->Notes.'" id="note_' . $id .'" onChange="SendToServer(this, this.value)" tabindex="'.(($Arrows+$r->SO)*$Offset+$TabIndex++).'"></td>';
	$JSON['html'].='</tr>';
	$First=false;
}


if($JSON['html']) {
	$JSON['html']='<table class="Tabella" style="width:auto; margin:auto;">'.$JSON['html'].'</table>';
}

JsonOut($JSON);
