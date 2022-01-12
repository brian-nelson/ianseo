<?php

require_once(dirname(__FILE__) . '/config.php');

CheckTourSession(true);

$Event=!empty($_REQUEST['cat']) ? $_REQUEST['cat'] : '';

$JS_SCRIPT[] ='<script src="./index.js"></script>';
$JS_SCRIPT[] ='<script src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>';
include('Common/Templates/head.php');

$q=safe_r_sql("select * from Events where EvTournament={$_SESSION['TourId']} and EvTeamEvent>0 and EvNumQualified!=4 order by EvProgr");

echo '<div align="center" style="margin-bottom: 1em"><select onchange="document.location=\'./?cat=\'+this.value" id="Category">';
while($r=safe_fetch($q)) {
	if(!$Event) {
		$Event=$r->EvCode;
	}
	if($r->EvCode==$Event) {
		$MaxTeams=$r->EvNumQualified;
	}
	echo '<option value="'.$r->EvCode.'"'.($r->EvCode==$Event ? ' selected="selected"' : '').'>'.$r->EvCode.' '.$r->EvEventName.'</option>';
}
echo '</select></div>';

// button to reassign people in the individual events
echo '<div align="center" style="margin-bottom:1em">
	<select id="MatchDays" onchange="setTeams(\''.$Event.'\', this)">
		<option value="0"></option>
		<option value="1">'.get_text('FlightsDay','Tournament', 1).'</option>
		<option value="2">'.get_text('FlightsDay','Tournament', 2).'</option>
		<option value="3">'.get_text('FlightsDay','Tournament', 3).'</option>
	</select>
	<input type="button" onclick="assignPeople(\''.$Event.'\')" value="'.get_text('TargetDoAssignment', 'Tournament').'">
	</div>';

// gets the first 16 teams for each (team) category
$Winners=getModuleParameter('FFTA', 'D1Winners');
if(!empty($Winners[$Event])) {
	$Winners=$Winners[$Event];
} else {
    $Winners=array();
}
$Teams='<option value=0>===</option>';
$tmp=array();
$q=safe_r_sql("select CoId, CoCode, CoName from Countries where cocode in (".implode(',', StrSafe_DB($Winners)).") and CoTournament={$_SESSION['TourId']} order by CoName");
while($r=safe_fetch($q)) {
    if($k=array_search($r->CoCode, $Winners)) {
        $tmp[$k]='<option value="'.$r->CoId.'">'.$k.' - '.$r->CoCode . ' ' . $r->CoName.'</option>';
    } else {
        $tmp[]='<option value="'.$r->CoId.'">'.$r->CoCode . ' ' . $r->CoName.'</option>';
    }
}


ksort($tmp, SORT_NUMERIC);
//debug_svela($tmp);

$AllInOne=getModuleParameter('FFTA', 'D1AllInOne', 0);
$MatchDay=0;
$NumMatch=5;

