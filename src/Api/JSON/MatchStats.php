<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Obj_RankFactory.php');

$TourId = 0;
$TourCode = '';
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
	$TourId = getIdFromCode($_REQUEST['CompCode']);
	$TourCode = $_REQUEST['CompCode'];
}

$EvType = -1;
if(isset($_REQUEST['Type']) && preg_match("/^[01]$/", $_REQUEST['Type'])) {
	$EvType = $_REQUEST['Type'];
}

$EvCode = '....';
if(isset($_REQUEST['Event']) && preg_match("/^[a-z0-9_-]+$/i", $_REQUEST['Event'])) {
	$EvCode = $_REQUEST['Event'];
}
$WaEvCode=$EvCode;
$q = safe_r_SQL("SELECT EvWaCategory from Events WHERE EvCode='{$EvCode}' AND EvTeamEvent={$EvType} AND EvTournament={$TourId}");
if($r = safe_fetch($q) AND !empty($r->EvWaCategory)) {
    $WaEvCode = $r->EvWaCategory;
}

$MatchId = -1;
if(isset($_REQUEST['MatchId']) && preg_match("/^[0-9]+$/", $_REQUEST['MatchId'])) {
	$MatchId = $_REQUEST['MatchId'];
}

$json_array=array();
$imgPath='/TV/Photos/' . $TourCode . '-%s-%s.jpg';

