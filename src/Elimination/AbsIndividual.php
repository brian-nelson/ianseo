<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_Sessions.inc.php');
//equire_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Fun_Phases.inc.php');
require_once('Final/Fun_ChangePhase.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Lib/CommonLib.php');

CheckTourSession(true);
checkACL(AclEliminations, AclReadWrite);

$advMode = (!empty($_REQUEST["Advanced"]));

$EventCode='';
if(isset($_REQUEST['EventCode'])) {
    $EventCode=$_REQUEST['EventCode'];
}

$ElimRequest = 0;
if(isset($_REQUEST['Elim'])) {
    $ElimRequest=intval($_REQUEST['Elim']);
}

$Sql = "SELECT EvCode, EvEventName, EvNumQualified, EvShootOff, EvE1ShootOff, EvE2ShootOff, EvElimType, EvElim1, EvElim2, EvFinalFirstPhase, EvFirstQualified " .
    "FROM Events " .
    "WHERE EvCode='{$EventCode}' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0 AND EvElimType!=0 AND EvCodeParent='' " .
    "ORDER BY EvProgr ASC ";
$q=safe_r_SQL($Sql);
$rowEv=safe_fetch($q);

if(!$rowEv) {
    header('location: ' . $CFG->ROOT_DIR . 'Final/Individual/AbsIndividual.php');
    die();
}

