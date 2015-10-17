<?php

function export_tournament($TourId, $Complete=false, $InfoSystem=false) {
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
		'Eliminations' => 'El',
		'Emails' => 'Em',
		'EventClass' => 'Ec',
		'Events' => 'Ev',
		'F2FGrid' => 'F2F',
		'F2FEntries' => 'F2F',
		'F2FFinal' => 'F2F',
		'FinalReportA' => 'Fra',
		'Finals' => 'Fin',
		'FinSchedule' => 'FS',
		'FinTraining' => 'Ft',
		'FinTrainingEvent' => 'Fte',
		'FinWarmup' => 'Fw',
		'GuessWho'=>'Gw',
		'GuessWhoData'=>'Gwd',
		'HhtData' => 'Hd',
		'HhtEvents' => 'He',
		'HhtSetup' => 'Hs',
		'IdCardElements' => 'Ice',
		'IdCards' => 'Ic',
		'Images' => 'Im',
		'Individuals' => 'Ind',
		'ModulesParameters' => 'Mp',
		'PrintOutsRules' => 'Por',
		'RecTournament' => 'Rt',
		'Reviews' => 'Rev',
		'Scheduler'=>'Sch',
		'Session'=>'Ses',
		'SubClass' => 'Sc',
		'TargetFaces' => 'Tf',
		'TargetGroups' => 'Tg',
		'TeamComponent' => 'Tc',
		'TeamFinals' => 'Tf',
		'TeamFinComponent' => 'Tfc',
		'Teams' => 'Te',
		'TournamentDistances' => 'Td',
		'TournamentInvolved' => 'Ti',
		'TourRecords' => 'Tr',
		'TVContents' => 'TVC',
		'TVOut' => 'TV',
		'TVParams' => 'TVP',
		'TVRules' => 'TVR',
		'TVSequence' => 'TVS',
		'VegasAwards'=>'Va',
	);

	if(!$InfoSystem) {
// 		$tabs['']='';
	}

	// Tabs where there is an EnID
	$tabsEnId=array(
			'AccEntries' => 'AEId',
			'Awarded' => 'AwEntry',
			'ElabQualifications' => 'EqId',
			'Eliminations' => 'ElId',
			'ExtraData' => 'EdId',
			'Qualifications' => 'QuId',
			'F2FEntries' => 'F2FEnId',
			'F2FFinal' => 'F2FEnId',
			'Finals' => 'FinAthlete',
			'GuessWhoData'=>array('GwdAthlete1','GwdAthlete2'),
			'HhtData' => 'HdEnId',
			'Individuals' => 'IndId',
			'Photos' => 'PhEnId',
			'Qualifications' => 'QuId',
			'TeamComponent' => 'TcId',
			'TeamFinComponent' => 'TfcId',
			'Vegas'=>'VeId',
	);

	// Tabs where there is an CoID
	$tabsCoId=array(
			'CasTeam' => 'CaTeam',
			'ClubTeam' => 'CTTeam',
			'TeamComponent' => 'TcCoId',
			'TeamFinComponent' => 'TfcCoId',
			'Teams' => 'TeCoId',
	);

	$Gara['Photos']=array();
	$Gara['Flags']=array();
	if($Complete) {
		// Adds localized Flags
		$Select
			= "SELECT distinct Flags.* FROM Entries
					inner join Countries on EnCountry=CoId
					inner join Flags on CoCode=FlCode and FlTournament in (-1, {$TourId})
					WHERE EnTournament={$TourId}
					ORDER BY FlCode ASC, FlTournament DESC";
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
		'TournamentInvolved' => 'TiId',
		'TVOut' => 'TVId',
		);

	// prendo le Tabelle definite nell'array che si estraggono sul Tournament
	foreach($tabs as $tab=>$code) {
		$Gara[$tab]=array();
		$Select
			= "SELECT * FROM $tab WHERE {$code}Tournament=" . StrSafe_DB($TourId) . " ";
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
		// adjust EnId
		foreach($tabsEnId as $tab => $Ids) {
			if(is_array($Ids)) {
				foreach($Ids as $Id) {
					foreach($Gara[$tab] as $k=>$v) {
						if($v[$Id] and !empty($Gara['Entries'][$v[$Id]]['EnOnlineId'])) {
							// the user has an Online ID
							$Gara[$tab][$k][$Id]=$Gara['Entries'][$v[$Id]]['EnOnlineId'];
						} else {
							// unset the record... will be send later on the next cycle
							unset($Gara[$tab][$k]);
						}
					}
				}
			} else {
				foreach($Gara[$tab] as $k=>$v) {
					if($v[$Ids] and !empty($Gara['Entries'][$v[$Ids]]['EnOnlineId'])) {
						// the user has an Online ID
						$Gara[$tab][$k][$Ids]=$Gara['Entries'][$v[$Ids]]['EnOnlineId'];
					} else {
						// unset the record... will be send later on the next cycle
						unset($Gara[$tab][$k]);
					}
				}
			}
		}
		// adjust CoId
		foreach($tabsCoId as $tab => $Id) {
			foreach($Gara[$tab] as $k=>$v) {
				if($v[$Id] and !empty($Gara['Countries'][$v[$Id]]['CoOnlineId'])) {
					// the user has an Online ID
					$Gara[$tab][$k][$Id]=$Gara['Countries'][$v[$Id]]['CoOnlineId'];
				} else {
					// unset the record... will be send later on the next cycle
					unset($Gara[$tab][$k]);
				}
			}
		}
		// adjust ToId
		foreach($tabs as $tab => $Id) {
			foreach($Gara[$tab] as $k=>$v) {
				$Gara[$tab][$k][$Id.'Tournament']=$Gara['Tournament']['ToOnlineId'];
			}
		}

		//adjust Entries
		$NewEntries=array();
		foreach($Gara['Entries'] as $Id => $Entry) {
			if(!$Entry['EnOnlineId']) continue; // skip the new entries
			$Entry['EnId']=$Entry['EnOnlineId'];
			$Entry['EnTournament']=$Gara['Tournament']['ToOnlineId'];
			$Entry['EnCountry']=$Gara['Countries'][$Entry['EnCountry']]['CoOnlineId'];
			$NewEntries[$Entry['EnId']]=$Entry;
		}
		$Gara['Entries']=$NewEntries;

		// adjust Countries
		$NewEntries=array();
		foreach($Gara['Countries'] as $Id => $Entry) {
			if(!$Entry['CoOnlineId']) continue; // skip the new entries
			$Entry['CoId']=$Entry['CoOnlineId'];
			$Entry['CoTournament']=$Gara['Tournament']['ToOnlineId'];
			$NewEntries[$Entry['CoId']]=$Entry;
		}
		$Gara['Countries']=$NewEntries;
	}

	return $Gara;
}
