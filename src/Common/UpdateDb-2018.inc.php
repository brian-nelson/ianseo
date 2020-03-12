<?php

if($version<'2018-01-14 12:29:02') {
	$q="ALTER TABLE `Events` ADD EvNumQualified int NOT NULL after EvFinalFirstPhase, ADD EvFirstQualified int default 1 after EvNumQualified";
	$r=safe_w_sql($q,false,array(1146, 1060));

	// updates the Phases
	safe_w_sql("insert ignore into Phases values (7, '7final', 8, 1)");
	safe_w_sql("insert ignore into Phases values (14, '14final', 16, 1)");

	// set previous things
	$q=safe_r_sql("select distinct ToId from Tournament");
	while($r=safe_fetch($q)) {
		updateEliminationEvents_20180114($r->ToId);
	}

	db_save_version('2018-01-14 12:29:02');
}

if($version<'2018-01-25 12:29:02') {
	$q="ALTER TABLE `Events` ADD EvWinnerFinalRank int NOT NULL default 1 after EvFinalFirstPhase";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2018-01-25 12:29:02');
}

if($version<'2018-01-31 12:29:02') {
	$q="ALTER TABLE `Session` change SesTar4Session SesTar4Session int NOT NULL, change SesFirstTarget SesFirstTarget int not null";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2018-01-31 12:29:02');
}

if($version<'2018-01-31 12:29:03') {
	$q="update Tournament set ToLocRule='NFAA' where ToLocRule='VEGAS'";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2018-01-31 12:29:03');
}

if($version<'2018-03-19 14:29:02') {
	$q="ALTER TABLE `Targets` ADD `TarFullSize` int NOT NULL after TarOrder";
	$r=safe_w_sql($q,false,array(1146, 1060));
	safe_w_sql("update Targets set TarFullSize=100 where TarId=1");
	safe_w_sql("update Targets set TarFullSize=100 where TarId=2");
	safe_w_sql("update Targets set TarFullSize=100 where TarId=3");
	safe_w_sql("update Targets set TarFullSize=100 where TarId=4");
	safe_w_sql("update Targets set TarFullSize=100 where TarId=5");
	safe_w_sql("update Targets set TarFullSize=50 where TarId=6");
	safe_w_sql("update Targets set TarFullSize=16 where TarId=7");
	safe_w_sql("update Targets set TarFullSize=0 where TarId=8");
	safe_w_sql("update Targets set TarFullSize=100 where TarId=9");
	safe_w_sql("update Targets set TarFullSize=100 where TarId=10");
	safe_w_sql("update Targets set TarFullSize=0 where TarId=11");
	safe_w_sql("update Targets set TarFullSize=0 where TarId=12");
	safe_w_sql("update Targets set TarFullSize=40 where TarId=13");
	safe_w_sql("update Targets set TarFullSize=40 where TarId=14");
	safe_w_sql("update Targets set TarFullSize=100 where TarId=15");
	safe_w_sql("update Targets set TarFullSize=100 where TarId=16");

	db_save_version('2018-03-19 14:29:02');
}

if($version<'2018-03-21 18:29:02') {
	$q="ALTER TABLE `Phases` ADD `PhRuleSets` TINYTEXT NOT NULL";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="ALTER TABLE `Phases` add index (PhId, PhRuleSets(50))";
	$r=safe_w_sql($q,false,array(1146, 1060));

	safe_w_sql("update Phases set PhRuleSets='FR' where PhId in (7,14)");
	db_save_version('2018-03-21 18:29:02');
}

if($version<'2018-03-26 18:29:03') {
	$q=safe_r_SQL("select version() as SqlVersion");
	if($r=safe_fetch($q)) {
		$v=explode('.', $r->SqlVersion);
		if(!($v[0]<5 or $v[1]<6)) {
			$q="ALTER TABLE `Finals` change `FinDateTime` FinDateTime DATETIME(3) NOT NULL default '0000-00-00 00:00:00.000'";
			$r=safe_w_sql($q,false,array(1146, 1060));
			$q="ALTER TABLE `TeamFinals` change `TfDateTime` TfDateTime DATETIME(3) NOT NULL default '0000-00-00 00:00:00.000'";
			$r=safe_w_sql($q,false,array(1146, 1060));
		}
	}

	db_save_version('2018-03-26 18:29:03');
}

