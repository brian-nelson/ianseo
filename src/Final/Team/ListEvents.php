<?php

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    CheckTourSession(true);
    checkACL(AclCompetition, AclReadWrite);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');


	$JS_SCRIPT=array(
        '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_ListEvents.js"></script>',
		'<script type="text/javascript" src="Fun_JS.js"></script>',
		);

	$PAGE_TITLE=get_text('TeamEventList');

	include('Common/Templates/head.php');
?>
<table class="Tabella" id="MyTable">
<tbody id="tbody">
<tr><th class="Title" colspan="10"><?php print get_text('TeamEventList');?></th></tr>
<tr class="Divider"><td colspan="10"></td></tr>
<tr>
<th class="w-5"><?php print get_text('EvCode');?></th>
<th class="w-30"><?php print get_text('EvName');?></th>
<th class="w-5"><?php print get_text('Para', 'Records');?></th>
<th class="w-5"><?php print get_text('Progr');?></th>
<th class="w-10"><?php print get_text('MatchModeScoring');?></th>
<th class="w-10"><?php print get_text('FirstPhase');?></th>
<th class="w-10"><?php print get_text('TargetType');?></th>
<th class="w-5">ø (cm)</th>
<th class="w-5"><?php print get_text('Distance', 'Tournament');?></th>
<th class="w-5">&nbsp;</th>
</tr>
<?php
	$ComboPhase = array();

	$Sql= "SELECT PhId FROM Phases WHERE (PhIndTeam & 2)=2 AND PhId>1 and PhRuleSets in ('', '{$_SESSION['TourLocRule']}') Order by PhId DESC ";
	$q=safe_r_sql($Sql);
	while($r=safe_fetch($q)) {
        $ComboPhase[$r->PhId] = get_text($r->PhId . '_Phase');
    }
    $ComboPhase[0]='---';

	$ComboMatchMode = array();
	for($i=0; $i<=1; $i++) {
        $ComboMatchMode[$i] = get_text('MatchMode_' . $i);
    }
	$ComboTarget = array();

	$Select	= "SELECT * FROM Targets ORDER BY TarId ASC ";
	$RsT=safe_r_sql($Select);

	if (safe_num_rows($RsT)>0) {
		while ($Row=safe_fetch($RsT)){
			$ComboTarget[$Row->TarId]=get_text($Row->TarDescr);
		}
	}

	$Select	= "SELECT * FROM Events "
		. "LEFT JOIN Targets ON EvFinalTargetType=TarId "
        . "LEFT JOIN (SELECT EcCode, COUNT(*) as ruleCnt FROM EventClass WHERE EcTeamEvent=1 AND EcTournament=" . StrSafe_DB($_SESSION['TourId']) . " GROUP BY EcCode) as sqy on EvCode=EcCode "
		. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='1' "
		. "ORDER BY EvProgr ASC,EvCode ASC, EvTeamEvent ASC ";

	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)>0) {
		while ($MyRow=safe_fetch($Rs)) {
			print '<tr id="Row_' . $MyRow->EvCode . '" class="rowHover">';
            print '<td class="Center"><input type="button" ' . ($MyRow->ruleCnt == 0 ? 'class="red"' : '') . ' value="'.$MyRow->EvCode .'" onclick="location=\'SetEventRules.php?EvCode=' . $MyRow->EvCode . '\'"></td>';

			print '<td class="Center"><input type="text" size="64" maxlength="64" name="d_EvEventName_' . $MyRow->EvCode . '" id="d_EvEventName_' . $MyRow->EvCode . '" value="' . $MyRow->EvEventName . '" onBlur="javascript:UpdateField(\'d_EvEventName_' . $MyRow->EvCode . '\');">';
			print '</td>';

			print '<td class="Center">';
			print '<input type="checkbox" name="d_EvIsPara_' . $MyRow->EvCode . '" id="d_EvIsPara_' . $MyRow->EvCode . '" ' . ($MyRow->EvIsPara ? 'checked="checked"' : '') . ' onclick="togglePara(this)">';
			print '</td>';

			print '<td class="Center">';
			print '<input type="text" size="3" maxlength="3" name="d_EvProgr_' . $MyRow->EvCode . '" id="d_EvProgr_' . $MyRow->EvCode . '" value="' . $MyRow->EvProgr . '" onBlur="javascript:UpdateField(\'d_EvProgr_' . $MyRow->EvCode . '\');">';
			print '</td>';

			print '<td class="Center">';
			print '<select name="d_EvMatchMode_' . $MyRow->EvCode . '" id="d_EvMatchMode_' . $MyRow->EvCode . '" onChange="javascript:UpdateField(\'d_EvMatchMode_' . $MyRow->EvCode . '\');">';
			foreach ($ComboMatchMode as $Key => $Value)
				print '<option value="' . $Key . '"' . ($Key==$MyRow->EvMatchMode ? ' selected' : '') . '>' . $Value . '</option>';
			print '</select>';
			print '</td>';


			print '<td class="Center">';
			print '<select name="d_EvFinalFirstPhase_' . $MyRow->EvCode . '" id="d_EvFinalFirstPhase_' . $MyRow->EvCode . '" onChange="javascript:UpdatePhase(\'' . $MyRow->EvCode . '\',' . $MyRow->EvFinalFirstPhase . ',\'' . get_text('MsgAreYouSure') . '\');">';
			foreach ($ComboPhase as $Key => $Value)	{
				print '<option value="' . $Key . '"' . ($Key==$MyRow->EvFinalFirstPhase ? ' selected' : '') . '>' . $Value . '</option>';
			}
			print '</select>';
			print '</td>';

			print '<td class="Center">';
			print '<select name="d_EvFinalTargetType_' . $MyRow->EvCode . '" id="d_EvFinalTargetType_' . $MyRow->EvCode . '" onChange="javascript:UpdateField(\'d_EvFinalTargetType_' . $MyRow->EvCode . '\');">';
			foreach ($ComboTarget as $Key => $Value) {
				print '<option value="' . $Key . '"' . ($Key==$MyRow->EvFinalTargetType ? ' selected' : '') . '>' . $Value . '</option>';
			}
			print '</select>';
			print '</td>';

			print '<td class="Center">';
			print '<input type="text" size="3" maxlength="3" name="d_EvTargetSize_' . $MyRow->EvCode . '" id="d_EvTargetSize_' . $MyRow->EvCode . '" value="' . $MyRow->EvTargetSize . '" onBlur="javascript:UpdateField(\'d_EvTargetSize_' . $MyRow->EvCode . '\');">';
			print '</td>';

			print '<td class="Center">';
			print '<input type="text" size="12" maxlength="10" name="d_EvDistance_' . $MyRow->EvCode . '" id="d_EvDistance_' . $MyRow->EvCode . '" value="' . $MyRow->EvDistance . '" onBlur="javascript:UpdateField(\'d_EvDistance_' . $MyRow->EvCode . '\');">';
			print '</td>';

			print '<td class="Center">';
			print '<a href="javascript:DeleteEvent(\'' . $MyRow->EvCode . '\',\'' . urlencode(get_text('MsgAreYouSure')) . '\');"><img src="../../Common/Images/drop.png" border="0" alt="#" title="#"></a>';
			print '</td>';
			print '</tr>';
		}
	}
