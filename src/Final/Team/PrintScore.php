<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	CheckTourSession(true);
    checkACL(AclTeams, AclReadOnly);
	require_once('Common/Globals.inc.php');
	require_once('Common/Fun_DB.inc.php');
	require_once('Common/Fun_Phases.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('HHT/Fun_HHT.local.inc.php');
	require_once('Common/Fun_Modules.php');
	require_once('Common/Lib/CommonLib.php');

	$JS_SCRIPT=array(
		phpVars2js(array("WebDir" => $CFG->ROOT_DIR, "AllEvents" => get_text('AllEvents'))),
		'<script type="text/javascript" src="../../Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="../../Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="../Fun_AJAX.js"></script>',
		'<script type="text/javascript">',
		'function DisableChkOther(NoDist, NumDist)',
		'{',
		'	if(NoDist)',
		'	{',
		'		if(document.getElementById(\'ChkDist0\').checked)',
		'		{',
		'			for(i=1; i<=NumDist; i++)',
		'				document.getElementById(\'ChkDist\'+i).checked=false;',
		'		}',
		'	}',
		'	else',
		'	{',
		'		for(i=1; i<=NumDist; i++)',
		'		{',
		'			if(document.getElementById(\'ChkDist\'+i).checked)',
		'				document.getElementById(\'ChkDist0\').checked=false;',
		'		}',
		'	}',
		'}',
		'</script>',
		);

	$PAGE_TITLE=get_text('PrintScore','Tournament');

	include('Common/Templates/head.php');

	echo '<table class="Tabella">';
	echo '<tr><th class="Title" colspan="2">' . get_text('PrintScore','Tournament')  . '</th></tr>';
	echo '<tr><th class="SubTitle" width="50%">' . get_text('Score1Page1Athlete')  . '</th><th class="SubTitle" width="50%">' . get_text('Score1Page1Match')  . '</th></tr>';
//Parametri
	echo '<tr>';
//Scores Personali
	echo '<td width="50%" class="Center"><br><a href="'.$CFG->ROOT_DIR.'Final/Team/PDFScore.php" class="Link" target="PrintOut">';
	echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdf.gif" alt="' . get_text('TeamFinal') . '" border="0"><br>';
	echo get_text('TeamFinal');
	echo '</a></td>';
//Scores Per Match
	echo '<td width="50%" class="Center" rowspan="3"><div align="Center">';
	$MyQuery = "SELECT MAX(GrPhase) as MaxPhase FROM Grids left join TeamFinals on GrMatchNo=TfMatchNo where TfTournament={$_SESSION['TourId']}";
	$Rs = safe_r_sql($MyQuery);
	$TmpCnt=32;
	if(safe_num_rows($Rs)>0) {
		$r=safe_fetch($Rs);
		$TmpCnt=$r->MaxPhase;
	}
	echo '<table class="Tabella" style="width:95%">';
	echo '<tr>';
	for($i=$TmpCnt; $i>0; $i=floor($i/2))
	{
		echo '<th class="SubTitle">' . get_text($i . '_Phase') . ($i==16 ?  " - " . get_text('12_Phase'):'') . '</th>';
	}
	echo '<th class="SubTitle">' . get_text('0_Phase') . '</th>';
	echo '</tr>';
	$MyQuery = 'SELECT EvCode, GrPhase, MAX(IF(TfTeam=0,0,1)) as Printable
        FROM Events 
        INNER JOIN TeamFinals ON EvCode=TfEvent AND EvTournament=TfTournament
        INNER JOIN Phases ON EvFinalFirstPhase=PhId  and (PhIndTeam & pow(2, EvTeamEvent))>0
        INNER JOIN Grids ON TfMatchNo=GrMAtchNo AND GrPhase<=greatest(PhId,PhLevel)  
        WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=1 AND EvFinalFirstPhase!=0 
        GROUP BY EvCode, EvEventName, EvFinalFirstPhase, GrPhase
        ORDER BY EvCode, GrPhase DESC';
	$Rs = safe_r_sql($MyQuery);

	if(safe_num_rows($Rs)>0)
	{
		$TmpEvent='';
		echo '<tr>';
		while($MyRow=safe_fetch($Rs))
		{
			if($TmpEvent!=$MyRow->EvCode)
			{
				if($TmpEvent!='')
					echo '</tr><tr>';
				$TmpEvent = $MyRow->EvCode;
				for($i=$TmpCnt; $i>$MyRow->GrPhase; $i = floor($i/2))
					echo '<td>&nbsp;</td>';
			}
			echo '<td class="Center"><a href="PDFScoreMatch.php?Event=' .  $MyRow->EvCode . '&amp;Phase=' . $MyRow->GrPhase . '" class="Link" target="PrintOut">';
			echo '<img src="../../Common/Images/pdf' . ($MyRow->Printable==1 ? '' : "_small") . '.gif" alt="' . $MyRow->EvCode . '" border="0"><br>';
			echo $MyRow->EvCode;
			echo '</a></td>';
		}
		echo '</tr>';
		safe_free_result($Rs);
	}

	echo '</table>';
	echo '<br></div></td>';
	echo '</tr>';
//Elenco dei PDF dello Score personale
	echo '<tr class="Divider"><td  colspan="1"></td></tr>';
	echo '<tr>';
	echo '<td class="Center" width="50%"><div align="center"><br>';
	echo '<table class="Tabella" style="width:80%">';
	echo '<tr>';
	//Eventi
	$MySql = "SELECT EvCode, EvEventName FROM Events WHERE EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvFinalFirstPhase!=0 ORDER BY EvProgr";
	$Rs = safe_r_sql($MySql);
	if(safe_num_rows($Rs)>0)
	{
		$TmpCnt=0;
		while($MyRow=safe_fetch($Rs))
		{
			if($TmpCnt++ % 2 == 0 && $TmpCnt != 1)
				echo '</tr><tr>';
			echo '<td class="Center" width="50%"><a href="PDFScore.php?Event=' .  $MyRow->EvCode . '" class="Link" target="PrintOut">';
			echo '<img src="../../Common/Images/pdf_small.gif" alt="' . $MyRow->EvCode . '" border="0"><br>';
			echo $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true);
			echo '</a></td>';
		}
		if($TmpCnt % 2 != 0)
			echo '<td>&nbsp;</td>';
		safe_free_result($Rs);
	}
	echo '</tr>';
	echo '</table>';
	echo '</div>&nbsp;</td>';
	echo '</tr>';
