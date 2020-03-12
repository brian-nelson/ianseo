<?php

/*

STANDARD DEFINITIONS (Target Tournaments)

*/

/*

FIELD DEFINITIONS (Target Tournaments)

*/

// creation of standard NZ tournament competition classes
function CreateStandardFieldClasses($TourId, $SubRule) {
	$i=1;
	switch($SubRule) {
		case '1': // All NZ Classes
			CreateClass($TourId, $i++, 21, 49, 0, 'SM', 'SM', 'Senior Men');
			CreateClass($TourId, $i++, 21, 49, 1, 'SW', 'SW', 'Senior Women');
			CreateClass($TourId, $i++, 18, 20, 0, 'JM', 'JM,SM', 'Junior Men');
			CreateClass($TourId, $i++, 18, 20, 1, 'JW', 'JW,SW', 'Junior Women');
			CreateClass($TourId, $i++, 16, 17, 0, 'CM', 'CM,JM,SM', 'Cadet Men');
			CreateClass($TourId, $i++, 16, 17, 1, 'CW', 'CW,JW,SW', 'Cadet Women');
			CreateClass($TourId, $i++, 50, 64, 0, 'MM', 'MM,SM', 'Masters Men (50-64)');
			CreateClass($TourId, $i++, 50, 64, 1, 'MW', 'MW,SW', 'Masters Women (50-64)');
			CreateClass($TourId, $i++, 65, 100, 0, 'VM', 'VM,MM,SM', 'Masters Men (65+)');
			CreateClass($TourId, $i++, 65, 100, 1, 'VW', 'VW,MW,SW', 'Masters Women (65+)');
			CreateClass($TourId, $i++, 14, 15, 0, 'IB', 'IB,CM,JM,SM', 'Intermediate Boys');
			CreateClass($TourId, $i++, 14, 15, 1, 'IG', 'IG,CW,JW,SW', 'Intermediate Girls');
			CreateClass($TourId, $i++, 11, 13, 0, 'YB', 'YB,IB,CM,JM', 'Cub Boys');
			CreateClass($TourId, $i++, 11, 13, 1, 'YG', 'YG,IG,CW,JW', 'Cub Girls');
			CreateClass($TourId, $i++, 1, 10, 0, 'KB', 'KB,YB,IB,CM,JM', 'Kiwi Boys');
			CreateClass($TourId, $i++, 1, 10, 1, 'KG', 'KG,YG,IG,CW,JW', 'Kiwi Girls');
			break;
		case '2': // Senior NZ & WA Classes
			CreateClass($TourId, $i++, 21, 49, 0, 'SM', 'SM', 'Senior Men');
			CreateClass($TourId, $i++, 21, 49, 1, 'SW', 'SW', 'Senior Women');
			CreateClass($TourId, $i++, 18, 20, 0, 'JM', 'JM,SM', 'Junior Men');
			CreateClass($TourId, $i++, 18, 20, 1, 'JW', 'JW,SW', 'Junior Women');
			CreateClass($TourId, $i++, 1, 17, 0, 'CM', 'CM,JM,SM', 'Cadet Men');
			CreateClass($TourId, $i++, 1, 17, 1, 'CW', 'CW,JW,SW', 'Cadet Women');
			CreateClass($TourId, $i++, 50, 64, 0, 'MM', 'MM,SM', 'Masters Men (50-64)');
			CreateClass($TourId, $i++, 50, 64, 1, 'MW', 'MW,SW', 'Masters Women (50-64)');
			CreateClass($TourId, $i++, 65, 100, 0, 'VM', 'VM,MM,SM', 'Masters Men (65+)');
			CreateClass($TourId, $i++, 65, 100, 1, 'VW', 'VW,MW,SW', 'Masters Women (65+)');
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

// creation of standard NZ individual matchplay competition events
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
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'RM',  'Recurve Men',             0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'RW',  'Recurve Women',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'RJM', 'Recurve Junior Men',      0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'RJW', 'Recurve Junior Women',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'CM',  'Compound Men',            0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'CW',  'Compound Women',          0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'CJM', 'Compound Junior Men',     0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 1, 4, 3, 1, 'CJW', 'Compound Junior Women',   0, 0, 0, $Elim1, $Elim2);
			break;
		case '2':
		case '3':
			// no team defaults
			break;
	}
}

function InsertStandardFieldEvents($TourId, $SubRule) {
	switch($SubRule) {
		case '1':
			InsertClassEvent($TourId, 0, 1, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 0, 1, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 0, 1, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 0, 1, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 0, 1, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 0, 1, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 0, 1, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 0, 1, 'CJW', 'C', 'JW');
			break;
		case '2':
		case '3':
			break;
	}
}

function InsertStandardFieldEliminations($TourId, $SubRule){
	$cls=array();
	switch($SubRule) {
		case '1':
			$cls=array('SM', 'SW', 'JM', 'JW', 'CM', 'CW', 'MM', 'MW');
			break;
		case '2':
			$cls=array('SM', 'SW', 'JM', 'JW');
			break;
	}
	foreach(array('R', 'C', 'B', 'L') as $div) {
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

