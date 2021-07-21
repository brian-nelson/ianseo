<?php

/*

STANDARD DEFINITIONS (Target Tournaments)

*/

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'AUT';
if(empty($SubRule)) {
    $SubRule='1';
}

function CreateStandardDivisions($TourId, $Type='FITA', $SubRule) {
	$i=1;
	$optionDivs = array(
	    'R'=>'Recurve',
        'C'=>'Compound',
        'B'=>'Blankbogen',
        'L'=>'Langbogen',
        'I'=>'Instinktiv Bogen',
        'RO'=>'Recurve Open',
        'CO'=>'Compound Open',
        'W1'=>'W1'
    );

	if ($Type == 'FIELD' OR $Type == '3D') {
        $optionDivs = array('R' => 'Recurve', 'C' => 'Compound', 'B' => 'Blankbogen', 'L' => 'Langbogen', 'I' => 'Instinktiv Bogen');
    } else if ($SubRule=="2") {
        $optionDivs = array('R'=>'Recurve','C'=>'Compound','RO'=>'Recurve Open','CO'=>'Compound Open','W1'=>'W1');
    }
    foreach ($optionDivs as $k => $v){
		CreateDivision($TourId, $i++, $k, $v, 1,'','',$k=='W1' or $k=='RO' or $k=='CO');
	}
}

function CreateStandardClasses($TourId, $SubRule, $Type='FITA') {
	switch($SubRule) {
		case '1':
        case '3':
            CreateClass($TourId, 1, 10, 12, 1, 'KW', 'KW', 'Schüler I weiblich');
            CreateClass($TourId, 2, 10, 12, 0, 'KM', 'KM', 'Schüler I männlich');
            CreateClass($TourId, 3, 10, 12, -1, 'KU', 'KU,KM,KW', 'Schüler I');

            CreateClass($TourId, 4, 13, 14, 1, 'SW', 'SW,KW', 'Schüler II weiblich');
            CreateClass($TourId, 5, 13, 14, 0, 'SM', 'SM,KM', 'Schüler II männlich');
            CreateClass($TourId, 6, 13, 14, -1, 'SU', 'SU,SW,SM', 'Schüler II');

            CreateClass($TourId, 7, 15, 17, 1, 'CW', 'CW,SW,KW', 'Kadetten weiblich');
            CreateClass($TourId, 8, 15, 17, 0, 'CM', 'CM,SM,KM', 'Kadetten männlich');
            CreateClass($TourId, 9, 18, 20, 1, 'JW', 'JW,CW,KW,SW', 'Junioren weiblich');
            CreateClass($TourId, 10, 18, 20, 0, 'JM', 'JM,CM,SM,KM', 'Junioren männlich');

            CreateClass($TourId, 11, 50, 64, 1, 'MW', 'MW,VW', 'Senioren I weiblich');
            CreateClass($TourId, 12, 50, 64, 0, 'MM', 'MM,VM', 'Senioren I männlich');
            CreateClass($TourId, 13, 50, 64, -1, 'MU', 'MU,MM,MW', 'Senioren I');

            CreateClass($TourId, 14, 65, 99, 1, 'VW', 'VW', 'Senioren II weiblich');
            CreateClass($TourId, 15, 65, 99, 0, 'VM', 'VM', 'Senioren II männlich');
            CreateClass($TourId, 16, 65, 99, -1, 'VU', 'VU,VM,VW', 'Senioren II');

            CreateClass($TourId, 17, 21, 49, 0, 'M', 'M,KM,SM,JM,CM,VM,MM', 'allgemeine Klasse männlich');
            CreateClass($TourId, 18, 21, 49, 1, 'W', 'W,KW,SW,JW,CW,VW,MW', 'allgemeine Klasse weiblich');

            break;
		case '2':
        case '4':
            CreateClass($TourId, 1, 1, 100, 0, 'M', 'M', 'Herren allgemeine Klasse ');
            CreateClass($TourId, 2, 1, 100, 1, 'W', 'W', 'Damen allgemeine Klasse');
			break;
	}
}

