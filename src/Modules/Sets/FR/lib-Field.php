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
			CreateClass($TourId, 1, 21,100, 0, 'M', 'M', 'Men');
			CreateClass($TourId, 2, 21,100, 1, 'W', 'W', 'Women');
			CreateClass($TourId, 3, 1, 20, 0, 'JM', 'JM,M', 'Junior Men');
			CreateClass($TourId, 4, 1, 20, 1, 'JW', 'JW,W', 'Junior Women');
			break;
	}
}

function CreateStandardFieldEvents($TourId, $SubRule) {
	$Elim1=array(
		'Archers' => 16,
		'Ends' => 12,
		'Arrows' => 3,
		'SO' => 1
	);
	$Elim2=array(
		'Archers' => 8,
		'Ends' => 8,
		'Arrows' => 3,
		'SO' => 1
	);
	switch($SubRule) {
		case '1':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'RM',  'Recurve Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'RW',  'Recurve Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'RJM', 'Recurve Junior Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'RJW', 'Recurve Junior Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'RCM', 'Recurve Cadet Men',     0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'RCW', 'Recurve Cadet Women',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'RMM', 'Recurve Master Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'RMW', 'Recurve Master Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'CM',  'Compound Men',          0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'CW',  'Compound Women',        0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'CJM', 'Compound Junior Men',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'CJW', 'Compound Junior Women', 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'CCM', 'Compound Cadet Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'CCW', 'Compound Cadet Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'CMM', 'Compound Master Men',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'CMW', 'Compound Master Women', 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'BM',  'Barebow Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'BW',  'Barebow Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'BJM', 'Barebow Junior Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'BJW', 'Barebow Junior Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'BCM', 'Barebow Cadet Men',     0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'BCW', 'Barebow Cadet Women',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'BMM', 'Barebow Master Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'BMW', 'Barebow Master Women',  0, 0, 0, $Elim1, $Elim2);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MT',  'Men Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WT',  'Women Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MJT',  'Men Junior Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WJT',  'Women Junior Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MCT',  'Men Cadet Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WCT',  'Women Cadet Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MMT',  'Men Master Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WMT',  'Women Master Team',0,248,15);
			break;
		case '2':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'RM',  'Recurve Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'RW',  'Recurve Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'RJM', 'Recurve Junior Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'RJW', 'Recurve Junior Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'CM',  'Compound Men',          0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'CW',  'Compound Women',        0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'CJM', 'Compound Junior Men',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'CJW', 'Compound Junior Women', 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'BM',  'Barebow Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'BW',  'Barebow Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'BJM', 'Barebow Junior Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'BJW', 'Barebow Junior Women',  0, 0, 0, $Elim1, $Elim2);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MT',  'Men Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WT',  'Women Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MJT',  'Men Junior Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WJT',  'Women Junior Team',0,248,15);
			break;
	}
}

function InsertStandardFieldEvents($TourId, $SubRule) {
	switch($SubRule) {
		case '1':
			InsertClassEvent($TourId, 0, 1, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 0, 1, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 0, 1, 'RCM', 'R', 'CM');
			InsertClassEvent($TourId, 0, 1, 'RMM', 'R', 'MM');
			InsertClassEvent($TourId, 0, 1, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 0, 1, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 0, 1, 'RCW', 'R', 'CW');
			InsertClassEvent($TourId, 0, 1, 'RMW', 'R', 'MW');
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

			InsertClassEvent($TourId, 1, 1, 'MT',  'R',  'M');
			InsertClassEvent($TourId, 2, 1, 'MT',  'C',  'M');
			InsertClassEvent($TourId, 3, 1, 'MT',  'B',  'M');
			InsertClassEvent($TourId, 1, 1, 'MJT', 'R', 'JM');
			InsertClassEvent($TourId, 2, 1, 'MJT', 'C', 'JM');
			InsertClassEvent($TourId, 3, 1, 'MJT', 'B', 'JM');
			InsertClassEvent($TourId, 1, 1, 'MCT', 'R', 'CM');
			InsertClassEvent($TourId, 2, 1, 'MCT', 'C', 'CM');
			InsertClassEvent($TourId, 3, 1, 'MCT', 'B', 'CM');
			InsertClassEvent($TourId, 1, 1, 'MMT', 'R', 'MM');
			InsertClassEvent($TourId, 2, 1, 'MMT', 'C', 'MM');
			InsertClassEvent($TourId, 3, 1, 'MMT', 'B', 'MM');
			InsertClassEvent($TourId, 1, 1, 'WT',  'R',  'W');
			InsertClassEvent($TourId, 2, 1, 'WT',  'C',  'W');
			InsertClassEvent($TourId, 3, 1, 'WT',  'B',  'W');
			InsertClassEvent($TourId, 1, 1, 'WJT', 'R', 'JW');
			InsertClassEvent($TourId, 2, 1, 'WJT', 'C', 'JW');
			InsertClassEvent($TourId, 3, 1, 'WJT', 'B', 'JW');
			InsertClassEvent($TourId, 1, 1, 'WCT', 'R', 'CW');
			InsertClassEvent($TourId, 2, 1, 'WCT', 'C', 'CW');
			InsertClassEvent($TourId, 3, 1, 'WCT', 'B', 'CW');
			InsertClassEvent($TourId, 1, 1, 'WMT', 'R', 'MW');
			InsertClassEvent($TourId, 2, 1, 'WMT', 'C', 'MW');
			InsertClassEvent($TourId, 3, 1, 'WMT', 'B', 'MW');
			break;
		case '2':
			InsertClassEvent($TourId, 0, 1, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 0, 1, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 0, 1, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 0, 1, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 0, 1, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 0, 1, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 0, 1, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 0, 1, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 0, 1, 'BM',  'B',  'M');
			InsertClassEvent($TourId, 0, 1, 'BJM', 'B', 'JM');
			InsertClassEvent($TourId, 0, 1, 'BW',  'B',  'W');
			InsertClassEvent($TourId, 0, 1, 'BJW', 'B', 'JW');

			InsertClassEvent($TourId, 1, 1, 'MT',  'R',  'M');
			InsertClassEvent($TourId, 2, 1, 'MT',  'C',  'M');
			InsertClassEvent($TourId, 3, 1, 'MT',  'B',  'M');
			InsertClassEvent($TourId, 1, 1, 'MJT', 'R', 'JM');
			InsertClassEvent($TourId, 2, 1, 'MJT', 'C', 'JM');
			InsertClassEvent($TourId, 3, 1, 'MJT', 'B', 'JM');
			InsertClassEvent($TourId, 1, 1, 'WT',  'R',  'W');
			InsertClassEvent($TourId, 2, 1, 'WT',  'C',  'W');
			InsertClassEvent($TourId, 3, 1, 'WT',  'B',  'W');
			InsertClassEvent($TourId, 1, 1, 'WJT', 'R', 'JW');
			InsertClassEvent($TourId, 2, 1, 'WJT', 'C', 'JW');
			InsertClassEvent($TourId, 3, 1, 'WJT', 'B', 'JW');
			break;
	}
}

function InsertStandardFieldEliminations($TourId, $SubRule){
	$cls=array();
	switch($SubRule) {
		case '1':
			$cls=array('M', 'W', 'JM', 'JW', 'CM', 'CW', 'MM', 'MW');
			break;
		case '2':
			$cls=array('M', 'W', 'JM', 'JW');
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

