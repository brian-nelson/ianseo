<?php

define("CellH",7);

$pdf->SetLineWidth(0.1);

$pdf->setDocUpdate($PdfData->LastUpdate);
$pdf->setPhase($PdfData->Phase);

if($PdfData->Version) {
	$pdf->setComment(trim("Vers. {$PdfData->Version} ({$PdfData->VersionDate}) {$PdfData->VersionNote}"));
}

$pdf->AddPage();
$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
$pdf->Bookmark($PdfData->IndexName, 0);

$pdf->SetXY(OrisPDF::leftMargin, OrisPDF::topStart);
$pdf->SetFont('','B');
$pdf->Cell(45, CellH, "Event Name", 1, 0, 'L', 0);
$pdf->Cell(20, CellH, "Date", 1, 0, 'R', 0);
$pdf->Cell(15, CellH, "Medal", 1, 0, 'L', 0);
$pdf->Cell(50, CellH, "Name", 1, 0, 'L', 0);
$pdf->Cell(60, CellH, "NOC", 1, 1, 'L', 0);

$arrMedals = array(1=>'gold','2'=>'silver','3'=>'bronze');

foreach($PdfData->rankData['events'] as $Event => $section) {
	if(!empty($_REQUEST['Events']) and !in_array($section['evCode'], $_REQUEST['Events'])) {
		continue;
	}
	$Rows=0;
	foreach($arrMedals as $vMed)
	{
		if(!empty($section[$vMed]))
		{
			$Rows += count($section[$vMed]);
			foreach($section[$vMed] as $ath)
			{
				if(!empty($ath))
					$Rows += (count($ath['athletes'])-1);

			}
		}
	}



	if(!$Rows)
		continue;

	$blockHeight=$Rows*CellH;

   	//Eventuale Salto Pagina
	if(!$pdf->SamePage($Rows, CellH)) {
  		$pdf->SetXY(OrisPDF::leftMargin, OrisPDF::topStart);
		$pdf->SetFont('','B');
		$pdf->Cell(45, CellH, "Event Name", 1, 0, 'L', 0);
		$pdf->Cell(20, CellH, "Date", 1, 0, 'R', 0);
		$pdf->Cell(15, CellH,  "Medal", 1, 0, 'L', 0);
		$pdf->Cell(50, CellH,  "Name", 1, 0, 'L', 0);
		$pdf->Cell(60, CellH,  "NOC", 1, 1, 'L', 0);
  	}

	$pdf->SetFont('','');

   	//Nome Evento
   	$X=$pdf->GetX();
   	$Y=$pdf->GetY();
   	$pdf->Cell(45, CellH, $section['evName'], 0, 0, 'L', 0);
   	$pdf->setX($X);
   	$pdf->Cell(45, $blockHeight, '', 1, 0, 'L', 0);

   	//Data
   	$X=$pdf->GetX();
   	$pdf->Cell(20, CellH, date('D j M', strtotime($section['date'])), '', 0, 'R', 0);
   	$pdf->SetX($X);
   	$pdf->Cell(20, $blockHeight, '', '1', 0, 'L', 0);

   	//Ciclo per ogni singola medaglia
   	$X=$pdf->GetX();
	foreach($arrMedals as $kMed=>$vMed)
	{
		if(!empty($section[$vMed]))
		{
		   	foreach($section[$vMed] as $item) {
		   		$pdf->SetX($X);
		   		//Nome della medaglia
		   		$pdf->Cell(15, CellH, mb_convert_case($PdfData->{'Medal_'.$kMed}, MB_CASE_UPPER, "UTF-8"), 0, 0, 'L', 0);
		   		$pdf->SetX($X);
		   		$pdf->Cell(15, CellH * count($item['athletes']), '', 1, 0, 'L', 0);
		   		//Elenco Atleti
		   		//Elenco Atleti
		   		$tmpX=$pdf->getX();
		   		$tmpY=$pdf->getY();
		   		$n=0;
		   		foreach($item['athletes'] as $ath) {
		   			$pdf->setXY($tmpX, $tmpY + CellH*$n);
		   			$pdf->Cell(50, CellH, $ath['athlete'],'RL' . ($n ? ($n==count($item['athletes'])-1 ? 'B' : '') : 'T' . (count($item['athletes'])==1 ? 'B':'')), 0, 'L', 0);
		   			$n++;
		   		}
				$pdf->setXY($tmpX+50, $tmpY);
		   		//Elenco NOC
				$tmpX=$pdf->getX();
				$tmp=$pdf->getCellPaddings();
				$pdf->setCellPaddings($tmp['L'],$tmp['T'],0,$tmp['B']);
				$pdf->Cell(8, CellH,  $item['countryCode'] , '', 0, 'L', 0);
				$pdf->setCellPaddings(1,$tmp['T'],$tmp['R'],$tmp['B']);
				$pdf->Cell(52, CellH,  $item['countryName'], '', 0, 'L', 0);
				$pdf->setCellPaddings($tmp['L'],$tmp['T'],$tmp['R'],$tmp['B']);
// 				$pdf->Cell(60, CellH, $item['countryCode'] . '-' . $item['countryName'], 0, 0, 'L', 0);
				$pdf->SetX($tmpX);
				$pdf->Cell(60, CellH * count($item['athletes']), '', 1, 1, 'L', 0);
		   	}
		}
	}
}

?>
