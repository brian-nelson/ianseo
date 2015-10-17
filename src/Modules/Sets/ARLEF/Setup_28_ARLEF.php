<?php
/*
28	Type_Bel_B25_Out
	safe_w_sql("insert into TourTypes set TtId=29, TtType='Type_Bel_B50-30_Out', TtDistance=2, TtOrderBy=29 on duplicate key update TtType='Type_Bel_B50-30_Out', TtDistance=2, TtOrderBy=29");
	safe_w_sql("insert into TourTypes set TtId=30, TtType='Type_Bel_BFITA_Out', TtDistance=4, TtOrderBy=30 on duplicate key update TtType='Type_Bel_BFITA_Out', TtDistance=4, TtOrderBy=30");

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (6)

*/

$TourType=28;

$tourDetTypeName		= 'Type_Bel_B25_Out';
$tourDetNumDist			= '2';
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
$tourDetDouble			= '0';
$DistanceInfoArray=array(array(12, 3), array(12, 3));

require_once('Setup_Target.php');
