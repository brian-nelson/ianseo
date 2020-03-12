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
			CreateClass($TourId, 3, 18, 20, 0, 'JM', 'JM,M', 'Junior Men');
			CreateClass($TourId, 4, 18, 20, 1, 'JW', 'JW,W', 'Junior Women');
			CreateClass($TourId, 5,  1, 17, 0, 'CM', 'CM,JM,M', 'Cadet Men');
			CreateClass($TourId, 6,  1, 17, 1, 'CW', 'CW,JW,W', 'Cadet Women');
			CreateClass($TourId, 7, 50,100, 0, 'MM', 'MM,M', 'Master Men');
			CreateClass($TourId, 8, 50,100, 1, 'MW', 'MW,W', 'Master Women');
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
			);

			$i=1;
			CreateEventNew($TourId, 'CM',  'Compound Men',             $i++, $SettingsInd);
			CreateEventNew($TourId, 'CW',  'Compound Women',           $i++, $SettingsInd);
			if($SubRule==1) {
				CreateEventNew($TourId, 'CJM', 'Compound Junior Men',      $i++, $SettingsInd);
				CreateEventNew($TourId, 'CJW', 'Compound Junior Women',    $i++, $SettingsInd);
				CreateEventNew($TourId, 'CCM', 'Compound Cadet Men',       $i++, $SettingsInd);
				CreateEventNew($TourId, 'CCW', 'Compound Cadet Women',     $i++, $SettingsInd);
				CreateEventNew($TourId, 'CMM', 'Compound Master Men',      $i++, $SettingsInd);
				CreateEventNew($TourId, 'CMW', 'Compound Master Women',    $i++, $SettingsInd);
			}
			CreateEventNew($TourId, 'BM',  'Barebow Men',              $i++, $SettingsInd);
			CreateEventNew($TourId, 'BW',  'Barebow Women',            $i++, $SettingsInd);
			if($SubRule==1) {
				CreateEventNew($TourId, 'BJM', 'Barebow Junior Men',       $i++, $SettingsInd);
				CreateEventNew($TourId, 'BJW', 'Barebow Junior Women',     $i++, $SettingsInd);
				CreateEventNew($TourId, 'BCM', 'Barebow Cadet Men',        $i++, $SettingsInd);
				CreateEventNew($TourId, 'BCW', 'Barebow Cadet Women',      $i++, $SettingsInd);
				CreateEventNew($TourId, 'BMM', 'Barebow Master Men',       $i++, $SettingsInd);
				CreateEventNew($TourId, 'BMW', 'Barebow Master Women',     $i++, $SettingsInd);
			}
			CreateEventNew($TourId, 'LM',  'Longbow Men',              $i++, $SettingsInd);
			CreateEventNew($TourId, 'LW',  'Longbow Women',            $i++, $SettingsInd);
			if($SubRule==1) {
				CreateEventNew($TourId, 'LJM', 'Longbow Junior Men',       $i++, $SettingsInd);
				CreateEventNew($TourId, 'LJW', 'Longbow Junior Women',     $i++, $SettingsInd);
				CreateEventNew($TourId, 'LCM', 'Longbow Cadet Men',        $i++, $SettingsInd);
				CreateEventNew($TourId, 'LCW', 'Longbow Cadet Women',      $i++, $SettingsInd);
				CreateEventNew($TourId, 'LMM', 'Longbow Master Men',       $i++, $SettingsInd);
				CreateEventNew($TourId, 'LMW', 'Longbow Master Women',     $i++, $SettingsInd);
			}
			CreateEventNew($TourId, 'IM',  'Instinctive Men',          $i++, $SettingsInd);
			CreateEventNew($TourId, 'IW',  'Instinctive Women',        $i++, $SettingsInd);
			if($SubRule==1) {
				CreateEventNew($TourId, 'IJM', 'Instinctive Junior Men',   $i++, $SettingsInd);
				CreateEventNew($TourId, 'IJW', 'Instinctive Junior Women', $i++, $SettingsInd);
				CreateEventNew($TourId, 'ICM', 'Instinctive Cadet Men',    $i++, $SettingsInd);
				CreateEventNew($TourId, 'ICW', 'Instinctive Cadet Women',  $i++, $SettingsInd);
				CreateEventNew($TourId, 'IMM', 'Instinctive Master Men',   $i++, $SettingsInd);
				CreateEventNew($TourId, 'IMW', 'Instinctive Master Women', $i++, $SettingsInd);
			}
			$i=1;
			CreateEventNew($TourId,'MT', 'Men Team',          $i++, $SettingsTeam);
			CreateEventNew($TourId,'WT', 'Women Team',        $i++, $SettingsTeam);
			if($SubRule==1) {
				CreateEventNew($TourId,'MJT','Men Junior Team',   $i++, $SettingsTeam);
				CreateEventNew($TourId,'WJT','Women Junior Team', $i++, $SettingsTeam);
				CreateEventNew($TourId,'MCT','Men Cadet Team',    $i++, $SettingsTeam);
				CreateEventNew($TourId,'WCT','Women Cadet Team',  $i++, $SettingsTeam);
				CreateEventNew($TourId,'MMT','Men Master Team',   $i++, $SettingsTeam);
				CreateEventNew($TourId,'WMT','Women Master Team', $i++, $SettingsTeam);
			}
			break;
		case '3':
		case '4':
			$SettingsInd=array(
				'EvFinalFirstPhase' => '2',
				'EvFinalTargetType'=>6,
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
				'EvFinalTargetType'=>6,
				'EvElimEnds'=>8,
				'EvElimArrows'=>3,
				'EvElimSO'=>3,
				'EvFinEnds'=>4,
				'EvFinArrows'=>3,
				'EvFinSO'=>3,
				'EvFinalAthTarget'=>MATCH_NO_SEP,
				'EvMatchArrowsNo'=>FINAL_FROM_4,
			);

			$i=1;
			CreateEventNew($TourId, 'CM',  'Compound Men',             $i++, $SettingsInd);
			CreateEventNew($TourId, 'CW',  'Compound Women',           $i++, $SettingsInd);
			if($SubRule==3) {
				CreateEventNew($TourId, 'CJM', 'Compound Junior Men',      $i++, $SettingsInd);
				CreateEventNew($TourId, 'CJW', 'Compound Junior Women',    $i++, $SettingsInd);
				CreateEventNew($TourId, 'CCM', 'Compound Cadet Men',       $i++, $SettingsInd);
				CreateEventNew($TourId, 'CCW', 'Compound Cadet Women',     $i++, $SettingsInd);
				CreateEventNew($TourId, 'CMM', 'Compound Master Men',      $i++, $SettingsInd);
				CreateEventNew($TourId, 'CMW', 'Compound Master Women',    $i++, $SettingsInd);
			}
			CreateEventNew($TourId, 'BM',  'Barebow Men',              $i++, $SettingsInd);
			CreateEventNew($TourId, 'BW',  'Barebow Women',            $i++, $SettingsInd);
			if($SubRule==3) {
				CreateEventNew($TourId, 'BJM', 'Barebow Junior Men',       $i++, $SettingsInd);
				CreateEventNew($TourId, 'BJW', 'Barebow Junior Women',     $i++, $SettingsInd);
				CreateEventNew($TourId, 'BCM', 'Barebow Cadet Men',        $i++, $SettingsInd);
				CreateEventNew($TourId, 'BCW', 'Barebow Cadet Women',      $i++, $SettingsInd);
				CreateEventNew($TourId, 'BMM', 'Barebow Master Men',       $i++, $SettingsInd);
				CreateEventNew($TourId, 'BMW', 'Barebow Master Women',     $i++, $SettingsInd);
			}
			CreateEventNew($TourId, 'LM',  'Longbow Men',              $i++, $SettingsInd);
			CreateEventNew($TourId, 'LW',  'Longbow Women',            $i++, $SettingsInd);
			if($SubRule==3) {
				CreateEventNew($TourId, 'LJM', 'Longbow Junior Men',       $i++, $SettingsInd);
				CreateEventNew($TourId, 'LJW', 'Longbow Junior Women',     $i++, $SettingsInd);
				CreateEventNew($TourId, 'LCM', 'Longbow Cadet Men',        $i++, $SettingsInd);
				CreateEventNew($TourId, 'LCW', 'Longbow Cadet Women',      $i++, $SettingsInd);
				CreateEventNew($TourId, 'LMM', 'Longbow Master Men',       $i++, $SettingsInd);
				CreateEventNew($TourId, 'LMW', 'Longbow Master Women',     $i++, $SettingsInd);
			}
			CreateEventNew($TourId, 'IM',  'Instinctive Men',          $i++, $SettingsInd);
			CreateEventNew($TourId, 'IW',  'Instinctive Women',        $i++, $SettingsInd);
			if($SubRule==3) {
				CreateEventNew($TourId, 'IJM', 'Instinctive Junior Men',   $i++, $SettingsInd);
				CreateEventNew($TourId, 'IJW', 'Instinctive Junior Women', $i++, $SettingsInd);
				CreateEventNew($TourId, 'ICM', 'Instinctive Cadet Men',    $i++, $SettingsInd);
				CreateEventNew($TourId, 'ICW', 'Instinctive Cadet Women',  $i++, $SettingsInd);
				CreateEventNew($TourId, 'IMM', 'Instinctive Master Men',   $i++, $SettingsInd);
				CreateEventNew($TourId, 'IMW', 'Instinctive Master Women', $i++, $SettingsInd);
			}
			$i=1;
			CreateEventNew($TourId,'MT', 'Men Team',          $i++, $SettingsTeam);
			CreateEventNew($TourId,'WT', 'Women Team',        $i++, $SettingsTeam);
			if($SubRule==3) {
				CreateEventNew($TourId,'MJT','Men Junior Team',   $i++, $SettingsTeam);
				CreateEventNew($TourId,'WJT','Women Junior Team', $i++, $SettingsTeam);
				CreateEventNew($TourId,'MCT','Men Cadet Team',    $i++, $SettingsTeam);
				CreateEventNew($TourId,'WCT','Women Cadet Team',  $i++, $SettingsTeam);
				CreateEventNew($TourId,'MMT','Men Master Team',   $i++, $SettingsTeam);
				CreateEventNew($TourId,'WMT','Women Master Team', $i++, $SettingsTeam);
			}
			break;
	}
}

