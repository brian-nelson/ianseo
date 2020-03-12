<?php
/*
													- ManTarget.php -
	Aggiorna il target in FinSchedule
*/

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Common/Fun_DB.inc.php');

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclCompetition, AclReadWrite, false);

	$Errore=0;
	$Which = '';
	$Target = '';
	$Phase = '';
	$Event = '';

	$Return='';

	foreach ($_REQUEST as $Key => $Value) {
		if (substr($Key,0,2)=='d_') {
			$Return .= SetTargetDb($Key, $Value);
		}
	}

	header('Content-Type: text/xml');
	print '<response>';
	if($Return) {
		print $Return;
	} else {
		print '<field>';
		print '<error>' . $Errore . '</error>';
		print '<which><![CDATA[' . $Which . ']]></which>';
		print '<target><![CDATA[' . $Target . ']]></target>';
		print '<phase><![CDATA[' . $Phase . ']]></phase>';
		print '<event><![CDATA[' . $Event . ']]></event>';
		print '</field>';
	}
	print '</response>';

function SetTargetDb($Key, $Value) {
	$ret='';
	$Errore=0;
	$Target='';

	$Which = $Key;

	$Campo = '';
	$ee = '';
	$mm = '';
	$ath = '';	// ath per bersaglio (0 --> 1; 1 --> 2)

	list(,$Campo,$ee,$mm,$ath)=explode('_',$Key);

	// cerco la fase del matchno
	$NumQualified=0;
	$Numsaved=0;
	$NumMatches=0;
	$FirstPhase=0;
	$Select = "SELECT GrPhase, EvFinalFirstPhase, EvNumQualified 
		FROM Events
		inner join Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2, EvTeamEvent))>0
		inner join Grids on GrPhase<=greatest(PhId, PhLevel)
		WHERE GrMatchNo=" . StrSafe_DB($mm) . " and EvCode='$ee' and EvTeamEvent=1 and EvTournament={$_SESSION['TourId']}";
	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)==1) {
		$MyRow=safe_fetch($Rs);
		$Phase=$MyRow->GrPhase;
		$FirstPhase=$MyRow->EvFinalFirstPhase;
		$NumQualified=$MyRow->EvNumQualified;
		$realPhase=namePhase($MyRow->EvFinalFirstPhase, $MyRow->GrPhase);
		$Numsaved=SavedInPhase($MyRow->EvFinalFirstPhase);
		$NumMatches=numMatchesByPhase($MyRow->EvFinalFirstPhase);
	}

	$Event = $ee;

// if target is followed by a "+" sign fills up the phase from this point up to the last with increments of 1
	if(substr($Value,-1)=='+') {
		$Value=intval($Value);
		$val=$Value;
		foreach(range($mm, $Phase*4 - 1, $ath+1) as $n) {
			$ret .= SetTargetDb('d_'.$Campo.'_'.$ee.'_'.$n.'_'.$ath, $val++);
		}
		return($ret);
	} elseif(substr($Value,-1)=='*') {
		$Value=intval($Value);
		$mm=2*floor($mm/2);
		$val=$Value;
		$z=1;
		foreach(range($mm, $Phase*4 - 1, $ath+1) as $n) {
			$ret .= SetTargetDb('d_'.$Campo.'_'.$ee.'_'.$n.'_'.$ath, $val++);
			$z = 1-$z;
			if($z) $val++;
		}
		return($ret);
	} elseif(substr($Value,-1)=='-') {
		// recupera gli scontri senza bye...
		$Value=intval($Value);
		$Sql = "SELECT DISTINCT EcCode, EcTeamEvent, EcNumber FROM EventClass WHERE EcCode='$ee' AND EcTeamEvent!=0 AND EcTournament=" . StrSafe_DB($_SESSION['TourId']);
		$RsEc=safe_r_sql($Sql);
		$RuleCnt=0;
		$Sql = "Select * ";
		while($MyRowEc=safe_fetch($RsEc)) {
            $Sql .= (++$RuleCnt == 1 ? "FROM ": "INNER JOIN ");
            $Sql .= "(SELECT EnCountry as C" . $RuleCnt . "
                  FROM Entries
                  INNER JOIN EventClass ON EnClass=EcClass AND EnDivision=EcDivision AND EnTournament=EcTournament AND EcTeamEvent=" . $MyRowEc->EcTeamEvent . " AND EcCode='$ee'
                  WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnTeamFEvent=1
                  group by EnCountry
                  HAVING COUNT(EnId)>=" . $MyRowEc->EcNumber . ") as sqy";
            $Sql .= ($RuleCnt == 1 ? " ": $RuleCnt . " ON C1=C". $RuleCnt . " ");
		}

		$Rs=safe_r_sql($Sql);
		$tmpQuanti=safe_num_rows($Rs);
		$tmpSaved=($Phase>=$FirstPhase ? SavedInPhase($FirstPhase) : SavedInPhase($realPhase));
		$tmpQuantiIn = min($NumQualified, maxPhaseRank($realPhase));
		$tmpQuantiOut = $tmpQuanti-$tmpQuantiIn;
		$tmpBye = ($tmpQuantiOut<0 ? abs($tmpQuantiOut) : 0) + $tmpSaved;


		if(!$tmpBye) {
			// no byes, so normal check
			return SetTargetDb($Key, $Value.'+');
		}

		// we have byes, so we go on with the
		$val=$Value;
		$PosToTake=($realPhase==24 or $realPhase==48) ? 'GrPosition2' : 'GrPosition';
		$MyQuery = "SELECT distinct GrMatchNo, if($PosToTake > EvNumQualified, 0, $PosToTake) as Position
			FROM Events
			inner join Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2,EvTeamEvent))>0
			inner join Grids on GrPhase<=greatest(PhId, PhLevel)
			WHERE GrPhase = '$Phase' AND GrMatchNo>= $mm and EvCode='$ee' and EvTeamEvent=1 and EvTournament={$_SESSION['TourId']} 
			ORDER BY GrMatchNo ASC";

		$q=safe_r_sql($MyQuery);

		while($r=safe_fetch($q)) {
			$butt= ($r->Position ? $val : '');
			$butt= ($r->Position <= $tmpBye || $r->Position > $tmpQuanti) ? '' : $val;

			if(!$ath or !($r->GrMatchNo%2)) {
				if($r->Position > $tmpBye) {
					$ret .= SetTargetDb('d_'.$Campo.'_'.$ee.'_'.$r->GrMatchNo.'_'.$ath, $butt);
					if($butt) $val++;
				} else {
					$ret .= SetTargetDb('d_' . $Campo . '_' . $ee . '_' . $r->GrMatchNo . '_' . $ath, '');
				}
			}
		}

		return($ret);
	}

