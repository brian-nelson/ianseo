<?php

/*

STANDARD THINGS

*/

// these go here as it is a "global" definition, used or not
$tourCollation = 'danish';
if(empty($SubRule)) $SubRule='1';

$tourDetIocCode = 'NOR_s';
if(($SubRule==3 and $TourType==6) or ($SubRule==5 and $TourType==3) or ($SubRule==9 and $TourType==9) or ($SubRule==7 and $TourType==11) or ($SubRule==9 and $TourType==17)) {
	$tourDetIocCode = 'NOR';
}

function CreateStandardDivisions($TourId, $Type=1, $SubRule=0) {
	$Champs=(($SubRule==3 and $Type==6)
			or ($SubRule==5 and $Type==3)
			or ($SubRule==9 and $Type==9)
			or ($SubRule==7 and $Type==11)
			or ($SubRule==9 and $Type==17)
			);
	$i=1;
	if(!$Champs) CreateDivision($TourId, $i++, 'F', 'Felles');
	CreateDivision($TourId, $i++, 'R', 'Recurve');
	CreateDivision($TourId, $i++, 'C', 'Compound');

	// T is only for non "field" comp...
	if($Type!=9 and $Type!=11 and $Type!=17) CreateDivision($TourId, $i++, 'T', 'Tradisjonell');

	// BU is only 3D and Hunter
	if($Type==11 or $Type==17) CreateDivision($TourId, $i++, 'BU', 'Buejeger');

	// B is in all types
	CreateDivision($TourId, $i++, 'B', 'Barebow');



	if($Type==9 or $Type==11 or $Type==17) {
		// in all "fields" competitions
		CreateDivision($TourId, $i++, 'LB', 'Langbue');
		CreateDivision($TourId, $i++, 'IN', 'Instinktiv');
	}


	// Only in Champs, "non athlete" divisions
	if($Champs) {
		CreateDivision($TourId, $i++, 'D', 'Dommer', 0);
		CreateDivision($TourId, $i++, 'A', 'Arrangør', 0);
		CreateDivision($TourId, $i++, 'V', 'Vip', 0);
	}
}

