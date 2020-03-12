<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/Obj_RankFactory.php');

$isEvent = (isset($_REQUEST['isEvent']) && is_numeric($_REQUEST['isEvent']) ? $_REQUEST['isEvent'] : null);
$numPlaces = (isset($_REQUEST['numPlaces']) && is_numeric($_REQUEST['numPlaces']) ? $_REQUEST['numPlaces'] : 0);
$evtList = (isset($_REQUEST['evtList']) && strlen($_REQUEST['evtList']) ? explode('|',$_REQUEST['evtList']) : array());
$serverDate=(isset($_REQUEST['serverDate']) ? $_REQUEST['serverDate'] : 0);
$viewI=(isset($_REQUEST['viewInd']) ? $_REQUEST['viewInd'] : 0);
$viewSnap=(isset($_REQUEST['viewSnap']) ? $_REQUEST['viewSnap'] : 0);
$comparedTo=(isset($_REQUEST['comparedTo']) ? $_REQUEST['comparedTo'] : 0);
$viewT=(isset($_REQUEST['viewTeam']) ? $_REQUEST['viewTeam'] : 0);
if (empty($_SESSION['TourId']) && (!CheckTourSession() || is_null($isEvent)))
	exit;

checkACL(AclSpeaker, AclReadOnly, false);

$optionsI=array('dist'=>0);
$optionsT=array();
$family='';
if($isEvent)
{
	$family='Abs';
	foreach($evtList as $event)
	{
		list($team,$ev)=preg_split("/[_]/",$event);
		if($team)
			$optionsT['events'][]=$ev;
		else
			$optionsI['events'][]=$ev;
	}
}
else
{
	$family='DivClass';
	foreach($evtList as $event)
	{
		list($team,$cl,$div)=preg_split("/[_]/",$event);
		if($team)
			$optionsT['events'][]=$div.$cl;
		else
			$optionsI['events'][]=$div.$cl;
	}

}
if($numPlaces)
{
	$optionsI['cutRank']=$numPlaces;
	$optionsT['cutRank']=$numPlaces;
}

if($comparedTo)
{
	$optionsI['comparedTo']=$comparedTo;
	$optionsT['comparedTo']=$comparedTo;
}

$query="SELECT UNIX_TIMESTAMP('".date('Y-m-d H:i:s')."') AS serverDate ";
$rs=safe_r_sql($query);

$row=safe_fetch($rs);
$xml = '<serverDate>' . $row->serverDate . '</serverDate>' . "\n";

$rankDataI=array();
$rankDataT=array();
$lastUpdate=0;


if($viewI && (!empty($optionsI['events']) || empty($optionsT['events']))) {
	if($viewSnap)
		$optionsI['subFamily']=$family;
	$rank=Obj_RankFactory::create($viewSnap ? 'Snapshot' : $family, $optionsI);
	$rank->read();
	$rankDataI=$rank->getData();
	$lastUpdate=strtotime($rankDataI['meta']['lastUpdate']);
	//print '<pre>';print_r($rankDataI);print '</pre>';exit;
} else
	$viewI=false;

if($viewT && (!empty($optionsT['events']) || empty($optionsI['events']))) {
	$rank=Obj_RankFactory::create($family.'Team',$optionsT);
	$rank->read();
	$rankDataT=$rank->getData();
	//print '<pre>';print_r($rankDataT);print '</pre>';exit;
	if($rankDataT['meta']['lastUpdate']!='0000-00-00 00:00:00')
	{
		if($lastUpdate!=0)
			$lastUpdate=max($lastUpdate,strtotime($rankDataT['meta']['lastUpdate']));
		else
			$lastUpdate=strtotime($rankDataT['meta']['lastUpdate']);
	}
} else
	$viewT=false;

