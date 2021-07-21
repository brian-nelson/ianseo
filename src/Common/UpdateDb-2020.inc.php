<?php

if($version<'2020-01-25 09:29:02') {
	$q="ALTER TABLE `Awarded` ADD index (AwTournament, AwDivision, AwClass, AwSubClass, AwEntry)";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2020-01-25 09:29:02');
}

if($version<'2020-02-25 11:29:02') {
	$q="ALTER TABLE `Events` ADD `EvMatchMultipleMatches` tinyint unsigned NOT NULL after EvFinalAthTarget";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2020-02-25 11:29:02');
}

if($version<'2020-03-21 12:00:03') {
	$q="ALTER TABLE `Finals` ADD `FinTbClosest` tinyint NOT NULL after FinTiebreak";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="ALTER TABLE `TeamFinals` ADD `TfTbClosest` tinyint NOT NULL after TfTiebreak";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="ALTER TABLE `Individuals` ADD `IndTbClosest` tinyint NOT NULL after IndTieBreak";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="ALTER TABLE `Teams` ADD `TeTbClosest` tinyint NOT NULL after TeTieBreak";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="ALTER TABLE `TeamComponent` ADD `TcIrmType` tinyint NOT NULL";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="ALTER TABLE `TeamFinComponent` ADD `TfcIrmType` tinyint NOT NULL";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2020-03-21 12:00:03');
}

if($version<'2020-03-23 12:00:02') {
	$q="ALTER TABLE `Countries` ADD `CoLevelBitmap` tinyint NOT NULL default 4";
	$r=safe_w_sql($q,false,array(1146, 1060));

	$q="CREATE TABLE IF NOT EXISTS `CountryLevels` (
		`ClBit` tinyint NOT NULL,
		`ClCountryLevel` varchar(4) NOT NULL,
		`ClRecordLevel` varchar(3) NOT NULL,
		primary key (ClBit)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1146, 1060));

	safe_w_sql("Insert into CountryLevels values (1,'Seas','SB'),(2,'Pers','PB'),(4,'Club','CLR'),(8,'',''),(16,'Natl','NR'),(32,'Cont','CR'),(64,'Game','GR'),(128,'Earth','WR')");
	db_save_version('2020-03-23 12:00:02');
}

if($version<'2020-03-24 12:00:04') {
	$q="ALTER TABLE `Eliminations` drop `ElimIrmType`";
	$r=safe_w_sql($q,false,array(1146, 1060, 1091));
	$q="ALTER TABLE `Eliminations` ADD `ElIrmType` tinyint NOT NULL";
	$r=safe_w_sql($q,false,array(1146, 1060, 1091));

	db_save_version('2020-03-24 12:00:04');
}

if($version<'2020-03-27 12:04:00') {
    safe_w_sql("INSERT INTO `TourTypes` (`TtId`, `TtType`, `TtDistance`, `TtOrderBy`) VALUES('40', 'Type_LocalUK', '4', '40')",false,array(1060));
    db_save_version('2020-03-27 12:04:00');
}

if($version<'2020-04-04 15:25:00') {
    $q = "ALTER TABLE `Eliminations` ADD `ElTbClosest` tinyint NOT NULL after ElTiebreak";
    $r = safe_w_sql($q, false, array(1146, 1060));
    db_save_version('2020-04-04 15:25:00');
}

if($version<'2020-04-20 15:25:00') {
	// updates the Tie Break Closest flag in all competitions already in the local DB.
	updateTbClosest_20200404();
    db_save_version('2020-04-20 15:25:00');
}

if($version<'2020-04-30 15:50:00') {
    safe_w_sql("ALTER TABLE `TeamFinComponent` ADD `TfcTimeStamp` DATETIME NOT NULL AFTER `TfcIrmType`", false, array(1146, 1060));
    db_save_version('2020-04-30 15:50:00');
}

