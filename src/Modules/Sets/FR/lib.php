<?php

/*

STANDARD DEFINITIONS (Target Tournaments)

*/
require_once(dirname(dirname(__FILE__)).'/lib.php');

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'FRA';
if(empty($SubRule)) $SubRule='1';

function CreateStandardDivisions($TourId, $Type='FR') {
	$i=1;
	if($Type!='3D') CreateDivision($TourId, $i++, 'CL', 'Arc Classique');
	CreateDivision($TourId, $i++, 'CO', 'Arc Poulies');
	if($Type=='FIELD') {
		CreateDivision($TourId, $i++, 'BB', 'Arc Nu');
	} elseif($Type=='3D') {
		CreateDivision($TourId, $i++, 'BB', 'Arc Nu');
		CreateDivision($TourId, $i++, 'AD', 'Arc Droit');
		CreateDivision($TourId, $i++, 'AC', 'Arc Chasse');
		CreateDivision($TourId, $i++, 'TL', 'Traditionnel');
	}
}

function CreateStandardClasses($TourId, $TourType, $SubRule) {
	$SubYouth=array(1,2,3);
	$SubJuniors=array(1,2,3,4,5,6,7,9,10,11);
	$SubAdults=array(1,4,5,6,7,9,10,11);
	$SubFederal=array(8,11);
	$i=1;
	switch($TourType) {
		case '6': // INDOORS 18
		case '7': // INDOORS 25
		case '8': // INDOORS 25+18
			switch($SubRule) {
				case '1':
					// All classes...
					CreateClass($TourId, $i++,  1, 10, 1, 'PF', 'PF,BF', 'Poussin Fille', '1', 'CL', 'PD', 'CW');
					CreateClass($TourId, $i++,  1, 10, 0, 'PH', 'PH,BH', 'Poussin Homme', '1', 'CL', 'PH', 'CM');
					CreateClass($TourId, $i++, 11, 12, 1, 'BF', 'BF,MF', 'Benjamine Fille', '1', 'CL', 'BF', 'CW');
					CreateClass($TourId, $i++, 11, 12, 0, 'BH', 'BH,MH', 'Benjamin Homme', '1', 'CL', 'BH', 'CM');
					CreateClass($TourId, $i++, 13, 14, 1, 'MF', 'MF,CF', 'Minime Fille', '1', 'BB,CL', 'MF', 'CW');
					CreateClass($TourId, $i++, 13, 14, 0, 'MH', 'MH,CH', 'Minime Homme', '1', 'BB,CL', 'MH', 'CM');
					CreateClass($TourId, $i++, 15, 17, 1, 'CF', 'CF,JF,1F', 'Cadette Fille', '1', '', 'CF', 'CW');
					CreateClass($TourId, $i++, 15, 17, 0, 'CH', 'CH,JH,1H', 'Cadet Homme', '1', '', 'CH', 'CM');
					CreateClass($TourId, $i++, 18, 20, 1, 'JF', 'JF,1F', 'Junior Fille', '1', '', 'JF', 'JW');
					CreateClass($TourId, $i++, 18, 20, 0, 'JH', 'JH,1H', 'Junior Homme', '1', '', 'JH', 'JM');
					CreateClass($TourId, $i++, 21, 39, 1, '1F', '1F', 'Senior 1 Femme', '1', '', '1F', 'W');
					CreateClass($TourId, $i++, 21, 39, 0, '1H', '1H', 'Senior 1 Homme', '1', '', '1H', 'M');
					CreateClass($TourId, $i++, 40, 59, 1, '2F', '2F,1F', 'Senior 2 Femme', '1', '', '2F', '');
					CreateClass($TourId, $i++, 40, 59, 0, '2H', '2H,1H', 'Senior 2 Homme', '1', '', '2H', '');
					CreateClass($TourId, $i++, 60,100, 1, '3F', '3F,2F,1F', 'Senior 3 Femme', '1', '', '3F', 'MW');
					CreateClass($TourId, $i++, 60,100, 0, '3H', '3H,2H,1H', 'Senior 3 Homme', '1', '', '3H', 'MM');
					break;
				case '2':
					// Championships Adults...
					CreateClass($TourId, $i++, 18, 20, 1, 'JF', 'JF,1F', 'Junior Fille', '1', '', 'JF', 'JW');
					CreateClass($TourId, $i++, 18, 20, 0, 'JH', 'JH,1H', 'Junior Homme', '1', '', 'JH', 'JM');
					CreateClass($TourId, $i++, 21, 39, 1, 'SF', '1F', 'Senior 1 Femme', '1', '', '1F', 'W');
					CreateClass($TourId, $i++, 21, 39, 0, 'SH', '1H', 'Senior 1 Homme', '1', '', '1H', 'M');
					CreateClass($TourId, $i++, 40, 59, 1, 'VF', '2F,1F', 'Senior 2 Femme', '1', '', '2F', '');
					CreateClass($TourId, $i++, 40, 59, 0, 'VH', '2H,1H', 'Senior 2 Homme', '1', '', '2H', '');
					CreateClass($TourId, $i++, 60,100, 1, 'WF', '3F,2F,1F', 'Senior 3 Femme', '1', '', '3F', 'MW');
					CreateClass($TourId, $i++, 60,100, 0, 'WH', '3H,2H,1H', 'Senior 3 Homme', '1', '', '3H', 'MM');
					break;
				case '3':
					// Championships Youth...
					CreateClass($TourId, $i++, 11, 12, 1, 'BF', 'BF,MF', 'Benjamine Fille', '1', 'CL', 'BF', 'CW');
					CreateClass($TourId, $i++, 11, 12, 0, 'BH', 'BH,MH', 'Benjamin Homme', '1', 'CL', 'BH', 'CM');
					CreateClass($TourId, $i++, 13, 14, 1, 'MF', 'MF,CF', 'Minime Fille', '1', 'BB,CL', 'MF', 'CW');
					CreateClass($TourId, $i++, 13, 14, 0, 'MH', 'MH,CH', 'Minime Homme', '1', 'BB,CL', 'MH', 'CM');
					CreateClass($TourId, $i++, 15, 17, 1, 'CF', 'CF,JF,1F', 'Cadette Fille', '1', '', 'CF', 'CW');
					CreateClass($TourId, $i++, 15, 17, 0, 'CH', 'CH,JH,1H', 'Cadet Homme', '1', '', 'CH', 'CM');
					CreateClass($TourId, $i++, 18, 20, 1, 'JF', 'JF,1F', 'Junior Fille', '1', '', 'JF', 'JW');
					CreateClass($TourId, $i++, 18, 20, 0, 'JH', 'JH,1H', 'Junior Homme', '1', '', 'JH', 'JM');
					break;
			}
			break;
		case '3': // 72 arrows round
			// we create all classes anyway
			if(in_array($SubRule, $SubYouth)) {
				CreateClass($TourId, $i++,  1, 10, 1, 'PF', 'PF,BF', 'Poussin Fille', '1', 'CL', 'PD', '');
				CreateClass($TourId, $i++,  1, 10, 0, 'PH', 'PH,BH', 'Poussin Homme', '1', 'CL', 'PH', '');
				CreateClass($TourId, $i++, 11, 12, 1, 'BF', 'BF,MF', 'Benjamine Fille', '1', 'CL', 'BF', '');
				CreateClass($TourId, $i++, 11, 12, 0, 'BH', 'BH,MH', 'Benjamin Homme', '1', 'CL', 'BH', '');
				CreateClass($TourId, $i++, 13, 14, 1, 'MF', 'MF,CF', 'Minime Fille', '1', 'CL', 'MF', '');
				CreateClass($TourId, $i++, 13, 14, 0, 'MH', 'MH,CH', 'Minime Homme', '1', 'CL', 'MH', '');
			}
			if(in_array($SubRule, $SubJuniors)) {
				CreateClass($TourId, $i++, 15, 17, 1, 'CF', 'CF,JF,1F', 'Cadette Fille', '1', 'CL,CO', 'CF', 'CW');
				CreateClass($TourId, $i++, 15, 17, 0, 'CH', 'CH,JH,1H', 'Cadet Homme', '1', 'CL,CO', 'CH', 'CM');
				CreateClass($TourId, $i++, 18, 20, 1, 'JF', 'JF,1F', 'Junior Fille', '1', 'CL,CO', 'JF', 'JW');
				CreateClass($TourId, $i++, 18, 20, 0, 'JH', 'JH,1H', 'Junior Homme', '1', 'CL,CO', 'JH', 'JM');
			}
			if(in_array($SubRule, $SubAdults)) {
				CreateClass($TourId, $i++, 21, 39, 1, '1F', '1F', 'Senior 1 Femme', '1', 'CL,CO', '1F', 'W');
				CreateClass($TourId, $i++, 21, 39, 0, '1H', '1H', 'Senior 1 Homme', '1', 'CL,CO', '1H', 'M');
				CreateClass($TourId, $i++, 40, 59, 1, '2F', '2F,1F', 'Senior 2 Femme', '1', 'CL,CO', '2F', '');
				CreateClass($TourId, $i++, 40, 59, 0, '2H', '2H,1H', 'Senior 2 Homme', '1', 'CL,CO', '2H', '');
				CreateClass($TourId, $i++, 60,100, 1, '3F', '3F,2F,1F', 'Senior 3 Femme', '1', 'CL,CO', '3F', 'MW');
				CreateClass($TourId, $i++, 60,100, 0, '3H', '3H,2H,1H', 'Senior 3 Homme', '1', 'CL,CO', '3H', 'MM');
			}

			if(in_array($SubRule, $SubFederal)) {
				// Ex Federal joined together with international 72 arrows
				CreateClass($TourId, $i++, 18, 20, 1, 'JW', 'JW,1W', 'Junior Fille', '1', 'CL,CO', '', '');
				CreateClass($TourId, $i++, 18, 20, 0, 'JM', 'JM,1M', 'Junior Homme', '1', 'CL,CO', '', '');
				CreateClass($TourId, $i++, 21, 39, 1, '1W', '1W', 'Senior 1 Femme', '1', 'CL,CO', '', '');
				CreateClass($TourId, $i++, 21, 39, 0, '1M', '1M', 'Senior 1 Homme', '1', 'CL,CO', '', '');
				CreateClass($TourId, $i++, 40, 59, 1, '2W', '2W,1W', 'Senior 2 Femme', '1', 'CL,CO', '', '');
				CreateClass($TourId, $i++, 40, 59, 0, '2M', '2M,1M', 'Senior 2 Homme', '1', 'CL,CO', '', '');
				CreateClass($TourId, $i++, 60,100, 1, '3W', '3W,2W,1W', 'Senior 3 Femme', '1', 'CL,CO', '', '');
				CreateClass($TourId, $i++, 60,100, 0, '3M', '3M,2M,1M', 'Senior 3 Homme', '1', 'CL,CO', '', '');
			}
			break;
	}
}

