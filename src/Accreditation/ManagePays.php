<?php
/*
	Viene incluso il motore ajax di index per sfruttare UpdateField
*/
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
    checkACL(AclCompetition, AclReadWrite);
	require_once('Common/Fun_FormatText.inc.php');

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="../Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_ManagePays.js"></script>',
		'<script type="text/javascript" src="../Common/js/Fun_JS.inc.js"></script>',
		);

	include('Common/Templates/head.php');
?>
<table class="Tabella">
<tr><th class="Title" colspan="7"><?php print get_text('Pay','Tournament'); ?></th></tr>
<tr class="Divider"><td colspan="7"></td></tr>
<?php
	$Select
		= "SELECT EnId,EnCode,EnFirstName,EnName,EnTournament,EnSex,EnDivision,EnClass,CoCode,CoName,EnPays "
		. "FROM Entries LEFT JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament "
		. "WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1 ";

	$OrderBy = " EnFirstName ASC,EnName ASC ";

	if (isset($_REQUEST['ordCode']) && ($_REQUEST['ordCode']=='ASC' || $_REQUEST['ordCode']=='DESC'))
		$OrderBy = "EnCode " . $_REQUEST['ordCode'] . " ";
	elseif (isset($_REQUEST['ordName']) && ($_REQUEST['ordName']=='ASC' || $_REQUEST['ordName']=='DESC'))
		$OrderBy = "EnFirstName " . $_REQUEST['ordName'] . ",EnName " . $_REQUEST['ordName'] . " ";
	elseif (isset($_REQUEST['ordCountry']) && ($_REQUEST['ordCountry']=='ASC' || $_REQUEST['ordCountry']=='DESC'))
		$OrderBy = "CoCode " . $_REQUEST['ordCountry'] . " ";
	elseif (isset($_REQUEST['ordDiv']) && ($_REQUEST['ordDiv']=='ASC' || $_REQUEST['ordDiv']=='DESC'))
		$OrderBy = "EnDivision " . $_REQUEST['ordDiv'] . " ";
	elseif (isset($_REQUEST['ordCl']) && ($_REQUEST['ordCl']=='ASC' || $_REQUEST['ordCl']=='DESC'))
		$OrderBy = "EnClass " . $_REQUEST['ordCl'] . " ";

	$Select.="ORDER BY " . $OrderBy;

	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)>0)
	{
		print '<tr>';
		print '<td class="Title" width="6%"><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?ordCode=' . (isset($_REQUEST['ordCode']) ? ( $_REQUEST['ordCode']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('Code','Tournament') . '</a></td>'
			. '<td class="Title" width="20%"><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?ordName=' . (isset($_REQUEST['ordName']) ? ( $_REQUEST['ordName']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('Archer') . '</a></td>'
			. '<td class="Title" width="4%"><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?ordCountry=' . (isset($_REQUEST['ordCountry']) ? ($_REQUEST['ordCountry']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('Country') . '</a></td>'
			. '<td class="Title" width="18%"><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?ordCountry=' . (isset($_REQUEST['ordCountry']) ? ($_REQUEST['ordCountry']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('NationShort','Tournament') . '</a></td>'
			. '<td class="Title" width="4%"><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?ordDiv=' . (isset($_REQUEST['ordDiv']) ? ($_REQUEST['ordDiv']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('Div') . '</a></td>'
			. '<td class="Title" width="4%"><a class="Link" href="' . $_SERVER['PHP_SELF'] . '?ordCl=' . (isset($_REQUEST['ordCl']) ? ($_REQUEST['ordCl']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('Cl') . '</a></td>'
			. '<td class="Title" width="11%">' . get_text('Pay','Tournament') . '</td>';
		print '</tr>';

		$CurRow = 0;
		while ($MyRow=safe_fetch($Rs))
		{
			$ComboPay
				= '<select name="d_e_EnPays_' . $MyRow->EnId .  '" id="d_e_EnPays_' . $MyRow->EnId .  '" onChange="UpdateField(\'d_e_EnPays_' . $MyRow->EnId . '\');">'
				. '<option value="1"' . ($MyRow->EnPays==1 ? ' selected' : '') . '>' . get_text('Yes') . '</option>'
				. '<option value="0"' . ($MyRow->EnPays==0 ? ' selected' : '') . '>' . get_text('No') . '</option>'
				. '</select>';
?>
<tr <?php print 'id="Row_' . $MyRow->EnId . '" ' . ($CurRow++ % 2 ? ' class="OtherColor"' : '');?>>
<td><?php print ($MyRow->EnCode!='' ? $MyRow->EnCode : '&nbsp;'); ?></td>
<td><?php print ($MyRow->EnFirstName . $MyRow->EnName !='' ? $MyRow->EnFirstName . ' ' . $MyRow->EnName : '&nbsp;'); ?></td>
<td><?php print ($MyRow->CoCode!='' ? $MyRow->CoCode : '&nbsp;'); ?></td>
<td><?php print ($MyRow->CoName!='' ? $MyRow->CoName : '&nbsp;'); ?></td>
<td class="Center"><?php print ($MyRow->EnDivision!='' ? $MyRow->EnDivision : '&nbsp;')?></td>
<td class="Center"><?php print ($MyRow->EnClass!='' ? $MyRow->EnClass : '&nbsp;')?></td>
<td class="Center" title="<?php print get_text('Pay','Tournament'); ?>"><?php print $ComboPay; ?></td>
</tr>
<?php
		}
	}
?>
</table>
<?php
	include('Common/Templates/tail.php');
?>