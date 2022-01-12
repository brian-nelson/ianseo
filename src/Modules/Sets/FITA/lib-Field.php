<?php

/*

STANDARD DEFINITIONS (Target Tournaments)

*/

/*

FIELD DEFINITIONS (Target Tournaments)

*/

function CreateStandardFieldClasses($TourId, $SubRule) {
	switch($SubRule) {
		case '1':
		case '3':
			CreateClass($TourId, 1, 21, 49, 0, 'M', 'M', 'Men');
			CreateClass($TourId, 2, 21, 49, 1, 'W', 'W', 'Women');
			CreateClass($TourId, 3, 18, 20, 0, 'U21M', 'U21M,M', 'Under 21 Men');
			CreateClass($TourId, 4, 18, 20, 1, 'U21W', 'U21W,W', 'Under 21 Women');
			CreateClass($TourId, 5, 1, 17, 0, 'U18M', 'U18M,U21M,M', 'Under 18 Men');
			CreateClass($TourId, 6, 1, 17, 1, 'U18W', 'U18W,U21W,W', 'Under 18 Women');
			CreateClass($TourId, 7, 50,100, 0, '50M', '50M,M', '50+ Men');
			CreateClass($TourId, 8, 50,100, 1, '50W', '50W,W', '50+ Women');
			break;
		case '2':
		case '4':
			CreateClass($TourId, 1, 21,100, 0, 'M', 'M', 'Men');
			CreateClass($TourId, 2, 21,100, 1, 'W', 'W', 'Women');
			CreateClass($TourId, 3, 1, 20, 0, 'U21M', 'U21M,M', 'Under 21 Men');
			CreateClass($TourId, 4, 1, 20, 1, 'U21W', 'U21W,W', 'Under 21 Women');
			break;
	}
}

