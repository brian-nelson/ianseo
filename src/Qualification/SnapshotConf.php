<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

CheckTourSession(true);
checkACL(AclQualification, AclReadWrite);

require_once('Common/Fun_Sessions.inc.php');

$JS_SCRIPT = array(
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
    '<script type="text/javascript" src="SnapshotConf.js"></script>',
    );
$PAGE_TITLE=get_text('SnapshotConf');

$Select = "SELECT ToId,ToNumSession,ToNumDist AS TtNumDist 
  FROM Tournament 
  WHERE ToId={$_SESSION['TourId']}";
$RsTour=safe_r_sql($Select);
$RowTour=safe_fetch($RsTour);

include('Common/Templates/head.php');

echo '<div align="center">';
echo '<div class="half">';
echo '<table class="Tabella">';
echo '<tr><th class="Title" colspan="3">'.get_text('SnapshotConf').'</th></tr>';
echo '<tr><th style="width:30%">'.get_text('CalcSnapshot', 'Tournament').'</th><td colspan="2"><input type="checkbox" id="snapshot" '. (getModuleParameter('ISK', 'Snapshot') ? 'checked="checked"' : '') .' onclick="toggleSnapshot(this)"></td></tr>';

echo '<tbody id="Conf" '.(getModuleParameter('ISK', 'Snapshot') ? '' : 'style="display:none"').'>';
$sessions=GetSessions('Q');
$tar4session=array();
$TrueSessions=array();
foreach ($sessions as $s) {
	$tar4session[$s->SesOrder]=$s->SesTar4Session;
	$TrueSessions[$s->SesOrder]=$s;
}

for($ses=1; $ses<=$RowTour->ToNumSession; $ses++) {
	echo '<tr><td colspan="3"></td></tr>';
	echo "<tr>";
	echo '<th rowspan="' . $RowTour->TtNumDist . '">' . get_text('Session') . ':&nbsp;' . $ses . '</th>';
	for($dist=1; $dist<=$RowTour->TtNumDist; $dist++)
	{
		echo '<td style="width:40%">
			<input type="button" value="'.htmlspecialchars(get_text('CalcSnapshotDist', 'Tournament', $dist)).'" onclick="rebuildSnapshot('.$ses.', '.$dist.', '.$TrueSessions[$ses]->SesFirstTarget.', '.($TrueSessions[$ses]->SesFirstTarget+$TrueSessions[$ses]->SesTar4Session-1).')">
			</td>';
		echo '<td style="width:30%"><a href="'.$CFG->ROOT_DIR.'Qualification/PrnCheckout.php?Session=' . $ses . '&Distance=' . $dist . '" target="PrintCheckOut">' . get_text('Print', 'Tournament') . '</a></td>';
		if($dist != $RowTour->TtNumDist)
			echo '</tr><tr>';
	}
	echo "</tr>";
}
echo '</tbody>';
echo '</table>';
echo '</div>';
echo '</div>';

include('Common/Templates/tail.php');
