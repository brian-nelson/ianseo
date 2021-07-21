<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
    checkACL(AclEliminations, AclReadWrite);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once ('Fun_Eliminations.local.inc.php');
	require_once ('Common/Lib/CommonLib.php');

	$Events=isset($_REQUEST['Events']) ? $_REQUEST['Events'] : array();

	$Select = "SELECT EvCode, EvTournament, EvEventName, EvElimType, EvElim1, EvElim2
	    FROM Events
	    WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0 AND (EvElim1>0 OR EvElim2>0)
	    ORDER BY EvProgr ASC ";
	$Rs=safe_r_sql($Select);

	$CheckEvent1='';
	$CheckEvent2='';
    $CheckEventPool='';
    $CheckEventPoolWA='';

	if (safe_num_rows($Rs)>0) {
		while($MyRow=safe_fetch($Rs)) {
		    if($MyRow->EvElimType==3) {
                $CheckEventPool .= '<input type="checkbox" name="Events[]" value="' . $MyRow->EvCode . '#2"' . (in_array($MyRow->EvCode . "#2", $Events) ? ' checked' : '') . '>' . $MyRow->EvCode . '&nbsp;&nbsp;';
            } elseif($MyRow->EvElimType==4) {
                $CheckEventPoolWA .= '<input type="checkbox" name="Events[]" value="' . $MyRow->EvCode . '#3"' . (in_array($MyRow->EvCode . "#3", $Events) ? ' checked' : '') . '>' . $MyRow->EvCode . '&nbsp;&nbsp;';
		    } else {
                if ($MyRow->EvElim1 > 0) {
                    $CheckEvent1 .= '<input type="checkbox" name="Events[]" value="' . $MyRow->EvCode . '#0"' . (in_array($MyRow->EvCode . "#0", $Events) ? ' checked' : '') . '>' . $MyRow->EvCode . '&nbsp;&nbsp;';
                }
                if ($MyRow->EvElim2 > 0) {
                    $CheckEvent2 .= '<input type="checkbox" name="Events[]" value="' . $MyRow->EvCode . '#1"' . (in_array($MyRow->EvCode . "#1", $Events) ? ' checked' : '') . '>' . $MyRow->EvCode . '&nbsp;&nbsp;';
                }
            }
		}
	}


	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Elimination/Fun_AJAX_SetTarget.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
        //'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Individual/Fun_AJAX_ManSchedule.js"></script>',
        '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Individual/Fun_AJAX_ManTarget.js"></script>',
    );

	include('Common/Templates/head.php');
?>
<table class="Tabella">
<tr><th class="Title" colspan="2"><?php print get_text('ManualTargetAssignment','Tournament');?></th></tr>
<tr class="Divider"><td colspan="2"></td></tr>
<tr>
<td class="Bold" width="20%"><input type="checkbox" name="chk_BlockAutoSave" id="chk_BlockAutoSave" value="1"><?php echo get_text('CmdBlocAutoSave') ?></td>
<td width="80%">
<form name="FrmParam" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="Tabella">
<tr><td style="width:90%;"><table class="Tabella">
<?php
if ($CheckEvent1!='') {
    print '<tr><td style="width:25%;" class="Right">' . get_text('Eliminations_1'). '</td><td>' . $CheckEvent1.'</td></tr>';
}

if ($CheckEvent2!='') {
    print '<tr><td  style="width:25%;" class="Right">' . get_text('Eliminations_2'). '</td><td>' . $CheckEvent2.'</td></tr>';
}

if ($CheckEventPool!='') {
    print '<tr><td  style="width:25%;" class="Right">' . get_text('WG_Pool2'). '</td><td>' . $CheckEventPool.'</td></tr>';
}

if ($CheckEventPoolWA!='') {
    print '<tr><td  style="width:25%;" class="Right">' . get_text('WA_Pool4'). '</td><td>' . $CheckEventPoolWA.'</td></tr>';
}
?>
</table>
</td>
<td><input type="submit" value="<?php echo get_text('CmdOk')?>"/></td>
</table></form>
</td>
</tr>
</table>
<br>
<?php

$sessions=GetSessions('E');
$EventsFilter="";
$PoolEvents=array();
$in=array();
if (count($Events)>0) {
    foreach ($Events as $e) {
        list($ev,$phase)=explode('#',$e);
        if($phase==2 or $phase==3) {
            $PoolEvents[] = $ev;
        } else  {
            $in[]=StrSafe_DB(str_replace('#','',$e));
        }

    }
    $EventsFilter.=" AND CONCAT(ElEventCode,ElElimPhase) IN(" . implode(',',$in). ") ";
}

