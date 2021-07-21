<?php
/*
1 	Type_FITA

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (1)

*/

$TourType=42;

// Tour details
$tourDetTypeName		= 'Type_NL_25p1';
$tourDetNumDist			= '2';
$tourDetNumEnds			= '25';
$tourDetMaxDistScore	= '250';
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
$DistanceInfoArray=array(array(25, 1), array(25, 1));

require_once('Setup_Target.php');
