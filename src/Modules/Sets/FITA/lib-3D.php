<?php

/*

3D DEFINITIONS (Target Tournaments)

*/
function CreateStandard3DClasses($TourId, $SubRule) {
	switch($SubRule) {
		case '1':
		case '3':
			CreateClass($TourId, 1, 21, 49, 0, 'M', 'M', 'Men');
			CreateClass($TourId, 2, 21, 49, 1, 'W', 'W', 'Women');
			CreateClass($TourId, 3, 18, 20, 0, 'U21M', 'U21M,M', 'Under 21 Men');
			CreateClass($TourId, 4, 18, 20, 1, 'U21W', 'U21W,W', 'Under 21 Women');
			CreateClass($TourId, 5, 1, 17, 0, 'U18M', 'U18M,U21M,M', 'Cadet Men');
			CreateClass($TourId, 6, 1, 17, 1, 'U18W', 'U18W,U21W,W', 'Cadet Women');
			CreateClass($TourId, 7, 50,100, 0, '50M', '50M,M', '50+ Men');
			CreateClass($TourId, 8, 50,100, 1, '50W', '50W,W', '50+ Women');
			break;
		case '2':
		case '4':
			CreateClass($TourId, 1, 1,100, 0, 'M', 'M', 'Men');
			CreateClass($TourId, 2, 1,100, 1, 'W', 'W', 'Women');
			break;
	}
}


