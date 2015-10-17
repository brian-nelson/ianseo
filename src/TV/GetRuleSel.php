<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

CheckTourSession(true);



$result='';

$EvType='';
$Cols=array('WIDTH');
$Arr_EventIndRule = array();	// Eventi Ind presenti nella regola
$Arr_EventTeamRule = array();	// Eventi Team presenti nella regola
$Arr_PhaseIndRule = array();	// fasi Ind presenti nella regola
$Arr_PhaseTeamRule = array();	// fasi Team presenti nella regola
$Arr_ColumnsRule = array();	// Columns to show presenti nella regola

$ResCols=array(); // event/phase/column selection
$i=0;

if(!empty($_REQUEST['Id']) and isset($_REQUEST['RuleId'])) {

	// get the rule settings if any
	$DBId=intval($_REQUEST['RuleId']);
	$Select
		= "SELECT * FROM TVParams "
		. "WHERE TVPId=$DBId "
		. "AND TVPTournament={$_SESSION['TourId']}";
	$Rs=safe_r_sql($Select);
	if($r=safe_fetch($Rs)) {
		// select the correct piece of chain to edit
		if ($r->TVPEventInd!='') $Arr_EventIndRule = explode('|',$r->TVPEventInd);
		if ($r->TVPEventTeam!='') $Arr_EventTeamRule = explode('|',$r->TVPEventTeam);
		if ($r->TVPPhasesInd!='') {
			$tmp = explode('|',$r->TVPPhasesInd);
			if(strstr($tmp[0],'+')) {
				foreach($tmp as $k => $v) {
					$t=explode('+',$v);
					$l=array_shift($t);
					$Arr_PhaseIndRule[$l]=$t;
				}
			} else {
				$Arr_PhaseIndRule=$tmp;
			}
		}
		if ($r->TVPPhasesTeam!='') {
			$tmp = explode('|',$r->TVPPhasesTeam);
			if(strstr($tmp[0],'+')) {
				foreach($tmp as $k => $v) {
					$t=explode('+',$v);
					$l=array_shift($t);
					$Arr_PhaseTeamRule[$l]=$t;
				}
			} else {
				$Arr_PhaseTeamRule=$tmp;
			}
		}
		if ($r->TVPColumns!='') $Arr_ColumnsRule = explode('|',$r->TVPColumns);
	} else {
		$Arr_ColumnsRule='ALL';
	}

	switch($_REQUEST['Id']) {
		case 'QUAL': // Divs and Clas of the Competition (individual)
		case 'QUALS': // ==> snapshot
		case 'QUALC': // ==> SubClasses
			$Cols[]='TEAM';
			$Cols[]='DIST';
			if($_REQUEST['Id']=='QUAL' or $_REQUEST['Id']=='QUALC') {
				$Cols[]='10';
				$Cols[]='X9';
				$Cols[]='COMP';
			} else {
				$Cols[]='TOT';
			}

			$Select
				= "SELECT DISTINCT EnDivision,EnClass,DivDescription,ClDescription "
				. "FROM Entries "
				. "INNER JOIN Divisions ON EnDivision=DivId AND EnTournament=DivTournament AND DivAthlete=1 "
				. "INNER JOIN Classes ON EnClass=ClId AND EnTournament=ClTournament AND ClAthlete=1 "
				. "WHERE EnTournament={$_SESSION['TourId']} "
				. "ORDER BY DivViewOrder, ClViewOrder ";
			$Rs=safe_r_sql($Select);

			$ResCols[$i]['header']=get_text('TVFilterEventInd','Tournament');
			if (safe_num_rows($Rs)) {
				while ($r=safe_fetch($Rs)) {
					$ResCols[$i]['data'][] = '<input type="checkbox" name="d_TVEventInd[]"'
						. (in_array($r->EnDivision . $r->EnClass, $Arr_EventIndRule) ? ' checked' : '')
						. ' value="' . $r->EnDivision . $r->EnClass . '">' . get_text($r->DivDescription,'','',true) . ' - ' . get_text($r->ClDescription,'','',true)
						. ' (' . $r->EnDivision . $r->EnClass . ')';
				}
			}
			break;
		case 'QUALT': // Divs and Clas of the Competition (team)
			$Cols[]='ATHL';
			$Cols[]='10';
			$Cols[]='X9';

			$Select
				= "SELECT distinct DivId, ClId, DivDescription, ClDescription, ClViewOrder, DivViewOrder
					FROM
						Tournament
						INNER JOIN
							Teams
						ON ToId=TeTournament AND TeFinEvent=0
						left JOIN
							(
								SELECT CONCAT(DivId, ClId) DivClass, Divisions.*, Classes.*
								FROM
									Divisions
										INNER JOIN Classes
									ON DivTournament=ClTournament
								WHERE
									DivAthlete and ClAthlete
							) AS DivClass
						ON TeEvent=DivClass AND TeTournament=DivTournament
					WHERE
						ToId={$_SESSION['TourId']}
					ORDER BY DivViewOrder, ClViewOrder";
			$Rs=safe_r_sql($Select);

			$ResCols[$i]['header']=get_text('TVFilterEventTeam','Tournament');
			if (safe_num_rows($Rs)) {
				while ($r=safe_fetch($Rs)) {
					$ResCols[$i]['data'][] = '<input type="checkbox" name="d_TVEventTeam[]"'
						. (in_array($r->DivId . $r->ClId, $Arr_EventTeamRule) ? ' checked' : '')
						. ' value="' . $r->DivId . $r->ClId . '">' . get_text($r->DivDescription,'','',true) . ' - ' . get_text($r->ClDescription,'','',true)
						. ' (' . $r->DivId . $r->ClId . ')';
				}
			}
			break;
		case 'ABS':
		case 'ABSS':
			$Cols[]='TEAM';
			$Cols[]='DIST';
			if($_REQUEST['Id']=='ABS') {
				$Cols[]='10';
				$Cols[]='X9';
				$Cols[]='COMP';
			} else {
				$Cols[]='TOT';
			}

			$Select
				= "SELECT EvCode,EvEventName "
				. "FROM Events "
				. "WHERE EvTournament={$_SESSION['TourId']} and EvTeamEvent=0 "
				. "ORDER BY EvTeamEvent ASC, EvProgr ";

			$Rs=safe_r_sql($Select);

			$ResCols[$i]['header']=get_text('TVFilterEventInd','Tournament');
			if (safe_num_rows($Rs)) {
				while ($r=safe_fetch($Rs)) {
					$ResCols[$i]['data'][] = '<input type="checkbox" name="d_TVEventInd[]"'
						. (in_array($r->EvCode, $Arr_EventIndRule) ? ' checked' : '')
						. ' value="' . $r->EvCode . '">' . get_text($r->EvEventName,'','',true)
						. ' (' . $r->EvCode . ')';
				}
			}
			break;
		case 'ABST':
			$Cols[]='ATHL';
			$Cols[]='10';
			$Cols[]='X9';

			$Select
				= "SELECT EvCode,EvEventName "
				. "FROM Events "
				. "WHERE EvTournament={$_SESSION['TourId']} and EvTeamEvent=1 "
				. "ORDER BY EvTeamEvent ASC, EvProgr ";

			$Rs=safe_r_sql($Select);

			$ResCols[$i]['header']=get_text('TVFilterEventInd','Tournament');
			if (safe_num_rows($Rs)) {
				while ($r=safe_fetch($Rs)) {
					$ResCols[$i]['data'][] = '<input type="checkbox" name="d_TVEventTeam[]"'
						. (in_array($r->EvCode, $Arr_EventTeamRule) ? ' checked' : '')
						. ' value="' . $r->EvCode . '">' . get_text($r->EvEventName,'','',true)
						. ' (' . $r->EvCode . ')';
				}
			}
			break;
		case 'ELIM':
			$Cols[]='TEAM';
			$Cols[]='10';
			$Cols[]='X9';
			$Cols[]='TOT';

			$Select
				= "SELECT EvCode,EvEventName "
				. "FROM Events "
				. "WHERE EvTournament={$_SESSION['TourId']} and EvTeamEvent=0 "
				. "ORDER BY EvTeamEvent ASC, EvProgr ";

			$Rs=safe_r_sql($Select);

			$ResCols[$i]['header']=get_text('TVFilterEventInd','Tournament');
			if (safe_num_rows($Rs)) {
				while ($r=safe_fetch($Rs)) {
					$ResCols[$i]['data'][] = '<input type="checkbox" name="d_TVEventInd[]"'
						. (in_array($r->EvCode, $Arr_EventIndRule) ? ' checked' : '')
						. ' value="' . $r->EvCode . '">' . get_text($r->EvEventName,'','',true)
						. ' (' . $r->EvCode . ')';
				}
			}

			$i++;

			$ResCols[$i]['header']=get_text('TVFilterPhaseIndFinal','Tournament');
			$ResCols[$i]['data'][] = '<input type="checkbox" name="d_TVPhaseInd[]"'
						. (in_array('1', $Arr_PhaseIndRule) ? ' checked' : '')
						. ' value="1">' . get_text('Eliminations_1');
			$ResCols[$i]['data'][] = '<input type="checkbox" name="d_TVPhaseInd[]"'
						. (in_array('2', $Arr_PhaseIndRule) ? ' checked' : '')
						. ' value="2">' . get_text('Eliminations_2');

			break;
		case 'FIN':
			$Cols[]='TEAM';
//			$Cols[]='10';
			$Cols[]='BYE';

			$Select
				= "SELECT MAX(EvFinalFirstPhase) AS Phase FROM Events where EvTeamEvent=0 and EvTournament={$_SESSION['TourId']}";
			$Rs=safe_r_sql($Select);

			if($MaxPhase=safe_fetch($Rs) and $MaxPhase->Phase) {
				// there are some finals!
				if($MaxPhase->Phase==48) $MaxPhase->Phase=64;
				elseif($MaxPhase->Phase==24) $MaxPhase->Phase=32;

				$tmp=array();
				while ($MaxPhase->Phase>=1) {
					$txt=get_text($MaxPhase->Phase . '_Phase');
					if($MaxPhase->Phase==64) $txt = get_text('48_Phase') . '-' . get_text('64_Phase');
					elseif($MaxPhase->Phase==32) $txt = get_text('24_Phase') . '-' . get_text('32_Phase');
					$tmp[] = '<input type="checkbox" name="d_TVPhaseInd[]" disabled="disabled" value="'.$MaxPhase->Phase.'"><span style="color:#e0e0e0;">' . $txt . '</span>';

					$MaxPhase->Phase/=2;
				}
				$tmp[] = '<input type="checkbox" name="d_TVPhaseInd[]" value="0"><span>' . get_text('0_Phase') . '</span>';

				$OrgPhases=implode('&nbsp;', $tmp);

				$Select
					= "SELECT EvCode, EvEventName, EvFinalFirstPhase "
					. "FROM Events "
					. "WHERE EvTournament={$_SESSION['TourId']} and EvTeamEvent=0 and EvFinalFirstPhase>0 and EvCode not in ('".implode("','", $_SESSION['MenuFinI'])."')"
					. "ORDER BY EvTeamEvent ASC, EvProgr ";

				$Rs=safe_r_sql($Select);

				$ResCols[$i]['header']=get_text('TVFilterEventInd','Tournament');
				$ResCols[$i+1]['header']=get_text('TVFilterPhaseIndFinal','Tournament');
				if (safe_num_rows($Rs)) {
					while ($r=safe_fetch($Rs)) {
						$ResCols[$i]['data'][] = '<input type="checkbox" name="d_TVEventInd[]"'
							. (in_array($r->EvCode, $Arr_EventIndRule) ? ' checked' : '')
							. ' value="' . $r->EvCode . '">' . get_text($r->EvEventName,'','',true)
							. ' (' . $r->EvCode . ')';

						$tmp = $OrgPhases;
						if($r->EvFinalFirstPhase==48) $r->EvFinalFirstPhase=64;
						elseif($r->EvFinalFirstPhase==24) $r->EvFinalFirstPhase=32;

						while ($r->EvFinalFirstPhase>=1) {
							$tmp=str_replace('name="d_TVPhaseInd[]" disabled="disabled" value="'.$r->EvFinalFirstPhase.'"><span style="color:#e0e0e0;">', 'name="d_TVPhaseInd['.$r->EvCode.'][]" value="'.$r->EvFinalFirstPhase.'" id="id_'.$r->EvCode.'_'.$r->EvFinalFirstPhase.'"><span>', $tmp);
							if(!empty($Arr_PhaseIndRule[$r->EvCode]) && in_array($r->EvFinalFirstPhase, $Arr_PhaseIndRule[$r->EvCode])) $tmp=str_replace('name="d_TVPhaseInd['.$r->EvCode.'][]" value="'.$r->EvFinalFirstPhase.'"', 'name="d_TVPhaseInd['.$r->EvCode.'][]" value="'.$r->EvFinalFirstPhase.'" checked="checked"', $tmp);

							$r->EvFinalFirstPhase/=2;
						}
						$tmp=str_replace('name="d_TVPhaseInd[]" value="0">', 'name="d_TVPhaseInd['.$r->EvCode.'][]" value="0" id="id_'.$r->EvCode.'_0">', $tmp);
						if(!empty($Arr_PhaseIndRule[$r->EvCode]) && in_array(0, $Arr_PhaseIndRule[$r->EvCode])) $tmp=str_replace('name="d_TVPhaseInd['.$r->EvCode.'][]" value="0"', 'name="d_TVPhaseInd['.$r->EvCode.'][]" value="0" checked="checked"', $tmp);
						$ResCols[$i+1]['data'][]=$tmp;
					}
				}
			}

			$i++;
			// select schedules
			$TmpHht=array();
			$Select
				= "(SELECT DISTINCT CONCAT(FSScheduledDate,' ',FSScheduledTime) AS MyDate,FSTeamEvent "
				. "FROM Tournament INNER JOIN FinSchedule ON ToId=FSTournament "
				. ($_SESSION["MenuHHT"] ? "INNER JOIN HhtEvents on HeTournament=ToId and HeFinSchedule=concat(FSScheduledDate, ' ', FSScheduledTime) and HeTeamEvent=FsTeamEvent " : "")
				. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " and FSScheduledDate>0 and FSTeamEvent=0 "
				.") UNION ALL "
				. "(SELECT DISTINCT CONCAT(FSScheduledDate,' ',FSScheduledTime) AS MyDate,FSTeamEvent "
				. "FROM Tournament INNER JOIN FinSchedule ON ToId=FSTournament "
				. ($_SESSION["MenuHHT"] ? "inner join HhtEvents on HeTournament=ToId and HeFinSchedule=concat(FSScheduledDate, ' ', FSScheduledTime) and HeTeamEvent=FsTeamEvent  " : "")
				. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " and FSScheduledDate>0 and FSTeamEvent!=0 "
				. ") ORDER BY MyDate ASC ";

			$Rs=safe_r_sql($Select);
			while($myRow=safe_fetch($Rs)) {
				if($myRow->FSTeamEvent)
					continue;
				$TmpHht[]='<option value="'.$myRow->FSTeamEvent . $myRow->MyDate.'">' . get_text('Individual') . ' ' . $myRow->MyDate . '</option>';
			}
			$Cols[] = '<br><select name="d_Scheduler" id="d_Scheduler" onchange="selectSchedule(this.value)"><option value="">--</option>' . implode('', $TmpHht) . '</select>';
			$Cols[] = '<input type="checkbox" id="useHHT" ' . ($_SESSION["MenuHHT"] ? "checked" : "disabled") . ' onClick="GetComboSchedule(0);">' . get_text('FollowHHT','Tournament');
			$Cols[] = '<input type="checkbox" id="onlyToday" onClick="GetComboSchedule(0);">' . get_text('OnlyToday','Tournament');
			break;
		case 'FINT':
			$Cols[]='ATHL';
			$Cols[]='FLAG';
//			$Cols[]='10';
			$Cols[]='BYE';

			$Select
				= "SELECT MAX(EvFinalFirstPhase) AS Phase FROM Events where EvTeamEvent=1 and EvTournament={$_SESSION['TourId']}";
			$Rs=safe_r_sql($Select);

			if($MaxPhase=safe_fetch($Rs) and $MaxPhase->Phase) {
				// there are some finals!
				if($MaxPhase->Phase==48) $MaxPhase->Phase=64;
				elseif($MaxPhase->Phase==24) $MaxPhase->Phase=32;

				$tmp=array();
				while ($MaxPhase->Phase>=1) {
					$txt=get_text($MaxPhase->Phase . '_Phase');
					if($MaxPhase->Phase==64) $txt = get_text('48_Phase') . '-' . get_text('64_Phase');
					elseif($MaxPhase->Phase==32) $txt = get_text('24_Phase') . '-' . get_text('32_Phase');
					$tmp[] = '<input type="checkbox" name="d_TVPhaseTeam[]" disabled="disabled" value="'.$MaxPhase->Phase.'"><span style="color:#e0e0e0;">' . $txt . '</span>';

					$MaxPhase->Phase/=2;
				}
				$tmp[] = '<input type="checkbox" name="d_TVPhaseTeam[]" value="0"><span>' . get_text('0_Phase') . '</span>';

				$OrgPhases=implode('&nbsp;', $tmp);

				$Select
					= "SELECT EvCode, EvEventName, EvFinalFirstPhase "
					. "FROM Events "
					. "WHERE EvTournament={$_SESSION['TourId']} and EvTeamEvent=1 and EvFinalFirstPhase>0 and EvCode not in ('".implode("','", $_SESSION['MenuFinT'])."')"
					. "ORDER BY EvTeamEvent ASC, EvProgr ";

				$Rs=safe_r_sql($Select);

				$ResCols[$i]['header']=get_text('TVFilterEventTeam','Tournament');
				$ResCols[$i+1]['header']=get_text('TVFilterPhaseTeamFinal','Tournament');
				if (safe_num_rows($Rs)) {
					while ($r=safe_fetch($Rs)) {
						$ResCols[$i]['data'][] = '<input type="checkbox" name="d_TVEventTeam[]"'
							. (in_array($r->EvCode, $Arr_EventTeamRule) ? ' checked' : '')
							. ' value="' . $r->EvCode . '">' . get_text($r->EvEventName,'','',true)
							. ' (' . $r->EvCode . ')';

						$tmp = $OrgPhases;
						if($r->EvFinalFirstPhase==48) $r->EvFinalFirstPhase=64;
						elseif($r->EvFinalFirstPhase==24) $r->EvFinalFirstPhase=32;

						while ($r->EvFinalFirstPhase>=1) {
							$tmp=str_replace('name="d_TVPhaseTeam[]" disabled="disabled" value="'.$r->EvFinalFirstPhase.'"><span style="color:#e0e0e0;">', 'name="d_TVPhaseTeam['.$r->EvCode.'][]" value="'.$r->EvFinalFirstPhase.'" id="id_'.$r->EvCode.'_'.$r->EvFinalFirstPhase.'"><span>', $tmp);
							if(!empty($Arr_PhaseTeamRule[$r->EvCode]) && in_array($r->EvFinalFirstPhase, $Arr_PhaseTeamRule[$r->EvCode])) $tmp=str_replace('name="d_TVPhaseTeam['.$r->EvCode.'][]" value="'.$r->EvFinalFirstPhase.'"', 'name="d_TVPhaseTeam['.$r->EvCode.'][]" value="'.$r->EvFinalFirstPhase.'" checked="checked"', $tmp);

							$r->EvFinalFirstPhase/=2;
						}
						$tmp=str_replace('name="d_TVPhaseTeam[]" value="0">', 'name="d_TVPhaseTeam['.$r->EvCode.'][]" value="0" id="id_'.$r->EvCode.'_0">', $tmp);
						if(!empty($Arr_PhaseTeamRule[$r->EvCode]) && in_array(0, $Arr_PhaseTeamRule[$r->EvCode])) $tmp=str_replace('name="d_TVPhaseTeam['.$r->EvCode.'][]" value="0"', 'name="d_TVPhaseTeam['.$r->EvCode.'][]" value="0" checked="checked"', $tmp);
						$ResCols[$i+1]['data'][]=$tmp;
					}
				}
			}

			$i++;
			// select schedules
			$TmpHht=array();
			$Select
				= "(SELECT DISTINCT CONCAT(FSScheduledDate,' ',FSScheduledTime) AS MyDate,FSTeamEvent "
				. "FROM Tournament INNER JOIN FinSchedule ON ToId=FSTournament "
				. ($_SESSION["MenuHHT"] ? "INNER JOIN HhtEvents on HeTournament=ToId and HeFinSchedule=concat(FSScheduledDate, ' ', FSScheduledTime) and HeTeamEvent=FsTeamEvent " : "")
				. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " and FSScheduledDate>0 and FSTeamEvent=1 "
				.") UNION ALL "
				. "(SELECT DISTINCT CONCAT(FSScheduledDate,' ',FSScheduledTime) AS MyDate,FSTeamEvent "
				. "FROM Tournament INNER JOIN FinSchedule ON ToId=FSTournament "
				. ($_SESSION["MenuHHT"] ? "inner join HhtEvents on HeTournament=ToId and HeFinSchedule=concat(FSScheduledDate, ' ', FSScheduledTime) and HeTeamEvent=FsTeamEvent  " : "")
				. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " and FSScheduledDate>0 and FSTeamEvent!=1 "
				. ") ORDER BY MyDate ASC ";

			$Rs=safe_r_sql($Select);
			while($myRow=safe_fetch($Rs)) {
				if(!$myRow->FSTeamEvent) continue;
				$TmpHht[]='<option value="'.$myRow->FSTeamEvent . $myRow->MyDate.'">' . get_text('Team') . ' ' . $myRow->MyDate . '</option>';
			}
			$Cols[] = '<br><select name="d_Scheduler" id="d_Scheduler" onchange="selectSchedule(this.value)"><option value="">--</option>' . implode('', $TmpHht) . '</select>';
			$Cols[] = '<input type="checkbox" id="useHHT" ' . ($_SESSION["MenuHHT"] ? "checked" : "disabled") . ' onClick="GetComboSchedule(1);">' . get_text('FollowHHT','Tournament');
			$Cols[] = '<input type="checkbox" id="onlyToday" onClick="GetComboSchedule(1);">' . get_text('OnlyToday','Tournament');
			break;
		case 'LIST':
			break;
		case 'LSPH':
			break;
		case 'RAND':
			break;
		case 'F2FLST':
		case 'F2FABS':
		case 'NLCLST':
		case 'NLCABS':
			$Select
				= "SELECT DISTINCT EnDivision,EnClass,DivDescription,ClDescription "
				. "FROM Entries "
				. "INNER JOIN Divisions ON EnDivision=DivId AND EnTournament=DivTournament AND DivAthlete=1 "
				. "INNER JOIN Classes ON EnClass=ClId AND EnTournament=ClTournament AND ClAthlete=1 "
				. "WHERE EnTournament={$_SESSION['TourId']} "
				. "ORDER BY DivViewOrder, ClViewOrder ";
			$Rs=safe_r_sql($Select);

			$ResCols[$i]['header']=get_text('TVFilterEventInd','Tournament');
			if (safe_num_rows($Rs)) {
				while ($r=safe_fetch($Rs)) {
					$ResCols[$i]['data'][] = '<input type="checkbox" name="d_TVEventInd[]"'
						. (in_array($r->EnDivision . $r->EnClass, $Arr_EventIndRule) ? ' checked' : '')
						. ' value="' . $r->EnDivision . $r->EnClass . '">' . get_text($r->DivDescription,'','',true) . ' - ' . get_text($r->ClDescription,'','',true)
						. ' (' . $r->EnDivision . $r->EnClass . ')';
				}
			}

			$i++;

			$ResCols[$i]['header']=get_text('TVFilterPhaseIndFinal','Tournament');
			$Select
				= "SELECT distinct F2FPhase AS Phase FROM F2FEntries where F2FTournament={$_SESSION['TourId']} order by Phase";
			$Rs=safe_r_sql($Select);

			while($MyRow=safe_fetch($Rs)) {
				$ResCols[$i]['data'][] = '<input type="checkbox" name="d_TVPhaseInd[]"'
					. (in_array($MyRow->Phase, $Arr_PhaseIndRule) ? ' checked' : '')
					. ' value="'.$MyRow->Phase.'">' . get_text('Phase') . ' ' . $MyRow->Phase;
			}
			break;
	}
	$i++;
}

