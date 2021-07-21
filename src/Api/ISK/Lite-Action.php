<?php
/*
THIS FILE IS ESSENTIAL TO MAKE THE APIS TO GET RECOGNIZED BY IANSEO

* HOW IT WORKS
the "codename" of the API will be used in ianseo. The codename is the name of the directory containing the Api.
The essentials are:

* ApiConfig.php
this file gets included in the Competition Setup (Tournament/index.php)
* DrawQRCode.php
this file is used by the ScoreCard printout routines.
 */

require_once(__DIR__.'/config-ianseo.php');

$JSON=array('error'=>1,'msg'=>get_text('ErrGenericError', 'Errors'));

if(ISK_PRO or !CheckTourSession() or checkACL(AclISKServer, AclReadWrite, false)!=AclReadWrite or empty($_REQUEST['act'])) {
	JsonOut($JSON);
}

require_once('Common/Lib/CommonLib.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Lib/Fun_Phases.inc.php');
require_once('Qualification/Fun_Qualification.local.inc.php');

$JSON['error']=0;
$JSON['msg']='';

switch($_REQUEST['act']) {
	case 'ImportType':
		if(isset($_REQUEST['val'])) {
			setModuleParameter('ISK', 'ImportType', intval($_REQUEST['val']));
		} else {
			$JSON['msg']=get_text('WAToolbox-MissingParams', 'Api');
			$JSON['error']=1;
		}
		break;
    case 'CalcClDivInd':
    case 'CalcClDivTeam':
    case 'CalcFinInd':
    case 'CalcFinTeam':
		if(isset($_REQUEST['val'])) {
			setModuleParameter('ISK', $_REQUEST['act'], intval($_REQUEST['val']));
		} else {
			$JSON['msg']=get_text('WAToolbox-MissingParams', 'Api');
			$JSON['error']=1;
		}
		break;
	case 'ImportQualNow':
		if(IsBlocked(BIT_BLOCK_QUAL)) {
			$JSON['msg']=get_text('BlockedPhase', 'Tournament');
			$JSON['error']=1;
		} else {

                // we import EVERYTHING related to this competition...
			$SQL="SELECT Qualifications.*, IskDtArrowstring, IskDtDistance, IskDtEndNo, DIDistance, DIEnds, DIArrows, ToGoldsChars, ToXNineChars 
				from Qualifications
				INNER JOIN Entries ON QuId=EnId
				INNER JOIN Tournament ON ToId=EnTournament
				INNER JOIN DistanceInformation ON DITournament=EnTournament AND DISession=QuSession AND DIType='Q'
				INNER JOIN IskData ON iskDtTournament=EnTournament AND IskDtMatchNo=0 AND IskDtEvent='' AND IskDtTeamInd=0 AND IskDtType='Q' AND IskDtTargetNo=QuTargetNo AND IskDtDistance=DIDistance
				WHERE EnTournament={$_SESSION['TourId']}";
			$updated=array();
			$q=safe_r_sql($SQL);
			while($r=safe_fetch($q)) {
				$Dist=$r->IskDtDistance;
				$arrowString = str_pad($r->{'QuD'.$Dist.'Arrowstring'},$r->DIArrows*$r->DIEnds);
				for($i=0; $i<$r->DIArrows; $i++){
					if($r->IskDtArrowstring[$i]!=' '){
						$arrowString[($r->IskDtEndNo-1)*$r->DIArrows+$i]=$r->IskDtArrowstring[$i];
					}
				}
				$Score=0;
				$Gold=0;
				$XNine=0;
				list($Score,$Gold,$XNine)=ValutaArrowStringGX($arrowString,$r->ToGoldsChars,$r->ToXNineChars);
				$Hits=strlen(str_replace(' ', '', $arrowString));

				$Update = "UPDATE Qualifications SET
						QuD{$Dist}Score={$Score}, QuD{$Dist}Gold={$Gold}, QuD{$Dist}Xnine={$XNine}, QuD{$Dist}ArrowString='{$arrowString}', QuD{$Dist}Hits={$Hits},
						QuScore=QuD1Score+QuD2Score+QuD3Score+QuD4Score+QuD5Score+QuD6Score+QuD7Score+QuD8Score,
						QuGold=QuD1Gold+QuD2Gold+QuD3Gold+QuD4Gold+QuD5Gold+QuD6Gold+QuD7Gold+QuD8Gold,
						QuXnine=QuD1Xnine+QuD2Xnine+QuD3Xnine+QuD4Xnine+QuD5Xnine+QuD6Xnine+QuD7Xnine+QuD8Xnine,
						QuHits=QuD1Hits+QuD2Hits+QuD3Hits+QuD4Hits+QuD5Hits+QuD6Hits+QuD7Hits+QuD8Hits,
						QuTimestamp=" . StrSafe_DB(date('Y-m-d H:i:s')) . "
						WHERE QuId={$r->QuId}";
				safe_w_SQL($Update);
				if(safe_w_affected_rows()) {
					$updated[$Dist][] = $r->QuId;
				}
				$Update = "DELETE FROM IskData
						WHERE IskDtTournament={$_SESSION['TourId']} AND IskDtMatchNo=0 AND IskDtEvent='' AND IskDtTeamInd=0 AND IskDtType='Q'
						AND IskDtTargetNo='{$r->QuTargetNo}' AND IskDtDistance={$Dist} AND IskDtEndNo={$r->IskDtEndNo} AND IskDtArrowstring='{$r->IskDtArrowstring}'";
				safe_w_SQL($Update);

				// calculate snapshot if any
				if(getModuleParameter('ISK', 'Snapshot')) {
					useArrowsSnapshotTarget($Dist, $r->QuTargetNo, strlen(rtrim($arrowString)));
				}
			}
			foreach($updated as $Dist => $upd) {
				$EnIds=implode(",",$upd);
				$SQL = "SELECT DISTINCT EnClass, EnDivision, MAX(EnIndClEvent) as IndCl, Max(EnTeamClEvent) as TeamCl
						FROM Entries
						WHERE EnTournament={$_SESSION['TourId']} AND EnId IN ($EnIds)
						group by EnClass, EnDivision";
				$q=safe_r_sql($SQL);
				while($r=safe_fetch($q)) {
					if ($r->IndCl != 0) {
						Obj_RankFactory::create('DivClass', array('tournament' => $_SESSION['TourId'], 'events' => $r->EnDivision . $r->EnClass, 'dist' => $Dist))->calculate();
						Obj_RankFactory::create('DivClass', array('tournament' => $_SESSION['TourId'], 'events' => $r->EnDivision . $r->EnClass, 'dist' => 0))->calculate();
					}
					if ($r->TeamCl != 0) {
						MakeTeams(NULL, $r->EnDivision . $r->EnClass, $_SESSION['TourId']);
					}
				}
				// Abs recalculation
				$SQL="select distinct IndEvent 
						from Individuals 
						inner join Entries on EnId=IndId and EnTournament=IndTournament and EnIndFEvent=1
						where IndId in ($EnIds) and IndTournament={$_SESSION['TourId']}";
				$q=safe_r_sql($SQL);
				while($r=safe_fetch($q)) {
					Obj_RankFactory::create('Abs', array('tournament' => $_SESSION['TourId'], 'events' => $r->IndEvent, 'dist' => $Dist))->calculate();
					Obj_RankFactory::create('Abs', array('tournament' => $_SESSION['TourId'], 'events' => $r->IndEvent, 'dist' => 0))->calculate();
					ResetShootoff($r->IndEvent, 0, 0, $_SESSION['TourId']);
				}

				// Abs Team recalc
				$SQL = "SELECT DISTINCT EvCode, EnDivision, EnClass
						FROM Events
						INNER JOIN EventClass ON EvCode=EcCode AND EcTeamEvent>0 AND EvTournament=EcTournament
						inner join Entries on EnId in ($EnIds) and EnTournament=EvTournament and EnDivision=EcDivision and EnClass=EcClass and if(EcSubClass='', true, EnSubClass=EcSubClass) and EnTeamFEvent+EnTeamMixEvent>0
						WHERE EvTournament={$_SESSION['TourId']} and EvTeamEvent=1
						";
				$q=safe_r_sql($SQL);
				while($r=safe_fetch($q)) {
					MakeTeamsAbs(NULL, $r->EnDivision, $r->EnClass, $_SESSION['TourId']);
				}
			}
			if($updated) {
				$JSON['msg']=get_text('ImportDoneQual', 'ISK');
			} else {
				$JSON['msg']=get_text('ImportDoneEmpty', 'ISK');
			}
		}
		break;
	case 'ImportMatchNow':
		if(IsBlocked(BIT_BLOCK_IND) or IsBlocked(BIT_BLOCK_TEAM)) {
			$JSON['msg']=get_text('BlockedPhase', 'Tournament');
			$JSON['error']=1;
		} else {
			$SQL=array("SELECT FSEvent, FSMatchNo, FSTeamEvent, FinArrowstring as Arrowstring, FinTiebreak as TieBreak, IskDtArrowstring, IskDtEndNo, GrPhase, IskDtEndNo, IskDtType
					FROM FinSchedule
					INNER JOIN Grids ON FSMatchNo=GrMatchNo
					INNER JOIN IskData ON IskDtTournament=FsTournament AND IskDtMatchNo=FsMatchNo AND IskDtEvent=FSEvent AND IskDtTeamInd=FsTeamEvent AND IskDtType='I' AND IskDtTargetNo='' AND IskDtDistance=0
					INNER JOIN Finals ON FsTournament=FinTournament AND FsMatchNo=FinMatchNo AND FSEvent=FinEvent
					WHERE FSTournament={$_SESSION['TourId']} AND FsTeamEvent=0");
			$SQL[]="SELECT FSEvent, FSMatchNo, FSTeamEvent, TfArrowstring as Arrowstring, TfTiebreak as TieBreak, IskDtArrowstring, IskDtEndNo, GrPhase, IskDtEndNo, IskDtType
					FROM FinSchedule
					INNER JOIN Grids ON FSMatchNo=GrMatchNo
					INNER JOIN IskData ON IskDtTournament=FsTournament AND IskDtMatchNo=FsMatchNo AND IskDtEvent=FSEvent AND IskDtTeamInd=FsTeamEvent AND IskDtType='T' AND IskDtTargetNo='' AND IskDtDistance=0
					INNER JOIN TeamFinals ON FsTournament=TfTournament AND FsMatchNo=TfMatchNo AND FSEvent=TfEvent
					WHERE FSTournament={$_SESSION['TourId']} AND FsTeamEvent=1";
			$q=safe_r_SQL('('.implode(') UNION (', $SQL).')');
			if(safe_num_rows($q)) {
				$JSON['msg']=get_text('ImportDoneMatches', 'ISK');
			} else {
				$JSON['msg']=get_text('ImportDoneEmpty', 'ISK');
			}
			while($r=safe_fetch($q)){
				$obj=getEventArrowsParams($r->FSEvent,$r->GrPhase,$r->FSTeamEvent,$_SESSION['TourId']);
				$isSO = ($End > $obj->ends);

				$arrowString = ($isSO ? str_pad($r->TieBreak,$obj->so) : str_pad($r->Arrowstring,$obj->arrows));
				for($i=0; $i<($isSO ? $obj->so : $obj->arrows); $i++){
					if($r->IskDtArrowstring[$i]!=' '){
						$arrowString[($isSO ? 0 : ($r->IskDtEndNo-1)*$obj->arrows)+$i]=$r->IskDtArrowstring[$i];
					}
				}
				$startPos = (($isSO ? ($obj->arrows*$obj->ends) : 0) +1);
				UpdateArrowString($r->FSMatchNo, $r->FSEvent, $r->FSTeamEvent, $arrowString, $startPos, ($startPos+($isSO ? $obj->so : $obj->arrows*$obj->ends)-1), $_SESSION['TourId']);

				$Update = "DELETE FROM IskData
						WHERE IskDtTournament={$_SESSION['TourId']} AND IskDtMatchNo={$r->FSMatchNo} AND IskDtEvent='{$r->FSEvent}' AND IskDtTeamInd={$r->FSTeamEvent} AND IskDtType='{$r->IskDtType}}'
						AND IskDtTargetNo='' AND IskDtDistance=0 AND IskDtEndNo={$r->IskDtEndNo} AND IskDtArrowstring='{$r->IskDtArrowstring}'";
				safe_w_SQL($Update);
			}
		}
		break;
    case 'doCalcClDivInd':
        if(!IsBlocked(BIT_BLOCK_QUAL)) {
            $qDist = safe_r_SQL("SELECT ToNumDist from Tournament WHERE ToId={$_SESSION['TourId']}");
            if ($rDist = safe_fetch($qDist)) {
                for ($i = 0; $i <= $rDist->ToNumDist; $i++) {
                    Obj_RankFactory::create('DivClass', array('tournament' => $_SESSION['TourId'], 'dist' => $i))->calculate();
                }
            }
            Obj_RankFactory::create('DivClass', array('tournament' => $_SESSION['TourId'], 'dist' => 0))->calculate();
            $JSON['msg']=get_text('CalculateNowDone', 'ISK');
        } else {
            $JSON['msg']=get_text('BlockedPhase', 'Tournament');
        }
        break;
    case 'doCalcClDivTeam':
        if(!IsBlocked(BIT_BLOCK_QUAL))	{
            $JSON['error']=intval(MakeTeams(NULL, NULL));
            if($JSON['error']) {
                $JSON['msg']=get_text('MakeTeamsError', 'Tournament');
            } else {
                $JSON['msg']=get_text('CalculateNowDone', 'ISK');
            }
        } else {
            $JSON['msg']=get_text('BlockedPhase', 'Tournament');
        }
        break;
    case 'doCalcFinInd':
        if(!IsBlocked(BIT_BLOCK_QUAL)) {
            $qDist = safe_r_SQL("SELECT ToNumDist from Tournament WHERE ToId={$_SESSION['TourId']}");
            if ($rDist = safe_fetch($qDist)) {
                for ($i = 0; $i <= $rDist->ToNumDist; $i++) {
                    Obj_RankFactory::create('Abs', array('tournament' => $_SESSION['TourId'], 'dist' => $i))->calculate();
                }
            }
            Obj_RankFactory::create('Abs', array('tournament' => $_SESSION['TourId'], 'dist' => 0))->calculate();
            $JSON['msg']=get_text('CalculateNowDone', 'ISK');
        } else {
            $JSON['msg']=get_text('BlockedPhase', 'Tournament');
        }
        break;
        break;
    case 'doCalcFinTeam':
		if(!IsBlocked(BIT_BLOCK_QUAL))	{
			$JSON['error']=intval(MakeTeamsAbs(NULL,null,null));
			if($JSON['error']) {
				$JSON['msg']=get_text('MakeTeamsError', 'Tournament');
			} else {
				$JSON['msg']=get_text('CalculateNowDone', 'ISK');
			}
		} else {
			$JSON['msg']=get_text('BlockedPhase', 'Tournament');
		}
		break;
	case 'DeleteDataQual':
		// removes all data from the temp table related to this competition
		safe_w_sql("delete from IskData where iskDtTournament={$_SESSION['TourId']} AND IskDtType='Q'");
		break;
	case 'DeleteDataMatch':
		// removes all data from the temp table related to this competition
		safe_w_sql("delete from IskData where iskDtTournament={$_SESSION['TourId']} AND IskDtType in ('I','T')");
		break;
	default:
		$JSON['error']=1;
		JsonOut($JSON);
}

JsonOut($JSON);
