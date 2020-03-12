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
		CreateDistance($TourId, $TourType, '_K_', 'Omärkt-Vit', 'Märkt-Vit');
		CreateDistance($TourId, $TourType, 'RE_', 'Omärkt-Röd', 'Märkt-Röd');
		CreateDistance($TourId, $TourType, 'RC_', 'Omärkt-Svt', 'Märkt-Svt');
		CreateDistance($TourId, $TourType, 'RJ_', 'Omärkt-Röd', 'Märkt-Röd');
		CreateDistance($TourId, $TourType, 'RS_', 'Omärkt-Blå', 'Märkt-Blå');
		CreateDistance($TourId, $TourType, 'RM_', 'Omärkt-Blå', 'Märkt-Blå');
		CreateDistance($TourId, $TourType, 'RV_', 'Omärkt-Blå', 'Märkt-Blå');
		CreateDistance($TourId, $TourType, 'BE_', 'Omärkt-Blå', 'Märkt-Blå');
		CreateDistance($TourId, $TourType, 'BC_', 'Omärkt-SvV', 'Märkt-SvV');
		CreateDistance($TourId, $TourType, 'BJ_', 'Omärkt-Blå', 'Märkt-Blå');
		CreateDistance($TourId, $TourType, 'BS_', 'Omärkt-Svt', 'Märkt-Svt');
		CreateDistance($TourId, $TourType, 'BM_', 'Omärkt-Svt', 'Märkt-Svt');
		CreateDistance($TourId, $TourType, 'BV_', 'Omärkt-Svt', 'Märkt-Svt');
		CreateDistance($TourId, $TourType, 'CE_', 'Omärkt-Röd', 'Märkt-Röd');
		CreateDistance($TourId, $TourType, 'CC_', 'Omärkt-Svt', 'Märkt-Svt');
		CreateDistance($TourId, $TourType, 'CJ_', 'Omärkt-Röd', 'Märkt-Röd');
		CreateDistance($TourId, $TourType, 'CS_', 'Omärkt-Blå', 'Märkt-Blå');
		CreateDistance($TourId, $TourType, 'CM_', 'Omärkt-Röd', 'Märkt-Röd');
		CreateDistance($TourId, $TourType, 'CV_', 'Omärkt-Blå', 'Märkt-Blå');
		CreateDistance($TourId, $TourType, 'LE_', 'Omärkt-Svt', 'Märkt-Svt');
		CreateDistance($TourId, $TourType, 'LC_', 'Omärkt-SvV', 'Märkt-SvV');
		CreateDistance($TourId, $TourType, 'LJ_', 'Omärkt-Svt', 'Märkt-Svt');
		CreateDistance($TourId, $TourType, 'LS_', 'Omärkt-SvV', 'Märkt-SvV');
		CreateDistance($TourId, $TourType, 'LM_', 'Omärkt-SvV', 'Märkt-SvV');
		CreateDistance($TourId, $TourType, 'LV_', 'Omärkt-SvV', 'Märkt-SvV');
		CreateDistance($TourId, $TourType, 'IE_', 'Omärkt-Svt', 'Märkt-Svt');
		CreateDistance($TourId, $TourType, 'IC_', 'Omärkt-SvV', 'Märkt-SvV');
		CreateDistance($TourId, $TourType, 'IJ_', 'Omärkt-Svt', 'Märkt-Svt');
		CreateDistance($TourId, $TourType, 'IS_', 'Omärkt-SvV', 'Märkt-SvV');
		CreateDistance($TourId, $TourType, 'IM_', 'Omärkt-SvV', 'Märkt-SvV');
		CreateDistance($TourId, $TourType, 'IV_', 'Omärkt-SvV', 'Märkt-SvV');
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