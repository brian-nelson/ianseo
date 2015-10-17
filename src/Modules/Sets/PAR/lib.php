<?php

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = '';
if(empty($SubRule)) $SubRule='1';

function CreateStandardDivisions($TourId, $Type='FITA') {
	$i=1;
	CreateDivision($TourId, $i++, 'C', 'Compound Open');
	CreateDivision($TourId, $i++, 'W1', 'Compound/Recurve W1');
	CreateDivision($TourId, $i++, 'R', 'Recurve');
	CreateDivision($TourId, $i++, 'VI', 'Visually Impaired');
}

function CreateStandardClasses($TourId, $SubRule) {
	CreateClass($TourId, 1, 1,100, 0, 'M', 'M', '~M');
	CreateClass($TourId, 2, 1,100, 1, 'W', 'W', '~F');
//	CreateClass($TourId, 3, 1, 20, 0, 'JM', 'JM,M', '~JM');
//	CreateClass($TourId, 4, 1, 20, 1, 'JW', 'JW,W', '~JF');
}

function CreateStandardEvents($TourId, $SubRule, $Outdoor=true) {
	$TargetR=($Outdoor?5:2);
	$TargetC=($Outdoor?9:4);
	$TargetSizeR=($Outdoor ? 122 : 40);
	$TargetSizeC=($Outdoor ? 80 : 40);
	$TargetSizeV=($Outdoor ? 80 : 60);
	$DistanceR=($Outdoor ? 70 : 18);
	$DistanceC=($Outdoor ? 50 : 18);
	$DistanceV=($Outdoor ? 30 : 18);

	$i=1;
	CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 5, 1, 5, 3, 1, 'RMO', 'Recurve Open Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
	CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'RWO', 'Recurve Open Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
	CreateEvent($TourId, $i++, 0, 0,  8, $TargetC, 5, 3, 1, 5, 3, 1, 'W1M', 'Compound/Recurve W1 Men', 0, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceC);
	CreateEvent($TourId, $i++, 0, 0,  8, $TargetC, 5, 3, 1, 5, 3, 1, 'C1W', 'Compound/Recurve W1 Women', 0, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceC);
	CreateEvent($TourId, $i++, 0, 0, 32, $TargetC, 5, 3, 1, 5, 3, 1, 'CMO', 'Compound Open Men', 0, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceC);
	CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'CWO', 'Compound Open Women', 0, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceC);
	CreateEvent($TourId, $i++, 0, 0,  2, $TargetR, 5, 3, 1, 5, 3, 1, 'VIM',  'Visually Impaired Men', 1, 240, 240, 0, 0, '', '', $TargetSizeV, $DistanceV);
	CreateEvent($TourId, $i++, 0, 0,  2, $TargetR, 5, 3, 1, 5, 3, 1, 'VIW',  'Visually Impaired Women', 1, 240, 240, 0, 0, '', '', $TargetSizeV, $DistanceV);
	$i=1;
	CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RMO', 'Recurve Open Men Team',1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
	CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'RWO', 'Recurve Open Women Team',1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
	if($Outdoor)
		CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 4, 2, 4, 4, 2, 'RXO', 'Recurve Open Mixed Team',1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
	CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'W1M', 'Compound/Recurve Men W1 Team',0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
	CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'W1W', 'Compound/Recurve Women W1 Team',0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
	if($Outdoor)
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'W1X', 'Compound/Recurve W1 Mixed Team',0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
	CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CMO', 'Compound Open Men Team',0, 0, 0, 0, 0, '', '', $TargetSizeV, $DistanceV);
	CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CWO', 'Compound Open Men Team',0, 0, 0, 0, 0, '', '', $TargetSizeV, $DistanceV);
	if($Outdoor)
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 4, 2, 4, 4, 2, 'CXO', 'Compound Open Mixed Team',0, 0, 0, 0, 0, '', '', $TargetSizeV, $DistanceV);
	
	
}

function InsertStandardEvents($TourId, $SubRule, $Outdoor=true) {
	InsertClassEvent($TourId, 0, 1, 'RMO', 'R', 'M');
	InsertClassEvent($TourId, 0, 1, 'RWO', 'R', 'W');
	InsertClassEvent($TourId, 0, 1, 'W1M', 'W1', 'M');
	InsertClassEvent($TourId, 0, 1, 'W1W', 'W1', 'W');
	InsertClassEvent($TourId, 0, 1, 'CMO', 'C', 'M');
	InsertClassEvent($TourId, 0, 1, 'CWO', 'C', 'W');
	InsertClassEvent($TourId, 0, 1, 'VIM', 'VI', 'M');
	InsertClassEvent($TourId, 0, 1, 'VIW', 'VI', 'W');

	InsertClassEvent($TourId, 1, 3, 'RMO', 'R', 'M');
	InsertClassEvent($TourId, 1, 3, 'RWO', 'R', 'W');
	InsertClassEvent($TourId, 1, 3, 'CMO', 'C', 'M');
	InsertClassEvent($TourId, 1, 3, 'CWO', 'C', 'W');
	InsertClassEvent($TourId, 1, 3, 'W1M', 'W1', 'M');
	InsertClassEvent($TourId, 1, 3, 'W1W', 'W1', 'W');
	if($Outdoor) {
		InsertClassEvent($TourId, 1, 1, 'RXO', 'R', 'W');
		InsertClassEvent($TourId, 2, 1, 'RXO', 'R', 'M');
		InsertClassEvent($TourId, 1, 1, 'CXO', 'C', 'W');
		InsertClassEvent($TourId, 2, 1, 'CXO', 'C', 'M');
		InsertClassEvent($TourId, 1, 1, 'W1X', 'W1', 'W');
		InsertClassEvent($TourId, 2, 1, 'W1X', 'W1', 'M');
	}
}

?>