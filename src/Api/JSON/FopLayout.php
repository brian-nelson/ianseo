<?php
/*
 *
call=FopLayout.php?ToCode=CompetitionCode[&SVG=1]

ToCode: the competition code
SVG: if not empty also creates the SVGs of the single timeframes


11:03
una cosa così andrebbe bene?

{
    '2019-08-29': {
        '09:00': {
            'data': [
                {
                'day': '2019-08-29',
                'time': '09:00',
                'targetList': ['1-2,4-5,7-8,10-11'],
                'distance': '70m',
                'category': 'RM',
                'phase': '1/4'
                'team': 1,
                'targetType': '5-X Outdoor 80cm',
                'targetNumber': '2',
                'warmup':false
                },
                {
                'day': '2019-08-29',
                'time': '09:00',
                'targetList': ['17-18,20-21,23-24,26-27'],
                'distance': '70m',
                'category': 'RW',
                'phase': '1/4'
                'team': 1,
                'targetType': '5-X Outdoor 80cm',
                'targetNumber': '2',
                'warmup':false
                },
                ],
            'svg': '<svg></svg>'
            },
        },
}


*/

require_once(dirname(__FILE__) . '/config.php');
$JSON=array('error' => 1, 'msg' => get_text('NoTour', 'Tournament'), 'fop'=>array());

if(empty($_REQUEST['ToCode'])) {
	JsonOut($JSON);
}

$CreateSvg=!empty($_REQUEST['SVG']);

$ToId=getIdFromCode($_REQUEST['ToCode']);

if(!$ToId) {
	JsonOut($JSON);
}

$JSON['error']=0;
$JSON['msg']='';

$TargetRanges=array();
$DistancesRanges=array();

$SQL=array();

// get qualifications times and details, they come from DistanceInformation
$SQL[]="select distinct 
                'Q' as Type,
                DiDay as SchDay, 
                date_format(DiStart, '%H:%i') as SchStart,
                DiDuration as Duration,
                DiSession as Session,
                DiTargets as Target,
                if(DiWarmStart=0, '', date_format(DiWarmStart, '%H:%i')) as WarmStart,
                DiWarmDuration as WarmDuration,
                '' as Description,
                DiOptions as SchComment,
                SesAth4Target as AthPerTarget,
                DiDistance,
                DiEnds,
                DiArrows
	from DistanceInformation
	inner join Session on SesTournament=DiTournament and SesOrder=DiSession and SesType='Q'
	where DiTournament=$ToId AND DiType='Q' and DiDay>0";

// check if we have official practice (a schedule event of type "z" with targets assigned)
$SQL[]="select distinct 
                'Z' as Type,
                SchDay as SchDay, 
                date_format(SchStart, '%H:%i') as SchStart,
                SchDuration as Duration,
                SchSesOrder as Session,
                SchTargets as Target,
                '' as WarmStart,
                '' as WarmDuration,
                if(SchText='', if(SchSubTitle='', if(SchTitle='', SchDescr, SchTitle), SchSubTitle), SchText) as Description,
                SchDescr as SchComment,
                SesAth4Target as AthPerTarget,
                '' as DiDistance,
                '' as DiEnds,
                '' as DiArrows
	from Scheduler
	inner join Session on SesTournament=SchTournament and SesOrder=SchSesOrder and SesType='Q'
	where SchTournament=$ToId AND SchSesType='Z' and SchTargets>''";


$SQL="(".implode(") UNION (", $SQL).") order by SchDay, SchStart, Target+0";

