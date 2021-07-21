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
if($version <= '2020-01-01 00:00:00') require_once('Common/UpdateDb-2019.inc.php');
if($version <= '2021-01-01 00:00:00') require_once('Common/UpdateDb-2020.inc.php');

if($version<'2021-01-05 11:13:01') {
	$q="ALTER TABLE ExtraData ADD INDEX (`EdId`, `EdType`, `EdEmail`(1));";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2021-01-05 11:13:01');
}

if($version<'2021-01-14 17:13:01') {
	$q="ALTER TABLE Emails ADD EmKey int not null after EmTournament, change EmTitle EmTitle varchar(50) not null";
	$r=safe_w_sql($q,false,array(1146, 1060));

	$q=safe_w_sql("select * from Emails order by EmTournament, EmTitle");
	$OldTour='aaa';
	while($r=safe_fetch($q)) {
		if($OldTour!=$r->EmTournament) {
			$Key=1;
			$OldTour = $r->EmTournament;
		}
		safe_w_sql("update Emails set EmKey=".($Key++)." where EmTournament=$r->EmTournament and EmTitle=".StrSafe_DB($r->EmTitle));
	}
	safe_w_sql("alter table Emails drop primary key",false,array(1146, 1060));
	safe_w_sql("alter table Emails add primary key (EmTournament, EmKey), add index (EmTournament, EmTitle)",false,array(1146, 1060));

	db_save_version('2021-01-14 17:13:01');
}

if($version<'2021-01-24 11:13:02') {
	$q="ALTER TABLE `ExtraData` DROP INDEX `EdId`, ADD INDEX `EdId` (`EdId`, `EdType`, `EdEmail`(1), `EdEvent`)";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2021-01-24 11:13:02');
}

if($version<'2021-03-07 19:15:00') {
    $q="UPDATE `Targets` SET `A_color` = '', `B_size` = '0', `B_color` = '', `I_color` = '', `O_size` = '9', `O_color` = 'FFFFFF', `P_size` = '18', `P_color` = '000000' WHERE `TarId` = 19";
    $r=safe_w_sql($q,false,array(1146, 1060));

    db_save_version('2021-03-07 19:15:00');
}

/*

// TEMPLATE
if($version<'2021-01-24 11:13:01') {
	$q="ALTER TABLE `Finals` ADD `FinShootFirst` tinyint NOT NULL after FinStatus";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2021-01-2 11:13:01');
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
