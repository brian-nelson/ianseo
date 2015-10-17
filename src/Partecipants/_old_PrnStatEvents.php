<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Qualification/Fun_Qualification.local.inc.php');
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_Phases.inc.php');

if(!isset($isCompleteResultBook))
	$pdf = new ResultPDF((get_text('StatEvents','Tournament')),true);

$listClDiv = array();
$DivArray = array();

$MyQuery = "SELECT DivId, ClId
	FROM Classes INNER JOIN Divisions ON DivTournament=ClTournament
	WHERE ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (DivAthlete=1 and ClAthlete=1)
	ORDER BY ClViewOrder, ClId, DivViewOrder, DivId ";
$Rs = safe_r_sql($MyQuery);
if(safe_num_rows($Rs)>0)
{
	while($MyRow=safe_fetch($Rs))
	{
		if(!array_key_exists($MyRow->ClId,$listClDiv))
			$listClDiv[$MyRow->ClId] = array();
		if(!array_key_exists($MyRow->DivId,$listClDiv[$MyRow->ClId]))
			$listClDiv[$MyRow->ClId][$MyRow->DivId]=array("I"=>'',"S"=>'');
		if(!in_array($MyRow->DivId,$DivArray))
			$DivArray[]=$MyRow->DivId;
	}
	safe_free_result($Rs);
}

//Parte per le premiazioni
$Sql = "SELECT EnDivision as Divisione, EnClass as Classe, SUM(EnIndClEvent) as QuantiInd, IFNULL(numTeam,0) AS QuantiSq
	FROM Entries
	inner join Divisions on EnDivision=DivId and DivAthlete=1 and DivTournament=" . StrSafe_DB($_SESSION['TourId']) . "
	inner join Classes on EnClass=ClId and ClAthlete=1 and ClTournament=" . StrSafe_DB($_SESSION['TourId']) . "
	LEFT JOIN (
	  SELECT sqDiv, sqCl, COUNT(sqQuanti) as numTeam
	  FROM
	    (SELECT EnDivision as sqDiv, EnClass as sqCl, COUNT(EnId) as sqQuanti
	    FROM Entries
	    WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnTeamClEvent=1
	    GROUP BY EnDivision, EnClass, IF(EnCountry2=0,EnCountry,EnCountry2), EnSubTeam
	    HAVING sqQuanti>=3) as sq
	  GROUP BY sqDiv, sqCl
	) AS sqy ON EnDivision=sqDiv AND EnClass=sqCl
	WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . "
	GROUP BY EnDivision, EnClass, numTeam";

$Rs=safe_r_sql($Sql);
if(safe_num_rows($Rs)>0)
{
	while($MyRow=safe_fetch($Rs))
	{
		$listClDiv[$MyRow->Classe][$MyRow->Divisione]["I"]=$MyRow->QuantiInd;
		$listClDiv[$MyRow->Classe][$MyRow->Divisione]["S"]=$MyRow->QuantiSq;
	}
}

//debug_svela($listClDiv);

