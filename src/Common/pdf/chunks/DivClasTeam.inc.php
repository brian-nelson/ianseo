<?php

$pdf->HideCols=$PdfData->HideCols;
$pdf->NumberThousandsSeparator=$PdfData->NumberThousandsSeparator;
$pdf->Continue=$PdfData->Continue;
$pdf->TotalShort=$PdfData->TotalShort;

$rankData=$PdfData->rankData;

if(count($rankData['sections']))
{
	$pdf->setDocUpdate($rankData['meta']['lastUpdate']);

	foreach($rankData['sections'] as $section)
	{
		if(!$pdf->SamePage(4*count($section['items'][0]['athletes'])+(!empty($meta['printHeader']) ? 30 : 16)+($section['meta']['sesArrows'] ? 8:0)))
			$pdf->AddPage();

		$meta=$section['meta'];

		writeGroupHeaderPrnTeam($pdf, $meta, false);

		foreach($section['items'] as $item)
		{
			if (!$pdf->SamePage(4*count($item['athletes'])))
			{
				$pdf->AddPage();
				writeGroupHeaderPrnTeam($pdf, $meta,true);
			}
			writeDataRowPrnTeam($pdf, $item);
		}
		$pdf->SetY($pdf->GetY()+5);
	}
}

function writeGroupHeaderPrnTeam($pdf, $section,$follows=false)
{
	if (!empty($section['sesArrows']))
	{
		$pdf->SetFont($pdf->FontStd,'B',10);
		$tmpHeader="";
		foreach($section['sesArrows'] as $k=>$v)
		{
			if($v)
			{
				if(strlen($tmpHeader)!=0)
					$tmpHeader .= " - ";
				$tmpHeader .= $v;
				if(count($section['sesArrows'])!=1 && !empty($section['fields']['session']))
					$tmpHeader .= " (" . $section['fields']['session'] . ": " . $k  . ")";
			}

		}
		if(strlen($tmpHeader)!=0)
			$pdf->Cell(190, 7.5, str_replace("<br/>"," ",$tmpHeader), 0, 1, 'R', 0);
	}
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(190, 6,  $section['descr'], 1, 1, 'C', 1);
	if($follows)
	{
		$pdf->SetXY(170,$pdf->GetY()-6);
	   	$pdf->SetFont($pdf->FontStd,'',6);
		$pdf->Cell(30, 6,  $pdf->Continue, 0, 1, 'R', 0);
	}

	$pdf->SetFont($pdf->FontStd,'B',7);
	$pdf->Cell(9, 4,  $section['fields']['rank'], 1, 0, 'C', 1);
	$pdf->Cell(54, 4, $section['fields']['countryName'], 1, 0, 'L', 1);
	$pdf->Cell(44, 4, $section['fields']['athletes']['name'], 1, 0, 'L', 1);
	$pdf->Cell(12, 4, $section['fields']['athletes']['fields']['div'], 1, 0, 'C', 1);
	$pdf->Cell(11, 4, $section['fields']['athletes']['fields']['ageclass'], 1, 0, 'C', 1);
	$pdf->Cell(11, 4, $section['fields']['athletes']['fields']['class'], 1, 0, 'C', 1);
	$pdf->Cell(8, 4,  $section['fields']['athletes']['fields']['subclass'], 1, 0, 'C', 1);
	$pdf->Cell(21, 4, $section['fields']['score'], 1, 0, 'C', 1);
	$pdf->Cell(10, 4, $section['fields']['gold'], 1, 0, 'C', 1);
	$pdf->Cell(10, 4, $section['fields']['xnine'], 1, 1, 'C', 1);
	$pdf->SetFont($pdf->FontStd,'',1);
	$pdf->Cell(190, 0.5,  '', 1, 1, 'C', 0);
}

function writeDataRowPrnTeam($pdf, $item)
{
	$pdf->SetFont($pdf->FontStd,'B',8);
	$height=4*count($item['athletes']);

	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell(9, $height,  $item['rank'], 1, 0, 'R', 0);

	$pdf->SetFont($pdf->FontStd,'',7);
	$pdf->Cell(8, $height,  $item['countryCode'], 'LTB', 0, 'C', 0);
	$pdf->Cell(46, $height,  $item['countryName'], 'RTB', 0, 'L', 0);

	$X=$pdf->GetX();
	$Y=$pdf->GetY();

	$pdf->SetX(168);
	$pdf->SetFont($pdf->FontFix,'B',8);
	$pdf->Cell(12, $height,  is_numeric($item['score']) ? number_format($item['score'],0,'',$pdf->NumberThousandsSeparator) : $item['score'], 1, 0, 'R', 0);

	$pdf->SetFont($pdf->FontFix,'',8);
	$pdf->Cell(10, $height,  $item['gold'], 1, 0, 'R', 0);
	$pdf->Cell(10, $height,  $item['xnine'], 1, 0, 'R', 0);

	$pdf->SetXY($X,$Y);


	foreach ($item['athletes'] as $a)
	{
		$pdf->SetFont($pdf->FontStd,'',7);
		$pdf->Cell(44, 4,  $a['athlete'], 1, 0, 'L', 0);
		$pdf->Cell(12, 4,  $a['div'], 1, 0, 'C', 0);
		$pdf->Cell(11, 4,  $a['ageclass'], 1, 0, 'C', 0);
		$pdf->Cell(11, 4,  $a['class'], 1, 0, 'C', 0);
		$pdf->Cell(8, 4,  $a['subclass'], 1, 0, 'C', 0);
		$pdf->SetFont($pdf->FontFix,'',7);
		$pdf->Cell(9, 4,  is_numeric($a['quscore']) ? number_format($a['quscore'],0,'',$pdf->NumberThousandsSeparator) : $a['quscore'], 1, 1, 'R', 0);
		$pdf->SetX(73);
	}

	$pdf->SetX(10);
}

?>