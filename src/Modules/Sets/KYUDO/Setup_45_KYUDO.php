<?php
/*
3 	Type_70m Round

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (3)

*/

$TourType=45;

$tourDetTypeName		= 'Type_FR_Kyudo';
$tourDetNumDist			= '2';
$tourDetNumEnds			= '12';
$tourDetMaxDistScore	= '12';
$tourDetMaxFinIndScore	= '100';
$tourDetMaxFinTeamScore	= '100';
$tourDetCategory		= '0'; // 0: Other, 1: Outdoor, 2: Indoor, 4:Field, 8:3D
$tourDetElabTeam		= '0'; // 0:Standard, 1:Field, 2:3DI
$tourDetElimination		= '1'; // 0: No Eliminations, 1: Elimination Allowed
$tourDetGolds			= '';
$tourDetXNine			= '';
$tourDetGoldsChars		= '';
$tourDetXNineChars		= '';
$tourDetDouble			= '0';
$DistanceInfoArray=array(array(3, 4), array(20, 1));

require_once('Setup_Kyudo.php');

