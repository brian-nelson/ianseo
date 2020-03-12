<?php
include_once('F2FGrid.inc.php');
include_once('UpdateFunctions.inc.php');

/*
ogni step viene salvato separatamente al proprio numero di versione...
creato un numero di versione DB apposito...
Se la versione è troppo vecchia include i vecchi file

*/

if($version <= '2011-01-01 00:00:00') require_once('Common/UpdateDb-2010.inc.php');
if($version <= '2012-01-01 00:00:00') require_once('Common/UpdateDb-2011.inc.php');
if($version <= '2013-01-01 00:00:00') require_once('Common/UpdateDb-2012.inc.php');
if($version <= '2014-01-01 00:00:00') require_once('Common/UpdateDb-2013.inc.php');
if($version <= '2015-01-01 00:00:00') require_once('Common/UpdateDb-2014.inc.php');
if($version <= '2016-01-01 00:00:00') require_once('Common/UpdateDb-2015.inc.php');
if($version <= '2017-01-01 00:00:00') require_once('Common/UpdateDb-2016.inc.php');
if($version <= '2018-01-01 00:00:00') require_once('Common/UpdateDb-2017.inc.php');
if($version <= '2019-01-01 00:00:00') require_once('Common/UpdateDb-2018.inc.php');

if($version<'2019-01-14 12:29:02') {
	$q="ALTER TABLE `AvailableTarget` ADD AtSession tinyint unsigned NOT NULL, ADD AtTarget int not null, add AtLetter varchar(1) not null, add index (AtTournament, AtSession, AtTarget, AtLetter)";
	$r=safe_w_sql($q,false,array(1146, 1060));

	// updates the existant things
	safe_w_sql("update AvailableTarget set AtSession=left(AtTargetNo,1), AtTarget=substr(AtTargetNo, 2, 3), AtLetter=right(AtTargetNo,1)");

	db_save_version('2019-01-14 12:29:02');
}

if($version<'2019-03-21 12:29:02') {
	$q="drop TABLE if exists `TeamDavis`";
	$r=safe_w_sql($q,false,array(1146, 1060));

	$q="CREATE TABLE IF NOT EXISTS `TeamDavis` (
		`TeDaTournament` int NOT NULL,
		`TeDaEvent` varchar(4) NOT NULL,
		`TeDaTeam` varchar(10) NOT NULL,
		`TeDaSubTeam` int NOT NULL,
		`TeDaBonusPoints` int NOT NULL,
		`TeDaMainPoints` int NOT NULL,
		`TeDaWinPoints` int NOT NULL,
		`TeDaLoosePoints` int NOT NULL,
		`TeDaDateTime` datetime NOT NULL,
		PRIMARY KEY (TeDaTournament, TeDaEvent, TeDaTeam, TeDaSubTeam),
		index (TeDaTournament, TeDaEvent, TeDaMainPoints, TeDaWinPoints, TeDaLoosePoints)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2019-03-21 12:29:02');
}

