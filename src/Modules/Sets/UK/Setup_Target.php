<?php
/*
Common Setup for "Target" Archery
*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType, $SubRule);

// default SubClasses
//if($SubRule==2) {
//	CreateSubClass($TourId, 1, 'S1', 'Junior Gentlemen Under 18');
//	CreateSubClass($TourId, 2, 'S2', 'Junior Ladies Under 18');
//	CreateSubClass($TourId, 3, 'S3', 'Junior Gentlemen Under 16');
//	CreateSubClass($TourId, 4, 'S4', 'Junior Ladies Under 16');
//	CreateSubClass($TourId, 5, 'S5', 'Junior Gentlemen Under 14');
//	CreateSubClass($TourId, 6, 'S6', 'Junior Ladies Under 14');
//	CreateSubClass($TourId, 7, 'S7', 'Junior Gentlemen Under 12');
//	CreateSubClass($TourId, 8, 'S8', 'Junior Ladies Under 12');
//}

// default Classes
CreateStandardClasses($TourId, $SubRule);

// default Distances
switch($TourType) {
	case 1:
	case 4:
		switch($SubRule) {
			case '1':
				CreateDistance($TourId, $TourType, '_M', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_W', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_JM', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_JW', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_CM', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_CW', '60 m', '50 m', '40 m', '30 m');
				CreateDistance($TourId, $TourType, '_MM', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_MW', '60 m', '50 m', '40 m', '30 m');
				break;
			case '2':
				CreateDistance($TourId, $TourType, '_M', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_W', '70 m', '60 m', '50 m', '30 m');
				break;
			case '3':
				CreateDistance($TourId, $TourType, '_M', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_W', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_JM', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_JW', '70 m', '60 m', '50 m', '30 m');
				break;
			case '4':
				CreateDistance($TourId, $TourType, '_JM', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_JW', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_CM', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '_CW', '60 m', '50 m', '40 m', '30 m');
				break;
		}
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
		switch($SubRule) {
			case '1':
				CreateDistance($TourId, $TourType, 'RM', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'RW', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'RJ_', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'RC_', '60m-1', '60m-2');
				CreateDistance($TourId, $TourType, 'RM_', '60m-1', '60m-2');
				break;
			case '2':
			case '3':
				CreateDistance($TourId, $TourType, 'R%', '70m-1', '70m-2');
				break;
			case '4':
				CreateDistance($TourId, $TourType, 'RJ_', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'RC_', '60m-1', '60m-2');
				break;
		}
		CreateDistance($TourId, $TourType, 'C%', '50m-1', '50m-2');
		break;
	case 5:
		CreateDistance($TourId, $TourType, '%', '60 m', '50 m', '40 m');
		break;
	case 6:
		CreateDistance($TourId, $TourType, '%', '18m-1', '18m-2');
		break;
	case 7:
		CreateDistance($TourId, $TourType, '%', '25m-1', '25m-2');
		break;
	case 8:
		CreateDistance($TourId, $TourType, '%', '25m-1', '25m-2', '18m-1', '18m-2');
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
CreateDistanceInformation($TourId, $DistanceInfoArray, 16);

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
//	'ToIocCode'	=> $tourDetIocCode,
	);
UpdateTourDetails($TourId, $tourDetails);

?>