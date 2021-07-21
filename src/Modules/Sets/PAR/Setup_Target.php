<?php
/*

Common setup for Target

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $SubRule);

// default Classes
CreateStandardClasses($TourId, $SubRule);

// default Distances
switch($TourType) {
	case 1:
	case 4:
		CreateDistanceNew($TourId, $TourType, '_M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'W1%',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'VI%',  array(array('60cm face',30), array('80cm face',30), array('80cm face',30), array('122cm face',30)));
		break;
	case 18:
		CreateDistanceNew($TourId, $TourType, 'RM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'RW', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'W1%',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'VI%',  array(array('60cm face',30), array('80cm face',30), array('80cm face',30), array('122cm face',30)));
		CreateDistanceNew($TourId, $TourType, 'C%',  array(array('50m-1',50), array('50m-2',50), array('-',0), array('-',0)));
		CreateDistanceNew($TourId, $TourType, 'W1%', array(array('50m-1',50), array('50m-2',50), array('-',0), array('-',0)));
		break;
	case 2:
		CreateDistanceNew($TourId, $TourType, '_M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'W1%',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'VI%',  array(array('60cm face',30), array('80cm face',30), array('80cm face',30), array('122cm face',30), array('60cm face',30), array('80cm face',30), array('80cm face',30), array('122cm face',30)));
		break;
	case 3:
		CreateDistanceNew($TourId, $TourType, 'C%', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'W1%', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'R%', array(array('70m-1',70), array('70m-2',70)));
		CreateDistanceNew($TourId, $TourType, 'VI%', array(array('30m-1',30), array('30m-2',30)));
		break;
	case 5:
		CreateDistanceNew($TourId, $TourType, 'C%', array(array('60 m',60), array('50 m',50), array('40 m',40)));
		CreateDistanceNew($TourId, $TourType, 'W1%', array(array('60 m',60), array('50 m',50), array('40 m',40)));
		CreateDistanceNew($TourId, $TourType, 'R%', array(array('60 m',60), array('50 m',50), array('40 m',40)));
		CreateDistanceNew($TourId, $TourType, 'VI%', array(array('30m-1',30), array('30m-2',30), array('30m-3',30)));
		break;
	case 6:
		CreateDistanceNew($TourId, $TourType, '%', array(array('18m-1',18), array('18m-2',18)));
		break;
	case 7:
		CreateDistanceNew($TourId, $TourType, '%', array(array('25m-1',25), array('25m-2',25)));
		break;
	case 8:
		CreateDistanceNew($TourId, $TourType, '%', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
		break;
    case 37:
        CreateDistanceNew($TourId, $TourType, 'C%', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
        CreateDistanceNew($TourId, $TourType, 'W1%', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
        CreateDistanceNew($TourId, $TourType, 'R%', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
        CreateDistanceNew($TourId, $TourType, 'VI%', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
        break;
}

if($TourType<5 or $TourType==6 or $TourType==18 or $TourType==37) {
	// default Events
	CreateStandardEvents($TourId, $SubRule, $TourType!=6);

	// Classes in Events
	InsertStandardEvents($TourId, $SubRule, $TourType!=6);

	// Finals & TeamFinals
	CreateFinals($TourId);
}

// Default Target
switch($TourType) {
	case 1:
	case 4:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
		CreateTargetFace($TourId, 2, '~DefaultVI', 'VI%', '1', 5, 60, 5, 80, 5, 80, 5, 122);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Option1', '%', '',  5, 122, 5, 122, 9, 80, 9, 80);
		break;
	case 18:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
		CreateTargetFace($TourId, 2, '~DefaultVI', 'VI%', '1', 5, 60, 5, 80, 5, 80, 5, 122);
		CreateTargetFace($TourId, 3, '~DefaultCO', 'C%', '1',  9, 80, 9, 80, 0, 0, 0, 0);
		CreateTargetFace($TourId, 4, '~DefaultCO', 'W1%', '1',  5, 80, 5, 80, 0, 0, 0, 0);
		// optional target faces
		CreateTargetFace($TourId, 5, '~Option1', 'R%', '',  5, 122, 5, 122, 9, 80, 9, 80);
		break;
	case 2:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
		CreateTargetFace($TourId, 2, '~DefaultVI', 'VI%', '1', 5, 60, 5, 80, 5, 80, 5, 122, 5, 60, 5, 80, 5, 80, 5, 122);
		// optional target faces
		CreateTargetFace($TourId, 3, 'Option1', '%', '',  5, 122, 5, 122, 9, 80, 9, 80,  5, 122, 5, 122, 9, 80, 9, 80);
		break;
	case 3:
		CreateTargetFace($TourId, 1, '~Default', 'R%', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 9, 80, 9, 80);
		CreateTargetFace($TourId, 3, '~DefaultW1', 'W1%', '1', 5, 80, 5, 80);
		CreateTargetFace($TourId, 4, '~DefaultVI', 'VI%', '1', 5, 80, 5, 80);
		break;
	case 5:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122, 5, 122);
		CreateTargetFace($TourId, 2, '~DefaultVI', 'VI%', '1', 5, 80, 5, 80, 5, 80);
		break;
	case 6:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 2, 40, 2, 40);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 4, 40, 4, 40);
		CreateTargetFace($TourId, 3, '~DefaultCO', 'W1%', '1', 4, 40, 4, 40);
		CreateTargetFace($TourId, 4, '~DefaultVI', 'VI%', '1', 1, 60, 1, 60);
		// optional target faces
		CreateTargetFace($TourId, 5, '~Option1', 'R%', '',  1, 40, 1, 40);
		break;
	case 7:
		CreateTargetFace($TourId, 1, '~Default', 'R%', '1', 2, 60, 2, 60);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 4, 60, 4, 60);
		CreateTargetFace($TourId, 3, '~DefaultCO', 'W1%', '1', 4, 60, 4, 60);
		CreateTargetFace($TourId, 4, '~DefaultVI', 'VI%', '1', 1, 80, 1, 80);
		// optional target faces
		CreateTargetFace($TourId, 5, '~Option1', 'R%', '',  1, 60, 1, 60);
		break;
	case 8:
		CreateTargetFace($TourId, 1, '~Default', 'R%', '1', 2, 60, 2, 60, 2, 40, 2, 40);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 4, 60, 4, 60, 4, 40, 4, 40);
		CreateTargetFace($TourId, 3, '~DefaultCO', 'W1%', '1', 4, 60, 4, 60, 4, 40, 4, 40);
		CreateTargetFace($TourId, 4, '~DefaultVI', 'VI%', '1', 1, 80, 1, 80, 1, 60, 1, 60);
		// optional target faces
		CreateTargetFace($TourId, 5, '~Option1', 'R%', '',  1, 60, 1, 60,  1, 40, 1, 40);
		break;
    case 37:
        CreateTargetFace($TourId, 1, '~Default', 'R%', '1', 5, 122, 5, 122, 5, 122, 5, 122);
        CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 9, 80, 9, 80, 9, 80, 9, 80);
        CreateTargetFace($TourId, 3, '~DefaultW1', 'W1%', '1', 5, 80, 5, 80, 5, 80, 5, 80);
        CreateTargetFace($TourId, 4, '~DefaultVI', 'VI%', '1', 5, 80, 5, 80, 5, 80, 5, 80);
        break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 32, 2);

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
