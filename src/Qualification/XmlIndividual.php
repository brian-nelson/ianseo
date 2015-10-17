<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');

if (!CheckTourSession())
{
	print get_text('CrackError');
	exit;
}

$ToFit=(isset($_REQUEST['ToFitarco']) ? $_REQUEST['ToFitarco'] : null);

$XmlDoc = new DOMDocument('1.0', 'UTF-8');

$TmpNode = $XmlDoc->createProcessingInstruction ("xml-stylesheet", 'type="text/xsl" href="/Common/Styles/StyleIndividual.xsl" ');
$XmlDoc->appendChild($TmpNode);

$XmlRoot = $XmlDoc->createElement('Results');
$XmlRoot->setAttribute('IANSEO', ProgramVersion);
$XmlRoot->setAttribute('TS', date('Y-m-d H:i:s'));
$XmlDoc->appendChild($XmlRoot);

$ListHeader = NULL;

$options=array('dist'=>0);
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

$family='DivClass';

$rank=Obj_RankFactory::create($family,$options);
$rank->read();
$rankData=$rank->getData();

if(count($rankData['sections']))
{
	foreach($rankData['sections'] as $section)
	{
		$ListHeader = $XmlDoc->createElement('List');
		$ListHeader->setAttribute('Title', get_text($section['meta']['descr'],'','',true));
		$ListHeader->setAttribute('Columns', '10' + $section['meta']['numDist']);
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
		$TmpNode->setAttribute('Name', 'Sub Class');
		$TmpNode->setAttribute('Columns', '2');
		$ListHeader->appendChild($TmpNode);

		$TmpNode = $XmlDoc->createElement('Caption',$section['meta']['fields']['countryName']);
		$TmpNode->setAttribute('Name', 'Nation');
		$TmpNode->setAttribute('Columns', '2');
		$ListHeader->appendChild($TmpNode);

		for ($i=1;$i<=$section['meta']['numDist'];++$i)
		{
			$TmpNode=$XmlDoc->createElement('Caption',$section['meta']['fields']['dist_'. $i]);
			$TmpNode->setAttribute('Name','Distance' . $i);
			$TmpNode->setAttribute('Columns',1);
			$ListHeader->appendChild($TmpNode);
		}

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

		foreach($section['items'] as $item)
		{
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

			$Element = $XmlDoc->createElement('Item', ($item['class']!=$item['ageclass'] ?  $item['ageclass'] : ''));
			$Element->setAttribute('Name', 'Age Class');
			$Athlete->appendChild($Element);

			$Element = $XmlDoc->createElement('Item', $item['subclass']);
			$Element->setAttribute('Name', 'Sub Class');
			$Athlete->appendChild($Element);

			$Element = $XmlDoc->createElement('Item', $item['countryCode']);
			$Element->setAttribute('Name', 'Nation Code');
			$Athlete->appendChild($Element);

			$Element = $XmlDoc->createElement('Item', $item['countryName']);
			$Element->setAttribute('Name', 'Nation');
			$Athlete->appendChild($Element);


			for($i=1; $i<=$section['meta']['numDist'];$i++)
			{
				list($rank,$score)=explode('|',$item['dist_' . $i]);
				$Element = $XmlDoc->createElement('Item', $score . "/" . str_pad($rank,2,"0",STR_PAD_LEFT));
				$Element->setAttribute('Name', 'Score'.$i);
				$Athlete->appendChild($Element);
			}
			$Element = $XmlDoc->createElement('Item', $item['score']);
			$Element->setAttribute('Name', 'Total');
			$Athlete->appendChild($Element);

			$Element = $XmlDoc->createElement('Item', $item['gold']);
			$Element->setAttribute('Name', 'Gold');
			$Athlete->appendChild($Element);

			$Element = $XmlDoc->createElement('Item', $item['xnine']);
			$Element->setAttribute('Name', 'Xnine');
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
?>