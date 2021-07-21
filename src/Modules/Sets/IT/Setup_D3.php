<?php
/*

COMMON SETUP 3D

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType);

// default SubClasses
CreateStandardSubClasses($TourId);

// default Classes
CreateStandardClasses($TourId, $SubRule, '3D'); //

// default Distances
switch($TourType) {
	case 11:
		CreateDistanceNew($TourId, $TourType, '%', array(array('Perc.',0)));
		break;
	case 13:
		CreateDistanceNew($TourId, $TourType, '%', array(array('Perc. 1',0), array('Perc. 2',0)));
		break;
}

// default Events
CreateStandard3DEvents($TourId, $SubRule);

// insert class in events
InsertStandard3DEvents($TourId, $SubRule);

// Elimination rounds
InsertStandard3DEliminations($TourId, $SubRule);

// Finals & TeamFinals
CreateFinals($TourId);

// Default Target
switch($TourType) {
	case 11:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 8, 0);
		break;
	case 13:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 8, 0, 8, 0);
		break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, $tourDetNumEnds, 6);

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
