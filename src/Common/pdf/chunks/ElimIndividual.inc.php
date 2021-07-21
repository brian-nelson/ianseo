<?php

$pdf->ShotOffShort=$PdfData->ShotOffShort;
$pdf->CoinTossShort=$PdfData->CoinTossShort;
$pdf->NumberThousandsSeparator=$PdfData->NumberThousandsSeparator;
$pdf->NumberDecimalSeparator=$PdfData->NumberDecimalSeparator;

if(!empty($PdfData->rankData) and count($PdfData->rankData['sections'])) {
	$DistSize = 11;
	$AddSize=0;
	$pdf->setDocUpdate($PdfData->rankData['meta']['lastUpdate']);
	foreach($PdfData->rankData['sections'] as $section)
	{
		//Verifico se l'header e qualche riga ci stanno nella stessa pagina altrimenti salto alla prosisma
		//if(!$pdf->SamePage(15+(strlen($section['meta']['printHeader']) ? 8:0)))
		if(!$pdf->SamePage(15))
			$pdf->AddPage();
		$pdf->writeGroupHeaderElimInd($section['meta'], $DistSize, $AddSize, $section['meta']['running'], false);
		$EndQualified = false;
		foreach($section['items'] as $item)
		{
			$pdf->writeDataRowElimInd($item, $DistSize, $AddSize,$section['meta']['running'],($EndQualified===false && $item['rank']>$section['meta']['qualifiedNo']));
			//if (!$pdf->SamePage(4* ($rankData['meta']['double'] ? 2 : 1)))
			if (!$pdf->SamePage(4)) {
				$pdf->AddPage();
				$pdf->writeGroupHeaderElimInd($section['meta'], $DistSize, $AddSize, $section['meta']['running'], true);
			}
			if($item['rank']>$section['meta']['qualifiedNo'])
				$EndQualified = true;
		}
		$pdf->SetY($pdf->GetY()+5);
	}
	if(!isset($isCompleteResultBook))
		$pdf->DrawShootOffLegend();
}


?>