<?php
	require_once(dirname(__FILE__) . '/config.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Lib/Fun_Phases.inc.php');

	$DistanceNum=!empty($_GET['distnum']) ? $_GET['distnum'] : 1;
	$EndNum=$_GET['endnum'];
	$TargetNo=!empty($_GET['qutarget']) ? $_GET['qutarget'] : 0;
	list($Event,$EventType,$MatchNo) = explode("|",(!empty($_GET['matchid']) ? $_GET['matchid'] : "0|0|0"));
	$EventType=($EventType=='T' ? 1 : 0);

	$ScoreRow=NULL;
	$json_array=array(
		'qutarget' => $TargetNo,
		'distnum' => $DistanceNum,
		'endnum' => $EndNum,
		'prevendscored' => false,
		);

	if($TargetNo) {
		$tmp=explode('|', $TargetNo);
		if(count($tmp)==3) {
			// ELIMINATION
			$Select	= "SELECT ElArrowString AS ArrowString, EvElimArrows DiArrows
				FROM Eliminations
				INNER JOIN Events on ElEventCode=EvCode and ElTournament=EvTournament and EvTeamEvent=0
				WHERE ElTargetNo=" . StrSafe_DB($tmp[2]) . "
					AND ElEventCode='{$tmp[1]}'
					AND ElElimPhase=".($tmp[0][1]-1)."
					AND ElTournament=$CompId";
		} else {
			// Qualification
			if($DistanceNum>1) {
				$PrevDist=$DistanceNum-1;
				$Select	= "SELECT QuD" . $DistanceNum . "ArrowString AS ArrowString, QuD" . $PrevDist . "ArrowString AS PrevArrowString, Di1.DiArrows, Di2.DiArrows PrevArrows, Di2.DiEnds PrevEnds
				FROM Qualifications
				INNER JOIN Entries on EnId=QuId
				INNER JOIN DistanceInformation Di1 on QuSession=Di1.DiSession and Di1.DiTournament=$CompId and Di1.DiDistance=$DistanceNum and Di1.DiType='Q'
				INNER JOIN DistanceInformation Di2 on QuSession=Di2.DiSession and Di2.DiTournament=$CompId and Di2.DiDistance=$PrevDist and Di2.DiType='Q'
				WHERE QuTargetNo=" . StrSafe_DB($TargetNo) . "
				AND EnTournament=$CompId";
			} else {
				$Select	= "SELECT QuD" . $DistanceNum . "ArrowString AS ArrowString, DiArrows
					FROM Qualifications
					INNER JOIN Entries on EnId=QuId
					INNER JOIN DistanceInformation on QuSession=DiSession and DiTournament=$CompId and DiDistance=$DistanceNum and DiType='Q'
					WHERE QuTargetNo=" . StrSafe_DB($TargetNo) . "
						AND EnTournament=$CompId";
			}
		}
	} else {
		//Get the phase relatedto the matchno
		$SQL="select GrPhase from Grids where GrMatchNo=$MatchNo";
		$Rs=safe_r_sql($SQL);
		if($r=safe_fetch($Rs))
			$Phase = $r->GrPhase;
		$objParam=getEventArrowsParams($Event,$Phase,$EventType,$CompId);

		$Select	= "SELECT " . ($EventType ? "Tf" : "Fin") . ($EndNum > $objParam->ends ? "TieBreak" : "ArrowString") . " AS ArrowString, " . ($EndNum>$objParam->ends ? $objParam->so : $objParam->arrows) . " AS DiArrows " . ($EndNum>$objParam->ends ? ", 1 as isSO" : "") . "
		FROM " . ($EventType ? "Team" : "") . "Finals
		WHERE " . ($EventType ? "Tf" : "Fin") . "Event=" . StrSafe_DB($Event) . " AND " . ($EventType ? "Tf" : "Fin") . "MatchNo=$MatchNo AND " . ($EventType ? "Tf" : "Fin") . "Tournament=$CompId";

	}

	// Retrieve the score info
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs) == 1) {
		// Now load the json array with the info we need
		$ScoreRow=safe_fetch($Rs);


		$StartPos = (empty($ScoreRow->isSO) ? ($EndNum-1) * $ScoreRow->DiArrows : 0);

		$arrow_array = Array();
		for($j=0; $j < $ScoreRow->DiArrows; $j++) {
			 array_push($arrow_array, DecodeFromLetter(substr($ScoreRow->ArrowString, $StartPos+$j, 1)) );
		}
		$json_array["arrowvalues"] = $arrow_array;
		$json_array["prevendscored"] = (
				($EndNum==1 and ($DistanceNum==1 or trim(substr($ScoreRow->PrevArrowString, ($ScoreRow->PrevEnds-1)*$ScoreRow->PrevArrows, $ScoreRow->PrevArrows)))
				 or (trim(substr($ScoreRow->ArrowString, $StartPos-$ScoreRow->DiArrows, $ScoreRow->DiArrows)))));
	}

	// Return the json structure with the callback function that is needed by the app
	SendResult($json_array);
