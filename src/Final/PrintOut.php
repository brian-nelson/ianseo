<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');
    checkACL(array(AclIndividuals, AclTeams), AclReadOnly);


	$JS_SCRIPT=array(
		'<script src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
		'<script src="PrintOut.js"></script>',
		);

	$PAGE_TITLE=get_text('PrintList','Tournament');

	include('Common/Templates/head.php');

	echo '<table class="Tabella">';
	echo '<tr><th class="Title" colspan="4">' . get_text('PrintList','Tournament')  . '</th></tr>';
	echo '<tr><th class="SubTitle" width="30%">' . get_text('BracketsInd')  . '</th>';
	echo '<th class="SubTitle" width="40%" colspan="2">' . get_text('CompleteResultBook')  . '</th>';
	echo '<th class="SubTitle" width="30%">' . get_text('BracketsSq')  . '</th></tr>';
//Griglie
	echo '<tr>';
//Individuale
	echo '<td class="Center"><br><a href="Individual/PrnBracket.php?ShowTargetNo=1&amp;ShowSchedule=1" class="Link" target="PrintOut">';
	echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdf_small.gif" alt="' . get_text('BracketsInd') . '" border="0"></a>&nbsp;&nbsp;&nbsp;';
	echo '<a href="Individual/OrisBracket.php?ShowTargetNo=1&amp;ShowSchedule=1" class="Link" target="OrisPrintOut">';
	echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdfOris_small.gif" title="' . get_text('StdORIS','Tournament') . '"  alt="' . get_text('StdORIS','Tournament') . '" border="0"></a><br>';
	echo '<a href="Individual/'.($_SESSION['ISORIS'] ? 'OrisBracket.php' : 'PrnBracket.php').'?ShowTargetNo=1&amp;ShowSchedule=1" class="Link" target="OrisPrintOut">' . get_text('BracketsInd') . '</a>';
	echo '</td>';
//Medagliere
	echo '<td class="Center"  colspan="2">';
	echo '<a href="'.$CFG->ROOT_DIR.'Final/OrisCompleteBook.php" class="Link" target="OrisPrintOut">';
	echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdfOris.gif" alt="' . get_text('CompleteResultBook') . '" border="0"></a><br>';
	echo '<a href="'.$CFG->ROOT_DIR.'Final/OrisCompleteBook.php" class="Link" target="OrisPrintOut">' . get_text('CompleteResultBook') . '</a>';
	echo '</br><a href="'.$CFG->ROOT_DIR.'Final/OrisCompleteBookChoose.php" class="Link">' . get_text('CompleteResultBookChoose') . '</a>';
	echo '</td>';
//Squadre
	echo '<td class="Center"><br><a href="Team/PrnBracket.php?ShowTargetNo=1&amp;ShowSchedule=1" class="Link" target="PrintOut">';
	echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdf_small.gif" alt="' . get_text('BracketsSq') . '" border="0"></a>&nbsp;&nbsp;&nbsp;';
	echo '<a href="Team/OrisBracket.php?ShowTargetNo=1&amp;ShowSchedule=1" class="Link" target="OrisPrintOut">';
	echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdfOris_small.gif" title="' . get_text('StdORIS','Tournament') . '"  alt="' . get_text('StdORIS','Tournament') . '" border="0"></a><br>';
	echo '<a href="Team/'.($_SESSION['ISORIS'] ? 'OrisBracket.php' : 'PrnBracket.php').'?ShowTargetNo=1&amp;ShowSchedule=1" class="Link" target="OrisPrintOut">' . get_text('BracketsSq') . '</a>';
	echo '</a></td>';
	echo '</tr>';
//Ranking
	echo '<tr>';
//Individuale
	echo '<td class="Center"><br><a href="Individual/PrnRanking.php" class="Link" target="PrintOut">';
	echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdf_small.gif" alt="' . get_text('RankingInd') . '" border="0"></a>&nbsp;&nbsp;&nbsp;';
	echo '<a href="Individual/OrisRanking.php" class="Link" target="OrisPrintOut">';
	echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdfOris_small.gif" title="' . get_text('StdORIS','Tournament') . '"  alt="' . get_text('StdORIS','Tournament') . '" border="0"></a><br>';
	echo '<a href="Individual/'.($_SESSION['ISORIS'] ? 'OrisRanking.php' : 'PrnRanking.php').'" class="Link" target="OrisPrintOut">' . get_text('RankingInd') . '</a>';
	echo '</td>';
