<?php

require_once('../../config.php');

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

	$XmlRoot->appendChild($Item = $XmlDoc->createElement('addresstowin'));
		$Item->appendChild($XmlDoc->createCDATASection('http://localhost:8888/ianseo/Modules/Boinx/Ianseoxtowin.xml'));

	$XmlRoot->appendChild($Item = $XmlDoc->createElement('danagetiming'));
		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, '../DanageDisplay/Timing.php')));

	$XmlRoot->appendChild($Item = $XmlDoc->createElement('roundrobinscore'));
		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, 'IanseoScoresRR.php')));

	$XmlRoot->appendChild($Item = $XmlDoc->createElement('roundrobinrank'));
		$Item->appendChild($XmlDoc->createCDATASection(sprintf($URLTemplate, 'IanseoQualRR.php')));

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


require_once('Common/Fun_FormatText.inc.php');

$PAGE_TITLE=get_text('TitleTourMenu', 'Tournament');

include('Common/Templates/head.php');

?>
<h1><?php echo get_text('HowToUse', 'Boinx'); ?></h1>

<p><b><?php echo get_text('StepImportLayers', 'Boinx', '1') ; ?></b><br />
<?php echo get_text('StepImportLayersText', 'Boinx') ; ?></p>
<table class="Tabella">
<tr>
<th class="Title" width="15%" nowrap="nowrap"><?php print get_text('Download','Boinx');?></th>
<th class="Title" width="85%"><?php print get_text('BoinxLayer','Boinx');?></th>
</tr>

<?php

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
