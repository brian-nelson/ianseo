<?php

if($version<'2012-01-11 10:20:00') {
	$q="SELECT ToId FROM Tournament ";
	$r=safe_r_sql($q);
	if (safe_num_rows($r)>0)
	{
		while ($row=safe_fetch($r))
		{
			Update3DIta_20120111($row->ToId);
		}
	}
	db_save_version('2012-01-11 10:20:00');
}

if($version<'2012-01-15 16:00:00') {
	$q="ALTER TABLE `TVParams` ADD `TVPColumns` varchar(255) NOT NULL AFTER `TVPPhasesTeam` ";
	$r=safe_w_sql($q,false,array(1060));

	$q="update `TVParams` set `TVPColumns`='ALL' ";
	db_save_version('2012-01-15 16:00:00');
}

if($version<'2012-01-18 12:00:03') {
	$q="ALTER TABLE `Tournament` change `ToBlock` `ToBlock` int(2) unsigned NOT NULL";
	$r=safe_w_sql($q,false,array(1060));

	safe_w_sql("update `Tournament` set `ToBlock`='0' where ToBlock!=63");
	safe_w_sql("update `Tournament` set `ToBlock`='65535' where ToBlock=63");

	safe_w_sql("drop table if exists ACL");

	$MySql="CREATE TABLE IF NOT EXISTS `ACL` (
		`AclTournament` int( 11 ) NOT NULL ,
		`AclIP` VARCHAR(15) NOT NULL ,
		`AclNick` VARCHAR(50) NOT NULL ,
		`AclAccess` int(2) unsigned NOT NULL ,
		PRIMARY KEY ( `AclTournament`, AclIP )
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT = 'Defines the IP-based Access Control List'";
	safe_w_SQL($MySql);

	db_save_version('2012-01-18 12:00:03');
}

if($version<'2012-01-20 10:36:00') {
	$q="ALTER TABLE `TVSequence` ADD `TVSFullScreen` varchar(1) NOT NULL ";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2012-01-20 10:36:00');
}

if($version<'2012-01-24 15:16:00') {
/* aggancio l'id del torneo alla griglia f2f */

// per prima cosa distruggo la griglia
	$q="DROP TABLE IF EXISTS `F2FGrid`";
	$r=safe_w_sql($q,false,array());

// poi la ricreo aggiungendo la colonna del torneo
	$q="
		CREATE TABLE IF NOT EXISTS `F2FGrid` (
		  `F2FTournament` int(10) unsigned NOT NULL,
		  `F2FPhase` tinyint(3) unsigned NOT NULL,
		  `F2FRound` tinyint(3) unsigned NOT NULL,
		  `F2FMatchNo1` tinyint(3) unsigned NOT NULL,
		  `F2FMatchNo2` tinyint(3) unsigned NOT NULL,
		  `F2FGroup` tinyint(3) unsigned NOT NULL,
		  PRIMARY KEY (`F2FTournament`,`F2FPhase`,`F2FRound`,`F2FMatchNo1`,`F2FMatchNo2`,`F2FGroup`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	";
	safe_w_SQL($q);

// tiro furi le gare di tipo 21 dal db
	$q="
		SELECT ToId	FROM Tournament WHERE ToType=21
	";
	$rs=safe_r_sql($q);

	if ($rs && safe_num_rows($rs)>0)
	{
		while ($row=safe_fetch($rs))
		{
			$q=insertIntoGridForF2F_21($row->ToId);
			$rs2=safe_w_sql($q,false,array(1062));
		}
	}

	db_save_version('2012-01-24 15:16:00');
}

if($version<'2012-01-26 14:48:00') {
	$q="ALTER TABLE `F2FEntries` ADD `F2FRankFinal` SMALLINT( 6 ) NOT NULL DEFAULT '0' AFTER `F2FRankScore` ;";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2012-01-26 14:48:00');
}

if($version<'2012-02-29 15:30:00') {
	$q="ALTER TABLE `Parameters` CHANGE `ParId` `ParId` VARCHAR( 15 ) NOT NULL ";
	$r=safe_w_sql($q,false,array(1060));
	SetParameter('OnClickMenu', '');

	db_save_version('2012-02-29 15:30:00');
}

if($version<'2012-03-31 18:30:00') {
	$q="ALTER TABLE `Tournament` ADD `ToOptions` text NOT NULL";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2012-03-31 18:30:00');
}

if($version<'2012-04-01 11:30:02') {
	$q="ALTER TABLE HhtData ADD `HdTimeStamp` DATETIME NOT NULL default '0000-00-00 00:00:00', ADD INDEX ( `HdTournament` , `HdTimeStamp` , `HdDistance` , `HdArrowStart` ) ";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2012-04-01 11:30:02');
}

if($version<'2012-05-07 10:15:00') {
	$q="CREATE TABLE  `ModulesParameters` (
		`MpModule` VARCHAR( 50 ) NOT NULL ,
		`MpParameter` VARCHAR( 20 ) NOT NULL ,
		`MpTournament` INT UNSIGNED NOT NULL ,
		`MpValue` TEXT NOT NULL ,
		PRIMARY KEY (  `MpModule` ,  `MpParameter` ,  `MpTournament` )
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1050));

	$q="ALTER TABLE  `Awards` ENGINE = MYISAM;";
	$r=safe_w_sql($q,false,array());

	$q="ALTER TABLE  `Scheduler` ENGINE = MYISAM;";
	$r=safe_w_sql($q,false,array());

	$q="ALTER TABLE  `Session` ENGINE = MYISAM;";
	$r=safe_w_sql($q,false,array());

	db_save_version('2012-05-07 10:15:00');
}

