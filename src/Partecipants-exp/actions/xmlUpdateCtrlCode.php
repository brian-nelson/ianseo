<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Lib/Fun_DateTime.inc.php');
	require_once('Partecipants/Fun_Partecipants.local.inc.php');
	require_once('Partecipants-exp/common/config.inc.php');
	require_once('Partecipants-exp/functions/rows.inc.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');
	require_once('Common/Fun_Various.inc.php');
/****** Controller ******/
	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadWrite, false);

	$tourId=StrSafe_DB($_SESSION['TourId']);

	$error=0;

	$id=(isset($_REQUEST['id']) ? $_REQUEST['id'] : '');
	$ctrlCode=(isset($_REQUEST['ctrl_code']) ? $_REQUEST['ctrl_code'] : '');
	$sex=(isset($_REQUEST['sex']) ? $_REQUEST['sex'] : null);

	$row=(isset($_REQUEST['row']) ? $_REQUEST['row'] : '');
	$col=(isset($_REQUEST['col']) ? $_REQUEST['col'] : '');
	$div=(isset($_REQUEST['div']) ? $_REQUEST['div'] : '');

	$noCalc=(isset($_REQUEST['noCalc']) ? $_REQUEST['noCalc'] : 0);

//	if (!$id || !$ctrlCode || !$row || !$col)
//	{
//		print get_text('CrackError');
//		exit;
//	}

	if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
	{
		//if (IsValidCF($ctrlCode) || ConvertDate($ctrlCode) || strlen($ctrlCode)==0)
		$ctrlCode=ConvertDateLoc($ctrlCode);

		if ($ctrlCode!==false)
		{
			$dob='0000-00-00';
			$cl='';
			$agecl='';
			$dontTouchCl=false;

			if (!empty($ctrlCode))	// la data non è vuota
			{
				$yy=substr($ctrlCode, 0, 4);

				$dob=$ctrlCode;
			// calcolo la classe anagrafica e setto l'altra uguale a lei
				$sql= "SELECT ToWhenFrom FROM Tournament WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
				//print $sql;exit;
				$rs=safe_r_sql($sql);
				$myRow=safe_fetch($rs);
				$ToWhenFrom=$myRow->ToWhenFrom;

				$year = substr($ToWhenFrom,0,4) - $yy;
//print $year;exit;
//				$sql
//					= "SELECT ClId "
//					. "FROM Classes "
//					. "WHERE ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND ClAgeFrom<=" . StrSafe_DB($year) . " AND ClAgeTo>=" . StrSafe_DB($year) . " ";
			// divisione del tipo
				$sql="SELECT EnDivision, EnSex FROM Entries INNER JOIN Divisions ON EnDivision=DivId AND EnTournament=DivTournament WHERE EnId={$id}";
				$r=safe_r_sql($sql);
				$div='';
				if ($r && safe_num_rows($r)==1)
				{
					$tmp=safe_fetch($r);
					$div=$tmp->EnDivision;
					if ($sex===null)
						$sex=$tmp->EnSex;
				}

				$sql = "SELECT DISTINCT ClId, ClValidClass from Classes
						inner join Divisions on DivTournament=ClTournament
							and DivAthlete=ClAthlete
							" . ($div ? " AND DivId='$div'" : '') . "
						where ClTournament={$_SESSION['TourId']}
							AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed))"
					. (!is_null($sex) ? " AND ClSex in (-1, {$sex})" : '')
					. ($year ? " and (ClAthlete!='1' or (ClAgeFrom<=$year and ClAgeTo>=$year))" : '')
//					. ($Clas ? " AND ClId='$Clas'" : '')
					. " order by ClViewOrder, DivViewOrder ";

				$rs=safe_w_sql($sql);

				if (safe_num_rows($rs)) {
					$myRow=safe_fetch($rs);
					$agecl=$myRow->ClId;
					$cl=$myRow->ClId;
					//print $agecl.' '.$cl;exit;
				}

			} else {
				$dontTouchCl=true;
			/*
			 * tiro fuori il vecchio sesso x decidere se resettare o no le classi
			 * se cambia il sesso faccio reset
			 */
				$q="SELECT EnSex FROM Entries WHERE EnId=". StrSafe_DB($id) . " ";
				$r=safe_r_sql($q);
				if ($r && safe_num_rows($r)==1)
				{
					$tmp=safe_fetch($r);
					if ($tmp->EnSex!=$sex)
						$dontTouchCl=false;
				}

			}

			$recalc=false;
			$indFEventOld=$teamFEventOld=$countryOld=$divOld=$clOld=$subClOld=$zeroOld=null;
			//$indFEvent=$teamFEvent=$country=$div=$cl=$zero=null;

			if (!$dontTouchCl && $noCalc==0)
			{
			// se la vecchia classe è diversa ricalcolo spareggi e squadre per la vecchia e la nuova
				$query= "SELECT EnClass FROM Entries WHERE EnId=" . StrSafe_DB($id) . " AND EnClass<>" . StrSafe_DB($cl) . " ";
				//print $query;exit;
				$rs=safe_r_sql($query);

				if ($rs && safe_num_rows($rs)==1)
				{
					$recalc=true;
				// prendo le vecchie impostazioni
					$x=Params4Recalc($id);
					if ($x!==false)
					{
						list($indFEventOld,$teamFEventOld,$countryOld,$divOld,$clOld,$subClOld,$zeroOld)=$x;
					}
				}
			}

			$query = "UPDATE Entries
					SET EnDob=" . StrSafe_DB($dob)
					. (is_null($sex) ? '' : ",  EnSex=" . StrSafe_DB($sex));

				if (!$dontTouchCl || empty($ctrlCode)) {
					$query .=",EnClass='" . $cl ."', EnAgeClass='" .$agecl ."'". ($recalc ? ", EnBadgePrinted=0" : "");
				}

			$rs=safe_w_sql($query . " WHERE EnId=" . StrSafe_DB($id));
			if($EnSelect=GetAccBoothEnWhere($id, true, true)) {
				LogAccBoothQuerry($query . " WHERE  $EnSelect", $_SESSION['TourCode']);
			}

			//print $query;exit;
			checkAgainstLUE($id);
			if ($recalc)
			{
				$indFEvent=$teamFEvent=$country=$div=$cl=$subCl=$zero=null;
				$x=Params4Recalc($id);
				if ($x!==false)
				{
					list($indFEvent,$teamFEvent,$country,$div,$cl,$subCl,$zero)=$x;
				}

			// ricalcolo il vecchio e il nuovo
				RecalculateShootoffAndTeams($indFEventOld,$teamFEventOld,$countryOld,$divOld,$clOld,$subClOld,$zeroOld);
				RecalculateShootoffAndTeams($indFEvent,$teamFEvent,$country,$div,$cl,$subCl,$zero);

			// rank di classe x tutte le distanze
				$q="SELECT ToNumDist FROM Tournament WHERE ToId={$_SESSION['TourId']}";
				$r=safe_r_sql($q);
				$tmpRow=safe_fetch($r);
				for ($i=0; $i<$tmpRow->ToNumDist;++$i)
				{
					CalcQualRank($i,$divOld.$clOld);
					CalcQualRank($i,$div.$cl);
				}

			// individuali assoluti
				MakeIndAbs();

			}
		}
		else
		{
			//print 'xxx';exit;
			$error=1;
		}
	}
	else
	{
		//print 'ppp';exit;
		$error=1;
	}

	$athletes=getAthletes($id);

/****** End Controlloer ******/

/****** Output ******/
	$xmlDoc=new DOMDocument('1.0',PageEncode);
		$xmlRoot=$xmlDoc->createElement('response');
		$xmlDoc->appendChild($xmlRoot);

	// Header
		$xmlHeader=$xmlDoc->createElement('header');
		$xmlRoot->appendChild($xmlHeader);

			$node=$xmlDoc->createElement('error',$error);
			$xmlHeader->appendChild($node);

			$node=$xmlDoc->createElement('row',intval($row));
			$xmlHeader->appendChild($node);

			$node=$xmlDoc->createElement('col',intval($col));
			$xmlHeader->appendChild($node);

	// Atleta
		if (count($athletes)==1)
		{
			$xmlAth=$xmlDoc->createElement('ath');
			$xmlRoot->appendChild($xmlAth);
				foreach ($athletes[0] as $k=>$v)
				{
					$node=$xmlDoc->createElement($k);
					$xmlAth->appendChild($node);
						$cdata=$xmlDoc->createCDATASection(($v!='' ? $v : '#'));
						$node->appendChild($cdata);
				}
		}

	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-type: text/xml; charset=' . PageEncode);

	print $xmlDoc->saveXML();
/****** End Output ******/
?>