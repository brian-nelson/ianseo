<?php
	require_once('HHT/serial.php');
	require_once('HHT/Fun_HHT.local.inc.php');


echo '<table width="100%">';
echo '<tr>';
echo '<th width="15%"><input type="checkbox" value="1" id="dispDanage">&nbsp;' . get_text('CmdEnable') . '</th>';
echo '<td width="10%"><select id="x_Contrast">';
for($i=9; $i>=09; $i--)
	echo '<option value="' . $i . '">' . $i . '</option>';
echo '</select></td>';
echo '<td width="15%">' . ComboHHT() . '</td>';
echo '<td width="25%" class="Center"><select id="x_DispType">';
echo '<option value="1">Big Scoring Display</option>';
echo '<option value="2">Timing Display</option>';
echo '</select></td>';
echo '<td width="10%"><input type="checkbox" value="1" id="x_autoRefresh">&nbsp;' . get_text('AutoRefresh','Tournament') . '</td>';
echo '<td width="25%" class="Center"><input type="button" value="Refresh" name="dispRefresh" onClick="refreshDisplay();"></td>';
echo '</tr>';

echo "</table>";
 
?>