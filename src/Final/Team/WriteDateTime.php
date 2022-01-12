<?php
/*
													- WriteDateTime.php -
	Scrive lo scheduling al singolo incontro
*/

	define('debug',false);

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/Fun_DateTime.inc.php');
    checkACL(AclCompetition, AclReadWrite, false);

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}

	$Errore=0;

	$Which = '';
	$vv = '';	// valore trattato
	if (!IsBlocked(BIT_BLOCK_TOURDATA)) {
		foreach ($_REQUEST as $Key => $Value) {
			if (substr($Key,0,2)=='d_') {
				$Which = $Key;
				$vv = $Value;

				$cc = '';	// campo
				$ee = '';	// evento
				$mm = '';	// matchno estratto dal nome
				$mm2 = '';	// matchno calcolato

				list(,$cc,$ee,$mm)=explode('_',$Key);

				$mm2=$mm+1;
				if($cc=='FSScheduledLen' && (strlen(trim($vv))==0 || !$vv))
				{
					$SearchifMatch	= "SELECT * from FinSchedule "
						. "where "
						. " FSEvent=" . StrSafe_DB($ee)
						. " AND FSTeamEvent='1'"
						. " AND FSMatchNo in ($mm, $mm2) "
						. " AND FSTournament=" . StrSafe_DB($_SESSION['TourId']) ;
					$Rs=safe_R_sql($SearchifMatch);
					if(safe_num_rows($Rs) && strlen(trim($vv))==0)
						$vv=0;
					elseif(!safe_num_rows($Rs) && !$vv)
						$vv='';
				}

				if (strlen(trim($vv))>0)
				{
					$badDate=false;
					if ($cc=='FSScheduledDate')
					{
						if($vv==='0') {
							// UN-schedule a single match
						} else {
							$vv=ConvertDate($Value);
							$badDate=!($vv>=date('Y-m-d',$_SESSION['ToWhenFromUTS']) && $vv<=date('Y-m-d',$_SESSION['ToWhenToUTS']));
						}
					}
					elseif ($cc=='FSScheduledTime')
						$vv=Convert24Time($vv);

					if (!$badDate || ($vv==0 && $cc=='FSScheduledLen'))
					{
					// Scrivo per $mm
						$Insert
							= "INSERT INTO FinSchedule (FSEvent,FSTeamEvent,FSMatchNo,FSTournament," . $cc . ") "
							. "VALUES("
							. StrSafe_DB($ee) . ","
							. StrSafe_DB('1') . ","
							. StrSafe_DB($mm) . ","
							. StrSafe_DB($_SESSION['TourId']) . ","
							. StrSafe_DB($vv) . ""
							. ") "
							. "ON DUPLICATE KEY UPDATE "
							. "FSTarget=FSTarget,"
							. "FSGroup=FSGroup,"
							. $cc . "=" . StrSafe_DB($vv) . " ";
						$Rs=safe_w_sql($Insert);

						if (!$Rs)
							$Errore=1;
						else
						{
							// Scrivo per $mm2
							$Insert
								= "INSERT INTO FinSchedule (FSEvent,FSTeamEvent,FSMatchNo,FSTournament," . $cc . ") "
								. "VALUES("
								. StrSafe_DB($ee) . ","
								. StrSafe_DB('1') . ","
								. StrSafe_DB($mm2) . ","
								. StrSafe_DB($_SESSION['TourId']) . ","
								. StrSafe_DB($vv) . ""
								. ") "
								. "ON DUPLICATE KEY UPDATE "
								. "FSTarget=FSTarget,"
								. "FSGroup=FSGroup,"
								. $cc . "=" . StrSafe_DB($vv) . " ";
							$Rs=safe_w_sql($Insert);
						}
					}
					else
						$Errore=1;

				} else {
					$Insert
						= "delete from FinSchedule "
						. "where "
						. " FSEvent=" . StrSafe_DB($ee)
						. " AND FSTeamEvent=" . StrSafe_DB('1')
						. " AND FSMatchNo in ($mm, $mm2) "
						. " AND FSTournament=" . StrSafe_DB($_SESSION['TourId']) ;
					$Rs=safe_w_sql($Insert);
				}
			}
		}
	}
	else
		$Errore=1;
	if (!debug)
		header('Content-Type: text/xml');
	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<which><![CDATA[' . $Which . ']]></which>' . "\n";
	print '</response>' . "\n";
?>
