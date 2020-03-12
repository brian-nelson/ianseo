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
$family='ElimInd';

if($evtList) {
	$options['eventsR']=array();
	foreach($evtList as $k=>$v) {
		$options['eventsR'][]=$k;
	}
} else {
	$options['eventsR']=array('@'.($isEvent ? 2 : 1));
}

if($numPlaces)
{
	$options['cutRank']=$numPlaces;
}

if($comparedTo)
{
	$options['comparedTo']=$comparedTo;
}

$query="SELECT UNIX_TIMESTAMP('".date('Y-m-d H:i:s')."') AS serverDate ";
$rs=safe_r_sql($query);

$row=safe_fetch($rs);
$xml = '<serverDate>' . $row->serverDate . '</serverDate>' . "\n";

$rankDataI=array();
$rankDataT=array();
$lastUpdate=0;


$rank=Obj_RankFactory::create($viewSnap ? 'Snapshot' : $family, $options);
$rank->read();
$rankDataI=$rank->getData();
$lastUpdate=strtotime($rankDataI['meta']['lastUpdate']);

if($lastUpdate>=$serverDate)
{
	foreach($rankDataI['sections'] as $keySection => $dataSection)
	{
		foreach($dataSection['items'] as $keyItem => $dataItem)
		{
			$xml .= '<s>'
			. '<st>0</st>'
			. '<sc>' . $dataSection['meta']['event'] . '</sc>'
			. '<sn>' . $dataSection['meta']['descr'] . '</sn>'
			. '<slu>' . $lastUpdate . '</slu>'
			. '<id><![CDATA[' . $dataItem['id'] . ']]></id>'
			. '<itgt><![CDATA[' . $dataItem['target'] . ']]></itgt>'
			. '<irk><![CDATA[' . $dataItem['rank'] . ']]></irk>'
			. '<oldrk><![CDATA[' . $dataItem['rank'] . ']]></oldrk>'
			. '<ia><![CDATA[' . $dataItem['athlete'] . ']]></ia>'
			. '<icn><![CDATA[' . $dataItem['countryCode'] . ' - ' . $dataItem['countryName']. ']]></icn>'
			. '<is><![CDATA[' . ($viewSnap ? $dataItem['scoreSnap'] . '<div class="SnapScore"> ('.$dataItem['score'].')</div>' : $dataItem['score']) . ']]></is>'
			. '<isg><![CDATA[' . $dataItem['gold'] . ']]></isg>'
			. '<isx><![CDATA[' . $dataItem['xnine'] . ']]></isx>'
			. '<ish><![CDATA[' . ($viewSnap ? $dataSection['meta']['snapArrows'] : $dataItem['hits']) . ']]></ish>'
			. '<isnote><![CDATA[';
			if(!$viewSnap) $xml .= $isEvent ? ($dataItem['so'] ? get_text('ShotOffShort', 'Tournament') : ($dataItem['ct']>1 ? get_text('CoinTossShort', 'Tournament') : '')) : '';
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