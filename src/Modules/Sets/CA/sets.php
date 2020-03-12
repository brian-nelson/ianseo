<?php
$version='2014-08-05 08:13:00';

// $AllowedTypes=array(1,2,3,4,5,6,7,8,9,10,11,12,13,18);
$AllowedTypes=array(1,2,3,4,5,6,7,8,9,10,11,12,13,18);

$SetType['CA']['descr']=get_text('Setup-CA', 'Install');
$SetType['CA']['types']=array();
$SetType['CA']['rules']=array();

foreach($AllowedTypes as $val) {
	$SetType['CA']['types']["$val"]=$TourTypes[$val];
}

// FITA, 2x FITA, 1/2 FITA, 70m Round, 18m
foreach(array(1, 2, 3, 4, 6, 18) as $val) {
	$SetType['CA']['rules']["$val"]=array(
		'SetAllClass',
		'SetChampionship',
// 		'SetJ-SClass',
// 		'SetJ-CClass',
		);
}

/*

// HF (all 3 types)
foreach(array(9, 10, 12) as $val) {
	$SetType['CA']['rules']["$val"]=array(
		'SetAllClass',
// 		'SetJ-SClass',
		);
}

// 3D (both types)
foreach(array(11, 13) as $val) {
	$SetType['CA']['rules']["$val"]=array(
		'SetAllClass',
// 		'SetOneClass',
		);
}

*/