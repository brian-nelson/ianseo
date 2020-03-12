<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/ArrTargets.inc.php');

$QuTarget=$_REQUEST['qutarget'];
$json_array = Array();

$tmp=explode('|', $QuTarget);
if(count($tmp)==3) {
	// ELIMINATION
	$SQL="select 'E' as Type, ElArrowString DiArrowstring, '' DiDistance, if(ElElimPhase=0, EvE1Ends, EvE2Ends) DiEnds, if(ElElimPhase=0, EvE1Arrows, EvE2Arrows) DiArrows, '' DiName, ToGoldsChars, ToXNineChars, ToGolds, ToXNine
		FROM Eliminations
		INNER JOIN Entries on EnId = ElId and EnTournament=$CompId
		INNER JOIN Tournament ON ToId=$CompId
		INNER JOIN Events ON ElTournament=EvTournament and ElEventCode=EvCode and EvTeamEvent=0
		WHERE ElElimPhase=".($tmp[0][1]-1)." and ElEventCode=".StrSafe_DB($tmp[1])." and ElTargetNo=" . StrSafe_DB($tmp[2]);
} else {
	// QUALIFICATION
	$Sql=array();
	for($n=1;$n<=8;$n++) {
		$Sql[]="SELECT 'Q' as Type, QuD{$n}ArrowString DiArrowstring, DiDistance, DiEnds, DiArrows, Td{$n} DiName, ToGoldsChars, ToXNineChars, ToGolds, ToXNine
		FROM Qualifications
		INNER JOIN Entries on EnId = Quid and EnTournament=$CompId
		INNER JOIN Tournament ON ToId=$CompId
		INNER join DistanceInformation on DiTournament=$CompId and DiSession=QuSession and DiDistance=$n and DiType='Q'
		INNER JOIN TournamentDistances ON ToType=TdType and TdTournament=$CompId AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE TdClasses
		WHERE QuTargetNo=" . StrSafe_DB($QuTarget);
	}

	$SQL='('.implode(') UNION (', $Sql).') order by DiDistance';
}

	// Retrieve the score info
	$Rs=safe_r_sql($SQL);

	while($r=safe_fetch($Rs)) {
		$Arrows=$r->DiArrows;
		$Ends=$r->DiEnds;
		$ArrowString=str_pad(rtrim($r->DiArrowstring), $Arrows*$Ends);

		$SQL = "SELECT IskDtEndNo, IskDtArrowstring
			FROM IskData
			WHERE IskDtTournament={$CompId} AND IskDtMatchNo=0 AND IskDtEvent='' AND IskDtTeamInd=0 AND IskDtType='{$r->Type}' AND IskDtTargetNo='{$QuTarget}' AND IskDtDistance={$r->DiDistance}
			ORDER BY IskDtEndNo";
		$q = safe_r_SQL($SQL);
		while($r2 = safe_fetch($q)){
			for($i=0; $i<$Arrows; $i++){
				if($r2->IskDtArrowstring[$i]!=' '){
					$ArrowString[($r2->IskDtEndNo-1)*$Arrows+$i]=$r2->IskDtArrowstring[$i];
				}
			}
		}

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
			'goldschar' => $r->ToGolds,
			'xninechar' => $r->ToXNine,
			'endarrows' => $Arrows,
			'endscores' => array()
		);
		$EndNum=1;
		$GrandTotal=0;
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
				$GrandTotal+=$EndArray['endtotal'];
				$EndArray['runtotal']=$GrandTotal;
				$EndNum++;
				$Distance['endscores'][]=$EndArray;
				$RealEnd=substr($RealEnd, $Arrows);
			}
		}
		$json_array[]=$Distance;
	}



// Return the json structure with the callback function that is needed by the app
SendResult($json_array);