if(!empty($_REQUEST["RESET"]) AND intval($_REQUEST["RESET"])==(($ElimRequest+1)*42)) {
    if($rowEv->EvElimType<=2 AND $ElimRequest!=0) {
        if($rowEv->EvElimType==2 and $ElimRequest==1) {
            ResetShootoff($EventCode, 0, 0);
            ResetElimRows($EventCode, 1);
            Obj_RankFactory::create('Abs', array('tournament' => $_SESSION['TourId'], 'events' => $EventCode, 'dist' => 0))->calculate();
        } elseif($rowEv->EvElimType==2 and $ElimRequest==2) {
            ResetShootoff($EventCode, 0, 1);
            ResetElimRows($EventCode, 2);
            safe_w_SQL("UPDATE Eliminations SET ElTiebreak='', ElTbClosest=0, ElDateTime=NOW() WHERE ElElimPhase=0 AND ElTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND ElEventCode=". StrSafe_DB($EventCode));
            Obj_RankFactory::create('ElimInd', array('eventsC' => array($EventCode . "@-1")))->calculate();
            Obj_RankFactory::create('FinalInd', array('eventsC' => array($EventCode . "@-3")))->calculate();
        } elseif($rowEv->EvElimType==1 and $ElimRequest==2) {
            ResetShootoff($EventCode, 0, 0);
            ResetElimRows($EventCode, 2);
            Obj_RankFactory::create('Abs', array('tournament' => $_SESSION['TourId'], 'events' => $EventCode, 'dist' => 0))->calculate();
        }

        // destroys the grid of all the events that need "handling"
        safe_w_sql("DELETE FROM Finals WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent = " . StrSafe_DB($EventCode));

        // Recreate Empty Grids
        safe_w_SQL("INSERT INTO Finals (FinEvent, FinMatchNo, FinTournament, FinDateTime) ".
            "SELECT EvCode, GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . ", NOW() " .
            "FROM Events ".
            "INNER JOIN Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2,EvTeamEvent))>0 " .
            "INNER JOIN Grids ON GrPhase<=greatest(PhId, PhLevel) AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " 
            WHERE EvCode=" . StrSafe_DB($EventCode));

    }
    if($rowEv->EvElimType<=2 AND $ElimRequest==0) {
        ResetShootoff($EventCode, 0, 2);
        safe_w_sql("UPDATE Eliminations SET ElTiebreak='', ElTbClosest=0 WHERE ElTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND ElEventCode = " . StrSafe_DB($EventCode) . " AND ElElimPhase=1");

        Obj_RankFactory::create('ElimInd', array('eventsC' => array($EventCode . "@-2")))->calculate();
        Obj_RankFactory::create('FinalInd', array('eventsC' => array($EventCode . "@-2")))->calculate();

        // destroys the grid of all the events that need "handling"
        safe_w_sql("DELETE FROM Finals WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent = " . StrSafe_DB($EventCode));

        // Recreate Empty Grids
        safe_w_SQL("INSERT INTO Finals (FinEvent, FinMatchNo, FinTournament, FinDateTime) ".
            "SELECT EvCode, GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . ", NOW() " .
            "FROM Events ".
            "INNER JOIN Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2,EvTeamEvent))>0 " .
            "INNER JOIN Grids ON GrPhase<=greatest(PhId, PhLevel) AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " 
            WHERE EvCode=" . StrSafe_DB($EventCode));
    }
    if($rowEv->EvElimType>=3 AND $ElimRequest==0) {
        ResetShootoff($EventCode, 0, 0);
        Obj_RankFactory::create('Abs', array('tournament' => $_SESSION['TourId'], 'events' => $EventCode, 'dist' => 0))->calculate();

        // destroys the grid of all the events that need "handling"
        safe_w_sql("DELETE FROM Finals WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent = " . StrSafe_DB($EventCode));

        // Recreate Empty Grids
        safe_w_SQL("INSERT INTO Finals (FinEvent, FinMatchNo, FinTournament, FinDateTime) ".
            "SELECT EvCode, GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . ", NOW() " .
            "FROM Events ".
            "INNER JOIN Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2,EvTeamEvent))>0 " .
            "INNER JOIN Grids ON GrPhase<=greatest(PhId, PhLevel) AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " 
            WHERE EvCode=" . StrSafe_DB($EventCode));

    }
    set_qual_session_flags();
    header('location: ' . $CFG->ROOT_DIR . 'Final/Individual/AbsIndividual.php');
    die();
}

$JS_SCRIPT = array( phpVars2js(array(
    'ROOT_DIR'=>$CFG->ROOT_DIR,
    'MsgInitFinalGridsError'=>get_text('MsgInitFinalGridsError'),
    'MsgAttentionFinReset'=>get_text('MsgAttentionFinReset'),
    'CmdCancel' => get_text('CmdCancel'),
    'CmdConfirm' => get_text('Confirm', 'Tournament'),
    'Advanced' => get_text('Advanced'),
    'MsgForExpert' => get_text('MsgForExpert', 'Tournament')
)),
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-confirm.min.js"></script>',
    '<script type="text/javascript" src="./AbsIndividual.js"></script>',
    '<link href="'.$CFG->ROOT_DIR.'Final/Individual/AbsIndividual.css" rel="stylesheet" type="text/css">',
    '<link href="'.$CFG->ROOT_DIR.'Common/css/jquery-confirm.min.css" media="screen" rel="stylesheet" type="text/css">',
);
include('Common/Templates/head.php');

$UnresolvedEvent = false;
$EventHandled = false;
$FirstQualified=$rowEv->EvFirstQualified;
$NumQualified=0;

if(($rowEv->EvElimType>=2 AND $ElimRequest==0) OR ($rowEv->EvElimType<=2 AND $ElimRequest==2)) {
    $NumQualified = $rowEv->EvElim2;
} else if($ElimRequest==0) {
    $NumQualified = $rowEv->EvNumQualified;
} else {
    $NumQualified = $rowEv->EvElim1;
}

$rank = Obj_RankFactory::create('Abs', array('events' => $EventCode, 'dist' => 0));
if(($rowEv->EvElimType<=2 AND $ElimRequest==0) OR ($rowEv->EvElimType==2 AND $ElimRequest==2)) {
    if($ElimRequest==0) {
        $rank = Obj_RankFactory::create('ElimInd', array('eventsR'=>array($EventCode . '@2')));
    } else {
        $rank = Obj_RankFactory::create('ElimInd',array('eventsR'=>array($EventCode . '@1')));
    }
}

// I have SO info coming back from form
if (isset($_REQUEST['R']) AND !IsBlocked(BIT_BLOCK_ELIM)) {
    // check received ranks
    $Events = array_keys($_REQUEST['R']);
    if(count($Events)==1 AND $Events[0]==$EventCode) {
        foreach ($_REQUEST['R'] as $Event => $EnIds) {
            // Check CT and SO have been done - need to check that in the range of allowed vales, none is present double
            $existingRanks = array();
            $cantResolve = false;
            foreach ($EnIds as $EnId => $AssignedRank) {
                if ($AssignedRank >= $FirstQualified AND $AssignedRank < ($FirstQualified + $NumQualified)) {
                    if (!array_key_exists($AssignedRank, $existingRanks)) {
                        $existingRanks[$AssignedRank] = 0;
                    }
                    if (++$existingRanks[$AssignedRank] != 1) {
                        $cantResolve = true;
                    }
                }
            }

            if (!$cantResolve AND (count($existingRanks) < min(count($EnIds), $NumQualified))) {
                if (isset($_REQUEST['bSO'][$Event])) {
                    foreach ($_REQUEST['bSO'][$Event] as $irmPos) {
                        if (!array_key_exists($irmPos, $existingRanks)) {
                            $existingRanks[$irmPos] = 0;
                        }
                        $existingRanks[$irmPos]++;
                    }
                    if (array_sum($existingRanks) < min(count($EnIds), $r->EvNumQualified)) {
                        $cantResolve = true;
                    }
                } else {
                    $cantResolve = true;
                }
            }

            // assign ranks only if all position in a event are solved, and not add to from the Grid-handling
            if ($cantResolve) {
                $UnresolvedEvent = true;
            } else {
                $EventHandled = true;
                $obj=getEventArrowsParams($Event,64, 0);
                foreach ($EnIds as $EnId => $AssignedRank) {
                    $tmpValue = array('ath' => $EnId, 'event' => $Event, 'dist' => 0, 'rank' => $AssignedRank);
                    if($rowEv->EvElimType<=2 AND $ElimRequest==0) {
                        $tmpValue['phase'] = 1;
                    } else if($rowEv->EvElimType==2 AND $ElimRequest==2) {
                            $tmpValue['phase'] = 0;
                    }
                    if (isset($_REQUEST['T'][$Event][$EnId]) and is_array($_REQUEST['T'][$Event][$EnId])) {
                        $tmpValue['tiebreak'] = '';
                        $tmpValue['closest'] = 0;
                        foreach ($_REQUEST['T'][$Event][$EnId] as $k => $v) {
                            $tmpValue['tiebreak'] .= GetLetterFromPrint(str_replace('*', '', $v));
                        }
                        $tmpValue['tiebreak'] = trim($tmpValue['tiebreak']);
                        if (isset($_REQUEST['C'][$Event][$EnId])) {
                            $tmpValue['closest'] = intval($_REQUEST['C'][$Event][$EnId]);
                        }

	                    $Decoded=array();
	                    $idx=0;
	                    while($TbString=substr($tmpValue['tiebreak'], $idx, $obj->so)) {
		                    if($obj->so==1) {
			                    $Decoded[]=DecodeFromLetter($TbString);
		                    } else {
			                    $Decoded[]=ValutaArrowString($TbString);
		                    }
		                    $idx+=$obj->so;
	                    }
                        $tmpValue['decoded'] = implode(',',$Decoded).($tmpValue['closest'] ? '+' : '');
                    }
                    $rank->setRow(array($tmpValue));
                }
            }
        }
    }

    if ($EventHandled) {
        if($ElimRequest==0) {
            // destroys the grid of all the events that need "handling"
            safe_w_sql("DELETE FROM Finals WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent=" . StrSafe_DB($EventCode) );
            // insert regular finals
            safe_w_sql("INSERT INTO Finals (FinEvent,FinMatchNo,FinTournament,FinDateTime) " .
                "SELECT EvCode,GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . ", NOW() " .
                "FROM Events INNER JOIN Grids ON GrPhase<=EvFinalFirstPhase "."
                WHERE EvCode = " . StrSafe_DB($EventCode) . " AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0");

            if($rowEv->EvElimType>=3) {
                $GridPositions = ($rowEv->EvElimType == 4 ? getPoolGridsWA() : getPoolGrids());
                $GridMatchNos = ($rowEv->EvElimType == 4 ? getPoolMatchNosWA() : getPoolMatchNos());

                // inserts pool finals + show match
                safe_w_sql("INSERT INTO Finals (FinEvent,FinMatchNo,FinTournament,FinDateTime) " .
                    "SELECT EvCode,GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . ", NOW() " .
                    "FROM Events INNER JOIN Grids ON GrMatchNo in (" . implode(',', $GridMatchNos) . ") " .
                    "WHERE EvCode = " . StrSafe_DB($EventCode) . " AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0");
                // insert the people...
                $Sql = "SELECT IndId, IndRank, IndEvent, IndTournament " .
                    "FROM Individuals ".
                    "INNER JOIN Events ON IndTournament=EvTournament AND IndEvent=EvCode AND EvTeamEvent=0 ".
                    "WHERE IndRank between ".$FirstQualified." and ".($FirstQualified+$NumQualified-1)." and IndTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode = " . StrSafe_DB($EventCode) .
                    " ORDER BY IndRank ASC ";
                $q = safe_r_sql($Sql);
                while ($r = safe_fetch($q)) {
                    if (!empty($GridPositions[$r->IndRank])) {
                        safe_w_SQL("update Finals set FinAthlete={$r->IndId} where FinEvent='{$r->IndEvent}' and FinMatchNo={$GridPositions[$r->IndRank]} and FinTournament={$r->IndTournament}");
                    }
                }

                // check the starting match
                foreach (range('A', ($rowEv->EvElimType == 4 ? 'D':'C')) as $Pool) {
                    $MatchDone = false;
                    $tmpPoolMatches = ($rowEv->EvElimType == 4 ? getPoolMatchesWA($Pool, true) : getPoolMatches($Pool));
                    foreach ($tmpPoolMatches as $Matchno => $dummy) {
                        $q = safe_r_sql("select * from Finals where FinAthlete>0 and FinMatchNo in ($Matchno, " . ($Matchno + 1) . ") and FinEvent=" . StrSafe_DB($EventCode) . " and FinTournament={$_SESSION['TourId']}");
                        switch (safe_num_rows($q)) {
                            case 2:
                                break 2; // exits the loop
                            case 1:
                                // promotes the entry in the upper match and removes this one
                                $r = safe_fetch($q);
                                safe_w_SQL("update Finals set FinAthlete=$r->FinAthlete where FinEvent=" . StrSafe_DB($EventCode) . " and FinMatchNo=" . intval($r->FinMatchNo / 2) . " and FinTournament=" . StrSafe_DB($_SESSION['TourId']));
                                safe_w_SQL("update Finals set FinAthlete=0 where FinEvent=" . StrSafe_DB($EventCode) . " and FinMatchNo=$r->FinMatchNo and FinTournament=" . StrSafe_DB($_SESSION['TourId']));
                                break 2;
                        }
                    }
                }

                Obj_RankFactory::create('FinalInd', array('eventsC' => array($EventCode . "@-3")))->calculate();

                // setto a 1 i flags che dicono che ho fatto gli spareggi per gli eventi
                safe_w_sql("UPDATE Events SET EvE2ShootOff=1, EvShootOff=1 WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode=" . StrSafe_DB($EventCode) . "  AND EvTeamEvent=0");
                set_qual_session_flags();
            } else {
                // insert the people...
                $Sql = "SELECT  ElId, ElRank, ElEventCode, ElTournament, GrMatchNo, EvFinalFirstPhase
                    FROM Eliminations 
                    INNER JOIN Events ON ElTournament=EvTournament AND ElEventCode=EvCode AND EvTeamEvent=0 
                    INNER JOIN Phases ON PhId=EvFinalFirstPhase and (PhIndTeam & 1) = 1 
                    INNER JOIN Grids ON GrPhase=greatest(PhId,PhLevel) AND (ElRank-EvFirstQualified+1)=IF(EvFinalFirstPhase=48,GrPosition2, if(GrPosition>EvNumQualified, 0, GrPosition)) 
                    WHERE ElRank between EvFirstQualified and (EvFirstQualified+EvNumQualified-1) and ElTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND ElEventCode = " . StrSafe_DB($EventCode) . " AND ElElimPhase=1 
                    ORDER BY EvCode, ElRank ASC, GrMatchNo ASC ";
                $q = safe_r_sql($Sql);
                while ($r = safe_fetch($q)) {
                    safe_w_sql("UPDATE Finals SET FinAthlete='{$r->ElId}' WHERE FinEvent='{$r->ElEventCode}' AND FinMatchNo={$r->GrMatchNo} AND FinTournament={$r->ElTournament}");
                }

                // Set SO Flag of the events and recalculate status for menu
                safe_w_sql("UPDATE Events SET EvShootOff='1' WHERE EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode=". StrSafe_DB($EventCode));
                set_qual_session_flags();

                move2NextPhase($rowEv->EvFinalFirstPhase, $EventCode, null);
                // Calculate Final rank of the ones out of the grids
                Obj_RankFactory::create('FinalInd', array('eventsC' => array($EventCode . "@-2")))->calculate();
            }
        } else {
            //Reset Elimination Table
            ResetElimRows($EventCode, $ElimRequest);

            //If doing the 1st Elimination, kill the second elimination too
            if($ElimRequest==1 AND $rowEv->EvElimType==2) {
                ResetElimRows($EventCode, 2);
            }

            // destroys the grid of all the events that need "handling"
            safe_w_sql("DELETE FROM Finals WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent=" . StrSafe_DB($EventCode) );
            // insert regular finals
            safe_w_sql("INSERT INTO Finals (FinEvent,FinMatchNo,FinTournament,FinDateTime) " .
                "SELECT EvCode,GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . ", NOW() " .
                "FROM Events INNER JOIN Grids ON GrPhase<=EvFinalFirstPhase "."
                WHERE EvCode = " . StrSafe_DB($EventCode) . " AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0");

            // insert the people...
            $Sql = "SELECT IndId, IndRank, IndEvent, IndTournament " .
                "FROM Individuals ".
                "INNER JOIN Events ON IndTournament=EvTournament AND IndEvent=EvCode AND EvTeamEvent=0 ".
                "WHERE IndRank between ".$FirstQualified." and ".($FirstQualified+$NumQualified-1)." and IndTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode = " . StrSafe_DB($EventCode) .
                " ORDER BY IndRank ASC ";
            if($ElimRequest==2 AND $rowEv->EvElimType==2) {
                $Sql = "SELECT ElId IndId, ElRank IndRank, ElEventCode IndEvent, ElTournament IndTournament " .
                    "FROM Eliminations ".
                    "INNER JOIN Events ON ElTournament=EvTournament AND ElEventCode=EvCode AND EvTeamEvent=0 ".
                    "WHERE ElRank between ".$FirstQualified." and ".($FirstQualified+$NumQualified-1)." and ElTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND ElEventCode = " . StrSafe_DB($EventCode) . " AND ElElimPhase=0 ".
                    "ORDER BY ElRank ASC ";
            }
            $q = safe_r_sql($Sql);
            while ($r = safe_fetch($q)) {
                safe_w_SQL("UPDATE Eliminations SET ElId={$r->IndId}, ElDateTime=NOW() WHERE ElElimPhase=({$ElimRequest}-1) AND ElTournament={$r->IndTournament} AND ElEventCode='{$r->IndEvent}' AND ElQualRank={$r->IndRank}");
            }

            ResetShootoff($EventCode,0,$ElimRequest);
            Obj_RankFactory::create('FinalInd', array('eventsC' => array($EventCode . "@-".(($rowEv->EvElimType==2 AND $ElimRequest==2) ? "1":"3"))))->calculate();

            // setto a 1 i flags che dicono che ho fatto gli spareggi per gli eventi
            safe_w_sql("UPDATE Events SET EvE{$ElimRequest}ShootOff=1 WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode=" . StrSafe_DB($EventCode) . "  AND EvTeamEvent=0");
            set_qual_session_flags();


        }
    }


} else {
    //Recalculate the rank if no SO solved.
    $q = safe_r_sql("select EvShootOff, EvE1ShootOff, EvE2ShootOff, EvElimType from Events where EvTournament={$_SESSION['TourId']} and EvTeamEvent=0 and EvCode=" . StrSafe_DB($EventCode));
    if ($r = safe_fetch($q)) {
        if(($rowEv->EvElimType>=2 AND $r->EvE2ShootOff==0 AND $ElimRequest==0) OR ($rowEv->EvElimType==1 AND $r->EvE2ShootOff==0 AND $ElimRequest==2) OR ($rowEv->EvElimType==2 AND $r->EvE1ShootOff==0 AND $ElimRequest==1)) {
            Obj_RankFactory::create('Abs', array('events' => $EventCode, 'dist' => 0))->calculate();
        } else if(($rowEv->EvElimType<=2 AND $r->EvShootOff==0 AND $ElimRequest==0)) {
            Obj_RankFactory::create('ElimInd', array('eventsC'=>array($EventCode . '@2')))->calculate();
        } else if(($rowEv->EvElimType==2 AND $r->EvE1ShootOff==0 AND $ElimRequest==2)) {
            Obj_RankFactory::create('ElimInd', array('eventsC'=>array($EventCode . '@1')))->calculate();
        }
    }
}

echo '<table class="Tabella">';
echo '<tr><th class="Title">'.get_text('ShootOff4Elim') .'</th></tr>';
if ($UnresolvedEvent) {
    echo '<tr class="warning"><td class="warningMsg">' . get_text('NotAllShootoffResolved', 'Tournament', $EventCode) . '</td></tr>';
}
if(!$UnresolvedEvent and $EventHandled) {
    echo '<tr><td class="Center"><input type="button" class="closeButton"  value="' . get_text('Close') . '" onclick="cancelShootOff()"></td></tr>';
} else {
    echo '<tr><td class="Center"><input type="button" value="' . get_text('Back') . '" onclick="cancelShootOff()"></td></tr>';
}
echo '</table>';

$rank->read();
$data = $rank->getData();
if (count($data['sections']) > 0) {
    echo '<form name="Frm" method="post" action="">';
    echo '<input type="hidden" id="Advanced" name="Advanced" value="' . ($advMode ? 1:0) . '">';
    echo '<input type="hidden" id="Elim" name="Elim" value="' . $ElimRequest . '">';
    echo '<input type="hidden" id="EventCode" name="EventCode" value="' . $EventCode . '">';
    foreach ($data['sections'] as $section) {
        echo '<table class="Tabella">' .
            '<tr class="Divider"><td colspan="9"></td></tr>' .
            '<tr><th class="Title" colspan="9">' . $section['meta']['descr'] . ' (' . $EventCode . ')</th></tr>'.
            '<tr>' .
            '<th width="10%" colspan="2">' . get_text('Rank') . '</th>' .
            '<th width="25%">' . get_text('Archer') . '</th>' .
            '<th width="20%" colspan="2">' . get_text('Country') . '</th>' .
            '<th width="10%">' . get_text('Total') . '</th>' .
            '<th width="5%">G</th>' .
            '<th width="5%">X</th>' .
            '<th width="25%">' . get_text('TieArrows') . '</th>' .
            '</tr>';

        $rnkBeforeSO = 1;
        $wasCTSO = false;
        $endRank = 1;
        foreach ($section['items'] as $item) {
            if (($item['rankBeforeSO'] + $item['ct']) >= $FirstQualified) {
                //Stop if Rank >QualifiedNo and no SO
                if ($item['rank'] > ($NumQualified + $FirstQualified - 1) AND $item['so'] == 0) {
                    continue;
                } else if ($item['irm'] >= 10) {
                    echo '<tr class="Divider"><td colspan="9"></td></tr>';
                }
                if ($rnkBeforeSO != $item['rankBeforeSO'] AND ($item['so'] != 0 OR $item['ct'] != 1) OR ($item['ct'] == 1 AND $wasCTSO)) {
                    echo '<tr class="Divider"><td colspan="9"></td></tr>';
                    $wasCTSO = false;
                }
                $nn = '[' . $EventCode . '][' . $item['id'] . ']';
                $endRank = $item['rankBeforeSO'] + $item['ct'] - 1;
                echo '<tr class="' . ($item['so'] != 0 ? 'error' : ($item['ct'] != 1 ? 'warning' : '')) . '">';
                echo '<th class="Title" width="5%">' . $item['rank'] . ($item['irm'] != 0 ? $item['irmText'] : '') . '<input type="hidden" name="P' . $nn . '" value="' . intval($item['rank']) . '"></th>';
                if($advMode) {
                    echo '<td width="5%"><input type="number" name="R' . $nn . '" value="' . (isset($_REQUEST["R"][$EventCode][$item['id']]) ? $_REQUEST["R"][$EventCode][$item['id']] : $item['rank']) . '"></td>';
                } else if ($item['irm'] < 10) {
                    //This part for DNF
                    if ($item['rankBeforeSO'] != $endRank) {
                        $wasCTSO = true;
                        echo '<td width="5%" class="Center"><select name="R' . $nn . '">';
                        for ($i = $item['rankBeforeSO']; $i <= $endRank; ++$i) {
                            echo '<option value="' . $i . '"' . (($i == $item['rank'] OR (isset($_REQUEST["R"][$EventCode][$item['id']]) and $i == $_REQUEST["R"][$EventCode][$item['id']])) ? ' selected' : '') . '>' . $i . '</option>';
                        }
                        echo '</select></td>';
                    } else {
                        echo '<td width="5%"><input type="hidden" name="R' . $nn . '" value="' . $item['rankBeforeSO'] . '"></td>';
                    }
                } else {
                    echo '<td width="5%"><input type="hidden" name="bSO' . $nn . '" value="' . $item['rankBeforeSO'] . '"></td>';
                }
                echo '<td>' . $item['athlete'] . '</td>' .
                    '<td width="5%" class="Center">' . $item['countryCode'] . '</td>' .
                    '<td width="15%">' . ($item['countryName'] != '' ? $item['countryName'] : '&nbsp') . '</td>' .
                    '<td class="Center">' . $item['score'] . '</td>' .
                    '<td class="Center">' . $item['gold'] . '</td>' .
                    '<td class="Center">' . $item['xnine'] . '</td>' .
                    '<td>';

                if ($item['so'] != 0) {
                    for ($i = 0; $i < 3; ++$i) {
                        echo '<input type="text" maxlength="3" size="1" name="T' . $nn . '[' . $i . ']" value="' . (strlen($item['tiebreak']) > $i ? DecodeFromLetter($item['tiebreak'][$i]) : (isset($_REQUEST["T"][$EventCode][$item['id']][$i]) ? $_REQUEST["T"][$EventCode][$item['id']][$i] : '')) . '">&nbsp;';
                    }
                    echo '<input type="checkbox" name="C' . $nn . '" value="1" ' . (($item['tiebreakClosest'] == 1 OR isset($_REQUEST["C"][$EventCode][$item['id']])) ? 'checked="checked"' : '') . '>' . get_text('Close2Center', 'Tournament');
                }
                echo '</td></tr>';
                $rnkBeforeSO = $item['rankBeforeSO'];
            }
        }
        echo '<tr><td class="Center" colspan="9"><input type="submit" value="' . get_text('CmdOk') . '"></td></tr>';
        echo '<tr><td colspan="9"><input type="button" value="' . get_text(($advMode ? 'DefaultMode' : 'AdvancedMode')) . '" onclick="goToAdvancedMode()" ></td></tr>';
        if($advMode) {
            echo '<tr><td colspan="9" class="Right"><input type="button" value="' . get_text('ResetBeforeSO','Tournament') . '" onclick="ResetDataToQR()" ></td></tr>';
        }
        echo '</table>';

    }
    echo '</form>';
}

include('Common/Templates/tail.php');