$options['tournament']=$TourId;
$options['events']=$EvCode;
$options['matchno']=$MatchId;

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
			$json_array = Array("Event"=>$EvCode, "Type"=>$EvType, "MatchId"=>$MatchId, "PhaseId"=>strval($kPh),
                "H2HStats"=>Array("Matches"=>0, "LeftWon"=>0, "RightWon"=>0, "LeftArrAvg"=>0, "RightArrAvg"=>0, "LeftSequence"=>'', "RightSequence"=>''));
			$objParam=getEventArrowsParams($kSec,$kPh,$EvType,$TourId);
			foreach($vPh['items'] as $kItem=>$vItem) {
				$tmpL = array();
				$tmpR = array();
				if($EvType==0) {
					$tmpL += array("Id"=>$vItem["bib"], "FamilyName"=>$vItem["familyName"], "GivenName"=>$vItem["givenName"], "NameOrder"=>$vItem["nameOrder"], "Gender"=>$vItem["gender"]);
					if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCode.'-En-'.$vItem['id'].'.jpg')) {
						$tmpL += array("ProfilePicURL"=>sprintf($imgPath, 'En', $vItem['id']));
					}
					$tmpR += array("Id"=>$vItem["oppBib"], "FamilyName"=>$vItem["oppFamilyName"], "GivenName"=>$vItem["oppGivenName"], "NameOrder"=>$vItem["oppNameOrder"], "Gender"=>$vItem["oppGender"]);
					if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCode.'-En-'.$vItem['oppId'].'.jpg')) {
						$tmpR += array("ProfilePicURL"=>sprintf($imgPath, 'En', $vItem['oppId']));
					}
				}
				$tmpL += array("TeamCode"=>$vItem["countryCode"], "TeamName"=>$vItem["countryName"]);
				if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCode.'-Fl-'.$vItem['countryCode'].'.jpg')) {
					$tmpL += array("FlagURL"=>sprintf($imgPath, 'Fl', $vItem['countryCode']));
				}
				$tmpL += array(
				    "Career"=>array("AvgArr"=>0, "MatchTot"=>0, "MatchWon"=>0, "MatchPercentage"=>0, "TieTot"=>0, "TieWon"=>0, "TiePercentage"=>0),
                    "CurrentSeason"=>array("AvgArr"=>0, "MatchTot"=>0, "MatchWon"=>0, "MatchPercentage"=>0, "TieTot"=>0, "TieWon"=>0, "TiePercentage"=>0),
                    "LastSeason"=>array("AvgArr"=>0, "MatchTot"=>0, "MatchWon"=>0, "MatchPercentage"=>0, "TieTot"=>0, "TieWon"=>0, "TiePercentage"=>0),
                    "WorldRanking"=>array("Current"=>0, "Weeks"=>0, "Best"=>0),
                    "WCupMedals"=>array("1"=>0, "2"=>0, "3"=>0),
                    "WChampMedals"=>array("1"=>0, "2"=>0, "3"=>0),
                    "Matches"=>array()
                    );

				$tmpR += array("TeamCode"=>$vItem["oppCountryCode"], "TeamName"=>$vItem["oppCountryName"]);
				if(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCode.'-Fl-'.$vItem['oppCountryCode'].'.jpg')) {
					$tmpR += array("FlagURL"=>sprintf($imgPath, 'Fl', $vItem['oppCountryCode']));
				}
                $tmpR += array(
                    "Career"=>array("AvgArr"=>0, "MatchTot"=>0, "MatchWon"=>0, "MatchPercentage"=>0, "TieTot"=>0, "TieWon"=>0, "TiePercentage"=>0),
                    "CurrentSeason"=>array("AvgArr"=>0, "MatchTot"=>0, "MatchWon"=>0, "MatchPercentage"=>0, "TieTot"=>0, "TieWon"=>0, "TiePercentage"=>0),
                    "LastSeason"=>array("AvgArr"=>0, "MatchTot"=>0, "MatchWon"=>0, "MatchPercentage"=>0, "TieTot"=>0, "TieWon"=>0, "TiePercentage"=>0),
                    "WorldRanking"=>array("Current"=>0, "Weeks"=>0, "Best"=>0),
                    "WCupMedals"=>array("1"=>0, "2"=>0, "3"=>0),
                    "WChampMedals"=>array("1"=>0, "2"=>0, "3"=>0),
                    "Matches"=>array()
                );

				$bioL=null;
                $bioR=null;
                if($EvType) {
//Get the biographies for the two teams
                    $bioL = json_decode(file_get_contents($CFG->WaWrapper . "/v3/TEAMBIOGRAPHY/?Noc=" . $vItem["countryCode"] . "&CatCode=" . $WaEvCode . "&Detailed=1&RBP=-1"));
                    $bioR = json_decode(file_get_contents($CFG->WaWrapper . "/v3/TEAMBIOGRAPHY/?Noc=" . $vItem["oppCountryCode"] . "&CatCode=" . $WaEvCode . "&Detailed=1&RBP=-1"));
                } else {
//Get the biographies for the two individuals
                    $bioL = json_decode(file_get_contents($CFG->WaWrapper . "/v3/ATHLETEBIOGRAPHY/?Id=" . $vItem["bib"] . "&CatCode=" . $WaEvCode . "&Detailed=1&RBP=-1"));
                    $bioR = json_decode(file_get_contents($CFG->WaWrapper . "/v3/ATHLETEBIOGRAPHY/?Id=" . $vItem["oppBib"] . "&CatCode=" . $WaEvCode . "&Detailed=1&RBP=-1"));
                }
//Career
                if(!empty($bioL->items[0]->Stats->Career[0]->AverageArr)) {
                    $tmpL['Career']['AvgArr'] = $bioL->items[0]->Stats->Career[0]->AverageArr;
                }
                if(!empty($bioL->items[0]->Stats->Career[0]->MatchTot)) {
                    $tmpL['Career']['MatchTot'] = $bioL->items[0]->Stats->Career[0]->MatchTot;
                    $tmpL['Career']['MatchWon'] = $bioL->items[0]->Stats->Career[0]->MatchWin;
                    $tmpL['Career']['MatchPercentage'] = $bioL->items[0]->Stats->Career[0]->MatchWinPercentage;
                }
                if(!empty($bioL->items[0]->Stats->Career[0]->TBTot)) {
                    $tmpL['Career']['TieTot'] = $bioL->items[0]->Stats->Career[0]->TBTot;
                    $tmpL['Career']['TieWon'] = $bioL->items[0]->Stats->Career[0]->TBWin;
                    $tmpL['Career']['TiePercentage'] = $bioL->items[0]->Stats->Career[0]->TBWinPercentage;
                }
                if(!empty($bioR->items[0]->Stats->Career[0]->AverageArr)) {
                    $tmpR['Career']['AvgArr'] = $bioR->items[0]->Stats->Career[0]->AverageArr;
                }
                if(!empty($bioR->items[0]->Stats->Career[0]->MatchTot)) {
                    $tmpR['Career']['MatchTot'] = $bioR->items[0]->Stats->Career[0]->MatchTot;
                    $tmpR['Career']['MatchWon'] = $bioR->items[0]->Stats->Career[0]->MatchWin;
                    $tmpR['Career']['MatchPercentage'] = $bioR->items[0]->Stats->Career[0]->MatchWinPercentage;
                }
                if(!empty($bioR->items[0]->Stats->Career[0]->TBTot)) {
                    $tmpR['Career']['TieTot'] = $bioR->items[0]->Stats->Career[0]->TBTot;
                    $tmpR['Career']['TieWon'] = $bioR->items[0]->Stats->Career[0]->TBWin;
                    $tmpR['Career']['TiePercentage'] = $bioR->items[0]->Stats->Career[0]->TBWinPercentage;
                }
