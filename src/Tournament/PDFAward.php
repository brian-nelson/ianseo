<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/OrisPDF.inc.php');
checkACl(AclCompetition,AclReadOnly);

$arrPosition=array('','1st','2nd','3rd','4th','5th');
$par_RepresentCountry = getModuleParameter('Awards','RepresentCountry',1);
$par_PlayAnthem = getModuleParameter('Awards','PlayAnthem',1);
$par_ShowPoints = getModuleParameter('Awards','ShowPoints',0);

$ReversedCountries="if(EnNameOrder=1, CONCAT(UPPER(EnFirstName), ' ', EnName), CONCAT(EnName, ' ', UPPER(EnFirstName)))";

$pdf = new OrisPDF('Awards', '');
$pdf->FontStd2=$pdf->FontStd;
$pdf->FontFix2=$pdf->FontFix;
// $pdf->addTTFfont($CFG->DOCUMENT_PATH.'Common/tcpdf/fonts/DroidSansFallback.ttf');
$pdf->SetFont($pdf->FontStd,'',8);
$pdf->FirstLang=(getModuleParameter('Awards', 'FirstLanguageCode') ? getModuleParameter('Awards', 'FirstLanguageCode') : ($_SESSION['TourPrintLang'] ? $_SESSION['TourPrintLang'] : SelectLanguage())); // set to empty string to avoid double printout
$pdf->SecondLang=''; // set to empty string to avoid double printout
$pdf->lBorder=0;
$pdf->setPhase(get_text('MenuLM_PrintAwards'));
if(getModuleParameter('Awards', 'SecondLanguage')) {
	$pdf->SecondLang=getModuleParameter('Awards', 'SecondLanguageCode');
	$pdf->lBorder='R';
	if(in_array($pdf->SecondLang, array('zh-cn', 'zh-tw'))) {
		$pdf->FontStd2='droidsansfallback';
		$pdf->FontFix2='droidsansfallback';
	}
    if(in_array($pdf->SecondLang, array('ja', 'ja-jp'))) {
        $pdf->FontStd2='arialuni';
        $pdf->FontFix2='arialuni';
    }
}

error_reporting(E_ALL);

$idList=array();
//Lista Premiazione in Ordine
$sqlOrder = "SELECT AwEvent, AwEventTrans, AwPositions, AwFinEvent, AwTeam, AwGroup, AwOrder, AwAwarderGrouping, EvFinalFirstPhase 
	FROM Awards
	left join Events on EvTournament=AwTournament and EvTeamEvent=AwTeam and EvCode=AwEvent
	WHERE AwTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AwUnrewarded=0 AND AwGroup!=0 
	ORDER BY AwGroup, AwOrder, AwEvent, AwFinEvent DESC, AwTeam";
