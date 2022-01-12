<?php

function export_tournament($TourId, $Complete=false, $InfoSystem='') {
	$Gara=array();
	// Inizia prendendo il torneo...
	$Select = "SELECT * FROM Tournament WHERE ToId=" . StrSafe_DB($TourId) . " ";
	$Rs=safe_r_sql($Select);

	if (!$Rs || safe_num_rows($Rs)!=1)
	{
		print get_text('CrackError');
		exit;
	}

	$Gara['Tournament']=safe_fetch_assoc($Rs);

	// prendo le Countries
	$Select = "SELECT * FROM Countries WHERE CoTournament=" . StrSafe_DB($TourId) . " ";
	$Rs=safe_r_sql($Select);
	while($MyRow=safe_fetch_assoc($Rs)){
		$Gara['Countries'][$MyRow['CoId']]=$MyRow;
	}

	// prendo le entries
	$Select = "SELECT * FROM Entries WHERE EnTournament=" . StrSafe_DB($TourId) . " ";
	$Rs=safe_r_sql($Select);
	while($MyRow=safe_fetch_assoc($Rs)){
		$Gara['Entries'][$MyRow['EnId']]=$MyRow;
	}

	// define which keys are not to be exported!
	$NotExportableKeys=array();
	$NotExportableKeys['ModulesParameters'][]="!(MpModule='Mailing' and MpParameter='SmtpServer')";
	$NotExportableKeys['ModulesParameters'][]="!(MpModule='SendToIanseo' and MpParameter='Credentials')";

	$tabs=array(
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
		'Divisions' => 'Div',
		'DistanceInformation' => 'Di',
		'DocumentVersions' => 'Dv',
		'Eliminations' => 'El',
		'Emails' => 'Em',
		'EventClass' => 'Ec',
		'Events' => 'Ev',
		'FinalReportA' => 'Fra',
		'Finals' => 'Fin',
		'FinOdfTiming' => 'FinOdf',
		'FinSchedule' => 'FS',
		'FinWarmup' => 'Fw',
        'GateLog' => 'GL',
		'HeartBeat'=>'Hb',
		'HhtData' => 'Hd',
		'HhtEvents' => 'He',
		'HhtSetup' => 'Hs',
		'IdCardElements' => 'Ice',
		'IdCards' => 'Ic',
		'Images' => 'Im',
		'IskDevices' => 'IskDv',
		'Individuals' => 'Ind',
		'Logs' => 'Log',
		'ModulesParameters' => 'Mp',
// 		'OnLineIds' => 'Oli', // This table is rewritten everytime so no need to export/import it
		'OdfDocuments' => 'OdfDoc',
		'OdfTranslations' => 'OdfTr',
		'OdfMessageStatus' => 'Oms',
		'Rankings' => 'Rank',
		'RecBroken' => 'RecBro',
		'RecTournament' => 'Rt',
		'Reviews' => 'Rev',
		'Scheduler'=>'Sch',
		'Session'=>'Ses',
		'SubClass' => 'Sc',
		'TargetFaces' => 'Tf',
		'TargetGroups' => 'Tg',
		'TeamComponent' => 'Tc',
		'TeamDavis' => 'TeDa',
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
		'VegasAwards'=>'Va',
	);

	//if(!$InfoSystem) {
// 		$tabs['']='';
//	}

	// Tabs where there is an EnID
	$tabsEnId=array(
			'AccEntries' => 'AEId',
			'Awarded' => 'AwEntry',
			'ElabQualifications' => 'EqId',
			'Eliminations' => 'ElId',
			'ExtraData' => 'EdId',
			'Finals' => array('FinAthlete','FinCoach'),
            'GateLog' => 'GLEntry',
			'HhtData' => 'HdEnId',
			'Individuals' => 'IndId',
			'Logs' => 'LogEntry',
			'Photos' => 'PhEnId',
			'Qualifications' => 'QuId',
			'RecBroken' => 'RecBroAthlete',
			'TeamComponent' => 'TcId',
			'TeamFinComponent' => 'TfcId',
			'Vegas'=>'VeId',
	);

	// Tabs where there is an CoID
	$tabsCoId=array(
			'CasTeam' => 'CaTeam',
			'ClubTeam' => 'CTTeam',
			'ExtraDataCountries' => 'EdcId',
			'RecBroken' => 'RecBroTeam',
			'TeamComponent' => 'TcCoId',
			'TeamFinComponent' => 'TfcCoId',
			'TeamFinals' => 'TfTeam',
			'Teams' => 'TeCoId',
			'TournamentInvolved' => 'TiCountry',
	);

	$Gara['Photos']=array();
	$Gara['Flags']=array();
	if($Complete) {
		// Adds localized Flags
		$Select
			= "SELECT distinct Flags.* FROM Entries
					inner join Countries on EnCountry=CoId
					inner join Flags on CoCode=FlCode and FlTournament = {$TourId}
					WHERE EnTournament={$TourId}
					ORDER BY FlCode, FlTournament DESC";
		$Rs=safe_r_sql($Select);
		$oldCode='';
		while($MyRow=safe_fetch_assoc($Rs)){
			if($oldCode!=$MyRow["FlCode"])
				$Gara['Flags'][]=$MyRow;
			$oldCode=$MyRow["FlCode"];
		}

		// and Pictures
		$Select
			= "SELECT Photos.* FROM Photos inner join Entries on Entries.EnId=Photos.PhEnId WHERE Entries.EnTournament=" . StrSafe_DB($TourId) . " ";
		$Rs=safe_r_sql($Select);
		while($MyRow=safe_fetch_assoc($Rs)){
			$Gara['Photos'][]=$MyRow;
		}
	}

	$noIds=array(
		'AccPrice' => 'APId',
		);

	// prendo le Tabelle definite nell'array che si estraggono sul Tournament
	foreach($tabs as $tab=>$code) {
		$Gara[$tab]=array();
		$Select = "SELECT * FROM $tab WHERE {$code}Tournament=" . StrSafe_DB($TourId) . " ";
		if(!empty($NotExportableKeys[$tab])) {
			$Select.= " and ".implode(' AND ', $NotExportableKeys[$tab]);
		}
		$Rs=safe_r_sql($Select);
		while($MyRow=safe_fetch_assoc($Rs)){
			if(isset($noIds[$tab])) unset($MyRow[$noIds[$tab]]);
			$Gara[$tab][]=$MyRow;
		}
	}

	// ExtraData
	$Gara['ExtraData']=array();
	$Select
	= "SELECT ExtraData.* FROM ExtraData inner join Entries on Entries.EnId=ExtraData.EdId WHERE Entries.EnTournament=" . StrSafe_DB($TourId) . " ";
	$Rs=safe_r_sql($Select);
	while($MyRow=safe_fetch_assoc($Rs)){
		$Gara['ExtraData'][]=$MyRow;
	}

	// ExtraDataCountries
	$Gara['ExtraDataCountries']=array();
	$Select
	= "SELECT ExtraDataCountries.* FROM ExtraDataCountries inner join Countries on CoId=EdcId WHERE CoTournament=" . StrSafe_DB($TourId) . " ";
	$Rs=safe_r_sql($Select);
	while($MyRow=safe_fetch_assoc($Rs)){
		$Gara['ExtraDataCountries'][]=$MyRow;
	}

	// ElabQualifications
	$Gara['ElabQualifications']=array();
	$Select
		= "SELECT ElabQualifications.* FROM ElabQualifications inner join Entries on Entries.EnId=ElabQualifications.EqId WHERE Entries.EnTournament=" . StrSafe_DB($TourId) . " ";
	$Rs=safe_r_sql($Select);
	while($MyRow=safe_fetch_assoc($Rs)){
		$Gara['ElabQualifications'][]=$MyRow;
	}

	// Qualifications
	$Gara['Qualifications']=array();
	$Select
		= "SELECT Qualifications.* FROM Qualifications inner join Entries on Entries.EnId=Qualifications.QuId WHERE Entries.EnTournament=" . StrSafe_DB($TourId) . " ";
	$Rs=safe_r_sql($Select);
	while($MyRow=safe_fetch_assoc($Rs)){
		$Gara['Qualifications'][]=$MyRow;
	}

	// Vegas
	$Gara['Vegas']=array();
	$Select
		= "SELECT Vegas.* FROM Vegas inner join Entries on EnId=VeId WHERE EnTournament=" . StrSafe_DB($TourId) . " ";
	$Rs=safe_r_sql($Select);
	while($MyRow=safe_fetch_assoc($Rs)){
		$Gara['Vegas'][]=$MyRow;
	}

	if($InfoSystem) {
		// protects Tournament images
		$Gara['Tournament']['ToImgL']=' '.bin2hex($Gara['Tournament']['ToImgL']);
		$Gara['Tournament']['ToImgR']=' '.bin2hex($Gara['Tournament']['ToImgR']);
		$Gara['Tournament']['ToImgB']=' '.bin2hex($Gara['Tournament']['ToImgB']);
		// gets all data in an array
		$switches=array();
		$q=safe_r_SQL("Select * from OnLineIds where OliServer='$InfoSystem' and OliTournament={$_SESSION['TourId']}");
		while($r=safe_fetch($q)) {
			$switches[$r->OliType][$r->OliId]=$r->OliOnlineId;
		}

		// removes not defined entries/countries
		//adjust Entries
		$NewEntries=array();
		foreach($Gara['Entries'] as $Id => $Entry) {
			if(empty($switches['E'][$Id])
					or empty($switches['C'][$Entry['EnCountry']])
					or (empty($switches['C'][$Entry['EnCountry2']]) and $Entry['EnCountry2'])
					or (empty($switches['C'][$Entry['EnCountry3']]) and $Entry['EnCountry3'])
					) {
				unset($switches['E'][$Id]); // missing entry, deletes the entry
				continue; // skip the new entries
			}
			$Entry['EnId']=$switches['E'][$Id];
			$Entry['EnOnlineId']=$switches['E'][$Id];
			$Entry['EnTournament']=$switches['T'][$_SESSION['TourId']];
			$Entry['EnCountry']=$switches['C'][$Entry['EnCountry']];
			if($Entry['EnCountry2']) $Entry['EnCountry2']=$switches['C'][$Entry['EnCountry2']];
			if($Entry['EnCountry3']) $Entry['EnCountry3']=$switches['C'][$Entry['EnCountry3']];
			$NewEntries[$Entry['EnId']]=$Entry;
		}
		$Gara['Entries']=$NewEntries;

		// adjust Countries
		$NewEntries=array();
		foreach($Gara['Countries'] as $Id => $Entry) {
			if(empty($switches['C'][$Id])) {
				unset($switches['C'][$Id]); // missing country, deletes the entry
				continue; // skip the new entries
			}
			$Entry['CoId']=$switches['C'][$Id];
			$Entry['CoOnlineId']=$switches['C'][$Id];
			$Entry['CoTournament']=$switches['T'][$_SESSION['TourId']];
			$NewEntries[$Entry['CoId']]=$Entry;
		}
		$Gara['Countries']=$NewEntries;

		// adjust EnId
		foreach($tabsEnId as $tab => $Ids) {
			if(is_array($Ids)) {
				foreach($Ids as $Id) {
					foreach($Gara[$tab] as $k=>$v) {
						if($v[$Id] and !empty($switches['E'][$v[$Id]])) {
							// the user has an Online ID
							$Gara[$tab][$k][$Id]=$switches['E'][$v[$Id]];
						} elseif($tab!='Finals') {
							// unset the record... will be send later on the next cycle
							unset($Gara[$tab][$k]);
						}
					}
				}
			} else {
				foreach($Gara[$tab] as $k=>$v) {
					if($v[$Ids] and !empty($switches['E'][$v[$Ids]])) {
						// the user has an Online ID
						$Gara[$tab][$k][$Ids]=$switches['E'][$v[$Ids]];
					} elseif($tab!='Finals' and $tab!='Eliminations') {
						// unset the record... will be send later on the next cycle
						unset($Gara[$tab][$k]);
					}
				}
			}
		}

		// adjust CoId
		foreach($tabsCoId as $tab => $Id) {
			foreach($Gara[$tab] as $k=>$v) {
				if($v[$Id] and !empty($switches['C'][$v[$Id]])) {
					// the user has an Online ID
					$Gara[$tab][$k][$Id]=$switches['C'][$v[$Id]];
				} elseif($tab!='TeamFinals') {
					// unset the record... will be send later on the next cycle
					unset($Gara[$tab][$k]);
				}
			}
		}

		// adjust ToId
		if($Complete) $tabs['Flags']='Fl';
		foreach($tabs as $tab => $Id) {
			foreach($Gara[$tab] as $k=>$v) {
				$Gara[$tab][$k][$Id.'Tournament']=$switches['T'][$_SESSION['TourId']];
			}
		}
	}

	return $Gara;
}
