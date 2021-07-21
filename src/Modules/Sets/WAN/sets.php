<?php
require_once('Common/Fun_Modules.php');
$version = '2019-11-09 14:32:00';

$AllowedTypes=array(3);

$SetType['WAN']['descr']=get_text('Setup-WAN', 'Install');
$SetType['WAN']['types']=array();
$SetType['WAN']['rules']=array();

foreach($AllowedTypes as $val) {
	$SetType['WAN']['types']["$val"]=$TourTypes[$val];
}

$SetType['WAN']['rules']["3"] = array(
    'NordicYouthChamp',
);