$rsOrder = safe_r_sql($sqlOrder);
while($rowOrder=safe_fetch($rsOrder)) {
	if(strstr($rowOrder->AwEvent, 'Custom-')) {
		list($dum, $Num) = explode('-', $rowOrder->AwEvent);

		$Awarders=array();
		if($rowOrder->AwAwarderGrouping) $Awarders=@unserialize($rowOrder->AwAwarderGrouping);
		$tmpAward =  'descrizione';
		$CustomEvent = getModuleParameter('Awards','Aw-CustomEvent-1-'. $Num);
		$CustomEvent2 = getModuleParameter('Awards','Aw-CustomEvent-2-'. $Num);
		$tmpAwarders = $Awarders;

		$data=array();
		$tmpNum=$Num;
		while(getModuleParameter('Awards','Aw-CustomEvent-1-'. $Num)==$CustomEvent) {
			$Winner1=getModuleParameter('Awards','Aw-CustomWinner-1-'. $Num);
			$Winner2=getModuleParameter('Awards','Aw-CustomWinner-2-'. $Num);
			if($Winner2) {
				$Winner=array($Winner1, $Winner2);
			} else {
				$Winner=$Winner1;
			}

			// get the nation
			$Country=getModuleParameter('Awards','Aw-CustomNation-1-'. $Num);
			//// check if we already have it
			//$coq=safe_r_sql("select * from Countries where CoCode=".StrSafe_DB($Country)." and CoTournament={$_SESSION['TourId']}");
			//if($cor=safe_fetch($coq)) {
			//	$Country=$cor->CoName;
			//}

			$data[]=array(array($Num), $Winner, $Country, $Country, 'EvCode', 'Score', 'Ori', 'XNine');

			$Num++;
		}
		writeData($pdf, $data, $tmpAward, $CustomEvent, $tmpAwarders, ($rowOrder->AwTeam==0 ? 1:0), $rowOrder->AwOrder, $CustomEvent2);


	} else {
		$sql="";
		if($rowOrder->AwFinEvent==0 && $rowOrder->AwTeam==0) {
			$sql = "SELECT AwAwarderGrouping, EnId, concat(EnDivision,EnClass) EvCode, concat(EnDivision,EnClass) EventTranslation, CoCode, $ReversedCountries AS Athlete, 
				CONCAT(" . ($_SESSION["ISORIS"] ? '' : "CoCode, ' ', ") . "if(CoNameComplete>'', if(ToLocRule='FR', concat(CoName, ' (',CoNameComplete,')'), CoNameComplete), CoName)) AS Country, 
				CONCAT(DivDescription, ' - ', ClDescription) as Category, 1 as Counter,
				QuClRank AS Rank, QuScore AS Score, QuGold AS Gold,QuXnine AS XNine, AwDescription, AwAwarders
				FROM Tournament
				INNER JOIN Entries ON ToId=EnTournament
				INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . "
				INNER JOIN Qualifications ON EnId=QuId
				INNER JOIN Classes ON EnClass=ClId AND ClTournament=EnTournament AND ClAthlete=1
				INNER JOIN Divisions ON EnDivision=DivId AND DivTournament=EnTournament AND DivAthlete=1
				INNER JOIN Awards ON EnTournament=AwTournament AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE AwEvent AND AwFinEvent=0 AND AwTeam=0 AND AwUnrewarded=0 AND INSTR(AwPositions,QuClRank)!=0
				WHERE EnAthlete=1 AND EnIndClEvent=1 AND EnStatus <= 1 AND QuScore != 0 AND ToId=" . StrSafe_DB($_SESSION['TourId']) . "
				AND AwEvent=" . StrSafe_DB($rowOrder->AwEvent) . " AND AwFinEvent=" . $rowOrder->AwFinEvent . " AND AwTeam=" . $rowOrder->AwTeam . "
				ORDER BY DivViewOrder, EnDivision, ClViewOrder, EnClass, INSTR(AwPositions,QuClRank) ASC, EnFirstName, EnName ";
		}
		else if($rowOrder->AwFinEvent==1 && $rowOrder->AwTeam==0)
		{
			$sql = "SELECT AwAwarderGrouping, EnId, concat(EvTeamEvent,EvCode) EvCode, concat(EvCode,EvTeamEvent) EventTranslation, CoCode, $ReversedCountries AS Athlete, 
				CONCAT(" . ($_SESSION["ISORIS"] ? '' : "CoCode, ' ', ") . "if(CoNameComplete>'', if(ToLocRule='FR', concat(CoName, ' (',CoNameComplete,')'), CoNameComplete), CoName)) AS Country, EvEventName as Category, 1 as Counter,
				IF(EvFinalFirstPhase=0,IndRank,ABS(IndRankFinal)) as Rank, QuScore AS Score, QuGold AS Gold,QuXnine AS XNine, AwDescription, AwAwarders
				FROM Tournament
				INNER JOIN Entries ON ToId=EnTournament
				INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . "
				INNER JOIN Qualifications ON EnId=QuId
				INNER JOIN Individuals ON EnId=IndId AND EnTournament=IndTournament
				INNER JOIN Events ON IndEvent=EvCode AND EvTournament=ToId AND EvTeamEvent=0
				INNER JOIN Awards ON EnTournament=AwTournament AND EvCode LIKE AwEvent AND AwFinEvent=1 AND AwTeam=0 AND AwUnrewarded=0 AND INSTR(AwPositions,IF(EvFinalFirstPhase=0,IndRank,ABS(IndRankFinal)))!=0
				WHERE  EnAthlete=1 AND EnIndFEvent=1 AND EnStatus <= 1 AND QuScore != 0 AND ToId=" . StrSafe_DB($_SESSION['TourId']) . "
				AND AwEvent=" . StrSafe_DB($rowOrder->AwEvent) . " AND AwFinEvent=" . $rowOrder->AwFinEvent . " AND AwTeam=" . $rowOrder->AwTeam . "
				ORDER BY EvProgr, EvCode, INSTR(AwPositions,IF(EvFinalFirstPhase=0,IndRank,ABS(IndRankFinal))) ASC, EnFirstName, EnName ";
		}
		else if($rowOrder->AwFinEvent==0 && $rowOrder->AwTeam==1)
		{
			$sql=" SELECT AwAwarderGrouping, CoCode, '' EvCode, '' EventTranslation, CONCAT(" . ($_SESSION["ISORIS"] ? '' : "CoCode, ' ', ") . "if(CoNameComplete>'', if(ToLocRule='FR', concat(CoName, ' (',CoNameComplete,')'), CoNameComplete), CoName), IF(TeSubTeam=0,'',CONCAT(' (',TeSubTeam,')'))) as Country, CONCAT(DivDescription, ' - ', ClDescription) as Category,
				EnId, group_concat($ReversedCountries order by EnSex DESC, EnFirstName, EnName separator '|') AS Athlete, Q as Counter,
				TeRank as Rank, TeScore as Score, TeGold as Gold, TeXnine AS XNine, AwDescription, AwAwarders
				FROM Tournament
				INNER JOIN Teams ON ToId=TeTournament AND TeFinEvent=0
				INNER JOIN Countries ON TeCoId=CoId AND TeTournament=CoTournament
				INNER JOIN
					(SELECT TcCoId, TcEvent, TcTournament, TcFinEvent, COUNT(TcId) as Q
					FROM TeamComponent
					GROUP BY TcCoId, TcEvent, TcTournament, TcFinEvent
					) AS sq ON TeCoId=sq.TcCoId AND TeEvent=sq.TcEvent AND TeTournament=sq.TcTournament AND TeFinEvent=sq.TcFinEvent
				INNER JOIN TeamComponent  AS tc ON TeCoId=tc.TcCoId AND TeEvent=tc.TcEvent AND TeTournament=tc.TcTournament AND TeFinEvent=tc.TcFinEvent
				INNER JOIN Entries ON TcId=EnId
				LEFT JOIN
					(SELECT CONCAT(DivId, ClId) DivClass, Divisions.*, Classes.*
					FROM Divisions
					INNER JOIN Classes ON DivTournament=ClTournament
					WHERE DivAthlete AND ClAthlete
					) AS DivClass ON TeEvent=DivClass AND TeTournament=DivTournament
				INNER JOIN Awards ON AwTournament=ToId AND TeEvent LIKE AwEvent AND AwFinEvent=0 AND AwTeam=1 AND AwUnrewarded=0 AND INSTR(AwPositions,TeRank)!=0
				WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . "
				AND AwEvent=" . StrSafe_DB($rowOrder->AwEvent) . " AND AwFinEvent=" . $rowOrder->AwFinEvent . " AND AwTeam=" . $rowOrder->AwTeam . "
				group by DivClass, CoId, TeSubTeam
				ORDER BY DivViewOrder, EnDivision, ClViewOrder, EnClass, TeEvent, INSTR(AwPositions,TeRank) ASC, CoCode ASC, TcOrder ";
		}
		else if($rowOrder->AwFinEvent==1 && $rowOrder->AwTeam==1)
		{
			$TeamComponent="LEFT JOIN TeamFinComponent  AS tfc ON TeCoId=tfc.TfcCoId AND TeEvent=tfc.TfcEvent AND TeTournament=tfc.TfcTournament AND TeSubTeam=tfc.tfcSubTeam AND TeFinEvent=1
				LEFT JOIN Entries ON TfcId=EnId";
			$TeamComponentOrder="TfcOrder";
			if(!$rowOrder->EvFinalFirstPhase) {
				$TeamComponent="LEFT JOIN TeamComponent  AS tc ON TeCoId=tc.TcCoId AND TeEvent=tc.TcEvent AND TeTournament=tc.TcTournament AND TeSubTeam=tc.tcSubTeam AND TeFinEvent=1
					LEFT JOIN Entries ON TcId=EnId";
				$TeamComponentOrder="TcOrder";
			}
			$sql = " SELECT AwAwarderGrouping, CoCode, concat(EvTeamEvent,EvCode) EvCode, concat(EvCode, EvTeamEvent) EventTranslation, CoId, 
				CONCAT(" . ($_SESSION["ISORIS"] ? '' : "CoCode, ' ', ") . "if(CoNameComplete>'', if(ToLocRule='FR', concat(CoName, ' (',CoNameComplete,')'), CoNameComplete), CoName), IF(TeSubTeam=0,'',CONCAT(' (',TeSubTeam,')'))) as Country, EvEventName as Category,
				EnId, group_concat($ReversedCountries order by EnSex DESC, EnFirstName, EnName separator '|') AS Athlete, Q as Counter,
				IF(EvFinalFirstPhase=0,TeRank,TeRankFinal) as Rank, IF(EvFinalFirstPhase=0,TeScore,IFNULL(TfScore,'')) as Score, IF(EvFinalFirstPhase=0,TeGold,'') as Gold, IF(EvFinalFirstPhase=0,TeXnine,'') AS XNine, AwDescription, AwAwarders
				FROM Tournament
				INNER JOIN Teams ON ToId=TeTournament AND TeFinEvent=1
				INNER JOIN Countries ON TeCoId=CoId AND TeTournament=CoTournament
				INNER JOIN
					(SELECT TcCoId, TcEvent, TcTournament, TcFinEvent, TcSubTeam, COUNT(TcId) as Q
						FROM TeamComponent
						GROUP BY TcCoId, TcEvent, TcTournament, TcFinEvent, TcSubTeam
					) AS sq ON TeCoId=sq.TcCoId AND TeEvent=sq.TcEvent AND TeTournament=sq.TcTournament AND TeFinEvent=sq.TcFinEvent AND TeSubTeam=sq.TcSubTeam 
				$TeamComponent
				LEFT JOIN TeamFinals ON TfEvent=TeEvent AND TfTournament=TeTournament AND TfMatchNo<4 AND TfTeam=TeCoId AND TfSubTeam=TeSubTeam
				INNER JOIN Events ON TeEvent=EvCode AND EvTournament=ToId AND EvTeamEvent=1
				INNER JOIN Awards ON AwTournament=ToId AND TeEvent like AwEvent AND AwFinEvent=1 AND AwTeam=1 AND AwUnrewarded=0 AND INSTR(AwPositions,IF(EvFinalFirstPhase=0,TeRank,TeRankFinal))!=0
				WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . "
				AND AwEvent=" . StrSafe_DB($rowOrder->AwEvent) . " AND AwFinEvent=" . $rowOrder->AwFinEvent . " AND AwTeam=" . $rowOrder->AwTeam . "
				group by EvCode, CoId, TeSubTeam
				ORDER BY EvProgr, TeEvent, INSTR(AwPositions,IF(EvFinalFirstPhase=0,TeRank,TeRankFinal)) ASC, CoCode ASC, $TeamComponentOrder ";
		}

		if($sql!="") {
			$rs = safe_r_sql($sql);
			$curAward = '';
			$tmpAwarders = array();
			$tmpAward = '';
			$tmpCategory = '';
			$tmpEvent = '';
			$data=array();

			while($row = safe_fetch($rs))
			{
				if($curAward != $row->Category . "|" . $row->AwDescription)
				{
					if(count($data)>0) {
						writeData($pdf, $data, $tmpAward, $tmpCategory, $tmpAwarders, ($rowOrder->AwTeam==0 ? 1:0), $rowOrder->AwOrder, $rowOrder->AwEventTrans ? $rowOrder->AwEventTrans : $tmpCategory, $tmpEvent);
					}
					$data=array();
					$Awarders=array();
					if($row->AwAwarderGrouping) $Awarders=@unserialize($row->AwAwarderGrouping);
					$tmpAward =  $row->AwDescription;
					$tmpCategory = $row->Category;
					$tmpEvent = $row->EventTranslation;
					$tmpAwarders = $Awarders;
					$curAward = $row->Category . "|" . $row->AwDescription;
				}
				if($rowOrder->AwTeam) {
					$tmp=explode('|', $row->Athlete);
					$data[]=array($row->Rank, $tmp, $row->Country, $row->CoCode, $row->EvCode, $row->Score, $row->Gold, $row->XNine);
				} else {
					$data[]=array($row->Rank, $row->Athlete, $row->Country, $row->CoCode, $row->EvCode, $row->Score, $row->Gold, $row->XNine);
				}
				$idList[]=$row->EnId;
			}
			if(count($data)>0) {
				writeData($pdf, $data, $tmpAward, $tmpCategory, $tmpAwarders, ($rowOrder->AwTeam==0 ? 1:0), $rowOrder->AwOrder, $rowOrder->AwEventTrans, $tmpEvent);
			}
		}
	}
}
$pdf->Output();