$q=safe_r_sql($SQL);
$OldKey='';
$OldDay='';
$OldTime='';
$FOP='';
while($r=safe_fetch($q)) {
	if($r->Target) {
		foreach(explode(',', $r->Target) as $Sequence) {
			$bits=explode('@', $Sequence);
			$tars=explode('-', $bits[0]);
			if(empty($TargetRanges[$r->SchDay])) {
				$TargetRanges[$r->SchDay]=array($tars[0], empty($tars[1]) ? $tars[0] : empty($tars[1]));
			}
			if($tars[0]<$TargetRanges[$r->SchDay][0]) {
				$TargetRanges[$r->SchDay][0]=$tars[0];
			}
			if(end($tars)>$TargetRanges[$r->SchDay][1]) {
				$TargetRanges[$r->SchDay][1]=end($tars);
			}
			if(empty($bits[2])) {
				$bits[2]='';
				$bits[3]='';
				// check which categories are shooting in that session
				$Cats=array();
				$t=safe_r_sql("select distinct EnDivision, EnClass, TfW{$r->DiDistance} as Width, TarDescr 
					from Entries 
    				inner join Qualifications on QuId=EnId and QuSession=$r->Session and QuTarget".(count($tars)==1 ? " = $tars[0]" : " between $tars[0] and $tars[1]")."
				    inner join Divisions on DivTournament=EnTournament and DivId=EnDivision 
				    inner join Classes on ClTournament=EnTournament and ClId=EnClass 
					left join TargetFaces on TfTournament=EnTournament and TfId=EnTargetFace
					left join Targets on TarId=TfT{$r->DiDistance}
					where EnTournament=$ToId order by DivViewOrder, ClViewOrder");
				while($u=safe_fetch($t)) {
					$Cats[]=$u->EnDivision.$u->EnClass;
					$bits[3]=get_text($u->TarDescr).' '.$u->Width.'cm';
				}
				$bits[2]=implode(', ', $Cats);
			} elseif(empty($bits[3])) {
				$bits[3]='';
				$tars=explode('-', $bits[0]);

				$t=safe_r_sql("select distinct TfW{$r->DiDistance} as Width, TarDescr 
					from Entries 
    				inner join Qualifications on QuId=EnId and QuSession=$r->Session and QuTarget".(count($tars)==1 ? " = $tars[0]" : " between $tars[0] and $tars[1]")."
				    inner join Divisions on DivTournament=EnTournament and DivId=EnDivision 
				    inner join Classes on ClTournament=EnTournament and ClId=EnClass 
					inner join TargetFaces on TfTournament=EnTournament and TfId=EnTargetFace
					inner join Targets on TarId=TfT{$r->DiDistance}
					where EnTournament=$ToId order by DivViewOrder, ClViewOrder");
				while($u=safe_fetch($t)) {
					$Cats=$u->EnDivision.$u->EnClass;
					$bits[3]=get_text($u->TarDescr).' '.$u->Width.'cm';
				}
			}

			if(empty($DistancesRanges[$r->SchDay][$r->SchStart])) {
				$DistancesRanges[$r->SchDay][$r->SchStart]=array($bits[1], $bits[1]);
			}
			if($bits[1]<$DistancesRanges[$r->SchDay][$r->SchStart][0]) {
				$DistancesRanges[$r->SchDay][$r->SchStart][0]=$bits[1];
			}
			if($bits[1]>$DistancesRanges[$r->SchDay][$r->SchStart][1]) {
				$DistancesRanges[$r->SchDay][$r->SchStart][1]=$bits[1];
			}

			$FOP=EmptyFop($r->SchDay, $r->SchStart);
			$FOP->targetList=array($bits[0]);
			$FOP->distance=$bits[1];
			$FOP->categories=$bits[2];
			$FOP->description=$r->Type=='Q' ? get_text('Q-Session', 'Tournament') : $r->Description;
			$FOP->warmup='0';
			$FOP->phase='Q';
			$FOP->team='0';
			$FOP->targetType=$bits[3];
			$FOP->athPerTarget=(integer) $r->AthPerTarget;
			$FOP->notes=$r->SchComment;
			$FOP->endsArrows= $r->DiEnds ? $r->DiEnds.'x'.$r->DiArrows : '';

			if($r->WarmStart) {
				$FOPw=clone $FOP;
				$FOPw->time=$r->WarmStart;
				$FOPw->warmup='1';
				$JSON['fop'][$r->SchDay][$r->WarmStart]['items'][]=$FOPw;

				if(empty($DistancesRanges[$r->SchDay][$r->WarmStart])) {
					$DistancesRanges[$r->SchDay][$r->WarmStart]=array(intval($u->Distance), intval($u->Distance));
				}
				if(intval($u->Distance)<$DistancesRanges[$r->SchDay][$r->WarmStart][0]) {
					$DistancesRanges[$r->SchDay][$r->WarmStart][0]=intval($u->Distance);
				}
				if(intval($u->Distance)>$DistancesRanges[$r->SchDay][$r->WarmStart][1]) {
					$DistancesRanges[$r->SchDay][$r->WarmStart][1]=intval($u->Distance);
				}
			}

			$JSON['fop'][$r->SchDay][$r->SchStart]['items'][]=$FOP;
		}
	} else {
		// get the real positions of athletes on field
		$SQL2="select 
	            group_concat(distinct QuTarget order by QuTarget) as Targets,
       			min(QuTarget+0) as MinTarget,
       			max(QuTarget+0) as MaxTarget,
	            EnDivision,
	            EnClass,
	            TfW{$r->DiDistance} as Width, 
       			Td{$r->DiDistance} as Distance,
	            TarDescr 
			from Entries 
            inner join Qualifications on QuId=EnId and QuSession=$r->Session and QuTarget>0
		    inner join Divisions on DivTournament=EnTournament and DivId=EnDivision 
		    inner join Classes on ClTournament=EnTournament and ClId=EnClass 
			inner join TargetFaces on TfTournament=EnTournament and TfId=EnTargetFace
			inner join Targets on TarId=TfT{$r->DiDistance}
			inner join Tournament on ToId=EnTournament
			inner join TournamentDistances ON TdType=ToType AND TdTournament=EnTournament AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE TdClasses and Td{$r->DiDistance}!='-'
			where EnTournament=$ToId 
			group by EnDivision, EnClass, TfId
			order by MinTarget";
		$t=safe_r_sql($SQL2);
		while($u=safe_fetch($t)) {
			if(empty($TargetRanges[$r->SchDay])) {
				$TargetRanges[$r->SchDay]=array($u->MinTarget, $u->MaxTarget);
			}
			if($u->MinTarget<$TargetRanges[$r->SchDay][0]) {
				$TargetRanges[$r->SchDay][0]=$u->MinTarget;
			}
			if($u->MaxTarget>$TargetRanges[$r->SchDay][1]) {
				$TargetRanges[$r->SchDay][1]=$u->MaxTarget;
			}

			if(empty($DistancesRanges[$r->SchDay][$r->SchStart])) {
				$DistancesRanges[$r->SchDay][$r->SchStart]=array(intval($u->Distance), intval($u->Distance));
			}
			if(intval($u->Distance)<$DistancesRanges[$r->SchDay][$r->SchStart][0]) {
				$DistancesRanges[$r->SchDay][$r->SchStart][0]=intval($u->Distance);
			}
			if(intval($u->Distance)>$DistancesRanges[$r->SchDay][$r->SchStart][1]) {
				$DistancesRanges[$r->SchDay][$r->SchStart][1]=intval($u->Distance);
			}

			$FOP=EmptyFop($r->SchDay, $r->SchStart);
			$FOP->targetList=array();
			$FOP->distance=intval($u->Distance);
			$FOP->categories=$u->EnDivision.$u->EnClass;
			$FOP->description=$r->Type=='Q' ? get_text('Q-Session', 'Tournament') : $r->Description;
			$FOP->warmup='0';
			$FOP->phase='Q';
			$FOP->team='0';
			$FOP->targetType=get_text($u->TarDescr).' '.$u->Width.'cm';
			$FOP->athPerTarget=(integer) $r->AthPerTarget;
			$FOP->notes=$r->SchComment;
			$FOP->endsArrows= $r->DiEnds ? $r->DiEnds.'x'.$r->DiArrows : '';

			$Targets=array();
			foreach(explode(',', $u->Targets) as $tar) {
				if(empty($Targets)) {
					$Targets[0]=$tar;
				} elseif($tar==end($Targets)+1) {
					$Targets[1]=$tar;
				} elseif($tar>end($Targets)+1) {
					$FOP->targetList[]=implode('-', $Targets);
					$Targets=array($tar);
				}
			}
			if($Targets) {
				$FOP->targetList[]=implode('-', $Targets);
			}

			if($r->WarmStart) {
				$FOPw=clone $FOP;
				$FOPw->time=$r->WarmStart;
				$FOPw->warmup='1';
				$JSON['fop'][$r->SchDay][$r->WarmStart]['items'][]=$FOPw;

				if(empty($DistancesRanges[$r->SchDay][$r->WarmStart])) {
					$DistancesRanges[$r->SchDay][$r->WarmStart]=array(intval($u->Distance), intval($u->Distance));
				}
				if(intval($u->Distance)<$DistancesRanges[$r->SchDay][$r->WarmStart][0]) {
					$DistancesRanges[$r->SchDay][$r->WarmStart][0]=intval($u->Distance);
				}
				if(intval($u->Distance)>$DistancesRanges[$r->SchDay][$r->WarmStart][1]) {
					$DistancesRanges[$r->SchDay][$r->WarmStart][1]=intval($u->Distance);
				}
			}

			$JSON['fop'][$r->SchDay][$r->SchStart]['items'][]=$FOP;
		}
	}
}

// check all the matches
require_once('Common/Lib/Fun_Phases.inc.php');
$SQL="select 
       FSScheduledDate SchDay,
       date_format(FSScheduledTime, '%H:%i') SchStart,
       date_format(FwTime, '%H:%i') as WarmupStart,
       FwTargets as WarmupTargets,
       FwOptions,
       group_concat(distinct FSTarget+0 order by FsTarget) as Targets,
       min(FSTarget+0) as MinTarget,
       max(FSTarget+0) as MaxTarget,
       EvDistance,
       EvCode,
       EvProgr,
       EvTeamEvent,
       TarDescr,
       GrPhase,
       EvTeamEvent,
       EvProgr,
       EvEventName,
       EvTargetSize,
       (EvFinalAthTarget & GrBitPhase) as DoubleArchers,
       if(EvMatchArrowsNo & GrBitPhase=GrBitPhase, EvElimArrows, EvFinArrows) as Arrows,
       if(EvMatchArrowsNo & GrBitPhase=GrBitPhase, EvElimEnds, EvFinEnds) as Ends,
       EvMaxTeamPerson,
       EvFinalFirstPhase
	from FinSchedule
	inner join Events on EvCode=FSEvent and EvTeamEvent=FSTeamEvent and EvTournament=FSTournament
	inner join Targets on TarId=EvFinalTargetType
	inner join Grids on GrMatchNo=FSMatchNo
	left join FinWarmup on FwTournament=FSTournament and FwEvent=FSEvent and FwTeamEvent=FSTeamEvent and FwDay=FSScheduledDate and FwMatchTime=FSScheduledTime
	where FSTournament=$ToId and FsTarget+0>0
	group by FSScheduledDate, FSScheduledTime, EvTeamEvent, EvCode
	order by FSScheduledDate, FSScheduledTime, MinTarget
	";

$q=safe_r_sql($SQL);
while($r=safe_fetch($q)) {
	if(empty($TargetRanges[$r->SchDay])) {
		$TargetRanges[$r->SchDay]=array($r->MinTarget,$r->MaxTarget);
	}
	if($r->MinTarget<$TargetRanges[$r->SchDay][0]) {
		$TargetRanges[$r->SchDay][0]=$r->MinTarget;
	}
	if($r->MaxTarget>$TargetRanges[$r->SchDay][1]) {
		$TargetRanges[$r->SchDay][1]=$r->MaxTarget;
	}
	$AthPerTarget=$r->EvMaxTeamPerson;
	if(!$r->EvTeamEvent and $r->DoubleArchers) {
		$AthPerTarget *= 2;
	}

	if($r->WarmupStart) {
		$FOP=EmptyFop($r->SchDay, $r->WarmupStart);
		$FOP->targetList=explode(', ', $r->WarmupTargets);
		$FOP->warmup='1';
		$FOP->athPerTarget=$AthPerTarget;
		$FOP->distance=$r->EvDistance;
		$FOP->categories=$r->EvCode;
		$FOP->team=$r->EvTeamEvent;
		$FOP->targetType=get_text($r->TarDescr).' '.$r->EvTargetSize.'cm';
		$FOP->description=$r->EvEventName;
		$FOP->phase=get_text(namePhase($r->EvFinalFirstPhase, $r->GrPhase).'_Phase');
		$FOP->notes=$r->FwOptions;
		$FOP->endsArrows='';
		$JSON['fop'][$r->SchDay][$r->WarmupStart]['items'][]=$FOP;

		if(empty($DistancesRanges[$r->SchDay][$r->WarmupStart])) {
			$DistancesRanges[$r->SchDay][$r->WarmupStart]=array($r->EvDistance, $r->EvDistance);
		}
		if($r->EvDistance<$DistancesRanges[$r->SchDay][$r->WarmupStart][0]) {
			$DistancesRanges[$r->SchDay][$r->WarmupStart][0]=$r->EvDistance;
		}
		if($r->EvDistance>$DistancesRanges[$r->SchDay][$r->WarmupStart][1]) {
			$DistancesRanges[$r->SchDay][$r->WarmupStart][1]=$r->EvDistance;
		}
	}

	$FOP=EmptyFop($r->SchDay, $r->SchStart);
	$Targets=array();
	foreach(explode(',', $r->Targets) as $tar) {
		if(empty($Targets)) {
			$Targets[0]=$tar;
		} elseif($tar==end($Targets)+1) {
			$Targets[1]=$tar;
		} elseif($tar>end($Targets)+1) {
			$FOP->targetList[]=implode('-', $Targets);
			$Targets=array($tar);
		}
	}
	if($Targets) {
		$FOP->targetList[]=implode('-', $Targets);
	}
	$FOP->warmup='0';
	$FOP->athPerTarget=$AthPerTarget;
	$FOP->distance=$r->EvDistance;
	$FOP->categories=$r->EvCode;
	$FOP->team=$r->EvTeamEvent;
	$FOP->targetType=get_text($r->TarDescr).' '.$r->EvTargetSize.'cm';
	$FOP->description=$r->EvEventName;
	$FOP->phase=get_text(namePhase($r->EvFinalFirstPhase, $r->GrPhase).'_Phase');
	$FOP->notes=$r->FwOptions;
	$FOP->endsArrows=$r->Ends.'x'.$r->Arrows;

	$JSON['fop'][$r->SchDay][$r->SchStart]['items'][]=$FOP;

	if(empty($DistancesRanges[$r->SchDay][$r->SchStart])) {
		$DistancesRanges[$r->SchDay][$r->SchStart]=array($r->EvDistance, $r->EvDistance);
	}
	if($r->EvDistance<$DistancesRanges[$r->SchDay][$r->SchStart][0]) {
		$DistancesRanges[$r->SchDay][$r->SchStart][0]=$r->EvDistance;
	}
	if($r->EvDistance>$DistancesRanges[$r->SchDay][$r->SchStart][1]) {
		$DistancesRanges[$r->SchDay][$r->SchStart][1]=$r->EvDistance;
	}
}

$terne=array(
	array(0,255,0),
	array(255,153,255),
	array(255,255,204),
	array(153,153,255),
	array(255,153,0),
	array(204,255,204),
// 			array(204,0,255),
	array(51,204,204),
);

// seed a lot of colors (Macolin rules!
foreach($terne as $col) {
	$ColorArray[] = array($col[0],$col[1],$col[2]);
}
foreach($terne as $col) {
	$ColorArray[] = array($col[1],$col[2],$col[0]);
}
foreach($terne as $col) {
	$ColorArray[] = array($col[2],$col[0],$col[1]);
}
foreach($terne as $col) {
	$ColorArray[] = array($col[0],$col[2],$col[1]);
}
foreach($terne as $col) {
	$ColorArray[] = array($col[1],$col[0],$col[2]);
}
foreach($terne as $col) {
	$ColorArray[] = array($col[2],$col[1],$col[0]);
}

$ColorAssignment = array();
$ColorIndex=0;
$TgtWidth=30;
$FontSizeTgt=15;
$FontSizeDateTime=20;
$OffsetX=0;
$StartY=30;

foreach($JSON['fop'] as $Day => $Starts) {
	$StartTarget=$TargetRanges[$Day][0];
	$SvgTargets='<g>';
	foreach(range($TargetRanges[$Day][0], $TargetRanges[$Day][1]) as $k=>$tgt) {
		$X1=$OffsetX+$TgtWidth*$k;
		$X2=$X1+$TgtWidth;
		$Xt=$X2-$TgtWidth/2;
		$SvgTargets.='<path d="M '.$X1.' '.$StartY.' l 0 '.$TgtWidth.' l '.$TgtWidth.' 0 l 0 -'.$TgtWidth.'" fill="#c0c0c0" stroke="#000000"/>';
		$SvgTargets.='<text font-size="'.$FontSizeTgt.'" x="'.$Xt.'" y="'.($StartY+$TgtWidth-8).'" text-anchor="middle" fill="#000000">'.$tgt.'</text>';
	}
	$Width=$X2;
	$SvgTargets.='</g>';
	foreach($Starts as $Start => $Items) {
		if(!$CreateSvg) {
			$JSON['fop'][$Day][$Start]['SVG']='';
			continue;
		}
		$FontY=$FontSizeDateTime;
		$H=$TgtWidth*6+$DistancesRanges[$Day][$Start][1]-$DistancesRanges[$Day][$Start][0]+($Items['items'][0]->phase=='Q' ? 0 : $TgtWidth);
		$W=max(500, $OffsetX+$Width);
		$SVG='<svg width="'.($W).'" height="'.($H).'" viewbox="-1 -1 '.($W+1).' '.($H+1).'"><style>svg {font-family: "Myriad Pro", "Myriad Web", "Tahoma", "Helvetica", "Arial", sans-serif}</style>';
		$SVG.=$SvgTargets;

		$TargetLimit=array();
		$OldTarget='';
		$DistanceLimit=array();
		$OldDistance='';
		$Txts='';
		foreach($Items['items'] as $Next => $Item) {
			if(!$Next) {
				$SVG.='<text x="0" y="'.$FontY.'" text-anchor="start" fill="#000000">';
				$SVG.='<tspan font-size="'.$FontSizeDateTime.'" font-weight="bold" x="0">'.$Day.' '.$Start.'</tspan>';
				$SVG.='<tspan font-size="'.$FontSizeTgt.'" dx="20" font-weight="bold">'.$Item->notes.'</tspan>';
				if($Item->phase=='Q') {
					$AthPerTarget='';
					for($n=1;$n<=$Item->athPerTarget;$n++) {
						$AthPerTarget.=chr(64+$n);
					}
					//$FontY+=$FontSizeTgt*1.2;
					$SVG.='<tspan font-size="'.$FontSizeTgt.'" dx="10" font-weight="bold">'.$AthPerTarget.'</tspan>';
				}
				$SVG.='<tspan font-size="'.$FontSizeTgt.'" dx="10">'.$Item->endsArrows.'</tspan>';
				$SVG.='</text>';
			}
			// sets the color
			if(empty($ColorAssignment[$Item->team.$Item->categories])) {
				$ColorAssignment[$Item->team.$Item->categories]=$ColorArray[$ColorIndex++];
				if($ColorIndex>=count($ColorArray)) {
					$ColorIndex=0;
				}
			}
			$Colour=$Item->warmup ? array(220,220,220) : $ColorAssignment[$Item->team.$Item->categories];

			// set the distance gap (Offset)
			$YGap=$TgtWidth+$DistancesRanges[$Day][$Start][1]-$Item->distance;
			$BoxHeight=$TgtWidth*2+$Item->distance-$DistancesRanges[$Day][$Start][0];
			$AthletesRow=$StartY+$YGap+$BoxHeight+$TgtWidth;
			$TargetRow=$AthletesRow+($Item->phase=='Q' ? 0 : 16);

			foreach($Item->targetList as $tgts) {
				$Atgts=explode('-', $tgts);

				if($OldDistance!=$Item->distance) {
					if($DistanceLimit) {
						$YGapTmp=$TgtWidth+$DistancesRanges[$Day][$Start][1]-$OldDistance;
						// draw target type string
						$x=$OffsetX+$TgtWidth*($DistanceLimit[0]-$StartTarget);
						$w=$TgtWidth*(1+end($DistanceLimit)-$DistanceLimit[0]);
						$SVG.='<rect x="'.$x.'" y="'.($StartY+$TgtWidth).'" height="'.$YGapTmp.'" width="'.$w.'" fill="#f0f0f0" stroke="black"/>';
						$SVG.='<text font-size="'.$FontSizeTgt.'" x="'.($x+$w/2).'" y="'.($StartY+$YGapTmp+$FontSizeTgt+4).'" text-anchor="middle" fill="#000000"'.($w==$TgtWidth ? ' lengthAdjust="spacingAndGlyphs" textLength="'.$w.'"' : '').'>'.$OldDistance.'</text>';
					}
					$DistanceLimit=array();
					$OldDistance=$Item->distance;
				}
				if(empty($DistanceLimit)) {
					$DistanceLimit=array($Atgts[0], end($Atgts));
				}
				if(end($Atgts)>end($DistanceLimit)) {
					$DistanceLimit[1]=end($Atgts);
				}

				$X1=$OffsetX+$TgtWidth*($Atgts[0]-$StartTarget);
				$X2=$TgtWidth*(1+end($Atgts)-$Atgts[0]);
				$Xt=$X1+$X2/2;
				$YOffset=$TgtWidth+$YGap;

				if($OldTarget!=$Item->targetType) {
					if($TargetLimit) {
						// draw target type string
						$x=$OffsetX+$TgtWidth*($TargetLimit[0]-$StartTarget);
						$w=$TgtWidth*(1+end($TargetLimit)-$TargetLimit[0]);
						$SVG.='<rect x="'.$x.'" y="'.$TargetRow.'" height="'.$TgtWidth.'" width="'.$w.'" fill="#f0f0f0" stroke="black"/>';
						$SVG.='<text font-size="'.$FontSizeTgt.'" x="'.($x+$w/2).'" y="'.($TargetRow+$FontSizeTgt+4).'" text-anchor="middle" fill="#000000"'.($w<=$TgtWidth*5 ? ' lengthAdjust="spacingAndGlyphs" textLength="'.$w.'"' : '').'>'.$OldTarget.'</text>';
					}
					$TargetLimit=array();
					$OldTarget=$Item->targetType;
				}
				if(empty($TargetLimit)) {
					$TargetLimit=array($Atgts[0], end($Atgts));
				}
				if(end($Atgts)>end($TargetLimit)) {
					$TargetLimit[1]=end($Atgts);
				}

				// draw category targets
				$SVG.='<rect x="'.$X1.'" y="'.($StartY+$YOffset).'" height="'.$BoxHeight.'" width="'.$X2.'" fill="rgba('.implode(',',$Colour).',0.75)" stroke="black" />';
				$FontY=$AthletesRow-$FontSizeTgt;
				if($Item->phase!='Q') {
					// prints the ath per target indication
					$aw=6;
					$aGap=($TgtWidth-$aw*$Item->athPerTarget)/($Item->athPerTarget+1);
					$ax=$X1;
					for($n=$Atgts[0]; $n<=end($Atgts);$n++ ) {
						$SVG.='<rect x="'.$ax.'" y="'.$AthletesRow.'" width="'.$TgtWidth.'" height="16" fill="none" stroke="gray"></rect>';
						for($i=0;$i<$Item->athPerTarget;$i++) {
							$SVG.='<rect x="'.($ax+$aGap+$i*($aGap+$aw)).'" y="'.($AthletesRow+5).'" width="'.$aw.'" height="'.$aw.'" fill="gray" stroke="none"></rect>';
						}
						$ax+=$TgtWidth;
					}
					$Txts.='<text font-size="'.$FontSizeTgt.'" x="'.$Xt.'" y="'.($FontY).'" text-anchor="middle" fill="#000000"'.($X2==$TgtWidth ? ' lengthAdjust="spacingAndGlyphs" textLength="'.$X2.'"' : '').'>'.$Item->phase.'</text>';
					$FontY -= $FontSizeTgt*1.2;
				}
				$Txts.='<text font-size="'.$FontSizeTgt.'" x="'.$Xt.'" y="'.($FontY).'" text-anchor="middle" fill="#000000"'.($X2==$TgtWidth ? ' lengthAdjust="spacingAndGlyphs" textLength="'.$X2.'"' : '').'>'.$Item->categories.'</text>';
			}
			// draw
		}
		if($TargetLimit) {
			// draw target type string
			$x=$OffsetX+$TgtWidth*($TargetLimit[0]-$StartTarget);
			$w=$TgtWidth*(1+end($TargetLimit)-$TargetLimit[0]);
			$SVG.='<rect x="'.$x.'" y="'.($TargetRow).'" height="'.$TgtWidth.'" width="'.$w.'" fill="#f0f0f0" stroke="black"/>';
			$SVG.='<text font-size="'.$FontSizeTgt.'" x="'.($x+$w/2).'" y="'.($TargetRow+$FontSizeTgt+4).'" text-anchor="middle" fill="#000000"'.($w<=$TgtWidth*5 ? ' lengthAdjust="spacingAndGlyphs" textLength="'.$w.'"' : '').'>'.$OldTarget.'</text>';
		}
		if($DistanceLimit) {
			// draw target type string
			$YGapTmp=$TgtWidth+$DistancesRanges[$Day][$Start][1]-$OldDistance;
			$x=$OffsetX+$TgtWidth*($DistanceLimit[0]-$StartTarget);
			$w=$TgtWidth*(1+end($DistanceLimit)-$DistanceLimit[0]);
			$SVG.='<rect x="'.$x.'" y="'.($StartY+$TgtWidth).'" height="'.$YGapTmp.'" width="'.$w.'" fill="#f0f0f0" stroke="black"/>';
			$SVG.='<text font-size="'.$FontSizeTgt.'" x="'.($x+$w/2).'" y="'.($StartY+$YGapTmp+$FontSizeTgt+4).'" text-anchor="middle" fill="#000000"'.($w==$TgtWidth ? ' lengthAdjust="spacingAndGlyphs" textLength="'.$w.'"' : '').'>'.$OldDistance.'</text>';
		}
		$SVG.=$Txts;

		$SVG.='</svg>';

		//echo($SVG);

		$JSON['fop'][$Day][$Start]['SVG']=$SVG;
	}
}

//$JSON['msg']=str_replace(array("\n","\t")," ",$SQL);
JsonOut($JSON);

function EmptyFop($Day, $Time) {
	return (object) array(
		'day' => $Day,
		'time' => $Time,
		'targetList' => array(),
		'distance' => '',
		'categories' => '',
		'description' => '',
		'phase' => '',
		'team' => '',
		'targetType' => '',
		'athPerTarget' => '',
		'warmup' => '',
		'notes' => '',
		'endsArrows' => '',
	);
}