<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/pdf/BackNoPDF.php');
	require_once('Common/Fun_FormatText.inc.php');
	checkACL(AclEliminations, AclReadOnly);

	$pdf = new BackNoPDF($_REQUEST['BackNo']);

	$MyQuery
		= "SELECT "
			. "ElEventCode, "
			. "ElTargetNo,EnName,EnFirstName, upper(EnFirstName) EnFirstNameUpper, "
			. " CoCode, CoName "
		. "FROM "
			. "Eliminations INNER JOIN Entries ON ElId=EnId "
			. "INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament "
		. "WHERE "
			. "EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
		if (isset($_REQUEST['Elim']))
		{
			$MyQuery.=" AND ElElimPhase=" . StrSafe_DB($_REQUEST['Elim']) . " ";
		}
		if (isset($_REQUEST['Event']))
		{
			$MyQuery.=" AND ElEventCode = " . StrSafe_DB($_REQUEST['Event']) . " ";
		}
		$MyQuery
			.="ORDER BY "
			. "ElElimPhase ASC, ElEventCode ASC ,ElTargetNo ASC ";
	//print $MyQuery;exit;
	//*DEBUG*/echo $MyQuery;exit();
	$Rs=safe_r_sql($MyQuery);
// Se il Recordset � valido e contiene almeno una riga
	if(safe_num_rows($Rs)>0)
	{
		$CntBackNo=0;
		$NumEnd = (!empty($_REQUEST['x_Session']) && $_REQUEST['x_Session']==1 ? 8 : 12);
		while($MyRow=safe_fetch($Rs))
		{
			$Targetno = intval(substr($MyRow->ElTargetNo,0,-1));
			$BisValue='';
			if($Targetno > $NumEnd)
			{
				$Targetno -= $NumEnd;
				$BisValue='bis';

				if($Targetno > $NumEnd) {
					$Targetno -= $NumEnd;
					$BisValue='ter';
				}
			}
			$Targetno.=substr($MyRow->ElTargetNo,-1);
			$pdf->DrawElements(
				(empty($Targetno) ? '' : $Targetno),
				(empty($MyRow) ? '' : $MyRow),
				$CntBackNo,
				$BisValue
				);
			$CntBackNo = ++$CntBackNo % 2;
		}
		safe_free_result($Rs);
	}

	if($pdf->BackGroundFile) unlink($pdf->BackGroundFile);

	$pdf->Output();
?>
