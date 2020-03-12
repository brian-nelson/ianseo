<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Lib/Fun_Phases.inc.php');
require_once('Fun_MatchTotal.inc.php');
//require_once('Common/Lib/Fun_Final.local.inc.php');
//require_once('Common/Lib/Fun_Modules.php');

$Event = isset($_REQUEST['Event']) ? $_REQUEST['Event'] : null;
$TeamEvent = isset($_REQUEST['Team']) ? $_REQUEST['Team'] : null;
$Target = isset($_REQUEST['ArrowPosition']);
$Arrow = isset($_REQUEST['Arrow']) ? $_REQUEST['Arrow'] : null;

$JSON=array('error'=>1, 'changed' => 0, 'confirm' => '', 'winner' => 0, 'newSOPossible'=>false);

checkACL(($TeamEvent ? AclTeams : AclIndividuals), AclReadWrite, false);
$isBlocked=($TeamEvent==0 ? IsBlocked(BIT_BLOCK_IND) : IsBlocked(BIT_BLOCK_TEAM));

if(is_null($Event) or is_null($TeamEvent) or is_null($Arrow) or $isBlocked) {
	JsonOut($JSON);
}

//require_once("Common/Obj_Target.php");
//require_once('Common/Fun_FormatText.inc.php');

foreach($Arrow as $MatchId => $SOs) {
	$MainMatch=$MatchId%2 ? $MatchId-1 : $MatchId;
	$JSON['confirm']='confirm['.$MatchId.']';
	foreach($SOs as $isSO=>$Ends) {
		foreach($Ends as $End=>$Arrows) {
			foreach($Arrows as $ArrowIndex => $ArrowValue) {
				//This will load the correct target into CurrentTarget
				$CurrentTarget=array();
				$validData=GetMaxScores($Event, $MatchId, $TeamEvent);
				// Check the arrow value is OK
				if(array_key_exists(strtoupper(GetLetterFromPrint($ArrowValue, $CurrentTarget)) , $validData["Arrows"])) {
					$ArrowLetter = GetLetterFromPrint($ArrowValue, $CurrentTarget);
				} else {
					$ArrowLetter = ' ';
				}
				$ArrowValue=trim($ArrowValue) ? DecodeFromLetter($ArrowLetter) : '';

				// index of arrow
				$obj=getEventArrowsParams($Event, getPhase($MatchId), $TeamEvent);
				if($isSO) {
					$Index = $obj->arrows*$obj->ends + ($obj->so * $End) + $ArrowIndex + 1;
				} else {
					$Index = ($obj->arrows * $End) + $ArrowIndex + 1;
				}

				if(isset($_REQUEST['x'])) {
					// received also arrow position
					$R=3;
					$X=$_REQUEST['x'];
					$Y=$_REQUEST['y'];
					$D=round(sqrt($X*$X + $Y*$Y)-$R,1);


					UpdateArrowPosition($MatchId, $Event, $TeamEvent, $Index, $D, $X, $Y, $R*2);

					if(empty($_REQUEST['noValue'])) {
						$Values=$validData['Arrows'];
						unset($Values['A']);
						uasort($Values, function($a, $b) {
							if($a['size']<$b['size']) return -1;
							if($a['size']>$b['size']) return 1;
							return 0;
						});

						// we need to set the "M" letter here
						$tmp='A';
						foreach($Values as $Letter => $data) {
							//$dist=$data['size']*$validData['TargetSize']*5/($validData['FullSize']);
							if($D<=$data['radius']) {
								$tmp=$Letter;
								break;
							}
						}
						if($tmp!=$ArrowLetter) {
							$JSON['changed']=1;
						}
						$ArrowLetter=$tmp;
						$ArrowValue=DecodeFromLetter($tmp);
					}
				}

				$JSON['p']=array();
				$JSON['t']=array();

				if(!trim($ArrowValue)) {
					// means the arrow has been deleted... needs to delete from positions as well
					DeleteArrowPosition($MatchId, $Event, $TeamEvent, $Index);
					$JSON['p'] = array(
						'id' => 'SvgArrow[' . $MatchId . '][' . $isSO . '][' . $End . '][' . $ArrowIndex . ']',
						'data' => array('X' => -2000, 'Y' => -2000, 'D' => 0, 'R' => 3),
					);
				}

				$IsFinished=UpdateArrowString($MatchId, $Event, $TeamEvent, $ArrowLetter, $Index, $Index);

				// we need to send back the arrow value, the set total, the winner, etc
				$options=array();
				$options['tournament']=$_SESSION['TourId'];
				$options['events']=$Event;
				$options['matchno']=($MatchId%2 ? $MatchId-1 : $MatchId);
				$options['extended']=$Arrows;

				if($TeamEvent) {
					$rank=Obj_RankFactory::create('GridTeam',$options);
				} else {
					$rank=Obj_RankFactory::create('GridInd',$options);
				}
				$rank->read();
				$Data=$rank->getData();

				if(empty($Data['sections'])) {
					JsonOut($JSON);
				}
				$Section=end($Data['sections']);

				if(empty($Section['phases'])) {
					JsonOut($JSON);
				}
				$Phase=end($Section['phases']);

				if(empty($Phase['items'])) {
					JsonOut($JSON);
				}
				$Match=end($Phase['items']);

				$JSON['arrowID']='Arrow['.$MatchId.']['.intval($isSO).']['.$End.']['.$ArrowIndex.']';
				$JSON['arrowValue']=$ArrowValue;
				$JSON['winner']=$Match['winner'] ? 'L' : ($Match['oppWinner'] ? 'R' : '');
                $JSON['newSOPossible'] = (
                    !($Match['winner'] OR $Match['oppWinner']) AND
                    (strlen(trim($Match['tiebreak'])) > 0 AND (strlen(trim($Match['tiebreak']))%$obj->so == 0)) AND
                    (strlen(trim($Match['oppTiebreak'])) > 0 AND (strlen(trim($Match['oppTiebreak']))%$obj->so == 0)) AND
                    (strlen(trim($Match['tiebreak'])) ==strlen(trim($Match['oppTiebreak'])))
                );

                // Left Side
                $Match['setPoints']=array_pad(explode('|', $Match['setPoints']), $obj->ends,'');
                $Match['setPointsByEnd']=array_pad(explode('|', $Match['setPointsByEnd']), $obj->ends,'');
                $Match['tiebreakDecoded']=array_pad(explode(',', $Match['tiebreakDecoded']), 3,'');
                // Right side
                $Match['oppSetPoints']=array_pad(explode('|', $Match['oppSetPoints']), $obj->ends,'');
                $Match['oppSetPointsByEnd']=array_pad(explode('|', $Match['oppSetPointsByEnd']), $obj->ends,'');
                $Match['oppTiebreakDecoded']=array_pad(explode(',', $Match['oppTiebreakDecoded']), 3,'');

                $soEnds=ceil(min(strlen(trim($Match['tiebreak'])), strlen(trim($Match['oppTiebreak'])))/$obj->so);
                $TotL=0;
                $TotR=0;
//				if($MatchId%2) {
    				if($isSO) {
						for($i=0;$i<$soEnds;$i++) {
							$JSON['t'][]=array(
								'id' => 'EndTotalR_SO_'.$i,
								'val' => $Match['oppTiebreakDecoded'][$i],
							);
						}
						if(isset($_REQUEST['x'])) {
							$JSON['p']=array(
								'id'=>'SvgArrow['.$MatchId.'][1]['.$End.']['.$ArrowIndex.']',
								'data'=> $Match['oppTiePosition'][$Index - $obj->arrows*$obj->ends -1],
							);
						}
					} else {
						if(isset($_REQUEST['x'])) {
							$JSON['p']=array(
								'id'=>'SvgArrow['.$MatchId.'][0]['.$End.']['.$ArrowIndex.']',
								'data'=> $Match['oppArrowPosition'][$Index - 1],
							);
						}
					}
					$JSON['t'][]=array(
						'id' => 'EndSetR_SO',
						'val' => $Match['oppSetScore'],
					);
//				} else {

					if($isSO) {
						for($i=0;$i<$soEnds;$i++) {
							$JSON['t'][]=array(
								'id' => 'EndTotalL_SO_'.$i,
								'val' => $Match['tiebreakDecoded'][$i],
							);
						}
						if(isset($_REQUEST['x'])) {
							$JSON['p']=array(
								'id'=>'SvgArrow['.$MatchId.'][1]['.$End.']['.$ArrowIndex.']',
								'data'=> $Match['tiePosition'][$Index - $obj->arrows*$obj->ends -1],
								);
						}
					} else {
						if(isset($_REQUEST['x'])) {
							$JSON['p']=array(
								'id'=>'SvgArrow['.$MatchId.'][0]['.$End.']['.$ArrowIndex.']',
								'data'=> $Match['arrowPosition'][$Index - 1],
								);
						}
					}
					$JSON['t'][]=array(
						'id' => 'EndSetL_SO',
						'val' => $Match['setScore'],
					);
			//	}
                for($i=0;$i<$obj->ends;$i++) {
                    if($Section['meta']['matchMode']) {
                        $TotL+=($Match['setPointsByEnd'][$i] ? $Match['setPointsByEnd'][$i] : 0);
                        $TotR+=($Match['oppSetPointsByEnd'][$i] ? $Match['oppSetPointsByEnd'][$i] : 0);
                    } else {
                        $TotL+=($Match['setPoints'][$i] ? $Match['setPoints'][$i] : 0);
                        $TotR+=($Match['oppSetPoints'][$i] ? $Match['oppSetPoints'][$i] : 0);
                    }
                    $JSON['t'][]=array(
                        'id' => 'EndTotalR_'.$i,
                        'val' => $Match['oppSetPoints'][$i],
                    );
                    $JSON['t'][]=array(
                        'id' => 'EndTotalL_'.$i,
                        'val' => $Match['setPoints'][$i],
                    );
                    $JSON['t'][]=array(
                        'id' => 'EndSetL_'.$i,
                        'val' => $Match['setPointsByEnd'][$i],
                    );
                    $JSON['t'][]=array(
                        'id' => 'EndSetR_'.$i,
                        'val' => $Match['oppSetPointsByEnd'][$i],
                    );
                    $JSON['t'][]=array(
                        'id' => 'TotalL_'.$i,
                        'val' => $TotL,
                    );
                    $JSON['t'][]=array(
                        'id' => 'TotalR_'.$i,
                        'val' => $TotR,
                    );
                }


                $JSON['error']=0;
				//$JSON['winner']=($Match['winner'] or $Match['oppWinner']);
			}
		}
	}
}

JsonOut($JSON);