$FirstTime=true;
$DivSize=(($pdf->getPageWidth()-35)/count($DivArray));
foreach($listClDiv as $cl=>$singleClass)
{

	if ($FirstTime || !$pdf->SamePage(16))
	{
		$TmpSegue = !$pdf->SamePage(16);
		if($TmpSegue)
			$pdf->AddPage();
	   	$pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->SetXY(25,$pdf->GetY()+5);
		$pdf->Cell(($pdf->getPageWidth()-35), 6,  (get_text('StatEvents','Tournament')), 1, 1, 'C', 1);
		if($TmpSegue)
		{
			$pdf->SetXY(($pdf->getPageWidth()-40),$pdf->GetY()-6);
		   	$pdf->SetFont($pdf->FontStd,'I',6);
			$pdf->Cell(30, 6,  (get_text('Continue')), 0, 1, 'R', 0);
		}
		$pdf->SetX(25);
	   	$pdf->SetFont($pdf->FontStd,'B',10);
		foreach($DivArray as $Value)
		{
			$pdf->Cell($DivSize-0.5, 6,  $Value, 1, 0, 'C', 1);
			$pdf->Cell(0.5, 6, '' , 1, 0, 'C', 1);
		}
		$pdf->Cell(0.1, 6,  '', 0, 1, 'C', 0);
		$pdf->SetX(25);
		$pdf->SetFont($pdf->FontStd,'',7);
		foreach($DivArray as $Value)
		{
			$pdf->Cell($DivSize/2, 6, get_text('Individual'), 1, 0, 'C', 1);
			$pdf->Cell($DivSize/2-0.5, 6, get_text('Team'), 1, 0, 'C', 1);
			$pdf->Cell(0.5, 6, '' , 1, 0, 'C', 1);
		}
		$pdf->Cell(0.1, 6,  '', 0, 1, 'C', 0);

		$FirstTime=false;
	}
	$pdf->SetFont($pdf->FontStd,'',8);
	$pdf->Cell(15, 5, $cl, 1, 0, 'C', 1);
	foreach($DivArray as $value)
	{
		$pdf->Cell($DivSize/2, 5, $singleClass[$value]["I"], 1, 0, 'R', 0);
		$pdf->Cell($DivSize/2-0.5, 5, $singleClass[$value]["S"], 1, 0, 'R', 0);
		$pdf->Cell(0.5, 5, '' , 1, 0, 'C', 0);
	}
	$pdf->Cell(0.1, 5,  '', 0, 1, 'C', 0);
}

//Eventi Individuali
$DivSize=(($pdf->getPageWidth()-35)/6);
$Sql = "SELECT EvCode as Code, EvEventName as EventName, EvFinalFirstPhase as FirstPhase, COUNT(EnId) as Quanti
	FROM Events
	INNER JOIN EventClass ON EvCode=EcCode AND EvTeamEvent=EcTeamEvent AND EvTournament=EcTournament
	INNER JOIN Individuals ON EvCode=IndEvent AND EvTournament=IndTournament
	INNER JOIN Entries ON EnId=IndId AND EnTournament=IndTournament AND EcClass=EnClass AND EcDivision=EnDivision
	WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0 AND ((EnIndFEvent=1 AND EnStatus<=1) OR EnId IS NULL)
	GROUP BY EvCode, EvFinalFirstPhase
	ORDER BY EvProgr";

$Rs=safe_r_sql($Sql);
if(safe_num_rows($Rs)>0)
{
	$FirstTime=true;
	while($MyRow=safe_fetch($Rs))
	{
		if ($FirstTime || !$pdf->SamePage(16))
		{
			$TmpSegue = !$pdf->SamePage(16);
			if($TmpSegue)
				$pdf->AddPage();
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->SetXY(25,$pdf->GetY()+5);
			$pdf->Cell($DivSize*6, 6,get_text('IndFinal'), 1, 1, 'C', 1);
			if($TmpSegue)
			{
				$pdf->SetXY(($pdf->getPageWidth()-40),$pdf->GetY()-6);
				$pdf->SetFont($pdf->FontStd,'I',6);
				$pdf->Cell(30, 6,  (get_text('Continue')), 0, 1, 'R', 0);
			}
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->SetX(25);
			$pdf->Cell($DivSize*2, 6, get_text('EvName'), 1, 0, 'C', 1);
			$pdf->Cell($DivSize, 6, get_text('Athletes'), 1, 0, 'C', 1);
			$pdf->Cell($DivSize, 6, get_text('FirstPhase'), 1, 0, 'C', 1);
			$pdf->Cell($DivSize, 6, get_text('FirstPhaseMatchesBye','Tournament'), 1, 0, 'C', 1);
			$pdf->Cell($DivSize, 6, get_text('FirstPhaseInOut','Tournament'), 1, 1, 'C', 1);
			$FirstTime=false;
		}
		$tmpSaved=valueFirstPhase($MyRow->FirstPhase)==$MyRow->FirstPhase ? 0 : 8;
		$tmpQuantiIn = maxPhaseRank($MyRow->FirstPhase);
		$tmpQuantiOut = $MyRow->Quanti-$tmpQuantiIn;
		$tmpMatch = (min($tmpQuantiIn,$MyRow->Quanti) -$tmpSaved)-$MyRow->FirstPhase;
		$tmpBye = $MyRow->FirstPhase-$tmpMatch;
		$pdf->SetFont($pdf->FontStd,'',8);
		$pdf->Cell(15, 5, $MyRow->Code, 1, 0, 'C', 1);
		$pdf->Cell($DivSize*2, 5, $MyRow->EventName, 1, 0, 'L', 0);
		$pdf->Cell($DivSize, 5, $MyRow->Quanti, 1, 0, 'R', 0);
		$pdf->Cell($DivSize, 5, ($MyRow->FirstPhase==0 ? "" : get_text(namePhase($MyRow->FirstPhase,$MyRow->FirstPhase).'_Phase')), 1, 0, 'R', ($tmpMatch<=0 ? 1:0));
		$pdf->Cell($DivSize/2, 5, ($MyRow->FirstPhase==0 ? "" : $tmpMatch), 'TBL', 0, 'R', ($tmpMatch<=0 ? 1:0));
		$pdf->Cell($DivSize/2, 5, ($MyRow->FirstPhase==0  || $tmpMatch<0 ? "" : (($tmpBye + $tmpSaved)==0 ? '' : '(' . $tmpBye . ($tmpSaved!=0 ? '+' . $tmpSaved : '') . ')')), 'TBR', 0, 'R', ($tmpMatch<=0 ? 1:0));
		$pdf->Cell($DivSize/2, 5, ($MyRow->FirstPhase==0 ? "" : ($MyRow->Quanti < $tmpQuantiIn ? $MyRow->Quanti : $tmpQuantiIn)), 'TBL', 0, 'R', ($tmpMatch<=0 ? 1:0));
		$pdf->Cell($DivSize/2, 5, ($MyRow->FirstPhase==0 ? "" : ($tmpQuantiOut>0 ? '(' . $tmpQuantiOut . ')' : '-----')), 'TBR', 1, 'R', ($tmpMatch<=0 ? 1:0));
	}
}

