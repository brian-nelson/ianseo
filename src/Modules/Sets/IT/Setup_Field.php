<?php
/*

Common Setup FIELD

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType);

// default SubClasses
CreateStandardSubClasses($TourId);

// default Classes
CreateStandardClasses($TourId, 1, 'FIELD'); // $SubRule force to 1 (ALL CLASSES)

// default Distances
switch($TourType) {
	case 9:
		CreateDistance($TourId, $TourType, '%', 'H&F');
		break;
	case 10:
	case 12:
		CreateDistance($TourId, $TourType, '%', 'Hunter', 'Field');
		break;
}

// default Events
CreateStandardFieldEvents($TourId, $SubRule); // $SubRule is OK as it is 1 or 2 elimination rounds

// insert class in events
InsertStandardFieldEvents($TourId, $SubRule); // $SubRule is OK as it is 1 or 2 elimination rounds

// Elimination rounds
InsertStandardFieldEliminations($TourId, $SubRule); // $SubRule is OK as it is 1 or 2 elimination rounds

// Finals & TeamFinals
CreateFinals($TourId);

// Default Target
/*

Blu per la Divisione Arco Nudo, per la classe Cadetti (Allievi) della Divisione Arco Ricurvo (Olimpico) e Cadetti (Allievi) della Divisione Arco Compound
Rosso per la Divisione Arco Ricurvo (Olimpico) e Arco Compound.
Giallo per la classe Cadetti (Allievi) della Divisione Arco Nudo
(*) Le classi non previste dalla FITA utilizzeranno i picchetti secondo le seguenti
modalità:
Picchetto Giallo: Giovanissimi (Arco Olimpico e Arco Nudo), Ragazzi Arco Nudo e Longbow (classe unica maschile e classe unica femminile)
Picchetto Blu: Ragazzi Arco Olimpico e Arco Compound.
[18:57:02 CEST] Ardingo: yellow peg
[18:57:05 CEST] Ardingo: red peg
[18:57:08 CEST] Ardingo: blue peg

 */
switch($TourType) {
	case 9:
		CreateTargetFace($TourId, 1, 'Picch. Giallo', 'REG-^(OLG|AN[AGR]|LB)', '1', 6, 0);
		CreateTargetFace($TourId, 2, 'Picch. Blu', 'REG-^(AN[^AGR]|OL[AR]|CO[AR]|AI[MF]$)', '1', 6, 0);
		CreateTargetFace($TourId, 3, 'Picch. Rosso', 'REG-^(OL[^AGR]|CO[^AGR])', '1', 6, 0);
		break;
	case 10:
	case 12:
		CreateTargetFace($TourId, 1, 'Picch. Giallo', 'REG-^(OLG|AN[AGR]|LB)', '1', 6, 0, 6, 0);
		CreateTargetFace($TourId, 2, 'Picch. Blu', 'REG-^(AN[^AGR]|OL[AR]|CO[AR]|AI[MF])', '1', 6, 0, 6, 0);
		CreateTargetFace($TourId, 3, 'Picch. Rosso', 'REG-^(OL[^AGR]|CO[^AGR])', '1', 6, 0, 6, 0);
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
	'ToIocCode'	=> $tourDetIocCode,
	);
UpdateTourDetails($TourId, $tourDetails);

?>