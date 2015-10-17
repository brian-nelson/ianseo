<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('IdCardEmpty.php');

if(!empty($_FILES['ImportBackNumbers'])) {
	require_once('Common/CheckPictures.php');
	if($Layout=unserialize(gzuncompress(file_get_contents($_FILES['ImportBackNumbers']['tmp_name'])))) {
		safe_w_sql("delete from IdCards where IcTournament={$_SESSION['TourId']}");
		safe_w_sql("delete from IdCardElements where IceTournament={$_SESSION['TourId']}");
		$SQL=array("IcTournament={$_SESSION['TourId']}");
		foreach($Layout['IdCards'] as $f => $v) {
			if($f=='IcTournament') continue;
			$SQL[]=$f.'='.StrSafe_DB($v);
		}
		safe_w_sql("insert ignore into IdCards set ".implode(',', $SQL));

		foreach($Layout['IdCardElements'] as $Record => $Fields) {
			$SQL=array("IceTournament={$_SESSION['TourId']}");
			foreach($Fields as $f => $v) {
				if($f=='IceTournament') continue;
				$SQL[]=$f.'='.StrSafe_DB($v);
			}
			safe_w_sql("insert ignore into IdCardElements set ".implode(',', $SQL));
			CheckPictures();
		}
	}
}

if(!empty($_REQUEST['ExportLayout'])) {
	$Layout=array();
	$q=safe_r_SQL("select * from IdCards where IcTournament={$_SESSION['TourId']}");
	if($r=safe_fetch_assoc($q)) {
		$Layout['IdCards']=$r;

		$q=safe_r_SQL("select * from IdCardElements where IceTournament={$_SESSION['TourId']}");
		while($r=safe_fetch_assoc($q)) {
			$Layout['IdCardElements'][]=$r;
		}

		// We'll be outputting a gzipped TExt File in UTF-8 pretending it's binary
		header('Content-type: application/octet-stream');

		// It will be called ToCode-IdCard.ianseo
		header("Content-Disposition: attachment; filename=\"{$_SESSION['TourCode']}-IdCard.ianseo\"");

		ini_set('memory_limit',sprintf('%sM',512));

		echo gzcompress(serialize($Layout),9);
		die();
	}
}

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

$Badges=array();
$t=safe_r_sql("SELECT * FROM IdCards WHERE IcTournament=" . StrSafe_DB($_SESSION['TourId']));
if(safe_num_rows($t)) {
	$Badges['CardCustom.php']=get_text('BadgeCustom', 'BackNumbers');
}
$Badges['Card.php']=get_text('BadgeStandard', 'Tournament');
$Badges['Cardx6.php']=get_text('BadgeStandard6', 'Tournament');

// select sessions
$SesNo=0;
$sessions=GetSessions('Q',true);

$JS_SCRIPT = array(
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
	'<script type="text/javascript" src="Fun_AJAX_IdCards.js"></script>',
	);
	$JS_SCRIPT[]='<script type="text/javascript">';
	$JS_SCRIPT[]='	var SesNo='.$SesNo.';';
// 	$JS_SCRIPT[]='	Ext.onReady';
// 	$JS_SCRIPT[]='	(';
// 	$JS_SCRIPT[]='		function()';
// 	$JS_SCRIPT[]='		{';
// 	$JS_SCRIPT[]='			Ext.get(\'d_Country\').on(\'change\',selectEntries);';
// 	$JS_SCRIPT[]='			Ext.get(\'d_Division\').on(\'change\',selectEntries);';
// 	$JS_SCRIPT[]='			Ext.get(\'d_Class\').on(\'change\',selectEntries);';
// 	for($i=1; $i<=$SesNo; $i++) {
// 		$JS_SCRIPT[]='			Ext.get(\'d_Session_'.$i.'\').on(\'change\',selectEntries);';
// 	}
// 	$JS_SCRIPT[]='		},';
// 	$JS_SCRIPT[]='		window';
// 	$JS_SCRIPT[]='	);';
	$JS_SCRIPT[]='</script>';

$PAGE_TITLE=get_text('BadgePrintout', 'Tournament');

include('Common/Templates/head.php');

echo '<form method="POST" target="Badges" enctype="multipart/form-data">';

echo '<table class="Tabella">'  . "\n";
echo '<tr><th class="Title" colspan="2">' . get_text('BadgeSetup','BackNumbers')  . '</th></tr>' . "\n";

//Parametri
	echo '<tr>';
