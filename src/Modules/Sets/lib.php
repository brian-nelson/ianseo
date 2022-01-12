<?php
/*

Types of tournament are:

TtId TtType               TtDistance
 1   WA FITA              4
 2   WA 2xFITA            8
 3   WA 70m Round         2
18   WA FITA+50           0
 4   WA FITA 72           4
 5   WA 900 Round         3
 6   WA Indoor 18         2
 7   WA Indoor 25         2
 8   WA Indoor 25+18      4
 9   WA HF 12+12          1
12   WA HF 12+12          2
10   WA HF 24+24          2
11   WA 3D                1
13   WA 3D                2

14   NFAA Las Vegas       4
32   NFAA_Indoor          2

15   ITA GiochiGioventu   2
16   ITA GiochiGioventuW  2
19   ITA GiochiStudentes  1
31   ITA Sperimental      2
33   ITA TrofeoCONI       1

17   NOR Field            0
22   NOR Indoor 18        1

20   SWE ForestRound      0

21   NED Face2Face        0

23   BEL 25m_Out          2
24   BEL 50-30_Out        2
25   BEL 50_Out           2
26   BEL B10_Out          2
27   BEL B15_Out          2
28   BEL B25_Out          2
29   BEL B50-30_Out       2
30   BEL BFITA_Out        4


*/


define('TGT_IND_1_big10', 1);
define('TGT_IND_6_big10', 2);
define('TGT_IND_1_small10', 3);
define('TGT_IND_6_small10', 4);
define('TGT_OUT_FULL', 5);
define('TGT_FIELD', 6);
define('TGT_HITMISS', 7);
define('TGT_3D', 8);
define('TGT_OUT_5_big10', 9);
define('TGT_OUT_6_big10', 10);
define('TGT_NOR_HUN', 11);
define('TGT_SWE_FORREST', 12);
define('TGT_IND_NFAA', 13);
define('TGT_KYUDO', 19);

define('MATCH_ALL_SEP', 0);
define('MATCH_SEP_FROM_32', 128);
define('MATCH_SEP_FROM_16', 192);
define('MATCH_SEP_FROM_8', 224);
define('MATCH_SEP_FROM_4', 240);
define('MATCH_SEP_FROM_2', 248);
define('MATCH_SEP_MEDALS', 252);
define('MATCH_SEP_GOLD', 254);
define('MATCH_NO_SEP', 255);

define('FINAL_NO_ELIM', 0);
define('FINAL_FROM_32', 128);
define('FINAL_FROM_16', 192);
define('FINAL_FROM_8', 224);
define('FINAL_FROM_4', 240);
define('FINAL_FROM_2', 248);
define('FINAL_MEDALS', 252);
define('FINAL_GOLD', 254);
define('FINAL_ALL', 255);


function CreateDivision($TourId, $Order, $Id, $Description, $Athlete='1', $RecDiv='', $WaDiv='', $IsPara=false) {
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
		. ", DivIsPara=".intval($IsPara)
		);
}

