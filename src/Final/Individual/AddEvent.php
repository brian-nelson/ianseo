<?php
/*
													- UpdateFieldEventList.php -
	Aggiorna il campo di Events passato in querystring.
*/

define('debug',false);

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Common/Fun_Phases.inc.php');

checkACL(AclCompetition, AclReadWrite, false);

$JSON=array('error' => 1);

if (!CheckTourSession() ||
	!isset($_REQUEST['New_EvCode']) ||
	!isset($_REQUEST['New_EvEventName']) ||
	!isset($_REQUEST['New_EvProgr']) ||
	!isset($_REQUEST['New_EvMatchMode']) ||
	!isset($_REQUEST['New_EvFinalFirstPhase']) ||
	!isset($_REQUEST['New_EvFinalTargetType']) ||
	!isset($_REQUEST['New_EvTargetSize']) ||
	!isset($_REQUEST['New_EvDistance']) ||
	!isset($_REQUEST['New_EvElim1']) ||
	!isset($_REQUEST['New_EvElim2']))
{
	print get_text('CrackError');
	exit;
}

if (IsBlocked(BIT_BLOCK_TOURDATA)) {
	JsonOut($JSON);
}

// Aggiungo la nuova riga
$Insert = "INSERT INTO Events SET 
	EvCode=" . StrSafe_DB($_REQUEST['New_EvCode']) . ",
	EvTeamEvent=0,
	EvEventName=" . StrSafe_DB($_REQUEST['New_EvEventName']) . ",
	EvProgr=" . intval($_REQUEST['New_EvProgr']) . ",
	EvShootOff=0,
	EvFinalFirstPhase=" . StrSafe_DB($_REQUEST['New_EvFinalFirstPhase']) . ",
	EvFinalTargetType=" . StrSafe_DB($_REQUEST['New_EvFinalTargetType']) . ",
	EvTargetSize=" . StrSafe_DB($_REQUEST['New_EvTargetSize']) . ",
	EvDistance=" . StrSafe_DB($_REQUEST['New_EvDistance']) . ",
	EvElim1=" . StrSafe_DB($_REQUEST['New_EvElim1']) . ",
	EvElim2=" . StrSafe_DB($_REQUEST['New_EvElim2']) . ",
	EvMatchMode= " . StrSafe_DB($_REQUEST['New_EvMatchMode']) . ",
	EvNumQualified= " . numQualifiedByPhase(intval($_REQUEST['New_EvFinalFirstPhase'])) . ",
	EvFirstQualified=1,
	EvTournament={$_SESSION['TourId']}
	";
$RsIns=safe_w_sql($Insert);

if(!safe_w_affected_rows()) {
	JsonOut($JSON);
}

$JSON['error']=0;
set_qual_session_flags();

$values=array(
	0 => "EvElimEnds=5,EvElimArrows=3,EvElimSO=1,EvFinEnds=5,EvFinArrows=3,EvFinSO=1 ",
	1 => "EvElimEnds=5,EvElimArrows=3,EvElimSO=1,EvFinEnds=5,EvFinArrows=3,EvFinSO=1 "
);

$MySql = "UPDATE "
		. "Events "
	. "SET "
		. $values[$_REQUEST['New_EvMatchMode']]
	. "WHERE "
		. "EvTeamEvent=0 AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode=" . StrSafe_DB($_REQUEST['New_EvCode']);
$Rs=safe_w_sql($MySql);

// creo le griglie delle eliminatorie
for ($i=1;$i<=2;++$i) {
	if ($_REQUEST['New_EvElim'.$i]>0) {
		CreateElimRows($_REQUEST['New_EvCode'],$i);
	}
}

// Creo la griglia
$Insert
	= "INSERT INTO Finals (FinEvent,FinMatchNo,FinTournament,FinDateTime) "
	. "SELECT EvCode,GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . "," . StrSafe_DB(date('Y-m-d H:i:s')) . " "
	. "FROM Events 
	inner join Phases on PhId=EvFinalFirstPhase and (PhIndTeam & 1)=1
	INNER JOIN Grids ON GrPhase<=greatest(PhId, PhLevel) AND EvTeamEvent='0' "
	. "AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
	. "WHERE EvCode=" . StrSafe_DB($_REQUEST['New_EvCode']) . " ";
if($_REQUEST['New_EvFinalFirstPhase']!=0) {
	safe_w_sql($Insert);
}

$JSON['new_evcode']=$_REQUEST['New_EvCode'];


JsonOut($JSON);