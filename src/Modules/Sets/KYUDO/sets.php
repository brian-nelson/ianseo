<?php
$version='2020-11-29 10:00:01';

$AllowedTypes=array(45);

$SetType['KYUDO']['descr']=get_text('Setup-Kyudo', 'Install');
$SetType['KYUDO']['types']=array();
$SetType['KYUDO']['rules']=array();

foreach($AllowedTypes as $val) {
	$SetType['KYUDO']['types'][$val]=$TourTypes[$val];
}