// Squadre
$Sql = "SELECT EvCode, EvEventName as EventName, EvFinalFirstPhase as FirstPhase, EvMixedTeam, EvMultiTeam, EvMaxTeamPerson,EvTeamCreationMode FROM Events WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 ORDER BY EvProgr";
$RsEv=safe_r_sql($Sql);
if(safe_num_rows($RsEv)>0)
{
	$FirstTime=true;

	while($MyRowEv=safe_fetch($RsEv))
	{
		if ($FirstTime || !$pdf->SamePage(16))
		{
			$TmpSegue = !$pdf->SamePage(16);
			if($TmpSegue)
				$pdf->AddPage();
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->SetXY(25,$pdf->GetY()+5);
			$pdf->Cell($DivSize*6, 6, 	get_text('TeamFinal'), 1, 1, 'C', 1);
			if($TmpSegue)
			{
				$pdf->SetXY(($pdf->getPageWidth()-40),$pdf->GetY()-6);
				$pdf->SetFont($pdf->FontStd,'I',6);
				$pdf->Cell(30, 6,  (get_text('Continue')), 0, 1, 'R', 0);
			}
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->SetX(25);
			$pdf->Cell($DivSize*4/3, 6, get_text('EvName'), 1, 0, 'C', 1);
			$pdf->Cell($DivSize*2/3, 6, get_text('Teams'), 1, 0, 'C', 1);
			$pdf->Cell($DivSize, 6, get_text('FirstPhase'), 1, 0, 'C', 1);
			$pdf->Cell($DivSize, 6, get_text('FirstPhaseMatchesBye','Tournament'), 1, 0, 'C', 1);
			$pdf->Cell($DivSize, 6, get_text('FirstPhaseInOut','Tournament'), 1, 0, 'C', 1);
			$pdf->Cell($DivSize, 6, get_text('MixedTeamEvent'), 1, 1, 'C', 1);
			$FirstTime=false;
		}
		$Sql = "SELECT DISTINCT EcCode, EcTeamEvent, EcNumber FROM EventClass WHERE EcCode=" . StrSafe_DB($MyRowEv->EvCode) . " AND EcTeamEvent!=0 AND EcTournament=" . StrSafe_DB($_SESSION['TourId']);
		$RsEc=safe_r_sql($Sql);
		if(safe_num_rows($RsEc)>0)
		{
			$RuleCnt=0;
			$Sql = "Select * ";
			while($MyRowEc=safe_fetch($RsEc))
			{
				$ifc=ifSqlForCountry($MyRowEv->EvTeamCreationMode);
				$Sql .= (++$RuleCnt == 1 ? "FROM ": "INNER JOIN ");
				$Sql .= "(SELECT {$ifc} as C" . $RuleCnt . ", SUM(IF(EnSubTeam=0,1,0)) AS QuantiMulti
					  FROM Entries
					  INNER JOIN EventClass ON EnClass=EcClass AND EnDivision=EcDivision AND EnTournament=EcTournament AND EcTeamEvent=" . $MyRowEc->EcTeamEvent . " AND EcCode=" . StrSafe_DB($MyRowEc->EcCode) . "
					  WHERE {$ifc}<>0 AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnTeam" . ($MyRowEv->EvMixedTeam ? 'Mix' : 'F') ."Event=1
					  group by {$ifc}, EnSubTeam
					  HAVING COUNT(EnId)>=" . $MyRowEc->EcNumber . ") as sqy";
				$Sql .= ($RuleCnt == 1 ? " ": $RuleCnt . " ON C1=C". $RuleCnt . " ");
			}

			$Rs=safe_r_sql($Sql);
			$tmpQuanti=safe_num_rows($Rs);

			$pdf->SetFont($pdf->FontStd,'',8);
			$pdf->Cell(15, 5, $MyRowEv->EvCode, 1, 0, 'C', 1);
			$pdf->Cell($DivSize*4/3, 5, $MyRowEv->EventName, 1, 0, 'L', 0);
			if($MyRowEv->EvMultiTeam!=0)
			{
				$tmpQuanti = 0;
				while($tmpRow=safe_fetch($Rs))
					$tmpQuanti += intval($tmpRow->QuantiMulti / $MyRowEv->EvMaxTeamPerson);
			}

			$pdf->Cell($DivSize*2/3, 5, $tmpQuanti, 1, 0, 'R', 0);
			$tmpSaved=valueFirstPhase($MyRowEv->FirstPhase)==$MyRowEv->FirstPhase ? 0 : 8;
			$tmpQuantiIn = maxPhaseRank($MyRowEv->FirstPhase);
			$tmpQuantiOut = $tmpQuanti-$tmpQuantiIn;
			$tmpMatch = (min($tmpQuantiIn,$tmpQuanti) -$tmpSaved)-$MyRowEv->FirstPhase;
			$tmpBye = $MyRowEv->FirstPhase-$tmpMatch;

			$pdf->Cell($DivSize, 5, ($MyRowEv->FirstPhase==0 ? "" : get_text(namePhase($MyRowEv->FirstPhase,$MyRowEv->FirstPhase).'_Phase')), 1, 0, 'R', ($tmpMatch<=0 ? 1:0));
			$pdf->Cell($DivSize/2, 5, ($MyRowEv->FirstPhase==0 ? "" : $tmpMatch), 'TBL', 0, 'R', ($tmpMatch<=0 ? 1:0));
			$pdf->Cell($DivSize/2, 5, ($MyRowEv->FirstPhase==0  || $tmpMatch<0 ? "" : '(' . $tmpBye . ($tmpSaved!=0 ? '+' . $tmpSaved : '') . ')'), 'TBR', 0, 'R', ($tmpMatch<=0 ? 1:0));
			$pdf->Cell($DivSize/2, 5, ($MyRowEv->FirstPhase==0 ? "" : ($tmpQuanti < $tmpQuantiIn ? $tmpQuanti : $tmpQuantiIn)), 'TBL', 0, 'R', ($tmpMatch<=0 ? 1:0));
			$pdf->Cell($DivSize/2, 5, ($MyRowEv->FirstPhase==0 ? "" : ($tmpQuantiOut>0 ? '(' . $tmpQuantiOut . ')' : '---')), 'TBR', 0, 'R', ($tmpMatch<=0 ? 1:0));
			$pdf->Cell($DivSize, 5, get_text($MyRowEv->EvMixedTeam ? 'Yes' : 'No'), 1, 1, 'C', 0);
		}
	}
}

if(!isset($isCompleteResultBook))
	$pdf->Output();
?>