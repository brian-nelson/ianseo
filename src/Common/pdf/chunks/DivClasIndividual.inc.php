<?php
//error_reporting(E_ALL);

$pdf->HideCols=$PdfData->HideCols;
$pdf->NumberThousandsSeparator=$PdfData->NumberThousandsSeparator;
$pdf->Continue=$PdfData->Continue;
$pdf->TotalShort=$PdfData->TotalShort;

$rankData=$PdfData->rankData;
if(count($rankData['sections']))
{
	$hideGolds = $PdfData->hideGolds;
	$DistSize = 12;
	$AddSize=0;
	$pdf->setDocUpdate($rankData['meta']['lastUpdate']);
	foreach($rankData['sections'] as $section)
	{
		if(!empty($_REQUEST['OnlySubClass']) && empty($section['meta']['subClass'])) continue;

		//Calcolo Le Misure per i Campi
		if($section['meta']['numDist']>=4) {
			$AddSize=0;
			if(!$rankData['meta']['double']) {
				$DistSize = (48 + ($hideGolds ? 10 : 0))/$section['meta']['numDist'];
			} else {
				$DistSize = (48 + ($hideGolds ? 10 : 0))/(($section['meta']['numDist']/2)+1);
			}
		} else {
			$AddSize = ((48 + ($hideGolds ? 10 : 0))-($section['meta']['numDist']*(48 + ($hideGolds ? 10 : 0))/4))/2;
		}
		//Verifico se l'header e qualche riga ci stanno nella stessa pagina altrimenti salto alla prosisma
		if(!$pdf->SamePage(($rankData['meta']['double'] ? 2 : 1)*9 + 6.5 + ($section['meta']['sesArrows'] ? 7.5 : 0)))
			$pdf->AddPage();

		writeGroupHeaderPrnIndividual($pdf, $section['meta'], $DistSize, $AddSize, $section['meta']['numDist'], $rankData['meta']['double'], false, $hideGolds);

		foreach($section['items'] as $item)
		{
			// goes here as to print the header on a new page it MUST be at least one line of results to print!
			if (!$pdf->SamePage(5* ($rankData['meta']['double'] ? 2 : 1)))
			{
				$pdf->AddPage();
// 				if($k < count($section['items'])) writeGroupHeaderPrnIndividual($pdf, $section['meta'], $DistSize, $AddSize, $section['meta']['numDist'], $rankData['meta']['double'], true, $hideGolds);
				writeGroupHeaderPrnIndividual($pdf, $section['meta'], $DistSize, $AddSize, $section['meta']['numDist'], $rankData['meta']['double'], true, $hideGolds);
			}
			writeDataRowPrnIndividual($pdf, $item, $DistSize, $AddSize, $section['meta']['numDist'], $rankData['meta']['double'], ($PdfData->family=='Snapshot' ? $section['meta']['snapDistance']: 0), $hideGolds);

			if(!isset($PdfData->HTML)) continue;


		}
		$pdf->SetY($pdf->GetY()+5);
	}
}

