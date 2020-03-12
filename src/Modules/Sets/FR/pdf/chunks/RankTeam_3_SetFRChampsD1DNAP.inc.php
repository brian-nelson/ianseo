<?php

$pdf->setDocUpdate($PdfData->rankData['meta']['lastUpdate']);

// se ho degli eventi
//$Bonus = getModuleParameter('FFTA', 'D1Bonus');
$FirstPage=true;
$Height=6;
$Width=$pdf->getPageWidth() - 87 - 24*count($PdfData->rankData['competitions']);

foreach($PdfData->rankData['sections'] as $section) {
	$NeedTitle=true;

	// Se Esistono righe caricate....
	if(count($section['items'])) {
		if(!$FirstPage) {
			$pdf->AddPage();
		}
		$FirstPage=false;

		foreach($section['items'] as $item) {
			if(!$pdf->SamePage(4 )) {
				$NeedTitle=true;
			}

			//Valuto Se è necessario il titolo
			if($NeedTitle) {
				// testastampa
				if ($section['meta']['printHeader']) {
			   		$pdf->SetFont($pdf->FontStd,'B',10);
					$pdf->Cell(0, 7.5,  $section['meta']['printHeader'], 0, 1, 'R', 0);
				}
				// Titolo della tabella
			   	$pdf->SetFont($pdf->FontStd,'B',10);
				$pdf->Cell(0, 7.5,  $section['meta']['descr'], 1, 1, 'C', 1);

				// Header vero e proprio
			   	$pdf->SetFont($pdf->FontStd,'B',7);
				$pdf->Cell(10, 10, $section['meta']['fields']['rank'], 1, 0, 'C', 1);
				$pdf->Cell(12+$Width, 10, $section['meta']['fields']['countryName'], 1, 0, 'C', 1);

				// Points
				$pdf->Cell(7, 10,'Pts',1,0,'C',1);
				// Differentiel
				$pdf->Cell(7, 10, 'Diff', 1, 0, 'C', 1);
				// somme
				$pdf->Cell(31, 5, 'Totaux', 1, 0, 'C', 1);

				foreach($PdfData->rankData['competitions'] as $comp) {
					$pdf->Cell(24, 5, $comp, 1, 0, 'C', 1);
				}

				$pdf->SetXY(46+$Width, $pdf->GetY()+5);
				$pdf->Cell(7, 5, 'Pts', 1, 0, 'C', 1);
				$pdf->Cell(7, 5, 'Pg', 1, 0, 'C', 1);
				$pdf->Cell(7, 5, 'Pp', 1, 0, 'C', 1);
				$pdf->Cell(10, 5, 'Qual', 1, 0, 'C', 1);
				foreach($PdfData->rankData['competitions'] as $comp) {
					$pdf->Cell(10, 5, 'Qual', 1, 0, 'C', 1);
					$pdf->Cell(7, 5, 'Rank', 1, 0, 'C', 1);
					$pdf->Cell(7, 5, 'Bonus', 1, 0, 'C', 1);
				}
				$NeedTitle=false;
				$pdf->ln();
			}

		   	$pdf->SetFont($pdf->FontStd,'B',8);
			$pdf->Cell(10, $Height, ($item['rank'] ? $item['rank'] : ''), 1, 0, 'C', 0);
		   	$pdf->SetFont($pdf->FontStd,'',8);
			$pdf->Cell(12, $Height,   $item['countryCode'], 'LTB', 0, 'C', 0);
			$pdf->Cell($Width, $Height, $item['countryName'] . ($item['subteam']<=1 ? '' : ' (' . $item['subteam'] .')'), 'TB', 0, 'L', 0);

		    $pdf->SetFont($pdf->FontStd,'b',8);
			$pdf->Cell(7, $Height, $item['mainPoints']+$item['bonusPoints'], 1, 0, 'R', 0);
			$pdf->SetFont($pdf->FontStd,'',8);
			$pdf->Cell(7, $Height, $item['diff'], 1, 0, 'R', 0);

	        $pdf->SetFont($pdf->FontStd,'b',7);
			$pdf->Cell(7, $Height, $item['mainPoints'], 1, 0, 'R', 0);
		    $pdf->SetFont($pdf->FontStd,'',7);
			$pdf->Cell(7, $Height, $item['winPoints'], 1, 0, 'R', 0);
			$pdf->Cell(7, $Height, $item['loosePoints'], 1, 0, 'R', 0);
			$pdf->Cell(10, $Height, $item['qualScore'], 1, 0, 'R', 0);

			foreach($PdfData->rankData['competitions'] as $ToId => $comp) {
		   	    $pdf->SetFont($pdf->FontStd,'',7);
				if(isset($item['finals'][$ToId])) {
					$qual=$item['finals'][$ToId]['qual'];
					$rank=$item['finals'][$ToId]['rank'];
					$bon =$item['finals'][$ToId]['bon'];
				} else {
					$qual='';
					$rank='';
					$bon='';
				}
				$pdf->Cell(10, $Height, $qual, 1, 0, 'R', 0);
				$pdf->Cell(7, $Height, $rank, 1, 0, 'R', 0);
			    $pdf->SetFont($pdf->FontStd,'b',8);
				$pdf->Cell(7, $Height, $bon ? $bon : '', 1, 0, 'R', 0);
			}

			$pdf->ln();
		}
	}
}


