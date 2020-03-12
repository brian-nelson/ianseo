<?php

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = '';
if(empty($SubRule)) $SubRule='1';

function CreateStandardDivisions($TourId, $Type='FITA') {
	$i=1;
	CreateDivision($TourId, $i++, 'R', 'Recurve Open');
	CreateDivision($TourId, $i++, 'C', 'Compound Open');
	CreateDivision($TourId, $i++, 'W1', 'W1 Open (Rec/Comp)');
	CreateDivision($TourId, $i++, 'VI', 'Visually Impaired');
}

function CreateStandardClasses($TourId, $SubRule) {
	CreateClass($TourId, 1, 1,100, 0, 'M', 'M', 'Men', 1, 'C,R,W1');
	CreateClass($TourId, 2, 1,100, 1, 'W', 'W', 'Women', 1, 'C,R,W1');
	CreateClass($TourId, 3, 1,100, -1, '1', '1', '1', 1, 'VI');
	CreateClass($TourId, 4, 1,100, -1, '23', '23', '2/3', 1, 'VI');
}

function CreateStandardEvents($TourId, $SubRule, $Outdoor=true) {
	$TargetR=($Outdoor?5:2);
	$TargetC=($Outdoor?9:4);
    $TargetW1=($Outdoor?5:4);
	$TargetSizeR=($Outdoor ? 122 : 40);
	$TargetSizeC=($Outdoor ? 80 : 40);
	$TargetSizeV=($Outdoor ? 80 : 60);
	$DistanceR=($Outdoor ? 70 : 18);
	$DistanceC=($Outdoor ? 50 : 18);
	$DistanceV=($Outdoor ? 30 : 18);

	$i=1;
	CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'RMO', 'Recurve Men Open', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
	CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'RWO', 'Recurve Women Open', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
	CreateEvent($TourId, $i++, 0, 0, 32, $TargetC, 5, 3, 1, 5, 3, 1, 'CMO', 'Compound Men Open', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
	CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'CWO', 'Compound Women Open', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
	CreateEvent($TourId, $i++, 0, 0,  8, $TargetW1, 5, 3, 1, 5, 3, 1, 'MW1', 'Men W1 Open (Rec/Comp)', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
	CreateEvent($TourId, $i++, 0, 0,  8, $TargetW1, 5, 3, 1, 5, 3, 1, 'WW1', 'Women W1 Open (Rec/Comp)', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
	CreateEvent($TourId, $i++, 0, 0,  2, $TargetR, 5, 3, 1, 5, 3, 1, 'VI1',  'Visually Impaired 1', 1, 240, 240, 0, 0, '', '', $TargetSizeV, $DistanceV);
	CreateEvent($TourId, $i++, 0, 0,  2, $TargetR, 5, 3, 1, 5, 3, 1, 'VI23',  'Visually Impaired 2/3', 1, 240, 240, 0, 0, '', '', $TargetSizeV, $DistanceV);
	$i=1;
	CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RMO', 'Recurve Men Open Team',1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
	CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'RWO', 'Recurve Women Open Team',1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
	if($Outdoor)
		CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 4, 2, 4, 4, 2, 'RXO', 'Recurve Open Mixed Team',1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
	CreateEvent($TourId, $i++, 1, 0, 8, $TargetW1, 4, 6, 3, 4, 6, 3, 'MW1', 'Men W1 Open (Rec/Comp) Team',0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
	CreateEvent($TourId, $i++, 1, 0, 8, $TargetW1, 4, 6, 3, 4, 6, 3, 'WW1', 'Women W1 Open (Rec/Comp) Team',0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
	if($Outdoor)
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetW1, 4, 4, 2, 4, 4, 2, 'W1X', 'W1 Open (Rec/Comp) Mixed Team',0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
	CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CMO', 'Compound Men Open Team',0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceV);
	CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CWO', 'Compound Women Open Team',0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceV);
	if($Outdoor)
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 4, 2, 4, 4, 2, 'CXO', 'Compound Open Mixed Team',0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceV);


}

function InsertStandardEvents($TourId, $SubRule, $Outdoor=true) {
	InsertClassEvent($TourId, 0, 1, 'RMO', 'R', 'M');
	InsertClassEvent($TourId, 0, 1, 'RWO', 'R', 'W');
	InsertClassEvent($TourId, 0, 1, 'MW1', 'W1', 'M');
	InsertClassEvent($TourId, 0, 1, 'WW1', 'W1', 'W');
	InsertClassEvent($TourId, 0, 1, 'CMO', 'C', 'M');
	InsertClassEvent($TourId, 0, 1, 'CWO', 'C', 'W');
	InsertClassEvent($TourId, 0, 1, 'VIM', 'VI', 'M');
	InsertClassEvent($TourId, 0, 1, 'VIW', 'VI', 'W');

	InsertClassEvent($TourId, 1, 3, 'RMO', 'R', 'M');
	InsertClassEvent($TourId, 1, 3, 'RWO', 'R', 'W');
	InsertClassEvent($TourId, 1, 3, 'CMO', 'C', 'M');
	InsertClassEvent($TourId, 1, 3, 'CWO', 'C', 'W');
	InsertClassEvent($TourId, 1, 3, 'MW1', 'W1', 'M');
	InsertClassEvent($TourId, 1, 3, 'WW1', 'W1', 'W');
	if($Outdoor) {
		InsertClassEvent($TourId, 1, 1, 'RXO', 'R', 'W');
		InsertClassEvent($TourId, 2, 1, 'RXO', 'R', 'M');
		InsertClassEvent($TourId, 1, 1, 'CXO', 'C', 'W');
		InsertClassEvent($TourId, 2, 1, 'CXO', 'C', 'M');
		InsertClassEvent($TourId, 1, 1, 'W1X', 'W1', 'W');
		InsertClassEvent($TourId, 2, 1, 'W1X', 'W1', 'M');
	}
}

