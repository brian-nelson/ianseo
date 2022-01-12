<?php
/*
11 	3D 	(1 distance)

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (11)

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, '3D', $SubRule);

// default SubClasses
//CreateSubClass($TourId, 1, '00', '00');

// default Classes
CreateStandardClasses($TourId, $SubRule);

// default Distances
switch($TourType) {
	case 11:
		CreateDistanceNew($TourId, $TourType, '%', array(array('Kurs',0)));
		break;
	case 13:
		CreateDistanceNew($TourId, $TourType, '%', array(array('Kurs 1',0), array('Kurs 2',0)));
		break;
}

// default Events
CreateStandard3DEvents($TourId, $SubRule);

// insert class in events
InsertStandard3DEvents($TourId, $SubRule);

// Elimination rounds
InsertStandard3DEliminations($TourId, $SubRule);

// Finals & TeamFinals
CreateFinals($TourId);

// Default Target
switch($TourType) {
	case 11:
        switch ($SubRule) {
            case 1:
            case 3:
                CreateTargetFace($TourId, 1, 'Weiß', 'REG-^BU13|^BU15|^LU13|^LU15|^TU13|^TU15', '1', TGT_3D, 0);
                CreateTargetFace($TourId, 2, 'Blau', 'REG-^RU13|^RU15|^CU13|^CU15', '1', TGT_3D, 0);
                CreateTargetFace($TourId, 3, 'Blau', 'REG-^BU18|^BU21|^B50|^B65|^B[M|W]$|^LU18|^LU21|^L50|^L65|^L[M|W]$|^TU18|^TU21|^T50|^T65|^T[M|W]$', '1', TGT_3D, 0);
                CreateTargetFace($TourId, 4, 'Rot', 'REG-^RU18|^RU21|^R50|^R65|^R[M|W]$|^CU18|^CU21|^C50|^C65|^C[M|W]$', '1', TGT_3D, 0);
                break;
            case 2:
            case 4:
                CreateTargetFace($TourId, 1, 'Blau', 'REG-^B|^L|^T', '1', TGT_3D, 0);
                CreateTargetFace($TourId, 2, 'Rot', 'REG-^R|^C', '1', TGT_3D, 0);
                break;
        }
        break;
    case 13:
        switch ($SubRule) {
            case 1:
            case 3:
                CreateTargetFace($TourId, 1, 'Weiß', 'REG-^BU13|^BU15|^LU13|^LU15|^TU13|^TU15', '1', TGT_3D, 0, TGT_3D, 0);
                CreateTargetFace($TourId, 2, 'Blau', 'REG-^RU13|^RU15|^CU13|^CU15', '1', TGT_3D, 0, TGT_3D, 0);
                CreateTargetFace($TourId, 3, 'Blau', 'REG-REG-^BU18|^BU21|^B50|^B65|^B[M|W]$|^LU18|^LU21|^L50|^L65|^L[M|W]$|^LU18|^LU21|^T50|^T65|^T[M|W]$', '1', TGT_3D, 0, TGT_3D, 0);
                CreateTargetFace($TourId, 4, 'Rot', 'REG-^RU18|^RU21|^R50|^R65|^RB[M|W]$|^CU18|^CU21|^C50|^C65|^C[M|W]$', '1', TGT_3D, 0, TGT_3D, 0);
                break;
            case 2:
            case 4:
                CreateTargetFace($TourId, 1, 'Blau', 'REG-^B|^L|^T', '1', TGT_3D, 0, TGT_3D, 0);
                CreateTargetFace($TourId, 2, 'Rot', 'REG-^R|^C', '1', TGT_3D, 0, TGT_3D, 0);
                break;
        }
        break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, $tourDetNumEnds, 6);

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

