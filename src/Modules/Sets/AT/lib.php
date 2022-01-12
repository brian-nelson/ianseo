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
        'L'=>'Langbogen',
        'T'=>'Traditional',
        'B'=>'Blankbogen',
        'W1'=>'W1',
        'RO'=>'Recurve Open',
        'CO'=>'Compound Open',
        'C'=>'Compound',
        'R'=>'Recurve'
    );

	if ($Type == 'FIELD' OR $Type == '3D') {
        $optionDivs = array('R' => 'Recurve', 'C' => 'Compound', 'B' => 'Blankbogen', 'L' => 'Langbogen', 'T' => 'Traditional');
    }
    foreach ($optionDivs as $k => $v){
		CreateDivision($TourId, $i++, $k, $v, 1, ($k=='W1' ? 'W1' : substr($k,0,1)), ($k=='W1' ? 'W1' : substr($k,0,1)),($k=='W1' or $k=='RO' or $k=='CO'));
	}
}

function CreateStandardClasses($TourId, $SubRule, $Type='FITA') {
	switch($SubRule) {
		case '1':
        case '3':
            CreateClass($TourId, 1, 10, 12, 1, 'U13W', 'U13W', 'U13 weiblich');
            CreateClass($TourId, 2, 10, 12, 0, 'U13M', 'U13M', 'U13 männlich');
            CreateClass($TourId, 3, 10, 12, -1, 'U13', 'U13', 'U13');

            CreateClass($TourId, 4, 13, 14, 1, 'U15W', 'U15W,U13W', 'U15 weiblich');
            CreateClass($TourId, 5, 13, 14, 0, 'U15M', 'U15M,U13M', 'U15 männlich');
            CreateClass($TourId, 6, 13, 14, -1, 'U15', 'U15,U13', 'U15');

            CreateClass($TourId, 7, 15, 17, 1, 'U18W', 'U18W,U15W,U13W', 'U18 weiblich');
            CreateClass($TourId, 8, 15, 17, 0, 'U18M', 'U18M,U15M,U13M', 'U18 männlich');
            CreateClass($TourId, 9, 18, 20, 1, 'U21W', 'U21W,U18W,U13W,U15W', 'U21 weiblich');
            CreateClass($TourId, 10, 18, 20, 0, 'U21M', 'U21M,U18M,U15M,U13M', 'U21 männlich');

            CreateClass($TourId, 11, 65, 99, 1, '65W', '65W', '65+ weiblich');
            CreateClass($TourId, 12, 65, 99, 0, '65M', '65M', '65+ männlich');
            CreateClass($TourId, 13, 65, 99, -1, '65', '65', '65+');

            CreateClass($TourId, 14, 50, 64, 1, '50W', '50W,65W', '50+ weiblich');
            CreateClass($TourId, 15, 50, 64, 0, '50M', '50M,65M', '50+ männlich');
            CreateClass($TourId, 16, 50, 64, -1, '50', '50,65', '50+');

            CreateClass($TourId, 17, 21, 49, 0, 'M', 'M,U13M,U15M,U21M,U18M,65M,50M', 'Herren');
            CreateClass($TourId, 18, 21, 49, 1, 'W', 'W,U13W,U15W,U21W,U18W,65W,50W', 'Damen');

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
    $TargetB=($Outdoor ? TGT_OUT_FULL : TGT_IND_6_big10);
    $TargetLT=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetSizeR=($Outdoor ? 122 : 40);
	$TargetSizeC=($Outdoor ? 80 : 40);
    $TargetSizeB=($Outdoor ? 122 : 40);
    $TargetSizeL=($Outdoor ? 122 : 60);
    $TargetSizeT=($Outdoor ? 122 : 40);
	$DistanceR=($Outdoor ? 70 : 18);
	$DistanceC=($Outdoor ? 50 : 18);
    $DistanceB=($Outdoor ? 50 : 18);
    $DistanceL=($Outdoor ? 30 : 18);
    $DistanceT=($Outdoor ? 40 : 18);
	$FirstPhase = ($Outdoor ? 48 : 16);
	$TeamFirstPhase = ($Outdoor ? 12 : 8);

    $i=1;
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetLT, 5, 3, 1, 5, 3, 1, 'LW', 'Langbogen Damen', 1, 240, 240, 0, 0, '', '', $TargetSizeL, $DistanceL);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetLT, 5, 3, 1, 5, 3, 1, 'LM', 'Langbogen Herren', 1, 240, 240, 0, 0, '', '', $TargetSizeL, $DistanceL);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetLT, 5, 3, 1, 5, 3, 1, 'TW', 'Traditional Damen', 1, 240, 240, 0, 0, '', '', $TargetSizeT, $DistanceT);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetLT, 5, 3, 1, 5, 3, 1, 'TM', 'Traditional Herren', 1, 240, 240, 0, 0, '', '', $TargetSizeT, $DistanceT);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BW', 'Blankbogen Damen', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BM', 'Blankbogen Herren', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW', 'Compound Damen', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM', 'Compound Herren', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RW', 'Recurve Damen', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM', 'Recurve Herren', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);

    $i=1;
    CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetLT, 4, 6, 3, 4, 6, 3, 'LW',  'Langbogen Damen Teams', 1, 0, 0, 0, 0, '', '', $TargetSizeL, $DistanceL, '', 1, 0, 1, 2,2,1);
    CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetLT, 4, 6, 3, 4, 6, 3, 'LM',  'Langbogen Herren Teams', 1, 0, 0, 0, 0, '', '', $TargetSizeL, $DistanceL, '', 1, 0, 1, 2,2,1);
    CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetLT, 4, 4, 2, 4, 4, 2, 'LX',  'Langbogen Mixed Teams', 1, 0, 0, 0, 0, '', '', $TargetSizeL, $DistanceL, '', 1, 0, 1, 2,3);
    CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetLT, 4, 6, 3, 4, 6, 3, 'TW',  'Traditional Damen Teams', 1, 0, 0, 0, 0, '', '', $TargetSizeT, $DistanceT, '', 1, 0, 1, 2,2,1);
    CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetLT, 4, 6, 3, 4, 6, 3, 'TM',  'Traditional Herren Teams', 1, 0, 0, 0, 0, '', '', $TargetSizeT, $DistanceT, '', 1, 0, 1, 2,2,1);
    CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetLT, 4, 4, 2, 4, 4, 2, 'TX',  'Traditional Mixed Teams', 1, 0, 0, 0, 0, '', '', $TargetSizeT, $DistanceT, '', 1, 0, 1, 2,3);
    CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BW',  'Blankbogen Damen Teams', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '', 1, 0, 1, 2,2,1);
    CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BM',  'Blankbogen Herren Teams', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '', 1, 0, 1, 2,2,1);
    CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BX',  'Blankbogen Mixed Teams', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '', 1, 0, 1, 2,3);
    CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CW',  'Compound Damen Teams', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1, 0, 1, 2,2,1);
    CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CM',  'Compound Herren Teams', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1, 0, 1, 2,2,1);
    CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CX',  'Compound Mixed Teams', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1, 0, 1, 2,3);
    CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RW',  'Recurve Damen Teams', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1, 0, 1, 2,2,1);
    CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RM',  'Recurve Herren Teams', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1, 0, 1, 2,2,1);
    CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RX',  'Recurve Mixed Teams', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1, 0, 1, 2,3);
}

