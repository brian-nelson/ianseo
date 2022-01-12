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

	/*
	SwissArchery Guest Divisions (GR, GC, GB, GI) are be added in function CreateStandardDivisions()
	further below to avoid generating match play rounds for guests
	*/

);

$ChClasses=array(
	/* WorldArchery Age Classes */
	'VH'=> 'Master Hommes',
	'VD'=> 'Master Dames',
	'H' => 'Hommes',
	'D' => 'Dames',
	'JH'=> 'Junior Hommes',
	'JD'=> 'Junior Dames',
	'CH'=> 'Cadet Hommes',
	'CD'=> 'Cadet Dames',

	/* SwissArchery Age Classes */
	'JE'=> 'Jeunesse',
	'MI'=> 'Mini',
	'PI'=> 'Piccolo'
);

function CreateStandardDivisions($TourId, $Type=1, $SubRule=0) {
	global $ChDivisions;
	$i=1;
	foreach($ChDivisions as $C => $D) {
		CreateDivision($TourId, $i++, $C, $D);
	}

	/* Add SwissArchery Guest Divisions here to avoid generating match play rounds for guests */
	CreateDivision($TourId, $i++, 'GR', 'Guest Recurve');
	CreateDivision($TourId, $i++, 'GC', 'Guest Compound');
	CreateDivision($TourId, $i++, 'GB', 'Guest Barebow'); // Target & 3D: Barebow only / Field: Barebow & Bowhunter (workaround)
	CreateDivision($TourId, $i++, 'GI', 'Guest Instinctive'); // Target & 3D: Bowhunter & Longbow / Field: Longbow only!

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
	CreateClass($TourId, $i++, 13, 14, -1, 'JE', 'JE,CD,CH,JD,JH,D,H', 'Jeunesse');
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

	// Create (Match Play) events for WA 70m Round (3), WA Indoor 18 (6)
	if($TourType===3 || $TourType===6){

		// Loop through all official divisions and classes and create their event
		foreach($ChDivisions as $cD => $D) {
		
			$Target=($cD==='C' ? $TargetC : $TargetR);

			// Define if SET Match Play mode: Off for Compound, On for all the others
			$Set=($cD==='C' ? 0 : 1);

			// Define default target size & distance per divison
			if($cD==='BH' || $cD==='LB') {
				$TgtSize = ($Outdoor ?  122 : 60);
				$Distance = ($Outdoor ?  30 : 18);

				// Full Face also in Indoor (type 1)
				$Target = ($Outdoor ? $TargetR : 1);
			} else if ($cD==='C') {
				$TgtSize = ($Outdoor ?  80 : 40);
				$Distance = ($Outdoor ?  50 : 18);
			} else if ($cD==='BB') {
				$TgtSize = ($Outdoor ?  122 : 40);
				$Distance = ($Outdoor ?  50 : 18);
			}

			foreach($ChClasses as $cC => $C) {

				// Re-define target size and distance for those age classes which deviate from the default
				$Dist = $Distance;

				// Outdoor WA 70/50: 60m for RC_ and RV_
				if($cD==='R' && ($cC==='CH' || $cC==='CD' || $cC==='VH' || $cC==='VD')) {
					$Dist = ($Outdoor ?  60 : 18);
				}

				// Outdoor WA 70/50: Jeunesse R/C/BB: 40m, BH/LB: 30m
				if($cC==='JE') {
					$TgtSize = ($Outdoor ?  122 : 60);
					$Dist = ($Outdoor ?  (($cD==='C' || $cD==='R' || $cD==='BB') ? 40:30) : 18);

					// Full Face also in Indoor for Barebow (BH & LB config is already Full Face)
					$Target = ($Outdoor ? $TargetR : ($cD === 'BB' ? 1 : $Target));
				}

				// Outdoor WA 70/50: Piccolo: 15m, Mini: 25m @ 80cm full face (type 5)
				// Indoor 18m: Piccolo: 15m, Mini 18m @80cm full face inner ten (type 3)
				if($cC==='PI' || $cC==='MI') {
					$TgtSize=80;
					$Dist = ($Outdoor ?  ($cC==='PI' ? 15 : 25) : ($cC==='PI' ? 15 : 18));

					// Full Face also in Indoor. (Type 3 for Compound, Type 1 for all the others)
					$Target = ($Outdoor ? $TargetR : ($cD === 'C' ? 3 : 1 ));
				}

				/* Ramon Keller 11.05.2017: Do not create '1/16' events for every division/class leave all on '--' and configure them manually: Put 'FirstPhase' param to 0 */
				CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, $cD.$cC,  "$D $C", $Set, 240, 0, 0, 0, '', '', $TgtSize, $Dist);
			}
		}

		// Create Para events: CMO, CWO, RMO, RWO, WW1, MW1
		$TargetR=($Outdoor ? 5 : 2);
		$TargetC=($Outdoor ? 9 : 4);

		$TgtSize =  ($Outdoor ?  122 : 40);
		$Distance =  ($Outdoor ?  70 : 18);

		CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RMO', 'Recurve Men Open', 1, 240, 0, 0, 0, '', '', $TgtSize, $Distance);
		CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RWO', 'Recurve Women Open', 1, 240, 0, 0, 0, '', '', $TgtSize, $Distance);

		$TgtSize =  ($Outdoor ?  80 : 40);
		$Distance =  ($Outdoor ?  50 : 18);
		CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CMO', 'Compound Men Open', 0, 240, 0, 0, 0, '', '', $TgtSize, $Distance);
		CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CWO', 'Compound Women Open', 0, 240, 0, 0, 0, '', '', $TgtSize, $Distance);
		CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'MW1', 'Men W1', 0, 240, 0, 0, 0, '', '', $TgtSize, $Distance);
		CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'WW1', 'Women W1', 0, 240, 0, 0, 0, '', '', $TgtSize, $Distance);

	}
	
	/* Team Events for WA70/50m Round */
	if($TourType === 3){

		foreach($ChDivisions as $cD => $D) {
			$Target=($cD==='C' ? $TargetC : $TargetR);
			
			if($cD==='R'){
				// Recurve 70m & 60m Teams
				CreateEvent($TourId, $i++, 1, 0, 0, $Target, 4, 6, 3, 4, 6, 3, $cD.'T60',  $D . ' Team 60m', 1, 240, 0, 0, 0, '', '', 122, 60);
				CreateEvent($TourId, $i++, 1, 0, 0, $Target, 4, 6, 3, 4, 6, 3, $cD.'T70',  $D . ' Team 70m ', 1, 240, 0, 0, 0, '', '', 122, 70);
				CreateEvent($TourId, $i++, 1, 0, 0, $Target, 4, 6, 3, 4, 6, 3, $cD.'JT',  $D . ' Team Jeunes', 1, 240, 0, 0, 0, '', '', 122, 40);
			} else {
				// C, BB, BH, LB
				CreateEvent($TourId, $i++, 1, 0, 0, $Target, 4, 6, 3, 4, 6, 3, $cD.'T',  $D . ' Team', ($cD=='C' ? 0 : 1), 240, 0, 0, 0, '', '', ($cD==='C' ? 80 : 122), (($cD==='C' || $cD==='BB') ? 50 : 30));
				CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, $cD.'JT',  $D . ' Team Jeunes', ($cD=='C' ? 0 : 1), 240, 0, 0, 0, '', '', 122, (($cD==='C' || $cD==='BB') ? 40 : 30));
			}
		}
	}
	/* End of edit by Ramon */

	/* Team Events for Indoor 18m */
	if($TourType===6) {
		//$i=1;
		foreach($ChDivisions as $cD => $D) {
			/* Ramon Keller 11.05.2017: 'PR' and 'PC' are Obsolete: See above in divisions section */
			//if($cD=='PR' or $cD=='PC') continue; // not for the para divisions...
			$Target=($cD==='C' ? $TargetC : $TargetR);
			CreateEvent($TourId, $i++, 1, 0, 0, $Target, 4, 6, 3, 4, 6, 3, $cD.'T',  $D . ' Team', ($cD=='C' ? 0 : 1), 240, 0, 0, 0, '', '', ($cD=='BH' || $cD=='LB' ? 60 : 40), 18);
			CreateEvent($TourId, $i++, 1, 0, 0, $Target, 4, 6, 3, 4, 6, 3, $cD.'JT', $D . ' Team Jeunes', ($cD=='C' ? 0 : 1), 240, 0, 0, 0, '', '', 60, 18);
		}
	}
	
	/* Team Events for Indoor 25m */
	if($TourType===7) {
		//$i=1;
		foreach($ChDivisions as $cD => $D) {
			$Target=($cD==='C' ? $TargetC : $TargetR);
			CreateEvent($TourId, $i++, 1, 0, 0, $Target, 4, 6, 3, 4, 6, 3, $cD.'T',  $D . ' Team', ($cD=='C' ? 0 : 1), 240, 0, 0, 0, '', '', ($cD=='BH' || $cD=='LB' ? 80 : 60), 25);
			CreateEvent($TourId, $i++, 1, 0, 0, $Target, 4, 6, 3, 4, 6, 3, $cD.'JT', $D . ' Team Jeunes', ($cD=='C' ? 0 : 1), 240, 0, 0, 0, '', '', 80, 25);
		}
	}

	/* Individual & Team Events for Field */
	if($TourType===9){
		// IMPORTANT:
		// Event configurations for FIELD are only required in order to have a calculation base for
		// the SwissArchery 'SAA Diploma' custom module.
		// SwissArchery currently does not execute any eliminations or finals in FIELD.

		// Individual Events
		$SettingsInd=array(
			'EvFinalFirstPhase' => '0',
			'EvFinalTargetType'=>TGT_FIELD,
			'EvElimEnds'=>12,
			'EvElimArrows'=>3,
			'EvElimSO'=>1,
			'EvFinEnds'=>4,
			'EvFinArrows'=>3,
			'EvFinSO'=>1,
			'EvElimType'=>2,
			'EvElim1'=>16,
			'EvE1Ends'=>12,
			'EvE1Arrows'=>3,
			'EvE1SO'=>1,
			'EvElim2'=>8,
			'EvE2Ends'=>8,
			'EvE2Arrows'=>3,
			'EvE2SO'=>1,
			'EvFinalAthTarget'=>0,
			'EvMatchArrowsNo'=>0
		);
		$SettingsTeam=array(
			'EvTeamEvent' => '1',
			'EvFinalFirstPhase' => '0',
			'EvFinalTargetType'=>TGT_FIELD,
			'EvElimEnds'=>8,
			'EvElimArrows'=>3,
			'EvElimSO'=>3,
			'EvFinEnds'=>4,
			'EvFinArrows'=>3,
			'EvFinSO'=>3,
			'EvFinalAthTarget'=>15,
			'EvMatchArrowsNo'=>FINAL_FROM_2,
		);

		// Loop through all official divisions and classes and create their events
		foreach ($ChDivisions as $cD => $D) {
			foreach ( $ChClasses as $cC => $C ) {
				CreateEventNew( $TourId, $cD . $cC, "$D $C", $i ++, $SettingsInd );

			}
		}

		foreach ($ChDivisions as $cD => $D) {
			// Team Events
			switch($cD){
				case 'R':
				case 'C':
				case 'BB':
				case 'BH':
					CreateEventNew($TourId, $cD.'JT', "$D Team Jeunes", $i++, $SettingsTeam); // U11 - U15
					CreateEventNew($TourId, $cD.'CT', "$D Team Cadets", $i++, $SettingsTeam); // U18
					CreateEventNew($TourId, $cD, "$D Team", $i++, $SettingsTeam); // U21 - Master
					break;
				case 'LB':
					CreateEventNew($TourId, $cD.'JT', "$D Team Jeunes", $i++, $SettingsTeam); // U11 - U15
					CreateEventNew($TourId, $cD.'T', "$D Team", $i++, $SettingsTeam); // U18 - Master
					break;
				default:
					break;
			}

		}
	}

	/* Individual & Team Events for 3D */
	if($TourType===11) {
		// IMPORTANT:
		// Event configurations for 3D are only required in order to have a calculation base for
		// the SwissArchery 'SAA Diploma' custom module.
		// SwissArchery currently does not execute any eliminations or finals in FIELD.

		// Individual Events
		$SettingsInd = array(
			'EvFinalFirstPhase' => '0',
			'EvFinalTargetType' => TGT_3D,
			'EvElimEnds' => 12,
			'EvElimArrows' => 1,
			'EvElimSO' => 1,
			'EvFinEnds' => 4,
			'EvFinArrows' => 1,
			'EvFinSO' => 1,
			'EvElimType' => 2,
			'EvElim1' => 16,
			'EvE1Ends' => 12,
			'EvE1Arrows' => 1,
			'EvE1SO' => 1,
			'EvElim2' => 8,
			'EvE2Ends' => 8,
			'EvE2Arrows' => 1,
			'EvE2SO' => 1,
			'EvFinalAthTarget' => MATCH_NO_SEP,
			'EvMatchArrowsNo' => FINAL_FROM_2
		);
		$SettingsTeam = array(
			'EvTeamEvent' => '1',
			'EvFinalFirstPhase' => '0',
			'EvFinalTargetType' => TGT_3D,
			'EvElimEnds' => 8,
			'EvElimArrows' => 3,
			'EvElimSO' => 3,
			'EvFinEnds' => 4,
			'EvFinArrows' => 3,
			'EvFinSO' => 3,
			'EvFinalAthTarget' => MATCH_NO_SEP,
			'EvMatchArrowsNo' => FINAL_FROM_2,
		);

		foreach ($ChDivisions as $cD => $D){
			foreach ($ChClasses as $cC => $C) {
				CreateEventNew($TourId, $cD.$cC, "$D $C", $i++, $SettingsInd);
			}
		}

		foreach ($ChDivisions as $cD => $D) {
			CreateEventNew($TourId, $cD.'JT', "$D Team Jeunes", $i++, $SettingsTeam); // U11 - U15
			CreateEventNew($TourId, $cD.'T', "$D Team", $i++, $SettingsTeam); // U18 - Master
		}
	}
}

function InsertStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
	Global $ChDivisions, $ChClasses;
	if($TourType===6 || $TourType===7) {
		// Indoor 18m (6) & WA Indoor 25m (7)
		foreach($ChDivisions as $cD => $D) {
			foreach($ChClasses as $cC => $C) {
				// individual events
				InsertClassEvent($TourId, 0, 1, $cD.$cC, $cD, $cC);

				// team events
				if(in_array($cC, array('H','D','JH','JD','VH','VD','CH','CD'))) InsertClassEvent($TourId, 1, 3, $cD.'T', $cD, $cC);
				elseif(in_array($cC, array('JE','MI','PI'))) InsertClassEvent($TourId, 1, 3, $cD.'JT', $cD, $cC);
			}
		}
	} else if($TourType===3){
		// Outdoor 70/50m
		foreach($ChDivisions as $cD => $D) {	
			foreach($ChClasses as $cC => $C) {
				// individual events
				InsertClassEvent($TourId, 0, 1, $cD.$cC, $cD, $cC);
				
				// team events
				switch(true) {
					case (in_array($cC, array('JE','MI','PI'))):
						InsertClassEvent($TourId, 1, 3, $cD.'JT', $cD, $cC);
						break;
					case ($cD === 'R' ):
						if(in_array($cC, array('H','D','JH','JD'))){
							$eventCode = $cD.'T70';
						} else if (in_array($cC, array('VH','VD','CH','CD'))){
							$eventCode = $cD.'T60';
						}
						InsertClassEvent($TourId, 1, 3, $eventCode, $cD, $cC);
						break;
					default:
						InsertClassEvent($TourId, 1, 3, $cD.'T', $cD, $cC);
						break;
				}
				
			}
			
		}
	} else if($TourType===9){
		// Field
		foreach($ChDivisions as $cD => $D){
			foreach($ChClasses as $cC => $C){
				InsertClassEvent($TourId, 0, 1, $cD.$cC, $cD, $cC);

				if(in_array($cC, array('JE','MI','PI'))){
					// JE,MI,PI of all Divisions
					InsertClassEvent($TourId, 1, 3, $cD.'JT', $cD, $cC);
				} else if(in_array($cD, array('R','C','BB','BH'))){
					// All R|C|BB|BH except JE,MI,PI
					if(in_array($cC, array('JD','JH','D','H','VD','VH'))){
						// All Except Cadets
						InsertClassEvent($TourId, 1, 3, $cD.'T', $cD, $cC);
					} else {
						// Cadets
						InsertClassEvent($TourId, 1, 3, $cD.'CT', $cD, $cC);
					}
				} else if($cD === 'LB'){
					// All LB except JE,MI,PI
					InsertClassEvent($TourId, 1, 3, $cD, $cD, $cC);
				}
			}
		}
	} else if($TourType===11){
		// 3D
		foreach ($ChDivisions as $cD => $D){
			foreach ($ChClasses as $cC => $C){
				InsertClassEvent($TourId, 0, 1, $cD.$cC, $cD, $cC);

				if(in_array($cC, array('JE','MI','PI'))){
					// JE,MI,PI of all Divisions
					InsertClassEvent($TourId, 1, 3, $cD.'JT', $cD, $cC);
				} else if(in_array($cC, array('CD','CH','JD','JH','D','H','VD','VH'))){
					InsertClassEvent($TourId, 1, 3, $cD.'T', $cD, $cC);
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

}

