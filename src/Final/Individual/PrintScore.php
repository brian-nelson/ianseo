<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	CheckTourSession(true);
    checkACL(AclIndividuals, AclReadOnly);
	require_once('Common/Globals.inc.php');
	require_once('Common/Fun_DB.inc.php');
	require_once('Common/Fun_Phases.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('HHT/Fun_HHT.local.inc.php');
	require_once('Common/Lib/CommonLib.php');

	// calcola la massima fase
	$MyQuery = "SELECT MAX(GrPhase) as MaxPhase FROM Grids left join Finals on GrMatchNo=FinMatchNo where FinTournament={$_SESSION['TourId']}";
	$Rs = safe_r_sql($MyQuery);
	$TmpCnt=32;
	if(safe_num_rows($Rs)>0) {
		$r=safe_fetch($Rs);
		$TmpCnt=$r->MaxPhase;
	}

	// calcola gli eventi esistenti
	$MyQuery = 'SELECT '
        . ' EvCode, EvEventName, GrPhase, MAX(IF(FinAthlete=0,0,1)) as Printable'
        . ' FROM Events '
        . ' INNER JOIN Phases ON PhId=EvFinalFirstPhase and (PhIndTeam & 1)=1 '
        . ' INNER JOIN Finals ON EvCode=FinEvent AND EvTournament=FinTournament '
        . ' INNER JOIN Grids ON FinMatchNo=GrMAtchNo AND if(EvElimType=3, true, GrPhase<=greatest(PhId, PhLevel)) '
        . ' WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=0 AND EvFinalFirstPhase!=0 '
        . ' GROUP BY EvCode, EvEventName, EvFinalFirstPhase, GrPhase'
        . ' ORDER BY EvCode, GrPhase DESC';
	$Rs = safe_r_sql($MyQuery);

	$Rows=array();
	$Events=array();
	$Printable=false;
	$OldCode='';
	while( $MyRow=safe_fetch($Rs) ) {
		if(empty($Rows[$MyRow->EvCode])) {
			if($OldCode and !$Printable) {
				unset($Rows[$OldCode]);
				unset($Events[$OldCode]);
			}
			$Printable = false;
			$OldCode = $MyRow->EvCode;
			$Rows[$MyRow->EvCode]='';
			$Events[$MyRow->EvCode] = $MyRow->EvEventName;
			for($i=$TmpCnt; $i>$MyRow->GrPhase; $i = floor($i/2)) $Rows[$MyRow->EvCode] .= '<td>&nbsp;</td>';
		}
		$Rows[$MyRow->EvCode] .=  '<td class="Center"><a href="'.$CFG->ROOT_DIR.'Final/Individual/PDFScoreMatch.php?Event=' .  $MyRow->EvCode . '&amp;Phase=' . $MyRow->GrPhase . '" class="Link" target="PrintOut">';
		$Rows[$MyRow->EvCode] .=  '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdf' . ($MyRow->Printable==1 ? '' : "_small") . '.gif" alt="' . $MyRow->EvCode . '" border="0"><br>';
		$Rows[$MyRow->EvCode] .=  $MyRow->EvCode;
		$Rows[$MyRow->EvCode] .=  '</a></td>';
		$Printable = ($Printable or $MyRow->Printable);
	}

	if($OldCode and !$Printable) {
		unset($Rows[$OldCode]);
		unset($Events[$OldCode]);
	}

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
		'',
		'}',
		'</script>',
		);

	include('Common/Templates/head.php');

	echo '<table class="Tabella">';

	echo '<tr><th class="Title" colspan="2">' . get_text('PrintScore','Tournament')  . '</th></tr>';

	echo '<tr>';
	echo '<th class="SubTitle" width="50%">' . get_text('Score1Page1Athlete')  . '</th>';
	echo '<th class="SubTitle" width="50%">' . get_text('Score1Page1Match')  . '</th>';
	echo '</tr>';

	echo '<tr valign="top">';
	echo '<td width="50%" class="Center"><div align="center">';

/**********************************
 * PRIMA LA COLONNA DI SINISTRA
 *********************************/
//INIZIO - Scores Personali su file unico
	echo '<br><a href="'.$CFG->ROOT_DIR.'Final/Individual/PDFScore.php" class="Link" target="PrintOut">';
	echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdf.gif" alt="' . get_text('IndFinal') . '" border="0"><br>';
	echo get_text('IndFinal');
	echo '</a>';

