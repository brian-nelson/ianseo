<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/Fun_DateTime.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Tournament/Fun_Tournament.local.inc.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	if (isset($_REQUEST['Command']))
	{
		if ($_REQUEST['Command']=='ADD')
		{
			$Insert
				= "INSERT INTO TournamentInvolved (TiTournament,TiType,TiCode,TiName) "
				. "VALUES("
				. StrSafe_DB($_SESSION['TourId']) . ","
				. StrSafe_DB($_REQUEST['new_Type']) . ","
				. StrSafe_DB($_REQUEST['new_Matr']) . ","
				. StrSafe_DB($_REQUEST['new_Name']) . " "
				. ") ";
			$RsIns=safe_w_sql($Insert);
		}
		elseif ($_REQUEST['Command']=='DELETE')
		{
			if (isset($_REQUEST['IdDel']))
			{
				$Delete
					= "DELETE FROM TournamentInvolved WHERE TiId=" . StrSafe_DB($_REQUEST['IdDel']) . " ";
				$RsDel = safe_w_sql($Delete);

				header('Location: ' . $_SERVER['PHP_SELF']);
				exit;
			}
		}
	}

	$TypeOptions = array
	(
		0 => '------'
	);

	$Sel="
		SELECT
			*
		FROM
			InvolvedType
		ORDER BY
			IF(ItJudge!=0,1,IF(ItDoS!=0,2,IF(ItJury!=0,3,4))) ASC, IF(ItJudge!=0,ItJudge,IF(ItDoS!=0,ItDoS,IF(ItJury!=0,ItJury,ItOC))) ASC
	";
		//print $Sel;exit;
	$RsSel = safe_r_sql($Sel);

	if (safe_num_rows($RsSel)>0)
	{
		while ($RowSel = safe_fetch($RsSel))
		{
			//$Attr = ($RowSel->ItJudge==1 ? 'J' : '') . ($RowSel->ItDoS==1 ? 'D' : '');
			$TypeOptions[$RowSel->ItId] = get_text($RowSel->ItDescription,'Tournament');// .  (strlen($Attr)>0 ? ' [' . $Attr . ']' : '');
		}
	}

	$JS_SCRIPT = array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Tournament/Fun_JS.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Tournament/Fun_AJAX_ManStaffField.js"></script>',
		);

	$PAGE_TITLE=get_text('StaffOnField','Tournament');

	include('Common/Templates/head.php');
?>
<form name="Frm" method="post" action="">
<input type="hidden" name="Command" value="">
<table class="Tabella">
<tr><th class="Title" colspan="4"><?php print get_text('StaffOnField','Tournament'); ?></th></tr>
<tr class="Divider"><td colspan="4"></td></tr>
<tr>
<th width="10%"><?php print get_text('Code','Tournament'); ?></th>
<th width="40%"><?php print get_text('Name','Tournament'); ?></th>
<th width="30%"><?php print get_text('Type','Tournament'); ?></th>
<th>&nbsp;</th>
</tr>
<tr>
<td class="Center">
<input type="text" name="new_Matr" id="new_Matr" maxlength="9" size="9" onKeyup="javascript:CercaMatr('new_Matr','new_Name');">
</td>
<td class="Center">
<input type="text" name="new_Name" id="new_Name" maxlength="64" size="64">
</td>
<td class="Center">
<select name="new_Type">
<?php
	foreach ($TypeOptions as $Key => $Value)
		print '<option value="' . $Key . '">' . $Value . '</option>' . "\n";
