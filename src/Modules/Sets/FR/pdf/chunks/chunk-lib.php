<?php
/**
 * Created by PhpStorm.
 * User: deligant
 * Date: 01/03/18
 * Time: 13.18
 */

function writeGroupHeaderPrnIndividualAbs(&$pdf, $section, $distSize, $addSize, $running, $distances, $double, $follows=false) {
	$tmpHeader="";
	$pdf->SetFont($pdf->FontStd,'B',$pdf->FontSizeTitle);
	if (!empty($section['sesArrows']))
	{
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
	}
	// testastampa
	if (strlen($section['printHeader']))
		$pdf->Cell(0, 7.5, $section['printHeader'], 0, 1, 'R', 0);
	else if(strlen($tmpHeader)!=0 && !$section['running'])
		$pdf->Cell(0, 7.5, $tmpHeader, 0, 1, 'R', 0);


	$pdf->SetFont($pdf->FontStd,'B',$pdf->FontSizeTitle);
	$pdf->Cell(0, 6,  $section['descr'], 1, 1, 'C', 1);
	if($follows)
	{
		$pdf->SetXY(170,$pdf->GetY()-6);
		$pdf->SetFont($pdf->FontStd,'',6);
		$pdf->Cell(0, 6, $pdf->Continue, 0, 1, 'R', 0);
	}
	$pdf->SetFont($pdf->FontStd,'B',$pdf->FontSizeHead);
	$pdf->Cell(8, 4 * ($double ? 2 : 1),  $section['fields']['rank'], 1, 0, 'C', 1);

	$pdf->Cell(45 + $addSize, 4 * ($double ? 2 : 1),  $section['fields']['athlete'], 1, 0, 'L', 1);
	$pdf->Cell(10, 4 * ($double ? 2 : 1),  $section['fields']['class'], 1, 0, 'C', 1);

	$pdf->Cell(51 + $addSize, 4 * ($double ? 2 : 1),  $section['fields']['countryName'], 1, 0, 'L', 1);
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
	if(!$running)
		$pdf->Cell(12, 4 * ($double ? 2 : 1),  $section['fields']['score'], 1, 0, 'C', 1);
	if($pdf->ShowTens)
		$pdf->Cell(6, 4 * ($double ? 2 : 1),  $section['fields']['gold'], 1, 0, 'C', 1);
	$pdf->Cell(6 * ($pdf->ShowTens?1:2), 4 * ($double ? 2 : 1),  $section['fields']['xnine'], 1, 0, 'C', 1);
	if($running)
	{
		$pdf->Cell(8, 4 * ($double ? 2 : 1),  $section['fields']['hits'], 1, 0, 'C', 1);
		$pdf->Cell(12, 4 * ($double ? 2 : 1),  $section['fields']['score'], 1, 1, 'C', 1);
	}
	else
		$pdf->Cell(8, 4 * ($double ? 2 : 1),  '', 1, 1, 'C', 1);
	$pdf->SetFont($pdf->FontStd,'',1);
	$pdf->Cell(0, 0.5,  '', 1, 1, 'C', 0);
}

