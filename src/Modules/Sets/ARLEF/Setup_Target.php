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
		CreateDistance($TourId, $TourType, '_HJ', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, '_H1', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, '_H2', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'C_H', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'R_H', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, '_DJ', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, '_D1', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, '_D2', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, '_HM', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, '_HV', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, '_HC', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'C_D', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'R_D', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, '_DM', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, '_DV', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, '_DC', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, '_MB', '50 m', '40 m', '30 m', '20 m');
		CreateDistance($TourId, $TourType, 'VI%',  'Bl. 60', 'Bl. 80', 'Bl. 80', 'Bl. 122');
		break;
	case 2:
		switch($SubRule) {
			case '1':
				CreateDistance($TourId, $TourType, '_M', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_W', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_JM', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_JW', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_CM', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_CW', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
				CreateDistance($TourId, $TourType, '_MM', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_MW', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
				break;
			case '2':
			case '5':
				CreateDistance($TourId, $TourType, '_M', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_W', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				break;
			case '3':
				CreateDistance($TourId, $TourType, '_M', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_W', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_JM', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_JW', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				break;
			case '4':
				CreateDistance($TourId, $TourType, '_JM', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_JW', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_CM', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_CW', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
				break;
		}
		break;
	case 18:
		CreateDistance($TourId, $TourType, 'C%', '50m-1', '50m-2', '-', '-');
		switch($SubRule) {
			case '1':
				CreateDistance($TourId, $TourType, 'RM', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'RW', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'RJM', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'RJW', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'RCM', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'RCW', '60 m', '50 m', '40 m', '30 m');
				CreateDistance($TourId, $TourType, 'RMM', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'RMW', '60 m', '50 m', '40 m', '30 m');
				break;
			case '2':
			case '5':
				CreateDistance($TourId, $TourType, 'RM', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'RW', '70 m', '60 m', '50 m', '30 m');
				break;
			case '3':
				CreateDistance($TourId, $TourType, 'RM', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'RW', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'RJM', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'RJW', '70 m', '60 m', '50 m', '30 m');
				break;
			case '4':
				CreateDistance($TourId, $TourType, 'RJM', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'RJW', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'RCM', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'RCW', '60 m', '50 m', '40 m', '30 m');
				break;
		}
		break;
	case 3:
		CreateDistance($TourId, $TourType, 'R_1', '70m-1', '70m-2');
		CreateDistance($TourId, $TourType, 'R_2', '70m-1', '70m-2');
		CreateDistance($TourId, $TourType, 'R_J', '70m-1', '70m-2');
		CreateDistance($TourId, $TourType, 'R_H', '70m-1', '70m-2');
		CreateDistance($TourId, $TourType, 'R_D', '70m-1', '70m-2');
		CreateDistance($TourId, $TourType, 'R_C', '60m-1', '60m-2');
		CreateDistance($TourId, $TourType, 'R_M', '60m-1', '60m-2');
		CreateDistance($TourId, $TourType, 'R_V', '60m-1', '60m-2');
		CreateDistance($TourId, $TourType, 'R_B', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, '_MP', '20m-1', '20m-2');
		CreateDistance($TourId, $TourType, 'C_1', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'C_2', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'C_J', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'C_C', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'C_M', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'C_V', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'C_B', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'C_H', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'C_D', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'VI%', '30m-1', '30m-2');
		break;
	case 5:
		CreateDistance($TourId, $TourType, '%', '60 m', '50 m', '40 m');
		break;
	case 6:
	case 7:
		$dist1=($TourType==6 ? '18m' : '25m');
		$dist2=($TourType==6 ? '10m' : '15m');
		if($SubRule==1) {
			CreateDistance($TourId, $TourType, 'B_', $dist2.'-1', $dist2.'-2');
			CreateDistance($TourId, $TourType, '___%', $dist1.'-1', $dist1.'-2');
		} else {
			CreateDistance($TourId, $TourType, '%J', $dist2.'-1', $dist2.'-2');
			CreateDistance($TourId, $TourType, '%S', $dist1.'-1', $dist1.'-2');
// 			debug_svela($TourType);
		}
		break;
	case 8:
		CreateDistance($TourId, $TourType, '%', '25m-1', '25m-2', '18m-1', '18m-2');
		break;
	case 23:
		CreateDistance($TourId, $TourType, '%', '25m-1', '25m-2');
		break;
	case 24:
		CreateDistance($TourId, $TourType, '_MB', '30m', '20m');
		CreateDistance($TourId, $TourType, 'NL%', '30m', '20m');
		CreateDistance($TourId, $TourType, 'NR%', '50m', '30m');
		CreateDistance($TourId, $TourType, 'C%D%', '50m', '30m');
		CreateDistance($TourId, $TourType, 'C%H%', '50m', '30m');
		CreateDistance($TourId, $TourType, 'R%D%', '50m', '30m');
		CreateDistance($TourId, $TourType, 'R%H%', '50m', '30m');
		CreateDistance($TourId, $TourType, 'VI%', '20m', '20m');
		break;
	case 25:
		CreateDistance($TourId, $TourType, '_MP', '25m-1', '25m-2');
		CreateDistance($TourId, $TourType, '_MB', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'NL%', '25m-1', '25m-2');
		CreateDistance($TourId, $TourType, 'NR%', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'C%D%', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'C%H%', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'R%D%', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'R%H%', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'VI%', '20m-1', '20m-2');
		break;
	case 26:
		CreateDistance($TourId, $TourType, '%', '10m-1', '10m-2');
		break;
	case 27:
		CreateDistance($TourId, $TourType, '%', '15m-1', '15m-2');
		break;
	case 28:
		CreateDistance($TourId, $TourType, '%', '25m-1', '25m-2');
		break;
	case 29:
		CreateDistance($TourId, $TourType, '%', '30m', '20m');
		break;
	case 30:
		CreateDistance($TourId, $TourType, '%', '50m', '40m', '30m', '20m');
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