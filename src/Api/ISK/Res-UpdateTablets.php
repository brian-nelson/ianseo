<?php

require_once(dirname(dirname(__FILE__)).'/config.php');
require_once(dirname(__FILE__).'/Lib.php');
$Error=1;

if(!CheckTourSession()) {
	header('Content-Type: text/xml');
	die('<response error="'.$Error.'"/>');
}
checkACL(AclISKServer, AclReadOnly,false);

require_once('Common/Lib/Fun_Modules.php');

$Sequence=$_REQUEST['ses'];
$Dist=intval($_REQUEST['dist']);
$End=intval($_REQUEST['end']);

// fetches an array of all available ends
$Ends=array();
$EndsStatus=array();
$TgtsStatus=array();
$Targets=array();
$Messages=array();
$Payloads=array();
$Out='';
$Msg='';
$AssignedDevices=array();
$MatchOvers=array();
$ShowImport=array();
$Anomalies=array();


switch($Sequence[0]) {
	case 'Q':
		// gets the targets
//		$SqlTargets="select distinct left(QuTargetNo, 4) Target from Entries inner join Qualifications on EnId=QuId and QuSession={$Sequence[2]} where EnTournament={$_SESSION['TourId']} and substr(QuTargetNo,2)+0>0 order by Target";
//// 		$Out.='<q><![CDATA['.$SqlTargets.']]></q>';
//		$q=safe_r_sql($SqlTargets);
//		while($r=safe_Fetch($q)) {
//			$Targets[]=StrSafe_DB($r->Target);
//			$TgtsStatus[intval(substr($r->Target,1))]='';
//		}
		// prepares an array with all the available ends and the values if any
		//$SQL="select substring(AtTargetNo, -4, 3)+0 Target, right(AtTargetNo, 1) Letter, AtTargetNo, Arrowstring, DiArrows
		//	from AvailableTarget
		//	left join (select QuTargetNo, EnTournament, QuD{$Dist}ArrowString Arrowstring
		//			from Qualifications
		//			inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']} and EnStatus<=1
		//			) Quals on AtTargetNo=QuTargetNo and AtTournament=EnTournament
		//	left join DistanceInformation on DiType='Q' and DiDistance=$Dist and DiSession={$Sequence[2]} and DiTournament={$_SESSION['TourId']}
		//	where AtTournament={$_SESSION['TourId']} and left(AtTargetNo, 4) in (".implode(',', $Targets).")
		//	order by AtTargetNo";
// 		$Out.='<q><![CDATA['.$SQL.']]></q>';

		$SQL="select distinct AtTarget Target, AtLetter Letter, AtTargetNo
			from Entries 
			inner join Qualifications on QuId=EnId and QuSession={$Sequence[2]} and QuTarget>0
			inner join AvailableTarget on QuTarget=AtTarget and QuSession=AtSession and AtTournament=EnTournament
			where EnTournament={$_SESSION['TourId']}
			order by AtTargetNo";
		$q=safe_r_sql($SQL);
		while($r=safe_fetch($q)) {
			$Ends[$r->Target][$r->Letter]='G';
			$TgtsStatus[$r->Target]='';
		}

		$SQL="select distinct QuTarget Target, QuLetter Letter, QuTargetNo, QuD{$Dist}ArrowString as Arrowstring, DiArrows, concat(EnDivision, EnClass) as Category
			from Entries
			inner join Qualifications on QuId=EnId and QuSession=$Sequence[2] and QuTarget>0
			left join DistanceInformation on DiType='Q' and DiDistance=$Dist and DiSession={$Sequence[2]} and DiTournament={$_SESSION['TourId']}
			where EnStatus<=1 and EnTournament={$_SESSION['TourId']}
			order by QuTargetNo";

		$q=safe_r_sql($SQL);
		while($r=safe_fetch($q)) {
			$Ends[$r->Target][$r->Letter]='';
			$Payloads[$r->Target]='ses='.$Sequence.'&dist='.$Dist.'&end='.$End.'&target='.$r->Target;
			$arrows=strlen(str_replace(' ', '', substr($r->Arrowstring, ($End-1)*$r->DiArrows, $r->DiArrows)));
			if(is_null($r->Arrowstring)) {
				$Ends[$r->Target][$r->Letter]='G'; // Gray, not used
			} elseif($arrows==$r->DiArrows) {
				$Ends[$r->Target][$r->Letter]='B'; // Blue, OK
				if($TgtsStatus[$r->Target]!='C') $TgtsStatus[$r->Target]='B';
			} elseif($arrows) {
				$Ends[$r->Target][$r->Letter]='C'; // Cyan, missing arrows
				$TgtsStatus[$r->Target]='C';
			}
			$Anomalies[$r->Target][$r->Letter]=preg_match('/ /sim', rtrim($r->Arrowstring));
		}

		// check which device should be attached to which target
		$SQL="Select IskDvCode, IskDvTarget+0 IntTarget from IskDevices where IskDvTournament={$_SESSION['TourId']} and IskDvState>0";
// 		$Out.='<q><![CDATA['.$SQL.']]></q>';
		$q=safe_r_sql($SQL);
		while($r=safe_fetch($q)) {
			if(!isset($TgtsStatus[$r->IntTarget])) {
				// device is not involved on this...
				continue;
			}
			if(empty($AssignedDevices[$r->IntTarget]) or !in_array($r->IskDvCode, $AssignedDevices[$r->IntTarget])) {
				$AssignedDevices[$r->IntTarget][]=$r->IskDvCode;
			}
		}

		// check if the devices have sent something to the targets
		$SQL="Select IskData.*, FsTarget+0 FsTarget, concat(FsScheduledDate, ' ', FsScheduledTime) Scheduled, DiArrows, substring(IskDtTargetNo, -4, 3)+0 Target, right(IskDtTargetNo, 1) Letter, trim(IskDtArrowstring) Arrowstring, IskDvCode, (IskDtType='Q' and left(IskDtTargetNo,1)={$Sequence[2]} and IskDtDistance=$Dist) CorrectSession, IskDvTarget
				from IskData
				left join FinSchedule on FsTournament={$_SESSION['TourId']} and FsMatchNo=IskDtMatchNo and FsEvent=IskDtEvent and FsTeamEvent=0
				left join DistanceInformation on DiType='Q' and DiDistance=$Dist and DiSession={$Sequence[2]} and DiTournament={$_SESSION['TourId']}
				left join IskDevices on IskDvTournament=IskDtTournament and IskDvState>0 and IskDvDevice=IskDtDevice
				where IskDtTournament={$_SESSION['TourId']}
				order by IskDtTargetNo, IskDtEndNo
				";
// 		$Out.='<q><![CDATA['.$SQL.']]></q>';
		$q=safe_r_sql($SQL);
		while($r=safe_fetch($q)) {
			$SkipRest=false;
			$popId="data[$r->IskDtMatchNo][".($r->IskDtEvent ? $r->IskDtEvent : ':::')."][$r->IskDtTeamInd][$r->IskDtType][".($r->IskDtTargetNo ? substr($r->IskDtTargetNo, 0, 4) : ':::')."][$r->IskDtDistance][$r->IskDtEndNo]=1";
			// check the correct code of device
			$r->IskDvCode=($r->IskDvCode ? $r->IskDvCode : 'Unknown');
			$GroupTargets=getGroupedTargets($r->IskDvTarget, $Sequence[2], 'Q', '', true);


			// readdress the correct target for matches
			if($r->IskDtType!='Q' and $r->IskDtType!='E') {
				$r->Target=intval($r->IskDtTargetNo);
			}
			if(!$r->IskDvCode) {
				// device is not known...
				$Msg.='<div>'.get_text('IskUnknownDevice', 'Api', $r->Target ? $r->Target : $r->FsTarget).'</div>';
			}
			if(!isset($TgtsStatus[$r->Target]) and !in_array($r->Target, $GroupTargets)) {
				// device is not involved on this... but is sending scores
				$Msg.='<div ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target ? $r->Target : $r->FsTarget).'" value="'.$popId.'">'.get_text('IskSpuriousDevice', 'Api', array($r->IskDvCode, $r->Target ? $r->Target : $r->FsTarget)).'</div>';
				$SkipRest=true;
			}
			if(!$r->CorrectSession) {
				// device is not sending Qualification scores
				$Msg.='<div ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target ? $r->Target : $r->FsTarget).'" value="'.$popId.'">'.get_text('IskSpuriousScore-Device', 'Api', array($r->IskDvCode, $r->Target ? $r->Target : $r->FsTarget, $r->IskDtType . ' '.(strstr('EQ', $r->IskDtType) ? ($r->IskDtTargetNo[0] . ' ' . $r->IskDtDistance) : $r->IskDtEvent . ' '. $r->IskDtMatchNo. ' ('.$r->Scheduled.')'))).'</div>';
				$SkipRest=true;
			}
			if($SkipRest) {
				continue;
			}
// 			if($r->Arrowstring) {
				// actually scoring something
				switch(true) {
					case ($Ends[$r->Target][$r->Letter]=='G'): // this Position should not score at all!!!
						$span='<span ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">' . $r->Letter . '</span>';
						if(empty($Messages[$r->Target][$r->IskDvCode]['Empty']) or !in_array($span, $Messages[$r->Target][$r->IskDvCode]['Empty'])) $Messages[$r->Target][$r->IskDvCode]['Empty'][]=$span;
						$TgtsStatus[$r->Target]='R';
						break;
					case ($r->IskDtEvent): // scoring on another session!
						$span='<span ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">' . $r->IskDtEvent . '</span>';
						if(empty($Messages[$r->Target][$r->IskDvCode]['Match']) or !in_array($span, $Messages[$r->Target][$r->IskDvCode]['Match'])) $Messages[$r->Target][$r->IskDvCode]['Match'][]=$span;
						$Ends[$r->Target][$r->Letter]='R'; // Red, error condition
						$TgtsStatus[$r->Target]='R';
						break;
					case ($r->IskDtDistance!=$Dist): // scoring on a different distance
						$span='<span ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">' . $r->IskDtDistance . '</span>';
						if(empty($Messages[$r->Target][$r->IskDvCode]['Distance']) or !in_array($span, $Messages[$r->Target][$r->IskDvCode]['Distance'])) $Messages[$r->Target][$r->IskDvCode]['Distance'][]=$span;
						$Ends[$r->Target][$r->Letter]='R'; // Red, error condition
						$TgtsStatus[$r->Target]='R';
						break;
					case ($r->IskDtEndNo!=$End):
						$span='<span ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">' . $r->IskDtEndNo . '</span>';
						if(empty($Messages[$r->Target][$r->IskDvCode]['End']) or !in_array($span, $Messages[$r->Target][$r->IskDvCode]['End'])) {
							$Messages[$r->Target][$r->IskDvCode]['End'][]=$span;
						}
// 						$Ends[$r->Target][$r->Letter]='O'; // Orange, error condition
						if($TgtsStatus[$r->Target]!='R') $TgtsStatus[$r->Target]='O';
						break;
					case ($r->IskDvTarget!=$r->Target and !in_array($r->Target, $GroupTargets)):
						$span='<span ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">' . $r->IskDtEndNo . '</span>';
						if(empty($Messages[$r->Target][$r->IskDvCode]['End']) or !in_array($span, $Messages[$r->Target][$r->IskDvCode]['End'])) {
							$Messages[$r->Target][$r->IskDvCode]['End'][]=$span;
						}
// 						$Ends[$r->Target][$r->Letter]='O'; // Orange, error condition
						if($TgtsStatus[$r->Target]!='R') $TgtsStatus[$r->Target]='O';
						break;
					case (strlen(str_replace(' ', '', $r->Arrowstring))!=$r->DiArrows):
						$Ends[$r->Target][$r->Letter]='Z'; // Yellow, score in progress
						$ShowImport[$r->Target]=true;
						if($TgtsStatus[$r->Target]!='R' and $TgtsStatus[$r->Target]!='O') {
							$TgtsStatus[$r->Target]='Z';
						}
						break;
					default:
						$Ends[$r->Target][$r->Letter]='Y'; // Yellow, score finished
						$ShowImport[$r->Target]=true;
						if(!$TgtsStatus[$r->Target] or $TgtsStatus[$r->Target]=='C' or $TgtsStatus[$r->Target]=='B') {
							$TgtsStatus[$r->Target]='Y';
						}
				}
// 			}
		}
		foreach($Ends as $t => $l) {
			if(empty($ShowImport[$t]) or $TgtsStatus[$t]!='Y') continue;
			$status=true;
			foreach($l as $l1) {
				$status=($status and ($l1=='Y' or $l1=='G'));
			}
			if(!$status) $TgtsStatus[$t]='Z';
		}
		$Error=0;
		break;
	case 'E':
		break;
	case 'I':
		$MatchTarget=array();
		$TargetsInvolved=array();
		$Date=substr($Sequence, 1, 10);
		$Time=substr($Sequence, 11);
		// Gets the targets involved
		$SQL="select tgt1.*, tgt2.*,
				if(GrPhase1 & EvMatchArrowsNo, EvElimArrows, EvFinArrows) Arrows, if(GrPhase1 & EvMatchArrowsNo, EvElimEnds, EvFinEnds) Ends, if(GrPhase1 & EvMatchArrowsNo, EvElimSO, EvFinSO) SO
			from (select GrPhase as GrPhase1, FinAthlete Entry1, FinArrowstring Arrowstring1, FinTieBreak TieBreak1, FsTarget+0 Target1, substr(FsLetter, length(FsTarget)+1, 1) Letter1, FsLetter FsLetter1, FsMatchNo FsMatchNo1, FsEvent FsEvent1, FinWinLose as Win1
				from FinSchedule
				inner join Grids on FsMatchNo=GrMatchno
				inner join Finals on FsEvent=FinEvent and FinTournament={$_SESSION['TourId']} and FsMatchNo=FinMatchNo
				where FsTournament={$_SESSION['TourId']} and FsTarget>'' and FsTeamEvent=0 and FsScheduledDate='$Date' and FsScheduledTime='$Time' and FsMatchNo%2=0) tgt1
			inner join (select FinAthlete Entry2, FinArrowstring Arrowstring2, FinTieBreak TieBreak2, FsTarget+0 Target2, substr(FsLetter, length(FsTarget)+1, 1) Letter2, FsLetter FsLetter2, FsMatchNo FsMatchNo2, FsEvent FsEvent2, FinWinLose as Win2
				from FinSchedule
				inner join Finals on FsEvent=FinEvent and FsTournament=FinTournament and FsMatchNo=FinMatchNo
				where FsTournament={$_SESSION['TourId']} and FsTarget>'' and FsTeamEvent=0 and FsScheduledDate='$Date' and FsScheduledTime='$Time') tgt2
				on FsEvent1=FsEvent2 and FsMatchNo2=FsMatchNo1+1
			inner join Events on FsEvent1=EvCode and EvTeamEvent=0 and EvTournament={$_SESSION['TourId']}
			order by FsLetter1";
		$q=safe_r_sql($SQL);
		while($r=safe_fetch($q)) {
			$Tgt=($r->Target1==$r->Target2 ? $r->Target1 : "{$r->Target1}-{$r->Target2}");
			if($r->Win1 or $r->Win2) $MatchOvers[]=$Tgt;
			$Let1=($r->Letter1 ? $r->Letter1 : 'A');
			$Let2=($r->Letter2 && $r->Letter2!='A' ? $r->Letter2 : 'B');
			$Ends[$Tgt][$Let1]='';
			$Ends[$Tgt][$Let2]='';
			$MatchTarget[$r->FsEvent1][$r->FsMatchNo1]=array($Tgt, $Let1);
			$MatchTarget[$r->FsEvent1][$r->FsMatchNo2]=array($Tgt, $Let2);
			$TargetsInvolved[$r->Target1]=$Tgt;
			$TargetsInvolved[$r->Target2]=$Tgt;
			if(empty($TgtsStatus[$Tgt])) $TgtsStatus[$Tgt]='';

			$arrows1=strlen(trim(substr($r->Arrowstring1, ($End-1)*$r->Arrows, $r->Arrows)));
			$arrows2=strlen(trim(substr($r->Arrowstring2, ($End-1)*$r->Arrows, $r->Arrows)));
			if($End > $r->Ends) {
				$arrows1=strlen(trim($r->TieBreak1));
				$arrows2=strlen(trim($r->TieBreak2));
				$r->Arrows=$r->SO;
			}
			// Letter 1
			if(!$r->Entry1) {
				$Ends[$Tgt][$Let1]='G'; // Gray, not used
			} elseif($arrows1==$r->Arrows) {
				$Ends[$Tgt][$Let1]='B'; // Blue, OK
				if($TgtsStatus[$Tgt]!='C') $TgtsStatus[$Tgt]='B';
			} elseif($arrows1) {
				$Ends[$Tgt][$Let1]='C'; // Cyan, missing arrows
				$TgtsStatus[$Tgt]='C';
			}
			$Anomalies[$Tgt][$Let1]=preg_match('/ /sim', rtrim($r->Arrowstring1));

			// Letter 2
			if(!$r->Entry2) {
				$Ends[$Tgt][$Let2]='G'; // Gray, not used
			} elseif($arrows2==$r->Arrows) {
				$Ends[$Tgt][$Let2]='B'; // Blue, OK
				if($TgtsStatus[$Tgt]!='C') $TgtsStatus[$Tgt]='B';
			} elseif($arrows2) {
				$Ends[$Tgt][$Let2]='C'; // Cyan, missing arrows
				$TgtsStatus[$Tgt]='C';
			}
			$Anomalies[$Tgt][$Let2]=preg_match('/ /sim', rtrim($r->Arrowstring2));

			$Payloads[$Tgt]='ses='.$Sequence.'&dist='.$Dist.'&end='.$End.'&event='.$r->FsEvent1.'&matchno='.$r->FsMatchNo1.','.$r->FsMatchNo2;
		}

		// check which device should be attached to which target
		$SQL="Select IskDvCode, IskDvTarget+0 IntTarget from IskDevices where IskDvTournament={$_SESSION['TourId']} and IskDvState>0";
		$Out.='<q><![CDATA['.$SQL.']]></q>';
		$q=safe_r_sql($SQL);
		while($r=safe_fetch($q)) {
			if(empty($TargetsInvolved[$r->IntTarget]) or !isset($TgtsStatus[$TargetsInvolved[$r->IntTarget]])) {
				// device is not involved on this...
				continue;
			}
			if(empty($AssignedDevices[$TargetsInvolved[$r->IntTarget]]) or !in_array($r->IskDvCode, $AssignedDevices[$TargetsInvolved[$r->IntTarget]])) {
				$AssignedDevices[$TargetsInvolved[$r->IntTarget]][]=$r->IskDvCode;
			}
		}

		// check if the devices have sent something to the targets
		$SQL="Select IskData.*, concat(FsScheduledDate, ' ', FsScheduledTime) Scheduled, FsTarget+0 Target, substr(FsLetter, length(FsTarget)+1, 1) Letter,
				trim(IskDtArrowstring) Arrowstring, IskDvCode, IskDtType='I' CorrectSession, IskDvTarget,
				if(EvMatchArrowsNo & GrPhase, EvElimArrows, EvFinArrows) Arrows, if(EvMatchArrowsNo & GrPhase, EvElimEnds, EvFinEnds) Ends, if(EvMatchArrowsNo & GrPhase, EvElimSO, EvFinSO) SO
			from IskData
			inner join FinSchedule on FsTournament={$_SESSION['TourId']} and FsMatchNo=IskDtMatchNo and FsEvent=IskDtEvent and FsTeamEvent=0
			inner join Grids on FsMatchNo=GrMatchno
			inner join IskDevices on IskDvTournament=IskDtTournament and IskDvState>0 and IskDvDevice=IskDtDevice
			inner join Events on IskDtEvent=EvCode and EvTeamEvent=0 and EvTournament={$_SESSION['TourId']}
			where IskDtTournament={$_SESSION['TourId']}
			order by IskDvTarget, IskDtEndNo
			";
		$Out.='<q><![CDATA['.$SQL.']]></q>';
		$q=safe_r_sql($SQL);
		while($r=safe_fetch($q)) {
			$SkipRest=false;
			$popId="data[$r->IskDtMatchNo][".($r->IskDtEvent ? $r->IskDtEvent : ':::')."][$r->IskDtTeamInd][$r->IskDtType][".($r->IskDtTargetNo ? $r->IskDtTargetNo : ':::')."][$r->IskDtDistance][$r->IskDtEndNo]=$r->IskDtArrowstring";
			// check the correct code of device
			$r->IskDvCode=($r->IskDvCode ? $r->IskDvCode : 'Unknown');

			// readdress the correct target for matches
			if(strstr('EQ', $r->IskDtType)) {
				$r->Target=intval(substr($r->IskDtTargetNo, 1));
			}
			if(!$r->IskDvCode) {
				// device is not known...
				$Msg.='<div ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">'.get_text('IskUnknownDevice', 'Api', $r->Target).'</div>';
			}
			if(empty($TargetsInvolved[$r->Target]) or !isset($TgtsStatus[$TargetsInvolved[$r->Target]])) {
				// device is not involved on this... but is sending scores
				$Msg.='<div ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">'.get_text('IskSpuriousDevice', 'Api', array($r->IskDvCode, $r->Target)).'</div>';
				$SkipRest=true;
			}
			if(!$r->CorrectSession) {
				// device is not sending Qualification scores
				$Msg.='<div ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">'.get_text('IskSpuriousScore-Device', 'Api', array($r->IskDvCode, $r->Target, $r->IskDtType . ' '.(strstr('EQ', $r->IskDtType) ? $r->IskDtTargetNo[0] : $r->IskDtEvent . ' '.$r->IskDtMatchNo. ' ('.$r->Scheduled.')'))).'</div>';
				$SkipRest=true;
			}
			if(empty($MatchTarget[$r->IskDtEvent][$r->IskDtMatchNo])) {
				$Msg.='<div ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">'.get_text('IskSpuriousScore-Match', 'Api', array($r->IskDvCode, $r->IskDtEvent . ' ' . $r->IskDtMatchNo. ' ('.$r->Scheduled.')')).'</div>';
				$SkipRest=true;
			}
			if($SkipRest) {
				continue;
			}

			$Tgt=$MatchTarget[$r->IskDtEvent][$r->IskDtMatchNo][0];
			$Let=$MatchTarget[$r->IskDtEvent][$r->IskDtMatchNo][1];


			if($r->Arrowstring) {
				// actually scoring something
				switch(true) {
					case ($Ends[$Tgt][$Let]=='G'): // this Position should not score at all!!!
						$span='<span ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">'.$Tgt.'</span>';
						if(empty($Messages[$r->Target][$r->IskDvCode]['Empty']) or !in_array($span, $Messages[$r->Target][$r->IskDvCode]['Empty'])) $Messages[$r->Target][$r->IskDvCode]['Empty'][]=$span;
						$TgtsStatus[$Tgt]='R';
						break;
					case ($r->IskDtDistance!=$Dist): // scoring on a different distance
						$span='<span ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">'.$r->IskDtDistance.'</span>';
						if(empty($Messages[$r->Target][$r->IskDvCode]['Distance']) or !in_array($span, $Messages[$r->Target][$r->IskDvCode]['Distance'])) $Messages[$r->Target][$r->IskDvCode]['Distance'][]=$span;
						$Ends[$Tgt][$Let]='R'; // Red, error condition
						$TgtsStatus[$Tgt]='R';
						break;
					case ($Tgt!=$TargetsInvolved[$r->Target]):
						$span='<span ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">' . $r->IskDtEndNo .'</span>';
						if(empty($Messages[$r->Target][$r->IskDvCode]['End']) or !in_array($span, $Messages[$r->Target][$r->IskDvCode]['End'])) $Messages[$r->Target][$r->IskDvCode]['End'][]=$span;
						if(empty($TgtsStatus[$r->Target]) or $TgtsStatus[$r->Target]!='R') $TgtsStatus[$r->Target]='O';
						break;
					case ($r->IskDtEndNo!=$End):
						$span='<span ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">' . $r->IskDtEndNo .'</span>';
						if(empty($Messages[$r->Target][$r->IskDvCode]['End']) or !in_array($span, $Messages[$r->Target][$r->IskDvCode]['End'])) $Messages[$r->Target][$r->IskDvCode]['End'][]=$span;
						$Ends[$Tgt][$Let]='O'; // Orange, error condition
						if($TgtsStatus[$Tgt]!='R') $TgtsStatus[$Tgt]='O';
						break;
					default:
						if(empty($AssignedDevices[$Tgt]) or !in_array($r->IskDvCode, $AssignedDevices[$Tgt])) {
							$span='<div ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">'.get_text('IskScoringDevice', 'Api', $r->IskDvCode).'</div>';
							if(empty($Messages[$Tgt][$r->IskDvCode]['IskScoringDevice']) or !in_array($span, $Messages[$Tgt][$r->IskDvCode]['IskScoringDevice'])) $Messages[$Tgt][$r->IskDvCode]['IskScoringDevice'][]=$span;
						}
						if(strlen(str_replace(' ', '', $r->Arrowstring))!=($End>$r->Ends ? $r->SO : $r->Arrows) ) {
							$Ends[$Tgt][$Let]='Z'; // Yellow, score in progress
							$ShowImport[$Tgt]=true;
							if($TgtsStatus[$Tgt]!='R' and $TgtsStatus[$Tgt]!='O') {
								$TgtsStatus[$Tgt]='Z';
							}
						} else {
							$Ends[$Tgt][$Let]='Y'; // Yellow, score in progress
							$ShowImport[$Tgt]=true;
							if(!$TgtsStatus[$Tgt] or strstr('BCZ', $TgtsStatus[$Tgt])) {
								$Clear=true;
								foreach($Ends[$Tgt] as $col) {
									if($col!='Y') $Clear=false;
								}
								if($Clear) {
									$TgtsStatus[$Tgt]='Y';
								} else {
									$TgtsStatus[$Tgt]='Z';
								}
							}
						}
				}
			}
		}
		$Error=0;
		break;
	case 'T':
		$MatchTarget=array();
		$TargetsInvolved=array();
		$Date=substr($Sequence, 1, 10);
		$Time=substr($Sequence, 11);
		// Gets the targets involved
		$SQL="select tgt1.*, tgt2.*,
				@ArBit:=(EvMatchArrowsNo & pow(2, if(FsMatchNo1=0, 0, floor(LOG(2, FsMatchNo1))))),
				if(@ArBit=0, EvFinArrows, EvElimArrows) Arrows, if(@ArBit=0, EvElimEnds, EvFinEnds) Ends, if(@ArBit=0, EvElimSO, EvFinSO) SO
			from (select TfTeam Entry1, TfArrowstring Arrowstring1, TfTieBreak TieBreak1, FsTarget+0 Target1, substr(FsLetter, length(FsTarget)+1, 1) Letter1, FsLetter FsLetter1, FsMatchNo FsMatchNo1, FsEvent FsEvent1, TfWinLose as Win1
				from FinSchedule
				inner join TeamFinals on FsEvent=TfEvent and TfTournament={$_SESSION['TourId']} and FsMatchNo=TfMatchNo
				where FsTournament={$_SESSION['TourId']} and FsTarget>'' and FsTeamEvent=1 and FsScheduledDate='$Date' and FsScheduledTime='$Time' and FsMatchNo%2=0) tgt1
			inner join (select TfTeam Entry2, TfArrowstring Arrowstring2, TfTieBreak TieBreak2, FsTarget+0 Target2, substr(FsLetter, length(FsTarget)+1, 1) Letter2, FsLetter FsLetter2, FsMatchNo FsMatchNo2, FsEvent FsEvent2, TfWinLose as Win2
				from FinSchedule
				inner join TeamFinals on FsEvent=TfEvent and TfTournament={$_SESSION['TourId']} and FsMatchNo=TfMatchNo
				where FsTournament={$_SESSION['TourId']} and FsTarget>'' and FsTeamEvent=1 and FsScheduledDate='$Date' and FsScheduledTime='$Time') tgt2
				on FsEvent1=FsEvent2 and FsMatchNo2=FsMatchNo1+1
			inner join Events on FsEvent1=EvCode and EvTeamEvent=1 and EvTournament={$_SESSION['TourId']}
			order by FsLetter1, Target1";
// 		$Out.='<q><![CDATA['.$SQL.']]></q>';
		$q=safe_r_sql($SQL);
		while($r=safe_fetch($q)) {
			$Tgt=($r->Target1==$r->Target2 ? $r->Target1 : "{$r->Target1}-{$r->Target2}");
			if($r->Win1 or $r->Win2) $MatchOvers[]=$Tgt;
			$Let1=($r->Letter1 ? $r->Letter1 : 'A');
			$Let2=($r->Letter2 && $r->Letter2!='A' ? $r->Letter2 : 'B');
			$Ends[$Tgt][$Let1]='';
			$Ends[$Tgt][$Let2]='';
			$MatchTarget[$r->FsEvent1][$r->FsMatchNo1]=array($Tgt, $Let1);
			$MatchTarget[$r->FsEvent1][$r->FsMatchNo2]=array($Tgt, $Let2);
			$TargetsInvolved[$r->Target1]=$Tgt;
			$TargetsInvolved[$r->Target2]=$Tgt;
			if(empty($TgtsStatus[$Tgt])) {
				$TgtsStatus[$Tgt]='';
			}

			$arrows1=strlen(trim(substr($r->Arrowstring1, ($End-1)*$r->Arrows, $r->Arrows)));
			$arrows2=strlen(trim(substr($r->Arrowstring2, ($End-1)*$r->Arrows, $r->Arrows)));
			if($End > $r->Ends) {
				$arrows1=strlen(trim($r->TieBreak1));
				$arrows2=strlen(trim($r->TieBreak2));
				$r->Arrows=$r->SO;
			}
			// Letter 1
			if(!$r->Entry1) {
				$Ends[$Tgt][$Let1]='G'; // Gray, not used
			} elseif($arrows1==$r->Arrows) {
				$Ends[$Tgt][$Let1]='B'; // Blue, OK
				if($TgtsStatus[$Tgt]!='C') $TgtsStatus[$Tgt]='B';
			} elseif($arrows1) {
				$Ends[$Tgt][$Let1]='C'; // Cyan, missing arrows
				$TgtsStatus[$Tgt]='C';
			}
			$Anomalies[$Tgt][$Let1]=preg_match('/ /sim', rtrim($r->Arrowstring1));
			// Letter 2
			if(!$r->Entry2) {
				$Ends[$Tgt][$Let2]='G'; // Gray, not used
			} elseif($arrows2==$r->Arrows) {
				$Ends[$Tgt][$Let2]='B'; // Blue, OK
				if($TgtsStatus[$Tgt]!='C') $TgtsStatus[$Tgt]='B';
			} elseif($arrows2) {
				$Ends[$Tgt][$Let2]='C'; // Cyan, missing arrows
				$TgtsStatus[$Tgt]='C';
			}
			$Anomalies[$Tgt][$Let2]=preg_match('/ /sim', rtrim($r->Arrowstring2));
			$Payloads[$Tgt]='ses='.$Sequence.'&dist='.$Dist.'&end='.$End.'&event='.$r->FsEvent1.'&matchno='.$r->FsMatchNo1.','.$r->FsMatchNo2;
		}

		// check which device should be attached to which target
		$SQL="Select IskDvCode, IskDvTarget+0 IntTarget from IskDevices where IskDvTournament={$_SESSION['TourId']} and IskDvState>0";
// 		$Out.='<q><![CDATA['.$SQL.']]></q>';
		$q=safe_r_sql($SQL);
		while($r=safe_fetch($q)) {
			if(empty($TargetsInvolved[$r->IntTarget]) or !isset($TgtsStatus[$TargetsInvolved[$r->IntTarget]])) {
				// device is not involved on this...
				continue;
			}
			if(empty($AssignedDevices[$TargetsInvolved[$r->IntTarget]]) or !in_array($r->IskDvCode, $AssignedDevices[$TargetsInvolved[$r->IntTarget]])) {
				$AssignedDevices[$TargetsInvolved[$r->IntTarget]][]=$r->IskDvCode;
			}
		}

		// check if the devices have sent something to the targets
		$SQL="Select IskData.*, concat(FsScheduledDate, ' ', FsScheduledTime) Scheduled, FsTarget+0 Target, substr(FsLetter, length(FsTarget)+1, 1) Letter,
				trim(IskDtArrowstring) Arrowstring, IskDvCode, IskDtType='T' CorrectSession, IskDvTarget,
				@ArBit:=(EvMatchArrowsNo & pow(2, if(IskDtMatchNo=0, 0, floor(LOG(2, IskDtMatchNo))))),
				if(@ArBit=0, EvFinArrows, EvElimArrows) Arrows, if(@ArBit=0, EvElimEnds, EvFinEnds) Ends, if(@ArBit=0, EvElimSO, EvFinSO) SO
			from IskData
			left join FinSchedule on FsTournament={$_SESSION['TourId']} and FsMatchNo=IskDtMatchNo and FsEvent=IskDtEvent and FsTeamEvent=1
			left join IskDevices on IskDvTournament=IskDtTournament and IskDvState>0 and IskDvDevice=IskDtDevice
			left join Events on IskDtEvent=EvCode and EvTeamEvent=1 and EvTournament={$_SESSION['TourId']}
			where IskDtTournament={$_SESSION['TourId']}
			order by IskDvTarget, IskDtEndNo";
// 		$Out.='<q><![CDATA['.$SQL.']]></q>';
		$q=safe_r_sql($SQL);
		while($r=safe_fetch($q)) {
			$SkipRest=false;
			$popId="data[$r->IskDtMatchNo][".($r->IskDtEvent ? $r->IskDtEvent : ':::')."][$r->IskDtTeamInd][$r->IskDtType][".($r->IskDtTargetNo ? $r->IskDtTargetNo : ':::')."][$r->IskDtDistance][$r->IskDtEndNo]=$r->IskDtArrowstring";
			// check the correct code of device
			$r->IskDvCode=($r->IskDvCode ? $r->IskDvCode : 'Unknown');

			// readdress the correct target for matches
			if(strstr('EQ', $r->IskDtType)) {
				$r->Target=intval(substr($r->IskDtTargetNo, 1));
			}
			if(!$r->IskDvCode) {
				// device is not known...
				$Msg.='<div ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">'.get_text('IskUnknownDevice', 'Api', $r->Target).'</div>';
			}
			if(empty($TargetsInvolved[$r->Target]) or !isset($TgtsStatus[$TargetsInvolved[$r->Target]])) {
				// device is not involved on this... but is sending scores
				$Msg.='<div ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">'.get_text('IskSpuriousDevice', 'Api', array($r->IskDvCode, $r->Target)).'</div>';
				$SkipRest=true;
			}
			if(!$r->CorrectSession) {
				// device is not sending Match scores
				$Msg.='<div ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">'.get_text('IskSpuriousScore-Device', 'Api', array($r->IskDvCode, $r->Target, $r->IskDtType . ' '.(strstr('EQ', $r->IskDtType) ? $r->IskDtTargetNo[0] : $r->IskDtEvent . ' '.$r->IskDtMatchNo. ' ('.$r->Scheduled.')'))).'</div>';
				$SkipRest=true;
			}
			if(empty($MatchTarget[$r->IskDtEvent][$r->IskDtMatchNo])) {
				$Msg.='<div ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">'.get_text('IskSpuriousScore-Match', 'Api', array($r->IskDvCode, $r->IskDtEvent . ' ' . $r->IskDtMatchNo. ' ('.$r->Scheduled.')')).'</div>';
				$SkipRest=true;
			}
			if($SkipRest) {
				continue;
			}

			$Tgt=$MatchTarget[$r->IskDtEvent][$r->IskDtMatchNo][0];
			$Let=$MatchTarget[$r->IskDtEvent][$r->IskDtMatchNo][1];

			if($r->Arrowstring) {
				// actually scoring something
				switch(true) {
					case ($Ends[$Tgt][$Let]=='G'): // this Position should not score at all!!!
						$Messages[$Tgt][$r->IskDvCode]['Empty'][]='<span ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">'. $Tgt.'</span>';
						$TgtsStatus[$Tgt]='R';
						break;
					case ($r->IskDtDistance!=$Dist): // scoring on a different distance
						$Messages[$Tgt][$r->IskDvCode]['Distance'][]='<span ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">'. $r->IskDtDistance.'</span>';
						$Ends[$Tgt][$Let]='R'; // Red, error condition
						$TgtsStatus[$Tgt]='R';
						break;
// 					case ($r->IskDvTarget!=$r->Target):
// 						$Messages[$r->Target][$r->IskDtEndNo]='<div ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">'.get_text('IskSpuriousScore-End', 'Api', array($r->IskDvCode, $r->IskDtEndNo)).'</div>';
// 						if($TgtsStatus[$r->Target]!='R') $TgtsStatus[$r->Target]='O';
// 						break;
					case ($r->IskDtEndNo!=$End):
						$Messages[$Tgt][$r->IskDvCode]['End'][]='<span ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">'. $r->IskDtEndNo.'</span>';
						$Ends[$Tgt][$Let]='O'; // Orange, error condition
						if($TgtsStatus[$Tgt]!='R') $TgtsStatus[$Tgt]='O';
						break;
					default:
						if(empty($AssignedDevices[$Tgt]) or !in_array($r->IskDvCode, $AssignedDevices[$Tgt])) {
							$Messages[$Tgt][$r->IskDvCode]['IskScoringDevice']='<div ondblclick="seeTarget(this)" title="'.get_text('IskTargetTitle', 'Api', $r->Target).'" value="'.$popId.'">'.get_text('', 'Api', $r->IskDvCode).'</div>';
						}
						if(strlen(str_replace(' ', '', $r->Arrowstring))!=($End>$r->Ends ? $r->SO : $r->Arrows) ) {
							$Ends[$Tgt][$Let]='Z'; // Yellow, score in progress
							$ShowImport[$Tgt]=true;
							if($TgtsStatus[$Tgt]!='R' and $TgtsStatus[$Tgt]!='O') {
								$TgtsStatus[$Tgt]='Z';
							}
						} else {
							$Ends[$Tgt][$Let]='Y'; // Yellow, score in progress
							$ShowImport[$Tgt]=true;
							if(!$TgtsStatus[$Tgt] or $TgtsStatus[$Tgt]=='C' or $TgtsStatus[$Tgt]=='B') {
								$TgtsStatus[$Tgt]='Y';
							}
						}
				}
			}
		}
		$Error=0;
		break;
	default:
		header('Content-Type: text/xml');
		die('<response error="'.$Error.'"/>');
}

