<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/Fun_Phases.inc.php');
require_once('Common/Lib/Fun_Modules.php');

CheckTourSession(true);

$JSON=array('error' => 1, 't' => array());

if(empty($_REQUEST['first'])) {
	JsonOut($JSON);
}

$TabIndexOffset=100;

foreach($_REQUEST['first'] as $Team => $Events) {
    checkACL(($Team ? AclTeams : AclIndividuals), AclReadWrite, false);
	foreach($Events as $Event => $Matches) {
		foreach($Matches as $Matchno => $Ends) {
			foreach($Ends as $End => $Start) {
				$rows=4;
				$cols=3;
				$so=1;
				$Sql1='';
				$Sql2='';
				$Params=getEventArrowsParams($Event, intval(log($Matchno, 2)), $Team);
				$TabIndex=100;
				if($Matchno%2) {
					$m=array($Matchno, $Matchno-1);
				} else {
					$m=array($Matchno, $Matchno+1);
				}
				if($Start=='y') {
					if($Team) {
						$Sql1="update TeamFinals set TfShootFirst=(TfShootFirst | ".pow(2, $End)."), TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " where TfTournament={$_SESSION['TourId']} and TfEvent='$Event' and TfMatchNo=$Matchno";
						$Sql2="update TeamFinals set TfShootFirst=(TfShootFirst & ~".pow(2, $End)."), TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " where TfTournament={$_SESSION['TourId']} and TfEvent='$Event' and TfMatchNo={$m[1]}";
						if($End==$Params->ends) {
							// we are setting the SO
							for($i=0; $i<3; $i++) {
								// alternate teams each component shoots 1 arrow, so we iterate for as many arrows per archer!
								for($j=0; $j < $Params->so; $j++) {
									$tabIndex=$TabIndexOffset + ($Params->ends*$Params->arrows*2) + ($i*$Params->so*2) + ($j*2) +1 ;

									$JSON['t'][]=array(
										'id' => 'Arrow['.$m[0].'][1]['.$i.']['.$j.']',
										'val' => $tabIndex,
									);
									$JSON['t'][]=array(
										'id' => 'Arrow['.$m[1].'][1]['.$i.']['.$j.']',
										'val' => $tabIndex+1,
									);
								}
							}
						} else {
							for($i=$End; $i<$Params->ends; $i++) {
								// alternate teams each component shoots 1 arrow, so we iterate for as many arrows per archer!
								for($j=0; $j < $Params->arrows; $j++) {
									$tabIndex=$TabIndexOffset + $i*$Params->arrows*2 + intval($j/$Params->MaxTeam)*$Params->arrows + $j%$Params->MaxTeam + 1;

									$JSON['t'][]=array(
										'id' => 'Arrow['.$m[0].'][0]['.$i.']['.$j.']',
										'val' => $tabIndex,
									);
									$JSON['t'][]=array(
										'id' => 'Arrow['.$m[1].'][0]['.$i.']['.$j.']',
										'val' => $tabIndex+$Params->MaxTeam,
									);
								}
							}
						}
					} else {
						$Sql1="update Finals set FinShootFirst=(FinShootFirst | ".pow(2, $End)."), FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . "  where FinTournament={$_SESSION['TourId']} and FinEvent='$Event' and FinMatchNo=$Matchno";
						$Sql2="update Finals set FinShootFirst=(FinShootFirst & ~".pow(2, $End)."), FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . "  where FinTournament={$_SESSION['TourId']} and FinEvent='$Event' and FinMatchNo={$m[1]}";
						if($End==$Params->ends) {
							// Setting SO
							for($i=0; $i<3; $i++) {
								// alternate teams each component shoots 1 arrow, so we iterate for as many arrows per archer!
								for($j=0; $j < $Params->so; $j++) {
									$tabIndex=$TabIndexOffset + $Params->ends*$Params->arrows*2 + $i*$Params->so*2 + $j + 1;

									$JSON['t'][]=array(
										'id' => 'Arrow['.$m[0].'][1]['.$i.']['.$j.']',
										'val' => $tabIndex,
									);
									$JSON['t'][]=array(
										'id' => 'Arrow['.$m[1].'][1]['.$i.']['.$j.']',
										'val' => $tabIndex+1,
									);
								}
							}
						} else {
							// setting regular arrows
							for($i=$End; $i<$Params->ends; $i++) {
								// alternate teams each component shoots 1 arrow, so we iterate for as many arrows per archer!
								for($j=0; $j < $Params->arrows; $j++) {
									$tabIndex=$TabIndexOffset + $i*$Params->arrows*2 + $j*2 + 1;

									$JSON['t'][]=array(
										'id' => 'Arrow['.$m[0].'][0]['.$i.']['.$j.']',
										'val' => $tabIndex,
									);
									$JSON['t'][]=array(
										'id' => 'Arrow['.$m[1].'][0]['.$i.']['.$j.']',
										'val' => $tabIndex+1,
									);
								}
							}
						}
					}
				} else {
					$Sql1="update Finals set FinShootFirst=FinShootFirst & ~".pow(2, $End)." where FinTournament={$_SESSION['TourId']} and FinEvent='$Event' and FinMatchNo=$Matchno";
					if($Team) $Sql1="update TeamFinals set TfShootFirst=TfShootFirst & ~".pow(2, $End)." where TfTournament={$_SESSION['TourId']} and TfEvent='$Event' and TfMatchNo=$Matchno";
				}
				safe_w_sql($Sql1);
				if($Sql2) safe_w_sql($Sql2);

				$JSON['error']=0;
			}
		}
	}
}

runJack("FinShootingFirst", $_SESSION['TourId'], array("Event"=>$Event ,"Team"=>$Team,"MatchNo"=>($Matchno % 2 ? $Matchno-1 : $Matchno) ,"TourId"=>$_SESSION['TourId']));

JsonOut($JSON);

