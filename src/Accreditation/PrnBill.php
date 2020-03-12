<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACL(AclParticipants, AclReadOnly);
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_Number.inc.php');

$OpDetails = "Accreditation";
if(isset($_REQUEST["OperationType"]))
	$OpDetails = $_REQUEST["OperationType"];

if(!isset($isCompleteResultBook))
	$pdf = new ResultPDF((get_text('StartlistCountry','Tournament')));

$TmpWhere="";
if(isset($_REQUEST["CountryName"]) && preg_match("/^[-,0-9A-Z]*$/i",str_replace(" ","",$_REQUEST["CountryName"])))
{
	foreach(explode(",",$_REQUEST["CountryName"]) as $Value)
	{
		$Tmp=NULL;
		if(preg_match("/^([A-Z0-9]*)\-([A-Z0-9]*)$/i",str_replace(" ","",$Value),$Tmp))
			$TmpWhere .= "(CoCode >= " . StrSafe_DB(strtoupper($Tmp[1]) ) . " AND CoCode <= " . StrSafe_DB(strtoupper($Tmp[2].chr(255))) . ") OR ";
		else
			$TmpWhere .= "CoCode LIKE " . StrSafe_DB(strtoupper(trim($Value)) . "%") . " OR CONCAT(EnFirstName, ' ', EnName) LIKE " . StrSafe_DB(strtoupper(trim($Value)) . "%") . " OR ";
	}
	$TmpWhere = substr($TmpWhere,0,-3);
}

$MyQuery = "SELECT EnCode as Bib, EnPays,EnName AS Name, upper(EnFirstName) AS FirstName, QuSession AS Session, SUBSTRING(QuTargetNo,2) AS TargetNo, CoCode AS NationCode, CoName AS Nation, EnClass AS ClassCode, EnDivision AS DivCode, EnAgeClass as AgeClass, EnSubClass as SubClass, EnStatus as Status, EnIndClEvent AS `IC`, EnTeamClEvent AS `TC`, EnIndFEvent AS `IF`, EnTeamFEvent as `TF`, EnTeamMixEvent as `TM`, APPrice ";
$MyQuery.= "FROM Entries AS e ";
$MyQuery.= "INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament ";
$MyQuery.= "INNER JOIN Qualifications AS q ON e.EnId=q.QuId ";
$MyQuery.= "INNER JOIN AccEntries AS ae ON e.EnId=ae.AEId AND e.EnTournament=ae.AETournament ";
$MyQuery.= "AND ae.AEOperation=(SELECT AOTId FROM AccOperationType WHERE AOTDescr=" . StrSafe_DB($OpDetails) . ") ";
$MyQuery.= "INNER JOIN AccPrice AS ap ON CONCAT(EnDivision,EnClass) LIKE ap.APDivClass AND e.EnTournament=ap.APTournament ";
$MyQuery.= "WHERE EnAthlete=1 AND EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " ";
if(isset($_REQUEST["Session"]) && is_numeric($_REQUEST["Session"]))
	$MyQuery .= "AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) . " ";
if($TmpWhere != "")
	$MyQuery .= "AND (" . $TmpWhere . ")";
