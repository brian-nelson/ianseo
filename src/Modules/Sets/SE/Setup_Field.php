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
		CreateDistance($TourId, $TourType, '%', 'Hunter', 'Field');
		break;
}

// Default Target
$i=1;
switch($TourType) {
	case 10:
	case 12:
		CreateTargetFace($TourId, $i++, 'Red Peg', 'REG-^[CR]{1,1}[J]{0,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'White Peg', 'REG-^[RCBL]{1,1}[K]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Blue Peg', 'REG-^[B]{1,1}[J]{0,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Blue Peg', 'REG-^[CR]{1,1}[SMV]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Black Peg', 'REG-^[L]{1,1}[CJSMV]{0,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Black Peg', 'REG-^[RCB]{1,1}[C]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Black Peg', 'REG-^[B]{1,1}[SMV]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		/*
		
		CreateTargetFace($TourId, $i++, 'Blue Peg', 'REG-^[B]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Black Peg', 'REG-^[L]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Red Peg', 'REG-^[CB]{1,1}[V]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Blue Peg', 'REG-^[RL]{1,1}[S]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Black Peg', 'REG-^[CB]{1,1}[S]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Blue Peg', 'REG-^[RC]{1,1}[M]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Black Peg', 'REG-^[BL]{1,1}[M]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Blue Peg', 'REG-^[L]{1,1}[V]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Black Peg', 'REG-^[R]{1,1}[V]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		CreateTargetFace($TourId, $i++, 'Black Peg', 'REG-^[RCBL]{1,1}[J]{1,1}[HD]{1,1}', '1',  6, 0, 6, 0);
		
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