function CreateStandardClasses($TourId, $SubRule, $Type) {
	$Champs=(($SubRule==3 and $Type==6)
			or ($SubRule==5 and $Type==3)
			or ($SubRule==9 and $Type==9)
			or ($SubRule==7 and $Type==11)
			or ($SubRule==9 and $Type==17)
			);
	$Field=($Type==9 or $Type==11 or $Type==17);
	$i=1;
		// Champs
	if($Champs) {
		if($Field) {
			// 3D champs
			CreateClass($TourId, $i++, 13, 15, -1, 'R', 'R,K,Di,Hi', 'Rekrutt', 1, 'B,IN,LB');
			CreateClass($TourId, $i++, 13, 15, 1, 'RJ', 'RJ,KJ,DJ,D', 'Rekrutt Jenter', 1, 'C,R');
			CreateClass($TourId, $i++, 13, 15, 0, 'RG', 'RG,KG,HJ,H', 'Rekrutt Gutter', 1, 'C,R');
			CreateClass($TourId, $i++, 16, 17, -1, 'K', 'K,Di,Hi', 'Kadett', 1, 'B,IN,LB');
			CreateClass($TourId, $i++, 16, 17, 1, 'KJ', 'KJ,DJ,D', 'Kadett Jenter', 1, 'C,R');
			CreateClass($TourId, $i++, 16, 17, 0, 'KG', 'KG,HJ,H', 'Kadett Gutter', 1, 'C,R');
			CreateClass($TourId, $i++, 18, 20, 1, 'DJ', 'DJ,D', 'Damer Junior', 1, 'C,R');
			CreateClass($TourId, $i++, 18, 20, 0, 'HJ', 'HJ,H', 'Herrer Junior', 1, 'C,R');
			CreateClass($TourId, $i++, 18, 99, 1, 'Di', 'Di', 'Damer', 1, 'B,IN,LB');
			CreateClass($TourId, $i++, 18, 99, 0, 'Hi', 'Hi', 'Herrer', 1, 'B,IN,LB');
			CreateClass($TourId, $i++, 21, 99, 1, 'D', 'D', 'Damer', 1, 'C,R');
			CreateClass($TourId, $i++, 21, 99, 0, 'H', 'H', 'Herrer', 1, 'C,R');
			if($Type==11 or $Type==17) CreateClass($TourId, $i++, 13, 99, -1, 'BU', 'BU', 'Buejeger', 1, 'BU');
		} else {
			// Outdoor and Indoor champs
			CreateClass($TourId, $i++, 13, 15, -1, 'R', 'R,K,Di,Hi', 'Rekrutt', 1, 'B,T');
			CreateClass($TourId, $i++, 13, 15, 1, 'RJ', 'RJ,KJ,DJ,D', 'Rekrutt Jenter', 1, 'C,R');
			CreateClass($TourId, $i++, 13, 15, 0, 'RG', 'RG,KG,HJ,H', 'Rekrutt Gutter', 1, 'C,R');
			CreateClass($TourId, $i++, 16, 17, -1, 'K', 'K,Di,Hi', 'Kadett', 1, 'B,T');
			CreateClass($TourId, $i++, 16, 17, 1, 'KJ', 'KJ,DJ,D', 'Kadett Jenter', 1, 'C,R');
			CreateClass($TourId, $i++, 16, 17, 0, 'KG', 'KG,HJ,H', 'Kadett Gutter', 1, 'C,R');
			CreateClass($TourId, $i++, 18, 20, 1, 'DJ', 'DJ,D', 'Damer Junior', 1, 'C,R');
			CreateClass($TourId, $i++, 18, 20, 0, 'HJ', 'HJ,H', 'Herrer Junior', 1, 'C,R');
			CreateClass($TourId, $i++, 50, 99, 1, 'D5', 'D5,D', 'Damer 50', 1, 'C,R');
			CreateClass($TourId, $i++, 50, 99, 0, 'H5', 'H5,H', 'Herrer 50', 1, 'C,R');
			CreateClass($TourId, $i++, 18, 99, 1, 'Di', 'Di', 'Damer', 1, 'B,T');
			CreateClass($TourId, $i++, 18, 99, 0, 'Hi', 'Hi', 'Herrer', 1, 'B,T');
			CreateClass($TourId, $i++, 21, 49, 1, 'D', 'D', 'Damer', 1, 'C,R');
			CreateClass($TourId, $i++, 21, 49, 0, 'H', 'H', 'Herrer', 1, 'C,R');
		}

		// non competing classes
		CreateClass($TourId, $i++, 1, 99, -1, 'DO', 'DO', 'Dommer', 0, 'D');
		CreateClass($TourId, $i++, 1, 99, -1, 'AR', 'AR', 'Arrangør', 0, 'A');
		CreateClass($TourId, $i++, 1, 99, -1, 'HE', 'HE', 'Helsepersonell', 0, 'A');
		CreateClass($TourId, $i++, 1, 99, -1, 'ST', 'ST', 'Stab', 0, 'A');
		CreateClass($TourId, $i++, 1, 99, -1, 'LA', 'LA', 'Lagleder', 0, 'V');
		CreateClass($TourId, $i++, 1, 99, -1, 'TR', 'TR', 'Trener', 0, 'V');
		CreateClass($TourId, $i++, 1, 99, -1, 'ME', 'ME', 'Media', 0, 'V');
	} else {
		// ordinary competitions
		// Felles, only 1 class
		CreateClass($TourId, $i++,  10, 10, -1, '6', '6', '6', 1, 'F');

		// C&R have all classes
		// T has 1,2,-,4,5
		if($Field) {
			CreateClass($TourId, $i++, 11, 13, -1, '5', '1,2,4,5', '5', 1, 'C,R,B,LB,IN');
			CreateClass($TourId, $i++, 14, 15, -1, '4', '1,2,4', '4', 1, 'C,R,B,LB,IN');
			CreateClass($TourId, $i++, 16, 100, -1, '2', '1,2', '2', 1, 'C,R,B,LB,IN');
			CreateClass($TourId, $i++, 16, 100, -1, '1', '1', '1', 1, 'C,R,B,LB,IN');

			// BueJager has only 1
			if($Type==11 or $Type==17) CreateClass($TourId, $i++, 11, 100, -1, '1u', '1u', '1', 1, 'BU');
		} else {
			CreateClass($TourId, $i++, 11, 13, -1, '5', '1,2,3,4,5', '5', 1, 'C,R,T');
			CreateClass($TourId, $i++, 14, 15, -1, '4', '1,2,3,4', '4', 1, 'C,R,T');
			CreateClass($TourId, $i++, 11, 15, -1, '4b', '1,4b', '4', 1, 'B');
			CreateClass($TourId, $i++, 16, 100, -1, '3', '1,2,3', '3', 1, 'C,R');
			CreateClass($TourId, $i++, 16, 100, -1, '2', '1,2', '2', 1, 'C,R,T');
			CreateClass($TourId, $i++, 16, 100, -1, '1', '1', '1', 1, 'B,C,R,T');
		}

	}
}

