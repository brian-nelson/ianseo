<?php

/*

STANDARD THINGS

*/

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'ITA';
if(empty($SubRule)) $SubRule='1';

function CreateStandardDivisions($TourId, $Type=1, $SubRule=0) {
	$i=1;
	if($Type==11 or $Type==13) {
		CreateDivision($TourId, $i++, 'AI', 'Arco Istintivo');
	} else {
		CreateDivision($TourId, $i++, 'OL', 'Arco olimpico');
	}
	CreateDivision($TourId, $i++, 'CO', 'Arco Compound');
	if(!in_array($Type, array(1,2,3,4,18,39))) {
		CreateDivision($TourId, $i++, 'AN', 'Arco Nudo');
	}
	if($Type>=9 and $Type<=13) {
		CreateDivision($TourId, $i++, 'LB', 'Long Bow');
	}
	if(in_array($Type, array(1,2,3,4,5,6,7,8,18,39))) {
		CreateDivision($TourId, $i++, 'W1', 'Compound W1');
		CreateDivision($TourId, $i++, 'V1', 'Visually Impaired 1');
		CreateDivision($TourId, $i++, 'V2', 'Visually Impaired 2/3');
	}
	if(in_array($Type, array(9,10,12))) {
		CreateDivision($TourId, $i++, 'AI', 'Arco Istintivo');
	}
}

function CreateStandardClasses($TourId, $SubRule, $Field='', $Type=0) {
	$i=1;
	$Fita=in_array($Type, array(1,2,3,4,5,18,39));
	$Indoor=in_array($Type, array(6,7,8));
	switch($SubRule) {
		case '1':
			if($Field=='3D') {
				CreateClass($TourId, $i++, 21, 100, 0, 'SM', 'SM', 'Over 20 Maschile');
				CreateClass($TourId, $i++, 21, 100, 1, 'SF', 'SF', 'Over 20 Femminile');
				CreateClass($TourId, $i++,  1, 20, 0, 'JM', 'JM,SM', 'Under 20 Maschile');
				CreateClass($TourId, $i++,  1, 20, 1, 'JF', 'JF,SF', 'Under 20 Femminile');
			} else {
				$divs=($Fita && $Type!=5 ? 'CO,OL' : (($Indoor or $Field=='FIELD' or $Type==5) ? 'AN,CO,OL' : ''));
                $divsGM=($Fita && $Type!=5 ? 'OL' : (($Indoor or $Field=='FIELD' or $Type==5) ? 'AN,OL' : ''));
				CreateClass($TourId, $i++, 21, 49, 0, 'SM', 'SM', 'Senior Maschile', 1, $divs);
				CreateClass($TourId, $i++, 21, 49, 1, 'SF', 'SF', 'Senior Femminile', 1, $divs);
				CreateClass($TourId, $i++, 50,100, 0, 'MM', 'MM,SM', 'Master Maschile', 1, $divs);
				CreateClass($TourId, $i++, 50,100, 1, 'MF', 'MF,SF', 'Master Femminile', 1, $divs);
				CreateClass($TourId, $i++, 18, 20, 0, 'JM', 'JM,SM', 'Junior Maschile', 1, $divs);
				CreateClass($TourId, $i++, 18, 20, 1, 'JF', 'JF,SF', 'Junior Femminile', 1, $divs);
				CreateClass($TourId, $i++, 15, 17, 0, 'AM', 'AM,JM', 'Allievi Maschile', 1, $divs);
				CreateClass($TourId, $i++, 15, 17, 1, 'AF', 'AF,JF', 'Allieve Femminile', 1, $divs);
				CreateClass($TourId, $i++, 13, 14, 0, 'RM', 'RM,AM,JM', 'Ragazzi Maschile', 1, $divs);
				CreateClass($TourId, $i++, 13, 14, 1, 'RF', 'RF,AF,JF', 'Ragazzi Femminile', 1, $divs);
				CreateClass($TourId, $i++,  9, 12, 0, 'GM', 'GM,RM', 'Giovanissimi Maschile', 1, $divsGM);
				CreateClass($TourId, $i++,  9, 12, 1, 'GF', 'GF,RF', 'Giovanissimi Femminile', 1, $divsGM);
				if($Fita or $Indoor) {
					CreateClass($TourId, $i++,  1, 100, 0, 'M', 'M', 'Maschile', 1, 'W1');
					CreateClass($TourId, $i++,  1, 100, 1, 'F', 'F', 'Femminile', 1, 'W1');
					CreateClass($TourId, $i++,  1, 100, -1, 'U', 'U', 'Unica', 1, 'V1,V2');
				}
				if($Field=='FIELD') {
					CreateClass($TourId, $i++,  1, 100, 0, 'M', 'M', 'Maschile', 1, 'AI,LB');
					CreateClass($TourId, $i++,  1, 100, 1, 'F', 'F', 'Femminile', 1, 'AI,LB');
				}
			}
			break;
		case '2':
			if($Field=='3D') {
				CreateClass($TourId, $i++, 21, 100, 0, 'SM', 'SM', 'Over 20 Maschile');
				CreateClass($TourId, $i++, 21, 100, 1, 'SF', 'SF', 'Over 20 Femminile');
				CreateClass($TourId, $i++,  1, 20, 0, 'JM', 'JM,SM', 'Under 20 Maschile');
				CreateClass($TourId, $i++,  1, 20, 1, 'JF', 'JF,SF', 'Under 20 Femminile');
			} else {
				CreateClass($TourId, $i++, 1, 100, 0, 'SM', 'SM', 'Senior Maschile');
				CreateClass($TourId, $i++, 1, 100, 1, 'SF', 'SF', 'Senior Femminile');
			}
			break;
		case '3':
			CreateClass($TourId, $i++, 18, 20, 0, 'JM', 'JM,SM', 'Junior Maschile', 1, $Fita?'CO,OL':'');
			CreateClass($TourId, $i++, 18, 20, 1, 'JF', 'JF,SF', 'Junior Femminile', 1, $Fita?'CO,OL':'');
			CreateClass($TourId, $i++, 15, 17, 0, 'AM', 'AM,JM', 'Allievi Maschile', 1, $Fita?'CO,OL':'');
			CreateClass($TourId, $i++, 15, 17, 1, 'AF', 'AF,JF', 'Allieve Femminile', 1, $Fita?'CO,OL':'');
			CreateClass($TourId, $i++, 13, 14, 0, 'RM', 'RM,AM,JM', 'Ragazzi Maschile', 1, $Fita?'CO,OL':'');
			CreateClass($TourId, $i++, 13, 14, 1, 'RF', 'RF,AF,JF', 'Ragazzi Femminile', 1, $Fita?'CO,OL':'');
			CreateClass($TourId, $i++,  9, 12, 0, 'GM', 'GM,RM', 'Giovanissimi Maschile', 1, $Fita?'OL':'');
			CreateClass($TourId, $i++,  9, 12, 1, 'GF', 'GF,RF', 'Giovanissimi Femminile', 1, $Fita?'OL':'');
			break;
		case '4':
			CreateClass($TourId, $i++, 18, 20, 0, 'JM', 'SM', 'Junior Maschile', 1, $Fita?'CO,OL':'');
			CreateClass($TourId, $i++, 18, 20, 1, 'JF', 'SF', 'Junior Femminile', 1, $Fita?'CO,OL':'');
			CreateClass($TourId, $i++, 21, 49, 0, 'SM', 'SM', 'Senior Maschile', 1, $Fita?'CO,OL':'');
			CreateClass($TourId, $i++, 21, 49, 1, 'SF', 'SF', 'Senior Femminile', 1, $Fita?'CO,OL':'');
			CreateClass($TourId, $i++, 50,100, 0, 'MM', 'MM,SM', 'Master Maschile', 1, $Fita?'CO,OL':'');
			CreateClass($TourId, $i++, 50,100, 1, 'MF', 'MF,SF', 'Master Femminile', 1, $Fita?'CO,OL':'');
			break;
	}
}