function CreateStandardEvents($TourId, $TourType, $SubRule, $Outdoor=false) {
	global $useOldRules;
	//$TargetR=($Outdoor?5:2);
	//$TargetC=($Outdoor?9:4);
	//$TargetSizeR=($Outdoor ? 122 : 40);
	//$TargetSizeC=($Outdoor ? 80 : 40);
	//$DistanceR=($Outdoor ? 70 : 18);
	//$DistanceRcm=($Outdoor ? 60 : 18);
	//$DistanceC=($Outdoor ? 50 : 18);

	$i=1;
	switch($TourType) {
		case 6: // INDOOR 18m
			$TargetR=2;
			$TargetC=4;
			$Distance=18;
			$TargetSize4=40;
			$TargetSize6=60;

			// NEVER as Team
			switch($SubRule) {
				case '2': // Championships Adults
					CreateEvent($TourId, $i++, 0, 0,  8, $TargetR, 5, 3, 1, 5, 3, 1, '1FCL', 'Classique Senior 1 Femme', 1, 240, 240, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, '1HCL', 'Classique Senior 1 Homme', 1, 240, 240, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  2, $TargetR, 5, 3, 1, 5, 3, 1, '2FCL', 'Classique Senior 2 Femme', 1, 240, 240, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  8, $TargetR, 5, 3, 1, 5, 3, 1, '2HCL', 'Classique Senior 2 Homme', 1, 240, 240, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  4, $TargetR, 5, 3, 1, 5, 3, 1, '3FCL', 'Classique Senior 3 Femme', 1, 240, 240, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  8, $TargetR, 5, 3, 1, 5, 3, 1, '3HCL', 'Classique Senior 3 Homme', 1, 240, 240, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  8, $TargetC, 5, 3, 1, 5, 3, 1, '1FCO', 'Poulies Senior 1 Femme', 0, 240, 240, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, '1HCO', 'Poulies Senior 1 Homme', 0, 240, 240, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  2, $TargetC, 5, 3, 1, 5, 3, 1, '2FCO', 'Poulies Senior 2 Femme', 0, 240, 240, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  4, $TargetC, 5, 3, 1, 5, 3, 1, '2HCO', 'Poulies Senior 2 Homme', 0, 240, 240, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  2, $TargetC, 5, 3, 1, 5, 3, 1, '3FCO', 'Poulies Senior 3 Femme', 0, 240, 240, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  4, $TargetC, 5, 3, 1, 5, 3, 1, '3HCO', 'Poulies Senior 3 Homme', 0, 240, 240, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  4, $TargetR, 5, 3, 1, 5, 3, 1, 'AFBB', 'Arc Nu Scratch Femme', 1, 240, 240, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  8, $TargetR, 5, 3, 1, 5, 3, 1, 'AHBB', 'Arc Nu Scratch Homme', 1, 240, 240, 0, 0, '', '', $TargetSize4, $Distance);
					break;
				case '3': // Championships YOUTH
					CreateEvent($TourId, $i++, 0, 0,  4, $TargetR, 5, 3, 1, 5, 3, 1, 'BFCL', 'Classique Benjamine Fille', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', $TargetSize6, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  8, $TargetR, 5, 3, 1, 5, 3, 1, 'BHCL', 'Classique Benjamin Homme', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', $TargetSize6, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  8, $TargetR, 5, 3, 1, 5, 3, 1, 'MFCL', 'Classique Minime Fille',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', $TargetSize6, $Distance);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'MHCL', 'Classique Minime Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', $TargetSize6, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  8, $TargetR, 5, 3, 1, 5, 3, 1, 'CFCL', 'Classique Cadette Fille',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'CHCL', 'Classique Cadet Homme',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  8, $TargetR, 5, 3, 1, 5, 3, 1, 'JFCL', 'Classique Junior Fille',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  8, $TargetR, 5, 3, 1, 5, 3, 1, 'JHCL', 'Classique Junior Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  2, $TargetC, 5, 3, 1, 5, 3, 1, 'CFCO', 'Poulies Cadette Fille',      0, 240, MATCH_SEP_MEDALS, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  4, $TargetC, 5, 3, 1, 5, 3, 1, 'CHCO', 'Poulies Cadet Homme',      0, 240, MATCH_SEP_MEDALS, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  2, $TargetC, 5, 3, 1, 5, 3, 1, 'JFCO', 'Poulies Junior Fille',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  4, $TargetC, 5, 3, 1, 5, 3, 1, 'JHCO', 'Poulies Junior Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  2, $TargetR, 5, 3, 1, 5, 3, 1, 'YFBB', 'Arc Nu Jeune Fille',       1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', $TargetSize6, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  4, $TargetR, 5, 3, 1, 5, 3, 1, 'YHBB', 'Arc Nu Jeune Homme',       1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', $TargetSize6, $Distance);
					break;
			}
			break;
		case 3: // Outdoor championships
			switch($SubRule) {
				case 2: // TNJ
					// Individuals
					CreateEvent($TourId, $i++, 0, 0, 14, 5, 5, 3, 1, 5, 3, 1, 'BFCL', 'Classique Benjamine Fille', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 30);
					CreateEvent($TourId, $i++, 0, 0, 14, 5, 5, 3, 1, 5, 3, 1, 'BHCL', 'Classique Benjamin Homme', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 30);
					CreateEvent($TourId, $i++, 0, 0, 14, 5, 5, 3, 1, 5, 3, 1, 'MFCL', 'Classique Minime Fille',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 40);
					CreateEvent($TourId, $i++, 0, 0, 14, 5, 5, 3, 1, 5, 3, 1, 'MHCL', 'Classique Minime Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 40);
					CreateEvent($TourId, $i++, 0, 0, 14, 5, 5, 3, 1, 5, 3, 1, 'CFCL', 'Classique Cadette Fille',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60);
					CreateEvent($TourId, $i++, 0, 0, 14, 5, 5, 3, 1, 5, 3, 1, 'CHCL', 'Classique Cadet Homme',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60);
					CreateEvent($TourId, $i++, 0, 0, 14, 5, 5, 3, 1, 5, 3, 1, 'JFCL', 'Classique Junior Fille',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 14, 5, 5, 3, 1, 5, 3, 1, 'JHCL', 'Classique Junior Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 14, 9, 5, 3, 1, 5, 3, 1, 'YFCO', 'Poulies Jeune Fille',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 14, 9, 5, 3, 1, 5, 3, 1, 'YHCO', 'Poulies Jeune Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'BFC2', 'Classique Benjamine Fille (5-8)', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 30, 'BFCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'BHC2', 'Classique Benjamin Homme (5-8)', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 30, 'BHCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'MFC2', 'Classique Minime Fille (5-8)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 40, 'MFCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'MHC2', 'Classique Minime Homme (5-8)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 40, 'MHCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'CFC2', 'Classique Cadette Fille (5-8)',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60, 'CFCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'CHC2', 'Classique Cadet Homme (5-8)',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60, 'CHCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'JFC2', 'Classique Junior Fille (5-8)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70, 'JFCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'JHC2', 'Classique Junior Homme (5-8)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70, 'JHCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  2, 9, 5, 3, 1, 5, 3, 1, 'YFP2', 'Poulies Jeune Fille (5-8)',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50, 'YFCO', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  2, 9, 5, 3, 1, 5, 3, 1, 'YHP2', 'Poulies Jeune Homme (5-8)',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50, 'YHCO', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'BFC3', 'Classique Benjamine Fille (9-12)', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 30, 'BFCL', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'BHC3', 'Classique Benjamin Homme (9-12)', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 30, 'BHCL', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'MFC3', 'Classique Minime Fille (9-12)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 40, 'MFCL', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'MHC3', 'Classique Minime Homme (9-12)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 40, 'MHCL', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'CFC3', 'Classique Cadette Fille (9-12)',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60, 'CFCL', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'CHC3', 'Classique Cadet Homme (9-12)',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60, 'CHCL', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'JFC3', 'Classique Junior Fille (9-12)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70, 'JFCL', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'JHC3', 'Classique Junior Homme (9-12)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70, 'JHCL', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  4, 9, 5, 3, 1, 5, 3, 1, 'YFP3', 'Poulies Jeune Fille (9-12)',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50, 'YFCO', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  4, 9, 5, 3, 1, 5, 3, 1, 'YHP3', 'Poulies Jeune Homme (9-12)',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50, 'YHCO', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'BFC4', 'Classique Benjamine Fille (13-16)', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 30, 'BFC3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'BHC4', 'Classique Benjamin Homme (13-16)', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 30, 'BHC3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'MFC4', 'Classique Minime Fille (13-16)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 40, 'MFC3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'MHC4', 'Classique Minime Homme (13-16)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 40, 'MHC3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'CFC4', 'Classique Cadette Fille (13-16)',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60, 'CFC3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'CHC4', 'Classique Cadet Homme (13-16)',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60, 'CHC3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'JFC4', 'Classique Junior Fille (13-16)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70, 'JFC3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'JHC4', 'Classique Junior Homme (13-16)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70, 'JHC3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0,  2, 9, 5, 3, 1, 5, 3, 1, 'YFP4', 'Poulies Jeune Fille (13-16)',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50, 'YFP3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0,  2, 9, 5, 3, 1, 5, 3, 1, 'YHP4', 'Poulies Jeune Homme (13-16)',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50, 'YHP3', '0', '0', 13);

					// Team
					$i=1;
					CreateEvent($TourId, $i++, 1, 1,  2, 5, 4, 4, 2, 4, 4, 2, 'DMCJ', 'Double Mixte Classique Juniors',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70);
					CreateEvent($TourId, $i++, 1, 1,  2, 5, 4, 4, 2, 4, 4, 2, 'DMCC', 'Double Mixte Classique Cadets',   1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 60);
					CreateEvent($TourId, $i++, 1, 1,  2, 9, 4, 4, 2, 4, 4, 2, 'DMPY', 'Double Mixte Poulies Jeunes',    0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50);
					// always second team!
					safe_w_sql("update Events set EvTeamCreationMode=2 where EvTeamEvent=1 and EvTournament=$TourId");
					break;
				case 3: // Championships Youth
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'BFCL', 'Classique Benjamine Fille', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 30);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, 'BHCL', 'Classique Benjamin Homme', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 30);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, 'MFCL', 'Classique Minime Fille',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 40);
					CreateEvent($TourId, $i++, 0, 0, 16, 5, 5, 3, 1, 5, 3, 1, 'MHCL', 'Classique Minime Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 40);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, 'CFCL', 'Classique Cadette Fille',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60);
					CreateEvent($TourId, $i++, 0, 0, 16, 5, 5, 3, 1, 5, 3, 1, 'CHCL', 'Classique Cadet Homme',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, 'JFCL', 'Classique Junior Fille',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 16, 5, 5, 3, 1, 5, 3, 1, 'JHCL', 'Classique Junior Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0,  2, 9, 5, 3, 1, 5, 3, 1, 'CFCO', 'Poulies Cadette Fille',      0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  4, 9, 5, 3, 1, 5, 3, 1, 'CHCO', 'Poulies Cadet Homme',      0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  2, 9, 5, 3, 1, 5, 3, 1, 'JFCO', 'Poulies Junior Fille',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  4, 9, 5, 3, 1, 5, 3, 1, 'JHCO', 'Poulies Junior Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);

					// MIXED TEAMS and Teams
					$i=1;
					CreateEvent($TourId, $i++, 1, 1,  4, 5, 4, 4, 2, 4, 4, 2, 'DMJ', 'Double Mixte Jeunes',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 60);
					CreateEvent($TourId, $i++, 1, 0,  8, 5, 4, 6, 2, 4, 6, 2, 'CJH', 'Cadet/Junior Hommes',   1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 60);
					CreateEvent($TourId, $i++, 1, 0,  8, 5, 4, 6, 2, 4, 6, 2, 'CJF', 'Cadet/Junior Filles',    1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 60);
					CreateEvent($TourId, $i++, 1, 0,  8, 5, 4, 6, 2, 4, 6, 2, 'BM',  'Benjamin/Minime',     1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 30);

					// drop out after 1/8
					CreateEvent($TourId, $i++, 1, 0,  4, 5, 4, 6, 2, 4, 6, 2, 'CJH3', 'Cadet/Junior Hommes (9-12)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 60,'CJH', '0', '0', '9');
					CreateEvent($TourId, $i++, 1, 0,  4, 5, 4, 6, 2, 4, 6, 2, 'CJF3', 'Cadet/Junior Filles (9-12)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 60,'CJF', '0', '0', '9');
					CreateEvent($TourId, $i++, 1, 0,  4, 5, 4, 6, 2, 4, 6, 2, 'BM3',  'Benjamin/Minime (9-12)',     1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 30,'BM', '0', '0', '9');

					// drop out after 1/4
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 2, 4, 6, 2, 'CJH2', 'Cadet/Junior Hommes (5-8)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 60,'CJH', '0', '0', '5');
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 2, 4, 6, 2, 'CJF2', 'Cadet/Junior Filles (5-8)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 60,'CJF', '0', '0', '5');
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 2, 4, 6, 2, 'BM2',  'Benjamin/Minime (5-8)',     1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 30,'BM', '0', '0', '5');

					// drop out after 1/4 of losers 1/8
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 2, 4, 6, 2, 'CJH4', 'Cadet/Junior Hommes (13-16)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 60,'CJH3', '0', '0', '13');
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 2, 4, 6, 2, 'CJF4', 'Cadet/Junior Filles (13-16)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 60,'CJF3', '0', '0', '13');
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 2, 4, 6, 2, 'BM4',  'Benjamin/Minime (13-16)',     1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 30,'BM3', '0', '0', '13');
					break;
				case 4: // Championships Scratch Recurve
					CreateEvent($TourId, $i++, 0, 0, 16, 5, 5, 3, 1, 5, 3, 1, 'FCL', 'Classique Scratch Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 24, 5, 5, 3, 1, 5, 3, 1, 'HCL', 'Classique Scratch Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);

					// MIXED TEAMS
					$i=1;
					CreateEvent($TourId, $i++, 1, 1,  4, 5, 4, 4, 2, 4, 4, 2, 'DMCL', 'Double Mixte Classique',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70);
					break;
				case 5: // Championships Scratch Compound
					CreateEvent($TourId, $i++, 0, 0,  16, 9, 5, 3, 1, 5, 3, 1, 'FCO', 'Poulies Scratch Femme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  24, 9, 5, 3, 1, 5, 3, 1, 'HCO', 'Poulies Scratch Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);

					// MIXED TEAMS
					$i=1;
					CreateEvent($TourId, $i++, 1, 1,  4, 9, 4, 4, 2, 4, 4, 2, 'DMCO', 'Double Mixte Poulie',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50);
					break;
				case 6: // Championships Veterans
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, 'VFCL', 'Classique Vétéran Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 16, 5, 5, 3, 1, 5, 3, 1, 'VHCL', 'Classique Vétéran Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, 'WFCL', 'Classique Super Vétéran Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, 'WHCL', 'Classique Super Vétéran Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0,  8, 9, 5, 3, 1, 5, 3, 1, 'VFCO', 'Poulies Vétéran Femme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 16, 9, 5, 3, 1, 5, 3, 1, 'VHCO', 'Poulies Vétéran Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  4, 9, 5, 3, 1, 5, 3, 1, 'WFCO', 'Poulies Super Vétéran Femme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  8, 9, 5, 3, 1, 5, 3, 1, 'WHCO', 'Poulies Super Vétéran Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					break;
				case 7: // D1/DNAP
					// 2019: we have 4 individual events and 1 match event for each of the 4 categories, sort of round robin
					CreateEvent($TourId, $i++, 0, 0, 64, 5, 5, 3, 1, 5, 3, 1, 'FCL1', 'Classique Femme 1',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 64, 5, 5, 3, 1, 5, 3, 1, 'FCL2', 'Classique Femme 2',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 64, 5, 5, 3, 1, 5, 3, 1, 'FCL3', 'Classique Femme 3',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 64, 5, 5, 3, 1, 5, 3, 1, 'FCL4', 'Classique Femme 4',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 64, 5, 5, 3, 1, 5, 3, 1, 'HCL1', 'Classique Homme 1',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 64, 5, 5, 3, 1, 5, 3, 1, 'HCL2', 'Classique Homme 2',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 64, 5, 5, 3, 1, 5, 3, 1, 'HCL3', 'Classique Homme 3',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 64, 5, 5, 3, 1, 5, 3, 1, 'HCL4', 'Classique Homme 4',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 64, 9, 5, 3, 1, 5, 3, 1, 'FCO1', 'Poulies Femme 1',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 64, 9, 5, 3, 1, 5, 3, 1, 'FCO2', 'Poulies Femme 2',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 64, 9, 5, 3, 1, 5, 3, 1, 'FCO3', 'Poulies Femme 3',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 64, 9, 5, 3, 1, 5, 3, 1, 'FCO4', 'Poulies Femme 4',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 64, 9, 5, 3, 1, 5, 3, 1, 'HCO1', 'Poulies Homme 1',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 64, 9, 5, 3, 1, 5, 3, 1, 'HCO2', 'Poulies Homme 2',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 64, 9, 5, 3, 1, 5, 3, 1, 'HCO3', 'Poulies Homme 3',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 64, 9, 5, 3, 1, 5, 3, 1, 'HCO4', 'Poulies Homme 4',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 0, 5, 5, 3, 1, 5, 3, 1, 'FCL', 'Classique Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 0, 5, 5, 3, 1, 5, 3, 1, 'HCL', 'Classique Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 0, 9, 5, 3, 1, 5, 3, 1, 'FCO', 'Poulies Femme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 0, 9, 5, 3, 1, 5, 3, 1, 'HCO', 'Poulies Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					// teams... Team matches
					$i=1;
					CreateEvent($TourId, $i++, 1, 0, 64, 5, 4, 6, 3, 4, 6, 3, 'FCL', 'Equipe Classique Femme',   1, 240, MATCH_ALL_SEP, 0, 0, '', '', 122, 70, '', '', '16');
					CreateEvent($TourId, $i++, 1, 0, 64, 5, 4, 6, 3, 4, 6, 3, 'HCL', 'Equipe Classique Homme',   1, 240, MATCH_ALL_SEP, 0, 0, '', '', 122, 70, '', '', '16');
					CreateEvent($TourId, $i++, 1, 0, 64, 9, 4, 6, 3, 4, 6, 3, 'FCO', 'Equipe Poulies Femme',     0, 240, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, '', '', '8');
					CreateEvent($TourId, $i++, 1, 0, 64, 9, 4, 6, 3, 4, 6, 3, 'HCO', 'Equipe Poulies Homme',     0, 240, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, '', '', '16');
					break;
				case 8: // Fédéral
					// NO EVENTS!!!
					break;
				case 9: // DR/D2
					CreateEvent($TourId, $i++, 1, 0,  12, 5, 4, 6, 3, 4, 6, 3, 'DRRF', 'Equipes DR Classique Femme',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70);
					CreateEvent($TourId, $i++, 1, 0,  12, 5, 4, 6, 3, 4, 6, 3, 'DRRH', 'Equipes DR Classique Homme',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70);
					CreateEvent($TourId, $i++, 1, 0,  12, 9, 4, 6, 3, 4, 6, 3, 'DRCF', 'Equipes DR Poulies Femme',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 1, 0,  12, 9, 4, 6, 3, 4, 6, 3, 'DRCH', 'Equipes DR Poulies Homme',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 1, 0,  8, 5, 4, 6, 3, 4, 6, 3, 'D2F', 'Equipes D2 Femme',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70);
					CreateEvent($TourId, $i++, 1, 0,  8, 5, 4, 6, 3, 4, 6, 3, 'D2H', 'Equipes D2 Homme',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70);

					// losers of 1/12 brackets 1st round (all byes in 1/8 so go directly to 1/4 but need to be stated as 1/8 to work)
					CreateEvent($TourId, $i++, 1, 0,  8, 5, 4, 6, 3, 4, 6, 3, 'RF17', 'Equipes DR Classique Femme (17-20)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DRRF', '0', '8', 17);
					CreateEvent($TourId, $i++, 1, 0,  8, 5, 4, 6, 3, 4, 6, 3, 'RH17', 'Equipes DR Classique Homme (17-20)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DRRH', '0', '8', 17);
					CreateEvent($TourId, $i++, 1, 0,  8, 9, 4, 6, 3, 4, 6, 3, 'CF17', 'Equipes DR Poulies Femme (17-20)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'DRCF', '0', '8', 17);
					CreateEvent($TourId, $i++, 1, 0,  8, 9, 4, 6, 3, 4, 6, 3, 'CH17', 'Equipes DR Poulies Homme (17-20)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'DRCH', '0', '8', 17);

					// losers of 1/12 brackets 2nd round
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'RF21', 'Equipes DR Classique Femme (21-24)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'RF17', '0', '0', 21);
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'RH21', 'Equipes DR Classique Homme (21-24)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'RH17', '0', '0', 21);
					CreateEvent($TourId, $i++, 1, 0,  2, 9, 4, 6, 3, 4, 6, 3, 'CF21', 'Equipes DR Poulies Femme (21-24)',   0, 0, MATCH_ALL_SEP, 0, 0, '', '',   80, 50, 'CF17', '0', '0', 21);
					CreateEvent($TourId, $i++, 1, 0,  2, 9, 4, 6, 3, 4, 6, 3, 'CH21', 'Equipes DR Poulies Homme (21-24)',   0, 0, MATCH_ALL_SEP, 0, 0, '', '',   80, 50, 'CH17', '0', '0', 21);

					// losers of 1/8 brackets of main stream
					CreateEvent($TourId, $i++, 1, 0,  4, 5, 4, 6, 3, 4, 6, 3, 'RF09', 'Equipes DR Classique Femme (9-12)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DRRF', '0', '0', 9);
					CreateEvent($TourId, $i++, 1, 0,  4, 5, 4, 6, 3, 4, 6, 3, 'RH09', 'Equipes DR Classique Homme (9-12)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DRRH', '0', '0', 9);
					CreateEvent($TourId, $i++, 1, 0,  4, 9, 4, 6, 3, 4, 6, 3, 'CF09', 'Equipes DR Poulies Femme (9-12)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'DRCF', '0', '0', 9);
					CreateEvent($TourId, $i++, 1, 0,  4, 9, 4, 6, 3, 4, 6, 3, 'CH09', 'Equipes DR Poulies Homme (9-12)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'DRCH', '0', '0', 9);
					CreateEvent($TourId, $i++, 1, 0,  4, 5, 4, 6, 3, 4, 6, 3, 'DF09', 'Equipes D2 Femme (9-12)',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'D2F', '0', '0', 9);
					CreateEvent($TourId, $i++, 1, 0,  4, 5, 4, 6, 3, 4, 6, 3, 'DH09', 'Equipes D2 Homme (9-12)',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'D2H', '0', '0', 9);

					// losers of 1/4 brackets of main stream
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'RF05', 'Equipes DR Classique Femme (5-8)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DRRF', '0', '0', 5);
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'RH05', 'Equipes DR Classique Homme (5-8)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DRRH', '0', '0', 5);
					CreateEvent($TourId, $i++, 1, 0,  2, 9, 4, 6, 3, 4, 6, 3, 'CF05', 'Equipes DR Poulies Femme (5-8)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'DRCF', '0', '0', 5);
					CreateEvent($TourId, $i++, 1, 0,  2, 9, 4, 6, 3, 4, 6, 3, 'CH05', 'Equipes DR Poulies Homme (5-8)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'DRCH', '0', '0', 5);
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'DF05', 'Equipes D2 Femme (5-8)',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'D2F', '0', '0', 5);
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'DH05', 'Equipes D2 Homme (5-8)',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'D2H', '0', '0', 5);

					// losers of 1/4 brackets of 1/8 losers (go for 13-16 position)
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'RF13', 'Equipes DR Classique Femme (13-16)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'RF09', '0', '0', 13);
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'RH13', 'Equipes DR Classique Homme (13-16)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'RH09', '0', '0', 13);
					CreateEvent($TourId, $i++, 1, 0,  2, 9, 4, 6, 3, 4, 6, 3, 'CF13', 'Equipes DR Poulies Femme (13-16)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'CF09', '0', '0', 13);
					CreateEvent($TourId, $i++, 1, 0,  2, 9, 4, 6, 3, 4, 6, 3, 'CH13', 'Equipes DR Poulies Homme (13-16)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'CH09', '0', '0', 13);
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'DF13', 'Equipes D2 Femme (13-16)',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DF09', '0', '0', 13);
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'DH13', 'Equipes D2 Homme (13-16)',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DH09', '0', '0', 13);
					break;
				case 10: // Champs French
					// Championships Scratch Recurve
					CreateEvent($TourId, $i++, 0, 0, 16, 5, 5, 3, 1, 5, 3, 1, 'FCL', 'Classique Scratch Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 24, 5, 5, 3, 1, 5, 3, 1, 'HCL', 'Classique Scratch Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					// Championships Scratch Compound
					CreateEvent($TourId, $i++, 0, 0,  16, 9, 5, 3, 1, 5, 3, 1, 'FCO', 'Poulies Scratch Femme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  24, 9, 5, 3, 1, 5, 3, 1, 'HCO', 'Poulies Scratch Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);

					// MIXED TEAMS
					$i=1;
					CreateEvent($TourId, $i++, 1, 1,  4, 5, 4, 4, 2, 4, 4, 2, 'DMCL', 'Double Mixte Classique',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70);
					CreateEvent($TourId, $i++, 1, 1,  4, 9, 4, 4, 2, 4, 4, 2, 'DMCO', 'Double Mixte Poulie',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50);
					break;
				case 11:
					// Coupe de France
					CreateEvent($TourId, $i++, 0, 0, 16, 5, 5, 3, 1, 5, 3, 1, 'JHCL', 'Classique Junior Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, 'JFCL', 'Classique Junior Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, '1FCL', 'Classique Senior 1 Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 16, 5, 5, 3, 1, 5, 3, 1, '1HCL', 'Classique Senior 1 Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, '2FCL', 'Classique Senior 2 Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 16, 5, 5, 3, 1, 5, 3, 1, '2HCL', 'Classique Senior 2 Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, '3FCL', 'Classique Senior 3 Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, '3HCL', 'Classique Senior 3 Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60);
					CreateEvent($TourId, $i++, 0, 0,  8, 9, 5, 3, 1, 5, 3, 1, 'JFCO', 'Poulies Junior Femme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 16, 9, 5, 3, 1, 5, 3, 1, 'JHCO', 'Poulies Junior Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  8, 9, 5, 3, 1, 5, 3, 1, '1FCO', 'Poulies Senior 1 Femme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 16, 9, 5, 3, 1, 5, 3, 1, '1HCO', 'Poulies Senior 1 Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  8, 9, 5, 3, 1, 5, 3, 1, '2FCO', 'Poulies Senior 2 Femme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 16, 9, 5, 3, 1, 5, 3, 1, '2HCO', 'Poulies Senior 2 Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  4, 9, 5, 3, 1, 5, 3, 1, '3FCO', 'Poulies Senior 3 Femme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  8, 9, 5, 3, 1, 5, 3, 1, '3HCO', 'Poulies Senior 3 Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					break;
			}
			break;
	}
}

function InsertStandardEvents($TourId, $TourType, $SubRule) {
	switch($TourType) {
		case 6:
			switch($SubRule) {
				case '2':
					InsertClassEvent($TourId, 0, 1,'1HCL', 'CL', '1H');
					InsertClassEvent($TourId, 0, 1,'1FCL', 'CL', '1F');
					InsertClassEvent($TourId, 0, 1,'2HCL', 'CL', '2H');
					InsertClassEvent($TourId, 0, 1,'2FCL', 'CL', '2F');
					InsertClassEvent($TourId, 0, 1,'3HCL', 'CL', '3H');
					InsertClassEvent($TourId, 0, 1,'3FCL', 'CL', '3F');
					InsertClassEvent($TourId, 0, 1,'1HCO', 'CO', '1H');
					InsertClassEvent($TourId, 0, 1,'1FCO', 'CO', '1F');
					InsertClassEvent($TourId, 0, 1,'2HCO', 'CO', '2H');
					InsertClassEvent($TourId, 0, 1,'2FCO', 'CO', '2F');
					InsertClassEvent($TourId, 0, 1,'3HCO', 'CO', '3H');
					InsertClassEvent($TourId, 0, 1,'3FCO', 'CO', '3F');
					InsertClassEvent($TourId, 0, 1,'AHBB', 'BB', '1H');
					InsertClassEvent($TourId, 0, 1,'AFBB', 'BB', '1F');
					InsertClassEvent($TourId, 0, 1,'AHBB', 'BB', '2H');
					InsertClassEvent($TourId, 0, 1,'AFBB', 'BB', '2F');
					InsertClassEvent($TourId, 0, 1,'AHBB', 'BB', '3H');
					InsertClassEvent($TourId, 0, 1,'AFBB', 'BB', '3F');
					break;
				case '3': // Championships YOUTH
					$TargetSizeB=60;
					InsertClassEvent($TourId, 0, 1, 'JHCL', 'CL','JH');
					InsertClassEvent($TourId, 0, 1, 'JFCL', 'CL','JF');
					InsertClassEvent($TourId, 0, 1, 'CHCL', 'CL','CH');
					InsertClassEvent($TourId, 0, 1, 'CFCL', 'CL','CF');
					InsertClassEvent($TourId, 0, 1, 'MHCL', 'CL','MH');
					InsertClassEvent($TourId, 0, 1, 'MFCL', 'CL','MF');
					InsertClassEvent($TourId, 0, 1, 'BHCL', 'CL','BH');
					InsertClassEvent($TourId, 0, 1, 'BFCL', 'CL','BF');
					InsertClassEvent($TourId, 0, 1, 'JHCO', 'CO','JH');
					InsertClassEvent($TourId, 0, 1, 'JFCO', 'CO','JF');
					InsertClassEvent($TourId, 0, 1, 'CHCO', 'CO','CH');
					InsertClassEvent($TourId, 0, 1, 'CFCO', 'CO','CF');
					InsertClassEvent($TourId, 0, 1, 'YHBB', 'BB','JH');
					InsertClassEvent($TourId, 0, 1, 'YFBB', 'BB','JF');
					InsertClassEvent($TourId, 0, 1, 'YHBB', 'BB','CH');
					InsertClassEvent($TourId, 0, 1, 'YFBB', 'BB','CF');
					InsertClassEvent($TourId, 0, 1, 'YHBB', 'BB','MH');
					InsertClassEvent($TourId, 0, 1, 'YFBB', 'BB','MF');
					break;
			}
			break;
		case 3:
			switch($SubRule) {
				case 2: // TNJ
					InsertClassEvent($TourId, 0, 1, 'JHCL', 'CL','JH');
					InsertClassEvent($TourId, 0, 1, 'JFCL', 'CL','JF');
					InsertClassEvent($TourId, 0, 1, 'CHCL', 'CL','CH');
					InsertClassEvent($TourId, 0, 1, 'CFCL', 'CL','CF');
					InsertClassEvent($TourId, 0, 1, 'MHCL', 'CL','MH');
					InsertClassEvent($TourId, 0, 1, 'MFCL', 'CL','MF');
					InsertClassEvent($TourId, 0, 1, 'BHCL', 'CL','BH');
					InsertClassEvent($TourId, 0, 1, 'BFCL', 'CL','BF');
					InsertClassEvent($TourId, 0, 1, 'YHCO', 'CO','JH');
					InsertClassEvent($TourId, 0, 1, 'YFCO', 'CO','JF');
					InsertClassEvent($TourId, 0, 1, 'YHCO', 'CO','CH');
					InsertClassEvent($TourId, 0, 1, 'YFCO', 'CO','CF');
					// Mixed Team
					InsertClassEvent($TourId, 1, 1, 'DMCJ', 'CL','JF');
					InsertClassEvent($TourId, 1, 1, 'DMCC', 'CL','CF');
					InsertClassEvent($TourId, 1, 1, 'DMPY', 'CO','JF');
					InsertClassEvent($TourId, 1, 1, 'DMPY', 'CO','CF');
					InsertClassEvent($TourId, 2, 1, 'DMCJ', 'CL','JH');
					InsertClassEvent($TourId, 2, 1, 'DMCC', 'CL','CH');
					InsertClassEvent($TourId, 2, 1, 'DMPY', 'CO','JH');
					InsertClassEvent($TourId, 2, 1, 'DMPY', 'CO','CH');
					break;
				case 3: // Championship Youth
					InsertClassEvent($TourId, 0, 1, 'JHCL', 'CL','JH');
					InsertClassEvent($TourId, 0, 1, 'JFCL', 'CL','JF');
					InsertClassEvent($TourId, 0, 1, 'CHCL', 'CL','CH');
					InsertClassEvent($TourId, 0, 1, 'CFCL', 'CL','CF');
					InsertClassEvent($TourId, 0, 1, 'MHCL', 'CL','MH');
					InsertClassEvent($TourId, 0, 1, 'MFCL', 'CL','MF');
					InsertClassEvent($TourId, 0, 1, 'BHCL', 'CL','BH');
					InsertClassEvent($TourId, 0, 1, 'BFCL', 'CL','BF');
					InsertClassEvent($TourId, 0, 1, 'JHCO', 'CO','JH');
					InsertClassEvent($TourId, 0, 1, 'JFCO', 'CO','JF');
					InsertClassEvent($TourId, 0, 1, 'CHCO', 'CO','CH');
					InsertClassEvent($TourId, 0, 1, 'CFCO', 'CO','CF');
					// Mixed Team
					InsertClassEvent($TourId, 1, 1, 'DMJ', 'CL','JF');
					InsertClassEvent($TourId, 1, 1, 'DMJ', 'CL','CF');
					InsertClassEvent($TourId, 2, 1, 'DMJ', 'CL','JH');
					InsertClassEvent($TourId, 2, 1, 'DMJ', 'CL','CH');
					// Teams
					InsertClassEvent($TourId, 1, 3, 'CJH', 'CL','JH');
					InsertClassEvent($TourId, 1, 3, 'CJH', 'CL','CH');
					InsertClassEvent($TourId, 1, 3, 'CJF', 'CL','JF');
					InsertClassEvent($TourId, 1, 3, 'CJF', 'CL','CF');
					InsertClassEvent($TourId, 1, 3, 'BM', 'CL','BF');
					InsertClassEvent($TourId, 1, 3, 'BM', 'CL','BH');
					InsertClassEvent($TourId, 1, 3, 'BM', 'CL','MF');
					InsertClassEvent($TourId, 1, 3, 'BM', 'CL','MH');
					break;
				case 4: // deprecated
					// Championships Scratch Recurve
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','CF');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','JF');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','SF');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','VF');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','WF');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','CH');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','JH');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','SH');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','VH');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','WH');
					// Mixed Team
					InsertClassEvent($TourId, 1, 1, 'DMCL', 'CL','CF');
					InsertClassEvent($TourId, 1, 1, 'DMCL', 'CL','JF');
					InsertClassEvent($TourId, 1, 1, 'DMCL', 'CL','SF');
					InsertClassEvent($TourId, 1, 1, 'DMCL', 'CL','VF');
					InsertClassEvent($TourId, 1, 1, 'DMCL', 'CL','WF');
					InsertClassEvent($TourId, 2, 1, 'DMCL', 'CL','CH');
					InsertClassEvent($TourId, 2, 1, 'DMCL', 'CL','JH');
					InsertClassEvent($TourId, 2, 1, 'DMCL', 'CL','SH');
					InsertClassEvent($TourId, 2, 1, 'DMCL', 'CL','VH');
					InsertClassEvent($TourId, 2, 1, 'DMCL', 'CL','WH');
					break;
				case 5: // deprecated
					// Championships Scratch Compound
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','CF');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','JF');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','SF');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','VF');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','WF');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','CH');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','JH');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','SH');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','VH');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','WH');
					// Mixed Team
					InsertClassEvent($TourId, 1, 1, 'DMCO', 'CO','CF');
					InsertClassEvent($TourId, 1, 1, 'DMCO', 'CO','JF');
					InsertClassEvent($TourId, 1, 1, 'DMCO', 'CO','SF');
					InsertClassEvent($TourId, 1, 1, 'DMCO', 'CO','VF');
					InsertClassEvent($TourId, 1, 1, 'DMCO', 'CO','WF');
					InsertClassEvent($TourId, 2, 1, 'DMCO', 'CO','CH');
					InsertClassEvent($TourId, 2, 1, 'DMCO', 'CO','JH');
					InsertClassEvent($TourId, 2, 1, 'DMCO', 'CO','SH');
					InsertClassEvent($TourId, 2, 1, 'DMCO', 'CO','VH');
					InsertClassEvent($TourId, 2, 1, 'DMCO', 'CO','WH');
					break;
				case 6: // deprecated
					// Championship Veteran
					InsertClassEvent($TourId, 0, 1, 'VFCL', 'CL','VF');
					InsertClassEvent($TourId, 0, 1, 'VHCL', 'CL','VH');
					InsertClassEvent($TourId, 0, 1, 'WFCL', 'CL','WF');
					InsertClassEvent($TourId, 0, 1, 'WHCL', 'CL','WH');
					InsertClassEvent($TourId, 0, 1, 'VFCO', 'CO','VF');
					InsertClassEvent($TourId, 0, 1, 'VHCO', 'CO','VH');
					InsertClassEvent($TourId, 0, 1, 'WFCO', 'CO','WF');
					InsertClassEvent($TourId, 0, 1, 'WHCO', 'CO','WH');
					break;
				case 7: // D1/DNAP... team events selection are as usual, indivudal no
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','CF');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','JF');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','1F');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','2F');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','3F');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','CH');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','JH');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','1H');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','2H');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','3H');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','CF');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','JF');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','1F');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','2F');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','3F');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','CH');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','JH');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','1H');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','2H');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','3H');
					// Teams
					InsertClassEvent($TourId, 1, 3, 'FCL', 'CL','CF');
					InsertClassEvent($TourId, 1, 3, 'FCL', 'CL','JF');
					InsertClassEvent($TourId, 1, 3, 'FCL', 'CL','1F');
					InsertClassEvent($TourId, 1, 3, 'FCL', 'CL','2F');
					InsertClassEvent($TourId, 1, 3, 'FCL', 'CL','3F');
					InsertClassEvent($TourId, 1, 3, 'HCL', 'CL','CH');
					InsertClassEvent($TourId, 1, 3, 'HCL', 'CL','JH');
					InsertClassEvent($TourId, 1, 3, 'HCL', 'CL','1H');
					InsertClassEvent($TourId, 1, 3, 'HCL', 'CL','2H');
					InsertClassEvent($TourId, 1, 3, 'HCL', 'CL','3H');
					InsertClassEvent($TourId, 1, 3, 'FCO', 'CO','CF');
					InsertClassEvent($TourId, 1, 3, 'FCO', 'CO','JF');
					InsertClassEvent($TourId, 1, 3, 'FCO', 'CO','1F');
					InsertClassEvent($TourId, 1, 3, 'FCO', 'CO','2F');
					InsertClassEvent($TourId, 1, 3, 'FCO', 'CO','3F');
					InsertClassEvent($TourId, 1, 3, 'HCO', 'CO','CH');
					InsertClassEvent($TourId, 1, 3, 'HCO', 'CO','JH');
					InsertClassEvent($TourId, 1, 3, 'HCO', 'CO','1H');
					InsertClassEvent($TourId, 1, 3, 'HCO', 'CO','2H');
					InsertClassEvent($TourId, 1, 3, 'HCO', 'CO','3H');
					break;
				case 8: // deprecated
					// Fédéral
					// no events
					break;
				case 9: // DR/D2
					InsertClassEvent($TourId, 1, 3, 'DRRF', 'CL','CF');
					InsertClassEvent($TourId, 1, 3, 'DRRF', 'CL','JF');
					InsertClassEvent($TourId, 1, 3, 'DRRF', 'CL','SF');
					InsertClassEvent($TourId, 1, 3, 'DRRF', 'CL','VF');
					InsertClassEvent($TourId, 1, 3, 'DRRF', 'CL','WF');
					InsertClassEvent($TourId, 1, 3, 'DRCF', 'CO','CF');
					InsertClassEvent($TourId, 1, 3, 'DRCF', 'CO','JF');
					InsertClassEvent($TourId, 1, 3, 'DRCF', 'CO','SF');
					InsertClassEvent($TourId, 1, 3, 'DRCF', 'CO','VF');
					InsertClassEvent($TourId, 1, 3, 'DRCF', 'CO','WF');
					InsertClassEvent($TourId, 1, 3, 'D2F', 'CL','CF');
					InsertClassEvent($TourId, 1, 3, 'D2F', 'CL','JF');
					InsertClassEvent($TourId, 1, 3, 'D2F', 'CL','SF');
					InsertClassEvent($TourId, 1, 3, 'D2F', 'CL','VF');
					InsertClassEvent($TourId, 1, 3, 'D2F', 'CL','WF');
					InsertClassEvent($TourId, 1, 3, 'DRRH', 'CL','CH');
					InsertClassEvent($TourId, 1, 3, 'DRRH', 'CL','JH');
					InsertClassEvent($TourId, 1, 3, 'DRRH', 'CL','SH');
					InsertClassEvent($TourId, 1, 3, 'DRRH', 'CL','VH');
					InsertClassEvent($TourId, 1, 3, 'DRRH', 'CL','WH');
					InsertClassEvent($TourId, 1, 3, 'DRCH', 'CO','CH');
					InsertClassEvent($TourId, 1, 3, 'DRCH', 'CO','JH');
					InsertClassEvent($TourId, 1, 3, 'DRCH', 'CO','SH');
					InsertClassEvent($TourId, 1, 3, 'DRCH', 'CO','VH');
					InsertClassEvent($TourId, 1, 3, 'DRCH', 'CO','WH');
					InsertClassEvent($TourId, 1, 3, 'D2H', 'CL','CH');
					InsertClassEvent($TourId, 1, 3, 'D2H', 'CL','JH');
					InsertClassEvent($TourId, 1, 3, 'D2H', 'CL','SH');
					InsertClassEvent($TourId, 1, 3, 'D2H', 'CL','VH');
					InsertClassEvent($TourId, 1, 3, 'D2H', 'CL','WH');
					break;
				case 10:
					// Championships Scratch Recurve
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','CF');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','JF');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','1F');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','2F');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','3F');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','CH');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','JH');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','1H');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','2H');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','3H');
					// Championships Scratch Compound
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','CF');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','JF');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','1F');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','2F');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','3F');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','CH');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','JH');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','1H');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','2H');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','3H');
					// Mixed Team Recurve
					InsertClassEvent($TourId, 1, 1, 'DMCL', 'CL','CF');
					InsertClassEvent($TourId, 1, 1, 'DMCL', 'CL','JF');
					InsertClassEvent($TourId, 1, 1, 'DMCL', 'CL','1F');
					InsertClassEvent($TourId, 1, 1, 'DMCL', 'CL','2F');
					InsertClassEvent($TourId, 1, 1, 'DMCL', 'CL','3F');
					InsertClassEvent($TourId, 2, 1, 'DMCL', 'CL','CH');
					InsertClassEvent($TourId, 2, 1, 'DMCL', 'CL','JH');
					InsertClassEvent($TourId, 2, 1, 'DMCL', 'CL','1H');
					InsertClassEvent($TourId, 2, 1, 'DMCL', 'CL','2H');
					InsertClassEvent($TourId, 2, 1, 'DMCL', 'CL','3H');
					// Mixed Team Compound
					InsertClassEvent($TourId, 1, 1, 'DMCO', 'CO','CF');
					InsertClassEvent($TourId, 1, 1, 'DMCO', 'CO','JF');
					InsertClassEvent($TourId, 1, 1, 'DMCO', 'CO','1F');
					InsertClassEvent($TourId, 1, 1, 'DMCO', 'CO','2F');
					InsertClassEvent($TourId, 1, 1, 'DMCO', 'CO','3F');
					InsertClassEvent($TourId, 2, 1, 'DMCO', 'CO','CH');
					InsertClassEvent($TourId, 2, 1, 'DMCO', 'CO','JH');
					InsertClassEvent($TourId, 2, 1, 'DMCO', 'CO','1H');
					InsertClassEvent($TourId, 2, 1, 'DMCO', 'CO','2H');
					InsertClassEvent($TourId, 2, 1, 'DMCO', 'CO','3H');
					break;
				case 11: // Coupe France
					// Recurve
					InsertClassEvent($TourId, 0, 1, 'JFCL', 'CL','JF');
					InsertClassEvent($TourId, 0, 1, '1FCL', 'CL','1F');
					InsertClassEvent($TourId, 0, 1, '2FCL', 'CL','2F');
					InsertClassEvent($TourId, 0, 1, '3FCL', 'CL','3F');
					InsertClassEvent($TourId, 0, 1, 'JHCL', 'CL','JH');
					InsertClassEvent($TourId, 0, 1, '1HCL', 'CL','1H');
					InsertClassEvent($TourId, 0, 1, '2HCL', 'CL','2H');
					InsertClassEvent($TourId, 0, 1, '3HCL', 'CL','3H');
					// Compound
					InsertClassEvent($TourId, 0, 1, 'JFCO', 'CO','JF');
					InsertClassEvent($TourId, 0, 1, '1FCO', 'CO','1F');
					InsertClassEvent($TourId, 0, 1, '2FCO', 'CO','2F');
					InsertClassEvent($TourId, 0, 1, '3FCO', 'CO','3F');
					InsertClassEvent($TourId, 0, 1, 'JHCO', 'CO','JH');
					InsertClassEvent($TourId, 0, 1, '1HCO', 'CO','1H');
					InsertClassEvent($TourId, 0, 1, '2HCO', 'CO','2H');
					InsertClassEvent($TourId, 0, 1, '3HCO', 'CO','3H');
					break;
			}
			break;
	}
}

