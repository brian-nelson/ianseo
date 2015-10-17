<?php
/*
													- WriteDateTime.php -
	Scrive lo scheduling al singolo incontro
*/

	define('debug',false);

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/Fun_DateTime.inc.php');

	if (!CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;

	$Which = '';
	$vv = '';	// valore trattato
	if (!IsBlocked(BIT_BLOCK_TOURDATA))
	{
		foreach ($_REQUEST as $Key => $Value)
		{
			if (substr($Key,0,2)=='d_')
			{
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
						. " AND FSTeamEvent='0'"
						. " AND FSMatchNo in ($mm, $mm2) "
						. " AND FSTournament=" . StrSafe_DB($_SESSION['TourId']) ;
					$Rs=safe_R_sql($SearchifMatch);
					if(safe_num_rows($Rs) && strlen(trim($vv))==0)
						$vv=0;
					elseif(!safe_num_rows($Rs) && !$vv)
						$vv='';
				}

				if (strlen(trim($vv))>0) {
					$badDate=false;
					if ($cc=='FSScheduledDate') {
						$vv=ConvertDate($Value);
						$badDate=!($vv>=date('Y-m-d',$_SESSION['ToWhenFromUTS']) && $vv<=date('Y-m-d',$_SESSION['ToWhenToUTS']));
						if(!$badDate) {
							// check if there is still a warmup for that event at the original time...
							$q=safe_r_SQL("select count(*) as Counted, FsScheduledDate, FsScheduledTime from FinSchedule
								where FsEvent=".StrSafe_DB($ee)."
								AND FsTeamEvent=0
								AND FsTournament={$_SESSION['TourId']}
								AND (FsScheduledDate, FsScheduledTime)=(select FsScheduledDate,FsScheduledTime from FinSchedule
									where FsEvent=".StrSafe_DB($ee)."
									AND FsTeamEvent=0
									AND FsMatchNo=".StrSafe_DB($mm)."
									AND FsTournament={$_SESSION['TourId']})");
							if($r=safe_fetch($q) and $r->Counted==1) {
								// change the associated warmup if any
								$q=safe_r_SQL("select * from FinWarmup
									where FwTournament={$_SESSION['TourId']}
									and FwTeamEvent=0
									and FwDay='$vv'
									and FwMatchTime='$r->FsScheduledTime'
									and FwEvent=".StrSafe_DB($ee));
								if($dest=safe_fetch($q)) {
									// destination date and time already exists for that event...
									// get the source data
									// deletes the associated warmup that failed with previous query
									safe_w_sql("delete from FinWarmup
										where FwTournament={$_SESSION['TourId']}
										and FwTeamEvent=0
										and FwDay='$r->FsScheduledDate'
										and FwMatchTime='$r->FsScheduledTime'
										and FwEvent=".StrSafe_DB($ee));
								} else {
									safe_w_sql("update ignore FinWarmup set FwDay='$vv'
										where FwTournament={$_SESSION['TourId']}
										and FwTeamEvent=0
										and FwDay='$r->FsScheduledDate'
										and FwMatchTime='$r->FsScheduledTime'
										and FwEvent=".StrSafe_DB($ee));
								}
							}
						}
					} elseif ($cc=='FSScheduledTime') {
						$vv=Convert24Time($vv);
					}

					if (($vv>0 && !$badDate) || ($vv==0 && $cc=='FSScheduledLen')) {
					// Scrivo per $mm
						$Insert
							= "INSERT INTO FinSchedule (FSEvent,FSTeamEvent,FSMatchNo,FSTournament," . $cc . ") "
							. "VALUES("
							. StrSafe_DB($ee) . ","
							. StrSafe_DB('0') . ","
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
								. StrSafe_DB('0') . ","
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
						. " AND FSTeamEvent=" . StrSafe_DB('0')
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