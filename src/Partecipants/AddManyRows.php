<?php
/*
													- AddManyRows.php -
	Inserisce N righe vuote preparando i campi con i valori di defaults
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession() || !isset($_REQUEST['Num'])) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadWrite, false);

	$Errore = 1;

	if (!IsBlocked(BIT_BLOCK_PARTICIPANT)) {
		$t=safe_r_sql("select ToIocCode from Tournament WHERE ToId={$_SESSION['TourId']}");
		$u=safe_fetch($t);
		for($i=0; $i<intval($_REQUEST['Num']); $i++) {
			safe_w_sql("Insert into Entries set EnTournament='{$_SESSION['TourId']}', EnIocCode='{$u->ToIocCode}'");
			safe_w_sql("Insert into Qualifications set QuId=".safe_w_last_id());
			$Errore=0;
		}
	}

	header('Content-Type: text/xml');


	print '<response>';
	print '<error>' . $Errore . '</error>';
	print '</response>';
