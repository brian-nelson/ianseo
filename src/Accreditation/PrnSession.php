<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACL(AclParticipants, AclReadOnly);
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');

$OpDetails = "Accreditation";
if(isset($_REQUEST["OperationType"]))
	$OpDetails = $_REQUEST["OperationType"];

if(!isset($isCompleteResultBook))
	$pdf = new ResultPDF(get_text($OpDetails,'Tournament'));



$MyQuery = "SELECT EnCode as Bib, EnName AS Name, FirstName, SUBSTRING(AtTargetNo,1,1) AS Session, SUBSTRING(AtTargetNo,2," . (TargetNoPadding+1) . ") AS TargetNo, CoCode AS NationCode, CoName AS Nation, EnClass AS ClassCode, EnDivision AS DivCode, EnAgeClass as AgeClass, EnSubClass as SubClass, EnStatus as Status, EnIndClEvent AS `IC`, EnTeamClEvent AS `TC`, EnIndFEvent AS `IF`, EnTeamFEvent as `TF`, EnTeamMixEvent as `TM`, if(AEId IS NULL, 0, 1) as OpDone ";
$MyQuery.= "FROM AvailableTarget at ";
$MyQuery.= "LEFT JOIN ";
$MyQuery.= "(SELECT QuTargetNo, EnId, EnCode, EnName, upper(EnFirstName) AS FirstName, CoCode, CoName, EnClass, EnDivision, EnAgeClass, EnSubClass, EnStatus, EnIndClEvent, EnTeamClEvent, EnIndFEvent, EnTeamFEvent, EnTeamMixEvent ";
$MyQuery.= "FROM Qualifications AS q  ";
$MyQuery.= "INNER JOIN Entries AS e ON q.QuId=e.EnId AND e.EnTournament= " . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1 ";
$MyQuery.= "INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament) as Sq ON at.AtTargetNo=Sq.QuTargetNo ";
$MyQuery.= "LEFT JOIN AccEntries AS ae ON Sq.EnId=ae.AEId AND at.AtTournament=ae.AETournament ";
$MyQuery.= "AND ae.AEOperation=(SELECT AOTId FROM AccOperationType WHERE AOTDescr=" . StrSafe_DB($OpDetails) . ") ";
$MyQuery.= "WHERE AtTournament = " . StrSafe_DB($_SESSION['TourId']) . " ";
if(isset($_REQUEST["Session"]) && is_numeric($_REQUEST["Session"]))
	$MyQuery .= "AND SUBSTRING(AtTargetNo,1,1) = " . StrSafe_DB($_REQUEST["Session"]) . " ";
