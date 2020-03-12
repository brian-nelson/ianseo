<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Fun_FormatText.inc.php');
checkACL(AclQualification, AclReadOnly);

if(!isset($isCompleteResultBook))
	$pdf = new ResultPDF((get_text('MedalIndClass','Tournament')));

$rank=Obj_RankFactory::create('DivClass',array('dist'=>0, 'cutRank'=>3));
$rank->read();
$rankData=$rank->getData();

if(count($rankData['sections']))
{
	$DistSize=12;
	$AddSize=0;
	$pdf->setDocUpdate($rankData['meta']['lastUpdate']);
	foreach($rankData['sections'] as $section)
	{
		//Calcolo Le Misure per i Campi
			if($rankData['meta']['numDist']>=4 && !$rankData['meta']['double'])
				$DistSize = 48/$rankData['meta']['numDist'];
			elseif($rankData['meta']['numDist']>=4 && $rankData['meta']['double'])
				$DistSize = 48/(($rankData['meta']['numDist']/2)+1);
			else
				$AddSize = (48-($rankData['meta']['numDist']*12))/2;

		//Verifico se l'header e qualche riga ci stanno nella stessa pagina altrimenti salto alla prosisma
		if(!$pdf->SamePage(28))
			$pdf->AddPage();
		writeGroupHeader($pdf, $section['meta'], $DistSize, $AddSize, $rankData['meta']['numDist'], $rankData['meta']['double'], false);

		foreach($section['items'] as $item)
		{
			writeDataRow($pdf, $item, $DistSize, $AddSize, $rankData['meta']['numDist'], $rankData['meta']['double']);
			if (!$pdf->SamePage(4* ($rankData['meta']['double'] ? 2 : 1)))
			{
				$pdf->AddPage();
				writeGroupHeader($pdf, $section['meta'], $DistSize, $AddSize, $rankData['meta']['numDist'], $rankData['meta']['double'], true);
			}
		}
		$pdf->SetY($pdf->GetY()+5);
	}
}
if(!isset($isCompleteResultBook))
	$pdf->Output();

function writeDataRow($pdf, $item)
{
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell(20, 4, ($item['rank']==1 ? get_text('MedalGold') : (($item['rank']==2 ? get_text('MedalSilver') : (($item['rank']==3 ? get_text('MedalBronze') : ""))))), 1, 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'',7);
	$pdf->Cell(50, 4, $item['athlete'], 1, 0, 'L', 0);
   	$pdf->SetFont($pdf->FontStd,'',7);
	$pdf->Cell(10, 4, $item['countryCode'], 'LTB', 0, 'C', 0);
	$pdf->Cell(80, 4, $item['countryName'], 'RTB', 0, 'L', 0);
   	$pdf->SetFont($pdf->FontFix,'B',8);
	$pdf->Cell(12, 4, is_numeric($item['score']) ? number_format($item['score'],0,'',get_text('NumberThousandsSeparator')) : '', 'LBT', 0, 'R', 0);
	$pdf->SetFont($pdf->FontFix,'',7);
	$pdf->Cell(9, 4, $item['gold'], 'BT', 0, 'R', 0);
	$pdf->Cell(9, 4, $item['xnine'], 'RBT', 1, 'R', 0);
}

function writeGroupHeader($pdf, $section, $follows=false)
{
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(190, 6,  $section['descr'], 1, 1, 'C', 1);
	if($follows)
	{
		$pdf->SetXY(170,$pdf->GetY()-6);
	   	$pdf->SetFont($pdf->FontStd,'',6);
		$pdf->Cell(30, 6,  (get_text('Continue')), 0, 1, 'R', 0);
	}
   	$pdf->SetFont($pdf->FontStd,'B',7);
	$pdf->Cell(20, 4,  (get_text('Medal')), 1, 0, 'C', 1);
	$pdf->Cell(50 , 4,  (get_text('Athlete')), 1, 0, 'L', 1);
	$pdf->Cell(90, 4,  (get_text('Country')), 1, 0, 'L', 1);
	$pdf->Cell(30, 4,  (get_text('TotaleScore')), 1, 1, 'C', 1);
	$pdf->SetFont($pdf->FontStd,'',1);
	$pdf->Cell(190, 0.5,  '', 1, 1, 'C', 0);
}
?>