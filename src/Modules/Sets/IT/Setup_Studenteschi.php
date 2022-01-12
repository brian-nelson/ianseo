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
CreateDivision($TourId, 1, 'OL', 'Arco Olimpico', 1, 'R', 'R');

// default Classes
CreateStandardStudClasses($TourId, $TourType);

// default Distances
if($TourType==33) {
	CreateDistanceNew($TourId, $TourType, '%', array(array('25m',25)));

	// Default Target
	CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 60);

	// create a first distance prototype
	CreateDistanceInformation($TourId, $DistanceInfoArray, 32, 4);

	$i=1;
	CreateEvent($TourId, $i++, 1, 0, 2, 5, 4, 4, 2, 4, 4, 2, 'MT',  'Squadre Maschili', 1, 0, 0, 0, 0, '', '', 60, 25);
	CreateEvent($TourId, $i++, 1, 0, 2, 5, 4, 4, 2, 4, 4, 2, 'FT',  'Squadre Femminili', 1, 0, 0, 0, 0, '', '', 60, 25);

	InsertClassEvent($TourId, 1, 2, 'MT', 'OL', 'M');
	InsertClassEvent($TourId, 1, 2, 'FT', 'OL', 'F');

} else {
	CreateDistanceNew($TourId, $TourType, '__C_', array(array('10m',10)));
	CreateDistanceNew($TourId, $TourType, '__A_', array(array('20m',20)));

	// Default Target
	CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 80);

	// create a first distance prototype
	CreateDistanceInformation($TourId, $DistanceInfoArray, 32, 4);
}

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