if($version<'2020-05-02 12:50:01') {
    safe_w_sql("ALTER TABLE `LookUpEntries` ADD KEY `LueIocCode` (`LueIocCode`);", false, array(1061));
    db_save_version('2020-05-02 12:50:01');
}

if($version<'2020-05-11 12:50:01') {
    safe_w_sql("ALTER TABLE `Finals` ADD `FinTbDecoded` varchar(15) not null after FinTbClosest;", false, array(1061));
    safe_w_sql("ALTER TABLE `TeamFinals` ADD `TfTbDecoded` varchar(15) not null after TfTbClosest;", false, array(1061));
    db_save_version('2020-05-11 12:50:01');
}

if($version<'2020-05-15 12:50:01') {
    safe_w_sql("ALTER TABLE `Individuals` ADD `IndTbDecoded` varchar(15) not null after IndTbClosest;", false, array(1061));
    safe_w_sql("ALTER TABLE `Eliminations` ADD `ElTbDecoded` varchar(15) not null after ElTbClosest;", false, array(1061));
    safe_w_sql("Update IrmTypes set IrmShowRank=0  where IrmType='DNS';", false, array(1061));
    db_save_version('2020-05-15 12:50:01');
}

if($version<'2020-05-18 12:50:01') {
    safe_w_sql("ALTER TABLE `Teams` ADD `TeTbDecoded` varchar(15) not null after TeTbClosest;", false, array(1061));
    db_save_version('2020-05-18 12:50:01');
}

if($version<'2020-05-18 19:50:01') {
    safe_w_sql("insert into Targets set TarId=17, TarDescr='TrgImperial', TarArray='TrgImperial', TarStars='abdfh', TarOrder=17, TarFullSize=100, B_size=100, B_color='FFFFFF', D_size=80, D_color='000000', F_size=60, F_color='00A3D1', H_size=40, H_color='ED2939', J_size=20, J_color='F9E11E';", false, array(1061));
    db_save_version('2020-05-18 19:50:01');
}

