<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');

	CheckTourSession(true);

	if (!isset($_REQUEST['Team']) || ($_REQUEST['Team']!=0 && $_REQUEST['Team']!=1) ||
		!isset($_REQUEST['d_Event']) ||
		!isset($_REQUEST['d_Phase']) ||
		!isset($_REQUEST['Language']) || ($_REQUEST['Language']!=1 && $_REQUEST['Language']!=2))
	{
		include('Common/Templates/head.php');
		print get_text('CrackError');
		include('Common/Templates/tail.php');
		exit;
	}

    checkACL(($_REQUEST['Team'] ? AclTeams : AclIndividuals), AclReadOnly);

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Individual/Fun_JS.js"></script>',
		);

	//$PAGE_TITLE=get_text('CmdImport','HTT');

	include('Common/Templates/head.php');

?>
<div align="center"><a class="Link" href="SelectSpot2.php?Team=<?php print $_REQUEST['Team']; ?>&amp;d_Phase=<?php print $_REQUEST['d_Phase']; ?>&amp;d_Event=<?php print $_REQUEST['d_Event']; ?>"><?php echo get_text('Back')  ?></a></div>
<?php
	$Select = "";

	if ($_REQUEST['Team']==0)
	{
		$Select
			= "SELECT FinEvent AS Event,FinMatchNo AS MatchNo,FinTournament,FinAthlete AS Id, FinLive AS Live,/*Finals*/ "
			. "EvProgr,EvEventName,	/* Events*/ "
			. "GrMatchNo,GrPhase,IF(EvFinalFirstPhase=48, GrPosition2, if(GrPosition>EvNumQualified, 0, GrPosition)) as GrPosition,	/* Grids */ "
			. "CONCAT(EnFirstName,' ',EnName) AS Name,EnCountry,	/* Entries*/ "
			. "CoCode,CoName,	/* Countries */ "
			. "RevLanguage" . $_REQUEST['Language'] . " AS Rev "
			. "FROM Finals INNER JOIN Events ON FinEvent=EvCode AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "INNER JOIN Grids ON FinMatchNo=GrMatchNo AND GrPhase=" . StrSafe_DB($_REQUEST['d_Phase']) . " "
			. "LEFT JOIN Entries ON FinAthlete=EnId "
			. "LEFT JOIN Countries ON EnCountry=CoId "
			. "LEFT JOIN Reviews ON FinEvent=RevEvent AND FinMatchNo=RevMatchNo AND FinTournament=RevTournament AND RevTeamEvent='0' AND RevTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "AND FinEvent=" . StrSafe_DB($_REQUEST['d_Event']) . " "
			. "ORDER BY EvProgr ASC,GrMatchNo ASC ";
	}
	else
	{
		$Select
			= "SELECT TfTeam AS Id,TfEvent AS Event,TfMatchNo AS MatchNo,TfTournament,TfLive AS Live, "
			. "EvProgr,EvEventName,	/* Events*/ "
			. "GrMatchNo,GrPhase,IF(EvFinalFirstPhase=48, GrPosition2, if(GrPosition>EvNumQualified, 0, GrPosition)) as GrPosition,	/* Grids */ "
			. "CoName,	/* Countries*/ "
			. "CoCode,CoName AS Name,	/* Countries */ "
			. "RevLanguage" . $_REQUEST['Language'] . " AS Rev "
			. "FROM TeamFinals INNER JOIN Events ON TfEvent=EvCode AND EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "INNER JOIN Grids ON TfMatchNo=GrMatchNo AND GrPhase=" . StrSafe_DB($_REQUEST['d_Phase']) . " "
			. "LEFT JOIN Countries ON TfTeam=CoId "
			. "LEFT JOIN Reviews ON TfEvent=RevEvent AND TfMatchNo=RevMatchNo AND TfTournament=RevTournament AND RevTeamEvent='1' AND RevTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "AND TfEvent=" . StrSafe_DB($_REQUEST['d_Event']) . " "
			. "ORDER BY EvProgr ASC,GrMatchNo ASC ";
	}

	//print $Select;

	$Rs=safe_r_sql($Select);

	$Output='';
	$MyHeader='';
	if (safe_num_rows($Rs)>0)
	{
		$Title=false;
		$firstName=true;
		$Rev='';

		while ($MyRow=safe_fetch($Rs))
		{
			if (!$Title)
			{
				$MyHeader
					= '<strong>' . get_text($MyRow->EvEventName,'','',true) . ' (' . $MyRow->Event . ') - ' . get_text( $_REQUEST['d_Phase'] . '_Phase') . '</strong><br/><br/></br>';
				$Title=true;
			}


			if ($firstName)
			{
				$Output
					.='<strong>'
						. $MyRow->GrPosition . '. ' . $MyRow->Name . ' (' . $MyRow->CoCode . ') - ';

				$Rev=nl2br(htmlentities($MyRow->Rev, ENT_NOQUOTES, "UTF-8"))	;

				$firstName=false;
			}
			else
			{
				$Output
					.=$MyRow->GrPosition . '. ' . $MyRow->Name . ' (' . $MyRow->CoCode . ')</strong><br/><br/>'
					. $Rev . '<br/><br/><br/>';
				$firstName=true;

			}
		}
	}


	print $MyHeader . $Output;

	include('Common/Templates/tail.php');
?>