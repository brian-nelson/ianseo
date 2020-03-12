<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/Fun_Phases.inc.php');
require_once('Common/Lib/Fun_Modules.php');

CheckTourSession(true);

if(empty($_REQUEST['first'])) {
	header('Content-Type: text/xml');
	echo '<response error="1" />';
	die();
}

$Error=1;
$Out='';

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
				$m=array($Matchno, ($Matchno%2) ? $Matchno-1 : $Matchno+1);
				if($Start=='y') {
					if($Team) {
						$Sql1="update TeamFinals set TfShootFirst=(TfShootFirst | ".pow(2, $End)."), TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " where TfTournament={$_SESSION['TourId']} and TfEvent='$Event' and TfMatchNo=$Matchno";
						$Sql2="update TeamFinals set TfShootFirst=(TfShootFirst & ~".pow(2, $End)."), TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " where TfTournament={$_SESSION['TourId']} and TfEvent='$Event' and TfMatchNo={$m[1]}";
						for($i=$End; $i<$Params->ends; ++$i) {
							// alternate teams each component shoots 1 arrow, so we iterate for as many arrows per archer!
							for($j=0; $j < ceil($Params->arrows/$Params->MaxTeam); $j++) {
								for($k=0; $k<2; $k++) {
									for($l=0; $l<$Params->MaxTeam; $l++) {
										$Out.='<t id="'.'s_' . $m[$k] . '_' . ($i*$Params->arrows + $j*$Params->MaxTeam + $l).'" val="'.($TabIndex + $i*2*$Params->arrows + $j*2*$Params->MaxTeam + $k*$Params->MaxTeam + $l).'"/>';
									}
								}
							}
						}
						// SO, each member shoots 1 arrow alternate
                        for($pSo=0; $pSo<3; $pSo++ ) {
                            for ($l = 0; $l < $Params->so; $l++) {
                                for ($k = 0; $k < 2; $k++) {
                                    $Out .= '<t id="' . 't_' . $m[$k] . '_' . (($pSo*$Params->so)+$l) . '" val="' . ($TabIndex + (2*$Params->ends * $Params->arrows )+ (2*$pSo*$Params->so) +  $l * 2 + $k) . '"/>';
                                }
                            }
                        }
					} else {
						$Sql1="update Finals set FinShootFirst=(FinShootFirst | ".pow(2, $End)."), FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . "  where FinTournament={$_SESSION['TourId']} and FinEvent='$Event' and FinMatchNo=$Matchno";
						$Sql2="update Finals set FinShootFirst=(FinShootFirst & ~".pow(2, $End)."), FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . "  where FinTournament={$_SESSION['TourId']} and FinEvent='$Event' and FinMatchNo={$m[1]}";
						for($i=$End; $i<$Params->ends; ++$i) {
							for($j=0; $j<$Params->arrows; $j++) {
								for($k=0; $k<2; $k++) {
									$Out.='<t id="'.'s_' . $m[$k] . '_' . ($i*$Params->arrows+$j).'" val="'.($TabIndex + $i*2*$Params->arrows + $j*2 + $k).'"/>';
								}
							}
						}
						// SO
                        for($pSo=0; $pSo<3; $pSo++ ) {
                            for ($j = 0; $j < $Params->so; $j++) {
                                for ($k = 0; $k < 2; $k++) {
                                    $Out .= '<t id="' . 't_' . $m[$k] . '_' . (($pSo*$Params->so)+$j) . '" val="' . ($TabIndex + (2* $Params->ends * $Params->arrows) + (2*$pSo*$Params->so) + $j * 2 + $k) . '"/>';
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


				$Error=0;

			}
		}
	}
}

header('Content-Type: text/xml');
echo '<response error="'.$Error.'">';
echo $Out;
echo '</response>';

runJack("FinShootingFirst", $_SESSION['TourId'],array("Event"=>$Event ,"Team"=>$Team,"MatchNo"=>($Matchno % 2 ? $Matchno-1 : $Matchno) ,"TourId"=>$_SESSION['TourId']));

die();

