<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
    checkACL(AclCompetition, AclReadWrite, false);

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;
	$availableCat = array();

	if (!IsBlocked(BIT_BLOCK_TOURDATA))
	{
		$delete
			= "DELETE FROM TournamentDistances "
			. "WHERE TdTournament={$_SESSION['TourId']} AND TdClasses=" . StrSafe_DB($_REQUEST['cl']) . " AND TdType=" . StrSafe_DB($_REQUEST['type']) . " ";
		$rs=safe_w_sql($delete);

		if (!$rs)
			$Errore=1;
		
		$select = "SELECT DivId, CONCAT(DivId,ClId) grDivCl
			FROM Divisions
			INNER JOIN Classes ON DivTournament=ClTournament
			LEFT JOIN TournamentDistances ON DivTournament=TdTournament AND  CONCAT(DivId,ClId) LIKE TdClasses
			WHERE DivTournament={$_SESSION['TourId']}
			AND ClAthlete=DivAthlete AND DivAthlete=1 AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed))
			AND TdClasses IS NULL
			ORDER BY DivViewOrder, ClViewOrder";
		$rs=safe_r_sql($select);
		while($row = safe_fetch($rs)) {
			if(!array_key_exists($row->DivId,$availableCat))
				$availableCat[$row->DivId] = array();
			$availableCat[$row->DivId][] = '<a name="" onclick="document.getElementById(\'TdClasses\').value=\''.$row->grDivCl.'\'">'.$row->grDivCl.'</a>';
		}
	}
	else
		$Errore=1;

	if (!debug)
		header('Content-Type: text/xml');
	print '<response>';
	print '<error>' . $Errore . '</error>';
	print '<row>' . $_REQUEST['row'] . '</row>';
	print '<q>' . $delete . '</q>';
	$tmp = array();
	foreach ($availableCat as $k=>$v)
		$tmp[] = implode(",&nbsp;", $v);
	print '<avb><![CDATA[' . implode("<br>", $tmp) . ']]></avb>';
	print '</response>';
?>