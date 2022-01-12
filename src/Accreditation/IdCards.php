<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('IdCardEmpty.php');

$CardType=(empty($_REQUEST['CardType']) ? 'A' : $_REQUEST['CardType']);
$lvl=0;
switch($CardType) {
	case 'A':
	    $lvl = checkACL(AclAccreditation, AclReadOnly);
		break;
	case 'Q':
	    $lvl = checkACL(AclQualification, AclReadOnly);
		break;
	case 'E':
	    $lvl = checkACL(AclEliminations, AclReadOnly);
		break;
	case 'I':
	    $lvl = checkACL(AclIndividuals, AclReadOnly);
		break;
	case 'T':
	    $lvl = checkACL(AclTeams, AclReadOnly);
		break;
	case 'Y':
	case 'Z':
	    $lvl = checkACL(AclCompetition, AclReadOnly);
		break;
}

// check card number
$CardNumber=0;
if(isset($_REQUEST['CardNumber'])) {
	$CardNumber=intval($_REQUEST['CardNumber']);
} else {
	$q=safe_r_sql("select * from IdCards where IcTournament={$_SESSION['TourId']} and IcType='$CardType' order by IcNumber");
	if($r=safe_fetch($q)) {
		$CardNumber=$r->IcNumber;
		cd_redirect(basename(__FILE__).go_get('CardNumber', $CardNumber));
	}
}


$GlobalLink="CardType={$CardType}&CardNumber={$CardNumber}";

$TourId=$_SESSION['TourId'];