//Current Season
                if(!empty($bioL->items[0]->Stats->Season)) {
                    $Season = $bioL->items[0]->Stats->Season;
                    if (!empty($bioL->items[0]->Stats->$Season[0]->AverageArr)) {
                        $tmpL['CurrentSeason']['AvgArr'] = $bioL->items[0]->Stats->$Season[0]->AverageArr;
                    }
                    if (!empty($bioL->items[0]->Stats->$Season[0]->MatchTot)) {
                        $tmpL['CurrentSeason']['MatchTot'] = $bioL->items[0]->Stats->$Season[0]->MatchTot;
                        $tmpL['CurrentSeason']['MatchWon'] = $bioL->items[0]->Stats->$Season[0]->MatchWin;
                        $tmpL['CurrentSeason']['MatchPercentage'] = $bioL->items[0]->Stats->$Season[0]->MatchWinPercentage;
                    }
                    if(!empty($bioL->items[0]->Stats->$Season[0]->TBTot)) {
                        $tmpL['CurrentSeason']['TieTot'] = $bioL->items[0]->Stats->$Season[0]->TBTot;
                        $tmpL['CurrentSeason']['TieWon'] = $bioL->items[0]->Stats->$Season[0]->TBWin;
                        $tmpL['CurrentSeason']['TiePercentage'] = $bioL->items[0]->Stats->$Season[0]->TBWinPercentage;
                    }
                }
                if(!empty($bioR->items[0]->Stats->Season)) {
                    $Season = $bioR->items[0]->Stats->Season;
                    if (!empty($bioR->items[0]->Stats->$Season[0]->AverageArr)) {
                        $tmpR['CurrentSeason']['AvgArr'] = $bioR->items[0]->Stats->$Season[0]->AverageArr;
                    }
                    if (!empty($bioR->items[0]->Stats->$Season[0]->MatchTot)) {
                        $tmpR['CurrentSeason']['MatchTot'] = $bioR->items[0]->Stats->$Season[0]->MatchTot;
                        $tmpR['CurrentSeason']['MatchWon'] = $bioR->items[0]->Stats->$Season[0]->MatchWin;
                        $tmpR['CurrentSeason']['MatchPercentage'] = $bioR->items[0]->Stats->$Season[0]->MatchWinPercentage;
                    }
                    if(!empty($bioR->items[0]->Stats->$Season[0]->TBTot)) {
                        $tmpR['CurrentSeason']['TieTot'] = $bioR->items[0]->Stats->$Season[0]->TBTot;
                        $tmpR['CurrentSeason']['TieWon'] = $bioR->items[0]->Stats->$Season[0]->TBWin;
                        $tmpR['CurrentSeason']['TiePercentage'] = $bioR->items[0]->Stats->$Season[0]->TBWinPercentage;
                    }
                }