function CreateClass($TourId, $Order, $From, $To, $Sex, $Id, $ValidClass, $Description, $Athlete='1', $AlDivision='', $RecCl='', $WaCl='', $IsPara=false) {
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
		. ", ClIsPara=".intval($IsPara)
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

function CreateDistanceNew($TourId, $Type, $Classes, $Distances=array()) {
	$SQL='';
	foreach($Distances as $k => $Distance) {
		$SQL.=", Td".($k+1)."=".StrSafe_DB($Distance[0]).", TdDist".($k+1)."=".intval($Distance[1]);
	}
	safe_w_sql("INSERT INTO TournamentDistances set TdTournament=$TourId, TdType=$Type, TdClasses=".StrSafe_DB($Classes) . $SQL);
}

function CreateEvent($TourId, $Order, $Team, $MixTeam, $FirstPhase, $TargetType, $ElimEnds, $ElimArrows, $ElimSO, $FinEnds, $FinArrows, $FinSO, $Code, $Description, $SetMode=0, $MatchArrows=0, $AthTarget=0, $ElimRound1=array(), $ElimRound2=array(), $RecCategory='', $WaCategory='', $tgtSize=0, $shootingDist=0, $parentEvent='', $MultipleTeam=0, $Selected=0, $EvWinnerFinalRank=1, $CreationMode=0, $MultipleTeamNo=0, $PartialTeam=0) {
	if(!$RecCategory) $RecCategory=$Code;
	if(!$WaCategory) $WaCategory=$Code;
	$Elim1=(empty($ElimRound1) ? 0 : $ElimRound1['Archers']);
	$Elim2=(empty($ElimRound2) ? 0 : $ElimRound2['Archers']);
	$ElEnd1=(empty($ElimRound1) ? $ElimEnds : $ElimRound1['Ends']);
	$ElEnd2=(empty($ElimRound2) ? $Elim2 : $ElimRound2['Ends']);
	$ElArr1=(empty($ElimRound1) ? $ElimArrows : $ElimRound1['Arrows']);
	$ElArr2=(empty($ElimRound2) ? $ElimArrows : $ElimRound2['Arrows']);
	$ElSO1=(empty($ElimRound1) ? $ElimSO : $ElimRound1['SO']);
	$ElSO2=(empty($ElimRound2) ? $ElimSO : $ElimRound2['SO']);
	$ElimType=0;
	if(!empty($ElimRound2)) $ElimType=1;
	if(!empty($ElimRound1)) $ElimType=2;
	$MultipleTeam=intval($MultipleTeam);
	safe_w_sql("INSERT INTO Events set "
		. " EvTournament=$TourId"
		. ", EvProgr=$Order"
		. ", EvTeamEvent=$Team"
		. ", EvMultiTeam=$MultipleTeam"
        . ", EvMultiTeamNo=$MultipleTeamNo"
        . ", EvPartialTeam=$PartialTeam"
		. ", EvMixedTeam=$MixTeam"
		. ", EvFinalFirstPhase=$FirstPhase"
		. ", EvWinnerFinalRank=$EvWinnerFinalRank"
		. ", EvNumQualified=".($Selected ? $Selected : numQualifiedByPhase($FirstPhase))
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
		. ", EvE1Ends=$ElEnd1"
		. ", EvE1Arrows=$ElArr1"
		. ", EvE1SO=$ElSO1"
		. ", EvElim2=$Elim2"
		. ", EvE2Ends=$ElEnd2"
		. ", EvE2Arrows=$ElArr2"
		. ", EvE2SO=$ElSO2"
        . ", EvTeamCreationMode=".intval($CreationMode)
		. ", EvRecCategory=" . StrSafe_DB($RecCategory)
		. ", EvWaCategory=".StrSafe_DB($WaCategory)
		. ", EvCodeParent=".StrSafe_DB($parentEvent)
		. ", EvElimType=".StrSafe_DB($ElimType)
		);
}

function CreateEventNew($TourId, $Code, $Description, $Order, $Options) {
	global 	$tourDetGolds, $tourDetXNine, $tourDetGoldsChars, $tourDetXNineChars;

	$Defaults=array(
		'EvTeamEvent' => 0,
		'EvFinalFirstPhase' => 2,
		'EvWinnerFinalRank' => 1,
		'EvNumQualified'=>4,
		'EvFirstQualified'=>1,
		'EvFinalTargetType'=>0,
		'EvTargetSize'=>40,
		'EvDistance'=>0,
		'EvFinalAthTarget'=>0,
		'EvElimType'=>0,
		'EvElim1'=>0,
		'EvE1Ends'=>0,
		'EvE1Arrows'=>0,
		'EvE1SO'=>0,
		'EvElim2'=>0,
		'EvE2Ends'=>0,
		'EvE2Arrows'=>0,
		'EvE2SO'=>0,
		'EvPartialTeam'=>0,
		'EvMultiTeam'=>0,
        'EvMultiTeamNo'=>0,
		'EvMixedTeam'=>0,
		'EvTeamCreationMode'=>0,
		'EvMaxTeamPerson'=>1,
		'EvMatchMode'=>0,
		'EvMatchArrowsNo'=>0,
		'EvElimEnds'=>0,
		'EvElimArrows'=>0,
		'EvElimSO'=>0,
		'EvFinEnds'=>0,
		'EvFinArrows'=>0,
		'EvFinSO'=>0,
		'EvRecCategory'=>$Code,
		'EvWaCategory'=>$Code,
		'EvMedals'=>1,
		'EvTourRules'=>'',
		'EvCodeParent'=>'',
		'EvGolds' => $tourDetGolds,
		'EvXNine' => $tourDetXNine,
		'EvGoldsChars' => $tourDetGoldsChars,
		'EvXNineChars' => $tourDetXNineChars,
		'EvIsPara' => 0,
	);
	foreach($Defaults as $def => $val) {
		if(!isset($Options[$def])) {
			$Options[$def]=$val;
		} elseif($def=='EvFinalFirstPhase' and !isset($Options['EvNumQualified'])) {
			$Options['EvNumQualified']=numQualifiedByPhase($Options['EvFinalFirstPhase']);
		}
	}
	$Query=array();
	$Query[]="EvTournament=$TourId";
	$Query[]="EvCode=" . StrSafe_DB($Code);
	$Query[]="EvEventName=".StrSafe_DB($Description);
	$Query[]="EvProgr=$Order";
	foreach($Options as $k=>$v) {
		$Query[]="$k=".StrSafe_DB($v);
	}

	safe_w_sql("INSERT INTO Events set ". implode(',', $Query));
}

function InsertClassEvent($TourId, $Team, $Number, $Code, $Division, $Class, $SubClass='') {
    $q1 = safe_r_SQL("SELECT EvCode FROM Events WHERE EvCode=".StrSafe_DB($Code)." AND EvTeamEvent='".($Team!=0 ? '1':'0')."' AND EvTournament={$TourId}");
	$q2 = safe_r_SQL("SELECT ClId FROM Classes WHERE ClId=".StrSafe_DB($Class)." AND ClTournament={$TourId}");
	$q3 = safe_r_SQL("SELECT DivId FROM Divisions WHERE DivId=".StrSafe_DB($Division)." AND DivTournament={$TourId}");
    if(safe_num_rows($q1)!=0 AND safe_num_rows($q2)!=0 AND safe_num_rows($q3)!=0) {
        safe_w_sql("INSERT INTO EventClass set "
            . " EcTournament=$TourId"
            . ", EcTeamEvent=$Team"
            . ", EcNumber=$Number"
            . ", EcCode=" . StrSafe_DB($Code)
            . ", EcDivision=" . StrSafe_DB($Division)
            . ", EcClass=" . StrSafe_DB($Class)
            . ", EcSubClass=" . StrSafe_DB($SubClass)
        );
    }
}

function CreateFinals($TourId) {
	CreateFinalsInd($TourId);
	CreateFinalsTeam($TourId);
}

/**
 * @param $TourId
 * @param string $StrEv2Delete [optional] SQL-escaped string that goes in the IN () statement
 */
function CreateFinalsInd($TourId, $StrEv2Delete='') {
	$Insert = "INSERT INTO Finals (FinEvent, FinMatchNo, FinTournament, FinDateTime) 
		SELECT EvCode, GrMatchNo, EvTournament, " . StrSafe_DB(date('Y-m-d H:i:s')) . "  
		FROM Events 
		INNER JOIN Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2,EvTeamEvent))>0
		INNER JOIN Grids ON GrPhase<=greatest(PhId, PhLevel) 
		WHERE EvTournament=$TourId AND EvTeamEvent='0'".($StrEv2Delete ? " AND EvCode IN ($StrEv2Delete)" : "");
	safe_w_sql($Insert);
}

/**
 * @param $TourId
 * @param string $StrEv2Delete [optional] SQL-escaped string that goes in the IN () statement
 */
function CreateFinalsTeam($TourId, $StrEv2Delete='') {
	$Insert = "INSERT INTO TeamFinals (TfEvent, TfMatchNo, TfTournament, TfDateTime) 
		SELECT EvCode, GrMatchNo, EvTournament," . StrSafe_DB(date('Y-m-d H:i:s')) . " 
		FROM Events 
		INNER JOIN Phases ON EvFinalFirstPhase=PhId and (PhIndTeam & pow(2, EvTeamEvent))>0
		INNER JOIN Grids ON GrPhase<=greatest(PhId,PhLevel) 
		WHERE EvTournament=$TourId AND EvTeamEvent='1'".($StrEv2Delete ? " AND EvCode IN ($StrEv2Delete)" : "");
	safe_w_sql($Insert);
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

function CreateDistanceInformation($TourId, $Distances, $Targets=0, $Athletes=4, $Session=1, $SesName='') {
	require_once('Tournament/Fun_ManSessions.inc.php');
	if($Targets) {
		insertSession($TourId, $Session, 'Q', $SesName, $Targets, $Athletes, 1, 0);
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
