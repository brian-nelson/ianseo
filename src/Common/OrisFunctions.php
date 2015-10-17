<?php
error_reporting(E_ALL);
/**
 * These functions are helpers to get all the necessary data into an object
 * that will be used to generate the ORIS PDFs or to be sent online to generate
 * the online results
 */

// first of all get the "forcing" option of the tournament!
DefineForcePrintouts($_SESSION['TourId']);

require_once('Common/StartListQueries.php');
require_once('Common/FinalQueries.php');
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/XmlCreationFunctions.php');
require_once('Common/Lib/Fun_DateTime.inc.php');
require_once('Common/Fun_Phases.inc.php');
require_once('Qualification/Fun_Qualification.local.inc.php');

function getPdfHeader() {
	$RET=new StdClass();

	$Sql = "SELECT ToCode, ToLocRule, ToName, ToComDescr, ToWhere, ".
		"date_format(ToWhenFrom, '".get_text('DateFmtDB')."') as ToWhenFrom, date_format(ToWhenTo, '".get_text('DateFmtDB')."') as ToWhenTo," .
	// riga di patch
		"ToWhenFrom AS DtFrom,ToWhenTo AS DtTo," .
		"(ToImgL) as ImgL, (ToImgR) as ImgR, (ToImgB) as ImgB, ToGolds AS TtGolds, ToXNine AS TtXNine,ToGoldsChars,ToXNineChars, " .
		"ToPrintPaper, ToPrintChars, ToCurrency, ToPrintLang " .
		"FROM Tournament   WHERE ToId = " . StrSafe_DB($_SESSION['TourId']);
	$Rs=safe_r_sql($Sql);
	$r=safe_fetch($Rs);

	$RET->Code		= $r->ToCode;
	$RET->Name		= $r->ToName;
	$RET->Oc		= $r->ToComDescr;
	$RET->Where	= $r->ToWhere;
	$RET->WhenF	= $r->ToWhenFrom;
	$RET->WhenT	= $r->ToWhenTo;
	$RET->imgL		= $r->ImgL;
	$RET->imgR		= $r->ImgR;
	$RET->imgB		= $r->ImgB;
	$RET->prnGolds = $r->TtGolds;
	$RET->prnXNine = $r->TtXNine;
	$RET->goldsChars = $r->ToGoldsChars;
	$RET->xNineChars = $r->ToXNineChars;
	$RET->docUpdate=date('Ymd.His');
	$RET->LocalRule=$r->ToLocRule;

	// patch
	$RET->DtFrom=$r->DtFrom;
	$RET->DtTo=$r->DtTo;
	$RET->ProgramVersion = ProgramVersion;
	$RET->ProgramBuild = (defined('ProgramBuild') ? ' (' . ProgramBuild . ')' : '');
	$RET->ProgramRelease = ProgramRelease;
	$RET->TournamentDate2String = TournamentDate2String($RET->WhenF, $RET->WhenT);
	$RET->Continue=get_text('Continue');
	$RET->LegendSO=get_text('LegendSO','Tournament');
	$RET->CoinTossShort=get_text('CoinTossShort','Tournament');
	$RET->CoinToss=get_text('CoinToss','Tournament');
	$RET->ShotOffShort=get_text('ShotOffShort','Tournament');
	$RET->ShotOff=get_text('ShotOff','Tournament');
	$RET->LegendStatus=get_text('LegendStatus','Tournament');
	$RET->Partecipation=get_text('Partecipation');
	$RET->IndQual=get_text('IndQual', 'Tournament');
	$RET->IndFin=get_text('IndFin', 'Tournament');
	$RET->TeamQual=get_text('TeamQual', 'Tournament');
	$RET->TeamFin=get_text('TeamFin', 'Tournament');
	$RET->MixedTeamFinEvent=get_text('MixedTeamFinEvent', 'Tournament');
	$RET->Yes=get_text('Yes');
	$RET->No=get_text('No');

	// ---

	if($r->ToPrintPaper) {
		$RET->PageSize = 'LETTER';
	}
	switch($r->ToPrintChars) {
		case 0:		 // helvetica & standard european fonts
			$RET->FontStd='helvetica';
			break;
		case 1:
			$RET->FontStd='dejavusans';
			$RET->FontFix='freemono';
			break;
		case 2:
			// This font is more chinese friendly -- by uian2000@gmail.com
			$RET->FontStd='droidsansfallback';
			$RET->FontFix='droidsansfallback';
			break;
	}

	if(is_null($r->ToCurrency)) {
		$RET->Currency = '€';
	} else {
		$RET->Currency = $r->ToCurrency;
	}

	$RET->StaffCategories=array();

	$Select="
		SELECT ti.*, it.*,IF(ItJudge!=0,'CatJudge',IF(ItDoS!=0,'CatDos',IF(ItJury!=0,'CatJury','CatOC'))) AS `Category`
		FROM TournamentInvolved AS ti LEFT JOIN InvolvedType AS it ON ti.TiType=it.ItId
		WHERE ti.TiTournament={$_SESSION['TourId']} AND it.ItId IS NOT NULL
		ORDER BY IF(ItJudge!=0,1,IF(ItDoS!=0,2,IF(ItJury!=0,3,4))) ASC, IF(ItJudge!=0,ItJudge,IF(ItDoS!=0,ItDoS,IF(ItJury!=0,ItJury,ItOC))) ASC,ti.TiName ASC
	";
	$Rs=safe_r_sql($Select);

	while($MyRow = safe_fetch($Rs)) {
		$RET->StaffCategories[get_text($MyRow->Category,'Tournament')][] = $MyRow->TiName;
	}
	foreach($RET->StaffCategories as $cat => $members) $RET->StaffCategories[$cat] = implode(', ', $members);

	// and now the pictures of the countries
	$RET->Flags=array();
	$query="select distinct"
		. " FlCode, FlJPG "
		. "from Entries"
		//. " inner join Countries on CoId in (EnCountry,EnCountry2)"
		. " inner join Countries on CoId in (EnCountry,EnCountry2,EnCountry3)"
		. " inner join Flags on CoCode=FlCode and FlTournament in (-1,EnTournament) "
		. "where FlJPG>'' and EnTournament={$_SESSION['TourId']} "
		. "order by FlTournament desc";
	$q=safe_r_sql($query);
	while($r=safe_fetch($q)) {
		if(!empty($RET->Flags[$r->FlCode])) continue;
		$im=imagecreatefromstring(base64_decode($r->FlJPG));
		// MUST be at most 20 in height
		$imgx=ceil(20*imagesx($im)/imagesy($im));

		$im2=imagecreatetruecolor($imgx, 20);

		if(!imagecopyresampled($im2, $im, 0, 0, 0, 0, $imgx, 20, imagesx($im), imagesy($im))) continue;

		if(!imagetruecolortopalette($im2, false, 255)) continue;

		$file=tempnam('/tmp', 'img');
		imagegif($im2, $file);
		$RET->Flags[$r->FlCode]=file_get_contents($file);
		unlink($file);
	}

	return $RET;
}

