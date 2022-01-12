<?php

/*

STANDARD DEFINITIONS (Target Tournaments)

*/

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'FITA';
if(empty($SubRule)) {
    $SubRule='1';
}

function CreateStandardDivisions($TourId, $Type='FITA') {
	$i=1;
	if($Type!='3D') {
	    CreateDivision($TourId, $i++, 'R', 'Recurve');
    }
	CreateDivision($TourId, $i++, 'C', 'Compound');
    if($Type!='FITA') {
        CreateDivision($TourId, $i++, 'B', 'Barebow');
    }
    if($Type=='3D') {
		CreateDivision($TourId, $i++, 'L', 'Longbow');
		CreateDivision($TourId, $i++, 'T', 'Traditional');
	}
}

function CreateStandardClasses($TourId, $SubRule, $Type='FITA') {
	switch($SubRule) {
		case '1':
			CreateClass($TourId, 1, 21, 49, 0, 'M', 'M', 'Men');
			CreateClass($TourId, 2, 21, 49, 1, 'W', 'W', 'Women');
			CreateClass($TourId, 3, 18, 20, 0, 'U21M', 'U21M,M', 'Under 21 Men');
			CreateClass($TourId, 4, 18, 20, 1, 'U21W', 'U21W,W', 'Under 21 Women');
			CreateClass($TourId, 5,  1, 17, 0, 'U18M', 'U18M,U21M,M', 'Under 18 Men');
			CreateClass($TourId, 6,  1, 17, 1, 'U18W', 'U18W,U21W,W', 'Under 18 Women');
			CreateClass($TourId, 7, 50,100, 0, '50M', '50M,M', '50+ Men');
			CreateClass($TourId, 8, 50,100, 1, '50W', '50W,W', '50+ Women');
			break;
		case '2':
		case '5':
			CreateClass($TourId, 1, 1,100, 0, 'M', 'M', 'Men');
			CreateClass($TourId, 2, 1,100, 1, 'W', 'W', 'Women');
			break;
		case '3':
			CreateClass($TourId, 1, 21,100, 0, 'M', 'M', 'Men');
			CreateClass($TourId, 2, 21,100, 1, 'W', 'W', 'Women');
			CreateClass($TourId, 3, 1, 20, 0, 'U21M', 'U21M,M', 'Under 21 Men');
			CreateClass($TourId, 4, 1, 20, 1, 'U21W', 'U21W,W', 'Under 21 Women');
			break;
		case '4':
			CreateClass($TourId, 1, 18, 20, 0, 'U21M', 'U21M,M', 'Under 21 Men');
			CreateClass($TourId, 2, 18, 20, 1, 'U21W', 'U21W,W', 'Under 21 Women');
			CreateClass($TourId, 3,  1, 17, 0, 'U18M', 'U18M,U21M,M', 'Under 18 Men');
			CreateClass($TourId, 4,  1, 17, 1, 'U18W', 'U18W,U21W,W', 'Under 18 Women');
			break;
	}
}

