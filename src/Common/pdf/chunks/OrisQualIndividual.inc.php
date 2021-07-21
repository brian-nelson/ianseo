<?php

$pdf->ShotOffShort=$PdfData->ShotOffShort;
$pdf->CoinTossShort=$PdfData->CoinTossShort;

if(count($rankData['sections'])) {
	$DistSize = 11;
	$AddSize=0;
	$pdf->setDocUpdate($rankData['meta']['lastUpdate']);
	$First=true;

	foreach($rankData['sections'] as $Event => $section) {
		if($section['meta']['version']) {
			$pdf->setComment(trim("Vers. {$section['meta']['version']} ({$section['meta']['versionDate']}) {$section['meta']['versionNotes']}"));
		} else {
			$tmpHeader="";
			$SessHeader=array();
			if (!empty($section['meta']['sesArrows'])) {
				foreach($section['meta']['sesArrows'] as $k=>$v) {
					if($v) {
						$SessHeader[$v][] = $k;
					}
				}
				if(count($section['meta']['sesArrows'])>1) {
					$tmp=array();
					foreach($SessHeader as $v => $Sessions) {
						$tmp[]=$v.' ('.$section['meta']['fields']['session'] . ": " . implode(', ', $Sessions).')';
					}
					$tmpHeader =implode(' - ', $tmp);
				} else {
					$tmpHeader = $v;
				}
			}
			if (strlen($section['meta']['printHeader'])) {
				$pdf->setComment(trim($section['meta']['printHeader']));
			} elseif(strlen($tmpHeader)!=0 && !$section['meta']['running']) {
				$pdf->setComment(trim($tmpHeader));
			}
		}

		$Header = array();
		$HeaderWidth = array();
		$Phase = '';
		$Rows = array();

		// Calcolo Le Misure per i Campi
		if($section['meta']['numDist']>=4) {
			$DistSize = 60 / $section['meta']['numDist'];
		} else  {
			$AddSize = (60-($section['meta']['numDist']*15));
		}
		$snapDistance = ($PdfData->family=='Snapshot' ? $section['meta']['snapDistance']: 0);

		//Preparo l'array di header di stampa
		$arrTitles=array("@Rank","@Back No.#", "@Name", "@NOC");
		$arrSizes=array();
		if($section['meta']['running']) {
			$arrSizes=array(10,10,35 + $AddSize,10);
		} else {
			$arrSizes=array(13,15,40 + $AddSize,15);
		}
		for($i=1; $i<=$section['meta']['numDist']; $i++) {
			$arrTitles[] = "@" . (is_null($section['meta']['fields']['dist_'. $i]) ? '.' . $i . '.' : $section['meta']['fields']['dist_'. $i]) . "#";
			$arrSizes[] = (!$snapDistance ? array($DistSize-5,5) : $DistSize);
		}

		$arrTitles[] = "@" .  $section['meta']['fields']['gold'] . "'s#";
		$arrSizes[] = 10;
		$arrTitles[] = "@" .  $section['meta']['fields']['xnine'] . "'s#";
		$arrSizes[] = 10;

		$arrTitles[] = "Score#";
		$arrSizes[] = 15;
		if($section['meta']['running']) {
			$arrTitles[] = "No. of\nArrows#";
			$arrSizes[] = 13;
			$arrTitles[] = "Arrow\nAverage#";
			$arrSizes[] = 17;
		}
		$arrTitles[] = " ";
		if($section['meta']['running'])
			$arrSizes[] = 2;
		else
			$arrSizes[] = 20;

		$pdf->SetDataHeader($arrTitles, $arrSizes);
		$pdf->setEvent($section['meta']['descr']);
		$pdf->setPhase("Qualification Round" . (empty($_REQUEST["Dist"]) ? '' : ' - ' . $section['fields']['dist_'. $i]));
		$pdf->Records=$section['records'];

		//Aggiungo Pagina
		$pdf->AddPage();
		$pdf->setOrisCode($PdfData->Code, ($section['meta']['running'] ? 'Running ' : '') . 'Results');
		if($First and (empty($pdf->CompleteBookTitle) or $pdf->CompleteBookTitle!=$PdfData->IndexName)) {
			$pdf->Bookmark($PdfData->IndexName, 0);
			$pdf->CompleteBookTitle=$PdfData->IndexName;
		}
		$First=false;
		$pdf->Bookmark($section['meta']['descr'], 1);

		$EndQualified = ($section['meta']['qualifiedNo']==0);
		$StartQualified = ($section['meta']['firstQualified']==1);
		$FirstIrm=false;
		foreach($section['items'] as $item) {
			if(!$StartQualified AND ($section['meta']['finished'] ? $item['rank']: $item['rankBeforeSO']+$item['ct'])>=$section['meta']['firstQualified']) {
				$pdf->addSpacer();
				$StartQualified = true;
			} else if(!$EndQualified && $item['rank']>($section['meta']['qualifiedNo']+$section['meta']['firstQualified']-1)) {
				$pdf->addSpacer();
				$EndQualified = true;
			} else if(!$FirstIrm && strlen($item['rank'])==0) {
				$pdf->addSpacer();
				$FirstIrm = true;
			}
			$dataRow = array(
				$item['rank'] . " #" ,
				ltrim($item['target'], '0') . " #",
				$item['athlete'],
				$item['countryCode']);

			for($i=1; $i<=$section['meta']['numDist'];$i++)
			{
				list($rank,$score)=explode('|',$item['dist_' . $i]);
				if($snapDistance==0)
				{
					$dataRow[] = $score . "#";
					$dataRow[] = $rank ? "/" . str_pad($rank,2," ", STR_PAD_LEFT) . "#" : '';
				}
				elseif($i<$snapDistance)
					$dataRow[] = $score . "#";
				else if($i==$snapDistance)
				{
					list($rankS,$scoreS)=explode('|',$item['dist_Snap']);
					$dataRow[] = $scoreS . "#";
				}
				else
				{
					$dataRow[] = "0#";
				}
			}

			$dataRow[] = (!$snapDistance? $item['gold'] : $item['goldSnap']) . "#";
			$dataRow[] = (!$snapDistance? $item['xnine'] : $item['xnineSnap']) . "#";
			if($section['meta']['running']) {
				$dataRow[] = $item['completeScore']. "#";
				$dataRow[] = $item['hits'] . "#";
			}
			$dataRow[] = (!$snapDistance ?  $item['score'] : $item['scoreSnap']). "#";

			if($snapDistance) {
				$dataRow[] = ($item['scoreSnap']!=$item['score'] ? $item['score'] : "") . "#";
			} elseif($section['meta']['running']) {
				$dataRow[] = '';
			} else {
				$tmpNote = '';
				if($item['so']>0) { //Spareggio
					$tmpNote .=  $pdf->ShotOffShort;
					if(strlen(trim($item['tiebreak']))) {
						$tmpNote .= ' ' . $item['tiebreakDecoded'];
					}
				} else if($item['ct']>1) {
					$tmpNote .=  $pdf->CoinTossShort;
				}
				if($item['notes']) {
					$tmpNote .= ' ' . $item['notes'];
				}
				if(!empty($item['record'])) {
					$tmpNote .= ' ' . $item['record'];
				}
				$dataRow[] = trim($tmpNote);
			}
			$pdf->printDataRow($dataRow);
		}
	}
}
$pdf->Records=array();



?>
