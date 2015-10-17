<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/Fun_PrintOuts.php');
	require_once('Common/Lib/Obj_RankFactory.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$ToFit=(isset($_REQUEST['ToFitarco']) ? $_REQUEST['ToFitarco'] : null);

	$XmlDoc = new DOMDocument('1.0', 'UTF-8');

	$TmpNode = $XmlDoc->createProcessingInstruction ("xml-stylesheet", 'type="text/xsl" href="/Common/Styles/StyleTeam.xsl" ');
	$XmlDoc->appendChild($TmpNode);

	$XmlRoot = $XmlDoc->createElement('Results');
	$XmlRoot->setAttribute('IANSEO', ProgramVersion);
	$XmlRoot->setAttribute('TS', date('Y-m-d H:i:s'));
	$XmlDoc->appendChild($XmlRoot);

	$ListHeader = NULL;

	$options=array();
	if(isset($_REQUEST["Event"]))
		$options['events'] = $_REQUEST["Event"];
	if(isset($_REQUEST["MaxNum"]) && is_numeric($_REQUEST["MaxNum"]))
		$options['cutRank'] = $_REQUEST["MaxNum"];
	if(isset($_REQUEST["ScoreCutoff"]) && is_numeric($_REQUEST["ScoreCutoff"]))
		$options['cutScore'] = $_REQUEST["ScoreCutoff"];
	if(isset($_REQUEST["Classes"]))
	{
		if(is_array($_REQUEST["Classes"]))
			$options['cls'] = $_REQUEST["Classes"];
		else
			$options['cls'] = array($_REQUEST["Classes"]);
	}
	if(isset($_REQUEST["Divisions"]))
	{
		if(is_array($_REQUEST["Divisions"]))
			$options['divs'] = $_REQUEST["Divisions"];
		else
			$options['divs'] = array($_REQUEST["Divisions"]);
	}

	$family='DivClassTeam';

	$rank=Obj_RankFactory::create($family,$options);
	$rank->read();
	$rankData=$rank->getData();

//	print '<pre>';
//	print_r($rankData);
//	print '</pre>';

	if(count($rankData['sections']))
	{
		foreach($rankData['sections'] as $section)
		{
			$ListHeader = $XmlDoc->createElement('List');
			$ListHeader->setAttribute('Title', get_text($section['meta']['descr'],'','',true));
			$ListHeader->setAttribute('Columns', '12');
			$XmlRoot->appendChild($ListHeader);

			$TmpNode = $XmlDoc->createElement('Caption',get_text($section['meta']['fields']['rank'],'','',true));
			$TmpNode->setAttribute('Name', 'Rank');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text($section['meta']['fields']['countryName'],'','',true));
			$TmpNode->setAttribute('Name', 'Nation');
			$TmpNode->setAttribute('Columns', '2');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text($section['meta']['fields']['athletes']['fields']['athlete'],'','',true));
			$TmpNode->setAttribute('Name', 'Athlete Name');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text($section['meta']['fields']['athletes']['fields']['div'],'','',true));
			$TmpNode->setAttribute('Name', 'Division');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text($section['meta']['fields']['athletes']['fields']['ageclass'],'','',true));
			$TmpNode->setAttribute('Name', 'Age Class');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text($section['meta']['fields']['athletes']['fields']['class'],'','',true));
			$TmpNode->setAttribute('Name', 'Class');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text($section['meta']['fields']['athletes']['fields']['subclass'],'','',true));
			$TmpNode->setAttribute('Name', 'Sub Class');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text($section['meta']['fields']['score'],'','',true));
			$TmpNode->setAttribute('Name', 'Total');
			$TmpNode->setAttribute('Columns', '2');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text($section['meta']['fields']['gold'],'','',true));
			$TmpNode->setAttribute('Name', 'Gold');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			$TmpNode = $XmlDoc->createElement('Caption',get_text($section['meta']['fields']['xnine'],'','',true));
			$TmpNode->setAttribute('Name', 'XNine');
			$TmpNode->setAttribute('Columns', '1');
			$ListHeader->appendChild($TmpNode);

			if (count($section['items'])>0)
			{
				foreach($section['items'] as $item)
				{
					$XmlTeam=$XmlDoc->createElement('Team');
						$XmlTeam->setAttribute('Rank',$item['rank']);
						$XmlTeam->setAttribute('NationCode',$item['countryCode']);
						$XmlTeam->setAttribute('Nation',$item['countryName']. ($item['subteam']!=0 ? ' (' . $item['subteam'] . ')' :''));
						$XmlTeam->setAttribute('Quanti',count($item['athletes']));
						$XmlTeam->setAttribute('Total',$item['score']);
						$XmlTeam->setAttribute('Gold',$item['gold']);
						$XmlTeam->setAttribute('Xnine',$item['xnine']);
					$ListHeader->appendChild($XmlTeam);

					if (count($item['athletes'])>0)
					{
						foreach ($item['athletes'] as $ath)
						{
							$XmlAthlete=$XmlDoc->createElement('Athlete');
								$XmlAthlete->setAttribute('Name', $ath['athlete']);
								$XmlAthlete->setAttribute('Division',$ath['div']);
								$XmlAthlete->setAttribute('AgeClass',$ath['ageclass']);
								$XmlAthlete->setAttribute('Class',$ath['class']);
								$XmlAthlete->setAttribute('SubClass',$ath['subclass']);
								$XmlAthlete->setAttribute('QuScore',$ath['quscore']);
							$XmlTeam->appendChild($XmlAthlete);
						}
					}
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