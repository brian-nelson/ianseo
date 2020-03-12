<?php
include_once('F2FGrid.inc.php');
include_once('UpdateFunctions.inc.php');

function UpdatePreOpen($TournamentID) {
	// checks if the structure of the database is consistent
//	CheckDbStructure();

	$DbVersion=GetParameter('DBUpdate');
	$rs = safe_r_SQL("SELECT ToDbVersion, ToType, ToNumSession FROM Tournament WHERE ToId='{$TournamentID}'");
	$row = safe_fetch($rs);
	$version = $row->ToDbVersion;

	if($version<'2010-11-26 15:50:00') {
		updateEnTimeStamp_20101126($TournamentID);
		to_save_version($TournamentID,'2010-11-26 15:50:00');
	}

	if($version<'2010-12-11 20:30:00') {
		recalculateIndividuals_20101211($TournamentID);
		to_save_version($TournamentID,'2010-12-11 20:30:00');
	}

	if($version<'2011-02-16 15:42:00') {
	// metto a posto il numero max di persone nei team
		calcMaxTeamPerson_20110216($TournamentID);

	// ricalcolo le rank a squadra
		recalculateTeamRanking_20110216($TournamentID);

		to_save_version($TournamentID,'2011-02-16 15:42:00');

	}

	if($version<'2011-03-09 14:38:00') {
		initTourGoldsXNineChars_20110309($TournamentID);
		to_save_version($TournamentID,'2011-03-09 14:38:00');
	}

	if($version<'2011-04-15 15:55:00') {
		RecalcFinRank_20110415($TournamentID);
		to_save_version($TournamentID,'2011-04-15 15:55:00');
	}

	if($version<'2012-01-11 10:20:00') {
		Update3DIta_20120111($TournamentID);
		to_save_version($TournamentID,'2012-01-11 10:20:00');
	}

	if($version<'2012-01-24 15:16:00') {
		$q=insertIntoGridForF2F_21($TournamentID);
		$rs2=safe_w_sql($q,false,array(1062));
		to_save_version($TournamentID,'2012-01-24 15:16:00');
	}

	if($version<'2012-05-18 07:10:00') {
		safe_w_sql("update Divisions set DivWaDivision=DivId, DivRecDivision=DivId where DivWaDivision='' or DivRecDivision=''");
		safe_w_sql("update Classes set ClWaClass=ClId, ClRecClass=ClId where ClWaClass='' or ClRecClass=''");
		safe_w_sql("update Events set EvWaCategory=EvCode, EvRecCategory=EvCode where EvWaCategory='' or EvRecCategory=''");
		to_save_version($TournamentID,'2012-05-18 07:10:00');
	}

	if($version<'2013-12-19 12:30:00') {
		// Updating DistanceInformation
		require_once('Modules/Sets/lib.php');
		require_once('Tournament/Fun_ManSessions.inc.php');
		$Distances=getDistanceArrays($row->ToType);
		$q=safe_r_sql("select SesAth4Target, SesOrder from Session where SesTournament=$TournamentID");
		while($r=safe_fetch($q)) {
			CreateDistanceInformation($TournamentID, $Distances, 0, $r->SesAth4Target, $r->SesOrder);
		}
		to_save_version($TournamentID,'2013-12-19 12:30:00');
	}

	if($version<'2014-03-22 14:00:00') {
		UpdateWinLose_20140322($TournamentID);
		to_save_version($TournamentID,'2014-03-22 14:00:00');
	}

	if($version<'2014-04-01 00:00:00') {
		UpdateItaRules_20140401($TournamentID);
		to_save_version($TournamentID,'2014-04-01 00:00:00');
	}

	if($version<'2014-05-23 16:00:00') {
		safe_w_sql("update Scheduler
			left join Session on SchTournament=SesTournament and SchSesOrder=SesOrder and SchSesType=SesType
			set SchDay=date(SchDateStart), SchStart=time(SchDateStart), SchDuration=TIMESTAMPDIFF(MINUTE, SchDateStart, SchDateEnd), SchText=SchDescr, SchTitle=SesName
			where SchTournament=$TournamentID");
		$q=safe_r_sql("select * from Scheduler where SchTournament=$TournamentID and SchDay>0 and SchStart>0 order by SchDay, SchStart, SchOrder desc");
		$oldKeys=array();
		while($r=safe_fetch($q)) {
			$good=true;
			$key=$r->SchDay.'|'.$r->SchStart;
			while(in_array($key, $oldKeys)) {
				$key++;
				$good=false;
			}
			$oldKeys[]=$key;
			if(!$good) {
				$SQL="update Scheduler set SchStart='".substr($key,-8)."' where SchDay='$r->SchDay' and SchStart='$r->SchStart' and SchDuration='$r->SchDuration' and SchSesOrder=$r->SchSesOrder and SchOrder=$r->SchOrder and SchSesType='$r->SchSesType' limit 1";
				safe_w_sql($SQL);
			}
		}
		to_save_version($TournamentID,'2014-04-01 00:00:00');
	}

	if($version<'2014-11-16 11:35:00') {
		UpdateArrowPosition_20141115($TournamentID);
		to_save_version($TournamentID,'2014-11-16 11:35:00');
	}

	if($version<'2015-03-04 21:30:01') {
		UpdateToOptions_20150304($TournamentID);
		to_save_version($TournamentID, '2015-03-04 21:30:00');
	}

	if($version<'2015-04-16 18:30:02') {
		UpdateSetPointsByEnd_20150416($TournamentID);
		to_save_version($TournamentID, '2015-04-16 18:30:02');
	}

	if($version<'2016-03-22 08:30:01') {
		UpdateSessionsFromAgileModule_20160322($TournamentID);
		to_save_version($TournamentID, '2016-03-22 08:30:01');
	}

	if($version<'2017-06-14 17:29:01') {
		updateEliminationEvents_20170530($TournamentID);
		to_save_version($TournamentID, '2017-06-14 17:29:01');
	}
	if($version<'2018-01-14 12:29:03') {
		updateEliminationEvents_20180114($TournamentID);
		to_save_version($TournamentID, '2018-01-14 12:29:03');
	}

	if($version<'2018-01-31 12:29:03') {
		safe_w_sql("update Tournament set ToLocRule='NFAA' where ToLocRule='VEGAS'");
		to_save_version($TournamentID, '2018-01-31 12:29:03');
	}

	if($version<'2018-05-03 14:36:00') {
		updateArrowPositions_20180503($TournamentID);
		to_save_version($TournamentID, '2018-05-03 14:36:00');
	}

	if($version<'2018-06-24 22:07:02') {
		updateArrowTimestamp_20180624($TournamentID);
		to_save_version($TournamentID, '2018-06-24 22:07:02');
	}

	to_save_version($TournamentID, $DbVersion);
}

function to_save_version($TournamentID,$newversion) {
	safe_w_sql("UPDATE Tournament SET ToDbVersion='{$newversion}' WHERE ToId='{$TournamentID}'");
}

function CheckDbStructure() {
	global $CFG;
	if(!file_exists($CFG->DOCUMENT_PATH.'Common/DbStruct.inc.php')
		or !($IanseoTables=unserialize(file_get_contents($CFG->DOCUMENT_PATH.'Common/DbStruct.inc.php')))) {
		PrintCrackError('', 'InvalidIanseo');
	}

	if($IanseoTables->ProgramVersion!=ProgramVersion or $IanseoTables->ProgramName!=ProgramName) PrintCrackError('', 'PrgVersMismatch');

	foreach($IanseoTables->Data as $Table => $data) {
		$passed=false;
		$t=safe_w_sql("show create table `$Table`", '', array(1146));
		if($u=safe_fetch($t)) {
			if(!empty($u->{'Create View'})) {
				$u->{'Create View'}=preg_replace('/CREATE .* `'.$u->{'View'}.'`/', 'CREATE VIEW `'.$u->{'View'}.'`', $u->{'Create View'});
				if($u->{'Create View'}!=$data->{'Create View'}) {
					debug_svela('REBUILD VIEW: STOPS HERE AS A DEVELOPMENT FEATURE');
					// rebuild the view
					safe_w_sql("drop view $Table");
					safe_w_sql($data->{'Create View'});
				}
			} else {
				if($u->{'Create Table'}!=$data->{'Create Table'}) {
					// recreates the table
					debug_svela('RECREATES TABLE: STOPS HERE AS A DEVELOPMENT FEATURE');
					safe_w_sql("alter table `$Table` rename `old_$Table`");
					safe_w_sql($data->{'Create Table'});
					$old=safe_r_sql("show fields in `old_$Table`");
					$new=safe_r_sql("show fields in `$Table`");
					$NewTable=array();
					while($newRow=safe_fetch($new)) $NewTable[]=$newRow->Field;
					while($oldRow=safe_fetch($old)) if(!in_array($oldRow->Field, $NewTable)) PrintCrackError('', 'DbTooMessy');
					safe_w_sql("insert into `$Table` (`".implode('`, `', $NewTable)."`) select `".implode('`, `', $NewTable)."` from `old_$Table`");
//					safe_w_sql("drop table `old_$Table`");
				}

			}
		} else {
			PrintCrackError('', 'InvalidIanseo');
		}
	}
}

