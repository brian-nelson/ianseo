<?php

/*

STANDARD THINGS

*/

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'SUI';
if(empty($SubRule)) $SubRule='1';

global $ChDivisions,$ChClasses;

$ChDivisions=array(
	'R' => 'Recurve',
	'C' => 'Compound',
	'BB'=> 'Barebow',
	'BH'=> 'Bowhunter',
	'LB'=> 'Longbow',
	/* Ramon Keller 11.05.2017: Obsolete. We use standard Para classes now: RWO, CMO, WW1, etc. */
	//'PR' => 'Recurve Para',
	//'PC' => 'Compound Para',
	);

$ChClasses=array(
	'H' => 'Hommes',
	'D' => 'Dames',
	'JH'=> 'Junior Hommes',
	'JD'=> 'Junior Dames',
	'VH'=> 'Master Hommes',
	'VD'=> 'Master Dames',
	'CH'=> 'Cadet Hommes',
	'CD'=> 'Cadet Dames',
	/* Ramon Keller 11.05.2017: Jeunesse is unisex since 2017-05-01 */
	//'EH'=> 'Jeunesse Hommes',
	//'ED'=> 'Jeunesse Dames',
	
	/* Ramon Keller 11.05.2017: New definition for Jeunesse: 'JE'*/ 
	'JE'=> 'Jeunesse',
	
	'MI'=> 'Mini',
	'PI'=> 'Piccolo',
	
	// DO NOT ADD PARA CLASSES HERE. ALLOWED DIVISION ATTRIBUTE IS NOT RESPECTED
	//'MO'=> 'Men Open',
	//'WO'=> 'Women Open'
	);

function CreateStandardDivisions($TourId, $Type=1, $SubRule=0) {
	global $ChDivisions;
	$i=1;
	foreach($ChDivisions as $C => $D) {
		CreateDivision($TourId, $i++, $C, $D);
	}
	CreateDivision($TourId, $i++, 'GR', 'Guests Recurve');
	CreateDivision($TourId, $i++, 'GC', 'Guests Compound');
	CreateDivision($TourId, $i++, 'GB', 'Guests without sight');
}

function CreateStandardClasses($TourId, $SubRule, $Field='', $Type=0) {
	$i=1;
	CreateClass($TourId, $i++, 21, 49, 0, 'H', 'H', 'Hommes');
	CreateClass($TourId, $i++, 21, 49, 1, 'D', 'D', 'Dames');
	CreateClass($TourId, $i++, 18, 20, 0, 'JH', 'JH,H', 'Junior Hommes');
	CreateClass($TourId, $i++, 18, 20, 1, 'JD', 'JD,D', 'Junior Dames');
	CreateClass($TourId, $i++, 50, 99, 0, 'VH', 'VH,H', 'Master Hommes');
	CreateClass($TourId, $i++, 50, 99, 1, 'VD', 'VD,D', 'Master Dames');
	CreateClass($TourId, $i++, 15, 17, 0, 'CH', 'CH,JH,H', 'Cadet Hommes');
	CreateClass($TourId, $i++, 15, 17, 1, 'CD', 'CD,JD,D', 'Cadet Dames');
	/* Ramon Keller 11.05.2017: Jeunesse is unisex since 2017-05-01 */
	//CreateClass($TourId, $i++, 13, 14, 0, 'EH', 'EH,CH,JH,H', 'Jeunesse Hommes');
	//CreateClass($TourId, $i++, 13, 14, 1, 'ED', 'ED,CD,JD,D', 'Jeunesse Dames');
	/* Ramon Keller 11.05.2017: New definition for Jeunesse': */
	CreateClass($TourId, $i++, 13, 14, -1, 'JE', 'JE,CD,CH,JD,JH,D,H', 'Jeunesse'); 
	/* Ramon Keller 11.05.2017: Updated 'Allowed Classes' for 'MI' and 'PI' */
	CreateClass($TourId, $i++, 11, 12, -1, 'MI', 'MI,JE,CD,CH,JD,JH,D,H', 'Mini'); 
	CreateClass($TourId, $i++,  1, 10, -1, 'PI', 'PI,MI,JE,CD,CH,JD,JH,D,H', 'Piccolo');
	CreateClass($TourId, $i++, 1, 99, 0, 'MO', 'MO', 'Men Open', 1, 'C,R');
	CreateClass($TourId, $i++, 1, 99, 1, 'WO', 'WO', 'Women Open', 1, 'C,R');
}

