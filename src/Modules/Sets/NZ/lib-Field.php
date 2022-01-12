<?php

/*

FIELD DEFINITIONS (Target Tournaments)

*/

// creation of standard NZ field tournament competition classes
function CreateStandardFieldClasses($TourId, $SubRule) {
	$i=1;
	switch($SubRule) {
		case '1': // All NZ Classes
			CreateClass($TourId, $i++, 21, 49, 0, 'SM', 'SM', 'Senior Men');
			CreateClass($TourId, $i++, 21, 49, 1, 'SW', 'SW', 'Senior Women');
			CreateClass($TourId, $i++, 50, 64, 0, 'MM', 'MM,SM', 'Master Men (50-64)');
			CreateClass($TourId, $i++, 50, 64, 1, 'MW', 'MW,SW', 'Master Women (50-64)');
			CreateClass($TourId, $i++, 65, 100, 0, 'VM', 'VM,MM,SM', 'Master Men (65+)');
			CreateClass($TourId, $i++, 65, 100, 1, 'VW', 'VW,MW,SW', 'Master Women (65+)');
			CreateClass($TourId, $i++, 18, 20, 0, 'JM', 'JM,SM', 'Junior Men');
			CreateClass($TourId, $i++, 18, 20, 1, 'JW', 'JW,SW', 'Junior Women');
			CreateClass($TourId, $i++, 16, 17, 0, 'CM', 'CM,JM,SM', 'Cadet Men');
			CreateClass($TourId, $i++, 16, 17, 1, 'CW', 'CW,JW,SW', 'Cadet Women');
			CreateClass($TourId, $i++, 14, 15, 0, 'IB', 'IB,CM,JM,SM', 'Intermediate Boys');
			CreateClass($TourId, $i++, 14, 15, 1, 'IG', 'IG,CW,JW,SW', 'Intermediate Girls');
			CreateClass($TourId, $i++, 11, 13, 0, 'YB', 'YB,IB,CM,JM', 'Cub Boys');
			CreateClass($TourId, $i++, 11, 13, 1, 'YG', 'YG,IG,CW,JW', 'Cub Girls');
			CreateClass($TourId, $i++, 1, 10, 0, 'KB', 'KB,YB,IB,CM,JM', 'Kiwi Boys');
			CreateClass($TourId, $i++, 1, 10, 1, 'KG', 'KG,YG,IG,CW,JW', 'Kiwi Girls');
			break;
		case '2': // Senior NZ & WA Classes Only
			CreateClass($TourId, $i++, 21, 49, 0, 'SM', 'SM', 'Senior Men');
			CreateClass($TourId, $i++, 21, 49, 1, 'SW', 'SW', 'Senior Women');
			CreateClass($TourId, $i++, 50, 64, 0, 'MM', 'MM,SM', 'Master Men (50-64)');
			CreateClass($TourId, $i++, 50, 64, 1, 'MW', 'MW,SW', 'Master Women (50-64)');
			CreateClass($TourId, $i++, 65, 100, 0, 'VM', 'VM,MM,SM', 'Master Men (65+)');
			CreateClass($TourId, $i++, 65, 100, 1, 'VW', 'VW,MW,SW', 'Master Women (65+)');
			CreateClass($TourId, $i++, 18, 20, 0, 'JM', 'JM,SM', 'Junior Men');
			CreateClass($TourId, $i++, 18, 20, 1, 'JW', 'JW,SW', 'Junior Women');
			CreateClass($TourId, $i++, 1, 17, 0, 'CM', 'CM,JM,SM', 'Cadet Men');
			CreateClass($TourId, $i++, 1, 17, 1, 'CW', 'CW,JW,SW', 'Cadet Women');
			break;
		case '3': // Junior Classes Only
			CreateClass($TourId, $i++, 18, 20, 0, 'JM', 'JM', 'Junior Men');
			CreateClass($TourId, $i++, 18, 20, 1, 'JW', 'JW', 'Junior Women');
			CreateClass($TourId, $i++, 16, 17, 0, 'CM', 'CM,JM', 'Cadet Men');
			CreateClass($TourId, $i++, 16, 17, 1, 'CW', 'CW,JW', 'Cadet Women');
			CreateClass($TourId, $i++, 14, 15, 0, 'IB', 'IB,CM,JM', 'Intermediate Boys');
			CreateClass($TourId, $i++, 14, 15, 1, 'IG', 'IG,CW,JW', 'Intermediate Girls');
			CreateClass($TourId, $i++, 11, 13, 0, 'YB', 'YB,IB,CM,JM', 'Cub Boys');
			CreateClass($TourId, $i++, 11, 13, 1, 'YG', 'YG,IG,CW,JW', 'Cub Girls');
			CreateClass($TourId, $i++, 1, 10, 0, 'KB', 'KB,YB,IB,CM,JM', 'Kiwi Boys');
			CreateClass($TourId, $i++, 1, 10, 1, 'KG', 'KG,YG,IG,CW,JW', 'Kiwi Girls');
			break;
	}
}

