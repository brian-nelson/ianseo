<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Fun_HHT.local.inc.php');
	require_once('Common/Fun_Sessions.inc.php');

	CheckTourSession(true);

	$select =
		"SELECT HsId, HsName, HsIpAddress, HsPort FROM HhtSetup WHERE HsTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY HsId";
	$rsHht=safe_r_sql($select);

	$JS_SCRIPT = array(
		'<script type="text/javascript" src="../Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="Fun_AJAX_Configuration.js"></script>',
		'<script type="text/javascript">var StrConfirm="' . get_text('MsgAreYouSure') . '";</script>',
		);
	$PAGE_TITLE=get_text('HTTSocket','HTT');

	include('Common/Templates/head.php');
?>
<div align="center">
	<div class="half">
		<table class="Tabella">
			<tbody id="tbody">
			<tr>
				<th class="Title" colspan="4"><?php print get_text('HTTSocket','HTT'); ?></th>
			</tr>
			<tr>
				<th style="width:30%;"><?php print get_text('Name','HTT'); ?></th>
				<th style="width:30%;"><?php print get_text('Host','HTT'); ?></th>
				<th style="width:20%;"><?php print get_text('Port','HTT'); ?></th>
				<th style="width:20%;">&nbsp;</th>
			</tr>
			<tr id="edit">
				<td class="Center">
					<input type="text" id="HhtName" size="20" maxlength="165" value="">
				</td>
				<td class="Center">
					<input type="text" id="HhtIpAddress" size="20" maxlength="16" value="">
				</td>
				<td class="Center">
					<input type="text" id="HhtIpPort" size="10" maxlength="5" value="9001">
				</td>
				<td class="Center">
					<input type="button" name="command" value="<?php print get_text('CmdOk'); ?>" onclick="saveHht();">&nbsp;&nbsp;
					<input type="button" name="command" value="<?php print get_text('CmdCancel'); ?>" onclick="resetInputHht();">
				</td>
			</tr>
			<tr class="Spacer"><td colspan="3"></td></tr>
			<?php
				if ($rsHht!=null)
				{
					while ($myRow=safe_fetch($rsHht))
					{
						echo '<tr id="row_' . $myRow->HsId . '">';
						echo '<td class="Center"><div id="ip_' . $myRow->HsId . '"><a href="ConfDetails.php?Id=' . $myRow->HsId . '">' . $myRow->HsName . '</a></div></td>';
						echo '<td class="Center"><div id="ip_' . $myRow->HsId . '">' . $myRow->HsIpAddress . '</div></td>';
						echo '<td class="Center"><div id="port_' . $myRow->HsId . '">' . $myRow->HsPort . '</div></td>';
						echo '<td class="Center"><img src="' . $CFG->ROOT_DIR . 'Common/Images/drop.png" border="0" alt="#" title="#" onclick="deleteHht(' . $myRow->HsId . ');"></td>';
						echo '</tr>';
					}
				}
			?>
			</tbody>
		</table>
		<br>
		<table class="Tabella">
			<tr><th colspan="3"><?php print get_text('CalcSnapshot', 'Tournament'); ?></th></tr>
			<?php
				$RowTour=RowTour();

				$sessions=GetSessions('Q');
				$tar4session=array();
				$TrueSessions=array();
				foreach ($sessions as $s)
				{
					$tar4session[$s->SesOrder]=$s->SesTar4Session;
					$TrueSessions[$s->SesOrder]=$s;
				}

				for($ses=1; $ses<=$RowTour->ToNumSession; $ses++)
				{
					echo "<tr>";
					echo '<th class="TitleLeft" style="width:15%;" rowspan="' . $RowTour->TtNumDist . '">' . get_text('Session') . ':&nbsp;' . $ses . '</th>';
					for($dist=1; $dist<=$RowTour->TtNumDist; $dist++)
					{
						echo '<td style="width:50%;"><a href="../Qualification/MakeSnapshot.php?Session=' . $ses . '&Distance=' . $dist. '&fromTarget='.$TrueSessions[$ses]->SesFirstTarget.'&toTarget=' . ($TrueSessions[$ses]->SesFirstTarget+$TrueSessions[$ses]->SesTar4Session-1) . '&numArrows=0" target="_blank">' . get_text('CalcSnapshotDist', 'Tournament', $dist) . '</a></td>';
						echo '<td style="width:35%;"><a href="./PrnCheckout.php?Session=' . $ses . '&Distance=' . $dist . '" target="PrintCheckOut">' . get_text('Print', 'Tournament') . '</a></td>';
						if($dist != $RowTour->TtNumDist)
							echo '</tr><tr>';
					}
					echo "</tr>";
					echo '<tr><td colspan="3"></td></tr>';
				}

			?>
		</table>
	</div>
</div>
<?php
	include('Common/Templates/tail.php');
?>