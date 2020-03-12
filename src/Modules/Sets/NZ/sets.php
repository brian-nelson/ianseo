<?php
$version='2017-03-18 20:53:00';

$AllowedTypes=array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 34, 35);

$SetType['NZ']['descr']=get_text('Setup-NZ', 'Install');
$SetType['NZ']['types']=array();
$SetType['NZ']['rules']=array();

foreach($AllowedTypes as $val) {
	$SetType['NZ']['types']["$val"]=$TourTypes[$val];
	$SetType['NZ']['rules']["$val"]=array(
		'SetNZAllClasses',
		'SetNZWAClasses',
		'SetNZJunClasses',
		);
}

