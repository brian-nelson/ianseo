<?php

require_once(dirname(dirname(__FILE__)).'/config.php');
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

// fetches an array of alla available ends
$EndsStatus=array();
$TgtsStatus=array();
$Targets=array();
$Messages=array();
$Payloads=array();
$Out='';
$AssignedDevices=array();
switch($Sequence[0]) {
	case 'Q':
		// gets the targets
		$SqlTargets="select distinct QuTarget Target from Entries inner join Qualifications on EnId=QuId and QuSession={$Sequence[2]} where EnTournament={$_SESSION['TourId']} order by Target";
		$q=safe_r_sql($SqlTargets);
		while($r=safe_Fetch($q)) {
			$Targets[]=$r->Target;
			$TgtsStatus[$r->Target]='';
		}
		// prepares an array with all the available ends and the values if any
		$SQL="select AtTarget Target, AtLetter Letter, AtTargetNo
			from AvailableTarget
			left join (select QuTargetNo, EnTournament
					from Qualifications
					inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']}
			  		where QuSession={$Sequence[2]}
					) Quals on AtTargetNo=QuTargetNo and AtTournament=EnTournament
			where AtTournament={$_SESSION['TourId']} and AtSession={$Sequence[2]} and AtTarget in (".implode(',', $Targets).")
			order by AtTargetNo";
		$q=safe_r_sql($SQL);
		while($r=safe_fetch($q)) {
			$Ends[$r->Target][$r->Letter]='';
			$Payloads[$r->Target]='ses='.$Sequence.'&dist='.$Dist.'&end='.$End.'&target='.$r->Target;
		}
		$Error=0;
		break;
	case 'E':
		break;
	case 'I':
		$Date=substr($Sequence, 1, 10);
		$Time=substr($Sequence, 11);
		$SQL="select *
			from (select FsTarget+0 Target1, substr(FsLetter, length(FsTarget)+1, 1) Letter1, FsLetter FsLetter1, FsMatchNo FsMatchNo1, FsEvent FsEvent1
				from FinSchedule
				inner join Finals on FsEvent=FinEvent and FsTournament=FinTournament and FsMatchNo=FinMatchNo
				left join Entries on FinAthlete=EnId
				where FsTournament={$_SESSION['TourId']} and FsTarget>'' and FsTeamEvent=0 and FsScheduledDate='$Date' and FsScheduledTime='$Time' and FsMatchNo%2=0) tgt1
			inner join (select FsTarget+0 Target2, substr(FsLetter, length(FsTarget)+1, 1) Letter2, FsLetter FsLetter2, FsMatchNo FsMatchNo2, FsEvent FsEvent2
				from FinSchedule
				inner join Finals on FsEvent=FinEvent and FsTournament=FinTournament and FsMatchNo=FinMatchNo
				left join Entries on FinAthlete=EnId
				where FsTournament={$_SESSION['TourId']} and FsTarget>'' and FsTeamEvent=0 and FsScheduledDate='$Date' and FsScheduledTime='$Time') tgt2
				on FsEvent1=FsEvent2 and FsMatchNo2=FsMatchNo1+1
			order by FsLetter1, Target1";
		$q=safe_r_sql($SQL);
		while($r=safe_fetch($q)) {
			$Tgt=($r->Target1==$r->Target2 ? $r->Target1 : "{$r->Target1}-{$r->Target2}");
			$Ends[$Tgt][$r->Letter1 ? $r->Letter1 : 'A']='';
			$Ends[$Tgt][$r->Letter2 ? $r->Letter2 : 'B']='';
			$Payloads[$Tgt]='ses='.$Sequence.'&dist='.$Dist.'&end='.$End.'&event='.$r->FsEvent1.'&matchno='.$r->FsMatchNo1.','.$r->FsMatchNo2;
		}
		$Error=0;
		break;
	case 'T':
		$Date=substr($Sequence, 1, 10);
		$Time=substr($Sequence, 11);
		$SQL="select *
			from (select FsTarget+0 Target1, substr(FsLetter, length(FsTarget)+1, 1) Letter1, FsLetter FsLetter1, FsMatchNo FsMatchNo1, FsEvent FsEvent1
				from FinSchedule
				inner join TeamFinals on FsEvent=TfEvent and FsTournament=TfTournament and FsMatchNo=TfMatchNo
				left join Countries on TfTeam=CoId and CoTournament=FsTournament
				where FsTournament={$_SESSION['TourId']} and FsTarget>'' and FsTeamEvent=1 and FsScheduledDate='$Date' and FsScheduledTime='$Time' and FsMatchNo%2=0) tgt1
			inner join (select FsTarget+0 Target2, substr(FsLetter, length(FsTarget)+1, 1) Letter2, FsLetter FsLetter2, FsMatchNo FsMatchNo2, FsEvent FsEvent2
				from FinSchedule
				inner join TeamFinals on FsEvent=TfEvent and FsTournament=TfTournament and FsMatchNo=TfMatchNo
				left join Countries on TfTeam=CoId and CoTournament=FsTournament
				where FsTournament={$_SESSION['TourId']} and FsTarget>'' and FsTeamEvent=1 and FsScheduledDate='$Date' and FsScheduledTime='$Time') tgt2
				on FsEvent1=FsEvent2 and FsMatchNo2=FsMatchNo1+1
			order by FsLetter1, Target1";
		$q=safe_r_sql($SQL);
		while($r=safe_fetch($q)) {
			$Tgt=($r->Target1==$r->Target2 ? $r->Target1 : "{$r->Target1}-{$r->Target2}");
			$Ends[$Tgt][$r->Letter1 ? $r->Letter1 : 'A']='';
			$Ends[$Tgt][$r->Letter2 && $r->Letter2!='A' ? $r->Letter2 : 'B']='';
			$Payloads[$Tgt]='ses='.$Sequence.'&dist='.$Dist.'&end='.$End.'&event='.$r->FsEvent1.'&matchno='.$r->FsMatchNo1.','.$r->FsMatchNo2;
		}
		$Error=0;
		break;
	default:
		header('Content-Type: text/xml');
		die('<response error="'.$Error.'"/>');
}

