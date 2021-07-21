<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
    checkACL(AclParticipants, AclReadOnly);
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
	<tr><th class="Title" colspan="8"><?php print get_text('ManAthStatus','Tournament'); ?></th></tr>
	<tr class="Divider"><td colspan="8"></td></tr>
	<tr class="Divider"><td colspan="8"></td></tr>
	<tr><td colspan="8" class="Bold"><input type="checkbox" name="chk_BlockAutoSave" id="chk_BlockAutoSave" value="1"><?php echo get_text('CmdBlocAutoSave') ?></td></tr>
	<tr class="Divider"><td colspan="8"></td></tr>
<?php
	$Select = "SELECT EnCode, EnFirstName, EnName, EnSex, EnDob as EnCtrlCode, EnNameOrder, EnStatus, CoCode, CoName, LueFamilyName, LueName, LueSex, LueCtrlCode, LueCountry, LueCoShort, LueNameOrder, LueStatus
		FROM Entries
		LEFT JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
		left join LookUpEntries on EnCode=LueCode and EnIocCode=LueIocCode and LueDefault=1
		WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . "
			and (EnFirstName!=LueFamilyName
				or EnName!=LueName
				or EnNameOrder!=LueNameOrder
				or EnSex!=LueSex
				or EnCtrlCode!=LueCtrlCode
				or CoCode!=LueCountry
				or EnStatus!=LueStatus
				)
		ORDER BY EnFirstName ASC,EnName ASC ";
	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)>0) {
		print '<tr>';
		print '<td class="Title">' . get_text('Code','Tournament') . '</td>'
			. '<td class="Title">' . get_text('Status','Tournament') . '</td>'
			. '<td class="Title">' . get_text('FamilyName','Tournament') . '</td>'
			. '<td class="Title">' . get_text('Name','Tournament') . '</td>'
			. '<td class="Title">' . get_text('Name','Tournament') . '</td>'
			. '<td class="Title">' . get_text('Sex','Tournament') . '</td>'
			. '<td class="Title">' . get_text('DOB', 'Tournament') . '</td>'
			. '<td class="Title">' . get_text('Country') . '</td>'
			. '<td class="Title">' . get_text('NationShort','Tournament') . '</td>'
		 	. '</tr>';

		while ($MyRow=safe_fetch($Rs)) {
			echo '<tr>';
			echo '<td>'.$MyRow->EnCode.'</td>';
			if($MyRow->EnStatus==$MyRow->LueStatus) {
				echo '<td class="OK">'.get_text('Status_'.$MyRow->EnStatus).'</td>';
			} else {
				echo '<td class="NoShoot">'.get_text('Status_'.$MyRow->EnStatus).' / '.get_text('Status_'.$MyRow->LueStatus).'</td>';
			}
			if($MyRow->EnFirstName==$MyRow->LueFamilyName) {
				echo '<td class="OK">'.$MyRow->EnFirstName.'</td>';
			} else {
				echo '<td class="NoShoot">'.$MyRow->EnFirstName.' / '.$MyRow->LueFamilyName.'</td>';
			}
			if($MyRow->EnName==$MyRow->LueName) {
				echo '<td class="OK">'.$MyRow->EnName.'</td>';
			} else {
				echo '<td class="NoShoot">'.$MyRow->EnName.' / '.$MyRow->LueName.'</td>';
			}
			if($MyRow->EnNameOrder==$MyRow->LueNameOrder) {
				echo '<td class="OK">'.($MyRow->EnNameOrder ? $MyRow->EnFirstName.' '.$MyRow->EnName : $MyRow->EnName.' '.$MyRow->EnFirstName).'</td>';
			} else {
				echo '<td class="NoShoot'.($MyRow->EnNameOrder==$MyRow->LueNameOrder ? 'OK' : '').'">'.($MyRow->EnNameOrder ? $MyRow->EnFirstName.' '.$MyRow->EnName : $MyRow->EnName.' '.$MyRow->EnFirstName).' / '.($MyRow->LueNameOrder ? $MyRow->LueFamilyName.' '.$MyRow->LueName : $MyRow->LueName.' '.$MyRow->LueFamilyName).'</td>';
			}
			if($MyRow->EnSex==$MyRow->LueSex) {
				echo '<td class="OK">'.get_text($MyRow->EnSex ? 'ShortFemale' : 'ShortMale', 'Tournament').'</td>';
			} else {
				echo '<td class="NoShoot">'.get_text($MyRow->EnSex ? 'ShortFemale' : 'ShortMale', 'Tournament').' / '.get_text($MyRow->LueSex ? 'ShortFemale' : 'ShortMale', 'Tournament').'</td>';
			}
			if($MyRow->EnCtrlCode==$MyRow->LueCtrlCode) {
				echo '<td class="OK">'.$MyRow->EnCtrlCode.'</td>';
			} else {
				echo '<td class="NoShoot">'.$MyRow->EnCtrlCode.' / '.$MyRow->LueCtrlCode.'</td>';
			}
			if($MyRow->CoCode==$MyRow->LueCountry) {
				echo '<td class="OK">'.$MyRow->CoCode.'</td>';
			} else {
				echo '<td class="NoShoot">'.$MyRow->CoCode.' / '.$MyRow->LueCountry.'</td>';
			}
			if($MyRow->CoName==$MyRow->LueCoShort) {
				echo '<td class="OK">'.$MyRow->CoName.'</td>';
			} else {
				echo '<td class="NoShoot">'.$MyRow->CoName.' / '.$MyRow->LueCoShort.'</td>';
			}
			echo '</tr>';
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
<?php
	include('Common/Templates/tail.php');
?>