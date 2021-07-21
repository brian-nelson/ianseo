<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/OrisPDF.inc.php');
checkACl(AclCompetition,AclReadOnly);

$arrPosition=array('','1st','2nd','3rd','4th','5th');
$par_RepresentCountry = getModuleParameter('Awards','RepresentCountry',1);
$par_PlayAnthem = getModuleParameter('Awards','PlayAnthem',1);


$pdf = new OrisPDF('Awards',get_text('MenuLM_PrintAwards'));
// $pdf->FontStd='droidsansfallback';
// $pdf->FontFix='droidsansfallback';
$pdf->SetFont($pdf->FontStd,'',8);

$pdf->FontStd2=$pdf->FontStd;
$pdf->FontFix2=$pdf->FontFix;

$pdf->FirstLang=(getModuleParameter('Awards', 'FirstLanguageCode') ? getModuleParameter('Awards', 'FirstLanguageCode') : ($_SESSION['TourPrintLang'] ? $_SESSION['TourPrintLang'] : SelectLanguage())); // set to empty string to avoid double printout
$pdf->SecondLang=''; // set to empty string to avoid double printout
$pdf->AddPage();
$pdf->SetTopMargin($pdf->lastY+1);
$pdf->sety($pdf->lastY+1);

if(getModuleParameter('Awards', 'SecondLanguage')) {
	$pdf->SecondLang=getModuleParameter('Awards', 'SecondLanguageCode');
	if(in_array($pdf->SecondLang, array('zh-cn', 'zh-tw'))) {
		$pdf->FontStd2='droidsansfallback';
		$pdf->FontFix2='droidsansfallback';
	}
    if(in_array($pdf->SecondLang, array('ja', 'ja-jp'))) {
        $pdf->FontStd2='arialuni';
        $pdf->FontFix2='arialuni';
    }
}
$pdf->SetAutoPageBreak(true, 20);

$LangCol=($pdf->getpagewidth()-20)/($pdf->SecondLang ? 2 : 1);

// Get all the texts in 2 columns
$Y=$pdf->gety();
$pdf->multicell($LangCol, 6, getModuleParameter('Awards', 'Aw-Intro-1'), 'R', 'L');
$Y1=$pdf->gety();
if($pdf->SecondLang) {
	$pdf->SetFont($pdf->FontStd2);
    if($Y1<$Y) {
        $pdf->setPage($pdf->getPage()-1);
    }
	$pdf->setXY($LangCol+10, $Y);
	$pdf->multicell($LangCol, 6, getModuleParameter('Awards', 'Aw-Intro-2'), 'L', 'L');
	$Y1=max($Y1, $pdf->gety());
	$pdf->sety($Y1, true);
	$pdf->SetFont($pdf->FontStd);
}
$pdf->ln(3);

$sql=array();
// get all the awarded categories individual
$sql[] = "SELECT distinct AwEventTrans, AwFinEvent, AwTeam, CONCAT(DivId, ClId) as EvCode, CONCAT(DivDescription, ' ', ClDescription) as Event
	FROM Divisions
	INNER JOIN Classes ON ClTournament=DivTournament
	INNER JOIN Awards ON AwTournament=DivTournament AND CONCAT(TRIM(DivId),TRIM(ClId)) = AwEvent AND AwFinEvent=0 AND AwUnrewarded=0
	WHERE DivTournament=" . StrSafe_DB($_SESSION['TourId']);

// // get all team categories
// $sql[] = "SELECT distinct AwEventTrans, AwFinEvent, AwTeam, CONCAT(DivId, ClId) as EvCode, CONCAT(DivDescription, ' ', ClDescription) as Event
// 		FROM Teams
// 		INNER JOIN Awards ON AwTournament=TeTournament AND TeEvent = AwEvent AND AwFinEvent=0 AND AwTeam=1 AND AwUnrewarded=0
// 		WHERE TeTournament=" . StrSafe_DB($_SESSION['TourId']);

// get all the events individual + teams
$sql[] = "SELECT distinct AwEventTrans, AwFinEvent, AwTeam, EvCode, EvEventName as Category
	FROM Events
	INNER JOIN Awards ON EvTournament=AwTournament AND EvCode = AwEvent AND AwFinEvent=1 AND AwTeam=EvTeamEvent AND AwUnrewarded=0
	WHERE  EvTournament=" . StrSafe_DB($_SESSION['TourId']);