if($version<'2019-04-03 20:29:03') {
	$q="drop TABLE if exists `OdfDocuments`";
	$r=safe_w_sql($q,false,array(1146, 1060));

	$q="CREATE TABLE IF NOT EXISTS `OdfDocuments` (
	`OdfDocTournament` int NOT NULL,
	`OdfDocCode` varchar(34) NOT NULL,
	`OdfDocType` varchar(22) NOT NULL,
	`OdfDocSubType` varchar(10) NOT NULL,
	`OdfDocVersion` int NOT NULL,
	`OdfDocDate` date NOT NULL,
	`OdfDocLogicalDate` date NOT NULL,
	`OdfDocTime` time NOT NULL,
	`OdfDocStatus` varchar(15) NOT NULL,
	`OdfDocSendStatus` tinyint NOT NULL,
	`OdfDocSendRetries` tinyint NOT NULL,
	PRIMARY KEY (OdfDocTournament, OdfDocCode, OdfDocType, OdfDocSubType),
	index (OdfDocTournament, OdfDocDate, OdfDocTime),
	index (OdfDocTournament, OdfDocSendStatus, OdfDocSendRetries, OdfDocDate, OdfDocTime)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8";

	$t=safe_w_sql("select @@version as MysqlVersion");
	if($u=safe_fetch($q)) {
		$MyVersion=preg_split('/[.-]/', $u->MysqlVersion);
		$MinVersion=$MyVersion[0]*1000000 + $MyVersion[1]*1000 + $MyVersion[2];
		if($MinVersion>=5006004) {
			$q="CREATE TABLE IF NOT EXISTS `OdfDocuments` (
			`OdfDocTournament` int NOT NULL,
			`OdfDocCode` varchar(34) NOT NULL,
			`OdfDocType` varchar(22) NOT NULL,
			`OdfDocSubType` varchar(10) NOT NULL,
			`OdfDocVersion` int NOT NULL,
			`OdfDocDate` date NOT NULL,
			`OdfDocLogicalDate` date NOT NULL,
			`OdfDocTime` time(3) NOT NULL,
			`OdfDocStatus` varchar(15) NOT NULL,
			`OdfDocSendStatus` tinyint NOT NULL,
			`OdfDocSendRetries` tinyint NOT NULL,
			PRIMARY KEY (OdfDocTournament, OdfDocCode, OdfDocType, OdfDocSubType),
			index (OdfDocTournament, OdfDocDate, OdfDocTime),
			index (OdfDocTournament, OdfDocSendStatus, OdfDocSendRetries, OdfDocDate, OdfDocTime)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		}
	}
	$r=safe_w_sql($q,false,array(1060));

	$q="drop TABLE if exists `OdfTranslations`";
	$r=safe_w_sql($q,false,array(1146, 1060));

	$q="CREATE TABLE IF NOT EXISTS `OdfTranslations` (
		`OdfTrTournament` int NOT NULL,
		`OdfTrInternal` varchar(10) NOT NULL,
		`OdfTrType` varchar(10) NOT NULL,
		`OdfTrOdfCode` varchar(34) NOT NULL,
		`OdfTrIanseo` varchar(34) NOT NULL,
		primary key (OdfTrTournament, OdfTrInternal, OdfTrIanseo, OdfTrType),
		index (OdfTrTournament, OdfTrInternal, OdfTrOdfCode, OdfTrType)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1060));

	$q="ALTER TABLE `Events` ADD `EvOdfCode` varchar(34) NOT NULL";
	$r=safe_w_sql($q,false,array(1146, 1060));

	$q="ALTER TABLE `Entries` ADD `EnOdfShortname` varchar(18) NOT NULL";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2019-04-03 20:29:03');
}

if($version<'2019-04-07 12:29:02') {
	$q="ALTER TABLE `Session` ADD `SesOdfCode` varchar(5) NOT NULL, add SesOdfPeriod varchar(5) not null";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2019-04-07 12:29:02');
}

if($version<'2019-04-07 15:29:02') {
	$q="ALTER TABLE `Session` ADD `SesOdfVenue` varchar(5) NOT NULL, add SesOdfLocation varchar(5) not null";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2019-04-07 15:29:02');
}

if($version<'2019-04-08 09:29:02') {
	$q="ALTER TABLE OdfTranslations drop primary key, drop index OdfTrTournament";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="ALTER TABLE OdfTranslations 
    	ADD `OdfTrLanguage` varchar(3) NOT NULL, 
    	add primary key (OdfTrTournament, OdfTrInternal, OdfTrType, OdfTrIanseo, OdfTrLanguage), 
    	add index (OdfTrTournament, OdfTrLanguage, OdfTrInternal, OdfTrType, OdfTrIanseo)";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2019-04-08 09:29:02');
}

if($version<'2019-04-08 11:29:02') {
	$q="ALTER TABLE `OdfTranslations` change OdfTrOdfCode OdfTrOdfCode varchar(50) NOT NULL";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2019-04-08 11:29:02');
}

if($version<'2019-04-08 15:29:02') {
	$q="ALTER TABLE `OdfDocuments` add OdfDocExtra text NOT NULL";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2019-04-08 15:29:02');
}

if($version<'2019-04-08 21:29:02') {
	$q="drop TABLE if exists `OdfScheduleStatus`";
	$r=safe_w_sql($q,false,array(1146, 1060));

	$q="CREATE TABLE IF NOT EXISTS `OdfScheduleStatus` (
		`OdfSchStTournament` int NOT NULL,
		`OdfSchStKey` varchar(25) NOT NULL,
		`OdfSchStStatus` varchar(15) NOT NULL,
		`OdfSchStTimestamp` datetime NOT NULL,
		primary key (OdfSchStTournament, OdfSchStKey)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1060));

	$q="ALTER TABLE `Session` change SesStatus SesStatus varchar(15) NOT NULL";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2019-04-08 21:29:02');
}

if($version<'2019-04-10 21:29:03') {
	$q="drop TABLE if exists `OdfMessageStatus`";
	$r=safe_w_sql($q,false,array(1146, 1060));

	$q="CREATE TABLE IF NOT EXISTS `OdfMessageStatus` (
		`OmsTournament` int NOT NULL,
		`OmsType` varchar(5) NOT NULL,
		`OmsKey` varchar(34) NOT NULL,
		`OmsStatus` varchar(15) NOT NULL,
		`OmsTimestamp` datetime NOT NULL,
		primary key (OmsTournament, OmsType, OmsKey)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1060));

	safe_w_sql("insert into OdfMessageStatus select OdfSchStTournament, 'SCHED', OdfSchStKey, OdfSchStStatus, OdfSchStTimestamp from OdfScheduleStatus");

	$q="drop TABLE if exists `OdfScheduleStatus`";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2019-04-10 21:29:03');
}

