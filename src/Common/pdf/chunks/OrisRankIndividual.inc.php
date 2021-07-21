<?php

$PdfData->LastUpdate=$PdfData->rankData['meta']['lastUpdate'];
$pdf->setPhase($PdfData->Phase);
$pdf->setDocUpdate($PdfData->rankData['meta']['lastUpdate']);

$First=true;

foreach($PdfData->rankData['sections'] as $Event => $section) {

	if(empty($section['items'])) continue;

	if(empty($section['meta']['jumpLines'])) {
		$section['meta']['jumpLines']=array(5,9);
	}

	//Titolo del Report
	$arrTitles = array("Rank#", "", "Name", "NOC");
	$arrSizes = array(13,2, 60, array(15,$pdf->getPageWidth()-120));


	$pdf->SetDataHeader($arrTitles, $arrSizes);
	$pdf->setEvent($section['meta']['descr']);
	if($section['meta']['version']) {
		$pdf->setComment(trim("Vers. {$section['meta']['version']} ({$section['meta']['versionDate']}) {$section['meta']['versionNotes']}"));
	} else {
		$pdf->setComment(trim($section['meta']['printHeader']));
	}
	$pdf->AddPage();
	$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
	if($First and (empty($pdf->CompleteBookTitle) or $pdf->CompleteBookTitle!=$PdfData->IndexName)) {
		$pdf->Bookmark($PdfData->IndexName, 0);
		$pdf->CompleteBookTitle=$PdfData->IndexName;
	}
	$First=false;
	$pdf->Bookmark($section['meta']['descr'], 1);

	$whatPhase = 0;
	$oldRank = -1;
	// Rank needs to take into account the missing positions due to DQB!
	$CurRanked=0;
	$ShowNotAwarded=false;

	foreach($section['items'] as $item) {
		if(empty($item['athlete'])) {
            continue;
        }
		if($item['rank']==1) {
			$ShowNotAwarded=true;
		}

		$minPhase = (count($item['finals']) ==0 ? -1 : min(array_keys($item['finals'])));

		if($ShowNotAwarded and ($minPhase==4 or ($minPhase==8 and !$section['meta']['elimType'])) and $minPhase!=$whatPhase) {
			// we have a change in the phase layer, from medals to semifinal losers or from semi to quarters
			while($CurRanked<4) {
				// there are missing positions due to DQB, so print "not assigned"
                $CurRanked++;
				$rnk=$CurRanked+($whatPhase==4 ? 4 : 0);
                $pdf->printDataRow(array($rnk.'#','',$PdfData->rankData['meta']['notAwarded'],'',''));
			}
			$CurRanked=0;
		}
        $CurRanked++;

		if($item['rank']>$oldRank) {
			$oldRank=-1;
		}

		if($oldRank!=$item['rank'] and (in_array($item['rank'], $section['meta']['jumpLines']) OR !$pdf->samePage(2)) and $item['rank']<=$section['meta']['lastQualified']) {
            $pdf->printDataRow(array('','','','',''));
            $oldRank = -1;
		}

		$dataRow = array(
			(($item['rank'] AND ($oldRank!=$item['rank'] OR !$pdf->samePage(2) or !is_numeric($item['rank']))) ? $item['rank'].'#' : ' '),
            '',
			$item['athlete'],
			$item['countryCode'],
			$item['countryNameLong'],
			);
		$pdf->printDataRow($dataRow);
        $whatPhase = $minPhase;
        $oldRank = $item['rank'];
	}
}

