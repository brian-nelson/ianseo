<?php

/*

STANDARD DEFINITIONS (Target Tournaments)

*/

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'NZ';
if(empty($SubRule)) {
    $SubRule='1';
}

// creation of standard NZ tournament bow types
function CreateStandardDivisions($TourId, $TourType) {
	$i=1;
	CreateDivision($TourId, $i++, 'R', 'Recurve');
	CreateDivision($TourId, $i++, 'C', 'Compound');
	CreateDivision($TourId, $i++, 'B', 'Barebow');
	CreateDivision($TourId, $i++, 'L', 'Longbow');
	if(in_array($TourType,array(5,6,7,8,9,10,11,12,13,35))) {
		CreateDivision($TourId, $i++, 'X', 'Crossbow');
	}
}

// creation of standard NZ tournament competition classes
function CreateStandardClasses($TourId, $SubRule) {
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

// creation of standard NZ matchplay competition events
function CreateStandardEvents($TourId, $SubRule, $Outdoor=true, $allowBB=true) {
	/*
		IANSEO Target Faces:
		1 - Indoor (1-big 10)
		2 - Indoor (6-big 10)
		3 - Indoor (1-small 10)
		4 - Indoor (6-small 10)
		5 - Outdoor (1-X)
		6 - Field Archery
		7 - Hit-Miss
		8 - 3D Standard
		9 - Outdoor (5-X)
	*/
	$TargetR=($Outdoor?5:2);
	$TargetC=($Outdoor?9:4);
    $TargetB=($Outdoor?5:1);
	$TargetIr=($Outdoor?5:1);
	$TargetIc=($Outdoor?9:4);
	$TargetYr=($Outdoor?5:1);
	$TargetYc=($Outdoor?5:3);
	
	$TargetSizeR=($Outdoor ? 122 : 40);
	$TargetSizeC=($Outdoor ? 80 : 40);
    $TargetSizeB=($Outdoor ? 122 : 40);

	$DistanceR=($Outdoor ? 70 : 18);
	$DistanceRcm=($Outdoor ? 60 : 18);
	$DistanceC=($Outdoor ? 50 : 18);
    $DistanceB=($Outdoor ? 50 : 18);
	$DistanceI=($Outdoor ? 45 : 18);
	$DistanceY=($Outdoor ? 35 : 18);
	//$DistanceK=($Outdoor ? 20 : 18);

	$FirstPhase = ($Outdoor ? 8 : 8);
	$TeamFirstPhase = ($Outdoor ? 12 : 8);

	// CreateEvent function requires variables:
	// ($TourId, $Order, $Team, $MixTeam, $FirstPhase, $TargetType, $ElimEnds, $ElimArrows, $ElimSO, $FinEnds, $FinArrows, $FinSO, 
	// $Code, $Description, $SetMode=0, $MatchArrows=0, $AthTarget=0, $ElimRound1=array(), $ElimRound2=array(), $RecCategory='', $WaCategory='', 
	// $tgtSize=0, $shootingDist=0, $parentEvent='', $MultipleTeam=0, $Selected=0, $EvWinnerFinalRank=1, $CreationMode=0, $MultipleTeamNo=0, $PartialTeam=0)
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
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetIr, 5, 3, 1, 5, 3, 1, 'RIB', 'Recurve Intermediate Boys', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetIr, 5, 3, 1, 5, 3, 1, 'RIG', 'Recurve Intermediate Girls', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetYr, 5, 3, 1, 5, 3, 1, 'RYB', 'Recurve Cub Boys', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceY);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetYr, 5, 3, 1, 5, 3, 1, 'RYG', 'Recurve Cub Girls', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceY);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM',  'Compound Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW',  'Compound Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CJM', 'Compound Junior Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CJW', 'Compound Junior Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CCM', 'Compound Cadet Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CCW', 'Compound Cadet Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CMM', 'Compound Master Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CMW', 'Compound Master Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetIc, 5, 3, 1, 5, 3, 1, 'CIB', 'Compound Intermediate Boys', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetIc, 5, 3, 1, 5, 3, 1, 'CIG', 'Compound Intermediate Girls', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetYc, 5, 3, 1, 5, 3, 1, 'CYB', 'Compound Cub Boys', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceY);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetYc, 5, 3, 1, 5, 3, 1, 'CYG', 'Compound Cub Girls', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceY);
			if($allowBB) {
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BM', 'Barebow Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BW', 'Barebow Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BJM', 'Barebow Junior Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BJW', 'Barebow Junior Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BCM', 'Barebow Cadet Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BCW', 'Barebow Cadet Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BMM', 'Barebow Master Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BMW', 'Barebow Master Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
            }
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RM',  'Recurve Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RW',  'Recurve Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RJM', 'Recurve Junior Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RJW', 'Recurve Junior Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RCM', 'Recurve Cadet Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RCW', 'Recurve Cadet Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RMM', 'Recurve Master Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RMW', 'Recurve Master Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CM',  'Compound Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CW',  'Compound Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CJM', 'Compound Junior Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CJW', 'Compound Junior Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CCM', 'Compound Cadet Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CCW', 'Compound Cadet Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CMM', 'Compound Master Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CMW', 'Compound Master Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
            if($allowBB) {
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BM', 'Barebow Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BW', 'Barebow Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                //CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BJM', 'Barebow Junior Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                //CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BJW', 'Barebow Junior Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                //CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BCM', 'Barebow Cadet Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                //CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BCW', 'Barebow Cadet Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                //CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BMM', 'Barebow Master Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                //CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BMW', 'Barebow Master Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                
            }
			if ($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RX',  'Recurve Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RJX', 'Recurve Junior Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RCX', 'Recurve Cadet Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RMX', 'Recurve Master Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CX',  'Compound Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CJX', 'Compound Junior Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CCX', 'Compound Cadet Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CMX', 'Compound Master Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				if($allowBB) {
					CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BX', 'Barebow Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
					//CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BJX', 'Barebow Junior Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
					//CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BCX', 'Barebow Cadet Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
					//CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BMX', 'Barebow Master Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
				}
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
			if($allowBB) {
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BM', 'Barebow Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BW', 'Barebow Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BJM', 'Barebow Junior Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BJW', 'Barebow Junior Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BCM', 'Barebow Cadet Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BCW', 'Barebow Cadet Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BMM', 'Barebow Master Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BMW', 'Barebow Master Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
            }
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RM',  'Recurve Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RW',  'Recurve Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RJM', 'Recurve Junior Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RJW', 'Recurve Junior Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RCM', 'Recurve Cadet Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RCW', 'Recurve Cadet Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RMM', 'Recurve Master Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RMW', 'Recurve Master Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CM',  'Compound Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CW',  'Compound Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CJM', 'Compound Junior Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CJW', 'Compound Junior Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CCM', 'Compound Cadet Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CCW', 'Compound Cadet Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CMM', 'Compound Master Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CMW', 'Compound Master Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
            if($allowBB) {
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BM', 'Barebow Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BW', 'Barebow Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                //CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BJM', 'Barebow Junior Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                //CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BJW', 'Barebow Junior Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                //CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BCM', 'Barebow Cadet Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                //CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BCW', 'Barebow Cadet Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                //CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BMM', 'Barebow Master Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                //CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BMW', 'Barebow Master Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                
            }
			if ($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RX',  'Recurve Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RJX', 'Recurve Junior Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RCX', 'Recurve Cadet Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RMX', 'Recurve Master Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CX',  'Compound Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CJX', 'Compound Junior Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CCX', 'Compound Cadet Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CMX', 'Compound Master Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				if($allowBB) {
					CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BX', 'Barebow Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
					//CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BJX', 'Barebow Junior Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
					//CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BCX', 'Barebow Cadet Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
					//CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BMX', 'Barebow Master Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
				}
			}
            break;
		case '3':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RJM', 'Recurve Junior Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RJW', 'Recurve Junior Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RCM', 'Recurve Cadet Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RCW', 'Recurve Cadet Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetIr, 5, 3, 1, 5, 3, 1, 'RIB', 'Recurve Intermediate Boys', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetIr, 5, 3, 1, 5, 3, 1, 'RIG', 'Recurve Intermediate Girls', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetYr, 5, 3, 1, 5, 3, 1, 'RYB', 'Recurve Cub Boys', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceY);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetYr, 5, 3, 1, 5, 3, 1, 'RYG', 'Recurve Cub Girls', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceY);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CJM', 'Compound Junior Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CJW', 'Compound Junior Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CCM', 'Compound Cadet Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CCW', 'Compound Cadet Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetIc, 5, 3, 1, 5, 3, 1, 'CIB', 'Compound Intermediate Boys', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetIc, 5, 3, 1, 5, 3, 1, 'CIG', 'Compound Intermediate Girls', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetYc, 5, 3, 1, 5, 3, 1, 'CYB', 'Compound Cub Boys', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceY);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetYc, 5, 3, 1, 5, 3, 1, 'CYG', 'Compound Cub Girls', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceY);
			if($allowBB) {
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BJM', 'Barebow Junior Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BJW', 'Barebow Junior Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BCM', 'Barebow Cadet Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BCW', 'Barebow Cadet Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
            }
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetIr, 4, 6, 3, 4, 6, 3, 'RYT',  'Recurve Youth Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceI);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetIc, 4, 6, 3, 4, 6, 3, 'CYT',  'Compound Youth Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceI);
			CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetIr, 4, 4, 2, 4, 4, 2, 'RYX', 'Recurve Youth Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceI);
			CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetIc, 4, 4, 2, 4, 4, 2, 'CYX', 'Compound Youth Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceI);
			break;
	}
}
// ($TourId, $Team, $Number, $Code, $Division, $Class, $SubClass='')
function InsertStandardEvents($TourId, $SubRule) {
	switch($SubRule) {
		case '1':
			InsertClassEvent($TourId, 0, 1, 'RM', 'R', 'SM');
			InsertClassEvent($TourId, 0, 1, 'RW', 'R', 'SW');
			InsertClassEvent($TourId, 0, 1, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 0, 1, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 0, 1, 'RCM', 'R', 'CM');
			InsertClassEvent($TourId, 0, 1, 'RCW', 'R', 'CW');
			InsertClassEvent($TourId, 0, 1, 'RMM', 'R', 'MM');
			InsertClassEvent($TourId, 0, 1, 'RMM', 'R', 'VM');
			InsertClassEvent($TourId, 0, 1, 'RMW', 'R', 'MW');
			InsertClassEvent($TourId, 0, 1, 'RMW', 'R', 'VW');
			InsertClassEvent($TourId, 0, 1, 'RIB', 'R', 'IB');
			InsertClassEvent($TourId, 0, 1, 'RIG', 'R', 'IG');
			InsertClassEvent($TourId, 0, 1, 'RYB', 'R', 'YB');
			InsertClassEvent($TourId, 0, 1, 'RYG', 'R', 'YG');
			InsertClassEvent($TourId, 0, 1, 'RYB', 'R', 'KB');
			InsertClassEvent($TourId, 0, 1, 'RYG', 'R', 'KG');
			InsertClassEvent($TourId, 0, 1, 'CM', 'C', 'SM');
			InsertClassEvent($TourId, 0, 1, 'CW', 'C', 'SW');
			InsertClassEvent($TourId, 0, 1, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 0, 1, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 0, 1, 'CCM', 'C', 'CM');
			InsertClassEvent($TourId, 0, 1, 'CCW', 'C', 'CW');
			InsertClassEvent($TourId, 0, 1, 'CMM', 'C', 'MM');
			InsertClassEvent($TourId, 0, 1, 'CMM', 'C', 'VM');
			InsertClassEvent($TourId, 0, 1, 'CMW', 'C', 'MW');
            InsertClassEvent($TourId, 0, 1, 'CMW', 'C', 'VW');
			InsertClassEvent($TourId, 0, 1, 'CIB', 'C', 'IB');
			InsertClassEvent($TourId, 0, 1, 'CIG', 'C', 'IG');
			InsertClassEvent($TourId, 0, 1, 'CYB', 'C', 'YB');
			InsertClassEvent($TourId, 0, 1, 'CYG', 'C', 'YG');
			InsertClassEvent($TourId, 0, 1, 'CYB', 'C', 'KB');
			InsertClassEvent($TourId, 0, 1, 'CYG', 'C', 'KG');
            InsertClassEvent($TourId, 0, 1, 'BM', 'B', 'SM');
            InsertClassEvent($TourId, 0, 1, 'BW', 'B', 'SW');
            //InsertClassEvent($TourId, 0, 1, 'BJM', 'B', 'JM');
            //InsertClassEvent($TourId, 0, 1, 'BJW', 'B', 'JW');
            //InsertClassEvent($TourId, 0, 1, 'BCM', 'B', 'CM');
            //InsertClassEvent($TourId, 0, 1, 'BCW', 'B', 'CW');
            //InsertClassEvent($TourId, 0, 1, 'BMM', 'B', 'MM');
            //InsertClassEvent($TourId, 0, 1, 'BMW', 'B', 'MW');

			InsertClassEvent($TourId, 1, 3, 'RM',  'R',  'SM');
			InsertClassEvent($TourId, 1, 3, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 1, 3, 'RCM', 'R', 'CM');
			InsertClassEvent($TourId, 1, 3, 'RMM', 'R', 'MM');
			InsertClassEvent($TourId, 1, 3, 'RMM', 'R', 'VM');
			InsertClassEvent($TourId, 1, 3, 'RW',  'R',  'SW');
			InsertClassEvent($TourId, 1, 3, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 1, 3, 'RCW', 'R', 'CW');
			InsertClassEvent($TourId, 1, 3, 'RMW', 'R', 'MW');
			InsertClassEvent($TourId, 1, 3, 'RMW', 'R', 'VW');
			InsertClassEvent($TourId, 1, 1, 'RX',  'R',  'SW');
			InsertClassEvent($TourId, 2, 1, 'RX',  'R',  'SM');
			InsertClassEvent($TourId, 1, 1, 'RJX', 'R', 'JW');
			InsertClassEvent($TourId, 2, 1, 'RJX', 'R', 'JM');
			InsertClassEvent($TourId, 1, 1, 'RCX', 'R', 'CW');
			InsertClassEvent($TourId, 2, 1, 'RCX', 'R', 'CM');
			InsertClassEvent($TourId, 1, 1, 'RMX', 'R', 'MW');
			InsertClassEvent($TourId, 2, 1, 'RMX', 'R', 'MM');
			InsertClassEvent($TourId, 1, 3, 'CM',  'C',  'SM');
			InsertClassEvent($TourId, 1, 3, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 1, 3, 'CCM', 'C', 'CM');
			InsertClassEvent($TourId, 1, 3, 'CMM', 'C', 'MM');
			InsertClassEvent($TourId, 1, 3, 'CMM', 'C', 'VM');
			InsertClassEvent($TourId, 1, 3, 'CW',  'C',  'SW');
			InsertClassEvent($TourId, 1, 3, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 1, 3, 'CCW', 'C', 'CW');
			InsertClassEvent($TourId, 1, 3, 'CMW', 'C', 'MW');
			InsertClassEvent($TourId, 1, 3, 'CMW', 'C', 'VW');
			InsertClassEvent($TourId, 1, 1, 'CX',  'C',  'SW');
			InsertClassEvent($TourId, 2, 1, 'CX',  'C',  'SM');
			InsertClassEvent($TourId, 1, 1, 'CJX', 'C', 'JW');
			InsertClassEvent($TourId, 2, 1, 'CJX', 'C', 'JM');
			InsertClassEvent($TourId, 1, 1, 'CCX', 'C', 'CW');
			InsertClassEvent($TourId, 2, 1, 'CCX', 'C', 'CM');
			InsertClassEvent($TourId, 1, 1, 'CMX', 'C', 'MW');
			InsertClassEvent($TourId, 1, 1, 'CMX', 'C', 'VW');
			InsertClassEvent($TourId, 2, 1, 'CMX', 'C', 'MM');
            InsertClassEvent($TourId, 2, 1, 'CMX', 'C', 'VM');
            InsertClassEvent($TourId, 1, 3, 'BM',  'B',  'SM');
            InsertClassEvent($TourId, 1, 3, 'BW',  'B',  'SW');
            //InsertClassEvent($TourId, 1, 3, 'BMM',  'B',  'MM');
            //InsertClassEvent($TourId, 1, 3, 'BJM', 'B', 'JM');
            //InsertClassEvent($TourId, 1, 3, 'BCM', 'B', 'CM');
            //InsertClassEvent($TourId, 1, 3, 'BMW',  'B',  'MW');
            //InsertClassEvent($TourId, 1, 3, 'BJW', 'B', 'JW');
            //InsertClassEvent($TourId, 1, 3, 'BCW', 'B', 'CW');
            InsertClassEvent($TourId, 1, 1, 'BX',  'B',  'SW');
            InsertClassEvent($TourId, 2, 1, 'BX',  'B',  'SM');
            //InsertClassEvent($TourId, 1, 1, 'BMX',  'B',  'MW');
            //InsertClassEvent($TourId, 2, 1, 'BMX',  'B',  'MM');
            //InsertClassEvent($TourId, 1, 1, 'BJX', 'B', 'JW');
            //InsertClassEvent($TourId, 2, 1, 'BJX', 'B', 'JM');
            //InsertClassEvent($TourId, 1, 1, 'BCX', 'B', 'CW');
            //InsertClassEvent($TourId, 2, 1, 'BCX', 'B', 'CM');
			break;
		case '2':
			InsertClassEvent($TourId, 0, 1, 'RM', 'R', 'SM');
			InsertClassEvent($TourId, 0, 1, 'RW', 'R', 'SW');
			InsertClassEvent($TourId, 0, 1, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 0, 1, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 0, 1, 'RCM', 'R', 'CM');
			InsertClassEvent($TourId, 0, 1, 'RCW', 'R', 'CW');
			InsertClassEvent($TourId, 0, 1, 'RMM', 'R', 'MM');
			InsertClassEvent($TourId, 0, 1, 'RMM', 'R', 'VM');
			InsertClassEvent($TourId, 0, 1, 'RMW', 'R', 'MW');
			InsertClassEvent($TourId, 0, 1, 'RMW', 'R', 'VW');
			InsertClassEvent($TourId, 0, 1, 'CM', 'C', 'SM');
			InsertClassEvent($TourId, 0, 1, 'CW', 'C', 'SW');
			InsertClassEvent($TourId, 0, 1, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 0, 1, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 0, 1, 'CCM', 'C', 'CM');
			InsertClassEvent($TourId, 0, 1, 'CCW', 'C', 'CW');
			InsertClassEvent($TourId, 0, 1, 'CMM', 'C', 'MM');
			InsertClassEvent($TourId, 0, 1, 'CMM', 'C', 'VM');
			InsertClassEvent($TourId, 0, 1, 'CMW', 'C', 'MW');
            InsertClassEvent($TourId, 0, 1, 'CMW', 'C', 'VW');
            InsertClassEvent($TourId, 0, 1, 'BM', 'B', 'SM');
            InsertClassEvent($TourId, 0, 1, 'BW', 'B', 'SW');
            //InsertClassEvent($TourId, 0, 1, 'BJM', 'B', 'JM');
            //InsertClassEvent($TourId, 0, 1, 'BJW', 'B', 'JW');
            //InsertClassEvent($TourId, 0, 1, 'BCM', 'B', 'CM');
            //InsertClassEvent($TourId, 0, 1, 'BCW', 'B', 'CW');
            //InsertClassEvent($TourId, 0, 1, 'BMM', 'B', 'MM');
            //InsertClassEvent($TourId, 0, 1, 'BMW', 'B', 'MW');

			InsertClassEvent($TourId, 1, 3, 'RM',  'R',  'SM');
			InsertClassEvent($TourId, 1, 3, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 1, 3, 'RCM', 'R', 'CM');
			InsertClassEvent($TourId, 1, 3, 'RMM', 'R', 'MM');
			InsertClassEvent($TourId, 1, 3, 'RMM', 'R', 'VM');
			InsertClassEvent($TourId, 1, 3, 'RW',  'R',  'SW');
			InsertClassEvent($TourId, 1, 3, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 1, 3, 'RCW', 'R', 'CW');
			InsertClassEvent($TourId, 1, 3, 'RMW', 'R', 'MW');
			InsertClassEvent($TourId, 1, 3, 'RMW', 'R', 'VW');
			InsertClassEvent($TourId, 1, 1, 'RX',  'R',  'SW');
			InsertClassEvent($TourId, 2, 1, 'RX',  'R',  'SM');
			InsertClassEvent($TourId, 1, 1, 'RJX', 'R', 'JW');
			InsertClassEvent($TourId, 2, 1, 'RJX', 'R', 'JM');
			InsertClassEvent($TourId, 1, 1, 'RCX', 'R', 'CW');
			InsertClassEvent($TourId, 2, 1, 'RCX', 'R', 'CM');
			InsertClassEvent($TourId, 1, 1, 'RMX', 'R', 'MW');
			InsertClassEvent($TourId, 2, 1, 'RMX', 'R', 'MM');
			InsertClassEvent($TourId, 1, 3, 'CM',  'C',  'SM');
			InsertClassEvent($TourId, 1, 3, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 1, 3, 'CCM', 'C', 'CM');
			InsertClassEvent($TourId, 1, 3, 'CMM', 'C', 'MM');
			InsertClassEvent($TourId, 1, 3, 'CMM', 'C', 'VM');
			InsertClassEvent($TourId, 1, 3, 'CW',  'C',  'SW');
			InsertClassEvent($TourId, 1, 3, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 1, 3, 'CCW', 'C', 'CW');
			InsertClassEvent($TourId, 1, 3, 'CMW', 'C', 'MW');
			InsertClassEvent($TourId, 1, 3, 'CMW', 'C', 'VW');
			InsertClassEvent($TourId, 1, 1, 'CX',  'C',  'SW');
			InsertClassEvent($TourId, 2, 1, 'CX',  'C',  'SM');
			InsertClassEvent($TourId, 1, 1, 'CJX', 'C', 'JW');
			InsertClassEvent($TourId, 2, 1, 'CJX', 'C', 'JM');
			InsertClassEvent($TourId, 1, 1, 'CCX', 'C', 'CW');
			InsertClassEvent($TourId, 2, 1, 'CCX', 'C', 'CM');
			InsertClassEvent($TourId, 1, 1, 'CMX', 'C', 'MW');
			InsertClassEvent($TourId, 1, 1, 'CMX', 'C', 'VW');
			InsertClassEvent($TourId, 2, 1, 'CMX', 'C', 'MM');
            InsertClassEvent($TourId, 2, 1, 'CMX', 'C', 'VM');
            InsertClassEvent($TourId, 1, 3, 'BM',  'B',  'SM');
            InsertClassEvent($TourId, 1, 3, 'BW',  'B',  'SW');
            //InsertClassEvent($TourId, 1, 3, 'BMM',  'B',  'MM');
            //InsertClassEvent($TourId, 1, 3, 'BJM', 'B', 'JM');
            //InsertClassEvent($TourId, 1, 3, 'BCM', 'B', 'CM');
            //InsertClassEvent($TourId, 1, 3, 'BMW',  'B',  'MW');
            //InsertClassEvent($TourId, 1, 3, 'BJW', 'B', 'JW');
            //InsertClassEvent($TourId, 1, 3, 'BCW', 'B', 'CW');
            InsertClassEvent($TourId, 1, 1, 'BX',  'B',  'SW');
            InsertClassEvent($TourId, 2, 1, 'BX',  'B',  'SM');
            //InsertClassEvent($TourId, 1, 1, 'BMX',  'B',  'MW');
            //InsertClassEvent($TourId, 2, 1, 'BMX',  'B',  'MM');
            //InsertClassEvent($TourId, 1, 1, 'BJX', 'B', 'JW');
            //InsertClassEvent($TourId, 2, 1, 'BJX', 'B', 'JM');
            //InsertClassEvent($TourId, 1, 1, 'BCX', 'B', 'CW');
            //InsertClassEvent($TourId, 2, 1, 'BCX', 'B', 'CM');
			break;
		case '3':
			InsertClassEvent($TourId, 0, 1, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 0, 1, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 0, 1, 'RCM', 'R', 'CM');
			InsertClassEvent($TourId, 0, 1, 'RCW', 'R', 'CW');
			InsertClassEvent($TourId, 0, 1, 'RIB', 'R', 'IB');
			InsertClassEvent($TourId, 0, 1, 'RIG', 'R', 'IG');
			InsertClassEvent($TourId, 0, 1, 'RYB', 'R', 'YB');
			InsertClassEvent($TourId, 0, 1, 'RYG', 'R', 'YG');
			InsertClassEvent($TourId, 0, 1, 'RYB', 'R', 'KB');
			InsertClassEvent($TourId, 0, 1, 'RYG', 'R', 'KG');
			InsertClassEvent($TourId, 0, 1, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 0, 1, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 0, 1, 'CCM', 'C', 'CM');
			InsertClassEvent($TourId, 0, 1, 'CCW', 'C', 'CW');
			InsertClassEvent($TourId, 0, 1, 'CIB', 'C', 'IB');
			InsertClassEvent($TourId, 0, 1, 'CIG', 'C', 'IG');
			InsertClassEvent($TourId, 0, 1, 'CYB', 'C', 'YB');
			InsertClassEvent($TourId, 0, 1, 'CYG', 'C', 'YG');
			InsertClassEvent($TourId, 0, 1, 'CYB', 'C', 'KB');
			InsertClassEvent($TourId, 0, 1, 'CYG', 'C', 'KG');

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
			InsertClassEvent($TourId, 1, 3, 'RYT',  'R',  'IB');
			InsertClassEvent($TourId, 1, 3, 'RYT',  'R',  'IG');
			InsertClassEvent($TourId, 1, 3, 'RYT',  'R',  'YB');
			InsertClassEvent($TourId, 1, 3, 'RYT',  'R',  'YG');
			InsertClassEvent($TourId, 1, 3, 'RYT',  'R',  'KB');
			InsertClassEvent($TourId, 1, 3, 'RYT',  'R',  'KG');
			InsertClassEvent($TourId, 1, 3, 'RYT',  'B',  'IB');
			InsertClassEvent($TourId, 1, 3, 'RYT',  'B',  'IG');
			InsertClassEvent($TourId, 1, 3, 'RYT',  'B',  'YB');
			InsertClassEvent($TourId, 1, 3, 'RYT',  'B',  'YG');
			InsertClassEvent($TourId, 1, 3, 'RYT',  'B',  'KB');
			InsertClassEvent($TourId, 1, 3, 'RYT',  'B',  'KG');
			InsertClassEvent($TourId, 1, 3, 'CYT',  'C',  'IB');
			InsertClassEvent($TourId, 1, 3, 'CYT',  'C',  'IG');
			InsertClassEvent($TourId, 1, 3, 'CYT',  'C',  'YB');
			InsertClassEvent($TourId, 1, 3, 'CYT',  'C',  'YG');
			InsertClassEvent($TourId, 1, 3, 'CYT',  'C',  'KB');
			InsertClassEvent($TourId, 1, 3, 'CYT',  'C',  'KG');
			InsertClassEvent($TourId, 1, 1, 'RYX',  'R',  'IB');
			InsertClassEvent($TourId, 2, 1, 'RYX',  'R',  'IG');
			InsertClassEvent($TourId, 1, 1, 'RYX',  'R',  'YB');
			InsertClassEvent($TourId, 2, 1, 'RYX',  'R',  'YG');
			InsertClassEvent($TourId, 1, 1, 'RYX',  'R',  'KB');
			InsertClassEvent($TourId, 2, 1, 'RYX',  'R',  'KG');
			InsertClassEvent($TourId, 1, 1, 'RYX',  'B',  'IB');
			InsertClassEvent($TourId, 2, 1, 'RYX',  'B',  'IG');
			InsertClassEvent($TourId, 1, 1, 'RYX',  'B',  'YB');
			InsertClassEvent($TourId, 2, 1, 'RYX',  'B',  'YG');
			InsertClassEvent($TourId, 1, 1, 'RYX',  'B',  'KB');
			InsertClassEvent($TourId, 2, 1, 'RYX',  'B',  'KG');
			InsertClassEvent($TourId, 1, 1, 'CYX',  'C',  'IB');
			InsertClassEvent($TourId, 2, 1, 'CYX',  'C',  'IG');
			InsertClassEvent($TourId, 1, 1, 'CYX',  'C',  'YB');
			InsertClassEvent($TourId, 2, 1, 'CYX',  'C',  'YG');
			InsertClassEvent($TourId, 1, 1, 'CYX',  'C',  'KB');
			InsertClassEvent($TourId, 2, 1, 'CYX',  'C',  'KG');
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