<?php

//error_reporting(E_ALL);

require_once(dirname(__FILE__).'/chunk-lib.php');

//$pdf->HideCols=$PdfData->HideCols;
$pdf->NumberThousandsSeparator=$PdfData->NumberThousandsSeparator;
$pdf->NumberDecimalSeparator=$PdfData->NumberDecimalSeparator;
$pdf->Continue=$PdfData->Continue;
$pdf->TotalShort=$PdfData->TotalShort;
$pdf->ShotOffShort=$PdfData->ShotOffShort;
$pdf->CoinTossShort=$PdfData->CoinTossShort;

if(count($rankData['sections']))
{
	$DistSize = 11;
	$AddSize=0;
	$pdf->setDocUpdate($rankData['meta']['lastUpdate']);
	foreach($rankData['sections'] as $section)
	{
		//Calcolo Le Misure per i Campi
		if($section['meta']['numDist']>=4 && !$rankData['meta']['double'])
			$DistSize = 44/$section['meta']['numDist'];
		elseif($section['meta']['numDist']>=4 && $rankData['meta']['double'])
			$DistSize = 44/(($section['meta']['numDist']/2)+1);
		else
			$AddSize = (44-($section['meta']['numDist']*11))/2;

		//Verifico se l'header e qualche riga ci stanno nella stessa pagina altrimenti salto alla prosisma
		if(!$pdf->SamePage(15+(strlen($section['meta']['printHeader']) ? 8:0)+($section['meta']['sesArrows'] ? 8:0)))
			$pdf->AddPage();
		writeGroupHeaderPrnIndividualAbs($pdf, $section['meta'], $DistSize, $AddSize, $section['meta']['running'], $section['meta']['numDist'], $rankData['meta']['double'], false);
		$EndQualified = ($section['meta']['qualifiedNo']==0);
		foreach($section['items'] as $item)
		{
			if($EndQualified===false && $item['rank']>$section['meta']['qualifiedNo'])
			{
				$pdf->SetFont($pdf->FontStd,'',1);
				$pdf->Cell(190, 1,  '', 1, 1, 'C', 1);
				if (!$pdf->SamePage(4* ($rankData['meta']['double'] ? 2 : 1)))
				{
					$pdf->AddPage();
					writeGroupHeaderPrnIndividualAbs($pdf, $section['meta'], $DistSize, $AddSize, $section['meta']['running'], $section['meta']['numDist'], $rankData['meta']['double'], true);
				}
				$EndQualified = true;
			}

			if (!$pdf->SamePage(4* ($rankData['meta']['double'] ? 2 : 1)))
			{
				$pdf->AddPage();
				writeGroupHeaderPrnIndividualAbs($pdf, $section['meta'], $DistSize, $AddSize, $section['meta']['running'], $section['meta']['numDist'], $rankData['meta']['double'], true);
			}
			writeDataRowPrnIndividualAbs($pdf, $item, $DistSize, $AddSize, $section['meta']['running'],$section['meta']['numDist'], $rankData['meta']['double'], ($PdfData->family=='Snapshot' ? $section['meta']['snapDistance']: 0));

		}
		$pdf->SetY($pdf->GetY()+5);
	}
	if(!isset($isCompleteResultBook))
		$pdf->DrawShootOffLegend();
}

?>
