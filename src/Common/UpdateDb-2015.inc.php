<?php

if($version<'2015-01-05 18:00:01') {
	$q="ALTER TABLE `Individuals` ADD index IndEvent (IndEvent, IndTournament, IndRankFinal, IndRank)";
	$r=safe_w_sql($q,false,array(1060));

	$q="ALTER TABLE `Events` ADD index EvTournament (EvTournament, EvTeamEvent, EvCode)";
	$r=safe_w_sql($q,false,array(1060));

	$q="ALTER TABLE `Tournament` ADD index ToDbVersion (ToDbVersion)";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-01-05 18:00:01');
}

if($version<'2015-01-06 18:30:00') {
	$q="ALTER TABLE `Grids` ADD index GrPosition (GrPosition, GrPhase), add index GrPosition2 (GrPosition2, GrPhase)";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-01-06 18:30:00');
}

if($version<'2015-01-08 19:45:00') {
	$q="ALTER TABLE `Tournament` ADD `ToVenue` tinytext not null AFTER ToWhere";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-01-08 19:45:00');
}

if($version<'2015-01-15 19:45:00') {
	$q="ALTER TABLE `Finals` ADD index FinLive (FinLive, FinTournament)";
	$r=safe_w_sql($q,false,array(1060));

	$q="ALTER TABLE `TeamFinals` ADD index TfLive (TfLive, TfTournament)";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-01-15 19:45:00');
}

if($version<'2015-01-30 11:20:00') {
	$q="insert into `InvolvedType` set ItId=15, ItDescription='Announcer', ItJudge=0, ItDoS=0, ItJury=0, ItOc=8 on duplicate key update ItId=15, ItDescription='Announcer', ItJudge=0, ItDoS=0, ItJury=0, ItOc=8";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-01-30 11:20:00');
}

if($version<'2015-02-19 12:45:03') {
	$r=safe_w_sql("drop table if exists OnLineIds",false,array(1060));
	$q="CREATE TABLE OnLineIds (
		OliId int not null,
		OliType varchar(1) not null,
		OliServer varchar(50) not null,
		OliOnlineId int not null,
		OliTournament int not null,
		primary key (OliId, OliType, OliServer, OliTournament),
		index (OliServer, OliTournament)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-02-19 12:45:03');
}

if($version<'2015-02-21 14:15:00') {
	$q="ALTER TABLE `Entries` ADD index (EnCode, EnIocCode, EnDivision, EnTournament)";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-02-21 14:15:00');
}

if($version<'2015-02-21 18:15:00') {
	require_once('Common/Lib/Fun_Modules.php');

	$Ret=new StdClass();
	$Ret->ForceLang='EN';
	$Ret->Division_Class=0;
	$Ret->SubClasses=0;
	$Ret->ShowTeams=1;
	$Ret->Teams=1;
	$Ret->Qualifications=1;
	$Ret->Elimination=0;
	$Ret->RoundRobin=0;
	$Ret->ClubTeam=0;
	$Ret->Brackets=1;
	$Ret->Finals=1;
	$Ret->Statistics=1;
	$Ret->FindYourRank=1;
	$Ret->Footer=0;
	$Ret->GuessFinals=0;
	$Ret->YouTubeLive='';
	$Ret->YouTubeLiveScore=0;
	$Ret->YouTubeLiveTopMenu=0;

	$q=safe_r_sql("select ToId, ToOptions from Tournament where ToOptions like '%s:8:\"InfoMenu\";O:8:\"stdClass\"%'");
	while($r=safe_fetch($q)) {
		$tmp=unserialize($r->ToOptions);
		if(!empty($tmp['InfoMenu'])) {
			foreach($Ret as $id => $val) {
				if(!isset($tmp['InfoMenu']->{$id})) $tmp['InfoMenu']->{$id} = $val;
			}
			setModuleParameter('InfoSystem', 'InfoMenu', $tmp['InfoMenu'], $r->ToId);
			unset($tmp['InfoMenu']);
			if($tmp) {
				safe_w_sql("update Tournament set ToOptions=".StrSafe_DB(serialize($tmp))." where ToId=$r->ToId");
			} else {
				safe_w_sql("update Tournament set ToOptions='' where ToId=$r->ToId");
			}
		}
	}

	db_save_version('2015-02-21 18:15:00');
}

