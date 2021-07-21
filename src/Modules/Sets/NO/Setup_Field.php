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
	CreateDistanceNew($TourId, $TourType, '%', array(array('Bane 1',0), array('Bane 2',0)));
	CreateTargetFace($TourId, 1, '~Default', '%', '1', 6, 0, 6, 0);
} else {
	if($SubRule==9) {
		CreateDistanceNew($TourId, $TourType, 'RD%', array(array('Blå',0)));
		CreateDistanceNew($TourId, $TourType, 'RH%', array(array('Blå',0)));
		CreateDistanceNew($TourId, $TourType, 'RK%', array(array('Blå',0)));
		CreateDistanceNew($TourId, $TourType, 'RR%', array(array('Gul',0)));
		CreateDistanceNew($TourId, $TourType, 'CD%', array(array('Rød',0)));
		CreateDistanceNew($TourId, $TourType, 'CH%', array(array('Rød',0)));
		CreateDistanceNew($TourId, $TourType, 'CK%', array(array('Rød',0)));
		CreateDistanceNew($TourId, $TourType, 'CR%', array(array('Blå',0)));
		CreateDistanceNew($TourId, $TourType, 'BUBU', array(array('Rød',0)));
		CreateDistanceNew($TourId, $TourType, 'BK', array(array('Blå',0)));
		CreateDistanceNew($TourId, $TourType, 'BR', array(array('Gul',0)));
		CreateDistanceNew($TourId, $TourType, 'B_i', array(array('Blå',0)));
		CreateDistanceNew($TourId, $TourType, 'LBK', array(array('Blå',0)));
		CreateDistanceNew($TourId, $TourType, 'LBR', array(array('Gul',0)));
		CreateDistanceNew($TourId, $TourType, 'LB_i', array(array('Blå',0)));
		CreateDistanceNew($TourId, $TourType, 'INK', array(array('Blå',0)));
		CreateDistanceNew($TourId, $TourType, 'INR', array(array('Gul',0)));
		CreateDistanceNew($TourId, $TourType, 'IN_i', array(array('Blå',0)));
	} else {
		CreateDistanceNew($TourId, $TourType, '%', array(array('Bane',0)));
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
