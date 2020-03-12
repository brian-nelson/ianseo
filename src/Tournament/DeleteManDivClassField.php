<?php
/*
													- UpdateFieldEventList.php -
	Aggiorna il campo di Events passato in querystring.
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
    checkACL(AclCompetition, AclReadWrite, false);

	if (!CheckTourSession() || !isset($_REQUEST['Tab']) || !isset($_REQUEST['Id']))
	{
		print get_text('CrackError');
		exit;
	}

/*
	- $Arr_Tables
	Array di lookup per le tabelle.
	Alla chiave corrisponde un vettore formato da:
		La tabella, i 2 campi di questa da usare come chiave per la delete
*/
	$Arr_Tables = array
	(
		'D' => array('Divisions','DivId','DivTournament'),
		'C' => array('Classes','ClId','ClTournament'),
		'SC'=> array('SubClass','ScId','ScTournament')
	);

	$Errore=0;
	$Which='#';

	if (!IsBlocked(BIT_BLOCK_TOURDATA) && !defined('dontEditClassDiv'))
	{
		if (!array_key_exists($_REQUEST['Tab'],$Arr_Tables))
			$Errore=1;
		else
		{
			$tt=$Arr_Tables[$_REQUEST['Tab']][0];	// tabella su cui fare l'update
			$kk=$Arr_Tables[$_REQUEST['Tab']][1];	// campo 1 da usare come chiave per l'update
			$kk2=$Arr_Tables[$_REQUEST['Tab']][2];	// campo 2 da usare come chiave per l'update

			$Which=$_REQUEST['Id'];

			$Delete
				= "DELETE FROM " . $tt . " "
				. "WHERE " . $kk . "="  .StrSafe_DB($_REQUEST['Id']) . " AND " . $kk2 . "=" . StrSafe_DB($_SESSION['TourId']) . " ";
			$Rs=safe_w_sql($Delete);

			if (debug) print $Delete;

			if (!$Rs)
				$Errore=1;
		}
	}
	else
		$Errore=1;

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<which>' . $Which . '</which>' . "\n";
	print '<tab>' . $_REQUEST['Tab'] . '</tab>' . "\n";
	print '</response>' . "\n";
?>