function getStartList($ORIS='', $Event='', $Elim=false) {
	$Data=new StdClass();

	$Data->Code='C51A';
	$Data->Order=($Elim ? '0' : '1');
	$Data->Description='Start List by Target';
	$Data->Header=array("Target","Name","NOC","Country","Date of Birth");
	$Data->HeaderWidth=array(15,50,15,50,60);
	$Data->Phase='Qualification Round';
	$Data->Continue=get_text('Continue');
	$Data->TournamentDate2String=TournamentDate2String($_SESSION['TourWhenFrom'], $_SESSION['TourWhenTo']);

	$Data->Data=array();

	$Data->Data['Fields']=array(
		"SesName"=>get_text('SessionDescr', 'Tournament'),
		"EvCode"=>get_text('EvCode'),
		"DivDescription"=>get_text('Division'),
		"ClDescription"=>get_text('Class'),
		"Bib"=>get_text('Code', 'Tournament'),
		"Athlete"=>get_text('Name', 'Tournament'),
		"Session"=>get_text('Session'),
		"TargetNo"=>get_text('Target'),
		"NationCode"=>get_text('Country'),
		"Nation"=>get_text('Nation'),
		"EventCode"=>get_text('EvCode'),
		"EventName"=>get_text('Event'),
		"DOB"=>get_text('DOB', 'Tournament'),
		"SesAth4Target"=>get_text('Ath4Target', 'Tournament'),
		"ClassCode"=>get_text('Class'),
		"DivCode"=>get_text('Division'),
		"AgeClass"=>get_text('AgeCl'),
		"SubClass"=>get_text('SubClass', 'Tournament'),
		"Status"=>get_text('Status', 'Tournament'),
		"IC"=>'IC',
		"TC"=>'TC',
		"IF"=>'IF',
		"TF"=>'TF',
		"TM"=>'TM',
		"NationCode2"=>get_text('Country'),
		"Nation2"=>get_text('Nation'),
		"NationCode3"=>get_text('Country'),
		"Nation3"=>get_text('Nation'),
		"EnSubTeam"=>'EnSubTeam',
		"TargetFace"=>get_text('TargetType'),
		"Poule"=>get_text('Poule', 'Tournament'),
		);

	if($ORIS) {
		$Data->Data['Fields']['TargetNo']='Target';
		$Data->Data['Fields']['Athlete']='Name';
		$Data->Data['Fields']['NationCode']='NOC';
		$Data->Data['Fields']['Nation']='Country';
		$Data->Data['Fields']['EventName']='Category';
		if($Elim and $Event) {
			$Data->Description.=' Round '. $Event;
		}
	} else {
		$Data->Data['Fields']['Athlete']=get_text('Athlete');
		$Data->HideCols = GetParameter("IntEvent");
		$Data->BisTarget = false;
		$Data->NumEnd = 0;

		$RsTour=safe_r_sql("SELECT (ToElabTeam!=0) as BisTarget, ToNumEnds AS TtNumEnds "
			. "FROM Tournament "
			. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']));
		if ($r=safe_fetch($RsTour)) {
			$Data->BisTarget = $r->BisTarget;
			$Data->NumEnd = $r->TtNumEnds;
		}
		$Data->Description=get_text('StartlistSession','Tournament');
	}

	$MyQuery = getStartListQuery($ORIS, $Event, $Elim);

	//echo $MyQuery;exit;
	$Rs=safe_r_sql($MyQuery);
	while ($MyRow=safe_fetch($Rs)) {
		$MyRow->EventName=get_text($MyRow->EventName,'','',true);
		if(!$Elim) {
			$MyRow->DivDescription=get_text($MyRow->DivDescription,'','',true);
			$MyRow->ClDescription=get_text($MyRow->ClDescription,'','',true);
		} else {
			if ($MyRow->SesName!='') {
				$MyRow->SesName=$MyRow->SesName . ' (' . get_text('Eliminations_' . ($MyRow->Session+1)) . ')';
			} else {
				$MyRow->SesName=get_text('Eliminations_' . ($MyRow->Session+1));
			}

		}
		$Data->Data['Items'][]=$MyRow;
	}

	return $Data;
}

function getStatEntriesByEvent($ORIS='') {
	$Data=new StdClass();
	$Data->Code='C30A';
	$Data->Order='2';
	$Data->Description='Number of Entries by Event';
	$Data->Continue=get_text('Continue');
	$Data->Data=array();

	if($ORIS) {
		// Individuals
		$Data->Header=array("Event","No. Athletes","No. Countries","No. Teams");
		$Data->HeaderWidth=array(60, 40, 40, 40);
		$MyQuery = getStatEntriesByEventQuery('IF');
		$Rs=safe_r_sql($MyQuery);
		while ($Row=safe_fetch($Rs)) {
			$Data->Data[$Row->Code]=array(
				'Name' => $Row->EventName,
				'Number' => $Row->Quanti,
				'Countries' => $Row->Countries,
				'Teams' => 0
				);
		}

		// Teams
		$Teams=array();

		$MyQuery = getStatEntriesByEventQuery('TF');
		$RsEv=safe_r_sql($MyQuery);
		while($MyRowEv=safe_fetch($RsEv)) {
			$Sql = "SELECT DISTINCT EcCode, EcTeamEvent, EcNumber FROM EventClass WHERE EcCode=" . StrSafe_DB($MyRowEv->EvCode) . " AND EcTeamEvent!=0 AND EcTournament=" . StrSafe_DB($_SESSION['TourId']);
			$RsEc=safe_r_sql($Sql);
			if(safe_num_rows($RsEc)>0) {
				$RuleCnt=0;
				$Sql = "Select * ";
				while($MyRowEc=safe_fetch($RsEc)) {
					$ifc=ifSqlForCountry($MyRowEv->EvTeamCreationMode);
					$Sql .= (++$RuleCnt == 1 ? "FROM ": "INNER JOIN ");
					$Sql .= "(SELECT {$ifc} as C" . $RuleCnt . ", SUM(IF(EnSubTeam=0,1,0)) AS QuantiMulti
					  FROM Entries
					  INNER JOIN EventClass ON EnClass=EcClass AND EnDivision=EcDivision AND EnTournament=EcTournament AND EcTeamEvent=" . $MyRowEc->EcTeamEvent . " AND EcCode=" . StrSafe_DB($MyRowEc->EcCode) . "
							  WHERE {$ifc}<>0 AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnTeam" . ($MyRowEv->EvMixedTeam ? 'Mix' : 'F') ."Event=1
							  		group by {$ifc}, EnSubTeam
							  		HAVING COUNT(EnId)>=" . $MyRowEc->EcNumber . ") as sqy";
					$Sql .= ($RuleCnt == 1 ? " ": $RuleCnt . " ON C1=C". $RuleCnt . " ");
				}

				$Rs=safe_r_sql($Sql);
				$tmpQuanti=safe_num_rows($Rs);
				$Countries=$tmpQuanti;
				if($MyRowEv->EvMultiTeam!=0) {
					$tmpQuanti = 0;
					while($tmpRow=safe_fetch($Rs)) {
						$Countries++;
						$tmpQuanti += intval($tmpRow->QuantiMulti / $MyRowEv->EvMaxTeamPerson);
					}
				}
				$tmpSaved=valueFirstPhase($MyRowEv->FirstPhase)==$MyRowEv->FirstPhase ? 0 : 8;
				$tmpQuantiIn = maxPhaseRank($MyRowEv->FirstPhase);
				$tmpQuantiOut = $tmpQuanti-$tmpQuantiIn;
				$tmpMatch = (min($tmpQuantiIn,$tmpQuanti) -$tmpSaved)-$MyRowEv->FirstPhase;
				$tmpBye = $MyRowEv->FirstPhase-$tmpMatch;
				$Teams[$MyRowEv->EvCode]=array(
						'Name' => $MyRowEv->EventName,
						'Number' => $tmpQuanti,
						'Countries' => $Countries,
						);
			}
		}
		foreach($Teams as $EvCode => $Items) {
			if(empty($Data->Data[$EvCode])) {
				$Data->Data[$EvCode]=array(
					'Name' => $Items['Name'],
					'Number' => 0,
					'Countries' => $Items['Countries'],
					'Teams' => $Items['Number']
					);
			} else {
				$Data->Data[$EvCode]['Teams']=$Items['Number'];
			}
		}
	} else {
		// Start with Qualification Rounds
		$MyQuery = getStatEntriesByEventQuery('QR');
		$QR=array();
		$QR['Title'] = get_text('StatEvents','Tournament');
		$QR['SubTitle'] = array(get_text('Individual'), get_text('Team'));
		$QR['Div'] = array();
		$QR['Cls'] = array();
		$QR['Data']=array();
		$Rs=safe_r_sql($MyQuery);
		while ($Row=safe_fetch($Rs)) {
			if(!in_array($Row->Divisione, $QR['Div'])) {
				$QR['Div'][] = $Row->Divisione;
			}
			if(!in_array($Row->Classe, $QR['Cls'])) {
				$QR['Cls'][] = $Row->Classe;
			}
			$QR['Data'][$Row->Divisione][$Row->Classe]=array($Row->QuantiInd,$Row->QuantiSq);
		}
		$Data->Data['QR']=$QR;

		// Go with Individual Finals
		$MyQuery = getStatEntriesByEventQuery('IF');
		$QR=array();
		$QR['Title'] = get_text('IndFinal');
		$QR['SubTitle'] = array(
				get_text('EvName'),
				get_text('Athletes'),
				get_text('FirstPhase'),
				get_text('FirstPhaseMatchesBye','Tournament'),
				get_text('FirstPhaseInOut','Tournament')
				);
		$QR['Data']=array();
		$Rs=safe_r_sql($MyQuery);
		while ($Row=safe_fetch($Rs)) {
			$tmpSaved=valueFirstPhase($Row->FirstPhase)==$Row->FirstPhase ? 0 : 8;
			$tmpQuantiIn = maxPhaseRank($Row->FirstPhase);
			$tmpQuantiOut = $Row->Quanti-$tmpQuantiIn;
			$tmpMatch = (min($tmpQuantiIn,$Row->Quanti) -$tmpSaved)-$Row->FirstPhase;
			$tmpBye = $Row->FirstPhase-$tmpMatch;

			$QR['Data'][$Row->Code]=array(
				'Name' => $Row->EventName,
				'FirstPhase' => $Row->FirstPhase,
				'Number' => $Row->Quanti,
				'Invalid' => ($tmpMatch<=0),
				'Phase' => $Row->FirstPhase==0 ? "" : get_text(namePhase($Row->FirstPhase,$Row->FirstPhase).'_Phase'),
				'Matches' => $Row->FirstPhase==0 ? "" : $tmpMatch,
				'Byes' => ($Row->FirstPhase==0  || $tmpMatch<0 ? "" : (($tmpBye + $tmpSaved)==0 ? '' : '(' . $tmpBye . ($tmpSaved!=0 ? '+' . $tmpSaved : '') . ')')),
				'ArchersIn' => $Row->FirstPhase==0 ? "" : ($Row->Quanti < $tmpQuantiIn ? $Row->Quanti : $tmpQuantiIn),
				'ArchersOut' => $Row->FirstPhase==0 ? "" : ($tmpQuantiOut>0 ? '(' . $tmpQuantiOut . ')' : '-----'),
				);
		}
		$Data->Data['IF']=$QR;

		// Go with Team Finals
		$QR=array();
		$QR['Title']=get_text('TeamFinal');
		$QR['SubTitle']=array(
			get_text('EvName'),
			get_text('MixedTeamEvent'),
			get_text('Teams'),
			get_text('FirstPhase'),
			get_text('FirstPhaseMatchesBye','Tournament'),
			get_text('FirstPhaseInOut','Tournament'),
			);
		$QR['Data']=array();
		$MyQuery = getStatEntriesByEventQuery('TF');
		$RsEv=safe_r_sql($MyQuery);
		while($MyRowEv=safe_fetch($RsEv)) {
			$Sql = "SELECT DISTINCT EcCode, EcTeamEvent, EcNumber FROM EventClass WHERE EcCode=" . StrSafe_DB($MyRowEv->EvCode) . " AND EcTeamEvent!=0 AND EcTournament=" . StrSafe_DB($_SESSION['TourId']);
			$RsEc=safe_r_sql($Sql);
			if(safe_num_rows($RsEc)>0) {
				$RuleCnt=0;
				$Sql = "Select * ";
				while($MyRowEc=safe_fetch($RsEc)) {
					$ifc=ifSqlForCountry($MyRowEv->EvTeamCreationMode);
					$Sql .= (++$RuleCnt == 1 ? "FROM ": "INNER JOIN ");
					$Sql .= "(SELECT {$ifc} as C" . $RuleCnt . ", SUM(IF(EnSubTeam=0,1,0)) AS QuantiMulti
					  FROM Entries
					  INNER JOIN EventClass ON EnClass=EcClass AND EnDivision=EcDivision AND EnTournament=EcTournament AND EcTeamEvent=" . $MyRowEc->EcTeamEvent . " AND EcCode=" . StrSafe_DB($MyRowEc->EcCode) . "
						  WHERE {$ifc}<>0 AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnTeam" . ($MyRowEv->EvMixedTeam ? 'Mix' : 'F') ."Event=1
						  group by {$ifc}, EnSubTeam
						  HAVING COUNT(EnId)>=" . $MyRowEc->EcNumber . ") as sqy";
					$Sql .= ($RuleCnt == 1 ? " ": $RuleCnt . " ON C1=C". $RuleCnt . " ");
				}

				$Rs=safe_r_sql($Sql);
				$tmpQuanti=safe_num_rows($Rs);
				if($MyRowEv->EvMultiTeam!=0) {
					$tmpQuanti = 0;
					while($tmpRow=safe_fetch($Rs)) {
						$tmpQuanti += intval($tmpRow->QuantiMulti / $MyRowEv->EvMaxTeamPerson);
					}
				}
				$tmpSaved=valueFirstPhase($MyRowEv->FirstPhase)==$MyRowEv->FirstPhase ? 0 : 8;
				$tmpQuantiIn = maxPhaseRank($MyRowEv->FirstPhase);
				$tmpQuantiOut = $tmpQuanti-$tmpQuantiIn;
				$tmpMatch = (min($tmpQuantiIn,$tmpQuanti) -$tmpSaved)-$MyRowEv->FirstPhase;
				$tmpBye = $MyRowEv->FirstPhase-$tmpMatch;
				$QR['Data'][$MyRowEv->EvCode]=array(
					'Name' => $MyRowEv->EventName,
					'Number' => $tmpQuanti,
					'Invalid' => ($tmpMatch<=0),
					'FirstPhase' => $MyRowEv->FirstPhase==0 ? "" : get_text(namePhase($MyRowEv->FirstPhase,$MyRowEv->FirstPhase).'_Phase'),
					'Matches' => $MyRowEv->FirstPhase==0 ? "" : $tmpMatch,
					'Byes' => $MyRowEv->FirstPhase==0  || $tmpMatch<0 ? "" : '(' . $tmpBye . ($tmpSaved!=0 ? '+' . $tmpSaved : '') . ')',
					'ArchersIn' => $MyRowEv->FirstPhase==0 ? "" : ($tmpQuanti < $tmpQuantiIn ? $tmpQuanti : $tmpQuantiIn),
					'ArchersOut' => $MyRowEv->FirstPhase==0 ? "" : ($tmpQuantiOut>0 ? '(' . $tmpQuantiOut . ')' : '---'),
					'MixedTeam' => get_text($MyRowEv->EvMixedTeam ? 'Yes' : 'No'),
					);
			}
		}
		$Data->Data['TF']=$QR;
	}

	return $Data;
}

