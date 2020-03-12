<?php
/*

Common setup for Field

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, 'FIELD');

// default SubClasses
//CreateSubClass($TourId, 1, '00', '00');

// default Classes
CreateStandardFieldClasses($TourId, $SubRule);

// default Distances
switch($TourType) {
	case 9:
		CreateDistance($TourId, $TourType, '%', 'Course');
		break;
	case 10:
	case 12:
		CreateDistance($TourId, $TourType, '%', 'Unmarked', 'Marked');
		break;
}

// default Events
CreateStandardFieldEvents($TourId, $SubRule);

// insert class in events
InsertStandardFieldEvents($TourId, $SubRule);

// Elimination rounds
InsertStandardFieldEliminations($TourId, $SubRule);

// Finals & TeamFinals
CreateFinals($TourId);

// create Groups finals
switch($SubRule) {
	case 3:
		$query = "INSERT INTO Finals (FinEvent,FinMatchNo,FinTournament,FinDateTime) 
			SELECT EvCode,GrMatchNo, $TourId, " . StrSafe_DB(date('Y-m-d H:i')) . " 
			FROM Events 
			INNER JOIN Grids ON GrMatchNo in (".implode(',', getPoolMatchNos()).") AND EvTeamEvent='0' AND EvTournament=$TourId
			where EvElimType=3";
		$rs=safe_w_sql($query);
		break;
	case 4:
		$query = "INSERT INTO Finals (FinEvent,FinMatchNo,FinTournament,FinDateTime) 
			SELECT EvCode,GrMatchNo, $TourId, " . StrSafe_DB(date('Y-m-d H:i')) . " 
			FROM Events 
			INNER JOIN Grids ON GrMatchNo in (".implode(',', getPoolMatchNosWA()).") AND EvTeamEvent='0' AND EvTournament=$TourId
			where EvElimType=4";
		$rs=safe_w_sql($query);
		break;
}

// Default Target
switch($TourType) {
	case 9:
		CreateTargetFace($TourId, 1, get_text('FieldPegYellow', 'Install'), 'REG-^BC', '1', 6, 0);
		CreateTargetFace($TourId, 2, get_text('FieldPegBlue', 'Install'), 'REG-^(B[^C]|[CR]C)', '1', 6, 0);
		CreateTargetFace($TourId, 3, get_text('FieldPegRed', 'Install'), 'REG-^[CR][^C]', '1', 6, 0);
		break;
	case 10:
	case 12:
		CreateTargetFace($TourId, 1, get_text('FieldPegYellow', 'Install'), 'REG-^BC', '1', 6, 0, 6, 0);
		CreateTargetFace($TourId, 2, get_text('FieldPegBlue', 'Install'), 'REG-^(B[^C]|[CR]C)', '1', 6, 0, 6, 0);
		CreateTargetFace($TourId, 3, get_text('FieldPegRed', 'Install'), 'REG-^[CR][^C]', '1', 6, 0, 6, 0);
		break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, $tourDetNumEnds, 4);

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