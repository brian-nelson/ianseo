<?php

function SetAccreditation($Id, $SetRap=0, $return='RicaricaOpener', $TourId=0, $AccOp=0) {
	$RicaricaOpener=false;
	if(!$TourId) 
		$TourId=$_SESSION['TourId'];
	if(!$AccOp) 
		$AccOp=$_SESSION['AccOp'];
	/*
	 * Devo prevenire l'insert se l'id è in stato 7.
	* Per farlo cerco lo stato del tizio.
	* Se è 7 vuol dire che uno ha cliccato sul bottone dopo aver aperto il popup e io non scrivo in db
	*/
	$Select = "SELECT EnId FROM Entries
		WHERE EnId="  . StrSafe_DB($Id) . " AND EnTournament=$TourId AND EnStatus='7' ";
	$Rs=safe_r_sql($Select);
	//TODO Patchare la query per supportare bene IpV6
	if (safe_num_rows($Rs)==0) {
		$Insert = "INSERT INTO AccEntries
			(AEId,AEOperation,AETournament,AEWhen,AEFromIp,AERapp)
			VALUES(
				$Id,"
				. StrSafe_DB($AccOp) . ","
				. StrSafe_DB($TourId) . ","
				. StrSafe_DB(date('Y-m-d H:i')) . ","
				. "INET_ATON('" . ($_SERVER['REMOTE_ADDR']!='::1' ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1') . "'), "
				. StrSafe_DB($SetRap) . ""
			. ") ON DUPLICATE KEY UPDATE "
				. "AEWhen=" . StrSafe_DB(date('Y-m-d H:i')) . ","
				. "AEFromIp=INET_ATON('" . ($_SERVER['REMOTE_ADDR']!='::1' ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1') . "') ";
		$RsIns=safe_w_sql($Insert);
		$RicaricaOpener=($return=='RicaricaOpener' ? true : (safe_w_affected_rows() ? 'AccreditationOK' : 'AccreditationTwice'));
	}
	return $RicaricaOpener;
}