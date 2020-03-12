<?php
require_once(dirname(dirname(__FILE__)).'/config.php');
require_once('Common/Lib/Fun_Modules.php');
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');

$JSON=array('error'=>true, 'data'=>array());
$SelectedEvent='';
$TourId=0;
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
    $TourId = getIdFromCode($_REQUEST['CompCode']);
}

$schedule=(isset($_REQUEST['Schedule']) && preg_match('/^[01]{1}[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}(:[0-9]{2})*$/',$_REQUEST['Schedule']) ? substr($_REQUEST['Schedule'],1) : null);
$team=(isset($_REQUEST['Schedule']) && preg_match('/^[01]{1}[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}(:[0-9]{2})*$/',$_REQUEST['Schedule']) ? substr($_REQUEST['Schedule'],0,1) : null);

if($TourId == 0 or is_null($schedule) or is_null($team)) {
    JsonOut($JSON);
} else {
    $JSON['error']=false;
}

if(strlen($schedule)<19) $schedule.=':00';

$Options=array('tournament' => $TourId, 'schedule' => $schedule);
if($team) {
    $rank=Obj_RankFactory::create('GridTeam',$Options);
    $rank->read();
    $rankData=$rank->getData();
} else {
    $rank=Obj_RankFactory::create('GridInd',$Options);
    $rank->read();
    $rankData=$rank->getData();
}

$list=array();
foreach($rankData['sections'] as $IdEvent => $section) {
    if(empty($section['phases'])) {
        continue;
    }
    $arrNo = ($section['meta']['finEnds']*$section['meta']['finArrows']);
    foreach($section['phases'] as $IdPhase => $phase) {
        foreach($phase['items'] as $key => $item) {
            if(empty($item['id']) AND empty($item['oppId']) AND empty($item['teamId']) AND empty($item['oppTeamId'])) {
                continue;
            }
            $data=array();
            $data['Target']=ltrim($item['target'],'0').($item['target']!=$item['oppTarget'] ? '-'.ltrim($item['oppTarget'],'0') : '');
            $data['TargetNoL'] = min(intval($item['target']),intval($item['oppTarget']));
            $data['TargetNoH'] = max(intval($item['target']),intval($item['oppTarget']));
            $data['OppL']=$team ? $item['countryName'] : $item['athlete'];
            $data['NocL']=$item['countryCode'];
            $data['OppR']=$team ? $item['oppCountryName'] : $item['oppAthlete'];
            $data['NocR']=$item['oppCountryCode'];
            if($team) {
                $data['ComponentsL']=array();
                if(!empty($section["athletes"][$item["teamId"]][$item["subTeam"]] )) {
                    foreach($section["athletes"][$item["teamId"]][$item["subTeam"]] as $kAth=>$vAth) {
                        $data['ComponentsL'][]= array("Code"=>$vAth["code"], "Athlete"=>$vAth["athlete"], "Gender"=>$vAth["gender"]);
                    }
                }
                $data['ComponentsR']=array();
                if(!empty($section["athletes"][$item["oppTeamId"]][$item["oppSubTeam"]] )) {
                    foreach($section["athletes"][$item["oppTeamId"]][$item["oppSubTeam"]] as $kAth=>$vAth) {
                        $data['ComponentsR'][]= array("Code"=>$vAth["code"], "Athlete"=>$vAth["athlete"], "Gender"=>$vAth["gender"]);
                    }
                }
            }
            $data['Event']=$IdEvent . ' ' . $phase['meta']['phaseName'];
            $data['ScoreL']=$section['meta']['matchMode'] ? $item['setScore'] : $item['score'];
            $data['ScoreR']=$section['meta']['matchMode'] ? $item['oppSetScore'] : $item['oppScore'];
            $data['TieL']=$item['tiebreakDecoded'];
            $data['TieR']=$item['oppTiebreakDecoded'];
            $data['WinL']=($item['winner']== 1 ? true : false);
            $data['WinR']=($item['oppWinner']== 1 ? true : false);
            $data['LastUpdate']=max($item['lastUpdated'], $item['oppLastUpdated']);
            $tmpDateTime =new DateTime(max($item['lastUpdated'], $item['oppLastUpdated']));
            $data['LuSeconds']=(time()- $tmpDateTime->getTimestamp());
            $data['Finished']=($item['winner'] or $item['oppWinner'] or ($item['notes']=='DSQ' and $item['oppNotes']=='DSQ'));
            $data['SO']=(($data['ScoreL'] == $data['ScoreR']) AND !$data['Finished'] AND strlen(trim($item['arrowstring']))==$arrNo AND strlen(trim($item['oppArrowstring']))==$arrNo);
            $data['Bye']=($item['tie']==2 or $item['oppTie']==2);
            $list[$item['target']]=$data;
        }
    }

}
ksort($list);
$JSON['data']=array_values($list);

JsonOut($JSON);