function CreateStandardEvents($TourId, $SubRule, $Outdoor=true, $allowBB=true) {
	$TargetR=($Outdoor?5:2);
	$TargetC=($Outdoor?9:4);
    $TargetB=($Outdoor?5:1);
	$TargetSizeR=($Outdoor ? 122 : 40);
	$TargetSizeC=($Outdoor ? 80 : 40);
    $TargetSizeB=($Outdoor ? 122 : 40);
	$DistanceR=($Outdoor ? 70 : 18);
	$DistanceRcm=($Outdoor ? 60 : 18);
	$DistanceC=($Outdoor ? 50 : 18);
    $DistanceB=($Outdoor ? 50 : 18);
	$FirstPhase = ($Outdoor ? 48 : 16);
	$TeamFirstPhase = ($Outdoor ? 12 : 8);
	switch($SubRule) {
		case '1':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM',  'Recurve Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RW',  'Recurve Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU21M', 'Recurve Under 21 Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU21W', 'Recurve Under 21 Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU18M', 'Recurve Under 18 Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU18W', 'Recurve Under 18 Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'R50M', 'Recurve 50+ Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'R50W', 'Recurve 50+ Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM',  'Compound Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW',  'Compound Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU21M', 'Compound Under 21 Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU21W', 'Compound Under 21 Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU18M', 'Compound Under 18 Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU18W', 'Compound Under 18 Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'C50M', 'Compound 50+ Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'C50W', 'Compound 50+ Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			if($allowBB) {
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BM', 'Barebow Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BW', 'Barebow Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BU21M', 'Barebow Under 21 Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BU21W', 'Barebow Under 21 Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BU18M', 'Barebow Under 18 Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BU18W', 'Barebow Under 18 Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'B50M', 'Barebow 50+ Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'B50W', 'Barebow 50+ Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
            }
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RM',  'Recurve Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RW',  'Recurve Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RU21M', 'Recurve Under 21 Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RU21W', 'Recurve Under 21 Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RU18M', 'Recurve Under 18 Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RU18W', 'Recurve Under 18 Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'R50M', 'Recurve 50+ Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'R50W', 'Recurve 50+ Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RX',  'Recurve Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RU21X', 'Recurve Under 21 Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RU18X', 'Recurve Under 18 Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'R50X', 'Recurve 50+ Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			}
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CM',  'Compound Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CW',  'Compound Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CU21M', 'Compound Under 21 Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CU21W', 'Compound Under 21 Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CU18M', 'Compound Under 18 Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CU18W', 'Compound Under 18 Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'C50M', 'Compound 50+ Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'C50W', 'Compound 50+ Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CX',  'Compound Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CU21X', 'Compound Under 21 Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CU18X', 'Compound Under 18 Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'C50X', 'Compound 50+ Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			}
            if($allowBB) {
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BM', 'Barebow Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BW', 'Barebow Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BU21M', 'Barebow Under 21 Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BU21W', 'Barebow Under 21 Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BU18M', 'Barebow Under 18 Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BU18W', 'Barebow Under 18 Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'B50M', 'Barebow 50+ Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'B50W', 'Barebow 50+ Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                if ($Outdoor) {
                    CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BX', 'Barebow Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                    CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BU21X', 'Barebow Under 21 Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                    CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BU18X', 'Barebow Under 18 Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                    CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'B50X', 'Barebow 50+ Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                }
            }
            break;
		case '2':
		case '5':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM',  'Recurve Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RW',  'Recurve Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM',  'Compound Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW',  'Compound Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
            if($allowBB) {
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BM', 'Barebow Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BW', 'Barebow Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
            }
            $i=1;
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RM',  'Recurve Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RW',  'Recurve Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RX',  'Recurve Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			}
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CM',  'Compound Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CW',  'Compound Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CX',  'Compound Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			}
            if($allowBB) {
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BM', 'Barebow Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BW', 'Barebow Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                if ($Outdoor) {
                    CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BX', 'Barebow Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                }
            }
        break;
		case '3':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM',  'Recurve Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RW',  'Recurve Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU21M', 'Recurve Under 21 Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU21W', 'Recurve Under 21 Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM',  'Compound Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW',  'Compound Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU21M', 'Compound Under 21 Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU21W', 'Compound Under 21 Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
            if($allowBB) {
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BM', 'Barebow Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BW', 'Barebow Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BU21M', 'Barebow Under 21 Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BU21W', 'Barebow Under 21 Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
            }
            $i=1;
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RM',  'Recurve Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RW',  'Recurve Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RU21M', 'Recurve Under 21 Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RU21W', 'Recurve Under 21 Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RX',  'Recurve Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RU21X', 'Recurve Under 21 Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			}
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CM',  'Compound Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CW',  'Compound Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CU21M', 'Compound Under 21 Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CU21W', 'Compound Under 21 Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CX',  'Compound Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CU21X', 'Compound Under 21 Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			}
            if($allowBB) {
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BM', 'Barebow Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BW', 'Barebow Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BU21M', 'Barebow Under 21 Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BU21W', 'Barebow Under 21 Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                if ($Outdoor) {
                    CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BX', 'Barebow Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                    CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BU21X', 'Barebow Under 21 Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                }
            }
			break;
		case '4':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU21M', 'Recurve Under 21 Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU21W', 'Recurve Under 21 Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU18M', 'Recurve Under 18 Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU18W', 'Recurve Under 18 Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU21M', 'Compound Under 21 Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU21W', 'Compound Under 21 Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU18M', 'Compound Under 18 Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU18W', 'Compound Under 18 Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
            if($allowBB) {
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BU21M', 'Barebow Under 21 Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BU21W', 'Barebow Under 21 Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BU18M', 'Barebow Under 18 Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BU18W', 'Barebow Under 18 Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
            }
            $i=1;
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RU21M', 'Recurve Under 21 Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RU21W', 'Recurve Under 21 Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RU18M', 'Recurve Under 18 Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RU18W', 'Recurve Under 18 Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RU21X', 'Recurve Under 21 Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RU18X', 'Recurve Under 18 Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			}
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CU21M', 'Compound Under 21 Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CU21W', 'Compound Under 21 Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CU18M', 'Compound Under 18 Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CU18W', 'Compound Under 18 Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CU21X', 'Compound Under 21 Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CU18X', 'Compound Under 18 Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			}
            if($allowBB) {
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BU21M', 'Barebow Under 21 Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BU21W', 'Barebow Under 21 Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BU18M', 'Barebow Under 18 Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BU18W', 'Barebow Under 18 Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                if ($Outdoor) {
                    CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BU21X', 'Barebow Under 21 Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                    CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BU18X', 'Barebow Under 18 Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                }
            }
			break;
	}
}

