<?php
/**
 * These functions are helpers to get all the necessary data into an object
 * that will be used to generate the ORIS PDFs or to be sent online to generate
 * the online results
 */

// first of all get the "forcing" option of the tournament!
require_once('Common/Lib/CommonLib.php');
DefineForcePrintouts($_SESSION['TourId']);

require_once('Common/StartListQueries.php');
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/XmlCreationFunctions.php');
require_once('Common/Lib/Fun_DateTime.inc.php');
require_once('Common/Fun_Phases.inc.php');
require_once('Qualification/Fun_Qualification.local.inc.php');

function getPdfHeader($ForOnline=true) {
	$RET=new StdClass();

	$Sql = "SELECT ToCode, ToLocRule, ToType, ToTypeSubRule, ToName, ToComDescr, ToWhere, ToTimeZone, 
		date_format(ToWhenFrom, '".get_text('DateFmtDB')."') as ToWhenFrom, date_format(ToWhenTo, '".get_text('DateFmtDB')."') as ToWhenTo,
		ToWhenFrom AS DtFrom,ToWhenTo AS DtTo,
		(ToImgL) as ImgL, (ToImgR) as ImgR, (ToImgB) as ImgB, ToGolds AS TtGolds, ToXNine AS TtXNine,ToGoldsChars,ToXNineChars,
		ToPrintPaper, ToPrintChars, ToCurrency, ToPrintLang 
		FROM Tournament   WHERE ToId = " . StrSafe_DB($_SESSION['TourId']);
	$Rs=safe_r_sql($Sql);
	$r=safe_fetch($Rs);

	$RET->TzOffset	= $r->ToTimeZone;
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
	$RET->LocalType=$r->ToType;
	$RET->LocalSubRule=$r->ToTypeSubRule;

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
    $RET->ToPrintChars = $r->ToPrintChars;
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
        case 3:
            // This font is more japanese friendly
            $RET->FontStd='arialuni';
            $RET->FontFix='arialuni';
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
		$RET->StaffCategories[get_text($MyRow->Category,'Tournament')][] = $MyRow->TiName . ' ' . $MyRow->TiGivenName;
	}
	foreach($RET->StaffCategories as $cat => $members) $RET->StaffCategories[$cat] = implode(', ', $members);

	// if not requested to send online we skip the following point
	if(!$ForOnline) {
		return $RET;
	}

	// and now the pictures of the countries
	$RET->Flags=array();
	$query="select FlCode, FlJPG 
		from Flags
		inner join Countries on CoCode=FlCode and CoTournament={$_SESSION['TourId']}
		inner join Entries on EnTournament={$_SESSION['TourId']} and CoId in (EnCountry,EnCountry2,EnCountry3) 
		where FlTournament in (-1, {$_SESSION['TourId']}) and FlJPG>'' 
		group by FlCode, FlTournament
		order by FlTournament desc";
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

