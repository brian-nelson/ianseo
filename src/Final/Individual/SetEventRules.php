<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	if (!CheckTourSession() || !isset($_REQUEST['EvCode'])) printCrackError();
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');


	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Individual/Fun_AJAX_SetEventRules.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Individual/Fun_JS.js"></script>',
		);

	include('Common/Templates/head.php');
?>
<div align="center">
<div class="medium">
<table class="Tabella" id="MyTable">
<tbody id="tbody">
<tr><th class="Title" colspan="7"><?php print get_text('EventClass');?></th></tr>
<tr class="Divider"><td colspan="3"></td></tr>
<?php
	$Select
		= "SELECT EvCode,EvEventName,EvElimEnds,EvElimArrows,EvElimSO,EvFinEnds,EvFinArrows,EvFinSO "
		. "FROM Events "
		. "WHERE EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$RsEv = safe_r_sql($Select);

	if (safe_num_rows($RsEv)==1)
	{
		$RowEv=safe_fetch($RsEv);
?>
<tr><td class="Title" colspan="3"><?php print get_text($RowEv->EvEventName,'','',true);?></td></tr>
<tr>
<th width="33%"><?php print get_text('Division');?></th>
<th width="34%"><?php print get_text('Class');?></th>
<th width="33%">&nbsp;</th>
</tr>
<?php
		$ComboDiv
			= '<select name="New_EcDivision" id="New_EcDivision" multiple="multiple">' . "\n";
			//. '<option value="">--</option>' . "\n";
		$Select
			= "SELECT * "
			. "FROM Divisions "
			. "WHERE DivTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND DivAthlete=1 "
			. "ORDER BY DivViewOrder ASC ";
		$RsSel = safe_r_sql($Select);
		if (safe_num_rows($RsSel)>0)
		{
			while ($Row=safe_fetch($RsSel))
				$ComboDiv.= '<option value="' . $Row->DivId . '">' . $Row->DivId . '</option>' . "\n";
		}
		$ComboDiv.= '</select>' . "\n";

		$ComboCl
			= '<select name="New_EcClass" id="New_EcClass" multiple="multiple">' . "\n";
			//. '<option value="">--</option>' . "\n";
		$Select
			= "SELECT * "
			. "FROM Classes "
			. "WHERE ClTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND ClAthlete=1 "
			. "ORDER BY ClViewOrder ASC ";
		$RsSel = safe_r_sql($Select);
		if (safe_num_rows($RsSel)>0)
		{
			while ($Row=safe_fetch($RsSel))
				$ComboCl.= '<option value="' . $Row->ClId . '">' . $Row->ClId . '</option>' . "\n";
		}
		$ComboCl.= '</select>' . "\n";


		$Select
			= "SELECT * FROM EventClass "
			. "WHERE EcCode=" . StrSafe_DB($RowEv->EvCode) . " AND EcTeamEvent='0' AND EcTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "ORDER BY EcDivision,EcClass ";
		$Rs=safe_r_sql($Select);

		if (safe_num_rows($Rs)>0)
		{
			while ($MyRow=safe_fetch($Rs))
			{
				print '<tr id="Row_' . $RowEv->EvCode . '_' . $MyRow->EcDivision . $MyRow->EcClass . '">';
				print '<td class="Center">' . $MyRow->EcDivision . '</td>';
				print '<td class="Center">' . $MyRow->EcClass . '</td>';
				print '<td class="Center">';
				print '<a href="javascript:DeleteEventRule(\'' . $RowEv->EvCode . '\',\'' . $MyRow->EcDivision . '\',\'' . $MyRow->EcClass . '\');"><img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" border="0" alt="#" title="#"></a>';
				print '</td>';
				print '</tr>' . "\n";
			}
		}

		print '<tr id="RowDiv" class="Divider"><td colspan="3"></td></tr>' . "\n";
	}
?>
</tbody>
</table>
<br/>

<table class="Tabella">
	<tr><td colspan="3" class="Center"><?php print get_text('PressCtrl2SelectAll');?></td></tr>
	<tr id="NewRow">
		<td style="width:33%;" class="Center" valign="top"><?php print $ComboDiv;?><br><br><a class="Link" href="javascript:SelectAllOpt('New_EcDivision');"><?php print get_text('SelectAll');?></a></td>
		<td style="width:34%;" class="Center" valign="top"><?php print $ComboCl;?><br><br><a class="Link" href="javascript:SelectAllOpt('New_EcClass');"><?php print get_text('SelectAll');?></a></td>
		<td style="width:33%;" class="Center" valign="top">
			<input type="button" name="Command" id="Command" value="<?php print get_text('CmdSave');?>" onclick="javascript:AddEventRule('<?php print $RowEv->EvCode;?>');">
		</td>
	</tr>
</table>
<table class="Tabella">
<tr><td class="Center"><a class="Link" href="ListEvents.php"><?php echo get_text('Back') ?></a></td></tr>
</table>
<div id="idOutput"></div>
</div>
</div>
<?php
	include('Common/Templates/tail.php');
?>