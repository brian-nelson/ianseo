<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	CheckTourSession(true);
	require_once('Common/Lib/CommonLib.php');
	require_once('Common/Globals.inc.php');
	require_once('Common/Fun_DB.inc.php');
	require_once('Common/Fun_FormatText.inc.php');



	$JS_SCRIPT=array(
		phpVars2js(array("WebDir" => $CFG->ROOT_DIR)),
		'<script type="text/javascript" src="../../../Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="../../../Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="../../Fun_AJAX.js.php"></script>',
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

	include('Common/Templates/head.php');

	echo '<table class="Tabella">';
	echo '<tr><th class="Title">' . get_text('PrintScore','Tournament')  . '</th></tr>';
	echo '<tr><th class="SubTitle">' . get_text('Score1Page1Match')  . '</th></tr>';
//Parametri
	echo '<tr>';
//Scores Per Match
	echo '<td class="Center"><div align="Center">';
	echo '<table class="Tabella" style="width:95%">';
	echo '<tr>';
	echo '<th class="SubTitle" colspan="3">' . get_text('GroupMatches','Tournament') .  ' - ' . get_text('FirstPhase','Tournament') . '</th>';
	echo '<th class="SubTitle" colspan="3">' . get_text('GroupMatches','Tournament') .  ' - ' . get_text('SecondPhase','Tournament') . '</th>';
	echo '</tr>';
	echo '<tr>';
	for($i=0; $i<=5; $i++)
	{
		//echo '<th class="SubTitle">' . ($i<=2 ? chr(65+$i) : $i-2) . '</th>';

		echo '<th class="SubTitle">Round ' . (($i%3)+1) . '</th>';
	}
	echo '</tr>';
	$MyQuery = 'SELECT '
        . ' EvCode '
        . ' FROM Events '
        . ' WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=1 '
        . ' ORDER BY EvProgr, EvCode';
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
			}
			for($i=0; $i<=5; $i++)
			{

				echo '<td class="Center"><a href="PDFScoreMatch.php?Event=' .  $MyRow->EvCode . '&amp;Phase=' . ($i<3 ? 1 : 2) . '&amp;Round=' . (1+($i%3)) . '" class="Link" target="PrintOut">';
				echo '<img src="../../Common/Images/pdf.gif" alt="' . $MyRow->EvCode . '" border="0"><br>';
				echo $MyRow->EvCode;
				echo '</a></td>';
			}
		}
		echo '</tr>';
		safe_free_result($Rs);
	}

	echo '</table>';
	echo '<br></div></td>';
	echo '</tr>';

//Score in bianco
	echo '<tr><th class="SubTitle" >' . get_text('ScoreDrawing')  . '</th></tr>';
	echo '<tr>';
//Scores Personali
	echo '<td width="50%" class="Center"><br><a href="PDFScoreMatch.php?Blank" class="Link" target="PrintOut">';
	echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdf.gif" alt="' . get_text('Score1Page1Match') . '" border="0"><br>';
	echo get_text('Score1Page1Match');
	echo '</a></td>';
	echo '</tr>';
/*
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
	echo '<td class="Center" width="40%">';
	$MySql = "SELECT EvCode, EvEventName FROM Events WHERE EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY EvProgr";
	$Rs = safe_r_sql($MySql);
	if(safe_num_rows($Rs)>0)
	{
		echo get_text('Event') . '<br><select name="Event" id="p_Event" onChange="javascript:ChangeEvent(1,\'p\');">';
		echo '<option value="">' . get_text('AllEvents')  . '</option>';
		while($MyRow=safe_fetch($Rs))
			echo '<option value="' . $MyRow->EvCode . '">' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true)  . '</option>';
		echo '</select>';
		safe_free_result($Rs);
	}
	echo '</td><td width="40%" class="Center">';
	echo get_text('Phase') . '<br><select name="Phase" id="p_Phase">';
	echo '<option value="">' . get_text('AllEvents')  . '</option>';
	echo '</select>';
	echo '</td>';
	echo '<td class="Center" width="20%" >';
	echo '<input name="Submit" type="submit" value="' . get_text('Print','Tournament') . '">';
	echo '</td>';
	echo '</tr>';
	echo '</table>';
	echo '</form>';
	echo '</td>';
	echo '</tr>';
*/
	echo '</table>';

	include('Common/Templates/tail.php');
?>