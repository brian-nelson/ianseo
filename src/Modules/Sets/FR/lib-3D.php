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
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CM',  'Compound Men',                0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CW',  'Compound Women',              0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CJM', 'Compound Junior Men',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CJW', 'Compound Junior Women',       0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CCM', 'Compound Cadet Men',          0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CCW', 'Compound Cadet Women',        0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CMM', 'Compound Master Men',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CMW', 'Compound Master Women',       0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BM',  'Barebow Men',                 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BW',  'Barebow Women',               0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BJM', 'Barebow Junior Men',          0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BJW', 'Barebow Junior Women',        0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BCM', 'Barebow Cadet Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BCW', 'Barebow Cadet Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BMM', 'Barebow Master Men',          0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BMW', 'Barebow Master Women',        0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LM',  'Longbow Men',                 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LW',  'Longbow Women',               0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LJM', 'Longbow Junior Men',          0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LJW', 'Longbow Junior Women',        0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LCM', 'Longbow Cadet Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LCW', 'Longbow Cadet Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LMM', 'Longbow Master Men',          0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LMW', 'Longbow Master Women',        0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IM',  'Instinctive Men',             0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IW',  'Instinctive Women',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IJM', 'Instinctive Junior Men',      0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IJW', 'Instinctive Junior Women',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'ICM', 'Instinctive Cadet Men',       0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'ICW', 'Instinctive Cadet Women',     0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IMM', 'Instinctive Master Men',      0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IMW', 'Instinctive Master Women',    0, 0, 0, $Elim1, $Elim2);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'MT',  'Men Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'WT',  'Women Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'MJT',  'Men Junior Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'WJT',  'Women Junior Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'MCT',  'Men Cadet Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'WCT',  'Women Cadet Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'MMT',  'Men Master Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'WMT',  'Women Master Team');
			break;
		case '2':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CM',  'Compound Men',          0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CW',  'Compound Women',        0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BM',  'Barebow Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BW',  'Barebow Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LM',  'Longbow Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LW',  'Longbow Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IM',  'Instinctive Men',       0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IW',  'Instinctive Women',     0, 0, 0, $Elim1, $Elim2);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'MT',  'Men Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'WT',  'Women Team');
			break;
	}
}

function InsertStandard3DEvents($TourId, $SubRule) {
	switch($SubRule) {
		case '1':
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