// Selezione Eventi per score personale
	echo '<tr>';
	echo '<td align="Center"><br>';
	echo '<form id="PrnParameters" action="PDFScore.php" method="post" target="PrintOut">';
	echo '<table class="Tabella" style="width:80%">';
	echo '<tr>';
	echo '<td class="Center" width="50%">';
	$MySql = "SELECT EvCode, EvEventName FROM Events WHERE EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvFinalFirstPhase!=0 ORDER BY EvProgr";
	$Rs = safe_r_sql($MySql);
	if(safe_num_rows($Rs)>0)
	{
		echo get_text('Event') . '<br><select name="Event[]" multiple="multiple" size="8">';
		while($MyRow=safe_fetch($Rs))
			echo '<option value="' . $MyRow->EvCode . '">' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true)  . '</option>';
		echo '</select>';
		safe_free_result($Rs);
	}
	echo '</td><td width="50%" class="Center">';
	echo '<input name="IncEmpty" type="checkbox" value="1">&nbsp;' . get_text('ScoreIncEmpty') . '<br>';
	echo '</td>';
	echo '</tr>';
	echo '</table>';
	echo '<br/><br/><input name="Submit" type="submit" onclick="this.form.action=\'PDFScore.php\'" value="' . get_text('PrintScore','Tournament') . '">';
	echo '<br/><br/><input name="Submit" type="submit" onclick="this.form.action=\'PrnLabels.php\'" value="' . get_text('FinalTeamLabels','Tournament') . '">';
	echo '<br/>&nbsp;</form>';
	echo '</td>';
