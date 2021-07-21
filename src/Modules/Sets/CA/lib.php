<?php

/*

STANDARD DEFINITIONS (Target Tournaments)

*/

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'CAN';
if(empty($SubRule)) $SubRule='1';

function CreateStandardDivisions($TourId, $SubRule, $Type='TARGET') {
	$i=1;
	if($Type=='3D') {
		if($SubRule!=2) CreateDivision($TourId, $i++, 'R', get_text('CA-R', 'Languages'));
		CreateDivision($TourId, $i++, 'B', get_text('CA-B', 'Languages'));
		CreateDivision($TourId, $i++, 'I', get_text('CA-I', 'Languages'));
		CreateDivision($TourId, $i++, 'L', get_text('CA-L', 'Languages'));
		CreateDivision($TourId, $i++, 'T', get_text('CA-T', 'Languages'));
		CreateDivision($TourId, $i++, 'U', get_text('CA-U', 'Languages'));
		CreateDivision($TourId, $i++, 'FP', get_text('CA-FP', 'Languages'));
		if($SubRule!=2) CreateDivision($TourId, $i++, 'H', get_text('CA-H', 'Languages'));
	} else {
		CreateDivision($TourId, $i++, 'R', get_text('CA-R', 'Languages'));
		CreateDivision($TourId, $i++, 'C', get_text('CA-C', 'Languages'));
		CreateDivision($TourId, $i++, 'B', get_text('CA-B', 'Languages'));
		if($Type=='FIELD') {
			CreateDivision($TourId, $i++, 'I', get_text('CA-I', 'Languages'));
			CreateDivision($TourId, $i++, 'L', get_text('CA-L', 'Languages'));
		}
		CreateDivision($TourId, $i++, 'FP', get_text('CA-FP', 'Languages'));
		CreateDivision($TourId, $i++, 'U', get_text('CA-U', 'Languages'));
		if($SubRule!=2) CreateDivision($TourId, $i++, 'H', get_text('CA-H', 'Languages'));
	}
}

function CreateStandardClasses($TourId, $SubRule, $Type='TARGET') {
	$i=1;
	switch($SubRule) {
		case '1':
			CreateClass($TourId, $i++, 21, 49, 0, 'M', 'M', get_text('CA-Cl-M', 'Languages'), '1', 'C,R,U');
			CreateClass($TourId, $i++, 21, 49, 1, 'W', 'W', get_text('CA-Cl-W', 'Languages'), '1', 'C,R,U');
			CreateClass($TourId, $i++, 21, 120, 0, 'Mb', 'Mb', get_text('CA-Cl-M', 'Languages'), '1', 'B');
			CreateClass($TourId, $i++, 21, 120, 1, 'Wb', 'Wb', get_text('CA-Cl-W', 'Languages'), '1', 'B');
			CreateClass($TourId, $i++,  1, 120, 0, 'Ma', 'Ma', get_text('CA-Cl-M', 'Languages'), '1', 'FP,H');
			CreateClass($TourId, $i++,  1, 120, 1, 'Wa', 'Wa', get_text('CA-Cl-W', 'Languages'), '1', 'FP,H');
			CreateClass($TourId, $i++, 18, 20, 0, 'JM', 'JM,M', get_text('CA-Cl-JM', 'Languages'), '1', 'B,C,R,U');
			CreateClass($TourId, $i++, 18, 20, 1, 'JW', 'JW,W', get_text('CA-Cl-JW', 'Languages'), '1', 'B,C,R,U');
			CreateClass($TourId, $i++, 15, 17, 0, 'CM', 'CM,JM,M', get_text('CA-Cl-CM', 'Languages'), '1', 'B,C,R,U');
			CreateClass($TourId, $i++, 15, 17, 1, 'CW', 'CW,JW,W', get_text('CA-Cl-CW', 'Languages'), '1', 'B,C,R,U');
			CreateClass($TourId, $i++, 13, 14, 0, 'BM', 'BM,CM,JM,M', get_text('CA-Cl-BM', 'Languages'), '1', 'B,C,R,U');
			CreateClass($TourId, $i++, 13, 14, 1, 'BW', 'BW,CW,JW,W', get_text('CA-Cl-BW', 'Languages'), '1', 'B,C,R,U');
			CreateClass($TourId, $i++, 10, 12, 0, 'PM', 'PM,BM,CM,JM,M', get_text('CA-Cl-PM', 'Languages'), '1', 'B,C,R,U');
			CreateClass($TourId, $i++, 10, 12, 1, 'PW', 'PW,BW,CW,JW,W', get_text('CA-Cl-PW', 'Languages'), '1', 'B,C,R,U');
			if($Type=='3D') {
				CreateClass($TourId, $i++, 50, 59, 0, '5M', '5M,M', get_text('CA-Cl-5M', 'Languages'), '1', 'C,R,O,T');
				CreateClass($TourId, $i++, 50, 59, 1, '5W', '5W,W', get_text('CA-Cl-5W', 'Languages'), '1', 'C,R,O,T');
				CreateClass($TourId, $i++, 60,100, 0, '6M', '6M,5M,M', get_text('CA-Cl-6M', 'Languages'), '1', 'C,R,T');
				CreateClass($TourId, $i++, 60,100, 1, '6W', '6W,5W,W', get_text('CA-Cl-6W', 'Languages'), '1', 'C,R,T');
			} else {
				CreateClass($TourId, $i++,  1,  9, 0, 'WM', 'WM,PM,BM,CM,JM,M', get_text('CA-Cl-WM', 'Languages'), '1', 'C,R');
				CreateClass($TourId, $i++,  1,  9, 1, 'WW', 'WW,PW,BW,CW,JW,W', get_text('CA-Cl-WW', 'Languages'), '1', 'C,R');
				CreateClass($TourId, $i++, 50,120, 0, 'MM', 'MM,M', get_text('CA-Cl-MM', 'Languages'), '1', 'C,R,U');
				CreateClass($TourId, $i++, 50,120, 1, 'MW', 'MW,W', get_text('CA-Cl-MW', 'Languages'), '1', 'C,R,U');
			}
			break;
		case '2':
			CreateClass($TourId, $i++, 1,100, 0, 'M', 'M', get_text('CA-Cl-M', 'Languages'));
			CreateClass($TourId, $i++, 1,100, 1, 'W', 'W', get_text('CA-Cl-W', 'Languages'));
			break;
	}
}

