<?php
/*
													- UpdateFieldEventList.php -
	Aggiorna il campo di Events passato in querystring.
*/

	define('debug',false);

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Sessions.inc.php');

	if (!CheckTourSession() ||
		!isset($_REQUEST['New_EvCode']) ||
		!isset($_REQUEST['New_EvEventName']) ||
		!isset($_REQUEST['New_EvProgr']) ||
		!isset($_REQUEST['New_EvMatchMode']) ||
		!isset($_REQUEST['New_EvFinalFirstPhase']) ||
		!isset($_REQUEST['New_EvFinalTargetType']) ||
		!isset($_REQUEST['New_EvTargetSize']) ||
		!isset($_REQUEST['New_EvDistance']) ||
		!isset($_REQUEST['New_EvElim1']) ||
		!isset($_REQUEST['New_EvElim2']))
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
			= "INSERT INTO Events (EvCode,EvTeamEvent,EvTournament,EvEventName,EvProgr,EvShootOff,EvFinalFirstPhase,EvFinalTargetType,EvTargetSize,EvDistance,EvElim1,EvElim2,EvMatchMode) "
			. "VALUES("
			. StrSafe_DB($_REQUEST['New_EvCode']) . ","
			. StrSafe_DB('0') . ","
			. StrSafe_DB($_SESSION['TourId']) . ","
			. StrSafe_DB($_REQUEST['New_EvEventName']) . ","
			. StrSafe_DB($_REQUEST['New_EvProgr']) . ","
			. StrSafe_DB('0') . ","
			. StrSafe_DB($_REQUEST['New_EvFinalFirstPhase']) . ","
			. StrSafe_DB($_REQUEST['New_EvFinalTargetType']) . ", "
			. StrSafe_DB($_REQUEST['New_EvTargetSize']) . ", "
			. StrSafe_DB($_REQUEST['New_EvDistance']) . ", "
			. StrSafe_DB($_REQUEST['New_EvElim1']) . ", "
			. StrSafe_DB($_REQUEST['New_EvElim2']) . ", "
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
			$values=array
			(
				0 => "EvElimEnds=5,EvElimArrows=3,EvElimSO=1,EvFinEnds=5,EvFinArrows=3,EvFinSO=1 ",
				1 => "EvElimEnds=5,EvElimArrows=3,EvElimSO=1,EvFinEnds=5,EvFinArrows=3,EvFinSO=1 "
			);

			$MySql
				= "UPDATE "
					. "Events "
				. "SET "
					. $values[$_REQUEST['New_EvMatchMode']]
				. "WHERE "
					. "EvTeamEvent=0 AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode=" . StrSafe_DB($_REQUEST['New_EvCode']);
			$Rs=safe_w_sql($MySql);

			// le query che seguono mi servono per generare la tendine dinamiche
			$Select
				= "SELECT MAX(GrPhase) AS Phase FROM Grids ";
			$RsPh=safe_r_sql($Select);

			if (safe_num_rows($RsPh)==1)
			{
				$Row=safe_fetch($RsPh);

				for ($Phase=$Row->Phase;$Phase>=2;$Phase/=2)
				{
					if($Phase==24){
						$xml
							.='<phase_id>' . 32 . '</phase_id>'
							. '<phase_name>' . get_text( '32_Phase') . '</phase_name>' . "\n";
					}
					$xml
						.='<phase_id>' . $Phase . '</phase_id>'
						. '<phase_name>' . get_text( $Phase . '_Phase') . '</phase_name>' . "\n";
					if($Phase==24) $Phase=32;
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

			/*$Select
				= "SELECT TtElimination "
				. "FROM Tournament INNER JOIN Tournament*Type ON ToType=TtId AND ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";*/
			$Select
				= "SELECT ToElimination AS TtElimination "
				. "FROM Tournament WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
			$RsElim=safe_r_sql($Select);

			$Elim=0;
			if (safe_num_rows($RsElim)==1)
			{
				$Row=safe_fetch($RsElim);
				$Elim=$Row->TtElimination;

			}

			$xml
				.='<elim>' . $Elim . '</elim>';


			for($i=0; $i<=2; $i++)
			{
				$xml
					.='<elim_id>' . $i . '</elim_id>'
					. '<elim_descr>' . get_text('Eliminations_' . ($i)) . '</elim_descr>';
			}

			$xml
				.='<elim1>' . $_REQUEST['New_EvElim1'] . '</elim1>'
				. '<elim2>' . $_REQUEST['New_EvElim2'] . '</elim2>'. "\n";

			for($i=0; $i<=1; $i++)
			{
				$xml
					.='<matchmode_id>' . $i . '</matchmode_id>'
					. '<matchmode_descr>' . get_text('MatchMode_' . ($i)) . '</matchmode_descr>' . "\n";
			}

		// creo le griglie delle eliminatorie
			for ($i=1;$i<=2;++$i)
			{
				if ($_REQUEST['New_EvElim'.$i]>0)
				{
					CreateElimRows($_REQUEST['New_EvCode'],$i);
				}
			}


		// Creo la griglia
			$Insert
				= "INSERT INTO Finals (FinEvent,FinMatchNo,FinTournament,FinDateTime) "
				. "SELECT EvCode,GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . "," . StrSafe_DB(date('Y-m-d H:i:s')) . " "
				. "FROM Events INNER JOIN Grids ON GrPhase<=if(EvFinalFirstPhase=24, 32, IF(EvFinalFirstPhase=48,64, EvFinalFirstPhase)) AND EvTeamEvent='0' "
				. "AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
				. "WHERE EvCode=" . StrSafe_DB($_REQUEST['New_EvCode']) . " ";
			if($_REQUEST['New_EvFinalFirstPhase']!=0)
				$RsIns=safe_r_sql($Insert);

			if (debug) print $Insert . '<br>';

			if (!$RsIns)
			{
				$Errore=1;
			}
		}
	}
	else
		$Errore=1;

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
	print '<new_evfinaltargettype>' . $_REQUEST['New_EvFinalTargetType'] . '</new_evfinaltargettype>' . "\n";
	print '<new_evtargetsize>' . $_REQUEST['New_EvTargetSize'] . '</new_evtargetsize>' . "\n";
	print '<new_evdistance>' . $_REQUEST['New_EvDistance'] . '</new_evdistance>' . "\n";
	print $xml;
	print '</response>' . "\n";
?>