<?php

function tour_delete($TourId) {
	require_once('Common/CheckPictures.php');
	$TourCode=getCodeFromId($TourId);
	$TableArray=array(
	"AccColors" => "AcTournament",
	"AccEntries" => "AETournament",
	"AccPrice" => "APTournament",
	"ACL" => "AclTournament",
	'AclDetails' => 'AclDtTournament',
	"AvailableTarget" => "AtTournament",
	"Awards" => "AwTournament",
	"Awarded" => "AwTournament",
	"BackNumber" => "BnTournament",
	"BoinxSchedule" => "BsTournament",
	"CasScore"=>"CaSTournament",
	"CasTeam"=>"CaTournament",
	"CasTeamFinal"=>"CTFTournament",
	"CasTeamTarget"=>"CTTTournament",
	"Classes" => "ClTournament",
	"Countries" => "CoTournament",
	"DistanceInformation" => "DiTournament",
	"Divisions" => "DivTournament",
	"Eliminations" => "ElTournament",
	"Emails" => "EmTournament",
	"Entries" => "EnTournament",
	"EventClass" => "EcTournament",
	"Events" => "EvTournament",
	"Individuals" => "IndTournament",
	"F2FGrid"=>"F2FTournament",
	"F2FEntries" => "F2FTournament",
	"F2FFinal" => "F2FTournament",
	"FinalReportA" => "FraTournament",
	"Finals" => "FinTournament",
	"FinSchedule" => "FSTournament",
	"FinTraining" => 'FtTournament',
	"FinTrainingEvent" => 'FteTournament',
	"Flags" => 'FlTournament',
	"GuessWho" => 'GwTournament',
	"GuessWhoData" => 'GwdTournament',
	"HhtData" => "HdTournament",
	"HhtEvents" => "HeTournament",
	"HhtSetup" => "HsTournament",
	"IdCardElements" => "IceTournament",
	"IdCards" => "IcTournament",
	"Images" => "ImTournament",
	"IskDevices" => "IskDvTournament",
	'ModulesParameters' => 'MpTournament',
	'OnLineIds' => 'OliTournament', // This table gets deleted and recreated everytime so no need to import/export it but needs to be deleted for housekeeping!
	'PrintOutsRules' => 'PorTournament',
	'Rankings' => 'RankTournament',
	'RecTournament' => 'RtTournament',
	"Reviews" => "RevTournament",
	"Scheduler" => "SchTournament",
	"Session" => "SesTournament",
	"SubClass" => "ScTournament",
	"TargetFaces" => "TfTournament",
	"TargetGroups" => "TgTournament",
	"TeamComponent" => "TcTournament",
	"TeamFinals" => "TfTournament",
	"TeamFinComponent" => "TfcTournament",
	"Teams" => "TeTournament",
	"Tournament" => "ToId",
	"TournamentDistances" => "TdTournament",
	"TournamentInvolved" => "TiTournament",
	"TourRecords" => "TrTournament",
	"TVContents" => "TVCTournament",
	"TVParams" => "TVPTournament",
	"TVRules" => "TVRTournament",
	"TVSequence" => "TVSTournament",
	"VegasAwards" => "VaTournament",
	);

	foreach($TableArray as $Key=>$Value)
	{
		$Sql = "DELETE FROM " . $Key . " WHERE " . $Value . " = " . StrSafe_DB($TourId);
		safe_w_sql($Sql);
	}
	$Sql = "DELETE FROM Qualifications WHERE QuId NOT IN (SELECT EnId From Entries) ";
	safe_w_sql($Sql);
	$Sql = "DELETE FROM ElabQualifications WHERE EqId NOT IN (SELECT EnId From Entries) ";
	safe_w_sql($Sql);
// 	$Sql = "DELETE FROM Eliminations WHERE ElId NOT IN (SELECT EnId From Entries) ";
// 	safe_w_sql($Sql);
	$Sql = "DELETE FROM Individuals WHERE IndId NOT IN (SELECT EnId From Entries) ";
	safe_w_sql($Sql);
	$Sql = "DELETE FROM Photos WHERE PhEnId NOT IN (SELECT EnId From Entries) ";
	safe_w_sql($Sql);
	$Sql = "DELETE FROM Vegas WHERE VeId NOT IN (SELECT EnId From Entries) ";
	safe_w_sql($Sql);
	$Sql = "DELETE FROM ExtraData WHERE EdId NOT IN (SELECT EnId From Entries) ";
	safe_w_sql($Sql);
	$Sql = "DELETE FROM ExtraDataCountries WHERE EdcId NOT IN (SELECT CoId From Countries) ";
	safe_w_sql($Sql);

	// removea all media
	RemoveMedia($TourCode);
}

