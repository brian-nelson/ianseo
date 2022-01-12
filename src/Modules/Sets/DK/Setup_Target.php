<?php
/*
Common Setup for "Target" Archery
*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId);

// default Classes
CreateStandardClasses($TourId, $SubRule);

// default SubClasses
CreateStandardSubClasses($TourId);


// default Distances

switch($TourType) {
    case 1:
        CreateDistanceNew($TourId, $TourType, 'RHS', array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RHJ', array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RDS', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RDJ', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RHK', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RHM', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RDK', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'RDM', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'R_A', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'R_N', array(array('40m',40), array('30m',30), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'R_C', array(array('20m',20), array('15m',15), array('15m',15), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'CHS', array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CHJ', array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CDS', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CDJ', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CHK', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CHM', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CDK', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'CDM', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'C_A', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'C_N', array(array('40m',40), array('30m',30), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'C_C', array(array('20m',20), array('15m',15), array('15m',15), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'B_S', array(array('50m',50), array('40m',40), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'B_J', array(array('50m',50), array('40m',40), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'B_M', array(array('50m',50), array('40m',40), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'B_N', array(array('30m',30), array('30m',30), array('20m',20), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'B_A', array(array('30m',30), array('30m',30), array('20m',20), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'B_K', array(array('30m',30), array('30m',30), array('20m',20), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'B_C', array(array('15m',15), array('15m',15), array('10m',10), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'L_S', array(array('40m',40), array('40m',40), array('30m',30), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'L_J', array(array('40m',40), array('40m',40), array('30m',30), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'L_M', array(array('40m',40), array('40m',40), array('30m',30), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'L_N', array(array('30m',30), array('30m',30), array('20m',20), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'L_A', array(array('30m',30), array('30m',30), array('20m',20), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'L_K', array(array('30m',30), array('30m',30), array('20m',20), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'L_C', array(array('15m',15), array('15m',15), array('10m',10), array('10m',10)));
        break;
	case 3:
        if($SubRule==1 OR $SubRule==2) {
            CreateDistanceNew($TourId, $TourType, 'R_S', array(array('70m-1',70), array('70m-2',70)));
            CreateDistanceNew($TourId, $TourType, 'C_S', array(array('50m-1',50), array('50m-2',50)));
            CreateDistanceNew($TourId, $TourType, 'B_S', array(array('50m-1',50), array('50m-2',50)));
            CreateDistanceNew($TourId, $TourType, 'L_S', array(array('40m-1',40), array('40m-2',40)));
        }

        if($SubRule==1 OR $SubRule==3) {
            CreateDistanceNew($TourId, $TourType, 'R_M', array(array('60m-1',60), array('60m-2',60)));
            CreateDistanceNew($TourId, $TourType, 'C_M', array(array('50m-1',50), array('50m-2',50)));
            CreateDistanceNew($TourId, $TourType, 'B_M', array(array('50m-1',50), array('50m-2',50)));
            CreateDistanceNew($TourId, $TourType, 'L_M', array(array('40m-1',40), array('40m-2',40)));
        }

        if($SubRule==1 OR $SubRule==4) {
            CreateDistanceNew($TourId, $TourType, 'R_J', array(array('70m-1',70), array('70m-2',70)));
            CreateDistanceNew($TourId, $TourType, 'R_K', array(array('60m-1',60), array('60m-2',60)));
            CreateDistanceNew($TourId, $TourType, 'R_A', array(array('40m-1',40), array('40m-2',40)));
            CreateDistanceNew($TourId, $TourType, 'R_N', array(array('30m-1',30), array('30m-2',30)));
            CreateDistanceNew($TourId, $TourType, 'R_C', array(array('15m-1',15), array('15m-2',15)));
            CreateDistanceNew($TourId, $TourType, 'C_J', array(array('50m-1',50), array('50m-2',50)));
            CreateDistanceNew($TourId, $TourType, 'C_K', array(array('50m-1',50), array('50m-2',50)));
            CreateDistanceNew($TourId, $TourType, 'C_A', array(array('30m-1',30), array('30m-2',30)));
            CreateDistanceNew($TourId, $TourType, 'C_N', array(array('30m-1',30), array('30m-2',30)));
            CreateDistanceNew($TourId, $TourType, 'C_C', array(array('15m-1',15), array('15m-2',15)));
            CreateDistanceNew($TourId, $TourType, 'B_J', array(array('50m-1',50), array('50m-2',50)));
            CreateDistanceNew($TourId, $TourType, 'B_K', array(array('30m-1',30), array('30m-2',30)));
            CreateDistanceNew($TourId, $TourType, 'B_A', array(array('30m-1',30), array('30m-2',30)));
            CreateDistanceNew($TourId, $TourType, 'B_N', array(array('30m-1',30), array('30m-2',30)));
            CreateDistanceNew($TourId, $TourType, 'B_C', array(array('15m-1',15), array('15m-2',15)));
            CreateDistanceNew($TourId, $TourType, 'L_J', array(array('40m-1',40), array('40m-2',40)));
            CreateDistanceNew($TourId, $TourType, 'L_K', array(array('30m-1',30), array('30m-2',30)));
            CreateDistanceNew($TourId, $TourType, 'L_A', array(array('30m-1',30), array('30m-2',30)));
            CreateDistanceNew($TourId, $TourType, 'L_N', array(array('30m-1',30), array('30m-2',30)));
            CreateDistanceNew($TourId, $TourType, 'L_C', array(array('15m-1',15), array('15m-2',15)));
        }

		break;
    case 37:
    case 3:

            CreateDistanceNew($TourId, $TourType, 'R_S', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
            CreateDistanceNew($TourId, $TourType, 'C_S', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
            CreateDistanceNew($TourId, $TourType, 'B_S', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
            CreateDistanceNew($TourId, $TourType, 'L_S', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
            CreateDistanceNew($TourId, $TourType, 'R_M', array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
            CreateDistanceNew($TourId, $TourType, 'C_M', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
            CreateDistanceNew($TourId, $TourType, 'B_M', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
            CreateDistanceNew($TourId, $TourType, 'L_M', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
            CreateDistanceNew($TourId, $TourType, 'R_J', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
            CreateDistanceNew($TourId, $TourType, 'R_K', array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
            CreateDistanceNew($TourId, $TourType, 'R_A', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
            CreateDistanceNew($TourId, $TourType, 'R_N', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
            CreateDistanceNew($TourId, $TourType, 'R_C', array(array('15m-1',15), array('15m-2',15), array('15m-3',15), array('15m-4',15)));
            CreateDistanceNew($TourId, $TourType, 'C_J', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
            CreateDistanceNew($TourId, $TourType, 'C_K', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
            CreateDistanceNew($TourId, $TourType, 'C_A', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
            CreateDistanceNew($TourId, $TourType, 'C_N', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
            CreateDistanceNew($TourId, $TourType, 'C_C', array(array('15m-1',15), array('15m-2',15), array('15m-3',15), array('15m-4',15)));
            CreateDistanceNew($TourId, $TourType, 'B_J', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
            CreateDistanceNew($TourId, $TourType, 'B_K', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
            CreateDistanceNew($TourId, $TourType, 'B_A', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
            CreateDistanceNew($TourId, $TourType, 'B_N', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
            CreateDistanceNew($TourId, $TourType, 'B_C', array(array('15m-1',15), array('15m-2',15), array('15m-3',15), array('15m-4',15)));
            CreateDistanceNew($TourId, $TourType, 'L_J', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
            CreateDistanceNew($TourId, $TourType, 'L_K', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
            CreateDistanceNew($TourId, $TourType, 'L_A', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
            CreateDistanceNew($TourId, $TourType, 'L_N', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
            CreateDistanceNew($TourId, $TourType, 'L_C', array(array('15m-1',15), array('15m-2',15), array('15m-3',15), array('15m-4',15)));

        break;
    case 5:
        CreateDistanceNew($TourId, $TourType, 'R_S', array(array('60m',60), array('50m',50), array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'R_J', array(array('60m',60), array('50m',50), array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'R_M', array(array('60m',60), array('50m',50), array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'R_K', array(array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'R_A', array(array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'R_N', array(array('30m',30), array('25m',25), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'C_S', array(array('60m',60), array('50m',50), array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'C_J', array(array('60m',60), array('50m',50), array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'C_M', array(array('60m',60), array('50m',50), array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'C_K', array(array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'C_A', array(array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'C_N', array(array('30m',30), array('25m',25), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'C_C', array(array('20m',20), array('15m',15), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'B_S', array(array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'B_J', array(array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'B_M', array(array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'B_K', array(array('30m',30), array('25m',25), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'B_A', array(array('30m',30), array('25m',25), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'B_N', array(array('20m',20), array('15m',15), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, 'L_S', array(array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'L_J', array(array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'L_M', array(array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'L_K', array(array('30m',30), array('25m',25), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'L_A', array(array('30m',30), array('25m',25), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'L_N', array(array('20m',20), array('15m',15), array('10m',10)));
        CreateDistanceNew($TourId, $TourType, '__C', array(array('20m',20), array('15m',15), array('10m',10)));

        break;
    case 6:
        if($SubRule==1 OR $SubRule==2) {
            CreateDistanceNew($TourId, $TourType, '__S', array(array('18m-1',18), array('18m-2',18)));
        }
        if($SubRule==1 OR $SubRule==3) {
            CreateDistanceNew($TourId, $TourType, '__M', array(array('18m-1',18), array('18m-2',18)));
        }
        if($SubRule==1 OR $SubRule==4) {
            CreateDistanceNew($TourId, $TourType, '__J', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, '__K', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, '__A', array(array('12m-1',12), array('12m-2',12)));
            CreateDistanceNew($TourId, $TourType, '__N', array(array('12m-1',12), array('12m-2',12)));
            CreateDistanceNew($TourId, $TourType, '__C', array(array('8m-1',8), array('8m-2',8)));
        }
        break;
    case 7:
        CreateDistanceNew($TourId, $TourType, '__S', array(array('25m-1',25), array('25m-2',25)));
        CreateDistanceNew($TourId, $TourType, '__M', array(array('25m-1',25), array('25m-2',25)));
        CreateDistanceNew($TourId, $TourType, '__J', array(array('25m-1',25), array('25m-2',25)));
        CreateDistanceNew($TourId, $TourType, '__K', array(array('25m-1',25), array('25m-2',25)));
        CreateDistanceNew($TourId, $TourType, '__A', array(array('15m-1',15), array('15m-2',15)));
        CreateDistanceNew($TourId, $TourType, '__N', array(array('15m-1',15), array('15m-2',15)));
        CreateDistanceNew($TourId, $TourType, '__C', array(array('10m-1',10), array('10m-2',10)));
        break;
}


if($TourType==3 or $TourType==6 or $TourType==7 or $TourType==37) {
	// default Events
	CreateStandardEvents($TourId, $SubRule, $TourType);

	// Finals & TeamFinals
	CreateFinals($TourId);
}


// Default Target
switch($TourType) {
    case 1:
        CreateTargetFace($TourId, 1, '~Default', '%', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
        CreateTargetFace($TourId, 2, '~50: 5-X/30,: 5-X', 'REG-^R|^C', '',TGT_OUT_FULL, 122, TGT_OUT_FULL, 122,TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
        CreateTargetFace($TourId, 3, '~50: 6-X/30: 6X', 'REG-^R|^C', '',TGT_OUT_FULL, 122, TGT_OUT_FULL, 122,TGT_OUT_6_big10, 80, TGT_OUT_6_big10, 80);
        break;
	case 3:
	    CreateTargetFace($TourId, 1, '~Default', 'REG-^R|^B|^L', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
        if($SubRule==1) {
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C', '1', TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
            CreateTargetFace($TourId, 3, '~50: 5-X', 'REG-^C', '0', TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
        } else {
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C', '1', TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
            CreateTargetFace($TourId, 3, '~50: 1-X', 'REG-^C', '0', TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
        }
		break;
    case 37:
        CreateTargetFace($TourId, 1, '~Default', 'REG-^R|^B|^L', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
        CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C', '1', TGT_OUT_FULL, 80, TGT_OUT_FULL, 80, TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
        CreateTargetFace($TourId, 3, '~50: 5-X', 'REG-^C', '0', TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
        break;
    case 5:
        CreateTargetFace($TourId, 1, '~Default', '%', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122,TGT_OUT_FULL, 122);
        break;
    case 6:
        CreateTargetFace($TourId, 1, '~Default', 'REG-^R|^B|^L', '1', TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
        CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C', '1', TGT_IND_1_small10, 40, TGT_IND_1_small10, 40);
        CreateTargetFace($TourId, 3, '~Option', 'REG-^R|^B|^L', '0', TGT_IND_6_big10, 40, TGT_IND_6_big10, 40);
        CreateTargetFace($TourId, 4, '~OptionCO', 'REG-^C', '0', TGT_IND_6_small10, 40, TGT_IND_6_small10, 40);
        break;
    case 7:
        CreateTargetFace($TourId, 1, '~Default', 'REG-^R|^B|^L', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);
        CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C', '1', TGT_IND_1_small10, 60, TGT_IND_1_small10, 60);
        CreateTargetFace($TourId, 3, '~Option', 'REG-^R|^B|^L', '0', TGT_IND_6_big10, 60, TGT_IND_6_big10, 60);
        CreateTargetFace($TourId, 4, '~OptionCO', 'REG-^C', '0', TGT_IND_6_small10, 60, TGT_IND_6_small10, 60);
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
