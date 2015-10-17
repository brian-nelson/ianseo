<?php

require_once('Common/Lib/Fun_DateTime.inc.php');
require_once('Common/Lib/Fun_Scheduler.php');

function InsertTextDate($Request) {
	foreach($Request as $OldDay => $Times) {
		foreach($Times as $OldTime => $Orders) {
			foreach($Orders as $OldOrder => $Value) {
				if(!$Value or $Value=='-') {
					$Value='';
				} elseif(strtolower(substr($Value, 0, 1))=='d') {
					$Value=date('Y-m-d', strtotime(sprintf('%+d days', substr($Value, 1) -1), $_SESSION['ToWhenFromUTS']));
				} else {
					$Value=CleanDate($Value);
				}

				if($Value) {
					$q=safe_r_sql("select * from Scheduler
						where SchTournament={$_SESSION['TourId']}
						AND SchDay='$OldDay'
						AND SchStart='$OldTime'
						AND SchOrder='$OldOrder'");
					if($r=safe_fetch($q)) {
						$success=safe_w_sql("update Scheduler
								set SchDay='$Value'
							where SchTournament={$_SESSION['TourId']}
								AND SchDay='$OldDay'
								AND SchStart='$OldTime'
								AND SchOrder='$OldOrder'
							", false, array(1062));
						if(!$success) out(); // FAILURE SO ERROR
					} else {
						out(); // strange thing happened!
					}
// 				} else {
//					SHould we allow to delete like that???
// 					safe_w_sql("delete from Schedule
// 						where SchTournament={$_SESSION['TourId']}
// 						AND SchDay='$OldDay'
// 						AND SchStart='$OldTime'
// 						AND SchOrder='$OldOrder'
// 						");
				}
				$q=safe_r_SQL("select
						SchDay DiDay,
						SchStart DiStart,
						SchDuration DiDuration,
						SchOrder,
						SchTitle,
						SchSubTitle,
						SchText,
						SchShift,
						'[$OldDay][$OldTime][$OldOrder]' old,
						'[$Value][$OldTime][$OldOrder]' new
					from Scheduler
					where SchTournament={$_SESSION['TourId']} and SchDay='$Value' AND SchStart='$OldTime' AND SchOrder='$OldOrder'");
				return DistanceInfoData(safe_fetch($q), empty($Value), true);
			}
		}
	}
	return array('error' => 1);
}

function InsertTextTime($Request) {
	foreach($Request as $OldDate => $Times) {
		foreach($Times as $OldTime => $Orders) {
			foreach($Orders as $OldOrder => $Value) {
				if(!$Value or $Value=='-') {
					$Value='';
				} else {
					$t=explode(':', $Value);
					if(count($t)==1) {
						$t[1]=$t[0]%60;
						$t[0]= intval($t[0]/60);
					}
					$Value=sprintf('%02d:%02d:00', $t[0], $t[1]);
				}
				$success=safe_w_sql("Update Scheduler
					set SchStart='$Value'
					where
						SchTournament={$_SESSION['TourId']}
						AND SchDay='$OldDate'
						AND SchStart='$OldTime'
						AND SchOrder='$OldOrder'
						", false, array(1062));
				if(!$success) out();
				$q=safe_r_SQL("select
						SchDay DiDay,
						SchStart DiStart,
						SchDuration DiDuration,
						SchOrder,
						SchTitle,
						SchSubTitle,
						SchText,
						SchShift,
						'[$OldDate][$OldTime][$OldOrder]' old,
						'[$OldDate][$Value][$OldOrder]' new
					from Scheduler
					where SchTournament={$_SESSION['TourId']} and SchDay='$OldDate' AND SchStart='$Value' AND SchOrder='$OldOrder'");
				return DistanceInfoData(safe_fetch($q), false, true);
			}
		}
	}
	return array('error' => 1);
}

function InsertTextDuration($Request, $Order=false) {
	$Errore=1;
	foreach($Request as $OldDate => $Times) {
		foreach($Times as $OldTime => $Orders) {
			foreach($Orders as $OldOrder => $Value) {
				$Value=intval($Value);
				$Field=($Order ? 'Order' : 'Duration');
				$success=safe_w_sql("Update Scheduler
					set Sch{$Field}='$Value'
					where
						SchTournament={$_SESSION['TourId']}
						AND SchDay='$OldDate'
						AND SchStart='$OldTime'
						AND SchOrder='$OldOrder'
					", false, array(1062));
// 				debug_svela("Update Scheduler
// 					set Sch{$Field}='$Value'
// 					where
// 						SchTournament={$_SESSION['TourId']}
// 						AND SchDay='$OldDate'
// 						AND SchStart='$OldTime'
// 						AND SchOrder='$OldOrder'
// 					");
				if(!$success) out();
				$q=safe_r_SQL("select
						SchDay DiDay,
						SchStart DiStart,
						SchDuration DiDuration,
						SchOrder,
						SchTitle,
						SchSubTitle,
						SchText,
						SchShift
					".($Order ? ",
						'[$OldDate][$OldTime][$OldOrder]' old,
						'[$OldDate][$OldTime][$Value]' new" : '')."
					from Scheduler
					where SchTournament={$_SESSION['TourId']} and SchDay='$OldDate' AND SchStart='$OldTime' AND SchOrder='".($Order ? $Value : $OldOrder)."'");
				return DistanceInfoData(safe_fetch($q), false, true);
			}
		}
	}
	return array('error' => 1);
}

function InsertTextShift($Request) {
	$Errore=1;
	foreach($Request as $OldDate => $Times) {
		foreach($Times as $OldTime => $Orders) {
			foreach($Orders as $OldOrder => $Value) {
				if(strlen($Value)) {
					$Value=StrSafe_DB(intval($Value));
				} else {
					$Value='null';
				}
				$success=safe_w_sql("Update Scheduler
					set SchShift=$Value
					where
						SchTournament={$_SESSION['TourId']}
						AND SchDay='$OldDate'
						AND SchStart='$OldTime'
						AND SchOrder='$OldOrder'
					", false, array(1062));
				if(!$success) out();
				$q=safe_r_SQL("select
						SchDay DiDay,
						SchStart DiStart,
						SchDuration DiDuration,
						SchOrder,
						SchTitle,
						SchSubTitle,
						SchText,
						SchShift
					from Scheduler
					where SchTournament={$_SESSION['TourId']} and SchDay='$OldDate' AND SchStart='$OldTime' AND SchOrder='".($Order ? $Value : $OldOrder)."'");
				return DistanceInfoData(safe_fetch($q), false, true);
			}
		}
	}
	return array('error' => 1);
}

function InsertText($Request, $Field) {
	$Errore=1;
	foreach($Request as $OldDate => $Times) {
		foreach($Times as $OldTime => $Orders) {
			foreach($Orders as $OldOrder => $Value) {
				safe_w_sql("Update Scheduler
					set Sch{$Field}=".StrSafe_DB($Value)."
					where
						SchTournament={$_SESSION['TourId']}
						AND SchDay='$OldDate'
						AND SchStart='$OldTime'
						AND SchOrder='$OldOrder'
					");
				$q=safe_r_SQL("select
						SchDay DiDay,
						SchStart DiStart,
						SchDuration DiDuration,
						SchOrder,
						SchTitle,
						SchSubTitle,
						SchText,
						SchShift
					from Scheduler
					where SchTournament={$_SESSION['TourId']} and SchDay='$OldDate' AND SchStart='$OldTime' AND SchOrder='$OldOrder'");
				return DistanceInfoData(safe_fetch($q));
			}
		}
	}
	return array('error' => 1);
}

function InsertSchedDate($Request, $Type='Q') {
	foreach($Request as $Session => $Distances) {
		foreach($Distances as $Dist => $Value) {
			if(!$Value or $Value=='-') {
				$Value='';
			} elseif(strtolower(substr($Value, 0, 1))=='d') {
				$Value=date('Y-m-d', strtotime(sprintf('%+d days', substr($Value, 1) -1), $_SESSION['ToWhenFromUTS']));
			} else {
				$Value=CleanDate($Value);
			}

			if($Value) {
				safe_w_sql("insert into DistanceInformation set
						DiTournament={$_SESSION['TourId']},
						DiDistance=$Dist,
						DiSession=$Session,
						DiType='$Type',
						DiDay='$Value'
					on duplicate key update
						DiDay='$Value'
						");
			} else {
				safe_w_sql("insert into DistanceInformation set
						DiTournament={$_SESSION['TourId']},
						DiDistance=$Dist,
						DiSession=$Session,
						DiType='$Type',
						DiDay='',
						DiStart='',
						DiDuration=0,
						DiWarmStart='',
						DiWarmDuration=0,
						DiOptions=''
					on duplicate key update
						DiDay='',
						DiStart='',
						DiDuration=0,
						DiWarmStart='',
						DiWarmDuration=0,
						DiOptions=''
						");
			}
			$q=safe_r_SQL("select DiDay, DiStart, DiDuration, DiWarmStart, DiWarmDuration, DiOptions, DiShift
				from DistanceInformation
				where DiTournament={$_SESSION['TourId']} and DiDistance=$Dist and DiSession=$Session and DiType='$Type'");
			return DistanceInfoData(safe_fetch($q));
		}
	}
	return array('error' => 1);
}

function InsertSchedTime($Request, $Field='', $Type='Q') {
	foreach($Request as $Session => $Distances) {
		foreach($Distances as $Dist => $Value) {
			if(!$Value or $Value=='-') {
				$Value='';
			} else {
				$t=explode(':', $Value);
				if(count($t)==1) {
					if($Value[0]=='-' and $Field) {
						// shortcut to put the warmup at some minutes before the startup
						// get the starting time
						$q=safe_r_sql("select DiStart from DistanceInformation where DiTournament={$_SESSION['TourId']} and DiDistance=$Dist and DiSession=$Session and DiType='$Type'");
						if($r=safe_fetch($q) and $r->DiStart!='00:00:00') {
							$t=explode(':', AddMinutes($r->DiStart, $Value));
							safe_w_sql("update DistanceInformation set DiWarmDuration='".abs($Value)."' where DiTournament={$_SESSION['TourId']} and DiDistance=$Dist and DiSession=$Session and DiType='$Type'");
						} else {
							$t=array(0, 0);
						}
					} else {
						$Minutes=$Value;
						$t[1]=$t[0]%60;
						$t[0]= intval($t[0]/60);
					}
				} else {
					$Minutes=($t[0]*60)+$t[1];
					$t[0]=intval($Minutes/60);
					$t[1]=$Minutes%60;
				}
				$Value=sprintf('%02d:%02d:00', $t[0], $t[1]);
				if(!$Field) {
					// change in the start, move the warmup accordingly
					$q=safe_r_sql("select DiStart, DiWarmStart from DistanceInformation where DiTournament={$_SESSION['TourId']} and DiDistance=$Dist and DiSession=$Session and DiType='$Type'");
					if($r=safe_fetch($q) and $r->DiStart!='00:00:00' and $r->DiWarmStart!='00:00:00') {
						$tmp=$Minutes - ((substr($r->DiStart, 0, 2)*60) + substr($r->DiStart, 3, 2));
						$DiWarmStart=(substr($r->DiWarmStart, 0, 2)*60) + substr($r->DiWarmStart, 3, 2) + $tmp;
						$Minutes=sprintf('%02d:%02d:00', intval($DiWarmStart/60), $DiWarmStart%60);
						safe_w_sql("Update DistanceInformation
							set DiWarmStart='$Minutes'
							where
								DiTournament={$_SESSION['TourId']}
								AND DiDistance=$Dist
								AND DiSession=$Session
								AND DiType='$Type'
							");
					}
				}
			}
			safe_w_sql("insert into DistanceInformation set
					DiTournament={$_SESSION['TourId']},
					DiDistance=$Dist,
					DiSession=$Session,
					DiType='$Type',
					Di{$Field}Start='$Value'
				on duplicate key update
					Di{$Field}Start='$Value'
					");
			$q=safe_r_SQL("select DiDay, DiStart, DiDuration, DiWarmStart, DiWarmDuration, DiOptions, DiShift
				from DistanceInformation
				where DiTournament={$_SESSION['TourId']} and DiDistance=$Dist and DiSession=$Session and DiType='$Type'");
			return DistanceInfoData(safe_fetch($q));
		}
	}
	return array('error' => 1);
}

function InsertSchedDuration($Request, $Field='', $Type='Q') {
	foreach($Request as $Session => $Distances) {
		foreach($Distances as $Dist => $Value) {
			$Value=intval($Value);
			safe_w_sql("insert into DistanceInformation set
					DiTournament={$_SESSION['TourId']},
					DiDistance=$Dist,
					DiSession=$Session,
					DiType='$Type',
					Di{$Field}Duration='$Value'
				on duplicate key update
					Di{$Field}Duration='$Value'
					");
			$q=safe_r_SQL("select DiDay, DiStart, DiDuration, DiWarmStart, DiWarmDuration, DiOptions, DiShift
				from DistanceInformation
				where DiTournament={$_SESSION['TourId']} and DiDistance=$Dist and DiSession=$Session and DiType='$Type'");
			return DistanceInfoData(safe_fetch($q));
		}
	}
	return array('error' => 1);
}

function InsertSchedShift($Request, $Type='Q') {
	foreach($Request as $Session => $Distances) {
		foreach($Distances as $Dist => $Value) {
			if(strlen($Value)) {
				$Value=StrSafe_DB(intval($Value));
			} else {
				$Value='null';
			}
			safe_w_sql("insert into DistanceInformation set
					DiTournament={$_SESSION['TourId']},
					DiDistance=$Dist,
					DiSession=$Session,
					DiType='$Type',
					DiShift=".$Value."
				on duplicate key update
					DiShift=".$Value."
					");
			$q=safe_r_SQL("select DiDay, DiStart, DiDuration, DiWarmStart, DiWarmDuration, DiOptions, DiShift
				from DistanceInformation
				where DiTournament={$_SESSION['TourId']} and DiDistance=$Dist and DiSession=$Session and DiType='$Type'");
			return DistanceInfoData(safe_fetch($q));
		}
	}
	return array('error' => 1);
}

function InsertSchedComment($Request, $Type='Q') {
	foreach($Request as $Session => $Distances) {
		foreach($Distances as $Dist => $Value) {
			safe_w_sql("insert into DistanceInformation set
					DiTournament={$_SESSION['TourId']},
					DiDistance=$Dist,
					DiSession=$Session,
					DiType='$Type',
					DiOptions=".StrSafe_DB($Value)."
				on duplicate key update
					DiOptions=".StrSafe_DB($Value)."
					");
			$q=safe_r_SQL("select DiDay, DiStart, DiDuration, DiWarmStart, DiWarmDuration, DiOptions, DiShift
				from DistanceInformation
				where DiTournament={$_SESSION['TourId']} and DiDistance=$Dist and DiSession=$Session and DiType='$Type'");
			return DistanceInfoData(safe_fetch($q));
		}
	}
	return array('error' => 1);
}

function DistanceInfoData($r='', $delete=false, $TextScheduler=false) {
	global $CFG;
	$return=array('error' => 1);
	if($r or $delete) {
		$return['error']=0;
		if(isset($r->DiDay)) $return['day']=$r->DiDay=='0000-00-00' ? '' : $r->DiDay;
		if(isset($r->DiStart)) $return['start']=$r->DiStart=='00:00:00' ? '' : substr($r->DiStart, 0, 5);
		if(isset($r->DiDuration)) $return['duration']=$r->DiDuration;
		if(isset($r->DiWarmStart)) $return['warmtime']=$r->DiWarmStart=='00:00:00' ? '' : substr($r->DiWarmStart, 0, 5);
		if(isset($r->DiWarmDuration)) $return['warmduration']=$r->DiWarmDuration;
		if(isset($r->DiShift)) $return['shift']=$r->DiShift;
		if(isset($r->DiOptions)) $return['options']=$r->DiOptions;
		if(isset($r->old)) $return['old']=$r->old;
		if(isset($r->new)) $return['new']=$r->new;
		if(isset($r->SchTitle)) $return['title']=$r->SchTitle;
		if(isset($r->SchSubTitle)) $return['subtitle']=$r->SchSubTitle;
		if(isset($r->SchText)) $return['text']=$r->SchText;
		if(isset($r->SchOrder)) $return['order']=$r->SchOrder;
		if(isset($r->oldTimName)) $return['oldTimName']=$r->oldTimName;
		if(isset($r->oldDurName)) $return['oldDurName']=$r->oldDurName;
		if(isset($r->oldOptName)) $return['oldOptName']=$r->oldOptName;
		if(isset($r->newTimName)) $return['newTimName']=$r->newTimName;
		if(isset($r->newDurName)) $return['newDurName']=$r->newDurName;
		if(isset($r->newOptName)) $return['newOptName']=$r->newOptName;

		if($delete) $return['del']=1;

		$Schedule=new Scheduler();
		$Schedule->ROOT_DIR=$CFG->ROOT_DIR;
		$return['sch']=$Schedule->getScheduleHTML('SET');

		if($TextScheduler) $return['txt']=getScheduleTexts();
	}
	return $return;
}

function ChangeFinSchedDate($Request, $Team='0') {
	foreach($Request as $Phase => $Dates) {
		foreach($Dates as $OldDate => $Times) {
			foreach($Times as $OldTime => $Value) {
				if(!$Value or $Value=='-') {
					$Value='';
				} elseif(strtolower(substr($Value, 0, 1))=='d') {
					$Value=date('Y-m-d', strtotime(sprintf('%+d days', substr($Value, 1) -1), $_SESSION['ToWhenFromUTS']));
				} else {
					$Value=CleanDate($Value);
				}

				if($Value) {
					safe_w_sql("Update FinWarmup
						inner join FinSchedule on FsEvent=FwEvent and FsTeamEvent=FwTeamEvent and FsTournament=FwTournament and FsScheduledDate=FwDay and FsScheduledTime=FwMatchTime
						inner join Grids on FsMatchNo=GrMatchNo and GrPhase=$Phase
						set FwDay='$Value'
						where
							FwTournament={$_SESSION['TourId']}
							AND FwTeamEvent=$Team
							AND FwDay='$OldDate'
							AND FwMatchTime='$OldTime'
						");
					safe_w_sql("Update FinSchedule
						inner join Grids on FsMatchNo=GrMatchNo and GrPhase=$Phase
						set FsScheduledDate='$Value'
						where
							FsTournament={$_SESSION['TourId']}
							AND FsTeamEvent=$Team
							AND FsScheduledDate='$OldDate'
							AND FsScheduledTime='$OldTime'
						");
// 				} else {
// 					safe_w_sql("update FinSchedule
// 						inner join Grids on FsMatchNo=GrMatchNo and GrPhase=$Phase
// 						set FsScheduledDate='',
// 							FsScheduledTime='',
// 							FsScheduledLen=0,
// 						where
// 							FsTournament={$_SESSION['TourId']}
// 							AND FsTeamEvent=$Team
// 							AND FsScheduledDate='$OldDate'
// 							AND FsScheduledTime='$OldTime'
// 						");
				}
				$SQL="select
						FsScheduledDate DiDay, FsScheduledTime DiStart,
						FsScheduledLen DiDuration,
						FwTime DiWarmStart,
						FwDuration DiWarmDuration,
						FwOptions DiOptions,
						'[$OldDate][$OldTime]' old,
						'[$Value][$OldTime]' new,
						ifnull(FsShift, '') as  DiShift
					from FinSchedule
					inner join Grids on FsMatchNo=GrMatchNo and GrPhase=$Phase
					left join FinWarmup on FsEvent=FwEvent and FsTeamEvent=FwTeamEvent and FsTournament=FwTournament and FsScheduledDate=FwDay and FsScheduledTime=FwMatchTime
					where FsTournament={$_SESSION['TourId']}
						AND FsTeamEvent=$Team
						AND FsScheduledDate='$Value'
						AND FsScheduledTime='$OldTime'
					group by FsTeamEvent, GrPhase, FsScheduledDate, FsScheduledTime
					order by FsTeamEvent, GrPhase desc";
				$q=safe_r_sql($SQL);
				return DistanceInfoData(safe_fetch($q));
			}

		}
	}
	return array('error' => 1);
}

function ChangeFinSchedTime($Request, $Team='0') {
	foreach($Request as $Phase => $Dates) {
		foreach($Dates as $OldDate => $Times) {
			foreach($Times as $OldTime => $Value) {
				if(!$Value or $Value=='-') {
					$Value='';
				} else {
					$t=explode(':', $Value);
					if(count($t)==1) {
						$Minutes=$Value;
						$t[1]=$t[0]%60;
						$t[0]= intval($t[0]/60);
					} else {
						$Minutes=($t[0]*60)+$t[1];
						$t[0]=intval($Minutes/60);
						$t[1]=$Minutes%60;
					}
					$Value=sprintf('%02d:%02d:00', $t[0], $t[1]);
				}
				$Minutes=0;
				if($OldTime!='00:00:00') {
					$Minutes=((substr($Value, 0, 2)*60)+substr($Value, 3, 2)) - ((substr($OldTime, 0, 2)*60)+substr($OldTime, 3, 2));
					$t=array();
					$t[0]=intval(abs($Minutes)/60);
					$t[1]=abs($Minutes)%60;
					$Minutes=($Minutes<0 ? '-' : '').sprintf('%02d:%02d:00', $t[0], $t[1]);
				}
				safe_w_sql("Update FinWarmup
					inner join FinSchedule on FsEvent=FwEvent and FsTeamEvent=FwTeamEvent and FsTournament=FwTournament and FsScheduledDate=FwDay and FsScheduledTime=FwMatchTime
					inner join Grids on FsMatchNo=GrMatchNo and GrPhase=$Phase
					set
					FwTime=if(FwTime>0, timestamp(concat(FwDay, ' ', FwTime), '$Minutes'), ''),
					FwMatchTime='$Value'
					where
						FwTournament={$_SESSION['TourId']}
						AND FwTeamEvent=$Team
						AND FwDay='$OldDate'
						AND FwMatchTime='$OldTime'
					");
				safe_w_sql("Update FinSchedule
					inner join Grids on FsMatchNo=GrMatchNo and GrPhase=$Phase
					set FsScheduledTime='$Value'
					where
						FsTournament={$_SESSION['TourId']}
						AND FsTeamEvent=$Team
						AND FsScheduledDate='$OldDate'
						AND FsScheduledTime='$OldTime'
					");
				$SQL="select
						FsScheduledDate DiDay, FsScheduledTime DiStart,
						FsScheduledLen DiDuration,
						FwTime DiWarmStart,
						FwDuration DiWarmDuration,
						FwOptions DiOptions,
						'[$OldDate][$OldTime]' old,
						'[$OldDate][$Value]' new,
						ifnull(FsShift, '') as  DiShift
					from FinSchedule
					inner join Grids on FsMatchNo=GrMatchNo and GrPhase=$Phase
					left join FinWarmup on FsEvent=FwEvent and FsTeamEvent=FwTeamEvent and FsTournament=FwTournament and FsScheduledDate=FwDay and FsScheduledTime=FwMatchTime
					where FsTournament={$_SESSION['TourId']}
						AND FsTeamEvent=$Team
						AND FsScheduledDate='$OldDate'
						AND FsScheduledTime='$Value'
						group by FsTeamEvent, GrPhase, FsScheduledDate, FsScheduledTime
						order by FsTeamEvent, GrPhase desc";
				$q=safe_r_sql($SQL);
				return DistanceInfoData(safe_fetch($q));
			}
		}
	}
	return array('error' => 1);
}

function ChangeFinSchedWarmTime($Request, $Team='0', $Warmup=false) {
	foreach($Request as $Phase => $Dates) {
		foreach($Dates as $OldDate => $Times) {
			foreach($Times as $OldTime => $WarmTimes) {
				foreach($WarmTimes as $WarmTime => $Value) {
					if(!$Value or $Value=='-') {
						$Value='';
					} else {
						$t=explode(':', $Value);
						if(count($t)==1) {
							if($Value[0]=='-') {
								// shortcut to put the warmup at some minutes before the startup
								// get the starting time
								if($OldTime!='00:00:00') {
									$Duration=abs($Value);
									$t=explode(':', AddMinutes($OldTime, $Value));
								} else {
									$t=array(0, 0);
								}
							} else {
								$Minutes=$Value;
								$t[1]=$t[0]%60;
								$t[0]= intval($t[0]/60);
							}
						} else {
							$Minutes=($t[0]*60)+$t[1];
							$t[0]=intval($Minutes/60);
							$t[1]=$Minutes%60;
						}
						$Value=sprintf('%02d:%02d:00', $t[0], $t[1]);
					}
					if(!$Value) {
						// deletes an old warmup
						safe_w_sql("delete from FinWarmup
							where
								FwTournament={$_SESSION['TourId']}
								AND FwTeamEvent=$Team
								AND FwDay='$OldDate'
								AND FwMatchTime='$OldTime'
								AND FwTime='$WarmTime:00'
							");
					} elseif(strlen($WarmTime)==5) {
						// updates an old warmup
						safe_w_sql("update FinWarmup
								set FwTime='$Value'".(isset($Duration) ? ", FwDuration=$Duration" : '' )."
								where
									FwTournament={$_SESSION['TourId']}
									AND FwTeamEvent=$Team
									AND FwDay='$OldDate'
									AND FwMatchTime='$OldTime'
									AND FwTime='$WarmTime:00'
							");
					} else {
						$WarmTime='';
						// inserts a new warmup
						safe_w_sql("Insert into FinWarmup
								select distinct FsTournament, FsEvent, FsTeamEvent, FsScheduledDate, '$Value', ".(isset($Duration) ? $Duration : 0 ).", FsScheduledTime, '', ''
								from FinSchedule
								inner join Grids on FsMatchNo=GrMatchNo and GrPhase=$Phase
								where
									FsTournament={$_SESSION['TourId']}
									AND FsTeamEvent=$Team
									AND FsScheduledDate='$OldDate'
									AND FsScheduledTime='$OldTime'
							on duplicate key update
								FwTime='$Value'".(isset($Duration) ? ", FwDuration=$Duration" : '' )."
							");
					}
					$SQL="select
							FwDay DiDay,
							FwTime DiWarmStart,
							FwDuration DiWarmDuration,
							FwOptions DiOptions,
							'Fld[".($Team?'T':'I')."][WarmTime][$Phase][$OldDate][$OldTime][$WarmTime]' as oldTimName,
							'Fld[".($Team?'T':'I')."][WarmDuration][$Phase][$OldDate][$OldTime][$WarmTime]' as oldDurName,
							'Fld[".($Team?'T':'I')."][Options][$Phase][$OldDate][$OldTime][$WarmTime]' as oldOptName,
							'Fld[".($Team?'T':'I')."][WarmTime][$Phase][$OldDate][$OldTime][".substr($Value, 0, 5)."]' as newTimName,
							'Fld[".($Team?'T':'I')."][WarmDuration][$Phase][$OldDate][$OldTime][".substr($Value, 0, 5)."]' as newDurName,
							'Fld[".($Team?'T':'I')."][Options][$Phase][$OldDate][$OldTime][".substr($Value, 0, 5)."]' as newOptName
							".(isset($Duration) ? ", FwDuration DiDuration" : '' )."
						from FinWarmup
						where FwTournament={$_SESSION['TourId']}
							AND FwTeamEvent=$Team
							AND FwDay='$OldDate'
							AND FwMatchTime='$OldTime'
							AND FwTime='$Value'";
					$q=safe_r_sql($SQL);
					return DistanceInfoData(safe_fetch($q), $Value=='');
				}
			}
		}
	}
	return array('error' => 1);
}

function ChangeFinSchedDuration($Request, $Team='0') {
	foreach($Request as $Phase => $Dates) {
		foreach($Dates as $OldDate => $Times) {
			foreach($Times as $OldTime => $Value) {
				$Value=intval($Value);
				safe_w_sql("Update FinSchedule
					inner join Grids on FsMatchNo=GrMatchNo and GrPhase=$Phase
					set FsScheduledLen='$Value'
					where
						FsTournament={$_SESSION['TourId']}
						AND FsTeamEvent=$Team
						AND FsScheduledDate='$OldDate'
						AND FsScheduledTime='$OldTime'
					");
				$SQL="select
						FsScheduledDate DiDay, FsScheduledTime DiStart,
						FsScheduledLen DiDuration,
						FwTime DiWarmStart,
						FwDuration DiWarmDuration,
						FwOptions DiOptions,
						ifnull(FsShift, '') as  DiShift
					from FinSchedule
					inner join Grids on FsMatchNo=GrMatchNo and GrPhase=$Phase
					left join FinWarmup on FsEvent=FwEvent and FsTeamEvent=FwTeamEvent and FsTournament=FwTournament and FsScheduledDate=FwDay and FsScheduledTime=FwMatchTime
					where FsTournament={$_SESSION['TourId']}
						AND FsTeamEvent=$Team
						AND FsScheduledDate='$OldDate'
						AND FsScheduledTime='$OldTime'
						group by FsTeamEvent, GrPhase, FsScheduledDate, FsScheduledTime
						order by FsTeamEvent, GrPhase desc";
				$q=safe_r_sql($SQL);
				return DistanceInfoData(safe_fetch($q));
			}
		}
	}
	return array('error' => 1);
}

function ChangeFinSchedWarmDuration($Request, $Team='0') {
	foreach($Request as $Phase => $Dates) {
		foreach($Dates as $OldDate => $Times) {
			foreach($Times as $OldTime => $WarmTimes) {
				foreach($WarmTimes as $WarmTime => $Value) {
					$Value=intval($Value);
					if(strlen($WarmTime)!=5) return;

					safe_w_sql("Update FinWarmup
							set FwDuration='$Value'
							where
								FwTournament={$_SESSION['TourId']}
								AND FwTeamEvent=$Team
								AND FwDay='$OldDate'
								AND FwMatchTime='$OldTime'
								AND FwTime='$WarmTime:00'
						");
					$SQL="select
							FwDay DiDay,
							FwTime DiWarmStart,
							FwDuration DiWarmDuration,
							FwOptions DiOptions,
							'Fld[".($Team?'T':'I')."][WarmTime][$Phase][$OldDate][$OldTime][$WarmTime]' as oldTimName,
							'Fld[".($Team?'T':'I')."][WarmDuration][$Phase][$OldDate][$OldTime][$WarmTime]' as oldDurName,
							'Fld[".($Team?'T':'I')."][Options][$Phase][$OldDate][$OldTime][$WarmTime]' as oldOptName,
							'Fld[".($Team?'T':'I')."][WarmTime][$Phase][$OldDate][$OldTime][$WarmTime]' as newTimName,
							'Fld[".($Team?'T':'I')."][WarmDuration][$Phase][$OldDate][$OldTime][$WarmTime]' as newDurName,
							'Fld[".($Team?'T':'I')."][Options][$Phase][$OldDate][$OldTime][$WarmTime]' as newOptName
							".(isset($Duration) ? ", FwDuration DiDuration" : '' )."
						from FinWarmup
						where FwTournament={$_SESSION['TourId']}
							AND FwTeamEvent=$Team
							AND FwDay='$OldDate'
							AND FwMatchTime='$OldTime'
							AND FwTime='$WarmTime:00'";
					$q=safe_r_sql($SQL);
					return DistanceInfoData(safe_fetch($q));
				}
			}
		}
	}
	return array('error' => 1);
}

function ChangeFinShift($Request, $Team='0') {
	foreach($Request as $Phase => $Dates) {
		foreach($Dates as $OldDate => $Times) {
			foreach($Times as $OldTime => $Value) {
				if(strlen($Value)) {
					$Value=StrSafe_DB(intval($Value));
				} else {
					$Value='null';
				}
				safe_w_sql("Update FinSchedule
					inner join Grids on FsMatchNo=GrMatchNo and GrPhase=$Phase
					set FsShift=$Value
					where
						FsTournament={$_SESSION['TourId']}
						AND FsTeamEvent=$Team
						AND FsScheduledDate='$OldDate'
						AND FsScheduledTime='$OldTime'
					");
				$SQL="select
						FsScheduledDate DiDay, FsScheduledTime DiStart,
						FsScheduledLen DiDuration,
						FwTime DiWarmStart,
						FwDuration DiWarmDuration,
						FwOptions DiOptions,
						ifnull(FsShift, '') as  DiShift
					from FinSchedule
					inner join Grids on FsMatchNo=GrMatchNo and GrPhase=$Phase
					left join FinWarmup on FsEvent=FwEvent and FsTeamEvent=FwTeamEvent and FsTournament=FwTournament and FsScheduledDate=FwDay and FsScheduledTime=FwMatchTime
					where FsTournament={$_SESSION['TourId']}
						AND FsTeamEvent=$Team
						AND FsScheduledDate='$OldDate'
						AND FsScheduledTime='$OldTime'
						group by FsTeamEvent, GrPhase, FsScheduledDate, FsScheduledTime
						order by FsTeamEvent, GrPhase desc";
				$q=safe_r_sql($SQL);
				return DistanceInfoData(safe_fetch($q));
			}
		}
	}
	return array('error' => 1);
}

function ChangeFinComment($Request, $Team='0') {
	foreach($Request as $Phase => $Dates) {
		foreach($Dates as $OldDate => $Times) {
			foreach($Times as $OldTime => $WarmTimes) {
				foreach($WarmTimes as $WarmTime => $Value) {
					if(strlen($WarmTime)!=5) return;
					safe_w_sql("update FinWarmup
							set FwOptions=".StrSafe_DB($Value)."
							where
								FwTournament={$_SESSION['TourId']}
								AND FwTeamEvent=$Team
								AND FwDay='$OldDate'
								AND FwMatchTime='$OldTime'
								AND FwTime='$WarmTime:00'
						");
					$SQL="select
							FwDay DiDay,
							FwTime DiWarmStart,
							FwDuration DiWarmDuration,
							FwOptions DiOptions,
							'Fld[".($Team?'T':'I')."][WarmTime][$Phase][$OldDate][$OldTime][$WarmTime]' as oldTimName,
							'Fld[".($Team?'T':'I')."][WarmDuration][$Phase][$OldDate][$OldTime][$WarmTime]' as oldDurName,
							'Fld[".($Team?'T':'I')."][Options][$Phase][$OldDate][$OldTime][$WarmTime]' as oldOptName,
							'Fld[".($Team?'T':'I')."][WarmTime][$Phase][$OldDate][$OldTime][$WarmTime]' as newTimName,
							'Fld[".($Team?'T':'I')."][WarmDuration][$Phase][$OldDate][$OldTime][$WarmTime]' as newDurName,
							'Fld[".($Team?'T':'I')."][Options][$Phase][$OldDate][$OldTime][$WarmTime]' as newOptName
							".(isset($Duration) ? ", FwDuration DiDuration" : '' )."
						from FinWarmup
						where FwTournament={$_SESSION['TourId']}
							AND FwTeamEvent=$Team
							AND FwDay='$OldDate'
							AND FwMatchTime='$OldTime'
							AND FwTime='$WarmTime:00'";
					$q=safe_r_sql($SQL);
					return DistanceInfoData(safe_fetch($q));
				}
			}
		}
	}
	return array('error' => 1);
}

function out($Value=array('error' => 1)) {
	header('Content-Type: text/xml');

	echo '<response>';
	foreach($Value as $fld => $data) {
		echo "<$fld><![CDATA[$data]]></$fld>";
	}
	echo '</response>';
	die();
}

function getScheduleTexts() {
	global $CFG;
	$ret='';
	$q=safe_r_sql("select Scheduler.*, if(SchStart=0, '', date_format(SchStart, '%H:%i')) Start from Scheduler
		where SchTournament={$_SESSION['TourId']} and SchDay>0
		order by SchDay, SchStart, SchDuration");
	$ret.='<tr>
			<th class="Title" colspan="9">'.get_text('Z-Session', 'Tournament').'</th>
		</tr>
		<tr>
			<th class="Title" width="10%"><img src="'.$CFG->ROOT_DIR.'Common/Images/Tip.png" title="'.get_Text('TipDate', 'Tournament').'" align="right">'.get_text('Date', 'Tournament').'</th>
			<th class="Title" width="10%">'.get_text('Time', 'Tournament').'</th>
			<th class="Title" width="10%">'.get_text('Order', 'Tournament').'</th>
			<th class="Title" width="10%">'.get_text('Length', 'Tournament').'</th>
			<th class="Title" width="10%">'.get_text('Delayed', 'Tournament').'</th>
			<th class="Title" width="10%">'.get_text('Title', 'Tournament').'</th>
			<th class="Title" width="10%">'.get_text('SubTitle', 'Tournament').'</th>
			<th class="Title" width="10%">'.get_text('Text', 'Tournament').'</th>
				<th class="Title"></th>
		</tr>';
	$ret.= '<tr>
			<td><input size="10" type="text" name="Fld[Day]"></td>
			<td><input size="5"  type="text" name="Fld[Start]"></td>
			<td><input size="5"  type="text" name="Fld[Order]"></td>
			<td><input size="3"  type="text" name="Fld[Duration]"></td>
			<td><input size="5"  type="text" name="Fld[Shift]"></td>
			<td><input size="30" type="text" name="Fld[Title]"></td>
			<td><input size="30" type="text" name="Fld[SubTitle]"></td>
			<td><input size="30" type="text" name="Fld[Text]"></td>
			<td><input type="button" onclick="DiInsert(this)" value="'.get_text('CmdAdd', 'Tournament').'"></td>
		</tr>';
	while($r=safe_fetch($q)) {
		$ret.= '<tr>
				<td><input size="10" type="text" name="Fld[Z][Day]['.$r->SchDay.']['.$r->SchStart.']['.$r->SchOrder.']" value="'.$r->SchDay.'" onchange="DiUpdate(this)"></td>
				<td><input size="5"  type="text" name="Fld[Z][Start]['.$r->SchDay.']['.$r->SchStart.']['.$r->SchOrder.']" value="'.$r->Start.'" onchange="DiUpdate(this)"></td>
				<td><input size="5"  type="text" name="Fld[Z][Order]['.$r->SchDay.']['.$r->SchStart.']['.$r->SchOrder.']" value="'.$r->SchOrder.'" onchange="DiUpdate(this)"></td>
				<td><input size="3"  type="text" name="Fld[Z][Duration]['.$r->SchDay.']['.$r->SchStart.']['.$r->SchOrder.']" value="'.$r->SchDuration.'" onchange="DiUpdate(this)"></td>
				<td><input size="5"  type="text" name="Fld[Z][Shift]['.$r->SchDay.']['.$r->SchStart.']['.$r->SchOrder.']" value="'.$r->SchShift.'" onchange="DiUpdate(this)"></td>
				<td><input size="30" type="text" name="Fld[Z][Title]['.$r->SchDay.']['.$r->SchStart.']['.$r->SchOrder.']" value="'.$r->SchTitle.'" onchange="DiUpdate(this)"></td>
				<td><input size="30" type="text" name="Fld[Z][SubTitle]['.$r->SchDay.']['.$r->SchStart.']['.$r->SchOrder.']" value="'.$r->SchSubTitle.'" onchange="DiUpdate(this)"></td>
				<td><input size="30" type="text" name="Fld[Z][Text]['.$r->SchDay.']['.$r->SchStart.']['.$r->SchOrder.']" value="'.$r->SchText.'" onchange="DiUpdate(this)"></td>
				<td><input type="button" id="Fld['.$r->SchDay.']['.$r->SchStart.']['.$r->SchOrder.']" onclick="DiDelete(this)" value="'.get_text('CmdDelete', 'Tournament').'"></td>
			</tr>';
	}
	return $ret;
}