<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
    checkACL(AclCompetition, AclReadWrite, false);

	if (!CheckTourSession() ||
		!isset($_REQUEST['type']) ||
		!isset($_REQUEST['cl']))
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;
	$affectedCat = array();
	$availableCat = array();
	$tds=array();

	if (!IsBlocked(BIT_BLOCK_TOURDATA)) {
		if ($_REQUEST['cl']!='') {

			$x=1;
			foreach($_REQUEST as $k=>$v) {
				if (substr($k,0,2)=='td')
					$tds[$k]=($v!='' ? $v : '.' . $x++ . '.');
			}

		// verifico se esiste una possibile div/cl per la regola
			$select
				= "SELECT DivId, ClId "
				. "FROM Divisions INNER JOIN Classes ON DivTournament=ClTournament "
				. "WHERE "
					. "CONCAT(DivId,ClId) LIKE " . StrSafe_DB($_REQUEST['cl']) . " AND "
					. "DivTournament=" . StrSafe_DB($_SESSION['TourId']) .  " AND "
					. "ClAthlete=DivAthlete AND DivAthlete=1 AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed)) "
					. "ORDER BY DivViewOrder, ClViewOrder";
			$rs=safe_r_sql($select);

			if (safe_num_rows($rs)==0) {
				$Errore=1;
			} else {
				while($row = safe_fetch($rs)) {
					if(!array_key_exists($row->DivId,$affectedCat))
						$affectedCat[$row->DivId] = array();
					$affectedCat[$row->DivId][] = $row->ClId;
				}
			// verifico che la regola non sia già inclusa in un'altra
				$select = "SELECT TdClasses FROM TournamentDistances "
					. "WHERE "
						. "TdType=" . StrSafe_DB($_REQUEST['type']) . " AND "
						. "TdTournament={$_SESSION['TourId']} AND "
						. "(TdClasses LIKE " . StrSafe_DB($_REQUEST['cl']) . " OR " . StrSafe_DB($_REQUEST['cl']) . "LIKE TdClasses) ";
				$rs=safe_r_sql($select);

                if (safe_num_rows($rs)!=0) {
                    $Errore=2;
                } else {
                    $distFields="";
                    $distValues="";

                    $replace = "REPLACE INTO TournamentDistances (TdTournament, TdClasses,TdType,";
                    foreach ($tds as $k => $v) {
                        $distFields.=$k . ",";
                        $distValues.=StrSafe_DB($v) . ",";
                    }
                    $distFields=substr($distFields,0,-1);
                    $distValues=substr($distValues,0,-1);

                    $replace .=$distFields . ") VALUES('{$_SESSION['TourId']}', " . StrSafe_DB($_REQUEST['cl']) . "," . StrSafe_DB($_REQUEST['type']) . "," . $distValues . ") ";

                    //print $replace;exit;
                    $rs=safe_w_sql($replace);

                    $select = "SELECT DivId, CONCAT(DivId,ClId) grDivCl 
                        FROM Divisions 
                        INNER JOIN Classes ON DivTournament=ClTournament 
                        LEFT JOIN TournamentDistances ON DivTournament=TdTournament AND  CONCAT(DivId,ClId) LIKE TdClasses
                        WHERE DivTournament={$_SESSION['TourId']} 
                        AND ClAthlete=DivAthlete AND DivAthlete=1 AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed)) 
                        AND TdClasses IS NULL
                        ORDER BY DivViewOrder, ClViewOrder";
                    //echo $select;exit;
                    $rs=safe_r_sql($select);
                    while($row = safe_fetch($rs)) {
                        if(!array_key_exists($row->DivId,$availableCat)) {
                            $availableCat[$row->DivId] = array();
                        }
                        $availableCat[$row->DivId][] = '<a onclick="document.getElementById(\'TdClasses\').value=\''.$row->grDivCl.'\'">'.$row->grDivCl.'</a>';
                    }
                }
			}
		} else
			$Errore=4;
	} else
		$Errore=5;

    header('Content-Type: text/xml');

	print '<response>';
	print '<error>' . $Errore . '</error>';
	print '<cl>' . $_REQUEST['cl'] . '</cl>';
	print '<type>' . $_REQUEST['type'] . '</type>';
	print '<num_dist>' . $_REQUEST['numDist'] . '</num_dist>';
	foreach ($tds as $v) {
		print '<td>' . $v . '</td>';
	}
	$tmp = array();
	foreach ($affectedCat as $k=>$v) {
        $tmp[] = $k . "; <b>" . implode("&nbsp;", $v) . "</b>";
    }
	print '<aff><![CDATA[' . implode("<br>", $tmp) . ']]></aff>';
	$tmp = array();
	foreach ($availableCat as $k=>$v) {
        $tmp[] = implode(",&nbsp;", $v);
    }
	print '<avb><![CDATA[' . implode("<br>", $tmp) . ']]></avb>';
	print '</response>';