function getStatEntriesByCountries($ORIS='', $Athletes=false) {
	$Data=new StdClass();
	$Data->Code='C30A';
	$Data->Order='2';
	$Data->Description='Number of Entries by Country';
	$Data->Header=array("NOC","Men#","Women#","Total\nCompetitors#","","Officials#", "Total#");
	$Data->HeaderWidth=array(array(10,40),15,15,25,10,15,15,5);
	$Data->Phase='';
	$Data->StatCountries=get_text('StatCountries','Tournament');
	$Data->Continue=get_text('Continue');
	$Data->TotalShort=get_text('TotalShort','Tournament');
	$Data->Total=get_text('Total');
	$Data->Data=array();

	$MyQuery = getStatEntriesByCountriesQuery($ORIS, $Athletes);
	$Rs=safe_r_sql($MyQuery);

	while ($info = safe_fetch_field($Rs)) {
		$Data->Data['Fields'][$info->name] = $info->name;
	}
	$Data->Data['Fields']['NationCode'] = get_text('CountryCode');
	$Data->Data['Fields']['NationName'] = get_text('Country');


	while ($MyRow=safe_fetch($Rs)) {
		$Data->Data['Items'][$MyRow->NationCode]=$MyRow;
	}

	return $Data;
}

function getStartListByCountries($ORIS=false, $Athletes=false, $orderByName=false) {
	$Data=new StdClass();

	$Data->Code='C32E';
	$Data->Order='2';
	$Data->Description='Entries by Country';
	$Data->Header=array("NOC","Country","Name","Date of Birth   #", "Back No.", "Event");
	$Data->HeaderWidth=array(10,40,40,35,20,45);
	$Data->Phase='';
	$Data->Data=array();

	$Data->Data['Fields'] = array(
		"Bib" => get_text('Code','Tournament'),
		"Athlete" => get_text('Athlete'),
		"Session" => get_text('SessionShort','Tournament'),
		"SesName" => get_text('Session'),
		"TargetNo" => get_text('Target'),
		'Nation' => get_text('Country'),
		'ClDescription' => get_text('Class'),
		'DivDescription' => get_text('Division'),
		'AgeClass' => get_text('AgeCl'),
		'SubClass' =>get_text('SubCl','Tournament'),
		'Status' => get_text('Status','Tournament'),
		'EventName' => get_text('Event'),
		'TargetFace' => get_text('TargetType'),

	);

	if($ORIS) {
		$Data->Data['Fields']['TargetNo']='Target';
		$Data->Data['Fields']['Athlete']='Name';
		$Data->Data['Fields']['NationCode']='NOC';
		$Data->Data['Fields']['Nation']='Country';
		$Data->Data['Fields']['EventName']='Event';
		$Data->Data['Fields']['Session']='Session';
	} else {
		$Data->HideCols = GetParameter("IntEvent");
		$Data->Description=get_text('StartlistCountry','Tournament');
	}
	$MyQuery = getStartListCountryQuery($ORIS, $Athletes, $orderByName);

	//echo $MyQuery;exit;
	$Rs=safe_r_sql($MyQuery);
	while ($MyRow=safe_fetch($Rs)) {
		if(!empty($MyRow->EventName)) $MyRow->EventName=get_text($MyRow->EventName,'','',true);
		$MyRow->DivDescription=get_text($MyRow->DivDescription,'','',true);
		$MyRow->ClDescription=get_text($MyRow->ClDescription,'','',true);
		$Data->Data['Items'][$MyRow->NationCode][]=$MyRow;
	}

	return $Data;
}

