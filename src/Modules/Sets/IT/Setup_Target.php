<?php
/*
1 	Type_FITA

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (1)

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType);

// default SubClasses
CreateStandardSubClasses($TourId);

// default Classes
CreateStandardClasses($TourId, $SubRule, '', $TourType);

// default Distances
switch($TourType) {
	case 1:
	case 4:
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, 'V%', array(array('60cm face',30), array('80cm face',30), array('80cm face',30), array('122cm face',30)));
				CreateDistanceNew($TourId, $TourType, 'W1%',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLSM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLJM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLMM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLAM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLSF', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLJF', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLMF', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLAF', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLRM', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
				CreateDistanceNew($TourId, $TourType, 'OLRF', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
				CreateDistanceNew($TourId, $TourType, 'OLGM', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
				CreateDistanceNew($TourId, $TourType, 'OLGF', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
				CreateDistanceNew($TourId, $TourType, 'COSM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'COJM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'COMM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'COAM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'COSF', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'COJF', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'COMF', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'COAF', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'CORM', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
				CreateDistanceNew($TourId, $TourType, 'CORF', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
				break;
			case '2':
				CreateDistanceNew($TourId, $TourType, '%M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '%F', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, '__JM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '__AM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '__JF', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '__AF', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '__R_', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
				CreateDistanceNew($TourId, $TourType, '__G_', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
				break;
			case '4':
				CreateDistanceNew($TourId, $TourType, '%SM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '%MM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '%SF', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '%MF', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				break;
		}
		break;
	case 18:
		CreateDistanceNew($TourId, $TourType, 'CO%', array(array('50m-1',50), array('50m-2',50), array('-',0), array('-',0)));
		CreateDistanceNew($TourId, $TourType, 'W1%', array(array('50m-1',50), array('50m-2',50), array('-',0), array('-',0)));
		CreateDistanceNew($TourId, $TourType, 'V%', array(array('60cm face',30), array('80cm face',30), array('80cm face',30), array('122cm face',30)));
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, 'OLSM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLJM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLMM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLAM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLSF', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLJF', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLMF', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLAF', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLRM', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
				CreateDistanceNew($TourId, $TourType, 'OLRF', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
				CreateDistanceNew($TourId, $TourType, 'OLGM', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
				CreateDistanceNew($TourId, $TourType, 'OLGF', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
				break;
			case '2':
				CreateDistanceNew($TourId, $TourType, 'OL%M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OL%F', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, 'OLJM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLAM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLJF', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLAF', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLR_', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
				CreateDistanceNew($TourId, $TourType, 'OLG_', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
				break;
			case '4':
				CreateDistanceNew($TourId, $TourType, 'OLSM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLMM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLSF', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLMF', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				break;
		}
		break;
	case 2:
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, 'V%', array(array('60cm face',30), array('80cm face',30), array('80cm face',30), array('122cm face',30), array('60cm face',30), array('80cm face',30), array('80cm face',30), array('122cm face',30)));
				CreateDistanceNew($TourId, $TourType, 'W1%',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLSM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLJM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLMM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLAM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLSF', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLJF', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLMF', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLAF', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'OLRM', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20), array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
				CreateDistanceNew($TourId, $TourType, 'OLRF', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20), array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
				CreateDistanceNew($TourId, $TourType, 'OLGM', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15), array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
				CreateDistanceNew($TourId, $TourType, 'OLGF', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15), array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
				CreateDistanceNew($TourId, $TourType, 'COSM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'COJM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'COMM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'COAM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'COSF', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'COJF', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'COMF', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'COAF', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'CORM', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20), array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
				CreateDistanceNew($TourId, $TourType, 'CORF', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20), array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
				break;
			case '2':
				CreateDistanceNew($TourId, $TourType, '%M', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '%F', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, '__JM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '__AM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '__JF', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '__AF', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '__R_', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20), array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
				CreateDistanceNew($TourId, $TourType, '__G_', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15), array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
				break;
			case '4':
				CreateDistanceNew($TourId, $TourType, '%SM', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '%MM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '%SF', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, '%MF', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
				break;
		}
		break;
	case 3:
        switch($SubRule) {
            case '1':
                CreateDistanceNew($TourId, $TourType, 'OLS_', array(array('70m-1',70), array('70m-2',70)));
                CreateDistanceNew($TourId, $TourType, 'OLJ_', array(array('70m-1',70), array('70m-2',70)));
                CreateDistanceNew($TourId, $TourType, 'OLA_', array(array('60m-1',60), array('60m-2',60)));
                CreateDistanceNew($TourId, $TourType, 'OLM_', array(array('60m-1',60), array('60m-2',60)));
                CreateDistanceNew($TourId, $TourType, 'OLR_', array(array('40m-1',40), array('40m-2',40)));
                CreateDistanceNew($TourId, $TourType, 'OLG_', array(array('25m-1',25), array('25m-2',25)));
				CreateDistanceNew($TourId, $TourType, 'ANS_', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'ANM_', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'ANJ_', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'ANA_', array(array('40m-1',40), array('40m-2',40)));
				CreateDistanceNew($TourId, $TourType, 'ANR_', array(array('25m-1',25), array('25m-2',25)));
				CreateDistanceNew($TourId, $TourType, 'ANG_', array(array('25m-1',25), array('25m-2',25)));
                break;
            case '2':
                CreateDistanceNew($TourId, $TourType, 'OL%', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'AN%', array(array('50m-1',50), array('50m-2',50)));
                break;
            case '3':
                CreateDistanceNew($TourId, $TourType, 'OLJ_', array(array('70m-1',70), array('70m-2',70)));
                CreateDistanceNew($TourId, $TourType, 'OLA_', array(array('60m-1',60), array('60m-2',60)));
                CreateDistanceNew($TourId, $TourType, 'OLR_', array(array('40m-1',40), array('40m-2',40)));
                CreateDistanceNew($TourId, $TourType, 'OLG_', array(array('25m-1',25), array('25m-2',25)));
				CreateDistanceNew($TourId, $TourType, 'ANJ_', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'ANA_', array(array('40m-1',40), array('40m-2',40)));
				CreateDistanceNew($TourId, $TourType, 'ANR_', array(array('25m-1',25), array('25m-2',25)));
				CreateDistanceNew($TourId, $TourType, 'ANG_', array(array('25m-1',25), array('25m-2',25)));
                break;
            case '4':
                CreateDistanceNew($TourId, $TourType, 'OLS_', array(array('70m-1',70), array('70m-2',70)));
                CreateDistanceNew($TourId, $TourType, 'OLM_', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'AN%', array(array('50m-1',50), array('50m-2',50)));
                break;
        }
        CreateDistanceNew($TourId, $TourType, 'CO%', array(array('50m-1',50), array('50m-2',50)));
        CreateDistanceNew($TourId, $TourType, 'W1%', array(array('50m-1',50), array('50m-2',50)));
        CreateDistanceNew($TourId, $TourType, 'V%', array(array('30m-1',30), array('30m-2',30)));
        break;
    case 37:
        switch($SubRule) {
            case '1':
                CreateDistanceNew($TourId, $TourType, 'OLS_', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
                CreateDistanceNew($TourId, $TourType, 'OLM_', array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
                CreateDistanceNew($TourId, $TourType, 'OLJ_', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
                CreateDistanceNew($TourId, $TourType, 'OLA_', array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
                CreateDistanceNew($TourId, $TourType, 'OLR_', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'OLG_', array(array('25m-1',25), array('25m-2',25), array('25m-3',25), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'ANS_', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
				CreateDistanceNew($TourId, $TourType, 'ANM_', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
				CreateDistanceNew($TourId, $TourType, 'ANJ_', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
				CreateDistanceNew($TourId, $TourType, 'ANA_', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
				CreateDistanceNew($TourId, $TourType, 'ANR_', array(array('25m-1',25), array('25m-2',25), array('25m-3',25), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'ANG_', array(array('25m-1',25), array('25m-2',25), array('25m-3',25), array('25m-4',25)));
                break;
            case '2':
                CreateDistanceNew($TourId, $TourType, 'OL%', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
				CreateDistanceNew($TourId, $TourType, 'AN%', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                break;
            case '3':
                CreateDistanceNew($TourId, $TourType, 'OLJ_', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
                CreateDistanceNew($TourId, $TourType, 'OLA_', array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
                CreateDistanceNew($TourId, $TourType, 'OLR_', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'OLG_', array(array('25m-1',25), array('25m-2',25), array('25m-3',25), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'ANJ_', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
				CreateDistanceNew($TourId, $TourType, 'ANA_', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
				CreateDistanceNew($TourId, $TourType, 'ANR_', array(array('25m-1',25), array('25m-2',25), array('25m-3',25), array('25m-4',25)));
				CreateDistanceNew($TourId, $TourType, 'ANG_', array(array('25m-1',25), array('25m-2',25), array('25m-3',25), array('25m-4',25)));
                break;
            case '4':
                CreateDistanceNew($TourId, $TourType, 'OLS_', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
                CreateDistanceNew($TourId, $TourType, 'OLM_', array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
				CreateDistanceNew($TourId, $TourType, 'AN%', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                break;
        }
        CreateDistanceNew($TourId, $TourType, 'CO%', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
        CreateDistanceNew($TourId, $TourType, 'W1%', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
        CreateDistanceNew($TourId, $TourType, 'V%', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
        break;
    case 39:
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, 'OLS_', array(array('70m-1',70)));
				CreateDistanceNew($TourId, $TourType, 'OLJ_', array(array('70m-1',70)));
				CreateDistanceNew($TourId, $TourType, 'OLA_', array(array('60m-1',60)));
				CreateDistanceNew($TourId, $TourType, 'OLM_', array(array('60m-1',60)));
				CreateDistanceNew($TourId, $TourType, 'OLR_', array(array('40m-1',40)));
				CreateDistanceNew($TourId, $TourType, 'OLG_', array(array('25m-1',25)));
				CreateDistanceNew($TourId, $TourType, 'ANS_', array(array('50m-1',50)));
				CreateDistanceNew($TourId, $TourType, 'ANM_', array(array('50m-1',50)));
				CreateDistanceNew($TourId, $TourType, 'ANJ_', array(array('50m-1',50)));
				CreateDistanceNew($TourId, $TourType, 'ANA_', array(array('40m-1',40)));
				CreateDistanceNew($TourId, $TourType, 'ANR_', array(array('25m-1',25)));
				CreateDistanceNew($TourId, $TourType, 'ANG_', array(array('25m-1',25)));
				break;
			case '2':
				CreateDistanceNew($TourId, $TourType, 'OL%', array(array('70m-1',70)));
				CreateDistanceNew($TourId, $TourType, 'AN%', array(array('50m-1',50)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, 'OLJ_', array(array('70m-1',70)));
				CreateDistanceNew($TourId, $TourType, 'OLA_', array(array('60m-1',60)));
				CreateDistanceNew($TourId, $TourType, 'OLR_', array(array('40m-1',40)));
				CreateDistanceNew($TourId, $TourType, 'OLG_', array(array('25m-1',25)));
				CreateDistanceNew($TourId, $TourType, 'ANJ_', array(array('50m-1',50)));
				CreateDistanceNew($TourId, $TourType, 'ANA_', array(array('40m-1',40)));
				CreateDistanceNew($TourId, $TourType, 'ANR_', array(array('25m-1',25)));
				CreateDistanceNew($TourId, $TourType, 'ANG_', array(array('25m-1',25)));
				break;
			case '4':
				CreateDistanceNew($TourId, $TourType, 'OLS_', array(array('70m-1',70)));
				CreateDistanceNew($TourId, $TourType, 'OLM_', array(array('60m-1',60)));
				CreateDistanceNew($TourId, $TourType, 'AN%', array(array('50m-1',50)));
				break;
		}
        CreateDistanceNew($TourId, $TourType, 'CO%', array(array('50m-1',50)));
        CreateDistanceNew($TourId, $TourType, 'W1%', array(array('50m-1',50)));
        CreateDistanceNew($TourId, $TourType, 'V%', array(array('30m-1',30)));
		break;
	case 5:
		CreateDistanceNew($TourId, $TourType, 'OLG_', array(array('25 m',25), array('20 m',20), array('15 m',15)));
		CreateDistanceNew($TourId, $TourType, 'COG_', array(array('25 m',25), array('20 m',20), array('15 m',15)));
		CreateDistanceNew($TourId, $TourType, 'ANG_', array(array('25 m',25), array('20 m',20), array('15 m',15)));
		CreateDistanceNew($TourId, $TourType, 'OLR_', array(array('40 m',40), array('35 m',35), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'COR_', array(array('40 m',40), array('35 m',35), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'ANR_', array(array('40 m',40), array('35 m',35), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'OLA_', array(array('60 m',60), array('50 m',50), array('40 m',40)));
		CreateDistanceNew($TourId, $TourType, 'COA_', array(array('60 m',60), array('50 m',50), array('40 m',40)));
		CreateDistanceNew($TourId, $TourType, 'ANA_', array(array('60 m',60), array('50 m',50), array('40 m',40)));
		CreateDistanceNew($TourId, $TourType, 'OLJ_', array(array('60 m',60), array('50 m',50), array('40 m',40)));
		CreateDistanceNew($TourId, $TourType, 'COJ_', array(array('60 m',60), array('50 m',50), array('40 m',40)));
		CreateDistanceNew($TourId, $TourType, 'ANJ_', array(array('60 m',60), array('50 m',50), array('40 m',40)));
		CreateDistanceNew($TourId, $TourType, 'OLM_', array(array('60 m',60), array('50 m',50), array('40 m',40)));
		CreateDistanceNew($TourId, $TourType, 'COM_', array(array('60 m',60), array('50 m',50), array('40 m',40)));
		CreateDistanceNew($TourId, $TourType, 'ANM_', array(array('60 m',60), array('50 m',50), array('40 m',40)));
		CreateDistanceNew($TourId, $TourType, 'OLS_', array(array('60 m',60), array('50 m',50), array('40 m',40)));
		CreateDistanceNew($TourId, $TourType, 'COS_', array(array('60 m',60), array('50 m',50), array('40 m',40)));
		CreateDistanceNew($TourId, $TourType, 'ANS_', array(array('60 m',60), array('50 m',50), array('40 m',40)));
		CreateDistanceNew($TourId, $TourType, 'V%', array(array('30m-1',30), array('30m-2',30), array('30m-3',30)));
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
}

if($TourType<5 or $TourType==6 or $TourType==18 or $TourType==37 or $TourType==39) {
	// default Events
	CreateStandardEvents($TourId, $TourType, $SubRule, $TourType!=6);

	// Classes in Events
	InsertStandardEvents($TourId, $TourType, $SubRule, $TourType!=6);

	// Finals & TeamFinals
	CreateFinals($TourId);
}

//Specific Lookup for sperimental indoor class
if($TourType >= 6 AND $TourType <= 8) {
    $tourDetIocCode = 'ITA_i';
}

// Default Target
$i=1;
switch($TourType) {
	case 1:
	case 4:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
		CreateTargetFace($TourId, $i++, '~DefaultVI', 'V%', '1', 5, 60, 5, 80, 5, 80, 5, 122);
		// optional target faces
		CreateTargetFace($TourId, $i++, '~30: 6-X', 'REG-^OL|^CO|^W1', '',  5, 122, 5, 122, 5, 80, 10, 80);
		CreateTargetFace($TourId, $i++, '~50: 5-X/30: 5-X', 'REG-^OL|^CO|^W1', '',  5, 122, 5, 122, 9, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '~50: 5-X/30: 6-X', 'REG-^OL|^CO|^W1', '',  5, 122, 5, 122, 9, 80, 10, 80);
		break;
	case 18:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
		CreateTargetFace($TourId, $i++, '~DefaultCO', 'CO%', '1', 9, 80, 9, 80, 0, 0, 0, 0);
		CreateTargetFace($TourId, $i++, '~Default W1', 'W1%', '1', 5, 80, 5, 80, 0, 0, 0, 0);
		CreateTargetFace($TourId, $i++, '~DefaultVI', 'V%', '1', 5, 60, 5, 80, 5, 80, 5, 122);
		// optional target faces
		CreateTargetFace($TourId, $i++, '~30: 6-X', 'OL%', '',  5, 122, 5, 122, 5, 80, 10, 80);
		CreateTargetFace($TourId, $i++, '~50: 5-X/30: 5-X', 'OL%', '',  5, 122, 5, 122, 9, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '~50: 5-X/30: 6-X', 'OL%', '',  5, 122, 5, 122, 9, 80, 10, 80);
		break;
	case 2:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
		CreateTargetFace($TourId, $i++, '~DefaultVI', 'V%', '1', 5, 60, 5, 80, 5, 80, 5, 122, 5, 60, 5, 80, 5, 80, 5, 122);
		// optional target faces
		CreateTargetFace($TourId, $i++, '~30: 6-X', 'REG-^OL|^CO|^W1', '',  5, 122, 5, 122, 5, 80, 10, 80,  5, 122, 5, 122, 5, 80, 10, 80);
		CreateTargetFace($TourId, $i++, '~50: 5-X/30: 5-X', 'REG-^OL|^CO|^W1', '',  5, 122, 5, 122, 9, 80, 9, 80,  5, 122, 5, 122, 9, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '~50: 5-X/30: 6-X', 'REG-^OL|^CO|^W1', '',  5, 122, 5, 122, 9, 80, 10, 80,  5, 122, 5, 122, 9, 80, 10, 80);
		break;
	case 3:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
		CreateTargetFace($TourId, $i++, '~DefaultCO', 'REG-^CO', '1',  TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
		CreateTargetFace($TourId, $i++, '~Default W1', 'REG-^W1', '1',  TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
		CreateTargetFace($TourId, $i++, '~DefaultVI', 'REG-^V', '1',  TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
		break;
    case 37:
        CreateTargetFace($TourId, $i++, '~Default', '%', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
        CreateTargetFace($TourId, $i++, '~DefaultCO', 'REG-^CO', '1',  TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80,  TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
        CreateTargetFace($TourId, $i++, '~Default W1', 'REG-^W1', '1',  TGT_OUT_FULL, 80, TGT_OUT_FULL, 80, TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
        CreateTargetFace($TourId, $i++, '~DefaultVI', 'REG-^V', '1',  TGT_OUT_FULL, 80, TGT_OUT_FULL, 80,  TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
        break;
    case 39:
        CreateTargetFace($TourId, $i++, '~Default', '%', '1', TGT_OUT_FULL, 122);
        CreateTargetFace($TourId, $i++, '~DefaultCO', 'REG-^CO', '1',  TGT_OUT_5_big10, 80);
        CreateTargetFace($TourId, $i++, '~Default W1', 'REG-^W1', '1',  TGT_OUT_FULL, 80);
        CreateTargetFace($TourId, $i++, '~DefaultVI', 'REG-^V', '1',  TGT_OUT_FULL, 80);
        break;
	case 5:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1',  5, 122, 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '~DefaultVI', 'V%', '1',  5, 80, 5, 80, 5, 80);
		break;
	case 6:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~DefaultCO', 'REG-^CO[^G]', '1', 4, 40, 4, 40);
		CreateTargetFace($TourId, $i++, '~Default G/VI', 'REG-^OLG|^ANG|^V', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~Default W1', 'REG-^W1', '1', 1, 40, 1, 40);
		// optional target faces
		CreateTargetFace($TourId, $i++, '~6-10', 'REG-^OL[AJMRS]', '',  2, 40, 2, 40);
		break;
	case 7:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~DefaultCO', 'REG-^CO[^G]', '1', 4, 60, 4, 60);
		CreateTargetFace($TourId, $i++, '~Default G/VI', 'REG-^OLG|^ANG|^V', '1', 1, 80, 1, 80);
		CreateTargetFace($TourId, $i++, '~Default W1', 'REG-^W1', '1', 1, 60, 1, 60);
		// optional target faces
		CreateTargetFace($TourId, $i++, '~6-10', 'REG-^OL[AJMRS]', '',  2, 60, 2, 60);
		break;
	case 8:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1', 1, 60, 1, 60, 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~DefaultCO', 'REG-^CO[^G]', '1', 4, 60, 4, 60, 4, 40, 4, 40);
		CreateTargetFace($TourId, $i++, '~Default G/VI', 'REG-^OLG|^V', '1', 1, 80, 1, 80, 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~Default W1', 'REG-^W1', '1', 1, 60, 1, 60, 1, 40, 1, 40);
		// optional target faces
		CreateTargetFace($TourId, $i++, '~6-10', 'REG-^OL[AJMRS]', '',  2, 60, 2, 60,  2, 40, 2, 40);
		break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 16, 4);

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
	'ToIocCode'	=> $tourDetIocCode,
	);
UpdateTourDetails($TourId, $tourDetails);

?>
