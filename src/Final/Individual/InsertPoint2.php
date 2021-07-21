<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Final/Fun_ChangePhase.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
    require_once('Common/Lib/CommonLib.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Lib/Fun_Phases.inc.php');
	//require_once('HHT/Fun_HHT.local.inc.php');

	if (!CheckTourSession() || !isset($_REQUEST['d_Phase'])) PrintCrackError();
    checkACL(AclIndividuals, AclReadWrite);

	$Cols2Remove = (isset($_REQUEST['d_Tie']) && $_REQUEST['d_Tie']==1 ? 0 : 2);

	$Error=false;

	$Tie_Error = array();	// Contiene gl'indici delle tendine Tie con errore
	$Score_Error = array(); // contiene gl'indici degli score che superano il max
	$Set_Error = array(); // contiene gl'indici dei set che superano il max

	if (isset($_REQUEST['Command']) && $_REQUEST['Command']=='SAVE' && !IsBlocked(BIT_BLOCK_IND)) {
	// Update dei punti e dei tie
		$AllowedEvents=array();
		foreach ($_REQUEST as $Key => $Value) {
			if (strpos($Key,'S_')===0) {
				if (!(is_numeric($Value) && $Value>=0)) {
					$Value=0;
				}

				$ee=""; $mm="";	// evento e matchno estratti dal nome campo
				list(,$ee,$mm)=explode('_',$Key);

				// get the match maximum values
				$MaxScores=GetMaxScores($ee, $mm);

				if(empty($AllowedEvents[$ee])) {
					// dal matchno recupero la fase
					$t=safe_r_sql("select GrPhase from Grids where GrMatchNo=".intval($mm));
					if($u=safe_fetch($t)) $AllowedEvents[$ee]=$u->GrPhase;
				}

				if ($Value > ($MaxScores['MaxSetPoints'] ? $MaxScores['MaxSetPoints'] : $MaxScores['MaxMatch']) ) {
					$Score_Error[$ee . '_' . $mm]=true;
				} else {
					$Update = "UPDATE Finals SET "
						. (isset($_REQUEST['X_' . $ee . '_' . $mm]) && $_REQUEST['X_' . $ee . '_' . $mm]==0 ? "FinScore" : "FinSetScore") . "=" . StrSafe_DB($Value) . " ";

					//Cerco i punti dei set, SE gara a set
					if(isset($_REQUEST['X_' . $ee . '_' . $mm]) && $_REQUEST['X_' . $ee . '_' . $mm]!=0) {
					    $setPoints=array();
					    $setScore=0;
						if(isset($_REQUEST['P_' . $ee . '_' . $mm]) && is_array($_REQUEST['P_' . $ee . '_' . $mm])) {
							//Valido i punti set POI decido se mi vanno bene o no
							foreach($_REQUEST['P_' . $ee . '_' . $mm] as $setPoint) {
								if(strlen($setPoint)>0) {
                                    if((!is_numeric($setPoint) && $setPoint!='') or $setPoint > $MaxScores['MaxEnd']) {
                                        $Set_Error[$ee . '_' . $mm]=true;
                                    } else {
                                        $setPoints[]=intval($setPoint);
                                        $setScore+=intval($setPoint);
                                    }
								}
							}
                            $Update.= ", FinSetPoints='" . implode("|", $setPoints) . "', FinScore='" . $setScore . "' ";
						}
					}

					// Check if we set up to ask for ties
					if (isset($_REQUEST['T_' . $ee . '_' . $mm])) {
                        // devo scrivere i punti di tie
						$tiebreak = '';

						if (isset($_REQUEST['t_' . $ee . '_' . $mm]) && is_array($_REQUEST['t_' . $ee . '_' . $mm])) {
							foreach ($_REQUEST['t_' . $ee . '_' . $mm] as $TieKey => $TieValue) {
								$tiebreak.=(GetLetterFromPrint($TieValue)!=' ' ? GetLetterFromPrint($TieValue) : ' ');
							}

							$Update.= ", FinTbClosest=" . (empty($_REQUEST['cl_'. $ee . '_' . $mm]) ? 0 : 1);

                            // split the SO arrows in Decoded ends
                            $Params=getEventArrowsParams($ee,getPhase($mm),0);
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
                            $Update.= ", FinTbDecoded='" . ($TbDecoded ? implode(",",$TbDecoded).(!empty($_REQUEST['cl_'. $ee . '_' . $mm]) ? '+' : '') : '') . "'";
						}
						$Update.= ", FinTieBreak=" . StrSafe_DB($tiebreak) . " ";


					    // devo settare la tendina del tie
						$t="";

						if(substr($_REQUEST['T_' . $ee .'_' . $mm], 0, 3)=='irm') {
						    // IRM "low" status (DNF/DNS)
                            $Update.= ", FinIrmType=" . intval(substr($_REQUEST['T_' . $ee .'_' . $mm], 4)) . " ";
                        } elseif(is_numeric($_REQUEST['T_' . $ee .'_' . $mm]) && $_REQUEST['T_' . $ee .'_' . $mm]>=0) {
							$t=$_REQUEST['T_' . $ee .'_' . $mm];

                            /*
                                Nel caso $t=1 la verifica che sia assegnato a chi ha l'ultima freccia di tiebreak più alta è fatta dai passaggi di classe.
                                Segnalo se entrambe le persone hanno il bye
                            */
                            $TieOk=true;
                            if ($t==2) {
                                $IndexOfOne = $ee .'_' . $mm;
                                $First = '';
                                $Last = '';

                                if ($mm%2==0) {
                                    $First = $ee .'_' . $mm;
                                    $Last = $ee .'_' . ($mm+1);
                                } else {
                                    $First = $ee .'_' . ($mm-1);
                                    $Last = $ee .'_' . $mm;
                                }

                                if ($_REQUEST['T_' . $First]==2 && $_REQUEST['T_' . $Last]==2) {
                                    $TieOk = false;
                                } else {
                                    $TieOk = true;
                                }
                            }

                            if ($TieOk) {
                                $Update .= ",FinTie=" . StrSafe_DB($t) . " ";
                            } else {
                                $Tie_Error[$ee . '_' . $mm] = true;
                            }
						}
					}

					$Update .=",FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " "
						. "WHERE FinEvent='" . $ee . "' AND FinMatchNo='" . $mm . "' AND FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
					$RsUp=safe_w_sql($Update);
				}
			}
		}

	// Faccio i passaggi di fase
		foreach($AllowedEvents as $event => $phase) move2NextPhase($phase, $event);
	}

	$PAGE_TITLE=get_text('MenuLM_Data insert (Table view)');

	$JS_SCRIPT=array(
        phpVars2js(array('ROOT_DIR' => $CFG->ROOT_DIR)),
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Individual/Fun_JS.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
		'<script type="text/javascript" src="./InsertPoint2.js"></script>',
        '<style>
            .disabled td {background-color:#bbb} 
            </style>',
		);

	include('Common/Templates/head.php');
?>
<form name="Frm" method="post" action="InsertPoint2.php">
<input type="hidden" name="Command" value="">
<?php

$useSession=false;

$PrecPhase = '';//($_REQUEST['d_Phase']==0 ? 1 : ($_REQUEST['d_Phase']==32 ? 48 :$_REQUEST['d_Phase']*2));
$NextPhase = '';//($_REQUEST['d_Phase']>1 ? ($_REQUEST['d_Phase']==48 ? 32 : ($_REQUEST['d_Phase']==24 ? 16 : $_REQUEST['d_Phase']/2)) : 0);

$PP=$PrecPhase;
$NP=$NextPhase;
$Sch=0;

if(!empty($_REQUEST['d_Event'])) {
	foreach($_REQUEST['d_Event'] as $key => $val) {
		echo '<input type="hidden" name="d_Event[]" id="d_Event_'.$key.'" value="'.$val.'">';
	}
}

if(!empty($_REQUEST['x_Session']) and $_REQUEST['x_Session']!=-1) {
	$useSession=true;

	echo '<input type="hidden" name="x_Session" id="x_Session" value="'.$_REQUEST['x_Session'].'">';

	$ComboArr=array();
	ComboSession('Individuals',$ComboArr);

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
?>
<input type="hidden" name="d_Phase" id="d_Phase" value="<?php print $_REQUEST['d_Phase'];?>">
<input type="hidden" name="d_Tie" id="d_Tie" value="<?php print $_REQUEST['d_Tie'];?>">
<input type="hidden" name="d_SetPoint" id="d_SetPoint" value="<?php print isset($_REQUEST['d_SetPoint']) ? $_REQUEST['d_SetPoint'] : '';?>">
<table class="Tabella">
<tr><th class="Title" colspan="<?php print (8-$Cols2Remove);?>"><?php print get_text('IndFinal'); ?></th></tr>
<tr class="Divider"><td colspan="<?php print (8-$Cols2Remove);?>"></td></tr>
<?php

	$QueryFilter = '';

	// if a scheduled round has been sent, it superseeds everything
	if(!empty($_REQUEST['x_Session']) and $_REQUEST['x_Session']!=-1) {
		// get all the pairs event<=>phase for that scheduled time
		$QueryFilter.= "AND concat(FSTeamEvent, FSScheduledDate, ' ', FSScheduledTime)=" . strSafe_DB($_REQUEST['x_Session']) . " ";
	} elseif(!empty($_REQUEST['d_Event'])) {
		// creates the filter on the matching events and phase
		$tmp=array();
		foreach($_REQUEST['d_Event'] as $event) $tmp[]=StrSafe_DB($event);
		sort($tmp);
		if($tmp) $QueryFilter.= "AND FinEvent in (" . implode(',', $tmp) . ") ";
		$QueryFilter.= "AND GrPhase=" . StrSafe_DB($_REQUEST['d_Phase']) . " ";
	}

//	Tiro fuori l'elenco degli eventi non spareggiati

	$Select
		= "SELECT distinct EvCode, EvEventName, EvMatchMode "
		. "FROM Finals "
		. "left JOIN FinSchedule ON FinEvent=FSEvent AND FsMatchNo=FinMatchNo AND FsTeamEvent='0' AND FsTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "INNER JOIN Events ON FinEvent=EvCode AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "INNER JOIN Grids ON FinMatchNo=GrMatchNo "
		. "WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvShootOff='0' " . $QueryFilter
		. "ORDER BY EvProgr ASC,EvCode, GrMatchNo ASC ";
	$Rs=safe_r_sql($Select);

	$Elenco = '';
	while ($Row=safe_fetch($Rs)) {
		$Elenco.= get_text($Row->EvEventName,'','',true) . ' (' . $Row->EvCode . ')<br>';
	}

	if ($Elenco!='') {
		print '<tr><td class="Bold" colspan="' . (8-$Cols2Remove) . '">' . get_text('IndFinEventWithoutShootOff') . '</td></tr>';
		print '<tr><td colspan="' . (8-$Cols2Remove) . '">';
		print $Elenco;
		print '</td>';
		print '</tr>';
		print '<tr class="Divider"><td colspan="' . (8-$Cols2Remove) . '"></td></tr>';
	}

    $IrmOptions=array();
    $q=safe_r_sql("select * from IrmTypes where IrmId>0 order by IrmId");
    while($r=safe_fetch($q)) {
        $IrmOptions[]=$r;
    }

// tiro fuori solo gli eventi spareggiati
	$Select = "SELECT FinEvent,FinMatchNo,FinTournament,FinAthlete, IF(EvMatchMode=0,FinScore,FinSetScore) AS Score, FinTie, FinTiebreak, FinSetPoints, FinTbClosest,
	        EvProgr,EvFinalFirstPhase,EvEventName, EvMatchMode, EvMatchArrowsNo, FsTarget,
	        GrMatchNo,GrPhase, IF(EvFinalFirstPhase=48, GrPosition2, if(GrPosition>EvNumQualified, 0, GrPosition)) as GrPosition,
	        CONCAT(EnFirstName,' ',EnName) AS Athlete,EnCountry, FinIrmType, IrmType,
	        CoCode,CoName
        FROM Finals
        inner join IrmTypes on IrmId=FinIrmType
        INNER JOIN Events ON FinEvent=EvCode AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . "
        INNER JOIN Grids ON FinMatchNo=GrMatchNo
        left JOIN FinSchedule ON FinEvent=FSEvent AND FsMatchNo=FinMatchNo AND FsTeamEvent='0' AND FsTournament=" . StrSafe_DB($_SESSION['TourId']) . "
        LEFT JOIN Entries ON FinAthlete=EnId
        LEFT JOIN Countries ON EnCountry=CoId
        WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvShootOff='1' " . $QueryFilter . "
        ORDER BY EvProgr ASC,EvCode, GrMatchNo ASC ";
	$Rs=safe_r_sql($Select);
	//print $Select;

	if (safe_num_rows($Rs)>0) {
		$MyEvent = '-----';
		while ($MyRow=safe_fetch($Rs)) {
			$obj=getEventArrowsParams($MyRow->FinEvent,$MyRow->GrPhase,0);
			if ($MyEvent!=$MyRow->FinEvent) {
				$ii=0;
				$StileRiga="";
				if ($MyEvent!='-----') {
					print '<tr><td colspan="' . (8-$Cols2Remove) . '" class="Center"><input type="submit" value="' . get_text('CmdSave') . '" onclick="document.Frm.Command.value=\'SAVE\'"></td></tr>';
					print '<tr class="Divider"><td></td></tr>';
				}
				print '<tr><th colspan="' . (8-$Cols2Remove) . '" class="Title">' . get_text($MyRow->EvEventName,'','',true) . ' (' . $MyRow->FinEvent . ') - ' . get_text('Phase') . ' ' . get_text(namePhase($MyRow->EvFinalFirstPhase,$MyRow->GrPhase) . '_Phase') . '</th></tr>';
				print '<tr>';
				print '<td colspan="' . (8-$Cols2Remove) . '" class="nowrap">';
				//$PrecPhase = ($_REQUEST['d_Phase']==0 ? 1 : ($_REQUEST['d_Phase']==32 ? 48 :$_REQUEST['d_Phase']*2));
				//$NextPhase = ($_REQUEST['d_Phase']>1 ? ($_REQUEST['d_Phase']==48 ? 32 : ($_REQUEST['d_Phase']==24 ? 16 : $_REQUEST['d_Phase']/2)) : 0);
				//print '<a class="Link" href="javascript:ChangePhase(' . $PrecPhase . ');">' . get_text('PrecPhase') . '</a>'

				if (!$useSession) {
					list($PP,$NP)=PrecNextPhaseForButton();
				// queste due solo per i nomi e gli score del turno successivo (e volendo precedente)
					$PrecPhase=$PP;
					$NextPhase=$NP;
				}

				print '<a class="Link mr-5" href="javascript:ChangePhase(\'' . $PP . '\',' .$Sch .');">' . get_text('PrecPhase') . '</a>
				    <a class="Link mr-5" href="javascript:ChangePhase(\'' . $NP . '\',' .$Sch .');">' . get_text('NextPhase') . '</a>
				    <a class="Link mr-5" href="PrnIndividual.php?Event='.$MyRow->FinEvent.'&IncBrackets=1&ShowTargetNo=1" target="Griglie">' . get_text('Brackets') . '</a>';
				if($_REQUEST['d_Phase']>0) {
					echo '<a class="Link mr-5" href="PDFScoreMatch.php?Event='.$MyRow->FinEvent.'&Phase='.$NextPhase.'" target="Scores">' . get_text('NextMatchScores') . '</a>
					    <a class="Link mr-5" href="PrnName.php?Event='.$MyRow->FinEvent.'&Phase='.$NextPhase.'" target="Names">' . get_text('NextMatchNames') . '</a>';
				}
				print '</td>';
				print '</tr>';
				print '<tr>';
				print '<th width="5%">' . get_text('Target') . '</th>';
				print '<th width="5%">' . get_text('Rank') . '</th>';
				print '<th width="30%">' . get_text('Athlete') . '</th>';
				print '<th width="5%">' . get_text('Country') . '</th>';

				if($MyRow->EvMatchMode!=0)
					print '<th width="10%">' . get_text('SetPoints', 'Tournament') . '</th>';
				print '<th width="20%" colspan="' . ($MyRow->EvMatchMode==0 ? '2':'1') . '">' . get_text('Score', 'Tournament') . '</th>';
				if (isset($_REQUEST['d_Tie']) && $_REQUEST['d_Tie']==1) {
					print '<th width="5%">' . get_text('ShotOff', 'Tournament') . '</th>';
					print '<th width="10%">' . get_text('TieArrows') . '</th>';
				}
				print '</tr>';
			}
			$Disabled = ($MyRow->FinIrmType or $MyRow->FinTie==2 or !$MyRow->Athlete);
			print '<tr class="' . $StileRiga . ($Disabled ? ' disabled' : '').'">';
			print '<td class="Center">' . $MyRow->FsTarget . '</td>';
			print '<td class="Center">' . $MyRow->GrPosition . '</td>';
			print '<td>' . (strlen($MyRow->Athlete)>0 ? $MyRow->Athlete : '&nbsp;') . /*' ' . $MyRow->FinAthlete .*/ '</td>';
			print '<td>' . (!is_null($MyRow->CoCode) ? $MyRow->CoCode : '&nbsp;') . '</td>';
			$TextStyle = '';
			if (array_search($MyRow->FinEvent . '_' . $MyRow->GrMatchNo,array_keys($Score_Error))!==false)
					$TextStyle='error';
			//Carico i punti
			print '<td class="Center" colspan="' . ($MyRow->EvMatchMode==0 ? '2':'1') . '">';
			print '<input type="text" class="' . $TextStyle . '" size="3" maxlength="3" name="S_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '" value="' . ($TextStyle=='' ? $MyRow->Score : $_REQUEST['S_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo]) . '">'.' '.$MyRow->IrmType;
			print '<input type="hidden" name="X_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '" value="' . $MyRow->EvMatchMode . '">';
			print '</td>';
			//Carico i set point in math di tipo set
			if($MyRow->EvMatchMode!=0) {
				$TextStyle = '';
				if (array_search($MyRow->FinEvent . '_' . $MyRow->GrMatchNo,array_keys($Set_Error))!==false)
					$TextStyle='error';
				$setPointsArray=explode("|",$MyRow->FinSetPoints);
				print '<td width="10%">';
				for($setNo=0; $setNo<$obj->ends; $setNo++) {
					if (isset($_REQUEST['d_SetPoint']) && $_REQUEST['d_SetPoint']==1)
						print '<input type="text" class="' . $TextStyle . '" size="2" maxlength="2" name="P_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '[' . $setNo . ']" value="' . ($TextStyle=='' ? (isset($setPointsArray[$setNo]) ? $setPointsArray[$setNo]:'') : $_REQUEST['P_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo][$setNo]) . '">&nbsp;';
					else
						print (isset($setPointsArray[$setNo]) ? $setPointsArray[$setNo]:'') . '&nbsp;&nbsp;&nbsp;';
				}
				print '</td>';
			}

			if (isset($_REQUEST['d_Tie']) && $_REQUEST['d_Tie']==1) {
				//print '<td class="Center"><input type="text" size="4" name="T_' . $MyRow->Event . '_' . $MyRow->MatchNo . '" value="' . $MyRow->tie . '"></td>';
				print '<td class="Center">';
				$ComboStyle = '';
				if (array_search($MyRow->FinEvent . '_' . $MyRow->GrMatchNo,array_keys($Tie_Error))!==false)
					$ComboStyle='error';

				print '<select event="M'.$MyRow->FinEvent.'" onchange="CheckIRM(this)" team="0" phase="'.$MyRow->GrPhase.'" class="' . $ComboStyle . '" name="T_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '" id="T_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '">';
				print '<option value="0"' . (($ComboStyle=='' && $MyRow->FinTie==0) || ($ComboStyle!='' && $_REQUEST['T_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo]==0) ? ' selected' : '') . '>0 - ' .	get_text('NoTie', 'Tournament') . '</option>';
				print '<option value="1"' . (($ComboStyle=='' && $MyRow->FinTie==1) || ($ComboStyle!='' && $_REQUEST['T_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo]==1) ? ' selected' : '') . '>1 - ' .	get_text('TieWinner', 'Tournament') . '</option>';
				print '<option value="2"' . (($ComboStyle=='' && $MyRow->FinTie==2) || ($ComboStyle!='' && $_REQUEST['T_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo]==2) ? ' selected' : '') . '>2 - ' .	get_text('Bye') . '</option>';
				foreach($IrmOptions as $irm) {
					if($irm->IrmShowRank) {
						echo '<option value="irm-'.$irm->IrmId.'"' . ($MyRow->FinIrmType==$irm->IrmId ? ' selected' : '') . '>' . $irm->IrmType . '</option>';
					} else {
						echo '<option value="man"' . ($MyRow->FinIrmType==$irm->IrmId ? ' selected' : '') . '>' . $irm->IrmType . '</option>';
					}
				}
				print '</select>';
				print '</td>';

				print '<td id="Tie_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '">';
				$TieBreak = str_pad($MyRow->FinTiebreak,$obj->so,' ',STR_PAD_RIGHT);
				for($pSo=0; $pSo<3; $pSo++ ) {
                    for ($i = 0; $i < $obj->so; ++$i) {
                        $ArrI = $i+($pSo*$obj->so);
                        print '<input type="text" name="t_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '[]" size="2" maxlength="3" value="' . (!empty($TieBreak[$ArrI]) ? DecodeFromLetter($TieBreak[$ArrI]):'') . '">&nbsp;';
                    }
                    print '&nbsp;';
                }
				// closest to center
                print '<input type="checkbox" name="cl_' . $MyRow->FinEvent . '_' . $MyRow->GrMatchNo . '"' . ($MyRow->FinTbClosest ? ' checked="checked"' : '') . '">&nbsp;'.get_text('ClosestShort', 'Tournament');
				print '</td>';
			}
			print '</tr>';
			if (++$ii==2) {
				$StileRiga=($StileRiga=="" ? $StileRiga="warning" : "");
				$ii=0;
			}

			$MyEvent=$MyRow->FinEvent;
		}
		print '<tr><td colspan="' . (8-$Cols2Remove) . '" class="Center"><input type="submit" value="' . get_text('CmdSave') . '" onclick="document.Frm.Command.value=\'SAVE\'"></td></tr>';
	} else {
		print '<tr>';
		print '<td colspan="' . (8-$Cols2Remove) . '">';
		//$PrecPhase = ($_REQUEST['d_Phase']==0 ? 1 : ($_REQUEST['d_Phase']==32 ? 48 :$_REQUEST['d_Phase']*2));
		//$NextPhase = ($_REQUEST['d_Phase']>1 ? ($_REQUEST['d_Phase']==48 ? 32 : ($_REQUEST['d_Phase']==24 ? 16 : $_REQUEST['d_Phase']/2)) : 0);
		//print '<a class="Link" href="javascript:ChangePhase(' . $PrecPhase . ');">' . get_text('PrecPhase') . '</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="Link" href="javascript:ChangePhase(' . $NextPhase . ');">' . get_text('NextPhase') . '</a>';

		list($PP,$NP)=PrecNextPhaseForButton();
		print '<a class="Link" href="javascript:ChangePhase(\'' . $PP . '\',' . $Sch.');">' . get_text('PrecPhase') . '</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="Link" href="javascript:ChangePhase(\'' . $NP . '\','.$Sch.');">' . get_text('NextPhase') . '</a>';
		print '</td>';
		print '</tr>';
	}

?>
</table>
</form>
<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');
?>