function CreateStandardSubClasses($TourId) {
	$i=1;
	CreateSubClass($TourId, $i++, 'R', 'R');
	CreateSubClass($TourId, $i++, 'K', 'K');
	CreateSubClass($TourId, $i++, 'Jr', 'Jr');
	CreateSubClass($TourId, $i++, 'Sr', 'Sr');
	CreateSubClass($TourId, $i++, '50', '50');
}

function CreateStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
	$TargetR=($Outdoor?5:2);
	$TargetC=($Outdoor?9:4);
	$SetC=($Outdoor?0:1);
	$Phase=($Outdoor?16:8);
	$Champs=(($SubRule==3 and $TourType==6)
			or ($SubRule==5 and $TourType==3)
//			or ($SubRule==9 and $Type==9)
//			or ($SubRule==7 and $Type==11)
//			or ($SubRule==9 and $Type==17)
			);
	$TargetSizeC=($Outdoor ? 80 : 40);
	$TargetSizeR=($Outdoor ? 122 : 40);
	$DistanceR=($Outdoor ? 70 : 18);
	$DistanceRW=($Outdoor ? 60 : 18);
	$DistanceT=($Outdoor ? 30 : 18);
	$DistanceB=($Outdoor ? 40 : 18);
	$DistanceC=($Outdoor ? 50 : 18);

	if($Champs) {
		// only Indoor and 70m Round
		$i=1;
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RRJ',  'Recurve Rekrutt Jenter', 1, 240, 255, 0, 0, '', '', $TargetSizeR, ($Outdoor ? 40 : 18));
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CRJ',  'Compound Rekrutt Jenter', 0, 240, 255, 0, 0, '', '', $TargetSizeC, ($Outdoor ? 30 : 18));
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RRG',  'Recurve Rekrutt Gutter', 1, 240, 255, 0, 0, '', '', $TargetSizeR, ($Outdoor ? 40 : 18));
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CRG',  'Compound Rekrutt Gutter', 0, 240, 255, 0, 0, '', '', $TargetSizeC, ($Outdoor ? 30 : 18));
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'TR',  'Tradisjonell Rekrutt', 1, 240, 255, 0, 0, '', '', $TargetSizeR, ($Outdoor ? 25 : 18));
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'BR',  'Barebow Rekrutt', 1, 240, 255, 0, 0, '', '', $TargetSizeR, ($Outdoor ? 25 : 18));
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RKJ',  'Recurve Kadett Jenter', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceRW);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CKJ',  'Compound Kadett Jenter', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RKG',  'Recurve Kadett Gutter', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceRW);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CKG',  'Compound Kadett Gutter', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'TK',  'Tradisjonell Kadett', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceT);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'BK',  'Barebow Kadett', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceB);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RDJ',  'Recurve Damer Junior', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CDJ',  'Compound Damer Junior', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RHJ',  'Recurve Herrer Junior', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CHJ',  'Compound Herrer Junior', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RD5',  'Recurve Damer 50', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceRW);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CD5',  'Compound Damer 50', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RH5',  'Recurve Herrer 50', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceRW);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CH5',  'Compound Herrer 50', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'BD',  'Barebow Damer', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceB);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CD',  'Compound Damer', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'BH',  'Barebow Herrer', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceB);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'TH',  'Tradisjonell Herrer', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceT);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RH',  'Recurve Herrer', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RD',  'Recurve Damer', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'TD',  'Tradisjonell Damer', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceT);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CH',  'Compound Herrer', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);

		//teams
		$Phase=($Outdoor ? 4 : 0);
		if($Outdoor) {
			CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'TR',  'Tradisjonell Rekrutt', 1, 0, 0, 0, 0, '', '', $TargetSizeR, ($Outdoor ? 25 : 18));
			CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetC, 4, 6, 3, 4, 6, 3, 'CR',  'Compound Rekrutt', 0, 0, 0, 0, 0, '', '', $TargetSizeC, ($Outdoor ? 30 : 18));
			CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'RR',  'Recurve Rekrutt',1, 0, 0, 0, 0, '', '', $TargetSizeR, ($Outdoor ? 40 : 18));
			CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'R60M',  'Recurve II',1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRW);
			CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'T',  'Tradisjonell',1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceT);
			CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetC, 4, 6, 3, 4, 6, 3, 'C',  'Compound',0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'R',  'Recurve',1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
		} else {
			CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'BR',  'Barebow Rekrutt', 1, 0, 0, 0, 0, '', '', $TargetSizeR, ($Outdoor ? 25 : 18));
			CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetC, 4, 6, 3, 4, 6, 3, 'CR',  'Compound II', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'RR',  'Recurve III',1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'B',  'Barebow',1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceB);
			CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'T',  'Tradisjonell I',1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceT);
			CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'R',  'Recurve I',1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetC, 4, 6, 3, 4, 6, 3, 'C',  'Compound I',0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
		}
	} elseif($SubRule=='2' or $SubRule=='4') {
		// allways individual finals if subrule==2, but NO teams
		$i=1;
		CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'T5',  'Tradisjonell 5', 1, 240, 255, 0, 0, '', '', $TargetSizeR, ($Outdoor ? 25 : 18));
		CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'C5',  'Compound 5', 0, 240, 255, 0, 0, '', '', $TargetSizeR, ($Outdoor ? 25 : 18));
		CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'R5',  'Recurve 5', 1, 240, 255, 0, 0, '', '', $TargetSizeR, ($Outdoor ? 25 : 18));

		CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'B4',  'Barebow 4', 1, 240, 255, 0, 0, '', '', $TargetSizeR, ($Outdoor ? 25 : 18));
		CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'T4',  'Tradisjonell 4', 1, 240, 255, 0, 0, '', '', $TargetSizeR, ($Outdoor ? 25 : 18));
		CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'C4',  'Compound 4', 0, 240, 255, 0, 0, '', '', $TargetSizeC, ($Outdoor ? 30 : 18));
		CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'R4',  'Recurve 4', 1, 240, 255, 0, 0, '', '', $TargetSizeR, ($Outdoor ? 40 : 18));

		CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'C3',  'Compound 3', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'R3',  'Recurve 3', 1, 240, 255, 0, 0, '', '', $TargetSizeR, ($Outdoor ? 50 : 18));

		CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'T2',  'Tradisjonell 2', 1, 240, 255, 0, 0, '', '', $TargetSizeR, ($Outdoor ? 25 : 18));
		CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'C2',  'Compound 2', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'R2',  'Recurve 2', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceRW);

		CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'B1',  'Barebow 1', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceB);
		CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'T1',  'Tradisjonell 1', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceT);
		CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'C1',  'Compound 1', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'R1',  'Recurve 1', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
	}
}

function InsertStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
	$inds=array();
	$team=array();
	$Champs=(($SubRule==3 and $TourType==6) or ($SubRule==5 and $TourType==3));

	if($Champs) {
		// 70m Round and Indoor Champs
		InsertClassEvent($TourId, 0, 1, 'RRJ', 'R', 'RJ');
		InsertClassEvent($TourId, 0, 1, 'CRJ', 'C', 'RJ');
		InsertClassEvent($TourId, 0, 1, 'RRG', 'R', 'RG');
		InsertClassEvent($TourId, 0, 1, 'CRG', 'C', 'RG');
		InsertClassEvent($TourId, 0, 1, 'TR',  'T', 'R');
		InsertClassEvent($TourId, 0, 1, 'BR',  'B', 'R');
		InsertClassEvent($TourId, 0, 1, 'RKJ', 'R', 'KJ');
		InsertClassEvent($TourId, 0, 1, 'CKJ', 'C', 'KJ');
		InsertClassEvent($TourId, 0, 1, 'RKG', 'R', 'KG');
		InsertClassEvent($TourId, 0, 1, 'CKG', 'C', 'KG');
		InsertClassEvent($TourId, 0, 1, 'TK',  'T', 'K');
		InsertClassEvent($TourId, 0, 1, 'BK',  'B', 'K');
		InsertClassEvent($TourId, 0, 1, 'RDJ', 'R', 'DJ');
		InsertClassEvent($TourId, 0, 1, 'CDJ', 'C', 'DJ');
		InsertClassEvent($TourId, 0, 1, 'RHJ', 'R', 'HJ');
		InsertClassEvent($TourId, 0, 1, 'CHJ', 'C', 'HJ');
		InsertClassEvent($TourId, 0, 1, 'RD5', 'R', 'D5');
		InsertClassEvent($TourId, 0, 1, 'CD5', 'C', 'D5');
		InsertClassEvent($TourId, 0, 1, 'RH5', 'R', 'H5');
		InsertClassEvent($TourId, 0, 1, 'CH5', 'C', 'H5');
		InsertClassEvent($TourId, 0, 1, 'BD',  'B', 'Di');
		InsertClassEvent($TourId, 0, 1, 'CD',  'C', 'D');
		InsertClassEvent($TourId, 0, 1, 'BH',  'B', 'Hi');
		InsertClassEvent($TourId, 0, 1, 'TH',  'T', 'Hi');
		InsertClassEvent($TourId, 0, 1, 'RH',  'R', 'H');
		InsertClassEvent($TourId, 0, 1, 'RD',  'R', 'D');
		InsertClassEvent($TourId, 0, 1, 'TD',  'T', 'Di');
		InsertClassEvent($TourId, 0, 1, 'CH',  'C', 'H');

		$team=array(
			'CR' => array('C' => array('RJ','RG')),
			'RR' => array('R' => array('RJ','RG')),
			'C' => array('C' => array('D','D5','DJ','H','H5','HJ','KG','KJ')),
			);
		if($Outdoor) {
			$team=array(
				'TR' => array('T' => array('R')),
				'CR' => array('C' => array('RJ','RG')),
				'RR' => array('R' => array('RJ','RG')),
				'R60M' => array('R' => array('D5','H5','KG','KJ')),
				'T' => array('T' => array('Hi','Di','K')),
				'C' => array('C' => array('D','D5','DJ','H','H5','HJ','KG','KJ')),
				'R' => array('R' => array('D','DJ','H','HJ')),
				);
		} else {
			$team['BR']= array('B' => array('R'));
			$team['B'] = array('B' => array('K','Di','Hi'));
			$team['R'] = array('R' => array('D','D5','DJ','H','H5','HJ','KG','KJ'));
			$team['T'] = array('T' => array('K','Di','Hi','R'));
		}
	} elseif($SubRule=='2' or $SubRule=='4') {
// 		InsertClassEvent($TourId, 0, 1, $d.$c, $d, $c);
		InsertClassEvent($TourId, 0, 1, 'T5', 'T', '5');
		InsertClassEvent($TourId, 0, 1, 'C5', 'C', '5');
		InsertClassEvent($TourId, 0, 1, 'R5', 'R', '5');
		InsertClassEvent($TourId, 0, 1, 'B4', 'B', '4b');
		InsertClassEvent($TourId, 0, 1, 'T4', 'T', '4');
		InsertClassEvent($TourId, 0, 1, 'C4', 'C', '4');
		InsertClassEvent($TourId, 0, 1, 'R4', 'R', '4');
		InsertClassEvent($TourId, 0, 1, 'C3', 'C', '3');
		InsertClassEvent($TourId, 0, 1, 'R3', 'R', '3');
		InsertClassEvent($TourId, 0, 1, 'T2', 'T', '2');
		InsertClassEvent($TourId, 0, 1, 'C2', 'C', '2');
		InsertClassEvent($TourId, 0, 1, 'R2', 'R', '2');
		InsertClassEvent($TourId, 0, 1, 'B1', 'B', '1');
		InsertClassEvent($TourId, 0, 1, 'T1', 'T', '1');
		InsertClassEvent($TourId, 0, 1, 'C1', 'C', '1');
		InsertClassEvent($TourId, 0, 1, 'R1', 'R', '1');
	}

	foreach($team as $n => $divs) {
		foreach($divs as $d => $cs) {
			foreach($cs as $c) {
				InsertClassEvent($TourId, 1, 3, $n, $d, $c);
			}
		}
	}
}

