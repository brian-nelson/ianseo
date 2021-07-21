<?php

/*

STANDARD DEFINITIONS (Target Tournaments)

*/

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'UK';
if(empty($SubRule)) $SubRule='1';

function CreateStandardDivisions($TourId, $Type, $SubRule) {
	$i=1;
    $optionDivs = array(
        'R'=>'Recurve',
        'C'=>'Compound',
        'B'=>'Barebow',
        'L'=>'Longbow',
    );
    if ($Type == 21) {
        $optionDivs = array('C' => 'Compound','R' => 'Recurve');
    } else if (($Type!=40) && ($SubRule == 1)) {
        $optionDivs = array('R'=>'Recurve/Barebow','C'=>'Compound','L'=>'Longbow');
    }

    foreach ($optionDivs as $k => $v){
        CreateDivision($TourId, $i++, $k, $v);
    }

}

function CreateStandardClasses($TourId, $SubRule,$TourType) {
    $i=1;
	switch($TourType) {
        case 40:
            CreateClass($TourId, $i++, 19, 110, 0, 'M', 'M4,M3,M2,M1,M', 'Men');
            CreateClass($TourId, $i++, 19, 110, 1, 'W', 'W5,W4,W3,W2,W', 'Women');
            CreateClass($TourId, $i++, 17, 18, 0, 'M1', 'M4,M3,M2,M1,M', 'Junior Men U18');
            CreateClass($TourId, $i++, 17, 18, 1, 'W2', 'W5,W4,W3,W2,W', 'Junior Women U18');
            CreateClass($TourId, $i++, 15, 16, 0, 'M2', 'M4,M3,M2,M1,M', 'Junior Men U16');
            CreateClass($TourId, $i++, 15, 16, 1, 'W3', 'W5,W4,W3,W2,W', 'Junior Women U16');
            CreateClass($TourId, $i++, 13, 14, 0, 'M3', 'M4,M3,M2,M1,M', 'Junior Men U14');
            CreateClass($TourId, $i++, 13, 14, 1, 'W4', 'W5,W4,W3,W2,W', 'Junior Women U14');
            CreateClass($TourId, $i++, 1, 12, 0, 'M4', 'M4,M3,M2,M1,M', 'Junior Men U12');
            CreateClass($TourId, $i++, 1, 12, 1, 'W5', 'W5,W4,W3,W2,W', 'Junior Women U12');
            break;
        default:
            switch ($SubRule) {
                case '1': // National Championships - F2F
                    CreateClass($TourId, $i++, 1, 99, 0, 'M', 'M', 'Gentlemen');
                    CreateClass($TourId, $i++, 1, 99, 1, 'W', 'W', 'Ladies');
                    break;
                case '2': // Junior National Championships
                    CreateClass($TourId, $i++, 17, 18, 0, 'S1', 'S1', 'Section 1 - Junior Gentlemen U18');
                    CreateClass($TourId, $i++, 17, 18, 1, 'S2', 'S2', 'Section 2 - Junior Ladies U18');
                    CreateClass($TourId, $i++, 15, 16, 0, 'S3', 'S3', 'Section 3 - Junior Gentlemen U16');
                    CreateClass($TourId, $i++, 15, 16, 1, 'S4', 'S4', 'Section 4 - Junior Ladies U16');
                    CreateClass($TourId, $i++, 13, 14, 0, 'S5', 'S5', 'Section 5 - Junior Gentlemen U14');
                    CreateClass($TourId, $i++, 13, 14, 1, 'S6', 'S6', 'Section 6 - Junior Ladies U14');
                    CreateClass($TourId, $i++, 1, 12, 0, 'S7', 'S7', 'Section 7 - Junior Gentlemen U12');
                    CreateClass($TourId, $i++, 1, 12, 1, 'S8', 'S8', 'Section 8 - Junior Ladies U12');
                    break;
                case 3:
                    CreateClass($TourId, $i++, 50, 100, 0, 'MM', 'MM,M', 'Master Men');
                    CreateClass($TourId, $i++, 50, 100, 1, 'MW', 'MW,W', 'Master Women');
                    CreateClass($TourId, $i++, 21, 49, 0, 'M', 'M4,M3,M2,M1,M,MM', 'Men');
                    CreateClass($TourId, $i++, 21, 49, 1, 'W', 'W5,W4,W3,W2,W,MW', 'Women');
                    CreateClass($TourId, $i++, 1, 17, 0, 'M1', 'M4,M3,M2,M1,M', 'Junior Men U18');
                    CreateClass($TourId, $i++, 1, 17, 1, 'W2', 'W5,W4,W3,W2,W', 'Junior Women U18');
                    CreateClass($TourId, $i++, 15, 16, 0, 'M2', 'M4,M3,M2,M1,M', 'Junior Men U16');
                    CreateClass($TourId, $i++, 15, 16, 1, 'W3', 'W5,W4,W3,W2,W', 'Junior Women U16');
                    CreateClass($TourId, $i++, 13, 14, 0, 'M3', 'M4,M3,M2,M1,M', 'Junior Men U14');
                    CreateClass($TourId, $i++, 13, 14, 1, 'W4', 'W5,W4,W3,W2,W', 'Junior Women U14');
                    CreateClass($TourId, $i++, 1, 12, 0, 'M4', 'M4,M3,M2,M1,M', 'Junior Men U12');
                    CreateClass($TourId, $i++, 1, 12, 1, 'W5', 'W5,W4,W3,W2,W', 'Junior Women U12');
                    break;

            }
            break;
	}

}


