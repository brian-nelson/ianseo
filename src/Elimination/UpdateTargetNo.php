<?php
/*
													- UpdateTargetNo.php -
	La pagina aggiorna il TargetNo del tizio in Qualifications se la sessione è settata
*/


define('debug',false);

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_Sessions.inc.php');

$JSON=array('error'=>1, 'rows' => array());
if (!CheckTourSession() or checkACL(AclEliminations, AclReadWrite, false)!=AclReadWrite or IsBlocked(BIT_BLOCK_ELIM)) {
	JsonOut($JSON);
}

$CommandSymbols=array('+');
$Command='';
$num='';		// numero del bersaglio per la piazzola usata
$letter='';		// lettera della piazzola

$toWrite=array();

foreach ($_REQUEST as $Key => $Value) {
	if (substr($Key, 0, 2) != 'd_') {
		continue;
	}

	list(, , , $Phase, $Event, $Rank) = explode('_', $Key);
	$Id = $Phase . '_' . $Event . '_' . $Rank;

	$q = "SELECT ElSession, Session.*
		FROM Eliminations
		LEFT JOIN Session ON ElSession=SesOrder AND ElTournament=SesTournament AND SesType='E'
		WHERE ElTournament={$_SESSION['TourId']} AND ElEventCode='{$Event}' AND ElElimPhase={$Phase} AND ElQualRank={$Rank}";

	$r = safe_r_sql($q);

	if (safe_num_rows($r) != 1) {
		JsonOut($JSON);
	}

	$session = safe_fetch($r);

	/* check the string  */

	if (trim($Value) != '') {
		if (in_array(substr($Value, -1), $CommandSymbols)) {
			$Command = substr($Value, -1);
			$Value = substr($Value, 0, -1);
		}
	}

	$Value = trim($Value);

	// Check format of the target
	if ($Value) {
		if (!preg_match('/^[0-9]{1,' . TargetNoPadding . '}[A-Z]{1}$/i', strtoupper($Value))) {
			JsonOut($JSON);
		}

		$Value = str_pad(strtoupper($Value), (TargetNoPadding + 1), '0', STR_PAD_LEFT);

		$num = intval($Value);
		$letter = substr($Value, -1);
	}

	if (!$Command) {
		// it was a simple value or an empty target...
		$JSON['rows'][] = array('key' => $Id, 'value' => $Value);
		$toWrite[] = (object)array('rnk' => $Rank, 'val' => $Value);
	} else {
		$lastLetter = ($session->ElSession ? chr(64 + $session->SesAth4Target) : 'D');
		$letters = range('A', $lastLetter);

		// get all the archers whose rank is more than the current
		$q = "SELECT ElElimPhase,ElEventCode,ElTournament,ElQualRank 
            FROM Eliminations
			WHERE ElElimPhase={$Phase} AND ElEventCode='{$Event}' AND ElTournament={$_SESSION['TourId']} AND ElQualRank>={$Rank}
			order by ElQualRank";
		$rs = safe_r_sql($q);

		$curNum = $num;
		$curLetter = $letter;

		while ($row = safe_fetch($rs)) {
			$Value = str_pad(strtoupper($curNum . $curLetter), (TargetNoPadding + 1), '0', STR_PAD_LEFT);
			$JSON['rows'][] = array('key' => $row->ElElimPhase . '_' . $row->ElEventCode . '_' . $row->ElQualRank, 'value' => $Value);
			$toWrite[] = (object)array('rnk' => $row->ElQualRank, 'val' => $Value);

			// Next Letter
			++$curLetter;

			// if out of range, goeas back to A and increments target
			if (!in_array($curLetter, $letters)) {
				++$curNum;
				$curLetter = 'A';
			}
		}
	}

	foreach ($toWrite as $tw) {
		$q = "UPDATE Eliminations
			SET ElTargetNo='$tw->val'
			WHERE ElElimPhase=$Phase AND ElEventCode='$Event' AND ElQualRank=$tw->rnk AND ElTournament={$_SESSION['TourId']}";
		safe_w_sql($q);
	}
}
$JSON['error']=0;
JsonOut($JSON);