function CreateStandardEvents($TourId, $SubRule, $Outdoor=true) {
	$TargetR=($Outdoor?5:2);
	$TargetC=($Outdoor?9:4);
	$TargetSizeR=($Outdoor ? 122 : 40);
	$TargetSizeC=($Outdoor ? 80 : 40);
	$DistanceR=($Outdoor ? 70 : 18);
	$DistanceRcm=($Outdoor ? 60 : 18);
	$DistanceC=($Outdoor ? 50 : 18);
	$FirstPhase = ($Outdoor ? 48 : 16);
	switch($SubRule) {
		case '1':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM',  get_text('CA-Ev-RM',  'Languages'), 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RW',  get_text('CA-Ev-RW',  'Languages'), 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RJM', get_text('CA-Ev-RJM', 'Languages'), 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RJW', get_text('CA-Ev-RJW', 'Languages'), 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RCM', get_text('CA-Ev-RCM', 'Languages'), 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RCW', get_text('CA-Ev-RCW', 'Languages'), 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RMM', get_text('CA-Ev-RMM', 'Languages'), 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RMW', get_text('CA-Ev-RMW', 'Languages'), 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM',  get_text('CA-Ev-CM', 'Languages'),  0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW',  get_text('CA-Ev-CW', 'Languages'),  0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CJM', get_text('CA-Ev-CJM', 'Languages'), 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CJW', get_text('CA-Ev-CJW', 'Languages'), 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CCM', get_text('CA-Ev-CCM', 'Languages'), 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CCW', get_text('CA-Ev-CCW', 'Languages'), 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CMM', get_text('CA-Ev-CMM', 'Languages'), 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CMW', get_text('CA-Ev-CMW', 'Languages'), 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RM',  get_text('CA-Ev-RMT',  'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RW',  get_text('CA-Ev-RWT',  'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RJM', get_text('CA-Ev-RJMT', 'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RJW', get_text('CA-Ev-RJWT', 'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RCM', get_text('CA-Ev-RCMT', 'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RCW', get_text('CA-Ev-RCWT', 'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RMM', get_text('CA-Ev-RMMT', 'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RMW', get_text('CA-Ev-RMWT', 'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RX',  get_text('CA-Ev-RX',  'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RJX', get_text('CA-Ev-RJX', 'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RCX', get_text('CA-Ev-RCX', 'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RMX', get_text('CA-Ev-RMX', 'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			}
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CM',  get_text('CA-Ev-CMT',  'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CW',  get_text('CA-Ev-CWT',  'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CJM', get_text('CA-Ev-CJMT', 'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CJW', get_text('CA-Ev-CJWT', 'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CCM', get_text('CA-Ev-CCMT', 'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CCW', get_text('CA-Ev-CCWT', 'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CMM', get_text('CA-Ev-CMMT', 'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CMW', get_text('CA-Ev-CMWT', 'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetC, 4, 4, 2, 4, 4, 2, 'CX',  get_text('CA-Ev-CX',  'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetC, 4, 4, 2, 4, 4, 2, 'CJX', get_text('CA-Ev-CJX', 'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetC, 4, 4, 2, 4, 4, 2, 'CCX', get_text('CA-Ev-CCX', 'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetC, 4, 4, 2, 4, 4, 2, 'CMX', get_text('CA-Ev-CMX', 'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			}
			break;
		case '2':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM', get_text('CA-Ev-RM', 'Languages'), 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RW', get_text('CA-Ev-RW', 'Languages'), 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM', get_text('CA-Ev-CM', 'Languages'), 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW', get_text('CA-Ev-CW', 'Languages'), 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RM', get_text('CA-Ev-RMT', 'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RW', get_text('CA-Ev-RWT', 'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RX', get_text('CA-Ev-RX', 'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			}
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CM', get_text('CA-Ev-CMT', 'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CW', get_text('CA-Ev-CWT', 'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetC, 4, 4, 2, 4, 4, 2, 'CX', get_text('CA-Ev-CX', 'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			}
			break;
		case '3':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM',  get_text('CA-Ev-RM', 'Languages'),  1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RW',  get_text('CA-Ev-RW', 'Languages'),  1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RJM', get_text('CA-Ev-RJM', 'Languages'), 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RJW', get_text('CA-Ev-RJW', 'Languages'), 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM',  get_text('CA-Ev-CM', 'Languages'),  0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW',  get_text('CA-Ev-CW', 'Languages'),  0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CJM', get_text('CA-Ev-CJM', 'Languages'), 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CJW', get_text('CA-Ev-CJW', 'Languages'), 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RM',  get_text('CA-Ev-RMT',  'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RW',  get_text('CA-Ev-RWT',  'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RJM', get_text('CA-Ev-RJMT', 'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RJW', get_text('CA-Ev-RJWT', 'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RX',  get_text('CA-Ev-RX',  'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RJX', get_text('CA-Ev-RJX', 'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			}
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CM',  get_text('CA-Ev-CMT',  'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CW',  get_text('CA-Ev-CWT',  'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CJM', get_text('CA-Ev-CJMT', 'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CJW', get_text('CA-Ev-CJWT', 'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetC, 4, 4, 2, 4, 4, 2, 'CX',  get_text('CA-Ev-CX',  'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetC, 4, 4, 2, 4, 4, 2, 'CJX', get_text('CA-Ev-CJX', 'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			}
			break;
		case '4':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RJM', get_text('CA-Ev-RJM', 'Languages'), 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RJW', get_text('CA-Ev-RJW', 'Languages'), 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RCM', get_text('CA-Ev-RCM', 'Languages'), 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RCW', get_text('CA-Ev-RCW', 'Languages'), 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CJM', get_text('CA-Ev-CJM', 'Languages'), 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CJW', get_text('CA-Ev-CJW', 'Languages'), 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CCM', get_text('CA-Ev-CCM', 'Languages'), 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CCW', get_text('CA-Ev-CCW', 'Languages'), 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RJM', get_text('CA-Ev-RJMT', 'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RJW', get_text('CA-Ev-RJWT', 'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RCM', get_text('CA-Ev-RCMT', 'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetR, 4, 6, 3, 4, 6, 3, 'RCW', get_text('CA-Ev-RCWT', 'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RJX', get_text('CA-Ev-RJX', 'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RCX', get_text('CA-Ev-RCX', 'Languages'), 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			}
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CJM', get_text('CA-Ev-CJMT', 'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CJW', get_text('CA-Ev-CJWT', 'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CCM', get_text('CA-Ev-CCMT', 'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 8, $TargetC, 4, 6, 3, 4, 6, 3, 'CCW', get_text('CA-Ev-CCWT', 'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetC, 4, 4, 2, 4, 4, 2, 'CJX', get_text('CA-Ev-CJX', 'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetC, 4, 4, 2, 4, 4, 2, 'CCX', get_text('CA-Ev-CCX', 'Languages'), 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			}
			break;
	}
}

