<?php

require_once('../../config.php');
require_once('Common/Lib/Fun_Modules.php');

if(!empty($_GET['ToCode'])) {
	$URLTemplate="http://{$_SERVER['HTTP_HOST']}".dirname($_SERVER['PHP_SELF'])."/%s?Tour={$_GET['ToCode']}";
	$XmlDoc = new DOMDocument('1.0', 'UTF-8');
	$XmlDoc->appendChild($XmlRoot = $XmlDoc->createElement('archeryscores'));
	$XmlRoot->appendChild($Item = $XmlDoc->createElement('address5set'));
		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, 'IanseoScores.php')));

	$XmlRoot->appendChild($Item = $XmlDoc->createElement('addressteam'));
		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, 'IanseoScores.php')));

	$XmlRoot->appendChild($Item = $XmlDoc->createElement('addressgrid'));
		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, 'IanseoGrids.php')));

	$XmlRoot->appendChild($Item = $XmlDoc->createElement('addressaward'));
		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, 'IanseoAwards.php')));

	$XmlRoot->appendChild($Item = $XmlDoc->createElement('addressrss'));
		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, 'IanseoRSS.php')));

	$XmlRoot->appendChild($Item = $XmlDoc->createElement('addressmeteo'));
		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, 'IanseoMeteo.php')));

	$XmlRoot->appendChild($Item = $XmlDoc->createElement('addressscheduler'));
		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, 'IanseoScheduler.php')));

	$XmlRoot->appendChild($Item = $XmlDoc->createElement('addressarcherbib'));
		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, 'IanseoQualInd.php')));

	$XmlRoot->appendChild($Item = $XmlDoc->createElement('addressvegas'));
		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, 'IanseoVegas.php')));

// 	$XmlRoot->appendChild($Item = $XmlDoc->createElement('addresstowin'));
// 		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, 'Ianseoxtowin.xml'));

	$XmlRoot->appendChild($Item = $XmlDoc->createElement('danagetiming'));
		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, '../DanageDisplay/Timing.php')));

	$XmlRoot->appendChild($Item = $XmlDoc->createElement('roundrobinscore'));
		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, 'IanseoScoresRR.php')));

	$XmlRoot->appendChild($Item = $XmlDoc->createElement('roundrobinrank'));
		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, 'IanseoQualRR.php')));

	$XmlRoot->appendChild($Item = $XmlDoc->createElement('rankinground'));
		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, 'IanseoRankingRound.php')));

	$XmlRoot->appendChild($Item = $XmlDoc->createElement('graphicrss'));
		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, 'IanseoRSSGraphic.php')));

	$XmlRoot->appendChild($Item = $XmlDoc->createElement('ianseorot'));
		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, 'IanseoRot.php')));

	$XmlRoot->appendChild($Item = $XmlDoc->createElement('clubteamphaserank'));
		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, '../ClubTeam/BoinxPhaseRank.php')));

	$XmlRoot->appendChild($Item = $XmlDoc->createElement('clubteamphasematch'));
		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, '../ClubTeam/BoinxPhaseMatch.php')));

	$XmlRoot->appendChild($Item = $XmlDoc->createElement('clubteamrss'));
		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, '../ClubTeam/BoinxRSS.php')));

	// We'll be outputting a XML
	// It will be called boinxianseo.xml
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);
	header('Content-Disposition: attachment; filename="boinxianseo.xml"');
	echo $XmlDoc->SaveXML();

	die();
}

chdir('Layers');

$Layers=glob('*.qtz');

if(!empty($_GET['Layer']) and in_array($_GET['Layer'], $Layers)) {
	// We'll be outputting a boix layer
	header('Content-type: application/x-quartzcomposer');
	// It will be called boinxianseo.xml
	header('Content-Disposition: attachment; filename="'.stripslashes($_GET['Layer']).'"');

	readfile(stripslashes($_GET['Layer']));
	die();
}

if(!empty($_REQUEST['SavePath'])) setModuleParameter('Boinx', 'SavePath', $_REQUEST['SavePath']);
if(!empty($_REQUEST['Write'])) setModuleParameter('Boinx', 'Write', $_REQUEST['Write']);

