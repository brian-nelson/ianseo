<?php
/*
													- Save_Par.php -
	Fa gli update oppure aggiunge una riga in Partecipants.php
*/

	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once dirname(dirname(__FILE__)) . '/Qualification/Fun_Qualification.local.inc.php';
	require_once dirname(dirname(__FILE__)) . '/Partecipants/Fun_Partecipants.local.inc.php';

	if (!CheckTourSession()) {
		print get_text('CrackError');
		exit;
	}
    checkACL(AclParticipants, AclReadWrite, false);

	$Errore=0;
	$CoId = 0;
	$CoId2=0;
	$EnSubTeam=0;
	$EnSex = 0;
	$dob='0000-00-00';

	$Id=$_REQUEST['d_e_EnId'];

	$xml='';
	if (!IsBlocked(BIT_BLOCK_PARTICIPANT))
	{
	/*
	 * Prima cosa gestisco la nazione di appartenenza
	 */

	// Cerco il codice della nazione nel db
		if (strlen($_REQUEST['d_c_CoCode'])!=0)
		{
			$Select
				= "SELECT CoId,CoName "
				. "FROM Countries "
				. "WHERE CoCode=" . StrSafe_DB($_REQUEST['d_c_CoCode']) . " AND CoTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
			$Rs=safe_r_sql($Select);
			if (debug)
				print $Select . '<br><br>';

			if ($Rs)
			{
			// la nazione esiste
				if (safe_num_rows($Rs)==1)
				{
					$MyRow=safe_fetch($Rs);
					$CoId=$MyRow->CoId;
					$CoName=$MyRow->CoName;
				}
			// la nazione non esiste quindi inserisco il suo codice
				elseif(safe_num_rows($Rs)==0)
				{
					$Insert
						= "INSERT INTO Countries (CoTournament,CoCode) "
						. "VALUES("
						. StrSafe_DB($_SESSION['TourId']) . ","
						. StrSafe_DB($_REQUEST['d_c_CoCode']) . " "
						. ")";
					$RsIns=safe_w_sql($Insert);
					if (debug)
						print $Insert . '<br><br>';

				// estraggo l'ultimo id
					$CoId=safe_w_last_id();

				}
			// errore mega bau!
				else
					$Errore=1;
			}
		// errore mega bau!
			else
				$Errore=1;
		}

	// In ogni caso salvo il nome breve della nazione all'id $CoId
		$Update
			= "UPDATE Countries SET "
			. "CoName=" . StrSafe_DB($_REQUEST['d_c_CoName']) . " "
			. "WHERE CoId=" . StrSafe_DB($CoId) . " AND CoTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
		$Rs=safe_w_sql($Update);
		$NomeCambiato=false;

	/*
	 * Se l'update precedente ha un affected_rows==1 significa che il nome era diverso
	 * e setto il flag $NomeCambiato
	 */
		if (safe_W_affected_rows()==1)
			$NomeCambiato=true;

		if (debug)
			print $Update . '<br><br>';


		if ($NomeCambiato)
		{
		/*
		 * Devo estrarre le persone con la stessa nazione perchè se il nome è cambiato occorre
		 * aggiornare la tabella
		 */
			$Select
				= "SELECT EnId "
				. "FROM Entries "
				. "WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
				. "AND EnCountry=" . StrSafe_DB($CoId) . " ";
			$Rs=safe_r_sql($Select);
			if (debug)
				print  $Select . '<br><br>';
			$xml='';

			if (safe_num_rows($Rs)>0)
			{
				while ($MyRow=safe_fetch($Rs))
				{
					$xml.='<other_en>' . $MyRow->EnId . '</other_en>';
				}
			}
		}

	// stessa cosa di prima ma con la nazione2
		if (strlen($_REQUEST['d_c_CoCode2'])!=0)
		{
			$Select
				= "SELECT CoId,CoName "
				. "FROM Countries "
				. "WHERE CoCode=" . StrSafe_DB($_REQUEST['d_c_CoCode2']) . " AND CoTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
			$Rs=safe_r_sql($Select);
			if (debug)
				print $Select . '<br><br>';

			if ($Rs)
			{
			// la nazione esiste
				if (safe_num_rows($Rs)==1)
				{
					$MyRow=safe_fetch($Rs);
					$CoId2=$MyRow->CoId;
					$CoName2=$MyRow->CoName;
				}
			// la nazione non esiste quindi inserisco il suo codice
				elseif(safe_num_rows($Rs)==0)
				{
					$Insert
						= "INSERT INTO Countries (CoTournament,CoCode) "
						. "VALUES("
						. StrSafe_DB($_SESSION['TourId']) . ","
						. StrSafe_DB($_REQUEST['d_c_CoCode2']) . " "
						. ")";
					$RsIns=safe_w_sql($Insert);
					if (debug)
						print $Insert . '<br><br>';

				// estraggo l'ultimo id
					$CoId2=safe_w_last_id();

				}
			// errore mega bau!
				else
					$Errore=1;
			}
		// errore mega bau!
			else
				$Errore=1;
		}

	// In ogni caso salvo il nome breve della nazione all'id $CoId
		$Update
			= "UPDATE Countries SET "
			. "CoName=" . StrSafe_DB($_REQUEST['d_c_CoName2']) . " "
			. "WHERE CoId=" . StrSafe_DB($CoId2) . " AND CoTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
		$Rs=safe_w_sql($Update);
		$NomeCambiato=false;

	/*
	 * Se l'update precedente ha un affected_rows==1 significa che il nome era diverso
	 * e setto il flag $NomeCambiato
	 */
		if (safe_W_affected_rows()==1)
			$NomeCambiato=true;

		if (debug)
			print $Update . '<br><br>';


		if ($NomeCambiato)
		{
		/*
		 * Devo estrarre le persone con la stessa nazione perchè se il nome è cambiato occorre
		 * aggiornare la tabella
		 */
			$Select
				= "SELECT EnId "
				. "FROM Entries "
				. "WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
				. "AND EnCountry2=" . StrSafe_DB($CoId2) . " ";
			$Rs=safe_r_sql($Select);
			if (debug)
				print  $Select . '<br><br>';
			$xml='';

			if (safe_num_rows($Rs)>0)
			{
				while ($MyRow=safe_fetch($Rs))
				{
					$xml.='<other_en>' . $MyRow->EnId . '</other_en>';
				}
			}
		}



	/*
	 * Sesso  e dob.
	 */
	// Uso quello che proviene dall'hidden. Se c'è anche iil codice fiscale buono lo calcolo con quello
		$EnSex=$_REQUEST['d_e_EnSex'];
		$dob='0000-00-00';
		$ctrlCode=ConvertDateLoc($_REQUEST['d_e_EnCtrlCode']);

		if ($ctrlCode!==false)
		{
			$dob=$ctrlCode;
		}


	/*
	 * In base al valore dell'id decido se aggiungere o aggiornare
	 *
	 * 1) Scrivo in Entries
	 * 2) Se aggiungo, creo la riga anche in Qualifications
	 * 3) Scrivo la sessione in Qualifications
	 */

		// recupero dell'indicazione se atleta in div e clas con la div e clas di gara
		$t=safe_r_sql("SELECT"
			." DivAthlete and ClAthlete as Athlete  "
			."FROM "
			." Divisions "
			." INNER JOIN Classes on DivTournament=ClTournament "
			."WHERE "
			." DivTournament={$_SESSION['TourId']} "
			." AND DivId=". StrSafe_DB(trim($_REQUEST['d_e_EnDivision']))
			." AND ClID=" . StrSafe_DB(trim($_REQUEST['d_e_EnClass'])));
		$EnAthlete = ($u=safe_fetch($t) and $u->Athlete);


		$Op=($Id!=0 ? 'Up' : 'Ins');

		if (debug)
			print 'Op = ' . $Op . '<br><br>';

		//TODO qui bisognerebbe estrarre la vecchia classe gara x fare l'aggiornamento della rank e delle squadre secondo EventClass (vedi todo(*) più avanti)

		$recalc=false;
		$indFEventOld=$teamFEventOld=$countryOld=$divOld=$clOld=$subClOld=$zeroOld=null;
		$indFEvent=$teamFEvent=$country=$div=$cl=$subCl=$zero=null;

		if ($Id!=0)
		{
		// se la vecchia classe o divisione è diversa ricalcolo spareggi e squadre per la vecchia e la nuova
			$query= "SELECT EnClass FROM Entries WHERE EnId=" . StrSafe_DB($Id) . " AND (EnClass<>" . StrSafe_DB($_REQUEST['d_e_EnClass']) . " OR EnDivision<>" . StrSafe_DB($_REQUEST['d_e_EnDivision']) . ") ";
			//print $query;exit;
			$rs=safe_r_sql($query);
			if ($rs && safe_num_rows($rs)==1)
			{
				$recalc=true;
			// prendo le vecchie impostazioni
				$x=Params4Recalc($Id);
				if ($x!==false)
				{
					list($indFEventOld,$teamFEventOld,$countryOld,$divOld,$clOld,$subClOld,$zeroOld)=$x;
				}
			}
		}
		else
		{
			$recalc=true;
		}

	// 1)
		$Insert
			= "INSERT INTO Entries (EnId,EnTournament,EnDivision,EnClass,EnAthlete,EnSubClass,EnAgeClass,EnCountry,EnSubTeam,EnCountry2,EnDob,EnCode,EnName,EnFirstName,EnSex,EnStatus,EnTargetFace) "
			. "VALUES("
			. ($Id!=0 ? StrSafe_DB($Id) : "''") . ","
			. StrSafe_DB($_SESSION['TourId']) . ","
			. StrSafe_DB(trim($_REQUEST['d_e_EnDivision'])) . ","
			. StrSafe_DB(trim($_REQUEST['d_e_EnClass'])) . ","
			. StrSafe_DB($EnAthlete) . ","
			. StrSafe_DB(trim($_REQUEST['d_e_EnSubClass'])) . ","
			. StrSafe_DB(trim($_REQUEST['d_e_EnAgeClass'])) . ","
			. StrSafe_DB($CoId) . ","
			. StrSafe_DB($_REQUEST['d_e_EnSubTeam']) . ","
			. StrSafe_DB($CoId2) . ","
			. StrSafe_DB(trim($dob)) . ","
			. StrSafe_DB(trim($_REQUEST['d_e_EnCode'])) . ","
			. StrSafe_DB(trim($_REQUEST['d_e_EnName'])) . ","
			. StrSafe_DB(trim($_REQUEST['d_e_EnFirstName'])) . ","
			. StrSafe_DB($EnSex) . ","
			. StrSafe_DB($_REQUEST['d_e_EnStatus']) . ","
			. StrSafe_DB($_REQUEST['d_e_EnTargetFace']) . ""
			. ") ON DUPLICATE KEY UPDATE "
			. "EnDivision=" . StrSafe_DB(trim($_REQUEST['d_e_EnDivision'])) . ","
			. "EnClass=" . StrSafe_DB(trim($_REQUEST['d_e_EnClass'])) . ","
			. "EnAthlete=" . StrSafe_DB($EnAthlete) . ","
			. "EnSubClass=" . StrSafe_DB(trim($_REQUEST['d_e_EnSubClass'])) . ","
			. "EnAgeClass=" . StrSafe_DB(trim($_REQUEST['d_e_EnAgeClass'])) . ","
			. "EnCountry=" . StrSafe_DB($CoId) . ","
			. "EnSubTeam=" . StrSafe_DB($_REQUEST['d_e_EnSubTeam']) . ","
			. "EnCountry2=" . StrSafe_DB($CoId2) . ","
			. "EnDob=" . StrSafe_DB($dob) . ","
			. "EnCode=" . StrSafe_DB(trim($_REQUEST['d_e_EnCode'])) . ","
			. "EnName=" . StrSafe_DB(trim($_REQUEST['d_e_EnName'])) . ","
			. "EnFirstName=" . StrSafe_DB(trim($_REQUEST['d_e_EnFirstName'])) . ","
			. "EnSex=" . StrSafe_DB($EnSex) . ","
			. "EnStatus=" . StrSafe_DB($_REQUEST['d_e_EnStatus']) . " " . ","
			. "EnTargetFace=" . StrSafe_DB($_REQUEST['d_e_EnTargetFace']) . " ";
//print $Insert;exit;
		$Rs=safe_w_sql($Insert);
		if (debug)
			print $Insert . '<br><br>';

		if (!$Rs)
			$Errore=1;

		if ($recalc)
		{
			$x=Params4Recalc($Id);
			if ($x!==false)
			{
				list($indFEvent,$teamFEvent,$country,$div,$cl,$subCl,$zero)=$x;
			}

		// ricalcolo il vecchio e il nuovo
			if (!is_null($indFEvent))
				RecalculateShootoffAndTeams($indFEventOld,$teamFEventOld,$countryOld,$divOld,$clOld,$subClOld,$zeroOld);
			RecalculateShootoffAndTeams($indFEvent,$teamFEvent,$country,$div,$cl,$subCl,$zero);

		// rank di classe x tutte le distanze
			$q="SELECT ToNumDist FROM Tournament WHERE ToId={$_SESSION['TourId']}";
			$r=safe_r_sql($q);
			$tmpRow=safe_fetch($r);
			for ($i=0; $i<$tmpRow->ToNumDist;++$i)
			{
				if (!is_null($indFEvent))
					CalcQualRank($i,$divOld.$clOld);
				CalcQualRank($i,$div.$cl);
			}

			MakeIndAbs();

		}
	}
	else
		$Errore=1;

	if ($Errore==0)
	{
		if ($Id==0)
		{
			$Id=safe_w_last_id();

		// aggiungo la riga in Qualifications
			$Insert
				= "INSERT INTO Qualifications (QuId,QuSession) "
				. "VALUES("
				. StrSafe_DB($Id) . ","
				. "0"
				. ") ";
			$Rs=safe_w_sql($Insert);
			if (debug)
				print $Insert . '<br><br>';

			if (!$Rs)
				$Errore=1;
		}

		//TODO (*) qui andrebbe il codice per aggiornare la rank e le squadre secondo EventClass se $Id!=0
	// In ogni caso salvo la sessione del tizio
		$Where="";
		$Update = "UPDATE Qualifications
			SET QuSession=" . StrSafe_DB($_REQUEST['d_q_QuSession']) . ",
				QuTargetNo=" . StrSafe_DB($_REQUEST['d_q_QuSession'] . $_REQUEST['d_q_QuTargetNo']) . ",
				QuTarget=" . intval($_REQUEST['d_q_QuTargetNo']) . ",
				QuLetter=" . StrSafe_DB(substr($_REQUEST['d_q_QuTargetNo']), -1) . ",
				QuTimestamp=QuTimestamp
			WHERE QuId=" . StrSafe_DB($Id) . " ";
		safe_w_sql($Update);
		if(safe_w_affected_rows()) {
			safe_w_sql("Update Entries set EnTimestamp='".date('Y-m-d H:i:s')."' where EnId={$Id}");
		}
		if (debug)
			print $Update . '<br><br>';

			if (!$Rs)
			$Errore=1;

	}

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' ;
	print '<PARAM>';
	print '<confirm_msg1><![CDATA[' . get_text('Archer') . ']]></confirm_msg1>';
	print '<confirm_msg2><![CDATA[' . get_text('Country') . ']]></confirm_msg2>';
	print '<confirm_msg3><![CDATA[' . get_text('OpDelete','Tournament') . ']]></confirm_msg3>';
	print '<confirm_msg4><![CDATA[' . get_text('MsgAreYouSure') . ']]></confirm_msg4>';
	print '</PARAM>' ;
	print '<error>' . $Errore . '</error>';
	print '<op>' . $Op . '</op>' ;
	print GetRows($Id);
	print $xml;
	print '</response>' ;
?>