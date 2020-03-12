<?php
/*
19 	Type_GiochiStudentes

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (2)

*/

$TourType=19;

$tourDetTypeName		= 'Type_GiochiStudentes';
$tourDetNumDist			= '1';
$tourDetNumEnds			= '12';
$tourDetMaxDistScore	= '360';
$tourDetMaxFinIndScore	= '0';
$tourDetMaxFinTeamScore	= '0';
$tourDetCategory		= '1'; // 0: Other, 1: Outdoor, 2: Indoor, 4:Field, 8:3D
$tourDetElabTeam		= '0'; // 0:Standard, 1:Field, 2:3DI
$tourDetElimination		= '0'; // 0: No Eliminations, 1: Elimination Allowed
$tourDetGolds			= '10';
$tourDetXNine			= '9';
$tourDetGoldsChars		= 'L';
$tourDetXNineChars		= 'J';
$tourDetDouble			= '0';
$DistanceInfoArray=array(array(12, 3));

require_once('Setup_Studenteschi.php');

