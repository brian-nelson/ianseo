<?php
/*
11 	3D 	(1 distance)

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (11)

*/

$TourType=11;

$tourDetTypeName		= 'Type_3D';
$tourDetNumDist			= (($SubRule<=4 or $SubRule==9) ? '1' : '2' );
switch($SubRule) {
	case 1:
	case 5:
		$tourDetNumEnds			= '10';
		break;
	case 2:
	case 6:
		$tourDetNumEnds			= '12';
		break;
	case 3:
	case 7:
		$tourDetNumEnds			= '20';
		break;
	default:
		$tourDetNumEnds			= '24';
}

$tourDetMaxDistScore	= $tourDetNumEnds*11*2;
$tourDetMaxFinIndScore	= '44';
$tourDetMaxFinTeamScore	= '132';
$tourDetCategory		= '8'; // 0: Other, 1: Outdoor, 2: Indoor, 4:Field, 8:3D
$tourDetElabTeam		= '2'; // 0:Standard, 1:Field, 2:3DI
$tourDetElimination		= '1'; // 0: No Eliminations, 1: Elimination Allowed
$tourDetGolds			= '11+10';
$tourDetXNine			= '11';
$tourDetGoldsChars		= 'ML';
$tourDetXNineChars		= 'M';
$tourDetDouble			= '0';
$DistanceInfoArray=array();
foreach(range(1,$tourDetNumDist) as $i) $DistanceInfoArray[]=array($tourDetNumEnds, 2);

require_once('Setup_D3.php');

?>