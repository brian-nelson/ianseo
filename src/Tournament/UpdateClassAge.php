<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Tournament/Fun_Tournament.local.inc.php');

	if (!CheckTourSession() ||
		!isset($_REQUEST['ClId']) ||
		!isset($_REQUEST['Age']) ||
		!isset($_REQUEST['FromTo']) ||
		($_REQUEST['FromTo']!='From' && $_REQUEST['FromTo']!='To'))
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;

	if (!IsBlocked(BIT_BLOCK_TOURDATA) && !defined('dontEditClassDiv')) {
		$Age = $_REQUEST['Age'];
		$ClId = $_REQUEST['ClId'];

		if (!is_numeric($Age)) {
			$Errore=1;
		} else {
			$ClDivAllowed=(empty($_REQUEST['AlDivs']) ? '' : $_REQUEST['AlDivs']);
			if (!CheckClassAge($ClId,$Age,$_REQUEST['FromTo'], $ClDivAllowed)) $Errore=1;
		}

		if (!$Errore) {
			$Update
				= "UPDATE Classes SET "
				. "ClAge" . $_REQUEST['FromTo'] . "=" . StrSafe_DB($Age) . " "
				. ", ClDivisionsAllowed=" . StrSafe_DB($ClDivAllowed) . " "
				. "WHERE ClId=" . StrSafe_DB($ClId) . " AND ClTournament=" . StrSafe_DB($_SESSION['TourId']) . "";
			$Rs=safe_w_sql($Update);

			$err=safe_w_error();
			if($err->errno!=0) {
				$Errore=1;
			}
		}
	} else {
		$Errore=1;
	}

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<clid>' . $_REQUEST['ClId'] . '</clid>' . "\n";
	print '<fromto>' . $_REQUEST['FromTo'] . '</fromto>' . "\n";
	print '</response>' . "\n";
?>