?>
</select>
</td>
<td class="Center">
<input type="submit" value="<?php print get_text('CmdAdd','Tournament');?>" onclick="document.Frm.Command.value='ADD'">&nbsp;&nbsp;
<input type="reset" value="<?php print get_text('CmdCancel');?>">
</td>
</tr>
</table>
<br>
<table class="Tabella">
<tr><th class="Title" colspan="4"><?php print get_text('PersonList','Tournament'); ?></th></tr>
<tr class="Divider"><td colspan="4"></td></tr>
<tr>
<th width="10%"><?php print get_text('Code','Tournament'); ?></th>
<th width="40%"><?php print get_text('Name','Tournament')?></th>
<th width="30%"><?php print get_text('Type','Tournament'); ?></th>
<th>&nbsp;</th>
</tr>
<?php
	$Select
		= "SELECT ti.*, it.*"
		. "FROM TournamentInvolved AS ti LEFT JOIN InvolvedType AS it ON ti.TiType=it.ItId "
		. "WHERE ti.TiTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "ORDER BY IF(it.ItId IS NOT NULL,IF(ItJudge!=0,1,IF(ItDoS!=0,2,IF(ItJury!=0,3,4))),9999) ASC, IF(it.ItId IS NOT NULL,IF(ItJudge!=0,ItJudge,IF(ItDoS!=0,ItDoS,IF(ItJury!=0,ItJury,ItOC))),9999) ASC,ti.TiName ASC ";
		//. "ORDER BY ti.TiName ASC ";

		//print $Select;  exit;

	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)>0)
	{
		while ($MyRow=safe_fetch($Rs))
		{
			print '<tr>';
			print '<td class="Center">';
			print '<input type="text" maxlength="9" size="9" name="d_TiMatr_' . $MyRow->TiId . '" id="d_TiMatr_' . $MyRow->TiId . '" value="' . $MyRow->TiCode . '" onKeyup="javascript:CercaMatr(\'d_TiMatr_' . $MyRow->TiId . '\',\'d_TiName_' . $MyRow->TiId . '\');" onBlur="javascript:EditMatr(' . $MyRow->TiId . ');">';
			print '</td>';

			print '<td class="Center">';
			print '<input type="text" maxlength="64" size="64" name="d_TiName_' . $MyRow->TiId . '" id="d_TiName_' . $MyRow->TiId . '" value="' . $MyRow->TiName . '" onBlur="javascript:EditMatr(' . $MyRow->TiId . ');">';
			print '</td>';

			print '<td class="Center">';
			print '<select name="d_TiType_' . $MyRow->TiId . '" id="d_TiType_' . $MyRow->TiId . '" onChange="javascript:EditMatr(' . $MyRow->TiId . ');">' . "\n";
			foreach ($TypeOptions as $Key => $Value)
			{
				print '<option value="' . $Key . '"' . ($MyRow->TiType==$Key ? ' selected' : '') . '>' . $Value . '</option>' . "\n";
			}
			print '</select>' . "\n";
			print '</td>';

			print '<td class="Center">';
			print '<input type="button" value="' . get_text('CmdDelete','Tournament') . '" onClick="javascript:DeleteId(\'' . $MyRow->TiId . '\',\'' . get_text('MsgAreYouSure') . '\');">';
			print '</td>';
			print '</tr>' . "\n";
		}
	}
?>
</table>
</form>

<form name="Frm2" method="get" target="PrnStaffField" action="PrnStaffField.php">
	<table class="Tabella">
		<tr><th class="Title" colspan="4"><?php print get_text('PrintList','Tournament'); ?></th></tr>
		<tr class="Divider"><td colspan="4"></td></tr>
		<tr>
			<td style="width:35%;">&nbsp;</td>
			<td style="width:15%;">
				<input type="checkbox" name="judge" value="1" checked/><?php print get_text('CatJudge','Tournament');?><br/>
				<input type="checkbox" name="dos" value="1" checked/><?php print get_text('CatDos','Tournament');?><br/>
				<input type="checkbox" name="jury" value="1" checked/><?php print get_text('CatJury','Tournament');?><br/>
				<input type="checkbox" name="oc" value="1" checked/><?php print get_text('CatOC','Tournament');?><br/>
			</td>
			<td class="Center" style="width:15%;">
				<input type="submit" name="Command" value="<?php print get_text('CmdOk');?>"/>
			</td>
			<td style="width:35%;">&nbsp;</td>

		</tr>
	</table>
</form>

<br>
<table class="Tabella">
	<tr><td class="Center">
		<a class="Link" href="index.php"><?php echo get_text('Back') ?></a>
	</td></tr>
</table>
<?php
	include('Common/Templates/tail.php');

	/*
	 * <tr><td class="Center" colspan="4">
<br>
<a class="Link" href="index.php"><?php echo get_text('Back') ?></a>
</td></tr>
</table>
	 */
?>