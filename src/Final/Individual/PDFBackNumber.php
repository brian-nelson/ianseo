<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/pdf/BackNoPDF.php');
	checkACL(AclIndividuals, AclReadOnly);

	$pdf = new BackNoPDF(1);


	$MyQuery = 'SELECT '
	    . ' EvCode, EvEventName, EvFinalFirstPhase, '
        . ' IF(EvFinalFirstPhase=48, GrPosition2, if(GrPosition>EvNumQualified, 0, GrPosition)) as GridPosition, EnName, EnFirstName, upper(EnFirstName) EnFirstNameUpper,'
        . ' CoCode, CoName'
        . ' FROM Events'
        . ' INNER JOIN Phases ON PhId=EvFinalFirstPhase and (PhIndTeam & 1)=1 '
        . ' INNER JOIN Finals ON EvCode=FinEvent AND EvTournament=FinTournament'
        . ' INNER JOIN Grids ON FinMatchNo=GrMAtchNo AND GrPhase=greatest(PhId, PhLevel) '
        . ' LEFT JOIN Entries ON FinAthlete=EnId AND FinTournament=EnTournament'
        . ' LEFT JOIN Countries on EnCountry=CoId AND EnTournament=CoTournament'
        . ' WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=0 AND IF(EvFinalFirstPhase=48, GrPosition2, if(GrPosition>EvNumQualified, 0, GrPosition))>0 ';
	if (isset($_REQUEST['Event']) && preg_match("/^[0-9A-Z]+$/i",$_REQUEST["Event"]))
		$MyQuery.= "AND EvCode LIKE '" . $_REQUEST['Event'] . "' ";
	if (isset($_REQUEST['noByes']))
		$MyQuery.= "AND FinAthlete != 0 ";
	$MyQuery .= ' ORDER BY EvCode, FinMatchNo';


	$Rs=safe_r_sql($MyQuery);
// Se il Recordset � valido e contiene almeno una riga
	if(safe_num_rows($Rs)>0)
	{
		$CntBackNo=0;
		while($MyRow=safe_fetch($Rs))
		{
			$pdf->DrawElements(
				(empty($MyRow->GridPosition) ? '' : $MyRow->GridPosition),
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