//MEdaglie
	echo '<td class="Center">';
	echo '<a href="PDFMedalStanding.php" class="Link" target="PrintOut">';
	echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdf_small.gif" alt="Medal Standing" border="0"></a>&nbsp;&nbsp;&nbsp;';
	echo '<a href="OrisMedalStanding.php" class="Link" target="OrisPrintOut">';
	echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdfOris_small.gif" title="' . get_text('StdORIS','Tournament') . '"  alt="' . get_text('StdORIS','Tournament') . '" border="0"></a><br>';
	echo '<a href="'.($_SESSION['ISORIS'] ? 'OrisMedalStanding.php' : 'PDFMedalStanding.php').'" class="Link" target="OrisPrintOut">' . get_text('MedalStanding') . '</a>';
	echo '</td>';
	echo '<td class="Center">';
	echo '<a href="PDFMedalList.php" class="Link" target="PrintOut">';
	echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdf_small.gif" alt="Medal Standing" border="0"></a>&nbsp;&nbsp;&nbsp;';
	echo '<a href="'.$CFG->ROOT_DIR.'Final/OrisMedalList.php" class="Link" target="OrisPrintOut">';
	echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdfOris_small.gif" title="' . get_text('StdORIS','Tournament') . '"  alt="' . get_text('StdORIS','Tournament') . '" border="0"></a><br>';
	echo '<a href="'.$CFG->ROOT_DIR.'Final/'.($_SESSION['ISORIS'] ? 'OrisMedalList.php' : 'PDFMedalList.php').'" class="Link" target="OrisPrintOutvvv">' . get_text('MedalList') . '</a>';
	echo '</td>';
//Squadre
	echo '<td class="Center"><br><a href="'.$CFG->ROOT_DIR.'Final/Team/PrnRanking.php" class="Link" target="PrintOut">';
	echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdf_small.gif" alt="' . get_text('RankingSq') . '" border="0"></a>&nbsp;&nbsp;&nbsp;';
	echo '<a href="'.$CFG->ROOT_DIR.'Final/Team/OrisRanking.php" class="Link" target="OrisPrintOut">';
	echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdfOris_small.gif" title="' . get_text('StdORIS','Tournament') . '"  alt="' . get_text('StdORIS','Tournament') . '" border="0"></a><br>';
	echo '<a href="'.$CFG->ROOT_DIR.'Final/Team/'.($_SESSION['ISORIS'] ? 'OrisRanking.php' : 'PrnRanking.php').'" class="Link" target="OrisPrintOut">' . get_text('RankingSq') . '</a>';
	echo '</td>';
	echo '</tr>';

