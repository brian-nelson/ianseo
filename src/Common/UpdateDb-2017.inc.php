<?php

if($version<'2017-02-20 12:00:02') {
	$q="replace into Targets set TarId=13, TarDescr='TrgNfaaInd', TarArray='TrgNfaaInd', TarOrder=13,
		A_size=0,
		B_size=40,
		B_color='000080',
		C_size=32,
		C_color='000080',
		D_size=24,
		D_color='000080',
		E_size=16,
		E_color='000080',
		F_size=8,
		F_color='f4f4f4',
		Z_size=4,
		Z_color='f4f4f4'";
	safe_w_sql($q,false,array(1060));

	$q="ALTER TABLE `Qualifications` change QuD3Arrowstring QuD3Arrowstring VARCHAR(90) NOT NULL, change QuD4Arrowstring QuD4Arrowstring VARCHAR(255) NOT NULL ";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2017-02-20 12:00:02');
}

if($version<'2017-04-07 09:09:00') {
	$q="ALTER TABLE `Qualifications` ADD `QuConfirm` int NOT NULL after QuArrow";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="ALTER TABLE `Eliminations` ADD `ElConfirm` int NOT NULL after ElTiebreak";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2017-04-07 09:09:00');
}

if($version<'2017-04-07 09:29:00') {
	$q="ALTER TABLE `IskDevices` ADD `IskDevRunningConf` text NOT NULL after IskDvSetup";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2017-04-07 09:29:00');
}

if($version<'2017-04-22 09:29:00') {
	$q="insert into TourTypes set TtId=34, TtType='Type_NZ_FITA+72', TtDistance=6, TtOrderBy=34 on duplicate key update TtType='Type_NZ_FITA+72', TtDistance=6, TtOrderBy=34";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="insert into TourTypes set TtId=35, TtType='Type_NZ_Clout', TtDistance=1, TtOrderBy=35 on duplicate key update TtType='Type_NZ_Clout', TtDistance=1, TtOrderBy=35";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="insert into TourTypes set TtId=36, TtType='type_VEGAS_1stDakotaBank', TtDistance=3, TtOrderBy=36 on duplicate key update TtType='type_VEGAS_1stDakotaBank', TtDistance=3, TtOrderBy=36";
	$r=safe_w_sql($q,false,array(1146, 1060));

	// needed for subclass event creation
	$q="ALTER TABLE `EventClass` ADD `EcSubClass` VARCHAR(2) NOT NULL AFTER `EcDivision`";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="ALTER TABLE `EventClass` DROP PRIMARY KEY, ADD PRIMARY KEY (`EcCode`, `EcTeamEvent`, `EcTournament`, `EcClass`, `EcDivision`, `EcSubClass`) USING BTREE";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2017-04-22 09:29:00');
}

if($version<'2017-04-22 09:29:02') {
	$q="alter table TourTypes change TtType TtType varchar(35) not null";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="update TourTypes set TtType='type_NFAA_1stDakotaBank' where TtId=36";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2017-04-22 09:29:02');
}

if($version<'2017-05-01 17:00:01') {
	$q="ALTER TABLE `IskDevices` change IskDevRunningConf IskDvRunningConf TEXT NOT NULL, change IdLastSeen IskDvLastSeen DATETIME NOT NULL ";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2017-05-01 17:00:01');
}

if($version<'2017-05-15 10:29:01') {
	$q="DROP TABLE if exists GateLog";
	$r=safe_w_sql($q,false,array(1051));

	$q="CREATE TABLE IF NOT EXISTS `GateLog` (
		`GLEntry` int NOT NULL,
		`GLDateTime` datetime NOT NULL,
		`GLIP` varchar(15) NOT NULL,
		`GLDirection` tinyint NOT NULL,
		`GLTournament` int(11) NOT NULL,
		`GLStatus` TINYINT NOT NULL,
		PRIMARY KEY (`GLEntry`, GLDateTime)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2017-05-15 10:29:01');
}

if($version<'2017-05-17 10:29:01') {
	$q="ALTER TABLE `GateLog` drop primary key, add index (GLEntry), add index (GLDateTime), add index (GLTournament, GLEntry)";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2017-05-17 10:29:01');
}

