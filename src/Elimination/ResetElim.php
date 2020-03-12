<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Elimination/Fun_Eliminations.local.inc.php');
	require_once('Common/Lib/CommonLib.php');

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclEliminations, AclReadWrite, false);

	$error=0;
	$xml='';

	$event=isset($_REQUEST['event']) ? $_REQUEST['event'] : null;

	if (is_null($event)) {
		$error=1;
	} else {
	// elimino le rige di Eliminations
        safe_w_sql("DELETE FROM Eliminations WHERE ElTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND ElEventCode=" . StrSafe_DB($event));
        safe_w_sql("DELETE FROM Finals WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent = " . StrSafe_DB($event));

        $q=safe_r_sql("SELECT EvElimType FROM Events WHERE EvCode='{$event}' AND EvTournament={$_SESSION['TourId']} AND EvTeamEvent=0");
        $r=safe_fetch($q);

        if($r->EvElimType==1 OR $r->EvElimType==2) {
            // ricreo le griglie eliminatorie
            $x=CreateElimRows($event,1);
            $y=CreateElimRows($event,2);
        } elseif($r->EvElimType==3) {
            $query
                = "INSERT INTO Finals (FinEvent,FinMatchNo,FinTournament,FinDateTime) "
                . "SELECT EvCode,GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . "," . StrSafe_DB(date('Y-m-d H:i')) . " "
                . "FROM Events INNER JOIN Grids ON GrMatchNo in (".implode(',', getPoolMatchNos()).") AND EvTeamEvent='0' "
                . "AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
                . "WHERE EvCode = " . StrSafe_DB($event);
            //print $query . '<br><br>';
            $rs=safe_w_sql($query);
        } elseif($r->EvElimType==4) {
            $query
                = "INSERT INTO Finals (FinEvent,FinMatchNo,FinTournament,FinDateTime) "
                . "SELECT EvCode,GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . "," . StrSafe_DB(date('Y-m-d H:i')) . " "
                . "FROM Events INNER JOIN Grids ON GrMatchNo in (".implode(',', getPoolMatchNosWA()).") AND EvTeamEvent='0' "
                . "AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
                . "WHERE EvCode = " . StrSafe_DB($event);
            //print $query . '<br><br>';
            $rs=safe_w_sql($query);
        }
        $query
            = "INSERT INTO Finals (FinEvent,FinMatchNo,FinTournament,FinDateTime) "
            . "SELECT EvCode,GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . "," . StrSafe_DB(date('Y-m-d H:i')) . " "
            . "FROM Events INNER JOIN Grids ON GrPhase<=EvFinalFirstPhase AND EvTeamEvent='0' "
            . "AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
            . "WHERE EvCode = " . StrSafe_DB($event);
        //print $query . '<br><br>';
        $rs=safe_w_sql($query);
        if (!ResetShootoff($event,0,0)) {
            $error = 1;
        }


	}

	$xml
		.='<response>'
			. '<error>' . $error . '</error>'
			. '<event>' . $event . '</event>'
		. '</response>';

	header('Content-Type: text/xml; charset=UTF-8');
	print $xml;
?>