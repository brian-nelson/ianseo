<?php
	// tolto perché dovrebbe già essere incluso e fa casino ogni tanto
	// require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/Fun_DateTime.inc.php');
	require_once('Partecipants/Fun_Targets.php');

	define('GROUP_TYPE_NOGROUP',0);
	define('GROUP_TYPE_TARGET',1);
	define('GROUP_TYPE_LETTER',2);
	define('GROUP_TYPE_COUNTRY',3);
	define('GROUP_TYPE_CATEGORY',4);	// div-agecl-cl


/*
	- IsValidCF($CodFiscale)
	Verifica se il codice fiscale passato è ben formato.
	Si appoggia a ValueEven($Carattere),ValueOdd($Carattere) e a ValueMonth($Mese).
	Ritorna true se ok altrimenti false
*/
function IsValidCF($CodFiscale)
{
	$CodFiscale=preg_replace("/[^a-zA-Z0-9]/i","",mb_convert_case($CodFiscale, MB_CASE_UPPER, "UTF-8"));
	// struttura CODFISC : AAAAAA00A00A000A dove A = A-Z e 0 = A-Z0-9
	// infatti nei casi di omocodia (stesso CF per 2 persone diverse)
	// i numeri vengono progressivamente sostituiti da lettere
	// A NOI interessa solo controllare che il CF sia ben formato, non se sono
	// corrette le omocodie, quindi saltiamo a piè pari la procedura di ricostruzione del codice!

	if(preg_match("/^[A-Z]{6}[A-Z0-9]{2}[A-Z]{1}[A-Z0-9]{2}[A-Z]{1}[A-Z0-9]{3}[A-Z]{1}$/i",$CodFiscale))
	{
		$listaControllo = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$cCodice		=$CodFiscale;
		$listaPari		= array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25);
		$listaDispari	= array(1,0,5,7,9,13,15,17,19,21,2,4,18,20,11,3,6,8,12,14,16,10,22,25,24,23);

		$somma = 0;

		for ($i = 0; $i < 15; $i++)
		{
			$c = $cCodice[$i];
			$x = strpos("0123456789", $c);
			if ($x !== FALSE) $c = $listaControllo[$x];
			$x = strpos($listaControllo, $c);

			// i modulo 2 = 0 � dispari perch� iniziamo da 0
			if (($i % 2) == 0)
				 $x = $listaDispari[$x];
			else
				 $x = $listaPari[$x];
			$somma += $x;
		}

		$CHECK= $listaControllo[($somma % 26)];

		if($CHECK==substr($CodFiscale,-1))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function ValueEven($Carattere)
{
	$Tmp = 0 ;
	if(preg_match("/[0-9]/i", $Carattere))
	{
		$Tmp = (ord($Carattere) - ord("0"));
	} elseif(preg_match("/[A-Z]/i",$Carattere))
	{
		$Tmp = (ord(strtoupper($Carattere)) - ord("A"));
	}
	return $Tmp;
}

function ValueOdd($Carattere)
{
	$Tmp = 0 ;
	switch(strtoupper($Carattere))
	{
		case "0":
		case "A":
			$Tmp = 1;
			break;
		case "1":
		case "B":
			$Tmp = 0;
			break;
		case "2":
		case "C":
			$Tmp = 5;
			break;
		case "3":
		case "D":
			$Tmp = 7;
			break;
		case "4":
		case "E":
			$Tmp = 9;
			break;
		case "5":
		case "F":
			$Tmp = 13;
			break;
		case "6":
		case "G":
			$Tmp = 15;
			break;
		case "7":
		case "H":
			$Tmp = 17;
			break;
		case "8":
		case "I":
			$Tmp = 19;
			break;
		case "9":
		case "J":
			$Tmp = 21;
			break;
		case "K":
			$Tmp = 2;
			break;
		case "L":
			$Tmp = 4;
			break;
		case "M":
			$Tmp = 18;
			break;
		case "N":
			$Tmp = 20;
			break;
		case "O":
			$Tmp = 11;
			break;
		case "P":
			$Tmp = 3;
			break;
		case "Q":
			$Tmp = 6;
			break;
		case "R":
			$Tmp = 8;
			break;
		case "S":
			$Tmp = 12;
			break;
		case "T":
			$Tmp = 14;
			break;
		case "U":
			$Tmp = 16;
			break;
		case "V":
			$Tmp = 10;
			break;
		case "W":
			$Tmp = 22;
			break;
		case "X":
			$Tmp = 25;
			break;
		case "Y":
			$Tmp = 24;
			break;
		case "Z":
			$Tmp = 23;
			break;
	}
	return $Tmp;
}


function ValueMonth($Mese)
{
	switch($Mese)
	{
		case 1:
			return "A";
		case 2:
			return "B";
		case 3:
			return "C";
		case 4:
			return "D";
		case 5:
			return "E";
		case 6:
			return "H";
		case 7:
			return "L";
		case 8:
			return "M";
		case 9:
			return "P";
		case 10:
			return "R";
		case 11:
			return "S";
		case 12:
			return "T";
	}
}

function MonthFromLetter($Letter)
{
	switch(strtoupper($Letter))
	{
		case 'A':
			return '01';
		case 'B':
			return '02';
		case 'C':
			return '03';
		case 'D':
			return '04';
		case 'E':
			return '05';
		case 'H':
			return '06';
		case 'L':
			return "07";
		case 'M':
			return "08";
		case 'P':
			return "09";
		case 'R':
			return "10";
		case 'S':
			return "11";
		case 'T':
			return "12";
	}
}

/**
 * Ritorna un array con le persone.
 *
 * @param int $Id: id della persona se si vuole solo una riga
 * @param string $OrderBy: clausola order by
 * @return mixed[]: array con le persone
 */
function GetRows($Id=null,$OrderBy=null,$AllTargets=false)
{
	$ret=array();

	$DefTargets=getTargets();

	if ($OrderBy===null)
	{
		$OrderBy= "QuSession ASC,QuTargetNo ASC ";
	}

	$Errore = 0;

	$Select="";
	if (!$AllTargets)
	{
		$Select
			= "SELECT e.*,IF(EnDob!='0000-00-00',EnDob,'0000-00-00 00:00:00') AS Dob,c.CoCode,c.CoName,c2.CoCode AS CoCode2,c2.CoName AS CoName2,  c3.CoCode AS CoCode3,c3.CoName AS CoName3,"
			. "q.QuSession AS `Session`,SUBSTRING(q.QuTargetNo,2) AS TargetNo,ToWhenFrom,TfName, "
			. "EdEmail "
			. "FROM Entries AS e LEFT JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament "
			. "LEFT JOIN Countries AS c2 ON e.EnCountry2=c2.CoId AND e.EnTournament=c2.CoTournament "
			. "LEFT JOIN Countries AS c3 ON e.EnCountry3=c3.CoId AND e.EnTournament=c3.CoTournament "
			. "LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId "
			. "LEFT JOIN ExtraData ON EdType='E' and EdId=EnId "
			. "INNER JOIN Qualifications AS q ON e.EnId=q.QuId "
			. "INNER JOIN Tournament ON EnTournament=ToId "
			. "WHERE e.EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " /*AND EnAthlete=1*/ "
			. ($Id!='' ? " AND EnId=" . StrSafe_DB($Id) : '') . " "
			. "ORDER BY " . $OrderBy . " ";
	}
	else
	{
		$Select
			= "(SELECT EnId,EnIocCode,EnTournament,EnDivision,EnClass,EnSubClass,EnAgeClass,"
				. "EnCountry,EnSubTeam,EnCountry2,EnCountry3,EnCtrlCode,Dob,"
				. "EnCode,EnName,EnFirstName,EnBadgePrinted,EnAthlete,"
				. "EnSex,EnWChair,EnSitting,EnIndClEvent,EnTeamClEvent,EnIndFEvent,EnTeamFEvent,EnTeamMixEvent,"
				. "EnDoubleSpace,EnPays,EnStatus,EnTargetFace,EnTimestamp,TfName, "
				. "CoCode,CoName,CoCode2,CoName2,CoCode3,CoName3,"
				. "SUBSTRING(AtTargetNo,1,1) AS `Session`,SUBSTRING(AtTargetNo,2) AS TargetNo,ToWhenFrom, EdEmail "
			. "FROM "
				. "AvailableTarget LEFT JOIN ("
					. "SELECT "
						. "EnId,EnIocCode,EnTournament,EnDivision,EnClass,EnSubClass,EnAgeClass,EdEmail,"
						. "EnCountry,EnSubTeam,EnCountry2,EnCountry3,EnCtrlCode,IF(EnDob!='0000-00-00',EnDob,'0000-00-00 00:00:00') AS Dob,"
						. "EnCode,EnName,EnFirstName,EnBadgePrinted,EnAthlete,"
						. "EnSex,EnWChair,EnSitting,EnIndClEvent,EnTeamClEvent,EnIndFEvent,EnTeamFEvent,EnTeamMixEvent,"
						. "EnDoubleSpace,EnPays,EnStatus,EnTargetFace,EnTimestamp,TfName, "
						. "c.CoCode AS CoCode,c.CoName AS CoName,c2.CoCode AS CoCode2,c2.CoName AS CoName2,c3.CoCode AS CoCode3,c3.CoName AS CoName3, q.QuSession AS `Session`,SUBSTRING(q.QuTargetNo,2) AS TargetNo,q.QuTargetNo AS QuTargetNo,ToWhenFrom "
					. "FROM Entries AS e LEFT JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament "
						. "LEFT JOIN Countries AS c2 ON e.EnCountry2=c2.CoId AND e.EnTournament=c2.CoTournament "
						. "LEFT JOIN Countries AS c3 ON e.EnCountry3=c3.CoId AND e.EnTournament=c3.CoTournament "
						. "LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId "
						. "LEFT JOIN ExtraData ON EdType='E' and EdId=EnId "
						. "INNER JOIN Qualifications AS q ON e.EnId=q.QuId "
						. "INNER JOIN Tournament ON EnTournament=ToId "
						. "WHERE e.EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " /*AND EnAthlete=1*/ "
						. ($Id!='' ? " AND EnId=" . StrSafe_DB($Id) : '') . " "
					. "ORDER BY " . $OrderBy . " "
				. ") AS sq ON AtTournament=EnTournament AND AtTargetNo=QuTargetNo "
			. "WHERE AtTournament=" . StrSafe_DB($_SESSION['TourId']) . " /*AND EnAthlete=1*/) "
			//. "ORDER BY " . $OrderBy . ") "

			. "UNION ALL "

			. "(SELECT EnId,EnIocCode,EnTournament,EnDivision,EnClass,EnSubClass,EnAgeClass,"
				. "EnCountry,EnSubTeam,EnCountry2,EnCountry3,EnCtrlCode,IF(EnDob!='0000-00-00',EnDob,'0000-00-00 00:00:00') AS Dob,"
				. "EnCode,EnName,EnFirstName,EnBadgePrinted,EnAthlete,EdEmail,"
				. "EnSex,EnWChair,EnSitting,EnIndClEvent,EnTeamClEvent,EnIndFEvent,EnTeamFEvent,EnTeamMixEvent,"
				. "EnDoubleSpace,EnPays,EnStatus,EnTargetFace,EnTimestamp,TfName, "
				. "c.CoCode AS CoCode,c.CoName AS CoName,c2.CoCode AS CoCode2,c2.CoName AS CoName2,c3.CoCode AS CoCode3,c3.CoName AS CoName3,"
				. "q.QuSession AS `Session`,SUBSTRING(q.QuTargetNo,2) AS TargetNo,ToWhenFrom "

			. "FROM "
				. "Entries LEFT JOIN Countries AS c ON EnCountry=c.CoId AND EnTournament=c.CoTournament "
				. "LEFT JOIN TargetFaces ON EnTournament=TfTournament AND EnTargetFace=TfId "
				. "LEFT JOIN Countries AS c2 ON EnCountry2=c2.CoId AND EnTournament=c2.CoTournament "
				. "LEFT JOIN Countries AS c3 ON EnCountry3=c3.CoId AND EnTournament=c3.CoTournament "
				. "LEFT JOIN ExtraData ON EdType='E' and EdId=EnId "
				. "INNER JOIN Qualifications AS q ON EnId=q.QuId "
				. "INNER JOIN Tournament ON EnTournament=ToId "
				. "WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND QuTargetNo='' "
				. ($Id!='' ? " AND EnId=" . StrSafe_DB($Id) : '') . ") "
				. "ORDER BY " . $OrderBy . " ";
	}
	//print $Select;exit;
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)>0)
	{
		while ($MyRow=safe_fetch($Rs))
		{
			if ($MyRow->EnId!==null)
			{
				if(empty($DefTargets[$MyRow->EnDivision][$MyRow->EnClass])) {
					// the target is missing for this entry... so sets the EnTargetFace to 0
					safe_w_sql("update Entries set EnTargetFace=0 where EnId=$MyRow->EnId");
					$MyRow->EnTargetFace=0;
				} elseif(empty($DefTargets[$MyRow->EnDivision][$MyRow->EnClass][$MyRow->EnTargetFace])) {
					// the assigned target face doesn't exists so resets to the first one (default)
					reset($DefTargets[$MyRow->EnDivision][$MyRow->EnClass]);
					list($TfId, $TfFace) = each($DefTargets[$MyRow->EnDivision][$MyRow->EnClass]);
					safe_w_sql("update Entries set EnTargetFace=$TfId where EnId=$MyRow->EnId");
					$MyRow->EnTargetFace=$TfId;
				}
			}

			$ret[]=array(
				'id' => $MyRow->EnId,
				'ioccode' => $MyRow->EnIocCode,
				'code' => $MyRow->EnCode,
				'status' => $MyRow->EnStatus,
				'session' => $MyRow->Session!=0 ? $MyRow->Session : '',
				'targetno' => $MyRow->TargetNo,
				'firstname' => stripslashes($MyRow->EnFirstName),
				'name' => stripslashes($MyRow->EnName),
				'email' => stripslashes($MyRow->EdEmail),
				'sex_id' => $MyRow->EnSex,
				'sex' =>  $MyRow->EnId!==null ? $MyRow->EnSex==0 ? get_text('ShortMale','Tournament') : get_text('ShortFemale','Tournament') : '',
				'ctrl_code' => $MyRow->EnCtrlCode,
				'dob' => $MyRow->Dob,
				'country_id' => $MyRow->EnCountry,
				'country_code' => $MyRow->CoCode,
				'country_name' => stripslashes($MyRow->CoName),
				'sub_team' => $MyRow->EnSubTeam,
				'country_id2' => $MyRow->EnCountry2,
				'country_code2' => $MyRow->CoCode2,
				'country_name2' => stripslashes($MyRow->CoName2),
				'country_id3' => $MyRow->EnCountry3,
				'country_code3' => $MyRow->CoCode3,
				'country_name3' => stripslashes($MyRow->CoName3),
				'division' => $MyRow->EnDivision,
				'class' => $MyRow->EnClass,
				'ageclass' => $MyRow->EnAgeClass,
				'subclass' => $MyRow->EnSubClass,
				'targetface' => $MyRow->EnTargetFace,
				'targetface_name' => $MyRow->TfName,
				'indcl'=>$MyRow->EnIndClEvent,
				'teamcl'=>$MyRow->EnTeamClEvent,
				'indfin'=>$MyRow->EnIndFEvent,
				'teamfin'=>$MyRow->EnTeamFEvent,
				'mixteamfin'=>$MyRow->EnTeamMixEvent,
				'wc'=>$MyRow->EnWChair,
				'double'=>$MyRow->EnDoubleSpace,
			);
		}
	}

	return $ret;
}

