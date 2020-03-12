<?php
/*
15 	Type_GiochiGioventu

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (2)

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateDivision($TourId, 1, 'OL', '~OL');

// default Classes
CreateStandardGdGClasses($TourId, $SubRule);

// default Distances
switch($TourType) {
	case 15:
		CreateDistance($TourId, $TourType, '__M_', '20m-1', '20m-2');
		CreateDistance($TourId, $TourType, '__F_', '20m-1', '20m-2');
		CreateDistance($TourId, $TourType, '__G_', '15m-1', '15m-2');
		break;
	case 16:
		CreateDistance($TourId, $TourType, '__M_', '18m-1', '18m-2');
		CreateDistance($TourId, $TourType, '__F_', '18m-1', '18m-2');
		CreateDistance($TourId, $TourType, '__G_', '15m-1', '15m-2');
		break;
}

// Default Target
CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 80, 5, 80);

//Load a different set of names
$tourDetIocCode         = 'ITA_p';

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 32, 4);

// // Update Tour details
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