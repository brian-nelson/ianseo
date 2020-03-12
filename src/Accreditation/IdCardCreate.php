<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);

$CardName=(empty($_REQUEST['CardName']) ? '' : $_REQUEST['CardName']);

$xmlDoc=new DOMDocument('1.0','UTF-8');
$xmlRoot=$xmlDoc->createElement('response');
$xmlDoc->appendChild($xmlRoot);
if(empty($_REQUEST['CardType'])) {
	$xmlRoot->setAttribute('error', 'Missing Name');
} else {
	require_once('IdCardEmpty.php');
	$Function='CreateDefault'.$_REQUEST['CardType'];

	// get max number of the required type
	$max=0;
	$q=safe_r_sql("select max(IcNumber)+1 IcNextNumber from IdCards where IcTournament={$_SESSION['TourId']} and IcType=".StrSafe_DB($_REQUEST['CardType']));
	if($r=safe_fetch($q)) $max=intval($r->IcNextNumber);

	// empty name is allowed ONLY on number 0
	if(($max and !$CardName) or (!$max and !function_exists($Function))) {
		$xmlRoot->setAttribute('error', 'Missing Name');
	} else {
		if(function_exists($Function)) {
			$Function($max, $CardName);
		} else {
			safe_w_sql("insert into IdCards set
				IcTournament={$_SESSION['TourId']},
				IcType=".StrSafe_DB($_REQUEST['CardType']).",
				IcNumber=$max,
				IcName=".StrSafe_DB($CardName));
		}

		$xmlRoot->setAttribute('error', '0');
		$xmlRoot->setAttribute('page', '?CardType='.$_REQUEST['CardType'].'&CardNumber='.$max);

	}

}

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-type: text/xml; charset=' . PageEncode);

print $xmlDoc->saveXML();
