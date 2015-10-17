<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/pdf/Report.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Various.inc.php');
require_once 'Tournament/Fun_Tournament.local.inc.php';

if (!isset($_SESSION['TourId']) && isset($_REQUEST['TourId']))
{
	CreateTourSession($_REQUEST['TourId']);
}

$RowTournament = NULL;

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

$copy2 = array(
			get_text('ReportCopy1','Tournament'),
			get_text('ReportCopy2','Tournament')
			);

$pdf = new Report((get_text('FinalReportTitle','Tournament')));

list($StrData,$ToCode)=ExportASC(null,false);
$StrData = str_replace("\r","",$StrData);
$StrData = str_replace("\n","",$StrData);
$pdf -> setValidationCode(number_format(sprintf("%u",crc32($StrData)),0,'',get_text('NumberThousandsSeparator')));



for($i=0;$i<count($copy2);++$i)
{
	$pdf->setCopy2($copy2[$i]);

	//Intestazione
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(175, 7,  get_text('FinalReportTitle','Tournament'), 1, 1, 'C', 1);
	//Codice Gara & Tipo
	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(40, 7,  get_text('TourCode','Tournament') . ": ", 'LT', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(50, 7,  $RowTournament->ToCode, 'T', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(30, 7,  get_text('TourType','Tournament') . ": ", 'T', 0, 'R', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(55, 7,  get_text($RowTournament->TtName, 'Tournament'), 'TR', 1, 'L', 0);
	//Denominazione
	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(40, 7,  get_text('TourName','Tournament') . ": ", 'L', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(135, 7,  $RowTournament->ToName, 'R', 1, 'L', 0);
	//Organizzazione
	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(40, 7,  get_text('TourCommitee','Tournament') . ": ", 'L', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(135, 7,  $RowTournament->ToCommitee . " - " . $RowTournament->ToComDescr , 'R', 1, 'L', 0);
	//Luogo e data di Svolgimento
	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(40, 7,  get_text('TourWhen','Tournament') . ": ", 'L', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(50, 7,  TournamentDate2String($RowTournament->DtFrom,$RowTournament->DtTo), 0, 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(30, 7,  get_text('TourWhere','Tournament') . ": ", 0, 0, 'R', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(55, 7,  $RowTournament->ToWhere, 'R', 1, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',1);
	$pdf->Cell(175, 0.5,  '', 1, 1, 'L', 1);

	//Personale sul Campo
//	$RowTournament = NULL;
	$Involved = array();
	$MySql = "SELECT TiName, TiCode, ItDescription  "
		. "FROM TournamentInvolved LEFT JOIN InvolvedType ON TiType=ItId "
		. "WHERE TiTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "ORDER BY ItOc, ItJury, ItDoS, ItJudge, TiName ";
	$Rs=safe_r_sql($MySql);
	if (safe_num_rows($Rs)>0)
	{
		while ($MyRow=safe_fetch($Rs))
		{
			if(!array_key_exists(get_text($MyRow->ItDescription,'Tournament'), $Involved))
				$Involved[get_text($MyRow->ItDescription,'Tournament')] = '';
			$Involved[get_text($MyRow->ItDescription,'Tournament')] .= (trim($MyRow->TiName) . (strlen($MyRow->TiCode)>0 ? '(' . $MyRow->TiCode . ')' : '') . ', ');
		}
		safe_free_result($Rs);
	}
	if(count($Involved)>0)
	{
		foreach($Involved as $InvType => $InvName)
		{
			$mcStartY = $pdf->GetY();
			$pdf->SetX($pdf->GetX()+40);
			$pdf->SetFont($pdf->FontStd,'B',10);
			//$pdf->Cell(150, 7,  substr($InvName,0,-2) , 'R', 1, 'L', 0);
			$pdf->MultiCell(135, 7,  substr($InvName,0,-2) , 'R', 'L',0,1);
			$mcEndY = $pdf->GetY();
			$pdf->SetY($mcStartY);
			$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell(40, $mcEndY-$mcStartY,  $InvType . ": ", 'L', 1, 'L', 0);

		}
	}
	else
	{
		$pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->Cell(175, 7,  get_text('NoStaffOnField','Tournament'), 'LR', 1, 'L', 0);
	}
	$pdf->SetFont($pdf->FontStd,'B',1);
	$pdf->Cell(175, 0.5,  '', 1, 1, 'L', 1);
	$pdf->SetXY($pdf->GetX(),$pdf->GetY()+5);

	//Parte di Report vera e propria
	/*$MySql = "SELECT FrqId, FrqStatus, FrqQuestion, FrqTip, FrqType, FrqOptions, FraAnswer "
		. "FROM FinalReportQ "
		. "INNER JOIN Tournament ON ToId=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "INNER JOIN Tournament*Type ON TtId=ToType "
		. "LEFT JOIN FinalReportA ON FrqId=FraQuestion AND FraTournament=ToId "
		. "WHERE (FrqStatus & TtCategory) > 0 "
		. "ORDER BY FrqId";*/
	$MySql = "SELECT FrqId, FrqStatus, FrqQuestion, FrqTip, FrqType, FrqOptions, FraAnswer "
		. "FROM FinalReportQ "
		. "INNER JOIN Tournament ON ToId=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "LEFT JOIN FinalReportA ON FrqId=FraQuestion AND FraTournament=ToId "
		. "WHERE (FrqStatus & ToCategory) > 0 "
		. "ORDER BY FrqId";

	$Rs=safe_r_sql($MySql);

	if(safe_num_rows($Rs)>0)
	{
		while($MyRow = safe_fetch($Rs))
		{
			if($MyRow->FrqType==-1)
			{
				$pdf->SetFont($pdf->FontStd,'B',10);
				$pdf->Cell(175, 7,  $MyRow->FrqId . ' - ' . $MyRow->FrqQuestion, 1, 1, 'L', 1);
			}
			else
			{
				switch($MyRow->FrqType)
				{
					case 0:
						$pdf->SetFont($pdf->FontStd,'',8);
						$pdf->Cell(10, 6,  $MyRow->FrqId . ".", 'LTB', 0, 'L', 0);
						$pdf->Cell(50, 6,  $MyRow->FrqQuestion . ": ", 'BT', 0, 'L', 0);
						$pdf->SetFont($pdf->FontStd,'B',8);
						$pdf->Cell(115, 6,  $MyRow->FraAnswer , 'TRB', 1, 'L', 0);
						break;
					case 1:
						$mcStartY = $pdf->GetY();
						$pdf->SetX($pdf->GetX()+60);
						$pdf->SetFont($pdf->FontStd,'B',8);
						$pdf->MultiCell(115, 7,  $MyRow->FraAnswer , 'RTB', 'L', 0, 1);
						$mcEndY = $pdf->GetY();
						if($mcStartY>$mcEndY)
						{
							$tmpMargin = $pdf->getMargins();
							$mcStartY = $tmpMargin['top'];
						}
						$pdf->SetY($mcStartY);
						$pdf->SetFont($pdf->FontStd,'',8);
						$pdf->Cell(10, $mcEndY-$mcStartY,  $MyRow->FrqId . ".", 'LTB' . (strlen($MyRow->FraAnswer)>0 ? '' : 'B'), 0, 'L', 0);
						$pdf->Cell(50, $mcEndY-$mcStartY,  $MyRow->FrqQuestion . ": ", 'TB', 1, 'L', 0);
						break;
					case 2:
						$pdf->SetFont($pdf->FontStd,'',8);
						$pdf->Cell(10, 6,  $MyRow->FrqId . ".", 'LTB', 0, 'L', 0);
						$pdf->Cell(50, 6,  $MyRow->FrqQuestion . ": ", 'BT', 0, 'L', 0);
						$pdf->SetFont($pdf->FontStd,'B',8);
						$pdf->Cell(115, 6,  ($MyRow->FraAnswer=='0' ?  get_text('No') : ($MyRow->FraAnswer=='1' ?  get_text('Yes') : '--')) , 'TRB', 1, 'L', 0);
						break;
					case 3:
						$pdf->SetFont($pdf->FontStd,'',8);
						$pdf->Cell(10, 6,  $MyRow->FrqId . ".", 'LTB', 0, 'L', 0);
						$pdf->Cell(50, 6,  $MyRow->FrqQuestion . ": ", 'BT', 0, 'L', 0);
						$pdf->SetFont($pdf->FontStd,'B',8);
						$pdf->Cell(115, 6,  $MyRow->FraAnswer , 'TRB', 1, 'L', 0);
						break;
					case 4:
						$pdf->SetFont($pdf->FontStd,'',8);
						$pdf->Cell(10, 6,  $MyRow->FrqId . ".", 'LTB', 0, 'L', 0);
						$pdf->Cell(50, 6,  $MyRow->FrqQuestion . ": ", 'BT', 0, 'L', 0);
						$pdf->SetFont($pdf->FontStd,'B',8);
						$pdf->Cell(115, 6,  str_replace("|",", ",$MyRow->FraAnswer) , 'TRB', 1, 'L', 0);
						break;
				}
			}
		}
		safe_free_result($Rs);
	}

	if ($i!=count($copy2)-1)
	{
		$pdf->startPageGroup();
		$pdf->AddPage();
	}

}

if (isset($_REQUEST['TourId']))
{
	EraseTourSession();
}

if(isset($__ExportPDF))
{
	$__ExportPDF = $pdf->Output('','S');
}
elseif(isset($_REQUEST['ToFitarco']))
{
	$Dest='D';
	if (isset($_REQUEST['Dest']))
		$Dest=$_REQUEST['Dest'];

	if ($Dest=='S')
		print $pdf->Output($_REQUEST['ToFitarco'],$Dest);
	else
		$pdf->Output($_REQUEST['ToFitarco'],$Dest);
}
else
	$pdf->Output();


//$pdf->Output();
?>