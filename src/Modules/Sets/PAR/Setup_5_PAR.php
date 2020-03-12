<?php
/*
5 	Type_900 Round

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (5)

*/

$TourType=5;

$tourDetTypeName		= 'Type_900 Round';
$tourDetNumDist			= '3';
$tourDetNumEnds			= '10';
$tourDetMaxDistScore	= '300';
$tourDetMaxFinIndScore	= '0';
$tourDetMaxFinTeamScore	= '0';
$tourDetCategory		= '1'; // 0: Other, 1: Outdoor, 2: Indoor, 4:Field, 8:3D
$tourDetElabTeam		= '0'; // 0:Standard, 1:Field, 2:3DI
$tourDetElimination		= '0'; // 0: No Eliminations, 1: Elimination Allowed
$tourDetGolds			= '10+X';
$tourDetXNine			= 'X';
$tourDetGoldsChars		= 'KL';
$tourDetXNineChars		= 'K';
$tourDetDouble			= '0';
$DistanceInfoArray=array(array(5,6),array(5,6),array(5,6));

require_once('Setup_Target.php');

?>