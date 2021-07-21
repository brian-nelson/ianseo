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
		CreateDistanceNew($TourId, $TourType, 'CE_', array(array('Röd påle',0)));
		CreateDistanceNew($TourId, $TourType, 'CM_', array(array('Röd påle',0)));
		CreateDistanceNew($TourId, $TourType, 'RE_', array(array('Blå påle',0)));
		CreateDistanceNew($TourId, $TourType, 'BE_', array(array('Blå påle',0)));
		CreateDistanceNew($TourId, $TourType, 'LE_', array(array('Blå påle',0)));
		CreateDistanceNew($TourId, $TourType, 'IE_', array(array('Blå påle',0)));
		CreateDistanceNew($TourId, $TourType, 'CS_', array(array('Blå påle',0)));
		CreateDistanceNew($TourId, $TourType, 'CV_', array(array('Blå påle',0)));
		CreateDistanceNew($TourId, $TourType, 'RJ_', array(array('Blå påle',0)));
		CreateDistanceNew($TourId, $TourType, 'BJ_', array(array('Blå påle',0)));
		CreateDistanceNew($TourId, $TourType, 'LJ_', array(array('Blå påle',0)));
		CreateDistanceNew($TourId, $TourType, 'IJ_', array(array('Blå påle',0)));
		CreateDistanceNew($TourId, $TourType, 'RC_', array(array('Svart påle',0)));
		CreateDistanceNew($TourId, $TourType, 'CC_', array(array('Svart påle',0)));
		CreateDistanceNew($TourId, $TourType, 'BC_', array(array('Svart påle',0)));
		CreateDistanceNew($TourId, $TourType, 'LC_', array(array('Svart påle',0)));
		CreateDistanceNew($TourId, $TourType, 'IC_', array(array('Svart påle',0)));
		CreateDistanceNew($TourId, $TourType, 'RS_', array(array('Svart påle',0)));
		CreateDistanceNew($TourId, $TourType, 'RM_', array(array('Svart påle',0)));
		CreateDistanceNew($TourId, $TourType, 'RV_', array(array('Svart påle',0)));
		CreateDistanceNew($TourId, $TourType, 'BS_', array(array('Svart påle',0)));
		CreateDistanceNew($TourId, $TourType, 'BM_', array(array('Svart påle',0)));
		CreateDistanceNew($TourId, $TourType, 'BV_', array(array('Svart påle',0)));
		CreateDistanceNew($TourId, $TourType, 'LS_', array(array('Svart påle',0)));
		CreateDistanceNew($TourId, $TourType, 'LM_', array(array('Svart påle',0)));
		CreateDistanceNew($TourId, $TourType, 'LV_', array(array('Svart påle',0)));
		CreateDistanceNew($TourId, $TourType, 'IS_', array(array('Svart påle',0)));
		CreateDistanceNew($TourId, $TourType, 'IM_', array(array('Svart påle',0)));
		CreateDistanceNew($TourId, $TourType, 'IV_', array(array('Svart påle',0)));
		CreateDistanceNew($TourId, $TourType, '_K_', array(array('Vit påle',0)));
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