function CreateStandardSubClasses($TourId) {
	$i=1;
	CreateSubClass($TourId, $i++, '01', '01');
	CreateSubClass($TourId, $i++, '02', '02');
	CreateSubClass($TourId, $i++, '03', '03');
	CreateSubClass($TourId, $i++, '04', '04');
}

function CreateStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
	$TargetR=($Outdoor?5:2);
	$TargetC=($Outdoor?9:4);

	$TargetSizeR=($Outdoor ? 122 : 40);
	$TargetSizeRg=($Outdoor ? 122 : 60);
	$TargetSizeC=($Outdoor ? 80 : 40);
	$DistanceR=($Outdoor ? 70 : 18);
	$DistanceRam=($Outdoor ? 60 : 18);
	$DistanceRr=($Outdoor ? 40 : 18);
	$DistanceRg=($Outdoor ? 25 : 18);
	$DistanceC=($Outdoor ? 50 : 18);


	switch($SubRule) {
		case '1':
		case '2':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLM',  'Assoluti Arco Olimpico Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLF',  'Assoluti Arco Olimpico Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COM',  'Assoluti Arco Compound Maschile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COF',  'Assoluti Arco Compound Femminile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			if(!$Outdoor) {
				CreateEvent($TourId, $i++, 0, 0, 16, 1, 5, 3, 1, 5, 3, 1, 'ANM',  'Assoluti Arco Nudo Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 0, 0, 16, 1, 5, 3, 1, 5, 3, 1, 'ANF',  'Assoluti Arco Nudo Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			}
			if($Outdoor && $SubRule==1) {
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLNM',  'Assoluti Arco Olimpico Allievi e Master Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLNF',  'Assoluti Arco Olimpico Allievi e Master Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLRM',  'Assoluti Arco Olimpico Ragazzi Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRr);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLRF',  'Assoluti Arco Olimpico Ragazze Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRr);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLGM',  'Assoluti Arco Olimpico Giovanissimi Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRg);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLGF',  'Assoluti Arco Olimpico Giovanissime Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRg);

			}
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLMT',  'Squadre Arco Olimpico Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLFT',  'Squadre Arco Olimpico Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 4, $TargetR, 4, 4, 2, 4, 4, 2, 'OLXT',  'Arco Olimpico Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			}
			CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COMT',  'Squadre Arco Compound Maschili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COFT',  'Squadre Arco Compound Femminili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 4, $TargetC, 4, 4, 2, 4, 4, 2, 'COXT',  'Arco Compound Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			} else {
				CreateEvent($TourId, $i++, 1, 0, 4, 1, 4, 6, 3, 4, 6, 3, 'ANMT',  'Squadre Arco Nudo Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 0, 4, 1, 4, 6, 3, 4, 6, 3, 'ANFT',  'Squadre Arco Nudo Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			}
			if($Outdoor && $SubRule==1) {
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLNM',  'Squadre Arco Olimpico Allievi e Master Maschile', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLNF',  'Squadre Arco Olimpico Allievi e Master Femminile', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLRM',  'Squadre Arco Olimpico Ragazzi Maschile', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRr);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLRF',  'Squadre Arco Olimpico Ragazze Femminile', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRr);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLGM',  'Squadre Arco Olimpico Giovanissimi Maschile', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRg);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLGF',  'Squadre Arco Olimpico Giovanissime Femminile', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRg);
			}
			break;
		case '3':
			if($Outdoor) {
				$i=1;
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLJM',  'Assoluti Arco Olimpico Junior Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLJF',  'Assoluti Arco Olimpico Junior Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLAM',  'Assoluti Arco Olimpico Allievi Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLAF',  'Assoluti Arco Olimpico Allievi Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLRM',  'Assoluti Arco Olimpico Ragazzi Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRr);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLRF',  'Assoluti Arco Olimpico Ragazzi Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRr);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLGM',  'Assoluti Arco Olimpico Giovanissimi Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRg);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLGF',  'Assoluti Arco Olimpico Giovanissimi Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRg);
				if($TourType==3 || $TourType==18 || $TourType==39) {
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COM',  'Assoluti Arco Compound Maschile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COF',  'Assoluti Arco Compound Femminile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
				} else {
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COJM',  'Assoluti Arco Compound Junior Maschile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COJF',  'Assoluti Arco Compound Junior Femminile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COAM',  'Assoluti Arco Compound Allievi Maschile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COAF',  'Assoluti Arco Compound Allievi Femminile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'CORM',  'Assoluti Arco Compound Ragazzi Maschile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'CORF',  'Assoluti Arco Compound Ragazzi Femminile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
				}
				$i=1;
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLJM',  'Squadre Arco Olimpico Junior Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLJF',  'Squadre Arco Olimpico Junior Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, 4, $TargetR, 4, 4, 2, 4, 4, 2, 'OLJX',  'Arco Olimpico Junior Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLAM',  'Squadre Arco Olimpico Allievi Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLAF',  'Squadre Arco Olimpico Allievi Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 1, 1, 4, $TargetR, 4, 4, 2, 4, 4, 2, 'OLAX',  'Arco Olimpico Allievi Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLRM',  'Squadre Arco Olimpico Ragazzi Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRr);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLRF',  'Squadre Arco Olimpico Ragazzi Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRr);
				CreateEvent($TourId, $i++, 1, 1, 4, $TargetR, 4, 4, 2, 4, 4, 2, 'OLRX',  'Arco Olimpico Ragazzi Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRr);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLGM',  'Squadre Arco Olimpico Giovanissimi Maschile', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRg);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLGF',  'Squadre Arco Olimpico Giovanissime Femminile', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRg);
				CreateEvent($TourId, $i++, 1, 1, 4, $TargetR, 4, 4, 2, 4, 4, 2, 'OLGX',  'Arco Olimpico Giovanissimi Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRg);
				if($TourType==3 || $TourType==18 || $TourType==39) {
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COM',  'Squadre Arco Compound Maschili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COF',  'Squadre Arco Compound Femminili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 1, 4, $TargetC, 4, 4, 2, 4, 4, 2, 'COX',  'Arco Compound Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				} else {
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COJM',  'Squadre Arco Compound Junior Maschili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COJF',  'Squadre Arco Compound Junior Femminili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 1, 4, $TargetC, 4, 4, 2, 4, 4, 2, 'COJX',  'Arco Compound Junior Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COAM',  'Squadre Arco Compound Allievi Maschili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COAF',  'Squadre Arco Compound Allievi Femminili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 1, 4, $TargetC, 4, 4, 2, 4, 4, 2, 'COAX',  'Arco Compound Allievi Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CORM',  'Squadre Arco Compound Ragazzi Maschili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CORF',  'Squadre Arco Compound Ragazzi Femminili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 1, 4, $TargetC, 4, 4, 2, 4, 4, 2, 'CORX',  'Arco Compound Ragazzi Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				}
			} else {
				$i=1;
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLM',  'Assoluti Arco Olimpico Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLF',  'Assoluti Arco Olimpico Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COM',  'Assoluti Arco Compound Maschile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COF',  'Assoluti Arco Compound Femminile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 0, 0, 16, 1, 5, 3, 1, 5, 3, 1, 'ANM',  'Assoluti Arco Nudo Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 0, 0, 16, 1, 5, 3, 1, 5, 3, 1, 'ANF',  'Assoluti Arco Nudo Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				$i=1;
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLMT',  'Squadre Arco Olimpico Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLFT',  'Squadre Arco Olimpico Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COMT',  'Squadre Arco Compound Maschili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COFT',  'Squadre Arco Compound Femminili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 0, 4, 1, 4, 6, 3, 4, 6, 3, 'ANMT',  'Squadre Arco Nudo Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 0, 4, 1, 4, 6, 3, 4, 6, 3, 'ANFT',  'Squadre Arco Nudo Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			}
			break;
		case '4':
			if($Outdoor) {
				$i=1;
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLSM',  'Assoluti Arco Olimpico Senior Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLSF',  'Assoluti Arco Olimpico Senior Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLMM',  'Assoluti Arco Olimpico Master Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLMF',  'Assoluti Arco Olimpico Master Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				if($TourType==3 || $TourType==18 || $TourType==39) {
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COM',  'Assoluti Arco Compound Maschile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COF',  'Assoluti Arco Compound Femminile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
				} else {
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COSM',  'Assoluti Arco Compound Senior Maschile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COSF',  'Assoluti Arco Compound Senior Femminile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COMM',  'Assoluti Arco Compound Master Maschile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COMF',  'Assoluti Arco Compound Master Femminile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
				}
				$i=1;
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLSM',  'Squadre Arco Olimpico Senior Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLSF',  'Squadre Arco Olimpico Senior Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, 4, $TargetR, 4, 4, 2, 4, 4, 2, 'OLSX',  'Arco Olimpico Senior Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLMM',  'Squadre Arco Olimpico Master Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLMF',  'Squadre Arco Olimpico Master Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 1, 1, 4, $TargetR, 4, 4, 2, 4, 4, 2, 'OLMX',  'Arco Olimpico Master Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				if($TourType==3 || $TourType==18) {
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COM',  'Squadre Arco Compound Maschili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COF',  'Squadre Arco Compound Femminili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 1, 4, $TargetC, 4, 4, 2, 4, 4, 2, 'COX',  'Arco Compound Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				} else {
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COSM',  'Squadre Arco Compound Senior Maschili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COSF',  'Squadre Arco Compound Senior Femminili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 1, 4, $TargetC, 4, 4, 2, 4, 4, 2, 'COSX',  'Arco Compound Senior Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COMM',  'Squadre Arco Compound Master Maschili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COMF',  'Squadre Arco Compound Master Femminili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 1, 4, $TargetC, 4, 4, 2, 4, 4, 2, 'COMX',  'Arco Compound Master Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				}
			} else {
				$i=1;
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLM',  'Assoluti Arco Olimpico Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLF',  'Assoluti Arco Olimpico Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COM',  'Assoluti Arco Compound Maschile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COF',  'Assoluti Arco Compound Femminile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 0, 0, 16, 1, 5, 3, 1, 5, 3, 1, 'ANM',  'Assoluti Arco Nudo Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 0, 0, 16, 1, 5, 3, 1, 5, 3, 1, 'ANF',  'Assoluti Arco Nudo Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				$i=1;
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLMT',  'Squadre Arco Olimpico Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLFT',  'Squadre Arco Olimpico Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COMT',  'Squadre Arco Compound Maschili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COFT',  'Squadre Arco Compound Femminili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 0, 4, 1, 4, 6, 3, 4, 6, 3, 'ANMT',  'Squadre Arco Nudo Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 0, 4, 1, 4, 6, 3, 4, 6, 3, 'ANFT',  'Squadre Arco Nudo Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			}
			break;
	}
}

function InsertStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
	if($TourType==3 || $TourType==18 || $TourType==39) {
		switch($SubRule) {
			case '1':
				$ds=array(
					'OL' => array('S', 'J'),
					'OLN' => array('M', 'A'),
					'OLR' => array('R'),
					'OLG' => array('G'),
					'CO' => array('S', 'J', 'M', 'A', 'R'),
					);
			case '2':
				if(empty($ds)) {
					$ds=array(
						'OL' => array('S'),
						'CO' => array('S'),
						);
				}
				foreach($ds as $d => $cs) {
					foreach($cs as $c) {
						InsertClassEvent($TourId, 0, 1, $d.'M', $d, $c.'M');
						InsertClassEvent($TourId, 0, 1, $d.'F', $d, $c.'F');

						InsertClassEvent($TourId, 1, 3, $d.'MT', $d, $c.'M');
						InsertClassEvent($TourId, 1, 3, $d.'FT', $d, $c.'F');
						InsertClassEvent($TourId, 1, 1, $d.'XT', $d, $c.'M');
						InsertClassEvent($TourId, 2, 1, $d.'XT', $d, $c.'F');
					}
				}
				break;
			case '3':
				$ds=array(
					'OL' => array('J', 'A', 'R', 'G'),
					'CO' => array('J', 'A', 'R'),
					);
			case '4':
				if(empty($ds)) {
					$ds=array(
						'OL' => array('S', 'M'),
						'CO' => array('S', 'M'),
						);
				}
				foreach($ds as $d => $cs) {
					foreach($cs as $c) {
						InsertClassEvent($TourId, 0, 1, $d.($d=='CO' ? '' : $c).'M', $d, $c.'M');
						InsertClassEvent($TourId, 0, 1, $d.($d=='CO' ? '' : $c).'F', $d, $c.'F');

						InsertClassEvent($TourId, 1, 3, $d.($d=='CO' ? '' : $c).'M', $d, $c.'M');
						InsertClassEvent($TourId, 1, 3, $d.($d=='CO' ? '' : $c).'F', $d, $c.'F');
						InsertClassEvent($TourId, 1, 1, $d.($d=='CO' ? '' : $c).'X', $d, $c.'M');
						InsertClassEvent($TourId, 2, 1, $d.($d=='CO' ? '' : $c).'X', $d, $c.'F');
					}
				}
				break;
		}
	} else {
		switch($SubRule) {
			case '1':
				$ds=array('OL', 'CO');
				$cs=array('S', 'J');
				if(!$Outdoor) {
					$ds[]='AN';
					$cs[]='M';
					$cs[]='A';
					$cs[]='R';
				}

				foreach($ds as $d) {
					foreach($cs as $c) {
						InsertClassEvent($TourId, 0, 1, $d.'M', $d, $c.'M');
						InsertClassEvent($TourId, 0, 1, $d.'F', $d, $c.'F');

						InsertClassEvent($TourId, 1, 3, $d.'MT', $d, $c.'M');
						InsertClassEvent($TourId, 1, 3, $d.'FT', $d, $c.'F');
						if($Outdoor) {
							InsertClassEvent($TourId, 1, 1, $d.'XT', $d, $c.'M');
							InsertClassEvent($TourId, 2, 1, $d.'XT', $d, $c.'F');
						}
					}
				}
				if($Outdoor) {
					InsertClassEvent($TourId, 0, 1, 'OLNM', 'OL', 'MM');
					InsertClassEvent($TourId, 0, 1, 'OLNM', 'OL', 'AM');
					InsertClassEvent($TourId, 0, 1, 'OLNF', 'OL', 'MF');
					InsertClassEvent($TourId, 0, 1, 'OLNF', 'OL', 'AF');
				}
				break;
			case '2':
				$ds=array('OL', 'CO');
				$cs=array('S');
				if(!$Outdoor) {
					$ds[]='AN';
				}
				foreach($ds as $d) {
					foreach($cs as $c) {
						InsertClassEvent($TourId, 0, 1, $d.'M', $d, $c.'M');
						InsertClassEvent($TourId, 0, 1, $d.'F', $d, $c.'F');

						InsertClassEvent($TourId, 1, 3, $d.'MT', $d, $c.'M');
						InsertClassEvent($TourId, 1, 3, $d.'FT', $d, $c.'F');
						if($Outdoor) {
							InsertClassEvent($TourId, 1, 1, $d.'XT', $d, $c.'M');
							InsertClassEvent($TourId, 2, 1, $d.'XT', $d, $c.'F');
						}
					}
				}
				break;
			case '3':
				if($Outdoor) {
					foreach(array('J','A','R') as $c) {
						foreach(array('OL','CO') as $d) {
							InsertClassEvent($TourId, 0, 1, $d.$c.'M', $d, $c.'M');
							InsertClassEvent($TourId, 0, 1, $d.$c.'F', $d, $c.'F');

							InsertClassEvent($TourId, 1, 3, $d.$c.'M', $d, $c.'M');
							InsertClassEvent($TourId, 1, 3, $d.$c.'F', $d, $c.'F');
							InsertClassEvent($TourId, 1, 1, $d.$c.'X', $d, $c.'M');
							InsertClassEvent($TourId, 2, 1, $d.$c.'X', $d, $c.'F');
						}
					}
				} else {
					foreach(array('J','A','R') as $c) {
						foreach(array('OL','CO','AN') as $d) {
							InsertClassEvent($TourId, 0, 1, $d.'M', $d, $c.'M');
							InsertClassEvent($TourId, 0, 1, $d.'F', $d, $c.'F');

							InsertClassEvent($TourId, 1, 3, $d.'MT', $d, $c.'M');
							InsertClassEvent($TourId, 1, 3, $d.'FT', $d, $c.'F');
						}
					}
				}
				break;
			case '4':
				if($Outdoor) {
					foreach(array('S','M') as $c) {
						foreach(array('OL','CO') as $d) {
							InsertClassEvent($TourId, 0, 1, $d.$c.'M', $d, $c.'M');
							InsertClassEvent($TourId, 0, 1, $d.$c.'F', $d, $c.'F');

							InsertClassEvent($TourId, 1, 3, $d.$c.'M', $d, $c.'M');
							InsertClassEvent($TourId, 1, 3, $d.$c.'F', $d, $c.'F');
							InsertClassEvent($TourId, 1, 1, $d.$c.'X', $d, $c.'M');
							InsertClassEvent($TourId, 2, 1, $d.$c.'X', $d, $c.'F');
						}
					}
				} else {
					foreach(array('S','M') as $c) {
						foreach(array('OL','CO','AN') as $d) {
							InsertClassEvent($TourId, 0, 1, $d.'M', $d, $c.'M');
							InsertClassEvent($TourId, 0, 1, $d.'F', $d, $c.'F');

							InsertClassEvent($TourId, 1, 3, $d.'MT', $d, $c.'M');
							InsertClassEvent($TourId, 1, 3, $d.'FT', $d, $c.'F');
						}
					}
				}
				break;
		}
	}
}

