<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');

$CardType=(empty($_REQUEST['CardType']) ? 'A' : $_REQUEST['CardType']);
$CardNumber=(empty($_REQUEST['CardNumber']) ? 0 : intval($_REQUEST['CardNumber']));

$FIELDS='distinct CoId, CoCode, CoName, "" as Bib';
$SORTSTRICT='CoCode, CoName';

require_once('CommonCard.php');

$xmlDoc=new DOMDocument('1.0','UTF-8');
$xmlRoot=$xmlDoc->createElement('response');
$xmlDoc->appendChild($xmlRoot);

$Countries=array();
$q=safe_r_sql($MyQuery);
while($r=safe_fetch($q)) {
	if(in_array($r->CoId, $Countries)) continue;
	$xmlRule=$xmlDoc->createElement('country');
	$xmlRule->setAttribute('id', $r->CoId);
	$xmlRule->setAttribute('option', $r->CoCode.'-'.substr($r->CoName, 0, 30));
	$xmlRoot->appendChild($xmlRule);
	$Countries[]=$r->CoId;
}

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);

print $xmlDoc->saveXML();