/*

FIELD ONLY THINGS

*/

function CreateStandardFieldEvents($TourId, $SubRule, $TourType=9) {
	$Target=($TourType==9 ? 6 : ($TourType==11 ? 8 : 11));
	// Individuals
	$i=1;
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'BR',  'Barebow Rekrutt', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'IR',  'Instinktiv Rekrutt', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'LR',  'Langbue Rekrutt', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RRJ',  'Recurve Rekrutt Jenter', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CRJ',  'Compound Rekrutt Jenter', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RRG',  'Recurve Rekrutt Gutter', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CRG',  'Compound Rekrutt Gutter', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'BK',  'Barebow Kadett', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'IK',  'Instinktiv Kadett', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'LK',  'Langbue Kadett', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RKJ',  'Recurve Kadett Jenter', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CKJ',  'Compound Kadett Jenter', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RKG',  'Recurve Kadett Gutter', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CKG',  'Compound Kadett Gutter', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RDJ',  'Recurve Damer Junior', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CDJ',  'Compound Damer Junior', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RHJ',  'Recurve Herrer Junior', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CHJ',  'Compound Herrer Junior', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'BD',  'Barebow Damer', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'ID',  'Instinktiv Damer', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'LD',  'Langbue Damer', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RD',  'Recurve Damer', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CD',  'Compound Damer', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'BH',  'Barebow Herrer', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'IH',  'Instinktiv Herrer', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'LH',  'Langbue Herrer', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RH',  'Recurve Herrer', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CH',  'Compound Herrer', 0, 240, 255);

	// Teams
	$i=1;
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'BY',  'Barebow Yngre');
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'CR',  'Compound Rekrutt');
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'RR',  'Recurve Rekrutt');
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'CK',  'Compound Kadett');
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'RK',  'Recurve Kadett');
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'IN',  'Instinktiv');
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'LB',  'Langbue');
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'B',  'Barebow');
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'R',  'Recurve');
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'C',  'Compound');
}

