<?php
	require_once('Common/Lib/Fun_DateTime.inc.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Partecipants/Fun_Partecipants.local.inc.php');

/**
 * Ritorna un array associativo con la lista delle sessioni
 * per la gara aperta.
 *
 * l'underscore serve perchè ora c'è la funzione GetSessions() in Fun_Sessions.inc.php
 *
 * @return mixed: elenco delle sessioni in caso di successo, false altrimenti
 */
	function getSessions_()
	{
		$sessions=array('0'=>'--');

		$ses=GetSessions('Q');

		foreach ($ses as $s)
		{
			$sessions[$s->SesOrder]=$s->SesOrder;
		}

		return $sessions;
	}

/**
 * Ritorna un array associativo con la lista delle divisioni
 * per la gara aperta
 *
 * @return mixed: elenco delle divisioni in caso di successo, false altrimenti
 */
	function getDivisions()
	{
		$divisions=array(''=>'');

		$query
			= "SELECT "
				. "* "
			. "FROM "
				. "Divisions "
			. "WHERE "
				. "DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "ORDER BY "
				. "DivViewOrder ASC ";
		$rs=safe_r_sql($query);

		if ($rs)
		{
			if (safe_num_rows($rs)>0)
			{
				while ($row=safe_fetch($rs))
					$divisions[$row->DivId]=$row->DivId;
			}
		}
		else
			return false;

		return $divisions;
	}

/**
 * Ritorna un array associativo con la lista delle categorie
 * per la gara aperta
 *
 * @return mixed: elenco delle categorie in caso di successo, false altrimenti
 */
	function getSubClasses()
	{
		$subClasses=array(''=>'');

		$query
			= "SELECT "
				. "* "
			. "FROM "
				. "SubClass "
			. "WHERE "
				. "ScTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "ORDER BY "
				. "ScViewOrder ASC ";
		$rs=safe_r_sql($query);
		//print $query;exit;
		if ($rs)
		{
			if (safe_num_rows($rs)>0)
			{
				while ($row=safe_fetch($rs))
					$subClasses[$row->ScId]=$row->ScId;
			}
		}
		else
			return false;

		return $subClasses;
	}

/**
 * Ritorna un array associativo con la lista delle classi
 * per la gara aperta
 *
 * @return mixed: elenco delle classi in caso di successo, false altrimenti
 */
	function getClasses()
	{
		$classes=array(''=>array('val'=>'','valid'=>''));

		$query
			= "SELECT "
				. "ClId,ClValidClass "
			. "FROM "
				. "Classes "
			. "WHERE "
				. "ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "ORDER BY "
				. "ClViewOrder ASC ";
		$rs=safe_r_sql($query);

		if ($rs)
		{
			if (safe_num_rows($rs)>0)
			{
				while ($row=safe_fetch($rs))
					$classes[$row->ClId]=array('val'=>$row->ClId,'valid'=>$row->ClValidClass);
			}
		}
		else
			return false;

		return $classes;
	}

	function getGenders()
	{
		$genders=array();
		$genders[0]=get_text('ShortMale','Tournament');
		$genders[1]=get_text('ShortFemale','Tournament');

		return $genders;
	}

