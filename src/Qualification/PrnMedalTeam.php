<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/Fun_PrintOuts.php');
checkACL(AclQualification, AclReadOnly);

if(!isset($isCompleteResultBook))
	$pdf = new ResultPDF(get_text('MedalSqClass','Tournament'));

$MyQuery = "SELECT CoCode AS NationCode, CoName AS Nation, TeEvent, ClDescription, DivDescription, Quanti, upper(EnFirstName) as FirstName, EnName AS Name,  EnClass AS ClassCode, EnDivision AS DivCode,EnAgeClass as AgeClass,  EnSubClass as SubClass, ";
$MyQuery.= "QuScore, TeScore, TeRank, TeGold, TeXnine, ToGolds AS TtGolds, ToXNine AS TtXNine  ";
$MyQuery.= "FROM Tournament AS t ";
$MyQuery.= "INNER JOIN Teams AS te ON t.ToId=te.TeTournament AND te.TeFinEvent=0 ";
$MyQuery.= "INNER JOIN Countries AS c ON te.TeCoId=c.CoId AND te.TeTournament=c.CoTournament ";
$MyQuery.= "INNER JOIN (SELECT TcCoId, TcEvent, TcTournament, TcFinEvent, COUNT(TcId) as Quanti FROM TeamComponent GROUP BY TcCoId, TcEvent, TcTournament, TcFinEvent) AS sq ON te.TeCoId=sq.TcCoId AND te.TeEvent=sq.TcEvent AND te.TeTournament=sq.TcTournament AND te.TeFinEvent=sq.TcFinEvent ";
$MyQuery.= "INNER JOIN TeamComponent AS tc ON te.TeCoId=tc.TcCoId AND te.TeEvent=tc.TcEvent AND te.TeTournament=tc.TcTournament AND te.TeFinEvent=tc.TcFinEvent ";
$MyQuery.= "INNER JOIN Entries AS en ON tc.TcId=en.EnId ";
$MyQuery.= "INNER JOIN Qualifications AS q ON en.EnId=q.QuId ";
$MyQuery.= "INNER JOIN (select concat(DivId, ClId) DivClass, Divisions.*, Classes.* from Divisions inner join Classes on DivTournament=ClTournament where DivAthlete and ClAthlete) DivClass on te.TeEvent=DivClass and te.TeTournament=DivTournament ";
$MyQuery.= "WHERE ToId = " . StrSafe_DB($_SESSION['TourId']) . " ";
if(isset($_REQUEST["Definition"])) $MyQuery .= "AND te.TeEvent LIKE " . StrSafe_DB($_REQUEST["Definition"]) . " ";
if(isset($_REQUEST["Classes"])) $MyQuery .= CleanEvents($_REQUEST["Classes"], 'ClId') ;
if(isset($_REQUEST["Divisions"])) $MyQuery .= CleanEvents($_REQUEST["Classes"], 'DivId') ;
$MyQuery.= "ORDER BY DivViewOrder, ClViewOrder, TeScore DESC, TeGold DESC, TeXnine DESC, NationCode, TcOrder ";

