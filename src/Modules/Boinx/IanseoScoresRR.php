<?php

require_once('./config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Modules/RoundRobin/Fun_F2F.local.inc.php');

include('Common/CheckPictures.php');
CheckPictures($TourCode);

// check where is the live flag
$rs=getElimPhaseMatchLive($TourId);

if(!safe_num_rows($rs)) {
	exit('No Live flag selected. Go to Ianseo Final/Team Spotting page and activate a Live Event');
}

$NumArrows=3;
$NumEnds=5;

$XmlDoc = new DOMDocument('1.0', 'UTF-8');
$XmlRoot = $XmlDoc->createElement('archeryscores');
$XmlDoc->appendChild($XmlRoot);

$Header = $XmlDoc->createElement('header');
$Header->appendChild($XmlDoc->createCDATASection(''));
$XmlRoot->appendChild($Header);

$Games = $XmlDoc->createElement('games');
$XmlRoot->appendChild($Games);

$oldEvent='';

while($row=safe_fetch($rs)) {
	$Game = $XmlDoc->createElement('game'.intval($row->target1));
	$Games->appendChild($Game);

	// Insert Event Name
	$Event = $XmlDoc->createElement('event');
	$Event->appendChild($XmlDoc->createCDATASection($row->EvEventName));
	$Game->appendChild($Event);

	// SetSystem
	$Event = $XmlDoc->createElement('ss');
	$Event->appendChild($XmlDoc->createCDATASection($row->EvMatchMode));
	$Game->appendChild($Event);

	// Insert Phase Name
	$Phase = $XmlDoc->createElement('phase');
	$Phase->appendChild($XmlDoc->createCDATASection('Phase 1'));
	$Game->appendChild($Phase);

	// create opponent 1
	$Opp1 = $XmlDoc->createElement('opponent1');
	$Game->appendChild($Opp1);

	// create opponent 2
	$Opp2 = $XmlDoc->createElement('opponent2');
	$Game->appendChild($Opp2);

	// targetno
	$Tg = $XmlDoc->createElement('targetno');
	$Tg->appendChild($XmlDoc->createCDATASection(ltrim($row->target1, '0')));
	$Opp1->appendChild($Tg);

	$Tg = $XmlDoc->createElement('targetno');
	$Tg->appendChild($XmlDoc->createCDATASection(ltrim($row->target2, '0')));
	$Opp2->appendChild($Tg);

	// name: Athlete name?
	$Ath1=$row->name1;
	$Ath2=$row->name2;
	$len=max(strlen($Ath1), strlen($Ath2));

	$pad1=GetPaddedNames($Ath1, $len);
	$pad2=GetPaddedNames($Ath2, $len);

	$Tg = $XmlDoc->createElement('name');
	$Tg->appendChild($XmlDoc->createCDATASection(str_pad($Ath1, $pad1, ' ')));
	$Opp1->appendChild($Tg);
	$Tg = $XmlDoc->createElement('name');
	$Tg->appendChild($XmlDoc->createCDATASection(str_pad($Ath2, $pad2, ' ')));
	$Opp2->appendChild($Tg);

	// shortname
	$Tg = $XmlDoc->createElement('shortname');
	$Tg->appendChild($XmlDoc->createCDATASection($row->countryCode1));
	$Opp1->appendChild($Tg);
	$Tg = $XmlDoc->createElement('shortname');
	$Tg->appendChild($XmlDoc->createCDATASection($row->countryCode2));
	$Opp2->appendChild($Tg);

	// component1
	$Tg = $XmlDoc->createElement('component1');
	$Tg->appendChild($XmlDoc->createCDATASection(''));
	$Opp1->appendChild($Tg);
	$Tg = $XmlDoc->createElement('component1');
	$Tg->appendChild($XmlDoc->createCDATASection(''));
	$Opp2->appendChild($Tg);

	// component2
	$Tg = $XmlDoc->createElement('component2');
	$Tg->appendChild($XmlDoc->createCDATASection(''));
	$Opp1->appendChild($Tg);
	$Tg = $XmlDoc->createElement('component2');
	$Tg->appendChild($XmlDoc->createCDATASection(''));
	$Opp2->appendChild($Tg);

	// component3
	$Tg = $XmlDoc->createElement('component3');
	$Tg->appendChild($XmlDoc->createCDATASection(''));
	$Opp1->appendChild($Tg);
	$Tg = $XmlDoc->createElement('component3');
	$Tg->appendChild($XmlDoc->createCDATASection(''));
	$Opp2->appendChild($Tg);

	// get the single ends => set
	$Arrows='';
	$OppArrows='';
	$EndNumber=0;
	$CurEnd=0;
	$opp1S=0;
	$opp2S=0;

	$Ends1=explode('|', $row->setpoints1);
	$Ends2=explode('|', $row->setpoints2);
	$Match1=explode('|', $row->matchpoints1);
	$Match2=explode('|', $row->matchpoints2);

	for($n=0; $n<5; $n++) {
		$End=substr($row->arrowstring1, $n*$NumArrows, $NumArrows);
		if(trim($End)) {
			$Arrows=$End;
			$OppArrows='';
		}
		$tot1=ValutaArrowString($End) . ($End!=strtoupper($End) ? '*' : '');

		$OppEnd=substr($row->arrowstring2, $n*$NumArrows, $NumArrows);
		if(trim($OppEnd)) {
			$Arrows=$End;
			$OppArrows=$OppEnd;
		}
		$tot2=ValutaArrowString($OppEnd) . ($OppEnd!=strtoupper($OppEnd) ? '*' : '');

		$len=max(strlen($tot1), strlen($tot2));
		$pad1=GetPaddedNames($tot1, $len);
		$pad2=GetPaddedNames($tot2, $len);

		$Tg = $XmlDoc->createElement('set'.($n+1));
		$Tg->appendChild($XmlDoc->createCDATASection(str_pad($Arrows ? $tot1 : '', $pad1, ' ', STR_PAD_LEFT)));
		$Opp1->appendChild($Tg);

		$Tg = $XmlDoc->createElement('set'.($n+1));
		$Tg->appendChild($XmlDoc->createCDATASection(str_pad($OppArrows ? $tot2 : '', $pad2, ' ', STR_PAD_LEFT)));
		$Opp2->appendChild($Tg);

		if($row->EvMatchMode) {
			$Tg = $XmlDoc->createElement('w'.($n+1));
			$Tg->appendChild($XmlDoc->createCDATASection(isset($Match1[$n]) ? $Match1[$n] : ''));
			$Opp1->appendChild($Tg);

			$Tg = $XmlDoc->createElement('w'.($n+1));
			$Tg->appendChild($XmlDoc->createCDATASection(isset($Match2[$n]) ? $Match2[$n] : ''));
			$Opp2->appendChild($Tg);

			// set points
			$Tg = $XmlDoc->createElement('s'.($n+1));
			$Tg->appendChild($XmlDoc->createCDATASection($row->score1));
			$Opp1->appendChild($Tg);

			$Tg = $XmlDoc->createElement('s'.($n+1));
			$Tg->appendChild($XmlDoc->createCDATASection($row->score2));
			$Opp2->appendChild($Tg);
		}

		if(strlen(str_replace(' ', '', $End))==$NumArrows and strlen(str_replace(' ', '', $OppEnd))==$NumArrows) $EndNumber=$n+1;
		if(trim($End) or trim($OppEnd)) $CurEnd=$n+1;
	}

	$Tg = $XmlDoc->createElement('endnumber');
	$Tg->appendChild($XmlDoc->createCDATASection($EndNumber));
	$Game->appendChild($Tg);

	$Tg = $XmlDoc->createElement('currentend');
	$Tg->appendChild($XmlDoc->createCDATASection($CurEnd));
	$Game->appendChild($Tg);

	$Tg = $XmlDoc->createElement('tie');
	$Tg->appendChild($XmlDoc->createCDATASection(''));
	$Opp1->appendChild($Tg);

	$Tg = $XmlDoc->createElement('tie');
	$Tg->appendChild($XmlDoc->createCDATASection(''));
	$Opp2->appendChild($Tg);

	// adding SO winner
	$Tg = $XmlDoc->createElement('sw');
	$Tg->appendChild($XmlDoc->createCDATASection(''));
	$Opp1->appendChild($Tg);

	$Tg = $XmlDoc->createElement('sw');
	$Tg->appendChild($XmlDoc->createCDATASection(''));
	$Opp2->appendChild($Tg);

	$tot1=ValutaArrowString($Arrows) . ($Arrows!=strtoupper($Arrows) ? '*' : '');
	$tot2=ValutaArrowString($OppArrows) . ($OppArrows!=strtoupper($OppArrows) ? '*' : '');
	$len=max(2, strlen($tot1), strlen($tot2));
	$pad1=GetPaddedNames($tot1, $len);
	$pad2=GetPaddedNames($tot2, $len);

	$Tg = $XmlDoc->createElement('setpoints');
	$Tg->appendChild($XmlDoc->createCDATASection(str_pad($tot1, $pad1, ' ', STR_PAD_LEFT)));
	$Opp1->appendChild($Tg);
	$Tg = $XmlDoc->createElement('setpoints');
	$Tg->appendChild($XmlDoc->createCDATASection(str_pad($tot2, $pad2, ' ', STR_PAD_LEFT)));
	$Opp2->appendChild($Tg);

	// arrows
	for($n=0; $n<6; $n++) {
		$End1=substr($Arrows, $n, 1);
		$tot1=ValutaArrowString($End1);
		$End2=substr($OppArrows, $n, 1);
		$tot2=ValutaArrowString($End2);

		$len=max(2, strlen($tot1), strlen($tot2));
		$pad1=GetPaddedNames($tot1, $len);
		$pad2=GetPaddedNames($tot2, $len);
		if($End1!='A' and !$tot1) $tot1='';
		if($End2!='A' and !$tot2) $tot2='';

		$Tg = $XmlDoc->createElement('arrow'.($n+1));
		$Tg->appendChild($XmlDoc->createCDATASection(str_pad($tot1, $pad1, ' ', STR_PAD_LEFT)));
		$Opp1->appendChild($Tg);

		$Tg = $XmlDoc->createElement('arrow'.($n+1));
		$Tg->appendChild($XmlDoc->createCDATASection(str_pad($tot2, $pad2, ' ', STR_PAD_LEFT)));
		$Opp2->appendChild($Tg);
	}

	// total
	$Tg = $XmlDoc->createElement('total');
	$Tg->appendChild($XmlDoc->createCDATASection($row->score1));
	$Opp1->appendChild($Tg);
	$Tg = $XmlDoc->createElement('total');
	$Tg->appendChild($XmlDoc->createCDATASection($row->score2));
	$Opp2->appendChild($Tg);

	// setscore
	$Tg = $XmlDoc->createElement('setscore');
	$Tg->appendChild($XmlDoc->createCDATASection($row->setscore1));
	$Opp1->appendChild($Tg);
	$Tg = $XmlDoc->createElement('setscore');
	$Tg->appendChild($XmlDoc->createCDATASection($row->setscore2));
	$Opp2->appendChild($Tg);

	$fotodir='http://' . $_SERVER['HTTP_HOST'] . $CFG->ROOT_DIR . 'TV/Photos/' . $TourCodeSafe . '-%s-%s.jpg';

	// flag
	$Tg = $XmlDoc->createElement('flag');
	$Tg->appendChild($XmlDoc->createCDATASection(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$row->countryCode1.'.jpg')?sprintf($fotodir, 'Fl', $row->countryCode1):''));
	$Opp1->appendChild($Tg);
	$Tg = $XmlDoc->createElement('flag');
	$Tg->appendChild($XmlDoc->createCDATASection(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-Fl-'.$row->countryCode2.'.jpg')?sprintf($fotodir, 'Fl', $row->countryCode2):''));
	$Opp2->appendChild($Tg);


	// photo1
	$Tg = $XmlDoc->createElement('photo1');
	$Tg->appendChild($XmlDoc->createCDATASection(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-En-'.$row->athId1.'.jpg')?sprintf($fotodir, 'En', $row->athId1):''));
	$Opp1->appendChild($Tg);
	$Tg = $XmlDoc->createElement('photo1');
	$Tg->appendChild($XmlDoc->createCDATASection(file_exists($CFG->DOCUMENT_PATH.'TV/Photos/'.$TourCodeSafe.'-En-'.$row->athId2.'.jpg')?sprintf($fotodir, 'En', $row->athId2):''));
	$Opp2->appendChild($Tg);

	// photo2
	$Tg = $XmlDoc->createElement('photo2');
	$Tg->appendChild($XmlDoc->createCDATASection(''));
	$Opp1->appendChild($Tg);
	$Tg = $XmlDoc->createElement('photo2');
	$Tg->appendChild($XmlDoc->createCDATASection(''));
	$Opp2->appendChild($Tg);

	// photo3
	$Tg = $XmlDoc->createElement('photo3');
	$Tg->appendChild($XmlDoc->createCDATASection(''));
	$Opp1->appendChild($Tg);
	$Tg = $XmlDoc->createElement('photo3');
	$Tg->appendChild($XmlDoc->createCDATASection(''));
	$Opp2->appendChild($Tg);

	// all arrows
	for($n=0; $n<15; $n++) {
		$a=DecodeFromLetter(substr($row->arrowstring1, $n, 1));
		$b=DecodeFromLetter(substr($row->arrowstring2, $n, 1));
		$Tg = $XmlDoc->createElement('a'.($n+1));
		$Tg->appendChild($XmlDoc->createCDATASection($a));
		$Opp1->appendChild($Tg);
		$Tg = $XmlDoc->createElement('a'.($n+1));
		$Tg->appendChild($XmlDoc->createCDATASection($b));
		$Opp2->appendChild($Tg);
	}

	$Tg = $XmlDoc->createElement('xtowin', '');
	$Opp1->appendChild($Tg);
	$Tg = $XmlDoc->createElement('xtowin', '');
	$Opp2->appendChild($Tg);

	$Tg = $XmlDoc->createElement('xtodescr', '');
	$Opp1->appendChild($Tg);
	$Tg = $XmlDoc->createElement('xtodescr', '');
	$Opp2->appendChild($Tg);
}


if(empty($EXCLUDE_HEADER)) {
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);
	echo $XmlDoc->SaveXML();

	die();
}