function CreateFinals_FR_3_SetFRChampsD1DNAP($TourId) {
	CreateFinalsInd_FR_3_SetFRChampsD1DNAP($TourId);
	CreateFinalsTeam_FR_3_SetFRChampsD1DNAP($TourId);
}

/**
 * @param $TourId
 * @param string $StrEv2Delete [optional] SQL-escaped string that goes in the IN () statement
 */
function CreateFinalsInd_FR_3_SetFRChampsD1DNAP($TourId, $StrEv2Delete='') {
	safe_w_sql("INSERT INTO Finals (FinEvent, FinMatchNo, FinTournament, FinDateTime) 
		SELECT EvCode, GrMatchNo, EvTournament, " . StrSafe_DB(date('Y-m-d H:i:s')) . "
		FROM Events 
		INNER JOIN Grids ON GrMatchNo between 128 and 207
		WHERE EvTournament=$TourId AND EvTeamEvent='0' and right(EvCode, 1) in (1,2,3,4)".($StrEv2Delete ? " AND EvCode IN ($StrEv2Delete)" : ""));
}

/**
 * @param $TourId
 * @param string $StrEv2Delete [optional] SQL-escaped string that goes in the IN () statement
 */
function CreateFinalsTeam_FR_3_SetFRChampsD1DNAP($TourId, $StrEv2Delete='') {
	safe_w_sql("INSERT INTO TeamFinals (TfEvent, TfMatchNo, TfTournament, TfDateTime) 
		SELECT EvCode, GrMatchNo, EvTournament, " . StrSafe_DB(date('Y-m-d H:i:s')) . " 
		FROM Events 
		INNER JOIN Grids ON GrMatchNo between 128 and 207
		WHERE EvTournament=$TourId AND EvTeamEvent='1'".($StrEv2Delete ? " AND EvCode IN ($StrEv2Delete)" : ""));
}

/*

FIELD DEFINITIONS (Target Tournaments)

*/

require_once(dirname(__FILE__).'/lib-Field.php');

/*

3D DEFINITIONS (Target Tournaments)

*/

require_once(dirname(__FILE__).'/lib-3D.php');

