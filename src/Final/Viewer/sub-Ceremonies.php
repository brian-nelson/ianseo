<?php

$arrPosition=array('','1st','2nd','3rd','4th','5th');
$ReversedCountries="if(EnNameOrder=1, CONCAT(UPPER(EnFirstName), ' ', EnName), CONCAT(EnName, ' ', UPPER(EnFirstName)))";
$par_RepresentCountry = getModuleParameter('Awards','RepresentCountry',1);
$par_PlayAnthem = getModuleParameter('Awards','PlayAnthem',1);
$par_ShowPoints = getModuleParameter('Awards','ShowPoints',0);
$FirstLang=(getModuleParameter('Awards', 'FirstLanguageCode') ? getModuleParameter('Awards', 'FirstLanguageCode') : ($_SESSION['TourPrintLang'] ? $_SESSION['TourPrintLang'] : SelectLanguage()));
$SecondLang=getModuleParameter('Awards', 'SecondLanguageCode');
$JSON['OppLeft']=$FirstLang;
$JSON['OppRight']=$SecondLang;

$Sql = "SELECT AwAwarderGrouping, AwPositions, EnId, concat(EvTeamEvent,EvCode) EvCode, concat(EvCode,EvTeamEvent) EventTranslation, CoCode, $ReversedCountries AS Athlete, 
    CONCAT(" . ($_SESSION["ISORIS"] ? '' : "CoCode, ' ', ") . "if(CoNameComplete>'', if(ToLocRule='FR', concat(CoName, ' (',CoNameComplete,')'), CoNameComplete), CoName)) AS Country, EvEventName as Category, 1 as Counter,
    IF(EvFinalFirstPhase=0,IndRank,ABS(IndRankFinal)) as `Rank`, QuScore AS Score, QuGold AS Gold,QuXnine AS XNine, AwDescription, AwAwarders
    FROM Tournament
    INNER JOIN Entries ON ToId=EnTournament
    INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament AND EnTournament={$TourId}
    INNER JOIN Qualifications ON EnId=QuId
    INNER JOIN Individuals ON EnId=IndId AND EnTournament=IndTournament
    INNER JOIN Events ON IndEvent=EvCode AND EvTournament=ToId AND EvTeamEvent=0
    INNER JOIN Awards ON EnTournament=AwTournament AND EvCode LIKE AwEvent AND AwFinEvent=1 AND AwTeam=0 AND AwUnrewarded=0 AND INSTR(AwPositions,IF(EvFinalFirstPhase=0,IndRank,ABS(IndRankFinal)))!=0
    WHERE  EnAthlete=1 AND EnIndFEvent=1 AND EnStatus <= 1 AND QuScore != 0 AND ToId={$TourId}
    AND AwEvent='{$Event}' AND AwFinEvent=1 AND AwTeam=0
    ORDER BY EvProgr, EvCode, INSTR(AwPositions,IF(EvFinalFirstPhase=0,IndRank,ABS(IndRankFinal))) ASC, EnFirstName, EnName ";