function getCountriesList($ORIS='') {
	$Data=new StdClass();

	$Data->Code='C32E';
	$Data->Order='2';
	$Data->Description='List of Countries';
	$Data->Header=array("", "NOC","Country");
	$Data->HeaderWidth=array(10,20,160);
	$Data->Phase='';
	$Data->Data=array();

	$Data->Data['Fields'] = array(
		'Nation' => get_text('Country'),
		);

	if($ORIS) {
		$Data->Data['Fields']['NationCode']='NOC';
	} else {
		$Data->Description=get_text('ListCountries','Tournament');
	}

	$MyQuery = getCountryList($ORIS);

	//echo $MyQuery;exit;
	$Rs=safe_r_sql($MyQuery);
	while ($MyRow=safe_fetch($Rs)) {
		$Data->Data['Items'][$MyRow->NationCode][]=$MyRow;
	}

	return $Data;
}

function getStartListAlphabetical($ORIS='') {
	$Data=new StdClass();

	$Data->Code='C32B';
	$Data->Order='3';
	$Data->Description='Entries by Name';
	$Data->Header=array("Name","NOC","Country","Date of Birth", "Back No.", "Event");
	$Data->HeaderWidth=array(50,10,35,30,20,45);
	$Data->Phase='';
	$Data->Continue=get_text('Continue');
	$Data->Data=array();

	$Data->Data['Fields'] = array(
		'SesName' => get_text('Session'),
		'Athlete' => get_text('Athlete'),
		'Bib' => get_text('Code','Tournament'),
		"Session" => get_text('SessionShort','Tournament'),
		'TargetNo' => get_text('Target'),
		'Nation' => get_text('Country'),
		'NationCode' => get_text('Country'),
		'AgeClass' => get_text('AgeCl'),
		'SubClass' => get_text('SubCl','Tournament'),
		'DivDescription' => get_text('Division'),
		'ClDescription' => get_text('Class'),
		'Category' => get_text('Event'),
		'Status' => get_text('Status', 'Tournament'),
		'TargetFace' => get_text('TargetType'),
		);

	if($ORIS) {
		$Data->Data['Fields']['Athlete'] = "Name";
		$Data->Data['Fields']['TargetNo'] = "Target";
		$Data->Data['Fields']['NationCode'] = "NOC";
		$Data->Data['Fields']['Nation'] = "Country";
		$Data->Data['Fields']['Category'] = "Event";
		$Data->Data['Fields']['SesName'] = "Session";
	} else {
		$Data->HideCols = GetParameter("IntEvent");
		$Data->Description=get_text('StartlistAlpha','Tournament');
	}
	$MyQuery = getStartListAlphaQuery($ORIS);

	$OldLetter='';
	$Group=0;

	//echo $MyQuery;exit;
	$Rs=safe_r_sql($MyQuery);
	while ($MyRow=safe_fetch($Rs)) {
		if($OldLetter != $MyRow->FirstLetter) {
			$Group++;
			$OldLetter = $MyRow->FirstLetter;
		}

		$MyRow->EventName=get_text($MyRow->EventName,'','',true);
		$Data->Data['Items'][$Group][]=$MyRow;

	}

	return $Data;
}