//Selezione dei singoli files
	echo '<tr class="Divider"><td  colspan="4"></td></tr>';
	echo '<tr>';
	echo '<td class="Center" colspan="2" width="50%">';
	$MySql = "SELECT EvCode, EvEventName FROM Events WHERE EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvFinalFirstPhase!=0 and EvCodeParent='' ORDER BY EvProgr";
	$Rs = safe_r_sql($MySql);
	if(safe_num_rows($Rs)>0)
	{
		echo '<table class="Tabella"><tr>';
		$TmpCnt=0;
		while($MyRow=safe_fetch($Rs))
		{
			if($TmpCnt++ % 2 == 0 && $TmpCnt != 1)
				echo '</tr><tr>';
			echo '<td class="Center"><a href="'.$CFG->ROOT_DIR.'Final/Individual/PrnIndividual.php?Event=' .  $MyRow->EvCode . '&amp;IncBrackets=1&amp;IncRankings=1&amp;ShowTargetNo=1&amp;ShowSchedule=1" class="Link" target="PrintOut">';
			echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdf_small.gif" alt="' . $MyRow->EvCode . '" border="0"></a>&nbsp;&nbsp;&nbsp;';
			echo '<a href="'.$CFG->ROOT_DIR.'Final/Individual/OrisIndividual.php?Event=' .  $MyRow->EvCode . '&amp;IncBrackets=1&amp;IncRankings=1&amp;ShowTargetNo=1&amp;ShowSchedule=1" class="Link" target="OrisPrintOut">';
			echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdfOris_small.gif" title="' . get_text('StdORIS','Tournament') . '"  alt="' . get_text('StdORIS','Tournament') . '" border="0"></a><br>';
			echo '<a href="'.$CFG->ROOT_DIR.'Final/Individual/'.($_SESSION['ISORIS'] ? 'OrisIndividual.php' : 'PrnIndividual.php').'?Event=' .  $MyRow->EvCode . '&amp;IncBrackets=1&amp;IncRankings=1&amp;ShowTargetNo=1&amp;ShowSchedule=1" class="Link" target="OrisPrintOut">' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true);
			echo '</a></td>';
		}
		if($TmpCnt % 2 != 0)
			echo '<td>&nbsp;</td>';
		echo '</tr></table>';
		safe_free_result($Rs);
	}
	echo '</td>';
	echo '<td class="Center" colspan="2" width="50%">';
	$MySql = "SELECT EvCode, EvEventName FROM Events WHERE EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvFinalFirstPhase!=0 and EvCodeParent='' ORDER BY EvProgr";
	$Rs = safe_r_sql($MySql);
	if(safe_num_rows($Rs)>0)
	{
		echo '<table class="Tabella"><tr>';
		$TmpCnt=0;
		while($MyRow=safe_fetch($Rs))
		{
			if($TmpCnt++ % 2 == 0 && $TmpCnt != 1)
				echo '</tr><tr>';
			echo '<td class="Center"><a href="'.$CFG->ROOT_DIR.'Final/Team/PrnTeam.php?Event=' .  $MyRow->EvCode . '&amp;IncBrackets=1&amp;IncRankings=1&amp;ShowTargetNo=1&amp;ShowSchedule=1" class="Link" target="PrintOut">';
			echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdf_small.gif" alt="' . $MyRow->EvCode . '" border="0">&nbsp;&nbsp;&nbsp;';
			echo '<a href="'.$CFG->ROOT_DIR.'Final/Team/OrisTeam.php?Event=' .  $MyRow->EvCode . '&amp;IncBrackets=1&amp;IncRankings=1&amp;ShowTargetNo=1&amp;ShowSchedule=1" class="Link" target="OrisPrintOut">';
			echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdfOris_small.gif" title="' . get_text('StdORIS','Tournament') . '"  alt="' . get_text('StdORIS','Tournament') . '" border="0"></a><br>';
			echo '<a href="'.$CFG->ROOT_DIR.'Final/Team/'.($_SESSION['ISORIS'] ? 'OrisTeam.php' : 'PrnTeam.php').'?Event=' .  $MyRow->EvCode . '&amp;IncBrackets=1&amp;IncRankings=1&amp;ShowTargetNo=1&amp;ShowSchedule=1" class="Link" target="OrisPrintOut">' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true);
			echo '</a></td>';
		}
		if($TmpCnt % 2 != 0)
			echo '<td>&nbsp;</td>';
		echo '</tr></table>';
		safe_free_result($Rs);
	}
	echo '</td>';
	echo '</tr>';
//Filtri per l' Individuale
	echo '<tr class="Divider"><td  colspan="4"></td></tr>';
	echo '<tr>';
	echo '<td class="Center" colspan="2" width="50%"><div align="center"><br><form id="PrnParametersInd" action="'.$CFG->ROOT_DIR.'Final/Individual/' . ($_SESSION['ISORIS'] ? 'Oris' : 'Prn') . 'Individual.php" method="get" target="PrintOutWorking">';
	echo '<table class="Tabella" style="width:80%">';
	echo '<tr>';
	//Eventi
	echo '<td class="Center" width="50%">';
	$MySql = "SELECT EvCode, EvEventName FROM Events WHERE EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvFinalFirstPhase!=0 and EvCodeParent='' ORDER BY EvProgr";
	$Rs = safe_r_sql($MySql);
	if(safe_num_rows($Rs)>0)
	{
		echo get_text('Event') . '<br><select id="IndividualEvents" name="Event[]" multiple="multiple" size="10">';
		echo '<option value=".">' . get_text('AllEvents')  . '</option>';
		while($MyRow=safe_fetch($Rs))
			echo '<option value="' . $MyRow->EvCode . '">' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true)  . '</option>';
		echo '</select>';
		safe_free_result($Rs);
	}
	echo '</td><td width="50%">';
	echo '<input name="IncRankings" type="checkbox" value="1" checked>&nbsp;' . get_text('Rankings') . '<br>';
	echo '<input name="IncBrackets" type="checkbox" value="1" checked>&nbsp;' . get_text('Brackets') . '<br>';
	echo '<input name="ShowTargetNo" type="checkbox" value="1" checked>&nbsp;' . get_text('Target') . '<br>';
	echo '<input name="ShowSchedule" type="checkbox" value="1" checked>&nbsp;' . get_text('ManFinScheduleInd') . '<br>';
	echo '<input name="ShowSetArrows" type="checkbox" value="1">&nbsp;' . get_text('ShowSetEnds', 'Tournament') . '<br>';
	echo '<input id="ShowOrisInd" name="ShowOrisInd" type="checkbox" value="1" onClick="javascript:CheckIfOris(\'ShowOrisInd\',\'PrnParametersInd\',true);"'.($_SESSION['ISORIS'] ? ' checked="checked"' : '').'>&nbsp;' . get_text('StdORIS','Tournament') . '<br>';
	echo '<br/><input name="ShowChildren" type="checkbox" onclick="updateEvents(this, 0)">&nbsp;' . get_text('ShowChildren', 'Tournament') . '<br>';
	//echo '<input id="PrintLabelsInd" name="PrintLabelsInd" type="checkbox" value="1" onClick="javascript:CheckIfLabel(\'PrintLabelsInd\',\'PrnParametersInd\',true);">&nbsp;' . get_text('FinalIndividualLabels','Tournament') . '<br>';
	echo '</td>';
	echo '</tr>';
	echo '</table>';
	echo '<br>&nbsp;<br><input type="submit" name="Button" value="' . get_text('BrakRank') . '"><br>&nbsp;';
	echo '</form></div><br></td>';