/*

FIELD ONLY THINGS

*/

function CreateStandardFieldEvents($TourId, $SubRule) {
	$Elim1=array(
		'Archers' => ($SubRule==3 ? 16 : 0),
		'Ends' => 12,
		'Arrows' => 3,
		'SO' => 1
	);
	$Elim2=array(
		'Archers' => ($SubRule==4 ? 0 : 8),
		'Ends' => 8,
		'Arrows' => 3,
		'SO' => 1
	);
	$i=1;
	CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'OLM', 'Assoluti Arco Olimpico Maschile',  0, 0, 0, $Elim1, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'OLF', 'Assoluti Arco Olimpico Femminile', 0, 0, 0, $Elim1, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'COM', 'Assoluti Arco Compound Maschile',  0, 0, 0, $Elim1, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'COF', 'Assoluti Arco Compound Femminile', 0, 0, 0, $Elim1, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'ANM', 'Assoluti Arco Nudo Maschile',     0, 0, 0, $Elim1, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'ANF', 'Assoluti Arco Nudo Femminile',   0, 0, 0, $Elim1, $Elim2);
	if($SubRule==2) {
		CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'LBM', 'Assoluti Arco LongBow Maschile',     0, 0, 0, $Elim1, $Elim2);
		CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'LBF', 'Assoluti Arco LongBow Femminile',   0, 0, 0, $Elim1, $Elim2);
	}
	$i=1;
	CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'SQM',  'Squadre Assolute Maschili',0,248,15);
	CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'SQF',  'Squadre Assolute Femminili',0,248,15);
}