function InsertStandardEvents($TourId, $SubRule) {
	switch($SubRule) {
		case '1':
			InsertClassEvent($TourId, 0, 1, 'RM', 'R', 'M');
			InsertClassEvent($TourId, 0, 1, 'RU21M', 'R', 'U21M');
			InsertClassEvent($TourId, 0, 1, 'RU18M', 'R', 'U18M');
			InsertClassEvent($TourId, 0, 1, 'R50M', 'R', '50M');
			InsertClassEvent($TourId, 0, 1, 'RW', 'R', 'W');
			InsertClassEvent($TourId, 0, 1, 'RU21W', 'R', 'U21W');
			InsertClassEvent($TourId, 0, 1, 'RU18W', 'R', 'U18W');
			InsertClassEvent($TourId, 0, 1, 'R50W', 'R', '50W');
			InsertClassEvent($TourId, 0, 1, 'CM', 'C', 'M');
			InsertClassEvent($TourId, 0, 1, 'CU21M', 'C', 'U21M');
			InsertClassEvent($TourId, 0, 1, 'CU18M', 'C', 'U18M');
			InsertClassEvent($TourId, 0, 1, 'C50M', 'C', '50M');
			InsertClassEvent($TourId, 0, 1, 'CW', 'C', 'W');
			InsertClassEvent($TourId, 0, 1, 'CU21W', 'C', 'U21W');
			InsertClassEvent($TourId, 0, 1, 'CU18W', 'C', 'U18W');
			InsertClassEvent($TourId, 0, 1, 'C50W', 'C', '50W');
            InsertClassEvent($TourId, 0, 1, 'BM', 'B', 'M');
            InsertClassEvent($TourId, 0, 1, 'BU21M', 'B', 'U21M');
            InsertClassEvent($TourId, 0, 1, 'BU18M', 'B', 'U18M');
            InsertClassEvent($TourId, 0, 1, 'B50M', 'B', '50M');
            InsertClassEvent($TourId, 0, 1, 'BW', 'B', 'W');
            InsertClassEvent($TourId, 0, 1, 'BU21W', 'B', 'U21W');
            InsertClassEvent($TourId, 0, 1, 'BU18W', 'B', 'U18W');
            InsertClassEvent($TourId, 0, 1, 'B50W', 'B', '50W');

			InsertClassEvent($TourId, 1, 3, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 1, 3, 'RU21M', 'R', 'U21M');
			InsertClassEvent($TourId, 1, 3, 'RU18M', 'R', 'U18M');
			InsertClassEvent($TourId, 1, 3, 'R50M', 'R', '50M');
			InsertClassEvent($TourId, 1, 3, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 1, 3, 'RU21W', 'R', 'U21W');
			InsertClassEvent($TourId, 1, 3, 'RU18W', 'R', 'U18W');
			InsertClassEvent($TourId, 1, 3, 'R50W', 'R', '50W');
			InsertClassEvent($TourId, 1, 1, 'RX',  'R',  'W');
			InsertClassEvent($TourId, 2, 1, 'RX',  'R',  'M');
			InsertClassEvent($TourId, 1, 1, 'RU21X', 'R', 'U21W');
			InsertClassEvent($TourId, 2, 1, 'RU21X', 'R', 'U21M');
			InsertClassEvent($TourId, 1, 1, 'RU18X', 'R', 'U18W');
			InsertClassEvent($TourId, 2, 1, 'RU18X', 'R', 'U18M');
			InsertClassEvent($TourId, 1, 1, 'R50X', 'R', '50W');
			InsertClassEvent($TourId, 2, 1, 'R50X', 'R', '50M');
			InsertClassEvent($TourId, 1, 3, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 1, 3, 'CU21M', 'C', 'U21M');
			InsertClassEvent($TourId, 1, 3, 'CU18M', 'C', 'U18M');
			InsertClassEvent($TourId, 1, 3, 'C50M', 'C', '50M');
			InsertClassEvent($TourId, 1, 3, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 1, 3, 'CU21W', 'C', 'U21W');
			InsertClassEvent($TourId, 1, 3, 'CU18W', 'C', 'U18W');
			InsertClassEvent($TourId, 1, 3, 'C50W', 'C', '50W');
			InsertClassEvent($TourId, 1, 1, 'CX',  'C',  'W');
			InsertClassEvent($TourId, 2, 1, 'CX',  'C',  'M');
			InsertClassEvent($TourId, 1, 1, 'CU21X', 'C', 'U21W');
			InsertClassEvent($TourId, 2, 1, 'CU21X', 'C', 'U21M');
			InsertClassEvent($TourId, 1, 1, 'CU18X', 'C', 'U18W');
			InsertClassEvent($TourId, 2, 1, 'CU18X', 'C', 'U18M');
			InsertClassEvent($TourId, 1, 1, 'C50X', 'C', '50W');
			InsertClassEvent($TourId, 2, 1, 'C50X', 'C', '50M');
            InsertClassEvent($TourId, 1, 3, 'BM',  'B',  'M');
            InsertClassEvent($TourId, 1, 3, 'B50M',  'B',  '50M');
            InsertClassEvent($TourId, 1, 3, 'BU21M', 'B', 'U21M');
            InsertClassEvent($TourId, 1, 3, 'BU18M', 'B', 'U18M');
            InsertClassEvent($TourId, 1, 3, 'BW',  'B',  'W');
            InsertClassEvent($TourId, 1, 3, 'B50W',  'B',  '50W');
            InsertClassEvent($TourId, 1, 3, 'BU21W', 'B', 'U21W');
            InsertClassEvent($TourId, 1, 3, 'BU18W', 'B', 'U18W');
            InsertClassEvent($TourId, 1, 1, 'BX',  'B',  'W');
            InsertClassEvent($TourId, 2, 1, 'BX',  'B',  'M');
            InsertClassEvent($TourId, 1, 1, 'B50X',  'B',  '50W');
            InsertClassEvent($TourId, 2, 1, 'B50X',  'B',  '50M');
            InsertClassEvent($TourId, 1, 1, 'BU21X', 'B', 'U21W');
            InsertClassEvent($TourId, 2, 1, 'BU21X', 'B', 'U21M');
            InsertClassEvent($TourId, 1, 1, 'BU18X', 'B', 'U18W');
            InsertClassEvent($TourId, 2, 1, 'BU18X', 'B', 'U18M');
			break;
		case '2':
		case '5':
			InsertClassEvent($TourId, 0, 1, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 0, 1, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 0, 1, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 0, 1, 'CW',  'C',  'W');
            InsertClassEvent($TourId, 0, 1, 'BM',  'B',  'M');
            InsertClassEvent($TourId, 0, 1, 'BW',  'B',  'W');

			InsertClassEvent($TourId, 1, 3, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 1, 3, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 1, 1, 'RX',  'R',  'W');
			InsertClassEvent($TourId, 2, 1, 'RX',  'R',  'M');
			InsertClassEvent($TourId, 1, 3, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 1, 3, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 1, 1, 'CX',  'C',  'W');
			InsertClassEvent($TourId, 2, 1, 'CX',  'C',  'M');
            InsertClassEvent($TourId, 1, 3, 'BM',  'B',  'M');
            InsertClassEvent($TourId, 1, 3, 'BW',  'B',  'W');
            InsertClassEvent($TourId, 1, 1, 'BX',  'B',  'W');
            InsertClassEvent($TourId, 2, 1, 'BX',  'B',  'M');
			break;
		case '3':
			InsertClassEvent($TourId, 0, 1, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 0, 1, 'RU21M', 'R', 'U21M');
			InsertClassEvent($TourId, 0, 1, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 0, 1, 'RU21W', 'R', 'U21W');
			InsertClassEvent($TourId, 0, 1, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 0, 1, 'CU21M', 'C', 'U21M');
			InsertClassEvent($TourId, 0, 1, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 0, 1, 'CU21W', 'C', 'U21W');
            InsertClassEvent($TourId, 0, 1, 'BM',  'B',  'M');
            InsertClassEvent($TourId, 0, 1, 'BU21M', 'B', 'U21M');
            InsertClassEvent($TourId, 0, 1, 'BW',  'B',  'W');
            InsertClassEvent($TourId, 0, 1, 'BU21W', 'B', 'U21W');

			InsertClassEvent($TourId, 1, 3, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 1, 3, 'RU21M', 'R', 'U21M');
			InsertClassEvent($TourId, 1, 3, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 1, 3, 'RU21W', 'R', 'U21W');
			InsertClassEvent($TourId, 1, 1, 'RX',  'R',  'W');
			InsertClassEvent($TourId, 2, 1, 'RX',  'R',  'M');
			InsertClassEvent($TourId, 1, 1, 'RU21X', 'R', 'U21W');
			InsertClassEvent($TourId, 2, 1, 'RU21X', 'R', 'U21M');
			InsertClassEvent($TourId, 1, 3, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 1, 3, 'CU21M', 'C', 'U21M');
			InsertClassEvent($TourId, 1, 3, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 1, 3, 'CU21W', 'C', 'U21W');
			InsertClassEvent($TourId, 1, 1, 'CX',  'C',  'W');
			InsertClassEvent($TourId, 2, 1, 'CX',  'C',  'M');
			InsertClassEvent($TourId, 1, 1, 'CU21X', 'C', 'U21W');
			InsertClassEvent($TourId, 2, 1, 'CU21X', 'C', 'U21M');
            InsertClassEvent($TourId, 1, 3, 'BM',  'B',  'M');
            InsertClassEvent($TourId, 1, 3, 'BU21M', 'B', 'U21M');
            InsertClassEvent($TourId, 1, 3, 'BW',  'B',  'W');
            InsertClassEvent($TourId, 1, 3, 'BU21W', 'B', 'U21W');
            InsertClassEvent($TourId, 1, 1, 'BX',  'B',  'W');
            InsertClassEvent($TourId, 2, 1, 'BX',  'B',  'M');
            InsertClassEvent($TourId, 1, 1, 'BU21X', 'B', 'U21W');
            InsertClassEvent($TourId, 2, 1, 'BU21X', 'B', 'U21M');
			break;
		case '4':
			InsertClassEvent($TourId, 0, 1, 'RU21M', 'R', 'U21M');
			InsertClassEvent($TourId, 0, 1, 'RU18M', 'R', 'U18M');
			InsertClassEvent($TourId, 0, 1, 'RU21W', 'R', 'U21W');
			InsertClassEvent($TourId, 0, 1, 'RU18W', 'R', 'U18W');
			InsertClassEvent($TourId, 0, 1, 'CU21M', 'C', 'U21M');
			InsertClassEvent($TourId, 0, 1, 'CU18M', 'C', 'U18M');
			InsertClassEvent($TourId, 0, 1, 'CU21W', 'C', 'U21W');
			InsertClassEvent($TourId, 0, 1, 'CU18W', 'C', 'U18W');
            InsertClassEvent($TourId, 0, 1, 'BU21M', 'B', 'U21M');
            InsertClassEvent($TourId, 0, 1, 'BU18M', 'B', 'U18M');
            InsertClassEvent($TourId, 0, 1, 'BU21W', 'B', 'U21W');
            InsertClassEvent($TourId, 0, 1, 'BU18W', 'B', 'U18W');

			InsertClassEvent($TourId, 1, 3, 'RU21M', 'R', 'U21M');
			InsertClassEvent($TourId, 1, 3, 'RU18M', 'R', 'U18M');
			InsertClassEvent($TourId, 1, 3, 'RU21W', 'R', 'U21W');
			InsertClassEvent($TourId, 1, 3, 'RU18W', 'R', 'U18W');
			InsertClassEvent($TourId, 1, 1, 'RU21X', 'R', 'U21W');
			InsertClassEvent($TourId, 2, 1, 'RU21X', 'R', 'U21M');
			InsertClassEvent($TourId, 1, 1, 'RU18X', 'R', 'U18W');
			InsertClassEvent($TourId, 2, 1, 'RU18X', 'R', 'U18M');
			InsertClassEvent($TourId, 1, 3, 'CU21M', 'C', 'U21M');
			InsertClassEvent($TourId, 1, 3, 'CU18M', 'C', 'U18M');
			InsertClassEvent($TourId, 1, 3, 'CU21W', 'C', 'U21W');
			InsertClassEvent($TourId, 1, 3, 'CU18W', 'C', 'U18W');
			InsertClassEvent($TourId, 1, 1, 'CU21X', 'C', 'U21W');
			InsertClassEvent($TourId, 2, 1, 'CU21X', 'C', 'U21M');
			InsertClassEvent($TourId, 1, 1, 'CU18X', 'C', 'U18W');
			InsertClassEvent($TourId, 2, 1, 'CU18X', 'C', 'U18M');
            InsertClassEvent($TourId, 1, 3, 'BU21M', 'B', 'U21M');
            InsertClassEvent($TourId, 1, 3, 'BU18M', 'B', 'U18M');
            InsertClassEvent($TourId, 1, 3, 'BU21W', 'B', 'U21W');
            InsertClassEvent($TourId, 1, 3, 'BU18W', 'B', 'U18W');
            InsertClassEvent($TourId, 1, 1, 'BU21X', 'B', 'U21W');
            InsertClassEvent($TourId, 2, 1, 'BU21X', 'B', 'U21M');
            InsertClassEvent($TourId, 1, 1, 'BU18X', 'B', 'U18W');
            InsertClassEvent($TourId, 2, 1, 'BU18X', 'B', 'U18M');
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

