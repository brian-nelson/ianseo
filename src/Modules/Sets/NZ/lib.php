<?php

/*

STANDARD DEFINITIONS (Target Tournaments)

*/

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'NZ';
if(empty($SubRule)) $SubRule='1';

// creation of standard NZ tournament bow types
function CreateStandardDivisions($TourId, $Type, $SubRule) {
	$i=1;
	CreateDivision($TourId, $i++, 'R', 'Recurve');
	CreateDivision($TourId, $i++, 'C', 'Compound');
	CreateDivision($TourId, $i++, 'B', 'Barebow');
	CreateDivision($TourId, $i++, 'L', 'Longbow');
	CreateDivision($TourId, $i++, 'X', 'Crossbow');
	
}

// creation of standard NZ tournament competition classes
function CreateStandardClasses($TourId, $SubRule) {
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
function CreateStandardEvents($TourId, $SubRule, $Outdoor=true) {
	$TargetR=($Outdoor?5:2);
	$TargetC=($Outdoor?9:4);
	$TargetSizeR=($Outdoor ? 122 : 40);
	$TargetSizeC=($Outdoor ? 80 : 40);
	$DistanceR=($Outdoor ? 70 : 18);
	$DistanceRcm=($Outdoor ? 60 : 18);
	$DistanceI=($Outdoor ? 45 : 18);
	$DistanceY=($Outdoor ? 35 : 18);
	$DistanceK=($Outdoor ? 20 : 18);
	$DistanceC=($Outdoor ? 50 : 18);
	$FirstPhase = ($Outdoor ? 48 : 16);
	switch($SubRule) {
		case '1':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM',  'Recurve Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RW',  'Recurve Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RJM', 'Recurve Junior Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RJW', 'Recurve Junior Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RCM', 'Recurve Cadet Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RCW', 'Recurve Cadet Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RMM', 'Recurve Master Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RMW', 'Recurve Master Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RIB', 'Recurve Intermediate Boys', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RIG', 'Recurve Intermediate Girls', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RYB', 'Recurve Cub Boys', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceY);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RYG', 'Recurve Cub Girls', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceY);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RKB', 'Recurve Kiwi Boys', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceK);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RKG', 'Recurve Kiwi Girls', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceK);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM',  'Compound Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW',  'Compound Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CJM', 'Compound Junior Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CJW', 'Compound Junior Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CCM', 'Compound Cadet Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CCW', 'Compound Cadet Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CMM', 'Compound Master Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CMW', 'Compound Master Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'CIB', 'Compound Intermediate Boys', 0, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'CIG', 'Compound Intermediate Girls', 0, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'CYB', 'Compound Cub Boys', 0, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceY);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'CYG', 'Compound Cub Girls', 0, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceY);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'CKB', 'Compound Kiwi Boys', 0, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceK);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'CKG', 'Compound Kiwi Girls', 0, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceK);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RM',  'Recurve Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RW',  'Recurve Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RJM', 'Recurve Junior Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RJW', 'Recurve Junior Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RCM', 'Recurve Cadet Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RCW', 'Recurve Cadet Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RMM', 'Recurve Master Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RMW', 'Recurve Master Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RX',  'Recurve Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RJX', 'Recurve Junior Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RCX', 'Recurve Cadet Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RMX', 'Recurve Master Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			}
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CM',  'Compound Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CW',  'Compound Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CJM', 'Compound Junior Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CJW', 'Compound Junior Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CCM', 'Compound Cadet Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CCW', 'Compound Cadet Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CMM', 'Compound Master Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CMW', 'Compound Master Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetC, 4, 4, 2, 4, 4, 2, 'CX',  'Compound Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetC, 4, 4, 2, 4, 4, 2, 'CJX', 'Compound Junior Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetC, 4, 4, 2, 4, 4, 2, 'CCX', 'Compound Cadet Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetC, 4, 4, 2, 4, 4, 2, 'CMX', 'Compound Master Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			}
			break;
		case '2':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM',  'Recurve Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RW',  'Recurve Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RJM', 'Recurve Junior Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RJW', 'Recurve Junior Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RCM', 'Recurve Cadet Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RCW', 'Recurve Cadet Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RMM', 'Recurve Master Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RMW', 'Recurve Master Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM',  'Compound Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW',  'Compound Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CJM', 'Compound Junior Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CJW', 'Compound Junior Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CCM', 'Compound Cadet Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CCW', 'Compound Cadet Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CMM', 'Compound Master Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CMW', 'Compound Master Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RM',  'Recurve Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RW',  'Recurve Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RJM', 'Recurve Junior Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RJW', 'Recurve Junior Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RCM', 'Recurve Cadet Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RCW', 'Recurve Cadet Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RMM', 'Recurve Master Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RMW', 'Recurve Master Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RX',  'Recurve Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RJX', 'Recurve Junior Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RCX', 'Recurve Cadet Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RMX', 'Recurve Master Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			}
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CM',  'Compound Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CW',  'Compound Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CJM', 'Compound Junior Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CJW', 'Compound Junior Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CCM', 'Compound Cadet Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CCW', 'Compound Cadet Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CMM', 'Compound Master Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CMW', 'Compound Master Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetC, 4, 4, 2, 4, 4, 2, 'CX',  'Compound Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetC, 4, 4, 2, 4, 4, 2, 'CJX', 'Compound Junior Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetC, 4, 4, 2, 4, 4, 2, 'CCX', 'Compound Cadet Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetC, 4, 4, 2, 4, 4, 2, 'CMX', 'Compound Master Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			}
			break;
		case '3':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RJM', 'Recurve Junior Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RJW', 'Recurve Junior Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RCM', 'Recurve Cadet Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RCW', 'Recurve Cadet Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RIB', 'Recurve Intermediate Boys', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RIG', 'Recurve Intermediate Girls', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RYB', 'Recurve Cub Boys', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceY);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RYG', 'Recurve Cub Girls', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceY);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RKB', 'Recurve Kiwi Boys', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceK);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RKG', 'Recurve Kiwi Girls', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceK);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CJM', 'Compound Junior Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CJW', 'Compound Junior Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CCM', 'Compound Cadet Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CCW', 'Compound Cadet Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'CIB', 'Compound Intermediate Boys', 0, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'CIG', 'Compound Intermediate Girls', 0, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'CYB', 'Compound Cub Boys', 0, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceY);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'CYG', 'Compound Cub Girls', 0, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceY);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'CKB', 'Compound Kiwi Boys', 0, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceK);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'CKG', 'Compound Kiwi Girls', 0, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceK);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RJM', 'Recurve Junior Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RJW', 'Recurve Junior Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RCM', 'Recurve Cadet Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RCW', 'Recurve Cadet Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RJX', 'Recurve Junior Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RCX', 'Recurve Cadet Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			}
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CJM', 'Compound Junior Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CJW', 'Compound Junior Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CCM', 'Compound Cadet Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CCW', 'Compound Cadet Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetC, 4, 4, 2, 4, 4, 2, 'CJX', 'Compound Junior Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetC, 4, 4, 2, 4, 4, 2, 'CCX', 'Compound Cadet Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			}
			break;
	}
}

