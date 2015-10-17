<?php

$PdfData->LastUpdate=$PdfData->rankData['meta']['lastUpdate'];
$pdf->setPhase($PdfData->Phase);
$pdf->setDocUpdate($PdfData->rankData['meta']['lastUpdate']);

foreach($PdfData->rankData['sections'] as $Event => $section) {

	if(empty($section['items'])) continue;

	$ElimCols=0;
	if($section['meta']['elim1']) $ElimCols++;
	if($section['meta']['elim2']) $ElimCols++;
	$NumPhases=$section['meta']['firstPhase'] ? ceil(log($section['meta']['firstPhase'], 2))+1 : 1;

	//Titolo del Report
	$arrTitles = array("Rk","Name","NOC","RR Score\nRank");
	$arrSizes = array(10, 33+(7*(7-$NumPhases-$ElimCols)), array(9+(1*(7-$NumPhases-$ElimCols)),20+(6*(7-$NumPhases-$ElimCols))), array(9,8));

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
	$pdf->setComment(trim($section['meta']['printHeader']));
	$pdf->AddPage();
	$pdf->setOrisCode($PdfData->Code, $PdfData->Description);

	//Risultati
	foreach($section['items'] as $item) {
		if(empty($item['athlete'])) continue;
		$dataRow = array(
			($item['rank'] ? $item['rank'] : ' '),
			$item['athlete'],
			$item['countryCode'],
			$item['countryName'],
			$item['qualScore'] . "#",
			'/' . substr('00' .$item['qualRank'],-2,2));

		//Gironi Eliminatori
		if(array_key_exists('e1',$item['elims'])) $dataRow[] = $item['elims']['e1']['score'] . '/' . substr('00' . $item['elims']['e1']['rank'],-2,2);
		if(array_key_exists('e2',$item['elims'])) $dataRow[] = $item['elims']['e2']['score'] . '/' . substr('00' . $item['elims']['e2']['rank'],-2,2);

		//Risultati  delle varie fasi
		foreach($item['finals'] as $k=>$v) {
			if($v['tie']==2) {
				$dataRow[] = $PdfData->Bye;
				$dataRow[] = '';
			} else {
				if($section['meta']['matchMode']!=0) {
					$dataRow[] = $v['setScore'] . "#";
					$dataRow[] = "(" . $v['score'] .")#";
				} else {
					$dataRow[] = ($section['meta']['matchMode']==0 ? $v['score'] : $v['setScore']) . "#";
					if(strlen($v['tiebreak'])>0 && $k<=1)
						$dataRow[] = "T.".str_replace('|',',',$v['tiebreak']) . "#";
					elseif($k<=1 && $v['tie']==1)
						$dataRow[] = "*#";
					else
						$dataRow[] = "";
				}
			}
		}
		$pdf->printDataRow($dataRow);
	}
}

?>