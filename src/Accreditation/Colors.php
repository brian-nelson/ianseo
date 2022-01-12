<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');

	CheckTourSession(true);
    checkACL(AclCompetition, AclReadWrite);

	$rs=null;

	$rs=safe_r_sql("SELECT * FROM AccColors WHERE AcTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY AcDivClass ASC ");

	$k=0;


	$JS_SCRIPT=array(
		'<script type="text/javascript" src="../Common/ColorPicker/302pop.js"></script>',
		'<script type="text/javascript" src="../Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="../Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_Colors.js"></script>',
		'<script type="text/javascript" >var StrConfirm="' . get_text('MsgAreYouSure') . '";</script>',
		);

	include('Common/Templates/head.php');
?>
<div>
	<table class="Tabella">
		<tbody id="tbody">
			<tr><th class="Title" colspan="9"><?php print get_text('ManColors','Tournament'); ?></th></tr>
			<tr id="edit">
				<th><?php print get_text('FilterOnDivCl','Tournament'); ?></th>
				<th><?php print get_text('Color','Tournament'); ?></th>
				<th><?php print get_text('TitleColor','Tournament'); ?></th>
				<th><?php print get_text('Athlete'); ?></th>
				<th><?php print get_text('Areas','Tournament'); ?></th>
				<th><?php print get_text('Transport','Tournament'); ?></th>
				<th><?php print get_text('Accomodation','Tournament'); ?></th>
				<th><?php print get_text('Meals','Tournament'); ?></th>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<td class="Center"><input type="hidden" name="d_rowId" id="d_rowId" value="-1"><input type="text" size="12" maxlength="10" name="d_Classes" id="d_Classes" value="" /></td>
				<td class="Center">
					<input type="text" name="d_Color" id="d_Color" value="" />
					<input type="text" id="Square" size="1" value="" readonly="readonly"/>
					<img src="../Common/Images/sel.gif" onclick="javascript:pickerPopup302('d_Color','Square');">
				</td>
				<td class="Center">
					<select name="d_TitleReverse" id="d_TitleReverse">
						<option value="1"><?php print get_text('Yes'); ?></option>
						<option value="0" selected><?php print get_text('No'); ?></option>
					</select>
				</td>
				<td class="Center">
					<select name="d_Ath" id="d_Ath">
						<option value="1"><?php print get_text('Yes'); ?></option>
						<option value="0"><?php print get_text('No'); ?></option>
					</select>
				</td>
				<td class="Center">
					<?php
						for($i=0; $i<=7;$i++) {
							echo $i . '<input type="checkbox" name="d_Area' . $i . '" id="d_Area' . $i . '" onclick="resetArea0(this)" />&nbsp;&nbsp;';
							if($i<2) {
								echo $i . '*<input type="checkbox" name="d_Area' . $i . 'Star" id="d_Area' . $i . 'Star"  onclick="resetArea0(this)"/>&nbsp;&nbsp;';
							}
						}
// 							echo '*<input type="checkbox" name="d_AreaStar" id="d_AreaStar" />';
					?>
				</td>
				<td class="Center">
					<select name="d_Transport" id="d_Transport">
						<option value="3"><?php print get_text('Transport_3','Tournament'); ?></option>
						<option value="2"><?php print get_text('Transport_2','Tournament'); ?></option>
						<option value="1"><?php print get_text('Transport_1','Tournament'); ?></option>
						<option value="0" selected><?php print get_text('No'); ?></option>
					</select>
				</td>
				<td class="Center">
					<select name="d_Accomodation" id="d_Accomodation">
						<option value="1"><?php print get_text('Yes'); ?></option>
						<option value="0" selected><?php print get_text('No'); ?></option>
					</select>
				</td>
				<td class="Center">
					<select name="d_Meals" id="d_Meals">
						<option value="1"><?php print get_text('Yes'); ?></option>
						<option value="0" selected><?php print get_text('No'); ?></option>
					</select>
				</td>
				<td class="Center">
					<input type="button" name="command" value="<?php print get_text('CmdOk'); ?>" onclick="save();">&nbsp;&nbsp;
					<input type="button" name="command" value="<?php print get_text('CmdCancel'); ?>" onclick="resetInput();">
				</td>
			</tr>
			<tr>
			<th><?php print get_text('Areas','Tournament'); ?></th>
			<td colspan="8">
			<?php
			for($i=0; $i<=7; $i++)
			{
				print '<b>' .$i . "</b>:&nbsp;" . get_text('Area_'. $i,'Tournament') . "; ";
			}
			print "<br><b>0*</b>:&nbsp;" . get_text('Area_0*','Tournament') . "; ";
			print "<b>1*</b>:&nbsp;" . get_text('Area_1*','Tournament') . "; ";

			?>
			</td>
			</tr>

			<tr class="Spacer"><td colspan="9"></td></tr>
			<?php

			if (safe_num_rows($rs)>0)
			{
				while ($myRow=safe_fetch($rs))
				{
					echo '<tr id="row_' .$k . '">';
					echo '<td class="Center">';
					echo '<a href="javascript:editRule(\'' .  $k . '\',\'' . $myRow->AcDivClass . '\',\'#' . $myRow->AcColor .'\',\''. $myRow->AcTitleReverse . '\',';
					for($i=0; $i<=7;$i++)
						echo "'". $myRow->{'AcArea' . $i} . "', ";

					echo '\'' . $myRow->AcAreaStar . '\',\'' . $myRow->AcTransport . '\',\'' . $myRow->AcAccomodation . '\',\'' . $myRow->AcMeal . '\')">';
					echo $myRow->AcDivClass . '</a></td>';
					echo '<td class="Center"><input type="text" readonly="readonly" size="1" style="background-color:#' . $myRow->AcColor . '" />&nbsp;#' . $myRow->AcColor .'</td>';
					echo '<td class="Center">' . ($myRow->AcTitleReverse==1 ? get_text('Yes') : get_text('No')) . '</td>';
					echo '<td class="Center">' . ($myRow->AcIsAthlete==1 ? get_text('Yes') : get_text('No')) . '</td>';
					echo '<td class="Center">';
					$tmp=array();
					for($i=0;$i<=7;$i++) {
						if($myRow->{'AcArea' . $i}) {
							$tmp[]= $i . (($i<2 and$myRow->AcAreaStar) ? '*' : '');
						}
					}
					print implode('&nbsp;&nbsp;&nbsp', $tmp);
					echo '</td>';
					echo '<td class="Center">' . ($myRow->AcTransport!=0 ? get_text('Transport_'.$myRow->AcTransport,'Tournament') : get_text('No')) . '</td>';
					echo '<td class="Center">' . ($myRow->AcAccomodation==1 ? get_text('Yes') : get_text('No')) . '</td>';
					echo '<td class="Center">' . ($myRow->AcMeal==1 ? get_text('Yes') : get_text('No')) . '</td>';
					echo '<td class="Center"><img src="../Common/Images/drop.png" border="0" alt="#" title="#" onclick="deleteRow(\'' . $k++ . '\',\'' . $myRow->AcDivClass . '\');"></td>';
					echo '</tr>';
				}
			}
			?>
		</tbody>
	</table>
</div>
<?php
	include('Common/Templates/tail.php');
?>