//Tipo di Score
	echo '<td width="50%"><br>';
	echo '<input name="BadgeDraw" type="radio" value="Complete" checked>&nbsp;' . get_text('BadgeComplete', 'BackNumbers') . '<br>';
	echo '<input name="BadgeDraw" type="radio" value="Test">&nbsp;' . get_text('BadgeTest', 'BackNumbers') . '<br><br>';

	// tipo di badge
	echo '<div style="margin-bottom:1em"><b>'.get_text('BadgeType', 'Tournament').'</b>'."\n";
	foreach($Badges as $BadgePage=>$Badge) {
		echo '<br/><input type="radio" name="BadgeType" onclick="this.form.action=\''.$BadgePage.'\'; document.getElementById(\'print_button\').style.display=\'inline\';document.getElementById(\'confirm_button\').style.display=\'none\'">'.$Badge."\n";
		if($BadgePage=='Card.php') {
			echo '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name="BadgePerPage">'
				. '<option value="4">'.get_text('Badge4PerPage', 'Tournament').'</option>'
				. '<option value="2">'.get_text('Badge2PerPage', 'Tournament').'</option>'
				. '<option value="1">'.get_text('Badge1PerPage', 'Tournament').'</option>'
				. '</select>';
		} elseif($BadgePage=='CardCustom.php') {
			$RowBn=emptyIdCard(safe_fetch($t));
			echo '<table align="center">
				<tr align="center">
					<th colspan="2">&nbsp;</th>
					<th>'.get_text('IdCardOffsets', 'BackNumbers') . '</th>
					<th>'.get_text('PaperDimention', 'BackNumbers') . '</th>
				</tr>
				<tr align="center">
					<th>'.get_text('Width', 'BackNumbers') . '</th>
					<td><input type="text" name="IdCardsSettings[Width]" id="IdWidth" size="3" value="' . $RowBn->Settings["Width"] . '"></td>
					<td><input type="text" name="IdCardsSettings[OffsetX]" id="IdRepX" size="10" value="' . $RowBn->Settings["OffsetX"] . '"></td>
					<td><input type="text" name="IdCardsSettings[PaperWidth]" id="IdPaperWidth" size="10" value="' . $RowBn->Settings["PaperWidth"] . '"></td>
				</tr>
				<tr align="center">
					<th>'.get_text('Heigh', 'BackNumbers') . '</th>
					<td><input type="text" name="IdCardsSettings[Height]" id="IdHeight" size="3" value="' . $RowBn->Settings["Height"] . '"></td>
					<td><input type="text" name="IdCardsSettings[OffsetY]" id="IdRepY" size="10" value="' . $RowBn->Settings["OffsetY"] . '"></td>
					<td><input type="text" name="IdCardsSettings[PaperHeight]" id="IdPaperHeight" size="10" value="' . $RowBn->Settings["PaperHeight"] . '"></td>
				</tr>
				</table>';
		}
	}
	echo '</div>'."\n";

	echo '</td>';
//Header e Immagini
// immagine fittizia del badge
	echo '<td width="50%" align="center"><br/>';
	if(safe_num_rows($t)) echo '<img src="ImgIdCard.php"><br/><br/>';
	echo '<input type="button" value="' . get_text('BadgeEdit', 'BackNumbers') . '" onClick="document.location=\''.$CFG->ROOT_DIR.'Accreditation/IdCardEdit.php\'">';
	echo '<br />';
	echo '<input type="submit" name="ExportLayout" value="' . get_text('BadgeExportLayout', 'BackNumbers') . '" onclick="this.form.target=\'\'; this.form.action=\''.basename(__FILE__).'\'">';
	echo '<br />';
	echo '<input type="file" name="ImportBackNumbers" />&nbsp;&nbsp;&nbsp;';
	echo '<input type="submit" name="ImportLayout" value="' . get_text('BadgeImportLayout', 'BackNumbers') . '" onclick="this.form.target=\'\'; this.form.action=\''.basename(__FILE__).'\'">';
	echo '</td>';
	echo '</tr>';
echo '</table>';

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

	echo '<div style="margin-bottom:1em"><b>'.get_text('BadgeSessions', 'Tournament').'</b>'."\n";
	foreach ($sessions as $s)
	{
		echo '<br/><input type="checkbox" onclick="ShowEntries()" id="d_Session_'.$s->SesOrder.'" name="Session[]" value="' . $s->SesOrder . '" onclick="hide_confirm(this.form)">Session ' . $s->Descr ."\n";
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
echo '<td align="center"><select onchange="ShowEntries()" name="Country[]" id="d_Country" multiple="multiple" title="'.get_text('PressCtrl2SelectAll').'" onclick="hide_confirm(this.form)" size="10">';
$Sql = "SELECT distinct CoId, CoCode, CoName From Entries left join Countries on EnCountry=CoId WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " order by CoCode, CoName";
$Rs = safe_r_sql($Sql);
while($r=safe_fetch($Rs)) {
	echo '<option value="'.$r->CoId.'">'.$r->CoCode.'-'.substr($r->CoName, 0, 30).'</option>';
}
echo '</select></td>';

// elenco Divisions
echo '<td align="center"><select onchange="ShowEntries()" name="Division[]" id="d_Division" multiple="multiple" title="'.get_text('PressCtrl2SelectAll').'" onclick="hide_confirm(this.form)" size="10">';
$Sql = "SELECT distinct EnDivision From Entries WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " order by EnDivision";
$Rs = safe_r_sql($Sql);
while($r=safe_fetch($Rs)) {
	echo '<option value="'.$r->EnDivision.'">'.$r->EnDivision.'</option>'."\n";
}
echo '</select></td>'."\n";

// elenco Classes
echo '<td align="center"><select onchange="ShowEntries()" name="Class[]" id="d_Class" multiple="multiple" title="'.get_text('PressCtrl2SelectAll').'" onclick="hide_confirm(this.form)" size="10">';
$Sql = "SELECT distinct EnClass From Entries WHERE EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " order by EnClass";
$Rs = safe_r_sql($Sql);
while($r=safe_fetch($Rs)) {
	echo '<option value="'.$r->EnClass.'">'.$r->EnClass.'</option>';
}
echo '</select></td>';

// elenco Entries
echo '<td align="center"><select name="Entries[]" id="p_Entries" multiple="multiple" title="'.get_text('PressCtrl2SelectAll').'" onclick="hide_confirm(this.form)" size="10">';
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