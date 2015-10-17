<?php

$pdf->NumberThousandsSeparator=$PdfData->NumberThousandsSeparator;
$pdf->NumberDecimalSeparator=$PdfData->NumberDecimalSeparator;
$pdf->Continue=$PdfData->Continue;
$pdf->TotalShort=$PdfData->TotalShort;
$pdf->ShotOffShort=$PdfData->ShotOffShort;
$pdf->CoinTossShort=$PdfData->CoinTossShort;

if(count($rankData['sections']))
{
	$pdf->setDocUpdate($rankData['meta']['lastUpdate']);

	foreach($rankData['sections'] as $section)
	{
		$meta=$section['meta'];

		if(!$pdf->SamePage(4+(!empty($meta['printHeader']) ? 8 : 0)))
			$pdf->AddPage();

		$oldScore = array(0,0,0,0);
		$newGroup = true;
		$ShootOffScores=array();
		foreach($section['items'] as $item)
		{
			if($item['ct']>1)
			{
				if($item['so']) {
					$ShootOffScores[]=$item;
				}
				if(($item['so']!=0 && $oldScore[0]!= $item['score']) || ($item['so']==0 && !($oldScore[0]== $item['score'] && $oldScore[1]== $item['gold'] && $oldScore[2]== $item['xnine'])))
				{
					$oldScore[3]=$item['ct'];
					if($newGroup)
					{
						$pdf->SetY($pdf->GetY()+2);
						$pdf->writeGroupHeaderPrnShooOffTeamAbs($meta,false);
						$newGroup = false;
					}
					else
					{
						$pdf->SetFont($pdf->FontStd,'',1);
						$pdf->Cell(190, 1,  '', 1, 1, 'C', 1);
					}
					if(!$pdf->SamePage(4*$oldScore[3]))
					{
						$pdf->AddPage();
						$pdf->writeGroupHeaderPrnShooOffTeamAbs($meta,true);
					}
				}
				$pdf->writeDataRowPrnShootOffTeamAbs($item, ($oldScore[3]==$item['ct'] ? 'T':($oldScore[3]==1 ? 'B':'')));
				if (!$pdf->SamePage(4*(--$oldScore[3])))
				{
					$pdf->AddPage();
					$pdf->writeGroupHeaderPrnShooOffTeamAbs($meta,true);
				}
				$oldScore = array($item['score'], $item['gold'], $item['xnine'], $oldScore[3]);
			}
		}

		if($ShootOffScores) {
			$CellHeight=6;
			$RestWidth=$pdf->getPageWidth()-107-$CellHeight;
			$pdf->SetFont($pdf->FontStd,'',1);
			$pdf->Cell(0, 1,  '', 0, 1, 'C', 0);
			$pdf->SetFont($pdf->FontStd,'B',9);
			$pdf->Cell(0, 6,  $PdfData->ShootOffArrows, 1, 1, 'C', 1);
			$pdf->SetFont($pdf->FontStd,'B',7);
			$pdf->Cell(12, 4, $PdfData->TargetShort, 1, 0, 'C', 1);
			$pdf->Cell(51, 4, $section['meta']['fields']['countryName'], 1, 0, 'C', 1);
			$pdf->Cell(24, 4, $PdfData->ShootOffArrows, 1, 0, 'C', 1);
			$pdf->Cell($CellHeight, 4, $PdfData->Winner, 1, 0, 'C', 1);
			$pdf->Cell($RestWidth, 4, $PdfData->Judge, 1, 0, 'C', 1);
			$pdf->ln();


			foreach($ShootOffScores as $item) {

				$pdf->SetFont($pdf->FontStd,'',7);
				$pdf->Cell(12, $CellHeight,  '', 1, 0, 'R', 0);
				//Nazione
				$pdf->Cell(8, $CellHeight,  $item['countryCode'], 'TBL', 0, 'L', 0);
				$pdf->Cell(43, $CellHeight,  $item['countryName'], 'TBR', 0, 'L', 0);

				// Arr1, 2 and 3
				$pdf->Cell(8, $CellHeight, '', 1, 0, 'C', 0);
				$pdf->Cell(8, $CellHeight, '', 1, 0, 'C', 0);
				$pdf->Cell(8, $CellHeight, '', 1, 0, 'C', 0);

				// Closest
				$pdf->Cell($CellHeight, $CellHeight, '', 1, 0, 'C', 0);
				// Signature
				$pdf->Cell($RestWidth, $CellHeight, '', 1, 0, 'C', 0);
				$pdf->ln();
			}
		}
	}
}

?>