<?php

/*

FIELD DEFINITIONS (Target Tournaments)

*/

function CreateStandardFieldEvents($TourId, $SubRule) {
	switch($SubRule) {
		case '1':
		case '2':
			$SettingsInd=array(
				'EvFinalFirstPhase' => '2',
				'EvFinalTargetType'=>6,
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
				'EvFinalAthTarget'=>MATCH_NO_SEP,
				'EvMatchArrowsNo'=>0,
			);
			$SettingsTeam=array(
				'EvTeamEvent' => '1',
				'EvFinalFirstPhase' => '4',
				'EvFinalTargetType'=>6,
				'EvElimEnds'=>4,
				'EvElimArrows'=>3,
				'EvElimSO'=>3,
				'EvFinEnds'=>4,
				'EvFinArrows'=>3,
				'EvFinSO'=>3,
				'EvFinalAthTarget'=>15,
				'EvMatchArrowsNo'=>248,
                'EvMultiTeam'=>1,
                'EvMultiTeamNo'=>2,
                'EvPartialTeam'=>1
			);
            $SettingsMixedTeam=array(
                'EvTeamEvent' => '1',
                'EvMixedTeam' => '1',
                'EvFinalFirstPhase' => '4',
                'EvFinalTargetType'=>TGT_FIELD,
                'EvElimEnds'=>4,
                'EvElimArrows'=>4,
                'EvElimSO'=>2,
                'EvFinEnds'=>4,
                'EvFinArrows'=>4,
                'EvFinSO'=>2,
                'EvFinalAthTarget'=>15,
                'EvMatchArrowsNo'=>FINAL_FROM_2,
                'EvMultiTeam'=>1,
                'EvMultiTeamNo'=>3,
                'EvPartialTeam'=>0
            );
            $i = 1;
            CreateEventNew($TourId,'LW', 'Langbogen Damen', $i++, $SettingsInd);
            CreateEventNew($TourId,'LM', 'Langbogen Herren', $i++, $SettingsInd);
            CreateEventNew($TourId,'TW', 'Traditional Damen', $i, $SettingsInd);
            CreateEventNew($TourId,'TM', 'Traditional Herren', $i++, $SettingsInd);
            CreateEventNew($TourId,'BW', 'Blankbogen Damen', $i++, $SettingsInd);
            CreateEventNew($TourId,'BM', 'Blankbogen Herren', $i++, $SettingsInd);
            CreateEventNew($TourId,'CW', 'Compound Damen', $i++, $SettingsInd);
            CreateEventNew($TourId,'CM', 'Compound Herren', $i++, $SettingsInd);
            CreateEventNew($TourId,'RW', 'Recurve Damen', $i++, $SettingsInd);
            CreateEventNew($TourId,'RM', 'Recurve Herren', $i++, $SettingsInd);
            $i = 1;
            CreateEventNew($TourId, 'LX', 'Langbogen Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'TX', 'Traditional Mixed Team', $i, $SettingsMixedTeam);
            CreateEventNew($TourId, 'BX', 'Blankbogen Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'CX', 'Compound Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'RX', 'Recurve Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'WT', 'Damen Team', $i++, $SettingsTeam);
            CreateEventNew($TourId, 'MT', 'Herren Team', $i++, $SettingsTeam);
			break;
		case '3':
		case '4':
			$SettingsInd=array(
				'EvFinalFirstPhase' => '2',
				'EvFinalTargetType'=>6,
				'EvElimEnds'=>6,
				'EvElimArrows'=>3,
				'EvElimSO'=>1,
				'EvFinEnds'=>4,
				'EvFinArrows'=>3,
				'EvFinSO'=>1,
				'EvElimType'=>4,
				'EvElim2'=>22,
				'EvFinalAthTarget'=>MATCH_NO_SEP,
				'EvMatchArrowsNo'=>248,
			);
			$SettingsTeam=array(
				'EvTeamEvent' => '1',
				'EvFinalFirstPhase' => '4',
				'EvFinalTargetType'=>6,
				'EvElimEnds'=>8,
				'EvElimArrows'=>3,
				'EvElimSO'=>3,
				'EvFinEnds'=>4,
				'EvFinArrows'=>3,
				'EvFinSO'=>3,
				'EvFinalAthTarget'=>15,
				'EvMatchArrowsNo'=>0,
                'EvMultiTeam'=>1,
                'EvMultiTeamNo'=>2,
                'EvPartialTeam'=>1
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
                'EvMultiTeam'=>1,
                'EvMultiTeamNo'=>3,
                'EvPartialTeam'=>0
            );
            $i = 1;
            CreateEventNew($TourId,'LW', 'Langbogen Damen', $i++, $SettingsInd);
            CreateEventNew($TourId,'LM', 'Langbogen Herren', $i++, $SettingsInd);
            CreateEventNew($TourId,'TW', 'Traditional Damen', $i, $SettingsInd);
            CreateEventNew($TourId,'TM', 'Traditional Herren', $i++, $SettingsInd);
            CreateEventNew($TourId,'BW', 'Blankbogen Damen', $i++, $SettingsInd);
            CreateEventNew($TourId,'BM', 'Blankbogen Herren', $i++, $SettingsInd);
            CreateEventNew($TourId,'CW', 'Compound Damen', $i++, $SettingsInd);
            CreateEventNew($TourId,'CM', 'Compound Herren', $i++, $SettingsInd);
            CreateEventNew($TourId,'RW', 'Recurve Damen', $i++, $SettingsInd);
            CreateEventNew($TourId,'RM', 'Recurve Herren', $i++, $SettingsInd);
            $i = 1;
            CreateEventNew($TourId, 'LX', 'Langbogen Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'TX', 'Traditional Mixed Team', $i, $SettingsMixedTeam);
            CreateEventNew($TourId, 'BX', 'Blankbogen Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'CX', 'Compound Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'RX', 'Recurve Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'WT', 'Damen Team', $i++, $SettingsTeam);
            CreateEventNew($TourId, 'MT', 'Herren Team', $i++, $SettingsTeam);
    break;
	}
}

function InsertStandardFieldEvents($TourId, $SubRule) {
    foreach (array('R'=>'R','C'=>'C','B'=>'B','L'=>'L','T'=>'T') as $kDiv=>$vDiv) {
        $clsTmpArr = array('W','U18W','U21W','50W','65W');
        foreach($clsTmpArr as $kClass=>$vClass) {
            if($vClass=='CW' and $kDiv!='L') {
                continue;
            }
            InsertClassEvent($TourId, 0, 1, $vDiv.'W', $kDiv,  $vClass);
            if($kDiv=='R' OR $kDiv=='C' OR $kDiv=='B') {
                InsertClassEvent($TourId, ($kDiv == 'R' ? 1 : ($kDiv == 'C' ? 2 : 3)), 1, 'WT', $kDiv, $vClass);
            }
            InsertClassEvent($TourId, 1, 1, $vDiv.'X', $kDiv, $vClass);
        }
        $clsTmpArr = array('M','U18M','U21M','50','50M','65','65M');
        foreach($clsTmpArr as $kClass=>$vClass) {
            if($vClass=='CM' and $kDiv!='L') {
                continue;
            }
            InsertClassEvent($TourId, 0, 1, $vDiv.'M', $kDiv,  $vClass);
            if($kDiv=='R' OR $kDiv=='C' OR $kDiv=='B') {
                InsertClassEvent($TourId, ($kDiv == 'R' ? 1 : ($kDiv == 'C' ? 2 : 3)), 1, 'MT', $kDiv, $vClass);
            }
            if(substr($vClass,-1,1) != 'U') {
                InsertClassEvent($TourId, 2, 1, $vDiv . 'X', $kDiv, $vClass);
            }
        }
    }
}

function InsertStandardFieldEliminations($TourId, $SubRule){
    if($SubRule==1 OR $SubRule==2) {
        foreach (array('R','C', 'B', 'L', 'T') as $kDiv) {
            foreach (array('M','W') as $kCl) {
                for($n=1; $n<=16; $n++) {
                    safe_w_SQL("INSERT INTO Eliminations set ElId=0, ElElimPhase=0, ElEventCode='{$kDiv}{$kCl}', ElTournament={$TourId}, ElQualRank={$n}");
                }
                for($n=1; $n<=8; $n++) {
                    safe_w_SQL("INSERT INTO Eliminations set ElId=0, ElElimPhase=1, ElEventCode='{$kDiv}{$kCl}', ElTournament={$TourId}, ElQualRank={$n}");
                }
            }
        }
    }
}

