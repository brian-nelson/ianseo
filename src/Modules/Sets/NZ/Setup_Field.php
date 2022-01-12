<?php
/*

Common setup for Field

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, 'FIELD');

// default SubClasses
CreateSubClass($TourId, 1, 'NZ', 'New Zealand');
CreateSubClass($TourId, 2, 'IN', 'International');
CreateSubClass($TourId, 3, 'OP', 'Open');

// default Classes
CreateStandardFieldClasses($TourId, $SubRule);

// default Distances
switch($TourType) {
	case 9: // Type_HF 12+12 	(1 distance)
		CreateDistanceNew($TourId, $TourType, '%', array(array('Course',0)));
		break;
	case 10: // Type_HF 24+24 	(2 distances)
	case 12: // Type_HF 12+12 	(2 distances)
		CreateDistanceNew($TourId, $TourType, '%', array(array('Unmarked',0), array('Marked',0)));
		break;
}

// default Events
CreateStandardFieldEvents($TourId, $SubRule);

// insert class in events
InsertStandardFieldEvents($TourId, $SubRule);

// Elimination rounds
InsertStandardFieldEliminations($TourId, $SubRule);

// Finals & TeamFinals
CreateFinals($TourId);

// Default Target
/*
ArcheryNZ customisation
9 - default WA field round (Unmarked or Marked) 24 targets
10 & 12 - assume WA Unmarked round + WA Marked round
	default target peg plus optional target pegs for NZ 3D Unmarked + WA Marked
*/

switch($TourType) {
	case 9:
		switch ($SubRule) {
			case '1':
				$i=1;
				CreateTargetFace($TourId, $i++, 'Red Peg', 'X%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RV%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RM%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RS%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RJ%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RC%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RI%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RY%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RK%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CV%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CM%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CS%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CJ%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CC%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CI%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CY%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CK%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BV%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BM%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BS%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BJ%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BC%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BI%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BY%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BK%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'L%', '1', 6, 0);
				break;
			case '2':
				$i=1;
				CreateTargetFace($TourId, $i++, 'Red Peg', 'X%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RV%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RM%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RS%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RJ%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RC%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CV%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CM%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CS%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CJ%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CC%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BV%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BM%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BS%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BJ%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BC%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'L%', '1', 6, 0);
				break;
			case '3':
				$i=1;
				CreateTargetFace($TourId, $i++, 'Red Peg', 'X%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RJ%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RC%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RI%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RY%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RK%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CJ%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CC%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CI%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CY%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CK%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BJ%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BC%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BI%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BY%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BK%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'L%', '1', 6, 0);
				break;
		}
		break;
	case 10:
	case 12:
		switch ($SubRule) {
			case '1':
				$i=1;
				// Default WA round target pegs
				CreateTargetFace($TourId, $i++, 'Red Peg', 'X%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RV%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RM%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RS%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RJ%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RC%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RI%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RY%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RK%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CV%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CM%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CS%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CJ%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CC%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CI%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CY%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CK%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BV%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BM%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BS%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BJ%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BC%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BI%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BY%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BK%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'L%', '1', 6, 0, 6, 0);
				// Optional NZ-3D/WA-Marked round target pegs
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Red', 'X%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Red', 'RV%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Red', 'RM%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Red', 'RS%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Red', 'RJ%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Blu', 'RC%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Blu', 'RI%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'RY%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'RK%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Red', 'CV%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Red', 'CM%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Red', 'CS%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Red', 'CJ%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Blu', 'CC%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Blu', 'CI%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'CY%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'CK%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Blu', 'BV%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Blu', 'BM%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Blu', 'BS%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Blu', 'BJ%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'BC%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'BI%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'BY%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'BK%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'L%', '0', 6, 0, 6, 0);
				break;
			case '2':
				$i=1;
				// Default WA round target pegs
				CreateTargetFace($TourId, $i++, 'Red Peg', 'X%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RV%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RM%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RS%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RJ%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RC%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CV%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CM%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CS%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CJ%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CC%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BV%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BM%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BS%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BJ%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BC%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'L%', '1', 6, 0, 6, 0);
				// Optional NZ-3D/WA-Marked round target pegs
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Red', 'X%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Red', 'RV%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Red', 'RM%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Red', 'RS%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Red', 'RJ%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Blu', 'RC%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Blu', 'RI%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'RY%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'RK%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Red', 'CV%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Red', 'CM%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Red', 'CS%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Red', 'CJ%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Blu', 'CC%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Blu', 'CI%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'CY%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'CK%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Blu', 'BV%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Blu', 'BM%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Blu', 'BS%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Blu', 'BJ%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'BC%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'BI%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'BY%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'BK%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'L%', '0', 6, 0, 6, 0);
				break;
			case '3':
				$i=1;
				// Default WA round target pegs
				CreateTargetFace($TourId, $i++, 'Red Peg', 'X%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RJ%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RC%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CJ%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CC%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BJ%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BC%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'L%', '1', 6, 0, 6, 0);
				// Optional NZ-3D/WA-Marked round target pegs
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Red', 'X%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Red', 'RJ%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Blu', 'RC%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Blu', 'RI%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'RY%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'RK%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Red', 'CJ%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Red / D2 Blu', 'CC%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Blu', 'CI%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'CY%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'CK%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Blu', 'BJ%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'BC%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'BI%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'BY%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'BK%', '0', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'D1 Blu / D2 Ylw', 'L%', '0', 6, 0, 6, 0);
				break;
		}
		break;
}


// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, $tourDetNumEnds, 4);

// Update Tour details
$tourDetails=array(
	'ToCollation' => $tourCollation,
	'ToTypeName' => $tourDetTypeName,
	'ToNumDist' => $tourDetNumDist,
	'ToNumEnds' => $tourDetNumEnds,
	'ToMaxDistScore' => $tourDetMaxDistScore,
	'ToMaxFinIndScore' => $tourDetMaxFinIndScore,
	'ToMaxFinTeamScore' => $tourDetMaxFinTeamScore,
	'ToCategory' => $tourDetCategory,
	'ToElabTeam' => $tourDetElabTeam,
	'ToElimination' => $tourDetElimination,
	'ToGolds' => $tourDetGolds,
	'ToXNine' => $tourDetXNine,
	'ToGoldsChars' => $tourDetGoldsChars,
	'ToXNineChars' => $tourDetXNineChars,
	'ToDouble' => $tourDetDouble,
//	'ToIocCode'	=> $tourDetIocCode,
	);
UpdateTourDetails($TourId, $tourDetails);

?>