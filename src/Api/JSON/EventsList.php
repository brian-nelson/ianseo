<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Fun_Phases.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');

$TourId=0;
if(isset($_REQUEST['CompCode']) && preg_match("/[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
	$TourId = getIdFromCode($_REQUEST['CompCode']);
}

$json_array=array();

$SQL="SELECT EvCode, EvEventName, EvTeamEvent, EvFinalFirstPhase FROM Events WHERE EvTournament=$TourId AND EvFinalFirstPhase!=0 order by EvTeamEvent, EvProgr";
// Retrieve the Event List
$q=safe_r_sql($SQL);
while($r=safe_fetch($q)) {
	$tmpPhases = Array();
	$cntPhase = 1;
	$phases = getPhasesId($r->EvFinalFirstPhase);
	foreach ($phases as $ph) {
		$tmpPhases[]=Array("PhCode"=>strval(bitwisePhaseId($ph)), "PhPhase"=>get_text(namePhase($r->EvFinalFirstPhase,$ph)."_Phase"), "PhNameShort"=>getPhaseTV($ph,$cntPhase), "PhName"=>get_text(getPhaseTV($ph,$cntPhase)."_Phase","Tournament"));
		$cntPhase++;
	}

    $tgtInfo = GetMaxScores($r->EvCode, 0, $r->EvTeamEvent, $TourId);
	$tgtTargetFace = array('Radius'=>$tgtInfo['TargetRadius'], 'Rings'=>array());
    foreach( $tgtInfo['Arrows'] as $item) {
        if($item['size']) {
            unset($item['size']);
            unset($item['letter']);
            $tgtTargetFace['Rings'][$item['print']] = $item;
        }
    }
    $json_array[] = Array("Event"=>$r->EvCode, "Type"=>$r->EvTeamEvent , "EvName"=>$r->EvEventName, "Distance"=>$tgtInfo['Distance'], "Phases"=>$tmpPhases, "TargetFace"=>$tgtTargetFace);

}


// Return the json structure with the callback function that is needed by the app
SendResult($json_array);