function CreateStandardEvents($TourId, $SubRule, $Outdoor=true,$TourType) {
	$TargetR=($Outdoor?5:2);
	$TargetC=($Outdoor?9:4);
	$SetC=($Outdoor?0:1);
	switch($TourType) {
        case 40:
            switch ($SubRule) {
                case 1:
                    $M = "York";
                    $W = "Hereford";
                    $B1 = "Bristol 1";
                    $B2 = "Bristol 2";
                    $B3 = "Bristol 3";
                    $B4 = "Bristol 4";
                    $B5 = "Bristol 5";
                    break;
                case 2:
                    $M = "St George";
                    $W = "Albion";
                    $B1 = "Albion";
                    $B2 = "Windsor";
                    $B3 = "Short Windsor";
                    $B4 = "Junior Windsor";
                    $B5 = "Short Junior Windsor";
                    break;
                case 3:
                    $M = "American";
                    $W = $M;
                    $B1 = $M;
                    $B2 = $M;
                    $B3 = $M;
                    $B4 = $M;
                    $B5 = $M;
                    break;
                case 4:
                    $M = "New National";
                    $W = "Long National";
                    $B1 = "Long National";
                    $B2 = "National";
                    $B3 = "Short National";
                    $B4 = "Junior National";
                    $B5 = "Short Junior National";
                    break;
                case 5:
                    $M = "New Western";
                    $W = "Long Western";
                    $B1 = "Long Western";
                    $B2 = "Western";
                    $B3 = "Short Western";
                    $B4 = "Junior Western";
                    $B5 = "Short Junior Western";
                    break;
                case 6:
                    $M = "New Warwick";
                    $W = "Long Warwick";
                    $B1 = "Long Warwick";
                    $B2 = "Warwick";
                    $B3 = "Short Warwick";
                    $B4 = "Junior Warwick";
                    $B5 = "Short Junior Warwick";
                    break;
                case 7:
                    $M = "St Nicholas";
                    $W = $M;
                    $B1 = $M;
                    $B2 = $M;
                    $B3 = $M;
                    $B4 = $M;
                    $B5 = $M;
                    break;
                case 8:
                    $M = "ontarget";
                    $W = $M;
                    $B1 = $M;
                    $B2 = $M;
                    $B3 = $M;
                    $B4 = $M;
                    $B5 = $M;
                    break;
                case 9:
                    $M = "Short Metric";
                    $W = "Short Metric";
                    $B1 = "Short Metric 1";
                    $B2 = "Short Metric 2";
                    $B3 = "Short Metric 3";
                    $B4 = "Short Metric 4";
                    $B5 = "Short Metric 5";
                    break;
                case 10:
                    $M = "Long Metric";
                    $W = "Long Metric";
                    $B1 = "Long Metric 1";
                    $B2 = "Long Metric 2";
                    $B3 = "Long Metric 3";
                    $B4 = "Long Metric 4";
                    $B5 = "Long Metric 5";
                    break;
                case 11:
                    $M = "Worcester";
                    $W = $M;
                    $B1 = $M;
                    $B2 = $M;
                    $B3 = $M;
                    $B4 = $M;
                    $B5 = $M;
                    break;
                case 12:
                    $M = "Bray 1";
                    $W = $M;
                    $B1 = $M;
                    $B2 = $M;
                    $B3 = $M;
                    $B4 = $M;
                    $B5 = $M;
                    break;
                case 13:
                    $M = "Bray 2";
                    $W = $M;
                    $B1 = $M;
                    $B2 = $M;
                    $B3 = $M;
                    $B4 = $M;
                    $B5 = $M;
                    break;
                case 14:
                    $M = "Stafford";
                    $W = $M;
                    $B1 = $M;
                    $B2 = $M;
                    $B3 = $M;
                    $B4 = $M;
                    $B5 = $M;
                    break;
                case 15:
                    $M = "Portsmouth";
                    $W = $M;
                    $B1 = $M;
                    $B2 = $M;
                    $B3 = $M;
                    $B4 = $M;
                    $B5 = $M;
                    break;
            }
            $i = 1;
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RM', $M . ' Recurve Men', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CM', $M . ' Compound Men', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LM', $M . ' Longbow Men', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BM', $M . ' Barebow Men', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RW', $W . ' Recurve Women', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CW', $W . ' Compound Women', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LW', $W . ' Longbow Women', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BW', $W . ' Barebow Women', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RM1', $B1 . ' Gentlemen Recurve Under 18', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CM1', $B1 . ' Gentlemen Compound Under 18', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LM1', $B1 . ' Gentlemen Longbow Under 18', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BM1', $B1 . ' Gentlemen Barebow Under 18', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RW2', $B2 . ' Ladies Recurve Under 18', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CW2', $B2 . ' Ladies Compound Under 18', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LW2', $B2 . ' Ladies Longbow Under 18', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BW2', $B2 . ' Ladies Barebow Under 18', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RM2', $B2 . ' Gentlemen Recurve Under 16', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CM2', $B2 . ' Gentlemen Compound Under 16', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LM2', $B2 . ' Gentlemen Longbow Under 16', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BM2', $B2 . ' Gentlemen Barebow Under 16', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RW3', $B3 . ' Ladies Recurve Under 16', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CW3', $B3 . ' Ladies Compound Under 16', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LW3', $B3 . ' Ladies Longbow Under 16', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BW3', $B3 . ' Ladies Barebow Under 16', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RM3', $B3 . ' Gentlemen Recurve Under 14', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CM3', $B3 . ' Gentlemen Compound Under 14', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LM3', $B3 . ' Gentlemen Longbow Under 14', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BM3', $B3 . ' Gentlemen Barebow Under 14', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RW4', $B4 . ' Ladies Recurve Under 14', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CW4', $B4 . ' Ladies Compound Under 14', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LW4', $B4 . ' Ladies Longbow Under 14', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BW4', $B4 . ' Ladies Barebow Under 14', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RM4', $B4 . ' Gentlemen Recurve Under 12', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CM4', $B4 . ' Gentlemen Compound Under 12', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LM4', $B4 . ' Gentlemen Longbow Under 12', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BM4', $B4 . ' Gentlemen Barebow Under 12', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RW5', $B5 . ' Ladies Recurve Under 12', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CW5', $B5 . ' Ladies Compound Under 12', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LW5', $B5 . ' Ladies Longbow Under 12', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BW5', $B5 . ' Ladies Barebow Under 12', 1, 240);
            break;
        default:
            switch ($SubRule) {
                case 1:// National Championships
                    $i = 1;
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'RM', 'Gentlemen Recurve', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'RW', 'Ladies Recurve', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'CM', 'Gentlemen Compound', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'CW', 'Ladies Compound', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'LM', 'Gentlemen Longbow', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'LW', 'Ladies Longbow', 1, 240);
                    break;
                case 2:
                    break;
                case 3:
                    if ($TourType == 1) {
                        $appAdult='1440';
                        $app1 = 'Metric 1';
                        $app2 = 'Metric 2';
                        $app3 = 'Metric 3';
                        $app4 = 'Metric 4';
                        $app5 = 'Metric 5';
                    }
                    elseif ($TourType == 2) {
                        $appAdult = 'Double 1440';
                        $app1 = 'Double Metric 1';
                        $app2 = 'Double Metric 2';
                        $app3 = 'Double Metric 3';
                        $app4 = 'Double Metric 4';
                        $app5 = 'Double Metric 5';
                    }
                    else {
                        $app1 = '';
                        $app2 = '';
                        $app3 = '';
                        $app4 = '';
                        $app5 = '';
                        $appAdult = '';

                    }

                    $i=1;
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RM', $appAdult.' Recurve Men', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RW', $appAdult.' Recurve Women', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CM', $appAdult.' Compound Men', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CW', $appAdult.' Compound Women', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LM', $appAdult.' Longbow Men', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LW', $appAdult.' Longbow Women', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BM', $appAdult.' Barebow Men', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BW', $appAdult.' Barebow Women', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RM1', $app1.' Gentlemen Recurve Under 18', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CM1', $app1.' Gentlemen Compound Under 18', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LM1', $app1.' Gentlemen Longbow Under 18', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BM1', $app1.' Gentlemen Barebow Under 18', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RW2', $app2.' Ladies Recurve Under 18', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CW2', $app2.' Ladies Compound Under 18', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LW2', $app2.' Ladies Longbow Under 18', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BW2', $app2.' Ladies Barebow Under 18', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RM2', $app2.' Gentlemen Recurve Under 16', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CM2', $app2.' Gentlemen Compound Under 16', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LM2', $app2.' Gentlemen Longbow Under 16', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BM2', $app2.' Gentlemen Barebow Under 16', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RW3', $app3.' Ladies Recurve Under 16', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CW3', $app3.' Ladies Compound Under 16', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LW3', $app3.' Ladies Longbow Under 16', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BW3', $app3.' Ladies Barebow Under 16', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RM3', $app3.' Gentlemen Recurve Under 14', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CM3', $app3.' Gentlemen Compound Under 14', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LM3', $app3.' Gentlemen Longbow Under 14', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BM3', $app3.' Gentlemen Barebow Under 14', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RW4', $app4.' Ladies Recurve Under 14', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CW4', $app4.' Ladies Compound Under 14', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LW4', $app4.' Ladies Longbow Under 14', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BW4', $app4.' Ladies Barebow Under 14', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RM4', $app4.' Gentlemen Recurve Under 12', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CM4', $app4.' Gentlemen Compound Under 12', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LM4', $app4.' Gentlemen Longbow Under 12', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BM4', $app4.' Gentlemen Barebow Under 12', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RW5', $app5.' Ladies Recurve Under 12', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CW5', $app5.' Ladies Compound Under 12', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LW5', $app5.' Ladies Longbow Under 12', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BW5', $app5.' Ladies Barebow Under 12', 1, 240);
                    break;

            }
            break;

            }
}

