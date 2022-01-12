<?php
/*
Common Setup for "Target" Archery
*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId,$TourType);

// default SubClasses
CreateSubClass($TourId, 1, 'NZ', 'New Zealand');
CreateSubClass($TourId, 2, 'IN', 'International');
CreateSubClass($TourId, 3, 'OP', 'Open');

// default Classes
CreateStandardClasses($TourId, $SubRule);

// default Distances
switch($TourType) {
	case 1: //WA-1440 (FITA)
	case 4: //FITA-72
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, 'RSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RVM', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RVW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CVM', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CVW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'CJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'B_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'B_W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'L_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'LSW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'LMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'LVW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'LJW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'LCW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'RI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'CI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'BI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'LI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RY%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CY%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'BY%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10)));
				CreateDistanceNew($TourId, $TourType, 'LY%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10)));
				CreateDistanceNew($TourId, $TourType, '_K%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10)));
				break;
			case '2':
				CreateDistanceNew($TourId, $TourType, 'RSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RVM', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RVW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CVM', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CVW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'CJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'B_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'B_W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'L_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'LSW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'LMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'LVW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'LJW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'LCW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'B_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'B_W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'L_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'LJW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'LCW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'RI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'CI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'BI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'LI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RY%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CY%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'BY%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10)));
				CreateDistanceNew($TourId, $TourType, 'LY%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10)));
				CreateDistanceNew($TourId, $TourType, '_K%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10)));
				break;
		}
		break;
	case 2: //2xWA-1440 (DblFITA)
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, 'RSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('90m-5',90), array('70m-6',70), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RVM', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RVW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('90m-5',90), array('70m-6',70), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('90m-5',90), array('70m-6',70), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CVM', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CVW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'CJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('90m-5',90), array('70m-6',70), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'B_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'B_W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'L_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'LSW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'LMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'LVW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'LJW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'LCW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'RI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'CI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'BI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30), array('40m-5',40), array('35m-6',35), array('30m-7',30), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'LI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30), array('40m-5',40), array('35m-6',35), array('30m-7',30), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RY%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30), array('40m-5',40), array('35m-6',35), array('30m-7',30), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CY%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30), array('40m-5',40), array('35m-6',35), array('30m-7',30), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'BY%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10), array('25m-5',25), array('20m-6',20), array('15m-7',15), array('10m-8',10)));
				CreateDistanceNew($TourId, $TourType, 'LY%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10), array('25m-5',25), array('20m-6',20), array('15m-7',15), array('10m-8',10)));
				CreateDistanceNew($TourId, $TourType, '_K%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10), array('25m-5',25), array('20m-6',20), array('15m-7',15), array('10m-8',10)));
				break;
			case '2':
				CreateDistanceNew($TourId, $TourType, 'RSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('90m-5',90), array('70m-6',70), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RVM', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RVW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('90m-5',90), array('70m-6',70), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('90m-5',90), array('70m-6',70), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CVM', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CVW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'CJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('90m-5',90), array('70m-6',70), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'B_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'B_W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'L_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'LSW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'LMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'LVW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'LJW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'LCW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('90m-5',90), array('70m-6',70), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('90m-5',90), array('70m-6',70), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'B_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'B_W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'L_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'LJW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'LCW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'RI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'CI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'BI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30), array('40m-5',40), array('35m-6',35), array('30m-7',30), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'LI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30), array('40m-5',40), array('35m-6',35), array('30m-7',30), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RY%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30), array('40m-5',40), array('35m-6',35), array('30m-7',30), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CY%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30), array('40m-5',40), array('35m-6',35), array('30m-7',30), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'BY%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10), array('25m-5',25), array('20m-6',20), array('15m-7',15), array('10m-8',10)));
				CreateDistanceNew($TourId, $TourType, 'LY%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10), array('25m-5',25), array('20m-6',20), array('15m-7',15), array('10m-8',10)));
				CreateDistanceNew($TourId, $TourType, '_K%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10), array('25m-5',25), array('20m-6',20), array('15m-7',15), array('10m-8',10)));
				break;
		}
		break;
	case 3: //WA-720
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, 'RS%', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'RJ%', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'RC%', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'RM%', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'RV%', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'C_M', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'C_W', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'B_M', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'B_W', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'L_M', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'L_W', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'RI%', array(array('45m-1',45), array('45m-2',45)));
				CreateDistanceNew($TourId, $TourType, 'CI%', array(array('45m-1',45), array('45m-2',45)));
				CreateDistanceNew($TourId, $TourType, 'BI%', array(array('35m-1',35), array('35m-2',35)));
				CreateDistanceNew($TourId, $TourType, 'LI%', array(array('35m-1',35), array('35m-2',35)));
				CreateDistanceNew($TourId, $TourType, 'RY%', array(array('35m-1',35), array('35m-2',35)));
				CreateDistanceNew($TourId, $TourType, 'CY%', array(array('35m-1',35), array('35m-2',35)));
				CreateDistanceNew($TourId, $TourType, 'BY%', array(array('20m-1',20), array('20m-2',20)));
				CreateDistanceNew($TourId, $TourType, 'LY%', array(array('20m-1',20), array('20m-2',20)));
				CreateDistanceNew($TourId, $TourType, '_K%', array(array('20m-1',20), array('20m-2',20)));
				break;
			case '2':
				CreateDistanceNew($TourId, $TourType, 'RS%', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'RJ%', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'RC%', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'RM%', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'RV%', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'C%', array(array('50m-1',50), array('50m-2',50)));
        		CreateDistanceNew($TourId, $TourType, 'B%', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'L%', array(array('50m-1',50), array('50m-2',50)));
			case '3':
				CreateDistanceNew($TourId, $TourType, 'RJ%', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'RC%', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'C_M', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'C_W', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'B_M', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'B_W', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'L_M', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'L_W', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'RI%', array(array('45m-1',45), array('45m-2',45)));
				CreateDistanceNew($TourId, $TourType, 'CI%', array(array('45m-1',45), array('45m-2',45)));
				CreateDistanceNew($TourId, $TourType, 'BI%', array(array('35m-1',35), array('35m-2',35)));
				CreateDistanceNew($TourId, $TourType, 'LI%', array(array('35m-1',35), array('35m-2',35)));
				CreateDistanceNew($TourId, $TourType, 'RY%', array(array('35m-1',35), array('35m-2',35)));
				CreateDistanceNew($TourId, $TourType, 'CY%', array(array('35m-1',35), array('35m-2',35)));
				CreateDistanceNew($TourId, $TourType, 'BY%', array(array('20m-1',20), array('20m-2',20)));
				CreateDistanceNew($TourId, $TourType, 'LY%', array(array('20m-1',20), array('20m-2',20)));
				CreateDistanceNew($TourId, $TourType, '_K%', array(array('20m-1',20), array('20m-2',20)));
				break;
		}
		break;
	case 5: //WA-900
		CreateDistanceNew($TourId, $TourType, '%', array(array('60 m',60), array('50 m',50), array('40 m',40)));
		break;
	case 6: //WA-18m
		CreateDistanceNew($TourId, $TourType, '%', array(array('18m-1',18), array('18m-2',18)));
		break;
	case 7: //WA-25m
		CreateDistanceNew($TourId, $TourType, '%', array(array('25m-1',25), array('25m-2',25)));
		break;
	case 8: //WA-25m+18m
		CreateDistanceNew($TourId, $TourType, '%', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
		break;
	case 34: //WA-1440+WA-720
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, 'RSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('70m-6',70)));
				CreateDistanceNew($TourId, $TourType, 'RSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('70m-6',70)));
				CreateDistanceNew($TourId, $TourType, 'RMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RVM', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RVW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('70m-6',70)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('70m-6',70)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'CSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CVM', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CVW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'B_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'B_W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'L_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'LSW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'LMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'LVW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'LJW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'LCW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'RI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('45m-5',45), array('45m-6',45)));
				CreateDistanceNew($TourId, $TourType, 'CI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('45m-5',45), array('45m-6',45)));
				CreateDistanceNew($TourId, $TourType, 'BI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30), array('35m-5',35), array('35m-6',35)));
				CreateDistanceNew($TourId, $TourType, 'LI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30), array('35m-5',35), array('35m-6',35)));
				CreateDistanceNew($TourId, $TourType, 'RY%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30), array('35m-5',35), array('35m-6',35)));
				CreateDistanceNew($TourId, $TourType, 'CY%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30), array('35m-5',35), array('35m-6',35)));
				CreateDistanceNew($TourId, $TourType, 'BY%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10), array('20m-5',20), array('20m-6',20)));
				CreateDistanceNew($TourId, $TourType, 'LY%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10), array('20m-5',20), array('20m-6',20)));
				CreateDistanceNew($TourId, $TourType, '_K%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10), array('20m-5',20), array('20m-6',20)));
				break;
			case '2':
				CreateDistanceNew($TourId, $TourType, 'RSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('70m-6',70)));
				CreateDistanceNew($TourId, $TourType, 'RSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('70m-6',70)));
				CreateDistanceNew($TourId, $TourType, 'RMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RVM', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RVW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('70m-6',70)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('70m-6',70)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'CSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CVM', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CVW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'B_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'B_W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'L_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'LSW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'LMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'LVW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'LJW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'LCW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('50m-5',50), array('50m-6',50)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('70m-6',70)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('70m-6',70)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'CJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'B_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'B_W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'L_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'LJW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'LCW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'RI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('45m-5',45), array('45m-6',45)));
				CreateDistanceNew($TourId, $TourType, 'CI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('45m-5',45), array('45m-6',45)));
				CreateDistanceNew($TourId, $TourType, 'BI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30), array('35m-5',35), array('35m-6',35)));
				CreateDistanceNew($TourId, $TourType, 'LI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30), array('35m-5',35), array('35m-6',35)));
				CreateDistanceNew($TourId, $TourType, 'RY%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30), array('35m-5',35), array('35m-6',35)));
				CreateDistanceNew($TourId, $TourType, 'CY%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('30m-4',30), array('35m-5',35), array('35m-6',35)));
				CreateDistanceNew($TourId, $TourType, 'BY%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10), array('20m-5',20), array('20m-6',20)));
				CreateDistanceNew($TourId, $TourType, 'LY%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10), array('20m-5',20), array('20m-6',20)));
				CreateDistanceNew($TourId, $TourType, '_K%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10), array('20m-5',20), array('20m-6',20)));
				break;
		}
		break;
	case 35; //NZ-Clout
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, 'CSM',array(array('185m',185)));
				CreateDistanceNew($TourId, $TourType, 'CMM',array(array('185m',185)));
				CreateDistanceNew($TourId, $TourType, 'CVM',array(array('185m',185)));
				CreateDistanceNew($TourId, $TourType, 'CJM',array(array('185m',185)));
				CreateDistanceNew($TourId, $TourType, 'CCM',array(array('165m',165)));
				CreateDistanceNew($TourId, $TourType, 'CSW',array(array('165m',165)));
				CreateDistanceNew($TourId, $TourType, 'CMW',array(array('165m',165)));
				CreateDistanceNew($TourId, $TourType, 'CVW',array(array('165m',165)));
				CreateDistanceNew($TourId, $TourType, 'CJW',array(array('165m',165)));
				CreateDistanceNew($TourId, $TourType, 'RSM',array(array('165m',165)));
				CreateDistanceNew($TourId, $TourType, 'RMM',array(array('165m',165)));
				CreateDistanceNew($TourId, $TourType, 'RVM',array(array('165m',165)));
				CreateDistanceNew($TourId, $TourType, 'RJM',array(array('165m',165)));
				CreateDistanceNew($TourId, $TourType, 'RSW',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'RMW',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'RVW',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'RJW',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'CCW',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'RC%',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'BSM',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'BMM',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'BVM',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'BJM',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'BCM',array(array('120m',120)));
				CreateDistanceNew($TourId, $TourType, 'B_W',array(array('120m',120)));
				CreateDistanceNew($TourId, $TourType, 'LSM',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'LMM',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'LVM',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'LJM',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'LCM',array(array('120m',120)));
				CreateDistanceNew($TourId, $TourType, 'L_W',array(array('120m',120)));
				CreateDistanceNew($TourId, $TourType, 'CI%',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'RI%',array(array('120m',120)));
				CreateDistanceNew($TourId, $TourType, 'BI%',array(array('80m',80)));
				CreateDistanceNew($TourId, $TourType, 'LI%',array(array('80m',80)));
				CreateDistanceNew($TourId, $TourType, '_Y%',array(array('80m',80)));
				CreateDistanceNew($TourId, $TourType, '_K%',array(array('80m',80)));
				break;
			case '2':
				CreateDistanceNew($TourId, $TourType, 'CSM',array(array('185m',185)));
				CreateDistanceNew($TourId, $TourType, 'CMM',array(array('185m',185)));
				CreateDistanceNew($TourId, $TourType, 'CVM',array(array('185m',185)));
				CreateDistanceNew($TourId, $TourType, 'CJM',array(array('185m',185)));
				CreateDistanceNew($TourId, $TourType, 'CCM',array(array('165m',165)));
				CreateDistanceNew($TourId, $TourType, 'CSW',array(array('165m',165)));
				CreateDistanceNew($TourId, $TourType, 'CMW',array(array('165m',165)));
				CreateDistanceNew($TourId, $TourType, 'CVW',array(array('165m',165)));
				CreateDistanceNew($TourId, $TourType, 'CJW',array(array('165m',165)));
				CreateDistanceNew($TourId, $TourType, 'RSM',array(array('165m',165)));
				CreateDistanceNew($TourId, $TourType, 'RMM',array(array('165m',165)));
				CreateDistanceNew($TourId, $TourType, 'RVM',array(array('165m',165)));
				CreateDistanceNew($TourId, $TourType, 'RJM',array(array('165m',165)));
				CreateDistanceNew($TourId, $TourType, 'RSW',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'RMW',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'RVW',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'RJW',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'CCW',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'RC%',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'BSM',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'BMM',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'BVM',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'BJM',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'BCM',array(array('120m',120)));
				CreateDistanceNew($TourId, $TourType, 'B_W',array(array('120m',120)));
				CreateDistanceNew($TourId, $TourType, 'LSM',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'LMM',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'LVM',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'LJM',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'LCM',array(array('120m',120)));
				CreateDistanceNew($TourId, $TourType, 'L_W',array(array('120m',120)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, 'CJM',array(array('185m',185)));
				CreateDistanceNew($TourId, $TourType, 'CCM',array(array('165m',165)));
				CreateDistanceNew($TourId, $TourType, 'CJW',array(array('165m',165)));
				CreateDistanceNew($TourId, $TourType, 'RJM',array(array('165m',165)));
				CreateDistanceNew($TourId, $TourType, 'RJW',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'RC%',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'CCW',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'BJM',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'BCM',array(array('120m',120)));
				CreateDistanceNew($TourId, $TourType, 'B_W',array(array('120m',120)));
				CreateDistanceNew($TourId, $TourType, 'LJM',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'LCM',array(array('120m',120)));
				CreateDistanceNew($TourId, $TourType, 'L_W',array(array('120m',120)));
				CreateDistanceNew($TourId, $TourType, 'CI%',array(array('145m',145)));
				CreateDistanceNew($TourId, $TourType, 'RI%',array(array('120m',120)));
				CreateDistanceNew($TourId, $TourType, 'BI%',array(array('80m',80)));
				CreateDistanceNew($TourId, $TourType, 'LI%',array(array('80m',80)));
				CreateDistanceNew($TourId, $TourType, '_Y%',array(array('80m',80)));
				CreateDistanceNew($TourId, $TourType, '_K%',array(array('80m',80)));
				break;
		}
		CreateDistanceNew($TourId, $TourType, 'X%',array(array('185m',185)));
		break;
    case 37: //2xWA-720 (Dbl720)
        switch($SubRule) {
            case '1':
				CreateDistanceNew($TourId, $TourType, 'RS%', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
				CreateDistanceNew($TourId, $TourType, 'RJ%', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
				CreateDistanceNew($TourId, $TourType, 'RC%', array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
				CreateDistanceNew($TourId, $TourType, 'RM%', array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
				CreateDistanceNew($TourId, $TourType, 'RV%', array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
				CreateDistanceNew($TourId, $TourType, 'C_M', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
				CreateDistanceNew($TourId, $TourType, 'C_W', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
				CreateDistanceNew($TourId, $TourType, 'B_M', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
				CreateDistanceNew($TourId, $TourType, 'B_W', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
				CreateDistanceNew($TourId, $TourType, 'L_M', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
				CreateDistanceNew($TourId, $TourType, 'L_W', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
				CreateDistanceNew($TourId, $TourType, 'RI%', array(array('45m-1',45), array('45m-2',45), array('45m-3',45), array('45m-4',45)));
				CreateDistanceNew($TourId, $TourType, 'CI%', array(array('45m-1',45), array('45m-2',45), array('45m-3',45), array('45m-4',45)));
				CreateDistanceNew($TourId, $TourType, 'BI%', array(array('35m-1',35), array('35m-2',35), array('35m-3',35), array('35m-4',35)));
				CreateDistanceNew($TourId, $TourType, 'LI%', array(array('35m-1',35), array('35m-2',35), array('35m-3',35), array('35m-4',35)));
				CreateDistanceNew($TourId, $TourType, 'RY%', array(array('35m-1',35), array('35m-2',35), array('35m-3',35), array('35m-4',35)));
				CreateDistanceNew($TourId, $TourType, 'CY%', array(array('35m-1',35), array('35m-2',35), array('35m-3',35), array('35m-4',35)));
				CreateDistanceNew($TourId, $TourType, 'BY%', array(array('20m-1',20), array('20m-2',20), array('20m-3',20), array('20m-4',20)));
				CreateDistanceNew($TourId, $TourType, 'LY%', array(array('20m-1',20), array('20m-2',20), array('20m-3',20), array('20m-4',20)));
				CreateDistanceNew($TourId, $TourType, '_K%', array(array('20m-1',20), array('20m-2',20), array('20m-3',20), array('20m-4',20)));
                break;
            case '2':
				CreateDistanceNew($TourId, $TourType, 'RS%', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
				CreateDistanceNew($TourId, $TourType, 'RJ%', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
				CreateDistanceNew($TourId, $TourType, 'RC%', array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
				CreateDistanceNew($TourId, $TourType, 'RM%', array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
				CreateDistanceNew($TourId, $TourType, 'RV%', array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
				CreateDistanceNew($TourId, $TourType, 'C%', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
				CreateDistanceNew($TourId, $TourType, 'B%', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
				CreateDistanceNew($TourId, $TourType, 'L%', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
				break;
            case '3':
				CreateDistanceNew($TourId, $TourType, 'RJ%', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
				CreateDistanceNew($TourId, $TourType, 'RC%', array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
				CreateDistanceNew($TourId, $TourType, 'C_M', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
				CreateDistanceNew($TourId, $TourType, 'C_W', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
				CreateDistanceNew($TourId, $TourType, 'B_M', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
				CreateDistanceNew($TourId, $TourType, 'B_W', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
				CreateDistanceNew($TourId, $TourType, 'L_M', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
				CreateDistanceNew($TourId, $TourType, 'L_W', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
				CreateDistanceNew($TourId, $TourType, 'RI%', array(array('45m-1',45), array('45m-2',45), array('45m-3',45), array('45m-4',45)));
				CreateDistanceNew($TourId, $TourType, 'CI%', array(array('45m-1',45), array('45m-2',45), array('45m-3',45), array('45m-4',45)));
				CreateDistanceNew($TourId, $TourType, 'BI%', array(array('35m-1',35), array('35m-2',35), array('35m-3',35), array('35m-4',35)));
				CreateDistanceNew($TourId, $TourType, 'LI%', array(array('35m-1',35), array('35m-2',35), array('35m-3',35), array('35m-4',35)));
				CreateDistanceNew($TourId, $TourType, 'RY%', array(array('35m-1',35), array('35m-2',35), array('35m-3',35), array('35m-4',35)));
				CreateDistanceNew($TourId, $TourType, 'CY%', array(array('35m-1',35), array('35m-2',35), array('35m-3',35), array('35m-4',35)));
				CreateDistanceNew($TourId, $TourType, 'BY%', array(array('20m-1',20), array('20m-2',20), array('20m-3',20), array('20m-4',20)));
				CreateDistanceNew($TourId, $TourType, 'LY%', array(array('20m-1',20), array('20m-2',20), array('20m-3',20), array('20m-4',20)));
				CreateDistanceNew($TourId, $TourType, '_K%', array(array('20m-1',20), array('20m-2',20), array('20m-3',20), array('20m-4',20)));
				break;
        }
        break;
}

// call creation of standard NZ matchplay competition events for Outdoor (3,37) and Indoor (6,7,8) rounds
if($TourType==3 or $TourType==6 or $TourType==7 or $TourType==8 or $TourType==37) {
	// Standard Matchplay Events
	CreateStandardEvents($TourId, $SubRule, in_array($TourType,array(3,37)), in_array($TourType,array(3,6,7,8,37)));

	// Classes in Events
	InsertStandardEvents($TourId, $SubRule);

	// Finals & TeamFinals
	CreateFinals($TourId);
}

// Default Target
switch($TourType) {
	// function expects ($TourId, $Id, $Name, $Classes, $Default, $T1, $W1, $T2=0, $W2=0, etc.)
	/*
		IANSEO Target Faces: ($Tn)
		1 - Indoor (1-big 10)
		2 - Indoor (6-big 10)
		3 - Indoor (1-small 10)
		4 - Indoor (6-small 10)
		5 - Outdoor (1-X)
		6 - Field Archery
		7 - Hit-Miss
		8 - 3D Standard
		9 - Outdoor (5-X)
		10 - Outdoor (6-X)
	*/
	case 1: //WA-1440 (FITA)
		switch ($SubRule)  {
			case '1':
				$i=1;
				CreateTargetFace($TourId, $i++, '60m WA1440', 'B_M', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'B_W', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '40m WA1440', 'BI%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'BY%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'BK%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'L_M', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'LSW', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'LVW', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'LMW', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'LJW', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'LCW', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '40m WA1440', 'LI%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'LY%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'LK%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'RVM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'RMM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '90m WA1440', 'RSM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '90m WA1440', 'RJM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'RCM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'RVW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'RMW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'RSW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'RJW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'RCW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'RI%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '40m WA1440', 'RY%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'RK%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'CVM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'CMM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '90m WA1440', 'CSM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '90m WA1440', 'CJM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'CCM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'CVW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'CMW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'CSW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'CJW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'CCW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'CI%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '40m WA1440', 'CY%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'CK%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				break;
			case '2':
				$i=1;
				CreateTargetFace($TourId, $i++, '60m WA1440', 'B_M', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'B_W', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'L_M', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'LSW', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'LVW', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'LMW', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'LJW', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'LCW', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'RVM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'RMM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '90m WA1440', 'RSM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '90m WA1440', 'RJM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'RCM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'RVW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'RMW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'RSW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'RJW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'RCW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'CVM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'CMM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '90m WA1440', 'CSM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '90m WA1440', 'CJM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'CCM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'CVW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'CMW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'CSW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'CJW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'CCW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				break;
			case '3':
				$i=1;
				CreateTargetFace($TourId, $i++, '60m WA1440', 'B_M', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'B_W', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '40m WA1440', 'BI%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'BY%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'BK%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'L_M', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'LJW', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'LCW', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '40m WA1440', 'LI%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'LY%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'LK%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '90m WA1440', 'RJM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'RCM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'RJW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'RCW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'RI%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '40m WA1440', 'RY%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'RK%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '90m WA1440', 'CJM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'CCM', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'CJW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'CCW', '1', 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'CI%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '40m WA1440', 'CY%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'CK%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
				break;
		}
		break;
	case 2: //2xWA-1440 (DblFITA)
		switch($SubRule) {
			case '1':
				$i=1;
				CreateTargetFace($TourId, $i++, '60m WA1440', 'B_M', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'B_W', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '40m WA1440', 'BI%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'BY%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'BK%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'L_M', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'LSW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'LVW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'LMW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'LJW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'LCW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '40m WA1440', 'LI%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'LY%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'LK%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'RVM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'RMM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '90m WA1440', 'RSM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '90m WA1440', 'RJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'RCM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'RVW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'RMW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'RSW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'RJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'RCW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'RI%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '40m WA1440', 'RY%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'RK%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'CVM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'CMM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '90m WA1440', 'CSM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '90m WA1440', 'CJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'CCM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'CVW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'CMW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'CSW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'CJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'CCW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'CI%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '40m WA1440', 'CY%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'CK%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				break;
			case '2':
				$i=1;
				CreateTargetFace($TourId, $i++, '60m WA1440', 'B_M', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'B_W', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'L_M', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'LSW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'LVW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'LMW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'LJW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'LCW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'RVM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'RMM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '90m WA1440', 'RSM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '90m WA1440', 'RJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'RCM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'RVW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'RMW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'RSW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'RJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'RCW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'CVM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'CMM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '90m WA1440', 'CSM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '90m WA1440', 'CJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'CCM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'CVW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'CMW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'CSW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'CJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'CCW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				break;
			case '3':
				$i=1;
				CreateTargetFace($TourId, $i++, '60m WA1440', 'B_M', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'B_W', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '40m WA1440', 'BI%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'BY%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'BK%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'L_M', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'LJW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'LCW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '40m WA1440', 'LI%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'LY%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'LK%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '90m WA1440', 'RJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'RCM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'RJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'RCW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'RI%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '40m WA1440', 'RY%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'RK%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '90m WA1440', 'CJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'CCM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m WA1440', 'CJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m WA1440', 'CCW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '55m WA1440', 'CI%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '40m WA1440', 'CY%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m WA1440', 'CK%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
				break;
			case '2':
				CreateTargetFace($TourId, $i++, '50m WA720', 'B_M', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'B_W', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'L_M', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'L_W', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RVM', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RMM', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m WA720', 'RSM', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m WA720', 'RJM', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RCM', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RVW', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RMW', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m WA720', 'RSW', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m WA720', 'RJW', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RCW', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CVM', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CMM', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CSM', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CJM', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CCM', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CVW', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CMW', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CSW', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CJW', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CCW', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				break;
			case '3':
				CreateTargetFace($TourId, $i++, '50m WA720', 'B_M', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'B_W', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '35m WA720', 'BI%', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '20m WA720', 'BY%', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '20m WA720', 'BK%', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'L_M', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'L_W', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '35m WA720', 'LI%', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '20m WA720', 'LY%', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '20m WA720', 'LK%', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m WA720', 'RJM', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RCM', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m WA720', 'RJW', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RCW', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '45m WA720', 'RI%', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '35m WA720', 'RY%', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '20m WA720', 'RK%', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CJM', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CCM', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CJW', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CCW', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '45m WA720', 'CI%', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '35m WA720', 'CY%', '1', 5, 80, 5, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '20m WA720', 'CK%', '1', 5, 80, 5, 80, 5, 80, 5, 80);
				break;
		}
		break;
	case 3:  //WA-720
		switch($SubRule) {
			case '1':
				$i=1;
				CreateTargetFace($TourId, $i++, '50m WA720', 'B_M', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'B_W', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '35m WA720', 'BI%', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '20m WA720', 'BY%', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '20m WA720', 'BK%', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'L_M', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'L_W', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '35m WA720', 'LI%', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '20m WA720', 'LY%', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '20m WA720', 'LK%', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RVM', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RMM', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m WA720', 'RSM', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m WA720', 'RJM', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RCM', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RVW', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RMW', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m WA720', 'RSW', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m WA720', 'RJW', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RCW', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '45m WA720', 'RI%', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '35m WA720', 'RY%', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '20m WA720', 'RK%', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CVM', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CMM', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CSM', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CJM', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CCM', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CVW', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CMW', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CSW', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CJW', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CCW', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '45m WA720', 'CI%', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '35m WA720', 'CY%', '1', 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '20m WA720', 'CK%', '1', 5, 80, 5, 80);
				break;
			case '2':
				$i=1;
				CreateTargetFace($TourId, $i++, '50m WA720', 'B_M', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'B_W', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'L_M', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'L_W', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RVM', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RMM', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m WA720', 'RSM', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m WA720', 'RJM', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RCM', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RVW', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RMW', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m WA720', 'RSW', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m WA720', 'RJW', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RCW', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CVM', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CMM', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CSM', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CJM', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CCM', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CVW', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CMW', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CSW', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CJW', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CCW', '1', 9, 80, 9, 80);
				break;
			case '3':
				$i=1;
				CreateTargetFace($TourId, $i++, '50m WA720', 'B_M', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'B_W', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '35m WA720', 'BI%', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '20m WA720', 'BY%', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '20m WA720', 'BK%', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'L_M', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'L_W', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '35m WA720', 'LI%', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '20m WA720', 'LY%', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '20m WA720', 'LK%', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m WA720', 'RJM', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RCM', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m WA720', 'RJW', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RCW', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '45m WA720', 'RI%', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '35m WA720', 'RY%', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '20m WA720', 'RK%', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CJM', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CCM', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CJW', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CCW', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '45m WA720', 'CI%', '1', 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '35m WA720', 'CY%', '1', 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '20m WA720', 'CK%', '1', 5, 80, 5, 80);
				break;
		}
		break;
	case 4: //WA_72
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 10, 80);
		// optional target faces
		CreateTargetFace($TourId, 2, '~Option1', '%', '',  5, 122, 5, 122, 5, 80,  5, 80);
		CreateTargetFace($TourId, 3, '~Option2', '%', '',  5, 122, 5, 122, 9, 80, 10, 80);
		break;
	case 5: //WA-900
		CreateTargetFace($TourId, 1, '~Default', '%', '1',  5, 122, 5, 122, 5, 122);
		// optional target faces
		CreateTargetFace($TourId, 2, '~Option1', '%', '',  5, 80, 5, 80, 5, 80);
		CreateTargetFace($TourId, 2, '~Option2', '%', '',  9, 80, 9, 80, 9, 80);
		break;
	case 6: //WA-18m
		switch($SubRule) {
			case '1':
			case '3':
				$i=1;
				CreateTargetFace($TourId, $i++, 'Triple Spot R', 'R_M', '1', 2, 40, 2, 40);
				CreateTargetFace($TourId, $i++, 'Triple Spot R', 'R_W', '1', 2, 40, 2, 40);
				CreateTargetFace($TourId, $i++, 'Triple Spot C', 'C_M', '1', 4, 40, 4, 40);
				CreateTargetFace($TourId, $i++, 'Triple Spot C', 'C_W', '1', 4, 40, 4, 40);
				CreateTargetFace($TourId, $i++, 'Full Face R', 'B%', '1', 1, 40, 1, 40);
				CreateTargetFace($TourId, $i++, 'Full Face R', 'L%', '1', 1, 40, 1, 40);
				CreateTargetFace($TourId, $i++, 'Full Face R', 'RI%', '1', 1, 40, 1, 40);
				CreateTargetFace($TourId, $i++, 'Triple Spot C', 'CI%', '1', 4, 40, 4, 40);
				CreateTargetFace($TourId, $i++, 'Full Face R', 'RY%', '1', 1, 40, 1, 40);
				CreateTargetFace($TourId, $i++, 'Full Face C', 'CY%', '1', 3, 40, 3, 40);
				CreateTargetFace($TourId, $i++, 'Full Face R', 'RK%', '1', 1, 40, 1, 40);
				CreateTargetFace($TourId, $i++, 'Full Face C', 'CK%', '1', 3, 40, 3, 40);
				break;
			case '2':
				$i=1;
				CreateTargetFace($TourId, $i++, 'Triple Spot R', 'R_M', '1', 2, 40, 2, 40);
				CreateTargetFace($TourId, $i++, 'Triple Spot R', 'R_W', '1', 2, 40, 2, 40);
				CreateTargetFace($TourId, $i++, 'Triple Spot C', 'C_M', '1', 4, 40, 4, 40);
				CreateTargetFace($TourId, $i++, 'Triple Spot C', 'C_W', '1', 4, 40, 4, 40);
				CreateTargetFace($TourId, $i++, 'Full Face R', 'B%', '1', 1, 40, 1, 40);
				CreateTargetFace($TourId, $i++, 'Full Face R', 'L%', '1', 1, 40, 1, 40);
				break;
		}
		break;
	case 7: //WA-25m
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 2, 60, 2, 60);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 4, 60, 4, 60);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Option1', 'REG-^R|^B', '',  1, 60, 1, 60);
		break;
	case 8: //WA-25m+18m
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 2, 60, 2, 60, 2, 40, 2, 40);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 4, 60, 4, 60, 4, 40, 4, 40);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Option1', 'REG-^R|^B', '',  1, 60, 1, 60,  1, 40, 1, 40);
		break;
	case 34: //WA-1440+WA-720
		switch ($SubRule) {
			case '1':
				$i=1;
				CreateTargetFace($TourId, $i++, '50m 1440/50m RR', 'B_M', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m 1440/50m RR', 'B_W', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '40m 1440/35m RR', 'BI%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '25m 1440/20m RR', 'BY%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '25m 1440/20m RR', 'BK%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m 1440/50m RR', 'L_M', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m 1440/50m RR', 'LSW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m 1440/50m RR', 'LVW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m 1440/50m RR', 'LMW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m 1440/50m RR', 'LJW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '55m 1440/50m RR', 'LCW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '40m 1440/35m RR', 'LI%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '25m 1440/20m RR', 'LY%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '25m 1440/20m RR', 'LK%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m 1440/60m RR', 'RVM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m 1440/60m RR', 'RMM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '90m 1440/70m RR', 'RSM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '90m 1440/70m RR', 'RJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m 1440/60m RR', 'RCM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '55m 1440/60m RR', 'RVW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m 1440/60m RR', 'RMW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m 1440/70m RR', 'RSW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m 1440/70m RR', 'RJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m 1440/60m RR', 'RCW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '55m 1440/45m RR', 'RI%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '40m 1440/35m RR', 'RY%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '25m 1440/20m RR', 'RK%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m 1440/50m RR', 'CVM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m 1440/50m RR', 'CMM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '90m 1440/50m RR', 'CSM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '90m 1440/50m RR', 'CJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m 1440/50m RR', 'CCM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '55m 1440/50m RR', 'CVW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m 1440/50m RR', 'CMW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m 1440/50m RR', 'CSW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m 1440/50m RR', 'CJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m 1440/50m RR', 'CCW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '55m 1440/45m RR', 'CI%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '40m 1440/35m RR', 'CY%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m 1440/20m RR', 'CK%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 80, 5, 80);
				break;
			case '2':
				$i=1;
				CreateTargetFace($TourId, $i++, '50m 1440/50m RR', 'B_M', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m 1440/50m RR', 'B_W', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m 1440/50m RR', 'L_M', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m 1440/50m RR', 'LSW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m 1440/50m RR', 'LVW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m 1440/50m RR', 'LMW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m 1440/50m RR', 'LJW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '55m 1440/50m RR', 'LCW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m 1440/60m RR', 'RVM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m 1440/60m RR', 'RMM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '90m 1440/70m RR', 'RSM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '90m 1440/70m RR', 'RJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m 1440/60m RR', 'RCM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '55m 1440/60m RR', 'RVW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m 1440/60m RR', 'RMW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m 1440/70m RR', 'RSW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m 1440/70m RR', 'RJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m 1440/60m RR', 'RCW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m 1440/50m RR', 'CVM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m 1440/50m RR', 'CMM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '90m 1440/50m RR', 'CSM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '90m 1440/50m RR', 'CJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m 1440/50m RR', 'CCM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '55m 1440/50m RR', 'CVW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m 1440/50m RR', 'CMW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m 1440/50m RR', 'CSW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m 1440/50m RR', 'CJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m 1440/50m RR', 'CCW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				break;
			case '3':
				$i=1;
				CreateTargetFace($TourId, $i++, '50m 1440/50m RR', 'B_M', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m 1440/50m RR', 'B_W', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '40m 1440/35m RR', 'BI%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '25m 1440/20m RR', 'BY%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '25m 1440/20m RR', 'BK%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m 1440/50m RR', 'L_M', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m 1440/50m RR', 'LJW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '55m 1440/50m RR', 'LCW', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '40m 1440/35m RR', 'LI%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '25m 1440/20m RR', 'LY%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '25m 1440/20m RR', 'LK%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '90m 1440/70m RR', 'RJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m 1440/60m RR', 'RCM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m 1440/70m RR', 'RJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m 1440/60m RR', 'RCW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '55m 1440/45m RR', 'RI%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '40m 1440/35m RR', 'RY%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '25m 1440/20m RR', 'RK%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '90m 1440/50m RR', 'CJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m 1440/50m RR', 'CCM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '70m 1440/50m RR', 'CJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '60m 1440/50m RR', 'CCW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '55m 1440/45m RR', 'CI%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '40m 1440/35m RR', 'CY%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '25m 1440/20m RR', 'CK%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 80, 5, 80);
				break;
		}
		break;
	case 35; //NZ-Clout
		break;
    case 37: //2xWA-720 (Dbl720)
        switch($SubRule) {
			case '1':
				$i=1;
				CreateTargetFace($TourId, $i++, '50m WA720', 'B_M', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'B_W', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '35m WA720', 'BI%', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '20m WA720', 'BY%', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '20m WA720', 'BK%', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'L_M', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'L_W', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '35m WA720', 'LI%', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '20m WA720', 'LY%', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '20m WA720', 'LK%', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RVM', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RMM', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m WA720', 'RSM', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m WA720', 'RJM', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RCM', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RVW', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RMW', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m WA720', 'RSW', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '70m WA720', 'RJW', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '60m WA720', 'RCW', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '45m WA720', 'RI%', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '35m WA720', 'RY%', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '20m WA720', 'RK%', '1', 5, 122, 5, 122, 5, 122, 5, 122);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CVM', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CMM', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CSM', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CJM', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CCM', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CVW', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CMW', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CSW', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CJW', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '50m WA720', 'CCW', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '45m WA720', 'CI%', '1', 9, 80, 9, 80, 9, 80, 9, 80);
				CreateTargetFace($TourId, $i++, '35m WA720', 'CY%', '1', 5, 80, 5, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, $i++, '20m WA720', 'CK%', '1', 5, 80, 5, 80, 5, 80, 5, 80);
		}
        break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 24, 4);

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