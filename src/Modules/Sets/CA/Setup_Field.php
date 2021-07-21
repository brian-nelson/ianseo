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
		CreateDistanceNew($TourId, $TourType, '%', array(array('Course',0,0)));
		break;
	case 10:
	case 12:
		CreateDistanceNew($TourId, $TourType, '%', array(array('Unmarked',0), array('Marked',0)));
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
		CreateTargetFace($TourId, 1, 'Yellow Peg', 'REG-^BC', '1', 6, 0);
		CreateTargetFace($TourId, 2, 'Blue Peg', 'REG-^(B[^C]|[CR]C)', '1', 6, 0);
		CreateTargetFace($TourId, 3, 'Red Peg', 'REG-^[CR][^C]', '1', 6, 0);
		break;
	case 10:
	case 12:
		CreateTargetFace($TourId, 1, 'Yellow Peg', 'REG-^BC', '1', 6, 0, 6, 0);
		CreateTargetFace($TourId, 2, 'Blue Peg', 'REG-^(B[^C]|[CR]C)', '1', 6, 0, 6, 0);
		CreateTargetFace($TourId, 3, 'Red Peg', 'REG-^[CR][^C]', '1', 6, 0, 6, 0);
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
	'ToIocCode'	=> $tourDetIocCode,
	);
UpdateTourDetails($TourId, $tourDetails);

?>
