<?php
/*
															- MakeTeamsAbs.php - 
	Genera le squadre Assolute
*/

	define('debug',false);	// settare a true per l'output di debug
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');
    checkACL(AclQualification, AclReadWrite, false);

	$Errore=0;
	
	if(!IsBlocked(BIT_BLOCK_QUAL)) {
		$Errore	= MakeTeamsAbs(NULL,null,null);
	}
	else
		$Errore=1;
	// produco l'xml di ritorno

	if (!debug)
		header('Content-Type: text/xml');
		
	print '<response>';
	print '<error>' . $Errore . '</error>';
	print '<msg><![CDATA[' . get_text('ResultSqAbs','Tournament') . ($Errore==1 ? get_text('MakeTeamsError','Tournament') : get_text('MakeTeamsOk','Tournament')) . ']]></msg>';
	print '</response>';
?>