if($viewI && $lastUpdate>=$serverDate)
{
	foreach($rankDataI['sections'] as $keySection => $dataSection)
	{
		$SectLastUpdate=(empty($dataSection['meta']['lastUpdate']) ? $lastUpdate : strtotime($dataSection['meta']['lastUpdate']));
		foreach($dataSection['items'] as $keyItem => $dataItem)
		{
			$xml .= '<s>'
			. '<st>0</st>'
			. '<sc>' . $dataSection['meta']['event'] . '</sc>'
			. '<sn>' . $dataSection['meta']['descr'] . '</sn>'
			. '<slu>' . $SectLastUpdate . '</slu>'
			. '<id><![CDATA[' . $dataItem['id'] . ']]></id>'
			. '<itgt><![CDATA[' . $dataItem['target'] . ']]></itgt>'
			. '<irk><![CDATA[' . $dataItem['rank'] . ']]></irk>'
			. '<oldrk><![CDATA[' . (!empty($dataItem['oldRank']) ?  $dataItem['oldRank'] : '') . ']]></oldrk>'
			. '<ia><![CDATA[' . $dataItem['athlete'] . ']]></ia>'
			. '<icn><![CDATA[' . $dataItem['countryCode'] . ' - ' . $dataItem['countryName']. ']]></icn>'
			. '<is><![CDATA[' . ($viewSnap ? $dataItem['scoreSnap'] . '<div class="SnapScore"> ('.$dataItem['score'].')</div>' : $dataItem['score']) . ']]></is>'
			. '<isg><![CDATA[' . $dataItem['gold'] . ']]></isg>'
			. '<isx><![CDATA[' . $dataItem['xnine'] . ']]></isx>'
			. '<ish><![CDATA[' . ($viewSnap ? $dataSection['meta']['snapArrows'] : $dataItem['hits']) . ']]></ish>'
			. '<isnote><![CDATA[';
			if(empty($dataSection['meta']['running'])) {
				if(!$viewSnap) $xml .= $isEvent ? ($dataItem['so'] ? get_text('ShotOffShort', 'Tournament') : ($dataItem['ct']>1 ? get_text('CoinTossShort', 'Tournament') : '')) : '';
			}
			$xml .= ']]></isnote></s>';
		}
	}
}

if($viewT && $lastUpdate>=$serverDate)
{
	foreach($rankDataT['sections'] as $keySection => $dataSection)
	{
		$SectLastUpdate=(empty($dataSection['meta']['lastUpdate']) ? $lastUpdate : strtotime($dataSection['meta']['lastUpdate']));
		foreach($dataSection['items'] as $keyItem => $dataItem) {
			$xml .= '<s>'
			. '<st>1</st>'
			. '<sc>' . $dataSection['meta']['event'] . '</sc>'
			. '<sn>' . $dataSection['meta']['descr'] . '</sn>'
			. '<slu>' . $SectLastUpdate . '</slu>'
			. '<id><![CDATA[' . $dataItem['id'] . ']]></id>'
			. '<itgt><![CDATA[' . get_text('Team') . ']]></itgt>'
			. '<irk><![CDATA[' . $dataItem['rank'] . ']]></irk>'
			. '<oldrk><![CDATA[' . '0' . ']]></oldrk>'
			. '<ia><![CDATA[' . $dataItem['countryCode'] . ' - ' . $dataItem['countryName'] . ']]></ia>'
			. '<icn><![CDATA[' . $dataItem['countryName'] . ']]></icn>'
			. '<is><![CDATA[' . $dataItem['score'] . ']]></is>'
			. '<isg><![CDATA[' . $dataItem['gold'] . ']]></isg>'
			. '<isx><![CDATA[' . $dataItem['xnine'] . ']]></isx>'
			. '<ish><![CDATA[' . $dataItem['hits'] . ']]></ish>'
			. '<isnote><![CDATA[';
			if(empty($dataSection['meta']['running'])) {
				$xml .= $isEvent ? ($dataItem['so'] ? get_text('ShotOffShort', 'Tournament') : ($dataItem['ct']>1 ? get_text('CoinTossShort', 'Tournament') : '')) : '';
			}
			$xml .= ']]></isnote></s>';
		}
	}
}




header('Content-Type: text/xml');

print '<response>' . "\n";

print '<error>0</error>' . "\n";

print $xml;

print '</response>' . "\n";



?>