function InsertStandardEvents($TourId, $SubRule,$TourType){

    switch ($TourType) {
        case 40:
            EventInserts($TourId);
            break;
        default:
            switch($SubRule){
                case 3:
                   EventInserts($TourId);
                break;


            }
    }
}

function EventInserts($TourId){
    foreach (array('R' => 'R', 'C' => 'C', 'B' => 'B', 'L' => 'L') as $kDiv => $vDiv) {
        $clsTmpArr = array('W', 'W2', 'W3', 'W4', 'W5');

        foreach ($clsTmpArr as $kClass => $vClass) {
            InsertClassEvent($TourId, 0, 1, $vDiv . $vClass, $kDiv, $vClass);

        }
        $clsTmpArr = array('M', 'M1', 'M2', 'M3', 'M4', 'M5');
        foreach ($clsTmpArr as $kClass => $vClass) {
            InsertClassEvent($TourId, 0, 1, $vDiv . $vClass, $kDiv, $vClass);

        }
    }

}

/*

FIELD DEFINITIONS (Target Tournaments)

*/

function CreateStandardFieldClasses($TourId, $SubRule) {
	switch($SubRule) {
		case '1':
			CreateClass($TourId, 1, 21, 49, 0, 'M', 'M', 'Men');
			CreateClass($TourId, 2, 21, 49, 1, 'W', 'W', 'Women');
			CreateClass($TourId, 3, 18, 20, 0, 'JM', 'JM,M', 'Junior Men');
			CreateClass($TourId, 4, 18, 20, 1, 'JW', 'JW,W', 'Junior Women');
			CreateClass($TourId, 5,  1, 17, 0, 'CM', 'CM,JM,M', 'Cadet Men');
			CreateClass($TourId, 6,  1, 17, 1, 'CW', 'CW,JW,W', 'Cadet Women');
			CreateClass($TourId, 7, 50,100, 0, 'MM', 'MM,M', 'Master Men');
			CreateClass($TourId, 8, 50,100, 1, 'MW', 'MW,W', 'Master Women');
			break;
		case '2':
			CreateClass($TourId, 1, 21,100, 0, 'M', 'M', 'Men');
			CreateClass($TourId, 2, 21,100, 1, 'W', 'W', 'Women');
			CreateClass($TourId, 3, 1, 20, 0, 'JM', 'JM,M', 'Junior Men');
			CreateClass($TourId, 4, 1, 20, 1, 'JW', 'JW,W', 'Junior Women');
			break;
	}
}

