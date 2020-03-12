<?php
/*
7 	Type_Indoor 25

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (6)

*/

$TourType=6;

$tourDetTypeName		= 'Type_Indoor 18';
$tourDetNumDist			= '2';
$tourDetNumEnds			= '10';
$tourDetMaxDistScore	= '300';
$tourDetMaxFinIndScore	= '150';
$tourDetMaxFinTeamScore	= '240';
$tourDetCategory		= '2'; // 0: Other, 1: Outdoor, 2: Indoor, 4:Field, 8:3D
$tourDetElabTeam		= '0'; // 0:Standard, 1:Field, 2:3DI
$tourDetElimination		= '0'; // 0: No Eliminations, 1: Elimination Allowed
$tourDetGolds			= '10';
$tourDetXNine			= '9';
$tourDetGoldsChars		= 'L';
$tourDetXNineChars		= 'J';
$tourDetDouble			= '0';
$DistanceInfoArray=array(array(10, 3), array(10, 3));

require_once('Setup_Target.php');

?>