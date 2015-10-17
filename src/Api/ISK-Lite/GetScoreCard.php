<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/ArrTargets.inc.php');

$QuTarget=$_REQUEST['qutarget'];
$json_array = Array();

$Sql=array();
for($n=1;$n<=8;$n++) {
	$Sql[]="SELECT QuD{$n}ArrowString DiArrowstring, DiDistance, DiEnds, DiArrows, Td{$n} DiName, ToGoldsChars, ToXNineChars
	FROM Qualifications
	INNER JOIN Entries on EnId = Quid and EnTournament=$CompId
	INNER JOIN Tournament ON ToId=$CompId
	INNER join DistanceInformation on DiTournament=$CompId and DiSession=QuSession and DiDistance=$n and DiType='Q'
	INNER JOIN TournamentDistances ON ToType=TdType and TdTournament=$CompId AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE TdClasses
	WHERE QuTargetNo=" . StrSafe_DB($QuTarget);
}

$SQL='('.implode(') UNION (', $Sql).') order by DiDistance';
// debug_svela($SQL);
// Retrieve the score info
$Rs=safe_r_sql($SQL);

while($r=safe_fetch($Rs)) {
	$Arrows=$r->DiArrows;
	$Ends=$r->DiEnds;
	$ArrowString=str_pad(rtrim($r->DiArrowstring), $Arrows*$Ends);

	$RealEnds=array();
	while(strlen($ArrowString)) {
		$RealEnds[]=substr($ArrowString, 0, $Arrows);
		$ArrowString=substr($ArrowString, $Arrows);
	}

	if($Arrows>3 and $Arrows%3) {
		// arrows per end are more than 6 and not multiple of 3
		// so ends will be reduced to max 6 arrows
		$tmp=ceil($Arrows/6);
		$Arrows=ceil($Arrows/$tmp);
	} else {
		$tmp=ceil($Arrows/3);
		$Arrows=ceil($Arrows/$tmp);
	}
	$Distance=array(
		'distancename' => $r->DiName,
		'endarrows' => $Arrows,
		'endscores' => array()
	);
	$EndNum=1;
	foreach($RealEnds as $RealEnd) {
		$RealEnd=str_pad($RealEnd, $Arrows);
		while(strlen($RealEnd)) {
			$End=substr($RealEnd, 0, $Arrows);
			$EndArray=array(
				'endnum' => $EndNum,
				'arrowscores' => array(),
				'endtotal' => 0,
				'endgolds' => 0,
				'endxnine' => 0);
			foreach(range(0, $Arrows-1) as $Arrow) {
				$EndArray['arrowscores'][]=DecodeFromLetter(substr($End, $Arrow, 1));
			}
			list($EndArray['endtotal'],$EndArray['endgolds'],$EndArray['endxnine']) = ValutaArrowStringGX($End, $r->ToGoldsChars, $r->ToXNineChars);
			$EndNum++;
			$Distance['endscores'][]=$EndArray;
			$RealEnd=substr($RealEnd, $Arrows);
		}
	}
	$json_array[]=$Distance;
}

// Return the json structure with the callback function that is needed by the app
SendResult($json_array);

