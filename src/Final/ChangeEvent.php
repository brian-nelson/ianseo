<?php
/*
													- ChangeEvent.php -
	Ritorna la fase da cui parte l'evento Ev.
	Se l'evento � '' (Tutti) viene ritornata la fase max in Grids
*/

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Phases.inc.php');

if (!isset($_REQUEST['Ev']) || !CheckTourSession()) {
	print get_text('CrackError');
	exit;
}
checkACL(array(AclIndividuals,AclTeams, AclOutput), AclReadOnly, false);

$Errore=0;
$Team = (isset($_REQUEST['TeamEvent']) ? $_REQUEST['TeamEvent'] : 0);
$StartPhase = -1;
$SetPoints = 0;
$PoolMatches=(!empty($_REQUEST['ElimPool']) and !$Team and !empty($_REQUEST['Ev']));
$PoolMatchesSingle=false;
$PoolMatchesSingleWA=false;

// se ho un event faccio la query
if (!empty($_REQUEST['Ev'])) {
	$Select
		= "SELECT EvFinalFirstPhase AS StartPhase, EvMatchMode as MatchMode, EvElimType "
		. "FROM Events "
		. "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode=" . StrSafe_DB($_REQUEST['Ev']) . " AND EvTeamEvent=" . StrSafe_DB($Team) . " ";
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)==1) {
		$Row=safe_fetch($Rs);
		$StartPhase = $Row->StartPhase;
		$SetPoints = ($Row->MatchMode!=0);
		$PoolMatchesSingle=($Row->EvElimType==3);
		$PoolMatchesSingleWA=($Row->EvElimType==4);
	} else {
		$Errore=1;
	}
} else {
    $Select = "SELECT MAX(EvFinalFirstPhase) AS Phase, MAX(EvMatchMode) AS MatchMode 
        FROM Events 
        WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=" . StrSafe_DB($Team);

    $Rs=safe_r_sql($Select);

    if (safe_num_rows($Rs)==1) {
        $Row=safe_fetch($Rs);
        $StartPhase=$Row->Phase;
        $SetPoints = ($Row->MatchMode!=0);
    } else {
        $Errore=1;
    }
}


header('Content-Type: text/xml');

print '<response>';
print '<error>' . $Errore . '</error>';
print '<team>' . $Team . '</team>';
print '<set_points>' . ($SetPoints ? '1':'0') . '</set_points>';
if($PoolMatches) {
	print '<start_phase>-1</start_phase>';
	print '<good_phase>';
	print '<code>-1</code>';
	print '<name>' . get_text('All_Phases') . '</name>';
	print '</good_phase>';
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

	print '<start_phase>'.$Starting.'</start_phase>';
	foreach($AllPhases as $k => $v) {
		print '<good_phase>';
		print '<code>'.$k.'</code>';
		print '<name>' . $v . '</name>';
		print '</good_phase>';
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

	print '<start_phase>'.$Starting.'</start_phase>';
	foreach($AllPhases as $k => $v) {
		print '<good_phase>';
		print '<code>'.$k.'</code>';
		print '<name><![CDATA[' . $v . ']]></name>';
		print '</good_phase>';
	}
} else {
	print '<start_phase>' . $StartPhase . '</start_phase>';
	for ($i=bitwisePhaseId($StartPhase);$i>=1;$i/=2)
	{
		print '<good_phase>';
		print '<code>' . bitwisePhaseId($i) . '</code>';
		print '<name><![CDATA[' . get_text( namePhase($StartPhase,$i). '_Phase') . ']]></name>';
		print '</good_phase>';
	}
	print '<good_phase>';
	print '<code>0</code>';
	print '<name>' . get_text('0_Phase') . '</name>';
	print '</good_phase>';
}
print '</response>';
