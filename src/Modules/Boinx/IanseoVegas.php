<?php
require_once('./config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/Fun_Scheduler.php');

require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Fun_Modules.php');
// require_once('Common/Fun_Phases.inc.php');
//require_once('Common/Fun_Phases.inc.php');


CreateTourSession(getIdFromCode($_GET['Tour']));
$StartEnds = getModuleParameter("Vegas", "EndNo", 0)*3;

$q=safe_r_sql("select EnFirstname, EnName, QuTargetNo, QuD5Arrowstring from Qualifications
		inner join Entries on EnId=QuId and EnTournament=$TourId
		Where QuSession=8
		order by QuTargetNo");

$XmlDoc = new DOMDocument('1.0', 'UTF-8');
$XmlRoot = $XmlDoc->createElement('shootoff');
$XmlDoc->appendChild($XmlRoot);

$Ends=0;
$n=0;
while($r=safe_fetch($q)) {
	$archer=$XmlDoc->createElement('archer'.$n++);
	$XmlRoot->AppendChild($archer);

	$a=$XmlDoc->createElement('name');
	$a->AppendChild($XmlDoc->createCDATASection(substr($r->EnFirstname, 0, 3) . ' ' . $r->EnName[0]));
	$archer->AppendChild($a);

	$a=$XmlDoc->createElement('target');
	$a->AppendChild($XmlDoc->createCDATASection(ltrim(substr($r->QuTargetNo, 1, -1), '0')));
	$archer->AppendChild($a);

	$Arrows=substr($r->QuD5Arrowstring, $StartEnds, 3);
	$Star=($Arrows==strtoupper($Arrows) ? '' : '*');
	$a=$XmlDoc->createElement('score');
	$a->AppendChild($XmlDoc->createCDATASection(ValutaArrowString($Arrows).$Star));
	$archer->AppendChild($a);
}

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);

echo $XmlDoc->SaveXML();

/*
[15:48:27] Ardingo: basta e avanza
[15:48:35] Christian Deligant: 7) link all'immagine della società
[15:48:40] Ardingo: se ci aggiungi anche la foto poi lo uso a far anche un'altra cosa
[15:48:41] Christian Deligant: 8) link alla foto atleta
*/
