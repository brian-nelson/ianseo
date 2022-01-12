<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Final/Fun_ChangePhase.inc.php');
require_once('Common/Lib/Fun_FormatText.inc.php');
require_once('Common/Lib/CommonLib.php');
require_once('Common/Lib/Fun_Phases.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');

if (!CheckTourSession() || !isset($_REQUEST['d_Phase'])) PrintCrackError();
checkACL(AclTeams, AclReadOnly);

$Cols2Remove = (isset($_REQUEST['d_Tie']) && $_REQUEST['d_Tie']==1 ? 0 : 3);
$QrCodeScorecards = '';
if(module_exists("Barcodes")) {
    $QrCodeScorecards .= "&Barcode=1";
}
foreach(AvailableApis() as $Api) {
    if(!($tmp=getModuleParameter($Api, 'Mode')) || $tmp=='live' ) {
        continue;
    }
    $QrCodeScorecards .= "&QRCode[]=$Api";
}

$Error=false;

$Tie_Error = array();	// Contiene gl'indici delle tendine Tie con errore
$Score_Error = array(); // contiene gl'indici degli score che superano il max
$Set_Error = array(); // contiene gl'indici dei set che superano il max

if(!empty($_REQUEST['Score']) and !IsBlocked(BIT_BLOCK_TEAM)) {
    // Update dei punti e dei tie
    foreach($_REQUEST['Score'] as $Event => $Matches) {
        $Field=$_REQUEST['MatchMode'][$Event] ? 'TfSetScore' : 'TfScore';
        // get the match maximum values
        $MaxScores=GetMaxScores($Event, $_REQUEST['Phase'][$Event]*2, 1);
        foreach($Matches as $Match => $Score) {
	        $Score=intval($Score);

            if ($Score > ($MaxScores['MaxSetPoints'] ? $MaxScores['MaxSetPoints'] : $MaxScores['MaxMatch']) ) {
                $Score_Error[$Event][$Match]=true;
            } else {
                // Start the update query syntax
                $Update = "UPDATE TeamFinals SET $Field=$Score";

                // if set event, check the set scores
                if($_REQUEST['MatchMode'][$Event] and !empty($_REQUEST['Points'][$Event][$Match])) {
                    $setPoints=array();
                    $setScore=0;
                	foreach($_REQUEST['Points'][$Event][$Match] as $k => $v) {
                		if(strlen($v)>0) {
                			if(!is_numeric($v) or $v>$MaxScores['MaxEnd']) {
                                $Set_Error[$Event][$Match]=true;
			                } else {
		                        $setPoints[]=intval($v);
		                        $setScore+=intval($v);
			                }
		                }
                    }
                    $Update.= ", TfSetPoints='" . ($setPoints ? implode("|",$setPoints) : '') . "', TfScore=$setScore";
                }

                // Check if we have a tie set
                if(isset($_REQUEST['Tie'][$Event][$Match])) {
                    // SO Arows
					$tiebreak = '';

					foreach ($_REQUEST['TieArrows'][$Event][$Match] as $TieKey => $TieValue) {
						$tiebreak.=(GetLetterFromPrint($TieValue)!=' ' ? GetLetterFromPrint($TieValue) : ' ');
					}
					$Update.= ", TfTieBreak=" . StrSafe_DB($tiebreak);

	                // Closest status
	                $Update.= ", TfTbClosest=" . (empty($_REQUEST['Closest'][$Event][$Match]) ? 0 : 1);

					// split the SO arrows in Decoded ends
	                $Params=getEventArrowsParams($Event,getPhase($Match),1);
	                $TbDecoded=array();
	                $tiebreak=rtrim($tiebreak);
	                $idx=0;
	                while($SoEnd=substr($tiebreak, $idx, $Params->so)) {
	                	if($Params->so>1) {
			                $TbDecoded[]=ValutaArrowString($SoEnd);
		                } else {
			                $TbDecoded[]=DecodeFromLetter($SoEnd);
		                }
	                	$idx+=$Params->so;
	                }
                    $Update.= ", TfTbDecoded='" . ($TbDecoded ? implode(",",$TbDecoded).(!empty($_REQUEST['Closest'][$Event][$Match]) ? '+' : '') : '') . "'";

                	// check if it is an IRM
	                if(substr($_REQUEST['Tie'][$Event][$Match], 0, 4)=='irm-') {
		                // IRM "low" status (DNF/DNS)
		                $Update.= ", TfIrmType=" . intval(substr($_REQUEST['Tie'][$Event][$Match], 4));
	                } else {
						// check if the opponent has a bye or something...
						$opp=($Match%2 ? $Match-1 : $Match+1);
						$Tie=intval($_REQUEST['Tie'][$Event][$Match]);
						if($Tie>0 and intval($_REQUEST['Tie'][$Event][$opp])>0) {
	                        $Tie_Error[$Event][$Match] = true;
						} else {
	                        $Update .= ", TfTie=$Tie";
						}
	                }
                }

                $Update .=", TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent='$Event' AND TfMatchNo=$Match AND TfTournament=" . StrSafe_DB($_SESSION['TourId']);
                safe_w_sql($Update);
            }
        }
    }

	// Execute next phase
    foreach($_REQUEST['Phase'] as $event => $phase) move2NextPhaseTeam($phase, $event);
}

