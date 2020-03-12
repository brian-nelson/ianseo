<?php
require_once('Common/Fun_Modules.php');
$version='2013-03-09 14:32:00';

$AllowedTypes=array(1,3,6,7,23,24,25,26,27,28,29,30);

$SetType['ARLEF']['descr']=get_text('Setup-ARLEF', 'Install');
$SetType['ARLEF']['types']=array();
$SetType['ARLEF']['rules']=array();


foreach($AllowedTypes as $val) {
	$SetType['ARLEF']['types']["$val"]=$TourTypes["$val"];
}

$SetType['ARLEF']['rules']["6"]=array(
		'SetAllClass',
		'SetKermesse',
		);
$SetType['ARLEF']['rules']["7"]=array(
		'SetAllClass',
		'SetKermesse',
);
