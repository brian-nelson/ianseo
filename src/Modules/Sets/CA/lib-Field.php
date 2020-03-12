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
			CreateClass($TourId, 1, 21, 49, 0, 'M', 'M', get_text('CA-Cl-M', 'Languages'));
			CreateClass($TourId, 2, 21, 49, 1, 'W', 'W', get_text('CA-Cl-W', 'Languages'));
			CreateClass($TourId, 3, 18, 20, 0, 'JM', 'JM,M', get_text('CA-Cl-JM', 'Languages'));
			CreateClass($TourId, 4, 18, 20, 1, 'JW', 'JW,W', get_text('CA-Cl-JW', 'Languages'));
			CreateClass($TourId, 5,  1, 17, 0, 'CM', 'CM,JM,M', get_text('CA-Cl-CM', 'Languages'));
			CreateClass($TourId, 6,  1, 17, 1, 'CW', 'CW,JW,W', get_text('CA-Cl-CW', 'Languages'));
			CreateClass($TourId, 7, 50,100, 0, 'MM', 'MM,M', get_text('CA-Cl-MM', 'Languages'));
			CreateClass($TourId, 8, 50,100, 1, 'MW', 'MW,W', get_text('CA-Cl-MW', 'Languages'));
			break;
		case '2':
			CreateClass($TourId, 1, 21,100, 0, 'M', 'M', get_text('CA-Cl-M', 'Languages'));
			CreateClass($TourId, 2, 21,100, 1, 'W', 'W', get_text('CA-Cl-W', 'Languages'));
			CreateClass($TourId, 3, 1, 20, 0, 'JM', 'JM,M', get_text('CA-Cl-JM', 'Languages'));
			CreateClass($TourId, 4, 1, 20, 1, 'JW', 'JW,W', get_text('CA-Cl-JW', 'Languages'));
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
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RM',  get_text('CA-Ev-RM',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RW',  get_text('CA-Ev-RW',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RJM', get_text('CA-Ev-RJM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RJW', get_text('CA-Ev-RJW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RCM', get_text('CA-Ev-RCM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RCW', get_text('CA-Ev-RCW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RMM', get_text('CA-Ev-RMM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RMW', get_text('CA-Ev-RMW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CM',  get_text('CA-Ev-CM',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CW',  get_text('CA-Ev-CW',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CJM', get_text('CA-Ev-CJM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CJW', get_text('CA-Ev-CJW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CCM', get_text('CA-Ev-CCM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CCW', get_text('CA-Ev-CCW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CMM', get_text('CA-Ev-CMM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CMW', get_text('CA-Ev-CMW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BM',  get_text('CA-Ev-BM',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BW',  get_text('CA-Ev-BW',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BJM', get_text('CA-Ev-BJM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BJW', get_text('CA-Ev-BJW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BCM', get_text('CA-Ev-BCM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BCW', get_text('CA-Ev-BCW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BMM', get_text('CA-Ev-BMM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BMW', get_text('CA-Ev-BMW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MT',  get_text('CA-Ev-MT',  'Languages'),0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WT',  get_text('CA-Ev-WT',  'Languages'),0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MJT', get_text('CA-Ev-MJT', 'Languages'),0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WJT', get_text('CA-Ev-WJT', 'Languages'),0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MCT', get_text('CA-Ev-MCT', 'Languages'),0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WCT', get_text('CA-Ev-WCT', 'Languages'),0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MMT', get_text('CA-Ev-MMT', 'Languages'),0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WMT', get_text('CA-Ev-WMT', 'Languages'),0,248,15);
			break;
		case '2':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RM',  get_text('CA-Ev-RM',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RW',  get_text('CA-Ev-RW',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RJM', get_text('CA-Ev-RJM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RJW', get_text('CA-Ev-RJW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CM',  get_text('CA-Ev-CM',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CW',  get_text('CA-Ev-CW',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CJM', get_text('CA-Ev-CJM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CJW', get_text('CA-Ev-CJW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BM',  get_text('CA-Ev-BM',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BW',  get_text('CA-Ev-BW',  'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BJM', get_text('CA-Ev-BJM', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BJW', get_text('CA-Ev-BJW', 'Languages'), 0, 0, 0, $Elim1, $Elim2);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 4, 6,  8, 3, 3, 4, 3, 3, 'MT',  get_text('CA-Ev-MT',  'Languages'),0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6,  8, 3, 3, 4, 3, 3, 'WT',  get_text('CA-Ev-WT',  'Languages'),0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6,  8, 3, 3, 4, 3, 3, 'MJT', get_text('CA-Ev-MJT', 'Languages'),0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6,  8, 3, 3, 4, 3, 3, 'WJT', get_text('CA-Ev-WJT', 'Languages'),0,248,15);
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

