<?php
/*
															- MakeTeams.php -
	Genera le squadre
*/

	define('debug',false);	// settare a true per l'output di debug
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');

	$Errore=0;

	if (!IsBlocked(BIT_BLOCK_QUAL))
	{
		$Errore	= MakeTeams(NULL, NULL);
	}
	else
		$Errore=1;

	// produco l'xml di ritorno

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<msg><![CDATA[' . get_text('ResultSqClass','Tournament') . "\n" . ($Errore==1 ? get_text('MakeTeamsError','Tournament') : get_text('MakeTeamsOk','Tournament')) . ']]></msg>';
	print '</response>' . "\n";


?>