// creation of standard NZ field matchplay competition events
function CreateStandardFieldEvents($TourId, $SubRule) {
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
	switch($SubRule) {
		case '1':
		case '2':
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
			CreateEventNew($TourId,'RM',  'Recurve Men',          $i++, $SettingsInd);
			CreateEventNew($TourId,'RW',  'Recurve Women',        $i++, $SettingsInd);
			CreateEventNew($TourId,'RJM', 'Recurve Junior Men',   $i++, $SettingsInd);
			CreateEventNew($TourId,'RJW', 'Recurve Junior Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'RCM', 'Recurve Cadet Men',    $i++, $SettingsInd);
			CreateEventNew($TourId,'RCW', 'Recurve Cadet Women',  $i++, $SettingsInd);
			CreateEventNew($TourId,'RMM', 'Recurve Master Men',   $i++, $SettingsInd);
			CreateEventNew($TourId,'RMW', 'Recurve Master Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'CM',  'Compound Men',         $i++, $SettingsInd);
			CreateEventNew($TourId,'CW',  'Compound Women',       $i++, $SettingsInd);
			CreateEventNew($TourId,'CJM', 'Compound Junior Men',  $i++, $SettingsInd);
			CreateEventNew($TourId,'CJW', 'Compound Junior Women',$i++, $SettingsInd);
			CreateEventNew($TourId,'CCM', 'Compound Cadet Men',   $i++, $SettingsInd);
			CreateEventNew($TourId,'CCW', 'Compound Cadet Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'CMM', 'Compound Master Men',  $i++, $SettingsInd);
			CreateEventNew($TourId,'CMW', 'Compound Master Women',$i++, $SettingsInd);
			CreateEventNew($TourId,'BM',  'Barebow Men',          $i++, $SettingsInd);
			CreateEventNew($TourId,'BW',  'Barebow Women',        $i++, $SettingsInd);
			CreateEventNew($TourId,'BJM', 'Barebow Junior Men',   $i++, $SettingsInd);
			CreateEventNew($TourId,'BJW', 'Barebow Junior Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'BCM', 'Barebow Cadet Men',    $i++, $SettingsInd);
			CreateEventNew($TourId,'BCW', 'Barebow Cadet Women',  $i++, $SettingsInd);
			CreateEventNew($TourId,'BMM', 'Barebow Master Men',   $i++, $SettingsInd);
			CreateEventNew($TourId,'BMW', 'Barebow Master Women', $i++, $SettingsInd);
			$i=1;
			CreateEventNew($TourId, 'MT',  'Men Team',                    $i++, $SettingsTeam);
			CreateEventNew($TourId, 'WT',  'Women Team',                  $i++, $SettingsTeam);
			CreateEventNew($TourId, 'RX',  'Recurve Mixed Team',          $i++, $SettingsMixedTeam);
			CreateEventNew($TourId, 'CX',  'Compound Mixed Team',         $i++, $SettingsMixedTeam);
			CreateEventNew($TourId, 'BX',  'Barebow Mixed Team',          $i++, $SettingsMixedTeam);
			CreateEventNew($TourId, 'MJT', 'Men Junior Team',             $i++, $SettingsTeam);
			CreateEventNew($TourId, 'WJT', 'Women Junior Team',           $i++, $SettingsTeam);
			CreateEventNew($TourId, 'RJX', 'Recurve Junior Mixed Team',   $i++, $SettingsMixedTeam);
			CreateEventNew($TourId, 'CJX', 'Compound Junior Mixed Team',  $i++, $SettingsMixedTeam);
			CreateEventNew($TourId, 'BJX', 'Barebow Junior Mixed Team',   $i++, $SettingsMixedTeam);
			CreateEventNew($TourId, 'MCT', 'Men Cadet Team',              $i++, $SettingsTeam);
			CreateEventNew($TourId, 'WCT', 'Women Cadet Team',            $i++, $SettingsTeam);
			CreateEventNew($TourId, 'RCX', 'Recurve Cadet Mixed Team',    $i++, $SettingsMixedTeam);
			CreateEventNew($TourId, 'CCX', 'Compound Cadet Mixed Team',   $i++, $SettingsMixedTeam);
			CreateEventNew($TourId, 'BCX', 'Barebow Cadet Mixed Team',    $i++, $SettingsMixedTeam);
			CreateEventNew($TourId, 'MMT', 'Men Master Team',             $i++, $SettingsTeam);
			CreateEventNew($TourId, 'WMT', 'Women Master Team',           $i++, $SettingsTeam);
			CreateEventNew($TourId, 'RMX', 'Recurve Master Mixed Team',   $i++, $SettingsMixedTeam);
			CreateEventNew($TourId, 'CMX', 'Compound Master Mixed Team',  $i++, $SettingsMixedTeam);
			CreateEventNew($TourId, 'BMX', 'Barebow Master Mixed Team',   $i++, $SettingsMixedTeam);
			break;
		case '3':
			$i=1;
			CreateEventNew($TourId,'RJM', 'Recurve Junior Men',   $i++, $SettingsInd);
			CreateEventNew($TourId,'RJW', 'Recurve Junior Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'RCM', 'Recurve Cadet Men',    $i++, $SettingsInd);
			CreateEventNew($TourId,'RCW', 'Recurve Cadet Women',  $i++, $SettingsInd);
			CreateEventNew($TourId,'CJM', 'Compound Junior Men',  $i++, $SettingsInd);
			CreateEventNew($TourId,'CJW', 'Compound Junior Women',$i++, $SettingsInd);
			CreateEventNew($TourId,'CCM', 'Compound Cadet Men',   $i++, $SettingsInd);
			CreateEventNew($TourId,'CCW', 'Compound Cadet Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'BJM', 'Barebow Junior Men',   $i++, $SettingsInd);
			CreateEventNew($TourId,'BJW', 'Barebow Junior Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'BCM', 'Barebow Cadet Men',    $i++, $SettingsInd);
			CreateEventNew($TourId,'BCW', 'Barebow Cadet Women',  $i++, $SettingsInd);
			$i=1;
			CreateEventNew($TourId,'RYB',  'Recurve Youth Boys',          $i++, $SettingsInd);
			CreateEventNew($TourId,'RYG',  'Recurve Youth Girls',         $i++, $SettingsInd);
			CreateEventNew($TourId,'CYB', 'Compound Youth Boys',          $i++, $SettingsInd);
			CreateEventNew($TourId,'CYG', 'Compound Youth Girls',         $i++, $SettingsInd);
			break;
	}
}

function InsertStandardFieldEvents($TourId, $SubRule) {
	switch ($SubRule) {
		case '1':
		case '2':
			InsertClassEvent($TourId, 0, 1, 'RM',  'R',  'SM');
			InsertClassEvent($TourId, 0, 1, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 0, 1, 'RCM', 'R', 'CM');
			InsertClassEvent($TourId, 0, 1, 'RMM', 'R', 'MM');
			InsertClassEvent($TourId, 0, 1, 'RMM', 'R', 'VM');
			InsertClassEvent($TourId, 0, 1, 'RW',  'R',  'SW');
			InsertClassEvent($TourId, 0, 1, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 0, 1, 'RCW', 'R', 'CW');
			InsertClassEvent($TourId, 0, 1, 'RMW', 'R', 'MW');
			InsertClassEvent($TourId, 0, 1, 'RMW', 'R', 'VW');
			InsertClassEvent($TourId, 0, 1, 'CM',  'C',  'SM');
			InsertClassEvent($TourId, 0, 1, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 0, 1, 'CCM', 'C', 'CM');
			InsertClassEvent($TourId, 0, 1, 'CMM', 'C', 'MM');
			InsertClassEvent($TourId, 0, 1, 'CMM', 'C', 'VM');
			InsertClassEvent($TourId, 0, 1, 'CW',  'C',  'SW');
			InsertClassEvent($TourId, 0, 1, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 0, 1, 'CCW', 'C', 'CW');
			InsertClassEvent($TourId, 0, 1, 'CMW', 'C', 'MW');
			InsertClassEvent($TourId, 0, 1, 'CMW', 'C', 'VW');
			InsertClassEvent($TourId, 0, 1, 'BM',  'B',  'SM');
			InsertClassEvent($TourId, 0, 1, 'BJM', 'B', 'JM');
			InsertClassEvent($TourId, 0, 1, 'BCM', 'B', 'CM');
			InsertClassEvent($TourId, 0, 1, 'BMM', 'B', 'MM');
			InsertClassEvent($TourId, 0, 1, 'BMM', 'B', 'VM');
			InsertClassEvent($TourId, 0, 1, 'BW',  'B',  'SW');
			InsertClassEvent($TourId, 0, 1, 'BJW', 'B', 'JW');
			InsertClassEvent($TourId, 0, 1, 'BCW', 'B', 'CW');
			InsertClassEvent($TourId, 0, 1, 'BMW', 'B', 'MW');
			InsertClassEvent($TourId, 0, 1, 'BMW', 'B', 'VW');
			InsertClassEvent($TourId, 0, 1, 'BM',  'L',  'SM');
			InsertClassEvent($TourId, 0, 1, 'BJM', 'L', 'JM');
			InsertClassEvent($TourId, 0, 1, 'BCM', 'L', 'CM');
			InsertClassEvent($TourId, 0, 1, 'BMM', 'L', 'MM');
			InsertClassEvent($TourId, 0, 1, 'BMM', 'L', 'VM');
			InsertClassEvent($TourId, 0, 1, 'BW',  'L',  'SW');
			InsertClassEvent($TourId, 0, 1, 'BJW', 'L', 'JW');
			InsertClassEvent($TourId, 0, 1, 'BCW', 'L', 'CW');
			InsertClassEvent($TourId, 0, 1, 'BMW', 'L', 'MW');
			InsertClassEvent($TourId, 0, 1, 'BMW', 'L', 'VW');

			InsertClassEvent($TourId, 1, 1, 'MT',  'R',  'SM');
			InsertClassEvent($TourId, 2, 1, 'MT',  'C',  'SM');
			InsertClassEvent($TourId, 3, 1, 'MT',  'B',  'SM');
			InsertClassEvent($TourId, 1, 1, 'WT',  'R',  'SW');
			InsertClassEvent($TourId, 2, 1, 'WT',  'C',  'SW');
			InsertClassEvent($TourId, 3, 1, 'WT',  'B',  'SW');
			InsertClassEvent($TourId, 1, 1, 'RX',  'R',  'SW');
			InsertClassEvent($TourId, 2, 1, 'RX',  'R',  'SM');
			InsertClassEvent($TourId, 1, 1, 'CX',  'C',  'SW');
			InsertClassEvent($TourId, 2, 1, 'CX',  'C',  'SM');
			InsertClassEvent($TourId, 1, 1, 'BX',  'B',  'SW');
			InsertClassEvent($TourId, 2, 1, 'BX',  'B',  'SM');
			InsertClassEvent($TourId, 1, 1, 'MJT', 'R', 'JM');
			InsertClassEvent($TourId, 2, 1, 'MJT', 'C', 'JM');
			InsertClassEvent($TourId, 3, 1, 'MJT', 'B', 'JM');
			InsertClassEvent($TourId, 1, 1, 'WJT', 'R', 'JW');
			InsertClassEvent($TourId, 2, 1, 'WJT', 'C', 'JW');
			InsertClassEvent($TourId, 3, 1, 'WJT', 'B', 'JW');
			InsertClassEvent($TourId, 1, 1, 'RJX', 'R',  'JW');
			InsertClassEvent($TourId, 2, 1, 'RJX', 'R',  'JM');
			InsertClassEvent($TourId, 1, 1, 'CJX', 'C',  'JW');
			InsertClassEvent($TourId, 2, 1, 'CJX', 'C',  'JM');
			InsertClassEvent($TourId, 1, 1, 'BJX', 'B',  'JW');
			InsertClassEvent($TourId, 2, 1, 'BJX', 'B',  'JM');
			InsertClassEvent($TourId, 1, 1, 'MCT', 'R', 'CM');
			InsertClassEvent($TourId, 2, 1, 'MCT', 'C', 'CM');
			InsertClassEvent($TourId, 3, 1, 'MCT', 'B', 'CM');
			InsertClassEvent($TourId, 1, 1, 'WCT', 'R', 'CW');
			InsertClassEvent($TourId, 2, 1, 'WCT', 'C', 'CW');
			InsertClassEvent($TourId, 3, 1, 'WCT', 'B', 'CW');
			InsertClassEvent($TourId, 1, 1, 'RCX', 'R',  'CW');
			InsertClassEvent($TourId, 2, 1, 'RCX', 'R',  'CM');
			InsertClassEvent($TourId, 1, 1, 'CCX', 'C',  'CW');
			InsertClassEvent($TourId, 2, 1, 'CCX', 'C',  'CM');
			InsertClassEvent($TourId, 1, 1, 'BCX', 'B',  'CW');
			InsertClassEvent($TourId, 2, 1, 'BCX', 'B',  'CM');
			InsertClassEvent($TourId, 1, 1, 'MMT', 'R', 'MM');
			InsertClassEvent($TourId, 2, 1, 'MMT', 'C', 'MM');
			InsertClassEvent($TourId, 3, 1, 'MMT', 'B', 'MM');
			InsertClassEvent($TourId, 1, 1, 'RMX', 'R',  'MW');
			InsertClassEvent($TourId, 2, 1, 'RMX', 'R',  'MM');
			InsertClassEvent($TourId, 1, 1, 'CMX', 'C',  'MW');
			InsertClassEvent($TourId, 2, 1, 'CMX', 'C',  'MM');
			InsertClassEvent($TourId, 1, 1, 'BMX', 'B',  'MW');
			InsertClassEvent($TourId, 2, 1, 'BMX', 'B',  'MM');
			InsertClassEvent($TourId, 1, 1, 'WMT', 'R', 'MW');
			InsertClassEvent($TourId, 2, 1, 'WMT', 'C', 'MW');
			InsertClassEvent($TourId, 3, 1, 'WMT', 'B', 'MW');
			break;
		case '3':
			InsertClassEvent($TourId, 0, 1, 'RYB', 'R', 'JM');
			InsertClassEvent($TourId, 0, 1, 'RYB', 'R', 'CM');
			InsertClassEvent($TourId, 0, 1, 'RYB', 'R', 'IB');
			InsertClassEvent($TourId, 0, 1, 'RYB', 'R', 'YB');
			InsertClassEvent($TourId, 0, 1, 'RYB', 'R', 'KB');
			InsertClassEvent($TourId, 0, 1, 'RYG', 'R', 'JW');
			InsertClassEvent($TourId, 0, 1, 'RYG', 'R', 'CW');
			InsertClassEvent($TourId, 0, 1, 'RYG', 'R', 'IG');
			InsertClassEvent($TourId, 0, 1, 'RYG', 'R', 'YG');
			InsertClassEvent($TourId, 0, 1, 'RYG', 'R', 'KG');
			InsertClassEvent($TourId, 0, 1, 'RYB', 'B', 'JM');
			InsertClassEvent($TourId, 0, 1, 'RYB', 'B', 'CM');
			InsertClassEvent($TourId, 0, 1, 'RYB', 'B', 'IB');
			InsertClassEvent($TourId, 0, 1, 'RYB', 'B', 'YB');
			InsertClassEvent($TourId, 0, 1, 'RYB', 'B', 'KB');
			InsertClassEvent($TourId, 0, 1, 'RYG', 'B', 'JW');
			InsertClassEvent($TourId, 0, 1, 'RYG', 'B', 'CW');
			InsertClassEvent($TourId, 0, 1, 'RYG', 'B', 'IG');
			InsertClassEvent($TourId, 0, 1, 'RYG', 'B', 'YG');
			InsertClassEvent($TourId, 0, 1, 'RYG', 'B', 'KG');
			InsertClassEvent($TourId, 0, 1, 'RYB', 'L', 'JM');
			InsertClassEvent($TourId, 0, 1, 'RYB', 'L', 'CM');
			InsertClassEvent($TourId, 0, 1, 'RYB', 'L', 'IB');
			InsertClassEvent($TourId, 0, 1, 'RYB', 'L', 'YB');
			InsertClassEvent($TourId, 0, 1, 'RYB', 'L', 'KB');
			InsertClassEvent($TourId, 0, 1, 'RYG', 'L', 'JW');
			InsertClassEvent($TourId, 0, 1, 'RYG', 'L', 'CW');
			InsertClassEvent($TourId, 0, 1, 'RYG', 'L', 'IG');
			InsertClassEvent($TourId, 0, 1, 'RYG', 'L', 'YG');
			InsertClassEvent($TourId, 0, 1, 'RYG', 'L', 'KG');
			InsertClassEvent($TourId, 0, 1, 'CYB', 'C', 'JM');
			InsertClassEvent($TourId, 0, 1, 'CYB', 'C', 'CM');
			InsertClassEvent($TourId, 0, 1, 'CYB', 'C', 'IB');
			InsertClassEvent($TourId, 0, 1, 'CYB', 'C', 'YB');
			InsertClassEvent($TourId, 0, 1, 'CYB', 'C', 'KB');
			InsertClassEvent($TourId, 0, 1, 'CYG', 'C', 'JW');
			InsertClassEvent($TourId, 0, 1, 'CYG', 'C', 'CW');
			InsertClassEvent($TourId, 0, 1, 'CYG', 'C', 'IG');
			InsertClassEvent($TourId, 0, 1, 'CYG', 'C', 'YG');
			InsertClassEvent($TourId, 0, 1, 'CYG', 'C', 'KG');
			break;
	}
}

function InsertStandardFieldEliminations($TourId, $SubRule){
	$cls=array();
	switch($SubRule) {
		case '1':
		case '2':
			$cls=array('SM', 'SW', 'JM', 'JW', 'CM', 'CW', 'MM', 'MW');
			break;
		case '3':
			$cls=array('JM', 'JW', 'CM', 'CW');
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

?>