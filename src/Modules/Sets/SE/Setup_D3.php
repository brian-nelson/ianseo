<?php
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
		CreateDistance($TourId, $TourType, '%', 'Rutt');
		break;
}

// Default Target
$i=1;
switch($TourType) {
	case 11:
		CreateTargetFace($TourId, $i++, 'Red Peg', 'REG-^[C]{1,1}[J]{0,1}[HD]{1,1}', '1', 8, 0);
		CreateTargetFace($TourId, $i++, 'Blue Peg', 'REG-^[RBLI]{1,1}[J]{0,1}[HD]{1,1}', '1', 8, 0);
		CreateTargetFace($TourId, $i++, 'Blue Peg', 'REG-^[C]{1,1}[SMV]{1,1}[HD]{1,1}', '1', 8, 0);
		CreateTargetFace($TourId, $i++, 'Black Peg', 'REG-^[RBLI]{1,1}[SMV]{1,1}[HD]{1,1}', '1', 8, 0);
		CreateTargetFace($TourId, $i++, 'Black Peg', 'REG-^[RCBLI]{1,1}[C]{1,1}[HD]{1,1}', '1', 8, 0);
		CreateTargetFace($TourId, $i++, 'White Peg', 'REG-^[RCBLI]{1,1}[K]{1,1}[HD]{1,1}', '1', 8, 0);
		break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 20, 4);

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