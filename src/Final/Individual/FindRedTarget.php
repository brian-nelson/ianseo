<?php
/*
													- FindRedTarget.php -
	Cerca i target doppi e ritorna l'elenco.
	La funzione ajax si preoccuperà di colorare i doppioni
*/

	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

	if (!CheckTourSession() ||
		!isset($_REQUEST['d_Event']) ||
		!isset($_REQUEST['d_Phase']))
	{
		print get_text('CrackError');
		exit;
	}
    checkACL(AclCompetition, AclReadWrite, false);

	$xml = '';
	$Errore=0;
	$Bersagli=1;
	$Ath4Tar = 0;

	$TargetFilter = "";
	if (isset($_REQUEST['Target']))
		$TargetFilter = " AND FSTarget = " . StrSafe_DB(str_pad($_REQUEST['Target'],TargetNoPadding,'0',STR_PAD_LEFT)) . " ";

	$MatchNoFilter = "";
// estraggo il numero di persone per bersaglio per la fase
	$Select
		= "SELECT EvFinalAthTarget "
		. "FROM Events "
		. "WHERE EvTeamEvent='0' AND EvCode=" . StrSafe_DB($_REQUEST['d_Event']) . " "
		. "AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)==1) {
		$MyRow=safe_fetch($Rs);

		$BitMask=$MyRow->EvFinalAthTarget;
		$Bit = ($_REQUEST['d_Phase']>0 ? $_REQUEST['d_Phase']*2 : 1);

		$Ath4Tar = (($Bit & $BitMask)==$Bit ? 1 : 0);

		if ($Ath4Tar==1)
			$MatchNoFilter = " AND (GrMatchNo % 2)=0 " ;
	}

	$Select
		= "SELECT FinEvent,GrPhase,GrMatchNo,FSTarget, FSScheduledDate, FSScheduledTime,Sq1Tar, Sq2Tar, IF(Sq1Quanti>Sq2Quanti, Sq1Quanti,Sq2Quanti) AS Quanti "
		. "FROM Events INNER JOIN Finals ON FinEvent=EvCode AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']). " "
		. "INNER JOIN Grids ON FinMatchNo=GrMatchNo AND GrPhase=" . StrSafe_DB($_REQUEST['d_Phase']) . " "
		. "AND FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent=" . StrSafe_DB($_REQUEST['d_Event']) . " "
		. "LEFT JOIN FinSchedule ON FinEvent=FSEvent AND FinMatchNo=FSMatchNo AND FinTournament=FSTournament AND (FSTeamEvent='0' OR FSTeamEvent IS NULL) "
		. "LEFT JOIN ("
		. "SELECT FSTarget AS Sq1Tar,COUNT(FSTarget) AS Sq1Quanti, GrPhase as Sq1GrPhase "
		. "FROM FinSchedule INNER JOIN Grids ON FSMatchNo=GrMatchNo "
		. "WHERE FSTarget<>'' AND FSScheduledDate='0000-00-00' AND FSEvent=" . StrSafe_DB($_REQUEST['d_Event']) . " AND FSTeamEvent='0' AND FSTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "GROUP BY FSTarget, GrPhase "
		. ") AS Sq1 ON FSTarget=Sq1Tar AND GrPhase=Sq1GrPhase "
		. "LEFT JOIN ("
		. "SELECT FSScheduledDate as Sq2FSScheduledDate, FSScheduledTime as Sq2FSScheduledTime, FSTarget AS Sq2Tar, COUNT(FSTarget) AS Sq2Quanti "
		. "FROM FinSchedule "
		. "WHERE FSTarget<>'' AND FSScheduledDate!='0000-00-00'  AND FSTeamEvent='0' AND FSTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "GROUP BY FSScheduledDate, FSScheduledTime, FSTarget "
		. ") AS Sq2 ON FSTarget=Sq2Tar AND FSScheduledDate=Sq2FSScheduledDate AND FSScheduledTime=Sq2FSScheduledTime "
		. "WHERE FSTarget<>'' " . $TargetFilter
		. "ORDER BY GrMatchNo ";
	$Rs=safe_r_sql($Select);

	if ($Rs) {
		if (safe_num_rows($Rs)>0) {
			while ($MyRow=safe_fetch($Rs)) {
				$xml
					.='<match>'
					. '<matchno><![CDATA[' . $MyRow->GrMatchNo . ']]></matchno>'
					. '<targetno><![CDATA[' . $MyRow->FSTarget . ']]></targetno>'
					. '<quanti><![CDATA[' . $MyRow->Quanti . ']]></quanti>'
					. '</match>';
			}
		} else {
            $Bersagli = 0;
        }
	} else {
        $Errore = 1;
    }

	header('Content-Type: text/xml');

	print '<response>';
	print '<error>' . $Errore . '</error>';
	print '<bersagli>' . $Bersagli . '</bersagli>';
	print '<event>' . $_REQUEST['d_Event'] . '</event>';
	print '<phase>' . $_REQUEST['d_Phase'] . '</phase>';
	print '<athfortar><![CDATA[' . $Ath4Tar . ']]></athfortar>';
	print $xml;
	print '</response>';
?>