//Last Season
                if(!empty($bioL->items[0]->Stats->LastSeason)) {
                    $Season = $bioL->items[0]->Stats->LastSeason;
                    if (!empty($bioL->items[0]->Stats->$Season[0]->AverageArr)) {
                        $tmpL['LastSeason']['AvgArr'] = $bioL->items[0]->Stats->$Season[0]->AverageArr;
                    }
                    if (!empty($bioL->items[0]->Stats->$Season[0]->MatchTot)) {
                        $tmpL['LastSeason']['MatchTot'] = $bioL->items[0]->Stats->$Season[0]->MatchTot;
                        $tmpL['LastSeason']['MatchWon'] = $bioL->items[0]->Stats->$Season[0]->MatchWin;
                        $tmpL['LastSeason']['MatchPercentage'] = $bioL->items[0]->Stats->$Season[0]->MatchWinPercentage;
                    }
                    if(!empty($bioL->items[0]->Stats->$Season[0]->TBTot)) {
                        $tmpL['LastSeason']['TieTot'] = $bioL->items[0]->Stats->$Season[0]->TBTot;
                        $tmpL['LastSeason']['TieWon'] = $bioL->items[0]->Stats->$Season[0]->TBWin;
                        $tmpL['LastSeason']['TiePercentage'] = $bioL->items[0]->Stats->$Season[0]->TBWinPercentage;
                    }
                }
                if(!empty($bioR->items[0]->Stats->LastSeason)) {
                    $Season = $bioR->items[0]->Stats->LastSeason;
                    if (!empty($bioR->items[0]->Stats->$Season[0]->AverageArr)) {
                        $tmpR['LastSeason']['AvgArr'] = $bioR->items[0]->Stats->$Season[0]->AverageArr;
                    }
                    if (!empty($bioL->items[0]->Stats->$Season[0]->MatchTot)) {
                        $tmpR['LastSeason']['MatchTot'] = $bioR->items[0]->Stats->$Season[0]->MatchTot;
                        $tmpR['LastSeason']['MatchWon'] = $bioR->items[0]->Stats->$Season[0]->MatchWin;
                        $tmpR['LastSeason']['MatchPercentage'] = $bioR->items[0]->Stats->$Season[0]->MatchWinPercentage;
                    }
                    if(!empty($bioR->items[0]->Stats->$Season[0]->TBTot)) {
                        $tmpR['LastSeason']['TieTot'] = $bioR->items[0]->Stats->$Season[0]->TBTot;
                        $tmpR['LastSeason']['TieWon'] = $bioR->items[0]->Stats->$Season[0]->TBWin;
                        $tmpR['LastSeason']['TiePercentage'] = $bioR->items[0]->Stats->$Season[0]->TBWinPercentage;
                    }
                }
//World Rankings
                if(!empty($bioL->items[0]->WorldRankings->Current)) {
                    $tmpL['WorldRanking']['Current'] = $bioL->items[0]->WorldRankings->Current[0]->Rnk;
                }
                if(!empty($bioL->items[0]->WorldRankings->Current)) {
                    $datetime1 = new DateTime($bioL->items[0]->WorldRankings->Current[0]->RnkDtSince);
                    $datetime2 = new DateTime();
                    $interval = $datetime2->diff($datetime1);
                    $tmpL['WorldRanking']['Weeks'] =intval($interval->format('%a')/7);
                }
                if(!empty($bioL->items[0]->WorldRankings->Best)) {
                    $tmpL['WorldRanking']['Best'] = $bioL->items[0]->WorldRankings->Best[0]->Rnk;
                }
                if(!empty($bioR->items[0]->WorldRankings->Current)) {
                    $tmpR['WorldRanking']['Current'] = $bioR->items[0]->WorldRankings->Current[0]->Rnk;
                }
                if(!empty($bioR->items[0]->WorldRankings->Current)) {
                    $datetime1 = new DateTime($bioR->items[0]->WorldRankings->Current[0]->RnkDtSince);
                    $datetime2 = new DateTime();
                    $interval = $datetime2->diff($datetime1);
                    $tmpR['WorldRanking']['Weeks'] =intval($interval->format('%a')/7);
                }
                if(!empty($bioR->items[0]->WorldRankings->Best)) {
                    $tmpR['WorldRanking']['Best'] = $bioR->items[0]->WorldRankings->Best[0]->Rnk;
                }
