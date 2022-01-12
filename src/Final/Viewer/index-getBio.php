<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Lib/CommonLib.php');

$JSON=array('error' => 1);


if(!CheckTourSession() OR GetTournamentIocCode()!='FITA') {
    JsonOut($JSON);
}

if (checkACL(array(AclTeams, AclIndividuals, AclOutput),AclNoAccess, false, $_SESSION['TourId'])==AclNoAccess) {
    JsonOut($JSON);
}

$Team=(isset($_REQUEST['Team']) ? intval($_REQUEST['Team']) : -1);
$Id1 = (isset($_REQUEST['Id1']) ? $_REQUEST['Id1'] : false);
$Id2 = (isset($_REQUEST['Id2']) ? $_REQUEST['Id2'] : false);
$Event = (isset($_REQUEST['Event']) ? $_REQUEST['Event'] : false);

if($Event) {
	// double check the WA event is correct
	$q=safe_r_sql("select if(EvWaCategory='', EvCode, EvWaCategory) as Event from Events where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event));
	if($r=safe_fetch($q)) {
		$Event=$r->Event;
	}
}

$H2HMatches = null;
$CatInformation = null;
if($Team) {
    if($Id1 !== false AND $Id2 !== false AND $Event !== false) {
        $rawData = file_get_contents($CFG->WaWrapper . "?v=3&RBP=All&content=COUNTRYMATCHES&CatCode={$Event}&Noc={$Id1}&Noc2={$Id2}");
        $H2HMatches = json_decode($rawData);
    }
    if($Id1 !== false AND $Event !== false) {
        $rawData = file_get_contents($CFG->WaWrapper . "?v=3&content=TEAMBIOGRAPHY&Noc={$Id1}&CatCode={$Event}&Detailed=1");
        if(($BioData=json_decode($rawData))!=null) {
            $JSON['error']=0;
            $JSON["BioL"] = bioTeam($BioData->items[0],$H2HMatches,'L',$Event);
        }
    }
    if($Id2 !== false AND $Event !== false) {
        $rawData = file_get_contents($CFG->WaWrapper . "?v=3&content=TEAMBIOGRAPHY&Noc={$Id2}&CatCode={$Event}&Detailed=1");
        if(($BioData=json_decode($rawData))!=null) {
            $JSON['error']=0;
            $JSON["BioR"] = bioTeam($BioData->items[0],$H2HMatches,'R',$Event);
        }
    }

} else {
    $rawData = file_get_contents($CFG->WaWrapper . "?v=3&RBP=All&content=CATEGORIES&CatCode={$Event}");
    $CatInformation =  json_decode($rawData);
    if(count($CatInformation->items) != 0) {
        $CatInformation = $CatInformation->items[0];
    } else {
        $CatInformation = null;
    }
    if($Id1 !== false AND $Id2 !== false) {
        $rawData = file_get_contents($CFG->WaWrapper . "?v=3&RBP=All&IndividualTeam=1&content=ATHLETEMATCHES&Id={$Id1}&Id2={$Id2}");
        $H2HMatches = json_decode($rawData);
    }
    if ($Id1 !== false) {
        $rawData = file_get_contents($CFG->WaWrapper . "?v=3&content=ATHLETEBIOGRAPHY&Id={$Id1}&Detailed=1");
        if (($BioData = json_decode($rawData)) != null) {
            if(count($BioData->items) != 0) {
                $JSON['error'] = 0;
                $JSON["BioL"] = bioInd($BioData->items[0], $H2HMatches, 'L', $CatInformation);
            }
        }
    }
    if ($Id2 !== false) {
        $rawData = file_get_contents($CFG->WaWrapper . "?v=3&content=ATHLETEBIOGRAPHY&Id={$Id2}&Detailed=1");
        if (($BioData = json_decode($rawData)) != null) {
            if(count($BioData->items) != 0) {
                $JSON['error'] = 0;
                $JSON["BioR"] = bioInd($BioData->items[0], $H2HMatches, 'R', $CatInformation);
            }
        }
    }
}
JsonOut($JSON);


function bioInd($data,$H2HMatches,$Side, $CatInformation) {
    $avg=0;
    $q=safe_r_SQL("select QuScore, QuHits, group_concat(trim(FinArrowstring) separator '') as FinArrowstring
		from Qualifications
		inner join Finals on FinAthlete=QuId
		inner join Entries on EnId=QuId
		where EnCode={$data->Id} AND EnTournament=" . $_SESSION['TourId']);
    if($r=safe_fetch($q) AND (($r->QuHits+strlen($r->FinArrowstring)!=0))) {
        $avg=round(($r->QuScore+ValutaArrowString($r->FinArrowstring))/($r->QuHits+strlen($r->FinArrowstring)), 3);
    }

    $tmp = '<div class="bioBox">';
    $tmp .= '<div class="btn-group btn-group-sm" role="group" id="names'.$Side.'"></div>';
    $tmp .= '<table class="table table-sm"><tbody>';
    //Age
    $tmp .= '<tr><td>Age: <b>'.$data->Age.'</b>';
    //World Ranking
    foreach ($data->WorldRankings->Current as $key => $WRank) {
        if(empty($CatInformation) OR ($WRank->CatCode==$CatInformation->Code)){
            $tmp .= '<br>World Rank:</b> ' . $WRank->Rnk . ' '  . $WRank->CatCode . '&nbsp;<i class="fa ' . ($WRank->Rnk < $WRank->RnkOld ? 'fa-level-up' : ($WRank->Rnk > $WRank->RnkOld ? 'fa-level-down' : 'fa-exchange')) . '"></i> ' . ($WRank->Rnk != $WRank->RnkOld ? '(' . ($WRank->RnkOld - $WRank->Rnk) . ')' : '');
        }
    }
    //Match/Tie stats
    $overallAvg=0;
    $overallBest=0;
    $tmp .= '<td>';
    if(!empty($data->Stats->Career)) {
        foreach ($data->Stats->Career as $key => $Stat) {
            if(empty($CatInformation) OR in_array($Stat->DivCode,$CatInformation->DivId)){
                $tmp .= 'Match won: <b>' . $Stat->MatchWin . '/' . $Stat->MatchTot . '</b> (' . $Stat->MatchWinPercentage . '%)<br>Ties won: <b>' . $Stat->TBWin . '/' . $Stat->TBTot . '</b> (' . $Stat->TBWinPercentage . '%)';

                if (!empty($Stat->AverageArr)) {
                    $overallAvg = max($overallAvg, $Stat->AverageArr);
                }
                if (!empty($Stat->QBest)) {
                    $overallBest = max($overallBest, $Stat->QBest);
                }
            }
        }
    }
    //Average and Best Score
    $seasonBest=0;
    if(!empty($data->Stats->Season) AND !empty($data->Stats->{$data->Stats->Season})) {
        foreach ($data->Stats->{$data->Stats->Season} as $key => $Stat) {
        	if(!empty($Stat->QBest)) {
                $seasonBest = max($seasonBest,$Stat->QBest);
	        }
        }
    }
    $tmp .= '</td></tr>';
    $tmp .= '<tr>';
    $tmp .= '<td>Overall Avg: <b>'.$overallAvg.'</b><br>Competition Avg.: <b>'.$avg.'</td>';
    $tmp .= '<td>Personal Best: <b>'.$overallBest.'</b><br>Season\'s Best: <b>'.$seasonBest.'</td>';
    $tmp .= '</tr>';

    // fetches the previous matches between the 2 opponents
    if ($H2HMatches != null) {
        $matchesList='';
        $TotalMatches = $H2HMatches->pageInfo->totalResults;
        $Wins = 0;
        $TB = 0;
        $WinsTB = 0;
        foreach ($H2HMatches->items as $Match) {
            if ($Match->Competitor1->Athlete->Id == $data->Id and $Match->Competitor1->WinLose) $Wins++;
            if ($Match->Competitor2->Athlete->Id == $data->Id and $Match->Competitor2->WinLose) $Wins++;
            if ($Match->Competitor1->TB !== '') {
                $TB++;
                if ($Match->Competitor1->Athlete->Id == $data->Id and $Match->Competitor1->WinLose) $WinsTB++;
                if ($Match->Competitor2->Athlete->Id == $data->Id and $Match->Competitor2->WinLose) $WinsTB++;
            }
            $matchesList .= '<tr class="'.((($Match->Competitor1->Athlete->Id == $data->Id and $Match->Competitor1->WinLose) OR ($Match->Competitor2->Athlete->Id == $data->Id and $Match->Competitor2->WinLose)) ? 'font-weight-bold' : '').'">'.
                '<td class="text-left text-nowrap">'.$Match->PhaseName.'</td><td class="text-left">'.$Match->CompName.'</td>'.
                '<td class="text-center text-nowrap">'.
                ($Match->Competitor1->Athlete->Id == $data->Id ? $Match->Competitor1->Score : $Match->Competitor2->Score).
                ($Match->Competitor1->TB ? ' <span class="small">T.'.($Match->Competitor1->Athlete->Id == $data->Id? $Match->Competitor1->TB : $Match->Competitor2->TB).'</span>':'').
                ' - '.
                ($Match->Competitor1->TB ? '<span class="small">T.'.($Match->Competitor1->Athlete->Id == $data->Id? $Match->Competitor2->TB : $Match->Competitor1->TB).'</span> ':'').
                ($Match->Competitor1->Athlete->Id == $data->Id ? $Match->Competitor2->Score : $Match->Competitor1->Score).
                '</td>'.
                '<td class="text-right text-nowrap">'.date('j M Y', strtotime($Match->CompDtTo)).'</td>'.
                '<td class="text-right">#'.($Match->Competitor1->Athlete->Id == $data->Id ? $Match->Competitor1->FinalRank : $Match->Competitor2->FinalRank).'</td></tr>';
        }

        $WinPercent = 0;
        $TiePercent = 0;
        if ($TotalMatches) $WinPercent = number_format(100 * $Wins / $TotalMatches, 0);
        if ($TB) $TiePercent = number_format(100 * $WinsTB / $TB, 0);

        $tmp .= '<tr>'.
            '<td>Match vs opponent won: <b>' . $Wins . '/' . $TotalMatches . '</b> (' . $WinPercent . '%)</td>'.
            '<td>Ties vs opponent won: <b>' . $WinsTB . '/' . $TB . '</b> (' . $TiePercent . '%)<i class="fa fa-lg fa-angle-double-up pull-right icoH2hDetail" onclick="toggleH2hDetails()"></i></td>'.
            '</tr>';
        if($TotalMatches) {
            $tmp .= '<tr class="tblH2hDetail"><td colspan="2">'.
                '<table class="table table-sm table-striped small"><thead><tr class="table-dark"><th>Phase</th><th>Place</th><th class="text-center">Score</th><th class="text-right">Date</th><th class="text-center"class="text-right">Rank</th></tr></thead>'.$matchesList.'</table>'.
                '</td></tr>';
        }

    }

    //Results
    $tmp .= '<tr>'.
        '<td colspan="2">'.
        '<span class="small"><input type="checkbox" class="mx-3" onclick="UpdateRows(this)" value="IsInd'.$data->Id .'" checked="checked">Individual<input type="checkbox" class="mx-3" onclick="UpdateRows(this)" value="IsTeam'.$data->Id.'" checked="checked">Team</span><br/>';
    foreach($data->Caps as $CatCode => $CatCaps) {
        foreach ($CatCaps as $Id => $Item) {
            $tmp .= '<span class="text-nowrap mr-2 small"><input type="checkbox" onclick="UpdateRows(this)" value="Lev' . $Id . $data->Id . '" checked="checked">' . $Item->Count . ' x ' . $Item->LevelName . '</span> ';
        }
    }
    $tmp .= '</td></tr>';

    $Level=0;
    $IndWins=0;
    $IndPodiums=0;
    $TeamWins=0;
    $TeamPodiums=0;
    $COMPS='';
    foreach($data->Medals as $medal) {
        if($Level!=$medal->ComLevel) {
            $COMPS = str_replace(array('^^^','$$$','+++','°°°'), array($IndWins, $IndPodiums, $TeamWins, $TeamPodiums), $COMPS);
            $Level=$medal->ComLevel;
            $COMPS.= '<tr class="table-dark Lev'.$Level.$data->Id.'"><th colspan="4">'.$medal->ComLevelDescr.' - Ind. Wins ^^^ / Podiums $$$ - Team Wins +++ / Podiums °°°</th></tr>';
		    $IndWins=0;
		    $IndPodiums=0;
		    $TeamWins=0;
		    $TeamPodiums=0;
        }
        if($medal->IsTeam) {
            $TeamPodiums++;
            if($medal->Rnk==1) {
                $TeamWins++;
            }
        } else {
            $IndPodiums++;
            if($medal->Rnk==1) {
                $IndWins++;
            }
        }
        $COMPS.= '<tr class="Lev'.$Level.$data->Id . ' ' . ($medal->IsTeam ? 'IsTeam'.$data->Id :'IsInd'.$data->Id ) .'">
			<td class="text-left">'.$medal->Rnk.'</td>
			<td class="text-left text-nowrap">'.$medal->Cat.($medal->IsTeam ? ' Team' : '').'</td>
			<td class="text-left">'.$medal->ComName.' ('.$medal->ComNOC.')</td>
			<td class="text-right text-nowrap">'.date('j M Y', strtotime($medal->Date)).'</td>
			</tr>';
    }


    $COMPS = str_replace(array('^^^','$$$','+++','°°°'), array($IndWins, $IndPodiums, $TeamWins, $TeamPodiums), $COMPS);
    $tmp .= '<tr><td colspan="2" class="bioBox"><table class="table table-sm table-striped small">'.$COMPS.'</table></td></tr>';

    $tmp .= '</tbody></table></div>';

    return $tmp;
}

function bioTeam($data,$H2HMatches,$Side,$Event) {
    $avg=0;
    $q=safe_r_SQL("select TeScore, TeHits, group_concat(trim(TfArrowstring) separator '') as TfArrowstring
		from Teams 
		inner join Countries on TeCoId=CoId and TeFinEvent=1
		inner join TeamFinals on TfEvent=TeEvent and TfTournament=TeTournament and TfTeam=TeCoId and TfSubTeam=TeSubTeam
		where CoCode='{$data->Id}' AND TeEvent='{$Event}' AND TeTournament={$_SESSION['TourId']} 
		group by TeCoId, TeSubTeam, TeEvent");
    if($r=safe_fetch($q) AND ($r->TeHits+strlen($r->TfArrowstring)!=0)) {
        $avg=round(($r->TeScore+ValutaArrowString($r->TfArrowstring))/($r->TeHits+strlen($r->TfArrowstring)), 3);
    }

    $tmp = '<div class="bioBox">';
    $tmp .= '<div class="btn-group btn-group-sm" role="group" id="names'.$Side.'"></div>';
    $tmp .= '<table class="table table-sm"><tbody>';
    //Age
    $tmp .= '<tr><td>ContinentalAssociation: <b>'.$data->ContinentalAssoc.'</b>';
    //World Ranking
    foreach ($data->WorldRankings->Current as $key => $WRank) {
        $tmp .= '<br>World Rank:</b> ' . $WRank->Rnk . ' '  . $WRank->CatCode . '&nbsp;<i class="fa ' . ($WRank->Rnk < $WRank->RnkOld ? 'fa-level-up' : ($WRank->Rnk > $WRank->RnkOld ? 'fa-level-down' : 'fa-exchange')) . '"></i> ' . ($WRank->Rnk != $WRank->RnkOld ? '(' . ($WRank->RnkOld - $WRank->Rnk) . ')' : '');
    }
    //Match/Tie stats
    $overallAvg=0;
    $overallBest=0;
    $tmp .= '<td>';
    if(!empty($data->Stats->Career)) {
        foreach ($data->Stats->Career as $key => $Stat) {
            if(empty($CatInformation) OR in_array($Stat->DivCode,$CatInformation->DivId)){
                $tmp .= 'Match won: <b>' . (empty($Stat->MatchWin) ? 0 : $Stat->MatchWin) . '/' . (empty($Stat->MatchTot) ? 0 : $Stat->MatchTot) . '</b> (' . (empty($Stat->MatchWinPercentage) ? 0 : $Stat->MatchWinPercentage) . '%)<br>Ties won: <b>' . (empty($Stat->TBWin) ? 0 : $Stat->TBWin) . '/' . (empty($Stat->TBTot) ? 0 : $Stat->TBTot) . '</b> (' . (empty($Stat->TBWinPercentage) ? 0 : $Stat->TBWinPercentage) . '%)';

                if (!empty($Stat->AverageArr)) {
                    $overallAvg = max($overallAvg, $Stat->AverageArr);
                }
                if (!empty($Stat->QBest)) {
                    $overallBest = max($overallBest, $Stat->QBest);
                }
            }
        }
    }
    //Average and Best Score
    $seasonBest=0;
    if(!empty($data->Stats->Season) AND !empty($data->Stats->{$data->Stats->Season})) {
        foreach ($data->Stats->{$data->Stats->Season} as $key => $Stat) {
            $seasonBest = max($seasonBest,(empty($Stat->QBest) ? 0 : $Stat->QBest));
        }
    }
    $tmp .= '</td></tr>';
    $tmp .= '<tr>';
    $tmp .= '<td>Overall Avg: <b>'.$overallAvg.'</b><br>Competition Avg.: <b>'.$avg.'</td>';
    $tmp .= '<td>Personal Best: <b>'.$overallBest.'</b><br>Season\'s Best: <b>'.$seasonBest.'</td>';
    $tmp .= '</tr>';

    // fetches the previous matches between the 2 opponents
    if ($H2HMatches != null) {
        $matchesList='';
        $TotalMatches = $H2HMatches->pageInfo->totalResults;
        $Wins = 0;
        $TB = 0;
        $WinsTB = 0;
        foreach ($H2HMatches->items as $Match) {
            if ($Match->Competitor1->Athlete->NOC == $data->NOC and $Match->Competitor1->WinLose) $Wins++;
            if ($Match->Competitor2->Athlete->NOC == $data->NOC and $Match->Competitor2->WinLose) $Wins++;
            if ($Match->Competitor1->TB !== '') {
                $TB++;
                if ($Match->Competitor1->Athlete->NOC == $data->NOC and $Match->Competitor1->WinLose) $WinsTB++;
                if ($Match->Competitor2->Athlete->NOC == $data->NOC and $Match->Competitor2->WinLose) $WinsTB++;
            }
            $matchesList .= '<tr class="'.((($Match->Competitor1->Athlete->Id == $data->Id and $Match->Competitor1->WinLose) OR ($Match->Competitor2->Athlete->Id == $data->Id and $Match->Competitor2->WinLose)) ? 'font-weight-bold' : '').'">'.
                '<td class="text-left text-nowrap">'.$Match->PhaseName.'</td><td class="text-left">'.$Match->CompName.'</td>'.
                '<td class="text-center text-nowrap">'.
                ($Match->Competitor1->Athlete->NOC == $data->NOC ? $Match->Competitor1->Score : $Match->Competitor2->Score).
                ($Match->Competitor1->TB ? ' <span class="small">T.'.($Match->Competitor1->Athlete->NOC == $data->NOC ? $Match->Competitor1->TB : $Match->Competitor2->TB).'</span>':'').
                ' - '.
                ($Match->Competitor1->TB ? '<span class="small">T.'.($Match->Competitor1->Athlete->NOC == $data->NOC ? $Match->Competitor2->TB : $Match->Competitor1->TB).'</span> ':'').
                ($Match->Competitor1->Athlete->NOC == $data->NOC ? $Match->Competitor2->Score : $Match->Competitor1->Score).
                '</td>'.
                '<td class="text-right text-nowrap">'.date('j M Y', strtotime($Match->CompDtTo)).'</td>'.
                '<td class="text-right">#'.($Match->Competitor1->Athlete->NOC == $data->NOC ? $Match->Competitor1->FinalRank : $Match->Competitor2->FinalRank).'</td></tr>';

        }

        $WinPercent = 0;
        $TiePercent = 0;
        if ($TotalMatches) $WinPercent = number_format(100 * $Wins / $TotalMatches, 0);
        if ($TB) $TiePercent = number_format(100 * $WinsTB / $TB, 0);

        $tmp .= '<tr>'.
            '<td>Match vs opponent won: <b>' . $Wins . '/' . $TotalMatches . '</b> (' . $WinPercent . '%)</td>'.
            '<td>Ties vs opponent won: <b>' . $WinsTB . '/' . $TB . '</b> (' . $TiePercent . '%)<i class="fa fa-lg fa-angle-double-up pull-right icoH2hDetail" onclick="toggleH2hDetails()"></i></td>'.
            '</tr>';
        if($TotalMatches) {
            $tmp .= '<tr class="tblH2hDetail"><td colspan="2">'.
                '<table class="table table-sm table-striped small"><thead><tr class="table-dark"><th>Phase</th><th>Place</th><th class="text-center">Score</th><th class="text-right">Date</th><th class="text-center"class="text-right">Rank</th></tr></thead>'.$matchesList.'</table>'.
                '</td></tr>';
        }
    }

      //Results
    $tmp .= '<tr><td colspan="2">';
    foreach($data->Caps as $CatCode => $CatCaps) {
        foreach ($CatCaps as $Id => $Item) {
            $tmp .= '<span class="text-nowrap mr-2 small"><input type="checkbox" onclick="UpdateRows(this)" value="Lev' . $Id . $data->Id . '" checked="checked">' . $Item->Count . ' x ' . $Item->LevelName . '</span> ';
        }
    }
    $tmp .= '</td></tr>';

    $Level=0;
    $COMPS='';
    foreach($data->Medals as $medal) {
        if($Level!=$medal->ComLevel) {
            $Level=$medal->ComLevel;
            $COMPS .= '<tr class="table-dark Lev'.$Level.$data->Id.'"><th colspan="4" class="Title">'.$medal->ComLevelDescr.'</th></tr>';
        }
        $COMPS .= '<tr class="Lev'.$Level.$data->Id.'">
			<td class="text-left">'.$medal->Rnk.'</td>
			<td class="text-left text-nowrap">'.$medal->Cat.' Team</td>
			<td class="text-left">'.$medal->ComName.' ('.$medal->ComNOC.')</td>
			<td class="text-right text-nowrap">'.date('j M Y', strtotime($medal->Date)).'</td>
			</tr>';
    }
    $tmp .= '<tr><td colspan="2" class="bioBox"><table class="table table-sm table-striped small">'.$COMPS.'</table></td></tr>';

    $tmp .= '</tbody></table></div>';

    return $tmp;
}