function writeDataRowPrnIndividualAbs(&$pdf, $item, $distSize, $addSize, $running, $distances, $double, $snapDistance, $border='TB') {
	$pdf->SetFont($pdf->FontStd,'B',$pdf->FontSizeLines);
	$pdf->Cell(8, 4 * ($double ? 2 : 1),  $item['rank'], $border.'LR', 0, 'R', 0);
	//Atleta
	$pdf->SetFont($pdf->FontStd,'',$pdf->FontSizeHead);
	$pdf->Cell(7, 4 * ($double ? 2 : 1),  ($item['session'] . "- " . $item['target']), $border. 'L', 0, 'R', 0);
	$pdf->SetFont($pdf->FontStd,'',$pdf->FontSizeHead);
	$pdf->Cell(11, 4 * ($double ? 2 : 1),  $item['bib'], $border, 0, 'L', 0);
	$pdf->Cell(27+ $addSize, 4 * ($double ? 2 : 1),  $item['athlete'], $border. 'R', 0, 'L', 0);
	//Classe
	$pdf->SetFont($pdf->FontStd,'',$pdf->FontSizeHeadSmall);
	$pdf->Cell(5, 4 * ($double ? 2 : 1), ($item['class']), $border.'L', 0, 'C', 0);
	$pdf->SetFont($pdf->FontStd,'',5);
	$pdf->Cell(5, 4 * ($double ? 2 : 1), ($item['class']!=$item['ageclass'] ?  ' ' . ( $item['ageclass']) : ''), $border.'R', 0, 'C', 0);
	//Nazione
	$pdf->SetFont($pdf->FontStd,'',$pdf->FontSizeHead);
	$pdf->Cell(11, 4 * ($double ? 2 : 1),  $item['countryCode'], $border.'L', 0, 'L', 0);
	$pdf->Cell(40 + $addSize, 4 * ($double ? 2 : 1),  $item['countryName'], $border.'R', 0, 'L', 0);
	$pdf->SetFont($pdf->FontFix,'',$pdf->FontSizeHead);
	if(!$double)
	{
		for($i=1; $i<=$distances;$i++)
		{
			list($rank,$score)=explode('|',$item['dist_' . $i]);
			if($snapDistance==0)
			{
				$cellContent=str_pad($score,3," ",STR_PAD_LEFT);
				if($rank) $cellContent.="/" . str_pad($rank,2," ",STR_PAD_LEFT);
				$pdf->Cell($distSize, 4,  $cellContent, $border.'LR', 0, 'R', 0);
			}
			elseif($i<$snapDistance)
			{
				$pdf->Cell($distSize/2, 4, str_pad($score,3," ",STR_PAD_LEFT), $border.'L', 0, 'R', 0);
				$pdf->Cell($distSize/2, 4, "", $border.'R', 0, 'R', 0);
			}
			else if($i==$snapDistance)
			{
				list($rankS,$scoreS)=explode('|',$item['dist_Snap']);
				$pdf->Cell($distSize/2, 4, str_pad($scoreS,3," ",STR_PAD_LEFT), $border.'L', 0, 'R', 0);
				$pdf->Cell($distSize/2, 4, ($scoreS != $score ? "(" . str_pad($score,3," ",STR_PAD_LEFT) . ")" : "     "), $border.'R', 0, 'R', 0);
			}
			else
			{
				$pdf->Cell($distSize/2, 4, str_pad("0",3," ",STR_PAD_LEFT), $border.'L', 0, 'R', 0);
				$pdf->Cell($distSize/2, 4, ($score!=0 ? "(" . str_pad($score,3," ",STR_PAD_LEFT) . ")" : "     "), $border.'R', 0, 'R', 0);
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
				$pdf->Cell($distSize, 4,  $cellContent, $border.'LR', 0, 'R', 0);
			}
			elseif($i<$snapDistance)
			{
				$pdf->Cell($distSize/2, 4, str_pad($score,3," ",STR_PAD_LEFT), $border.'L', 0, 'R', 0);
				$pdf->Cell($distSize/2, 4, "", $border.'R', 0, 'R', 0);
			}
			else if($i==$snapDistance)
			{
				list($rankS,$scoreS)=explode('|',$item['dist_Snap']);
				$pdf->Cell($distSize/2, 4, str_pad($scoreS,3," ",STR_PAD_LEFT), $border.'L', 0, 'R', 0);
				$pdf->Cell($distSize/2, 4, ($scoreS != $score ? "(" . str_pad($score,3," ",STR_PAD_LEFT) . ")" : "     "), $border.'R', 0, 'R', 0);
			}
			else
			{
				$pdf->Cell($distSize/2, 4, str_pad("0",3," ",STR_PAD_LEFT), $border.'L', 0, 'R', 0);
				$pdf->Cell($distSize/2, 4, ($score!=0 ? "(" . str_pad($score,3," ",STR_PAD_LEFT) . ")" : "     "), $border.'R', 0, 'R', 0);
			}
			$RunningTotal += $score;
		}
		$pdf->Cell($distSize, 4, number_format($RunningTotal,0,'',$pdf->NumberThousandsSeparator), 1, 0, 'R', 0);
		$pdf->setXY($TmpX,$TmpY+4);
		$RunningTotal=0;
		for($i; $i<=$distances;$i++)
		{
			list($rank,$score)=explode('|',$item['dist_' . $i]);
			if($snapDistance==0)
			{
				$cellContent=str_pad($score,3," ",STR_PAD_LEFT);
				if($rank) $cellContent.="/" . str_pad($rank,2," ",STR_PAD_LEFT);
				$pdf->Cell($distSize, 4,  $cellContent, $border.'LR', 0, 'R', 0);
			}
			elseif($i<$snapDistance)
			{
				$pdf->Cell($distSize/2, 4, str_pad($score,3," ",STR_PAD_LEFT), $border.'L', 0, 'R', 0);
				$pdf->Cell($distSize/2, 4, "", $border.'R', 0, 'R', 0);
			}
			else if($i==$snapDistance)
			{
				list($rankS,$scoreS)=explode('|',$item['dist_Snap']);
				$pdf->Cell($distSize/2, 4, str_pad($scoreS,3," ",STR_PAD_LEFT), $border.'L', 0, 'R', 0);
				$pdf->Cell($distSize/2, 4, ($scoreS != $score ? "(" . str_pad($score,3," ",STR_PAD_LEFT) . ")" : "     "), $border.'R', 0, 'R', 0);
			}
			else
			{
				$pdf->Cell($distSize/2, 4, str_pad("0",3," ",STR_PAD_LEFT), $border.'L', 0, 'R', 0);
				$pdf->Cell($distSize/2, 4, ($score!=0 ? "(" . str_pad($score,3," ",STR_PAD_LEFT) . ")" : "     "), $border.'R', 0, 'R', 0);
			}
			$RunningTotal += $score;
		}
		$pdf->Cell($distSize, 4, number_format($RunningTotal,0,'',$pdf->NumberThousandsSeparator), $border.'LR', 0, 'R', 0);
		$pdf->setXY($pdf->GetX(),$TmpY);
	}
	$pdf->SetFont($pdf->FontFix,'B',$pdf->FontSizeLines);
	if(!$running)
	{
		if($snapDistance==0)
			$pdf->Cell(12, 4 * ($double ? 2 : 1), number_format($item['score'],0,'',$pdf->NumberThousandsSeparator), $border.'LR', 0, 'R', 0);
		else
		{
			$pdf->Cell(6, 4 * ($double ? 2 : 1), number_format($item['scoreSnap'],0,'',$pdf->NumberThousandsSeparator), $border.'L', 0, 'R', 0);
			$pdf->SetFont($pdf->FontFix,'',$pdf->FontSizeHead);
			$pdf->Cell(6, 4 * ($double ? 2 : 1), ($item['score']==$item['scoreSnap'] ? '' : '(' . number_format($item['score'],0,'',$pdf->NumberThousandsSeparator) . ')'), $border.'R', 0, 'R', 0);
		}
	}
	$pdf->SetFont($pdf->FontFix,'',$pdf->FontSizeLines);
	if($pdf->ShowTens) {
		if($snapDistance==0) {
			$pdf->SetFont($pdf->FontFix,'',$pdf->FontSizeLines);
			$pdf->Cell(6, 4 * ($double ? 2 : 1), $item['gold'], $border.'LR', 0, 'R', 0);
		} else {
			$pdf->SetFont($pdf->FontFix,'',$pdf->FontSizeHeadSmall);
			$pdf->Cell(6, 4 * ($double ? 2 : 1), str_pad($item['goldSnap'],2," ", STR_PAD_LEFT) . ($item['gold']==$item['goldSnap'] ? "": "(". str_pad($item['gold'],2," ", STR_PAD_LEFT). ")"), $border.'LR', 0, 'R', 0);
		}
	}
	$pdf->SetFont($pdf->FontFix,'',$pdf->FontSizeLines);
	if($snapDistance==0) {
		$pdf->SetFont($pdf->FontFix,'',$pdf->FontSizeLines);
		$pdf->Cell(6 * ($pdf->ShowTens ? 1:2), 4 * ($double ? 2 : 1), $item['xnine'],$border.'LR', 0, 'R', 0);
	} else {
		$pdf->SetFont($pdf->FontFix,'',$pdf->FontSizeHeadSmall);
		$pdf->Cell(6 * ($pdf->ShowTens ? 1:2), 4 * ($double ? 2 : 1), str_pad($item['xnineSnap'],2," ", STR_PAD_LEFT) . ($item['xnine']==$item['xnineSnap'] ? "": "(". str_pad($item['xnine'],2," ", STR_PAD_LEFT). ")"), $border.'LR', 0, 'R', 0);
	}
	if($running)
	{
		$pdf->Cell(8, 4 * ($double ? 2 : 1),  $item['hits'], $border.'LR', 0, 'R', 0);
		$pdf->SetFont($pdf->FontFix,'B',$pdf->FontSizeLines);
		$pdf->Cell(12, 4 * ($double ? 2 : 1),  number_format($item['score'],3,$pdf->NumberDecimalSeparator,$pdf->NumberThousandsSeparator), $border.'LR', 1, 'R', 0);
	} else {
		//Definizione dello spareggio/Sorteggio
		$pdf->SetFont($pdf->FontStd,'I',5);
		if($pdf->ShowCTSO) {
			if(!empty($item['so']) &&  $item['so']>0) {
				$tmpArr="";
				if(strlen(trim($item['tiebreak']))) {
					$tmpArr=".";
					for($countArr=0; $countArr<strlen(trim($item['tiebreak'])); $countArr++) {
						$tmpArr .= DecodeFromLetter(substr(trim($item['tiebreak']),$countArr,1)) . ",";
					}
					$tmpArr = substr($tmpArr,0,-1);

				}
				$pdf->Cell(8, 4 * ($double ? 2 : 1),  ($pdf->ShotOffShort . $tmpArr), $border.'LR', 1, 'L', 1);
			} elseif(!empty($item['ct']) &&  $item['ct']>1) {
				$pdf->Cell(8, 4 * ($double ? 2 : 1),  $pdf->CoinTossShort, $border.'LR', 1, 'L', 0);
			} else {
				$pdf->Cell(8, 4 * ($double ? 2 : 1),  '', $border.'LR', 1, 'R', 0);
			}
		} else {
			$pdf->Cell(8, 4 * ($double ? 2 : 1),  '', $border.'LR', 1, 'R', 0);
		}
	}
}
