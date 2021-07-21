<?php
require_once(__DIR__.'/config-ianseo.php');

CheckTourSession(true);
checkACL(AclISKServer, AclReadWrite);

require_once(__DIR__.'/Lib.php');
require_once('Common/Lib/Fun_Phases.inc.php');

$LockSessions=getModuleParameter('ISK', 'LockedSessions', array());

// gets all the qualification, Elimination, Individual and team matches
/* Fields of the key are:
IskDtMatchNo
IskDtEvent
IskDtTeamInd
IskDtType
IskDtTargetNo (just first digit as it is the session
IskDtDistance
*/

$SQL=GetLockableSessions();
$q=safe_r_sql($SQL);
$Sessions=array();
$Cols=0;
$SesCols=array('Q'=>0,'E'=>0,'I'=>0,'T'=>0);
$Headers=array();
while($r=safe_fetch($q)) {
	$Sessions[$r->SesType][$r->Description][$r->Distance]=$r;
	$Cols=max($Cols, count($Sessions[$r->SesType][$r->Description]));
	$SesCols[$r->SesType]=max($SesCols[$r->SesType], count($Sessions[$r->SesType][$r->Description]));
}

foreach($Sessions as $Type => $Events) {
	foreach($Events as $k => $v) {
		if(count($v)==$SesCols[$Type]) {
			// same number of columns...
			$Headers[$Type]=array_keys($v);
		}
	}
}

$IncludeFA = true;
$PAGE_TITLE=get_text('ManageLockedSessions', 'ISK');
$JS_SCRIPT=array(
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
	'<script type="text/javascript" src="./Sessions.js"></script>',
	'<link href="./Sessions.css" rel="stylesheet" type="text/css">',
);

require_once('Common/Templates/head.php');

echo '<table class="Tabella freeWidth">';
echo '<tr><th colspan="'.($Cols+2).'" class="Main">'.get_text('ManageLockedSessions', 'ISK').'</th></tr>';
echo '<tr class="divider"><td colspan="'.($Cols+2).'"></td></tr>';
echo '<tr><td colspan="'.($Cols+2).'" class="Left Legend"><i class="fa fa-2x fa-times-circle locked" style="vertical-align:-0.2em;margin-right:0.5em;" onclick="toggleLock(this)" ref="lockall"></i>'.get_text('LockAll', 'ISK').'</td></tr>'.
	'<tr><td colspan="'.($Cols+2).'" class="Left Legend"><i class="fa fa-2x fa-check-circle unlocked" style="vertical-align:-0.2em;margin-right:0.5em;" onclick="toggleLock(this)" ref="unlockall"></i>'.get_text('UnlockAll', 'ISK').'</td></tr>';

foreach($Sessions as $Type => $Events) {
	echo '<tr class="divider"><td colspan="'.($Cols+2).'"></td></tr>';
	$First=true;
	foreach($Events as $Event => $Items) {
		$colsEvent=count($Items);
		if($First) {
			echo '<tr><th class="Title NoWrap">'.get_text($Type.'-Session', 'Tournament').'</th>';
			foreach($Headers[$Type] as $k) {
				if($Type=='Q' or $Type=='E') {
					$tit=get_text('PopupStatusDistance', 'Api', $k);
				} else {
					$tit=get_text($k.'_Phase');
				}
				echo '<th class="Title" width="10%">'.$tit.'</th>';
			}
			// blank columns go after
			if($SesCols[$Type]<$Cols) {
				echo '<th class="Title" colspan="'.($Cols-$SesCols[$Type]).'"></th>';
			}
			echo '<th class="Title" style="padding:0.5rem;"><i class="fa fa-lg fa-file-pdf-o" onclick="window.open(\'PdfCheck.php\')"></i></th>';
			echo '</tr>';
		}
		echo '<tr><th class="NoWrap Right">'.$Event.'</th>';
		if($Type=='I' or $Type=='T') {
			// blank columns go first
			if($colsEvent<$SesCols[$Type]) {
				echo '<td colspan="'.($SesCols[$Type]-$colsEvent).'"></td>';
			}
		}
		foreach($Items as $k => $item) {
			$active= (in_array($item->LockKey, $LockSessions) ? 'fa-times-circle locked' : 'fa-check-circle unlocked');
			echo '<td class="Center"><i class="fa fa-2x '.$active.'" onclick="toggleLock(this)" ref="'.$item->LockKey.'"></i></td>';
		}
		// blank columns go after
		if($SesCols[$Type]<$Cols) {
			echo '<td colspan="'.($Cols-$SesCols[$Type]).'"></td>';
		}
		echo '<td class="Center"><i class="fa fa-2x fa-file-pdf-o" onclick="window.open(\'PdfCheck.php?ses='.$item->LockKey.'\')"></i></td>';
		echo '</tr>';
		$First=false;
	}
}
echo '<tr><td colspan="'.($Cols+2).'" class="Left Legend">'.get_text('ISK-LockedSessionHelp', 'Help', '<i class="fa fa-2x fa-check-circle unlocked" style="vertical-align:-0.2em; margin-right: 0.25em;></i>/<i class="fa fa-2x fa-times-circle locked" style="vertical-align:-0.2em; margin-right: 0.25em;"></i>').'</td></tr>';
echo '</table>';

require_once('Common/Templates/tail.php');
