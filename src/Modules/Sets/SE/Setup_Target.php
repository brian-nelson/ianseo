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
		CreateDistance($TourId, $TourType, 'RC_', '50m', '40m','30m', '20m');
		CreateDistance($TourId, $TourType, 'CC_', '50m', '40m','30m', '20m');
		CreateDistance($TourId, $TourType, 'LJ_', '40m', '30m-2','30m-3', '20m');
		CreateDistance($TourId, $TourType, 'BJ_', '50m', '40m','30m', '20m');
		CreateDistance($TourId, $TourType, 'RJH', '70m', '60m','50m', '30m');
		CreateDistance($TourId, $TourType, 'RJD', '60m', '50m','40m', '30m');
		CreateDistance($TourId, $TourType, 'CJH', '70m', '60m','50m', '30m');
		CreateDistance($TourId, $TourType, 'CJD', '60m', '50m','40m', '30m');
		CreateDistance($TourId, $TourType, 'BV_', '50m', '40m','30m', '20m');
		CreateDistance($TourId, $TourType, 'LV_', '40m', '30m-2','30m-3', '20m');
		CreateDistance($TourId, $TourType, 'RV_', '60m', '50m','40m', '30m');
		CreateDistance($TourId, $TourType, 'CV_', '60m', '50m','40m', '30m');

		CreateDistance($TourId, $TourType, 'BM_', '50m', '40m','30m', '20m');
		CreateDistance($TourId, $TourType, 'LM_', '40m', '30m-2','30m-3', '20m');
		CreateDistance($TourId, $TourType, 'RM_', '60m', '50m','40m', '30m');
		CreateDistance($TourId, $TourType, 'CM_', '60m', '50m','40m', '30m');

		CreateDistance($TourId, $TourType, 'BS_', '50m', '40m','30m', '20m');
		CreateDistance($TourId, $TourType, 'LS_', '40m', '30m-2','30m-3', '20m');
		CreateDistance($TourId, $TourType, 'RS_', '60m', '50m','40m', '30m');
		CreateDistance($TourId, $TourType, 'CS_', '60m', '50m','40m', '30m');
		CreateDistance($TourId, $TourType, 'B_', '60m', '50m','40m', '30m');
		CreateDistance($TourId, $TourType, 'L_', '40m', '30m-2','30m-3', '20m');
		CreateDistance($TourId, $TourType, 'RH', '90m', '70m','50m', '30m');
		CreateDistance($TourId, $TourType, 'RD', '70m', '60m','50m', '30m');
		CreateDistance($TourId, $TourType, 'CH', '90m', '70m','50m', '30m');
		CreateDistance($TourId, $TourType, 'CD', '70m', '60m','50m', '30m');
		break;

	case 3:
		CreateDistance($TourId, $TourType, '_K_', '20m-1', '20m-2');
		CreateDistance($TourId, $TourType, 'RC_', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'CC_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'BC_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'LC_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'RJ_', '60m-1', '60m-2');
		CreateDistance($TourId, $TourType, 'CJ_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'BJ_', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'LJ_', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'RV_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'CV_', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'BV_', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'LV_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'RM_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'CM_', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'BM_', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'LM_', '30m-1', '30m-2');

		CreateDistance($TourId, $TourType, 'RS_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'CS_', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'BS_', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'LS_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'R_', '70m-1', '70m-2');
		CreateDistance($TourId, $TourType, 'C_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'B_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'L_', '40m-1', '40m-2');
		break;

	case 5:
		CreateDistance($TourId, $TourType, '_K_', '20m-1', '20m-2','20m-3');
		CreateDistance($TourId, $TourType, '_C_', '40m', '30m','20m');
		CreateDistance($TourId, $TourType, 'LJ_', '40m', '30m','20m');
		CreateDistance($TourId, $TourType, 'LS_', '40m', '30m','20m');
		CreateDistance($TourId, $TourType, 'LM_', '40m', '30m','20m');
		CreateDistance($TourId, $TourType, 'LV_', '40m', '30m','20m');
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
		CreateDistance($TourId, $TourType, 'R_', '60m', '50m','40m');
		CreateDistance($TourId, $TourType, 'B_', '60m', '50m','40m');
		CreateDistance($TourId, $TourType, 'C_', '60m', '50m','40m');
		CreateDistance($TourId, $TourType, 'L_', '40m', '30m','20m');
		break;
	case 6:
		CreateDistance($TourId, $TourType, '%K_', '12m-1', '12m-2');
		CreateDistance($TourId, $TourType, '%C_', '12m-1', '12m-2');
		CreateDistance($TourId, $TourType, '%J_', '18m-1', '18m-2');
		CreateDistance($TourId, $TourType, '%S_', '18m-1', '18m-2');
		CreateDistance($TourId, $TourType, '%M_', '18m-1', '18m-2');
		CreateDistance($TourId, $TourType, '%V_', '18m-1', '18m-2');
		break;

	case 7:
		CreateDistance($TourId, $TourType, '%K_', '12m-1', '12m-2');
		CreateDistance($TourId, $TourType, '%C_', '12m-1', '12m-2');
		CreateDistance($TourId, $TourType, '%J_', '25m-1', '25m-2');
		CreateDistance($TourId, $TourType, '%S_', '25m-1', '25m-2');
		CreateDistance($TourId, $TourType, '%M_', '25m-1', '25m-2');
		CreateDistance($TourId, $TourType, '%V_', '25m-1', '25m-2');
		break;

	case 22:
		CreateDistance($TourId, $TourType, '%K_', '12m-1');
		CreateDistance($TourId, $TourType, '%C_', '12m-1');
		CreateDistance($TourId, $TourType, '%J_', '18m-1');
		CreateDistance($TourId, $TourType, '%S_', '18m-1');
		CreateDistance($TourId, $TourType, '%M_', '18m-1');
		CreateDistance($TourId, $TourType, '%V_', '18m-1');
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
	case 1:
		CreateTargetFace($TourId, $i++, '~Default', '%K_', '1', 5, 122, 5, 122, 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '~Default', 'REG-^[RBL]{1,1}[CJMV]{1,1}[HD]{1,1}', '1',  5, 122, 5, 122, 5, 80, 5, 80);
		CreateTargetFace($TourId, $i++, '~DefaultCO', 'REG-^[C]{1,1}[CJMV]{1,1}[HD]{1,1}', '1', 5, 122, 5, 122, 9, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '~Default', 'REG-^[RBL]{1,1}[S]{0,1}[HD]{1,1}', '1', 5, 122, 5, 122, 5, 80, 5, 80);
		CreateTargetFace($TourId, $i++, '~DefaultCO', 'REG-^[C]{1,1}[S]{0,1}[HD]{1,1}', '1', 5, 122, 5, 122, 9, 80, 9, 80);
		
		break;

	case 3:
		CreateTargetFace($TourId, $i++, '~Default', '_K_', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '~Default', 'REG-^[RBL]{1,1}[CJMV]{1,1}[HD]{1,1}', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '~DefaultCO', 'REG-^[C]{1,1}[CJMV]{1,1}[HD]{1,1}', '1', 9, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '~Default', 'REG-^[RBL]{1,1}[S]{1,1}[HD]{1,1}', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '~DefaultCO', 'REG-^[C]{1,1}[S]{1,1}[HD]{1,1}', '1', 9, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '~Default', 'REG-^[RBL]{1,1}[HD]{1,1}', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '~DefaultCO', 'REG-^[C]{1,1}[HD]{1,1}', '1', 9, 80, 9, 80);
		break;
	case 5:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1', 5, 122, 5, 122, 5, 122);
		break;

	case 6:
		CreateTargetFace($TourId, $i++, '10 ring', 'REG-[FKL]{1,1}K[HD]{1,1}', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '3 spot', 'REG-CK[HD]{1,1}', '1', 3, 60, 3, 60);
		CreateTargetFace($TourId, $i++, '10 ring', 'REG-[FKL]{1,1}Y[HD]{1,1}', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '3 spot', 'REG-CY[HD]{1,1}', '1', 4, 40, 4, 40);

		CreateTargetFace($TourId, $i++, '10 ring', 'REG-[FK]{1,1}[JS]{1,1}[HD]{1,1}', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '10 ring', 'REG-L[JS]{1,1}[HD]{1,1}', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '3 spot', 'REG-C[JS]{1,1}[HD]{1,1}', '1', 4, 40, 4, 40);

		CreateTargetFace($TourId, $i++, '10 ring', 'REG-[FK]{1,1}[MV]{1,1}[HD]{1,1}', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '3 spot', 'REG-C[MV]{1,1}[HD]{1,1}', '1', 4, 40, 4, 40);
		break;

	case 7:
		CreateTargetFace($TourId, $i++, '10 ring', 'REG-[FKL]{1,1}K[HD]{1,1}', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '3 spot', 'REG-CK[HD]{1,1}', '1', 3, 60, 3, 60);

		CreateTargetFace($TourId, $i++, '10 ring', 'REG-[FKL]{1,1}Y[HD]{1,1}', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '3 spot', 'REG-CY[HD]{1,1}', '1', 4, 40, 4, 40);

		CreateTargetFace($TourId, $i++, '10 ring', 'REG-[FKL]{1,1}[JS]{1,1}[HD]{1,1}', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '3 spot', 'REG-C[JS]{1,1}[HD]{1,1}', '1', 4, 60, 4, 60);

		CreateTargetFace($TourId, $i++, '10 ring', 'REG-[FK]{1,1}[MV]{1,1}[HD]{1,1}', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '3 spot', 'REG-C[MV]{1,1}[HD]{1,1}', '1', 4, 60, 4, 60);
		break;

	case 22:
		CreateTargetFace($TourId, $i++, '~Default', 'REG-[FKL]{1,1}K[HD]{1,1}', '1', 1, 60);
		CreateTargetFace($TourId, $i++, '~DefaultCO', 'REG-CK[HD]{1,1}', '1', 3, 60);
		CreateTargetFace($TourId, $i++, '~Default', 'REG-[FKL]{1,1}Y[HD]{1,1}', '1', 1, 40);
		CreateTargetFace($TourId, $i++, '~DefaultCO', 'REG-CY[HD]{1,1}', '1', 4, 40);

		CreateTargetFace($TourId, $i++, '~Default', 'REG-[FK]{1,1}[JS]{1,1}[HD]{1,1}', '1', 1, 40);
		CreateTargetFace($TourId, $i++, '~Default', 'REG-L[JS]{1,1}[HD]{1,1}', '1', 1, 60);
		CreateTargetFace($TourId, $i++, '~DefaultCO', 'REG-C[JS]{1,1}[HD]{1,1}', '1', 4, 40);

		CreateTargetFace($TourId, $i++, '~Default', 'REG-[FK]{1,1}[MV]{1,1}[HD]{1,1}', '1', 1, 40);
		CreateTargetFace($TourId, $i++, '~DefaultCO', 'REG-C[MV]{1,1}[HD]{1,1}', '1', 4, 40);
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