/**
 * Ritorna un array associativo con la lista degli atleti della gara aperta
 *
 * @param int $id: id della persona nel caso si voglia ritornare solo una persona
 *
 * @return mixed array: elenco delle persone
 */
	function getAthletes($id=null)
	{
		$dtFormat=get_text('DateFmtDB');

		$athletes=array();

		// check the editable ageclasses...
		$EditableClasses=array();
		$sql="select distinct c1.ClId from Classes c1 
			inner join Divisions d1 on d1.DivTournament=c1.ClTournament and (c1.ClDivisionsAllowed='' or find_in_set(d1.DivId, c1.ClDivisionsAllowed))
			inner join Classes c2 on c2.ClTournament=c1.ClTournament 
				and (c2.ClDivisionsAllowed='' or find_in_set(d1.DivId, c2.ClDivisionsAllowed)) 
				and c2.ClId!=c1.ClId 
				and c2.ClSex=c1.ClSex 
				and (c1.ClAgeFrom between c2.ClAgeFrom and c2.ClAgeTo or c1.ClAgeTo between c2.ClAgeFrom and c2.ClAgeTo)
			where c1.ClTournament={$_SESSION['TourId']}
			";
		$q=safe_r_sql($sql);
		while($r=safe_fetch($q)) {
			$EditableClasses[]=$r->ClId;
		}

		$query
			= "SELECT "
				. "e.*, if(year(EnDob)>0, year(ToWhenFrom) - year(EnDob),'') as EnYears, DATE_FORMAT(EnDob,'" . $dtFormat . "') AS Dob, IF(PhPhoto IS NULL OR PhPhoto='',0,1) AS HasPhoto,c.CoCode,c.CoName,c2.CoCode AS CoCode2,c2.CoName AS CoName2,c3.CoCode AS CoCode3,c3.CoName AS CoName3,q.QuSession,SUBSTRING(q.QuTargetNo,2) AS TargetNo,ToWhenFrom "
			. "FROM "
				. "Entries AS e LEFT JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament "
				. "LEFT JOIN Countries AS c2 on e.EnCountry2=c2.CoId AND e.EnTournament=c2.CoTournament "
				. "LEFT JOIN Countries AS c3 on e.EnCountry3=c3.CoId AND e.EnTournament=c3.CoTournament "
				. "INNER JOIN Qualifications AS q ON e.EnId=q.QuId "
				. "INNER JOIN Tournament ON EnTournament=ToId "
				. "LEFT JOIN Photos ON EnId=PhEnId "
			. "WHERE "
				. "e.EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " /*AND EnAthlete=1*/ "
				. (!is_null($id) ? " AND EnId=" . StrSafe_DB($id) : '') . " ";
				//print $query;exit;
		$rs=safe_r_sql($query);

		while ($myRow=safe_fetch($rs)) {

		/*
		 * Se EnYears > 0 allora c'è l'età
		 */
			$ageClassEditable='1';
			$ageClass=$myRow->EnAgeClass;
			if ($myRow->EnYears and !in_array($myRow->EnAgeClass, $EditableClasses)) {
				$ageClassEditable='0';
			}

			$athletes[]=array
			(
				'id'=>$myRow->EnId,
				'code'=> trim($myRow->EnCode),
				'status'=> $myRow->EnStatus,
				'session'=> $myRow->QuSession,
				'target_no'=> trim($myRow->TargetNo),
				'first_name'=> stripslashes(trim($myRow->EnFirstName)),
				'name'=> stripslashes(trim($myRow->EnName)),
				'sex'=>$myRow->EnSex,
				'ctrl_code'=>strtoupper($myRow->EnCtrlCode),
				'dob'=> ($myRow->EnYears ? $myRow->Dob : ''),
				'country_id'=>$myRow->EnCountry,
				'country_code'=>trim($myRow->CoCode),
				'country_name'=>stripslashes(trim($myRow->CoName)),
				'country_id2'=>$myRow->EnCountry2,
				'country_code2'=>$myRow->CoCode2,
				'country_name2'=>stripslashes(trim($myRow->CoName2)),
				'country_id3'=>$myRow->EnCountry3,
				'country_code3'=>$myRow->CoCode3,
				'country_name3'=>stripslashes(trim($myRow->CoName3)),
				'sub_team'=>$myRow->EnSubTeam,
				'division'=>($myRow->EnDivision),
				'age_class'=>($ageClass),
				'age_class_editable'=>$ageClassEditable,
				'class'=>($myRow->EnClass),
				'sub_class'=>($myRow->EnSubClass),
				'has_photo'=>$myRow->HasPhoto,
				'athlete'=>$myRow->EnAthlete
			);
		}

		return $athletes;
	}

	function old_getAthletes($id=null)
	{
		$dtFormat=get_text('DateFmtDB');

		$athletes=array();

		$query
			= "SELECT "
				. "e.*,IF(EnDob!='0000-00-00',DATE_FORMAT(EnDob,'" . $dtFormat . "'),'') AS Dob, IF(PhPhoto IS NULL OR PhPhoto='',0,1) AS HasPhoto,c.CoCode,c.CoName,q.QuSession,SUBSTRING(q.QuTargetNo,2) AS TargetNo,ToWhenFrom "
			. "FROM "
				. "Entries AS e LEFT JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament "
				. "INNER JOIN Qualifications AS q ON e.EnId=q.QuId "
				. "INNER JOIN Tournament ON EnTournament=ToId "
				. "LEFT JOIN Photos ON EnId=PhEnId "
			. "WHERE "
				. "e.EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " /*AND EnAthlete=1*/ "
				. (!is_null($id) ? " AND EnId=" . StrSafe_DB($id) : '') . " ";
				//print $query;exit;
		$rs=safe_r_sql($query);

		if (safe_num_rows($rs)>0)
		{
			while ($myRow=safe_fetch($rs))
			{
				$ageClassEditable='0';
				$ageClass='';

				if (IsValidCF($myRow->EnCtrlCode))
				{
					// Ultime 2 cifre dell'anno
					$__yy = substr($myRow->EnCtrlCode,6,2);

				// Prime 2 cifre dell'anno
					$yy__ = '19';

				/*
					Pivot per discriminare 19xx e 20xx
				*/
					if ($__yy >= '00' && $__yy<='20')
						$yy__='20';

					$yy=intval($yy__ . $__yy);

					$year = substr($myRow->ToWhenFrom,0,4) - $yy;
					//print $year .'-'. $yy. '<br>';

				// Estraggo l'ageclass dalla tabella
					$query
						= "SELECT ClId,ClValidClass "
						. "FROM Classes "
						. "WHERE ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND ClAgeFrom<=" . StrSafe_DB($year) . " AND ClAgeTo>=" . StrSafe_DB($year) . " ";

					$cond="AND ClSex = " . StrSafe_DB($myRow->EnSex) . " ";
					$condUnisex="AND ClSex = -1 ";


					$rsCl = safe_r_sql($query.$cond);

					//print $query . '<br><br>';
					if (safe_num_rows($rsCl)==1)
					{
						$row=safe_fetch($rsCl);
						$ageClass = $row->ClId;
						$ageClassEditable='0';
					}
					elseif (safe_num_rows($rsCl)==0)
					{
						$rsCl = safe_r_sql($query.$condUnisex);
						if (safe_num_rows($rsCl)==1)
						{
							$row=safe_fetch($rsCl);
							$ageClass = $row->ClId;
							$ageClassEditable='0';
						}
						else
						{
							$ageClass = '';
							$ageClassEditable='0';
						}
					}
				}
				else
				{
					$ageClass = $myRow->EnAgeClass;

					$query
						= "SELECT ClId,ClValidClass "
						. "FROM Classes "
						. "WHERE ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND ClId=" . StrSafe_DB($ageClass) . " ";

					$rsCl = safe_r_sql($query);

					if (safe_num_rows($rsCl)==1)
					{
						$row=safe_fetch($rsCl);
						$ageClassEditable='1';
					}
					else
					{
						$ageClassEditable='1';
					}
				}

				$athletes[]=array
				(
					'id'=>$myRow->EnId,
					'code'=> trim($myRow->EnCode),
					'status'=> $myRow->EnStatus,
					'session'=> $myRow->QuSession,
					'target_no'=> trim($myRow->TargetNo),
					'first_name'=> stripslashes(trim($myRow->EnFirstName)),
					'name'=> stripslashes(trim($myRow->EnName)),
					'sex'=>$myRow->EnSex,
					'ctrl_code'=>strtoupper($myRow->EnCtrlCode),
					'country_id'=>$myRow->EnCountry,
					'country_code'=>trim($myRow->CoCode),
					'country_name'=>stripslashes(trim($myRow->CoName)),
					'division'=>($myRow->EnDivision),
					'age_class'=>($ageClass),
					'age_class_editable'=>$ageClassEditable,
					'class'=>($myRow->EnClass),
					'sub_class'=>($myRow->EnSubClass),
					'has_photo'=>$myRow->HasPhoto,
					'athlete'=>$myRow->EnAthlete
				);
			}
		}

		return $athletes;
	}

	function __getAthletes($id=null)
	{
		$athletes=array();

		$query
			= "SELECT "
				. "e.*,IF(PhPhoto IS NULL OR PhPhoto='',0,1) AS HasPhoto,c.CoCode,c.CoName,q.QuSession,SUBSTRING(q.QuTargetNo,2) AS TargetNo,ToWhenFrom "
			. "FROM "
				. "Entries AS e LEFT JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament "
				. "INNER JOIN Qualifications AS q ON e.EnId=q.QuId "
				. "INNER JOIN Tournament ON EnTournament=ToId "
				. "LEFT JOIN Photos ON EnId=PhEnId "
			. "WHERE "
				. "e.EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " /*AND EnAthlete=1*/ "
				. (!is_null($id) ? " AND EnId=" . StrSafe_DB($id) : '') . " ";
				//print $query;exit;
		$rs=safe_r_sql($query);

		if (safe_num_rows($rs)>0)
		{
			while ($myRow=safe_fetch($rs))
			{
				$ageClassEditable='0';
				$ageClass='';

				if (IsValidCF($myRow->EnCtrlCode))
				{
					// Ultime 2 cifre dell'anno
					$__yy = substr($myRow->EnCtrlCode,6,2);

				// Prime 2 cifre dell'anno
					$yy__ = '19';

				/*
					Pivot per discriminare 19xx e 20xx
				*/
					if ($__yy >= '00' && $__yy<='20')
						$yy__='20';

					$yy=intval($yy__ . $__yy);

					$year = substr($myRow->ToWhenFrom,0,4) - $yy;
					//print $year .'-'. $yy. '<br>';

				// Estraggo l'ageclass dalla tabella
					$query
						= "SELECT ClId,ClValidClass "
						. "FROM Classes "
						. "WHERE ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND ClAgeFrom<=" . StrSafe_DB($year) . " AND ClAgeTo>=" . StrSafe_DB($year) . " "
						. "AND ClSex = " . StrSafe_DB($myRow->EnSex) . " ";

					$rsCl = safe_r_sql($query);

					//print $query . '<br><br>';
					if (safe_num_rows($rsCl)==1)
					{
						$row=safe_fetch($rsCl);
						$ageClass = $row->ClId;
						$ageClassEditable='0';
					}
					else
					{

						$ageClass = '';
						$ageClassEditable='0';
					}
				}
				else
				{
					$ageClass = $myRow->EnAgeClass;

					$query
						= "SELECT ClId,ClValidClass "
						. "FROM Classes "
						. "WHERE ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND ClId=" . StrSafe_DB($ageClass) . " ";

					$rsCl = safe_r_sql($query);

					if (safe_num_rows($rsCl)==1)
					{
						$row=safe_fetch($rsCl);
						$ageClassEditable='1';
					}
					else
					{
						$ageClassEditable='1';
					}
				}

				$athletes[]=array
				(
					'id'=>$myRow->EnId,
					'code'=> trim($myRow->EnCode),
					'status'=> $myRow->EnStatus,
					'session'=> $myRow->QuSession,
					'target_no'=> trim($myRow->TargetNo),
					'first_name'=> stripslashes(trim($myRow->EnFirstName)),
					'name'=> stripslashes(trim($myRow->EnName)),
					'sex'=>$myRow->EnSex,
					'ctrl_code'=>strtoupper($myRow->EnCtrlCode),
					'country_id'=>$myRow->EnCountry,
					'country_code'=>trim($myRow->CoCode),
					'country_name'=>stripslashes(trim($myRow->CoName)),
					'division'=>($myRow->EnDivision),
					'age_class'=>($ageClass),
					'age_class_editable'=>$ageClassEditable,
					'class'=>($myRow->EnClass),
					'sub_class'=>($myRow->EnSubClass),
					'has_photo'=>$myRow->HasPhoto,
					'athlete'=>$myRow->EnAthlete
				);
			}
		}

		return $athletes;
	}
?>