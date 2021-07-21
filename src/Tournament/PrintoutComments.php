<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/CommonLib.php');
	require_once('Common/Fun_Sessions.inc.php');

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
	checkACL(array(AclQualification,AclEliminations,AclIndividuals,AclTeams),AclReadWrite);

//Data handler
if(!empty($_REQUEST['action']) and preg_match("/^(list|set|bulk|session)$/i",$_REQUEST['action'])) {
    $JSON=array('error'=>1, 'data'=>array());
    if(strtolower($_REQUEST['action']) == 'session' AND !empty($_REQUEST['key'])) {
        if($_REQUEST['key'][0]=='Q') {
            $q = safe_r_SQL("SELECT DISTINCT EcCode, EcTeamEvent
                from Entries
                INNER JOIN Qualifications on EnId=QuId
                INNER JOIN EventClass On EnTournament=EcTournament AND EcClass=EnClass AND EcDivision=EnDivision
                WHERE EnTournament={$_SESSION['TourId']} AND QuSession=".intval(substr($_REQUEST['key'],-1)));
            while($r=safe_fetch($q)) {
                $JSON['data'][] = 'chkEv_'.$r->EcCode.'_'.$r->EcTeamEvent;
            }
            $JSON['error']=0;
        }
        if($_REQUEST['key'][0]=='I' OR $_REQUEST['key'][0]=='T') {
            $q = safe_r_SQL("SELECT DISTINCT FSEvent, FSTeamEvent
                from FinSchedule
                WHERE FsTournament={$_SESSION['TourId']} AND FSScheduledDate='".substr($_REQUEST['key'],1,10)."' AND FSScheduledTime='".substr($_REQUEST['key'],-8)."'");
            while($r=safe_fetch($q)) {
                $JSON['data'][] = 'chkEv_'.$r->FSEvent.'_'.$r->FSTeamEvent;
            }
            $JSON['error']=0;
        }
        JsonOut($JSON);
        die();
    }
    if (strtolower($_REQUEST['action']) == 'set' AND !empty($_REQUEST['key'])) {
        $offShortcut = (strpos($_REQUEST['value'],'||!')===0 ? 1 : (strpos($_REQUEST['value'],'||')===0 ? -1 : 0));
        $arrShortcut = ($offShortcut == 0  ? 0 : intval(substr($_REQUEST['value'],($offShortcut==1 ? 3:2))));
        $ev ='';
        $isTeam = 0;
        list($what, $ev, $isTeam) = explode('_', $_REQUEST['key']);
        $what = (substr($what,-1,1)=='Q' ? 'EvQualPrintHead' : 'EvFinalPrintHead');
        $value = StrSafe_DB($_REQUEST['value']);
        if($offShortcut != 0) {
            $value = "CONCAT('".($offShortcut==1 ? 'OFFICIAL':'Unofficial')." After ',(".$arrShortcut."*EvMaxTeamPerson),' Arrows')";
        }
        safe_w_SQL("UPDATE `Events` SET {$what}={$value} WHERE EvTournament={$_SESSION['TourId']} AND EvCode='{$ev}' AND EvTeamEvent={$isTeam}");
        if(safe_w_affected_rows()!=0) {
            $JSON['error'] = 0;
        }
    }
    if (strtolower($_REQUEST['action']) == 'bulk' AND !empty($_REQUEST['what']) AND !empty($_REQUEST['key'])) {
        $offShortcut = (strpos($_REQUEST['value'],'||!')===0 ? 1 : (strpos($_REQUEST['value'],'||')===0 ? -1 : 0));
        $arrShortcut = ($offShortcut == 0  ? 0 : intval(substr($_REQUEST['value'],($offShortcut==1 ? 3:2))));
        foreach (explode('|',$_REQUEST['key']) as $key) {
            $ev ='';
            $isTeam = 0;
            list($what, $ev, $isTeam) = explode('_', $key);
            $what = ($_REQUEST['what']=='Q' ? 'EvQualPrintHead' : 'EvFinalPrintHead');
            $value = StrSafe_DB($_REQUEST['value']);
            if($offShortcut != 0) {
                $value = "CONCAT('".($offShortcut==1 ? 'OFFICIAL':'Unofficial')." After ',(".$arrShortcut."*EvMaxTeamPerson),' Arrows')";
            }
            safe_w_SQL("UPDATE `Events` SET {$what}={$value} WHERE EvTournament={$_SESSION['TourId']} AND EvCode='{$ev}' AND EvTeamEvent={$isTeam}");
        }
        $JSON['error'] = 0;
    }

    $Sql = "SELECT EvCode, EvTeamEvent, EvQualPrintHead, EvFinalPrintHead " .
        "FROM Events " .
        "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCodeParent=''";
    $q=safe_r_SQL($Sql);
    while($r=safe_fetch($q)) {
        $JSON['data'][] = array('id'=>$r->EvCode.'_'.$r->EvTeamEvent, 'Q'=>$r->EvQualPrintHead, 'F'=>$r->EvFinalPrintHead, );
    }
    if(strtolower($_REQUEST['action']) == 'list') {
        $JSON['error'] = 0;
    }
    JsonOut($JSON);
    die();
}

$JS_SCRIPT = array( phpVars2js(array(
    'CmdCancel' => get_text('CmdCancel'),
    'CmdConfirm' => get_text('Confirm', 'Tournament'),
)),
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-confirm.min.js"></script>',
    '<script type="text/javascript" src="./PrintoutComments.js"></script>',
    '<link href="./PrintoutComments.css" rel="stylesheet" type="text/css">',
    '<link href="'.$CFG->ROOT_DIR.'Common/css/jquery-confirm.min.css" media="screen" rel="stylesheet" type="text/css">',
);
include('Common/Templates/head.php');
echo '<table class="Tabella">';
echo '<tr><th class="Title" colspan="5">'.get_text('PrintTextTitle','Tournament').'</th></tr>';
echo '<tr class="divider"><td colspan="5"></td></tr>';
echo '<tr>'.
    '<td colspan="3">'.get_text('PrintCommentTip', 'Tournament').'</td>'.
    '<td class="txtContainer"><input type="text" id="txtQ"><input type="button" value="'.get_text('CmdSave').'" onclick="bulkSave(\'Q\')"></td>'.
    '<td class="txtContainer"><input type="text" id="txtF"><input type="button" value="'.get_text('CmdSave').'" onclick="bulkSave(\'F\')"></td>'.
    '</tr>';
echo '<tr>'.
    '<th class="smallContainer"><input type="checkbox" id="chkBulk" onclick="chkBulkSelection()"></th>'.
    '<td colspan="4"><select id="cmbSessions"><option value="---">'.get_text('Select', 'Tournament').'</option>';
    foreach (getScheduledSessions() as $s) {
        echo '<option value="'.$s->keyValue.'">'.$s->Description.'</option>';
    }
echo '</select><input type="button" value="'.	get_text('SelectSession', 'Tournament').'" onclick="selectSession()"></td></tr>';
echo '<tr class="divider"><td colspan="5"></td></tr>';
$Sql = "SELECT EvCode, EvEventName, EvFinalFirstPhase, EvTeamEvent, EvQualPrintHead, EvFinalPrintHead " .
    "FROM Events " .
    "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCodeParent='' " .
    "ORDER BY EvTeamEvent ASC, EvProgr ASC, EvCode ";
$q=safe_r_SQL($Sql);
$isTeam=-1;
while ($r=safe_fetch($q)) {
    if($isTeam!=$r->EvTeamEvent) {
        echo '<tr><th colspan="3">'.get_text('Event').'</th><th>Header Qualification</th><th>Header Finals</th></tr>';
        $isTeam=$r->EvTeamEvent;
    }
    echo '<tr class="EventLine" id="ev_'.$r->EvCode.'_'.$r->EvTeamEvent.'">'.
        '<th class="smallContainer"><input type="checkbox" id="chkEv_'.$r->EvCode.'_'.$r->EvTeamEvent.'"></th>'.
        '<td class="evCodeContainer">'.$r->EvCode.'</td>'.
        '<td class="evContainer">'.$r->EvEventName.'</td>'.
        '<td class="txtContainer"><input type="text" id="txtQ_'.$r->EvCode.'_'.$r->EvTeamEvent.'" onchange="updateField(this)"></td>'.
        '<td class="txtContainer"><input type="text" id="txtF_'.$r->EvCode.'_'.$r->EvTeamEvent.'" onchange="updateField(this)"></td>'.
        '</tr>';

}

echo '</table>';
include('Common/Templates/tail.php');