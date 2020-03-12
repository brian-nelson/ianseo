<?php
require_once('./config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/Fun_Scheduler.php');
require_once('Modules/RoundRobin/Fun_F2F.local.inc.php');

$fotodir='http://' . $_SERVER['HTTP_HOST'] . $CFG->ROOT_DIR . 'TV/Photos/' . $TourCodeSafe . '-%s-%s.jpg';
// $ENTRY->ArcherPic=(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-En-'.$ENTRY->EnId.'.jpg') ? sprintf($fotodir, 'En', $ENTRY->EnId) : '');
// $ENTRY->CountryPic=(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$ENTRY->CoCode.'.jpg') ? sprintf($fotodir, 'Fl', $ENTRY->CoCode) : '');

$XmlDoc = new DOMDocument('1.0', 'UTF-8');
$XmlRoot = $XmlDoc->createElement('roundrobin');
$XmlDoc->appendChild($XmlRoot);

$dataRankGroup=getRankGroup(array('RM', 'RW'), array(1), false, $TourId);
foreach($dataRankGroup as $EvCode => $Phases) {
	$Event=$XmlDoc->createElement('event');
	$XmlRoot->AppendChild($Event);

	$Event->AppendChild($XmlDoc->createElement('code', $EvCode));
	$Event->AppendChild($XmlDoc->createElement('name', $Phases['descr']));

	$OldGroup=0;
	$archer=1;
	foreach($Phases['phases']['1']['items'] as $item) {
		if($OldGroup!=$item['group']) {
			$Group=$XmlDoc->createElement('group');
			$Event->AppendChild($Group);

			$Group->AppendChild($XmlDoc->createElement('group', $item['group']));
			$OldGroup=$item['group'];
			$archer=1;
		}

		$arc=$XmlDoc->createElement('arch'.$archer);
		$Group->AppendChild($arc);

		$arc->AppendChild($XmlDoc->createElement('name', $item['athlete']));
		$arc->AppendChild($XmlDoc->createElement('code', $item['countryCode']));
		$arc->AppendChild($XmlDoc->createElement('country', $item['countryName']));
		$arc->AppendChild($XmlDoc->createElement('rank', $item['rank']));
		$arc->AppendChild($XmlDoc->createElement('score', $item['points']));
		$arc->AppendChild($XmlDoc->createElement('average', $item['score']));
		$arc->AppendChild($XmlDoc->createElement('so', $item['tiescore']));

		$archer++;
	}
}


header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);

echo $XmlDoc->SaveXML();
die();


/*
<round>1</round>

<arch1>
<name>DEMASI Pasquale</name>
<country>Tanzania</country>
<rank>1</rank>
<score>2</score>
<average></average>
<so>28</so>
</arch1>


<arch2>
<name>DEMASI Pasquale</name>
<country>Tanzania</country>
<rank>1</rank>
<score>2</score>
<average></average>
<so>28</so>
</arch2>
*/


$a=$XmlDoc->createElement('name');
$a->AppendChild($XmlDoc->createCDATASection($ENTRY->Name));
$XmlRoot->AppendChild($a);

$a=$XmlDoc->createElement('country');
$a->AppendChild($XmlDoc->createCDATASection($ENTRY->Country));
$XmlRoot->AppendChild($a);

$a=$XmlDoc->createElement('bib');
$a->AppendChild($XmlDoc->createCDATASection(ltrim($ENTRY->Bib, '0')));
$XmlRoot->AppendChild($a);

$a=$XmlDoc->createElement('score');
$a->AppendChild($XmlDoc->createCDATASection($ENTRY->QuScore ? $ENTRY->QuScore : ''));
$XmlRoot->AppendChild($a);

$a=$XmlDoc->createElement('catrank');
$a->AppendChild($XmlDoc->createCDATASection($ENTRY->QuRank ? $ENTRY->QuRank : ''));
$XmlRoot->AppendChild($a);

$a=$XmlDoc->createElement('absrank');
$a->AppendChild($XmlDoc->createCDATASection($ENTRY->IndRank ? abs($ENTRY->IndRank) : ''));
$XmlRoot->AppendChild($a);

$a=$XmlDoc->createElement('categoryshort');
$a->AppendChild($XmlDoc->createCDATASection($ENTRY->CategoryShort));
$XmlRoot->AppendChild($a);

$a=$XmlDoc->createElement('categorylong');
$a->AppendChild($XmlDoc->createCDATASection($ENTRY->CategoryLong));
$XmlRoot->AppendChild($a);

$a=$XmlDoc->createElement('eventlong');
$a->AppendChild($XmlDoc->createCDATASection($ENTRY->EventLong));
$XmlRoot->AppendChild($a);

$a=$XmlDoc->createElement('eventshort');
$a->AppendChild($XmlDoc->createCDATASection($ENTRY->EventShort));
$XmlRoot->AppendChild($a);

$a=$XmlDoc->createElement('countrypic');
$a->AppendChild($XmlDoc->createCDATASection($ENTRY->CountryPic));
$XmlRoot->AppendChild($a);

$a=$XmlDoc->createElement('archerpic');
$a->AppendChild($XmlDoc->createCDATASection($ENTRY->ArcherPic));
$XmlRoot->AppendChild($a);

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);

echo $XmlDoc->SaveXML();

/*
[15:48:27] Ardingo: basta e avanza
[15:48:35] Christian Deligant: 7) link all'immagine della società
[15:48:40] Ardingo: se ci aggiungi anche la foto poi lo uso a far anche un'altra cosa
[15:48:41] Christian Deligant: 8) link alla foto atleta
*/
