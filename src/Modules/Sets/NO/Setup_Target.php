<?php
/*

COMMON SETUP FOR TARGET

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType, $SubRule);

// default Classes
CreateStandardClasses($TourId, $SubRule, $TourType);

// default Subclasses
CreateStandardSubClasses($TourId);

// default Distances
switch($TourType) {
	case 1: // FITA
	case 4: // 1/2 FITA
		// only ordinary tournaments
		CreateDistance($TourId, $TourType,   'F6', '25 m', '20 m', '15 m', '10 m');
		CreateDistance($TourId, $TourType,   'R1', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType,   'R2', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType,   'R3', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType,   'R4', '50 m', '40 m', '30 m', '20 m');
		CreateDistance($TourId, $TourType,   'C1', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType,   'C2', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType,   'C3', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType,   'C4', '50 m', '40 m', '30 m', '20 m');
		CreateDistance($TourId, $TourType,   'B1', '50 m', '40 m', '30 m', '20 m');
		CreateDistance($TourId, $TourType,   'B2', '30 m', '25 m', '20 m', '15 m');
		CreateDistance($TourId, $TourType,   'B4', '30 m', '25 m', '20 m', '15 m');
		CreateDistance($TourId, $TourType,  'IN1', '40 m', '30 m', '25 m', '20 m');
		CreateDistance($TourId, $TourType,  'IN4', '30 m', '25 m', '20 m', '15 m');
		CreateDistance($TourId, $TourType,  'LB1', '40 m', '30 m', '25 m', '20 m');
		CreateDistance($TourId, $TourType,  'LB4', '30 m', '25 m', '20 m', '15 m');
		CreateDistance($TourId, $TourType,   '%5', '30 m', '25 m', '20 m', '15 m');
		break;
	case 2: // 2xFITA
		// only ordinary tournaments
		CreateDistance($TourId, $TourType,   'F6', '25 m', '20 m', '15 m', '10 m', '25 m', '20 m', '15 m', '10 m');
		CreateDistance($TourId, $TourType,   'R1', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType,   'R2', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType,   'R3', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType,   'R4', '50 m', '40 m', '30 m', '20 m', '50 m', '40 m', '30 m', '20 m');
		CreateDistance($TourId, $TourType,   'C1', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType,   'C2', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType,   'C3', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType,   'C4', '50 m', '40 m', '30 m', '20 m', '50 m', '40 m', '30 m', '20 m');
		CreateDistance($TourId, $TourType,   'B1', '50 m', '40 m', '30 m', '20 m', '50 m', '40 m', '30 m', '20 m');
		CreateDistance($TourId, $TourType,   'B2', '30 m', '25 m', '20 m', '15 m', '30 m', '25 m', '20 m', '15 m');
		CreateDistance($TourId, $TourType,   'B4', '30 m', '25 m', '20 m', '15 m', '30 m', '25 m', '20 m', '15 m');
		CreateDistance($TourId, $TourType,  'IN1', '40 m', '30 m', '25 m', '20 m', '40 m', '30 m', '25 m', '20 m');
		CreateDistance($TourId, $TourType,  'IN4', '30 m', '25 m', '20 m', '15 m', '30 m', '25 m', '20 m', '15 m');
		CreateDistance($TourId, $TourType,  'LB1', '40 m', '30 m', '25 m', '20 m', '40 m', '30 m', '25 m', '20 m');
		CreateDistance($TourId, $TourType,  'LB4', '30 m', '25 m', '20 m', '15 m', '30 m', '25 m', '20 m', '15 m');
		CreateDistance($TourId, $TourType,   '%5', '30 m', '25 m', '20 m', '15 m', '30 m', '25 m', '20 m', '15 m');
		break;
	case 3: // 72 Round
		switch($SubRule) {
			case 1:
			case 2:
				// standard 70m
				CreateDistance($TourId, $TourType,   'F6', '20 m', '20 m');
				CreateDistance($TourId, $TourType,   'R1', '70 m', '70 m');
				CreateDistance($TourId, $TourType,   'R2', '60 m', '60 m');
				CreateDistance($TourId, $TourType,   'R3', '50 m', '50 m');
				CreateDistance($TourId, $TourType,   'R4', '40 m', '40 m');
				CreateDistance($TourId, $TourType,   '%5', '25 m', '25 m');
				CreateDistance($TourId, $TourType,   'C1', '50 m', '50 m');
				CreateDistance($TourId, $TourType,   'C2', '50 m', '50 m');
				CreateDistance($TourId, $TourType,   'C3', '50 m', '50 m');
				CreateDistance($TourId, $TourType,   'C4', '30 m', '30 m');
				CreateDistance($TourId, $TourType,   'B1', '40 m', '40 m');
				CreateDistance($TourId, $TourType,   'B2', '25 m', '25 m');
				CreateDistance($TourId, $TourType,   'B4', '25 m', '25 m');
				CreateDistance($TourId, $TourType,  'IN1', '30 m', '30 m');
				CreateDistance($TourId, $TourType,  'IN4', '25 m', '25 m');
				CreateDistance($TourId, $TourType,  'LB1', '30 m', '30 m');
				CreateDistance($TourId, $TourType,  'LB4', '25 m', '25 m');
				break;
			case 3:
				// Norges Runden
				CreateDistance($TourId, $TourType,  'F6', '15 m', '15 m');
				CreateDistance($TourId, $TourType,  'R1', '50 m', '50 m');
				CreateDistance($TourId, $TourType,  'R2', '50 m', '50 m');
				CreateDistance($TourId, $TourType,  '%3', '50 m', '50 m');
				CreateDistance($TourId, $TourType,  'R4', '30 m', '30 m');
				CreateDistance($TourId, $TourType,  'R5', '20 m', '20 m');
				CreateDistance($TourId, $TourType,  'C1', '50 m', '50 m');
				CreateDistance($TourId, $TourType,  'C2', '50 m', '50 m');
				CreateDistance($TourId, $TourType,  'C4', '30 m', '30 m');
				CreateDistance($TourId, $TourType,  'C5', '20 m', '20 m');
				CreateDistance($TourId, $TourType, 'IN%', '20 m', '20 m');
				CreateDistance($TourId, $TourType, 'LB%', '20 m', '20 m');
				CreateDistance($TourId, $TourType,  'B%', '30 m', '30 m');
				break;
			case 4:
				// Champs
				CreateDistance($TourId, $TourType, '__R', '25 m', '25 m');
				CreateDistance($TourId, $TourType, 'R_', '70 m', '70 m');
				CreateDistance($TourId, $TourType, 'RDJ','70 m', '70 m');
				CreateDistance($TourId, $TourType, 'RHJ','70 m', '70 m');
				CreateDistance($TourId, $TourType, 'R_5','60 m', '60 m');
				CreateDistance($TourId, $TourType, 'RKJ','60 m', '60 m');
				CreateDistance($TourId, $TourType, 'RKG','60 m', '60 m');
				CreateDistance($TourId, $TourType, 'RR_','40 m', '40 m');
				CreateDistance($TourId, $TourType, 'C_', '50 m', '50 m');
				CreateDistance($TourId, $TourType, 'CDJ','50 m', '50 m');
				CreateDistance($TourId, $TourType, 'CHJ','50 m', '50 m');
				CreateDistance($TourId, $TourType, 'C_5','50 m', '50 m');
				CreateDistance($TourId, $TourType, 'CKJ','50 m', '50 m');
				CreateDistance($TourId, $TourType, 'CKG','50 m', '50 m');
				CreateDistance($TourId, $TourType, 'CR_','30 m', '30 m');
				CreateDistance($TourId, $TourType, 'IN_i', '30 m', '30 m');
				CreateDistance($TourId, $TourType, 'LB_i', '30 m', '30 m');
				CreateDistance($TourId, $TourType, '__K', '30 m', '30 m');
				CreateDistance($TourId, $TourType, 'B_i', '40 m', '40 m');
				CreateDistance($TourId, $TourType, 'BK', '40 m', '40 m');
				CreateDistance($TourId, $TourType, 'BR', '25 m', '25 m');
				break;
		}
		break;
	case 5: // 900 round
		if($SubRule==1) {
			// 900 Round
			CreateDistance($TourId, $TourType, 'R1', '60 m', '50 m', '40 m');
			CreateDistance($TourId, $TourType, 'R2', '60 m', '50 m', '40 m');
			CreateDistance($TourId, $TourType, '%3', '60 m', '50 m', '40 m');
			CreateDistance($TourId, $TourType, 'R4', '40 m', '30 m', '20 m');
			CreateDistance($TourId, $TourType, 'R5', '25 m', '20 m', '15 m');
			CreateDistance($TourId, $TourType, 'C1', '60 m', '50 m', '40 m');
			CreateDistance($TourId, $TourType, 'C2', '60 m', '50 m', '40 m');
			CreateDistance($TourId, $TourType, 'C4', '40 m', '30 m', '20 m');
			CreateDistance($TourId, $TourType, 'C5', '25 m', '20 m', '15 m');
			CreateDistance($TourId, $TourType, 'B%', '40 m', '30 m', '20 m');
			CreateDistance($TourId, $TourType, 'IN%', '25 m', '20 m', '15 m');
			CreateDistance($TourId, $TourType, 'LB%', '25 m', '20 m', '15 m');
			CreateDistance($TourId, $TourType, 'F6', '20 m', '15 m', '10 m');
		} else {
			// Norsk kortrunde
			CreateDistance($TourId, $TourType, 'F6', '20 m', '15 m', '10 m');
			CreateDistance($TourId, $TourType, 'B%', '35 m', '25 m', '20 m');
			CreateDistance($TourId, $TourType, '%3', '50 m', '35 m', '25 m');
			CreateDistance($TourId, $TourType, 'C5', '25 m', '20 m', '15 m');
			CreateDistance($TourId, $TourType, 'R5', '25 m', '20 m', '15 m');
			CreateDistance($TourId, $TourType, 'C4', '35 m', '25 m', '20 m');
			CreateDistance($TourId, $TourType, 'R4', '35 m', '25 m', '20 m');
			CreateDistance($TourId, $TourType, 'C2', '50 m', '35 m', '25 m');
			CreateDistance($TourId, $TourType, 'C1', '50 m', '35 m', '25 m');
			CreateDistance($TourId, $TourType, 'R2', '50 m', '35 m', '25 m');
			CreateDistance($TourId, $TourType, 'R1', '50 m', '35 m', '25 m');
            CreateDistance($TourId, $TourType, 'LB%', '25 m', '20 m', '15 m');
            CreateDistance($TourId, $TourType, 'IN%', '25 m', '20 m', '15 m');
		}
		break;
	case 6:
		if($SubRule<3) {
			// ordinary tournaments
			CreateDistance($TourId, $TourType, 'F6', '12m-1', '12m-2');
			CreateDistance($TourId, $TourType, '%5', '12m-1', '12m-2');
			CreateDistance($TourId, $TourType, '%4', '18m-1', '18m-2');
			CreateDistance($TourId, $TourType, '%3', '18m-1', '18m-2');
			CreateDistance($TourId, $TourType, '%2', '18m-1', '18m-2');
			CreateDistance($TourId, $TourType, '%1', '18m-1', '18m-2');
		} else {
			// Champs
			CreateDistance($TourId, $TourType, '%', '18m-1', '18m-2');
		}
		break;
	case 7:
		// ordinary tournaments
		CreateDistance($TourId, $TourType, 'F6', '16m-1', '16m-2');
		CreateDistance($TourId, $TourType, '%5', '16m-1', '16m-2');
		CreateDistance($TourId, $TourType, '%4', '25m-1', '25m-2');
		CreateDistance($TourId, $TourType, '%3', '25m-1', '25m-2');
		CreateDistance($TourId, $TourType, '%2', '25m-1', '25m-2');
		CreateDistance($TourId, $TourType, '%1', '25m-1', '25m-2');
		break;
	case 8:
		// ordinary tournaments
		CreateDistance($TourId, $TourType, 'F6', '16m-1', '16m-2', '12m-1', '12m-2');
		CreateDistance($TourId, $TourType, '%5', '16m-1', '16m-2', '12m-1', '12m-2');
		CreateDistance($TourId, $TourType, '%4', '25m-1', '25m-2', '18m-1', '18m-2');
		CreateDistance($TourId, $TourType, '%3', '25m-1', '25m-2', '18m-1', '18m-2');
		CreateDistance($TourId, $TourType, '%2', '25m-1', '25m-2', '18m-1', '18m-2');
		CreateDistance($TourId, $TourType, '%1', '25m-1', '25m-2', '18m-1', '18m-2');
		break;
	case 18:
		// only with Finals
		CreateDistance($TourId, $TourType, 'F6', '25 m', '20 m', '25 m', '10 m');
		CreateDistance($TourId, $TourType, 'C5', '25m-1', '25m-2', '-', '-');
		CreateDistance($TourId, $TourType, 'R5', '30 m', '25 m', '20 m', '15 m');
		CreateDistance($TourId, $TourType, 'B1', '50 m', '40 m', '30 m', '20 m');
		CreateDistance($TourId, $TourType, 'B4', '30 m', '25 m', '20 m', '15 m');
		CreateDistance($TourId, $TourType, 'C4', '40m-1', '40m-2', '-', '-');
		CreateDistance($TourId, $TourType, 'R4', '50 m', '40 m', '30 m', '20 m');
		CreateDistance($TourId, $TourType, 'C3', '50m-1', '50m-2', '-', '-');
		CreateDistance($TourId, $TourType, 'R3', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, 'C2', '50m-1', '50m-2', '-', '-');
		CreateDistance($TourId, $TourType, 'R2', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'C1', '50m-1', '50m-2', '-', '-');
		CreateDistance($TourId, $TourType, 'R1', '90 m', '70 m', '50 m', '30 m');
		break;
	case 22:
		if($SubRule<3) {
			// ordinary tournaments
			CreateDistance($TourId, $TourType, 'F6', '12m');
			CreateDistance($TourId, $TourType, '%5', '12m');
			CreateDistance($TourId, $TourType, '%4', '18m');
			CreateDistance($TourId, $TourType, '%3', '18m');
			CreateDistance($TourId, $TourType, '%2', '18m');
			CreateDistance($TourId, $TourType, '%1', '18m');
		} else {
			// Champs
			CreateDistance($TourId, $TourType, '%', '18m');
		}
		break;
}

// rules 1, 2 and 4 do not have finals!
if(in_array($TourType, array(3, 6, 7, 8, 22))) {
	$Champ=false;
	if($SubRule==2 or $Champ=(($TourType==3 and $SubRule==4) or ($TourType==6 and $SubRule==3))) {
		// default Events
		CreateStandardEvents($TourId, $TourType, $SubRule, $tourDetCategory=='1');

		// Classes in Events
		InsertStandardEvents($TourId, $TourType, $SubRule, $tourDetCategory=='1');

		// Finals & TeamFinals
		CreateFinals($TourId);
	}
}

// Default Target
$i=1;
switch($TourType) {
	case 1:
	case 4:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
		// optional target faces
		CreateTargetFace($TourId, $i++, '~30: 6-X', 'REG-R1|R2|C1|C2', '',  5, 122, 5, 122, 5, 80, 10, 80);
		CreateTargetFace($TourId, $i++, '~50: 5-X/30: 5-X', 'REG-R1|R2|C1|C2', '',  5, 122, 5, 122, 9, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '~50: 5-X/30: 6-X', 'REG-R1|R2|C1|C2', '',  5, 122, 5, 122, 9, 80, 10, 80);
		break;
	case 2:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
		// optional target faces
		CreateTargetFace($TourId, $i++, '~30: 6-X', 'REG-R1|R2|C1|C2', '',  5, 122, 5, 122, 5, 80, 10, 80,  5, 122, 5, 122, 5, 80, 10, 80);
		CreateTargetFace($TourId, $i++, '~50: 5-X/30: 5-X', 'REG-R1|R2|C1|C2', '',  5, 122, 5, 122, 9, 80, 9, 80,  5, 122, 5, 122, 9, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '~50: 5-X/30: 6-X', 'REG-R1|R2|C1|C2', '',  5, 122, 5, 122, 9, 80, 10, 80,  5, 122, 5, 122, 9, 80, 10, 80);
		break;
	case 3:
		if($SubRule==3) {
			// Norgesrunde
			CreateTargetFace($TourId, $i++, '~Default', '%', '1', 5, 80, 5, 80);
			CreateTargetFace($TourId, $i++, '~5-X', 'REG-R1|R2|C1|C2', '',  9, 80, 9, 80);
		} elseif($SubRule==4) {
			// Championship
			CreateTargetFace($TourId, $i++, '~Default', '%', '1', 5, 122, 5, 122);
			CreateTargetFace($TourId, $i++, '~5-X', 'REG-^C[DHKR][J5]*', '1',  9, 80, 9, 80);
//			CreateTargetFace($TourId, $i++, '~5-X', 'CKJ', '1',  9, 80, 9, 80);
//			CreateTargetFace($TourId, $i++, '~5-X', 'CKG', '1',  9, 80, 9, 80);
		} else {
			// ordinary 70m
			CreateTargetFace($TourId, $i++, '~Default', '%', '1', 5, 122, 5, 122);
			CreateTargetFace($TourId, $i++, '~5-X', 'REG-^C[124]', '1',  9, 80, 9, 80);
            CreateTargetFace($TourId, $i++, '~1-X', 'C3', '1',  5, 80, 5, 80);
		}
		break;
	case 5:
		if($SubRule==2) {
			// Norsk Kortrunde
			CreateTargetFace($TourId, $i++, '~Default', '%', '1',  5, 80, 5, 80, 5, 80);
//			// optional target faces
			CreateTargetFace($TourId, $i++, '~5-X', 'REG-^C[12]|^R[12]', '',  9, 80, 9, 80, 9, 80);
		} else {
			CreateTargetFace($TourId, $i++, '~Default', '%', '1',  5, 122, 5, 122, 5, 122);
		}
		break;
	case 6:
	case 22:
		if($SubRule==3) {
			// Champs
			CreateTargetFace($TourId, $i++, '~Standard 40', 'REG-(^R[KDH]|^B[KDH])', '1', 1, 40, 1, 40);
			CreateTargetFace($TourId, $i++, '~Standard 40 CO', 'REG-^C[KDH]', '1', 4, 40, 4, 40);
			CreateTargetFace($TourId, $i++, '~Standard 60', 'REG-^IN|^LB|^[^C]R', '1', 1, 60, 1, 60);
			CreateTargetFace($TourId, $i++, '~Standard 60 CO', 'REG-^CR', '1', 4, 60, 4, 60);
//			// optional target faces
			CreateTargetFace($TourId, $i++, 'Vegas', 'REG-^R[KDH]', '',  2, 40, 2, 40);
		} else {
			CreateTargetFace($TourId, $i++, '~Standard 60', '%', '1', 1, 60, 1, 60); // most of the "small" class use big targets!
			CreateTargetFace($TourId, $i++, '~Standard 40', 'REG-^(R2|B1)', '1', 1, 40, 1, 40);
			CreateTargetFace($TourId, $i++, '~Standard C1-C2', 'REG-C[12]', '1', 4, 40, 4, 40);
			CreateTargetFace($TourId, $i++, '~Standard C3-C5', 'REG-C[345]', '1', 4, 60, 4, 60);
			CreateTargetFace($TourId, $i++, '~Standard R1', 'REG-R1', '1',  2, 40, 2, 40);
		}
		break;
	case 7:
		CreateTargetFace($TourId, $i++, '~Standard 80', '%', '1', 1, 80, 1, 80); // most of the "small" class use big targets!
		CreateTargetFace($TourId, $i++, '~Standard 60', 'REG-^(R2|B1)', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~Standard C1-C2', 'REG-C[12]', '1', 4, 60, 4, 60);
		CreateTargetFace($TourId, $i++, '~Standard C3-C5', 'REG-C[345]', '1', 4, 80, 4, 80);
		CreateTargetFace($TourId, $i++, '~Standard R1', 'REG-R1', '1',  2, 60, 2, 60);
		break;
	case 8:
		CreateTargetFace($TourId, $i++, '~Standard 80', '%', '1', 1, 80, 1, 80, 1, 60, 1, 60); // most of the "small" class use big targets!
		CreateTargetFace($TourId, $i++, '~Standard 60', 'REG-^(R2|B1)', '1', 1, 60, 1, 60, 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~Standard C1-C2', 'REG-C[12]', '1', 4, 60, 4, 60, 4, 40, 4, 40);
		CreateTargetFace($TourId, $i++, '~Standard C3-C5', 'REG-C[345]', '1', 4, 80, 4, 80, 4, 60, 4, 60);
		CreateTargetFace($TourId, $i++, '~Standard R1', 'REG-R1', '1',  2, 60, 2, 60,  2, 40, 2, 40);
		break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 32, 4);

// Update Tour details
$tourDetails=array(
	'ToCollation' => $tourCollation,
	'ToTypeName' => $tourDetTypeName,
	'ToNumDist' => $tourDetNumDist,
	'ToNumEnds' => $tourDetNumEnds,
	'ToMaxDistScore' => $tourDetMaxDistScore,
	'ToMaxFinIndScore' => $tourDetMaxFinIndScore,
	'ToMaxFinTeamScore' => $tourDetMaxFinTeamScore,
	'ToCategory' => $tourDetCategory,
	'ToElabTeam' => $tourDetElabTeam,
	'ToElimination' => $tourDetElimination,
	'ToGolds' => $tourDetGolds,
	'ToXNine' => $tourDetXNine,
	'ToGoldsChars' => $tourDetGoldsChars,
	'ToXNineChars' => $tourDetXNineChars,
	'ToDouble' => $tourDetDouble,
	'ToIocCode'	=> $tourDetIocCode,
	);
UpdateTourDetails($TourId, $tourDetails);

