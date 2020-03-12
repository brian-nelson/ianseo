<?php
/*
Common Setup for "Target" Archery
*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType, $SubRule);

// default Classes
CreateStandardClasses($TourId, $SubRule, $TourType);

// default Subclasses
CreateStandardSubClasses($TourId);

// default Distances
switch($TourType) {
case 3:	// Outdoor - Utandyra fjarlægðir - COMPLETE

    CreateDistance($TourId, $TourType, 'R_', '70 m', '70 m');  // Recurve Senior Opinn flokkur
    CreateDistance($TourId, $TourType, 'RM_', '60 m', '60 m'); // Recurve Master E50
    CreateDistance($TourId, $TourType, 'RJ_', '70 m', '70 m'); // Recurve Junior U21
    CreateDistance($TourId, $TourType, 'RC_', '60 m', '60 m'); // Recurve Cadet U18
    CreateDistance($TourId, $TourType, 'RN_', '40 m', '40 m'); // Recurve Nordic U15
    CreateDistance($TourId, $TourType, 'RB_', '60 m', '60 m'); // Recurve Beginner Byrjendur

    CreateDistance($TourId, $TourType, 'C_', '50 m', '50 m');  // Compound Open
    CreateDistance($TourId, $TourType, 'CM_', '50 m', '50 m'); // Compound Master E50
    CreateDistance($TourId, $TourType, 'CJ_', '50 m', '50 m'); // Compound Junior U21
    CreateDistance($TourId, $TourType, 'CC_', '50 m', '50 m'); // Compound Cadet U18
    CreateDistance($TourId, $TourType, 'CN_', '30 m', '30 m'); // Compound Nordic U15
    CreateDistance($TourId, $TourType, 'CB_', '50 m', '50 m'); // Compound Beginner Byrjendur

    CreateDistance($TourId, $TourType, 'B_', '30 m', '30 m');  // Barebow Open
    CreateDistance($TourId, $TourType, 'BM_', '30 m', '30 m'); // Barebow Master E50
    CreateDistance($TourId, $TourType, 'BJ_', '30 m', '30 m'); // Barebow Junior U21
    CreateDistance($TourId, $TourType, 'BC_', '30 m', '30 m'); // Barebow Cadet U18
    CreateDistance($TourId, $TourType, 'BN_', '30 m', '30 m'); // Barebow Nordic U15
    CreateDistance($TourId, $TourType, 'BB_', '30 m', '30 m'); // Barebow Beginner Byrjendur
    break;
case 6:    // Indoor - Innandyra fjarlægðir

    CreateDistance($TourId, $TourType, 'R_', '18 m', '18 m');  // Recurve Senior Opinn flokkur
    CreateDistance($TourId, $TourType, 'RM_', '18 m', '18 m'); // Recurve Master E50
    CreateDistance($TourId, $TourType, 'RJ_', '18 m', '18 m'); // Recurve Junior U21
    CreateDistance($TourId, $TourType, 'RC_', '18 m', '18 m'); // Recurve Cadet U18
    CreateDistance($TourId, $TourType, 'RN_', '12 m', '12 m'); // Recurve Nordic U15
    CreateDistance($TourId, $TourType, 'RB_', '18 m', '18 m'); // Recurve Beginner Byrjendur

    CreateDistance($TourId, $TourType, 'C_', '18 m', '18 m');  // Compound Senior Opinn flokkur
    CreateDistance($TourId, $TourType, 'CM_', '18 m', '18 m'); // Compound Master E50
    CreateDistance($TourId, $TourType, 'CJ_', '18 m', '18 m'); // Compound Junior U21
    CreateDistance($TourId, $TourType, 'CC_', '18 m', '18 m'); // Compound Cadet U18
    CreateDistance($TourId, $TourType, 'CN_', '12 m', '12 m'); // Compound Nordic U15
    CreateDistance($TourId, $TourType, 'CB_', '18 m', '18 m'); // Compound Beginner Byrjendur

    CreateDistance($TourId, $TourType, 'B_', '18 m', '18 m');  // Barebow Senior Opinn flokkur
    CreateDistance($TourId, $TourType, 'BM_', '18 m', '18 m'); // Barebow Master E50
    CreateDistance($TourId, $TourType, 'BJ_', '18 m', '18 m'); // Barebow Junior U21
    CreateDistance($TourId, $TourType, 'BC_', '18 m', '18 m'); // Barebow Cadet U18
    CreateDistance($TourId, $TourType, 'BN_', '12 m', '12 m'); // Barebow Nordic U15
    CreateDistance($TourId, $TourType, 'BB_', '18 m', '18 m'); // Barebow Beginner Byrjendur
	break;
}

// default Events
CreateStandardEvents($TourId, $TourType, $SubRule, $tourDetCategory=='1');

// Classes in Events
InsertStandardEvents($TourId, $TourType, $SubRule, $tourDetCategory=='1');

// Finals & TeamFinals
CreateFinals($TourId);

// Default Target
$i=1;
switch($TourType) {
	case 6: // Indoor - Innandyra skífur
        if ($SubRule==1) {
		// Recurve - Sveigboga skífur standard undankeppni
        CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'RM', '1', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'RW', '1', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'RM_', '1', 2, 40, 2, 40);
        CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'RJ_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'RC_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'RB_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'RN_', '1', 1, 60, 1, 60);
		// Optional Recurve - valmöguleika skífur fyrir sveigboga
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'RM', '', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'RW', '', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'RM_', '', 1, 40, 1, 40);
        CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'RJ_', '', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'RC_', '', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'RB_', '', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'RN_', '', 2, 60, 2, 60);
		
		// Compound - Trissuboga skífur standard undankeppni
        CreateTargetFace($TourId, $i++, '~40cm (6-10 small-ten)', 'CM', '1', 4, 40, 4, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10 small-ten)', 'CW', '1', 4, 40, 4, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10 small-ten)', 'CM_', '1', 4, 40, 4, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10 small-ten)', 'CJ_', '1', 4, 40, 4, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10 small-ten)', 'CC_', '1', 4, 40, 4, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10 small-ten)', 'CB_', '1', 4, 40, 4, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10 small-ten)', 'CN_', '1', 3, 60, 3, 60);
		// optional Compound - valmöguleika skífur fyrir trissuboga
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'CM', '', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'CW', '', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'CM_', '', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'CJ_', '', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'CC_', '', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'CB_', '', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~60cm (6-10 small-ten)', 'CN_', '', 4, 60, 4, 60);
		
		// Barebow - Berboga skífur standard undankeppni
        CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'BM', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'BW', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'BM_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'BJ_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'BC_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'BB_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'BN_', '1', 1, 60, 1, 60);	
        // optional Barebow - valmöguleika skífur fyrir berboga
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'BM', '', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'BW', '', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'BM_', '', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'BJ_', '', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'BC_', '', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'BB_', '', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'BN_', '', 2, 60, 2, 60);
		}
		if ($SubRule==2) {
		// Recurve - Sveigboga skífur standard undankeppni
        CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'R%', '1', 2, 40, 2, 40);
		// Optional Recurve - valmöguleika skífur fyrir sveigboga
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'R%', '', 1, 40, 1, 40);
		
		// Compound - Trissuboga skífur standard undankeppni
		CreateTargetFace($TourId, $i++, '~40cm (6-10 small-ten)', 'C%', '1', 4, 40, 4, 40);
		// optional Compound - valmöguleika skífur fyrir trissuboga
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'C%', '', 3, 40, 3, 40);
		
		// Barebow - Berboga skífur standard undankeppni
        CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'B%', '1', 1, 40, 1, 40);
        // optional Barebow - valmöguleika skífur fyrir berboga
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'B%', '', 2, 40, 2, 40);
		}
		if ($SubRule==3) {
		// Recurve - Sveigboga skífur standard undankeppni
        CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'R%', '1', 2, 40, 2, 40);
		// Optional Recurve - valmöguleika skífur fyrir sveigboga
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'R%', '', 1, 40, 1, 40);
		
		// Compound - Trissuboga skífur standard undankeppni
		CreateTargetFace($TourId, $i++, '~40cm (6-10 small-ten)', 'C%', '1', 4, 40, 4, 40);
		// optional Compound - valmöguleika skífur fyrir trissuboga
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'C%', '', 3, 40, 3, 40);
		
		// Barebow - Berboga skífur standard undankeppni
        CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'B%', '1', 1, 40, 1, 40);
        // optional Barebow - valmöguleika skífur fyrir berboga
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'B%', '', 2, 40, 2, 40);
		}
		if ($SubRule==4) {
		// Recurve - Sveigboga skífur standard undankeppni
        CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'RU', '1', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'RM_', '1', 2, 40, 2, 40);
        CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'RJ_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'RC_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'RN_', '1', 1, 60, 1, 60);
		// Optional Recurve - valmöguleika skífur fyrir sveigboga
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'RU', '', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'RM_', '', 1, 40, 1, 40);
        CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'RJ_', '', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'RC_', '', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'RN_', '', 2, 60, 2, 60);
		
		// Compound - Trissuboga skífur standard undankeppni
        CreateTargetFace($TourId, $i++, '~40cm (6-10 small-ten)', 'CU', '1', 4, 40, 4, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10 small-ten)', 'CM_', '1', 4, 40, 4, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10 small-ten)', 'CJ_', '1', 4, 40, 4, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10 small-ten)', 'CC_', '1', 4, 40, 4, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10 small-ten)', 'CN_', '1', 3, 60, 3, 60);
		// optional Compound - valmöguleika skífur fyrir trissuboga
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'CU', '', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'CM_', '', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'CJ_', '', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'CC_', '', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~60cm (6-10 small-ten)', 'CN_', '', 4, 60, 4, 60);
		
		// Barebow - Berboga skífur standard undankeppni
        CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'BU', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'BM_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'BJ_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'BC_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'BN_', '1', 1, 60, 1, 60);	
        // optional Barebow - valmöguleika skífur fyrir berboga
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'BU', '', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'BM_', '', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'BJ_', '', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'BC_', '', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'BN_', '', 2, 60, 2, 60);
		}
		
        break;
    case 3: // Outdoor - Utandyra skífur - COMPLETE
        CreateTargetFace($TourId, $i++, '122cm (1-10)', 'R%', '1', 5, 122, 5, 122);
        CreateTargetFace($TourId, $i++, '80cm (5-10)', 'C%', '1', 9, 80, 9, 80);
        CreateTargetFace($TourId, $i++, '80cm (1-10)', 'B%', '1', 5, 80, 5, 80);
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 10, 2);

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
