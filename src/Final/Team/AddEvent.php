<?php
/*
													- UpdateFieldEventList.php -
	Aggiorna il campo di Events passato in querystring.
*/

	define('debug',false);

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
    require_once('Common/Lib/Fun_Phases.inc.php');


	if (!CheckTourSession() ||
		!isset($_REQUEST['New_EvCode']) ||
		!isset($_REQUEST['New_EvEventName']) ||
		!isset($_REQUEST['New_EvProgr']) ||
		!isset($_REQUEST['New_EvMatchMode']) ||
		!isset($_REQUEST['New_EvFinalFirstPhase']) ||
		!isset($_REQUEST['New_EvFinalTargetType']) ||
		!isset($_REQUEST['New_EvTargetSize']) ||
		!isset($_REQUEST['New_EvDistance']))
	{
		print get_text('CrackError');
		exit;
	}
    checkACL(AclCompetition, AclReadWrite, false);

    $JSON=array('error' => 1);

    if (IsBlocked(BIT_BLOCK_TOURDATA)) {
        JsonOut($JSON);
    }

    $Insert
        = "INSERT INTO Events (EvCode,EvTeamEvent,EvTournament,EvEventName,EvProgr,EvShootOff,EvFinalFirstPhase, EvNumQualified, EvFinalTargetType,EvTargetSize,EvDistance,EvMatchMode) "
        . "VALUES("
        . StrSafe_DB($_REQUEST['New_EvCode']) . ","
        . StrSafe_DB('1') . ","
        . StrSafe_DB($_SESSION['TourId']) . ","
        . StrSafe_DB($_REQUEST['New_EvEventName']) . ","
        . StrSafe_DB($_REQUEST['New_EvProgr']) . ","
        . StrSafe_DB('0') . ","
        . StrSafe_DB($_REQUEST['New_EvFinalFirstPhase']) . ","
        . numQualifiedByPhase($_REQUEST['New_EvFinalFirstPhase']) . ","
        . StrSafe_DB($_REQUEST['New_EvFinalTargetType']) . ", "
        . StrSafe_DB($_REQUEST['New_EvTargetSize']) . ", "
        . StrSafe_DB($_REQUEST['New_EvDistance']) . ", "
        . StrSafe_DB($_REQUEST['New_EvMatchMode']) . " "
        . ") ";
    $RsIns=safe_w_sql($Insert);

    if(!safe_w_affected_rows()) {
        JsonOut($JSON);
    }

    $JSON['error']=0;
    set_qual_session_flags();

    $values=array(
        0 => "EvElimEnds=4,EvElimArrows=6,EvElimSO=3,EvFinEnds=4,EvFinArrows=6,EvFinSO=3 ",
        1 => "EvElimEnds=4,EvElimArrows=6,EvElimSO=3,EvFinEnds=6,EvFinArrows=6,EvFinSO=3 "
    );

    $MySql = "UPDATE "
        . "Events "
        . "SET "
        . $values[$_REQUEST['New_EvMatchMode']]
        . "WHERE "
        . "EvTeamEvent=1 AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode=" . StrSafe_DB($_REQUEST['New_EvCode']);
    $Rs=safe_w_sql($MySql);

    // Creo la griglia
    if($_REQUEST['New_EvFinalFirstPhase']!=0) {
	    $Insert
	        = "INSERT INTO TeamFinals (TfEvent,TfMatchNo,TfTournament,TfDateTime)  "
	        . "SELECT EvCode,GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . "," . StrSafe_DB(date('Y-m-d H:i:s')) . " "
	        . "FROM Events 
	        inner join Phases on PhId=EvFinalFirstPhase and (PhIndTeam & 2)=2
	        INNER JOIN Grids ON GrPhase<=greatest(PhId, PhLevel) AND EvTeamEvent=1 "
	        . "AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
	        . "WHERE EvCode=" . StrSafe_DB($_REQUEST['New_EvCode']) . " ";
        safe_w_sql($Insert);
    }

    $JSON['new_evcode']=$_REQUEST['New_EvCode'];


    JsonOut($JSON);