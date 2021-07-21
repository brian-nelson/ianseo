<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Lib/CommonLib.php');

$JSON=array('error' => 1, 'time' => '');

if(!CheckTourSession()) {
	JsonOut($JSON);
}

$TourId=$_SESSION['TourId'];
if (!isset($_REQUEST['time']) or checkACL(array((empty($_REQUEST['Team']) ? AclIndividuals:AclTeams), AclOutput),AclNoAccess, false, $TourId)==AclNoAccess) {
	JsonOut($JSON);
}

$JSON['error']=0;
if(empty($_REQUEST['time'])) {
    $_REQUEST['time'] = '0000-00-00 00:00:00';
}

// start checking if there is a modification
$q=safe_r_sql("(Select FinDateTime LastUpdate from Finals where FinTournament={$TourId} and FinDateTime>'{$_REQUEST['time']}')"
	. " UNION "
	. "(Select TfDateTime LastUpdate from TeamFinals where TfTournament={$TourId} and TfDateTime>'{$_REQUEST['time']}')"
	. " Order by LastUpdate desc"
	. " limit 1");
if(!($r=safe_fetch($q)) or $r->LastUpdate<=$_REQUEST['time']) {
	// no new things
	JsonOut($JSON);
}

$JSON['time']=$r->LastUpdate;
$JSON['Event']='';
$JSON['OppLeft']='';
$JSON['OppRight']='';
$JSON['TgtLeft']='';
$JSON['TgtRight']='';
$JSON['ScoreLeft']='';
$JSON['ScoreRight']='';
$JSON['UpdateL']=0;
$JSON['UpdateR']=0;
$JSON['IdL']=0;
$JSON['IdR']=0;
$JSON['WinnerL']=false;
$JSON['WinnerR']=false;
$JSON['TgtSize']=0;
$JSON['AthL'] = [];
$JSON['AthR'] = [];

// get what needs to be checked
$MatchNo=((isset($_REQUEST['MatchNo']) AND intval($_REQUEST['MatchNo'])>=0) ? intval($_REQUEST['MatchNo']/2)*2 : -1);
$Event=(empty($_REQUEST['Event']) ? '' : $_REQUEST['Event']);
$Team=(isset($_REQUEST['Team']) ? intval($_REQUEST['Team']) : -1);
$Lock=(isset($_REQUEST['Lock']) ? intval($_REQUEST['Lock']) : 0);
$TourId=(isset($_REQUEST['TourId']) ? intval($_REQUEST['TourId']) : $_SESSION['TourId']);
$LiveExists=false;
$Live=false;


if ($x=getMatchLive($TourId)) {
	if(!$Lock) {
		$Event=$x->Event;
		$MatchNo=$x->MatchNo;
		$Team=$x->Team;
    }
	$Live=($Event==$x->Event and $MatchNo==$x->MatchNo and $Team==$x->Team);
	$LiveExists=(!$Live and $Lock);
}

if(!$Event or $MatchNo<0 or $Team<0) {
	$JSON['Event']=get_text('NoLiveEvent');
	JsonOut($JSON);
}

$JSON['EvCode']=$Event;
$JSON['MatchNo']=intval($MatchNo);
$JSON['EvTeam']=intval($Team);
$JSON['LiveExists']=boolval($LiveExists);

$options = array(
    'tournament' => $TourId,
    'matchno' => $MatchNo,
    'events' => $Event,
    'extended' => true,
);
if($Team) {
	$rank = Obj_RankFactory::create('GridTeam', $options);
} else {
	$rank = Obj_RankFactory::create('GridInd', $options);
}

$rank->read();
$rankData=$rank->getData();

if(!$rankData['sections']) {
    $JSON['Event']=get_text('NoLiveEvent');
    JsonOut($JSON);
}

$JSON['time']=$rankData['meta']['lastUpdate'];

require_once("Common/Obj_Target.php");

$Section=end($rankData['sections']);
$Phase=end($Section['phases']);
$Match=end($Phase['items']);

if($Match['lastUpdated']>$_REQUEST['time']) {
    $JSON['UpdateL']=1;
}
if($Match['oppLastUpdated']>$_REQUEST['time']) {
    $JSON['UpdateR'] = 1;
}
$JSON['Phase']=intval(key($Section['phases']));

$JSON['IdL']=$Match[($Team ? 'countryCode':'bib')];
$JSON['IdR']=$Match[($Team ? 'oppCountryCode':'oppBib')];

// check if we are running SO or normal match
$IsSO=false;

// actual ends number is not that important in case of SO!
$NumEnds=$Phase['meta']['FinElimChooser'] ? $Section['meta']['elimEnds'] : $Section['meta']['finEnds'];

