<?php

if($version<'2011-01-04 10:29:00') {
	$MySql="
		ALTER TABLE `Entries` ADD `EnSubTeam` TINYINT NOT NULL DEFAULT '0' AFTER `EnCountry` ,
		ADD `EnCountry2` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `EnSubTeam` ;
	";
	safe_w_SQL($MySql,false,array(1060));
	db_save_version('2011-01-04 10:29:00');
}

if($version<'2011-01-17 00:32:00') {
	$MySql="ALTER TABLE `Entries` ADD `EnIocCode` varchar(5) NOT NULL DEFAULT '' AFTER `EnCountry`";
	safe_w_SQL($MySql, false, array(1060));
	$MySql="ALTER TABLE `LookUpEntries` ADD `LueIocCode` varchar(5) NOT NULL DEFAULT '' AFTER `LueCode`";
	safe_w_SQL($MySql, false, array(1060));
	$MySql="ALTER TABLE `Tournament` ADD `ToIocCode` varchar(5) NOT NULL DEFAULT '' AFTER `ToCode`";
	safe_w_SQL($MySql, false, array(1060));

	safe_w_sql("drop table if exists LookUpPaths");

	$MySql="CREATE TABLE IF NOT EXISTS `LookUpPaths` (
		`LupIocCode` VARCHAR( 5 ) NOT NULL ,
		`LupFors` VARCHAR(1) NOT NULL ,
		`LupPath` VARCHAR(255) NOT NULL ,
		`LupPhotoPath` VARCHAR(255) NOT NULL ,
		`LupFlagsPath` VARCHAR(255) NOT NULL ,
		`LupLastUpdate` datetime NULL ,
		PRIMARY KEY ( `LupIocCode` )
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT = 'Defines the LookUp paths for each IOC Country'";
	safe_w_SQL($MySql);

	safe_w_sql("insert into LookUpPaths set"
		. " LupIocCode='ITA',"
		. " LupPath='http://www.fitarco-italia.org/gare/ianseo/IanseoJson.php', "
		. " LupPhotoPath='http://www.fitarco-italia.org/gare/ianseo/IanseoPhoto.php', "
		. " LupFlagsPath='http://www.fitarco-italia.org/gare/ianseo/IanseoFlags.php'"
		);

	safe_w_sql("insert into LookUpPaths set"
		. " LupIocCode='FITA',"
		. " LupFors='1',"
		. " LupPath='http://".$_SERVER['HTTP_HOST'].$CFG->ROOT_DIR."Modules/IanseoTeam/LookupFitaId.php', "
		. " LupPhotoPath='http://".$_SERVER['HTTP_HOST'].$CFG->ROOT_DIR."Modules/IanseoTeam/LookupFitaPhoto.php'"
		);

	safe_w_sql("ALTER TABLE `Tournament` DROP `ToUpNamesUrl`, DROP `ToUpPhotosUrl`, DROP `ToUpFlagsUrl` ",false,array(1060, 1091));

	safe_w_sql("ALTER TABLE `LookUpEntries` ADD `LueCountry2` VARCHAR( 5 ) NOT NULL AFTER `LueCoShort` , ADD `LueCoDescr2` VARCHAR( 80 ) NOT NULL AFTER `LueCountry2` , ADD `LueCoShort2` VARCHAR( 30 ) NOT NULL AFTER `LueCoDescr2`",false,array(1060));
	safe_w_sql("ALTER TABLE `LookUpEntries`  DROP `LueAthlete`,  DROP `LueJudge`,  DROP `LueDoS` ", false, array(1060, 1091));

	safe_w_sql("update Tournament set ToIocCode='ITA' where ToLocRule='IT'");
	safe_w_sql("update LookUpEntries set LueIocCode='ITA' where LueIocCode=''");
	safe_w_sql("update Entries inner join Tournament on EnTournament=ToId set EnIocCode='ITA' where ToIocCode='ITA'");
	db_save_version('2011-01-17 00:32:00');
}

if($version<'2011-01-17 18:39:00') {
	$MySql="ALTER TABLE `Flags` ADD `FlIocCode` varchar(5) NOT NULL AFTER `FlTournament` ";
	safe_w_SQL($MySql,false,array(1060));
	$MySql="ALTER TABLE `Countries` ADD `CoIocCode` varchar(5) NOT NULL AFTER `CoTournament` ";
	safe_w_SQL($MySql,false,array(1060));
	$MySql="ALTER TABLE `Flags` drop primary key, add primary key (FlTournament, FlIocCode, FlCode)";
	safe_w_SQL($MySql,false,array(1060,1091));
	safe_w_sql("update Flags set FlIocCode='FITA' where FlTournament=-1");
	db_save_version('2011-01-17 18:39:00');
}

