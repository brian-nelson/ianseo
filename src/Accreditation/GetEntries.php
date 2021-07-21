<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');

if(!empty($_REQUEST['SortByTarget'])) {
	$SORT=' TargetNo, Printed, FirstName, Name ';

}
$CardType=(empty($_REQUEST['CardType']) ? 'A' : $_REQUEST['CardType']);
$CardNumber=(empty($_REQUEST['CardNumber']) ? 0 : intval($_REQUEST['CardNumber']));

require_once('CommonCard.php');

$JSON=array(
	'error'=>0,
	'reds'=>0,
	'greens'=>0,
	'Entries'=>array()
);

$q=safe_r_sql($MyQuery);
while($r=safe_fetch($q)) {
	$Event=$r->DivCode.$r->ClassCode;
	if($CardType=='I') {
		$Event=$r->EvCode;
	}
	$JSON['Entries'][]=array(
		'id'=>$r->EnId,
		'style' => $r->Printed?'green':'red',
		'text' => ($r->FirstName.$r->Name ? "$r->FirstName $r->Name" : $r->Bib)." ($Event".(empty($_REQUEST['SortByTarget']) ? '' : " - $r->TargetNo").")",
	);
	if($r->Printed) {
		$JSON['greens']++;
	} else {
		$JSON['reds']++;
	}
}

JsonOut($JSON);

function usersort($a,$b) {
	if($a['txt']>$b['txt']) return 1;
	if($a['txt']<$b['txt']) return -1;
	return 0;
}