if($Team) {
    $TeamComponent="LEFT JOIN TeamFinComponent  AS tfc ON TeCoId=tfc.TfcCoId AND TeEvent=tfc.TfcEvent AND TeTournament=tfc.TfcTournament AND TeSubTeam=tfc.tfcSubTeam AND TeFinEvent=1
        LEFT JOIN Entries ON TfcId=EnId";
    $TeamComponentOrder="TfcOrder";
    $Sql = " SELECT AwAwarderGrouping, AwPositions, CoCode, concat(EvTeamEvent,EvCode) EvCode, concat(EvCode, EvTeamEvent) EventTranslation, CoId, 
        CONCAT(" . ($_SESSION["ISORIS"] ? '' : "CoCode, ' ', ") . "if(CoNameComplete>'', if(ToLocRule='FR', concat(CoName, ' (',CoNameComplete,')'), CoNameComplete), CoName), IF(TeSubTeam=0,'',CONCAT(' (',TeSubTeam,')'))) as Country, EvEventName as Category,
        EnId, group_concat($ReversedCountries order by EnSex DESC, EnFirstName, EnName separator '|') AS Athlete, Q as Counter,
        IF(EvFinalFirstPhase=0,TeRank,TeRankFinal) as `Rank`, IF(EvFinalFirstPhase=0,TeScore,IFNULL(TfScore,'')) as Score, IF(EvFinalFirstPhase=0,TeGold,'') as Gold, IF(EvFinalFirstPhase=0,TeXnine,'') AS XNine, AwDescription, AwAwarders
        FROM Tournament
        INNER JOIN Teams ON ToId=TeTournament AND TeFinEvent=1
        INNER JOIN Countries ON TeCoId=CoId AND TeTournament=CoTournament
        INNER JOIN
            (SELECT TcCoId, TcEvent, TcTournament, TcFinEvent, TcSubTeam, COUNT(TcId) as Q
                FROM TeamComponent
                GROUP BY TcCoId, TcEvent, TcTournament, TcFinEvent, TcSubTeam
            ) AS sq ON TeCoId=sq.TcCoId AND TeEvent=sq.TcEvent AND TeTournament=sq.TcTournament AND TeFinEvent=sq.TcFinEvent AND TeSubTeam=sq.TcSubTeam 
        $TeamComponent
        LEFT JOIN TeamFinals ON TfEvent=TeEvent AND TfTournament=TeTournament AND TfMatchNo<4 AND TfTeam=TeCoId AND TfSubTeam=TeSubTeam
        INNER JOIN Events ON TeEvent=EvCode AND EvTournament=ToId AND EvTeamEvent=1
        INNER JOIN Awards ON AwTournament=ToId AND TeEvent like AwEvent AND AwFinEvent=1 AND AwTeam=1 AND AwUnrewarded=0 AND INSTR(AwPositions,IF(EvFinalFirstPhase=0,TeRank,TeRankFinal))!=0
        WHERE ToId={$TourId}
        AND AwEvent='{$Event}' AND AwFinEvent=1 AND AwTeam=1
        group by EvCode, CoId, TeSubTeam
        ORDER BY EvProgr, TeEvent, INSTR(AwPositions,IF(EvFinalFirstPhase=0,TeRank,TeRankFinal)) ASC, CoCode ASC, $TeamComponentOrder ";
}

$q = safe_r_sql($Sql);
$AwPositions = '';

$curAward = '';
$tmpAwarders = array();
$tmpAward = '';
$Category = '';
$tmpEvent = '';
$data=array();

