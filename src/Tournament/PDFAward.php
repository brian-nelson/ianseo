<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/OrisPDF.inc.php');
require_once('Common/Fun_Modules.php');

$arrPosition=array('','1st','2nd','3rd','4th','5th');
$par_RepresentCountry = getModuleParameter('Awards','RepresentCountry',1);
$par_PlayAnthem = getModuleParameter('Awards','PlayAnthem',1);


$pdf = new OrisPDF('Awards',get_text('MenuLM_PrintAwards'));
$idList=array();
//Lista Premiazione in Ordine
$sqlOrder = "SELECT AwEvent, AwFinEvent, AwTeam, AwGroup "
	. "FROM Awards "
	. "WHERE AwTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AwUnrewarded=0 AND AwGroup!=0 "
	. "ORDER BY AwGroup, AwOrder, AwEvent, AwFinEvent DESC, AwTeam";
$rsOrder = safe_r_sql($sqlOrder);
while($rowOrder=safe_fetch($rsOrder))
{
	$sql="";
	if($rowOrder->AwFinEvent==0 && $rowOrder->AwTeam==0)
	{
		$sql = "SELECT EnId, CONCAT(EnName, ' ', UPPER(EnFirstName)) AS Athlete, CONCAT(" . ($_SESSION["ISORIS"] ? '' : "CoCode, ' ', ") . "CoName) AS Country, CONCAT(DivDescription, ' - ', ClDescription) as Category, 1 as Counter,
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
			ORDER BY DivViewOrder, EnDivision, ClViewOrder, EnClass, QuClRank ASC, EnFirstName, EnName ";
	}
	else if($rowOrder->AwFinEvent==1 && $rowOrder->AwTeam==0)
	{
		$sql = "SELECT EnId, CONCAT(EnName, ' ', UPPER(EnFirstName)) AS Athlete, CONCAT(" . ($_SESSION["ISORIS"] ? '' : "CoCode, ' ', ") . "CoName) AS Country, EvEventName as Category, 1 as Counter,
			IF(EvFinalFirstPhase=0,IndRank,ABS(IndRankFinal)) as Rank, QuScore AS Score, QuGold AS Gold,QuXnine AS XNine, AwDescription, AwAwarders
			FROM Tournament
			INNER JOIN Entries ON ToId=EnTournament
			INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . "
			INNER JOIN Qualifications ON EnId=QuId
			INNER JOIN Individuals ON EnId=IndId AND EnTournament=IndTournament
			INNER JOIN Events ON IndEvent=EvCode AND EvTournament=ToId AND EvTeamEvent=0
			INNER JOIN Awards ON EnTournament=AwTournament AND EvCode LIKE AwEvent AND AwFinEvent=1 AND AwTeam=0 AND AwUnrewarded=0 AND INSTR(AwPositions,IF(EvFinalFirstPhase=0,IndRank,ABS(IndRankFinal)))!=0
			WHERE  EnAthlete=1 AND EnIndClEvent=1 AND EnStatus <= 1 AND QuScore != 0 AND ToId=" . StrSafe_DB($_SESSION['TourId']) . "
			AND AwEvent=" . StrSafe_DB($rowOrder->AwEvent) . " AND AwFinEvent=" . $rowOrder->AwFinEvent . " AND AwTeam=" . $rowOrder->AwTeam . "
			ORDER BY EvProgr, EvCode, IF(EvFinalFirstPhase=0,IndRank,ABS(IndRankFinal)) ASC, EnFirstName, EnName ";
	}
	else if($rowOrder->AwFinEvent==0 && $rowOrder->AwTeam==1)
	{
		$sql=" SELECT CONCAT(" . ($_SESSION["ISORIS"] ? '' : "CoCode, ' ', ") . "CoName, IF(TeSubTeam=0,'',CONCAT(' (',TeSubTeam,')'))) as Country, CONCAT(DivDescription, ' - ', ClDescription) as Category,
			EnId, group_concat( CONCAT(EnName, ' ', UPPER(EnFirstName)) order by EnSex DESC, EnFirstName, EnName separator '|') AS Athlete, Q as Counter,
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
			ORDER BY DivViewOrder, EnDivision, ClViewOrder, EnClass, TeEvent, Rank ASC, CoCode ASC, TcOrder ";
// 		debug_svela($sql);
	}
	else if($rowOrder->AwFinEvent==1 && $rowOrder->AwTeam==1)
	{
		$sql = " SELECT CoId, CONCAT(" . ($_SESSION["ISORIS"] ? '' : "CoCode, ' ', ") . "CoName, IF(TeSubTeam=0,'',CONCAT(' (',TeSubTeam,')'))) as Country, EvEventName as Category,
			EnId, group_concat(CONCAT(EnName, ' ', UPPER(EnFirstName)) order by EnSex DESC, EnFirstName, EnName separator '|') AS Athlete, Q as Counter,
			IF(EvFinalFirstPhase=0,TeRank,TeRankFinal) as Rank, IF(EvFinalFirstPhase=0,TeScore,IFNULL(TfScore,'')) as Score, IF(EvFinalFirstPhase=0,TeGold,'') as Gold, IF(EvFinalFirstPhase=0,TeXnine,'') AS XNine, AwDescription, AwAwarders
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
			ORDER BY EvProgr, TeEvent, Rank ASC, CoCode ASC, TfcOrder ";
	}

// 	debug_svela($sql);
//echo $sql;exit;
	if($sql!="")
	{
		$rs = safe_r_sql($sql);
		$curAward = '';
		$tmpAwarders = '';
		$tmpAward = '';
		$tmpCategory = '';
		$data=array();

		while($row = safe_fetch($rs))
		{
			if($curAward != $row->Category . "|" . $row->AwDescription)
			{
				if(count($data)>0) {
					writeData($pdf, $data, $tmpAward, $tmpCategory, $tmpAwarders, ($rowOrder->AwTeam==0 ? 1:0));
				}
				$data=array();
				$tmpAward =  $row->AwDescription;
				$tmpCategory = $row->Category;
				$tmpAwarders = $row->AwAwarders;
				$curAward = $row->Category . "|" . $row->AwDescription;
			}
			if($rowOrder->AwTeam) {
				$tmp=explode('|', $row->Athlete);
				$data[]=array($row->Rank,$row->Country, $tmp, $row->Score, $row->Gold, $row->XNine);
			} else {
				$data[]=array($row->Rank, $row->Athlete, $row->Country, $row->Score, $row->Gold, $row->XNine);
			}
			$idList[]=$row->EnId;
		}
		if(count($data)>0) {
			writeData($pdf, $data, $tmpAward, $tmpCategory, $tmpAwarders, ($rowOrder->AwTeam==0 ? 1:0));
		}
	}
}
$pdf->Output();

