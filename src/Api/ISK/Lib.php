<?php

function getGroupedTargets($TargetNo, $Session=0, $SesType='Q', $SesPhase='', $returnArray=false) {
	global $CompId;
	// get all targets associated/grouped together with the target requested
	if(empty($CompId)) {
		if(empty($_SESSION['TourId'])) return $TargetNo;

		$CompId=$_SESSION['TourId'];
	}

	$TargetToQuery=$TargetNo;
	if($returnArray and $SesType=='Q') {
		$TargetToQuery=$Session.str_pad($TargetNo, 3, '0', STR_PAD_LEFT);
	}

	$SubSelect="select TgGroup, TgSession, TgSesType
		from TargetGroups
		where TgTournament=$CompId
		and TgTargetNo='$TargetToQuery'";
	if($SesType!='Q') {
		$SubSelect.=" and TgSesType='{$SesType}{$SesPhase}'";
	}
	$Tmp=array();
	$q=safe_r_sql("Select TgTargetNo
		from TargetGroups
		where TgTournament=$CompId
		and (TgGroup, TgSession, TgSesType)=($SubSelect) order by TgTargetNo");
	while($r=safe_fetch($q)) {
		if($returnArray and $SesType=='Q') {
			$Tmp[]=intval(substr($r->TgTargetNo,1));
		} else {
			$Tmp[]=$r->TgTargetNo;
		}
	}
	if($Tmp and !$returnArray) {
		$TargetNo=implode("','", $Tmp);
	}

	if($returnArray) {
		if($Tmp) {
			return $Tmp;
		} else {
			return array($TargetNo);
		}
	}
	return $TargetNo;
}

function GetLockableSessions() {
	$SQL=array();

// QUALIFICATIONS
	$SQL[]="select 
       concat_ws('|','Q', QuSession, DiDistance) as LockKey,
       'Q' as SesType,
       SesName as Description,
       DiDistance as Distance,
       0 as FirstPhase,
       0 as Order1, SesOrder as Order2, DiDistance as Order3
	from Qualifications
	inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']}
	inner join DistanceInformation on DiTournament=EnTournament and DiSession=QuSession and DiType='Q'
	left join Session on SesTournament=EnTournament and SesType='Q' and SesOrder=QuSession
	where QuSession>0
	group by QuSession, DiDistance";

// ELIMINATIONS
	$SQL[]="select 
       concat_ws('|','E', ElElimPhase, ElEventCode) as LockKey,
       'E' as SesType,
       concat(SesName, ' - ', ElEventCode) as Description,
       1 as Distance,
       0 as FirstPhase,
       1 as Order1, ElElimPhase as Order2, EvProgr as Order3
	from Eliminations
	inner join Events on EvCode=ElEventCode and EvTeamEvent=0 and EvTournament=ElTournament
	left join Session on SesTournament=ElTournament and SesType='E' and SesOrder=ElSession
	where ElTournament={$_SESSION['TourId']}
	group by ElEventCode, ElElimPhase";

// Individual Matches
	$SQL[]="select 
       concat_ws('|','I',GrPhase,FinEvent) as LockKey, 
       'I' as SesType,
       EvEventName as Description,
       GrPhase as Distance,
       EvFinalFirstPhase as FirstPhase,
       3 as Order1, EvProgr as Order2, 128-GrPhase as Order3
	from Finals
	inner join Events on EvCode=FinEvent and EvTeamEvent=0 and EvTournament=FinTournament and EvFinalFirstPhase>0
	inner join Grids on GrMatchNo=FinMatchNo
	where FinTournament={$_SESSION['TourId']}
	group by GrPhase, FinEvent";

// Team Matches
	$SQL[]="select 
       concat_ws('|','T',GrPhase,TfEvent) as LockKey, 
       'T' as SesType,
       EvEventName as Description,
       GrPhase as Distance,
       EvFinalFirstPhase as FirstPhase,
       4 as Order1, EvProgr as Order2, 128-GrPhase as Order3
	from TeamFinals
	inner join Events on EvCode=TfEvent and EvTeamEvent=1 and EvTournament=TfTournament and EvFinalFirstPhase>0
	inner join Grids on GrMatchNo=TfMatchNo
	where TfTournament={$_SESSION['TourId']}
	group by GrPhase, TfEvent";

	return "(".implode(') UNION (', $SQL).") order by Order1, Order2, Order3";
}
