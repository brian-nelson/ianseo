<?php
require_once('Common/Fun_Modules.php');
$version='2021-05-03 23:12:00';

$AllowedTypes=array(1,2,3,4,5,6,7,8,9,10,11,12,13,34,35,37);

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

?>