//Filtri per a Squadre
	echo '<td class="Center" colspan="2" width="50%"><div align="center"><br><form id="PrnParametersTeam" action="'.$CFG->ROOT_DIR.'Final/Team/' . ($_SESSION['ISORIS'] ? 'Oris' : 'Prn') . 'Team.php" method="get" target="PrintOutWorking">';
	echo '<table class="Tabella" style="width:80%">';
	echo '<tr>';
	//Eventi
	echo '<td class="Center" width="50%">';
	$MySql = "SELECT EvCode, EvEventName FROM Events WHERE EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvFinalFirstPhase!=0 and EvCodeParent='' ORDER BY EvProgr";
	$Rs = safe_r_sql($MySql);
	if(safe_num_rows($Rs)>0)
	{
		echo get_text('Event') . '<br><select id="TeamEvents" name="Event[]" multiple="multiple" size="10">';
		echo '<option value=".">' . get_text('AllEvents')  . '</option>';
		while($MyRow=safe_fetch($Rs))
			echo '<option value="' . $MyRow->EvCode . '">' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true)  . '</option>';
		echo '</select>';
		safe_free_result($Rs);
	}
	echo '</td><td width="50%">';
	echo '<input name="IncRankings" type="checkbox" value="1" checked>&nbsp;' . get_text('Rankings') . '<br>';
	echo '<input name="IncBrackets" type="checkbox" value="1" checked>&nbsp;' . get_text('Brackets') . '<br>';
	echo '<input name="ShowTargetNo" type="checkbox" value="1" checked>&nbsp;' . get_text('Target') . '<br>';
	echo '<input name="ShowSchedule" type="checkbox" value="1" checked>&nbsp;' . get_text('ManFinScheduleTeam') . '<br>';
	echo '<input name="ShowSetArrows" type="checkbox" value="1">&nbsp;' . get_text('ShowSetEnds', 'Tournament') . '<br>';
	echo '<input id="ShowOrisTeam" name="ShowOrisTeam" type="checkbox" value="1" onClick="javascript:CheckIfOris(\'ShowOrisTeam\',\'PrnParametersTeam\',false);"'.($_SESSION['ISORIS'] ? ' checked="checked"' : '').'>&nbsp;' . get_text('StdORIS','Tournament') . '<br>';
	echo '<br/><input name="ShowChildren" type="checkbox" onclick="updateEvents(this, 1)">&nbsp;' . get_text('ShowChildren', 'Tournament') . '<br>';
	// echo '<input id="PrintLabelsTeam" name="PrintLabelsTeam" type="checkbox" value="1" onClick="javascript:CheckIfLabel(\'PrintLabelsTeam\',\'PrnParametersTeam\',false);">&nbsp;' . get_text('FinalTeamLabels','Tournament') . '<br>';
	echo '</td>';
	echo '</tr>';
	echo '</table>';
	echo '<br>&nbsp;<br><input type="submit" name="Button" value="' . get_text('BrakRank') . '"><br>&nbsp;';
	echo '</form></div><br></td>';
	echo '</tr>';
	echo '</table>';

	include('Common/Templates/tail.php');
?>
