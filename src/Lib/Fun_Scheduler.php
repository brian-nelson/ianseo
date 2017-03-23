<?php
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Lib/Fun_DateTime.inc.php');
require_once('Common/Lib/CommonLib.php');

Class Scheduler {
	var $SingleDay='';
	var $FromDay='';
	var $TourId=0;
	var $ROOT_DIR='/';
	var $DayByDay=false;
	var $Finalists=false;
	var $Ranking=false;
	var $SesType='';
	var $SesFilter='';
	var $DateFormat= '%W, %M %D %Y';
	var $TimeFormat='%l:%i %p';
	var $Ods;
	var $SchedVersion='';
	var $SchedVersionDate='';
	var $SchedVersionNote='';
	var $SchedVersionText='';
	var $FopVersion='';
	var $FopVersionDate='';
	var $FopVersionNote='';
	var $FopVersionText='';
	var $LastUpdate='';
	var $Groups=array();
	var $ActiveSessions=array();
	var $Schedule=array();
	var $FopLocations=array();
	var $PageBreaks=array();

	function __destruct() {
		DefineForcePrintouts($this->TourId, true);
	}

	function __construct($TourId=0) {
		$this->TourId=($TourId ? $TourId : $_SESSION['TourId']);
		if(!empty($_SESSION['ActiveSession'])) {
			$this->ActiveSessions=$_SESSION['ActiveSession'];
		} elseif($tmp=Get_Tournament_Option('ActiveSession', '', $this->TourId)) {
			$this->ActiveSessions=$tmp;
		}

		DefineForcePrintouts($this->TourId);

		$q=safe_r_sql("select concat(DvMajVersion, '.', DvMinVersion) as DocVersion, DvPrintDateTime,
				date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate,
				DvPrintDateTime,
				DvNotes as DocNotes
				from DocumentVersions
				where DvTournament='{$this->TourId}' and DvFile='SCHED'");
		if($r=safe_fetch($q)) {
			$this->SchedVersion=$r->DocVersion;
			$this->SchedVersionDate=$r->DocVersionDate;
			$this->SchedVersionNote=$r->DocNotes;
			$this->SchedVersionText=trim('Vers. '.$r->DocVersion . " ($r->DocVersionDate) $r->DocNotes");
			$this->LastUpdate=$r->DvPrintDateTime;
		}
		$q=safe_r_sql("select concat(DvMajVersion, '.', DvMinVersion) as DocVersion, DvPrintDateTime,
				date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate,
				DvNotes as DocNotes
				from DocumentVersions
				where DvTournament='{$this->TourId}' and DvFile='FOP'");
		if($r=safe_fetch($q)) {
			$this->FopVersion=$r->DocVersion;
			$this->FopVersionDate=$r->DocVersionDate;
			$this->FopVersionNote=$r->DocNotes;
			$this->FopVersionText=trim('Vers. '.$r->DocVersion . " ($r->DocVersionDate) $r->DocNotes");
			$this->LastUpdate=max($this->LastUpdate, $r->DvPrintDateTime);
		}

		/// get max scheduling... based on entries, qualification, finals, teams, eliminations
		$sql="(select max(greatest(EnTimestamp, QuTimestamp)) LastDate from Qualifications inner join Entries on EnId=QuId where EnTournament={$this->TourId})
			union
			(select max(ElDateTime) from Eliminations where ElTournament={$this->TourId})
			union
			(select max(FinDateTime) from Finals where FinTournament={$this->TourId})
			union
			(select max(TfDateTime) from TeamFinals where TfTournament={$this->TourId})
			order by LastDate desc";
		$q=safe_r_SQL(($sql));
		if($r=safe_fetch($q)) $this->LastUpdate=$r->LastDate;

		$this->FopLocations=Get_Tournament_Option('FopLocations', array());
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
		$tmp->DistanceName=(!empty($r->{'TD'.$r->Distance})? $r->{'TD'.$r->Distance} : get_text('Distance', 'Tournament'). ' '.$r->Distance);
		$tmp->Order=$r->OrderPhase;
		$tmp->Shift=$Shift;
		$tmp->SO=$r->EvShootOff;
		$tmp->grPos=$r->grPos;

		switch($r->Type) {
			case 'Q':
			case 'E':
				$tmp->SubTitle=$r->SesName ? $r->SesName : get_text('Session'). ' ' . $r->Session;
				if($r->Options and $Warmup) {
					$tmp->Text=$r->Options;
				} else {
					$tmp->Text=$r->SesName ? $r->SesName : get_text('Session'). ' ' . $r->Session;
				}
				$tmp->Target=$r->Target;
				break;
			case 'Z':
				$tmp->Title=$r->SesName;
				$tmp->SubTitle=$r->Options;
				$tmp->Text=$r->Events;
				$tmp->Target=$r->Target;
				break;
			default:
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
				// check if there is a location
				if($r->Target and empty($_REQUEST['NoLocations']) and !empty($_SESSION['FopLocations'])) {
					foreach($_SESSION['FopLocations'] as $n) {
						if($r->Target>=$n->Tg1 and $r->Target<=$n->Tg2) {
							$tmp->Events.= " ($n->Loc)";
						}
					}
				}
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


		if(empty($this->Schedule[$tmp->Day][$tmp->Start][$r->Session][$r->Distance])) {
			$this->Schedule[$tmp->Day][$tmp->Start][$r->Session][$r->Distance]=array();
		}
		if(!in_array($tmp, $this->Schedule[$tmp->Day][$tmp->Start][$r->Session][$r->Distance])) {
			array_push($this->Schedule[$tmp->Day][$tmp->Start][$r->Session][$r->Distance], $tmp);
		}
		$this->Groups[$tmp->Type][$r->Session][$r->Distance][$tmp->Day][$tmp->Start][]=$tmp;
	}

	function GetSchedule() {
		$SQL=array();
		// First gets the Texts: titles and description for a given time always go before everything else
		// getting them first to seed the array!
		if(!$this->SesType or strstr($this->SesType, 'Z')) {
			$SQL[]="select distinct
				'' EvShootOff,
				'' grPos,
					SchTargets Target,
					'Z' Type,
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
					SchShift SchDelay,
					'' TD1, '' TD2, '' TD3, '' TD4, '' TD5, '' TD6, '' TD7, '' TD8
				from Scheduler
				where SchTournament=$this->TourId
					and SchDay>0 and SchStart>0
					".($this->SingleDay ? " and SchDay='$this->SingleDay'" : '')."
					".($this->FromDay ? " and SchDay>='$this->FromDay'" : '')."
					";
		}

		// Then gets the qualification rounds
		if(!$this->SesType or strstr($this->SesType, 'Q')) {
			$SQL[]="select distinct
				'' EvShootOff,
				'' grPos,
					DiTargets Target,
					DiType Type,
					DiDay Day,
					DiSession Session,
					DiDistance Distance,
					'' Medal,
					if(DiStart=0, '', date_format(DiStart, '%H:%i')) Start,
					DiDuration Duration,
					if(DiWarmStart=0, '', date_format(DiWarmStart, '%H:%i')) WarmStart,
					DiWarmDuration WarmDuration,
					DiOptions Options,
					SesName,
					'' Events,
					'' Event,
					DiSession OrderPhase,
					DiShift SchDelay,
					TD1, TD2, TD3, TD4, TD5, TD6, TD7, TD8
				from DistanceInformation
				INNER join Session on SesTournament=DiTournament and SesOrder=DiSession and SesType=DiType and SesType='Q'
				left join (select * from TournamentDistances where TdTournament=$this->TourId group by TdTournament having count(*)=1) TD on TdTournament=SesTournament
				where DiTournament=$this->TourId
					and DiDay>0 and (DiStart>0 or DiWarmStart>0)
					".($this->SingleDay ? " and DiDay='$this->SingleDay'" : '')."
					".($this->FromDay ? " and DiDay>='$this->FromDay'" : '')."
					".(strlen($this->SesFilter) ? " and DiSession='$this->SesFilter'" : '')."
				order by DiDay, DiStart, DiWarmStart, DiSession, DiDistance";
		}

		// Then gets the Elimination rounds
		if(!$this->SesType or strstr($this->SesType, 'E')) {
			$SQL[]="select distinct
				'' EvShootOff,
				'' grPos,
					'0' Target,
					'E' Type,
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
					DiShift SchDelay,
					'' TD1, '' TD2, '' TD3, '' TD4, '' TD5, '' TD6, '' TD7, '' TD8
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
			$SQL[]="select distinct
				'' EvShootOff,
				'' grPos,
				'0' Target,
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
				'',
					'' TD1, '' TD2, '' TD3, '' TD4, '' TD5, '' TD6, '' TD7, '' TD8

				from FinWarmup
				inner join Events on FwEvent=EvCode and EvTeamEvent=FwTeamEvent and EvTournament=FwTournament
				where FwTournament=$this->TourId
					and FwMatchTime=0
				group by FwTeamEvent, FwDay, FwTime
				";
		}

		// Then gets the robin rounds
		if(!$this->SesType or strstr($this->SesType, 'R')) {
			$SQL[]="select distinct
				'' EvShootOff,
				'' grPos,
					'0' Target,
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
					0 SchDelay,
					'' TD1, '' TD2, '' TD3, '' TD4, '' TD5, '' TD6, '' TD7, '' TD8
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
			// get all the named sessions
			$SQL[]="select distinct
					'' EvShootOff,
				'' grPos,
					'0' Target,
					'Z' Type,
					date_format(SesDtStart, '%Y-%m-%d') Day,
					'-' Session,
					'-' Distance,
					'' EvMedals,
					if(SesDtStart=0, '', date_format(SesDtStart, '%H:%i')) DiStart,
					0 DiDuration,
					'' DiWarmStart,
					0,
					0,
					SesName,
					'',
					'' Event,
					0,
					0 SchDelay,
					'' TD1, '' TD2, '' TD3, '' TD4, '' TD5, '' TD6, '' TD7, '' TD8
				from Session
				where SesTournament=$this->TourId
					and SesName!=''
					and SesDtStart>0
					".($this->SingleDay ? " and date_format(SesDtStart, '%Y-%m-%d')='$this->SingleDay'" : '')."
					".($this->FromDay ? " and date_format(SesDtStart, '%Y-%m-%d')>='$this->FromDay'" : '')."
				order by SesDtStart";

			$SQL[]="select distinct
				EvShootOff,
				EvFinalFirstPhase=48 or EvFinalFirstPhase = 24 As grPos,
				max(FsTarget*1) as Target,
				if(FsTeamEvent=0, 'I', 'T') Type,
				FsScheduledDate Day,
				GrPhase Session,
				EvFinalFirstPhase Distance,
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
				FsShift SchDelay,
					'' TD1, '' TD2, '' TD3, '' TD4, '' TD5, '' TD6, '' TD7, '' TD8

				from FinSchedule
				inner join Events on FsEvent=EvCode and FsTeamEvent=EvTeamEvent and FsTournament=EvTournament
				inner join Grids on FsMatchNo=GrMatchNo
				left join FinWarmup on FsEvent=FwEvent and FsTeamEvent=FwTeamEvent and FsTournament=FwTournament and FsScheduledDate=FwDay and FsScheduledTime=FwMatchTime
				where FsTournament=$this->TourId
					and FsScheduledDate>0 and (FsScheduledTime>0 or FwTime>0)
					".($this->SingleDay ? " and FsScheduledDate='$this->SingleDay'" : '')."
					".($this->FromDay ? " and FsScheduledDate>='$this->FromDay'" : '')."
				group by FsTeamEvent, FsScheduledDate, FsScheduledTime, GrPhase, FwTime
				";
		}

		$sql='('.implode(') UNION (', $SQL).') order by Day, if(Start>0, if(WarmStart>0, least(Start, WarmStart), Start), WarmStart), Type!=\'Z\', OrderPhase, Distance';

// 		debug_svela($sql);

		$q=safe_r_SQL($sql);

		while($r=safe_fetch($q)) {
			if($r->WarmStart) {
				$this->push($r, true);
			}
			if($r->Start) {
				$this->push($r, false, $r->WarmStart);
			}
		}

		return $this->Schedule;
	}

	/**
	 * @param string $Type
	 * Default value is IS, other values: SET, SHOW
	 * @return string
	 *
	 * Returns the HTML representation of the Schedule
	 */
	function getScheduleHTML($Type='IS', $Title='') {
		$ret=array();
		if($Title) $ret[]='<tr><th colspan="2" class="SchHeadTitle">'.$Title.'</th></tr>';
		foreach($this->GetSchedule() as $Date => $Times) {
			$ret[]='<tr><th colspan="2" class="SchDay">'.formatTextDate($Date, true).'</th></tr>';
			$OldTitle='';
			$OldSubTitle='';
			$OldType='';
			$OldStart='';
			$OldEnd='';
			$IsTitle=false;

			$OldComment='';
			ksort($Times);
			foreach($Times as $Time => $Sessions) {
				$Singles=array();
				foreach($Sessions as $Session => $Distances) {
					foreach($Distances as $Distance => $Items) {
						foreach($Items as $k => $Item) {
							$key=$Item->Day
								.'|'.$Time
								.'|'.$Session
								.'|'.$Distance
								.'|'.$Item->Order;
							if($Item->Comments) {
								$SingleKey="{$Item->Duration}-{$Item->Title}-{$Item->SubTitle}-{$Item->Comments}";
								if(in_array($SingleKey, $Singles)) continue;
								$Singles[]=$SingleKey;
							}

							$ActiveSession=in_array($key, $this->ActiveSessions);

							$timing='';

							if($Item->Type=='Z') {
								// free text
								$timing=$Item->Start.($Item->Duration ? '-'.addMinutes($Item->Start, $Item->Duration) : '');

								if($OldTitle!=$Item->Title and $Item->Title) {
									if(!$IsTitle) {
										$tmp='<tr name="'.$key.'"'.(($ActiveSession and !$Item->SubTitle and !$Item->Text) ? ' class="active"' : '').'><td>';
										if(!$Item->SubTitle and !$Item->Text) {
											// rimosso il 12-04-2015 per espresso parere di matteo ;)
// 											$tmp.=$timing . ($Item->Shift && $timing ? ($Type=='IS' ? '<span class="SchDelay">' : '') . '&nbsp;+' . $Item->Shift . ($Type=='IS' ? '</span>' : ''): "");
// 											$timing='';
										}
										$txt=$Item->Title;
										if($Type=='SET') {
											$txt='<a href="?Activate='.$key.'">'.strip_tags($txt).'</a>';
										}

										$tmp.='</td><td class="SchTitle">'.$txt.'</td></tr>';
										$ret[]=$tmp;
									}
									$OldTitle=$Item->Title;
									$OldSubTitle='';
									$IsTitle=true;
								}
								if($OldSubTitle!=$Item->SubTitle and $Item->SubTitle) {
									$tmp='<tr name="'.$key.'"'.(($ActiveSession and !$Item->Text) ? ' class="active"' : '').'><td>';
									if(!$Item->Text) {
										$tmp.=$timing . ($Item->Shift && $timing ? ($Type=='IS' ? '<span class="SchDelay">' : '') . '&nbsp;+' . $Item->Shift . ($Type=='IS' ? '</span>' : ''): "");
										$timing='';
									}
									$txt=$Item->SubTitle;
									if($Type=='SET') {
										$txt='<a href="?Activate='.$key.'">'.strip_tags($txt).'</a>';
									}
									$tmp.='</td><td class="SchSubTitle">'.$txt.'</td></tr>';
									$ret[]=$tmp;
									$OldSubTitle=$Item->SubTitle;
									$IsTitle=false;
								}
								if($Item->Text) {
									$txt=$Item->Text;
									if($Type=='SET') {
										$txt='<a href="?Activate='.$key.'">'.strip_tags($txt).'</a>';
									}
									$tmp='<tr name="'.$key.'"'.($ActiveSession ? ' class="active"' : '').'><td>';
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
											$t=safe_r_SQL("select distinct EcCode, EvTeamEvent from Entries
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
													$txt='<a href="?Activate='.urlencode($key).'">'.strip_tags($txt).'</a>';
												}
												$ret[]='<tr name="'.$key.'"'.($ActiveSession ? ' class="active"' : '').'><td>'
													. $timing . ($Item->Shift && $timing ? ($Type=='IS' ? '<span class="SchDelay">' : '') . '&nbsp;+' . $Item->Shift . ($Type=='IS' ? '</span>' : ''): "")
													.'</td><td class="SchWarmup">'.$txt.'</td></tr>';
												$timing='';
											}
											if($Type=='IS') {
												while($u=safe_fetch($t)) {
													$Link[$u->EvTeamEvent][]='Event[]='.$u->EcCode;
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
												$txt=$Item->DistanceName.$lnk;
											} else {
												$txt=$Item->DistanceName;
												// more distances defined so format is different...
											}

											if($Type=='SET') {
												$txt='<a href="?Activate='.urlencode($key).'">'.strip_tags($txt).'</a>';
											}
											$ret[]='<tr name="'.$key.'"'.($ActiveSession ? ' class="active"' : '').'><td>'
												. $timing . ($Item->Shift && $timing ? ($Type=='IS' ? '<span class="SchDelay">' : '') . '&nbsp;+' . $Item->Shift . ($Type=='IS' ? '</span>' : ''): "")
												.'</td><td class="SchItem">'.$txt.'</td></tr>';
											$IsTitle=false;
											break;
										case 'I':
										case 'T':
											$lnk=$Item->Text.': '.$Item->Events;
											if($this->Finalists or $Type=='SET') { // && $Item->Session<=1) {
												// Bronze or Gold Finals
												if($Item->Type=='I') {
													$SQL="select concat(upper(e1.EnFirstname), ' ', e1.EnName, ' (', c1.CoCode, ')') LeftSide,
													concat('(', c2.CoCode, ') ', upper(e2.EnFirstname), ' ', e2.EnName) RightSide
													from Finals tf1
													inner join Finals tf2 on tf1.FinEvent=tf2.FinEvent and tf1.FinTournament=tf2.FinTournament and tf2.FinMatchNo=tf1.FinMatchNo+1 and tf2.FinMatchNo%2=1
													inner join FinSchedule fs1 on tf1.FinTournament=fs1.FsTournament and tf1.FinEvent=fs1.FsEvent and tf1.FinMatchNo=fs1.FsMatchNo and fs1.FsTeamEvent=0 and fs1.FsScheduledDate='$Date' and fs1.FsScheduledTime='$Time'
													inner join FinSchedule fs2 on tf2.FinTournament=fs2.FsTournament and tf2.FinEvent=fs2.FsEvent and tf2.FinMatchNo=fs2.FsMatchNo and fs2.FsTeamEvent=0 and fs2.FsScheduledDate='$Date' and fs2.FsScheduledTime='$Time'
													inner join Entries e1 on e1.EnId=tf1.FinAthlete and tf1.FinEvent IN ('$Item->Event')
													inner join Entries e2 on e2.EnId=tf2.FinAthlete and tf2.FinEvent IN ('$Item->Event')
													inner join Countries c1 on e1.EnCountry=c1.CoId and c1.CoTournament=$this->TourId
													inner join Countries c2 on e2.EnCountry=c2.CoId and c2.CoTournament=$this->TourId
													inner join Grids on tf1.FinMatchNo=GrMatchNo and GrPhase=$Item->Session
													where tf1.FinTournament=$this->TourId ";
												} else {
													$SQL="select concat(c1.CoName, ' (', c1.CoCode, ')') LeftSide,
													concat('(', c2.CoCode, ') ', c2.CoName) RightSide
													from TeamFinals tf1
													inner join TeamFinals tf2 on tf1.TfEvent=tf2.TfEvent and tf1.TfTournament=tf2.TfTournament and tf2.TfMatchNo=tf1.TfMatchNo+1 and tf2.TfMatchNo%2=1
													inner join FinSchedule fs1 on tf1.TfTournament=fs1.FsTournament and tf1.TfEvent=fs1.FsEvent and tf1.TfMatchNo=fs1.FsMatchNo and fs1.FsTeamEvent=1 and fs1.FsScheduledDate='$Date' and fs1.FsScheduledTime='$Time'
													inner join FinSchedule fs2 on tf2.TfTournament=fs2.FsTournament and tf2.TfEvent=fs2.FsEvent and tf2.TfMatchNo=fs2.FsMatchNo and fs2.FsTeamEvent=1 and fs2.FsScheduledDate='$Date' and fs2.FsScheduledTime='$Time'
													inner join Countries c1 on c1.CoId=tf1.TfTeam and tf1.TfEvent IN ('$Item->Event')
													inner join Countries c2 on c2.CoId=tf2.TfTeam and tf2.TfEvent IN ('$Item->Event')
													inner join Grids on tf1.TfMatchNo=GrMatchNo and GrPhase=$Item->Session
													where tf1.TfTournament=$this->TourId";
												}
												$q=safe_r_SQL($SQL);
												if(safe_num_rows($q)==1 and $r=safe_fetch($q) and trim($r->LeftSide) and trim($r->RightSide)) {
													$lnk.= '<br>' . $r->LeftSide.' - '.$r->RightSide;
												}
											}
											if($Type=='SET') {
												$lnk='<a href="?Activate='.urlencode($key).'">'.strip_tags(str_replace('<br>', ': ', $lnk)).'</a>';
											} elseif($Type=='IS') {
												$lnk='<a href="'.$this->ROOT_DIR.'Finals/session.php?Session='.urlencode(($Item->Type=='T' ? 1 : 0)."$Item->Day $Item->Start:00").'">'.$lnk.'</a>';
											}
											$ret[]='<tr name="'.$key.'"'.($ActiveSession ? ' class="active"' : '').'><td>'
												. $timing . ($Item->Shift && $timing ? ($Type=='IS' ? '<span class="SchDelay">' : '') . '&nbsp;+' . $Item->Shift . ($Type=='IS' ? '</span>' : ''): "")
												.'</td><td class="SchItem">'.$lnk.'</td></tr>';
											$IsTitle=false;
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
												$lnk='<a href="?Activate='.urlencode($key).'">'.strip_tags($lnk).'</a>';
											} elseif($Type=='IS') {
												$lnk='<a href="'.$this->ROOT_DIR.'Rounds/?Session='.urlencode("$Item->Day $Item->Start:00").'">'.$lnk.'</a>';
											}
											$ret[]='<tr name="'.$key.'"'.($ActiveSession ? ' class="active"' : '').'><td>'
												. $timing . ($Item->Shift && $timing ? ($Type=='IS' ? '<span class="SchDelay">' : '') . '&nbsp;+' . $Item->Shift . ($Type=='IS' ? '</span>' : ''): "")
												.'</td><td class="SchItem">'.$lnk.'</td></tr>';
											$IsTitle=false;
											break;
										default:
// 											debug_svela($Item);
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
										$lnk='<a href="?Activate='.urlencode($key).'">'.strip_tags($lnk).'</a>';
									}
									$ret[]='<tr name="'.$key.'"'.($ActiveSession ? ' class="active"' : '').'><td>'
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
			return '<table width="100%" class="SchTable">'.implode('', $ret).'</table>';
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
		$DelayWidth=10;
		$TimingWidth=20;
		$DurationWidth=10;
		$CellHeight=5;
		if($this->DayByDay) {
			$FontAdjust= 2;
			$DelayWidth=20;
			$TimingWidth=35;
			$DurationWidth=20;
			$StartX+=10;
			$CellHeight=8;
		}
		$TimeColumns=$TimingWidth+$DurationWidth+$DelayWidth;
		$descrSize=$pdf->getPageWidth() - 20-$TimeColumns;
		$RepeatTile='';

		$pdf->ln();
		$pdf->SetFont($pdf->FontStd, 'B', 20*$FontAdjust);
		$pdf->Cell(0, 0, 'Schedule', '', 1, 'C' );
		$pdf->SetFont($pdf->FontStd, '', 8*$FontAdjust);

		if($this->SchedVersion) {
			$pdf->dy(-4.5*$FontAdjust);
			$pdf->Cell(0, 0, $this->SchedVersionText, '', 1, 'R' );
			$pdf->Version=$this->SchedVersion;
		}
		$pdf->dy(3*$FontAdjust);

		foreach($this->GetSchedule() as $Date => $Times) {
			if(!$Start and ($this->DayByDay or !$pdf->SamePage($CellHeight*4))) {
				$pdf->AddPage();
			} elseif(!$Start) {
				$pdf->dy(2*$FontAdjust);
			}
			$Start=false;

			if(in_array($Date, $this->PageBreaks)) {
				$pdf->AddPage();
			}

			// DAY
			$pdf->SetFont($pdf->FontStd,'B',8*$FontAdjust);
			$pdf->Cell(0, $CellHeight, formatTextDate($Date, true) ,0,1,'L',1);
			$pdf->SetFont($pdf->FontStd,'');

			$OldTitle='';
			$OldSubTitle='';
			$OldType='';
			$OldStart='';
			$OldEnd='';
			$IsTitle=false;
			$FirstTitle=true;

			$OldComment='';
			ksort($Times);
			foreach($Times as $Time => $Sessions) {
				$Singles=array();
				foreach($Sessions as $Session => $Distances) {
					foreach($Distances as $Distance => $Items) {
						foreach($Items as $k => $Item) {

							if($Item->Comments) {
								$SingleKey="{$Item->Duration}-{$Item->Title}-{$Item->SubTitle}-{$Item->Comments}";
								if(in_array($SingleKey, $Singles)) continue;
								$Singles[]=$SingleKey;
							}

							if(!$pdf->SamePage($CellHeight)) {
								$pdf->AddPage();
								// Day...
								$pdf->SetFont('', 'B');
								$pdf->Cell(0, $CellHeight, formatTextDate($Date, true) . '    ('.get_text('Continue').')',0,1,'L',1);
								$FirstTitle=true;

								// maybe the session title?
								if($Item->Type!='Z' and $OldTitle==$Item->Title and $RepeatTitle) {
									$pdf->SetX($StartX+$TimeColumns);
									$pdf->Cell($descrSize, $CellHeight, $RepeatTitle . ", " . formatWeekDayLong($Date) . '    ('.get_text('Continue').')',0,1,'L',0);
								}
								$pdf->SetFont('', '');
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
										if(!$FirstTitle) $pdf->ln(2);
										$pdf->SetX($StartX+$TimeColumns);
										$pdf->SetFont('', 'B');
										$pdf->Cell($descrSize, $CellHeight, strip_tags($Item->Title), 0, 1, 'L', 0);
										$pdf->SetFont('', '');
										$RepeatTitle=$Item->Title;
									}
									$OldTitle=$Item->Title;
									$OldSubTitle='';
									$IsTitle=true;
								}
								if($OldSubTitle!=$Item->SubTitle and $Item->SubTitle) {
									if(!$Item->Text) {
										$pdf->SetX($StartX);
										if($Item->Shift and $timing) {
											$pdf->SetX($StartX-$DelayWidth);
											$pdf->Cell($DelayWidth, $CellHeight, $timingDelayed, 0, 0);
											$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
										} else {
											$pdf->SetX($StartX+$DelayWidth);
											$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
										}
										if($timing and $Item->Duration) {
											$pdf->SetFont('', 'I');
											$pdf->setColor('text', 75);
											$pdf->Cell($DurationWidth, $CellHeight, sprintf('%02d:%02d', $Item->Duration/60, $Item->Duration%60), 0, 0, 'R', 0);
											$pdf->SetFont('', '');
											$pdf->setColor('text', 0);
										}
										$timing='';
									}
									$pdf->SetX($StartX+$TimeColumns);
									$pdf->SetFont('', 'BI');
									$pdf->Cell($descrSize, $CellHeight, strip_tags($Item->SubTitle), 0, 1, 'L', 0);
									$pdf->SetFont('', '');
									$OldSubTitle=$Item->SubTitle;
									$IsTitle=false;
								}
								if($Item->Text) {
									$pdf->SetX($StartX);
									if($Item->Shift and $timing) {
										$pdf->Cell($DelayWidth, $CellHeight, $timingDelayed, 0, 0);
										$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
										$pdf->Line($StartX, $y=$pdf->GetY()+($CellHeight/2), $StartX+$TimingWidth-$FontAdjust, $y);
									} else {
										$pdf->SetX($StartX+$DelayWidth);
										$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
									}
									if($timing and $Item->Duration) {
										$pdf->SetFont('', 'I');
										$pdf->setColor('text', 75);
										$pdf->Cell($DurationWidth, $CellHeight, sprintf('%02d:%02d', $Item->Duration/60, $Item->Duration%60), 0, 0, 'R', 0);
										$pdf->SetFont('', '');
										$pdf->setColor('text', 0);
									}
									$pdf->SetX($StartX+$TimeColumns);
									$pdf->Cell($descrSize, $CellHeight, strip_tags($Item->Text), 0, 1, 'L', 0);
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
										if(!$FirstTitle) $pdf->ln(2);
										$pdf->SetX($StartX+$TimeColumns);
										$pdf->SetFont('', 'B');
										$pdf->Cell($descrSize, $CellHeight, $Item->Title, 0, 1, 'L', 0);
										$pdf->SetFont('', '');
										$RepeatTitle=$Item->Title;
									}
									$OldTitle=$Item->Title;
									$IsTitle=true;
									$OldSubTitle='';
								}
								if($OldSubTitle!=$Item->SubTitle) {
									// SubTitle
									$pdf->SetX($StartX+$TimeColumns);
									$pdf->SetFont('', 'BI');
									$pdf->Cell($descrSize, $CellHeight, $Item->SubTitle, 0, 1, 'L', 0);
									$pdf->SetFont('', '');
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

								$lnk=strip_tags($Item->Text);
								if(!$Item->Warmup) {
									// not warmup!
									$OldComment='';
									switch($Item->Type) {
										case 'Q':
										case 'E':
											$t=safe_r_SQL("select distinct EcCode, EvTeamEvent from Entries
												INNER JOIN Qualifications on QuId=EnId and QuSession=$Item->Session
												INNER JOIN EventClass ON EcClass=EnClass AND EcDivision=EnDivision AND EcTournament=EnTournament
												INNER JOIN Events on EvCode=EcCode AND EvTeamEvent=IF(EcTeamEvent!=0, 1,0) AND EvTournament=EcTournament
												where EnTournament=$this->TourId
												order by EvTeamEvent, EvProgr");
											$Link=array();
											$lnk='';
											if($Item->Comments) {
												if($Item->Shift and $timing) {
													$pdf->SetX($StartX);
													$pdf->Cell($DelayWidth, $CellHeight, $timingDelayed, 0, 0);
													$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
												} else {
													$pdf->SetX($StartX+$DelayWidth);
													$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
												}
												if($timing and $Item->Duration) {
													$pdf->SetFont('', 'I');
													$pdf->setColor('text', 75);
													$pdf->Cell($DurationWidth, $CellHeight, sprintf('%02d:%02d', $Item->Duration/60, $Item->Duration%60), 0, 0, 'R', 0);
													$pdf->SetFont('', '');
													$pdf->setColor('text', 0);
												}
												$pdf->SetX($StartX+$TimeColumns);
												$pdf->SetFont('', 'I');
												$pdf->Cell($descrSize, $CellHeight, $Item->Comments, 0, 1, 'L', 0);
												$pdf->SetFont('', '');
												$timing='';
											}

											if(count($this->Groups[$Item->Type][$Session])==1) {
												$txt=$Item->Text.$lnk;
											} elseif($Item==@end(end(end(end($this->Groups[$Item->Type][$Session]))))) {
												$txt=$Item->DistanceName.$lnk;
											} else {
												$txt=$Item->DistanceName;
												// more distances defined so format is different...
											}

											if($Item->Shift and $timing) {
												$pdf->SetX($StartX);
												$pdf->Cell($DelayWidth, $CellHeight, $timingDelayed, 0, 0);
												$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
											} else {
												$pdf->SetX($StartX+$DelayWidth);
												$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
											}
											if($timing and $Item->Duration) {
												$pdf->SetFont('', 'I');
												$pdf->setColor('text', 75);
												$pdf->Cell($DurationWidth, $CellHeight, sprintf('%02d:%02d', $Item->Duration/60, $Item->Duration%60), 0, 0, 'R');
												$pdf->SetFont('', '');
												$pdf->setColor('text', 0);
											}
											$pdf->SetX($StartX+$TimeColumns);
											$pdf->Cell($descrSize, $CellHeight, $txt, 0, 1, 'L', 0);
											$IsTitle=false;
											break;
										case 'I':
										case 'T':
											$lnk=$Item->Text.': '.$Item->Events;
											if($Item->Shift and $timing) {
												$pdf->SetX($StartX);
												$pdf->Cell($DelayWidth, $CellHeight, $timingDelayed, 0, 0);
												$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
											} else {
												$pdf->SetX($StartX+$DelayWidth);
												$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
											}
											if($timing and $Item->Duration) {
												$pdf->SetFont('', 'I');
												$pdf->setColor('text', 75);
												$pdf->Cell($DurationWidth, $CellHeight, sprintf('%02d:%02d', $Item->Duration/60, $Item->Duration%60), 0, 0, 'R');
												$pdf->SetFont('', '');
												$pdf->setColor('text', 0);
											}
											$pdf->SetX($StartX+$TimeColumns);
											$IsTitle=false;
											if($this->Finalists) { // && $Item->Session<=1) {
												$SQL='';
												// Bronze or Gold Finals
												if($Item->Type=='I') {
													if($Item->SO) {
														// SO are resolved so we can extract the people
														$SQL="select ind1.IndRank LeftRank, ind2.IndRank RightRank, concat(upper(e1.EnFirstname), ' ', e1.EnName, ' (', c1.CoCode, ')') LeftSide,
																concat('(', c2.CoCode, ') ', upper(e2.EnFirstname), ' ', e2.EnName) RightSide
															from Finals tf1
															inner join Finals tf2 on tf1.FinEvent=tf2.FinEvent and tf1.FinTournament=tf2.FinTournament and tf2.FinMatchNo=tf1.FinMatchNo+1 and tf2.FinMatchNo%2=1
															inner join FinSchedule fs1 on tf1.FinTournament=fs1.FsTournament and tf1.FinEvent=fs1.FsEvent and tf1.FinMatchNo=fs1.FsMatchNo and fs1.FsTeamEvent=0 and fs1.FsScheduledDate='$Date' and fs1.FsScheduledTime='$Time'
															inner join FinSchedule fs2 on tf2.FinTournament=fs2.FsTournament and tf2.FinEvent=fs2.FsEvent and tf2.FinMatchNo=fs2.FsMatchNo and fs2.FsTeamEvent=0 and fs2.FsScheduledDate='$Date' and fs2.FsScheduledTime='$Time'
															inner join Entries e1 on e1.EnId=tf1.FinAthlete and tf1.FinEvent IN ('$Item->Event')
															inner join Entries e2 on e2.EnId=tf2.FinAthlete and tf2.FinEvent IN ('$Item->Event')
															inner join Individuals ind1 on e1.EnId=ind1.IndId and tf1.FinEvent=ind1.IndEvent
															inner join Individuals ind2 on e2.EnId=ind2.IndId and tf2.FinEvent=ind2.IndEvent
															inner join Countries c1 on e1.EnCountry=c1.CoId and c1.CoTournament=$this->TourId
															inner join Countries c2 on e2.EnCountry=c2.CoId and c2.CoTournament=$this->TourId
															inner join Events on EvTournament=tf1.FinTournament and EvTeamEvent=0 and EvCode=tf1.FinEvent
															inner join Grids on tf1.FinMatchNo=GrMatchNo and GrPhase=$Item->Session
															where tf1.FinTournament=$this->TourId";
													} elseif($this->Ranking) {
														// we can only catch the supposed positions of the opponents
														$Fld=($Item->grPos ? 'GrPosition2' : 'GrPosition');
														$SQL="select Gr1.{$Fld} LeftRank, Gr2.{$Fld} RightRank, '' LeftSide, '' RightSide
															from Finals tf1
															inner join Finals tf2 on tf1.FinEvent=tf2.FinEvent and tf1.FinTournament=tf2.FinTournament and tf2.FinMatchNo=tf1.FinMatchNo+1 and tf2.FinMatchNo%2=1
															inner join FinSchedule fs1 on tf1.FinTournament=fs1.FsTournament and tf1.FinEvent=fs1.FsEvent and tf1.FinMatchNo=fs1.FsMatchNo and fs1.FsTeamEvent=0 and fs1.FsScheduledDate='$Date' and fs1.FsScheduledTime='$Time'
															inner join FinSchedule fs2 on tf2.FinTournament=fs2.FsTournament and tf2.FinEvent=fs2.FsEvent and tf2.FinMatchNo=fs2.FsMatchNo and fs2.FsTeamEvent=0 and fs2.FsScheduledDate='$Date' and fs2.FsScheduledTime='$Time'
															inner join Events on EvTournament=tf1.FinTournament and EvTeamEvent=0 and EvCode=tf1.FinEvent
															inner join Grids Gr1 on tf1.FinMatchNo=Gr1.GrMatchNo and Gr1.GrPhase=$Item->Session
															inner join Grids Gr2 on tf2.FinMatchNo=Gr2.GrMatchNo and Gr2.GrPhase=$Item->Session
															where tf1.FinTournament=$this->TourId";
													}
												} else {
													if($Item->SO) {
														// SO are resolved so we can extract the people
														$SQL="select ind1.TeRank LeftRank, ind2.TeRank RightRank, concat(c1.CoName, ' (', c1.CoCode, ')') LeftSide,
																concat('(', c2.CoCode, ') ', c2.CoName) RightSide
															from TeamFinals tf1
															inner join TeamFinals tf2 on tf1.TfEvent=tf2.TfEvent and tf1.TfTournament=tf2.TfTournament and tf2.TfMatchNo=tf1.TfMatchNo+1 and tf2.TfMatchNo%2=1
															inner join FinSchedule fs1 on tf1.TfTournament=fs1.FsTournament and tf1.TfEvent=fs1.FsEvent and tf1.TfMatchNo=fs1.FsMatchNo and fs1.FsTeamEvent=1 and fs1.FsScheduledDate='$Date' and fs1.FsScheduledTime='$Time'
															inner join FinSchedule fs2 on tf2.TfTournament=fs2.FsTournament and tf2.TfEvent=fs2.FsEvent and tf2.TfMatchNo=fs2.FsMatchNo and fs2.FsTeamEvent=1 and fs2.FsScheduledDate='$Date' and fs2.FsScheduledTime='$Time'
															inner join Countries c1 on c1.CoId=tf1.TfTeam and tf1.TfEvent IN ('$Item->Event')
															inner join Countries c2 on c2.CoId=tf2.TfTeam and tf2.TfEvent IN ('$Item->Event')
															inner join Teams ind1 on c1.CoId=ind1.TeCoId and tf1.TfEvent=ind1.TeEvent and tf1.TfSubTeam=ind1.TeSubTeam and ind1.TeFinEvent=1
															inner join Teams ind2 on c2.CoId=ind2.TeCoId and tf2.TfEvent=ind2.TeEvent and tf2.TfSubTeam=ind2.TeSubTeam and ind2.TeFinEvent=1
															inner join Grids Gr1 on tf1.TfMatchNo=Gr1.GrMatchNo and Gr1.GrPhase=$Item->Session
															inner join Grids Gr2 on tf2.TfMatchNo=Gr2.GrMatchNo and Gr2.GrPhase=$Item->Session
															where tf1.TfTournament=$this->TourId";
													} elseif($this->Ranking) {
														// we can only catch the supposed positions of the opponents
														$Fld=($Item->grPos ? 'GrPosition2' : 'GrPosition');
														$SQL="select Gr1.{$Fld} LeftRank, Gr2.{$Fld} RightRank, '' LeftSide, '' RightSide
															from TeamFinals tf1
															inner join TeamFinals tf2 on tf1.TfEvent=tf2.TfEvent and tf1.TfTournament=tf2.TfTournament and tf2.TfMatchNo=tf1.TfMatchNo+1 and tf2.TfMatchNo%2=1
															inner join FinSchedule fs1 on tf1.TfTournament=fs1.FsTournament and tf1.TfEvent=fs1.FsEvent and tf1.TfMatchNo=fs1.FsMatchNo and fs1.FsTeamEvent=1 and fs1.FsScheduledDate='$Date' and fs1.FsScheduledTime='$Time'
															inner join FinSchedule fs2 on tf2.TfTournament=fs2.FsTournament and tf2.TfEvent=fs2.FsEvent and tf2.TfMatchNo=fs2.FsMatchNo and fs2.FsTeamEvent=1 and fs2.FsScheduledDate='$Date' and fs2.FsScheduledTime='$Time'
															inner join Grids Gr1 on tf1.TfMatchNo=Gr1.GrMatchNo and Gr1.GrPhase=$Item->Session
															inner join Grids Gr2 on tf2.TfMatchNo=Gr2.GrMatchNo and Gr2.GrPhase=$Item->Session
															where tf1.TfTournament=$this->TourId";
													}
												}
												if($SQL and $q=safe_r_SQL($SQL) and safe_num_rows($q)==1 and $r=safe_fetch($q)) {
													if(trim($r->LeftSide) and trim($r->RightSide)) {
														$pdf->Cell($descrSize, $CellHeight, $lnk, 0, 1, 'L', 0);
														$pdf->SetXY($StartX+$TimeColumns, $pdf->getY()-1.5);
														$pdf->Cell($descrSize, $CellHeight, ($this->Ranking ? '#'.$r->LeftRank.' ' : '') . $r->LeftSide.' - '.$r->RightSide . ($this->Ranking ? ' #'.$r->RightRank : ''), 0, 1, 'L', 0);
													} else {
														$lnk.= ' (#'.$r->LeftRank.' - #'.$r->RightRank.')';
														$pdf->Cell($descrSize, $CellHeight, $lnk, 0, 1, 'L', 0);
													}
												} else {
													$pdf->Cell($descrSize, $CellHeight, $lnk, 0, 1, 'L', 0);
												}
											} else {
												$pdf->Cell($descrSize, $CellHeight, $lnk, 0, 1, 'L', 0);
											}
											break;
										case 'R':
											$lnk=$Item->Text.': '.$Item->Events;
											if($Item->Shift and $timing) {
												$pdf->SetX($StartX);
												$pdf->Cell($DelayWidth, $CellHeight, $timingDelayed, 0, 0);
												$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
											} else {
												$pdf->SetX($StartX+$DelayWidth);
												$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
											}
											if($timing and $Item->Duration) {
												$pdf->SetFont('', 'I');
												$pdf->setColor('text', 75);
												$pdf->Cell($DurationWidth, $CellHeight, sprintf('%02d:%02d', $Item->Duration/60, $Item->Duration%60), 0, 0, 'R');
												$pdf->SetFont('', '');
												$pdf->setColor('text', 0);
											}
											$pdf->SetX($StartX+$TimeColumns);
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
// 											debug_svela($Item);
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
									if($Item->Shift and $timing) {
										$pdf->SetX($StartX);
										$pdf->Cell($DelayWidth, $CellHeight, $timingDelayed, 0, 0);
										$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
									} else {
										$pdf->SetX($StartX+$DelayWidth);
										$pdf->Cell($TimingWidth, $CellHeight, $timing, 0, 0);
									}
									if($timing and $Item->Duration) {
										$pdf->SetFont('', 'I');
										$pdf->setColor('text', 75);
										$pdf->Cell($DurationWidth, $CellHeight, sprintf('%02d:%02d', $Item->Duration/60, $Item->Duration%60), 0, 0, 'R');
										$pdf->SetFont('', '');
										$pdf->setColor('text', 0);
									}
									$pdf->SetX($StartX+$TimeColumns);
									$pdf->SetFont('', 'I');
									$pdf->Cell($descrSize, $CellHeight, $lnk, 0, 1, 'L', 0);
									$pdf->SetFont('', '');
									$IsTitle=false;
								}
							}
							$FirstTitle=false;
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
								$ret[$nDay][]=array($Item->Day, $LinTim, $LinTit, $LinSub, $LinTxt, $ActiveSession, '', '', '', '','');
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
												$ret[$nDay][]=array($Item->Day, $LinTim, $LinTit, $LinSub, $Item->Comments, $ActiveSession, '', '', '', '','');
											}
											if(count($this->Groups[$Item->Type][$Session])==1) {
												$txt=$Item->Text.$lnk;
											} elseif($Item==@end(end(end(end($this->Groups[$Item->Type][$Session]))))) {
												$txt=$Item->DistanceName.$lnk;
											} else {
												$txt=$Item->DistanceName;
												// more distances defined so format is different...
											}
											$ret[$nDay][]=array($Item->Day, $LinTim, $LinTit, $LinSub, $txt, $ActiveSession, '', '', '', '','');

											$IsTitle=false;
											break;
										case 'I':
										case 'T':
											$lnk=$Item->Text.': '.$Item->Events;
											$tmp=array($Item->Day, $LinTim, $LinTit, $LinSub, $lnk, $ActiveSession, '', '', '', '','');
											$IsTitle=false;
											if(true or $this->Finalists) { // && $Item->Session<=1) {
												// Bronze or Gold Finals
												if($Item->Type=='I') {
													$SQL="select tf1.FinMatchNo MatchNo, 0 TeamEvent, tf1.FinEvent Event, concat(upper(e1.EnFirstname), ' ', e1.EnName, ' (', c1.CoCode, ')') Opp1, concat(upper(e2.EnFirstname), ' ', e2.EnName, ' (', c2.CoCode, ')') Opp2
													from Finals tf1
													inner join Finals tf2 on tf1.FinEvent=tf2.FinEvent and tf1.FinTournament=tf2.FinTournament and tf2.FinMatchNo=tf1.FinMatchNo+1 and tf2.FinMatchNo%2=1
													inner join FinSchedule fs1 on tf1.FinTournament=fs1.FsTournament and tf1.FinEvent=fs1.FsEvent and tf1.FinMatchNo=fs1.FsMatchNo and fs1.FsTeamEvent=0 and fs1.FsScheduledDate='$Date' and fs1.FsScheduledTime='$Time'
													inner join FinSchedule fs2 on tf2.FinTournament=fs2.FsTournament and tf2.FinEvent=fs2.FsEvent and tf2.FinMatchNo=fs2.FsMatchNo and fs2.FsTeamEvent=0 and fs2.FsScheduledDate='$Date' and fs2.FsScheduledTime='$Time'
													inner join Entries e1 on e1.EnId=tf1.FinAthlete and tf1.FinEvent IN ('$Item->Event')
													inner join Entries e2 on e2.EnId=tf2.FinAthlete and tf2.FinEvent IN ('$Item->Event')
													inner join Countries c1 on e1.EnCountry=c1.CoId and c1.CoTournament=$this->TourId
													inner join Countries c2 on e2.EnCountry=c2.CoId and c2.CoTournament=$this->TourId
													inner join Grids on tf1.FinMatchNo=GrMatchNo and GrPhase=$Item->Session
													where tf1.FinTournament=$this->TourId ";
												} else {
													$SQL="select tf1.TfMatchNo MatchNo, 1 TeamEvent, tf1.TfEvent Event, concat(c1.CoName, ' (', c1.CoCode, ')') Opp1, concat(c2.CoName, '(', c2.CoCode, ') ') Opp2
													from TeamFinals tf1
													inner join TeamFinals tf2 on tf1.TfEvent=tf2.TfEvent and tf1.TfTournament=tf2.TfTournament and tf2.TfMatchNo=tf1.TfMatchNo+1 and tf2.TfMatchNo%2=1
													inner join FinSchedule fs1 on tf1.TfTournament=fs1.FsTournament and tf1.TfEvent=fs1.FsEvent and tf1.TfMatchNo=fs1.FsMatchNo and fs1.FsTeamEvent=1 and fs1.FsScheduledDate='$Date' and fs1.FsScheduledTime='$Time'
													inner join FinSchedule fs2 on tf2.TfTournament=fs2.FsTournament and tf2.TfEvent=fs2.FsEvent and tf2.TfMatchNo=fs2.FsMatchNo and fs2.FsTeamEvent=1 and fs2.FsScheduledDate='$Date' and fs2.FsScheduledTime='$Time'
													inner join Countries c1 on c1.CoId=tf1.TfTeam and tf1.TfEvent IN ('$Item->Event')
													inner join Countries c2 on c2.CoId=tf2.TfTeam and tf2.TfEvent IN ('$Item->Event')
													inner join Grids on tf1.TfMatchNo=GrMatchNo and GrPhase=$Item->Session
													where tf1.TfTournament=$this->TourId";
												}
												$q=safe_r_SQL($SQL);
												if(safe_num_rows($q)==1 and $r=safe_fetch($q) and trim($r->Opp1) and trim($r->Opp2)) {
													$tmp[6] =$r->MatchNo;
													$tmp[7] =$r->TeamEvent;
													$tmp[8] =$r->Event;
													$tmp[9] =$r->Opp1;
													$tmp[10]=$r->Opp2;
												}
											}
											$ret[$nDay][]=$tmp;

											break;
										default:
// 											debug_svela($Item);
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
									$ret[$nDay][]=array($Item->Day, $LinTim, $LinTit, $LinSub, $lnk, $ActiveSession, '','','','','');
									$IsTitle=false;
								}
							}
						}
					}
				}
			}
		}

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

				$a=$XmlDoc->createElement('matchno');
				$a->AppendChild($XmlDoc->createCDATASection($Item[6]));
				$Line->AppendChild($a);

				$a=$XmlDoc->createElement('team');
				$a->AppendChild($XmlDoc->createCDATASection($Item[7]));
				$Line->AppendChild($a);

				$a=$XmlDoc->createElement('event');
				$a->AppendChild($XmlDoc->createCDATASection($Item[8]));
				$Line->AppendChild($a);

				$a=$XmlDoc->createElement('opp1');
				$a->AppendChild($XmlDoc->createCDATASection($Item[9]));
				$Line->AppendChild($a);

				$a=$XmlDoc->createElement('opp2');
				$a->AppendChild($XmlDoc->createCDATASection($Item[10]));
				$Line->AppendChild($a);
			}
		}
		return $XmlDoc;
	}

	function exportODS($filename='SpreadSheet.ods', $type='a') {
		if(!$filename) $filename=$_SESSION['TourCode'].'.ods';
		require_once('Common/ods/ods.php');
		$this->Ods = new ods();
		$this->Ods->setActiveSheet('Schedule');

		$this->Ods->setStyle('DateCell',
				array('style:text-properties' => array('fo:font-weight' => 'bold', 'fo:font-size' => '18pt')),
				array('style:family'=>'table-cell')
				);
		$this->Ods->setStyle('DateRow',
				array('style:table-row-properties' => array('style:row-height' => '24pt', 'style:use-optimal-row-height' => 'true', 'fo:background-color' => '#dddddd')),
				array('style:family'=>'table-row')
				);
		$this->Ods->setStyle('MainTitle', array('style:text-properties' => array('fo:font-weight' => 'bold', 'fo:font-size' => '25pt')));
		$this->Ods->setStyle('MainTitleRow',
				array('style:table-row-properties' => array('style:row-height' => '36pt', 'style:use-optimal-row-height' => 'true')),
				array('style:family'=>'table-row')
				);
		$this->Ods->setStyle('Title', array('style:text-properties' => array('fo:font-weight' => 'bold', 'fo:font-size' => '14pt')));
		$this->Ods->setStyle('TitleRow',
				array('style:table-row-properties' => array('style:row-height' => '21pt', 'style:use-optimal-row-height' => 'true')),
				array('style:family'=>'table-row')
				);
		$this->Ods->setStyle('SubTitle', array('style:text-properties' => array('fo:font-weight' => 'bold', 'fo:font-style' => 'italic', 'fo:font-size' => '12pt')));
		$this->Ods->setStyle('Comments', array('style:text-properties' => array('fo:font-style' => 'italic')));
		$this->Ods->setStyle('Duration', array('style:text-properties' => array('fo:font-style' => 'italic', 'fo:color' => '#666666')));
		//$TXT=array();

		$this->Ods->setStyle('DateFOPCell',
				array('style:text-properties' => array('fo:font-weight' => 'bold', 'fo:font-size' => '12pt')),
				array('style:family'=>'table-cell')
				);
		$this->Ods->setStyle('DateFOPRow',
				array('style:table-row-properties' => array('style:row-height' => '18pt', 'style:use-optimal-row-height' => 'true', 'fo:background-color' => '#dddddd')),
				array('style:family'=>'table-row')
				);
		$this->Ods->setStyle('TitleFOP', array('style:text-properties' => array('fo:font-weight' => 'bold', 'fo:font-size' => '10pt')));
		$this->Ods->setStyle('TitleFOPRow',
				array('style:table-row-properties' => array('style:row-height' => '15pt', 'style:use-optimal-row-height' => 'true')),
				array('style:family'=>'table-row')
				);
		$this->Ods->setStyle('SubTitleFOP', array('style:text-properties' => array('fo:font-weight' => 'bold', 'fo:font-style' => 'italic', 'fo:font-size' => '10pt')));
		$this->Ods->setStyle('Distance', array('style:paragraph-properties' => array('fo:text-align' => 'center')));
		//$TXT=array();

		$this->Ods->setStyle('TimeCol', array('style:table-column-properties' => array('style:column-width' => '1cm')),
				array('style:family'=>'table-column')
				);
		$this->Ods->setStyle('DescCol', array('style:table-column-properties' => array('style:column-width' => '7.5cm')),
				array('style:family'=>'table-column')
				);
		$this->Ods->setStyle('TgtCol', array('style:table-column-properties' => array('style:column-width' => '0.5cm')),
				array('style:family'=>'table-column')
				);

		$this->Ods->setStyle('ColorWarmup', array('style:table-cell-properties' => array('fo:background-color' => sprintf("#%02X%02X%02X", 198, 198, 198))));

		$row=array('Schedule');

		if($this->SchedVersion) {
			$row[]=null;
			$row[]=null;
			$row[]=$this->SchedVersionText;
		}
		$this->Ods->setRowStyle('MainTitleRow');
		$this->Ods->setCellStyle('MainTitle', null, 0);
		$this->Ods->addRow($row);


// 		$this->Ods->currentRow=-1;
		// seed the schedule
		$this->GetSchedule();

		foreach($this->Schedule as $Date => $Times) {
			$this->Ods->currentRow+=2;
			$this->Ods->currentCell=0;

			$this->Ods->setRowStyle('DateRow');
			$this->Ods->setCellStyle('DateCell');
			$this->Ods->addRow(formatTextDate($Date, true));

			$OldTitle='';
			$OldSubTitle='';
			$OldType='';
			$OldStart='';
			$OldEnd='';
			$IsTitle=false;
			$FirstTitle=true;

			$OldComment='';
			ksort($Times);

			foreach($Times as $Time => $Sessions) {
				$Singles=array();
				foreach($Sessions as $Session => $Distances) {
					foreach($Distances as $Distance => $Items) {
						foreach($Items as $k => $Item) {

							if($Item->Comments) {
								$SingleKey="{$Item->Duration}-{$Item->Title}-{$Item->SubTitle}-{$Item->Comments}";
								if(in_array($SingleKey, $Singles)) continue;
								$Singles[]=$SingleKey;
							}

							$timingDelayed='';
							$timing=array('', '', '', '');


							if($Item->Type=='Z') {
								// free text
								$timing[1]=$Item->Start;
								if($Item->Duration) {
									$timing[2] = addMinutes($Item->Start, $Item->Duration);
									$timing[3] = sprintf('%02d:%02d', $Item->Duration/60, $Item->Duration%60);
								}
								if($Item->Shift) $timing[0] = '+'.$Item->Shift;

								if($OldTitle!=$Item->Title and $Item->Title) {
									if(!$IsTitle) {
										$this->Ods->setRowStyle('TitleRow');
										$this->Ods->setCellStyle('Title', null, 4);
										$this->Ods->addRow(array('', '', '', '', htmlspecialchars(strip_tags($Item->Title))));
									}
									$OldTitle=$Item->Title;
									$OldSubTitle='';
									$IsTitle=true;
								}

								if($OldSubTitle!=$Item->SubTitle and $Item->SubTitle) {
									$row=array('', '', '', '', htmlspecialchars(strip_tags($Item->SubTitle)));
									if(!$Item->Text) {
										if($Item->Shift and $timing[1]) {
											$row[0]=$timing[0];
										}
										$row[1]=$timing[1];
										$row[2]=$timing[2];
										$row[3]=$timing[3];
										$timing[3]='';
										$timing[2]='';
										$timing[1]='';
									}
									$this->Ods->setCellStyle('SubTitle', null, 4);
									$this->Ods->setCellStyle('Duration', null, 3);
									$this->Ods->addRow($row);
									$OldSubTitle=$Item->SubTitle;
									$IsTitle=false;
								}
								if($Item->Text) {
									$row=array('', '', '', '', htmlspecialchars(strip_tags($Item->Text)));
									if($Item->Shift and $timing[1]) {
										$row[0]=$timing[0];
									}
									$row[1]=$timing[1];
									$row[2]=$timing[2];
									$row[3]=$timing[3];
									$timing[3]='';
									$timing[2]='';
									$timing[1]='';
									$this->Ods->setCellStyle('Duration', null, 3);
									$this->Ods->addRow($row);
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
										$this->Ods->setRowStyle('TitleRow');
										$this->Ods->setCellStyle('Title', null, 4);
										$this->Ods->addRow(array('', '', '', '', htmlspecialchars(strip_tags($Item->Title))));
									}
									$OldTitle=$Item->Title;
									$IsTitle=true;
									$OldSubTitle='';
								}
								if($OldSubTitle!=$Item->SubTitle) {
									// SubTitle
									$row=array('', '', '', '', htmlspecialchars(strip_tags($Item->SubTitle)));
									$this->Ods->setCellStyle('SubTitle', null, 4);
									$this->Ods->addRow($row);
									$OldSubTitle=$Item->SubTitle;
									$IsTitle=false;
								}

								$timing=array('', '', '', '');
								if($OldStart != $Item->Start or $OldEnd != $Item->Duration) {
									$timing[1]=$Item->Start;
									if($Item->Duration) {
										$timing[2]=addMinutes($Item->Start, $Item->Duration);
										$timing[3] = sprintf('%02d:%02d', $Item->Duration/60, $Item->Duration%60);
									}
									if($Item->Shift) $timing[0] = '+'.$Item->Shift;
									$OldStart=$Item->Start;
									$OldEnd=$Item->Duration;
								}

								$lnk=strip_tags($Item->Text);
								if(!$Item->Warmup) {
									// not warmup!
									$OldComment='';
									switch($Item->Type) {
										case 'Q':
										case 'E':
											$t=safe_r_SQL("select distinct EcCode, EvTeamEvent from Entries
												INNER JOIN Qualifications on QuId=EnId and QuSession=$Item->Session
												INNER JOIN EventClass ON EcClass=EnClass AND EcDivision=EnDivision AND EcTournament=EnTournament
												INNER JOIN Events on EvCode=EcCode AND EvTeamEvent=IF(EcTeamEvent!=0, 1,0) AND EvTournament=EcTournament
												where EnTournament=$this->TourId
												order by EvTeamEvent, EvProgr");
											$Link=array();
											$lnk='';
											if($Item->Comments) {
												$row=array('', '', '', '', htmlspecialchars(strip_tags($Item->Comments)));
												if($Item->Shift and $timing[1]) {
													$row[0]=$timing[0];
												}
												$row[1]=$timing[1];
												$row[2]=$timing[2];
												$row[3]=$timing[3];
												$timing[3]='';
												$timing[2]='';
												$timing[1]='';
												$this->Ods->setCellStyle('Duration', null, 3);
												$this->Ods->setCellStyle('Comments', null, 4);
												$this->Ods->addRow($row);
												$IsTitle=false;
											}

											if(count($this->Groups[$Item->Type][$Session])==1) {
												$txt=$Item->Text.$lnk;
											} elseif($Item==@end(end(end(end($this->Groups[$Item->Type][$Session]))))) {
												$txt=$Item->DistanceName.$lnk;
											} else {
												$txt=$Item->DistanceName;
												// more distances defined so format is different...
											}
											$row=array('', '', '', '', htmlspecialchars(strip_tags($txt)));
											if($Item->Shift and $timing[1]) {
												$row[0]=$timing[0];
											}
											$row[1]=$timing[1];
											$row[2]=$timing[2];
											$row[3]=$timing[3];
											$timing[3]='';
											$timing[2]='';
											$timing[1]='';
											$this->Ods->setCellStyle('Duration', null, 3);
											$this->Ods->addRow($row);
											$IsTitle=false;
											break;
										case 'I':
										case 'T':
											$lnk=$Item->Text.': '.$Item->Events;
											$row=array('', '', '', '', htmlspecialchars(strip_tags($lnk)));
											if($Item->Shift and $timing[1]) {
												$row[0]=$timing[0];
											}
											$row[1]=$timing[1];
											$row[2]=$timing[2];
											$row[3]=$timing[3];
											$timing[3]='';
											$timing[2]='';
											$timing[1]='';
											$this->Ods->setCellStyle('Duration', null, 3);
											$this->Ods->addRow($row);
											$IsTitle=false;
											if($this->Finalists) { // && $Item->Session<=1) {
												// Bronze or Gold Finals
												if($Item->Type=='I') {
													$SQL="select ind1.IndRank LeftRank, ind2.IndRank RightRank, concat(upper(e1.EnFirstname), ' ', e1.EnName, ' (', c1.CoCode, ')') LeftSide,
													concat('(', c2.CoCode, ') ', upper(e2.EnFirstname), ' ', e2.EnName) RightSide
													from Finals tf1
													inner join Finals tf2 on tf1.FinEvent=tf2.FinEvent and tf1.FinTournament=tf2.FinTournament and tf2.FinMatchNo=tf1.FinMatchNo+1 and tf2.FinMatchNo%2=1
													inner join FinSchedule fs1 on tf1.FinTournament=fs1.FsTournament and tf1.FinEvent=fs1.FsEvent and tf1.FinMatchNo=fs1.FsMatchNo and fs1.FsTeamEvent=0 and fs1.FsScheduledDate='$Date' and fs1.FsScheduledTime='$Time'
													inner join FinSchedule fs2 on tf2.FinTournament=fs2.FsTournament and tf2.FinEvent=fs2.FsEvent and tf2.FinMatchNo=fs2.FsMatchNo and fs2.FsTeamEvent=0 and fs2.FsScheduledDate='$Date' and fs2.FsScheduledTime='$Time'
													inner join Entries e1 on e1.EnId=tf1.FinAthlete and tf1.FinEvent IN ('$Item->Event')
													inner join Entries e2 on e2.EnId=tf2.FinAthlete and tf2.FinEvent IN ('$Item->Event')
													inner join Individuals ind1 on e1.EnId=ind1.IndId and tf1.FinEvent=ind1.IndEvent
													inner join Individuals ind2 on e2.EnId=ind2.IndId and tf2.FinEvent=ind2.IndEvent
													inner join Countries c1 on e1.EnCountry=c1.CoId and c1.CoTournament=$this->TourId
													inner join Countries c2 on e2.EnCountry=c2.CoId and c2.CoTournament=$this->TourId
													inner join Grids on tf1.FinMatchNo=GrMatchNo and GrPhase=$Item->Session
													where tf1.FinTournament=$this->TourId";
												} else {
													$SQL="select ind1.TeRank LeftRank, ind2.TeRank RightRank, concat(c1.CoName, ' (', c1.CoCode, ')') LeftSide,
													concat('(', c2.CoCode, ') ', c2.CoName) RightSide
													from TeamFinals tf1
													inner join TeamFinals tf2 on tf1.TfEvent=tf2.TfEvent and tf1.TfTournament=tf2.TfTournament and tf2.TfMatchNo=tf1.TfMatchNo+1 and tf2.TfMatchNo%2=1
													inner join FinSchedule fs1 on tf1.TfTournament=fs1.FsTournament and tf1.TfEvent=fs1.FsEvent and tf1.TfMatchNo=fs1.FsMatchNo and fs1.FsTeamEvent=1 and fs1.FsScheduledDate='$Date' and fs1.FsScheduledTime='$Time'
													inner join FinSchedule fs2 on tf2.TfTournament=fs2.FsTournament and tf2.TfEvent=fs2.FsEvent and tf2.TfMatchNo=fs2.FsMatchNo and fs2.FsTeamEvent=1 and fs2.FsScheduledDate='$Date' and fs2.FsScheduledTime='$Time'
													inner join Countries c1 on c1.CoId=tf1.TfTeam and tf1.TfEvent IN ('$Item->Event')
													inner join Countries c2 on c2.CoId=tf2.TfTeam and tf2.TfEvent IN ('$Item->Event')
													inner join Teams ind1 on c1.CoId=ind1.TeCoId and tf1.TfEvent=ind1.TeEvent and tf1.TfSubTeam=ind1.TeSubTeam and ind1.TeFinEvent=1
													inner join Teams ind2 on c2.CoId=ind2.TeCoId and tf2.TfEvent=ind2.TeEvent and tf2.TfSubTeam=ind2.TeSubTeam and ind2.TeFinEvent=1
													inner join Grids on tf1.TfMatchNo=GrMatchNo and GrPhase=$Item->Session
													where tf1.TfTournament=$this->TourId";
												}
												$q=safe_r_SQL($SQL);
												if(safe_num_rows($q)==1 and $r=safe_fetch($q) and trim($r->LeftSide) and trim($r->RightSide)) {
													$this->Ods->addCell(htmlspecialchars(strip_tags(($this->Ranking ? '#'.$r->LeftRank.' ' : '') . $r->LeftSide.' - '.$r->RightSide . ($this->Ranking ? ' #'.$r->RightRank : ''))));
												}
											}
											break;
										case 'R':
											$lnk=$Item->Text.': '.$Item->Events;
											$row=array('', '', '', '', htmlspecialchars(strip_tags($lnk)));
											if($Item->Shift and $timing[1]) {
												$row[0]=$timing[0];
											}
											$row[1]=$timing[1];
											$row[2]=$timing[2];
											$row[3]=$timing[3];
											$timing[3]='';
											$timing[2]='';
											$timing[1]='';
											$this->Ods->setCellStyle('Duration', null, 3);
											$this->Ods->addRow($row);
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
													$this->Ods->addCell($r->LeftSide.' - '.$r->RightSide);
												}
											}
											break;
										default:
// 											debug_svela($Item);
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
									$row=array('', '', '', '', htmlspecialchars(strip_tags($lnk)));
									if($Item->Shift and $timing[1]) {
										$row[0]=$timing[0];
									}
									$row[1]=$timing[1];
									$row[2]=$timing[2];
									$row[3]=$timing[3];
									$this->Ods->setCellStyle('Duration', null, 3);
									$this->Ods->setCellStyle('Comments', null, 4);
									$this->Ods->addRow($row);
									$IsTitle=false;
								}
							}
							$FirstTitle=false;
						}
					}
				}
			}
		}

		$terne=array(
				array(0,255,0),
				array(255,153,255),
				array(255,255,204),
				array(153,153,255),
				array(255,153,0),
				array(204,255,204),
				//array(102,0,51),
				array(51,204,204),
		);

		$ColorArray=array();
		foreach($terne as $col) {
			$ColorArray[] = sprintf("#%02X%02X%02X", $col[0], $col[1], $col[2]);
		}
		foreach($terne as $col) {
			$ColorArray[] = sprintf("#%02X%02X%02X", $col[1], $col[2], $col[0]);
		}
		foreach($terne as $col) {
			$ColorArray[] = sprintf("#%02X%02X%02X", $col[2], $col[0], $col[1]);
		}

		$ColorAssignment = array();
		$ColorIndex=0;

		if(!$LocationsToPrint=Get_Tournament_Option('FopLocations')) {
			$tmp=new stdClass();
			$tmp->Loc='';
			$tmp->Tg1=1;
			$tmp->Tg2=9999;
			$LocationsToPrint[]=$tmp;
		}

		$Done=array();

		$OldDate='';
		$OldTime='';

		foreach($this->Schedule as $Date => $Times) {
			$this->Ods->setActiveSheet($Date);
			$this->Ods->currentRow = 0;
			$this->Ods->currentCell= 0;

			$this->Ods->addRow($this->FopVersionText);
			$this->Ods->currentRow ++;

			$this->Ods->setRowStyle('DateFOPRow');
			$this->Ods->setColStyle('TimeCol', 0, 4);
			$this->Ods->setColStyle('DescCol', 4, 1);
			$this->Ods->setColStyle('TgtCol', 5, 250);
			$this->Ods->setCellStyle('DateFOPCell');
			$this->Ods->addRow(formatTextDate($Date, true));

			$OldTitle='';
			$OldSubTitle='';
			$OldType='';
			$OldStart='';
			$OldEnd='';
			$IsTitle=false;

			$OldComment='';
			$OldTime='';
			$RowTime=$this->Ods->currentRow;
			ksort($Times);

			foreach($Times as $Time => $Sessions) {
				$Singles=array();
				$this->Ods->currentRow++;
				foreach($Sessions as $Session => $Distances) {
					foreach($Distances as $Distance => $Items) {
						foreach($Items as $k => $Item) {

							if($Item->Comments) {
								$SingleKey="{$Item->Duration}-{$Item->Title}-{$Item->SubTitle}-{$Item->Comments}";
								if(in_array($SingleKey, $Singles)) continue;
								$Singles[]=$SingleKey;
							}

							$timingDelayed='';
							$timing=array('', '', '', '');


							if($Item->Type=='Z') {
								// free text
								$timing[1]=$Item->Start;
								if($Item->Duration) {
									$timing[2] = addMinutes($Item->Start, $Item->Duration);
									$timing[3] = sprintf('%02d:%02d', $Item->Duration/60, $Item->Duration%60);
								}
								if($Item->Shift) $timing[0] = '+'.$Item->Shift;

								if($OldTitle!=$Item->Title and $Item->Title) {
									if(!$IsTitle) {
										$this->Ods->setRowStyle('TitleFOPRow');
										$this->Ods->setCellStyle('TitleFOP', null, 4);
										$this->Ods->addRow(array('', '', '', '', htmlspecialchars(strip_tags($Item->Title))));
									}
									$OldTitle=$Item->Title;
									$OldSubTitle='';
									$IsTitle=true;
								}

								if($OldSubTitle!=$Item->SubTitle and $Item->SubTitle) {
									$row=array('', '', '', '', htmlspecialchars(strip_tags($Item->SubTitle)));
									if(!$Item->Text) {
										if($Item->Shift and $timing[1]) {
											$row[0]=$timing[0];
										}
										$row[1]=$timing[1];
										$row[2]=$timing[2];
										$row[3]=$timing[3];
										$timing[3]='';
										$timing[2]='';
										$timing[1]='';
									}
									$this->Ods->setCellStyle('SubTitleFOP', null, 4);
									$this->Ods->setCellStyle('Duration', null, 3);
									$this->Ods->addRow($row);
									$OldSubTitle=$Item->SubTitle;
									$IsTitle=false;
								}
								if($Item->Text) {
									$row=array('', '', '', '', htmlspecialchars(strip_tags($Item->Text)));
									if($Item->Shift and $timing[1]) {
										$row[0]=$timing[0];
									}
									$row[1]=$timing[1];
									$row[2]=$timing[2];
									$row[3]=$timing[3];
									$timing[3]='';
									$timing[2]='';
									$timing[1]='';
									$this->Ods->setCellStyle('Duration', null, 3);
									$this->Ods->addRow($row);
									$IsTitle=false;
								}
								$OldStart=$Item->Start;
								$OldEnd=$Item->Duration;
								$OldComment='';

								// is there a terget assigment?
								if($Item->Target) {
									$rows=array();
									$MaxTgt=0;
									foreach(explode(',', $Item->Target) as $Block) {
										$tmp= explode('@', $Block);
										$Range=$tmp[0];
										$Dist=$tmp[1];
										if(!empty($tmp[2])) $Event=$tmp[2];
										if(!empty($tmp[3])) $Target=$tmp[3];

										if(empty($ColorAssignment["{$Dist}-{$Event}"])) {
											$ColorAssignment["{$Dist}-{$Event}"]='Color'.$ColorIndex;
											$this->Ods->setStyle('Color'.$ColorIndex, array('style:table-cell-properties' => array('fo:background-color' => $ColorArray[$ColorIndex])));
											$ColorIndex++;
										}

										$tmp=explode('-', $Range);
										if(count($tmp)>1) {
											foreach(range($tmp[0], $tmp[1]) as $tgt) {
												$rows[$tgt]['d']=$Dist;
												$rows[$tgt]['e']=$Event;
												$rows[$tgt]['c']=$ColorAssignment["{$Dist}-{$Event}"];
												$MaxTgt=max($MaxTgt, $tgt);
											}
										} else {
											$rows[$tmp[0]]['d']=$Dist;
											$rows[$tmp[0]]['e']=$Event;
											$rows[$tmp[0]]['c']=$ColorAssignment["{$Dist}-{$Event}"];
											$MaxTgt=max($MaxTgt, $tmp[0]);
										}
									}

									$tgts=array();
									$oldDistance=0;
									$grp=0;
									ksort($rows);

									foreach($rows as $tgt => $def) {
										if($oldDistance!="{$def['d']}-{$def['e']}") $grp++;
										$oldDistance="{$def['d']}-{$def['e']}";
										$tgts[$grp]['distance']=$def['d'];
										$tgts[$grp]['targets'][]=$tgt;
									}

									$this->Ods->currentRow-=2;

									foreach($tgts as $k=>$grp) {
										$this->Ods->currentCell=$grp['targets'][0]+6;
										$this->Ods->setCellStyle('Distance');
										$this->Ods->setCellAttribute('table:number-columns-spanned', 1+end($grp['targets'])-$grp['targets'][0]);
										$this->Ods->Cell($grp['distance'], 'string');
										foreach($grp['targets'] as $tgt) {
											$this->Ods->setCellStyle($rows[$tgt]['c'], $this->Ods->currentRow+1, $tgt+6);
										}
										$this->Ods->Cell($rows[$tgt]['e'], 'string', $this->Ods->currentRow+1, $grp['targets'][0]+6, true);
										// 												$this->Ods->Cell(1+end($grp['targets'])-$grp['targets'][0], 'string', $this->Ods->currentRow+1, end($grp['targets'])+6, true);
									}
									$OldRow=$this->Ods->currentRow+3;
									$this->Ods->currentRow=2;
									$this->Ods->currentCell=7;
									foreach(range(1, $MaxTgt) as $tgt) $this->Ods->Cell($tgt);
									$this->Ods->currentCell=0;
									$this->Ods->currentRow=$OldRow;

								}
							} else {
								// all other kind of texts have a title and the items
								if($OldTitle!=$Item->Title) {
									// Title
									if(!$IsTitle) {
										$this->Ods->setRowStyle('TitleRow');
										$this->Ods->setCellStyle('Title', null, 4);
										$this->Ods->addRow(array('', '', '', '', htmlspecialchars(strip_tags($Item->Title))));
									}
									$OldTitle=$Item->Title;
									$IsTitle=true;
									$OldSubTitle='';
								}
								if($OldSubTitle!=$Item->SubTitle) {
									// SubTitle
									$row=array('', '', '', '', htmlspecialchars(strip_tags($Item->SubTitle)));
									$this->Ods->setCellStyle('SubTitle', null, 4);
									$this->Ods->addRow($row);
									$OldSubTitle=$Item->SubTitle;
									$IsTitle=false;
								}

								$timing=array('', '', '', '');
								if($OldStart != $Item->Start or $OldEnd != $Item->Duration) {
									$timing[1]=$Item->Start;
									if($Item->Duration) {
										$timing[2]=addMinutes($Item->Start, $Item->Duration);
										$timing[3] = sprintf('%02d:%02d', $Item->Duration/60, $Item->Duration%60);
									}
									if($Item->Shift) $timing[0] = '+'.$Item->Shift;
									$OldStart=$Item->Start;
									$OldEnd=$Item->Duration;
								}

								$lnk=strip_tags($Item->Text);
								if(!$Item->Warmup) {
									// not warmup!
									$OldComment='';
									switch($Item->Type) {
										case 'Q':
										case 'E':
											if($OldDate==$Date and $OldTime==$Time) $this->Ods->currentRow--;
											$Link=array();
											$lnk='';
											if($Item->Comments) {
												$row=array('', '', '', '', htmlspecialchars(strip_tags($Item->Comments)));
												if($Item->Shift and $timing[1]) {
													$row[0]=$timing[0];
												}
												$row[1]=$timing[1];
												$row[2]=$timing[2];
												$row[3]=$timing[3];
												$timing[3]='';
												$timing[2]='';
												$timing[1]='';
												$this->Ods->setCellStyle('Duration', null, 3);
												$this->Ods->setCellStyle('Comments', null, 4);
												$this->Ods->addRow($row);
												$IsTitle=false;
											}

											if(count($this->Groups[$Item->Type][$Session])==1) {
												$txt=$Item->Text.$lnk;
											} elseif($Item==@end(end(end(end($this->Groups[$Item->Type][$Session]))))) {
												$txt=$Item->DistanceName.$lnk;
											} else {
												$txt=$Item->DistanceName;
												// more distances defined so format is different...
											}
											$row=array('', '', '', '', htmlspecialchars(strip_tags($txt)));
											if($Item->Shift and $timing[1]) {
												$row[0]=$timing[0];
											}
											$row[1]=$timing[1];
											$row[2]=$timing[2];
											$row[3]=$timing[3];
											$timing[3]='';
											$timing[2]='';
											$timing[1]='';
											$this->Ods->setCellStyle('Duration', null, 3);
											$this->Ods->addRow($row);
											$IsTitle=false;

											if($Item->Type=='Q' and empty($Done[$Date][$Time][$Item->Type])) {
												$Done[$Date][$Time][$Item->Type]=true;
												if($Item->Target) {
													// USES THIS ONE!!!
													$rows=array();
													$MaxTgt=0;
													foreach(explode(',', $Item->Target) as $Block) {
														$tmp= explode('@', $Block);
														$Range=$tmp[0];
														$Dist=$tmp[1];
														if(!empty($tmp[2])) $Event=$tmp[2];
														if(!empty($tmp[3])) $Target=$tmp[3];

														if(empty($ColorAssignment["{$Dist}-{$Event}"])) {
															$ColorAssignment["{$Dist}-{$Event}"]='Color'.$ColorIndex;
															$this->Ods->setStyle('Color'.$ColorIndex, array('style:table-cell-properties' => array('fo:background-color' => $ColorArray[$ColorIndex])));
															$ColorIndex++;
														}

														$tmp=explode('-', $Range);
														if(count($tmp)>1) {
															foreach(range($tmp[0], $tmp[1]) as $tgt) {
																$rows[$tgt]['d']=$Dist;
																$rows[$tgt]['e']=$Event;
																$rows[$tgt]['c']=$ColorAssignment["{$Dist}-{$Event}"];
																$MaxTgt=max($MaxTgt, $tgt);
															}
														} else {
															$rows[$tmp[0]]['d']=$Dist;
															$rows[$tmp[0]]['e']=$Event;
															$rows[$tmp[0]]['c']=$ColorAssignment["{$Dist}-{$Event}"];
															$MaxTgt=max($MaxTgt, $tmp[0]);
														}
													}

													$tgts=array();
													$oldDistance=0;
													$grp=0;
													ksort($rows);

													foreach($rows as $tgt => $def) {
														if($oldDistance!="{$def['d']}-{$def['e']}") $grp++;
														$oldDistance="{$def['d']}-{$def['e']}";
														$tgts[$grp]['distance']=$def['d'];
														$tgts[$grp]['targets'][]=$tgt;
													}

													$this->Ods->currentRow-=2;

													foreach($tgts as $k=>$grp) {
														$this->Ods->currentCell=$grp['targets'][0]+6;
														$this->Ods->setCellStyle('Distance');
														$this->Ods->setCellAttribute('table:number-columns-spanned', 1+end($grp['targets'])-$grp['targets'][0]);
														$this->Ods->Cell($grp['distance'], 'string');
														foreach($grp['targets'] as $tgt) {
															$this->Ods->setCellStyle($rows[$tgt]['c'], $this->Ods->currentRow+1, $tgt+6);
														}
														$this->Ods->Cell($rows[$tgt]['e'], 'string', $this->Ods->currentRow+1, $grp['targets'][0]+6, true);
														// 												$this->Ods->Cell(1+end($grp['targets'])-$grp['targets'][0], 'string', $this->Ods->currentRow+1, end($grp['targets'])+6, true);
													}
													$OldRow=$this->Ods->currentRow+3;
													$this->Ods->currentRow=2;
													$this->Ods->currentCell=7;
													foreach(range(1, $MaxTgt) as $tgt) $this->Ods->Cell($tgt);
													$this->Ods->currentCell=0;
													$this->Ods->currentRow=$OldRow;

												} else {
													// Get which session and distance is shot at this time...
													$Sql="select * from DistanceInformation where DiTournament={$_SESSION['TourId']} and DiDay='$Date' and DiStart='$Time'";
													$t=safe_r_sql($Sql);
													if(safe_num_rows(($t))) {
														$this->Ods->currentRow-=($Item->Comments ? 2 : 1);
													}
													$MaxTgt=0;
													while($u=safe_fetch($t)) {
														$Sql="select distinct cast(substr(QuTargetNo,2) as unsigned) TargetNo, IFNULL(Td{$u->DiDistance},'.{$u->DiDistance}.') as Distance, TarDescr, TarDim, DiDay, DiStart, DiWarmStart from
															Entries
															inner join Qualifications on EnId=QuId
															inner join DistanceInformation on QuSession=DiSession and DiTournament={$_SESSION['TourId']} and DiDistance={$u->DiDistance} and DiDay='$Date' and DiStart='$Time'
															left join TournamentDistances on concat(trim(EnDivision),trim(EnClass)) like TdClasses and EnTournament=TdTournament
															left join (select TfId, TarDescr, TfW{$u->DiDistance} as TarDim, TfTournament from TargetFaces inner join Targets on TfT{$u->DiDistance}=TarId) tf on TfTournament=EnTournament and TfId=EnTargetFace
															where EnTournament={$_SESSION['TourId']}
															order by TargetNo, Distance desc, TargetNo, TarDescr, TarDim";
														$v=safe_r_sql($Sql);
														$tgts=array();
														$oldDistance=0;
														$grp=0;
														while($w=safe_fetch($v)) {
															$MaxTgt=max($MaxTgt, $w->TargetNo);
															if($oldDistance!=$w->Distance) $grp++;
															$oldDistance=$w->Distance;
															// table:number-columns-spanned
															$tgts[$grp]['distance']=$w->Distance;
															$tgts[$grp]['targets'][]=$w->TargetNo;
														}
														foreach($tgts as $k=>$grp) {
															if(empty($ColorAssignment[$grp['distance']])) {
																$ColorAssignment[$grp['distance']]='Color'.$ColorIndex;
																$this->Ods->setStyle('Color'.$ColorIndex, array('style:table-cell-properties' => array('fo:background-color' => $ColorArray[$ColorIndex])));
																$ColorIndex++;
															}
															$this->Ods->currentCell=$grp['targets'][0]+6;
															$this->Ods->setCellStyle('Distance');
															$this->Ods->setCellAttribute('table:number-columns-spanned', 1+end($grp['targets'])-$grp['targets'][0]);
															$this->Ods->Cell($grp['distance'], 'string');
															foreach($grp['targets'] as $tgt) {
																$this->Ods->setCellStyle($ColorAssignment[$grp['distance']], $this->Ods->currentRow+1, $tgt+6);
															}
															$this->Ods->Cell('1', 'string', $this->Ods->currentRow+1, $grp['targets'][0]+6, true);
															$this->Ods->Cell(1+end($grp['targets'])-$grp['targets'][0], 'string', $this->Ods->currentRow+1, end($grp['targets'])+6, true);
														}
													}
													$OldRow=$this->Ods->currentRow+1;
													$this->Ods->currentRow=2;
													$this->Ods->currentCell=7;
													foreach(range(1, $MaxTgt) as $tgt) $this->Ods->Cell($tgt);
													$this->Ods->currentCell=0;
													$this->Ods->currentRow=$OldRow;

												}
											}
											break;
										case 'I':
										case 'T':
											if($OldDate==$Date and $OldTime==$Time) {
												$this->Ods->currentRow--;
											}
											$lnk=$Item->Text.': '.$Item->Events;
											$row=array('', '', '', '', htmlspecialchars(strip_tags($lnk)));
											if($Item->Shift and $timing[1]) {
												$row[0]=$timing[0];
											}
											$row[1]=$timing[1];
											$row[2]=$timing[2];
											$row[3]=$timing[3];
											$timing[3]='';
											$timing[2]='';
											$timing[1]='';
											$this->Ods->setCellStyle('Duration', null, 3);
											$this->Ods->addRow($row);
											$IsTitle=false;
											if($this->Finalists) { // && $Item->Session<=1) {
												// Bronze or Gold Finals
												if($Item->Type=='I') {
													$SQL="select ind1.IndRank LeftRank, ind2.IndRank RightRank, concat(upper(e1.EnFirstname), ' ', e1.EnName, ' (', c1.CoCode, ')') LeftSide,
														concat('(', c2.CoCode, ') ', upper(e2.EnFirstname), ' ', e2.EnName) RightSide
														from Finals tf1
														inner join Finals tf2 on tf1.FinEvent=tf2.FinEvent and tf1.FinTournament=tf2.FinTournament and tf2.FinMatchNo=tf1.FinMatchNo+1 and tf2.FinMatchNo%2=1
														inner join FinSchedule fs1 on tf1.FinTournament=fs1.FsTournament and tf1.FinEvent=fs1.FsEvent and tf1.FinMatchNo=fs1.FsMatchNo and fs1.FsTeamEvent=0 and fs1.FsScheduledDate='$Date' and fs1.FsScheduledTime='$Time'
														inner join FinSchedule fs2 on tf2.FinTournament=fs2.FsTournament and tf2.FinEvent=fs2.FsEvent and tf2.FinMatchNo=fs2.FsMatchNo and fs2.FsTeamEvent=0 and fs2.FsScheduledDate='$Date' and fs2.FsScheduledTime='$Time'
														inner join Entries e1 on e1.EnId=tf1.FinAthlete and tf1.FinEvent IN ('$Item->Event')
														inner join Entries e2 on e2.EnId=tf2.FinAthlete and tf2.FinEvent IN ('$Item->Event')
														inner join Individuals ind1 on e1.EnId=ind1.IndId and tf1.FinEvent=ind1.IndEvent
														inner join Individuals ind2 on e2.EnId=ind2.IndId and tf2.FinEvent=ind2.IndEvent
														inner join Countries c1 on e1.EnCountry=c1.CoId and c1.CoTournament=$this->TourId
														inner join Countries c2 on e2.EnCountry=c2.CoId and c2.CoTournament=$this->TourId
														inner join Grids on tf1.FinMatchNo=GrMatchNo and GrPhase=$Item->Session
														where tf1.FinTournament=$this->TourId";
												} else {
													$SQL="select ind1.TeRank LeftRank, ind2.TeRank RightRank, concat(c1.CoName, ' (', c1.CoCode, ')') LeftSide,
														concat('(', c2.CoCode, ') ', c2.CoName) RightSide
														from TeamFinals tf1
														inner join TeamFinals tf2 on tf1.TfEvent=tf2.TfEvent and tf1.TfTournament=tf2.TfTournament and tf2.TfMatchNo=tf1.TfMatchNo+1 and tf2.TfMatchNo%2=1
														inner join FinSchedule fs1 on tf1.TfTournament=fs1.FsTournament and tf1.TfEvent=fs1.FsEvent and tf1.TfMatchNo=fs1.FsMatchNo and fs1.FsTeamEvent=1 and fs1.FsScheduledDate='$Date' and fs1.FsScheduledTime='$Time'
														inner join FinSchedule fs2 on tf2.TfTournament=fs2.FsTournament and tf2.TfEvent=fs2.FsEvent and tf2.TfMatchNo=fs2.FsMatchNo and fs2.FsTeamEvent=1 and fs2.FsScheduledDate='$Date' and fs2.FsScheduledTime='$Time'
														inner join Countries c1 on c1.CoId=tf1.TfTeam and tf1.TfEvent IN ('$Item->Event')
														inner join Countries c2 on c2.CoId=tf2.TfTeam and tf2.TfEvent IN ('$Item->Event')
														inner join Teams ind1 on c1.CoId=ind1.TeCoId and tf1.TfEvent=ind1.TeEvent and tf1.TfSubTeam=ind1.TeSubTeam and ind1.TeFinEvent=1
														inner join Teams ind2 on c2.CoId=ind2.TeCoId and tf2.TfEvent=ind2.TeEvent and tf2.TfSubTeam=ind2.TeSubTeam and ind2.TeFinEvent=1
														inner join Grids on tf1.TfMatchNo=GrMatchNo and GrPhase=$Item->Session
														where tf1.TfTournament=$this->TourId";
												}
												$q=safe_r_SQL($SQL);
												if(safe_num_rows($q)==1 and $r=safe_fetch($q) and trim($r->LeftSide) and trim($r->RightSide)) {
													$this->Ods->addCell(htmlspecialchars(strip_tags(($this->Ranking ? '#'.$r->LeftRank.' ' : '') . $r->LeftSide.' - '.$r->RightSide . ($this->Ranking ? ' #'.$r->RightRank : ''))));
												}
											}

											if(empty($Done[$Date][$Time][$Item->Type])) {
												$Done[$Date][$Time][$Item->Type]=true;
												$MaxTgt=0;
												$rows=array();

												// get the warmup targets first (will be overwritten by the real matches...
												$MyQuery = "SELECT FwEvent ,
														FwTargets,
														FwOptions,
														UNIX_TIMESTAMP(FwDay) as SchDate,
														DATE_FORMAT(FwTime,'" . get_text('TimeFmt') . "') as SchTime,
														FwDay,
														FwTime, EvDistance
													FROM FinWarmup
													INNER JOIN Events ON FwEvent=EvCode AND FwTeamEvent=EvTeamEvent AND FwTournament=EvTournament
													WHERE FwTournament=" . StrSafe_DB($_SESSION['TourId']) . "
														AND date_format(FwDay, '%Y-%m-%d')='$Date' and FwTime='$Time'
														and FwTargets!=''
													ORDER BY FwTargets";
												$t = safe_r_sql($MyQuery);
												while($u=safe_fetch($t)) {
													foreach(explode(',', $u->FwTargets) as $range) {
														$tmp=explode('-', $range);
														if(count($tmp)>1) {
															foreach(range($tmp[0], $tmp[1]) as $tgt) {
																$rows[$tgt]['d']=$u->EvDistance;
																$rows[$tgt]['e']=$u->FwEvent;
																$rows[$tgt]['c']='ColorWarmup';
																$rows[$tgt]['w']='1';
																$MaxTgt=max($MaxTgt, $tgt);
															}
														} else {
															$rows[$tmp[0]]['d']=$u->EvDistance;
															$rows[$tmp[0]]['e']=$u->FwEvent;
															$rows[$tmp[0]]['c']='ColorWarmup';
															$rows[$tmp[0]]['w']='1';
															$MaxTgt=max($MaxTgt, $tmp[0]);
														}
													}
												}

												// Now get the targets with the matches
												$MyQuery = "SELECT '' as Warmup, FSEvent, FSTeamEvent, GrPhase, FsMatchNo, FsTarget, '' as TargetTo, EvMatchArrowsNo, EvMatchMode, EvMixedTeam, EvTeamEvent, UNIX_TIMESTAMP(FSScheduledDate) as SchDate, DATE_FORMAT(FSScheduledTime,'" . get_text('TimeFmt') . "') as SchTime, EvFinalFirstPhase,
														@bit:=pow(2, ceil(log2(GrPhase))+1) & EvMatchArrowsNo,
														IF(@bit=0,EvFinEnds,EvElimEnds) AS `ends`,
														IF(@bit=0,EvFinArrows,EvElimArrows) AS `arrows`,
														IF(@bit=0,EvFinSO,EvElimSO) AS `so`,
														EvMaxTeamPerson,
														FSScheduledDate,
														FSScheduledTime, EvDistance
													FROM FinSchedule
													INNER JOIN Grids ON FSMatchNo=GrMatchNo
													INNER JOIN Events ON FSEvent=EvCode AND FSTeamEvent=EvTeamEvent AND FSTournament=EvTournament
													WHERE FSTournament=" . StrSafe_DB($_SESSION['TourId']) . "
														AND FSScheduledDate='$Date' and FSScheduledTime='$Time'
														and FsTarget!=''
														AND GrPhase<=if(EvFinalFirstPhase=24, 32, if(EvFinalFirstPhase=48, 64, EvFinalFirstPhase))
													ORDER BY Warmup ASC, FSTarget ASC, FSMatchNo ASC";
												$MaxTgt=0;
												$tgts=array();
												$oldDistance=0;
												$grp=0;
												$t = safe_r_sql($MyQuery);
												while($u=safe_fetch($t)) {
													if(empty($ColorAssignment["{$u->EvDistance}-{$u->FSEvent}"])) {
														$ColorAssignment["{$u->EvDistance}-{$u->FSEvent}"]='Color'.$ColorIndex;
														$this->Ods->setStyle('Color'.$ColorIndex, array('style:table-cell-properties' => array('fo:background-color' => $ColorArray[$ColorIndex])));
														$ColorIndex++;
													}
													$u->FsTarget=intval($u->FsTarget);
													$rows[$u->FsTarget]['d']=$u->EvDistance;
													$rows[$u->FsTarget]['e']=$u->FSEvent;
													$rows[$u->FsTarget]['c']=$ColorAssignment["{$u->EvDistance}-{$u->FSEvent}"];
													$rows[$u->FsTarget]['w']='0';
													$MaxTgt=max($MaxTgt, $u->FsTarget);
												}

												// $rows is now containing all targets
												$tgts=array();
												$oldDistance=0;
												$grp=0;
												ksort($rows);

												foreach($rows as $tgt => $def) {
													if($oldDistance!="{$def['d']}-{$def['e']}-{$def['w']}") $grp++;
													$oldDistance="{$def['d']}-{$def['e']}-{$def['w']}";
													$tgts[$grp]['distance']=$def['d'];
													$tgts[$grp]['targets'][]=$tgt;
												}

												$this->Ods->currentRow-=2;

												foreach($tgts as $k=>$grp) {
													$this->Ods->currentCell=$grp['targets'][0]+6;
													$this->Ods->setCellStyle('Distance');
													$this->Ods->setCellAttribute('table:number-columns-spanned', 1+end($grp['targets'])-$grp['targets'][0]);
													$this->Ods->Cell($grp['distance'], 'string');
													foreach($grp['targets'] as $tgt) {
														$this->Ods->setCellStyle($rows[$tgt]['c'], $this->Ods->currentRow+1, $tgt+6);
													}
													$this->Ods->Cell($rows[$tgt]['e'], 'string', $this->Ods->currentRow+1, $grp['targets'][0]+6, true);
												}
												$OldRow=$this->Ods->currentRow+3;
												$this->Ods->currentRow=2;
												$this->Ods->currentCell=7;
												foreach(range(1, $MaxTgt) as $tgt) $this->Ods->Cell($tgt);
												$this->Ods->currentCell=0;
												$this->Ods->currentRow=$OldRow;

											}


											break;
										case 'R':
											$lnk=$Item->Text.': '.$Item->Events;
											$row=array('', '', '', '', htmlspecialchars(strip_tags($lnk)));
											if($Item->Shift and $timing[1]) {
												$row[0]=$timing[0];
											}
											$row[1]=$timing[1];
											$row[2]=$timing[2];
											$row[3]=$timing[3];
											$timing[3]='';
											$timing[2]='';
											$timing[1]='';
											$this->Ods->setCellStyle('Duration', null, 3);
											$this->Ods->addRow($row);
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
													$this->Ods->addCell($r->LeftSide.' - '.$r->RightSide);
												}
											}
											break;
										default:
// 											debug_svela($Item);
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
									if($OldDate==$Date and $OldTime==$Time) $this->Ods->currentRow--;
									if($OldComment==$lnk) continue;
									$OldComment=$lnk;
									$row=array('', '', '', '', htmlspecialchars(strip_tags($lnk)));
									if($Item->Shift and $timing[1]) {
										$row[0]=$timing[0];
									}
									$row[1]=$timing[1];
									$row[2]=$timing[2];
									$row[3]=$timing[3];
									$this->Ods->setCellStyle('Duration', null, 3);
									$this->Ods->setCellStyle('Comments', null, 4);
									$this->Ods->addRow($row);
									$IsTitle=false;

									if(empty($Done[$Date][$Time][$Item->Type])) {
										$Done[$Date][$Time][$Item->Type]=true;
										$MaxTgt=0;
										$rows=array();
										switch($Item->Type) {
											case 'Q':
												break;
											case 'I':
											case 'T':

												// get the warmup targets first (will be overwritten by the real matches...
												$MyQuery = "SELECT FwEvent ,
														FwTargets,
														FwOptions,
														UNIX_TIMESTAMP(FwDay) as SchDate,
														DATE_FORMAT(FwTime,'" . get_text('TimeFmt') . "') as SchTime,
														FwDay,
														FwTime, EvDistance
													FROM FinWarmup
													INNER JOIN Events ON FwEvent=EvCode AND FwTeamEvent=EvTeamEvent AND FwTournament=EvTournament
													WHERE FwTournament=" . StrSafe_DB($_SESSION['TourId']) . "
														AND date_format(FwDay, '%Y-%m-%d')='$Date' and FwTime='$Time'
														and FwTargets!=''
														ORDER BY FwTargets";
												$t = safe_r_sql($MyQuery);
												while($u=safe_fetch($t)) {
													foreach(explode(',', $u->FwTargets) as $range) {
														$tmp=explode('-', $range);
														if(count($tmp)>1) {
															foreach(range($tmp[0], $tmp[1]) as $tgt) {
																$rows[$tgt]['d']=$u->EvDistance;
																$rows[$tgt]['e']=$u->FwEvent;
																$rows[$tgt]['c']='ColorWarmup';
																$MaxTgt=max($MaxTgt, $tgt);
															}
														} else {
															$rows[$tmp[0]]['d']=$u->EvDistance;
															$rows[$tmp[0]]['e']=$u->FwEvent;
															$rows[$tmp[0]]['c']='ColorWarmup';
															$MaxTgt=max($MaxTgt, $tmp[0]);
														}
													}
												}

												break;
										}
										$tgts=array();
										$oldDistance=0;
										$grp=0;
										ksort($rows);

										foreach($rows as $tgt => $def) {
											if($oldDistance!="{$def['d']}-{$def['e']}") $grp++;
											$oldDistance="{$def['d']}-{$def['e']}";
											$tgts[$grp]['distance']=$def['d'];
											$tgts[$grp]['targets'][]=$tgt;
										}

										$this->Ods->currentRow-=2;

										foreach($tgts as $k=>$grp) {
											$this->Ods->currentCell=$grp['targets'][0]+6;
											$this->Ods->setCellStyle('Distance');
											$this->Ods->setCellAttribute('table:number-columns-spanned', 1+end($grp['targets'])-$grp['targets'][0]);
											$this->Ods->Cell($grp['distance'], 'string');
											foreach($grp['targets'] as $tgt) {
												$this->Ods->setCellStyle($rows[$tgt]['c'], $this->Ods->currentRow+1, $tgt+6);
											}
											$this->Ods->Cell($rows[$tgt]['e'], 'string', $this->Ods->currentRow+1, $grp['targets'][0]+6, true);
											// 												$this->Ods->Cell(1+end($grp['targets'])-$grp['targets'][0], 'string', $this->Ods->currentRow+1, end($grp['targets'])+6, true);
										}
										$OldRow=$this->Ods->currentRow+3;
										$this->Ods->currentRow=2;
										$this->Ods->currentCell=7;
										foreach(range(1, $MaxTgt) as $tgt) $this->Ods->Cell($tgt);
										$this->Ods->currentCell=0;
										$this->Ods->currentRow=$OldRow;
									}
								}
							}
							$OldTime=$Time;
							$OldDate=$Date;
						}
					}
				}
			}
		}

		$this->Ods->save($filename, 'a');
		die();
	}

	function FOP() {

		$terne=array(
			array(0,255,0),
			array(255,153,255),
			array(255,255,204),
			array(153,153,255),
			array(255,153,0),
			array(204,255,204),
// 			array(204,0,255),
			array(51,204,204),
		);

		// seed a lot of colors (Macolin rules!
		foreach($terne as $col) {
			$ColorArray[] = array($col[0],$col[1],$col[2]);
		}
		foreach($terne as $col) {
			$ColorArray[] = array($col[1],$col[2],$col[0]);
		}
		foreach($terne as $col) {
			$ColorArray[] = array($col[2],$col[0],$col[1]);
		}
		foreach($terne as $col) {
			$ColorArray[] = array($col[0],$col[2],$col[1]);
		}
		foreach($terne as $col) {
			$ColorArray[] = array($col[1],$col[0],$col[2]);
		}
		foreach($terne as $col) {
			$ColorArray[] = array($col[2],$col[1],$col[0]);
		}

		$ColorAssignment = array();
		$ColorIndex=0;
		$OldSession = '';
		$OldDist = '';
		$OldTarget = '';
		$TmpColor=array(255,255,255);
		$SecondaryDistance=0;
		$TgText='';
		$TgFirst=0;
		$TgNo=0;
		$TgTop=0;

		$FirstTarget=0;


		// BUILDS AN ARRAY WITH ALL TARGETS DAY BY DAY
		$FOP=array();
		$Done=array();

		foreach($this->GetSchedule() as $Date => $Times) {
			$FOP[$Date]=array('min'=>0, 'max'=>0, 'times'=>array());
			ksort($Times);

			foreach($Times as $Time => $Sessions) {
				foreach($Sessions as $Session => $Distances) {
					foreach($Distances as $Distance => $Items) {
						foreach($Items as $k => $Item) {
							if($Item->Type=='Z') {
								// if no FOP item skip
								if(!$Item->Target) continue;
								if(empty($FOP[$Date]['times'][$Time])) {
									$FOP[$Date]['times'][$Time]=array('time'=>'', 'text'=>array(), 'targets'=>array(), 'min'=>0, 'max'=>0);
								}
								// attach global info
								if(empty($FOP[$Date]['times'][$Time]['time'])) {
									$FOP[$Date]['times'][$Time]['time']=$Item->Start;
									if($Item->Duration) {
										$FOP[$Date]['times'][$Time]['time'] .= '-'.addMinutes($Item->Start, $Item->Duration);
									}
								}
								$tmp=array_merge(explode(' - ', $Item->Title), explode(' - ', $Item->SubTitle), explode(' - ', $Item->Text));
								foreach($tmp as $txt) {
									if($txt and !in_array($txt, $FOP[$Date]['times'][$Time]['text'])) {
										$FOP[$Date]['times'][$Time]['text'][]=strip_tags($txt);
									}
								}

								foreach(explode(',', $Item->Target) as $Block) {

									$tmp= explode('@', $Block);
									$bl=new TargetButt();
									$Range=$tmp[0];
									$bl->Distance=$tmp[1];
									if(!empty($tmp[2])) $bl->Event=$tmp[2];
									if(!empty($tmp[3])) $bl->Target=$tmp[3];

									$tmp=explode('-', $Range);
									if(!$FOP[$Date]['times'][$Time]['min']) $FOP[$Date]['times'][$Time]['min']=$tmp[0];
									if(!$FOP[$Date]['min']) $FOP[$Date]['min']=$tmp[0];
									$FOP[$Date]['times'][$Time]['min']=min($FOP[$Date]['times'][$Time]['min'], $tmp[0]);
									$FOP[$Date]['min']=min($FOP[$Date]['min'], $tmp[0]);
									if(count($tmp)>1) {
										$bl->Range=array($tmp[0], $tmp[1]);
										$FOP[$Date]['times'][$Time]['max']=max($FOP[$Date]['times'][$Time]['max'], $tmp[1]);
										$FOP[$Date]['max']=max($FOP[$Date]['max'], $tmp[1]);
									} else {
										$bl->Range=array($tmp[0],$tmp[0]);
										$FOP[$Date]['times'][$Time]['max']=max($FOP[$Date]['times'][$Time]['max'], $tmp[0]);
										$FOP[$Date]['max']=max($FOP[$Date]['max'], $tmp[0]);
									}

									$FOP[$Date]['times'][$Time]['targets'][]=$bl;
								}
							} else {
								// No free text, so targets are (should be) assigned
								if(empty($FOP[$Date]['times'][$Time])) {
									$FOP[$Date]['times'][$Time]=array('time'=>'', 'text'=>array(), 'targets'=>array(), 'min'=>0, 'max'=>0);
								}

								if(empty($FOP[$Date]['times'][$Time]['time'])) {
									$FOP[$Date]['times'][$Time]['time']=$Item->Start;
									if($Item->Duration) {
										$FOP[$Date]['times'][$Time]['time'] .= '-'.addMinutes($Item->Start, $Item->Duration);
									}
								}
								$OldComment='';
								if(!$Item->Warmup) {
									// not warmup!
									switch($Item->Type) {
										case 'Q':
										case 'E':
											$tmp=preg_replace('/\([^)]+\)/sim', '', $Item->Title.' - '.$Item->SubTitle.' - '.$Item->Text);
											foreach(preg_split('/( - )|(, )/', $tmp) as $txt) {
												if($txt and !in_array($txt, $FOP[$Date]['times'][$Time]['text'])) {
													$FOP[$Date]['times'][$Time]['text'][]=strip_tags($txt);
												}
											}

// 											if($Item->Comments and !in_array($Item->Comments, $FOP[$Date]['times'][$Time]['text'])) {
// 												$FOP[$Date]['times'][$Time]['text'][]=strip_tags($Item->Comments);
// 											}

											if($Item->Type=='Q' and empty($Done[$Date][$Time][$Item->Type])) {
// 												$Done[$Date][$Time][$Item->Type]=true;
												if($Item->Target) {
													// USES THIS ONE!!!
													foreach(explode(',', $Item->Target) as $Block) {
														$tmp= explode('@', $Block);
														$bl=new TargetButt();
														$Range=$tmp[0];
														$bl->Distance=$tmp[1];
														if(!empty($tmp[2])) $bl->Event=$tmp[2];
														if(!empty($tmp[3])) $bl->Target=$tmp[3];

														if(empty($ColorAssignment["{$bl->Distance}-{$bl->Event}"])) {
															$ColorAssignment["{$bl->Distance}-{$bl->Event}"]=$ColorArray[$ColorIndex];
															$ColorIndex++;
														}
														$bl->Colour=$ColorAssignment["{$bl->Distance}-{$bl->Event}"];

														$tmp=explode('-', $Range);
														if(!$FOP[$Date]['times'][$Time]['min']) $FOP[$Date]['times'][$Time]['min']=$tmp[0];
														if(!$FOP[$Date]['min']) $FOP[$Date]['min']=$tmp[0];
														$FOP[$Date]['times'][$Time]['min']=min($FOP[$Date]['times'][$Time]['min'], $tmp[0]);
														$FOP[$Date]['min']=min($FOP[$Date]['min'], $tmp[0]);
														if(count($tmp)>1) {
															$bl->Range=array($tmp[0], $tmp[1]);
															$FOP[$Date]['times'][$Time]['max']=max($FOP[$Date]['times'][$Time]['max'], $tmp[1]);
															$FOP[$Date]['max']=max($FOP[$Date]['max'], $tmp[1]);
														} else {
															$bl->Range=array($tmp[0],$tmp[0]);
															$FOP[$Date]['times'][$Time]['max']=max($FOP[$Date]['times'][$Time]['max'], $tmp[0]);
															$FOP[$Date]['max']=max($FOP[$Date]['max'], $tmp[0]);
														}

														$FOP[$Date]['times'][$Time]['targets'][]=$bl;
													}
												} else {
													// Get which session and distance is shot at this time...
													$Sql="select * from DistanceInformation where DiTournament={$_SESSION['TourId']} and DiDay='$Date' and DiStart='$Time'";
													$t=safe_r_sql($Sql);
													while($u=safe_fetch($t)) {
														$Sql="select distinct SesAth4Target, cast(substr(QuTargetNo,2) as unsigned) TargetNo, IFNULL(Td{$u->DiDistance},'.{$u->DiDistance}.') as Distance, TarDescr, TarDim, DiDay, DiStart, DiWarmStart from
															Entries
															inner join Qualifications on EnId=QuId
															inner join DistanceInformation on QuSession=DiSession and DiTournament={$_SESSION['TourId']} and DiDistance={$u->DiDistance} and DiDay='$Date' and DiStart='$Time'
															inner join Session on SesOrder=QuSession and SesType='{$Item->Type}' and SesTournament={$_SESSION['TourId']}
															left join TournamentDistances on concat(trim(EnDivision),trim(EnClass)) like TdClasses and EnTournament=TdTournament
															left join (select TfId, TarDescr, TfW{$u->DiDistance} as TarDim, TfTournament from TargetFaces inner join Targets on TfT{$u->DiDistance}=TarId) tf on TfTournament=EnTournament and TfId=EnTargetFace
															where EnTournament={$_SESSION['TourId']}
															order by TargetNo, Distance desc, TargetNo, TarDescr, TarDim";
														$v=safe_r_sql($Sql);
														$k="";
														$first=true;
														while($w=safe_fetch($v)) {
															if(empty($bl) or $k!="{$w->TarDescr} {$w->TarDim} {$w->Distance}") {
																if($k) {
																	$FOP[$Date]['times'][$Time]['targets'][]=$bl;
																}

																$bl=new TargetButt();
																$bl->Target=get_text($w->TarDescr)." $w->TarDim cm";
																$bl->Distance=$w->Distance;
																$bl->Event=get_text($Item->Type.'-Session', 'Tournament');
																$bl->ArcTarget=$w->SesAth4Target;
																$bl->Range=array($w->TargetNo, $w->TargetNo);
																if(empty($ColorAssignment["{$w->TarDescr} {$w->TarDim}"])) {
																	$ColorAssignment["{$w->TarDescr} {$w->TarDim}"]=$ColorArray[$ColorIndex];
																	$ColorIndex++;
																}
																$bl->Colour=$ColorAssignment["{$w->TarDescr} {$w->TarDim}"];

																if(!$FOP[$Date]['times'][$Time]['min']) $FOP[$Date]['times'][$Time]['min']=$w->TargetNo;
																if(!$FOP[$Date]['min']) $FOP[$Date]['min']=$w->TargetNo;
															} elseif($w->TargetNo == $bl->Range[1]+1) {
																// sequence is OK
																$bl->Range[1]=$w->TargetNo;
															} else {
																// starts another block because there is a "hole" in the target sequence
																$FOP[$Date]['times'][$Time]['targets'][]=$bl;
																$bl=new TargetButt();
																$bl->Target=get_text($w->TarDescr)." $w->TarDim cm";
																$bl->Distance=$w->Distance;
																$bl->Event=get_text($Item->Type.'-Session', 'Tournament');
																$bl->ArcTarget=$w->SesAth4Target;
																$bl->Range=array($w->TargetNo, $w->TargetNo);
																$bl->Colour=$ColorAssignment["{$w->TarDescr} {$w->TarDim}"];
															}
															$FOP[$Date]['times'][$Time]['min']=min($FOP[$Date]['times'][$Time]['min'], $w->TargetNo);
															$FOP[$Date]['min']=min($FOP[$Date]['min'], $w->TargetNo);
															$FOP[$Date]['times'][$Time]['max']=max($FOP[$Date]['times'][$Time]['max'], $w->TargetNo);
															$FOP[$Date]['max']=max($FOP[$Date]['max'], $w->TargetNo);

															$k="{$w->TarDescr} {$w->TarDim} {$w->Distance}";
														}
														if($k) {
															$FOP[$Date]['times'][$Time]['targets'][]=$bl;
														}
													}
												}
											}
											break;
										case 'I':
										case 'T':
											if($Item->Title and !in_array($Item->Title, $FOP[$Date]['times'][$Time]['text'])) {
												$FOP[$Date]['times'][$Time]['text'][]=strip_tags($Item->Title);
											}
// 											if($Item->Comments and !in_array($Item->Comments, $FOP[$Date]['times'][$Time]['text'])) {
// 												$FOP[$Date]['times'][$Time]['text'][]=strip_tags($Item->Comments);
// 											}

// 											if($Date=='2016-05-05' and $Time=='09:45') {
// 												debug_svela(($Done[$Date][$Time][$Item->Type]));
// 											}


// 											$FOP[$Date]['times'][$Time]['text'][array_search($Item->Text, $FOP[$Date]['times'][$Time]['text'])].=': '.$Item->Events;

											if(true or empty($Done[$Date][$Time][$Item->Type])) {
												$Done[$Date][$Time][$Item->Type]=true;
												$rows=array();

												// get the warmup targets first (will be overwritten by the real matches)...
												$MyQuery = "SELECT FwEvent ,
														FwTargets,
														FwOptions,
														UNIX_TIMESTAMP(FwDay) as SchDate,
														DATE_FORMAT(FwTime,'" . get_text('TimeFmt') . "') as SchTime,
														FwDay,
														FwTime, EvDistance, TarDescr, EvTargetSize, FsEvent
													FROM FinWarmup
													INNER JOIN Events ON FwEvent=EvCode AND FwTeamEvent=EvTeamEvent AND FwTournament=EvTournament
													left join Targets on EvFinalTargetType=TarId
													left join FinSchedule on FwTeamEvent=FsTeamEvent and FwEvent=FsEvent and FsTournament=FwTournament and FsScheduledDate='$Date' and FsScheduledTime='$Time'
													WHERE FwTournament=" . StrSafe_DB($_SESSION['TourId']) . "
														AND FwDay='$Date' and FwTime='$Time'
														and FwTargets!=''
													GROUP BY FwEvent
													ORDER BY FwTargets";
												$t = safe_r_sql($MyQuery);
												while($u=safe_fetch($t)) {
													foreach(explode(',', $u->FwTargets) as $range) {
														$tmp=explode('-', $range);
														if(count($tmp)>1) {
															foreach(range($tmp[0], $tmp[1]) as $tgt) {
																$rows[$tgt]['d']=$u->EvDistance;
																$rows[$tgt]['e']=$u->FwEvent;
																$rows[$tgt]['w']=1;
																$rows[$tgt]['ph']=($u->FwOptions ? $u->FwOptions : ($u->FsEvent ? get_text('Bye') : get_text('WarmUp', 'Tournament')));
																$rows[$tgt]['f']=get_text($u->TarDescr)." $u->EvTargetSize cm";
															}
														} else {
															$rows[$tmp[0]]['d']=$u->EvDistance;
															$rows[$tmp[0]]['e']=$u->FwEvent;
															$rows[$tmp[0]]['w']=1;
															$rows[$tmp[0]]['ph']=($u->FwOptions ? $u->FwOptions : ($u->FsEvent ? get_text('Bye') : get_text('WarmUp', 'Tournament')));
															$rows[$tmp[0]]['f']=get_text($u->TarDescr)." $u->EvTargetSize cm";
														}
													}
												}

												// Now get the targets with the matches
												$MyQuery = "SELECT '' as Warmup, FSEvent, FSTeamEvent, GrPhase, FsMatchNo, FsTarget, '' as TargetTo, EvMatchArrowsNo, EvMatchMode, EvMixedTeam, EvTeamEvent, UNIX_TIMESTAMP(FSScheduledDate) as SchDate, DATE_FORMAT(FSScheduledTime,'" . get_text('TimeFmt') . "') as SchTime, EvFinalFirstPhase,
														@bit:=pow(2, ceil(log2(GrPhase))+1) & EvMatchArrowsNo,
														IF(@bit=0,EvFinEnds,EvElimEnds) AS `ends`,
														IF(@bit=0,EvFinArrows,EvElimArrows) AS `arrows`,
														IF(@bit=0,EvFinSO,EvElimSO) AS `so`,
														if(EvTeamEvent=1, EvMaxTeamPerson, if(right(FsLetter,1)='A' or right(FsLetter,1)='B', 2, 1)) as Persons,
														FSScheduledDate,
														FSScheduledTime, EvDistance, TarDescr, EvTargetSize
													FROM FinSchedule
													INNER JOIN Grids ON FSMatchNo=GrMatchNo
													INNER JOIN Events ON FSEvent=EvCode AND FSTeamEvent=EvTeamEvent AND FSTournament=EvTournament
													left join Targets on EvFinalTargetType=TarId
													WHERE FSTournament=" . StrSafe_DB($_SESSION['TourId']) . "
														AND FSScheduledDate='$Date' and FSScheduledTime='$Time'
														and FsTarget!=''
														AND GrPhase<=if(EvFinalFirstPhase=24, 32, if(EvFinalFirstPhase=48, 64, EvFinalFirstPhase))
														ORDER BY Warmup ASC, FSTarget ASC, FSMatchNo ASC";
												$t = safe_r_sql($MyQuery);
												while($u=safe_fetch($t)) {
													if(!in_array("$u->ends ends of $u->arrows arrows", $FOP[$Date]['times'][$Time]['text'])) {
														$FOP[$Date]['times'][$Time]['text'][]="$u->ends ends of $u->arrows arrows";
													}
													if(empty($ColorAssignment["{$u->EvDistance}-{$u->FSEvent}"])) {
														$ColorAssignment["{$u->EvDistance}-{$u->FSEvent}"]=$ColorArray[$ColorIndex];
														$ColorIndex++;
													}
													if($u->EvFinalFirstPhase==24 or $u->EvFinalFirstPhase==48) {
														if($u->GrPhase==32) $u->GrPhase=24;
														elseif($u->GrPhase==64) $u->GrPhase=48;
													}

													$u->FsTarget=intval($u->FsTarget);
													$rows[$u->FsTarget]['d']=$u->EvDistance;
													$rows[$u->FsTarget]['e']=$u->FSEvent;
													$rows[$u->FsTarget]['c']=$ColorAssignment["{$u->EvDistance}-{$u->FSEvent}"];
													$rows[$u->FsTarget]['f']=get_text($u->TarDescr)." $u->EvTargetSize cm";
													$rows[$u->FsTarget]['p']=$u->Persons;
													$rows[$u->FsTarget]['w']=0;
													$rows[$u->FsTarget]['ph']=get_text($u->GrPhase . '_Phase');
												}

												// $rows is now containing all targets
												ksort($rows);
												$k='';
												foreach($rows as $tgt => $def) {
													if(empty($bl) or $k!="{$def['d']}-{$def['e']}-{$def['w']}-{$def['ph']}") {
														if($k) {
															$FOP[$Date]['times'][$Time]['targets'][]=$bl;
														}

														$bl=new TargetButt();
														$bl->Target=$def['f'];
														$bl->Event=$def['e'];
														$bl->Distance=$def['d'];
														$bl->Range=array($tgt, $tgt);
														if(!empty($def['c'])) $bl->Colour=$def['c'];
														if(!empty($def['p'])) $bl->ArcTarget=$def['p'];
														if(!empty($def['ph'])) $bl->Phase=$def['ph'];

														if(!$FOP[$Date]['times'][$Time]['min']) $FOP[$Date]['times'][$Time]['min']=$tgt;
														if(!$FOP[$Date]['min']) $FOP[$Date]['min']=$tgt;
													} elseif($tgt == $bl->Range[1]+1) {
														// sequence is OK
														$bl->Range[1]=$tgt;
													} else {
														// starts another block because there is a "hole" in the target sequence
														$FOP[$Date]['times'][$Time]['targets'][]=$bl;
														$bl=new TargetButt();
														$bl->Target=$def['f'];
														$bl->Event=$def['e'];
														$bl->Distance=$def['d'];
														$bl->Range=array($tgt, $tgt);
														if(!empty($def['c'])) $bl->Colour=$def['c'];
														if(!empty($def['p'])) $bl->ArcTarget=$def['p'];
														if(!empty($def['ph'])) $bl->Phase=$def['ph'];
													}
													$FOP[$Date]['times'][$Time]['min']=min($FOP[$Date]['times'][$Time]['min'], $tgt);
													$FOP[$Date]['min']=min($FOP[$Date]['min'], $tgt);
													$FOP[$Date]['times'][$Time]['max']=max($FOP[$Date]['times'][$Time]['max'], $tgt);
													$FOP[$Date]['max']=max($FOP[$Date]['max'], $tgt);

													$k="{$def['d']}-{$def['e']}-{$def['w']}-{$def['ph']}";
												}
												if($k) {
													$FOP[$Date]['times'][$Time]['targets'][]=$bl;
												}
											}
											break;
										case 'R':

											continue; // temporary put there...

											$lnk=$Item->Text.': '.$Item->Events;
											$row=array('', '', '', '', htmlspecialchars(strip_tags($lnk)));
											if($Item->Shift and $timing[1]) {
												$row[0]=$timing[0];
											}
											$row[1]=$timing[1];
											$row[2]=$timing[2];
											$row[3]=$timing[3];
											$timing[3]='';
											$timing[2]='';
											$timing[1]='';
											$this->Ods->setCellStyle('Duration', null, 3);
											$this->Ods->addRow($row);
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
													$this->Ods->addCell($r->LeftSide.' - '.$r->RightSide);
												}
											}
											break;
										default:
// 											debug_svela($Item);
									}

								} else {
									if($Item->Comments) {
										$lnk=$Item->Comments;
										if(!in_array($Item->Comments, $FOP[$Date]['times'][$Time]['text'])) {
											$FOP[$Date]['times'][$Time]['text'][]=strip_tags($Item->Comments);
										}
									} else {
										switch($Item->Type) {
											case 'I':
											case 'T':
												$lnk=$Item->Text.': '.$Item->Events.' '.get_text('WarmUp', 'Tournament');
												break;
											default:
												$lnk=' '.get_text('WarmUp', 'Tournament');
										}
										if(!in_array($lnk, $FOP[$Date]['times'][$Time]['text'])) {
											$FOP[$Date]['times'][$Time]['text'][]=strip_tags($lnk);
										}
									}

									$IsTitle=false;

									if(empty($Done[$Date][$Time][$Item->Type])) {
// 										$Done[$Date][$Time][$Item->Type]=true;
										$MaxTgt=0;
										$rows=array();
										switch($Item->Type) {
											case 'Q':
												if($Item->Target) {
													// USES THIS ONE!!!
													foreach(explode(',', $Item->Target) as $Block) {
														$tmp= explode('@', $Block);
														$bl=new TargetButt();
														$Range=$tmp[0];
														$bl->Distance=$tmp[1];
														if(!empty($tmp[2])) $bl->Event=$tmp[2];
														if(!empty($tmp[3])) $bl->Target=$tmp[3];

// 														if(empty($ColorAssignment["{$bl->Distance}-{$bl->Event}"])) {
// 															$ColorAssignment["{$bl->Distance}-{$bl->Event}"]=$ColorArray[$ColorIndex];
// 															$ColorIndex++;
// 														}
// 														$bl->Colour=$ColorAssignment["{$bl->Distance}-{$bl->Event}"];

														$tmp=explode('-', $Range);
														if(!$FOP[$Date]['times'][$Time]['min']) $FOP[$Date]['times'][$Time]['min']=$tmp[0];
														if(!$FOP[$Date]['min']) $FOP[$Date]['min']=$tmp[0];
														$FOP[$Date]['times'][$Time]['min']=min($FOP[$Date]['times'][$Time]['min'], $tmp[0]);
														$FOP[$Date]['min']=min($FOP[$Date]['min'], $tmp[0]);
														if(count($tmp)>1) {
															$bl->Range=array($tmp[0], $tmp[1]);
															$FOP[$Date]['times'][$Time]['max']=max($FOP[$Date]['times'][$Time]['max'], $tmp[1]);
															$FOP[$Date]['max']=max($FOP[$Date]['max'], $tmp[1]);
														} else {
															$bl->Range=array($tmp[0],$tmp[0]);
															$FOP[$Date]['times'][$Time]['max']=max($FOP[$Date]['times'][$Time]['max'], $tmp[0]);
															$FOP[$Date]['max']=max($FOP[$Date]['max'], $tmp[0]);
														}

														$FOP[$Date]['times'][$Time]['targets'][]=$bl;
													}
												} else {
													$Sql="select * from DistanceInformation where DiTournament={$_SESSION['TourId']} and DiDay='$Date' and DiWarmStart='$Time'";
													$t=safe_r_sql($Sql);
													while($u=safe_fetch($t)) {
														$Sql="select distinct SesAth4Target, cast(substr(QuTargetNo,2) as unsigned) TargetNo, IFNULL(Td{$u->DiDistance},'.{$u->DiDistance}.') as Distance, TarDescr, TarDim, DiDay, DiStart, DiWarmStart from
															Entries
															inner join Qualifications on EnId=QuId
															inner join DistanceInformation on QuSession=DiSession and DiTournament={$_SESSION['TourId']} and DiDistance={$u->DiDistance} and DiDay='$Date' and DiWarmStart='$Time'
															inner join Session on SesOrder=QuSession and SesType='{$Item->Type}' and SesTournament={$_SESSION['TourId']}
															left join TournamentDistances on concat(trim(EnDivision),trim(EnClass)) like TdClasses and EnTournament=TdTournament
															left join (select TfId, TarDescr, TfW{$u->DiDistance} as TarDim, TfTournament from TargetFaces inner join Targets on TfT{$u->DiDistance}=TarId) tf on TfTournament=EnTournament and TfId=EnTargetFace
															where EnTournament={$_SESSION['TourId']}
															order by TargetNo, Distance desc, TargetNo, TarDescr, TarDim";
														$v=safe_r_sql($Sql);
														$k="";
														$first=true;
														while($w=safe_fetch($v)) {
															if(empty($bl) or $k!="{$w->TarDescr} {$w->TarDim} {$w->Distance}") {
																if($k) {
																	$FOP[$Date]['times'][$Time]['targets'][]=$bl;
																}

																$bl=new TargetButt();
																$bl->Target=get_text($w->TarDescr)." $w->TarDim cm";
																$bl->Distance=$w->Distance;
																$bl->Event=get_text('WarmUp', 'Tournament');
																$bl->ArcTarget=$w->SesAth4Target;
																$bl->Range=array($w->TargetNo, $w->TargetNo);

																if(!$FOP[$Date]['times'][$Time]['min']) $FOP[$Date]['times'][$Time]['min']=$w->TargetNo;
																if(!$FOP[$Date]['min']) $FOP[$Date]['min']=$w->TargetNo;
															} elseif($w->TargetNo == $bl->Range[1]+1) {
																// sequence is OK
																$bl->Range[1]=$w->TargetNo;
															} else {
																// starts another block because there is a "hole" in the target sequence
																$FOP[$Date]['times'][$Time]['targets'][]=$bl;
																$bl=new TargetButt();
																$bl->Target=get_text($w->TarDescr)." $w->TarDim cm";
																$bl->Distance=$w->Distance;
																$bl->Event=get_text('WarmUp', 'Tournament');
																$bl->ArcTarget=$w->SesAth4Target;
																$bl->Range=array($w->TargetNo, $w->TargetNo);
															}
															$FOP[$Date]['times'][$Time]['min']=min($FOP[$Date]['times'][$Time]['min'], $w->TargetNo);
															$FOP[$Date]['min']=min($FOP[$Date]['min'], $w->TargetNo);
															$FOP[$Date]['times'][$Time]['max']=max($FOP[$Date]['times'][$Time]['max'], $w->TargetNo);
															$FOP[$Date]['max']=max($FOP[$Date]['max'], $w->TargetNo);

															$k="{$w->TarDescr} {$w->TarDim} {$w->Distance}";
														}
														if($k) {
															$FOP[$Date]['times'][$Time]['targets'][]=$bl;
														}
													}
												}
												break;
											case 'I':
											case 'T':

												// get the warmup targets first (will be overwritten by the real matches...
												$MyQuery = "SELECT FwEvent ,
														FwTargets,
														FwOptions,
														UNIX_TIMESTAMP(FwDay) as SchDate,
														DATE_FORMAT(FwTime,'" . get_text('TimeFmt') . "') as SchTime,
														FwDay,
														FwTime, EvDistance, TarDescr, EvTargetSize
													FROM FinWarmup
													INNER JOIN Events ON FwEvent=EvCode AND FwTeamEvent=EvTeamEvent AND FwTournament=EvTournament
													left join Targets on EvFinalTargetType=TarId
													WHERE FwTournament=" . StrSafe_DB($_SESSION['TourId']) . "
															AND date_format(FwDay, '%Y-%m-%d')='$Date' and FwTime='$Time'
															and FwTargets!=''
															ORDER BY FwTargets";
												$t = safe_r_sql($MyQuery);

												while($u=safe_fetch($t)) {
													foreach(explode(',', $u->FwTargets) as $range) {
														$tmp=explode('-', $range);
														if(count($tmp)>1) {
															foreach(range($tmp[0], $tmp[1]) as $tgt) {
																$rows[$tgt]['d']=$u->EvDistance;
																$rows[$tgt]['e']=$u->FwEvent;
																$rows[$tgt]['f']=get_text($u->TarDescr)." $u->EvTargetSize cm";
// 																$rows[$tgt]['ph']=($u->FwOptions ? substr($u->FwOptions, 0, 30) : get_text('WarmUp', 'Tournament'));
																$rows[$tgt]['ph']=get_text('WarmUp', 'Tournament');
															}
														} else {
															$rows[$tmp[0]]['d']=$u->EvDistance;
															$rows[$tmp[0]]['e']=$u->FwEvent;
															$rows[$tmp[0]]['f']=get_text($u->TarDescr)." $u->EvTargetSize cm";
// 															$rows[$tmp[0]]['ph']=($u->FwOptions ? substr($u->FwOptions, 0, 30) : get_text('WarmUp', 'Tournament'));
															$rows[$tmp[0]]['ph']=get_text('WarmUp', 'Tournament');
														}
													}
												}


												ksort($rows);
												$k='';
												foreach($rows as $tgt => $def) {
													if(empty($bl) or $k!="{$def['d']}-{$def['e']}") {
														if($k) {
															$FOP[$Date]['times'][$Time]['targets'][]=$bl;
														}

														$bl=new TargetButt();
														$bl->Target=$def['f'];
														$bl->Event=$def['e'];
														$bl->Distance=$def['d'];
														$bl->Range=array($tgt, $tgt);
														if(!empty($def['c'])) $bl->Colour=$def['c'];
														if(!empty($def['p'])) $bl->ArcTarget=$def['p'];
														if(!empty($def['ph'])) $bl->Phase=$def['ph'];

														if(!$FOP[$Date]['times'][$Time]['min']) $FOP[$Date]['times'][$Time]['min']=$tgt;
														if(!$FOP[$Date]['min']) $FOP[$Date]['min']=$tgt;
													} elseif($tgt == $bl->Range[1]+1) {
														// sequence is OK
														$bl->Range[1]=$tgt;
													} else {
														// starts another block because there is a "hole" in the target sequence
														$FOP[$Date]['times'][$Time]['targets'][]=$bl;
														$bl=new TargetButt();
														$bl->Target=$def['f'];
														$bl->Event=$def['e'];
														$bl->Distance=$def['d'];
														$bl->Range=array($tgt, $tgt);
														if(!empty($def['c'])) $bl->Colour=$def['c'];
														if(!empty($def['p'])) $bl->ArcTarget=$def['p'];
														if(!empty($def['ph'])) $bl->Phase=$def['ph'];
													}
													$FOP[$Date]['times'][$Time]['min']=min($FOP[$Date]['times'][$Time]['min'], $tgt);
													$FOP[$Date]['min']=min($FOP[$Date]['min'], $tgt);
													$FOP[$Date]['times'][$Time]['max']=max($FOP[$Date]['times'][$Time]['max'], $tgt);
													$FOP[$Date]['max']=max($FOP[$Date]['max'], $tgt);

													$k="{$def['d']}-{$def['e']}";
												}
												if($k) {
													$FOP[$Date]['times'][$Time]['targets'][]=$bl;
												}
												break;
										}
									}
								}
							}
							$OldTime=$Time;
							$OldDate=$Date;
						}
					}
				}
			}
		}

// 		debug_svela($FOP);

		// Starts the real job...
		include_once('Common/pdf/ResultPDF.inc.php');

		$FirstPage=true;
		$DistHeight=4;
		$TgtHeight=3;
		$EventHeight=4;
		$PhaseHeight=4;
		$TgtFaceHeight=3;
		$ArcTgtHeight=2;
		$TimeHeight=6;
		$TimeWidth=20;

		foreach($FOP as $Day => $Blocks) {
			if(!$Blocks['min']) continue;
			$TwoColumns=false;
			if($FirstPage) {
				$pdf = new ResultPDF(get_text('FopSetup'), $Blocks['max']-$Blocks['min']<=32);
				$pdf->Version=$this->FopVersion;
				$pdf->SetCellPadding(0.1);
				$pdf->SetFillColor(200);
				$pdf->SetTextColor(0);
// 				$pdf->SetAutoPageBreak(false);
			} else {
				$pdf->AddPage($Blocks['max']-$Blocks['min']>32 ? 'L' : 'P');
			}
			$FirstPage=false;
			$FirstDate=true;

			// Title of the page is ALWAYS the date and the version
			$pdf->SetFont('', 'B', 25);
			$pdf->Cell(0, 0, formatTextDate($Day, true), 'B', 1, 'C');
			$pdf->dy(-5, true);
			$pdf->SetFontSize(7);
			$pdf->Cell(0, 0, $this->FopVersionText, '', 0, 'R');
			$pdf->SetFont('', '', 8);

			// calculates the width of the targets
			$TgtWidthOrg=min(7, ($pdf->getPageWidth()-21-$TimeWidth)/(1+$Blocks['max']-$Blocks['min']));
			$pdf->ln(6);

			$SecondColumn=0;

			if($Blocks['max']-$Blocks['min'] < 4) {
				// the "page" is split in two columns...
				$SecondColumn=20+(($pdf->getPageWidth()-30)/2);
			}

			$CurrentXOffset=0;
			$CurrentYOffset=0;
			$StartY=0;
			$MaxY=0;

			$LastBlock=end($Blocks['times']);

			foreach($Blocks['times'] as $Time => $Block) {
				if(!($CurrentXOffset%2) or !$SecondColumn) {
					if(!$pdf->SamePage(11 + $DistHeight + $TgtHeight + $EventHeight + $PhaseHeight + $TgtFaceHeight + $ArcTgtHeight)) {
						$pdf->AddPage();
						$FirstDate=true;
						$pdf->SetFont('', 'B', 16);
						$pdf->Cell(0, 0, formatTextDate($Day, true).' ('.get_text('Continue').')', 'B', 1, 'C');
						$pdf->dy(-4, true);
						$pdf->SetFontSize(7);
						$pdf->Cell(0, 0, $this->FopVersionText, '', 0, 'R');
						$pdf->SetFont('', '', 8);
						$pdf->ln(7);
						$MaxY=0;
					}
				}
				if(!$FirstDate and ($Block!=$LastBlock or !$SecondColumn)) {
					$pdf->setY($MaxY, false);
					$pdf->SetLineStyle(array('width'=>0.5, 'color' => array(128)));
					$tmp=$pdf->getMargins();
					$pdf->Line($tmp['left'], $pdf->getY(), $tmp['left'] + $pdf->getPageWidth() - $SecondColumn - 20, $pdf->getY());
					$pdf->SetLineStyle(array('width'=>.1, 'color' => array(0)));
					$pdf->ln(2);
				}
				$FirstDate=false;

				$Y=$pdf->gety();
				if($CurrentXOffset%2 and $SecondColumn) {
					$pdf->SetLeftMargin($SecondColumn);
					$pdf->sety($StartY, true);
					$Y=$pdf->gety();
				} else {
					$pdf->SetLeftMargin(10);
					$pdf->setx(10);
				}

				$CurrentXOffset++;
				$StartY=$Y;

				$pdf->SetFont('', 'B', 10);
				$pdf->Cell($TimeWidth, 0, $Block['time'], 0, 1);
				$pdf->SetFont('', '', 7);
				foreach($Block['text'] as $txt) {
					$txt=substr($txt, 0, 30);
					$pdf->Cell($TimeWidth, 3, $txt, '', 1);
				}
				$pdf->setY($Y);
				$MaxOffset=0;
				$pdf->SetFont('', '', 8);

				$TargetFacesBlocks=array();
				$CurFace='£$';

				$ArcPerTarget=array();
				$CurArcNum=-10;

// 			if($Day=='2015-07-17') debug_svela($Blocks);

// 				if((count($Block['targets'])==1 and $Block['targets'][0]->Range[1]-$Block['targets'][0]->Range[0]<3)) {
// 					$TgtWidth=2*$TgtWidthOrg;
// 					$Max=$Block['targets'][0]->Range[1];
// 					$Min=$Block['targets'][0]->Range[0];
// 				} else {
					$TgtWidth=$TgtWidthOrg;
					$Max=$Blocks['max'];
					$Min=$Blocks['min'];
// 				}

				$tmp=$pdf->getMargins();
				$pdf->setX($tmp['left']+1+$TimeWidth);
				$this->PrintTargetLinePdf($pdf, $TgtWidth, $TgtHeight, $Min, $Max);
				$pdf->ln();
				$OrgY=$pdf->GetY();

				$larCell=$TgtWidth/5;

				foreach($Block['targets'] as $Range) {
					$Y=$OrgY;
					$pdf->SetFillColor($Range->Colour[0], $Range->Colour[1], $Range->Colour[2]);
					$RangeWidth=(1+$Range->Range[1]-$Range->Range[0])*$TgtWidth;
					$RangeStart=$tmp['left']+1 + $TimeWidth + $TgtWidth*($Range->Range[0]-$Blocks['min']);
					$Offset=min(8, max(0, 14-(intval($Range->Distance)/5)));
					$MaxOffset=max($MaxOffset, $Offset);

					// prints the distance block
					$pdf->setXY($RangeStart, $OrgY);
					$pdf->Cell($RangeWidth, $DistHeight + $Offset, $Range->Distance, '1', 0, 'C');
					$Y+=$DistHeight + $Offset;

					// Events on each block
					$pdf->SetFont('', 'B');
					$pdf->setXY($RangeStart, $Y);
					$pdf->Cell($RangeWidth, $EventHeight, $Range->Event, 'LTR', 0, 'C', 1);
					$pdf->SetFont('', '');
					$Y+=$EventHeight;
					$pdf->setY($Y);

					// Phase on each block
					if($Range->Phase) {
						$pdf->SetFont('', 'B');
						$pdf->setXY($RangeStart, $Y);
						$pdf->Cell($RangeWidth, $PhaseHeight, $Range->Phase, 'LBR', 0, 'C', 1);
						$pdf->SetFont('', '');
						$Y+=$PhaseHeight;
					}

					if($Range->ArcTarget and $Range->ArcTarget<4) {
						foreach(range($Range->Range[0], $Range->Range[1]) as $tgt) {
							$colX=$tmp['left']+1 + $TimeWidth + $TgtWidth*($tgt-$Blocks['min']) ;
							$pdf->SetFillColor(255);
							$pdf->Rect($colX, $Y, $TgtWidth, $ArcTgtHeight, "DF");
							$pdf->SetFillColor(127);
							if($Range->ArcTarget & 1) {
								$pdf->Rect($colX + 2*$larCell, $Y + 0.5, $larCell, 1, "DF");
							}
							if($Range->ArcTarget & 2) {
								$pdf->Rect($colX + 1*$larCell, $Y + 0.5, $larCell, 1, "DF");
								$pdf->Rect($colX + 3*$larCell, $Y + 0.5, $larCell, 1, "DF");
							}
						}
						$Y+=$ArcTgtHeight;
						$GetArcPerTarget=false;
					} else {
						$GetArcPerTarget=true;
					}

					// Target faces used in the block
					if($CurFace!=$Range->Target) {
						$CurFace=$Range->Target;
						$TargetFacesBlocks[$CurFace][]=array($Range->Range[0], $Range->Range[1], $Y);
						$TargetIndex=count($TargetFacesBlocks[$CurFace])-1;
						$CurArcNum=-10;
					}
					if($Range->Range[0]<$TargetFacesBlocks[$CurFace][$TargetIndex][0]) $TargetFacesBlocks[$CurFace][$TargetIndex][0]=$Range->Range[0];
					if($Range->Range[1]>$TargetFacesBlocks[$CurFace][$TargetIndex][1]) $TargetFacesBlocks[$CurFace][$TargetIndex][1]=$Range->Range[1];
					$TargetFacesBlocks[$CurFace][$TargetIndex][2]=max($Y, $TargetFacesBlocks[$CurFace][$TargetIndex][2]);
					if($GetArcPerTarget) {
						if($CurArcNum!=$Range->ArcTarget) {
							$CurArcNum=$Range->ArcTarget;
							$ArcPerTarget[$CurArcNum][]=array($Range->Range[0], $Range->Range[1], $Y);
							$ArcPerTargetIndex=count($ArcPerTarget[$CurArcNum])-1;
						}
						if($Range->Range[0]<$ArcPerTarget[$CurArcNum][$ArcPerTargetIndex][0]) $ArcPerTarget[$CurArcNum][$ArcPerTargetIndex][0]=$Range->Range[0];
						if($Range->Range[1]>$ArcPerTarget[$CurArcNum][$ArcPerTargetIndex][1]) $ArcPerTarget[$CurArcNum][$ArcPerTargetIndex][1]=$Range->Range[1];
					}
				}
				$pdf->SetFontSize(7);
				$Gap=$pdf->getY();
				if(empty($Block['targets'])) $Gap=$pdf->gety()+10;
				foreach($TargetFacesBlocks as $Targetface => $Ranges) {
					if(!$Targetface) continue;
					foreach($Ranges as $Range) {
						$RangeWidth=(1+$Range[1]-$Range[0])*$TgtWidth;
						$RangeStart=$tmp['left'] + 1 + $TimeWidth + $TgtWidth*($Range[0]-$Blocks['min']);
						$pdf->setXY($RangeStart, $Range[2]);
						$pdf->Cell($RangeWidth, $TgtFaceHeight, $Targetface, 'LR', 1, 'C');
						$Gap=max($Gap, $pdf->gety());
					}
				}
				foreach($ArcPerTarget as $Targetface => $Ranges) {
					if(!$Targetface) continue;
					foreach($Ranges as $Range) {
						$RangeWidth=(1+$Range[1]-$Range[0])*$TgtWidth;
						$RangeStart=$tmp['left'] + 1 + $TimeWidth + $TgtWidth*($Range[0]-$Blocks['min']);
						$pdf->setXY($RangeStart, $Range[2] + $TgtFaceHeight);
						$pdf->Cell($RangeWidth, $ArcTgtHeight, $Targetface.' Arc/Tgt', 'LR', 1, 'C');
						$Gap=max($Gap, $pdf->gety());
					}
				}
				$pdf->SetFontSize(8);
				$pdf->SetY($Gap+3, true);
				$MaxY=max($MaxY, $pdf->getY());
// 				$pdf->ln();

// 				if($Time=='08:30') debug_svela($Block);
			}
		}
		$pdf->Output();
		die();
	}

	function PrintTargetLinePdf(&$pdf, $TgtWidth, $TgtHeight, $Min, $Max) {
		$pdf->SetFont('', '', 6);
		if($this->FopLocations) {
			$OldX=$pdf->getx();
			$OldY=$pdf->gety();
// 			debug_svela($this->FopLocations);
			foreach($this->FopLocations as $field) {
				if($field->Tg1<=$Max and $field->Tg2>=$Min) {
					$start=max($field->Tg1, $Min);
					$end=min($field->Tg2, $Max);
					$pdf->setx($OldX+($start-$Min)*$TgtWidth);
					$pdf->cell($TgtWidth*(1+$end-$start), $TgtHeight, $field->Loc, 'LR', 0, 'C');
				}
			}
			$pdf->setxy($OldX, $OldY+$TgtHeight);
		}
		foreach(range($Min, $Max) as $tgt) {
			$pdf->cell($TgtWidth, $TgtHeight, $tgt, 'LBR', 0, 'C');
		}
		$pdf->SetFont('', '', 8);
	}
}

function AddMinutes($Time, $Minutes) {
	if($Minutes==0) return $Time;
	$newtime=(substr($Time, 0, 2)*60)+substr($Time, 3, 2)+$Minutes;
	return sprintf('%02d:%02d', intval($newtime/60), $newtime%60);
}

Class TargetButt {
	var $Target='';
	var $Range=array(0, 0);
	var $Colour=array(200, 200, 200); // Warmup colour
	var $Event='';
	var $Distance='';
	var $ArcTarget=0;
	var $Phase='';
}