<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
    checkACL(AclQualification, AclReadOnly);
	require_once('Common/Fun_FormatText.inc.php');

	$PAGE_TITLE=get_text('PrintList', 'Tournament');

	$JS_SCRIPT=array(
		'<script type="text/javascript">
		function changevisibility(box, who, optdiv) {
			if(box.checked) {
				if(who !== undefined) document.getElementById(who).style.display=\'block\';
				if(optdiv !== undefined) document.getElementById(optdiv).style.display=\'none\';
			} else {
				if(who !== undefined) document.getElementById(who).style.display=\'none\';
				if(optdiv !== undefined) document.getElementById(optdiv).style.display=\'block\';
			}
		}
		</script>');

	include('Common/Templates/head.php');

	echo '<table class="Tabella">';
	echo '<tr><th class="Title" colspan="4">' . get_text('PrintList','Tournament')  . '</th></tr>';
	echo '<tr><th class="SubTitle" width="30%">' . get_text('ResultIndAbs','Tournament')  . '</th>';
	echo '<th class="SubTitle" width="40%" colspan="2">' . get_text('ResultAbs','Tournament')  . '</th>';
	echo '<th class="SubTitle" width="30%">' . get_text('ResultSqAbs','Tournament')  . '</th></tr>';
//Classifica
	echo '<tr>';
//Individuale
	echo '<td class="Center"><br><a href="PrnIndividualAbs.php" class="Link" target="PrintOut">';
	echo '<img src="../Common/Images/pdf_small.gif" alt="' . get_text('ResultIndAbs','Tournament') . '" border="0"></a>&nbsp;&nbsp;&nbsp;';
	echo '<a href="OrisIndividual.php" class="Link" target="ORISPrintOut">';
	echo '<img src="../Common/Images/pdfOris_small.gif" title="' . get_text('StdORIS','Tournament') . '"  alt="' . get_text('StdORIS','Tournament') . '" border="0"></a><br>';
	echo '<a href="'.($_SESSION['ISORIS'] ? 'OrisIndividual.php' : 'PrnIndividualAbs.php').'" class="Link" target="PrintOut">' . get_text('ResultIndAbs','Tournament') . '</a>';
	echo '</a></td>';
//Completa
	echo '<td class="Center"  colspan="2"><br><a href="PrnCompleteAbs.php" class="Link" target="PrintOut">';
	echo '<img src="../Common/Images/pdf.gif" alt="' . get_text('ResultAbs','Tournament') . '" border="0"><br>';
	echo get_text('ResultAbs','Tournament');
	echo '</a></td>';
//Squadre
	echo '<td class="Center"><br><a href="PrnTeamAbs.php" class="Link" target="PrintOut">';
	echo '<img src="../Common/Images/pdf_small.gif" alt="' . get_text('ResultSqAbs','Tournament') . '" border="0"></a>&nbsp;&nbsp;&nbsp;';
	echo '<a href="OrisTeam.php" class="Link" target="OrisPrintOut">';
	echo '<img src="../Common/Images/pdfOris_small.gif" title="' . get_text('StdORIS','Tournament') . '"  alt="' . get_text('StdORIS','Tournament') . '" border="0"></a><br>';
	echo '<a href="'.($_SESSION['ISORIS'] ? 'OrisTeam.php' : 'PrnTeamAbs.php').'" class="Link" target="PrintOut">' . get_text('ResultSqAbs','Tournament') . '</a>';
	echo '</a></td>';
	echo '</tr>';
//Selezione dei singoli files
	echo '<tr class="Divider"><td  colspan="4"></td></tr>';
//Filtri per l' Individuale
	echo '<tr class="Divider"><td  colspan="4"></td></tr>';
	echo '<tr>';
	echo '<td class="Center" colspan="2" width="50%"><div align="center"><br><form id="PrnParametersInd" action="PrintIndividual.php" method="get" target="PrintOut">';
	echo '<table class="Tabella" style="width:95%">';
	echo '<tr>';
	//Eventi
	echo '<td class="Center" width="50%">';
	$MySql = "SELECT EvCode, EvEventName FROM Events WHERE EvTeamEvent='0' and EvCodeParent='' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY EvProgr";
	$Rs = safe_r_sql($MySql);
	if(safe_num_rows($Rs)>0)
	{
		echo get_text('Event') . '<br><select name="Event[]" multiple="multiple" size="'.min(15,safe_num_rows($Rs)+1).'">';
		echo '<option value=".">' . get_text('AllEvents')  . '</option>';
		while($MyRow=safe_fetch($Rs))
			echo '<option value="' . $MyRow->EvCode . '">' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true)  . '</option>';
		echo '</select>';
		safe_free_result($Rs);
	}
	echo '</td><td class="Center" width="50%">';
	echo get_text('NumResult','Tournament') . '<br><input name="MaxNum" type="text" size="10" maxlength="3">';
	echo '<br/><br/><input id="ShowOrisInd" name="ShowOrisInd" type="checkbox" '.($_SESSION['ISORIS'] ? 'checked="checked" ' : '').'value="1">&nbsp;' . get_text('StdORIS','Tournament') . '<br>';

	echo '<br/><div id="SnapShot"><input name="Snapshot" type="checkbox" onclick="changevisibility(this, null, \'DistanceRankings\')">' . get_text('Snapshot','Tournament') . '</div>';
	// RankByDistance
	$t=safe_r_sql("select ToNumDist from Tournament where ToId='{$_SESSION['TourId']}'");
	$u=safe_fetch($t);
	echo '<br><div id="DistanceRankings">';
	if($u->ToNumDist>1) {
		echo '<div><input type="checkbox" name="distEnable" onclick="changevisibility(this,\'AfterDistance\',\'SnapShot\')">' . get_text('RankByDistance','Tournament') . '</div>';
		echo '<div id="AfterDistance" style="display:none">';
		echo '<div>' . get_text('AfterDistance','Tournament') . ' <select name="runningDist">'
			. '<option value="">' . get_text('Last','Tournament') . '</option>'
			;
			for($n=1; $n<=$u->ToNumDist; $n++) {
				echo '<option value="'.$n.'">' . $n . '</option>';
			}
			echo '</select></div>';
		echo '<div>' . get_text('AtDistance','Tournament') . ' <select name="atDist">'
			. '<option value="">--</option>'
			;
			for($n=1; $n<=$u->ToNumDist; $n++) {
				echo '<option value="'.$n.'">' . $n . '</option>';
			}
			echo '</select></div>';
		echo '</div>';
	}
	echo "</div>";
	echo '</td>';
	echo '</tr>';
	echo '</table>';
	echo '<br><input type="submit" name="Button" value="' . get_text('ResultIndAbs','Tournament') . '">';
	echo '</form></div><br></td>';
