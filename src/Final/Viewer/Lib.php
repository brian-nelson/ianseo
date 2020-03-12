<?php
	//define ("TourId",42);	// id della gara di torino2008


/**
 * Cerca quale evento è live in questo momento
 * Se trova più eventi contemporanei ritorna i dati di quello con modifiche più recenti.
 *
 * @param Int $Team: vale 0 se l'evento è individuale e 1 se è a squadre.
 *
 * @return Array: Un vettore formato dall'Evento e dal MatchNo oppure false in caso di errore.
 * 					Se non ci sono eventi live viene ritornato array(NULL,NULL)
 */
	function FindLive($TourId=0, $Team=-1) {
		if ($Team==0) {
			$Select
				= "SELECT"
				. "  '0' Team"
				. " , FinDateTime DateTime"
				. " , FinEvent AS Event"
				. " , FinMatchNo AS MatchNo"
				. " FROM"
				. "  Finals "
				. " WHERE"
				. "  FinTournament=$TourId"
				. "  AND FinLive='1' "
				. "ORDER BY"
				. " Team,"
				. " DateTime DESC,"
				. " Event ASC,"
				. " MatchNo ASC ";
		} elseif ($Team==1) {
			$Select
				= "SELECT"
				. "  '1' Team "
				. " , TfDateTime DateTime"
				. " , TfEvent AS Event "
				. " , TfMatchNo AS MatchNo "
				. " FROM"
				. "  TeamFinals "
				. " WHERE"
				. "  TfTournament=$TourId"
				. "  AND TfLive='1' "
				. "ORDER BY"
				. " Team,"
				. " DateTime DESC,"
				. " Event ASC,"
				. " MatchNo ASC";
		} elseif ($Team==-1) {
			$Select
				= "(SELECT"
				. "  '0' Team"
				. " , FinDateTime DateTime"
				. " , FinEvent AS Event"
				. " , FinMatchNo AS MatchNo"
				. " FROM"
				. "  Finals "
				. " WHERE"
				. "  FinTournament=$TourId"
				. "  AND FinLive='1') "
				. "UNION "
				. "(SELECT"
				. "  '1' Team "
				. " , TfDateTime DateTime "
				. " , TfEvent AS Event "
				. " , TfMatchNo AS MatchNo "
				. " FROM"
				. "  TeamFinals "
				. " WHERE"
				. "  TfTournament=$TourId"
				. "  AND TfLive='1') "
				. "ORDER BY"
				. " Team,"
				. " DateTime DESC,"
				. " Event ASC,"
				. " MatchNo ASC ";
		} else {
			return '';
		}

		$Rs=safe_r_sql($Select);

		// at least 2 rows because "live" is set on both opponents!
		if (safe_num_rows($Rs)<2) {
			return '';
		}

		$MyRow1=safe_fetch($Rs);
		$MyRow2=safe_fetch($Rs);

		// check if these are really 2 opponents in the same match!!
		if($MyRow1->Event==$MyRow2->Event
			and $MyRow1->Team==$MyRow2->Team
			and ($MyRow1->MatchNo==$MyRow2->MatchNo-1 or $MyRow1->MatchNo==$MyRow2->MatchNo+1)
			) {
			return array($MyRow1->Event, min($MyRow1->MatchNo, $MyRow2->MatchNo), $MyRow1->Team);
		}

		return '';
	}
?>