<?php
/*

STANDARD THINGS

*/

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = '';
if(empty($SubRule)) $SubRule='1';

function CreateStandardDivisions($TourId, $Type=1, $SubRule=0) {
    $i=1;
    CreateDivision($TourId,$i++,'R','Recurve');
    CreateDivision($TourId,$i++,'B','Barebow');
    CreateDivision($TourId,$i++,'C','Compound');
    CreateDivision($TourId,$i++,'L','Longbow');
    CreateDivision($TourId,$i++,'I','Instinctive');
    CreateDivision($TourId,$i++,'OF','Official', 0);
}

function CreateStandardClasses($TourId, $SubRule=0, $Field='', $TourType=0) {
    $i=1;
    CreateClass($TourId, $i++, 13,  15, 0, 'NM', 'NM', 'Nordic Cadet (13-15) Men',1, 'R,B,C,L,I');
    CreateClass($TourId, $i++, 13,  15, 1, 'NW', 'NW', 'Nordic Cadet (13-15) Women',1, 'R,B,C,L,I');
    CreateClass($TourId, $i++, 16,  17, 0, 'WM', 'WM', 'WA Cadet (16-17) Men',1, 'R,B,C,L,I');
    CreateClass($TourId, $i++, 16,  17, 1, 'WW', 'WW', 'WA Cadet (16-17) Women',1, 'R,B,C,L,I');
    CreateClass($TourId, $i++, 18,  20, 0, 'JM', 'JM', 'Junior (18-20) Men',1, 'R,B,C,L,I');
    CreateClass($TourId, $i++, 18,  20, 1, 'JW', 'JW', 'Junior (18-20) Women',1, 'R,B,C,L,I');
    CreateClass($TourId, $i++, 1,  99, -1, 'Jg', 'Jg', 'Judge',0, 'OF');
    CreateClass($TourId, $i++, 1,  99, -1, 'Tm', 'Tm', 'Team Manager',0, 'OF');
    CreateClass($TourId, $i++, 1,  99, -1, 'Co', 'Co', 'Coach',0, 'OF');
    CreateClass($TourId, $i++, 1,  99, -1, 'Do', 'Do', 'Delegation Official',0, 'OF');
    CreateClass($TourId, $i++, 1,  99, -1, 'Md', 'Md', 'Medical Personnel',0, 'OF');
    CreateClass($TourId, $i++, 1,  99, -1, 'St', 'St', 'Staff',0, 'OF');
    CreateClass($TourId, $i++, 1,  99, -1, 'Ot', 'Ot', 'Other Function',0, 'OF');
}

function CreateStandardSubClasses($TourId) {
    $i=1;
//	CreateSubClass($TourId, $i++, 'M', 'Motion');
//	CreateSubClass($TourId, $i++, 'T', 'Wooden Arrow');
}

function CreateStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
	$TargetR=($Outdoor ? 5 : 2);
	$TargetC=($Outdoor ? 9 : 4);
    $TargetOther=($Outdoor ? 5 : 1);
	$TargetSizeR=($Outdoor ? 122 : 40);
	$TargetSizeC=($Outdoor ? 80 : 40);
	$DistanceR=($Outdoor ? 70 : 18);
	$DistanceC=($Outdoor ? 50 : 18);
	$AthTarget=254;  // This is a bit container corresponding to the finals and 1 or 2 archers per target. Each bit represents a phase (1=gold final, 2=semi, etc),  An active bit means 2 archers

    if($TourType==3) {
        $dv = array('R'=>'Recurve','B'=>'Barebow','C'=>'Compound','L'=>'Longbow','I'=>'Instinctive');

        // Finals created for all classes
        $cl = array('N'=>'Nordic Cadet (13-15)','W'=>'WA Cadet (16-17)','J'=>'Junior (18-20)');
        $ge = array('M'=>'Men','W'=>'Women');

        $i=1;
        foreach($dv as $k_dv => $v_dv) {
            foreach($cl as $k_cl => $v_cl) {
                foreach($ge as $k_ge => $v_ge) {
                    $CurrTarget = ($k_dv=='C' ? $TargetC : ($k_dv=='R' ? $TargetR : $TargetOther));
                    $CurrTargetSize = ($k_dv=='C' ? $TargetSizeC : $TargetSizeR);
                    CreateEvent($TourId, $i++, 0, 0, ($Outdoor ? 32 : 16), $CurrTarget, 5, 3, 1, 5, 3, 1, $k_dv . $k_cl . $k_ge,  $v_dv . ' ' . $v_cl . ' ' . $v_ge, ($k_dv=='C' ? 0 : 1), 240, $AthTarget, array(), array(), '', '', $CurrTargetSize);
                }
            }
        }

        $i=1;
        CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RNT',  'Recurve Nordic Cadet (13-15) Team', 1,0, 0, null, null, '', '', 0, 0, '', 1);
        CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RWT',  'Recurve WA Cadet (16-17) Team', 1,0, 0, null, null, '', '', 0, 0, '', 1);
        CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RJT',  'Recurve Junior (18-20) Team', 1,0, 0, null, null, '', '', 0, 0, '', 1);
        CreateEvent($TourId, $i++, 1, 0, 8, $TargetOther, 4, 6, 3, 4, 6, 3, 'BNT',  'Barebow Nordic Cadet (13-15) Team', 1,0, 0, null, null, '', '', 0, 0, '', 1);
        CreateEvent($TourId, $i++, 1, 0, 8, $TargetOther, 4, 6, 3, 4, 6, 3, 'BWT',  'Barebow WA Cadet (16-17) Team', 1,0, 0, null, null, '', '', 0, 0, '', 1);
        CreateEvent($TourId, $i++, 1, 0, 8, $TargetOther, 4, 6, 3, 4, 6, 3, 'BJT',  'Barebow Junior (18-20) Team', 1,0, 0, null, null, '', '', 0, 0, '', 1);
        CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CNT',  'Compound Nordic Cadet (13-15) Team', 0,0, 0, null, null, '', '', 0, 0, '', 1);
        CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CWT',  'Compound WA Cadet (16-17) Team', 0,0, 0, null, null, '', '', 0, 0, '', 1);
        CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CJT',  'Compound Junior (18-20) Team', 0,0, 0, null, null, '', '', 0, 0, '', 1);
        CreateEvent($TourId, $i++, 1, 0, 8, $TargetOther, 4, 6, 3, 4, 6, 3, 'LNT',  'Longbow Nordic Cadet (13-15) Team', 1,0, 0, null, null, '', '', 0, 0, '', 1);
        CreateEvent($TourId, $i++, 1, 0, 8, $TargetOther, 4, 6, 3, 4, 6, 3, 'LWT',  'Longbow WA Cadet (16-17) Team', 1,0, 0, null, null, '', '', 0, 0, '', 1);
        CreateEvent($TourId, $i++, 1, 0, 8, $TargetOther, 4, 6, 3, 4, 6, 3, 'LJT',  'Longbow Junior (18-20) Team', 1,0, 0, null, null, '', '', 0, 0, '', 1);
        CreateEvent($TourId, $i++, 1, 0, 8, $TargetOther, 4, 6, 3, 4, 6, 3, 'INT',  'Instinctive Nordic Cadet (13-15) Team', 1,0, 0, null, null, '', '', 0, 0, '', 1);
        CreateEvent($TourId, $i++, 1, 0, 8, $TargetOther, 4, 6, 3, 4, 6, 3, 'IWT',  'Instinctive WA Cadet (16-17) Team', 1,0, 0, null, null, '', '', 0, 0, '', 1);
        CreateEvent($TourId, $i++, 1, 0, 8, $TargetOther, 4, 6, 3, 4, 6, 3, 'IJT',  'Instinctive Junior (18-20) Team', 1,0, 0, null, null, '', '', 0, 0, '', 1);
    }
}

function InsertStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
    $dv = array('R','B','C','L','I');
    $cl = array('N'=>array('NM','NW'), 'W'=>array('WM','WW'), 'J'=>array('JM','JW'));

    if($TourType==3) {
        foreach($dv as $v_dv) {
            foreach($cl as $k_cl => $v_cl) {
                foreach($v_cl as $dett_cl) {
                    //Indvidual event
                    InsertClassEvent($TourId, 0, 1, $v_dv.$dett_cl, $v_dv, $dett_cl);
                    //Team composition
                    InsertClassEvent($TourId, 1, 3, $v_dv . $k_cl . 'T', $v_dv, $dett_cl);
                }
            }
        }
    }
}