function getStartListCategory($ORIS=false,$orderByTeam=0) {
	$Data=new StdClass();

	$Data->Code='C32B';
	$Data->Order='3';
	$Data->Description='Entries by Category';
	$Data->Header=array("Name","NOC","Country","Date of Birth", "Back No.", "Event");
	$Data->HeaderWidth=array(50,10,35,30,20,45);
	$Data->Phase='';
	$Data->Continue=get_text('Continue');
	$Data->Data=array();

	$Data->Data['Fields'] = array(
			'SesName' => get_text('Session'),
			'Athlete' => get_text('Athlete'),
			'Bib' => get_text('Code','Tournament'),
			"Session" => get_text('SessionShort','Tournament'),
			'TargetNo' => get_text('Target'),
			'Nation' => get_text('Country'),
			'NationCode' => get_text('Country'),
			'AgeClass' => get_text('AgeCl'),
			'SubClass' => get_text('SubCl','Tournament'),
			'DivDescription' => get_text('Division'),
			'ClDescription' => get_text('Class'),
			'Category' => get_text('Event'),
			'Status' => get_text('Status', 'Tournament'),
			'TargetFace' => get_text('TargetType'),
	);

	if($ORIS) {
		$Data->Data['Fields']['Athlete'] = "Name";
		$Data->Data['Fields']['TargetNo'] = "Target";
		$Data->Data['Fields']['NationCode'] = "NOC";
		$Data->Data['Fields']['Nation'] = "Country";
		$Data->Data['Fields']['Category'] = "Event";
		$Data->Data['Fields']['SesName'] = "Session";
	} else {
		$Data->HideCols = GetParameter("IntEvent");
		$Data->Description=get_text('StartlistAlpha','Tournament');
	}
	$MyQuery = getStartListCategoryQuery($ORIS,$orderByTeam);

	$OldCategory='';
	$Group=0;

	//echo $MyQuery;exit;
	$Rs=safe_r_sql($MyQuery);
	while ($MyRow=safe_fetch($Rs)) {
		if($OldCategory != $MyRow->EventCode) {
			$Group++;
			$OldCategory = $MyRow->EventCode;
		}

		$MyRow->EventName=get_text($MyRow->EventName,'','',true);
		$Data->Data['Items'][$Group][]=$MyRow;

	}

	return $Data;
}