if(count($PoolEvents)) {
    // select all the distinct events that already have a schedule defined
    $Schedules=array();
    $q=safe_r_sql("select ScheduledTime, EvCode, EvElimType, group_concat(EvCode order by EvProgr separator '-') as Codes
        from Events
        inner join (select distinct FSEvent, group_concat(distinct date_format(concat(FSScheduledDate, ' ', FSScheduledTime), '%e %b %H:%i') order by FSScheduledDate, FSScheduledTime separator '/') as ScheduledTime 
            from FinSchedule 
            where FSTeamEvent=0 and FSScheduledDate>0 and FSScheduledTime>0 and FSMatchNo>=8 and FSTournament={$_SESSION['TourId']}
            group by FsEvent
            ) Schedule on FSEvent=EvCode
        where EvTeamEvent=0 and EvElimType>2 and EvTournament={$_SESSION['TourId']}
        group by ScheduledTime
        order by ScheduledTime, EvProgr");
    while($r=safe_fetch($q)) {
        $Schedules[$r->EvElimType][]='<option value="'.$r->EvCode.'">'.$r->ScheduledTime.': '.$r->Codes.'</option>';
    }

    $Sql = "SELECT EvCode, EvElimType, EvEventName from Events WHERE EvCode IN ('". implode("','",$PoolEvents) . "') AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0 order by EvProgr";
    $q= safe_r_SQL($Sql);
    $firstLoop = true;
    $tabIndex=1;
    while($r=safe_fetch($q)) {
        if ($firstLoop ) {
            print '</table>';
            $firstLoop = false;
        }
        echo '
            <table class="Tabella" id="block_'.$r->EvCode.'">
            <tr>
                <th class="Title "colspan="4">' . get_text($r->EvElimType==3 ? 'WG_Pool2' : 'WA_Pool4') . " - " . $r->EvEventName . '</th>
            </tr>
            <tr>
                <th class="SubTitle" width="20%">' . get_text('Match', 'Tournament') . '</th>
                <th class="SubTitle" width="20%">
                    <div>' . get_text('MatchesSchedule', 'Tournament') . '</div>
                    '.(empty($Schedules[$r->EvElimType]) ? '' : '<div><select><option value=""></option>'.implode('', $Schedules[$r->EvElimType]).'</select><input type="button" event="'.$r->EvCode.'" value="'.get_text('Clone', 'Tournament').'" onclick="CloneSchedule(this)"></div>').'
                </th>
                <th class="SubTitle" width="10%">' . get_text('Target') . '</th>
                <th class="SubTitle" width="50%"><div align="left">'.get_text('ScheduleAssignmentKeys', 'Tournament').'</div></th>
            </tr>';
        if($r->EvElimType==3) {
            $MatchArray = getPoolMatches();
        } elseif($r->EvElimType==4) {
            $MatchArray = getPoolMatchesWA();
        }
        $MatchSchedule = array();
        $Sql = "SELECT FSMatchNo, FSTarget, DATE_FORMAT(FSScheduledDate,'%d-%m-%Y') AS ScheduledDate,  DATE_FORMAT(FSScheduledTime,'%H:%i') AS ScheduledTime, FSScheduledLen 
            FROM FinSchedule
            WHERE FSEvent='{$r->EvCode}' AND FSTeamEvent=0 AND FSMatchNo IN (".implode(',',array_keys($MatchArray)).") AND FSTournament=" . StrSafe_DB($_SESSION['TourId']);
        $q2 = safe_r_SQL($Sql);
        while($r2=safe_fetch($q2)) {
            $MatchSchedule[$r2->FSMatchNo] = array($r2->ScheduledDate, $r2->ScheduledTime, $r2->FSScheduledLen, $r2->FSTarget);
        }
        $Rows=array();
        foreach ($MatchArray as $kMatch => $vMatch) {
            if(!array_key_exists($kMatch,$MatchSchedule)) {
                $MatchSchedule[$kMatch] = array('','','','');
            }
            echo '<tr>
                <td>'.$vMatch.'</td>
                <td>
                    <input type="text" tabindex="'.($tabIndex++).'" maxlength="10" size="10" name="d_FSScheduledDate_'.$r->EvCode.'_'.$kMatch.'" id="d_FSScheduledDate_'.$r->EvCode.'_'.$kMatch.'" value="'.$MatchSchedule[$kMatch][0].'" onblur="WriteSchedule(this);" class="">
                    @
                    <input type="text" tabindex="'.($tabIndex++).'" maxlength="5" size="4" name="d_FSScheduledTime_'.$r->EvCode.'_'.$kMatch.'" id="d_FSScheduledTime_'.$r->EvCode.'_'.$kMatch.'" value="'.$MatchSchedule[$kMatch][1].'" onblur="WriteSchedule(this);">
                    &nbsp;/&nbsp;
                    <input type="text" tabindex="'.($tabIndex++).'" maxlength="3" size="2" name="d_FSScheduledLen_'.$r->EvCode.'_'.$kMatch.'" id="d_FSScheduledLen_'.$r->EvCode.'_'.$kMatch.'" value="'.$MatchSchedule[$kMatch][2].'" onblur="WriteSchedule(this);">
                </td>
                
                <td>
                    <input type="text" tabindex="'.($tabIndex++).'" maxlength="7" size="3" name="d_FSTarget_'.$r->EvCode.'_'.$kMatch.'_1" id="d_FSTarget_'.$r->EvCode.'_'.$kMatch.'_1" value="'.$MatchSchedule[$kMatch][3].'" onblur="WriteTarget(\'d_FSTarget_'.$r->EvCode.'_'.$kMatch.'_1\');" class="">                   
                </td>
                </tr>';
        }
    }
    print '</table>';
    //print_r();
}

if(count($in)) {
    // target assignment for "old style" 1st/2nd elimination rounds
    $Select = "SELECT
        ElElimPhase,ElEventCode,EvEventName,ElQualRank,ElTournament,ElTargetNo AS TargetNo,ElSession,CoCode, CoName,
        EnCode,EnName,EnFirstName,EnDivision,EnClass,EnCountry
        FROM Eliminations
        INNER JOIN Events ON ElEventCode=EvCode AND ElTournament=EvTournament AND EvTeamEvent=0
        LEFT JOIN Entries ON ElId=EnId AND ElTournament=EnTournament
        LEFT JOIN Countries ON EnCountry=CoId AND ElTournament=CoTournament
        WHERE ElTournament=" . StrSafe_DB($_SESSION['TourId']) . "  " . $EventsFilter . " 
        ORDER BY ElElimPhase ASC, ElEventCode ASC,ElQualRank ASC ";

    $Rs = safe_r_sql($Select);

    $curEvent = '';

    if (safe_num_rows($Rs) > 0) {
        while ($MyRow = safe_fetch($Rs)) {
            if ($curEvent != $MyRow->ElElimPhase . '_' . $MyRow->ElEventCode) {
                if ($curEvent != '') {
                    print '</table>';
                }
                print '<table class="Tabella">' ;
                print '<tr>';
                print '<th class="Title "colspan="6">' . get_text('Eliminations_' . ($MyRow->ElElimPhase + 1)) . " - " . $MyRow->ElEventCode . '</th>';
                print '<th colspan="2">';
                print '<div align="left">';
                $id = $MyRow->ElElimPhase . '_' . $MyRow->ElEventCode;
                print get_text('Session') . '&nbsp;&nbsp;<select id="d_q_ElSession_' . $id . '" onChange="UpdateSession(this);">';
                print '<option value="0">---</option>';
                foreach ($sessions as $s) {
                    print '<option value="' . $s->SesOrder . '"' . ($s->SesOrder == $MyRow->ElSession ? ' selected' : '') . '>' . $s->Descr . '</option>';
                }
                print '</select>';
                print '</div>';
                print '</th>';
                print '</tr>';
                print '<tr>';
                //print '<th class="SubTitle" width="10%">' . get_text('Event') . '</a></th>';
                print '<th class="SubTitle" width="5%">' . get_text('Rank') . '</a></th>';
                print '<th class="SubTitle" width="10%">' . get_text('Target') . '</a></th>';
                print '<th class="SubTitle" width="5%">' . get_text('Code', 'Tournament') . '</a></th>';
                print '<th class="SubTitle" width="15%">' . get_text('Athlete') . '</a></th>';
                print '<th class="SubTitle" colspan="2" width="25%">' . get_text('Country') . '</a></th>';
                print '<th class="SubTitle" width="10%">' . get_text('Division') . '</a></th>';
                print '<th class="SubTitle" width="10%">' . get_text('Class') . '</a></th>';
                print '</tr>' . "\n";
            }

            $id = $MyRow->ElElimPhase . '_' . $MyRow->ElEventCode . '_' . $MyRow->ElQualRank;

            print '<tr>';

            /*	print '<td class="Center">';
                print get_text('Eliminations_' . ($MyRow->ElElimPhase+1) ) . " - " . $MyRow->ElEventCode;
                print '</td>';*/

            print '<td class="Center">';
            print $MyRow->ElQualRank;
            print '</td>';

            print '<td class="Center">';
            print '<input type="text" size="4"  name="d_q_ElTargetNo_' . $id . '" id="d_q_ElTargetNo_' . $id . '" value="' . $MyRow->TargetNo . '"' . ' onchange="UpdateTargetNo(this);">';
            //print $MyRow->TargetNo;
            print '</td>';

            print '<td class="Center">';
            print ($MyRow->EnCode != '' ? $MyRow->EnCode : '&nbsp;');
            print '</td>';

            print '<td>';
            print ($MyRow->EnFirstName . ' ' . $MyRow->EnName != ' ' ? $MyRow->EnFirstName . ' ' . $MyRow->EnName : '&nbsp;');
            print '</td>';

            print '<td class="Center" width="4%">';
            print ($MyRow->CoCode != '' ? $MyRow->CoCode : '&nbsp;');
            print '</td>';

            print '<td width="16%">';
            print ($MyRow->CoName != '' ? $MyRow->CoName : '&nbsp;');
            print '</td>';

            print '<td class="Center">';
            print (trim($MyRow->EnDivision) != '' ? $MyRow->EnDivision : '&nbsp;');
            print '</td>';

            print '<td class="Center">';
            print (trim($MyRow->EnClass) != '' ? $MyRow->EnClass : '&nbsp;');
            print '</td>';

            print '</tr>';

            $curEvent = $MyRow->ElElimPhase . '_' . $MyRow->ElEventCode;
        }
    }
    print '</table>';
}

?>
<div id="idOutput"></div>
<script type="text/javascript">FindRedTarget();</script>
<?php
	include('Common/Templates/tail.php');
?>