function InsertStandard3DEvents($TourId, $SubRule) {
	switch($SubRule) {
		case '1':
		case '3':
			InsertClassEvent($TourId, 0, 1, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 0, 1, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 0, 1, 'CCM', 'C', 'CM');
			InsertClassEvent($TourId, 0, 1, 'CMM', 'C', 'MM');
			InsertClassEvent($TourId, 0, 1, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 0, 1, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 0, 1, 'CCW', 'C', 'CW');
			InsertClassEvent($TourId, 0, 1, 'CMW', 'C', 'MW');
			InsertClassEvent($TourId, 0, 1, 'BM',  'B',  'M');
			InsertClassEvent($TourId, 0, 1, 'BJM', 'B', 'JM');
			InsertClassEvent($TourId, 0, 1, 'BCM', 'B', 'CM');
			InsertClassEvent($TourId, 0, 1, 'BMM', 'B', 'MM');
			InsertClassEvent($TourId, 0, 1, 'BW',  'B',  'W');
			InsertClassEvent($TourId, 0, 1, 'BJW', 'B', 'JW');
			InsertClassEvent($TourId, 0, 1, 'BCW', 'B', 'CW');
			InsertClassEvent($TourId, 0, 1, 'BMW', 'B', 'MW');
			InsertClassEvent($TourId, 0, 1, 'LM',  'L',  'M');
			InsertClassEvent($TourId, 0, 1, 'LJM', 'L', 'JM');
			InsertClassEvent($TourId, 0, 1, 'LCM', 'L', 'CM');
			InsertClassEvent($TourId, 0, 1, 'LMM', 'L', 'MM');
			InsertClassEvent($TourId, 0, 1, 'LW',  'L',  'W');
			InsertClassEvent($TourId, 0, 1, 'LJW', 'L', 'JW');
			InsertClassEvent($TourId, 0, 1, 'LCW', 'L', 'CW');
			InsertClassEvent($TourId, 0, 1, 'LMW', 'L', 'MW');
			InsertClassEvent($TourId, 0, 1, 'IM',  'I',  'M');
			InsertClassEvent($TourId, 0, 1, 'IJM', 'I', 'JM');
			InsertClassEvent($TourId, 0, 1, 'ICM', 'I', 'CM');
			InsertClassEvent($TourId, 0, 1, 'IMM', 'I', 'MM');
			InsertClassEvent($TourId, 0, 1, 'IW',  'I',  'W');
			InsertClassEvent($TourId, 0, 1, 'IJW', 'I', 'JW');
			InsertClassEvent($TourId, 0, 1, 'ICW', 'I', 'CW');
			InsertClassEvent($TourId, 0, 1, 'IMW', 'I', 'MW');

			InsertClassEvent($TourId, 1, 1, 'MT',  'C',  'M');
			InsertClassEvent($TourId, 2, 1, 'MT',  'L',  'M');
			InsertClassEvent($TourId, 3, 1, 'MT',  'B',  'M');
			InsertClassEvent($TourId, 3, 1, 'MT',  'I',  'M');
			InsertClassEvent($TourId, 1, 1, 'MJT', 'C', 'JM');
			InsertClassEvent($TourId, 2, 1, 'MJT', 'L', 'JM');
			InsertClassEvent($TourId, 3, 1, 'MJT', 'B', 'JM');
			InsertClassEvent($TourId, 3, 1, 'MJT', 'I', 'JM');
			InsertClassEvent($TourId, 1, 1, 'MCT', 'C', 'CM');
			InsertClassEvent($TourId, 2, 1, 'MCT', 'L', 'CM');
			InsertClassEvent($TourId, 3, 1, 'MCT', 'B', 'CM');
			InsertClassEvent($TourId, 3, 1, 'MCT', 'I', 'CM');
			InsertClassEvent($TourId, 1, 1, 'MMT', 'C', 'MM');
			InsertClassEvent($TourId, 2, 1, 'MMT', 'L', 'MM');
			InsertClassEvent($TourId, 3, 1, 'MMT', 'B', 'MM');
			InsertClassEvent($TourId, 3, 1, 'MMT', 'I', 'MM');
			InsertClassEvent($TourId, 1, 1, 'WT',  'C',  'W');
			InsertClassEvent($TourId, 2, 1, 'WT',  'L',  'W');
			InsertClassEvent($TourId, 3, 1, 'WT',  'B',  'W');
			InsertClassEvent($TourId, 3, 1, 'WT',  'I',  'W');
			InsertClassEvent($TourId, 1, 1, 'WJT', 'C', 'JW');
			InsertClassEvent($TourId, 2, 1, 'WJT', 'L', 'JW');
			InsertClassEvent($TourId, 3, 1, 'WJT', 'B', 'JW');
			InsertClassEvent($TourId, 3, 1, 'WJT', 'I', 'JW');
			InsertClassEvent($TourId, 1, 1, 'WCT', 'C', 'CW');
			InsertClassEvent($TourId, 2, 1, 'WCT', 'L', 'CW');
			InsertClassEvent($TourId, 3, 1, 'WCT', 'B', 'CW');
			InsertClassEvent($TourId, 3, 1, 'WCT', 'I', 'CW');
			InsertClassEvent($TourId, 1, 1, 'WMT', 'C', 'MW');
			InsertClassEvent($TourId, 2, 1, 'WMT', 'L', 'MW');
			InsertClassEvent($TourId, 3, 1, 'WMT', 'B', 'MW');
			InsertClassEvent($TourId, 3, 1, 'WMT', 'I', 'MW');
			break;
		case '2':
		case '4':
			InsertClassEvent($TourId, 0, 1, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 0, 1, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 0, 1, 'BM',  'B',  'M');
			InsertClassEvent($TourId, 0, 1, 'BW',  'B',  'W');
			InsertClassEvent($TourId, 0, 1, 'LM',  'L',  'M');
			InsertClassEvent($TourId, 0, 1, 'LW',  'L',  'W');
			InsertClassEvent($TourId, 0, 1, 'IM',  'I',  'M');
			InsertClassEvent($TourId, 0, 1, 'IW',  'I',  'W');

			InsertClassEvent($TourId, 1, 1, 'MT',  'C',  'M');
			InsertClassEvent($TourId, 2, 1, 'MT',  'L',  'M');
			InsertClassEvent($TourId, 3, 1, 'MT',  'B',  'M');
			InsertClassEvent($TourId, 3, 1, 'MT',  'I',  'M');
			InsertClassEvent($TourId, 1, 1, 'WT',  'C',  'W');
			InsertClassEvent($TourId, 2, 1, 'WT',  'L',  'W');
			InsertClassEvent($TourId, 3, 1, 'WT',  'B',  'W');
			InsertClassEvent($TourId, 3, 1, 'WT',  'I',  'W');
			break;
	}
}

function InsertStandard3DEliminations($TourId, $SubRule){
	$cls=array();
	switch($SubRule) {
		case '1':
			$cls=array('M', 'W', 'JM', 'JW', 'CM', 'CW', 'MM', 'MW');
			break;
		case '2':
			$cls=array('M', 'W');
			break;
	}
	foreach(array('C', 'B', 'L', 'I') as $div) {
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
