<?php
$version='2011-05-13 08:13:00';

$AllowedTypes=array(1,2,3,4,5,6,7,8,18,37);

// prepare the available sets
$SetType['PAR']['descr']=get_text('Setup-PAR', 'Install');
$SetType['PAR']['types']=array();
$SetType['PAR']['rules']=array();

foreach($AllowedTypes as $val) {
	$SetType['PAR']['types'][$val]=$TourTypes[$val];
}
foreach(array(3, 6, 37) as $val) {
    $SetType['PAR']['rules'][$val]=array(
        'SetStandard',
        'SetIPCandWA',
    );
}