//Medals
                foreach($bioL->items[0]->Medals as $k=>$v) {
                    if($v->ComLevel==2) {
                        $tmpL['WChampMedals'][$v->Rnk]++;
                    }
                    if($v->ComLevel==3 AND $v->ComDis) {
                        $tmpL['WCupMedals'][$v->Rnk]++;
                    }
                }
                foreach($bioR->items[0]->Medals as $k=>$v) {
                    if($v->ComLevel==2) {
                        $tmpR['WChampMedals'][$v->Rnk]++;
                    }
                    if($v->ComLevel==3 AND $v->ComDis) {
                        $tmpR['WCupMedals'][$v->Rnk]++;
                    }
                }
//Get the Head2Head History
                $H2Hbio = null;
                if($EvType) {
                    $H2Hbio = json_decode(file_get_contents($CFG->WaWrapper . "/v3/COUNTRYMATCHES/?Noc=" . $vItem["countryCode"] . "&Noc2=" . $vItem["oppCountryCode"] . "&CatCode=" . $WaEvCode . "&IndividualTeam=2&RBP=-1"));
                } else {
                    $H2Hbio = json_decode(file_get_contents($CFG->WaWrapper . "/v3/ATHLETEMATCHES/?Id=" . $vItem["bib"] . "&Id2=" . $vItem["oppBib"] . "&IndividualTeam=1&RBP=-1"));
                }
                $LSum=0;
                $LCnt=0;
                $RSum=0;
                $RCnt=0;
                foreach($H2Hbio->items as $k=>$v) {
//"H2HStats"=>Array("Matches"=>0, "LeftWon"=>0, "RightWon"=>0, LeftArrAvg=>0, RightArrAvg=>0, "LeftSequence"=>'', "RightSequence"=>'')
                    $json_array['H2HStats']['Matches']++;
                    $cL = ($EvType ? ($v->Competitor1->Athlete->NOC == $vItem["countryCode"] ? 'Competitor1' : 'Competitor2') : ($v->Competitor1->Athlete->Id == $vItem["bib"] ? 'Competitor1' : 'Competitor2'));
                    $cR = ($EvType ? ($v->Competitor1->Athlete->NOC == $vItem["oppCountryCode"] ? 'Competitor1' : 'Competitor2') : ($v->Competitor1->Athlete->Id == $vItem["oppBib"] ? 'Competitor1' : 'Competitor2'));
                    if($v->$cL->WinLose) {
                        $json_array['H2HStats']['LeftWon']++;
                        $json_array['H2HStats']['LeftSequence'] .= 'W';
                    } else {
                        $json_array['H2HStats']['LeftSequence'] .= 'L';
                    }
                    if($v->$cR->WinLose) {
                        $json_array['H2HStats']['RightWon']++;
                        $json_array['H2HStats']['RightSequence'] .= 'W';
                    } else {
                        $json_array['H2HStats']['RightSequence'] .= 'L';
                    }
                    $v->$cL->Arr = str_replace('X','10',$v->$cL->Arr);
                    $v->$cR->Arr = str_replace('X','10',$v->$cR->Arr);

                    $LSum += (array_sum(explode(',',trim($v->$cL->Arr))) + array_sum(explode(',',trim($v->$cL->ArrTB))));
                    $LCnt += (empty($v->$cL->Arr) ? 0: count(explode(',',trim($v->$cL->Arr)))) + (empty($v->$cL->ArrTB) ? 0: count(explode(',',trim($v->$cL->ArrTB))));
                    $RSum += (array_sum(explode(',',$v->$cR->Arr)) + array_sum(explode(',',$v->$cR->ArrTB)));
                    $RCnt += (empty($v->$cR->Arr) ? 0: count(explode(',',$v->$cR->Arr))) + (empty($v->$cR->ArrTB) ? 0: count(explode(',',$v->$cR->ArrTB)));
                }
                if($LCnt) {
                    $json_array['H2HStats']['LeftArrAvg'] = round(floatval($LSum/$LCnt),3);
                }
                if($RCnt) {
                    $json_array['H2HStats']['RightArrAvg'] = round(floatval($RSum/$RCnt),3);
                }