if($version<'2019-04-11 08:29:02') {
	$q="ALTER TABLE `Events` ADD `EvOdfGender` varchar(1) NOT NULL";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2019-04-11 08:29:02');
}

if($version<'2019-04-14 16:29:02') {
	$q="ALTER TABLE `FinSchedule` ADD `FsOdfMatchName` int NOT NULL";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2019-04-14 16:29:02');
}

if($version<'2019-04-26 16:29:02') {
	$q="ALTER TABLE `Rankings` ADD `RankPersonalBest` int NOT NULL, ADD `RankSeasonBest` int NOT NULL";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2019-04-26 16:29:02');
}

if($version<'2019-05-02 08:29:02') {
	// some installations of old ianseos did not create the table, so recreate here
	$q="CREATE TABLE IF NOT EXISTS `OdfDocuments` (
		`OdfDocTournament` int NOT NULL,
		`OdfDocCode` varchar(34) NOT NULL,
		`OdfDocType` varchar(22) NOT NULL,
		`OdfDocSubType` varchar(10) NOT NULL,
		`OdfDocVersion` int NOT NULL,
		`OdfDocDate` date NOT NULL,
		`OdfDocLogicalDate` date NOT NULL,
		`OdfDocTime` time NOT NULL,
		`OdfDocStatus` varchar(15) NOT NULL,
		`OdfDocSendStatus` tinyint NOT NULL,
		`OdfDocSendRetries` tinyint NOT NULL,
		`OdfDocExtra` text NOT NULL,
		PRIMARY KEY (OdfDocTournament, OdfDocCode, OdfDocType, OdfDocSubType),
		index (OdfDocTournament, OdfDocDate, OdfDocTime),
		index (OdfDocTournament, OdfDocSendStatus, OdfDocSendRetries, OdfDocDate, OdfDocTime)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2019-05-02 08:29:02');
}

if($version<'2019-05-08 14:29:01') {
	$q="ALTER TABLE `Individuals` ADD `IndRecordBitmap` tinyint NOT NULL, add IndIrmType  tinyint NOT NULL, add IndIrmTypeFinal  tinyint NOT NULL, drop index IndEvent, add index IndEvent (IndEvent, IndTournament, IndRankFinal, IndIrmTypeFinal, IndIrmType, IndRank)";
	$r=safe_w_sql($q,false,array(1146, 1060));

	$q="ALTER TABLE `Finals` ADD `FinRecordBitmap` tinyint NOT NULL, add FinIrmType  tinyint NOT NULL";
	$r=safe_w_sql($q,false,array(1146, 1060));

	$q="ALTER TABLE `TeamFinals` ADD `TfRecordBitmap` tinyint NOT NULL, add TfIrmType  tinyint NOT NULL";
	$r=safe_w_sql($q,false,array(1146, 1060));

	$q="ALTER TABLE `Teams` ADD `TeRecordBitmap` tinyint NOT NULL, add TeIrmType  tinyint NOT NULL, add TeIrmTypeFinal  tinyint NOT NULL, add index (TeEvent, TeTournament, TeRankFinal, TeIrmTypeFinal, TeIrmType, TeRank)";
	$r=safe_w_sql($q,false,array(1146, 1060));

	$q="drop TABLE if exists `IrmTypes`";
	$r=safe_w_sql($q,false,array(1146, 1060));

	$q="CREATE TABLE IF NOT EXISTS `IrmTypes` (
		`IrmId` tinyint NOT NULL,
		`IrmType` varchar(5) NOT NULL,
		`IrmShowRank` tinyint NOT NULL,
		primary key (IrmId)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1060));

	safe_w_sql("insert into IrmTypes values (0, '', 1), (5, 'DNF', 1), (10, 'DNS', 1), (15, 'DSQ', 0), (20, 'DQB', 0)");

	db_save_version('2019-05-08 14:29:01');
}

if($version<'2019-05-10 12:29:02') {
	$q="ALTER TABLE `Qualifications` ADD `QuIrmType` tinyint NOT NULL, add index (QuIrmType)";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2019-05-10 12:29:02');
}

