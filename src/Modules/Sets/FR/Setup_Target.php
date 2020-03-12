<?php
/*
Common Setup for "Target" Archery
*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
switch($TourType) {
	case 6:
	case 7:
	case 8:
		// Indoors: adds BareBow to C and R
		CreateStandardDivisions($TourId, 'FIELD');
		break;
	case 3:
		CreateStandardDivisions($TourId);
		break;
	default:
		CreateStandardDivisions($TourId);
}

// default Classes
CreateStandardClasses($TourId, $TourType, $SubRule);

// default Distances
switch($TourType) {
	case 3:
		switch($SubRule) {
			case 8:
				// FEDERAL
				CreateDistance($TourId, $TourType, 'CLS_', '50m-1', '50m-2');
				CreateDistance($TourId, $TourType, 'CLV_', '50m-1', '50m-2');
				CreateDistance($TourId, $TourType, 'CLW_', '50m-1', '50m-2');
				CreateDistance($TourId, $TourType, 'CLJ_', '50m-1', '50m-2');
				CreateDistance($TourId, $TourType, 'CLC_', '50m-1', '50m-2');
				CreateDistance($TourId, $TourType, 'CLM_', '30m-1', '30m-2');
				CreateDistance($TourId, $TourType, 'CLB_', '20m-1', '20m-2');
				CreateDistance($TourId, $TourType, 'CLP_', '20m-1', '20m-2');
				CreateDistance($TourId, $TourType, 'CO%', '50m-1', '50m-2');
				break;
			case 11:
				// Coupe France: mix of international and federal
				CreateDistance($TourId, $TourType, 'CL1H', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'CL2H', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'CL3H', '60m-1', '60m-2');
				CreateDistance($TourId, $TourType, 'CLJH', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'CLCH', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'CL1F', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'CL2F', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'CL3F', '60m-1', '60m-2');
				CreateDistance($TourId, $TourType, 'CLJF', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'CLCF', '60m-1', '60m-2');
				CreateDistance($TourId, $TourType, 'CL_W', '50m-1', '50m-2');
				CreateDistance($TourId, $TourType, 'CL_M', '50m-1', '50m-2');
				CreateDistance($TourId, $TourType, 'CO%', '50m-1', '50m-2');
				break;
			default:
				CreateDistance($TourId, $TourType, 'CL1_', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'CL2_', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'CL3_', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'CLJ_', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'CLC_', '60m-1', '60m-2');
				CreateDistance($TourId, $TourType, 'CLM_', '40m-1', '40m-2');
				CreateDistance($TourId, $TourType, 'CLB_', '30m-1', '30m-2');
				CreateDistance($TourId, $TourType, 'CLP_', '20m-1', '20m-2');
				CreateDistance($TourId, $TourType, 'CO%', '50m-1', '50m-2');
		}
		break;
	//case 5:
	//	CreateDistance($TourId, $TourType, '%', '60 m', '50 m', '40 m');
	//	break;
	case 6:
		CreateDistance($TourId, $TourType, '%', '18m-1', '18m-2');
		break;
	case 7:
		CreateDistance($TourId, $TourType, '%', '25m-1', '25m-2');
		break;
	case 8:
		CreateDistance($TourId, $TourType, '%', '25m-1', '25m-2', '18m-1', '18m-2');
		break;
}

if(in_array($TourType, array(3, 6)) and $SubRule>1) {
	// default Events
	CreateStandardEvents($TourId, $TourType, $SubRule, $TourType!=6);

	// Classes in Events
	InsertStandardEvents($TourId, $TourType, $SubRule);

	// Finals & TeamFinals
	if($TourType==3 and $SubRule==7) {
		// if D1 creates individual finals MatchNo a little differently
		CreateFinals_FR_3_SetFRChampsD1DNAP($TourId);
	} else {
		CreateFinals($TourId);
	}
}

// Default Target
$TgtId=1;
switch($TourType) {
	case 3:
		switch($SubRule) {
			case 8:
				// FEDERAL
				CreateTargetFace($TourId, 1, 'Blason 80', 'REG-^CL[BMP]', '1', 5, 80, 5, 80);
				CreateTargetFace($TourId, 2, 'Blason 122', 'REG-^(CO|CL[^CJSVW])', '1', 5, 122, 5, 122);
				break;
			case 11:
				// Coupe France, mix of international and federal
				CreateTargetFace($TourId, 1, 'Blason Classique 122', 'CL%', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, 2, 'Blason Compound', 'REG-^CO.[FH]', '1',  9, 80, 9, 80);
				CreateTargetFace($TourId, 3, 'Blason Fédéral 122', 'REG-^CO.[WM]', '1', 5, 122, 5, 122);
				break;
			default:
				CreateTargetFace($TourId, 1, 'Blason Classique 80', 'REG-^CL[BM]', '1', 5, 80, 5, 80);
				CreateTargetFace($TourId, 2, 'Blason Classique 122', 'REG-^CL[^BM]', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, 3, 'Blason Compound', 'CO%', '1',  9, 80, 9, 80);
		}
		break;
	case 6:
		switch($SubRule) {
			case '1':
				// All classes
				CreateTargetFace($TourId, $TgtId++, 'Blason Unique', 'REG-^(CL|BB)[^BMPY]', '1', 1, 40, 1, 40);
				CreateTargetFace($TourId, $TgtId++, 'Blason Unique', 'REG-^(CL|BB)[BMY]', '1', 1, 60, 1, 60);
				CreateTargetFace($TourId, $TgtId++, 'Blason Unique', 'CLP%', '1', 1, 80, 1, 80);
				CreateTargetFace($TourId, $TgtId++, 'Trispot Poulie 6-10', 'CO%', '1', 4, 40, 4, 40);
				// optional target faces
				CreateTargetFace($TourId, $TgtId++, 'Trispot Classique 6-10', 'REG-^(CL|BB)[^BMPY]', '',  2, 40, 2, 40);
				break;
			case '2':
				// Championships Adults
				CreateTargetFace($TourId, $TgtId++, 'Trispot Classique 6-10', 'CL%', '1', 2, 40, 2, 40);
				CreateTargetFace($TourId, $TgtId++, 'Trispot Poulie 6-10', 'CO%', '1', 2, 40, 2, 40);
				CreateTargetFace($TourId, $TgtId++, 'Blason Unique', 'BB%', '1', 1, 40, 1, 40);
				break;
			case '3':
				// Championships Youth
				CreateTargetFace($TourId, $TgtId++, 'Trispot Classique 6-10', 'REG-^CL[^BM]', '1', 2, 40, 2, 40);
				CreateTargetFace($TourId, $TgtId++, 'Trispot 60cm 6-10', 'REG-^CL[BM]', '1', 2, 60, 2, 60);
				CreateTargetFace($TourId, $TgtId++, 'Trispot Poulie 6-10', 'CO%', '1', 2, 40, 2, 40);
				CreateTargetFace($TourId, $TgtId++, 'Blason Unique', 'REG-^(BB)', '1', 1, 60, 1, 60);
				break;
		}
		break;
	case 7:
		CreateTargetFace($TourId, $TgtId++, 'Blason Unique', 'REG-^(CL|BB)[^BMPY]', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $TgtId++, 'Blason Unique', 'REG-^(CL|BB)[BMY]', '1', 1, 80, 1, 80);
		CreateTargetFace($TourId, $TgtId++, 'Blason Unique', 'CLP%', '1', 1, 122, 1, 122);
		CreateTargetFace($TourId, $TgtId++, 'Trispot Poulie 6-10', 'CO%', '1', 4, 60, 4, 60);
		// optional target faces
		CreateTargetFace($TourId, $TgtId++, 'Trispot Classique 10', 'REG-^(CL|BB)[^BMPY]', '',  2, 60, 2, 60);
		break;
	case 8:
		CreateTargetFace($TourId, $TgtId++, 'Blason Unique', 'REG-^(CL|BB)[^BMPY]', '1', 1, 60, 1, 60,  1, 40, 1, 40);
		CreateTargetFace($TourId, $TgtId++, 'Blason Unique', 'REG-^(CL|BB)[BMY]', '1', 1, 80, 1, 80, 1, 60, 1, 60);
		CreateTargetFace($TourId, $TgtId++, 'Blason Unique', 'CLP%', '1', 1, 122, 1, 122, 1, 80, 1, 80);
		CreateTargetFace($TourId, $TgtId++, 'Trispot Poulie 6-10', 'CO%', '1', 4, 60, 4, 60, 4, 40, 4, 40);
		// optional target faces
		CreateTargetFace($TourId, $TgtId++, 'Trispot Classique 10', 'REG-^(CL|BB)[^BMPY]', '',  2, 60, 2, 60, 2, 40, 2, 40);
		break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 24, 4);

$tourDetIocCode         = 'FRA';

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

