<?php
/*
Common Setup for "Target" Archery
*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId,(in_array($TourType,array(3,6,7,8,37)) ? '70M':'FITA'), $SubRule);

// default SubClasses
//CreateSubClass($TourId, 1, '00', '00');

// default Classes
CreateStandardClasses($TourId, $SubRule);

// default Distances

switch($TourType) {
    case 1:
        CreateDistanceNew($TourId, $TourType, 'RK%',  array(array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'RS%',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'RCW',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RCM',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RJW',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RJM',  array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RW',   array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RM',   array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RMW',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RMM',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RMU',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RV%',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));

        CreateDistanceNew($TourId, $TourType, 'CK%',  array(array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'CS%',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'CCW',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CCM',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CJW',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CJM',  array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CW',   array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CM',   array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CMW',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CMM',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CMU',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CV%',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));

        CreateDistanceNew($TourId, $TourType, 'BK%',  array(array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'BS%',  array(array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'BC%',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'BJ%',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'BM%',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'BV%',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'B_',   array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));

        CreateDistanceNew($TourId, $TourType, 'W1K%', array(array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'W1S%', array(array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'W1C%', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'W1J%', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'W1M%', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'W1V%', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'W1_',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));

        CreateDistanceNew($TourId, $TourType, 'I%',   array(array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'L%',   array(array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        break;
	case 3:
		switch($SubRule) {
			case '1':
                CreateDistanceNew($TourId, $TourType, 'RC%',  array(array('60m-1', 60), array('60m-2', 60)));
                CreateDistanceNew($TourId, $TourType, 'RJ%',  array(array('70m-1', 70), array('70m-2', 70)));
                CreateDistanceNew($TourId, $TourType, 'RK%',  array(array('30m-1', 30), array('30m-2', 30)));
                CreateDistanceNew($TourId, $TourType, 'RM',   array(array('70m-1', 70), array('70m-2', 70)));
                CreateDistanceNew($TourId, $TourType, 'RM_',  array(array('60m-1', 60), array('60m-2', 60)));
                CreateDistanceNew($TourId, $TourType, 'RS%',  array(array('40m-1', 40), array('40m-2', 40)));
                CreateDistanceNew($TourId, $TourType, 'RV%',  array(array('60m-1', 60), array('60m-2', 60)));
                CreateDistanceNew($TourId, $TourType, 'RW',   array(array('70m-1', 70), array('70m-2', 70)));

                CreateDistanceNew($TourId, $TourType, 'ROC%',  array(array('60m-1', 60), array('60m-2', 60)));
                CreateDistanceNew($TourId, $TourType, 'ROJ%',  array(array('70m-1', 70), array('70m-2', 70)));
                CreateDistanceNew($TourId, $TourType, 'ROK%',  array(array('30m-1', 30), array('30m-2', 30)));
                CreateDistanceNew($TourId, $TourType, 'ROM',   array(array('70m-1', 70), array('70m-2', 70)));
                CreateDistanceNew($TourId, $TourType, 'ROM_',  array(array('60m-1', 60), array('60m-2', 60)));
                CreateDistanceNew($TourId, $TourType, 'ROS%',  array(array('40m-1', 40), array('40m-2', 40)));
                CreateDistanceNew($TourId, $TourType, 'ROV%',  array(array('60m-1', 60), array('60m-2', 60)));
                CreateDistanceNew($TourId, $TourType, 'ROW',   array(array('70m-1', 70), array('70m-2', 70)));

                CreateDistanceNew($TourId, $TourType, 'CC%',  array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'CJ%',  array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'CK%',  array(array('30m-1', 30), array('30m-2', 30)));
                CreateDistanceNew($TourId, $TourType, 'CM%',  array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'CS%',  array(array('40m-1', 40), array('40m-2', 40)));
                CreateDistanceNew($TourId, $TourType, 'CV%',  array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'CW%',  array(array('50m-1', 50), array('50m-2', 50)));

                CreateDistanceNew($TourId, $TourType, 'COC%',  array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'COJ%',  array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'COK%',  array(array('30m-1', 30), array('30m-2', 30)));
                CreateDistanceNew($TourId, $TourType, 'COM%',  array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'COS%',  array(array('40m-1', 40), array('40m-2', 40)));
                CreateDistanceNew($TourId, $TourType, 'COV%',  array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'COW%',  array(array('50m-1', 50), array('50m-2', 50)));

                CreateDistanceNew($TourId, $TourType, 'W1C%', array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'W1J%', array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'W1K%', array(array('30m-1', 30), array('30m-2', 30)));
                CreateDistanceNew($TourId, $TourType, 'W1M%', array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'W1S%', array(array('40m-1', 40), array('40m-2', 40)));
                CreateDistanceNew($TourId, $TourType, 'W1V%', array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'W1W%', array(array('50m-1', 50), array('50m-2', 50)));

                CreateDistanceNew($TourId, $TourType, 'BC%',  array(array('40m-1', 40), array('40m-2', 40)));
                CreateDistanceNew($TourId, $TourType, 'BJ%',  array(array('40m-1', 40), array('40m-2', 40)));
                CreateDistanceNew($TourId, $TourType, 'BK%',  array(array('30m-1', 30), array('30m-2', 30)));
                CreateDistanceNew($TourId, $TourType, 'BM%',  array(array('40m-1', 40), array('40m-2', 40)));
                CreateDistanceNew($TourId, $TourType, 'BS%',  array(array('30m-1', 30), array('30m-2', 30)));
                CreateDistanceNew($TourId, $TourType, 'BV%',  array(array('40m-1', 40), array('40m-2', 40)));
                CreateDistanceNew($TourId, $TourType, 'BW%',  array(array('40m-1', 40), array('40m-2', 40)));

                CreateDistanceNew($TourId, $TourType, 'I%',   array(array('30m-1', 30), array('30m-2', 30)));
                CreateDistanceNew($TourId, $TourType, 'L%',   array(array('30m-1', 30), array('30m-2', 30)));
				break;
			case '2':
				CreateDistanceNew($TourId, $TourType, 'R%', array(array('70m-1',70), array('70m-2',70)));
                CreateDistanceNew($TourId, $TourType, 'C%', array(array('50m-1',50), array('50m-2',50)));
                CreateDistanceNew($TourId, $TourType, 'W1%', array(array('50m-1',50), array('50m-2',50)));
				break;
		}
		break;
    case 5:
        CreateDistanceNew($TourId, $TourType, '%K_', array(array('30m',30), array('25m',25),array('20m',20)));

        CreateDistanceNew($TourId, $TourType, 'BS_', array(array('30m',30), array('25m',25),array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'BC_', array(array('40m',40), array('35m',35),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'BJ_', array(array('40m',40), array('35m',35),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'BM_', array(array('40m',40), array('35m',35),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'BV_', array(array('40m',40), array('35m',35),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'BW', array(array('40m',40), array('35m',35),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'BM', array(array('40m',40), array('35m',35),array('30m',30)));

        CreateDistanceNew($TourId, $TourType, 'ROS_', array(array('40m',40), array('35m',35),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ROC_', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'ROJ_', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'ROM_', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'ROV_', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'ROW', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'ROM', array(array('60m',60), array('50m',50),array('40m',40)));

        CreateDistanceNew($TourId, $TourType, 'CS_', array(array('40m',40), array('35m',35),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CC_', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'CJ_', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'CM_', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'CV_', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'CW', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'CM', array(array('60m',60), array('50m',50),array('40m',40)));

        CreateDistanceNew($TourId, $TourType, 'COS_', array(array('40m',40), array('35m',35),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'COC_', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'COJ_', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'COM_', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'COV_', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'COW', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'COM', array(array('60m',60), array('50m',50),array('40m',40)));

        CreateDistanceNew($TourId, $TourType, 'W1S_', array(array('40m',40), array('35m',35),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'W1C_', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'W1J_', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'W1M_', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'W1V_', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'W1W', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'W1M', array(array('60m',60), array('50m',50),array('40m',40)));

        CreateDistanceNew($TourId, $TourType, 'I%', array(array('30m',30), array('25m',25),array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'L%', array(array('30m',30), array('25m',25),array('20m',20)));
        break;
	case 6:
		CreateDistanceNew($TourId, $TourType, 'R%',   array(array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'C%',   array(array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'W1_',   array(array('18m-1',18), array('18m-2',18)));
        if($SubRule==1) {
        	CreateDistanceNew($TourId, $TourType, 'B%',   array(array('18m-1',18), array('18m-2',18)));
	        CreateDistanceNew($TourId, $TourType, 'I%',   array(array('18m-1',18), array('18m-2',18)));
	        CreateDistanceNew($TourId, $TourType, 'L%',   array(array('18m-1',18), array('18m-2',18)));
	        CreateDistanceNew($TourId, $TourType, 'W1K%', array(array('10m-1',10), array('10m-2',10)));
	        CreateDistanceNew($TourId, $TourType, 'W1S%', array(array('10m-1',10), array('10m-2',10)));
	        CreateDistanceNew($TourId, $TourType, 'W1C%', array(array('18m-1',18), array('18m-2',18)));
	        CreateDistanceNew($TourId, $TourType, 'W1J%', array(array('18m-1',18), array('18m-2',18)));
	        CreateDistanceNew($TourId, $TourType, 'W1V%', array(array('18m-1',18), array('18m-2',18)));
	        CreateDistanceNew($TourId, $TourType, 'W1M_', array(array('18m-1',18), array('18m-2',18)));
        }
		break;
	case 7:
        CreateDistanceNew($TourId, $TourType, 'R%',   array(array('25m-1',25), array('25m-2',25)));
        CreateDistanceNew($TourId, $TourType, 'C%',   array(array('25m-1',25), array('25m-2',25)));
        CreateDistanceNew($TourId, $TourType, 'W1_',   array(array('25m-1',25), array('25m-2',25)));
        if($SubRule==1) {
            CreateDistanceNew($TourId, $TourType, 'B%',   array(array('25m-1',25), array('25m-2',25)));
            CreateDistanceNew($TourId, $TourType, 'I%',   array(array('25m-1',25), array('25m-2',25)));
            CreateDistanceNew($TourId, $TourType, 'L%',   array(array('25m-1',25), array('25m-2',25)));
            CreateDistanceNew($TourId, $TourType, 'W1K%', array(array('20m-1',20), array('20m-2',20)));
            CreateDistanceNew($TourId, $TourType, 'W1S%', array(array('20m-1',20), array('20m-2',20)));
            CreateDistanceNew($TourId, $TourType, 'W1C%', array(array('25m-1',25), array('25m-2',25)));
            CreateDistanceNew($TourId, $TourType, 'W1J%', array(array('25m-1',25), array('25m-2',25)));
            CreateDistanceNew($TourId, $TourType, 'W1V%', array(array('25m-1',25), array('25m-2',25)));
            CreateDistanceNew($TourId, $TourType, 'W1M_', array(array('25m-1',25), array('25m-2',25)));
        }
		break;
	case 8:
        CreateDistanceNew($TourId, $TourType, 'R%',   array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'C%',   array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'W1_',   array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        if($SubRule==1) {
            CreateDistanceNew($TourId, $TourType, 'B%',   array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'I%',   array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'L%',   array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'W1K%', array(array('20m-1',20), array('20m-2',20), array('10m-1',10), array('10m-2',10)));
            CreateDistanceNew($TourId, $TourType, 'W1S%', array(array('20m-1',20), array('20m-2',20), array('10m-1',10), array('10m-2',10)));
            CreateDistanceNew($TourId, $TourType, 'W1C%', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'W1J%', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'W1V%', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'W1M_', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        }
		break;
    case 37:
        switch($SubRule) {
            case '1':
                CreateDistanceNew($TourId, $TourType, 'RC%',  array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
                CreateDistanceNew($TourId, $TourType, 'RJ%',  array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
                CreateDistanceNew($TourId, $TourType, 'RK%',  array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
                CreateDistanceNew($TourId, $TourType, 'RM',   array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
                CreateDistanceNew($TourId, $TourType, 'RM_',  array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
                CreateDistanceNew($TourId, $TourType, 'RS%',  array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'RV%',  array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
                CreateDistanceNew($TourId, $TourType, 'RW',   array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));

                CreateDistanceNew($TourId, $TourType, 'ROC%',  array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
                CreateDistanceNew($TourId, $TourType, 'ROJ%',  array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
                CreateDistanceNew($TourId, $TourType, 'ROK%',  array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
                CreateDistanceNew($TourId, $TourType, 'ROM',   array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
                CreateDistanceNew($TourId, $TourType, 'ROM_',  array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
                CreateDistanceNew($TourId, $TourType, 'ROS%',  array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'ROV%',  array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
                CreateDistanceNew($TourId, $TourType, 'ROW',   array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));

                CreateDistanceNew($TourId, $TourType, 'CC%',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'CJ%',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'CK%',  array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
                CreateDistanceNew($TourId, $TourType, 'CM',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'CM_',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'CS%',  array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'CV%',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'CW',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));

                CreateDistanceNew($TourId, $TourType, 'COC%',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'COJ%',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'COK%',  array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
                CreateDistanceNew($TourId, $TourType, 'COM',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'COM_',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'COS%',  array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'COV%',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'COW',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));


                CreateDistanceNew($TourId, $TourType, 'W1C%', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'W1J%', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'W1K%', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
                CreateDistanceNew($TourId, $TourType, 'W1M', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'W1M_', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'W1S%', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'W1V%', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'W1W%', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));

                CreateDistanceNew($TourId, $TourType, 'BC%',  array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'BJ%',  array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'BK%',  array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
                CreateDistanceNew($TourId, $TourType, 'BM%',  array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'BS%',  array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
                CreateDistanceNew($TourId, $TourType, 'BV%',  array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'BW%',  array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));

                CreateDistanceNew($TourId, $TourType, 'I%',   array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
                CreateDistanceNew($TourId, $TourType, 'L%',   array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
                break;
            case '2':
                CreateDistanceNew($TourId, $TourType, 'R%', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
                CreateDistanceNew($TourId, $TourType, 'C%', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'W1%', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                break;
        }
        break;
}


if($TourType==3 or $TourType==6 or $TourType==37) {
	// default Events
	CreateStandardEvents($TourId, $SubRule, $TourType!=6, in_array($TourType,array(3,6,7,8,37)));

	// Classes in Events
	InsertStandardEvents($TourId, $SubRule, $TourType!=6);

	// Finals & TeamFinals
	CreateFinals($TourId);
}


// Default Target
switch($TourType) {
    case 1:
        CreateTargetFace($TourId, 1, '~Default', '%', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
        CreateTargetFace($TourId, 2, '~50: 5-X/30: 5-X', 'REG-^R|^C', '',TGT_OUT_FULL, 122, TGT_OUT_FULL, 122,TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
        break;
	case 3:
	    if ($SubRule==1) {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^R|^B|^I|^L|^CS|^CK|^W1K|^W1S', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C[^S|^K]', '1', TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
            CreateTargetFace($TourId, 3, '~Default', 'REG-^W1[^S|^K]', '1', TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
        } else {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^R', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C', '1', TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
            CreateTargetFace($TourId, 3, '~Default', 'REG-^W1[^S|^K]', '1', TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
        }
		break;
    case 5:
    case 1:
        CreateTargetFace($TourId, 1, '~Default', '%', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
        break;
	case 6:
        if($SubRule==1) {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^R[^S|^K]', '1', TGT_IND_6_big10, 40, TGT_IND_6_big10, 40);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C[^S|^K]|W1[^S|^K|^C]', '1', TGT_IND_6_small10, 40, TGT_IND_6_small10, 40);
            CreateTargetFace($TourId, 3, '~Default', 'REG-^RS|^RK|^CS|^CK', '1', TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
            CreateTargetFace($TourId, 4, '~Default', 'REG-^B', '1', TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
            CreateTargetFace($TourId, 5, '~Default', 'REG-^I|^L', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);
            CreateTargetFace($TourId, 6, '~Default', 'REG-^W1S|^W1K', '1', TGT_IND_1_big10, 80, TGT_IND_1_big10, 80);
            CreateTargetFace($TourId, 7, '~Default', 'REG-^W1C', '1', TGT_IND_1_small10, 60, TGT_IND_1_small10, 60);
        } else {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^R', '1', TGT_IND_6_big10, 60, TGT_IND_6_big10, 60);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C|^W1', '1', TGT_IND_6_small10, 60, TGT_IND_6_small10, 60);
        }
		break;
	case 7:
        if($SubRule==1) {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^R[^S|^K]', '1', TGT_IND_6_big10, 60, TGT_IND_6_big10, 60, TGT_IND_6_big10, 40, TGT_IND_6_big10, 40);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C[^S|^K]|W1[^S|^K|^C]', '1', TGT_IND_6_small10, 60, TGT_IND_6_small10, 60, TGT_IND_6_small10, 40, TGT_IND_6_small10, 40);
            CreateTargetFace($TourId, 3, '~Default', 'REG-^RS|^RK|^CS|^CK', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60, TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
            CreateTargetFace($TourId, 4, '~Default', 'REG-^B', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60, TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
            CreateTargetFace($TourId, 5, '~Default', 'REG-^I|^L', '1', TGT_IND_1_big10, 80, TGT_IND_1_big10, 80, TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);
            CreateTargetFace($TourId, 6, '~Default', 'REG-^W1S|^W1K', '1', TGT_IND_1_big10, 122, TGT_IND_1_big10, 122, TGT_IND_1_big10, 80, TGT_IND_1_big10, 80);
            CreateTargetFace($TourId, 7, '~Default', 'REG-^W1C', '1', TGT_IND_1_small10, 80, TGT_IND_1_small10, 80, TGT_IND_1_small10, 60, TGT_IND_1_small10, 60);
        } else {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^R', '1', TGT_IND_6_big10, 60, TGT_IND_6_big10, 60, TGT_IND_6_big10, 40, TGT_IND_6_big10, 40);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C|^W1', '1', TGT_IND_6_small10, 60, TGT_IND_6_small10, 60, TGT_IND_6_small10, 40, TGT_IND_6_small10, 40);
        }
		break;
	case 8:
        if($SubRule==1) {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^R[^S|^K]', '1', TGT_IND_6_big10, 60, TGT_IND_6_big10, 60);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C[^S|^K]|W1[^S|^K|^C]', '1', TGT_IND_6_small10, 60, TGT_IND_6_small10, 60);
            CreateTargetFace($TourId, 3, '~Default', 'REG-^RS|^RK|^CS|^CK', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);
            CreateTargetFace($TourId, 4, '~Default', 'REG-^B', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);
            CreateTargetFace($TourId, 5, '~Default', 'REG-^I|^L', '1', TGT_IND_1_big10, 80, TGT_IND_1_big10, 80);
            CreateTargetFace($TourId, 6, '~Default', 'REG-^W1S|^W1K', '1', TGT_IND_1_big10, 122, TGT_IND_1_big10, 122);
            CreateTargetFace($TourId, 7, '~Default', 'REG-^W1C', '1', TGT_IND_1_small10, 80, TGT_IND_1_small10, 80);
        } else {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^R', '1', TGT_IND_6_big10, 40, TGT_IND_6_big10, 40);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C', '1', TGT_IND_6_small10, 40, TGT_IND_6_small10, 40);
        }
		break;
    case 37:
        if ($SubRule==1) {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^R|^B|^I|^L|^CS|^CK|^W1K|^W1S', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122,TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C[^S|^K]', '1', TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80,TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
            CreateTargetFace($TourId, 3, '~Default', 'REG-^W1[^S|^K]', '1', TGT_OUT_FULL, 80, TGT_OUT_FULL, 80,TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
        } else {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^R', '1',  TGT_OUT_FULL, 122, TGT_OUT_FULL, 122,TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C', '1', TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80,TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
            CreateTargetFace($TourId, 3, '~Default', 'REG-^W1[^S|^K]', '1', TGT_OUT_FULL, 80, TGT_OUT_FULL, 80,TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
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
