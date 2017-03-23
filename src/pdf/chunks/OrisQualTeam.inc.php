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
			if (!empty($section['meta']['sesArrows']))
			{
				foreach($section['meta']['sesArrows'] as $k=>$v)
				{
					if($v)
					{
						if(strlen($tmpHeader)!=0)
							$tmpHeader .= " - ";
						$tmpHeader .= $v;
						if(count($section['meta']['sesArrows'])!=1) {
							$tmpHeader .= " (" . $section['meta']['fields']['athletes']['fields']['session'] . ": " . $k  . ")";
						}
					}
				}
			}
			if (strlen($section['meta']['printHeader']))
				$pdf->setComment(trim($section['meta']['printHeader']));
			else if(strlen($tmpHeader)!=0 && !$section['meta']['running'])
				$pdf->setComment(trim($tmpHeader));
		}

		$Header = array();
		$HeaderWidth = array();
		if($section['meta']['running'])
		{
			$Header = array("Rank","NOC", "Name","Individual\nTotal#","Team\nTotal#","No. of\nArrows#" , "Arrow\nAverage#"," ", " ");
			$HeaderWidth = array(15,45,45,20,15,15,20,10,5);
		}
		else
		{
			$Header = array("Rank","NOC", "Name","Individual\nTotal#","Team\nTotal#"," ", " ");
			$HeaderWidth = array(15,50,50,20,25,20,10);
		}
		$pdf->SetDataHeader($Header, $HeaderWidth);
		$pdf->setEvent($section['meta']['descr']);
		$pdf->setPhase('Qualification Round');
		$pdf->Records=$section['records'];

		//Aggiungo Pagina
		$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
		$pdf->AddPage();
		if($First and (empty($pdf->CompleteBookTitle) or $pdf->CompleteBookTitle!=$PdfData->IndexName)) {
			$pdf->Bookmark($PdfData->IndexName, 0);
			$pdf->CompleteBookTitle=$PdfData->IndexName;
		}
		$First=false;
		$pdf->Bookmark($section['meta']['descr'], 1);

		$EndQualified = false;
		foreach($section['items'] as $item) {
			if(!$EndQualified && $item['rank']>$section['meta']['qualifiedNo']) {
				$pdf->addSpacer();
				$EndQualified = true;
			}

			$aths=$item['athletes'];

			$tmpRow=array();

			$t=array();
			for ($i=0;$i<count($aths);++$i) {
				$dataRow=array();

				if ($i==0) {
					$dataRow[]=$item['rank'] . " #";
					$dataRow[]=$item['countryCode'] . ' - ' . $item['countryName'] . (intval($item['subteam'])<=1 ? '' : ' (' . $item['subteam'] .')');
				} else {
					$dataRow[]=' ';
					$dataRow[]=' ';
				}

				$t[]=$aths[$i]['athlete'];
				$dataRow[]=$aths[$i]['athlete'];
				$dataRow[]=$aths[$i]['quscore'] . ' #';

				if ($i==0) {
					if($section['meta']['running']) {
						$dataRow[]=$item['completeScore'] . ' #';
						$dataRow[]=$item['hits'] . ' #';
					}
					$dataRow[]=$item['score'] . ' #';
					if($item['notes']) {
						$dataRow[] = $item['notes'] . "#";
					}

					if($item['so']>0) {
						$dataRow[] = "T. " . $item['gold'] . ";" . $item['xnine'];
						$dataRow[] = $item['tiebreakDecoded'] ? $pdf->ShotOffShort . ' ' . $item['tiebreakDecoded'] : $pdf->ShotOffShort;
// 						debug_svela($PdfData);

					} elseif ($item['ct']>1) {
						$dataRow[] = "T. " . $item['gold'] . ";" . $item['xnine'];
						$dataRow[] = $pdf->CoinTossShort;
					} elseif ($item['tie']) {
						$dataRow[] = "T. " . $item['gold'] . ";" . $item['xnine'];
						$dataRow[] = " ";
					} else {
						$dataRow[]=" ";
						$dataRow[]=" ";
					}

				} else {
					$dataRow[]=' ';
					$dataRow[]=' ';
					$dataRow[]=' ';
				}
				$tmpRow[]=$dataRow;
			}

			$pdf->samePage(count($tmpRow));
			foreach($tmpRow as $row) $pdf->printDataRow($row);

			$pdf->lastY += 2.5;
		}
	}
}

$pdf->Records=array();
?>