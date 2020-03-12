<?php
require_once(dirname(__FILE__) . '/config.php');

if(empty($_GET['distnum']) and empty($_GET['sesstarget']) and empty($_GET['endnum'])) die();

require_once('Common/Lib/ArrTargets.inc.php');

$TargetNo=getGroupedTargets((!empty($_GET['sesstarget']) ? $_GET['sesstarget'] : 0));
$Distance = (!empty($_GET['distnum']) ? $_GET['distnum'] : 1);
$End = (!empty($_GET['endnum']) ? $_GET['endnum'] : 1);

$json_array=array();


$tmp=explode('|', $TargetNo);
if(count($tmp)==3) {
	// ELIMINATION
	$SQL="select
			'E' as Type,
			ElTargetNo as TargetNo,
			ElScore DistScore,
			ElGold DistGold,
			ElXnine DistXnine,
			if(ElElimPhase=0, EvE1Arrows, EvE2Arrows) as ArrowsNo, 
			if(ElElimPhase=0, EvE1Ends, EvE2Ends) EvElimEnds, if(ElElimPhase=0, EvE1Arrows, EvE2Arrows) EvElimArrows
			substr(ElArrowstring, 1+(if(ElElimPhase=0, EvE1Arrows, EvE2Arrows)*($End-1)), if(ElElimPhase=0, EvE1Arrows, EvE2Arrows)) Arrowstring,
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
	$q=safe_r_sql($SQL);
	while($r=safe_fetch($q)) {
		$Score='';
		$Golds='';
		$Xnine='';
		if(!empty($r->Arrowstring)) {
			$ArrowString = $r->Arrowstring;
			$SQL = "SELECT IskDtEndNo, IskDtArrowstring
				FROM IskData
				WHERE IskDtTournament={$CompId} AND IskDtMatchNo=0 AND IskDtEvent='' AND IskDtTeamInd=0 AND IskDtType='{$r->Type}' AND IskDtTargetNo='{$r->TargetNo}' AND IskDtDistance={$Distance} AND IskDtEndNo={$End}
				ORDER BY IskDtEndNo";
			$q2 = safe_r_SQL($SQL);
			if($r2 = safe_fetch($q2)){
				for($i=0; $i<$r->ArrowsNo; $i++){
					if($r2->IskDtArrowstring[$i]!=' '){
						$ArrowString[$i]=$r2->IskDtArrowstring[$i];
					}
				}
			}
			list($Score, $Golds, $Xnine) = ValutaArrowStringGX($ArrowString, $r->ToGoldsChars, $r->ToXNineChars);
		}
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
			'xnine' => $r->QuXnine
		);
	}
} else {
	// QUALIFICATION
	$Filter="left(QuTargetNo,4) in ('".$TargetNo."')";

	$SQL="SELECT QuId, QuSession, QuTargetNo, DIDistance, DIEnds, DIArrows, ToGoldsChars, ToXNineChars from Qualifications
			INNER JOIN Entries ON QuId=EnId
			INNER JOIN Tournament ON ToId=EnTournament
			INNER JOIN DistanceInformation ON DITournament=EnTournament AND DISession=QuSession AND DIDistance=".StrSafe_DB($Distance)." AND DIType='Q'
			WHERE EnTournament=$CompId and $Filter 
			ORDER BY QuTargetNo";
	$q=safe_r_sql($SQL);
	while($r=safe_fetch($q)) {
		$tmp = getQualificationTotals($r->QuId, $r->DIDistance, $End, $r->DIArrows, $r->DIEnds, $r->ToGoldsChars, $r->ToXNineChars);
		$json_array[]=array(
			'qutarget' => $r->QuTargetNo,
			'endscore' => $tmp['curendscore'],
			'curscore' => $tmp['curscore'],
			'curgolds' => $tmp['curgold'],
			'curxnine' => $tmp['curxnine'],
			'score' => $tmp['score'],
			'golds' => $tmp['gold'],
			'xnine' => $tmp['xnine']
		);
	}
}




// Return the json structure with the callback function that is needed by the app
SendResult($json_array);