if($version<'2012-05-18 07:10:00') {
	$r=safe_w_sql('drop table if exists Records');
	$q="CREATE TABLE  `Records` (
		`ReType` VARCHAR( 2 ) NOT NULL ,
		`ReCode` VARCHAR( 25 ) NOT NULL ,
		`ReTeam` smallint(5) NOT NULL ,
		`RePara` varchar(1) NOT NULL ,
		`ReCategory` varchar(8) NOT NULL ,
		`ReTourType` smallint(5) NOT NULL ,
		`ReDistance` varchar(5) NOT NULL ,
		`ReTotal` smallint(5) NOT NULL ,
		`ReXNine` smallint(5) NOT NULL ,
		`ReDate` date NOT NULL ,
		`ReExtra` text NOT NULL ,
		`ReLastUpdated` datetime NOT NULL ,
		PRIMARY KEY (  `ReType`, `ReCode`, `ReTeam`, `RePara`, `ReCategory`, `ReTourType`, `ReDistance` )
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1050));

	$r=safe_w_sql('drop table if exists RecTournament');
	$q="CREATE TABLE  `RecTournament` (
		`RtTournament` int( 10 ) NOT NULL ,
		`RtRecType` VARCHAR( 2 ) NOT NULL ,
		`RtRecCode` VARCHAR( 25 ) NOT NULL ,
		`RtRecTeam` smallint(5) NOT NULL ,
		`RtRecPara` varchar(1) NOT NULL ,
		`RtRecCategory` varchar(8) NOT NULL ,
		`RtRecDistance` varchar(5) NOT NULL ,
		`RtRecTotal` smallint(5) NOT NULL ,
		`RtRecXNine` smallint(5) NOT NULL ,
		`RtRecDate` date NOT NULL ,
		`RtRecExtra` text NOT NULL ,
		`RtRecLastUpdated` datetime NOT NULL ,
		PRIMARY KEY (  `RtTournament`, `RtRecType`, `RtRecCode`, `RtRecTeam`, `RtRecPara`, `RtRecCategory`, `RtRecDistance` )
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1050));

	$q="CREATE TABLE  `TourRecords` (
		`TrTournament` int( 10 ) NOT NULL ,
		`TrRecType` VARCHAR( 2 ) NOT NULL ,
		`TrRecCode` VARCHAR( 25 ) NOT NULL ,
		`TrRecTeam` smallint(5) NOT NULL ,
		`TrRecPara` varchar(1) NOT NULL ,
		PRIMARY KEY (  `TrTournament`, `TrRecType`, `TrRecCode`, `TrRecTeam`, `TrRecPara` )
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1050));

	$q="ALTER TABLE `Tournament` ADD `ToRecCode` VARCHAR( 25 ) NOT NULL ";
	$r=safe_w_sql($q,false,array(1060));

	$q="ALTER TABLE `Divisions` ADD `DivRecDivision` VARCHAR( 4 ) NOT NULL, ADD `DivWaDivision` VARCHAR( 4 ) NOT NULL ";
	$r=safe_w_sql($q,false,array(1060));

	$q="ALTER TABLE `Classes` ADD `ClRecClass` VARCHAR( 4 ) NOT NULL, ADD `ClWaClass` VARCHAR( 4 ) NOT NULL ";
	$r=safe_w_sql($q,false,array(1060));

	$q="ALTER TABLE `Events` ADD `EvRecCategory` VARCHAR( 8 ) NOT NULL, ADD `EvWaCategory` VARCHAR( 8 ) NOT NULL ";
	$r=safe_w_sql($q,false,array(1060));

	safe_w_sql("update Divisions set DivWaDivision=DivId, DivRecDivision=DivId where DivWaDivision='' or DivRecDivision=''");
	safe_w_sql("update Classes set ClWaClass=ClId, ClRecClass=ClId where ClWaClass='' or ClRecClass=''");
	safe_w_sql("update Events set EvWaCategory=EvCode, EvRecCategory=EvCode where EvWaCategory='' or EvRecCategory=''");

	db_save_version('2012-05-18 07:10:00');
}

