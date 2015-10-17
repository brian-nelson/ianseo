<?php
require_once(dirname(__FILE__) . '/config.php');

if(empty($_GET['distnum']) and empty($_GET['sesstarget']) and empty($_GET['endnum'])) die();

require_once('Common/Lib/ArrTargets.inc.php');

$TargetNo=getGroupedTargets($_GET['sesstarget']);
$Distance=intval($_GET['distnum']);
$End=intval($_GET['endnum']);

$json_array=array();


$tmp=explode('|', $TargetNo);
if(count($tmp)==3) {
	// ELIMINATION
	$SQL="select
			ElScore DistScore,
			ElGold DistGold,
			ElXnine DistXnine,
			substr(ElArrowstring, 1+(EvElimArrows*($End-1)), EvElimArrows) Arrowstring,
			ElScore QuScore, ElGold QuGold, ElXnine QuXnine, concat('{$tmp['0']}|{$tmp['1']}|', ElTargetNo) as QuTargetNo,
			ToGoldsChars, ToXNineChars
		 from Eliminations
		 inner join Events on ElEventCode=EvCode and ElTournament=EvTournament and EvTeamEvent=0
		 inner join Tournament on ElTournament=ToId
		 where
		 	left(ElTargetNo, 3) in ('{$tmp[2]}')
		 	and ElEventCode='{$tmp[1]}'
		 	AND ElElimPhase=".($tmp[0][1]-1)."
		 	and ElTournament=$CompId
		 order by ElTargetNo
		";
// 	debug_svela($SQL);
} else {
	// QUALIFICATION
	$SQL="select
			QuD{$Distance}Score DistScore,
			QuD{$Distance}Gold DistGold,
			QuD{$Distance}Xnine DistXnine,
			substr(QuD{$Distance}Arrowstring, 1+(DiArrows*($End-1)), DiArrows) Arrowstring,
			QuScore, QuGold, QuXnine, QuTargetNo,
			ToGoldsChars, ToXNineChars
		 from Qualifications
		 inner join Entries on EnId=QuId and EnTournament=$CompId
		 inner join DistanceInformation on DiTournament=$CompId and DiSession=QuSession and DiDistance=$Distance and DiType='Q'
		 inner join Tournament on ToId=$CompId
		 where left(QuTargetNo, 4) in ('$TargetNo')
		 order by QuTargetNo
		";
}

$q=safe_r_sql($SQL);
while($r=safe_fetch($q)) {
	list($Score, $Golds, $Xnine) = ValutaArrowStringGX($r->Arrowstring, $r->ToGoldsChars, $r->ToXNineChars);
	$json_array[]=array(
		'qutarget' => $r->QuTargetNo,
		'endscore' => $Score,
		'endgolds' => $Golds,
		'endxnine' => $Xnine,
		'curscore' => $r->DistScore,
		'curgolds' => $r->DistGold,
		'curxnine' => $r->DistXnine,
		'score' => $r->QuScore,
		'golds' => $r->QuGold,
		'xnine' => $r->QuXnine,
	);
}


// Return the json structure with the callback function that is needed by the app
SendResult($json_array);
