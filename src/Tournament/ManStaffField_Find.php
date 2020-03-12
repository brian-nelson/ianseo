<?php
/*
													- ManStaffField_Find.php -
	La pagina riceve la 'Matr' che � la matricola da cercare e 'IdReturn che � l'id della casella di testo da completare.
	Se la query ritorna un solo risultato allora viene ritornato nell'xml
*/
	require_once(dirname(dirname(__FILE__)) . '/config.php');
    checkACL(AclCompetition, AclReadWrite, false);
	if (!isset($_REQUEST['Matr']) || !isset($_REQUEST['IdReturn']) || !CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}

	$Errore = 0;
	$UniqueRow = 0;
	$xml='';

	$Select
		= "SELECT LueCode,LueFamilyName,LueName "
		. "FROM LookUpEntries "
		. "WHERE LueCode=" . StrSafe_DB($_REQUEST['Matr']) . " ";
	$Rs=safe_r_sql($Select);
	if ($Rs)
	{
		if (safe_num_rows($Rs)==1)
		{
			$UniqueRow=1;
			$MyRow=safe_fetch($Rs);
			$xml
				.= '<name>' . $MyRow->LueFamilyName . ' ' . $MyRow->LueName . '</name>' . "\n";

		}
	}
	else
		$Errore=1;

// produco l'xml di ritorno
	header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<unique>' . $UniqueRow . '</unique>' . "\n";
	print $xml;
	print '<id_ret>' . $_REQUEST['IdReturn'] . '</id_ret>' . "\n";
	print '</response>' . "\n";
?>