// verifico che il target sia un numero vero
	if (preg_match('/^[0-9]{1,' . TargetNoPadding . '}$/i',$Value) || strlen(trim($Value))==0) {
		if (strlen(trim($Value))>0) {
			$Target = str_pad($Value,TargetNoPadding,'0',STR_PAD_LEFT);
		}
	// scrivo il target
		$Insert
			= "INSERT INTO FinSchedule (FSEvent,FSTeamEvent,FSMatchNo,FSTournament,FSTarget, FSLetter) "
			. "VALUES("
			. StrSafe_DB($ee) . ","
			. StrSafe_DB('1') . ","
			. StrSafe_DB($mm) . ","
			. StrSafe_DB($_SESSION['TourId']) . ","
			. StrSafe_DB($Target) . ","
			. StrSafe_DB($Target?($Target.'A'):'') . ""
			. ") "
			. "ON DUPLICATE KEY UPDATE "
			. "FSTarget=" . StrSafe_DB($Target) . ","
			. "FSGroup=FSGroup,"
			. "FSScheduledTime=FSScheduledTime ";
		$Rs=safe_w_sql($Insert);

		if (!$Rs) {
            $Errore = 1;
        } else {
			if ($ath==1) {
				$Insert
					= "INSERT INTO FinSchedule (FSEvent,FSTeamEvent,FSMatchNo,FSTournament,FSTarget, FSLetter) "
					. "VALUES("
					. StrSafe_DB($ee) . ","
					. StrSafe_DB('1') . ","
					. StrSafe_DB($mm+1) . ","
					. StrSafe_DB($_SESSION['TourId']) . ","
					. StrSafe_DB($Target) . ","
					. StrSafe_DB($Target?($Target.'B'):'') . ""
					. ") "
					. "ON DUPLICATE KEY UPDATE "
					. "FSTarget=" . StrSafe_DB($Target) . ","
					. "FSGroup=FSGroup,"
					. "FSScheduledTime=FSScheduledTime ";
				$Rs=safe_w_sql($Insert);

				if (!$Rs) {
                    $Errore = 1;
                }
			}
		}
	} else {
        $Errore = 1;
    }

	$ret.= '<field>';
	$ret.=  '<error>' . $Errore . '</error>';
	$ret.=  '<which><![CDATA[' . $Which . ']]></which>';
	$ret.=  '<target><![CDATA[' . $Target . ']]></target>';
	$ret.=  '<phase><![CDATA[' . $Phase . ']]></phase>';
	$ret.=  '<event><![CDATA[' . $Event . ']]></event>';
	$ret.=  '</field>';

	return($ret);
}
?>