//SQL insertion of matchplay events
function InsertStandardEvents($TourId, $SubRule) {
	switch($SubRule) {
		case '1':
			InsertClassEvent($TourId, 0, 1, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 0, 1, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 0, 1, 'RCM', 'R', 'CM');
			InsertClassEvent($TourId, 0, 1, 'RMM', 'R', 'MM');
			InsertClassEvent($TourId, 0, 1, 'RIB', 'R', 'IB');
			InsertClassEvent($TourId, 0, 1, 'RYB', 'R', 'YB');
			InsertClassEvent($TourId, 0, 1, 'RKB', 'R', 'KB');
			InsertClassEvent($TourId, 0, 1, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 0, 1, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 0, 1, 'RCW', 'R', 'CW');
			InsertClassEvent($TourId, 0, 1, 'RMW', 'R', 'MW');
			InsertClassEvent($TourId, 0, 1, 'RIG', 'R', 'IG');
			InsertClassEvent($TourId, 0, 1, 'RYG', 'R', 'YG');
			InsertClassEvent($TourId, 0, 1, 'RKG', 'R', 'KG');
			InsertClassEvent($TourId, 0, 1, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 0, 1, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 0, 1, 'CCM', 'C', 'CM');
			InsertClassEvent($TourId, 0, 1, 'CMM', 'C', 'MM');
			InsertClassEvent($TourId, 0, 1, 'CIB', 'C', 'IB');
			InsertClassEvent($TourId, 0, 1, 'CYB', 'C', 'YB');
			InsertClassEvent($TourId, 0, 1, 'CKB', 'C', 'KB');
			InsertClassEvent($TourId, 0, 1, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 0, 1, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 0, 1, 'CCW', 'C', 'CW');
			InsertClassEvent($TourId, 0, 1, 'CMW', 'C', 'MW');
			InsertClassEvent($TourId, 0, 1, 'CIG', 'C', 'IG');
			InsertClassEvent($TourId, 0, 1, 'CYG', 'C', 'YG');
			InsertClassEvent($TourId, 0, 1, 'CKG', 'C', 'KG');
			
			InsertClassEvent($TourId, 1, 3, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 1, 3, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 1, 3, 'RCM', 'R', 'CM');
			InsertClassEvent($TourId, 1, 3, 'RMM', 'R', 'MM');
			InsertClassEvent($TourId, 1, 3, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 1, 3, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 1, 3, 'RCW', 'R', 'CW');
			InsertClassEvent($TourId, 1, 3, 'RMW', 'R', 'MW');
			InsertClassEvent($TourId, 1, 1, 'RX',  'R',  'W');
			InsertClassEvent($TourId, 2, 1, 'RX',  'R',  'M');
			InsertClassEvent($TourId, 1, 1, 'RJX', 'R', 'JW');
			InsertClassEvent($TourId, 2, 1, 'RJX', 'R', 'JM');
			InsertClassEvent($TourId, 1, 1, 'RCX', 'R', 'CW');
			InsertClassEvent($TourId, 2, 1, 'RCX', 'R', 'CM');
			InsertClassEvent($TourId, 1, 1, 'RMX', 'R', 'MW');
			InsertClassEvent($TourId, 2, 1, 'RMX', 'R', 'MM');
			InsertClassEvent($TourId, 1, 3, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 1, 3, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 1, 3, 'CCM', 'C', 'CM');
			InsertClassEvent($TourId, 1, 3, 'CMM', 'C', 'MM');
			InsertClassEvent($TourId, 1, 3, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 1, 3, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 1, 3, 'CCW', 'C', 'CW');
			InsertClassEvent($TourId, 1, 3, 'CMW', 'C', 'MW');
			InsertClassEvent($TourId, 1, 1, 'CX',  'C',  'W');
			InsertClassEvent($TourId, 2, 1, 'CX',  'C',  'M');
			InsertClassEvent($TourId, 1, 1, 'CJX', 'C', 'JW');
			InsertClassEvent($TourId, 2, 1, 'CJX', 'C', 'JM');
			InsertClassEvent($TourId, 1, 1, 'CCX', 'C', 'CW');
			InsertClassEvent($TourId, 2, 1, 'CCX', 'C', 'CM');
			InsertClassEvent($TourId, 1, 1, 'CMX', 'C', 'MW');
			InsertClassEvent($TourId, 2, 1, 'CMX', 'C', 'MM');
			break;
		case '2':
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
			
			InsertClassEvent($TourId, 1, 3, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 1, 3, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 1, 3, 'RCM', 'R', 'CM');
			InsertClassEvent($TourId, 1, 3, 'RMM', 'R', 'MM');
			InsertClassEvent($TourId, 1, 3, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 1, 3, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 1, 3, 'RCW', 'R', 'CW');
			InsertClassEvent($TourId, 1, 3, 'RMW', 'R', 'MW');
			InsertClassEvent($TourId, 1, 1, 'RX',  'R',  'W');
			InsertClassEvent($TourId, 2, 1, 'RX',  'R',  'M');
			InsertClassEvent($TourId, 1, 1, 'RJX', 'R', 'JW');
			InsertClassEvent($TourId, 2, 1, 'RJX', 'R', 'JM');
			InsertClassEvent($TourId, 1, 1, 'RCX', 'R', 'CW');
			InsertClassEvent($TourId, 2, 1, 'RCX', 'R', 'CM');
			InsertClassEvent($TourId, 1, 1, 'RMX', 'R', 'MW');
			InsertClassEvent($TourId, 2, 1, 'RMX', 'R', 'MM');
			InsertClassEvent($TourId, 1, 3, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 1, 3, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 1, 3, 'CCM', 'C', 'CM');
			InsertClassEvent($TourId, 1, 3, 'CMM', 'C', 'MM');
			InsertClassEvent($TourId, 1, 3, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 1, 3, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 1, 3, 'CCW', 'C', 'CW');
			InsertClassEvent($TourId, 1, 3, 'CMW', 'C', 'MW');
			InsertClassEvent($TourId, 1, 1, 'CX',  'C',  'W');
			InsertClassEvent($TourId, 2, 1, 'CX',  'C',  'M');
			InsertClassEvent($TourId, 1, 1, 'CJX', 'C', 'JW');
			InsertClassEvent($TourId, 2, 1, 'CJX', 'C', 'JM');
			InsertClassEvent($TourId, 1, 1, 'CCX', 'C', 'CW');
			InsertClassEvent($TourId, 2, 1, 'CCX', 'C', 'CM');
			InsertClassEvent($TourId, 1, 1, 'CMX', 'C', 'MW');
			InsertClassEvent($TourId, 2, 1, 'CMX', 'C', 'MM');
			break;
		case '3':
			InsertClassEvent($TourId, 0, 1, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 0, 1, 'RCM', 'R', 'CM');
			InsertClassEvent($TourId, 0, 1, 'RIB', 'R', 'IB');
			InsertClassEvent($TourId, 0, 1, 'RYB', 'R', 'YB');
			InsertClassEvent($TourId, 0, 1, 'RKB', 'R', 'KB');
			InsertClassEvent($TourId, 0, 1, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 0, 1, 'RCW', 'R', 'CW');
			InsertClassEvent($TourId, 0, 1, 'RIG', 'R', 'IG');
			InsertClassEvent($TourId, 0, 1, 'RYG', 'R', 'YG');
			InsertClassEvent($TourId, 0, 1, 'RKG', 'R', 'KG');
			InsertClassEvent($TourId, 0, 1, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 0, 1, 'CCM', 'C', 'CM');
			InsertClassEvent($TourId, 0, 1, 'CIB', 'C', 'IB');
			InsertClassEvent($TourId, 0, 1, 'CYB', 'C', 'YB');
			InsertClassEvent($TourId, 0, 1, 'CKB', 'C', 'KB');
			InsertClassEvent($TourId, 0, 1, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 0, 1, 'CCW', 'C', 'CW');
			InsertClassEvent($TourId, 0, 1, 'CIG', 'C', 'IG');
			InsertClassEvent($TourId, 0, 1, 'CYG', 'C', 'YG');
			InsertClassEvent($TourId, 0, 1, 'CKG', 'C', 'KG');
			
			InsertClassEvent($TourId, 1, 3, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 1, 3, 'RCM', 'R', 'CM');
			InsertClassEvent($TourId, 1, 3, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 1, 3, 'RCW', 'R', 'CW');
			InsertClassEvent($TourId, 1, 1, 'RJX', 'R', 'JW');
			InsertClassEvent($TourId, 2, 1, 'RJX', 'R', 'JM');
			InsertClassEvent($TourId, 1, 1, 'RCX', 'R', 'CW');
			InsertClassEvent($TourId, 2, 1, 'RCX', 'R', 'CM');
			InsertClassEvent($TourId, 1, 3, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 1, 3, 'CCM', 'C', 'CM');
			InsertClassEvent($TourId, 1, 3, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 1, 3, 'CCW', 'C', 'CW');
			InsertClassEvent($TourId, 1, 1, 'CJX', 'C', 'JW');
			InsertClassEvent($TourId, 2, 1, 'CJX', 'C', 'JM');
			InsertClassEvent($TourId, 1, 1, 'CCX', 'C', 'CW');
			InsertClassEvent($TourId, 2, 1, 'CCX', 'C', 'CM');
			break;
	}
}

/*

FIELD DEFINITIONS (Target Tournaments)

*/

require_once(dirname(__FILE__).'/lib-Field.php');

/*

3D DEFINITIONS (Target Tournaments)

*/

require_once(dirname(__FILE__).'/lib-3D.php');



?>