/*

MAJOR REWRITING OF ALL THE IMAGES IN IANSEO!!!!!

CENTRALIZED IN A SINGLE TABLE ALL THE IMG FILES IN Images Table!!!


*/
if($version<'2011-01-18 10:39:00') {
	safe_w_SQL("DROP TABLE IF EXISTS `Images`");

	safe_w_sql("CREATE TABLE IF NOT EXISTS `Images` (
		  `ImTournament` int(11) NOT NULL,
		  `ImIocCode` varchar(5) NOT NULL COMMENT 'If IocCode is empty Ref is on ID and not Code',
		  `ImSection` varchar(5) NOT NULL COMMENT 'Section of Ianseo in which it is used',
		  `ImReference` varchar(11) NOT NULL COMMENT 'Depending on section, refers to EnCode, position, coCode etc',
		  `ImType` varchar(3) NOT NULL COMMENT 'PNG, SVG, JPG, etc',
		  `ImContent` mediumblob NOT NULL,
		  `ImgLastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  `ImChecked` varchar(1) NOT NULL,
		  PRIMARY KEY (`ImTournament`,`ImIocCode`,`ImSection`,`ImReference`,`ImType`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

	// Updates the Flags Service for IOC Flags
		safe_w_sql("update LookUpPaths set"
			. " LupFlagsPath='http://www.ianseo.net/Ianseo/GetFlags.php' "
			. "WHERE"
			. " LupIocCode='FITA'"
			);

	db_save_version('2011-01-18 10:39:00');
}

if($version<'2011-01-25 11:20:00') {
	$MySql="ALTER TABLE `Tournament` ADD `ToCollation` varchar(15) NOT NULL";
	safe_w_SQL($MySql,false,array(1060));
	db_save_version('2011-01-25 11:20:00');
}

if($version<'2011-01-28 15:56:00') {
	safe_w_SQL("DROP TABLE IF EXISTS `BoinxSchedule`");

	safe_w_sql("CREATE TABLE IF NOT EXISTS `BoinxSchedule` (
		  `BsTournament` int(11) NOT NULL,
		  `BsType` varchar(25) NOT NULL ,
		  `BsExtra` varchar(25) NOT NULL ,
		  PRIMARY KEY (`BsTournament`,`BsType`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

	db_save_version('2011-01-28 15:56:00');
}

if($version<'2011-01-30 12:56:00') {
	$MySql="ALTER TABLE `Flags` change `FlPNG` `FlJPG` MEDIUMBLOB NOT NULL";
	safe_w_SQL($MySql,false,array(1060));
	$q=safe_r_sql("select * from Flags where FlJPG");
	while($r=safe_fetch($q)) {
		$tmpnam=tempnam('/tmp', 'img');
		$img=imagecreatefromstring(base64_decode($r->FlJPG));
		imagejpeg($img, $tmpnam, 95);
		safe_w_sql("update Flags set FlJPG='".base64_encode(file_get_contents($tmpnam))."' where FlTournament=$r->FlTournament and FlIocCode=$r->FlIocCode and FlCode=$r->FlCode");
	}
	db_save_version('2011-01-30 12:56:00');
}

if($version<'2011-02-07 17:17:00') {
	$MySql="ALTER TABLE `Teams` ADD `TeFinalRank` SMALLINT( 6 ) NOT NULL AFTER `TeRank` ,
		ADD `TeSO` SMALLINT( 6 ) NOT NULL DEFAULT '0' AFTER `TeFinalRank` ;";
	safe_w_SQL($MySql,false,array(1060));

	db_save_version('2011-02-07 17:17:00');
}

if($version<'2011-02-09 21:02:00') {
	$MySql="update Qualifications set QuTargetNo = concat(substr(QuTargetNo,1,1), lpad(substr(QuTargetNo,2,length(QuTargetNo)-2), 3, '0'), substr(QuTargetNo,-1)) where length(QuTargetNo)<5";
	safe_w_SQL($MySql,false,array(1060));
	$MySql="update AvailableTarget set AtTargetNo = concat(substr(AtTargetNo,1,1), lpad(substr(AtTargetNo,2,length(AtTargetNo)-2), 3, '0'), substr(AtTargetNo,-1)) where length(AtTargetNo)<5";
	safe_w_SQL($MySql,false,array(1060));

	db_save_version('2011-02-09 21:02:00');
}

if($version<'2011-02-09 22:41:00') {
	setparameter('IsCode', '');

	db_save_version('2011-02-09 22:41:00');
}

if($version<'2011-02-10 11:07:00') {
	$MySql="ALTER TABLE `Teams` CHANGE `TeFinalRank` `TeRankFinal` SMALLINT( 6 ) NOT NULL;";
	safe_w_SQL($MySql,false,array(1060));

	$MySql="ALTER TABLE `Teams` ADD `TeTimeStampFinal` DATETIME NULL DEFAULT NULL AFTER `TeTimeStamp`";
	safe_w_SQL($MySql,false,array(1060));

	db_save_version('2011-02-10 11:07:00');
}

if($version<'2011-02-16 15:42:00') {
	$MySql="ALTER TABLE `Events` ADD `EvMaxTeamPerson` TINYINT NOT NULL DEFAULT '1' AFTER `EvMixedTeam` ";
	safe_w_SQL($MySql,false,array(1060));

// metto a posto il campo per le gare presenti e le rank
	$q="SELECT ToId FROM Tournament ";
	$r=safe_r_sql($q);

	while ($row=safe_fetch($r))
	{
		calcMaxTeamPerson_20110216($row->ToId);
		recalculateTeamRanking_20110216($row->ToId);
	}

	db_save_version('2011-02-16 15:42:00');
}

if($version<'2011-03-09 14:38:00') {
	$MySql="ALTER TABLE `TournamentType` ADD `TtGoldsChars` VARCHAR( 16 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `TtXNine` ,
		ADD `TtXNineChars` VARCHAR( 16 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `TtGoldsChars` ;
	";
	safe_w_SQL($MySql,false,array(1060));

	$MySql="ALTER TABLE `Tournament` ADD `ToGoldsChars` VARCHAR( 16 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `ToXNine` ,
		ADD `ToXNineChars` VARCHAR( 16 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `ToGoldsChars` ;
	";
	safe_w_SQL($MySql,false,array(1060));

	$maps=getMapsGoldsXNineChars_20110309();

// aggiorno TournamentType
	$q="
		SELECT
			TtId,TtGolds,TtXNine
		FROM
			TournamentType
	";
	//print $q;exit;
	$r=safe_r_sql($q);

	if (safe_num_rows($r)>0)
	{
		while ($row=safe_fetch($r))
		{
			$gold=$maps['G'][$row->TtGolds];
			$xnine=$maps['X'][$row->TtXNine];
			//print $gold . ' - ' . $xnine . '<br>';
			$sql="UPDATE TournamentType SET TtGoldsChars='{$gold}',TtXNineChars='{$xnine}' WHERE TtId={$row->TtId} ";
			//print $sql.'<br><br>';
			$rr=safe_w_sql($sql);
		}
	}
	//exit;
// aggiorno tutti i tornei
	$q="SELECT ToId FROM Tournament ";
	$r=safe_r_sql($q);

	if (safe_num_rows($r)>0)
	{
		while ($row=safe_fetch($r))
		{
			initTourGoldsXNineChars_20110309($row->ToId);
		}
	}

	db_save_version('2011-03-09 14:38:00');
}

if($version<'2011-03-11 18:25:00') {
	$MySql="ALTER TABLE Finals ADD INDEX FinAthleteEventTournament (FinAthlete, FinEvent, FinTournament) ";
	safe_w_SQL($MySql,false,array(1061));

	$MySql="ALTER TABLE Eliminations ADD INDEX ElAthleteEventTournament (ElId, ElEventCode, ElTournament) ";
	safe_w_SQL($MySql,false,array(1061));

	db_save_version('2011-03-11 18:25:00');
}

if($version<'2011-04-03 18:47:00') {
	$MySql="ALTER TABLE Classes ADD ClDivisionsAllowed varchar(255) not null";
	safe_w_SQL($MySql,false,array(1060,1061));

	db_save_version('2011-04-03 18:47:00');
}


if($version<'2011-04-15 15:55:00') {
// i tornei nel db
	$q="SELECT ToId FROM Tournament ";

	$r=safe_r_sql($q);
	if (safe_num_rows($r)>0)
	{
		while ($row=safe_fetch($r))
		{
			RecalcFinRank_20110415($row->ToId);
		}
	}

	db_save_version('2011-04-15 15:55:00');
}

if($version<'2011-04-18 18:35:00') {
	$q="ALTER TABLE `Classes` DROP PRIMARY KEY , ADD PRIMARY KEY ( `ClId` , `ClTournament` , `ClDivisionsAllowed` )";
	$r=safe_w_sql($q,false,array(1061,1091));

	db_save_version('2011-04-18 18:35:00');
}

if($version<'2011-04-20 18:24:00') {
	// Adds the subrule definition
	$q="ALTER TABLE `Tournament` ADD `ToTypeSubRule` VARCHAR( 25 ) NOT NULL AFTER `ToTypeName` ";
	$r=safe_w_sql($q,false,array(1061,1091));

	db_save_version('2011-04-20 18:24:00');
}

if($version<'2011-04-24 19:25:00') {
	// Adds the subrule definition
	$q="ALTER TABLE `Targets` ADD `N_size` INT(3) NOT NULL AFTER `M_color` ,
ADD `N_color` VARCHAR( 6 ) NOT NULL AFTER `N_size` ,
ADD `O_size` INT(3) NOT NULL AFTER `N_color` ,
ADD `O_color` VARCHAR( 6 ) NOT NULL AFTER `O_size` ,
ADD `P_size` INT(3) NOT NULL AFTER `O_color` ,
ADD `P_color` VARCHAR( 6 ) NOT NULL AFTER `P_size` ,
ADD `Q_size` INT(3) NOT NULL AFTER `P_color` ,
ADD `Q_color` VARCHAR( 6 ) NOT NULL AFTER `Q_size` ,
ADD `R_size` INT(3) NOT NULL AFTER `Q_color` ,
ADD `R_color` VARCHAR( 6 ) NOT NULL AFTER `R_size` ,
ADD `S_size` INT(3) NOT NULL AFTER `R_color` ,
ADD `S_color` VARCHAR( 6 ) NOT NULL AFTER `S_size` ,
ADD `T_size` INT(3) NOT NULL AFTER `S_color` ,
ADD `T_color` VARCHAR( 6 ) NOT NULL AFTER `T_size` ,
ADD `U_size` INT(3) NOT NULL AFTER `T_color` ,
ADD `U_color` VARCHAR( 6 ) NOT NULL AFTER `U_size` ,
ADD `V_size` INT(3) NOT NULL AFTER `U_color` ,
ADD `V_color` VARCHAR( 6 ) NOT NULL AFTER `V_size` ,
ADD `W_size` INT(3) NOT NULL AFTER `V_color` ,
ADD `W_color` VARCHAR( 6 ) NOT NULL AFTER `W_size` ,
ADD `X_size` INT(3) NOT NULL AFTER `W_color` ,
ADD `X_color` VARCHAR( 6 ) NOT NULL AFTER `X_size` ,
ADD `Y_size` INT(3) NOT NULL AFTER `X_color` ,
ADD `Y_color` VARCHAR( 6 ) NOT NULL AFTER `Y_size` ,
ADD `Z_size` INT(3) NOT NULL AFTER `Y_color` ,
ADD `Z_color` VARCHAR( 6 ) NOT NULL AFTER `Z_size`
";
	$r=safe_w_sql($q,false,array(1061,1060,1091));

	db_save_version('2011-04-24 19:25:00');
}

if($version<'2011-04-25 09:00:00') {
	$q="DROP TABLE Broadcast";
	$r=safe_w_sql($q,false,array(1051));

	$q="DROP TABLE TournamentType";
	$r=safe_w_sql($q,false,array(1051));

	$q="DELETE FROM TournamentDistances WHERE TdTournament='0'";
	$r=safe_w_sql($q,false,array(1061));

	$q="DELETE FROM Parameters WHERE ParId='LuePath'";
	$r=safe_w_sql($q,false,array(1061));


	db_save_version('2011-04-25 09:00:00');
}

if($version<'2011-04-27 08:24:00') {
	// Adds the subrule definition
	$q="replace into LookUpPaths SET LupPath = 'http://www.bueskyting.no/ianseo/NOR-Athletes.gz', LupIocCode = 'NOR'";
	$r=safe_w_sql($q,false,array(1061,1091));

	db_save_version('2011-04-27 08:24:00');
}

if($version<'2011-04-27 22:55:00') {
	// realign targets
	$r=safe_w_sql("truncate Targets");

	$q="INSERT INTO `Targets` (`TarId`, `TarDescr`, `TarArray`, `TarOrder`, `A_size`, `A_color`, `B_size`, `B_color`, `C_size`, `C_color`, `D_size`, `D_color`, `E_size`, `E_color`, `F_size`, `F_color`, `G_size`, `G_color`, `H_size`, `H_color`, `I_size`, `I_color`, `J_size`, `J_color`, `K_size`, `K_color`, `L_size`, `L_color`, `M_size`, `M_color`, `N_size`, `N_color`, `O_size`, `O_color`, `P_size`, `P_color`, `Q_size`, `Q_color`, `R_size`, `R_color`, `S_size`, `S_color`, `T_size`, `T_color`, `U_size`, `U_color`, `V_size`, `V_color`, `W_size`, `W_color`, `X_size`, `X_color`, `Y_size`, `Y_color`, `Z_size`, `Z_color`, `TarDummyLine`) VALUES
		(1, 'TrgIndComplete', 'TrgIndComplete', 4, 0, '', 100, 'FFFFFF', 90, 'FFFFFF', 80, '000000', 70, '000000', 60, '00A3D1', 50, '00A3D1', 40, 'ED2939', 30, 'ED2939', 20, 'F9E11E', 0, 'F9E11E', 10, 'F9E11E', 0, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0),
		(2, 'TrgIndSmall', 'TrgIndSmall', 5, 0, '', 0, 'FFFFFF', 0, 'FFFFFF', 0, '000000', 0, '000000', 0, '00A3D1', 50, '00A3D1', 40, 'ED2939', 30, 'ED2939', 20, 'F9E11E', 0, 'F9E11E', 10, 'F9E11E', 0, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0),
		(4, 'TrgCOIndSmall', 'TrgCOIndSmall', 7, 0, '', 0, 'FFFFFF', 0, 'FFFFFF', 0, '000000', 0, '000000', 0, '00A3D1', 50, '00A3D1', 40, 'ED2939', 30, 'ED2939', 20, 'F9E11E', 0, 'F9E11E', 5, 'F9E11E', 0, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 10),
		(3, 'TrgCOIndComplete', 'TrgCOIndComplete', 6, 0, '', 100, 'FFFFFF', 90, 'FFFFFF', 80, '000000', 70, '000000', 60, '00A3D1', 50, '00A3D1', 40, 'ED2939', 30, 'ED2939', 20, 'F9E11E', 0, 'F9E11E', 5, 'F9E11E', 0, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 10),
		(5, 'TrgOutdoor', 'TrgOutdoor', 1, 0, '', 100, 'FFFFFF', 90, 'FFFFFF', 80, '000000', 70, '000000', 60, '00A3D1', 50, '00A3D1', 40, 'ED2939', 30, 'ED2939', 20, 'F9E11E', 5, 'F9E11E', 10, 'F9E11E', 0, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0),
		(6, 'TrgField', 'TrgField', 8, 0, '', 50, '000000', 40, '000000', 30, '000000', 20, '000000', 10, 'F9E11E', 5, 'F9E11E', 0, 'ED2939', 0, 'ED2939', 0, 'F9E11E', 0, 'F9E11E', 0, 'F9E11E', 0, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0),
		(7, 'TrgHMOutComplete', 'TrgHMOutComplete', 10, 16, 'ED2939', 4, 'F9E11E', 0, 'FFFFFF', 0, '000000', 0, '000000', 0, '00A3D1', 0, '00A3D1', 0, 'ED2939', 0, 'ED2939', 0, 'F9E11E', 0, 'F9E11E', 0, 'F9E11E', 0, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0),
		(8, 'Trg3DComplete', 'Trg3DComplete', 9, 0, '', 0, 'FFFFFF', 0, 'FFFFFF', 0, '000000', 0, '000000', 60, '00A3D1', 0, '00A3D1', 0, 'ED2939', 30, '00A3D1', 0, 'F9E11E', 0, 'F9E11E', 15, 'ED2939', 5, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0),
		(9, 'TrgCOOutdoor', 'TrgCOOutdoor', 2, 0, '', 0, '', 0, '', 0, '', 0, '', 60, '00A3D1', 50, '00A3D1', 40, 'ED2939', 30, 'ED2939', 20, 'F9E11E', 5, 'F9E11E', 10, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0),
		(10, 'TrgCOOutdoorSmall', 'TrgCOOutdoorSmall', 3, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 50, '00A3D1', 40, 'ED2939', 30, 'ED2939', 20, 'F9E11E', 5, 'F9E11E', 10, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0),
		(11, 'TrgHunterNor', 'TrgHunterNor', 11, 0, '', 0, '', 30, '00A3D1', 0, '', 0, '', 25, '00A3D1', 0, '', 20, 'ED2939', 0, '', 0, '', 0, '', 15, 'ED2939', 0, '', 10, 'F9E11E', 0, '', 0, '', 5, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0)";
	$r=safe_w_sql($q,false,array(1061,1091));

	db_save_version('2011-04-27 22:55:00');
}

if($version<'2011-04-28 20:55:00') {
	$q=safe_r_sql("select * from Classes group by ClId, ClTournament having count(*)>1 ");
	while(safe_num_rows($q)) {
		while($r=safe_fetch($q)) {
			if($r->ClId=='D' and $r->ClDivisionsAllowed=='T') {
				safe_w_sql("update Classes set ClId='Dt', ClValidClass='Dt' where ClDivisionsAllowed='$r->ClDivisionsAllowed' and ClId='$r->ClId' and ClTournament=$r->ClTournament");
			} elseif($r->ClId=='H' and $r->ClDivisionsAllowed=='T') {
				safe_w_sql("update Classes set ClId='Ht', ClValidClass='Ht' where ClDivisionsAllowed='$r->ClDivisionsAllowed' and ClId='$r->ClId' and ClTournament=$r->ClTournament");
			} elseif($r->ClId=='D' and $r->ClDivisionsAllowed=='B') {
				safe_w_sql("update Classes set ClId='Db', ClValidClass='Db' where ClDivisionsAllowed='$r->ClDivisionsAllowed' and ClId='$r->ClId' and ClTournament=$r->ClTournament");
			} elseif($r->ClId=='H' and $r->ClDivisionsAllowed=='B') {
				safe_w_sql("update Classes set ClId='Hb', ClValidClass='Hb' where ClDivisionsAllowed='$r->ClDivisionsAllowed' and ClId='$r->ClId' and ClTournament=$r->ClTournament");
			} elseif($r->ClId=='D' and $r->ClDivisionsAllowed=='B,T') {
				safe_w_sql("update Classes set ClId='Di', ClValidClass='Di' where ClDivisionsAllowed='$r->ClDivisionsAllowed' and ClId='$r->ClId' and ClTournament=$r->ClTournament");
			} elseif($r->ClId=='H' and $r->ClDivisionsAllowed=='B,T') {
				safe_w_sql("update Classes set ClId='Hi', ClValidClass='Hi' where ClDivisionsAllowed='$r->ClDivisionsAllowed' and ClId='$r->ClId' and ClTournament=$r->ClTournament");
			} elseif($r->ClId=='4' and $r->ClDivisionsAllowed=='B') {
				safe_w_sql("update Classes set ClId='4b', ClValidClass='1,4b' where ClDivisionsAllowed='$r->ClDivisionsAllowed' and ClId='$r->ClId' and ClTournament=$r->ClTournament");
			} elseif($r->ClId=='1' and $r->ClDivisionsAllowed=='BU') {
				safe_w_sql("update Classes set ClId='1u', ClValidClass='1u' where ClDivisionsAllowed='$r->ClDivisionsAllowed' and ClId='$r->ClId' and ClTournament=$r->ClTournament");
			} else {
				$OldId=$r->ClId;
				$r->ClId++;
				$tmp=explode(',', $r->ClValidClass);
				foreach($tmp as $k=>$v) if($v==$OldId) $tmp[$k]=$r->ClId;
				safe_w_sql("update Classes set ClId='$r->ClId', ClValidClass='".implode(',', $tmp)."' where ClDivisionsAllowed='$r->ClDivisionsAllowed' and ClId='$OldId' and ClTournament=$r->ClTournament");
			}


		}
		$q=safe_r_sql("select * from Classes group by ClId, ClTournament having count(*)>1 ");
	}

	safe_w_sql("update Classes set ClValidClass='R,Dt,Ht' where ClId='R' and ClDivisionsAllowed='T' and ClValidClass='R,D,H'");
	safe_w_sql("update Classes set ClValidClass='K,Db,Hb' where ClId='K' and ClDivisionsAllowed='B' and ClValidClass='K,D,H'");
	safe_w_sql("update Classes set ClValidClass='R,K,Di,Hi' where ClId='R' and ClDivisionsAllowed='B,T' and ClValidClass='R,K,D,H'");
	safe_w_sql("update Classes set ClValidClass='K,Di,Hi' where ClId='K' and ClDivisionsAllowed='B,T' and ClValidClass='K,D,H'");

	safe_w_sql("ALTER TABLE Classes DROP PRIMARY KEY , ADD PRIMARY KEY ( `ClId` , `ClTournament` )");

	db_save_version('2011-04-28 20:55:00');
}

if($version<'2011-05-03 10:55:00') {

	$q="ALTER TABLE `TargetFaces` ADD `TfRegExp` VARCHAR( 255 ) NOT NULL AFTER `TfClasses` ";
	$r=safe_w_sql($q,false,array(1061,1091));

	db_save_version('2011-05-03 10:55:00');
}

if($version<'2011-05-03 15:55:00') {

	$q="ALTER TABLE `TargetFaces` CHANGE `TfName` `TfName` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ";
	$r=safe_w_sql($q,false,array(1061,1091));

	db_save_version('2011-05-03 15:55:00');
}

if($version<'2011-06-20 17:00:00') {

	$q="UPDATE Phases SET PhLevel='64' WHERE PhId=48";
	$r=safe_w_sql($q,false,array());
	$q="REPLACE INTO Phases (PhId,PhDescr,PhLevel) VALUES ('64','64Final','-1')";
	$r=safe_w_sql($q,false,array());

	$q="ALTER TABLE Grids ADD GrPosition2 TINYINT NOT NULL DEFAULT '0' AFTER GrPosition";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2011-06-20 17:00:00');
}

if($version<'2011-06-30 9:56:00') {
	$q="TRUNCATE TABLE Grids";
	$r=safe_w_sql($q,false,array());

// se no i 128 non ci stanno
	$q="ALTER TABLE `Grids` CHANGE `GrPosition` `GrPosition` SMALLINT NOT NULL DEFAULT '0',CHANGE `GrPosition2` `GrPosition2` SMALLINT NOT NULL DEFAULT '0'";
	$r=safe_w_sql($q,false,array());

	$q="INSERT INTO `Grids` (`GrMatchNo`, `GrPosition`, `GrPosition2`, `GrPhase`) VALUES
		(0, 1, 1, 0), (1, 2, 2, 0), (2, 4, 4, 1), (3, 3, 3, 1), (4, 1, 1, 2), (5, 4, 4, 2), (6, 3, 3, 2), (7, 2, 2, 2),
		(8, 1, 1, 4), (9, 8, 8, 4), (10, 5, 5, 4), (11, 4, 4, 4), (12, 3, 3, 4), (13, 6, 6, 4), (14, 7, 7, 4), (15, 2, 2, 4),
		(16, 1, 1, 8), (17, 16, 16, 8), (18, 9, 9, 8), (19, 8, 8, 8), (20, 5, 5, 8), (21, 12, 12, 8), (22, 13, 13, 8), (23, 4, 4, 8),
		(24, 3, 3, 8), (25, 14, 14, 8), (26, 11, 11, 8), (27, 6, 6, 8), (28, 7, 7, 8), (29, 10, 10, 8), (30, 15, 15, 8), (31, 2, 2, 8),
		(32, 1, 1, 16), (33, 32, 32, 16), (34, 17, 17, 16), (35, 16, 16, 16), (36, 9, 9, 16), (37, 24, 24, 16), (38, 25, 25, 16), (39, 8, 8, 16),
		(40, 5, 5, 16), (41, 28, 28, 16), (42, 21, 21, 16), (43, 12, 12, 16), (44, 13, 13, 16), (45, 20, 20, 16), (46, 29, 29, 16), (47, 4, 4, 16),
		(48, 3, 3, 16), (49, 30, 30, 16), (50, 19, 19, 16), (51, 14, 14, 16), (52, 11, 11, 16), (53, 22, 22, 16), (54, 27, 27, 16), (55, 6, 6, 16),
		(56, 7, 7, 16), (57, 26, 26, 16), (58, 23, 23, 16), (59, 10, 10, 16), (60, 15, 15, 16), (61, 18, 18, 16), (62, 31, 31, 16), (63, 2, 2, 16),
		(64, 1, 1, 32), (65, 64, 0, 32), (66, 33, 33, 32), (67, 32, 32, 32), (68, 17, 17, 32), (69, 48, 48, 32), (70, 49, 49, 32), (71, 16, 16, 32),
		(72, 9, 9, 32), (73, 56, 56, 32), (74, 41, 41, 32), (75, 24, 24, 32), (76, 25, 25, 32), (77, 40, 40, 32), (78, 57, 0, 32), (79, 8, 8, 32),
		(80, 5, 5, 32), (81, 60, 0, 32), (82, 37, 37, 32), (83, 28, 28, 32), (84, 21, 21, 32), (85, 44, 44, 32), (86, 53, 53, 32), (87, 12, 12, 32),
		(88, 13, 13, 32), (89, 52, 52, 32), (90, 45, 45, 32), (91, 20, 20, 32), (92, 29, 29, 32), (93, 36, 36, 32), (94, 61, 0, 32), (95, 4, 4, 32),
		(96, 3, 3, 32), (97, 62, 0, 32), (98, 35, 35, 32), (99, 30, 30, 32), (100, 19, 19, 32), (101, 46, 46, 32), (102, 51, 51, 32), (103, 14, 14, 32),
		(104, 11, 11, 32), (105, 54, 54, 32), (106, 43, 43, 32), (107, 22, 22, 32), (108, 27, 27, 32), (109, 38, 38, 32), (110, 59, 0, 32), (111, 6, 6, 32),
		(112, 7, 7, 32), (113, 58, 0, 32), (114, 39, 39, 32), (115, 26, 26, 32), (116, 23, 23, 32), (117, 42, 42, 32), (118, 55, 55, 32), (119, 10, 10, 32),
		(120, 15, 15, 32), (121, 50, 50, 32), (122, 47, 47, 32), (123, 18, 18, 32), (124, 31, 31, 32), (125, 34, 34, 32), (126, 63, 0, 32), (127, 2, 2, 32),
		(128, 1, 1, 64), (129, 128, 0, 64), (130, 65, 0, 64), (131, 64, 0, 64), (132, 33, 33, 64), (133, 96, 80, 64), (134, 97, 81, 64), (135, 32, 32, 64),
		(136, 17, 17, 64), (137, 112, 96, 64), (138, 81, 65, 64), (139, 48, 48, 64), (140, 49, 49, 64), (141, 80, 64, 64), (142, 113, 97, 64), (143, 16, 16, 64),
		(144, 9, 9, 64), (145, 120, 104, 64), (146, 73, 57, 64), (147, 56, 56, 64), (148, 41, 41, 64), (149, 88, 72, 64), (150, 105, 89, 64), (151, 24, 24, 64),
		(152, 25, 25, 64), (153, 104, 88, 64), (154, 89, 73, 64), (155, 40, 40, 64), (156, 57, 0, 64), (157, 72, 0, 64), (158, 121, 0, 64), (159, 8, 8, 64),
		(160, 5, 5, 64), (161, 124, 0, 64), (162, 69, 0, 64), (163, 60, 0, 64), (164, 37, 37, 64), (165, 92, 76, 64), (166, 101, 85, 64), (167, 28, 28, 64),
		(168, 21, 21, 64), (169, 108, 92, 64), (170, 85, 69, 64), (171, 44, 44, 64), (172, 53, 53, 64), (173, 76, 60, 64), (174, 117, 101, 64), (175, 12, 12, 64),
		(176, 13, 13, 64), (177, 116, 100, 64), (178, 77, 61, 64), (179, 52, 52, 64), (180, 45, 45, 64), (181, 84, 68, 64), (182, 109, 93, 64), (183, 20, 20, 64),
		(184, 29, 29, 64), (185, 100, 84, 64), (186, 93, 77, 64), (187, 36, 36, 64), (188, 61, 0, 64), (189, 68, 0, 64), (190, 125, 0, 64), (191, 4, 4, 64),
		(192, 3, 3, 64), (193, 126, 0, 64), (194, 67, 0, 64), (195, 62, 0, 64), (196, 35, 35, 64), (197, 94, 78, 64), (198, 99, 83, 64), (199, 30, 30, 64),
		(200, 19, 19, 64), (201, 110, 94, 64), (202, 83, 67, 64), (203, 46, 46, 64), (204, 51, 51, 64), (205, 78, 62, 64), (206, 115, 99, 64), (207, 14, 14, 64),
		(208, 11, 11, 64), (209, 118, 102, 64), (210, 75, 59, 64), (211, 54, 54, 64), (212, 43, 43, 64), (213, 86, 70, 64), (214, 107, 91, 64), (215, 22, 22, 64),
		(216, 27, 27, 64), (217, 102, 86, 64), (218, 91, 75, 64), (219, 38, 38, 64), (220, 59, 0, 64), (221, 70, 0, 64), (222, 123, 0, 64), (223, 6, 6, 64),
		(224, 7, 7, 64), (225, 122, 0, 64), (226, 71, 0, 64), (227, 58, 0, 64), (228, 39, 39, 64), (229, 90, 74, 64), (230, 103, 87, 64), (231, 26, 26, 64),
		(232, 23, 23, 64), (233, 106, 90, 64), (234, 87, 71, 64), (235, 42, 42, 64), (236, 55, 55, 64), (237, 74, 58, 64), (238, 119, 103, 64), (239, 10, 10, 64),
		(240, 15, 15, 64), (241, 114, 98, 64), (242, 79, 63, 64), (243, 50, 50, 64), (244, 47, 47, 64), (245, 82, 66, 64), (246, 111, 95, 64), (247, 18, 18, 64),
		(248, 31, 31, 64), (249, 98, 82, 64), (250, 95, 79, 64), (251, 34, 34, 64), (252, 63, 0, 64), (253, 66, 0, 64), (254, 127, 0, 64), (255, 2, 2, 64)";
	$r=safe_w_sql($q,false,array());
	db_save_version('2011-06-30 9:56:00');
}

if($version<'2011-07-22 13:40:00') {

	$q="ALTER TABLE `Photos` CHANGE `PhPhoto` `PhPhoto` LONGBLOB NOT NULL ";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2011-07-22 13:40:00');
}

if($version<'2011-08-01 10:55:00') {

	$q="
		INSERT INTO Parameters (ParId,ParValue) VALUES('ChkUp','".date('Y-m-d H:i:s')."') ON DUPLICATE KEY UPDATE ParValue='".date('Y-m-d H:i:s')."'
	";
	$r=safe_w_sql($q,false,array());

	db_save_version('2011-08-01 10:55:00');
}

if($version<'2011-08-14 11:38:00') {

	$q="CREATE or replace VIEW `EventCategories` AS select * from
`EventClass` join `Events` on `EvCode` = `EcCode`
 and `EvTeamEvent` = `EcTeamEvent`
 and `EvTournament` = `EcTournament`";
	$r=safe_w_sql($q,false,array());

	db_save_version('2011-08-14 11:38:00');
}

if($version<'2011-08-17 18:13:00') {
	require_once('Common/Fun_FormatText.inc.php');

	// updates Countries
	safe_w_sql("update Countries set CoName=upper(CoName)");
	$Sql="select * from Countries where (binary CoName=upper(CoName) or binary CoName=lower(CoName)) and CoName>''";
	$q=safe_r_sql($Sql);
	while($r=safe_fetch($q)) {
		safe_w_sql("update Countries set "
			. "CoName=".StrSafe_DB(AdjustCaseTitle($r->CoName))
			. " WHERE CoId=$r->CoId");
	}
	$Sql="select * from Countries where (binary CoNameComplete=upper(CoNameComplete) or binary CoNameComplete=upper(CoNameComplete)) and CoNameComplete>''";
	$q=safe_r_sql($Sql);
	while($r=safe_fetch($q)) {
		safe_w_sql("update Countries set "
			. "CoNameComplete=".StrSafe_DB(AdjustCaseTitle($r->CoNameComplete))
			. " WHERE CoId=$r->CoId");
	}

	// updates Entries
	$Sql="select * from Entries where (binary EnName=upper(EnName) or binary EnName=upper(EnName)) and EnName>''";
	$q=safe_r_sql($Sql);
	while($r=safe_fetch($q)) {
		safe_w_sql("update Entries set "
			. "EnName=".StrSafe_DB(AdjustCaseTitle($r->EnName))
			. " WHERE EnId=$r->EnId");
	}
	$Sql="select * from Entries where (binary EnFirstName=upper(EnFirstName) or binary EnFirstName=upper(EnFirstName) ) and EnFirstName>''";
	$q=safe_r_sql($Sql);
	while($r=safe_fetch($q)) {
		safe_w_sql("update Entries set "
			. "EnFirstName=".StrSafe_DB(AdjustCaseTitle($r->EnFirstName))
			. " WHERE EnId=$r->EnId");
	}

	db_save_version('2011-08-17 18:13:00');
}

if($version<'2011-08-23 15:59:00') {
	$q="ALTER TABLE `LookUpEntries` ADD `LueStatusValidUntil` DATE NOT NULL DEFAULT '0000-00-00' AFTER `LueStatus` ";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2011-08-23 15:59:00');
}

if($version<'2011-09-06 09:48:00') {
	safe_w_sql('DROP TABLE IF EXISTS `LookUpEntries`');
	safe_w_sql('CREATE TABLE IF NOT EXISTS `LookUpEntries` (
  `LueCode` varchar(9) NOT NULL,
  `LueIocCode` varchar(5) NOT NULL DEFAULT \'\',
  `LueFamilyName` varchar(60) NOT NULL,
  `LueName` varchar(30) NOT NULL,
  `LueSex` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
  `LueCtrlCode` varchar(16) NOT NULL,
  `LueCountry` varchar(5) NOT NULL,
  `LueCoDescr` varchar(80) NOT NULL,
  `LueCoShort` varchar(30) NOT NULL,
  `LueCountry2` varchar(5) NOT NULL,
  `LueCoDescr2` varchar(80) NOT NULL,
  `LueCoShort2` varchar(30) NOT NULL,
  `LueDivision` varchar(2) NOT NULL,
  `LueClass` varchar(2) NOT NULL,
  `LueSubClass` varchar(2) NOT NULL,
  `LueStatus` tinyint(4) NOT NULL,
  `LueStatusValidUntil` date NOT NULL DEFAULT \'0000-00-00\',
  `LueDefault` tinyint(4) NOT NULL DEFAULT \'0\',
  PRIMARY KEY (`LueCode`,`LueIocCode`,`LueClass`),
  KEY `LueCountry` (`LueCountry`),
  KEY `LueCode` (`LueCode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8');

	db_save_version('2011-09-06 09:48:00');
}

if($version<'2011-09-08 11:43:00') {

	$q="
		UPDATE
			LookUpPaths
		SET
			LupPath='http://www.fitarco-italia.org/gare/ianseo/IanseoData.php'
		WHERE
			LupIocCode='ITA'
	";
	safe_w_sql($q);

	db_save_version('2011-09-08 11:43:00');
}

if($version<'2011-10-06 08:48:00') {
	$q="ALTER TABLE `Tournament` ADD `ToIsORIS` varchar(1) NOT NULL DEFAULT '' ";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2011-10-06 08:48:00');
}

if($version<'2011-11-04 18:08:00') {
	$q="ALTER TABLE `Classes` CHANGE `ClValidClass` `ClValidClass` VARCHAR( 24 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0'";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2011-11-04 18:08:00');
}

if($version<'2011-11-08 16:32:00') {
	$q="ALTER TABLE `Entries` ADD `EnCountry3` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `EnCountry2` ";
	$r=safe_w_sql($q,false,array(1060));

	$q="ALTER TABLE `LookUpEntries` ADD `LueCountry3` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `LueCoShort2` ,
		ADD `LueCoDescr3` VARCHAR( 80 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `LueCountry3` ,
		ADD `LueCoShort3` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `LueCoDescr3`";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2011-11-08 16:32:00');
}

if($version<'2011-11-09 10:43:00') {

	$q="INSERT INTO `Targets` (
		`TarId` ,`TarDescr` ,`TarArray` ,`TarOrder` ,`A_size` ,`A_color` ,`B_size` ,`B_color` ,`C_size` ,`C_color` ,
		`D_size` ,`D_color` ,`E_size` ,	`E_color` ,`F_size` ,`F_color` ,`G_size` ,`G_color` ,`H_size` ,`H_color` ,
		`I_size` ,`I_color` ,`J_size` ,`J_color` ,`K_size` ,`K_color` ,`L_size` ,`L_color` ,`M_size` ,`M_color` ,
		`N_size` ,`N_color` ,	`O_size` ,`O_color` ,`P_size` ,`P_color` ,`Q_size` ,`Q_color` ,`R_size` ,`R_color` ,
		`S_size` ,`S_color` ,`T_size` ,`T_color` ,`U_size` ,`U_color` ,`V_size` ,`V_color` ,`W_size` ,`W_color` ,
		`X_size` ,`X_color` ,`Y_size` ,`Y_color` ,`Z_size` ,`Z_color` ,	`TarDummyLine`
		)
		VALUES (
		NULL , 'TrgForestSwe', 'TrgForestSwe', '12', '', '', '', '', '', '', '', '', '', '', '30', '00A3D1', '', '', '', '', '', '', '', '', '', '', '25', 'F9E11E', '', '', '', '', '', '', '', '', '20', 'ED2939', '', '', '', '', '', '', '', '', '15', 'f9e11e', '', '', '', '', '', '', '', '', ''
		);
	";
	$r=safe_w_sql($q,false,array());
	db_save_version('2011-11-09 10:43:00');
}

if($version<'2011-11-10 10:05:00') {
	$q="
		ALTER TABLE `Events` ADD `EvTeamCreationMode` TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER `EvMixedTeam`
	";
	$r=safe_w_sql($q,false,array(1060));
	db_save_version('2011-11-10 10:05:00');
}


if($version<'2011-11-15 18:05:00') {
	$q="DROP TABLE IF EXISTS `F2FEntries`";
	$r=safe_w_sql($q,false,array(1060));
	$q="CREATE TABLE IF NOT EXISTS `F2FEntries` (
		  `F2FTournament` int(10) unsigned NOT NULL DEFAULT '0',
		  `F2FPhase` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1=fase 1;2=fase2',
		  `F2FMatchNo` tinyint(3) unsigned NOT NULL DEFAULT '0',
		  `F2FGroup` tinyint(3) unsigned NOT NULL,
		  `F2FEventCode` varchar(4) NOT NULL,
		  `F2FEnId` int(11) unsigned NOT NULL DEFAULT '0',
		  `F2FRank` tinyint(4) unsigned NOT NULL,
		  `F2FTiebreak` varchar(9) NOT NULL,
		  PRIMARY KEY (`F2FTournament`,`F2FPhase`,`F2FMatchNo`,`F2FGroup`,`F2FEventCode`,`F2FEnId`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1050));

	$q="DROP TABLE IF EXISTS `F2FFinal`";
	$r=safe_w_sql($q,false,array(1060));
	$q="CREATE TABLE IF NOT EXISTS `F2FFinal` (
		  `F2FEvent` varchar(4) NOT NULL,
		  `F2FMatchNo` tinyint(4) unsigned NOT NULL DEFAULT '0',
		  `F2FTournament` int(10) unsigned NOT NULL DEFAULT '0',
		  `F2FSetPoints` varchar(23) NOT NULL,
		  `F2FSetScore` tinyint(4) NOT NULL DEFAULT '0',
		  `F2FScore` smallint(6) NOT NULL DEFAULT '0',
		  `F2FTie` tinyint(1) NOT NULL DEFAULT '0',
		  `F2FArrowString` varchar(36) NOT NULL,
		  `F2FTieBreak` varchar(3) NOT NULL,
		  `F2FTiePoins` varchar(5) NOT NULL,
		  `F2FTieScore` smallint(6) NOT NULL DEFAULT '0',
		  `F2FScore2` smallint(6) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`F2FEvent`,`F2FMatchNo`,`F2FTournament`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1050));

	$q="DROP TABLE IF EXISTS `F2FGrid`";
	$r=safe_w_sql($q,false,array(1060));
	$q="CREATE TABLE IF NOT EXISTS `F2FGrid` (
		  `F2FPhase` tinyint(3) unsigned NOT NULL COMMENT '1 o 2 a seconda della fase della gara',
		  `F2FRound` tinyint(3) unsigned NOT NULL,
		  `F2FMatchNo1` tinyint(3) unsigned NOT NULL,
		  `F2FMatchNo2` tinyint(3) unsigned NOT NULL,
		  `F2FGroup` tinyint(3) unsigned NOT NULL,
		  `F2FTarget1` varchar(3) NOT NULL,
		  `F2FTarget2` varchar(3) NOT NULL,
		  PRIMARY KEY (`F2FPhase`,`F2FRound`,`F2FMatchNo1`,`F2FMatchNo2`,`F2FGroup`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1050));
	$q="INSERT INTO `F2FGrid` (`F2FPhase`, `F2FRound`, `F2FMatchNo1`, `F2FMatchNo2`, `F2FGroup`, `F2FTarget1`, `F2FTarget2`) VALUES
		(0, 1, 1, 6, 16, '46a', '46b'),(0, 1, 2, 5, 16, '47a', '47b'),(0, 1, 3, 4, 16, '48a', '48b'),(0, 2, 1, 5, 16, '47b', '47a'),(0, 2, 2, 3, 16, '46a', '46b'),(0, 2, 4, 6, 16, '48a', '48b'),
		(0, 3, 1, 4, 16, '47a', '47b'),(0, 3, 2, 6, 16, '46b', '46a'),(0, 3, 3, 5, 16, '48b', '48a'),(0, 4, 1, 3, 16, '46b', '46a'),(0, 4, 2, 4, 16, '47b', '47a'),(0, 4, 5, 6, 16, '48b', '48b'),
		(0, 5, 1, 2, 16, '48a', '48b'),(0, 5, 3, 6, 16, '47a', '47b'),(0, 5, 4, 5, 16, '46a', '46b'),(1, 1, 1, 6, 8, '22a', '22b'),(1, 1, 2, 5, 8, '23a', '23b'),(1, 1, 3, 4, 8, '24a', '24b'),
		(1, 2, 1, 5, 8, '23b', '23a'),(1, 2, 2, 3, 8, '22a', '22b'),(1, 2, 4, 6, 8, '24a', '24b'),(1, 3, 1, 4, 8, '23a', '23b'),(1, 3, 2, 6, 8, '22b', '22a'),(1, 3, 3, 5, 8, '24b', '24a'),
		(1, 4, 1, 3, 8, '22b', '22a'),(1, 4, 2, 4, 8, '23b', '23a'),(1, 4, 5, 6, 8, '24b', '24a'),(1, 5, 1, 2, 8, '24a', '24b'),(1, 5, 3, 6, 8, '23a', '23b'),(1, 5, 4, 5, 8, '22a', '22b'),
		(2, 1, 1, 6, 4, '10a', '10b'),(2, 1, 2, 5, 4, '11a', '11b'),(2, 1, 3, 4, 4, '12a', '12b'),(2, 2, 1, 5, 4, '11b', '11a'),(2, 2, 2, 3, 4, '10a', '10b'),(2, 2, 4, 6, 4, '12a', '12b'),
		(2, 3, 1, 4, 4, '11a', '11b'),(2, 3, 2, 6, 4, '10b', '10a'),(2, 3, 3, 5, 4, '12b', '12a'),(2, 4, 1, 3, 4, '10b', '10a'),(2, 4, 2, 4, 4, '11b', '11a'),(2, 4, 5, 6, 4, '12b', '12a'),
		(2, 5, 1, 2, 4, '12a', '12b'),(2, 5, 3, 6, 4, '11a', '11b'),(2, 5, 4, 5, 4, '10a', '10b'),(3, 1, 1, 6, 2, '4a', '4b'),(3, 1, 2, 5, 2, '5a', '5b'),(3, 1, 3, 4, 2, '6a', '6b'),
		(3, 2, 1, 5, 2, '5b', '5a'),(3, 2, 2, 3, 2, '4a', '4b'),(3, 2, 4, 6, 2, '6a', '6b'),(3, 3, 1, 4, 2, '5a', '5b'),(3, 3, 2, 6, 2, '4b', '4a'),(3, 3, 3, 5, 2, '6b', '6a'),
		(3, 4, 1, 3, 2, '4b', '4a'),(3, 4, 2, 4, 2, '5b', '5a'),(3, 4, 5, 6, 2, '6b', '6a'),(3, 5, 1, 2, 2, '6a', '6b'),(3, 5, 3, 6, 2, '5a', '5b'),(3, 5, 4, 5, 2, '4a', '4b'),
		(0, 1, 1, 6, 1, '1a', '1b'),(0, 1, 2, 5, 1, '2a', '2b'),(0, 1, 3, 4, 1, '3a', '3b'),(0, 2, 1, 5, 1, '2b', '2a'),(0, 2, 2, 3, 1, '1a', '1b'),(0, 2, 4, 6, 1, '3a', '3b'),
		(0, 3, 1, 4, 1, '2a', '2b'),(0, 3, 2, 6, 1, '1b', '1a'),(0, 3, 3, 5, 1, '3b', '3a'),(0, 4, 1, 3, 1, '1b', '1a'),(0, 4, 2, 4, 1, '2b', '2a'),(0, 4, 5, 6, 1, '3b', '3a'),
		(0, 5, 1, 2, 1, '3a', '3b'),(0, 5, 3, 6, 1, '2a', '2b'),(0, 5, 4, 5, 1, '1a', '1b'),(0, 1, 1, 6, 2, '4a', '4b'),(0, 1, 2, 5, 2, '5a', '5b'),(0, 1, 3, 4, 2, '6a', '6b'),
		(0, 2, 1, 5, 2, '5b', '5a'),(0, 2, 2, 3, 2, '4a', '4b'),(0, 2, 4, 6, 2, '6a', '6b'),(0, 3, 1, 4, 2, '5a', '5b'),(0, 3, 2, 6, 2, '4b', '4a'),(0, 3, 3, 5, 2, '6b', '6a'),
		(0, 4, 1, 3, 2, '4b', '4a'),(0, 4, 2, 4, 2, '5b', '5a'),(0, 4, 5, 6, 2, '6b', '6b'),(0, 5, 1, 2, 2, '6a', '6b'),(0, 5, 3, 6, 2, '5a', '5b'),(0, 5, 4, 5, 2, '4a', '4b'),
		(0, 1, 1, 6, 3, '7a', '7b'),(0, 1, 2, 5, 3, '8a', '8b'),(0, 1, 3, 4, 3, '9a', '9b'),(0, 2, 1, 5, 3, '8b', '8a'),(0, 2, 2, 3, 3, '7a', '7b'),(0, 2, 4, 6, 3, '9a', '9b'),
		(0, 3, 1, 4, 3, '8a', '8b'),(0, 3, 2, 6, 3, '7b', '7a'),(0, 3, 3, 5, 3, '9b', '9a'),(0, 4, 1, 3, 3, '7b', '7a'),(0, 4, 2, 4, 3, '8b', '8a'),(0, 4, 5, 6, 3, '9b', '9b'),
		(0, 5, 1, 2, 3, '9a', '9b'),(0, 5, 3, 6, 3, '8a', '8b'),(0, 5, 4, 5, 3, '7a', '7b'),(0, 1, 1, 6, 4, '10a', '10b'),(0, 1, 2, 5, 4, '11a', '11b'),(0, 1, 3, 4, 4, '12a', '12b'),
		(0, 2, 1, 5, 4, '11b', '11a'),(0, 2, 2, 3, 4, '10a', '10b'),(0, 2, 4, 6, 4, '12a', '12b'),(0, 3, 1, 4, 4, '11a', '11b'),(0, 3, 2, 6, 4, '10b', '10a'),(0, 3, 3, 5, 4, '12b', '12a'),
		(0, 4, 1, 3, 4, '10b', '10a'),(0, 4, 2, 4, 4, '11b', '11a'),(0, 4, 5, 6, 4, '12b', '12b'),(0, 5, 1, 2, 4, '12a', '12b'),(0, 5, 3, 6, 4, '11a', '11b'),(0, 5, 4, 5, 4, '10a', '10b'),
		(0, 1, 1, 6, 5, '13a', '13b'),(0, 1, 2, 5, 5, '14a', '14b'),(0, 1, 3, 4, 5, '15a', '15b'),(0, 2, 1, 5, 5, '14b', '14a'),(0, 2, 2, 3, 5, '13a', '13b'),(0, 2, 4, 6, 5, '15a', '15b'),
		(0, 3, 1, 4, 5, '14a', '14b'),(0, 3, 2, 6, 5, '13b', '13a'),(0, 3, 3, 5, 5, '15b', '15a'),(0, 4, 1, 3, 5, '13b', '13a'),(0, 4, 2, 4, 5, '14b', '14a'),(0, 4, 5, 6, 5, '15b', '15b'),
		(0, 5, 1, 2, 5, '15a', '15b'),(0, 5, 3, 6, 5, '14a', '14b'),(0, 5, 4, 5, 5, '13a', '13b'),(0, 1, 1, 6, 6, '16a', '16b'),(0, 1, 2, 5, 6, '17a', '17b'),(0, 1, 3, 4, 6, '18a', '18b'),
		(0, 2, 1, 5, 6, '17b', '17a'),(0, 2, 2, 3, 6, '16a', '16b'),(0, 2, 4, 6, 6, '18a', '18b'),(0, 3, 1, 4, 6, '17a', '17b'),(0, 3, 2, 6, 6, '16b', '16a'),(0, 3, 3, 5, 6, '18b', '18a'),
		(0, 4, 1, 3, 6, '16b', '16a'),(0, 4, 2, 4, 6, '17b', '17a'),(0, 4, 5, 6, 6, '18b', '18b'),(0, 5, 1, 2, 6, '18a', '18b'),(0, 5, 3, 6, 6, '17a', '17b'),(0, 5, 4, 5, 6, '16a', '16b'),
		(0, 1, 1, 6, 7, '19a', '19b'),(0, 1, 2, 5, 7, '20a', '20b'),(0, 1, 3, 4, 7, '21a', '21b'),(0, 2, 1, 5, 7, '20b', '20a'),(0, 2, 2, 3, 7, '19a', '19b'),(0, 2, 4, 6, 7, '21a', '21b'),
		(0, 3, 1, 4, 7, '20a', '20b'),(0, 3, 2, 6, 7, '19b', '19a'),(0, 3, 3, 5, 7, '21b', '21a'),(0, 4, 1, 3, 7, '19b', '19a'),(0, 4, 2, 4, 7, '20b', '20a'),(0, 4, 5, 6, 7, '21b', '21b'),
		(0, 5, 1, 2, 7, '21a', '21b'),(0, 5, 3, 6, 7, '20a', '20b'),(0, 5, 4, 5, 7, '19a', '19b'),(0, 1, 1, 6, 8, '22a', '22b'),(0, 1, 2, 5, 8, '23a', '23b'),(0, 1, 3, 4, 8, '24a', '24b'),
		(0, 2, 1, 5, 8, '23b', '23a'),(0, 2, 2, 3, 8, '22a', '22b'),(0, 2, 4, 6, 8, '24a', '24b'),(0, 3, 1, 4, 8, '23a', '23b'),(0, 3, 2, 6, 8, '22b', '22a'),(0, 3, 3, 5, 8, '24b', '24a'),
		(0, 4, 1, 3, 8, '22b', '22a'),(0, 4, 2, 4, 8, '23b', '23a'),(0, 4, 5, 6, 8, '24b', '24b'),(0, 5, 1, 2, 8, '24a', '24b'),(0, 5, 3, 6, 8, '23a', '23b'),(0, 5, 4, 5, 8, '22a', '22b'),
		(0, 1, 1, 6, 9, '25a', '25b'),(0, 1, 2, 5, 9, '26a', '26b'),(0, 1, 3, 4, 9, '27a', '27b'),(0, 2, 1, 5, 9, '26b', '26a'),(0, 2, 2, 3, 9, '25a', '25b'),(0, 2, 4, 6, 9, '27a', '27b'),
		(0, 3, 1, 4, 9, '26a', '26b'),(0, 3, 2, 6, 9, '25b', '25a'),(0, 3, 3, 5, 9, '27b', '27a'),(0, 4, 1, 3, 9, '25b', '25a'),(0, 4, 2, 4, 9, '26b', '26a'),(0, 4, 5, 6, 9, '27b', '27b'),
		(0, 5, 1, 2, 9, '27a', '27b'),(0, 5, 3, 6, 9, '26a', '26b'),(0, 5, 4, 5, 9, '25a', '25b'),(0, 1, 1, 6, 10, '28a', '28b'),(0, 1, 2, 5, 10, '29a', '29b'),(0, 1, 3, 4, 10, '30a', '30b'),
		(0, 2, 1, 5, 10, '29b', '29a'),(0, 2, 2, 3, 10, '28a', '28b'),(0, 2, 4, 6, 10, '30a', '30b'),(0, 3, 1, 4, 10, '29a', '29b'),(0, 3, 2, 6, 10, '28b', '28a'),(0, 3, 3, 5, 10, '30b', '30a'),
		(0, 4, 1, 3, 10, '28b', '28a'),(0, 4, 2, 4, 10, '29b', '29a'),(0, 4, 5, 6, 10, '30b', '30b'),(0, 5, 1, 2, 10, '30a', '30b'),(0, 5, 3, 6, 10, '29a', '29b'),(0, 5, 4, 5, 10, '28a', '28b'),
		(0, 1, 1, 6, 11, '31a', '31b'),(0, 1, 2, 5, 11, '32a', '32b'),(0, 1, 3, 4, 11, '33a', '33b'),(0, 2, 1, 5, 11, '32b', '32a'),(0, 2, 2, 3, 11, '31a', '31b'),(0, 2, 4, 6, 11, '33a', '33b'),
		(0, 3, 1, 4, 11, '32a', '32b'),(0, 3, 2, 6, 11, '31b', '31a'),(0, 3, 3, 5, 11, '33b', '33a'),(0, 4, 1, 3, 11, '31b', '31a'),(0, 4, 2, 4, 11, '32b', '32a'),(0, 4, 5, 6, 11, '33b', '33b'),
		(0, 5, 1, 2, 11, '33a', '33b'),(0, 5, 3, 6, 11, '32a', '32b'),(0, 5, 4, 5, 11, '31a', '31b'),(0, 1, 1, 6, 12, '34a', '34b'),(0, 1, 2, 5, 12, '35a', '35b'),(0, 1, 3, 4, 12, '36a', '36b'),
		(0, 2, 1, 5, 12, '35b', '35a'),(0, 2, 2, 3, 12, '34a', '34b'),(0, 2, 4, 6, 12, '36a', '36b'),(0, 3, 1, 4, 12, '35a', '35b'),(0, 3, 2, 6, 12, '34b', '34a'),(0, 3, 3, 5, 12, '36b', '36a'),
		(0, 4, 1, 3, 12, '34b', '34a'),(0, 4, 2, 4, 12, '35b', '35a'),(0, 4, 5, 6, 12, '36b', '36b'),(0, 5, 1, 2, 12, '36a', '36b'),(0, 5, 3, 6, 12, '35a', '35b'),(0, 5, 4, 5, 12, '34a', '34b'),
		(0, 1, 1, 6, 13, '37a', '37b'),(0, 1, 2, 5, 13, '38a', '38b'),(0, 1, 3, 4, 13, '39a', '39b'),(0, 2, 1, 5, 13, '38b', '38a'),(0, 2, 2, 3, 13, '37a', '37b'),(0, 2, 4, 6, 13, '39a', '39b'),
		(0, 3, 1, 4, 13, '38a', '38b'),(0, 3, 2, 6, 13, '37b', '37a'),(0, 3, 3, 5, 13, '39b', '39a'),(0, 4, 1, 3, 13, '37b', '37a'),(0, 4, 2, 4, 13, '38b', '38a'),(0, 4, 5, 6, 13, '39b', '39b'),
		(0, 5, 1, 2, 13, '39a', '39b'),(0, 5, 3, 6, 13, '38a', '38b'),(0, 5, 4, 5, 13, '37a', '37b'),(0, 1, 1, 6, 14, '40a', '40b'),(0, 1, 2, 5, 14, '41a', '41b'),(0, 1, 3, 4, 14, '42a', '42b'),
		(0, 2, 1, 5, 14, '41b', '41a'),(0, 2, 2, 3, 14, '40a', '40b'),(0, 2, 4, 6, 14, '42a', '42b'),(0, 3, 1, 4, 14, '41a', '41b'),(0, 3, 2, 6, 14, '40b', '40a'),(0, 3, 3, 5, 14, '42b', '42a'),
		(0, 4, 1, 3, 14, '40b', '40a'),(0, 4, 2, 4, 14, '41b', '41a'),(0, 4, 5, 6, 14, '42b', '42b'),(0, 5, 1, 2, 14, '42a', '42b'),(0, 5, 3, 6, 14, '41a', '41b'),(0, 5, 4, 5, 14, '40a', '40b'),
		(0, 1, 1, 6, 15, '43a', '43b'),(0, 1, 2, 5, 15, '44a', '44b'),(0, 1, 3, 4, 15, '45a', '45b'),(0, 2, 1, 5, 15, '44b', '44a'),(0, 2, 2, 3, 15, '43a', '43b'),(0, 2, 4, 6, 15, '45a', '45b'),
		(0, 3, 1, 4, 15, '44a', '44b'),(0, 3, 2, 6, 15, '43b', '43a'),(0, 3, 3, 5, 15, '45b', '45a'),(0, 4, 1, 3, 15, '43b', '43a'),(0, 4, 2, 4, 15, '44b', '44a'),(0, 4, 5, 6, 15, '45b', '45b'),
		(0, 5, 1, 2, 15, '45a', '45b'),(0, 5, 3, 6, 15, '44a', '44b'),(0, 5, 4, 5, 15, '43a', '43b'),(1, 1, 1, 6, 1, '1a', '1b'),(1, 1, 2, 5, 1, '2a', '2b'),(1, 1, 3, 4, 1, '3a', '3b'),
		(1, 2, 1, 5, 1, '2b', '2a'),(1, 2, 2, 3, 1, '1a', '1b'),(1, 2, 4, 6, 1, '3a', '3b'),(1, 3, 1, 4, 1, '2a', '2b'),(1, 3, 2, 6, 1, '1b', '1a'),(1, 3, 3, 5, 1, '3b', '3a'),
		(1, 4, 1, 3, 1, '1b', '1a'),(1, 4, 2, 4, 1, '2b', '2a'),(1, 4, 5, 6, 1, '3b', '3a'),(1, 5, 1, 2, 1, '3a', '3b'),(1, 5, 3, 6, 1, '2a', '2b'),(1, 5, 4, 5, 1, '1a', '1b'),
		(1, 1, 1, 6, 2, '4a', '4b'),(1, 1, 2, 5, 2, '5a', '5b'),(1, 1, 3, 4, 2, '6a', '6b'),(1, 2, 1, 5, 2, '5b', '5a'),(1, 2, 2, 3, 2, '4a', '4b'),(1, 2, 4, 6, 2, '6a', '6b'),
		(1, 3, 1, 4, 2, '5a', '5b'),(1, 3, 2, 6, 2, '4b', '4a'),(1, 3, 3, 5, 2, '6b', '6a'),(1, 4, 1, 3, 2, '4b', '4a'),(1, 4, 2, 4, 2, '5b', '5a'),(1, 4, 5, 6, 2, '6b', '6a'),
		(1, 5, 1, 2, 2, '6a', '6b'),(1, 5, 3, 6, 2, '5a', '5b'),(1, 5, 4, 5, 2, '4a', '4b'),(1, 1, 1, 6, 3, '7a', '7b'),(1, 1, 2, 5, 3, '8a', '8b'),(1, 1, 3, 4, 3, '9a', '9b'),
		(1, 2, 1, 5, 3, '8b', '8a'),(1, 2, 2, 3, 3, '7a', '7b'),(1, 2, 4, 6, 3, '9a', '9b'),(1, 3, 1, 4, 3, '8a', '8b'),(1, 3, 2, 6, 3, '7b', '7a'),(1, 3, 3, 5, 3, '9b', '9a'),
		(1, 4, 1, 3, 3, '7b', '7a'),(1, 4, 2, 4, 3, '8b', '8a'),(1, 4, 5, 6, 3, '9b', '9a'),(1, 5, 1, 2, 3, '9a', '9b'),(1, 5, 3, 6, 3, '8a', '8b'),(1, 5, 4, 5, 3, '7a', '7b'),
		(1, 1, 1, 6, 4, '10a', '10b'),(1, 1, 2, 5, 4, '11a', '11b'),(1, 1, 3, 4, 4, '12a', '12b'),(1, 2, 1, 5, 4, '11b', '11a'),(1, 2, 2, 3, 4, '10a', '10b'),(1, 2, 4, 6, 4, '12a', '12b'),
		(1, 3, 1, 4, 4, '11a', '11b'),(1, 3, 2, 6, 4, '10b', '10a'),(1, 3, 3, 5, 4, '12b', '12a'),(1, 4, 1, 3, 4, '10b', '10a'),(1, 4, 2, 4, 4, '11b', '11a'),(1, 4, 5, 6, 4, '12b', '12a'),
		(1, 5, 1, 2, 4, '12a', '12b'),(1, 5, 3, 6, 4, '11a', '11b'),(1, 5, 4, 5, 4, '10a', '10b'),(1, 1, 1, 6, 5, '13a', '13b'),(1, 1, 2, 5, 5, '14a', '14b'),(1, 1, 3, 4, 5, '15a', '15b'),
		(1, 2, 1, 5, 5, '14b', '14a'),(1, 2, 2, 3, 5, '13a', '13b'),(1, 2, 4, 6, 5, '15a', '15b'),(1, 3, 1, 4, 5, '14a', '14b'),(1, 3, 2, 6, 5, '13b', '13a'),(1, 3, 3, 5, 5, '15b', '15a'),
		(1, 4, 1, 3, 5, '13b', '13a'),(1, 4, 2, 4, 5, '14b', '14a'),(1, 4, 5, 6, 5, '15b', '15a'),(1, 5, 1, 2, 5, '15a', '15b'),(1, 5, 3, 6, 5, '14a', '14b'),(1, 5, 4, 5, 5, '13a', '13b'),
		(1, 1, 1, 6, 6, '16a', '16b'),(1, 1, 2, 5, 6, '17a', '17b'),(1, 1, 3, 4, 6, '18a', '18b'),(1, 2, 1, 5, 6, '17b', '17a'),(1, 2, 2, 3, 6, '16a', '16b'),(1, 2, 4, 6, 6, '18a', '18b'),
		(1, 3, 1, 4, 6, '17a', '17b'),(1, 3, 2, 6, 6, '16b', '16a'),(1, 3, 3, 5, 6, '18b', '18a'),(1, 4, 1, 3, 6, '16b', '16a'),(1, 4, 2, 4, 6, '17b', '17a'),(1, 4, 5, 6, 6, '18b', '18a'),
		(1, 5, 1, 2, 6, '18a', '18b'),(1, 5, 3, 6, 6, '17a', '17b'),(1, 5, 4, 5, 6, '16a', '16b'),(1, 1, 1, 6, 7, '19a', '19b'),(1, 1, 2, 5, 7, '20a', '20b'),(1, 1, 3, 4, 7, '21a', '21b'),
		(1, 2, 1, 5, 7, '20b', '20a'),(1, 2, 2, 3, 7, '19a', '19b'),(1, 2, 4, 6, 7, '21a', '21b'),(1, 3, 1, 4, 7, '20a', '20b'),(1, 3, 2, 6, 7, '19b', '19a'),(1, 3, 3, 5, 7, '21b', '21a'),
		(1, 4, 1, 3, 7, '19b', '19a'),(1, 4, 2, 4, 7, '20b', '20a'),(1, 4, 5, 6, 7, '21b', '21a'),(1, 5, 1, 2, 7, '21a', '21b'),(1, 5, 3, 6, 7, '20a', '20b'),(1, 5, 4, 5, 7, '19a', '19b'),
		(2, 1, 1, 6, 1, '1a', '1b'),(2, 1, 2, 5, 1, '2a', '2b'),(2, 1, 3, 4, 1, '3a', '3b'),(2, 2, 1, 5, 1, '2b', '2a'),(2, 2, 2, 3, 1, '1a', '1b'),(2, 2, 4, 6, 1, '3a', '3b'),
		(2, 3, 1, 4, 1, '2a', '2b'),(2, 3, 2, 6, 1, '1b', '1a'),(2, 3, 3, 5, 1, '3b', '3a'),(2, 4, 1, 3, 1, '1b', '1a'),(2, 4, 2, 4, 1, '2b', '2a'),(2, 4, 5, 6, 1, '3b', '3a'),
		(2, 5, 1, 2, 1, '3a', '3b'),(2, 5, 3, 6, 1, '2a', '2b'),(2, 5, 4, 5, 1, '1a', '1b'),(2, 1, 1, 6, 2, '4a', '4b'),(2, 1, 2, 5, 2, '5a', '5b'),(2, 1, 3, 4, 2, '6a', '6b'),
		(2, 2, 1, 5, 2, '5b', '5a'),(2, 2, 2, 3, 2, '4a', '4b'),(2, 2, 4, 6, 2, '6a', '6b'),(2, 3, 1, 4, 2, '5a', '5b'),(2, 3, 2, 6, 2, '4b', '4a'),(2, 3, 3, 5, 2, '6b', '6a'),
		(2, 4, 1, 3, 2, '4b', '4a'),(2, 4, 2, 4, 2, '5b', '5a'),(2, 4, 5, 6, 2, '6b', '6a'),(2, 5, 1, 2, 2, '6a', '6b'),(2, 5, 3, 6, 2, '5a', '5b'),(2, 5, 4, 5, 2, '4a', '4b'),
		(2, 1, 1, 6, 3, '7a', '7b'),(2, 1, 2, 5, 3, '8a', '8b'),(2, 1, 3, 4, 3, '9a', '9b'),(2, 2, 1, 5, 3, '8b', '8a'),(2, 2, 2, 3, 3, '7a', '7b'),(2, 2, 4, 6, 3, '9a', '9b'),
		(2, 3, 1, 4, 3, '8a', '8b'),(2, 3, 2, 6, 3, '7b', '7a'),(2, 3, 3, 5, 3, '9b', '9a'),(2, 4, 1, 3, 3, '7b', '7a'),(2, 4, 2, 4, 3, '8b', '8a'),(2, 4, 5, 6, 3, '9b', '9a'),
		(2, 5, 1, 2, 3, '9a', '9b'),(2, 5, 3, 6, 3, '8a', '8b'),(2, 5, 4, 5, 3, '7a', '7b'),(3, 1, 1, 6, 1, '1a', '1b'),(3, 1, 2, 5, 1, '2a', '2b'),(3, 1, 3, 4, 1, '3a', '3b'),
		(3, 2, 1, 5, 1, '2b', '2a'),(3, 2, 2, 3, 1, '1a', '1b'),(3, 2, 4, 6, 1, '3a', '3b'),(3, 3, 1, 4, 1, '2a', '2b'),(3, 3, 2, 6, 1, '1b', '1a'),(3, 3, 3, 5, 1, '3b', '3a'),
		(3, 4, 1, 3, 1, '1b', '1a'),(3, 4, 2, 4, 1, '2b', '2a'),(3, 4, 5, 6, 1, '3b', '3a'),(3, 5, 1, 2, 1, '3a', '3b'),(3, 5, 3, 6, 1, '2a', '2b'),(3, 5, 4, 5, 1, '1a', '1b')";
	$r=safe_w_sql($q,false,array(1060));

	$q="DROP TABLE IF EXISTS `F2FGroupMatch`";
	$r=safe_w_sql($q,false,array(1060));
	$q="CREATE TABLE IF NOT EXISTS `F2FGroupMatch` (
		  `F2FGMGroup` tinyint(3) unsigned NOT NULL,
		  `F2FGMRank` tinyint(4) unsigned NOT NULL,
		  `F2FGMMatchNo` tinyint(3) unsigned NOT NULL,
		  PRIMARY KEY (`F2FGMGroup`,`F2FGMMatchNo`,`F2FGMRank`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1050));

	$q="DROP TABLE IF EXISTS `F2FRankMatch`";
	$r=safe_w_sql($q,false,array(1060));
	$q="CREATE TABLE IF NOT EXISTS `F2FRankMatch` (
		  `F2FRMEventPhase` tinyint(4) NOT NULL,
		  `F2FRMRank` tinyint(4) NOT NULL,
		  `F2FRMMatchNo` tinyint(3) unsigned NOT NULL,
		  PRIMARY KEY (`F2FRMEventPhase`,`F2FRMRank`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1050));

	$q="DROP TABLE IF EXISTS `F2FScore`";
	$r=safe_w_sql($q,false,array(1060));
	$q="CREATE TABLE IF NOT EXISTS `F2FScore` (
		  `F2FTournament` int(10) unsigned NOT NULL DEFAULT '0',
		  `F2FPhase` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0=fase0;1=fase1;2=fase2;ecc',
		  `F2FRound` tinyint(3) unsigned NOT NULL,
		  `F2FMatchNo` tinyint(3) unsigned NOT NULL,
		  `F2FEventCode` varchar(4) NOT NULL,
		  `F2FTarget` varchar(3) NOT NULL,
		  `F2FSetPoints` varchar(23) NOT NULL DEFAULT '',
		  `F2FSetScore` tinyint(4) NOT NULL DEFAULT '0',
		  `F2FScore` smallint(6) NOT NULL DEFAULT '0',
		  `F2FTie` tinyint(1) NOT NULL DEFAULT '0',
		  `F2FArrowString` varchar(36) NOT NULL,
		  `F2FArrowPosition` varchar(360) NOT NULL,
		  `F2FTiebreak` varchar(9) NOT NULL,
		  `F2FTiePosition` varchar(90) NOT NULL,
		  `F2FPoints` tinyint(4) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`F2FTournament`,`F2FPhase`,`F2FMatchNo`,`F2FEventCode`,`F2FRound`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1050));

	$q="DROP TABLE IF EXISTS `F2FTarget`";
	$r=safe_w_sql($q,false,array(1060));
	$q="CREATE TABLE IF NOT EXISTS `F2FTarget` (
		  `F2FTournament` int(10) unsigned NOT NULL DEFAULT '0',
		  `F2FEvent` varchar(4) NOT NULL DEFAULT '',
		  `F2FMatchNo` tinyint(4) unsigned NOT NULL DEFAULT '0',
		  `F2FTarget` varchar(3) NOT NULL,
		  `F2FSchedule` datetime NOT NULL,
		  PRIMARY KEY (`F2FTournament`,`F2FEvent`,`F2FMatchNo`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1050));

	db_save_version('2011-11-15 18:05:00');
}

