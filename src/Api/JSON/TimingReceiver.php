<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Fun_Modules.php');
require_once('Common/Lib/Fun_Phases.inc.php');

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
 * Time - time in seconds and side identified by Side
 * Color - Color in string (red|yellow|green) and side identified by Side
 * End - End No.
 * Beep - No. of beep [0-3]
 * Parameter in the different messages:
 * ST - Msg, Side, End
 * AB - Msg, Time, End
 * SS - Msg, Side, End
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

$end = 0;
if(isset($_REQUEST['End']) && preg_match("/^([0-9]+)|(T)$/i", $_REQUEST['End'])) {
    if($_REQUEST['End']=='T') {
        $end = 'T';
    } else {
        $end = intval($_REQUEST['End']);
    }
}

$time = 0;
if(isset($_REQUEST['Time']) && preg_match("/^[0-9]+$/", $_REQUEST['Time'])) {
	$time = intval($_REQUEST['Time']);
}

$color = 'red';
if(isset($_REQUEST['Color']) && preg_match("/^(red|yellow|green)+$/i", $_REQUEST['Color'])) {
	$color = mb_strtolower($_REQUEST['Color']);
}

$beep = 0;
if(isset($_REQUEST['Beep']) && preg_match("/^(-1)|(-3)|[01235]$/", $_REQUEST['Beep'])) {
	$beep = intval($_REQUEST['Beep']);
}


$json_array=array("Error"=>1, "Info"=>"");

switch ($msgType) {
	case 'ST':
		if($side==0) {
			continue;
            $json_array["Error"]=0;
		} else if($end != 0 OR $end == 'T'){
		    $json_array["Info"] = " Shooting First " . ($side==1 ? "Left":"Right") . " - End " . (is_numeric($end) ? $end : "S.O.") ;
            $json_array["Error"]=0;
            UpdateShootFirst($side,$end,$TourId);
        }
		break;
	case 'SS':
		if($side!=0) {
			$json_array["Info"] = "Start Shooting - " . ($side==1 ? "Left":"Right") . " - End " . (is_numeric($end) ? $end : "S.O.") ;
			$json_array["Error"]=0;
		}
		break;
	case 'AB':
		$json_array["Info"] = "Call in Line - Start Time to {$time} seconds - End " . (is_numeric($end) ? $end : "S.O.") ;
		$json_array["Error"]=0;
		break;
	case 'AT':
		if($side==0) {
			runJack("Timing", $TourId, array("Time"=>$time ,"Side"=>"Left", "Color"=>$color, "Beeps"=>$beep, "TourId"=>$TourId));
			runJack("Timing", $TourId, array("Time"=>$time ,"Side"=>"Right", "Color"=>$color, "Beeps"=>$beep, "TourId"=>$TourId));
			$json_array["Info"] = "Timing, Both sides to {$time} seconds, {$beep} beeps";
		} else {
			$tmpSide = ($side==1 ? "Left":"Right");
			runJack("Timing", $TourId, array("Time"=>$time ,"Side"=>$tmpSide, "Color"=>$color, "Beeps"=>$beep, "TourId"=>$TourId));
			$json_array["Info"] = "Timing, {$tmpSide} to {$time} seconds, {$beep} beeps";
		}
		$json_array["Error"]=0;
		break;
	case 'AE':
		$json_array["Info"] = "End of Timing";
		$json_array["Error"]=0;
		break;
}

SendResult($json_array);

function UpdateShootFirst($Side, $End, $TourId) {
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
        $MatchNo = $r->MatchNo;
        $MatchNoSet=$r->MatchNo+($Side-1);
        $MatchNoClear=$MatchNoSet + ($MatchNoSet %2 ==0 ? 1:-1);
        $event=$r->Event;
        $TeamEvent=$r->TeamEvent;

        if(is_numeric($End)) {
            $End = intval($End) - 1;
        } else {
            $objParam=getEventArrowsParams($event,($MatchNo<=1 ? 0 : pow(2, intval(log($MatchNo, 2)))),$TeamEvent,$TourId);
            $End = $objParam->ends;
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
    }
}