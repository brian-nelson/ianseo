<?php

require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Fun_Phases.inc.php');
require_once('Common/Lib/Fun_Modules.php');

$TourId = 0;
$TourCode = '';
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
	$TourId = getIdFromCode($_REQUEST['CompCode']);
	$TourCode = preg_replace('/[^a-z0-9_-]+/i','', $_REQUEST['CompCode']);
}

$EvType = -1;
if(isset($_REQUEST['Type']) && preg_match("/^[01]$/", $_REQUEST['Type'])) {
	$EvType = $_REQUEST['Type'];
}

$EvCode = '';
if(isset($_REQUEST['Event']) && preg_match("/^[a-z0-9_-]+$/i", $_REQUEST['Event'])) {
	$EvCode = $_REQUEST['Event'];
}

$MatchId = -1;
if(isset($_REQUEST['MatchId']) && preg_match("/^[0-9]+$/", $_REQUEST['MatchId'])) {
	$MatchId = $_REQUEST['MatchId'];
}

$JSON=array();

if(!$TourId or !$EvCode or $MatchId==-1 or $EvType==-1) {
	SendResult($JSON);
}

$JSON['Arrows']=array();

$Params=getEventArrowsParams($EvCode, intval(log($MatchId, 2)), $EvType, $TourId);
if($MatchId%2) {
	$MatchL=$MatchId-1;
	$MatchR=$MatchId;
} else {
	$MatchL=$MatchId;
	$MatchR=$MatchId+1;
}

if($EvType) {
	$q=safe_r_sql("select TfShootFirst as ShootFirst, EvMaxTeamPerson from TeamFinals inner join Events on EvCode=TfEvent and EvTournament=TfTournament and EvTeamEvent=1 where TfTournament=$TourId and TfEvent='$EvCode' and TfMatchNo in ($MatchL, $MatchR) order by TfMatchNo");
} else {
	$q=safe_r_sql("select FinShootFirst as ShootFirst, EvMaxTeamPerson from Finals inner join Events on EvCode=FinEvent and EvTournament=FinTournament and EvTeamEvent=0 where FinTournament=$TourId and FinEvent='$EvCode' and FinMatchNo in ($MatchL, $MatchR) order by FinMatchNo");
}
$r=safe_fetch($q);
$ShootFirstL=$r->ShootFirst;
$MaxPersons=$r->EvMaxTeamPerson;

$r=safe_fetch($q);
$ShootFirstR=$r->ShootFirst;

for($End=0;$End<=$Params->ends;$End++) {
	$EndBit=pow(2, $End);
	if(!($ShootFirstL & $EndBit or $ShootFirstR & $EndBit)) {
		// end is not yet set
		//break;
	}
	$ShootsFirst='';

	if($End==$Params->ends) {
		$Name='SO';
		$NumArrows=$Params->so;
		// dealing with SO, so in any case it is 1 arrow alternate per person
        for($soPos=0; $soPos<3; $soPos++) {
            if ($ShootFirstL & $EndBit) {
                $ShootsFirst = 0;
                for ($n = 0; $n < $MaxPersons; $n++) {
                    $JSON['Arrows'][] = array(0, $End+$soPos, $n); // left shoots first
                    $JSON['Arrows'][] = array(1, $End+$soPos, $n); // right shoots next
                }
            } elseif ($ShootFirstR & $EndBit) {
                $ShootsFirst = 1;
                for ($n = 0; $n < $MaxPersons; $n++) {
                    $JSON['Arrows'][] = array(1, $End+$soPos, $n); // right shoots first
                    $JSON['Arrows'][] = array(0, $End+$soPos, $n); // left shoots next
                }
            }
        }
	} else {
		$Name=$End+1;
		$NumArrows=$Params->arrows;
		// normal alternate sequence
		if($ShootFirstL & $EndBit) {
			$ShootsFirst=0;
			for($n=0;$n<$Params->arrows/$MaxPersons;$n++) {
				for($i=0;$i<$MaxPersons;$i++) {
					$JSON['Arrows'][]=array(0, $End, $n*$MaxPersons + $i); // left shoots first
				}
				for($i=0;$i<$MaxPersons;$i++) {
					$JSON['Arrows'][]=array(1, $End, $n*$MaxPersons + $i); // right shoots next
				}
			}
		} elseif($ShootFirstR & $EndBit) {
			$ShootsFirst=1;
			for($n=0;$n<$Params->arrows/$MaxPersons;$n++) {
				for($i=0;$i<$MaxPersons;$i++) {
					$JSON['Arrows'][]=array(1, $End, $n*$MaxPersons + $i); // right shoots first
				}
				for($i=0;$i<$MaxPersons;$i++) {
					$JSON['Arrows'][]=array(0, $End, $n*$MaxPersons + $i); // left shoots next
				}
			}
		}
	}
}

SendResult($JSON);

