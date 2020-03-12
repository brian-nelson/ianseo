<?php
/*

STANDARD THINGS

*/

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'NED';
if(empty($SubRule)) $SubRule='1';

function CreateStandardDivisions($TourId, $Type=1, $SubRule=0) 
{
	$i=1;
	
	CreateDivision($TourId,$i++,'C','Compound');
	CreateDivision($TourId,$i++,'R','Recurve');
}

function CreateStandardClasses($TourId, $SubRule, $Field='', $Type=0) {
	$i=1;
	
	CreateClass($TourId, $i++, 0, 100, 0, 'H', 'H', 'Heren',1,'R,C');
	CreateClass($TourId, $i++, 0, 100, 1, 'D', 'D', 'Damen',1,'R,C');
	
}

function CreateStandardSubClasses($TourId) {
	
}

function CreateStandardEvents($TourId, $SubRule, $Outdoor=true) {
	$TargetR=($Outdoor?5:2);
	$TargetC=($Outdoor?9:4);
	$TargetSizeR=($Outdoor ? 122 : 40);
	$TargetSizeC=($Outdoor ? 80 : 40);
	$DistanceR=($Outdoor ? 70 : 18);
	$DistanceC=($Outdoor ? 50 : 18);
	
	switch($SubRule) {
		default:
			$i=1;
			CreateEvent($TourId, $i++, 0, 0,16, $TargetR, 5, 3, 1, 5, 3, 1, 'RH',  'Recurve Heren', 1, 240, 240, 0, 0, 'RM', 'RM', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'RD',  'Recurve Damen', 1, 240, 240, 0, 0, 'RW', 'RW', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'CH',  'Compound Heren', 0, 240, 240, 0, 0, 'CM', 'CM', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'CD',  'Compound Damen', 0, 240, 240, 0, 0, 'CW', 'CW', $TargetSizeC, $DistanceC);
			break;
	}
}

function InsertStandardEvents($TourId, $SubRule) {
	switch($SubRule) {
		default:
			InsertClassEvent($TourId, 0, 1, 'RH',  'R',  'H');
			InsertClassEvent($TourId, 0, 1, 'RD',  'R',  'D');
			InsertClassEvent($TourId, 0, 1, 'CH',  'C',  'H');
			InsertClassEvent($TourId, 0, 1, 'CD',  'C',  'D');
			break;
	}
}