function CreateStandardSubClasses($TourId) {
//	$i=1;
//	CreateSubClass($TourId, $i++, '01', '01');
//	CreateSubClass($TourId, $i++, '02', '02');
//	CreateSubClass($TourId, $i++, '03', '03');
//	CreateSubClass($TourId, $i++, '04', '04');
}

function CreateStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
	global $ChDivisions, $ChClasses;
	$i=1;
	$TargetR=($Outdoor ? 5 : 2);
	$TargetC=($Outdoor ? 9 : 4);

	$TgtSize =  ($Outdoor ?  122 : 40);
	$Distance =  ($Outdoor ?  70 : 18);
	$Set = 0;

	//Skip individual events for Indoor 25m
	if($TourType==3 or $TourType==6){
		foreach($ChDivisions as $cD => $D) {
		
			
			/* 'PR' and 'PC' are Obsolete: See above in divisions section */
			//if($cD=='PR' or $cD=='PC') continue; // not for the para divisions...
			$Target=($cD=='C' ? $TargetC : $TargetR);
			$Set=($cD=='C' ? 0 : 1);
			if($cD=='BH' || $cD=='LB') {
				$TgtSize = ($Outdoor ?  122 : 60);
				$Distance = ($Outdoor ?  30 : 18);
			} else if ($cD=='C') {
				$TgtSize = ($Outdoor ?  80 : 40);
				$Distance = ($Outdoor ?  50 : 18);
			} else if ($cD=='BB') {
				$TgtSize = ($Outdoor ?  80 : 40);
				$Distance = ($Outdoor ?  30 : 18);
			}
			foreach($ChClasses as $cC => $C) {
				if($cD=='R' && ($cC=='CH' || $cC=='CD' || $cC=='VH' || $cC=='VD')) {
					$Distance = ($Outdoor ?  60 : 18);
				}
				
				/* Ramon Keller 11.05.2017: Jeunesse is unisex since 2017-05-01 */
				// Replaced 'if($cC=='EH' || $cC=='ED') {' with next line
				if($cC=='JE') {
					$TgtSize = ($Outdoor ?  122 : 60);
					$Distance = ($Outdoor ?  (($cD=='C' || $cD=='R') ? 40:30) : 18);
				}
				if($cC=='PI' || $cC=='MI') {
					$TgtSize=80;
					$Distance = ($Outdoor ?  ($cC=='PI' ? 15 : 25) : ($cC=='PI' ? 15 : 18));
				}

				/* Ramon Keller 11.05.2017: Do not create '1/16' events for every division/class leave all on '--' and configure them manually */
				//CreateEvent($TourId, $i++, 0, 0,16, $Target, 5, 3, 1, 5, 3, 1, $cD.$cC,  "$D $C", $Set, 240, 0, 0, 0, '', '', $TgtSize, $Distance);
				CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, $cD.$cC,  "$D $C", $Set, 240, 0, 0, 0, '', '', $TgtSize, $Distance);
			}
		}
		CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'GW',  "Guests with sight", $Set, 240, 0, 0, 0, '', '', $TgtSize, $Distance);
		CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'GO',  "Guests without sight", $Set, 240, 0, 0, 0, '', '', $TgtSize, $Distance);


		// create "standard" Para events: CMO, CWO, RMO, RWO, WW1, MW1
		$TargetR=($Outdoor ? 5 : 2);
		$TargetC=($Outdoor ? 9 : 4);

		$TgtSize =  ($Outdoor ?  122 : 40);
		$Distance =  ($Outdoor ?  70 : 18);

		CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RMO',  "Recurve Men Open", 1, 240, 0, 0, 0, '', '', $TgtSize, $Distance);
		CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RWO',  "Recurve Women Open", 1, 240, 0, 0, 0, '', '', $TgtSize, $Distance);

		$TgtSize =  ($Outdoor ?  80 : 40);
		$Distance =  ($Outdoor ?  50 : 18);
		CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CMO',  "Compound Men Open", 0, 240, 0, 0, 0, '', '', $TgtSize, $Distance);
		CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CWO',  "Compound Women Open", 0, 240, 0, 0, 0, '', '', $TgtSize, $Distance);
		CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'MW1',  "Men W1", 0, 240, 0, 0, 0, '', '', $TgtSize, $Distance);
		CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'WW1',  "Women W1", 0, 240, 0, 0, 0, '', '', $TgtSize, $Distance);

	}
	
	/* Ramon Keller 11.05.2017: Introduced Team Events also for WA70/50m Round */
	if($TourType == 3){
		//$i=1;
		foreach($ChDivisions as $cD => $D) {
			$Target=($cD=='C' ? $TargetC : $TargetR);
			
			if($cD=='R'){
				// Recurve 70m & 60m Teams
				CreateEvent($TourId, $i++, 1, 0, 0, $Target, 4, 6, 3, 4, 6, 3, $cD.'T60',  $D . ' Team 60m', 1, 240, 0, 0, 0, '', '', 122, 60);
				CreateEvent($TourId, $i++, 1, 0, 0, $Target, 4, 6, 3, 4, 6, 3, $cD.'T70',  $D . ' Team 70m ', 1, 240, 0, 0, 0, '', '', 122, 70);
				CreateEvent($TourId, $i++, 1, 0, 0, $Target, 4, 6, 3, 4, 6, 3, $cD.'JT',  $D . ' Team Jeunes', 1, 240, 0, 0, 0, '', '', 122, 40);
			} else {
				// C, BB, BH, LB
				CreateEvent($TourId, $i++, 1, 0, 0, $Target, 4, 6, 3, 4, 6, 3, $cD.'T',  $D . ' Team', ($cD=='C' ? 0 : 1), 240, 0, 0, 0, '', '', ($cD=='C' ||$cD=='BB' ? 80 : 122), ($cD=='C' ? 50 : 30));				
				CreateEvent($TourId, $i++, 1, 0, 0, $Target, 4, 6, 3, 4, 6, 3, $cD.'JT',  $D . ' Team Jeunes', ($cD=='C' ? 0 : 1), 240, 0, 0, 0, '', '', 122, ($cD=='C' ? 40 : 30));
			}
		}
	}
	/* End of edit by Ramon */

	if($TourType==6) {
		//$i=1;
		foreach($ChDivisions as $cD => $D) {
			/* Ramon Keller 11.05.2017: 'PR' and 'PC' are Obsolete: See above in divisions section */
			//if($cD=='PR' or $cD=='PC') continue; // not for the para divisions...
			$Target=($cD=='C' ? $TargetC : $TargetR);
			CreateEvent($TourId, $i++, 1, 0, 0, $Target, 4, 6, 3, 4, 6, 3, $cD.'T',  $D . ' Team', ($cD=='C' ? 0 : 1), 240, 0, 0, 0, '', '', ($cD=='BH' || $cD=='LB' ? 60 : 40), 18);
			CreateEvent($TourId, $i++, 1, 0, 0, $Target, 4, 6, 3, 4, 6, 3, $cD.'JT', $D . ' Team Jeunes', ($cD=='C' ? 0 : 1), 240, 0, 0, 0, '', '', 60, 18);
		}
	}
	
	/* Ramon Keller 11.05.2017: Team Events for Indoor 25m */
	if($TourType==7) {
		//$i=1;
		foreach($ChDivisions as $cD => $D) {
			$Target=($cD=='C' ? $TargetC : $TargetR);
			CreateEvent($TourId, $i++, 1, 0, 0, $Target, 4, 6, 3, 4, 6, 3, $cD.'T',  $D . ' Team', ($cD=='C' ? 0 : 1), 240, 0, 0, 0, '', '', ($cD=='BH' || $cD=='LB' ? 80 : 60), 25);
			CreateEvent($TourId, $i++, 1, 0, 0, $Target, 4, 6, 3, 4, 6, 3, $cD.'JT', $D . ' Team Jeunes', ($cD=='C' ? 0 : 1), 240, 0, 0, 0, '', '', 80, 25);
		}
	}
	/* End of edit by Ramon */
	
}

function InsertStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
	Global $ChDivisions, $ChClasses;
	if($TourType==6 or $TourType==7) {
		// Indoor 18m
		foreach($ChDivisions as $cD => $D) {
			foreach($ChClasses as $cC => $C) {
				// individual events
				InsertClassEvent($TourId, 0, 1, $cD.$cC, $cD, $cC);

				// team events
				if(in_array($cC, array('H','D','JH','JD','VH','VD','CH','CD'))) InsertClassEvent($TourId, 1, 3, $cD.'T', $cD, $cC);
				/* Ramon Keller 11.05.2017: Jeunesse unisex change */
				//elseif(in_array($cC, array('EH','ED','MI','PI'))) InsertClassEvent($TourId, 1, 3, $cD.'JT', $cD, $cC);
				elseif(in_array($cC, array('JE','MI','PI'))) InsertClassEvent($TourId, 1, 3, $cD.'JT', $cD, $cC);
			}
		}
	} else if($TourType==3){
		// Outdoor 70m
		foreach($ChDivisions as $cD => $D) {	
			foreach($ChClasses as $cC => $C) {
				// individual events
				InsertClassEvent($TourId, 0, 1, $cD.$cC, $cD, $cC);
				
				// team events
				switch(true) {
					/* Ramon Keller 11.05.2017: Jeunesse unisex change */
					//case (in_array($cC, array('EH','ED','MI','PI'))):
					case (in_array($cC, array('JE','MI','PI'))):
						InsertClassEvent($TourId, 1, 3, $cD.'JT', $cD, $cC);
						break;
					case ($cD=='C'):
						InsertClassEvent($TourId, 1, 3, $cD.'T', $cD, $cC);
						break;
					case ($cD!='R'):
						InsertClassEvent($TourId, 1, 3, $cD.'T', $cD, $cC);
						break;
					case (in_array($cC, array('H','D','JH','JD'))):
						InsertClassEvent($TourId, 1, 3, $cD.'70T', $cD, $cC);
						break;
					default:
						InsertClassEvent($TourId, 1, 3, $cD.'60T', $cD, $cC);
						break;
				}
				
			}
			
		}
	}
	
	/* Ramon Keller 11.05.2017: Configure Para individual Events for Recurve & Compound 
		Ok. They will probably never have any event. But it should be configured anyways */
	foreach($ChDivisions as $cD => $D) {
		InsertClassEvent($TourId, 0, 1, $cD.'MO', $cD, 'MO');
		InsertClassEvent($TourId, 0, 1, $cD.'WO', $cD, 'WO');
	}
	
	InsertClassEvent($TourId, 0, 1, 'MW1', 'W1', 'M');
	InsertClassEvent($TourId, 0, 1, 'WW1', 'W1', 'W');
	
	/* End of edit by Ramon */
		
	foreach($ChClasses as $ClCode => $clDesc) {
		InsertClassEvent($TourId, 0, 1, 'GW', 'GR', $ClCode);
		InsertClassEvent($TourId, 0, 1, 'GW', 'GC', $ClCode);
		InsertClassEvent($TourId, 0, 1, 'GO', 'GB', $ClCode);
	}
}

