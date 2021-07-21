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

// default Classes
CreateStandardClasses($TourId, $SubRule, '', $TourType);

// default Distances
switch($TourType) {
	case 1:  // FITA - 4 Distances
		CreateDistanceNew($TourId, $TourType, 'RH',   array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_MO',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GRH',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'CH',   array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GCH',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'RJH',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GRJH', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'CJH',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GCJH', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));

		CreateDistanceNew($TourId, $TourType, 'RD',   array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_WO',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GRD',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'CD',   array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GCD',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'RJD',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GRJD', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'CJD',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GCJD', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'RCH',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GRCH', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'CCH',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GCCH', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'RVH',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GRVH', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'CVH',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GCVH', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));

		CreateDistanceNew($TourId, $TourType, 'RCD',  array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GRCD', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'CCD',  array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GCCD', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'RVD',  array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GRVD', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'CVD',  array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GCVD', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));

		CreateDistanceNew($TourId, $TourType, 'RJE',  array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
		CreateDistanceNew($TourId, $TourType, 'GRJE', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
		CreateDistanceNew($TourId, $TourType, 'CJE',  array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
		CreateDistanceNew($TourId, $TourType, 'GCJE', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));

		CreateDistanceNew($TourId, $TourType, 'BB_',  array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'BH_',  array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'LB_',  array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GB_',  array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GI_',  array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'BBJH', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'BBJD', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'BHJH', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'BHJD', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'LBJH', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'LBJD', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GBJH', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GBJD', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GIJH', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GIJD', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'BBC_', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'BHC_', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'LBC_', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GBC_', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GIC_', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));

		CreateDistanceNew($TourId, $TourType, 'BBJE', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'BHJE', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'LBJE', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GBJE', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GIJE', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'BBV_', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'BHV_', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'LBV_', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GBV_', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GIV_', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '%MI',  array(array('-',0), array('-',0), array('25 m',25), array('20 m',20)));
		CreateDistanceNew($TourId, $TourType, '%PI',  array(array('-',0), array('-',0), array('20 m',20), array('15 m',15)));
		break;
	case 2:  // 2 x FITA - 8 Distances
		CreateDistanceNew($TourId, $TourType, 'RH',   array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_MO',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GRH',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'CH',   array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GCH',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'RJH',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GRJH', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'CJH',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GCJH', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));

		CreateDistanceNew($TourId, $TourType, 'RD',   array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_WO',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GRD',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'CD',   array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GCD',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'RJD',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GRJD', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'CJD',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GCJD', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'RCH',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GRCH', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'CCH',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GCCH', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'RVH',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GRVH', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'CVH',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GCVH', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));

		CreateDistanceNew($TourId, $TourType, 'RCD',  array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GRCD', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'CCD',  array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GCCD', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'RVD',  array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GRVD', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'CVD',  array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GCVD', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));

		CreateDistanceNew($TourId, $TourType, 'RJE',  array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20), array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
		CreateDistanceNew($TourId, $TourType, 'GRJE', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20), array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
		CreateDistanceNew($TourId, $TourType, 'CJE',  array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20), array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
		CreateDistanceNew($TourId, $TourType, 'GCJE', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20), array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));

		CreateDistanceNew($TourId, $TourType, 'BB_',  array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'BH_',  array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'LB_',  array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GB_',  array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GI_',  array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'BBJH', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'BBJD', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'BHJH', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'BHJD', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'LBJH', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'LBJD', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GBJH', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GBJD', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GIJH', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GIJD', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'BBC_', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'BHC_', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'LBC_', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GBC_', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GIC_', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));

		CreateDistanceNew($TourId, $TourType, 'BBJE', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'BHJE', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'LBJE', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GBJE', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GIJE', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'BBV_', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'BHV_', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'LBV_', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GBV_', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'GIV_', array(array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30), array('40 m',40), array('40 m',40), array('30 m',30), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '%MI',  array(array('-',0), array('-',0), array('25 m',25), array('20 m',20), array('-',0), array('-',0), array('25 m',25), array('20 m',20)));
		CreateDistanceNew($TourId, $TourType, '%PI',  array(array('-',0), array('-',0), array('20 m',20), array('15 m',15), array('-',0), array('-',0), array('20 m',20), array('15 m',15)));
		break;
	case 3:  // 70m/50m Round - 2 Distances
		CreateDistanceNew($TourId, $TourType, '%PI',  array(array('15m-1',15), array('15m-2',15)));
		CreateDistanceNew($TourId, $TourType, '%MI',  array(array('25m-1',25), array('25m-2',25)));

		CreateDistanceNew($TourId, $TourType, 'BHV_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'LBV_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'GIV_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'BH_',  array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'LB_',  array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'GI_',  array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'BHJ_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'LBJ_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'GIJ_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'BHC_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'LBC_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'GIC_', array(array('30m-1',30), array('30m-2',30)));

		// Recurve, Compound & Barebow Jeunesse
		CreateDistanceNew($TourId, $TourType, 'RJE',  array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'CJE',  array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'GCJE', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'GRJE', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'BBJE', array(array('40m-1',40), array('40m-2',40))); // Barebow JE
		CreateDistanceNew($TourId, $TourType, 'GBJE', array(array('40m-1',40), array('40m-2',40))); // Guest Barebow JE

		CreateDistanceNew($TourId, $TourType, 'CV_',  array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'C_',   array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'CJH',  array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'CJD',  array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'CC_',  array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'C_O',  array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'GCV_', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'GC_',  array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'GCJH', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'GCJD', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'GCC_', array(array('50m-1',50), array('50m-2',50)));

		// Barebow U18 - Master (incl. Guests)
		CreateDistanceNew($TourId, $TourType, 'BBV_', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'GBV_', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'BB_', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'GB_', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'BBJH', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'BBJD', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'GBJH', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'GBJD', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'BBC_', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'GBC_', array(array('50m-1',50), array('50m-2',50)));

		CreateDistanceNew($TourId, $TourType, 'RV_',  array(array('60m-1',60), array('60m-2',60)));
		CreateDistanceNew($TourId, $TourType, 'RC_',  array(array('60m-1',60), array('60m-2',60)));
		CreateDistanceNew($TourId, $TourType, 'GRV_', array(array('60m-1',60), array('60m-2',60)));
		CreateDistanceNew($TourId, $TourType, 'GRC_', array(array('60m-1',60), array('60m-2',60)));

		CreateDistanceNew($TourId, $TourType, 'R_',   array(array('70m-1',70), array('70m-2',70)));
		CreateDistanceNew($TourId, $TourType, 'RJH',  array(array('70m-1',70), array('70m-2',70)));
		CreateDistanceNew($TourId, $TourType, 'RJD',  array(array('70m-1',70), array('70m-2',70)));
		CreateDistanceNew($TourId, $TourType, 'R_O',  array(array('70m-1',70), array('70m-2',70)));
		CreateDistanceNew($TourId, $TourType, 'GR_',  array(array('70m-1',70), array('70m-2',70)));
		CreateDistanceNew($TourId, $TourType, 'GRJH', array(array('70m-1',70), array('70m-2',70)));
		CreateDistanceNew($TourId, $TourType, 'GRJD', array(array('70m-1',70), array('70m-2',70)));
		break;
	case 6:  // Indoor 18 m - 2 Distances
		CreateDistanceNew($TourId, $TourType, '%D', array(array('18m-1',18), array('18m-2',18)));
		CreateDistanceNew($TourId, $TourType, '%H', array(array('18m-1',18), array('18m-2',18)));
		CreateDistanceNew($TourId, $TourType, 'R_O', array(array('18m-1',18), array('18m-2',18)));
		CreateDistanceNew($TourId, $TourType, 'C_O', array(array('18m-1',18), array('18m-2',18)));
		CreateDistanceNew($TourId, $TourType, '%JE', array(array('18m-1',18), array('18m-2',18)));
		CreateDistanceNew($TourId, $TourType, '%MI', array(array('18m-1',18), array('18m-2',18)));
		CreateDistanceNew($TourId, $TourType, '%PI', array(array('15m-1',15), array('15m-2',15)));
		break;
	case 7:  // Indoor 25 m - 2 Distances
		CreateDistanceNew($TourId, $TourType, '%D', array(array('25m-1',25), array('25m-2',25)));
		CreateDistanceNew($TourId, $TourType, '%H', array(array('25m-1',25), array('25m-2',25)));
		CreateDistanceNew($TourId, $TourType, 'R_O', array(array('25m-1',25), array('25m-2',25)));
		CreateDistanceNew($TourId, $TourType, 'C_O', array(array('25m-1',25), array('25m-2',25)));
		CreateDistanceNew($TourId, $TourType, '%JE', array(array('25m-1',25), array('25m-2',25)));
		CreateDistanceNew($TourId, $TourType, '%MI', array(array('25m-1',25), array('25m-2',25)));
		CreateDistanceNew($TourId, $TourType, '%PI', array(array('18m-1',18), array('18m-2',18)));
		break;
	case 9:   // Field Archery
	case 11:  // 3D
	case 13:
		if($tourDetNumDist==2)
			CreateDistanceNew($TourId, $TourType, '%', array(array('Course 1',0), array('Course 2',0)));
		else
			CreateDistanceNew($TourId, $TourType, '%', array(array('Course',0)));
		break;
}

/* Ramon Keller 11.05.2017: Added TourType==7 for Indoor 25m */
if($TourType==3 or $TourType==6 or $TourType==7) {
	// default Events
	CreateStandardEvents($TourId, $TourType, $SubRule, $TourType!=6);

	// Classes in Events
	InsertStandardEvents($TourId, $TourType, $SubRule, $TourType!=6);

	// Finals & TeamFinals
	CreateFinals($TourId);
}

// Default Target
$i=1;
switch($TourType) {
	case 1:  // FITA - 4 Distances
		CreateTargetFace($TourId, $i++, '122cm(1-x) / 80cm(1-x)',  'REG-^(R|GR)[CJV]{0,1}[HD]{1,1}', '1', 5, 122, 5, 122, 5, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '122cm(1-x) / 80cm(1-x)',  'REG-^(R)[MW]{1,1}[O]{1,1}', '1', 5, 122, 5, 122, 5, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '122cm(1-x) / 80cm(5-x)',  'REG-^(C|GC)[CJV]{0,1}[HD]{1,1}', '1', 5, 122, 5, 122, 9, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '122cm(1-x) / 80cm(5-x)',  'REG-^(C)[MW]{1,1}[O]{1,1}', '1', 5, 122, 5, 122, 9, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '122cm(1-x) / 122cm(1-x)', 'REG-^((LB)|(BB)|(BH)|GB|GI)(JE){1,1}', '1', 5, 122, 5, 122, 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++,  '80cm(1-x) / 80cm(1-x)',  'REG-^(BB|GB)[CJV]{0,1}[HD]{1,1}', '1', 5, 80, 5, 80, 5, 80, 5, 80);
		CreateTargetFace($TourId, $i++, '122cm(1-x) / 122cm(1-x)', 'REG-^((LB)|(BH)|GI)[CJV]{0,1}[HD]{1,1}', '1', 5, 122, 5, 122, 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '122cm(1-x) / 80cm(1-x)',  'REG-^(R|C|GR|GC)(JE)$', '1', 5, 122, 5, 122, 5, 80, 5, 80);
		CreateTargetFace($TourId, $i++,  '80cm(1-x) / 80cm(1-x)',  'REG-(MI|PI)$', '1', 5, 80, 5, 80, 5, 80, 5, 80);

	/*
		CreateTargetFace($TourId, $i++, '~Default', 'REG-^(R)[ECJV]{0,1}[HD]{1,1}', '1', 5, 122, 5, 122, 5, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '~Default', 'REG-^(C)[ECJV]{0,1}[HD]{1,1}', '1', 5, 122, 5, 122, 9, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '~Default', 'REG-^((LB)|(BB)|(BH))[E][HD]{1,1}', '1', 5, 122, 5, 122, 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '~Default', 'REG-^(BB)[CJV]{0,1}[HD]{1,1}', '1', 5, 80, 5, 80, 5, 80, 5, 80);
		CreateTargetFace($TourId, $i++, '~Default', 'REG-^((LB)|(BH))[CJV]{0,1}[HD]{1,1}', '1', 5, 122, 5, 122, 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '~Default', 'REG-(MI|PI)$', '1', 5, 80, 5, 80, 5, 80, 5, 80);
	*/
		break;
	case 2:  // 2 x FITA - 8 Distances
		CreateTargetFace($TourId, $i++, '122cm(1-x) / 80cm(1-x)',  'REG-^(R|GR)[CJV]{0,1}[HD]{1,1}', '1', 5, 122, 5, 122, 5, 80, 9, 80, 5, 122, 5, 122, 5, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '122cm(1-x) / 80cm(1-x)',  'REG-^(R)[MW]{1,1}[O]{1,1}', '1', 5, 122, 5, 122, 5, 80, 9, 80, 5, 122, 5, 122, 5, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '122cm(1-x) / 80cm(5-x)',  'REG-^(C|GC)[CJV]{0,1}[HD]{1,1}', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '122cm(1-x) / 80cm(5-x)',  'REG-^(C)[MW]{1,1}[O]{1,1}', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '122cm(1-x) / 122cm(1-x)', 'REG-^((LB)|(BB)|(BH)|GB|GI)(JE){1,1}', '1', 5, 122, 5, 122, 5, 122, 5, 122, 5, 122, 5, 122, 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++,  '80cm(1-x) / 80cm(1-x)',  'REG-^(BB|GB)[CJV]{0,1}[HD]{1,1}', '1', 5, 80, 5, 80, 5, 80, 5, 80, 5, 80, 5, 80, 5, 80, 5, 80);
		CreateTargetFace($TourId, $i++, '122cm(1-x) / 122cm(1-x)', 'REG-^((LB)|(BH)|GI)[CJV]{0,1}[HD]{1,1}', '1', 5, 122, 5, 122, 5, 122, 5, 122, 5, 122, 5, 122, 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '122cm(1-x) / 80cm(1-x)',  'REG-^(R|C|GR|GC)(JE)$', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
		CreateTargetFace($TourId, $i++,  '80cm(1-x) / 80cm(1-x)',  'REG-(MI|PI)$', '1', 5, 80, 5, 80, 5, 80, 5, 80, 5, 80, 5, 80, 5, 80, 5, 80);

	/*
		CreateTargetFace($TourId, $i++, '~Default', 'REG-^(R)[ECJV]{0,1}[HD]{1,1}', '1', 5, 122, 5, 122, 5, 80, 9, 80, 5, 122, 5, 122, 5, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '~Default', 'REG-^(C)[ECJV]{0,1}[HD]{1,1}', '1', 5, 122, 5, 122, 9, 80, 9, 80, 5, 122, 5, 122, 9, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '~Default', 'REG-^((LB)|(BB)|(BH))[E][HD]{1,1}', '1', 5, 122, 5, 122, 5, 122, 5, 122, 5, 122, 5, 122, 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '~Default', 'REG-^(BB)[CJV]{0,1}[HD]{1,1}', '1', 5, 80, 5, 80, 5, 80, 5, 80, 5, 80, 5, 80, 5, 80, 5, 80);
		CreateTargetFace($TourId, $i++, '~Default', 'REG-^((LB)|(BH))[CJV]{0,1}[HD]{1,1}', '1', 5, 122, 5, 122, 5, 122, 5, 122, 5, 122, 5, 122, 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '~Default', 'REG-(MI|PI)$', '1', 5, 80, 5, 80, 5, 80, 5, 80, 5, 80, 5, 80, 5, 80, 5, 80);
	*/
		break;
	case 3:  // 70m/50m Round - 2 Distances
		CreateTargetFace($TourId, $i++, '122cm (1-x)', 'REG-^((R)|(LB)|(BH)|GR|GI)[CJV]{0,1}[HD]{1,1}', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '122cm (1-x)', 'REG-^(R)[MW]{1,1}[O]{1,1}', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '122cm (1-x)', 'REG-^(BB|GB)[CJV]{0,1}[HD]{1,1}', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '80cm (5-x)', 'REG-^(C|GC)[CJV]{0,1}[HD]{1,1}', '1', 9, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '80cm (5-x)', 'REG-^(C)[MW]{1,1}[O]{1,1}', '1', 9, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '122cm (1-x)', 'REG-(JE){1,1}$', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '80cm (1-x)', 'REG-(MI|PI)$', '1', 5, 80, 5, 80);
		break;
	case 6:  // Indoor 18 m - 2 Distances
		CreateTargetFace($TourId, $i++, '40cm (1-big 10)', '%', '1', 1, 40, 1, 40);  // big 10
		CreateTargetFace($TourId, $i++, 'Trispot Comp 40cm', 'REG-^(C|GC)(D|C|H|V|JD|JH|MO|WO)', '1', 4, 40, 4, 40);  // small 10
		CreateTargetFace($TourId, $i++, '60cm (1-big 10)', 'REG-((^R|^BB|^GR|^GB|^GI)JE)|(^LB|^BH|^GI).*[^I]$', '1', 1, 60, 1, 60);  // big 10
		CreateTargetFace($TourId, $i++, 'Trispot Comp 60cm', 'REG-(^C|^GC)JE', '1', 4, 60, 4, 60);  // small 10
		CreateTargetFace($TourId, $i++, '80cm (1-small 10)', 'REG-(^C|^GC).I$', '1', 3, 80, 3, 80);  // small 10
		CreateTargetFace($TourId, $i++, '80cm (1-big 10)', 'REG-(^R|^BB|^BH|^LB|^GR|^GB|^GI).I$', '1', 1, 80, 1, 80);  // big 10
		// optional target faces
		CreateTargetFace($TourId, $i++, 'Trispot Rec 40cm', 'REG-^(GR|R)(D|C|H|V|JD|JH|MO|WO)', '',  2, 40, 2, 40);  // big 10
		CreateTargetFace($TourId, $i++, 'Trispot Rec 40cm', 'REG-^(BB|GB)[CJV]{0,1}[HD]{1,1}', '',  2, 40, 2, 40);  // big 10
		CreateTargetFace($TourId, $i++, 'Trispot Rec 60cm', 'REG-(^R|^GR)JE', '', 2, 60, 2, 60);  // big 10

	/* Previous configuration
		CreateTargetFace($TourId, $i++, 'Standard 40cm', '%', '1', 1, 40, 1, 40); // big 10
		CreateTargetFace($TourId, $i++, 'Trispot Comp 40cm', 'REG-^C[HDJVC]', '1', 4, 40, 4, 40);  // small 10
		CreateTargetFace($TourId, $i++, 'Standard 60cm', 'REG-(^BH|^L|^RE|^BBE).*[^I]$', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, 'Standard 60cm', 'CE%', '1', 3, 60, 3, 60);
		CreateTargetFace($TourId, $i++, 'Standard 80cm', 'REG-^C.I$', '1', 3, 80, 3, 80);
		CreateTargetFace($TourId, $i++, 'Standard 80cm', 'REG-^[^C].+I', '1', 1, 80, 1, 80);
		// optional target faces
		CreateTargetFace($TourId, $i++, 'Trispot Rec 40cm', 'REG-^R[HDJVC]', '',  2, 40, 2, 40);
	*/
		break;
	case 7:  // Indoor 25 m - 2 Distances
		/* Ramon Keller 11.05.2017: Newly introduced to CH: 25m Indoor */
		CreateTargetFace($TourId, $i++, '60cm (1-big 10)', '%', '1', 1, 60, 1, 60);  // big 10
		CreateTargetFace($TourId, $i++, 'Trispot Comp 60cm', 'REG-^(C|GC)(D|C|H|V|JD|JH|MO|WO)', '1', 4, 60, 4, 60);  // small 10
		CreateTargetFace($TourId, $i++, '80cm (1-big 10)', 'REG-(^(R|BB|LB|BH|GR|GB|GI)(JE|MI)$)|(^(LB|BH|GB|GI).*[^(MI)])$', '1', 1, 80, 1, 80);  // big 10
		CreateTargetFace($TourId, $i++, '80cm (1-small 10)', 'REG-(^C|^GC)(JE|MI)$', '1', 3, 80, 3, 80);  // small 10
		CreateTargetFace($TourId, $i++, '122cm (1-small 10)', 'REG-(^C|^GC)PI$', '1', 3, 122, 3, 122);  // small 10
		CreateTargetFace($TourId, $i++, '80cm (1-big 10)', 'REG-(^R|^BB|^BH|^LB|^GR|^GB|^GI)PI$', '1', 1, 122, 1, 122);  // big 10
		// optional target faces
		CreateTargetFace($TourId, $i++, 'Trispot Rec 60cm', 'REG-^(GR|R)(D|C|H|V|JD|JH|MO|WO)', '',  2, 60, 2, 60);  // big 10
		break;
	case 9:  // Field Archery
		CreateTargetFace($TourId, $i++, 'Rot / Rouge', 'REG-^((R)|(C)|(GR)|(GC))[JV]{0,1}[HD]{1,1}', '1', 6, 0, ($tourDetNumDist==2 ? 6 : 0), 0);
		CreateTargetFace($TourId, $i++, 'Blau / Bleu', 'REG-^((BB)|(BH)|(GB))[JV]{0,1}[HD]{1,1}', '1', 6, 0, ($tourDetNumDist==2 ? 6 : 0), 0);
		CreateTargetFace($TourId, $i++, 'Blau / Bleu', 'REG-((R)|(C))[C][HD]{1,1}$', '1', 6, 0, ($tourDetNumDist==2 ? 6 : 0), 0);
		CreateTargetFace($TourId, $i++, 'Gelb / Jaune', 'REG-^(LB|GI)[JV]{0,1}[HD]{1,1}', '1', 6, 0, ($tourDetNumDist==2 ? 6 : 0), 0);
		CreateTargetFace($TourId, $i++, 'Gelb / Jaune', 'REG-((BB)|(LB)|(BH)|(GB)|GI)[C][HD]{1,1}$', '1', 6, 0, ($tourDetNumDist==2 ? 6 : 0), 0);
		//CreateTargetFace($TourId, $i++, 'Gelb / Jaune', 'REG-[CR](E)[HD]{1,1}$', '1', 6, 0, ($tourDetNumDist==2 ? 6 : 0), 0);
		//CreateTargetFace($TourId, $i++, 'Grün / Vert', 'REG-(E)[HD]{1,1}$', '1', 6, 0, ($tourDetNumDist==2 ? 6 : 0), 0);
		//CreateTargetFace($TourId, $i++, 'Grün / Vert', 'REG-[^CR](E)[HD]{1,1}$', '1', 6, 0, ($tourDetNumDist==2 ? 6 : 0), 0);
		CreateTargetFace($TourId, $i++, 'Gelb / Jaune', 'REG-^(C|R|GC|GR)(JE){1,1}$', '1', 6, 0, ($tourDetNumDist==2 ? 6 : 0), 0);
		//CreateTargetFace($TourId, $i++, 'Grün / Vert', 'REG-^(BB|BH|LB|GB)(JE){1,1}$', '1', 6, 0, ($tourDetNumDist==2 ? 6 : 0), 0);
		CreateTargetFace($TourId, $i++, 'Grün / Vert', 'REG-[^CR](JE){1,1}$', '1', 6, 0, ($tourDetNumDist==2 ? 6 : 0), 0);
		CreateTargetFace($TourId, $i++, 'Grün / Vert', 'REG-(MI|PI)$', '1', 6, 0, ($tourDetNumDist==2 ? 6 : 0), 0);
		break;
	case 11:  // 3D
	case 13:
		CreateTargetFace($TourId, $i++, 'Rot / Rouge', 'REG-^((R)|(C)|(GR)|(GC))[CJV]{0,1}[HD]{1,1}', '1', 8, 0, ($tourDetNumDist==2 ? 8 : 0), 0);
		CreateTargetFace($TourId, $i++, 'Blau / Bleu', 'REG-^((LB)|(BB)|(BH)|(GB)|GI)[CJV]{0,1}[HD]{1,1}', '1', 8, 0, ($tourDetNumDist==2 ? 8 : 0), 0);
		CreateTargetFace($TourId, $i++, 'Blau / Bleu', 'REG-[CR](JE)$', '1', 8, 0, ($tourDetNumDist==2 ? 8 : 0), 0);
		CreateTargetFace($TourId, $i++, 'Grün / Vert', 'REG-[^CR](JE)$', '1', 8, 0, ($tourDetNumDist==2 ? 8 : 0), 0);
		CreateTargetFace($TourId, $i++, 'Grün / Vert', 'REG-(MI|PI)$', '1', 8, 0, ($tourDetNumDist==2 ? 8 : 0), 0);

	/*
		CreateTargetFace($TourId, $i++, 'Rot / Rouge', 'REG-^((R)|(C))[CJV]{0,1}[HD]{1,1}', '1', 8, 0, ($tourDetNumDist==2 ? 8 : 0), 0);
		CreateTargetFace($TourId, $i++, 'Blau / Bleu', 'REG-^((LB)|(BB)|(BH))[CJV]{0,1}[HD]{1,1}', '1', 8, 0, ($tourDetNumDist==2 ? 8 : 0), 0);
		CreateTargetFace($TourId, $i++, 'Blau / Bleu', 'REG-[CR](E)[HD]{1,1}$', '1', 8, 0, ($tourDetNumDist==2 ? 8 : 0), 0);
		CreateTargetFace($TourId, $i++, 'Grün / Vert', 'REG-[^CR](E)[HD]{1,1}$', '1', 8, 0, ($tourDetNumDist==2 ? 8 : 0), 0);
		CreateTargetFace($TourId, $i++, 'Grün / Vert', 'REG-(MI|PI)$', '1', 8, 0, ($tourDetNumDist==2 ? 8 : 0), 0);
	*/
		break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 32, 4);

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

