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
		CreateDistance($TourId, $TourType, 'RH', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, '_MO', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GRH', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'CH', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GCH', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'RJH', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GRJH', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'CJH', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GCJH', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'RD', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, '_WO', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GRD', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'CD', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GCD', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'RJD', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GRJD', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'CJD', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GCJD', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'RCH', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GRCH', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'CCH', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GCCH', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'RVH', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GRVH', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'CVH', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GCVH', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'RCD', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, 'GRCD', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, 'CCD', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, 'GCCD', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, 'RVD', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, 'GRVD', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, 'CVD', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, 'GCVD', '60 m', '50 m', '40 m', '30 m');
		/* Ramon Keller 11.05.2017: Jeunesse unisex change */
		//CreateDistance($TourId, $TourType, 'RE_', '50 m', '40 m', '30 m', '20 m');
		//CreateDistance($TourId, $TourType, 'CE_', '50 m', '40 m', '30 m', '20 m');
		CreateDistance($TourId, $TourType, 'RJE', '50 m', '40 m', '30 m', '20 m');
		CreateDistance($TourId, $TourType, 'GRJE', '50 m', '40 m', '30 m', '20 m');
		CreateDistance($TourId, $TourType, 'CJE', '50 m', '40 m', '30 m', '20 m');
		CreateDistance($TourId, $TourType, 'GCJE', '50 m', '40 m', '30 m', '20 m');
		CreateDistance($TourId, $TourType, 'BB_', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BH_', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'LB_', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'GB_', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BBJH', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BBJD', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BHJH', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BHJD', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'LBJH', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'LBJD', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'GBJH', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'GBJD', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BBC_', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BHC_', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'LBC_', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'GBC_', '40 m', '40 m', '30 m', '30 m');
		/* Ramon Keller 11.05.2017: Jeunesse unisex change */
		//CreateDistance($TourId, $TourType, 'BBE_', '40 m', '40 m', '30 m', '30 m');
		//CreateDistance($TourId, $TourType, 'BHE_', '40 m', '40 m', '30 m', '30 m');
		//CreateDistance($TourId, $TourType, 'LBE_', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BBJE', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BHJE', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'LBJE', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'GBJE', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BBV_', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BHV_', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'LBV_', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'GBV_', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, '%MI', '-', '-', '25 m', '20 m');
		// Fixed deviation from SwissRuleset for %PI
		//CreateDistance($TourId, $TourType, '%PI', '-', '-', '25 m', '20 m');
		CreateDistance($TourId, $TourType, '%PI', '-', '-', '20 m', '15 m');
		break;
	case 2:  // 2 x FITA - 8 Distances
		CreateDistance($TourId, $TourType, 'RH', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, '_MO', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GRH', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'CH', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GCH', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'RJH', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GRJH', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'CJH', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GCJH', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'RD', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, '_WO', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GRD', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'CD', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GCD', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'RJD', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GRJD', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'CJD', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GCJD', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'RCH', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GRCH', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'CCH', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GCCH', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'RVH', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GRVH', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'CVH', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'GCVH', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'RCD', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, 'GRCD', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, 'CCD', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, 'GCCD', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, 'RVD', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, 'GRVD', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, 'CVD', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, 'GCVD', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
		/* Ramon Keller 11.05.2017: Jeunesse unisex change */
		//CreateDistance($TourId, $TourType, 'RE_', '50 m', '40 m', '30 m', '20 m');
		//CreateDistance($TourId, $TourType, 'CE_', '50 m', '40 m', '30 m', '20 m');
		CreateDistance($TourId, $TourType, 'RJE', '50 m', '40 m', '30 m', '20 m', '50 m', '40 m', '30 m', '20 m');
		CreateDistance($TourId, $TourType, 'GRJE', '50 m', '40 m', '30 m', '20 m', '50 m', '40 m', '30 m', '20 m');
		CreateDistance($TourId, $TourType, 'CJE', '50 m', '40 m', '30 m', '20 m', '50 m', '40 m', '30 m', '20 m');
		CreateDistance($TourId, $TourType, 'GCJE', '50 m', '40 m', '30 m', '20 m', '50 m', '40 m', '30 m', '20 m');
		CreateDistance($TourId, $TourType, 'BB_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BH_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'LB_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'GB_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BBJH', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BBJD', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BHJH', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BHJD', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'LBJH', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'LBJD', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'GBJH', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'GBJD', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BBC_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BHC_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'LBC_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'GBC_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		/* Ramon Keller 11.05.2017: Jeunesse unisex change */
		//CreateDistance($TourId, $TourType, 'BBE_', '40 m', '40 m', '30 m', '30 m');
		//CreateDistance($TourId, $TourType, 'BHE_', '40 m', '40 m', '30 m', '30 m');
		//CreateDistance($TourId, $TourType, 'LBE_', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BBJE', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BHJE', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'LBJE', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'GBJE', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BBV_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BHV_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'LBV_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'GBV_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, '%MI', '-', '-', '25 m', '20 m', '-', '-', '25 m', '20 m');
		// Fixed deviation from SwissRuleset for %PI
		//CreateDistance($TourId, $TourType, '%PI', '-', '-', '25 m', '20 m');
		CreateDistance($TourId, $TourType, '%PI', '-', '-', '20 m', '15 m', '-', '-', '20 m', '15 m');

	/*
		CreateDistance($TourId, $TourType, 'RH', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'CH', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'RJH', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'CJH', '90 m', '70 m', '50 m', '30 m', '90 m', '70 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'RD', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'CD', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'RJD', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'CJD', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'RCH', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'CCH', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'RVH', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'CVH', '70 m', '60 m', '50 m', '30 m', '70 m', '60 m', '50 m', '30 m');
		CreateDistance($TourId, $TourType, 'RCD', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, 'CCD', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, 'RVD', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, 'CVD', '60 m', '50 m', '40 m', '30 m', '60 m', '50 m', '40 m', '30 m');
		CreateDistance($TourId, $TourType, 'RE_', '50 m', '40 m', '30 m', '20 m', '50 m', '40 m', '30 m', '20 m');
		CreateDistance($TourId, $TourType, 'CE_', '50 m', '40 m', '30 m', '20 m', '50 m', '40 m', '30 m', '20 m');
		CreateDistance($TourId, $TourType, 'BB_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BH_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'LB_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BBJ_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BHJ_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'LBJ_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BBC_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BHC_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'LBC_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BBE_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BHE_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'LBE_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BBV_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'BHV_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, 'LBV_', '40 m', '40 m', '30 m', '30 m', '40 m', '40 m', '30 m', '30 m');
		CreateDistance($TourId, $TourType, '%MI', '-', '-', '25 m', '20 m', '-', '-', '25 m', '20 m');
		CreateDistance($TourId, $TourType, '%PI', '-', '-', '20 m', '15 m', '-', '-', '20 m', '15 m');
	*/
		break;
	case 3:  // 70m/50m Round - 2 Distances
		CreateDistance($TourId, $TourType, '%PI', '15m-1', '15m-2');
		CreateDistance($TourId, $TourType, '%MI', '25m-1', '25m-2');

		CreateDistance($TourId, $TourType, 'BBV_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'BHV_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'LBV_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'GBV_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'BB_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'BH_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'LB_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'GB_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'BBJ_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'BHJ_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'LBJ_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'GBJ_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'BBC_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'BHC_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'LBC_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'GBC_', '30m-1', '30m-2');
		//CreateDistance($TourId, $TourType, 'BBJE', '30m-1', '30m-2');
		//CreateDistance($TourId, $TourType, 'BHJE', '30m-1', '30m-2');
		//CreateDistance($TourId, $TourType, 'LBJE', '30m-1', '30m-2');
		//CreateDistance($TourId, $TourType, 'GBJE', '30m-1', '30m-2');

		// Updated Jeunesse categories CJE/RJE (GCJE/GRJE)
		CreateDistance($TourId, $TourType, 'RJE', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'CJE', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'GCJE', '40m-1', '40m-2');
		CreateDistance($TourId, $TourType, 'GRJE', '40m-1', '40m-2');

		CreateDistance($TourId, $TourType, 'CV_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'C_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'CJH', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'CJD', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'CC_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'C_O', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'GCV_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'GC_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'GCJH', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'GCJD', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'GCC_', '50m-1', '50m-2');

		CreateDistance($TourId, $TourType, 'RV_', '60m-1', '60m-2');
		CreateDistance($TourId, $TourType, 'RC_', '60m-1', '60m-2');
		CreateDistance($TourId, $TourType, 'GRV_', '60m-1', '60m-2');
		CreateDistance($TourId, $TourType, 'GRC_', '60m-1', '60m-2');

		CreateDistance($TourId, $TourType, 'R_', '70m-1', '70m-2');
		CreateDistance($TourId, $TourType, 'RJH', '70m-1', '70m-2');
		CreateDistance($TourId, $TourType, 'RJD', '70m-1', '70m-2');
		CreateDistance($TourId, $TourType, 'R_O', '70m-1', '70m-2');
		CreateDistance($TourId, $TourType, 'GR_', '70m-1', '70m-2');
		CreateDistance($TourId, $TourType, 'GRJH', '70m-1', '70m-2');
		CreateDistance($TourId, $TourType, 'GRJD', '70m-1', '70m-2');


	/* Old/Previous configuration
		CreateDistance($TourId, $TourType, 'RV_', '60m-1', '60m-2');
		CreateDistance($TourId, $TourType, 'R_', '70m-1', '70m-2');
		CreateDistance($TourId, $TourType, 'RJ_', '70m-1', '70m-2');
		CreateDistance($TourId, $TourType, 'RC_', '60m-1', '60m-2');
		CreateDistance($TourId, $TourType, 'RE_', '40m-1', '40m-2');

		CreateDistance($TourId, $TourType, 'CV_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'C_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'CJ_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'CC_', '50m-1', '50m-2');
		CreateDistance($TourId, $TourType, 'CE_', '40m-1', '40m-2');

		CreateDistance($TourId, $TourType, 'BBV_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'BHV_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'LBV_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'BB_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'BH_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'LB_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'BBJ_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'BHJ_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'LBJ_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'BBC_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'BHC_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'LBC_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'BBE_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'BHE_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, 'LBE_', '30m-1', '30m-2');
		CreateDistance($TourId, $TourType, '%MI', '25m-1', '25m-2');
		CreateDistance($TourId, $TourType, '%PI', '15m-1', '15m-2');
		*/
		break;
	case 6:  // Indoor 18 m - 2 Distances
		CreateDistance($TourId, $TourType, '%D', '18m-1', '18m-2');
		CreateDistance($TourId, $TourType, '%H', '18m-1', '18m-2');
		CreateDistance($TourId, $TourType, 'R_O', '18m-1', '18m-2');
		CreateDistance($TourId, $TourType, 'C_O', '18m-1', '18m-2');
		/* Ramon Keller 11.05.2017: Jeunesse unisex change. Addded next line */
		CreateDistance($TourId, $TourType, '%JE', '18m-1', '18m-2');
		CreateDistance($TourId, $TourType, '%MI', '18m-1', '18m-2');
		CreateDistance($TourId, $TourType, '%PI', '15m-1', '15m-2');
		break;
	case 7:  // Indoor 25 m - 2 Distances
		/* Ramon Keller 11.05.2017: Newly introduced to CH: 25m Indoor */
		CreateDistance($TourId, $TourType, '%D', '25m-1', '25m-2');
		CreateDistance($TourId, $TourType, '%H', '25m-1', '25m-2');
		CreateDistance($TourId, $TourType, 'R_O', '25m-1', '25m-2');
		CreateDistance($TourId, $TourType, 'C_O', '25m-1', '25m-2');
		CreateDistance($TourId, $TourType, '%JE', '25m-1', '25m-2');
		CreateDistance($TourId, $TourType, '%MI', '25m-1', '25m-2');
		CreateDistance($TourId, $TourType, '%PI', '18m-1', '18m-2');
		break;
	case 9:   // Field Archery
	case 11:  // 3D
	case 13:
		if($tourDetNumDist==2)
			CreateDistance($TourId, $TourType, '%', 'Course 1', 'Course 2');
		else
			CreateDistance($TourId, $TourType, '%', 'Course');
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
		CreateTargetFace($TourId, $i++, '122cm(1-x) / 122cm(1-x)', 'REG-^((LB)|(BB)|(BH)|GB)(JE){1,1}', '1', 5, 122, 5, 122, 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++,  '80cm(1-x) / 80cm(1-x)',  'REG-^(BB)[CJV]{0,1}[HD]{1,1}', '1', 5, 80, 5, 80, 5, 80, 5, 80);
		CreateTargetFace($TourId, $i++, '122cm(1-x) / 122cm(1-x)', 'REG-^((LB)|(BH)|GB)[CJV]{0,1}[HD]{1,1}', '1', 5, 122, 5, 122, 5, 122, 5, 122);
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
		CreateTargetFace($TourId, $i++, '122cm(1-x) / 122cm(1-x)', 'REG-^((LB)|(BB)|(BH)|GB)(JE){1,1}', '1', 5, 122, 5, 122, 5, 122, 5, 122, 5, 122, 5, 122, 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++,  '80cm(1-x) / 80cm(1-x)',  'REG-^(BB)[CJV]{0,1}[HD]{1,1}', '1', 5, 80, 5, 80, 5, 80, 5, 80, 5, 80, 5, 80, 5, 80, 5, 80);
		CreateTargetFace($TourId, $i++, '122cm(1-x) / 122cm(1-x)', 'REG-^((LB)|(BH)|GB)[CJV]{0,1}[HD]{1,1}', '1', 5, 122, 5, 122, 5, 122, 5, 122, 5, 122, 5, 122, 5, 122, 5, 122);
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
		CreateTargetFace($TourId, $i++, '122cm (1-x)', 'REG-^((R)|(LB)|(BH)|GR|GB)[CJV]{0,1}[HD]{1,1}', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '122cm (1-x)', 'REG-^(R)[MW]{1,1}[O]{1,1}', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++,  '80cm (1-x)', 'REG-^(BB)[CJV]{0,1}[HD]{1,1}', '1', 5, 80, 5, 80);
		CreateTargetFace($TourId, $i++,  '80cm (5-x)', 'REG-^(C|GC)[CJV]{0,1}[HD]{1,1}', '1', 9, 80, 9, 80);
		CreateTargetFace($TourId, $i++,  '80cm (5-x)', 'REG-^(C)[MW]{1,1}[O]{1,1}', '1', 9, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '122cm (1-x)', 'REG-(JE){1,1}$', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++,  '80cm (1-x)', 'REG-(MI|PI)$', '1', 5, 80, 5, 80);
		break;
	case 6:  // Indoor 18 m - 2 Distances
		CreateTargetFace($TourId, $i++, '40cm (1-big 10)', '%', '1', 1, 40, 1, 40);  // big 10
		CreateTargetFace($TourId, $i++, 'Trispot Comp 40cm', 'REG-^(C|GC)(D|C|H|V|JD|JH|MO|WO)', '1', 4, 40, 4, 40);  // small 10
		CreateTargetFace($TourId, $i++, '60cm (1-big 10)', 'REG-((^R|^BB|^GR|^GB)JE)|(^LB|^BH|^GB).*[^I]$', '1', 1, 60, 1, 60);  // big 10
		CreateTargetFace($TourId, $i++, '60cm (1-small 10)', 'REG-(^C|^GC)JE', '1', 3, 60, 3, 60);  // small 10
		CreateTargetFace($TourId, $i++, '80cm (1-small 10)', 'REG-(^C|^GC).I$', '1', 3, 80, 3, 80);  // small 10
		CreateTargetFace($TourId, $i++, '80cm (1-big 10)', 'REG-(^R|^BB|^BH|^LB|^GR|^GB).I$', '1', 1, 80, 1, 80);  // big 10
		// optional target faces
		CreateTargetFace($TourId, $i++, 'Trispot Rec 40cm', 'REG-^(GR|R)(D|C|H|V|JD|JH|MO|WO)', '',  2, 40, 2, 40);  // big 10

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
		CreateTargetFace($TourId, $i++, '80cm (1-big 10)', 'REG-(^(R|BB|LB|BH|GR|GB)(JE|MI)$)|(^(LB|BH|GB).*[^(MI)])$', '1', 1, 80, 1, 80);  // big 10
		CreateTargetFace($TourId, $i++, '80cm (1-small 10)', 'REG-(^C|^GC)(JE|MI)$', '1', 3, 80, 3, 80);  // small 10
		CreateTargetFace($TourId, $i++, '122cm (1-small 10)', 'REG-(^C|^GC)PI$', '1', 3, 122, 3, 122);  // small 10
		CreateTargetFace($TourId, $i++, '80cm (1-big 10)', 'REG-(^R|^BB|^BH|^LB|^GR|^GB)PI$', '1', 1, 122, 1, 122);  // big 10
		// optional target faces
		CreateTargetFace($TourId, $i++, 'Trispot Rec 60cm', 'REG-^(GR|R)(D|C|H|V|JD|JH|MO|WO)', '',  2, 60, 2, 60);  // big 10
		break;
	case 9:  // Field Archery
		CreateTargetFace($TourId, $i++, 'Rot / Rouge', 'REG-^((R)|(C)|(GR)|(GC))[JV]{0,1}[HD]{1,1}', '1', 6, 0, ($tourDetNumDist==2 ? 6 : 0), 0);
		CreateTargetFace($TourId, $i++, 'Blau / Bleu', 'REG-^((BB)|(BH)|(GB))[JV]{0,1}[HD]{1,1}', '1', 6, 0, ($tourDetNumDist==2 ? 6 : 0), 0);
		CreateTargetFace($TourId, $i++, 'Blau / Bleu', 'REG-((R)|(C))[C][HD]{1,1}$', '1', 6, 0, ($tourDetNumDist==2 ? 6 : 0), 0);
		CreateTargetFace($TourId, $i++, 'Gelb / Jaune', 'REG-^(LB)[JV]{0,1}[HD]{1,1}', '1', 6, 0, ($tourDetNumDist==2 ? 6 : 0), 0);
		CreateTargetFace($TourId, $i++, 'Gelb / Jaune', 'REG-((BB)|(LB)|(BH)|(GB))[C][HD]{1,1}$', '1', 6, 0, ($tourDetNumDist==2 ? 6 : 0), 0);
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
		CreateTargetFace($TourId, $i++, 'Blau / Bleu', 'REG-^((LB)|(BB)|(BH)|(GB))[CJV]{0,1}[HD]{1,1}', '1', 8, 0, ($tourDetNumDist==2 ? 8 : 0), 0);
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

?>