<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);
checkACL(AclEliminations, AclReadOnly);
require_once('Common/Fun_FormatText.inc.php');

$PAGE_TITLE=get_text('PrintList', 'Tournament');

$JS_SCRIPT=array();

include('Common/Templates/head.php');

echo '<table class="Tabella">'.
    '<tr><th class="Title" colspan="2">' . get_text('PrintList','Tournament')  . '</th></tr>';

$Sql = "SELECT EvCode, EvEventName, EvNumQualified, EvShootOff, EvE1ShootOff, EvE2ShootOff, EvElimType, EvElim1, EvElim2, EvFinalFirstPhase, EvFirstQualified 
	FROM Events
	WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0 AND EvElimType!=0 AND EvCodeParent='' and if(EvElimType>=3, EvShootOff>0, true)
	ORDER BY EvElimType = 4 DESC, EvElimType = 3 DESC, EvElimType <= 2 DESC, EvProgr ASC ";
$q=safe_r_SQL($Sql);

$EventList = array();
while ($r=safe_fetch($q)) {
    $EventList[$r->EvElimType == 1 ? 2 : $r->EvElimType ][] = $r;
}

// Pools
foreach($EventList as $ElimType => $Rows) {
	if($ElimType>=3) {
		// new Pool system
		echo '<tr class="Divider"><td colspan="2"></td></tr>';
		echo '<tr><th colspan="2">' . get_text(($ElimType == 4 ? 'WA_Pool4' : 'WG_Pool2')) . '</th></tr>';
		//List of Events
		echo '<tr><td width="50%" style="vertical-align: middle;"><div style="display: grid; grid-template-columns: 1fr 1fr;">';
		foreach ($Rows as $k => $r) {
			if(!$r->EvShootOff) {
				continue;
			}
			echo '<div style="padding-bottom: 2vh; padding-top: 1vh; text-align: center;">' .
				'<a class="Link" target="ORISPrintOut" href="OrisPoolIndividual.php?isPool='.$ElimType.'&EventCode=' . $r->EvCode . '"><img src="' . $CFG->ROOT_DIR . 'Common/Images/pdfOris.gif" alt="' . $r->EvEventName . '" border="0"></a><br>' .
				'<a class="Link" target="ORISPrintOut" href="OrisPoolIndividual.php?isPool='.$ElimType.'&EventCode=' . $r->EvCode . '">' . $r->EvCode . ' - ' . $r->EvEventName . '</a>' .
				'</div>';
		}
		echo '</div></td>';

		//Event Selector
		echo '<td width="50%" style="vertical-align: middle;"><div style="display: grid; grid-template-columns: 1fr 1fr;">';
		echo '<div style="text-align: center; align-self: center;">' .
			'<a class="Link" target="ORISPrintOut" href="OrisPoolIndividual.php?isPool='.$ElimType.'"><img src="' . $CFG->ROOT_DIR . 'Common/Images/pdfOris.gif" alt="' . get_text(($ElimType == 4 ? 'WA_Pool4' : 'WG_Pool2'))  . '" border="0"></a><br>' .
			'<a class="Link" target="ORISPrintOut" href="OrisPoolIndividual.php?isPool='.$ElimType.'">' . get_text(($ElimType == 4 ? 'WA_Pool4' : 'WG_Pool2')) . '</a>' .
			'</div>';
		echo '<div  style="padding: 5px; text-align: center; "><form action="OrisPoolIndividual.php" target="ORISPrintOut"><input type="hidden" value="'.$ElimType.'" name="isPool"><select id="Event" name="EventCode[]" multiple="multiple" size="' . (count($Rows) + 1) . '">';
		foreach ($Rows as $k => $r) {
			if(!$r->EvShootOff) {
				continue;
			}
			echo '<option value="' . $r->EvCode . '">' . $r->EvCode . ' - ' . $r->EvEventName . '</option>';
		}
		echo '</select><br><input style="margin-top: 2vh;" id="ShowStartList" name="doStartlist" type="checkbox" value="1">&nbsp;' . get_text('StartlistSession','Tournament') . '
			<br><input type="submit" name="Button" value="' . get_text('Print', 'Tournament') . '"></form></div>';
		echo '</div></td></tr>';
	} else {
		// old Elimination systems
		echo '<tr class="Divider"><td colspan="2"></td></tr>';
		echo '<tr><th colspan="2">' . get_text('Elimination') . '</th></tr>';
		//List of Events
		echo '<tr><td width="50%" style="vertical-align: middle;"><div style="display: grid; grid-template-columns: 1fr 1fr;">';
		foreach ($Rows as $k => $r) {
			echo '<div style="padding-bottom: 2vh; padding-top: 1vh; text-align: center;">' .
				'<a class="Link" target="PrintOut" href="PrnIndividual.php?EventCode=' . $r->EvCode . '"><img src="' . $CFG->ROOT_DIR . 'Common/Images/pdf.gif" alt="' . $r->EvEventName . '" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .
				'<a class="Link" target="ORISPrintOut" href="OrisIndividual.php?EventCode=' . $r->EvCode . '"><img src="' . $CFG->ROOT_DIR . 'Common/Images/pdfOris.gif" alt="' . $r->EvEventName . '" border="0"></a><br>' .
				'<a class="Link" target="'.($_SESSION['ISORIS'] ? 'ORIS' : '').'PrintOut" href="'.($_SESSION['ISORIS'] ? 'OrisIndividual.php' : 'PrnIndividual.php').'?EventCode=' . $r->EvCode . '">' . $r->EvCode . ' - ' . $r->EvEventName . '</a>' .
				'</div>';
		}
		echo '</div></td>';
		//Event Selector
		echo '<td width="50%" style="vertical-align: middle;"><form action="PrnIndividual.php" target="'.($_SESSION['ISORIS'] ? 'ORIS' : '').'PrintOut"><div style="display: grid; grid-template-columns: 1fr 1fr;">';
		echo '<div style="text-align: center; align-self: center;">'.
			'<input id="ShowOrisIndividual" name="isORIS" type="checkbox" value="1"'.($_SESSION['ISORIS'] ? ' checked="checked"' : '').'>&nbsp;' . get_text('StdORIS','Tournament') . '<br>'.
			'<input id="ShowStartList" name="doStartlist" type="checkbox" value="1">&nbsp;' . get_text('StartlistSession','Tournament') . '<br>'.
			'</div>';
		echo '<div  style="padding: 5px; text-align: center; "><select id="Event" name="EventCode[]" multiple="multiple" size="' . (count($Rows) + 1) . '">';
		foreach ($Rows as $k => $r) {
			echo '<option value="' . $r->EvCode . '">' . $r->EvCode . ' - ' . $r->EvEventName . '</option>';
		}
		echo '</select><br><input style="margin-top: 2vh;" type="submit" name="Button" value="' . get_text('Print', 'Tournament') . '"></div>';
		echo '</div></form></td></tr>';
	}
}

echo '</table>';

include('Common/Templates/tail.php');
