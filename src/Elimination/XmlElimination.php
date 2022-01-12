<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/Obj_RankFactory.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}
	
$MaxNum=0;
if(isset($_REQUEST["MaxNum"]) && is_numeric($_REQUEST["MaxNum"]))
	$MaxNum = $_REQUEST["MaxNum"];

$ToFit=(isset($_REQUEST['ToFitarco']) ? $_REQUEST['ToFitarco'] : null);

$XmlDoc = new DOMDocument('1.0', 'UTF-8');

$TmpNode = $XmlDoc->createProcessingInstruction ("xml-stylesheet", 'type="text/xsl" href="/Common/Styles/StyleElimination.xsl" ');
$XmlDoc->appendChild($TmpNode);

$XmlRoot = $XmlDoc->createElement('Results');
$XmlRoot->setAttribute('IANSEO', ProgramVersion);
$XmlRoot->setAttribute('TS', date('Y-m-d H:i:s'));
$XmlDoc->appendChild($XmlRoot);

$ListHeader = NULL;

$options=array();
if(isset($_REQUEST["Event"]) && preg_match("/^[0-9A-Z]+$/i",$_REQUEST["Event"]))
	$options['events'] = array($_REQUEST["Event"]);

$family='ElimInd';

$rank=Obj_RankFactory::create($family,$options);
$rank->read();
$rankData=$rank->getData();

if(count($rankData['sections']))
{
	foreach($rankData['sections'] as $section)
	{
		$ListHeader = $XmlDoc->createElement('List');
		$ListHeader->setAttribute('Title', $section['meta']['descr'] . " - " . $section['meta']['round']);
		$ListHeader->setAttribute('Columns', '10');
		$XmlRoot->appendChild($ListHeader);
			
		$TmpNode = $XmlDoc->createElement('Caption',$section['meta']['fields']['rank']);
		$TmpNode->setAttribute('Name', 'Rank');
		$TmpNode->setAttribute('Columns', '1');
		$ListHeader->appendChild($TmpNode);
			
		$TmpNode = $XmlDoc->createElement('Caption',$section['meta']['fields']['athlete']);
		$TmpNode->setAttribute('Name', 'Athlete Name');
		$TmpNode->setAttribute('Columns', '2');
		$ListHeader->appendChild($TmpNode);
			
		$TmpNode = $XmlDoc->createElement('Caption',$section['meta']['fields']['class']);
		$TmpNode->setAttribute('Name', 'Class');
		$TmpNode->setAttribute('Columns', '1');
		$ListHeader->appendChild($TmpNode);
			
		$TmpNode = $XmlDoc->createElement('Caption',$section['meta']['fields']['countryName']);
		$TmpNode->setAttribute('Name', 'Nation');
		$TmpNode->setAttribute('Columns', '2');
		$ListHeader->appendChild($TmpNode);
			
		$TmpNode = $XmlDoc->createElement('Caption',$section['meta']['fields']['score']);
		$TmpNode->setAttribute('Name', 'Total');
		$TmpNode->setAttribute('Columns', '1');
		$ListHeader->appendChild($TmpNode);
	
		$TmpNode = $XmlDoc->createElement('Caption',$section['meta']['fields']['gold']);
		$TmpNode->setAttribute('Name', 'Gold');
		$TmpNode->setAttribute('Columns', '1');
		$ListHeader->appendChild($TmpNode);
	
		$TmpNode = $XmlDoc->createElement('Caption',$section['meta']['fields']['xnine']);
		$TmpNode->setAttribute('Name', 'XNine');
		$TmpNode->setAttribute('Columns', '1');
		$ListHeader->appendChild($TmpNode);
		
		$TmpNode = $XmlDoc->createElement('Caption','');
		$TmpNode->setAttribute('Name', 'SO');
		$TmpNode->setAttribute('Columns', '1');
		$ListHeader->appendChild($TmpNode);	
			
		$EndQualified = false;
		foreach($section['items'] as $item)
		{
			if($item['rank'] > $section['meta']['qualifiedNo'] && !$EndQualified)
			{
				$Athlete = $XmlDoc->createElement('Athlete');
		   		$ListHeader->appendChild($Athlete);
		   			$Element = $XmlDoc->createElement('Item');
		   				$cdata=$XmlDoc->createCDATASection(' ');
		   				$Element->appendChild($cdata);
		   			$Element->setAttribute('Name', 'Separator');
		   			$Element->setAttribute('Columns', 10);
		   			$Athlete->appendChild($Element);

				$EndQualified = true;
			}
			
			$Athlete = $XmlDoc->createElement('Athlete');
			$ListHeader->appendChild($Athlete);
		
			$Element = $XmlDoc->createElement('Item', $item['rank']);
			$Element->setAttribute('Name', 'Rank');
			$Athlete->appendChild($Element);
		
			$Element = $XmlDoc->createElement('Item', $item['target']);
			$Element->setAttribute('Name', 'Target No');
			$Athlete->appendChild($Element);
	
			$Element = $XmlDoc->createElement('Item', $item['athlete']);
			$Element->setAttribute('Name', 'Athlete Name');
			$Athlete->appendChild($Element);
			
			$Element = $XmlDoc->createElement('Item', $item['class'] . ($item['class']!=$item['ageclass'] ?  ' ' . ($item['ageclass']) : ''));
			$Element->setAttribute('Name', 'Class');
			$Athlete->appendChild($Element);
		
			$Element = $XmlDoc->createElement('Item', $item['countryCode']);
			$Element->setAttribute('Name', 'Nation Code');
			$Athlete->appendChild($Element);

			$Element = $XmlDoc->createElement('Item', $item['countryName']);
			$Element->setAttribute('Name', 'Nation');
			$Athlete->appendChild($Element);
			
			$Element = $XmlDoc->createElement('Item', $item['score']);
			$Element->setAttribute('Name', 'Total');
			$Athlete->appendChild($Element);

			$Element = $XmlDoc->createElement('Item', $item['gold']);
			$Element->setAttribute('Name', 'Gold');
			$Athlete->appendChild($Element);

			$Element = $XmlDoc->createElement('Item', $item['xnine']);
			$Element->setAttribute('Name', 'Xnine');
			$Athlete->appendChild($Element);
			
			//Definizione dello spareggio/Sorteggio
			$so='';
			if($item['so']>0)  //Spareggio
			{
				$tmpArr="";
				if(strlen(trim($item['tiebreak'])))
				{
					$tmpArr="-T.";
					for($countArr=0; $countArr<strlen(trim($item['tiebreak'])); $countArr++)
						$tmpArr .= DecodeFromLetter(substr(trim($item['tiebreak']),$countArr,1)) . ",";
					$tmpArr = substr($tmpArr,0,-1);
				}
				$so = get_text('ShotOffShort','Tournament') . $tmpArr;
			}
			elseif($item['ct']>1)
				$so = get_text('CoinTossShort','Tournament');
				
			$Element = $XmlDoc->createElement('Item', $so);
			$Element->setAttribute('Name', 'SO');
			$Athlete->appendChild($Element);
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