<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;

	if (!IsBlocked(BIT_BLOCK_TOURDATA))
	{
		$delete
			= "DELETE FROM TournamentDistances "
			. "WHERE TdTournament={$_SESSION['TourId']} AND TdClasses=" . StrSafe_DB($_REQUEST['cl']) . " AND TdType=" . StrSafe_DB($_REQUEST['type']) . " ";
		$rs=safe_w_sql($delete);

		if (!$rs)
			$Errore=1;
	}
	else
		$Errore=1;

	if (!debug)
		header('Content-Type: text/xml');
	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<row>' . $_REQUEST['row'] . '</row>' . "\n";
	print '<q>' . $delete . '</q>' . "\n";
	print '</response>' . "\n";
?>