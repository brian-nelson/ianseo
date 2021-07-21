<?php
/*
1 	Type_FITA

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (1)

*/

$TourType=43;

// Tour details
$tourDetTypeName		= 'Type_NL_Hout';
$tourDetNumDist			= '1';
$tourDetNumEnds			= '25';
$tourDetMaxDistScore	= '150';
$tourDetMaxFinIndScore	= '0';
$tourDetMaxFinTeamScore	= '0';
$tourDetCategory		= '4'; // 0: Other, 1: Outdoor, 2: Indoor, 4:Field, 8:3D
$tourDetElabTeam		= '1'; // 0:Standard, 1:Field, 2:3DI
$tourDetElimination		= '1'; // 0: No Eliminations, 1: Elimination Allowed
$tourDetGolds			= '6';
$tourDetXNine			= '';
$tourDetGoldsChars		= 'G';
$tourDetXNineChars		= '';

$tourDetDouble			= '0';
$DistanceInfoArray=array(array(25, 1));

require_once('Setup_Target.php');
