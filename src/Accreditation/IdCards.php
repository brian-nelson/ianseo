<?php

/*

nella stampa scegliere con questi filtri (in AND):
- società
- categoria
- con foto
- accreditato
- non stampato (e mettere la possibilità di mettere il flag di "stampato" ad una certa selezione, ovviamente)
[10:04:24] Christian Deligant: categoria cosa intendi?
[10:04:24] Matteo Pisani: esatto
[10:04:32] Matteo Pisani: category, fita style
[10:04:36] Matteo Pisani: classe e divisione
[10:04:46] Matteo Pisani: oviamente anche nome...
[10:05:51] Matteo Pisani: che uno lla peggio mette sempre i "non stampati" e manda sempre solo i nuovi, + un bottone che dice: Ok, adesso questa selezione è stampata" (da premere solo DOPO aver visto la carta fuori dalla stampante
[10:06:00] Christian Deligant: bene... farei una cosa semplice del tipo...
combo box (multiplo) per nazioni, categorie e nomi; con checkbutton per foto accreditato e stampato



*/

require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Sessions.inc.php');

if(!empty($_REQUEST['DoPrint'])) {
	$FIELDS = "EnId";
	$SORT = 'EnId';

	require_once('CommonCard.php');

	$IDs=array();
	$q=safe_r_sql($MyQuery);
	while($r=safe_fetch($q)) $IDs[] = $r->EnId;

	sort($IDs);

	if($IDs) safe_w_sql("update Entries set EnBadgePrinted=now() where EnId in (".implode(',', $IDs).") ");
	cd_redirect($_SERVER['SCRIPT_NAME']);
}

$Badges=array(
	'Card.php'   => get_text('BadgeStandard', 'Tournament'),
	'Cardx6.php' => get_text('BadgeStandard6', 'Tournament'),
	'Cardx6-bis.php' => get_text('BadgeStandard6-bis', 'Tournament'),
	);

// select sessions
$SesNo=0;
$sessions=GetSessions('Q',true);

$JS_SCRIPT = array(
	'<script type="text/javascript" src="../Common/ext-2.2/adapter/ext/ext-base.js"></script>',
	'<script type="text/javascript" src="../Common/ext-2.2/ext-all-debug.js"></script>',
	'<script type="text/javascript" src="../Common/ext-2.2/ext.util/ext.util.js"></script>',
	'<script type="text/javascript" src="Fun_AJAX_IdCards.js"></script>',
	);
	$JS_SCRIPT[]='<script type="text/javascript">';
	$JS_SCRIPT[]='	var SesNo='.$SesNo.';';
	$JS_SCRIPT[]='	Ext.onReady';
	$JS_SCRIPT[]='	(';
	$JS_SCRIPT[]='		function()';
	$JS_SCRIPT[]='		{';
	$JS_SCRIPT[]='			Ext.get(\'d_Country\').on(\'change\',selectEntries);';
	$JS_SCRIPT[]='			Ext.get(\'d_Division\').on(\'change\',selectEntries);';
	$JS_SCRIPT[]='			Ext.get(\'d_Class\').on(\'change\',selectEntries);';
	for($i=1; $i<=$SesNo; $i++) {
		$JS_SCRIPT[]='			Ext.get(\'d_Session_'.$i.'\').on(\'change\',selectEntries);';
	}
	$JS_SCRIPT[]='		},';
	$JS_SCRIPT[]='		window';
	$JS_SCRIPT[]='	);';
	$JS_SCRIPT[]='</script>';

$PAGE_TITLE=get_text('BadgePrintout', 'Tournament');

include('Common/Templates/head.php');

echo '<form method="GET" target="Badges">';

echo '<table class="Tabella">'  . "\n";
echo '<tr><th class="Title" colspan="5">' . get_text('BadgePrintout','Tournament')  . '</th></tr>' . "\n";
echo '<tr>' . "\n";
echo '<th class="Title">'.get_text('BadgeOptions','Tournament').'</th>' . "\n";
echo '<th class="Title">'.get_text('Country').'</th>' . "\n";
echo '<th class="Title">'.get_text('Division').'</th>' . "\n";
echo '<th class="Title">'.get_text('Class').'</th>' . "\n";
echo '<th class="Title">'.get_text('BadgeNames','Tournament').'</th>' . "\n";
echo '</tr>' . "\n";

echo '<tr valign="top">' . "\n";