function InsertStandardFieldEvents($TourId, $SubRule) {
	InsertClassEvent($TourId, 0, 1, 'BR', 'B', 'R');
	InsertClassEvent($TourId, 0, 1, 'IR', 'IN', 'R');
	InsertClassEvent($TourId, 0, 1, 'LR', 'LB', 'R');
	InsertClassEvent($TourId, 0, 1, 'RRJ', 'R', 'RJ');
	InsertClassEvent($TourId, 0, 1, 'CRJ', 'C', 'RJ');
	InsertClassEvent($TourId, 0, 1, 'RRG', 'R', 'RG');
	InsertClassEvent($TourId, 0, 1, 'CRG', 'C', 'RG');
	InsertClassEvent($TourId, 0, 1, 'BK', 'B', 'K');
	InsertClassEvent($TourId, 0, 1, 'IK', 'IN', 'K');
	InsertClassEvent($TourId, 0, 1, 'LK', 'LB', 'K');
	InsertClassEvent($TourId, 0, 1, 'RKJ', 'R', 'KJ');
	InsertClassEvent($TourId, 0, 1, 'CKJ', 'C', 'KJ');
	InsertClassEvent($TourId, 0, 1, 'RKG', 'R', 'KG');
	InsertClassEvent($TourId, 0, 1, 'CKG', 'C', 'KG');
	InsertClassEvent($TourId, 0, 1, 'RDJ', 'R', 'DJ');
	InsertClassEvent($TourId, 0, 1, 'CDJ', 'C', 'DJ');
	InsertClassEvent($TourId, 0, 1, 'RHJ', 'R', 'HJ');
	InsertClassEvent($TourId, 0, 1, 'CHJ', 'C', 'HJ');
	InsertClassEvent($TourId, 0, 1, 'BD', 'B', 'Di');
	InsertClassEvent($TourId, 0, 1, 'ID', 'IN', 'Di');
	InsertClassEvent($TourId, 0, 1, 'LD', 'LB', 'Di');
	InsertClassEvent($TourId, 0, 1, 'RD', 'R', 'D');
	InsertClassEvent($TourId, 0, 1, 'CD', 'C', 'D');
	InsertClassEvent($TourId, 0, 1, 'BH', 'B', 'Hi');
	InsertClassEvent($TourId, 0, 1, 'IH', 'IN', 'Hi');
	InsertClassEvent($TourId, 0, 1, 'LH', 'LB', 'Hi');
	InsertClassEvent($TourId, 0, 1, 'RH', 'R', 'H');
	InsertClassEvent($TourId, 0, 1, 'CH', 'C', 'H');

	$teams=array(
		'BY' => array('B' => array('R','K')),
		'CR' => array('C' => array('RG','RJ')),
		'RR' => array('R' => array('RG','RJ')),
		'CK' => array('C' => array('KG','KJ')),
		'RK' => array('R' => array('KG','KJ')),
		'IN' => array('IN' => array('R','K','Di','Hi')),
		'LB' => array('LB' => array('R','K','Di','Hi')),
		'B' => array('B' => array('Di','Hi')),
		'R' => array('R' => array('D','DJ','H','HJ')),
		'C' => array('C' => array('D','DJ','H','HJ')),
		);

	foreach($teams as $Team => $Divs) {
		foreach($Divs as $Div => $Classes) {
			foreach($Classes as $Class) {
				InsertClassEvent($TourId, 1, 3, $Team, $Div, $Class);
			}
		}
	}
}

