<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');

checkACL(AclCompetition, AclReadWrite);
CheckTourSession(true); // will print the crack error string if not inside a tournament!

$numDist=0;
$tourType=0;
$colspan=0;

$rsDist=null;

$AvDiv=array();
$q=safe_r_sql("select DivId, ClId from Divisions inner join Classes on ClTournament=DivTournament and ClAthlete=DivAthlete where DivTournament='{$_SESSION['TourId']}' and DivAthlete=1 AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed)) order by DivViewOrder, ClViewOrder");
while($r=safe_fetch($q)) {
    $AvDiv[$r->DivId][$r->ClId]='<a name="" onclick="document.getElementById(\'TdClasses\').value=\''.$r->DivId.$r->ClId.'\'">'.$r->DivId.$r->ClId.'</a>';
}

$select
    = "SELECT ToType,ToNumDist AS TtNumDist "
    . "FROM Tournament "
    . "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
//print $select;exit;
$rs=safe_r_sql($select);

if (safe_num_rows($rs)==1) {
    $r=safe_fetch($rs);
    $tourType=$r->ToType;
    $numDist=$r->TtNumDist;
}

if ($tourType*$numDist!=0) {
    $colspan=2+$numDist;

    $select
        = "SELECT DISTINCT t.* "
        . "FROM Divisions INNER JOIN Classes ON DivTournament=ClTournament AND DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
        . "INNER JOIN TournamentDistances AS t ON TdType=" . $tourType . " and TdTournament=DivTournament AND CONCAT(TRIM(DivId),TRIM(ClId)) LIKE TdClasses "
        . "WHERE DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";

    $rsDist=safe_r_sql($select);
}

foreach(($DefinedDistances=getDistances(false)) as $Dist=>$divs) {
    foreach($divs as $Div=>$cl) {
        foreach($cl as $Class=>$default) {
            unset ($AvDiv[$Div][$Class]);
        }
    }
}

$JS_SCRIPT = array(
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Tournament/Fun_AJAX_ManDistances.js"></script>',
    '<script type="text/javascript">var StrConfirm="' . get_text('MsgAreYouSure') . '";</script>',
    );
$PAGE_TITLE=get_text('ManDistances','Tournament');

include('Common/Templates/head.php');

echo '<div style="margin:auto">';
echo '<table class="Tabella freeWidth">';
echo '<tbody id="tbody">';
echo '<tr><th class="Title" colspan="'.($colspan+1).'">'.(get_text('ManDistances','Tournament')).'</th></tr>';
echo '<tr>
    <th>'.(get_text('AvailableValues','Tournament')).'</th>
    <th>'.(get_text('FilterOnDivCl','Tournament')).'</th>';
for ($i=1;$i<=$numDist;++$i) {
    echo '<th>.'.($i).'.</th>';
}
echo '<th>&nbsp;</th>
    </tr>';

echo '<tr id="edit">';
echo '<td class="Center">
    <input type="hidden" id="type" value="'.($tourType).'">
    <div id="avb">';
foreach($AvDiv as $Div=>$Cl) {
    if($Cl) echo implode(', ',$Cl).'<br/>';
}
echo '</div>
    </td>';
echo '<td class="Center"><input type="text" id="TdClasses" size="12" maxlength="10" value=""></td>';

for ($i=1;$i<=$numDist;++$i) {
    echo '<td class="Center"><input type="text" id="Td'.($i).'" size="12" maxlength="10" value=""></td>';
}
echo '<td class="Center">
    <input type="button" name="command" value="'.(get_text('CmdOk')).'" onclick="save('.($numDist).');">&nbsp;&nbsp;
    <input type="button" name="command" value="'.(get_text('CmdCancel')).'" onclick="resetInput('.($numDist).')">
    </td>
    </tr>
    <tr class="Spacer"><td colspan="'.($colspan+1).'"></td></tr>';
$ToSave=array();
if ($rsDist!=null) {
    $k=0;
    while ($myRow=safe_fetch($rsDist)) {
        echo '<tr id="row_'.$k.'" ref="'.$myRow->TdClasses.'">
            <td>'.(array_key_exists($myRow->TdClasses, $DefinedDistances) ? print_distances($DefinedDistances[$myRow->TdClasses]) : '&nbsp;') . '</td>
            <td class="Center" style="width:20%;"><div id="cl_'.$k.'">'.$myRow->TdClasses.'</div></td>';
        foreach(range(1, $numDist) as $i) {
            echo '<td class="Center"><input ref="'.$i.'" value="'.$myRow->{'Td' . $i}.'" onchange="updateDistance(this)" size="12" maxlength="10"></td>';
        }
        echo '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" border="0" alt="#" title="#" onclick="deleteRow('.$k.', \''.$myRow->TdClasses.'\', '.$tourType.');"></td></tr>';
        ++$k;
        $ToSave[]=StrSafe_DB($myRow->TdClasses);
    }
}

// removes the unfit matches...
if($ToSave) {
    safe_w_sql("delete from TournamentDistances where TdType=" . $tourType . " and TdTournament={$_SESSION['TourId']} AND TdClasses not in (".implode(',', $ToSave).")");
} else {
    safe_w_sql("delete from TournamentDistances where TdType=" . $tourType . " and TdTournament={$_SESSION['TourId']}");
}

echo '</tbody>
    </table>
    <br/>';

// DISTANCE INFORMATION MANAGEMENT
// Based on SESSIONS!!!!
require_once('./ManDistancesSessions.php');

echo '</div>
    <div id="idOutput"></div>';

include('Common/Templates/tail.php');

function getDistances($ByDist=true) {
	$ar=array();

	$MySql="select DivId, ClId, TdClasses
		from Divisions
		inner join Classes on DivTournament=ClTournament and DivAthlete=ClAthlete
		inner join TournamentDistances on DivTournament=TdTournament and concat(trim(DivId),trim(ClId)) like TdClasses
		WHERE
			DivTournament={$_SESSION['TourId']}
			AND DivAthlete='1'
			AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed))
		".($ByDist ? "and TdClasses='$ByDist'" : '')."
		order by
			DivViewOrder, ClViewOrder";

	$q=safe_r_sql($MySql);
	if($ByDist) {
		while($r=safe_fetch($q)) {
			$ar[]=$r->DivId.$r->ClId;
		}
	} else {
		while($r=safe_fetch($q)) {
			$ar[$r->TdClasses][$r->DivId][$r->ClId] = '1';
		}
	}

	return $ar;
}

function print_distances($DefinedDistances) {
	$ret='';
	foreach($DefinedDistances as $Div=>$Cl) {
		$ret.= $Div . ':&nbsp;';
		foreach($Cl as $class=>$def) {
			if($def) $class="<b>$class</b>";
			$ret.= $class . '&nbsp;';
		}
		$ret.='<br/>';
	}
	return $ret;
}
