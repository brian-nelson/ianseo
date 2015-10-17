<?php
$version='2011-10-07 17:33:00';

$AllowedTypes=array(6,7,8);

$SetType['UK']['descr']=get_text('Setup-UK', 'Install');
$SetType['UK']['types']=array();
$SetType['UK']['rules']=array();

foreach($AllowedTypes as $val) {
	$SetType['UK']['types']["$val"]=$TourTypes[$val];
	$SetType['UK']['rules']["$val"]=array(
		'SetUkNationals',
		'SetUkJunNationals',
	);
}