if(trim($Match['tiebreak']) or trim($Match['oppTiebreak'])) {
    $IsSO=true;
    $NumArrows=$Phase['meta']['FinElimChooser'] ? $Section['meta']['elimSO'] : $Section['meta']['finSO'];
    $ArrowstrinL=strlen(trim($Match['tiebreak']));
    $ArrowstrinR=strlen(trim($Match['oppTiebreak']));
    $ObjL='tiebreak';
    $ObjR='oppTiebreak';
    $PosL='tiePosition';
    $PosR='oppTiePosition';
} else {
    $NumArrows=$Phase['meta']['FinElimChooser'] ? $Section['meta']['elimArrows'] : $Section['meta']['finArrows'];
    $ArrowstrinL=strlen(trim($Match['arrowstring']));
    $ArrowstrinR=strlen(trim($Match['oppArrowstring']));
    $ObjL='arrowstring';
    $ObjR='oppArrowstring';
    $PosL='arrowPosition';
    $PosR='oppArrowPosition';
}

$EndL=ceil(max($ArrowstrinL, $ArrowstrinR)/$NumArrows);
$EndR=$EndL;
if($Match['status']==3 and $Match['oppStatus']==3 and $Match['winner']==0 and $Match['oppWinner']==0) {
    $EndL++;
    $JSON['UpdateL']=1;
    $JSON['UpdateR'] = 1;
    $EndR++;
    $JSON['UpdateL']=1;
    $JSON['UpdateR'] = 1;
}

if(!$EndL) $EndL++;
if(!$EndR) $EndR++;

if($EndL>$NumEnds and $EndR>$NumEnds) {
    // double check we are in a SO situation...
    $IsSO=true;
    $NumArrows=$Phase['meta']['FinElimChooser'] ? $Section['meta']['elimSO'] : $Section['meta']['finSO'];
    $ArrowstrinL=strlen(trim($Match['tiebreak']));
    $ArrowstrinR=strlen(trim($Match['oppTiebreak']));
    $ObjL='tiebreak';
    $ObjR='oppTiebreak';
    $PosL='tiePosition';
    $PosR='oppTiePosition';

    $EndL=ceil(max($ArrowstrinL, $ArrowstrinR)/$NumArrows);
    $EndR=$EndL;

    if(!$EndL or $Match['status']==3) {
        $EndL++;
    }
    if(!$EndR or $Match['oppStatus']==3) {
        $EndR++;
    }
}

if($IsSO) {
    $EndL=-$EndL;
    $EndR=-$EndR;
}

$JSON['Event']=$Section['meta']['eventName'].'<br/>'.$Phase['meta']['matchName'];

if($Team) {
	$JSON['OppLeft']=$Match['countryName'].get_flag_ianseo($Match['countryCode'], 0, '', $_SESSION['TourCode']);
	$JSON['OppRight']=$Match['oppCountryName'].get_flag_ianseo($Match['oppCountryCode'], 0, '', $_SESSION['TourCode']);
} else {
	$JSON['OppLeft']= $Match['fullName'] . ' - '.$Match['countryCode'].get_flag_ianseo($Match['countryCode'], 0, '', $_SESSION['TourCode']);
	$JSON['OppRight']=$Match['oppFullName'] . ' - '.$Match['oppCountryCode'].get_flag_ianseo($Match['oppCountryCode'], 0, '', $_SESSION['TourCode']);
}

$ArrL=substr($Match[$ObjL], $IndexL=$NumArrows*(abs($EndL)-1), $NumArrows);
$ArrR=substr($Match[$ObjR], $IndexR=$NumArrows*(abs($EndR)-1), $NumArrows);

$JSON['ScoreLeft']='<div class="badge badge-danger">'.($EndL<0 ? 'SO '.abs($EndL) : 'End '.$EndL).'</div>';
$JSON['ScoreRight']='<div class="badge badge-danger">'.($EndR<0 ? 'SO '.abs($EndR) : 'End '.$EndR).'</div>';

$TotL=ValutaArrowString($ArrL);
$TotR=ValutaArrowString($ArrR);
//$ShowDistance=($IsSO and strlen(trim($ArrL))==strlen(trim($ArrR)) and strlen(trim($ArrL))==$NumArrows and $TotL==$TotR);

foreach(DecodeFromString(str_pad($ArrL, $NumArrows, ' ', STR_PAD_RIGHT), false, true) as $k => $Point) {
    $JSON['ScoreLeft'].='<div class="badge badge-primary">'.$Point.(($IsSO and !empty($Match[$PosL][$IndexL+$k])) ? ' ('.$Match[$PosL][$IndexL+$k]['D'].')' : '').'</div>';
}
$JSON['ScoreLeft'].='<div class="badge badge-info">'.$TotL.(($IsSO AND $Match['closest']!=0 AND $k==($NumArrows-1)) ? '+':'').'</div>';
if(!$IsSO) {
    $JSON['ScoreLeft'].='<div class="badge badge-secondary total">'.($Section['meta']['matchMode'] ? $Match['setScore'] : $Match['score']).'</div>';
}

