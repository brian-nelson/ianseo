<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');

$CardType=(empty($_REQUEST['CardType']) ? 'A' : $_REQUEST['CardType']);
$CardNumber=(empty($_REQUEST['CardNumber']) ? 0 : intval($_REQUEST['CardNumber']));

$FIELDS='distinct CoId, CoCode, CoName, "" as Bib';
$SORTSTRICT='CoCode, CoName';

foreach(array('PrintNotPrinted','PrintAccredited','PrintPhoto') as $tmp) {
	if(isset($_REQUEST[$tmp])) {
		unset($_REQUEST[$tmp]);
	}
}

require_once('CommonCard.php');

$JSON=array(
	'error' =>0,
	'Countries' => array(),
);

$Countries=array();

$q=safe_r_sql($MyQuery);
while($r=safe_fetch($q)) {
	$Countries[$r->CoId] = array('id'=>$r->CoId, 'txt' => $r->CoCode.'-'.substr($r->CoName, 0, 30));
}
$JSON['Countries']=array_values($Countries);

JsonOut($JSON);
