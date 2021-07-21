<?php
/*
Common Setup for "Target" Archery
*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType, $SubRule);
if(($TourType==40) or (($TourType != 40)and($SubRule == 3))) {
    CreateSubClass($TourId, 1, 'OD', 'Out of Division');
}

// default Classes
CreateStandardClasses($TourId, $SubRule,$TourType);

// default Distances
switch($TourType) {
	case 1:
	case 4:
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, '_M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_JM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_JW', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_CM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_CW', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_MM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_MW', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				break;
			case '2':
				CreateDistanceNew($TourId, $TourType, '_M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				break;
			case '3':
                CreateDistanceNew($TourId, $TourType, '_M', array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
                CreateDistanceNew($TourId, $TourType, '_W', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
                CreateDistanceNew($TourId, $TourType, '_%1', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
                CreateDistanceNew($TourId, $TourType, '_%2', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
                CreateDistanceNew($TourId, $TourType, '_%3', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
                CreateDistanceNew($TourId, $TourType, '_%4', array(array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
                CreateDistanceNew($TourId, $TourType, '_%5', array(array('30m',30), array('20m',20), array('15m',15), array('10m',10)));
				break;
		}
		break;
    case 37:
        switch($SubRule) {
            case '1':
                CreateDistanceNew($TourId, $TourType, '_M', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
                CreateDistanceNew($TourId, $TourType, '_W', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
                CreateDistanceNew($TourId, $TourType, '_JM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
                CreateDistanceNew($TourId, $TourType, '_JW', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
                CreateDistanceNew($TourId, $TourType, '_CM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
                CreateDistanceNew($TourId, $TourType, '_CW', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
                CreateDistanceNew($TourId, $TourType, '_MM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
                CreateDistanceNew($TourId, $TourType, '_MW', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
                break;
            case '2':
                CreateDistanceNew($TourId, $TourType, '_M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
                CreateDistanceNew($TourId, $TourType, '_W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
                break;
            case '3':
                foreach (array('R' => 'R', 'L' => 'L', 'B' => 'B', ) as $kDiv => $vDiv){
                    if ($vDiv == 'B') {
                        CreateDistanceNew($TourId, $TourType, $vDiv . 'W', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                        CreateDistanceNew($TourId, $TourType, $vDiv . 'M', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                        CreateDistanceNew($TourId, $TourType, $vDiv . 'W2', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                        CreateDistanceNew($TourId, $TourType, $vDiv . 'M1', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));

                    }
                    else{
                        CreateDistanceNew($TourId, $TourType, $vDiv . 'W', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
                        CreateDistanceNew($TourId, $TourType, $vDiv . 'W2', array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
                        CreateDistanceNew($TourId, $TourType, $vDiv . 'M', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
                        CreateDistanceNew($TourId, $TourType, $vDiv . 'M1', array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));

                    }

                    CreateDistanceNew($TourId, $TourType, $vDiv . 'W3', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                    CreateDistanceNew($TourId, $TourType, $vDiv . 'W4', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                    CreateDistanceNew($TourId, $TourType, $vDiv . 'W5', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
                    CreateDistanceNew($TourId, $TourType, $vDiv . 'M2', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                    CreateDistanceNew($TourId, $TourType, $vDiv . 'M3', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                    CreateDistanceNew($TourId, $TourType, $vDiv . 'M4', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
                }
                CreateDistanceNew($TourId, $TourType, 'CM', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'CM1', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'CM2', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'CM3', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'CM4', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
                CreateDistanceNew($TourId, $TourType, 'CW', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'CW2', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'CW3', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'CW4', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'CW5', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
                break;

        }
        break;

	case 2:
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, '_M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_JM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_JW', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_CM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_CW', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_MM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_MW', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				break;
			case '2':
				CreateDistanceNew($TourId, $TourType, '_M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '_W', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, '_M', array(array('90m',90), array('70m',70), array('50m',50), array('30m',30), array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
				CreateDistanceNew($TourId, $TourType, '_W', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
				CreateDistanceNew($TourId, $TourType, '_%1', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
				CreateDistanceNew($TourId, $TourType, '_%2', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30), array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
				CreateDistanceNew($TourId, $TourType, '_%3', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20), array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
				CreateDistanceNew($TourId, $TourType, '_%4', array(array('40m',40), array('30m',30), array('20m',20), array('10m',10), array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
				CreateDistanceNew($TourId, $TourType, '_%5', array(array('30m',30), array('20m',20), array('15m',15), array('10m',10), array('30m',30), array('20m',20), array('15m',15), array('10m',10)));
				break;
		}
		break;
	case 18:
		CreateDistanceNew($TourId, $TourType, 'C%', array(array('50m-1',50), array('50m-2',50), array('-',0), array('-',0)));
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, 'RM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RW', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RCM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RCW', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RMM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RMW', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				break;
			case '2':
				CreateDistanceNew($TourId, $TourType, 'RM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RW', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, 'RM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RW', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RJM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'RJW', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				break;

		}
		break;
	case 3:
		$d70=array(array('70m-1',70), array('70m-2',70));
		$d60=array(array('60m-1',60), array('60m-2',60));
		$d50=array(array('50m-1',50), array('50m-2',50));
		$d40=array(array('40m-1',40), array('40m-2',40));
		$d30=array(array('30m-1',30), array('30m-2',30));
		switch($SubRule) {
			case '1':
                CreateDistanceNew($TourId, $TourType, 'R%', $d70);
                CreateDistanceNew($TourId, $TourType, 'C%', $d50);
                CreateDistanceNew($TourId,$TourType,  'L%', $d70);
                CreateDistanceNew($TourId,$TourType,  'B%', $d50);
				break;
			case '2':
			case '3':
            foreach (array('R' => 'R', 'L' => 'L', 'B' => 'B', ) as $kDiv => $vDiv){
				if ($vDiv == 'B') {
					CreateDistanceNew($TourId, $TourType, $vDiv . 'W',  $d50);
					CreateDistanceNew($TourId, $TourType, $vDiv . 'M',  $d50);
					CreateDistanceNew($TourId, $TourType, $vDiv . 'W2', $d50);
					CreateDistanceNew($TourId, $TourType, $vDiv . 'M1', $d50);
				} else {
					CreateDistanceNew($TourId, $TourType, $vDiv . 'W', $d70);
					CreateDistanceNew($TourId, $TourType, $vDiv . 'W2', $d60);
					CreateDistanceNew($TourId, $TourType, $vDiv . 'M', $d70);
					CreateDistanceNew($TourId, $TourType, $vDiv . 'M1', $d60);
				}

                CreateDistanceNew($TourId, $TourType, $vDiv . 'W3', $d50);
                CreateDistanceNew($TourId, $TourType, $vDiv . 'W4', $d40);
                CreateDistanceNew($TourId, $TourType, $vDiv . 'W5', $d30);
                CreateDistanceNew($TourId, $TourType, $vDiv . 'M2', $d50);
                CreateDistanceNew($TourId, $TourType, $vDiv . 'M3', $d40);
                CreateDistanceNew($TourId, $TourType, $vDiv . 'M4', $d30);
            }
            CreateDistanceNew($TourId, $TourType, 'CM', $d50);
            CreateDistanceNew($TourId, $TourType, 'CM1', $d50);
            CreateDistanceNew($TourId, $TourType, 'CM2', $d50);
            CreateDistanceNew($TourId, $TourType, 'CM3', $d40);
            CreateDistanceNew($TourId, $TourType, 'CM4', $d30);
            CreateDistanceNew($TourId, $TourType, 'CW', $d50);
            CreateDistanceNew($TourId, $TourType, 'CW2', $d50);
            CreateDistanceNew($TourId, $TourType, 'CW3', $d50);
            CreateDistanceNew($TourId, $TourType, 'CW4', $d40);
            CreateDistanceNew($TourId, $TourType, 'CW5', $d30);
            break;
		}
		break;
	case 5:
		CreateDistanceNew($TourId, $TourType, '%', array(array('60 m',60), array('50 m',50), array('40 m',40)));
		break;
	case 6:
		CreateDistanceNew($TourId, $TourType, '%', array(array('18m-1',18), array('18m-2',18)));
		break;
	case 7:
		CreateDistanceNew($TourId, $TourType, '%', array(array('25m-1',25), array('25m-2',25)));
		break;
	case 8:
		CreateDistanceNew($TourId, $TourType, '%', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
		break;
    case 40:
     switch ($SubRule){
         case 1:
         case 2:
			CreateDistanceNew($TourId, $TourType, '_M', array(array('100y',100), array('80y',80), array('60y',60)));
			CreateDistanceNew($TourId, $TourType, '_W', array(array('80y',80), array('60y',60), array('50y',50)));
			CreateDistanceNew($TourId, $TourType, '_%1', array(array('80y',80), array('60y',60), array('50y',50)));
			CreateDistanceNew($TourId, $TourType, '_%2', array(array('60y',60), array('50y',50), array('40y',40)));
			CreateDistanceNew($TourId, $TourType, '_%3', array(array('50y',50), array('40y',40), array('30y',30)));
			CreateDistanceNew($TourId, $TourType, '_%4', array(array('40y',40), array('30y',30), array('20y',20)));
			CreateDistanceNew($TourId, $TourType, '_%5', array(array('30y',30), array('20y',20), array('10y',10)));
            break;
         case 3:
             CreateDistanceNew($TourId, $TourType, '%', array(array('60y',60), array('50y',50), array('40y',40)));
             break;
         case 4:
         case 5:
         case 6:
             CreateDistanceNew($TourId, $TourType, '_M', array(array('100y',100), array('80y',80)));
             CreateDistanceNew($TourId, $TourType, '_W', array(array('80y',80), array('60y',60)));
             CreateDistanceNew($TourId, $TourType, '_%1', array(array('80y',80), array('60y',60)));
             CreateDistanceNew($TourId, $TourType, '_%2', array(array('60y',60), array('50y',50)));
             CreateDistanceNew($TourId, $TourType, '_%3', array(array('50y',50), array('40y',40)));
             CreateDistanceNew($TourId, $TourType, '_%4', array(array('40y',40), array('30y',30)));
             CreateDistanceNew($TourId, $TourType, '_%5', array(array('30y',30), array('20y',20)));
             break;
         case 7:
             CreateDistanceNew($TourId, $TourType, '_%', array(array('40y',40), array('30y',30)));
             break;
         case 8:
             CreateDistanceNew($TourId, $TourType, '_%', array(array('50y',50), array('50y',50), array('50y',50)));
             break;
         case 9:
             CreateDistanceNew($TourId, $TourType, '_M', array(array('50m',50), array('30m',30)));
             CreateDistanceNew($TourId, $TourType, '_W', array(array('50m',50), array('30m',30)));
             CreateDistanceNew($TourId, $TourType, '_%1', array(array('50m',50), array('30m',30)));
             CreateDistanceNew($TourId, $TourType, '_%2', array(array('40m',40), array('30m',30)));
             CreateDistanceNew($TourId, $TourType, '_%3', array(array('30m',30), array('20m',20)));
             CreateDistanceNew($TourId, $TourType, '_%4', array(array('20m',20), array('10m',10)));
             CreateDistanceNew($TourId, $TourType, '_%5', array(array('15m',15), array('10m',10)));
             break;
         case 10:
             CreateDistanceNew($TourId, $TourType, '_M', array(array('90m',90), array('70m',70)));
             CreateDistanceNew($TourId, $TourType, '_W', array(array('70m',70), array('60m',60)));
             CreateDistanceNew($TourId, $TourType, '_%1', array(array('70m',70), array('60m',60)));
             CreateDistanceNew($TourId, $TourType, '_%2', array(array('60m',60), array('50m',50)));
             CreateDistanceNew($TourId, $TourType, '_%3', array(array('50m',50), array('40m',40)));
             CreateDistanceNew($TourId, $TourType, '_%4', array(array('40m',40), array('30m',30)));
             CreateDistanceNew($TourId, $TourType, '_%5', array(array('30m',30), array('20m',20)));
             break;
         break;

         case 11:
         case 15:
             CreateDistanceNew($TourId, $TourType, '_%', array(array('20y',20), array('20y',20)));
             break;
         case 12:
             CreateDistanceNew($TourId, $TourType, '_%', array(array('20y',20)));
             break;
         case 13:
             CreateDistanceNew($TourId, $TourType, '_%', array(array('25y',25)));
             break;
         case 14:
             CreateDistanceNew($TourId, $TourType, '_%', array(array('30m',30), array('30m',30)));
             break;
     }
        break;

}

if($TourType<5 or $TourType==6 or 7 or 8 or 37 or $TourType==18 or $TourType==40) {
    // default Events
    CreateStandardEvents($TourId, $SubRule, $TourType != 6,$TourType);

    // Classes in Events
    InsertStandardEvents($TourId, $SubRule, $TourType != 6,$TourType);

    // Finals & TeamFinals
    CreateFinals($TourId);
}

// Default Target
switch($TourType) {
	case 1:
	case 4:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122, 9, 80, 9, 80);
		// optional target faces
		CreateTargetFace($TourId, 2, '~Option1', '%', '',  5, 122, 5, 122, 5, 80,  5, 80);
		CreateTargetFace($TourId, 3, '~Option2', '%', '',  5, 122, 5, 122, 5, 80, 9, 80);
		break;
    case 37:
        CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122, 5, 122, 5, 122);
        CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 9, 80, 9, 80, 9, 80, 9, 80);
        // optional target faces
        break;
	case 2:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
		// optional target faces
		CreateTargetFace($TourId, 2, '~Option1', '%', '',  5, 122, 5, 122, 5, 80,  5, 80,  5, 122, 5, 122, 5, 80,  5, 80);
		CreateTargetFace($TourId, 3, '~Option2', '%', '',  5, 122, 5, 122, 5, 80, 9, 80,  5, 122, 5, 122, 5, 80, 9, 80);
		break;
	case 18:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 122, 5, 122, 9, 80, 9, 80);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 9, 80, 9, 80);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Option1', '%', '',  5, 122, 5, 122, 9, 80,  9, 80);
		CreateTargetFace($TourId, 4, '~Option2', '%', '',  5, 122, 5, 122, 9, 80, 9, 80);
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
		CreateTargetFace($TourId, 3, '~Option1', '%', '',  1, 40, 1, 40);
		break;
	case 7:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 2, 60, 2, 60);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 4, 60, 4, 60);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Option1', '%', '',  1, 60, 1, 60);
		break;
	case 8:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 2, 60, 2, 60, 2, 40, 2, 40);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', 4, 60, 4, 60, 4, 40, 4, 40);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Option1', '%', '',  1, 60, 1, 60,  1, 40, 1, 40);
		break;
    case 40:
        if (($SubRule < 4) or ($SubRule== 8)){
        	$Tgt = ($SubRule < 4 ? 17 : 5);
            CreateTargetFace($TourId, 1, '~Default', '%', '1', $Tgt, 122, $Tgt, 122, $Tgt, 122);
        }
        elseif (($SubRule < 11) and ($SubRule != 9)) {
        	$Tgt = ($SubRule < 8 ? 17 : 5);
            CreateTargetFace($TourId, 1, '~Default', '%', '1', $Tgt, 122, $Tgt, 122);
        }
        elseif($SubRule == 9){
            CreateTargetFace($TourId, 1, '~Default', '%', '1', 5, 80, 5, 80);
        }
        elseif($SubRule == 11){
            CreateTargetFace($TourId, 1, '~Default', '%', '1', 13, 60, 13, 60);
        }
        elseif ($SubRule == 12 ){
            CreateTargetFace($TourId, 1, '~Default', '%', '1', 1, 40);
        }
        elseif  ($SubRule==13){
            CreateTargetFace($TourId, 1, '~Default', '%', '1', 1, 60);
        }
        elseif($SubRule == 14){
            CreateTargetFace($TourId, 1, '~Default', '%', '1', 1, 80, 1, 80);
        }
        elseif($SubRule == 15){
            CreateTargetFace($TourId, 1, '~Default', '%', '1', 1, 60, 1, 60);
        }
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 16);

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