// archers class, t= target class, d= devices assigned a= anomalies
foreach($Ends as $Tgt => $Let) {
	foreach($Let as $k => $v) {
		$Out.='<a id="'.$Tgt.'-'.$k.'" v="'.$v.'" '.(!empty($Anomalies[$Tgt][$k]) ? 'a="1"' : 'a="0"').'/>';
	}
}

// Target status and target messages
foreach($TgtsStatus as $Tgt => $status) {
	// target as a whole is in "currently scoring" if at least one is still scoring...
	if(!empty($Ends[$Tgt]) and in_array('Z', $Ends[$Tgt])) $status='Z';
	$Out.='<t id="'.$Tgt.'" a="'.(empty($Anomalies[$Tgt]) ? '0' : array_sum($Anomalies[$Tgt])).'" v="'.$status.'" d="'.(empty($AssignedDevices[$Tgt]) ? '' : implode(', ', $AssignedDevices[$Tgt])).'" o="'.(in_array($Tgt, $MatchOvers) ? 1 : 0).'" i="'.intval(!empty($ShowImport[$Tgt])).'"><![CDATA[';
	if(!empty($Messages[$Tgt])) {
		foreach($Messages[$Tgt] as $dv => $msgs) {
			foreach($msgs as $type => $letters) {
				if($type=='IskScoringDevice') {
					$Out.=$letters;
				} else {
					$Out.= '<div class="Spurious">' . get_text('IskSpuriousScore-'.$type, 'Api', array($dv, implode(', ', $letters)));
				}
			}
		}
		// G =
	}
	$Out.=']]></t>';
}

// Target payloads
foreach($Payloads as $Tgt => $status) {
	$Out.='<pl id="'.$Tgt.'"><![CDATA[' . $status . ']]></pl>';
}

$Locked=getModuleParameter('ISK', 'StickyEnds', array('SeqCode'=>$Sequence, 'Distance'=>$Dist, 'Ends'=>array()));
foreach(range(1, $_REQUEST['maxend']) as $i) {
	$Out.='<st id="sticky['.$i.']" checked="'.(($Locked['SeqCode']==$Sequence and $Locked['Distance']==$Dist and in_array($i, $Locked['Ends'])) ? '1' : '0').'" />';
}
if($Locked['SeqCode']==$Sequence and $Locked['Distance']==$Dist) {
	$Out.='<sm><![CDATA['.implode(', ', $Locked['Ends']).']]></sm>';
} else {
	$Out.='<sm><![CDATA['.get_text('StickyAlreadySet', 'Api').']]></sm>';
}

header('Content-Type: text/xml');
echo '<response error="'.$Error.'" ses="'.$Sequence.'" dis="'.$Dist.'" end="'.$End.'">';
echo $Out;
echo '<msg><![CDATA['.$Msg.']]></msg>';
echo '</response>';
