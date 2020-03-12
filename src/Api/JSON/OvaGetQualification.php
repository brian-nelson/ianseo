<?php
require_once(dirname(dirname(__FILE__)).'/config.php');
require_once('Common/Lib/Obj_RankFactory.php');

$JSON=array('error'=>true, 'data'=>array());
$TourId=0;
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
    $TourId = getIdFromCode($_REQUEST['CompCode']);
}
if($TourId == 0) {
    JsonOut($JSON);
} else {
    $JSON['error']=false;
}

$EventsI=array();
$EventsT=array();
$limitEvents=false;
if(!empty($_REQUEST['Events'])) {
    $limitEvents=true;
    $tmp=explode(',', $_REQUEST['Events']);
    foreach ($tmp as $ev) {
        if(substr($ev,-1,1)=='I') {
            $EventsI[]=substr($ev,0,-2);
        } else {
            $EventsT[]=substr($ev,0,-2);
        }
    }
}
$options=array('tournament'=>$TourId,'dist'=>0);
if(!$limitEvents OR count($EventsI)) {
    $rank = Obj_RankFactory::create('Abs', $options+(count($EventsI) ? array('events'=>$EventsI):array()));
    $rank->read();
    $Data=$rank->getData();
    foreach($Data['sections'] as $kSec=>$vSec) {
        $json_array = Array(
            "Event" => $kSec,
            "Type" => 'I',
            "Running" => ($vSec['meta']['running'] ? true : false),
            "LastUpdate"=>$vSec['meta']['lastUpdate'],
            $tmpDateTime = new DateTime($vSec['meta']['lastUpdate']),
            "LuSeconds"=>(time()-$tmpDateTime->getTimestamp()),
            "Header"=>(empty($vSec['meta']['printHeader']) ? ($vSec['meta']['running'] ? 'Running' : implode(', ',$vSec['meta']['sesArrows'])): $vSec['meta']['printHeader']),
            "Results" => array()
        );
        foreach ($vSec['items'] as $kItem => $vItem) {
            $tmp = array("Rank"=>intval($vItem["rank"]));
            $tmp += array("Code"=>$vItem["bib"], "Athlete"=>$vItem["athlete"],
                "Target"=>ltrim($vItem["target"],"0"),
                "Session"=>$vItem["session"]);

            $tmp += array("Noc"=>$vItem["countryCode"], "Nation"=>$vItem["countryName"]);
            $tmp += array("Score"=>($vSec['meta']['running'] ? number_format(floatval($vItem["score"]),3) : intval($vItem["score"])), "Gold"=>intval($vItem["gold"]), "XNine"=>intval($vItem["xnine"]), "Arrows"=>intval($vItem["hits"]),
                "CT"=>(($vItem["so"]==0 AND $vItem["ct"]>1) ? true:false), "SO"=>($vItem["so"]>0 ? true:false));
            $tmp += array("SOValue" => ($vItem["so"]>0 ? $vItem["tiebreakDecoded"]:''));
            $json_array["Results"][] = $tmp;
        }
        $JSON['data'][]=$json_array;
    }
}
if(!$limitEvents OR count($EventsT)) {
    $rank = Obj_RankFactory::create('AbsTeam', $options+(count($EventsT) ? array('events'=>$EventsT):array()));
    $rank->read();
    $Data=$rank->getData();
    foreach($Data['sections'] as $kSec=>$vSec) {
        $json_array = Array(
            "Event" => $kSec,
            "Type" => 'T',
            "Running" => ($vSec['meta']['running'] ? true : false),
            "LastUpdate"=>$vSec['meta']['lastUpdate'],
            $tmpDateTime = new DateTime($vSec['meta']['lastUpdate']),
            "LuSeconds"=>(time()-$tmpDateTime->getTimestamp()),
            "Header"=>(empty($vSec['meta']['printHeader']) ? ($vSec['meta']['running'] ? 'Running' : implode(', ',$vSec['meta']['sesArrows'])): $vSec['meta']['printHeader']),
            "Results" => array()
        );
        foreach ($vSec['items'] as $kItem => $vItem) {
            $tmp = array("Rank"=>intval($vItem["rank"]));
            $tmp += array("Noc"=>$vItem["countryCode"], "Nation"=>$vItem["countryName"]);
            $tmpAth=array();
            foreach($vItem["athletes"] as $kAth=>$vAth) {
                $tmpAth[$kAth]= array("Code"=>$vAth["bib"], "Athlete"=>$vAth["athlete"], "Gender"=>($vAth["gender"] ? 'W':'M'));
            }
            $tmp["Components"] = $tmpAth;
            $tmp += array("Score"=>($vSec['meta']['running'] ? floatval(number_format($vItem["score"],3)) : intval($vItem["score"])), "Gold"=>intval($vItem["gold"]), "XNine"=>intval($vItem["xnine"]), "Arrows"=>intval($vItem["hits"]),
                "CT"=>(($vItem["so"]==0 AND $vItem["ct"]>1) ? true:false), "SO"=>($vItem["so"]>0 ? true:false));
            $tmp += array("SOValue" => ($vItem["so"]>0 ? $vItem["tiebreakDecoded"]:''));
            $json_array["Results"][] = $tmp;
        }
        $JSON['data'][]=$json_array;
    }
}

JsonOut($JSON);