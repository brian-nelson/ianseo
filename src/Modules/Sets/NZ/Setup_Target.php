<?php
/*
Common Setup for "Target" Archery
*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId);

// default SubClasses
CreateSubClass($TourId, 1, 'NZ', 'New Zealand');
CreateSubClass($TourId, 2, 'IN', 'International');
CreateSubClass($TourId, 3, 'OP', 'Open');

// default Classes
CreateStandardClasses($TourId, $SubRule);

// default Distances
switch($TourType) {
	case 1:
	case 4:
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, 'BCM', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'BCW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'BI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'BJM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'BJW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'BMM', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'BMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'BSM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'BSW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'BV%', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'CJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'LC%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'LI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'L_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'L_W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, '_K%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10)));
				CreateDistanceNew($TourId, $TourType, '_Y%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('25m-4',25)));
				break;
			case '2':
				CreateDistanceNew($TourId, $TourType, 'BCM', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'BCW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'BJM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'BJW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'BMM', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'BMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'BSM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'BSW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'BV%', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'LC%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'L_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'L_W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, 'BCM', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'BCW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'BI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'BJM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'BJW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'CJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'CJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'LC%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'LI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'LJM', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'LJW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
				CreateDistanceNew($TourId, $TourType, '_K%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10)));
				CreateDistanceNew($TourId, $TourType, '_Y%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('25m-4',25)));
				break;
		}
		break;
	case 2:
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, 'BCM', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'BCW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'BI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('25m-4',25), array('40m-5',40), array('35m-6',35), array('30m-7',30), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'BJM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'BJW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'BMM', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'BMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'BSM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'BSW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'BV%', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'CJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('90m-5',90), array('70m-6',70), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('90m-5',90), array('70m-6',70), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'LC%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'LI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('25m-4',25), array('40m-5',40), array('35m-6',35), array('30m-7',30), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'L_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'L_W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('90m-5',90), array('70m-6',70), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('90m-5',90), array('70m-6',70), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, '_K%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10), array('25m-5',25), array('20m-6',20), array('15m-7',15), array('10m-8',10)));
				CreateDistanceNew($TourId, $TourType, '_Y%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('25m-4',25), array('40m-5',40), array('35m-6',35), array('30m-7',30), array('25m-8',25)));
				break;
			case '2':
				CreateDistanceNew($TourId, $TourType, 'BCM', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'BCW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'BJM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'BJW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'BMM', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'BMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'BSM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'BSW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'BV%', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('90m-5',90), array('70m-6',70), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('90m-5',90), array('70m-6',70), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'LC%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'L_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'L_W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('90m-5',90), array('70m-6',70), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('90m-5',90), array('70m-6',70), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, 'BCM', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'BCW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'BI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('25m-4',25), array('40m-5',40), array('35m-6',35), array('30m-7',30), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'BJM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'BJW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'CJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('90m-5',90), array('70m-6',70), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'CJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'LC%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'LI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('25m-4',25), array('40m-5',40), array('35m-6',35), array('30m-7',30), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'LJM', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'LJW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('50m-6',50), array('40m-7',40), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('55m-5',55), array('45m-6',45), array('35m-7',35), array('25m-8',25)));
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('90m-5',90), array('70m-6',70), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('60m-6',60), array('50m-7',50), array('30m-8',30)));
				CreateDistanceNew($TourId, $TourType, '_K%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10), array('25m-5',25), array('20m-6',20), array('15m-7',15), array('10m-8',10)));
				CreateDistanceNew($TourId, $TourType, '_Y%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('25m-4',25), array('40m-5',40), array('35m-6',35), array('30m-7',30), array('25m-8',25)));
				break;
		}
		break;
	case 3:
		switch($SubRule) {
			case '1':
			case '2':
				CreateDistanceNew($TourId, $TourType, 'RSM', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'RSW', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'RJ_', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'RC_', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'RM_', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'C%',  array(array('50m-1',50), array('50m-2',50)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, 'RJ_', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'RC_', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'CJ_', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'CC_', array(array('50m-1',50), array('50m-2',50)));
				break;
		}
		break;
	case 5:
		CreateDistanceNew($TourId, $TourType, '%', array(array('60 m',60), array('50 m',50), array('40 m',40)));
		break;
	case 6:
		CreateDistanceNew($TourId, $TourType, '%', array(array('18m-1',28), array('18m-2',18)));
		break;
	case 7:
		CreateDistanceNew($TourId, $TourType, '%', array(array('25m-1',25), array('25m-2',25)));
		break;
	case 8:
		CreateDistanceNew($TourId, $TourType, '%', array(array('25m-1',25), array('25m-2',25), array('18m-1',28), array('18m-2',18)));
		break;
	case 34:
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, 'BCM', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('45m-5',45), array('45m-6',45)));
				CreateDistanceNew($TourId, $TourType, 'BCW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('45m-5',45), array('45m-6',45)));
				CreateDistanceNew($TourId, $TourType, 'BI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('25m-4',25), array('35m-5',35), array('35m-6',35)));
				CreateDistanceNew($TourId, $TourType, 'BJM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'BJW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'BMM', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'BMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'BSM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'BSW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'BV%', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'CCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('45m-5',45), array('45m-6',45)));
				CreateDistanceNew($TourId, $TourType, 'CJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'LC%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('45m-5',45), array('45m-6',45)));
				CreateDistanceNew($TourId, $TourType, 'LI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('25m-4',25), array('35m-5',35), array('35m-6',35)));
				CreateDistanceNew($TourId, $TourType, 'L_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'L_W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('45m-5',45), array('45m-6',45)));
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('70m-6',70)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('70m-6',70)));
				CreateDistanceNew($TourId, $TourType, 'RMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('70m-6',70)));
				CreateDistanceNew($TourId, $TourType, 'RSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('70m-6',70)));
				CreateDistanceNew($TourId, $TourType, '_K%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10), array('20m-5',20), array('20m-6',20)));
				CreateDistanceNew($TourId, $TourType, '_Y%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('25m-4',25), array('35m-5',35), array('35m-6',35)));
				break;
			case '2':
				CreateDistanceNew($TourId, $TourType, 'BCM', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('45m-5',45), array('45m-6',45)));
				CreateDistanceNew($TourId, $TourType, 'BCW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('45m-5',45), array('45m-6',45)));
				CreateDistanceNew($TourId, $TourType, 'BJM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'BJW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'BMM', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'BMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'BSM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'BSW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'BV%', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'CCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'LC%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('45m-5',45), array('45m-6',45)));
				CreateDistanceNew($TourId, $TourType, 'L_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'L_W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('70m-6',70)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('70m-6',70)));
				CreateDistanceNew($TourId, $TourType, 'RMM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RMW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RSM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('70m-6',70)));
				CreateDistanceNew($TourId, $TourType, 'RSW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('70m-6',70)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, 'BCM', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('45m-5',45), array('45m-6',45)));
				CreateDistanceNew($TourId, $TourType, 'BCW', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('45m-5',45), array('45m-6',45)));
				CreateDistanceNew($TourId, $TourType, 'BI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('25m-4',25), array('35m-5',35), array('35m-6',35)));
				CreateDistanceNew($TourId, $TourType, 'BJM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'BJW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'CCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('45m-5',45), array('45m-6',45)));
				CreateDistanceNew($TourId, $TourType, 'CJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'CJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('50m-5',50), array('50m-6',50)));
				CreateDistanceNew($TourId, $TourType, 'LC%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('45m-5',45), array('45m-6',45)));
				CreateDistanceNew($TourId, $TourType, 'LI%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('25m-4',25), array('35m-5',35), array('35m-6',35)));
				CreateDistanceNew($TourId, $TourType, 'LJM', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'LJW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30), array('60m-5',60), array('60m-6',60)));
				CreateDistanceNew($TourId, $TourType, 'RI%', array(array('55m-1',55), array('45m-2',45), array('35m-3',35), array('25m-4',25), array('45m-5',45), array('45m-6',45)));
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('70m-6',70)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30), array('70m-5',70), array('70m-6',70)));
				CreateDistanceNew($TourId, $TourType, '_K%', array(array('25m-1',25), array('20m-2',20), array('15m-3',15), array('10m-4',10), array('20m-5',20), array('20m-6',20)));
				CreateDistanceNew($TourId, $TourType, '_Y%', array(array('40m-1',40), array('35m-2',35), array('30m-3',30), array('25m-4',25), array('35m-5',35), array('35m-6',35)));
				break;
			}
			break;
		case 35:
			switch($SubRule) {
				case '1':
				case '3':
					CreateDistanceNew($TourId, $TourType, 'X%',array(array('185m',185)));
					CreateDistanceNew($TourId, $TourType, 'RI%',array(array('120m',120)));
					CreateDistanceNew($TourId, $TourType, 'RCW',array(array('145m',145)));
					CreateDistanceNew($TourId, $TourType, 'RCM',array(array('145m',145)));
					CreateDistanceNew($TourId, $TourType, 'R_W',array(array('145m',145)));
					CreateDistanceNew($TourId, $TourType, 'R_M',array(array('165m',165)));
					CreateDistanceNew($TourId, $TourType, 'LCM',array(array('120m',120)));
					CreateDistanceNew($TourId, $TourType, 'L_W',array(array('120m',120)));
					CreateDistanceNew($TourId, $TourType, 'L_M',array(array('145m',145)));
					CreateDistanceNew($TourId, $TourType, 'L_G',array(array('80m',80)));
					CreateDistanceNew($TourId, $TourType, 'L_B',array(array('80m',80)));
					CreateDistanceNew($TourId, $TourType, 'CI%',array(array('145m',145)));
					CreateDistanceNew($TourId, $TourType, 'CCW',array(array('145m',145)));
					CreateDistanceNew($TourId, $TourType, 'CCM',array(array('165m',165)));
					CreateDistanceNew($TourId, $TourType, 'C_W',array(array('165m',165)));
					CreateDistanceNew($TourId, $TourType, 'C_M',array(array('185m',185)));
					CreateDistanceNew($TourId, $TourType, 'BCM',array(array('120m',120)));
					CreateDistanceNew($TourId, $TourType, 'B_W',array(array('120m',120)));
					CreateDistanceNew($TourId, $TourType, 'B_M',array(array('145m',145)));
					CreateDistanceNew($TourId, $TourType, 'B_G',array(array('80m',80)));
					CreateDistanceNew($TourId, $TourType, 'B_B',array(array('80m',80)));
					CreateDistanceNew($TourId, $TourType, '_Y%',array(array('80m',80)));
					CreateDistanceNew($TourId, $TourType, '_K%',array(array('80m',80)));
					break;
				case '2':
					CreateDistanceNew($TourId, $TourType, 'X%',array(array('185m',185)));
					CreateDistanceNew($TourId, $TourType, 'RCW',array(array('145m',145)));
					CreateDistanceNew($TourId, $TourType, 'RCM',array(array('145m',145)));
					CreateDistanceNew($TourId, $TourType, 'R_W',array(array('145m',145)));
					CreateDistanceNew($TourId, $TourType, 'R_M',array(array('165m',165)));
					CreateDistanceNew($TourId, $TourType, 'LCM',array(array('120m',120)));
					CreateDistanceNew($TourId, $TourType, 'L_W',array(array('120m',120)));
					CreateDistanceNew($TourId, $TourType, 'L_M',array(array('145m',145)));
					CreateDistanceNew($TourId, $TourType, 'CCW',array(array('145m',145)));
					CreateDistanceNew($TourId, $TourType, 'CCM',array(array('165m',165)));
					CreateDistanceNew($TourId, $TourType, 'C_W',array(array('165m',165)));
					CreateDistanceNew($TourId, $TourType, 'C_M',array(array('185m',185)));
					CreateDistanceNew($TourId, $TourType, 'BCM',array(array('120m',120)));
					CreateDistanceNew($TourId, $TourType, 'B_W',array(array('120m',120)));
					CreateDistanceNew($TourId, $TourType, 'B_M',array(array('145m',145)));
					break;
			}
			break;
}

if($TourType<5 or $TourType==6 or $TourType==18 or $TourType==34) {
	// default Events
	CreateStandardEvents($TourId, $SubRule, $TourType!=6);

	// Classes in Events
	InsertStandardEvents($TourId, $SubRule, $TourType!=6);

	// Finals & TeamFinals
	CreateFinals($TourId);
}

// Default Target
switch($TourType) {
	case 1:
	case 4:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 10, 80);
		// optional target faces
		CreateTargetFace($TourId, 2, '~Option1', '%', '',  5, 122, 5, 122, 5, 80,  5, 80);
		CreateTargetFace($TourId, 3, '~Option2', '%', '',  5, 122, 5, 122, 9, 80, 10, 80);
		break;
	case 2:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 10, 80, 5, 122, 5, 122, 5, 80, 10, 80);
		// optional target faces
		CreateTargetFace($TourId, 2, '~Option1', '%', '',  5, 122, 5, 122, 5, 80,  5, 80,  5, 122, 5, 122, 5, 80,  5, 80);
		CreateTargetFace($TourId, 3, '~Option2', '%', '',  5, 122, 5, 122, 9, 80, 10, 80,  5, 122, 5, 122, 9, 80, 10, 80);
		break;
	case 18:
		CreateTargetFace($TourId, 1, '~Default', 'R%', '1', 5, 122, 5, 122, 5, 80, 10, 80);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 9, 80, 9, 80);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Option1', 'R%', '',  5, 122, 5, 122, 5, 80,  5, 80);
		CreateTargetFace($TourId, 4, '~Option2', 'R%', '',  5, 122, 5, 122, 9, 80, 10, 80);
		break;
	case 3:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1',  9, 80, 9, 80);
		break;
	case 5:
		CreateTargetFace($TourId, 1, '~Default', '%', '1',  5, 122, 5, 122, 5, 122);
		break;
	case 6:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 2, 40, 2, 40);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 4, 40, 4, 40);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Option1', 'R%', '',  1, 40, 1, 40);
		break;
	case 7:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 2, 60, 2, 60);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 4, 60, 4, 60);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Option1', 'R%', '',  1, 60, 1, 60);
		break;
	case 8:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 2, 60, 2, 60, 2, 40, 2, 40);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 4, 60, 4, 60, 4, 40, 4, 40);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Option1', 'R%', '',  1, 60, 1, 60,  1, 40, 1, 40);
		break;
	case 34:
		switch($SubRule) {
			case '1':
				CreateTargetFace($TourId, 1, '55m Int FITA', 'BCM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 2, '55m Int FITA', 'BCW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 3, '40m Horsham', 'BI%', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 4, '70m WA1440', 'BJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 5, '60m WA1440', 'BJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 6, '60m WA1440', 'BMM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 7, '60m WA1440', 'BMW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 8, '70m WA1440', 'BSM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 9, '60m WA1440', 'BSW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 10, '60m WA1440', 'BV%', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 11, '70m WA1440', 'CCM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 12, '60m WA1440', 'CCW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 13, '55m Int FITA', 'CI%', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 14, '90m WA1440', 'CJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 15, '70m WA1440', 'CJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 16, '70m WA1440', 'CMM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 17, '60m WA1440', 'CMW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 18, '90m WA1440', 'CSM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 19, '70m WA1440', 'CSW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 20, '55m Int FITA', 'LC%', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 21, '40m Horsham', 'LI%', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 22, '60m WA1440', 'L_M', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 23, '60m WA1440', 'L_W', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 24, '70m WA1440', 'RCM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 25, '60m WA1440', 'RCW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 26, '55m Int FITA', 'RI%', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 27, '90m WA1440', 'RJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 28, '70m WA1440', 'RJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 29, '70m WA1440', 'RMM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 30, '60m WA1440', 'RMW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 31, '90m WA1440', 'RSM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 32, '70m WA1440', 'RSW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 33, '25m Kiwi Round', '_K%', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 34, '40m Horsham', '_Y%', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				break;
			case '2':
				CreateTargetFace($TourId, 1, '55m Int FITA', 'BCM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 2, '55m Int FITA', 'BCW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 3, '70m WA1440', 'BJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 4, '60m WA1440', 'BJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 5, '60m WA1440', 'BMM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 6, '60m WA1440', 'BMW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 7, '70m WA1440', 'BSM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 8, '60m WA1440', 'BSW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 9, '60m WA1440', 'BV%', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 10, '70m WA1440', 'CCM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 11, '60m WA1440', 'CCW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 12, '90m WA1440', 'CJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 13, '70m WA1440', 'CJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 14, '70m WA1440', 'CMM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 15, '60m WA1440', 'CMW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 16, '90m WA1440', 'CSM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 17, '70m WA1440', 'CSW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 18, '55m Int FITA', 'LC%', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 19, '60m WA1440', 'L_M', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 20, '60m WA1440', 'L_W', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 21, '70m WA1440', 'RCM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 22, '60m WA1440', 'RCW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 23, '90m WA1440', 'RJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 24, '70m WA1440', 'RJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 25, '70m WA1440', 'RMM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 26, '60m WA1440', 'RMW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 27, '90m WA1440', 'RSM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 28, '70m WA1440', 'RSW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				break;
			case '3':
				CreateTargetFace($TourId, 1, '55m Int FITA', 'BCM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 2, '55m Int FITA', 'BCW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 3, '40m Horsham', 'BI%', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 4, '70m WA1440', 'BJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 5, '60m WA1440', 'BJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 6, '70m WA1440', 'CCM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 7, '60m WA1440', 'CCW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 8, '55m Int FITA', 'CI%', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 9, '90m WA1440', 'CJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 10, '70m WA1440', 'CJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 11, '55m Int FITA', 'LC%', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 12, '40m Horsham', 'LI%', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 13, '60m WA1440', 'LJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 14, '60m WA1440', 'LJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 15, '70m WA1440', 'RCM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 16, '60m WA1440', 'RCW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 17, '55m Int FITA', 'RI%', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 18, '90m WA1440', 'RJM', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 19, '70m WA1440', 'RJW', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 20, '25m Kiwi Round', '_K%', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				CreateTargetFace($TourId, 21, '40m Horsham', '_Y%', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
				break;
		}
		// CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 80, 5, 80);
		// CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1',  5, 122, 5, 122, 9, 80, 9, 80, 9, 80, 9, 80);
		break;
	case 35:
		switch($SubRule) {
			case 1:
			case 3:
				CreateTargetFace($TourId, 1, '185m', 'X%', '1', 5, 750);
				CreateTargetFace($TourId, 2, '120m', 'RI%', '1', 5, 750);
				CreateTargetFace($TourId, 3, '145m', 'RCW', '1', 5, 750);
				CreateTargetFace($TourId, 4, '145m', 'RCM', '1', 5, 750);
				CreateTargetFace($TourId, 5, '145m', 'R_W', '1', 5, 750);
				CreateTargetFace($TourId, 6, '165m', 'R_M', '1', 5, 750);
				CreateTargetFace($TourId, 7, '120m', 'LCM', '1', 5, 750);
				CreateTargetFace($TourId, 8, '120m', 'L_W', '1', 5, 750);
				CreateTargetFace($TourId, 9, '145m', 'L_M', '1', 5, 750);
				CreateTargetFace($TourId, 10, '80m', 'L_G', '1', 5, 750);
				CreateTargetFace($TourId, 11, '80m', 'L_B', '1', 5, 750);
				CreateTargetFace($TourId, 12, '145m', 'CI%', '1', 5, 750);
				CreateTargetFace($TourId, 13, '145m', 'CCW', '1', 5, 750);
				CreateTargetFace($TourId, 14, '165m', 'CCM', '1', 5, 750);
				CreateTargetFace($TourId, 15, '165m', 'C_W', '1', 5, 750);
				CreateTargetFace($TourId, 16, '185m', 'C_M', '1', 5, 750);
				CreateTargetFace($TourId, 17, '120m', 'BCM', '1', 5, 750);
				CreateTargetFace($TourId, 18, '120m', 'B_W', '1', 5, 750);
				CreateTargetFace($TourId, 19, '145m', 'B_M', '1', 5, 750);
				CreateTargetFace($TourId, 20, '80m', 'B_G', '1', 5, 750);
				CreateTargetFace($TourId, 21, '80m', 'B_B', '1', 5, 750);
				CreateTargetFace($TourId, 22, '80m', '_Y%', '1', 5, 750);
				CreateTargetFace($TourId, 23, '80m', '_K%', '1', 5, 750);
				break;
			case 2:
				CreateTargetFace($TourId, 1, '185m', 'X%', '1', 5, 750);
				CreateTargetFace($TourId, 3, '145m', 'RCW', '1', 5, 750);
				CreateTargetFace($TourId, 4, '145m', 'RCM', '1', 5, 750);
				CreateTargetFace($TourId, 5, '145m', 'R_W', '1', 5, 750);
				CreateTargetFace($TourId, 6, '165m', 'R_M', '1', 5, 750);
				CreateTargetFace($TourId, 7, '120m', 'LCM', '1', 5, 750);
				CreateTargetFace($TourId, 8, '120m', 'L_W', '1', 5, 750);
				CreateTargetFace($TourId, 9, '145m', 'L_M', '1', 5, 750);
				CreateTargetFace($TourId, 13, '145m', 'CCW', '1', 5, 750);
				CreateTargetFace($TourId, 14, '165m', 'CCM', '1', 5, 750);
				CreateTargetFace($TourId, 15, '165m', 'C_W', '1', 5, 750);
				CreateTargetFace($TourId, 16, '185m', 'C_M', '1', 5, 750);
				CreateTargetFace($TourId, 17, '120m', 'BCM', '1', 5, 750);
				CreateTargetFace($TourId, 18, '120m', 'B_W', '1', 5, 750);
				CreateTargetFace($TourId, 19, '145m', 'B_M', '1', 5, 750);
				break;
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
