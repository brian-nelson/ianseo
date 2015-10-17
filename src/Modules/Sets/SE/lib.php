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
	if($Type==11 || $Type==20)
		CreateDivision($TourId,$i++,'I','Instinctive');
}

function CreateStandardClasses($TourId, $SubRule, $Field='', $Type=0) {
	$i=1;
	CreateClass($TourId, $i++, 0, 12, 0, 'KH', 'KH,CH', 'Knatte 10 Herrar',1);
	CreateClass($TourId, $i++, 0, 12, 1, 'KD', 'KD,CD,KH,CH', 'Knatte 10 Damer',1);
	CreateClass($TourId, $i++, 13, 15,0, 'CH', 'CH,JH', 'Cadett 13 Herrar',1);
	CreateClass($TourId, $i++, 13, 15,1, 'CD', 'CD,JD,CH,JH', 'Cadett 13 Damer',1);
	CreateClass($TourId, $i++, 16, 20,0, 'JH', 'JH,SH,H', 'Junior 16 Herrar',1);
	CreateClass($TourId, $i++, 16, 20,1, 'JD', 'JD,SD,D,JH,SH,H', 'Junior 16 Damer',1);
	CreateClass($TourId, $i++, 21, 49,0, 'H', 'H,SH', 'Herrar',1);
	CreateClass($TourId, $i++, 21, 49,1, 'D', 'D,SD,H,SH', 'Damer',1);
	CreateClass($TourId, $i++, 21, 49,0, 'SH', 'SH,H', 'Senior 21 Herrar',1);
	CreateClass($TourId, $i++, 21, 49,1, 'SD', 'SD,D,SH,H', 'Senior 21 Damer',1);
	CreateClass($TourId, $i++, 50, 59,0, 'MH', 'MH,SH,H', 'Master 50 Herrar',1);
	CreateClass($TourId, $i++, 50, 59,1, 'MD', 'MD,SD,D,MH,SH,H', 'Master 50 Damer',1);
	CreateClass($TourId, $i++, 60, 100,0, 'VH', 'VH,MH,SH,H', 'Veteran 60 Herrar',1);
	CreateClass($TourId, $i++, 60, 100,1, 'VD', 'VD,MD,SD,D,VH,MH,SH,H', 'Veteran 60 Damer',1);
}

function CreateStandardSubClasses($TourId) {
	$i=1;
	CreateSubClass($TourId, $i++, 'M', 'Motion');
	CreateSubClass($TourId, $i++, 'T', 'Wooden Arrow');
}

function CreateStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
	$TargetR=($Outdoor?5:2);
	$TargetC=($Outdoor?9:4);

	if($TourType==6  || $TourType==3 || $TourType==1) {
		$dv = array('R'=>'Recurve','B'=>'Barebow','C'=>'Compound','L'=>'Longbow');
		if($TourType==10 || $TourType==11 || $TourType==12 || $TourType==20)
			$dv[] = array('I'=>'Istinctive');
		$cl = array('C'=>'Cadett','J'=>'Junior','S'=>'Senior','M'=>'Master','V'=>'Veteran');
		$ge = array('H'=>'Herrar','D'=>'Damer');
		$i=1;
		foreach($dv as $k_dv => $v_dv) {
			foreach($cl as $k_cl => $v_cl) {
				foreach($ge as $k_ge => $v_ge) {
					if($k_cl=='S')
						CreateEvent($TourId, $i++, 0, 0, ($Outdoor ? 48 : 16), ($k_dv=='C' ? $TargetC : $TargetR), 5, 3, 1, 5, 3, 1, $k_ge. $k_dv,  $v_ge . ' ' . $v_dv, ($k_dv=='C' ? 0 : 1), 240, 240);
					CreateEvent($TourId, $i++, 0, 0, ($Outdoor ? 48 : 16), ($k_dv=='C' ? $TargetC : $TargetR), 5, 3, 1, 5, 3, 1, $k_cl . $k_ge. $k_dv,  $v_cl . ' ' . $v_ge . ' ' . $v_dv, ($k_dv=='C' ? 0 : 1), 240, 240);
				}
			}
		}
		$i=1;
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'LKC',  'Lag knatte compound');
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'LCC',  'Lag cadett compound');
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'LJC',  'Lag junior compound');
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'LSC',  'Lag senior compound');
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'LKB',  'Lag knatte barebow');
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'LCB',  'Lag cadett barebow');
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'LJB',  'Lag junior barebow');
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'LSB',  'Lag senior barebow');
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'LKR',  'Lag knatte recurve');
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'LCR',  'Lag cadett recurve');
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'LJR',  'Lag junior recurve');
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'LSR',  'Lag senior recurve');
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'LKL',  'Lag knatte Longbow');
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'LCL',  'Lag cadett Longbow');
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'LJL',  'Lag junior Longbow');
		CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'LSL',  'Lag senior Longbow');
		if($TourType==11 || $TourType==20) {
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'LKI',  'Lag knatte Istinctive');
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'LCI',  'Lag cadett Istinctive');
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'LJI',  'Lag junior Istinctive');
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'LSI',  'Lag senior Istinctive');
		}
	}
}

function InsertStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
	$dv = array('R','B','C','L');
	if($TourType==11 || $TourType==20)
		$dv[] = array('I'=>'Istinctive');
	$cl = array('K'=>array('KH','KD'), 'C'=>array('CH','CD'), 'J'=>array('JH','JD'), 'S'=>array('SH','MH','VH','H','D', 'SD','MD','VD'));

	if($TourType==6 || $TourType==3 || $TourType==1) {
		foreach($dv as $v_dv) {
			foreach($cl as $k_cl => $v_cl) {
				foreach($v_cl as $dett_cl) {
					//Indvidual event
					if($k_cl!='K')
						InsertClassEvent($TourId, 0, 1, $dett_cl.$v_dv, $v_dv, $dett_cl);
					//Team composition
					InsertClassEvent($TourId, 1, 3, 'L' . $k_cl . $v_dv, $v_dv, $dett_cl);

				}
			}
		}
	}
}