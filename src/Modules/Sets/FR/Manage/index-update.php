<?php

require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Fun_DateTime.inc.php');

$JSON=array(
	'error'=>1,
	'matches'=>array(),
	);

if(!CheckTourSession()
		or !isset($_REQUEST['event'])
		or !isset($_REQUEST['phase'])
		or !isset($_REQUEST['match'])
		or !isset($_REQUEST['item'])
		or !isset($_REQUEST['team'])
		or !isset($_REQUEST['val'])) {
	JsonOut($JSON);
}

$Event=$_REQUEST['event'];
$Phase=intval($_REQUEST['phase']);
$Matchno=intval($_REQUEST['match']);
$Team=intval($_REQUEST['team']);

$AllInOne=getModuleParameter('FFTA', 'D1AllInOne', 0);
$StartMatchNo=array(128, 144, 160, 176, 192);
if($_REQUEST['day']==3 and $_REQUEST['event']=='FCO' and !$AllInOne) {
	$StartMatchNo=array(128, 144, 160, 176);
}


switch($_REQUEST['item']) {
	case 'te':
		// updates the team, so we need to update all the entries in the sub-events and the team itself
		if(!$AllInOne) {
			$SQL="select * from Individuals 
	            inner join Entries on EnId=IndId and EnTournament=IndTournament and EnCountry=".intval($_REQUEST['team'])." and EnTeamFEvent=1
	            where IndEvent=".StrSafe_DB($_REQUEST['event'])." 
	            order by IndRank";
			$q=safe_r_sql($SQL);
			$i=1;
			while($r=safe_fetch($q)) {
				safe_w_sql("update Finals set FinAthlete=$r->EnId where FinEvent=".StrSafe_DB($_REQUEST['event'].$i)." and FinMatchNo=$Matchno and FinTournament={$_SESSION['TourId']}");
				$i++;
			}
		}
		safe_w_sql("update TeamFinals set TfTeam=$Team, TfSubTeam=0 where TfEvent=".StrSafe_DB($_REQUEST['event'])." and TfMatchNo=$Matchno and TfTournament={$_SESSION['TourId']}");
		break;
	case 'tg':
		// updates the target
		$Target=str_pad(intval($_REQUEST['val']), 3,'0', STR_PAD_LEFT);
		$Letter=$Target.(($Matchno%2) ? 'B' : 'A');
		if($Phase==0) {
			$TeamEvent=1;
		} else {
			$Event.=$Phase;
			$TeamEvent=0;
		}
		safe_w_sql("insert into FinSchedule set FsTarget=".StrSafe_DB($Target).", FsLetter=".StrSafe_DB($Letter).", FsTeamEvent=$TeamEvent, FsEvent=".StrSafe_DB($Event).", FsMatchNo=$Matchno, FsTournament={$_SESSION['TourId']} 
			on duplicate key update FsTarget=".StrSafe_DB($Target).", FsLetter=".StrSafe_DB($Letter));
		$JSON['matches'][]=array('ph'=>$Phase, 'id' => 'tg-'.$Matchno, 'val' => ltrim($TeamEvent ? $Target : $Letter, '0'));
		if(isset($_REQUEST['auto'])) {
			// it is the first one so repeat for the other positions
			if(in_array($Matchno, $StartMatchNo)) {
				// start of game, updates the following game
				$Changes=array();
				$tgt=intval($_REQUEST['val']);
				$IsTarget=true;
				for($i=$Matchno; $i<208; $i+=2) {
					if(strstr($Event, 'FCO')) {
						if($AllInOne and in_array($i, array(134, 150, 166, 182, 198))) {
							$IsTarget=false;
						} elseif(!$AllInOne and in_array($i, array(136, 152, 168, 184, 200))) {
							$IsTarget=false;
						} elseif(in_array($i, $StartMatchNo)) {
							$IsTarget=true;
						}
					}
					if(in_array($i, $StartMatchNo)) {
						$tgt=intval($_REQUEST['val']);
					}
					if($IsTarget) {
						$tgt1=str_pad($tgt, 3,'0', STR_PAD_LEFT);
						$tgt2=str_pad($tgt+1, 3,'0', STR_PAD_LEFT);
					} else {
						$tgt1='';
						$tgt2='';
					}
					$Changes[]=array(0, '1', $i, $tgt1, 'A');
					$Changes[]=array(0, '1', $i+1, $tgt1, 'B');
					$Changes[]=array(0, '2', $i, $tgt2, 'A');
					$Changes[]=array(0, '2', $i+1, $tgt2, 'B');
					$Changes[]=array(0, '3', $i, $tgt1, 'C');
					$Changes[]=array(0, '3', $i+1, $tgt1, 'D');
					$Changes[]=array(0, '4', $i, $tgt2, 'C');
					$Changes[]=array(0, '4', $i+1, $tgt2, 'D');
					$Changes[]=array(1, '', $i, $tgt1, 'A');
					$Changes[]=array(1, '', $i+1, $tgt2, 'B');
					$tgt+=2;
				}
			} else {
				$Changes=array(
					array(0, '1', $Matchno+1, $Target, 'B'),
					array(0, '3', $Matchno, $Target, 'C'),
					array(0, '3', $Matchno+1, $Target, 'D'),
					array(1, '', $Matchno, $Target, 'A'),
				);
				$Target2=str_pad(intval($_REQUEST['val'])+1, 3,'0', STR_PAD_LEFT);
				$Changes[]=array(0, '2', $Matchno, $Target2, 'A');
				$Changes[]=array(0, '2', $Matchno+1, $Target2, 'B');
				$Changes[]=array(0, '4', $Matchno, $Target2, 'C');
				$Changes[]=array(0, '4', $Matchno+1, $Target2, 'D');
				$Changes[]=array(1, '', $Matchno+1, $Target2, 'B');
			}

			foreach($Changes as $k) {
				safe_w_sql("insert into FinSchedule set FsTarget=".StrSafe_DB($k[3]).", FsLetter=".StrSafe_DB($k[3].$k[4]).", FsTeamEvent=$k[0], FsEvent=".StrSafe_DB($_REQUEST['event'].$k[1]).", FsMatchNo=".($k[2]).", FsTournament={$_SESSION['TourId']} 
					on duplicate key update FsTarget=".StrSafe_DB($k[3]).", FsLetter=".StrSafe_DB($k[3].$k[4]));
				$JSON['matches'][]=array('ph'=>intval($k[1]), 'id' => 'tg-'.($k[2]), 'val' => ltrim($k[3].($k[0] ? '' : $k[4]), '0'));
			}
		}
		break;
	case 'da':
		$date='';
		if($_REQUEST['val'][0]=='d') {
			$offset=substr($_REQUEST['val'],1);
			$q=safe_r_sql("select date_add(ToWhenFrom, interval $offset day) as NewDate from Tournament where ToId={$_SESSION['TourId']}");
			if($r=safe_fetch($q)) {
				$date=$r->NewDate;
			}
		} else {
			$date=CleanDate($_REQUEST['val']);
		}
		if($Phase==0) {
			$TeamEvent=1;
		} else {
			$Event.=$Phase;
			$TeamEvent=0;
		}
		safe_w_sql("insert into FinSchedule set FSScheduledDate='$date', FsTeamEvent=$TeamEvent, FsEvent=".StrSafe_DB($Event).", FsMatchNo=$Matchno, FsTournament={$_SESSION['TourId']} 
			on duplicate key update FSScheduledDate='$date'");
		safe_w_sql("insert into FinSchedule set FSScheduledDate='$date', FsTeamEvent=$TeamEvent, FsEvent=".StrSafe_DB($Event).", FsMatchNo=".($Matchno%2 ? $Matchno-1 : $Matchno+1).", FsTournament={$_SESSION['TourId']} 
			on duplicate key update FSScheduledDate='$date'");
		$JSON['matches'][]=array('ph'=>$Phase, 'id' => 'da-'.$Matchno, 'val' => $date);
		$JSON['matches'][]=array('ph'=>$Phase, 'id' => 'da-'.($Matchno+1), 'val' => $date);
		if(isset($_REQUEST['auto'])) {
			/// creates/updates all other days of this game
			if(in_array($Matchno, $StartMatchNo)) {
				$Changes=array();
				for($i=$Matchno; $i<208; $i++) {
					$Changes[]=array(1, '', $i, $date);
					if(!$AllInOne) {
						$Changes[]=array(0, '1', $i, $date);
						$Changes[]=array(0, '2', $i, $date);
						$Changes[]=array(0, '3', $i, $date);
						$Changes[]=array(0, '4', $i, $date);
					}
				}
			} else {
				$Changes=array(
					array(1, '',  $Matchno, $date),
					array(1, '',  $Matchno+1, $date),
					array(0, '1', $Matchno+1, $date),
					array(0, '3', $Matchno, $date),
					array(0, '3', $Matchno+1, $date),
					array(0, '2', $Matchno, $date),
					array(0, '2', $Matchno+1, $date),
					array(0, '4', $Matchno, $date),
					array(0, '4', $Matchno+1, $date),
				);
				if($AllInOne) {
					// keeps only the first 2
					$Changes=array_slice($Changes,0,2);
				}

			}
			foreach($Changes as $k) {
				safe_w_sql("insert into FinSchedule set FSScheduledDate='$k[3]', FsTeamEvent=$k[0], FsEvent=".StrSafe_DB($_REQUEST['event'].$k[1]).", FsMatchNo=".($k[2]).", FsTournament={$_SESSION['TourId']} 
					on duplicate key update FSScheduledDate=".StrSafe_DB($k[3]));
				$JSON['matches'][]=array('ph'=>intval($k[1]), 'id' => 'da-'.($k[2]), 'val' => $k[3]);
			}
		}
		break;
	case 'ti':
		$time=explode(':', $_REQUEST['val']);
		while(count($time)<2) {
			$time[]='00';
		}
		if(count($time)>2) {
			$time=array_slice($time, 0, 2);
		}
		$time=implode(':', $time);
		if($Phase==0) {
			$TeamEvent=1;
			$Duration=getModuleParameter('FFTA', 'DefaultMatchTeam', 30);
		} else {
			$Event.=$Phase;
			$TeamEvent=0;
			$Duration=getModuleParameter('FFTA', 'DefaultMatchIndividual', 40);
		}
		$time1=date('H:i', strtotime("$time"));
		safe_w_sql("insert into FinSchedule set FSScheduledTime='$time1:00', FSScheduledLen=$Duration, FsTeamEvent=$TeamEvent, FsEvent=".StrSafe_DB($Event).", FsMatchNo=$Matchno, FsTournament={$_SESSION['TourId']} 
			on duplicate key update FSScheduledTime='$time1:00', FSScheduledLen=$Duration");
		if(!$AllInOne) {
			safe_w_sql("insert into FinSchedule set FSScheduledTime='$time1:00', FSScheduledLen=$Duration, FsTeamEvent=$TeamEvent, FsEvent=" . StrSafe_DB($Event) . ", FsMatchNo=" . ($Matchno % 2 ? $Matchno - 1 : $Matchno + 1) . ", FsTournament={$_SESSION['TourId']} 
				on duplicate key update FSScheduledTime='$time1:00', FSScheduledLen=$Duration");
		}
		$JSON['matches'][]=array('ph'=>$Phase, 'id' => 'ti-'.$Matchno, 'val' => $time1);
		$JSON['matches'][]=array('ph'=>$Phase, 'id' => 'ti-'.($Matchno+1), 'val' => $time1);
		if(isset($_REQUEST['auto'])) {
			$DurationTeam = getModuleParameter('FFTA', 'DefaultMatchTeam', 30);
			$DurationInd  = getModuleParameter('FFTA', 'DefaultMatchIndividual', 40);
			/// creates/updates all other days of this game
			if(in_array($Matchno, $StartMatchNo)) {
				$Changes=array();
				for($i=$Matchno; $i<208; $i++) {
					if($i!=$Matchno and in_array($i, $StartMatchNo)) {
						$time=date('H:i', strtotime("$time + ".($DurationTeam + $DurationInd)." minutes"));
					}
					$time1=date('H:i', strtotime("$time"));
					$time2=date('H:i', strtotime("$time + $DurationTeam minutes"));
					$time3=date('H:i', strtotime("$time + $DurationTeam minutes"));

					$Changes[]=array(1, '', $i, $time1, $DurationTeam);
					if(!$AllInOne) {
						// adds the individuals
						$Changes[]=array(0, '1', $i, $time2, $DurationInd);
						$Changes[]=array(0, '2', $i, $time2, $DurationInd);
						$Changes[]=array(0, '3', $i, $time3, $DurationInd);
						$Changes[]=array(0, '4', $i, $time3, $DurationInd);
					}


				}
			} else {
				$time1=date('H:i', strtotime("$time"));
				$time2=date('H:i', strtotime("$time + $DurationTeam minutes"));
				$time3=date('H:i', strtotime("$time + $DurationTeam minutes"));
				$Changes=array(
					array(1, '',  $Matchno, $time1, $DurationTeam),
					array(1, '',  $Matchno+1, $time1, $DurationTeam),
					array(0, '1', $Matchno, $time2, $DurationInd),
					array(0, '1', $Matchno+1, $time2, $DurationInd),
					array(0, '2', $Matchno, $time2, $DurationInd),
					array(0, '2', $Matchno+1, $time2, $DurationInd),
					array(0, '3', $Matchno, $time3, $DurationInd),
					array(0, '3', $Matchno+1, $time3, $DurationInd),
					array(0, '4', $Matchno, $time3, $DurationInd),
					array(0, '4', $Matchno+1, $time3, $DurationInd),
				);
				if($AllInOne) {
					// keeps only the first 2
					$Changes=array_slice($Changes,0,2);
				}
			}
			foreach($Changes as $k) {
				safe_w_sql("insert into FinSchedule set FSScheduledTime='$k[3]:00', FSScheduledLen=$k[4], FsTeamEvent=$k[0], FsEvent=".StrSafe_DB($_REQUEST['event'].$k[1]).", FsMatchNo=".($k[2]).", FsTournament={$_SESSION['TourId']} 
					on duplicate key update FSScheduledTime='$k[3]:00', FSScheduledLen=$k[4]");
				$JSON['matches'][]=array('ph'=>intval($k[1]), 'id' => 'ti-'.($k[2]), 'val' => $k[3]);
			}
		}
		break;
	default:
		debug_svela($_REQUEST);
}

$JSON['error']=0;

JsonOut($JSON);
