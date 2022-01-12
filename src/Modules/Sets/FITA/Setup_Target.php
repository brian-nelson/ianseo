<?php
/*
Common Setup for "Target" Archery
*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId,(in_array($TourType,array(3,6,7,8,37)) ? '70M':'FITA'));

// default SubClasses
//CreateSubClass($TourId, 1, '00', '00');

// default Classes
CreateStandardClasses($TourId, $SubRule);

// default Distances
switch($TourType) {
	case 1:
	case 4:
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, '_M',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_W',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_U21M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_U21W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_U18M', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_U18W', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_50M', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_50W', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				break;
			case '2':
			case '5':
				CreateDistanceNew($TourId, $TourType, '_M',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_W',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, '_M',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_W',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_U21M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_U21W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				break;
			case '4':
				CreateDistanceNew($TourId, $TourType, '_U21M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_U21W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_U18M', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_U18W', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				break;
		}
		break;
	case 2:
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, '_M',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_W',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_U21M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_U21W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_U18M', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_U18W', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_50M', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_50W', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				break;
			case '2':
			case '5':
				CreateDistanceNew($TourId, $TourType, '_M',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_W',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, '_M',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_W',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_U21M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_U21W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				break;
			case '4':
				CreateDistanceNew($TourId, $TourType, '_U21M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_U21W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_U18M', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_U18W', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				break;
		}
		break;
	case 18:
		CreateDistanceNew($TourId, $TourType, 'C%', array(array('50m-1',50), array('50m-2',50), array('-',0), array('-',0)));
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, 'RM',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RW',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RU21M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RU21W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RU18M', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RU18W', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'R50M', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'R50W', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				break;
			case '2':
			case '5':
				CreateDistanceNew($TourId, $TourType, 'RM',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RW',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, 'RM',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RW',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RU21M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RU21W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				break;
			case '4':
				CreateDistanceNew($TourId, $TourType, 'RU21M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RU21W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RU18M', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RU18W', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				break;
		}
		break;
	case 3:
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, 'RM', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'RW', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'RU21_', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'RU18_', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'R50_', array(array('60m-1',60), array('60m-2',60)));
				break;
			case '2':
			case '3':
			case '5':
				CreateDistanceNew($TourId, $TourType, 'R%', array(array('70m-1',70), array('70m-2',70)));
				break;
			case '4':
				CreateDistanceNew($TourId, $TourType, 'RU21_', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'RU18_', array(array('60m-1',60), array('60m-2',60)));
				break;
		}
		CreateDistanceNew($TourId, $TourType, 'C%', array(array('50m-1',50), array('50m-2',50)));
        CreateDistanceNew($TourId, $TourType, 'B%', array(array('50m-1',50), array('50m-2',50)));
		break;
	case 5:
		CreateDistanceNew($TourId, $TourType, '%', array(array('60 m',60), array('50 m',50), array('40 m',40)));
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
        switch($SubRule) {
            case '1':
                CreateDistanceNew($TourId, $TourType, 'RM', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
                CreateDistanceNew($TourId, $TourType, 'RW', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
                CreateDistanceNew($TourId, $TourType, 'RU21_', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
                CreateDistanceNew($TourId, $TourType, 'RU18_', array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
                CreateDistanceNew($TourId, $TourType, 'R50_', array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
                break;
            case '2':
            case '3':
            case '5':
                CreateDistanceNew($TourId, $TourType, 'R%', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
                break;
            case '4':
                CreateDistanceNew($TourId, $TourType, 'RU21_', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
                CreateDistanceNew($TourId, $TourType, 'RU18_', array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
                break;
        }
	    CreateDistanceNew($TourId, $TourType, 'C%', array(array('50m-1',50), array('50m-2',50), array('50m-1',50), array('50m-2',50)));
	    CreateDistanceNew($TourId, $TourType, 'B%', array(array('50m-1',50), array('50m-2',50), array('50m-1',50), array('50m-2',50)));
        break;
}

if($TourType<5 or $TourType==6 or $TourType==18 or $TourType==37) {
	// default Events
	CreateStandardEvents($TourId, $SubRule, $TourType!=6, in_array($TourType,array(3,6,7,8,37)));

	// Classes in Events
	InsertStandardEvents($TourId, $SubRule);

	// Finals & TeamFinals
	CreateFinals($TourId);
}

// Default Target
switch($TourType) {
	case 1:
	case 4:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 10, 80);
		// optional target faces
		CreateTargetFace($TourId, 2, '~Option1', '%', '',  5, 122, 5, 122, 5, 80,  5, 80);
		CreateTargetFace($TourId, 3, '~Option2', '%', '',  5, 122, 5, 122, 9, 80, 10, 80);
		break;
	case 2:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 10, 80, 5, 122, 5, 122, 5, 80, 10, 80);
		// optional target faces
		CreateTargetFace($TourId, 2, '~Option1', '%', '',  5, 122, 5, 122, 5, 80,  5, 80,  5, 122, 5, 122, 5, 80,  5, 80);
		CreateTargetFace($TourId, 3, '~Option2', '%', '',  5, 122, 5, 122, 9, 80, 10, 80,  5, 122, 5, 122, 9, 80, 10, 80);
		break;
	case 18:
		CreateTargetFace($TourId, 1, '~Default', 'R%', '1', 5, 122, 5, 122, 5, 80, 10, 80);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 9, 80, 9, 80);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Option1', 'R%', '',  5, 122, 5, 122, 5, 80,  5, 80);
		CreateTargetFace($TourId, 4, '~Option2', 'R%', '',  5, 122, 5, 122, 9, 80, 10, 80);
		break;
	case 3:
		CreateTargetFace($TourId, 1, '~Default', 'REG-^R|^B', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C', '1',  9, 80, 9, 80);
		break;
	case 5:
		CreateTargetFace($TourId, 1, '~Default', '%', '1',  5, 122, 5, 122, 5, 122);
		break;
	case 6:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 2, 40, 2, 40);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 4, 40, 4, 40);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Option1', 'REG-^R|^B', '',  1, 40, 1, 40);
		break;
	case 7:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 2, 60, 2, 60);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 4, 60, 4, 60);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Option1', 'REG-^R|^B', '',  1, 60, 1, 60);
		break;
	case 8:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 2, 60, 2, 60, 2, 40, 2, 40);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 4, 60, 4, 60, 4, 40, 4, 40);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Option1', 'REG-^R|^B', '',  1, 60, 1, 60,  1, 40, 1, 40);
		break;
    case 37:
        CreateTargetFace($TourId, 1, '~Default', 'REG-^R|^B', '1', 5, 122, 5, 122,5, 122, 5, 122);
        CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C', '1',  9, 80, 9, 80,9, 80, 9, 80);
        break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 24, 4);

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