?>
<tr id="RowDiv" class="Divider"><td colspan="10"></td></tr>
<tr id="NewRow">
<td class="Center"><input type="text" name="New_EvCode" id="New_EvCode"  size="12" maxlength="10"></td>
<td class="Center"><input type="text" size="64" maxlength="64" name="New_EvEventName" id="New_EvEventName"></td>
<td class="Center"><input type="checkbox" name="New_EvIsPara" id="New_EvIsPara"></td>
<td class="Center"><input type="text" size="3" maxlength="3" name="New_EvProgr" id="New_EvProgr"></td>
<td class="Center">
<select name="New_EvMatchMode" id="New_EvMatchMode">
<?php
	foreach ($ComboMatchMode as $Key => $Value)
		print '<option value="' . $Key . '">' . $Value . '</option>';
?>
</select>
</td>
<td class="Center">
<select name="New_EvFinalFirstPhase" id="New_EvFinalFirstPhase">
<?php
	foreach ($ComboPhase as $Key => $Value)
		print '<option value="' . $Key . '">' . $Value . '</option>';
?>
</select>
</td>
<td class="Center">
<select name="New_EvFinalTargetType" id="New_EvFinalTargetType">
<?php
	foreach ($ComboTarget as $Key => $Value)
		print '<option value="' . $Key . '">' . $Value . '</option>';
?>
</select>
</td>
<td class="Center"><input type="text" name="New_EvTargetSize" id="New_EvTargetSize" size="3" maxlength="3"></td>
<td class="Center"><input type="text" name="New_EvDistance" id="New_EvDistance" size="12" maxlength="10"></td>
<td class="Center">
<input type="button" name="Command" id="Command" value="<?php print get_text('CmdSave');?>" onClick="javascript:AddEvent(<?php print "'" . str_replace('<br>','\n',get_text('MsgRowMustBeComplete')) . "'";?>);">
</td>
</tr>
</tbody>
</table>
<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');
?>
