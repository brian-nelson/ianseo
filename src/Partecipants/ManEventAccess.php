<?php
/*
	Viene incluso il motore ajax di index per sfruttare UpdateField
*/
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="../Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_ManStatus.js"></script>',
		'<script type="text/javascript" src="../Common/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_index.js"></script>',
		);

	$PAGE_TITLE=get_text('EventAccess','Tournament');

	include('Common/Templates/head.php');
?>
<table class="Tabella">
<tr><th class="Title" colspan="13"><?php print get_text('EventAccess','Tournament'); ?></th></tr>
<tr class="Divider"><td colspan="13"></td></tr>
<tr class="Divider"><td colspan="13"></td></tr>
<tr><td colspan="13" class="Bold"><input type="checkbox" name="chk_BlockAutoSave" id="chk_BlockAutoSave" value="1"><?php echo get_text('CmdBlocAutoSave') ?></td></tr>
<tr class="Divider"><td colspan="13"></td></tr>
<?php
	$Select
		= "SELECT EnId,"
		. "EnCode,"
		. "EnFirstName,"
		. "EnName,"
		. "EnTournament,"
		. "EnSex,"
		. "EnDivision,"
		. "EnClass,"
		. "CoCode,"
		. "CoName,"
		. "EnIndClEvent,"
		. "EnTeamClEvent,"
		. "EnIndFEvent,"
		. "EnTeamFEvent,"
		. "EnTeamMixEvent,"
		. "EnWChair, "
		. "EnDoubleSpace "
		. "FROM Entries LEFT JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament "
		. "WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1 ";

	$OrderBy = " EnFirstName ASC,EnName ASC ";

	if (isset($_REQUEST['ordCode']) && ($_REQUEST['ordCode']=='ASC' || $_REQUEST['ordCode']=='DESC'))
		$OrderBy = "EnCode " . $_REQUEST['ordCode'] . " ";
	elseif (isset($_REQUEST['ordName']) && ($_REQUEST['ordName']=='ASC' || $_REQUEST['ordName']=='DESC'))
		$OrderBy = "EnFirstName " . $_REQUEST['ordName'] . ",EnName " . $_REQUEST['ordName'] . " ";
	elseif (isset($_REQUEST['ordCountry']) && ($_REQUEST['ordCountry']=='ASC' || $_REQUEST['ordCountry']=='DESC'))
		$OrderBy = "CoCode " . $_REQUEST['ordCountry'] . ", EnFirstName ASC, EnName ASC ";
	elseif (isset($_REQUEST['ordDiv']) && ($_REQUEST['ordDiv']=='ASC' || $_REQUEST['ordDiv']=='DESC'))
		$OrderBy = "EnDivision " . $_REQUEST['ordDiv'] . ", EnFirstName ASC, EnName ASC  ";
	elseif (isset($_REQUEST['ordCl']) && ($_REQUEST['ordCl']=='ASC' || $_REQUEST['ordCl']=='DESC'))
		$OrderBy = "EnClass " . $_REQUEST['ordCl'] . ", EnFirstName ASC, EnName ASC  ";
	elseif (isset($_REQUEST['ordIn']) && ($_REQUEST['ordIn']=='ASC' || $_REQUEST['ordIn']=='DESC'))
		$OrderBy = "EnIndClEvent " . $_REQUEST['ordIn'] . ", EnFirstName ASC, EnName ASC  ";
	elseif (isset($_REQUEST['ordFn']) && ($_REQUEST['ordFn']=='ASC' || $_REQUEST['ordFn']=='DESC'))
		$OrderBy = "EnIndFEvent " . $_REQUEST['ordFn'] . ", EnFirstName ASC, EnName ASC  ";
	elseif (isset($_REQUEST['ordTm']) && ($_REQUEST['ordTm']=='ASC' || $_REQUEST['ordTm']=='DESC'))
		$OrderBy = "EnTeamClEvent " . $_REQUEST['ordTm'] . ", EnFirstName ASC, EnName ASC  ";
	elseif (isset($_REQUEST['ordFt']) && ($_REQUEST['ordFt']=='ASC' || $_REQUEST['ordFt']=='DESC'))
		$OrderBy = "EnTeamFEvent " . $_REQUEST['ordFt'] . ", EnFirstName ASC, EnName ASC  ";
	elseif (isset($_REQUEST['ordMx']) && ($_REQUEST['ordMx']=='ASC' || $_REQUEST['ordMx']=='DESC'))
		$OrderBy = "EnTeamMixEvent " . $_REQUEST['ordMx'] . ", EnFirstName ASC, EnName ASC  ";
	elseif (isset($_REQUEST['ordWc']) && ($_REQUEST['ordWc']=='ASC' || $_REQUEST['ordWc']=='DESC'))
		$OrderBy = "EnWChair " . $_REQUEST['ordWc'] . ", EnFirstName ASC, EnName ASC  ";
	elseif (isset($_REQUEST['ordXb']) && ($_REQUEST['ordXb']=='ASC' || $_REQUEST['ordXb']=='DESC'))
		$OrderBy = "EnDoubleSpace " . $_REQUEST['ordXb'] . ", EnFirstName ASC, EnName ASC  ";

	$Select.="ORDER BY " . $OrderBy;

	$Rs=safe_r_sql($Select);

	if (debug)
		print $Select . '<br><br>';
	if (safe_num_rows($Rs)>0)
	{
		print '<tr>';
		print '<td class="Title" width="6%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordCode=' . (isset($_REQUEST['ordCode']) ? ( $_REQUEST['ordCode']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('Code','Tournament') . '</a></td>'
			. '<td class="Title" width="19%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordName=' . (isset($_REQUEST['ordName']) ? ( $_REQUEST['ordName']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('Archer') . '</a></td>'
			. '<td class="Title" width="4%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordCountry=' . (isset($_REQUEST['ordCountry']) ? ($_REQUEST['ordCountry']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('Country') . '</a></td>'
			. '<td class="Title" width="17%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordCountry=' . (isset($_REQUEST['ordCountry']) ? ($_REQUEST['ordCountry']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('NationShort','Tournament') . '</a></td>'
			. '<td class="Title" width="4%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordDiv=' . (isset($_REQUEST['ordDiv']) ? ($_REQUEST['ordDiv']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('Div') . '</a></td>'
			. '<td class="Title" width="4%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordCl=' . (isset($_REQUEST['ordCl']) ? ($_REQUEST['ordCl']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('Cl') . '</a></td>'
			. '<td class="Title" width="8%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordIn=' . (isset($_REQUEST['ordIn']) ? ($_REQUEST['ordIn']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('IndClEvent', 'Tournament') . '</a></td>'
			. '<td class="Title" width="8%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordFn=' . (isset($_REQUEST['ordFn']) ? ($_REQUEST['ordFn']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('IndFinEvent', 'Tournament') . '</a></td>'
			. '<td class="Title" width="8%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordTm=' . (isset($_REQUEST['ordTm']) ? ($_REQUEST['ordTm']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('TeamClEvent', 'Tournament') . '</a></td>'
			. '<td class="Title" width="8%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordFt=' . (isset($_REQUEST['ordFt']) ? ($_REQUEST['ordFt']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('TeamFinEvent', 'Tournament') . '</a></td>'
			. '<td class="Title" width="8%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordMx=' . (isset($_REQUEST['ordMx']) ? ($_REQUEST['ordMx']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('MixedTeamFinEvent', 'Tournament') . '</a></td>'
			. '<td class="Title" width="8%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordWc=' . (isset($_REQUEST['ordWc']) ? ($_REQUEST['ordWc']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('WheelChair', 'Tournament') . '</a></td>'
			. '<td class="Title" width="8%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . '?ordXb=' . (isset($_REQUEST['ordXb']) ? ($_REQUEST['ordXb']=='ASC' ? 'DESC' : 'ASC') : 'ASC') . '">' . get_text('DoubleSpace', 'Tournament') . '</a></td>';
			print '</tr>' . "\n";

		$CurRow = 0;
		while ($MyRow=safe_fetch($Rs))
		{
			$ComboIndCl
				= '<select name="d_e_EnIndClEvent_' . $MyRow->EnId .  '" id="d_e_EnIndClEvent_' . $MyRow->EnId .  '" onChange="UpdateField(\'d_e_EnIndClEvent_' . $MyRow->EnId . '\');">' . "\n"
				. '<option value="1"' . ($MyRow->EnIndClEvent==1 ? ' selected' : '') . '>' . get_text('Yes') . '</option>' . "\n"
				. '<option value="0"' . ($MyRow->EnIndClEvent==0 ? ' selected' : '') . '>' . get_text('No') . '</option>' . "\n"
				. '</select>' . "\n";
			$ComboTeamCl
				= '<select name="d_e_EnTeamClEvent_' . $MyRow->EnId .  '" id="d_e_EnTeamClEvent_' . $MyRow->EnId .  '" onChange="UpdateField(\'d_e_EnTeamClEvent_' . $MyRow->EnId . '\');">' . "\n"
				. '<option value="1"' . ($MyRow->EnTeamClEvent==1 ? ' selected' : '') . '>' . get_text('Yes') . '</option>' . "\n"
				. '<option value="0"' . ($MyRow->EnTeamClEvent==0 ? ' selected' : '') . '>' . get_text('No') . '</option>' . "\n"
				. '</select>' . "\n";
			$ComboIndFin
				= '<select name="d_e_EnIndFEvent_' . $MyRow->EnId .  '" id="d_e_EnIndFEvent_' . $MyRow->EnId .  '" onChange="UpdateField(\'d_e_EnIndFEvent_' . $MyRow->EnId . '\');">' . "\n"
				. '<option value="1"' . ($MyRow->EnIndFEvent==1 ? ' selected' : '') . '>' . get_text('Yes') . '</option>' . "\n"
				. '<option value="0"' . ($MyRow->EnIndFEvent==0 ? ' selected' : '') . '>' . get_text('No') . '</option>' . "\n"
				. '</select>' . "\n";
			$ComboTeamFin
				= '<select name="d_e_EnTeamFEvent_' . $MyRow->EnId .  '" id="d_e_EnTeamFEvent_' . $MyRow->EnId .  '" onChange="UpdateField(\'d_e_EnTeamFEvent_' . $MyRow->EnId . '\');">' . "\n"
				. '<option value="1"' . ($MyRow->EnTeamFEvent==1 ? ' selected' : '') . '>' . get_text('Yes') . '</option>' . "\n"
				. '<option value="0"' . ($MyRow->EnTeamFEvent==0 ? ' selected' : '') . '>' . get_text('No') . '</option>' . "\n"
				. '</select>' . "\n";
			$ComboMixTeamFin
				= '<select name="d_e_EnTeamMixEvent_' . $MyRow->EnId .  '" id="d_e_EnTeamMixEvent_' . $MyRow->EnId .  '" onChange="UpdateField(\'d_e_EnTeamMixEvent_' . $MyRow->EnId . '\');">' . "\n"
				. '<option value="1"' . ($MyRow->EnTeamMixEvent==1 ? ' selected' : '') . '>' . get_text('Yes') . '</option>' . "\n"
				. '<option value="0"' . ($MyRow->EnTeamMixEvent==0 ? ' selected' : '') . '>' . get_text('No') . '</option>' . "\n"
				. '</select>' . "\n";
			$ComboDoubleSpace
				= '<select name="d_e_EnDoubleSpace_' . $MyRow->EnId .  '" id="d_e_EnDoubleSpace_' . $MyRow->EnId .  '" onChange="UpdateField(\'d_e_EnDoubleSpace_' . $MyRow->EnId . '\');">' . "\n"
				. '<option value="1"' . ($MyRow->EnDoubleSpace==1 ? ' selected' : '') . '>' . get_text('Yes') . '</option>' . "\n"
				. '<option value="0"' . ($MyRow->EnDoubleSpace==0 ? ' selected' : '') . '>' . get_text('No') . '</option>' . "\n"
				. '</select>' . "\n";
			$ComboWheelChair
				= '<select name="d_e_EnWChair_' . $MyRow->EnId .  '" id="d_e_EnWChair_' . $MyRow->EnId .  '" onChange="UpdateField(\'d_e_EnWChair_' . $MyRow->EnId . '\');">' . "\n"
				. '<option value="1"' . ($MyRow->EnWChair==1 ? ' selected' : '') . '>' . get_text('Yes') . '</option>' . "\n"
				. '<option value="0"' . ($MyRow->EnWChair==0 ? ' selected' : '') . '>' . get_text('No') . '</option>' . "\n"
				. '</select>' . "\n";
				?>
<tr <?php print 'id="Row_' . $MyRow->EnId . '" ' . ($CurRow++ % 2 ? ' class="OtherColor"' : '');?>>
<td><?php print ($MyRow->EnCode!='' ? $MyRow->EnCode : '&nbsp;'); ?></td>
<td><?php print ($MyRow->EnFirstName . $MyRow->EnName !='' ? $MyRow->EnFirstName . ' ' . $MyRow->EnName : '&nbsp;'); ?></td>
<td><?php print ($MyRow->CoCode!='' ? $MyRow->CoCode : '&nbsp'); ?></td>
<td><?php print ($MyRow->CoName!='' ? $MyRow->CoName : '&nbsp;'); ?></td>
<td class="Center"><?php print ($MyRow->EnDivision!='' ? $MyRow->EnDivision : '&nbsp')?></td>
<td class="Center"><?php print ($MyRow->EnClass!='' ? $MyRow->EnClass : '&nbsp')?></td>
<td class="Center" title="<?php print get_text('IndClEvent', 'Tournament'); ?>"><?php print $ComboIndCl; ?></td>
<td class="Center" title="<?php print get_text('IndFinEvent', 'Tournament'); ?>"><?php print $ComboIndFin; ?></td>
<td class="Center" title="<?php print get_text('TeamClEvent', 'Tournament'); ?>"><?php print $ComboTeamCl; ?></td>
<td class="Center" title="<?php print get_text('TeamFinEvent', 'Tournament'); ?>"><?php print $ComboTeamFin; ?></td>
<td class="Center" title="<?php print get_text('MixedTeamFinEvent', 'Tournament'); ?>"><?php print $ComboMixTeamFin; ?></td>
<td class="Center" title="<?php print get_text('WheelChair', 'Tournament'); ?>"><?php print $ComboWheelChair; ?></td>
<td class="Center" title="<?php print get_text('DoubleSpace', 'Tournament'); ?>"><?php print $ComboDoubleSpace; ?></td>
</tr>
<?php
		}
	}
?>
</table>
<?php
	include('Common/Templates/tail.php');
?>