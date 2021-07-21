<?php

if($version<'2016-01-15 17:55:00') {
	$q="ALTER TABLE `Qualifications` ADD `QuNotes` varchar(50) NOT NULL";
	$r=safe_w_sql($q,false,array(1060));
	$q="ALTER TABLE `Individuals` ADD `IndNotes` varchar(50) NOT NULL";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2016-01-15 17:55:00');
}

if($version<'2016-01-15 20:55:00') {
	$q="ALTER TABLE `ModulesParameters` change `MpParameter` MpParameter varchar(30) NOT NULL";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2016-01-15 20:55:00');
}

if($version<'2016-01-20 11:40:00') {
	$q="ALTER TABLE `Classes` ADD `ClTourRules` VARCHAR(75) NOT NULL AFTER `ClWaClass`";
	$r=safe_w_sql($q,false,array(1060));
	$q="ALTER TABLE `Divisions` ADD `DivTourRules` VARCHAR(75) NOT NULL AFTER `DivWaDivision`";
	$r=safe_w_sql($q,false,array(1060));
	$q="ALTER TABLE `Events` ADD `EvTourRules` VARCHAR(75) NOT NULL AFTER `EvMedals`";
	$r=safe_w_sql($q,false,array(1060));
	$q="ALTER TABLE `EventClass` ADD `EcTourRules` VARCHAR(75) NOT NULL AFTER `EcNumber`";
	$r=safe_w_sql($q,false,array(1060));
	$q="ALTER TABLE `TournamentDistances` ADD `TdTourRules` VARCHAR(75) NOT NULL AFTER `Td8`";
	$r=safe_w_sql($q,false,array(1060));
	$q="ALTER TABLE `DistanceInformation` ADD `DiTourRules` VARCHAR(75) NOT NULL AFTER `DiTargets`";
	$r=safe_w_sql($q,false,array(1060));
	$q="ALTER TABLE `TargetFaces` ADD `TfTourRules` VARCHAR(75) NOT NULL AFTER `TfDefault`";
	$r=safe_w_sql($q,false,array(1060));
	$q="ALTER TABLE `Entries` ADD `EnLueTimeStamp` DATETIME NOT NULL AFTER `EnTargetFace`,
		ADD `EnLueFieldChanged` SMALLINT NOT NULL AFTER `EnLueTimeStamp`";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2016-01-20 11:40:00');
}


