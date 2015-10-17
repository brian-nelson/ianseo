<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');


	$JS_SCRIPT=array(
		'<script type="text/javascript" src="../../Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_ListEvents.js"></script>',
		'<script type="text/javascript" src="../../Common/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="Fun_JS.js"></script>',
		);

	$PAGE_TITLE=get_text('TeamEventList');

	include('Common/Templates/head.php');
?>
<table class="Tabella" id="MyTable">
<tbody id="tbody">
<tr><th class="Title" colspan="9"><?php print get_text('TeamEventList');?></th></tr>
<tr class="Divider"><td colspan="9"></td></tr>
<tr>
<th width="5%"><?php print get_text('EvCode');?></th>
<th width="30%"><?php print get_text('EvName');?></th>
<th width="5%"><?php print get_text('Progr');?></th>
<th width="10%"><?php print get_text('MatchModeScoring');?></th>
<th width="10%"><?php print get_text('FirstPhase');?></th>
<th width="10%"><?php print get_text('TargetType');?></th>
<th width="5%">ø (cm)</th>
<th width="5%"><?php print get_text('Distance', 'Tournament');?></th>
<th width="5%">&nbsp;</th>
</tr>
<?php
	$ComboPhase = array();
	$StartPhase = -1;

	$Select
		= "SELECT GrPhase FROM Grids WHERE GrPhase=" . StrSafe_DB(TeamStartPhase) . " AND GrPosition='1' ";
	$RsPh=safe_r_sql($Select);

// Se la fase iniziale esiste in griglia allora uso quella altrimenti cerco la massima disponibile

	if (safe_num_rows($RsPh)!=1)
	{
		$Select
			= "SELECT MAX(GrPhase) AS Phase FROM Grids ";
		$RsPh=safe_r_sql($Select);

		if (safe_num_rows($RsPh)==1)
		{
			$Row=safe_fetch($RsPh);
			$StartPhase=$Row->Phase;
		}
	}
	else
		$StartPhase=TeamStartPhase;

	if ($StartPhase!=-1)
	{
		for ($Phase=$StartPhase;$Phase>=2;$Phase/=2)
		{
			$ComboPhase[$Phase]=get_text($Phase . '_Phase');
		}
		$ComboPhase[0]='---';
	}

	$ComboMatchMode = array();
	for($i=0; $i<=1; $i++)
		$ComboMatchMode[$i]=get_text('MatchMode_' . $i);

	$ComboTarget = array();

	$Select
		= "SELECT * FROM Targets ORDER BY TarId ASC ";
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
		. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='1' "
		. "ORDER BY EvProgr ASC,EvCode ASC, EvTeamEvent ASC ";

	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)>0)
	{
		while ($MyRow=safe_fetch($Rs))
		{
			print '<tr id="Row_' . $MyRow->EvCode . '">';
			print '<td class="Center"><a class="Link" href="SetEventRules.php?EvCode=' . $MyRow->EvCode . '">' . $MyRow->EvCode . '</a></td>';

			print '<td class="Center"><input type="text" size="64" maxlength="64" name="d_EvEventName_' . $MyRow->EvCode . '" id="d_EvEventName_' . $MyRow->EvCode . '" value="' . $MyRow->EvEventName . '" onBlur="javascript:UpdateField(\'d_EvEventName_' . $MyRow->EvCode . '\');">';
			print '</td>';

			print '<td class="Center">';
			print '<input type="text" size="3" maxlength="3" name="d_EvProgr_' . $MyRow->EvCode . '" id="d_EvProgr_' . $MyRow->EvCode . '" value="' . $MyRow->EvProgr . '" onBlur="javascript:UpdateField(\'d_EvProgr_' . $MyRow->EvCode . '\');">';
			print '</td>';

			print '<td class="Center">';
			print '<select name="d_EvMatchMode_' . $MyRow->EvCode . '" id="d_EvMatchMode_' . $MyRow->EvCode . '" onChange="javascript:UpdateField(\'d_EvMatchMode_' . $MyRow->EvCode . '\');">' . "\n";
			foreach ($ComboMatchMode as $Key => $Value)
				print '<option value="' . $Key . '"' . ($Key==$MyRow->EvMatchMode ? ' selected' : '') . '>' . $Value . '</option>' . "\n";
			print '</select>' . "\n";
			print '</td>';


			print '<td class="Center">';
			print '<select name="d_EvFinalFirstPhase_' . $MyRow->EvCode . '" id="d_EvFinalFirstPhase_' . $MyRow->EvCode . '" onChange="javascript:UpdatePhase(\'' . $MyRow->EvCode . '\',' . $MyRow->EvFinalFirstPhase . ',\'' . get_text('MsgAreYouSure') . '\');">' . "\n";
			foreach ($ComboPhase as $Key => $Value)
			{
				print '<option value="' . $Key . '"' . ($Key==$MyRow->EvFinalFirstPhase ? ' selected' : '') . '>' . $Value . '</option>' . "\n";
			}
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
<tr id="RowDiv" class="Divider"><td colspan="9"></td></tr>
<tr id="NewRow">
<td class="Center"><input type="text" name="New_EvCode" id="New_EvCode" size="4" maxlength="4"></td>
<td class="Center"><input type="text" size="64" maxlength="64" name="New_EvEventName" id="New_EvEventName"></td>
<td class="Center"><input type="text" size="3" maxlength="3" name="New_EvProgr" id="New_EvProgr"></td>
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
</tbody>
</table>
<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');
?>