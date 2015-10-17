<?php
/*
													- UpdateFieldEventList.php -
	Aggiorna il campo di Events passato in querystring.
*/

	define('debug',false);

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');

	if (!CheckTourSession() ||
		!isset($_REQUEST['New_EvCode']) ||
		!isset($_REQUEST['New_EvEventName']) ||
		!isset($_REQUEST['New_EvProgr']) ||
		!isset($_REQUEST['New_EvMatchMode']) ||
		!isset($_REQUEST['New_EvFinalFirstPhase']) ||
		!isset($_REQUEST['New_EvFinalTargetType']) ||
		!isset($_REQUEST['New_EvTargetSize']) ||
		!isset($_REQUEST['New_EvDistance']))
	{
		print get_text('CrackError');
		exit;
	}

	$Errore=0;
	$xml = '';

	if (!IsBlocked(BIT_BLOCK_TOURDATA))
	{
	// Aggiungo la nuova riga
		$Insert
			= "INSERT INTO Events (EvCode,EvTeamEvent,EvTournament,EvEventName,EvProgr,EvShootOff,EvFinalFirstPhase,EvFinalTargetType,EvTargetSize,EvDistance,EvMatchMode) "
			. "VALUES("
			. StrSafe_DB($_REQUEST['New_EvCode']) . ","
			. StrSafe_DB('1') . ","
			. StrSafe_DB($_SESSION['TourId']) . ","
			. StrSafe_DB($_REQUEST['New_EvEventName']) . ","
			. StrSafe_DB($_REQUEST['New_EvProgr']) . ","
			. StrSafe_DB('0') . ","
			. StrSafe_DB($_REQUEST['New_EvFinalFirstPhase']) . ","
			. StrSafe_DB($_REQUEST['New_EvFinalTargetType']) . ", "
			. StrSafe_DB($_REQUEST['New_EvTargetSize']) . ", "
			. StrSafe_DB($_REQUEST['New_EvDistance']) . ", "
			. StrSafe_DB($_REQUEST['New_EvMatchMode']) . " "
			. ") ";
		$RsIns=safe_w_sql($Insert);
		set_qual_session_flags();

		if (debug) print $Insert . '<br>';

		if (!$RsIns)
		{
			$Errore=1;
		}
		else
		{
		/*
		 *  imposto i parametri delle frecce.
		 *  Sicuramente ho un evento team cumulativo e non mixed perchè il flag del mixed si
		 *  gestisce dopo la creazione dell'evento
		 */
			$MySql
				= "UPDATE "
					. "Events "
				. "SET "
					. "EvElimEnds=4,EvElimArrows=6,EvElimSO=3,EvFinEnds=4,EvFinArrows=6,EvFinSO=3 "
				. "WHERE "
					. "EvTeamEvent=1 AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode=".StrSafe_DB($_REQUEST['New_EvCode']);
			$Rs=safe_w_sql($MySql);


			// le query che seguono mi servono per generare la tendine dinamiche
			$StartPhase = -1;

			$Select
				= "SELECT GrPhase FROM Grids WHERE GrPhase=" . StrSafe_DB(TeamStartPhase) . " AND GrPosition='1' ";
			$RsPh=safe_r_sql($Select);
		// Se la fase iniziale esiste in griglia allora uso quella altrimenti cerco la massima disponibile

			if (!(safe_num_rows($RsPh)==1))
			{
				$Select
					= "SELECT MAX(GrPhase) AS Phase FROM Grids ";
				$RsPh=safe_r_sql($Select);

				if (safe_num_rows($RsPh)==1)
				{
					$Row=safe_fetch($RsPh);
					$StartPhase=$Row->Phase;
				}
			}
			else
				$StartPhase=TeamStartPhase;

			if ($StartPhase!=-1)
			{

				for ($Phase=$StartPhase;$Phase>=2;$Phase/=2)
				{
					$xml
						.='<phase_id>' . $Phase . '</phase_id>'
						. '<phase_name>' . get_text( $Phase . '_Phase') . '</phase_name>' . "\n";
				}
				$xml
					.='<phase_id>0</phase_id>'
					. '<phase_name>---</phase_name>' . "\n";
			}

			$Select
				= "SELECT * FROM Targets ORDER BY TarId ASC ";
			$RsT=safe_r_sql($Select);

			if (safe_num_rows($RsT)>0)
			{
				while ($Row=safe_fetch($RsT))
				{
					$xml
						.='<tar_id>' . $Row->TarId . '</tar_id>'
						. '<tar_descr>' . get_text($Row->TarDescr) . '</tar_descr>' . "\n";
				}
			}

			for($i=0; $i<=1; $i++)
			{
				$xml
					.='<matchmode_id>' . $i . '</matchmode_id>'
					. '<matchmode_descr>' . get_text('MatchMode_' . ($i)) . '</matchmode_descr>' . "\n";
			}

		// Creo la griglia
			$Insert
				= "INSERT INTO TeamFinals (TfEvent,TfMatchNo,TfTournament,TfDateTime) "
				. "SELECT EvCode,GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . "," . StrSafe_DB(date('Y-m-d H:i:s')) . " "
				. "FROM Events INNER JOIN Grids ON GrPhase<=EvFinalFirstPhase AND EvTeamEvent='1' "
				. "AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
				. "WHERE EvCode=" . StrSafe_DB($_REQUEST['New_EvCode']) . " ";
			if($_REQUEST['New_EvFinalFirstPhase']!=0)
				$RsIns=safe_w_sql($Insert);

			if (debug) print $Insert . '<br>';

			if (!$RsIns)
			{
				$Errore=1;
			}
		}
	}
	else
	{
		$Errore=1;
	}
	if (!debug)
		header('Content-Type: text/xml');
	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<confirm_msg>' . get_text('MsgAreYouSure') . '</confirm_msg>' . "\n";
	print '<new_evcode>' . $_REQUEST['New_EvCode'] . '</new_evcode>' . "\n";
	print '<new_eveventname>' . $_REQUEST['New_EvEventName'] . '</new_eveventname>' . "\n";
	print '<new_evprogr>' . $_REQUEST['New_EvProgr'] . '</new_evprogr>' . "\n";
	print '<new_evmatchmode>' . $_REQUEST['New_EvMatchMode'] . '</new_evmatchmode>' . "\n";
	print '<new_evfinalfirstphase>' . $_REQUEST['New_EvFinalFirstPhase'] . '</new_evfinalfirstphase>' . "\n";
	print '<new_evtargetsize>' . $_REQUEST['New_EvTargetSize'] . '</new_evtargetsize>' . "\n";
	print '<new_evdistance>' . $_REQUEST['New_EvDistance'] . '</new_evdistance>' . "\n";
	print '<new_evfinaltargettype>' . $_REQUEST['New_EvFinalTargetType'] . '</new_evfinaltargettype>' . "\n";
	print $xml;
	print '</response>' . "\n";
?>