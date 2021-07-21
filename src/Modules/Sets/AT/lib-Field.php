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
				'EvFinalAthTarget'=>0,
				'EvMatchArrowsNo'=>0,
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
            CreateEventNew($TourId,'RM', 'Recurve männlich', $i++, $SettingsInd);
            CreateEventNew($TourId,'RW', 'Recurve weiblich', $i++, $SettingsInd);
            CreateEventNew($TourId,'CM', 'Compound männlich', $i++, $SettingsInd);
            CreateEventNew($TourId,'CW', 'Compound weiblich', $i++, $SettingsInd);
            CreateEventNew($TourId,'BM', 'Blankbogen männlich', $i++, $SettingsInd);
            CreateEventNew($TourId,'BW', 'Blankbogen weiblich', $i++, $SettingsInd);
            CreateEventNew($TourId,'LM', 'Langbogen männlich', $i++, $SettingsInd);
            CreateEventNew($TourId,'LW', 'Langbogen weiblich', $i++, $SettingsInd);
            CreateEventNew($TourId,'IM', 'Instinktivbogen männlich', $i++, $SettingsInd);
            CreateEventNew($TourId,'IW', 'Instinktivbogen weiblich', $i, $SettingsInd);

            $i = 1;
            CreateEventNew($TourId, 'MT', 'Herren Team', $i++, $SettingsTeam);
            CreateEventNew($TourId, 'WT', 'Damen Team', $i++, $SettingsTeam);
            CreateEventNew($TourId, 'RX', 'Recurve Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'CX', 'Compound Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'BX', 'Blankbogen Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'LX', 'Langbogen Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'IX', 'Instinktivbogen Mixed Team', $i, $SettingsMixedTeam);

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
				'EvFinalAthTarget'=>0,
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
            CreateEventNew($TourId,'RM', 'Recurve männlich', $i++, $SettingsInd);
            CreateEventNew($TourId,'RW', 'Recurve weiblich', $i++, $SettingsInd);
            CreateEventNew($TourId,'CM', 'Compound männlich', $i++, $SettingsInd);
            CreateEventNew($TourId,'CW', 'Compound weiblich', $i++, $SettingsInd);
            CreateEventNew($TourId,'BM', 'Blankbogen männlich', $i++, $SettingsInd);
            CreateEventNew($TourId,'BW', 'Blankbogen weiblich', $i++, $SettingsInd);
            CreateEventNew($TourId,'LM', 'Langbogen männlich', $i++, $SettingsInd);
            CreateEventNew($TourId,'LW', 'Langbogen weiblich', $i++, $SettingsInd);
            CreateEventNew($TourId,'IM', 'Instinktivbogen männlich', $i++, $SettingsInd);
            CreateEventNew($TourId,'IW', 'Instinktivbogen weiblich', $i, $SettingsInd);

            $i = 1;
            CreateEventNew($TourId, 'MT', 'Herren Team', $i++, $SettingsTeam);
            CreateEventNew($TourId, 'WT', 'Damen Team', $i++, $SettingsTeam);
            CreateEventNew($TourId, 'RX', 'Recurve Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'CX', 'Compound Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'BX', 'Blankbogen Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'LX', 'Langbogen Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'IX', 'Instinktivbogen Mixed Team', $i, $SettingsMixedTeam);

            break;
	}
}

function InsertStandardFieldEvents($TourId, $SubRule) {
    foreach (array('R'=>'R','C'=>'C','B'=>'B','L'=>'L','I'=>'I') as $kDiv=>$vDiv) {
        $clsTmpArr = array('W','JW','MW','VW','CW');
        if($SubRule==2 OR $SubRule==4) {
            $clsTmpArr = array('W');
        }
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
        $clsTmpArr = array('M','JM','MU','MM','VU','VM','CM');
        if($SubRule==2 OR $SubRule==4) {
            $clsTmpArr = array('M');
        }
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
        foreach (array('R','C', 'B', 'L', 'I') as $kDiv) {
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