if($version<'2011-11-21 17:33:00') {

	$q="DROP TABLE IF EXISTS `F2FTargetElim`";
	$r=safe_w_sql($q,false,array(1060));

	$q="CREATE TABLE `F2FTargetElim` (
		`F2FTournament` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0',
		`F2FPhase` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0',
		`F2FRound` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0',
		`F2FMatchNo1` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0',
		`F2FMatchNo2` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0',
		`F2FGroup` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0',
		`F2FEventCode` VARCHAR(4)  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
		`F2FTarget1` VARCHAR( 3 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		`F2FTarget2` VARCHAR( 3 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		PRIMARY KEY ( `F2FTournament`,`F2FPhase`,`F2FRound`,`F2FMatchNo1`,`F2FMatchNo2`,`F2FGroup`,`F2FEventCode` )
		) ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
	";
	$r=safe_w_sql($q,false,array(1050));

	db_save_version('2011-11-21 17:33:00');
}

if ($version<'2011-11-22 09:17:00')
{
// tutte le gare f2f
	$q="SELECT ToId FROM Tournament WHERE ToType=21 ";
	$r=safe_r_sql($q);

	while ($row=safe_fetch($r))
	{
	// eventi della gara
		$q="SELECT EvCode FROM Events WHERE EvTeamEvent=0 AND EvTournament={$row->ToId} ";
		$rs=safe_r_sql($q);

		while ($row2=safe_fetch($rs))
		{
			safe_w_sql("delete from F2FTargetElim where F2FTournament={$row->ToId} AND F2FEventCode='{$row2->EvCode}' ");

			$sql="
				INSERT INTO `F2FTargetElim` (
					`F2FTournament`,
					`F2FPhase`,
					`F2FRound`,
					`F2FMatchNo1`,
					`F2FMatchNo2`,
					`F2FGroup`,
					`F2FEventCode`,
					`F2FTarget1`,
					`F2FTarget2`
				)
				SELECT
					{$row->ToId},
					`F2FPhase`,
					`F2FRound`,
					`F2FMatchNo1`,
					`F2FMatchNo2`,
					`F2FGroup`,
					'$row2->EvCode',
					UPPER(IF(LENGTH(`F2FTarget1`)<3,CONCAT('0',`F2FTarget1`),`F2FTarget1`)),
					UPPER(IF(LENGTH(`F2FTarget2`)<3,CONCAT('0',`F2FTarget2`),`F2FTarget2`))
				FROM
					F2FGrid

			";
					//print $sql.'<br><br>';
			$rs2=safe_w_sql($sql);
		}
	}

	db_save_version('2011-11-22 09:17:00');
}

if ($version<'2011-11-22 16:03:00')
{
	$q="ALTER TABLE `F2FEntries`
		  DROP `F2FTarget`;";
	$r=safe_w_sql($q,false,array(1091));

	$q="ALTER TABLE `F2FFinal` DROP PRIMARY KEY ;";
	$r=safe_w_sql($q,false,array(1091));

	$q="ALTER TABLE `F2FFinal` ADD `F2FEnId` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0' FIRST ;";
	$r=safe_w_sql($q);

	$q="ALTER TABLE `F2FFinal` ADD `F2FPhase` TINYINT UNSIGNED NOT NULL AFTER `F2FEvent` ,
		ADD `F2FRound` TINYINT UNSIGNED NOT NULL AFTER `F2FPhase` ,
		ADD `F2FGroup` TINYINT UNSIGNED NOT NULL AFTER `F2FRound` ;";
	$r=safe_w_sql($q);

	$q="ALTER TABLE `F2FFinal` ADD `F2FTarget` VARCHAR( 3 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `F2FTournament` ;";
	$r=safe_w_sql($q);

	$q="ALTER TABLE `F2FFinal` ADD PRIMARY KEY ( `F2FEnId` , `F2FEvent` , `F2FPhase` , `F2FRound` , `F2FGroup` , `F2FMatchNo` , `F2FTournament` ) ";
	$r=safe_w_sql($q);

	db_save_version('2011-11-22 16:03:00');
}

if ($version<'2011-11-24 15:55:00')
{
	$q="
		ALTER TABLE `F2FFinal` DROP `F2FSetPoints` ,
		DROP `F2FSetScore` ,
		DROP `F2FScore` ,
		DROP `F2FTie` ,
		DROP `F2FArrowString` ,
		DROP `F2FTieBreak` ,
		DROP `F2FTiePoins` ,
		DROP `F2FTieScore` ,
		DROP `F2FScore2` ;
	";
	$r=safe_w_sql($q,false,array(1060,1091));

	$q="
		ALTER TABLE `F2FFinal` ADD `F2FArrowstring` VARCHAR( 12 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
	";
	$r=safe_w_sql($q,false,array(1060));

	$q="
		ALTER TABLE `F2FFinal` ADD `F2FScore` SMALLINT( 6 ) NOT NULL DEFAULT '0',
			ADD `F2FPoints` TINYINT( 3 ) NOT NULL DEFAULT '0';
	";
	$r=safe_w_sql($q,false,array(1060));

	$q="
		ALTER TABLE `F2FEntries` DROP `F2FRank` ,
			DROP `F2FTiebreak` ;
	";
	$r=safe_w_sql($q,false,array(1060,1091));

	$q="
		ALTER TABLE `F2FEntries` ADD `F2FRankPoints` SMALLINT( 6 ) NOT NULL DEFAULT '0',
		ADD `F2FRankMatch` SMALLINT( 6 ) NOT NULL DEFAULT '0';
	";
	$r=safe_w_sql($q,false,array(1060,1091));

	$q="ALTER TABLE `F2FEntries` ADD `F2FTieArrowstring` VARCHAR( 3 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
		ADD `F2FDist2Center` TINYINT NOT NULL DEFAULT '0';
	";
	$r=safe_w_sql($q,false,array(1060,1091));

	$q="ALTER TABLE `F2FEntries` DROP PRIMARY KEY ,
		ADD PRIMARY KEY ( `F2FTournament` , `F2FPhase` , `F2FEventCode` , `F2FEnId` , `F2FMatchNo` , `F2FGroup` )
	";
	$r=safe_w_sql($q,false,array(1060,1091));

	$q="
		ALTER TABLE `F2FEntries` ADD `F2FPoints` INT NOT NULL DEFAULT '0' AFTER `F2FEnId` ,
		ADD `F2FScore` INT NOT NULL DEFAULT '0' AFTER `F2FPoints`
	";
	$r=safe_w_sql($q,false,array(1060,1091));

	db_save_version('2011-11-24 15:55:00');
}

if ($version<'2011-11-24 16:58:00')
{
	$q="ALTER TABLE `F2FEntries` CHANGE `F2FRankPoints` `F2FRankGroup` SMALLINT( 6 ) NOT NULL DEFAULT '0',
		CHANGE `F2FRankMatch` `F2FRankScore` SMALLINT( 6 ) NOT NULL DEFAULT '0'";
	$r=safe_w_sql($q,false,array(1060,1091,1054));
	db_save_version('2011-11-24 16:58:00');
}

if ($version<'2011-11-25 10:04:00')
{
	$q="
		ALTER TABLE `F2FEntries` ADD `F2FTieScore` TINYINT NOT NULL DEFAULT '0' AFTER `F2FTieArrowstring` ;
	";
	$r=safe_w_sql($q,false,array(1060,1091,1054));

	$q="
		ALTER TABLE `F2FEntries` ADD `F2FTieArrowstring2` VARCHAR( 3 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
			ADD `F2FTieScore2` TINYINT NOT NULL DEFAULT '0',
			ADD `F2FDist2Center2` TINYINT NOT NULL DEFAULT '0';
	";
	$r=safe_w_sql($q,false,array(1060,1091,1054));

	db_save_version('2011-11-25 10:04:00');
}

if($version<'2011-11-27 14:35:00') {
	$q="ALTER TABLE `Countries` ADD `CoParent1` INT UNSIGNED NOT NULL AFTER  `CoSubCountry`, ADD `CoParent2` INT UNSIGNED NOT NULL AFTER  `CoParent1`";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2011-11-27 14:35:00');
}

if($version<'2011-11-29 10:33:00') {
	$q="ALTER TABLE `F2FFinal` ADD `F2FSchedule` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2011-11-29 10:33:00');
}

if($version<'2011-11-29 14:33:00') {
	$q="ALTER TABLE  `F2FFinal` DROP PRIMARY KEY , ADD PRIMARY KEY (  `F2FTournament` ,  `F2FEvent` ,  `F2FPhase` ,  `F2FGroup` ,  `F2FMatchNo` ,  `F2FRound` ,  `F2FEnId` )";
	$r=safe_w_sql($q,false,array(1060));

	$q="ALTER TABLE  `F2FFinal` ADD  `F2FTimeStamp` TIMESTAMP NOT NULL";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2011-11-29 14:33:00');
}

if($version<'2011-12-01 21:27:00') {
	$q="CREATE TABLE  Awards (AwTournament INT UNSIGNED NOT NULL ,
		AwEvent VARCHAR(4) NOT NULL ,
		AwFinEvent TINYINT NOT NULL ,
		AwTeam TINYINT NOT NULL ,
		AwUnrewarded TINYINT NOT NULL ,
		AwPositions VARCHAR(16) NOT NULL ,
		AwDescription VARCHAR(50) NOT NULL ,
		PRIMARY KEY (AwTournament, AwEvent, AwFinEvent, AwTeam)) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1050));

	db_save_version('2011-12-01 21:27:00');
}