// MAtches History
                $rnkMatches=null;
                $id='';
                $oppId='';
                if($EvType) {
                    $id='teamId';
                    $oppId='oppTeamId';
                    $rnkMatches = Obj_RankFactory::create('GridTeam', array('coid' => $vItem['teamId'], 'tournament' => $TourId, 'events' => $EvCode));
                } else {
                    $id='id';
                    $oppId='oppId';
                    $rnkMatches = Obj_RankFactory::create('GridInd', array('enid' => $vItem['id'], 'tournament' => $TourId, 'events' => $EvCode));
                }
                $rnkMatches->read();
                $rnkMatchesData = $rnkMatches->getData();
                $cntPhase = 1;
                foreach ($rnkMatchesData['sections'][$options['events']]['phases'] as $kPh => $vPh) {
                    if ($MatchId >= $vPh['items'][0]['matchNo']) {
                        continue;
                    }
                    //debug_svela($vPh);
                    $tmpMatch = array("PhCode" => $kPh, "PhPhase" => $vPh['meta']['phaseName'], "PhNameShort" => getPhaseTV($kPh, $cntPhase), "PhName" => get_text(getPhaseTV($kPh, $cntPhase) . "_Phase", "Tournament"),
                        "Id" => '', "FamilyName" => '', "GivenName" => '', "NameOrder" => 0, "Gender" => 0, "NOC" => '', "Score" => '', "OppScore" => '', "isBye" => false, "topSeeded" => false);
                    $cntPhase++;

                    if (($vPh['items'][0]['saved'] OR $vPh['items'][0]['oppSaved']) AND ($vPh['items'][0]['tie'] == 2 OR $vPh['items'][0]['oppTie'] == 2) AND ($vPh['items'][0]['id'] == 0 OR $vPh['items'][0]['oppId'] == 0)) {
                        $tmpMatch['topSeeded'] = true;
                        $tmpMatch['isBye'] = true;
                    } else if ($vPh['items'][0]['tie'] == 2 OR $vPh['items'][0]['oppTie'] == 2) {
                        if ($vPh['items'][0][$id] == 0 OR $vPh['items'][0][$oppId] == 0) {
                            $tmpMatch['isBye'] = true;
                        } else {
                            $tmpMatch['isBye'] = true;
                            if($EvType==0) {
                                $tmpMatch["Id"] = $vPh['items'][0][(($vPh['items'][0]['id'] == $vItem['id']) ? 'oppBib' : 'bib')];
                                $tmpMatch["FamilyName"] = $vPh['items'][0][(($vPh['items'][0]['id'] == $vItem['id']) ? 'oppFamilyName' : 'familyName')];
                                $tmpMatch["GivenName"] = $vPh['items'][0][(($vPh['items'][0]['id'] == $vItem['id']) ? 'oppGivenName' : 'givenName')];
                                $tmpMatch["NameOrder"] = $vPh['items'][0][(($vPh['items'][0]['id'] == $vItem['id']) ? 'oppNameOrder' : 'nameOrder')];
                                $tmpMatch["Gender"] = $vPh['items'][0][(($vPh['items'][0]['id'] == $vItem['id']) ? 'oppGender' : 'gender')];
                            } else {
                                $tmpMatch["FamilyName"] = $vPh['items'][0][(($vPh['items'][0][$id] == $vItem[$id]) ? 'oppCountryName' : 'countryName')];
                            }
                            $tmpMatch["NOC"] = $vPh['items'][0][(($vPh['items'][0][$id] == $vItem[$id]) ? 'oppCountryCode' : 'countryCode')];
                            $tmpMatch["OppScore"] = $vPh['items'][0][(($vPh['items'][0][$id] == $vItem[$id]) ? 'oppIrmText' : 'irmText')];
                        }
                    } else {
                        if($EvType==0) {
                            $tmpMatch["Id"] = $vPh['items'][0][(($vPh['items'][0]['id'] == $vItem['id']) ? 'oppBib' : 'bib')];
                            $tmpMatch["FamilyName"] = $vPh['items'][0][(($vPh['items'][0]['id'] == $vItem['id']) ? 'oppFamilyName' : 'familyName')];
                            $tmpMatch["GivenName"] = $vPh['items'][0][(($vPh['items'][0]['id'] == $vItem['id']) ? 'oppGivenName' : 'givenName')];
                            $tmpMatch["NameOrder"] = $vPh['items'][0][(($vPh['items'][0]['id'] == $vItem['id']) ? 'oppNameOrder' : 'nameOrder')];
                            $tmpMatch["Gender"] = $vPh['items'][0][(($vPh['items'][0]['id'] == $vItem['id']) ? 'oppGender' : 'gender')];
                        } else {
                            $tmpMatch["FamilyName"] = $vPh['items'][0][(($vPh['items'][0][$id] == $vItem[$id]) ? 'oppCountryName' : 'countryName')];
                        }
                        $tmpMatch["NOC"] = $vPh['items'][0][(($vPh['items'][0][$id] == $vItem[$id]) ? 'oppCountryCode' : 'countryCode')];
                        $tmpMatch["Score"] = $vPh['items'][0][(($vPh['items'][0][$id] == $vItem[$id]) ? ($rnkMatchesData['sections'][$options['events']]['meta']['matchMode'] ? 'setScore' : 'score') : ($rnkMatchesData['sections'][$options['events']]['meta']['matchMode'] ? 'oppSetScore' : 'oppScore'))];
                        $tmpMatch["OppScore"] = $vPh['items'][0][(($vPh['items'][0][$id] == $vItem[$id]) ? ($rnkMatchesData['sections'][$options['events']]['meta']['matchMode'] ? 'oppSetScore' : 'oppScore') : ($rnkMatchesData['sections'][$options['events']]['meta']['matchMode'] ? 'setScore' : 'score'))];
                    }
                    $tmpL['Matches'][] = $tmpMatch;
                }

                $rnkMatches=null;
                $id='';
                $oppId='';
                if($EvType) {
                    $id='teamId';
                    $oppId='oppTeamId';
                    $rnkMatches = Obj_RankFactory::create('GridTeam', array('coid' => $vItem['oppTeamId'], 'tournament' => $TourId, 'events' => $EvCode));
                } else {
                    $id='id';
                    $oppId='oppId';
                    $rnkMatches = Obj_RankFactory::create('GridInd', array('enid' => $vItem['oppId'], 'tournament' => $TourId, 'events' => $EvCode));
                }
                $rnkMatches->read();
                $rnkMatchesData = $rnkMatches->getData();
                $cntPhase = 1;
                foreach ($rnkMatchesData['sections'][$options['events']]['phases'] as $kPh => $vPh) {
                    if ($MatchId >= $vPh['items'][0]['matchNo']) {
                        continue;
                    }
                    //debug_svela($vPh);
                    $tmpMatch = array("PhCode" => $kPh, "PhPhase" => $vPh['meta']['phaseName'], "PhNameShort" => getPhaseTV($kPh, $cntPhase), "PhName" => get_text(getPhaseTV($kPh, $cntPhase) . "_Phase", "Tournament"),
                        "Id" => '', "FamilyName" => '', "GivenName" => '', "NameOrder" => 0, "Gender" => 0, "NOC" => '', "Score" => '', "OppScore" => '', "isBye" => false, "topSeeded" => false);


                    if (($vPh['items'][0]['saved'] OR $vPh['items'][0]['oppSaved']) AND ($vPh['items'][0]['tie'] == 2 OR $vPh['items'][0]['oppTie'] == 2) AND ($vPh['items'][0]['id'] == 0 OR $vPh['items'][0]['oppId'] == 0)) {
                        $tmpMatch['topSeeded'] = true;
                        $tmpMatch['isBye'] = true;
                    } else if ($vPh['items'][0]['tie'] == 2 OR $vPh['items'][0]['oppTie'] == 2) {
                        if ($vPh['items'][0][$id] == 0 OR $vPh['items'][0][$oppId] == 0) {
                            $tmpMatch['isBye'] = true;
                        } else {
                            $tmpMatch['isBye'] = true;
                            if($EvType==0) {
                                $tmpMatch["Id"] = $vPh['items'][0][(($vPh['items'][0]['id'] == $vItem['oppId']) ? 'oppBib' : 'bib')];
                                $tmpMatch["FamilyName"] = $vPh['items'][0][(($vPh['items'][0]['id'] == $vItem['oppId']) ? 'oppFamilyName' : 'familyName')];
                                $tmpMatch["GivenName"] = $vPh['items'][0][(($vPh['items'][0]['id'] == $vItem['oppId']) ? 'oppGivenName' : 'givenName')];
                                $tmpMatch["NameOrder"] = $vPh['items'][0][(($vPh['items'][0]['id'] == $vItem['oppId']) ? 'oppNameOrder' : 'nameOrder')];
                                $tmpMatch["Gender"] = $vPh['items'][0][(($vPh['items'][0]['id'] == $vItem['oppId']) ? 'oppGender' : 'gender')];
                            } else {
                                $tmpMatch["FamilyName"] = $vPh['items'][0][(($vPh['items'][0][$id] == $vItem[$oppId]) ? 'oppCountryName' : 'countryName')];
                            }
                            $tmpMatch["NOC"] = $vPh['items'][0][(($vPh['items'][0][$id] == $vItem[$oppId]) ? 'oppCountryCode' : 'countryCode')];
                            $tmpMatch["OppScore"] = $vPh['items'][0][(($vPh['items'][0][$id] == $vItem[$oppId]) ? 'oppIrmText' : 'irmText')];
                        }
                    } else {
                        if($EvType==0) {
                            $tmpMatch["Id"] = $vPh['items'][0][(($vPh['items'][0]['id'] == $vItem['oppId']) ? 'oppBib' : 'bib')];
                            $tmpMatch["FamilyName"] = $vPh['items'][0][(($vPh['items'][0]['id'] == $vItem['oppId']) ? 'oppFamilyName' : 'familyName')];
                            $tmpMatch["GivenName"] = $vPh['items'][0][(($vPh['items'][0]['id'] == $vItem['oppId']) ? 'oppGivenName' : 'givenName')];
                            $tmpMatch["NameOrder"] = $vPh['items'][0][(($vPh['items'][0]['id'] == $vItem['oppId']) ? 'oppNameOrder' : 'nameOrder')];
                            $tmpMatch["Gender"] = $vPh['items'][0][(($vPh['items'][0]['id'] == $vItem['oppId']) ? 'oppGender' : 'gender')];
                        } else {
                            $tmpMatch["FamilyName"] = $vPh['items'][0][(($vPh['items'][0][$id] == $vItem[$oppId]) ? 'oppCountryName' : 'countryName')];
                        }
                        $tmpMatch["NOC"] = $vPh['items'][0][(($vPh['items'][0][$id] == $vItem[$oppId]) ? 'oppCountryCode' : 'countryCode')];
                        $tmpMatch["Score"] = $vPh['items'][0][(($vPh['items'][0][$id] == $vItem[$oppId]) ? ($rnkMatchesData['sections'][$options['events']]['meta']['matchMode'] ? 'setScore' : 'score') : ($rnkMatchesData['sections'][$options['events']]['meta']['matchMode'] ? 'oppSetScore' : 'oppScore'))];
                        $tmpMatch["OppScore"] = $vPh['items'][0][(($vPh['items'][0][$id] == $vItem[$oppId]) ? ($rnkMatchesData['sections'][$options['events']]['meta']['matchMode'] ? 'oppSetScore' : 'oppScore') : ($rnkMatchesData['sections'][$options['events']]['meta']['matchMode'] ? 'setScore' : 'score'))];
                    }
                    $tmpR['Matches'][] = $tmpMatch;
                }


				$json_array['LeftOpponent'] = $tmpL;
				$json_array['RightOpponent'] = $tmpR;
			}
		}
	}
}

// Return the json structure with the callback function that is needed by the app
SendResult($json_array);