function InsertStandardEvents($TourId, $SubRule) {
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

			InsertClassEvent($TourId, 1, 3, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 1, 3, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 1, 3, 'RCM', 'R', 'CM');
			InsertClassEvent($TourId, 1, 3, 'RMM', 'R', 'MM');
			InsertClassEvent($TourId, 1, 3, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 1, 3, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 1, 3, 'RCW', 'R', 'CW');
			InsertClassEvent($TourId, 1, 3, 'RMW', 'R', 'MW');
			InsertClassEvent($TourId, 1, 1, 'RX',  'R',  'W');
			InsertClassEvent($TourId, 2, 1, 'RX',  'R',  'M');
			InsertClassEvent($TourId, 1, 1, 'RJX', 'R', 'JW');
			InsertClassEvent($TourId, 2, 1, 'RJX', 'R', 'JM');
			InsertClassEvent($TourId, 1, 1, 'RCX', 'R', 'CW');
			InsertClassEvent($TourId, 2, 1, 'RCX', 'R', 'CM');
			InsertClassEvent($TourId, 1, 1, 'RMX', 'R', 'MW');
			InsertClassEvent($TourId, 2, 1, 'RMX', 'R', 'MM');
			InsertClassEvent($TourId, 1, 3, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 1, 3, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 1, 3, 'CCM', 'C', 'CM');
			InsertClassEvent($TourId, 1, 3, 'CMM', 'C', 'MM');
			InsertClassEvent($TourId, 1, 3, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 1, 3, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 1, 3, 'CCW', 'C', 'CW');
			InsertClassEvent($TourId, 1, 3, 'CMW', 'C', 'MW');
			InsertClassEvent($TourId, 1, 1, 'CX',  'C',  'W');
			InsertClassEvent($TourId, 2, 1, 'CX',  'C',  'M');
			InsertClassEvent($TourId, 1, 1, 'CJX', 'C', 'JW');
			InsertClassEvent($TourId, 2, 1, 'CJX', 'C', 'JM');
			InsertClassEvent($TourId, 1, 1, 'CCX', 'C', 'CW');
			InsertClassEvent($TourId, 2, 1, 'CCX', 'C', 'CM');
			InsertClassEvent($TourId, 1, 1, 'CMX', 'C', 'MW');
			InsertClassEvent($TourId, 2, 1, 'CMX', 'C', 'MM');
			break;
		case '2':
			InsertClassEvent($TourId, 0, 1, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 0, 1, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 0, 1, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 0, 1, 'CW',  'C',  'W');

			InsertClassEvent($TourId, 1, 3, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 1, 3, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 1, 1, 'RX',  'R',  'W');
			InsertClassEvent($TourId, 2, 1, 'RX',  'R',  'M');
			InsertClassEvent($TourId, 1, 3, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 1, 3, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 1, 1, 'CX',  'C',  'W');
			InsertClassEvent($TourId, 2, 1, 'CX',  'C',  'M');
			break;
		case '3':
			InsertClassEvent($TourId, 0, 1, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 0, 1, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 0, 1, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 0, 1, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 0, 1, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 0, 1, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 0, 1, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 0, 1, 'CJW', 'C', 'JW');

			InsertClassEvent($TourId, 1, 3, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 1, 3, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 1, 3, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 1, 3, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 1, 1, 'RX',  'R',  'W');
			InsertClassEvent($TourId, 2, 1, 'RX',  'R',  'M');
			InsertClassEvent($TourId, 1, 1, 'RJX', 'R', 'JW');
			InsertClassEvent($TourId, 2, 1, 'RJX', 'R', 'JM');
			InsertClassEvent($TourId, 1, 3, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 1, 3, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 1, 3, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 1, 3, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 1, 1, 'CX',  'C',  'W');
			InsertClassEvent($TourId, 2, 1, 'CX',  'C',  'M');
			InsertClassEvent($TourId, 1, 1, 'CJX', 'C', 'JW');
			InsertClassEvent($TourId, 2, 1, 'CJX', 'C', 'JM');
			break;
		case '4':
			InsertClassEvent($TourId, 0, 1, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 0, 1, 'RCM', 'R', 'CM');
			InsertClassEvent($TourId, 0, 1, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 0, 1, 'RCW', 'R', 'CW');
			InsertClassEvent($TourId, 0, 1, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 0, 1, 'CCM', 'C', 'CM');
			InsertClassEvent($TourId, 0, 1, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 0, 1, 'CCW', 'C', 'CW');

			InsertClassEvent($TourId, 1, 3, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 1, 3, 'RCM', 'R', 'CM');
			InsertClassEvent($TourId, 1, 3, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 1, 3, 'RCW', 'R', 'CW');
			InsertClassEvent($TourId, 1, 1, 'RJX', 'R', 'JW');
			InsertClassEvent($TourId, 2, 1, 'RJX', 'R', 'JM');
			InsertClassEvent($TourId, 1, 1, 'RCX', 'R', 'CW');
			InsertClassEvent($TourId, 2, 1, 'RCX', 'R', 'CM');
			InsertClassEvent($TourId, 1, 3, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 1, 3, 'CCM', 'C', 'CM');
			InsertClassEvent($TourId, 1, 3, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 1, 3, 'CCW', 'C', 'CW');
			InsertClassEvent($TourId, 1, 1, 'CJX', 'C', 'JW');
			InsertClassEvent($TourId, 2, 1, 'CJX', 'C', 'JM');
			InsertClassEvent($TourId, 1, 1, 'CCX', 'C', 'CW');
			InsertClassEvent($TourId, 2, 1, 'CCX', 'C', 'CM');
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

