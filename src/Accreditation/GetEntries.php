<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');

	$SORT=' Printed, FirstName, Name ';
	require_once('CommonCard.php');

	$xmlDoc=new DOMDocument('1.0','UTF-8');
	$xmlRoot=$xmlDoc->createElement('response');
	$xmlDoc->appendChild($xmlRoot);

	$q=safe_r_sql($MyQuery);
	while($r=safe_fetch($q)) {
		$xmlRule=$xmlDoc->createElement('entry');
		$xmlRule->setAttribute('id', $r->EnId);
		$xmlRule->setAttribute('option', "$r->FirstName $r->Name ($r->DivCode$r->ClassCode)");
		$xmlRule->setAttribute('style', $r->Printed?'green':'red');
		$xmlRoot->appendChild($xmlRule);
	}

	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);

	print $xmlDoc->saveXML();
