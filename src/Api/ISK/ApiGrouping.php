<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

CheckTourSession(true);
checkACL(AclISKServer, AclReadWrite);

require_once('Common/Lib/CommonLib.php');

$Session=(empty($_REQUEST['Session']) ? '0' : $_REQUEST['Session']);
$SesType=(empty($_REQUEST['SesType']) ? 'Q' : $_REQUEST['SesType']);
$ElPhase=(empty($_REQUEST['ElPhase']) ? '0' : $_REQUEST['ElPhase']);
$SesRow='';
$tgt=$Session.'%03s';

$PAGE_TITLE=get_text('API-TargetGrouping', 'Api');
$JS_SCRIPT=array(
		phpVars2js(array('ConfirmDeleteRow'=> get_text('API-ConfirmDeleteRow', 'Api'))),
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
	'<script type="text/javascript" src="ApiGrouping.js"></script>',
	);

require_once('../lib.php');

include('Common/Templates/head.php');

echo '<select name="Session" onchange="if(this.value) {window.location.href=this.value;}">';

// fetch the Qualification sessions
echo '<option value="">==> '.get_text('QualRound').'</option>';

$q=safe_r_sql(getSesSQL('Q'));
while($r=safe_fetch($q)) {
	echo '<option value="?SesType=Q&Session='.$r->SesOrder.'"'.($SesType=='Q' && $r->SesOrder==$Session ? ' selected="selected"' : '').'>'.($r->SesName ? $r->SesName : get_text('Session').' '.$r->SesOrder).'</option>';
	if($r->SesOrder==$Session and $SesType=='Q') {
		$SesRow=$r;
		$SesRow->Range=Range($r->SesFirstTarget, $r->SesTar4Session+$r->SesFirstTarget-1);
	}
}

// If Eliminations
if($_SESSION['MenuElimDo']) {
	$OldPhase='';
	$SQL=getSesSQL('E');
	$q=safe_r_SQL($SQL);
	while($r=safe_fetch($q)) {
		if($OldPhase!=$r->Phase) {
			echo '<option value="">==> '.get_text('Eliminations_'.$r->Phase).'</option>';
			$OldPhase=$r->Phase;
		}
		echo '<option value="?SesType=E&ElPhase='.$r->Phase.'&Session='.$r->SesOrder.'"'.($SesType=='E' && $ElPhase==$r->Phase && $r->SesOrder==$Session ? ' selected="selected"' : '').'>'.($r->SesName ? $r->SesName : get_text('Session').' '.$r->SesOrder).'</option>';
		if($r->SesOrder==$Session and $SesType=='E' and $ElPhase==$r->Phase) {
			$SesRow=$r;
			$SesRow->Range=explode(',', $r->SesTar4Session);
		}
	}
}

$Sessions=array();
if($_SESSION['MenuFinIDo']) $Sessions['I']=true;
if($_SESSION['MenuFinTDo']) $Sessions['T']=true;

echo '</select>';

if($SesRow) {
	echo '<table id="Groups" class="Tabella">';
	echo '<tr>
		<th class="Title" rowspan="2" colspan="2">'.get_text('API-Group', 'Api').'</th>
		<th class="Title" colspan="'.count($SesRow->Range).'">'.get_text('API-Targets', 'Api').'</th>
		</tr>';
	echo '<tr>';
	foreach($SesRow->Range as $Target) echo '<th class="Title">'.($Target).'</th>';
	echo '</tr>';

	echo buildGroups($SesType, $Session, $ElPhase, $SesRow->Range);

	echo '<tr>';
	echo '<th><img title="'.get_text('CmdAdd', 'Tournament').'" alt="add" src="'.$CFG->ROOT_DIR.'Common/Images/Enabled1.png" height="20" alt="add" onclick="AddGroup(this)"></th>';
	echo '<th><input type="text" id="GrName"></th>';
	foreach($SesRow->Range as $Target) {
		$tgtno=sprintf($tgt, $Target);
		echo '<td><input type="radio" onclick="UpdateGroup(this, \'delete\')" name="tgt['.$SesType.']['.$ElPhase.']['.$tgtno.']" value="***Group***"></td>';
	}
	echo '</tr>';
	echo '</table>';
}

include('Common/Templates/tail.php');