if($version<'2017-05-29 17:29:01') {
	$q="ALTER TABLE `Finals` change `FinArrowstring` FinArrowstring varchar(60) NOT NULL";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2017-05-29 17:29:01');
}

if($version<'2017-05-30 10:29:01') {
	safe_w_sql("ALTER TABLE `Events` ADD `EvElimType` tinyint NOT NULL after EvFinalAthTarget",false,array(1146, 1060));
	safe_w_sql("ALTER TABLE `Events` ADD `EvE1Ends` tinyint NOT NULL after EvElim1",false,array(1146, 1060));
	safe_w_sql("ALTER TABLE `Events` ADD `EvE1Arrows` tinyint NOT NULL after EvE1Ends",false,array(1146, 1060));
	safe_w_sql("ALTER TABLE `Events` ADD `EvE1SO` tinyint NOT NULL after EvE1Arrows",false,array(1146, 1060));
	safe_w_sql("ALTER TABLE `Events` ADD `EvE2Ends` tinyint NOT NULL after EvElim2",false,array(1146, 1060));
	safe_w_sql("ALTER TABLE `Events` ADD `EvE2Arrows` tinyint NOT NULL after EvE2Ends",false,array(1146, 1060));
	safe_w_sql("ALTER TABLE `Events` ADD `EvE2SO` tinyint NOT NULL after EvE2Arrows",false,array(1146, 1060));

	// now update all the Field/3D competitions to the new format
	$q=safe_r_sql("select * from Tournament where ToElimination!=0");
	while($r=safe_fetch($q)) {
		safe_w_sql("update Events set EvElimType=0 where EvTournament=$r->ToId and EvTeamEvent=0 and EvElim1=0 and EvElim2=0");
		safe_w_sql("update Events set EvElimType=2, EvE1Ends=EvElimEnds, EvE1Arrows=EvElimArrows, EvE1SO=EvElimSO where EvTournament=$r->ToId and EvTeamEvent=0 and EvElim1>0");
		safe_w_sql("update Events set EvElimType=if(EvElim1=0, 1, 2), EvE2Ends=8, EvE2Arrows=3, EvE2SO=1 where EvTournament=$r->ToId and EvTeamEvent=0 and EvElim2>0");
	}

	db_save_version('2017-05-30 10:29:01');
}

if($version<'2017-06-14 17:29:01') {
	$q="ALTER TABLE `IskDevices` ADD `IskDvGps` TEXT NOT NULL AFTER `IskDvUrlDownload`";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2017-06-14 17:29:01');
}