if(!empty($_REQUEST['delete']) AND $lvl==AclReadWrite) {
	safe_w_sql("delete from IdCards where IcTournament=$TourId and IcType='$CardType' and IcNumber=$CardNumber");
	safe_w_sql("delete from IdCardElements where IceTournament=$TourId and IceCardType='$CardType' and IceCardNumber=$CardNumber");
	$imgs=glob($CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-'.$CardType.'-'.$CardNumber.'-*');
	foreach($imgs as $file) unlink($imgs);
	cd_redirect(basename(__FILE__).go_get('delete', '', true));
}

if(!empty($_FILES['ImportBackNumbers']['size']) AND $lvl==AclReadWrite) {
	require_once('Common/CheckPictures.php');
	if($Layout=unserialize(gzuncompress(file_get_contents($_FILES['ImportBackNumbers']['tmp_name'])))) {
		// before deleting gets the name of the badge
		$Name=get_text($CardType.'-Badge', 'BackNumbers');
		$q=safe_r_sql("select IcName from IdCards where IcTournament=$TourId and IcType='$CardType' and IcNumber=$CardNumber");
		if($r=safe_fetch($q)) $Name=$r->IcName;
		safe_w_sql("delete from IdCards where IcTournament=$TourId and IcType='$CardType' and IcNumber=$CardNumber");
		safe_w_sql("delete from IdCardElements where IceTournament=$TourId and IceCardType='$CardType' and IceCardNumber=$CardNumber");
		$SQL=array("IcTournament=$TourId");
		$SQL[]="IcType='$CardType'";
		$SQL[]="IcNumber=$CardNumber";
		$SQL[]="IcName='$Name'";
		foreach($Layout['IdCards'] as $f => $v) {
			if(in_array($f, array('IcTournament', 'IcType', 'IcName', 'IcNumber'))) continue;
			$SQL[]=$f.'='.StrSafe_DB($v);
		}
		safe_w_sql("insert ignore into IdCards set ".implode(',', $SQL));

		foreach($Layout['IdCardElements'] as $Record => $Fields) {
			$SQL=array("IceTournament=$TourId");
			$SQL[]="IceCardType='$CardType'";
			$SQL[]="IceCardNumber=$CardNumber";
			foreach($Fields as $f => $v) {
				if(in_array($f, array('IceTournament', 'IceCardType', 'IceCardNumber'))) continue;
				$SQL[]=$f.'='.StrSafe_DB($v);
			}
			safe_w_sql("insert ignore into IdCardElements set ".implode(',', $SQL));
			CheckPictures();
		}
	}
}

if(!empty($_REQUEST['ExportLayout'])) {
	$Layout=array();
	$q=safe_r_SQL("select * from IdCards where IcTournament=$TourId and IcType='$CardType' and IcNumber=$CardNumber");
	if($r=safe_fetch_assoc($q)) {
		$Layout['IdCards']=$r;

		$q=safe_r_SQL("select * from IdCardElements where IceTournament=$TourId and IceCardType='$CardType' and IceCardNumber=$CardNumber");
		while($r=safe_fetch_assoc($q)) {
			$Layout['IdCardElements'][]=$r;
		}

		// We'll be outputting a gzipped TExt File in UTF-8 pretending it's binary
		header('Content-type: application/octet-stream');

		// It will be called ToCode-IdCard.ianseo
		header("Content-Disposition: attachment; filename=\"{$_SESSION['TourCode']}-{$CardType}-{$CardNumber}-IdCard.ianseo\"");

		ini_set('memory_limit',sprintf('%sM',512));

		echo gzcompress(serialize($Layout),9);
		die();
	}
}


$Badges=array();
$t=safe_r_sql("SELECT * FROM IdCards WHERE IcTournament=$TourId and IcType='$CardType' and IcNumber=$CardNumber");
$RowBn=emptyIdCard(safe_fetch($t));

// select sessions
$Qsessions=GetSessions('Q',true);
$Esessions=GetSessions('E',true);
$SesQNo=count($Qsessions);
$SesENo=count($Esessions);


$JS_SCRIPT = array(
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
	'<script type="text/javascript" src="Fun_AJAX_IdCards.js"></script>',
	);

$JS_SCRIPT[]='<script type="text/javascript">';
$JS_SCRIPT[]='	var SesQNo='.$SesQNo.';';
$JS_SCRIPT[]='	var SesENo='.$SesENo.';';
$JS_SCRIPT[]='</script>';
$JS_SCRIPT[]='<style>';
$JS_SCRIPT[]='#SpecificBadges {display:flex;justify-content:center;flex-wrap:wrap;}';
$JS_SCRIPT[]='#SpecificBadges div {margin-left:1em;margin-right:1em;}';
$JS_SCRIPT[]='</style>';

$PAGE_TITLE=get_text($CardType.'-Badge', 'BackNumbers');

$ONLOAD=' onload="ShowEntries()"';

include('Common/Templates/head.php');

if($CardType=='A' and $_SESSION['AccreditationTourIds']) {
	$TourId=$_SESSION['AccreditationTourIds'];
}


echo '<form method="POST" target="Badges" enctype="multipart/form-data">';
echo '<table class="Tabella">' ;
echo '<tr><th class="Title" colspan="2">' . $PAGE_TITLE  . '</th></tr>';
echo '<tr>';
echo '<td class="w-50">';

// tipo di badge
if($CardType=='A') {
	echo '<div style="margin-bottom:1em"><b>'.get_text('BadgeType', 'Tournament').'</b>';
	echo '<div><input type="radio" name="BadgeTypeSelector" value="Card.php" onclick="hide_custom()">'.get_text('BadgeStandard', 'Tournament')
		. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name="BadgePerPage">'
		. '<option value="4">'.get_text('Badge4PerPage', 'Tournament').'</option>'
		. '<option value="2">'.get_text('Badge2PerPage', 'Tournament').'</option>'
		. '<option value="1">'.get_text('Badge1PerPage', 'Tournament').'</option>'
		. '</select></div>';
	echo '<div><input type="radio" name="BadgeTypeSelector" value="Cardx6.php" onclick="hide_custom()">'.get_text('BadgeStandard6', 'Tournament')."</div>";
	echo '<div><input type="radio" name="BadgeTypeSelector" value="CardCustom.php" onclick="show_custom()" checked="checked">'.get_text('BadgeCustom', 'BackNumbers')."</div>";
	echo '</div>';
} else {
	echo '<input type="hidden" name="BadgeTypeSelector" value="CardCustom.php">';
}

// this stays on the opened competition, so on session
echo '<div class="CustomBadges">';
	// little table with the badge selector...
	echo '<table class="Tabella" style="margin-right:auto; margin-bottom:2em;">';
	echo '<tr><th>'.get_text('BadgeType', 'Tournament').'</th>
		<td><select id="BadgeType" name="CardType" onchange="location.href=\'?CardType=\'+this.value">';

	$TypeArray=array();
	if(hasACL(AclAccreditation, AclReadOnly)) {
		$TypeArray[]='A';
	}
	if(hasACL(AclQualification, AclReadOnly)) {
		$TypeArray[]='Q';
	}
	$q=safe_r_sql("Select distinct EvElim2, EvTeamEvent from Events where EvTournament={$_SESSION['TourId']} and EvFinalFirstPhase>0 order by EvElim2=0");
	while($r=safe_fetch($q)) {
		if($r->EvElim2>0 and !in_array('E', $TypeArray) and hasACL(AclEliminations, AclReadOnly)) $TypeArray[]='E';
		if(!$r->EvTeamEvent and !in_array('I', $TypeArray) and hasACL(AclIndividuals, AclReadOnly)) $TypeArray[]='I';
		if($r->EvTeamEvent and !in_array('T', $TypeArray) and hasACL(AclTeams, AclReadOnly)) $TypeArray[]='T';
	}
	if(hasACL(AclCompetition, AclReadOnly)) {
		$TypeArray[]='Y';
		$TypeArray[]='Z';
	}
	foreach($TypeArray as $Type) {
		echo '<option value="'.$Type.'"'.($CardType==$Type ? ' selected="selected"' : '').'>'.get_text($Type.'-Badge', 'BackNumbers').'</options>';
	}
	echo '</select></td></tr>';

	$IdCards=safe_r_sql("select * from IdCards where IcTournament={$_SESSION['TourId']} and IcType='$CardType' order by IcNumber");
	if(safe_num_rows($IdCards)) {
		echo '<tr><th>'.get_text('BadgeName', 'BackNumbers').'</th>
			<td><select name="CardNumber" id="BadgeNumber" onchange="location.href=\'?CardType='.$CardType.'&CardNumber=\'+this.value">';
			while($r=safe_fetch($IdCards)) {
				echo '<option value="'.$r->IcNumber.'"'.($CardNumber==$r->IcNumber ? ' selected="selected"' : '').'>'.$r->IcName.'</options>';
			}
			echo '</select></td></tr>';

		$CategoryMatches=getModuleParameter('Accreditation', 'Matches-'.$CardType.'-'.$CardNumber, '');
		echo '<tr><th>'.get_text('AccreditationMatches', 'BackNumbers').'</th>
			<td>'.$CategoryMatches.'</td></tr>';
	}
    if($lvl==AclReadWrite) {
        echo '<tr><th>' . get_text('NewBadgeName', 'BackNumbers') . '</th>
		    <td><input type="text" id="newBadgeName">
			    <input type="button" value="' . get_text('BadgeCreate', 'BackNumbers') . '" onclick="CreateNewBadge()">';
        if (safe_num_rows($IdCards)) echo ' <input type="button" value="' . get_text('BadgeDelete', 'BackNumbers') . '" onclick="if(confirm(\'' . get_text('MsgAreYouSure') . '\')) {location.href=location.href+\'&delete=1\'}">';
        echo '</td></tr>';
    }
	echo '</table>';
echo '</div>';

if (safe_num_rows($IdCards)) {

	/** Show all the options for this badge type and number **/
	echo '<div class="CustomBadges">';
	echo '<input name="BadgeDraw" type="radio" value="Complete" checked="checked" onclick="hide_confirm()">&nbsp;' . get_text('BadgeComplete', 'BackNumbers') . '<br>';
	echo '<input name="BadgeDraw" type="radio" value="Test" onclick="hide_confirm()">&nbsp;' . get_text('BadgeTest', 'BackNumbers') . '<br><br>';
	echo '</div>';

	if($lvl==AclReadWrite) {
	    echo '<div class="CustomBadges">';
	    echo '<div><input type="button" value="' . get_text('BadgeEdit', 'BackNumbers') . '" onClick="window.open(\'' . $CFG->ROOT_DIR . 'Accreditation/IdCardEdit.php?' . $GlobalLink . '\')"></div>';
	    echo '<div><input type="submit" name="ExportLayout" value="' . get_text('BadgeExportLayout', 'BackNumbers') . '" onclick="baseForm(this)"></div>';
	    echo '<div></div><input type="file" name="ImportBackNumbers" />&nbsp;&nbsp;&nbsp;';
	    echo '<input type="submit" name="ImportLayout" value="' . get_text('BadgeImportLayout', 'BackNumbers') . '" onclick="baseForm(this)"></div>';
	    echo '</div>';
	}
	echo '</td>';

	//Header e Immagini
	// immagine fittizia del badge
		echo '<td class="w-50 Center">';
			echo '<div class="CustomBadges">';
			if(safe_num_rows($t)) echo '<img src="ImgIdCard.php?'.$GlobalLink.'">';
			echo '</div>';

		echo '</td>';
		echo '</tr>';
	echo '</table>';

		// Badge printout
	echo '<table class="Tabella">' ;
		echo '<tr><th class="Title" colspan="'.(5+($CardType=='I' or $CardType=='T')).'">' . get_text('BadgePrintout','Tournament')  . '</th></tr>' ;

		/******
		 * these items follow the multi competition setting!
		 * */

		// Selector for specific badges if any
		$Head=true;
		foreach(explode(',', $TourId) as $ToId) {
			if($Specific=getModuleParameterLike('Accreditation', 'Matches-'.$CardType.'-%', $ToId)) {
				if($Head) {
					echo '<tr><th class="Title" colspan="'.(5+($CardType=='I' or $CardType=='T')).'">' . get_text('PrintSpecificBadges','BackNumbers')  . '</th></tr>';
					$Head=false;
				}
				$SpecCards=array();
				foreach($Specific as $Id => $Name) {
					$tmp=explode('-', $Id);
					$SpecCards[$Id]=end($tmp);
				}
				$tt=safe_r_sql("select * from IdCards where IcType='$CardType' and IcNumber in (".implode(',', $SpecCards).") and IcTournament=$ToId");
				echo '<tr class="CustomBadges"><td colspan="'.(5+($CardType=='I' or $CardType=='T')).'" class="Center"><div id="SpecificBadges">';
				while($uu=safe_fetch($tt)) {
					echo '<div><input type="checkbox" name="Specifics['.$ToId.']['.$uu->IcNumber.']" value="'.$Specific['Matches-'.$CardType.'-'.$uu->IcNumber].'" checked="checked">'.$uu->IcName.' ('.$Specific['Matches-'.$CardType.'-'.$uu->IcNumber].')</div>';
				}
				echo '</div></td></tr>';
			}
		}

		echo '<tr>';
		echo '<th class="Title">'.get_text('BadgeOptions','Tournament').'</th>';
		echo '<th class="Title">'.get_text('Country').' (<span id="CountriesLeft"></span>)</th>';
		if($CardType=='I' or $CardType=='T') {
			echo '<th class="Title">'.get_text('Phase').'</th>';
		} else {
			echo '<th class="Title">'.get_text('Division').'</th>';
			echo '<th class="Title">'.get_text('Class').'</th>';
		}
		echo '<th class="Title">'.get_text('BadgeNames','Tournament').' (<span id="getEntriesNum"></span>)</th>';
		echo '</tr>';


		echo '<tr class="Top">';

		// Elenco opzioni
		echo '<td nowrap="nowrap">';

		// Select a bib
		echo '<div style="margin-bottom:1em"><b>'.get_text('BibNumber', 'BackNumbers').':</b> <input type="text" id="BibNumber" name="BibNumber" onkeypress="checkBibNumber(this)"> <input type="button" id="print_button2" value="'.get_text('Print','Tournament').'" onclick="printBibname(this)"></div>';

		// Specific Options
		switch($CardType) {
			case 'A':
				if($_SESSION['AccreditationTourIds']) {
					$TourId=$_SESSION['AccreditationTourIds'];
				}
				if($_SESSION['AccBooth']) {
					echo '<div style="margin-bottom:1em"><b>'.get_text('Depot', 'BackNumbers').'</b>';
					echo '<br/><input type="checkbox" name="HasPlastic" id="HasPlastic" onclick="ShowEntries()">'.get_text('PrintHasPlastic', 'BackNumbers');
					echo '</div>';
					echo '<div style="margin-bottom:1em"><b>'.get_text('AutoCHK-Code', 'BackNumbers').'</b>';
					$t=safe_r_sql("select * from Tournament where ToId in ({$_SESSION['AccreditationTourIds']})");
					while($u=safe_fetch($t)) {
						echo '<br/><input type="checkbox" name="TourId[]" class="TourId" id="TourId[]" onclick="updateView()" value="'.$u->ToId.'">'.$u->ToCode;
					}
					echo '</div>';
				}
			case 'Q':
				echo '<div style="margin-bottom:1em"><b>'.get_text('BadgeSessions', 'Tournament').'</b>';
				foreach ($Qsessions as $s)
				{
					echo '<br/><input type="checkbox" onclick="ShowEntries()" id="d_QSession_'.$s->SesOrder.'" class="QSession" name="Session[]" value="' . $s->SesOrder . '">Session ' . $s->Descr ;
				}
				echo '<br/><input type="checkbox" name="SortByTarget" id="SortByTarget"'.($CardType=='A' ? '' : ' checked="checked"').' onclick="ShowEntries()">'.get_text('SortByTarget', 'Tournament');
				// break is left out on purpose!
				if($CardType=='A') {
					echo '</div>';
					echo '<div style="margin-bottom:1em"><b>'.get_text('BadgeOptions', 'Tournament').'</b>';
					// badges devono includere la foto?
					echo '<br/><input type="checkbox" name="IncludePhoto" id="IncludePhoto" checked="checked" onclick="hide_confirm(this.form)">'.get_text('BadgeIncludePhoto', 'Tournament');
					// solo badges con foto?
					echo '<br/><input type="checkbox" name="PrintPhoto" id="PrintPhoto" checked="checked" onclick="ShowEntries()">'.get_text('BadgeOnlyPrintPhoto', 'Tournament');
					// solo accreditati?
					echo '<br/><input type="checkbox" name="PrintAccredited" id="PrintAccredited" onclick="ShowEntries()">'.get_text('BadgeOnlyPrintAccredited', 'Tournament');
				}
				// solo i non stampati precedentemente?
				echo '<br/><input type="checkbox" name="PrintNotPrinted" id="PrintNotPrinted" checked="checked" onclick="ShowEntries()">'.get_text('BadgeOnlyNotPrinted', 'Tournament');
				echo '</div>';
				break;
			case 'E':
				echo '<div style="margin-bottom:1em"><b>'.get_text('BadgeSessions', 'Tournament').'</b>';
				foreach ($Esessions as $s)
				{
					echo '<br/><input type="checkbox" onclick="ShowEntries()" id="d_ESession_'.$s->SesOrder.'" class="ESession" name="ESession[]" value="' . $s->SesOrder . '">Session ' . $s->Descr ;
				}
				echo '<br/><input type="checkbox" name="SortByTarget" id="SortByTarget"'.($CardType=='A' ? '' : ' checked="checked"').' onclick="ShowEntries()">'.get_text('SortByTarget', 'Tournament');
				// solo i non stampati precedentemente?
				echo '<br/><input type="checkbox" name="PrintNotPrinted" id="PrintNotPrinted" checked="checked" onclick="ShowEntries()">'.get_text('BadgeOnlyNotPrinted', 'Tournament');
				echo '</div>';
				break;
			case 'I':
			case 'T':
			case 'Y':
			case 'Z':
				echo '<div style="margin-bottom:1em"><b>'.get_text('Events', 'Tournament').'</b>';
				$q=safe_r_sql("select * from Events where EvTournament={$_SESSION['TourId']} and EvTeamEvent=".intval($CardType=='T' or $CardType=='Z')." and EvFinalFirstPhase>0 and EvShootOff=1 order by EvProgr");
				while ($r=safe_fetch($q)) {
					echo '<br/><input type="checkbox" onclick="ShowEntries()" id="Event['.$r->EvCode.']" class="Events" name="Event[]" value="'.$r->EvCode.'">' . $r->EvEventName ;
				}
				if(strstr('YZ', $CardType)) {
					echo '<br/><input type="number" name="TopRanked" id="TopRanked" onchange="ShowEntries()">&nbsp;'.get_text('RankLimit', 'BackNumbers');
					echo '<br/><input type="number" name="TopRankedFinal" id="TopRankedFinal" onchange="ShowEntries()">&nbsp;'.get_text('FinalRankLimit', 'BackNumbers');
				}
				// solo i non stampati precedentemente?
				echo '<br/><input type="checkbox" name="PrintNotPrinted" id="PrintNotPrinted" checked="checked" onclick="ShowEntries()">'.get_text('BadgeOnlyNotPrinted', 'Tournament');
				echo '</div>';
				break;
		}

		echo '</td>';

		// elenco Countries
		echo '<td class="Center">
			<select onchange="ShowEntries()" name="Country[]" id="d_Country" multiple="multiple" title="'.get_text('PressCtrl2SelectAll').'" onclick="hide_confirm(this.form)" size="10">
			</select>
			</td>';

		if($CardType=='I' or $CardType=='T') {
			// elenco Phases
			echo '<td class="Center"><select onchange="ShowEntries()" name="Phase" id="d_Phase" title="'.get_text('PressCtrl2SelectAll').'" size="10">';
			echo '<option value="-1" selected></option>';
			$phases = getPhaseArray();
			$q=safe_r_sql("SELECT distinct EvFinalFirstPhase, greatest(PhId, PhLevel) as Phase
				from Events 
				inner join Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2, EvTeamEvent))>0
				where EvTeamEvent=".($CardType=='T' ? 1 : 0)." and EvTournament in ($TourId) and EvFinalFirstPhase>0 order by EvFinalFirstPhase desc");
			while($Rs = safe_fetch($q)) {
	            foreach ($phases as $k=>$v) {
	                if($k<=valueFirstPhase($Rs->EvFinalFirstPhase)) {
	                    echo '<option value="' . $k . '">' . get_text(namePhase($Rs->EvFinalFirstPhase, $k) . '_Phase') . '</option>';
	                }
	            }

			}
		    echo '<option value="1">'.get_text('1_Phase').'</option>';
		    echo '<option value="0">'.get_text('0_Phase').'</option>';
			echo '</select></td>';
		} else {
			// elenco Divisions
			echo '<td class="Center"><select onchange="ShowEntries()" name="Division[]" id="d_Division" multiple="multiple" title="'.get_text('PressCtrl2SelectAll').'" size="10">';
			//$Sql = "SELECT distinct EnDivision From Entries WHERE EnTournament in ($TourId) order by EnDivision";
			//$Rs = safe_r_sql($Sql);
			//while($r=safe_fetch($Rs)) {
			//	echo '<option value="'.$r->EnDivision.'">'.$r->EnDivision.'</option>';
			//}
			echo '</select></td>';

			// elenco Classes
			echo '<td class="Center"><select onchange="ShowEntries()" name="Class[]" id="d_Class" multiple="multiple" title="'.get_text('PressCtrl2SelectAll').'" size="10">';
			//$Sql = "SELECT distinct EnClass From Entries WHERE EnTournament in ($TourId) order by EnClass";
			//$Rs = safe_r_sql($Sql);
			//while($r=safe_fetch($Rs)) {
			//	echo '<option value="'.$r->EnClass.'">'.$r->EnClass.'</option>';
			//}
			echo '</select></td>';
		}

		// elenco Entries
		echo '<td class="Center">
			<select name="Entries[]" id="p_Entries" multiple="multiple" title="'.get_text('PressCtrl2SelectAll').'"  size="10">
			</select>
			</td>';

		echo '</tr>';

		echo '<tr><td colspan="'.(5+($CardType=='I' or $CardType=='T')).'" class="Center">';
		echo '<input type="button" style="display:none;margin-left:2em" id="confirm_button" name="DoPrint" title="'.get_text('BadgeConfirmPrintedDescr','Tournament').'" value="'.get_text('BadgeConfirmPrinted','Tournament').'" onclick="ConfirmPrinted()">';
		echo '<input type="submit" id="print_button" value="'.get_text('Print','Tournament').'" onclick="pdfForm(this)">';

	echo '</td></tr>';
}
echo '</table></form>';

include('Common/Templates/tail.php');