$Teams.=implode('', $tmp);
echo '<table class="Tabella" style="width:auto; margin:auto">';
for($i=1; $i<=$NumMatch; $i++) {
	echo '<thead id="GameTitle'.$i.'">';
	echo '<tr>
		<th colspan="7" class="Title">'.get_text('GameNumber','Tournament', $i).'</th>
		</tr>';

	echo '<tr><td colspan="7"></td></tr>';

	$UsedTeams=array();
	$FreshTeams=$Teams;
	for($j=1; $j<=$MaxTeams/2; $j++) {
		if($j==1) {
			echo '<tr>
					<th></th>
					<th>Equipes</th>
					<th>Détail</th>
					<th>Cible 1</th>
					<th>Cible 2</th>
					<th>Jour</th>
					<th>Heure</th>
				</tr>';
		}
		$Matchno1 = 128 + (($i-1)*16) + (($j-1)*2);
		$Matchno2 = $Matchno1 + 1;
		$fake=new stdClass();
		$Status=array();
		foreach(range($Matchno1, $Matchno2) as $matchno) {
			foreach(range(0,4) as $phase) {
				$Status[$phase][$matchno]= (object) array('Phase'=>$phase, 'MatchNo' => $matchno, 'Team' => '', 'Code' => '', 'Event'=>$Event, 'Target' => '', 'FsDate' => '', 'FsTime'=>'');
			}
		}
		$SQL=array();
		if(!$AllInOne) {
			$SQL[]="select right(FinEvent, 1) as Phase, FinMatchNo as MatchNo, '' as Team, '' as Code, FinEvent as Event, trim(leading '0' from FsLetter) as Target, if(FSScheduledDate=0, '', FSScheduledDate) as FsDate, date_format(FSScheduledTime, '%H:%i') as FsTime
		    from Finals
            left join FinSchedule on FSTeamEvent=0 and FSMatchNo=FinMatchNo and FSEvent=FinEvent and FSTournament=FinTournament
            where FinMatchNo in ($Matchno1, $Matchno2) and FinEvent like '{$Event}_' and FinTournament={$_SESSION['TourId']}";
		}
		$SQL[]="select 0 as Phase, TfMatchNo as MatchNo, TfTeam as Team, CoCode as Code, TfEvent as Event, trim(leading '0' from FsTarget) as Target, if(FSScheduledDate=0, '', FSScheduledDate) as FsDate, date_format(FSScheduledTime, '%H:%i') as FsTime
		    from TeamFinals
		    inner join Countries on CoId=TfTeam and CoTournament=TfTournament
            left join FinSchedule on FSTeamEvent=1 and FSMatchNo=TfMatchNo and FSEvent=TfEvent and FSTournament=TfTournament
            where TfMatchNo in ($Matchno1, $Matchno2) and TfEvent = '$Event' and TfTournament={$_SESSION['TourId']}";
		$q=safe_r_sql("(".implode(') UNION (', $SQL).") order by MatchNo, Event");
		while($r=safe_fetch($q)) {
		    $Status[$r->Phase][$r->MatchNo]=$r;
        }

        //debug_svela($Status);
        foreach($UsedTeams as $t) {
	        $FreshTeams=str_replace('value="'.$t.'"','value="'.$t.'" disabled="disabled"', $FreshTeams);
        }

        if($Matchno1==128 and !empty($Status[0][128]->Code) and !empty($Status[0][129]->Code)) {
	        $pos1=array_search($Status[0][128]->Code, $Winners);
	        $pos2=array_search($Status[0][129]->Code, $Winners);
	        switch($AllInOne.'-'.$pos1.'-'.$pos2) {
		        case '0-1-8':
		        case '0-1-16':
		        case '1-1-16':
			        $MatchDay=1;
		            break;
		        case '0-1-3':
		        case '0-1-11':
		        case '1-1-11':
		        case '1-1-5':
			        $MatchDay=2;
		            break;
		        case '0-1-5':
			        $NumMatch=4;
		        case '0-1-6':
		        case '1-1-4':
			        $MatchDay=3;
		            break;
		        case '1-1-6':
			        if($Event=='FCO') {
			        	$MatchDay=1;
			        } else {
			        	$MatchDay=3;
			        }
		            break;
	        }
        }

		echo '</thead>';
		echo '<tbody game="'.$i.'">';
		echo '<tr class="separator"><th colspan="7"></th></tr>';
		echo '<tr match="ma-0">
				<th rowspan="5">Match '.$j.'</th>
				<td rowspan="5" nowrap="nowrap">
				    <div><select oldvalue="'.$Status[0][$Matchno1]->Team.'" match="te-'.$Matchno1.'" onchange="updateMatch(this)">'.str_replace('value="'.$Status[0][$Matchno1]->Team.'"', 'value="'.$Status[0][$Matchno1]->Team.'" selected="selected"', $FreshTeams).'</select></div>
				    <div><select oldvalue="'.$Status[0][$Matchno2]->Team.'" match="te-'.$Matchno2.'" onchange="updateMatch(this)">'.str_replace('value="'.$Status[0][$Matchno2]->Team.'"', 'value="'.$Status[0][$Matchno2]->Team.'" selected="selected"', $FreshTeams).'</select></div>
				</td>
				<th>Equipe</th>
				<td><input match="tg-'.$Matchno1.'" onchange="updateMatch(this, true)" type="text" value="'.$Status[0][$Matchno1]->Target.'" size="3"></td>
				<td><input match="tg-'.$Matchno2.'" onchange="updateMatch(this)" type="text" value="'.$Status[0][$Matchno2]->Target.'" size="3"></td>
				<td><input match="da-'.$Matchno1.'" onchange="updateMatch(this, true)" type="text" value="'.$Status[0][$Matchno1]->FsDate.'"></td>
				<td><input match="ti-'.$Matchno1.'" onchange="updateMatch(this, true)" type="text" value="'.$Status[0][$Matchno1]->FsTime.'"></td>
			</tr>';
		if(!$AllInOne) {
			echo '<tr match="ma-1">
					<th>Ind 1</th>
					<td><input match="tg-'.$Matchno1.'" onchange="updateMatch(this)" type="text" value="'.$Status[1][$Matchno1]->Target.'" size="3"></td>
					<td><input match="tg-'.$Matchno2.'" onchange="updateMatch(this)" type="text" value="'.$Status[1][$Matchno2]->Target.'" size="3"></td>
					<td><input match="da-'.$Matchno1.'" onchange="updateMatch(this)" type="text" value="'.$Status[1][$Matchno1]->FsDate.'"></td>
					<td><input match="ti-'.$Matchno1.'" onchange="updateMatch(this)" type="text" value="'.$Status[1][$Matchno1]->FsTime.'"></td>
				</tr>';
			echo '<tr match="ma-2">
					<th>Ind 2</th>
					<td><input match="tg-'.$Matchno1.'" onchange="updateMatch(this)" type="text" value="'.$Status[2][$Matchno1]->Target.'" size="3"></td>
					<td><input match="tg-'.$Matchno2.'" onchange="updateMatch(this)" type="text" value="'.$Status[2][$Matchno2]->Target.'" size="3"></td>
					<td><input match="da-'.$Matchno1.'" onchange="updateMatch(this)" type="text" value="'.$Status[2][$Matchno1]->FsDate.'"></td>
					<td><input match="ti-'.$Matchno1.'" onchange="updateMatch(this)" type="text" value="'.$Status[2][$Matchno1]->FsTime.'"></td>
				</tr>';
			echo '<tr match="ma-3">
					<th>Ind 3</th>
					<td><input match="tg-'.$Matchno1.'" onchange="updateMatch(this)" type="text" value="'.$Status[3][$Matchno1]->Target.'" size="3"></td>
					<td><input match="tg-'.$Matchno2.'" onchange="updateMatch(this)" type="text" value="'.$Status[3][$Matchno2]->Target.'" size="3"></td>
					<td><input match="da-'.$Matchno1.'" onchange="updateMatch(this)" type="text" value="'.$Status[3][$Matchno1]->FsDate.'"></td>
					<td><input match="ti-'.$Matchno1.'" onchange="updateMatch(this)" type="text" value="'.$Status[3][$Matchno1]->FsTime.'"></td>
				</tr>';
			if($NumMatch==5) {
				echo '<tr match="ma-4">
						<th>Ind 4</th>
						<td><input match="tg-'.$Matchno1.'" onchange="updateMatch(this)" type="text" value="'.$Status[4][$Matchno1]->Target.'" size="3"></td>
						<td><input match="tg-'.$Matchno2.'" onchange="updateMatch(this)" type="text" value="'.$Status[4][$Matchno2]->Target.'" size="3"></td>
						<td><input match="da-'.$Matchno1.'" onchange="updateMatch(this)" type="text" value="'.$Status[4][$Matchno1]->FsDate.'"></td>
						<td><input match="ti-'.$Matchno1.'" onchange="updateMatch(this)" type="text" value="'.$Status[4][$Matchno1]->FsTime.'"></td>
					</tr>';
			}
		}
		echo '</tbody>';
		$UsedTeams[]=$Status[0][$Matchno1]->Team;
		$UsedTeams[]=$Status[0][$Matchno2]->Team;
	}
}
echo '</table>';

echo '<script>$(function() {$(\'#MatchDays\').val('.$MatchDay.')})</script>';
include('Common/Templates/tail.php');
