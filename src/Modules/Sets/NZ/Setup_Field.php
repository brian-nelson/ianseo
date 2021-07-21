<?php
/*

Common setup for Field

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, 'FIELD');

// default SubClasses
CreateSubClass($TourId, 1, 'NZ', 'New Zealand');
CreateSubClass($TourId, 2, 'IN', 'International');
CreateSubClass($TourId, 3, 'OP', 'Open');

// default Classes
CreateStandardFieldClasses($TourId, $SubRule);

// default Distances
switch($TourType) {
	case 9:
		CreateDistanceNew($TourId, $TourType, '%', array(array('Course',0)));
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
		switch ($SubRule) {
			case '1':
				CreateTargetFace($TourId, 1, 'Red Peg', 'X%', '1', 6, 0);
				CreateTargetFace($TourId, 2, 'Blue Peg', 'BS%', '1', 6, 0);
				CreateTargetFace($TourId, 3, 'Blue Peg', 'BJ%', '1', 6, 0);
				CreateTargetFace($TourId, 4, 'Blue Peg', 'BM%', '1', 6, 0);
				CreateTargetFace($TourId, 5, 'Blue Peg', 'BV%', '1', 6, 0);
				CreateTargetFace($TourId, 6, 'Yellow Peg', 'B%', '1', 6, 0);
				CreateTargetFace($TourId, 7, 'Yellow Peg', 'L%', '1', 6, 0);
				CreateTargetFace($TourId, 8, 'Red Peg', 'RS%', '1', 6, 0);
				CreateTargetFace($TourId, 9, 'Red Peg', 'RV%', '1', 6, 0);
				CreateTargetFace($TourId, 10, 'Red Peg', 'RM%', '1', 6, 0);
				CreateTargetFace($TourId, 11, 'Red Peg', 'CS%', '1', 6, 0);
				CreateTargetFace($TourId, 12, 'Red Peg', 'CV%', '1', 6, 0);
				CreateTargetFace($TourId, 13, 'Red Peg', 'CM%', '1', 6, 0);
				CreateTargetFace($TourId, 14, 'Red Peg', '_J%', '1', 6, 0);
				CreateTargetFace($TourId, 15, 'Blue Peg', '_C%', '1', 6, 0);
				CreateTargetFace($TourId, 16, 'Blue Peg', '_I%', '1', 6, 0);
				CreateTargetFace($TourId, 17, 'Yellow Peg', '_Y%', '1', 6, 0);
				CreateTargetFace($TourId, 18, 'Yellow Peg', '_K%', '1', 6, 0);
				break;
			case '2':
				CreateTargetFace($TourId, 1, 'Red Peg', 'X%', '1', 6, 0);
				CreateTargetFace($TourId, 2, 'Blue Peg', 'BS%', '1', 6, 0);
				CreateTargetFace($TourId, 3, 'Blue Peg', 'BJ%', '1', 6, 0);
				CreateTargetFace($TourId, 4, 'Blue Peg', 'BM%', '1', 6, 0);
				CreateTargetFace($TourId, 5, 'Blue Peg', 'BV%', '1', 6, 0);
				CreateTargetFace($TourId, 6, 'Yellow Peg', 'B%', '1', 6, 0);
				CreateTargetFace($TourId, 7, 'Yellow Peg', 'L%', '1', 6, 0);
				CreateTargetFace($TourId, 8, 'Red Peg', 'RS%', '1', 6, 0);
				CreateTargetFace($TourId, 9, 'Red Peg', 'RV%', '1', 6, 0);
				CreateTargetFace($TourId, 10, 'Red Peg', 'RM%', '1', 6, 0);
				CreateTargetFace($TourId, 11, 'Red Peg', 'CS%', '1', 6, 0);
				CreateTargetFace($TourId, 12, 'Red Peg', 'CV%', '1', 6, 0);
				CreateTargetFace($TourId, 13, 'Red Peg', 'CM%', '1', 6, 0);
				CreateTargetFace($TourId, 14, 'Red Peg', '_J%', '1', 6, 0);
				CreateTargetFace($TourId, 15, 'Blue Peg', '_C%', '1', 6, 0);
				break;
			case '3':
				CreateTargetFace($TourId, 1, 'Red Peg', 'X%', '1', 6, 0);
				CreateTargetFace($TourId, 2, 'Blue Peg', 'BJ%', '1', 6, 0);
				CreateTargetFace($TourId, 3, 'Yellow Peg', 'B%', '1', 6, 0);
				CreateTargetFace($TourId, 4, 'Yellow Peg', 'L%', '1', 6, 0);
				CreateTargetFace($TourId, 5, 'Red Peg', '_J%', '1', 6, 0);
				CreateTargetFace($TourId, 6, 'Blue Peg', '_C%', '1', 6, 0);
				CreateTargetFace($TourId, 7, 'Blue Peg', '_I%', '1', 6, 0);
				CreateTargetFace($TourId, 8, 'Yellow Peg', '_Y%', '1', 6, 0);
				CreateTargetFace($TourId, 9, 'Yellow Peg', '_K%', '1', 6, 0);
				break;
		}
		break;
	case 10:
	case 12:
		// Assumes Day 1 is Unmarked and Day 2 is Marked
		switch ($SubRule) {
			case '1':
				CreateTargetFace($TourId, 1, 'D1 Red / D2 Red', 'X%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 2, 'D1 Blu / D2 Blu', 'BS%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 3, 'D1 Blu / D2 Blu', 'BJ%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 4, 'D1 Blu / D2 Blu', 'BM%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 5, 'D1 Blu / D2 Blu', 'BV%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 6, 'D1 Blu / D2 Ylw', 'B%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 7, 'D1 Blu / D2 Ylw', 'L%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 8, 'D1 Red / D2 Red', 'RS%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 9, 'D1 Red / D2 Red', 'RV%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 10, 'D1 Red / D2 Red', 'RM%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 11, 'D1 Red / D2 Red', 'CS%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 12, 'D1 Red / D2 Red', 'CV%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 13, 'D1 Red / D2 Red', 'CM%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 14, 'D1 Red / D2 Red', '_J%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 15, 'D1 Red / D2 Blu', '_C%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 16, 'D1 Blu / D2 Blu', '_I%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 17, 'D1 Blu / D2 Ylw', '_Y%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 18, 'D1 Blu / D2 Ylw', '_K%', '1', 6, 0, 6, 0);
				break;
			case '2':
				CreateTargetFace($TourId, 1, 'D1 Red / D2 Red', 'X%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 2, 'D1 Blu / D2 Blu', 'BS%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 3, 'D1 Blu / D2 Blu', 'BJ%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 4, 'D1 Blu / D2 Blu', 'BM%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 5, 'D1 Blu / D2 Blu', 'BV%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 6, 'D1 Blu / D2 Ylw', 'B%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 7, 'D1 Blu / D2 Ylw', 'L%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 8, 'D1 Red / D2 Red', 'RS%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 9, 'D1 Red / D2 Red', 'RV%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 10, 'D1 Red / D2 Red', 'RM%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 11, 'D1 Red / D2 Red', 'CS%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 12, 'D1 Red / D2 Red', 'CV%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 13, 'D1 Red / D2 Red', 'CM%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 14, 'D1 Red / D2 Red', '_J%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 15, 'D1 Red / D2 Blu', '_C%', '1', 6, 0, 6, 0);
				break;
			case '3':
				CreateTargetFace($TourId, 1, 'D1 Red / D2 Red', 'X%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 2, 'D1 Blu / D2 Blu', 'BJ%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 3, 'D1 Blu / D2 Ylw', 'B%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 4, 'D1 Blu / D2 Ylw', 'L%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 5, 'D1 Red / D2 Red', '_J%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 6, 'D1 Red / D2 Blu', '_C%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 7, 'D1 Blu / D2 Blu', '_I%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 8, 'D1 Blu / D2 Ylw', '_Y%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, 9, 'D1 Blu / D2 Ylw', '_K%', '1', 6, 0, 6, 0);
				break;
		}
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