foreach(DecodeFromString(str_pad($ArrR, $NumArrows, ' ', STR_PAD_RIGHT), false, true) as $k => $Point) {
    $JSON['ScoreRight'].='<div class="badge badge-primary">'.$Point.(($IsSO and !empty($Match[$PosR][$IndexR+$k])) ? ' ('.$Match[$PosR][$IndexR+$k]['D'].')' : '').'</div>';
}
$JSON['ScoreRight'].='<div class="badge badge-info">'.$TotR.(($IsSO AND $Match['oppClosest']!=0 AND $k==($NumArrows-1)) ? '+':'').'</div>';
if(!$IsSO) {
    $JSON['ScoreRight'] .= '<div class="badge badge-secondary total">' . ($Section['meta']['matchMode'] ? $Match['oppSetScore'] : $Match['oppScore']) . '</div>';
}

$JSON['WinnerL'] = ($Match['winner']==1);
$JSON['WinnerR'] = ($Match['oppWinner']==1);

if($Team) {
    foreach ($Section['athletes'][$Match['teamId']][$Match['subTeam']] as $ath) {
        $JSON['AthL'][] = array("Id"=>$ath['code'], "Ath"=>$ath['fullName']);
    }
    foreach ($Section['athletes'][$Match['oppTeamId']][$Match['oppSubTeam']] as $ath) {
        $JSON['AthR'][] = array("Id"=>$ath['code'], "Ath"=>$ath['fullName']);
    }
} else {
    $JSON['AthL'][] = array("Id"=>$Match['bib'], "Ath"=>$Match['fullName']);
    $JSON['AthR'][] = array("Id"=>$Match['oppBib'], "Ath"=>$Match['oppFullName']);
}