function getDivClasIndividual($Div='', $Clas='', $Options=array()) {
	foreach($Options as $k => $v) $_REQUEST[$k]=$v;

	$Data=new StdClass();

	$Data->Order='1';
	$Data->HideCols = GetParameter("IntEvent");
	$Data->hideGolds = (getTournamentType()==14);
	$Data->NumberThousandsSeparator = get_text('NumberThousandsSeparator');
	$Data->Description=get_text('ResultIndClass','Tournament');
	$Data->Continue=get_text('Continue');
	$Data->TotalShort=get_text('TotalShort','Tournament');

	$options=array('dist'=>0);
	if(isset($_REQUEST["Event"]))
		$options['events'] = $_REQUEST["Event"];
	if(isset($_REQUEST["MaxNum"]) && is_numeric($_REQUEST["MaxNum"]))
		$options['cutRank'] = $_REQUEST["MaxNum"];
	if(isset($_REQUEST["ScoreCutoff"]) && is_numeric($_REQUEST["ScoreCutoff"]))
		$options['cutScore'] = $_REQUEST["ScoreCutoff"];
	if(isset($_REQUEST["Classes"]))
	{
		if(is_array($_REQUEST["Classes"]))
			$options['cls'] = $_REQUEST["Classes"];
		else
			$options['cls'] = array($_REQUEST["Classes"]);
	}
	if(isset($_REQUEST["Divisions"]))
	{
		if(is_array($_REQUEST["Divisions"]))
			$options['divs'] = $_REQUEST["Divisions"];
		else
			$options['divs'] = array($_REQUEST["Divisions"]);
	}
	if($Div) $options['divs'] = array($Div);
	if($Clas) $options['cls'] = array($Clas);

	$family='DivClass';
	if(!empty($_REQUEST["distEnable"]) && isset($_REQUEST["atDist"]) && intval($_REQUEST["atDist"]))
		$options['dist'] = $_REQUEST["atDist"];
	elseif(!empty($_REQUEST["distEnable"]) && isset($_REQUEST["runningDist"]) && intval($_REQUEST["runningDist"]))
		$options['runningDist'] = $_REQUEST["runningDist"];
	elseif(!empty($_REQUEST["Snapshot"]))
	{
		$options['subFamily'] = $family;
		$family = 'Snapshot';
		if(!empty($_REQUEST["SnapshotArrNo"]))
			$options['arrNo'] = $_REQUEST["SnapshotArrNo"];
		else
			$options['arrNo'] = 0;
	}
	elseif(!empty($_REQUEST["SubClassRank"]))
	{
		$family='SubClass';
		if(!empty($_REQUEST["SubClassDivRank"])) $options['joinDivs']=true;
		if(!empty($_REQUEST["SubClassClassRank"])) $options['joinCls']=true;
		if(!empty($_REQUEST["ShowAwards"])) $options['showAwards'] = true;
	}

	$Data->family=$family;

	$rank=Obj_RankFactory::create($family,$options);
	$rank->read();
	$Data->rankData=$rank->getData();

	return $Data;
}

function getDivClasTeam($Div='', $Clas='') {
	$Data=new StdClass();

	$Data->Order='1';
	$Data->HideCols = GetParameter("IntEvent");
	$Data->hideGolds = (getTournamentType()==14);
	$Data->NumberThousandsSeparator = get_text('NumberThousandsSeparator');
	$Data->Description=get_text('ResultSqClass','Tournament');
	$Data->Continue=get_text('Continue');
	$Data->TotalShort=get_text('TotalShort','Tournament');

	$options=array();

	if(isset($_REQUEST["Event"]))
		$options['events'] = $_REQUEST["Event"];
	if(isset($_REQUEST["MaxNum"]) && is_numeric($_REQUEST["MaxNum"]))
		$options['cutRank'] = $_REQUEST["MaxNum"];
	if(isset($_REQUEST["ScoreCutoff"]) && is_numeric($_REQUEST["ScoreCutoff"]))
		$options['cutScore'] = $_REQUEST["ScoreCutoff"];
	if(isset($_REQUEST["Classes"]))
	{
		if(is_array($_REQUEST["Classes"]))
			$options['cls'] = $_REQUEST["Classes"];
		else
			$options['cls'] = array($_REQUEST["Classes"]);
	}
	if(isset($_REQUEST["Divisions"]))
	{
		if(is_array($_REQUEST["Divisions"]))
			$options['divs'] = $_REQUEST["Divisions"];
		else
			$options['divs'] = array($_REQUEST["Divisions"]);
	}

	if($Div) $options['divs'] = array($Div);
	if($Clas) $options['cls'] = array($Clas);

	$rank=Obj_RankFactory::create('DivClassTeam',$options);
	$rank->read();
	$Data->rankData=$rank->getData();

	return $Data;
}

function getQualificationIndividual($EventRequested='', $ORIS=false) {
	$Data=new StdClass();

	$Data->Code='C73A';
	$Data->Order='1';
	$Data->Description='Results';
	$Data->ShotOffShort=get_text('ShotOffShort','Tournament');
	$Data->ShootOffArrows=get_text('ShootOffArrows','Tournament');
	$Data->Judge=get_text('Judge','Tournament');
	$Data->Winner=get_text('Winner');
	$Data->TargetShort=get_text('TargetShort','Tournament');
	$Data->CoinTossShort=get_text('CoinTossShort','Tournament');
	$Data->NumberThousandsSeparator = get_text('NumberThousandsSeparator');
	$Data->NumberDecimalSeparator = get_text('NumberDecimalSeparator');
	$Data->Continue=get_text('Continue');
	$Data->TotalShort=get_text('TotalShort','Tournament');

	if(!$ORIS) {
		$Data->Description=get_text('ResultIndAbs','Tournament');
	}

	$options=array('dist'=>0);
	if(isset($_REQUEST["Event"]) && $_REQUEST["Event"][0]!=".")
		$options['events'] = $_REQUEST["Event"];
	if(isset($_REQUEST["MaxNum"]) && is_numeric($_REQUEST["MaxNum"]))
		$options['cutRank'] = $_REQUEST["MaxNum"];
	$family='Abs';
	if($EventRequested) $options['events']=$EventRequested;

	if(!empty($_REQUEST["distEnable"]) && isset($_REQUEST["atDist"]) && intval($_REQUEST["atDist"]))
		$options['dist'] = $_REQUEST["atDist"];
	elseif(!empty($_REQUEST["distEnable"]) && isset($_REQUEST["runningDist"]) && intval($_REQUEST["runningDist"]))
		$options['runningDist'] = $_REQUEST["runningDist"];
	elseif(!empty($_REQUEST["Snapshot"]))
	{
		$options['subFamily'] = $family;
		$family = 'Snapshot';
		if(!empty($_REQUEST["SnapshotArrNo"]))
			$options['arrNo'] = $_REQUEST["SnapshotArrNo"];
		else
			$options['arrNo'] = 0;
	}

	$Data->family=$family;

	$rank=Obj_RankFactory::create($family,$options);
	$rank->read();
	$Data->rankData=$rank->getData();

	return $Data;
}

