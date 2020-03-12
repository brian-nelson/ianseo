<?php
$version='2017-05-01 00:00:00';

/* Ramon Keller 2017-05-01: Added Indoor 25m */
$AllowedTypes=array(1,2,3,6,7,9,11);

$SetType['CH']['descr']=get_text('Setup-CH', 'Install');
$SetType['CH']['types']=array();
$SetType['CH']['rules']=array();

foreach($AllowedTypes as $val) {
	switch($val) {
		case 9:
			$SetType['CH']['types']["$val"]=get_text('TrgField');
			$SetType['CH']['rules']["$val"]=array(
				'Set12',
				'Set16',
				'Set20',
				'Set24',
				'Set12+12',
				'Set16+16',
				'Set20+20',
				'Set24+24');
		break;

		case 11:
			$SetType['CH']['types']["$val"]=get_text('Type_3D', 'Tournament');
			$SetType['CH']['rules']["$val"]=array(
				'Set24',
				'Set28',
				'Set32',
				'Set24+24');
			break;
		default:
			$SetType['CH']['types']["$val"]=($val!=9 ? $TourTypes[$val] : get_text('TrgField'));

	}
}