//Filtri per a Squadre
	echo '<td class="Center" colspan="2" width="50%"><div align="center"><br><form id="PrnParametersTeam" action="PrintTeam.php" method="get" target="PrintOut">';
	echo '<table class="Tabella" style="width:95%">';
	echo '<tr>';
	//Eventi
	echo '<td class="Center" width="50%">';
	$MySql = "SELECT EvCode, EvEventName FROM Events WHERE EvTeamEvent='1' and EvCodeParent='' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY EvProgr";
	$Rs = safe_r_sql($MySql);
	if(safe_num_rows($Rs)>0)
	{
		echo get_text('Event') . '<br><select name="Event[]" multiple="multiple" size="'.min(15,safe_num_rows($Rs)+1).'">';
		echo '<option value=".">' . get_text('AllEvents')  . '</option>';
		while($MyRow=safe_fetch($Rs))
			echo '<option value="' . $MyRow->EvCode . '">' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true)  . '</option>';
		echo '</select>';
		safe_free_result($Rs);
	}
	echo '</td><td class="Center" width="50%">';
	echo get_text('NumResult','Tournament') . '<br><input name="MaxNum" type="text" size="10" maxlength="3">';
	echo '<br/><br/><input id="ShowOrisTeam" name="ShowOrisTeam" type="checkbox" value="1" '.($_SESSION['ISORIS'] ? 'checked="checked" ' : '').'>&nbsp;' . get_text('StdORIS','Tournament') . '<br>';
	echo '</td>';
	echo '</tr>';
	echo '</table>';
	echo '<br><input type="submit" name="Button" value="' . get_text('ResultSqAbs','Tournament') . '">';
	echo '</form></div><br></td>';
	echo '</tr>';
//Classifica
	echo '<tr>';
	echo '<td class="Center" colspan="4"><br><a href="PrnShootoff.php" class="Link" target="PrintOut">';
	echo '<img src="../Common/Images/pdf.gif" alt="' . get_text('MenuLM_PrnShootOff') . '" border="0"><br>';
	echo get_text('MenuLM_PrnShootOff');
	echo '</a></td>';
	echo '</tr>';
	echo '</table>';

	include('Common/Templates/tail.php');
?>