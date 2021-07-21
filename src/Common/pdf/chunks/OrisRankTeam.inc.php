<?php

$pdf->setPhase($PdfData->Phase);
$pdf->setDocUpdate($PdfData->rankData['meta']['lastUpdate']);

$First=true;
foreach($PdfData->rankData['sections'] as $Event => $section) {
	if(empty($section['items'])) continue;
	$NumComponenti = 1;

	//Titolo del Report
	$arrTitles = array("Rank#", "", "NOC", "Name");
	$arrSizes = array(13,2, array(15,$pdf->getPageWidth()-160),100, 10);

	$pdf->SetDataHeader($arrTitles, $arrSizes);
	$pdf->setEvent($section['meta']['descr']);
	if($section['meta']['version']) {
		$pdf->setComment(trim("Vers. {$section['meta']['version']} ({$section['meta']['versionDate']}) {$section['meta']['versionNotes']}"));
	} else {
		$pdf->setComment(trim($section['meta']['printHeader']));
	}
	$pdf->AddPage();
	$pdf->setOrisCode($section['meta']['OrisCode'], $PdfData->Description);
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
	//$JumpLine=false;

    foreach($section['items'] as $item) {
		$NumComponenti = max(1, count($item['athletes']));
		$changedPage = !$pdf->SamePage($NumComponenti+1, 3.5, $pdf->lastY);
        $minPhase = (count($item['finals']) ==0 ? -1 : min(array_keys($item['finals'])));
		if($item['rank']==1) {
			$ShowNotAwarded=true;
		}
		//if($item['rank']!=0) {
		//	$JumpLine=true;
		//}

	    if($ShowNotAwarded and ($minPhase==4 or $minPhase==8) and $minPhase!=$whatPhase and is_numeric($item['rank'])) {
		    // we have a change in the phase layer, from medals to semifinal losers or from semi to quarters
		    while($CurRanked<4) {
			    // there are missing positions due to DQB, so print "not assigned"
			    $CurRanked++;
			    $rnk=$CurRanked+($whatPhase==4 ? 4 : 0);
			    $pdf->printDataRow(array($rnk.'#','',$PdfData->rankData['meta']['notAwarded'],'',''));
			    $pdf->lastY += 2.5;
		    }
		    $CurRanked=0;
	    }
	    $CurRanked++;

		$dataRow = array(
            (($item['rank'] AND ($oldRank!=$item['rank'] OR !is_numeric($item['rank']) OR $minPhase <= 4 OR $changedPage)) ? $item['rank'].'#' : ' '),
			' ',
			$item['countryCode'],
            $item['countryNameLong'] . ($item['subteam']<=1 ? '' : ' (' . $item['subteam'] .')'));

		if(count($item['athletes'])) {
			$dataRow[] = $item['athletes'][0]['athlete'];
			$dataRow[] = $item['athletes'][0]['irm'];
		} else {
			$dataRow[]='';
		}

		$pdf->printDataRow($dataRow);

		//Metto i nomi degli altri Componenti se li ho
		if($NumComponenti>1) {
			for($k=1; $k<$NumComponenti; $k++) {
				$pdf->printDataRow(array('','','','',
					$item['athletes'][$k]['athlete'],
					$item['athletes'][$k]['irm']));
			}
		}

		$pdf->lastY += 2.5;
        $whatPhase = $minPhase;
        $oldRank = $item['rank'];
	}
}

/*

		//Risultati  delle varie fasi
		foreach($item['finals'] as $k=>$v) {
			if($v['irm']) {
				if($v['irmText']=='DQB') {
					break;
				}
				$dataRow[] = $v['irmText'];
				$dataRow[] = '';
				break;
			} elseif($v['tie']==2) {
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


 */
