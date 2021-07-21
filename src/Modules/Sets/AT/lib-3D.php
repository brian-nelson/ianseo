<?php

/*

3D DEFINITIONS (Target Tournaments)

*/

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
			$SettingsInd=array(
				'EvFinalFirstPhase' => '2',
				'EvFinalTargetType'=>8,
				'EvElimEnds'=>12,
				'EvElimArrows'=>1,
				'EvElimSO'=>1,
				'EvFinEnds'=>4,
				'EvFinArrows'=>1,
				'EvFinSO'=>1,
				'EvElimType'=>2,
				'EvElim1'=>16,
				'EvE1Ends'=>12,
				'EvE1Arrows'=>1,
				'EvE1SO'=>1,
				'EvElim2'=>8,
				'EvE2Ends'=>8,
				'EvE2Arrows'=>1,
				'EvE2SO'=>1,
				'EvFinalAthTarget'=>MATCH_NO_SEP,
				'EvMatchArrowsNo'=>FINAL_FROM_2,
			);
			$SettingsTeam=array(
				'EvTeamEvent' => '1',
				'EvFinalFirstPhase' => '2',
				'EvFinalTargetType'=>8,
				'EvElimEnds'=>8,
				'EvElimArrows'=>3,
				'EvElimSO'=>3,
				'EvFinEnds'=>4,
				'EvFinArrows'=>3,
				'EvFinSO'=>3,
				'EvFinalAthTarget'=>MATCH_NO_SEP,
				'EvMatchArrowsNo'=>FINAL_FROM_2,
                'EvMultiTeam'=>1,
                'EvMultiTeamNo'=>2,
                'EvPartialTeam'=>1
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
                'EvMultiTeam'=>1,
                'EvMultiTeamNo'=>3,
                'EvPartialTeam'=>0
            );

            $i = 1;
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
            CreateEventNew($TourId, 'CX', 'Compound Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'BX', 'Blankbogen Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'LX', 'Langbogen Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'IX', 'Instinktivbogen Mixed Team', $i, $SettingsMixedTeam);
			break;
		case '3':
		case '4':
			$SettingsInd=array(
				'EvFinalFirstPhase' => '2',
				'EvFinalTargetType'=>8,
				'EvElimEnds'=>6,
				'EvElimArrows'=>1,
				'EvElimSO'=>1,
				'EvFinEnds'=>4,
				'EvFinArrows'=>1,
				'EvFinSO'=>1,
				'EvElimType'=>4,
				'EvElim2'=>22,
				'EvFinalAthTarget'=>MATCH_NO_SEP,
				'EvMatchArrowsNo'=>FINAL_FROM_2,
			);
			$SettingsTeam=array(
				'EvTeamEvent' => '1',
				'EvFinalFirstPhase' => '4',
				'EvFinalTargetType'=>8,
				'EvElimEnds'=>8,
				'EvElimArrows'=>3,
				'EvElimSO'=>3,
				'EvFinEnds'=>4,
				'EvFinArrows'=>3,
				'EvFinSO'=>3,
				'EvFinalAthTarget'=>MATCH_NO_SEP,
				'EvMatchArrowsNo'=>FINAL_FROM_4,
                'EvMultiTeam'=>1,
                'EvMultiTeamNo'=>2,
                'EvPartialTeam'=>1
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
                'EvMultiTeam'=>1,
                'EvMultiTeamNo'=>3,
                'EvPartialTeam'=>0
            );
            $i = 1;
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
            CreateEventNew($TourId, 'CX', 'Compound Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'BX', 'Blankbogen Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'LX', 'Langbogen Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'IX', 'Instinktivbogen Mixed Team', $i, $SettingsMixedTeam);
			break;
	}
}

function InsertStandard3DEvents($TourId, $SubRule) {
    foreach (array('C'=>'C','B'=>'B','L'=>'L','I'=>'I') as $kDiv=>$vDiv) {
        $clsTmpArr = array('W','JW','MW','VW','CW');
        if($SubRule==2 OR $SubRule==4) {
            $clsTmpArr = array('W');
        }
        foreach($clsTmpArr as $kClass=>$vClass) {
            InsertClassEvent($TourId, 0, 1, $vDiv.'W', $kDiv,  $vClass);
            InsertClassEvent($TourId, ($kDiv == 'C' ? 1 : ($kDiv == 'L' ? 2 : 3)), 1, 'WT', $kDiv, $vClass);
            InsertClassEvent($TourId, 1, 1, $vDiv.'X', $kDiv, $vClass);
        }
        $clsTmpArr = array('M','JM','MU','MM','VU','VM','CM');
        if($SubRule==2 OR $SubRule==4) {
            $clsTmpArr = array('M');
        }
        foreach($clsTmpArr as $kClass=>$vClass) {
            InsertClassEvent($TourId, 0, 1, $vDiv.'M', $kDiv,  $vClass);
            InsertClassEvent($TourId, ($kDiv == 'C' ? 1 : ($kDiv == 'L' ? 2 : 3)), 1, 'MT', $kDiv, $vClass);
            if(substr($vClass,-1,1) != 'U') {
                InsertClassEvent($TourId, 2, 1, $vDiv . 'X', $kDiv, $vClass);
            }
        }
    }
}

function InsertStandard3DEliminations($TourId, $SubRule){
    if($SubRule==1 OR $SubRule==2) {
        foreach (array('R', 'C', 'B', 'L', 'I') as $kDiv) {
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
