<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclCompetition, AclReadWrite, false);


	$Errore=0;

	if (!isset($_REQUEST['EvCode']) || !isset($_REQUEST['EvMixed'])) {
		$Errore=1;
	} else {
		$Update
			= "UPDATE Events SET "
			. "EvMixedTeam=" . StrSafe_DB($_REQUEST['EvMixed']) . " "
			. "WHERE EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EvTeamEvent='1' AND "
			. "EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
		$Rs=safe_w_sql($Update);
		if(safe_w_affected_rows())
			MakeTeamsAbs(null,null,null);
	}

	header('Content-Type: text/xml');

	print '<response>';
	print '<error>' . $Errore . '</error>';
	print '</response>';
?>