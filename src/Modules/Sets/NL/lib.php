<?php
/*

STANDARD THINGS

*/

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'NED';
if(empty($SubRule)) $SubRule='1';

function CreateStandardDivisions($TourId, $Type=3, $SubRule=1) {
	$i=1;
	if($Type!=43) {
        CreateDivision($TourId, $i++, 'C', 'Compound');
        CreateDivision($TourId, $i++, 'R', 'Recurve');
        if ($Type != 1 and $Type != 2) {
            CreateDivision($TourId, $i++, 'B', 'Barebow');
        }
        if ($Type == '6' OR $Type == '7') {
            CreateDivision($TourId, $i++, 'LB', 'Longbow');
            CreateDivision($TourId, $i++, 'IB', 'Instinctive Bow');
        }
    } else {
        CreateDivision($TourId, $i++, 'T', 'Traditioneel');
    }
}

function CreateStandardClasses($TourId, $TourType=3, $SubRule=1) {
	$i=1;
	switch ($TourType) {
        case 1:
        case 2:
            CreateClass($TourId, $i++, 0, 17, 0, 'CH', 'CH', 'Heren Cadetten');
            CreateClass($TourId, $i++, 0, 17, 1, 'CD', 'CD', 'Dames Cadetten');
            CreateClass($TourId, $i++, 18, 20, 0, 'JH', 'CH,JH', 'Heren Junioren');
            CreateClass($TourId, $i++, 18, 20, 1, 'JD', 'CD,JD', 'Dames Junioren');
            CreateClass($TourId, $i++, 21, 49, 0,  'H', 'CH,JH,H,MH', 'Heren');
            CreateClass($TourId, $i++, 21, 49, 1,  'D', 'CD,JD,D,MD', 'Dames');
            CreateClass($TourId, $i++, 50, 99, 0, 'MH', 'MH', 'Heren Masters');
            CreateClass($TourId, $i++, 50, 99, 1, 'MD', 'MD', 'Dames Masters');
            break;
        case 3:
        case 37:
            switch ($SubRule) {
                case 1:
                    CreateClass($TourId, $i++, 0,  13, 0, 'AH', 'AH', 'Heren Aspiranten');
                    CreateClass($TourId, $i++, 0,  13, 1, 'AD', 'AD', 'Dames Aspiranten');
                    CreateClass($TourId, $i++, 14, 17, 0, 'CH', 'AH,CH', 'Heren Cadetten');
                    CreateClass($TourId, $i++, 14, 17, 1, 'CD', 'AD,CD', 'Dames Cadetten');
                    CreateClass($TourId, $i++, 18, 20, 0, 'JH', 'AH,CH,JH', 'Heren Junioren');
                    CreateClass($TourId, $i++, 18, 20, 1, 'JD', 'AD,CD,JD', 'Dames Junioren');
                    CreateClass($TourId, $i++, 21, 49, 0,  'H', 'AH,CH,JH,H,MH', 'Heren');
                    CreateClass($TourId, $i++, 21, 49, 1,  'D', 'AD,CD,JD,D,MD', 'Dames');
                    CreateClass($TourId, $i++, 50, 99, 0, 'MH', 'MH', 'Heren Masters');
                    CreateClass($TourId, $i++, 50, 99, 1, 'MD', 'MD', 'Dames Masters');
                    break;
                case 2:
                case 3:
                    CreateClass($TourId, $i++, 0, 100, 0, 'H', 'H', 'Heren');
                    CreateClass($TourId, $i++, 0, 100, 1, 'D', 'D', 'Dames');

            }
            break;
        case 7:
            $SubRule=3;
        case 6:
        case 42:
            switch ($SubRule) {
                case 1:
                case 2:
                    $i=1;
                    CreateClass($TourId, $i++, 1, 10, 0, 'UJ',  'UJ', 'Aspiranten Jongens t/m 10 jaar');
                    CreateClass($TourId, $i++, 1, 10, 1, 'UM',  'UM', 'Aspiranten Meisjes t/m 10 jaar');
                    CreateClass($TourId, $i++, 1, 10, 0, 'AJ',  'AJ', 'Aspiranten Jongens 11 en 12 jaar');
                    CreateClass($TourId, $i++, 1, 10, 1, 'AM',  'AM', 'Aspiranten Meisjes 11 en 12 jaar');
                    CreateClass($TourId, $i++, 13, 17, -1, 'C1',  'C1', 'Cadetten Klasse 1','1','R,C');
                    CreateClass($TourId, $i++, 13, 17, -1, 'C2',  'C2', 'Cadetten Klasse 2','1','R,C');
                    CreateClass($TourId, $i++, 18, 20, -1, 'J1',  'J1', 'Junioren Klasse 1','1','R,C');
                    CreateClass($TourId, $i++, 18, 20, -1, 'J2',  'J2', 'Junioren Klasse 2','1','R,C');
                    CreateClass($TourId, $i++, 18, 20, -1, 'J',  'J', 'Jeugd Klasse 1','1','B,LB,IB');
                    CreateClass($TourId, $i++, 21, 99, -1, 'S1',  'S1', 'Senioren Klasse 1','1','R,C,B,LB,IB');
                    CreateClass($TourId, $i++, 21, 99, -1, 'S2',  'S2', 'Senioren Klasse 2','1','R,C,B,LB,IB');
                    CreateClass($TourId, $i++, 21, 99, -1, 'S3',  'S3', 'Senioren Klasse 3','1','R');
                    CreateClass($TourId, $i++, 21, 99, -1, 'S4',  'S4', 'Senioren Klasse 4','1','R');
                    CreateClass($TourId, $i++, 21, 99, -1, 'S5',  'S5', 'Senioren Klasse 5','1','R');
                    CreateClass($TourId, $i++, 21, 99, -1, 'S6',  'S6', 'Senioren Klasse 6','1','R');
                    break;
                case 3:
                    CreateClass($TourId, $i++, 1, 17, 0, 'CH', 'CH', 'Heren Cadetten');
                    CreateClass($TourId, $i++, 1, 17, 1, 'CD', 'CD', 'Dames Cadetten');
                    CreateClass($TourId, $i++, 18, 20, 0, 'JH', 'CH,JH', 'Heren Junioren');
                    CreateClass($TourId, $i++, 18, 20, 1, 'JD', 'CD,JD', 'Dames Junioren');
                    CreateClass($TourId, $i++, 21, 49, 0, 'H',  'CH,JH,H,MH', 'Heren');
                    CreateClass($TourId, $i++, 21, 49, 1, 'D',  'CD,JD,D,MD', 'Dames');
                    CreateClass($TourId, $i++, 50, 99, 0, 'MH', 'MH', 'Heren Masters');
                    CreateClass($TourId, $i++, 50, 99, 1, 'MD', 'MD', 'Dames Masters');
                    break;
                case 4:
                    CreateClass($TourId, $i++, 0, 100, 0, 'H', 'H', 'Heren');
                    CreateClass($TourId, $i++, 0, 100, 1, 'D', 'D', 'Dames');
            }
            break;
        case 41:
            CreateClass($TourId, $i++, 1, 11, 0, 'UH',  'UH', 'Heren Aspiranten t/m 11 jaar');
            CreateClass($TourId, $i++, 1, 11, 1, 'UD',  'UD', 'Dames Aspiranten t/m 10 jaar');
            CreateClass($TourId, $i++, 12, 13, 0, 'AH',  'UH,AH', 'Heren Aspiranten 12 en 13 jaar');
            CreateClass($TourId, $i++, 12, 13, 1, 'AD',  'UD,AD', 'Dames Aspiranten 12 en 13 jaar');
            CreateClass($TourId, $i++, 14, 17, 0, 'CH', 'UH,AH,CH', 'Heren Cadetten');
            CreateClass($TourId, $i++, 14, 17, 1, 'CD', 'UD,AD,CD', 'Dames Cadetten');
            CreateClass($TourId, $i++, 18, 20, 0, 'JH', 'UH,AH,CH,JH', 'Heren Junioren');
            CreateClass($TourId, $i++, 18, 20, 1, 'JD', 'UD,AD,CD,JD', 'Dames Junioren');
            break;
        case 43:
            CreateClass($TourId, $i++, 0, 100, 0, 'H', 'H', 'Heren');
            CreateClass($TourId, $i++, 0, 100, 1, 'D', 'D', 'Dames');
    }

}

