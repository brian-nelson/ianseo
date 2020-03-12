<?php
/*
9 	Type_HF 12+12 	(1 distance)

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (8)

*/

$TourType=9;

$tourDetTypeName		= 'Type_NorField';
$tourDetNumDist			= (($SubRule<=4 or $SubRule==9) ? '1' : '2' );
switch($SubRule) {
	case 1:
	case 5:
		$tourDetNumEnds			= '12';
		break;
	case 2:
	case 6:
		$tourDetNumEnds			= '16';
		break;
	case 3:
	case 7:
		$tourDetNumEnds			= '20';
		break;
	default:
		$tourDetNumEnds			= '24';
}

$tourDetMaxDistScore	= $tourDetNumEnds*6*3;
$tourDetMaxFinIndScore	= '72';
$tourDetMaxFinTeamScore	= '144';
$tourDetCategory		= '4'; // 0: Other, 1: Outdoor, 2: Indoor, 4:Field, 8:3D
$tourDetElabTeam		= '1'; // 0:Standard, 1:Field, 2:3DI
$tourDetElimination		= '1'; // 0: No Eliminations, 1: Elimination Allowed
$tourDetGolds			= '6+5';
$tourDetXNine			= '6';
$tourDetGoldsChars		= 'FG';
$tourDetXNineChars		= 'G';
$tourDetDouble			= '0';
$DistanceInfoArray=array();
foreach(range(1,$tourDetNumDist) as $i) $DistanceInfoArray[]=array($tourDetNumEnds, 3);

require_once('Setup_Field.php');

?>