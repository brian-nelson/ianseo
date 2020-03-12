<?php
/*
	Viene incluso il motore ajax di index per sfruttare UpdateField
*/
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
    checkACL(AclParticipants, AclReadWrite);
	require_once('Common/Fun_FormatText.inc.php');

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="../Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_ManStatus.js"></script>',
		'<script type="text/javascript" src="../Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_index.js"></script>',
		'<style>
			select.o1, option[value="1"] {background-color:#d0ffff}
			select.o0, option[value="0"] {background-color:#ffffd0}
		</style>',
		);

	$PAGE_TITLE=get_text('EventAccess','Tournament');

	$Order=empty($_REQUEST['Order']) ? '' : $_REQUEST['Order'];

	include('Common/Templates/head.php');
?>
<table class="Tabella">
<tr><th class="Title" colspan="13"><?php print get_text('EventAccess','Tournament'); ?></th></tr>
<tr class="Divider"><td colspan="13"></td></tr>
<tr class="Divider"><td colspan="13"></td></tr>
<tr><td colspan="13" class="Bold">
        <form>
        <div style="display:flex;justify-content: space-around;flex-wrap: wrap">
            <div style="margin:0 1em"><input type="checkbox" name="chk_BlockAutoSave" id="chk_BlockAutoSave" value="1"><?php echo get_text('CmdBlocAutoSave') ?></div>
            <div style="margin:0 1em"><?= get_text('Division') ?> <input type="text" name="Div" value="<?= empty($_REQUEST['Div']) ? '' : $_REQUEST['Div'] ?>"></div>
            <div style="margin:0 1em"><?= get_text('Class') ?> <input type="text" name="Class" value="<?= empty($_REQUEST['Class']) ? '' : $_REQUEST['Class'] ?>"></div>
            <div style="margin:0 1em"><?= get_text('Country') ?> <input type="text" name="Country" value="<?= empty($_REQUEST['Country']) ? '' : $_REQUEST['Country'] ?>"></div>
            <div style="margin:0 1em"><?= get_text('Event') ?> <input type="text" name="Event" value="<?= empty($_REQUEST['Event']) ? '' : $_REQUEST['Event'] ?>"></div>
            <div style="margin:0 1em"><?= get_text('Archer') ?> <input type="text" name="Name" value="<?= empty($_REQUEST['Name']) ? '' : $_REQUEST['Name'] ?>"></div>
            <div style="margin:0 1em"><input type="submit" value="<?= get_text('CmdOk') ?>"></div>
        </div>
        </form>
    </td></tr>
<tr class="Divider"><td colspan="13"></td></tr>
<?php
	$Select = "SELECT EnId,
		EnCode,
		EnFirstName,
		EnName,
		EnTournament,
		EnSex,
		EnDivision,
		EnClass,
		CoCode,
		CoName,
		EnIndClEvent,
		EnTeamClEvent,
		EnIndFEvent,
		EnTeamFEvent,
		EnTeamMixEvent,
		EnWChair,
		EnDoubleSpace,
		EcCode 
		FROM Entries 
		LEFT JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
		left join EventClass on EcTournament=EnTournament and EcDivision=EnDivision and EcClass=EnClass and EcTeamEvent=0
		WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1 ";

	if(!empty($_REQUEST['Div'])) {
	    $Select.=" and EnDivision like '{$_REQUEST['Div']}' ";
    }
	if(!empty($_REQUEST['Class'])) {
	    $Select.=" and EnClass like '{$_REQUEST['Class']}' ";
    }
	if(!empty($_REQUEST['Country'])) {
	    $Select.=" and (CoCode like '%{$_REQUEST['Country']}%' or CoName like '%{$_REQUEST['Country']}%') ";
    }
	if(!empty($_REQUEST['Event'])) {
	    $Select.=" and EcCode like '%{$_REQUEST['Event']}%' ";
    }
	if(!empty($_REQUEST['Name'])) {
	    $Select.=" and concat(EnFirstName,' ',EnName) like '%{$_REQUEST['Name']}%' ";
    }

    $Direction=substr($Order, -4)=='Desc' ? 'desc' : '';
    switch($Order) {
        case 'ordCode':
        case 'ordCodeDesc':
		        $OrderBy = " EnCode $Direction ";
            break;
        case 'ordName':
        case 'ordNameDesc':
            $OrderBy = " EnFirstName $Direction, EnName ";
            break;
        case 'ordCountry':
        case 'ordCountryDesc':
            $OrderBy = " CoCode $Direction, EnFirstName, EnName ";
            break;
        case 'ordDiv':
        case 'ordDivDesc':
            $OrderBy = "EnDivision $Direction, EnFirstName, EnName ";
            break;
        case 'ordCl':
        case 'ordClDesc':
            $OrderBy = "EnClass $Direction, EnFirstName, EnName ";
            break;
        case 'ordIn':
        case 'ordInDesc':
            $OrderBy = "EnIndClEvent $Direction, EnFirstName, EnName ";
            break;
        case 'ordFn':
        case 'ordFnDesc':
            $OrderBy = "EnIndFEvent $Direction, EnFirstName, EnName ";
            break;
        case 'ordTm':
        case 'ordTmDesc':
            $OrderBy = "EnTeamClEvent $Direction, EnFirstName, EnName ";
            break;
        case 'ordFt':
        case 'ordFtDesc':
            $OrderBy = "EnTeamFEvent $Direction, EnFirstName, EnName ";
            break;
        case 'ordMx':
        case 'ordMxDesc':
            $OrderBy = "EnTeamMixEvent $Direction, EnFirstName, EnName ";
            break;
        case 'ordWc':
        case 'ordWcDesc':
            $OrderBy = "EnWChair $Direction, EnFirstName, EnName ";
            break;
        case 'ordXb':
        case 'ordXbDesc':
            $OrderBy = "EnDoubleSpace $Direction, EnFirstName, EnName ";
            break;
        default:
	        $OrderBy = " EnFirstName ASC,EnName ASC ";
    }


	$Select.="ORDER BY " . $OrderBy;

	$Rs=safe_r_sql($Select);

	if (debug)
		print $Select . '<br><br>';
	if (safe_num_rows($Rs)>0)
	{
		print '<tr>';
		print '<td class="Title" width="6%" ><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordCode' ? 'ordCodeDesc' : 'ordCode') . '">' . get_text('Code','Tournament') . '</a></td>'
			. '<td class="Title" width="19%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordName' ? 'ordNameDesc' : 'ordName') . '">' . get_text('Archer') . '</a></td>'
			. '<td class="Title" width="4%" ><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordCountry' ? 'ordCountryDesc' : 'ordCountry') . '">' . get_text('Country') . '</a></td>'
			. '<td class="Title" width="17%"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordCountry' ? 'ordCountryDesc' : 'ordCountry') . '">' . get_text('NationShort','Tournament') . '</a></td>'
			. '<td class="Title" width="4%" ><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordDiv' ? 'ordDivDesc' : 'ordDiv') . '">' . get_text('Div') . '</a></td>'
			. '<td class="Title" width="4%" ><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordCl' ? 'ordClDesc' : 'ordCl') . '">' . get_text('Cl') . '</a></td>'
			. '<td class="Title" width="8%" ><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordIn' ? 'ordInDesc' : 'ordIn') . '">' . get_text('IndClEvent', 'Tournament') . '</a></td>'
			. '<td class="Title" width="8%" ><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordFn' ? 'ordFnDesc' : 'ordFn') . '">' . get_text('IndFinEvent', 'Tournament') . '</a></td>'
			. '<td class="Title" width="8%" ><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordTm' ? 'ordTmDesc' : 'ordTm') . '">' . get_text('TeamClEvent', 'Tournament') . '</a></td>'
			. '<td class="Title" width="8%" ><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordFt' ? 'ordFtDesc' : 'ordFt') . '">' . get_text('TeamFinEvent', 'Tournament') . '</a></td>'
			. '<td class="Title" width="8%" ><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordMx' ? 'ordMxDesc' : 'ordMx') . '">' . get_text('MixedTeamFinEvent', 'Tournament') . '</a></td>'
			. '<td class="Title" width="8%" ><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordWc' ? 'ordWcDesc' : 'ordWc') . '">' . get_text('WheelChair', 'Tournament') . '</a></td>'
			. '<td class="Title" width="8%" ><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordXb' ? 'ordXbDesc' : 'ordXb') . '">' . get_text('DoubleSpace', 'Tournament') . '</a></td>';
        print '</tr>';

        echo '<tbody id="MainBody">';
		$CurRow = 0;
		while ($MyRow=safe_fetch($Rs))
		{
			$ComboIndCl
				= '<select class="o'.$MyRow->EnIndClEvent.'" name="d_e_EnIndClEvent_' . $MyRow->EnId .  '" id="d_e_EnIndClEvent_' . $MyRow->EnId .  '" onChange="UpdateField(\'d_e_EnIndClEvent_' . $MyRow->EnId . '\');">'
				. '<option value="1"' . ($MyRow->EnIndClEvent==1 ? ' selected' : '') . '>' . get_text('Yes') . '</option>'
				. '<option value="0"' . ($MyRow->EnIndClEvent==0 ? ' selected' : '') . '>' . get_text('No') . '</option>'
				. '</select>';
			$ComboTeamCl
				= '<select class="o'.$MyRow->EnTeamClEvent.'" name="d_e_EnTeamClEvent_' . $MyRow->EnId .  '" id="d_e_EnTeamClEvent_' . $MyRow->EnId .  '" onChange="UpdateField(\'d_e_EnTeamClEvent_' . $MyRow->EnId . '\');">'
				. '<option value="1"' . ($MyRow->EnTeamClEvent==1 ? ' selected' : '') . '>' . get_text('Yes') . '</option>'
				. '<option value="0"' . ($MyRow->EnTeamClEvent==0 ? ' selected' : '') . '>' . get_text('No') . '</option>'
				. '</select>';
			$ComboIndFin
				= '<select class="o'.$MyRow->EnIndFEvent.'" name="d_e_EnIndFEvent_' . $MyRow->EnId .  '" id="d_e_EnIndFEvent_' . $MyRow->EnId .  '" onChange="UpdateField(\'d_e_EnIndFEvent_' . $MyRow->EnId . '\');">'
				. '<option value="1"' . ($MyRow->EnIndFEvent==1 ? ' selected' : '') . '>' . get_text('Yes') . '</option>'
				. '<option value="0"' . ($MyRow->EnIndFEvent==0 ? ' selected' : '') . '>' . get_text('No') . '</option>'
				. '</select>';
			$ComboTeamFin
				= '<select class="o'.$MyRow->EnTeamFEvent.'" name="d_e_EnTeamFEvent_' . $MyRow->EnId .  '" id="d_e_EnTeamFEvent_' . $MyRow->EnId .  '" onChange="UpdateField(\'d_e_EnTeamFEvent_' . $MyRow->EnId . '\');">'
				. '<option value="1"' . ($MyRow->EnTeamFEvent==1 ? ' selected' : '') . '>' . get_text('Yes') . '</option>'
				. '<option value="0"' . ($MyRow->EnTeamFEvent==0 ? ' selected' : '') . '>' . get_text('No') . '</option>'
				. '</select>';
			$ComboMixTeamFin
				= '<select class="o'.$MyRow->EnTeamMixEvent.'" name="d_e_EnTeamMixEvent_' . $MyRow->EnId .  '" id="d_e_EnTeamMixEvent_' . $MyRow->EnId .  '" onChange="UpdateField(\'d_e_EnTeamMixEvent_' . $MyRow->EnId . '\');">'
				. '<option value="1"' . ($MyRow->EnTeamMixEvent==1 ? ' selected' : '') . '>' . get_text('Yes') . '</option>'
				. '<option value="0"' . ($MyRow->EnTeamMixEvent==0 ? ' selected' : '') . '>' . get_text('No') . '</option>'
				. '</select>';
			$ComboDoubleSpace
				= '<select class="o'.$MyRow->EnDoubleSpace.'" name="d_e_EnDoubleSpace_' . $MyRow->EnId .  '" id="d_e_EnDoubleSpace_' . $MyRow->EnId .  '" onChange="UpdateField(\'d_e_EnDoubleSpace_' . $MyRow->EnId . '\');">'
				. '<option value="1"' . ($MyRow->EnDoubleSpace==1 ? ' selected' : '') . '>' . get_text('Yes') . '</option>'
				. '<option value="0"' . ($MyRow->EnDoubleSpace==0 ? ' selected' : '') . '>' . get_text('No') . '</option>'
				. '</select>';
			$ComboWheelChair
				= '<select class="o'.$MyRow->EnWChair.'" name="d_e_EnWChair_' . $MyRow->EnId .  '" id="d_e_EnWChair_' . $MyRow->EnId .  '" onChange="UpdateField(\'d_e_EnWChair_' . $MyRow->EnId . '\');">'
				. '<option value="1"' . ($MyRow->EnWChair==1 ? ' selected' : '') . '>' . get_text('Yes') . '</option>'
				. '<option value="0"' . ($MyRow->EnWChair==0 ? ' selected' : '') . '>' . get_text('No') . '</option>'
				. '</select>';
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
        echo '</tbody>';
	}
?>
</table>
<?php
	include('Common/Templates/tail.php');
?>