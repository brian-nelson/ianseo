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
				CreateDistance($TourId, $TourType, 'BCM', '55m-1', '45m-2', '35m-3', '25m-4');
				CreateDistance($TourId, $TourType, 'BCW', '55m-1', '45m-2', '35m-3', '25m-4');
				CreateDistance($TourId, $TourType, 'BI%', '40m-1', '35m-2', '30m-3', '25m-4');
				CreateDistance($TourId, $TourType, 'BJM', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'BJW', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'BMM', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'BMW', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'BSM', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'BSW', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'BV%', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'CCM', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'CCW', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'CI%', '55m-1', '45m-2', '35m-3', '25m-4');
				CreateDistance($TourId, $TourType, 'CJM', '90m-1', '70m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'CJW', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'CMM', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'CMW', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'CSM', '90m-1', '70m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'CSW', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'LC%', '55m-1', '45m-2', '35m-3', '25m-4');
				CreateDistance($TourId, $TourType, 'LI%', '40m-1', '35m-2', '30m-3', '25m-4');
				CreateDistance($TourId, $TourType, 'L_M', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'L_W', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'RCM', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'RCW', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'RI%', '55m-1', '45m-2', '35m-3', '25m-4');
				CreateDistance($TourId, $TourType, 'RJM', '90m-1', '70m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'RJW', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'RMM', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'RMW', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'RSM', '90m-1', '70m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'RSW', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, '_K%', '25m-1', '20m-2', '15m-3', '10m-4');
				CreateDistance($TourId, $TourType, '_Y%', '40m-1', '35m-2', '30m-3', '25m-4');	
				break;
			case '2':
				CreateDistance($TourId, $TourType, 'BCM', '55m-1', '45m-2', '35m-3', '25m-4');
				CreateDistance($TourId, $TourType, 'BCW', '55m-1', '45m-2', '35m-3', '25m-4');
				CreateDistance($TourId, $TourType, 'BJM', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'BJW', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'BMM', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'BMW', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'BSM', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'BSW', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'BV%', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'CCM', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'CCW', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'CJM', '90m-1', '70m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'CJW', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'CMM', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'CMW', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'CSM', '90m-1', '70m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'CSW', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'LC%', '55m-1', '45m-2', '35m-3', '25m-4');
				CreateDistance($TourId, $TourType, 'L_M', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'L_W', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'RCM', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'RCW', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'RJM', '90m-1', '70m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'RJW', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'RMM', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'RMW', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'RSM', '90m-1', '70m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'RSW', '70m-1', '60m-2', '50m-3', '30m-4');
				break;
			case '3':
				CreateDistance($TourId, $TourType, 'BCM', '55m-1', '45m-2', '35m-3', '25m-4');
				CreateDistance($TourId, $TourType, 'BCW', '55m-1', '45m-2', '35m-3', '25m-4');
				CreateDistance($TourId, $TourType, 'BI%', '40m-1', '35m-2', '30m-3', '25m-4');
				CreateDistance($TourId, $TourType, 'BJM', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'BJW', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'CCM', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'CCW', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'CI%', '55m-1', '45m-2', '35m-3', '25m-4');
				CreateDistance($TourId, $TourType, 'CJM', '90m-1', '70m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'CJW', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'LC%', '55m-1', '45m-2', '35m-3', '25m-4');
				CreateDistance($TourId, $TourType, 'LI%', '40m-1', '35m-2', '30m-3', '25m-4');
				CreateDistance($TourId, $TourType, 'LJM', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'LJW', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'RCM', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'RCW', '60m-1', '50m-2', '40m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'RI%', '55m-1', '45m-2', '35m-3', '25m-4');
				CreateDistance($TourId, $TourType, 'RJM', '90m-1', '70m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, 'RJW', '70m-1', '60m-2', '50m-3', '30m-4');
				CreateDistance($TourId, $TourType, '_K%', '25m-1', '20m-2', '15m-3', '10m-4');
				CreateDistance($TourId, $TourType, '_Y%', '40m-1', '35m-2', '30m-3', '25m-4');	
				break;
		}
		break;
	case 2:
		switch($SubRule) {
			case '1':
				CreateDistance($TourId, $TourType, 'BCM', '55m-1', '45m-2', '35m-3', '25m-4', '55m-5', '45m-6', '35m-7', '25m-8');
				CreateDistance($TourId, $TourType, 'BCW', '55m-1', '45m-2', '35m-3', '25m-4', '55m-5', '45m-6', '35m-7', '25m-8');
				CreateDistance($TourId, $TourType, 'BI%', '40m-1', '35m-2', '30m-3', '25m-4', '40m-5', '35m-6', '30m-7', '25m-8');
				CreateDistance($TourId, $TourType, 'BJM', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'BJW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'BMM', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'BMW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'BSM', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'BSW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'BV%', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'CCM', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'CCW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'CI%', '55m-1', '45m-2', '35m-3', '25m-4', '55m-5', '45m-6', '35m-7', '25m-8');
				CreateDistance($TourId, $TourType, 'CJM', '90m-1', '70m-2', '50m-3', '30m-4', '90m-5', '70m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'CJW', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'CMM', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'CMW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'CSM', '90m-1', '70m-2', '50m-3', '30m-4', '90m-5', '70m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'CSW', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'LC%', '55m-1', '45m-2', '35m-3', '25m-4', '55m-5', '45m-6', '35m-7', '25m-8');
				CreateDistance($TourId, $TourType, 'LI%', '40m-1', '35m-2', '30m-3', '25m-4', '40m-5', '35m-6', '30m-7', '25m-8');
				CreateDistance($TourId, $TourType, 'L_M', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'L_W', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'RCM', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'RCW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'RI%', '55m-1', '45m-2', '35m-3', '25m-4', '55m-5', '45m-6', '35m-7', '25m-8');
				CreateDistance($TourId, $TourType, 'RJM', '90m-1', '70m-2', '50m-3', '30m-4', '90m-5', '70m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'RJW', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'RMM', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'RMW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'RSM', '90m-1', '70m-2', '50m-3', '30m-4', '90m-5', '70m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'RSW', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, '_K%', '25m-1', '20m-2', '15m-3', '10m-4', '25m-5', '20m-6', '15m-7', '10m-8');
				CreateDistance($TourId, $TourType, '_Y%', '40m-1', '35m-2', '30m-3', '25m-4', '40m-5', '35m-6', '30m-7', '25m-8');
				break;
			case '2':
				CreateDistance($TourId, $TourType, 'BCM', '55m-1', '45m-2', '35m-3', '25m-4', '55m-5', '45m-6', '35m-7', '25m-8');
				CreateDistance($TourId, $TourType, 'BCW', '55m-1', '45m-2', '35m-3', '25m-4', '55m-5', '45m-6', '35m-7', '25m-8');
				CreateDistance($TourId, $TourType, 'BJM', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'BJW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'BMM', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'BMW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'BSM', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'BSW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'BV%', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'CCM', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'CCW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'CJM', '90m-1', '70m-2', '50m-3', '30m-4', '90m-5', '70m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'CJW', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'CMM', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'CMW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'CSM', '90m-1', '70m-2', '50m-3', '30m-4', '90m-5', '70m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'CSW', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'LC%', '55m-1', '45m-2', '35m-3', '25m-4', '55m-5', '45m-6', '35m-7', '25m-8');
				CreateDistance($TourId, $TourType, 'L_M', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'L_W', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'RCM', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'RCW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'RJM', '90m-1', '70m-2', '50m-3', '30m-4', '90m-5', '70m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'RJW', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'RMM', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'RMW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'RSM', '90m-1', '70m-2', '50m-3', '30m-4', '90m-5', '70m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'RSW', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				break;
			case '3':
				CreateDistance($TourId, $TourType, 'BCM', '55m-1', '45m-2', '35m-3', '25m-4', '55m-5', '45m-6', '35m-7', '25m-8');
				CreateDistance($TourId, $TourType, 'BCW', '55m-1', '45m-2', '35m-3', '25m-4', '55m-5', '45m-6', '35m-7', '25m-8');
				CreateDistance($TourId, $TourType, 'BI%', '40m-1', '35m-2', '30m-3', '25m-4', '40m-5', '35m-6', '30m-7', '25m-8');
				CreateDistance($TourId, $TourType, 'BJM', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'BJW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'CCM', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'CCW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'CI%', '55m-1', '45m-2', '35m-3', '25m-4', '55m-5', '45m-6', '35m-7', '25m-8');
				CreateDistance($TourId, $TourType, 'CJM', '90m-1', '70m-2', '50m-3', '30m-4', '90m-5', '70m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'CJW', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'LC%', '55m-1', '45m-2', '35m-3', '25m-4', '55m-5', '45m-6', '35m-7', '25m-8');
				CreateDistance($TourId, $TourType, 'LI%', '40m-1', '35m-2', '30m-3', '25m-4', '40m-5', '35m-6', '30m-7', '25m-8');
				CreateDistance($TourId, $TourType, 'LJM', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'LJW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'RCM', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'RCW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '50m-6', '40m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'RI%', '55m-1', '45m-2', '35m-3', '25m-4', '55m-5', '45m-6', '35m-7', '25m-8');
				CreateDistance($TourId, $TourType, 'RJM', '90m-1', '70m-2', '50m-3', '30m-4', '90m-5', '70m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, 'RJW', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '60m-6', '50m-7', '30m-8');
				CreateDistance($TourId, $TourType, '_K%', '25m-1', '20m-2', '15m-3', '10m-4', '25m-5', '20m-6', '15m-7', '10m-8');
				CreateDistance($TourId, $TourType, '_Y%', '40m-1', '35m-2', '30m-3', '25m-4', '40m-5', '35m-6', '30m-7', '25m-8');
				break;
		}
		break;
	case 3:
		switch($SubRule) {
			case '1':
			case '2':
				CreateDistance($TourId, $TourType, 'RSM', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'RSW', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'RJ_', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'RC_', '60m-1', '60m-2');
				CreateDistance($TourId, $TourType, 'RM_', '60m-1', '60m-2');
				CreateDistance($TourId, $TourType, 'C%', '50m-1', '50m-2');
				break;
			case '3':
				CreateDistance($TourId, $TourType, 'RJ_', '70m-1', '70m-2');
				CreateDistance($TourId, $TourType, 'RC_', '60m-1', '60m-2');
				CreateDistance($TourId, $TourType, 'CJ_', '50m-1', '50m-2');
				CreateDistance($TourId, $TourType, 'CC_', '50m-1', '50m-2');
				break;
		}
		break;
	case 5:
		CreateDistance($TourId, $TourType, '%', '60 m', '50 m', '40 m');
		break;
	case 6:
		CreateDistance($TourId, $TourType, '%', '18m-1', '18m-2');
		break;
	case 7:
		CreateDistance($TourId, $TourType, '%', '25m-1', '25m-2');
		break;
	case 8:
		CreateDistance($TourId, $TourType, '%', '25m-1', '25m-2', '18m-1', '18m-2');
		break;
	case 34:
		switch($SubRule) {
			case '1':
				CreateDistance($TourId, $TourType, 'BCM', '55m-1', '45m-2', '35m-3', '25m-4', '45m-5', '45m-6');
				CreateDistance($TourId, $TourType, 'BCW', '55m-1', '45m-2', '35m-3', '25m-4', '45m-5', '45m-6');
				CreateDistance($TourId, $TourType, 'BI%', '40m-1', '35m-2', '30m-3', '25m-4', '35m-5', '35m-6');
				CreateDistance($TourId, $TourType, 'BJM', '70m-1', '60m-2', '50m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'BJW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'BMM', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'BMW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'BSM', '70m-1', '60m-2', '50m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'BSW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'BV%', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'CCM', '70m-1', '60m-2', '50m-3', '30m-4', '50m-5', '50m-6');
				CreateDistance($TourId, $TourType, 'CCW', '60m-1', '50m-2', '40m-3', '30m-4', '50m-5', '50m-6');
				CreateDistance($TourId, $TourType, 'CI%', '55m-1', '45m-2', '35m-3', '25m-4', '45m-5', '45m-6');
				CreateDistance($TourId, $TourType, 'CJM', '90m-1', '70m-2', '50m-3', '30m-4', '50m-5', '50m-6');
				CreateDistance($TourId, $TourType, 'CJW', '70m-1', '60m-2', '50m-3', '30m-4', '50m-5', '50m-6');
				CreateDistance($TourId, $TourType, 'CMM', '70m-1', '60m-2', '50m-3', '30m-4', '50m-5', '50m-6');
				CreateDistance($TourId, $TourType, 'CMW', '60m-1', '50m-2', '40m-3', '30m-4', '50m-5', '50m-6');
				CreateDistance($TourId, $TourType, 'CSM', '90m-1', '70m-2', '50m-3', '30m-4', '50m-5', '50m-6');
				CreateDistance($TourId, $TourType, 'CSW', '70m-1', '60m-2', '50m-3', '30m-4', '50m-5', '50m-6');
				CreateDistance($TourId, $TourType, 'LC%', '55m-1', '45m-2', '35m-3', '25m-4', '45m-5', '45m-6');
				CreateDistance($TourId, $TourType, 'LI%', '40m-1', '35m-2', '30m-3', '25m-4', '35m-5', '35m-6');
				CreateDistance($TourId, $TourType, 'L_M', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'L_W', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'RCM', '70m-1', '60m-2', '50m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'RCW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'RI%', '55m-1', '45m-2', '35m-3', '25m-4', '45m-5', '45m-6');
				CreateDistance($TourId, $TourType, 'RJM', '90m-1', '70m-2', '50m-3', '30m-4', '70m-5', '70m-6');
				CreateDistance($TourId, $TourType, 'RJW', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '70m-6');
				CreateDistance($TourId, $TourType, 'RMM', '70m-1', '60m-2', '50m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'RMW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'RSM', '90m-1', '70m-2', '50m-3', '30m-4', '70m-5', '70m-6');
				CreateDistance($TourId, $TourType, 'RSW', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '70m-6');
				CreateDistance($TourId, $TourType, '_K%', '25m-1', '20m-2', '15m-3', '10m-4', '20m-5', '20m-6');
				CreateDistance($TourId, $TourType, '_Y%', '40m-1', '35m-2', '30m-3', '25m-4', '35m-5', '35m-6');
				break;
			case '2':
				CreateDistance($TourId, $TourType, 'BCM', '55m-1', '45m-2', '35m-3', '25m-4', '45m-5', '45m-6');
				CreateDistance($TourId, $TourType, 'BCW', '55m-1', '45m-2', '35m-3', '25m-4', '45m-5', '45m-6');
				CreateDistance($TourId, $TourType, 'BJM', '70m-1', '60m-2', '50m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'BJW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'BMM', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'BMW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'BSM', '70m-1', '60m-2', '50m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'BSW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'BV%', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'CCM', '70m-1', '60m-2', '50m-3', '30m-4', '50m-5', '50m-6');
				CreateDistance($TourId, $TourType, 'CCW', '60m-1', '50m-2', '40m-3', '30m-4', '50m-5', '50m-6');
				CreateDistance($TourId, $TourType, 'CJM', '90m-1', '70m-2', '50m-3', '30m-4', '50m-5', '50m-6');
				CreateDistance($TourId, $TourType, 'CJW', '70m-1', '60m-2', '50m-3', '30m-4', '50m-5', '50m-6');
				CreateDistance($TourId, $TourType, 'CMM', '70m-1', '60m-2', '50m-3', '30m-4', '50m-5', '50m-6');
				CreateDistance($TourId, $TourType, 'CMW', '60m-1', '50m-2', '40m-3', '30m-4', '50m-5', '50m-6');
				CreateDistance($TourId, $TourType, 'CSM', '90m-1', '70m-2', '50m-3', '30m-4', '50m-5', '50m-6');
				CreateDistance($TourId, $TourType, 'CSW', '70m-1', '60m-2', '50m-3', '30m-4', '50m-5', '50m-6');
				CreateDistance($TourId, $TourType, 'LC%', '55m-1', '45m-2', '35m-3', '25m-4', '45m-5', '45m-6');
				CreateDistance($TourId, $TourType, 'L_M', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'L_W', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'RCM', '70m-1', '60m-2', '50m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'RCW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'RJM', '90m-1', '70m-2', '50m-3', '30m-4', '70m-5', '70m-6');
				CreateDistance($TourId, $TourType, 'RJW', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '70m-6');
				CreateDistance($TourId, $TourType, 'RMM', '70m-1', '60m-2', '50m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'RMW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'RSM', '90m-1', '70m-2', '50m-3', '30m-4', '70m-5', '70m-6');
				CreateDistance($TourId, $TourType, 'RSW', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '70m-6');
				break;
			case '3':
				CreateDistance($TourId, $TourType, 'BCM', '55m-1', '45m-2', '35m-3', '25m-4', '45m-5', '45m-6');
				CreateDistance($TourId, $TourType, 'BCW', '55m-1', '45m-2', '35m-3', '25m-4', '45m-5', '45m-6');
				CreateDistance($TourId, $TourType, 'BI%', '40m-1', '35m-2', '30m-3', '25m-4', '35m-5', '35m-6');
				CreateDistance($TourId, $TourType, 'BJM', '70m-1', '60m-2', '50m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'BJW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'CCM', '70m-1', '60m-2', '50m-3', '30m-4', '50m-5', '50m-6');
				CreateDistance($TourId, $TourType, 'CCW', '60m-1', '50m-2', '40m-3', '30m-4', '50m-5', '50m-6');
				CreateDistance($TourId, $TourType, 'CI%', '55m-1', '45m-2', '35m-3', '25m-4', '45m-5', '45m-6');
				CreateDistance($TourId, $TourType, 'CJM', '90m-1', '70m-2', '50m-3', '30m-4', '50m-5', '50m-6');
				CreateDistance($TourId, $TourType, 'CJW', '70m-1', '60m-2', '50m-3', '30m-4', '50m-5', '50m-6');
				CreateDistance($TourId, $TourType, 'LC%', '55m-1', '45m-2', '35m-3', '25m-4', '45m-5', '45m-6');
				CreateDistance($TourId, $TourType, 'LI%', '40m-1', '35m-2', '30m-3', '25m-4', '35m-5', '35m-6');
				CreateDistance($TourId, $TourType, 'LJM', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'LJW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'RCM', '70m-1', '60m-2', '50m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'RCW', '60m-1', '50m-2', '40m-3', '30m-4', '60m-5', '60m-6');
				CreateDistance($TourId, $TourType, 'RI%', '55m-1', '45m-2', '35m-3', '25m-4', '45m-5', '45m-6');
				CreateDistance($TourId, $TourType, 'RJM', '90m-1', '70m-2', '50m-3', '30m-4', '70m-5', '70m-6');
				CreateDistance($TourId, $TourType, 'RJW', '70m-1', '60m-2', '50m-3', '30m-4', '70m-5', '70m-6');
				CreateDistance($TourId, $TourType, '_K%', '25m-1', '20m-2', '15m-3', '10m-4', '20m-5', '20m-6');
				CreateDistance($TourId, $TourType, '_Y%', '40m-1', '35m-2', '30m-3', '25m-4', '35m-5', '35m-6');
				break;
			}
			break;
		case 35:
			switch($SubRule) {
				case '1':
				case '3':
					CreateDistance($TourId, $TourType, 'X%','185m');
					CreateDistance($TourId, $TourType, 'RI%','120m');
					CreateDistance($TourId, $TourType, 'RCW','145m');
					CreateDistance($TourId, $TourType, 'RCM','145m');
					CreateDistance($TourId, $TourType, 'R_W','145m');
					CreateDistance($TourId, $TourType, 'R_M','165m');
					CreateDistance($TourId, $TourType, 'LCM','120m');
					CreateDistance($TourId, $TourType, 'L_W','120m');
					CreateDistance($TourId, $TourType, 'L_M','145m');
					CreateDistance($TourId, $TourType, 'L_G','80m');
					CreateDistance($TourId, $TourType, 'L_B','80m');
					CreateDistance($TourId, $TourType, 'CI%','145m');
					CreateDistance($TourId, $TourType, 'CCW','145m');
					CreateDistance($TourId, $TourType, 'CCM','165m');
					CreateDistance($TourId, $TourType, 'C_W','165m');
					CreateDistance($TourId, $TourType, 'C_M','185m');
					CreateDistance($TourId, $TourType, 'BCM','120m');
					CreateDistance($TourId, $TourType, 'B_W','120m');
					CreateDistance($TourId, $TourType, 'B_M','145m');
					CreateDistance($TourId, $TourType, 'B_G','80m');
					CreateDistance($TourId, $TourType, 'B_B','80m');
					CreateDistance($TourId, $TourType, '_Y%','80m');
					CreateDistance($TourId, $TourType, '_K%','80m');
					break;
				case '2':
					CreateDistance($TourId, $TourType, 'X%','185m');
					CreateDistance($TourId, $TourType, 'RCW','145m');
					CreateDistance($TourId, $TourType, 'RCM','145m');
					CreateDistance($TourId, $TourType, 'R_W','145m');
					CreateDistance($TourId, $TourType, 'R_M','165m');
					CreateDistance($TourId, $TourType, 'LCM','120m');
					CreateDistance($TourId, $TourType, 'L_W','120m');
					CreateDistance($TourId, $TourType, 'L_M','145m');
					CreateDistance($TourId, $TourType, 'CCW','145m');
					CreateDistance($TourId, $TourType, 'CCM','165m');
					CreateDistance($TourId, $TourType, 'C_W','165m');
					CreateDistance($TourId, $TourType, 'C_M','185m');
					CreateDistance($TourId, $TourType, 'BCM','120m');
					CreateDistance($TourId, $TourType, 'B_W','120m');
					CreateDistance($TourId, $TourType, 'B_M','145m');
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