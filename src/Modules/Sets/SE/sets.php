<?php
$version='2011-10-06 08:48:00';

$AllowedTypes=array(1,3,37,5,39,22,6,7,12,10,11);

$SetType['SE']['descr']=get_text('Setup-SE', 'Install');
$SetType['SE']['types']=array();
$SetType['SE']['rules']=array();

foreach($AllowedTypes as $val) {
	$SetType['SE']['types']["$val"]=$TourTypes[$val];
}

$SetType['SE']['rules']["11"]=array(
		'Set1Dist1Arrow',
		'Set1Dist2Arrow',
);

/*
$SetType['SE']['rules'][20]=array(
	'Set2x15',
	'Set1x30',
	'SetChampionship',
);
*/