function CreateStandardSubClasses($TourId, $TourType=3, $SubRule=1) {
    if(($TourType==6 or $TourType==42) and $SubRule<=2) {
        CreateSubClass($TourId, 1, 'E', 'In klasse eren teams');
        CreateSubClass($TourId, 2, 'A', 'In klasse A teams');
        CreateSubClass($TourId, 3, 'B', 'In klasse B teams');
        CreateSubClass($TourId, 4, 'C', 'In klasse C teams');
        CreateSubClass($TourId, 5, 'D', 'In klasse D teams');
    }
}

function CreateStandardEvents($TourId, $SubRule, $Outdoor=true) {
    $TargetR=($Outdoor ? TGT_OUT_FULL : TGT_IND_6_big10);
	$TargetC=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_6_small10);
    $TargetB=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeR=($Outdoor ? 122 : 40);
	$TargetSizeC=($Outdoor ? 80 : 40);
    $TargetSizeB=($Outdoor ? 122 : 40);
	$DistanceR=($Outdoor ? 70 : 18);
    $DistanceR_mc=($Outdoor ? 60 : 18);
	$DistanceC=($Outdoor ? 50 : 18);
    $DistanceB=($Outdoor ? 50 : 18);

    $i=1;
	if(($Outdoor AND $SubRule==1) OR (!$Outdoor AND $SubRule==3)) {
        CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'RH', 'Heren Recurve', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'RM', 'RM', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'RD', 'Dames Recurve', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'RW', 'RW', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'RHJ', 'Heren Junioren Recurve', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'RJM', 'RJM', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'RDJ', 'Dames Junioren Recurve', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'RKW', 'RJW', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'RHC', 'Heren Cadetten Recurve', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'RCM', 'RCM', $TargetSizeR, $DistanceR_mc);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'RDC', 'Dames Cadetten Recurve', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'RCW', 'RCW', $TargetSizeR, $DistanceR_mc);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'RHM', 'Heren Masters Recurve', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'RMM', 'RMM', $TargetSizeR, $DistanceR_mc);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'RDM', 'Dames Masters Recurve', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'RMW', 'RMW', $TargetSizeR, $DistanceR_mc);

        CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'CH', 'Heren Compound ', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'CM', 'CM', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'CD', 'Dames Compound', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'CW', 'CW', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'CHJ', 'Heren Junioren Compound ', 0, FINAL_NO_ELIM, FINAL_NO_ELIM, 0, 0, 'CJM', 'CJM', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'CDJ', 'Dames Junioren Compound', 0, FINAL_NO_ELIM, FINAL_NO_ELIM, 0, 0, 'CJW', 'CJW', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'CHC', 'Heren Cadetten Compound ', 0, FINAL_NO_ELIM, FINAL_NO_ELIM, 0, 0, 'CCM', 'CCM', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'CDC', 'Dames Cadetten Compound', 0, FINAL_NO_ELIM, FINAL_NO_ELIM, 0, 0, 'CWC', 'CCW', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'CHM', 'Heren Masters Compound ', 0, FINAL_NO_ELIM, FINAL_NO_ELIM, 0, 0, 'CMM', 'MCM', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'CDM', 'Dames Masters Compound', 0, FINAL_NO_ELIM, FINAL_NO_ELIM, 0, 0, 'CMW', 'CMW', $TargetSizeC, $DistanceC);

        CreateEvent($TourId, $i++, 0, 0, 16, $TargetB, 5, 3, 1, 5, 3, 1, 'BH', 'Heren Barebow', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'BM', 'BM', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetB, 5, 3, 1, 5, 3, 1, 'BD', 'Dames Barebow', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'BW', 'BW', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, 16, $TargetB, 5, 3, 1, 5, 3, 1, 'BHJ', 'Heren Junioren Barebow', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'BJM', 'BJM', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetB, 5, 3, 1, 5, 3, 1, 'BDJ', 'Dames Junioren Barebow', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'BJW', 'BJW', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, 16, $TargetB, 5, 3, 1, 5, 3, 1, 'BHC', 'Heren Cadetten Barebow', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'BCM', 'BCM', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetB, 5, 3, 1, 5, 3, 1, 'BDC', 'Dames Cadetten Barebow', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'BCW', 'BCW', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, 16, $TargetB, 5, 3, 1, 5, 3, 1, 'BHM', 'Heren Masters Barebow', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'BMM', 'BMM', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetB, 5, 3, 1, 5, 3, 1, 'BDM', 'Dames Masters Barebow', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'BMW', 'BMW', $TargetSizeB, $DistanceB);

        $i = 1;

        if ($Outdoor) {
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RX', 'Recurve Mixed Team', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RX', 'RX', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RXJ', 'Recurve Junioren Mixed Team', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RJX', 'RJX', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RXC', 'Recurve Cadetten Mixed Team', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RCX', 'RCX', $TargetSizeR, $DistanceR_mc);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RXM', 'Recurve Masters Mixed Team', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RMX', 'RMX', $TargetSizeR, $DistanceR_mc);
        }
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'RH', 'Heren Recurve', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RM', 'RM', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'RD', 'Dames Recurve', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RW', 'RW', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'RHJ', 'Heren Junioren Recurve', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RJM', 'RJM', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'RDJ', 'Dames Junioren Recurve', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RKW', 'RJW', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'RHC', 'Heren Cadetten Recurve', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RCM', 'RCM', $TargetSizeR, $DistanceR_mc);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'RDC', 'Dames Cadetten Recurve', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RCW', 'RCW', $TargetSizeR, $DistanceR_mc);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'RHM', 'Heren Masters Recurve', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RMM', 'RMM', $TargetSizeR, $DistanceR_mc);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'RDM', 'Dames Masters Recurve', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RMM', 'RMM', $TargetSizeR, $DistanceR_mc);

        if ($Outdoor) {
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'CX', 'Compound Mixed Team', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CX', 'CX', $TargetSizeC, $DistanceC);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'CXJ', 'Compound Junioren Mixed Team', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CJX', 'CJX', $TargetSizeC, $DistanceC);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'CXC', 'Compound Cadetten Mixed Team', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CCX', 'CCX', $TargetSizeC, $DistanceC);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'CXM', 'Compound Masters Mixed Team', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CMX', 'CMX', $TargetSizeC, $DistanceC);
        }
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CH', 'Heren Compound', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CM', 'CM', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CD', 'Dames Compound', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CW', 'CW', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CHJ', 'Heren Junioren Compound', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CJM', 'CJM', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CDJ', 'Dames Junioren Compound', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CKW', 'CJW', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CHC', 'Heren Cadetten Compound', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CCM', 'CCM', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CDC', 'Dames Cadetten Compound', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CCW', 'CCW', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CHM', 'Heren Masters Compound', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CMM', 'CMM', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CDM', 'Dames Masters Compound', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CMM', 'CMM', $TargetSizeC, $DistanceC);

        if ($Outdoor) {
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetB, 4, 4, 2, 4, 4, 2, 'BX', 'Barebow Mixed Team', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BX', 'BX', $TargetSizeB, $DistanceB);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetB, 4, 4, 2, 4, 4, 2, 'BXJ', 'Barebow Junioren Mixed Team', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BJX', 'BJX', $TargetSizeB, $DistanceB);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetB, 4, 4, 2, 4, 4, 2, 'BXC', 'Barebow Cadetten Mixed Team', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BCX', 'BCX', $TargetSizeB, $DistanceB);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetB, 4, 4, 2, 4, 4, 2, 'BXM', 'Barebow Masters Mixed Team', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BMX', 'BMX', $TargetSizeB, $DistanceB);
        }
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'BH', 'Heren Barebow', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BM', 'BM', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'BD', 'Dames Barebow', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BW', 'BW', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'BHJ', 'Heren Junioren Barebow', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BJM', 'BJM', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'BDJ', 'Dames Junioren Barebow', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BKW', 'BJW', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'BHC', 'Heren Cadetten Barebow', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BCM', 'BCM', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'BDC', 'Dames Cadetten Barebow', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BCW', 'BCW', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'BHM', 'Heren Masters Barebow', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BMM', 'BMM', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'BDM', 'Dames Masters Barebow', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BMM', 'BMM', $TargetSizeB, $DistanceB);
    } else if(($Outdoor) OR (!$Outdoor AND $SubRule==4)) {
        CreateEvent($TourId, $i++, 0, 0,16, $TargetR, 5, 3, 1, 5, 3, 1, 'RH',  'Heren Recurve', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'RM', 'RM', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'RD',  'Dames Recurve', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'RW', 'RW', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'CH',  'Heren Compound ', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'CM', 'CM', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'CD',  'Dames Compound', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'CW', 'CW', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0,16, $TargetB, 5, 3, 1, 5, 3, 1, 'BH',  'Heren Barebow', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'BM', 'BM', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetB, 5, 3, 1, 5, 3, 1, 'BD',  'Dames Barebow', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'BW', 'BW', $TargetSizeB, $DistanceB);
        $i=1;
        if($Outdoor) {
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RX', 'Recurve Mixed Team', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RX', 'RX', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'CX', 'Compound Mixed Team', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CX', 'CX', $TargetSizeC, $DistanceC);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'BX', 'Barebow Mixed Team', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BX', 'BX', $TargetSizeB, $DistanceB);
        }
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'RH',  'Heren Recurve', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RM', 'RM', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'RD',  'Dames Recurve', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RW', 'RW', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CH',  'Heren Compound', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CM', 'CM', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CD',  'Dames Compound', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CW', 'CW', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'BH',  'Heren Barebow', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BM', 'BM', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'BD',  'Dames Barebow', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BW', 'BW', $TargetSizeB, $DistanceB);
	}
}

