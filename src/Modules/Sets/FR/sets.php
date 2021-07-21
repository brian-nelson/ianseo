<?php
require_once('Common/Fun_Modules.php');
$version='2017-11-23 18:13:00';

//$AllowedTypes=array(1,2,3,4,5,6,7,8,9,10,11,12,13,18);
$AllowedTypes=array(3, 6, 7, 8);

$SetType['FR']['descr']=get_text('Setup-FR', 'Install');
$SetType['FR']['types']=array();
$SetType['FR']['rules']=array();

foreach($AllowedTypes as $val) {
	$SetType['FR']['types']["$val"]=$TourTypes[$val];
}

// BUILD ONE PER TIME... When finished we can group
// 18m have championships
$SetType['FR']['rules']["6"][0]='SetAllClass';
$SetType['FR']['rules']["6"][1]='SetFRChampionshipSen';
$SetType['FR']['rules']["6"][2]='SetFRChampionshipJun';

// 70m round have several championship
$SetType['FR']['rules']["3"]["0"]='SetAllClass'; // done
$SetType['FR']['rules']["3"]["1"]='SetFRChampsTNJ'; // done
$SetType['FR']['rules']["3"]["2"]='SetFRChampionshipJun'; // done
//$SetType['FR']['rules']["3"][3]='SetFRChampsScratchR'; // deprecated
//$SetType['FR']['rules']["3"][4]='SetFRChampsScratchC'; // deprecated
//$SetType['FR']['rules']["3"][5]='SetFRChampsVet'; // deprecated
$SetType['FR']['rules']["3"]["9"]='SetFRTAE'; // done
$SetType['FR']['rules']["3"]["10"]='SetFRCoupeFrance'; // done
$SetType['FR']['rules']["3"]["6"]='SetFRChampsD1DNAP'; // done
//$SetType['FR']['rules']["3"][7]='SetFRChampsFederal'; // deprecated
$SetType['FR']['rules']["3"]["8"]='SetFRFinDRD2'; // done