function CreateStandard3DEvents($TourId, $SubRule) {
	$Elim1=array(
		'Archers' => 16,
		'Ends' => 12,
		'Arrows' => 1,
		'SO' => 1
	);
	$Elim2=array(
		'Archers' => 8,
		'Ends' => 8,
		'Arrows' => 1,
		'SO' => 1
	);
	switch($SubRule) {
        case '1':
        case '2':
            $SettingsInd = array(
                'EvFinalFirstPhase' => '2',
                'EvFinalTargetType' => TGT_3D,
                'EvElimEnds' => 12,
                'EvElimArrows' => 1,
                'EvElimSO' => 1,
                'EvFinEnds' => 4,
                'EvFinArrows' => 1,
                'EvFinSO' => 1,
                'EvElimType' => 2,
                'EvElim1' => 16,
                'EvE1Ends' => 12,
                'EvE1Arrows' => 1,
                'EvE1SO' => 1,
                'EvElim2' => 8,
                'EvE2Ends' => 8,
                'EvE2Arrows' => 1,
                'EvE2SO' => 1,
                'EvFinalAthTarget' => MATCH_NO_SEP,
                'EvMatchArrowsNo' => FINAL_FROM_2
            );
            $SettingsTeam = array(
                'EvTeamEvent' => '1',
                'EvFinalFirstPhase' => '2',
                'EvFinalTargetType' => TGT_3D,
                'EvElimEnds' => 8,
                'EvElimArrows' => 3,
                'EvElimSO' => 3,
                'EvFinEnds' => 4,
                'EvFinArrows' => 3,
                'EvFinSO' => 3,
                'EvFinalAthTarget' => MATCH_NO_SEP,
                'EvMatchArrowsNo' => FINAL_FROM_2,
            );
            $SettingsMixedTeam = array(
                'EvTeamEvent' => '1',
                'EvMixedTeam' => '1',
                'EvFinalFirstPhase' => '2',
                'EvFinalTargetType' => TGT_3D,
                'EvElimEnds' => 8,
                'EvElimArrows' => 2,
                'EvElimSO' => 2,
                'EvFinEnds' => 4,
                'EvFinArrows' => 2,
                'EvFinSO' => 2,
                'EvFinalAthTarget' => MATCH_NO_SEP,
                'EvMatchArrowsNo' => FINAL_FROM_2,
            );

            $i = 1;
            CreateEventNew($TourId, 'CM', 'Compound Men', $i++, $SettingsInd);
            CreateEventNew($TourId, 'CW', 'Compound Women', $i++, $SettingsInd);
            if ($SubRule == 1) {
                CreateEventNew($TourId, 'CU21M', 'Compound Under 21 Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'CU21W', 'Compound Under 21 Women', $i++, $SettingsInd);
                CreateEventNew($TourId, 'CU18M', 'Compound Cadet Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'CU18W', 'Compound Cadet Women', $i++, $SettingsInd);
                CreateEventNew($TourId, 'C50M', 'Compound 50+ Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'C50W', 'Compound 50+ Women', $i++, $SettingsInd);
            }
            CreateEventNew($TourId, 'BM', 'Barebow Men', $i++, $SettingsInd);
            CreateEventNew($TourId, 'BW', 'Barebow Women', $i++, $SettingsInd);
            if ($SubRule == 1) {
                CreateEventNew($TourId, 'BU21M', 'Barebow Under 21 Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'BU21W', 'Barebow Under 21 Women', $i++, $SettingsInd);
                CreateEventNew($TourId, 'BU18M', 'Barebow Under 18 Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'BU18W', 'Barebow Under 18 Women', $i++, $SettingsInd);
                CreateEventNew($TourId, 'B50M', 'Barebow 50+ Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'B50W', 'Barebow 50+ Women', $i++, $SettingsInd);
            }
            CreateEventNew($TourId, 'LM', 'Longbow Men', $i++, $SettingsInd);
            CreateEventNew($TourId, 'LW', 'Longbow Women', $i++, $SettingsInd);
            if ($SubRule == 1) {
                CreateEventNew($TourId, 'LU21M', 'Longbow Under 21 Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'LU21W', 'Longbow Under 21 Women', $i++, $SettingsInd);
                CreateEventNew($TourId, 'LU18M', 'Longbow Under 18 Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'LU18W', 'Longbow Under 18 Women', $i++, $SettingsInd);
                CreateEventNew($TourId, 'L50M', 'Longbow 50+ Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'L50W', 'Longbow 50+ Women', $i++, $SettingsInd);
            }
            CreateEventNew($TourId, 'TM', 'Traditional Men', $i++, $SettingsInd);
            CreateEventNew($TourId, 'TW', 'Traditional Women', $i++, $SettingsInd);
            if ($SubRule == 1) {
                CreateEventNew($TourId, 'TU21M', 'Traditional Under 21 Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'TU21W', 'Traditional Under 21 Women', $i++, $SettingsInd);
                CreateEventNew($TourId, 'TU18M', 'Traditional Under 18 Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'TU18W', 'Traditional Under 18 Women', $i++, $SettingsInd);
                CreateEventNew($TourId, 'T50M', 'Traditional 50+ Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'T50W', 'Traditional 50+ Women', $i++, $SettingsInd);
            }
            $i = 1;
            CreateEventNew($TourId, 'MT', 'Men Team', $i++, $SettingsTeam);
            CreateEventNew($TourId, 'WT', 'Women Team', $i++, $SettingsTeam);
            CreateEventNew($TourId, 'CX', 'Compound Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'BX', 'Barebow Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'LX', 'Longbow Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'TX', 'Traditional Mixed Team', $i++, $SettingsMixedTeam);

            if ($SubRule == 1) {
                CreateEventNew($TourId, 'MU21T', 'Men Under 21 Team', $i++, $SettingsTeam);
                CreateEventNew($TourId, 'WU21T', 'Women Under 21 Team', $i++, $SettingsTeam);
                CreateEventNew($TourId, 'CU21X', 'Compound Under 21 Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'BU21X', 'Barebow Under 21 Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'LU21X', 'Longbow Under 21 Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'TU21X', 'Traditional Under 21 Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'MU18T', 'Men Under 18 Team', $i++, $SettingsTeam);
                CreateEventNew($TourId, 'WU18T', 'Women Under 18 Team', $i++, $SettingsTeam);
                CreateEventNew($TourId, 'CU18X', 'Compound Under 18 Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'BU18X', 'Barebow Under 18 Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'LU18X', 'Longbow Under 18 Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'TU18X', 'Traditional Under 18 Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'M50T', 'Men 50+ Team', $i++, $SettingsTeam);
                CreateEventNew($TourId, 'W50T', 'Women 50+ Team', $i++, $SettingsTeam);
                CreateEventNew($TourId, 'C50X', 'Compound 50+ Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'B50X', 'Barebow 50+ Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'L50X', 'Longbow 50+ Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'T50X', 'Traditional 50+ Mixed Team', $i++, $SettingsMixedTeam);
            }
            break;
        case '3':
        case '4':
            $SettingsInd = array(
                'EvFinalFirstPhase' => '2',
                'EvFinalTargetType' => TGT_3D,
                'EvElimEnds' => 6,
                'EvElimArrows' => 1,
                'EvElimSO' => 1,
                'EvFinEnds' => 4,
                'EvFinArrows' => 1,
                'EvFinSO' => 1,
                'EvElimType' => 4,
                'EvElim2' => 22,
                'EvFinalAthTarget' => MATCH_NO_SEP,
                'EvMatchArrowsNo' => FINAL_FROM_2,
            );
            $SettingsTeam = array(
                'EvTeamEvent' => '1',
                'EvFinalFirstPhase' => '4',
                'EvFinalTargetType' => TGT_3D,
                'EvElimEnds' => 8,
                'EvElimArrows' => 3,
                'EvElimSO' => 3,
                'EvFinEnds' => 4,
                'EvFinArrows' => 3,
                'EvFinSO' => 3,
                'EvFinalAthTarget' => MATCH_NO_SEP,
                'EvMatchArrowsNo' => FINAL_FROM_2,
            );
            $SettingsMixedTeam = array(
                'EvTeamEvent' => '1',
                'EvMixedTeam' => '1',
                'EvFinalFirstPhase' => '2',
                'EvFinalTargetType' => TGT_3D,
                'EvElimEnds' => 8,
                'EvElimArrows' => 2,
                'EvElimSO' => 2,
                'EvFinEnds' => 4,
                'EvFinArrows' => 2,
                'EvFinSO' => 2,
                'EvFinalAthTarget' => MATCH_NO_SEP,
                'EvMatchArrowsNo' => FINAL_FROM_2,
            );

            $i = 1;
            CreateEventNew($TourId, 'CM', 'Compound Men', $i++, $SettingsInd);
            CreateEventNew($TourId, 'CW', 'Compound Women', $i++, $SettingsInd);
            if ($SubRule == 3) {
                CreateEventNew($TourId, 'CU21M', 'Compound Under 21 Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'CU21W', 'Compound Under 21 Women', $i++, $SettingsInd);
                CreateEventNew($TourId, 'CU18M', 'Compound Under 18 Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'CU18W', 'Compound Under 18 Women', $i++, $SettingsInd);
                CreateEventNew($TourId, 'C50M', 'Compound 50+ Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'C50W', 'Compound 50+ Women', $i++, $SettingsInd);
            }
            CreateEventNew($TourId, 'BM', 'Barebow Men', $i++, $SettingsInd);
            CreateEventNew($TourId, 'BW', 'Barebow Women', $i++, $SettingsInd);
            if ($SubRule == 3) {
                CreateEventNew($TourId, 'BU21M', 'Barebow Under 21 Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'BU21W', 'Barebow Under 21 Women', $i++, $SettingsInd);
                CreateEventNew($TourId, 'BU18M', 'Barebow Under 18 Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'BU18W', 'Barebow Under 18 Women', $i++, $SettingsInd);
                CreateEventNew($TourId, 'B50M', 'Barebow 50+ Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'B50W', 'Barebow 50+ Women', $i++, $SettingsInd);
            }
            CreateEventNew($TourId, 'LM', 'Longbow Men', $i++, $SettingsInd);
            CreateEventNew($TourId, 'LW', 'Longbow Women', $i++, $SettingsInd);
            if ($SubRule == 3) {
                CreateEventNew($TourId, 'LU21M', 'Longbow Under 21 Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'LU21W', 'Longbow Under 21 Women', $i++, $SettingsInd);
                CreateEventNew($TourId, 'LU18M', 'Longbow Under 18 Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'LU18W', 'Longbow Under 18 Women', $i++, $SettingsInd);
                CreateEventNew($TourId, 'L50M', 'Longbow 50+ Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'L50W', 'Longbow 50+ Women', $i++, $SettingsInd);
            }
            CreateEventNew($TourId, 'TM', 'Traditional Men', $i++, $SettingsInd);
            CreateEventNew($TourId, 'TW', 'Traditional Women', $i++, $SettingsInd);
            if ($SubRule == 3) {
                CreateEventNew($TourId, 'TU21M', 'Traditional Under 21 Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'TU21W', 'Traditional Under 21 Women', $i++, $SettingsInd);
                CreateEventNew($TourId, 'TU18M', 'Traditional Under 18 Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'TU18W', 'Traditional Under 18 Women', $i++, $SettingsInd);
                CreateEventNew($TourId, 'T50M', 'Traditional 50+ Men', $i++, $SettingsInd);
                CreateEventNew($TourId, 'T50W', 'Traditional 50+ Women', $i++, $SettingsInd);
            }
            $i = 1;
            CreateEventNew($TourId, 'MT', 'Men Team', $i++, $SettingsTeam);
            CreateEventNew($TourId, 'WT', 'Women Team', $i++, $SettingsTeam);
            CreateEventNew($TourId, 'CX', 'Compound Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'BX', 'Barebow Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'LX', 'Longbow Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'TX', 'Traditional Mixed Team', $i++, $SettingsMixedTeam);
            if ($SubRule == 3) {
                CreateEventNew($TourId, 'MU21T', 'Men Under 21 Team', $i++, $SettingsTeam);
                CreateEventNew($TourId, 'WU21T', 'Women Under 21 Team', $i++, $SettingsTeam);
                CreateEventNew($TourId, 'CU21X', 'Compound Under 21 Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'BU21X', 'Barebow Under 21 Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'LU21X', 'Longbow Under 21 Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'TU21X', 'Traditional Under 21 Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'MU18T', 'Men Under 18 Team', $i++, $SettingsTeam);
                CreateEventNew($TourId, 'WU18T', 'Women Under 18 Team', $i++, $SettingsTeam);
                CreateEventNew($TourId, 'CU18X', 'Compound Under 18 Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'BU18X', 'Barebow Under 18 Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'LU18X', 'Longbow Under 18 Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'TU18X', 'Traditional Under 18 Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'M50T', 'Men 50+ Team', $i++, $SettingsTeam);
                CreateEventNew($TourId, 'W50T', 'Women 50+ Team', $i++, $SettingsTeam);
                CreateEventNew($TourId, 'C50X', 'Compound 50+ Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'B50X', 'Barebow 50+ Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'L50X', 'Longbow 50+ Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'T50X', 'Traditional 50+ Mixed Team', $i++, $SettingsMixedTeam);
            }
            break;
	}
}

function InsertStandard3DEvents($TourId, $SubRule)
{
    InsertClassEvent($TourId, 0, 1, 'CM', 'C', 'M');
    InsertClassEvent($TourId, 0, 1, 'CU21M', 'C', 'U21M');
    InsertClassEvent($TourId, 0, 1, 'CU18M', 'C', 'U18M');
    InsertClassEvent($TourId, 0, 1, 'C50M', 'C', '50M');
    InsertClassEvent($TourId, 0, 1, 'CW', 'C', 'W');
    InsertClassEvent($TourId, 0, 1, 'CU21W', 'C', 'U21W');
    InsertClassEvent($TourId, 0, 1, 'CU18W', 'C', 'U18W');
    InsertClassEvent($TourId, 0, 1, 'C50W', 'C', '50W');
    InsertClassEvent($TourId, 0, 1, 'BM', 'B', 'M');
    InsertClassEvent($TourId, 0, 1, 'BU21M', 'B', 'U21M');
    InsertClassEvent($TourId, 0, 1, 'BU18M', 'B', 'U18M');
    InsertClassEvent($TourId, 0, 1, 'B50M', 'B', '50M');
    InsertClassEvent($TourId, 0, 1, 'BW', 'B', 'W');
    InsertClassEvent($TourId, 0, 1, 'BU21W', 'B', 'U21W');
    InsertClassEvent($TourId, 0, 1, 'BU18W', 'B', 'U18W');
    InsertClassEvent($TourId, 0, 1, 'B50W', 'B', '50W');
    InsertClassEvent($TourId, 0, 1, 'LM', 'L', 'M');
    InsertClassEvent($TourId, 0, 1, 'LU21M', 'L', 'U21M');
    InsertClassEvent($TourId, 0, 1, 'LU18M', 'L', 'U18M');
    InsertClassEvent($TourId, 0, 1, 'L50M', 'L', '50M');
    InsertClassEvent($TourId, 0, 1, 'LW', 'L', 'W');
    InsertClassEvent($TourId, 0, 1, 'LU21W', 'L', 'U21W');
    InsertClassEvent($TourId, 0, 1, 'LU18W', 'L', 'U18W');
    InsertClassEvent($TourId, 0, 1, 'L50W', 'L', '50W');
    InsertClassEvent($TourId, 0, 1, 'TM', 'T', 'M');
    InsertClassEvent($TourId, 0, 1, 'TU21M', 'T', 'U21M');
    InsertClassEvent($TourId, 0, 1, 'TU18M', 'T', 'U18M');
    InsertClassEvent($TourId, 0, 1, 'T50M', 'T', '50M');
    InsertClassEvent($TourId, 0, 1, 'TW', 'T', 'W');
    InsertClassEvent($TourId, 0, 1, 'TU21W', 'T', 'U21W');
    InsertClassEvent($TourId, 0, 1, 'TU18W', 'T', 'U18W');
    InsertClassEvent($TourId, 0, 1, 'T50W', 'T', '50W');

    InsertClassEvent($TourId, 1, 1, 'MT', 'C', 'M');
    InsertClassEvent($TourId, 2, 1, 'MT', 'L', 'M');
    InsertClassEvent($TourId, 3, 1, 'MT', 'B', 'M');
    InsertClassEvent($TourId, 3, 1, 'MT', 'T', 'M');
    InsertClassEvent($TourId, 1, 1, 'WT', 'C', 'W');
    InsertClassEvent($TourId, 2, 1, 'WT', 'L', 'W');
    InsertClassEvent($TourId, 3, 1, 'WT', 'B', 'W');
    InsertClassEvent($TourId, 3, 1, 'WT', 'T', 'W');
    InsertClassEvent($TourId, 1, 1, 'CX', 'C', 'W');
    InsertClassEvent($TourId, 2, 1, 'CX', 'C', 'M');
    InsertClassEvent($TourId, 1, 1, 'LX', 'L', 'W');
    InsertClassEvent($TourId, 2, 1, 'LX', 'L', 'M');
    InsertClassEvent($TourId, 1, 1, 'BX', 'B', 'W');
    InsertClassEvent($TourId, 2, 1, 'BX', 'B', 'M');
    InsertClassEvent($TourId, 1, 1, 'TX', 'T', 'W');
    InsertClassEvent($TourId, 2, 1, 'TX', 'T', 'M');
    InsertClassEvent($TourId, 1, 1, 'MU21T', 'C', 'U21M');
    InsertClassEvent($TourId, 2, 1, 'MU21T', 'L', 'U21M');
    InsertClassEvent($TourId, 3, 1, 'MU21T', 'B', 'U21M');
    InsertClassEvent($TourId, 3, 1, 'MU21T', 'T', 'U21M');
    InsertClassEvent($TourId, 1, 1, 'WU21T', 'C', 'U21W');
    InsertClassEvent($TourId, 2, 1, 'WU21T', 'L', 'U21W');
    InsertClassEvent($TourId, 3, 1, 'WU21T', 'B', 'U21W');
    InsertClassEvent($TourId, 3, 1, 'WU21T', 'T', 'U21W');
    InsertClassEvent($TourId, 1, 1, 'CU21X', 'C', 'U21W');
    InsertClassEvent($TourId, 2, 1, 'CU21X', 'C', 'U21M');
    InsertClassEvent($TourId, 1, 1, 'LU21X', 'L', 'U21W');
    InsertClassEvent($TourId, 2, 1, 'LU21X', 'L', 'U21M');
    InsertClassEvent($TourId, 1, 1, 'BU21X', 'B', 'U21W');
    InsertClassEvent($TourId, 2, 1, 'BU21X', 'B', 'U21M');
    InsertClassEvent($TourId, 1, 1, 'TU21X', 'T', 'U21W');
    InsertClassEvent($TourId, 2, 1, 'TU21X', 'T', 'U21M');
    InsertClassEvent($TourId, 1, 1, 'MU18T', 'C', 'U18M');
    InsertClassEvent($TourId, 2, 1, 'MU18T', 'L', 'U18M');
    InsertClassEvent($TourId, 3, 1, 'MU18T', 'B', 'U18M');
    InsertClassEvent($TourId, 3, 1, 'MU18T', 'T', 'U18M');
    InsertClassEvent($TourId, 1, 1, 'WU18T', 'C', 'U18W');
    InsertClassEvent($TourId, 2, 1, 'WU18T', 'L', 'U18W');
    InsertClassEvent($TourId, 3, 1, 'WU18T', 'B', 'U18W');
    InsertClassEvent($TourId, 3, 1, 'WU18T', 'T', 'U18W');
    InsertClassEvent($TourId, 1, 1, 'CU18X', 'C', 'U18W');
    InsertClassEvent($TourId, 2, 1, 'CU18X', 'C', 'U18M');
    InsertClassEvent($TourId, 1, 1, 'LU18X', 'L', 'U18W');
    InsertClassEvent($TourId, 2, 1, 'LU18X', 'L', 'U18M');
    InsertClassEvent($TourId, 1, 1, 'BU18X', 'B', 'U18W');
    InsertClassEvent($TourId, 2, 1, 'BU18X', 'B', 'U18M');
    InsertClassEvent($TourId, 1, 1, 'TU18X', 'T', 'U18W');
    InsertClassEvent($TourId, 2, 1, 'TU18X', 'T', 'U18M');
    InsertClassEvent($TourId, 1, 1, 'M50T', 'C', '50M');
    InsertClassEvent($TourId, 2, 1, 'M50T', 'L', '50M');
    InsertClassEvent($TourId, 3, 1, 'M50T', 'B', '50M');
    InsertClassEvent($TourId, 3, 1, 'M50T', 'T', '50M');
    InsertClassEvent($TourId, 1, 1, 'W50T', 'C', '50W');
    InsertClassEvent($TourId, 2, 1, 'W50T', 'L', '50W');
    InsertClassEvent($TourId, 3, 1, 'W50T', 'B', '50W');
    InsertClassEvent($TourId, 3, 1, 'W50T', 'T', '50W');
    InsertClassEvent($TourId, 1, 1, 'C50X', 'C', '50W');
    InsertClassEvent($TourId, 2, 1, 'C50X', 'C', '50M');
    InsertClassEvent($TourId, 1, 1, 'L50X', 'L', '50W');
    InsertClassEvent($TourId, 2, 1, 'L50X', 'L', '50M');
    InsertClassEvent($TourId, 1, 1, 'B50X', 'B', '50W');
    InsertClassEvent($TourId, 2, 1, 'B50X', 'B', '50M');
    InsertClassEvent($TourId, 1, 1, 'T50X', 'T', '50W');
    InsertClassEvent($TourId, 2, 1, 'T50X', 'T', '50M');
}

function InsertStandard3DEliminations($TourId, $SubRule){
	$cls=array();
	switch($SubRule) {
		case '1':
			$cls=array('M', 'W', 'U21M', 'U21W', 'U18M', 'U18W', '50M', '50W');
			break;
		case '2':
			$cls=array('M', 'W');
			break;
	}
	foreach(array('C', 'B', 'L', 'T') as $div) {
		foreach($cls as $cl) {
			for($n=1; $n<=16; $n++) {
				safe_w_SQL("INSERT INTO Eliminations set ElId=0, ElElimPhase=0, ElEventCode='$div$cl', ElTournament=$TourId, ElQualRank=$n");
			}
			for($n=1; $n<=8; $n++) {
				safe_w_SQL("INSERT INTO Eliminations set ElId=0, ElElimPhase=1, ElEventCode='$div$cl', ElTournament=$TourId, ElQualRank=$n");
			}
		}
	}
}
