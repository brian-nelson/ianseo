<?php
/*

Common setup for Field

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, 'FIELD');

// default SubClasses
CreateSubClass($TourId, 1, '00', '00');

// default Classes
CreateStandardFieldClasses($TourId, $SubRule);

// default Distances
switch($TourType) {
	case 9:
		CreateDistance($TourId, $TourType, '%', 'Course');
		break;
	case 10:
	case 12:
		CreateDistance($TourId, $TourType, '%', 'Unmarked', 'Marked');
		break;
}

// default Events
CreateStandardFieldEvents($TourId, $SubRule);

// insert class in events
InsertStandardFieldEvents($TourId, $SubRule);

// Elimination rounds
InsertStandardFieldEliminations($TourId, $SubRule);

// Finals & TeamFinals
CreateFinals($TourId);

// Default Target
switch($TourType) {
	case 9:
		CreateTargetFace($TourId, 1, 'Picch. Giallo', 'REG-^BC', '1', 6, 0);
		CreateTargetFace($TourId, 2, 'Picch. Blu', 'REG-^(B[^C]|[CR]C)', '1', 6, 0);
		CreateTargetFace($TourId, 3, 'Picch. Rosso', 'REG-^[CR][^C]', '1', 6, 0);
		break;
	case 10:
	case 12:
		CreateTargetFace($TourId, 1, 'Picch. Giallo', 'REG-^BC', '1', 6, 0, 6, 0);
		CreateTargetFace($TourId, 2, 'Picch. Blu', 'REG-^(B[^C]|[CR]C)', '1', 6, 0, 6, 0);
		CreateTargetFace($TourId, 3, 'Picch. Rosso', 'REG-^[CR][^C]', '1', 6, 0, 6, 0);
		break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, $tourDetNumEnds, 4);

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