function CreateStandardFieldEvents($TourId, $SubRule) {
	$Elim1=array(
		'Archers' => 16,
		'Ends' => 12,
		'Arrows' => 3,
		'SO' => 1
	);
	$Elim2=array(
		'Archers' => 8,
		'Ends' => 8,
		'Arrows' => 3,
		'SO' => 1
	);
	switch($SubRule) {
		case '1':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RM',  'Recurve Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RW',  'Recurve Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RJM', 'Recurve Junior Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RJW', 'Recurve Junior Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RCM', 'Recurve Cadet Men',     0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RCW', 'Recurve Cadet Women',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RMM', 'Recurve Master Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RMW', 'Recurve Master Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CM',  'Compound Men',          0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CW',  'Compound Women',        0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CJM', 'Compound Junior Men',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CJW', 'Compound Junior Women', 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CCM', 'Compound Cadet Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CCW', 'Compound Cadet Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CMM', 'Compound Master Men',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CMW', 'Compound Master Women', 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BM',  'Barebow Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BW',  'Barebow Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BJM', 'Barebow Junior Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BJW', 'Barebow Junior Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BCM', 'Barebow Cadet Men',     0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BCW', 'Barebow Cadet Women',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BMM', 'Barebow Master Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BMW', 'Barebow Master Women',  0, 0, 0, $Elim1, $Elim2);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MT',  'Men Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WT',  'Women Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MJT',  'Men Junior Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WJT',  'Women Junior Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MCT',  'Men Cadet Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WCT',  'Women Cadet Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MMT',  'Men Master Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WMT',  'Women Master Team',0,248,15);
			break;
		case '2':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RM',  'Recurve Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RW',  'Recurve Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RJM', 'Recurve Junior Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RJW', 'Recurve Junior Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CM',  'Compound Men',          0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CW',  'Compound Women',        0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CJM', 'Compound Junior Men',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CJW', 'Compound Junior Women', 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BM',  'Barebow Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BW',  'Barebow Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BJM', 'Barebow Junior Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BJW', 'Barebow Junior Women',  0, 0, 0, $Elim1, $Elim2);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MT',  'Men Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WT',  'Women Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MJT',  'Men Junior Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WJT',  'Women Junior Team',0,248,15);
			break;
	}
}

