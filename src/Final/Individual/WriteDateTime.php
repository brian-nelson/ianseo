<?php
/*
													- WriteDateTime.php -
	Scrive lo scheduling al singolo incontro
*/

	define('debug',false);

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/Fun_DateTime.inc.php');
	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclCompetition, AclReadWrite, false);

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
				$Date='';
				$Time='';
				$Len='';

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
						if($Value==='0') {
							// -- UN scheduling a match
						} elseif(!$Value or $Value=='-') {
							$Value='';
						} elseif(strtolower(substr($Value, 0, 1))=='d') {
							$Value=date('Y-m-d', strtotime(sprintf('%+d days', substr($Value, 1) -1), $_SESSION['ToWhenFromUTS']));
						} else {
							$Value=CleanDate($Value);
						}
						$vv=$Value;

						if($vv!=='0') {
							$badDate=!($vv>=date('Y-m-d',$_SESSION['ToWhenFromUTS']) && $vv<=date('Y-m-d',$_SESSION['ToWhenToUTS']));
						}
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
						if($vv[0]=='+') {
							// get the previous match timing
							$vv2=substr($vv,1);
							$SQL="select FinSchedule.*, addtime(FSScheduledTime, '00:$vv2:00') as NewTime from FinSchedule where FsEvent='$ee' and FSTeamEvent=0 and FSTournament={$_SESSION['TourId']} and FsMatchNo in (".($mm*2). ",".($mm*2 + 2).")";
							$q=safe_r_sql($SQL);
							if($r=safe_fetch(($q))) {
								// Scrivo per $mm
								$sql="FSEvent='$ee',
									FSTournament={$_SESSION['TourId']},
									FSTeamEvent=0,
									FSScheduledDate='$r->FSScheduledDate',
									FSScheduledTime='" . substr($r->NewTime,0,5) . "',
									FSScheduledLen=$r->FSScheduledLen";
								$Insert = "INSERT INTO FinSchedule 
									set FSMatchNo=$mm,
									$sql
									ON DUPLICATE KEY UPDATE 
									FSTarget=FSTarget,
									FSGroup=FSGroup,
									$sql";
								$Rs=safe_w_sql($Insert);

								// Scrivo per $mm2
								$Insert = "INSERT INTO FinSchedule 
									set FSMatchNo=$mm2,
									$sql
									ON DUPLICATE KEY UPDATE 
									FSTarget=FSTarget,
									FSGroup=FSGroup,
									$sql";
								$Rs=safe_w_sql($Insert);

								$vv=substr($r->NewTime,0,5);
							}
						}
						$vv=Convert24Time($vv);
					}

					if (!$badDate || ($vv==0 && $cc=='FSScheduledLen')) {
						// Scrivo per $mm
						$Insert = "INSERT INTO FinSchedule (FSEvent,FSTeamEvent,FSMatchNo,FSTournament," . $cc . ") 
							VALUES(
							" . StrSafe_DB($ee) . ",
							0,
							" . StrSafe_DB($mm) . ",
							" . StrSafe_DB($_SESSION['TourId']) . ",
							" . StrSafe_DB($vv) . "
							) ON DUPLICATE KEY UPDATE 
							FSTarget=FSTarget,
							FSGroup=FSGroup,
							" . $cc . "=" . StrSafe_DB($vv) . " ";
						$Rs=safe_w_sql($Insert);

						// Scrivo per $mm2
						$Insert = "INSERT INTO FinSchedule (FSEvent,FSTeamEvent,FSMatchNo,FSTournament," . $cc . ")
							VALUES(
							" . StrSafe_DB($ee) . ",
							0,
							" . StrSafe_DB($mm2) . ",
							" . StrSafe_DB($_SESSION['TourId']) . ",
							" . StrSafe_DB($vv) . "
							) ON DUPLICATE KEY UPDATE 
							FSTarget=FSTarget, 
							FSGroup=FSGroup,
							" . $cc . "=" . StrSafe_DB($vv) . " ";
						$Rs=safe_w_sql($Insert);
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
		$q=safe_r_sql("select * from FinSchedule where FSEvent='$ee' AND FSTeamEvent=0 AND FSMatchNo=$mm AND FSTournament={$_SESSION['TourId']}");
		if($r=safe_fetch($q)) {
			$Date=$r->FSScheduledDate;
			$Time=substr($r->FSScheduledTime,0,5);
			$Len=$r->FSScheduledLen;
		}
	} else {
		$Errore=1;
	}

	if (!debug)
		header('Content-Type: text/xml');
	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<which><![CDATA[' . $Which . ']]></which>' . "\n";
	print '<value><![CDATA[' . $vv . ']]></value>' . "\n";
	print '<date><![CDATA[' . $Date . ']]></date>' . "\n";
	print '<time><![CDATA[' . $Time . ']]></time>' . "\n";
	print '<len><![CDATA[' . $Len . ']]></len>' . "\n";
	print '<m><![CDATA[' . $mm . ']]></m>' . "\n";
	print '<e><![CDATA[' . $ee . ']]></e>' . "\n";
	print '</response>' . "\n";
