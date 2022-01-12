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
CreateDivision($TourId, 1, 'OL', '~OL', 1, 'R', 'R');

// default Classes
CreateStandardGdGClasses($TourId, $SubRule);

// default Distances
switch($TourType) {
	case 15:
		CreateDistanceNew($TourId, $TourType, '___1', array(array('15m-1',15), array('15m-2',15)));
		CreateDistanceNew($TourId, $TourType, '___2', array(array('15m-1',15), array('15m-2',15)));
		CreateDistanceNew($TourId, $TourType, '___3', array(array('20m-1',20), array('20m-2',20)));
		CreateDistanceNew($TourId, $TourType, '___4', array(array('20m-1',20), array('20m-2',20)));
		break;
	case 16:
		CreateDistanceNew($TourId, $TourType, '__M_', array(array('18m-1',18), array('18m-2',18)));
		CreateDistanceNew($TourId, $TourType, '__F_', array(array('18m-1',18), array('18m-2',18)));
		CreateDistanceNew($TourId, $TourType, '__G_', array(array('15m-1',15), array('15m-2',15)));
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