switch ($_REQUEST["View"]) {
    case 'Scorecard':

        $cols= $Section['meta'][($Phase['meta']['FinElimChooser'] ? 'elimArrows':'finArrows')];
        $rows  = $Section['meta'][($Phase['meta']['FinElimChooser'] ? 'elimEnds':'finEnds')];
        $so = $Section['meta'][($Phase['meta']['FinElimChooser'] ? 'elimSO':'finSO')];
        $matchMode = $Section['meta']['matchMode'];

        $JSON['TgtLeft'] = '<table class="table table-bordered table-sm mt-2"><thead class="table-dark"><tr><th scope="col"></th>';
        for ($i=1; $i<=$cols; $i++) {
            $JSON['TgtLeft'] .= '<th scope="col" class="text-center">'.$i.'</th>';
        }
        $JSON['TgtLeft'] .= '<th scope="col" class="text-center">'.get_text('EndScore').'</th><th scope="col" class="text-center">'.($matchMode ? get_text('SetPoints', 'Tournament') : get_text('Total')).'</th></tr></thead>';
        $JSON['TgtRight'] = $JSON['TgtLeft'];

        $arrString = str_pad($Match['arrowstring'],$rows*$cols," ",STR_PAD_RIGHT);
        $oppString = str_pad($Match['oppArrowstring'],$rows*$cols," ",STR_PAD_RIGHT);
        $lenSo = max(strlen(trim($Match['tiebreak'])),strlen(trim($Match['oppTiebreak'])));
        $arrSo = str_pad($Match['tiebreak'],$lenSo," ",STR_PAD_RIGHT);
        $oppSo = str_pad($Match['oppTiebreak'],$lenSo," ",STR_PAD_RIGHT);

        $athEnds = explode('|', $Match['setPoints']);
        $oppEnds = explode('|', $Match['oppSetPoints']);
        $athSets = explode('|', $Match['setPointsByEnd']);
        $oppSets = explode('|', $Match['oppSetPointsByEnd']);
        $athRunning=0;
        $oppRunning=0;
        for($r=0; $r<$rows; $r++) {
            $JSON['TgtLeft'] .= '<tr><th scope="row" class="table-dark text-center">'.($r+1).'</th>';
            $JSON['TgtRight'] .= '<tr><th scope="row" class="table-dark text-center">'.($r+1).'</th>';
            for($c=0; $c<$cols; $c++) {
                $JSON['TgtLeft'] .= '<td class="text-center whiteBg">'.DecodeFromLetter($arrString[($r*$cols)+$c]).'</td>';
                $JSON['TgtRight'] .= '<td class="text-center whiteBg">'.DecodeFromLetter($oppString[($r*$cols)+$c]).'</td>';

            }
            $athRunning += ($matchMode ? (empty($athSets[$r]) ? 0 : $athSets[$r]) : (empty($athEnds[$r]) ? 0 : $athEnds[$r]));
            $oppRunning += ($matchMode ? (empty($oppSets[$r]) ? 0 : $oppSets[$r]) : (empty($oppEnds[$r]) ? 0 : $oppEnds[$r]));
            $JSON['TgtLeft'] .= '<td class="text-right table-warning">'. (empty($athEnds[$r]) ? '' : $athEnds[$r]).'</td><td class="text-right font-weight-bold table-info">'.$athRunning.'</td></tr>';
            $JSON['TgtRight'] .= '<td class="text-right table-warning">'.(empty($oppEnds[$r]) ? '' : $oppEnds[$r]).'</td><td class="text-right font-weight-bold table-info">'.$oppRunning.'</td></tr>';
        }
        for($r=0; $r<max(ceil($lenSo/$so),1); $r++) {
            $JSON['TgtLeft'] .= '<tr><th scope="row" class="table-dark text-center">'.($lenSo ? get_text('ShotOffShort', 'Tournament') . ' ' . ($r+1) : '&nbsp;').'</th>';
            $JSON['TgtRight'] .= '<tr><th scope="row" class="table-dark text-center">'.($lenSo ? get_text('ShotOffShort', 'Tournament') . ' ' . ($r+1) : '&nbsp;').'</th>';
            if($lenSo) {
                for ($c = 0; $c < $so; $c++) {
                    $JSON['TgtLeft'] .= '<td class="text-center whiteBg">' . DecodeFromLetter($arrSo[($r * $so) + $c]) . '</td>';
                    $JSON['TgtRight'] .= '<td class="text-center whiteBg">' . DecodeFromLetter($oppSo[($r * $so) + $c]) . '</td>';
                }
                if ($so < $cols) {
                    $JSON['TgtLeft'] .= '<td class="text-center whiteBg closestText" colspan="' . ($cols - $so) . '">' . ($Match['closest']!=0 ? '+':'&nbsp;') . '</td>';
                    $JSON['TgtRight'] .= '<td class="text-center whiteBg closestText" colspan="' . ($cols - $so) . '">' . ($Match['oppClosest']!=0 ? '+':'&nbsp;') . '</td>';
                }
                $JSON['TgtLeft'] .= '<td class="text-right table-warning">' . ValutaArrowString(substr($arrSo, ($r * $so), $so)) . '</td>';
                $JSON['TgtRight'] .= '<td class="text-right table-warning">' . ValutaArrowString(substr($oppSo, ($r * $so), $so)) . '</td>';
            } else {
                $JSON['TgtLeft'] .= '<td class="text-center whiteBg" colspan="' . ($cols+1) . '">&nbsp;</td>';
                $JSON['TgtRight'] .= '<td class="text-center whiteBg" colspan="' . ($cols+1) . '">&nbsp;</td>';
            }
            if($r==0) {
                $JSON['TgtLeft'] .= '<td class="text-right font-weight-bold table-info align-middle" rowspan="'.max(ceil($lenSo/$so),1).'">' . ($matchMode ? $Match['setScore'] : $Match['score']) . '</td>';
                $JSON['TgtRight'] .= '<td class="text-right font-weight-bold table-info align-middle" rowspan="'.max(ceil($lenSo/$so),1).'">' . ($matchMode ? $Match['oppSetScore'] : $Match['oppScore']) . '</td>';
            }
            $JSON['TgtLeft'] .= '</tr>';
            $JSON['TgtRight'] .= '</tr>';

        }
        $JSON['TgtLeft'] .= '</table>';
        $JSON['TgtRight'] .= '</table>';


        break;
    case 'Presentation':
        $options['extended'] = false;
        unset($options['matchno']);
        if($Team) {
            //Left Team
            $options['coid'] = $Match['teamId'];
            $rank = Obj_RankFactory::create('GridTeam', $options);
            $rank->read();
            $rankData=$rank->getData();
            $JSON['TgtLeft'] = '<div id="picsL" class="d-flex justify-content-center">';
            foreach ($rankData['sections'][$options['events']]['athletes'][$Match['teamId']][$Match['subTeam']] as $ath) {
                $JSON['TgtLeft'] .= '<figure class="figure m-2">' .
                    get_photo_ianseo($ath['id'], '', '', 'class="figure-img rounded" style="width: 8vw;"', true, $_SESSION['TourCode']) .
                    '<figcaption class="figure-caption text-center"  style="width: 10vw; overflow-x: fragments">'.$ath['fullName'].'</figcaption>' .
                    '</figure>';
            }
            $JSON['TgtLeft'] .= '</div>';
            $JSON['TgtLeft'] .= '<div class="text-left">' .
                '<ul><li>' . get_text('QualRound') . ': ' . $Match['qualScore'] . ' - #&nbsp;' . $Match['qualRank'] . '</li>';
            foreach ($rankData['sections'][$options['events']]['phases'] as $kPh => $vPh) {
                if($MatchNo >= $vPh['items'][0]['matchNo']) {
                    continue;
                }
                $JSON['TgtLeft'] .= '<li>'.$vPh['meta']['phaseName'].': ';
                if(($vPh['items'][0]['saved'] OR $vPh['items'][0]['oppSaved']) AND ($vPh['items'][0]['tie']==2 OR $vPh['items'][0]['oppTie']==2) AND ($vPh['items'][0]['teamId']==0 OR $vPh['items'][0]['oppTeamId']==0)) {
                    $JSON['TgtLeft'] .= '<span class="small font-italic">'.$rankData['meta']['saved'].'</span>';
                } else if($vPh['items'][0]['tie']==2 OR $vPh['items'][0]['oppTie']==2) {
                    if($vPh['items'][0]['teamId']==0 OR $vPh['items'][0]['oppTeamId']==0) {
                        $JSON['TgtLeft'] .= '<span class="small font-italic">' . get_text('Bye') . '</span>';
                    } else {
                        $JSON['TgtLeft'] .= '<span class="'.($vPh['items'][0]['winner'] ? 'font-weight-bold':'').'">' .
                            $vPh['items'][0]['countryName'] . '</span> (<span class="font-italic">' .
                            ' <span class="small">' . (($vPh['items'][0]['tie']==2) ? get_text('Bye'):$vPh['items'][0]['notes']).'</span>'.
                            ' - ' .
                            ' <span class="small">' . (($vPh['items'][0]['oppTie']==2) ? get_text('Bye'):$vPh['items'][0]['oppNotes']).'</span>'.
                            '</span>) <span class="'.($vPh['items'][0]['oppWinner'] ? 'font-weight-bold':'').'">' . $vPh['items'][0]['oppCountryName'] .
                            '</span>';
                    }
                } else {
                    $JSON['TgtLeft'] .= '<span class="'.($vPh['items'][0]['winner'] ? 'font-weight-bold':'').'">' .
                        $vPh['items'][0]['countryName'] . '</span> (<span class="font-italic">' .
                        ($rankData['sections'][$options['events']]['meta']['matchMode'] ? $vPh['items'][0]['setScore'] : $vPh['items'][0]['score']) .
                        (($vPh['items'][0]['tie']==1 OR $vPh['items'][0]['oppTie']==1) ? ' <span class="small">T.'.$vPh['items'][0]['tiebreakDecoded'] .'</span>':'').
                        ' - ' .
                        ($rankData['sections'][$options['events']]['meta']['matchMode'] ? $vPh['items'][0]['oppSetScore'] : $vPh['items'][0]['oppScore']).
                        (($vPh['items'][0]['tie']==1 OR $vPh['items'][0]['oppTie']==1) ? ' <span class="small">T.'.$vPh['items'][0]['oppTiebreakDecoded'].'</span>':'').
                        '</span>) <span class="'.($vPh['items'][0]['oppWinner'] ? 'font-weight-bold':'').'">' . $vPh['items'][0]['oppCountryName'] .
                        '</span>';
                }
                $JSON['TgtLeft'] .= '</li>';
            }
            if($vPh['items'][0]['irm']) {
	            $JSON['TgtLeft'] .= '<li><span class="font-weight-bold">'.$vPh['items'][0]['irmText'].'</span></li>';
            }
            $JSON['TgtLeft'] .= '</ul>' .
                '</div>';
            //Right Team
            $options['coid'] = $Match['oppTeamId'];
            $rank = Obj_RankFactory::create('GridTeam', $options);
            $rank->read();
            $rankData=$rank->getData();
            $JSON['TgtRight'] = '<div id="picsR" class="d-flex justify-content-center"">';
            foreach ($rankData['sections'][$options['events']]['athletes'][$Match['oppTeamId']][$Match['oppSubTeam']] as $ath) {
                $JSON['TgtRight'] .= '<figure class="figure m-2">' .
                    get_photo_ianseo($ath['id'], '', '', 'class="figure-img rounded" style="width: 8vw;"', true, $_SESSION['TourCode']) .
                    '<figcaption class="figure-caption text-center" style="width: 10vw; overflow-x: fragments">'.$ath['fullName'].'</figcaption>' .
                    '</figure>';
            }
            $JSON['TgtRight'] .= '</div>';
            $JSON['TgtRight'] .= '<div class="text-left">' .
                '<ul><li>' . get_text('QualRound') . ': ' . $Match['oppQualScore'] . ' - #&nbsp;' . $Match['oppQualRank'] . '</li>';
            foreach ($rankData['sections'][$options['events']]['phases'] as $kPh => $vPh) {
                if($MatchNo >= $vPh['items'][0]['matchNo']) {
                    continue;
                }
                $JSON['TgtRight'] .= '<li>'.$vPh['meta']['phaseName'].': ';
                if(($vPh['items'][0]['saved'] OR $vPh['items'][0]['oppSaved']) AND ($vPh['items'][0]['tie']==2 OR $vPh['items'][0]['oppTie']==2) AND ($vPh['items'][0]['teamId']==0 OR $vPh['items'][0]['oppTeamId']==0)) {
                    $JSON['TgtRight'] .= '<span class="small font-italic">'.$rankData['meta']['saved'].'</span>';
                } else if($vPh['items'][0]['tie']==2 OR $vPh['items'][0]['oppTie']==2) {
                    if($vPh['items'][0]['teamId']==0 OR $vPh['items'][0]['oppTeamId']==0) {
                        $JSON['TgtRight'] .= '<span class="small font-italic">' . get_text('Bye') . '</span>';
                    } else {
                        $JSON['TgtRight'] .= '<span class="'.($vPh['items'][0]['winner'] ? 'font-weight-bold':'').'">' .
                            $vPh['items'][0]['countryName'] . '</span> (<span class="font-italic">' .
                            ' <span class="small">' . (($vPh['items'][0]['tie']==2) ? get_text('Bye'):$vPh['items'][0]['notes']).'</span>'.
                            ' - ' .
                            ' <span class="small">' . (($vPh['items'][0]['oppTie']==2) ? get_text('Bye'):$vPh['items'][0]['oppNotes']).'</span>'.
                            '</span>) <span class="'.($vPh['items'][0]['oppWinner'] ? 'font-weight-bold':'').'">' . $vPh['items'][0]['oppCountryName'] .
                            '</span>';
                    }
                } else {
                    $JSON['TgtRight'] .= '<span class="'.($vPh['items'][0]['winner'] ? 'font-weight-bold':'').'">' .
                        $vPh['items'][0]['countryName'] . '</span> (<span class="font-italic">' .
                        ($rankData['sections'][$options['events']]['meta']['matchMode'] ? $vPh['items'][0]['setScore'] : $vPh['items'][0]['score']) .
                        (($vPh['items'][0]['tie']==1 OR $vPh['items'][0]['oppTie']==1) ? ' <span class="small">T.'.$vPh['items'][0]['tiebreakDecoded'] .'</span>':'').
                        ' - ' .
                        ($rankData['sections'][$options['events']]['meta']['matchMode'] ? $vPh['items'][0]['oppSetScore'] : $vPh['items'][0]['oppScore']).
                        (($vPh['items'][0]['tie']==1 OR $vPh['items'][0]['oppTie']==1) ? ' <span class="small">T.'.$vPh['items'][0]['oppTiebreakDecoded'].'</span>':'').
                        '</span>) <span class="'.($vPh['items'][0]['oppWinner'] ? 'font-weight-bold':'').'">' . $vPh['items'][0]['oppCountryName'] .
                        '</span>';
                }
                $JSON['TgtRight'] .= '</li>';
            }
            $JSON['TgtRight'] .= '</ul>' .
                '</div>';

        } else {
        //Left Archer
            $options['enid'] = $Match['id'];
            $rank = Obj_RankFactory::create('GridInd', $options);
            $rank->read();
            $rankData=$rank->getData();
            $JSON['TgtLeft'] = '<div id="picsL" class="d-flex justify-content-center"><figure class="figure m-2">' .
                get_photo_ianseo($Match['id'], 150, '', 'class="figure-img rounded"', true, $_SESSION['TourCode']) .
                '<figcaption class="figure-caption text-center">'.$Match['fullName'].'</figcaption>' .
                '</figure></div>' .
                '<div class="text-left">' .
                '<ul><li>' . get_text('QualRound') . ': ' . $Match['qualScore'] . ' - #&nbsp;' . $Match['qualRank'] . '</li>';
            foreach ($rankData['sections'][$options['events']]['phases'] as $kPh => $vPh) {
                if($MatchNo >= $vPh['items'][0]['matchNo']) {
                    continue;
                }
                $JSON['TgtLeft'] .= '<li>'.$vPh['meta']['phaseName'].': ';
                if(($vPh['items'][0]['saved'] OR $vPh['items'][0]['oppSaved']) AND ($vPh['items'][0]['tie']==2 OR $vPh['items'][0]['oppTie']==2) AND ($vPh['items'][0]['id']==0 OR $vPh['items'][0]['oppId']==0)) {
                    $JSON['TgtLeft'] .= '<span class="small font-italic">'.$rankData['meta']['saved'].'</span>';
                } else if($vPh['items'][0]['tie']==2 OR $vPh['items'][0]['oppTie']==2) {
                    if($vPh['items'][0]['id']==0 OR $vPh['items'][0]['oppId']==0) {
                        $JSON['TgtLeft'] .= '<span class="small font-italic">' . get_text('Bye') . '</span>';
                    } else {
                        $JSON['TgtLeft'] .= '<span class="'.($vPh['items'][0]['winner'] ? 'font-weight-bold':'').'">' .
                            $vPh['items'][0]['countryCode'] . ' - ' . $vPh['items'][0]['fullName'] . '</span> (<span class="font-italic">' .
                            ' <span class="small">' . (($vPh['items'][0]['tie']==2) ? get_text('Bye'):$vPh['items'][0]['notes']).'</span>'.
                            ' - ' .
                            ' <span class="small">' . (($vPh['items'][0]['oppTie']==2) ? get_text('Bye'):$vPh['items'][0]['oppNotes']).'</span>'.
                            '</span>) <span class="'.($vPh['items'][0]['oppWinner'] ? 'font-weight-bold':'').'">' . $vPh['items'][0]['oppFullName'] . ' - '.  $vPh['items'][0]['oppCountryCode'] .
                            '</span>';
                    }
                } else {
                    $JSON['TgtLeft'] .= '<span class="'.($vPh['items'][0]['winner'] ? 'font-weight-bold':'').'">' .
                        $vPh['items'][0]['countryCode'] . ' - ' . $vPh['items'][0]['fullName'] . '</span> (<span class="font-italic">' .
                        ($rankData['sections'][$options['events']]['meta']['matchMode'] ? $vPh['items'][0]['setScore'] : $vPh['items'][0]['score']) .
                        (($vPh['items'][0]['tie']==1 OR $vPh['items'][0]['oppTie']==1) ? ' <span class="small">T.'.$vPh['items'][0]['tiebreakDecoded'] .'</span>':'').
                        ' - ' .
                        ($rankData['sections'][$options['events']]['meta']['matchMode'] ? $vPh['items'][0]['oppSetScore'] : $vPh['items'][0]['oppScore']).
                        (($vPh['items'][0]['tie']==1 OR $vPh['items'][0]['oppTie']==1) ? ' <span class="small">T.'.$vPh['items'][0]['oppTiebreakDecoded'].'</span>':'').
                        '</span>) <span class="'.($vPh['items'][0]['oppWinner'] ? 'font-weight-bold':'').'">' . $vPh['items'][0]['oppFullName'] . ' - '.  $vPh['items'][0]['oppCountryCode'] .
                        '</span>';
                }
                $JSON['TgtLeft'] .= '</li>';
            }
            $JSON['TgtLeft'] .= '</ul>' .
                '</div>';

        //Right Archer
            $options['enid'] = $Match['oppId'];
            $rank = Obj_RankFactory::create('GridInd', $options);
            $rank->read();
            $rankData=$rank->getData();
            $JSON['TgtRight'] = '<div id="picsR" class="d-flex justify-content-center"><figure class="figure m-2">' .
                get_photo_ianseo($Match['oppId'], 150, '', 'class="figure-img rounded"', true, $_SESSION['TourCode']) .
                '<figcaption class="figure-caption text-center">'.$Match['oppFullName'].'</figcaption>' .
                '</figure></div>' .
                '<div class="text-left">' .

                '<ul><li>' . get_text('QualRound') . ': ' . $Match['oppQualScore'] . ' - #&nbsp;' . $Match['oppQualRank'] . '</li>';
            foreach ($rankData['sections'][$options['events']]['phases'] as $kPh => $vPh) {
                if($MatchNo >= $vPh['items'][0]['matchNo']) {
                    continue;
                }
                $JSON['TgtRight'] .= '<li>'.$vPh['meta']['phaseName'].': ';
                if(($vPh['items'][0]['saved'] OR $vPh['items'][0]['oppSaved']) AND ($vPh['items'][0]['tie']==2 OR $vPh['items'][0]['oppTie']==2) AND ($vPh['items'][0]['id']==0 OR $vPh['items'][0]['oppId']==0)) {
                    $JSON['TgtRight'] .= '<span class="small font-italic">' . $rankData['meta']['saved'] . '</span>';
                } else if($vPh['items'][0]['tie']==2 OR $vPh['items'][0]['oppTie']==2) {
                    if($vPh['items'][0]['id']==0 OR $vPh['items'][0]['oppId']==0) {
                        $JSON['TgtRight'] .= '<span class="small font-italic">' . get_text('Bye') . '</span>';
                    } else {
                        $JSON['TgtRight'] .= '<span class="'.($vPh['items'][0]['winner'] ? 'font-weight-bold':'').'">' .
                            $vPh['items'][0]['countryCode'] . ' - ' . $vPh['items'][0]['fullName']  . '</span> (<span class="font-italic">' .
                            ' <span class="small">' . (($vPh['items'][0]['tie']==2) ? get_text('Bye'):$vPh['items'][0]['notes']).'</span>'.
                            ' - ' .
                            ' <span class="small">' . (($vPh['items'][0]['oppTie']==2) ? get_text('Bye'):$vPh['items'][0]['oppNotes']).'</span>'.
                            '</span>) <span class="'.($vPh['items'][0]['oppWinner'] ? 'font-weight-bold':'').'">' . $vPh['items'][0]['oppFullName'] . ' - '.  $vPh['items'][0]['oppCountryCode'] .
                            '</span>';
                    }
                } else {
                    $JSON['TgtRight'] .= '<span class="'.($vPh['items'][0]['winner'] ? 'font-weight-bold':'').'">' .
                        $vPh['items'][0]['countryCode'] . ' - ' . $vPh['items'][0]['fullName'] . '</span> (<span class="font-italic">' .
                        ($rankData['sections'][$options['events']]['meta']['matchMode'] ? $vPh['items'][0]['setScore'] : $vPh['items'][0]['score']) .
                        (($vPh['items'][0]['tie']==1 OR $vPh['items'][0]['oppTie']==1) ? ' <span class="small">T.'.$vPh['items'][0]['tiebreakDecoded'] .'</span>':'').
                        ' - ' .
                        ($rankData['sections'][$options['events']]['meta']['matchMode'] ? $vPh['items'][0]['oppSetScore'] : $vPh['items'][0]['oppScore']).
                        (($vPh['items'][0]['tie']==1 OR $vPh['items'][0]['oppTie']==1) ? ' <span class="small">T.'.$vPh['items'][0]['oppTiebreakDecoded'].'</span>':'').
                        '</span>) <span class="'.($vPh['items'][0]['oppWinner'] ? 'font-weight-bold':'').'">' . $vPh['items'][0]['oppFullName'] . ' - '.  $vPh['items'][0]['oppCountryCode'] .
                        '</span>';

                }
                $JSON['TgtRight'] .= '</li>';
            }
            $JSON['TgtRight'] .= '</ul>' .
                '</div>';
            }
        break;
    case 'Ceremony':
        $Ceremonies=array('','');
        require_once('sub-Ceremonies.php');
        $JSON['TgtLeft'] = $Ceremonies[0];
        $JSON['TgtRight'] = $Ceremonies[1];
        break;



    case 'Target':
        $target = new Obj_Target();

    // we already have most of the data needed for the target!
        $target->initSVG($TourId, $Event, $MatchNo, $Team);
        $target->setSVGHeader('', '');
        $target->setTarget();

        // get the arrow timing, assuming it is the last one
        $lastTime='';
        $LastArrow='';

        $q=safe_r_sql("select date_sub(now(), interval 10 minute) as CurrentDateTime");
        $r=safe_fetch($q);

        if($Match['lastUpdated']>$r->CurrentDateTime) {
	        $q=safe_r_sql("select * from FinOdfTiming where FinOdfEvent='$Event' and FinOdfTeamEvent=$Team and FinOdfMatchno in ($MatchNo, ".($MatchNo+1).") and FinOdfTournament={$_SESSION['TourId']} ");
	        while($r=safe_fetch($q)) {
	            if($r->FinOdfArrows) {
	                $ar=json_decode($r->FinOdfArrows, true);
	                if(!is_array($ar)) {
	                    $ar=array($ar);
	                }
	                $ar=end($ar);
	                if($ar['Ts']>$lastTime) {
				        $lastTime=$ar['Ts'];
				        $LastArrow=($r->FinOdfMatchno==$MatchNo ? 'L' : 'R');
			        }

		        }
	        }
        }

        $JSON['TgtSize'] = $target->Diameter;
        $JSON['TgtZoom'] = round(sqrt($target->TargetRadius) / 7, 1);

        $arrowsL=array();
        $arrowsR=array();
        foreach(range($IndexL, $IndexL+$NumArrows-1) as $i) {
        	if(isset($Match[$PosL][$i])) {
	            $arrowsL[]=$Match[$PosL][$i];
	        }
        	if(isset($Match[$PosR][$i])) {
	            $arrowsR[]=$Match[$PosR][$i];
	        }
        }
        $target->drawSVGArrows($arrowsL, true, $LastArrow=='L');
        $JSON['TgtLeft'] = $target->OutputStringSVG();

        $target->drawSVGArrows($arrowsR, true, $LastArrow=='R');
        $JSON['TgtRight'] = $target->OutputStringSVG();
        $JSON['LastArrow'] = $LastArrow;
}

JsonOut($JSON);