function CreateStandard3DEvents($TourId, $SubRule, $TourType=11) {
	$Target=8 ;
	// Individuals
	$i=1;
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'BR',  'Barebow Rekrutt', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'IR',  'Instinktiv Rekrutt', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'LR',  'Langbue Rekrutt', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RRJ',  'Recurve Rekrutt Jenter', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CRJ',  'Compound Rekrutt Jenter', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RRG',  'Recurve Rekrutt Gutter', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CRG',  'Compound Rekrutt Gutter', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'BK',  'Barebow Kadett', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'IK',  'Instinktiv Kadett', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'LK',  'Langbue Kadett', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RKJ',  'Recurve Kadett Jenter', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CKJ',  'Compound Kadett Jenter', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RKG',  'Recurve Kadett Gutter', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CKG',  'Compound Kadett Gutter', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RDJ',  'Recurve Damer Junior', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CDJ',  'Compound Damer Junior', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RHJ',  'Recurve Herrer Junior', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CHJ',  'Compound Herrer Junior', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'BD',  'Barebow Damer', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'ID',  'Instinktiv Damer', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'LD',  'Langbue Damer', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RD',  'Recurve Damer', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CD',  'Compound Damer', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'BU',  'Buejegere', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'BH',  'Barebow Herrer', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'IH',  'Instinktiv Herrer', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'LH',  'Langbue Herrer', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RH',  'Recurve Herrer', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CH',  'Compound Herrer', 0, 240, 255);

	// Teams
	$i=1;
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'BR',  'Barebow Rekrutt');
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'IR',  'Instinktiv Rekrutt');
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'LR',  'Langbue Rekrutt');
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'CR',  'Compound Rekrutt');
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'RR',  'Recurve Rekrutt');
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'IN',  'Instinktiv');
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'LB',  'Langbue');
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'BU',  'Buejeger');
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'B',  'Barebow');
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'R',  'Recurve');
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'C',  'Compound');
}

