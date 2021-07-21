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
				CreateDistanceNew($TourId, $TourType, 'CLS_', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'CLV_', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'CLW_', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'CLJ_', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'CLC_', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'CLM_', array(array('30m-1',30), array('30m-2',30)));
				CreateDistanceNew($TourId, $TourType, 'CLB_', array(array('20m-1',20), array('20m-2',20)));
				CreateDistanceNew($TourId, $TourType, 'CLP_', array(array('20m-1',20), array('20m-2',20)));
				CreateDistanceNew($TourId, $TourType, 'CO%', array(array('50m-1',50), array('50m-2',50)));
				break;
			case 11:
				// Coupe France: mix of international and federal
				CreateDistanceNew($TourId, $TourType, 'CL1H', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'CL2H', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'CL3H', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'CLJH', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'CLCH', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'CL1F', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'CL2F', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'CL3F', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'CLJF', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'CLCF', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'CL_W', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'CL_M', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'CO%', array(array('50m-1',50), array('50m-2',50)));
				break;
			default:
				CreateDistanceNew($TourId, $TourType, 'CL1_', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'CL2_', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'CL3_', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'CLJ_', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'CLC_', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'CLM_', array(array('40m-1',40), array('40m-2',40)));
				CreateDistanceNew($TourId, $TourType, 'CLB_', array(array('30m-1',30), array('30m-2',30)));
				CreateDistanceNew($TourId, $TourType, 'CLP_', array(array('20m-1',20), array('20m-2',20)));
				CreateDistanceNew($TourId, $TourType, 'CO%', array(array('50m-1',50), array('50m-2',50)));
		}
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

