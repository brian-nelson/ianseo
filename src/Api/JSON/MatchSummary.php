<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Obj_RankFactory.php');

$TourId = 0;
$TourCode = '';
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
	$TourId = getIdFromCode($_REQUEST['CompCode']);
	$TourCode = preg_replace('/[^a-z0-9_-]+/i','', $_REQUEST['CompCode']);
}

$EvType = -1;
if(isset($_REQUEST['Type']) && preg_match("/^[01]$/", $_REQUEST['Type'])) {
	$EvType = $_REQUEST['Type'];
}

$EvCode = '....';
if(isset($_REQUEST['Event']) && preg_match("/^[a-z0-9_-]+$/i", $_REQUEST['Event'])) {
	$EvCode = $_REQUEST['Event'];
}

$MatchId = -1;
if(isset($_REQUEST['MatchId']) && preg_match("/^[0-9]+$/", $_REQUEST['MatchId'])) {
	$MatchId = $_REQUEST['MatchId'];
}

$showArrowsPosition = false;
if(isset($_REQUEST['ArrowPosition']) && preg_match("/^[01]$/", $_REQUEST['ArrowPosition'])) {
    $showArrowsPosition  = ($_REQUEST['ArrowPosition']==1);
}

$translateX = true;
if(isset($_REQUEST['rawX']) && preg_match("/^[01]$/", $_REQUEST['rawX'])) {
    $translateX  = ($_REQUEST['rawX']!=1);
}


$FullFlag=0;
if(!empty($_REQUEST['FullFlag'])) {
	if(file_exists($CasparConfig=$CFG->DOCUMENT_PATH.'/Modules/Caspar/config.php')) {
		require_once($CasparConfig);
	}
	$FullFlag=1;
}

$json_array=array();

$options['tournament']=$TourId;
$options['events']=$EvCode;
$options['matchno']=$MatchId;
$options['extended']=$showArrowsPosition;

$BasePosition=array('X' => -1000,
	'Y' => -1000,
	'R' => 0,
	'D' => 0,
	'pD' => 0,
	'pA' => 0,
	);

$rank=null;
if($EvType) {
	$rank=Obj_RankFactory::create('GridTeam',$options);
} else {
	$rank=Obj_RankFactory::create('GridInd',$options);
}
$rank->read();
$Data=$rank->getData();

