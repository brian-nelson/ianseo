<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Various.inc.php');
checkACL(AclCompetition, AclReadOnly);

$RowTournament = NULL;
/*$MySql = "SELECT ToCode, ToName, ToCommitee, ToComDescr, ToWhere, UNIX_TIMESTAMP(ToWhenFrom) AS DtFrom, UNIX_TIMESTAMP(ToWhenTo) AS DtTo, TtName "
	. "FROM Tournament "
	. "INNER JOIN Tournament*Type ON ToType=TtId "
	. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);*/
$MySql = "SELECT"
	. " ToCode,"
	. " ToName,"
	. " ToCommitee,"
	. " ToComDescr,"
	. " ToWhere,"
	. " date_format(ToWhenFrom, '".get_text('DateFmtDB')."') AS DtFrom,"
	. " date_format(ToWhenTo, '".get_text('DateFmtDB')."') AS DtTo,"
	. " ToTypeName AS TtName "
	. "FROM Tournament "
	. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
$Rs=safe_r_sql($MySql);
if(safe_num_rows($Rs)==1)
{
	$RowTournament = safe_fetch($Rs);
	safe_free_result($Rs);
}

$AllQuestions = isset($_REQUEST["All"]);

$pdf = new ResultPDF((get_text('FinalReportTitle','Tournament')));


//Intestazione
$pdf->SetFont($pdf->FontStd,'B',10);
$pdf->Cell(190, 7,  get_text('FinalReportTitle','Tournament'), 1, 1, 'C', 1);
//Codice Gara & Tipo
$pdf->SetFont($pdf->FontStd,'',10);
$pdf->Cell(40, 7,  get_text('TourCode','Tournament') . ": ", 'LT', 0, 'L', 0);
$pdf->SetFont($pdf->FontStd,'B',10);
$pdf->Cell(50, 7,  $RowTournament->ToCode, 'T', 0, 'L', 0);
$pdf->SetFont($pdf->FontStd,'',10);
$pdf->Cell(30, 7,  get_text('TourType','Tournament') . ": ", 'T', 0, 'R', 0);
$pdf->SetFont($pdf->FontStd,'B',10);
$pdf->Cell(70, 7,  get_text($RowTournament->TtName,'Tournament'), 'TR', 1, 'L', 0);
//Denominazione
$pdf->SetFont($pdf->FontStd,'',10);
$pdf->Cell(40, 7,  get_text('TourName','Tournament') . ": ", 'L', 0, 'L', 0);
$pdf->SetFont($pdf->FontStd,'B',10);
$pdf->Cell(150, 7,  $RowTournament->ToName, 'R', 1, 'L', 0);
//Organizzazione
$pdf->SetFont($pdf->FontStd,'',10);
$pdf->Cell(40, 7,  get_text('TourCommitee','Tournament') . ": ", 'L', 0, 'L', 0);
$pdf->SetFont($pdf->FontStd,'B',10);
$pdf->Cell(150, 7,  $RowTournament->ToCommitee . " - " . $RowTournament->ToComDescr , 'R', 1, 'L', 0);
//Luogo e data di Svolgimento
$pdf->SetFont($pdf->FontStd,'',10);
$pdf->Cell(40, 7,  get_text('TourWhen','Tournament') . ": ", 'L', 0, 'L', 0);
$pdf->SetFont($pdf->FontStd,'B',10);
$pdf->Cell(50, 7,  TournamentDate2String($RowTournament->DtFrom,$RowTournament->DtTo), 0, 0, 'L', 0);
$pdf->SetFont($pdf->FontStd,'',10);
$pdf->Cell(30, 7,  get_text('TourWhere','Tournament') . ": ", 0, 0, 'R', 0);
$pdf->SetFont($pdf->FontStd,'B',10);
$pdf->Cell(70, 7,  $RowTournament->ToWhere, 'R', 1, 'L', 0);
$pdf->SetFont($pdf->FontStd,'B',1);
$pdf->Cell(190, 0.5,  '', 1, 1, 'L', 1);

$pdf->SetXY($pdf->GetX(),$pdf->GetY()+5);

//Parte di Report vera e propria
/*$MySql = "SELECT FrqId, FrqStatus, FrqQuestion, FrqTip, FrqType, FrqOptions, TtCategory "
	. "FROM FinalReportQ "
	. "INNER JOIN Tournament ON ToId=" . StrSafe_DB($_SESSION['TourId']) . " "
	. "INNER JOIN Tournament*Type ON TtId=ToType ";
if($AllQuestions)
	$MySql .= "WHERE FrqStatus > 0 ";
else
	$MySql .= "WHERE (FrqStatus & TtCategory) > 0 ";
$MySql .= "ORDER BY FrqId";*/

