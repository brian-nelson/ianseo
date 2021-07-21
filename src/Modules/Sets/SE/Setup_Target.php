<?php

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType);

// default SubClasses
CreateStandardSubClasses($TourId);

// default Classes
CreateStandardClasses($TourId, $SubRule, '', $TourType);

// default Distances
switch($TourType)
{
	case 1:
		CreateDistanceNew($TourId, $TourType, '_K_', array(array('20m-1',20), array('20m-2',20), array('20m-3',20), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'BC_', array(array('40m',40), array('30m-2',30), array('30m-3',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'LC_', array(array('40m',40), array('30m-2',30), array('30m-3',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'IC_', array(array('40m',40), array('30m-2',30), array('30m-3',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'RC_', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'CC_', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'LJ_', array(array('40m',40), array('30m-2',30), array('30m-3',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'IJ_', array(array('40m',40), array('30m-2',30), array('30m-3',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'BJ_', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'RJH', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'RJD', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'CJH', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'CJD', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'BV_', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'LV_', array(array('40m',40), array('30m-2',30), array('30m-3',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'IV_', array(array('40m',40), array('30m-2',30), array('30m-3',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'RV_', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'CV_', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));

		CreateDistanceNew($TourId, $TourType, 'BM_', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'LM_', array(array('40m',40), array('30m-2',30), array('30m-3',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'IM_', array(array('40m',40), array('30m-2',30), array('30m-3',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'RM_', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'CM_', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));

		CreateDistanceNew($TourId, $TourType, 'BS_', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'LS_', array(array('40m',40), array('30m-2',30), array('30m-3',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'IS_', array(array('40m',40), array('30m-2',30), array('30m-3',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'RS_', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'CS_', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'BE_', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'LE_', array(array('40m',40), array('30m-2',30), array('30m-3',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'IE_', array(array('40m',40), array('30m-2',30), array('30m-3',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'REH', array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'RED', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'CEH', array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'CED', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
		break;

	case 3:
		CreateDistanceNew($TourId, $TourType, '_K_', array(array('20m-1',20), array('20m-2',20)));
		CreateDistanceNew($TourId, $TourType, 'RC_', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'CC_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'BC_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'LC_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'IC_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'RJ_', array(array('60m-1',60), array('60m-2',60)));
		CreateDistanceNew($TourId, $TourType, 'CJ_', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'BJ_', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'LJ_', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'IJ_', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'RV_', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'CV_', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'BV_', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'LV_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'IV_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'RM_', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'CM_', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'BM_', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'LM_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'IM_', array(array('30m-1',30), array('30m-2',30)));

		CreateDistanceNew($TourId, $TourType, 'RS_', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'CS_', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'BS_', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'LS_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'IS_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'RE_', array(array('70m-1',70), array('70m-2',70)));
		CreateDistanceNew($TourId, $TourType, 'CE_', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'BE_', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'LE_', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'IE_', array(array('40m-1',40), array('40m-2',40)));
		break;

	case 37:  // Double 70m/50m round
		CreateDistanceNew($TourId, $TourType, '_K_', array(array('20m-1',20), array('20m-2',20), array('20m-1',20), array('20m-2',20)));
		CreateDistanceNew($TourId, $TourType, 'RC_', array(array('40m-1',40), array('40m-2',40), array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'CC_', array(array('30m-1',30), array('30m-2',30), array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'BC_', array(array('30m-1',30), array('30m-2',30), array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'LC_', array(array('30m-1',30), array('30m-2',30), array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'IC_', array(array('30m-1',30), array('30m-2',30), array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'RJ_', array(array('60m-1',60), array('60m-2',60), array('60m-1',60), array('60m-2',60)));
		CreateDistanceNew($TourId, $TourType, 'CJ_', array(array('50m-1',50), array('50m-2',50), array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'BJ_', array(array('40m-1',40), array('40m-2',40), array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'LJ_', array(array('40m-1',40), array('40m-2',40), array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'IJ_', array(array('40m-1',40), array('40m-2',40), array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'RV_', array(array('50m-1',50), array('50m-2',50), array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'CV_', array(array('40m-1',40), array('40m-2',40), array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'BV_', array(array('40m-1',40), array('40m-2',40), array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'LV_', array(array('30m-1',30), array('30m-2',30), array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'IV_', array(array('30m-1',30), array('30m-2',30), array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'RM_', array(array('50m-1',50), array('50m-2',50), array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'CM_', array(array('50m-1',50), array('50m-2',50), array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'BM_', array(array('40m-1',40), array('40m-2',40), array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'LM_', array(array('30m-1',30), array('30m-2',30), array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'IM_', array(array('30m-1',30), array('30m-2',30), array('30m-1',30), array('30m-2',30)));

		CreateDistanceNew($TourId, $TourType, 'RS_', array(array('50m-1',50), array('50m-2',50), array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'CS_', array(array('40m-1',40), array('40m-2',40), array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'BS_', array(array('40m-1',40), array('40m-2',40), array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'LS_', array(array('30m-1',30), array('30m-2',30), array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'IS_', array(array('30m-1',30), array('30m-2',30), array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'RE_', array(array('70m-1',70), array('70m-2',70), array('70m-1',70), array('70m-2',70)));
		CreateDistanceNew($TourId, $TourType, 'CE_', array(array('50m-1',50), array('50m-2',50), array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'BE_', array(array('50m-1',50), array('50m-2',50), array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'LE_', array(array('40m-1',40), array('40m-2',40), array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'IE_', array(array('40m-1',40), array('40m-2',40), array('40m-1',40), array('40m-2',40)));
		break;

	case 5:
		CreateDistanceNew($TourId, $TourType, '_K_', array(array('20m-1',20), array('20m-2',20), array('20m-3',20)));
		CreateDistanceNew($TourId, $TourType, '_C_', array(array('40m',40), array('30m',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'LJ_', array(array('40m',40), array('30m',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'LS_', array(array('40m',40), array('30m',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'LM_', array(array('40m',40), array('30m',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'LV_', array(array('40m',40), array('30m',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'IJ_', array(array('40m',40), array('30m',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'IS_', array(array('40m',40), array('30m',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'IM_', array(array('40m',40), array('30m',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'IV_', array(array('40m',40), array('30m',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'BJ_', array(array('40m',40), array('30m',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'RJ_', array(array('60m',60), array('50m',50), array('40m',40)));
		CreateDistanceNew($TourId, $TourType, 'CJ_', array(array('60m',60), array('50m',50), array('40m',40)));
		CreateDistanceNew($TourId, $TourType, 'RV_', array(array('60m',60), array('50m',50), array('40m',40)));
		CreateDistanceNew($TourId, $TourType, 'CV_', array(array('60m',60), array('50m',50), array('40m',40)));
		CreateDistanceNew($TourId, $TourType, 'BV_', array(array('60m',60), array('50m',50), array('40m',40)));
		CreateDistanceNew($TourId, $TourType, 'RM_', array(array('60m',60), array('50m',50), array('40m',40)));
		CreateDistanceNew($TourId, $TourType, 'CM_', array(array('60m',60), array('50m',50), array('40m',40)));
		CreateDistanceNew($TourId, $TourType, 'BM_', array(array('60m',60), array('50m',50), array('40m',40)));
		CreateDistanceNew($TourId, $TourType, 'RS_', array(array('60m',60), array('50m',50), array('40m',40)));
		CreateDistanceNew($TourId, $TourType, 'CS_', array(array('60m',60), array('50m',50), array('40m',40)));
		CreateDistanceNew($TourId, $TourType, 'BS_', array(array('60m',60), array('50m',50), array('40m',40)));
		CreateDistanceNew($TourId, $TourType, 'RE_', array(array('60m',60), array('50m',50), array('40m',40)));
		CreateDistanceNew($TourId, $TourType, 'BE_', array(array('60m',60), array('50m',50), array('40m',40)));
		CreateDistanceNew($TourId, $TourType, 'CE_', array(array('60m',60), array('50m',50), array('40m',40)));
		CreateDistanceNew($TourId, $TourType, 'LE_', array(array('40m',40), array('30m',30), array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'IE_', array(array('40m',40), array('30m',30), array('20m',20)));
		break;

	case 6:
	case 22:
		CreateDistanceNew($TourId, $TourType, '_K_', array(array('12m-1',12), array('12m-2',12)));
		CreateDistanceNew($TourId, $TourType, '_C_', array(array('12m-1',12), array('12m-2',12)));
		CreateDistanceNew($TourId, $TourType, '_J_', array(array('18m-1',18), array('18m-2',18)));
		CreateDistanceNew($TourId, $TourType, '_S_', array(array('18m-1',18), array('18m-2',18)));
		CreateDistanceNew($TourId, $TourType, '_M_', array(array('18m-1',18), array('18m-2',18)));
		CreateDistanceNew($TourId, $TourType, '_V_', array(array('18m-1',18), array('18m-2',18)));
		CreateDistanceNew($TourId, $TourType, '_E_', array(array('18m-1',18), array('18m-2',18)));
		break;

	case 7:
		CreateDistanceNew($TourId, $TourType, '_K_', array(array('12m-1',12), array('12m-2',12)));
		CreateDistanceNew($TourId, $TourType, '_C_', array(array('12m-1',12), array('12m-2',12)));
		CreateDistanceNew($TourId, $TourType, '_J_', array(array('25m-1',25), array('25m-2',25)));
		CreateDistanceNew($TourId, $TourType, '_S_', array(array('25m-1',25), array('25m-2',25)));
		CreateDistanceNew($TourId, $TourType, '_M_', array(array('25m-1',25), array('25m-2',25)));
		CreateDistanceNew($TourId, $TourType, '_V_', array(array('25m-1',25), array('25m-2',25)));
		CreateDistanceNew($TourId, $TourType, '_E_', array(array('25m-1',25), array('25m-2',25)));
		break;

	case 39:
		CreateDistanceNew($TourId, $TourType, 'CE_', array(array('50m-1',50)));
		CreateDistanceNew($TourId, $TourType, 'CJH', array(array('50m-1',50)));
		CreateDistanceNew($TourId, $TourType, 'CS_', array(array('40m-1',40)));
		CreateDistanceNew($TourId, $TourType, 'CM_', array(array('40m-1',40)));
		CreateDistanceNew($TourId, $TourType, 'CV_', array(array('40m-1',40)));
		CreateDistanceNew($TourId, $TourType, 'CJD', array(array('40m-1',40)));
		CreateDistanceNew($TourId, $TourType, 'RE_', array(array('30m-1',30)));
		CreateDistanceNew($TourId, $TourType, 'BE_', array(array('30m-1',30)));
		CreateDistanceNew($TourId, $TourType, 'RJ_', array(array('30m-1',30)));
		CreateDistanceNew($TourId, $TourType, 'RS_', array(array('30m-1',30)));
		CreateDistanceNew($TourId, $TourType, 'RM_', array(array('30m-1',30)));
		CreateDistanceNew($TourId, $TourType, 'RV_', array(array('30m-1',30)));
		CreateDistanceNew($TourId, $TourType, 'CC_', array(array('30m-1',30)));
		CreateDistanceNew($TourId, $TourType, 'RC_', array(array('20m-1',20)));
		CreateDistanceNew($TourId, $TourType, 'BC_', array(array('20m-1',20)));
		CreateDistanceNew($TourId, $TourType, 'BJ_', array(array('20m-1',20)));
		CreateDistanceNew($TourId, $TourType, 'BS_', array(array('20m-1',20)));
		CreateDistanceNew($TourId, $TourType, 'BM_', array(array('20m-1',20)));
		CreateDistanceNew($TourId, $TourType, 'BV_', array(array('20m-1',20)));
		CreateDistanceNew($TourId, $TourType, 'IE_', array(array('20m-1',20)));
		CreateDistanceNew($TourId, $TourType, 'IC_', array(array('20m-1',20)));
		CreateDistanceNew($TourId, $TourType, 'IJ_', array(array('20m-1',20)));
		CreateDistanceNew($TourId, $TourType, 'IS_', array(array('20m-1',20)));
		CreateDistanceNew($TourId, $TourType, 'IM_', array(array('20m-1',20)));
		CreateDistanceNew($TourId, $TourType, 'IV_', array(array('20m-1',20)));
		CreateDistanceNew($TourId, $TourType, 'LE_', array(array('20m-1',20)));
		CreateDistanceNew($TourId, $TourType, 'LC_', array(array('20m-1',20)));
		CreateDistanceNew($TourId, $TourType, 'LJ_', array(array('20m-1',20)));
		CreateDistanceNew($TourId, $TourType, 'LS_', array(array('20m-1',20)));
		CreateDistanceNew($TourId, $TourType, 'LM_', array(array('20m-1',20)));
		CreateDistanceNew($TourId, $TourType, 'LV_', array(array('20m-1',20)));
		CreateDistanceNew($TourId, $TourType, '_K_', array(array('20m-1',20)));
		break;
}

if($TourType==6 || $TourType==3 || $TourType==37 || $TourType==1) {
	// default Events
	CreateStandardEvents($TourId, $TourType, $SubRule, $TourType!=6);

	// Classes in Events
	InsertStandardEvents($TourId, $TourType, $SubRule, $TourType!=6);

	// Finals & TeamFinals
	CreateFinals($TourId);
}

// Default Target
$i=1;
switch($TourType)
{
	case 1:  // Full FITA
		CreateTargetFace($TourId, $i++, '10 ring 122cm', '%K_', '1', 5, 122, 5, 122, 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '10 ring 122/80cm', 'REG-^[RBLI]{1,1}[CJESMV]{1,1}[HD]{1,1}', '1',  5, 122, 5, 122, 5, 80, 5, 80);
		CreateTargetFace($TourId, $i++, '10 ring 122/80cm', 'REG-^[C]{1,1}[CJESMV]{1,1}[HD]{1,1}', '1', 5, 122, 5, 122, 9, 80, 9, 80);
		break;

	case 3:  // 70m/50m round
		CreateTargetFace($TourId, $i++, '10 ring 122cm', '_K_', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '10 ring 122cm', 'REG-^[RBLI]{1,1}[CJESMV]{1,1}[HD]{1,1}', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '6 ring 80cm',   'REG-^[C]{1,1}[CJESMV]{1,1}[HD]{1,1}', '1', 9, 80, 9, 80);
		break;

    case 37: // Double 70m/50m round
        CreateTargetFace($TourId, $i++, '10 ring 122cm', '_K_', '1', 5, 122, 5, 122, 5, 122, 5, 122);
        CreateTargetFace($TourId, $i++, '10 ring 122cm', 'REG-^[RBLI]{1,1}[CJESMV]{1,1}[HD]{1,1}', '1', 5, 122, 5, 122, 5, 122, 5, 122);
        CreateTargetFace($TourId, $i++, '6 ring 80cm',   'REG-^[C]{1,1}[CJESMV]{1,1}[HD]{1,1}', '1', 9, 80, 9, 80, 9, 80, 9, 80);
        break;

	case 5:  // 900 round
		CreateTargetFace($TourId, $i++, '10 ring 122cm', '%', '1', 5, 122, 5, 122, 5, 122);
		break;

	case 6:  // Indoor 18m, 2 Dist - 60 arrows
		// KS 2015-11-16
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'REG-^[CRBLI]{1,1}[K]{1,1}[HD]{1,1}', '1', 1, 60, 1, 60);

		CreateTargetFace($TourId, $i++, '3 spot', 'R%', '1', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '3 spot', 'C%', '1', 4, 40, 4, 40);
		CreateTargetFace($TourId, $i++, '10 ring 40cm', 'B%', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'L%', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'I%', '1', 1, 60, 1, 60);

		CreateTargetFace($TourId, $i++, '10 ring 40cm', 'R%', '0', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '10 ring 40cm', 'C%', '0', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '3 spot', 'B%', '0', 2, 40, 2, 40);
		break;

	case 7:  // Indoor 25m, 2 Dist - 60 arrows
		// KS 2015-11-16
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'REG-^[CRBLI]{1,1}[K]{1,1}[HD]{1,1}', '1', 1, 60, 1, 60);

		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'R%', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '3 spot', 'R%', '0', 2, 60, 2, 60);
		CreateTargetFace($TourId, $i++, '3 spot', 'C%', '1', 4, 60, 4, 60);
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'C%', '0', 3, 60, 3, 60);
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'B%', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '3 spot', 'B%', '0', 2, 60, 2, 60);
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'L%', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'I%', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '10 ring 40cm', 'REG-^[CRB]{1,1}[C]{1,1}[HD]{1,1}', '1', 1, 40, 1, 40);

		break;

	case 22:  // Indoor 18m, 1 Dist - 30 arrows
		// KS 2015-11-16
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'REG-^[CRBLI]{1,1}[K]{1,1}[HD]{1,1}', '1', 1, 60);

		CreateTargetFace($TourId, $i++, '3 spot', 'R%', '1', 2, 40);
		CreateTargetFace($TourId, $i++, '3 spot', 'C%', '1', 4, 40);
		CreateTargetFace($TourId, $i++, '10 ring 40cm', 'B%', '1', 1, 40);
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'L%', '1', 1, 60);
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'I%', '1', 1, 60);
		CreateTargetFace($TourId, $i++, '10 ring 40cm', 'R%', '0', 1, 40);
		CreateTargetFace($TourId, $i++, '10 ring 40cm', 'C%', '0', 3, 40);
		CreateTargetFace($TourId, $i++, '3 spot', 'B%', '0', 2, 40);
		break;

	case 39:  // SBF36 round, 1 Dist
		// KS 2018-05-21
		CreateTargetFace($TourId, $i++, '10 ring 122cm', '_K_', '1', 5, 122);
		CreateTargetFace($TourId, $i++, '10 ring 80cm', 'REG-^[RBLI]{1,1}[CJESMV]{1,1}[HD]{1,1}', '1', 5, 80);
		CreateTargetFace($TourId, $i++, '6 ring 80cm',   'REG-^[RBLI]{1,1}[CJESMV]{1,1}[HD]{1,1}', '0', 9, 80);
		CreateTargetFace($TourId, $i++, '6 ring 80cm',   'REG-^[C]{1,1}[CJESMV]{1,1}[HD]{1,1}', '1', 9, 80);
		CreateTargetFace($TourId, $i++, '10 ring 80cm', 'REG-^[C]{1,1}[CJESMV]{1,1}[HD]{1,1}', '0', 5, 80);
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