// Summary of the matches
$Height=5.5;
$ColWidth=(max($pdf->getPageWidth(), $pdf->getPageHeight())-20-12)/5;
$Blocks=array(0, 10, 13+$ColWidth, 16+$ColWidth*2, 19+$ColWidth*3, 22+$ColWidth*4);
$ClubCol=$ColWidth-5-6-6;

foreach($PdfData->rankData['details'] as $Event => $section) {
	$RunNumber=1;
	foreach($section as $CompId => $Lines) {
		if(empty($PdfData->rankData['sections'][$Event])) {
			continue;
		}
		$pdf->AddPage('L');

		// testastampa
		if (!empty($PdfData->rankData['sections'][$Event]['meta']['printHeader'])) {
	        $pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell(0, 7.5,  $PdfData->rankData['sections'][$Event]['meta']['printHeader'], 0, 1, 'R', 0);
		}

		// Titolo della tabella
	    $pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->Cell(0, 7.5, $PdfData->rankData['sections'][$Event]['meta']['descr'], 1, 1, 'C', 1);
		$pdf->cell(0, 7.5, $PdfData->rankData['meta']['Run'.$RunNumber++].' - '.$PdfData->rankData['competitions'][$CompId], 1, 1, 'C', 1);
		$pdf->ln(2);

		// remembers where the Y is!
		$OrgY=$pdf->GetY();

		foreach($Lines as $Line => $Matches) {
	        $pdf->SetFont($pdf->FontStd,'B',8);
			$pdf->SetLeftMargin($Blocks[$Line]);
			$pdf->SetXY($Blocks[$Line], $OrgY);
			$pdf->Cell($ColWidth, 5, $PdfData->rankData['meta']['Game'.(($RunNumber-2)*5+$Line)], 1, 1, 'C', 1);

			$pdf->ln(2);
	        $pdf->SetFont($pdf->FontStd,'',7);
			$pdf->Cell(5, 5, $PdfData->rankData['meta']['target'], 1, 0, 'C', 1);
			$pdf->Cell($ClubCol, 5, $PdfData->rankData['sections'][$Event]['meta']['fields']['countryName'], 1, 0, 'L', 1);
			$pdf->Cell(6, 5, $PdfData->rankData['meta']['score'], 1, 0, 'C', 1);
	        $pdf->SetFont($pdf->FontStd,'b',7);
			$pdf->Cell(6, 5, $PdfData->rankData['meta']['points'], 1, 1, 'C', 1);
			$pdf->ln(2);

			foreach($Matches as $Match) {
				//debug_svela($Match);
	            $pdf->SetFont($pdf->FontStd,'',8);
				$pdf->Cell(5, $Height, $Match['tgt1'],1,0,'C');
				$pdf->Cell($ClubCol, $Height, $Match['details']['E']['Name1'],1,0,'L');
				$pdf->Cell(6, $Height, $Match['score1'],1,0,'C');
	            $pdf->SetFont($pdf->FontStd,'b',8);
	            if($Match['score1']+$Match['score2']) {
					$pdf->Cell(6, $Height, ($Match['score1']>$Match['score2'] or ($Match['score1']==$Match['score2'] and $Match['winner1'])) ? 2 : 0,1,1,'C');
	            } else {
					$pdf->Cell(6, $Height, '',1,1,'C');
	            }

	            $pdf->SetFont($pdf->FontStd,'',8);
				$pdf->Cell(5, $Height, $Match['tgt2'],1,0,'C');
				$pdf->Cell($ClubCol, $Height, $Match['details']['E']['Name2'],1,0,'L');
				$pdf->Cell(6, $Height, $Match['score2'],1,0,'C');
	            $pdf->SetFont($pdf->FontStd,'b',8);
	            if($Match['score1']+$Match['score2']) {
					$pdf->Cell(6, $Height, ($Match['score2']>$Match['score1'] or ($Match['score1']==$Match['score2'] and $Match['winner2'])) ? 2 : 0,1,1,'C');
	            } else {
					$pdf->Cell(6, $Height, '',1,1,'C');
	            }
				$pdf->ln(3);
			}

		}
		// Header vero e proprio
		$pdf->SetLeftMargin(10);
	}
}

// DETAILS of all the matches
$Height=2.5;
$ColWidth=(max($pdf->getPageWidth(), $pdf->getPageHeight())-20-12)/5;
$Blocks=array(0, 10, 13+$ColWidth, 16+$ColWidth*2, 19+$ColWidth*3, 22+$ColWidth*4);
$ClubCol=($ColWidth-14)/2;

$pads=$pdf->getCellPaddings();
$pdf->setCellPaddings(0,0,0,0);