$MyQuery.= "ORDER BY AtTargetNo, CoCode, Name, CoName, FirstName ";
//echo $MyQuery;exit;
$Rs=safe_r_sql($MyQuery);
if($Rs)
{
	$ShowStatusLegend = false;
	$CurSession=-1;
	$OldTarget='';
	while($MyRow=safe_fetch($Rs))
	{
		$pdf->SetDefaultColor();
		if ($CurSession != $MyRow->Session || !$pdf->SamePage(4) || (strtoupper(substr($MyRow->TargetNo,-1,1))=='A' && !$pdf->SamePage(16)))
		{
			$TmpSegue = !$pdf->SamePage(4);
			if(strtoupper(substr($MyRow->TargetNo,-1,1))=='A' && !$pdf->SamePage(16))
			{
				$TmpSegue=true;
				$pdf->AddPage();
			}
			elseif($CurSession != -1)
				$pdf->SetXY(10,$pdf->GetY()+5);
			$CurSession = $MyRow->Session;


		   	$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell(190, 6,  (get_text('Session')) . " " . $CurSession . " (" . (get_text($OpDetails,'Tournament')) . ")", 1, 1, 'C', 1);
			if($TmpSegue)
			{
				$pdf->SetXY(170,$pdf->GetY()-6);
			   	$pdf->SetFont($pdf->FontStd,'I',6);
				$pdf->Cell(30, 6,  (get_text('Continue')), 0, 1, 'R', 0);
			}
		   	$pdf->SetFont($pdf->FontStd,'B',7);
			$pdf->Cell(11, 4,  (get_text('Target')), 1, 0, 'C', 1);
			$pdf->Cell(10, 4,  (get_text('Code','Tournament')), 1, 0, 'C', 1);
			$pdf->Cell(44, 4,  (get_text('Athlete')), 1, 0, 'L', 1);
			$pdf->Cell(56, 4,  (get_text('Country')), 1, 0, 'L', 1);
			$pdf->Cell(12, 4,  (get_text('AgeCl')), 1, 0, 'C', 1);
			$pdf->Cell(9, 4,  (get_text('SubCl','Tournament')), 1, 0, 'C', 1);
			$pdf->Cell(12, 4,  (get_text('Division')), 1, 0, 'C', 1);
			$pdf->Cell(12, 4,  (get_text('Cl')), 1, 0, 'C', 1);
			//Disegna i Pallini
			$pdf->DrawParticipantHeader();
			$pdf->SetFont($pdf->FontStd,'B',7);
			$pdf->Cell(10, 4,  (get_text('Status','Tournament')), 1, 1, 'C', 1);
			$OldTeam='';
			$FirstTime=false;
		}
		if($OldTarget != substr($MyRow->TargetNo,0,-1))
		{
			$OldTarget = substr($MyRow->TargetNo,0,-1);
			$pdf->SetFont($pdf->FontStd,'',1);
			$pdf->Cell(190, 0.5,  '', 1, 1, 'C', 0);
		}
//Disegno il quadrato pieno per gli accreditati
		if($MyRow->OpDone!=0)
			$pdf->SetFillColor(0xCC,0xCC,0xCC);
		else
			$pdf->SetDefaultColor();
		$pdf->Cell(3, 4,  '', 'LTB', 0, 'R', $MyRow->OpDone);
//Inizio dei dati "scritti"
	   	$pdf->SetFont($pdf->FontStd,'B',7);
		if($MyRow->OpDone!=0)
			$pdf->SetAccreditedColor();
		else
			$pdf->SetDefaultColor();
		$pdf->Cell(8, 4,  ($MyRow->TargetNo), 'RTB', 0, 'R', $MyRow->OpDone);
	   	$pdf->SetFont($pdf->FontStd,'',7);
		$pdf->Cell(10, 4,  ($MyRow->Bib), 1, 0, 'R', $MyRow->OpDone);
		$pdf->Cell(44, 4,  $MyRow->FirstName . ' ' . $MyRow->Name, 1, 0, 'L', $MyRow->OpDone);
		$pdf->Cell(8, 4,  $MyRow->NationCode, 'LTB', 0, 'C', $MyRow->OpDone);
		$pdf->Cell(48, 4,  $MyRow->Nation, 'RTB', 0, 'L', $MyRow->OpDone);
		$pdf->Cell(12, 4,  ($MyRow->AgeClass), 1, 0, 'C', $MyRow->OpDone);
		$pdf->Cell(9, 4,  ($MyRow->SubClass), 1, 0, 'C', $MyRow->OpDone);
		$pdf->Cell(12, 4,  ($MyRow->DivCode), 1, 0, 'C', $MyRow->OpDone);
		$pdf->Cell(12, 4,  ($MyRow->ClassCode), 1, 0, 'C', $MyRow->OpDone);
//Disegna i Pallini per la partecipazione
		$pdf->DrawParticipantDetails($MyRow->IC, $MyRow->IF, $MyRow->TC, $MyRow->TF, $MyRow->TM, $MyRow->OpDone);
		if($MyRow->OpDone!=0)
			$pdf->SetAccreditedColor();
		else
			$pdf->SetDefaultColor();
		$pdf->SetFont($pdf->FontStd,'',7);
		$ShowStatusLegend = ($ShowStatusLegend || ($MyRow->Status!=0));
		$pdf->Cell(10, 4,  ($MyRow->Status==0 ? '' : ($MyRow->Status)) , 1, 1, 'C', $MyRow->OpDone);
	}

//Legenda per la partecipazione alle varie fasi
	$pdf->DrawPartecipantLegend();
//Legenda per lo stato di ammisisone alle gare
	if($ShowStatusLegend)
		$pdf->DrawStatusLegend();

	safe_free_result($Rs);
}
if(!isset($isCompleteResultBook))
	$pdf->Output();
?>