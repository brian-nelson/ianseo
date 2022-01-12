<?php

/*
// this page is specific for the target assignment for french D1

We need to get all matches based on the scheduler

*/

require_once(dirname(__FILE__) . '/config.php');
require_once('Common/pdf/IanseoPdf.php');

if($AllInOne=getModuleParameter('FFTA', 'D1AllInOne', 0)) {
	$Comps=array();
	foreach(getModuleParameter('FFTA', 'ConnectedCompetitions', array()) as $Code) {
		$Comps[]=getIdFromCode($Code);
	}
	$Filter=implode(',',$Comps);
	$SQL="select *
		from (
		    select FsMatchNo LeftMatchNo, FsEvent LeftEvent, EvEventName EventName, EvCode EventCode, FSTeamEvent LeftTeamEvent, FSScheduledDate LeftDate, FSScheduledTime LeftTime, date_format(FSScheduledTime,'%k:%i') MatchTime, if(FSTeamEvent=1, FSTarget, FSLetter) as LeftTarget, CoName as LeftName
			from FinSchedule
		    inner join Events on EvCode=left(FSEvent,3) and EvTeamEvent=FSTeamEvent and EvTournament=FSTournament
			left join (
			    select TfMatchNo, TfEvent, TfTournament, CoCode, CoName
			    from TeamFinals
			    inner join Countries on CoId=TfTeam and CoTournament=TfTournament
			    where TfTournament in ($Filter)
			    ) Team on TfMatchNo=FSMatchNo and TfEvent=FSEvent and TfTournament=FSTournament and FSTeamEvent=1
			where FSScheduledDate!=0 and FSMatchNo%2=0 and FSTournament in ($Filter) and FsTarget>0 
			) LO
		left join (
		    select FsMatchNo RightMatchNo, FsEvent RightEvent, FSTeamEvent RightTeamEvent, FSScheduledDate RightDate, FSScheduledTime RightTime, if(FSTeamEvent=1, FSTarget, FSLetter) as RightTarget, CoName as RightName
			from FinSchedule
			left join (
			    select TfMatchNo, TfEvent, TfTournament, CoCode, CoName
			    from TeamFinals
			    inner join Countries on CoId=TfTeam and CoTournament=TfTournament
			    where TfTournament in ($Filter)
			    ) Team on TfMatchNo=FSMatchNo and TfEvent=FSEvent and TfTournament=FSTournament and FSTeamEvent=1
			where FSScheduledDate!=0 and FSMatchNo%2=1 and FSTournament in ($Filter) and FsTarget>0  
			) RO on RightMatchNo=LeftMatchno+1 and RightEvent=LeftEvent and RightTeamEvent=LeftTeamEvent and RightDate=LeftDate and RightTime=LeftTime
		order by EventCode, LeftDate, LeftTime, LeftMatchno, LeftEvent, LeftTeamEvent desc";

	$q=safe_r_sql($SQL);

	$OldEvent='';
	$OldDate='';
	$OldTime='';
	$OldTimeInd='';
	$CellH=4;

	$pdf=new IanseoPdf(get_text('StartListbyTarget', 'Tournament'));
	$pdf->startPageGroup();

	while($r=safe_fetch($q)) {
		if($OldEvent!=$r->EventCode) {
			$pdf->AddPage();
			$pdf->ln($CellH);
			$pdf->cell(0, 5, $r->EventName, 1,1,'C',true);
			$OldEvent=$r->EventCode;
			$OldDate='';
			$OldTime='';
			$OldTimeInd='';
		}

		if(($OldDate!=$r->LeftDate or $OldTime!=$r->LeftTime) and !$pdf->samePage(8*$CellH + 2)) {
			$pdf->AddPage();
			$pdf->ln($CellH);
			$pdf->cell(0, 5, $r->EventName." (".get_text('Continue').")", 1,1,'C',true);
			$OldTime='';
			$OldTimeInd='';
			$OldDate='';
		}

		if($OldDate!=$r->LeftDate) {
			$pdf->ln(2);
			$pdf->cell(15, $CellH, $r->LeftDate);
			$OldDate=$r->LeftDate;
			$OldTime='';
			$OldTimeInd='';
			$firstRow=true;
		}

		if($OldTime!=$r->LeftTime and !$firstRow) {
			$pdf->ln(1);
		}
		$pdf->SetX(25);
		$pdf->cell(10, $CellH, $OldTime==$r->LeftTime ? '' : $r->MatchTime);
		$pdf->cell(5, $CellH, ltrim($r->LeftTarget, '0'));
		$pdf->cell(60, $CellH, $r->LeftName);
		$pdf->cell(5, $CellH, ltrim($r->RightTarget,'0'));
		$pdf->cell(60, $CellH, $r->RightName);
		$OldTime=$r->LeftTime;

		$pdf->ln();
		$firstRow=false;
	}
} else {
	$SQL="select *
		from (
		    select FsMatchNo LeftMatchNo, FsEvent LeftEvent, EvEventName EventName, EvCode EventCode, FSTeamEvent LeftTeamEvent, FSScheduledDate LeftDate, FSScheduledTime LeftTime, date_format(FSScheduledTime,'%k:%i') MatchTime, if(FSTeamEvent=1, FSTarget, FSLetter) as LeftTarget, if(FSTeamEvent=1, CoName, concat_ws(' ',EnFirstName,EnName)) as LeftName
			from FinSchedule
		    inner join Events on EvCode=left(FSEvent,3) and EvTeamEvent=FSTeamEvent and EvTournament=FSTournament
			left join (
			    select FinMatchNo, FinEvent, FinTournament, EnCountry, EnFirstName, EnName
			    from Finals
				inner join Entries on EnId=FinAthlete
			    where FinTournament={$_SESSION['TourId']}
			    ) Indiv on FinMatchNo=FSMatchNo and FinEvent=FSEvent and FinTournament=FSTournament and FSTeamEvent=0
			left join (
			    select TfMatchNo, TfEvent, TfTournament, CoCode, CoName
			    from TeamFinals
			    inner join Countries on CoId=TfTeam and CoTournament=TfTournament
			    where TfTournament={$_SESSION['TourId']}
			    ) Team on TfMatchNo=FSMatchNo and TfEvent=FSEvent and TfTournament=FSTournament and FSTeamEvent=1
			where FSScheduledDate!=0 and FSMatchNo%2=0 and FSTournament={$_SESSION['TourId']} and FsTarget>0 
			) LO
		left join (
		    select FsMatchNo RightMatchNo, FsEvent RightEvent, FSTeamEvent RightTeamEvent, FSScheduledDate RightDate, FSScheduledTime RightTime, if(FSTeamEvent=1, FSTarget, FSLetter) as RightTarget, if(FSTeamEvent=1, CoName, concat_ws(' ',EnFirstName,EnName)) as RightName
			from FinSchedule
			left join (
			    select FinMatchNo, FinEvent, FinTournament, EnCountry, EnFirstName, EnName
			    from Finals
				inner join Entries on EnId=FinAthlete
			    where FinTournament={$_SESSION['TourId']}
			    ) Indiv on FinMatchNo=FSMatchNo and FinEvent=FSEvent and FinTournament=FSTournament and FSTeamEvent=0
			left join (
			    select TfMatchNo, TfEvent, TfTournament, CoCode, CoName
			    from TeamFinals
			    inner join Countries on CoId=TfTeam and CoTournament=TfTournament
			    where TfTournament={$_SESSION['TourId']}
			    ) Team on TfMatchNo=FSMatchNo and TfEvent=FSEvent and TfTournament=FSTournament and FSTeamEvent=1
			where FSScheduledDate!=0 and FSMatchNo%2=1 and FSTournament={$_SESSION['TourId']} and FsTarget>0  
			) RO on RightMatchNo=LeftMatchno+1 and RightEvent=LeftEvent and RightTeamEvent=LeftTeamEvent and RightDate=LeftDate and RightTime=LeftTime
		order by EventCode, LeftDate, LeftMatchno, LeftEvent, LeftTeamEvent desc, LeftTime";

	$q=safe_r_sql($SQL);

	$OldEvent='';
	$OldDate='';
	$OldTime='';
	$OldTimeInd='';
	$CellH=3.75;

	$pdf=new IanseoPdf(get_text('StartListbyTarget', 'Tournament'));
	$pdf->startPageGroup();

	while($r=safe_fetch($q)) {
		if($OldEvent!=$r->EventCode) {
			$pdf->AddPage();
			$pdf->ln($CellH);
			$pdf->cell(0, 5, $r->EventName, 1,1,'C',true);
			$OldEvent=$r->EventCode;
			$OldDate='';
			$OldTime='';
			$OldTimeInd='';
		}
		if($OldDate!=$r->LeftDate) {
			$pdf->ln(2);
			$pdf->cell(0, $CellH, $r->LeftDate, 1,1,'C',true);
			$OldDate=$r->LeftDate;
			$OldTime='';
			$OldTimeInd='';
		}
		if($r->LeftTeamEvent) {
			if(!$pdf->samePage(18)) {
				$pdf->AddPage();
				$pdf->ln($CellH);
				//$pdf->cell(0, 0, $r->LeftDate, 1,1,'C',true);
				//$OldDate=$r->LeftDate;
				$OldTime='';
				$OldTimeInd='';
			}
			$pdf->ln(2);
			$Y=$pdf->GetY();
			// Date and time of event is in the left column
			$pdf->cell(10, $CellH, $OldTime==$r->LeftTime ? '' : $r->MatchTime);
			$pdf->cell(5, $CellH, ltrim($r->LeftTarget, '0'));
			$pdf->cell(40, $CellH, $r->LeftName);
			$pdf->ln($CellH);
			$pdf->cell(10, $CellH, '');
			$pdf->cell(5, $CellH, ltrim($r->RightTarget,'0'));
			$pdf->cell(40, $CellH, $r->RightName);
			if($OldTime!=$r->LeftTime) {
				$OldTime=$r->LeftTime;
				$OldTimeInd='';
			}
		} else {
			$pdf->SetXY(90, $Y);
			$pdf->cell(10, $CellH, $OldTimeInd==$r->LeftTime ? '' : $r->MatchTime);
			$pdf->cell(10, $CellH, ltrim($r->LeftTarget,'0'));
			$pdf->cell(40, $CellH, $r->LeftName);
			$pdf->cell(10, $CellH, ltrim($r->RightTarget,'0'));
			$pdf->cell(40, $CellH, $r->RightName);
			$pdf->ln($CellH);
			$Y+=$CellH;
			if($OldTimeInd!=$r->LeftTime) {
				$OldTimeInd=$r->LeftTime;
			}
		}
	}
}

$pdf->output();