function writeDataRowPrnIndividual($pdf, $item, $distSize, $addSize, $distances, $double, $snapDistance, $HideGolds=false)
{
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell(8, 4 * ($double ? 2 : 1),  $item['rank'], 1, 0, 'R', 0);
	$pdf->SetFont($pdf->FontStd,'',7);
	$pdf->Cell(7, 4 * ($double ? 2 : 1),  ($item['session'] . "- " . $item['target']), 'TLB', 0, 'R', 0);
	$pdf->SetFont($pdf->FontStd,'',7);
	//Per gare tipo "internazionali" mette qui le colonne "nascoste"
	$pdf->Cell(37+ $addSize + ($pdf->HideCols ? 8:0) , 4 * ($double ? 2 : 1),  $item['athlete'], 'TRB', 0, 'L', 0);
   	if(!$pdf->HideCols)
   	{
		$pdf->SetFont($pdf->FontStd,'',6);
		$pdf->Cell(4, 4 * ($double ? 2 : 1), ($item['class']!=$item['ageclass'] ?  $item['ageclass'] : ''), 'TLB', 0, 'C', 0);
		$pdf->Cell(4, 4 * ($double ? 2 : 1),  ($item['subclass']), 'TBR', 0, 'C', 0);
   	}
	$pdf->SetFont($pdf->FontStd,'',7);
	$pdf->Cell(8, 4 * ($double ? 2 : 1),  $item['countryCode'], 'LTB', 0, 'L', 0);
	$pdf->Cell(42 + $addSize, 4 * ($double ? 2 : 1),  $item['countryName'], 'RTB', 0, 'L', 0);
	$pdf->SetFont($pdf->FontFix,'',7);
	if(!$double)
	{
		for($i=1; $i<=$distances;$i++)
		{
			list($rank,$score)=explode('|',$item['dist_' . $i]);
			if($snapDistance==0)
			{
				$cellContent=str_pad($score,3," ",STR_PAD_LEFT);
				if($rank) $cellContent.="/" . str_pad($rank,2," ",STR_PAD_LEFT);
				$pdf->Cell($distSize, 4,  $cellContent, 1, 0, 'R', 0);
			}
			elseif($i<$snapDistance)
			{
				$pdf->Cell($distSize/2, 4, str_pad($score,3," ",STR_PAD_LEFT), 'TBL', 0, 'R', 0);
				$pdf->Cell($distSize/2, 4, "", 'TBR', 0, 'R', 0);
			}
			else if($i==$snapDistance)
			{
				list($rankS,$scoreS)=explode('|',$item['dist_Snap']);
				$pdf->Cell($distSize/2, 4, str_pad($scoreS,3," ",STR_PAD_LEFT), 'TBL', 0, 'R', 0);
				$pdf->Cell($distSize/2, 4, ($scoreS != $score ? "(" . str_pad($score,3," ",STR_PAD_LEFT) . ")" : "     "), 'TBR', 0, 'R', 0);
			}
			else
			{
				$pdf->Cell($distSize/2, 4, str_pad("0",3," ",STR_PAD_LEFT), 'TBL', 0, 'R', 0);
				$pdf->Cell($distSize/2, 4, ($score!=0 ? "(" . str_pad($score,3," ",STR_PAD_LEFT) . ")" : "     "), 'TBR', 0, 'R', 0);
			}
		}
	}
	else
	{
		$TmpX=$pdf->GetX();
		$TmpY=$pdf->GetY();
		$RunningTotal=0;
		for($i=1; $i<=$distances/2;$i++)
		{
			list($rank,$score)=explode('|',$item['dist_' . $i]);
			if($snapDistance==0)
			{
				$cellContent=str_pad($score,3," ",STR_PAD_LEFT);
				if($rank) $cellContent.="/" . str_pad($rank,2," ",STR_PAD_LEFT);
				$pdf->Cell($distSize, 4,  $cellContent, 1, 0, 'R', 0);
			}
			elseif($i<$snapDistance)
			{
				$pdf->Cell($distSize/2, 4, str_pad($score,3," ",STR_PAD_LEFT), 'TBL', 0, 'R', 0);
				$pdf->Cell($distSize/2, 4, "", 'TBR', 0, 'R', 0);
			}
			else if($i==$snapDistance)
			{
				list($rankS,$scoreS)=explode('|',$item['dist_Snap']);
				$pdf->Cell($distSize/2, 4, str_pad($scoreS,3," ",STR_PAD_LEFT), 'TBL', 0, 'R', 0);
				$pdf->Cell($distSize/2, 4, ($scoreS != $score ? "(" . str_pad($score,3," ",STR_PAD_LEFT) . ")" : "     "), 'TBR', 0, 'R', 0);
			}
			else
			{
				$pdf->Cell($distSize/2, 4, str_pad("0",3," ",STR_PAD_LEFT), 'TBL', 0, 'R', 0);
				$pdf->Cell($distSize/2, 4, ($score!=0 ? "(" . str_pad($score,3," ",STR_PAD_LEFT) . ")" : "     "), 'TBR', 0, 'R', 0);
			}
			if(is_numeric($score)) $RunningTotal += $score;
		}
		$pdf->Cell($distSize, 4, is_numeric($RunningTotal) ? number_format($RunningTotal,0,'',$pdf->NumberThousandsSeparator) : '', 1, 0, 'R', 0);
		$pdf->setXY($TmpX,$TmpY+4);
		$RunningTotal=0;
		for($i; $i<=$distances;$i++)
		{
			list($rank,$score)=explode('|',$item['dist_' . $i]);
			if($snapDistance==0)
			{
				$cellContent=str_pad($score,3," ",STR_PAD_LEFT);
				if($rank) $cellContent.="/" . str_pad($rank,2," ",STR_PAD_LEFT);
				$pdf->Cell($distSize, 4,  $cellContent, 1, 0, 'R', 0);
			}
			elseif($i<$snapDistance)
			{
				$pdf->Cell($distSize/2, 4, str_pad($score,3," ",STR_PAD_LEFT), 'TBL', 0, 'R', 0);
				$pdf->Cell($distSize/2, 4, "", 'TBR', 0, 'R', 0);
			}
			else if($i==$snapDistance)
			{
				list($rankS,$scoreS)=explode('|',$item['dist_Snap']);
				$pdf->Cell($distSize/2, 4, str_pad($scoreS,3," ",STR_PAD_LEFT), 'TBL', 0, 'R', 0);
				$pdf->Cell($distSize/2, 4, ($scoreS != $score ? "(" . str_pad($score,3," ",STR_PAD_LEFT) . ")" : "     "), 'TBR', 0, 'R', 0);
			}
			else
			{
				$pdf->Cell($distSize/2, 4, str_pad("0",3," ",STR_PAD_LEFT), 'TBL', 0, 'R', 0);
				$pdf->Cell($distSize/2, 4, ($score!=0 ? "(" . str_pad($score,3," ",STR_PAD_LEFT) . ")" : "     "), 'TBR', 0, 'R', 0);
			}
			if(is_numeric($score)) $RunningTotal += $score;
		}
		$pdf->Cell($distSize, 4, is_numeric($RunningTotal) ? number_format($RunningTotal,0,'',$pdf->NumberThousandsSeparator) : '', 1, 0, 'R', 0);
		$pdf->setXY($pdf->GetX(),$TmpY);
	}
  	$pdf->SetFont($pdf->FontFix,'B',8);
  	if($snapDistance==0)
		$pdf->Cell(12, 4 * ($double ? 2 : 1), is_numeric($item['score']) ? number_format($item['score'],0,'',$pdf->NumberThousandsSeparator) : '', 1, 0, 'R', 0);
	else
	{
		$pdf->Cell(6, 4 * ($double ? 2 : 1), number_format($item['scoreSnap'],0,'',$pdf->NumberThousandsSeparator), 'TBL', 0, 'R', 0);
		$pdf->SetFont($pdf->FontFix,'',7);
		$pdf->Cell(6, 4 * ($double ? 2 : 1), ($item['score']==$item['scoreSnap'] ? '' : '(' . number_format($item['score'],0,'',$pdf->NumberThousandsSeparator) . ')'), 'TBR', 0, 'R', 0);
	}
	$pdf->SetFont($pdf->FontFix,'',8);
  	if(!$HideGolds)
  	{
		if($snapDistance==0)
			$pdf->Cell(10, 4 * ($double ? 2 : 1), $item['gold'], 1, 0, 'R', 0);
		else
		{
			$pdf->Cell(5, 4 * ($double ? 2 : 1), number_format($item['goldSnap'],0,'',$pdf->NumberThousandsSeparator), 'TBL', 0, 'R', 0);
			$pdf->SetFont($pdf->FontFix,'',7);
			$pdf->Cell(5, 4 * ($double ? 2 : 1), ($item['gold']==$item['goldSnap'] ? '' : '(' . number_format($item['gold'],0,'',$pdf->NumberThousandsSeparator) . ')'), 'TBR', 0, 'R', 0);
		}
  	}
	$pdf->SetFont($pdf->FontFix,'',8);
	if($snapDistance==0)
		$pdf->Cell(10, 4 * ($double ? 2 : 1), $item['xnine'], 1, 1, 'R', 0);
	else
	{
		$pdf->Cell(5, 4 * ($double ? 2 : 1), number_format($item['xnineSnap'],0,'',$pdf->NumberThousandsSeparator), 'TBL', 0, 'R', 0);
		$pdf->SetFont($pdf->FontFix,'',7);
		$pdf->Cell(5, 4 * ($double ? 2 : 1), ($item['xnine']==$item['xnineSnap'] ? '' : '(' . number_format($item['xnine'],0,'',$pdf->NumberThousandsSeparator) . ')'), 'TBR', 1, 'R', 0);
	}

}

