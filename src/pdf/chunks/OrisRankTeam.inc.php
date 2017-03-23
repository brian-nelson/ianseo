<?php

$pdf->setPhase($PdfData->Phase);
$pdf->setDocUpdate($PdfData->rankData['meta']['lastUpdate']);

$First=true;
foreach($PdfData->rankData['sections'] as $Event => $section) {
	if(empty($section['items'])) continue;

	$NumPhases=$section['meta']['firstPhase'] ? ceil(log($section['meta']['firstPhase'], 2))+1 : 1;
	$NumComponenti = 1;

	//Titolo del Report
	$arrTitles = array("Rk", "NOC", "Back\nNo#", "Name", "RR Score\nRank");
	$arrSizes = array(10, 45+(8*(4-$NumPhases)), 11, 45+(7*(4-$NumPhases)), array(10,9));

	foreach($section['meta']['fields']['finals'] as $k=>$v) {
		if(is_numeric($k) && $k!=1) {
			$arrTitles[] = "1/".$k;
			$arrSizes[] = array(8,7);
		}
	}
	//l'ultimo campo è SEMPRE in inglese, quindi lo sovrascrivo!
	$arrTitles[count($arrTitles)-1] = 'Finals';


	$pdf->SetDataHeader($arrTitles, $arrSizes);
	$pdf->setEvent($section['meta']['descr']);
	if($section['meta']['version']) {
		$pdf->setComment(trim("Vers. {$section['meta']['version']} ({$section['meta']['versionDate']}) {$section['meta']['versionNotes']}"));
	} else {
		$pdf->setComment(trim($section['meta']['printHeader']));
	}
	$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
	$pdf->AddPage();
	if($First and (empty($pdf->CompleteBookTitle) or $pdf->CompleteBookTitle!=$PdfData->IndexName)) {
		$pdf->Bookmark($PdfData->IndexName, 0);
		$pdf->CompleteBookTitle=$PdfData->IndexName;
	}
	$First=false;
	$pdf->Bookmark($section['meta']['descr'], 1);

	foreach($section['items'] as $item) {
		$NumComponenti = max(1, count($item['athletes']));
		$pdf->SamePage($NumComponenti);

		$dataRow = array(
			($item['rank'] ? $item['rank'] : ''),
			$item['countryCode'] . ' -  ' . $item['countryName'] . ($item['subteam']<=1 ? '' : ' (' . $item['subteam'] .')'));

		if(count($item['athletes'])) {
			$dataRow[] = $item['qualRank']. "A#";
			$dataRow[] = $item['athletes'][0]['athlete'];
		} else {
			$dataRow[]='';
			$dataRow[]='';
		}
		$dataRow[] = $item['qualScore'] . "#";
		$dataRow[] = '/' . substr('00' . $item['qualRank'],-2,2);
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
					if(strlen($v['tiebreak'])>0 && $k<=1) {
						$dataRow[] =  "T." . $v['tiebreakDecoded'] . "#";
					} elseif($k<=1 && $v['tie']==1) {
						$dataRow[] = "*#";
					} else {
						$dataRow[] = $v['notes'];
					}
				}
			}
		}

		$pdf->printDataRow($dataRow);

		//Metto i nomi degli altri Componenti se li ho
		if($NumComponenti>1) {
			for($k=1; $k<$NumComponenti; $k++) {
				$pdf->printDataRow(array('','',
					$item['qualRank']. chr(65+$k) . "#",
					$item['athletes'][$k]['athlete']));
			}
		}

		$pdf->lastY += 2.5;
	}
}

?>