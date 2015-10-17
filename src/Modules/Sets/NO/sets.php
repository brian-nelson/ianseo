<?php
$version='2011-05-13 08:13:00';

$AllowedTypes=array(1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 17, 18);

$SetType['NO']['descr']=get_text('Setup-NO', 'Install');
$SetType['NO']['types']=array();
$SetType['NO']['rules']=array();


foreach($AllowedTypes as $val) {
	$SetType['NO']['types']["$val"]=$TourTypes[$val];
	$SetType['NO']['rules']["$val"]=array(
		'SetOrdinary',
		'SetOrdinaryFinals',
		);
	switch($val) {
		case '3':
			$SetType['NO']['rules']["$val"][]='NorgesRunden';
			$SetType['NO']['rules']["$val"][]='NorgesRundenFinals';
		case '6':
			$SetType['NO']['rules']["$val"][]='SetChampionship';
			break;
		case '5':
			$SetType['NO']['rules']["$val"][]='NorskKortrunde';
			$SetType['NO']['rules']["$val"][]='NorskKortrundeFinals';
			break;
		case '9':
		case '17':
			$SetType['NO']['types']["$val"]=get_text($val=='9' ? 'Type_NorField' : 'Type_NorH', 'Tournament');
			$SetType['NO']['rules']["$val"]=array(
				'Set12',
				'Set16',
				'Set20',
				'Set24',
				'Set12+12',
				'Set16+16',
				'Set20+20',
				'Set24+24',
				'SetChampionship'
				);
			break;
		case '11':
			$SetType['NO']['types']['11']=get_text('Type_3D', 'Tournament');
			$SetType['NO']['rules']['11']=array(
				'Set3D10',
				'Set3D12',
				'Set3D20',
				'Set3D10+10',
				'Set3D12+12',
				'Set3D20+20',
				'SetChampionship'
				);
			break;
	}
}

unset($SetType['NO']['rules']["18"]);
