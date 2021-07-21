<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/CommonLib.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Tournament/Fun_Tournament.local.inc.php');
    checkACL(AclCompetition, AclReadWrite);

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$JS_SCRIPT = array(
        phpVars2js(array(
            'MsgAreYouSure' => get_text('MsgAreYouSure'),
	        'MsgRowMustBeComplete' => str_replace('<br>','\n',get_text('MsgRowMustBeComplete')),
        )),
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Tournament/Fun_AJAX_ManDivClass.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
		);
	$PAGE_TITLE=get_text('ManDivClass','Tournament');

	include('Common/Templates/head.php');

?>
<div align="center">
<div class="medium">
<table class="Tabella">
	<tr><th class="Title"><?php print get_text('ManDivClass','Tournament');?></th></tr>
</table>
<br>
<table class="Tabella">
<tr><th class="Title" colspan="6"><?php print get_text('Divisions','Tournament');?></th></tr>
<tr>
    <th width="5%"><?php print get_text('Division');?></th>
    <th width="30%"><?php print get_text('Descr','Tournament');?></th>
    <th width="10%"><?php print get_text('Para','Records');?></th>
    <th width="10%"><?php print get_text('Athlete');?></th>
    <th width="10%"><?php print get_text('Progr');?></th>
    <th width="15%">&nbsp;</th>
</tr>
<tbody id="tbody_div">
<?php

$Select
    = "SELECT * "
    . "FROM Divisions "
    . "WHERE DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
    . "ORDER BY DivViewOrder ASC ";
$Rs=safe_r_sql($Select);

if (safe_num_rows($Rs)>0) {
    $Disabled=(defined('dontEditClassDiv') ? ' disabled="disabled"' : '');
    while ($MyRow=safe_fetch($Rs)) {
        echo '<tr id="Div_'.$MyRow->DivId.'">
            <td class="Bold Center">'.$MyRow->DivId.'</td>
            <td><input type="text" '.$Disabled.' name="d_DivDescription_'.$MyRow->DivId.'" id="d_DivDescription_'.$MyRow->DivId.'" size="56" maxlength="32" value="'.ManageHTML($MyRow->DivDescription).'" onBlur="UpdateField(\'D\',\'d_DivDescription_'.$MyRow->DivId.'\')"></td>
            <td class="Center"><select '.$Disabled.' name="d_DivIsPara_'.$MyRow->DivId.'" id="d_DivIsPara_'.$MyRow->DivId.'"  onBlur="UpdateField(\'D\',\'d_DivIsPara_'.$MyRow->DivId.'\')">
                <option value="0">'.get_text('No').'</option>
                <option value="1"'.($MyRow->DivIsPara?' selected':'').'>'.get_text('Yes').'</option>
                </select></td>
            <td class="Center"><select '.$Disabled.' name="d_DivAthlete_'.$MyRow->DivId.'" id="d_DivAthlete_'.$MyRow->DivId.'"  onBlur="UpdateField(\'D\',\'d_DivAthlete_'.$MyRow->DivId.'\')">
                <option value="0">'.get_text('No').'</option>
                <option value="1"'.($MyRow->DivAthlete?' selected':'').'>'.get_text('Yes').'</option>
                </select></td>
            <td class="Center"><input '.$Disabled.' type="text" name="d_DivViewOrder_'.$MyRow->DivId.'" id="d_DivViewOrder_'.$MyRow->DivId.'" size="3" maxlength="3" value="'.ManageHTML($MyRow->DivViewOrder).'" onBlur="UpdateField(\'D\',\'d_DivViewOrder_'.$MyRow->DivId.'\')"></td>
            <td class="Center">'.(defined('dontEditClassDiv') ? '&nbsp;' : '<img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" border="0" alt="#" title="#" onclick="DeleteRow(\'D\',\''.$MyRow->DivId.'\')">').'</td>
            </tr>';
    }
}