$Rs=safe_r_sql($MyQuery);
if($Rs)
{
	$CurGroup = "....";
	$CurTeam = "....";
// Variabili per la gestione del ranking
	$MyRank = 1;
	$MyPos = 0;
// Variabili che contengono i punti del precedente atleta per la gestione del rank
	$MyScoreOld = 0;
	$MyGoldOld = 0;
	$MyXNineOld = 0;
	while($MyRow=safe_fetch($Rs))
	{
//se cambia classifica rifaccio l'header
		if ($CurGroup != $MyRow->TeEvent || !$pdf->SamePage(4) || ($CurTeam != $MyRow->NationCode && !$pdf->SamePage(4 * $MyRow->Quanti+16)))
		{
			$TmpSegue = !$pdf->SamePage(4);
			if($CurTeam != $MyRow->NationCode && !$pdf->SamePage(4 * 3 * $MyRow->Quanti+16))
			{
				$TmpSegue=true;
				$pdf->AddPage();
			}
			else if($CurGroup != "....")
				$pdf->SetXY(10,$pdf->GetY()+5);

			if($CurGroup != $MyRow->TeEvent)
				$TmpSegue=false;
			$CurGroup = $MyRow->TeEvent;
		   	$pdf->SetFont($pdf->FontStd,'B',10);
			$TmpTitle;
			if(!is_null($MyRow->DivDescription) && !is_null($MyRow->ClDescription))
			{
				$TmpTitle = get_text($MyRow->DivDescription,'','',true) . " - " . get_text($MyRow->ClDescription,'','',true);
			}
			else
			{
				$TmpTitle = get_text($MyRow->TeEvent,'','',true);
			}
			$pdf->Cell(190, 6,  $TmpTitle, 1, 1, 'C', 1);
			if($TmpSegue)
			{
				$pdf->SetXY(170,$pdf->GetY()-6);
			   	$pdf->SetFont($pdf->FontStd,'',6);
				$pdf->Cell(30, 6,  get_text('Continue'), 0, 1, 'R', 0);
			}
			else
			{
				$MyRank = 1;
				$MyPos = 0;
				$MyScoreOld = 0;
				$MyGoldOld = 0;
				$MyXNineOld = 0;
				$CurTeam = "";
			}
		   	$pdf->SetFont($pdf->FontStd,'B',7);
			$pdf->Cell(20, 4,  get_text('Medal'), 1, 0, 'C', 1);
			$pdf->Cell(85, 4,  get_text('Country'), 1, 0, 'L', 1);
			$pdf->Cell(50, 4,  get_text('Athlete'), 1, 0, 'L', 1);
			$pdf->Cell(35, 4,  get_text('TotaleScore'), 1, 1, 'C', 1);
			$pdf->SetFont($pdf->FontStd,'',1);
			$pdf->Cell(190, 0.5,  '', 1, 1, 'C', 0);
		}
//Al cambio squadra riscrivo il nome altrimento gestisco solo i componenti
		if($CurTeam != $MyRow->NationCode)
		{
			// Sicuramente devo incrementare la posizione
			$MyPos++;
			// Se non ho parimerito il ranking � uguale alla posizione
			if (!($MyRow->TeScore==$MyScoreOld && $MyRow->TeGold==$MyGoldOld && $MyRow->TeXnine==$MyXNineOld))
				$MyRank = $MyPos;
			if($MyRank<=3)
			{
			   	$pdf->SetFont($pdf->FontStd,'B',8);
				$pdf->Cell(20, 4 * ($MyRow->Quanti), ($MyRank==1 ? get_text('MedalGold') : (($MyRank==2 ? get_text('MedalSilver') : (($MyRank==3 ? get_text('MedalBronze') : ""))))), 1, 0, 'L', 0);
		   		$pdf->SetFont($pdf->FontStd,'',7);
				$pdf->Cell(10, 4 * ($MyRow->Quanti),  $MyRow->NationCode, 'LTB', 0, 'C', 0);
				$pdf->Cell(75, 4 * ($MyRow->Quanti),  $MyRow->Nation, 'RTB', 0, 'L', 0);
			}
		}
		else
		{
			$pdf->SetX($pdf->GetX()+105);
		}

		if($MyRank<=3)
		{
			$pdf->SetFont($pdf->FontStd,'',7);
			$pdf->Cell(50, 4,  $MyRow->FirstName . ' ' . $MyRow->Name, 'TRB', 0, 'L', 0);
		   	$pdf->SetFont($pdf->FontFix,'',6);
			$pdf->Cell(8, 4,  number_format(($MyRow->QuScore),0,'',get_text('NumberThousandsSeparator')), 1, 0, 'R', 0);
			if($CurTeam != $MyRow->NationCode)
			{
				$CurTeam = $MyRow->NationCode;
			   	$pdf->SetFont($pdf->FontFix,'B',8);
				$pdf->Cell(11, 4 * ($MyRow->Quanti),  number_format(($MyRow->TeScore),0,'',get_text('NumberThousandsSeparator')), 'TB', 0, 'R', 0);
			   	$pdf->SetFont($pdf->FontFix,'',7);
				$pdf->Cell(8, 4 * ($MyRow->Quanti),  ($MyRow->TeGold), 'TB', 0, 'R', 0);
				$pdf->Cell(8, 4 * ($MyRow->Quanti),  ($MyRow->TeXnine), 'TBR', 0, 'R', 0);
			}
			$pdf->Cell(0.5, 4,  '', 0, 1, 'C', 0);
			$MyScoreOld = $MyRow->TeScore;
			$MyGoldOld = $MyRow->TeGold;
			$MyXNineOld = $MyRow->TeXnine;
		}
	}
	safe_free_result($Rs);
}
if(!isset($isCompleteResultBook))
	$pdf->Output();

?>