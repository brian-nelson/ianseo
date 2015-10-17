<?php
/*
1 	Type_FITA

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (1)

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType);

// default SubClasses
CreateStandardSubClasses($TourId);

// default Classes
CreateStandardClasses($TourId, $SubRule, '', $TourType);

// default Distances
switch($TourType) {
	case 1:
	case 4:
		switch($SubRule) {
			case '1':
				CreateDistance($TourId, $TourType, 'VI%', '60cm face', '80cm face', '80cm face', '122cm face');
				CreateDistance($TourId, $TourType, 'C1%',  '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLSM', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLJM', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'COSM', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'COJM', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLMM', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLAM', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLSF', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLJF', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'COMM', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'COAM', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'COSF', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'COJF', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLMF', '60 m', '50 m', '40 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLAF', '60 m', '50 m', '40 m', '30 m');
				CreateDistance($TourId, $TourType, 'COMF', '60 m', '50 m', '40 m', '30 m');
				CreateDistance($TourId, $TourType, 'COAF', '60 m', '50 m', '40 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLRM', '50 m', '40 m', '30 m', '20 m');
				CreateDistance($TourId, $TourType, 'OLRF', '50 m', '40 m', '30 m', '20 m');
				CreateDistance($TourId, $TourType, 'CORM', '50 m', '40 m', '30 m', '20 m');
				CreateDistance($TourId, $TourType, 'CORF', '50 m', '40 m', '30 m', '20 m');
				CreateDistance($TourId, $TourType, 'OLGM', '30 m', '25 m', '20 m', '15 m');
				CreateDistance($TourId, $TourType, 'OLGF', '30 m', '25 m', '20 m', '15 m');
				CreateDistance($TourId, $TourType, 'COGM', '30 m', '25 m', '20 m', '15 m');
				CreateDistance($TourId, $TourType, 'COGF', '30 m', '25 m', '20 m', '15 m');
				break;
			case '2':
				CreateDistance($TourId, $TourType, '%M', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '%F', '70 m', '60 m', '50 m', '30 m');
				break;
			case '3':
				CreateDistance($TourId, $TourType, '__JM', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '__AM', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '__JF', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '__AF', '60 m', '50 m', '40 m', '30 m');
				CreateDistance($TourId, $TourType, '__R_', '50 m', '40 m', '30 m', '20 m');
				CreateDistance($TourId, $TourType, '__G_', '30 m', '25 m', '20 m', '15 m');
				break;
			case '4':
				CreateDistance($TourId, $TourType, '%SM', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '%MM', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '%SF', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '%MF', '60 m', '50 m', '40 m', '30 m');
				break;
		}
		break;
	case 18:
		CreateDistance($TourId, $TourType, 'C%', '50m-1', '50m-2', '-', '-');
		switch($SubRule) {
			case '1':
				CreateDistance($TourId, $TourType, 'VI%', '60cm face', '80cm face', '80cm face', '122cm face');
				CreateDistance($TourId, $TourType, 'OLSM', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLJM', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLMM', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLAM', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLSF', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLJF', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLMF', '60 m', '50 m', '40 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLAF', '60 m', '50 m', '40 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLRM', '50 m', '40 m', '30 m', '20 m');
				CreateDistance($TourId, $TourType, 'OLRF', '50 m', '40 m', '30 m', '20 m');
				CreateDistance($TourId, $TourType, 'OLGM', '30 m', '25 m', '20 m', '15 m');
				CreateDistance($TourId, $TourType, 'OLGF', '30 m', '25 m', '20 m', '15 m');
				break;
			case '2':
				CreateDistance($TourId, $TourType, 'OL%M', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OL%F', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'VI%', '60cm face', '80cm face', '80cm face', '122cm face');
				break;
			case '3':
				CreateDistance($TourId, $TourType, 'OLJM', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLAM', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLJF', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLAF', '60 m', '50 m', '40 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLR_', '50 m', '40 m', '30 m', '20 m');
				CreateDistance($TourId, $TourType, 'OLG_', '30 m', '25 m', '20 m', '15 m');
				CreateDistance($TourId, $TourType, 'VI%', '60cm face', '80cm face', '80cm face', '122cm face');
				break;
			case '4':
				CreateDistance($TourId, $TourType, 'OLSM', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLMM', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLSF', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLMF', '60 m', '50 m', '40 m', '30 m');
				CreateDistance($TourId, $TourType, 'VI%', '60cm face', '80cm face', '80cm face', '122cm face');
				break;
		}
		break;
	case 2:
		switch($SubRule) {
			case '1':
				CreateDistance($TourId, $TourType, 'VI%', '60cm face', '80cm face', '80cm face', '122cm face', '60cm face', '80cm face', '80cm face', '122cm face');
				CreateDistance($TourId, $TourType, 'C1%',  '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLSM', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLJM', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'COSM', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'COJM', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLMM', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLAM', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLSF', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLJF', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'COMM', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'COAM', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'COSF', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'COJF', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLMF', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLAF', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
				CreateDistance($TourId, $TourType, 'COMF', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
				CreateDistance($TourId, $TourType, 'COAF', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
				CreateDistance($TourId, $TourType, 'OLRM', '50 m', '40 m', '30 m', '20 m', '50 m', '40 m', '30 m', '20 m');
				CreateDistance($TourId, $TourType, 'OLRF', '50 m', '40 m', '30 m', '20 m', '50 m', '40 m', '30 m', '20 m');
				CreateDistance($TourId, $TourType, 'CORM', '50 m', '40 m', '30 m', '20 m', '50 m', '40 m', '30 m', '20 m');
				CreateDistance($TourId, $TourType, 'CORF', '50 m', '40 m', '30 m', '20 m', '50 m', '40 m', '30 m', '20 m');
				CreateDistance($TourId, $TourType, 'OLGM', '30 m', '25 m', '20 m', '15 m', '30 m', '25 m', '20 m', '15 m');
				CreateDistance($TourId, $TourType, 'OLGF', '30 m', '25 m', '20 m', '15 m', '30 m', '25 m', '20 m', '15 m');
				CreateDistance($TourId, $TourType, 'COGM', '30 m', '25 m', '20 m', '15 m', '30 m', '25 m', '20 m', '15 m');
				CreateDistance($TourId, $TourType, 'COGF', '30 m', '25 m', '20 m', '15 m', '30 m', '25 m', '20 m', '15 m');
				break;
			case '2':
				CreateDistance($TourId, $TourType, '%M', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '%F', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				break;
			case '3':
				CreateDistance($TourId, $TourType, '__JM', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '__AM', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '__JF', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '__AF', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
				CreateDistance($TourId, $TourType, '__R_', '50 m', '40 m', '30 m', '20 m', '50 m', '40 m', '30 m', '20 m');
				CreateDistance($TourId, $TourType, '__G_', '30 m', '25 m', '20 m', '15 m', '30 m', '25 m', '20 m', '15 m');
				break;
			case '4':
				CreateDistance($TourId, $TourType, '%SM', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '%MM', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '%SF', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
				CreateDistance($TourId, $TourType, '%MF', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
				break;
		}
		break;
	case 3:
		switch($SubRule) {
			case '1':
				CreateDistance($TourId, $TourType, 'OLS_', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'OLJ_', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'OLA_', '60m-1', '60m-2');
				CreateDistance($TourId, $TourType, 'OLM_', '60m-1', '60m-2');
				CreateDistance($TourId, $TourType, 'OLR_', '40m-1', '40m-2');
				CreateDistance($TourId, $TourType, 'OLG_', '25m-1', '25m-2');
				CreateDistance($TourId, $TourType, 'CO%', '50m-1', '50m-2');
				CreateDistance($TourId, $TourType, 'VI%', '30m-1', '30m-2');
				break;
			case '2':
				CreateDistance($TourId, $TourType, 'OL%', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'CO%', '50m-1', '50m-2');
				CreateDistance($TourId, $TourType, 'VI%', '30m-1', '30m-2');
				break;
			case '3':
				CreateDistance($TourId, $TourType, 'OLJ_', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'OLA_', '60m-1', '60m-2');
				CreateDistance($TourId, $TourType, 'OLR_', '40m-1', '40m-2');
				CreateDistance($TourId, $TourType, 'OLG_', '25m-1', '25m-2');
				CreateDistance($TourId, $TourType, 'CO%', '50m-1', '50m-2');
				CreateDistance($TourId, $TourType, 'VI%', '30m-1', '30m-2');
				break;
			case '4':
				CreateDistance($TourId, $TourType, 'OLS_', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'OLM_', '60m-1', '60m-2');
				CreateDistance($TourId, $TourType, 'CO%', '50m-1', '50m-2');
				CreateDistance($TourId, $TourType, 'VI%', '30m-1', '30m-2');
				break;
		}
		break;
	case 5:
		CreateDistance($TourId, $TourType, 'OLG_', '25 m', '20 m', '15 m');
		CreateDistance($TourId, $TourType, 'COG_', '25 m', '20 m', '15 m');
		CreateDistance($TourId, $TourType, 'ANG_', '25 m', '20 m', '15 m');
		CreateDistance($TourId, $TourType, 'OLR_', '40 m', '35 m', '30 m');
		CreateDistance($TourId, $TourType, 'COR_', '40 m', '35 m', '30 m');
		CreateDistance($TourId, $TourType, 'ANR_', '40 m', '35 m', '30 m');
		CreateDistance($TourId, $TourType, 'OLA_', '60 m', '50 m', '40 m');
		CreateDistance($TourId, $TourType, 'COA_', '60 m', '50 m', '40 m');
		CreateDistance($TourId, $TourType, 'ANA_', '60 m', '50 m', '40 m');
		CreateDistance($TourId, $TourType, 'OLJ_', '60 m', '50 m', '40 m');
		CreateDistance($TourId, $TourType, 'COJ_', '60 m', '50 m', '40 m');
		CreateDistance($TourId, $TourType, 'ANJ_', '60 m', '50 m', '40 m');
		CreateDistance($TourId, $TourType, 'OLM_', '60 m', '50 m', '40 m');
		CreateDistance($TourId, $TourType, 'COM_', '60 m', '50 m', '40 m');
		CreateDistance($TourId, $TourType, 'ANM_', '60 m', '50 m', '40 m');
		CreateDistance($TourId, $TourType, 'OLS_', '60 m', '50 m', '40 m');
		CreateDistance($TourId, $TourType, 'COS_', '60 m', '50 m', '40 m');
		CreateDistance($TourId, $TourType, 'ANS_', '60 m', '50 m', '40 m');
		CreateDistance($TourId, $TourType, 'VI%', '30m-1', '30m-2', '30m-3');
		break;
	case 6:
		CreateDistance($TourId, $TourType, '%', '18m-1', '18m-2');
		break;
	case 7:
		CreateDistance($TourId, $TourType, '%', '25m-1', '25m-2');
		break;
	case 8:
		CreateDistance($TourId, $TourType, '%', '25m-1', '25m-2', '18m-1', '18m-2');
		break;
}

if($TourType<5 or $TourType==6 or $TourType==18) {
	// default Events
	CreateStandardEvents($TourId, $TourType, $SubRule, $TourType!=6);

	// Classes in Events
	InsertStandardEvents($TourId, $TourType, $SubRule, $TourType!=6);

	// Finals & TeamFinals
	CreateFinals($TourId);
}

// Default Target
$i=1;
switch($TourType) {
	case 1:
	case 4:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
		CreateTargetFace($TourId, $i++, '~DefaultVI', 'VI%', '1', 5, 60, 5, 80, 5, 80, 5, 122);
		// optional target faces
		CreateTargetFace($TourId, $i++, '~30: 6-X', 'REG-^OL|^CO|^C1', '',  5, 122, 5, 122, 5, 80, 10, 80);
		CreateTargetFace($TourId, $i++, '~50: 5-X/30: 5-X', 'REG-^OL|^CO|^C1', '',  5, 122, 5, 122, 9, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '~50: 5-X/30: 6-X', 'REG-^OL|^CO|^C1', '',  5, 122, 5, 122, 9, 80, 10, 80);
		break;
	case 18:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
		CreateTargetFace($TourId, $i++, '~DefaultCO', 'C%', '1', 9, 80, 9, 80, 0, 0, 0, 0);
		CreateTargetFace($TourId, $i++, '~DefaultVI', 'VI%', '1', 5, 60, 5, 80, 5, 80, 5, 122);
		// optional target faces
		CreateTargetFace($TourId, $i++, '~30: 6-X', 'OL%', '',  5, 122, 5, 122, 5, 80, 10, 80);
		CreateTargetFace($TourId, $i++, '~50: 5-X/30: 5-X', 'OL%', '',  5, 122, 5, 122, 9, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '~50: 5-X/30: 6-X', 'OL%', '',  5, 122, 5, 122, 9, 80, 10, 80);
		break;
	case 2:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
		CreateTargetFace($TourId, $i++, '~DefaultVI', 'VI%', '1', 5, 60, 5, 80, 5, 80, 5, 122, 5, 60, 5, 80, 5, 80, 5, 122);
		// optional target faces
		CreateTargetFace($TourId, $i++, '~30: 6-X', 'REG-^OL|^CO|^C1', '',  5, 122, 5, 122, 5, 80, 10, 80,  5, 122, 5, 122, 5, 80, 10, 80);
		CreateTargetFace($TourId, $i++, '~50: 5-X/30: 5-X', 'REG-^OL|^CO|^C1', '',  5, 122, 5, 122, 9, 80, 9, 80,  5, 122, 5, 122, 9, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '~50: 5-X/30: 6-X', 'REG-^OL|^CO|^C1', '',  5, 122, 5, 122, 9, 80, 10, 80,  5, 122, 5, 122, 9, 80, 10, 80);
		break;
	case 3:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '~DefaultCO', 'REG-^CO', '1',  9, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '~DefaultVI', 'REG-^VI', '1',  5, 80, 5, 80);
		break;
	case 5:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1',  5, 122, 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '~DefaultVI', 'VI%', '1',  5, 80, 5, 80, 5, 80);
		break;
	case 6:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~DefaultCO', 'REG-^CO[^G]', '1', 4, 40, 4, 40);
		CreateTargetFace($TourId, $i++, '~Default G/VI', 'REG-^OLG|^ANG|^VI', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~Default COG', 'REG-^COG', '1', 4, 60, 4, 60);
		// optional target faces
		CreateTargetFace($TourId, $i++, '~6-10', 'REG-^OL[AJMRS]', '',  2, 40, 2, 40);
		break;
	case 7:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~DefaultCO', 'REG-^CO[^G]', '1', 4, 60, 4, 60);
		CreateTargetFace($TourId, $i++, '~Default G/VI', 'REG-^OLG|^VI', '1', 1, 80, 1, 80);
		CreateTargetFace($TourId, $i++, '~Default COG', 'REG-^COG', '1', 4, 80, 4, 80);
		// optional target faces
		CreateTargetFace($TourId, $i++, '~6-10', 'REG-^OL[AJMRS]', '',  2, 60, 2, 60);
		break;
	case 8:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1', 1, 60, 1, 60, 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~DefaultCO', 'REG-^CO[^G]', '1', 4, 60, 4, 60, 4, 40, 4, 40);
		CreateTargetFace($TourId, $i++, '~Default G/VI', 'REG-^OLG|^VI', '1', 1, 80, 1, 80, 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~Default COG', 'REG-^COG', '1', 4, 80, 4, 80, 4, 60, 4, 60);
		// optional target faces
		CreateTargetFace($TourId, $i++, '~6-10', 'REG-^OL[AJMRS]', '',  2, 60, 2, 60,  2, 40, 2, 40);
		break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 16, 4);

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

?>