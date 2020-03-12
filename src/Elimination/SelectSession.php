<?php
/*
													- SelectSession.php -
	Estrae il primo e l'ultimo target della sessione
*/

	require_once(dirname(dirname(__FILE__)) . '/config.php');

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclEliminations, AclReadOnly, false);

	$Errore=0;
	$First='';
	$Last='';

	if (isset($_REQUEST['Ses'])) {
		$Select
			= "SELECT SUBSTRING(MIN(ElTargetNo),1," . TargetNoPadding . ") AS Minimo, SUBSTRING(MAX(ElTargetNo),1," . TargetNoPadding . ") AS Massimo "
			. "FROM Eliminations INNER JOIN Entries ON ElId=EnId AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "WHERE ElElimPhase = " . StrSafe_DB($_REQUEST['Ses']) . " AND ElTargetNo!='' ";
		$Rs=safe_r_sql($Select);
		if (safe_num_rows($Rs)==1) {
			$MyRow=safe_fetch($Rs);
			$First=(!is_null($MyRow->Minimo) ? $MyRow->Minimo : '#');
			$Last=(!is_null($MyRow->Massimo) ? $MyRow->Massimo : '#');
		}
		else
			$Errore=1;
	}

	header('Content-Type: text/xml');

	print '<response>';
	print '<error>' . $Errore . '</error>';
	print '<minimo>' . $First . '</minimo>';
	print '<massimo>' . $Last . '</massimo>';
	print '</response>';
?>