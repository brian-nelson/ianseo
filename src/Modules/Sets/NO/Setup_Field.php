<?php
/*

COMMON SETUP FOR TARGET

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType, $SubRule);

// default Classes
CreateStandardClasses($TourId, $SubRule, $TourType);

// default Subclasses
CreateStandardSubClasses($TourId);

if($SubRule==9) {
	// Champs
	CreateStandardFieldEvents($TourId, $SubRule);
	InsertStandardFieldEvents($TourId, $SubRule);
}

// default Distances
// default targets
if($tourDetNumDist==2) {
	CreateDistance($TourId, $TourType, '%', 'Bane 1', 'Bane 2');
	CreateTargetFace($TourId, 1, '~Default', '%', '1', 6, 0, 6, 0);
} else {
	if($SubRule==9) {
		CreateDistance($TourId, $TourType, 'RD%', 'Blå');
		CreateDistance($TourId, $TourType, 'RH%', 'Blå');
		CreateDistance($TourId, $TourType, 'RK%', 'Blå');
		CreateDistance($TourId, $TourType, 'RR%', 'Gul');
		CreateDistance($TourId, $TourType, 'CD%', 'Rød');
		CreateDistance($TourId, $TourType, 'CH%', 'Rød');
		CreateDistance($TourId, $TourType, 'CK%', 'Rød');
		CreateDistance($TourId, $TourType, 'CR%', 'Blå');
		CreateDistance($TourId, $TourType, 'BUBU', 'Rød');
		CreateDistance($TourId, $TourType, 'BK', 'Blå');
		CreateDistance($TourId, $TourType, 'BR', 'Gul');
		CreateDistance($TourId, $TourType, 'B_i', 'Blå');
		CreateDistance($TourId, $TourType, 'LBK', 'Blå');
		CreateDistance($TourId, $TourType, 'LBR', 'Gul');
		CreateDistance($TourId, $TourType, 'LB_i', 'Blå');
		CreateDistance($TourId, $TourType, 'INK', 'Blå');
		CreateDistance($TourId, $TourType, 'INR', 'Gul');
		CreateDistance($TourId, $TourType, 'IN_i', 'Blå');
	} else {
		CreateDistance($TourId, $TourType, '%', 'Bane');
	}
	CreateTargetFace($TourId, 1, '~Default', '%', '1', 6, 0);
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