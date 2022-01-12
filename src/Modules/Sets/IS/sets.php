<?php
$version='2016-05-29 08:13:00';

$AllowedTypes=array(3,6);

//$SetType['IS']['descr']=get_text('Setup-IS', 'Install');
$SetType['IS']['descr']='Icelandic Tournament Rules/ Íslenskar Reglur';
$SetType['IS']['types']=array();
$SetType['IS']['rules']=array();

foreach($AllowedTypes as $val) {
	$SetType['IS']['types']["$val"]=$TourTypes[$val];
	$SetType['IS']['rules']["$val"]=array(
		'SetChampionship',
		'SetAduClass',
		'SetOneClass',
		'SetAllClass',
		'SetKidClass',
		'SetWAPools-All',
		);
		
		// 'SetUkNationals' Heitir National Championships, 'SetUkJunNationals' heitir Junior National Championships
}