//Combo di Selezione per gli scores a match
	echo '<td align="Center"><br>';
	echo '<form id="PrnParametersMatch" action="PDFScoreMatch.php" method="get" target="PrintOut">';
	echo '<table class="Tabella" style="width:80%">';
	echo '<tr>';
	echo '<td class="Center" width="70%">';
	$MySql = "SELECT EvCode, EvEventName FROM Events WHERE EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvFinalFirstPhase!=0 ORDER BY EvProgr";
	$Rs = safe_r_sql($MySql);
	if(safe_num_rows($Rs)>0)
	{
		echo get_text('Event') . '<br><select name="Event[]" multiple="multiple" id="d_Event" onChange="javascript:ChangeEvent(1);" size="8">';
		while($MyRow=safe_fetch($Rs))
			echo '<option value="' . $MyRow->EvCode . '">' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true)  . '</option>';
		echo '</select>';
		safe_free_result($Rs);
	}
	echo '</td><td width="30%" class="Center">';
	echo '<select name="Phase" id="d_Phase" size="6">';
	echo '<option value="">' . get_text('AllEvents')  . '</option>';
	echo '</select>';
	echo '</td>';
	echo '</tr>';
	echo '<tr><td colspan="2" class="Center">' . ComboSes(RowTour(), 'Teams') . '</td></tr>';
	echo '<tr>';
	echo '<td colspan="2" class="left">';
	echo '<input name="ScoreFilled" type="checkbox" value="1">&nbsp;' . get_text('ScoreFilled') . '<br>';
	echo '<input name="IncEmpty" type="checkbox" value="1">&nbsp;' . get_text('ScoreIncEmpty') . '<br>';
	echo '<input name="ScoreFlags" type="checkbox" value="1">&nbsp;' . get_text('ScoreFlags','Tournament') . '<br>';
	if(module_exists("Barcodes"))
		echo '<input name="Barcode" type="checkbox" checked value="1">&nbsp;' . get_text('ScoreBarcode','Tournament') . '<br>';
	foreach(AvailableApis() as $Api) {
        if(!($tmp=getModuleParameter($Api, 'Mode')) || $tmp=='live' ) {
            continue;
        }
		echo '<input name="QRCode[]" type="checkbox" checked value="'.$Api.'" >&nbsp;' . get_text($Api.'-QRCode','Api') . '<br>';
	}
	echo '</td>';
	echo '</tr>';
	echo '</table>';
	echo '<br>&nbsp;<br><input name="Submit" type="submit" value="' . get_text('PrintScore','Tournament') . '"><br>&nbsp;';
	echo '</form>';
	echo '</td>';
	echo '</tr>';
//Score in bianco
	echo '<tr><th class="SubTitle" colspan="2">' . get_text('ScoreDrawing')  . '</th></tr>';
	echo '<tr>';
//Scores Personali
	echo '<td width="50%" class="Center"><br>';

	//$q=safe_r_sql("select EvCode, EvMatchMode, EvFinalFirstPhase, EvMatchArrowsNo, EvElimEnds, EvElimArrows, EvElimSO, EvFinEnds, EvFinArrows, EvFinSO from Events where EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent!=0 AND EvFinalFirstPhase!=0 group by EvMatchMode, EvFinalFirstPhase, EvMatchArrowsNo, EvElimEnds, EvElimArrows, EvElimSO, EvFinEnds, EvFinArrows, EvFinSO");
