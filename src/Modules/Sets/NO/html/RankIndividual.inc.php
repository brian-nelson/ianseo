<?php
	if(count($rankData['sections'])) {
		$DistSize = 11;
		$AddSize=0;
		foreach($rankData['sections'] as $Event => $section) {
			$PdfData->Order=$section['meta']['order'];
			$Header = array();
			$HeaderWidth = array();
			$Phase = '';
			$Rows = array();

			$ElimCols=0;
			if($section['meta']['elim1']) $ElimCols++;
			if($section['meta']['elim2']) $ElimCols++;

			//Preparo l'array di header di stampa
			if($ORIS) {
				$HtmlTitles=array("Rank", "Name", "Country", "Qualification");
				for($i=1; $i<=$ElimCols; $i++) {
					$HtmlTitles[] = $section['meta']['fields']['elims']['e' . $i];
				}
				foreach($section['meta']['fields']['finals'] as $k=>$v) {
					if(is_numeric($k) && $k>1) $HtmlTitles[] = $v;
				}
				$HtmlTitles[] = 'Final';
			} else {
				$HtmlTitles=array();
				$HtmlTitles[] = $section['meta']['fields']['rank'];
				$HtmlTitles[] = $section['meta']['fields']['athlete'];
				$HtmlTitles[] = $section['meta']['fields']['countryName'];
				$HtmlTitles[] = $section['meta']['fields']['qualRank'];
				for($i=1; $i<=$ElimCols; $i++) {
					$HtmlTitles[] = $section['meta']['fields']['elims']['e' . $i];
				}
				foreach($section['meta']['fields']['finals'] as $k=>$v) {
					if(is_numeric($k) && $k!=1) $HtmlTitles[] = $v;
				}
			}

			$PdfData->HTML[$Event]['Headers'] = $HtmlTitles;
			$PdfData->HTML[$Event]['Description'] =  $PdfData->Description;
			$PdfData->HTML[$Event]['Title'] = $section['meta']['descr'];
			$PdfData->HTML[$Event]['Items'] = array();

			foreach($section['items'] as $item) {
				$HtmlRow = array(
					$item['rank'],
					$item['athlete'],
					$item['countryCode'],
					$item['countryName'],
					$item['qualScore'] . '-' . $item['qualRank'],
					);

				//Risultati  delle varie fasi
				if(array_key_exists('e1',$item['elims'])) {
					$HtmlRow[] = $item['elims']['e1']['score'] . '-' . $item['elims']['e1']['rank'];
				}
				if(array_key_exists('e2',$item['elims'])) {
					$HtmlRow[] = $item['elims']['e2']['score'] . '-' . $item['elims']['e2']['rank'];
				}

				foreach($item['finals'] as $k=>$v) {
					if($v['tie']==2) {
						$HtmlRow[] = $PdfData->Bye;
					} else {
						if($section['meta']['matchMode']!=0) {
							$HtmlRow[] = '(' . $v['score'] . ')  '  .  $v['setScore'];
						} else {
							$r= ($section['meta']['matchMode']==0 ? $v['score'] : $v['setScore']) . ($k<=1 && $v['tie']==1 && strlen($v['tiebreak'])==0 ? '*' : '');
							if(strlen($v['tiebreak'])>0 && $k<=1) $r.= "T.".str_replace('|',',',$v['tiebreak']);
							$HtmlRow[] = $r;
						}
					}
				}
				$PdfData->HTML[$Event]['Items'][]=$HtmlRow;
			}
		}
	}

	$HTML='';
	$RowClass=1;

	$Desc='';
	foreach($PdfData->HTML as $Session => $Data) {
		$Desc=$Data['Title'];
		$HTML.=  '<div class="accordion">';
			$HTML.=  '<div class="title open" id="acc_' . $Session . '">';
			$HTML.=  '<span>' . $Data['Title'] . '</span>';
			$HTML.=  '</div>';
		$HTML.=  '<div class="text title open" id="acc_' . $Session . '-text">';

		//Header
		$HTML.=  '<table class="Griglia" width="100%" cellpadding="0" cellspacing="0">';
		$HTML.=  '<tr>';
			$HTML.=  '<th><div nowrap class="Center title" >' . $Data['Headers'][0]  .'</div></th>';
			$HTML.=  '<th><div nowrap class="Center title" >' . $Data['Headers'][1]  .'</div></th>';
			$HTML.=  '<th colspan="3"><div nowrap class="Center title">' . $Data['Headers'][2]  .'</div></th>';
			for($n=3; $n<count($Data['Headers']);$n++) $HTML.=  '<th><div nowrap class="Center title">' . $Data['Headers'][$n]  .'</div></th>';
		$HTML.=  '</tr>';

		$first=" first";
		foreach($Data['Items'] as $Session => $Archer) {
			if(!$Archer) {
				$RowClass=1-$RowClass;
				continue;
			}
			$HTML.= '<tr class="row'.$RowClass.$first.'">';
				$HTML.= '<td class="col Rank">' . $Archer[0]  .'</td>';
				$HTML.= '<td class="col Athlete">' . $Archer[1] .'</td>';
				$HTML.= '<td class="Flag">' . (is_file($TourPath . '/img/'.$Archer[2].'.gif')?'<img src="img/'.$Archer[2].'.gif" align="absmiddle">':'')  .'</td>';
				$HTML.= '<td class="col CoCode">' . $Archer[2] .'</td>';
				$HTML.= '<td class="col Country">' . $Archer[3] .'</td>';
				for($n=4; $n<count($Archer); $n++) {
					$HTML.= '<td class="col Dists">' . $Archer[$n] .'</td>';
				}
			$HTML.= '</tr>';
			$first='';
		}
		$HTML.= '</table>';
		$HTML.= '</div>';
		$HTML.= '</div>';
	}

	if($HTML) {
		$HTML = sprintf(
			file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/Common/templates/startlist-page.inc.php'),
			$Desc,
			$HTML
			);
	}
?>