<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	include_once('Common/Fun_FormatText.inc.php');
	include_once('Final/Fun_Xml_Ranking.inc.php');
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
	$rank=Obj_RankFactory::create('FinalTeam',$options);
	$rank->read();
	$rankData=$rank->getData();

	if(count($rankData['sections']))
	{
		foreach($rankData['sections'] as $section)
		{
			$NumPhases=numPhases($section['meta']['firstPhase']);
	
			if(count($section['items']))
			{
				$ListHeader = $XmlDoc->createElement('List');
				$ListHeader->setAttribute('Title', get_text($section['meta']['descr'],'','',true));
				$ListHeader->setAttribute('Columns', 3 + $NumPhases);
				$XmlRoot->appendChild($ListHeader);
	
				$TitElem=array();
				$TitElem['Rank'] = $section['meta']['fields']['rank'];
				$TitElem['Country'] = $section['meta']['fields']['countryName'];
				$TitElem['Rank Score'] = $section['meta']['fields']['qualRank'];
	
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
					$NumComponenti = max(1,count($item['athletes']));
					
					$tmpRow=array();
					$tmpRow['Rank'] = ($item['rank'] ? $item['rank'] : '');
					$tmpRow['Team Name'] = $item['countryCode'] . ' -  ' . $item['countryName'] . ($item['subteam']<=1 ? '' : ' (' . $item['subteam'] .')');
					$tmpRow['TeScore'] = number_format($item['qualScore'],0,get_text('NumberDecimalSeparator'), get_text('NumberThousandsSeparator') ) . '-' . substr('00' . $item['qualRank'],-2,2);
	
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
								{
									$tmpTxt="";
									$tmpArr=explode("|",$v['tiebreak']);
									for($countArr=0; $countArr<count($tmpArr); $countArr+=$NumComponenti)
										$tmpTxt .= array_sum(array_slice($tmpArr,$countArr,$NumComponenti)). ",";
									$tmp .=  " T." . substr($tmpTxt,0,-1);
								}
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