<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');

	CheckTourSession(true); // will print the crack error string if not inside a tournament!

	$numDist=0;
	$tourType=0;
	$colspan=0;

	$rsDist=null;

	$AvDiv=array();
	$q=safe_r_sql("select DivId, ClId from Divisions inner join Classes on ClTournament=DivTournament and ClAthlete=DivAthlete where DivTournament='{$_SESSION['TourId']}' and DivAthlete=1 AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed)) order by DivViewOrder, ClViewOrder");
	while($r=safe_fetch($q)) {
		$AvDiv[$r->DivId][$r->ClId]='<a name="" onclick="document.getElementById(\'TdClasses\').value=\''.$r->DivId.$r->ClId.'\'">'.$r->DivId.$r->ClId.'</a>';
	}

	$select
		= "SELECT ToType,ToNumDist AS TtNumDist "
		. "FROM Tournament "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
	//print $select;exit;
	$rs=safe_r_sql($select);

	if (safe_num_rows($rs)==1) {
		$r=safe_fetch($rs);
		$tourType=$r->ToType;
		$numDist=$r->TtNumDist;
	}

	if ($tourType*$numDist!=0) {
		$colspan=2+$numDist;

		$select
			= "SELECT DISTINCT t.* "
			. "FROM Divisions INNER JOIN Classes ON DivTournament=ClTournament AND DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "INNER JOIN TournamentDistances AS t ON TdType=" . $tourType . " and TdTournament=DivTournament AND CONCAT(TRIM(DivId),TRIM(ClId)) LIKE TdClasses "
			. "WHERE DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
		//print $select;exit;
		$rsDist=safe_r_sql($select);
	}

	foreach(($DefinedDistances=getDistances(false)) as $Dist=>$divs) {
		foreach($divs as $Div=>$cl) {
			foreach($cl as $Class=>$default) {
				unset ($AvDiv[$Div][$Class]);
			}
		}
	}

// 	debug_svela($AvDiv);

	$JS_SCRIPT = array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Tournament/Fun_AJAX_ManDistances.js"></script>',
		'<script type="text/javascript">var StrConfirm="' . get_text('MsgAreYouSure') . '";</script>',
		);
	$PAGE_TITLE=get_text('ManDistances','Tournament');

	include('Common/Templates/head.php');

?>
	<div align="center">
		<div class="half">
			<table class="Tabella">
				<tbody id="tbody">
					<tr><th class="Title" colspan="<?php print $colspan+1; ?>"><?php print get_text('ManDistances','Tournament'); ?></th></tr>
					<tr>
						<th><?php print get_text('AvailableValues','Tournament'); ?></th>
						<th><?php print get_text('FilterOnDivCl','Tournament'); ?></th>
						<?php for ($i=1;$i<=$numDist;++$i) { ?>
							<th>.<?php print $i; ?>.</th>
						<?php } ?>
						<th>&nbsp;</th>
					</tr>
				<?php // righe per l'insert/edit ?>
					<tr id="edit">
						<td class="Center">
							<input type="hidden" id="type" value="<?php print $tourType; ?>">
							<?php
							foreach($AvDiv as $Div=>$Cl) {
								if($Cl) echo implode(', ',$Cl).'<br/>';
							}
							?>
						</td>
						<td class="Center">
							<input type="text" id="TdClasses" size="4" maxlength="4" value="">
						</td>
						<?php for ($i=1;$i<=$numDist;++$i) { ?>
							<td class="Center"><input type="text" id="Td<?php print $i; ?>" size="10" maxlength="32" value=""></td>
						<?php } ?>
						<td class="Center">
							<input type="button" name="command" value="<?php print get_text('CmdOk'); ?>" onclick="save(<?php print $numDist; ?>);">&nbsp;&nbsp;
							<input type="button" name="command" value="<?php print get_text('CmdCancel'); ?>" onclick="resetInput(<?php print $numDist; ?>);">
						</td>
					</tr>
					<tr class="Spacer"><td colspan="<?php print $colspan+1; ?>"></td></tr>
				<?php
					if ($rsDist!=null)
					{
						$k=0;
						while ($myRow=safe_fetch($rsDist)) {
							echo '<tr id="row_'.$k.'">
								<td>'.print_distances($DefinedDistances[$myRow->TdClasses]).'</td>
								<td class="Center" style="width:20%;"><div id="cl_'.$k.'">'.$myRow->TdClasses.'</div></td>';
							foreach(range(1, $numDist) as $i) {
								echo '<td class="Center"><div id="td_'.$k.'_'.$i.'">'.$myRow->{'Td' . $i}.'</div></td>';
							}
							echo '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" border="0" alt="#" title="#" onclick="deleteRow('.$k.', \''.$myRow->TdClasses.'\', '.$tourType.');"></td></tr>';
							++$k;
						}
					}
				?>
				</tbody>
			</table>
			<div>&nbsp;</div>
<?php

// DISTANCE INFORMATION MANAGEMENT
// Based on SESSIONS!!!!
require_once('./ManDistancesSessions.php');

?>
		</div>
	</div>
	<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');

function getDistances($ByDist=true) {
	$ar=array();

	$MySql="select DivId, ClId, TdClasses
		from Divisions
		inner join Classes on DivTournament=ClTournament and DivAthlete=ClAthlete
		inner join TournamentDistances on DivTournament=TdTournament and concat(trim(DivId),trim(ClId)) like TdClasses
		WHERE
			DivTournament={$_SESSION['TourId']}
			AND DivAthlete='1'
			AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed))
		".($ByDist ? "ans TdClasses='$ByDist'" : '')."
		order by
			DivViewOrder, ClViewOrder";

	$q=safe_r_sql($MySql);
	if($ByDist) {
		while($r=safe_fetch($q)) {
			$ar[]=$r->DivId.$r->ClId;
		}
	} else {
		while($r=safe_fetch($q)) {
			$ar[$r->TdClasses][$r->DivId][$r->ClId] = '1';
		}
	}

	return $ar;
}

function print_distances($DefinedDistances) {
	$ret='';
	foreach($DefinedDistances as $Div=>$Cl) {
		$ret.= $Div . ':&nbsp;';
		foreach($Cl as $class=>$def) {
			if($def) $class="<b>$class</b>";
			$ret.= $class . '&nbsp;';
		}
		$ret.='<br/>';
	}
	return $ret;
}
