<?php
/*
11 	3D 	(1 distance)

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (11)

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, '3D', $SubRule);

// default SubClasses
//CreateSubClass($TourId, 1, '00', '00');

// default Classes
CreateStandardClasses($TourId, $SubRule);

// default Distances
switch($TourType) {
	case 11:
		CreateDistanceNew($TourId, $TourType, '%', array(array('Kurs',0)));
		break;
	case 13:
		CreateDistanceNew($TourId, $TourType, '%', array(array('Kurs 1',0), array('Kurs 2',0)));
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
        CreateTargetFace($TourId, 1, 'Weiß', 'REG-^[BLI][KS]', '1', TGT_3D, 0);
        CreateTargetFace($TourId, 2, 'Blau', 'REG-^[CR][KS]', '1', TGT_3D, 0);
        CreateTargetFace($TourId, 3, 'Blau', 'REG-^([BLI][CJMVW])', '1', TGT_3D, 0);
        CreateTargetFace($TourId, 4, 'Rot', 'REG-^[RC][CJMVW]', '1', TGT_3D, 0);
		break;
	case 13:
        CreateTargetFace($TourId, 1, 'Weiß', 'REG-^[BLI][KS]', '1', TGT_3D, 0, TGT_3D, 0);
        CreateTargetFace($TourId, 2, 'Blau', 'REG-^[CR][KS]', '1', TGT_3D, 0, TGT_3D, 0);
        CreateTargetFace($TourId, 3, 'Blau', 'REG-^([BLI][CJMVW])', '1', TGT_3D, 0, TGT_3D, 0);
        CreateTargetFace($TourId, 4, 'Rot', 'REG-^[RC][CJMVW]', '1', TGT_3D, 0, TGT_3D, 0);
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
//	'ToIocCode'	=> $tourDetIocCode,
	);
UpdateTourDetails($TourId, $tourDetails);