foreach($PdfData->rankData['details'] as $Event => $section) {
	$RunNumber=1;
	foreach($section as $CompId => $Lines) {
		if(empty($PdfData->rankData['sections'][$Event])) {
			continue;
		}
		$pdf->AddPage('L');

		// testastampa
		if ($PdfData->rankData['sections'][$Event]['meta']['printHeader']) {
	        $pdf->SetFont($pdf->FontStd,'B',7);
			$pdf->Cell(0, 5,  $PdfData->rankData['sections'][$Event]['meta']['printHeader'], 0, 1, 'R', 0);
		}

		// Titolo della tabella
	    $pdf->SetFont($pdf->FontStd,'B',7);
		$pdf->Cell(0, 5, $PdfData->rankData['sections'][$Event]['meta']['descr'] . ' / ' . $PdfData->rankData['meta']['Run'.$RunNumber++].' - '.$PdfData->rankData['competitions'][$CompId], 1, 1, 'C', 1);
		$pdf->ln(2);

		// remembers where the Y is!
		$OrgY=$pdf->GetY();

		foreach($Lines as $Line => $Matches) {
	        $pdf->SetFont($pdf->FontStd,'B',5);
			$pdf->SetLeftMargin($Blocks[$Line]);
			$pdf->SetXY($Blocks[$Line], $OrgY);
			$pdf->Cell($ColWidth, $Height, $PdfData->rankData['meta']['Game'.(($RunNumber-2)*5+$Line)], 1, 1, 'C', 1);
			$pdf->ln(2);


			foreach($Matches as $Match) {
		        $pdf->SetFont($pdf->FontStd,'',5);
				$pdf->Cell(2, $Height, '', 1, 0, 'C', '1');
				$pdf->Cell($ClubCol, $Height, $Match['details']['E']['Name1'], 1, 0, 'C', 1);
				$pdf->Cell(6,$Height, $PdfData->rankData['meta']['score'], 1, 0, 'C', 1);
				$pdf->Cell($ClubCol, $Height, $Match['details']['E']['Name2'], 1, 0, 'C', 1);
		        $pdf->SetFont($pdf->FontStd,'b',5);
				$pdf->Cell(6, $Height, $PdfData->rankData['meta']['points'], 1, 1, 'C', 1);

				foreach(array('I1','I2','E','I3','I4') as $Type) {
					if($Type=='I4' and $Match['team2']=='0335057_0') {
						//debug_svela($Match);
					}
					$PrintScore=empty($Match['details'][$Type]) ? 0 : $Match['details'][$Type]['points1']+$Match['details'][$Type]['points2'];
		            $pdf->SetFont($pdf->FontStd,'',4);
					$pdf->Cell(2, $Height, $Type , 1, 0, 'C');
					$pdf->Cell($ClubCol, $Height, !empty($Match['details'][$Type]['Name1']) ? $Match['details'][$Type]['Name1'] : '',1,0,'C');
					$pdf->Cell(3, $Height, ($PrintScore and $Match['details'][$Type]['score1']+$Match['details'][$Type]['score2']) ? $Match['details'][$Type]['score1'] : '',1,0,'C');
					$pdf->Cell(3, $Height, ($PrintScore and $Match['details'][$Type]['score1']+$Match['details'][$Type]['score2']) ? $Match['details'][$Type]['score2'] : '',1,0,'C');
					$pdf->Cell($ClubCol, $Height, !empty($Match['details'][$Type]['Name2']) ? $Match['details'][$Type]['Name2']:'',1,0,'C');
		            $pdf->SetFont($pdf->FontStd,'b',4);
					$pdf->Cell(3, $Height, $PrintScore ? $Match['details'][$Type]['points1'] : '',1,0,'C');
					$pdf->Cell(3, $Height, $PrintScore ? $Match['details'][$Type]['points2'] : '',1,1,'C');
					//debug_svela($Match);
				}
				$PrintScore=$Match['score1']+$Match['score2'];
	            $pdf->SetFont($pdf->FontStd,'',4);
				$pdf->Cell(2, $Height, '', 0, 0, 'C');
				$pdf->Cell($ClubCol, $Height, $PdfData->rankData['meta']['gameTotal'],0,0,'R');
	            $pdf->SetFont($pdf->FontStd,'b',4);
				$pdf->Cell(3, $Height, $PrintScore ? (($Match['score1']>$Match['score2'] or ($Match['score1']==$Match['score2'] and $Match['winner1'])) ? 2 : 0) : '',0,0,'C');
				$pdf->Cell(3, $Height, $PrintScore ? (($Match['score2']>$Match['score1'] or ($Match['score1']==$Match['score2'] and $Match['winner2'])) ? 2 : 0) : '',0,0,'C');
				$pdf->Cell($ClubCol, $Height, '',0,0,'C');

				$pdf->Cell(3, $Height, $PrintScore ? $Match['score1'] : '',0,0,'C');
				$pdf->Cell(3, $Height, $PrintScore ? $Match['score2'] : '',0,1,'C');
				$pdf->ln(2);
			}

		}
		// Header vero e proprio
		$pdf->SetLeftMargin(10);
	}
}
$pdf->setCellPaddings($pads['L'],$pads['T'],$pads['R'],$pads['B']);

