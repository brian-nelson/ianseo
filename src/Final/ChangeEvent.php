<?php
/*
													- ChangeEvent.php -
	Ritorna la fase da cui parte l'evento Ev.
	Se l'evento � '' (Tutti) viene ritornata la fase max in Grids
*/

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Phases.inc.php');

$JSON=array('error' => 1);

if (!isset($_REQUEST['Ev']) or !CheckTourSession() or checkACL(array(AclIndividuals,AclTeams, AclOutput), AclReadOnly, false) < AclReadOnly) {
	JsonOut($JSON);
}


$Errore=0;
$Team = (isset($_REQUEST['TeamEvent']) ? intval($_REQUEST['TeamEvent']) : 0);
$StartPhase = -1;
$SetPoints = 0;
$PoolMatches=(!empty($_REQUEST['ElimPool']) and !$Team and !empty($_REQUEST['Ev']));
$PoolMatchesSingle=false;
$PoolMatchesSingleWA=false;

// se ho un event faccio la query
if (!empty($_REQUEST['Ev'])) {
	$Select = "SELECT EvFinalFirstPhase AS StartPhase, EvMatchMode as MatchMode, EvElimType
		FROM Events
		WHERE EvTournament={$_SESSION['TourId']} AND EvCode=" . StrSafe_DB($_REQUEST['Ev']) . " AND EvTeamEvent=$Team";
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)==1) {
		$Row=safe_fetch($Rs);
		$StartPhase = $Row->StartPhase;
		$SetPoints = ($Row->MatchMode!=0);
		$PoolMatchesSingle=($Row->EvElimType==3);
		$PoolMatchesSingleWA=($Row->EvElimType==4);
	} else {
		JsonOut($JSON);
	}
} else {
    $Select = "SELECT MAX(EvFinalFirstPhase) AS Phase, MAX(EvMatchMode) AS MatchMode 
        FROM Events 
        WHERE EvTournament={$_SESSION['TourId']} AND EvTeamEvent=$Team";

    $Rs=safe_r_sql($Select);

    if (safe_num_rows($Rs)==1) {
        $Row=safe_fetch($Rs);
        $StartPhase=$Row->Phase;
        $SetPoints = ($Row->MatchMode!=0);
    } else {
		JsonOut($JSON);
    }
}

$JSON['error']=0;
$JSON['team']=$Team;
$JSON['set_points']=($SetPoints ? '1':'0');
$JSON['start_phase']=-1;
$JSON['good_phase']=array();

if($PoolMatches) {
	$JSON['good_phase'][]=array('code' => -1, 'name' => get_text('All_Phases'));
} elseif($PoolMatchesSingleWA) {
	require_once('Common/Lib/CommonLib.php');
	$Starting=64;
	$AllPhases=getPoolMatchesPhasesWA();
	if(!empty($_REQUEST['Ev'])) {
		$q=safe_r_sql("select distinct GrPhase, EvFinalFirstPhase 
			from Grids 
			inner join Finals on GrMatchNo=FinMatchNo and FinEvent=".StrSafe_DB($_REQUEST['Ev'])." and FinTournament=".$_SESSION['TourId']."
			inner join Events on EvCode=FinEvent and EvTeamEvent=0 and EvTournament=FinTournament
			order by GrPhase desc
			");
		$tmp=$AllPhases;
		$AllPhases=array();
		$Starting=0;
		while($r=safe_fetch($q)) {
			if(!$Starting) {
				$Starting=$r->GrPhase;
			}
			if(isset($tmp[$r->GrPhase])) {
				$AllPhases[$r->GrPhase]=$tmp[$r->GrPhase];
			} else {
				$AllPhases[$r->GrPhase]=get_text(namePhase($r->EvFinalFirstPhase, $r->GrPhase). '_Phase');
			}
		}
	}

	$JSON['start_phase']=$Starting;
	foreach($AllPhases as $k => $v) {
		$JSON['good_phase'][]=array('code' => $k, 'name' => $v);
	}
} elseif($PoolMatchesSingle) {
	require_once('Common/Lib/CommonLib.php');
	$Starting=64;
	$AllPhases=getPoolMatchesPhases();
	if(!empty($_REQUEST['Ev'])) {
		$q=safe_r_sql("select distinct GrPhase, EvFinalFirstPhase
			from Grids 
			inner join Finals on GrMatchNo=FinMatchNo and FinEvent=".StrSafe_DB($_REQUEST['Ev'])." and FinTournament=".$_SESSION['TourId']."
			inner join Events on EvCode=FinEvent and EvTeamEvent=0 and EvTournament=FinTournament
			order by GrPhase desc
			");
		$tmp=$AllPhases;
		$AllPhases=array();
		$Starting=0;
		while($r=safe_fetch($q)) {
			if(!$Starting) {
				$Starting=$r->GrPhase;
			}
			if(isset($tmp[$r->GrPhase])) {
				$AllPhases[$r->GrPhase]=$tmp[$r->GrPhase];
			} else {
				$AllPhases[$r->GrPhase]=get_text(namePhase($r->EvFinalFirstPhase, $r->GrPhase). '_Phase');
			}
		}
	}

	$JSON['start_phase']=$Starting;
	foreach($AllPhases as $k => $v) {
		$JSON['good_phase'][]=array('code' => $k, 'name' => $v);
	}
} else {
	$JSON['start_phase']=$StartPhase;
	for ($i=bitwisePhaseId($StartPhase);$i>=1;$i/=2) {
		$JSON['good_phase'][]=array('code' => bitwisePhaseId($i), 'name' => get_text( namePhase($StartPhase,$i). '_Phase'));
	}
	$JSON['good_phase'][]=array('code' => 0, 'name' => get_text( '0_Phase'));
}

JsonOut($JSON);
