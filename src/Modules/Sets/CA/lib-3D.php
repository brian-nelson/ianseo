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
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CM',  get_text('CA-Ev-CM',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CW',  get_text('CA-Ev-CW',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CJM', get_text('CA-Ev-CJM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CJW', get_text('CA-Ev-CJW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CCM', get_text('CA-Ev-CCM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CCW', get_text('CA-Ev-CCW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CMM', get_text('CA-Ev-CMM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CMW', get_text('CA-Ev-CMW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BM',  get_text('CA-Ev-BM',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BW',  get_text('CA-Ev-BW',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BJM', get_text('CA-Ev-BJM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BJW', get_text('CA-Ev-BJW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BCM', get_text('CA-Ev-BCM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BCW', get_text('CA-Ev-BCW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BMM', get_text('CA-Ev-BMM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BMW', get_text('CA-Ev-BMW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LM',  get_text('CA-Ev-LM',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LW',  get_text('CA-Ev-LW',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LJM', get_text('CA-Ev-LJM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LJW', get_text('CA-Ev-LJW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LCM', get_text('CA-Ev-LCM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LCW', get_text('CA-Ev-LCW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LMM', get_text('CA-Ev-LMM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LMW', get_text('CA-Ev-LMW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IM',  get_text('CA-Ev-IM',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IW',  get_text('CA-Ev-IW',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IJM', get_text('CA-Ev-IJM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IJW', get_text('CA-Ev-IJW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'ICM', get_text('CA-Ev-ICM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'ICW', get_text('CA-Ev-ICW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IMM', get_text('CA-Ev-IMM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IMW', get_text('CA-Ev-IMW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'MT',  get_text('CA-Ev-MT',  'Languages'));
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'WT',  get_text('CA-Ev-WT',  'Languages'));
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'MJT', get_text('CA-Ev-MJT', 'Languages'));
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'WJT', get_text('CA-Ev-WJT', 'Languages'));
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'MCT', get_text('CA-Ev-MCT', 'Languages'));
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'WCT', get_text('CA-Ev-WCT', 'Languages'));
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'MMT', get_text('CA-Ev-MMT', 'Languages'));
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'WMT', get_text('CA-Ev-WMT', 'Languages'));
			break;
		case '2':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CM', get_text('CA-Ev-CM',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CW', get_text('CA-Ev-CW',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BM', get_text('CA-Ev-BM',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BW', get_text('CA-Ev-BW',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LM', get_text('CA-Ev-LM',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LW', get_text('CA-Ev-LW',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IM', get_text('CA-Ev-IM',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IW', get_text('CA-Ev-IW',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'MT', get_text('CA-Ev-MT',  'Languages'));
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'WT', get_text('CA-Ev-WT',  'Languages'));
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
