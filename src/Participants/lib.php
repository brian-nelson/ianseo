<?php

require_once('Qualification/Fun_Qualification.local.inc.php');
//require_once ('Partecipants/Fun_Partecipants.local.inc.php');
require_once('Common/Fun_FormatText.inc.php');

function saveField($Field, $Value, $EnId, $ToId) {
	global $JSON;
	static $ArrayLue=array('firstname', 'name', 'country_code', 'dob', 'sex', 'status'),
		$ArrayBadge=array('code', 'firstname', 'name', 'country_name', 'country_code', 'division', 'class'),
		$ArrayBackno=array('firstname', 'name', 'country_name', 'country_code', 'division', 'class', 'targetno'),
		$ArrayRecalc=array('division', 'class', 'sex', 'dob', 'country_code');

	if(!$EnId) {
		// TODO: needs to create an Entry!!!
		$JSON['msg']='TODO: need to create an Entry first, file: '.__FILE__.' row '.__LINE__;
		JsonOut($JSON);
	}

	$Updated=false; // needs to recalculate printed items
	$OldControl='';
	if(in_array($Field, $ArrayRecalc)) {
		list($indFEventOld, $teamFEventOld, $countryOld, $divOld, $clOld, $subClOld, $zeroOld)=Params4Recalc($EnId);
	}

	switch($Field) {
		case 'caption':
			if($Value) {
				safe_w_sql("insert into ExtraData set EdId=$EnId, EdType='C', EdExtra=".StrSafe_DB($Value)." on duplicate key update EdExtra=".StrSafe_DB($Value));
			} else {
				safe_w_sql("delete from ExtraData where EdId=$EnId and EdType='C'");
			}
			break;
		case 'locCode':
			if($Value) {
				safe_w_sql("insert into ExtraData set EdId=$EnId, EdType='Z', EdExtra=".StrSafe_DB($Value)." on duplicate key update EdExtra=".StrSafe_DB($Value));
			} else {
				safe_w_sql("delete from ExtraData where EdId=$EnId and EdType='Z'");
			}
			break;
		case 'email':
			if($Value) {
				$Value=strtolower($Value);
				safe_w_sql("insert into ExtraData set EdId=$EnId, EdType='E', EdEmail=".StrSafe_DB($Value)." on duplicate key update EdEmail=".StrSafe_DB($Value));
				if(safe_w_affected_rows()) {
					safe_w_sql("update Entries set EnTimestamp='".date('Y-m-d H:i:s')."' where EnId=$EnId");
				}
			} else {
				safe_w_sql("update ExtraData set EdEmail='' where EdId=$EnId and EdType='E'");
				safe_w_sql("delete from ExtraData where EdEmail='' and EdExtra='' and EdId=$EnId and EdType='E'");
			}
			break;
		case 'status':
			$Value=intval($Value);
			safe_w_sql("update Entries set EnTimestamp=EnTimestamp, EnStatus=".StrSafe_DB($Value)." where EnId=$EnId");
			$Updated=safe_w_affected_rows();
			break;
		case 'firstname':
			$Value=AdjustCaseTitle($Value);
			safe_w_sql("update Entries set EnTimestamp=EnTimestamp, EnFirstName=".StrSafe_DB($Value)." where EnId=$EnId");
			$Updated=safe_w_affected_rows();
			break;
		case 'name':
			$Value=AdjustCaseTitle($Value);
			safe_w_sql("update Entries set EnTimestamp=EnTimestamp, EnName=".StrSafe_DB($Value)." where EnId=$EnId");
			$Updated=safe_w_affected_rows();
			break;
		case 'subclass':
			safe_w_sql("update Entries set EnTimestamp=EnTimestamp, EnSubClass=".StrSafe_DB($Value)." where EnId=$EnId");
			$Updated=safe_w_affected_rows();
			break;
		case 'dob':
			$Value=ConvertDateLoc($Value);
			if($Value) {
				safe_w_sql("update Entries set EnTimestamp=EnTimestamp, EnDob=".StrSafe_DB($Value)." where EnId=$EnId");
				if($Updated=safe_w_affected_rows()) {
					checkAndSetClasses($EnId);
				}
			}
			break;
		case 'country_code':
		case 'country_code2':
		case 'country_code3':
			$Value=mb_convert_case(trim($Value), MB_CASE_UPPER, "UTF-8");
			$q=safe_r_sql("SELECT CoId, CoName FROM Countries WHERE CoCode='$Value' AND CoTournament=$ToId");
			if($r=safe_fetch($q)) {
				$CoId=$r->CoId;
			} else {
				// creates the country
				safe_w_sql("insert into Countries set CoCode=".StrSafe_DB($Value).", CoName=".StrSafe_DB($Value).", CoTournament=$ToId");
				$CoId=safe_w_last_id();
			}
			switch($Field) {
				case 'country_code':
					safe_w_sql("update Entries set EnTimestamp=EnTimestamp, EnCountry=$CoId where EnId=$EnId");
					break;
				case 'country_code2':
					safe_w_sql("update Entries set EnTimestamp=EnTimestamp, EnCountry2=$CoId where EnId=$EnId");
					break;
				case 'country_code3':
					safe_w_sql("update Entries set EnTimestamp=EnTimestamp, EnCountry3=$CoId where EnId=$EnId");
					break;
			}
			$Updated=safe_w_affected_rows();
			break;
		case 'country_name':
		case 'country_name2':
		case 'country_name3':
			if($Value!='USA') {
				$Value=AdjustCaseTitle($Value);
			}
			switch($Field) {
				case 'country_name':
					safe_w_sql("update Countries inner join Entries on CoId=EnCountry and CoTournament=EnTournament set CoName=".StrSafe_DB($Value)." where EnId=$EnId");
					break;
				case 'country_name2':
					safe_w_sql("update Countries inner join Entries on CoId=EnCountry2 and CoTournament=EnTournament set CoName=".StrSafe_DB($Value)." where EnId=$EnId");
					break;
				case 'country_name3':
					safe_w_sql("update Countries inner join Entries on CoId=EnCountry2 and CoTournament=EnTournament set CoName=".StrSafe_DB($Value)." where EnId=$EnId");
					break;
			}
			break;
		case 'code':
			// sets the code and updates the status of the archer from the LueTable if any...
			safe_w_sql("update Entries set EnTimestamp=EnTimestamp, EnCode=".StrSafe_DB($Value)." where EnId=$EnId");
			$Updated=safe_w_affected_rows();

			if($Updated) {
				safe_w_sql("update Entries
					left join LookUpEntries on EnCode=LueCode and EnIocCode=LueIocCode and LueDefault=1
					left join LookUpPaths on EnIocCode=LupIocCode
					set EnStatus=LueStatus, EnLueTimestamp=LupLastUpdate
					where EnId=$EnId");
			}
			break;
		case 'targetno':
			$Value=str_pad($Value, TargetNoPadding+1, '0', STR_PAD_LEFT);
			$q=safe_r_sql("select QuSession, QuTargetNo from Qualifications where QuId=$EnId");
			if($r=safe_fetch($q)) {
				$TargetNo=$r->QuSession.$Value;
				$Target=intval($Value);
				$Letter=substr($Value, -1);
				safe_w_sql("update Qualifications set QuTargetNo=".StrSafe_DB($TargetNo).", QuTarget=$Target, QuLetter=".StrSafe_DB($Letter)." where QuId=$EnId");
				$Updated=safe_w_affected_rows();
			}
			break;
		case 'session':
			$Value=intval($Value);
			$q=safe_r_sql("select QuSession, QuTargetNo, QuLetter, QuTarget from Qualifications where QuId=$EnId");
			if($r=safe_fetch($q)) {
				$TargetNo=$Value.substr($r->QuTargetNo, 1);
				safe_w_sql("update Qualifications set QuSession=$Value, QuTargetNo=".StrSafe_DB($TargetNo)." where QuId=$EnId");
				$Updated=safe_w_affected_rows();
			}
			break;
		case 'sex':
			$Value=intval($Value);
			$Value=min(max(0, $Value), 1);
			safe_w_sql("update Entries set EnTimestamp=EnTimestamp, EnSex=".StrSafe_DB($Value)." where EnId=$EnId");
			if($Updated=safe_w_affected_rows()) {
				checkAndSetClasses($EnId);
			}
			break;
		case 'wc':
			$Value=intval($Value);
			safe_w_sql("update Entries set EnTimestamp=EnTimestamp, EnWChair=".StrSafe_DB($Value)." where EnId=$EnId");
			break;
		case 'division':
			safe_w_sql("update Entries set EnTimestamp=EnTimestamp, EnDivision=".StrSafe_DB($Value)." where EnId=$EnId");
			if($Updated=safe_w_affected_rows()) {
				checkAndSetClasses($EnId);
			}
			break;
		case 'class':
			safe_w_sql("update Entries set EnTimestamp=EnTimestamp, EnClass=".StrSafe_DB($Value)." where EnId=$EnId");
			if($Updated=safe_w_affected_rows()) {
				checkAndSetClasses($EnId);
			}
			break;
		case 'ageclass':
			safe_w_sql("update Entries set EnTimestamp=EnTimestamp, EnAgeClass=".StrSafe_DB($Value)." where EnId=$EnId");
			if($Updated=safe_w_affected_rows()) {
				checkAndSetClasses($EnId);
			}
			break;
		case 'targetface_name':
			safe_w_sql("update Entries set EnTimestamp=EnTimestamp, EnTargetFace=".StrSafe_DB($Value)." where EnId=$EnId");
			break;
	}

	// this is for the change of background
	$JSON['newvalue']=safe_w_affected_rows();


	if($Updated) {
		// updates timestamps
		if(in_array($Field, $ArrayLue)) {
			checkAgainstLUE($EnId);
		}
		if(in_array($Field, $ArrayBadge)) {
			safe_w_sql("update Entries set EnBadgePrinted=0 where EnId=$EnId");
		}
		if(in_array($Field, $ArrayBackno)) {
			safe_w_sql("update Qualifications set QuBacknoPrinted=0 where QuId=$EnId");
			safe_w_sql("update Eliminations set ElBacknoPrinted=0 where ElId=$EnId");
			safe_w_sql("update Individuals set IndBacknoPrinted=0 where IndId=$EnId");
			$q=safe_r_sql("select * from TeamFinComponent where TfcId=$EnId");
			while($r=safe_fetch($q)) {
				safe_w_sql("update Teams set TeBacknoPrinted=0 where TeCoId=$r->TfcCoId and TeSubTeam=$r->TfcSubTeam and TeEvent='$r->TfcEvent' and TeTournament=$r->TfcTournament and TeFinEvent=1");
			}
		}
		if(in_array($Field, $ArrayRecalc)) {
			list($indFEventNew, $teamFEventNew, $countryNew, $divNew, $clNew, $subClNew, $zeroNew)=Params4Recalc($EnId);

			// check and recalculates shootoffs for both old and new division
			RecalculateShootoffAndTeams($ToId, $indFEventOld, $teamFEventOld, $countryOld, $divOld, $clOld, $subClOld, $zeroOld);
			RecalculateShootoffAndTeams($ToId, $indFEventNew, $teamFEventNew, $countryNew, $divNew, $clNew, $subClNew, $zeroNew);

			// rank di classe x tutte le distanze
			$q="SELECT ToNumDist FROM Tournament WHERE ToId={$ToId}";
			$r=safe_r_sql($q);
			$tmpRow=safe_fetch($r);
			for ($i=0; $i<$tmpRow->ToNumDist;++$i) {
				CalcQualRank($i,$divOld.$clOld);
				CalcQualRank($i,$divNew.$clNew);
			}

			// individuale abs
			MakeIndAbs();
		}

	}

	$JSON['value']=$Value;
	$JSON['error']=0;

	// 				switch($Campo) {
	// 					case 'EnDivision':
	// 						$query="SELECT EnDivision FROM Entries WHERE EnId=". StrSafe_DB($Chiave). " AND EnDivision<>" . StrSafe_DB($Value) . " " ;
	// 						$rs=safe_r_sql($query);

	// 						if ($rs && safe_num_rows($rs)==1)
	// 						{
	// 							$recalc=true;

	// 						// prendo le vecchie impostazioni
	// 							$x=Params4Recalc($Chiave);
	// 							if ($x!==false)
	// 							{
	// 								list($indFEventOld,$teamFEventOld,$countryOld,$divOld,$clOld,$zeroOld)=$x;
	// 							}
	// 						}
	// 						break;
	// 					case 'EnName':
	// 					case 'EnFirstName':
	// 						$Value=AdjustCaseTitle($Value);
	// 					case 'CoName':
	// 					case 'CoNameComplete':
	// 						$passValue = $Value;
	// 						break;
	// 					case 'EnIndClEvent':
	// 					case 'EnTeamClEvent':
	// 					case 'EnIndFEvent':
	// 					case 'EnTeamFEvent':
	// 					case 'EnTeamMixEvent':
	// 						$recalc=true;
	// 						break;
	// 					case 'CoParent1':
	// 					case 'CoParent2':
	// 						$searchSQL = "SELECT CoId FROM Countries WHERE CoCode=" . StrSafe_DB(stripslashes($Value)) . " AND CoTournament=".StrSafe_DB($_SESSION['TourId']);
	// 						$rsSearch = safe_r_sql($searchSQL);
	// 						if(safe_num_rows($rsSearch)==1 && $row = safe_fetch($rsSearch))
	// 							$Value=$row->CoId;
	// 						else
	// 							$Value=0;
	// 						break;
	// 				}

	// 				$Update
	// 					= "UPDATE " . $Arr_Tabelle[$Tabella][0]  . " SET "
	// 					. $Campo . "=" . StrSafe_DB(stripslashes($Value)) . " "
	// 					. "WHERE " . $Arr_Tabelle[$Tabella][1] . "=" . StrSafe_DB($Chiave). " ";
	// 				$RsUp=safe_w_sql($Update);
	// 				if(safe_w_affected_rows()) {
	// 					switch($Campo) {
	// 						case 'EnName':
	// 						case 'EnFirstName':
	// 						case 'CoName':
	// 						case 'CoNameComplete':
	// 							safe_w_sql("update Qualifications set QuBacknoPrinted=0 where ". $Arr_Tabelle[$Tabella][1] . "=" . StrSafe_DB($Chiave));
	// 							break;
	// 					}
	// 				}

	// 				if (!$RsUp)
	// 				{
	// 					$Errore=1;
	// 				}

	// 				if (debug)
	// 					print $Update .'<br><br>';

	// 				$Select = "SELECT " . $Campo . " FROM " . $Arr_Tabelle[$Tabella][0] . " WHERE " . $Arr_Tabelle[$Tabella][1] . "=" . StrSafe_DB($Chiave). " ";
	// 				if($Campo=='CoParent1' || $Campo=='CoParent2')
	// 				{
	// 					if($Value!=0)
	// 						$Select = "SELECT CoCode as " . $Campo . " FROM " . $Arr_Tabelle[$Tabella][0] . " WHERE " . $Arr_Tabelle[$Tabella][1] . "=" . StrSafe_DB($Value). " ";
	// 					else
	// 						$Select = "SELECT '' as " . $Campo ;
	// 				}

	// 				$Rs=safe_r_sql($Select);

	// 				if (debug)
	// 					print $Select .'<br><br>';

	// 				if (!$Rs || safe_num_rows($Rs)!=1)
	// 				{
	// 					$Errore=1;
	// 				}
	// 				else
	// 				{
	// 					$Row=safe_fetch($Rs);
	// 					//print '..' . stripslashes($Value) ;
	// 					if ($Row->{$Campo}!=stripslashes($passValue))
		// 					{
	// 						$Errore=1;
	// 					}
	// 					else
	// 					{
	// 						$Value = $Row->{$Campo};
	// 						if ($recalc)
	// 						{
	// 							$x=Params4Recalc($Chiave);
	// 							if ($x!==false)
	// 							{
	// 								list($indFEvent,$teamFEvent,$country,$div,$cl,$zero)=$x;
	// 							}

	// 						// ricalcolo il vecchio e il nuovo
	// 							RecalculateShootoffAndTeams($indFEventOld,$teamFEventOld,$countryOld,$divOld,$clOld,$zeroOld);
	// 							RecalculateShootoffAndTeams($indFEvent,$teamFEvent,$country,$div,$cl,$zero);

	// 						// rank di classe x tutte le distanze
	// 							$q="SELECT ToNumDist FROM Tournament WHERE ToId={$_SESSION['TourId']}";
	// 							$r=safe_r_sql($q);
	// 							$tmpRow=safe_fetch($r);
	// 							for ($i=0; $i<$tmpRow->ToNumDist;++$i)
		// 							{
	// 								CalcQualRank($i,$divOld.$clOld);
	// 								CalcQualRank($i,$div.$cl);
	// 							}

	// 						// individuale abs
	// 							MakeIndAbs();
	// 						}
	// 					}
	// 				}
	// 			}
	// 			else
	// 				$Errore=1;

	// 			print '<response>' . "\n";
	// 			print '<error>' . $Errore . '</error>' . "\n";
	// 			print '<which>' . $Which . '</which>' . "\n";
	// 			print '<value>' . ((($Campo=='CoParent1' || $Campo=='CoParent2') && $passValue='') ? '' : $Value) . '</value>' . "\n";
	// 			print '</response>' . "\n";
	// 		}
	// 	}
	return $Value;
}

function checkAndSetClasses($EnId) {
	global $JSON;
	// check also AgeClass, Class and Div as these may be invalid, in case resets them
	$Age='';
	$Shoot='';
	$q=safe_r_sql("select distinct Age.ClId AgeClass, Shoot.ClId ShootClass, EnAgeClass=Age.ClId as SameAge
			from Entries
			inner join Tournament on EnTournament=ToId
			inner join Classes Age on if(EnDob=0, true, year(ToWhenTo)-year(EnDob) between Age.ClAgeFrom and Age.ClAgeTo) and Age.ClSex in (-1, EnSex) and (Age.ClDivisionsAllowed='' or EnDivision='' or find_in_set(EnDivision, Age.ClDivisionsAllowed)) and Age.ClTournament=EnTournament
			left join Classes Shoot on EnClass=Shoot.ClId and Shoot.ClSex in (-1, EnSex) and find_in_set(Shoot.ClId, Age.ClValidClass) and (Shoot.ClDivisionsAllowed='' or EnDivision='' or find_in_set(EnDivision, Shoot.ClDivisionsAllowed)) and Shoot.ClTournament=EnTournament
			where EnId=$EnId
			order by SameAge desc, ShootClass is null
			");
	if(safe_num_rows($q)==1) {
		// Only one choice, so sets Ageclass and Shooting class
		$r=safe_fetch($q);
		$Age=$r->AgeClass;
		$Shoot=$r->ShootClass;
	} elseif(safe_num_rows($q)) {
		$Valid=false;
		$Ages=array();
		while($r=safe_fetch($q)) {
			if($r->SameAge) {
				$Age=$r->AgeClass;
				if($r->ShootClass) {
					$Shoot=$r->ShootClass;
					$Valid=true;
					break; // no need to go further
				}
			}
			if(empty($Ages[$r->AgeClass])) $Ages[$r->AgeClass]=array();
			if($r->ShootClass) $Ages[$r->AgeClass][$r->ShootClass]=true;
		}
		if(!$Valid and count($Ages)==1) {
			$Age=key($Ages);
			$tmp=current($Ages);
			if(count($tmp)==1) {
				$Shoot=key($tmp);
			}
		}
	}
	safe_w_sql("update Entries set EnTimestamp=EnTimestamp, EnAgeClass=".StrSafe_DB($Age).", EnClass=".StrSafe_DB($Shoot)." where EnId=$EnId");
	if(safe_w_affected_rows()) {
		$JSON['fields']['class']=$Age;
		$JSON['fields']['ageclass']=$Shoot;
	}
}

function Params4Recalc($EnId) {
	$indFEvent=$teamFEvent=$country=$div=$cl=null;

	$q="SELECT EnIndFEvent, EnTeamFEvent, EnCountry, EnDivision, EnClass, EnSubClass, EnStatus, QuScore
		FROM Entries
		INNER JOIN Qualifications ON EnId=QuId
		WHERE EnId={$EnId} ";

	$rs=safe_r_sql($q);

	if (safe_num_rows($rs)==1) {
		$row=safe_fetch($rs);

		$indFEvent=$row->EnIndFEvent;
		$teamFEvent=$row->EnTeamFEvent;
		$country=$row->EnCountry;
		$div=$row->EnDivision;
		$cl=$row->EnClass;
		$subCl=$row->EnSubClass;
		$zero=true;
		if ($row->EnStatus<=1) {
			$zero=($row->QuScore==0);
		}

		return array($indFEvent, $teamFEvent, $country, $div, $cl, $subCl, $zero);
	}

	return false;
}

function RecalculateShootoffAndTeams($TourId, $indFEvent, $teamFEvent, $country, $div, $cl, $subCl, $zero) {
	$Errore=0;

	if ($zero) return 0;

	// scopro se $div e $cl sono per gli atleti
	$q=" SELECT (DivAthlete AND ClAthlete) AS isAth
		FROM Divisions
		INNER JOIN Classes ON DivTournament=ClTournament
		WHERE DivTournament=$TourId AND (DivAthlete AND ClAthlete)=1
			AND DivId=" . StrSafe_DB($div) . " AND ClId=" . StrSafe_DB($cl) . " ";
	//print $q.'<br><br>';
	$rs=safe_r_sql($q);

	if ($rs && safe_num_rows($rs)==1) {
		$queries=array();

		$date=date('Y-m-d H:i:s');

		// shootoff degli individuali a zero (e reset della RankFinal)
		if ($indFEvent==1) {
			$queries[]=" UPDATE Events
				INNER JOIN EventClass ON EvCode=EcCode AND EvTeamEvent='0' AND EvTournament=EcTournament AND EcTournament=$TourId AND EcDivision=" . StrSafe_DB($div) . " AND EcClass=" . StrSafe_DB($cl) . " and if(EcSubClass='', true, EcSubClass='$subCl')
				INNER JOIN Individuals ON EvCode=IndEvent AND EvTournament=IndTournament AND EvTeamEvent=0 AND EvTournament=$TourId
				SET
					EvShootOff='0',
					EvE1ShootOff='0',
					EvE2ShootOff='0',
					IndRankFinal=0,
					IndTimestampFinal='{$date}' ";
		}
		// shootoff dei team a zero
		if ($teamFEvent==1) {
			$queries[]=" UPDATE Events
				INNER JOIN EventClass ON EvCode=EcCode AND EvTeamEvent='1' AND EvTournament=EcTournament AND EcTournament=$TourId AND EcDivision=" . StrSafe_DB($div) . " AND EcClass=" . StrSafe_DB($cl) . " and if(EcSubClass='', true, EcSubClass='$subCl')
				SET
					EvShootOff='0',
					EvE1ShootOff='0',
					EvE2ShootOff='0' ";
		}

		foreach ($queries as $q) {
			$rs=safe_w_sql($q);
		}
		set_qual_session_flags();

		// teams
		if (MakeTeams($country, $div . $cl)) {
			$Errore=1;
		} else {
			if (MakeTeamsAbs($country,$div,$cl)) {
				$Errore=1;
			}
		}
 	}

	return $Errore;
}