$MySql = "SELECT FrqId, FrqStatus, FrqQuestion, FrqTip, FrqType, FrqOptions, ToCategory AS TtCategory "
	. "FROM FinalReportQ "
	. "INNER JOIN Tournament ON ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
if($AllQuestions)
	$MySql .= "WHERE FrqStatus > 0 ";
else
	$MySql .= "WHERE (FrqStatus & ToCategory) > 0 ";
$MySql .= "ORDER BY FrqId";

$Rs=safe_r_sql($MySql);

if(safe_num_rows($Rs)>0)
{
	while($MyRow = safe_fetch($Rs))
	{
		if($MyRow->FrqType==-1)
		{
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell(190, 7,  $MyRow->FrqId . ' - ' . $MyRow->FrqQuestion, 1, 1, 'L', 1);
		}
		else
		{
			$hLine = ($pdf->GetNumChars(str_replace("|"," / ",$MyRow->FrqOptions))>85 ? 2:1);
			$pdf->SetFont($pdf->FontStd,'',8);
			$pdf->Cell(10, 5*$hLine + ($MyRow->FrqTip || $AllQuestions ? 4 : 0),  $MyRow->FrqId . ".", 'LTB', 0, 'L', 0);
			$pdf->Cell(60, 5*$hLine,  $MyRow->FrqQuestion, 'T'. ($MyRow->FrqTip || $AllQuestions ? '' : 'B'), 0, 'L', 0);
			switch($MyRow->FrqType)
			{
				case 0:
					$pdf->Cell(120, 5*$hLine,  get_text('jrTextBox', 'Common', ($MyRow->FrqOptions>0 ? $MyRow->FrqOptions : 255)) , 'RT' . ($MyRow->FrqTip || $AllQuestions ? '' : 'B'), 1, 'L', 0);
					break;
				case 1:
					$pdf->Cell(120, 5*$hLine,  get_text('jrTextArea', 'Common', explode('|',$MyRow->FrqOptions)) , 'RT' . ($MyRow->FrqTip || $AllQuestions ? '' : 'B'), 1, 'L', 0);
					break;
				case 2:
					$pdf->Cell(120, 5*$hLine,  get_text('jrYesNo', 'Common') , 'RT' . ($MyRow->FrqTip || $AllQuestions ? '' : 'B'), 1, 'L', 0);
					break;
				case 3:
					if($hLine!=1)
					{
						$pdf->MultiCell(120, 5,  get_text('jrSingleChoice', 'Common', str_replace("|"," / ",$MyRow->FrqOptions)) , 0, 'L', 0, 0, '', '', true, 1, false, true, 5*$hLine);
						$pdf->SetX($pdf->GetX()-120);
						$pdf->Cell(120, 5*$hLine, '', 'RT' . ($MyRow->FrqTip || $AllQuestions ? '' : 'B'), 1, 'L', 0);
					}
					else
						$pdf->Cell(120, 5*$hLine,  get_text('jrSingleChoice', 'Common', str_replace("|"," / ",$MyRow->FrqOptions)) , 'RT' . ($MyRow->FrqTip || $AllQuestions ? '' : 'B'), 1, 'L', 0);
					break;
				case 4:
					if($hLine!=1)
					{
						$pdf->MultiCell(120, 5,  get_text('jrMultiChoice', 'Common', str_replace("|"," / ",$MyRow->FrqOptions)) , 0, 'L', 0, 0, '', '', true, 1, false, true, 5*$hLine);
						$pdf->SetX($pdf->GetX()-120);
						$pdf->Cell(120, 5*$hLine, '', 'RT' . ($MyRow->FrqTip || $AllQuestions ? '' : 'B'), 1, 'L', 0);
					}
					else
						$pdf->Cell(120, 5*$hLine,  get_text('jrMultiChoice', 'Common', str_replace("|"," / ",$MyRow->FrqOptions)) , 'RT' . ($MyRow->FrqTip || $AllQuestions ? '' : 'B'), 1, 'L', 0);
					break;
			}

			if($MyRow->FrqTip || $AllQuestions)
			{
				$pdf->SetFont($pdf->FontStd,'',6);
				$pdf->SetX($pdf->GetX()+10);
				$pdf->Cell(($AllQuestions ? 150 : 180), 4,  $MyRow->FrqTip . ".", 'RB', ($AllQuestions ? 0 : 1), 'L', 0);
				if($AllQuestions)
				{
					$tmpText = '';
					for ($i=1; $i<=8; $i*=2)
					{
						if($MyRow->FrqStatus & $i)
							$tmpText .=  get_text('TourType_' . $i,'Tournament') . '-' ;
					}
					$pdf->Cell(30, 4, substr($tmpText,0,-1), 1, 1, 'C', 1);
				}
			}
		}
	}
	safe_free_result($Rs);
}

$pdf->Output();
?>