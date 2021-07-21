<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/OrisPDF.inc.php');
checkACl(AclCompetition,AclReadOnly);

$arrPosition=array('','1st','2nd','3rd','4th','5th');

$ReversedCountries="if(EnNameOrder=1, CONCAT(UPPER(EnFirstName), ' ', EnName), CONCAT(EnName, ' ', UPPER(EnFirstName)))";

$pdf = new OrisPDF('Awards', '');
$pdf->FontStd2=$pdf->FontStd;
$pdf->FontFix2=$pdf->FontFix;
// $pdf->addTTFfont($CFG->DOCUMENT_PATH.'Common/tcpdf/fonts/DroidSansFallback.ttf');
$pdf->SetFont($pdf->FontStd,'',8);
$pdf->FirstLang=(getModuleParameter('Awards', 'FirstLanguageCode') ? getModuleParameter('Awards', 'FirstLanguageCode') : ($_SESSION['TourPrintLang'] ? $_SESSION['TourPrintLang'] : SelectLanguage())); // set to empty string to avoid double printout
$pdf->SecondLang=''; // set to empty string to avoid double printout
$pdf->lBorder=0;
$pdf->setPhase(get_text('MenuLM_PrintAwardsPositions'));
if(getModuleParameter('Awards', 'SecondLanguage')) {
	$pdf->SecondLang=getModuleParameter('Awards', 'SecondLanguageCode');
	$pdf->lBorder='L';
	if(in_array($pdf->SecondLang, array('zh-cn', 'zh-tw'))) {
		$pdf->FontStd2='droidsansfallback';
		$pdf->FontFix2='droidsansfallback';
	}
    if(in_array($pdf->SecondLang, array('ja', 'ja-jp'))) {
        $pdf->FontStd2='arialuni';
        $pdf->FontFix2='arialuni';
    }
}

//error_reporting(E_ALL);