if($version<'2020-05-28 12:00:02') {
	$r=safe_w_sql("drop TABLE if exists ClassWaEquivalents",false,array(1146, 1060));
	$r=safe_w_sql("CREATE TABLE IF NOT EXISTS `ClassWaEquivalents` (
		`ClWaEqFrom` tinyint NOT NULL,
		`ClWaEqTo` tinyint NOT NULL,
		`ClWaEqEvent` varchar(4) NOT NULL,
		`ClWaEqGender` tinyint NOT NULL,
		`ClWaEqDivision` varchar(2) NOT NULL,
		`ClWaEqAgeClass` varchar(2) NOT NULL,
		`ClWaEqMain` TINYINT NOT NULL,
		primary key (ClWaEqEvent,ClWaEqGender,ClWaEqDivision,ClWaEqAgeClass),
		index (ClWaEqDivision, ClWaEqGender, ClWaEqFrom, ClWaEqTo)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8",false,array(1146, 1060));

	safe_w_sql("alter table RecTournament add RtRecCatEquivalents varchar(25) not null after RtRecCategory",false,array(1146, 1060));
	safe_w_sql("update RecTournament set RtRecCatEquivalents=RtRecCategory where RtRecCatEquivalents=''");

	$r=safe_w_sql("drop TABLE if exists RecAreas",false,array(1146, 1060));
	$r=safe_w_sql("CREATE TABLE IF NOT EXISTS `RecAreas` (
		`ReArCode` varchar(20) NOT NULL,
		`ReArName` varchar(50) NOT NULL,
		`ReArBitLevel` tinyint NOT NULL,
		primary key (ReArCode),
		index (ReArBitLevel, ReArName)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8",false,array(1146, 1060));

	safe_w_sql("alter table TourTypes add TtWaEquivalent int not null",false,array(1146, 1060));
	safe_w_sql("update TourTypes set TtWaEquivalent=1 where TtId =3");
	safe_w_sql("update TourTypes set TtWaEquivalent=10 where TtId =22");
	safe_w_sql("update TourTypes set TtWaEquivalent=10 where TtId =6");
	safe_w_sql("update TourTypes set TtWaEquivalent=11 where TtId =7");
	safe_w_sql("update TourTypes set TtWaEquivalent=12 where TtId =8");
	safe_w_sql("update TourTypes set TtWaEquivalent=15 where TtId =10");
	safe_w_sql("update TourTypes set TtWaEquivalent=17 where TtId =13");
	safe_w_sql("update TourTypes set TtWaEquivalent=2 where TtId =1");
	safe_w_sql("update TourTypes set TtWaEquivalent=25 where TtId =37");
	safe_w_sql("update TourTypes set TtWaEquivalent=3 where TtId =2");
	safe_w_sql("update TourTypes set TtWaEquivalent=4 where TtId =4");
	safe_w_sql("update TourTypes set TtWaEquivalent=5 where TtId =5");

	db_save_version('2020-05-28 12:00:02');
}

if($version<'2020-05-29 15:25:02') {
	updateTbDecoded_20200519();
    db_save_version('2020-05-29 15:25:02');
}

if($version<'2020-06-01 12:00:02') {
	$q="ALTER TABLE `ClassWaEquivalents` ADD `ClWaEqTeam` tinyint NOT NULL";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2020-06-01 12:00:02');
}

if($version<'2020-06-01 19:00:04') {
	safe_w_sql("ALTER TABLE `RecAreas` add ReArMaCode varchar(10) not null, change ReArBitLevel ReArBitLevel tinyint unsigned NOT NULL",false, array(1146, 1060));
	safe_w_sql("ALTER TABLE `CountryLevels` change ClBit ClBit tinyint unsigned NOT NULL",false, array(1146, 1060));
	safe_w_sql("ALTER TABLE `ClassWaEquivalents` change ClWaEqFrom ClWaEqFrom tinyint unsigned NOT NULL, change ClWaEqTo ClWaEqTo tinyint unsigned NOT NULL",false, array(1146, 1060));
	safe_w_sql("ALTER TABLE `Individuals` change IndRecordBitmap IndRecordBitmap tinyint unsigned NOT NULL",false, array(1146, 1060));
	safe_w_sql("ALTER TABLE `Finals` change FinRecordBitmap FinRecordBitmap tinyint unsigned NOT NULL",false, array(1146, 1060));
	safe_w_sql("ALTER TABLE `TeamFinals` change TfRecordBitmap TfRecordBitmap tinyint unsigned NOT NULL",false, array(1146, 1060));
	safe_w_sql("ALTER TABLE `Teams` change TeRecordBitmap TeRecordBitmap tinyint unsigned NOT NULL",false, array(1146, 1060));

	db_save_version('2020-06-01 19:00:04');
}

if($version<'2020-07-24 15:13:00') {
	safe_w_sql("ALTER TABLE `Events` ADD `EvMultiTeamNo` TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER `EvMultiTeam`",false, array(1146, 1060));
	db_save_version('2020-07-24 15:13:00');
}

if($version<'2020-07-30 14:45:00') {
	safe_w_sql("ALTER TABLE `Teams` CHANGE `TeRank` `TeRank` SMALLINT NOT NULL",false, array(1146, 1060));
	db_save_version('2020-07-30 14:45:00');
}

if($version<'2020-08-20 20:32:00') {
    safe_w_sql("REPLACE INTO `TourTypes` (`TtId`, `TtType`, `TtDistance`, `TtOrderBy`, `TtWaEquivalent`) VALUES 
        ('41', 'Type_NL_YouthFita', '3', '41', ''), 
        ('42', 'Type_NL_25p1', '1', '42', ''), 
        ('43', 'Type_NL_Hout', '1', '43', ''),
        ('44', 'Type_CH_Federal', '4', '44', '')",false, array(1060));
    db_save_version('2020-08-20 20:32:00');
}

if($version<'2020-10-01 22:20:00') {
    safe_w_sql("INSERT INTO `Targets` 
        (`TarId`, `TarDescr`, `TarArray`, `TarStars`, `TarOrder`, `TarFullSize`, 
         `A_size`, `A_color`, `B_size`, `B_color`, `C_size`, `C_color`, `D_size`, `D_color`, `E_size`, `E_color`, 
         `F_size`, `F_color`, `G_size`, `G_color`, `H_size`, `H_color`, `I_size`, `I_color`, `J_size`, `J_color`, 
         `K_size`, `K_color`, `L_size`, `L_color`, `M_size`, `M_color`, `N_size`, `N_color`, `O_size`, `O_color`, 
         `P_size`, `P_color`, `Q_size`, `Q_color`, `R_size`, `R_color`, `S_size`, `S_color`, `T_size`, `T_color`, 
         `U_size`, `U_color`, `V_size`, `V_color`, `W_size`, `W_color`, `X_size`, `X_color`, `Y_size`, `Y_color`, 
         `Z_size`, `Z_color`, `TarDummyLine`) VALUES 
         (18, 'TrgNfaa3D', 'TrgNfaa3D', 'afil', '9', '0', '0', '', '0', 'FFFFFF', '0', 'FFFFFF', '0', '000000', 
          '0', '000000', '60', '00A3D1', '0', '00A3D1', '0', 'ED2939', '30', '00A3D1', '0', 'F9E11E', '0', 'F9E11E', 
          '15', 'ED2939', '0', '', '5', 'F9E11E', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', 
          '0', '', '0', '', '0', '', '0', '', '0')",false, array(1060));
    db_save_version('2020-10-01 22:20:00');
}

if($version<'2020-10-28 12:00:02') {
	if(safe_server_version()>=50600) {
		$DateTime='datetime(3)';
	} else {
		$DateTime='datetime';
	}
	$q="CREATE TABLE IF NOT EXISTS `Logs` (
		`LogTournament` int not null,
		`LogType` varchar(20) NOT NULL,
		`LogEntry` int NOT NULL,
		`LogMessage` text NOT NULL,
		`LogTimestamp` $DateTime NOT NULL,
		`LogIP` varchar(15) NOT NULL,
		primary key (LogTournament, LogType, LogEntry, LogTimestamp),
		index (LogType, LogTournament, LogTimestamp)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2020-10-28 12:00:02');
}

if($version<'2020-10-30 12:00:02') {
	$q="alter table Emails
		add EmFrom varchar(50) not null,
    	add EmCc varchar(100) not null,
    	add EmBcc varchar(50) not null";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2020-10-30 12:00:02');
}

if($version<'2020-10-30 19:00:02') {
	$q="alter table Logs
		add LogTitle varchar(20) not null after LogType";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2020-10-30 19:00:02');
}

if($version<'2020-11-01 15:13:01') {
	// check the field is still there
	$q=safe_r_sql("SHOW COLUMNS FROM `RecTournament` LIKE 'RtRecType'");
	if(safe_num_rows($q)) {
		// prepare the drop of RtRecType and TrRecType
		safe_w_sql("update RecTournament set RtRecType='', RtRecCode='WAE-CR' where RtRecType='CR' and RtRecCode='EMAU'",false, array(1146, 1060, 1054));
		safe_w_sql("update RecTournament set RtRecType='', RtRecCode='WR' where RtRecType='WR' and RtRecCode='WA'",false, array(1146, 1060, 1054));
		safe_w_sql("update RecTournament set RtRecType='', RtRecCode='OR' where RtRecType='OR' and RtRecCode='WA'",false, array(1146, 1060, 1054));
		safe_w_sql("update RecTournament set RtRecType='', RtRecCode='WAAM-CR' where RtRecType='CR' and RtRecCode='WAAM'",false, array(1146, 1060, 1054));
		safe_w_sql("update RecTournament set RtRecType='', RtRecCode='WAE-CR' where RtRecType='CR' and RtRecCode='WAE'",false, array(1146, 1060, 1054));
		safe_w_sql("update RecTournament set RtRecType='', RtRecCode='WAAS-CR' where RtRecType='CR' and RtRecCode='WAAS'",false, array(1146, 1060, 1054));
		safe_w_sql("update RecTournament set RtRecType='', RtRecCode='WAE-CG-GR' where RtRecType='GR' and RtRecCode='WAE-CG'",false, array(1146, 1060, 1054));
		safe_w_sql("update RecTournament set RtRecType='', RtRecCode='WAAM-CG-GR' where RtRecType='GR' and RtRecCode='WAAM-CG'",false, array(1146, 1060, 1054));
		safe_w_sql("update RecTournament set RtRecType='', RtRecCode='WAAS-CG-GR' where RtRecType='GR' and RtRecCode='WAAS-CG'",false, array(1146, 1060, 1054));
		safe_w_sql("update RecTournament set RtRecType='', RtRecCode='FISU-GR' where RtRecType='GR' and RtRecCode='FISU'",false, array(1146, 1060, 1054));

		safe_w_sql("update TourRecords set TrRecType='', TrRecCode='WAE-CR' where TrRecType='CR' and TrRecCode='EMAU'",false, array(1146, 1060, 1054));
		safe_w_sql("update TourRecords set TrRecType='', TrRecCode='WR' where TrRecType='WR' and TrRecCode='WA'",false, array(1146, 1060, 1054));
		safe_w_sql("update TourRecords set TrRecType='', TrRecCode='OR' where TrRecType='OR' and TrRecCode='WA'",false, array(1146, 1060, 1054));
		safe_w_sql("update TourRecords set TrRecType='', TrRecCode='WAAM-CR' where TrRecType='CR' and TrRecCode='WAAM'",false, array(1146, 1060, 1054));
		safe_w_sql("update TourRecords set TrRecType='', TrRecCode='WAE-CR' where TrRecType='CR' and TrRecCode='WAE'",false, array(1146, 1060, 1054));
		safe_w_sql("update TourRecords set TrRecType='', TrRecCode='WAAS-CR' where TrRecType='CR' and TrRecCode='WAAS'",false, array(1146, 1060, 1054));
		safe_w_sql("update TourRecords set TrRecType='', TrRecCode='WAE-CG-GR' where TrRecType='GR' and TrRecCode='WAE-CG'",false, array(1146, 1060, 1054));
		safe_w_sql("update TourRecords set TrRecType='', TrRecCode='WAAM-CG-GR' where TrRecType='GR' and TrRecCode='WAAM-CG'",false, array(1146, 1060, 1054));
		safe_w_sql("update TourRecords set TrRecType='', TrRecCode='WAAS-CG-GR' where TrRecType='GR' and TrRecCode='WAAS-CG'",false, array(1146, 1060, 1054));
		safe_w_sql("update TourRecords set TrRecType='', TrRecCode='FISU-GR' where TrRecType='GR' and TrRecCode='FISU'",false, array(1146, 1060, 1054));

		// performs the drop of the field and update of table structure
		// Maria DB has a singular behaviour so we first drop indices then the column
		safe_w_sql("ALTER TABLE `TourRecords` drop PRIMARY KEY",false,array(1146, 1060, 1091));
		safe_w_sql("alter table `TourRecords` drop TrRecType",false, array(1146, 1060, 1091));
		safe_w_sql("ALTER TABLE `TourRecords` ADD PRIMARY KEY (`TrTournament`,`TrRecCode`,`TrRecTeam`,`TrRecPara`)",false,array(1146, 1060, 1091));

		safe_w_sql("ALTER TABLE `RecTournament` drop PRIMARY KEY",false,array(1146, 1060, 1091));
		safe_w_sql("ALTER TABLE `RecTournament` drop index RtTournament",false,array(1146, 1060, 1091));
		safe_w_sql("ALTER TABLE `RecTournament` drop index RtTournament_2",false,array(1146, 1060, 1091));
		safe_w_sql("ALTER TABLE `RecTournament` drop index RtRecPhase",false,array(1146, 1060, 1091));
		safe_w_sql("alter table `RecTournament` drop RtRecType",false, array(1146, 1060, 1091));
		safe_w_sql("alter table RecTournament ADD RtRecLocalCategory VARCHAR(4) NOT NULL after RtRecCategory, 
	        add RtRecLocalEquivalents VARCHAR(25) not null after RtRecCatEquivalents, 
	        ADD RtRecTargetCode VARCHAR(5) NOT NULL, 
		    add RtRecComponents tinyint unsigned not null default 1, 
	        add RtRecTarget varchar(5) not null,
			add RtRecMeters tinyint unsigned not null,
			add RtRecMaxScore int unsigned not null,
	        ADD `RtRecCategoryName` varchar(50) NOT NULL after RtRecCategory, 
	        ADD `RtRecDivision` varchar(2) NOT NULL after RtRecCode,
	        add RtRecDouble tinyint unsigned not null",false, array(1146, 1060, 1091));
		safe_w_sql("alter table `RecTournament` add primary key (RtTournament, RtRecCode, RtRecTeam, RtRecCategory, RtRecPhase, RtRecSubphase, RtRecDouble, RtRecPara, RtRecMeters),
	       add index RtRecPhase (RtTournament, RtRecCode, RtRecTeam, RtRecCategory, RtRecPhase, RtRecSubphase)");
		safe_w_sql("update RecTournament set RtRecLocalCategory=RtRecCategory, RtRecLocalEquivalents=RtRecCatEquivalents where RtRecLocalCategory=''",false,array(1146, 1060));

		safe_w_sql("alter table Events add EvIsPara tinyint unsigned not null",false, array(1146, 1060, 1091));
		safe_w_sql("alter table Divisions add DivIsPara tinyint unsigned not null",false, array(1146, 1060, 1091));
		safe_w_sql("alter table Classes add ClIsPara tinyint unsigned not null",false, array(1146, 1060, 1091));
		safe_w_sql("alter table Entries add EnClassified tinyint unsigned not null after EnSex",false, array(1146, 1060, 1091));
		safe_w_sql("alter table LookUpEntries add LueClassified tinyint unsigned not null after LueSex",false, array(1146, 1060, 1091));
		safe_w_sql("alter table TargetFaces add TfWaTarget varchar(5) not null",false, array(1146, 1060, 1091));
		safe_w_sql("ALTER TABLE `Countries` ADD `CoMaCode` VARCHAR(5) NOT NULL, add CoCaCode VARCHAR(5) not null",false,array(1146, 1060, 1091));
		safe_w_sql("ALTER TABLE `TournamentDistances` ADD TdDist1 tinyint unsigned not null, ADD TdDist2 tinyint unsigned not null, ADD TdDist3 tinyint unsigned not null, ADD TdDist4 tinyint unsigned not null, ADD TdDist5 tinyint unsigned not null, ADD TdDist6 tinyint unsigned not null, ADD TdDist7 tinyint unsigned not null, ADD TdDist8 tinyint unsigned not null",false,array(1146, 1060, 1901));
		safe_w_sql("alter table TeamFinComponent ADD index (`TfcTournament`,`TfcEvent`,`TfcCoId`,`TfcSubTeam`,`TfcOrder`)",false, array(1146, 1060, 1091));

		safe_w_sql("alter table TourRecords add `TrHeaderCode` varchar(2) NOT NULL,
			add `TrHeader` varchar(25) NOT NULL,
			add `TrFontFile` varchar(50) NOT NULL,
			add `TrDownload` datetime NOT NULL,
			add `TrUpdated` datetime NOT NULL",false, array(1146, 1060, 1091));

		safe_w_sql("ALTER TABLE `RecAreas` 
	        ADD `ReArWaMaintenance` tinyint NOT NULL, 
	        add ReArOdfCode varchar(3) not null, 
	        add ReArOdfHeader varchar(50) not null, 
	        add ReArOdfParaCode varchar(3) not null, 
	        add ReArOdfParaHeader varchar(50) not null",false,array(1146, 1060, 1091));

		safe_w_sql("update TournamentDistances set 
			TdDist1=Td1+0,
			TdDist2=Td2+0,
			TdDist3=Td3+0,
			TdDist4=Td4+0,
			TdDist5=Td5+0,
			TdDist6=Td6+0,
			TdDist7=Td7+0,
			TdDist8=Td8+0", false, array(1060));

		safe_w_sql("drop TABLE if exists `Records`",false,array(1146, 1060));
		safe_w_sql("drop table if exists RecHeaders",false, array(1146, 1060));

		safe_w_SQL("drop table if exists RecTargetFaces");
		safe_w_sql("create table RecTargetFaces (
	        RtfId varchar(5) not null primary key,
	        RtfDescription varchar(40) not null,
	        RtfDiameter tinyint unsigned not null
			) engine=MyISAM", false, array(1060));

		safe_w_sql("insert into RecTargetFaces values
			('40X', '40cm Small 10 (Compound)', 40),
			('40', '40cm Big 10 (Recurve)', 40),
			('60X', '60cm Small 10 (Compound)', 60),
			('60', '60cm Big 10 (Recurve)', 60),
			('80', '80cm', 80),
			('122', '122cm', 122),
			('9753', '90m-70m: 122cm; 50m-30m: 80cm', 0),
			('7653', '70m-60m: 122cm; 50m-30m: 80cm', 0),
			('6543', '60m-50m: 122cm; 40m-30m: 80cm', 0),
			('3333', '30m: 60cm; 80cm; 80cm; 122cm', 0)
			");


		safe_w_sql("drop table if exists RecBroken");
		safe_w_sql("create table RecBroken (
	        RecBroTournament int not null,
	        RecBroAthlete int not null,
	        RecBroTeam int not null,
	        RecBroSubTeam int not null,
	        RecBroRecCode varchar(25) not null,
	        RecBroRecCategory varchar(8) not null,
	        RecBroRecPara tinyint unsigned not null,
	        RecBroRecTeam tinyint unsigned not null,
	        RecBroRecPhase tinyint unsigned not null,
	        RecBroRecSubPhase tinyint unsigned not null,
	        RecBroRecDouble tinyint unsigned not null,
	        RecBroRecMeters tinyint unsigned not null,
	        RecBroRecEvent varchar(4) not null,
	        RecBroRecMatchno tinyint unsigned not null,
	        RecBroRecDate datetime not null,
	        primary key (RecBroTournament, RecBroAthlete, RecBroTeam, RecBroSubTeam, RecBroRecCode, RecBroRecCategory, RecBroRecPara, RecBroRecTeam, RecBroRecPhase, RecBroRecSubPhase, RecBroRecDouble, RecBroRecMeters, RecBroRecEvent, RecBroRecMatchno)
			) engine=MyISAM", false, array(1060));

		// recreate a clean table
		safe_w_sql("drop table if exists ClassWaEquivalents",false,array(1146, 1060));
		safe_w_sql("CREATE TABLE `ClassWaEquivalents` (
			`ClWaEqTourRule` varchar(16) NOT NULL,
			`ClWaEqFrom` tinyint(3) unsigned NOT NULL,
			`ClWaEqTo` tinyint(3) unsigned NOT NULL,
			`ClWaEqEvent` varchar(4) NOT NULL,
			`ClWaEqDescription` varchar(60) NOT NULL,
			`ClWaEqGender` tinyint(4) NOT NULL,
			`ClWaEqDivision` varchar(2) NOT NULL,
			`ClWaEqAgeClass` varchar(2) NOT NULL,
			`ClWaEqMain` tinyint(4) NOT NULL,
			`ClWaEqTeam` tinyint(4) NOT NULL,
			`ClWaEqMixedTeam` tinyint(3) unsigned NOT NULL,
			`ClWaEqPara` tinyint(3) unsigned NOT NULL,
			`ClWaEqComponents` tinyint(3) unsigned NOT NULL DEFAULT '1',
			`ClWaEqOrder` int unsigned NOT NULL,
			PRIMARY KEY (`ClWaEqTourRule`,`ClWaEqEvent`,`ClWaEqGender`,`ClWaEqDivision`,`ClWaEqAgeClass`),
			index (`ClWaEqTourRule`,`ClWaEqDivision`,`ClWaEqGender`,`ClWaEqFrom`,`ClWaEqTo`, ClWaEqOrder),
			index (`ClWaEqDivision`,`ClWaEqGender`,`ClWaEqFrom`,`ClWaEqTo`,`ClWaEqAgeClass`,`ClWaEqTeam`,`ClWaEqTourRule`, ClWaEqOrder),
			index (`ClWaEqGender`,`ClWaEqMixedTeam`,`ClWaEqComponents`,`ClWaEqTeam`,`ClWaEqDescription`,`ClWaEqTourRule`,`ClWaEqFrom`,`ClWaEqTo`,`ClWaEqAgeClass`, ClWaEqOrder)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8",false,array(1146, 1060));
	}

	db_save_version('2020-11-01 15:13:01');
}

if($version<'2020-11-20 18:13:01') {
	$q="ALTER TABLE `IskDevices` change IskDvCode IskDvCode varchar(4) NOT NULL, add index (IskDvCode, IskDvTournament)";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2020-11-20 18:13:01');
}

if($version<'2020-11-25 09:13:01') {
	$q="Insert ignore into IrmTypes set IrmId=7, IrmType='DNF'";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2020-11-25 09:13:01');
}

if($version<'2020-11-29 10:00:01') {
    $q="INSERT INTO `TourTypes` (`TtId`, `TtType`, `TtDistance`, `TtOrderBy`, `TtWaEquivalent`) VALUES ('45', 'Type_FR_Kyudo', '1', '45', '0')";
    $r=safe_w_sql($q,false,array(1146, 1060));

    db_save_version('2020-11-29 10:00:01');
}

if($version<'2020-11-30 10:00:00') {
    $q="INSERT INTO `Targets` (`TarId`, `TarDescr`, `TarArray`, `TarStars`, `TarOrder`, `TarFullSize`, `A_size`, `A_color`, `B_size`, `B_color`, `C_size`, `C_color`, `D_size`, `D_color`, `E_size`, `E_color`, `F_size`, `F_color`, `G_size`, `G_color`, `H_size`, `H_color`, `I_size`, `I_color`, `J_size`, `J_color`, `K_size`, `K_color`, `L_size`, `L_color`, `M_size`, `M_color`, `N_size`, `N_color`, `O_size`, `O_color`, `P_size`, `P_color`, `Q_size`, `Q_color`, `R_size`, `R_color`, `S_size`, `S_color`, `T_size`, `T_color`, `U_size`, `U_color`, `V_size`, `V_color`, `W_size`, `W_color`, `X_size`, `X_color`, `Y_size`, `Y_color`, `Z_size`, `Z_color`, `TarDummyLine`) 
    VALUES(19, 'TrgKyudo', 'TrgKyudo', 'a', '18', '36', '0', '000000', '9', 'FFFFFF', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', 'ED2939', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0')";

    $r=safe_w_sql($q,false,array(1146, 1060));

    db_save_version('2020-11-30 10:00:00');
}
