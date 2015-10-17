<?php
/*
															- CalcRank.php -
	Calcola la rank (anche l'abs).
	Se riceve Dist=1,2,.... calcola la rank sulla distanza Dist; se non lo riceve, calcola la rank sul totale
*/
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Fun_Qualification.local.inc.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/Obj_RankFactory.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;

	if (!IsBlocked(BIT_BLOCK_QUAL))
	{
		$Dist=(isset($_REQUEST['Dist']) ? $_REQUEST['Dist'] : 0);

		$Errore=CalcRank($Dist);
	}
	else
		$Errore=1;

	// produco l'xml di ritorno
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<msg>' . ($Errore==1 ? get_text('CalcRankError','Tournament') : get_text('CalcRankOk','Tournament')) . '</msg>';
	print '</response>' . "\n";
?>