<?php


require_once(dirname(__DIR__) . '/config.php');
CheckTourSession(true);
checkACL(AclCompetition, AclReadWrite, false);
//require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Phases.inc.php');

$JSON=array('error'=>1);

if(checkACL(AclCompetition, AclReadWrite, '')!=AclReadWrite or !isset($_REQUEST['team']) or !isset($_REQUEST['option']) or !isset($_REQUEST['act'])) {
	JsonOut($JSON);
}

$Team=empty($_REQUEST['team']) ? 0 : 1;
$Option='AthButt';
if(!empty($_REQUEST['option'])) {
	switch($_REQUEST['option']) {
		case 'ArrowPhase':
			$Option=$_REQUEST['option'];
		case 'DoubleMatch':
			$Option=$_REQUEST['option'];
	}
}

$Phases=array(64,32,16,8,4,2,1,0);

switch($_REQUEST['act']) {
	case 'get':
		$JSON['events']=array();
		$JSON['show']=array();
		foreach($Phases as $i) {
			$JSON['show']['ph'.$i]=false;
		}
		$JSON['error']=0;
		$q=safe_r_sql("select EvCode, EvEventName, EvFinalFirstPhase, EvFinalAthTarget, EvMatchMultipleMatches, EvMatchArrowsNo, EvElimArrows, EvElimEnds, EvElimSO, EvFinArrows, EvFinEnds, EvFinSO from Events where EvTeamEvent=$Team and EvTournament={$_SESSION['TourId']} and EvFinalFirstPhase>0 order by EvProgr");
		while($r=safe_fetch($q)) {
			$tmp=(object) array(
				'code' => $r->EvCode,
				'event' => $r->EvCode.' - '.$r->EvEventName,
				'phases' => array(),
			);
			$StartPhase=bitwisePhaseId($r->EvFinalFirstPhase);
			switch($Option) {
				case 'AthButt':
					foreach($Phases as $i) {
						$phase=max(1,$i*2);
						$t=array('ph' => $i, 'badge' => '', 'double' =>'');
						if($i>$StartPhase) {
							$tmp->phases[]=$t;
							continue;
						}
						$JSON['show']['ph'.$i]=true;

						if($phase & $r->EvFinalAthTarget) {
							$t['badge']=2;
						} else {
							$t['badge']=1;
						}
						if($phase & $r->EvMatchMultipleMatches) {
							$t['double']=1;
						}
						$tmp->phases[]=$t;
					}
					$JSON['legend1']=get_text('AthButtLegend1', 'Tournament');
					$JSON['legend2']=get_text('AthButtLegend2', 'Tournament');
					break;
				case 'ArrowPhase':
					$tmp->eArrows = $r->EvElimArrows;
					$tmp->eEnds = $r->EvElimEnds;
					$tmp->eSO = $r->EvElimSO;
					$tmp->fArrows = $r->EvFinArrows;
					$tmp->fEnds = $r->EvFinEnds;
					$tmp->fSO = $r->EvFinSO;
					$tmp->eText = get_text('EliminationShort', 'Tournament');
					$tmp->fText = get_text('FinalShort', 'Tournament');
					foreach($Phases as $i) {
						$phase=max(1,$i*2);
						if($i>$StartPhase) {
							$tmp->phases[]=array('val' => '', 'ph' => $i);
							continue;
						}
						$JSON['show']['ph'.$i]=true;

						if($phase & $r->EvMatchArrowsNo) {
							$tmp->phases[]=array('val' => 1, 'ph' => $i);
						} else {
							$tmp->phases[]=array('val' => 2, 'ph' => $i);
						}
					}
					$JSON['legend1']=get_text('ArrowPhaseLegend1', 'Tournament');
					$JSON['legend2']=get_text('ArrowPhaseLegend2', 'Tournament');
					break;
			}
			$JSON['events'][]=$tmp;
		}
		break;
	case 'set':
		if(IsBlocked(BIT_BLOCK_TOURDATA)) {
			JsonOut($JSON);
		}

		switch($Option) {
			case 'AthButt':
				if(!isset($_REQUEST['phase']) or empty($_REQUEST['event']) or empty($_REQUEST['value']) or !in_array($_REQUEST['phase'], $Phases) or !in_array($_REQUEST['value'], array(1,2))) {
					JsonOut($JSON);
				}

				$Phase=max(1,intval($_REQUEST['phase'])*2);
				switch($_REQUEST['value']) {
					case 1:
						$SQL="EvFinalAthTarget=EvFinalAthTarget & ~{$Phase}";
						break;
					case 2:
						$SQL="EvFinalAthTarget=EvFinalAthTarget | $Phase";
						break;
					default:
						JsonOut($JSON);
				}
				safe_w_sql("update Events set $SQL where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode='{$_REQUEST['event']}'");
				$JSON['error']=0;
				$JSON['badge']=$_REQUEST['value'];
				break;
			case 'DoubleMatch':
				if(!isset($_REQUEST['phase']) or empty($_REQUEST['event']) or !isset($_REQUEST['value']) or !in_array($_REQUEST['phase'], $Phases) or !in_array($_REQUEST['value'], array(0,1))) {
					JsonOut($JSON);
				}

				$Phase=max(1,intval($_REQUEST['phase'])*2);
				switch($_REQUEST['value']) {
					case 0:
						$SQL="EvMatchMultipleMatches=EvMatchMultipleMatches & ~{$Phase}";
						break;
					case 1:
						$SQL="EvMatchMultipleMatches=EvMatchMultipleMatches | $Phase";
						break;
					default:
						JsonOut($JSON);
				}
				safe_w_sql("update Events set $SQL where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode='{$_REQUEST['event']}'");
				$JSON['error']=0;
				$JSON['double']=$_REQUEST['value'];
				break;
			case 'ArrowPhase':
				if(!empty($_REQUEST['field'])) {
					if(empty($_REQUEST['event']) or !isset($_REQUEST['value'])) {
						JsonOut($JSON);
					}
					$Value=intval($_REQUEST['value']);
					switch($_REQUEST['field']) {
						case 'eArrows':
							$SQL="EvElimArrows=$Value";
							break;
						case 'eEnds':
							$SQL="EvElimEnds=$Value";
							break;
						case 'eSO':
							$SQL="EvElimSO=$Value";
							break;
						case 'fArrows':
							$SQL="EvFinArrows=$Value";
							break;
						case 'fEnds':
							$SQL="EvFinEnds=$Value";
							break;
						case 'fSO':
							$SQL="EvFinSO=$Value";
							break;
						default:
							JsonOut($JSON);
					}
					safe_w_sql("update Events set $SQL where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode='{$_REQUEST['event']}'");
					$JSON['error']=0;

				} else {
					if(!isset($_REQUEST['phase']) or empty($_REQUEST['event']) or empty($_REQUEST['value']) or (!in_array($_REQUEST['phase'], $Phases) and empty($_REQUEST['field'])) or !in_array($_REQUEST['value'], array(1,2))) {
						JsonOut($JSON);
					}

					$Phase=max(1,intval($_REQUEST['phase'])*2);
					switch($_REQUEST['value']) {
						case 1:
							$SQL="EvMatchArrowsNo=EvMatchArrowsNo | $Phase";
							break;
						case 2:
							$SQL="EvMatchArrowsNo=EvMatchArrowsNo & ~{$Phase}";
							break;
						default:
							JsonOut($JSON);
					}
					safe_w_sql("update Events set $SQL where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode='{$_REQUEST['event']}'");
					$JSON['error']=0;
				}
				break;
		}

		break;
}

JsonOut($JSON);
