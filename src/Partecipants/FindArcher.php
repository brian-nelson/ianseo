<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	if (!CheckTourSession() or !isset($_REQUEST['Id'])) printCrackerror('popup');
    checkACL(AclParticipants, AclReadOnly);

	require_once('Common/Fun_FormatText.inc.php');
	require_once('Partecipants/Fun_Partecipants.local.inc.php');

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Partecipants/Fun_AJAX_FindArcher.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Partecipants/Fun_JS.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
		);

	include('Common/Templates/head-popup.php');

?>
<form name="Frm" method="GET" action="">
<input type="hidden" name="Id" id="Id" value="<?php print $_REQUEST['Id']; ?>">
<!--<table class="Tabella">
<tr><th class="Title" colspan="12"><?php //print get_text('FindArcher','Tournament');?></th></tr>
<tr class="Divider"><td></td></tr>
<tr>
<th class="TitleLeft" width="15%"><?php //print get_text('Code','Tournament'); ?></th>
<td><input type="text" name="d_e_EnCode" id="d_e_EnCode" onKeyup="javascript:SearchArcher();"></td>
</tr>
<tr>
<th class="TitleLeft" width="15%"><?php // print get_text('FamilyName','Tournament'); ?></th>
<td><input type="text" name="d_e_EnFirstName" id="d_e_EnFirstName" onKeyup="javascript:SearchArcher();"></td>
</tr>
<tr>
<th class="TitleLeft" width="15%"><?php //print get_text('Name','Tournament'); ?></th>
<td><input type="text" name="d_e_EnName" id="d_e_EnName" onKeyup="javascript:SearchArcher();"></td>
</tr>
<tr>
<th class="TitleLeft" width="15%"><?php //print get_text('Country'); ?></th>
<td><input type="text" name="d_c_CoCode" id="d_c_CoCode" onKeyup="javascript:SearchArcher();"></td>
</tr>
</table>-->
<table class="Tabella">
<tr><th class="Title" colspan="7"><?php print get_text('FindArcher','Tournament');?></th></tr>
<tr class="Divider"><td></td></tr>
<tr>
<td class="Title" width="10%"><?php print get_text('Code','Tournament');?></td>
<td class="Title" width="30%"><?php print get_text('Archer');?></td>
<td class="Title" width="30%"><?php print get_text('Country');?></td>
<td class="Title" width="10%"><?php print get_text('Div');?></td>
<td class="Title" width="10%"><?php echo get_text('AgeCl') ?></td>
<td class="Title" width="10%"><?php print get_text('SubCl','Tournament');?></td>
</tr>
<tr>
<td class="Center"><input type="text" name="d_e_EnCode" id="d_e_EnCode" size="9" onKeyup="javascript:SearchArcher();"></td>
<td class="Center"><input type="text" name="d_e_Archer" id="d_e_Archer" size="30" onKeyup="javascript:SearchArcher();"></td>
<td class="Center"><input type="text" name="d_c_CoCode" id="d_c_CoCode" size="30" onKeyup="javascript:SearchArcher();"></td>
<td class="Center">
<?php
	$Select
		= "SELECT * FROM Divisions WHERE DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY DivViewOrder ASC ";
	$Rs=safe_r_sql($Select);
	print '<select name="d_e_EnDivision" id="d_e_EnDivision" onChange="javascript:SearchArcher();">';
	print '<option value="">--</option>';
	if (safe_num_rows($Rs)>0)
	{
		while ($MyRow=safe_fetch($Rs))
			print '<option value="' . $MyRow->DivId . '">' . $MyRow->DivId . '</option>';
	}
	print '</select>';
?>
</td>
<td class="Center">
<?php
	$Select
		= "SELECT * FROM Classes WHERE ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY ClViewOrder ASC ";
	$Rs=safe_r_sql($Select);
	print '<select name="d_e_EnClass" id="d_e_EnClass" onChange="javascript:SearchArcher();">';
	print '<option value="">--</option>';
	if (safe_num_rows($Rs)>0)
	{
		while ($MyRow=safe_fetch($Rs))
			print '<option value="' . $MyRow->ClId . '">' . $MyRow->ClId . '</option>';
	}
	print '</select>';
?>
</td>
<td class="Center">
<?php
	$Select
		= "SELECT * FROM SubClass WHERE ScTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY SCViewOrder ASC ";
	$Rs=safe_r_sql($Select);
	print '<select name="d_e_EnSubClass" id="d_e_EnSubClass" onChange="javascript:SearchArcher();">';
	print '<option value="">--</option>';
	if (safe_num_rows($Rs)>0)
	{
		while ($MyRow=safe_fetch($Rs))
			print '<option value="' . substr($MyRow->ScId,1) . '">' . $MyRow->ScId . '</option>';
	}
	print '</select>';
?>
</td>

</tr>
</table>
</form>
<br>
<table class="Tabella" id="idResults">
<tbody>
</tbody>
</table>
<table class="Tabella">
<tr><td class="Center"><a class="Link" href="javascript:window.close();"><?php print get_text('Close');?></a></td></tr>
</table>
<div id="idOutput">	</div>
<?php include('Common/Templates/tail-popup.php'); ?>