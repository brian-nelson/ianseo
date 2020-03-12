<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
define("HideCols", GetParameter("IntEvent"));

if(!isset($_REQUEST["Session"]) || !is_numeric($_REQUEST["Session"]) || !isset($_REQUEST["Distance"]) || !is_numeric($_REQUEST["Distance"]))
	exit;

if(!isset($isCompleteResultBook))
	$pdf = new ResultPDF((get_text('StartlistSession','Tournament')));

/*$MyQuery = "SELECT EnCode as Bib, EnName AS Name, EnFirstName AS FirstName, SUBSTRING(QuTargetNo,1,1) AS Session, SUBSTRING(QuTargetNo,2," . (TargetNoPadding+1) . ") AS TargetNo, CoCode AS NationCode, CoName AS Nation, EnClass AS ClassCode, EnDivision AS DivCode,EnAgeClass as AgeClass,  EnSubClass as SubClass, ClDescription, DivDescription, EnStatus as Status, EnIndClEvent AS `IC`, EnTeamClEvent AS `TC`, EnIndFEvent AS `IF`, EnTeamFEvent as `TF`, EnTeamMixEvent as `TM`, ";
$MyQuery.= "QuD" . $_REQUEST["Distance"]. "Score AS Score, QuD" . $_REQUEST["Distance"]. "Gold as Gold, QuD" . $_REQUEST["Distance"]. "Xnine as Xnine,";
$MyQuery.= "QuD5Score, QuD5Rank, QuD6Score, QuD6Rank, QuD7Score, QuD7Rank, QuD8Score, QuD8Rank, ";
$MyQuery.= "ToType, TtGolds, TtXNine ";
$MyQuery.= "FROM Tournament AS t ";
$MyQuery.= "INNER JOIN Tournament*Type AS tt ON t.ToType=tt.TtId ";
$MyQuery.= "INNER JOIN Entries AS e ON t.ToId=e.EnTournament ";
$MyQuery.= "INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament ";
$MyQuery.= "INNER JOIN Qualifications AS q ON e.EnId=q.QuId ";
$MyQuery.= "INNER JOIN Classes AS cl ON e.EnClass=cl.ClId AND ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
$MyQuery.= "INNER JOIN Divisions AS d ON e.EnDivision=d.DivId AND DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
$MyQuery.= "WHERE EnAthlete=1 AND ToId = " . StrSafe_DB($_SESSION['TourId']) . " ";
$MyQuery.= "AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) . " ";
$MyQuery.= "ORDER BY QuTargetNo, CoCode, Name, CoName, FirstName ";*/

$MyQuery = "SELECT EnCode as Bib, EnName AS Name, upper(EnFirstName) AS FirstName, SUBSTRING(QuTargetNo,1,1) AS Session, SUBSTRING(QuTargetNo,2," . (TargetNoPadding+1) . ") AS TargetNo, CoCode AS NationCode, CoName AS Nation, EnClass AS ClassCode, EnDivision AS DivCode,EnAgeClass as AgeClass,  EnSubClass as SubClass, ClDescription, DivDescription, EnStatus as Status, EnIndClEvent AS `IC`, EnTeamClEvent AS `TC`, EnIndFEvent AS `IF`, EnTeamFEvent as `TF`, EnTeamMixEvent as `TM`, ";
$MyQuery.= "upper(right(QuTargetNo,1)) TargetLetter, QuD" . $_REQUEST["Distance"]. "Score AS Score, QuD" . $_REQUEST["Distance"]. "Gold as Gold, QuD" . $_REQUEST["Distance"]. "Xnine as Xnine,";
$MyQuery.= "QuD5Score, QuD5Rank, QuD6Score, QuD6Rank, QuD7Score, QuD7Rank, QuD8Score, QuD8Rank, ";
$MyQuery.= "ToType, ToGolds AS TtGolds, ToXNine AS TtXNine ";
$MyQuery.= "FROM Tournament AS t ";
$MyQuery.= "INNER JOIN Entries AS e ON t.ToId=e.EnTournament ";
$MyQuery.= "INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament ";
$MyQuery.= "INNER JOIN Qualifications AS q ON e.EnId=q.QuId ";
$MyQuery.= "INNER JOIN Classes AS cl ON e.EnClass=cl.ClId AND ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
$MyQuery.= "INNER JOIN Divisions AS d ON e.EnDivision=d.DivId AND DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
$MyQuery.= "WHERE EnAthlete=1 AND ToId = " . StrSafe_DB($_SESSION['TourId']) . " ";
$MyQuery.= "AND QuSession = " . StrSafe_DB($_REQUEST["Session"]) . " ";
$MyQuery.= "ORDER BY QuTargetNo, CoCode, Name, CoName, FirstName ";

