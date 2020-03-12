<?php
$version='2012-01-24 15:16:00';

$AllowedTypes=array(3,6);

$SetType['NL']['descr']=get_text('Setup-NL', 'Install');
$SetType['NL']['types']=array();
$SetType['NL']['rules']=array();

foreach($AllowedTypes as $val) {
	$SetType['NL']['types']["$val"]=$TourTypes[$val];
}

$SetType['NL']['rules']["6"]=array(
	'SetOneClass',
	'SetChampionship'
);
$SetType['NL']['rules']["3"]=array(
	'SetOneClass',
	'LooserBrackets'	
);