function CreateStandardEvents($TourId, $SubRule, $Outdoor=true, $allowBB=true) {
	$TargetR=($Outdoor ? TGT_OUT_FULL : TGT_IND_6_big10);
	$TargetC=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_6_small10);
    $TargetW1=($Outdoor ? TGT_OUT_FULL : TGT_IND_6_small10);
    $TargetB=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetSizeR=($Outdoor ? 122 : 40);
	$TargetSizeC=($Outdoor ? 80 : 40);
    $TargetSizeB=($Outdoor ? 122 : 40);
    $TargetSizeLI=($Outdoor ? 122 : 60);
	$DistanceR=($Outdoor ? 70 : 18);
	$DistanceC=($Outdoor ? 50 : 18);
    $DistanceB=($Outdoor ? 50 : 18);
    $DistanceLI=($Outdoor ? 30 : 18);
	$FirstPhase = ($Outdoor ? 48 : 16);
	$TeamFirstPhase = ($Outdoor ? 12 : 8);
	switch($SubRule) {
		case '1':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM', 'Recurve männlich', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RW', 'Recurve weiblich', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM', 'Compound männlich', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW', 'Compound weiblich', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
            CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BM', 'Blankbogen männlich', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
            CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BW', 'Blankbogen weiblich', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
            CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'LM', 'Langbogen männlich', 1, 240, 240, 0, 0, '', '', $TargetSizeLI, $DistanceLI);
            CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'LW', 'Langbogen weiblich', 1, 240, 240, 0, 0, '', '', $TargetSizeLI, $DistanceLI);
            CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'IM', 'Instinktivbogen männlich', 1, 240, 240, 0, 0, '', '', $TargetSizeLI, $DistanceLI);
            CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'IW', 'Instinktivbogen weiblich', 1, 240, 240, 0, 0, '', '', $TargetSizeLI, $DistanceLI);
            CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RMO', 'Recurve Open männlich', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RWO', 'Recurve Open weiblich', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CMO', 'Compound Open männlich', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
            CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CWO', 'Compound Open weiblich', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
            CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetW1, 5, 3, 1, 5, 3, 1, 'W1M', 'W1 Open männlich', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
            CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetW1, 5, 3, 1, 5, 3, 1, 'W1W', 'W1 Open weiblich', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);

            $i=1;
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RM',  'Recurve Herren Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1, 0, 1, 2,2,1);
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RW',  'Recurve Damen Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1, 0, 1, 2,2,1);
            if($Outdoor) {
                CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RX',  'Recurve Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1, 0, 1, 2,3);
            }
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CM',  'Compound Herren Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1, 0, 1, 2,2,1);
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CW',  'Compound Damen Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1, 0, 1, 2,2,1);
            if($Outdoor) {
                CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CX',  'Compound Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1, 0, 1, 2,3);
            }
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BM',  'Blankbogen Herren Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '', 1, 0, 1, 2,2,1);
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BW',  'Blankbogen Damen Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '', 1, 0, 1, 2,2,1);
            if($Outdoor) {
                CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BX',  'Blankbogen Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '', 1, 0, 1, 2,3);
            }
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'LM',  'Langbogen Herren Team', 1, 0, 0, 0, 0, '', '', $TargetSizeLI, $DistanceLI, '', 1, 0, 1, 2,2,1);
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'LW',  'Langbogen Damen Team', 1, 0, 0, 0, 0, '', '', $TargetSizeLI, $DistanceLI, '', 1, 0, 1, 2,2,1);
            if($Outdoor) {
                CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'LX',  'Langbogen Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeLI, $DistanceLI, '', 1, 0, 1, 2,3);
            }
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'IM',  'Instinktivbogen Herren Team', 1, 0, 0, 0, 0, '', '', $TargetSizeLI, $DistanceLI, '', 1, 0, 1, 2,2,1);
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'IW',  'Instinktivbogen Damen Team', 1, 0, 0, 0, 0, '', '', $TargetSizeLI, $DistanceLI, '', 1, 0, 1, 2,2,1);
            if($Outdoor) {
                CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'IX',  'Instinktivbogen Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeLI, $DistanceLI, '', 1, 0, 1, 2,3);
            }
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RMO',  'Recurve Open Herren Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1, 0, 1, 2,2,1);
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RWO',  'Recurve Open Damen Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1, 0, 1, 2,2,1);
            if($Outdoor) {
                CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RXO',  'Recurve Open Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1, 0, 1, 2,3);
            }
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CMO',  'Compound Open Herren Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1, 0, 1, 2,2,1);
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CWO',  'Compound Open Damen Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1, 0, 1, 2,2,1);
            if($Outdoor) {
                CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CXO',  'Compound Open Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1, 0, 1, 2,3);
            }
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetW1, 4, 6, 3, 4, 6, 3, 'W1M',  'W1 Herren Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1, 0, 1, 2,2,1);
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetW1, 4, 6, 3, 4, 6, 3, 'W1W',  'W1 Damen Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1, 0, 1, 2,2,1);
            if($Outdoor) {
                CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetW1, 4, 4, 2, 4, 4, 2, 'W1X',  'W1 Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1, 0, 1, 2,3);
            }

            break;
		case '2':
			$i=1;
            CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM', 'Recurve männlich', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RW', 'Recurve weiblich', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM', 'Compound männlich', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
            CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW', 'Compound weiblich', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
            CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RMO', 'Recurve Open männlich', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RWO', 'Recurve Open weiblich', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CMO', 'Compound Open männlich', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
            CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CWO', 'Compound Open weiblich', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
            CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetW1, 5, 3, 1, 5, 3, 1, 'W1M', 'W1 Open männlich', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
            CreateEvent($TourId, $i, 0, 0, $FirstPhase, $TargetW1, 5, 3, 1, 5, 3, 1, 'W1W', 'W1 Open weiblich', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
            $i=1;
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RM',  'Recurve Herren Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1, 0, 1, 2,2,1);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RW',  'Recurve Damen Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1, 0, 1, 2,2,1);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RX',  'Recurve Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1, 0, 1, 2,3);
			}
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CM',  'Compound Herren Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1, 0, 1, 2,2,1);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CW',  'Compound Damen Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1, 0, 1, 2,2,1);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CX',  'Compound Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1, 0, 1, 2,3);
			}
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RMO',  'Recurve Open Herren Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1, 0, 1, 2,2,1);
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RWO',  'Recurve Open Damen Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1, 0, 1, 2,2,1);
            if($Outdoor) {
                CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RXO',  'Recurve Open Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1, 0, 1, 2,3);
            }
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CMO',  'Compound Open Herren Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1, 0, 1, 2,2,1);
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CWO',  'Compound Open Damen Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1, 0, 1, 2,2,1);
            if($Outdoor) {
                CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CXO',  'Compound Open Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1, 0, 1, 2,3);
            }
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetW1, 4, 6, 3, 4, 6, 3, 'W1M',  'W1 Herren Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1, 0, 1, 2,2,1);
            CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetW1, 4, 6, 3, 4, 6, 3, 'W1W',  'W1 Damen Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1, 0, 1, 2,2,1);
            if($Outdoor) {
                CreateEvent($TourId, $i, 1, 1, $TeamFirstPhase, $TargetW1, 4, 4, 2, 4, 4, 2, 'W1X',  'W1 Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1, 0, 1, 2,3);
            }
        break;
	}
}

