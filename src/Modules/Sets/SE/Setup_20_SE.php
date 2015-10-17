<?php
/*
20 swe forest round

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (20)

*/

$TourType=20;

$tourDetTypeName		= 'Type_SweForestRound';
$tourDetNumDist			= ($SubRule==1 || $SubRule==2 ? '1' : '2');	// 60 frecce non ci stanno in una distanza
$tourDetNumEnds			= 0;
if ($SubRule==1) {
	$tourDetNumEnds	= 15;
}
elseif ($SubRule==2 || $SubRule==3)
{
	$tourDetNumEnds	= 30;
}
$tourDetMaxDistScore	= '600';
$tourDetMaxFinIndScore	= '0';	// per ora a zero
$tourDetMaxFinTeamScore	= '0';	// per ora a zero
$tourDetCategory		= '8'; // 0: Other, 1: Outdoor, 2: Indoor, 4:Field, 8:3D
$tourDetElabTeam		= '2'; // 0:Standard, 1:Field, 2:3DI
$tourDetElimination		= '0'; // 0: No Eliminations, 1: Elimination Allowed
$tourDetGolds			= '11';	// da sistemare
$tourDetXNine			= '10';	// da sistemare
$tourDetGoldsChars		= 'M'; // da sistemare
$tourDetXNineChars		= 'L';// da sistemare
$tourDetDouble			= '0';// da sistemare
switch($SubRule) {
	case 2:
		$tourDetNumDist = 1;
		$tourDetNumEnds = 30;
		$DistanceInfoArray=array(array(30,1));
		break;
	case 3:
		$tourDetNumDist = 2;
		$tourDetNumEnds = 30;
		$DistanceInfoArray=array(array(30,1),array(30,1));
		break;
	default:
		$tourDetNumDist = 1;
		$tourDetNumEnds = 15;
		$DistanceInfoArray=array(array(15,1),array(15,1));
}

require_once('Setup_D3.php');

?>