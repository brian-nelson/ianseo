<?php
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Lib/Fun_DateTime.inc.php');
require_once('Common/Lib/CommonLib.php');

Class Scheduler {
	var $Schedule=array();
	var $SingleDay='';
	var $FromDay='';
	var $TourId=0;
	var $ROOT_DIR='/';
	var $Groups=array();
	var $ActiveSessions=array();
	var $DayByDay=false;
	var $Finalists=false;
	var $SesType='';
	var $SesFilter='';
	var $DateFormat= '%W, %M %D %Y';
	var $TimeFormat='%l:%i %p';

	function __construct($TourId=0) {
		$this->TourId=($TourId ? $TourId : $this->TourId=$_SESSION['TourId']);
		if(!empty($_SESSION['ActiveSession'])) {
			$this->ActiveSessions=$_SESSION['ActiveSession'];
		} elseif($tmp=Get_Tournament_Option('ActiveSession', '', $this->TourId)) {
			$this->ActiveSessions=$tmp;
		}
	}

	function push($r, $Warmup=false, $HasWarmup=false) {
		static $Shift=0, $Day='';
		static $PushKey='';

		$tmpKey="$r->Day|$r->Start|$r->Events|$r->Session";
		if($PushKey==$tmpKey and !$Warmup) return;

		if($tmpKey and !$Warmup) $PushKey=$tmpKey;
		$tmp=new StdClass();

		// reset shift if day is different
		if($Shift and $Day!=$r->Day) {
			$Shift=0;
			$Day='';
		}

		// if a shift is defined then changes the shift
		if(strlen($r->SchDelay)) {
			$Shift=$r->SchDelay;
			$Day=$r->Day;
		}

		$tmp->Type=$r->Type;
		$tmp->Title=get_text($r->Type.'-Session', 'Tournament');
		$tmp->SubTitle=$r->SesName;
		$tmp->Text='';
		$tmp->Warmup=$Warmup;
		$tmp->Day=$r->Day;
		$tmp->Events=$r->Events;
		$tmp->Event=$r->Event;
		$tmp->Session=$r->Session;
		$tmp->Distance=$r->Distance;
		$tmp->Order=$r->OrderPhase;
		$tmp->Shift=$Shift;




		switch($r->Type) {
			case 'Q':
			case 'E':
				$tmp->SubTitle=$r->SesName ? $r->SesName : get_text('Session'). ' ' . $r->Session;
				if($r->Options and $Warmup) {
					$tmp->Text=$r->Options;
				} else {
					$tmp->Text=$r->SesName ? $r->SesName : get_text('Session'). ' ' . $r->Session;
				}
				break;
			case 'Z':
				$tmp->Title=$r->SesName;
				$tmp->SubTitle=$r->Options;
				$tmp->Text=$r->Events;
				break;
			default:
// 				if($r->Type=='I') debug_svela($r);
				$ses=$r->Session;
				if(($r->Distance==24 or $r->Distance==48) and $ses>16) {
					$ses=($ses==32 ? 24 : 48);
				}
				if(empty($tmp->Text)) $tmp->Text='';
				if($r->Type=='R') {
					list($Phase, $Round, $Group) = explode('-', $r->Session);
					$tmp->Text=', Phase '.$Phase.' Round '.$Round.' Group '.$Group;
				} else {
					$tmp->Text.=', '. get_text($ses . '_Phase' . (!$r->Medal && $ses<=1 ? "NM":""));
				}
				if($tmp->Text[0]==',') $tmp->Text=substr($tmp->Text,2);
				break;
		}

		if($Warmup) {
			$tmp->Start=$r->WarmStart;
			$tmp->Duration=$r->WarmDuration;
			$tmp->Comments=$r->Options;
		} else {
			$tmp->Start=$r->Start;
			$tmp->Duration=$r->Duration;
			$tmp->Comments=($HasWarmup ? '' : $r->Options);
		}

		$this->Schedule[$tmp->Day][$tmp->Start][$r->Session][$r->Distance][]=$tmp;
		$this->Groups[$tmp->Type][$r->Session][$r->Distance][$tmp->Day][$tmp->Start][]=$tmp;
	}

	function GetSchedule() {
		$SQL=array();
		// First gets the Texts: titles and description for a given time always go before everything else
		// getting them first to seed the array!
		if(!$this->SesType or strstr($this->SesType, 'Z')) {
			$SQL[]="select 'Z' Type,
					SchDay Day,
					'-' Session,
					'-' Distance,
					'' Medal,
					if(SchStart=0, '', date_format(SchStart, '%H:%i')) Start,
					SchDuration Duration,
					'' WarmStart,
					'' WarmDuration,
					SchSubTitle Options,
					SchTitle SesName,
					SchText Events,
					'' Event,
					SchOrder OrderPhase,
					SchShift SchDelay
				from Scheduler
				where SchTournament=$this->TourId
					and SchDay>0 and SchStart>0
					".($this->SingleDay ? " and SchDay='$this->SingleDay'" : '')."
					".($this->FromDay ? " and SchDay>='$this->FromDay'" : '')."
					";
		}

		// Then gets the qualification rounds
		if(!$this->SesType or strstr($this->SesType, 'Q')) {
			$SQL[]="select DiType Type,
					DiDay Day,
					DiSession Session,
					DiDistance Distance,
					'',
					if(DiStart=0, '', date_format(DiStart, '%H:%i')) Start,
					DiDuration Duration,
					if(DiWarmStart=0, '', date_format(DiWarmStart, '%H:%i')) WarmStart,
					DiWarmDuration WarmDuration,
					DiOptions Options,
					SesName,
					'' Events,
					'' Event,
					DiSession OrderPhase,
					DiShift SchDelay
				from DistanceInformation
				INNER join Session on SesTournament=DiTournament and SesOrder=DiSession and SesType=DiType and SesType='Q'
				where DiTournament=$this->TourId
					and DiDay>0 and (DiStart>0 or DiWarmStart>0)
					".($this->SingleDay ? " and DiDay='$this->SingleDay'" : '')."
					".($this->FromDay ? " and DiDay>='$this->FromDay'" : '')."
					".(strlen($this->SesFilter) ? " and DiSession='$this->SesFilter'" : '')."
				order by DiDay, DiStart, DiWarmStart, DiSession, DiDistance";
		}

		// Then gets the Elimination rounds
		if(!$this->SesType or strstr($this->SesType, 'E')) {
			$SQL[]="select DiType,
					DiDay,
					DiSession,
					DiDistance,
					'',
					if(DiStart=0, '', date_format(DiStart, '%H:%i')) DiStart,
					DiDuration,
					if(DiWarmStart=0, '', date_format(DiWarmStart, '%H:%i')) DiWarmStart,
					DiWarmDuration,
					DiOptions,
					SesName,
					Events,
					'' Event,
					DiSession,
					DiShift SchDelay
				from Session
				inner join (select distinct ElSession, ElTournament, ElElimPhase, group_concat(distinct ElEventCode order by ElEventCode separator ', ') Events from Eliminations where ElTournament=$this->TourId group by ElTournament, ElSession, ElElimPhase) Phase on ElSession=SesOrder and ElTournament=SesTournament
				inner join DistanceInformation on SesTournament=DiTournament and SesOrder=DiSession and ElElimPhase=DiDistance and DiType='E'
				where DiTournament=$this->TourId
					and DiDay>0 and (DiStart>0 or DiWarmStart>0)
					".($this->SingleDay ? " and DiDay='$this->SingleDay'" : '')."
					".($this->FromDay ? " and DiDay>='$this->FromDay'" : '')."
				order by DiDay, DiStart, DiWarmStart, DiSession, DiDistance";
		}

		// Get all the Free warmups
		if(!$this->SesType or strstr($this->SesType, 'F')) {
			$SQL[]="select
				if(FwTeamEvent=0, 'I', 'T'),
				FwDay,
				'',
				'',
				'',
				date_format(FwTime, '%H:%i'),
				FwDuration,
				date_format(FwTime, '%H:%i') FwTime,
				FwDuration,
				FwOptions,
				'',
				if(count(*)=2, group_concat(distinct EvEventName order by EvEventName separator ', '), group_concat(distinct FwEvent order by FwEvent separator ', ')) Events,
				group_concat(distinct FwEvent order by FwEvent separator '\',\'') Event,
				'',
				''

				from FinWarmup
				inner join Events on FwEvent=EvCode and EvTeamEvent=FwTeamEvent and EvTournament=FwTournament
				where FwTournament=$this->TourId
					and FwMatchTime=0
				group by FwTeamEvent, FwDay, FwTime
				";
		}

		// Then gets the robin rounds
		if(!$this->SesType or strstr($this->SesType, 'R')) {
			$SQL[]="select
					'R' Type,
					date_format(F2FSchedule, '%Y-%m-%d') Day,
					concat(F2FPhase, '-', F2FRound, '-', F2FGroup) Session,
					F2FPhase Distance,
					'',
					if(F2FSchedule=0, '', date_format(F2FSchedule, '%H:%i')) Start,
					0 Duration,
					'' WarmStart,
					0 WarmDuration,
					0 Options,
					'',
					if(count(*)=2, group_concat(distinct EvEventName order by EvEventName separator ', '), group_concat(distinct F2FEvent order by F2FEvent separator ', ')) Events,
					group_concat(distinct F2FEvent order by F2FEvent separator '\',\'') Event,
					1 OrderPhase,
					0 SchDelay
				from F2FFinal
				inner join Events on F2FEvent=EvCode and EvTeamEvent=0 and F2FTournament=EvTournament
				where F2FTournament=$this->TourId
					and F2FSchedule>0
					".($this->SingleDay ? " and date_format(F2FSchedule, '%Y-%m-%d')='$this->SingleDay'" : '')."
					".($this->FromDay ? " and date_format(F2FSchedule, '%Y-%m-%d')>='$this->FromDay'" : '')."
				group by F2FPhase, F2FSchedule
				";
		}

		// Get all the matches
		if(!$this->SesType or strstr($this->SesType, 'F')) {
			$SQL[]="select
				if(FsTeamEvent=0, 'I', 'T'),
				FsScheduledDate,
				GrPhase,
				EvFinalFirstPhase,
				EvMedals,
				if(FsScheduledTime=0, '', date_format(FsScheduledTime, '%H:%i')) ScheduledTime,
				FsScheduledLen,
				if(FwTime=0, '', date_format(FwTime, '%H:%i')) FwTime,
				FwDuration,
				FwOptions,
				'',
				if(count(*)=2, group_concat(distinct EvEventName order by EvEventName separator ', '), group_concat(distinct FsEvent order by FsEvent separator ', ')) Events,
				group_concat(distinct FsEvent order by FsEvent separator '\',\'') Event,
				1+(1/(1+GrPhase)),
				FsShift SchDelay

				from FinSchedule
				inner join Events on FsEvent=EvCode and FsTeamEvent=EvTeamEvent and FsTournament=EvTournament
				inner join Grids on FsMatchNo=GrMatchNo
				left join FinWarmup on FsEvent=FwEvent and FsTeamEvent=FwTeamEvent and FsTournament=FwTournament and FsScheduledDate=FwDay and FsScheduledTime=FwMatchTime
				where FsTournament=$this->TourId
					and FsScheduledDate>0 and (FsScheduledTime>0 or FwTime>0)
					".($this->SingleDay ? " and FsScheduledDate='$this->SingleDay'" : '')."
					".($this->FromDay ? " and FsScheduledDate>='$this->FromDay'" : '')."
				group by FsTeamEvent, GrPhase, FsScheduledDate, FsScheduledTime, FwTime
				";
		}

		$q=safe_r_SQL('('.implode(') UNION (', $SQL).') order by Day, if(Start>0, if(WarmStart>0, least(Start, WarmStart), Start), WarmStart), Type!=\'Z\', OrderPhase, Distance');

		while($r=safe_fetch($q)) {
			if($r->WarmStart) {
				$this->push($r, true);
			}
			if($r->Start) {
				$this->push($r, false, $r->WarmStart);
			}
		}

// debug_svela($SQL);
		return $this->Schedule;
	}

	/**
	 * @param string $Type
	 * Default value is IS, other values: SET, SHOW
	 * @return string
	 *
	 * Returns the HTML representation of the Schedule
	 */
	function getScheduleHTML($Type='IS') {
		$ret=array();
		foreach($this->GetSchedule() as $Date => $Times) {
			$ret[]='<tr><th colspan="2" class="SchDay">'.formatTextDate($Date).'</th></tr>';
			$OldTitle='';
			$OldSubTitle='';
			$OldType='';
			$OldStart='';
			$OldEnd='';
			$IsTitle=false;

			$OldComment='';
			ksort($Times);
			foreach($Times as $Time => $Sessions) {
				foreach($Sessions as $Session => $Distances) {
					foreach($Distances as $Distance => $Items) {
						foreach($Items as $k => $Item) {
							$key=$Item->Day
								.'|'.$Time
								.'|'.$Session
								.'|'.$Distance
								.'|'.$Item->Order;
							$ActiveSession=in_array($key, $this->ActiveSessions);

// 							if($ActiveSession) debug_svela($Item);
							$timing='';

							if($Item->Type=='Z') {
								// free text
								$timing=$Item->Start.($Item->Duration ? '-'.addMinutes($Item->Start, $Item->Duration) : '');

								if($OldTitle!=$Item->Title and $Item->Title) {
									if(!$IsTitle) {
										$tmp='<tr'.(($ActiveSession and !$Item->SubTitle and !$Item->Text) ? ' class="active"' : '').'><td>';
										if(!$Item->SubTitle and !$Item->Text) {
											$tmp.=$timing . ($Item->Shift && $timing ? ($Type=='IS' ? '<span class="SchDelay">' : '') . '&nbsp;+' . $Item->Shift . ($Type=='IS' ? '</span>' : ''): "");
											$timing='';
										}
										$txt=$Item->Title;
										if($Type=='SET') {
											$txt='<a href="?Activate='.$key.'">'.$txt.'</a>';
										}

										$tmp.='</td><td class="SchTitle">'.$txt.'</td></tr>';
										$ret[]=$tmp;
									}
									$OldTitle=$Item->Title;
									$OldSubTitle='';
									$IsTitle=true;
								}
								if($OldSubTitle!=$Item->SubTitle and $Item->SubTitle) {
									$tmp='<tr'.(($ActiveSession and !$Item->Text) ? ' class="active"' : '').'><td>';
									if(!$Item->Text) {
										$tmp.=$timing . ($Item->Shift && $timing ? ($Type=='IS' ? '<span class="SchDelay">' : '') . '&nbsp;+' . $Item->Shift . ($Type=='IS' ? '</span>' : ''): "");
										$timing='';
									}
									$txt=$Item->SubTitle;
									if($Type=='SET') {
										$txt='<a href="?Activate='.$key.'">'.$txt.'</a>';
									}
									$tmp.='</td><td class="SchSubTitle">'.$txt.'</td></tr>';
									$ret[]=$tmp;
									$OldSubTitle=$Item->SubTitle;
									$IsTitle=false;
								}
								if($Item->Text) {
									$txt=$Item->Text;
									if($Type=='SET') {
										$txt='<a href="?Activate='.$key.'">'.$txt.'</a>';
									}
									$tmp='<tr'.($ActiveSession ? ' class="active"' : '').'><td>';
									$tmp.=$timing . ($Item->Shift && $timing ? ($Type=='IS' ? '<span class="SchDelay">' : '') . '&nbsp;+' . $Item->Shift . ($Type=='IS' ? '</span>' : ''): "");
									$ret[]=$tmp.'</td><td class="SchItem">'.$txt.'</td></tr>';
									$timing='';
									$IsTitle=false;
								}
								$OldStart=$Item->Start;
								$OldEnd=$Item->Duration;
								$OldComment='';
							} else {
								// all other kind of texts have a title and the items
								if($OldTitle!=$Item->Title) {
									// Title
									if(!$IsTitle) {
										$ret[]='<tr><td></td><td class="SchTitle">'.$Item->Title.'</td></tr>';
									}
									$OldTitle=$Item->Title;
									$OldSubTitle='';
									$IsTitle=true;
								}
								if($OldSubTitle!=$Item->SubTitle) {
									// SubTitle
									$ret[]='<tr><td></td><td class="SchSubTitle">'.$Item->SubTitle.'</td></tr>';
									$OldSubTitle=$Item->SubTitle;
									$IsTitle=false;
								}

								$timing='';
								if($OldStart != $Item->Start or $OldEnd != $Item->Duration) {
									$timing=$Item->Start.($Item->Duration ? '-'.addMinutes($Item->Start, $Item->Duration) : '');
									$OldStart=$Item->Start;
									$OldEnd=$Item->Duration;
								}

								$lnk=$Item->Text;
								if(!$Item->Warmup) {
									// not warmup!
									$OldComment='';
									switch($Item->Type) {
										case 'Q':
										case 'E':
											$t=safe_r_SQL("select distinct EcCode, EcTeamEvent from Entries
												INNER JOIN Qualifications on QuId=EnId and QuSession=$Item->Session
												INNER JOIN EventClass ON EcClass=EnClass AND EcDivision=EnDivision AND EcTournament=EnTournament
												INNER JOIN Events on EvCode=EcCode AND EvTeamEvent=IF(EcTeamEvent!=0, 1,0) AND EvTournament=EcTournament
												where EnTournament=$this->TourId
												order by EvTeamEvent, EvProgr");
											$Link=array();
											$lnk='';
											if($Item->Comments) {
												$txt=$Item->Comments;
												if($Type=='SET') {
													$txt='<a href="?Activate='.urlencode($key).'">'.$txt.'</a>';
												}
												$ret[]='<tr'.($ActiveSession ? ' class="active"' : '').'><td>'
													. $timing . ($Item->Shift && $timing ? ($Type=='IS' ? '<span class="SchDelay">' : '') . '&nbsp;+' . $Item->Shift . ($Type=='IS' ? '</span>' : ''): "")
													.'</td><td class="SchWarmup">'.$txt.'</td></tr>';
												$timing='';
											}
											if($Type=='IS') {
												while($u=safe_fetch($t)) {
													$Link[$u->EcTeamEvent][]='Event[]='.$u->EcCode;
												}

												if(!empty($Link[0])) {
													$lnk.='<br/><a href="'.$this->ROOT_DIR.'Qualification/?type=0&'.implode('&',$Link[0]).'">'.get_text('ViewIndividualResults', 'InfoSystem').'</a>';
												}
												if(!empty($Link[1])) {
													$lnk.='<br/><a href="'.$this->ROOT_DIR.'Qualification/?type=1&'.implode('&',$Link[1]).'">'.get_text('ViewTeamResults', 'InfoSystem').'</a>';
												}
											}
											if(count($this->Groups[$Item->Type][$Session])==1) {
												$txt=$Item->Text.$lnk;
											} elseif($Item==@end(end(end(end($this->Groups[$Item->Type][$Session]))))) {
												$txt=get_text('Distance', 'Tournament'). ' '.$Distance.$lnk;
											} else {
												$txt=get_text('Distance', 'Tournament'). ' '.$Distance;
												// more distances defined so format is different...
											}

											if($Type=='SET') {
												$txt='<a href="?Activate='.urlencode($key).'">'.$txt.'</a>';
											}
											$ret[]='<tr'.($ActiveSession ? ' class="active"' : '').'><td>'
												. $timing . ($Item->Shift && $timing ? ($Type=='IS' ? '<span class="SchDelay">' : '') . '&nbsp;+' . $Item->Shift . ($Type=='IS' ? '</span>' : ''): "")
												.'</td><td class="SchItem">'.$txt.'</td></tr>';
											$IsTitle=false;
											break;
										case 'I':
										case 'T':
											$lnk=$Item->Text.': '.$Item->Events;
											if($this->Finalists && $Item->Session<=1) {
												// Bronze or Gold Finals
												if($Item->Type=='I') {
													$SQL="select concat(upper(e1.EnFirstname), ' ', e1.EnName, ' (', c1.CoCode, ')') LeftSide,
													concat('(', c2.CoCode, ') ', upper(e2.EnFirstname), ' ', e2.EnName) RightSide
													from Finals tf1
													inner join Finals tf2 on tf1.FinEvent=tf2.FinEvent and tf1.FinTournament=tf2.FinTournament and tf2.FinMatchNo=tf1.FinMatchNo+1 and tf2.FinMatchNo%2=1
													inner join Entries e1 on e1.EnId=tf1.FinAthlete and tf1.FinEvent IN ('$Item->Event')
													inner join Entries e2 on e2.EnId=tf2.FinAthlete and tf2.FinEvent IN ('$Item->Event')
													inner join Countries c1 on e1.EnCountry=c1.CoId and c1.CoTournament=$this->TourId
													inner join Countries c2 on e2.EnCountry=c2.CoId and c2.CoTournament=$this->TourId
													inner join Grids on tf1.FinMatchNo=GrMatchNo and GrPhase=$Item->Session
													where tf1.FinTournament=$this->TourId";
												} else {
													$SQL="select concat(c1.CoName, ' (', c1.CoCode, ')') LeftSide,
													concat('(', c2.CoCode, ') ', c2.CoName) RightSide
													from TeamFinals tf1
													inner join TeamFinals tf2 on tf1.TfEvent=tf2.TfEvent and tf1.TfTournament=tf2.TfTournament and tf2.TfMatchNo=tf1.TfMatchNo+1 and tf2.TfMatchNo%2=1
													inner join Countries c1 on c1.CoId=tf1.TfTeam and tf1.TfEvent IN ('$Item->Event')
													inner join Countries c2 on c2.CoId=tf2.TfTeam and tf2.TfEvent IN ('$Item->Event')
													inner join Grids on tf1.TfMatchNo=GrMatchNo and GrPhase=$Item->Session
													where tf1.TfTournament=$this->TourId";
												}
												// 												debug_svela($SQL);
												$q=safe_r_SQL($SQL);
												if($r=safe_fetch($q) and trim($r->LeftSide) and trim($r->RightSide)) {
													$lnk.= '<br>' . $r->LeftSide.' - '.$r->RightSide;
												}
											}
// 											if($Time=='11:25') debug_svela($Item);
											if($Type=='SET') {
												$lnk='<a href="?Activate='.urlencode($key).'">'.$lnk.'</a>';
											} elseif($Type=='IS') {
												$lnk='<a href="'.$this->ROOT_DIR.'Finals/session.php?Session='.urlencode(($Item->Type=='T' ? 1 : 0)."$Item->Day $Item->Start:00").'">'.$lnk.'</a>';
											}
											$ret[]='<tr'.($ActiveSession ? ' class="active"' : '').'><td>'
												. $timing . ($Item->Shift && $timing ? ($Type=='IS' ? '<span class="SchDelay">' : '') . '&nbsp;+' . $Item->Shift . ($Type=='IS' ? '</span>' : ''): "")
												.'</td><td class="SchItem">'.$lnk.'</td></tr>';
											$IsTitle=false;
// 											debug_svela($Item);
											break;
										case 'R':
											$lnk=$Item->Text.': '.$Item->Events;
											if($this->Finalists) {
												list($Phase, $Round, $Group)=explode('-', $Item->Session);
												$SQL="select concat(upper(e1.EnFirstname), ' ', e1.EnName, ' (', c1.CoCode, ')') LeftSide,
													tf1.F2FTarget LeftTgt, tf2.F2FTarget RightTgt,
													concat('(', c2.CoCode, ') ', upper(e2.EnFirstname), ' ', e2.EnName) RightSide
													from F2FGrid g
													inner join F2FFinal tf1 on g.F2FTournament=tf1.F2FTournament and g.F2FPhase=tf1.F2FPhase and g.F2FRound=tf1.F2FRound and g.F2FGroup=tf1.F2FGroup and g.F2FMatchNo1=tf1.F2FMatchNo
													inner join F2FFinal tf2 on g.F2FTournament=tf2.F2FTournament and g.F2FPhase=tf2.F2FPhase and g.F2FRound=tf2.F2FRound and g.F2FGroup=tf2.F2FGroup and g.F2FMatchNo2=tf2.F2FMatchNo and tf1.F2FEvent=tf2.F2FEvent
													inner join Entries e1 on e1.EnId=tf1.F2FEnId and tf1.F2FEvent IN ('$Item->Event')
													inner join Entries e2 on e2.EnId=tf2.F2FEnId and tf2.F2FEvent IN ('$Item->Event')
													inner join Countries c1 on e1.EnCountry=c1.CoId and c1.CoTournament=$this->TourId
													inner join Countries c2 on e2.EnCountry=c2.CoId and c2.CoTournament=$this->TourId
													where g.F2FTournament=$this->TourId and tf1.F2FSchedule='$Date $Time'";
// 												debug_svela($SQL);
												$q=safe_r_SQL($SQL);
												while($r=safe_fetch($q) and trim($r->LeftSide) and trim($r->RightSide)) {
													if($r->LeftTgt < $r->RightTgt) {
														$lnk.= '<br>' . $r->LeftSide.' - '.$r->RightSide;
													} else {
														$lnk.= '<br>' . $r->RightSide.' - '.$r->LeftSide;
													}
												}
											}
											if($Type=='SET') {
												$lnk='<a href="?Activate='.urlencode($key).'">'.$lnk.'</a>';
											} elseif($Type=='IS') {
												$lnk='<a href="'.$this->ROOT_DIR.'Rounds/?Session='.urlencode("$Item->Day $Item->Start:00").'">'.$lnk.'</a>';
											}
											$ret[]='<tr'.($ActiveSession ? ' class="active"' : '').'><td>'
												. $timing . ($Item->Shift && $timing ? ($Type=='IS' ? '<span class="SchDelay">' : '') . '&nbsp;+' . $Item->Shift . ($Type=='IS' ? '</span>' : ''): "")
												.'</td><td class="SchItem">'.$lnk.'</td></tr>';
											$IsTitle=false;
// 											debug_svela($Item);
											break;
										default:
											debug_svela($Item);
									}

								} else {
									if($Item->Comments) {
										$lnk=$Item->Comments;
									} else {
										switch($Item->Type) {
											case 'I':
											case 'T':
												$lnk=$Item->Text.': '.$Item->Events.' '.'warmup';
												break;
											default:
												$lnk.=' Warmup';
										}
									}

									if($OldComment==$lnk) continue;

									$OldComment=$lnk;

									if($Type=='SET') {
										$lnk='<a href="?Activate='.urlencode($key).'">'.$lnk.'</a>';
									}
									$ret[]='<tr'.($ActiveSession ? ' class="active"' : '').'><td>'
										. $timing . ($Item->Shift && $timing ? ($Type=='IS' ? '<span class="SchDelay">' : '') . '&nbsp;+' . $Item->Shift . ($Type=='IS' ? '</span>' : ''): "")
										.'</td><td class="SchItem SchWarmup">'.$lnk.'</td></tr>';
									$IsTitle=false;
								}
							}
						}
					}
				}
			}
		}
		if($ret) {
			return '<table width="100%">'.implode('', $ret).'</table>';
		}
		return '';
	}

	/**
	 * @param string $pdf
	 * If empty creates and returns a pdf, otherwise adds page to an existant pdf
	 * @return tcpdf object
	 *
	 *
	 */
	function getSchedulePDF(&$pdf='') {
		if(empty($pdf)) {
			require_once('Common/pdf/IanseoPdf.php');
			$pdf= new IanseoPdf('Scheduler');
			$pdf->startPageGroup();
			$pdf->AddPage();
		} else {
			$pdf->AddPage();
		}

		$Start=true;
		$StartX=$pdf->getX();
		$FontAdjust= 1;
		$TimingWidth=20;
		$DelayWidth=10;
		$descrSize=$pdf->getPageWidth() - 40;
		$CellHeight=5;
		if($this->DayByDay) {
			$FontAdjust= 2;
			$TimingWidth=30;
			$StartX+=10;
			$CellHeight=8;
		} else {
			$StartX+=20;
		}


		foreach($this->GetSchedule() as $Date => $Times) {
			if(!$Start and ($this->DayByDay or !$pdf->SamePage(55))) {
				$pdf->AddPage();
			} elseif(!$Start) {
				$pdf->dy(2*$FontAdjust);
			}
			$Start=false;

			// DAY
			$pdf->SetFont($pdf->FontStd,'B',8*$FontAdjust);
			$pdf->Cell(0, $CellHeight, formatTextDate($Date),0,1,'L',1);

			$OldTitle='';
			$OldSubTitle='';
			$OldType='';
			$OldStart='';
			$OldEnd='';
			$IsTitle=false;

			$OldComment='';
			ksort($Times);
//			debug_svela($Times);
			foreach($Times as $Time => $Sessions) {
				foreach($Sessions as $Session => $Distances) {
					foreach($Distances as $Distance => $Items) {
						foreach($Items as $k => $Item) {

							if(!$pdf->SamePage(15)) {
								$pdf->AddPage();
								// Day...
								$pdf->SetFont($pdf->FontStd,'B',8*$FontAdjust);
								$pdf->Cell(0, $CellHeight, formatTextDate($Date) . '    ('.get_text('Continue').')',0,1,'L',1);

								// maybe the session title?
								if($Item->Type!='Z' and $OldTitle==$Item->Title and $OldTitle) {
									$pdf->SetFont($pdf->FontStd,'B',8*$FontAdjust);
									$pdf->SetX($StartX+$TimingWidth);
									$pdf->Cell($descrSize, $CellHeight, $Item->Title . '    ('.get_text('Continue').')',0,1,'L',0);
								}
								$pdf->SetFont($pdf->FontStd,'',8*$FontAdjust);
							}

							$timingDelayed='';
							$timing='';

							if($Item->Type=='Z') {
								// free text
								$timing=$Item->Start.($Item->Duration ? '-'.addMinutes($Item->Start, $Item->Duration) : '');
								if($Item->Shift) {
									$timingDelayed = '+'.$Item->Shift;
								}
								if($OldTitle!=$Item->Title and $Item->Title) {
									if(!$IsTitle) {
										$pdf->SetX($StartX);
										// prints timing only if alone
										if(!$Item->SubTitle and !$Item->Text) {
											if($Item->Shift and $timing) {
												$pdf->SetX($StartX-$DelayWidth);
												$pdf->Cell($DelayWidth, $CellHeight, $timingDelayed, 0, 0);
												$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
											} else {
												$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
											}
											$timing='';
										} else {
											$pdf->Cell($TimingWidth, $CellHeight, ' ', 0, 0);
										}
										$pdf->SetFont($pdf->FontStd,'B',8*$FontAdjust);
										$pdf->Cell($descrSize, $CellHeight, $Item->Title, 0, 1, 'L', 0);
										$pdf->SetFont($pdf->FontStd,'',8*$FontAdjust);
									}
									$OldTitle=$Item->Title;
									$OldSubTitle='';
									$IsTitle=true;
								}
								if($OldSubTitle!=$Item->SubTitle and $Item->SubTitle) {
									$pdf->SetX($StartX);
									if(!$Item->Text) {
										if($Item->Shift and $timing) {
											$pdf->SetX($StartX-$DelayWidth);
											$pdf->Cell($DelayWidth, $CellHeight, $timingDelayed, 0, 0);
											$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
										} else {
											$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
										}
										$timing='';
									} else {
										$pdf->Cell($TimingWidth, $CellHeight, ' ', 0, 0);
									}
									$pdf->SetFont($pdf->FontStd, 'BI', 8*$FontAdjust);
									$pdf->Cell($descrSize, $CellHeight, $Item->SubTitle, 0, 1, 'L', 0);
									$pdf->SetFont($pdf->FontStd,'',8*$FontAdjust);
									$OldSubTitle=$Item->SubTitle;
									$IsTitle=false;
								}
								if($Item->Text) {
									$pdf->SetX($StartX);
									if($Item->Shift and $timing) {
										$pdf->SetX($StartX-$DelayWidth);
										$pdf->Cell($DelayWidth, $CellHeight, $timingDelayed, 0, 0);
										$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
										$pdf->Line($StartX, $y=$pdf->GetY()+($CellHeight/2), $StartX+$TimingWidth-$FontAdjust, $y);
									} else {
										$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
									}
									$pdf->SetFont($pdf->FontStd,'',8*$FontAdjust);
									$pdf->Cell($descrSize, $CellHeight, $Item->Text, 0, 1, 'L', 0);
									$pdf->SetFont($pdf->FontStd,'',8*$FontAdjust);
									$timing='';
									$IsTitle=false;
								}
								$OldStart=$Item->Start;
								$OldEnd=$Item->Duration;
								$OldComment='';
							} else {
								// all other kind of texts have a title and the items
								if($OldTitle!=$Item->Title) {
									// Title
									if(!$IsTitle) {
										$pdf->SetX($StartX);
										$pdf->Cell($TimingWidth, $CellHeight, ' ', 0, 0);
										$pdf->SetFont($pdf->FontStd,'B',8*$FontAdjust);
										$pdf->Cell($descrSize, $CellHeight, $Item->Title, 0, 1, 'L', 0);
										$pdf->SetFont($pdf->FontStd,'',8*$FontAdjust);
									}
									$OldTitle=$Item->Title;
									$OldSubTitle='';
									$IsTitle=true;
								}
								if($OldSubTitle!=$Item->SubTitle) {
									// SubTitle
									$pdf->SetX($StartX);
									$pdf->Cell($TimingWidth, $CellHeight, ' ', 0, 0);
									$pdf->SetFont($pdf->FontStd,'BI',8*$FontAdjust);
									$pdf->Cell($descrSize, $CellHeight, $Item->SubTitle, 0, 1, 'L', 0);
									$pdf->SetFont($pdf->FontStd,'',8*$FontAdjust);
									$OldSubTitle=$Item->SubTitle;
									$IsTitle=false;
								}

								$timing='';
								if($OldStart != $Item->Start or $OldEnd != $Item->Duration) {
									$timing=$Item->Start.($Item->Duration ? '-'.addMinutes($Item->Start, $Item->Duration) : '');
									$OldStart=$Item->Start;
									$OldEnd=$Item->Duration;
									if($Item->Shift) {
										$timingDelayed = '+'.$Item->Shift;
									}
								}

								$lnk=$Item->Text;
								if(!$Item->Warmup) {
									// not warmup!
									$OldComment='';
									switch($Item->Type) {
										case 'Q':
										case 'E':
											$t=safe_r_SQL("select distinct EcCode, EcTeamEvent from Entries
												INNER JOIN Qualifications on QuId=EnId and QuSession=$Item->Session
												INNER JOIN EventClass ON EcClass=EnClass AND EcDivision=EnDivision AND EcTournament=EnTournament
												INNER JOIN Events on EvCode=EcCode AND EvTeamEvent=IF(EcTeamEvent!=0, 1,0) AND EvTournament=EcTournament
												where EnTournament=$this->TourId
												order by EvTeamEvent, EvProgr");
											$Link=array();
											$lnk='';
											if($Item->Comments) {
												$pdf->SetX($StartX);
												if($Item->Shift and $timing) {
													$pdf->SetX($StartX-$DelayWidth);
													$pdf->Cell($DelayWidth, $CellHeight, $timingDelayed, 0, 0);
													$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
												} else {
													$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
												}
												$pdf->SetFont($pdf->FontStd,'I',8*$FontAdjust);
												$pdf->Cell($descrSize, $CellHeight, $Item->Comments, 0, 1, 'L', 0);
												$pdf->SetFont($pdf->FontStd,'',8*$FontAdjust);
												$timing='';
											}

											if(count($this->Groups[$Item->Type][$Session])==1) {
												$txt=$Item->Text.$lnk;
											} elseif($Item==@end(end(end(end($this->Groups[$Item->Type][$Session]))))) {
												$txt=get_text('Distance', 'Tournament'). ' '.$Distance.$lnk;
											} else {
												$txt=get_text('Distance', 'Tournament'). ' '.$Distance;
												// more distances defined so format is different...
											}

											$pdf->SetX($StartX);
											if($Item->Shift and $timing) {
												$pdf->SetX($StartX-$DelayWidth);
												$pdf->Cell($DelayWidth, $CellHeight, $timingDelayed, 0, 0);
												$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
											} else {
												$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
											}
											$pdf->Cell($descrSize, $CellHeight, $txt, 0, 1, 'L', 0);
											$IsTitle=false;
											break;
										case 'I':
										case 'T':
											$lnk=$Item->Text.': '.$Item->Events;
											$pdf->SetX($StartX);
											if($Item->Shift and $timing) {
												$pdf->SetX($StartX-$DelayWidth);
												$pdf->Cell($DelayWidth, $CellHeight, $timingDelayed, 0, 0);
												$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
											} else {
												$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
											}
											$pdf->Cell($descrSize, $CellHeight, $lnk, 0, 1, 'L', 0);
											$IsTitle=false;
											if($this->Finalists && $Item->Session<=1) {
												// Bronze or Gold Finals
												if($Item->Type=='I') {
													$SQL="select concat(upper(e1.EnFirstname), ' ', e1.EnName, ' (', c1.CoCode, ')') LeftSide,
															concat('(', c2.CoCode, ') ', upper(e2.EnFirstname), ' ', e2.EnName) RightSide
														from Finals tf1
														inner join Finals tf2 on tf1.FinEvent=tf2.FinEvent and tf1.FinTournament=tf2.FinTournament and tf2.FinMatchNo=tf1.FinMatchNo+1 and tf2.FinMatchNo%2=1
														inner join Entries e1 on e1.EnId=tf1.FinAthlete and tf1.FinEvent IN ('$Item->Event')
														inner join Entries e2 on e2.EnId=tf2.FinAthlete and tf2.FinEvent IN ('$Item->Event')
														inner join Countries c1 on e1.EnCountry=c1.CoId and c1.CoTournament=$this->TourId
														inner join Countries c2 on e2.EnCountry=c2.CoId and c2.CoTournament=$this->TourId
														inner join Grids on tf1.FinMatchNo=GrMatchNo and GrPhase=$Item->Session
														where tf1.FinTournament=$this->TourId";
												} else {
													$SQL="select concat(c1.CoName, ' (', c1.CoCode, ')') LeftSide,
															concat('(', c2.CoCode, ') ', c2.CoName) RightSide
														from TeamFinals tf1
														inner join TeamFinals tf2 on tf1.TfEvent=tf2.TfEvent and tf1.TfTournament=tf2.TfTournament and tf2.TfMatchNo=tf1.TfMatchNo+1 and tf2.TfMatchNo%2=1
														inner join Countries c1 on c1.CoId=tf1.TfTeam and tf1.TfEvent IN ('$Item->Event')
														inner join Countries c2 on c2.CoId=tf2.TfTeam and tf2.TfEvent IN ('$Item->Event')
														inner join Grids on tf1.TfMatchNo=GrMatchNo and GrPhase=$Item->Session
														where tf1.TfTournament=$this->TourId";
												}
// 												debug_svela($SQL);
												$q=safe_r_SQL($SQL);
												if($r=safe_fetch($q) and trim($r->LeftSide) and trim($r->RightSide)) {
													$pdf->SetXY($StartX+$TimingWidth, $pdf->getY()-1.5);
													$pdf->Cell($descrSize, $CellHeight, $r->LeftSide.' - '.$r->RightSide, 0, 1, 'L', 0);
												}
											}
											break;
										case 'R':
											$lnk=$Item->Text.': '.$Item->Events;
											$pdf->SetX($StartX);
											if($Item->Shift and $timing) {
												$pdf->SetX($StartX-$DelayWidth);
												$pdf->Cell($DelayWidth, $CellHeight, $timingDelayed, 0, 0);
												$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
											} else {
												$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
											}
											$pdf->Cell($descrSize, $CellHeight, $lnk, 0, 1, 'L', 0);
											$IsTitle=false;
											if($this->Finalists) {
												list($Phase, $Round, $Group)=explode('-', $Item->Session);
												$SQL="select concat(upper(e1.EnFirstname), ' ', e1.EnName, ' (', c1.CoCode, ')') LeftSide,
													concat('(', c2.CoCode, ') ', upper(e2.EnFirstname), ' ', e2.EnName) RightSide
													from F2FGrid g
													inner join F2FFinal tf1 on g.F2FTournament=tf1.F2FTournament and g.F2FPhase=tf1.F2FPhase and g.F2FRound=tf1.F2FRound and g.F2FGroup=tf1.F2FGroup and g.F2FMatchNo1=tf1.F2FMatchNo
													inner join F2FFinal tf2 on g.F2FTournament=tf2.F2FTournament and g.F2FPhase=tf2.F2FPhase and g.F2FRound=tf2.F2FRound and g.F2FGroup=tf2.F2FGroup and g.F2FMatchNo2=tf2.F2FMatchNo and tf1.F2FEvent=tf2.F2FEvent
													inner join Entries e1 on e1.EnId=tf1.F2FEnId and tf1.F2FEvent IN ('$Item->Event')
													inner join Entries e2 on e2.EnId=tf2.F2FEnId and tf2.F2FEvent IN ('$Item->Event')
													inner join Countries c1 on e1.EnCountry=c1.CoId and c1.CoTournament=$this->TourId
													inner join Countries c2 on e2.EnCountry=c2.CoId and c2.CoTournament=$this->TourId
													where g.F2FTournament=$this->TourId and tf1.F2FSchedule='$Date $Time'";
												$q=safe_r_SQL($SQL);
												while($r=safe_fetch($q) and trim($r->LeftSide) and trim($r->RightSide)) {
													$pdf->SetXY($StartX+$TimingWidth, $pdf->getY()-1.5);
													$pdf->Cell($descrSize, $CellHeight, $r->LeftSide.' - '.$r->RightSide, 0, 1, 'L', 0);
												}
											}
											break;
										default:
											debug_svela($Item);
									}

								} else {
									if($Item->Comments) {
										$lnk=$Item->Comments;
									} else {
										switch($Item->Type) {
											case 'I':
											case 'T':
												$lnk=$Item->Text.': '.$Item->Events.' '.'warmup';
												break;
											default:
												$lnk.=' Warmup';
										}
									}
									if($OldComment==$lnk) continue;
									$OldComment=$lnk;
									$pdf->SetX($StartX);
									if($Item->Shift and $timing) {
										$pdf->SetX($StartX-$DelayWidth);
										$pdf->Cell($DelayWidth, $CellHeight, $timingDelayed, 0, 0);
										$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
									} else {
										$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
									}
									$pdf->SetFont($pdf->FontStd,'I',8*$FontAdjust);
									$pdf->Cell($descrSize, $CellHeight, $lnk, 0, 1, 'L', 0);
									$pdf->SetFont($pdf->FontStd,'',8*$FontAdjust);
									$IsTitle=false;
								}
							}
						}
					}
				}
			}
		}
		return $pdf;
	}

	function getScheduleBoinx() {
		$nDay=0;
		$ret=array();

		foreach($this->GetSchedule() as $Date => $Times) {
			$nDay++;
			$nGroup=0;
			$n=0;

			$OldTitle='';
			$OldSubTitle='';
			$OldType='';
			$OldStart='';
			$OldEnd='';
			$IsTitle=false;

			$OldComment='';
			ksort($Times);
			foreach($Times as $Time => $Sessions) {
				foreach($Sessions as $Session => $Distances) {
					foreach($Distances as $Distance => $Items) {
						foreach($Items as $k => $Item) {
							$key=$Item->Day
							.'|'.$Time
							.'|'.$Session
							.'|'.$Distance
							.'|'.$Item->Order;
							$ActiveSession=in_array($key, $this->ActiveSessions);


							$LinTim=$Item->Start.($Item->Duration ? '-'.addMinutes($Item->Start, $Item->Duration) : '');
							$LinTit='';
							$LinSub='';
							$LinTxt='';
							if($Item->Type=='Z') {
								// free text
								$OldComment='';
								if($OldTitle!=$Item->Title and $Item->Title) {
									if(!$IsTitle) {
										$LinTit=$Item->Title;
									}
									$OldTitle=$Item->Title;
									$OldSubTitle='';
									$IsTitle=true;
								}
								if($OldSubTitle!=$Item->SubTitle and $Item->SubTitle) {
									$LinSub=$Item->SubTitle;
									$OldSubTitle=$Item->SubTitle;
									$IsTitle=false;
								}
								if($Item->Text) {
									$LinTxt=$Item->Text;
									$IsTitle=false;
								}
								$OldStart=$Item->Start;
								$OldEnd=$Item->Duration;
								$ret[$nDay][]=array($Item->Day, $LinTim, $LinTit, $LinSub, $LinTxt, $ActiveSession);
							} else {
								// all other kind of texts have a title and the items
								if($OldTitle!=$Item->Title) {
									// Title
									if(!$IsTitle) {
										$LinTit=$Item->Title;
									}
									$OldTitle=$Item->Title;
									$OldSubTitle='';
									$IsTitle=true;
								}
								if($OldSubTitle!=$Item->SubTitle) {
									// SubTitle
									$LinSub=$Item->SubTitle;
									$OldSubTitle=$Item->SubTitle;
									$IsTitle=false;
								}

// 								$timing='';
// 								if($OldStart != $Item->Start or $OldEnd != $Item->Duration) {
// 									$timing=$Item->Start.($Item->Duration ? '-'.addMinutes($Item->Start, $Item->Duration) : '');
// 									$OldStart=$Item->Start;
// 									$OldEnd=$Item->Duration;
// 								}

								$lnk=$Item->Text;
								if(!$Item->Warmup) {
									// not warmup!
									$OldComment='';
									switch($Item->Type) {
										case 'Q':
										case 'E':
											$lnk='';
											if($Item->Comments) {
												$ret[$nDay][]=array($Item->Day, $LinTim, $LinTit, $LinSub, $Item->Comments, $ActiveSession);
											}
											if(count($this->Groups[$Item->Type][$Session])==1) {
												$txt=$Item->Text.$lnk;
											} elseif($Item==@end(end(end(end($this->Groups[$Item->Type][$Session]))))) {
												$txt=get_text('Distance', 'Tournament'). ' '.$Distance.$lnk;
											} else {
												$txt=get_text('Distance', 'Tournament'). ' '.$Distance;
												// more distances defined so format is different...
											}
											$ret[$nDay][]=array($Item->Day, $LinTim, $LinTit, $LinSub, $txt, $ActiveSession);

											$IsTitle=false;
											break;
										case 'I':
										case 'T':
											$lnk=$Item->Text.': '.$Item->Events;
											$ret[$nDay][]=array($Item->Day, $LinTim, $LinTit, $LinSub, $lnk, $ActiveSession);
											$IsTitle=false;
											// 											debug_svela($Item);

											break;
										default:
											debug_svela($Item);
									}

								} else {
									if($Item->Comments) {
										$lnk=$Item->Comments;
									} else {
										switch($Item->Type) {
											case 'I':
											case 'T':
												$lnk=$Item->Text.': '.$Item->Events.' '.'warmup';
												break;
											default:
												$lnk.=' Warmup';
										}
									}
									if($OldComment==$lnk) continue;
									$OldComment=$lnk;
									$ret[$nDay][]=array($Item->Day, $LinTim, $LinTit, $LinSub, $lnk, $ActiveSession);
									$IsTitle=false;
								}
							}
						}
					}
				}
			}
		}

// 		debug_svela($ret);
		$XmlDoc = new DOMDocument('1.0', 'UTF-8');
		$XmlRoot = $XmlDoc->createElement('schedule');
		$XmlDoc->appendChild($XmlRoot);

		foreach($ret as $nDay => $events) {
			$Day = $XmlDoc->createElement('day'.$nDay);
			$XmlRoot->AppendChild($Day);
			$nGroup=0;
			foreach($events as $n=>$Item) {
				if(($n%8)==0) {
					$Group = $XmlDoc->createElement('groupevent'.(++$nGroup));
					$Day->AppendChild($Group);
				}
				$Line = $XmlDoc->createElement('event'.($n%8 + 1));
				$Group->AppendChild($Line);

				$a=$XmlDoc->createElement('day');
				$a->AppendChild($XmlDoc->createCDATASection($Item[0]));
				$Line->AppendChild($a);

				$a=$XmlDoc->createElement('time');
				$a->AppendChild($XmlDoc->createCDATASection($Item[1]));
				$Line->AppendChild($a);

				$a=$XmlDoc->createElement('name');
				$a->AppendChild($XmlDoc->createCDATASection($Item[2]));
				$Line->AppendChild($a);

				$a=$XmlDoc->createElement('sub');
				$a->AppendChild($XmlDoc->createCDATASection($Item[3]));
				$Line->AppendChild($a);

				$a=$XmlDoc->createElement('details');
				$a->AppendChild($XmlDoc->createCDATASection($Item[4]));
				$Line->AppendChild($a);

				$a=$XmlDoc->createElement('on', $Item[5] ? 1: 0 );
				$Line->AppendChild($a);
			}
		}
		return $XmlDoc;
	}
}

function AddMinutes($Time, $Minutes) {
	if($Minutes==0) return $Time;
	$newtime=(substr($Time, 0, 2)*60)+substr($Time, 3, 2)+$Minutes;
	return sprintf('%02d:%02d', intval($newtime/60), $newtime%60);
}