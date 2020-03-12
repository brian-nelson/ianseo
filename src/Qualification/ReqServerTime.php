<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession() || !isset($_REQUEST['When'])) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclQualification, AclReadOnly,false);

	if (!is_numeric($_REQUEST['When']) || $_REQUEST['When']<0)
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;

	$mkNow=time();	// Ora attuale
	$mkRet=$mkNow-$_REQUEST['When']*60;
	$TxtHour = date('H:i',$mkRet);

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>';
	print '<error>' . $Errore . '</error>';
	print '<hour>' . $TxtHour . '</hour>';
	print '</response>';
?>