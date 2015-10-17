<?php
	require_once(dirname(__FILE__) . '/config.php');
	require_once('Common/Lib/Fun_Phases.inc.php');

	$json_array=array();

	$Types=array('I','T','E1','E2');

	$EventType=(!empty($_GET['type']) && in_array($_GET['type'],$Types) ? $_GET['type'] : '');

	switch($EventType) {
		case 'T':
			$SQL="SELECT EvCode, EvEventName, EvFinalFirstPhase FROM Events WHERE EvTournament=$CompId AND EvTeamEvent=1 AND EvFinalFirstPhase!=0 order by EvProgr";
			break;
		case 'E1':
			$SQL="SELECT EvCode, EvEventName, EvFinalFirstPhase FROM Events WHERE EvTournament=$CompId AND EvTeamEvent=0 and EvElim1>0 AND EvFinalFirstPhase!=0 order by EvProgr";
			break;
		case 'E2':
			$SQL="SELECT EvCode, EvEventName, EvFinalFirstPhase FROM Events WHERE EvTournament=$CompId AND EvTeamEvent=0 and EvElim2>0 AND EvFinalFirstPhase!=0 order by EvProgr";
			break;
		default:
			$SQL="SELECT EvCode, EvEventName, EvFinalFirstPhase FROM Events WHERE EvTournament=$CompId AND EvTeamEvent=0 AND EvFinalFirstPhase!=0 order by EvProgr";
	}

	// Retrieve the Event List
	$q=safe_r_sql($SQL);
	while($r=safe_fetch($q)) {
		$tmpPhases = Array();
		if($EventType[0]=='E') {
			$t=safe_r_sql("select distinct left(ElTargetNo, 3) TargetNo
				from Eliminations
				where ElTournament=$CompId
					and ElElimPhase=".($EventType[1]-1)."
					and ElEventCode='$r->EvCode'
					and ElTargetNo>''
				order by TargetNo");
			while($u=safe_fetch($t)) {
				$tmpPhases[]=Array("code"=>"{$EventType}|{$r->EvCode}|{$u->TargetNo}", "name"=>(int)$u->TargetNo);
			}
		} else {
			$phases = getPhasesId($r->EvFinalFirstPhase);
			foreach ($phases as $ph) {
				$tmpPhases[]=Array("code"=>bitwisePhaseId($ph), "name"=>$ph."_Phase");
			}
		}
		$json_array[] = Array("code"=>$r->EvCode, "name"=>$r->EvEventName, "phases"=>$tmpPhases);
	}


	// Return the json structure with the callback function that is needed by the app
	SendResult($json_array);

