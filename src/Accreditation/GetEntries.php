<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');

if(!empty($_REQUEST['SortByTarget'])) {
	$SORT=' TargetNo, Printed, FirstName, Name ';

}
$CardType=(empty($_REQUEST['CardType']) ? 'A' : $_REQUEST['CardType']);
$CardNumber=(empty($_REQUEST['CardNumber']) ? 0 : intval($_REQUEST['CardNumber']));

require_once('CommonCard.php');

$xmlDoc=new DOMDocument('1.0','UTF-8');
$xmlRoot=$xmlDoc->createElement('response');
$xmlDoc->appendChild($xmlRoot);

//$xmlRoot->setAttribute('query', $MyQuery);
$Greens=0;
$Reds=0;

$q=safe_r_sql($MyQuery);
while($r=safe_fetch($q)) {
	$Event=$r->DivCode.$r->ClassCode;
	if($CardType=='I') $Event=$r->EvCode;
	$xmlRule=$xmlDoc->createElement('entry');
	$xmlRule->setAttribute('id', $r->EnId);
	$xmlRule->setAttribute('option', ($r->FirstName.$r->Name ? "$r->FirstName $r->Name" : $r->Bib)." ($Event".(empty($_REQUEST['SortByTarget']) ? '' : " - $r->TargetNo").")");
	$xmlRule->setAttribute('style', $r->Printed?'green':'red');
	$xmlRoot->appendChild($xmlRule);
	if($r->Printed) {
		$Greens++;
	} else {
		$Reds++;
	}
}
$xmlRoot->setAttribute('green', $Greens);
$xmlRoot->setAttribute('red', $Reds);

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);

print $xmlDoc->saveXML();
