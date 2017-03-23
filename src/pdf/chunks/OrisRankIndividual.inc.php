<?php

$PdfData->LastUpdate=$PdfData->rankData['meta']['lastUpdate'];
$pdf->setPhase($PdfData->Phase);
$pdf->setDocUpdate($PdfData->rankData['meta']['lastUpdate']);

$First=true;

foreach($PdfData->rankData['sections'] as $Event => $section) {

	if(empty($section['items'])) continue;

	$ElimCols=0;
	if($section['meta']['elim1']) $ElimCols++;
	if($section['meta']['elim2']) $ElimCols++;
	$NumPhases=$section['meta']['firstPhase'] ? ceil(log($section['meta']['firstPhase'], 2))+1 : 1;

	//Titolo del Report
	$arrTitles = array("Rk","Name","NOC","RR Score\nRank");
	$arrSizes = array(9, 33+(7*(7-$NumPhases-$ElimCols)), array(9+(1*(7-$NumPhases-$ElimCols)),20+(6*(7-$NumPhases-$ElimCols))), array(12,6));

	for($i=1; $i<=$ElimCols; $i++) {
		$arrTitles[] = $PdfData->{'Elim' . $i};
		$arrSizes[] = 14;
	}

	foreach($section['meta']['fields']['finals'] as $k=>$v) {
		if(is_numeric($k) && $k!=1) {
			$arrTitles[] = "1/".$k;
			$arrSizes[] = array(7,7);
		}
	}

	//l'ultimo campo è SEMPRE in inglese, quindi lo sovrascrivo!
	$arrTitles[count($arrTitles)-1] = 'Finals';

	$pdf->SetDataHeader($arrTitles, $arrSizes);
	$pdf->setEvent($section['meta']['descr']);
	$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
	if($section['meta']['version']) {
		$pdf->setComment(trim("Vers. {$section['meta']['version']} ({$section['meta']['versionDate']}) {$section['meta']['versionNotes']}"));
	} else {
		$pdf->setComment(trim($section['meta']['printHeader']));
	}
	$pdf->AddPage();
	if($First and (empty($pdf->CompleteBookTitle) or $pdf->CompleteBookTitle!=$PdfData->IndexName)) {
		$pdf->Bookmark($PdfData->IndexName, 0);
		$pdf->CompleteBookTitle=$PdfData->IndexName;
	}
	$First=false;
	$pdf->Bookmark($section['meta']['descr'], 1);

	//Risultati
	foreach($section['items'] as $item) {
		if(empty($item['athlete'])) continue;
		$dataRow = array(
			($item['rank'] ? $item['rank'] : ' '),
			$item['athlete'],
			$item['countryCode'],
			$item['countryName'],
			$item['qualScore'] . "#",
			'/' . substr('   ' .$item['qualRank'],-3,3));

		//Gironi Eliminatori
		if(array_key_exists('e1',$item['elims'])) $dataRow[] = $item['elims']['e1']['score'] . '/' . substr('  ' . $item['elims']['e1']['rank'],-2,2);
		if(array_key_exists('e2',$item['elims'])) $dataRow[] = $item['elims']['e2']['score'] . '/' . substr('  ' . $item['elims']['e2']['rank'],-2,2);

		//Risultati  delle varie fasi
		foreach($item['finals'] as $k=>$v) {
			if($v['tie']==2) {
				$dataRow[] = $PdfData->Bye;
				$dataRow[] = '';
			} else {
				if($k==4 && $section['meta']['matchMode']!=0 && $item['rank']>=5) {
					$dataRow[] = $v['setScore'] . "#";
					$dataRow[] = "(" . $v['score'] .")#";
				} elseif($v['notes'] and ($section['meta']['matchMode']==0 ? $v['score'] : $v['setScore'])==0 ) {
					$dataRow[] = $v['notes'];
					$dataRow[] = '';
				} else {
					$dataRow[] = ($section['meta']['matchMode']==0 ? $v['score'] : $v['setScore']) . "#";
					if(strlen($v['tiebreak'])>0 && $k<=1)
						$dataRow[] = "T.".str_replace('|',',',$v['tiebreak']) . "#";
					elseif($k<=1 && $v['tie']==1)
						$dataRow[] = "*#";
					else
						$dataRow[] = $v['notes'];
				}
			}

		}
		$pdf->printDataRow($dataRow);
	}
}

?>