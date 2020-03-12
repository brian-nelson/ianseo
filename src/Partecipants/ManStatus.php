<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
    checkACL(AclParticipants, AclReadWrite);
	require_once('Common/Fun_FormatText.inc.php');

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="../Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_ManStatus.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_index.js"></script>',
		'<script type="text/javascript" src="../Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="Fun_JS.js"></script>',
		);

	$PAGE_TITLE=get_text('ManAthStatus','Tournament');

	include('Common/Templates/head.php');
?>
	<table class="Tabella">
	<tr><th class="Title" colspan="7"><?php print get_text('ManAthStatus','Tournament'); ?></th></tr>
	<tr class="Divider"><td colspan="7"></td></tr>
	<tr class="Divider"><td colspan="7"></td></tr>
	<tr><td colspan="7" class="Bold"><input type="checkbox" name="chk_BlockAutoSave" id="chk_BlockAutoSave" value="1"><?php echo get_text('CmdBlocAutoSave') ?></td></tr>
	<tr class="Divider"><td colspan="7"></td></tr>
<?php
	$Select
		= "SELECT EnId,EnCode,EnFirstName,EnName,EnTournament,EnSex,EnStatus,CoCode,CoName "
		. "FROM Entries LEFT JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament "
		. "WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1 AND (EnStatus=1 OR EnStatus=5 OR EnStatus=8) "
		. "ORDER BY EnFirstName ASC,EnName ASC ";
	$Rs=safe_r_sql($Select);
	if (debug)
		print $Select . '<br><br>';
	if (safe_num_rows($Rs)>0)
	{
		print '<tr>';
		print '<td class="Title" width="6%">' . get_text('Code','Tournament') . '</td>'
			. '<td class="Title" width="28%">' . get_text('FamilyName','Tournament') . '</td>'
			. '<td class="Title" width="28%">' . get_text('Name','Tournament') . '</td>'
			. '<td class="Title" width="3%">' . get_text('Sex','Tournament') . '</td>'
			. '<td class="Title" width="4%">' . get_text('Country') . '</td>'
			. '<td class="Title" width="11%">' . get_text('NationShort','Tournament') . '</td>'
			. '<td class="Title" width="11%">' . get_text('Status','Tournament') . '</td>';
		print '</tr>';

		while ($MyRow=safe_fetch($Rs))
		{
			$RowStyle='';
			switch ($MyRow->EnStatus)
			{
				case 0:
					$RowStyle = '';
					break;
				case 1:
					$RowStyle = 'CanShoot';
					break;
				case 5:
					$RowStyle = 'UnknownShoot';
					break;
				case 8:
					$RowStyle = 'CouldShoot';
					break;
				case 9:
					$RowStyle = 'NoShoot';
					break;
			}

			$ComboStatus
				 = '<select name="d_e_EnStatus_' . $MyRow->EnId . '" id="d_e_EnStatus_' . $MyRow->EnId . '" onChange="javascript:UpdateStatus(\'d_e_EnStatus_' . $MyRow->EnId . '\');">'
				 . '<option value="1"' . ($MyRow->EnStatus==1 ? ' selected' : '') . '>' . get_text('Status_1') . '</option>'
				 . '<option value="5"' . ($MyRow->EnStatus==5 ? ' selected' : '') . '>' . get_text('Status_5') . '</option>'
				 . '<option value="8"' . ($MyRow->EnStatus==8 ? ' selected' : '') . '>' . get_text('Status_8') . '</option>'
				 . '</select>';
?>
<tr <?php print 'id="Row_' . $MyRow->EnId . '" class="' . $RowStyle . '"';?>>
<td><?php print ($MyRow->EnCode!='' ? $MyRow->EnCode : '&nbsp;'); ?></td>
<td><?php print ($MyRow->EnFirstName!='' ? $MyRow->EnFirstName : '&nbsp;'); ?></td>
<td><?php print ($MyRow->EnName!='' ? $MyRow->EnName : ''); ?></td>
<td><?php print ($MyRow->EnSex==0 ? 'M' : 'F'); ?></td>
<td><?php print ($MyRow->CoCode!='' ? $MyRow->CoCode : '&nbsp'); ?></td>
<td><?php print ($MyRow->CoName!='' ? $MyRow->CoName : '&nbsp;'); ?></td>
<td><?php print $ComboStatus; ?></td>
</tr>
<?php
		}
	}
	else
	{
?>
<tr><th class="Title"><?php print get_text('NoAth2Manage','Tournament'); ?></th></tr>
<?php
	}
?>
</table>
<table class="Tabella">
<tr><td class="Bold Center"><?php print get_text('LegendStatus','Tournament'); ?></td></tr>
<tr class="CanShoot"><td><?php print get_text('Status_1'); ?></td></tr>
<tr class="UnknownShoot"><td><?php print get_text('Status_5'); ?></td></tr>
<tr class="CouldShoot"><td><?php print get_text('Status_8'); ?></td></tr>
</table>
<div id="idOutput"></div>
<script type="text/javascript">GetStatus('');</script>
<?php
	include('Common/Templates/tail.php');
?>