<?php

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType);

// default SubClasses
CreateStandardSubClasses($TourId);

// default Classes
CreateStandardClasses($TourId, $SubRule, 'FIELD'); // $SubRule force to 1 (ALL CLASSES)

// default Distances
switch($TourType) {
	case 10:
	case 12:
		CreateDistanceNew($TourId, $TourType, '_K_', array(array('Omärkt-Vit',0), array('Märkt-Vit',0)));
		CreateDistanceNew($TourId, $TourType, 'RE_', array(array('Omärkt-Röd',0), array('Märkt-Röd',0)));
		CreateDistanceNew($TourId, $TourType, 'RC_', array(array('Omärkt-Svt',0), array('Märkt-Svt',0)));
		CreateDistanceNew($TourId, $TourType, 'RJ_', array(array('Omärkt-Röd',0), array('Märkt-Röd',0)));
		CreateDistanceNew($TourId, $TourType, 'RS_', array(array('Omärkt-Blå',0), array('Märkt-Blå',0)));
		CreateDistanceNew($TourId, $TourType, 'RM_', array(array('Omärkt-Blå',0), array('Märkt-Blå',0)));
		CreateDistanceNew($TourId, $TourType, 'RV_', array(array('Omärkt-Blå',0), array('Märkt-Blå',0)));
		CreateDistanceNew($TourId, $TourType, 'BE_', array(array('Omärkt-Blå',0), array('Märkt-Blå',0)));
		CreateDistanceNew($TourId, $TourType, 'BC_', array(array('Omärkt-SvV',0), array('Märkt-SvV',0)));
		CreateDistanceNew($TourId, $TourType, 'BJ_', array(array('Omärkt-Blå',0), array('Märkt-Blå',0)));
		CreateDistanceNew($TourId, $TourType, 'BS_', array(array('Omärkt-Svt',0), array('Märkt-Svt',0)));
		CreateDistanceNew($TourId, $TourType, 'BM_', array(array('Omärkt-Svt',0), array('Märkt-Svt',0)));
		CreateDistanceNew($TourId, $TourType, 'BV_', array(array('Omärkt-Svt',0), array('Märkt-Svt',0)));
		CreateDistanceNew($TourId, $TourType, 'CE_', array(array('Omärkt-Röd',0), array('Märkt-Röd',0)));
		CreateDistanceNew($TourId, $TourType, 'CC_', array(array('Omärkt-Svt',0), array('Märkt-Svt',0)));
		CreateDistanceNew($TourId, $TourType, 'CJ_', array(array('Omärkt-Röd',0), array('Märkt-Röd',0)));
		CreateDistanceNew($TourId, $TourType, 'CS_', array(array('Omärkt-Blå',0), array('Märkt-Blå',0)));
		CreateDistanceNew($TourId, $TourType, 'CM_', array(array('Omärkt-Röd',0), array('Märkt-Röd',0)));
		CreateDistanceNew($TourId, $TourType, 'CV_', array(array('Omärkt-Blå',0), array('Märkt-Blå',0)));
		CreateDistanceNew($TourId, $TourType, 'LE_', array(array('Omärkt-Svt',0), array('Märkt-Svt',0)));
		CreateDistanceNew($TourId, $TourType, 'LC_', array(array('Omärkt-SvV',0), array('Märkt-SvV',0)));
		CreateDistanceNew($TourId, $TourType, 'LJ_', array(array('Omärkt-Svt',0), array('Märkt-Svt',0)));
		CreateDistanceNew($TourId, $TourType, 'LS_', array(array('Omärkt-SvV',0), array('Märkt-SvV',0)));
		CreateDistanceNew($TourId, $TourType, 'LM_', array(array('Omärkt-SvV',0), array('Märkt-SvV',0)));
		CreateDistanceNew($TourId, $TourType, 'LV_', array(array('Omärkt-SvV',0), array('Märkt-SvV',0)));
		CreateDistanceNew($TourId, $TourType, 'IE_', array(array('Omärkt-Svt',0), array('Märkt-Svt',0)));
		CreateDistanceNew($TourId, $TourType, 'IC_', array(array('Omärkt-SvV',0), array('Märkt-SvV',0)));
		CreateDistanceNew($TourId, $TourType, 'IJ_', array(array('Omärkt-Svt',0), array('Märkt-Svt',0)));
		CreateDistanceNew($TourId, $TourType, 'IS_', array(array('Omärkt-SvV',0), array('Märkt-SvV',0)));
		CreateDistanceNew($TourId, $TourType, 'IM_', array(array('Omärkt-SvV',0), array('Märkt-SvV',0)));
		CreateDistanceNew($TourId, $TourType, 'IV_', array(array('Omärkt-SvV',0), array('Märkt-SvV',0)));
		break;
}

// Default Target
$i=1;
switch($TourType) {
	case 10:
	case 12:
		CreateTargetFace($TourId, $i++, 'Röd påle', 'REG-^[C]{1,1}[EMJ]{0,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Röd påle', 'REG-^[R]{1,1}[EJ]{0,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Blå påle', 'REG-^[B]{1,1}[EJ]{0,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Blå påle', 'REG-^[C]{1,1}[SV]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Blå påle', 'REG-^[R]{1,1}[SMV]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Svart påle', 'REG-^[LI]{1,1}[EJ]{0,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Svart påle', 'REG-^[RC]{1,1}[C]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Svart påle', 'REG-^[B]{1,1}[SMV]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Svart/vit påle', 'REG-^[LI]{1,1}[CSMV]{0,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Svart/vit påle', 'REG-^[B]{1,1}[C]{0,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Vit påle', 'REG-^[RCBLI]{1,1}[K]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		/*

		CreateTargetFace($TourId, $i++, 'Blå påle', 'REG-^[B]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Svart påle', 'REG-^[L]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Red Peg', 'REG-^[CB]{1,1}[V]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Blå påle', 'REG-^[RL]{1,1}[S]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Svart påle', 'REG-^[CB]{1,1}[S]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Blå påle', 'REG-^[RC]{1,1}[M]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Svart påle', 'REG-^[BL]{1,1}[M]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Blå påle', 'REG-^[L]{1,1}[V]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Svart påle', 'REG-^[R]{1,1}[V]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Svart påle', 'REG-^[RCBL]{1,1}[J]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);

		*/
		break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, ($TourType==10 ? 24 : 12));

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