$idList=array();
//Lista Premiazione in Ordine
$sqlOrder = "SELECT AwEvent, AwEventTrans, AwPositions, AwFinEvent, AwTeam, AwGroup, AwOrder, AwAwarderGrouping "
	. "FROM Awards "
	. "WHERE AwTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AwUnrewarded=0 AND AwGroup!=0 "
	. "ORDER BY AwGroup, AwOrder, AwEvent, AwFinEvent DESC, AwTeam";
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
			$data[]=array(array($Num), $Winner, getModuleParameter('Awards','Aw-CustomNation-1-'. $Num), 'coCode', 'EvCode', 'Score', 'Ori', 'XNine');

			$Num++;
		}
		writeData($pdf, $data, $tmpAward, $CustomEvent, $tmpAwarders, ($rowOrder->AwTeam==0 ? 1:0), $rowOrder->AwOrder, $CustomEvent2);


	} else {
		$sql="";
		if($rowOrder->AwFinEvent==0 && $rowOrder->AwTeam==0) {
			$sql = "SELECT AwAwarderGrouping, EnId, concat(EnDivision,EnClass) EvCode, concat(EnDivision,EnClass) EventTranslation, CoCode, $ReversedCountries AS Athlete, CONCAT(" . ($_SESSION["ISORIS"] ? '' : "CoCode, ' ', ") . "if(CoNameComplete>'', CoNameComplete, CoName)) AS Country, CONCAT(DivDescription, ' - ', ClDescription) as Category, 1 as Counter,
				QuClRank AS `Rank`, QuScore AS Score, QuGold AS Gold,QuXnine AS XNine, AwDescription, AwAwarders
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
			$sql = "SELECT AwAwarderGrouping, EnId, concat(EvTeamEvent,EvCode) EvCode, concat(EvCode,EvTeamEvent) EventTranslation, CoCode, $ReversedCountries AS Athlete, CONCAT(" . ($_SESSION["ISORIS"] ? '' : "CoCode, ' ', ") . "if(CoNameComplete>'', CoNameComplete, CoName)) AS Country, EvEventName as Category, 1 as Counter,
				IF(EvFinalFirstPhase=0,IndRank,ABS(IndRankFinal)) as `Rank`, QuScore AS Score, QuGold AS Gold,QuXnine AS XNine, AwDescription, AwAwarders
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
			$sql=" SELECT AwAwarderGrouping, CoCode, '' EvCode, '' EventTranslation, CONCAT(" . ($_SESSION["ISORIS"] ? '' : "CoCode, ' ', ") . "if(CoNameComplete>'', CoNameComplete, CoName), IF(TeSubTeam=0,'',CONCAT(' (',TeSubTeam,')'))) as Country, CONCAT(DivDescription, ' - ', ClDescription) as Category,
				EnId, group_concat($ReversedCountries order by EnSex DESC, EnFirstName, EnName separator '|') AS Athlete, Q as Counter,
				TeRank as `Rank`, TeScore as Score, TeGold as Gold, TeXnine AS XNine, AwDescription, AwAwarders
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
			$sql = " SELECT AwAwarderGrouping, CoCode, concat(EvTeamEvent,EvCode) EvCode, concat(EvCode, EvTeamEvent) EventTranslation, CoId, CONCAT(" . ($_SESSION["ISORIS"] ? '' : "CoCode, ' ', ") . "if(CoNameComplete>'', CoNameComplete, CoName), IF(TeSubTeam=0,'',CONCAT(' (',TeSubTeam,')'))) as Country, EvEventName as Category,
				EnId, group_concat($ReversedCountries order by EnSex DESC, EnFirstName, EnName separator '|') AS Athlete, Q as Counter,
				IF(EvFinalFirstPhase=0,TeRank,TeRankFinal) as `Rank`, IF(EvFinalFirstPhase=0,TeScore,IFNULL(TfScore,'')) as Score, IF(EvFinalFirstPhase=0,TeGold,'') as Gold, IF(EvFinalFirstPhase=0,TeXnine,'') AS XNine, AwDescription, AwAwarders
				FROM Tournament
				INNER JOIN Teams ON ToId=TeTournament AND TeFinEvent=1
				INNER JOIN Countries ON TeCoId=CoId AND TeTournament=CoTournament
				INNER JOIN
					(SELECT TcCoId, TcEvent, TcTournament, TcFinEvent, TcSubTeam, COUNT(TcId) as Q
						FROM TeamComponent
						GROUP BY TcCoId, TcEvent, TcTournament, TcFinEvent, TcSubTeam
					) AS sq ON TeCoId=sq.TcCoId AND TeEvent=sq.TcEvent AND TeTournament=sq.TcTournament AND TeFinEvent=sq.TcFinEvent AND TeSubTeam=sq.TcSubTeam
				LEFT JOIN TeamFinComponent  AS tfc ON TeCoId=tfc.TfcCoId AND TeEvent=tfc.TfcEvent AND TeTournament=tfc.TfcTournament AND TeSubTeam=tfc.tfcSubTeam AND TeFinEvent=1
				LEFT JOIN Entries ON TfcId=EnId
				LEFT JOIN TeamFinals ON TfEvent=TeEvent AND TfTournament=TeTournament AND TfMatchNo<4 AND TfTeam=TeCoId AND TfSubTeam=TeSubTeam
				INNER JOIN Events ON TeEvent=EvCode AND EvTournament=ToId AND EvTeamEvent=1
				INNER JOIN Awards ON AwTournament=ToId AND TeEvent like AwEvent AND AwFinEvent=1 AND AwTeam=1 AND AwUnrewarded=0 AND INSTR(AwPositions,IF(EvFinalFirstPhase=0,TeRank,TeRankFinal))!=0
				WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . "
				AND AwEvent=" . StrSafe_DB($rowOrder->AwEvent) . " AND AwFinEvent=" . $rowOrder->AwFinEvent . " AND AwTeam=" . $rowOrder->AwTeam . "
				group by EvCode, CoId, TeSubTeam
				ORDER BY EvProgr, TeEvent, INSTR(AwPositions,IF(EvFinalFirstPhase=0,TeRank,TeRankFinal)) ASC, CoCode ASC, TfcOrder ";
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
	$Positions = getModuleParameter('Awards','PrintPositions', array('Usher','2A','2B','2C','1A','1B','1C','3A','3B','3C', 'Tray Bearer 1', 'Tray Bearer 2', 'Tray Bearer 3', 'VIP Usher', 'V1', 'V2', 'V3', 'VIP Usher'));
    $Positions = str_replace('; ', ', #, ',$Positions);
	if(!is_array($Positions )) {
        $Positions = explode(', ', $Positions);
    }

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

	// Prizes and Awarders

	$Special1='';
	$Special2='';
	$order=1;
	foreach($Awarders as $k => $v) {
		if(!is_numeric($k)) {
			$Special1=getModuleParameter('Awards', 'Aw-Awarder-1-'.$v);
			$Special2=getModuleParameter('Awards', 'Aw-Awarder-2-'.$v);
			continue;
		} else {
			$Positions=str_replace('V'.$order, 'VIP '.$order.': '.getModuleParameter('Awards', 'Aw-Awarder-1-'.$v), $Positions);
			$order++;
		}
	}

	// Single medals
	$WinNat='';
	for($i=count($data)-1; $i>=0; $i--)	{
		if(is_array($data[$i][0])) {
			continue;
		}
		if($indEvent) {
			$ath=$data[$i][1];
			$Positions=str_replace($data[$i][0].'A', $data[$i][0].' A: '.$data[$i][3].' - '.$ath, $Positions);
		} else {
			foreach($data[$i][1] as $k => $ath) {
				$Positions=str_replace($data[$i][0].chr(65+$k), $data[$i][0].' '.chr(65+$k).': '.$data[$i][3].' - '.$ath, $Positions);
			}
		}
	}

	$pdf->setfont('','',12);
	foreach($Positions as $line) {
        if($line=='#') {
            $pdf->Ln(5);
        } else {
            $pdf->cell(0, 7, $line, '', 1);
        }
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
?>
