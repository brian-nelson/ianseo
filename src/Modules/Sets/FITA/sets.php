<?php
require_once('Common/Fun_Modules.php');
$version='2011-05-13 08:13:00';

$AllowedTypes=array(1,2,3,4,5,6,7,8,9,10,11,12,13,18,37);

$SetType['default']['descr']=get_text('Setup-Default', 'Install');
$SetType['default']['types']=array();
$SetType['default']['rules']=array();

foreach($AllowedTypes as $val) {
	$SetType['default']['types']["$val"]=$TourTypes[$val];
}

// FITA, 2x FITA, 1/2 FITA, 70m Round, 18m
foreach(array(1, 2, 3, 4, 6, 18, 37) as $val) {
	$SetType['default']['rules']["$val"]=array(
		'SetAllClass',
		'SetOneClass',
		'SetJ-SClass',
		'SetJ-CClass',
		);
	if(module_exists('QuotaTournament'))
		$SetType['default']['rules']["$val"][]='QuotaTournm';
}

// HF (all 3 types)
foreach(array(9, 10, 12) as $val) {
	$SetType['default']['rules']["$val"]=array(
		'SetAllClass',
		'SetJ-SClass',
		'SetWAPools-All',
		'SetWAPools-JS',
		);
}

// 3D (both types)
foreach(array(11, 13) as $val) {
	$SetType['default']['rules']["$val"]=array(
		'SetAllClass',
		'SetOneClass',
		'SetWAPools-All',
		'SetWAPools-One',
		);
}


/*
regole FITA
* Tipo FITA, 2xFITA, 1/2FITA e 70m (1, 2, 3, 4)
- 4 subrules (tutte le classi, una sola classe, S-J e J-C)
- incluse le finali (nuove regole per OL e CO)

* tipo indoor: (6, 7, 8)
- 4 subrules (come sopra) solo per 18
- incluse finali solo per 18m

* tipo 900 round (5)
- 1 subrule (tutte le classi)
- no finali

* tipo HF (12+12 1 dist, 12+12 2 dist, 24+24 2 dist) (9, 10, 12)
- 2 subrules (S-J e Tutte le classi)
- finali (separate per classi)

* tipo 3D (1 e 2 dist) (11, 13)
- 2 subrules (S e Tutte classi)
- finali (16-8)



*** per l'italia farei "Giovanili", "Adulti","Tutte le classi"

*/
?>