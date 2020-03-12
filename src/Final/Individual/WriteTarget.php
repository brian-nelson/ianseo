<?php
/*
													- ManTarget.php -
	Aggiorna il target in FinSchedule
*/

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Phases.inc.php');

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

	if (!IsBlocked(BIT_BLOCK_TOURDATA)) {
		foreach ($_REQUEST as $Key => $Value) {
			if (substr($Key,0,2)=='d_') {
				$Return .= SetTargetDb($Key, $Value);
			}
		}
	} else {
		$Errore=1;
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
	$Target = '';
	$Phase = '';
	$firstPhase = '';
	$realPhase = '';
	$Event = '';
	$Which = $Key;

	$Campo = '';
	$ee = '';
	$mm = '';	// matchno estratto
	$ath = '';	// ath per bersaglio (0 --> 1; 1 --> 2)

	list(,$Campo,$ee,$mm,$ath)=explode('_',$Key);
	$Event = $ee;

// cerco la fase del matchno
	$Select = "SELECT GrPhase, EvFinalFirstPhase
		FROM Events
		inner join Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2, EvTeamEvent))>0
		inner join Grids on GrPhase<=greatest(PhId, PhLevel) 
		WHERE GrMatchNo=" . StrSafe_DB($mm) . " and EvCode='$ee' and EvTeamEvent=0 and EvTournament={$_SESSION['TourId']}";
	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)==1) {
		$MyRow=safe_fetch($Rs);
		$Phase=$MyRow->GrPhase;
		$realPhase=namePhase($MyRow->EvFinalFirstPhase, $MyRow->GrPhase);
	}



// if target is followed by a "+" sign fills up the phase from this point up to the last with increments of 1
	if(substr($Value,-1)=='+') {
		$Value=intval($Value);
		$val=$Value;
		foreach(range($mm, $Phase*4 - 1, $ath+1) as $n) {
			$ret .= SetTargetDb('d_'.$Campo.'_'.$ee.'_'.$n.'_'.$ath, $val++);
		}
		return $ret;
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
		return $ret;
	} elseif(substr($Value,-1)=='-') {
		// recupera gli scontri senza bye...
		// lo scontro parte da un numero pari
		$mm=2*floor($mm/2);
		$Value=intval($Value);
		$val=$Value;

		// cerca i byes, quindi va a prendere la differenza tra il numero di atleti della fase e gli atleti presenti in quell'evento
		$MyQuery = "SELECT COUNT(EnId) as Quanti, EvFinalFirstPhase as FirstPhase, EvNumQualified
			FROM Events
			INNER JOIN Individuals ON EvCode=IndEvent AND EvTournament=IndTournament
			INNER JOIN Entries ON EnId=IndId AND EnTournament=IndTournament
			WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode=" . StrSafe_DB($ee) ." AND EvTeamEvent=0 AND ((EnIndFEvent=1 AND EnStatus<=1) OR EnId IS NULL)";
		$q=safe_r_sql($MyQuery);
		$r=safe_fetch($q);
		$tmpQuanti=$r->Quanti;
		$tmpSaved=($Phase>=$r->FirstPhase ? SavedInPhase($r->FirstPhase) : SavedInPhase($realPhase));
		$tmpQuantiIn = min($r->EvNumQualified, maxPhaseRank($realPhase));
		$tmpQuantiOut = $tmpQuanti-$tmpQuantiIn;
		$tmpBye = ($tmpQuantiOut<0 ? abs($tmpQuantiOut) : 0) + $tmpSaved;

		// ci sono byes, quindi va a riempire solo i matchno dei match pieni cioè con una rank superiore all'ultimo bye!
		// esempio: 1/8, 13 presenti, sono 3 byes, quindi si parte dal 4° in ranking...
		//
		$PosToTake=($realPhase==24 or $realPhase==48) ? 'GrPosition2' : 'GrPosition';
		$MyQuery = "SELECT distinct GrMatchNo, if($PosToTake > EvNumQualified, 0, $PosToTake) as Position
			FROM Events
			inner join Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2,EvTeamEvent))>0
			inner join Grids on GrPhase<=greatest(PhId, PhLevel)
			WHERE GrPhase = '$Phase' AND GrMatchNo>= $mm and EvCode='$ee' and EvTeamEvent=0 and EvTournament={$_SESSION['TourId']} 
			ORDER BY GrMatchNo ASC";
		$q=safe_r_sql($MyQuery);

		while($r=safe_fetch($q)) {
			$butt= ($r->Position < $tmpBye || $r->Position > $tmpQuanti) ? '' : $val;

			if(!$ath or !($r->GrMatchNo%2)) {
				if($r->Position > $tmpBye) {
					$ret .= SetTargetDb('d_'.$Campo.'_'.$ee.'_'.$r->GrMatchNo.'_'.$ath, $butt);
					if($butt) $val++;
				} else {
					$ret .= SetTargetDb('d_'.$Campo.'_'.$ee.'_'.$r->GrMatchNo.'_'.$ath, '');
					$ret.= '<field>';
					$ret.=  '<error>0</error>';
					$ret.= '<org>d_'.$Campo.'_'.$ee.'_'.$r->GrMatchNo.'_'.$ath.' - </org>'."\n";
					$ret.=  '<which><![CDATA[d_'.$Campo.'_'.$ee.'_'.$r->GrMatchNo.'_'.$ath.']]></which>';
					$ret.=  '<target><![CDATA[]]></target>';
					$ret.=  '<phase><![CDATA[' . $Phase . ']]></phase>';
					$ret.=  '<event><![CDATA[' . $Event . ']]></event>';
					$ret.=  '</field>';
				}
			}
		}
		return $ret;
	}