function writeGroupHeaderPrnIndividual($pdf, $section, $distSize, $addSize, $distances, $double, $follows=false, $HideGolds=false)
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
				if(count($section['sesArrows'])!=1)
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
	$pdf->Cell(8, 4 * ($double ? 2 : 1),  $section['fields']['rank'], 1, 0, 'C', 1);

	$pdf->Cell(44 + $addSize + ($pdf->HideCols ? 8:0), 4 * ($double ? 2 : 1),  $section['fields']['athlete'], 1, 0, 'L', 1);
	if(!$pdf->HideCols)
		$pdf->Cell(8, 4 * ($double ? 2 : 1),  $section['fields']['subclass'], 1, 0, 'C', 1);
	$pdf->Cell(50 + $addSize, 4 * ($double ? 2 : 1),  $section['fields']['countryName'], 1, 0, 'L', 1);
	if(!$double)
	{
		for($i=1; $i<=$distances;$i++)
		$pdf->Cell($distSize, 4,  $section['fields']['dist_'. $i], 1, 0, 'C', 1);
	}
	else
	{
		$TmpX=$pdf->GetX();
		$TmpY=$pdf->GetY();
		for($i=1; $i<=$distances/2;$i++)
			$pdf->Cell($distSize, 4, $section['fields']['dist_'. $i], 1, 0, 'C', 1);
		$pdf->Cell($distSize, 4, $pdf->TotalShort, 1, 0, 'C', 1);
		$pdf->setXY($TmpX,$TmpY+4);
		for($i; $i<=$distances;$i++)
			$pdf->Cell($distSize, 4, $section['fields']['dist_'. $i], 1, 0, 'C', 1);
		$pdf->Cell($distSize, 4, $pdf->TotalShort, 1, 0, 'C', 1);
		$pdf->setXY($pdf->GetX(),$TmpY);
	}
	$pdf->Cell(12, 4 * ($double ? 2 : 1),  $section['fields']['score'], 1, 0, 'C', 1);

	if(!$HideGolds)
		$pdf->Cell(10, 4 * ($double ? 2 : 1),  $section['fields']['gold'], 1, 0, 'C', 1);
	$pdf->Cell(10, 4 * ($double ? 2 : 1),  $section['fields']['xnine'], 1, 1, 'C', 1);
	$pdf->SetFont($pdf->FontStd,'',1);
	$pdf->Cell(190, 0.5,  '', 1, 1, 'C', 0);
}

?>
