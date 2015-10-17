<?php

$pdf->ShotOffShort=$PdfData->ShotOffShort;
$pdf->CoinTossShort=$PdfData->CoinTossShort;

if(count($PdfData->rankData['sections'])) {
	$DistSize = 15;
	$AddSize=0;
	$pdf->setDocUpdate($PdfData->rankData['meta']['lastUpdate']);
	foreach($PdfData->rankData['sections'] as $section) {
		$arrTitles=array("Rank","Back No.#", "Name", "NOC", "Elimination#","@" . $section['meta']['fields']['gold'] . "'s#","@" . $section['meta']['fields']['xnine'] . "'s#");
		$arrSizes=array(15,15,60,20,array(15,5),10,10);
		if($section['meta']['running'])
			$arrTitles[] = "No. of\nArrows#";
		else
			$arrTitles[] = "Score#";
		$arrSizes[] = 20;

		if($section['meta']['running'])
		{
			$arrTitles[] = "Arrow\nAverage#";
			$arrSizes[] = 15;
		}
		$arrTitles[] = " ";
		$arrSizes[] = ($section['meta']['running'] ? 5 : 20);

		$pdf->SetDataHeader($arrTitles, $arrSizes);

		$pdf->setEvent($section['meta']['descr']);

		if($section['meta']['roundText'] == 'Eliminations_1')
			$pdf->setPhase("Elimination Round 1");
		else if($section['meta']['roundText'] == 'Eliminations_2')
			$pdf->setPhase("Elimination Round 2");
		else
			$pdf->setPhase("Elimination Round");

			// Gestione del commento di Stampa
//			if (!is_null($MyRow->EvQualPrintHead) && $MyRow->EvQualPrintHead!='')
//				$pdf->setComment($MyRow->EvQualPrintHead);

		$pdf->setOrisCode('C73A', ($section['meta']['running']  ? 'Running ' : '') . 'Results');
		$pdf->AddPage();

		$EndQualified = false;
		foreach($section['items'] as $item) {
			if($EndQualified===false && $item['rank']>$section['meta']['qualifiedNo']) {
				$pdf->addSpacer();
				$EndQualified = true;
			}
			$dataRow = array(
				$item['rank'],
				$item['target'] . " #",
				$item['athlete'],
				$item['countryCode'],
				$item['completeScore'] . "#",
				"/" . str_pad($item['rank'],2," ", STR_PAD_LEFT) . "#",
				$item['gold'] . "#",
				$item['xnine'] . "#",
				);

			if($section['meta']['running']) $dataRow[] = $item['hits'] . "#";
			$dataRow[] = $item['score'] . "#";

			if($section['meta']['running']) {
				$dataRow[] = '';
			} else {
				if($item['so']>0) { //Spareggio
					$dataRow[] = $pdf->ShotOffShort;
					if(strlen(trim($item['tiebreak']))) {
						$tmpArr=" T.";
						for($countArr=0; $countArr<strlen(trim($item['tiebreak'])); $countArr++) {
							$tmpArr .= DecodeFromLetter(substr(trim($item['tiebreak']),$countArr,1)) . ",";
						}
						$dataRow[] = substr($tmpArr,0,-1);
					}
				}
				elseif($item['ct']>1) {
					$dataRow[] = $pdf->CoinTossShort;
				} else {
					$dataRow[] = '';
				}
			}

			$pdf->printDataRow($dataRow);
		}
	}
}

?>