<?php
/*
17 	Type_H

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (8)

This is a weird Norwegian type

In Hunter, count only the best score of one arrow. The count is based on where the arrow hits the target.
1st arrow: heart 15 point
1st arrow: body 12 point

2nd arrow: heart 10 point
2nd arrow: body 7 point

3rd arrow: heart 5 point
2nd arrow: body 2 point

I (Chris) suppose that in case of tie you count the 15 points arrows and then the 12 points arrows

The rest is managed as if it was a 3D type

*/

$TourType=17;

$tourDetTypeName		= 'Type_NorH';
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

$tourDetMaxDistScore	= $tourDetNumEnds*(15);
$tourDetMaxFinIndScore	= '72';
$tourDetMaxFinTeamScore	= '144';
$tourDetCategory		= '8'; // 0: Other, 1: Outdoor, 2: Indoor, 4:Field, 8:3D
$tourDetElabTeam		= '2'; // 0:Standard, 1:Field, 2:3DI
$tourDetElimination		= '1'; // 0: No Eliminations, 1: Elimination Allowed
$tourDetGolds			= '15';
$tourDetXNine			= '12';
$tourDetGoldsChars		= 'Q';
$tourDetXNineChars		= 'N';
$tourDetDouble			= '0';
$DistanceInfoArray=array();
foreach(range(1,$tourDetNumDist) as $i) $DistanceInfoArray[]=array($tourDetNumEnds, 1);

require_once('Setup_Hunter.php');

?>