function InsertStandard3DEvents($TourId, $SubRule) {
	InsertClassEvent($TourId, 0, 1, 'BR', 'B', 'R');
	InsertClassEvent($TourId, 0, 1, 'IR', 'IN', 'R');
	InsertClassEvent($TourId, 0, 1, 'LR', 'LB', 'R');
	InsertClassEvent($TourId, 0, 1, 'RRJ', 'R', 'RJ');
	InsertClassEvent($TourId, 0, 1, 'CRJ', 'C', 'RJ');
	InsertClassEvent($TourId, 0, 1, 'RRG', 'R', 'RG');
	InsertClassEvent($TourId, 0, 1, 'CRG', 'C', 'RG');
	InsertClassEvent($TourId, 0, 1, 'BK', 'B', 'K');
	InsertClassEvent($TourId, 0, 1, 'IK', 'IN', 'K');
	InsertClassEvent($TourId, 0, 1, 'LK', 'LB', 'K');
	InsertClassEvent($TourId, 0, 1, 'RKJ', 'R', 'KJ');
	InsertClassEvent($TourId, 0, 1, 'CKJ', 'C', 'KJ');
	InsertClassEvent($TourId, 0, 1, 'RKG', 'R', 'KG');
	InsertClassEvent($TourId, 0, 1, 'CKG', 'C', 'KG');
	InsertClassEvent($TourId, 0, 1, 'RDJ', 'R', 'DJ');
	InsertClassEvent($TourId, 0, 1, 'CDJ', 'C', 'DJ');
	InsertClassEvent($TourId, 0, 1, 'RHJ', 'R', 'HJ');
	InsertClassEvent($TourId, 0, 1, 'CHJ', 'C', 'HJ');
	InsertClassEvent($TourId, 0, 1, 'BD', 'B', 'Di');
	InsertClassEvent($TourId, 0, 1, 'ID', 'IN', 'Di');
	InsertClassEvent($TourId, 0, 1, 'LD', 'LB', 'Di');
	InsertClassEvent($TourId, 0, 1, 'RD', 'R', 'D');
	InsertClassEvent($TourId, 0, 1, 'CD', 'C', 'D');
	InsertClassEvent($TourId, 0, 1, 'BU', 'BU', 'BU');
	InsertClassEvent($TourId, 0, 1, 'BH', 'B', 'Hi');
	InsertClassEvent($TourId, 0, 1, 'IH', 'IN', 'Hi');
	InsertClassEvent($TourId, 0, 1, 'LH', 'LB', 'Hi');
	InsertClassEvent($TourId, 0, 1, 'RH', 'R', 'H');
	InsertClassEvent($TourId, 0, 1, 'CH', 'C', 'H');

	$teams=array(
		'BR' => array('B' => array('R')),
		'IR' => array('IN' => array('R')),
		'LR' => array('LB' => array('R')),
		'CR' => array('C' => array('RG','RJ')),
		'RR' => array('R' => array('RG','RJ')),
		'IN' => array('IN' => array('K','Di','Hi')),
		'LB' => array('LB' => array('K','Di','Hi')),
		'BU' => array('BU' => array('BU')),
		'B' => array('B' => array('K','Di','Hi')),
		'R' => array('R' => array('KG','KJ','D','DJ','H','HJ')),
		'C' => array('C' => array('KG','KJ','D','DJ','H','HJ')),
		);

	foreach($teams as $Team => $Divs) {
		foreach($Divs as $Div => $Classes) {
			foreach($Classes as $Class) {
				InsertClassEvent($TourId, 1, 3, $Team, $Div, $Class);
			}
		}
	}
}

?>