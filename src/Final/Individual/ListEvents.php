<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Fun_Various.inc.php');

	/*$Select
		= "SELECT TtElimination "
		. "FROM Tournament INNER JOIN Tournament*Type ON ToType=TtId AND ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";*/
	$Select
		= "SELECT ToElimination AS TtElimination "
		. "FROM Tournament WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$RsElim=safe_r_sql($Select);

	$Elim=0;
	if (safe_num_rows($RsElim)==1)
	{
		$Row=safe_fetch($RsElim);
		$Elim=$Row->TtElimination;
	}

	$JS_SCRIPT=array(
		phpVars2js(array(
			'StrResetElimError' => get_text('ResetElimError', 'Tournament'),
			)),
		'<script type="text/javascript" src="../../Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_ListEvents.js"></script>',
		'<script type="text/javascript" src="../../Common/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="Fun_JS.js"></script>',
		);

	$ONLOAD=' onLoad="javascript:ChangeNew_EvElim();"';

	include('Common/Templates/head.php');
?>
<table class="Tabella" id="MyTable">
<tbody id="tbody">
<tr><th class="Title" colspan="<?php echo (10+$Elim); ?>"><?php print get_text('IndEventList');?></th></tr>
<tr class="Divider"><td colspan="<?php echo (10+$Elim); ?>"></td></tr>
<tr>
<th width="5%"><?php print get_text('EvCode');?></th>
<th width="25%"><?php print get_text('EvName');?></th>
<th width="5%"><?php print get_text('Progr');?></th>
<?php
if($Elim)
	echo '<th width="20%">' . get_text('Elimination') . '</th>';
