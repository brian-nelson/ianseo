<?php
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType);

// default SubClasses
CreateStandardSubClasses($TourId);

// default Classes
CreateStandardClasses($TourId, $SubRule, '3D'); //

// default Distances
switch($TourType) {
	case 11:
		CreateDistance($TourId, $TourType, 'CE_', 'Röd påle');
		CreateDistance($TourId, $TourType, 'CM_', 'Röd påle');
		CreateDistance($TourId, $TourType, 'CJ_', 'Röd påle');

		CreateDistance($TourId, $TourType, 'RE_', 'Blå påle');
		CreateDistance($TourId, $TourType, 'BE_', 'Blå påle');
		CreateDistance($TourId, $TourType, 'LE_', 'Blå påle');
		CreateDistance($TourId, $TourType, 'IE_', 'Blå påle');
		CreateDistance($TourId, $TourType, 'CS_', 'Blå påle');
		CreateDistance($TourId, $TourType, 'CV_', 'Blå påle');
		CreateDistance($TourId, $TourType, 'RJ_', 'Blå påle');
		CreateDistance($TourId, $TourType, 'BJ_', 'Blå påle');
		CreateDistance($TourId, $TourType, 'LJ_', 'Blå påle');
		CreateDistance($TourId, $TourType, 'IJ_', 'Blå påle');

		CreateDistance($TourId, $TourType, 'RC_', 'Svart påle');
		CreateDistance($TourId, $TourType, 'CC_', 'Svart påle');
		CreateDistance($TourId, $TourType, 'BC_', 'Svart påle');
		CreateDistance($TourId, $TourType, 'LC_', 'Svart påle');
		CreateDistance($TourId, $TourType, 'IC_', 'Svart påle');
		CreateDistance($TourId, $TourType, 'RS_', 'Svart påle');
		CreateDistance($TourId, $TourType, 'RM_', 'Svart påle');
		CreateDistance($TourId, $TourType, 'RV_', 'Svart påle');
		CreateDistance($TourId, $TourType, 'BS_', 'Svart påle');
		CreateDistance($TourId, $TourType, 'BM_', 'Svart påle');
		CreateDistance($TourId, $TourType, 'BV_', 'Svart påle');
		CreateDistance($TourId, $TourType, 'LS_', 'Svart påle');
		CreateDistance($TourId, $TourType, 'LM_', 'Svart påle');
		CreateDistance($TourId, $TourType, 'LV_', 'Svart påle');
		CreateDistance($TourId, $TourType, 'IS_', 'Svart påle');
		CreateDistance($TourId, $TourType, 'IM_', 'Svart påle');
		CreateDistance($TourId, $TourType, 'IV_', 'Svart påle');

		CreateDistance($TourId, $TourType, '_K_', 'Vit påle');
		break;
}

// Default Target
$i=1;
switch($TourType) {
	case 11:
		CreateTargetFace($TourId, $i++, 'Röd påle', 'REG-^[C]{1,1}[EMJ]{0,1}[HD]{1,1}', '1', 8, 0);
		CreateTargetFace($TourId, $i++, 'Blå påle', 'REG-^[RBLI]{1,1}[EJ]{0,1}[HD]{1,1}', '1', 8, 0);
		CreateTargetFace($TourId, $i++, 'Blå påle', 'REG-^[C]{1,1}[SV]{1,1}[HD]{1,1}', '1', 8, 0);
		CreateTargetFace($TourId, $i++, 'Svart påle', 'REG-^[RBLI]{1,1}[SMV]{1,1}[HD]{1,1}', '1', 8, 0);
		CreateTargetFace($TourId, $i++, 'Svart påle', 'REG-^[RCBLI]{1,1}[C]{1,1}[HD]{1,1}', '1', 8, 0);
		CreateTargetFace($TourId, $i++, 'Vit påle', 'REG-^[RCBLI]{1,1}[K]{1,1}[HD]{1,1}', '1', 8, 0);
		break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 20, 4);

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