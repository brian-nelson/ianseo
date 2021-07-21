<?php
$version='2012-01-24 15:16:00';

$AllowedTypes=array(3,37,1,2,6,7,41,42,43);

$SetType['NL']['descr']=get_text('Setup-NL', 'Install');
$SetType['NL']['types']=array();
$SetType['NL']['rules']=array();

foreach($AllowedTypes as $val) {
	$SetType['NL']['types'][$val]=$TourTypes[$val];
}


$SetType['NL']['rules'][3] = array(
    'SetAllClass',
    'SetOneClass',
    'LooserBrackets'
);
$SetType['NL']['rules'][42] = array(
    'SetNLRegio25',
    'SetNLRayonBonds25'
);
$SetType['NL']['rules'][6] = array(
    'SetNLRegio',
    'SetNLRayonBonds',
    'SetAllClass',
    'LooserBrackets'
);