if($version<'2017-07-22 14:29:02') {
	$q="ALTER TABLE `TargetFaces` 
		ADD `TfXNineChars` varchar(16) NOT NULL after TfRegExp,
		ADD `TfGoldsChars` varchar(16) NOT NULL after TfRegExp,
		ADD `TfXNine` varchar(5) NOT NULL after TfRegExp,
		ADD `TfGolds` varchar(5) NOT NULL after TfRegExp
		";
	$r=safe_w_sql($q,false,array(1146, 1060));

	// update 10/9 targets (indoors)
	safe_w_sql("update TargetFaces inner join Tournament on TfTournament=ToId 
		set 
		TfGolds=ToGolds, 
		TfXNine=ToXNine, 
		TfGoldsChars=ToGoldsChars, 
		TfXNineChars=ToXNineChars");

	db_save_version('2017-07-22 14:29:02');
}

if($version<'2017-07-22 14:29:03') {
	$q="ALTER TABLE `Events` 
		ADD `EvXNineChars` varchar(16) NOT NULL after EvFinalTargetType,
		ADD `EvGoldsChars` varchar(16) NOT NULL after EvFinalTargetType,
		ADD `EvXNine` varchar(5) NOT NULL after EvFinalTargetType,
		ADD `EvGolds` varchar(5) NOT NULL after EvFinalTargetType
		";
	$r=safe_w_sql($q,false,array(1146, 1060));

	// update 10/9 targets (indoors)
	safe_w_sql("update Events inner join Tournament on EvTournament=ToId 
		set 
		EvGolds=ToGolds, 
		EvXNine=ToXNine, 
		EvGoldsChars=ToGoldsChars, 
		EvXNineChars=ToXNineChars");

	db_save_version('2017-07-22 14:29:03');
}

if($version<'2017-08-10 09:29:02') {
	$q="insert into LookUpPaths set LupIocCode='BALT', 
		LupPath='https://baltic.service.ianseo.net/IanseoData.php', 
		LupPhotoPath='https://baltic.service.ianseo.net/GetPhoto.php', 
		LupFlagsPath='https://baltic.service.ianseo.net/GetFlags.php' 
		on duplicate key update 
		LupPath='https://baltic.service.ianseo.net/IanseoData.php', 
		LupPhotoPath='https://baltic.service.ianseo.net/GetPhoto.php', 
		LupFlagsPath='https://baltic.service.ianseo.net/GetFlags.php'";
	$r=safe_w_sql($q,false,array(1146, 1060));

	$q="insert into LookUpPaths set LupIocCode='NOR', 
		LupPath='https://nor.service.ianseo.net/IanseoData.php', 
		LupPhotoPath='https://nor.service.ianseo.net/GetPhoto.php', 
		LupFlagsPath='https://nor.service.ianseo.net/GetFlags.php' 
		on duplicate key update 
		LupPath='https://nor.service.ianseo.net/IanseoData.php', 
		LupPhotoPath='https://nor.service.ianseo.net/GetPhoto.php', 
		LupFlagsPath='https://nor.service.ianseo.net/GetFlags.php'";
	$r=safe_w_sql($q,false,array(1146, 1060));

	$q="insert into LookUpPaths set LupIocCode='NOR_s', 
		LupPath='https://nor.service.ianseo.net/IanseoData.php', 
		LupPhotoPath='https://nor.service.ianseo.net/GetPhoto.php', 
		LupFlagsPath='https://nor.service.ianseo.net/GetFlags.php' 
		on duplicate key update 
		LupPath='https://nor.service.ianseo.net/IanseoData.php', 
		LupPhotoPath='https://nor.service.ianseo.net/GetPhoto.php', 
		LupFlagsPath='https://nor.service.ianseo.net/GetFlags.php'";
	$r=safe_w_sql($q,false,array(1146, 1060));

	$q="insert into LookUpPaths set LupIocCode='CAN', 
		LupPath='https://can.service.ianseo.net/IanseoData.php', 
		LupPhotoPath='https://can.service.ianseo.net/GetPhoto.php', 
		LupFlagsPath='https://can.service.ianseo.net/GetFlags.php' 
		on duplicate key update 
		LupPath='https://can.service.ianseo.net/IanseoData.php', 
		LupPhotoPath='https://can.service.ianseo.net/GetPhoto.php', 
		LupFlagsPath='https://can.service.ianseo.net/GetFlags.php'";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2017-08-10 09:29:02');
}

if($version<'2017-10-04 13:29:01') {
	$q="insert into LookUpPaths set LupIocCode='NOR_s', 
		LupPath='https://nor.service.ianseo.net/IanseoData.php?ScoreClass=1', 
		LupPhotoPath='https://nor.service.ianseo.net/GetPhoto.php', 
		LupFlagsPath='https://nor.service.ianseo.net/GetFlags.php' 
		on duplicate key update 
		LupPath='https://nor.service.ianseo.net/IanseoData.php?ScoreClass=1', 
		LupPhotoPath='https://nor.service.ianseo.net/GetPhoto.php', 
		LupFlagsPath='https://nor.service.ianseo.net/GetFlags.php'";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2017-10-04 13:29:01');
}

if($version<'2017-11-19 08:39:00') {
    $q = "REPLACE INTO `TourTypes` (`TtId`, `TtType`, `TtDistance`, `TtOrderBy`) VALUES 
      (37, 'Type_2x70mRound', '4', '36'), 
      (38, 'Type_ProAMIndoor', '3', '37')";
    $r = safe_w_sql($q, false, array(1146, 1060));

    $q = "REPLACE INTO `Targets` 
      (`TarId`, `TarDescr`, `TarArray`, `TarStars`, `TarOrder`, `A_size`, `A_color`, `B_size`, `B_color`, `C_size`, `C_color`, `D_size`, `D_color`, `E_size`, `E_color`, `F_size`, `F_color`, `G_size`, `G_color`, `H_size`, `H_color`, `I_size`, `I_color`, `J_size`, `J_color`, `K_size`, `K_color`, `L_size`, `L_color`, `M_size`, `M_color`, `N_size`, `N_color`, `O_size`, `O_color`, `P_size`, `P_color`, `Q_size`, `Q_color`, `R_size`, `R_color`, `S_size`, `S_color`, `T_size`, `T_color`, `U_size`, `U_color`, `V_size`, `V_color`, `W_size`, `W_color`, `X_size`, `X_color`, `Y_size`, `Y_color`, `Z_size`, `Z_color`, `TarDummyLine`) 
      VALUES 
      (14, 'TrgProAMIndNfaa', 'TrgProAMIndNfaa', 'a-f', '14', '0', '', '40', '000080', '32', '000080', '24', '000080', '16', '000080', '8', 'f4f4f4', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '4', 'f4f4f4', '0', '', '0'),
      (15, 'TrgProAMIndVegas', 'TrgProAMIndVegas', 'a-l', '15', '0', '', '100', 'FFFFFF', '90', 'FFFFFF', '80', '000000', '70', '000000', '60', '00A3D1', '50', '00A3D1', '40', 'ED2939', '30', 'ED2939', '20', 'F9E11E', '0', '', '10', 'F9E11E', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '5', 'F9E11E', '0', '', '0', '', '0'),
      (16, 'TrgProAMIndVegasSmall', 'TrgProAMIndVegasSmall', 'ag-l', '16', '0', '', '0', 'FFFFFF', '', 'FFFFFF', '0', 'FFFFFF', '0', 'FFFFFF', '0', 'FFFFFF', '50', '00A3D1', '40', 'ED2939', '30', 'ED2939', '20', 'F9E11E', '0', '', '10', 'F9E11E', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '5', 'F9E11E', '0', '', '0', '', '0')";
    $r = safe_w_sql($q, false, array(1146, 1060));

    db_save_version('2017-11-19 08:39:00');
}

if($version<'2017-12-05 13:29:01') {
	$q="ALTER TABLE `Countries` change CoCode CoCode VARCHAR(10) not null, change CoSubCountry CoSubCountry VARCHAR(10) not null ";
	$r=safe_w_sql($q,false,array(1146, 1060));

	$q="ALTER TABLE `Flags` change FlCode FlCode VARCHAR(10) not null ";
	$r=safe_w_sql($q,false,array(1146, 1060));

	$q="ALTER TABLE `Tournament` change ToCommitee ToCommitee VARCHAR(10) not null ";
	$r=safe_w_sql($q,false,array(1146, 1060));

	$q="ALTER TABLE `LookUpEntries` change LueCountry LueCountry VARCHAR(10) not null, change LueCountry2 LueCountry2 VARCHAR(10) not null, change LueCountry3 LueCountry3 VARCHAR(10) not null";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2017-12-05 13:29:01');
}

if($version<'2017-12-14 08:11:00') {
    safe_w_sql("ALTER TABLE `Phases` ADD `PhIndTeam` TINYINT NOT NULL AFTER `PhLevel`",false,array(1060));
    safe_w_sql("INSERT INTO `Phases` (`PhId`, `PhDescr`, `PhLevel`, `PhIndTeam`) VALUES ('12', '12Final', '16', '2')",false,array(1062));
    safe_w_sql("UPDATE `Phases` SET PhIndTeam=3 WHERE PhId<=16 AND `PhIndTeam`=0",false,array(1062));
    safe_w_sql("UPDATE `Phases` SET PhIndTeam=1 WHERE PhId>16 AND `PhIndTeam`=0",false,array(1062));
    safe_w_sql("ALTER TABLE `Phases` ORDER BY `PhId`", false,array());

    safe_w_sql("Update `Grids` SET GrPosition2=0 WHERE `GrPhase`=16 AND `GrPosition2`>24", false, array());
    db_save_version('2017-12-14 08:11:00');
}