function InsertStandardFieldEvents($TourId, $SubRule) {
	InsertClassEvent($TourId, 0, 1, 'OLM', 'OL', 'SM');
	InsertClassEvent($TourId, 0, 1, 'OLM', 'OL', 'JM');
	InsertClassEvent($TourId, 0, 1, 'OLM', 'OL', 'MM');
	InsertClassEvent($TourId, 0, 1, 'OLM', 'OL', 'VM');
	InsertClassEvent($TourId, 0, 1, 'OLF', 'OL', 'SF');
	InsertClassEvent($TourId, 0, 1, 'OLF', 'OL', 'JF');
	InsertClassEvent($TourId, 0, 1, 'OLF', 'OL', 'MF');
	InsertClassEvent($TourId, 0, 1, 'OLF', 'OL', 'VF');
	InsertClassEvent($TourId, 0, 1, 'COM', 'CO', 'SM');
	InsertClassEvent($TourId, 0, 1, 'COM', 'CO', 'MM');
	InsertClassEvent($TourId, 0, 1, 'COM', 'CO', 'JM');
	InsertClassEvent($TourId, 0, 1, 'COM', 'CO', 'VM');
	InsertClassEvent($TourId, 0, 1, 'COF', 'CO', 'JF');
	InsertClassEvent($TourId, 0, 1, 'COF', 'CO', 'SF');
	InsertClassEvent($TourId, 0, 1, 'COF', 'CO', 'MF');
	InsertClassEvent($TourId, 0, 1, 'COF', 'CO', 'VF');
	InsertClassEvent($TourId, 0, 1, 'ANM', 'AN', 'SM');
	InsertClassEvent($TourId, 0, 1, 'ANM', 'AN', 'JM');
	InsertClassEvent($TourId, 0, 1, 'ANM', 'AN', 'MM');
	InsertClassEvent($TourId, 0, 1, 'ANM', 'AN', 'VM');
	InsertClassEvent($TourId, 0, 1, 'ANF', 'AN', 'MF');
	InsertClassEvent($TourId, 0, 1, 'ANF', 'AN', 'SF');
	InsertClassEvent($TourId, 0, 1, 'ANF', 'AN', 'JF');
	InsertClassEvent($TourId, 0, 1, 'ANF', 'AN', 'VF');
	if($SubRule==2) {
		InsertClassEvent($TourId, 0, 1, 'LBM', 'LB', 'SM');
		InsertClassEvent($TourId, 0, 1, 'LBM', 'LB', 'JM');
		InsertClassEvent($TourId, 0, 1, 'LBM', 'LB', 'MM');
		InsertClassEvent($TourId, 0, 1, 'LBM', 'LB', 'VM');
		InsertClassEvent($TourId, 0, 1, 'LBF', 'LB', 'MF');
		InsertClassEvent($TourId, 0, 1, 'LBF', 'LB', 'SF');
		InsertClassEvent($TourId, 0, 1, 'LBF', 'LB', 'JF');
		InsertClassEvent($TourId, 0, 1, 'LBF', 'LB', 'VF');
	}

	InsertClassEvent($TourId, 1, 1, 'SQM', 'OL', 'SM');
	InsertClassEvent($TourId, 1, 1, 'SQM', 'OL', 'JM');
	InsertClassEvent($TourId, 1, 1, 'SQM', 'OL', 'MM');
	InsertClassEvent($TourId, 1, 1, 'SQM', 'OL', 'VM');
	InsertClassEvent($TourId, 2, 1, 'SQM', 'CO', 'SM');
	InsertClassEvent($TourId, 2, 1, 'SQM', 'CO', 'JM');
	InsertClassEvent($TourId, 2, 1, 'SQM', 'CO', 'MM');
	InsertClassEvent($TourId, 2, 1, 'SQM', 'CO', 'VM');
	InsertClassEvent($TourId, 3, 1, 'SQM', 'AN', 'SM');
	InsertClassEvent($TourId, 3, 1, 'SQM', 'AN', 'JM');
	InsertClassEvent($TourId, 3, 1, 'SQM', 'AN', 'MM');
	InsertClassEvent($TourId, 3, 1, 'SQM', 'AN', 'VM');
	InsertClassEvent($TourId, 1, 1, 'SQF', 'OL', 'SF');
	InsertClassEvent($TourId, 1, 1, 'SQF', 'OL', 'JF');
	InsertClassEvent($TourId, 1, 1, 'SQF', 'OL', 'MF');
	InsertClassEvent($TourId, 1, 1, 'SQF', 'OL', 'VF');
	InsertClassEvent($TourId, 2, 1, 'SQF', 'CO', 'SF');
	InsertClassEvent($TourId, 2, 1, 'SQF', 'CO', 'JF');
	InsertClassEvent($TourId, 2, 1, 'SQF', 'CO', 'MF');
	InsertClassEvent($TourId, 2, 1, 'SQF', 'CO', 'VF');
	InsertClassEvent($TourId, 3, 1, 'SQF', 'AN', 'SF');
	InsertClassEvent($TourId, 3, 1, 'SQF', 'AN', 'JF');
	InsertClassEvent($TourId, 3, 1, 'SQF', 'AN', 'MF');
	InsertClassEvent($TourId, 3, 1, 'SQF', 'AN', 'VF');
}


