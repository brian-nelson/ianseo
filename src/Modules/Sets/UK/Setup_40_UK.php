<?php
/*
40	Type_LocalUK

$TourId is the ID of the tournament!
$SubRule is the eventual AGB defined Rounds (see sets.php for the order)
$TourType is the Tour Type (40)

*/

$TourType=40;

//Set max distance score and end information
switch($SubRule) {
    case 1: //YHB
        $tourDetMaxDistScore	= '648';
        $tourDetNumDist = '3';
        $tourDetNumEnds = '12';
        $DistanceInfoArray = array(array(12, 6), array(8, 6), array(4, 6));
        break;
    case 2: //Windsors,Albions
        $tourDetMaxDistScore	= '324';
        $tourDetNumDist = '3';
        $tourDetNumEnds = '6';
        $DistanceInfoArray = array(array(6, 6), array(6, 6), array(6, 6));
        break;
    case 3: //Americans
        $tourDetMaxDistScore	= '270';
        $tourDetNumDist = '3';
        $tourDetNumEnds = '5';
        $DistanceInfoArray = array(array(5, 6), array(5, 6), array(5, 6));
        break;
    case 4: //Nationals
        $tourDetMaxDistScore	= '432';
        $tourDetNumDist = '2';
        $tourDetNumEnds = '8';
        $DistanceInfoArray = array(array(8, 6), array(4, 6));
        break;
    case 5: //Westerns
        $tourDetMaxDistScore	= '432';
        $tourDetNumDist = '2';
        $tourDetNumEnds = '8';
        $DistanceInfoArray = array(array(8, 6), array(8, 6));
        break;
    case 6: //Warwicks
        $tourDetMaxDistScore	= '216';
        $tourDetNumDist = '2';
        $tourDetNumEnds = '4';
        $DistanceInfoArray = array(array(4, 6), array(4, 6));
        break;
    case 7: //StNicholas
        $tourDetMaxDistScore	= '432';
        $tourDetNumDist = '2';
        $tourDetNumEnds = '5';
        $DistanceInfoArray = array(array(8, 6), array(6, 6));
        break;
    case 8: //OnTarget
        $tourDetMaxDistScore	= '240';
        $tourDetNumDist = '3';
        $tourDetNumEnds = '4';
        $DistanceInfoArray = array(array(4, 6), array(4, 6), array(4, 6));
        break;
    case 9: //ShortMetrics
        $tourDetMaxDistScore	= '360';
        $tourDetNumDist = '2';
        $tourDetNumEnds = '6';
        $DistanceInfoArray = array(array(6, 6), array(6, 6));
        break;
    case 10: //LongMetrics
        $tourDetMaxDistScore	= '360';
        $tourDetNumDist = '2';
        $tourDetNumEnds = '6';
        $DistanceInfoArray = array(array(6, 6), array(6, 6));
        break;
    case 11: //Worcester
        $tourDetMaxDistScore	= '150';
        $tourDetNumDist = '2';
        $tourDetNumEnds = '6';
        $DistanceInfoArray = array(array(6, 5), array(6, 5));
        break;
    case 12: //Bray 1
    case 13: //Bray 2
        $tourDetMaxDistScore = '270';
        $tourDetNumDist = '1';
        $tourDetNumEnds = '10';
        $DistanceInfoArray = array(array(10, 3));
        break;
    case 14: //Stafford
        $tourDetMaxDistScore	= '324';
        $tourDetNumDist = '2';
        $tourDetNumEnds = '12';
        $DistanceInfoArray = array(array(12, 3),array(12, 3));
        break;
    case 15: //Portsmouth
        $tourDetMaxDistScore	= '300';
        $tourDetNumDist = '2';
        $tourDetNumEnds = '10';
        $DistanceInfoArray = array(array(10, 3),array(10, 3));
        break;




}

//set golds/xnine labels and charicters

if($SubRule < 11){
    if($SubRule < 8){ //set for anything outdoor, but not OnTarget
        $tourDetGolds			= 'Hits';
        $tourDetXNine			= 'Golds';
        $tourDetGoldsChars		= 'BDFHJ';
        $tourDetXNineChars		= 'J';
    } else {//Set for OnTarget/Metric rounds
        $tourDetGolds = '10+X';
        $tourDetXNine = 'X';
        $tourDetGoldsChars = 'KL';
        $tourDetXNineChars = 'K';
    }
    $tourDetCategory		= '1'; // 0: Other, 1: Outdoor, 2: Indoor, 4:Field, 8:3D
}
else{
    if ($SubRule == 11){ //set for Worcester Rounds
        $tourDetGolds			= '5';
        $tourDetXNine			= 'X';
        $tourDetGoldsChars		= 'F';
        $tourDetXNineChars		= 'Z';
    } else {
        $tourDetGolds			= 'Hits';
        $tourDetXNine			= 'Golds';
        $tourDetGoldsChars		= 'BCDEFGHIJL';
        $tourDetXNineChars		= 'L';
    }
    $tourDetCategory		= '2'; // 0: Other, 1: Outdoor, 2: Indoor, 4:Field, 8:3D}
}

//other generic settings we can leave here

$tourDetTypeName		= 'Type_LocalUK';
$tourDetMaxFinIndScore	= '135';
$tourDetMaxFinTeamScore	= '216';
$tourDetElabTeam		= '0'; // 0:Standard, 1:Field, 2:3DI
$tourDetElimination		= '0'; // 0: No Eliminations, 1: Elimination Allowed
$tourDetDouble			= '0';

require_once('Setup_Target.php');

?>