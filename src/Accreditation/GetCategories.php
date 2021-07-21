<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');

$CardType=(empty($_REQUEST['CardType']) ? 'A' : $_REQUEST['CardType']);
$CardNumber=(empty($_REQUEST['CardNumber']) ? 0 : intval($_REQUEST['CardNumber']));

$FIELDS='distinct EnDivision, EnClass';
$SORTSTRICT='EnDivision, EnClass';

foreach(array('PrintNotPrinted','PrintAccredited','PrintPhoto') as $tmp) {
	if(isset($_REQUEST[$tmp])) {
		unset($_REQUEST[$tmp]);
	}
}

require_once('CommonCard.php');

$JSON=array(
	'error' =>0,
	'Divisions' => array(),
	'Classes' => array(),
);

$Divs=array();
$Clas=array();
$q=safe_r_sql($MyQuery);
while($r=safe_fetch($q)) {
	$Divs[$r->EnDivision]=array('id'=>$r->EnDivision, 'txt'=>$r->EnDivision);
	$Clas[$r->EnClass]=array('id'=>$r->EnClass, 'txt'=>$r->EnClass);
}

$JSON['Divisions']=array_values($Divs);
$JSON['Classes']=array_values($Clas);

JsonOut($JSON);