$OldStep='-5';
$q=safe_r_sql("(".implode(') UNION (', $sql).") ORDER BY AwFinEvent, AwTeam, EvCode");
while($r=safe_fetch($q)) {
	if($OldStep!=$r->AwFinEvent.$r->AwTeam) {
		$OldStep=$r->AwFinEvent.$r->AwTeam;
		switch($OldStep) {
			case '00': $text=get_text('StageQ', 'ISK').get_text('Individual'); break;
			case '01': $text=get_text('StageQ', 'ISK').get_text('Team'); break;
			case '10': $text=get_text('StageMI', 'ISK'); break;
			case '11': $text=get_text('StageMT', 'ISK'); break;
		}
		$pdf->cell(0, 6, $text, 0, 1, 'C', 1);
		$pdf->ln(3);
	}
	$Y=$pdf->gety();
	$pdf->multicell($LangCol, 6, $r->EvCode . ': ' . $r->Event , 'R', 'L');
	$Y1=$pdf->gety();
	if($pdf->SecondLang) {
		$pdf->SetFont($pdf->FontStd2);
        if($Y1<$Y) {
            $pdf->setPage($pdf->getPage()-1);
        }
		$pdf->setXY($LangCol+10, $Y);
		$pdf->multicell($LangCol, 6, $r->AwEventTrans, 'L', 'L');
		$Y1=max($Y1, $pdf->gety());
		$pdf->sety($Y1, true);
		$pdf->SetFont($pdf->FontStd);
	}
	$pdf->ln(3);

}

$pdf->cell(0, 6, get_text('Options', 'Tournament'), 0, 1, 'C', 1);
$pdf->ln(3);

$Lines=array(
// 		'Aw-Intro',
// 		'Aw-Medal',
// 		'Aw-Plaque',
// 		'Aw-Giver',
// 		'Aw-Giving',
		'Aw-Med1',
		'Aw-Med2',
		'Aw-Med3',
		'Aw-Med4',
		'Aw-representing',
		'Aw-Anthem',
		'Aw-Anthem-TPE',
		'Aw-Applause',
);

foreach($Lines as $line) {
	$Y=$pdf->gety();
	$pdf->multicell($LangCol, 6, getModuleParameter('Awards', $line.'-1'), 'R', 'L');
	$Y1=$pdf->gety();
	if($pdf->SecondLang) {
		$pdf->SetFont($pdf->FontStd2);
        if($Y1<$Y) {
            $pdf->setPage($pdf->getPage()-1);
        }
		$pdf->setXY($LangCol+10, $Y);
		$pdf->multicell($LangCol, 6, getModuleParameter('Awards', $line.'-2'), 'L', 'L');
		$Y1=max($Y1, $pdf->gety());
		$pdf->sety($Y1, true);
		$pdf->SetFont($pdf->FontStd);
	}
	$pdf->ln(3);
}

$n=1;
$def='ssss';
while(($awarder=getModuleParameter('Awards', 'Aw-Award-1-'.$n, $def))!=$def) {
	$Y=$pdf->gety();
	$pdf->multicell($LangCol, 7, getModuleParameter('Awards', 'Aw-Award-1-'. $n), 'R', 'L');
	$Y1=$pdf->gety();
	if($pdf->SecondLang) {
		$pdf->SetFont($pdf->FontStd2);
        if($Y1<$Y) {
            $pdf->setPage($pdf->getPage()-1);
        }
		$pdf->setXY($LangCol+10, $Y);
		$pdf->multicell($LangCol, 7, getModuleParameter('Awards', 'Aw-Award-2-'. $n), 'L', 'L');
		$Y1=max($Y1, $pdf->gety());
		$pdf->sety($Y1, true);
		$pdf->SetFont($pdf->FontStd);
	}
	$pdf->ln(3);

	$n++;
}

$Y=$pdf->GetY();
$pdf->multicell($LangCol, 6, getModuleParameter('Awards', 'Aw-Special-1'), 'R', 'L');
$Y1=$pdf->GetY();
if($pdf->SecondLang) {
	$pdf->SetFont($pdf->FontStd2);
    if($Y1<$Y) {
        $pdf->setPage($pdf->getPage()-1);
    }
	$pdf->setXY($LangCol+10, $Y);
	$pdf->multicell($LangCol, 6, getModuleParameter('Awards', 'Aw-Special-2'), 'L', 'L');
	$Y1=max($Y1, $pdf->GetY());
	$pdf->sety($Y1, true);
	$pdf->SetFont($pdf->FontStd);
}

