<?php
/*
													- UpdateFieldEventList.php -
	Aggiorna il campo di Events passato in querystring.
*/

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_Sessions.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');
	require_once('Common/Fun_Various.inc.php');

	if (!CheckTourSession() ||
		!isset($_REQUEST['EvCode']) || !isset($_REQUEST['EcNumber']))
	{
		print get_text('CrackError');
		exit;
	}
    checkACL(AclCompetition, AclReadWrite, false);

	$Errore=0;

	$Tuple = array();
	$Rules = array();

	$Tuple_Index=0;
	$xml = '';

	$NewGroup = 1;

	if (!IsBlocked(BIT_BLOCK_TOURDATA)) {
		$Select
			= "SELECT (IF(MAX(EcTeamEvent) IS NULL,1,MAX(EcTeamEvent)+1)) AS NewGroup "
			. "FROM EventClass "
			. "WHERE EcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EcCode=" . StrSafe_DB($_REQUEST['EvCode']) . " ";
		$Rs=safe_r_sql($Select);

		if (safe_num_rows($Rs)==1) {
			$Row=safe_fetch($Rs);
			$NewGroup=$Row->NewGroup;
		}

		foreach ($_REQUEST['New_EcDivision'] as $DivKey => $DivValue) {
			foreach ($_REQUEST['New_EcClass'] as $ClKey => $ClValue) {
			    foreach ($_REQUEST['New_EcSubClass'] as $SubClKey => $SubClValue) {
                    $Tuple[$Tuple_Index]
                        = "("
                        . StrSafe_DB($_REQUEST['EvCode']) . ", "
                        . StrSafe_DB($NewGroup) . ", "
                        . StrSafe_DB($_SESSION['TourId']) . ", "
                        . StrSafe_DB($ClValue) . ", "
                        . StrSafe_DB($DivValue) . ","
                        . StrSafe_DB($SubClValue) . ","
                        . StrSafe_DB($_REQUEST['EcNumber']) . ""
                        . ")";
                    $Rules[$Tuple_Index] = $DivValue . "|" . $ClValue . "|" . $SubClValue;
                    $Tuple_Index++;
                }
            }
		}

		foreach ($Tuple as $Key => $Value) {
			$Insert = "INSERT INTO EventClass (EcCode,EcTeamEvent,EcTournament,EcClass,EcDivision,EcSubClass,EcNumber) VALUES" . $Value;
			$RsIns=safe_w_sql($Insert);

			if (safe_w_affected_rows()==1) {
				safe_w_sql("UPDATE Events SET EvTourRules='' where EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EvTeamEvent='1' AND EvTournament = " . StrSafe_DB($_SESSION['TourId']));
				$xml.= '<new_rule>' . $Rules[$Key] . '</new_rule>' ;
			}
		}

	// calcolo il numero massimo di persone nel team
		calcMaxTeamPerson(array($_REQUEST['EvCode']));

	// reset shootoff dell'evento
		ResetShootoff($_REQUEST['EvCode'],1,0);

	// teamabs
		MakeTeamsAbs(null,null,null);
	}
	else
		$Errore=1;

	if ($xml=='')
		$Errore=1;

    header('Content-Type: text/xml');

	print '<response>' ;
	print '<error>' . $Errore . '</error>' ;
	print '<confirm_msg>' . get_text('MsgAreYouSure') . '</confirm_msg>' ;
	print '<evcode>' . $_REQUEST['EvCode'] . '</evcode>' ;
	print '<new_number>' . $_REQUEST['EcNumber'] . '</new_number>' ;
	print '<new_group>' . $NewGroup . '</new_group>' ;
	print $xml;
	/*print '<new_ecdivision>' . $_REQUEST['New_EcDivision'] . '</new_ecdivision>' ;
	print '<new_ecclass>' . $_REQUEST['New_EcClass'] . '</new_ecclass>' ;*/
	print '</response>' ;
?>