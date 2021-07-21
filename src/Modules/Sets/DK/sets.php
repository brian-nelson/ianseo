<?php
require_once('Common/Fun_Modules.php');
$version = '2020-01-01 00:00:00';

$AllowedTypes=array(1,3,5,37,6,7);

$SetType['DK']['descr']=get_text('Setup-DK', 'Install');
$SetType['DK']['types']=array();
$SetType['DK']['rules']=array();

foreach($AllowedTypes as $val) {
	$SetType['DK']['types']["$val"]=$TourTypes[$val];
}

foreach($AllowedTypes as $val) {
    if($val==3 OR $val==6) {
        $SetType['DK']['rules'][$val] = array(
            'SetAllClass',
            'SetSeniorClass',
            'SetMasterClass',
            'SetKidClass'
        );
    }
}

/* No subrules requests as of today */