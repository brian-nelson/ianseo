<?php
require_once(dirname(dirname(__FILE__)).'/lib.php');
$tourCollation = '';

CreateDivision($TourId, 1, 'Y', 'Yumi');
CreateClass($TourId, 1, 0, 100, -1, 'U', 'U', 'Archer');

CreateDistanceNew($TourId, $TourType, '%', array(array('Kinteki', 28), array('S.O.', 28)));
CreateTargetFace($TourId, 1, '~Default', '%', '1', TGT_KYUDO, 36, TGT_KYUDO, 36);

CreateDistanceInformation($TourId, $DistanceInfoArray, 32, 1);

$Settings=array(
    'EvFinalFirstPhase' => '0',
    'EvFinalTargetType'=>TGT_KYUDO,
    'EvTargetSize'=>10,
    'EvDistance'=>28,
    'EvElimEnds'=>20,
    'EvElimArrows'=>2,
    'EvElimSO'=>2,
    'EvFinEnds'=>0,
    'EvFinArrows'=>0,
    'EvFinSO'=>0,
    'EvElimType'=>1,
    'EvElim2'=>16,
    'EvE2Ends'=>20,
    'EvE2Arrows'=>2,
    'EvE2SO'=>2,
    'EvFinalAthTarget'=>0,
    'EvMatchArrowsNo'=>0,
);
CreateEventNew($TourId, 'Elim', 'Individual Eliminations', 1, $Settings);
InsertClassEvent($TourId, 0, 1, 'Elim', 'Y',  'U');

$Settings=array(
    'EvTeamEvent' => 1,
    'EvFinalFirstPhase' => '4',
    'EvFinalTargetType'=>TGT_KYUDO,
    'EvTargetSize'=>10,
    'EvDistance'=>28,
    'EvElimEnds'=>4,
    'EvElimArrows'=>3,
    'EvElimSO'=>3,
    'EvFinEnds'=>4,
    'EvFinArrows'=>3,
    'EvFinSO'=>3,
    'EvFinalAthTarget'=>0,
    'EvMatchArrowsNo'=>0,
);
CreateEventNew($TourId, 'Team', 'Team Eliminations', 1, $Settings);
InsertClassEvent($TourId, 1, 3, 'Team', 'Y',  'U');

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