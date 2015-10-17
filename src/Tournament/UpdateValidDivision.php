<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Tournament/Fun_Tournament.local.inc.php');

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
		$StrList=CreateValidDivision($_REQUEST['ClList']);

		$Update
			= "UPDATE Classes SET "
			. "ClDivisionsAllowed=" . StrSafe_DB($StrList) . " "
			. "WHERE ClId=" . StrSafe_DB($_REQUEST['ClId']) . " AND ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
		$Rs=safe_w_sql($Update);
		if (!$Rs)
			$Errore=1;
	}
	else
		$Errore=1;

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<clid>' . $_REQUEST['ClId'] . '</clid>' . "\n";
	print '<valid><![CDATA[' . $StrList . ']]></valid>' . "\n";
	print '</response>' . "\n";
?>