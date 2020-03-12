<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Lib/ArrTargets.inc.php');

$JSON=array('error' => 1);


if(!CheckTourSession()) {
    JsonOut($JSON);
}

if (checkACL(array(AclTeams, AclIndividuals, AclOutput),AclNoAccess, false, $_SESSION['TourId'])==AclNoAccess) {
    JsonOut($JSON);
}

$Team=(isset($_REQUEST['Team']) ? intval($_REQUEST['Team']) : -1);
$Id1 = (isset($_REQUEST['Id1']) ? $_REQUEST['Id1'] : false);
$Id2 = (isset($_REQUEST['Id2']) ? $_REQUEST['Id2'] : false);
$Event = (isset($_REQUEST['Event']) ? $_REQUEST['Event'] : false);


$H2HMatches = null;
if($Team) {
    if($Id1 !== false AND $Id2 !== false AND $Event !== false) {
        $rawData = file_get_contents($CFG->WaWrapper . "?v=3&RBP=All&content=COUNTRYMATCHES&CatCode={$Event}&Noc={$Id1}&Noc2={$Id2}");
        $H2HMatches = json_decode($rawData);
    }
    if($Id1 !== false AND $Event !== false) {
        $rawData = file_get_contents($CFG->WaWrapper . "?v=3&content=BIOTEAM&ID=" . $Id1 ."&CAT=" . $Event);
        if(($BioData=json_decode($rawData))!=null) {
            $JSON['error']=0;
            $JSON["BioL"] = bioTeam($BioData->items[0],$H2HMatches,'L');
        }
    }
    if($Id2 !== false AND $Event !== false) {
        $rawData = file_get_contents($CFG->WaWrapper . "?v=3&content=BIOTEAM&ID=" . $Id2 ."&CAT=" . $Event);
        if(($BioData=json_decode($rawData))!=null) {
            $JSON['error']=0;
            $JSON["BioR"] = bioTeam($BioData->items[0],$H2HMatches,'R');
        }
    }

} else {
    if($Id1 !== false AND $Id2 !== false) {
        $rawData = file_get_contents($CFG->WaWrapper . "?v=3&RBP=All&content=ATHLETEMATCHES&Id={$Id1}&Id2={$Id2}");
        $H2HMatches = json_decode($rawData);
    }
    if ($Id1 !== false) {
        $rawData = file_get_contents($CFG->WaWrapper . "?v=3&content=BIODET&ID=" . $Id1);
        if (($BioData = json_decode($rawData)) != null) {
            $JSON['error'] = 0;
            $JSON["BioL"] = bioInd($BioData->items[0],$H2HMatches,'L');
        }
    }
    if ($Id2 !== false) {
        $rawData = file_get_contents($CFG->WaWrapper . "?v=3&content=BIODET&ID=" . $Id2);
        if (($BioData = json_decode($rawData)) != null) {
            $JSON['error'] = 0;
            $JSON["BioR"] = bioInd($BioData->items[0],$H2HMatches,'R');
        }
    }
}

JsonOut($JSON);


