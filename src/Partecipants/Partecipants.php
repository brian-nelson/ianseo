<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
    checkACL(AclParticipants, AclReadWrite);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Partecipants/Fun_Partecipants.local.inc.php');
	require_once('Common/Fun_Sessions.inc.php');

	$OrderBy='';

	if (isset($_REQUEST['ordSes']) && ($_REQUEST['ordSes']=='ASC' || $_REQUEST['ordSes']=='DESC'))
		$OrderBy = "QuSession " . $_REQUEST['ordSes'] . " ";

	if (isset($_REQUEST['ordTar']) && ($_REQUEST['ordTar']=='ASC' || $_REQUEST['ordTar']=='DESC'))
		$OrderBy = "QuTargetNo " . $_REQUEST['ordTar'] . " ";

	if (isset($_REQUEST['ordCode']) && ($_REQUEST['ordCode']=='ASC' || $_REQUEST['ordCode']=='DESC'))
		$OrderBy = "EnCode " . $_REQUEST['ordCode'] . " ";

	if (isset($_REQUEST['ordName']) && ($_REQUEST['ordName']=='ASC' || $_REQUEST['ordName']=='DESC'))
		$OrderBy = "EnFirstName " . $_REQUEST['ordName'] . ",EnName " . $_REQUEST['ordName'] . " ";

	if (isset($_REQUEST['ordCtrl']) && ($_REQUEST['ordCtrl']=='ASC' || $_REQUEST['ordCtrl']=='DESC'))
		$OrderBy = "EnCtrlCode " . $_REQUEST['ordCtrl'] . " ";

	if (isset($_REQUEST['ordSex']) && ($_REQUEST['ordSex']=='ASC' || $_REQUEST['ordSex']=='DESC'))
		$OrderBy = "EnSex " . $_REQUEST['ordSex'] . " ";

	if (isset($_REQUEST['ordCountry']) && ($_REQUEST['ordCountry']=='ASC' || $_REQUEST['ordCountry']=='DESC'))
		$OrderBy = "CoCode " . $_REQUEST['ordCountry'] . " ";

	if (isset($_REQUEST['ordDiv']) && ($_REQUEST['ordDiv']=='ASC' || $_REQUEST['ordDiv']=='DESC'))
		$OrderBy = "EnDivision " . $_REQUEST['ordDiv'] . " ";

	if (isset($_REQUEST['ordAgeCl']) && ($_REQUEST['ordAgeCl']=='ASC' || $_REQUEST['ordAgeCl']=='DESC'))
		$OrderBy = "EnAgeClass " . $_REQUEST['ordAgeCl'] . " ";

	if (isset($_REQUEST['ordCl']) && ($_REQUEST['ordCl']=='ASC' || $_REQUEST['ordCl']=='DESC'))
		$OrderBy = "EnClass " . $_REQUEST['ordCl'] . " ";

	if (isset($_REQUEST['ordSubCl']) && ($_REQUEST['ordSubCl']=='ASC' || $_REQUEST['ordSubCl']=='DESC'))
		$OrderBy = "EnSubClass " . $_REQUEST['ordSubCl'] . " ";

	// prepare the Target array
	require_once('Fun_Targets.php');

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="../Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_index.js"></script>',
	//	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Partecipants-exp/firebuglite/firebug-lite-compressed.js"></script>',
	//	'<script type="text/javascript">firebug.env.css = "'.$CFG->ROOT_DIR.'Partecipants-exp/firebuglite/firebug-lite.css";</script>',
		'<script type="text/javascript" src="Fun_AJAX_Partecipants.js"></script>',
		'<script type="text/javascript" src="../Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="Fun_JS.js"></script>',
		getTargetsScript(),
		'<script type="text/javascript">',
		'var StrLoading = "' . get_text('Loading','Tournament') . '";',
		'var StrCancel = "' . get_text('CmdCancel') . '";',
		'</script>',
		);

	$ONLOAD=' onLoad="javascript:GetRows_Par(\'\',\''. $OrderBy . '\');"';

	$PAGE_TITLE=get_text('TourPartecipants','Tournament');

	include('Common/Templates/head.php');

	$MyHeader
		= '<tr>'
		. '<td class="Title" width="4%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordSes=' . (isset($_REQUEST['ordSes']) && $_REQUEST['ordSes']=='ASC' ? 'DESC' : 'ASC') . '">' . get_text('Session') . '</a></td>'
		. '<td class="Title" width="4%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordTar=' . (isset($_REQUEST['ordTar']) && $_REQUEST['ordTar']=='ASC' ? 'DESC' : 'ASC') . '">' . get_text('Target') . '</a></td>'
		. '<td class="Title" width="6%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordCode=' . (isset($_REQUEST['ordCode']) && $_REQUEST['ordCode']=='ASC' ? 'DESC' : 'ASC') . '">' . get_text('Code','Tournament') . '</a></td>'
		. '<td class="Title" width="12%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordName=' . (isset($_REQUEST['ordName']) && $_REQUEST['ordName']=='ASC' ? 'DESC' : 'ASC') . '">' . get_text('FamilyName','Tournament') . '</a></td>'
		. '<td class="Title" width="12%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordName=' . (isset($_REQUEST['ordName']) && $_REQUEST['ordName']=='ASC' ? 'DESC' : 'ASC') . '">' . get_text('Name','Tournament') . '</a></td>'
		//. '<td class="Title" width="3%">' . get_text('Sex','Tournament') . '</td>'
		. '<td class="Title" width="7%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordCtrl=' . (isset($_REQUEST['ordCtrl']) && $_REQUEST['ordCtrl']=='ASC' ? 'DESC' : 'ASC') . '">' .get_text('DOB','Tournament') . '</a></td>'
		. '<td class="Title" width="4%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordSex=' . (isset($_REQUEST['ordSex']) && $_REQUEST['ordSex']=='ASC' ? 'DESC' : 'ASC') . '">' . get_text('Sex','Tournament') . '</a></td>'
		. '<td class="Title" width="4%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordCountry=' . (isset($_REQUEST['ordCountry']) && $_REQUEST['ordCountry']=='ASC' ? 'DESC' : 'ASC') . '">' . get_text('Country') . '</a></td>'
		. '<td class="Title" width="21%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordCountry=' . (isset($_REQUEST['ordCountry']) && $_REQUEST['ordCountry']=='ASC' ? 'DESC' : 'ASC') . '">' . get_text('NationShort','Tournament') . '</a></td>'

		. '<td class="Title" width="4%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordSubTeam=' . (isset($_REQUEST['ordSubTeam']) && $_REQUEST['ordSubTeam']=='ASC' ? 'DESC' : 'ASC') . '">' . get_text('PartialTeam') . '</a></td>'
		. '<td class="Title" width="4%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordCountry2=' . (isset($_REQUEST['ordCountry2']) && $_REQUEST['ordCountry2']=='ASC' ? 'DESC' : 'ASC') . '">' . get_text('Country') .' (2)' . '</a></td>'
		. '<td class="Title" width="21%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordCountry2=' . (isset($_REQUEST['ordCountry2']) && $_REQUEST['ordCountry2']=='ASC' ? 'DESC' : 'ASC') . '">' . get_text('NationShort','Tournament') .' (2)'. '</a></td>'

		. '<td class="Title" width="5%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordDiv=' . (isset($_REQUEST['ordDiv']) && $_REQUEST['ordDiv']=='ASC' ? 'DESC' : 'ASC') . '">' . get_text('Div') . '</a></td>'
		. '<td class="Title" width="5%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordAgeCl=' . (isset($_REQUEST['ordAgeCl']) && $_REQUEST['ordAgeCl']=='ASC' ? 'DESC' : 'ASC') . '">' . get_text('AgeCl') . '</a></td>'
		. '<td class="Title" width="5%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordCl=' . (isset($_REQUEST['ordCl']) && $_REQUEST['ordCl']=='ASC' ? 'DESC' : 'ASC') . '">' . get_text('Cl') . '</a></td>'
		. '<td class="Title" width="5%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordSubCl=' . (isset($_REQUEST['ordSubCl']) && $_REQUEST['ordSubCl']=='ASC' ? 'DESC' : 'ASC') . '">' . get_text('SubCl','Tournament') . '</a></td>'
		. '<td class="Title" width="5%">' . get_text('Target') . '</td>'
		. '<td class="Title" width="5%">&nbsp;</td>'
		. '</tr>';