function getEliminationIndividual($EventRequested='', $ORIS=false) {
	$Data=new StdClass();

	$Data->Code='C73A';
	$Data->Order='1';
	if(!$ORIS) {
		$Data->Description=get_text('Elimination');
	} else {
		$Data->Description='Results';
	}
	$Data->NumberThousandsSeparator = get_text('NumberThousandsSeparator');
	$Data->NumberDecimalSeparator = get_text('NumberDecimalSeparator');
	$Data->ShotOffShort=get_text('ShotOffShort','Tournament');
	$Data->CoinTossShort=get_text('CoinTossShort','Tournament');
	$Data->Continue=get_text('Continue');

	$options=array();
	if(isset($_REQUEST["Event"]))
		$options['eventsR'] = $_REQUEST["Event"];
	if($EventRequested) {
		if(is_array($EventRequested)) {
			$options['events']=$EventRequested;
		} else {
			$options['events']=array($EventRequested);
		}
	}

	$family='ElimInd';

	$rank=Obj_RankFactory::create($family,$options);
	$rank->read();
	$Data->rankData=$rank->getData();

	return $Data;
}

function getQualificationTeam($EventRequested='', $ORIS=false) {
	$Data=new StdClass();

	$Data->Code='C73B';
	$Data->Order='1';
	$Data->Description='Results';
	$Data->ShotOffShort=get_text('ShotOffShort','Tournament');
	$Data->CoinTossShort=get_text('CoinTossShort','Tournament');
	$Data->NumberThousandsSeparator = get_text('NumberThousandsSeparator');
	$Data->NumberDecimalSeparator = get_text('NumberDecimalSeparator');
	$Data->Continue=get_text('Continue');
	$Data->TotalShort=get_text('TotalShort','Tournament');
	$Data->ShootOffArrows=get_text('ShootOffArrows','Tournament');
	$Data->Judge=get_text('Judge','Tournament');
	$Data->Winner=get_text('Winner');
	$Data->TargetShort=get_text('TargetShort','Tournament');

	if(!$ORIS) {
		$Data->Description=get_text('ResultSqClass','Tournament');
	}

	$options=array();
	if(isset($_REQUEST["Event"]) && $_REQUEST["Event"][0]!=".")
		$options['events'] = $_REQUEST["Event"];
	if(isset($_REQUEST["MaxNum"]) && is_numeric($_REQUEST["MaxNum"]))
		$options['cutRank'] = $_REQUEST["MaxNum"];
	if($EventRequested) $options['events']=$EventRequested;

	$rank=Obj_RankFactory::create('AbsTeam',$options);
	$rank->read();
	$Data->rankData=$rank->getData();

	return $Data;
}

function getBracketsIndividual($EventRequested='', $ORIS=false, $ShowTargetNo=true, $ShowSchedule=true, $ShowSetArrows=true) {
	$Data=new StdClass();

	$Data->Code='C75B';
	$Data->Description='Result Brackets';
	$Data->Final=get_text('0_Phase');
	$Data->Bronze=get_text('1_Phase');
	$Data->Bye=get_text('Bye');
	$Data->Events=array();
	$Data->ShowTargetNo = $ShowTargetNo;
	$Data->ShowSchedule = $ShowSchedule;
	$Data->ShowSetArrows= $ShowSetArrows;

	if(!$ORIS) {
		$Data->Description=get_text('BracketsInd');
	}

	$options=array();
	if($EventRequested) $options['events']=$EventRequested;

	$rank=Obj_RankFactory::create('GridInd',$options);
	$rank->read();
	$Data->rankData=$rank->getData();
	$Data->LastUpdate=$Data->rankData['meta']['lastUpdate'];

	return $Data;
}

function getRankingIndividual($EventRequested='', $ORIS=false) {
	$Data=new StdClass();

	$Data->Code='C74A';
	$Data->Description='Results Summary';
	$Data->Phase="";
	$Data->Bye=get_text('Bye');
	$Data->Elim1=get_text('Eliminations_1');
	$Data->Elim2=get_text('Eliminations_2');
	$Data->NumberThousandsSeparator = get_text('NumberThousandsSeparator');
	$Data->NumberDecimalSeparator = get_text('NumberDecimalSeparator');
	$Data->Continue=get_text('Continue');

	if(!$ORIS) {
		$Data->Description=get_text('RankingInd');
	}

	$options=array();
	if(isset($_REQUEST["Event"]) && $_REQUEST["Event"][0]!=".")
		$options['eventsR'] = $_REQUEST["Event"];
	if($EventRequested) $options['eventsR']=$EventRequested;

	$rank=Obj_RankFactory::create('FinalInd',$options);
	$rank->read();
	$Data->rankData=$rank->getData();
	$Data->LastUpdate=$Data->rankData['meta']['lastUpdate'];
	return $Data;
}

function getRankingTeams($EventRequested='', $ORIS=false) {
	$Data=new StdClass();

	$Data->Code='C74B';
	$Data->Description='Results Summary';
	$Data->Phase="";
	$Data->NumberThousandsSeparator = get_text('NumberThousandsSeparator');
	$Data->NumberDecimalSeparator = get_text('NumberDecimalSeparator');
	$Data->Bye=get_text('Bye');

	if(!$ORIS) {
		$Data->Description=get_text('RankingSq');
	}

	$options=array();
	if(isset($_REQUEST["Event"]) && $_REQUEST["Event"][0]!=".")
		$options['eventsR'] = $_REQUEST["Event"];
	if($EventRequested) $options['eventsR']=$EventRequested;

	$rank=Obj_RankFactory::create('FinalTeam',$options);
	$rank->read();
	$Data->rankData=$rank->getData();
	$Data->LastUpdate=$Data->rankData['meta']['lastUpdate'];

	return $Data;
}

function getBracketsTeams($EventRequested='', $ORIS=false, $ShowTargetNo=true, $ShowSchedule=true, $ShowSetArrows=true) {
	$Data=new StdClass();

	$Data->Code='C75C';
	$Data->Description='Result Brackets';
	$Data->Phase="Final Round";
	$Data->Final=get_text('0_Phase');
	$Data->Bronze=get_text('1_Phase');
	$Data->Bye=get_text('Bye');
	$Data->ShowTargetNo = $ShowTargetNo;
	$Data->ShowSchedule = $ShowSchedule;
	$Data->ShowSetArrows= $ShowSetArrows;

	if(!$ORIS) {
		$Data->Description=get_text('BracketsSq');
	}

	$options=array();
	if($EventRequested) $options['events']=$EventRequested;

	$rank=Obj_RankFactory::create('GridTeam',$options);
	$rank->read();
	$Data->rankData=$rank->getData();
	$Data->LastUpdate=$Data->rankData['meta']['lastUpdate'];

	return $Data;
}

