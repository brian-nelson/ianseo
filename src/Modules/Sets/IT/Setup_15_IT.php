<?php
/*
15 	Type_GiochiGioventu

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (2)

*/

$TourType=15;

$tourDetTypeName		= 'Type_GiochiGioventu';
$tourDetNumDist			= '2';
$tourDetNumEnds			= '8';
$tourDetMaxDistScore	= '240';
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
$DistanceInfoArray=array(array(8, 3), array(8, 3));

require_once('Setup_GdG.php');

?>