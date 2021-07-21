<?php
$pdf->Continue=$PdfData->Continue;
$rankData=$PdfData->rankData;

if(count($rankData['sections'])) {
	$pdf->setDocUpdate($rankData['meta']['lastUpdate']);

//	global $Cols;
	$first=true;

	foreach($rankData['sections'] as $section) {
		$Cols=array(8, 50, 10, 40, 0, 12, 12);

		$CellWidth=($pdf->getPageWidth()-20-array_sum($Cols));
		$Cols[4]=$CellWidth;

		//Verifico se l'header e qualche riga ci stanno nella stessa pagina altrimenti salto alla prosisma
		if(!$first) $pdf->AddPage();
		$first=false;

		writeGroupHeaderPrnIndividual($pdf, $section['meta'], false);


		foreach($section['items'] as $item) {
			if (!$pdf->SamePage(8)) {
				$pdf->AddPage();
				writeGroupHeaderPrnIndividual($pdf, $section['meta'], true);
			}

			writeDataRowPrnIndividual($pdf, $item);
			if(!isset($PdfData->HTML)) continue;
		}
		$pdf->SetY($pdf->GetY()+5);
	}
}

function writeDataRowPrnIndividual($pdf, $item) {
	global $Cols;

	$pdf->SetFont($pdf->FontStd,'B',12);
	$pdf->Cell($Cols[0], 8,  $item['rank'], 'B', 0, 'R', 0);

	$pdf->SetFont($pdf->FontStd,'',12);
	$pdf->Cell($Cols[1], 8,  $item['athlete'], 'B', 0, 'L', 0);

	$pdf->SetFont($pdf->FontStd,'',12);
	$pdf->Cell($Cols[2], 8,  $item['countryCode'], 'B', 0, 'L', 0);
	$pdf->Cell($Cols[3], 8,  $item['countryName'], 'B', 0, 'L', 0);
	$pdf->SetFont($pdf->FontFix,'',10);
    $item["D1Arrowstring"] = str_replace('B','X ', $item["D1Arrowstring"]);
    $item["D1Arrowstring"] = str_replace('A','O ', $item["D1Arrowstring"]);
    $pdf->Cell($Cols[4], 8,  ' ' . $item["D1Arrowstring"], 'B', 0, 'L', 0);
    list($rank,$score)=explode('|',$item['dist_1']);
    $pdf->SetFont($pdf->FontStd,'B',12);
    $pdf->Cell($Cols[5], 8,  str_pad($score,3," ",STR_PAD_LEFT), 'B', 0, 'R', 0);
    list($rank,$score)=explode('|',$item['dist_2']);
    $pdf->SetFont($pdf->FontStd,'',12);
    $pdf->Cell($Cols[6], 8,  str_pad((empty($score) ? '' : $score),3," ",STR_PAD_LEFT), 'B', 0, 'R', 0);


	$pdf->ln();
}

function writeGroupHeaderPrnIndividual($pdf, $section, $follows=false)
{
	global $Cols;
	$pdf->SetFont($pdf->FontStd,'B',14);
	$pdf->Cell(0, 10,  $section['descr'], 0, 0, 'C', );
	if($follows) {
		$pdf->SetX($pdf->getX()-30);
	   	$pdf->SetFont($pdf->FontStd,'',9);
		$pdf->Cell(30, 10,  $pdf->Continue, 0, 0, 'R', 0);
	}
	$pdf->ln();
   	$pdf->SetFont($pdf->FontStd,'B',12);
	$pdf->Cell($Cols[0], 4,  $section['fields']['rank'], 1, 0, 'C', 1);

	$pdf->Cell($Cols[1] , 4 ,  $section['fields']['athlete'], 1, 0, 'L', 1);
	$pdf->Cell($Cols[2]+$Cols[3] , 4 ,  $section['fields']['countryName'], 1, 0, 'L', 1);

	$pdf->Cell($Cols[4]+$Cols[5], 4,  $section['fields']['dist_1'], 1, 0, 'C', 1);
    $pdf->Cell($Cols[6], 4,  $section['fields']['dist_2'], 1, 0, 'C', 1);

	$pdf->ln();
	$pdf->sety($pdf->gety()+1);
}
