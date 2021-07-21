<?php
/*
37 	Type_2x70mRound Round

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (37)

*/

$TourType=37;

$tourDetTypeName		= 'Type_2x70mRound';
$tourDetNumDist			= '4';
$tourDetNumEnds			= '12';
$tourDetMaxDistScore	= '360';
$tourDetMaxFinIndScore	= '150';
$tourDetMaxFinTeamScore	= '240';
$tourDetCategory		= '1'; // 0: Other, 1: Outdoor, 2: Indoor, 4:Field, 8:3D
$tourDetElabTeam		= '0'; // 0:Standard, 1:Field, 2:3DI
$tourDetElimination		= '0'; // 0: No Eliminations, 1: Elimination Allowed
$tourDetGolds			= '10+X';
$tourDetXNine			= 'X';
$tourDetGoldsChars		= 'KL';
$tourDetXNineChars		= 'K';
$tourDetDouble			= '1';
$DistanceInfoArray=array(array(6,6),array(6,6),array(6,6),array(6,6));

require_once('Setup_Target.php');

?>