?>
<form name="Frm" method="GET" action="">
<table class="Tabella" id="idAthList">
<tbody>
<tr><th class="Title" colspan="18"><?php echo get_text('TourPartecipants','Tournament') ?></th></tr>
<tr class="Divider"><td colspan="18"></td></tr>
<tr>
<td colspan="5" class="Bold">
<?php
// vedi commento "turni"
	$ComboSes
		= '<select id="d_q_QuSession_" onBlur="javascript:SelectSession_Par();">'
		. '<option value="0">--</option>';

	$ComboDiv
		= '<select id="d_e_EnDivision_" onChange="CheckTargetFaces()">'
		. '<option value="">--</option>';

	$Select = "SELECT DivId FROM Divisions WHERE DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY DivViewOrder ASC ";
	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)>0)
	{
		while ($Row=safe_fetch($Rs))
		{
			$ComboDiv.='<option value="' . $Row->DivId . '">' . $Row->DivId . '</option>';
		}
	}
	$ComboDiv.='</select>';


	$ComboCl
		= '<select id="d_e_EnClass_" onChange="CheckTargetFaces()">'
		. '<option value="">--</option>';

	$ComboSubCl
		= '<select id="d_e_EnSubClass_">'
		. '<option value="">--</option>';

	$Select = "SELECT ScId FROM SubClass WHERE ScTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY ScViewOrder ASC ";
	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)>0)
	{
		while ($Row=safe_fetch($Rs))
		{
			$ComboSubCl.='<option value="' . $Row->ScId . '">' . $Row->ScId . '</option>';
		}
	}
	$ComboSubCl.='</select>';

	$ComboAgeCl
		= '<select id="d_e_EnAgeClass_" onFocus="javascript:GetClassesByGender_Par();" onBlur="javascript:SelectAgeClass_Par(\'\');">'
		. '<option value="">--</option>';

	$Select = "SELECT ClId FROM Classes WHERE ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY ClViewOrder ASC ";
	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)>0)
	{
		while ($Row=safe_fetch($Rs))
		{
			$Arr_Cl[$Row->ClId]=$Row->ClId;
			$ComboAgeCl.='<option value="' . $Row->ClId . '">' . $Row->ClId . '</option>';
		//	$ComboCl.='<option value="' . $Row->ClId . '">' . $Row->ClId . '</option>';
		}
	}
	$ComboAgeCl.='</select>';
	$ComboCl.='</select>';

