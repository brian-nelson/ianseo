<?php
function CreateDivision($TourId, $Order, $Id, $Description, $Athlete='1', $RecDiv='', $WaDiv='') {
	if(!$RecDiv) $RecDiv=$Id;
	if(!$WaDiv) $WaDiv=$Id;
	safe_w_sql("INSERT INTO Divisions set "
		. " DivTournament=$TourId"
		. ", DivViewOrder=".StrSafe_DB($Order)
		. ", DivId=".StrSafe_DB($Id)
		. ", DivDescription=".StrSafe_DB($Description)
		. ", DivAthlete=".StrSafe_DB(intval($Athlete))
		. ", DivRecDivision=".StrSafe_DB($RecDiv)
		. ", DivWaDivision=".StrSafe_DB($WaDiv)
		);
}

function CreateClass($TourId, $Order, $From, $To, $Sex, $Id, $ValidClass, $Description, $Athlete='1', $AlDivision='', $RecCl='', $WaCl='') {
	if(!$RecCl) $RecCl=$Id;
	if(!$WaCl) $WaCl=$Id;
	safe_w_sql("INSERT INTO Classes set "
		. " ClTournament=$TourId"
		. ", ClViewOrder=".StrSafe_DB($Order)
		. ", ClAgeFrom=".StrSafe_DB($From)
		. ", ClAgeTo=".StrSafe_DB($To)
		. ", ClSex=".StrSafe_DB($Sex)
		. ", ClId=".StrSafe_DB($Id)
		. ", ClValidClass=".StrSafe_DB($ValidClass)
		. ", ClDescription=".StrSafe_DB($Description)
		. ", ClAthlete=".StrSafe_DB(intval($Athlete))
		. ", ClDivisionsAllowed=".StrSafe_DB($AlDivision)
		. ", ClRecClass=".StrSafe_DB($RecCl)
		. ", ClWaClass=".StrSafe_DB($WaCl)
		);
}

function CreateSubClass($TourId, $Order, $Id, $Description) {
	safe_w_sql("INSERT INTO SubClass set "
		. " ScTournament=$TourId"
		. ", ScViewOrder=".StrSafe_DB($Order)
		. ", ScId=".StrSafe_DB($Id)
		. ", ScDescription=".StrSafe_DB($Description)
		);
}

function CreateDistance($TourId, $Type, $Classes, $D1='', $D2='', $D3='', $D4='', $D5='', $D6='', $D7='', $D8='') {
	safe_w_sql("INSERT INTO TournamentDistances set "
		. " TdTournament=$TourId"
		. ", TdType=$Type"
		. ", TdClasses=".StrSafe_DB($Classes)
		. ", Td1=".StrSafe_DB($D1)
		. ", Td2=".StrSafe_DB($D2)
		. ", Td3=".StrSafe_DB($D3)
		. ", Td4=".StrSafe_DB($D4)
		. ", Td5=".StrSafe_DB($D5)
		. ", Td6=".StrSafe_DB($D6)
		. ", Td7=".StrSafe_DB($D7)
		. ", Td8=".StrSafe_DB($D8)
		);
}

function CreateEvent($TourId, $Order, $Team, $MixTeam, $FirstPhase, $TargetType, $ElimEnds, $ElimArrows, $ElimSO, $FinEnds, $FinArrows, $FinSO, $Code, $Description, $SetMode=0, $MatchArrows=0, $AthTarget=0, $Elim1=0, $Elim2=0, $RecCategory='', $WaCategory='', $tgtSize=0, $shootingDist=0) {
	if(!$RecCategory) $RecCategory=$Code;
	if(!$WaCategory) $WaCategory=$Code;
	safe_w_sql("INSERT INTO Events set "
		. " EvTournament=$TourId"
		. ", EvProgr=$Order"
		. ", EvTeamEvent=$Team"
		. ", EvMixedTeam=$MixTeam"
		. ", EvFinalFirstPhase=$FirstPhase"
		. ", EvFinalTargetType=$TargetType"
		. ", EvTargetSize=$tgtSize"
		. ", EvDistance=$shootingDist"
		. ", EvElimEnds=$ElimEnds"
		. ", EvElimArrows=$ElimArrows"
		. ", EvElimSO=$ElimSO"
		. ", EvFinEnds=$FinEnds"
		. ", EvFinArrows=$FinArrows"
		. ", EvFinSO=$FinSO"
		. ", EvCode=" . StrSafe_DB($Code)
		. ", EvEventName=".StrSafe_DB($Description)
		. ", EvMatchMode=$SetMode"
		. ", EvMatchArrowsNo=$MatchArrows"
		. ", EvFinalAthTarget=$AthTarget"
		. ", EvElim1=$Elim1"
		. ", EvElim2=$Elim2"
		. ", EvRecCategory=" . StrSafe_DB($RecCategory)
		. ", EvWaCategory=".StrSafe_DB($WaCategory)
		);
}