function InsertStandardEvents($TourId, $SubRule, $Outdoor=true) {
	switch($SubRule) {
		case '1':
		    foreach (array('R'=>'R_','C'=>'C_','B'=>'B_','L'=>'L_','I'=>'I_','W1'=>'W1_','RO'=>'R_O','CO'=>'C_O') as $kDiv=>$vDiv) {
		        $clsTmpArr = array('W','JW','CW','MW','VW');
		        if($kDiv == 'R' AND $Outdoor) {
                    $clsTmpArr = array('W','JW');
                }
                foreach($clsTmpArr as $kClass=>$vClass) {
                    InsertClassEvent($TourId, 0, 1, str_replace('_','W',$vDiv), $kDiv,  $vClass);
                    InsertClassEvent($TourId, 1, 3, str_replace('_','W',$vDiv),  $kDiv,  $vClass);
                    InsertClassEvent($TourId, 1, 1, str_replace('_','X',$vDiv),  $kDiv,  $vClass);
                }
                $clsTmpArr = array('M','JM','CM','MU','MM','VU','VM');
                if($kDiv == 'R' AND $Outdoor) {
                    $clsTmpArr = array('M','JM');
                }
		        foreach($clsTmpArr as $kClass=>$vClass) {
                    InsertClassEvent($TourId, 0, 1, str_replace('_','M',$vDiv), $kDiv,  $vClass);
                    InsertClassEvent($TourId, 1, 3, str_replace('_','M',$vDiv),  $kDiv,  $vClass);
                    InsertClassEvent($TourId, 2, 1, str_replace('_','X',$vDiv),  $kDiv,  $vClass);
                }
            }
            break;
		case '2':
			InsertClassEvent($TourId, 0, 1, 'RM',  'R',  'M');
            InsertClassEvent($TourId, 0, 1, 'RMO',  'RO',  'M');
			InsertClassEvent($TourId, 0, 1, 'RW',  'R',  'W');
            InsertClassEvent($TourId, 0, 1, 'RWO',  'RO',  'W');
			InsertClassEvent($TourId, 0, 1, 'CM',  'C',  'M');
            InsertClassEvent($TourId, 0, 1, 'CMO',  'CO',  'M');
			InsertClassEvent($TourId, 0, 1, 'CW',  'C',  'W');
            InsertClassEvent($TourId, 0, 1, 'CWO',  'CO',  'W');
            InsertClassEvent($TourId, 0, 1, 'W1M',  'W1',  'M');
            InsertClassEvent($TourId, 0, 1, 'W1W',  'W1',  'W');

			InsertClassEvent($TourId, 1, 3, 'RM',  'R',  'M');
            InsertClassEvent($TourId, 1, 3, 'RMO',  'RO',  'M');
			InsertClassEvent($TourId, 1, 3, 'RW',  'R',  'W');
            InsertClassEvent($TourId, 1, 3, 'RWO',  'RO',  'W');
			InsertClassEvent($TourId, 1, 1, 'RX',  'R',  'W');
            InsertClassEvent($TourId, 1, 1, 'RXO',  'RO',  'W');
			InsertClassEvent($TourId, 2, 1, 'RX',  'R',  'M');
            InsertClassEvent($TourId, 2, 1, 'RXO',  'RO',  'M');
			InsertClassEvent($TourId, 1, 3, 'CM',  'C',  'M');
            InsertClassEvent($TourId, 1, 3, 'CMO',  'CO',  'M');
			InsertClassEvent($TourId, 1, 3, 'CW',  'C',  'W');
            InsertClassEvent($TourId, 1, 3, 'CWO',  'CO',  'W');
			InsertClassEvent($TourId, 1, 1, 'CX',  'C',  'W');
            InsertClassEvent($TourId, 1, 1, 'CXO',  'CO',  'W');
			InsertClassEvent($TourId, 2, 1, 'CX',  'C',  'M');
            InsertClassEvent($TourId, 2, 1, 'CXO',  'CO',  'M');
            InsertClassEvent($TourId, 1, 3, 'W1M',  'W1',  'W');
            InsertClassEvent($TourId, 1, 3, 'W1W',  'W1',  'W');
            InsertClassEvent($TourId, 1, 1, 'W1X',  'W1',  'M');
            InsertClassEvent($TourId, 2, 1, 'W1X',  'W1',  'W');
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