function CreateStandardFieldEvents($TourId, $SubRule) {
	switch($SubRule) {
		case '1':
		case '2':
			$SettingsInd=array(
				'EvFinalFirstPhase' => '2',
				'EvFinalTargetType'=>TGT_FIELD,
				'EvElimEnds'=>12,
				'EvElimArrows'=>3,
				'EvElimSO'=>1,
				'EvFinEnds'=>4,
				'EvFinArrows'=>3,
				'EvFinSO'=>1,
				'EvElimType'=>2,
				'EvElim1'=>16,
				'EvE1Ends'=>12,
				'EvE1Arrows'=>3,
				'EvE1SO'=>1,
				'EvElim2'=>8,
				'EvE2Ends'=>8,
				'EvE2Arrows'=>3,
				'EvE2SO'=>1,
				'EvFinalAthTarget'=>0,
				'EvMatchArrowsNo'=>0,
			);
			$SettingsTeam=array(
				'EvTeamEvent' => '1',
				'EvFinalFirstPhase' => '4',
				'EvFinalTargetType'=>TGT_FIELD,
				'EvElimEnds'=>8,
				'EvElimArrows'=>3,
				'EvElimSO'=>3,
				'EvFinEnds'=>4,
				'EvFinArrows'=>3,
				'EvFinSO'=>3,
				'EvFinalAthTarget'=>15,
				'EvMatchArrowsNo'=>FINAL_FROM_2,
			);
            $SettingsMixedTeam=array(
                'EvTeamEvent' => '1',
                'EvMixedTeam' => '1',
                'EvFinalFirstPhase' => '4',
                'EvFinalTargetType'=>TGT_FIELD,
                'EvElimEnds'=>8,
                'EvElimArrows'=>4,
                'EvElimSO'=>2,
                'EvFinEnds'=>4,
                'EvFinArrows'=>4,
                'EvFinSO'=>2,
                'EvFinalAthTarget'=>15,
                'EvMatchArrowsNo'=>FINAL_FROM_2,
            );
			$i=1;
			CreateEventNew($TourId,'RM', 'Recurve Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'RW', 'Recurve Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'RU21M', 'Recurve Under 21 Men',$i++, $SettingsInd);
			CreateEventNew($TourId,'RU21W', 'Recurve Under 21 Women', $i++, $SettingsInd);
			if($SubRule==1) {
				CreateEventNew($TourId,'RU18M', 'Recurve Under 18 Men', $i++, $SettingsInd);
				CreateEventNew($TourId,'RU18W', 'Recurve Under 18 Women', $i++, $SettingsInd);
				CreateEventNew($TourId,'R50M', 'Recurve 50+ Men', $i++, $SettingsInd);
				CreateEventNew($TourId,'R50W', 'Recurve 50+ Women', $i++, $SettingsInd);
			}
			CreateEventNew($TourId,'CM', 'Compound Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'CW', 'Compound Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'CU21M', 'Compound Under 21 Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'CU21W', 'Compound Under 21 Women',$i++, $SettingsInd);
			if($SubRule==1) {
				CreateEventNew($TourId,'CU18M', 'Compound Under 18 Men', $i++, $SettingsInd);
				CreateEventNew($TourId,'CU18W', 'Compound Under 18 Women', $i++, $SettingsInd);
				CreateEventNew($TourId,'C50M', 'Compound 50+ Men', $i++, $SettingsInd);
				CreateEventNew($TourId,'C50W', 'Compound 50+ Women',$i++, $SettingsInd);
			}
			CreateEventNew($TourId,'BM', 'Barebow Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'BW', 'Barebow Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'BU21M', 'Barebow Under 21 Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'BU21W', 'Barebow Under 21 Women', $i++, $SettingsInd);
			if($SubRule==1) {
				CreateEventNew($TourId,'BU18M', 'Barebow Under 18 Men', $i++, $SettingsInd);
				CreateEventNew($TourId,'BU18W', 'Barebow Under 18 Women', $i++, $SettingsInd);
				CreateEventNew($TourId,'B50M', 'Barebow 50+ Men', $i++, $SettingsInd);
				CreateEventNew($TourId,'B50W', 'Barebow 50+ Women', $i++, $SettingsInd);
			}
			$i=1;
			CreateEventNew($TourId, 'MT', 'Men Team', $i++, $SettingsTeam);
			CreateEventNew($TourId, 'WT', 'Women Team', $i++, $SettingsTeam);
            CreateEventNew($TourId, 'RX', 'Recurve Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'CX', 'Compound Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'BX', 'Barebow Mixed Team', $i++, $SettingsMixedTeam);
			CreateEventNew($TourId, 'MU21T','Men Under 21 Team', $i++, $SettingsTeam);
			CreateEventNew($TourId, 'WU21T','Women Under 21 Team', $i++, $SettingsTeam);
            CreateEventNew($TourId, 'RU21X', 'Recurve Under 21 Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'CU21X', 'Compound Under 21 Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'BU21X', 'Barebow Under 21 Mixed Team', $i++, $SettingsMixedTeam);

        if($SubRule==1) {
                CreateEventNew($TourId, 'MU18T','Men Under 18 Team', $i++, $SettingsTeam);
                CreateEventNew($TourId, 'WU18T','Women Under 18 Team', $i++, $SettingsTeam);
                CreateEventNew($TourId, 'RU18X', 'Recurve Under 18 Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'CU18X', 'Compound Under 18 Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'BU18X', 'Barebow Under 18 Mixed Team', $i++, $SettingsMixedTeam);
				CreateEventNew($TourId, 'M50T','Men 50+ Team', $i++, $SettingsTeam);
				CreateEventNew($TourId, 'W50T','Women 50+ Team', $i++, $SettingsTeam);
                CreateEventNew($TourId, 'R50X', 'Recurve 50+ Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'C50X', 'Compound 50+ Mixed Team', $i++, $SettingsMixedTeam);
                CreateEventNew($TourId, 'B50X', 'Barebow 50+ Mixed Team', $i++, $SettingsMixedTeam);
			}
			break;
		case '3':
		case '4':
			$SettingsInd=array(
				'EvFinalFirstPhase' => '2',
				'EvFinalTargetType'=>TGT_FIELD,
				'EvElimEnds'=>6,
				'EvElimArrows'=>3,
				'EvElimSO'=>1,
				'EvFinEnds'=>4,
				'EvFinArrows'=>3,
				'EvFinSO'=>1,
				'EvElimType'=>4,
				'EvElim2'=>22,
				'EvFinalAthTarget'=>0,
				'EvMatchArrowsNo'=>FINAL_FROM_2,
			);
			$SettingsTeam=array(
				'EvTeamEvent' => '1',
				'EvFinalFirstPhase' => '4',
				'EvFinalTargetType'=>TGT_FIELD,
				'EvElimEnds'=>8,
				'EvElimArrows'=>3,
				'EvElimSO'=>3,
				'EvFinEnds'=>4,
				'EvFinArrows'=>3,
				'EvFinSO'=>3,
				'EvFinalAthTarget'=>15,
				'EvMatchArrowsNo'=>0,
			);
            $SettingsMixedTeam=array(
                'EvTeamEvent' => '1',
                'EvMixedTeam' => '1',
                'EvFinalFirstPhase' => '4',
                'EvFinalTargetType'=>TGT_FIELD,
                'EvElimEnds'=>8,
                'EvElimArrows'=>4,
                'EvElimSO'=>2,
                'EvFinEnds'=>4,
                'EvFinArrows'=>4,
                'EvFinSO'=>2,
                'EvFinalAthTarget'=>15,
                'EvMatchArrowsNo'=>FINAL_FROM_2,
            );
			$i=1;
			CreateEventNew($TourId,'RM', 'Recurve Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'RW', 'Recurve Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'RU21M', 'Recurve Under 21 Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'RU21W', 'Recurve Under 21 Women', $i++, $SettingsInd);
			if($SubRule==3) {
				CreateEventNew($TourId,'RU18M', 'Recurve Under 18 Men', $i++, $SettingsInd);
				CreateEventNew($TourId,'RU18W', 'Recurve Under 18 Women', $i++, $SettingsInd);
				CreateEventNew($TourId,'R50M', 'Recurve 50+ Men', $i++, $SettingsInd);
				CreateEventNew($TourId,'R50W', 'Recurve 50+ Women', $i++, $SettingsInd);
			}
			CreateEventNew($TourId,'CM', 'Compound Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'CW', 'Compound Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'CU21M', 'Compound Under 21 Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'CU21W', 'Compound Under 21 Women',$i++, $SettingsInd);
			if($SubRule==3) {
				CreateEventNew($TourId,'CU18M', 'Compound Under 18 Men', $i++, $SettingsInd);
				CreateEventNew($TourId,'CU18W', 'Compound Under 18 Women', $i++, $SettingsInd);
				CreateEventNew($TourId,'C50M', 'Compound 50+ Men', $i++, $SettingsInd);
				CreateEventNew($TourId,'C50W', 'Compound 50+ Women',$i++, $SettingsInd);
			}
			CreateEventNew($TourId,'BM', 'Barebow Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'BW', 'Barebow Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'BU21M', 'Barebow Under 21 Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'BU21W', 'Barebow Under 21 Women', $i++, $SettingsInd);
			if($SubRule==3) {
				CreateEventNew($TourId,'BU18M', 'Barebow Under 18 Men', $i++, $SettingsInd);
				CreateEventNew($TourId,'BU18W', 'Barebow Under 18 Women', $i++, $SettingsInd);
				CreateEventNew($TourId,'B50M', 'Barebow 50+ Men', $i++, $SettingsInd);
				CreateEventNew($TourId,'B50W', 'Barebow 50+ Women', $i++, $SettingsInd);
			}
			$i=1;
			CreateEventNew($TourId, 'MT', 'Men Team', $i++, $SettingsTeam);
            CreateEventNew($TourId, 'WT', 'Women Team', $i++, $SettingsTeam);
            CreateEventNew($TourId, 'RX', 'Recurve Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'CX', 'Compound Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'BX', 'Barebow Mixed Team', $i++, $SettingsMixedTeam);
			CreateEventNew($TourId, 'MU21T','Men Under 21 Team', $i++, $SettingsTeam);
			CreateEventNew($TourId, 'WU21T','Women Under 21 Team', $i++, $SettingsTeam);
            CreateEventNew($TourId, 'RU21X', 'Recurve Under 21 Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'CU21X', 'Compound Under 21 Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'BU21X', 'Barebow Under 21 Mixed Team', $i++, $SettingsMixedTeam);
			if($SubRule==3) {
				CreateEventNew($TourId, 'MU18T','Men Under 18 Team', $i++, $SettingsTeam);
				CreateEventNew($TourId, 'WU18T','Women Under 18 Team', $i++, $SettingsTeam);
				CreateEventNew($TourId, 'RU18X', 'Recurve Under 18 Mixed Team', $i++, $SettingsMixedTeam);
				CreateEventNew($TourId, 'CU18X', 'Compound Under 18 Mixed Team', $i++, $SettingsMixedTeam);
				CreateEventNew($TourId, 'BU18X', 'Barebow Under 18 Mixed Team', $i++, $SettingsMixedTeam);
				CreateEventNew($TourId, 'M50T','Men 50+ Team', $i++, $SettingsTeam);
				CreateEventNew($TourId, 'W50T','Women 50+ Team', $i++, $SettingsTeam);
				CreateEventNew($TourId, 'R50X', 'Recurve 50+ Mixed Team', $i++, $SettingsMixedTeam);
				CreateEventNew($TourId, 'C50X', 'Compound 50+ Mixed Team', $i++, $SettingsMixedTeam);
				CreateEventNew($TourId, 'B50X', 'Barebow 50+ Mixed Team', $i++, $SettingsMixedTeam);
			}
			break;
	}
}

function InsertStandardFieldEvents($TourId, $SubRule) {
    InsertClassEvent($TourId, 0, 1, 'RM', 'R', 'M');
    InsertClassEvent($TourId, 0, 1, 'RU21M', 'R', 'U21M');
    InsertClassEvent($TourId, 0, 1, 'RU18M', 'R', 'U18M');
    InsertClassEvent($TourId, 0, 1, 'R50M', 'R', '50M');
    InsertClassEvent($TourId, 0, 1, 'RW', 'R', 'W');
    InsertClassEvent($TourId, 0, 1, 'RU21W', 'R', 'U21W');
    InsertClassEvent($TourId, 0, 1, 'RU18W', 'R', 'U18W');
    InsertClassEvent($TourId, 0, 1, 'R50W', 'R', '50W');
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

    InsertClassEvent($TourId, 1, 1, 'MT', 'R', 'M');
    InsertClassEvent($TourId, 2, 1, 'MT', 'C', 'M');
    InsertClassEvent($TourId, 3, 1, 'MT', 'B', 'M');
    InsertClassEvent($TourId, 1, 1, 'WT', 'R', 'W');
    InsertClassEvent($TourId, 2, 1, 'WT', 'C', 'W');
    InsertClassEvent($TourId, 3, 1, 'WT', 'B', 'W');
    InsertClassEvent($TourId, 1, 1, 'RX', 'R', 'W');
    InsertClassEvent($TourId, 2, 1, 'RX', 'R', 'M');
    InsertClassEvent($TourId, 1, 1, 'CX', 'C', 'W');
    InsertClassEvent($TourId, 2, 1, 'CX', 'C', 'M');
    InsertClassEvent($TourId, 1, 1, 'BX', 'B', 'W');
    InsertClassEvent($TourId, 2, 1, 'BX', 'B', 'M');
    InsertClassEvent($TourId, 1, 1, 'MU21T', 'R', 'U21M');
    InsertClassEvent($TourId, 2, 1, 'MU21T', 'C', 'U21M');
    InsertClassEvent($TourId, 3, 1, 'MU21T', 'B', 'U21M');
    InsertClassEvent($TourId, 1, 1, 'WU21T', 'R', 'U21W');
    InsertClassEvent($TourId, 2, 1, 'WU21T', 'C', 'U21W');
    InsertClassEvent($TourId, 3, 1, 'WU21T', 'B', 'U21W');
    InsertClassEvent($TourId, 1, 1, 'RU21X', 'R', 'U21W');
    InsertClassEvent($TourId, 2, 1, 'RU21X', 'R', 'U21M');
    InsertClassEvent($TourId, 1, 1, 'CU21X', 'C', 'U21W');
    InsertClassEvent($TourId, 2, 1, 'CU21X', 'C', 'U21M');
    InsertClassEvent($TourId, 1, 1, 'BU21X', 'B', 'U21W');
    InsertClassEvent($TourId, 2, 1, 'BU21X', 'B', 'U21M');
    InsertClassEvent($TourId, 1, 1, 'MU18T', 'R', 'U18M');
    InsertClassEvent($TourId, 2, 1, 'MU18T', 'C', 'U18M');
    InsertClassEvent($TourId, 3, 1, 'MU18T', 'B', 'U18M');
    InsertClassEvent($TourId, 1, 1, 'WU18T', 'R', 'U18W');
    InsertClassEvent($TourId, 2, 1, 'WU18T', 'C', 'U18W');
    InsertClassEvent($TourId, 3, 1, 'WU18T', 'B', 'U18W');
    InsertClassEvent($TourId, 1, 1, 'RU18X', 'R', 'U18W');
    InsertClassEvent($TourId, 2, 1, 'RU18X', 'R', 'U18M');
    InsertClassEvent($TourId, 1, 1, 'CU18X', 'C', 'U18W');
    InsertClassEvent($TourId, 2, 1, 'CU18X', 'C', 'U18M');
    InsertClassEvent($TourId, 1, 1, 'BU18X', 'B', 'U18W');
    InsertClassEvent($TourId, 2, 1, 'BU18X', 'B', 'U18M');
    InsertClassEvent($TourId, 1, 1, 'M50T', 'R', '50M');
    InsertClassEvent($TourId, 2, 1, 'M50T', 'C', '50M');
    InsertClassEvent($TourId, 3, 1, 'M50T', 'B', '50M');
    InsertClassEvent($TourId, 1, 1, 'R50X', 'R', '50W');
    InsertClassEvent($TourId, 2, 1, 'R50X', 'R', '50M');
    InsertClassEvent($TourId, 1, 1, 'C50X', 'C', '50W');
    InsertClassEvent($TourId, 2, 1, 'C50X', 'C', '50M');
    InsertClassEvent($TourId, 1, 1, 'B50X', 'B', '50W');
    InsertClassEvent($TourId, 2, 1, 'B50X', 'B', '50M');
    InsertClassEvent($TourId, 1, 1, 'W50T', 'R', '50W');
    InsertClassEvent($TourId, 2, 1, 'W50T', 'C', '50W');
    InsertClassEvent($TourId, 3, 1, 'W50T', 'B', '50W');
}

function InsertStandardFieldEliminations($TourId, $SubRule){
	$cls=array();
	switch($SubRule) {
		case '1':
			$cls=array('M', 'W', 'U21M', 'U21W', 'U18M', 'U18W', '50M', '50W');
			break;
		case '2':
			$cls=array('M', 'W', 'U21M', 'U21W');
			break;
	}
	foreach(array('R', 'C', 'B') as $div) {
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