// TABELLONE DI SINISTRA CON GLI SCORE PER EVENTO
	echo '<br/><br/><table class="Tabella" style="width:80%">';
	$i=-1;
	echo '<tr>';
	foreach($Events as $Event => $EventName) {
		if($i>0) echo '</tr><tr>';
		echo '<td class="Center" width="50%"><a href="PDFScore.php?Event=' .  $Event . '" class="Link" target="PrintOut">';
		echo '<img src="../../Common/Images/pdf_small.gif" alt="' . $Event . '" border="0"><br>';
		echo $Event . ' - ' . get_text($EventName,'','',true);
		echo '</a></td>';
		$i = 1-abs($i);
	}
	if(!$i) echo '<td>&nbsp;</td>';
	echo '</tr>';
	echo '</table>';

	// tabella di selezione per evento multiplo
	echo '<br/><br/><form id="PrnParameters" action="PDFScore.php" method="get" target="PrintOut">';
	echo '<table class="Tabella" style="width:80%">';
	echo '<tr>';
	//Eventi
	echo '<td class="Center" width="50%">';
	echo get_text('Event') . '<br><select name="Event[]" multiple="multiple" size="10">';
	// echo '<option value=".">' . get_text('AllEvents')  . '</option>';
	foreach($Events as $Event => $EventName) {
		echo '<option value="' . $Event . '">' . $Event . ' - ' . get_text($EventName,'','',true)  . '</option>';
	}
	echo '</select>';
	echo '</td><td width="50%" class="Left">';
	echo '<input name="IncEmpty" type="checkbox" value="1">&nbsp;' . get_text('ScoreIncEmpty') . '<br>';
	echo '<input name="ScoreFlags" type="checkbox" value="1" checked>&nbsp;' . get_text('ScoreFlags','Tournament') . '<br>';
	echo '</td>';
	echo '</tr>';
	echo '</table>';
	echo '<br/><br/><input name="Submit" type="submit" onclick="this.form.action=\'PDFScore.php\'" value="' . get_text('PrintScore','Tournament') . '">';
	echo '<br/><br/><input name="Submit" type="submit" onclick="this.form.action=\'PrnLabels.php\'" value="' . get_text('FinalIndividualLabels','Tournament') . '">';
	echo '<br/>&nbsp;</form>';


	echo '</div></td>';

	echo '<td width="50%" class="Center"><div align="Center">';
