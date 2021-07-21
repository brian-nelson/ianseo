<?php

$rankData=$PdfData->rankData;

$First=true;
foreach($rankData['sections'] as $Event => $section) {
	$pdf->endPage();
	$pdf->setOrisCode($PdfData->ScoreCode, '', true);
	$pdf->setEvent($section['meta']['eventName']);
	$pdf->Records=array(); // $section['records'];
	$pdf->setPhase($PdfData->ScorePhase);
	$pdf->setDocUpdate($PdfData->LastUpdate);
	$pdf->setDataHeader(array(),array());

	if($section['meta']['version']) {
		$pdf->setComment(trim("Vers. {$section['meta']['version']} ({$section['meta']['versionDate']}) {$section['meta']['versionNotes']}"));
	} else {
		$pdf->setComment('');
	}

	$pdf->AddPage('P');
	$AddPage=false;

	if($First and (empty($pdf->CompleteBookTitle) or $pdf->CompleteBookTitle!=$PdfData->ScoreIndexName)) {
		$pdf->Bookmark($PdfData->ScoreIndexName, 0);
		$pdf->CompleteBookTitle=$PdfData->ScoreIndexName;
	}
	$pdf->Bookmark($section['meta']['eventName'], 1);

	$CurPlace=0;
	ksort($section['phases']);
	foreach($section['phases'] as $PhaseNum => $Phase) {
		foreach($Phase['items'] as $item) {
			if(!($item['countryCode'] and $item['oppCountryCode'])) {
				continue;
			}

			if($AddPage and !$CurPlace) {
				$pdf->AddPage('P');
			}
			$First=false;
			$pdf->OrisScorecard($item, $CurPlace, $Phase['meta'], $section['meta'], $rankData['meta'], 1);

			$CurPlace=1-$CurPlace;
			$AddPage=true;
		}
	}
}
