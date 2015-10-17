<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Partecipants/Fun_Targets.php');

	CheckTourSession(true); // will print the crack error string if not inside a tournament!

	$Advanced = (ProgramRelease!='FITARCO' AND ProgramRelease!='STABLE');

	$numDist=0;
	$colspan=0;

	$rsDist=null;

	$AvDiv=array();
	$q=safe_r_sql("select DivId, ClId from Divisions inner join Classes on ClTournament=DivTournament and ClAthlete=DivAthlete where DivTournament='{$_SESSION['TourId']}' and DivAthlete=1 AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed)) order by DivViewOrder, ClViewOrder");
	while($r=safe_fetch($q)) {
		$AvDiv[$r->DivId][$r->ClId]='<a name="" onclick="document.getElementById(\'TdClasses\').value=\''.$r->DivId.$r->ClId.'\'">'.$r->DivId.$r->ClId.'</a>';
	}

	$AvTargets=array();
	$q=safe_r_sql("select * from Targets order by TarOrder");
	while($r=safe_fetch($q)) {
		$AvTargets[$r->TarId]= get_text($r->TarDescr);
	}

	$select
		= "SELECT ToType,ToNumDist AS TtNumDist "
		. "FROM Tournament "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
	//print $select;exit;
	$rs=safe_r_sql($select);

	if (safe_num_rows($rs)==1) {
		$r=safe_fetch($rs);
		$numDist=$r->TtNumDist;
	}

	if ($numDist!=0)
	{
		$colspan=2+$numDist+$Advanced;

		$select
			= "SELECT DISTINCT t.* "
			. "FROM TargetFaces t "
			. "WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
		//print $select;exit;
		$rsDist=safe_r_sql($select);
	}

	foreach(($DefinedTargets=getTargets(false)) as $Target=>$divs) {
		foreach($divs as $Div=>$cl) {
			foreach($cl as $Class=>$default) {
				if($default) unset ($AvDiv[$Div][$Class]);
			}
		}
	}

	$JS_SCRIPT = array(
		'<script type="text/javascript" src="../Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_ManTargets.js"></script>',
		'<script type="text/javascript" src="../Common/Fun_JS.inc.js"></script>',
		'<script type="text/javascript">var StrConfirm="' . get_text('MsgAreYouSure') . '"; '
		. 'var CannotDelete="'.get_text('CannotDelete','Tournament').'"; </script>',
		);
	$PAGE_TITLE=get_text('MenuLM_Targets');

	include('Common/Templates/head.php');

?>
	<div align="center">
		<table class="Tabella">
			<tbody id="tbody">
				<tr><th class="Title" colspan="<?php print $colspan+3; ?>"><?php print get_text('MenuLM_Targets'); ?></th></tr>
				<tr>
					<th><?php print get_text('AvailableValues','Tournament').'<br/>'.get_text('BoldIsDefault','Tournament'); ?></th>
					<th><?php print get_text('Name','Tournament'); ?></th>
					<th><?php print get_text('FilterOnDivCl','Tournament'); ?></th>
					<th><?php print get_text('FilterOnDivClAdv','Tournament'); ?></th>
					<?php for ($i=1;$i<=$numDist;++$i) { ?>
						<th>.<?php print $i; ?>.</th>
					<?php } ?>
					<th><?php echo get_text('TVSetAsDefault','Tournament'); ?></th>
					<th>&nbsp;</th>
				</tr>
			<?php // righe per l'insert/edit ?>
				<tr id="edit">
					<td class="Center">
						<?php
						foreach($AvDiv as $Div=>$Cl) {
							if($Cl) echo implode(', ',$Cl).'<br/>';
						}
						?>
					</td>
					<td class="Center">
						<input type="text" id="TdName" size="10" maxlength="15" value="">
					</td>
					<td class="Center">
						<input type="text" id="TdClasses" size="4" maxlength="4" value="">
					</td>
					<td class="Center">
						<?php echo ($Advanced ? '<input type="text" id="TdRegExp" size="8" value="">' : '&nbsp;'); ?>
					</td>
					<?php for ($i=1;$i<=$numDist;++$i) { ?>
						<td class="Center">
						<select id="TdFace<?php print $i; ?>">
						<?php
						foreach($AvTargets as $Id=>$Val) {
							echo '<option value="'.$Id.'">'.$Val.'</option>';
						}
						?></select>
						<br/>ø (cm) <input type="text" id="TdDiam<?php print $i; ?>" size="3" maxlength="3" value="">
						</td>
					<?php } ?>
					<td class="Center">
						<input type="checkbox" id="TdDefault">
					</td>
					<td class="Center">
						<input type="button" name="command" value="<?php print get_text('CmdOk'); ?>" onclick="save(<?php print $numDist; ?>);">&nbsp;&nbsp;
						<input type="button" name="command" value="<?php print get_text('CmdCancel'); ?>" onclick="resetInput(<?php print $numDist; ?>);">
					</td>
				</tr>
				<tr class="Spacer"><td colspan="<?php print $colspan+3; ?>"></td></tr>
			<?php
				if ($rsDist!=null)
				{
					$k=0;
					while ($myRow=safe_fetch($rsDist))
					{
			?>
						<tr id="row_<?php print $k; ?>">
							<td><?php echo print_targets($myRow->TfId); ?></td>
							<td class="Center"><?php echo get_text($myRow->TfName,'Tournament','',true);?></td>
							<td class="Center" style="width:20%;"><div id="cl_<?php print $k; ?>"><?php print $myRow->TfClasses; ?></div></td>
							<td class="Center" style="width:20%;"><div id="reg_<?php print $k; ?>"><?php print $myRow->TfRegExp; ?></div></td>
							<?php 
							for ($i=1;$i<=$numDist;++$i) { 
								echo '<td class="Center"><div id="td_' . $k . '_' . $i . '">' . ($myRow->{'TfT' . $i} ? $AvTargets[$myRow->{'TfT' . $i}] . '<br/> '.$myRow->{'TfW' . $i}.' cm' : '') . '</div></td>';
							}
							?> 
							<td class="Center"><?php echo $myRow->TfDefault?get_text('Yes'):''; ?></td>
							<td class="Center"><img src="<?php echo $CFG->ROOT_DIR ?>Common/Images/drop.png" border="0" alt="#" title="#" onclick="deleteRow(<?php print $k; ?>, <?php print $myRow->TfId; ?>);"></td>
						</tr>
			<?php
						++$k;
					}
				}
			?>
			</tbody>
		</table>
	</div>
	<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');

function print_targets($TfId) {
	global $DefinedTargets;
	$ret='';
	if(empty($DefinedTargets[$TfId])) return '&nbsp;';
	foreach($DefinedTargets[$TfId] as $Div=>$Cl) {
		$ret.= $Div . ':&nbsp;';
		foreach($Cl as $class=>$def) {
			if($def) $class="<b>$class</b>";
			$ret.= $class . '&nbsp;';
		}
		$ret.='<br/>';
	}
	return $ret;
}
?>