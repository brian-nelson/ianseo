<?php
/*
													- ManStaffField_Edit.php -
	Rieceve Matr,Name,Type e Row per aggiornare la riga
*/
	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!isset($_REQUEST['Row']) || !isset($_REQUEST['Matr']) || !isset($_REQUEST['Name']) || !isset($_REQUEST['Type']) || !CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Update
		= "UPDATE TournamentInvolved SET "
		. "TiCode=" . StrSafe_DB($_REQUEST['Matr']) . ","
		. "TiName=" . StrSafe_DB($_REQUEST['Name']) . ","
		. "TiType=" . StrSafe_DB($_REQUEST['Type']) . " "
		. "WHERE TiId=" . StrSafe_DB($_REQUEST['Row']) . " ";
	$RsUp=safe_w_sql($Update);

	$Errore = 0;
// verifico che quello che c'� nel db dopo l'update sia uguale a quello passato in querystring
	$Select
		= "SELECT TiCode, TiName, TiType FROM TournamentInvolved WHERE TiId=" . StrSafe_DB($_REQUEST['Row']) . " ";
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs))
	{
		$Row=safe_fetch($Rs);
		if ($Row->TiCode!=$_REQUEST['Matr'] || $Row->TiName!=$_REQUEST['Name'] || $Row->TiType!=$_REQUEST['Type'])
			$Errore=1;
	}
	else
		$Errore=1;

// produco l'xml di ritorno
	header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<row>' . $_REQUEST['Row'] . '</row>' . "\n";
	print '</response>' . "\n";
?>