function InsertStandardFieldEvents($TourId, $SubRule) {
	switch($SubRule) {
		case '1':
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
			InsertClassEvent($TourId, 0, 1, 'BM',  'B',  'M');
			InsertClassEvent($TourId, 0, 1, 'BJM', 'B', 'JM');
			InsertClassEvent($TourId, 0, 1, 'BCM', 'B', 'CM');
			InsertClassEvent($TourId, 0, 1, 'BMM', 'B', 'MM');
			InsertClassEvent($TourId, 0, 1, 'BW',  'B',  'W');
			InsertClassEvent($TourId, 0, 1, 'BJW', 'B', 'JW');
			InsertClassEvent($TourId, 0, 1, 'BCW', 'B', 'CW');
			InsertClassEvent($TourId, 0, 1, 'BMW', 'B', 'MW');

			InsertClassEvent($TourId, 1, 1, 'MT',  'R',  'M');
			InsertClassEvent($TourId, 2, 1, 'MT',  'C',  'M');
			InsertClassEvent($TourId, 3, 1, 'MT',  'B',  'M');
			InsertClassEvent($TourId, 1, 1, 'MJT', 'R', 'JM');
			InsertClassEvent($TourId, 2, 1, 'MJT', 'C', 'JM');
			InsertClassEvent($TourId, 3, 1, 'MJT', 'B', 'JM');
			InsertClassEvent($TourId, 1, 1, 'MCT', 'R', 'CM');
			InsertClassEvent($TourId, 2, 1, 'MCT', 'C', 'CM');
			InsertClassEvent($TourId, 3, 1, 'MCT', 'B', 'CM');
			InsertClassEvent($TourId, 1, 1, 'MMT', 'R', 'MM');
			InsertClassEvent($TourId, 2, 1, 'MMT', 'C', 'MM');
			InsertClassEvent($TourId, 3, 1, 'MMT', 'B', 'MM');
			InsertClassEvent($TourId, 1, 1, 'WT',  'R',  'W');
			InsertClassEvent($TourId, 2, 1, 'WT',  'C',  'W');
			InsertClassEvent($TourId, 3, 1, 'WT',  'B',  'W');
			InsertClassEvent($TourId, 1, 1, 'WJT', 'R', 'JW');
			InsertClassEvent($TourId, 2, 1, 'WJT', 'C', 'JW');
			InsertClassEvent($TourId, 3, 1, 'WJT', 'B', 'JW');
			InsertClassEvent($TourId, 1, 1, 'WCT', 'R', 'CW');
			InsertClassEvent($TourId, 2, 1, 'WCT', 'C', 'CW');
			InsertClassEvent($TourId, 3, 1, 'WCT', 'B', 'CW');
			InsertClassEvent($TourId, 1, 1, 'WMT', 'R', 'MW');
			InsertClassEvent($TourId, 2, 1, 'WMT', 'C', 'MW');
			InsertClassEvent($TourId, 3, 1, 'WMT', 'B', 'MW');
			break;
		case '2':
			InsertClassEvent($TourId, 0, 1, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 0, 1, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 0, 1, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 0, 1, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 0, 1, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 0, 1, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 0, 1, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 0, 1, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 0, 1, 'BM',  'B',  'M');
			InsertClassEvent($TourId, 0, 1, 'BJM', 'B', 'JM');
			InsertClassEvent($TourId, 0, 1, 'BW',  'B',  'W');
			InsertClassEvent($TourId, 0, 1, 'BJW', 'B', 'JW');

			InsertClassEvent($TourId, 1, 1, 'MT',  'R',  'M');
			InsertClassEvent($TourId, 2, 1, 'MT',  'C',  'M');
			InsertClassEvent($TourId, 3, 1, 'MT',  'B',  'M');
			InsertClassEvent($TourId, 1, 1, 'MJT', 'R', 'JM');
			InsertClassEvent($TourId, 2, 1, 'MJT', 'C', 'JM');
			InsertClassEvent($TourId, 3, 1, 'MJT', 'B', 'JM');
			InsertClassEvent($TourId, 1, 1, 'WT',  'R',  'W');
			InsertClassEvent($TourId, 2, 1, 'WT',  'C',  'W');
			InsertClassEvent($TourId, 3, 1, 'WT',  'B',  'W');
			InsertClassEvent($TourId, 1, 1, 'WJT', 'R', 'JW');
			InsertClassEvent($TourId, 2, 1, 'WJT', 'C', 'JW');
			InsertClassEvent($TourId, 3, 1, 'WJT', 'B', 'JW');
			break;
	}
}