function InsertStandardFieldEliminations($TourId, $SubRule){
	if($SubRule==4) return;
	$cls=array('M', 'F');
	$divs=array('OL', 'CO', 'AN');
	if($SubRule==2) $divs[]='AI';
	foreach($divs as $div) {
		foreach($cls as $cl) {
			if($SubRule==3) {
				for($n=1; $n<=16; $n++) {
					safe_w_SQL("INSERT INTO Eliminations set ElId=0, ElElimPhase=0, ElEventCode='$div$cl', ElTournament=$TourId, ElQualRank=$n");
				}
			}
			for($n=1; $n<=8; $n++) {
				safe_w_SQL("INSERT INTO Eliminations set ElId=0, ElElimPhase=1, ElEventCode='$div$cl', ElTournament=$TourId, ElQualRank=$n");
			}
		}
	}
}

/*

3D ONLY THINGS

*/

function CreateStandard3DEvents($TourId, $SubRule) {
	$Elim1=array(
		'Archers' => 16,
		'Ends' => 12,
		'Arrows' => 1,
		'SO' => 1
	);
	$Elim2=array(
		'Archers' => 8,
		'Ends' => 8,
		'Arrows' => 1,
		'SO' => 1
	);
	$i=1;
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'COSM', 'Arco Compound Senior Maschile',   0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'COSF', 'Arco Compound Senior Femminile',  0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'AISM', 'Arco Istintivo Senior Maschile',  0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'AISF', 'Arco Istintivo Senior Femminile', 0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'ANSM', 'Arco Nudo Senior Maschile',       0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'ANSF', 'Arco Nudo Senior Femminile',      0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LBSM', 'Longbow Senior Maschile',         0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LBSF', 'Longbow Senior Femminile',        0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'COJM', 'Arco Compound Junior Maschile',   0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'COJF', 'Arco Compound Junior Femminile',  0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'AIJM', 'Arco Istintivo Junior Maschile',  0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'AIJF', 'Arco Istintivo Junior Femminile', 0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'ANJM', 'Arco Nudo Junior Maschile',       0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'ANJF', 'Arco Nudo Junior Femminile',      0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LBJM', 'Longbow Junior Maschile',         0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LBJF', 'Longbow Junior Femminile',        0, 0, 0, 0, $Elim2);
	$i=1;
	CreateEvent($TourId, $i++, 1, 0, 4, 8, 8, 3, 3, 4, 3, 3, 'SQSM',  'Squadre Senior Maschili',0,248,15);
	CreateEvent($TourId, $i++, 1, 0, 4, 8, 8, 3, 3, 4, 3, 3, 'SQSF',  'Squadre Senior Femminili',0,248,15);
	CreateEvent($TourId, $i++, 1, 0, 4, 8, 8, 3, 3, 4, 3, 3, 'SQJM',  'Squadre Junior Maschili',0,248,15);
	CreateEvent($TourId, $i++, 1, 0, 4, 8, 8, 3, 3, 4, 3, 3, 'SQJF',  'Squadre Junior Femminili',0,248,15);
}