if($version<'2016-02-05 18:10:00') {
	$q="DROP TABLE TVOut";
	$r=safe_w_sql($q,false,array(1051));

	$q="CREATE TABLE IF NOT EXISTS `TVOut` (
		`TVOId` tinyint(4) NOT NULL,
		`TVOName` varchar(50) NOT NULL,
		`TVOUrl` text NOT NULL,
		`TVOMessage` text NOT NULL,
		`TVORuleId` int(11) NOT NULL,
		`TVOTourCode` varchar(8) NOT NULL,
		`TVORuleType` tinyint(4) NOT NULL,
		`TVOLastUpdate` datetime NOT NULL,
		PRIMARY KEY (`TVOId`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2016-02-05 18:10:00');
}

if($version<'2016-02-15 03:13:01') {
	$q="insert into LookUpPaths set LupIocCode='CAN', LupPhotoPath='http://can.service.ianseo.net/GetPhoto.php', LupFlagsPath='http://can.service.ianseo.net/GetFlags.php' on duplicate key update LupPhotoPath='http://can.service.ianseo.net/GetPhoto.php', LupFlagsPath='http://can.service.ianseo.net/GetFlags.php'";
	$r=safe_w_sql($q,false,array(1060));
	$q="insert into LookUpPaths set LupIocCode='NOR', LupPhotoPath='http://nor.service.ianseo.net/GetPhoto.php', LupFlagsPath='http://nor.service.ianseo.net/GetFlags.php' on duplicate key update LupPhotoPath='http://nor.service.ianseo.net/GetPhoto.php', LupFlagsPath='http://nor.service.ianseo.net/GetFlags.php'";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2016-02-15 03:13:01');
}

if($version<'2016-02-15 10:56:00') {
	$r=safe_w_sql("DELETE FROM CasGrid WHERE CGPhase=0");
	$r=safe_w_sql("INSERT INTO CasGrid VALUES
		(0,1,1,8,1), (0,1,4,5,1), (0,1,2,7,2), (0,1,3,6,2),
		(0,2,1,5,1), (0,2,4,8,1), (0,2,2,6,2), (0,2,3,7,2),
		(0,3,1,4,1), (0,3,5,8,1), (0,3,2,3,2), (0,3,6,7,2)");
	db_save_version('2016-02-15 10:56:00');
}

if($version<'2016-02-16 20:00:00') {
	$q="ALTER TABLE `CasScore`
		ADD `CaSSetPointsByEnd` VARCHAR(23) NOT NULL AFTER `CaSSetPoints`,
		ADD `CaSWinLose` TINYINT NOT NULL AFTER `CaSTie`;";
	safe_w_sql($q,false,array(1060));
	db_save_version('2016-02-16 20:00:00');
}

if($version<'2016-02-17 17:00:00') {
	$r=safe_w_sql("DELETE FROM CasGrid WHERE CGPhase=0");
	$r=safe_w_sql("INSERT INTO `CasGrid` VALUES
		(0, 1, 1, 8, 1), (0, 3, 4, 1, 1), (0, 2, 8, 4, 1), (0, 3, 8, 5, 1),
		(0, 2, 5, 1, 1), (0, 1, 4, 5, 1), (0, 2, 6, 2, 2), (0, 1, 3, 6, 2),
		(0, 3, 3, 2, 2), (0, 1, 2, 7, 2), (0, 3, 7, 6, 2), (0, 2, 7, 3, 2)");
	db_save_version('2016-02-17 17:00:00');
}

if($version<'2016-02-22 18:45:00') {
	SetParameter('AccActive','');
	SetParameter('AccCompetitions','');
	SetParameter('AccIPs','');

	db_save_version('2016-02-22 18:45:00');
}

if($version<'2016-02-23 11:45:01') {
	$q="ALTER TABLE `Qualifications` ADD `QuTarget` int NOT NULL after QuSession, add QuLetter varchar(1) not null after QuTarget, add index (QuSession, QuTarget, QuLetter)";
	$r=safe_w_sql($q,false,array(1060));

	safe_w_sql("update Qualifications set QuTarget=substr(QuTargetNo, 2, 5)+0, QuLetter=right(QuTargetNo, 1)");

	db_save_version('2016-02-23 11:45:01');
}

if($version<'2016-03-04 11:45:00') {
	$q="ALTER TABLE `TVOut` CHANGE `TVOId` `TVOId` TINYINT(4) NOT NULL AUTO_INCREMENT";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2016-03-04 11:45:00');
}

if($version<'2016-03-22 08:30:01') {
	$q="ALTER TABLE `Session` ADD `SesDtStart` DATETIME NOT NULL AFTER `SesStatus`, ADD `SesDtEnd` DATETIME NOT NULL AFTER `SesDtStart`";
	$r=safe_w_sql($q,false,array(1060));

	safe_w_sql("DELETE FROM `Flags` WHERE `FlTournament` = -1");

	db_save_version('2016-03-22 08:30:01');
}

if($version<'2016-03-22 11:00:00') {
	$q="update LookUpPaths set LupFlagsPath='https://extranet.worldarchery.sport/Api/GetFlags.php' where LupIocCode='FITA'";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2016-03-22 11:00:00');
}

if($version<'2016-04-02 16:00:00') {

	$q="CREATE or replace VIEW `EventCategories` AS select * from
`EventClass` join `Events` on `EvCode` = `EcCode`
 and `EvTeamEvent` = `EcTeamEvent`
 and `EvTournament` = `EcTournament`";
	$r=safe_w_sql($q,false,array());

	db_save_version('2016-04-02 16:00:00');
}

if($version<'2016-04-06 16:00:00') {
	$q="ALTER TABLE `Entries` ADD `EnNameOrder` tinyint NOT NULL";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2016-04-06 16:00:00');
}

if($version<'2016-04-06 16:00:01') {
	$q="ALTER TABLE `LookUpEntries` ADD `LueNameOrder` tinyint NOT NULL";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2016-04-06 16:00:01');
}

if($version<'2016-04-08 19:45:00') {
	$q="ALTER TABLE `Events` ADD `EvCodeParent` VARCHAR(4) NOT NULL AFTER `EvTourRules`";
	$r=safe_w_sql($q,false,array(1060));
	$q="ALTER TABLE `Tournament` ADD `ToNameShort` VARCHAR(60) NOT NULL AFTER `ToName`";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2016-04-08 19:45:00');
}

if($version<'2016-04-27 13:00:01') {
	$q="ALTER TABLE `Teams` ADD `TeNotes` varchar(30) NOT NULL";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2016-04-27 13:00:01');
}

if($version<'2016-05-05 16:00:00') {
	$q="ALTER TABLE `Awards` change AwEvent AwEvent varchar(15) not null";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2016-05-05 16:00:00');
}

if($version<'2016-05-18 22:00:01') {
	safe_w_sql("drop table if exists ExtraDataCountries");
	$q="CREATE TABLE IF NOT EXISTS `ExtraDataCountries` (
		`EdcId` int(10) NOT NULL,
		`EdcSubTeam` tinyint NOT NULL,
		`EdcType` varchar(10) NOT NULL,
		`EdcEvent` varchar(4) NOT NULL,
		`EdcEmail` varchar(100) NOT NULL,
		`EdcExtra` text NOT NULL,
		PRIMARY KEY (EdcId, EdcType, EdcEvent, EdcSubTeam)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1060));

	safe_w_sql("alter table ExtraData add EdEvent varchar(4) after EdType, drop primary key, add primary key (EdId, EdType, EdEvent) ", '', array(1060));

	// amend old data
	safe_w_sql("insert ignore into ExtraDataCountries select EdId, 0, left(EdType, 1), substr(EdType, 2, length(EdType)-2), EdEmail, EdExtra from ExtraData where EdType like 'X%1' or EdType like 'Y%1'");
	safe_w_sql("update ExtraData set EdEvent=substr(EdType, 2, length(EdType)-2), EdType=left(EdType, 1) where EdType like 'X%0' or EdType like 'Y%0'");
	safe_w_sql("delete from ExtraData where EdType like 'X%1' or EdType like 'Y%1'");

	db_save_version('2016-05-18 22:00:01');
}

if($version<'2016-05-31 15:00:00') {
	$q="ALTER TABLE `Events` CHANGE `EvDistance` `EvDistance` VARCHAR(6) NOT NULL;";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2016-05-31 15:00:00');
}

if($version<'2016-06-29 11:00:00') {
	$q="ALTER TABLE `AccOperationType` ADD `AOTOrder` tinyint NOT NULL";
	$r=safe_w_sql($q,false,array(1060));
	safe_w_sql("update AccOperationType set AOTOrder=AOTId*10");
	safe_w_sql("Insert into AccOperationType set AOTOrder=5, AOTDescr='Payments'");

	db_save_version('2016-06-29 11:00:00');
}

if($version<'2016-08-31 11:00:00') {
	$q="ALTER TABLE `IskDevices` ADD `IskDvGroup` tinyint NOT NULL after IskDvDevice, add IskDvSetup blob not null, add key (IskDvTournament, IskDvGroup)";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2016-08-31 11:00:00');
}

if($version<'2016-09-01 13:00:00') {
	$q="ALTER TABLE IskDevices ADD `IskDvSchedKey` varchar(25) NOT NULL after IskDvGroup, add key (IskDvTournament, IskDvSchedKey, IskDvGroup)";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2016-09-01 13:00:00');
}

if($version<'2016-09-03 00:35:01') {
	$q="ALTER TABLE `IskData` change  `IskDtType` IskDtType varchar(2) NOT NULL,
		add IskDtSession tinyint not null,
		drop primary key";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$r=safe_w_sql("ALTER TABLE `IskData` add primary key (IskDtTournament,
		IskDtMatchNo,
		IskDtEvent,
		IskDtTeamInd,
		IskDtType,
		IskDtTargetNo,
		IskDtDistance,
		IskDtEndNo,
		IskDtSession)",false,array(1146, 1060));

	db_save_version('2016-09-03 00:35:01');
}

if($version<'2016-09-03 12:35:00') {
	$q="ALTER TABLE `Eliminations` ADD key (ElId, ElElimPhase)";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2016-09-03 12:35:00');
}

if($version<'2016-09-06 17:35:01') {
	$q="ALTER TABLE `Scheduler` change `SchTargets` SchTargets text NOT NULL";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="ALTER TABLE `DistanceInformation` change `DiTargets` DiTargets text NOT NULL";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2016-09-06 17:35:01');
}

if($version<'2016-12-24 20:00:00') {
	$q="ALTER TABLE `IskDevices` ADD `IskDvUrlDownload` TINYTEXT NOT NULL AFTER `IskDvSetup`";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2016-12-24 20:00:00');
}
