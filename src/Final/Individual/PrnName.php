<?php
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Lib/Fun_PrintOuts.php');

set_time_limit(0);

//$MyQuery = 'SELECT '
//	. ' EvCode, EvEventName, EvFinalFirstPhase, GrPhase, '
//	//. ' FSTarget as FinTarget, GrMatchNo, EnFirstName as Athlete, '
//	. ' GrMatchNo, EnFirstName as Athlete, '
//	. ' CoCode, CoName, IndRank as Rank, '
//    . ' NULLIF(s32.FSTarget,\'\') s32, NULLIF(s16.FSTarget,\'\') s16, NULLIF(s8.FSTarget,\'\') s8, NULLIF(s4.FSTarget,\'\') s4, NULLIF(s2.FSTarget,\'\') s2, NULLIF(sb.FSTarget,\'\') sBr, NULLIF(sg.FSTarget,\'\') sGo '
//	. ' FROM Events'
//	. ' INNER JOIN Finals ON EvCode=FinEvent AND EvTournament=FinTournament'
//	. ' INNER JOIN Grids ON FinMatchNo=GrMAtchNo AND GrPhase<=EvFinalFirstPhase'
//	. ' INNER JOIN Individuals ON FinAthlete=IndId AND FinEvent=IndEvent AND FinTournament=IndTournament'
//	. ' LEFT JOIN Entries ON FinAthlete=EnId AND FinTournament=EnTournament'
//	. ' LEFT JOIN Qualifications ON QuId=EnId'
//	. ' LEFT JOIN Countries on EnCountry=CoId AND EnTournament=CoTournament'
//
//	. ' LEFT JOIN FinSchedule s32 ON EvCode=s32.FSEvent AND EvTeamEvent=s32.FSTeamEvent AND EvTournament=s32.FSTournament AND IF(EvFinalFirstPhase=32 OR EvFinalFirstPhase=48 OR EvFinalFirstPhase=24,FinMatchNo,-256)=s32.FSMatchNo'
//	. ' LEFT JOIN FinSchedule s16 ON EvCode=s16.FSEvent AND EvTeamEvent=s16.FSTeamEvent AND EvTournament=s16.FSTournament AND IF(EvFinalFirstPhase=16,FinMatchNo,FLOOR(s32.FSMatchNo/2))=s16.FSMatchNo'
//	. ' LEFT JOIN FinSchedule s8 ON EvCode=s8.FSEvent AND EvTeamEvent=s8.FSTeamEvent AND EvTournament=s8.FSTournament AND IF(EvFinalFirstPhase=8,FinMatchNo,FLOOR(s16.FSMatchNo/2))=s8.FSMatchNo'
//	. ' LEFT JOIN FinSchedule s4 ON EvCode=s4.FSEvent AND EvTeamEvent=s4.FSTeamEvent AND EvTournament=s4.FSTournament AND IF(EvFinalFirstPhase=4,FinMatchNo,FLOOR(s8.FSMatchNo/2))=s4.FSMatchNo'
//	. ' LEFT JOIN FinSchedule s2 ON EvCode=s2.FSEvent AND EvTeamEvent=s2.FSTeamEvent AND EvTournament=s2.FSTournament AND IF(EvFinalFirstPhase=2,FinMatchNo,FLOOR(s4.FSMatchNo/2))=s2.FSMatchNo'
//	. ' LEFT JOIN FinSchedule sb ON EvCode=sb.FSEvent AND EvTeamEvent=sb.FSTeamEvent AND EvTournament=sb.FSTournament AND FLOOR(s2.FSMatchNo/2)=sb.FSMatchNo'
//	. ' LEFT JOIN FinSchedule sg ON EvCode=sg.FSEvent AND EvTeamEvent=sg.FSTeamEvent AND EvTournament=sg.FSTournament AND FLOOR(s2.FSMatchNo/2)-2=sg.FSMatchNo'
//
//	. ' WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=0 ';
//	if (!empty($_REQUEST['Event'])) $MyQuery.= CleanEvents($_REQUEST['Event'], 'EvCode') . ' ';
//	if (isset($_REQUEST['Phase']) && preg_match("/^[0-9]{1,2}$/i",$_REQUEST["Phase"]))
//		$MyQuery.= "AND GrPhase = " . StrSafe_DB($_REQUEST['Phase']) . " ";
//	$MyQuery .= ' ORDER BY EvCode, GrPhase DESC, FinMatchNo ASC';
$MyQuery = 'SELECT '
	. ' EvCode, EvEventName, EvFinalFirstPhase, GrPhase, '
	//. ' FSTarget as FinTarget, GrMatchNo, EnFirstName as Athlete, '
	. ' GrMatchNo, EnId, Concat(EnFirstName, " ", LEFT(EnName,1), ".") as Athlete, '
	. ' CoCode, CoName, IndRank as Rank, '
    . ' NULLIF(s64.FSLetter,\'\') s64, NULLIF(s32.FSLetter,\'\') s32, NULLIF(s16.FSLetter,\'\') s16, NULLIF(s8.FSLetter,\'\') s8, NULLIF(s4.FSLetter,\'\') s4, NULLIF(s2.FSLetter,\'\') s2, NULLIF(sb.FSLetter,\'\') sBr, NULLIF(sg.FSLetter,\'\') sGo '
	. ' FROM Events'
	. ' INNER JOIN Finals ON EvCode=FinEvent AND EvTournament=FinTournament'
	. ' INNER JOIN Grids ON FinMatchNo=GrMatchNo AND GrPhase=(IF(EvFinalFirstPhase=24,32, IF(EvFinalFirstPhase=48,64,EvFinalFirstPhase )))'
	. ' INNER JOIN Individuals ON FinAthlete=IndId AND FinEvent=IndEvent AND FinTournament=IndTournament'
	. ' LEFT JOIN Entries ON FinAthlete=EnId AND FinTournament=EnTournament'
	. ' LEFT JOIN Qualifications ON QuId=EnId'
	. ' LEFT JOIN Countries on EnCountry=CoId AND EnTournament=CoTournament'

	. ' LEFT JOIN FinSchedule s64 ON EvCode=s64.FSEvent AND EvTeamEvent=s64.FSTeamEvent AND EvTournament=s64.FSTournament AND IF(EvFinalFirstPhase=64 OR EvFinalFirstPhase=48 ,FinMatchNo,-256)=s64.FSMatchNo'
    . ' LEFT JOIN FinSchedule s32 ON EvCode=s32.FSEvent AND EvTeamEvent=s32.FSTeamEvent AND EvTournament=s32.FSTournament AND IF(EvFinalFirstPhase=32 OR EvFinalFirstPhase=24,FinMatchNo,FLOOR(s64.FSMatchNo/2))=s32.FSMatchNo'
	. ' LEFT JOIN FinSchedule s16 ON EvCode=s16.FSEvent AND EvTeamEvent=s16.FSTeamEvent AND EvTournament=s16.FSTournament AND IF(EvFinalFirstPhase=16,FinMatchNo,FLOOR(s32.FSMatchNo/2))=s16.FSMatchNo'
	. ' LEFT JOIN FinSchedule s8 ON EvCode=s8.FSEvent AND EvTeamEvent=s8.FSTeamEvent AND EvTournament=s8.FSTournament AND IF(EvFinalFirstPhase=8,FinMatchNo,FLOOR(s16.FSMatchNo/2))=s8.FSMatchNo'
	. ' LEFT JOIN FinSchedule s4 ON EvCode=s4.FSEvent AND EvTeamEvent=s4.FSTeamEvent AND EvTournament=s4.FSTournament AND IF(EvFinalFirstPhase=4,FinMatchNo,FLOOR(s8.FSMatchNo/2))=s4.FSMatchNo'
	. ' LEFT JOIN FinSchedule s2 ON EvCode=s2.FSEvent AND EvTeamEvent=s2.FSTeamEvent AND EvTournament=s2.FSTournament AND IF(EvFinalFirstPhase=2,FinMatchNo,FLOOR(s4.FSMatchNo/2))=s2.FSMatchNo'
	. ' LEFT JOIN FinSchedule sb ON EvCode=sb.FSEvent AND EvTeamEvent=sb.FSTeamEvent AND EvTournament=sb.FSTournament AND FLOOR(s2.FSMatchNo/2)=sb.FSMatchNo'
	. ' LEFT JOIN FinSchedule sg ON EvCode=sg.FSEvent AND EvTeamEvent=sg.FSTeamEvent AND EvTournament=sg.FSTournament AND FLOOR(s2.FSMatchNo/2)-2=sg.FSMatchNo'

	. ' WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=0 ';
	if (!empty($_REQUEST['Event'])) $MyQuery.= CleanEvents($_REQUEST['Event'], 'EvCode') . ' ';
	if (isset($_REQUEST['Phase']) && preg_match("/^[0-9]{1,2}$/i",$_REQUEST["Phase"]))
	{
		$p=$_REQUEST['Phase'];
	//	print $p;exit;
		if ($p==24)
		{
			$p=32;
		}
		elseif ($p==48)
		{
			$p=64;
		}

		$MyQuery.= 'AND EnId IN (SELECT DISTINCT FinAthlete FROM Finals INNER JOIN Grids ON FinMatchNo=GrMatchNo WHERE FinTournament='.$_SESSION['TourId'].' and grPhase=' . $p  . (!empty($_REQUEST['Event']) ? CleanEvents($_REQUEST['Event'], 'EvCode'):''). ')';
	}
	$MyQuery .= ' ORDER BY EvCode, GrPhase DESC, FinMatchNo ASC';

//*DEBUG*/echo $MyQuery;exit();
$Rs=safe_r_sql($MyQuery);
// Se il Recordset è valido e contiene almeno una riga
if (safe_num_rows($Rs)>0)
{
//	debug_svela($MyQuery);

	if(!empty($_REQUEST['BigNames'])) {
		require_once('Common/Fun_FormatText.inc.php');
		require_once('Common/pdf/BigNamesPDF.inc.php');
		$pdf = new BigNamesPDF(get_text('Sign/guide-board','Tournament'),false);
		$pdf->init($Rs);
		$pdf->TargetAssignment = !empty($_REQUEST['TargetAssign']);
		$pdf->IncludeLogo = !empty($_REQUEST['IncludeLogo']);
		$pdf->Make();
		exit();
	} else {
		require_once('Common/pdf/IanseoPdf.php');
		include_once('Final/PDFNames.php');
		exit();
	}
}

?>