<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/pdf/BackNoPDF.php');
	checkACL(AclTeams, AclReadOnly);

	$pdf = new BackNoPDF(2);

	$MyQuery = 'SELECT '
       	. ' EvCode, EvEventName, EvFinalFirstPhase, GrPosition, '
        . " CoCode, CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) AS CoName, EnFirstName, upper(EnFirstName) EnFirstNameUpper, EnName "
   	    . ' FROM Events '
       	. ' INNER JOIN TeamFinals ON EvCode=TfEvent AND EvTournament=TfTournament '
        . ' INNER JOIN Grids ON TfMatchNo=GrMatchNo AND GrPhase=EvFinalFirstPhase '
        . ' INNER JOIN Countries on TfTeam=CoId AND TfTournament=CoTournament '
        . ' INNER JOIN TeamFinComponent ON CoId=TfcCoId AND TfSubTeam=TfcSubTeam AND CoTournament=TfcTournament AND EvCode=TfcEvent '
        . ' INNER JOIN Entries ON TfcId=EnId AND TfcTournament=EnTournament '
        . ' WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=1 ';
	if (isset($_REQUEST['Event']) && preg_match("/^[0-9A-Z]+$/i",$_REQUEST["Event"]))
		$MyQuery.= "AND EvCode LIKE '" . $_REQUEST['Event'] . "' ";
	$MyQuery .= ' ORDER BY EvCode, TfMatchNo';
	//*DEBUG*/echo $MyQuery;exit();
	$Rs=safe_r_sql($MyQuery);
// Se il Recordset � valido e contiene almeno una riga
	if(safe_num_rows($Rs)>0)
	{
		$CntBackNo=0;
		while($MyRow=safe_fetch($Rs))
		{
			$pdf->DrawElements(
				(!is_null($MyRow->GrPosition) ? mb_strtolower($MyRow->GrPosition) : ' '),
				(empty($MyRow) ? '' : $MyRow),
				$CntBackNo
				);
			$CntBackNo = ++$CntBackNo % 2;
		}
		safe_free_result($Rs);
	}

if($pdf->BackGroundFile) unlink($pdf->BackGroundFile);

$pdf->Output();
?>