function InsertStandardFieldEliminations($TourId, $SubRule){
	$cls=array();
	switch($SubRule) {
		case '1':
			$cls=array('M', 'W', 'JM', 'JW', 'CM', 'CW', 'MM', 'MW');
			break;
		case '2':
			$cls=array('M', 'W', 'JM', 'JW');
			break;
	}
	foreach(array('R', 'C', 'B') as $div) {
		foreach($cls as $cl) {
			for($n=1; $n<=16; $n++) {
				safe_w_SQL("INSERT INTO Eliminations set ElId=0, ElElimPhase=0, ElEventCode='$div$cl', ElTournament=$TourId, ElQualRank=$n");
			}
			for($n=1; $n<=8; $n++) {
				safe_w_SQL("INSERT INTO Eliminations set ElId=0, ElElimPhase=1, ElEventCode='$div$cl', ElTournament=$TourId, ElQualRank=$n");
			}
		}
	}
}

/*

3D DEFINITIONS (Target Tournaments)

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
	switch($SubRule) {
		case '1':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CM',  'Compound Men',          0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CW',  'Compound Women',        0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CJM', 'Compound Junior Men',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CJW', 'Compound Junior Women', 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CCM', 'Compound Cadet Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CCW', 'Compound Cadet Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CMM', 'Compound Master Men',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CMW', 'Compound Master Women', 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BM',  'Barebow Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BW',  'Barebow Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BJM', 'Barebow Junior Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BJW', 'Barebow Junior Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BCM', 'Barebow Cadet Men',     0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BCW', 'Barebow Cadet Women',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BMM', 'Barebow Master Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BMW', 'Barebow Master Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LM',  'Longbow Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LW',  'Longbow Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LJM', 'Longbow Junior Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LJW', 'Longbow Junior Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LCM', 'Longbow Cadet Men',     0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LCW', 'Longbow Cadet Women',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LMM', 'Longbow Master Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LMW', 'Longbow Master Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IM',  'Instinctive Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IW',  'Instinctive Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IJM', 'Instinctive Junior Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IJW', 'Instinctive Junior Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'ICM', 'Instinctive Cadet Men',     0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'ICW', 'Instinctive Cadet Women',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'ICM', 'Instinctive Master Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'ICW', 'Instinctive Master Women',  0, 0, 0, $Elim1, $Elim2);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'MT',  'Men Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'WT',  'Women Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'MJT',  'Men Junior Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'WJT',  'Women Junior Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'MCT',  'Men Cadet Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'WCT',  'Women Cadet Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'MMT',  'Men Master Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'WMT',  'Women Master Team');
			break;
		case '2':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CM',  'Compound Men',          0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CW',  'Compound Women',        0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BM',  'Barebow Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BW',  'Barebow Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LM',  'Longbow Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LW',  'Longbow Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IM',  'Instinctive Men',       0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IW',  'Instinctive Women',     0, 0, 0, $Elim1, $Elim2);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'MT',  'Men Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'WT',  'Women Team');
			break;
	}
}

function InsertStandard3DEvents($TourId, $SubRule) {
	switch($SubRule) {
		case '1':
			InsertClassEvent($TourId, 0, 1, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 0, 1, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 0, 1, 'CCM', 'C', 'CM');
			InsertClassEvent($TourId, 0, 1, 'CMM', 'C', 'MM');
			InsertClassEvent($TourId, 0, 1, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 0, 1, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 0, 1, 'CCW', 'C', 'CW');
			InsertClassEvent($TourId, 0, 1, 'CMW', 'C', 'MW');
			InsertClassEvent($TourId, 0, 1, 'BM',  'B',  'M');
			InsertClassEvent($TourId, 0, 1, 'BJM', 'B', 'JM');
			InsertClassEvent($TourId, 0, 1, 'BCM', 'B', 'CM');
			InsertClassEvent($TourId, 0, 1, 'BMM', 'B', 'MM');
			InsertClassEvent($TourId, 0, 1, 'BW',  'B',  'W');
			InsertClassEvent($TourId, 0, 1, 'BJW', 'B', 'JW');
			InsertClassEvent($TourId, 0, 1, 'BCW', 'B', 'CW');
			InsertClassEvent($TourId, 0, 1, 'BMW', 'B', 'MW');
			InsertClassEvent($TourId, 0, 1, 'LM',  'L',  'M');
			InsertClassEvent($TourId, 0, 1, 'LJM', 'L', 'JM');
			InsertClassEvent($TourId, 0, 1, 'LCM', 'L', 'CM');
			InsertClassEvent($TourId, 0, 1, 'LMM', 'L', 'MM');
			InsertClassEvent($TourId, 0, 1, 'LW',  'L',  'W');
			InsertClassEvent($TourId, 0, 1, 'LJW', 'L', 'JW');
			InsertClassEvent($TourId, 0, 1, 'LCW', 'L', 'CW');
			InsertClassEvent($TourId, 0, 1, 'LMW', 'L', 'MW');
			InsertClassEvent($TourId, 0, 1, 'IM',  'I',  'M');
			InsertClassEvent($TourId, 0, 1, 'IJM', 'I', 'JM');
			InsertClassEvent($TourId, 0, 1, 'ICM', 'I', 'CM');
			InsertClassEvent($TourId, 0, 1, 'IMM', 'I', 'MM');
			InsertClassEvent($TourId, 0, 1, 'IW',  'I',  'W');
			InsertClassEvent($TourId, 0, 1, 'IJW', 'I', 'JW');
			InsertClassEvent($TourId, 0, 1, 'ICW', 'I', 'CW');
			InsertClassEvent($TourId, 0, 1, 'IMW', 'I', 'MW');

			InsertClassEvent($TourId, 1, 1, 'MT',  'C',  'M');
			InsertClassEvent($TourId, 2, 1, 'MT',  'L',  'M');
			InsertClassEvent($TourId, 3, 1, 'MT',  'B',  'M');
			InsertClassEvent($TourId, 3, 1, 'MT',  'I',  'M');
			InsertClassEvent($TourId, 1, 1, 'MJT', 'C', 'JM');
			InsertClassEvent($TourId, 2, 1, 'MJT', 'L', 'JM');
			InsertClassEvent($TourId, 3, 1, 'MJT', 'B', 'JM');
			InsertClassEvent($TourId, 3, 1, 'MJT', 'I', 'JM');
			InsertClassEvent($TourId, 1, 1, 'MCT', 'C', 'CM');
			InsertClassEvent($TourId, 2, 1, 'MCT', 'L', 'CM');
			InsertClassEvent($TourId, 3, 1, 'MCT', 'B', 'CM');
			InsertClassEvent($TourId, 3, 1, 'MCT', 'I', 'CM');
			InsertClassEvent($TourId, 1, 1, 'MMT', 'C', 'MM');
			InsertClassEvent($TourId, 2, 1, 'MMT', 'L', 'MM');
			InsertClassEvent($TourId, 3, 1, 'MMT', 'B', 'MM');
			InsertClassEvent($TourId, 3, 1, 'MMT', 'I', 'MM');
			InsertClassEvent($TourId, 1, 1, 'WT',  'C',  'W');
			InsertClassEvent($TourId, 2, 1, 'WT',  'L',  'W');
			InsertClassEvent($TourId, 3, 1, 'WT',  'B',  'W');
			InsertClassEvent($TourId, 3, 1, 'WT',  'I',  'W');
			InsertClassEvent($TourId, 1, 1, 'WJT', 'C', 'JW');
			InsertClassEvent($TourId, 2, 1, 'WJT', 'L', 'JW');
			InsertClassEvent($TourId, 3, 1, 'WJT', 'B', 'JW');
			InsertClassEvent($TourId, 3, 1, 'WJT', 'I', 'JW');
			InsertClassEvent($TourId, 1, 1, 'WCT', 'C', 'CW');
			InsertClassEvent($TourId, 2, 1, 'WCT', 'L', 'CW');
			InsertClassEvent($TourId, 3, 1, 'WCT', 'B', 'CW');
			InsertClassEvent($TourId, 3, 1, 'WCT', 'I', 'CW');
			InsertClassEvent($TourId, 1, 1, 'WMT', 'C', 'MW');
			InsertClassEvent($TourId, 2, 1, 'WMT', 'L', 'MW');
			InsertClassEvent($TourId, 3, 1, 'WMT', 'B', 'MW');
			InsertClassEvent($TourId, 3, 1, 'WMT', 'I', 'MW');
			break;
		case '2':
			InsertClassEvent($TourId, 0, 1, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 0, 1, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 0, 1, 'BM',  'B',  'M');
			InsertClassEvent($TourId, 0, 1, 'BW',  'B',  'W');
			InsertClassEvent($TourId, 0, 1, 'LM',  'L',  'M');
			InsertClassEvent($TourId, 0, 1, 'LW',  'L',  'W');
			InsertClassEvent($TourId, 0, 1, 'IM',  'I',  'M');
			InsertClassEvent($TourId, 0, 1, 'IW',  'I',  'W');

			InsertClassEvent($TourId, 1, 1, 'MT',  'C',  'M');
			InsertClassEvent($TourId, 2, 1, 'MT',  'L',  'M');
			InsertClassEvent($TourId, 3, 1, 'MT',  'B',  'M');
			InsertClassEvent($TourId, 3, 1, 'MT',  'I',  'M');
			InsertClassEvent($TourId, 1, 1, 'WT',  'C',  'W');
			InsertClassEvent($TourId, 2, 1, 'WT',  'L',  'W');
			InsertClassEvent($TourId, 3, 1, 'WT',  'B',  'W');
			InsertClassEvent($TourId, 3, 1, 'WT',  'I',  'W');
			break;
	}
}

function InsertStandard3DEliminations($TourId, $SubRule){
	$cls=array();
	switch($SubRule) {
		case '1':
			$cls=array('M', 'W', 'JM', 'JW', 'CM', 'CW', 'MM', 'MW');
			break;
		case '2':
			$cls=array('M', 'W');
			break;
	}
	foreach(array('C', 'B', 'L', 'I') as $div) {
		foreach($cls as $cl) {
			for($n=1; $n<=16; $n++) {
				safe_w_SQL("INSERT INTO Eliminations set ElId=0, ElElimPhase=0, ElEventCode='$div$cl', ElTournament=$TourId, ElQualRank=$n");
			}
			for($n=1; $n<=8; $n++) {
				safe_w_SQL("INSERT INTO Eliminations set ElId=0, ElElimPhase=1, ElEventCode='$div$cl', ElTournament=$TourId, ElQualRank=$n");
			}
		}
	}
}
?>