function InsertClassEvent($TourId, $Team, $Number, $Code, $Division, $Class) {
	safe_w_sql("INSERT INTO EventClass set "
		. " EcTournament=$TourId"
		. ", EcTeamEvent=$Team"
		. ", EcNumber=$Number"
		. ", EcCode=" . StrSafe_DB($Code)
		. ", EcDivision=".StrSafe_DB($Division)
		. ", EcClass=".StrSafe_DB($Class)
		);
}

function CreateFinals($TourId) {
	safe_w_sql("INSERT INTO Finals (FinEvent,FinMatchNo,FinTournament) SELECT EvCode,GrMatchNo,EvTournament FROM Events INNER JOIN Grids ON GrPhase<=IF(EvFinalFirstPhase=48,64,EvFinalFirstPhase) WHERE EvTournament=$TourId AND EvTeamEvent='0'");
	safe_w_sql("INSERT INTO TeamFinals (TfEvent,TfMatchNo,TfTournament) SELECT EvCode,GrMatchNo,EvTournament FROM Events INNER JOIN Grids ON GrPhase<=IF(EvFinalFirstPhase=48,64,EvFinalFirstPhase) WHERE EvTournament=$TourId AND EvTeamEvent='1'");
}

function CreateTargetFace($TourId, $Id, $Name, $Classes, $Default, $T1, $W1, $T2=0, $W2=0, $T3=0, $W3=0, $T4=0, $W4=0, $T5=0, $W5=0, $T6=0, $W6=0, $T7=0, $W7=0, $T8=0, $W8=0) {
	$Field='TfClasses';
	if(substr($Classes, 0, 4)=='REG-') {
		$Field='TfRegExp';
		$Classes=substr($Classes, 4);
	}
	safe_w_sql("INSERT INTO TargetFaces set "
		. " TfTournament=$TourId"
		. ", TfId=$Id"
		. ", TfName=" . StrSafe_DB($Name)
		. ", $Field=" . StrSafe_DB($Classes)
		. ", TfDefault=" . StrSafe_DB($Default)
		. ", TfT1=$T1"
		. ", TfW1=$W1"
		. ", TfT2=$T2"
		. ", TfW2=$W2"
		. ", TfT3=$T3"
		. ", TfW3=$W3"
		. ", TfT4=$T4"
		. ", TfW4=$W4"
		. ", TfT5=$T5"
		. ", TfW5=$W5"
		. ", TfT6=$T6"
		. ", TfW6=$W6"
		. ", TfT7=$T7"
		. ", TfW7=$W7"
		. ", TfT8=$T8"
		. ", TfW8=$W8"
		);
}

function CreateDistanceInformation($TourId, $Distances, $Targets=0, $Athletes=4, $Session=1) {
	require_once('Tournament/Fun_ManSessions.inc.php');
	if($Targets) {
		insertSession($TourId, 1, 'Q', '', $Targets, $Athletes, 1, 0);
	}
	foreach($Distances as $Dist => $Infos) {
		safe_w_sql("insert into DistanceInformation set DiTournament=$TourId, DiType='Q', DiSession=$Session, DiDistance=$Dist+1, DiEnds={$Infos[0]}, DiArrows={$Infos[1]} ON DUPLICATE KEY UPDATE DiEnds={$Infos[0]}, DiArrows={$Infos[1]} ");
	}
}
/*
$Collations=array(
	'czech',
	'danish',
	'esperanto',
	'estonian',
	'hungarian',
	'icelandic',
	'latvian',
	'lithuanian',
	'persian',
	'polish',
	'roman',
	'romanian',
	'slovak',
	'slovenian',
	'spanish2',
	'spanish',
	'swedish',
	'turkish'
	);

*/

function UpdateTourDetails($TourId, $Field, $Value='') {
	if(!is_array($Field)) $Field=array($Field => $Value);

	$q=array();
	foreach($Field as $k=>$v) {
		$q[]=$k.'='.StrSafe_DB($v);
	}

	safe_w_sql("update Tournament set ".implode(',', $q)." where ToId=$TourId");
}
?>