// per il bittaggio vedi elimFinFromMatchArrowsNo
	$query="SELECT EvCode, EvMatchMode, EvFinalFirstPhase, EvMatchArrowsNo, EvElimEnds, EvElimArrows, EvElimSO, EvFinEnds, EvFinArrows, EvFinSO
		FROM Events
		INNER JOIN Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2,EvTeamEvent))>0
		WHERE EvTournament = '{$_SESSION['TourId']}'
			AND EvTeamEvent !=0
			AND EvFinalFirstPhase !=0
		GROUP BY
			EvMatchMode, EvFinalFirstPhase, (EvMatchArrowsNo & (POW(2,1+LOG(2,IF(EvFinalFirstPhase>0,2*greatest(PhId, PhLevel),1)))-1)), EvElimEnds, EvElimArrows, EvElimSO, EvFinEnds, EvFinArrows, EvFinSO
	";
	//print $query;
	$q=safe_r_sql($query);
	echo '<table width="100%" cellspacing="0" cellpadding="1">';
	echo '<tr>';
	while($r=safe_fetch($q)) {
		echo '<td><a href="'.$CFG->ROOT_DIR.'Final/Team/PDFScore.php?Blank=1&Model='.$r->EvCode.'" class="Link" target="PrintOut">';
			echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdf.gif" alt="' . get_text('Score1Page1Athlete') . '" border="0"><br>';
			echo get_text('Score1Page1Athlete');
			$dif=($r->EvElimEnds!=$r->EvFinEnds or $r->EvElimArrows!=$r->EvFinArrows or $r->EvElimSO!=$r->EvFinSO);

			$txt='<b>'. ($r->EvMatchMode?'<br/>'.get_text('MatchMode_1').':</b> ':'');

			$tmp=array();
			list($hasElim,$hasFin)=eventHasScoreTypes($r->EvCode,1);
			if ($hasElim)
			{
				$tmp[]=array(get_text('EliminationShort', 'Tournament'),get_text('EventDetails', 'Tournament', array($r->EvElimEnds, $r->EvElimArrows, $r->EvElimSO)));
			}

			if ($hasFin)
			{
				$tmp[]=array(get_text('FinalShort', 'Tournament'),get_text('EventDetails', 'Tournament', array($r->EvFinEnds, $r->EvFinArrows, $r->EvFinSO)));
			}

			//$txt.='<b>'. ($r->EvMatchMode?'<br/>'.get_text('MatchMode_1').':</b> ':'');

			foreach ($tmp as $t)
			{
				$txt.='<br>'.(count($tmp)>1 && $dif ? $t[0] . ' ' : '') . $t[1];
			}

			//$txt=substr($txt,0,-5);

			echo $txt;

//			echo '<br/><b>'. ($r->EvMatchMode?get_text('MatchMode_1').':</b> ':'') . ($dif?get_text('EliminationShort', 'Tournament').' ':'') . get_text('EventDetails', 'Tournament', array($r->EvElimEnds, $r->EvElimArrows, $r->EvElimSO)) ;
//			if($dif) echo '<br/>'. get_text('FinalShort', 'Tournament').' ' . get_text('EventDetails', 'Tournament', array($r->EvFinEnds, $r->EvFinArrows, $r->EvFinSO)) ;
			echo '<br/>'. get_text('FirstPhase').': 1/'. namePhase($r->EvFinalFirstPhase,$r->EvFinalFirstPhase);
		echo '</a></td>';
	}
	echo '</tr>';
	echo '</table>';

	echo '</td>';
//Scores per singolo match
	echo '<td width="50%" class="Center">';
	// recupera per questo torneo quanti formati ci sono...
//	$q=safe_r_sql("(select distinct EvMatchMode, EvElimEnds, EvElimArrows, EvElimSO from Events where EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 AND EvFinalFirstPhase!=0 )"
//		. "UNION (select distinct EvMatchMode, EvFinEnds, EvFinArrows, EvFinSO from Events where EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 AND EvFinalFirstPhase!=0 )");

	$query="
		SELECT
			EvCode,EvFinalFirstPhase,EvMatchArrowsNo,
			EvElimEnds, EvElimArrows, EvElimSO,
			EvFinEnds, EvFinArrows, EvFinSO
		FROM
			Events
		WHERE
			EvTournament={$_SESSION['TourId']} AND EvTeamEvent=1
	";

