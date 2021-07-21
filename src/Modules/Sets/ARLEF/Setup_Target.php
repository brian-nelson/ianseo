<?php
/*
Common Setup for "Target" Archery
*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// $tourDetIocCode='BEL';
$tourDetIocCode='';

// default Divisions
CreateStandardDivisions($TourId, $TourType, $SubRule);

// default SubClasses
// CreateSubClass($TourId, 1, '00', '00');

// default Classes
CreateStandardClasses($TourId, $TourType, $SubRule);

// default Distances
switch($TourType) {
	case 1:
	case 4:
		CreateDistanceNew($TourId, $TourType, '_HJ', array(array('90 m',90), array('70 m',70), array('50 m', 50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_H1', array(array('90 m',90), array('70 m',70), array('50 m', 50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_H2', array(array('90 m',90), array('70 m',70), array('50 m', 50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'C_H', array(array('90 m',90), array('70 m',70), array('50 m', 50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'R_H', array(array('90 m',90), array('70 m',70), array('50 m', 50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_DJ', array(array('70 m',70), array('60 m',60), array('50 m', 50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_D1', array(array('70 m',70), array('60 m',60), array('50 m', 50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_D2', array(array('70 m',70), array('60 m',60), array('50 m', 50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_HM', array(array('70 m',70), array('60 m',60), array('50 m', 50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_HV', array(array('70 m',70), array('60 m',60), array('50 m', 50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_HC', array(array('70 m',70), array('60 m',60), array('50 m', 50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'C_D', array(array('70 m',70), array('60 m',60), array('50 m', 50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'R_D', array(array('70 m',70), array('60 m',60), array('50 m', 50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_DM', array(array('60 m',60), array('50 m',50), array('40 m', 40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_DV', array(array('60 m',60), array('50 m',50), array('40 m', 40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_DC', array(array('60 m',60), array('50 m',50), array('40 m', 40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_MB', array(array('50 m',50), array('40 m',40), array('30 m', 30), array('20 m',20)));
		CreateDistanceNew($TourId, $TourType, 'VI%',  array(array('Bl. 60',30), array('Bl. 80',30), array('Bl. 80', 30), array('Bl. 122', 30)));
		break;
	case 2:
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, '_M', array(array('90 m', 90), array('70 m',70), array('50 m', 50), array('30 m', 30), array('90 m', 90), array('70 m',70), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, '_W', array(array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30), array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, '_JM', array(array('90 m', 90), array('70 m',70), array('50 m', 50), array('30 m', 30), array('90 m', 90), array('70 m',70), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, '_JW', array(array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30), array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, '_CM', array(array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30), array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, '_CW', array(array('60 m', 60), array('50 m',50), array('40 m', 40), array('30 m', 30), array('60 m', 60), array('50 m',50), array('40 m', 40), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, '_MM', array(array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30), array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, '_MW', array(array('60 m', 60), array('50 m',50), array('40 m', 40), array('30 m', 30), array('60 m', 60), array('50 m',50), array('40 m', 40), array('30 m', 30)));
				break;
			case '2':
			case '5':
				CreateDistanceNew($TourId, $TourType, '_M', array(array('90 m', 90), array('70 m',70), array('50 m', 50), array('30 m', 30), array('90 m', 90), array('70 m',70), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, '_W', array(array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30), array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, '_M', array(array('90 m', 90), array('70 m',70), array('50 m', 50), array('30 m', 30), array('90 m', 90), array('70 m',70), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, '_W', array(array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30), array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, '_JM', array(array('90 m', 90), array('70 m',70), array('50 m', 50), array('30 m', 30), array('90 m', 90), array('70 m',70), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, '_JW', array(array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30), array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30)));
				break;
			case '4':
				CreateDistanceNew($TourId, $TourType, '_JM', array(array('90 m', 90), array('70 m',70), array('50 m', 50), array('30 m', 30), array('90 m', 90), array('70 m',70), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, '_JW', array(array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30), array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, '_CM', array(array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30), array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, '_CW', array(array('60 m', 60), array('50 m',50), array('40 m', 40), array('30 m', 30), array('60 m', 60), array('50 m',50), array('40 m', 40), array('30 m', 30)));
				break;
		}
		break;
	case 18:
		CreateDistanceNew($TourId, $TourType, 'C%', array(array('50m-1', 50), array('50m-2',50), array('-',0), array('-',0)));
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, 'RM', array(array('90 m', 90), array('70 m',70), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, 'RW', array(array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90 m', 90), array('70 m',70), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60 m', 60), array('50 m',50), array('40 m', 40), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, 'RMM', array(array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, 'RMW', array(array('60 m', 60), array('50 m',50), array('40 m', 40), array('30 m', 30)));
				break;
			case '2':
			case '5':
				CreateDistanceNew($TourId, $TourType, 'RM', array(array('90 m', 90), array('70 m',70), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, 'RW', array(array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, 'RM', array(array('90 m', 90), array('70 m',70), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, 'RW', array(array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90 m', 90), array('70 m',70), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30)));
				break;
			case '4':
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90 m', 90), array('70 m',70), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70 m', 70), array('60 m',60), array('50 m', 50), array('30 m', 30)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60 m', 60), array('50 m',50), array('40 m', 40), array('30 m', 30)));
				break;
		}
		break;
	case 3:
		CreateDistanceNew($TourId, $TourType, 'R_1', array(array('70m-1', 70), array('70m-2', 70)));
		CreateDistanceNew($TourId, $TourType, 'R_2', array(array('70m-1', 70), array('70m-2', 70)));
		CreateDistanceNew($TourId, $TourType, 'R_J', array(array('70m-1', 70), array('70m-2', 70)));
		CreateDistanceNew($TourId, $TourType, 'R_H', array(array('70m-1', 70), array('70m-2', 70)));
		CreateDistanceNew($TourId, $TourType, 'R_D', array(array('70m-1', 70), array('70m-2', 70)));
		CreateDistanceNew($TourId, $TourType, 'R_C', array(array('60m-1', 60), array('60m-2', 60)));
		CreateDistanceNew($TourId, $TourType, 'R_M', array(array('60m-1', 60), array('60m-2', 60)));
		CreateDistanceNew($TourId, $TourType, 'R_V', array(array('60m-1', 60), array('60m-2', 60)));
		CreateDistanceNew($TourId, $TourType, 'R_B', array(array('40m-1', 40), array('40m-2', 40)));
		CreateDistanceNew($TourId, $TourType, '_MP', array(array('20m-1', 20), array('20m-2', 20)));
		CreateDistanceNew($TourId, $TourType, 'C_1', array(array('50m-1', 50), array('50m-2', 50)));
		CreateDistanceNew($TourId, $TourType, 'C_2', array(array('50m-1', 50), array('50m-2', 50)));
		CreateDistanceNew($TourId, $TourType, 'C_J', array(array('50m-1', 50), array('50m-2', 50)));
		CreateDistanceNew($TourId, $TourType, 'C_C', array(array('50m-1', 50), array('50m-2', 50)));
		CreateDistanceNew($TourId, $TourType, 'C_M', array(array('50m-1', 50), array('50m-2', 50)));
		CreateDistanceNew($TourId, $TourType, 'C_V', array(array('50m-1', 50), array('50m-2', 50)));
		CreateDistanceNew($TourId, $TourType, 'C_B', array(array('50m-1', 50), array('50m-2', 50)));
		CreateDistanceNew($TourId, $TourType, 'C_H', array(array('50m-1', 50), array('50m-2', 50)));
		CreateDistanceNew($TourId, $TourType, 'C_D', array(array('50m-1', 50), array('50m-2', 50)));
		CreateDistanceNew($TourId, $TourType, 'VI%', array(array('30m-1', 30), array('30m-2', 30)));
		break;
	case 5:
		CreateDistanceNew($TourId, $TourType, '%', array(array('60 m', 60), array('50 m', 50), array('40 m',40)));
		break;
	case 6:
	case 7:
		$dist1=($TourType==6 ? '18' : '25');
		$dist2=($TourType==6 ? '10' : '15');
		if($SubRule==1) {
			CreateDistanceNew($TourId, $TourType, 'B_', array(array($dist2.'m-1', $dist1), array($dist2.'m-2', $dist2)));
			CreateDistanceNew($TourId, $TourType, '___%', array(array($dist1.'m-1', $dist1), array($dist1.'m-2', $dist2)));
		} else {
			CreateDistanceNew($TourId, $TourType, '%J', array(array($dist2.'m-1', $dist1), array($dist2.'m-2', $dist2)));
			CreateDistanceNew($TourId, $TourType, '%S', array(array($dist1.'m-1', $dist1), array($dist1.'m-2', $dist2)));
		}
		break;
	case 8:
		CreateDistanceNew($TourId, $TourType, '%', array(array('25m-1', 25), array('25m-2', 25), array('18m-1',18), array('18m-2',18)));
		break;
	case 23:
		CreateDistanceNew($TourId, $TourType, '%', array(array('25m-1', 25), array('25m-2', 25)));
		break;
	case 24:
		CreateDistanceNew($TourId, $TourType, '_MB', array(array('30m', 30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'NL%', array(array('30m', 30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'NR%', array(array('50m', 50), array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'C%D%', array(array('50m', 50), array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'C%H%', array(array('50m', 50), array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'R%D%', array(array('50m', 50), array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'R%H%', array(array('50m', 50), array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'VI%', array(array('20m', 20), array('20m',20)));
		break;
	case 25:
		CreateDistanceNew($TourId, $TourType, '_MP', array(array('25m-1', 25), array('25m-2',25)));
		CreateDistanceNew($TourId, $TourType, '_MB', array(array('50m-1', 50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'NL%', array(array('25m-1', 25), array('25m-2',25)));
		CreateDistanceNew($TourId, $TourType, 'NR%', array(array('50m-1', 50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'C%D%', array(array('50m-1', 50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'C%H%', array(array('50m-1', 50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'R%D%', array(array('50m-1', 50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'R%H%', array(array('50m-1', 50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'VI%', array(array('20m-1', 20), array('20m-2',20)));
		break;
	case 26:
		CreateDistanceNew($TourId, $TourType, '%', array(array('10m-1',10), array('10m-2',10)));
		break;
	case 27:
		CreateDistanceNew($TourId, $TourType, '%', array(array('15m-1',15), array('15m-2',15)));
		break;
	case 28:
		CreateDistanceNew($TourId, $TourType, '%', array(array('25m-1',25), array('25m-2',25)));
		break;
	case 29:
		CreateDistanceNew($TourId, $TourType, '%', array(array('30m-1',30), array('20m-2',30)));
		break;
	case 30:
		CreateDistanceNew($TourId, $TourType, '%', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
		break;
}

if(in_array($TourType, array(1,3,6,7,23,24,25)) and $SubRule==1) {
	// default Events
	CreateStandardEvents($TourId, $TourType);

	// Classes in Events
	InsertStandardEvents($TourId, $TourType);

	// Finals & TeamFinals
	CreateFinals($TourId);
}

// Default Target
$i=1;
switch($TourType) {
	case 1:
	case 4:
		CreateTargetFace($TourId, $i++, 'Tous grands', '%', '1',  5, 122, 5, 122, 5, 80,  5, 80);
		CreateTargetFace($TourId, $i++, '30m réduit', '%', '', 5, 122, 5, 122, 5, 80, 9, 80);
		CreateTargetFace($TourId, $i++, 'courtes réduit', '%', '',  5, 122, 5, 122, 9, 80, 9, 80);
		break;
	case 2:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 10, 80, 5, 122, 5, 122, 5, 80, 10, 80);
		// optional target faces
		CreateTargetFace($TourId, 2, '~Option1', '%', '',  5, 122, 5, 122, 5, 80,  5, 80,  5, 122, 5, 122, 5, 80,  5, 80);
		CreateTargetFace($TourId, 3, '~Option2', '%', '',  5, 122, 5, 122, 9, 80, 10, 80,  5, 122, 5, 122, 9, 80, 10, 80);
		break;
	case 3:
		CreateTargetFace($TourId, $i++, '122cm', 'REG-^[RV]', '1',  5, 122, 5, 122); // outdoor complete
		CreateTargetFace($TourId, $i++, '80cm', 'REG-^C', '1',  9, 80, 9, 80); // outdoor 5-10
		break;
	case 5:
		CreateTargetFace($TourId, 1, '~Default', '%', '1',  5, 122, 5, 122, 5, 122);
		break;
	case 6:
	case 7:
		$tgt1=($TourType==6 ? 40 : 60);
		$tgt2=($TourType==6 ? 60 : 80);
		$tgt3=($TourType==6 ? 80 : 122);
		if($SubRule==1) {
			// C e Brev C
			CreateTargetFace($TourId, $i++, 'Trispot C', 'C%', '1',  4, $tgt1, 4, $tgt1);
			CreateTargetFace($TourId, $i++, $tgt3.'cm C', 'BCMP', '1',  3, $tgt3, 3, $tgt3);
			CreateTargetFace($TourId, $i++, $tgt2.'cm C', 'REG-(^C[DH]B)|(^CMP)|(^BC[DH])', '1',  3, $tgt2, 3, $tgt2);
			CreateTargetFace($TourId, $i++, $tgt1.'cm C', 'C%', '',  3, $tgt1, 3, $tgt1);

			// R e Brev R, NR e NL
			CreateTargetFace($TourId, $i++, $tgt1.'cm R', 'REG-^R[DH][^B]|RS|R2|NR', '1',  1, $tgt1, 1, $tgt1);
			CreateTargetFace($TourId, $i++, 'Trispot R', 'REG-^R[DH][^B]|RS|R2', '', 2, $tgt1, 2, $tgt1);
			CreateTargetFace($TourId, $i++, $tgt3.'cm R', 'REG-(^BRMP)|BN|BB|BA|BV', '1',  1, $tgt3, 1, $tgt3);
			CreateTargetFace($TourId, $i++, $tgt2.'cm R', 'REG-(^R[DH]B)|^RMP|^(BR[DH])|NL|VI', '1',  1, $tgt2, 1, $tgt2);
		} else {
			CreateTargetFace($TourId, $i++, $tgt1.'cm', '_S', '1',  1, $tgt1, 1, $tgt1);
			CreateTargetFace($TourId, $i++, $tgt2.'cm', '_J', '1',  1, $tgt2, 1, $tgt2);
		}
		break;
	case 8:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 2, 60, 2, 60, 2, 40, 2, 40);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 4, 60, 4, 60, 4, 40, 4, 40);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Option1', 'R%', '',  1, 60, 1, 60,  1, 40, 1, 40);
		break;
	case 18:
		CreateTargetFace($TourId, 1, '~Default', 'R%', '1', 5, 122, 5, 122, 5, 80, 10, 80);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 9, 80, 9, 80);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Option1', 'R%', '',  5, 122, 5, 122, 5, 80,  5, 80);
		CreateTargetFace($TourId, 4, '~Option2', 'R%', '',  5, 122, 5, 122, 9, 80, 10, 80);
		break;
	case 23:
		CreateTargetFace($TourId, $i++, '60cm', 'REG-^([CR][^M])|(NR)', '1', 5, 60, 5, 60); // outdoor complete
		CreateTargetFace($TourId, $i++, '80cm', 'REG-^CMB|RMB|NL|VI', '1',  5, 80, 5, 80); // outdoor complete
		CreateTargetFace($TourId, $i++, '60cm Trispot', 'REG-^C.[^B]', '', 2, 60, 2, 60); // indoor recurve 6-10
		break;
	case 24:
		CreateTargetFace($TourId, $i++, '80cm', '%', '1', 5, 80, 5, 80); // outdoor complete
		CreateTargetFace($TourId, $i++, '80cm-C', 'C%', '', 9, 80, 9, 80); // outdoor complete
		break;
	case 25:
		CreateTargetFace($TourId, $i++, '122cm', 'REG-^(..P|NL|R|VI)', '1', 5, 122, 5, 122); // outdoor complete
		CreateTargetFace($TourId, $i++, '80cm', 'REG-^(CMB|NR)', '1',  5, 80, 5, 80); // outdoor complete
		CreateTargetFace($TourId, $i++, '80cm-C', 'REG-^C.[^BP]', '1', 9, 80, 9, 80); // outdoor recurve 5-10
		break;
	case 26:
		CreateTargetFace($TourId, $i++, '80cm', '%', '1',  5, 80, 5, 80); // outdoor complete
		break;
	case 27:
		CreateTargetFace($TourId, $i++, '80cm', '%', '1',  5, 80, 5, 80); // outdoor complete
		break;
	case 28:
		CreateTargetFace($TourId, $i++, '122cm', 'REG-[BP]$', '1',  5, 122, 5, 122); // outdoor complete
		CreateTargetFace($TourId, $i++, '80cm', 'REG-[^BP]$', '1',  5, 80, 5, 80); // outdoor complete
		break;
	case 29:
		CreateTargetFace($TourId, $i++, '122cm', 'REG-^([CR]B[BP])|(LB)', '1',  5, 122, 5, 122); // outdoor complete
		CreateTargetFace($TourId, $i++, '80cm', 'REG-^[RCN]B[^BP]', '1',  5, 80, 5, 80); // outdoor complete
		break;
	case 30:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
		break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 24, 4);

// Update Tour details
$tourDetails=array(
	'ToCollation' => $tourCollation,
	'ToTypeName' => $tourDetTypeName,
	'ToNumDist' => $tourDetNumDist,
	'ToNumEnds' => $tourDetNumEnds,
	'ToMaxDistScore' => $tourDetMaxDistScore,
	'ToMaxFinIndScore' => $tourDetMaxFinIndScore,
	'ToMaxFinTeamScore' => $tourDetMaxFinTeamScore,
	'ToCategory' => $tourDetCategory,
	'ToElabTeam' => $tourDetElabTeam,
	'ToElimination' => $tourDetElimination,
	'ToGolds' => $tourDetGolds,
	'ToXNine' => $tourDetXNine,
	'ToGoldsChars' => $tourDetGoldsChars,
	'ToXNineChars' => $tourDetXNineChars,
	'ToDouble' => $tourDetDouble,
	'ToIocCode'	=> $tourDetIocCode,
	);
UpdateTourDetails($TourId, $tourDetails);

?>