function writeGroupHeader($pdf, $Description, $Category, $indEvent)
{
	$pdf->SetFont($pdf->FontStd,'B',26);
	$pdf->Cell(0, 6, "Victory Ceremony -  " . $Category, 0, 1, 'C', 1);
	$pdf->ln(5);

	$pdf->SetFont($pdf->FontStd,'',12);
	$pdf->Cell(0, 6, "Ladies and Gentlemen the Victory Ceremony for the", 0, 1, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',12);
	$pdf->Cell(0, 6, $Description ? $Description : $Category, 0, 1, 'L', 0);
}

function writeData($pdf, $data, $Description, $Category, $Awarders, $indEvent)
{
	GLOBAL $par_RepresentCountry, $par_PlayAnthem;
	$arrOrisPosition=array('','Gold Medal','Silver Medal','Bronze Medal','4th Place','5th');
	$pdf->AddPage();
	$pdf->setXY(OrisPDF::leftMargin, $pdf->lastY);
	writeGroupHeader($pdf, $Description, $Category, $indEvent);
	writeHiLight($pdf, "GO FANFARE");
	$pdf->SetFont($pdf->FontStd,'',14);
	$pdf->MultiCell(0, 6, $Awarders, 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'',12);
	for($i=count($data)-1; $i>=0; $i--)	{
		if($indEvent) {
			writeHiLight($pdf);
			$pdf->Cell(0, 6, $arrOrisPosition[$data[$i][0]], 0, 1, 'L', 0);
			if($par_RepresentCountry)
				$pdf->Cell(0, 6, "representing " . $data[$i][2], 0, 1, 'L', 0);
			$pdf->SetFont($pdf->FontStd,'B',12);
			$pdf->Cell(0, 6, $data[$i][1], 0, 1, 'L', 0);
			$pdf->SetFont($pdf->FontStd,'',12);
		} else {
			$pdf->SetFont($pdf->FontStd,'B',12);
			writeHiLight($pdf);
			$pdf->Cell(0, 6, $arrOrisPosition[$data[$i][0]], 0, 1, 'L', 0);
			if($par_RepresentCountry) {
				$pdf->SetFont($pdf->FontStd,'B',12);
				$pdf->Cell(0, 6, $data[$i][1], 0, 1, 'L', 0);
				$pdf->SetFont($pdf->FontStd,'',12);
				$pdf->Cell(0, 6, "represented by ", 0, 1, 'L', 0);
			}
			for($j=0; $j<count($data[$i][2]); $j++) {
				$pdf->SetFont($pdf->FontStd,'',12);
				$pdf->Cell(0, 6, $data[$i][2][$j], 0, 1, 'L', 0);
			}
		}
	}
	if($par_PlayAnthem) {
		writeHiLight($pdf,"NATIONAL ANTHEM");
		$pdf->Cell(0, 7, "Ladies and Gentlemen, the National Anthem of ", 0, 1, 'L', 0);
		$pdf->SetFont($pdf->FontStd,'B',12);
		$pdf->Cell(0, 7, ($indEvent ? $data[0][2] : $data[0][1]), 0, 1, 'L', 0);
	}
	$pdf->SetFont($pdf->FontStd,'',12);
	writeHiLight($pdf,"ACKNOWLEDGE MEDALLISTS");
	$pdf->Cell(0, 7, "Ladies and Gentlemen, please give a warm round of applause to our Athletes", 0, 1, 'L', 0);
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