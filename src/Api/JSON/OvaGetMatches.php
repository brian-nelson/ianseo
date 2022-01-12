<?php
require_once(dirname(dirname(__FILE__)).'/config.php');
require_once('Common/Lib/Fun_Modules.php');
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');

$JSON=array('error'=>true, 'data'=>array());
$SelectedEvent='';
$TourId=0;
if(isset($_REQUEST['CompCode']) AND preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
    $TourId = getIdFromCode($_REQUEST['CompCode']);
}

$schedule=((isset($_REQUEST['Schedule']) AND preg_match('/^[01]{1}[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}(:[0-9]{2})*$/',$_REQUEST['Schedule'])) ? substr($_REQUEST['Schedule'],1) : null);
$team=((isset($_REQUEST['Schedule']) AND preg_match('/^[01]{1}[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}(:[0-9]{2})*$/',$_REQUEST['Schedule'])) ? substr($_REQUEST['Schedule'],0,1) : null);

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
    foreach($section['phases'] as $IdPhase => $phase) {
        $objParam=getEventArrowsParams($IdEvent, $IdPhase, $team, $TourId);
        $arrNo = $objParam->ends*$objParam->arrows;
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
            $data['ScoreL']=(($item['tie']==2 or $item['oppTie']==2) ? $item['notes'] : ($section['meta']['matchMode'] ? $item['setScore'] : $item['score']));
            $data['ScoreR']=(($item['tie']==2 or $item['oppTie']==2) ? $item['oppNotes'] : ($section['meta']['matchMode'] ? $item['oppSetScore'] : $item['oppScore']));
            $data['TieL']=$item['tiebreakDecoded'];
            $data['TieR']=$item['oppTiebreakDecoded'];
            $data['WinL']=($item['winner']== 1 ? true : false);
            $data['WinR']=($item['oppWinner']== 1 ? true : false);
            $data['LastUpdate']=max($item['lastUpdated'], $item['oppLastUpdated']);
            $tmpDateTime =new DateTime(max($item['lastUpdated'], $item['oppLastUpdated']));
            $data['LuSeconds']=(time()- $tmpDateTime->getTimestamp());
            $data['Finished']=($item['winner'] or $item['oppWinner'] or $item['irm'] or $item['oppIrm'] or ($item['notes']=='DSQ' and $item['oppNotes']=='DSQ'));
            $data['SO']=(($data['ScoreL'] == $data['ScoreR']) AND !$data['Finished'] AND strlen(trim($item['arrowstring']))==$arrNo AND strlen(trim($item['oppArrowstring']))==$arrNo);
            $data['Bye']=($item['tie']==2 or $item['oppTie']==2);

            $end = array();
            $oppEnd = array();
            $endScore = explode("|",$item['setPoints']);
            $oppEndScore = explode("|",$item['oppSetPoints']);
            $running=array(0,0);
            $item['arrowstring']=str_pad($item['arrowstring'], $objParam->arrows*$objParam->ends, ' ', STR_PAD_RIGHT);
            $item['oppArrowstring']=str_pad($item['oppArrowstring'], $objParam->arrows*$objParam->ends, ' ', STR_PAD_RIGHT);
            if($objParam->EvMatchMode) {
                $setAssPoint = explode("|",$item['setPointsByEnd']);
                $oppSetAssPoint = explode("|",$item['oppSetPointsByEnd']);
                for($i=0; $i<$objParam->ends; $i++) {
                    $running[0] += (!empty($setAssPoint[$i]) ? $setAssPoint[$i] : 0);
                    $running[1] += (!empty($oppSetAssPoint[$i]) ? $oppSetAssPoint[$i] : 0);
                    //Regular Scoring
                    $arrValue = DecodeFromString(substr($item['arrowstring'], $i * $objParam->arrows, $objParam->arrows), false);
                    if (!is_array($arrValue)) {
                        $arrValue = array($arrValue);
                    } elseif (count($arrValue) == 0) {
                        $arrValue = array_fill(0, $objParam->arrows, '');
                    }
                    $arrValue = array_map('trim', $arrValue);
                    $oppArrValue = DecodeFromString(substr($item['oppArrowstring'], $i * $objParam->arrows, $objParam->arrows), false);
                    if (!is_array($oppArrValue)) {
                        $oppArrValue = array($oppArrValue);
                    } elseif (count($oppArrValue) == 0) {
                        $oppArrValue = array_fill(0, $objParam->arrows, '');
                    }
                    $oppArrValue = array_map('trim', $oppArrValue);

                    $tmpEnd = array('EndNum' => strval($i + 1), 'EndScore' => (!empty($endScore[$i]) ? $endScore[$i] : 0), 'PointAssigned' => strval((!empty($setAssPoint[$i]) ? $setAssPoint[$i] : 0)), 'RunningScore' => strval($running[0]), 'ShootFirst' => ($item["shootFirst"] & pow(2, $i)) != 0, 'Arrows' => $arrValue);
                    $tmpOppEnd = array('EndNum' => strval($i + 1), 'EndScore' => (!empty($oppEndScore[$i]) ? $oppEndScore[$i] : 0), 'PointAssigned' => strval((!empty($oppSetAssPoint[$i]) ? $oppSetAssPoint[$i] : 0)), 'RunningScore' => strval($running[1]), 'ShootFirst' => ($item["oppShootFirst"] & pow(2, $i)) != 0, 'Arrows' => $oppArrValue);
                    $end[] = $tmpEnd;
                    $oppEnd[] = $tmpOppEnd;
                }
                //Shootoof
                $SoShot = ceil(max(strlen(trim($item['tiebreak'])),strlen(trim($item['oppTiebreak'])))/$objParam->so);
                for($i=0; $i<$SoShot; $i++) {
                    $arrValue = DecodeFromString(str_pad(substr($item['tiebreak'], $i*$objParam->so, $objParam->so),$objParam->so,' ',STR_PAD_RIGHT), false);
                    if (!is_array($arrValue)) {
                        $arrValue = array($arrValue);
                    } elseif (count($arrValue) == 0) {
                        $arrValue = array_fill(0, $objParam->so, '');
                    }
                    $arrValue = array_map('trim', $arrValue);
                    $oppArrValue = DecodeFromString(str_pad(substr($item['oppTiebreak'], $i*$objParam->so, $objParam->so),$objParam->so,' ',STR_PAD_RIGHT), false);
                    if (!is_array($oppArrValue)) {
                        $oppArrValue = array($oppArrValue);
                    } elseif (count($oppArrValue) == 0) {
                        $oppArrValue = array_fill(0, $objParam->so, '');
                    }
                    $oppArrValue = array_map('trim', $oppArrValue);
                    for($filler=$objParam->so; $filler<$objParam->arrows; $filler++) {
                        $arrValue[]='';
                        $oppArrValue[]='';
                    }
                    $end[] = array('EndNum' => 'S.O.'.strval($i+1), 'EndScore' => strval(ValutaArrowString(substr($item['tiebreak'], $i*$objParam->so, $objParam->so))),
                            'PointAssigned' => strval(($SoShot==($i+1) AND $item['tie']) ? 1 : 0), 'RunningScore' => $item['setScore'], 'ShootFirst' => ($item["shootFirst"] & pow(2, $objParam->arrows)) != 0, 'Arrows' => $arrValue);
                    $oppEnd[] = array('EndNum' => 'S.O.'.strval($i+1), 'EndScore' => strval(ValutaArrowString(substr($item['oppTiebreak'], $i*$objParam->so, $objParam->so))),
                            'PointAssigned' => strval(($SoShot==($i+1) AND $item['oppTie']) ? 1 : 0), 'RunningScore' => $item['oppSetScore'], 'ShootFirst' => ($item["oppShootFirst"] & pow(2, $objParam->arrows)) != 0, 'Arrows' => $oppArrValue);
                }
            } else {
                for($i=0; $i<$objParam->ends; $i++) {
                    $running[0] += (!empty($endScore[$i]) ? $endScore[$i] : 0);
                    $running[1] += (!empty($oppEndScore[$i]) ? $oppEndScore[$i] : 0);
                    //Regular Scoring
                    $arrValue = DecodeFromString(substr($item['arrowstring'], $i * $objParam->arrows, $objParam->arrows), false);
                   if (!is_array($arrValue)) {
                        $arrValue = array($arrValue);
                    } elseif (count($arrValue) == 0) {
                        $arrValue = array_fill(0, $objParam->arrows, '');
                    }
                    $arrValue = array_map('trim', $arrValue);
                    $oppArrValue = DecodeFromString(substr($item['oppArrowstring'], $i * $objParam->arrows, $objParam->arrows), false);
                    if (!is_array($oppArrValue)) {
                        $oppArrValue = array($oppArrValue);
                    } elseif (count($oppArrValue) == 0) {
                        $arrValue = array_fill(0, $objParam->arrows, '');
                    }
                    $oppArrValue = array_map('trim', $oppArrValue);
                    $tmpEnd = array('EndNum' => strval($i + 1), 'EndScore' => strval(!empty($endScore[$i]) ? $endScore[$i] : 0), 'RunningScore' => strval($running[0]), 'ShootFirst' => ($item["shootFirst"] & pow(2, $i)) != 0, 'Arrows' => $arrValue);
                    $tmpOppEnd = array('EndNum' => strval($i + 1), 'EndScore' => strval(!empty($oppEndScore[$i]) ? $oppEndScore[$i] : 0), 'RunningScore' => strval($running[1]), 'ShootFirst' => ($item["oppShootFirst"] & pow(2, $i)) != 0, 'Arrows' => $oppArrValue);
                    $end[] = $tmpEnd;
                    $oppEnd[] = $tmpOppEnd;
                }
                for($i=0; $i<ceil(max(strlen(trim($item['tiebreak'])),strlen(trim($item['oppTiebreak'])))/$objParam->so); $i++) {
                    $arrValue = DecodeFromString(str_pad(substr($item['tiebreak'], $i*$objParam->so, $objParam->so),  $objParam->so, ' ', STR_PAD_RIGHT),false);
                    if (!is_array($arrValue)) {
                        $arrValue = array($arrValue);
                    } elseif (count($arrValue) == 0) {
                        $arrValue = array_fill(0, $objParam->so, '');
                    }
                    $arrValue = array_map('trim', $arrValue);
                    $oppArrValue = DecodeFromString(str_pad(substr($item['oppTiebreak'], $i*$objParam->so, $objParam->so),$objParam->so,' ',STR_PAD_RIGHT), false);
                    if (!is_array($oppArrValue)) {
                        $oppArrValue = array($oppArrValue);
                    } elseif (count($oppArrValue) == 0) {
                        $oppArrValue = array_fill(0, $objParam->so, '');
                    }
                    $oppArrValue = array_map('trim', $oppArrValue);
                    for($filler=$objParam->so; $filler<$objParam->arrows; $filler++) {
                        $arrValue[]='';
                        $oppArrValue[]='';
                    }

                    $end[] = array('EndNum' => 'S.O.'.strval($i+1), 'EndScore' => strval(ValutaArrowString(substr($item['tiebreak'], $i*$objParam->so, $objParam->so))),
                            'RunningScore' => strval($running[0]), 'ShootFirst' => ($item["shootFirst"] & pow(2, $objParam->arrows)) != 0, 'Arrows' => $arrValue);
                    $oppEnd[] = array('EndNum' => 'S.O.'.strval($i+1), 'EndScore' => strval(ValutaArrowString(substr($item['oppTiebreak'], $i*$objParam->so, $objParam->so))),
                            'RunningScore' => strval($running[1]), 'ShootFirst' => ($item["oppShootFirst"] & pow(2, $objParam->arrows)) != 0, 'Arrows' => $oppArrValue);
                }
            }
            $data['Details']=array("MatchMode"=>intval($objParam->EvMatchMode), "EndsL"=>$end, "EndsR"=>$oppEnd);

            $list[$item['target']]=$data;
        }
    }

}
ksort($list);
$JSON['data']=array_values($list);

JsonOut($JSON);