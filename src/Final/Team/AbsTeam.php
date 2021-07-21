<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Final/Fun_ChangePhase.inc.php');

CheckTourSession(true);
checkACL(AclTeams, AclReadWrite);

$Error=false;

$advMode = (!empty($_REQUEST["Advanced"]));

$EventCodes=array();
if(isset($_REQUEST['EventCodes'])) {
    $EventCodes=$_REQUEST['EventCodes'];
}

if(!empty($_REQUEST["RESET"]) AND intval($_REQUEST["RESET"])==(count($EventCodes)*42)) {
    foreach ($EventCodes as $evCode) {
        ResetShootoff($evCode, 1, 0);
        Obj_RankFactory::create('AbsTeam', array('tournament' => $_SESSION['TourId'], 'events' => $evCode,'components'=>false))->calculate();

        // destroys the grid of all the events that need "handling"
        safe_w_sql("DELETE FROM TeamFinals WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfEvent = " . StrSafe_DB($evCode));

        // Empty TeamFinComponent
        safe_w_sql("DELETE FROM TeamFinComponent WHERE TfcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfcEvent = " . StrSafe_DB($evCode));

        // Recreate Empty Grids
        safe_w_SQL("INSERT INTO TeamFinals (TfEvent, TfMatchNo, TfTournament, TfDateTime) ".
            "SELECT EvCode, GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . ", NOW() " .
            "FROM Events ".
            "INNER JOIN Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2,EvTeamEvent))>0 " .
            "INNER JOIN Grids ON GrPhase<=greatest(PhId, PhLevel) AND EvTeamEvent=1 AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " 
            WHERE EvCode=" . StrSafe_DB($evCode));



    }
    header('location: ' . $_SERVER["PHP_SELF"]);
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
    '<script type="text/javascript" src="./AbsTeam.js"></script>',
    '<link href="./AbsTeam.css" rel="stylesheet" type="text/css">',
    '<link href="'.$CFG->ROOT_DIR.'Common/css/jquery-confirm.min.css" media="screen" rel="stylesheet" type="text/css">',
    );
include('Common/Templates/head.php');

if(count($EventCodes) != 0) {

    $rank = Obj_RankFactory::create('AbsTeam', array('events' => $EventCodes, 'components'=>false));
    $UnresolvedEvents = array();
    $EventHandled = array();

    // I have SO info coming back from form
    if (isset($_REQUEST['R']) AND !IsBlocked(BIT_BLOCK_TEAM)) {
        $Grids2Handle = array();

        // check received ranks
        $Events = array_keys($_REQUEST['R']);
        foreach ($_REQUEST['R'] as $Event => $EnIds) {
            $q = safe_r_sql("select EvFinalFirstPhase, EvNumQualified, EvFirstQualified from Events where EvCode=" . StrSafe_DB($Event) . " and EvTeamEvent=1 and EvTournament='{$_SESSION['TourId']}'");
            $r = safe_fetch($q);

            // Check CT and SO have been done - need to check that in the range of allowed vales, none is present double
            $existingRanks = array();
            $cantResolve = false;
            foreach ($EnIds as $EnId => $AssignedRank) {
                if ($AssignedRank >= $r->EvFirstQualified AND $AssignedRank < ($r->EvFirstQualified + $r->EvNumQualified)) {
                    if (!array_key_exists($AssignedRank, $existingRanks)) {
                        $existingRanks[$AssignedRank] = 0;
                    }
                    if (++$existingRanks[$AssignedRank] != 1) {
                        $cantResolve = true;
                    }
                }
            }

            if (!$cantResolve AND (count($existingRanks) < min(count($EnIds),$r->EvNumQualified))) {
                if (isset($_REQUEST['bSO'][$Event])) {
                    foreach ($_REQUEST['bSO'][$Event] as $irmPos) {
                        if (!array_key_exists($irmPos, $existingRanks)) {
                            $existingRanks[$irmPos] = 0;
                        }
                        $existingRanks[$irmPos]++;
                    }
                    if (array_sum($existingRanks) < min(count($EnIds),$r->EvNumQualified)) {
                        $cantResolve = true;
                    }
                } else {
                    $cantResolve = true;
                }
            }

            // assign ranks only if all position in a event are solved, and not add to from the Grid-handling
            if ($cantResolve) {
                $UnresolvedEvents[] = $Event;
            } else {
                $Grids2Handle[] = $Event;
	            $obj=getEventArrowsParams($Event,64, 1);
                foreach ($EnIds as $EnId => $AssignedRank) {
                    list($id,$subteam)=explode('_',$EnId);
                    $tmpValue = array('team' => $id, 'subteam'=>$subteam, 'event' => $Event, 'rank' => $AssignedRank);
                    if (isset($_REQUEST['T'][$Event][$EnId]) and is_array($_REQUEST['T'][$Event][$EnId])) {
                        $tmpValue['tiebreak'] = '';
                        $tmpValue['closest'] = 0;
			            $Decoded=array();
			            $idx=0;
                        foreach ($_REQUEST['T'][$Event][$EnId] as $k => $v) {
                            $tmpValue['tiebreak'] .= GetLetterFromPrint(str_replace('*','',$v));
                        }
                        $tmpValue['tiebreak'] = trim($tmpValue['tiebreak']);
                        if (isset($_REQUEST['C'][$Event][$EnId])) {
                            $tmpValue['closest'] = intval($_REQUEST['C'][$Event][$EnId]);
                        }
                        while($TbString=substr($tmpValue['tiebreak'], $idx, $obj->so)) {
	                        if($obj->so==1) {
		                        $Decoded[]=DecodeFromLetter($TbString);
	                        } else {
		                        $Decoded[]=ValutaArrowString($TbString);
	                        }
                        	$idx+=$obj->so;
	                    }
                        $tmpValue['decoded'] = implode(',', $Decoded).($tmpValue['closest'] ? '+' : '');
                    }
                    $rank->setRow(array($tmpValue));
                }
            }
        }

        if (count($Grids2Handle) != 0) {
            // destroys the grid of all the events that need "handling"
            safe_w_sql("DELETE FROM TeamFinals WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfEvent IN ('" . implode("','", $Grids2Handle) . "')");
            // Empty TeamFinComponent
            safe_w_sql("DELETE FROM TeamFinComponent WHERE TfcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfcEvent IN ('" . implode("','", $Grids2Handle) . "')");
            // Recreats the grid
            require_once('Modules/Sets/lib.php');
            $FunName = 'CreateFinalsTeam';
            if (file_exists($CFG->DOCUMENT_PATH . 'Modules/Sets/' . $_SESSION['TourLocRule'] . '/lib.php')) {
                // a lib for the current ruleset exists
                require_once('Modules/Sets/' . $_SESSION['TourLocRule'] . '/lib.php');
                if (function_exists($tmp = 'CreateFinalsTeam_' . $_SESSION['TourLocRule'] . '_' . $_SESSION['TourType'] . '_' . $_SESSION['TourLocSubRule'])) {
                    // a specific function name exists
                    $FunName = $tmp;
                }
            }
            // execute the function that will recreate the grids of the events destroyed before
            $FunName($_SESSION['TourId'], "'" . implode("','", $Grids2Handle) . "'");

            $Sql = "SELECT TeCoId, TeSubTeam, TeRank, TeEvent, TeTournament, GrMatchNo, EvFinalFirstPhase
            FROM Teams 
            INNER JOIN Events ON TeTournament=EvTournament AND TeEvent=EvCode AND EvTeamEvent=1
            INNER JOIN Phases ON PhId=EvFinalFirstPhase and (PhIndTeam & 2) > 0 
            INNER JOIN Grids ON GrPhase=greatest(PhId,PhLevel) AND (TeRank-EvFirstQualified+1)=IF(EvFinalFirstPhase=48,GrPosition2, if(GrPosition>EvNumQualified, 0, GrPosition)) 
            WHERE TeRank between EvFirstQualified and (EvNumQualified+EvFirstQualified-1) 
                and TeTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TeFinEvent=1 AND EvCode IN ('" . implode("','", $Grids2Handle) . "') 
                ORDER BY EvCode, TeRank ASC, GrMatchNo ASC ";
            $q = safe_r_sql($Sql);
            while ($r = safe_fetch($q)) {
                //Keep Track of the events that has been handled and their firstphase
                if (!array_key_exists($r->TeEvent, $EventHandled)) {
                    $EventHandled[$r->TeEvent] = valueFirstPhase($r->EvFinalFirstPhase);
                }
                safe_w_sql("UPDATE TeamFinals SET TfTeam='{$r->TeCoId}', TfSubTeam='{$r->TeSubTeam}', TfDateTime='" . date('Y-m-d H:i:s') . "' WHERE TfEvent='{$r->TeEvent}' AND TfMatchNo={$r->GrMatchNo} AND TfTournament={$r->TeTournament}");
            }

            // Team Components
            $now = date('Y-m-d H:i:s');
            safe_w_SQL("INSERT INTO TeamFinComponent (TfcCoId, TfcSubTeam, TfcTournament, TfcEvent, TfcId, TfcOrder, TfcTimeStamp) 
				SELECT TcCoId, TcSubTeam, TcTournament, TcEvent, TcId, TcOrder, '{$now}'
				FROM TeamComponent 
				INNER JOIN Teams ON TeCoId=TcCoId AND TeSubTeam=TcSubTeam AND TeEvent=TcEvent AND TeTournament=TcTournament AND TeFinEvent=TcFinEvent
				INNER JOIN Events ON TeTournament=EvTournament AND TeEvent=EvCode AND EvTeamEvent=1
	    		WHERE (TeRank between EvFirstQualified and (EvNumQualified+EvFirstQualified-1)) AND TcFinEvent=1 AND TcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TcEvent IN ('" . implode("','", $Grids2Handle) . "')");

            // Set SO Flag of the events and recalculate status for menu
            safe_w_sql("UPDATE Events SET EvShootOff=1 WHERE EvTeamEvent=1 AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode IN ('" . implode("','", $Grids2Handle) . "')");
            set_qual_session_flags();

            $tmpRecalcs = array();
            foreach ($EventHandled as $evCode => $firstPhase) {
                $tmpRecalcs[] = $evCode . "@-3";
                //Move the Byes of first phase
                move2NextPhaseTeam($firstPhase, $evCode, null);
            }
            // Calculate Final rank of the ones out of the grids
            Obj_RankFactory::create('FinalTeam', array('eventsC' => $tmpRecalcs))->calculate();
        }
        foreach ($UnresolvedEvents as $evCode) {
            ResetShootoff($evCode, 1, 0);
        }

    } else {
        //Recalculate the ABS rank of those that has no SO solved.
        foreach ($EventCodes as $Event) {
            $q = safe_r_sql("select EvShootOff from Events where EvTournament={$_SESSION['TourId']} and EvTeamEvent=1 and EvCode=" . StrSafe_DB($Event));
            if ($r = safe_fetch($q) and !$r->EvShootOff) {
                Obj_RankFactory::create('AbsTeam', array('tournament' => $_SESSION['TourId'], 'events' => $Event, 'components'=>false))->calculate();
            }
        }
    }


    echo '<table class="Tabella">';
    echo '<tr><th class="Title">'.(get_text('ShootOff4Final') . ' - ' . get_text('Team')).'</th></tr>';

    if (count($UnresolvedEvents) > 0) {
        echo '<tr class="warning"><td class="warningMsg">' . get_text('NotAllShootoffResolved', 'Tournament', implode(' - ', $UnresolvedEvents)) . '</td></tr>';
    }
    if(count($UnresolvedEvents) == 0 AND count($EventHandled) != 0) {
        echo '<tr><td class="Center"><input type="button" class="closeButton"  value="' . get_text('Close') . '" onclick="cancelShootOff()"></td></tr>';
    } else {
        echo '<tr><td class="Center"><input type="button" value="' . get_text('Back') . '" onclick="cancelShootOff()"></td></tr>';
    }
    echo '</table>';

    if (!$Error) {
        $rank->read();
        $data = $rank->getData();

        if (count($data['sections']) > 0) {
            echo '<form name="Frm" method="post" action="">';
            foreach ($data['sections'] as $section) {
                echo '<input type="hidden" name="Advanced" id="Advanced" value="' . ($advMode ? 1:0) . '">';
                echo '<input type="hidden" name="EventCodes[]" value="' . $section['meta']['event'] . '">';
                echo '<table class="Tabella">' .
                    '<tr class="Divider"><td colspan="8"></td></tr>' .
                    '<tr><th class="Title" colspan="8">' . $section['meta']['descr'] . ' (' . $section['meta']['event'] . ')</th></tr>'.
                    '<tr>' .
                        '<th width="10%" colspan="2">' . get_text('Rank') . '</th>' .
                        '<th width="40%" colspan="2">' . get_text('Country') . '</th>' .
                        '<th width="10%">' . get_text('Total') . '</th>' .
                        '<th width="5%">G</th>' .
                        '<th width="5%">X</th>' .
                        '<th width="25%">' . get_text('TieArrows') . '</th>' .
                    '</tr>';

                $rnkBeforeSO = 1;
                $wasCTSO = false;
                $endRank = 1;
                $obj=getEventArrowsParams($section['meta']['event'], 64, 1);
                foreach ($section['items'] as $item) {
                    if (($item['rankBeforeSO'] + $item['ct']) >= $section['meta']['firstQualified']) {
                        //Stop if Rank >QualifiedNo and no SO
                        if ($item['rank'] > ($section['meta']['qualifiedNo'] + $section['meta']['firstQualified'] - 1) AND $item['so'] == 0) {
                            continue;
                        } else if ($item['irm'] >= 10) {
                            echo '<tr class="Divider"><td colspan="8"></td></tr>';
                        }
                        if ($rnkBeforeSO != $item['rankBeforeSO'] AND ($item['so'] != 0 OR $item['ct'] != 1) OR ($item['ct'] == 1 AND $wasCTSO)) {
                            echo '<tr class="Divider"><td colspan="8"></td></tr>';
                            $wasCTSO = false;
                        }
                        $nn = '[' . $section['meta']['event'] . '][' . $item['id'] . '_' . $item['subteam'] . ']';
                        $endRank = $item['rankBeforeSO'] + $item['ct'] - 1;
                        echo '<tr class="' . ($item['so'] != 0 ? 'error' : ($item['ct'] != 1 ? 'warning' : '')) . '">';
                        echo '<th class="Title" width="5%">' . $item['rank'] . ($item['irm'] != 0 ? $item['irmText'] : '') . '<input type="hidden" name="P' . $nn . '" value="' . intval($item['rank']) . '"></th>';
                        if($advMode) {
                            echo '<td width="5%"><input type="number" name="R' . $nn . '" value="' . (isset($_REQUEST["R"][$section['meta']['event']][$item['id'] . '_' . $item['subteam']]) ? $_REQUEST["R"][$section['meta']['event']][$item['id'] . '_' . $item['subteam']] : $item['rank']) . '"></td>';
                        } else if ($item['irm'] < 10) {
                            //This part for DNF
                            if ($item['rankBeforeSO'] != $endRank) {
                                $wasCTSO = true;
                                echo '<td width="5%" class="Center"><select name="R' . $nn . '">';
                                for ($i = $item['rankBeforeSO']; $i <= $endRank; ++$i) {
                                    echo '<option value="' . $i . '"' . (($i == $item['rank'] OR (isset($_REQUEST["R"][$section['meta']['event']][$item['id'] . '_' . $item['subteam']]) and $i == $_REQUEST["R"][$section['meta']['event']][$item['id'] . '_' . $item['subteam']])) ? ' selected' : '') . '>' . $i . '</option>';
                                }
                                echo '</select></td>';
                            } else {
                                echo '<td width="5%"><input type="hidden" name="R' . $nn . '" value="' . $item['rankBeforeSO'] . '"></td>';
                            }
                        } else {
                            echo '<td width="5%"><input type="hidden" name="bSO' . $nn . '" value="' . $item['rankBeforeSO'] . '"></td>';
                        }
                        echo '<td width="5%" class="Center">' . $item['countryCode'] . '</td>' .
                            '<td width="15%">' . ($item['countryName']!='' ? $item['countryName'] . (intval($item['subteam'])<=1 ? '' : ' (' . $item['subteam'] .')') : '&nbsp') . '</td>' .
                            '<td class="Center">' . $item['score'] . '</td>' .
                            '<td class="Center">' . $item['gold'] . '</td>' .
                            '<td class="Center">' . $item['xnine'] . '</td>' .
                            '<td class="SoCell">';

                        if ($item['so'] != 0) {
                        	echo '<div>';
                        	for($j=0;$j<3;$j++) {
                        		echo '<div class="SoRow"><span>'.get_text('ShotOffShort','Tournament').' '.($j+1).'</span>';
	                            for ($i = 0; $i < $obj->so; ++$i) {
	                            	$idx=($j*$obj->so)+$i;
	                                echo '<input type="text" maxlength="3" size="1" name="T' . $nn . '[' . $idx . ']" value="' . (strlen($item['tiebreak']) > $idx ? DecodeFromLetter($item['tiebreak'][$idx]) : (isset($_REQUEST["T"][$section['meta']['event']][$item['id'] . '_' . $item['subteam']][$idx]) ? $_REQUEST["T"][$section['meta']['event']][$item['id'] . '_' . $item['subteam']][$idx] : '')) . '">&nbsp;';
	                            }
	                            echo '</div>';
	                        }
                            echo '</div>';
                            echo '<div><input type="checkbox" name="C' . $nn . '" value="1" ' . (($item['tiebreakClosest'] == 1 OR isset($_REQUEST["C"][$section['meta']['event']][$item['id'] . '_' . $item['subteam']])) ? 'checked="checked"' : '') . '>' . get_text('Close2Center', 'Tournament').'</div>';
                        }
                        echo '</td></tr>';
                        $rnkBeforeSO = $item['rankBeforeSO'];
                    }
                }
                echo '<tr><td class="Center" colspan="8"><input type="submit" value="' . get_text('CmdOk') . '"></td></tr>';
                echo '<tr><td colspan="8"><input type="button" value="' . get_text(($advMode ? 'DefaultMode' : 'AdvancedMode')) . '" onclick="goToAdvancedMode()" ></td></tr>';
                if($advMode) {
                    echo '<tr><td colspan="8" class="Right"><input type="button" value="' . get_text('ResetBeforeSO','Tournament') . '" onclick="ResetDataToQR()" ></td></tr>';
                }
                echo '</table>';

            }
            echo '</form>';
        }
    }
} else {
    $Sql = "SELECT EvCode, EvEventName, EvNumQualified, EvShootOff, GROUP_CONCAT(DISTINCT itemNo) as SoCt " .
        "FROM Events " .
        "LEFT JOIN (
            SELECT TeEvent, CONCAT_WS('|', COUNT(*), TeSO) as itemNo 
            FROM `Teams` 
            WHERE `TeTournament` = " . StrSafe_DB($_SESSION['TourId']) . " and TeFinEvent=1 AND TeSO!=0
            GROUP BY TeEvent, TeSO
            HAVING  COUNT(*)>1
            ORDER BY TeEvent, TeSO DESC
        ) as sqy ON EvCode=TeEvent ".
        "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 AND EvFinalFirstPhase!=0 AND EvCodeParent='' " .
        "GROUP BY EvCode ORDER BY EvProgr ASC ";
    $q=safe_r_SQL($Sql);
    echo '<table class="Tabella">';
    echo '<tr><th colspan="8" class="Title">'.(get_text('ShootOff4Final') . ' - ' . get_text('Team')).'</th></tr>';
    $FinTeamCalc=getModuleParameter('ISK','CalcFinTeam',0, 0, true);
    if($FinTeamCalc) {
        echo '<tr class="warning"><td colspan="8" class="">'.get_text('RkCalcOffWarning', 'ISK').'</td></tr>';
    }
    echo '<tr><th colspan="3">'.get_text('Event').'</th><th></th><th>'.get_text('ShotOff', 'Tournament').'</th><th>'.get_text('CoinToss', 'Tournament').'</th><th></th></tr>';
    while ($r=safe_fetch($q)) {
        $toBesolved = false;
        $status = '';
        if($r->EvShootOff==0) {
            $toBesolved = true;
        }
        echo '<tr class="rowHover"  id="ev_'.$r->EvCode.'" toBeSolved="'.intval($toBesolved).'">'.
            '<th class="smallContainer" onclick="gotoShootOff(\''.$r->EvCode.'\',true)"><div class="so-status'.($toBesolved ? ' notsolved':'').'">&nbsp;</div></th>'.
            '<td class="evCodeContainer" onclick="gotoShootOff(\''.$r->EvCode.'\',true)">'.$r->EvCode.'</td>'.
            '<td class="evContainer" onclick="gotoShootOff(\''.$r->EvCode.'\',true)">'.$r->EvEventName.'</td>';
        $so=array();
        $ct=array();
        if(!is_null($r->SoCt)) {
            foreach (explode(',',$r->SoCt) as $ctsoItem) {
                list($tmpNo,$tmpPos) = explode('|',$ctsoItem);
                if($tmpPos<0) {
                    $ct[] = get_text('NumTieAtPosition', 'Tournament', array($tmpNo,abs(intval($tmpPos))));
                } else {
                    $so[] = $tmpNo . '&nbsp;@&nbsp;' . intval($tmpPos) .(intval($tmpPos)+intval($tmpNo)>$r->EvNumQualified ? ' (' . get_text(($r->EvNumQualified == intval($tmpPos) ? 'OnePlace':'PlacesNo'), 'Tournament', ($r->EvNumQualified-intval($tmpPos)+1)) . ')' : '');
                }
            }
        }
        echo '<td class="soctPdf">' . (is_null($r->SoCt) ? '' : '<a href="' . $CFG->ROOT_DIR . 'Qualification/PrnShootoff.php?Events=' . $r->EvCode . '|1" target="PrintOut"><img src="' . $CFG->ROOT_DIR . 'Common/Images/pdf_small.gif" alt="' . $r->EvCode . '" border="0"></a>') . '</td>';
        echo '<td class="soctContainer"><strong>' . implode('<br>', $so) . '</strong></td>';
        echo '<td class="soctContainer">' . implode('<br>', $ct) . '</td>';
        echo '<td class="btnContainer"><input type="button" value="' . get_text('InitFinalGrids') . '" onclick="gotoShootOff(\'' . $r->EvCode . '\',false)"></td>';
        echo '</tr>';
    }

    echo '</table>';

}

include('Common/Templates/tail.php');
