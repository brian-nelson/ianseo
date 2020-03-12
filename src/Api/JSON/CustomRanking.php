<?php
require_once(dirname(__FILE__) . '/config.php');

$RankType = '';
if(isset($_REQUEST['RankType']) && preg_match("/^(WCUP)+$/i", $_REQUEST['RankType'])) {
	$RankType = $_REQUEST['RankType'];
}

$EvType = -1;
if(isset($_REQUEST['Type']) && preg_match("/^[01]$/", $_REQUEST['Type'])) {
	$EvType = $_REQUEST['Type'];
}

$EvCode = false;
if(isset($_REQUEST['Event']) && preg_match("/^[a-z0-9_-]+$/i", $_REQUEST['Event'])) {
	$EvCode = $_REQUEST['Event'];
}

$CutRank=false;
if(isset($_REQUEST["CutPosition"]) && preg_match("/^[0-9]+$/i", $_REQUEST['CutPosition'])) {
	$CutRank = $_REQUEST['CutPosition'];
}

$json_array=array();

switch($RankType) {
	case 'WCUP':
		require_once('Modules/WorldCup/config.inc.php');
		require_once('Modules/WorldCup/elab.php');
		
		if($EvType==0) { 
			require_once('Modules/WorldCup/index-Common.php');
			foreach($ScoreInd as $ev=>$ath) {
				if($EvCode!=$ev) {
					continue;
				}
                $json_array = array("RankingName"=>$TVMainTitle, "Event"=>$ev, "EventName"=>"", "Type"=>"1", "Results"=>array(), "RankType"=>$RankType);
				$Select = "SELECT EvEventName as Name FROM Events WHERE EvCode=" . StrSafe_DB($EvCode) . " AND EvTeamEvent=0 AND EvTournament=" . StrSafe_DB(getIdFromCode($headerCompetition)) . " ";
				$Rs=safe_r_sql($Select);
				if (safe_num_rows($Rs)==1) {
					$r = safe_fetch($Rs);
					$json_array["EventName"] = $r->Name;
				}
				$TotInd = array();
				$MaxInd = array();
				$ExistingNOC = array();
				$EvQualifiedNo = $QualifiedNo;
				foreach($ath as $keyAth=>$result) {
					$validScore=array();
					$maxScore=array();
					$TotInd[$keyAth]=0;
					$MaxInd[$keyAth]=0;
					foreach($result as $kres=>$value) {
						if($value != -999) {
							$validScore[]= ($value == -1000 ? 0 : abs($value));
							$maxScore[]=abs($BestScoreInd[$ev][$keyAth][$kres]);
						}
					}
					rsort($validScore);
					rsort($maxScore);
					for($i=0; $i<(min($bestOf,count($validScore))); $i++)
						$TotInd[$keyAth] += $validScore[$i];
						for($i=0; $i<(min($bestOf,count($maxScore))); $i++)
							$MaxInd[$keyAth] += $maxScore[$i];
				}
				arsort($TotInd,SORT_NUMERIC);
				arsort($MaxInd,SORT_NUMERIC);
				reset($TotInd);
				reset($MaxInd);
			
				//Calculate CutScore
				$CheckQualifiedNumber = $QualifiedNo;
				$nocList=array();
				for($i=1; $i<$CheckQualifiedNumber;$i++) {
					if(!array_key_exists($AthInd[key($TotInd)][1], $nocList)) {
						$nocList[$AthInd[key($TotInd)][1]] = 1;
					} else {
						$nocList[$AthInd[key($TotInd)][1]]++;
					}
					if(($nocList[$AthInd[key($TotInd)][1]]>$MaxByNoc) || ($AthInd[key($TotInd)][1]==$HostCountry && $CheckQualifiedNumber == $QualifiedNo)) {
						$CheckQualifiedNumber++;
					}
					//			echo $AthInd[key($TotInd)][1] . "." . $CheckQualifiedNumber . "." . current($TotInd) . "<br>";
					next($TotInd);
				}
				$CutScore=current($TotInd);
				if($runningEvent != count($competitions)-1) {
					$CutScore -= (count($competitions)-$runningEvent-1) * $Bonus[1];
					if($CutScore < 0 ) {
						$CutScore = 0;
					}
				}
				reset($TotInd);
			
				//Calculate SureScore[HL]
				$CheckQualifiedNumber = $QualifiedNo;
				$nocList=array();
				for($i=1; $i<$CheckQualifiedNumber;$i++) {
					if(!array_key_exists($AthInd[key($MaxInd)][1], $nocList)) {
						$nocList[$AthInd[key($MaxInd)][1]] = 1;
					} else {
						$nocList[$AthInd[key($MaxInd)][1]]++;
					}
					if(($nocList[$AthInd[key($MaxInd)][1]]>$MaxByNoc) || ($AthInd[key($MaxInd)][1]==$HostCountry && $CheckQualifiedNumber == $QualifiedNo)) {
						$CheckQualifiedNumber++;
					}
					next($MaxInd);
				}
				$SureScoreH=current($MaxInd);
				next($MaxInd);
				while((array_key_exists($AthInd[key($MaxInd)][1], $nocList) && $nocList[$AthInd[key($MaxInd)][1]]>$MaxByNoc)) {
					next($MaxInd);
				}
				$SureScoreL=current($MaxInd);
				reset($MaxInd);
				$SureScoreH += (count($competitions)-$runningEvent-1) * $Bonus[1];
				
				$actRank = 0;
				$runRank=0;
				$oldPoint = -1;
				foreach($TotInd as $waid=>$point) {
					if($point==0 && ($PosInd[$ev][$waid][$runningEvent]==-999 || $PosInd[$ev][$waid][$runningEvent]>16)) {
						continue;
					}
					$tmpRow = array();
					$runRank++;
					if($oldPoint!=$point) {
						$actRank = $runRank;
					}
					$oldPoint = $point;
		
					$tmpRow["Rank"] = strval($actRank);
					$tmpRow["Id"] = strval($waid);
					$tmpRow["FamilyName"] = $AthInd[$waid][3];
					$tmpRow["GivenName"] = $AthInd[$waid][4];
					$tmpRow["NameOrder"] = $AthInd[$waid][5];
					$tmpRow["TeamCode"] = $AthInd[$waid][1];
					$tmpRow["TeamName"] = $AthInd[$waid][2];
					$tmpRow["Points"] = strval($point);
					$tmpRow["Status"] = "2";
					$tmpRow["StatusText"] = "Eligible";
					$tmpRow["Stages"] = array();
					
		
					$i=0;
					$stillCompeting = false;
                    $canRed=true;
                    $isAnEventWinner = false;
					foreach($ScoreInd[$ev][$waid] as $detailPoint){
						$Stage=array("Stage"=>strval($i+1), "Points"=>"0", "PresentInStage"=>false, "StillCompeting"=>false);
						if($PosInd[$ev][$waid][$i] == -999) {
							$Stage["PresentInStage"]=false;
						} else {
							$Stage["PresentInStage"]=true;
							if($detailPoint == -1000) {
								$Stage["Points"]="0";
								$Stage["StillCompeting"]=true;
								$stillCompeting = true;
							}elseif($detailPoint < 0) {
								$Stage["Points"]=strval(abs($detailPoint));
								$Stage["StillCompeting"]=true;
								$stillCompeting = true;
                                if($detailPoint==-21) {
                                    $canRed = false;
                                }
							} else {
                                $Stage["Points"] = strval($detailPoint);
                                if($detailPoint == $Bonus[1]) {
                                    $isAnEventWinner = true;
                                }
                            }
						}
                        $tmpRow["Stages"][] = $Stage;
						$i++;
					}

					//Calculus of the in/out position
					if(array_key_exists($AthInd[$waid][1], $ExistingNOC)){
						$ExistingNOC[$AthInd[$waid][1]]++;
					} else {
						$ExistingNOC[$AthInd[$waid][1]] = 1;
					}
					$qualified=0;

                    if($isAnEventWinner) {
                        $tmpRow["Status"] = "6";
                        $tmpRow["StatusText"] = "Stage winner";
                        $EvQualifiedNo++;
                    } elseif($ExistingNOC[$AthInd[$waid][1]]>$MaxByNoc AND !in_array($waid,$pendingIds)) {
						$tmpRow["Status"] = "3";
						$tmpRow["StatusText"] = "Third+ athlete";
						if($ExistingNOC[$AthInd[$waid][1]]==$MaxByNoc+1)
							$EvQualifiedNo++;
					} elseif($point>=$SureScoreH && $point>$SureScoreL) {
						$tmpRow["Status"] = "0";
						$tmpRow["StatusText"] = "Qualified";
					} elseif($actRank>$EvQualifiedNo AND $CutScore>$MaxInd[$waid] AND $canRed AND !in_array($waid,$pendingIds)) {
						$tmpRow["Status"] = "4";
						$tmpRow["StatusText"] = "Not Qualified";
					}
                    if(!$canRed) {
                        $EvQualifiedNo++;
                    }
					/*	ANTALYA - TO RE-ENABLE if something weird
					 } elseif($actRank>$EvQualifiedNo && (!$stillCompeting || $CutScore>$MaxInd[$waid])) {
					 $qualified=-1;
					 }*/
					
					$CanDoBetter=0;
					$UsedPoints = array_fill(0,count($Bonus),0);	//Array of the possible points already used
					$UsedPoints[0]=1;		//Exclude the first (0), in the $Bonus Array just for padding
					for($cntPeople=1; $cntPeople<=count($Bonus); $cntPeople++) {
						$ScoreOther = current(array_slice($TotInd, array_search($waid, array_keys($TotInd)) + $cntPeople, 1));
						for($cntBonus=(count($Bonus)-1); $cntBonus>0; $cntBonus--) {
							if($UsedPoints[$cntBonus]==1) {
								continue;
							}
							if($ScoreOther+$Bonus[$cntBonus]>=$point) {
								$UsedPoints[$cntBonus]=1;
								$CanDoBetter++;
								break;
							}
						}
					}
					if(!$isAnEventWinner and ($CanDoBetter<$QualifiedNo-$actRank OR in_array($waid,$qualifiedIds))) {
						$tmpRow["Status"] = "0";
						$tmpRow["StatusText"] = "Qualified";
					}
                    if(in_array($waid,$notQualifiedIds)) {
                        $tmpRow["Status"] = "4";
                        $tmpRow["StatusText"] = "Not Qualified";
                    }
					

					if($CutRank === false || $CutRank>=$actRank) {
						$json_array["Results"][] = $tmpRow;
					}
				}
			}
		} elseif($EvType == 1) {
			require_once('Modules/WorldCup/indexTeam-Common.php');
			foreach($ScoreTeam as $ev=>$team) {
				if($EvCode!=$ev) {
					continue;
				}
				$json_array = array("RankingName"=>$TVMainTitle, "Event"=>$ev, "EventName"=>"", "Type"=>"1", "Results"=>array());
				$Select = "SELECT EvEventName as Name FROM Events WHERE EvCode=" . StrSafe_DB($EvCode) . " AND EvTeamEvent=1 AND EvTournament=" . StrSafe_DB(getIdFromCode($headerCompetition)) . " ";
				$Rs=safe_r_sql($Select);
				if (safe_num_rows($Rs)==1) {
					$r = safe_fetch($Rs);
					$json_array["EventName"] = $r->Name;
				}
				
				
				
				$TotTeam = array();
				foreach($team as $keyTeam=>$result) {
					$validScore=array();
					$TotTeam[$keyTeam]=0;
					foreach($result as $value) {
						if($value != -999) {
							$validScore[]=abs($value);
						}
					}
					rsort($validScore);
					for($i=0; $i<count($validScore); $i++) {
						$TotTeam[$keyTeam] += $validScore[$i];
					}
				}
				arsort($TotTeam,SORT_NUMERIC);
				
				//Calculate CutScore
				$CheckQualifiedNumber = $MixedTeamQualifiedNo;
				for($i=1; $i<$MixedTeamQualifiedNo;$i++) {
					next($TotTeam);
				}
				$CutScore=current($TotTeam);
				if($runningEvent != count($competitions)-1) {
					$CutScore -= (count($competitions)-$runningEvent-1) * $BonusTeam[1];
					if($CutScore < 0 ) {
						$CutScore = 0;
					}
				}
				reset($TotTeam);
			
				$actRank = 0;
				$runRank=0;
				$oldPoint = -1;
				foreach($TotTeam as $waid=>$point) {
					if($point==0 && ($PosTeam[$ev][$waid][$runningEvent]==-999 || $PosTeam[$ev][$waid][$runningEvent]>16)) {
						continue;
					}
					$tmpRow = array();
					$runRank++;
					if($oldPoint!=$point) {
						$actRank = $runRank;
					}
					$oldPoint = $point;
			
					$tmpRow["Rank"] = strval($actRank);
					$tmpRow["TeamCode"] = strval($waid);
					$tmpRow["TeamName"] = $TeamList[$waid];
					$tmpRow["Points"] = strval($point);
					$tmpRow["Status"] = "0";
					$tmpRow["StatusText"] = "";
					$tmpRow["Stages"] = array();
					
					$i=0;
					foreach($ScoreTeam[$ev][$waid] as $detailPoint){
						$Stage=array("Stage"=>strval($i+1), "Points"=>"0", "PresentInStage"=>false, "StillCompeting"=>false);
						if($PosTeam[$ev][$waid][$i] == -999) {
							$Stage["PresentInStage"]=false;
						} else {
							$Stage["PresentInStage"]=true;
							if($detailPoint < 0) {
								$Stage["Points"]=strval(abs($detailPoint));
								$Stage["StillCompeting"]=true;
							} else {
								$Stage["Points"]=strval($detailPoint);
							}
						}
						$tmpRow["Stages"][] = $Stage;
						$i++;
					}
					
					//////////////////
					$qualified=0;
					if($actRank>$MixedTeamQualifiedNo && $CutScore>$point) {
						$tmpRow["Status"] = "4";
						$tmpRow["StatusText"] = "Not Qualified";
					}
					//////////////////
					$CanDoBetter=0;
					if($runningEvent != count($competitions)-1) {
						$UsedPoints = array_fill(0,count($BonusTeam),0);	//Array of the possible points already used
						$UsedPoints[0]=1;		//Exclude the first (0), in the $Bonus Array just for padding
						for($cntPeople=1; $cntPeople<=count($BonusTeam); $cntPeople++) {
							$ScoreOther = current(array_slice($TotTeam, array_search($waid, array_keys($TotTeam)) + $cntPeople, 1));
							for($cntBonus=(count($BonusTeam)-1); $cntBonus>0; $cntBonus--) {
								if($UsedPoints[$cntBonus]==1) {
									continue;
								}
								if($ScoreOther+$BonusTeam[$cntBonus]>=$point) {
									$UsedPoints[$cntBonus]=1;
									$CanDoBetter++;
									break;
								}
							}
						}
					}
					if($CanDoBetter<=$MixedTeamQualifiedNo-$actRank) {
						$tmpRow["Status"] = "0";
						$tmpRow["StatusText"] = "Qualified";
/*						if($waid=='TPE') {
                            $tmpRow["Status"] = "-1";
                            $tmpRow["StatusText"] = "Not Qualified";
                        }
*/
					}
					
					
					if($CutRank === false || $CutRank>=$actRank) {
						$json_array["Results"][] = $tmpRow;
					}
				}
			}
		}
		break;
	default:
		break;
}


// Return the json structure with the callback function that is needed by the app
SendResult($json_array);