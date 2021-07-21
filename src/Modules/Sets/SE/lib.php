<?php
/*

STANDARD THINGS

*/

// these go here as it is a "global" definition, used or not
$tourCollation = 'swedish';
$tourDetIocCode = 'SWE';
if(empty($SubRule)) $SubRule='1';

function CreateStandardDivisions($TourId, $Type=1, $SubRule=0) {
	$i=1;
	CreateDivision($TourId,$i++,'R','Recurve');
	CreateDivision($TourId,$i++,'B','Barebow');
	CreateDivision($TourId,$i++,'C','Compound');
	CreateDivision($TourId,$i++,'L','Longbow');
	CreateDivision($TourId,$i++,'I','Instinctive');
}

function CreateStandardClasses($TourId, $SubRule, $Field='', $Type=0) {
	$i=1;
	CreateClass($TourId, $i++,  0,  12, 0, 'KH', 'KH,CH', 'Knatte 10 Herrar',1);
	CreateClass($TourId, $i++,  0,  12, 1, 'KD', 'KD,CD,KH,CH', 'Knatte 10 Damer',1);
	CreateClass($TourId, $i++, 13,  15, 0, 'CH', 'CH,JH,EH', 'Cadet 13 Herrar',1);
	CreateClass($TourId, $i++, 13,  15, 1, 'CD', 'CD,JD,ED,CH,JH,EH', 'Cadet 13 Damer',1);
	CreateClass($TourId, $i++, 16,  20, 0, 'JH', 'JH,SH,EH', 'Junior 16 Herrar',1);
	CreateClass($TourId, $i++, 16,  20, 1, 'JD', 'JD,SD,ED,JH,SH,EH', 'Junior 16 Damer',1);
	CreateClass($TourId, $i++, 21,  49, 0, 'EH', 'EH,SH', 'Elit Herrar',1);
	CreateClass($TourId, $i++, 21,  49, 1, 'ED', 'ED,SD,EH,SH', 'Elit Damer',1);
	CreateClass($TourId, $i++, 21,  49, 0, 'SH', 'SH,EH', 'Senior 21 Herrar',1);
	CreateClass($TourId, $i++, 21,  49, 1, 'SD', 'SD,ED,SH,EH', 'Senior 21 Damer',1);
	CreateClass($TourId, $i++, 50,  59, 0, 'MH', 'MH,SH,EH', 'Master 50 Herrar',1);
	CreateClass($TourId, $i++, 50,  59, 1, 'MD', 'MD,SD,ED,MH,SH,EH', 'Master 50 Damer',1);
	CreateClass($TourId, $i++, 60, 100, 0, 'VH', 'VH,MH,SH,EH', 'Veteran 60 Herrar',1);
	CreateClass($TourId, $i++, 60, 100, 1, 'VD', 'VD,MD,SD,ED,VH,MH,SH,EH', 'Veteran 60 Damer',1);
}

function CreateStandardSubClasses($TourId) {
	$i=1;
	CreateSubClass($TourId, $i++, 'M', 'Motion');
	CreateSubClass($TourId, $i++, 'T', 'Wooden Arrow');
}

function CreateStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
	$TargetR=($Outdoor?5:2);
	$TargetC=($Outdoor?9:4);
	$TargetOther=($Outdoor?5:1);

	if($TourType==6 || $TourType==3 || $TourType==37 || $TourType==1) {
		$dv = array('R'=>'Recurve','B'=>'Barebow','C'=>'Compound','L'=>'Longbow','I'=>'Instinctive');
		// Finals created for all classes for outdoor comps, but not indoor
		if($TourType==3 || $TourType==37 || $TourType==1) {
		       $cl = array('C'=>'Cadet 13','J'=>'Junior 16','E'=>'Elit','S'=>'Senior 21','M'=>'Master 50','V'=>'Veteran 60');
		} else {
			$cl = array('C'=>'Cadet 13','J'=>'Junior 16','E'=>'Elit');
		}
		$ge = array('H'=>'Herrar','D'=>'Damer');
		$i=1;
		foreach($dv as $k_dv => $v_dv) {
			foreach($cl as $k_cl => $v_cl) {
				foreach($ge as $k_ge => $v_ge) {
					$CurrTarget = ($k_dv=='C' ? $TargetC : ($k_dv=='R' ? $TargetR : $TargetOther));
					CreateEvent($TourId, $i++, 0, 0, ($Outdoor ? 32 : 16), $CurrTarget, 5, 3, 1, 5, 3, 1, $k_dv . $k_cl . $k_ge,  $v_dv . ' ' . $v_cl . ' ' . $v_ge, ($k_dv=='C' ? 0 : 1), 240, 240);
				}
			}
		}
		$i=1;
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'LCC',  'Lag Cadet 13 Compound');
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'LJC',  'Lag Junior 16 Compound');
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'LSC',  'Lag Senior Compound');
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetOther, 4, 6, 3, 4, 6, 3, 'LCB',  'Lag Cadet 13 Barebow', 1);
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetOther, 4, 6, 3, 4, 6, 3, 'LJB',  'Lag Junior 16 Barebow', 1);
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetOther, 4, 6, 3, 4, 6, 3, 'LSB',  'Lag Senior Barebow', 1);
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'LCR',  'Lag Cadet 13 Recurve', 1);
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'LJR',  'Lag Junior 16 Recurve', 1);
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'LSR',  'Lag Senior Recurve', 1);
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetOther, 4, 6, 3, 4, 6, 3, 'LCL',  'Lag Cadet 13 Longbow', 1);
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetOther, 4, 6, 3, 4, 6, 3, 'LJL',  'Lag Junior 16 Longbow', 1);
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetOther, 4, 6, 3, 4, 6, 3, 'LSL',  'Lag Senior Longbow', 1);
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetOther, 4, 6, 3, 4, 6, 3, 'LCI',  'Lag Cadet 13 Instinctive', 1);
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetOther, 4, 6, 3, 4, 6, 3, 'LJI',  'Lag Junior 16 Instinctive', 1);
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetOther, 4, 6, 3, 4, 6, 3, 'LSI',  'Lag Senior Instinctive', 1);
	}
}

function InsertStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
	$dv = array('R','B','C','L','I');
	$cl = array('K'=>array('KH','KD'), 'C'=>array('CH','CD'), 'J'=>array('JH','JD'), 'S'=>array('SH','MH','VH','EH','ED', 'SD','MD','VD'));

	if($TourType==6 || $TourType==3 || $TourType==37 || $TourType==1) {
		foreach($dv as $v_dv) {
			foreach($cl as $k_cl => $v_cl) {
				foreach($v_cl as $dett_cl) {
					//Indvidual event
					if($k_cl != 'K') {
						InsertClassEvent($TourId, 0, 1, $v_dv.$dett_cl, $v_dv, $dett_cl);
						//Team composition
						InsertClassEvent($TourId, 1, 3, 'L' . $k_cl . $v_dv, $v_dv, $dett_cl);
					}
				}
			}
		}
	}
}