if($version<'2012-05-19 10:00:04') {
	$q="ALTER TABLE `TourRecords` ADD `TrColor` varchar(6) NOT NULL DEFAULT '000000'";
	$r=safe_w_sql($q,false,array(1060));

	$q="ALTER TABLE `Flags` ADD `FlContAssoc` varchar(10) NOT NULL";
	$r=safe_w_sql($q,false,array(1060));
	foreach(array(
		'AFG'=>'AAF',
		'ALG'=>'FAA',
		'ARG'=>'COPARCO',
		'ARM'=>'EMAU',
		'ARU'=>'COPARCO',
		'AUS'=>'OAC',
		'AUT'=>'EMAU',
		'AZE'=>'EMAU',
		'BAN'=>'AAF',
		'BAR'=>'COPARCO',
		'BEL'=>'EMAU',
		'BEN'=>'FAA',
		'BER'=>'COPARCO',
		'BHU'=>'AAF',
		'BIH'=>'EMAU',
		'BLR'=>'EMAU',
		'BOT'=>'FAA',
		'BRA'=>'COPARCO',
		'BUL'=>'EMAU',
		'CAF'=>'FAA',
		'CAM'=>'AAF',
		'CAN'=>'COPARCO',
		'CHA'=>'FAA',
		'CHI'=>'COPARCO',
		'CHN'=>'AAF',
		'CIV'=>'FAA',
		'CMR'=>'FAA',
		'COL'=>'COPARCO',
		'CRC'=>'COPARCO',
		'CRO'=>'EMAU',
		'CUB'=>'COPARCO',
		'CYP'=>'EMAU',
		'CZE'=>'EMAU',
		'DEN'=>'EMAU',
		'DMA'=>'COPARCO',
		'DOM'=>'COPARCO',
		'ECU'=>'COPARCO',
		'EGY'=>'FAA',
		'ERI'=>'FAA',
		'ESA'=>'COPARCO',
		'ESP'=>'EMAU',
		'EST'=>'EMAU',
		'FIJ'=>'OAC',
		'FIN'=>'EMAU',
		'FPO'=>'OAC',
		'FRA'=>'EMAU',
		'FRO'=>'EMAU',
		'GAB'=>'FAA',
		'GBR'=>'EMAU',
		'GEO'=>'EMAU',
		'GER'=>'EMAU',
		'GHA'=>'FAA',
		'GRE'=>'EMAU',
		'GUA'=>'COPARCO',
		'GUI'=>'FAA',
		'GUM'=>'OAC',
		'HAI'=>'COPARCO',
		'HKG'=>'AAF',
		'HON'=>'COPARCO',
		'HUN'=>'EMAU',
		'INA'=>'AAF',
		'IND'=>'AAF',
		'IRI'=>'AAF',
		'IRL'=>'EMAU',
		'IRQ'=>'AAF',
		'ISL'=>'EMAU',
		'ISR'=>'EMAU',
		'ITA'=>'EMAU',
		'JPN'=>'AAF',
		'KAZ'=>'AAF',
		'KEN'=>'FAA',
		'KGZ'=>'AAF',
		'KIR'=>'OAC',
		'KOR'=>'AAF',
		'KOS'=>'EMAU',
		'KSA'=>'AAF',
		'LAO'=>'AAF',
		'LAT'=>'EMAU',
		'LBA'=>'FAA',
		'LIB'=>'AAF',
		'LIE'=>'EMAU',
		'LTU'=>'EMAU',
		'LUX'=>'EMAU',
		'MAC'=>'AAF',
		'MAR'=>'FAA',
		'MAS'=>'AAF',
		'MDA'=>'EMAU',
		'MEX'=>'COPARCO',
		'MGL'=>'AAF',
		'MLT'=>'EMAU',
		'MNE'=>'EMAU',
		'MON'=>'EMAU',
		'MRI'=>'FAA',
		'MTN'=>'FAA',
		'MYA'=>'AAF',
		'NAM'=>'FAA',
		'NED'=>'EMAU',
		'NEP'=>'AAF',
		'NFI'=>'OAC',
		'NGR'=>'FAA',
		'NCA'=>'COPARCO',
		'NIG'=>'FAA',
		'NOR'=>'EMAU',
		'NZL'=>'OAC',
		'PAN'=>'COPARCO',
		'PAR'=>'COPARCO',
		'PER'=>'COPARCO',
		'PHI'=>'AAF',
		'PLW'=>'OAC',
		'PNG'=>'OAC',
		'POL'=>'EMAU',
		'POR'=>'EMAU',
		'PRK'=>'AAF',
		'PUR'=>'COPARCO',
		'QAT'=>'AAF',
		'ROU'=>'EMAU',
		'RSA'=>'FAA',
		'RUS'=>'EMAU',
		'SAM'=>'OAC',
		'SEN'=>'FAA',
		'SIN'=>'AAF',
		'SLE'=>'FAA',
		'SLO'=>'EMAU',
		'SMR'=>'EMAU',
		'SOL'=>'OAC',
		'SOM'=>'FAA',
		'SRB'=>'EMAU',
		'SRI'=>'AAF',
		'SUI'=>'EMAU',
		'SUR'=>'COPARCO',
		'SVK'=>'EMAU',
		'SWE'=>'EMAU',
		'SYR'=>'AAF',
		'TGA'=>'OAC',
		'THA'=>'AAF',
		'TJK'=>'AAF',
		'TKM'=>'AAF',
		'TOG'=>'FAA',
		'TPE'=>'AAF',
		'TRI'=>'COPARCO',
		'TUN'=>'FAA',
		'TUR'=>'EMAU',
		'UGA'=>'FAA',
		'UKR'=>'EMAU',
		'URU'=>'COPARCO',
		'USA'=>'COPARCO',
		'UZB'=>'AAF',
		'VAN'=>'OAC',
		'VEN'=>'COPARCO',
		'VIE'=>'AAF',
		'ASA'=>'COPARCO',
		'COD'=>'FAA',
		'KUW'=>'AAF',
		'PAK'=>'AAF',
		) as $k=>$v) safe_w_sql("update Flags set FlContAssoc='$v' where FlCode='$k' and FlIocCode='FITA' and FlTournament=-1");

//'AHO'=>'COPARCO',
//'ALB'=>'EMAU',
//'AND'=>'EMAU',
//'ANG'=>'FAA',
//'ANT'=>'COPARCO',
//'BAH'=>'COPARCO',
//'BDI'=>'FAA',
//'BIZ'=>'COPARCO',
//'BOL'=>'COPARCO',
//'BRN'=>'',
//'BRU'=>'',
//'BUR'=>'',
//'CAY'=>'',
//'CGO'=>'',
//'COK'=>'',
//'COM'=>'',
//'CPV'=>'',
//'DJI'=>'',
//'ETH'=>'FAA',
//'FSM'=>'',
//'GAM'=>'',
//'GBS'=>'',
//'GEQ'=>'',
//'GRN'=>'',
//'GUY'=>'',
//'ISV'=>'',
//'IVB'=>'',
//'JAM'=>'COPARCO',
//'JOR'=>'',
//'LBR'=>'',
//'LCA'=>'',
//'LES'=>'',
//'MAD'=>'',
//'MAW'=>'',
//'MDV'=>'',
//'MHL'=>'',
//'MKD'=>'',
//'MLI'=>'',
//'MOZ'=>'',
//'NRU'=>'',
//'OMA'=>'',
//'PLE'=>'',
//'RWA'=>'',
//'SEY'=>'',
//'SKN'=>'',
//'STP'=>'',
//'SUD'=>'',
//'SWZ'=>'',
//'TAN'=>'',
//'TLS'=>'',
//'TUV'=>'',
//'UAE'=>'',
//'VIN'=>'',
//'YEM'=>'',
//'ZAM'=>'',
//'ZIM'=>'',

	db_save_version('2012-05-19 10:00:04');
}