if($version<'2011-12-10 18:11:00') {
	$q="ALTER TABLE  `Awards` ADD  `AwAwarders` VARCHAR(200) NOT NULL";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2011-12-10 18:11:00');
}

if($version<'2011-12-11 10:30:00') {
	$q="ALTER TABLE  `Awards` ADD  `AwGroup` TINYINT NOT NULL, ADD  `AwOrder` TINYINT NOT NULL";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2011-12-11 10:30:00');
}

if($version<'2011-12-13 10:27:00') {
// per rancare la roba inutile dell'f2f
	$q="DROP TABLE IF EXISTS F2FGroupMatch ";
	$r=safe_w_sql($q,false,array());

	$q="DROP TABLE IF EXISTS F2FRankMatch ";
	$r=safe_w_sql($q,false,array());

	$q="DROP TABLE IF EXISTS F2FScore ";
	$r=safe_w_sql($q,false,array());

	$q="DROP TABLE IF EXISTS F2FTarget ";
	$r=safe_w_sql($q,false,array());

	$q="DROP TABLE IF EXISTS F2FTargetElim ";
	$r=safe_w_sql($q,false,array());

	$q="ALTER TABLE `F2FGrid`
		  DROP `F2FTarget1`,
		  DROP `F2FTarget2`;";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2011-12-13 10:27:00');
}

?>