function Params4Recalc($ath)
{
	$indFEvent=$teamFEvent=$country=$div=$cl=null;

	$q="
		SELECT
			EnIndFEvent,EnTeamFEvent,EnCountry,EnDivision,EnClass,EnStatus,QuScore
		FROM
			Entries
			INNER JOIN
				Qualifications
			ON EnId=QuId
		WHERE
			EnId={$ath}
	";

	$rs=safe_r_sql($q);

	if ($rs && safe_num_rows($rs)==1)
	{
		$row=safe_fetch($rs);

		$indFEvent=$row->EnIndFEvent;
		$teamFEvent=$row->EnTeamFEvent;
		$country=$row->EnCountry;
		$div=$row->EnDivision;
		$cl=$row->EnClass;
		$zero=true;
		if ($row->EnStatus<=1)
		{
			$zero=($row->QuScore==0);
		}

		return array($indFEvent,$teamFEvent,$country,$div,$cl,$zero);
	}
	else
		return false;
}

function RecalculateShootoffAndTeams($indFEvent,$teamFEvent,$country,$div,$cl,$zero)
{
	$Errore=0;

	if ($zero)
		return 0;

// scopro se $div e $cl sono per gli atleti
	$q="
		SELECT
			(DivAthlete AND ClAthlete) AS isAth
		FROM
			Divisions
			INNER JOIN
				Classes
			ON DivTournament=ClTournament
		WHERE
			DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (DivAthlete AND ClAthlete)=1
			AND DivId=" . StrSafe_DB($div) . " AND ClId=" . StrSafe_DB($cl) . "
	";
	//print $q.'<br><br>';
	$rs=safe_r_sql($q);

	if ($rs && safe_num_rows($rs)==1)
	{
		$queries=array();

		$date=date('Y-m-d H:i:s');

	// shootoff degli individuali a zero (e reset della RankFinal)
		if ($indFEvent==1)
		{
			$queries[]="
				UPDATE
					Events
					INNER JOIN
						EventClass
					ON EvCode=EcCode AND EvTeamEvent='0' AND EvTournament=EcTournament AND EcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND
					EcDivision=" . StrSafe_DB($div) . " AND EcClass=" . StrSafe_DB($cl) . "
					INNER JOIN
						Individuals
					ON EvCode=IndEvent AND EvTournament=IndTournament AND EvTeamEvent=0 AND EvTournament={$_SESSION['TourId']}
				SET
					EvShootOff='0',
					EvE1ShootOff='0',
					EvE2ShootOff='0',
					IndRankFinal=0,
					IndTimestampFinal='{$date}'
			";
		}
	// shootoff dei team a zero
		if ($teamFEvent==1)
		{
			$queries[]="
				UPDATE
					Events
					INNER JOIN
						EventClass
					ON EvCode=EcCode AND EvTeamEvent='1' AND EvTournament=EcTournament AND EcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND
					EcDivision=" . StrSafe_DB($div) . " AND EcClass=" . StrSafe_DB($cl) . "
				SET
					EvShootOff='0',
					EvE1ShootOff='0',
					EvE2ShootOff='0'
			";
		}

		foreach ($queries as $q)
		{
			//print $q.'<br><br>';
			$rs=safe_w_sql($q);
		}
		set_qual_session_flags();

	// teams
		if (MakeTeams($country, $div . $cl))
		{
			$Errore=1;
			//print 'team error';
		}
		else
		{
			if (MakeTeamsAbs($country,$div,$cl))
			{
				$Errore=1;
				//print 'absteam error';
			}
		}

	}

	//exit;
	return $Errore;
}

function getAllDivCl()
{
	$divs=array('--');
	$cls=array('--');
	$agecls=array('--');

	$q="SELECT DivId FROM Divisions WHERE DivTournament={$_SESSION['TourId']} ORDER BY DivViewOrder ASC";
	$r=safe_r_sql($q);
	if ($r)
	{
		while ($row=safe_fetch($r))
		{
			$divs[]=$row->DivId;
		}
	}

	$q="SELECT ClId FROM Classes WHERE ClTournament={$_SESSION['TourId']} ORDER BY ClViewOrder ASC";
	$r=safe_r_sql($q);
	if ($r)
	{
		while ($row=safe_fetch($r))
		{
			$cls[]=$row->ClId;
			$agecls[]=$row->ClId;
		}
	}

	return array($divs,$agecls,$cls);
}
?>