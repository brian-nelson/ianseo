<?php

require_once(dirname(__FILE__) . '/config.php');

$JSON=array('error'=>1, 'reload'=> 0);

if(!isset($_REQUEST['pos']) or !isset($_REQUEST['cat']) or empty($_REQUEST['item'])) {
	JsonOut($JSON);
}

switch($_REQUEST['item']) {
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
			foreach(array('DefaultMatchIndividual', 'DefaultMatchTeam', 'D1Bonus', 'D1Winners') as $type) {
				if($valOld=getModuleParameter('FFTA', $type, '', $CompId)) {
					setModuleParameter('FFTA', $type, $valOld);
					$JSON['reload']=1;
				}
			}
		}

		break;
}

JsonOut($JSON);