while ($r = safe_fetch($q)) {
    if ($r->AwAwarderGrouping) {
        $Awarders = @unserialize($r->AwAwarderGrouping);
    }
    $AwPositions = $r->AwPositions;
    $tmpAward = $r->AwDescription;
    $Category = $r->Category;
    $tmpEvent = $r->EventTranslation;
    $tmpAwarders = $Awarders;

    if ($Team) {
        $tmp = explode('|', $r->Athlete);
        $data[] = array($r->Rank, $tmp, $r->Country, $r->CoCode, $r->EvCode, $r->Score, $r->Gold, $r->XNine);
    } else {
        $data[] = array($r->Rank, $r->Athlete, $r->Country, $r->CoCode, $r->EvCode, $r->Score, $r->Gold, $r->XNine);
    }
}
if(count($data)>0) {
    $Ceremonies[0] = '<div class="ceremoniesDiv">' . get_text_eval(getModuleParameter('Awards', 'Aw-Intro-1'), $Category) . '</div>';
    if ($SecondLang) {
        $Ceremonies[1] = '<div class="ceremoniesDiv">' . get_text_eval(getModuleParameter('Awards', 'Aw-Intro-2'), $Category) . '</div>';
    }

    $Special1 = '';
    $Special2 = '';
    foreach ($Awarders as $k => $v) {
        if (!is_numeric($k)) {
            $Special1 = get_text_eval(getModuleParameter('Awards', 'Aw-Special-1'), getModuleParameter('Awards', 'Aw-Awarder-1-' . $v));
            $Special2 = get_text_eval(getModuleParameter('Awards', 'Aw-Special-2'), getModuleParameter('Awards', 'Aw-Awarder-2-' . $v));
            continue;
        }
        $Name = '';
        $Title = '';
        if(getModuleParameter('Awards', 'Aw-Awarder-1-' . $v)) {
            list($Name, $Title) = @explode(',', getModuleParameter('Awards', 'Aw-Awarder-1-' . $v), 2);
        }
        $Ceremonies[0] .= '<div class="ceremoniesDiv">' . get_text_eval(getModuleParameter('Awards', 'Aw-Award-1-' . $k), $Title) . '</div>';
        $Ceremonies[0] .= '<div class="ceremoniesHighLight">' . $Name . '</div>';
        if ($SecondLang) {
            $Title = '';
            if(getModuleParameter('Awards', 'Aw-Awarder-2-' . $v)) {
                list(, $Title) = @explode(',', getModuleParameter('Awards', 'Aw-Awarder-2-' . $v), 2);
            }
            $Ceremonies[1] .= '<div class="ceremoniesDiv">' . get_text_eval(getModuleParameter('Awards', 'Aw-Award-2-' . $k), $Title) . '</div>';
            $Ceremonies[1] .= '<div class="ceremoniesHighLight">&nbsp;</div>';
        }
    }

    $WinNat = '';
    for ($i = count($data) - 1; $i >= 0; $i--) {
        $Club1 = '';
        $Club2 = '';
        if ($par_RepresentCountry) {
            $Club1 = (get_text($data[$i][3], 'IOC_Codes', '', '1', '', $FirstLang) == $data[$i][3] ? $data[$i][2] : get_text($data[$i][3], 'IOC_Codes', '', '', '', $FirstLang));
            $Club2 = (get_text($data[$i][3], 'IOC_Codes', '', '1', '', $SecondLang) == $data[$i][3] ? $data[$i][2] : get_text($data[$i][3], 'IOC_Codes', '', '', '', $SecondLang));
        }

        if ($Team) {
            $ath = implode(' - ', $data[$i][1]);
        } else {
            $ath = $data[$i][1];
        }

        if ($AwPositions != '1,2,4,3' or $data[$i][0] != 4) {
            $Ceremonies[0] .= '<hr class="spacer">';
            if ($SecondLang) {
                $Ceremonies[1] .= '<hr class="spacer">';
            }
            $med1 = '';
            $med2 = '';
            if (is_numeric($data[$i][0])) {
                $med1 = getModuleParameter('Awards', 'Aw-Med' . $data[$i][0] . '-1');
                $med2 = getModuleParameter('Awards', 'Aw-Med' . $data[$i][0] . '-2');
            } else {
                $WinNat = $data[$i][3];
                $par_PlayAnthem = false;
                $med1 = getModuleParameter('Awards', 'Aw-CustomPrize-1-' . $data[$i][0][0]);
                $med2 = getModuleParameter('Awards', 'Aw-CustomPrize-2-' . $data[$i][0][0]);
            }
            $Ceremonies[0] .= '<div class="ceremoniesDiv">' . get_text_eval($med1, $Club1);
            if ($SecondLang) {
                $Ceremonies[1] .= '<div class="ceremoniesDiv">' . get_text_eval($med2, $Club2);
            }
        }

        if ($par_RepresentCountry and $Club1) {
            if ($Club1 = get_text($data[$i][3], 'IOC_Codes', '', '', '', $FirstLang, false)) {
                $Club2 = get_text($data[$i][3], 'IOC_Codes', '', '', '', $SecondLang, false);
            } else {
                $Club1 = (get_text($data[$i][3], 'IOC_Codes', '', '1', '', $FirstLang) == $data[$i][3] ? $data[$i][2] : get_text($data[$i][3], 'IOC_Codes', '', '', '', $pdf->FirstLang));
                $Club2 = (get_text($data[$i][3], 'IOC_Codes', '', '1', '', $SecondLang) == $data[$i][3] ? $data[$i][2] : get_text($data[$i][3], 'IOC_Codes', '', '', '', $pdf->SecondLang));
            }

            $Ceremonies[0] .= '<br>' . get_text_eval(getModuleParameter('Awards', 'Aw-representing-1'), $Club1);
            if ($SecondLang) {
                $Ceremonies[1] .= '<br>' . get_text_eval(getModuleParameter('Awards', 'Aw-representing-2'), $Club2);
            }
        }


        if ($par_ShowPoints) {
            $Ceremonies[0] .= '<br>' . get_text_eval('Points') . ' ' . $data[$i][5] . '; ' . get_text_eval('Golds') . ' ' . $data[$i][6] . '; ' . get_text_eval('XNine') . ' ' . $data[$i][7];
            if ($SecondLang) {
                $Ceremonies[1] .= '<br>&nbsp;';
            }
        }
        $Ceremonies[0] .= '</div>';
        if ($SecondLang) {
            $Ceremonies[1] .= '</div>';
        }


        if ($data[$i][0] == 1) {
            $WinNat = $data[$i][3];
        }

        if (is_array($ath)) {
            $Ceremonies[0] .= '<div class="ceremoniesHighLight">' . $ath[0] . '</div>';
            if ($SecondLang) {
                $Ceremonies[1] .= '<div class="ceremoniesHighLight">' . $ath[1] . '</div>';
            }
        } else {
            $Ceremonies[0] .= '<div class="ceremoniesHighLight">' . $ath . '</div>';
            if ($SecondLang) {
                $Ceremonies[1] .= '<div class="ceremoniesHighLight">&nbsp;</div>';
            }
        }
    }
    if ($par_PlayAnthem) {
        $Ceremonies[0] .= '<hr class="spacer">';
        if ($SecondLang) {
            $Ceremonies[1] .= '<hr class="spacer">';
        }
        if ($data[0][3] == 'TPE') {
            $Ceremonies[0] .= '<div class="ceremoniesDiv">' . get_text_eval(getModuleParameter('Awards', 'Aw-Anthem-TPE-1')) . '</div>';
            if ($SecondLang) {
                $Ceremonies[1] .= '<div class="ceremoniesDiv">' . get_text_eval(getModuleParameter('Awards', 'Aw-Anthem-TPE-2')) . '</div>';
            }
        } else {
            $Ceremonies[0] .= '<div class="ceremoniesDiv">' . get_text_eval(getModuleParameter('Awards', 'Aw-Anthem-1')) . '</div>';
            if ($SecondLang) {
                $Ceremonies[1] .= '<div class="ceremoniesDiv">' . get_text_eval(getModuleParameter('Awards', 'Aw-Anthem-2')) . '</div>';
            }
        }
        $Ceremonies[0] .= '<div class="ceremoniesHighLight">' . $Club1 . '</div>';
        if ($SecondLang) {
            $Ceremonies[1] .= '<div class="ceremoniesHighLight">' . $Club2 . '</div>';
        }
    }

    $Ceremonies[0] .= '<hr class="spacer">';
    if ($SecondLang) {
        $Ceremonies[1] .= '<hr class="spacer">';
    }

    $Ceremonies[0] .= '<div class="ceremoniesDiv">' . get_text_eval(getModuleParameter('Awards', 'Aw-Applause-1')) . '</div>';
    if ($SecondLang) {
        $Ceremonies[1] .= '<div class="ceremoniesDiv">' . get_text_eval(getModuleParameter('Awards', 'Aw-Applause-2')) . '</div>';
    }
    $Ceremonies[0] = '<div class="ceremonies">'.$Ceremonies[0].'</div>';
    if ($SecondLang) {
        $Ceremonies[1] = '<div class="ceremonies">'.$Ceremonies[1].'</div>';
    }

}