require_once('Common/Fun_FormatText.inc.php');

$PAGE_TITLE=get_text('TitleTourMenu', 'Tournament');

include('Common/Templates/head.php');

echo '<h1>'.get_text('HowToUse', 'Boinx').'</h1>';

if(ProgramRelease!='STABLE' and ProgramRelease!='FITARCO' and !empty($_SESSION['TourId'])) {
	echo '<div><form>';
	echo '<table class="Tabella">';
	echo '<tr>';
	echo '<th class="head" width="15%" nowrap="nowrap">'.get_text('SavePath','Boinx').'</th>';
	echo '<td width="85%">';
	$tmp=getModuleParameter('Boinx', 'SavePath');
	if($tmp and !is_writable($tmp)) echo '<div class="Warning">'.get_text('DirNotWriteable', 'Boinx').'</div>';
	echo '<input style="width:100%" type="text" name="SavePath" value="'. $tmp .'"></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<th class="head" width="15%" nowrap="nowrap">'.get_text('Activate','Boinx').'</th>';
	$tmp=getModuleParameter('Boinx', 'Write');
	echo '<td width="85%">'.get_text('Yes').'<input type="radio" name="Write" value="1"'.($tmp==1 ? ' checked="checked"' : '') .'><input type="radio" name="Write" value="0"'.($tmp==0 ? ' checked="checked"' : '') .'>'.get_text('No').'</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td colspan="2"><input type="submit" value="'.get_text('CmdSave') .'"></td>';
	echo '</tr>';
	echo '</table>';
	echo '</form></div>';
}

echo '<p><b>'.get_text('StepImportLayers', 'Boinx', '1').'</b><br />';
echo get_text('StepImportLayersText', 'Boinx');
echo '</p>';

echo '<table class="Tabella">';
echo '<tr>';
echo '<th class="Title" width="15%" nowrap="nowrap">'.get_text('Download','Boinx').'</th>';
echo '<th class="Title" width="85%">'.get_text('BoinxLayer','Boinx').'</th>';
echo '</tr>';


foreach($Layers as $Layer) {
	echo '<tr>';
	echo '<td><a href="?Layer='.$Layer.'">'.$Layer.'</a></td>';
	print '<td>' . get_text($Layer, 'Boinx') . '</td>';
	echo '</tr>';
}

?>
</table>

<p><b><?php echo get_text('StepImportXML', 'Boinx', '2') ; ?></b><br />
<?php echo get_text('StepImportXMLText', 'Boinx', 'boinxianseo.xml') ; ?></p>
<table class="Tabella">
<tr>
<th class="Title" width="5%" nowrap="nowrap"><?php print get_text('XMLConfiguration','Boinx');?></th>
<th class="Title" width="5%" nowrap="nowrap"><?php print get_text('TourCode','Tournament');?></th>
<th class="Title" width="10%" nowrap="nowrap"><?php print get_text('TourWhen','Tournament');?></th>
<th class="Title" width="30%"><?php print get_text('TourWhere','Tournament');?></th>
<th class="Title" width="50%"><?php print get_text('TourName','Tournament');?></th>
</tr>

<?php

$q=safe_r_sql("select ToCode, ToName, ToWhere, DATE_FORMAT(ToWhenFrom,'" . get_text('DateFmtDB') . "') AS DtFrom, "
		. "DATE_FORMAT(ToWhenTo,'" . get_text('DateFmtDB') . "') AS DtTo from Tournament order by ToWhenTo desc, ToWhenFrom desc");
while($r=safe_fetch($q)) {
	echo '<tr>';
	echo '<td><a href="?ToCode='.$r->ToCode.'">'.get_text('Download', 'Boinx').'</a></td>';
	print '<td>' . $r->ToCode . '</td>';
	print '<td nowrap="nowrap">' . get_text('From','Tournament') . ' ' . $r->DtFrom . ' ' . get_text('To','Tournament') . ' ' . $r->DtTo . '</td>';
	print '<td>' . ManageHTML($r->ToWhere) . '</td>';
	print '<td>' . ManageHTML($r->ToName) . '</td>';
	echo '</tr>';
}

?>
</table>
<?php include('Common/Templates/tail.php'); ?>
