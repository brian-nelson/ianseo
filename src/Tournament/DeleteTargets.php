<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=2;

	if (!IsBlocked(BIT_BLOCK_TOURDATA)
		and $q=safe_r_sql("select * from TargetFaces where TfTournament={$_SESSION['TourId']}")
		and safe_num_rows($q)>1)
	{
		// targets can be deleted only if there are targets left!
		$delete
			= "DELETE FROM TargetFaces "
			. "WHERE TfTournament={$_SESSION['TourId']} AND TfId=" . intval($_REQUEST['tfid']) ;
		$rs=safe_w_sql($delete);

		$Errore=(safe_w_affected_rows() ? 0 : 2 );
	}

	if (!debug)
		header('Content-Type: text/xml');
	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<row>' . $_REQUEST['row'] . '</row>' . "\n";
	print '</response>' . "\n";
?>