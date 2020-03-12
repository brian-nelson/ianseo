<?php
/*
12 	Type_HF 12+12 	(2 distances)

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (12)

*/

$TourType=12;

$tourDetTypeName		= 'Type_HF 12+12';
$tourDetNumDist			= '2';
$tourDetNumEnds			= '12';
$tourDetMaxDistScore	= '216';
$tourDetMaxFinIndScore	= '72';
$tourDetMaxFinTeamScore	= '144';
$tourDetCategory		= '4'; // 0: Other, 1: Outdoor, 2: Indoor, 4:Field, 8:3D
$tourDetElabTeam		= '1'; // 0:Standard, 1:Field, 2:3DI
$tourDetElimination		= '1'; // 0: No Eliminations, 1: Elimination Allowed
$tourDetGolds			= '6';
$tourDetXNine			= '5';
$tourDetGoldsChars		= 'G';
$tourDetXNineChars		= 'F';
$tourDetDouble			= '0';
$DistanceInfoArray=array(array(12, 3), array(12, 3));

require_once('Setup_Field.php');

?>