// verifico che il target sia un numero vero
	if (preg_match('/^[0-9]{1,' . TargetNoPadding . '}$/i',$Value) || strlen(trim($Value))==0)
	{
		if (strlen(trim($Value))>0)
		{
			$Target = str_pad($Value,TargetNoPadding,'0',STR_PAD_LEFT);
		}
	// scrivo il target
		$Insert
			= "INSERT INTO FinSchedule (FSEvent,FSTeamEvent,FSMatchNo,FSTournament,FSTarget, FSLetter) "
			. "VALUES("
			. StrSafe_DB($ee) . ","
			. StrSafe_DB('0') . ","
			. StrSafe_DB($mm) . ","
			. StrSafe_DB($_SESSION['TourId']) . ","
			. StrSafe_DB($Target) . ","
			. StrSafe_DB($Target ? ($Target) : '') . ""
			. ") "
			. "ON DUPLICATE KEY UPDATE "
			. "FSTarget=" . StrSafe_DB($Target) . ","
			. "FSGroup=FSGroup,"
			. "FSLetter=". StrSafe_DB($Target ? ($Target) : '').", "
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
					. StrSafe_DB('0') . ","
					. StrSafe_DB($mm+1) . ","
					. StrSafe_DB($_SESSION['TourId']) . ","
					. StrSafe_DB($Target) . ","
					. StrSafe_DB($Target ? ($Target.'B') : '') . ""
					. ") "
					. "ON DUPLICATE KEY UPDATE "
					. "FSTarget=" . StrSafe_DB($Target) . ","
					. "FSGroup=FSGroup,"
					. "FSLetter=". StrSafe_DB($Target ? ($Target.'B') : '').", "
					. "FSScheduledTime=FSScheduledTime ";
				$Rs=safe_w_sql($Insert);

				if (!$Rs) {
				    $Errore=1;
                }

				$Update
					= "update FinSchedule "
					. " SET "
					. " FSLetter=". StrSafe_DB($Target ? ($Target.'A') : '')." "
					. "WHERE"
					. " FSEvent= " . StrSafe_DB($ee)
					. " AND FSTeamEvent= " . StrSafe_DB('0')
					. " AND FSMatchNo= " . StrSafe_DB($mm)
					. " AND FSTournament= " . StrSafe_DB($_SESSION['TourId']);
				$Rs=safe_w_sql($Update);

				if (!$Rs) {
				    $Errore=1;
                }
			}
		}
	} else {
		$Errore=1;
	}

	$ret.= '<field>';
	$ret.=  '<error>' . $Errore . '</error>';
	$ret.= '<org>'.$Key.' - '.$Value.'</org>'."\n";
	$ret.=  '<which><![CDATA[' . $Which . ']]></which>';
	$ret.=  '<target><![CDATA[' . $Target . ']]></target>';
	$ret.=  '<phase><![CDATA[' . $Phase . ']]></phase>';
	$ret.=  '<event><![CDATA[' . $Event . ']]></event>';
	$ret.=  '</field>';

	return($ret);
}