function getMedalList($ORIS=false) {
	$Data=new StdClass();

	$Data->Code='C93';
	$Data->Description='Medallists by Event';
	$Data->Phase="";
	$Data->Order="0";
	$Data->LastUpdate='';
	$Data->EvName=get_text('EvName');
	$Data->TourWhen=get_text('TourWhen','Tournament');
	$Data->Medal=get_text('Medal');
	$Data->Athlete=get_text('Athlete');
	$Data->Country=get_text('Country');
	for($n=0; $n<7; $n++) $Data->{'DayOfWeek_'.$n} = get_text('DayOfWeek_'.$n);
	for($n=0; $n<12; $n++) $Data->{'Month_'.$n} = get_text('Month_'.$n);
	$Data->Medal_1=get_text('MedalGold');
	$Data->Medal_2=get_text('MedalSilver');
	$Data->Medal_3=get_text('MedalBronze');


	$rank=Obj_RankFactory::create('MedalList',array());
	$rank->read();
	$Data->rankData=$rank->getData();
	$Data->LastUpdate=$Data->rankData['lastUpdate'];

	return $Data;
}

function getMedalStand($ORIS=false) {
	$Data=new StdClass();

	$Data->Code='C95';
	$Data->Description='Medal Standings';
	$Data->Phase="";
	$Data->Order="0";
	$Data->LastUpdate='';
	$Data->EvName=get_text('EvName');
	$Data->TourWhen=get_text('TourWhen','Tournament');
	$Data->Medal=get_text('Medal');
	$Data->Athlete=get_text('Athlete');
	$Data->Country=get_text('Country');
	for($n=0; $n<7; $n++) $Data->{'DayOfWeek_'.$n} = get_text('DayOfWeek_'.$n);
	for($n=0; $n<12; $n++) $Data->{'Month_'.$n} = get_text('Month_'.$n);
	$Data->Medal_1=get_text('MedalGold');
	$Data->Medal_2=get_text('MedalSilver');
	$Data->Medal_3=get_text('MedalBronze');
	$Data->Rank = get_text('Rank');
	$Data->Individual = get_text('Individual');
	$Data->Team = get_text('Team');
	$Data->Total = get_text('Total');


	if(!$ORIS) $Data->Description=get_text('MedalStanding');

	$options=array();
	if(isset($_REQUEST["Event"]) && $_REQUEST["Event"][0]!=".")
		$options['eventsR'] = $_REQUEST["Event"];
	$options['cutRank']=3;

	$rank=Obj_RankFactory::create('FinalInd',$options);
	$rank->read();
	if(empty($Data->Ind))
		$Data->Ind = new StdClass();
	$Data->Ind->rankData=$rank->getData();
	$Data->LastUpdate=$Data->Ind->rankData['meta']['lastUpdate'];

	$CountryList=array();
	$colTots=array();
	$colRank=array();

	$rankData=$rank->getData();

	$tmp=new StdClass();
	$tmp->I[1]=0;
	$tmp->I[2]=0;
	$tmp->I[3]=0;
	$tmp->T[1]=0;
	$tmp->T[2]=0;
	$tmp->T[3]=0;
	$tmp->U[1]=0;
	$tmp->U[2]=0;
	$tmp->U[3]=0;

	foreach($rankData['sections'] as $Event => $section) {
		if($section['meta']['medals']) {
			foreach($section['items'] as $item) {
				if($item['rank']!=0) {
					if(empty($CountryList[$item['countryCode']])) {
						$CountryList[$item['countryCode']] = clone $tmp;
						$CountryList[$item['countryCode']]->Name = $item['countryName'];
						$colTots[$item['countryCode']]=0;
					}
					$CountryList[$item['countryCode']]->I[$item['rank']]++;
					$CountryList[$item['countryCode']]->U[$item['rank']]++;

					$colTots[$item['countryCode']]++;
					$colRank[$item['countryCode']] = 0;
				}
			}
		}
	}

	$options=array();
	if(isset($_REQUEST["Event"]) && $_REQUEST["Event"][0]!=".")
		$options['eventsR'] = $_REQUEST["Event"];
	$options['cutRank']=3;

	$rank=Obj_RankFactory::create('FinalTeam',$options);
	$rank->read();
	$rankData=$rank->getData();
	foreach($rankData['sections'] as $Event => $section) {
		if($section['meta']['medals']) {
			foreach($section['items'] as $item) {
				if($item['rank']!=0) {
					if(empty($CountryList[$item['countryCode']])) {
						$CountryList[$item['countryCode']] = clone $tmp;
						$CountryList[$item['countryCode']]->Name = $item['countryName'];
						$colTots[$item['countryCode']]=0;
					}
					$CountryList[$item['countryCode']]->T[$item['rank']]++;
					$CountryList[$item['countryCode']]->U[$item['rank']]++;

					$colTots[$item['countryCode']]++;
					$colRank[$item['countryCode']] = 0;
				}
			}
		}
	}

	uasort($CountryList, 'standComp');
	arsort($colTots);

	$MyRank=0;
	$MyPos=0;
	$TmpOldValue=-1;
	foreach($colTots as $key=>$value) {
		$MyPos++;
		if($TmpOldValue != $value) {
			$MyRank=$MyPos;
			$TmpOldValue = $value;
		}
		$colRank[$key]=$MyRank;
	}

	$Data->CountryList=$CountryList;
	$Data->colTots=$colTots;
	$Data->colRank=$colRank;

	return $Data;
}

function standComp($a, $b) {
	if($a->U[1]>$b->U[1]) return -1;
	if($a->U[1]<$b->U[1]) return 1;
	if($a->U[2]>$b->U[2]) return -1;
	if($a->U[2]<$b->U[2]) return 1;
	if($a->U[3]>$b->U[3]) return -1;
	if($a->U[3]<$b->U[3]) return 1;
	return 0;
}

function DefineForcePrintouts($TourId) {
	$q=safe_r_SQL("select ToPrintLang from Tournament where ToId=$TourId");
	$r=safe_fetch($q);
	@define('PRINTLANG', $r->ToPrintLang);
}

function getScoQuals() {
	$_REQUEST["GetScorecardAsString"]=true;
	require_once('Qualification/PDFScore.php');
}