if($Cols) {
	// select the columns to show
	$ResCols[$i]['header']=get_text('TVFilterColumns','Tournament');
	foreach($Cols as $col) {
		if($col[0]=='<') {
			$ResCols[$i]['data'][] = $col ;
		} elseif($col=='COMP') {
			$compareTo='0';
			foreach($Arr_ColumnsRule as $sub) {
				if(substr($sub,0,4)==$col) $compareTo=substr($sub,5);
			}
			$ResCols[$i]['data'][] = '<input type="text" size="2" name="d_TVColumns[' . $col . ']"'
					. ' value="' . $compareTo . '">' . get_text('TvView'.$col,'Tournament') ;
		} elseif($col=='WIDTH') {
			$TotWidthCol=7;
			foreach($Arr_ColumnsRule as $sub) {
				if(substr($sub,0,5)==$col) $TotWidthCol=substr($sub,6);
			}
			$ResCols[$i]['data'][] = '<input type="text" size="2" name="d_TVColumns[' . $col . ']"'
				. ' value="' . $TotWidthCol . '">' . get_text('TvView'.$col,'Tournament') ;
		} else {
			$ResCols[$i]['data'][] = '<input type="checkbox" name="d_TVColumns[' . $col . ']"'
				. (($Arr_ColumnsRule=='ALL' or in_array($col, $Arr_ColumnsRule) ) ? ' checked' : '')
				. ' value="' . $col . '">' . get_text('TvView'.$col,'Tournament') ;
		}
	}
}

if($ResCols) {
	$result='<table width="100%" class="Tabella">';
	// Column headers
	$result.='<tr>';
	foreach($ResCols as $ColId => $data) {
		$result.='<th class="TitleCenter">' . $data['header'] . '</th>';
	}
	$result.='</tr>';
	$result.='<tr valign="top">';
	foreach($ResCols as $ColId => $data) {
		$result.='<td nowrap="nowrap">'.implode('<br/>', $data['data']).'</td>';
	}
	$result.='</tr></table>';
}

header('Content-Type: text/xml');

print '<response>' . "\n";
print '<result><![CDATA[' . $result . ']]></result>';
print '</response>' . "\n";

die();
