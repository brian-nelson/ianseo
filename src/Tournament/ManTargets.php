<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

checkACL(AclCompetition, AclReadWrite);
CheckTourSession(true); // will print the crack error string if not inside a tournament!

require_once('Common/Lib/CommonLib.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Partecipants/Fun_Targets.php');



$Advanced = (ProgramRelease!='FITARCO' AND ProgramRelease!='STABLE');

$numDist=0;
$colspan=0;
$rsDist='';


$AvDiv=array();
$q=safe_r_sql("select DivId, ClId from Divisions inner join Classes on ClTournament=DivTournament and ClAthlete=DivAthlete where DivTournament='{$_SESSION['TourId']}' and DivAthlete=1 AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed)) order by DivViewOrder, ClViewOrder");
while($r=safe_fetch($q)) {
    $AvDiv[$r->DivId][$r->ClId]='<a name="" onclick="document.getElementById(\'TdClasses\').value=\''.$r->DivId.$r->ClId.'\'">'.$r->DivId.$r->ClId.'</a>';
}

$AvTargets=array();
$SelTargets='<option value="">---</option>';
$q=safe_r_sql("select * from Targets order by TarOrder");
while($r=safe_fetch($q)) {
    $AvTargets[$r->TarId]= get_text($r->TarDescr);
    $SelTargets.='<option value="'.$r->TarId.'">'.get_text($r->TarDescr).'</option>';
}

$select = "SELECT ToType,ToNumDist AS TtNumDist
    FROM Tournament
    WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
$rs=safe_r_sql($select);

if ($r=safe_fetch($rs) and $r->TtNumDist) {
    $numDist=$r->TtNumDist;
    $colspan=2+$numDist+$Advanced;

    $select = "SELECT DISTINCT *
        FROM TargetFaces
        WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
    $rsDist=safe_r_sql($select);
}

foreach(($DefinedTargets=getTargets(false)) as $Target=>$divs) {
    foreach($divs as $Div=>$cl) {
        foreach($cl as $Class=>$default) {
            if($default) unset ($AvDiv[$Div][$Class]);
        }
    }
}

$JS_SCRIPT = array(
	'<link href="'.$CFG->ROOT_DIR.'Common/css/font-awesome.css" rel="stylesheet" type="text/css">',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
    '<script type="text/javascript" src="Fun_AJAX_ManTargets.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
    phpVars2js(array(
    	'StrConfirm' =>get_text('MsgAreYouSure'),
    	'CannotDelete' =>get_text('CannotDelete','Tournament'),
    	'numDist' => $numDist,
        )),
    );
$PAGE_TITLE=get_text('MenuLM_Targets');

include('Common/Templates/head.php');

echo '<table class="Tabella">';
echo '<tr><th class="Title" colspan="'.($colspan+3).'">'.get_text('MenuLM_Targets').'</th></tr>';
echo '<tr>';
echo '<th>'.get_text('AvailableValues','Tournament').'<br/>'.get_text('BoldIsDefault','Tournament').'</th>';
echo '<th>'.get_text('Name','Tournament').'</th>';
echo '<th>'.get_text('FilterOnDivCl','Tournament').'</th>';
echo '<th>'.get_text('FilterOnDivClAdv','Tournament').'</th>';
for ($i=1;$i<=$numDist;++$i) {
    echo '<th>.'.$i.'.</th>';
}
echo '<th><?php '.get_text('TVSetAsDefault','Tournament').'</th>';
echo '<th></th>';
echo '</tr>';

// Insert row
echo '<tr id="edit">';
echo '<td class="Center" id="categories">';
foreach($AvDiv as $Div=>$Cl) {
    if($Cl) echo implode(', ',$Cl).'<br/>';
}
echo '</td>';
echo '<td class="Center"><input type="text" id="TdName" size="15" maxlength="15" value=""></td>';
echo '<td class="Center"><input type="text" id="TdClasses" size="12" maxlength="10" value=""></td>';
echo '<td class="Center">'.($Advanced ? '<input type="text" id="TdRegExp" size="16" value="">' : '&nbsp;').'</td>';
for ($i=1;$i<=$numDist;++$i) {
    echo '<td class="Center"><select id="TdFace'.$i.'">'.$SelTargets.'</select><br/>ø (cm) <input type="text" id="TdDiam'.$i.'" size="3" maxlength="3" value=""></td>';
}
echo '<td class="Center"><input type="checkbox" id="TdDefault"></td>';
echo '<td class="Center">
        <input type="button" name="command" value="'.get_text('CmdOk').'" onclick="saveTarget();">&nbsp;&nbsp;
        <input type="button" name="command" value="'.get_text('CmdCancel').'" onclick="resetTarget();">
    </td>';
echo '</tr>';
echo '<tr class="Spacer"><td colspan="'.($colspan+3).'"></td></tr>';

// target faces already set
echo '<tbody id="tbody"></tbody>';
//if ($rsDist) {
//    $k=0;
//    while ($myRow=safe_fetch($rsDist)) {
//        echo '<tr id="row_'.$k.'" ref="'.$myRow->TfId.'">';
//        echo '<td>'.print_targets($myRow->TfId).'</td>';
//        echo '<td class="Center">'.get_text($myRow->TfName,'Tournament','',true).'</td>';
//        echo '<td class="Center" style="width:20%;"><div id="cl_'.$k.'">'.$myRow->TfClasses.'</div></td>';
//        echo '<td class="Center" style="width:20%;"><div id="reg_'.$k.'">'.$myRow->TfRegExp.'</div></td>';
//        for ($i=1;$i<=$numDist;++$i) {
//            echo '<td class="Center"><div id="td_' . $k . '_' . $i . '">' . ($myRow->{'TfT' . $i} ? $AvTargets[$myRow->{'TfT' . $i}] . '<br/> '.$myRow->{'TfW' . $i}.' cm' : '') . '</div></td>';
//        }
//        echo '<td class="Center">'.($myRow->TfDefault?get_text('Yes'):'').'</td>';
//        echo '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" border="0" alt="#" title="#" onclick="deleteRow('.$k.', '.$myRow->TfId.');"></td>';
//        echo '</tr>';
//        ++$k;
//    }
//}
//echo '</tbody>';
echo '</table>';
echo '<div id="idOutput"></div>';

include('Common/Templates/tail.php');

function print_targets($TfId) {
	global $DefinedTargets;
	$ret='';
	if(empty($DefinedTargets[$TfId])) return '&nbsp;';
	foreach($DefinedTargets[$TfId] as $Div=>$Cl) {
		$ret.= $Div . ':&nbsp;';
		foreach($Cl as $class=>$def) {
			if($def) $class="<b>$class</b>";
			$ret.= $class . '&nbsp;';
		}
		$ret.='<br/>';
	}
	return $ret;
}
?>
