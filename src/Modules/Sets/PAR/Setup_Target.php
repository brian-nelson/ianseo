<?php
/*

Common setup for Target

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId);

// default SubClasses
CreateSubClass($TourId, 1, '00', '00');

// default Classes
CreateStandardClasses($TourId, $SubRule);

// default Distances
switch($TourType) {
	case 1:
	case 4:
		CreateDistance($TourId, $TourType, '_M', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, '_W', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'W1%',  '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'VI%',  '60cm face', '80cm face', '80cm face', '122cm face');
		break;
	case 18:
		CreateDistance($TourId, $TourType, 'RM', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'RW', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'VI%',  '60cm face', '80cm face', '80cm face', '122cm face');
		CreateDistance($TourId, $TourType, 'C_',  '50m-1', '50m-2', '-', '-');
		CreateDistance($TourId, $TourType, 'W1%',  '50m-1', '50m-2', '-', '-');
		break;
	case 2:
		CreateDistance($TourId, $TourType, '_M', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, '_W', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'W1%',  '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'VI%',  '60cm face', '80cm face', '80cm face', '122cm face', '60cm face', '80cm face', '80cm face', '122cm face');
		break;
	case 3:
		CreateDistance($TourId, $TourType, 'C%', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'W1%', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'R%', '70m-1', '70m-2');
		CreateDistance($TourId, $TourType, 'VI%', '30m-1', '30m-2');
		break;
	case 5:
		CreateDistance($TourId, $TourType, 'C%', '60 m', '50 m', '40 m');
		CreateDistance($TourId, $TourType, 'W1%', '60 m', '50 m', '40 m');
		CreateDistance($TourId, $TourType, 'R%', '60 m', '50 m', '40 m');
		CreateDistance($TourId, $TourType, 'VI%', '30m-1', '30m-2', '30m-3');
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
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
		CreateTargetFace($TourId, 2, '~DefaultVI', 'VI%', '1', 5, 60, 5, 80, 5, 80, 5, 122);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Option1', '%', '',  5, 122, 5, 122, 9, 80, 9, 80);
		break;
	case 18:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
		CreateTargetFace($TourId, 2, '~DefaultVI', 'VI%', '1', 5, 60, 5, 80, 5, 80, 5, 122);
		CreateTargetFace($TourId, 3, '~DefaultCO', 'C%', '1',  9, 80, 9, 80, 0, 0, 0, 0);
		CreateTargetFace($TourId, 4, '~DefaultCO', 'W1%', '1',  9, 80, 9, 80, 0, 0, 0, 0);
		// optional target faces
		CreateTargetFace($TourId, 5, '~Option1', 'R%', '',  5, 122, 5, 122, 9, 80, 9, 80);
		break;
	case 2:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
		CreateTargetFace($TourId, 2, '~DefaultVI', 'VI%', '1', 5, 60, 5, 80, 5, 80, 5, 122, 5, 60, 5, 80, 5, 80, 5, 122);
		// optional target faces
		CreateTargetFace($TourId, 3, 'Option1', '%', '',  5, 122, 5, 122, 9, 80, 9, 80,  5, 122, 5, 122, 9, 80, 9, 80);
		break;
	case 3:
		CreateTargetFace($TourId, 1, '~Default', 'R%', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 9, 80, 9, 80);
		CreateTargetFace($TourId, 3, '~DefaultCO', 'W1%', '1', 9, 80, 9, 80);
		CreateTargetFace($TourId, 4, '~DefaultVI', 'VI%', '1', 5, 80, 5, 80);
		break;
	case 5:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122, 5, 122);
		CreateTargetFace($TourId, 2, '~DefaultVI', 'VI%', '1', 5, 80, 5, 80, 5, 80);
		break;
	case 6:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 2, 40, 2, 40);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 4, 40, 4, 40);
		CreateTargetFace($TourId, 3, '~DefaultCO', 'W1%', '1', 4, 40, 4, 40);
		CreateTargetFace($TourId, 4, '~DefaultVI', 'VI%', '1', 1, 60, 1, 60);
		// optional target faces
		CreateTargetFace($TourId, 5, '~Option1', 'R%', '',  1, 40, 1, 40);
		break;
	case 7:
		CreateTargetFace($TourId, 1, '~Default', 'R%', '1', 2, 60, 2, 60);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 4, 60, 4, 60);
		CreateTargetFace($TourId, 3, '~DefaultCO', 'W1%', '1', 4, 60, 4, 60);
		CreateTargetFace($TourId, 4, '~DefaultVI', 'VI%', '1', 1, 80, 1, 80);
		// optional target faces
		CreateTargetFace($TourId, 5, '~Option1', 'R%', '',  1, 60, 1, 60);
		break;
	case 8:
		CreateTargetFace($TourId, 1, '~Default', 'R%', '1', 2, 60, 2, 60, 2, 40, 2, 40);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 4, 60, 4, 60, 4, 40, 4, 40);
		CreateTargetFace($TourId, 3, '~DefaultCO', 'W1%', '1', 4, 60, 4, 60, 4, 40, 4, 40);
		CreateTargetFace($TourId, 4, '~DefaultVI', 'VI%', '1', 1, 80, 1, 80, 1, 60, 1, 60);
		// optional target faces
		CreateTargetFace($TourId, 5, '~Option1', 'R%', '',  1, 60, 1, 60,  1, 40, 1, 40);
		break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 32, 2);

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