/*
 * Per ogni evento scopro se le sue fasi prevedono o no l'uso dei parametri elim e fin.
 * Se almeno una fase usa un tipo di parametri, memorizzo la terna in $list (purchè non l'abbia già messa prima).
 * Poi per tutte le terne (che saranno diverse) preparo i link
 */
	$q=safe_r_sql($query);

	echo '<br><table width="100%" cellspacing="0" cellpadding="1">';
	echo '<tr>';
	$list=array();
	while($r=safe_fetch($q)) {
		$elimFin=elimFinFromMatchArrowsNo($r->EvFinalFirstPhase,$r->EvMatchArrowsNo);

		$arr=array($r->EvElimEnds,$r->EvElimArrows,$r->EvElimSO);
		if ($elimFin[0] && !in_array($arr,$list))
		{
			$list[]=$arr;
		}

		$arr=array($r->EvFinEnds,$r->EvFinArrows,$r->EvFinSO);
		if ($elimFin[1] && !in_array($arr,$list))
		{
			$list[]=$arr;
		}
//		echo '<td><a href="'.$CFG->ROOT_DIR.'Final/Team/PDFScoreMatch.php?Blank=1&Rows=' . $r->EvElimEnds . '&Cols='.$r->EvElimArrows.'&SO='.$r->EvElimSO.'" class="Link" target="PrintOut">';
//		echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdf.gif" alt="' . get_text('Score1Page1Match') . '" border="0"><br>';
//		echo get_text('Score1Page1Match');
//		echo '<br/>'. ($r->EvMatchMode?get_text('MatchMode_1').':</b> ':'') . get_text('EventDetails', 'Tournament', array($r->EvElimEnds, $r->EvElimArrows, $r->EvElimSO)) ;
//		echo '</a></td>';
	}
	if (count($list)>0)
	{
		foreach ($list as $l)
		{
			echo '<td><a href="PDFScoreMatch.php?Blank=1&Rows=' . $l[0] . '&Cols='.$l[1].'&SO='.$l[2].'" class="Link" target="PrintOut">';
			echo '<img src="../../Common/Images/pdf.gif" alt="' . get_text('Score1Page1Match') . '" border="0"><br>';
			echo get_text('Score1Page1Match');
			echo '<br/>'. get_text('EventDetails', 'Tournament', array($l[0], $l[1], $l[2])) ;
			echo '</a></td>';
		}
	}
	echo '</tr>';
	echo '</table>';

	echo '</td>';
	echo '</tr>';
// Nomi Ferrari
	echo '<tr>' . "\n";
	echo '<th colspan="2" class="SubTitle">' . get_text('Teams') . '</th>';
	echo '</tr>' . "\n";
	echo '<tr>' . "\n";
	echo '<td colspan="2" class="Center"><br><a href="'.$CFG->ROOT_DIR.'Final/Team/PrnName.php" class="Link" target="PrintOut"><img src="'.$CFG->ROOT_DIR.'Common/Images/pdf.gif" alt="' . get_text('Teams') . '" border="0"><br>' . get_text('Teams') . '</a></td>';
	echo '</tr>' . "\n";
//Selezione evento per nomi ferrari
	echo '<tr>' . "\n";
	echo '<td align="Center" colspan="2"><br>';
	echo '<form id="PrnParametersNames" action="'.$CFG->ROOT_DIR.'Final/Team/PrnName.php" method="get" target="PrintOut">';
	echo '<table class="Tabella" style="width:60%">';
	echo '<tr>';
	//Eventi
	echo '<td class="Center" width="25%">';
	$MySql = "SELECT EvCode, EvEventName FROM Events WHERE EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvFinalFirstPhase!=0 ORDER BY EvProgr";
	$Rs = safe_r_sql($MySql);
	if(safe_num_rows($Rs)>0)
	{
		echo get_text('Event') . '<br><select name="Event[]" multiple="multiple" id="p_Event" onChange="javascript:ChangeEvent(1,\'p\',null,true);" size="8">';
		while($MyRow=safe_fetch($Rs))
			echo '<option value="' . $MyRow->EvCode . '">' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true)  . '</option>';
		echo '</select>';
		safe_free_result($Rs);
	}
	echo '</td><td width="25%" class="Center">';
	echo get_text('Phase') . '<br><select name="Phase" id="p_Phase" size="6">';
	echo '<option value="">' . get_text('AllEvents')  . '</option>';
	echo '</select>';
	echo '</td>';
	echo '<td class="left" width="25%" >';
	echo '<input name="BigNames" type="checkbox" checked="checked" />' . get_text('BigNames','Tournament') ;
	echo '<br/><input name="IncludeLogo" type="checkbox" checked="checked" />' . get_text('IncludeLogo','BackNumbers') ;
	echo '<br/><input name="TargetAssign" type="checkbox" checked="checked" />' . get_text('TargetAssignment','Tournament') ;
	echo '<br/><input name="ColouredPhases" type="checkbox" />' . get_text('ColouredPhases','Tournament') ;
	echo '</td>';
	echo '<td class="Center" width="25%" >';
	echo '<input name="Submit" type="submit" value="' . get_text('Print','Tournament') . '">';
	echo '</td>';
	echo '</tr>';
	echo '</table>';
	echo '</form>';
	echo '</td>';
	echo '</tr>';
	echo '</table>';

	include('Common/Templates/tail.php');
?>