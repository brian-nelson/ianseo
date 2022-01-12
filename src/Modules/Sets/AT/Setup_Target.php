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
        CreateDistanceNew($TourId, $TourType, 'RU13%',  array(array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'RU15%',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'RU18W',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RU18M',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RU21W',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RU21M',  array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RW',   array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RM',   array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'R50W',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'R50M',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'R50',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'R65%',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));

        CreateDistanceNew($TourId, $TourType, 'CU13%',  array(array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'CU15%',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'CU18W',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CU18M',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CU21W',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CU21M',  array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CW',   array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CM',   array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'C50W',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'C50M',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'C50',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'C65%',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));

        CreateDistanceNew($TourId, $TourType, 'BU13%',  array(array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'BU15%',  array(array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'BU18%',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'BU21%',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'B50%',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'B65%',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'B_',   array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));

        CreateDistanceNew($TourId, $TourType, 'ROU13%',  array(array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'ROU15%',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'ROU18W',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ROU18M',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ROU21W',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ROU21M',  array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ROW',   array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ROM',   array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RO50W',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RO50M',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RO50',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RO65%',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));

        CreateDistanceNew($TourId, $TourType, 'COU13%',  array(array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'COU15%',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'COU18W',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'COU18M',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'COU21W',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'COU21M',  array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'COW',   array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'COM',   array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CO50W',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CO50M',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CO50',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CO65%',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));

        CreateDistanceNew($TourId, $TourType, 'W1U13%', array(array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'W1U15%', array(array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'W1U18%', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'W1U21%', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'W150%', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'W165%', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'W1_',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));

        CreateDistanceNew($TourId, $TourType, 'T%',   array(array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'L%',   array(array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        break;
    case 2:
        CreateDistanceNew($TourId, $TourType, 'RU13%', array(array('40m',40), array('30m',30), array('20m',20), array('10m',10), array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'RU15%', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20), array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'RU18W', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30), array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RU18M', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RU21W', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RU21M', array(array('90m',90), array('70m',70), array('50m',50), array('30m',30), array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RW',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RM',  array(array('90m',90), array('70m',70), array('50m',50), array('30m',30), array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'R50W', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30), array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'R50M', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'R50', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'R65%', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30), array('60m',60), array('50m',50), array('40m',40), array('30m',30)));

        CreateDistanceNew($TourId, $TourType, 'CU13%',  array(array('40m',40), array('30m',30), array('20m',20), array('10m',10), array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'CU15%',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20), array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'CU18W',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30), array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CU18M',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CU21W',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CU21M',  array(array('90m',90), array('70m',70), array('50m',50), array('30m',30), array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CW',   array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CM',   array(array('90m',90), array('70m',70), array('50m',50), array('30m',30), array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'C50W',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30), array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'C50M',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'C50',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'C65%',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30), array('60m',60), array('50m',50), array('40m',40), array('30m',30)));

        CreateDistanceNew($TourId, $TourType, 'BU13%',  array(array('40m',40), array('30m',30), array('20m',20), array('10m',10), array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'BU15%',  array(array('40m',40), array('30m',30), array('20m',20), array('10m',10), array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'BU18%',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20), array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'BU21%',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20), array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'B50%',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20), array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'B65%',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20), array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'B_',   array(array('50m',50), array('40m',40), array('30m',30), array('20m',20), array('50m',50), array('40m',40), array('30m',30), array('20m',20)));

        CreateDistanceNew($TourId, $TourType, 'ROU13%', array(array('40m',40), array('30m',30), array('20m',20), array('10m',10), array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'ROU15%', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20), array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'ROU18W', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30), array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ROU18M', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ROU21W', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ROU21M', array(array('90m',90), array('70m',70), array('50m',50), array('30m',30), array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ROW',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ROM',  array(array('90m',90), array('70m',70), array('50m',50), array('30m',30), array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RO50W', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30), array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RO50M', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RO50', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RO65%', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30), array('60m',60), array('50m',50), array('40m',40), array('30m',30)));

        CreateDistanceNew($TourId, $TourType, 'COU13%',  array(array('40m',40), array('30m',30), array('20m',20), array('10m',10), array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'COU15%',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20), array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'COU18W',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30), array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'COU18M',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'COU21W',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'COU21M',  array(array('90m',90), array('70m',70), array('50m',50), array('30m',30), array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'COW',   array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'COM',   array(array('90m',90), array('70m',70), array('50m',50), array('30m',30), array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CO50W',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30), array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CO50M',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CO50',  array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CO65%',  array(array('60m',60), array('50m',50), array('40m',40), array('30m',30), array('60m',60), array('50m',50), array('40m',40), array('30m',30)));

        CreateDistanceNew($TourId, $TourType, 'W1U13%', array(array('40m',40), array('30m',30), array('20m',20), array('10m',10), array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'W1U15%', array(array('40m',40), array('30m',30), array('20m',20), array('10m',10), array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'W1U18%', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20), array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'W1U21%', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20), array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'W150%', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20), array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'W165%', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20), array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'W1_',  array(array('50m',50), array('40m',40), array('30m',30), array('20m',20), array('50m',50), array('40m',40), array('30m',30), array('20m',20)));

        CreateDistanceNew($TourId, $TourType, 'T%',   array(array('40m',40), array('30m',30), array('20m',20), array('10m',10), array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'L%',   array(array('40m',40), array('30m',30), array('20m',20), array('10m',10), array('40m',40), array('30m',30), array('20m',20), array('10m',10)));
        break;
	case 3:
		switch($SubRule) {
			case '1':
                CreateDistanceNew($TourId, $TourType, 'RU13%',  array(array('30m-1', 30), array('30m-2', 30)));
                CreateDistanceNew($TourId, $TourType, 'RU15%',  array(array('40m-1', 40), array('40m-2', 40)));
                CreateDistanceNew($TourId, $TourType, 'RU18%',  array(array('60m-1', 60), array('60m-2', 60)));
                CreateDistanceNew($TourId, $TourType, 'RU21%',  array(array('70m-1', 70), array('70m-2', 70)));
                CreateDistanceNew($TourId, $TourType, 'R50%',  array(array('60m-1', 60), array('60m-2', 60)));
                CreateDistanceNew($TourId, $TourType, 'R65%',  array(array('60m-1', 60), array('60m-2', 60)));
                CreateDistanceNew($TourId, $TourType, 'R_',   array(array('70m-1', 70), array('70m-2', 70)));

                CreateDistanceNew($TourId, $TourType, 'ROU13%',  array(array('30m-1', 30), array('30m-2', 30)));
                CreateDistanceNew($TourId, $TourType, 'ROU15%',  array(array('40m-1', 40), array('40m-2', 40)));
                CreateDistanceNew($TourId, $TourType, 'ROU18%',  array(array('60m-1', 60), array('60m-2', 60)));
                CreateDistanceNew($TourId, $TourType, 'ROU21%',  array(array('70m-1', 70), array('70m-2', 70)));
                CreateDistanceNew($TourId, $TourType, 'RO50%',  array(array('60m-1', 60), array('60m-2', 60)));
                CreateDistanceNew($TourId, $TourType, 'RO65%',  array(array('60m-1', 60), array('60m-2', 60)));
                CreateDistanceNew($TourId, $TourType, 'RO_',   array(array('70m-1', 70), array('70m-2', 70)));

                CreateDistanceNew($TourId, $TourType, 'CU13%',  array(array('30m-1', 30), array('30m-2', 30)));
                CreateDistanceNew($TourId, $TourType, 'CU15%',  array(array('40m-1', 40), array('40m-2', 40)));
                CreateDistanceNew($TourId, $TourType, 'CU18%',  array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'CU21%',  array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'C50%',  array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'C65%',  array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'C_',  array(array('50m-1', 50), array('50m-2', 50)));

                CreateDistanceNew($TourId, $TourType, 'COU13%',  array(array('30m-1', 30), array('30m-2', 30)));
                CreateDistanceNew($TourId, $TourType, 'COU15%',  array(array('40m-1', 40), array('40m-2', 40)));
                CreateDistanceNew($TourId, $TourType, 'COU18%',  array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'COU21%',  array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'CO50%',  array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'CO65%',  array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'CO_',  array(array('50m-1', 50), array('50m-2', 50)));

                CreateDistanceNew($TourId, $TourType, 'W1U13%', array(array('30m-1', 30), array('30m-2', 30)));
                CreateDistanceNew($TourId, $TourType, 'W1U15%', array(array('40m-1', 40), array('40m-2', 40)));
                CreateDistanceNew($TourId, $TourType, 'W1U18%', array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'W1U21%', array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'W150%', array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'W165%', array(array('50m-1', 50), array('50m-2', 50)));
                CreateDistanceNew($TourId, $TourType, 'W1_', array(array('50m-1', 50), array('50m-2', 50)));

                CreateDistanceNew($TourId, $TourType, 'BU13%',  array(array('30m-1', 30), array('30m-2', 30)));
                CreateDistanceNew($TourId, $TourType, 'BU15%',  array(array('30m-1', 30), array('30m-2', 30)));
                CreateDistanceNew($TourId, $TourType, 'BU18%',  array(array('40m-1', 40), array('40m-2', 40)));
                CreateDistanceNew($TourId, $TourType, 'BU21%',  array(array('40m-1', 40), array('40m-2', 40)));
                CreateDistanceNew($TourId, $TourType, 'B50%',  array(array('40m-1', 40), array('40m-2', 40)));
                CreateDistanceNew($TourId, $TourType, 'B65%',  array(array('40m-1', 40), array('40m-2', 40)));
                CreateDistanceNew($TourId, $TourType, 'B_',  array(array('40m-1', 40), array('40m-2', 40)));

                CreateDistanceNew($TourId, $TourType, 'TU13%',   array(array('30m-1', 30), array('30m-2', 30)));
                CreateDistanceNew($TourId, $TourType, 'TU15%',   array(array('30m-1', 30), array('30m-2', 30)));
                CreateDistanceNew($TourId, $TourType, 'TU18%',   array(array('30m-1', 30), array('30m-2', 30)));
                CreateDistanceNew($TourId, $TourType, 'TU21%',   array(array('40m-1', 40), array('40m-2', 40)));
                CreateDistanceNew($TourId, $TourType, 'T50%',   array(array('40m-1', 40), array('40m-2', 40)));
                CreateDistanceNew($TourId, $TourType, 'T65%',   array(array('40m-1', 40), array('40m-2', 40)));
                CreateDistanceNew($TourId, $TourType, 'T_',   array(array('40m-1', 40), array('40m-2', 40)));

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
        CreateDistanceNew($TourId, $TourType, '%U13%', array(array('30m',30), array('25m',25),array('20m',20)));

        CreateDistanceNew($TourId, $TourType, 'BU15%', array(array('30m',30), array('25m',25),array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'BU18%', array(array('40m',40), array('35m',35),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'BU21%', array(array('40m',40), array('35m',35),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'B50%', array(array('40m',40), array('35m',35),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'B65%', array(array('40m',40), array('35m',35),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'B_', array(array('40m',40), array('35m',35),array('30m',30)));

        CreateDistanceNew($TourId, $TourType, 'RU15%', array(array('40m',40), array('35m',35),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RU18%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'RU21%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'R50%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'R65%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'R_', array(array('60m',60), array('50m',50),array('40m',40)));

        CreateDistanceNew($TourId, $TourType, 'ROU15%', array(array('40m',40), array('35m',35),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ROU18%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'ROU21%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'RO50%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'RO65%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'RO_', array(array('60m',60), array('50m',50),array('40m',40)));

        CreateDistanceNew($TourId, $TourType, 'CU15%', array(array('40m',40), array('35m',35),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CU18%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'CU21%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'C50%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'C65%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'C_', array(array('60m',60), array('50m',50),array('40m',40)));

        CreateDistanceNew($TourId, $TourType, 'COU15%', array(array('40m',40), array('35m',35),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'COU18%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'COU21%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'CO50%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'CO65%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'CO_', array(array('60m',60), array('50m',50),array('40m',40)));

        CreateDistanceNew($TourId, $TourType, 'W1U15%', array(array('40m',40), array('35m',35),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'W1U18%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'W1U21%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'W150%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'W165%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'W1_', array(array('60m',60), array('50m',50),array('40m',40)));

        CreateDistanceNew($TourId, $TourType, 'T%', array(array('30m',30), array('25m',25),array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'L%', array(array('30m',30), array('25m',25),array('20m',20)));
        break;
	case 6:
		CreateDistanceNew($TourId, $TourType, 'R%',   array(array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'C%',   array(array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'W1_',   array(array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'B%',   array(array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'T%',   array(array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'L%',   array(array('18m-1',18), array('18m-2',18)));
        if($SubRule==1) {
	        CreateDistanceNew($TourId, $TourType, 'W1U13%', array(array('10m-1',10), array('10m-2',10)));
	        CreateDistanceNew($TourId, $TourType, 'W1U15%', array(array('10m-1',10), array('10m-2',10)));
	        CreateDistanceNew($TourId, $TourType, 'W1U18%', array(array('18m-1',18), array('18m-2',18)));
	        CreateDistanceNew($TourId, $TourType, 'W1U21%', array(array('18m-1',18), array('18m-2',18)));
	        CreateDistanceNew($TourId, $TourType, 'W165%', array(array('18m-1',18), array('18m-2',18)));
	        CreateDistanceNew($TourId, $TourType, 'W150%', array(array('18m-1',18), array('18m-2',18)));
        }
		break;
	case 7:
        CreateDistanceNew($TourId, $TourType, 'R%',   array(array('25m-1',25), array('25m-2',25)));
        CreateDistanceNew($TourId, $TourType, 'C%',   array(array('25m-1',25), array('25m-2',25)));
        CreateDistanceNew($TourId, $TourType, 'W1_',   array(array('25m-1',25), array('25m-2',25)));
        CreateDistanceNew($TourId, $TourType, 'B%',   array(array('25m-1',25), array('25m-2',25)));
        CreateDistanceNew($TourId, $TourType, 'T%',   array(array('25m-1',25), array('25m-2',25)));
        CreateDistanceNew($TourId, $TourType, 'L%',   array(array('25m-1',25), array('25m-2',25)));
        if($SubRule==1) {
            CreateDistanceNew($TourId, $TourType, 'W1U13%', array(array('20m-1',20), array('20m-2',20)));
            CreateDistanceNew($TourId, $TourType, 'W1U15%', array(array('20m-1',20), array('20m-2',20)));
            CreateDistanceNew($TourId, $TourType, 'W1U18%', array(array('25m-1',25), array('25m-2',25)));
            CreateDistanceNew($TourId, $TourType, 'W1U21%', array(array('25m-1',25), array('25m-2',25)));
            CreateDistanceNew($TourId, $TourType, 'W165%', array(array('25m-1',25), array('25m-2',25)));
            CreateDistanceNew($TourId, $TourType, 'W150%', array(array('25m-1',25), array('25m-2',25)));
        }
		break;
	case 8:
        CreateDistanceNew($TourId, $TourType, 'R%',   array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'C%',   array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'W1_',   array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'B%',   array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'T%',   array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'L%',   array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        if($SubRule==1) {
            CreateDistanceNew($TourId, $TourType, 'W1U13%', array(array('20m-1',20), array('20m-2',20), array('10m-1',10), array('10m-2',10)));
            CreateDistanceNew($TourId, $TourType, 'W1U15%', array(array('20m-1',20), array('20m-2',20), array('10m-1',10), array('10m-2',10)));
            CreateDistanceNew($TourId, $TourType, 'W1U18%', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'W1U21%', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'W165%', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'W150%', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        }
		break;
    case 37:
        switch($SubRule) {
            case '1':
                CreateDistanceNew($TourId, $TourType, 'RU13%',  array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
                CreateDistanceNew($TourId, $TourType, 'RU15%',  array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'RU18%',  array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
                CreateDistanceNew($TourId, $TourType, 'RU21%',  array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
                CreateDistanceNew($TourId, $TourType, 'R50%',  array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
                CreateDistanceNew($TourId, $TourType, 'R65%',  array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
                CreateDistanceNew($TourId, $TourType, 'R_',   array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));

                CreateDistanceNew($TourId, $TourType, 'ROU13%',  array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
                CreateDistanceNew($TourId, $TourType, 'ROU15%',  array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'ROU18%',  array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
                CreateDistanceNew($TourId, $TourType, 'ROU21%',  array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
                CreateDistanceNew($TourId, $TourType, 'RO50%',  array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
                CreateDistanceNew($TourId, $TourType, 'RO65%',  array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
                CreateDistanceNew($TourId, $TourType, 'RO_',   array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));

                CreateDistanceNew($TourId, $TourType, 'CU13%',  array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
                CreateDistanceNew($TourId, $TourType, 'CU15%',  array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'CU18%',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'CU21%',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'C50%',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'C65%',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'C_',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));

                CreateDistanceNew($TourId, $TourType, 'COU13%',  array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
                CreateDistanceNew($TourId, $TourType, 'COU15%',  array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'COU18%',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'COU21%',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'CO50%',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'CO65%',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'CO_',  array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));

                CreateDistanceNew($TourId, $TourType, 'W1U13%', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
                CreateDistanceNew($TourId, $TourType, 'W1U15%', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'W1U18%', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'W1U21%', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'W150%', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'W165%', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
                CreateDistanceNew($TourId, $TourType, 'W1_', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));

                CreateDistanceNew($TourId, $TourType, 'BU13%',  array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
                CreateDistanceNew($TourId, $TourType, 'BU15%',  array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
                CreateDistanceNew($TourId, $TourType, 'BU18%',  array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'BU21%',  array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'B50%',  array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'B65%',  array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
                CreateDistanceNew($TourId, $TourType, 'B%',  array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));

                CreateDistanceNew($TourId, $TourType, 'T%',   array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
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
    case 2:
        CreateTargetFace($TourId, 1, '~Default', '%', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80, TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
        CreateTargetFace($TourId, 2, '~50: 5-X/30: 5-X', 'REG-^R|^C', '',TGT_OUT_FULL, 122, TGT_OUT_FULL, 122,TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80,TGT_OUT_FULL, 122, TGT_OUT_FULL, 122,TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
        break;
	case 3:
	    if ($SubRule==1) {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^R|^B|^T|^L|^CU13|^CU15|^COU13|^COU15|^W1U13|^W1U15', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^CU18|^CU21|^C50|^C65|^C[M|W]$', '1', TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
            CreateTargetFace($TourId, 3, '~DefaultCO', 'REG-^COU18|^COU21|^CO50|^CO65|^CO[M|W]$', '1', TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
            CreateTargetFace($TourId, 4, '~Default', 'REG-^W1U18|^W1U21|^W150|^W165|^W1[M|W]$', '1', TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
        } else {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^R|^B|^T|^L|', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C', '1', TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
            CreateTargetFace($TourId, 3, '~Default', 'REG-^W1', '1', TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
        }
		break;
    case 5:
        CreateTargetFace($TourId, 1, '~Default', '%', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
        break;
	case 6:
        if($SubRule==1) {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^RU18|^RU21|^R50|^R65|^R[M|W]$|^ROU18|^ROU21|^RO50|^RO65|^RO[M|W]$', '1', TGT_IND_6_big10, 40, TGT_IND_6_big10, 40);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^CU18|^CU21|^C50|^C65|^C[M|W]$|^COU18|^COU21|^CO50|^CO65|^CO[M|W]$|^W1U21|^W150|^W165|^W1[M|W]$', '1', TGT_IND_6_small10, 40, TGT_IND_6_small10, 40);
            CreateTargetFace($TourId, 3, '~Default', 'REG-^RU13|^RU15|^CU13|^CU15|^ROU13|^ROU15|^COU13|^COU15', '1', TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
            CreateTargetFace($TourId, 4, '~Default', 'REG-^B', '1', TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
            CreateTargetFace($TourId, 5, '~Default', 'REG-^TU13|^TU15|^TU18|^L', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);
            CreateTargetFace($TourId, 6, '~Default', 'REG-^TU21|^T50|^T65|^T[M|W]$', '1', TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
            CreateTargetFace($TourId, 7, '~Default', 'REG-^W1U13|^W1U15', '1', TGT_IND_1_big10, 80, TGT_IND_1_big10, 80);
            CreateTargetFace($TourId, 8, '~Default', 'REG-^W1U18', '1', TGT_IND_1_small10, 60, TGT_IND_1_small10, 60);
        } else {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^R', '1', TGT_IND_6_big10, 40, TGT_IND_6_big10, 40);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C|^W1', '1', TGT_IND_6_small10, 40, TGT_IND_6_small10, 40);
            CreateTargetFace($TourId, 3, '~Default', 'REG-^B|^T', '1', TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
            CreateTargetFace($TourId, 4, '~Default', 'REG-^L', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);
        }
		break;
    case 7:
        if($SubRule==1) {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^RU18|^RU21|^R50|^R65|^R[M|W]$|^ROU18|^ROU21|^RO50|^RO65|^RO[M|W]$', '1', TGT_IND_6_big10, 60, TGT_IND_6_big10, 60);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^CU18|^CU21|^C50|^C65|^C[M|W]$|^COU18|^COU21|^CO50|^CO65|^CO[M|W]$|^W1U21|^W150|^W165|^W1[M|W]$', '1', TGT_IND_6_small10, 60, TGT_IND_6_small10, 60);
            CreateTargetFace($TourId, 3, '~Default', 'REG-^RU13|^RU15|^CU13|^CU15|^ROU13|^ROU15|^COU13|^COU15', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);
            CreateTargetFace($TourId, 4, '~Default', 'REG-^B', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);
            CreateTargetFace($TourId, 5, '~Default', 'REG-^T|^L', '1', TGT_IND_1_big10, 80, TGT_IND_1_big10, 80);
            CreateTargetFace($TourId, 6, '~Default', 'REG-^W1U13|^W1U15', '1', TGT_IND_1_big10, 122, TGT_IND_1_big10, 122);
            CreateTargetFace($TourId, 7, '~Default', 'REG-^W1U18', '1', TGT_IND_1_small10, 80, TGT_IND_1_small10, 80);
        } else {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^R', '1', TGT_IND_6_big10, 60, TGT_IND_6_big10, 60);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C|^W1', '1', TGT_IND_6_small10, 60, TGT_IND_6_small10, 60);
            CreateTargetFace($TourId, 4, '~Default', 'REG-^B', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);
            CreateTargetFace($TourId, 5, '~Default', 'REG-^T|^L', '1', TGT_IND_1_big10, 80, TGT_IND_1_big10, 80);
        }
        break;
	case 8:
        if($SubRule==1) {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^RU18|^RU21|^R50|^R65|^R[M|W]$|^ROU18|^ROU21|^RO50|^RO65|^RO[M|W]$', '1', TGT_IND_6_big10, 60, TGT_IND_6_big10, 60, TGT_IND_6_big10, 40, TGT_IND_6_big10, 40);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^CU18|^CU21|^C50|^C65|^C[M|W]$|^COU18|^COU21|^CO50|^CO65|^CO[M|W]$|^W1U21|^W150|^W165|^W1[M|W]$', '1', TGT_IND_6_small10, 60, TGT_IND_6_small10, 60, TGT_IND_6_small10, 40, TGT_IND_6_small10, 40);
            CreateTargetFace($TourId, 3, '~Default', 'REG-^RU13|^RU15|^CU13|^CU15|^ROU13|^ROU15|^COU13|^COU15', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60, TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
            CreateTargetFace($TourId, 4, '~Default', 'REG-^B', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60, TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
            CreateTargetFace($TourId, 5, '~Default', 'REG-^T|^L', '1', TGT_IND_1_big10, 80, TGT_IND_1_big10, 80, TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);
            CreateTargetFace($TourId, 6, '~Default', 'REG-^W1U13|^W1U15', '1', TGT_IND_1_big10, 122, TGT_IND_1_big10, 122, TGT_IND_1_big10, 80, TGT_IND_1_big10, 80);
            CreateTargetFace($TourId, 7, '~Default', 'REG-^W1U18', '1', TGT_IND_1_small10, 80, TGT_IND_1_small10, 80, TGT_IND_1_small10, 60, TGT_IND_1_small10, 60);
        } else {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^R', '1', TGT_IND_6_big10, 60, TGT_IND_6_big10, 60, TGT_IND_6_big10, 40, TGT_IND_6_big10, 40);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C|^W1', '1', TGT_IND_6_small10, 60, TGT_IND_6_small10, 60, TGT_IND_6_small10, 40, TGT_IND_6_small10, 40);
            CreateTargetFace($TourId, 3, '~Default', 'REG-^B', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60, TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
            CreateTargetFace($TourId, 4, '~Default', 'REG-^T|^L', '1', TGT_IND_1_big10, 80, TGT_IND_1_big10, 80, TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);

        }
		break;
    case 37:
        if ($SubRule==1) {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^R|^B|^T|^L|^CU13|^CU15|^COU13|^COU15|^W1U13|^W1U15', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122,TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^CU18|^CU21|^C50|^C65|^C[M|W]$', '1', TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80,TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
            CreateTargetFace($TourId, 3, '~DefaultCO', 'REG-^COU18|^COU21|^CO50|^CO65|^CO[M|W]$', '1', TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80,TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
            CreateTargetFace($TourId, 4, '~Default', 'REG-^W1U18|^W1U21|^W150|^W165|^W1[M|W]$', '1', TGT_OUT_FULL, 80, TGT_OUT_FULL, 80,TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
        } else {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^R|^B|^T|^L', '1',  TGT_OUT_FULL, 122, TGT_OUT_FULL, 122,TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
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