?>
</tbody>
<tr id="NewDiv" class="Spacer"><td colspan="6"></td></tr>
<?php if(!defined('dontEditClassDiv')) { ?>
	<tr>
	<td class="Center"><input type="text" name="New_DivId" id="New_DivId" size="3" maxlength="2"></td>
	<td><input type="text" name="New_DivDescription" id="New_DivDescription" size="56" maxlength="32"></td>
	<td class="Center"><select name="New_DivIsPara" id="New_DivIsPara">
		<option value="0"><?php echo get_text('No'); ?></option>
		<option value="1"><?php echo get_text('Yes'); ?></option>
		</select></td>
	<td class="Center"><select name="New_DivAthlete" id="New_DivAthlete">
		<option value="0"><?php echo get_text('No'); ?></option>
		<option value="1"><?php echo get_text('Yes'); ?></option>
		</select></td>
	<td class="Center"><input type="text" name="New_DivViewOrder" id="New_DivViewOrder" size="3" maxlength="3"></td>
	<td class="Center"><input type="button" name="CommandDiv" value="<?php print get_text('CmdSave');?>" onClick="AddDiv();"></td>
	</tr>
<?php }?>
</table>
<br>
<table class="Tabella">
<tr><th class="Title" colspan="11"><?php print get_text('Classes','Tournament');?></th></tr>
<tr>
<th width="5%"><?php print get_text('Class');?></th>
<th width="5%"><?php print get_text('Sex','Tournament');?></th>
<th width="30%"><?php print get_text('Descr','Tournament');?></th>
<th width="10%"><?php print get_text('Para', 'Records');?></th>
<th width="10%"><?php print get_text('Athlete');?></th>
<th width="10%"><?php print get_text('Progr');?></th>
<th width="10%"><?php echo get_text('YearStart','Tournament') ?></th>
<th width="10%"><?php echo get_text('YearEnd','Tournament') ?></th>
<th width="15%"><?php echo get_text('ValidClass','Tournament') ?></th>
<th width="15%"><?php echo get_text('ValidDivisions','Tournament') ?></th>
<th width="10%">&nbsp;</th>
</tr>
<tbody id="tbody_cl">
<?php
	$Select
		= "SELECT * "
		. "FROM Classes "
		. "WHERE ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "ORDER BY ClViewOrder ASC ";
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)>0)
	{
		while ($MyRow=safe_fetch($Rs))
		{
			$id_post=$MyRow->ClId ;
?>
<tr id="Cl_<?php print $id_post;?>">
<td class="Bold Center"><?php print $MyRow->ClId;?></td>
<td>
<select <?php print (!defined('dontEditClassDiv') ? '' :' disabled="disabled"');?> name="d_ClSex_<?php print $id_post;?>" id="d_ClSex_<?php print $id_post;?>" onChange="UpdateField('C','d_ClSex_<?php print $id_post;?>')">
<option value="0"<?php print ($MyRow->ClSex==0 ? ' selected' : '');?>><?php print get_text('ShortMale','Tournament');?></option>
<option value="1"<?php print ($MyRow->ClSex==1 ? ' selected' : '');?>><?php print get_text('ShortFemale','Tournament');?></option>
<option value="-1"<?php print ($MyRow->ClSex==-1 ? ' selected' : '');?>><?php print get_text('ShortUnisex','Tournament');?></option>
</select>
</td>
<td><input <?php print (!defined('dontEditClassDiv') ? '' :' disabled="disabled"');?> type="text" name="d_ClDescription_<?php print $id_post;?>" id="d_ClDescription_<?php print $id_post;?>" size="56" maxlength="32" value="<?php print ManageHTML($MyRow->ClDescription);?>" onBlur="UpdateField('C','d_ClDescription_<?php print $id_post;?>')"></td>
<td class="Center"><select <?php print (!defined('dontEditClassDiv') ? '' :' disabled="disabled"');?> name="d_ClIsPara_<?php print $id_post;?>" id="d_ClIsPara_<?php print $id_post;?>"  onChange="UpdateField('C','d_ClIsPara_<?php print $id_post;?>')">
	<option value="0"><?php echo get_text('No'); ?></option>
	<option value="1"<?php print ($MyRow->ClIsPara?' selected':'');?>><?php echo get_text('Yes'); ?></option>
	</select></td>
<td class="Center"><select <?php print (!defined('dontEditClassDiv') ? '' :' disabled="disabled"');?> name="d_ClAthlete_<?php print $id_post;?>" id="d_ClAthlete_<?php print $id_post;?>"  onChange="UpdateField('C','d_ClAthlete_<?php print $id_post;?>')">
	<option value="0"><?php echo get_text('No'); ?></option>
	<option value="1"<?php print ($MyRow->ClAthlete?' selected':'');?>><?php echo get_text('Yes'); ?></option>
	</select></td>
<td class="Center"><input <?php print (!defined('dontEditClassDiv') ? '' :' disabled="disabled"');?> type="text" name="d_ClViewOrder_<?php print $id_post;?>" id="d_ClViewOrder_<?php print $id_post;?>" size="3" maxlength="3" value="<?php print ManageHTML($MyRow->ClViewOrder);?>" onBlur="UpdateField('C','d_ClViewOrder_<?php print $id_post;?>')"></td>
<td class="Center"><input <?php print (!defined('dontEditClassDiv') ? '' :' disabled="disabled"');?> type="text" name="d_ClAgeFrom_<?php print $id_post;?>" id="d_ClAgeFrom_<?php print $id_post;?>" size="3" maxlength="3" value="<?php print $MyRow->ClAgeFrom;?>" onBlur="UpdateClassAge('<?php print $id_post;?>','From')"></td>
<td class="Center"><input <?php print (!defined('dontEditClassDiv') ? '' :' disabled="disabled"');?> type="text" name="d_ClAgeTo_<?php print $id_post;?>" id="d_ClAgeTo_<?php print $id_post;?>" size="3" maxlength="3" value="<?php print $MyRow->ClAgeTo;?>" onBlur="UpdateClassAge('<?php print $id_post;?>','To')"></td>
<td class="Center"><input <?php print (!defined('dontEditClassDiv') ? '' :' disabled="disabled"');?> type="text" name="d_ClValidClass_<?php print $id_post;?>" id="d_ClValidClass_<?php print $id_post;?>" size="8" maxlength="24" value="<?php print $MyRow->ClValidClass;?>" onBlur="UpdateValidClass('<?php print $id_post;?>')"></td>
<td class="Center"><input <?php print (!defined('dontEditClassDiv') ? '' :' disabled="disabled"');?> type="text" name="d_ClValidDivision_<?php print $id_post;?>" id="d_ClValidDivision_<?php print $id_post;?>" size="8" maxlength="255" value="<?php print $MyRow->ClDivisionsAllowed;?>" onBlur="UpdateValidDivision('<?php print $id_post;?>')"></td>
<td class="Center"><?= (defined('dontEditClassDiv') ? '&nbsp;' : '<img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" border="0" alt="#" title="#" onclick="DeleteRow(\'C\',\''.$id_post.'\')">') ?></td>
</tr>

<?php
		}
	}
