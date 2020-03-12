<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
CheckTourSession(true);

$JSON=array('error'=>1, 'data'=>array());

if(empty($_REQUEST['Session']) or !preg_match("/^[EF][0-9]+$/i", $_REQUEST['Session'])) {
	JsonOut($JSON);
}

$SesType=$_REQUEST['Session'][0];
$SesOrder=intval(substr($_REQUEST['Session'], 1));

checkACL(array(AclIndividuals,AclTeams, AclOutput), AclReadOnly, false);
require_once('Common/Lib/CommonLib.php');
require_once('Common/Lib/Obj_RankFactory.php');

// get all matches in that session
$Sql = "SELECT SesName, SesDtStart, SesDtEnd FROM Session WHERE SesTournament=".$_SESSION['TourId'] . " AND SesType='$SesType' and SesOrder=$SesOrder";
$q=safe_r_SQL($Sql);
$Sessions = array();
$whereCond=array();
$cnt=1;
while($r=safe_fetch($q)) {
	$Sessions[] = array("Name"=>$r->SesName, "Start"=>$r->SesDtStart, "End"=>$r->SesDtEnd);
	$whereCond[$cnt++] = "()";
}

$Sql = "SELECT FsEvent, FsTeamEvent, FsMatchNo, EvElimType
	FROM FinSchedule
	inner join Session on SesTournament=FSTournament and CONCAT(FsScheduledDate, ' ', FsScheduledTime) BETWEEN SesDtStart AND SesDtEnd
	inner join Events on EvTournament=FSTournament and EvTeamEvent=FSTeamEvent and EvCode=FSEvent
	WHERE FsTournament=".$_SESSION['TourId'] ." AND (FsMatchNo%2=0) and SesType='$SesType' and SesOrder=$SesOrder
	ORDER BY FsScheduledDate, FsScheduledTime";
$q=safe_r_SQL($Sql);
while($r=safe_fetch($q)) {
    $opts = array('matchno' => $r->FsMatchNo, 'events' => $r->FsEvent);
    $rank = Obj_RankFactory::create(($r->FsTeamEvent ? 'GridTeam' : 'GridInd'), $opts);
    $rank->read();
    $Data = $rank->getData();
//  debug_svela($Data);

    foreach ($Data['sections'] as $kSec => $vSec) {
        foreach ($vSec['phases'] as $kPh => $vPh) {
            foreach ($vPh['items'] as $kItem => $vItem) {
                $tmpL = "";
                $tmpR = "";
                if ($r->FsTeamEvent == 0) {
                    if ($vItem["bib"]) {
                        $tmpL = $vItem["athlete"] . " (" . $vItem["countryCode"] . ")";
                    }
                    if($vItem["oppBib"]) {
                        $tmpR =  $vItem["oppAthlete"] . " (" . $vItem["oppCountryCode"] . ")";
                    }
                } else {
                    if ($vItem["teamId"]) {
                        $tmpL = $vItem["countryName"] . " (" . $vItem["countryCode"] . ")";
                    }
                    if($vItem["oppTeamId"]) {
                        $tmpR =  $vItem["oppCountryName"] . " (" . $vItem["oppCountryCode"] . ")" ;
                    }
                }
                if($tmpL!='' AND $tmpR!='') {
                    $JSON['data'][] = array('key' => $kSec . '|' . $vItem['matchNo'] . '|' . $r->FsTeamEvent, 'Event' => $kSec, 'MatchNo' => $vItem['matchNo'], 'Team' => $r->FsTeamEvent, 'Time' => $vItem['scheduledTime'], 'PhEv' => $vPh['meta']['phaseName'] . ' ' . $kSec, 'value' => $tmpL . '<br>' . $tmpR);
                }
            }
        }
    }
    $JSON['error']=0;
}
JsonOut($JSON);