?>
<th width="10%"><?php print get_text('MatchModeScoring');?></th>
<th width="10%"><?php print get_text('FirstPhase');?></th>
<th width="10%"><?php print get_text('TargetType');?></th>
<th width="5%">ø (cm)</th>
<th width="5%"><?php print get_text('Distance', 'Tournament');?></th>
<th width="5%">&nbsp;</th>
</tr>
<?php
	$ComboPhase = array();

	$Select = "SELECT PhId FROM Phases WHERE PhId>1 ORDER BY PhId DESC";
	$RsPh=safe_r_sql($Select);

	while($Phase=safe_fetch($RsPh))
	{
		$ComboPhase[$Phase->PhId]=get_text($Phase->PhId . '_Phase');
	}
	$ComboPhase[0]='---';

	$ComboMatchMode = array();
	for($i=0; $i<=1; $i++)
		$ComboMatchMode[$i]=get_text('MatchMode_' . $i);

	$ComboTarget = array();

	$Select
		= "SELECT * FROM Targets ORDER BY TarOrder ";
	$RsT=safe_r_sql($Select);

	if (safe_num_rows($RsT)>0)
	{
		while ($Row=safe_fetch($RsT))
		{
			$ComboTarget[$Row->TarId]=get_text($Row->TarDescr);
		}
	}

	$Select
		= "SELECT * FROM Events "
		. "INNER JOIN Targets ON EvFinalTargetType=TarId "
		. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='0' "
		. "ORDER BY EvProgr ASC,EvCode ASC, EvTeamEvent ASC ";

	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)>0)
	{
		while ($MyRow=safe_fetch($Rs))
		{
			print '<tr id="Row_' . $MyRow->EvCode . '">';
			print '<td class="Center"><a class="Link" href="SetEventRules.php?EvCode=' . $MyRow->EvCode . '">' . $MyRow->EvCode . '</a></td>';

			print '<td class="Center"><input type="text" size="50" maxlength="64" name="d_EvEventName_' . $MyRow->EvCode . '" id="d_EvEventName_' . $MyRow->EvCode . '" value="' . $MyRow->EvEventName . '" onBlur="javascript:UpdateField(\'d_EvEventName_' . $MyRow->EvCode . '\');">';
			print '</td>';

			print '<td class="Center">';
			print '<input type="text" size="3" maxlength="3" name="d_EvProgr_' . $MyRow->EvCode . '" id="d_EvProgr_' . $MyRow->EvCode . '" value="' . $MyRow->EvProgr . '" onBlur="javascript:UpdateField(\'d_EvProgr_' . $MyRow->EvCode . '\');">';
			print '</td>';


			if($Elim)
			{
				print '<td class="Center">';
				print '<input type="hidden" name="old_EvElim_' . $MyRow->EvCode . '" id="old_EvElim_' . $MyRow->EvCode . '" />';
				print '<select name="d_EvElim_' . $MyRow->EvCode . '" id="d_EvElim_' . $MyRow->EvCode . '" onFocus="javascript:document.getElementById(\'old_EvElim_' . $MyRow->EvCode . '\').value=this.value;" onChange="javascript:ManageElim(\'' . $MyRow->EvCode . '\');"' . ($Elim==0 ? ' disabled' : '') . '>' . "\n";
				print '<option value="0"' . ($MyRow->EvElim1==0 && $MyRow->EvElim2==0 ? ' selected'  :'') . '>' . get_text('Eliminations_0') . '</option>' . "\n";
				print '<option value="1"' . ($MyRow->EvElim1==0 && $MyRow->EvElim2>0 ? ' selected' : '') . '>' . get_text('Eliminations_1') . '</option>' . "\n";
				print '<option value="2"' . ($MyRow->EvElim1>0 && $MyRow->EvElim2>0 ? ' selected' : '') . '>' . get_text('Eliminations_2') . '</option>' . "\n";
				print '</select>&nbsp;' . "\n";
				print '<input type="hidden" name="old_EvElim1_' . $MyRow->EvCode . '" id="old_EvElim1_' . $MyRow->EvCode . '" />';
				print '<input type="hidden" name="old_EvElim2_' . $MyRow->EvCode . '" id="old_EvElim2_' . $MyRow->EvCode . '" />';
				print '<input type="text" size="3" maxlength="3" name="d_EvElim1_' . $MyRow->EvCode . '" id="d_EvElim1_' . $MyRow->EvCode . '" value="' . $MyRow->EvElim1 . '" ' . ($MyRow->EvElim1>0 ? '' : ' readOnly') . ($Elim==0 ? ' disabled' : '') . ' onFocus="javascript:document.getElementById(\'old_EvElim1_' . $MyRow->EvCode . '\').value=this.value;" onBlur="javascript:UpdateField(\'d_EvElim1_' . $MyRow->EvCode . '\');">&nbsp;';
				print '<input type="text" size="3" maxlength="3" name="d_EvElim2_' . $MyRow->EvCode . '" id="d_EvElim2_' . $MyRow->EvCode . '" value="' . $MyRow->EvElim2 . '" ' . ($MyRow->EvElim2>0 ? '' : ' readOnly') . ($Elim==0 ? ' disabled' : '') . ' onFocus="javascript:document.getElementById(\'old_EvElim2_' . $MyRow->EvCode . '\').value=this.value;" onBlur="javascript:UpdateField(\'d_EvElim2_' . $MyRow->EvCode . '\');">';
				print '</td>';
			}

			print '<td class="Center">';
			print '<select name="d_EvMatchMode_' . $MyRow->EvCode . '" id="d_EvMatchMode_' . $MyRow->EvCode . '" onChange="javascript:UpdateField(\'d_EvMatchMode_' . $MyRow->EvCode . '\');">' . "\n";
			foreach ($ComboMatchMode as $Key => $Value)
				print '<option value="' . $Key . '"' . ($Key==$MyRow->EvMatchMode ? ' selected' : '') . '>' . $Value . '</option>' . "\n";
			print '</select>' . "\n";
			print '</td>';

			print '<td class="Center">';
			print '<select name="d_EvFinalFirstPhase_' . $MyRow->EvCode . '" id="d_EvFinalFirstPhase_' . $MyRow->EvCode . '" onChange="javascript:UpdatePhase(\'' . $MyRow->EvCode . '\',' . $MyRow->EvFinalFirstPhase . ',\'' . get_text('MsgAreYouSure') . '\');">' . "\n";
			foreach ($ComboPhase as $Key => $Value)
				print '<option value="' . $Key . '"' . ($Key==$MyRow->EvFinalFirstPhase ? ' selected' : '') . '>' . $Value . '</option>' . "\n";
			print '</select>' . "\n";
			print '</td>';

			print '<td class="Center">';
			print '<select name="d_EvFinalTargetType_' . $MyRow->EvCode . '" id="d_EvFinalTargetType_' . $MyRow->EvCode . '" onChange="javascript:UpdateField(\'d_EvFinalTargetType_' . $MyRow->EvCode . '\');">' . "\n";
			foreach ($ComboTarget as $Key => $Value)
			{
				print '<option value="' . $Key . '"' . ($Key==$MyRow->EvFinalTargetType ? ' selected' : '') . '>' . $Value . '</option>' . "\n";
			}
			print '</select>' . "\n";
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
			print '</tr>' . "\n";
		}
	}
