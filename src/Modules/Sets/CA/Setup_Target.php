<?php
/*
Common Setup for "Target" Archery
*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $SubRule);

// default SubClasses
CreateSubClass($TourId, 1, '00', '00');

// default Classes
CreateStandardClasses($TourId, $SubRule);

// default Distances
switch($TourType) {
	case 1:
	case 4:
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, '_M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_JM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_JW', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_CM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_CW', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_MM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_MW', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_B_', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
				CreateDistanceNew($TourId, $TourType, '_P_', array(array('30 m',30), array('25 m',25), array('25 m',25), array('20 m',20)));
				CreateDistanceNew($TourId, $TourType, '_W_', array(array('20 m',20), array('15 m',15), array('15 m',15), array('10 m',10)));
				break;
			case '2':
			case '5':
				CreateDistanceNew($TourId, $TourType, '_M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, '_M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_JM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_JW', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				break;
			case '4':
				CreateDistanceNew($TourId, $TourType, '_JM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_JW', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_CM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_CW', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				break;
		}
		break;
	case 2:
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, '_M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_JM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_JW', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_CM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_CW', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_MM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_MW', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_B_', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20), array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
				CreateDistanceNew($TourId, $TourType, '_P_', array(array('30 m',30), array('25 m',25), array('25 m',25), array('20 m',20), array('30 m',30), array('25 m',25), array('25 m',25), array('20 m',20)));
				CreateDistanceNew($TourId, $TourType, '_W_', array(array('20 m',20), array('15 m',15), array('15 m',15), array('10 m',10), array('20 m',20), array('15 m',15), array('15 m',15), array('10 m',10)));
				break;
			case '2':
			case '5':
				CreateDistanceNew($TourId, $TourType, '_M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, '_M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_JM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_JW', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				break;
			case '4':
				CreateDistanceNew($TourId, $TourType, '_JM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_JW', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_CM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_CW', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				break;
		}
		break;
	case 18:
		CreateDistanceNew($TourId, $TourType, 'C%', array(array('50m-1',50), array('50m-2',50), array('-',0), array('-',0)));
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, 'RM',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RW',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RMM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RMW', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				break;
			case '2':
			case '5':
				CreateDistanceNew($TourId, $TourType, 'RM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RW', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, 'RM',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RW',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				break;
			case '4':
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				break;
		}
		break;
	case 3:
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, 'RM', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'RW', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'RJ_', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'RC_', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'RM_', array(array('60m-1',60), array('60m-2',60)));
				break;
			case '2':
			case '3':
			case '5':
				CreateDistanceNew($TourId, $TourType, 'R%', array(array('70m-1',70), array('70m-2',70)));
				break;
			case '4':
				CreateDistanceNew($TourId, $TourType, 'RJ_', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'RC_', array(array('60m-1',60), array('60m-2',60)));
				break;
		}
		CreateDistanceNew($TourId, $TourType, 'C%', array(array('50m-1',50), array('50m-2',50)));
		break;
	case 5:
		CreateDistanceNew($TourId, $TourType, '__', array(array('55 m',55), array('45 m',45), array('35 m',35)));
		CreateDistanceNew($TourId, $TourType, '_J_', array(array('55 m',55), array('45 m',45), array('35 m',35)));
		CreateDistanceNew($TourId, $TourType, '_C_', array(array('55 m',55), array('45 m',45), array('35 m',35)));
		CreateDistanceNew($TourId, $TourType, '_M_', array(array('55 m',55), array('45 m',45), array('35 m',35)));
		CreateDistanceNew($TourId, $TourType, '_B_', array(array('45 m',45), array('35 m',35), array('25 m',25)));
		CreateDistanceNew($TourId, $TourType, '_P_', array(array('30 m',30), array('25 m',25), array('20 m',20)));
		break;
	case 6:
		CreateDistanceNew($TourId, $TourType, '%', array(array('18m-1',18), array('18m-2',18)));
		break;
	case 7:
		CreateDistanceNew($TourId, $TourType, '%', array(array('25m-1',25), array('25m-2',25)));
		break;
	case 8:
		CreateDistanceNew($TourId, $TourType, '%', ray(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
		break;
}

if($TourType<5 or $TourType==6 or $TourType==18) {
	// default Events
	CreateStandardEvents($TourId, $SubRule, $TourType!=6);

	// Classes in Events
	InsertStandardEvents($TourId, $SubRule, $TourType!=6);

	// Finals & TeamFinals
	CreateFinals($TourId);
}

// Default Target
switch($TourType) {
	case 1:
	case 4:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 10, 80);
		// optional target faces
		CreateTargetFace($TourId, 2, '~Option1', '%', '',  5, 122, 5, 122, 5, 80,  5, 80);
		CreateTargetFace($TourId, 3, '~Option2', '%', '',  5, 122, 5, 122, 9, 80, 10, 80);
		break;
	case 2:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 10, 80, 5, 122, 5, 122, 5, 80, 10, 80);
		// optional target faces
		CreateTargetFace($TourId, 2, '~Option1', '%', '',  5, 122, 5, 122, 5, 80,  5, 80,  5, 122, 5, 122, 5, 80,  5, 80);
		CreateTargetFace($TourId, 3, '~Option2', '%', '',  5, 122, 5, 122, 9, 80, 10, 80,  5, 122, 5, 122, 9, 80, 10, 80);
		break;
	case 18:
		CreateTargetFace($TourId, 1, '~Default', 'R%', '1', 5, 122, 5, 122, 5, 80, 10, 80);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 9, 80, 9, 80);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Option1', 'R%', '',  5, 122, 5, 122, 5, 80,  5, 80);
		CreateTargetFace($TourId, 4, '~Option2', 'R%', '',  5, 122, 5, 122, 9, 80, 10, 80);
		break;
	case 3:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1',  9, 80, 9, 80);
		break;
	case 5:
		CreateTargetFace($TourId, 1, '~Default', '%', '1',  5, 122, 5, 122, 5, 122);
		break;
	case 6:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 2, 40, 2, 40);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 4, 40, 4, 40);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Option1', 'R%', '',  1, 40, 1, 40);
		break;
	case 7:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 2, 60, 2, 60);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 4, 60, 4, 60);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Option1', 'R%', '',  1, 60, 1, 60);
		break;
	case 8:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 2, 60, 2, 60, 2, 40, 2, 40);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 4, 60, 4, 60, 4, 40, 4, 40);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Option1', 'R%', '',  1, 60, 1, 60,  1, 40, 1, 40);
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
	'ToPrintPaper' => 1,
	'ToCurrency' => '$'
	);
UpdateTourDetails($TourId, $tourDetails);

?>