if($version<'2012-06-02 14:00:00') {
	$q="ALTER TABLE  `TourRecords` ADD  `TrFlags` SET(  'bar',  'gap' ) NOT NULL";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2012-06-02 14:00:00');
}

if($version<'2012-06-04 08:00:00') {
	$q="CREATE TABLE  `TourTypes` (
		`TtId` int( 10 ) NOT NULL ,
		`TtType` VARCHAR( 20 ) NOT NULL ,
		`TtDistance` int NOT NULL ,
		`TtOrderBy` int NOT NULL ,
		PRIMARY KEY (  `TtId` )
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1050));
	safe_w_sql("insert ignore into TourTypes VALUES
		(1 , 'Type_FITA',                 4,  1),
		(2 , 'Type_2xFITA',               8,  2),
		(4 , 'Type_FITA 72',              4,  3),
		(18, 'Type_FITA+50',              0,  4),
		(3 , 'Type_70m Round',            2,  5),
		(6 , 'Type_Indoor 18',            2,  6),
		(7 , 'Type_Indoor 25',            2,  7),
		(8 , 'Type_Indoor 25+18',         4,  8),
		(14, 'Type_Las Vegas',            4,  9),
		(9 , 'Type_HF 12+12',             1, 10),
		(12, 'Type_HF 12+12',             2, 11),
		(10, 'Type_HF 24+24',             2, 12),
		(17, 'Type_NorField',             0, 13),
		(11, '3D',                        1, 14),
		(13, '3D',                        2, 15),
		(5 , 'Type_900 Round',            3, 16),
		(15, 'Type_GiochiGioventu',       2, 17),
		(16, 'Type_GiochiGioventuW', 2, 18),
		(19, 'Type_GiochiStudentes',   1, 19),
		(20, 'Type_SweForestRound',       0, 20),
		(21, 'Type_Face2Face',            0, 21),
		(22, 'Type_Indoor 18',            1, 22)
		");

	db_save_version('2012-06-04 08:00:00');
}

