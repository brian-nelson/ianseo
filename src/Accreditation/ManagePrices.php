<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
    checkACL(AclCompetition, AclReadWrite);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Number.inc.php');

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="../Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_ManagePrices.js"></script>',
		'<script type="text/javascript" src="../Common/js/Fun_JS.inc.js"></script>',
		);

	include('Common/Templates/head.php');
?>
<div id="idOutput"></div>
<div align="center">
<div class="medium">
<table class="Tabella" id="MyTable">
<tbody id="tbody">
<tr><th class="Title" colspan="4"><?php print get_text('ManPrices','Tournament');?></th></tr>
<tr class="Divider"><td colspan="4"></td></tr>
<tr>
<th width="50%" colspan="2"><?php print get_text('Division');?> - <?php print get_text('Class');?></th>
<th width="25%"><?php print get_text('Price','Tournament');?></th>
<th width="25%">&nbsp;</th>
</tr>
<?php
	$ComboDiv
		= '<select name="New_Division" id="New_Division" multiple="multiple">' . "\n";
		//. '<option value="">--</option>' . "\n";
	$Select
		= "SELECT * "
		. "FROM Divisions "
		. "WHERE DivTournament = " . StrSafe_DB($_SESSION['TourId']) . " "
		. "ORDER BY DivViewOrder ASC ";
	$RsSel = safe_r_sql($Select);
	if (safe_num_rows($RsSel)>0)
	{
		while ($Row=safe_fetch($RsSel))
			$ComboDiv.= '<option value="' . $Row->DivId . '">' . $Row->DivId . '</option>' . "\n";
	}
	$ComboDiv.= '</select>' . "\n";

	$ComboCl
		= '<select name="New_Class" id="New_Class" multiple="multiple">' . "\n";
		//. '<option value="">--</option>' . "\n";
	$Select
		= "SELECT * "
		. "FROM Classes "
		. "WHERE ClTournament = " . StrSafe_DB($_SESSION['TourId']) . " "
		. "ORDER BY ClViewOrder ASC ";
	$RsSel = safe_r_sql($Select);
	if (safe_num_rows($RsSel)>0)
	{
		while ($Row=safe_fetch($RsSel))
			$ComboCl.= '<option value="' . $Row->ClId . '">' . $Row->ClId . '</option>' . "\n";
	}
	$ComboCl.= '</select>' . "\n";

// qui l'elenco dei prezzi
	$Select
		= "SELECT AccPrice.*,ToCurrency FROM AccPrice "
		. "LEFT JOIN Tournament on ToId=APTournament "
		. "WHERE APTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "ORDER BY APId ASC ";
	$Rs=safe_r_sql($Select);
//	print $Select;
	if (safe_num_rows($Rs)>0)
	{
		while ($MyRow=safe_fetch($Rs))
		{
			print '<tr id="Row_' . $MyRow->APDivClass . '">';
			print '<td class="Center" colspan="2">' . $MyRow->APDivClass . '</td>';
			print '<td class="Right">' . NumFormat($MyRow->APPrice,2) . '&nbsp;' . $MyRow->ToCurrency . '</td>';
			print '<td class="Center"><a href="javascript:DeletePrice(\'' . $MyRow->APDivClass . '\');"><img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" border="0" alt="#" title="#"></a></td>';
			print '</tr>' . "\n";
		}
	}

	print '<tr id="RowDiv" class="Divider"><td colspan="4"></td></tr>' . "\n";
	print '<tr><td colspan="4" class="Center">' . get_text('PressCtrl2SelectAll') . '</td></tr>' . "\n";
	print '<tr id="NewRow">' . "\n";
	print '<td class="Center" valign="top">' . $ComboDiv . '<br><br><a class="Link" href="javascript:SelectAllOpt(\'New_Division\');">' . get_text('SelectAll') . '</a></td>' . "\n";
	print '<td class="Center" valign="top">' . $ComboCl . '<br><br><a class="Link" href="javascript:SelectAllOpt(\'New_Class\');">' . get_text('SelectAll') . '</a></td>' . "\n";
	print '<td class="Center" valign="top"><input type="text" name="New_Price" id="New_Price" size="5" maxlength="10"></td>';
	print '<td class="Center" valign="top">';
	print '<input type="button" name="Command" id="Command" value="' . get_text('CmdSave') . '" onclick="javascript:AddPrice();">';
	print '</td>';
	print '</tr>' . "\n";

?>
</tbody>
</table>
</div>
</div>
<?php
	include('Common/Templates/tail.php');
?>