function InsertStandard3DEvents($TourId, $SubRule) {
	InsertClassEvent($TourId, 0, 1, 'COSM', 'CO', 'SM');
	InsertClassEvent($TourId, 0, 1, 'COJM', 'CO', 'JM');
	InsertClassEvent($TourId, 0, 1, 'COSF', 'CO', 'SF');
	InsertClassEvent($TourId, 0, 1, 'COJF', 'CO', 'JF');
	InsertClassEvent($TourId, 0, 1, 'AISM', 'AI', 'SM');
	InsertClassEvent($TourId, 0, 1, 'AIJM', 'AI', 'JM');
	InsertClassEvent($TourId, 0, 1, 'AISF', 'AI', 'SF');
	InsertClassEvent($TourId, 0, 1, 'AIJF', 'AI', 'JF');
	InsertClassEvent($TourId, 0, 1, 'ANSM', 'AN', 'SM');
	InsertClassEvent($TourId, 0, 1, 'ANJM', 'AN', 'JM');
	InsertClassEvent($TourId, 0, 1, 'ANSF', 'AN', 'SF');
	InsertClassEvent($TourId, 0, 1, 'ANJF', 'AN', 'JF');
	InsertClassEvent($TourId, 0, 1, 'LBSM', 'LB', 'SM');
	InsertClassEvent($TourId, 0, 1, 'LBJM', 'LB', 'JM');
	InsertClassEvent($TourId, 0, 1, 'LBSF', 'LB', 'SF');
	InsertClassEvent($TourId, 0, 1, 'LBJF', 'LB', 'JF');

	InsertClassEvent($TourId, 1, 1, 'SQSM',  'CO',  'SM');
	InsertClassEvent($TourId, 2, 1, 'SQSM',  'LB',  'SM');
	InsertClassEvent($TourId, 3, 1, 'SQSM',  'AN',  'SM');
	InsertClassEvent($TourId, 3, 1, 'SQSM',  'AI',  'SM');
	InsertClassEvent($TourId, 1, 1, 'SQJM',  'CO',  'JM');
	InsertClassEvent($TourId, 2, 1, 'SQJM',  'LB',  'JM');
	InsertClassEvent($TourId, 3, 1, 'SQJM',  'AN',  'JM');
	InsertClassEvent($TourId, 3, 1, 'SQJM',  'AI',  'JM');
	InsertClassEvent($TourId, 1, 1, 'SQSF',  'CO',  'SF');
	InsertClassEvent($TourId, 2, 1, 'SQSF',  'LB',  'SF');
	InsertClassEvent($TourId, 3, 1, 'SQSF',  'AN',  'SF');
	InsertClassEvent($TourId, 3, 1, 'SQSF',  'AI',  'SF');
	InsertClassEvent($TourId, 1, 1, 'SQJF',  'CO',  'JF');
	InsertClassEvent($TourId, 2, 1, 'SQJF',  'LB',  'JF');
	InsertClassEvent($TourId, 3, 1, 'SQJF',  'AN',  'JF');
	InsertClassEvent($TourId, 3, 1, 'SQJF',  'AI',  'JF');
}

