<?php
/*
Common Setup for "Target" Archery
*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType, $SubRule);

// default SubClasses
CreateStandardSubClasses($TourId, $TourType, $SubRule);

// default Classes
CreateStandardClasses($TourId, $TourType, $SubRule);

// default Distances
switch($TourType) {
    case 1:
        CreateDistanceNew($TourId, $TourType, '_H', array(array('90 m', 90), array('70 m', 70), array('50 m', 50), array('30 m', 30)));
        CreateDistanceNew($TourId, $TourType, '_D', array(array('70 m', 70), array('60 m', 60), array('50 m', 50), array('30 m', 30)));
        CreateDistanceNew($TourId, $TourType, '_JH', array(array('90 m', 90), array('70 m', 70), array('50 m', 50), array('30 m', 30)));
        CreateDistanceNew($TourId, $TourType, '_JD', array(array('70 m', 70), array('60 m', 60), array('50 m', 50), array('30 m', 30)));
        CreateDistanceNew($TourId, $TourType, '_CH', array(array('70 m', 70), array('60 m', 60), array('50 m', 50), array('30 m', 30)));
        CreateDistanceNew($TourId, $TourType, '_CD', array(array('60 m', 60), array('50 m', 50), array('40 m', 40), array('30 m', 30)));
        CreateDistanceNew($TourId, $TourType, '_MH', array(array('70 m', 70), array('60 m', 60), array('50 m', 50), array('30 m', 30)));
        CreateDistanceNew($TourId, $TourType, '_MD', array(array('60 m', 60), array('50 m', 50), array('40 m', 40), array('30 m', 30)));
        break;
    case 2:
        CreateDistanceNew($TourId, $TourType, '_H', array(array('90 m', 90), array('70 m', 70), array('50 m', 50), array('30 m', 30)));
        CreateDistanceNew($TourId, $TourType, '_D', array(array('70 m', 70), array('60 m', 60), array('50 m', 50), array('30 m', 30)));
        CreateDistanceNew($TourId, $TourType, '_JH', array(array('90 m', 90), array('70 m', 70), array('50 m', 50), array('30 m', 30)));
        CreateDistanceNew($TourId, $TourType, '_JD', array(array('70 m', 70), array('60 m', 60), array('50 m', 50), array('30 m', 30)));
        CreateDistanceNew($TourId, $TourType, '_CH', array(array('70 m', 70), array('60 m', 60), array('50 m', 50), array('30 m', 30)));
        CreateDistanceNew($TourId, $TourType, '_CD', array(array('60 m', 60), array('50 m', 50), array('40 m', 40), array('30 m', 30)));
        CreateDistanceNew($TourId, $TourType, '_MH', array(array('70 m', 70), array('60 m', 60), array('50 m', 50), array('30 m', 30)));
        CreateDistanceNew($TourId, $TourType, '_MD', array(array('60 m', 60), array('50 m', 50), array('40 m', 40), array('30 m', 30)));
        break;
	case 3:
	    if($SubRule==1) {
            CreateDistanceNew($TourId, $TourType, '_A_', array(array('30m-1', 30), array('30m-2', 30)));
            CreateDistanceNew($TourId, $TourType, 'RC_', array(array('60m-1', 60), array('60m-2', 60)));
            CreateDistanceNew($TourId, $TourType, 'RM_', array(array('60m-1', 60), array('60m-2', 60)));
            CreateDistanceNew($TourId, $TourType, 'RJ_', array(array('70m-1', 70), array('70m-2', 70)));
            CreateDistanceNew($TourId, $TourType, 'R_', array(array('70m-1', 70), array('70m-2', 70)));
            CreateDistanceNew($TourId, $TourType, 'CC_', array(array('50m-1', 50), array('50m-2', 50)));
            CreateDistanceNew($TourId, $TourType, 'CM_', array(array('50m-1', 50), array('50m-2', 50)));
            CreateDistanceNew($TourId, $TourType, 'CJ_', array(array('50m-1', 50), array('50m-2', 50)));
            CreateDistanceNew($TourId, $TourType, 'C_', array(array('50m-1', 50), array('50m-2', 50)));
            CreateDistanceNew($TourId, $TourType, 'BC_', array(array('50m-1', 50), array('50m-2', 50)));
            CreateDistanceNew($TourId, $TourType, 'BM_', array(array('50m-1', 50), array('50m-2', 50)));
            CreateDistanceNew($TourId, $TourType, 'BJ_', array(array('50m-1', 50), array('50m-2', 50)));
            CreateDistanceNew($TourId, $TourType, 'B_', array(array('50m-1', 50), array('50m-2', 50)));
        } else {
		    CreateDistanceNew($TourId, $TourType, 'R%', array(array('70m-1',70), array('70m-2',70)));
		    CreateDistanceNew($TourId, $TourType, 'C%', array(array('50m-1',50), array('50m-2',50)));
		    CreateDistanceNew($TourId, $TourType, 'B%', array(array('50m-1',50), array('50m-2',50)));
        }
		break;
    case 37:
        CreateDistanceNew($TourId, $TourType, '_A_', array(array('30m-1', 30), array('30m-2', 30), array('30m-3', 30), array('30m-4',30)));
        CreateDistanceNew($TourId, $TourType, 'RC_', array(array('60m-1', 60), array('60m-2', 60), array('60m-3', 60), array('60m-4',60)));
        CreateDistanceNew($TourId, $TourType, 'RM_', array(array('60m-1', 60), array('60m-2', 60), array('60m-3', 60), array('60m-4',60)));
        CreateDistanceNew($TourId, $TourType, 'RJ_', array(array('70m-1', 70), array('70m-2', 70), array('70m-3', 70), array('70m-4',70)));
        CreateDistanceNew($TourId, $TourType, 'R_',  array(array('70m-1', 70), array('70m-2', 70), array('70m-3', 70), array('70m-4',70)));
        CreateDistanceNew($TourId, $TourType, 'CC_', array(array('50m-1', 50), array('50m-2', 50), array('50m-3', 50), array('50m-4',50)));
        CreateDistanceNew($TourId, $TourType, 'CM_', array(array('50m-1', 50), array('50m-2', 50), array('50m-3', 50), array('50m-4',50)));
        CreateDistanceNew($TourId, $TourType, 'CJ_', array(array('50m-1', 50), array('50m-2', 50), array('50m-3', 50), array('50m-4',50)));
        CreateDistanceNew($TourId, $TourType, 'C_',  array(array('50m-1', 50), array('50m-2', 50), array('50m-3', 50), array('50m-4',50)));
        CreateDistanceNew($TourId, $TourType, 'BC_', array(array('50m-1', 50), array('50m-2', 50), array('50m-3', 50), array('50m-4',50)));
        CreateDistanceNew($TourId, $TourType, 'BM_', array(array('50m-1', 50), array('50m-2', 50), array('50m-3', 50), array('50m-4',50)));
        CreateDistanceNew($TourId, $TourType, 'BJ_', array(array('50m-1', 50), array('50m-2', 50), array('50m-3', 50), array('50m-4',50)));
        CreateDistanceNew($TourId, $TourType, 'B_',  array(array('50m-1', 50), array('50m-2', 50), array('50m-3', 50), array('50m-4',50)));
	    break;
	case 6:
	    if($SubRule==1) {
		    CreateDistanceNew($TourId, $TourType, '%', array(array('18m', 18)));
        } else if($SubRule==2) {
		    CreateDistanceNew($TourId, $TourType, '%U_', array(array('18m-1', 18), array('-',0)));
		    CreateDistanceNew($TourId, $TourType, '%A_', array(array('18m-1', 18), array('-',0)));
		    CreateDistanceNew($TourId, $TourType, '%S_', array(array('18m-1', 18), array('18m-2',18)));
		    CreateDistanceNew($TourId, $TourType, '%J', array(array('18m-1', 18), array('18m-2',18)));
		    CreateDistanceNew($TourId, $TourType, '%C_', array(array('18m-1', 18), array('18m-2',18)));
		    CreateDistanceNew($TourId, $TourType, '%J_', array(array('18m-1', 18), array('18m-2',18)));
        } else {
		    CreateDistanceNew($TourId, $TourType, '%', array(array('18m-1',18), array('18m-2',18)));
        }
		break;
    case 7:
        CreateDistanceNew($TourId, $TourType, '%', array(array('25m-1',25), array('25m-2',25)));
        break;
    case 41:
        CreateDistanceNew($TourId, $TourType, '_U_', array(array('20m-1',20), array('20m-2',20), array('20m-3',20)));
        CreateDistanceNew($TourId, $TourType, 'BA_', array(array('20m-1',20), array('20m-2',20), array('20m-3',20)));
        CreateDistanceNew($TourId, $TourType, 'RA_', array(array('40 m',40), array('30 m',30), array('20 m',20)));
        CreateDistanceNew($TourId, $TourType, 'CA_', array(array('40 m',40), array('30 m',30), array('20 m',20)));
        CreateDistanceNew($TourId, $TourType, 'BC_', array(array('40 m',40), array('30 m',30), array('20 m',20)));
        CreateDistanceNew($TourId, $TourType, 'RC_', array(array('60 m',60), array('40 m',40), array('30 m',30)));
        CreateDistanceNew($TourId, $TourType, 'CC_', array(array('60 m',60), array('40 m',40), array('30 m',30)));
        CreateDistanceNew($TourId, $TourType, 'BJ_', array(array('60 m',60), array('40 m',40), array('30 m',30)));
        CreateDistanceNew($TourId, $TourType, 'RJ_', array(array('70 m',70), array('50 m',50), array('30 m',30)));
        CreateDistanceNew($TourId, $TourType, 'CJ_', array(array('70 m',70), array('50 m',50), array('30 m',30)));
        break;
    case 42:
        if($SubRule==1) {
	        CreateDistanceNew($TourId, $TourType, '%', array(array('25m',25)));
        } else {
	        CreateDistanceNew($TourId, $TourType, '%U_', array(array('25m-1',25), array('-',0)));
	        CreateDistanceNew($TourId, $TourType, '%A_', array(array('25m-1',25), array('-',0)));
	        CreateDistanceNew($TourId, $TourType, '%C_', array(array('25m-1',25), array('25m-2',25)));
	        CreateDistanceNew($TourId, $TourType, '%J_', array(array('25m-1',25), array('25m-2',25)));
	        CreateDistanceNew($TourId, $TourType, '%J', array(array('25m-1',25), array('25m-2',25)));
	        CreateDistanceNew($TourId, $TourType, '%S_', array(array('25m-1',25), array('25m-2',25)));
        }
        break;
    case 43:
        CreateDistanceNew($TourId, $TourType, '%', array(array('Open Ronde',0)));
}

if($TourType==6 OR $TourType==3 OR $TourType==42) {
    if(($TourType==6 OR $TourType==42) and $SubRule<=2) {
        $i=1;
        for($loop=1; $loop<=6; $loop++) {
            CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_6_big10, 5, 3, 1, 5, 3, 1, 'RS'.$loop, 'Recurve Senioren Klasse '.$loop, 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        }
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_6_big10, 5, 3, 1, 5, 3, 1, 'RJ1', 'Recurve Junioren Klasse 1', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_6_big10, 5, 3, 1, 5, 3, 1, 'RJ2', 'Recurve Junioren Klasse 2', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_6_big10, 5, 3, 1, 5, 3, 1, 'RC1', 'Recurve Cadetten Klasse 1', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_6_big10, 5, 3, 1, 5, 3, 1, 'RC2', 'Recurve Cadetten Klasse 2', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));

        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_6_small10, 5, 3, 1, 5, 3, 1, 'CS1', 'Compound Senioren Klasse 1', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_6_small10, 5, 3, 1, 5, 3, 1, 'CS2', 'Compound Senioren Klasse 2', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_6_small10, 5, 3, 1, 5, 3, 1, 'CJ1', 'Compound Junioren Klasse 1', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_6_small10, 5, 3, 1, 5, 3, 1, 'CJ2', 'Compound Junioren Klasse 2', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_6_small10, 5, 3, 1, 5, 3, 1, 'CC1', 'Compound Cadetten Klasse 1', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_6_small10, 5, 3, 1, 5, 3, 1, 'CC2', 'Compound Cadetten Klasse 2', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));

        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'BS1', 'Barebow Senioren Klasse 1', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'BS2', 'Barebow Senioren Klasse 2', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'BJ', 'Barebow Jeugd Klasse 1', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));

        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'LBS1', 'Longbow Senioren Klasse 1', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'LBS2', 'Longbow Senioren Klasse 2', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'LBJ', 'Longbow Jeugd Klasse 1', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));

        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'IBS1', 'Istinctive Bow Senioren Klasse 1', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'IBS2', 'Istinctive Bow Senioren Klasse 2', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'IBJ', 'Istinctive Bow Jeugd Klasse 1', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));

        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'RCUJ', 'Recurve Jongens Aspiranten t/m 10 jaar', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', 60, ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'RCUM', 'Recurve Meisjes Aspiranten t/m 10 jaar', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', 60, ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'RCAJ', 'Recurve Jongens Aspiranten 10 en 11 jaar', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', 60, ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'RCAM', 'Recurve Meisjes Aspiranten 10 en 11 jaar', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', 60, ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'CPUJ', 'Compound Jongens Aspiranten t/m 10 jaar', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', 60, ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'CPUM', 'Compound Meisjes Aspiranten t/m 10 jaar', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', 60, ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'CPAJ', 'Compound Jongens Aspiranten 10 en 11 jaar', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', 60, ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'CPAM', 'Compound Meisjes Aspiranten 10 en 11 jaar', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', 60, ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'BBUJ', 'Barebow Jongens Aspiranten t/m 10 jaar', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', 60, ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'BBUM', 'Barebow Meisjes Aspiranten t/m 10 jaar', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', 60, ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'BBAJ', 'Barebow Jongens Aspiranten 10 en 11 jaar', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', 60, ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'BBAM', 'Barebow Meisjes Aspiranten 10 en 11 jaar', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', 60, ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'LBUJ', 'Longbow Jongens Aspiranten t/m 10 jaar', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', 60, ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'LBUM', 'Longbow Meisjes Aspiranten t/m 10 jaar', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', 60, ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'LBAJ', 'Longbow Jongens Aspiranten 10 en 11 jaar', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', 60, ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'LBAM', 'Longbow Meisjes Aspiranten 10 en 11 jaar', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', 60, ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'IBUJ', 'Istinctive Bow Jongens Aspiranten t/m 10 jaar', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', 60, ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'IBUM', 'Istinctive Bow Meisjes Aspiranten t/m 10 jaar', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', 60, ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'IBAJ', 'Istinctive Bow Jongens Aspiranten 10 en 11 jaar', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', 60, ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 0, 0, 0, TGT_IND_1_big10, 5, 3, 1, 5, 3, 1, 'IBAM', 'Istinctive Bow Meisjes Aspiranten 10 en 11 jaar', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', 60, ($TourType==42 ? 25 : 18));

        foreach (array('R','C','B','LB','IB') as $vDiv) {
            foreach(array('S1','S2','S3','S4','S5','S6','C1','C2','J1','J2','J') as $vCl) {
                InsertClassEvent($TourId, 0, 1, $vDiv . $vCl, $vDiv, $vCl);
            }
        }

        foreach (array('RC','CP','BB','LB','IB') as $vDiv) {
            foreach(array('UJ','UM','AJ','AM') as $vCl) {
                InsertClassEvent($TourId, 0, 1, $vDiv . $vCl, $vDiv, $vCl);
            }
        }

        $i=1;
        CreateEvent($TourId, $i++, 1, 0, 0, TGT_IND_6_big10, 5, 3, 1, 5, 3, 1, 'RS', 'Recurve Senioren Ereklasse', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 1, 0, 0, TGT_IND_6_big10, 5, 3, 1, 5, 3, 1, 'RS_A', 'Recurve Senioren Klasse A', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 1, 0, 0, TGT_IND_6_big10, 5, 3, 1, 5, 3, 1, 'RS_B', 'Recurve Senioren Klasse B', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 1, 0, 0, TGT_IND_6_big10, 5, 3, 1, 5, 3, 1, 'RS_C', 'Recurve Senioren Klasse C', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 1, 0, 0, TGT_IND_6_big10, 5, 3, 1, 5, 3, 1, 'RS_D', 'Recurve Senioren Klasse D', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 1, 0, 0, TGT_IND_6_small10, 5, 3, 1, 5, 3, 1, 'CS', 'Compound Senioren Ereklasse', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 1, 0, 0, TGT_IND_6_small10, 5, 3, 1, 5, 3, 1, 'CS_A', 'Compound Senioren Klasse A', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 1, 0, 0, TGT_IND_6_small10, 5, 3, 1, 5, 3, 1, 'BS', 'Barebow Senioren Ereklasse', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 1, 0, 0, TGT_IND_6_small10, 5, 3, 1, 5, 3, 1, 'LBS', 'Longbow Senioren Ereklasse', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));
        CreateEvent($TourId, $i++, 1, 0, 0, TGT_IND_6_small10, 5, 3, 1, 5, 3, 1, 'IBS', 'Istinctive Bow Senioren Ereklasse', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', ($TourType==42 ? 60 : 40), ($TourType==42 ? 25 : 18));

        $tmpArrDefinition = array(
            'RS'=>array('d'=>'R', 'k'=>'E', 'c'=>array('S1','S2','S3','S4','S5','S6','C1','C2','J1','J2')),
            'RS_A'=>array('d'=>'R', 'k'=>'A', 'c'=>array('S1','S2','S3','S4','S5','S6','C1','C2','J1','J2')),
            'RS_B'=>array('d'=>'R', 'k'=>'B', 'c'=>array('S1','S2','S3','S4','S5','S6','C1','C2','J1','J2')),
            'RS_C'=>array('d'=>'R', 'k'=>'C', 'c'=>array('S1','S2','S3','S4','S5','S6','C1','C2','J1','J2')),
            'RS_D'=>array('d'=>'R', 'k'=>'D', 'c'=>array('S1','S2','S3','S4','S5','S6','C1','C2','J1','J2')),
            'CS'=>array('d'=>'C', 'k'=>'E', 'c'=>array('S1','S2','C1','C2','J1','J2')),
            'CS_A'=>array('d'=>'C', 'k'=>'A', 'c'=>array('S1','S2','C1','C2','J1','J2')),
            'BS'=>array('d'=>'B', 'k'=>'E', 'c'=>array('S1','S2','J')),
            'IBS'=>array('d'=>'IB', 'k'=>'E', 'c'=>array('S1','S2','J')),
            'LBS'=>array('d'=>'LB', 'k'=>'E', 'c'=>array('S1','S2','J')),
        );

        foreach($tmpArrDefinition as $teamCode=>$def) {
            foreach($def['c'] as $cl) {
                InsertClassEvent($TourId, 1, 3, $teamCode, $def['d'], $cl, $def['k']);
            }
        }


        foreach (array('R','C','B','LB','IB') as $vDiv) {
        }


    } else {
        // default Events
        CreateStandardEvents($TourId, $SubRule, $TourType != 6);

        // Classes in Events
        InsertStandardEvents($TourId, $SubRule, $TourType != 6);

        //Add Looser Backets if needed
        if (($TourType == 3 and $SubRule == 3) or ($TourType == 6 and $SubRule == 4)) {
            $TargetR=(($TourType != 6) ? TGT_OUT_FULL : TGT_IND_6_big10);
            $TargetC=(($TourType != 6) ? TGT_OUT_5_big10 : TGT_IND_6_small10);
            $TargetB=(($TourType != 6) ? TGT_OUT_FULL : TGT_IND_1_big10);
            $TargetSizeR=(($TourType != 6) ? 122 : 40);
            $TargetSizeC=(($TourType != 6) ? 80 : 40);
            $TargetSizeB=(($TourType != 6) ? 122 : 40);
            $DistanceR=(($TourType != 6) ? 70 : 18);
            $DistanceC=(($TourType != 6) ? 50 : 18);
            $DistanceB=(($TourType != 6) ? 50 : 18);

            $i = 10;
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'RH-A', 'Recurve Heren - 17e t/n 20e plaats', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeR, $DistanceR, 'RH', '0', '0', 17);
            CreateEvent($TourId, $i++, 0, 0, 4, $TargetR, 5, 3, 1, 5, 3, 1, 'RH-1', 'Recurve Heren - 9e t/n 12e plaats', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeR, $DistanceR, 'RH', '0', '0', 9);
            CreateEvent($TourId, $i++, 0, 0, 4, $TargetR, 5, 3, 1, 5, 3, 1, 'RH-B', 'Recurve Heren - 25e t/n 28e plaats', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeR, $DistanceR, 'RH-A', '0', '0', 25);
            CreateEvent($TourId, $i++, 0, 0, 2, $TargetR, 5, 3, 1, 5, 3, 1, 'RH-2', 'Recurve Heren - 5e t/n 8e plaats', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeR, $DistanceR, 'RH', '0', '0', 5);
            CreateEvent($TourId, $i++, 0, 0, 2, $TargetR, 5, 3, 1, 5, 3, 1, 'RH-3', 'Recurve Heren - 13e t/n 16e plaats', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeR, $DistanceR, 'RH-1', '0', '0', 13);
            CreateEvent($TourId, $i++, 0, 0, 2, $TargetR, 5, 3, 1, 5, 3, 1, 'RH-C', 'Recurve Heren - 21e t/n 24e plaats', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeR, $DistanceR, 'RH-A', '0', '0', 21);
            CreateEvent($TourId, $i++, 0, 0, 2, $TargetR, 5, 3, 1, 5, 3, 1, 'RH-D', 'Recurve Heren - 29e t/n 32e plaats', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeR, $DistanceR, 'RH-B', '0', '0', 29);
            InsertClassEvent($TourId, 0, 1, 'RH-1', 'R', 'H');
            InsertClassEvent($TourId, 0, 1, 'RH-2', 'R', 'H');
            InsertClassEvent($TourId, 0, 1, 'RH-3', 'R', 'H');
            InsertClassEvent($TourId, 0, 1, 'RH-A', 'R', 'H');
            InsertClassEvent($TourId, 0, 1, 'RH-B', 'R', 'H');
            InsertClassEvent($TourId, 0, 1, 'RH-C', 'R', 'H');
            InsertClassEvent($TourId, 0, 1, 'RH-D', 'R', 'H');

            CreateEvent($TourId, $i++, 0, 0, 4, $TargetR, 5, 3, 1, 5, 3, 1, 'RD-1', 'Recurve Dames - 9e t/n 12e plaats', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeR, $DistanceR, 'RD', '0', '0', 9);
            CreateEvent($TourId, $i++, 0, 0, 2, $TargetR, 5, 3, 1, 5, 3, 1, 'RD-2', 'Recurve Dames - 5e t/n 8e plaats', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeR, $DistanceR, 'RD', '0', '0', 5);
            CreateEvent($TourId, $i++, 0, 0, 2, $TargetR, 5, 3, 1, 5, 3, 1, 'RD-3', 'Recurve Dames - 13e t/n 16e plaats', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeR, $DistanceR, 'RD-1', '0', '0', 13);
            InsertClassEvent($TourId, 0, 1, 'RD-1', 'R', 'D');
            InsertClassEvent($TourId, 0, 1, 'RD-2', 'R', 'D');
            InsertClassEvent($TourId, 0, 1, 'RD-3', 'R', 'D');

            CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'CH-A', 'Compound Heren - 17e t/n 20e plaats', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeC, $DistanceC, 'CH', '0', '0', 17);
            CreateEvent($TourId, $i++, 0, 0, 4, $TargetC, 5, 3, 1, 5, 3, 1, 'CH-1', 'Compound Heren - 9e t/n 12e plaats', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeC, $DistanceC, 'CH', '0', '0', 9);
            CreateEvent($TourId, $i++, 0, 0, 4, $TargetC, 5, 3, 1, 5, 3, 1, 'CH-B', 'Compound Heren - 25e t/n 28e plaats', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeC, $DistanceC, 'CH-A', '0', '0', 25);
            CreateEvent($TourId, $i++, 0, 0, 2, $TargetC, 5, 3, 1, 5, 3, 1, 'CH-2', 'Compound Heren - 5e t/n 8e plaats', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeC, $DistanceC, 'CH', '0', '0', 5);
            CreateEvent($TourId, $i++, 0, 0, 2, $TargetC, 5, 3, 1, 5, 3, 1, 'CH-3', 'Compound Heren - 13e t/n 16e plaats', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeC, $DistanceC, 'CH-1', '0', '0', 13);
            CreateEvent($TourId, $i++, 0, 0, 2, $TargetC, 5, 3, 1, 5, 3, 1, 'CH-C', 'Compound Heren - 21e t/n 24e plaats', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeC, $DistanceC, 'CH-A', '0', '0', 21);
            CreateEvent($TourId, $i++, 0, 0, 2, $TargetC, 5, 3, 1, 5, 3, 1, 'CH-D', 'Compound Heren - 29e t/n 32e plaats', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeC, $DistanceC, 'CH-B', '0', '0', 29);
            InsertClassEvent($TourId, 0, 1, 'CH-1', 'C', 'H');
            InsertClassEvent($TourId, 0, 1, 'CH-2', 'C', 'H');
            InsertClassEvent($TourId, 0, 1, 'CH-3', 'C', 'H');
            InsertClassEvent($TourId, 0, 1, 'CH-A', 'C', 'H');
            InsertClassEvent($TourId, 0, 1, 'CH-B', 'C', 'H');
            InsertClassEvent($TourId, 0, 1, 'CH-C', 'C', 'H');
            InsertClassEvent($TourId, 0, 1, 'CH-D', 'C', 'H');

            CreateEvent($TourId, $i++, 0, 0, 4, $TargetC, 5, 3, 1, 5, 3, 1, 'CD-1', 'Compound Dames - 9e t/n 12e plaats', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeC, $DistanceC, 'CD', '0', '0', 9);
            CreateEvent($TourId, $i++, 0, 0, 2, $TargetC, 5, 3, 1, 5, 3, 1, 'CD-2', 'Compound Dames - 5e t/n 8e plaats', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeC, $DistanceC, 'CD', '0', '0', 5);
            CreateEvent($TourId, $i++, 0, 0, 2, $TargetC, 5, 3, 1, 5, 3, 1, 'CD-3', 'Compound Dames - 13e t/n 16e plaats', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeC, $DistanceC, 'CD-1', '0', '0', 13);
            InsertClassEvent($TourId, 0, 1, 'CD-1', 'C', 'D');
            InsertClassEvent($TourId, 0, 1, 'CD-2', 'C', 'D');
            InsertClassEvent($TourId, 0, 1, 'CD-3', 'C', 'D');

            CreateEvent($TourId, $i++, 0, 0, 8, $TargetB, 5, 3, 1, 5, 3, 1, 'BH-A', 'Barebow Heren - 17e t/n 20e plaats', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeB, $DistanceB, 'BH', '0', '0', 17);
            CreateEvent($TourId, $i++, 0, 0, 4, $TargetB, 5, 3, 1, 5, 3, 1, 'BH-1', 'Barebow Heren - 9e t/n 12e plaats', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeB, $DistanceB, 'BH', '0', '0', 9);
            CreateEvent($TourId, $i++, 0, 0, 4, $TargetB, 5, 3, 1, 5, 3, 1, 'BH-B', 'Barebow Heren - 25e t/n 28e plaats', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeB, $DistanceB, 'BH-A', '0', '0', 25);
            CreateEvent($TourId, $i++, 0, 0, 2, $TargetB, 5, 3, 1, 5, 3, 1, 'BH-2', 'Barebow Heren - 5e t/n 8e plaats', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeB, $DistanceB, 'BH', '0', '0', 5);
            CreateEvent($TourId, $i++, 0, 0, 2, $TargetB, 5, 3, 1, 5, 3, 1, 'BH-3', 'Barebow Heren - 13e t/n 16e plaats', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeB, $DistanceB, 'BH-1', '0', '0', 13);
            CreateEvent($TourId, $i++, 0, 0, 2, $TargetB, 5, 3, 1, 5, 3, 1, 'BH-C', 'Barebow Heren - 21e t/n 24e plaats', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeB, $DistanceB, 'BH-A', '0', '0', 21);
            CreateEvent($TourId, $i++, 0, 0, 2, $TargetB, 5, 3, 1, 5, 3, 1, 'BH-D', 'Barebow Heren - 29e t/n 32e plaats', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeB, $DistanceB, 'BH-B', '0', '0', 29);
            InsertClassEvent($TourId, 0, 1, 'BH-1', 'B', 'H');
            InsertClassEvent($TourId, 0, 1, 'BH-2', 'B', 'H');
            InsertClassEvent($TourId, 0, 1, 'BH-3', 'B', 'H');
            InsertClassEvent($TourId, 0, 1, 'BH-A', 'B', 'H');
            InsertClassEvent($TourId, 0, 1, 'BH-B', 'B', 'H');
            InsertClassEvent($TourId, 0, 1, 'BH-C', 'B', 'H');
            InsertClassEvent($TourId, 0, 1, 'BH-D', 'B', 'H');

            CreateEvent($TourId, $i++, 0, 0, 4, $TargetB, 5, 3, 1, 5, 3, 1, 'BD-1', 'Barebow Dames - 9e t/n 12e plaats', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeB, $DistanceB, 'BD', '0', '0', 9);
            CreateEvent($TourId, $i++, 0, 0, 2, $TargetB, 5, 3, 1, 5, 3, 1, 'BD-2', 'Barebow Dames - 5e t/n 8e plaats', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeB, $DistanceB, 'BD', '0', '0', 5);
            CreateEvent($TourId, $i++, 0, 0, 2, $TargetB, 5, 3, 1, 5, 3, 1, 'BD-3', 'Barebow Dames - 13e t/n 16e plaats', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, '', '', $TargetSizeB, $DistanceB, 'BD-1', '0', '0', 13);
            InsertClassEvent($TourId, 0, 1, 'BD-1', 'B', 'D');
            InsertClassEvent($TourId, 0, 1, 'BD-2', 'B', 'D');
            InsertClassEvent($TourId, 0, 1, 'BD-3', 'B', 'D');

            safe_w_sql("UPDATE Events SET EvMedals=0 WHERE EvCode IN ('RH-1','RH-2','RH-3','RH-A','RH-B','RH-C','RH-D','RD-1','RD-2','RD-3','CH-1','CH-2','CH-3','CH-A','CH-B','CH-C','CH-D','CD-1','CD-2','CD-3') AND EvTournament=$TourId");
        }

        // Finals & TeamFinals
        CreateFinals($TourId);
    }
} else if($TourType==43) {
    $i=1;
    $SettingsInd=array(
        'EvFinalFirstPhase' => '0',
        'EvFinalTargetType'=>TGT_FIELD,
        'EvTargetSize'=>0,
        'EvElimEnds'=>10,
        'EvElimArrows'=>1,
        'EvElimSO'=>1,
        'EvFinEnds'=>4,
        'EvFinArrows'=>3,
        'EvFinSO'=>1,
        'EvElimType'=>1,
        'EvElim2'=>10,
        'EvE2Ends'=>10,
        'EvE2Arrows'=>1,
        'EvE2SO'=>1,
        'EvFinalAthTarget'=>0,
        'EvMatchArrowsNo'=>0,
    );
    CreateEventNew($TourId, 'TH', 'Traditioneel Heren', $i++, $SettingsInd);
    CreateEventNew($TourId, 'TD', 'Traditioneel Dames', $i++, $SettingsInd);
    InsertClassEvent($TourId, 0, 1, 'TH', 'T', 'H');
    InsertClassEvent($TourId, 0, 1, 'TD', 'T', 'D');
    $SettingsTeam=array(
        'EvTeamEvent' => '1',
        'EvFinalFirstPhase' => '0',
        'EvFinalTargetType'=>TGT_FIELD,
        'EvTargetSize'=>0,
        'EvElimEnds'=>8,
        'EvElimArrows'=>3,
        'EvElimSO'=>3,
        'EvFinEnds'=>4,
        'EvFinArrows'=>3,
        'EvFinSO'=>3,
        'EvFinalAthTarget'=>15,
        'EvMatchArrowsNo'=>FINAL_FROM_2,
    );
    CreateEventNew($TourId, 'TT', 'Traditioneel Teams', 1, $SettingsTeam);
    InsertClassEvent($TourId, 1, 3, 'TT', 'T', 'D');
    InsertClassEvent($TourId, 1, 3, 'TT', 'T', 'H');
}

// Default Target
switch($TourType) {
    case 1:
        CreateTargetFace($TourId, 1, '~Default', '%', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 80, TGT_OUT_5_big10, 80);
        // optional target faces
        CreateTargetFace($TourId, 2, '~Option1', '%', '',  TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 80,  TGT_OUT_FULL, 80);
        CreateTargetFace($TourId, 3, '~Option2', '%', '',  TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_6_big10, 80, TGT_OUT_6_big10, 80);
        CreateTargetFace($TourId, 4, '~Option3', '%', '',  TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_5_big10, 80,  TGT_OUT_5_big10, 80);
        break;
    case 2:
        CreateTargetFace($TourId, 1, '~Default', '%', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 80, TGT_OUT_5_big10, 80, TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 80, TGT_OUT_5_big10, 80);
        // optional target faces
        CreateTargetFace($TourId, 2, '~Option1', '%', '',  TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 80,  TGT_OUT_FULL, 80, TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 80,  TGT_OUT_FULL, 80);
        CreateTargetFace($TourId, 3, '~Option2', '%', '',  TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_6_big10, 80, TGT_OUT_6_big10, 80, TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_6_big10, 80, TGT_OUT_6_big10, 80);
        CreateTargetFace($TourId, 4, '~Option3', '%', '',  TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_5_big10, 80,  TGT_OUT_5_big10, 80, TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_5_big10, 80,  TGT_OUT_5_big10, 80);
        break;

	case 3:
	    if($SubRule==1) {
	        CreateTargetFace($TourId, 1, '~Default', 'REG-^R|^B|^CA', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C', '1', TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
        } else {
            CreateTargetFace($TourId, 1, '~Default', '%', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
        }
		break;
    case 37:
        CreateTargetFace($TourId, 1, '~Default', 'REG-^R|^B|^CA', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
        CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C', '1', TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
        break;
	case 6:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', TGT_IND_6_big10, 40, TGT_IND_6_big10, 40);
		CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', TGT_IND_6_small10, 40, TGT_IND_6_small10, 40);
        // optional target faces
        CreateTargetFace($TourId, 3, '~Option1', 'REG-^R|^B|^LB|^IB', '',  TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);

        if($SubRule<=2) {
            CreateTargetFace($TourId, 4, 'Asprianten', 'REG-^[R|B|C|IB|LB][AU]', '1', TGT_IND_1_big10, 60);
        }
		break;
    case 7:
        CreateTargetFace($TourId, 1, '~Default', '%', '1', TGT_IND_6_big10, 60, TGT_IND_6_big10, 60);
        CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', TGT_IND_6_small10, 60, TGT_IND_6_small10, 60);
        // optional target faces
        CreateTargetFace($TourId, 3, '~Option1', 'REG-^R|^B|^LB|^IB', '',  TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);
        break;
    case 41:
        CreateTargetFace($TourId, 1, '~Default', '%', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
        break;
    case 42:
        CreateTargetFace($TourId, 1, '~Default', '%', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);
        CreateTargetFace($TourId, 2, '~DefaultCO', 'C%', '1', TGT_IND_6_small10, 60, TGT_IND_6_small10, 60);
        if($SubRule<=2) {
            CreateTargetFace($TourId, 4, 'Asprianten', 'REG-^[R|B|C|IB|LB][AU]', '1', TGT_IND_1_big10, 60);
        }
        break;
    case 43:
        CreateTargetFace($TourId, 1, '~Default', '%', '1', TGT_FIELD, 50);
        break;
}

if(($TourType==6 OR $TourType==42) AND $SubRule==1) {
    $tourDetNumDist=1;
    unset($DistanceInfoArray[1]);
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
//	'ToIocCode'	=> $tourDetIocCode,
	);
UpdateTourDetails($TourId, $tourDetails);