function getStartList($ORIS='', $Event='', $Elim=false, $Filled=false, $isPool=false, $BySchedule=false) {
	$Data=new StdClass();

	$Data->Code='C51A';
	$Data->Order=($Elim ? '0' : '1');
	$Data->Description='Start List by Target';
	$Data->Header=array("Target","Name","NOC","Country","#W. Rank    ", "Date of Birth");
	$Data->HeaderPool=array("Target","Name","NOC","Country", "Points", "#W. Rank    ", "Date of Birth");

	$Data->Phase='Qualification Round';
	$Data->OdfCodes=array();
	$Data->IndexName='Start List by Target';
	$Data->HeaderWidth=array(15,50,15,45,15,55);
	$Data->HeaderWidthPool=array(15,50,15,35,20,15,45);
	$Data->Continue=get_text('Continue');
	$Data->TournamentDate2String=TournamentDate2String($_SESSION['TourWhenFrom'], $_SESSION['TourWhenTo']);

	$Data->Data=array();

	$Data->Data['Fields']=array(
		"SesName"=>get_text('SessionDescr', 'Tournament'),
		"EvCode"=>get_text('EvCode'),
		"DivDescription"=>get_text('Division'),
		"ClDescription"=>get_text('Class'),
		'Category' => get_text('DivisionClass'),
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
		"Schedule_Points"=>get_text('Schedule', 'Tournament').' / '.get_text('Points', 'Tournament'),
		);

	if($ORIS) {
		$Data->Data['Fields']['TargetNo']='Target';
		$Data->Data['Fields']['Athlete']='Name';
		$Data->Data['Fields']['NationCode']='NOC';
		$Data->Data['Fields']['Nation']='Country';
		$Data->Data['Fields']['EventName']='Category';
		if($Elim and $Event and !is_array($Event)) {
			$Data->Description.=' Round '. $Event;
		}
	} else {
		$Data->Description=get_text('StartListbyTarget', 'Tournament');
		$Data->Header=array(get_text('Target'), get_text('Name', 'Tournament'), get_text('Country'), get_text('Nation'), get_text('DOB', 'Tournament'));
		$Data->Phase=get_text('QualRound');
		$Data->IndexName=get_text('StartListbyTarget', 'Tournament');
		$Data->Data['Fields']['Athlete']=get_text('Athlete');
		$Data->HideCols = GetParameter("IntEvent");
		$Data->BisTarget = false;
		$Data->NumEnd = 0;
		$Data->Description=get_text('StartlistSession','Tournament');
	}

	$RsTour=safe_r_sql("SELECT (ToElabTeam!=0) as BisTarget, ToNumEnds AS TtNumEnds, (select max(RankRanking) as IsRanked from Rankings where RankTournament={$_SESSION['TourId']}) as IsRanked
		FROM Tournament
		WHERE ToId=" . StrSafe_DB($_SESSION['TourId']));
	if ($r=safe_fetch($RsTour)) {
		$Data->BisTarget = $r->BisTarget;
		$Data->NumEnd = $r->TtNumEnds;
		$Data->IsRanked = $r->IsRanked;
	}

	if($isPool) {
		require_once('Elimination/Fun_Eliminations.local.inc.php');
		if($isPool==3) {
			$Data->Description=get_text('WG_Pool2');
			$Data->MatchTitles=getPoolMatches();
			$Data->MatchTitlesShort=getPoolMatchesShort();
			$Data->MatchSlots=getPoolMatchesWinners();
			$Data->MatchTitleGroups=array(
				1 => get_text('PoolName', 'Tournament', 'A'),
				2 => get_text('PoolName', 'Tournament', 'B'),
			);
		} elseif($isPool==4) {
			$Data->Description=get_text('WA_Pool4');
			$Data->MatchTitlesWA=getPoolMatchesWA();
			$Data->MatchTitlesWAShort=getPoolMatchesShortWA();
			$Data->MatchSlotsWA=getPoolMatchesWinnersWA();
			$Data->MatchTitleAB=get_text('PoolName', 'Tournament', 'AB');
			$Data->MatchTitleCD=get_text('PoolName', 'Tournament', 'CD');
			$Data->MatchTitleGroups=array(
				1 => get_text('PoolName', 'Tournament', 'A'),
				2 => get_text('PoolName', 'Tournament', 'B'),
				3 => get_text('PoolName', 'Tournament', 'C'),
				4 => get_text('PoolName', 'Tournament', 'D'),
			);
		}
	}

	$MyQuery = getStartListQuery($ORIS, $Event, $Elim, $Filled, $isPool, $BySchedule);

	//echo $MyQuery;exit;
	$Rs=safe_r_sql($MyQuery);

	$OldCode='';

	$Data->Timestamp = '';
	while ($MyRow=safe_fetch($Rs)) {
		if($MyRow->EnTimestamp and $MyRow->EnTimestamp>$Data->Timestamp) {
			$Data->Timestamp=$MyRow->EnTimestamp;
		}
		if(empty($Data->OdfCodes[$MyRow->EventCode])) {
			$Data->OdfCodes[$MyRow->EventCode]=array('event' => $MyRow->EvOdfCode, 'version' => $MyRow->DocVersion, 'versionDate' => $MyRow->DocVersionDate);
		}
		unset($MyRow->EnTimestamp);
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
		if(isset($MyRow->Tiebreak) and trim($MyRow->Tiebreak)) {
			$MyRow->TiebreakDecoded=implode(', ', DecodeFromString(trim($MyRow->Tiebreak), false, true));
		}
		if(!empty($MyRow->EvElimType)) {
			if($MyRow->EvElimType==3) {
				$idx=array_search($MyRow->FinMatchNo, getPoolMatchNos());
				$Data->Data['Items'][$MyRow->EventCode][$idx] = $MyRow;
			} elseif($MyRow->EvElimType==4) {
				$idx=array_search($MyRow->FinMatchNo, getPoolMatchNosWA());
				$Data->Data['Items'][$MyRow->EventCode][$idx] = $MyRow;
			} else {
				$Data->Data['Items'][$MyRow->EventCode][] = $MyRow;
			}
		} else {
			$Data->Data['Items'][$MyRow->EventCode][] = $MyRow;
		}
	}

	return $Data;
}

function getStatEntriesByEvent($ORIS='') {
	$Data=new StdClass();
	$Data->Code='C30A';
	$Data->Order='2';
	$Data->Description='Number of Entries by Event';
	$Data->IndexName='Number of Entries by Event';
	$Data->Continue=get_text('Continue');
	$Data->Data=array();

	if($ORIS) {
		// Individuals
		$Data->Header=array("Event","No. Athletes#","No. Countries#","No. Teams#");
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
			$Sql = "SELECT DISTINCT EcCode, EcTeamEvent, EcNumber 
				FROM EventClass 
				WHERE EcCode=" . StrSafe_DB($MyRowEv->EvCode) . " 
					AND EcTeamEvent!=0 
					AND EcTournament=" . StrSafe_DB($_SESSION['TourId']);
			$RsEc=safe_r_sql($Sql);
			if(safe_num_rows($RsEc)>0) {
                $RuleCnt=0;
                $Sql = "";
                $MultiTeams=array(999);
                while($MyRowEc=safe_fetch($RsEc)) {
                    $ifc=ifSqlForCountry($MyRowEv->EvTeamCreationMode);
                    $Sql .= (++$RuleCnt == 1 ? "FROM ": "INNER JOIN ");
                    $Sql .= "(SELECT {$ifc} as C" . $RuleCnt . ", floor(SUM(IF(EnSubTeam=0,1,0))/$MyRowEc->EcNumber) AS QuantiMulti" . $RuleCnt . "
						FROM Entries
						INNER JOIN EventClass ON EnClass=EcClass AND EnDivision=EcDivision and if(EcSubClass=0, true, EcSubClass=EnSubClass) AND EnTournament=EcTournament AND EcTeamEvent=" . $MyRowEc->EcTeamEvent . " AND EcCode=" . StrSafe_DB($MyRowEc->EcCode) . "
						WHERE {$ifc}<>0 AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnTeam" . ($MyRowEv->EvMixedTeam ? 'Mix' : 'F') ."Event=1
						group by {$ifc}, EnSubTeam
						HAVING COUNT(EnId)>=" . $MyRowEc->EcNumber . ") as sqy";
                    $Sql .= ($RuleCnt == 1 ? " ": $RuleCnt . " ON C1=C". $RuleCnt . " ");
                    $MultiTeams[]='QuantiMulti'.$RuleCnt;
                }
                $Sql = "Select *, least(".implode(',', $MultiTeams).") as MinTeams ".$Sql;

                $Rs=safe_r_sql($Sql);
                $tmpQuanti=safe_num_rows($Rs);
                $Countries=$tmpQuanti;
                if($MyRowEv->EvMultiTeam!=0) {
                    $tmpQuanti = 0;
                    while($tmpRow=safe_fetch($Rs)) {
                        $tmpQuanti += ($MyRowEv->EvMultiTeamNo == 0 ? $tmpRow->MinTeams : min($MyRowEv->EvMultiTeamNo,$tmpRow->MinTeams));
                    }
                }

				$tmpSaved=(valueFirstPhase($MyRowEv->FirstPhase)==$MyRowEv->FirstPhase ? 0 : 8);
				$tmpQuantiIn = $MyRowEv->EvNumQualified;
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
		$Data->Description=get_text('NumberOfEntriesByEvent', 'Tournament');
		$Data->IndexName=get_text('NumberOfEntriesByEvent', 'Tournament');

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
			$tmpSaved=max(0, $Row->EvNumQualified - (numMatchesByPhase($Row->FirstPhase)*2));
			$tmpQuantiIn = $Row->EvNumQualified;
			$tmpQuantiOut = $Row->Quanti - $tmpQuantiIn;
			$tmpMatch = (min($tmpQuantiIn, $Row->Quanti) - $tmpSaved) - numMatchesByPhase($Row->FirstPhase);
			$tmpBye = numMatchesByPhase($Row->FirstPhase) - $tmpMatch;

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
				$Sql = "";
				$MultiTeams=array(999);
				while($MyRowEc=safe_fetch($RsEc)) {
					$ifc=ifSqlForCountry($MyRowEv->EvTeamCreationMode);
					$Sql .= (++$RuleCnt == 1 ? "FROM ": "INNER JOIN ");
					$Sql .= "(SELECT {$ifc} as C" . $RuleCnt . ", floor(SUM(IF(EnSubTeam=0,1,0))/$MyRowEc->EcNumber) AS QuantiMulti" . $RuleCnt . "
						FROM Entries
						INNER JOIN EventClass ON EnClass=EcClass AND EnDivision=EcDivision and if(EcSubClass=0, true, EcSubClass=EnSubClass) AND EnTournament=EcTournament AND EcTeamEvent=" . $MyRowEc->EcTeamEvent . " AND EcCode=" . StrSafe_DB($MyRowEc->EcCode) . "
						WHERE {$ifc}<>0 AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnTeam" . ($MyRowEv->EvMixedTeam ? 'Mix' : 'F') ."Event=1
						group by {$ifc}, EnSubTeam
						HAVING COUNT(EnId)>=" . $MyRowEc->EcNumber . ") as sqy";
					$Sql .= ($RuleCnt == 1 ? " ": $RuleCnt . " ON C1=C". $RuleCnt . " ");
					$MultiTeams[]='QuantiMulti'.$RuleCnt;
				}
				$Sql = "Select *, least(".implode(',', $MultiTeams).") as MinTeams ".$Sql;

				$Rs=safe_r_sql($Sql);
				$tmpQuanti=safe_num_rows($Rs);
				if($MyRowEv->EvMultiTeam!=0) {
					$tmpQuanti = 0;
					while($tmpRow=safe_fetch($Rs)) {
						$tmpQuanti += ($MyRowEv->EvMultiTeamNo == 0 ? $tmpRow->MinTeams : min($MyRowEv->EvMultiTeamNo,$tmpRow->MinTeams));
					}
				}

				$tmpSaved=max(0, $MyRowEv->EvNumQualified - (numMatchesByPhase($MyRowEv->FirstPhase)*2));
				$tmpQuantiIn = $MyRowEv->EvNumQualified;
				$tmpQuantiOut = $tmpQuanti-$tmpQuantiIn;
				$tmpMatch = (min($tmpQuantiIn,$tmpQuanti) -$tmpSaved)-numMatchesByPhase($MyRowEv->FirstPhase);
				$tmpBye = numMatchesByPhase($MyRowEv->FirstPhase)-$tmpMatch;

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
	$Data->IndexName='Number of Entries by Country';
	$Data->Header=array("NOC","Men#","Women#","Total\nCompetitors#","","Officials#", "Total#");
	$Data->HeaderWidth=array(array(10,40),15,15,25,10,15,15,5);
	$Data->Phase='';
	$Data->StatCountries=get_text('StatCountries','Tournament');
	$Data->Continue=get_text('Continue');
	$Data->TotalShort=get_text('TotalShort','Tournament');
	$Data->Total=get_text('Total');
	$Data->Data=array();

	if(!$ORIS) {
		$Data->Description=get_text('NumberOfEntriesByCountry', 'Tournament');
		$Data->IndexName=get_text('NumberOfEntriesByCountry', 'Tournament');
		$Data->Header=array(get_text('CountryCode'), get_text('M')."#", get_text('F')."#",get_text('TotalCompetitors', 'Tournament')."#", "", get_text("Officials", 'Tournament')."#", get_text("Total")."#");
	}

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

function getCompetitionOfficials($ORIS=false) {
    $Data=new StdClass();
    $Data->Code='C35A';
    $Data->Order='2';
    $Data->Description='Competition Officials';
    $Data->Header=array("Function","Name","Organisation","§Gender");
    $Data->IndexName='Competition Officials';
    $Data->HeaderWidth=array(55,60,array(10,50),15);
    $Data->Phase='';
    $Data->Data=array();

    $Sql = "SELECT TiName, TiGivenName, TiGender, ItDescription, CoCode, CoNameComplete,
        concat(DvMajVersion, '.', DvMinVersion) as DocVersion, date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate, DvNotes as DocNotes
	FROM TournamentInvolved
	LEFT JOIN InvolvedType ON TiType=ItId
    LEFT JOIN Countries on TiCountry=CoId and TiTournament=CoTournament
    LEFT JOIN DocumentVersions on TiTournament=DvTournament AND DvFile = 'COMP-OFF'
	WHERE TiTournament={$_SESSION['TourId']}
	ORDER BY IF(ItJudge!=0,1,IF(ItDoS!=0,2,IF(ItJury!=0,3,4))) ASC, IF(ItJudge!=0,ItJudge,IF(ItDoS!=0,ItDoS,IF(ItJury!=0,ItJury,ItOC))) ASC, TiName ASC, TiGivenName ASC";

    $Data->DocVersion='';
    $Data->DocVersionDate='';
    $Data->DocVersionNotes='';
    $q=safe_r_sql($Sql);
    while ($r=safe_fetch($q)) {
        $Data->Data['Items'][$r->ItDescription][]=$r;
        if(!empty($r->DocVersion)) {
            $Data->DocVersion=$r->DocVersion;
            $Data->DocVersionDate=$r->DocVersionDate;
            $Data->DocVersionNotes=$r->DocNotes;
        }
    }
    return $Data;
}

function getStartListByCountries($ORIS=false, $Athletes=false, $orderByName=false, $Events=array(), $Sessions=array()) {
	$Data=new StdClass();

	$Data->Code='C32E';
	$Data->Order='2';
	$Data->Description='Entries by Country';
	$Data->Header=array("NOC","Country","Name","#W. Rank    ", "Date of Birth   #", "#Back No.  ", "Event");
	$Data->HeaderPool=array("Target","Name","NOC","Country", "Points", "#W. Rank    ", "Date of Birth");
	$Data->IndexName='Entries by Country';
	$Data->HeaderWidth=array(10,45,45,15,20,15,45);
	$Data->Phase='';
	$Data->Data=array();

	$Data->Data['Fields'] = array(
		"Bib" => get_text('Code','Tournament'),
        "Bib2" => get_text('LocalCode','Tournament'),
		"Athlete" => get_text('Athlete'),
		"Session" => get_text('SessionShort','Tournament'),
		"SesName" => get_text('Session'),
		"TargetNo" => get_text('Target'),
		'Nation' => get_text('Country'),
		'ClDescription' => get_text('Class'),
		'DivDescription' => get_text('Division'),
		'Category' => get_text('DivisionClass'),
		'AgeClass' => get_text('AgeCl'),
		'SubClass' =>get_text('SubCl','Tournament'),
		'Status' => get_text('Status','Tournament'),
		"EvCode"=>get_text('EvCode'),
		'EventName' => get_text('Event'),
		'TargetFace' => get_text('TargetType'),
		'Photo' => get_text('Photo', 'Tournament'),
		'DOB' => get_text('DOB', 'Tournament'),
		'Email' => get_text('Email', 'Tournament'),
		"NationCode"=>get_text('Country'),
		"EventCode"=>get_text('EvCode'),
		"ClassCode"=>get_text('Class'),
		"DivCode"=>get_text('Division'),
		"NationCode2"=>get_text('Country'),
		"Nation2"=>get_text('Nation'),
		"NationCode3"=>get_text('Country'),
		"Nation3"=>get_text('Nation'),
		"MissingPhoto"=>'Photo Missing',
		"RetakePhoto"=>'Photo to Retake',
	);


	$RsTour=safe_r_sql("SELECT (ToElabTeam!=0) as BisTarget, ToNumEnds AS TtNumEnds, (select max(RankRanking) as IsRanked from Rankings where RankTournament={$_SESSION['TourId']}) as IsRanked
		FROM Tournament
		WHERE ToId=" . StrSafe_DB($_SESSION['TourId']));
	if ($r=safe_fetch($RsTour)) {
		$Data->BisTarget = $r->BisTarget;
		$Data->NumEnd = $r->TtNumEnds;
		$Data->IsRanked = $r->IsRanked;
	}
	$Data->DocVersion='';
	$Data->DocVersionDate='';
	$Data->DocVersionNotes='';

	if($ORIS) {
		$Data->Data['Fields']['TargetNo']='Target';
		$Data->Data['Fields']['Athlete']='Name';
		$Data->Data['Fields']['NationCode']='NOC';
		$Data->Data['Fields']['Nation']='Country';
		$Data->Data['Fields']['EventName']='Event';
		$Data->Data['Fields']['Session']='Session';
	} else {
		$Data->Description=get_text('EntriesByCountry', 'Tournament');
		$Data->Header=array(get_text('Country'), get_text('Nation'), get_text('Name', 'Tournament'), get_text('DOB', 'Tournament')."   #", get_text('Target'), get_text("Event"));
		$Data->IndexName=get_text('EntriesByCountry', 'Tournament');
		$Data->HideCols = GetParameter("IntEvent");
		$Data->Description=get_text('StartlistCountry','Tournament');
	}
	$MyQuery = getStartListCountryQuery($ORIS, $Athletes, $orderByName, $Events, $Sessions);

	//echo $MyQuery;exit;
	$Rs=safe_r_sql($MyQuery);
	while ($MyRow=safe_fetch($Rs)) {
	    if(!empty($MyRow->EvCodeParent)) continue;
		if(!empty($MyRow->EventName)) $MyRow->EventName=get_text($MyRow->EventName,'','',true);
		$MyRow->DivDescription=get_text($MyRow->DivDescription,'','',true);
		$MyRow->ClDescription=get_text($MyRow->ClDescription,'','',true);
		$Data->Data['Items'][$MyRow->NationCode][]=$MyRow;
		if(!empty($MyRow->DocVersion)) {
			$Data->DocVersion=$MyRow->DocVersion;
			$Data->DocVersionDate=$MyRow->DocVersionDate;
			$Data->DocVersionNotes=$MyRow->DocNotes;
		}
	}
	return $Data;
}

function getStandingRecords($ORIS=true) {
	$Data=new StdClass();

	$Data->Code='C24';
	$Data->Order='2';
	$Data->Description='Records';
	$Data->Header=array(
			"Record Description",
			"§Score",
			"Name",
			"§NOC\nCode",
			"Location",
			"#Date");
	$Data->HeaderWidth=array(50, 15, 50, 10, 45, 0);
	$Data->IndexName='Records';
	$Data->Phase='';
	$Data->DocVersion='';
	$Data->DocVersionDate='';
	$Data->DocVersionNotes='';
	$Data->RecordAs=$_SESSION['TourRealWhenFrom'];
	$Data->SubSections=array();
	$Data->Data=array();

	$Data->Data['Fields'] = array(
		"Athlete" => get_text('Athlete'),
		'Nation' => get_text('Country'),
	);

	if($ORIS) {
		$Data->Data['Fields']['Athlete']='Name';
		$Data->Data['Fields']['NationCode']='NOC';
		$Data->Data['Fields']['Nation']='Country';
		$Data->Data['Fields']['EventName']='Event';
		$Data->Data['Fields']['Session']='Session';
	} else {
		$Data->Description=get_text('EntriesByCountry', 'Tournament');
		$Data->Header=array(get_text('Country'), get_text('Nation'), get_text('Name', 'Tournament'), get_text('DOB', 'Tournament')."   #", get_text('Target'), get_text("Event"));
		$Data->IndexName=get_text('EntriesByCountry', 'Tournament');
		$Data->HideCols = GetParameter("IntEvent");
		$Data->Description=get_text('StartlistCountry','Tournament');
	}
	$MyQuery = getStandingRecordsQuery($ORIS);

	$Rs=safe_r_sql($MyQuery);
	$Record=array();
	$Countries=array();
	while ($MyRow=safe_fetch($Rs)) {
		if($MyRow->RtRecDate=='0000-00-00') {
			$MyRow->RtRecDate='';
		}
		if($MyRow->RtRecLastUpdated<$Data->RecordAs) {
			$Data->RecordAs=substr($MyRow->RtRecLastUpdated, 0, 10);
		}
		if(!empty($MyRow->EventName)) {
			$MyRow->EventName=get_text($MyRow->EventName,'','',true);
		}
		$MyRow->RtRecExtra=unserialize($MyRow->RtRecExtra);

		$Data->Data['Items'][$MyRow->EvTeamEvent]["$MyRow->EvRecCategory-$MyRow->RtRecCode"][]=$MyRow;
		$Data->SubSections[$MyRow->EvTeamEvent]["$MyRow->EvRecCategory-$MyRow->RtRecCode"]=$MyRow->EventName . ' - ' . $MyRow->TrHeader;
		if(!empty($MyRow->DocVersion)) {
			$Data->DocVersion=$MyRow->DocVersion;
			$Data->DocVersionDate=$MyRow->DocVersionDate;
			$Data->DocVersionNotes=$MyRow->DocNotes;
		}
	}

	return $Data;
}

function getBrokenRecords($ORIS=true) {
	$Data=new StdClass();

	$Data->Code='C81';
	$Data->Order='2';
	$Data->Description='Records Broken';
	$Data->Header=array(
			"Record Description",
			"§Record Score\nold / new",
			"Name",
			"§NOC\nCode",
			"#Date");
	$Data->HeaderWidth=array(50, 30, 55, 15, 0);
	$Data->IndexName='Records';
	$Data->Phase='';
	$Data->DocVersion='';
	$Data->DocVersionDate='';
	$Data->DocVersionNotes='';
	$Data->RecordAs=$_SESSION['TourRealWhenTo'];
	$Data->SubSections=array();
	$Data->Data=array();

	$Data->Data['Fields'] = array(
		"Athlete" => get_text('Athlete'),
		'Nation' => get_text('Country'),
	);

	if($ORIS) {
		$Data->Data['Fields']['Athlete']='Name';
		$Data->Data['Fields']['NationCode']='NOC';
		$Data->Data['Fields']['Nation']='Country';
		$Data->Data['Fields']['EventName']='Event';
		$Data->Data['Fields']['Session']='Session';
	} else {
		$Data->Description=get_text('EntriesByCountry', 'Tournament');
		$Data->Header=array(get_text('Country'), get_text('Nation'), get_text('Name', 'Tournament'), get_text('DOB', 'Tournament')."   #", get_text('Target'), get_text("Event"));
		$Data->IndexName=get_text('EntriesByCountry', 'Tournament');
		$Data->HideCols = GetParameter("IntEvent");
		$Data->Description=get_text('StartlistCountry','Tournament');
	}
	$MyQuery = getBrokenRecordsQuery($ORIS);

	$Rs=safe_r_sql($MyQuery);
	$Record=array();
	if($CheckDate=(date('Y-m-d')<$Data->RecordAs)) {
		$Data->RecordAs=date('Y-m-d');
	}
	//error_reporting(E_ALL);
	while ($MyRow=safe_fetch($Rs)) {
		//if($MyRow->RtRecXNine and $MyRow->RtRecTotal==$MyRow->NewRecord and $MyRow->RtRecXNine>=$MyRow->NewXNine) {
		//	// in case full scores the X are marked so check the Xs of the new record
		//	continue;
		//}
		switch($MyRow->Phase) {
			case '1':
				if(!$MyRow->RtRecXNine) {
					// if no XNine marks, remove the ones of the new record
					$MyRow->NewXNine=0;
				}
				break;
			case '3':
				// matches...
				//$items=getEventArrowsParams($MyRow->EvRecCategory, 2, $MyRow->EvTeamEvent);
				//if($MyRow->NewRecord!=$items->ends*$items->arrows*10) {
				//	$MyRow->NewXNine=0;
				//}
				break;
			default:
				if(!$MyRow->RtRecXNine) {
					// if no XNine marks, remove the ones of the new record
					$MyRow->NewXNine=0;
				}
		}
		if($CheckDate and $MyRow->RecordDateDate>$Data->RecordAs) {
			$Data->RecordAs=$MyRow->RecordDateDate;
		}
		if(!empty($MyRow->EventName)) {
			$MyRow->EventName=get_text($MyRow->EventName,'','',true);
		}
		$MyRow->RtRecExtra=unserialize($MyRow->RtRecExtra);

		$Data->Data['Items'][$MyRow->TeamEvent]["$MyRow->RecCategory-$MyRow->RtRecCode"][]=$MyRow;
		$Data->SubSections[$MyRow->TeamEvent]["$MyRow->RecCategory-$MyRow->RtRecCode"]=$MyRow->RecCategoryName . ' - ' . $MyRow->TrHeader;
		if(!empty($MyRow->DocVersion)) {
			$Data->DocVersion=$MyRow->DocVersion;
			$Data->DocVersionDate=$MyRow->DocVersionDate;
			$Data->DocVersionNotes=$MyRow->DocNotes;
		}
	}

	return $Data;
}

function getCountriesList($ORIS='') {
	$Data=new StdClass();

	$Data->Code='C30';
	$Data->Order='2';
	$Data->Description='List of Countries';
	$Data->Header=array("", "NOC","Country");
	$Data->IndexName='List of Countries';
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
		$Data->IndexName=get_text('ListCountries','Tournament');
		$Data->Header=array("", get_text('Country'), get_text('Nation'));
	}

	$MyQuery = getCountryList();

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
	$Data->Header=array("Name","NOC","Country", '#W. Rank    ', "#Date of Birth  ", "#Back No.  ", "Event");
	$Data->HeaderWidth=array(45, 10, 40, 15, 25, 15, 40);
	$Data->Phase='';
	$Data->Continue=get_text('Continue');
	$Data->Data=array();
	$Data->IndexName='Entries by Name';
	$Data->DocVersion='';
	$Data->DocVersionDate='';
	$Data->DocVersionNotes='';

	$Locations=array();
	if($FopLocations=Get_Tournament_Option('FopLocations')) {
		foreach($FopLocations as $loc) {
			foreach(range($loc->Tg1, $loc->Tg2) as $t) {
				$Locations[$t]=$loc->Loc;
			}
		}
	}

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

	$RsTour=safe_r_sql("SELECT (ToElabTeam!=0) as BisTarget, ToNumEnds AS TtNumEnds, (select max(RankRanking) as IsRanked from Rankings where RankTournament={$_SESSION['TourId']}) as IsRanked
		FROM Tournament
		WHERE ToId=" . StrSafe_DB($_SESSION['TourId']));
	if ($r=safe_fetch($RsTour)) {
		$Data->BisTarget = $r->BisTarget;
		$Data->NumEnd = $r->TtNumEnds;
		$Data->IsRanked = $r->IsRanked;
	}

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
		$Data->IndexName=get_text('StartlistAlpha','Tournament');
		$Data->Header=array(get_text('Name', 'Tournament'), get_text('Country'), get_text('Nation'), get_text('DOB', 'Tournament')."   #", get_text('Target'), get_text("Event"));
	}
	$MyQuery = getStartListAlphaQuery($ORIS);

	$OldLetter='';
	$Group=0;

	//echo $MyQuery;exit;
	$Rs=safe_r_sql($MyQuery);
	while ($MyRow=safe_fetch($Rs)) {
		if(isset($Locations[intval($MyRow->TargetButt)])) {
			$MyRow->Location=$Locations[intval($MyRow->TargetButt)];
		}
		if($OldLetter != strtoupper($MyRow->FirstLetter)) {
			$Group++;
			$OldLetter = strtoupper($MyRow->FirstLetter);
		}

		$MyRow->EventName=get_text($MyRow->EventName,'','',true);
		$Data->Data['Items'][$Group][]=$MyRow;

		if(!empty($MyRow->DocVersion)) {
			$Data->DocVersion=$MyRow->DocVersion;
			$Data->DocVersionDate=$MyRow->DocVersionDate;
			$Data->DocVersionNotes=$MyRow->DocNotes;
		}

	}

	return $Data;
}


function getStartListCategory($ORIS=false, $orderByTeam=0, $Events=array()) {
	$Data=new StdClass();

	$Data->Code='C32C';
	$Data->Order='3';
	$Data->Description='Entries by Event';
	$Data->Header=array("NOC", "Country", "#Back No.", "#W. Rank    ", " Date of Birth", "Name");
	$Data->HeaderWidth=array(15, 40, 15, 15, 25, 65);
	$Data->Phase='';
	$Data->Continue=get_text('Continue');
	$Data->Data=array();
	$Data->IndexName='Entries by Event';
	$Data->DocVersion='';
	$Data->DocVersionDate='';
	$Data->DocVersionNotes='';

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
			'Rank' => get_text('Rank'),
	);

	$Data->BisTarget = false;
	$Data->NumEnd = 0;
	$RsTour=safe_r_sql("SELECT (ToElabTeam!=0) as BisTarget, ToNumEnds AS TtNumEnds, (select max(RankRanking) as IsRanked from Rankings where RankTournament={$_SESSION['TourId']}) as IsRanked
		FROM Tournament
		WHERE ToId=" . StrSafe_DB($_SESSION['TourId']));
	if ($r=safe_fetch($RsTour)) {
		$Data->BisTarget = $r->BisTarget;
		$Data->NumEnd = $r->TtNumEnds;
		$Data->IsRanked = $r->IsRanked;
	}

	if($ORIS) {
		$Data->Data['Fields']['Athlete'] = "Name";
		$Data->Data['Fields']['TargetNo'] = "Target";
		$Data->Data['Fields']['NationCode'] = "NOC";
		$Data->Data['Fields']['Nation'] = "Country";
		$Data->Data['Fields']['Category'] = "Event";
		$Data->Data['Fields']['SesName'] = "Session";
	} else {
		$Data->HideCols = GetParameter("IntEvent");
		$Data->Description=get_text('StartlistTeam','Tournament');
		$Data->Description=get_text('StartlistTeam','Tournament');
	}

	if(!empty($_REQUEST['TeamEvents'])) {
		if(!is_array($_REQUEST['TeamEvents'])) {
			$_REQUEST['TeamEvents']=array($_REQUEST['TeamEvents']);
		}
		$Events=$_REQUEST['TeamEvents'];
	}

	$MyQuery = getStartListCategoryQuery($ORIS, $orderByTeam, $Events);
	$OldCategory='';
	$Group=0;

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
	$Data->hideGolds = (getTournamentType()==14 or getTournamentType()==32);
	$Data->NumberThousandsSeparator = get_text('NumberThousandsSeparator');
	$Data->Description=get_text('ResultIndClass','Tournament');
	$Data->Continue=get_text('Continue');
	$Data->TotalShort=get_text('TotalShort','Tournament');
	$Data->IndexName=get_text('ResultIndClass','Tournament');

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
        if(!empty($_REQUEST["SubClassGenderRank"])) $options['joinGender']=true;
		if(!empty($_REQUEST["ShowAwards"])) $options['showAwards'] = true;
	}

	if(!empty($_REQUEST['Session'])) {
		$options['sessions']=array(0);
		foreach($_REQUEST['Session'] as $Ses) {
			$options['sessions'][]=$Ses;
		}
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
	$Data->hideGolds = (getTournamentType()==14 or getTournamentType()==32);
	$Data->NumberThousandsSeparator = get_text('NumberThousandsSeparator');
	$Data->Description=get_text('ResultSqClass','Tournament');
	$Data->Continue=get_text('Continue');
	$Data->TotalShort=get_text('TotalShort','Tournament');
	$Data->IndexName=get_text('ResultSqClass','Tournament');

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

function getQualificationIndividual($EventRequested='', $ORIS=false, $ShowRecords=false) {
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
	$Data->IndexName=get_text('ResultIndAbs', 'Tournament');

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

	if($ShowRecords) $options['records']=true;
	$Data->family=$family;

	$rank=Obj_RankFactory::create($family,$options);
	$rank->read();
	$Data->rankData=$rank->getData();

	return $Data;
}

function getEliminationIndividual($EventRequested='', $ORIS=false) {
	$Data=new StdClass();

	$options=array();
	if($EventRequested) {
		if(is_array($EventRequested)) {
            foreach($EventRequested as $evRaw) {
                if(strpos($evRaw,'@')) {
                    if(!array_key_exists('eventR',$options)) {
                        $options['eventsR']=array();
                    }
                    $options['eventsR'][] = $evRaw;
                } else {
                    if(!array_key_exists('events',$options)) {
                        $options['events']=array();
                    }
                    $options['events']=array($evRaw);
                }
            }
		} elseif(strpos($EventRequested,'@')) {
            $options['eventR']=array($EventRequested);
        } else {
			$options['events']=array($EventRequested);
		}
	}
	$rank=Obj_RankFactory::create('ElimInd',$options);
	$rank->read();
	$Data->rankData=$rank->getData();

	$Data->Code='C73A';
	$Data->Order='1';
	if(!$ORIS) {
		$Data->Description=get_text('Elimination');
	} else {
		$Data->Description='Results';
	}
	$Data->NumberThousandsSeparator = get_text('NumberThousandsSeparator');
	$Data->NumberDecimalSeparator = get_text('NumberDecimalSeparator');
    $Data->ShootOffArrows=get_text('ShootOffArrows','Tournament');
	$Data->ShotOffShort=get_text('ShotOffShort','Tournament');
	$Data->CoinTossShort=get_text('CoinTossShort','Tournament');
	$Data->Continue=get_text('Continue');
    $Data->Judge=get_text('Judge','Tournament');
    $Data->Winner=get_text('Winner');
    $Data->TargetShort=get_text('TargetShort','Tournament');
	$Data->IndexName=get_text('Elimination');

	return $Data;
}

function getEliminationPoolIndividual($EventRequested='', $ORIS=false, $isPool=4, $BySchedule=false) {
	$Data=getStartList($ORIS, $EventRequested, true,false, $isPool, $BySchedule);

	$Data->Code='C73A';
	$Data->Order='1';
	if(!$ORIS) {
		$Data->Description=get_text('Elimination');
	} else {
		$Data->Description='Results';
	}
	$Data->NumberThousandsSeparator = get_text('NumberThousandsSeparator');
	$Data->NumberDecimalSeparator = get_text('NumberDecimalSeparator');
    $Data->ShootOffArrows=get_text('ShootOffArrows','Tournament');
	$Data->ShotOffShort=get_text('ShotOffShort','Tournament');
	$Data->CoinTossShort=get_text('CoinTossShort','Tournament');
	$Data->Continue=get_text('Continue');
    $Data->Judge=get_text('Judge','Tournament');
    $Data->Winner=get_text('Winner');
    $Data->TargetShort=get_text('TargetShort','Tournament');
	$Data->IndexName=get_text('Elimination');

	return $Data;
}

function getQualificationTeam($EventRequested='', $ORIS=false, $ShowRecords=false) {
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
	$Data->IndexName=get_text('ResultSqAbs', 'Tournament');

	if(!$ORIS) {
		$Data->Description=get_text('ResultSqClass','Tournament');
	}

	$options=array();
	if(isset($_REQUEST["Event"]) && $_REQUEST["Event"][0]!=".")
		$options['events'] = $_REQUEST["Event"];
	if(isset($_REQUEST["MaxNum"]) && is_numeric($_REQUEST["MaxNum"]))
		$options['cutRank'] = $_REQUEST["MaxNum"];
	if($EventRequested) $options['events']=$EventRequested;
	if($ShowRecords) $options['records']=true;


	$rank=Obj_RankFactory::create('AbsTeam',$options);
	$rank->read();
	$Data->rankData=$rank->getData();

	return $Data;
}

function getBracketsIndividual($EventRequested='', $ORIS=false, $ShowTargetNo=true, $ShowSchedule=true, $ShowSetArrows=true, $ShowRecords=false) {
	$Data=new StdClass();

	$Data->Code='C75A';
	$Data->Description='Result Brackets';
	$Data->Final=get_text('0_Phase');
	$Data->Bronze=get_text('1_Phase');
	$Data->Bye=get_text('Bye');
	$Data->Events=array();
	$Data->ShowTargetNo = $ShowTargetNo;
	$Data->ShowSchedule = $ShowSchedule;
	$Data->ShowSetArrows= $ShowSetArrows;
	$Data->IndexName=get_text('FinalBracketsInd', 'Tournament');

	$Data->ScoreCode='C73D';
	$Data->ScoreDescription='';
	$Data->ScorePhase=get_text('ScorecardsInd', 'Tournament');
	$Data->ScoreIndexName=get_text('ScorecardsInd', 'Tournament');

	if(!$ORIS) {
		$Data->Description=get_text('BracketsInd');
	}

	$options=array();
    $options['noElim']=true;
	if($EventRequested) $options['events']=$EventRequested;
	if($ShowRecords) $options['records']=true;

	$rank=Obj_RankFactory::create('GridInd',$options);
	$rank->read();
	$Data->rankData=$rank->getData();
	$Data->LastUpdate=$Data->rankData['meta']['lastUpdate'];

	return $Data;
}

function getRankingIndividual($EventRequested='', $ORIS=false, $ShowRecords=false) {
	$Data=new StdClass();

	$Data->Code='C76A';
	$Data->Description='Results Summary';
	$Data->Phase=get_text('FinalRankInd', 'Tournament');
	$Data->Bye=get_text('Bye');
	$Data->Elim1=get_text('Eliminations_1');
	$Data->Elim2=get_text('Eliminations_2');
	$Data->NumberThousandsSeparator = get_text('NumberThousandsSeparator');
	$Data->NumberDecimalSeparator = get_text('NumberDecimalSeparator');
	$Data->Continue=get_text('Continue');
	$Data->IndexName=get_text('FinalRankInd', 'Tournament');

	if(!$ORIS) {
		$Data->Description=get_text('RankingInd');
	}

	$options=array();
	if(isset($_REQUEST["Event"]) && $_REQUEST["Event"][0]!=".")
		$options['eventsR'] = $_REQUEST["Event"];
	if($EventRequested) $options['eventsR']=$EventRequested;
	if($ShowRecords) $options['records']=true;

	$rank=Obj_RankFactory::create('FinalInd',$options);
	$rank->read();
	$Data->rankData=$rank->getData();
	$Data->LastUpdate=$Data->rankData['meta']['lastUpdate'];
	return $Data;
}

function getRankingTeams($EventRequested='', $ORIS=false, $ShowRecords=false) {
	$Data=new StdClass();

	$Data->Code='C76B';
	$Data->Description='Results Summary';
	$Data->Phase=get_text('FinalRankTeams', 'Tournament');
	$Data->NumberThousandsSeparator = get_text('NumberThousandsSeparator');
	$Data->NumberDecimalSeparator = get_text('NumberDecimalSeparator');
	$Data->Bye=get_text('Bye');
	$Data->IndexName=get_text('FinalRankTeams', 'Tournament');

	if(!$ORIS) {
		$Data->Description=get_text('RankingSq');
	}

	$options=array();
	if(isset($_REQUEST["Event"]) && $_REQUEST["Event"][0]!=".")
		$options['eventsR'] = $_REQUEST["Event"];
	if($EventRequested) $options['eventsR']=$EventRequested;
	if($ShowRecords) $options['records']=true;

	$rank=Obj_RankFactory::create('FinalTeam',$options);
	$rank->read();
	$Data->rankData=$rank->getData();
	$Data->LastUpdate=$Data->rankData['meta']['lastUpdate'];

	return $Data;
}

function getBracketsTeams($EventRequested='', $ORIS=false, $ShowTargetNo=true, $ShowSchedule=true, $ShowSetArrows=true, $ShowRecords=false) {
	$Data=new StdClass();

	$Data->Code='C75B';
	$Data->Description='Result Brackets';
	$Data->Phase="Final Round";
	$Data->Final=get_text('0_Phase');
	$Data->Bronze=get_text('1_Phase');
	$Data->Bye=get_text('Bye');
	$Data->ShowTargetNo = $ShowTargetNo;
	$Data->ShowSchedule = $ShowSchedule;
	$Data->ShowSetArrows= $ShowSetArrows;
	$Data->IndexName=get_text('FinalBracketsTeam', 'Tournament');
	$Data->ScoreCode='C73E';
	$Data->ScoreDescription='';
	$Data->ScorePhase=get_text('ScorecardsTeams', 'Tournament');
	$Data->ScoreIndexName=get_text('ScorecardsTeams', 'Tournament');

	if(!$ORIS) {
		$Data->Description=get_text('BracketsSq');
	}

	$options=array();
	if($EventRequested) $options['events']=$EventRequested;
	if($ShowRecords) $options['records']=true;

	$rank=Obj_RankFactory::create('GridTeam',$options);
	$rank->read();
	$Data->rankData=$rank->getData();
	$Data->LastUpdate=$Data->rankData['meta']['lastUpdate'];

	return $Data;
}

function getMedalList($ORIS=false, $TourId=0, $Event='', $Team='') {
	if(!$TourId) {
		$TourId=$_SESSION['TourId'];
	}

	$Data=new StdClass();

	$Data->Code='C93';
	$Data->Description='Medallists by Event';
	$Data->Phase="Medallists by Event";
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
	$Data->IndexName='Medallists by Event';
	$Data->Version='';
	$Data->VersionData='';
	$Data->VersionNote='';
	// get version
	$q=safe_r_sql("select concat(DvMajVersion, '.', DvMinVersion) as DocVersion,
			date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate,
			DvNotes as DocNotes
			from DocumentVersions
			where DvTournament='{$_SESSION['TourId']}' and DvFile='MEDAL'");
	if($r=safe_fetch($q)) {
		$Data->Version=$r->DocVersion;
		$Data->VersionDate=$r->DocVersionDate;
		$Data->VersionNote=$r->DocNotes;
	}

	$options=array();
	$options['tournament']=$TourId;
	$options['event']=$Event;
	$options['teamEvent']=$Team;

	$rank=Obj_RankFactory::create('MedalList',$options);
	$rank->read();
	$Data->rankData=$rank->getData();
	$Data->LastUpdate=$Data->rankData['lastUpdate'];

	return $Data;
}

function getMedalStand($ORIS=false, $TourId=0) {
	$Data=new StdClass();

	$Data->Code='C95';
	$Data->Description='Medal Standings';
	$Data->Phase='Medal Standings';
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
	$Data->IndexName='Medal Standings';
	$Data->Version='';
	$Data->VersionData='';
	$Data->VersionNote='';
	$Data->OdfLastUpdate='';
	$Data->OdfLastEvent='';
	$Data->OdfTotalEvents=0;
	$Data->OdfFinishedEvents=0;
	// get version
	$q=safe_r_sql("select concat(DvMajVersion, '.', DvMinVersion) as DocVersion,
			date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate,
			DvNotes as DocNotes
			from DocumentVersions
			where DvTournament='{$_SESSION['TourId']}' and DvFile='MEDAL'");
	if($r=safe_fetch($q)) {
		$Data->Version=$r->DocVersion;
		$Data->VersionDate=$r->DocVersionDate;
		$Data->VersionNote=$r->DocNotes;
	}



	if(!$ORIS) {
		$Data->Description=get_text('MedalStanding');
	}

	$options=array();
	if($TourId) {
		$options['tournament']=$TourId;
	}
	if(isset($_REQUEST["Event"]) && $_REQUEST["Event"][0]!=".")
		$options['eventsR'] = $_REQUEST["Event"];
	$options['cutRank']=3;

	$rank=Obj_RankFactory::create('FinalInd',$options);
	$rank->read();
	if(empty($Data->Ind)) {
		$Data->Ind = new StdClass();
	}
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
			$Data->OdfTotalEvents++;
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

					if($item['rank']==1) {
						// updates OdfRelated things
						$Data->OdfFinishedEvents++;

						if($Data->OdfLastUpdate<$section['meta']['lastUpdate']) {
							$Data->OdfLastUpdate=$section['meta']['lastUpdate'];
							$Data->OdfLastEvent=$section['meta']['odfEvent'];
						}
					}
				}
			}
		}
	}

	$options=array();
	if($TourId) {
		$options['tournament']=$TourId;
	}
	if(isset($_REQUEST["Event"]) && $_REQUEST["Event"][0]!=".")
		$options['eventsR'] = $_REQUEST["Event"];
	$options['cutRank']=3;

	$rank=Obj_RankFactory::create('FinalTeam',$options);
	$rank->read();
	$rankData=$rank->getData();
	foreach($rankData['sections'] as $Event => $section) {
		/// changed explicitly since Fazza 2021: from full podium to at least one!
		if(count($section['items']) == 0) continue;
		if($section['meta']['medals']) {
			$Data->OdfTotalEvents++;
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

					if($item['rank']==1) {
						// updates OdfRelated things
						$Data->OdfFinishedEvents++;

						if($Data->OdfLastUpdate<$section['meta']['lastUpdate']) {
							$Data->OdfLastUpdate=$section['meta']['lastUpdate'];
							$Data->OdfLastEvent=$section['meta']['odfEvent'];
						}
					}
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

function OdfMedalStand($TourId=0) {
	if(!$TourId) {
		$TourId=$_SESSION['TourId'];
	}
	$Data=new StdClass();

	$Data->Code='C95';
	$Data->Description='Medal Standings';
	$Data->Phase='Medal Standings';
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
	$Data->IndexName='Medal Standings';
	$Data->Version='';
	$Data->VersionData='';
	$Data->VersionNote='';
	$Data->OdfLastUpdate='';
	$Data->OdfLastEvent='';
	$Data->OdfTotalEvents=0;
	$Data->OdfFinishedEvents=0;
	$Data->SameRank=array();
	$Data->SameTotRank=array();
	$Data->CountryList=array();

	// get version
	$q=safe_r_sql("select concat(DvMajVersion, '.', DvMinVersion) as DocVersion,
			date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate,
			DvNotes as DocNotes
			from DocumentVersions
			where DvTournament='{$_SESSION['TourId']}' and DvFile='MEDAL'");
	if($r=safe_fetch($q)) {
		$Data->Version=$r->DocVersion;
		$Data->VersionDate=$r->DocVersionDate;
		$Data->VersionNote=$r->DocNotes;
	}

	$Data->Description=get_text('MedalStanding');

	$options=array(
		'tournament'=>$TourId,
		'cutRank' => 3,
		);
	if(isset($_REQUEST["Event"]) && $_REQUEST["Event"][0]!=".") {
		$options['eventsR'] = $_REQUEST["Event"];
	}

	$rank=Obj_RankFactory::create('FinalInd',$options);
	$rank->read();
	$rankData=$rank->getData();

	$Data->LastUpdate = $rankData['meta']['lastUpdate'];

	// get the ODF genders implied
	$tmp=array();
	$q=safe_r_sql("select distinct EvOdfGender from Events where EvTournament=$TourId and EvOdfGender!='' order by EvTeamEvent, EvProgr");
	while($r=safe_fetch($q)) {
		$tmp[$r->EvOdfGender]=array(1 => 0, 2=> 0, 3=>0);
	}
	$tmp['TOT']=array(1 => 0, 2=> 0, 3=>0);

	$Data->medStatus=$tmp;

	foreach($rankData['sections'] as $Event => $section) {
		if($section['meta']['medals']) {
			$Data->OdfTotalEvents++;
			foreach($section['items'] as $item) {
				if($item['rank']!=0) {
					if(empty($Data->CountryList[$item['countryCode']])) {
						$Data->CountryList[$item['countryCode']]['Code'] = $item['countryCode'];
						$Data->CountryList[$item['countryCode']]['Name'] = $item['countryName'];
						$Data->CountryList[$item['countryCode']]['Rank'] = 0;
						$Data->CountryList[$item['countryCode']]['Medals'] = $tmp;
						$Data->CountryList[$item['countryCode']]['TotMedal'] = 0;
						$Data->CountryList[$item['countryCode']]['TotRank'] = 0;
					}
					$Data->CountryList[$item['countryCode']]['Medals'][$section['meta']['odfGender']][$item['rank']]++;
					$Data->CountryList[$item['countryCode']]['Medals']['TOT'][$item['rank']]++;
					$Data->medStatus[$section['meta']['odfGender']][$item['rank']]++;
					$Data->medStatus['TOT'][$item['rank']]++;

					$Data->CountryList[$item['countryCode']]['TotMedal']++;

					if($item['rank']==1) {
						// updates OdfRelated things
						$Data->OdfFinishedEvents++;

						if($Data->OdfLastUpdate<$section['meta']['lastUpdate']) {
							$Data->OdfLastUpdate=$section['meta']['lastUpdate'];
							$Data->OdfLastEvent=$section['meta']['odfEvent'];
						}
					}
				}
			}
		}
	}

	$options=array();
	if($TourId) {
		$options['tournament']=$TourId;
	}
	if(isset($_REQUEST["Event"]) && $_REQUEST["Event"][0]!=".")
		$options['eventsR'] = $_REQUEST["Event"];
	$options['cutRank']=3;

	$rank=Obj_RankFactory::create('FinalTeam',$options);
	$rank->read();
	$rankData=$rank->getData();
	foreach($rankData['sections'] as $Event => $section) {
		if(count($section['items']) < 3) continue;
		if($section['meta']['medals']) {
			$Data->OdfTotalEvents++;
			foreach($section['items'] as $item) {
				if($item['rank']!=0) {
					if(empty($Data->CountryList[$item['countryCode']])) {
						$Data->CountryList[$item['countryCode']]['Code'] = $item['countryCode'];
						$Data->CountryList[$item['countryCode']]['Name'] = $item['countryName'];
						$Data->CountryList[$item['countryCode']]['Rank'] = 0;
						$Data->CountryList[$item['countryCode']]['Medals'] = $tmp;
						$Data->CountryList[$item['countryCode']]['TotMedal'] = 0;
						$Data->CountryList[$item['countryCode']]['TotRank'] = 0;
					}
					$Data->CountryList[$item['countryCode']]['Medals'][$section['meta']['odfGender']][$item['rank']]++;
					$Data->CountryList[$item['countryCode']]['Medals']['TOT'][$item['rank']]++;
					$Data->medStatus[$section['meta']['odfGender']][$item['rank']]++;
					$Data->medStatus['TOT'][$item['rank']]++;

					$Data->CountryList[$item['countryCode']]['TotMedal']++;

					if($item['rank']==1) {
						// updates OdfRelated things
						$Data->OdfFinishedEvents++;

						if($Data->OdfLastUpdate<$section['meta']['lastUpdate']) {
							$Data->OdfLastUpdate=$section['meta']['lastUpdate'];
							$Data->OdfLastEvent=$section['meta']['odfEvent'];
						}
					}
				}
			}
		}
	}


	$MyPos=0;
	$TmpOldValue=0;
	$MyRank=0;
	uasort($Data->CountryList, 'odfTotComp');
	foreach($Data->CountryList as $Country => $cData) {
		$MyPos++;
		if($TmpOldValue != $cData['TotMedal']) {
			$MyRank=$MyPos;
			$TmpOldValue = $cData['TotMedal'];
		}
		$Data->CountryList[$Country]['TotRank']=$MyRank;
		if(!isset($Data->SameTotRank[$MyRank])) {
			$Data->SameTotRank[$MyRank]=0;
		}
		$Data->SameTotRank[$MyRank]++;
	}

	$MyPos=0;
	$TmpOldValue=0;
	$MyRank=1;
	uasort($Data->CountryList, 'odfStandComp');
	foreach($Data->CountryList as $Country => $cData) {
		$MyPos++;
		if($TmpOldValue != $cData['Medals']['TOT'][1]*10000+$cData['Medals']['TOT'][2]*100+$cData['Medals']['TOT'][3]) {
			$MyRank=$MyPos;
			$TmpOldValue = $cData['Medals']['TOT'][1]*10000+$cData['Medals']['TOT'][2]*100+$cData['Medals']['TOT'][3];
		}
		$Data->CountryList[$Country]['Rank']=$MyRank;
		if(!isset($Data->SameRank[$MyRank])) {
			$Data->SameRank[$MyRank]=0;
		}
		$Data->SameRank[$MyRank]++;
	}

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

function odfStandComp($a, $b) {
	if($a['Medals']['TOT'][1]>$b['Medals']['TOT'][1]) return -1;
	if($a['Medals']['TOT'][1]<$b['Medals']['TOT'][1]) return 1;
	if($a['Medals']['TOT'][2]>$b['Medals']['TOT'][2]) return -1;
	if($a['Medals']['TOT'][2]<$b['Medals']['TOT'][2]) return 1;
	if($a['Medals']['TOT'][3]>$b['Medals']['TOT'][3]) return -1;
	if($a['Medals']['TOT'][3]<$b['Medals']['TOT'][3]) return 1;
	if($a['Code']<$b['Code']) return -1;
	if($a['Code']>$b['Code']) return 1;
	return 0;
}

function odfTotComp($a, $b) {
	if($a['TotMedal'] > $b['TotMedal']) return -1;
	if($a['TotMedal'] < $b['TotMedal']) return 1;
	return 0;
}

function getScoQuals() {
	$pdf=CreateSessionScorecard('ONLINE');
	return (object) array(
		'filename' => 'QUALCARDS',
		'name' => get_text('ScorecardsQual','Tournament'),
		'order' => 40,
		'update' => $pdf->LastUpdate,
		'pdf' => $pdf->Output('', 'S'));
}

function getSchedule($Order=10, $Name='') {
	//error_reporting(E_ERROR);
	require_once('Common/Lib/Fun_Scheduler.php');
	require_once('Common/Lib/Fun_Modules.php');

	$Schedule = new Scheduler();
	$Schedule->Finalists=true;

	if($PageBreaks=getModuleParameter('Schedule', 'PageBreaks')) {
		$Schedule->PageBreaks=explode(',', $PageBreaks);
	}

	$pdf = $Schedule->getSchedulePDF();

	return (object) array(
		'filename' => 'SCHEDULE.pdf',
		'name' => $Name ? $Name : $pdf->Title.($pdf->Version ? ' - v'.$pdf->Version : ''),
		'order' => $Order ? $Order : 10,
		'update' =>  date('Y-m-d H:i:s',$Schedule->SchedVersionDate ? strtotime($Schedule->SchedVersionDate) : time()),
		'pdf' => $pdf->Output('', 'S'));
}

function getFop($Order=10, $Name='') {
	//error_reporting(E_ERROR);
	require_once('Common/Lib/Fun_Scheduler.php');
	require_once('Common/Lib/Fun_Modules.php');

	$FopLocations=Get_Tournament_Option('FopLocations', array());
	$DaysToPrint=array();
	foreach(range(0,  intval(($_SESSION['ToWhenToUTS']-$_SESSION['ToWhenFromUTS'])/86400)) as $n) {
		$DaysToPrint[]=date('Y-m-d', $_SESSION['ToWhenFromUTS'] + $n*86400);
	}

	// defines the Locations (these will be printed on a single page)
	$LocationsToPrint=array();
	if(!$FopLocations) {
		// prints everything in a single location
		$tmp=new stdClass();
		$tmp->Loc='';
		$tmp->Tg1=1;
		$tmp->Tg2=99999;
		$LocationsToPrint[]=$tmp;
	} else {
		$LocationsToPrint=$FopLocations;
	}

	$Scheduler=new Scheduler();
	$Scheduler->SplitLocations=true;
	$Scheduler->DaysToPrint=$DaysToPrint;
	$Scheduler->LocationsToPrint=$LocationsToPrint;

	$pdf=$Scheduler->FOP(false);

	return (object) array(
		'filename' => 'FOP.pdf',
		'name' => $Name ? $Name : $pdf->Title.($pdf->Version ? ' - v'.$pdf->Version : ''),
		'order' => $Order ? $Order : 10,
		'update' => date('Y-m-d H:i:s',$Scheduler->FopVersionDate ? strtotime($Scheduler->FopVersionDate) : time()),
		'pdf' => $pdf->Output('', 'S'));
}

function getGenericPdf($File, $Name, $Order=10) {
	//error_reporting(E_ERROR);

	return (object) array(
		'filename' => $File['name'],
		'name' => $Name,
		'order' => $Order ? $Order : 10,
		'update' => date('Y-m-d H:i:s'),
		'pdf' => file_get_contents($File['tmp_name']));
}