if($version<'2019-05-21 12:29:02') {
	$q="ALTER TABLE `OdfDocuments` change `OdfDocSubType` OdfDocSubType varchar(34) NOT NULL";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="ALTER TABLE `OdfDocuments` add OdfDocDataFeed varchar(1) NOT NULL after OdfDocSubType";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="ALTER TABLE `OdfDocuments` drop primary key";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="ALTER TABLE `OdfDocuments` add primary key (OdfDocTournament, OdfDocCode, OdfDocType, OdfDocSubType, OdfDocDataFeed)";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="ALTER TABLE `OdfMessageStatus` add OmsDataFeed varchar(1) NOT NULL after OmsKey";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="ALTER TABLE `OdfMessageStatus` drop primary key";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="ALTER TABLE `OdfMessageStatus` add primary key (OmsTournament, OmsType, OmsKey, OmsDataFeed)";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2019-05-21 12:29:02');
}

if($version<'2019-05-21 18:29:02') {
	$q="ALTER TABLE `OdfDocuments` add OdfDocSubCode varchar(34) NOT NULL after OdfDocCode";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="ALTER TABLE `OdfDocuments` drop primary key";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="ALTER TABLE `OdfDocuments` add primary key (OdfDocTournament, OdfDocCode, OdfDocSubCode, OdfDocType, OdfDocSubType, OdfDocDataFeed)";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2019-05-21 18:29:02');
}

if($version<'2019-05-23 12:29:02') {
	$q="alter table RecTournament add RtRecSubphase tinyint not null";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="update RecTournament set RtRecPhase=3 where RtRecDistance like '%match%' and RtRecPhase=0";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="update RecTournament set RtRecPhase=1 where RtRecDistance='Total' and RtRecPhase=0";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="update RecTournament set RtRecPhase=1 where RtRecDistance like '%Round%' and RtRecPhase=0";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="update RecTournament set RtRecPhase=2 where RtRecPhase=0";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q=safe_w_sql("select * from RecTournament where RtRecPhase=2 order by RtTournament, RtRecType, RtRecCode, RtRecTeam, RtRecPara, RtRecCategory, RtRecDistance desc");
	$k='';
	while($r=safe_fetch($q)) {
		if($k!=$r->RtTournament.$r->RtRecType.$r->RtRecCode.$r->RtRecTeam.$r->RtRecPara.$r->RtRecCategory.$r->RtRecPhase) {
			$k=$r->RtTournament.$r->RtRecType.$r->RtRecCode.$r->RtRecTeam.$r->RtRecPara.$r->RtRecCategory.$r->RtRecPhase;
			$sub=1;
		}
		safe_w_sql("update RecTournament set RtRecSubphase=$sub
			where RtTournament=$r->RtTournament
				and RtRecType='$r->RtRecType'
			  	and RtRecCode='$r->RtRecCode'
			  	and RtRecTeam=$r->RtRecTeam
			  	and RtRecPara=$r->RtRecPara
			  	and RtRecCategory='$r->RtRecCategory'
			  	and RtRecPhase=$r->RtRecPhase
			  	and RtRecDistance='$r->RtRecDistance'");
		$sub++;
	}
	$q="alter table RecTournament add unique key (RtTournament, RtRecType, RtRecCode, RtRecTeam, RtRecPara, RtRecCategory, RtRecPhase, RtRecSubphase)";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2019-05-23 12:29:02');
}

if($version<'2019-06-05 12:29:03') {
	$q="ALTER TABLE `Parameters` change ParValue ParValue text not null";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2019-06-05 12:29:03');
}

if($version<'2019-06-30 13:50:00') {
    $q="ALTER TABLE `Tournament` CHANGE `ToImgL` `ToImgL` MEDIUMBLOB NOT NULL, CHANGE `ToImgR` `ToImgR` MEDIUMBLOB NOT NULL, CHANGE `ToImgB` `ToImgB` MEDIUMBLOB NOT NULL";
    $r=safe_w_sql($q,false,array(1146, 1060));

    db_save_version('2019-06-30 13:50:00');
}

/*

// TEMPLATE
if($version<'2019-06-05 12:29:02') {
	$q="ALTER TABLE `Finals` ADD `FinShootFirst` tinyint NOT NULL after FinStatus";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2019-06-05 12:29:02');
}

IMPORTANT: InfoSystem related things MUST be changed in the lib.php file!!!
REMEMBER TO CHANGE ALSO Common/Lib/UpdateTournament.inc.php!!!

*/

db_save_version($newversion);

function db_save_version($newversion) {
	global $CFG;
	//Aggiorno alla versione attuale SOLO le gare che erano alla versione immediatamente precedente
	$oldDbVersion = GetParameter('DBUpdate');
	safe_w_sql("UPDATE Tournament SET ToDbVersion='{$newversion}' WHERE ToDbVersion='{$oldDbVersion}'");

	SetParameter('DBUpdate', $newversion);
	SetParameter('SwUpdate', ProgramVersion);

	foreach(glob($CFG->DOCUMENT_PATH.'TV/Photos/*.ser') as $file) {
		@unlink($file);
		@unlink(substr($file, 0, -3).'check');
	}
}
