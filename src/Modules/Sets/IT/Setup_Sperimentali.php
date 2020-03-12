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
CreateDivision($TourId, 1, 'OL', 'Arco Olimpico');
CreateDivision($TourId, 2, 'CO', 'Arco Compound');
CreateDivision($TourId, 3, 'AN', 'Arco Nudo');


// default Classes
CreateStandardSperimClasses($TourId);

// default Distances
CreateDistance($TourId, $TourType, 'AN_1', '30m','30m');
CreateDistance($TourId, $TourType, 'OL_1', '30m','30m');
CreateDistance($TourId, $TourType, 'CO_1', '50m','50m');
CreateDistance($TourId, $TourType, '___2', '50m','50m');

// Default Target
CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 120, 5, 120);
CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^CO[MF]2', '1', 5, 80, 5, 80);


//Load a different set of names
$tourDetIocCode         = 'ITA_e';

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 24, 4);

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