/*
	Conto quanti ath ho in elenco in modo da decidere se attivare il warning oppure no
*/
	$Sel
		= "SELECT EnId "
		. "FROM Entries "
		. "WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete='1' ";
	$RsSel = safe_r_sql($Sel);
	$Warning = 0;

	if (safe_num_rows($RsSel)>0)
		$Warning=1;

// Numero max di righe
	$MaxRows=0;
	$sessions=GetSessions('Q');

	foreach ($sessions as $s)
	{
		$MaxRows+=$s->SesAth4Target*$s->SesTar4Session;
		$ComboSes.='<option value="' . $s->SesOrder . '">' . $s->SesOrder . '</option>';
	}
/*
	$Sel = "SELECT ToNumSession, ";
	for ($i=1;$i<=9;++$i)
		$Sel.= "ToTar4Session" . $i . ",ToAth4Target" . $i . ",";
	$Sel=substr($Sel,0,-1);

	$Sel
		.=" FROM Tournament "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
//	print $Sel;
	$RsSel = safe_r_sql($Sel);

	if (safe_num_rows($RsSel)==1)
	{
		$Row=safe_fetch($RsSel);

		for ($i=1;$i<=$Row->ToNumSession;++$i)
		{
			$MaxRows+= ($Row->{'ToTar4Session' . $i}*$Row->{'ToAth4Target' . $i});

		// turni
			$ComboSes.='<option value="' . $i . '">' . $i . '</option>';
		}
	}*/

	$ComboSes.='</select>';

	//print '<a class="Link" href="javascript:AddManyRows(' . $MaxRows . ',' . $Warning . ',\'' . urlencode(str_replace('<br>','\n',get_text('MsgAddManyRowsWarning','Tournament'))) . '\');">' . get_text('AddMaxRows','Tournament') . '</a>';

	$ComboSex='<select id="d_e_EnSex_"  onChange="javascript:CheckCtrlCode_Par();">';
		$ComboSex.='<option value="0">' . get_text('ShortMale','Tournament') . '</option>';
		$ComboSex.='<option value="1">' . get_text('ShortFemale','Tournament') . '</option>';
	$ComboSex.='</select>';

	$ComboTf='<select id="d_e_EnTargetFace_">';
		$ComboTf.='<option value="0">--</option>';
	$ComboTf.='</select>';