/**********************************
 *
 * ADESSO LA COLONNA DI DESTRA
 *
 *********************************/

	//INIZIO - Tabellona di destra, Scores Per Match
	$ColWidth=intval(100/round(log($TmpCnt, 2)+2));

	echo '<table class="Tabella" style="width:95%">';
	echo '<tr>';
	for($i=$TmpCnt; $i>0; $i=floor($i/2))
	{
		if($i==24)
			$i=32;
		elseif ($i==48)
			$i=64;
		echo '<th class="SubTitle" width="'.$ColWidth.'%">' . get_text($i . '_Phase') . ($i==32 ?  " - " . get_text('24_Phase') :($i==64 ?  " - " . get_text('48_Phase') :'')) . '</th>';
	}
	echo '<th class="SubTitle">' . get_text('0_Phase') . '</th>';
	echo '</tr>';

	echo '<tr>'.implode('</tr><tr>', $Rows).'</tr>';
	echo '</table>';



	echo '<br/><br/>';
	echo '<form id="PrnParametersMatch" action="PDFScoreMatch.php" method="get" target="PrintOut">';
	echo '<table class="Tabella" style="width:80%">';
	echo '<tr>';
	//Eventi
	echo '<td class="Center" width="70%">';
	echo get_text('Event') . '<br><select name="Event[]" id="d_Event" onChange="javascript:ChangeEvent(0);" multiple="multiple" size="10">';
	echo '<option value="">' . get_text('AllEvents')  . '</option>';
	foreach($Events as $Event => $EventName) {
		echo '<option value="' . $Event . '">' . $Event . ' - ' . get_text($EventName,'','',true)  . '</option>';
	}
	echo '</select>';
	echo '</td><td width="30%" class="Center">';
	echo get_text('Phase') . '<br><select name="Phase" id="d_Phase" size="6">';
	echo '<option value="">' . get_text('AllEvents')  . '</option>';
	echo '</select>';
	echo '</td>';
	echo '</tr>';
	echo '<tr><td colspan="2" class="Center">' . ComboSes(RowTour(), 'Individuals') . '</td></tr>';
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
	// recupera per questo torneo quanti formati ci sono...
	$query="SELECT EvCode, EvMatchMode, EvFinalFirstPhase, EvMatchArrowsNo, EvElimEnds, EvElimArrows, EvElimSO, EvFinEnds, EvFinArrows, EvFinSO
		FROM Events
		INNER JOIN Phases on PhId=EvFinalFirstPhase	and (PhIndTeam & 1)=1
		WHERE EvTournament = '{$_SESSION['TourId']}'
			AND EvTeamEvent =0
			AND EvFinalFirstPhase !=0
		GROUP BY
			EvMatchMode, EvFinalFirstPhase, (EvMatchArrowsNo & (POW(2,1+LOG(2,IF(EvFinalFirstPhase>0, 2*greatest(PhId, PhLevel), 1)))-1)), EvElimEnds, EvElimArrows, EvElimSO, EvFinEnds, EvFinArrows, EvFinSO
	";
	//print $query;
	$q=safe_r_sql($query);
	echo '<table width="100%" cellspacing="0" cellpadding="1">';
	echo '<tr>';
	while($r=safe_fetch($q)) {
		echo '<td><a href="'.$CFG->ROOT_DIR.'Final/Individual/PDFScore.php?Blank=1&Model='.$r->EvCode.'" class="Link" target="PrintOut">';
			echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdf.gif" alt="' . get_text('Score1Page1Athlete') . '" border="0"><br>';
			echo get_text('Score1Page1Athlete');
			$dif=($r->EvElimEnds!=$r->EvFinEnds or $r->EvElimArrows!=$r->EvFinArrows or $r->EvElimSO!=$r->EvFinSO);

			$txt='<b>'. ($r->EvMatchMode?'<br/>'.get_text('MatchMode_1').':</b> ':'');

			$tmp=array();
			list($hasElim,$hasFin)=eventHasScoreTypes($r->EvCode,0);
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
			echo '<br/>'. get_text('FirstPhase').': 1/'. namePhase($r->EvFinalFirstPhase,$r->EvFinalFirstPhase);
		echo '</a></td>';
	}
	echo '</tr>';
	echo '</table>';
	echo '</td>';
//Scores per singolo match
	echo '<td width="50%" class="Center">';
	// recupera per questo torneo quanti formati ci sono...
	$query="
		SELECT
			EvCode,EvFinalFirstPhase,EvMatchArrowsNo,
			EvElimEnds, EvElimArrows, EvElimSO,
			EvFinEnds, EvFinArrows, EvFinSO
		FROM
			Events
		WHERE
			EvTournament={$_SESSION['TourId']} AND EvTeamEvent=0
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
	echo '<th colspan="2" class="SubTitle">' . get_text('Partecipants') . '</th>';
	echo '</tr>' . "\n";
	echo '<tr>' . "\n";
	echo '<td colspan="2" class="Center"><br><a href="'.$CFG->ROOT_DIR.'Final/Individual/PrnName.php" class="Link" target="PrintOut"><img src="'.$CFG->ROOT_DIR.'Common/Images/pdf.gif" alt="' . get_text('Partecipants') . '" border="0"><br>' . get_text('Partecipants') . '</a></td>';
	echo '</tr>' . "\n";
//Selezione evento per nomi ferrari
	echo '<tr>' . "\n";
	echo '<td align="Center" colspan="2"><br>';
	echo '<form id="PrnParametersNames" action="'.$CFG->ROOT_DIR.'Final/Individual/PrnName.php" method="get" target="PrintOut">';
	echo '<table class="Tabella" style="width:60%">';
	echo '<tr>';

//Eventi
	echo '<td class="Center" width="25%">';
	echo get_text('Event') . '<br><select name="Event[]" multiple="multiple" id="p_Event" onChange="javascript:ChangeEvent(0,\'p\',null,true);" size="10">';
	foreach($Events as $Event => $EventName) {
		echo '<option value="' . $Event . '">' . $Event . ' - ' . get_text($EventName,'','',true)  . '</option>';
	}
	echo '</select>';
	echo '</td><td width="25%" class="Center">';
	echo get_text('Phase') . '<br><select name="Phase" id="p_Phase" size="8">';
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