<?php
require_once('./config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/Fun_Scheduler.php');

// require_once('Common/Lib/ArrTargets.inc.php');
// require_once('Common/Fun_Phases.inc.php');
//require_once('Common/Fun_Phases.inc.php');

$ENTRY='';
$q=safe_r_sql("select * from BoinxSchedule where BsTournament=$TourId and BsType='Qua_Ind'");

if(($r=safe_fetch($q))) {
	$SQL="select substr(QuTargetNo, 2) Bib
		, concat(EnName, ' ', upper(EnFirstName)) Name
		, concat(CoCode, '-', CoName) Country
		, QuScore, QuClRank QuRank, IndRank
		, concat(DivDescription, ' ', ClDescription) CategoryLong
		, concat(DivId, ClId) CategoryShort
		, EvCode EventShort, EvEventName EventLong
		, EnId, CoCode
		from Entries
		inner join Qualifications on EnId=QuId
		inner join Countries on EnCountry=CoId
		inner join Divisions on EnDivision=DivId and EnTournament=DivTournament
		inner join Classes on EnClass=ClId and EnTournament=ClTournament
		left join Individuals on IndId=EnId
		left join Events on IndEvent=EvCode and EnTournament=EvTournament
		where QuTargetNo='$r->BsExtra' and EnTournament=$TourId
		";
	$q=safe_r_sql($SQL);
	$ENTRY=safe_fetch($q);
}

if(!$ENTRY) {
	$ENTRY=new StdClass();
	$ENTRY->EnId='';
	$ENTRY->CoCode='';
	$ENTRY->Bib='';
	$ENTRY->Name='';
	$ENTRY->Country='';
	$ENTRY->QuScore='';
	$ENTRY->QuRank='';
	$ENTRY->IndRank='';
	$ENTRY->CategoryLong='';
	$ENTRY->CategoryShort='';
	$ENTRY->EventShort='';
	$ENTRY->EventLong='';
}

$fotodir='http://' . $_SERVER['HTTP_HOST'] . $CFG->ROOT_DIR . 'TV/Photos/' . $TourCodeSafe . '-%s-%s.jpg';
$ENTRY->ArcherPic=(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-En-'.$ENTRY->EnId.'.jpg') ? sprintf($fotodir, 'En', $ENTRY->EnId) : '');
$ENTRY->CountryPic=(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$ENTRY->CoCode.'.jpg') ? sprintf($fotodir, 'Fl', $ENTRY->CoCode) : '');

$XmlDoc = new DOMDocument('1.0', 'UTF-8');
$XmlRoot = $XmlDoc->createElement('archer');
$XmlDoc->appendChild($XmlRoot);

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