$MyQuery.= "ORDER BY CoCode, Name, CoName, FirstName, TargetNo ";
//echo $MyQuery;exit;
$Rs=safe_r_sql($MyQuery);
if($Rs)
{
	$ShowStatusLegend = false;
	$OldTeam='#@#@#';
	$isFirstTime=true;
	$TotalPrice=0;
	while($MyRow=safe_fetch($Rs))
	{

		if($OldTeam != $MyRow->NationCode)
		{
			if(!$isFirstTime)
			{
				$pdf->SetFont($pdf->FontStd,'',8);
				$pdf->Cell(165, 15, get_text('Cash', 'Tournament') . ":", 0, 0, 'R', 0);
				$pdf->SetFont($pdf->FontStd,'B',12);
				$pdf->Cell(25, 15, NumFormat($TotalPrice,2) . " " . $pdf->writeCurrency(), 0, 1, 'R', 0);
				$pdf->AddPage();
			}
			$isFirstTime=false;
			$pdf->SetXY(10,$pdf->GetY()+5);

		   	$pdf->SetFont($pdf->FontStd,'',8);
			$pdf->Cell($pdf->GetStringWidth(get_text('Country'))+5, 10, (get_text('Country')), 0, 0, 'L', 0);
		   	$pdf->SetFont($pdf->FontStd,'B',12);
			$pdf->Cell(15, 10,  $MyRow->NationCode, 0, 0, 'C', 0);
			$pdf->Cell(50, 10,  $MyRow->Nation, 0, 1, 'L', 0);
		   	$pdf->SetFont($pdf->FontStd,'',8);
			$pdf->Cell(19, 10, get_text('Partecipants'), 0, 1, 'L', 0);
		   	$pdf->SetFont($pdf->FontStd,'B',7);
			$pdf->Cell(10, 4,  '', 0, 0, 'C', 0);
			$pdf->Cell(10, 4, get_text('SessionShort','Tournament'), 1, 0, 'C', 1);
			$pdf->Cell(15, 4, get_text('Target'), 1, 0, 'C', 1);
			$pdf->Cell(10, 4, get_text('Code','Tournament'), 1, 0, 'C', 1);
			$pdf->Cell(45, 4, get_text('Athlete'), 1, 0, 'L', 1);
			$pdf->Cell(15, 4, get_text('AgeCl'), 1, 0, 'C', 1);
			$pdf->Cell(10, 4, get_text('SubCl','Tournament'), 1, 0, 'C', 1);
			$pdf->Cell(15, 4, get_text('Division'), 1, 0, 'C', 1);
			$pdf->Cell(15, 4, get_text('Cl'), 1, 0, 'C', 1);
			//Disegna i Pallini
			$pdf->DrawParticipantHeader();
			$pdf->SetFont($pdf->FontStd,'B',7);
			$pdf->Cell(10, 4, get_text('Status','Tournament'), 1, 0, 'C', 1);
			$pdf->Cell(20, 4, get_text('Price','Tournament'), 1, 1, 'C', 1);
			$OldTeam = $MyRow->NationCode;
			$pdf->SetFont($pdf->FontStd,'',1);
			$pdf->Cell(190, 0.5,  '', 0, 1, 'C', 0);
			$OldTeam = $MyRow->NationCode;
			$TotalPrice=0;
			$ShowStatusLegend = false;
		}
	   	$pdf->SetFont($pdf->FontStd,'',7);
		$pdf->Cell(10, 4,  '', 0, 0, 'C', 0);
		$pdf->Cell(10, 4,  ($MyRow->Session), 1, 0, 'R', 0);
		$pdf->Cell(15, 4,  ($MyRow->TargetNo), 1, 0, 'R', 0);
		$pdf->Cell(10, 4,  ($MyRow->Bib), 1, 0, 'R', 0);
		$pdf->Cell(45, 4,  $MyRow->FirstName . ' ' . $MyRow->Name, 1, 0, 'L', 0);
		$pdf->Cell(15, 4,  ($MyRow->AgeClass), 1, 0, 'C', 0);
		$pdf->Cell(10, 4,  ($MyRow->SubClass), 1, 0, 'C', 0);
		$pdf->Cell(15, 4,  ($MyRow->DivCode), 1, 0, 'C', 0);
		$pdf->Cell(15, 4,  ($MyRow->ClassCode), 1, 0, 'C', 0);
//Disegna i Pallini per la partecipazione
		$pdf->DrawParticipantDetails($MyRow->IC, $MyRow->IF, $MyRow->TC, $MyRow->TF, $MyRow->TM);
		$pdf->SetDefaultColor();
		$pdf->SetFont($pdf->FontStd,'',7);
		$ShowStatusLegend = ($ShowStatusLegend || ($MyRow->Status!=0));
		$pdf->Cell(10, 4,  ($MyRow->Status==0 ? '' : ($MyRow->Status)) , 1, 0, 'C', 0);
		$pdf->Cell(20, 4,  ($MyRow->EnPays==1 ? NumFormat($MyRow->APPrice,2) : NumFormat(0,2)) . " " . $pdf->writeCurrency(), 1, 1, 'R', 0);
		$TotalPrice += ($MyRow->EnPays==1 ? $MyRow->APPrice : 0);
	}
	$pdf->SetFont($pdf->FontStd,'',8);
	$pdf->Cell(165, 15, get_text('Cash', 'Tournament'). ":", 0, 0, 'R', 0);
	$pdf->SetFont($pdf->FontStd,'B',12);
	$pdf->Cell(25, 15, NumFormat($TotalPrice,2) . " " . $pdf->writeCurrency(), 0, 1, 'R', 0);


	safe_free_result($Rs);
}
if(!isset($isCompleteResultBook))
	$pdf->Output();
?>