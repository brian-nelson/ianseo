<?php

require_once(dirname(__FILE__) . '/config.php');

$JSON=array('error'=>1, 'reload'=> 0);

if(!isset($_REQUEST['pos']) or !isset($_REQUEST['cat']) or empty($_REQUEST['item'])) {
	JsonOut($JSON);
}

switch($_REQUEST['item']) {
	case 'ALLONE':
		setModuleParameter('FFTA', 'D1AllInOne', intval($_REQUEST['club']));
		if($_REQUEST['club']) {
			// all matches are made only with teams, so no bonus and no individual, FCO=6 teams...
			setModuleParameter('FFTA', 'DefaultMatchIndividual', 0);
			$Winners=getModuleParameter('FFTA', 'D1Winners');
			$ClubsToRemove=array();
			// removes from the table
			if(!empty($Winners['FCO']['7'])) {
				$ClubsToRemove[]=$Winners['FCO']['7'];
			}
			if(!empty($Winners['FCO']['8'])) {
				$ClubsToRemove[]=$Winners['FCO']['8'];
			}
			$Modules=array();
			foreach(array('DefaultMatchIndividual', 'DefaultMatchTeam', 'D1Bonus', 'D1Winners', 'D1AllInOne') as $type) {
				$tmp=getModuleParameter('FFTA', $type);
				// reset bonus...
				if($type=='D1Bonus') {
					foreach($tmp as $k1 => &$v1) {
						foreach($v1 as $k2 => &$v2) {
							$v2=0;
						}
					}
				}
				if(isset($tmp['FCO']) and is_array($tmp['FCO'])) {
					$tmp['FCO']=array_slice($tmp['FCO'],0,6, true);
				}
				$Modules[$type]=$tmp;
			}

			require_once('Qualification/Fun_Qualification.local.inc.php');
			if($Connected=getModuleParameter('FFTA', 'ConnectedCompetitions')) {
				foreach($Connected as $tmp) {
					if($CompId=getIdFromCode($tmp)) {
						// reset to 6 the numbers of FCO
						safe_w_sql("update Events set EvNumQualified=6 where EvTournament=$CompId and EvTeamEvent=1 and EvCode='FCO'");
						// removes all the individual events
						safe_w_sql("delete from Finals where FinTournament=$CompId");
						safe_w_sql("delete from Events where EvTeamEvent=0 and EvTournament=$CompId");
						safe_w_sql("delete from FinSchedule where FsTeamEvent=0 and FsTournament=$CompId");
						safe_w_sql("update Qualifications inner join Entries on EnId=QuId and EnTournament=$CompId set QuHits=1");
						if($ClubsToRemove) {
							safe_w_sql("delete from TeamDavis where TeDaTournament=$CompId and TeDaEvent='FCO' and TeDaTeam in ('".implode("','", $ClubsToRemove)."')");
						}
						foreach(array('DefaultMatchIndividual', 'DefaultMatchTeam', 'D1Bonus', 'D1Winners', 'D1AllInOne') as $type) {
							setModuleParameter('FFTA', $type, $Modules[$type], $CompId);
						}
						// recreates the teams
						MakeTeams(NULL, NULL, $CompId);
						MakeTeamsAbs(null,null,null, $CompId);
					}
				}
			} else {
				// reset to 6 the numbers of FCO
				safe_w_sql("update Events set EvNumQualified=6 where EvTournament={$_SESSION['TourId']} and EvTeamEvent=1 and EvCode='FCO'");
				// removes all the individual events
				safe_w_sql("delete from Finals where FinTournament={$_SESSION['TourId']}");
				safe_w_sql("delete from Events where EvTeamEvent=0 and EvTournament={$_SESSION['TourId']}");
				safe_w_sql("delete from FinSchedule where FsTeamEvent=0 and FsTournament={$_SESSION['TourId']}");
				if($ClubsToRemove) {
					safe_w_sql("delete from TeamDavis where TeDaTournament={$_SESSION['TourId']} and TeDaEvent='FCO' and TeDaTeam in ('".implode("','", $ClubsToRemove)."')");
				}
				// recreates the teams
				MakeTeams();
				MakeTeamsAbs();
			}
			// sets all qual arrows to be shot

			set_qual_session_flags();
		}
		$JSON['reload']=1;
		$JSON['error']=0;
		break;
	case 'DEFIND':
		setModuleParameter('FFTA', 'DefaultMatchIndividual', intval($_REQUEST['club']));
		$JSON['error']=0;
		break;
	case 'DEFTEAM':
		setModuleParameter('FFTA', 'DefaultMatchTeam', intval($_REQUEST['club']));
		$JSON['error']=0;
		break;
	case 'BONUS':
		$Bonus=getModuleParameter('FFTA', 'D1Bonus');

		if(isset($Bonus[$_REQUEST['cat']][$_REQUEST['pos']])) {
			$Bonus[$_REQUEST['cat']][$_REQUEST['pos']]=intval($_REQUEST['club']);
			setModuleParameter('FFTA', 'D1Bonus', $Bonus);

			$JSON['error']=0;
		}
		break;
	case 'CLUB':
		$Winners=getModuleParameter('FFTA', 'D1Winners');

		if(isset($Winners[$_REQUEST['cat']][$_REQUEST['pos']])) {
			$Winners[$_REQUEST['cat']][$_REQUEST['pos']]=$_REQUEST['club'];
			setModuleParameter('FFTA', 'D1Winners', $Winners);

			$q=safe_r_sql("select distinct CoId, LueCountry, LueCoDescr, LueCoShort from LookUpEntries left join Countries on CoCode=LueCountry and CoTournament={$_SESSION['TourId']} where LueIocCode like 'FRA%' and LueCountry=".StrSafe_DB($_REQUEST['club']));
			if($r=safe_fetch($q)) {
				if(!$r->CoId) {
					safe_w_sql("insert into Countries set CoCode=".StrSafe_DB($r->LueCountry).", CoName=".StrSafe_DB($r->LueCoShort).", CoNameComplete=".StrSafe_DB($r->LueCoDescr).", CoTournament={$_SESSION['TourId']}");
				}
			}

			$JSON['error']=0;
		}
		break;
	case 'CONNECTED':
		$Comps=array();
		foreach(preg_split('/[ ,;+]+/', $_REQUEST['club']) as $c) {
			if($c=trim($c)) {
				$Comps[]=$c;
			}
		}

		setModuleParameter('FFTA', 'ConnectedCompetitions', $Comps);
		$JSON['error']=0;

		// check if we have some more defaults already set!
		if($Comps) {
			// check if this parameter is already present in the first competition set
			$CompId=getIdFromCode($Comps[0]);
			foreach(array('DefaultMatchIndividual', 'DefaultMatchTeam', 'D1Bonus', 'D1Winners', 'D1AllInOne') as $type) {
				if($valOld=getModuleParameter('FFTA', $type, '', $CompId)) {
					setModuleParameter('FFTA', $type, $valOld);
					$JSON['reload']=1;
				}
			}
		}

		break;
}

JsonOut($JSON);