function bioInd($data,$H2HMatches,$Side) {
    $avg=0;
    $q=safe_r_SQL("select QuScore, QuHits, group_concat(trim(FinArrowstring) separator '') as FinArrowstring
		from Qualifications
		inner join Finals on FinAthlete=QuId
		inner join Entries on EnId=QuId
		where EnCode={$data->Id} AND EnTournament=" . $_SESSION['TourId']);
    if($r=safe_fetch($q)) {
        $avg=round(($r->QuScore+ValutaArrowString($r->FinArrowstring))/($r->QuHits+strlen($r->FinArrowstring)), 3);
    }

    $tmp = '<div class="bioBox">';
    $tmp .= '<div class="btn-group btn-group-sm" role="group" id="names'.$Side.'"></div>';
    $tmp .= '<table class="table table-sm"><tbody>';
    $tmp .= '<tr>'.
            '<td>Age: <b>'.$data->Age.'</b><br>World Rank:</b> '.$data->Rnk.' '.$data->Cat.'&nbsp;<i class="fa '. ($data->Rnk<$data->RnkOld ? 'fa-level-up' : ($data->Rnk>$data->RnkOld ? 'fa-level-down' : 'fa-exchange')) . '"></i> '.($data->Rnk!=$data->RnkOld ? '(' . ($data->RnkOld-$data->Rnk) . ')': '').'</td>'.
            '<td>Match won: <b>'.$data->MatchWin.'/'.$data->MatchTot.'</b> ('.$data->MatchWinPercentage.'%)<br>Ties won: <b>'.$data->TBWin.'/'.$data->TBTot.'</b> ('.$data->TBWinPercentage.'%)</td>'.
        '</tr>'.
        '<tr>'.
        '<td>Overall Avg: <b>'.$data->AverageArr.'</b><br>Competition Avg.: <b>'.$avg.'</td>'.
        '<td>Personal Best: <b>'.$data->QCareer.'</b><br>Season\'s Best: <b>'.$data->QSeason.'</td>'.
        '</tr>';

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
    foreach($data->caps as $Id => $Item) {
        $tmp .=  '<span class="text-nowrap mr-2 small"><input type="checkbox" onclick="UpdateRows(this)" value="Lev'.$Id.$data->Id.'" checked="checked">'.$Item[0].' x '.$Item[1].'</span> ';
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
            $COMPS.= '<tr class="table-dark Lev'.$Level.$data->Id.'"><th colspan="4">'.$data->CompetitionLevels->{$Level}.' - Ind. Wins ^^^ / Podiums $$$ - Team Wins +++ / Podiums °°°</th></tr>';
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

function bioTeam($data,$H2HMatches,$Side) {
    $avg=0;
    $q=safe_r_SQL("select TeScore, TeHits, group_concat(trim(TfArrowstring) separator '') as TfArrowstring
		from Teams 
		inner join Countries on TeCoId=CoId and TeFinEvent=1
		inner join TeamFinals on TfEvent=TeEvent and TfTournament=TeTournament and TfTeam=TeCoId and TfSubTeam=TeSubTeam
		where CoCode='{$data->Id}' AND TeEvent='{$data->Cat}' AND TeTournament={$_SESSION['TourId']} 
		group by TeCoId, TeSubTeam, TeEvent");
    if($r=safe_fetch($q)) {
        $avg=round(($r->TeScore+ValutaArrowString($r->TfArrowstring))/($r->TeHits+strlen($r->TfArrowstring)), 3);
    }

    $tmp = '<div class="bioBox">';
    $tmp .= '<div class="btn-group btn-group-sm" role="group" id="names'.$Side.'"></div>';
    $tmp .= '<table class="table table-sm"><tbody>';
    $tmp .= '<tr>'.
        '<td>Joined WA: <b>'.(empty($data->Joined) ?  $data->Founded : $data->Joined).'</b><br>World Rank:</b> '.$data->Rnk.' '.$data->Cat.'&nbsp;<i class="fa '. ($data->Rnk<$data->RnkOld ? 'fa-level-up' : ($data->Rnk>$data->RnkOld ? 'fa-level-down' : 'fa-exchange')) . '"></i> '.($data->Rnk!=$data->RnkOld ? '(' . ($data->RnkOld-$data->Rnk) . ')': '').'</td>'.
        '<td>Match won: <b>'.$data->MatchWin.'/'.$data->MatchTot.'</b> ('.$data->MatchWinPercentage.'%)<br>Ties won: <b>'.$data->TBWin.'/'.$data->TBTot.'</b> ('.$data->TBWinPercentage.'%)</td>'.
        '</tr>'.
        '<tr>'.
        '<td>Overall Avg: <b>'.$data->AverageArr.'</b><br>Competition Avg.: <b>'.$avg.'</td>'.
        '<td>Personal Best: <b>'.$data->QCareer.'</b><br>Season\'s Best: <b>'.$data->QSeason.'</td>'.
        '</tr>';

    // fetches the previous matches between the 2 opponents
    if ($H2HMatches != null) {
        $matchesList='';
        $TotalMatches = $H2HMatches->pageInfo->totalResults;
        $Wins = 0;
        $TB = 0;
        $WinsTB = 0;
        foreach ($H2HMatches->items as $Match) {
            if ($Match->Competitor1->Athlete->NOC == $data->Id and $Match->Competitor1->WinLose) $Wins++;
            if ($Match->Competitor2->Athlete->NOC == $data->Id and $Match->Competitor2->WinLose) $Wins++;
            if ($Match->Competitor1->TB !== '') {
                $TB++;
                if ($Match->Competitor1->Athlete->NOC == $data->Id and $Match->Competitor1->WinLose) $WinsTB++;
                if ($Match->Competitor2->Athlete->NOC == $data->Id and $Match->Competitor2->WinLose) $WinsTB++;
            }
            $matchesList .= '<tr class="'.((($Match->Competitor1->Athlete->NOC == $data->Id and $Match->Competitor1->WinLose) OR ($Match->Competitor2->Athlete->NOC == $data->Id and $Match->Competitor2->WinLose)) ? 'font-weight-bold' : '').'">'.
                '<td class="text-left text-nowrap">'.$Match->PhaseName.'</td><td class="text-left">'.$Match->CompName.'</td>'.
                '<td class="text-center text-nowrap">'.
                ($Match->Competitor1->Athlete->NOC == $data->Id ? $Match->Competitor1->Score : $Match->Competitor2->Score).
                ($Match->Competitor1->TB ? ' <span class="small">T.'.($Match->Competitor1->Athlete->NOC == $data->Id? $Match->Competitor1->TB : $Match->Competitor2->TB).'</span>':'').
                ' - '.
                ($Match->Competitor1->TB ? '<span class="small">T.'.($Match->Competitor1->Athlete->NOC == $data->Id? $Match->Competitor2->TB : $Match->Competitor1->TB).'</span> ':'').
                ($Match->Competitor1->Athlete->NOC == $data->Id ? $Match->Competitor2->Score : $Match->Competitor1->Score).
                '</td>'.
                '<td class="text-right text-nowrap">'.date('j M Y', strtotime($Match->CompDtTo)).'</td>'.
                '<td class="text-right">#'.($Match->Competitor1->Athlete->NOC == $data->Id ? $Match->Competitor1->FinalRank : $Match->Competitor2->FinalRank).'</td></tr>';

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
        '<td colspan="2">';
    foreach($data->caps as $Id => $Item) {
        $tmp .=  '<span class="text-nowrap mr-2 small"><input type="checkbox" onclick="UpdateRows(this)" value="Lev'.$Id.$data->Id.'" checked="checked">'.$Item[0].' x '.$Item[1].'</span> ';
    }
    $tmp .= '</td></tr>';

    $Level=0;
    $COMPS='';
    foreach($data->Medals as $medal) {
        if($Level!=$medal->ComLevel) {
            $Level=$medal->ComLevel;
            $COMPS .= '<tr class="table-dark Lev'.$Level.$data->Id.'"><th colspan="4" class="Title">'.$data->CompetitionLevels->{$Level}.'</th></tr>';
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