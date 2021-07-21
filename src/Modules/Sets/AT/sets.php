<?php
require_once('Common/Fun_Modules.php');
$version = '2020-01-01 00:00:00';

$AllowedTypes=array(1,3,37,5,6,7,8,9,12,10,11,13);

$SetType['AT']['descr']=get_text('Setup-AT', 'Install');
$SetType['AT']['types']=array();
$SetType['AT']['rules']=array();

foreach($AllowedTypes as $val) {
	$SetType['AT']['types']["$val"]=$TourTypes[$val];
}

foreach($AllowedTypes as $val) {
    if($val!=1 AND $val!=5) {
        $SetType['AT']['rules']["$val"] = array(
            'SetAllClass',
            'SetOneClass'
        );
    }
    if($val >= 9 and $val != 37) {
        $SetType['AT']['rules']["$val"][] = 'SetWAPools-All';
        $SetType['AT']['rules']["$val"][] = 'SetWAPools-One';
    }
}

/* No subrules requests as of today */