echo '</tbody>';

	if(!defined('dontEditClassDiv')) { ?>
	<tr id="NewCl" class="Spacer"><td colspan="11"></td></tr>
	<tr>
	<td class="Bold Center"><input type="text" name="New_ClId" id="New_ClId" size="3" maxlength="2"></td>
	<td>
	<select name="New_ClSex" id="New_ClSex">
	<option value="0"><?php print get_text('ShortMale','Tournament');?></option>
	<option value="1"><?php print get_text('ShortFemale','Tournament');?></option>
	<option value="-1"><?php print get_text('ShortUnisex','Tournament');?></option>
	</select>
	</td>
	<td><input type="text" name="New_ClDescription" id="New_ClDescription" size="56" maxlength="32"></td>
	<td class="Center"><select name="New_ClIsPara" id="New_ClIsPara">
		<option value="0"><?php echo get_text('No'); ?></option>
		<option value="1"><?php echo get_text('Yes'); ?></option>
		</select></td>
	<td class="Center"><select name="New_ClAthlete" id="New_ClAthlete">
		<option value="0"><?php echo get_text('No'); ?></option>
		<option value="1"><?php echo get_text('Yes'); ?></option>
		</select></td>
	<td class="Center"><input type="text" name="New_ClViewOrder" id="New_ClViewOrder" size="3" maxlength="3"></td>
	<td class="Center"><input type="text" name="New_ClAgeFrom" id="New_ClAgeFrom" size="3" maxlength="3"></td>
	<td class="Center"><input type="text" name="New_ClAgeTo" id="New_ClAgeTo" size="3" maxlength="3"></td>
	<td class="Center"><input type="text" name="New_ClValidClass" id="New_ClValidClass" size="8" maxlength="16"></td>
	<td class="Center"><input type="text" name="New_ClValidDivision" id="New_ClValidDivision" size="8" maxlength="16"></td>
	<td class="Center"><input type="button" name="CommandDiv" value="<?php print get_text('CmdSave');?>" onClick="AddCl()"></td>
	</tr>
<?php }?>
<tr  class="Spacer"><td colspan="11"></td></tr>
<tr><td colspan="11" class="Center"><a class="Link" href="ManDistances.php"><?php print get_text('ManDistances','Tournament'); ?></a></td></tr>
</table>
</div>
</div>
<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');
?>