if($version<'2012-08-12 00:30:00') {
	$q = "
		CREATE VIEW `EventCategories` AS select `EventClass`.`EcCode` AS `EcCode`,`EventClass`.`EcTeamEvent` AS `EcTeamEvent`,
		`EventClass`.`EcTournament` AS `EcTournament`,`EventClass`.`EcClass` AS `EcClass`,`EventClass`.`EcDivision` AS `EcDivision`,
		`EventClass`.`EcNumber` AS `EcNumber`,`Events`.`EvCode` AS `EvCode`,`Events`.`EvTeamEvent` AS `EvTeamEvent`,
		`Events`.`EvTournament` AS `EvTournament`,`Events`.`EvEventName` AS `EvEventName`,`Events`.`EvProgr` AS `EvProgr`,
		`Events`.`EvShootOff` AS `EvShootOff`,`Events`.`EvE1ShootOff` AS `EvE1ShootOff`,`Events`.`EvE2ShootOff` AS `EvE2ShootOff`,
		`Events`.`EvSession` AS `EvSession`,`Events`.`EvPrint` AS `EvPrint`,`Events`.`EvQualPrintHead` AS `EvQualPrintHead`,
		`Events`.`EvQualLastUpdate` AS `EvQualLastUpdate`,`Events`.`EvFinalFirstPhase` AS `EvFinalFirstPhase`,
		`Events`.`EvFinalPrintHead` AS `EvFinalPrintHead`,`Events`.`EvFinalLastUpdate` AS `EvFinalLastUpdate`,
		`Events`.`EvFinalTargetType` AS `EvFinalTargetType`,`Events`.`EvFinalAthTarget` AS `EvFinalAthTarget`,
		`Events`.`EvElim1` AS `EvElim1`,`Events`.`EvElim2` AS `EvElim2`,`Events`.`EvPartialTeam` AS `EvPartialTeam`,
		`Events`.`EvMultiTeam` AS `EvMultiTeam`,`Events`.`EvMixedTeam` AS `EvMixedTeam`,`Events`.`EvMaxTeamPerson` AS `EvMaxTeamPerson`,
		`Events`.`EvRunning` AS `EvRunning`,`Events`.`EvMatchMode` AS `EvMatchMode`,`Events`.`EvMatchArrowsNo` AS `EvMatchArrowsNo`,
		`Events`.`EvElimEnds` AS `EvElimEnds`,`Events`.`EvElimArrows` AS `EvElimArrows`,`Events`.`EvElimSO` AS `EvElimSO`,`Events`.
		`EvFinEnds` AS `EvFinEnds`,`Events`.`EvFinArrows` AS `EvFinArrows`,`Events`.`EvFinSO` AS `EvFinSO`
		from (`EventClass` join `Events` on(((`Events`.`EvCode` = `EventClass`.`EcCode`) and (`Events`.`EvTeamEvent` = `EventClass`.`EcTeamEvent`) and (`Events`.`EvTournament` = `EventClass`.`EcTournament`))));
	";
	$r=safe_w_sql($q,false,array(1050));
	db_save_version('2012-08-12 00:30:00');
}