// archers class, t= target class, d= devices assigned
foreach($Ends as $Tgt => $Let) {
	$Out.='<div class="TargetContainer"><div class="TargetTitle" value="'.$Payloads[$Tgt].'" id="t-'.$Tgt.'" title="'.get_text('IskTargetTitle', 'Api', $Tgt).'" ondblclick="seeTarget(this)"><span class="DisableSelection">'.get_text('IskTargetTitle', 'Api', $Tgt).'</span><img class="TargetInfoImg ContextMenuDiv" onClick="seeTarget(parentNode)"/></div>';
	$Out.='<div class="TargetDevices">'.get_text('IskDeviceAssigned', 'Api').': <span id="d-'.$Tgt.'"></span></div>';
	$Out.='<div class="TargetLetters">';
	foreach($Let as $k => $v) {
		$Out.='<span id="l-'.$Tgt.'-'.$k.'">'.$k.'</span>';
	}
	$Out.='</div>';
	$Out.='<div class="TargetMessage" id="m-'.$Tgt.'"></div>';
	$Out.='<div align="center"><input class="TgtImport ClickableDiv" id="i-'.$Tgt.'" type="button" onclick="dataImport(this)" value="'.get_text('CmdImport', 'Api').'"></div>';
	$Out.='</div>';
}

$m='';
$Locked=getModuleParameter('ISK', 'StickyEnds', array('SeqCode'=>$Sequence, 'Distance'=>$Dist, 'Ends'=>array()));

if(!($Locked['SeqCode']==$Sequence and $Locked['Distance']==$Dist)) {
	$m= get_text('StickyAlreadySet', 'Api');
}

header('Content-Type: text/xml');
echo '<response error="'.$Error.'">';
echo '<html><![CDATA['.$Out.']]></html>';
echo '<sticky><![CDATA[';
foreach(range(1, $_REQUEST['maxend']) as $i) {
	echo $i.'<input onclick="toggleSticky(this)" type="checkbox" id="sticky['.$i.']"'.(($Locked['SeqCode']==$Sequence and $Locked['Distance']==$Dist and in_array($i, $Locked['Ends'])) ? ' checked="checked"' : '').'>&nbsp;&nbsp;';
}

echo ']]></sticky>';
echo '<sm><![CDATA['.$m.']]></sm>';
if(!empty($_SESSION['debug'])) {
	echo '<debug><![CDATA['.$SQL.']]></debug>';
}
echo '</response>';
