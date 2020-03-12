<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');

$CardType=(empty($_REQUEST['CardType']) ? 'A' : $_REQUEST['CardType']);
$CardNumber=(empty($_REQUEST['CardNumber']) ? 0 : intval($_REQUEST['CardNumber']));

$FIELDS='distinct EnDivision, EnClass';
$SORTSTRICT='EnDivision, EnClass';

require_once('CommonCard.php');

$xmlDoc=new DOMDocument('1.0','UTF-8');
$xmlRoot=$xmlDoc->createElement('response');
$xmlDoc->appendChild($xmlRoot);

$Divs=array();
$Clas=array();
$q=safe_r_sql($MyQuery);
while($r=safe_fetch($q)) {
	if(!in_array($r->EnDivision, $Divs)) {
		$xmlRule=$xmlDoc->createElement('div');
		$xmlRule->setAttribute('id', $r->EnDivision);
		$xmlRule->setAttribute('option', $r->EnDivision);
		$xmlRoot->appendChild($xmlRule);
		$Divs[]=$r->EnDivision;
	}
	if(!in_array($r->EnClass, $Clas)) {
		$xmlRule=$xmlDoc->createElement('class');
		$xmlRule->setAttribute('id', $r->EnClass);
		$xmlRule->setAttribute('option', $r->EnClass);
		$xmlRoot->appendChild($xmlRule);
		$Clas[]=$r->EnClass;
	}
}

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);

print $xmlDoc->saveXML();