// print all the countries involved in the finals
//
$q=safe_r_sql("(select distinct CoCode 
	from Countries
		inner join Entries on CoId in (EnCountry, EnCountry2, EnCountry3)
		inner join Finals on FinAthlete=EnId and FinMatchNo<4 and FinTournament={$_SESSION['TourId']})
	union
	(select distinct CoCode 
	from Countries
	inner join TeamFinals on TfTeam=CoId and TfMatchNo<4 and TfTournament={$_SESSION['TourId']})");
$pdf->dy(1);
while($r=safe_fetch($q)) {
	$Y=$pdf->gety();
	$pdf->multicell($LangCol, 5, get_text($r->CoCode, 'IOC_Codes', '', '', '', $pdf->FirstLang, false), 'R', 'L');
	$Y1=$pdf->gety();
	if($pdf->SecondLang) {
		$pdf->SetFont($pdf->FontStd2);
		if($Y1<$Y) {
		    $pdf->setPage($pdf->getPage()-1);
        }
		$pdf->setXY($LangCol+10, $Y);
		$pdf->multicell($LangCol, 5, get_text($r->CoCode, 'IOC_Codes', '', '', '', $pdf->SecondLang, false), 'L', 'L');
		$Y1=max($Y1, $pdf->gety());
		$pdf->sety($Y1, true);
		$pdf->SetFont($pdf->FontStd);
	}
	$pdf->ln(2);
	$n++;
}

// Awarders
$pdf->AddPage();
$pdf->SetTopMargin($pdf->lastY+1);
$pdf->sety($pdf->lastY+1);

$n=1;
$def='ssss';
while(($a=getModuleParameter('Awards', 'Aw-Awarder-1-'.$n, $def))!=$def) {
	$Y=$pdf->gety();
	$pdf->multicell($LangCol, 5, $a, 'R', 'L');
	$Y1=$pdf->gety();
	if($pdf->SecondLang) {
		$pdf->SetFont($pdf->FontStd2);
        if($Y1<$Y) {
            $pdf->setPage($pdf->getPage()-1);
        }
		$pdf->setXY($LangCol+10, $Y);
		$pdf->multicell($LangCol, 5, getModuleParameter('Awards', 'Aw-Awarder-2-'.$n), 'L', 'L');
		$Y1=max($Y1, $pdf->gety());
		$pdf->sety($Y1, true);
		$pdf->SetFont($pdf->FontStd);
	}
	$pdf->ln(2);
	$n++;
}


// print the awarders and what they award
$pdf->AddPage();

$evArray= array(
		"00"=>get_text('IndClEvent', 'Tournament'),
		"10"=>get_text('IndFinEvent', 'Tournament'),
		"01"=>get_text('TeamClEvent', 'Tournament'),
		"11"=>get_text('TeamFinEvent', 'Tournament')
);

$Select = "SELECT *
		FROM Awards
		WHERE AwTournament=" . StrSafe_DB($_SESSION['TourId']) . "
		and AwUnrewarded=0 AND AwGroup!=0
		ORDER BY AwGroup DESC, AwOrder, AwFinEvent DESC, AwTeam ASC, AwEvent";
$q = safe_r_sql($Select);

while ($MyRow=safe_fetch($q)) {
	$Awards=array();
	if($MyRow->AwAwarderGrouping) $Awards=@unserialize($MyRow->AwAwarderGrouping);

	if(!$pdf->SamePage(2+count($Awards), 6)) {
// 		$pdf->AddPage();
	}
	$pdf->dy(2);
	$pdf->SetFont($pdf->FontStd,'B',13);
	$pdf->Cell(0, 6, $MyRow->AwEvent . ' ('.$evArray[$MyRow->AwFinEvent. $MyRow->AwTeam].')', '', 1, 'L', 1);
	$pdf->SetFont($pdf->FontStd,'',11);


	foreach($Awards as $k=>$v) {
		if(is_numeric($k)) {
			$pdf->MultiCell(0, 0, get_text_eval(getModuleParameter('Awards', 'Aw-Award-1-'.$k), getModuleParameter('Awards', 'Aw-Awarder-1-'.$v)), '', 'L', '', 1);
		} else {
			$pdf->MultiCell(0, 0, get_text_eval(getModuleParameter('Awards', 'Aw-Special-1'), getModuleParameter('Awards', 'Aw-Awarder-1-'.$v)), '', 'L', '', 1);
		}
		$pdf->dy(2);
	}
}

$pdf->Output();

