<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Lib/Fun_PrintOuts.php');

$TeamLeaf=false;

if(!empty($_REQUEST['TestCountries'])) {
	$_REQUEST['BigNames']=true;
	$_REQUEST['TargetAssign']=false;
	$MyQuery="SELECT "
	. " '' EvCode, '' EvEventName, '' EvFinalFirstPhase, '' GrPhase, "
	. " '' TfTarget, '' GrMatchNo, "
	. " FlCode CoCode, concat(FlCode,' ', IF(co.CoName,co.CoName,'TEST')) Athlete, 1 AS Rank, 'test1|test2|test3' CoName, "
	. " '' s8, '' s4, '' s2, '' sBr, '' sGo "
	. " FROM Flags "
	. " left join (select CoCode, CoName from Countries group by CoCode) co on CoCode=FlCode "
	. " Where FlSVG>'' and FlTournament=-1 "
	. (!empty($_REQUEST['checked'])?" and FlChecked='1'":'')
	. (!empty($_REQUEST['unchecked'])?" and FlChecked!='1'":'')
	. " order by FlCode "
	. (!empty($_REQUEST['nolimit']) ? '' : " limit 10 ")
	;
} elseif(!empty($_REQUEST['TeamLabel'])) {
	$_REQUEST['BigNames']=true;
	$_REQUEST['TargetAssign']=false;
	$MyQuery="SELECT distinct "
	. " '' EvCode, '' EvEventName, '' EvFinalFirstPhase, '' GrPhase, "
	. " '' TfTarget, '' GrMatchNo, "
	. " CoCode, Countries.CoName Athlete, 1 AS Rank, 'test1|test2|test3' CoName, "
	. " fl.FlSVG, fl.FlJPG, "
	. " '' s8, '' s4, '' s2, '' sBr, '' sGo "
	. " FROM Countries "
	. " INNER JOIN Entries on EnCountry=CoId "
	. " left join (select FlCode, FlSVG, FlJPG from Flags where FlTournament=-1 or FlTournament={$_SESSION['TourId']}) fl on CoCode=FlCode "
	. " Where CoTournament={$_SESSION['TourId']} "
	. " order by CoCode "
	;
	$TeamLeaf=true;
} else {
	$MyQuery = 'SELECT '
		. ' EvCode, EvEventName, EvFinalFirstPhase, GrPhase, '
		. ' TfTarget, GrMatchNo, '
		. ' CoCode, CoName Athlete, TeRank AS Rank, group_concat(concat(upper(EnFirstname), " ", EnName) separator "|") CoName, '
		. ' NULLIF(s8.FSTarget,\'\') s8, NULLIF(s4.FSTarget,\'\') s4, NULLIF(s2.FSTarget,\'\') s2, NULLIF(sb.FSTarget,\'\') sBr, NULLIF(sg.FSTarget,\'\') sGo '
		. ' '
		. ' FROM Events'
		. ' INNER JOIN TeamFinals ON EvCode=TfEvent AND EvTournament=TfTournament '
		. ' INNER JOIN Grids ON TfMatchNo=GrMatchNo /* AND GrPhase<=EvFinalFirstPhase */ '
		. ' LEFT JOIN Teams ON TfTeam=TeCoId AND TfTournament=TeTournament AND TfEvent=TeEvent and TfSubTeam=TeSubTeam AND TeFinEvent=1 '
		. ' LEFT JOIN Countries on TfTeam=CoId AND TfTournament=CoTournament'
		. ' left join TeamFinComponent on  TfcCoId=CoId and TfcSubTeam=TeSubTeam and TfcTournament=EvTournament and TfcEvent=EvCode '
		. ' left join Entries on TfcId=EnId '
		. ' LEFT JOIN FinSchedule s8 ON EvCode=s8.FSEvent AND EvTeamEvent=s8.FSTeamEvent AND EvTournament=s8.FSTournament AND IF(EvFinalFirstPhase=8,TfMatchNo,-256)=s8.FSMatchNo'
		. ' LEFT JOIN FinSchedule s4 ON EvCode=s4.FSEvent AND EvTeamEvent=s4.FSTeamEvent AND EvTournament=s4.FSTournament AND IF(EvFinalFirstPhase=4,TfMatchNo,FLOOR(s8.FSMatchNo/2))=s4.FSMatchNo'
		. ' LEFT JOIN FinSchedule s2 ON EvCode=s2.FSEvent AND EvTeamEvent=s2.FSTeamEvent AND EvTournament=s2.FSTournament AND IF(EvFinalFirstPhase=2,TfMatchNo,FLOOR(s4.FSMatchNo/2))=s2.FSMatchNo'
		. ' LEFT JOIN FinSchedule sb ON EvCode=sb.FSEvent AND EvTeamEvent=sb.FSTeamEvent AND EvTournament=sb.FSTournament AND FLOOR(s2.FSMatchNo/2)=sb.FSMatchNo'
		. ' LEFT JOIN FinSchedule sg ON EvCode=sg.FSEvent AND EvTeamEvent=sg.FSTeamEvent AND EvTournament=sg.FSTournament AND FLOOR(s2.FSMatchNo/2)-2=sg.FSMatchNo'
		. ' WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=1 ';
		$MyQuery.=' AND EvFinalFirstPhase=GrPhase ';
		if (!empty($_REQUEST['Event'])) $MyQuery.= CleanEvents($_REQUEST['Event'], 'EvCode') . ' ';
		if (isset($_REQUEST['Phase']) && preg_match("/^[0-9]{1,2}$/i",$_REQUEST["Phase"])) {
			$MyQuery.= ' AND (TfEvent, TfTeam, TfSubTeam) IN (SELECT DISTINCT TfEvent,TfTeam, TfSubTeam FROM TeamFinals INNER JOIN Grids ON TfMatchNo=GrMatchNo WHERE TfTournament='.$_SESSION['TourId'].' and grPhase=' . $_REQUEST['Phase']  . (!empty($_REQUEST['Event']) ? CleanEvents($_REQUEST['Event'], 'TfEvent'):''). ')';
		}
		$MyQuery .= ' Group By EvCode, GrPhase, TfMatchNo ';
		$MyQuery .= ' ORDER BY EvCode, GrPhase DESC, TfMatchNo ASC, TfcOrder';
// 		debug_svela($MyQuery, true);
}
//*DEBUG*/echo $MyQuery;exit();
$Rs=safe_r_sql($MyQuery);
// Se il Recordset � valido e contiene almeno una riga
if (safe_num_rows($Rs)>0) {
	if(!empty($_REQUEST['BigNames'])) {
		require_once('Common/Fun_FormatText.inc.php');
		require_once('Common/pdf/BigNamesPDF.inc.php');

		$pdf = new BigNamesPDF(get_text('Sign/guide-board','Tournament'),false);

		if(!empty($_REQUEST['TestCountries'])) {
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);
		}
		$pdf->init($Rs);

		$pdf->TargetAssignment = !empty($_REQUEST['TargetAssign']);
		$pdf->IncludeLogo = (!empty($_REQUEST['IncludeLogo']) or !empty($_REQUEST['TestCountries']) or $TeamLeaf);
		$pdf->TeamLeaf = $TeamLeaf;
		$pdf->Local = !empty($_REQUEST['local']);
		if($TeamLeaf) $pdf->setPrintHeader(false);

		$pdf->Make();
		exit();
	} else {
		require_once('Common/pdf/IanseoPdf.php');
		include_once('Final/PDFNames.php');
		exit();
	}
}

?>