?>
<tr id="RowDiv" class="Divider"><td colspan="10"></td></tr>
<tr id="NewRow">
<td class="Center"><input type="text" name="New_EvCode" id="New_EvCode" size="4" maxlength="4"></td>
<td class="Center"><input type="text" size="50" maxlength="64" name="New_EvEventName" id="New_EvEventName"></td>
<td class="Center"><input type="text" size="3" maxlength="3" name="New_EvProgr" id="New_EvProgr"></td>
<?php
if($Elim)
{
?>
<td class="Center">
<select name="New_EvElim" id="New_EvElim"<?php print ($Elim==0 ? ' disabled' : '');?> onChange="javascript:ChangeNew_EvElim();">
<option value="0"><?php print get_text('Eliminations_0'); ?></option>
<option value="1"><?php print get_text('Eliminations_1'); ?></option>
<option value="2"><?php print get_text('Eliminations_2'); ?></option>
</select>&nbsp;
<input type="text" size="3" maxlength="3" name="New_EvElim1" id="New_EvElim1" value="0"<?php print ($Elim==0 ? ' disabled' : '');?>>&nbsp;
<input type="text" size="3" maxlength="3" name="New_EvElim2" id="New_EvElim2" value="0"<?php print ($Elim==0 ? ' disabled' : '');?>>
</td>
<?php
}
?>
<td class="Center">
<select name="New_EvMatchMode" id="New_EvMatchMode">
<?php
	foreach ($ComboMatchMode as $Key => $Value)
		print '<option value="' . $Key . '">' . $Value . '</option>' . "\n";
?>
</select>
</td>
<td class="Center">
<select name="New_EvFinalFirstPhase" id="New_EvFinalFirstPhase">
<?php
	foreach ($ComboPhase as $Key => $Value)
		print '<option value="' . $Key . '">' . $Value . '</option>' . "\n";
?>
</select>
</td>
<td class="Center">
<select name="New_EvFinalTargetType" id="New_EvFinalTargetType">
<?php
	foreach ($ComboTarget as $Key => $Value)
		print '<option value="' . $Key . '">' . $Value . '</option>' . "\n";
?>
</select>
</td>
<td class="Center"><input type="text" name="New_EvTargetSize" id="New_EvTargetSize" size="3" maxlength="3"></td>
<td class="Center"><input type="text" name="New_EvDistance" id="New_EvDistance" size="12" maxlength="10"></td>

<td class="Center">
<input type="button" name="Command" id="Command" value="<?php print get_text('CmdSave');?>" onClick="javascript:AddEvent(<?php print "'" . str_replace('<br>','\n',get_text('MsgRowMustBeComplete')) . "'";?>);">
</td>
</tr>
<?php
if($Elim)
	echo '<tr><td colspan="' . (10+$Elim) . '" class="Bold Center">' . get_text('ChangeElimWarning') . '</td></tr>';
?>
</tbody>
</table>
<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');
?>