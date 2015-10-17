<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Fun_FormatText.inc.php');

	$PAGE_TITLE=get_text('PrintList', 'Tournament');

	$JS_SCRIPT=array(
		'<script type="text/javascript">',
		'	function CheckIfOris(chkValue,FormName)',
		'	{',
	'			if(document.getElementById(chkValue).checked)',
	'				document.getElementById(FormName).action = \'OrisIndividual.php\';',
	'			else',
	'				document.getElementById(FormName).action = \'PrnIndividual.php\';',
		'',
		'	}',
		'</script>',
	);

	include('Common/Templates/head.php');

	echo '<form id="PrnParametersInd" action="' . ($_SESSION['ISORIS'] ? 'Oris' : 'Prn') . 'Individual.php" method="get" target="PrintOut">';
	echo '<table class="Tabella">';
	echo '<tr><th class="Title" colspan="4">' . get_text('PrintList','Tournament')  . '</th></tr>';
	print '<tr><th colspan="4">' . get_text('StartlistSession','Tournament') . '</th></tr>' . "\n";
	print '<tr>';
		print '<td class="Center" width="33%"><br>';
		print '<a class="Link" target="PrintOut" href="PrnSession.php"><img src="../Common/Images/pdf.gif" alt="' . get_text('Elimination') . '" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		print '<a class="Link" target="ORISPrintOut" href="OrisStartList.php"><img src="../Common/Images/pdfOris.gif" alt="' . get_text('Elimination') . '" border="0"></a>';
		print '<br/><a class="Link" target="PrintOut" href="'.($_SESSION['ISORIS'] ? 'OrisStartList.php' : 'PrnSession.php').'">' . get_text('StartlistSession','Tournament') . '</a>';
		print '</td>';
		print '<td class="Center" colspan="2" width="34%"><br>';
		print '<a class="Link" target="PrintOut" href="PrnSession.php?Elim=0"><img src="../Common/Images/pdf.gif" alt="' . get_text('Eliminations_1') . '" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		print '<a class="Link" target="ORISPrintOut" href="OrisStartList.php?Elim=0"><img src="../Common/Images/pdfOris.gif" alt="' . get_text('Eliminations_1') . '" border="0"></a>';
		print '<br/><a class="Link" target="PrintOut" href="'.($_SESSION['ISORIS'] ? 'OrisStartList.php' : 'PrnSession.php').'?Elim=0">' . get_text('Eliminations_1') . '</a>';
		print '</td>';
		print '<td class="Center" width="33%"><br>';
		print '<a class="Link" target="PrintOut" href="PrnSession.php?Elim=1"><img src="../Common/Images/pdf.gif" alt="' . get_text('Eliminations_2') . '" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		print '<a class="Link" target="ORISPrintOut" href="OrisStartList.php?Elim=1"><img src="../Common/Images/pdfOris.gif" alt="' . get_text('Eliminations_2') . '" border="0"></a>';
		print '<br/><a class="Link" target="PrintOut" href="'.($_SESSION['ISORIS'] ? 'OrisStartList.php' : 'PrnSession.php').'?Elim=1">' . get_text('Eliminations_2') . '</a>';
		print '</td>';
	print '</tr>' . "\n";
	echo '<tr><th class="SubTitle" colspan="4">' . get_text('Elimination')  . '</th></tr>' . "\n";
//Classifica
	echo '<tr>';
		print '<td class="Center" colspan="4"><br>';
		print '<a class="Link" target="PrintOut" href="PrnIndividual.php"><img src="../Common/Images/pdf.gif" alt="' . get_text('Elimination') . '" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		print '<a class="Link" target="ORISPrintOut" href="OrisIndividual.php"><img src="../Common/Images/pdfOris.gif" alt="' . get_text('Elimination') . '" border="0"></a>';
		print '<br/><a class="Link" target="PrintOut" href="'.($_SESSION['ISORIS'] ? 'OrisIndividual.php' : 'PrnIndividual.php').'">' . get_text('Elimination') . '</a>';
		print '</td>';
	echo '</tr>';
//Header dei Gironi Eliminatori
	echo '<tr>';
		print '<th colspan="2" width="50%">' . get_text('Eliminations_1') . '</th>';
		print '<th colspan="2" width="50%">' . get_text('Eliminations_2') . '</th>';
	echo '</tr>';
//1o girone eliminatorio
	echo '<tr>';
		print '<td colspan="2" width="50%" class="Center">';
		$MySql = "SELECT EvCode, EvEventName FROM Events WHERE EvTeamEvent='0' AND EvElim1!=0 AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY EvProgr";
		$Rs = safe_r_sql($MySql);
		if(safe_num_rows($Rs)>0)
		{
			echo get_text('Event') . '<br><select id="event1" name="Event[]" multiple="multiple" rows="'.(safe_num_rows($Rs)+1).'">';
			while($MyRow=safe_fetch($Rs))
				echo '<option value="' . $MyRow->EvCode . '@1">' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true)  . '</option>';
			echo '</select>';
			safe_free_result($Rs);
		}
		echo '<br><br><a class="Link" href="javascript:SelectAllOpt(\'event1\');">' . get_text('SelectAll') . '</a>';
		print '</td>';
//2o girone eliminatorio
		print '<td colspan="2" width="50%" class="Center">';
		$MySql = "SELECT EvCode, EvEventName FROM Events WHERE EvTeamEvent='0' AND EvElim2!=0 AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY EvProgr";
		$Rs = safe_r_sql($MySql);
		if(safe_num_rows($Rs)>0)
		{
			echo get_text('Event') . '<br><select id="event2" name="Event[]" multiple="multiple" rows="'.(safe_num_rows($Rs)+1).'">';
			while($MyRow=safe_fetch($Rs))
				echo '<option value="' . $MyRow->EvCode . '@2">' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true)  . '</option>';
			echo '</select>';
			safe_free_result($Rs);
		}
		echo '<br><br><a class="Link" href="javascript:SelectAllOpt(\'event2\');">' . get_text('SelectAll') . '</a>';
		print '</td>';
	echo '</tr>';
//Oris e Pulsante
	echo '<tr>';
		print '<td colspan="4" class="Center">';
		echo '<br/><br/><input id="ShowOrisIndividual" name="ShowOrisIndividual" type="checkbox" value="1" onClick="javascript:CheckIfOris(\'ShowOrisIndividual\',\'PrnParametersInd\');"'.($_SESSION['ISORIS'] ? ' checked="checked"' : '').'>&nbsp;' . get_text('StdORIS','Tournament') . '<br>';
		echo '<br><input type="submit" name="Button" value="' . get_text('Print', 'Tournament') . '"><br>&nbsp;';
		print '</td>';
	echo '</tr>';
	echo '</table>';
	echo '</form>';

	include('Common/Templates/tail.php');
?>