function InsertStandardEvents($TourId, $SubRule, $Outdoor=true) {
    if(($Outdoor AND $SubRule==1) OR (!$Outdoor AND $SubRule==3)) {
        foreach (array('R','C','B') as $vDiv) {
            foreach(array('D'=>'D','DJ'=>'JD','DC'=>'CD','DM'=>'MD') as $kCl=>$vCl) {
                InsertClassEvent($TourId, 0, 1, $vDiv.$kCl, $vDiv,  $vCl);
                InsertClassEvent($TourId, 1, 3, $vDiv.$kCl, $vDiv,  $vCl);
                InsertClassEvent($TourId, 1, 1, $vDiv.'X'.(strlen($vCl)==2 ? substr($vCl,-2,1):''), $vDiv,  $vCl);
            }
            foreach(array('H'=>'H','HJ'=>'JH','HC'=>'CH','HM'=>'MH') as $kCl=>$vCl) {
                InsertClassEvent($TourId, 0, 1, $vDiv.$kCl, $vDiv,  $vCl);
                InsertClassEvent($TourId, 1, 3, $vDiv.$kCl, $vDiv,  $vCl);
                InsertClassEvent($TourId, 2, 1, $vDiv.'X'.(strlen($vCl)==2 ? substr($vCl,-2,1):''), $vDiv,  $vCl);
            }
        }
    } else if(($Outdoor) OR (!$Outdoor AND $SubRule==4)) {
        foreach (array('R','C','B') as $vDiv) {
            foreach(array('D','H') as $kCl=>$vCl) {
                InsertClassEvent($TourId, 0, 1, $vDiv.$vCl, $vDiv,  $vCl);
                InsertClassEvent($TourId, 1, 3, $vDiv.$vCl, $vDiv,  $vCl);
                InsertClassEvent($TourId, ($kCl+1), 1, $vDiv.'X', $vDiv,  $vCl);
            }
        }
	}
}