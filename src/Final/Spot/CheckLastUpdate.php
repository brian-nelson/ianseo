<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Final/Spot/Common/Config.inc.php');

	$Errore=0;
	$Reload=0;

	if (!isset($_REQUEST['LastUpdate']) or empty($_REQUEST['TourId']) or !($TourId=intval($_REQUEST['TourId']))) {
		$Errore=1;
	} else {
        checkACL(AclOutput,AclReadOnly, false, $TourId);
		$q=safe_r_sql("(Select FinDateTime LastUpdate from Finals where FinTournament={$TourId} and FinDateTime>'{$_REQUEST['LastUpdate']}')"
			. " UNION "
			. "(Select TfDateTime LastUpdate from TeamFinals where TfTournament={$TourId} and TfDateTime>'{$_REQUEST['LastUpdate']}')"
			. " Order by LastUpdate desc"
			. " limit 1");
		if($r=safe_fetch($q) and $r->LastUpdate>$_REQUEST['LastUpdate']) {
			$Reload=$r->LastUpdate;
		}

	}

	header('Content-Type: text/xml');

	print '<response>' ;
	print '<error>' . $Errore . '</error>';
	print '<lu>' . $Reload . '</lu>';
	print '</response>' ;
?>