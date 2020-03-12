<?php

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType);

// default SubClasses
CreateStandardSubClasses($TourId);

// default Classes
CreateStandardClasses($TourId, $SubRule, '', $TourType);

// default Distances
switch($TourType)
{
	case 1:
		CreateDistance($TourId, $TourType, '_K_', '20m-1', '20m-2','20m-3', '20m-4');
		CreateDistance($TourId, $TourType, 'BC_', '40m', '30m-2','30m-3', '20m');
		CreateDistance($TourId, $TourType, 'LC_', '40m', '30m-2','30m-3', '20m');
		CreateDistance($TourId, $TourType, 'IC_', '40m', '30m-2','30m-3', '20m');
		CreateDistance($TourId, $TourType, 'RC_', '50m', '40m','30m', '20m');
		CreateDistance($TourId, $TourType, 'CC_', '50m', '40m','30m', '20m');
		CreateDistance($TourId, $TourType, 'LJ_', '40m', '30m-2','30m-3', '20m');
		CreateDistance($TourId, $TourType, 'IJ_', '40m', '30m-2','30m-3', '20m');
		CreateDistance($TourId, $TourType, 'BJ_', '50m', '40m','30m', '20m');
		CreateDistance($TourId, $TourType, 'RJH', '70m', '60m','50m', '30m');
		CreateDistance($TourId, $TourType, 'RJD', '60m', '50m','40m', '30m');
		CreateDistance($TourId, $TourType, 'CJH', '70m', '60m','50m', '30m');
		CreateDistance($TourId, $TourType, 'CJD', '60m', '50m','40m', '30m');
		CreateDistance($TourId, $TourType, 'BV_', '50m', '40m','30m', '20m');
		CreateDistance($TourId, $TourType, 'LV_', '40m', '30m-2','30m-3', '20m');
		CreateDistance($TourId, $TourType, 'IV_', '40m', '30m-2','30m-3', '20m');
		CreateDistance($TourId, $TourType, 'RV_', '60m', '50m','40m', '30m');
		CreateDistance($TourId, $TourType, 'CV_', '60m', '50m','40m', '30m');

		CreateDistance($TourId, $TourType, 'BM_', '50m', '40m','30m', '20m');
		CreateDistance($TourId, $TourType, 'LM_', '40m', '30m-2','30m-3', '20m');
		CreateDistance($TourId, $TourType, 'IM_', '40m', '30m-2','30m-3', '20m');
		CreateDistance($TourId, $TourType, 'RM_', '60m', '50m','40m', '30m');
		CreateDistance($TourId, $TourType, 'CM_', '60m', '50m','40m', '30m');

		CreateDistance($TourId, $TourType, 'BS_', '50m', '40m','30m', '20m');
		CreateDistance($TourId, $TourType, 'LS_', '40m', '30m-2','30m-3', '20m');
		CreateDistance($TourId, $TourType, 'IS_', '40m', '30m-2','30m-3', '20m');
		CreateDistance($TourId, $TourType, 'RS_', '60m', '50m','40m', '30m');
		CreateDistance($TourId, $TourType, 'CS_', '60m', '50m','40m', '30m');
		CreateDistance($TourId, $TourType, 'BE_', '60m', '50m','40m', '30m');
		CreateDistance($TourId, $TourType, 'LE_', '40m', '30m-2','30m-3', '20m');
		CreateDistance($TourId, $TourType, 'IE_', '40m', '30m-2','30m-3', '20m');
		CreateDistance($TourId, $TourType, 'REH', '90m', '70m','50m', '30m');
		CreateDistance($TourId, $TourType, 'RED', '70m', '60m','50m', '30m');
		CreateDistance($TourId, $TourType, 'CEH', '90m', '70m','50m', '30m');
		CreateDistance($TourId, $TourType, 'CED', '70m', '60m','50m', '30m');
		break;

	case 3:
		CreateDistance($TourId, $TourType, '_K_', '20m-1', '20m-2');
		CreateDistance($TourId, $TourType, 'RC_', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'CC_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'BC_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'LC_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'IC_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'RJ_', '60m-1', '60m-2');
		CreateDistance($TourId, $TourType, 'CJ_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'BJ_', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'LJ_', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'IJ_', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'RV_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'CV_', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'BV_', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'LV_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'IV_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'RM_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'CM_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'BM_', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'LM_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'IM_', '30m-1', '30m-2');

		CreateDistance($TourId, $TourType, 'RS_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'CS_', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'BS_', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'LS_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'IS_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'RE_', '70m-1', '70m-2');
		CreateDistance($TourId, $TourType, 'CE_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'BE_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'LE_', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'IE_', '40m-1', '40m-2');
		break;

	case 5:
		CreateDistance($TourId, $TourType, '_K_', '20m-1', '20m-2','20m-3');
		CreateDistance($TourId, $TourType, '_C_', '40m', '30m','20m');
		CreateDistance($TourId, $TourType, 'LJ_', '40m', '30m','20m');
		CreateDistance($TourId, $TourType, 'LS_', '40m', '30m','20m');
		CreateDistance($TourId, $TourType, 'LM_', '40m', '30m','20m');
		CreateDistance($TourId, $TourType, 'LV_', '40m', '30m','20m');
		CreateDistance($TourId, $TourType, 'IJ_', '40m', '30m','20m');
		CreateDistance($TourId, $TourType, 'IS_', '40m', '30m','20m');
		CreateDistance($TourId, $TourType, 'IM_', '40m', '30m','20m');
		CreateDistance($TourId, $TourType, 'IV_', '40m', '30m','20m');
		CreateDistance($TourId, $TourType, 'BJ_', '40m', '30m','20m');
		CreateDistance($TourId, $TourType, 'RJ_', '60m', '50m','40m');
		CreateDistance($TourId, $TourType, 'CJ_', '60m', '50m','40m');
		CreateDistance($TourId, $TourType, 'RV_', '60m', '50m','40m');
		CreateDistance($TourId, $TourType, 'CV_', '60m', '50m','40m');
		CreateDistance($TourId, $TourType, 'BV_', '60m', '50m','40m');
		CreateDistance($TourId, $TourType, 'RM_', '60m', '50m','40m');
		CreateDistance($TourId, $TourType, 'CM_', '60m', '50m','40m');
		CreateDistance($TourId, $TourType, 'BM_', '60m', '50m','40m');
		CreateDistance($TourId, $TourType, 'RS_', '60m', '50m','40m');
		CreateDistance($TourId, $TourType, 'CS_', '60m', '50m','40m');
		CreateDistance($TourId, $TourType, 'BS_', '60m', '50m','40m');
		CreateDistance($TourId, $TourType, 'RE_', '60m', '50m','40m');
		CreateDistance($TourId, $TourType, 'BE_', '60m', '50m','40m');
		CreateDistance($TourId, $TourType, 'CE_', '60m', '50m','40m');
		CreateDistance($TourId, $TourType, 'LE_', '40m', '30m','20m');
		CreateDistance($TourId, $TourType, 'IE_', '40m', '30m','20m');
		break;
		
	case 6:
	case 22:
		CreateDistance($TourId, $TourType, '_K_', '12m-1', '12m-2');
		CreateDistance($TourId, $TourType, '_C_', '12m-1', '12m-2');
		CreateDistance($TourId, $TourType, '_J_', '18m-1', '18m-2');
		CreateDistance($TourId, $TourType, '_S_', '18m-1', '18m-2');
		CreateDistance($TourId, $TourType, '_M_', '18m-1', '18m-2');
		CreateDistance($TourId, $TourType, '_V_', '18m-1', '18m-2');
		CreateDistance($TourId, $TourType, '_E_', '18m-1', '18m-2');
		break;

	case 7:
		CreateDistance($TourId, $TourType, '_K_', '12m-1', '12m-2');
		CreateDistance($TourId, $TourType, '_C_', '12m-1', '12m-2');
		CreateDistance($TourId, $TourType, '_J_', '25m-1', '25m-2');
		CreateDistance($TourId, $TourType, '_S_', '25m-1', '25m-2');
		CreateDistance($TourId, $TourType, '_M_', '25m-1', '25m-2');
		CreateDistance($TourId, $TourType, '_V_', '25m-1', '25m-2');
		CreateDistance($TourId, $TourType, '_E_', '25m-1', '25m-2');
		break;

	case 39:
		CreateDistance($TourId, $TourType, 'CE_', '50m-1');
		CreateDistance($TourId, $TourType, 'CJH', '50m-1');

		CreateDistance($TourId, $TourType, 'CS_', '40m-1');
		CreateDistance($TourId, $TourType, 'CM_', '40m-1');
		CreateDistance($TourId, $TourType, 'CV_', '40m-1');
		CreateDistance($TourId, $TourType, 'CJD', '40m-1');

		CreateDistance($TourId, $TourType, 'RE_', '30m-1');
		CreateDistance($TourId, $TourType, 'BE_', '30m-1');
		CreateDistance($TourId, $TourType, 'RJ_', '30m-1');
		CreateDistance($TourId, $TourType, 'RS_', '30m-1');
		CreateDistance($TourId, $TourType, 'RM_', '30m-1');
		CreateDistance($TourId, $TourType, 'RV_', '30m-1');
		CreateDistance($TourId, $TourType, 'CC_', '30m-1');

		CreateDistance($TourId, $TourType, 'RC_', '20m-1');
		CreateDistance($TourId, $TourType, 'BC_', '20m-1');
		CreateDistance($TourId, $TourType, 'BJ_', '20m-1');
		CreateDistance($TourId, $TourType, 'BS_', '20m-1');
		CreateDistance($TourId, $TourType, 'BM_', '20m-1');
		CreateDistance($TourId, $TourType, 'BV_', '20m-1');
		CreateDistance($TourId, $TourType, 'IE_', '20m-1');
		CreateDistance($TourId, $TourType, 'IC_', '20m-1');
		CreateDistance($TourId, $TourType, 'IJ_', '20m-1');
		CreateDistance($TourId, $TourType, 'IS_', '20m-1');
		CreateDistance($TourId, $TourType, 'IM_', '20m-1');
		CreateDistance($TourId, $TourType, 'IV_', '20m-1');
		CreateDistance($TourId, $TourType, 'LE_', '20m-1');
		CreateDistance($TourId, $TourType, 'LC_', '20m-1');
		CreateDistance($TourId, $TourType, 'LJ_', '20m-1');
		CreateDistance($TourId, $TourType, 'LS_', '20m-1');
		CreateDistance($TourId, $TourType, 'LM_', '20m-1');
		CreateDistance($TourId, $TourType, 'LV_', '20m-1');
		CreateDistance($TourId, $TourType, '_K_', '20m-1');
		break;
}

if($TourType==6 || $TourType==3 || $TourType==1) {
	// default Events
	CreateStandardEvents($TourId, $TourType, $SubRule, $TourType!=6);

	// Classes in Events
	InsertStandardEvents($TourId, $TourType, $SubRule, $TourType!=6);

	// Finals & TeamFinals
	CreateFinals($TourId);
}

// Default Target
$i=1;
switch($TourType)
{
	case 1:  // Full FITA
		CreateTargetFace($TourId, $i++, '10 ring 122cm', '%K_', '1', 5, 122, 5, 122, 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '10 ring 122/80cm', 'REG-^[RBLI]{1,1}[CJESMV]{1,1}[HD]{1,1}', '1',  5, 122, 5, 122, 5, 80, 5, 80);
		CreateTargetFace($TourId, $i++, '10 ring 122/80cm', 'REG-^[C]{1,1}[CJESMV]{1,1}[HD]{1,1}', '1', 5, 122, 5, 122, 9, 80, 9, 80);
		break;

	case 3:  // 70m/50m round
		CreateTargetFace($TourId, $i++, '10 ring 122cm', '_K_', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '10 ring 122cm', 'REG-^[RBLI]{1,1}[CJESMV]{1,1}[HD]{1,1}', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '6 ring 80cm',   'REG-^[C]{1,1}[CJESMV]{1,1}[HD]{1,1}', '1', 9, 80, 9, 80);
		break;
		
	case 5:  // 900 round
		CreateTargetFace($TourId, $i++, '10 ring 122cm', '%', '1', 5, 122, 5, 122, 5, 122);
		break;

	case 6:  // Indoor 18m, 2 Dist - 60 arrows
		// KS 2015-11-16
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'REG-^[CRBLI]{1,1}[K]{1,1}[HD]{1,1}', '1', 1, 60, 1, 60);

		CreateTargetFace($TourId, $i++, '3 spot', 'R%', '1', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '3 spot', 'C%', '1', 4, 40, 4, 40);
		CreateTargetFace($TourId, $i++, '10 ring 40cm', 'B%', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'L%', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'I%', '1', 1, 60, 1, 60);

		CreateTargetFace($TourId, $i++, '10 ring 40cm', 'R%', '0', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '10 ring 40cm', 'C%', '0', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '3 spot', 'B%', '0', 2, 40, 2, 40);
		break;
		
	case 7:  // Indoor 25m, 2 Dist - 60 arrows
		// KS 2015-11-16
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'REG-^[CRBLI]{1,1}[K]{1,1}[HD]{1,1}', '1', 1, 60, 1, 60);

		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'R%', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '3 spot', 'R%', '0', 2, 60, 2, 60);
		CreateTargetFace($TourId, $i++, '3 spot', 'C%', '1', 4, 60, 4, 60);
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'C%', '0', 3, 60, 3, 60);
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'B%', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '3 spot', 'B%', '0', 2, 60, 2, 60);
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'L%', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'I%', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '10 ring 40cm', 'REG-^[CRB]{1,1}[C]{1,1}[HD]{1,1}', '1', 1, 40, 1, 40);
		
		break;

	case 22:  // Indoor 18m, 1 Dist - 30 arrows
		// KS 2015-11-16
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'REG-^[CRBLI]{1,1}[K]{1,1}[HD]{1,1}', '1', 1, 60);

		CreateTargetFace($TourId, $i++, '3 spot', 'R%', '1', 2, 40);
		CreateTargetFace($TourId, $i++, '3 spot', 'C%', '1', 4, 40);
		CreateTargetFace($TourId, $i++, '10 ring 40cm', 'B%', '1', 1, 40);
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'L%', '1', 1, 60);
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'I%', '1', 1, 60);
		CreateTargetFace($TourId, $i++, '10 ring 40cm', 'R%', '0', 1, 40);
		CreateTargetFace($TourId, $i++, '10 ring 40cm', 'C%', '0', 3, 40);
		CreateTargetFace($TourId, $i++, '3 spot', 'B%', '0', 2, 40);
		break;

	case 39:  // SBF36 round, 1 Dist
		// KS 2018-05-21
		CreateTargetFace($TourId, $i++, '10 ring 122cm', '_K_', '1', 5, 122);
		CreateTargetFace($TourId, $i++, '10 ring 80cm', 'REG-^[RBLI]{1,1}[CJESMV]{1,1}[HD]{1,1}', '1', 5, 80);
		CreateTargetFace($TourId, $i++, '6 ring 80cm',   'REG-^[RBLI]{1,1}[CJESMV]{1,1}[HD]{1,1}', '0', 9, 80);
		CreateTargetFace($TourId, $i++, '6 ring 80cm',   'REG-^[C]{1,1}[CJESMV]{1,1}[HD]{1,1}', '1', 9, 80);
		CreateTargetFace($TourId, $i++, '10 ring 80cm', 'REG-^[C]{1,1}[CJESMV]{1,1}[HD]{1,1}', '0', 5, 80);
		break;
		
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 16, 4);

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