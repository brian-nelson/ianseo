<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Final/Fun_Xml_Ranking.inc.php');
	require_once('Common/Lib/Obj_RankFactory.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$ToFit=(isset($_REQUEST['ToFitarco']) ? $_REQUEST['ToFitarco'] : null);

	$XmlDoc = new DOMDocument('1.0', 'UTF-8');

	$TmpNode = $XmlDoc->createProcessingInstruction ("xml-stylesheet", 'type="text/xsl" href="/Common/Styles/StyleRanking.xsl" ');
	$XmlDoc->appendChild($TmpNode);


	$XmlRoot = $XmlDoc->createElement('Results');
	$XmlRoot->setAttribute('IANSEO', ProgramVersion);
	$XmlRoot->setAttribute('TS', date('Y-m-d H:i:s'));
	$XmlDoc->appendChild($XmlRoot);

	$ListHeader = NULL;

	$options=array();
	if(isset($_REQUEST["Event"]) && $_REQUEST["Event"][0]!=".")
		$options['eventsR'] = $_REQUEST["Event"];
	$rank=Obj_RankFactory::create('FinalInd',$options);
	$rank->read();
	$rankData=$rank->getData();

	// se ho degli eventi
	if(count($rankData['sections']))
	{
		foreach($rankData['sections'] as $section)
		{
			$ElimCols=0;
			if($section['meta']['elim1'])
				$ElimCols++;
			if($section['meta']['elim2'])
				$ElimCols++;

			$NumPhases=numPhases($section['meta']['firstPhase']);

			//Se Esistono righe caricate....
			if(count($section['items']))
			{

				$ListHeader = $XmlDoc->createElement('List');
				$ListHeader->setAttribute('Title', get_text($section['meta']['descr'],'','',true));
				$ListHeader->setAttribute('Columns', 4+$ElimCols + $NumPhases);
				$XmlRoot->appendChild($ListHeader);

				$TitElem=array();
				$TitElem['Rank'] = $section['meta']['fields']['rank'];
				$TitElem['Athlete'] = $section['meta']['fields']['athlete'];
				$TitElem['Country'] = $section['meta']['fields']['countryName'];
				$TitElem['Rank Score'] = $section['meta']['fields']['qualRank'];

				
				for($i=1; $i<=$ElimCols; $i++)
					$TitElem['Elim' . $i] = $section['meta']['fields']['elims']['e' . $i];
				foreach($section['meta']['fields']['finals'] as $k=>$v)
				{
					if(is_numeric($k) && $k!=1)
						$TitElem['Phase' . $k] = $v;
				}

				// crea il titolo
				XML_Ranking_Header($XmlDoc, $ListHeader, $TitElem);


				$XMLRows=array();
				foreach($section['items'] as $item)
				{
					$tmpRow=array();

					$tmpRow['Rank'] = ($item['rank'] ? $item['rank'] : ' ');
					$tmpRow['Athlete'] = $item['athlete'];
					$tmpRow['Country'] = $item['countryCode'] . ' - ' . $item['countryName'];
					$tmpRow['QuScore'] = number_format($item['qualScore'], 0, get_text('NumberDecimalSeparator'), get_text('NumberThousandsSeparator')) . '-' . substr('00' . $item['qualRank'],-2,2);

					//Gironi Eliminatori
					if(array_key_exists('e1',$item['elims']))
						$tmpRow['Elim1'] = number_format($item['elims']['e1']['score'],0,get_text('NumberDecimalSeparator'),get_text('NumberThousandsSeparator')) . '-' . substr('00' . $item['elims']['e1']['rank'],-2,2); 
					if(array_key_exists('e2',$item['elims']))
						$tmpRow['Elim2'] = number_format($item['elims']['e2']['score'],0,get_text('NumberDecimalSeparator'),get_text('NumberThousandsSeparator')) . '-' . substr('00' . $item['elims']['e2']['rank'],-2,2); 
					//Risultati  delle varie fasi
					foreach($item['finals'] as $k=>$v)
					{
						$tmp = '';
						if($v['tie']==2)
							$tmp = get_text('Bye');
						else
						{
							if($k==4 && $section['meta']['matchMode']!=0 && $item['rank']>=5)
								$tmp = $v['setScore'] . " (" . $v['score'] .")";
							else
							{
								$tmp = ($section['meta']['matchMode']==0 ? $v['score'] : $v['setScore']);
								if(strlen($v['tiebreak'])>0 && $k<=1)
									$tmp .= " T.".str_replace('|',',',$v['tiebreak']);
								elseif($k<=1 && $v['tie']==1)
									$tmp .= "*";
							}
						}
						$tmpRow['phase' . $k] = $tmp;
					}
					$XMLRows[] = $tmpRow;
				}

				foreach($XMLRows as $XMLRow) {
					XML_Ranking_Row($XmlDoc, $ListHeader, $XMLRow);
				}
			}
		}
	}


	if (is_null($ToFit))
	{
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Content-type: text/xml; charset=' . PageEncode);
		echo $XmlDoc->SaveXML();
	}
	else
		$XmlDoc->save($ToFit);
?>