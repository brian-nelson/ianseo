<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Fun_Modules.php');

/*
 * Message:
 * ST - Selection who shoot first
 * AB - Call in line, with the timing that need to be set when it will be "green"
 * SS - Start of the end (1 beep)
 * AT - Every Second, with info about timing, side and what we are shooting (Individual Matches, Team Matches - means hold time or reset time on athlete change)
 * AE - End of the end (3 beeps)
 * 
 * Data: 
 * Msg  - can be one among ST,AB,SS,AT,AE
 * Side - can be L (left), R (right), <empty> (undefined) for not alternate shooting
 * Time - time in seconds
 * Color - Color in string (red|yellow|green)
 * Beep - No. of beep [0-3]
 * 
 * Parameter in the different messages:
 * ST - Msg, Side
 * AB - Msg, Time
 * SS - Msg, Side
 * AT - Msg, Side, Time, Color, Beep
 * AE - Msg
 *  
 */

$TourId = 0;
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
	$TourId = getIdFromCode($_REQUEST['CompCode']);
} else {
	$IsCode=GetParameter('CasparCode');
	$TourId=getIdFromCode($IsCode);
}

$msgType = "";
if(isset($_REQUEST['Msg']) && preg_match("/^[ST|AB|SS|AT|AE]+$/i", $_REQUEST['Msg'])) {
	$msgType = $_REQUEST['Msg'];
}

$side = 0;
if(isset($_REQUEST['Side']) && preg_match("/^[LR]+$/i", $_REQUEST['Side'])) {
	$side = ($_REQUEST['Side']=="L" ? 1:2);
}

$time = 0;
if(isset($_REQUEST['Time']) && preg_match("/^[0-9]+$/", $_REQUEST['Time'])) {
	$time = $_REQUEST['Time'];
}

$color = 'red';
if(isset($_REQUEST['Color']) && preg_match("/^(red|yellow|green)+$/i", $_REQUEST['Color'])) {
	$color = mb_strtolower($_REQUEST['Color']);
}

$beep = 0;
if(isset($_REQUEST['Beep']) && preg_match("/^[01235]$/", $_REQUEST['Beep'])) {
	$beep = $_REQUEST['Beep'];
}

$isTeam = 0;
if(isset($_REQUEST['Team']) && preg_match("/^[01]+$/i", $_REQUEST['Team'])) {
	$isTeam = $_REQUEST['Team'];
}

$json_array=array("Error"=>1, "Info"=>"");

switch ($msgType) {
	case 'ST':
		if($side==0) {
			continue;
		}
	case 'SS':
		if(/*($liveMatch=getLiveMatch()) !== false &&*/ $side!=0) {
			/*
			$MatchNoSet = $liveMatch["MatchNo"] + ($side-1);
			$MatchNoClear = $MatchNoSet + ($MatchNoSet %2 ==0 ? 1:-1);
			
			$event=$liveMatch["MatchNo"];
			$TeamEvent=$r->TeamEvent;
			$isTeamMatch = ($r->TeamEvent ? true : false);
			
			
			if($End[0]=="T") {
				$End = ($TeamEvent ? 4 : 5);
			} else {
				$End = intval($End)-1;
			}
			
			$TabPrefix=($TeamEvent ? 'Tf' : 'Fin');
			$Table=($TeamEvent ? 'Team' : '');
			
			safe_w_sql("UPDATE {$Table}Finals
			SET {$TabPrefix}ShootFirst=({$TabPrefix}ShootFirst | ".pow(2, $End).")
			WHERE {$TabPrefix}Tournament={$TourId} AND {$TabPrefix}Event='$event' AND {$TabPrefix}MatchNo={$MatchNoSet}");
			
			safe_w_sql("UPDATE {$Table}Finals
			SET {$TabPrefix}ShootFirst=({$TabPrefix}ShootFirst & ~".pow(2, $End).")
			WHERE {$TabPrefix}Tournament={$TourId} and {$TabPrefix}Event='$event' and {$TabPrefix}MatchNo={$MatchNoClear}");
			runJack("FinShootingFirst", $TourId, array("Event"=>$event ,"Team"=>$TeamEvent ,"MatchNo"=>$MatchNo ,"TourId"=>$TourId));
			*/
			$json_array["Info"] = "Shooting First - " . $side==1 ? "Left":"Right";
			$json_array["Error"]=0;
		}
		break;
	case 'AB':
		runJack("Timing", $TourId, array("Time"=>$time ,"Side"=>"Left", "TourId"=>$TourId));
		runJack("Timing", $TourId, array("Time"=>$time ,"Side"=>"Right", "TourId"=>$TourId));
		$json_array["Info"] = "Call in Line - Reset Both sides to {$time} seconds";
		$json_array["Error"]=0;
		break;
	case 'AT':
		if($side==0) {
			runJack("Timing", $TourId, array("Time"=>$time ,"Side"=>"Left", "Color"=>$color, "Beeps"=>$beep, "TourId"=>$TourId));
			runJack("Timing", $TourId, array("Time"=>$time ,"Side"=>"Right", "Color"=>$color, "Beeps"=>$beep, "TourId"=>$TourId));
			$json_array["Info"] = "Timing, Both sides to {$time} seconds";
		} else {
			$tmpSide = ($side==1 ? "Left":"Right");
			runJack("Timing", $TourId, array("Time"=>$time ,"Side"=>$tmpSide, "Color"=>$color, "Beeps"=>$beep, "TourId"=>$TourId));
			$json_array["Info"] = "Timing, {$tmpSide} to {$time} seconds";
		}
		$json_array["Error"]=0;
		break;
	case 'AE':
		$json_array["Info"] = "End of Timing";
		$json_array["Error"]=0;
		break;
}

SendResult($json_array);

function getLiveMatch() {
	global $TourId;
	// gets the live match
	$q=safe_r_SQL("(select 0 TeamEvent, FinMatchNo MatchNo, FinEvent Event
			from Finals
			inner join Events on FinTournament=EvTournament and FinEvent=EvCode and EvTeamEvent=0
			where FinTournament={$TourId} and FinLive=1
			) union (
			select 1, TfMatchNo, TfEvent
			from TeamFinals
			inner join Events on TfTournament=EvTournament and TfEvent=EvCode and EvTeamEvent=1
			where TfTournament={$TourId} and TfLive=1)
			order by MatchNo");
	if($r=safe_fetch($q)) {
		return array("MatchNo"=>$r->MatchNo, "Event"=>$r->Event, "Team"=>$r->TeamEvent);
	} else {
		return false;
	}
}