$PAGE_TITLE=get_text('MenuLM_Data insert (Table view)');

$JS_SCRIPT=array(
	phpVars2js(array('ROOT_DIR' => $CFG->ROOT_DIR)),
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Individual/Fun_JS.js"></script>',
    '<script type="text/javascript" src="./InsertPoint2.js"></script>',
	'<style>
            .disabled td {background-color:#bbb} 
            </style>',
    );

include('Common/Templates/head.php');
echo '<form method="post" name="Frm">';

$useSession=false;

$PrecPhase = '';//($_REQUEST['d_Phase']==0 ? 1 : $_REQUEST['d_Phase']*2);
$NextPhase = '';//($_REQUEST['d_Phase']>1 ? $_REQUEST['d_Phase']/2 : 0);
$PP=$PrecPhase;
$NP=$NextPhase;
$Sch=0;

if(!empty($_REQUEST['d_Event'])) {
	foreach($_REQUEST['d_Event'] as $key => $val) {
		echo '<input type="hidden" name="d_Event[]" id="d_Event_'.$key.'" value="'.$val.'">';
	}
}

if(!empty($_REQUEST['x_Session']) and $_REQUEST['x_Session']!=-1) {
	echo '<input type="hidden" name="x_Session" id="x_Session" value="'.$_REQUEST['x_Session'].'">';

	$useSession=true;

	$ComboArr=array();
	ComboSession('Teams', $ComboArr);

// schedule attuale
	$actual=array_search($_REQUEST['x_Session'],$ComboArr);

/*
 * La precedente deve esser per forza diversa da false ma nel caso non faccio nulla
 */
	if ($actual!==false) {
	// prima imposto l'inizio e la fine uguale all'attuale
		$PP=$ComboArr[$actual];
		$NP=$ComboArr[$actual];

	// poi metto a posto

		if ($actual!=0) {
			$PP=$ComboArr[$actual-1];
		}

		if ($actual!=(count($ComboArr)-1)) {
			$NP=$ComboArr[$actual+1];
		}
	}

	$Sch=1;
}

$IrmOptions=array();
$q=safe_r_sql("select * from IrmTypes where IrmId>0 order by IrmId");
while($r=safe_fetch($q)) {
	$IrmOptions[]=$r;
}

echo '<input type="hidden" name="d_Phase" id="d_Phase" value="'.$_REQUEST['d_Phase'].'">';
echo '<input type="hidden" name="d_Tie" id="d_Tie" value="'.$_REQUEST['d_Tie'].'">';
echo '<input type="hidden" name="d_SetPoint" id="d_SetPoint" value="'.(isset($_REQUEST['d_SetPoint']) ? $_REQUEST['d_SetPoint'] : 0).'">';
echo '<table class="Tabella">';
echo '<tr><th class="Title" colspan="'. (9-$Cols2Remove).'">'. get_text('IndFinal').'</th></tr>';
echo '<tr class="Divider"><td colspan="'.(9-$Cols2Remove).'"></td></tr>';

$QueryFilter = '';
// if a scheduled round has been sent, it superseeds everything
if(!empty($_REQUEST['x_Session']) and $_REQUEST['x_Session']!=-1) {
    // get all the pairs event<=>phase for that scheduled time
    $QueryFilter.= "AND concat(FSTeamEvent, FSScheduledDate, ' ', FSScheduledTime)=" . strSafe_DB($_REQUEST['x_Session']) . " ";
} elseif(!empty($_REQUEST['d_Event'])) {
    // creates the filter on the matching events and phase
    $tmp=array();
    foreach($_REQUEST['d_Event'] as $event) {
        $tmp[]=StrSafe_DB($event);
    }
    sort($tmp);
    if($tmp) {
        $QueryFilter.= "AND TfEvent in (" . implode(',', $tmp) . ") ";
    }
    $QueryFilter.= "AND GrPhase=" . intval($_REQUEST['d_Phase']) . " ";
}


//	Tiro fuori l'elenco degli eventi non spareggiati
$Select = "SELECT distinct EvCode, EvEventName, EvMatchMode
    FROM TeamFinals
    left JOIN FinSchedule ON TfEvent=FSEvent AND FsMatchNo=TfMatchNo AND FsTeamEvent=1 AND FsTournament={$_SESSION['TourId']}
    INNER JOIN Events ON TfEvent=EvCode AND EvTeamEvent=1 AND EvTournament={$_SESSION['TourId']}
    INNER JOIN Grids ON TfMatchNo=GrMatchNo
    WHERE TfTournament={$_SESSION['TourId']} AND EvShootOff=0 " . $QueryFilter . "
    ORDER BY EvProgr ASC, EvCode, GrMatchNo ASC ";
$Rs=safe_r_sql($Select);

$Elenco = '';
while ($Row=safe_fetch($Rs)) {
    $Elenco.= get_text($Row->EvEventName,'','',true) . ' (' . $Row->EvCode . ')<br>';
}

if ($Elenco!='') {
    print '<tr><td class="Bold" colspan="' . (9-$Cols2Remove) . '">' . get_text('IndFinEventWithoutShootOff') . '</td></tr>';
    print '<tr><td colspan="' . (9-$Cols2Remove) . '">';
    print $Elenco;
    print '</td>';
    print '</tr>';
    print '<tr class="Divider"><td colspan="' . (9-$Cols2Remove) . '"></td></tr>';
}

// tiro fuori solo gli eventi spareggiati
$Select = "SELECT TfEvent, TfMatchNo, TfTournament, TfTeam, IF(EvMatchMode=0,TfScore,TfSetScore) AS Score, TfTie, TfTiebreak, TfSetPoints, TfTbClosest,TfIrmType,
    EvProgr, EvFinalFirstPhase, EvEventName, EvMatchMode, EvMatchArrowsNo, FsTarget, GrMatchNo, GrPhase, GrPosition, GrPosition2,
    CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) AS TeamName, CoCode, CoName
    FROM TeamFinals
    left JOIN FinSchedule ON TfEvent=FSEvent AND FsMatchNo=TfMatchNo AND FsTeamEvent='1' AND FsTournament=TfTournament 
    INNER JOIN Events ON TfEvent=EvCode AND EvTeamEvent='1' AND EvTournament=TfTournament 
    INNER JOIN Grids ON TfMatchNo=GrMatchNo 
    LEFT JOIN Countries ON TfTeam=CoId 
    WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvShootOff='1' " . $QueryFilter
    . " ORDER BY EvProgr ASC, EvCode, GrMatchNo ASC ";