?>
&nbsp;
</td>
<td id="tdStatus" colspan="18" class="Bold FontMedium"><div id="idStatus">&nbsp;</div></td>
</tr>
<tr class="Divider"><td colspan="18"><a name="Edit"></a></td></tr>
<?php
	print $MyHeader;
// Qui sotto la riga di insert/edit
?>
<tr id="EditRow">
<td class="Right">
<input type="hidden" id="d_e_EnId_" value="0">
<?php /*?><input type="hidden" id="d_e_EnSex_" value="0"><?php */?>
<input type="hidden" id="d_e_EnStatus_" value="0">
<?php print $ComboSes; ?>
</td>
<td class="Right"><input style="text-align:right;" type="text" size="<?php print (TargetNoPadding+1); ?>" maxlength="<?php print (TargetNoPadding+1); ?>" id="d_q_QuTargetNo_" value="" onBlur="javascript:CheckTargetNo_Par();"></td>
<td>
<input type="hidden" id="CanComplete_" value="0">
<input style="text-align:right;" type="text" id="d_e_EnCode_" size="6" maxlength="9" onFocus="javascript:SetCompleteFlag('');" onKeyUp="javascript:CercaMatr_Par();" onBlur="javascript:SetCompleteFlag('');">
</td>
<td><input type="text" id="d_e_EnFirstName_" size="20" maxlength="30"></td>
<td><input type="text" id="d_e_EnName_" size="20" maxlength="30"></td>
<td><input type="text" id="d_e_EnCtrlCode_" size="16" maxlength="16" onBlur="javascript:CheckCtrlCode_Par();"></td>
<td><?php print $ComboSex;?></td>
<td>
<input type="hidden" id="d_e_EnCountry_" value="0">
<input style="text-align:right;" type="text" id="d_c_CoCode_" size="5" maxlength="5" onKeyUp="javascript:SelectCountryCode('');">
</td>
<td><input type="text" id="d_c_CoName_" size="20" maxlength="30"></td>

<td><input type="text" id="d_e_EnSubTeam_" size="2" maxlength="3"></td>
<td>
<input type="hidden" id="d_e_EnCountry2_" value="0">
<input style="text-align:right;" type="text" id="d_c_CoCode2_" size="5" maxlength="5" onKeyUp="javascript:SelectCountryCode2('');">
</td>
<td><input type="text" id="d_c_CoName2_" size="20" maxlength="30"></td>

<td class="Center"><?php print $ComboDiv; ?></td>
<td class="Center"><?php print $ComboAgeCl; ?></td>
<td class="Center"><?php print $ComboCl; ?></td>
<td class="Center"><?php print $ComboSubCl; ?></td>
<td class="Center"><?php print $ComboTf; ?></td>
<td class="Center">&nbsp;</td>
<td class="Center">&nbsp;</td>
</tr>
<tr>
<td class="Center" colspan="18">
<input type="button" value="<?php print get_text('CmdSave'); ?>" onClick="javascript:Save_Par();">&nbsp;&nbsp;
<input type="button" value="<?php print get_text('CmdCancel'); ?>" onClick="javascript:ResetInput();">
</td>
</tr>
<tr id="EditRowSep" class="Spacer"><td colspan="18"></td></tr>
</tbody>
</table>
</form>
<script type="text/javascript">document.getElementById('EditRow').oncontextmenu=FindByContext;</script>
<table class="Tabella">
<tr><td class="Bold Center"><?php print get_text('LegendStatus','Tournament'); ?></td></tr>
<tr class="CanShoot"><td><?php print get_text('Status_1'); ?></td></tr>
<tr class="CouldShoot"><td><?php print get_text('Status_8'); ?></td></tr>
<tr class="NoShoot"><td><?php print get_text('Status_9'); ?></td></tr>
</table>
<script type="text/javascript">SetOnTextBox();</script>
<div id="idOutput">	</div>
<?php
	include('Common/Templates/tail.php');
?>