<?php
/*

COMMON SETUP 3D

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType, $SubRule);

// default Classes
CreateStandardClasses($TourId, $SubRule, $TourType); //

// default Subclasses
CreateStandardSubClasses($TourId);

if($SubRule==7) {
	// Champs
	CreateStandard3DEvents($TourId, $SubRule, $TourType);
	InsertStandard3DEvents($TourId, $SubRule);
}

// default Distances & Default Target
if($tourDetNumDist==1) {
	CreateDistance($TourId, $TourType, '%', 'Bane');
	CreateTargetFace($TourId, 1, '~Default', '%', '1', 8, 0);
} else {
	CreateDistance($TourId, $TourType, '%', 'Bane 1', 'Bane 2');
	CreateTargetFace($TourId, 1, '~Default', '%', '1', 8, 0, 8, 0);
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