$Rs=safe_r_sql($Select);
//print $Select;

if (safe_num_rows($Rs)>0) {
    $MyEvent = '-----';
    while ($MyRow=safe_fetch($Rs)) {
        $obj=getEventArrowsParams($MyRow->TfEvent,$MyRow->GrPhase,1);
        if ($MyEvent!=$MyRow->TfEvent) {
            print '<input type="hidden" name="MatchMode[' . $MyRow->TfEvent . ']" value="' . $MyRow->EvMatchMode . '">';
            print '<input type="hidden" name="Phase[' . $MyRow->TfEvent . ']" value="' . $MyRow->GrPhase . '">';
            $ii=0;
            $StileRiga="";
            if ($MyEvent!='-----') {
                print '<tr><td colspan="' . (9-$Cols2Remove) . '" class="Center"><input type="submit" value="' . get_text('CmdSave') . '"></td></tr>';
                print '<tr class="Divider"><td></td></tr>';
            }
            print '<tr><th colspan="' . (9-$Cols2Remove) . '" class="Title">' . get_text($MyRow->EvEventName,'','',true) . ' (' . $MyRow->TfEvent . ') - ' . get_text('Phase') . ' ' . get_text(namePhase($MyRow->EvFinalFirstPhase,$MyRow->GrPhase) . '_Phase') . '</th></tr>';
            print '<tr>';
            print '<td colspan="' . (9-$Cols2Remove) . '">';

            if (!$useSession) {
                list($PP,$NP)=PrecNextPhaseForButton();
            // queste due solo per i nomi e gli score del turno successivo (e volendo precedente)
                $PrecPhase=$PP;
                $NextPhase=$NP;
            }

            print '<a class="Link mr-5" href="javascript:ChangePhase(\'' . $PP . '\',' . $Sch. ');">' . get_text('PrecPhase') . '</a>'
                //. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="Link" href="javascript:ChangePhase(' . $NextPhase . ');">' . get_text('NextPhase') . '</a>'
                . '<a class="Link mr-5" href="javascript:ChangePhase(\'' . $NP . '\',' . $Sch. ');">' . get_text('NextPhase') . '</a>'
                . '<a class="Link mr-5" href="PrnTeam.php?Event='.$MyRow->TfEvent.'&IncBrackets=1&ShowTargetNo=1" target="Griglie">' . get_text('Brackets') . '</a>';
            if($_REQUEST['d_Phase']>0) {
                print '<a class="Link mr-5" href="PDFScoreMatch.php?Event='.$MyRow->TfEvent.'&Phase='.$NextPhase.$QrCodeScorecards.'" target="Scores">' . get_text('NextMatchScores') . '</a>'
                    . '<a class="Link mr-5" href="PrnName.php?Event='.$MyRow->TfEvent.'&Phase='.$NextPhase.'" target="Names">' . get_text('NextMatchNames') . '</a>'
                    ;
            }
            print '</td>';
            print '</tr>';
            print '<tr>';
            print '<th width="5%">' . get_text('Target') . '</th>';
            print '<th width="5%">' . get_text('Rank') . '</th>';
            print '<th width="35%" colspan="2">' . get_text('Country') . '</th>';

            if($MyRow->EvMatchMode!=0) {
                print '<th width="10%">' . get_text('SetPoints', 'Tournament') . '</th>';
            }
            print '<th width="20%" colspan="' . ($MyRow->EvMatchMode==0 ? '2':'1') . '">' . get_text('Score', 'Tournament') . '</th>';
            if (isset($_REQUEST['d_Tie']) && $_REQUEST['d_Tie']==1) {
                print '<th width="5%">' . get_text('ShotOff', 'Tournament') . '</th>';
                print '<th width="10%" colspan="2">' . get_text('TieArrows') . '</th>';
            }
            print '</tr>';
        }

	    $Disabled = ($MyRow->TfIrmType or $MyRow->TfTie==2 or !$MyRow->TeamName);
	    print '<tr class="' . $StileRiga . ($Disabled ? ' disabled' : '').'">';

        print '<td class="Center">' . $MyRow->FsTarget . '</td>';
        print '<td class="Center">' . (useGrPostion2($MyRow->EvFinalFirstPhase, $MyRow->GrPhase) ? ($MyRow->GrPosition2 ? $MyRow->GrPosition2 : '&nbsp;') : $MyRow->GrPosition) . '</td>';
        print '<td>' . (!is_null($MyRow->CoCode) ? $MyRow->CoCode : '&nbsp;') . '</td>';
        print '<td>' . (strlen($MyRow->TeamName)>0 ? $MyRow->TeamName : '&nbsp;') . /*' ' . $MyRow->FinAthlete .*/ '</td>';
        $TextStyle = '';
        if (array_search($MyRow->TfEvent . '_' . $MyRow->GrMatchNo,array_keys($Score_Error))!==false) {
            $TextStyle = 'error';
        }
        //Carico i punti
        print '<td class="Center" colspan="' . ($MyRow->EvMatchMode==0 ? '2':'1') . '">';
        print '<input type="text" class="' . $TextStyle . '" size="3" maxlength="3" name="Score[' . $MyRow->TfEvent . '][' . $MyRow->GrMatchNo . ']" value="' . $MyRow->Score . '">';
        print '</td>';
        //Carico i set point in math di tipo set
        if($MyRow->EvMatchMode) {
            $TextStyle = '';
            if(!empty($Set_Error[$MyRow->TfEvent][$MyRow->GrMatchNo])) {
                $TextStyle = 'error';
            }
            $setPointsArray=explode("|",$MyRow->TfSetPoints);
            print '<td width="10%">';
            for($setNo=0; $setNo<$obj->ends; $setNo++) {
                if (empty($_REQUEST['d_SetPoint'])) {
                    print (isset($setPointsArray[$setNo]) ? $setPointsArray[$setNo] : '') . '&nbsp;&nbsp;&nbsp;';
                } else {
                    print '<input type="text" class="' . $TextStyle . '" size="2" maxlength="2" name="Points[' . $MyRow->TfEvent . '][' . $MyRow->GrMatchNo . '][' . $setNo . ']" value="' . ($TextStyle == '' ? (isset($setPointsArray[$setNo]) ? $setPointsArray[$setNo] : '') : $_REQUEST['Points'][$MyRow->TfEvent][$MyRow->GrMatchNo][$setNo]) . '">&nbsp;';
                }
            }
            print '</td>';
        }


        if (!empty($_REQUEST['d_Tie'])) {
            print '<td class="Center">';
            $ComboStyle = '';
            if (!empty($Tie_Error[$MyRow->TfEvent][$MyRow->GrMatchNo])) {
                $ComboStyle='error';
            }

            print '<select event="M'.$MyRow->TfEvent.'" onchange="CheckIRM(this)" team="1" phase="'.$MyRow->GrPhase.'" class="' . $ComboStyle . '" name="Tie[' . $MyRow->TfEvent . '][' . $MyRow->GrMatchNo . ']" id="T_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '">';
            print '<option value="0"' . (($ComboStyle=='' && $MyRow->TfTie==0) || ($ComboStyle!='' && $_REQUEST['Tie'][$MyRow->TfEvent][$MyRow->GrMatchNo]==0) ? ' selected' : '') . '>0 - ' .	get_text('NoTie', 'Tournament') . '</option>';
            print '<option value="1"' . (($ComboStyle=='' && $MyRow->TfTie==1) || ($ComboStyle!='' && $_REQUEST['Tie'][$MyRow->TfEvent][$MyRow->GrMatchNo]==1) ? ' selected' : '') . '>1 - ' .	get_text('TieWinner', 'Tournament') . '</option>';
            print '<option value="2"' . (($ComboStyle=='' && $MyRow->TfTie==2) || ($ComboStyle!='' && $_REQUEST['Tie'][$MyRow->TfEvent][$MyRow->GrMatchNo]==2) ? ' selected' : '') . '>2 - ' .	get_text('Bye') . '</option>';
	        foreach($IrmOptions as $irm) {
		        echo '<option value="'.($irm->IrmShowRank ? 'irm-'.$irm->IrmId : 'man').'"' . ($MyRow->TfIrmType==$irm->IrmId ? ' selected' : '') . '>' . $irm->IrmType . '</option>' . "\n";
	        }
            print '</select>';
            print '</td>';

            print '<td id="Tie_' . $MyRow->TfEvent . '_' . $MyRow->GrMatchNo . '">';
            $TieBreak = str_pad($MyRow->TfTiebreak,$obj->so,' ',STR_PAD_RIGHT);
            for($pSo=0; $pSo<3; $pSo++ ) {
            	echo '<div class="NoWrap">';
                for ($i = 0; $i < $obj->so; ++$i) {
                    $ArrI = $i+($pSo*$obj->so);
                    print '<input type="text" name="TieArrows[' . $MyRow->TfEvent . '][' . $MyRow->GrMatchNo . '][]" size="2" maxlength="3" value="' . (!empty($TieBreak[$ArrI]) ? DecodeFromLetter($TieBreak[$ArrI]):'') . '">&nbsp;';
                }
                print '</div>';
            }

            print '</td>';
	        echo '<td>';
	        // closest to center
	        print '<input type="checkbox" name="Closest[' . $MyRow->TfEvent . '][' . $MyRow->GrMatchNo . ']"' . ($MyRow->TfTbClosest ? ' checked="checked"' : '') . '">&nbsp;'.get_text('ClosestShort', 'Tournament');
	        echo '</td>';
        }
        print '</tr>';
        if (++$ii==2) {
            $StileRiga=($StileRiga=="" ? $StileRiga="warning" : "");
            $ii=0;
        }

        $MyEvent=$MyRow->TfEvent;
    }
    print '<tr><td colspan="' . (9-$Cols2Remove) . '" class="Center"><input type="submit" value="' . get_text('CmdSave') . '"></td></tr>';
} else {
    print '<tr>';
    print '<td colspan="' . (9-$Cols2Remove) . '">';

    list($PP,$NP)=PrecNextPhaseForButton();

    print '<a class="Link" href="ChangePhase(\'' . $PP . '\',' . $Sch. ');">' . get_text('PrecPhase') . '</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="Link" href="ChangePhase(\'' . $NP . '\',' . $Sch. ');">' . get_text('NextPhase') . '</a>';
    print '</td>';
    print '</tr>';
}

echo '</table>';
echo '</form>';
echo '<div id="idOutput"></div>';

include('Common/Templates/tail.php');
