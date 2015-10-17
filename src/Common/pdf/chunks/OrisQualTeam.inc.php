<?php

$pdf->ShotOffShort=$PdfData->ShotOffShort;
$pdf->CoinTossShort=$PdfData->CoinTossShort;

if(count($rankData['sections'])) {
	$DistSize = 11;
	$AddSize=0;
	$pdf->setDocUpdate($rankData['meta']['lastUpdate']);

	foreach($rankData['sections'] as $Event => $section) {
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
					if(count($section['meta']['sesArrows'])!=1)
						$tmpHeader .= " (" . $section['meta']['fields']['session'] . ": " . $k  . ")";
				}
			}
		}
		if (strlen($section['meta']['printHeader']))
			$pdf->setComment(trim($section['meta']['printHeader']));
		else if(strlen($tmpHeader)!=0 && !$section['meta']['running'])
			$pdf->setComment(trim($tmpHeader));

		$Header = array();
		$HeaderWidth = array();
		if($section['meta']['running'])
		{
			$Header = array("Rank","NOC", "Name","Individual\nTotal#","No. of\nArrows#" , "Arrow\nAverage#"," ", " ");
			$HeaderWidth = array(15,45,45,20,20,20,15,10);
		}
		else
		{
			$Header = array("Rank","NOC", "Name","Individual\nTotal#","Team\nTotal#"," ", " ");
			$HeaderWidth = array(15,50,50,20,25,20,10);
		}
		$pdf->SetDataHeader($Header, $HeaderWidth);
		$pdf->setEvent($section['meta']['descr']);
		$pdf->setPhase('Qualification Round');

		//Aggiungo Pagina
		$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
		$pdf->AddPage();

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
					if($section['meta']['running'])
						$dataRow[]=$item['hits'] . ' #';

					$dataRow[]=$item['score'] . ' #';

					if($item['so']>0) {
						$dataRow[] = "T. " . $item['gold'] . ";" . $item['xnine'];
						$dataRow[] = $item['tiebreakDecoded'] ? $pdf->ShotOffShort . ' ' . $item['tiebreakDecoded'] : '';

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

?>