function tour_import($filename, $isString=false) {
	require_once('Common/CheckPictures.php');
	// Tabelle che hanno il codice Tournament
	$tabs_on_tour=array(
		'AccColors' => 'Ac',
		'AccEntries' => 'AE',
		'AccPrice' => 'AP',
		'ACL' => 'Acl',
		'AclDetails' => 'AclDt',
		'AvailableTarget' => 'At',
		'Awards' => 'Aw',
		'Awarded' => 'Aw',
		'BackNumber' => 'Bn',
		'CasScore' => 'CaS',
		'CasTeam' => 'Ca',
		'CasTeamFinal' => 'CTF',
		'CasTeamTarget' => 'CTT',
		'Classes' => 'Cl',
		'ClubTeam' => 'CT',
		'ClubTeamScore' => 'CTS',
		'Countries' => 'Co',
		'Divisions' => 'Div',
		'DistanceInformation' => 'Di',
		'DocumentVersions' => 'Dv',
		'Emails' => 'Em',
		'Entries' => 'En',
		'EventClass' => 'Ec',
		'Events' => 'Ev',
		'Individuals' => 'Ind',
		"F2FGrid"=>'F2F',
		"F2FEntries" => "F2F",
		"F2FFinal" => "F2F",
		//"F2FScore" => "F2F",
		//"F2FTarget" => "F2F",
		//"F2FTargetElim" => "F2F",
		'FinalReportA' => 'Fra',
		'Finals' => 'Fin',
		'FinSchedule' => 'FS',
		'FinTraining' => 'Ft',
		'FinTrainingEvent' => 'Fte',
		'FinWarmup' => 'Fw',
		//'Flags' => 'Fl',
		'HhtData' => 'Hd',
		'HhtEvents' => 'He',
		'HhtSetup' => 'Hs',
		'IdCardElements' => 'Ice',
		'IdCards' => 'Ic',
		'Images' => 'Im',
		'IskDevices' => 'IskDv',
		'ModulesParameters' => 'Mp',
// 		'OnLineIds' => 'Oli', // this table gets deleted/inserted if local TourId changes so no need to import/export it
		'Rankings' => 'Rank',
		'RecTournament' => 'Rt',
		'Reviews' => 'Rev',
		'SubClass' => 'Sc',
		'TargetFaces' => 'Tf',
		'TeamComponent' => 'Tc',
		'TeamFinals' => 'Tf',
		'TeamFinComponent' => 'Tfc',
		'Teams' => 'Te',
		'TournamentDistances' => 'Td',
		'TournamentInvolved' => 'Ti',
		'TourRecords' => 'Tr',
		'TVContents' => 'TVC',
		'TVParams' => 'TVP',
		'TVRules' => 'TVR',
		'TVSequence' => 'TVS',
		'Session'=>'Ses',
		'Scheduler'=>'Sch',
		'Eliminations'=>'El',
		'VegasAwards'=>'Va',
	);

	// Tabelle che hanno il codice Countries
	$tab_to_country=array(
		'CasTeam' => 'CaTeam',
		'ClubTeam' => 'CTTeam',
		'Entries' => array('EnCountry','EnCountry2','EnCountry3'),
		'ExtraDataCountries' => 'EdcId',
		'TeamComponent' => 'TcCoId',
		'TeamFinals' => 'TfTeam',
		'TeamFinComponent' => 'TfcCoId',
		'Teams' => 'TeCoId',
		);

	// Tabelle che hanno il codice Entries
	$tab_to_entry=array(
		'AccEntries' => 'AEId',
		'Awarded' => 'AwEntry',
		'ElabQualifications' => 'EqId',
		'Eliminations' => 'ElId',
		'ExtraData' => 'EdId',
		'Individuals' => 'IndId',
		"F2FEntries" => "F2FEnId",
		"F2FFinal" => "F2FEnId",
		'Finals' => 'FinAthlete',
		'HhtData' => 'HdEnId',
		'Photos' => 'PhEnId',
		'Qualifications' => 'QuId',
		'TeamComponent' => 'TcId',
		'TeamFinComponent' => 'TfcId',
		'Vegas' => 'VeId',
		);

	// Tabelle che dipendono SOLO da Tournament
	$tabs_only_tour=array(
		'AccColors',
		'AccPrice',
		'ACL',
		'AclDetails',
		'AvailableTarget',
		'Awards',
		'BackNumber',
		'CasScore',
		'CasTeamFinal',
		'CasTeamTarget',
		'Classes',
		'ClubTeamScore',
		'DistanceInformation',
		'DocumentVersions',
		'Divisions',
		'Emails',
		'EventClass',
		'Events',
		'F2FGrid',
		//"F2FScore",
		//"F2FTarget",
		//"F2FTargetElim",
		'FinalReportA',
		'FinSchedule',
		'FinTraining',
		'FinTrainingEvent',
		'FinWarmup',
		//'Flags',
		'HhtEvents',
		'HhtSetup',
		'IdCardElements',
		'IdCards',
		'Images',
		'ModulesParameters',
// 		'OnLineIds', see comments above
		'Rankings',
		'RecTournament' ,
		'Reviews',
		'SubClass',
		'TargetFaces',
		'TournamentDistances',
		'TournamentInvolved',
		'TourRecords' ,
		'TVContents',
		'TVParams',
		'TVRules',
		'TVSequence',
		'Session',
		'Scheduler',
		'Eliminations',
		'VegasAwards',
	);

	if($isString) {
		$Gara=unserialize(gzuncompress($filename));
	} else {
		$Gara=unserialize(gzuncompress(file_get_contents($filename)));
	}

	// If is not compatible, exits
	if($Gara['Tournament']['ToDbVersion'] > GetParameter('DBUpdate')) {
		return false;
	}

	require_once('UpdateTournament.inc.php');
	$Gara=UpdateTournament($Gara);

	// CONTROLLA SE C'E' UN TORNEO CON LO STESSO CODICE E LO SEGA!
	$q=safe_r_sql("select ToId from Tournament where ToCode=" . strsafe_db($Gara['Tournament']['ToCode']));
	if($r=safe_fetch($q) ){
		// esiste un tournament con lo stesso codice... ranzo tutto!
		tour_delete($r->ToId);
	}

	// elimina i media di questa gara
	RemoveMedia($Gara['Tournament']['ToCode']);

	// Insert competition
	$quer=array();
	foreach($Gara['Tournament'] as $key=>$val) {
		if($key!='ToId'){
			$quer[]="$key=" . strsafe_db($val);
		}
	}
	safe_w_sql("Insert into Tournament set ".implode(', ', $quer));
	$TourId=safe_w_last_id();

	// adjust ToId in all arrays...
	foreach($tabs_on_tour as $tab=>$code) {
		if(isset($Gara[$tab])) {
			foreach($Gara[$tab] as $key=>$val) {
				$Gara[$tab][$key][$code.'Tournament']=$TourId;
			}
		}
	}

	// inserisce le tabelle che hanno SOLO il tournament
	foreach($tabs_only_tour as $tab) {
		if(isset($Gara[$tab])) {
			foreach($Gara[$tab] as $record) {
				$query=array();
				foreach($record as $key=>$val) {
					$query[]="$key = " . strsafe_db($val) ;
				}
				safe_w_sql("insert into $tab set ". implode(', ', $query). " on duplicate key update ". implode(', ', $query));
			}
		}
	}

	// inserisce i paesi e mantieni l'array per il cambio country
	$Countries=array();
	if(array_key_exists('Countries',$Gara) && is_array($Gara['Countries']) && count($Gara['Countries'])>0)
	{
		foreach($Gara['Countries'] as $record) {
			$query=array();
			foreach($record as $key=>$val) {
				if($key!='CoId' and $key!='CoFlag' and $key!='CoMail' and $key!='CoNoPrint'){
					$query[]="$key = " . strsafe_db($val) ;
				}
			}
			safe_w_sql("insert into Countries set ". implode(', ', $query));
			$Countries[$record['CoId']]=safe_w_last_id();
		}
	}
	//aggiorna CoParent1 della Countries stessa
	$tmpSql = "SELECT DISTINCT CoParent1 FROM Countries WHERE CoTournament=" . $TourId . " AND CoParent1!=0";
	$tmpRs = safe_r_sql($tmpSql);
	if(safe_num_rows($tmpRs)!=0)
	{
		while($tmpRow=safe_fetch($tmpRs))
			safe_w_sql("UPDATE Countries SET CoParent1=". $Countries[$tmpRow->CoParent1] . " WHERE CoParent1=" . $tmpRow->CoParent1 . " AND CoTournament=" . $TourId);
		safe_free_result($tmpRs);
	}
	//aggiorna CoParent2 della Countries stessa
	$tmpSql = "SELECT DISTINCT CoParent2 FROM Countries WHERE CoTournament=" . $TourId . " AND CoParent2!=0";
	$tmpRs = safe_r_sql($tmpSql);
	if(safe_num_rows($tmpRs)!=0)
	{
		while($tmpRow=safe_fetch($tmpRs))
		safe_w_sql("UPDATE Countries SET CoParent2=". $Countries[$tmpRow->CoParent2] . " WHERE CoParent2=" . $tmpRow->CoParent2 . " AND CoTournament=" . $TourId);
		safe_free_result($tmpRs);
	}

	// aggiorna i paesi nelle tabelle che ne fanno uso
	foreach($tab_to_country as $tab=>$field) {
		if(array_key_exists($tab, $Gara))
		{
			foreach($Gara[$tab] as $key=>$record) {
				if(is_array($field)) {
					foreach($field as $ff) {
						if(array_key_exists($ff,$record) && $record[$ff])
							$Gara[$tab][$key][$ff]=$Countries[$record[$ff]];
					}
				} else {
					if($record[$field] && array_key_exists($record[$field],$Countries))
						$Gara[$tab][$key][$field]=$Countries[$record[$field]];
				}
			}
		}
	}

	// inserisce le Entries e mantieni l'array per il cambio Entry
	$Entries=array();
	if(array_key_exists('Entries',$Gara) && is_array($Gara['Entries']) && count($Gara['Entries'])>0)
	{
		foreach($Gara['Entries'] as $record) {
			$query=array();
			foreach($record as $key=>$val) {
				if($key!='EnId' and $key!='EnWorldRank'){
					$query[]="$key = " . strsafe_db($val) ;
				}
			}
			safe_w_sql("insert into Entries set ". implode(', ', $query));
			$Entries[$record['EnId']]=safe_w_last_id();
		}
	}
	// aggiorna le rimanenti tabelle con le Entries corrette
	foreach($tab_to_entry as $tab=>$field) {
		if(array_key_exists($tab, $Gara))
		{
			foreach($Gara[$tab] as $key=>$record) {
				if(array_key_exists($record[$field], $Entries) && $record[$field]) $Gara[$tab][$key][$field]=$Entries[$record[$field]];
			}
		}
	}

	// inserisce le tabelle restanti
	unset($tab_to_country['Entries']);
	$final_tabs = array_unique(array_merge(array_keys($tab_to_country), array_keys($tab_to_entry)));
	foreach($final_tabs as $tab) {
		if(array_key_exists($tab, $Gara))
		{
			foreach($Gara[$tab] as $record) {
				$query=array();
				foreach($record as $key=>$val) {
					$query[]="$key = " . strsafe_db($val) ;
				}

				// attenzione: questa query era "commentata" per non far apparirer errori:
				// se questo comportamento è desiderato, chiamare la funzione safe_w_sql passando
				// come terzo parametro un array con gli errori numerici ammessi
				safe_w_sql("REPLACE INTO $tab set ". implode(', ', $query));
			}
		}
	}

	//Gestisce la tabella Flags
	if(array_key_exists('Flags',$Gara) && is_array($Gara['Flags']) && count($Gara['Flags'])>0)
	{
		foreach($Gara['Flags'] as $record) {
			if($record['FlTournament']!=-1)
				$record['FlTournament']=$TourId;
			$query=array();
			foreach($record as $key=>$val) {
				$query[]="$key = " . strsafe_db($val) ;
			}
			safe_w_sql("insert into Flags set ". implode(', ', $query). " on duplicate key update ". implode(', ', $query));
		}
	}

// 	// Manage OnLineIds table
// 	foreach($Entries as $Org => $New) {
// 		safe_w_sql("update OnLineIds set OliId=$New where OliTournament=$TourId and OliType='E' and OliId=$Org");
// 	}
// 	foreach($Countries as $Org => $New) {
// 		safe_w_sql("update OnLineIds set OliId=$New where OliTournament=$TourId and OliType='C' and OliId=$Org");
// 	}
// 	safe_w_sql("update OnLineIds set OliId=$TourId where OliTournament=$TourId and OliType='T'");

	// if for accreditation, inserts all the LocCodes!
	if(!empty($_REQUEST['Accreditation'])) {
		safe_w_sql("insert ignore into ExtraData (select EnId, 'Z', '', concat(EnCode, '-', EnIocCode, '-', EnDivision) from Entries where EnTournament=$TourId)");
	}

	// RECREATES ALL MEDIA
	CheckPictures($Gara['Tournament']['ToCode']);

	// check if this is an active Accreditation Booth
	if(GetParameter('AccBoothActive') and in_array($Gara['Tournament']['ToCode'], explode(', ', GetParameter('AccBoothCodes')))) {
		require_once('Modules/AccreditationBooth/Lib.php');
		// delete all the log files of this competition...
		deleteAccLogs($Gara['Tournament']['ToCode']);
		// track info for this competition in all servers ready to track
		$q=safe_r_sql("select distinct SsApi, SsName from ianseo_Accreditation.SyncServers where SsStatus=1");
		while ($r=safe_fetch($q)) {
			startAccTrack($Gara['Tournament']['ToCode'], $r->SsName, $r->SsApi);
		}
	}

	return ($TourId);
}
