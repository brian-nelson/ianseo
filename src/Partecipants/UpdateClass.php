<?php
/*
													- UpdateClass.php -
	Serve ad aggiornare la classe e la classe gara
*/

define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once dirname(dirname(__FILE__)).'/Qualification/Fun_Qualification.local.inc.php';
	require_once('Partecipants/Fun_Partecipants.local.inc.php');

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadWrite,false);

	$Errore=0;

	if (!isset($_REQUEST['EnId']) || !isset($_REQUEST['d_e_EnAgeClass']) || !isset($_REQUEST['d_e_EnClass']))
		$Errore=1;

	if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
	{
		$Rs=NULL;
		$Row=NULL;
		if ($Errore==0)
		{
		// verifico che l'ageclass esista (se è diversa da '')
			if (trim($_REQUEST['d_e_EnAgeClass'])!='')
			{
				$Select
					= "SELECT * FROM Classes WHERE ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND ClId=" . StrSafe_DB($_REQUEST['d_e_EnAgeClass']) . " ";
				$Rs=safe_r_sql($Select);

				if (!$Rs)
					$Errore=1;

				if (debug)
					print $Select . '<br><br>';
			}

			if ($Errore==0)
			{
				if ($Rs)
				{
					if (safe_num_rows($Rs)==1)
					{
					// verifico che la classe sia tra quelle ammesse (se diversa da '')
						$Row=safe_fetch($Rs);
						if (!(trim($_REQUEST['d_e_EnClass'])=='' || (trim($_REQUEST['d_e_EnClass'])!='' && strpos($Row->ClValidClass,$_REQUEST['d_e_EnClass'])!==false)))
							$Errore=1;
					}
					else
						$Errore=1;
				}
			}
		}
	}
	else
		$Errore=1;

	if ($Errore==0)
	{
		$recalc=false;
		$indFEventOld=$teamFEventOld=$countryOld=$divOld=$clOld=$subClOld=$zeroOld=null;
		$indFEvent=$teamFEvent=$country=$div=$cl=$subCl=$zero=null;

	// se la vecchia classe è diversa ricalcolo spareggi e squadre per la vecchia e la nuova
		$query= "SELECT EnClass FROM Entries WHERE EnId=" . StrSafe_DB($_REQUEST['EnId']) . " AND EnClass<>" . StrSafe_DB($_REQUEST['d_e_EnClass']) . " ";
		//print $query;exit;
		$rs=safe_r_sql($query);
		if ($rs && safe_num_rows($rs)==1)
		{
			$recalc=true;
		// prendo le vecchie impostazioni
			$x=Params4Recalc($_REQUEST['EnId']);
			if ($x!==false)
			{
				list($indFEventOld,$teamFEventOld,$countryOld,$divOld,$clOld,$subClOld,$zeroOld)=$x;
			}
		}

		$Update
			= "UPDATE Entries SET "
			. "EnClass=" . StrSafe_DB($_REQUEST['d_e_EnClass']) . ","
			. "EnAgeClass=" . StrSafe_DB($_REQUEST['d_e_EnAgeClass']) . " "
			. "WHERE EnId=" . StrSafe_DB($_REQUEST['EnId']) . " ";
		$RsUp=safe_w_sql($Update);

	// devo capire se il tipo è atleta oppure no.
		$EnAthlete=1;
		$query
			= "SELECT EnDivision,EnClass FROM Entries WHERE EnId=" . StrSafe_DB($_REQUEST['EnId']) . " ";
		$rs=safe_r_sql($query);
		$tmp=null;
		if ($rs && safe_num_rows($rs)==1)
		{
			$tmp=safe_fetch($rs);

			// recupero dell'indicazione se atleta in div e clas con la div e clas di gara
			$query
				= "SELECT"
				. " DivAthlete and ClAthlete as Athlete  "
				. "FROM "
				. " Divisions "
				. " INNER JOIN Classes on DivTournament=ClTournament "
				. "WHERE "
				. " DivTournament={$_SESSION['TourId']} "
				. " AND DivId=". StrSafe_DB($tmp->EnDivision)
				. " AND ClID=" . StrSafe_DB($tmp->EnClass);
				//print $query;exit;

			$t=safe_r_sql($query);
			$EnAthlete = intval(($u=safe_fetch($t) and $u->Athlete));

		}

		$Update
			= "UPDATE Entries SET "
			. "EnAthlete=" . $EnAthlete . " "
			. "WHERE EnId=" . StrSafe_DB($_REQUEST['EnId']) . " ";
			//print $Update;Exit;
		$RsUp=safe_w_sql($Update);

		if (debug)
			print $Update . '<br><br>';

		if (!$RsUp)
		{
			$Errore=1;
		}
		else
		{
			if ($recalc)
			{
				$x=Params4Recalc($_REQUEST['EnId']);
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

			// individuale abs
				MakeIndAbs();

			}
		}

	}
	if (!debug)
		header('Content-Type: text/xml');

	print '<response>';
	print '<error>' . $Errore . '</error>';
	print '<id>' . $_REQUEST['EnId'] . '</id>';
	print '</response>';

?>