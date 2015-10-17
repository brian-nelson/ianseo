<?php
/*
													- ManTarget.php -
	Aggiorna il target in FinSchedule
*/

	define('debug',false);

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_DB.inc.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;
	$Which = '';
	$Target = '';
	$Phase = '';
	$Event = '';

	$Return='';

	foreach ($_REQUEST as $Key => $Value)
	{
		if (substr($Key,0,2)=='d_')
		{
			$Return .= SetTargetDb($Key, $Value);
		}
	}

	if (!debug)
		header('Content-Type: text/xml');
	print '<response>' . "\n";
	if($Return) {
		print $Return;
	} else {
		print '<field>';
		print '<error>' . $Errore . '</error>' . "\n";
		print '<which><![CDATA[' . $Which . ']]></which>' . "\n";
		print '<target><![CDATA[' . $Target . ']]></target>' . "\n";
		print '<phase><![CDATA[' . $Phase . ']]></phase>' . "\n";
		print '<event><![CDATA[' . $Event . ']]></event>' . "\n";
		print '</field>';
	}
	print '</response>' . "\n";

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
	$Select
		= "SELECT GrPhase FROM Grids WHERE GrMatchNo=" . StrSafe_DB($mm) . " ";
	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)==1)
	{
		$MyRow=safe_fetch($Rs);
		$Phase=$MyRow->GrPhase;

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
		// recupera se la squadra è mixed oppure no
//		$q=safe_r_sql("select * from Events where EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 "
//			. " and EvCode='$ee' ");
//		$r=safe_fetch($q);
//
//		if($r->EvMixedTeam) {
//			// TODO:
//		} else {
			$Sql = "SELECT DISTINCT EcCode, EcTeamEvent, EcNumber FROM EventClass WHERE EcCode='$ee' AND EcTeamEvent!=0 AND EcTournament=" . StrSafe_DB($_SESSION['TourId']);
			$RsEc=safe_r_sql($Sql);
			$RuleCnt=0;
			$Sql = "Select * ";
			while($MyRowEc=safe_fetch($RsEc))
			{
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
			$Quanti=($Phase*2) - safe_num_rows($Rs);
//		}

		if(!$Quanti) {
			// no byes, so normal check
			return SetTargetDb($Key, $Value.'+');
		}

		// we have byes, so we go on with the
		$val=$Value;
		$mm=2*floor($mm/2);

		$MyQuery = 'SELECT '
			. ' GrMatchNo, GrPosition '
			. ' FROM Grids'
			. ' WHERE '
			. " GrPhase = '$Phase' "
			. " AND GrMatchNo>= $mm "
			. " AND GrPosition <= ".(($Phase*2) - $Quanti)
			. ' ORDER BY GrMatchNo ASC'
			. ' LIMIT ' . (($Phase*2) - $Quanti);

		$q=safe_r_sql($MyQuery);

		while($r=safe_fetch($q)) {
			$butt= ($r->GrPosition > $Quanti) ? $val : '';

			if(!$ath or !($r->GrMatchNo%2)) {
				$ret .= SetTargetDb('d_'.$Campo.'_'.$ee.'_'.$r->GrMatchNo.'_'.$ath, $butt);
				if($butt) $val++;
			}
		}

		return($ret);
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

		if (!$Rs)
			$Errore=1;
		else
		{
			if ($ath==1)
			{
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

				if (!$Rs)
					$Errore=1;
			}
		}
	}
	else
		$Errore=1;

	$ret.= '<field>';
	$ret.=  '<error>' . $Errore . '</error>' . "\n";
	$ret.=  '<which><![CDATA[' . $Which . ']]></which>' . "\n";
	$ret.=  '<target><![CDATA[' . $Target . ']]></target>' . "\n";
	$ret.=  '<phase><![CDATA[' . $Phase . ']]></phase>' . "\n";
	$ret.=  '<event><![CDATA[' . $Event . ']]></event>' . "\n";
	$ret.=  '</field>';

	return($ret);
}
?>