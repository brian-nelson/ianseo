<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Tournament/Fun_Tournament.local.inc.php');
    checkACL(AclCompetition, AclReadWrite, false);

	if (!CheckTourSession() ||
		!isset($_REQUEST['ClId']) ||
		!isset($_REQUEST['ClList']))
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;

	if (!IsBlocked(BIT_BLOCK_TOURDATA) && !defined('dontEditClassDiv'))
	{
		$ClId = $_REQUEST['ClId'];
		$StrList=CreateValidClass($ClId, $_REQUEST['ClList']);
		if (debug) print $StrList;

		$Update
			= "UPDATE Classes SET "
			. "ClValidClass=" . StrSafe_DB($StrList) . " "
			. "WHERE ClId=" . StrSafe_DB($ClId)
			. " AND ClTournament=" . StrSafe_DB($_SESSION['TourId']);
		safe_w_sql($Update);
		
		if(safe_w_affected_rows()) {
			safe_w_sql("UPDATE Classes SET ClTourRules='' WHERE ClId=" . StrSafe_DB($ClId) . " AND ClTournament=" . StrSafe_DB($_SESSION['TourId']));
		}
	}
	else
		$Errore=1;

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<clid>' . $_REQUEST['ClId'] . '</clid>' . "\n";
	print '<valid>' . $StrList . '</valid>' . "\n";
	print '</response>' . "\n";
?>