function InsertStandard3DEliminations($TourId, $SubRule){
	$cls=array('SM', 'JM', 'SF', 'JF');
	foreach(array('CO', 'LB', 'AN', 'AI') as $div) {
		foreach($cls as $cl) {
			for($n=1; $n<=8; $n++) {
				safe_w_SQL("INSERT INTO Eliminations set ElId=0, ElElimPhase=1, ElEventCode='$div$cl', ElTournament=$TourId, ElQualRank=$n");
			}
		}
	}
}

/*

GdG THINGS

*/


function CreateStandardStudClasses($TourId, $TourType) {
	$i=1;
	if($TourType==33) {
		CreateClass($TourId, $i++, 9, 14, 0, 'M', 'M', 'Maschi');
		CreateClass($TourId, $i++, 9, 14, 1, 'F', 'F', 'Femmine');
	} else {
		CreateClass($TourId, $i++, 13, 21, 0, 'CM', 'CM', 'Cadetti Maschile');
		CreateClass($TourId, $i++, 13, 21, 1, 'CF', 'CF', 'Cadette Femminile');
		CreateClass($TourId, $i++, 13, 21, 0, 'AM', 'AM', 'Allievi Maschile');
		CreateClass($TourId, $i++, 13, 21, 1, 'AF', 'AF', 'Allieve Femminile');
	}
}

function CreateStandardGdGClasses($TourId, $SubRule, $Field='') {
	$i=1;
//	CreateClass($TourId, $i++, 14, 14, 0, 'M3', 'M3', 'Ragazzi Terza Media');
//	CreateClass($TourId, $i++, 14, 14, 1, 'F3', 'F3', 'Ragazze Terza Media');
	CreateClass($TourId, $i++, 13, 13, 0, 'M2', 'M2', 'Ragazzi Seconda Media');
	CreateClass($TourId, $i++, 13, 13, 1, 'F2', 'F2', 'Ragazze Seconda Media');
	CreateClass($TourId, $i++, 12, 12, 0, 'M1', 'M1', 'Ragazzi Prima Media');
	CreateClass($TourId, $i++, 12, 12, 1, 'F1', 'F1', 'Ragazze Prima Media');
	CreateClass($TourId, $i++, 9, 11, 0, 'GM', 'GM', 'Giovanissimi Maschile');
	CreateClass($TourId, $i++, 9, 11, 1, 'GF', 'GF', 'Giovanissimi Femminile');
}

function CreateStandardSperimClasses($TourId) {
	$i=1;
	CreateClass($TourId, $i++, 0, 1, 0, 'M1', 'M1,M2', 'Esordienti 1 Maschile');
	CreateClass($TourId, $i++, 0, 1, 1, 'F1', 'F1,F2', 'Esordienti 1 Femminile');
	CreateClass($TourId, $i++, 2, 2, 0, 'M2', 'M2', 'Esordienti 2 Maschile');
	CreateClass($TourId, $i++, 2, 2, 1, 'F2', 'F2', 'Esordienti 2 Femminile');
	CreateClass($TourId, $i++, 3, 3, -1, 'P', 'P', 'Pulcini');
}
?>