if($newversion=='2015-03-10 22:30:05' and ProgramRelease=='TESTING') {
	$version='2015-02-21 18:15:00';
}

if($version<'2015-03-02 13:35:00') {
	safe_w_sql("DROP TABLE IskDevices",false,array(1051));
	safe_w_sql("CREATE TABLE `IskDevices` (
		`IskDvTournament` int(10) NOT NULL,
		`IskDvDevice` varchar(36) NOT NULL,
		`IskDvCode` varchar(2) NOT NULL,
		`IskDvTarget` varchar(3) NOT NULL,
		`IskDvState` tinyint(4) NOT NULL,
		`IskDvIpAddress` varchar(15) NOT NULL,
		`IdLastSeen` datetime NOT NULL,
		PRIMARY KEY (`IskDvTournament`,`IskDvDevice`),
		UNIQUE KEY `IskDvCode` (`IskDvTournament`,`IskDvCode`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8",false,array(1050));

	safe_w_sql("CREATE TABLE IF NOT EXISTS `IskData` (
		`IskDtTournament` int(11) NOT NULL,
		`IskDtMatchNo` int(11) NOT NULL,
		`IskDtEvent` varchar(4) NOT NULL,
		`IskDtTeamInd` int(11) NOT NULL,
		`IskDtTagetNo` varchar(5) NOT NULL,
		`IskDtDistance` int(11) NOT NULL,
		`IskDtEndNo` int(11) NOT NULL,
		`IskDtArrowstring` varchar(9) NOT NULL,
		`IskDtUpdate` datetime NOT NULL,
		`IskDtDevice` varchar(36) NOT NULL,
		PRIMARY KEY (`IskDtTournament`,`IskDtMatchNo`,`IskDtEvent`,`IskDtTeamInd`,`IskDtTagetNo`,`IskDtDistance`,`IskDtEndNo`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8",false,array(1050));

	db_save_version('2015-03-02 13:35:00');
}

if($version<'2015-03-02 17:35:00') {
	$r=safe_w_sql("ALTER TABLE `Finals` ADD `FinShootFirst` tinyint NOT NULL after FinStatus", false, array(1060));
	$r=safe_w_sql("ALTER TABLE `TeamFinals` ADD `TfShootFirst` tinyint NOT NULL after TfStatus, Add TfShootingArchers text not null after TfShootFirst", false, array(1060));

	db_save_version('2015-03-02 17:35:00');
}

if($version<'2015-03-04 10:35:00') {
	$r=safe_w_sql("ALTER TABLE `IskDevices` ADD `IskDvTargetReq` varchar(3) NOT NULL after IskDvTarget, ADD `IskDvAppVersion` tinyint NOT NULL after IskDvState", false, array(1060));
	db_save_version('2015-03-04 10:35:00');
}

if($version<'2015-03-04 14:50:00') {
	$r=safe_w_sql("ALTER TABLE IskDevices DROP INDEX IskDvCode", false, array(1091));
	$r=safe_w_sql("ALTER TABLE IskDevices DROP PRIMARY KEY", false, array(1091));
	$r=safe_w_sql("ALTER TABLE IskDevices ADD PRIMARY KEY (`IskDvDevice`)", false, array(1064));
	$r=safe_w_sql("ALTER TABLE IskDevices ADD INDEX `IskDvTournament` (`IskDvTournament`)", false, array(1064));
	db_save_version('2015-03-04 14:50:00');
}

if($version<'2015-03-04 15:50:00') {
	$r=safe_w_sql("ALTER TABLE `IskDevices` ADD `IskDvBattery` tinyint NOT NULL after IskDvAppVersion", false, array(1060));
	db_save_version('2015-03-04 15:50:00');
}

if($version<'2015-03-04 21:30:00') {
	$q=safe_w_sql("Select ToId from Tournament where ToOptions!=''",false,array(1060));
	while($r=safe_fetch($q)) {
		UpdateToOptions_20150304($r->ToId);
	}
	db_save_version('2015-03-04 21:30:01');
}

if($version<'2015-03-05 19:30:00') {
	$q="ALTER TABLE `IskData` change IskDtTagetNo IskDtTargetNo varchar(5) not null";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-03-05 19:30:00');
}

if($version<'2015-03-07 11:09:00') {
	$r=safe_w_sql("ALTER TABLE `IskData` add IskDtType varchar(1) not null AFTER IskDtTeamInd",false,array(1060));
	$r=safe_w_sql("ALTER TABLE `IskData` DROP PRIMARY KEY",false,array(1091));
	$r=safe_w_sql("ALTER TABLE `IskData` ADD PRIMARY KEY (`IskDtTournament`,`IskDtMatchNo`,`IskDtEvent`,`IskDtTeamInd`,`IskDtType`,`IskDtTargetNo`,`IskDtDistance`,`IskDtEndNo`)", false, array(1064));
	db_save_version('2015-03-07 11:09:00');
}

if($version<'2015-03-10 22:30:01') {
	$q="ALTER TABLE `IskDevices` change `IskDvAppVersion` IskDvAppVersion varchar(11) NOT NULL after IskDvDevice";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-03-10 22:30:01');
}

if($version<'2015-03-10 22:30:02') {
	$q="ALTER TABLE `IskDevices` change `IskDvAppVersion` IskDvAppVersion tinyint NOT NULL, add IskDvVersion varchar(12) after IskDvDevice";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-03-10 22:30:02');
}

if($version<'2015-03-18 06:30:00') {
	$q="ALTER TABLE `IskDevices` ADD `IskDvAuthRequest` tinyint NOT NULL";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-03-18 06:30:00');
}

if($version<'2015-03-28 18:30:01') {
	safe_w_sql("drop table if exists Raspberries");
	$q="CREATE TABLE IF NOT EXISTS Raspberries (
		`RasDevice` varchar(35) NOT NULL primary key,
		`RasIp` varchar(15) NOT NULL,
		`RasLocation` varchar(50) NOT NULL,
		`RasUrl` varchar(255) NOT NULL,
		`RasTourCode` varchar(11) NOT NULL,
		`RasRot` varchar(36) NOT NULL,
		`RasType` varchar(36) NOT NULL,
		`RasLastseen` datetime NOT NULL,
		RasActive tinyint not null,
		index Active (RasActive, RasLastseen)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-03-28 18:30:01');
}

if($version<'2015-04-16 18:30:02') {
	$q="ALTER TABLE `Finals` ADD `FinSetPointsByEnd` VARCHAR( 36 ) NOT NULL after FinSetPoints";
	$r=safe_w_sql($q,false,array(1060));
	$q="ALTER TABLE `TeamFinals` ADD `TfSetPointsByEnd` VARCHAR( 36 ) NOT NULL after TfSetPoints";
	$r=safe_w_sql($q,false,array(1060));

	UpdateSetPointsByEnd_20150416();

	db_save_version('2015-04-16 18:30:02');
}

if($version<'2015-04-22 13:00:00') {
	$q="ALTER TABLE `RecTournament` change `RtRecDistance` RtRecDistance varchar(50) NOT NULL";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-04-22 13:00:00');
}

if($version<'2015-04-24 16:00:00') {
	$q="ALTER TABLE `Scheduler` ADD `SchTargets` varchar(255) NOT NULL, add SchLink varchar(100) not null";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-04-24 16:00:00');
}

if($version<'2015-04-24 16:30:00') {
	$q="ALTER TABLE `DistanceInformation` ADD `DiTargets` varchar(255) NOT NULL";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-04-24 16:30:00');
}

if($version<'2015-04-26 18:30:00') {
	$q="ALTER TABLE `Entries` change EnTimestamp EnTimestamp Timestamp not null, ADD `EnWorldRank` int NOT NULL";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-04-26 18:30:00');
}

if($version<'2015-04-27 08:30:00') {
	safe_w_sql("drop table if exists DocumentVersions");
	$q="CREATE TABLE IF NOT EXISTS DocumentVersions (
		DvTournament int NOT NULL,
		DvFile varchar(50) not null comment 'calling chunk basename or rank object name',
		DvEvent varchar(10) not null comment 'if div+class => DIV|CLASS',
		DvOrder int not null,
		DvSectors varchar(50) not null,
		DvSector varchar(1) not null,
		DvMajVersion tinyint not null,
		DvMinVersion tinyint not null,
		DvPrintDateTime datetime not null,
		DvIncludedDateTime datetime not null,
		DvNotes text not null,
		primary key (DvTournament, DvFile, DvEvent),
		index DvOrder (DvOrder, DvEvent)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-04-27 08:30:00');
}

if($version<'2015-04-28 10:30:00') {
	$q="ALTER TABLE `FinSchedule` ADD `FSTimestamp` Timestamp NOT NULL";
	$r=safe_w_sql($q,false,array(1060));
	$q="ALTER TABLE `FinWarmup` ADD `FwTimestamp` Timestamp NOT NULL";
	$r=safe_w_sql($q,false,array(1060));
	$q="ALTER TABLE `Scheduler` ADD `SchTimestamp` Timestamp NOT NULL";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-04-28 10:30:00');
}

if($version<'2015-04-30 16:10:00') {
	$q="ALTER TABLE `Session` CHANGE `SesName` `SesName` VARCHAR(100) NOT NULL DEFAULT ''";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-04-30 16:10:00');
}

if($version<'2015-04-30 23:30:00') {
	$q="ALTER TABLE `Entries` drop EnWorldRank";
	$r=safe_w_sql($q,false,array(1060));

	safe_w_sql("drop table if exists Rankings");
	$q="CREATE TABLE IF NOT EXISTS Rankings (
		RankTournament int NOT NULL,
		RankCode varchar(25) not null,
		RankIocCode varchar(5) not null,
		RankTeam tinyint not null,
		RankEvent varchar(4) not null,
		RankRanking int not null,
		primary key (RankTournament, RankCode, RankTeam, RankEvent),
		index DvOrder (RankTournament, RankTeam, RankEvent, RankRanking)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-04-30 23:30:00');
}

if($version<'2015-05-01 11:30:00') {
	$q="ALTER TABLE `LookUpPaths`
		ADD `LupRankingPath` varchar(255) NOT NULL,
		add LupClubNamesPath varchar(255) NOT NULL,
		add LupRecordsPath varchar(255) NOT NULL";
	$r=safe_w_sql($q,false,array(1060));

	safe_w_sql("update LookUpPaths set LupPath='%Modules/LookUpFunctions/LookupFitaId.php',
		LupPhotoPath='%Modules/LookUpFunctions/LookupFitaPhoto.php',
		LupRankingPath='%Modules/LookUpFunctions/LookupFitaRanking.php',
		LupClubNamesPath='%Modules/LookUpFunctions/LookupFitaClubNames.php'
		where LupIocCode='FITA'");

	safe_w_sql("Alter table IdCards
			add IcNumber int not null,
			add IcType varchar(1) not null default 'A',
			add IcName varchar(50) not null default 'Accreditation',
			add index (IcTournament, IcNumber)");

	safe_w_sql("Alter table IdCardElements
			add IceCardNumber int not null,
			add index (IceTournament, IceCardNumber, IceOrder)");

	db_save_version('2015-05-01 11:30:00');
}

if($version<'2015-05-01 11:30:01') {
	$q="ALTER TABLE `IdCards` drop primary key";
	$r=safe_w_sql($q,false,array(1060));
	$q="ALTER TABLE `IdCards` add primary key (IcTournament, IcType, IcNumber)";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-05-01 11:30:01');
}

if($version<'2015-05-01 11:30:02') {
	$q="Alter table IdCardElements
			add IceCardType varchar(1) not null default 'A',
			add index (IceTournament, IceCardType, IceCardNumber, IceOrder)";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-05-01 11:30:02');
}

if($version<'2015-05-01 14:50:00') {
	$q="CREATE TABLE IF NOT EXISTS DocumentVersions (
		DvTournament int NOT NULL,
		DvFile varchar(50) not null comment 'calling chunk basename or rank object name',
		DvEvent varchar(10) not null comment 'if div+class => DIV|CLASS',
		DvOrder int not null,
		DvSectors varchar(50) not null,
		DvSector varchar(1) not null,
		DvMajVersion tinyint not null,
		DvMinVersion tinyint not null,
		DvPrintDateTime datetime not null,
		DvIncludedDateTime datetime not null,
		DvNotes text not null,
		primary key (DvTournament, DvFile, DvEvent),
		index DvOrder (DvOrder, DvEvent)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-05-01 14:50:00');
}

if($version<'2015-05-02 08:50:01') {
	$q="ALTER TABLE `AccEntries` change `AEOperation` AEOperation int NOT NULL, add AEExtra varchar(25) ";
	$r=safe_w_sql($q,false,array(1060));

	$q="ALTER TABLE `Individuals` Add `IndBacknoPrinted` datetime NOT NULL ";
	$r=safe_w_sql($q,false,array(1060));

	$q="ALTER TABLE `Teams` Add `TeBacknoPrinted` datetime NOT NULL ";
	$r=safe_w_sql($q,false,array(1060));

	$q="ALTER TABLE `Eliminations` Add `ElBacknoPrinted` datetime NOT NULL ";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-05-02 08:50:01');
}

if($version<'2015-05-03 10:50:00') {
	$q="ALTER TABLE `LookUpPaths` change `LupFors` LupOrigin varchar(3) NOT NULL";
	$r=safe_w_sql($q,false,array(1060));

	safe_w_sql("update LookUpPaths set LupOrigin='WA' where LupOrigin!=''");
	db_save_version('2015-05-03 10:50:00');
}

if($version<'2015-05-03 12:50:02') {
	$q="ALTER TABLE `Countries` drop `CoMail`";
	$r=safe_w_sql($q,false,array(1091));
	$q="ALTER TABLE `Countries` drop CoNoPrint";
	$r=safe_w_sql($q,false,array(1091));
	$q="ALTER TABLE `Countries` drop CoFlag";
	$r=safe_w_sql($q,false,array(1091));

	db_save_version('2015-05-03 12:50:02');
}

if($version<'2015-05-04 13:15:01') {
	$q="ALTER TABLE `ExtraData` change `EdType` EdType varchar(10) NOT NULL";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-05-04 13:15:01');
}

if($version<'2015-05-07 11:45:00') {
	$q="ALTER TABLE `Finals` ADD `FinNotes` varchar(30) NOT NULL";
	$r=safe_w_sql($q,false,array(1060));
	$q="ALTER TABLE `TeamFinals` ADD `TfNotes` varchar(30) NOT NULL";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-05-07 11:45:00');
}

if($version<'2015-05-07 19:45:00') {
	$q="ALTER TABLE `Eliminations` ADD index ElDateTime (ElTournament, ElDateTime)";
	$r=safe_w_sql($q,false,array(1060));

	$q="ALTER TABLE `Finals` ADD index FinDateTime (FinTournament, FinDateTime)";
	$r=safe_w_sql($q,false,array(1060));

	$q="ALTER TABLE `TeamFinals` ADD index TfDateTime (TfTournament, TfDateTime)";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-05-07 19:45:00');
}

if($version<'2015-05-11 17:45:00') {
	$q="ALTER TABLE `Targets` ADD TarStars varchar (10) NOT NULL after TarArray";
	$r=safe_w_sql($q,false,array(1060));

	safe_w_sql("update Targets set TarStars='a-j' where TarId in (1, 3)");
	safe_w_sql("update Targets set TarStars='ag-j' where TarId in (2, 4)");
	safe_w_sql("update Targets set TarStars='a-j' where TarId in (5)");
	safe_w_sql("update Targets set TarStars='a-f' where TarId in (6, 13)");
	safe_w_sql("update Targets set TarStars='a' where TarId in (7)");
	safe_w_sql("update Targets set TarStars='afil' where TarId in (8)");
	safe_w_sql("update Targets set TarStars='af-j' where TarId in (9)");
	safe_w_sql("update Targets set TarStars='ag-j' where TarId in (10)");
	safe_w_sql("update Targets set TarStars='acfhln' where TarId in (11)");
	safe_w_sql("update Targets set TarStars='aflq' where TarId in (12)");
	db_save_version('2015-05-11 17:42:00');
}

if($version<'2015-05-14 19:45:00') {
	$q="ALTER TABLE `Awards` ADD `AwAwarderGrouping` text NOT NULL after AwAwarders";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-05-14 19:45:00');
}

if($version<'2015-05-21 14:45:00') {
	$q="replace into TourTypes set TtId=33, TtType='type_ITA_TrofeoCONI', TtDistance=1, TtOrderBy=33";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-05-21 14:45:00');
}

if($version<'2015-08-17 16:15:00') {
	$r=safe_w_sql("ALTER TABLE `CasTeam` change `CaSubTeam` `CaSubTeam` tinyint(4) NOT NULL",false,array(1054));
	$r=safe_w_sql("ALTER TABLE `ClubTeam` change `CTSubTeam` `CTSubTeam` tinyint(4) NOT NULL",false,array(1054));
	$r=safe_w_sql("ALTER TABLE `TeamComponent` change `TcSubTeam` `TcSubTeam` tinyint(4) NOT NULL",false,array(1054));
	$r=safe_w_sql("ALTER TABLE `TeamFinals` change `TfSubTeam` `TfSubTeam` tinyint(4) NOT NULL",false,array(1054));
	$r=safe_w_sql("ALTER TABLE `TeamFinComponent` change `TfcSubTeam` `TfcSubTeam` tinyint(4) NOT NULL",false,array(1054));
	$r=safe_w_sql("ALTER TABLE `Teams` change `TeSubTeam` `TeSubTeam` tinyint(4) NOT NULL",false,array(1054));
	db_save_version('2015-08-17 16:15:00');
}

if($version<'2015-11-28 09:45:01') {
	$q="ALTER TABLE `TVParams` ADD `TVPSettings` text NOT NULL";
	$r=safe_w_sql($q,false,array(1060));
	$q="ALTER TABLE `TVRules` ADD `TVRSettings` text NOT NULL";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-11-28 09:45:01');
}

if($version<'2015-12-02 17:20:01') {
	$q="ALTER TABLE `RecTournament` ADD `RtRecPhase` tinyint not null, add index RtRecPhase(RtTournament, RtRecType, RtRecCode, RtRecTeam, RtRecCategory, RtRecPhase)";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-12-02 17:20:01');
}

if($version<'2015-12-27 11:45:00') {
	$q="ALTER TABLE `IskDevices` ADD `IskDvProActive` tinyint NOT NULL, add IskDvProConnected tinyint not null";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2015-12-27 11:45:00');
}

