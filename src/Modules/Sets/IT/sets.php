<?php
$version='2011-05-13 08:13:00';

$AllowedTypes=array(1,2,3,4,5,6,7,8,9,10,11,12,13,15,16,18,19,31);

$SetType['IT']['descr']=get_text('Setup-IT', 'Install');
$SetType['IT']['types']=array();
$SetType['IT']['rules']=array();

foreach($AllowedTypes as $val) {
	$SetType['IT']['types']["$val"]=$TourTypes[$val];
	$SetType['IT']['rules']["$val"]=array(
		'SetAllClass',
		'SetOneClass',
		'SetKidClass',
		'SetAduClass',
		);
}

unset($SetType['IT']['rules']["11"]);
unset($SetType['IT']['rules']["13"]);
unset($SetType['IT']['rules']["15"]);
unset($SetType['IT']['rules']["16"]);
unset($SetType['IT']['rules']["19"]);
unset($SetType['IT']['rules']["31"]);

$SetType['IT']['rules']["9"]=array(
	'Set1Elim',
	'SetCR',
	'Set2Elim',
	'SetNoElim',
	);
$SetType['IT']['rules']["10"]=array(
	'Set1Elim',
	'SetCR',
	'Set2Elim',
	'SetNoElim',
);
$SetType['IT']['rules']["12"]=array(
	'Set1Elim',
	'SetCR',
	'Set2Elim',
	'SetNoElim',
	);
?>