function writeData($pdf, $data, $Description, $Category, $Awarders, $indEvent, $Order, $EventTranslated, $Event='') {
	static $LangCol;
	GLOBAL $par_RepresentCountry, $par_PlayAnthem, $rowOrder, $par_ShowPoints;

	$PlayAnthem=$par_PlayAnthem;

	if(empty($LangCol)) {
		$LangCol=($pdf->getpagewidth()-20)/($pdf->SecondLang ? 2 : 1);
	}
	$pdf->AddPage();
	$pdf->setXY(OrisPDF::leftMargin, $pdf->lastY-10);

	// HEADER
	$pdf->SetFont($pdf->FontStd,'B',20);
	$pdf->Cell(0, 6, ($Order ? "$Order) " : '') . $Category, 0, 1, 'C', 1);
	$pdf->setY($pdf->getY()+2);

	$pdf->SetFont($pdf->FontStd,'B',13);
	writeHiLight($pdf, "GO FANFARE");
	$pdf->ln(1);

	//<b>[[$text]@

	$pdf->SetFont($pdf->FontStd,'',13);
	$lines2=$pdf->multiCell($LangCol, 6, get_text_eval(getModuleParameter('Awards', 'Aw-Intro-1'), $Category), $pdf->lBorder, 'L', 0, 0);
	$lines=0;
	if($pdf->SecondLang) {
		$pdf->SetFont($pdf->FontStd2,'',13);
		$lines=$pdf->multiCell($LangCol, 6, get_text_eval(getModuleParameter('Awards', 'Aw-Intro-2'), $EventTranslated ? $EventTranslated : $Category), 'L', 'L', 0, 0);
	}
	$pdf->ln(6*max($lines, $lines2));
	$pdf->ln(2);

	// Prizes and Awarders

	$Special1='';
	$Special2='';
	foreach($Awarders as $k => $v) {
		if(!is_numeric($k)) {
			$Special1=get_text_eval(getModuleParameter('Awards', 'Aw-Special-1'), getModuleParameter('Awards', 'Aw-Awarder-1-'.$v));
			$Special2=get_text_eval(getModuleParameter('Awards', 'Aw-Special-2'), getModuleParameter('Awards', 'Aw-Awarder-2-'.$v));
			continue;
		}
		$pdf->SetFont($pdf->FontStd,'',13);
		list($Name, $Title) = @explode(',', getModuleParameter('Awards', 'Aw-Awarder-1-'.$v), 2);
		$lines2=$pdf->MultiCell($LangCol, 6, get_text_eval(getModuleParameter('Awards', 'Aw-Award-1-'.$k), $Title), $pdf->lBorder, 'L', 0, 0);
		$lines=0;
		if($pdf->SecondLang) {
			list(, $Title) = @explode(',', getModuleParameter('Awards', 'Aw-Awarder-2-'.$v), 2);
			$pdf->SetFont($pdf->FontStd2,'',13);
			$lines=$pdf->MultiCell($LangCol, 6, get_text_eval(getModuleParameter('Awards', 'Aw-Award-2-'.$k), $Title), 'L', 'L', 0, 0);
		}
		$pdf->ln(6*max($lines, $lines2));
		$pdf->SetFont($pdf->FontStd,'B',13);
		$pdf->MultiCell(0, 6, $Name, 0, 'C', 0, 1);
		$pdf->ln(2);
	}

	// Single medals
	$WinNat='';
	for($i=count($data)-1; $i>=0; $i--)	{
		$Club1='';
		$Club2='';
		if($par_RepresentCountry) {
			$Club1=(get_text($data[$i][3], 'IOC_Codes', '', '1', '', $pdf->FirstLang)==$data[$i][3] ? $data[$i][2] : get_text($data[$i][3], 'IOC_Codes', '', '', '', $pdf->FirstLang));
			$Club2=(get_text($data[$i][3], 'IOC_Codes', '', '1', '', $pdf->SecondLang)==$data[$i][3] ? $data[$i][2] : get_text($data[$i][3], 'IOC_Codes', '', '', '', $pdf->SecondLang));
		}

		if($indEvent) {
			$ath=$data[$i][1];
		} else {
			$ath=implode(' - ', $data[$i][1]);
		}

		if($rowOrder->AwPositions!='1,2,4,3' or $data[$i][0]!=4) {
			writeHiLight($pdf);
			if(is_numeric($data[$i][0])) {
				$med1=getModuleParameter('Awards', 'Aw-Med'.$data[$i][0].'-1');
				$med2=getModuleParameter('Awards', 'Aw-Med'.$data[$i][0].'-2');
			} else {
				$WinNat=$data[$i][3];
				$PlayAnthem=false;
				$med1=getModuleParameter('Awards','Aw-CustomPrize-1-'. $data[$i][0][0]);
				$med2=getModuleParameter('Awards','Aw-CustomPrize-2-'. $data[$i][0][0]);
			}
			$pdf->SetFont($pdf->FontStd,'',13);
			$lines2=$pdf->MultiCell($LangCol, 6, get_text_eval($med1, $Club1), $pdf->lBorder, 'L', 0, 0);
			$lines=0;
			if($pdf->SecondLang) {
				$pdf->SetFont($pdf->FontStd2,'',13);
				$lines=$pdf->MultiCell($LangCol, 6, get_text_eval($med2, $Club2), 'L', 'L', 0, 0);
			}

			$pdf->ln(6*max($lines, $lines2));
		}

		if($par_RepresentCountry and $Club1) {
			if($Club1=get_text($data[$i][3], 'IOC_Codes', '', '', '', $pdf->FirstLang, false)) {
				$Club2=get_text($data[$i][3], 'IOC_Codes', '', '', '', $pdf->SecondLang, false);
			} else {
				$Club1=(get_text($data[$i][3], 'IOC_Codes', '', '1', '', $pdf->FirstLang)==$data[$i][3] ? $data[$i][2] : get_text($data[$i][3], 'IOC_Codes', '', '', '', $pdf->FirstLang));
				$Club2=(get_text($data[$i][3], 'IOC_Codes', '', '1', '', $pdf->SecondLang)==$data[$i][3] ? $data[$i][2] : get_text($data[$i][3], 'IOC_Codes', '', '', '', $pdf->SecondLang));
			}

//if(is_array($Club1))
			$pdf->SetFont($pdf->FontStd,'',13);
			$lines2=$pdf->MultiCell($LangCol, 6, get_text_eval(getModuleParameter('Awards', 'Aw-representing-1'), $Club1), $pdf->lBorder, 'L', 0, 0);
			$lines=0;
			if($pdf->SecondLang) {
				$pdf->SetFont($pdf->FontStd2,'',13);
				$lines=$pdf->MultiCell($LangCol, 6, get_text_eval(getModuleParameter('Awards', 'Aw-representing-2'), $Club2), 'L', 'L', 0, 0);
			}

			$pdf->ln(6*max($lines, $lines2));
		}

		if($par_ShowPoints) {
			$pdf->Cell($LangCol, 6, get_text_eval('Points').' '.$data[$i][5].'; '.get_text_eval('Golds').' '.$data[$i][6].'; '.get_text_eval('XNine').' '.$data[$i][7], $pdf->lBorder, 1, 'L', 0);
		}

		if($data[$i][0]==1)
			$WinNat=$data[$i][3];

		$pdf->SetFont($pdf->FontStd,'B',13);
		if(is_array($ath)) {
			$pdf->SetFont($pdf->FontStd,'',13);
			$lines2=$pdf->MultiCell($LangCol, 6, $ath[0], $pdf->lBorder, 'L', 0, 0);
			$lines=0;
			if($pdf->SecondLang) {
				$pdf->SetFont($pdf->FontStd2,'',13);
				$lines=$pdf->MultiCell($LangCol, 6, $ath[1], 'L', 'L', 0, 0);
			}

			$pdf->ln(6*max($lines, $lines2));
		} else {
			$pdf->MultiCell(0, 6, $ath, 0, 'C', 0, 1);
		}
		$pdf->SetFont($pdf->FontStd,'',13);
		$pdf->ln(2);
	}

	if($WinNat) {
		if($Special1) {
			$lines2=$pdf->MultiCell($LangCol, 6, $Special1, $pdf->lBorder, 'L', 0, 0);
			$lines=0;
			if($pdf->SecondLang) {
				$lines=$pdf->MultiCell($LangCol, 6, $Special2, 'L', 'L', 0, 0);
			}
			$pdf->ln(6*max($lines, $lines2));
		}

		if($PlayAnthem) {
			writeHiLight($pdf,"ANTHEM");
			$lines=0;
			if($data[0][3]=='TPE') {
				$pdf->SetFont($pdf->FontStd,'',13);
				$lines2=$pdf->multiCell($LangCol, 7, get_text_eval(getModuleParameter('Awards', 'Aw-Anthem-TPE-1')), $pdf->lBorder, 'L', 0, 0);
				if($pdf->SecondLang) {
					$pdf->SetFont($pdf->FontStd2,'',13);
					$lines=$pdf->multiCell($LangCol, 7, get_text_eval(getModuleParameter('Awards', 'Aw-Anthem-TPE-2')), 'L', 'L', 0, 0);
				}
			} else {
				$pdf->SetFont($pdf->FontStd,'',13);
				$lines2=$pdf->multiCell($LangCol, 7, get_text_eval(getModuleParameter('Awards', 'Aw-Anthem-1')), $pdf->lBorder, 'L', 0, 0);
				if($pdf->SecondLang) {
					$pdf->SetFont($pdf->FontStd2,'',13);
					$lines=$pdf->multiCell($LangCol, 7, get_text_eval(getModuleParameter('Awards', 'Aw-Anthem-2')), 'L', 'L', 0, 0);
				}
			}
			$pdf->ln(6*max($lines, $lines2));

			$pdf->SetFont($pdf->FontStd,'B',13);
			$pdf->Cell($LangCol, 7, $Club1, $pdf->lBorder, 0, 'C', 0);
			if($pdf->SecondLang) {
				$pdf->SetFont($pdf->FontStd2,'',13);
				$pdf->Cell($LangCol, 7, $Club2, 'L', 0, 'C', 0);
			}

			$pdf->ln(8);
		}

		$pdf->SetFont($pdf->FontStd,'',13);
		writeHiLight($pdf,"ACKNOWLEDGE MEDALLISTS");
		$pdf->SetFont($pdf->FontStd,'',13);
		$lines2=$pdf->multicell($LangCol, 7, get_text_eval(getModuleParameter('Awards', 'Aw-Applause-1')), $pdf->lBorder, 'L', 0, 0);
		$lines=0;
		if($pdf->SecondLang) {
			$pdf->SetFont($pdf->FontStd2,'',13);
			$lines=$pdf->multicell($LangCol, 7, get_text_eval(getModuleParameter('Awards', 'Aw-Applause-2')), 'L', 'L', 0, 0);
		}
		$pdf->ln(6*max($lines, $lines2));
	}
}

function writeHiLight($pdf, $text='')
{
	$pdf->SetDrawColor(0x80, 0x80, 0x80);
	$pdf->SetFillColor(0x80,0x80,0x80);
	$pdf->SetTextColor(0xFF, 0xFF, 0xFF);
	if($text) {
		$pdf->Cell(0, 6, $text, 1, 1, 'C', 1);
	} else {
		$pdf->setfontsize(2);
		$pdf->Cell(0, 2, '', 1, 1, 'C', 1);
	}
	$pdf->setfontsize(12);
	$pdf->SetDefaultColor();
}