foreach($Data['sections'] as $kSec=>$vSec) {
	if(!empty($vSec['phases'])) {
		foreach($vSec['phases'] as $kPh=>$vPh) {
			$json_array = Array("Event"=>$EvCode, "Type"=>$EvType, "MatchId"=>$MatchId, "MatchLive"=>false, "MatchFinished"=>false, "MatchRunningEnd"=>"0", "MatchRunningEndSO"=>"0", "MatchConfirmed"=>false, "ScoreCanChange"=>0);
			$objParam=getEventArrowsParams($kSec, $kPh, $EvType, $TourId);
			$json_array['EventName'] = $vSec['meta']['eventName'];
			$json_array['Phase'] = $vPh['meta']['phaseName'];
			$json_array['PhaseName'] = $vPh['meta']['matchName'];
			$json_array['Mode'] = Array("ScoringMode"=>($vSec["meta"]["matchMode"]==1 ? "S" : "C"), "Arrows"=>strval($objParam->arrows), "Ends"=>strval($objParam->ends), "ShootoffArrows"=>strval($objParam->so));
			foreach($vPh['items'] as $kItem=>$vItem) {
				$json_array['MatchFinished'] = ($vItem['winner'] OR $vItem['oppWinner']);
				$json_array['MatchConfirmed'] = ($vItem['status']==1 AND  $vItem['oppStatus']==1);
				$json_array["MatchLive"] = ($vItem['liveFlag'] ? true : false);
				$tmpL = array();
				$tmpR = array();
				if($EvType==0) {
					$tmpL += array("Id"=>$vItem["bib"], "FamilyName"=>$vItem["familyName"], "GivenName"=>$vItem["givenName"], "NameOrder"=>$vItem["nameOrder"], "Gender"=>$vItem["gender"]);
					$tmpR += array("Id"=>$vItem["oppBib"], "FamilyName"=>$vItem["oppFamilyName"], "GivenName"=>$vItem["oppGivenName"], "NameOrder"=>$vItem["oppNameOrder"], "Gender"=>$vItem["oppGender"]);
				}
				$tmpL += array("TeamCode"=>$vItem["countryCode"], "TeamName"=>$vItem["countryName"]);
				$tmpR += array("TeamCode"=>$vItem["oppCountryCode"], "TeamName"=>$vItem["oppCountryName"]);
				$tmpL += array("EndConfirmed"=>($vItem['status']==3 || $vItem['status']==1), "Winner"=>($vItem["winner"]? true:false) , 'ToWin' => '', 'Score'=>($vSec['meta']['matchMode'] ? $vItem['setScore'] : $vItem['score']));
				$tmpR += array("EndConfirmed"=>($vItem['oppStatus']==3 || $vItem['oppStatus']==1), "Winner"=>($vItem["oppWinner"]? true:false), 'ToWin' => '', 'Score'=>($vSec['meta']['matchMode'] ? $vItem['oppSetScore'] : $vItem['oppScore']));
                $tmpL += array("PositionAvailable"=>boolval($vItem['arrowpositionAvailable']));
                $tmpR += array("PositionAvailable"=>boolval($vItem['oppArrowpositionAvailable']));
                $tmpL += array("ClosestToCenter"=>boolval($vItem['closest']));
                $tmpR += array("ClosestToCenter"=>boolval($vItem['oppClosest']));

                $tmpL['FullFlag']='';
                $tmpR['FullFlag']='';
                if($FullFlag) {
	                if(file_exists($CFG->DOCUMENT_PATH.($img='TV/Photos/'.$TourCode.'-FlSvg-'.$vItem["countryCode"].'.svg'))) {
		                $f=file_get_contents($CFG->DOCUMENT_PATH.$img);
		                $xmlget = simplexml_load_string($f);
		                $xmlattributes = $xmlget->attributes();
		                if(isset($xmlattributes->width)) {
			                $Viewbox='0 0 '.$xmlattributes->width.' '.$xmlattributes->height;
		                } else {
			                $Viewbox=$xmlattributes->viewBox;
		                }
		                $svg= '<svg x="0" y="0" height="100%" viewBox="'.$Viewbox.'" preserveAspectRatio="xMidYMid meet">';
		                //$svg.= $f;
		                $svg.= '<image xlink:href="'.$Sets['ianseo'].$img.'" />';
		                $svg.= '</svg>';
		                $tmpL['FullFlag']=$svg;
	                } elseif(is_file($CFG->DOCUMENT_PATH.($img='TV/Photos/'.$TourCode.'-Fl-'.$vItem["countryCode"].'.jpg'))) {
		                $svg= '<svg x="0" y="0" height="100%" width="100%" preserveAspectRatio="xMidYMid meet">';
		                $svg.= '<image xlink:href="'.$Sets['ianseo'].$img.'" height="100%" />';
		                $svg.= '</svg>';
		                $tmpL['FullFlag']=$svg;
	                }
	                if(file_exists($CFG->DOCUMENT_PATH.($img='TV/Photos/'.$TourCode.'-FlSvg-'.$vItem["oppCountryCode"].'.svg'))) {
		                $f=file_get_contents($CFG->DOCUMENT_PATH.$img);
		                $xmlget = simplexml_load_string($f);
		                $xmlattributes = $xmlget->attributes();
		                if(isset($xmlattributes->width)) {
			                $Viewbox='0 0 '.$xmlattributes->width.' '.$xmlattributes->height;
		                } else {
			                $Viewbox=$xmlattributes->viewBox;
		                }
		                $svg= '<svg x="0" y="0" height="100%" viewBox="'.$Viewbox.'" preserveAspectRatio="xMidYMid meet">';
		                //$svg.= $f;
		                $svg.= '<image xlink:href="'.$Sets['ianseo'].$img.'" />';
		                $svg.= '</svg>';
		                $tmpR['FullFlag']=$svg;
	                } elseif(is_file($CFG->DOCUMENT_PATH.($img='TV/Photos/'.$TourCode.'-Fl-'.$vItem["oppCountryCode"].'.jpg'))) {
		                $svg= '<svg x="0" y="0" height="100%" width="100%" preserveAspectRatio="xMidYMid meet">';
		                $svg.= '<image xlink:href="'.$Sets['ianseo'].$img.'" height="100%" />';
		                $svg.= '</svg>';
		                $tmpR['FullFlag']=$svg;
	                }
                }

                $end = array();
				$oppEnd = array();
				$endScore = explode("|",$vItem['setPoints']);
				$oppEndScore = explode("|",$vItem['oppSetPoints']);
				$running=array(0,0);
				$runningEnd = 0;
                $runningEndSo = 1;
				$vItem['arrowstring']=str_pad($vItem['arrowstring'], $objParam->arrows*$objParam->ends, ' ', STR_PAD_RIGHT);
				$vItem['oppArrowstring']=str_pad($vItem['oppArrowstring'], $objParam->arrows*$objParam->ends, ' ', STR_PAD_RIGHT);
				if($vSec['meta']['matchMode']) {
					$setAssPoint = explode("|",$vItem['setPointsByEnd']);
					$oppSetAssPoint = explode("|",$vItem['oppSetPointsByEnd']);
					for($i=0; $i<$objParam->ends; $i++) {
                        $running[0] += (!empty($setAssPoint[$i]) ? $setAssPoint[$i] : 0);
                        $running[1] += (!empty($oppSetAssPoint[$i]) ? $oppSetAssPoint[$i] : 0);
                        //Regular Scoring
                        $arrValue = DecodeFromString(substr($vItem['arrowstring'], $i * $objParam->arrows, $objParam->arrows), false);
                        $regExp = '';
                        $pointStar = (!empty($endScore[$i]) ? $endScore[$i] : 0);
                        $pointRaiseStar = $pointStar;
                        if (!ctype_upper(substr($vItem['arrowstring'], $i * $objParam->arrows, $objParam->arrows))) {
                            $pointRaiseStar += RaiseStars(substr($vItem['arrowstring'], $i * $objParam->arrows, $objParam->arrows), $regExp, $EvCode, $EvType, $TourId);
                        }
                        if (!is_array($arrValue)) {
                            $arrValue = array($arrValue);
                        } elseif (count($arrValue) == 0) {
                            $arrValue = array_fill(0, $objParam->arrows, '');
                        }
                        $arrValue = array_map('trim', $arrValue);
                        for ($aPtr = 0; $aPtr < count($arrValue); $aPtr++) {
                            if ($arrValue[$aPtr] == 'X' AND $translateX) {
                                $arrValue[$aPtr] = '10';
                            }
                        }
                        $oppArrValue = DecodeFromString(substr($vItem['oppArrowstring'], $i * $objParam->arrows, $objParam->arrows), false);
                        if (!is_array($oppArrValue)) {
                            $oppArrValue = array($oppArrValue);
                        } elseif (count($oppArrValue) == 0) {
                            $oppArrValue = array_fill(0, $objParam->arrows, '');
                        }
                        $oppArrValue = array_map('trim', $oppArrValue);
                        for ($aPtr = 0; $aPtr < count($oppArrValue); $aPtr++) {
                            if ($oppArrValue[$aPtr] == 'X' AND $translateX) {
                                $oppArrValue[$aPtr] = '10';
                            }
                        }
                        $oppPointStar = (!empty($oppEndScore[$i]) ? $oppEndScore[$i] : 0);
                        $oppPointRaiseStar = $oppPointStar;
                        if (!ctype_upper(substr($vItem['oppArrowstring'], $i * $objParam->arrows, $objParam->arrows))) {
                            $oppPointRaiseStar += RaiseStars(substr($vItem['oppArrowstring'], $i * $objParam->arrows, $objParam->arrows), $regExp, $EvCode, $EvType, $TourId);
                        }
                        $tmpEnd = array('EndNum' => strval($i + 1), 'EndScore' => (!empty($endScore[$i]) ? $endScore[$i] : 0), 'PointAssigned' => strval((!empty($setAssPoint[$i]) ? $setAssPoint[$i] : 0)), 'RunningScore' => strval($running[0]), 'ShootFirst' => ($vItem["shootFirst"] & pow(2, $i)) != 0, 'Arrows' => $arrValue);
                        $tmpOppEnd = array('EndNum' => strval($i + 1), 'EndScore' => (!empty($oppEndScore[$i]) ? $oppEndScore[$i] : 0), 'PointAssigned' => strval((!empty($oppSetAssPoint[$i]) ? $oppSetAssPoint[$i] : 0)), 'RunningScore' => strval($running[1]), 'ShootFirst' => ($vItem["oppShootFirst"] & pow(2, $i)) != 0, 'Arrows' => $oppArrValue);
                        if ($showArrowsPosition) {
	                        foreach (range($i * $objParam->arrows, $i * $objParam->arrows + $objParam->arrows -1) as $j) {
		                        if (isset($vItem['arrowPosition'][$j])) {
			                        $vPos = $vItem['arrowPosition'][$j];
	                                $vPos["pD"] = round($vPos['D'] + $vPos['R'],1);
	                                if ($vPos['X'] == 0 AND $vPos['Y'] == 0) {
	                                    $vPos["pA"] = 0;
	                                } elseif ($vPos['X'] < 0) {
	                                    $vPos["pA"] = round(rad2deg(atan2($vPos['X'],$vPos['Y']))+360,1);      // TRANSPOSED !! y,x params
	                                } else{
	                                    $vPos["pA"] = round(rad2deg(atan2($vPos['X'],$vPos['Y'])),1);
	                                }
                                    $tmpEnd['Positions'][] = $vPos;
		                        //} else {
			                     //   $vPos=$BasePosition;
		                        }

		                        if (isset($vItem['oppArrowPosition'][$j])) {
			                        $vPos = $vItem['oppArrowPosition'][$j];
	                                $vPos["pD"] = round($vPos['D'] + $vPos['R'],1);
	                                if ($vPos['X'] == 0 AND $vPos['Y'] == 0) {
	                                    $vPos["pA"] = 0;
	                                } elseif ($vPos['X'] < 0) {
	                                    $vPos["pA"] = round(rad2deg(atan2($vPos['X'],$vPos['Y']))+360,1);      // TRANSPOSED !! y,x params
	                                } else{
	                                    $vPos["pA"] = round(rad2deg(atan2($vPos['X'],$vPos['Y'])),1);
	                                }
		                            $tmpOppEnd['Positions'][] = $vPos;
		                        //} else {
			                        //$vPos=$BasePosition;
		                        }

	                        }
                        }
                        $end[] = $tmpEnd;
                        $oppEnd[] = $tmpOppEnd;

                        if (!empty($endScore[$i]) || !empty($oppEndScore[$i])) {
                            if (strpos(substr($vItem['arrowstring'], $i * $objParam->arrows, $objParam->arrows), ' ') || strpos(substr($vItem['oppArrowstring'], $i * $objParam->arrows, $objParam->arrows), ' ')) {
                                $runningEnd = $i;
                            } else {
                                $runningEnd = $i + 1;
                            }
                        }
                        if (($pointStar != $pointRaiseStar) || ($oppPointStar != $oppPointRaiseStar)) {
                            if (($pointStar == $oppPointStar && ($pointStar < $oppPointRaiseStar or $pointRaiseStar > $oppPointStar)) or ($pointStar > $oppPointStar && $pointStar <= $oppPointRaiseStar) || ($pointStar < $oppPointStar && $pointRaiseStar >= $oppPointStar)) {
                                $tmp = 0;
                                if ($pointStar != $pointRaiseStar) $tmp += 1;
                                if ($oppPointStar != $oppPointRaiseStar) $tmp += 2;
                                $json_array["ScoreCanChange"] = $tmp;
                            }
                        }
                    }
                    //Shootoof
                    $SoShot = ceil(max(strlen(trim($vItem['tiebreak'])),strlen(trim($vItem['oppTiebreak'])))/$objParam->so);
                    for($i=0; $i<$SoShot; $i++) {
                        $arrValue = DecodeFromString(str_pad(substr($vItem['tiebreak'], $i*$objParam->so, $objParam->so),$objParam->so,' ',STR_PAD_RIGHT), false);
                        if (!is_array($arrValue)) {
                            $arrValue = array($arrValue);
                        } elseif (count($arrValue) == 0) {
                            $arrValue = array_fill(0, $objParam->so, '');
                        }
                        $arrValue = array_map('trim', $arrValue);
                        for ($aPtr = 0; $aPtr < count($arrValue); $aPtr++) {
                            if ($arrValue[$aPtr] == 'X'  AND $translateX) {
                                $arrValue[$aPtr] = '10';
                            }
                        }
                        $oppArrValue = DecodeFromString(str_pad(substr($vItem['oppTiebreak'], $i*$objParam->so, $objParam->so),$objParam->so,' ',STR_PAD_RIGHT), false);
                        if (!is_array($oppArrValue)) {
                            $oppArrValue = array($oppArrValue);
                        } elseif (count($oppArrValue) == 0) {
                            $oppArrValue = array_fill(0, $objParam->so, '');
                        }
                        $oppArrValue = array_map('trim', $oppArrValue);
                        for ($aPtr = 0; $aPtr < count($oppArrValue); $aPtr++) {
                            if ($oppArrValue[$aPtr] == 'X' AND $translateX) {
                                $oppArrValue[$aPtr] = '10';
                            }
                        }
                        if ($vItem['tiebreakDecoded'] == 'X' AND $translateX) {
                            $vItem['tiebreakDecoded'] = '10';
                        }
                        if ($vItem['oppTiebreakDecoded'] == 'X' AND $translateX) {
                            $vItem['oppTiebreakDecoded'] = '10';
                        }
                        $tmpPos=array();
                        $tmpOppPos=array();
                        if ($showArrowsPosition) {
                            foreach (array_slice($vItem['tiePosition'], $i * $objParam->so, $objParam->so) as $vPos) {
                                $vPos["pD"] = round($vPos['D'] + $vPos['R'],1);
                                if ($vPos['X'] == 0 AND $vPos['Y'] == 0) {
                                    $vPos["pA"] = 0;
                                } elseif ($vPos['X'] < 0) {
                                    $vPos["pA"] = round(rad2deg(atan2($vPos['X'],$vPos['Y']))+360,1);      // TRANSPOSED !! y,x params
                                } else{
                                    $vPos["pA"] = round(rad2deg(atan2($vPos['X'],$vPos['Y'])),1);
                                }
                                $tmpPos[] = $vPos;
                            }
                            foreach (array_slice($vItem['oppTiePosition'], $i * $objParam->so, $objParam->so) as $vPos) {
                                $vPos["pD"] = round($vPos['D'] + $vPos['R'],1);
                                if ($vPos['X'] == 0 AND $vPos['Y'] == 0) {
                                    $vPos["pA"] = 0;
                                } elseif ($vPos['X'] < 0) {
                                    $vPos["pA"] = round(rad2deg(atan2($vPos['X'],$vPos['Y']))+360,1);      // TRANSPOSED !! y,x params
                                } else{
                                    $vPos["pA"] = round(rad2deg(atan2($vPos['X'],$vPos['Y'])),1);
                                }
                                $tmpOppPos[] = $vPos;
                            }
                        }

                        $end[] = array('EndNum' => 'SO', 'SONum'=>strval($i+1), 'EndScore' => strval(ValutaArrowString(substr($vItem['tiebreak'], $i*$objParam->so, $objParam->so))),
                            'PointAssigned' => strval(($SoShot==($i+1) AND $vItem['tie']) ? 1 : 0), 'RunningScore' => $vItem['setScore'], 'ShootFirst' => ($vItem["shootFirst"] & pow(2, $objParam->arrows)) != 0, 'Arrows' => $arrValue) +
                            ($showArrowsPosition ? array("Positions"=>$tmpPos) : array());
                        $oppEnd[] = array('EndNum' => 'SO', 'SONum'=>strval($i+1), 'EndScore' => strval(ValutaArrowString(substr($vItem['oppTiebreak'], $i*$objParam->so, $objParam->so))),
                            'PointAssigned' => strval(($SoShot==($i+1) AND $vItem['oppTie']) ? 1 : 0), 'RunningScore' => $vItem['oppSetScore'], 'ShootFirst' => ($vItem["oppShootFirst"] & pow(2, $objParam->arrows)) != 0, 'Arrows' => $oppArrValue) +
                            ($showArrowsPosition ? array("Positions"=>$tmpOppPos) : array());

                        if (strlen(str_replace(' ', '', substr($vItem['tiebreak'], $i*$objParam->so, $objParam->so)))== $objParam->so AND strlen(str_replace(' ', '', substr($vItem['oppTiebreak'], $i*$objParam->so, $objParam->so)))== $objParam->so) {
                            $runningEndSo = $i + 2;
                        } else {
                            $runningEndSo = $i + 1;
                        }
                    }
				} else {
					for($i=0; $i<$objParam->ends; $i++) {
                        $running[0] += (!empty($endScore[$i]) ? $endScore[$i] : 0);
                        $running[1] += (!empty($oppEndScore[$i]) ? $oppEndScore[$i] : 0);
                        //Regular Scoring
                        $arrValue = DecodeFromString(substr($vItem['arrowstring'], $i * $objParam->arrows, $objParam->arrows), false);
                        $regExp = '';
                        $pointStar = $running[0];
                        $pointRaiseStar = $pointStar;
                        if (!ctype_upper(substr($vItem['arrowstring'], $i * $objParam->arrows, $objParam->arrows))) {
                            $pointRaiseStar += RaiseStars(substr($vItem['arrowstring'], $i * $objParam->arrows, $objParam->arrows), $regExp, $EvCode, $EvType, $TourId);
                        }
                        if (!is_array($arrValue)) {
                            $arrValue = array($arrValue);
                        } elseif (count($arrValue) == 0) {
                            $arrValue = array_fill(0, $objParam->arrows, '');
                        }
                        $arrValue = array_map('trim', $arrValue);
                        for ($aPtr = 0; $aPtr < count($arrValue); $aPtr++) {
                            if ($arrValue[$aPtr] == 'X'  AND $translateX) {
                                $arrValue[$aPtr] = '10';
                            }
                        }
                        $oppArrValue = DecodeFromString(substr($vItem['oppArrowstring'], $i * $objParam->arrows, $objParam->arrows), false);

                        $oppPointStar = $running[1];
                        $oppPointRaiseStar = $oppPointStar;
                        if (!ctype_upper(substr($vItem['oppArrowstring'], $i * $objParam->arrows, $objParam->arrows))) {
                            $oppPointRaiseStar += RaiseStars(substr($vItem['oppArrowstring'], $i * $objParam->arrows, $objParam->arrows), $regExp, $EvCode, $EvType, $TourId);
                        }
                        if (!is_array($oppArrValue)) {
                            $oppArrValue = array($oppArrValue);
                        } elseif (count($oppArrValue) == 0) {
                            $arrValue = array_fill(0, $objParam->arrows, '');
                        }
                        $oppArrValue = array_map('trim', $oppArrValue);
                        for ($aPtr = 0; $aPtr < count($oppArrValue); $aPtr++) {
                            if ($oppArrValue[$aPtr] == 'X' AND $translateX) {
                                $oppArrValue[$aPtr] = '10';
                            }
                        }
                        $tmpEnd = array('EndNum' => strval($i + 1), 'EndScore' => strval(!empty($endScore[$i]) ? $endScore[$i] : 0), 'RunningScore' => strval($running[0]), 'ShootFirst' => ($vItem["shootFirst"] & pow(2, $i)) != 0, 'Arrows' => $arrValue);
                        $tmpOppEnd = array('EndNum' => strval($i + 1), 'EndScore' => strval(!empty($oppEndScore[$i]) ? $oppEndScore[$i] : 0), 'RunningScore' => strval($running[1]), 'ShootFirst' => ($vItem["oppShootFirst"] & pow(2, $i)) != 0, 'Arrows' => $oppArrValue);
                        if ($showArrowsPosition) {
                            foreach (range($i * $objParam->arrows, $i * $objParam->arrows + $objParam->arrows -1) as $j) {
                            	if(isset($vItem['arrowPosition'][$j])) {
	                                $vPos=$vItem['arrowPosition'][$j];
	                                $vPos["pD"] = round($vPos['D'] + $vPos['R'],1);
	                                if ($vPos['X'] == 0 AND $vPos['Y'] == 0) {
	                                    $vPos["pA"] = 0;
	                                } elseif ($vPos['X'] < 0) {
	                                    $vPos["pA"] = round(rad2deg(atan2($vPos['X'],$vPos['Y']))+360,1);      // TRANSPOSED !! y,x params
	                                } else{
	                                    $vPos["pA"] = round(rad2deg(atan2($vPos['X'],$vPos['Y'])),1);
	                                }
                                    $tmpEnd['Positions'][] = $vPos;
	                            //} else {
	                            //    $vPos=$BasePosition;
	                            }

                            	if(isset($vItem['oppArrowPosition'][$j])) {
	                                $vPos=$vItem['oppArrowPosition'][$j];
	                                $vPos["pD"] = round($vPos['D'] + $vPos['R'],1);
	                                if ($vPos['X'] == 0 AND $vPos['Y'] == 0) {
	                                    $vPos["pA"] = 0;
	                                } elseif ($vPos['X'] < 0) {
	                                    $vPos["pA"] = round(rad2deg(atan2($vPos['X'],$vPos['Y']))+360,1);      // TRANSPOSED !! y,x params
	                                } else{
	                                    $vPos["pA"] = round(rad2deg(atan2($vPos['X'],$vPos['Y'])),1);
	                                }
                                    $tmpOppEnd['Positions'][] = $vPos;
	                            //} else {
		                         //   $vPos = $BasePosition;
	                            }
                            }
                        }
                        $end[] = $tmpEnd;
                        $oppEnd[] = $tmpOppEnd;

                        if (!empty($endScore[$i]) || !empty($oppEndScore[$i])) {
                            if (strpos(substr($vItem['arrowstring'], $i * $objParam->arrows, $objParam->arrows), ' ') !== false or strpos(substr($vItem['oppArrowstring'], $i * $objParam->arrows, $objParam->arrows), ' ') !== false) {
                                $runningEnd = $i;
                            } else {
                                $runningEnd = $i + 1;
                            }
                        }

                        if (($pointStar != $pointRaiseStar) || ($oppPointStar != $oppPointRaiseStar)) {
                            if (($pointStar > $oppPointStar && $pointStar <= $oppPointRaiseStar) || ($pointStar < $oppPointStar && $pointRaiseStar >= $oppPointStar) || ($pointStar == $oppPointStar && ($pointStar != $oppPointRaiseStar || $pointRaiseStar != $oppPointStar))) {
                                $tmp = 0;
                                if ($pointStar != $pointRaiseStar) $tmp += 1;
                                if ($oppPointStar != $oppPointRaiseStar) $tmp += 2;
                                $json_array["ScoreCanChange"] = $tmp;
                            }
                        }
                    }
                    for($i=0; $i<ceil(max(strlen(trim($vItem['tiebreak'])),strlen(trim($vItem['oppTiebreak'])))/$objParam->so); $i++) {
					    $arrValue = DecodeFromString(str_pad(substr($vItem['tiebreak'], $i*$objParam->so, $objParam->so),  $objParam->so, ' ', STR_PAD_RIGHT),false);
                        if (!is_array($arrValue)) {
                            $arrValue = array($arrValue);
                        } elseif (count($arrValue) == 0) {
                            $arrValue = array_fill(0, $objParam->so, '');
                        }
                        $arrValue = array_map('trim', $arrValue);
                        for ($aPtr = 0; $aPtr < count($arrValue); $aPtr++) {
                            if ($arrValue[$aPtr] == 'X' AND $translateX) {
                                $arrValue[$aPtr] = '10';
                            }
                        }
                        $oppArrValue = DecodeFromString(str_pad(substr($vItem['oppTiebreak'], $i*$objParam->so, $objParam->so),$objParam->so,' ',STR_PAD_RIGHT), false);
                        if (!is_array($oppArrValue)) {
                            $oppArrValue = array($oppArrValue);
                        } elseif (count($oppArrValue) == 0) {
                            $oppArrValue = array_fill(0, $objParam->so, '');
                        }
                        $oppArrValue = array_map('trim', $oppArrValue);
                        for ($aPtr = 0; $aPtr < count($oppArrValue); $aPtr++) {
                            if ($oppArrValue[$aPtr] == 'X' AND $translateX) {
                                $oppArrValue[$aPtr] = '10';
                            }
                        }
                        if ($vItem['tiebreakDecoded'] == 'X' AND $translateX) {
                            $vItem['tiebreakDecoded'] = '10';
                        }
                        if ($vItem['oppTiebreakDecoded'] == 'X' AND $translateX) {
                            $vItem['oppTiebreakDecoded'] = '10';
                        }
                        $tmpPos=array();
                        $tmpOppPos=array();
                        if ($showArrowsPosition) {
                            foreach (array_slice($vItem['tiePosition'], $i * $objParam->so, $objParam->so) as $vPos) {
                                $vPos["pD"] = round($vPos['D'] + $vPos['R'],1);
                                if ($vPos['X'] == 0 AND $vPos['Y'] == 0) {
                                    $vPos["pA"] = 0;
                                } elseif ($vPos['X'] < 0) {
                                    $vPos["pA"] = round(rad2deg(atan2($vPos['X'],$vPos['Y']))+360,1);      // TRANSPOSED !! y,x params
                                } else{
                                    $vPos["pA"] = round(rad2deg(atan2($vPos['X'],$vPos['Y'])),1);
                                }
                                $tmpPos[] = $vPos;
                            }
                            foreach (array_slice($vItem['oppTiePosition'], $i * $objParam->so, $objParam->so) as $vPos) {
                                $vPos["pD"] = round($vPos['D'] + $vPos['R'],1);
                                if ($vPos['X'] == 0 AND $vPos['Y'] == 0) {
                                    $vPos["pA"] = 0;
                                } elseif ($vPos['X'] < 0) {
                                    $vPos["pA"] = round(rad2deg(atan2($vPos['X'],$vPos['Y']))+360,1);      // TRANSPOSED !! y,x params
                                } else{
                                    $vPos["pA"] = round(rad2deg(atan2($vPos['X'],$vPos['Y'])),1);
                                }
                                $tmpOppPos[] = $vPos;
                            }
                        }

                        $end[] = array('EndNum' => 'SO', 'SONum'=>strval($i+1), 'EndScore' => strval(ValutaArrowString(substr($vItem['tiebreak'], $i*$objParam->so, $objParam->so))),
                            'RunningScore' => strval($running[0]), 'ShootFirst' => ($vItem["shootFirst"] & pow(2, $objParam->arrows)) != 0, 'Arrows' => $arrValue) +
                            ($showArrowsPosition ? array("Positions"=>$tmpPos) : array());
                        $oppEnd[] = array('EndNum' => 'SO', 'SONum'=>strval($i+1), 'EndScore' => strval(ValutaArrowString(substr($vItem['oppTiebreak'], $i*$objParam->so, $objParam->so))),
                            'RunningScore' => strval($running[1]), 'ShootFirst' => ($vItem["oppShootFirst"] & pow(2, $objParam->arrows)) != 0, 'Arrows' => $oppArrValue) +
                            ($showArrowsPosition ? array("Positions"=>$tmpOppPos) : array());
                        if (strlen(str_replace(' ', '', substr($vItem['tiebreak'], $i*$objParam->so, $objParam->so)))== $objParam->so AND strlen(str_replace(' ', '', substr($vItem['oppTiebreak'], $i*$objParam->so, $objParam->so)))== $objParam->so) {
                            $runningEndSo = $i + 2;
                        } else {
                            $runningEndSo = $i + 1;
                        }
                    }
				}

				$IsSO=false;
				if($runningEnd < $objParam->ends) {
					$endOrg0=strlen(rtrim($vItem['arrowstring']));
					$endOrg1=strlen(rtrim($vItem['oppArrowstring']));
					$NumArrows=$objParam->ends*$objParam->arrows;
				} else {
					$IsSO=true;
					$endOrg0=strlen(rtrim($vItem['tiebreak']));
					$endOrg1=strlen(rtrim($vItem['oppTiebreak']));
                    $NumArrows = ((strlen(rtrim($vItem['tiebreak'])) OR strlen(rtrim($vItem['oppTiebreak']))) ? $vSec['meta']['finSO']*ceil(max(strlen(rtrim($vItem['tiebreak'])),strlen(rtrim($vItem['oppTiebreak'])))/$vSec['meta']['finSO']):$vSec['meta']['finSO']);
				}
				// X to win appears if
				// * cumulative
				// - 1 arrow left to shoot
				// * set system
				// - 1 point to win the match
				if(abs( $dif = $endOrg0-$endOrg1 )==1
						and ($endOrg0==$NumArrows
								or $endOrg1==$NumArrows
								or ($vSec['meta']['matchMode']
										and (strlen(rtrim(substr($vItem['arrowstring'], $runningEnd*$objParam->arrows, $objParam->arrows)))==$objParam->arrows or strlen(rtrim(substr($vItem['oppArrowstring'], $runningEnd*$objParam->arrows, $objParam->arrows)))==$objParam->arrows)
										and ($vItem['oppSetScore']>=$objParam->ends-1
												or $vItem['setScore']>=$objParam->ends-1)))) {
					if(!$IsSO and $vSec['meta']['matchMode']) {
						// Set Score
						if(($dif==1 and $vItem['oppSetScore']>=$objParam->ends-1) or ($dif==-1 and $vItem['setScore']>=$objParam->ends-1)) {
							// can win the match
							$ToWin=($endScore[$runningEnd]-$oppEndScore[$runningEnd])*$dif;
							if(($dif==-1 and $vItem['setScore']==$objParam->ends-1) or ($dif==1 and $vItem['oppSetScore']==$objParam->ends-1)) {
								$ToWin++;
							}

							// check if any stars
							if(!ctype_upper($vItem['arrowstring']) or !ctype_upper($vItem['oppArrowstring'])) {
								if($dif==1) {
									$ToWin+=RaiseStars($vItem['arrowstring'], $regExp);
								} elseif($dif==-1) {
									$ToWin+=RaiseStars($vItem['oppArrowstring'], $regExp);
								}
							}
							if($ToWin<=$vSec['meta']['maxPoint'] and $ToWin>0) {
								// check the correct value of $ToWin
								if(!in_array($ToWin, $vSec['meta']['targetTypeValues'])) {
									foreach(array_reverse($vSec['meta']['targetTypeValues']) as $v) {
										if($v>$ToWin) {
											$ToWin=$v;
											break;
										}
									}
								}
								if($ToWin>=$vSec['meta']['minPoint']) {
									if($dif==1) {
										$tmpR['ToWin']=$ToWin.' to win';
									} else {
										$tmpL['ToWin']=$ToWin.' to win';
									}
								} else {
									if($dif==1) {
										$tmpR['ToWin']='Hit to win';
									} else {
										$tmpL['ToWin']='Hit to win';
									}
								}
							}
						}
					} else {
						// Cumulative
						$ToWin=($vItem['score']-$vItem['oppScore'])*$dif +1;
						if($IsSO) {
							if($vSec['meta']['finSO']>1) {
								$ToWin=(intval(ValutaArrowString($vItem['tiebreak']))-intval(ValutaArrowString($vItem['oppTiebreak'])))*$dif +1;
								rsort($arrValue);
								rsort($oppArrValue);
								if($dif==1 and $arrValue[0]<$oppArrValue[0]) {
									$ToWin--;
								} elseif($dif==-1 and $arrValue[0]>$oppArrValue[0]) {
									$ToWin--;
								}
							} else {
								$ToWin=(intval(ValutaArrowString($vItem['tiebreak']))-intval(ValutaArrowString($vItem['oppTiebreak'])))*$dif +1;
							}
						} else {
							$scoreStar = $vItem['score'] + RaiseStars($vItem['arrowstring'], $regExp, $EvCode, $EvType, $TourId);
							$oppScoreStar = $vItem['oppScore'] + RaiseStars($vItem['oppArrowstring'], $regExp, $EvCode, $EvType, $TourId);
							$scoreToWin=($vItem['score']-$oppScoreStar)*$dif +1;
							$oppScoreToWin=($scoreStar-$vItem['oppScore'])*$dif +1;
							$ToWin=max($scoreToWin,$oppScoreToWin);
						}
						if($ToWin<=$vSec['meta']['maxPoint'] and $ToWin>0) {
							// check the correct value of $ToWin
							if(!in_array($ToWin, $vSec['meta']['targetTypeValues'])) {
								foreach(array_reverse($vSec['meta']['targetTypeValues']) as $v) {
									if($v>$ToWin) {
										$ToWin=$v;
										break;
									}
								}
							}
							if($ToWin>=$vSec['meta']['minPoint']) {
								if($dif==1) {
									$tmpR['ToWin']=$ToWin.' to win';
								} else {
									$tmpL['ToWin']=$ToWin.' to win';
								}
							} else {
								if($dif==1) {
									$tmpR['ToWin']='Hit to win';
								} else {
									$tmpL['ToWin']='Hit to win';
								}
							}
						}
					}
				}


				if(!$json_array['MatchFinished']) {
                    $json_array['MatchRunningEnd'] = ($runningEnd < $objParam->ends) ? strval($runningEnd + 1) : 'SO';
                    $json_array['MatchRunningEndSO'] = ($runningEnd < $objParam->ends) ? $json_array['MatchRunningEndSO'] : strval($runningEndSo);
                }
				$tmpL["Ends"] = $end;
				$tmpR["Ends"] = $oppEnd;

				$json_array['LeftOpponent'] = $tmpL;
				$json_array['RightOpponent'] = $tmpR;
			}
		}
	}
}

// Return the json structure with the callback function that is needed by the app
SendResult($json_array);