function InsertStandardEvents($TourId, $SubRule, $Outdoor=true) {
    foreach (array('R'=>'R_','C'=>'C_','B'=>'B_','L'=>'L_','T'=>'T_') as $kDiv=>$vDiv) {
        $clsTmpArr = array('W','U21W','U18W','50W','65W');
        if($kDiv == 'R' AND $Outdoor) {
            $clsTmpArr = array('W','U21W');
        } else if($kDiv == 'T') {
            $clsTmpArr = array('W','U21W','50W','65W');
        }
        foreach($clsTmpArr as $kClass=>$vClass) {
            InsertClassEvent($TourId, 0, 1, str_replace('_','W',$vDiv), $kDiv,  $vClass);
            InsertClassEvent($TourId, 1, 3, str_replace('_','W',$vDiv),  $kDiv,  $vClass);
            InsertClassEvent($TourId, 1, 1, str_replace('_','X',$vDiv),  $kDiv,  $vClass);
        }
        $clsTmpArr = array('M','U21M','U18M','50','50M','65','65M');
        if($kDiv == 'R' AND $Outdoor) {
            $clsTmpArr = array('M','U21M');
        } else if($kDiv == 'T') {
            $clsTmpArr = array('M','U21M','50','50M','65','65M');
        }
        foreach($clsTmpArr as $kClass=>$vClass) {
            InsertClassEvent($TourId, 0, 1, str_replace('_','M',$vDiv), $kDiv,  $vClass);
            InsertClassEvent($TourId, 1, 3, str_replace('_','M',$vDiv),  $kDiv,  $vClass);
            InsertClassEvent($TourId, 2, 1, str_replace('_','X',$vDiv),  $kDiv,  $vClass);
        }
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