if($version<'2012-09-08 11:20:00') {
	$q="CREATE TABLE  `GuessWho` (
		`GwPlayerCode` VARCHAR( 20 ) NOT NULL ,
		`GwPlayerPass` VARCHAR( 20 ) BINARY NOT NULL ,
		`GwPlayerName` VARCHAR( 50 ) NOT NULL ,
		`GwPlayerPhone` VARCHAR( 30 ) NOT NULL ,
		`GwTournament` int NOT NULL ,
		PRIMARY KEY (  GwTournament, GwPlayerCode )
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1050));

	$q="CREATE TABLE  `GuessWhoData` (
		`GwAthlete1` int( 10 ) NOT NULL ,
		`GwAthlete2` int( 10 ) NOT NULL ,
		`GwPlayerCode` VARCHAR( 20 ) NOT NULL ,
		`GwEvent` VARCHAR( 5 ) NOT NULL ,
		`GwTeamEvent` int NOT NULL ,
		`GwTournament` int NOT NULL ,
		PRIMARY KEY (  GwTournament, GwEvent, GwTeamEvent, GwPlayerCode )
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1050));

	db_save_version('2012-09-08 11:20:00');
}

if($version<'2012-09-20 18:20:00') {

	$r=safe_w_sql('drop table GuessWhoData',false,array(1050));
	$q="CREATE TABLE IF NOT EXISTS `GuessWhoData` (
		  `GwdAthlete1` int(10) NOT NULL,
		  `GwdAthlete2` int(10) NOT NULL,
		  `GwdPlayerCode` varchar(20) NOT NULL,
		  `GwdEvent` varchar(5) NOT NULL,
		  `GwdTeamEvent` int(11) NOT NULL,
		  `GwdTournament` int(11) NOT NULL,
		  PRIMARY KEY (`GwdTournament`,`GwdEvent`,`GwdTeamEvent`,`GwdPlayerCode`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1050));

	db_save_version('2012-09-20 18:20:00');
}


if($version<'2012-09-23 01:28:00') {

	$q="ALTER TABLE `FinSchedule` ADD  `FSScheduledLen` SMALLINT NOT NULL DEFAULT  '0' AFTER  `FSScheduledTime`";
	$r=safe_w_sql($q,false,array(1060));
	$q="ALTER TABLE `FinTraining` ADD  `FtScheduledLen` SMALLINT NOT NULL DEFAULT  '0' AFTER  `FtScheduledTime`";
	$r=safe_w_sql($q,false,array(1060));
	$q="ALTER TABLE  `Finals` ADD  `FinStatus` TINYINT NOT NULL DEFAULT  '0' AFTER  `FinLive`";
	$r=safe_w_sql($q,false,array(1060));
	$q="ALTER TABLE  `TeamFinals` ADD  `TfStatus` TINYINT NOT NULL DEFAULT  '0' AFTER  `TfLive`";
	$r=safe_w_sql($q,false,array(1060));
	$q="ALTER TABLE  `Session` ADD  `SesStatus` TINYINT NOT NULL DEFAULT  '0' AFTER  `SesFollow`";
	$r=safe_w_sql($q,false,array(1060));
	db_save_version('2012-09-23 01:28:00');
}
