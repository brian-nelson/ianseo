<?php
/*
13 	3D 	(2 distances)

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (13)

*/

$TourType=13;

$tourDetTypeName		= '3D';
$tourDetNumDist			= '2';
$tourDetNumEnds			= '24';
$tourDetMaxDistScore	= '528';
$tourDetMaxFinIndScore	= '44';
$tourDetMaxFinTeamScore	= '132';
$tourDetCategory		= '8'; // 0: Other, 1: Outdoor, 2: Indoor, 4:Field, 8:3D
$tourDetElabTeam		= '2'; // 0:Standard, 1:Field, 2:3DI
$tourDetElimination		= '1'; // 0: No Eliminations, 1: Elimination Allowed
$tourDetGolds			= '11';
$tourDetXNine			= '10';
$tourDetGoldsChars		= 'M';
$tourDetXNineChars		= 'L';
$tourDetDouble			= '0';
$DistanceInfoArray=array(array(24, 2), array(24, 2));

require_once('Setup_D3.php');

?>