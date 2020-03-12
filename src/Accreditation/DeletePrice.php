<?php
/*
													- DeletePrice.php -
	Elimina una coppia DivClass da EventClass
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession() || !isset($_REQUEST['DelDivCl']))
	{
		print get_text('CrackError');
		exit;
	}
    checkACL(AclCompetition, AclReadWrite,false);

	$Errore=0;

	if (!IsBlocked(BIT_BLOCK_ACCREDITATION))
	{
		$Delete
			= "DELETE FROM AccPrice "
			. "WHERE APDivClass=" . StrSafe_DB($_REQUEST['DelDivCl']) . " "
			. "AND APTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
		$Rs=safe_w_sql($Delete);
		if (debug) print $Delete;

		if (safe_w_affected_rows()!=1)
			$Errore=1;
	}
	else
		$Errore=1;
	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<divcl>' . $_REQUEST['DelDivCl'] . '</divcl>' . "\n";
	print '</response>' . "\n";
?>