if($version<'2018-04-07 10:29:02') {
	$r=safe_w_sql("drop table if exists FinOdfTiming",false,array(1146, 1060));
	$q="CREATE TABLE IF NOT EXISTS `FinOdfTiming` (
		`FinOdfTournament` int NOT NULL,
		`FinOdfEvent` varchar(4) NOT NULL,
		`FinOdfTeamEvent` tinyint NOT NULL,
		`FinOdfMatchno` int NOT NULL,
		`FinOdfStartlist` datetime NOT NULL,
		`FinOdfPrepare` datetime NOT NULL,
		`FinOdfBegin` datetime NOT NULL,
		`FinOdfEnd` datetime NOT NULL,
		`FinOdfUnofficial` datetime NOT NULL,
		`FinOdfConfirmed` datetime NOT NULL,
		`FinOdfArrows` text NOT NULL,
		`FinOdfTiming` text NOT NULL,
		PRIMARY KEY (FinOdfTournament, FinOdfTeamEvent, FinOdfEvent, FinOdfMatchno),
		index (FinOdfPrepare, FinOdfTournament, FinOdfTeamEvent, FinOdfEvent, FinOdfMatchno),
		index (FinOdfBegin, FinOdfTournament, FinOdfTeamEvent, FinOdfEvent, FinOdfMatchno),
		index (FinOdfEnd, FinOdfTournament, FinOdfTeamEvent, FinOdfEvent, FinOdfMatchno),
		index (FinOdfUnofficial, FinOdfTournament, FinOdfTeamEvent, FinOdfEvent, FinOdfMatchno),
		index (FinOdfConfirmed, FinOdfTournament, FinOdfTeamEvent, FinOdfEvent, FinOdfMatchno)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1060));

	db_save_version('2018-04-07 10:29:02');
}

if($version<'2018-04-25 10:10:00') {
    $r=safe_w_sql("drop table if exists HeartBeat",false,array(1146, 1060));
    $q="CREATE TABLE IF NOT EXISTS `HeartBeat` ( 
        `HbTournament` INT NOT NULL, 
        `HbEvent` VARCHAR(4) NOT NULL, 
        `HbTeamEvent` INT NOT NULL, 
        `HbMatchNo` INT NOT NULL, 
        `HbValue` SMALLINT NOT NULL, 
        `HbDateTime` DATETIME NOT NULL, 
        PRIMARY KEY (`HbTournament`, `HbEvent`, `HbTeamEvent`, `HbMatchNo`, `HbDateTime`)
        ) ENGINE = MyISAM DEFAULT CHARSET=utf8";
	$r=safe_w_sql($q,false,array(1060));

    db_save_version('2018-04-25 10:10:00');
}

if($version<'2018-04-29 10:29:02') {
	$q="ALTER TABLE `Finals` change FinTiePosition FinTiePosition text not null";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="ALTER TABLE `TeamFinals` change TfTiePosition TfTiePosition text not null";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2018-04-29 10:29:02');
}

if($version<'2018-05-03 14:36:00') {
	updateArrowPositions_20180503();
	db_save_version('2018-05-03 14:36:00');
}

if($version<'2018-05-21 17:58:00') {
    $q = "INSERT INTO `TourTypes` (`TtId`, `TtType`, `TtDistance`, `TtOrderBy`) VALUES 
      (39, 'Type_36Arr70mRound', '1', '39')";
    $r = safe_w_sql($q, false, array(1146, 1060));

    db_save_version('2018-05-21 17:58:00');
}

if($version<'2018-06-24 22:07:02') {
	updateArrowTimestamp_20180624();
	db_save_version('2018-06-24 22:07:02');
}

if($version<'2018-07-17 17:07:02') {
	$q="ALTER TABLE `Photos` ADD `PhToRetake` tinyint NOT NULL";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2018-07-17 17:07:02');
}

if($version<'2018-09-19 13:49:00') {
    $q="REPLACE INTO `LookUpPaths` (`LupIocCode`, `LupOrigin`, `LupPath`, `LupPhotoPath`, `LupFlagsPath`, `LupLastUpdate`, `LupRankingPath`, `LupClubNamesPath`, `LupRecordsPath`) 
      VALUES 
      ('ITA_i', '', 'http://www.fitarco-italia.org/gare/ianseo/IanseoDataIndoor.php', 'http://www.fitarco-italia.org/gare/ianseo/IanseoPhoto.php', 'http://www.fitarco-italia.org/gare/ianseo/IanseoFlags.php', NULL, '', '', '')";
    $r=safe_w_sql($q,false,array());
    $q="ALTER TABLE `LookUpEntries` DROP PRIMARY KEY, ADD PRIMARY KEY (`LueCode`, `LueIocCode`, `LueDivision`, `LueClass`)";
    $r=safe_w_sql($q,false,array());
    db_save_version('2018-09-19 13:49:00');
}