// Elenco opzioni
echo '<td nowrap="nowrap">';
	// tipo di badge
	echo '<div style="margin-bottom:1em"><b>'.get_text('BadgeType', 'Tournament').'</b>'."\n";
	foreach($Badges as $BadgePage=>$Badge) {
		echo '<br/><input type="radio" name="BadgeType" onclick="this.form.action=\''.$BadgePage.'\'; document.getElementById(\'print_button\').style.display=\'inline\';document.getElementById(\'confirm_button\').style.display=\'none\'">'.$Badge."\n";
		if($BadgePage=='Card.php') echo '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name="BadgePerPage">'
				. '<option value="4">'.get_text('Badge4PerPage', 'Tournament').'</option>'
				. '<option value="4B7">'.get_text('Badge4PerPageB7', 'Tournament').'</option>'
				. '<option value="2">'.get_text('Badge2PerPage', 'Tournament').'</option>'
				. '<option value="1">'.get_text('Badge1PerPage', 'Tournament').'</option>'
				. '</select>';
	}
	echo '</div>'."\n";

	echo '<div style="margin-bottom:1em"><b>'.get_text('BadgeSessions', 'Tournament').'</b>'."\n";
	foreach ($sessions as $s)
	{
		echo '<br/><input type="checkbox" id="d_Session_'.$s->SesOrder.'" name="Session[]" value="' . $s->SesOrder . '" onclick="hide_confirm(this.form)">Session ' . $s->Descr ."\n";
	}
	echo '</div>'."\n";

	// more options
	echo '<div style="margin-bottom:1em"><b>'.get_text('BadgeOptions', 'Tournament').'</b>'."\n";
	// badges devono includere la foto?
	echo '<br/><input type="checkbox" name="IncludePhoto" checked="checked" onclick="hide_confirm(this.form)">'.get_text('BadgeIncludePhoto', 'Tournament')."\n";
	// solo badges con foto?
	echo '<br/><input type="checkbox" name="PrintPhoto" checked="checked" onclick="hide_confirm(this.form)">'.get_text('BadgeOnlyPrintPhoto', 'Tournament')."\n";
	// solo accreditati?
	echo '<br/><input type="checkbox" name="PrintAccredited" checked="checked" onclick="hide_confirm(this.form)">'.get_text('BadgeOnlyPrintAccredited', 'Tournament')."\n";
	// solo i non stampati precedentemente?
	echo '<br/><input type="checkbox" name="PrintNotPrinted" checked="checked" onclick="hide_confirm(this.form)">'.get_text('BadgeOnlyNotPrinted', 'Tournament')."\n";
	echo '</div>';

echo '</td>';

// elenco Countries
echo '<td align="center"><select name="Country[]" id="d_Country" multiple="multiple" title="'.get_text('PressCtrl2SelectAll').'" onclick="hide_confirm(this.form)">';
$Sql = "SELECT distinct CoId, CoCode, CoName From Entries left join Countries on EnCountry=CoId WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " order by CoCode, CoName";
$Rs = safe_r_sql($Sql);
while($r=safe_fetch($Rs)) {
	echo '<option value="'.$r->CoId.'">'.$r->CoCode.'-'.substr($r->CoName, 0, 30).'</option>';
}
echo '</select></td>';

// elenco Divisions
echo '<td align="center"><select name="Division[]" id="d_Division" multiple="multiple" title="'.get_text('PressCtrl2SelectAll').'" onclick="hide_confirm(this.form)">';
$Sql = "SELECT distinct EnDivision From Entries inner join Divisions on EnDivision=DivId and EnTournament=DivTournament WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " order by DivViewOrder";
$Rs = safe_r_sql($Sql);
while($r=safe_fetch($Rs)) {
	echo '<option value="'.$r->EnDivision.'">'.$r->EnDivision.'</option>'."\n";
}
echo '</select></td>'."\n";

// elenco Classes
echo '<td align="center"><select name="Class[]" id="d_Class" multiple="multiple" title="'.get_text('PressCtrl2SelectAll').'" onclick="hide_confirm(this.form)">';
$Sql = "SELECT distinct EnClass From Entries inner join Classes on EnClass=ClId and EnTournament=ClTournament WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " order by ClViewOrder";
$Rs = safe_r_sql($Sql);
while($r=safe_fetch($Rs)) {
	echo '<option value="'.$r->EnClass.'">'.$r->EnClass.'</option>';
}
echo '</select></td>';

// elenco Entries
echo '<td align="center"><select name="Entries[]" id="p_Entries" multiple="multiple" title="'.get_text('PressCtrl2SelectAll').'" onclick="hide_confirm(this.form)">';
$Sql = "SELECT distinct EnId, EnDivision, EnClass, concat(EnFirstname, ' ', EnName) Name, (EnBadgePrinted is not null and EnBadgePrinted!='0000-00-00 00:00:00') Printed From Entries WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " order by Printed, Name";
$Rs = safe_r_sql($Sql);
while($r=safe_fetch($Rs)) {
	echo '<option value="'.$r->EnId.'"'.($r->Printed?' style="color:green"':' style="color:red"').'>'.$r->Name.' ('.$r->EnDivision.$r->EnClass.')</option>';
}
echo '</select></td>';

echo '</tr>'."\n";

echo '<tr><td colspan="5" align="center">'."\n";
echo '<input type="submit" style="display:none;margin-left:2em" id="confirm_button" name="DoPrint" title="'.get_text('BadgeConfirmPrintedDescr','Tournament').'" value="'.get_text('BadgeConfirmPrinted','Tournament').'" onclick="check_confirm(this.form)">'."\n";
echo '<input type="submit" style="display:none" id="print_button" value="'.get_text('Print','Tournament').'" onclick="activate_confirm(this.form)">'."\n";
echo '</td></tr>'."\n";
echo '</table></form>'."\n";

echo '<script>
function activate_confirm(form) {
	form.target=\'Badges\';
	document.getElementById(\'confirm_button\').style.display=\'inline\';
}

function hide_confirm(form) {
	document.getElementById(\'confirm_button\').style.display=\'none\';
}

function check_confirm(form) {
	form.target=\'\';
	form.action=\'\'
}

</script>';

include('Common/Templates/tail.php');
?>