//*DEBUG*/echo $MyQuery;exit();
$Rs=safe_r_sql($MyQuery);
if($Rs)
{
	$ShowStatusLegend = false;
	$CurSession=-1;
	$OldTarget='';
	while($MyRow=safe_fetch($Rs))
	{
		if ($CurSession != $MyRow->Session || !$pdf->SamePage(4) || ($MyRow->TargetLetter=='A' && !$pdf->SamePage(16)))
		{
			$TmpSegue = !$pdf->SamePage(4);
			if($MyRow->TargetLetter=='A' && !$pdf->SamePage(16))
			{
				$TmpSegue=true;
				$pdf->AddPage();
			}
			elseif($CurSession != -1)
				$pdf->SetXY(10,$pdf->GetY()+5);
			$CurSession = $MyRow->Session;
		   	$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell(190, 6, get_text('Session') . " " . $CurSession . " - " . get_text('Distance','HTT') . " " . $_REQUEST["Distance"] , 1, 1, 'C', 1);
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
			$pdf->Cell(16, 4,  (get_text('Country')), 1, 0, 'L', 1);
			if(!HideCols)
			{
				$pdf->Cell(12, 4,  (get_text('AgeCl')), 1, 0, 'C', 1);
				$pdf->Cell(9, 4,  (get_text('SubCl','Tournament')), 1, 0, 'C', 1);
			}
			$pdf->Cell(12 + (HideCols==true ? 23 : 0), 4,  (get_text('Division')), 1, 0, 'C', 1);
			$pdf->Cell(12 + (HideCols==true ? 22 : 0), 4,  (HideCols==true ? get_text('Class') : get_text('Cl')), 1, 0, 'C', 1);
			//Disegna i Pallini
			if(!HideCols)
			{
				$pdf->DrawParticipantHeader();
				$pdf->SetFont($pdf->FontStd,'B',7);
				$pdf->Cell(10, 4,  (get_text('Status','Tournament')), 1, 0, 'C', 1);
			}
			$pdf->Cell(16, 4, get_text('TotaleScore'), 1, 0, 'C', 1);
			$pdf->Cell(12, 4, $MyRow->TtGolds, 1, 0, 'C', 1);
			$pdf->Cell(12, 4, $MyRow->TtXNine, 1, 0, 'C', 1);
			$pdf->Cell(1, 4,  '', 0, 1, 'C', 0);
			$OldTeam='';
			$FirstTime=false;
		}
	   	$pdf->SetFont($pdf->FontStd,'B',8);
		if($OldTarget != substr($MyRow->TargetNo,0,-1))
		{
			$OldTarget = substr($MyRow->TargetNo,0,-1);
			$pdf->SetFont($pdf->FontStd,'',1);
			$pdf->Cell(190, 0.5,  '', 0, 1, 'C', 0);
			$pdf->SetFont($pdf->FontStd,'B',8);
			$pdf->Cell(7, 4, (substr($MyRow->TargetNo,0,-1)), 'LTB', 0, 'R', 0);
			$pdf->Cell(4, 4,  (substr($MyRow->TargetNo,-1,1)), 'RTB', 0, 'R', 0);
		}
		else
		{
			$pdf->Cell(7, 4,  '', 0, 0, 'R', 0);
			$pdf->Cell(4, 4,  (substr($MyRow->TargetNo,-1,1)), 1, 0, 'R', 0);
		}
	   	$pdf->SetFont($pdf->FontStd,'',7);
		$pdf->Cell(10, 4,  ($MyRow->Bib), 1, 0, 'R', 0);
		$pdf->Cell(44, 4,  $MyRow->FirstName . ' ' . $MyRow->Name, 1, 0, 'L', 0);
		$pdf->Cell(16, 4,  $MyRow->NationCode, 1, 0, 'C', 0);
		if(!HideCols)
		{
			$pdf->Cell(12, 4,  ($MyRow->AgeClass), 1, 0, 'C', 0);
			$pdf->Cell(9, 4,  ($MyRow->SubClass), 1, 0, 'C', 0);
		}
		$pdf->Cell(12 + (HideCols==true ? 23 : 0), 4, (HideCols==true ? get_text($MyRow->DivDescription,'','',true) : $MyRow->DivCode), 1, 0, 'C', 0);
		$pdf->Cell(12 + (HideCols==true ? 22 : 0), 4,  (HideCols==true ? get_text($MyRow->ClDescription,'','',true) : $MyRow->ClassCode), 1, 0, 'C', 0);
//Disegna i Pallini per la partecipazione
		if(!HideCols)
		{
			$pdf->DrawParticipantDetails($MyRow->IC, $MyRow->IF, $MyRow->TC, $MyRow->TF, $MyRow->TM);
			$pdf->SetDefaultColor();
			$pdf->SetFont($pdf->FontStd,'',7);
			$ShowStatusLegend = ($ShowStatusLegend || ($MyRow->Status!=0));
			$pdf->Cell(10, 4,  ($MyRow->Status==0 ? '' : ($MyRow->Status)) , 1, 0, 'C', 0);
		}
		$pdf->SetFont($pdf->FontFix,'B',8);
		$pdf->Cell(16, 4,  $MyRow->Score, 1, 0, 'R', 0);
		$pdf->SetFont($pdf->FontFix,'',8);
		$pdf->Cell(12, 4,  $MyRow->Gold, 1, 0, 'R', 0);
		$pdf->Cell(12, 4,  $MyRow->Xnine, 1, 0, 'R', 0);


		$pdf->Cell(1, 4,  '' , 0, 1, 'C', 0);
	}
	if(!HideCols)
	{
	//Legenda per la partecipazione alle varie fasi
		$pdf->DrawPartecipantLegend();
	//Legenda per lo stato di ammisisone alle gare
		if($ShowStatusLegend)
			$pdf->DrawStatusLegend();
	}

	safe_free_result($Rs);
}
if(!isset($isCompleteResultBook))
{
	if(isset($_REQUEST['ToFitarco']))
	{
		$Dest='D';
		if (isset($_REQUEST['Dest']))
			$Dest=$_REQUEST['Dest'];
		$pdf->Output($_REQUEST['ToFitarco'],$Dest);
	}
	else
		$pdf->Output();
}
?>