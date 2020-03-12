<?php
/*
35 	Type_NZ_Clout

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (1)

*/

$TourType=35;

// Tour details
$tourDetTypeName		= 'Type_NZ_Clout';
$tourDetNumDist			= '1';
$tourDetNumEnds			= '12';
$tourDetMaxDistScore	= '324';
$tourDetMaxFinIndScore	= '135';
$tourDetMaxFinTeamScore	= '216';
$tourDetCategory		= '1'; // 0: Other, 1: Outdoor, 2: Indoor, 4:Field, 8:3D
$tourDetElabTeam		= '0'; // 0:Standard, 1:Field, 2:3DI
$tourDetElimination		= '0'; // 0: No Eliminations, 1: Elimination Allowed
$tourDetGolds			= '9s';
$tourDetXNine			= '7s';
$tourDetGoldsChars		= '9';
$tourDetXNineChars		= '7';
$tourDetDouble			= '0';
$DistanceInfoArray=array(array(6,6));

require_once('Setup_Target.php');

?>